@extends('layouts.app')

@section('title', 'Conversations en Attente - Mercedes-Benz Bot')
@section('page-title', 'Conversations en Attente d\'Agent')

@section('content')
<!-- Alert Header -->
<div class="mb-6">
    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-orange-500 rounded-lg p-4 shadow">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    @if($pendingCount > 0)
                        <svg class="h-6 w-6 text-orange-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    @else
                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                </div>
                <div class="ml-3">
                    @if($pendingCount > 0)
                        <p class="text-sm font-semibold text-orange-800">
                            <span class="text-2xl font-bold">{{ $pendingCount }}</span> conversation(s) en attente de prise en charge
                        </p>
                        <p class="text-xs text-orange-700 mt-1">
                            Des clients attendent de parler à un agent. Veuillez prendre en charge ces conversations rapidement.
                        </p>
                    @else
                        <p class="text-sm font-semibold text-green-800">
                            Aucune conversation en attente
                        </p>
                        <p class="text-xs text-green-700 mt-1">
                            Toutes les demandes d'agent ont été traitées.
                        </p>
                    @endif
                </div>
            </div>
            <button onclick="location.reload()" type="button" class="btn-secondary ml-4">
                <svg class="mr-2 h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Rafraîchir
            </button>
        </div>
    </div>
</div>

@if($pendingConversations->isEmpty())
<!-- Empty State -->
<div class="card text-center py-12">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune conversation en attente</h3>
    <p class="mt-1 text-sm text-gray-500">
        Toutes les demandes de transfert vers un agent ont été prises en charge.
    </p>
    <div class="mt-6">
        <a href="{{ route('dashboard.active') }}" class="btn-primary">
            Voir les conversations actives
        </a>
    </div>
</div>
@else
<!-- Pending Conversations Grid -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    @foreach($pendingConversations as $conversation)
    <div class="card hover:shadow-lg transition-shadow duration-200 p-0 overflow-hidden border-l-4 border-orange-500">
        <!-- Conversation Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-yellow-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full flex items-center justify-center text-white font-semibold shadow-lg @if($conversation->is_client) bg-gradient-to-br from-blue-500 to-blue-700 @else bg-gradient-to-br from-gray-500 to-gray-700 @endif">
                        {{ strtoupper(substr($conversation->display_name ?? $conversation->phone_number, 0, 1)) }}
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $conversation->display_name ?? 'Client' }}
                        </h3>
                        <p class="text-sm text-gray-600">{{ $conversation->phone_number }}</p>
                    </div>
                </div>
                <div class="flex flex-col items-end">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                        <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="5"/>
                        </svg>
                        En attente
                    </span>
                    @if($conversation->transferred_at)
                        <span class="text-xs text-gray-500 mt-1">
                            Depuis {{ $conversation->transferred_at->diffForHumans() }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Conversation Details -->
        <div class="px-6 py-4">
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Session ID</dt>
                    <dd class="mt-1 text-xs text-gray-900 font-mono truncate">{{ $conversation->session_id }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Type</dt>
                    <dd class="mt-1">
                        @if($conversation->is_client)
                            <span class="badge bg-indigo-100 text-indigo-800">Client</span>
                        @elseif($conversation->is_client === false)
                            <span class="badge bg-orange-100 text-orange-800">Non-client</span>
                        @else
                            <span class="badge bg-gray-100 text-gray-800">Inconnu</span>
                        @endif
                    </dd>
                </div>

                @if($conversation->email)
                <div class="col-span-2">
                    <dt class="text-xs font-medium text-gray-500 uppercase">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->email }}</dd>
                </div>
                @endif

                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Temps d'attente</dt>
                    <dd class="mt-1 text-sm font-semibold text-orange-600">
                        @if($conversation->transferred_at)
                            {{ $conversation->transferred_at->diffForHumans(null, true) }}
                        @else
                            {{ $conversation->last_activity_at->diffForHumans(null, true) }}
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Statut</dt>
                    <dd class="mt-1">
                        @if($conversation->status === 'transferred')
                            <span class="badge bg-purple-100 text-purple-800">Transférée</span>
                        @else
                            <span class="badge badge-info">{{ ucfirst($conversation->status) }}</span>
                        @endif
                    </dd>
                </div>

                @if($conversation->vin)
                <div class="col-span-2">
                    <dt class="text-xs font-medium text-gray-500 uppercase">VIN</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $conversation->vin }}</dd>
                </div>
                @endif

                @if($conversation->carte_vip)
                <div class="col-span-2">
                    <dt class="text-xs font-medium text-gray-500 uppercase">Carte VIP</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $conversation->carte_vip }}</dd>
                </div>
                @endif
            </dl>

            @if($conversation->menu_path)
            <div class="mt-4">
                <dt class="text-xs font-medium text-gray-500 uppercase mb-2">Parcours du client</dt>
                <dd class="flex flex-wrap gap-1">
                    @foreach(json_decode($conversation->menu_path, true) ?? [] as $menu)
                        <span class="badge badge-info">{{ $menu }}</span>
                        @if(!$loop->last)
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        @endif
                    @endforeach
                </dd>
            </div>
            @endif

            <!-- Recent Events -->
            @if($conversation->events->isNotEmpty())
            <div class="mt-4 border-t pt-4">
                <dt class="text-xs font-medium text-gray-500 uppercase mb-2">Dernières activités</dt>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @foreach($conversation->events->take(5) as $event)
                    <div class="flex items-start text-sm">
                        <span class="flex-shrink-0 h-5 w-5 text-gray-400 mr-2">
                            @if($event->event_type === 'agent_transfer')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="text-orange-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            @elseif($event->event_type === 'menu_choice')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            @elseif($event->event_type === 'free_input' || $event->event_type === 'message_received')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            @else
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-600 truncate">
                                @if($event->event_type === 'agent_transfer')
                                    <span class="font-semibold text-orange-600">Demande de transfert vers agent</span>
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                                @endif
                                @if($event->user_input)
                                    : <span class="font-medium">{{ Str::limit($event->user_input, 30) }}</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-400">{{ $event->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Actions Footer -->
        <div class="px-6 py-4 bg-orange-50 border-t border-orange-200">
            <div class="flex justify-between items-center">
                <div class="text-xs text-orange-700">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Client en attente depuis {{ $conversation->transferred_at ? $conversation->transferred_at->diffForHumans() : $conversation->last_activity_at->diffForHumans() }}
                </div>
                <div class="flex space-x-2">
                    <form action="{{ route('dashboard.chat.take-over', $conversation->id) }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="btn-primary py-2 px-4 text-sm shadow-lg hover:shadow-xl transition-all">
                            <svg class="w-4 h-4 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Prendre en charge maintenant
                        </button>
                    </form>
                    <a href="{{ route('dashboard.show', $conversation->id) }}" class="btn-secondary py-2 px-4 text-sm">
                        Détails
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Auto-refresh Info -->
<div class="mt-6 bg-orange-50 border border-orange-200 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-orange-700">
                <strong>Important:</strong> Cette page affiche les conversations en attente de prise en charge par un agent.
                Veuillez rafraîchir régulièrement pour voir les nouvelles demandes.
            </p>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// Auto-refresh every 10 seconds to check for new pending conversations
setInterval(function() {
    if (document.visibilityState === 'visible') {
        // Show a subtle notification before refresh
        const pendingCount = {{ $pendingCount }};
        if (pendingCount > 0) {
            console.log('Checking for new pending conversations...');
        }
        location.reload();
    }
}, 10000);
</script>
@endpush
