@extends('layouts.app')

@section('title', 'Détail Conversation - Mercedes-Benz Bot')
@section('page-title', 'Détail de la conversation')

@section('content')
<!-- Back Button -->
<div class="mb-6">
    <a href="{{ route('dashboard.conversations') }}" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors duration-200">
        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour aux conversations
    </a>
</div>

    <!-- Conversation Header -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">
                    Conversation #{{ $conversation->id }}
                </h2>
                @if($conversation->status === 'active')
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Active
                    </span>
                @elseif($conversation->status === 'completed')
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        Terminée
                    </span>
                @elseif($conversation->status === 'transferred')
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                        Transférée
                    </span>
                @elseif($conversation->status === 'timeout')
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Timeout
                    </span>
                @else
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                        {{ ucfirst($conversation->status) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Conversation Info Grid -->
        <div class="px-6 py-5">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nom du client</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->display_name ?? 'N/A' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->phone_number }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->email ?? 'N/A' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Type de client</dt>
                    <dd class="mt-1 text-sm">
                        @if($conversation->is_client)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                Client Mercedes
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                Non-client
                            </span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Session ID</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono text-xs">{{ $conversation->session_id }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Menu actuel</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->current_menu ?? 'N/A' }}</dd>
                </div>

                @if($conversation->vin)
                <div>
                    <dt class="text-sm font-medium text-gray-500">VIN</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $conversation->vin }}</dd>
                </div>
                @endif

                @if($conversation->carte_vip)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Carte VIP</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->carte_vip }}</dd>
                </div>
                @endif

                <div>
                    <dt class="text-sm font-medium text-gray-500">Début de la conversation</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->started_at ? $conversation->started_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Dernière activité</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->last_activity_at ? $conversation->last_activity_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>
                </div>

                @if($conversation->ended_at)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Fin de la conversation</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->ended_at->format('d/m/Y H:i:s') }}</dd>
                </div>
                @endif

                @if($conversation->duration_seconds)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Durée totale</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ gmdate('H:i:s', $conversation->duration_seconds) }}</dd>
                </div>
                @endif

                @if($conversation->transferred_at)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Transférée le</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->transferred_at->format('d/m/Y H:i:s') }}</dd>
                </div>
                @endif

                @if($conversation->chatwoot_conversation_id)
                <div>
                    <dt class="text-sm font-medium text-gray-500">ID Chatwoot</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $conversation->chatwoot_conversation_id }}</dd>
                </div>
                @endif
            </dl>

            @if($conversation->menu_path)
            <div class="mt-6">
                <dt class="text-sm font-medium text-gray-500 mb-2">Parcours du client</dt>
                <dd class="flex flex-wrap gap-2">
                    @foreach(json_decode($conversation->menu_path, true) ?? [] as $menu)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $menu }}
                        </span>
                        @if(!$loop->last)
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        @endif
                    @endforeach
                </dd>
            </div>
            @endif
        </div>
    </div>

    <!-- Timeline of Events -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                Timeline des événements ({{ $conversation->events->count() }})
            </h3>
        </div>

        <div class="px-6 py-5">
            <div class="flow-root">
                <ul class="-mb-8">
                    @forelse($conversation->events as $event)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif

                            <div class="relative flex space-x-3">
                                <div>
                                    @if($event->event_type === 'menu_choice')
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </span>
                                    @elseif($event->event_type === 'free_input')
                                        <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </span>
                                    @elseif($event->event_type === 'agent_transfer')
                                        <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </span>
                                    @elseif($event->event_type === 'message_sent')
                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                        </span>
                                    @elseif($event->event_type === 'error' || $event->event_type === 'invalid_input')
                                        <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </span>
                                    @else
                                        <span class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                                        </p>

                                        @if($event->user_input)
                                        <p class="mt-1 text-sm text-gray-700 bg-gray-50 p-2 rounded">
                                            <span class="font-medium">Saisie utilisateur:</span> {{ $event->user_input }}
                                        </p>
                                        @endif

                                        @if($event->bot_message)
                                        <p class="mt-1 text-sm text-gray-600 bg-blue-50 p-2 rounded">
                                            <span class="font-medium">Message bot:</span> {{ $event->bot_message }}
                                        </p>
                                        @endif

                                        @if($event->widget_name)
                                        <p class="mt-1 text-xs text-gray-500">
                                            Widget: <span class="font-mono">{{ $event->widget_name }}</span>
                                        </p>
                                        @endif

                                        @if($event->metadata)
                                        <details class="mt-2">
                                            <summary class="text-xs text-gray-500 cursor-pointer hover:text-gray-700">Métadonnées</summary>
                                            <pre class="mt-1 text-xs bg-gray-100 p-2 rounded overflow-x-auto">{{ json_encode($event->metadata, JSON_PRETTY_PRINT) }}</pre>
                                        </details>
                                        @endif
                                    </div>

                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                        <time datetime="{{ $event->created_at }}">
                                            {{ $event->created_at->format('H:i:s') }}
                                        </time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="text-center text-gray-500 py-8">
                        Aucun événement enregistré pour cette conversation
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
