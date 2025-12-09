@extends('layouts.app')

@section('title', 'Conversations Actives - Mercedes-Benz Bot')
@section('page-title', 'Conversations Actives')

@section('content')
<div x-data="{
    conversations: @js($activeConversations),
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

                // Animation subtile
                document.querySelectorAll('.conversation-card').forEach(card => {
                    card.classList.add('pulse-animation');
                    setTimeout(() => card.classList.remove('pulse-animation'), 500);
                });
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

<!-- Header Actions -->
<div class="mb-6 flex items-center justify-between bg-gradient-to-r from-green-50 to-blue-50 p-4 rounded-lg border border-green-200 shadow-sm">
    <div class="flex items-center space-x-4">
        <div class="flex items-center">
            <div class="relative">
                <span class="flex h-4 w-4">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-green-500"></span>
                </span>
            </div>
            <div class="ml-3">
                <p class="text-sm font-semibold text-gray-900">
                    <span x-text="conversations.length"></span> conversation(s) en cours
                </p>
                <p class="text-xs text-gray-500">
                    Mise à jour: <span x-text="formatTime(lastUpdate)"></span>
                </p>
            </div>
        </div>
    </div>
    <button @click="refreshConversations()" type="button"
            :disabled="loading"
            class="btn-secondary flex items-center space-x-2 transition-all hover:scale-105"
            :class="{ 'opacity-50 cursor-not-allowed': loading }">
        <svg class="h-4 w-4" :class="{ 'animate-spin': loading }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        <span>Rafraîchir</span>
    </button>
</div>

<!-- Empty State -->
<div x-show="conversations.length === 0"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     class="card text-center py-16">
    <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
        <svg class="w-full h-full" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune conversation active</h3>
    <p class="text-sm text-gray-500 mb-6">
        Il n'y a actuellement aucune conversation en cours.
    </p>
    <a href="{{ route('dashboard') }}" class="btn-primary inline-flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Retour au dashboard
    </a>
</div>

<!-- Active Conversations Grid -->
<div x-show="conversations.length > 0"
     class="grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-3">
    <template x-for="conversation in conversations" :key="conversation.id">
        <div class="conversation-card card p-0 overflow-hidden transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl cursor-pointer group"
             @click="window.location.href = `/dashboard/conversations/${conversation.id}`">

            <!-- Conversation Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 via-blue-50 to-purple-50 border-b border-gray-200 relative overflow-hidden">
                <!-- Animated background -->
                <div class="absolute inset-0 bg-gradient-to-r from-green-400/10 to-blue-400/10 transform translate-x-full group-hover:translate-x-0 transition-transform duration-500"></div>

                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div :class="conversation.is_client ? 'bg-gradient-to-br from-blue-500 to-blue-700' : 'bg-gradient-to-br from-gray-500 to-gray-700'"
                                 class="h-14 w-14 rounded-full flex items-center justify-center text-white font-bold shadow-lg text-lg ring-4 ring-white">
                                <span x-text="conversation.nom_prenom ? conversation.nom_prenom.charAt(0).toUpperCase() : conversation.phone_number.charAt(0)"></span>
                            </div>
                            <span class="absolute bottom-0 right-0 block h-4 w-4 rounded-full bg-green-500 ring-2 ring-white animate-pulse"></span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900" x-text="conversation.nom_prenom || 'Client'"></h3>
                            <p class="text-sm text-gray-600 font-medium" x-text="conversation.phone_number"></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-2.5 w-2.5 rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                        </span>
                        <span class="text-xs font-semibold text-green-700 uppercase tracking-wide">En ligne</span>
                    </div>
                </div>
            </div>

            <!-- Conversation Details -->
            <div class="px-6 py-4 space-y-4">
                <!-- Info Grid -->
                <dl class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Type</dt>
                        <dd>
                            <span :class="conversation.is_client ? 'bg-indigo-100 text-indigo-800' : 'bg-orange-100 text-orange-800'"
                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                <span x-text="conversation.is_client ? 'Client' : 'Non-client'"></span>
                            </span>
                        </dd>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Durée</dt>
                        <dd class="text-sm font-semibold text-gray-900" x-text="conversation.started || 'N/A'"></dd>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3 col-span-2" x-show="conversation.current_menu">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Menu actuel</dt>
                        <dd class="text-sm font-semibold text-gray-900 truncate" x-text="conversation.current_menu"></dd>
                    </div>
                </dl>

                <!-- Last Activity -->
                <div class="flex items-center space-x-2 text-xs text-gray-500 bg-blue-50 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">Dernière activité:</span>
                    <span x-text="conversation.last_activity"></span>
                </div>
            </div>

            <!-- Actions Footer -->
            <div class="px-6 py-3 bg-gradient-to-r from-gray-50 to-blue-50/50 border-t border-gray-200 flex justify-between items-center">
                <span class="text-xs text-gray-500 font-medium">
                    Cliquez pour voir les détails
                </span>
                <div class="flex space-x-2">
                    <a :href="`/dashboard/chat/${conversation.id}`"
                       @click.stop
                       class="inline-flex items-center px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-all transform hover:scale-105 shadow-sm hover:shadow-md">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Chat
                    </a>
                    <a :href="`/dashboard/conversations/${conversation.id}`"
                       @click.stop
                       class="inline-flex items-center px-3 py-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg border border-gray-300 transition-all">
                        Détails
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>

<!-- Info Footer -->
<div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg p-4 shadow-sm">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-semibold text-blue-900 mb-1">
                Mise à jour automatique
            </h4>
            <p class="text-sm text-blue-700">
                Les conversations actives sont rafraîchies automatiquement toutes les 8 secondes.
                Cliquez sur "Rafraîchir" pour une mise à jour manuelle immédiate.
            </p>
        </div>
    </div>
</div>
</div>

@push('styles')
<style>
.pulse-animation {
    animation: pulse-once 0.5s ease-out;
}

@keyframes pulse-once {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.02);
        opacity: 0.8;
    }
}

.conversation-card {
    position: relative;
}

.conversation-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 2px;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.conversation-card:hover::before {
    opacity: 0.5;
}
</style>
@endpush
@endsection
