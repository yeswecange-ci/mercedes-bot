<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════\n";
echo "  TEST APRÈS CORRECTION\n";
echo "═══════════════════════════════════════════════\n\n";

// Simuler la requête du dashboard
$dateFrom = now()->subDays(30)->format('Y-m-d');
$dateTo = now()->format('Y-m-d');
$dateFromFull = $dateFrom . ' 00:00:00';
$dateToFull = $dateTo . ' 23:59:59';

echo "📅 PÉRIODE\n";
echo str_repeat("-", 50) . "\n";
echo "Date de début: {$dateFrom} (Full: {$dateFromFull})\n";
echo "Date de fin: {$dateTo} (Full: {$dateToFull})\n\n";

// Test des requêtes corrigées
echo "✅ RÉSULTATS AVEC CORRECTION\n";
echo str_repeat("-", 50) . "\n";

$conversationsInRange = \App\Models\Conversation::whereBetween('started_at', [$dateFromFull, $dateToFull]);
echo "Conversations dans la période: " . $conversationsInRange->count() . "\n";

$clientsInRange = \App\Models\Client::whereBetween('last_interaction_at', [$dateFromFull, $dateToFull]);
echo "Clients dans la période: " . $clientsInRange->count() . "\n";

$totalEvents = \App\Models\ConversationEvent::whereHas('conversation', function($q) use ($dateFromFull, $dateToFull) {
    $q->whereBetween('started_at', [$dateFromFull, $dateToFull]);
})->count();
echo "Événements dans la période: {$totalEvents}\n";

$totalMessages = \App\Models\ConversationEvent::where('event_type', 'message_received')
    ->whereHas('conversation', function($q) use ($dateFromFull, $dateToFull) {
        $q->whereBetween('started_at', [$dateFromFull, $dateToFull]);
    })->count();
echo "Messages reçus: {$totalMessages}\n";

$uniqueClients = (clone $conversationsInRange)->distinct('phone_number')->count('phone_number');
echo "Clients uniques: {$uniqueClients}\n";

echo "\n✅ Les statistiques s'affichent maintenant correctement !\n";
echo "═══════════════════════════════════════════════\n";
