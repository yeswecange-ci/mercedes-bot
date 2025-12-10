@extends('layouts.app')

@section('title', 'Dashboard - Mercedes-Benz Bot')
@section('page-title', 'Dashboard')

@section('content')
<!-- Date Range Filter -->
<div class="mb-6">
    <form method="GET" action="{{ route('dashboard') }}" class="card">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}"
                       class="input-field">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}"
                       class="input-field">
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

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Conversations -->
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Total</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_conversations']) }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Active Conversations -->
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Actives</p>
                <p class="text-2xl font-semibold text-green-600">{{ number_format($stats['active_conversations']) }}</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Completed Conversations -->
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Terminées</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['completed_conversations']) }}</p>
            </div>
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Transferred -->
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Transférées</p>
                <p class="text-2xl font-semibold text-purple-600">{{ number_format($stats['transferred_conversations']) }}</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="card">
        <p class="text-xs text-gray-500 mb-1">Clients</p>
        <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_clients']) }}</p>
    </div>
    <div class="card">
        <p class="text-xs text-gray-500 mb-1">Non-Clients</p>
        <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_non_clients']) }}</p>
    </div>
    <div class="card">
        <p class="text-xs text-gray-500 mb-1">Clients Uniques</p>
        <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['unique_clients']) }}</p>
    </div>
    <div class="card">
        <p class="text-xs text-gray-500 mb-1">Nouveaux</p>
        <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['new_clients']) }}</p>
    </div>
</div>

<!-- Additional Stats -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="card">
        <p class="text-xs text-gray-500 mb-1">Durée moy.</p>
        <p class="text-lg font-semibold text-gray-900">{{ $stats['avg_duration'] ? gmdate('i:s', $stats['avg_duration']) : 'N/A' }}</p>
    </div>
    <div class="card">
        <p class="text-xs text-gray-500 mb-1">Événements</p>
        <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_events']) }}</p>
    </div>
    <div class="card">
        <p class="text-xs text-gray-500 mb-1">Messages</p>
        <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_messages']) }}</p>
    </div>
    <div class="card">
        <p class="text-xs text-gray-500 mb-1">Choix Menu</p>
        <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_menu_choices']) }}</p>
    </div>
    <div class="card">
        <p class="text-xs text-gray-500 mb-1">Saisies</p>
        <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_free_inputs']) }}</p>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <div class="card">
        <h3 class="text-sm font-medium text-gray-900 mb-3">Distribution des menus</h3>
        <div class="h-48">
            <canvas id="menuChart"></canvas>
        </div>
    </div>
    <div class="card">
        <h3 class="text-sm font-medium text-gray-900 mb-3">Tendance quotidienne</h3>
        <div class="h-48">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Conversations -->
<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Conversations récentes</h3>
        <a href="{{ route('dashboard.conversations') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors duration-200">
            Voir tout →
        </a>
    </div>
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
                        Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentConversations as $conversation)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold mr-3 @if($conversation->is_client) bg-gradient-to-br from-blue-500 to-blue-700 @else bg-gradient-to-br from-gray-500 to-gray-700 @endif">
                                {{ strtoupper(substr($conversation->display_name ?? 'N', 0, 1)) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $conversation->display_name ?? 'N/A' }}</div>
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
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Aucune conversation pour la période sélectionnée</p>
                    </td>
                </tr>
                @endforelse
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
const menuCtx = document.getElementById('menuChart').getContext('2d');
new Chart(menuCtx, {
    type: 'doughnut',
    data: {
        labels: ['Véhicules neufs', 'SAV', 'Réclamations', 'Club VIP', 'Agent'],
        datasets: [{
            data: [
                {{ $menuStats['vehicules'] }},
                {{ $menuStats['sav'] }},
                {{ $menuStats['reclamation'] }},
                {{ $menuStats['vip'] }},
                {{ $menuStats['agent'] }}
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
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// Daily Trend Chart
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailyStats->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))) !!},
        datasets: [{
            label: 'Conversations',
            data: {!! json_encode($dailyStats->pluck('total_conversations')) !!},
            borderColor: 'rgb(14, 165, 233)',
            backgroundColor: 'rgba(14, 165, 233, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: 'rgb(14, 165, 233)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
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
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});
</script>
@endpush
