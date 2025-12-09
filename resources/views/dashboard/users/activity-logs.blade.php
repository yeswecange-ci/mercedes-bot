@extends('layouts.app')

@section('title', 'Historique des Activités - Mercedes-Benz Bot')
@section('page-title', 'Historique des Activités')

@section('content')
<!-- Back Button -->
<div class="mb-6">
    <a href="{{ route('dashboard.users.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour à la gestion des utilisateurs
    </a>
</div>

<!-- Filters -->
<div class="card mb-6">
    <form method="GET" action="{{ route('dashboard.users.activity-logs') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Utilisateur</label>
            <select name="user_id" id="user_id" class="input-field">
                <option value="">Tous les utilisateurs</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="action" class="block text-sm font-medium text-gray-700 mb-2">Action</label>
            <select name="action" id="action" class="input-field">
                <option value="">Toutes les actions</option>
                @foreach($actions as $action)
                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $action)) }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                   class="input-field">
        </div>

        <div>
            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                   class="input-field">
        </div>

        <div class="md:col-span-4 flex justify-end">
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filtrer
            </button>
        </div>
    </form>
</div>

<!-- Activity Logs -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique des activités</h3>

    @if($logs->isEmpty())
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="mt-2 text-sm text-gray-500">Aucune activité enregistrée</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($logs as $log)
        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <!-- Action Badge -->
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            @if(str_contains($log->action, 'created')) bg-green-100 text-green-800
                            @elseif(str_contains($log->action, 'updated')) bg-blue-100 text-blue-800
                            @elseif(str_contains($log->action, 'deleted')) bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>

                        <!-- User -->
                        <span class="text-sm font-medium text-gray-900">
                            @if($log->user)
                                {{ $log->user->name }}
                            @else
                                <span class="text-gray-400">Utilisateur supprimé</span>
                            @endif
                        </span>

                        <!-- Date -->
                        <span class="text-xs text-gray-500">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </span>
                    </div>

                    <!-- Description -->
                    <p class="text-sm text-gray-600 mb-2">{{ $log->description }}</p>

                    <!-- Details -->
                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                        @if($log->ip_address)
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            {{ $log->ip_address }}
                        </span>
                        @endif

                        @if($log->model_type)
                        <span>
                            {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                        </span>
                        @endif
                    </div>

                    <!-- Changes -->
                    @if($log->changes && is_array($log->changes))
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <button type="button"
                                onclick="toggleChanges('changes-{{ $log->id }}')"
                                class="text-xs text-primary-600 hover:text-primary-800 font-medium">
                            Voir les modifications
                        </button>
                        <div id="changes-{{ $log->id }}" class="hidden mt-2 text-xs bg-gray-50 p-3 rounded">
                            <pre class="whitespace-pre-wrap">{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
    <div class="mt-6 pt-6 border-t border-gray-200">
        {{ $logs->links() }}
    </div>
    @endif
    @endif
</div>

@push('scripts')
<script>
function toggleChanges(id) {
    const element = document.getElementById(id);
    element.classList.toggle('hidden');
}
</script>
@endpush
@endsection
