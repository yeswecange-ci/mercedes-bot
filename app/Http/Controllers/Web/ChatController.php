<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationEvent;
use Illuminate\Http\Request;
use Twilio\Rest\Client as TwilioClient;

class ChatController extends Controller
{
    /**
     * Display the chat interface for a specific conversation
     */
    public function show($id)
    {
        $conversation = Conversation::with(['agent', 'events'])
            ->findOrFail($id);

        return view('dashboard.chat', compact('conversation'));
    }

    /**
     * Take over a conversation from the bot
     */
    public function takeOver(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        // Check if conversation is active and not already taken
        if ($conversation->status !== 'active') {
            return redirect()->back()->with('error', 'Cette conversation ne peut pas être prise en charge.');
        }

        if ($conversation->agent_id) {
            return redirect()->back()->with('error', 'Cette conversation est déjà prise en charge.');
        }

        // Update conversation to transferred status
        $conversation->update([
            'status' => 'transferred',
            'agent_id' => auth()->id(),
        ]);

        // Log the takeover event
        ConversationEvent::create([
            'conversation_id' => $conversation->id,
            'event_type' => 'agent_takeover',
            'bot_message' => 'Conversation prise en charge par ' . auth()->user()->name,
        ]);

        // Send notification to client via WhatsApp
        try {
            $twilio = new TwilioClient(
                config('services.twilio.account_sid'),
                config('services.twilio.auth_token')
            );

            $twilio->messages->create(
                'whatsapp:' . $conversation->phone_number,
                [
                    'from' => 'whatsapp:' . config('services.twilio.whatsapp_number'),
                    'body' => "Vous êtes maintenant en contact avec un agent Mercedes-Benz. Comment puis-je vous aider ?",
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Error sending takeover message: ' . $e->getMessage());
        }

        return redirect()->route('dashboard.chat.show', $conversation->id)
            ->with('success', 'Vous avez pris en charge cette conversation.');
    }

    /**
     * Send a message from agent to client
     */
    public function send(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1600',
        ]);

        $conversation = Conversation::findOrFail($id);

        // Check if agent is authorized to send messages
        if ($conversation->status !== 'transferred' || $conversation->agent_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'error' => 'Vous n\'êtes pas autorisé à envoyer des messages dans cette conversation.'
            ], 403);
        }

        $message = $request->input('message');

        // Send message via Twilio
        try {
            $twilio = new TwilioClient(
                config('services.twilio.account_sid'),
                config('services.twilio.auth_token')
            );

            $twilioMessage = $twilio->messages->create(
                'whatsapp:' . $conversation->phone_number,
                [
                    'from' => 'whatsapp:' . config('services.twilio.whatsapp_number'),
                    'body' => $message,
                ]
            );

            // Log the agent message
            ConversationEvent::create([
                'conversation_id' => $conversation->id,
                'event_type' => 'agent_message',
                'bot_message' => $message,
            ]);

            // Update last activity
            $conversation->update([
                'last_activity_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message_sid' => $twilioMessage->sid,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending agent message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'envoi du message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Close a transferred conversation
     */
    public function close(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        // Check if agent is authorized to close
        if ($conversation->status !== 'transferred' || $conversation->agent_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à clôturer cette conversation.');
        }

        // Update conversation status
        $conversation->update([
            'status' => 'completed',
            'ended_at' => now(),
            'duration_seconds' => $conversation->started_at ?
                now()->diffInSeconds($conversation->started_at) : null,
        ]);

        // Log the closure event
        ConversationEvent::create([
            'conversation_id' => $conversation->id,
            'event_type' => 'conversation_closed',
            'bot_message' => 'Conversation clôturée par ' . auth()->user()->name,
        ]);

        // Send closing message to client
        try {
            $twilio = new TwilioClient(
                config('services.twilio.account_sid'),
                config('services.twilio.auth_token')
            );

            $twilio->messages->create(
                'whatsapp:' . $conversation->phone_number,
                [
                    'from' => 'whatsapp:' . config('services.twilio.whatsapp_number'),
                    'body' => "Merci d'avoir contacté Mercedes-Benz. Votre conversation a été clôturée. N'hésitez pas à nous recontacter si besoin !",
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Error sending closing message: ' . $e->getMessage());
        }

        return redirect()->route('dashboard.conversations')
            ->with('success', 'Conversation clôturée avec succès.');
    }
}
