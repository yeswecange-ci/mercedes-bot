@extends('layouts.app')

@section('title', 'Détail Client - Mercedes-Benz Bot')
@section('page-title', 'Détail du Client')

@section('content')
<!-- Back Button and Actions -->
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('dashboard.clients.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour à la liste des clients
    </a>

    @if(auth()->user()->canEditClients())
    <a href="{{ route('dashboard.clients.edit', $client->id) }}" class="btn-primary">
        <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Modifier
    </a>
    @endif
</div>

<!-- Client Header -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="flex items-start justify-between">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-semibold shadow-lg @if($client->is_client) bg-gradient-to-br from-blue-500 to-blue-700 @else bg-gradient-to-br from-gray-500 to-gray-700 @endif">
                {{ strtoupper(substr($client->nom_prenom ?? $client->phone_number, 0, 1)) }}
            </div>
            <div class="ml-4">
                <h2 class="text-2xl font-bold text-gray-900">{{ $client->nom_prenom ?? 'Client Anonyme' }}</h2>
                <p class="text-sm text-gray-500">{{ $client->phone_number }}</p>
                @if($client->email)
                <p class="text-sm text-gray-500">{{ $client->email }}</p>
                @endif
            </div>
        </div>

        <div class="flex flex-col items-end space-y-2">
            @if($client->is_client === true)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                Client Mercedes
            </span>
            @elseif($client->is_client === false)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                Non-client
            </span>
            @endif

            @if($client->carte_vip)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                VIP: {{ $client->carte_vip }}
            </span>
            @endif

            @if($client->vin)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                VIN: {{ $client->vin }}
            </span>
            @endif
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Conversations</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($client->conversation_count) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Messages</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($interactionStats['total_messages']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Choix menus</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($interactionStats['menu_choices']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Dernière activité</p>
                <p class="text-sm font-bold text-gray-900">
                    @if($client->last_interaction_at)
                    {{ $client->last_interaction_at->diffForHumans() }}
                    @else
                    N/A
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Client Information -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations</h3>
        <dl class="space-y-3">
            <div>
                <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $client->phone_number }}</dd>
            </div>
            @if($client->email)
            <div>
                <dt class="text-sm font-medium text-gray-500">Email</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $client->email }}</dd>
            </div>
            @endif
            @if($client->vin)
            <div>
                <dt class="text-sm font-medium text-gray-500">VIN</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $client->vin }}</dd>
            </div>
            @endif
            @if($client->carte_vip)
            <div>
                <dt class="text-sm font-medium text-gray-500">Carte VIP</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $client->carte_vip }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-sm font-medium text-gray-500">Première interaction</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($client->first_interaction_at)
                    {{ $client->first_interaction_at->format('d/m/Y H:i') }}
                    @else
                    N/A
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Historique des conversations</h3>

        @if($conversations->isEmpty())
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500">Aucune conversation</p>
        </div>
        @else
        <div class="space-y-4">
            @foreach($conversations as $conversation)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            @if($conversation->status === 'completed') bg-blue-100 text-blue-800
                            @elseif($conversation->status === 'active') bg-green-100 text-green-800
                            @elseif($conversation->status === 'transferred') bg-purple-100 text-purple-800
                            @elseif($conversation->status === 'timeout') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($conversation->status) }}
                        </span>

                        @if($conversation->current_menu)
                        <span class="text-xs text-gray-500">{{ $conversation->current_menu }}</span>
                        @endif
                    </div>

                    <a href="{{ route('dashboard.show', $conversation->id) }}"
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Voir détails →
                    </a>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center space-x-4 text-gray-500">
                        <span>{{ $conversation->started_at->format('d/m/Y H:i') }}</span>
                        @if($conversation->duration_seconds)
                        <span>{{ gmdate('i:s', $conversation->duration_seconds) }}</span>
                        @endif
                        <span>{{ $conversation->events->count() }} événements</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($conversations->hasPages())
        <div class="mt-4">
            {{ $conversations->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
