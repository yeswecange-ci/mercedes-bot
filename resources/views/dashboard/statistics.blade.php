@extends('layouts.app')

@section('title', 'Statistiques - Mercedes-Benz Bot')
@section('page-title', 'Statistiques Détaillées')

@section('content')
<!-- Subtitle -->
<div class="mb-6">
    <p class="text-sm text-gray-600">
        Analyse approfondie des performances du bot
    </p>
</div>

<!-- Date Range Filter -->
<div class="mb-6">
    <form method="GET" action="{{ route('dashboard.statistics') }}" class="card">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" class="input-field">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" class="input-field">
            </div>
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filtrer
            </button>
        </div>
    </form>
</div>

<!-- Summary Stats Cards - CONSISTENT with dashboard -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Conversations -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total Conversations</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_conversations']) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Active Conversations -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Actives</p>
                <p class="text-3xl font-bold text-green-600">{{ number_format($stats['active_conversations']) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Completed Conversations -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Terminées</p>
                <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['completed_conversations']) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Transferred Conversations -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Transférées</p>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['transferred_conversations']) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Clients -->
    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Clients Mercedes</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_clients']) }}</p>
            </div>
        </div>
    </div>

    <!-- Non-Clients -->
    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Non-Clients</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_non_clients']) }}</p>
            </div>
        </div>
    </div>

    <!-- Unique Clients -->
    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Clients Uniques</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['unique_clients']) }}</p>
            </div>
        </div>
    </div>

    <!-- New Clients -->
    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Nouveaux Clients</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['new_clients']) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <!-- Average Duration -->
    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Durée moyenne</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['avg_duration'] ? gmdate('i:s', $stats['avg_duration']) : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Total Events -->
    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Total Événements</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_events']) }}</p>
            </div>
        </div>
    </div>

    <!-- Total Messages -->
    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Messages Reçus</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_messages']) }}</p>
            </div>
        </div>
    </div>

    <!-- Menu Choices -->
    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Choix Menu</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_menu_choices']) }}</p>
            </div>
        </div>
    </div>

    <!-- Free Inputs -->
    <div class="card">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Saisies Libres</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_free_inputs']) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Menu Distribution -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Distribution des choix de menu</h3>
        <div class="relative h-64 mb-4">
            <canvas id="menuDistributionChart"></canvas>
        </div>
        <div class="grid grid-cols-2 gap-2">
            @php
                $menuLabels = [
                    'vehicules' => ['label' => 'Véhicules neufs', 'color' => 'bg-blue-500'],
                    'sav' => ['label' => 'SAV', 'color' => 'bg-green-500'],
                    'reclamation' => ['label' => 'Réclamations', 'color' => 'bg-red-500'],
                    'vip' => ['label' => 'Club VIP', 'color' => 'bg-purple-500'],
                    'agent' => ['label' => 'Agent', 'color' => 'bg-yellow-500']
                ];
            @endphp
            @foreach($menuLabels as $key => $data)
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center">
                    <span class="h-3 w-3 rounded-full {{ $data['color'] }} mr-2"></span>
                    <span class="text-gray-600">{{ $data['label'] }}</span>
                </div>
                <span class="font-semibold text-gray-900">{{ $menuStats[$key] ?? 0 }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition par statut</h3>
        <div class="relative h-64 mb-4">
            <canvas id="statusChart"></canvas>
        </div>
        <div class="space-y-2">
            @php
                $statusLabels = [
                    'completed' => ['label' => 'Terminées', 'color' => 'bg-blue-500'],
                    'active' => ['label' => 'Actives', 'color' => 'bg-green-500'],
                    'transferred' => ['label' => 'Transférées', 'color' => 'bg-purple-500'],
                    'timeout' => ['label' => 'Timeout', 'color' => 'bg-yellow-500'],
                    'abandoned' => ['label' => 'Abandonnées', 'color' => 'bg-gray-500']
                ];
            @endphp
            @foreach($statusLabels as $key => $data)
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center">
                    <span class="h-3 w-3 rounded-full {{ $data['color'] }} mr-2"></span>
                    <span class="text-gray-600">{{ $data['label'] }}</span>
                </div>
                <span class="font-semibold text-gray-900">{{ $statusStats[$key] ?? 0 }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Daily Conversations Trend -->
    <div class="bg-white p-6 rounded-lg shadow lg:col-span-2">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Tendance quotidienne des conversations</h3>
        <div class="relative h-80">
            <canvas id="dailyTrendChart"></canvas>
        </div>
    </div>

    <!-- Client vs Non-Client Chart -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Clients vs Non-Clients</h3>
        <div class="relative h-64">
            <canvas id="clientChart"></canvas>
        </div>
    </div>

    <!-- Peak Hours -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Heures de pointe</h3>
        <div class="relative h-64">
            <canvas id="peakHoursChart"></canvas>
        </div>
    </div>
</div>

<!-- Popular Paths -->
<div class="bg-white p-6 rounded-lg shadow mb-8">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Parcours les plus populaires</h3>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse($popularPaths as $path)
        <div class="border-l-4 border-blue-500 pl-3 py-2 bg-gray-50 rounded-r hover:bg-gray-100 transition-colors">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap gap-1 mb-1">
                        @foreach(json_decode($path->menu_path, true) ?? [] as $menu)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $menu }}
                            </span>
                            @if(!$loop->last)
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        @endforeach
                    </div>
                </div>
                <span class="ml-3 flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $path->count }} fois
                </span>
            </div>
        </div>
        @empty
        <div class="col-span-2">
            <p class="text-sm text-gray-500 text-center py-8">Aucun parcours enregistré</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Detailed Event Statistics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Event Types Breakdown -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition des types d'événements</h3>
        <div class="space-y-3">
            @php
                $eventLabels = [
                    'message_received' => ['label' => 'Messages reçus', 'color' => 'bg-blue-500', 'icon' => '💬'],
                    'menu_choice' => ['label' => 'Choix de menu', 'color' => 'bg-green-500', 'icon' => '📋'],
                    'free_input' => ['label' => 'Saisies libres', 'color' => 'bg-purple-500', 'icon' => '✏️'],
                    'agent_transfer' => ['label' => 'Transferts agent', 'color' => 'bg-orange-500', 'icon' => '👤'],
                    'message_sent' => ['label' => 'Messages envoyés', 'color' => 'bg-cyan-500', 'icon' => '📤'],
                ];
                $totalEvents = $eventStats->sum('count');
            @endphp
            @forelse($eventStats as $event)
                @php
                    $eventType = $event->event_type;
                    $eventData = $eventLabels[$eventType] ?? ['label' => ucfirst($eventType), 'color' => 'bg-gray-500', 'icon' => '📌'];
                    $percentage = $totalEvents > 0 ? round(($event->count / $totalEvents) * 100, 1) : 0;
                @endphp
                <div class="relative">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center">
                            <span class="text-lg mr-2">{{ $eventData['icon'] }}</span>
                            <span class="text-sm font-medium text-gray-700">{{ $eventData['label'] }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($event->count) }}</span>
                            <span class="text-xs text-gray-500">({{ $percentage }}%)</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="{{ $eventData['color'] }} h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">Aucun événement enregistré</p>
            @endforelse
        </div>
    </div>

    <!-- Widget Usage Statistics -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Utilisation des widgets de collecte</h3>
        <div class="space-y-3">
            @php
                $widgetLabels = [
                    'collect_name' => ['label' => 'Collecte du nom', 'icon' => '👤', 'color' => 'bg-indigo-500'],
                    'collect_email' => ['label' => 'Collecte de l\'email', 'icon' => '📧', 'color' => 'bg-blue-500'],
                    'collect_vin' => ['label' => 'Collecte du VIN', 'icon' => '🚗', 'color' => 'bg-green-500'],
                    'collect_carte_vip' => ['label' => 'Collecte carte VIP', 'icon' => '💳', 'color' => 'bg-purple-500'],
                    'check_client' => ['label' => 'Vérification client', 'icon' => '✅', 'color' => 'bg-teal-500'],
                ];
                $totalWidgets = $widgetStats->sum('count');
            @endphp
            @forelse($widgetStats as $widget)
                @php
                    $widgetName = $widget->widget_name;
                    $widgetData = $widgetLabels[$widgetName] ?? ['label' => ucfirst($widgetName), 'icon' => '📝', 'color' => 'bg-gray-500'];
                    $percentage = $totalWidgets > 0 ? round(($widget->count / $totalWidgets) * 100, 1) : 0;
                @endphp
                <div class="relative">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center">
                            <span class="text-lg mr-2">{{ $widgetData['icon'] }}</span>
                            <span class="text-sm font-medium text-gray-700">{{ $widgetData['label'] }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($widget->count) }}</span>
                            <span class="text-xs text-gray-500">({{ $percentage }}%)</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="{{ $widgetData['color'] }} h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">Aucune collecte de données enregistrée</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Summary Table -->
<div class="bg-white p-6 rounded-lg shadow mb-8">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Résumé des statistiques clés</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Métrique
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Valeur
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Total des conversations
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-semibold">
                        {{ number_format($stats['total_conversations']) }}
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Clients uniques contactés
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-semibold">
                        {{ number_format($stats['unique_clients']) }}
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Nouveaux clients acquis
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-semibold">
                        {{ number_format($stats['new_clients']) }}
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Total des événements enregistrés
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-semibold">
                        {{ number_format($stats['total_events']) }}
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Messages WhatsApp reçus
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-semibold">
                        {{ number_format($stats['total_messages']) }}
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Interactions via menu
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-semibold">
                        {{ number_format($stats['total_menu_choices']) }}
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Saisies libres
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-semibold">
                        {{ number_format($stats['total_free_inputs']) }}
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Durée moyenne des conversations
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-semibold">
                        {{ $stats['avg_duration'] ? gmdate('i:s', $stats['avg_duration']) : 'N/A' }} min
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 bg-blue-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-900">
                        Taux de conversion (Clients / Unique)
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-blue-900 font-bold">
                        @php
                            $conversionRate = $stats['unique_clients'] > 0
                                ? round(($stats['total_clients'] / $stats['unique_clients']) * 100, 1)
                                : 0;
                        @endphp
                        {{ $conversionRate }}%
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('scripts')
<script>
// Menu Distribution Chart
const menuCtx = document.getElementById('menuDistributionChart').getContext('2d');
new Chart(menuCtx, {
    type: 'doughnut',
    data: {
        labels: ['Véhicules neufs', 'SAV', 'Réclamations', 'Club VIP', 'Agent'],
        datasets: [{
            data: [
                {{ $menuStats['vehicules'] ?? 0 }},
                {{ $menuStats['sav'] ?? 0 }},
                {{ $menuStats['reclamation'] ?? 0 }},
                {{ $menuStats['vip'] ?? 0 }},
                {{ $menuStats['agent'] ?? 0 }}
            ],
            backgroundColor: [
                'rgb(59, 130, 246)',
                'rgb(16, 185, 129)',
                'rgb(239, 68, 68)',
                'rgb(139, 92, 246)',
                'rgb(245, 158, 11)'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: ['Terminées', 'Actives', 'Transférées', 'Timeout', 'Abandonnées'],
        datasets: [{
            data: [
                {{ $statusStats['completed'] ?? 0 }},
                {{ $statusStats['active'] ?? 0 }},
                {{ $statusStats['transferred'] ?? 0 }},
                {{ $statusStats['timeout'] ?? 0 }},
                {{ $statusStats['abandoned'] ?? 0 }}
            ],
            backgroundColor: [
                'rgb(59, 130, 246)',
                'rgb(16, 185, 129)',
                'rgb(139, 92, 246)',
                'rgb(245, 158, 11)',
                'rgb(107, 114, 128)'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Daily Trend Chart
const dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailyStats->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))) !!},
        datasets: [
            {
                label: 'Conversations',
                data: {!! json_encode($dailyStats->pluck('total_conversations')) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            },
            {
                label: 'Transférées',
                data: {!! json_encode($dailyStats->pluck('transferred_conversations')) !!},
                borderColor: 'rgb(139, 92, 246)',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(139, 92, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 15
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Client vs Non-Client Chart
const clientCtx = document.getElementById('clientChart').getContext('2d');
new Chart(clientCtx, {
    type: 'doughnut',
    data: {
        labels: ['Clients Mercedes', 'Non-Clients'],
        datasets: [{
            data: [
                {{ $dailyStats->sum('clients_count') }},
                {{ $dailyStats->sum('non_clients_count') }}
            ],
            backgroundColor: [
                'rgb(99, 102, 241)',
                'rgb(251, 146, 60)'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// Peak Hours Chart
const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
new Chart(peakCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($peakHours->pluck('hour')->map(fn($h) => $h . 'h')) !!},
        datasets: [{
            label: 'Conversations',
            data: {!! json_encode($peakHours->pluck('count')) !!},
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>
@endpush
