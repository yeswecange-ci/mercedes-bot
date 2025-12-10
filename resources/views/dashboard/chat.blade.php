@extends('layouts.app')

@section('title', 'Chat Agent - Mercedes-Benz Bot')
@section('page-title', 'Chat avec ' . ($conversation->display_name ?? $conversation->phone_number))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Chat Messages - 2/3 width -->
    <div class="lg:col-span-2">
        <div class="card p-0 flex flex-col" style="height: calc(100vh - 200px);">
            <!-- Conversation Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-blue-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-full flex items-center justify-center text-white font-semibold mr-3 bg-blue-600">
                            {{ strtoupper(substr($conversation->display_name ?? 'C', 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $conversation->display_name ?? 'Client' }}
                            </h3>
                            <p class="text-sm text-gray-600">{{ $conversation->phone_number }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($conversation->status === 'active' && !$conversation->agent_id)
                        <form method="POST" action="{{ route('dashboard.chat.take-over', $conversation->id) }}">
                            @csrf
                            <button type="submit" class="btn-primary py-2 px-4 text-sm">
                                <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Prendre en charge
                            </button>
                        </form>
                        @elseif($conversation->agent_id === auth()->id())
                        <span class="badge-success">Vous êtes en charge</span>
                        @elseif($conversation->agent_id)
                        <span class="badge-warning">Prise en charge par {{ $conversation->agent->name ?? 'un agent' }}</span>
                        @endif

                        @if($conversation->status === 'transferred' && $conversation->agent_id === auth()->id())
                        <form method="POST" action="{{ route('dashboard.chat.close', $conversation->id) }}">
                            @csrf
                            <button type="submit" class="btn-secondary py-2 px-4 text-sm">
                                Clôturer
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
                @forelse($conversation->events()->orderBy('created_at', 'asc')->get() as $event)
                    @if($event->event_type === 'message_received' && $event->user_input)
                        <!-- Client Message -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center text-white text-sm font-medium @if($conversation->is_client) bg-blue-500 @else bg-gray-400 @endif">
                                {{ strtoupper(substr($conversation->display_name ?? 'C', 0, 1)) }}
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-200 inline-block max-w-lg">
                                    <p class="text-sm text-gray-900">{{ $event->user_input }}</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $event->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if(($event->event_type === 'message_sent' || $event->event_type === 'agent_message') && $event->bot_message)
                        <!-- Bot/Agent Message -->
                        <div class="flex items-start justify-end">
                            <div class="mr-3 flex-1 text-right">
                                <div class="bg-primary-600 text-white rounded-lg px-4 py-2 shadow-sm inline-block max-w-lg">
                                    <p class="text-sm">{{ $event->bot_message }}</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $event->created_at->format('d/m/Y H:i') }}
                                    @if($event->event_type === 'agent_message')
                                        <span class="ml-1 text-primary-600 font-medium">(Agent)</span>
                                    @else
                                        <span class="ml-1">(Bot)</span>
                                    @endif
                                </p>
                            </div>
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm font-medium">
                                {{ $event->event_type === 'agent_message' ? 'A' : 'B' }}
                            </div>
                        </div>
                    @endif

                    @if($event->event_type === 'agent_takeover')
                        <!-- System Event: Agent Takeover -->
                        <div class="flex justify-center">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 text-xs text-blue-700">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $event->bot_message }} - {{ $event->created_at->format('H:i') }}
                            </div>
                        </div>
                    @endif

                    @if($event->event_type === 'agent_transfer')
                        <!-- System Event: Agent Transfer Request -->
                        <div class="flex justify-center">
                            <div class="bg-orange-50 border border-orange-200 rounded-lg px-4 py-2 text-xs text-orange-700">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Le client a demandé à parler à un agent - {{ $event->created_at->format('H:i') }}
                            </div>
                        </div>
                    @endif

                    @if($event->event_type === 'conversation_closed')
                        <!-- System Event: Conversation Closed -->
                        <div class="flex justify-center">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-xs text-gray-700">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $event->bot_message }} - {{ $event->created_at->format('H:i') }}
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Aucun message dans cette conversation</p>
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            @if($conversation->status === 'transferred' && $conversation->agent_id === auth()->id())
            <div class="px-6 py-4 border-t border-gray-200 bg-white">
                <form id="send-message-form" class="flex items-end space-x-3">
                    @csrf
                    <div class="flex-1">
                        <textarea
                            id="message-input"
                            name="message"
                            rows="2"
                            required
                            class="input-field resize-none"
                            placeholder="Tapez votre message..."
                            maxlength="1600"></textarea>
                    </div>
                    <button type="submit" class="btn-primary py-3 px-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
            </div>
            @else
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <p class="text-sm text-gray-600 text-center">
                    @if($conversation->status !== 'transferred')
                        Cette conversation est gérée par le bot automatique.
                    @elseif($conversation->agent_id !== auth()->id())
                        Cette conversation est prise en charge par un autre agent.
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Conversation Info - 1/3 width -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Client Info Card -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations client</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Nom</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->display_name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Téléphone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->phone_number }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->email ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Type</dt>
                    <dd class="mt-1">
                        @if($conversation->is_client)
                            <span class="badge bg-indigo-100 text-indigo-800">Client</span>
                        @else
                            <span class="badge bg-orange-100 text-orange-800">Non-client</span>
                        @endif
                    </dd>
                </div>
                @if($conversation->vin)
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">VIN</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $conversation->vin }}</dd>
                </div>
                @endif
                @if($conversation->carte_vip)
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Carte VIP</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->carte_vip }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Conversation Stats -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Statut</dt>
                    <dd class="mt-1">
                        @if($conversation->status === 'active')
                            <span class="badge-success">Active</span>
                        @elseif($conversation->status === 'transferred')
                            <span class="badge bg-purple-100 text-purple-800">En support agent</span>
                        @elseif($conversation->status === 'completed')
                            <span class="badge-info">Terminée</span>
                        @else
                            <span class="badge bg-gray-100 text-gray-800">{{ ucfirst($conversation->status) }}</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Début</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->started_at ? $conversation->started_at->format('d/m/Y H:i') : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Dernière activité</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->last_activity_at ? $conversation->last_activity_at->diffForHumans() : 'N/A' }}</dd>
                </div>
                @if($conversation->duration_seconds)
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Durée</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ gmdate('H:i:s', $conversation->duration_seconds) }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Menu Path -->
        @if($conversation->menu_path)
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Parcours</h3>
            <div class="flex flex-wrap gap-2">
                @foreach(json_decode($conversation->menu_path, true) ?? [] as $menu)
                    <span class="badge badge-info">{{ $menu }}</span>
                    @if(!$loop->last)
                        <svg class="h-4 w-4 text-gray-400 self-center" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-scroll to bottom of messages
function scrollToBottom() {
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

// Scroll on page load
document.addEventListener('DOMContentLoaded', scrollToBottom);

// Handle message form submission
document.getElementById('send-message-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const messageInput = document.getElementById('message-input');
    const message = messageInput.value.trim();

    if (!message) return;

    try {
        const response = await fetch('{{ route('dashboard.chat.send', $conversation->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ message })
        });

        const data = await response.json();

        if (data.success) {
            messageInput.value = '';
            // Reload page to show new message
            window.location.reload();
        } else {
            alert('Erreur lors de l\'envoi du message: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Erreur lors de l\'envoi du message');
    }
});

// Auto-refresh messages every 5 seconds
setInterval(() => {
    if (document.visibilityState === 'visible') {
        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newMessages = doc.getElementById('messages-container');
            if (newMessages) {
                const currentContainer = document.getElementById('messages-container');
                const wasAtBottom = currentContainer.scrollHeight - currentContainer.scrollTop === currentContainer.clientHeight;
                currentContainer.innerHTML = newMessages.innerHTML;
                if (wasAtBottom) {
                    scrollToBottom();
                }
            }
        });
    }
}, 5000);
</script>
@endpush
