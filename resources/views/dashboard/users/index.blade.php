@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs - Mercedes-Benz Bot')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
<!-- Header with Actions -->
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center space-x-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au dashboard
        </a>
    </div>

    <div class="flex items-center space-x-3">
        <a href="{{ route('dashboard.users.activity-logs') }}" class="btn-secondary">
            <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Historique des activités
        </a>
        <a href="{{ route('dashboard.users.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvel utilisateur
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Total</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Super Admins</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['super_admins'] }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Admins</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['admins'] }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Superviseurs</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['supervisors'] }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Agents</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['agents'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-6">
    <form method="GET" action="{{ route('dashboard.users.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                   placeholder="Nom ou email..."
                   class="input-field">
        </div>
        <div class="min-w-[150px]">
            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Rôle</label>
            <select name="role" id="role" class="input-field">
                <option value="">Tous</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="supervisor" {{ request('role') === 'supervisor' ? 'selected' : '' }}>Superviseur</option>
                <option value="agent" {{ request('role') === 'agent' ? 'selected' : '' }}>Agent</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">
            <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filtrer
        </button>
    </form>
</div>

<!-- Users Table -->
<div class="card">
    <div class="overflow-x-auto -mx-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Utilisateur
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rôle
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date de création
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold mr-3 bg-blue-600">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->role === 'super_admin')
                            <span class="badge bg-purple-100 text-purple-800">Super Admin</span>
                        @elseif($user->role === 'admin')
                            <span class="badge bg-indigo-100 text-indigo-800">Admin</span>
                        @elseif($user->role === 'supervisor')
                            <span class="badge bg-green-100 text-green-800">Superviseur</span>
                        @else
                            <span class="badge bg-gray-100 text-gray-800">Agent</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $user->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('dashboard.users.edit', $user->id) }}"
                               class="text-primary-600 hover:text-primary-900 transition-colors duration-200">
                                Modifier
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('dashboard.users.destroy', $user->id) }}" method="POST"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                    Supprimer
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Aucun utilisateur trouvé</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
