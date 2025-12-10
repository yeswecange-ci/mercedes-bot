<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════\n";
echo "  DIAGNOSTIC STATISTIQUES DASHBOARD\n";
echo "═══════════════════════════════════════════════\n\n";

// Dates de test (30 derniers jours)
$dateFrom = now()->subDays(30)->format('Y-m-d');
$dateTo = now()->format('Y-m-d');

echo "📅 PÉRIODE DE TEST\n";
echo str_repeat("-", 50) . "\n";
echo "Date de début: {$dateFrom}\n";
echo "Date de fin: {$dateTo}\n\n";

// 1. Vérifier les conversations
echo "💬 CONVERSATIONS\n";
echo str_repeat("-", 50) . "\n";

$totalConversations = \App\Models\Conversation::count();
echo "Total conversations (tous): {$totalConversations}\n";

$conversationsWithDates = \App\Models\Conversation::whereNotNull('started_at')->count();
echo "Conversations avec started_at: {$conversationsWithDates}\n";

$conversationsInRange = \App\Models\Conversation::whereBetween('started_at', [$dateFrom, $dateTo])->count();
echo "Conversations dans la période: {$conversationsInRange}\n";

$activeConversations = \App\Models\Conversation::where('status', 'active')->count();
echo "Conversations actives: {$activeConversations}\n";

$completedConversations = \App\Models\Conversation::where('status', 'completed')->count();
echo "Conversations terminées: {$completedConversations}\n\n";

// Afficher quelques conversations pour debug
echo "📋 EXEMPLE DE CONVERSATIONS\n";
echo str_repeat("-", 50) . "\n";
$sampleConvs = \App\Models\Conversation::orderBy('created_at', 'desc')->limit(3)->get();
foreach($sampleConvs as $conv) {
    echo "ID: {$conv->id}\n";
    echo "  Created: {$conv->created_at}\n";
    echo "  Started: " . ($conv->started_at ?? 'NULL') . "\n";
    echo "  Status: {$conv->status}\n";
    echo "  Phone: {$conv->phone_number}\n";
    echo "  Name: " . ($conv->client_full_name ?? 'NULL') . "\n";
    echo "\n";
}

// 2. Vérifier les clients
echo "👥 CLIENTS\n";
echo str_repeat("-", 50) . "\n";

$totalClients = \App\Models\Client::count();
echo "Total clients: {$totalClients}\n";

$clientsWithInteraction = \App\Models\Client::whereNotNull('last_interaction_at')->count();
echo "Clients avec last_interaction_at: {$clientsWithInteraction}\n";

$clientsInRange = \App\Models\Client::whereBetween('last_interaction_at', [$dateFrom, $dateTo])->count();
echo "Clients dans la période: {$clientsInRange}\n";

$isClient = \App\Models\Client::where('is_client', true)->count();
echo "Clients Mercedes (is_client=true): {$isClient}\n";

$nonClient = \App\Models\Client::where('is_client', false)->count();
echo "Non-clients (is_client=false): {$nonClient}\n\n";

// 3. Vérifier les événements
echo "📊 ÉVÉNEMENTS\n";
echo str_repeat("-", 50) . "\n";

$totalEvents = \App\Models\ConversationEvent::count();
echo "Total événements: {$totalEvents}\n";

$eventsWithConv = \App\Models\ConversationEvent::whereHas('conversation')->count();
echo "Événements liés à des conversations: {$eventsWithConv}\n";

$messageReceived = \App\Models\ConversationEvent::where('event_type', 'message_received')->count();
echo "Messages reçus: {$messageReceived}\n";

$menuChoice = \App\Models\ConversationEvent::where('event_type', 'menu_choice')->count();
echo "Choix de menu: {$menuChoice}\n";

$freeInput = \App\Models\ConversationEvent::where('event_type', 'free_input')->count();
echo "Saisies libres: {$freeInput}\n\n";

// 4. Vérifier les statistiques quotidiennes
echo "📈 STATISTIQUES QUOTIDIENNES\n";
echo str_repeat("-", 50) . "\n";

$dailyStatsCount = \App\Models\DailyStatistic::count();
echo "Total daily_statistics: {$dailyStatsCount}\n";

$dailyStatsInRange = \App\Models\DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->count();
echo "Daily statistics dans la période: {$dailyStatsInRange}\n\n";

// 5. Problème potentiel
echo "⚠️  DIAGNOSTIC\n";
echo str_repeat("-", 50) . "\n";

$problems = [];

if ($conversationsInRange === 0 && $totalConversations > 0) {
    $problems[] = "🔴 Conversations existent mais aucune dans la période de 30 jours";
    $problems[] = "   → Vérifier les dates de 'started_at' dans la table conversations";

    // Trouver la plus ancienne et la plus récente
    $oldest = \App\Models\Conversation::whereNotNull('started_at')->orderBy('started_at', 'asc')->first();
    $newest = \App\Models\Conversation::whereNotNull('started_at')->orderBy('started_at', 'desc')->first();

    if ($oldest) {
        $problems[] = "   → Plus ancienne conversation: " . $oldest->started_at->format('Y-m-d H:i:s');
    }
    if ($newest) {
        $problems[] = "   → Plus récente conversation: " . $newest->started_at->format('Y-m-d H:i:s');
    }
}

if ($conversationsWithDates < $totalConversations) {
    $missing = $totalConversations - $conversationsWithDates;
    $problems[] = "🟡 {$missing} conversations n'ont pas de 'started_at'";
}

if ($clientsInRange === 0 && $totalClients > 0) {
    $problems[] = "🔴 Clients existent mais aucun dans la période de 30 jours";
    $problems[] = "   → Vérifier les dates de 'last_interaction_at' dans la table clients";
}

if (empty($problems)) {
    echo "✅ Aucun problème détecté\n";
} else {
    foreach($problems as $problem) {
        echo $problem . "\n";
    }
}

echo "\n";
echo "═══════════════════════════════════════════════\n";
echo "  FIN DU DIAGNOSTIC\n";
echo "═══════════════════════════════════════════════\n";
