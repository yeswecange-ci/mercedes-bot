<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationEvent;
use App\Models\DailyStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardWebController extends Controller
{
    /**
     * Display the main dashboard
     */
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Get overall statistics
        $stats = [
            'total_conversations' => Conversation::whereBetween('started_at', [$dateFrom, $dateTo])->count(),
            'active_conversations' => Conversation::active()->count(),
            'completed_conversations' => Conversation::whereBetween('started_at', [$dateFrom, $dateTo])
                ->where('status', 'completed')->count(),
            'transferred_conversations' => Conversation::whereBetween('started_at', [$dateFrom, $dateTo])
                ->where('status', 'transferred')->count(),
            'total_clients' => Conversation::whereBetween('started_at', [$dateFrom, $dateTo])
                ->where('is_client', true)->distinct('phone_number')->count(),
            'total_non_clients' => Conversation::whereBetween('started_at', [$dateFrom, $dateTo])
                ->where('is_client', false)->distinct('phone_number')->count(),
            'avg_duration' => Conversation::whereBetween('started_at', [$dateFrom, $dateTo])
                ->whereNotNull('ended_at')
                ->avg('duration_seconds'),
        ];

        // Get daily statistics for chart
        $dailyStats = DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'asc')
            ->get();

        // Get menu distribution
        $menuStats = [
            'vehicules' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_vehicules_neufs'),
            'sav' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_sav'),
            'reclamation' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_reclamations'),
            'vip' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_club_vip'),
            'agent' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_agent'),
        ];

        // Recent conversations
        $recentConversations = Conversation::with('events')
            ->whereBetween('started_at', [$dateFrom, $dateTo])
            ->orderBy('started_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact('stats', 'dailyStats', 'menuStats', 'recentConversations', 'dateFrom', 'dateTo'));
    }

    /**
     * Display active conversations
     */
    public function active()
    {
        $activeConversations = Conversation::active()
            ->with('events')
            ->orderBy('last_activity_at', 'desc')
            ->get();

        return view('dashboard.active', compact('activeConversations'));
    }

    /**
     * Display conversations pending agent takeover
     */
    public function pending()
    {
        // Get conversations that are transferred but no agent assigned yet
        // OR conversations where user explicitly requested agent (check events)
        $pendingConversations = Conversation::where(function($query) {
                $query->where('status', 'transferred')
                      ->whereNull('agent_id');
            })
            ->orWhereHas('events', function($query) {
                $query->where('event_type', 'agent_transfer')
                      ->where('created_at', '>=', now()->subHours(24));
            })
            ->with(['events' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(5);
            }])
            ->orderBy('transferred_at', 'desc')
            ->orderBy('last_activity_at', 'desc')
            ->get();

        // Count conversations waiting for agent
        $pendingCount = $pendingConversations->count();

        return view('dashboard.pending', compact('pendingConversations', 'pendingCount'));
    }

    /**
     * Display all conversations list
     */
    public function conversations(Request $request)
    {
        $query = Conversation::with('events');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_client')) {
            $query->where('is_client', $request->is_client);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('started_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('started_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('phone_number', 'like', "%{$search}%")
                  ->orWhere('nom_prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $conversations = $query->orderBy('started_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.conversations', compact('conversations'));
    }

    /**
     * Display conversation detail
     */
    public function show($id)
    {
        $conversation = Conversation::with(['events' => function($query) {
            $query->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        return view('dashboard.show', compact('conversation'));
    }

    /**
     * Display statistics page
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Get daily statistics for charts
        $dailyStats = DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'asc')
            ->get();

        // Get menu distribution
        $menuStats = [
            'vehicules' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_vehicules_neufs'),
            'sav' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_sav'),
            'reclamation' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_reclamations'),
            'vip' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_club_vip'),
            'agent' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_agent'),
        ];

        // Status distribution
        $statusStats = Conversation::whereBetween('started_at', [$dateFrom, $dateTo])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Popular paths
        $popularPaths = Conversation::whereBetween('started_at', [$dateFrom, $dateTo])
            ->whereNotNull('menu_path')
            ->select('menu_path', DB::raw('count(*) as count'))
            ->groupBy('menu_path')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Peak hours
        $peakHours = Conversation::whereBetween('started_at', [$dateFrom, $dateTo])
            ->select(DB::raw('HOUR(started_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get();

        return view('dashboard.statistics', compact('dailyStats', 'menuStats', 'statusStats', 'popularPaths', 'peakHours', 'dateFrom', 'dateTo'));
    }

    /**
     * Display search page for free inputs
     */
    public function search(Request $request)
    {
        $query = ConversationEvent::with('conversation')
            ->where('event_type', 'free_input');

        if ($request->filled('search')) {
            $query->where('user_input', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('event_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('event_at', '<=', $request->date_to);
        }

        $freeInputs = $query->orderBy('event_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.search', compact('freeInputs'));
    }
}
