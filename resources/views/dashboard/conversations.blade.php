@extends('layouts.app')

@section('title', 'Conversations - Mercedes-Benz Bot')
@section('page-title', 'Toutes les conversations')

@section('content')
<!-- Stats Summary -->
<div class="mb-6 flex items-center justify-between">
    <p class="text-sm text-gray-600">
        <span class="font-semibold text-gray-900">{{ $conversations->total() }}</span> conversations au total
    </p>
</div>

<!-- Filters -->
<div class="card mb-6">
    <form method="GET" action="{{ route('dashboard.conversations') }}" class="space-y-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            <!-- Search -->
            <div class="xl:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Nom, téléphone, email..."
                       class="input-field">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select name="status" id="status" class="input-field">
                    <option value="">Tous</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Terminée</option>
                    <option value="transferred" {{ request('status') === 'transferred' ? 'selected' : '' }}>Transférée</option>
                    <option value="timeout" {{ request('status') === 'timeout' ? 'selected' : '' }}>Timeout</option>
                    <option value="abandoned" {{ request('status') === 'abandoned' ? 'selected' : '' }}>Abandonnée</option>
                </select>
            </div>

            <!-- Client Type Filter -->
            <div>
                <label for="is_client" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select name="is_client" id="is_client" class="input-field">
                    <option value="">Tous</option>
                    <option value="1" {{ request('is_client') === '1' ? 'selected' : '' }}>Clients</option>
                    <option value="0" {{ request('is_client') === '0' ? 'selected' : '' }}>Non-clients</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="input-field">
            </div>

            <!-- Date To -->
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="input-field">
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('dashboard.conversations') }}" class="btn-secondary">
                Réinitialiser
            </a>
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filtrer
            </button>
        </div>
    </form>
</div>

<!-- Conversations Table -->
<div class="card">
    <div class="overflow-x-auto -mx-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Client
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Téléphone
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Statut
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Menu actuel
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Durée
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($conversations as $conversation)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                                {{ strtoupper(substr($conversation->nom_prenom ?? 'N', 0, 1)) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $conversation->nom_prenom ?? 'N/A' }}</div>
                                @if($conversation->email)
                                <div class="text-xs text-gray-500">{{ $conversation->email }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $conversation->phone_number }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($conversation->status === 'active')
                            <span class="badge-success">Active</span>
                        @elseif($conversation->status === 'completed')
                            <span class="badge-info">Terminée</span>
                        @elseif($conversation->status === 'transferred')
                            <span class="badge bg-purple-100 text-purple-800">Transférée</span>
                        @elseif($conversation->status === 'timeout')
                            <span class="badge-warning">Timeout</span>
                        @else
                            <span class="badge bg-gray-100 text-gray-800">{{ ucfirst($conversation->status) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($conversation->is_client)
                            <span class="badge bg-indigo-100 text-indigo-800">Client</span>
                        @else
                            <span class="badge bg-orange-100 text-orange-800">Non-client</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $conversation->current_menu ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        @if($conversation->duration_seconds)
                            {{ gmdate('i:s', $conversation->duration_seconds) }}
                        @else
                            <span class="text-green-600 font-medium">En cours</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $conversation->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('dashboard.show', $conversation->id) }}" class="text-primary-600 hover:text-primary-900 transition-colors duration-200">
                            Détails →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Aucune conversation trouvée</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($conversations->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $conversations->links() }}
    </div>
    @endif
</div>
@endsection
