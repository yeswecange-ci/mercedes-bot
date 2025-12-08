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

        <!-- Peak Hours -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Heures de pointe</h3>
            <div class="relative h-64">
                <canvas id="peakHoursChart"></canvas>
            </div>
        </div>

        <!-- Popular Paths -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Parcours les plus populaires</h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($popularPaths as $path)
                <div class="border-l-4 border-blue-500 pl-3 py-2 bg-gray-50 rounded-r">
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
                <p class="text-sm text-gray-500 text-center py-4">Aucun parcours enregistré</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Résumé de la période</h3>
        </div>
        <div class="px-6 py-5">
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-gray-50 px-4 py-5 sm:p-6 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total conversations</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ number_format($dailyStats->sum('total_conversations')) }}
                    </dd>
                </div>

                <div class="bg-gray-50 px-4 py-5 sm:p-6 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500 truncate">Utilisateurs uniques</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ number_format($dailyStats->sum('unique_users')) }}
                    </dd>
                </div>

                <div class="bg-gray-50 px-4 py-5 sm:p-6 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500 truncate">Taux de transfert</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        @php
                            $total = $dailyStats->sum('total_conversations');
                            $transferred = array_sum($statusStats ?? []) > 0 ? ($statusStats['transferred'] ?? 0) : 0;
                            $rate = $total > 0 ? round(($transferred / $total) * 100, 1) : 0;
                        @endphp
                        {{ $rate }}%
                    </dd>
                </div>

                <div class="bg-gray-50 px-4 py-5 sm:p-6 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500 truncate">Durée moyenne</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        @php
                            $avgDuration = $dailyStats->avg('avg_session_duration_seconds');
                        @endphp
                        {{ $avgDuration ? gmdate('i:s', $avgDuration) : 'N/A' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection

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
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
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
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
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
                tension: 0.3,
                fill: true
            },
            {
                label: 'Transférées',
                data: {!! json_encode($dailyStats->pluck('transferred_conversations')) !!},
                borderColor: 'rgb(139, 92, 246)',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.3,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
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
            backgroundColor: 'rgb(59, 130, 246)',
        }]
    },
    options: {
        responsive: true,
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
                }
            }
        }
    }
});
</script>
@endpush
