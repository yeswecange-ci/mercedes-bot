<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$phone = '+22553989046';

echo "═══════════════════════════════════════════════\n";
echo "  DIAGNOSTIC COMPLET - CLIENT CALEB TESTEUR\n";
echo "═══════════════════════════════════════════════\n\n";

// 1. Informations client
$client = \App\Models\Client::where('phone_number', $phone)->first();

if (!$client) {
    echo "❌ Client non trouvé\n";
    exit;
}

echo "📋 INFORMATIONS CLIENT\n";
echo str_repeat("-", 50) . "\n";
echo "ID: {$client->id}\n";
echo "Phone: {$client->phone_number}\n";
echo "WhatsApp Name: " . ($client->whatsapp_profile_name ?? 'NULL') . "\n";
echo "Full Name: " . ($client->client_full_name ?? 'NULL') . "\n";
echo "Display Name: {$client->display_name}\n";
echo "Interaction Count: {$client->interaction_count}\n";
echo "Conversation Count: {$client->conversation_count}\n";
echo "\n";

echo "🔍 VÉRIFICATION API\n";
echo str_repeat("-", 50) . "\n";
echo "client_has_name devrait être: " . ($client->client_full_name !== null ? 'TRUE' : 'FALSE') . "\n";
if ($client->client_full_name !== null) {
    echo "✅ Le flow devrait SKIP la demande de nom\n";
} else {
    echo "❌ Le flow va DEMANDER le nom\n";
}
echo "\n";

// 2. Toutes les conversations
echo "💬 HISTORIQUE DES CONVERSATIONS\n";
echo str_repeat("-", 50) . "\n";

$conversations = \App\Models\Conversation::where('phone_number', $phone)
    ->orderBy('created_at', 'asc')
    ->get();

echo "Nombre total de conversations: {$conversations->count()}\n\n";

foreach($conversations as $i => $conv) {
    echo "🔹 CONVERSATION #" . ($i + 1) . " (ID: {$conv->id})\n";
    echo "   Created: {$conv->created_at}\n";
    echo "   Status: {$conv->status}\n";
    echo "   WhatsApp Name: " . ($conv->whatsapp_profile_name ?? 'NULL') . "\n";
    echo "   Full Name: " . ($conv->client_full_name ?? 'NULL') . "\n";
    echo "   Current Menu: {$conv->current_menu}\n";
    echo "   Started: {$conv->started_at}\n";
    echo "   Ended: " . ($conv->ended_at ?? 'NULL') . "\n";
    echo "\n";
}

// 3. Tous les événements collect_name
echo "📝 ÉVÉNEMENTS DE COLLECTE DE NOM\n";
echo str_repeat("-", 50) . "\n";

$collectNameEvents = \App\Models\ConversationEvent::whereIn(
    'conversation_id',
    $conversations->pluck('id')
)->where('widget_name', 'collect_name')
->orderBy('event_at', 'asc')
->get();

if ($collectNameEvents->isEmpty()) {
    echo "❌ AUCUN événement 'collect_name' trouvé\n";
} else {
    echo "Nombre d'événements 'collect_name': {$collectNameEvents->count()}\n\n";

    foreach($collectNameEvents as $i => $event) {
        echo "Event #" . ($i + 1) . ":\n";
        echo "   Conversation ID: {$event->conversation_id}\n";
        echo "   Date: {$event->event_at}\n";
        echo "   Input (nom saisi): {$event->user_input}\n";
        echo "   Widget: {$event->widget_name}\n";
        echo "\n";
    }
}

// 4. Timeline complète des événements
echo "📊 TIMELINE COMPLÈTE DES ÉVÉNEMENTS\n";
echo str_repeat("-", 50) . "\n";

$allEvents = \App\Models\ConversationEvent::whereIn(
    'conversation_id',
    $conversations->pluck('id')
)->orderBy('event_at', 'asc')->get();

foreach($allEvents as $event) {
    $time = $event->event_at->format('Y-m-d H:i:s');
    $type = str_pad($event->event_type, 20);
    $widget = $event->widget_name ? "({$event->widget_name})" : '';
    $input = $event->user_input ? "Input: " . substr($event->user_input, 0, 30) : '';

    echo "{$time} | {$type} {$widget} {$input}\n";
}

echo "\n";

// 5. Problèmes détectés
echo "⚠️  PROBLÈMES DÉTECTÉS\n";
echo str_repeat("-", 50) . "\n";

$problems = [];

// Vérifier si le nom a été écrasé
if ($collectNameEvents->count() > 1) {
    $problems[] = "🔴 Le nom a été collecté {$collectNameEvents->count()} fois (devrait être 1 seule fois)";

    foreach($collectNameEvents as $i => $event) {
        $problems[] = "   → Collecte #" . ($i+1) . ": \"{$event->user_input}\"";
    }
}

// Vérifier si client_full_name est NULL
if ($client->client_full_name === null && $collectNameEvents->count() > 0) {
    $problems[] = "🔴 client_full_name est NULL malgré une collecte de nom";
}

// Vérifier les conversations multiples actives
$activeConvs = $conversations->where('status', 'active')->count();
if ($activeConvs > 1) {
    $problems[] = "🔴 Plusieurs conversations actives simultanées ({$activeConvs})";
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
