<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationEvent;
use App\Models\DailyStatistic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Statistiques globales du dashboard
     * 
     * GET /api/dashboard/stats
     */
    public function stats(Request $request): JsonResponse
    {
        $period = $request->get('period', 'today'); // today, week, month, custom
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Définir la période
        switch ($period) {
            case 'today':
                $start = today();
                $end = today();
                break;
            case 'week':
                $start = now()->startOfWeek();
                $end = now()->endOfWeek();
                break;
            case 'month':
                $start = now()->startOfMonth();
                $end = now()->endOfMonth();
                break;
            case 'custom':
                $start = $startDate ? new \DateTime($startDate) : today()->subDays(30);
                $end = $endDate ? new \DateTime($endDate) : today();
                break;
            default:
                $start = today();
                $end = today();
        }

        // Stats agrégées
        $conversations = Conversation::whereBetween('started_at', [$start, $end]);
        
        $stats = [
            'period' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
            ],
            'overview' => [
                'total_conversations' => (clone $conversations)->count(),
                'unique_users' => (clone $conversations)->distinct('phone_number')->count(),
                'active_now' => Conversation::active()
                    ->where('last_activity_at', '>=', now()->subMinutes(30))
                    ->count(),
                'avg_duration_minutes' => round(
                    (clone $conversations)
                        ->whereNotNull('ended_at')
                        ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, started_at, ended_at)) as avg')
                        ->value('avg') ?? 0,
                    1
                ),
            ],
            'by_status' => [
                'completed' => (clone $conversations)->where('status', 'completed')->count(),
                'transferred' => (clone $conversations)->where('status', 'transferred')->count(),
                'timeout' => (clone $conversations)->where('status', 'timeout')->count(),
                'abandoned' => (clone $conversations)->where('status', 'abandoned')->count(),
                'active' => (clone $conversations)->where('status', 'active')->count(),
            ],
            'by_menu' => $this->getMenuStats($start, $end),
            'by_client_status' => [
                'clients' => (clone $conversations)->where('is_client', true)->count(),
                'non_clients' => (clone $conversations)->where('is_client', false)->count(),
                'unknown' => (clone $conversations)->whereNull('is_client')->count(),
            ],
        ];

        return response()->json($stats);
    }

    /**
     * Conversations récentes
     * 
     * GET /api/dashboard/conversations
     */
    public function conversations(Request $request): JsonResponse
    {
        $query = Conversation::with(['events' => function ($q) {
            $q->orderBy('event_at', 'desc')->limit(5);
        }]);

        // Filtres
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('phone')) {
            $query->where('phone_number', 'like', '%' . $request->phone . '%');
        }

        if ($request->has('date')) {
            $query->whereDate('started_at', $request->date);
        }

        if ($request->has('is_client')) {
            $query->where('is_client', $request->boolean('is_client'));
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $conversations = $query->orderBy('last_activity_at', 'desc')
                               ->paginate($perPage);

        return response()->json($conversations);
    }

    /**
     * Détail d'une conversation avec tous ses événements
     * 
     * GET /api/dashboard/conversations/{id}
     */
    public function conversationDetail(int $id): JsonResponse
    {
        $conversation = Conversation::with(['events' => function ($q) {
            $q->orderBy('event_at', 'asc');
        }])->find($id);

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        // Enrichir avec des informations supplémentaires
        $data = $conversation->toArray();
        $data['summary'] = [
            'total_events' => $conversation->events->count(),
            'free_inputs' => $conversation->events->where('event_type', 'free_input')->count(),
            'menu_choices' => $conversation->events->where('event_type', 'menu_choice')->count(),
            'duration_seconds' => $conversation->duration_seconds,
            'full_path' => $conversation->full_path,
        ];

        // Grouper les saisies libres
        $data['free_inputs'] = $conversation->events
            ->where('event_type', 'free_input')
            ->map(function ($event) {
                return [
                    'widget' => $event->widget_name,
                    'input' => $event->user_input,
                    'at' => $event->event_at->format('Y-m-d H:i:s'),
                ];
            })
            ->values();

        return response()->json($data);
    }

    /**
     * Conversations actives en temps réel
     * 
     * GET /api/dashboard/active
     */
    public function activeConversations(): JsonResponse
    {
        $active = Conversation::active()
            ->where('last_activity_at', '>=', now()->subMinutes(30))
            ->with(['events' => function ($q) {
                $q->orderBy('event_at', 'desc')->limit(1);
            }])
            ->orderBy('last_activity_at', 'desc')
            ->get()
            ->map(function ($conv) {
                return [
                    'id' => $conv->id,
                    'phone_number' => $conv->phone_number,
                    'nom_prenom' => $conv->nom_prenom,
                    'current_menu' => $conv->current_menu,
                    'last_activity' => $conv->last_activity_at->diffForHumans(),
                    'started' => $conv->started_at->diffForHumans(),
                    'last_event' => $conv->events->first()?->event_type,
                ];
            });

        return response()->json([
            'count' => $active->count(),
            'conversations' => $active
        ]);
    }

    /**
     * Historique des statistiques quotidiennes
     * 
     * GET /api/dashboard/history
     */
    public function history(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        
        $stats = DailyStatistic::where('date', '>=', now()->subDays($days))
                               ->orderBy('date', 'asc')
                               ->get();

        // Formater pour les graphiques
        $chartData = [
            'labels' => $stats->pluck('date')->map(fn($d) => $d->format('d/m')),
            'datasets' => [
                [
                    'label' => 'Conversations',
                    'data' => $stats->pluck('total_conversations'),
                ],
                [
                    'label' => 'Transferts agent',
                    'data' => $stats->pluck('transferred_conversations'),
                ],
                [
                    'label' => 'Timeouts',
                    'data' => $stats->pluck('timeout_conversations'),
                ],
            ],
        ];

        return response()->json([
            'stats' => $stats,
            'chart_data' => $chartData
        ]);
    }

    /**
     * Parcours les plus fréquents
     * 
     * GET /api/dashboard/paths
     */
    public function popularPaths(Request $request): JsonResponse
    {
        $days = $request->get('days', 7);
        
        $paths = Conversation::where('started_at', '>=', now()->subDays($days))
            ->whereNotNull('menu_path')
            ->selectRaw('menu_path, COUNT(*) as count')
            ->groupBy('menu_path')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                $path = is_string($item->menu_path) 
                    ? json_decode($item->menu_path, true) 
                    : $item->menu_path;
                return [
                    'path' => is_array($path) ? implode(' → ', $path) : $path,
                    'count' => $item->count,
                ];
            });

        return response()->json($paths);
    }

    /**
     * Recherche dans les saisies libres
     * 
     * GET /api/dashboard/search-inputs
     */
    public function searchInputs(Request $request): JsonResponse
    {
        $query = $request->get('q');
        
        if (!$query || strlen($query) < 3) {
            return response()->json([
                'error' => 'Query must be at least 3 characters'
            ], 422);
        }

        $events = ConversationEvent::where('event_type', 'free_input')
            ->where('user_input', 'like', '%' . $query . '%')
            ->with('conversation:id,phone_number,nom_prenom,started_at')
            ->orderBy('event_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'conversation_id' => $event->conversation_id,
                    'phone_number' => $event->conversation->phone_number,
                    'nom_prenom' => $event->conversation->nom_prenom,
                    'widget_name' => $event->widget_name,
                    'user_input' => $event->user_input,
                    'event_at' => $event->event_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'query' => $query,
            'count' => $events->count(),
            'results' => $events
        ]);
    }

    /**
     * Stats par menu
     */
    private function getMenuStats($startDate, $endDate): array
    {
        $events = ConversationEvent::whereBetween('event_at', [$startDate, $endDate])
            ->where('event_type', 'menu_choice')
            ->where('menu_name', 'menu_principal')
            ->selectRaw('choice_label, COUNT(*) as count')
            ->groupBy('choice_label')
            ->get()
            ->pluck('count', 'choice_label')
            ->toArray();

        return [
            'vehicules_neufs' => $events['Véhicules neufs'] ?? 0,
            'sav' => $events['Service après-vente'] ?? 0,
            'reclamations' => $events['Réclamations'] ?? 0,
            'club_vip' => $events['Club VIP'] ?? 0,
            'agent' => $events['Parler à un agent'] ?? 0,
        ];
    }
}
