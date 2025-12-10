<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;

class TwilioWebhookController extends Controller
{
    /**
     * Handle incoming WhatsApp messages from Twilio
     */
    public function handleIncomingMessage(Request $request)
    {
        try {
            // Validate incoming Twilio data
            $validated = $request->validate([
                'From' => 'required|string',
                'Body' => 'nullable|string|max:1600', // WhatsApp message limit
                'MessageSid' => 'required|string',
                'ProfileName' => 'nullable|string|max:255',
                'NumMedia' => 'nullable|integer|min:0',
                'MediaUrl0' => 'nullable|url',
                'MediaContentType0' => 'nullable|string',
            ]);

            // Log incoming request for debugging
            Log::info('Twilio Incoming Message', $validated);

            // Extract Twilio message data
            $from = $validated['From']; // Format: whatsapp:+212XXXXXXXXX
            $body = $validated['Body'] ?? '';
            $messageId = $validated['MessageSid'];
            $profileName = $validated['ProfileName'] ?? null;
            $numMedia = $validated['NumMedia'] ?? 0;

            // Clean phone number (remove whatsapp: prefix)
            $phoneNumber = str_replace('whatsapp:', '', $from);

            // Check if there's an existing active conversation (with 24h timeout)
            $conversation = Conversation::where('phone_number', $phoneNumber)
                ->whereIn('status', ['active', 'transferred'])
                ->where('last_activity_at', '>', now()->subHours(24))
                ->latest()
                ->first();

            // If no active or transferred conversation, create a new one
            if (!$conversation) {
                $conversation = Conversation::create([
                    'phone_number' => $phoneNumber,
                    'session_id' => uniqid('session_', true),
                    'whatsapp_profile_name' => $profileName ?? 'Client WhatsApp',
                    'started_at' => now(),
                    'last_activity_at' => now(),
                    'current_menu' => 'main_menu',
                    'status' => 'active',
                ]);
            } else {
                // Update last activity and profile name if changed
                $updates = ['last_activity_at' => now()];

                if ($profileName && $conversation->whatsapp_profile_name !== $profileName) {
                    $updates['whatsapp_profile_name'] = $profileName;
                }

                $conversation->update($updates);
            }

            // Synchronize with Client table
            $client = \App\Models\Client::findOrCreateByPhone($phoneNumber);

            // Check if client already exists (has interaction history)
            $clientExists = $client->wasRecentlyCreated === false && $client->client_full_name !== null;

            // Update client information - update WhatsApp profile name (always)
            if ($profileName) {
                $client->update(['whatsapp_profile_name' => $profileName]);
            }

            $client->incrementInteractions();

            // Prepare metadata for the event
            $metadata = [
                'message_sid' => $messageId,
                'profile_name' => $profileName,
            ];

            // Handle media attachments (images, videos, audio)
            if ($numMedia > 0) {
                $mediaItems = [];

                for ($i = 0; $i < min($numMedia, 10); $i++) {
                    $mediaUrl = $request->input("MediaUrl{$i}");
                    $mediaType = $request->input("MediaContentType{$i}");

                    if ($mediaUrl) {
                        $mediaItems[] = [
                            'url' => $mediaUrl,
                            'type' => $mediaType,
                        ];
                    }
                }

                $metadata['media'] = $mediaItems;
                $metadata['media_count'] = $numMedia;
            }

            // Store the incoming message as an event
            ConversationEvent::create([
                'conversation_id' => $conversation->id,
                'event_type' => 'message_received',
                'user_input' => $body,
                'metadata' => $metadata,
            ]);

            // Check if conversation is transferred to an agent
            $isAgentMode = $conversation->status === 'transferred' && $conversation->agent_id !== null;
            $isPendingAgent = $conversation->status === 'transferred' && $conversation->agent_id === null;

            // Return conversation data to Twilio Flow
            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'session_id' => $conversation->session_id,
                'phone_number' => $phoneNumber,
                'current_menu' => $conversation->current_menu,
                'is_client' => $client->is_client ?? $conversation->is_client,
                'client_full_name' => $client->client_full_name ?? $conversation->client_full_name,
                'whatsapp_profile_name' => $client->whatsapp_profile_name ?? $conversation->whatsapp_profile_name,
                'profile_name' => $profileName ?? $conversation->whatsapp_profile_name,
                'message' => $body,
                'status' => $conversation->status,
                'agent_mode' => $isAgentMode,
                'pending_agent' => $isPendingAgent,  // Nouveau: conversation en attente d'agent
                'has_media' => $numMedia > 0,
                'media_count' => $numMedia,
                'client_exists' => $clientExists,
                'client_has_name' => $client->client_full_name !== null,
                'client_status_known' => $client->is_client !== null,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Twilio Webhook Validation Error', [
                'errors' => $e->errors(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Invalid request data',
                'details' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Twilio Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle menu choice from Twilio Flow
     */
    public function handleMenuChoice(Request $request)
    {
        try {
            Log::info('Twilio Menu Choice', $request->all());

            $conversationId = $request->input('conversation_id');
            $menuChoice = $request->input('menu_choice');
            $userInput = $request->input('user_input');

            $conversation = Conversation::find($conversationId);

            if (!$conversation) {
                return response()->json(['success' => false, 'error' => 'Conversation not found'], 404);
            }

            // Update conversation menu
            $conversation->update([
                'current_menu' => $menuChoice,
                'last_activity_at' => now(),
            ]);

            // Add menu to path
            $menuPath = ($conversation->menu_path && is_string($conversation->menu_path))
                ? json_decode($conversation->menu_path, true)
                : [];
            $menuPath[] = $menuChoice;
            $conversation->update(['menu_path' => json_encode($menuPath)]);

            // Store event
            ConversationEvent::create([
                'conversation_id' => $conversation->id,
                'event_type' => 'menu_choice',
                'user_input' => $userInput,
                'metadata' => ['menu_choice' => $menuChoice],
            ]);

            return response()->json([
                'success' => true,
                'current_menu' => $menuChoice,
                'menu_path' => $menuPath,
            ]);

        } catch (\Exception $e) {
            Log::error('Menu Choice Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle free text input from user
     */
    public function handleFreeInput(Request $request)
    {
        try {
            Log::info('Twilio Free Input', $request->all());

            $conversationId = $request->input('conversation_id');
            $userInput = $request->input('user_input');
            $widgetName = $request->input('widget_name');

            $conversation = Conversation::find($conversationId);

            if (!$conversation) {
                return response()->json(['success' => false, 'error' => 'Conversation not found'], 404);
            }

            // Update last activity
            $conversation->update(['last_activity_at' => now()]);

            // Store free input event
            ConversationEvent::create([
                'conversation_id' => $conversation->id,
                'event_type' => 'free_input',
                'user_input' => $userInput,
                'widget_name' => $widgetName,
            ]);

            // Update conversation data based on widget
            $this->updateConversationData($conversation, $widgetName, $userInput);

            return response()->json([
                'success' => true,
                'stored' => true,
            ]);

        } catch (\Exception $e) {
            Log::error('Free Input Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle agent transfer request
     */
    public function handleAgentTransfer(Request $request)
    {
        try {
            Log::info('Twilio Agent Transfer', $request->all());

            $conversationId = $request->input('conversation_id');
            $conversation = Conversation::find($conversationId);

            if (!$conversation) {
                return response()->json(['success' => false, 'error' => 'Conversation not found'], 404);
            }

            // Update conversation status
            $conversation->update([
                'status' => 'transferred',
                'transferred_at' => now(),
                'last_activity_at' => now(),
            ]);

            // Store transfer event
            ConversationEvent::create([
                'conversation_id' => $conversation->id,
                'event_type' => 'agent_transfer',
                'metadata' => ['reason' => $request->input('reason')],
            ]);

            // TODO: Integrate with Chatwoot or your live chat system
            // $this->transferToChatwoot($conversation);

            return response()->json([
                'success' => true,
                'transferred' => true,
                'message' => 'Conversation transférée à un agent',
            ]);

        } catch (\Exception $e) {
            Log::error('Agent Transfer Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Complete a conversation
     */
    public function completeConversation(Request $request)
    {
        try {
            Log::info('Twilio Complete Conversation', $request->all());

            $conversationId = $request->input('conversation_id');
            $conversation = Conversation::find($conversationId);

            if (!$conversation) {
                return response()->json(['success' => false, 'error' => 'Conversation not found'], 404);
            }

            // Calculate duration
            $durationSeconds = $conversation->started_at ?
                $conversation->started_at->diffInSeconds(now()) : 0;

            // Update conversation
            $conversation->update([
                'status' => 'completed',
                'ended_at' => now(),
                'duration_seconds' => $durationSeconds,
                'last_activity_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'completed' => true,
                'duration_seconds' => $durationSeconds,
            ]);

        } catch (\Exception $e) {
            Log::error('Complete Conversation Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Send a message to a WhatsApp number
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'message' => 'required|string',
            'conversation_id' => 'nullable|exists:conversations,id',
        ]);

        try {
            $phoneNumber = $request->phone_number;
            $message = $request->message;

            // Send via Twilio
            $twilio = new \Twilio\Rest\Client(
                config('services.twilio.account_sid'),
                config('services.twilio.auth_token')
            );

            $twilioMessage = $twilio->messages->create(
                "whatsapp:$phoneNumber",
                [
                    'from' => 'whatsapp:' . config('services.twilio.whatsapp_number'),
                    'body' => $message,
                ]
            );

            // Log event if conversation exists
            if ($request->conversation_id) {
                ConversationEvent::create([
                    'conversation_id' => $request->conversation_id,
                    'event_type' => 'message_sent',
                    'bot_message' => $message,
                    'metadata' => ['message_sid' => $twilioMessage->sid],
                ]);
            }

            return response()->json([
                'success' => true,
                'message_sid' => $twilioMessage->sid,
            ]);

        } catch (\Exception $e) {
            Log::error('Send Message Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update conversation data based on widget input
     */
    private function updateConversationData($conversation, $widgetName, $userInput)
    {
        switch ($widgetName) {
            case 'collect_name':
                // Stocker le nom saisi manuellement dans client_full_name
                $conversation->update(['client_full_name' => $userInput]);

                // Synchroniser avec la table clients
                $client = \App\Models\Client::findOrCreateByPhone($conversation->phone_number);
                $client->update(['client_full_name' => $userInput]);
                break;

            case 'collect_email':
                $conversation->update(['email' => $userInput]);

                // Synchroniser avec la table clients
                $client = \App\Models\Client::findOrCreateByPhone($conversation->phone_number);
                if (!$client->email) {
                    $client->update(['email' => $userInput]);
                }
                break;

            case 'collect_vin':
                $conversation->update(['vin' => $userInput]);

                // Synchroniser avec la table clients
                $client = \App\Models\Client::findOrCreateByPhone($conversation->phone_number);
                if (!$client->vin) {
                    $client->update(['vin' => $userInput]);
                }
                break;

            case 'collect_carte_vip':
                $conversation->update(['carte_vip' => $userInput]);

                // Synchroniser avec la table clients
                $client = \App\Models\Client::findOrCreateByPhone($conversation->phone_number);
                if (!$client->carte_vip) {
                    $client->update(['carte_vip' => $userInput]);
                }
                break;

            case 'check_client':
                $isClient = in_array($userInput, ['1', 'oui', 'yes']);
                $conversation->update(['is_client' => $isClient]);

                // Synchroniser avec la table clients
                $client = \App\Models\Client::findOrCreateByPhone($conversation->phone_number);
                if ($client->is_client === null) {
                    $client->update(['is_client' => $isClient]);
                }
                break;
        }
    }
}
