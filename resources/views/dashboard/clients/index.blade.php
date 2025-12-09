@extends('layouts.app')

@section('title', 'Clients - Mercedes-Benz Bot')
@section('page-title', 'Gestion des Clients')

@section('content')
<!-- Subtitle -->
<div class="mb-6 flex items-center justify-between">
    <p class="text-sm text-gray-600">
        Gérez et suivez tous les utilisateurs qui interagissent avec le bot
    </p>
    <form method="GET" action="{{ route('dashboard.clients.sync') }}"
          onsubmit="return confirm('Voulez-vous synchroniser tous les clients depuis les conversations ?')"
          class="inline">
        <button type="submit" class="btn-primary">
            <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Synchroniser
        </button>
    </form>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Clients</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_clients']) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Clients Mercedes</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['mercedes_clients']) }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Interactions</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_interactions']) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="mb-6 bg-white p-6 rounded-lg shadow">
    <form method="GET" action="{{ route('dashboard.clients.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <!-- Search -->
            <div class="sm:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                    Rechercher un client
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="Nom, téléphone, email..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            <!-- Client Type -->
            <div>
                <label for="is_client" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="is_client" id="is_client"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Tous</option>
                    <option value="true" {{ request('is_client') === 'true' ? 'selected' : '' }}>Clients Mercedes</option>
                    <option value="false" {{ request('is_client') === 'false' ? 'selected' : '' }}>Non-clients</option>
                </select>
            </div>

            <!-- Search Button -->
            <div class="flex items-end">
                <button type="submit"
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrer
                </button>
            </div>
        </div>

        @if(request()->hasAny(['search', 'is_client', 'date_from', 'date_to']))
        <div class="flex justify-between items-center pt-2 border-t">
            <p class="text-sm text-gray-500">
                {{ $clients->total() }} résultat(s) trouvé(s)
            </p>
            <a href="{{ route('dashboard.clients.index') }}"
               class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Réinitialiser
            </a>
        </div>
        @endif
    </form>
</div>

<!-- Clients Table -->
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Liste des clients</h3>
    </div>

    @if($clients->isEmpty())
    <!-- Empty State -->
    <div class="px-6 py-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun client</h3>
        <p class="mt-1 text-sm text-gray-500">
            Aucun client ne correspond à vos critères de recherche.
        </p>
    </div>
    @else
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Client
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Contact
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Interactions
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Dernière activité
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($clients as $client)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold @if($client->is_client) bg-gradient-to-br from-blue-500 to-blue-700 @else bg-gradient-to-br from-gray-500 to-gray-700 @endif">
                                {{ strtoupper(substr($client->nom_prenom ?? $client->phone_number, 0, 1)) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $client->nom_prenom ?? 'Anonyme' }}
                                </div>
                                @if($client->vin)
                                <div class="text-xs text-gray-500">VIN: {{ $client->vin }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $client->phone_number }}</div>
                        @if($client->email)
                        <div class="text-xs text-gray-500">{{ $client->email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($client->is_client === true)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            Client Mercedes
                        </span>
                        @elseif($client->is_client === false)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            Non-client
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Non défini
                        </span>
                        @endif

                        @if($client->carte_vip)
                        <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            VIP
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ number_format($client->interaction_count) }} messages</div>
                        <div class="text-xs text-gray-500">{{ number_format($client->conversation_count) }} conversations</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($client->last_interaction_at)
                        {{ $client->last_interaction_at->format('d/m/Y H:i') }}
                        <div class="text-xs text-gray-400">{{ $client->last_interaction_at->diffForHumans() }}</div>
                        @else
                        N/A
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('dashboard.clients.show', $client->id) }}"
                           class="text-blue-600 hover:text-blue-900">
                            Détails
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($clients->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $clients->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
