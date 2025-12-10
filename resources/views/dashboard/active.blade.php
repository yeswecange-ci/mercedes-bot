@extends('layouts.app')

@section('title', 'Conversations Actives - Mercedes-Benz Bot')
@section('page-title', 'Conversations Actives')

@section('content')
<div x-data="{
    conversations: @js($activeConversations->map(function($conv) {
        return [
            'id' => $conv->id,
            'display_name' => $conv->display_name,
            'phone_number' => $conv->phone_number,
            'is_client' => $conv->is_client,
            'current_menu' => $conv->current_menu,
            'started_at' => $conv->started_at ? $conv->started_at->diffForHumans() : 'N/A',
            'last_activity_at' => $conv->last_activity_at ? $conv->last_activity_at->format('d/m/Y H:i') : 'N/A',
        ];
    })),
    loading: false,
    lastUpdate: Date.now(),

    async refreshConversations() {
        try {
            const response = await fetch('/api/dashboard/active', {
                headers: {
                    'Authorization': 'Bearer ' + (localStorage.getItem('token') || ''),
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.conversations = data.conversations || [];
                this.lastUpdate = Date.now();
            }
        } catch (error) {
            console.error('Refresh error:', error);
        }
    },

    formatTime(timestamp) {
        return new Date(timestamp).toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}" x-init="setInterval(() => refreshConversations(), 8000)">

<!-- Header -->
<div class="mb-6 flex items-center justify-between card">
    <div class="flex items-center space-x-3">
        <div class="h-2 w-2 rounded-full bg-green-500"></div>
        <div>
            <p class="text-sm font-medium text-gray-900">
                <span x-text="conversations.length"></span> conversation(s) active(s)
            </p>
            <p class="text-xs text-gray-500">
                Mise à jour: <span x-text="formatTime(lastUpdate)"></span>
            </p>
        </div>
    </div>
    <button @click="refreshConversations()" type="button"
            :disabled="loading"
            class="btn-secondary"
            :class="{ 'opacity-50': loading }">
        <svg class="h-4 w-4 mr-2" :class="{ 'animate-spin': loading }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Rafraîchir
    </button>
</div>

<!-- Empty State -->
<div x-show="conversations.length === 0" class="card text-center py-12">
    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
    </svg>
    <h3 class="text-sm font-medium text-gray-900 mb-1">Aucune conversation active</h3>
    <p class="text-xs text-gray-500 mb-4">Il n'y a actuellement aucune conversation en cours.</p>
    <a href="{{ route('dashboard') }}" class="btn-primary">Retour au dashboard</a>
</div>

<!-- Active Conversations -->
<div x-show="conversations.length > 0" class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
    <template x-for="conversation in conversations" :key="conversation.id">
        <div class="card hover:shadow-md">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-full flex items-center justify-center text-white font-semibold text-sm bg-blue-600">
                        <span x-text="conversation.display_name.charAt(0).toUpperCase()"></span>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900" x-text="conversation.display_name"></h3>
                        <p class="text-xs text-gray-500" x-text="conversation.phone_number"></p>
                    </div>
                </div>
                <span class="h-2 w-2 rounded-full bg-green-500"></span>
            </div>

            <div class="space-y-2 mb-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Type:</span>
                    <span :class="conversation.is_client ? 'badge bg-indigo-100 text-indigo-800' : 'badge bg-orange-100 text-orange-800'"
                          x-text="conversation.is_client ? 'Client' : 'Non-client'"></span>
                </div>
                <div class="flex items-center justify-between text-xs" x-show="conversation.current_menu">
                    <span class="text-gray-500">Menu:</span>
                    <span class="text-gray-900 font-medium" x-text="conversation.current_menu"></span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Démarrée:</span>
                    <span class="text-gray-900" x-text="conversation.started_at"></span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Dernière activité:</span>
                    <span class="text-gray-900" x-text="conversation.last_activity_at"></span>
                </div>
            </div>

            <div class="flex space-x-2 pt-3 border-t border-gray-200">
                <a :href="`/dashboard/chat/${conversation.id}`" class="btn-primary flex-1 text-center text-xs py-1.5">
                    Chat
                </a>
                <a :href="`/dashboard/conversations/${conversation.id}`" class="btn-secondary flex-1 text-center text-xs py-1.5">
                    Détails
                </a>
            </div>
        </div>
    </template>
</div>

</div>
@endsection
