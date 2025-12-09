@extends('layouts.app')

@section('title', 'Conversations Actives - Mercedes-Benz Bot')
@section('page-title', 'Conversations Actives')

@section('content')
<!-- Header Actions -->
<div class="mb-6 flex items-center justify-between">
    <p class="text-sm text-gray-600">
        <span class="font-semibold text-gray-900">{{ $activeConversations->count() }}</span> conversation(s) en cours
    </p>
    <button onclick="location.reload()" type="button" class="btn-secondary">
        <svg class="mr-2 h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Rafraîchir
    </button>
</div>

@if($activeConversations->isEmpty())
<!-- Empty State -->
<div class="card text-center py-12">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
    </svg>
    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune conversation active</h3>
    <p class="mt-1 text-sm text-gray-500">
        Il n'y a actuellement aucune conversation en cours.
    </p>
</div>
@else
<!-- Active Conversations Grid -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    @foreach($activeConversations as $conversation)
    <div class="card hover:shadow-lg transition-shadow duration-200 p-0 overflow-hidden">
        <!-- Conversation Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-blue-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full flex items-center justify-center text-white font-semibold shadow-lg @if($conversation->is_client) bg-gradient-to-br from-blue-500 to-blue-700 @else bg-gradient-to-br from-gray-500 to-gray-700 @endif">
                        {{ strtoupper(substr($conversation->nom_prenom ?? $conversation->phone_number, 0, 1)) }}
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $conversation->nom_prenom ?? 'Client' }}
                        </h3>
                        <p class="text-sm text-gray-600">{{ $conversation->phone_number }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="animate-pulse inline-flex h-3 w-3 rounded-full bg-green-500 mr-2"></span>
                    <span class="text-sm font-semibold text-green-700">En ligne</span>
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
                        @else
                            <span class="badge bg-orange-100 text-orange-800">Non-client</span>
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
                    <dt class="text-xs font-medium text-gray-500 uppercase">Menu actuel</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->current_menu ?? 'N/A' }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Durée</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($conversation->started_at)
                            {{ $conversation->started_at->diffForHumans(null, true) }}
                        @else
                            N/A
                        @endif
                    </dd>
                </div>

                @if($conversation->vin)
                <div class="col-span-2">
                    <dt class="text-xs font-medium text-gray-500 uppercase">VIN</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $conversation->vin }}</dd>
                </div>
                @endif
            </dl>

            @if($conversation->menu_path)
            <div class="mt-4">
                <dt class="text-xs font-medium text-gray-500 uppercase mb-2">Parcours</dt>
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
                            @if($event->event_type === 'menu_choice')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            @elseif($event->event_type === 'free_input')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            @else
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-600 truncate">
                                {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                                @if($event->user_input)
                                    : <span class="font-medium">{{ $event->user_input }}</span>
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
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div class="text-xs text-gray-500">
                    Dernière activité: {{ $conversation->last_activity_at ? $conversation->last_activity_at->diffForHumans() : 'N/A' }}
                </div>
                <div class="flex space-x-2">
                    @if($conversation->status === 'transferred')
                        <a href="{{ route('dashboard.chat.show', $conversation->id) }}" class="btn-primary py-1.5 px-3 text-xs">
                            <svg class="w-3 h-3 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Chat
                        </a>
                    @else
                        <a href="{{ route('dashboard.chat.show', $conversation->id) }}" class="btn-primary py-1.5 px-3 text-xs">
                            Prendre en charge →
                        </a>
                    @endif
                    <a href="{{ route('dashboard.show', $conversation->id) }}" class="btn-secondary py-1.5 px-3 text-xs">
                        Détails
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Auto-refresh Info -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-700">
                Les conversations actives sont affichées en temps réel. Cliquez sur "Rafraîchir" pour mettre à jour la liste.
            </p>
        </div>
    </div>
</div>
@endif
@endsection
