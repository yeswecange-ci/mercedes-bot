@extends('layouts.app')

@section('title', 'Recherche - Mercedes-Benz Bot')
@section('page-title', 'Recherche')

@section('content')
<!-- Subtitle -->
<div class="mb-6">
    <p class="text-sm text-gray-600">
        Recherchez et analysez les textes libres saisis par les utilisateurs
    </p>
</div>

    <!-- Search Form -->
    <div class="mb-6 bg-white p-6 rounded-lg shadow">
        <form method="GET" action="{{ route('dashboard.search') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <!-- Search Query -->
                <div class="sm:col-span-3">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                        Rechercher un mot-clé
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Rechercher dans les saisies utilisateurs..."
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700">Date de début</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700">Date de fin</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- Search Button -->
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Rechercher
                    </button>
                </div>
            </div>

            @if(request()->hasAny(['search', 'date_from', 'date_to']))
            <div class="flex justify-between items-center pt-2 border-t">
                <p class="text-sm text-gray-500">
                    {{ $freeInputs->total() }} résultat(s) trouvé(s)
                </p>
                <a href="{{ route('dashboard.search') }}"
                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Réinitialiser
                </a>
            </div>
            @endif
        </form>
    </div>

    @if(request()->filled('search') || request()->filled('date_from') || request()->filled('date_to'))
    <!-- Results -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Résultats de la recherche</h3>
        </div>

        @if($freeInputs->isEmpty())
        <!-- Empty State -->
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun résultat</h3>
            <p class="mt-1 text-sm text-gray-500">
                Aucune saisie utilisateur ne correspond à vos critères de recherche.
            </p>
        </div>
        @else
        <!-- Results List -->
        <div class="divide-y divide-gray-200">
            @foreach($freeInputs as $input)
            <div class="px-6 py-5 hover:bg-gray-50">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <!-- User Input -->
                        <div class="mb-3">
                            <div class="flex items-center mb-1">
                                <svg class="h-5 w-5 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span class="text-xs font-medium text-gray-500 uppercase">Saisie utilisateur</span>
                            </div>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 p-3 rounded-md border-l-4 border-purple-500">
                                {{ $input->user_input }}
                            </p>
                        </div>

                        <!-- Bot Response -->
                        @if($input->bot_message)
                        <div class="mb-3">
                            <div class="flex items-center mb-1">
                                <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                </svg>
                                <span class="text-xs font-medium text-gray-500 uppercase">Réponse du bot</span>
                            </div>
                            <p class="text-sm text-gray-700 bg-blue-50 p-3 rounded-md border-l-4 border-blue-500">
                                {{ $input->bot_message }}
                            </p>
                        </div>
                        @endif

                        <!-- Conversation Info -->
                        @if($input->conversation)
                        <div class="flex flex-wrap items-center gap-3 mt-3">
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $input->conversation->nom_prenom ?? 'N/A' }}
                            </div>

                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ $input->conversation->phone_number }}
                            </div>

                            @if($input->conversation->is_client !== null)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $input->conversation->is_client ? 'bg-indigo-100 text-indigo-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ $input->conversation->is_client ? 'Client' : 'Non-client' }}
                            </span>
                            @endif

                            <a href="{{ route('dashboard.show', $input->conversation->id) }}"
                               class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 font-medium">
                                Voir la conversation
                                <svg class="ml-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Timestamp -->
                    <div class="ml-4 flex-shrink-0 text-right">
                        <time class="text-xs text-gray-500" datetime="{{ $input->created_at }}">
                            {{ $input->created_at->format('d/m/Y') }}<br>
                            {{ $input->created_at->format('H:i:s') }}
                        </time>
                    </div>
                </div>

                @if($input->widget_name)
                <div class="mt-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                        Widget: {{ $input->widget_name }}
                    </span>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($freeInputs->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $freeInputs->links() }}
        </div>
        @endif
        @endif
    </div>
    @else
    <!-- Empty State - No Search Yet -->
    <div class="bg-white shadow rounded-lg p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Lancez une recherche</h3>
        <p class="mt-1 text-sm text-gray-500">
            Utilisez le formulaire ci-dessus pour rechercher dans les saisies texte des utilisateurs.
        </p>
        <div class="mt-6">
            <div class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-100">
                <svg class="mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Astuce: Vous pouvez rechercher par mot-clé, nom, ou filtrer par date
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
