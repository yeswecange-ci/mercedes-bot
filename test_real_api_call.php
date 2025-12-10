<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════\n";
echo "  SIMULATION APPEL API RÉEL\n";
echo "═══════════════════════════════════════════════\n\n";

// Simuler exactement ce que Twilio envoie
$phoneNumber = 'whatsapp:+22553989046'; // Avec le préfixe whatsapp:
$from = $phoneNumber;

echo "📞 REQUÊTE TWILIO SIMULÉE\n";
echo str_repeat("-", 50) . "\n";
echo "From: {$from}\n\n";

// Nettoyer le numéro (comme dans le controller)
$phoneNumberClean = str_replace('whatsapp:', '', $from);
echo "Phone Number (cleaned): {$phoneNumberClean}\n\n";

// Vérifier si le client existe
$client = \App\Models\Client::where('phone_number', $phoneNumberClean)->first();

if (!$client) {
    echo "❌ CLIENT NON TROUVÉ EN BASE !\n";
    echo "   → C'est le problème : le client n'existe pas avec ce numéro\n\n";

    // Chercher tous les clients pour voir
    echo "Clients en base:\n";
    $allClients = \App\Models\Client::all();
    foreach($allClients as $c) {
        echo "  - {$c->phone_number} : " . ($c->client_full_name ?? 'NO NAME') . "\n";
    }
    exit;
}

echo "✅ CLIENT TROUVÉ\n";
echo str_repeat("-", 50) . "\n";
echo "ID: {$client->id}\n";
echo "Phone: {$client->phone_number}\n";
echo "Full Name: " . ($client->client_full_name ?? 'NULL') . "\n";
echo "WhatsApp Name: " . ($client->whatsapp_profile_name ?? 'NULL') . "\n";
echo "Is Client: " . ($client->is_client === null ? 'NULL' : ($client->is_client ? 'TRUE' : 'FALSE')) . "\n";
echo "Created: {$client->created_at}\n";
echo "Last Interaction: " . ($client->last_interaction_at ?? 'NULL') . "\n\n";

// Vérifier la conversation active
$conversation = \App\Models\Conversation::where('phone_number', $phoneNumberClean)
    ->whereIn('status', ['active', 'transferred'])
    ->where('last_activity_at', '>', now()->subHours(24))
    ->latest()
    ->first();

if ($conversation) {
    echo "💬 CONVERSATION ACTIVE TROUVÉE\n";
    echo str_repeat("-", 50) . "\n";
    echo "ID: {$conversation->id}\n";
    echo "Status: {$conversation->status}\n";
    echo "Client Full Name in Conv: " . ($conversation->client_full_name ?? 'NULL') . "\n";
    echo "Started: {$conversation->started_at}\n\n";
} else {
    echo "📝 AUCUNE CONVERSATION ACTIVE\n";
    echo "   → Une nouvelle conversation sera créée\n\n";
}

// Simuler la logique du controller
$clientExists = $client->wasRecentlyCreated === false && $client->client_full_name !== null;
$isAgentMode = false;
$isPendingAgent = false;

echo "🔍 VALEURS CALCULÉES\n";
echo str_repeat("-", 50) . "\n";
echo "client->wasRecentlyCreated: " . ($client->wasRecentlyCreated ? 'TRUE' : 'FALSE') . "\n";
echo "client->client_full_name !== null: " . ($client->client_full_name !== null ? 'TRUE' : 'FALSE') . "\n";
echo "clientExists: " . ($clientExists ? 'TRUE' : 'FALSE') . "\n\n";

// Simuler la réponse API
$apiResponse = [
    'success' => true,
    'conversation_id' => $conversation->id ?? 'NEW',
    'phone_number' => $phoneNumberClean,
    'client_full_name' => $client->client_full_name ?? $conversation->client_full_name ?? null,
    'whatsapp_profile_name' => $client->whatsapp_profile_name ?? $conversation->whatsapp_profile_name ?? null,
    'is_client' => $client->is_client ?? $conversation->is_client ?? null,
    'agent_mode' => $isAgentMode ? 'true' : 'false',
    'pending_agent' => $isPendingAgent ? 'true' : 'false',
    'client_has_name' => $client->client_full_name !== null ? 'true' : 'false',
    'client_status_known' => $client->is_client !== null ? 'true' : 'false',
];

echo "📊 RÉPONSE API QUI SERA ENVOYÉE À TWILIO\n";
echo str_repeat("-", 50) . "\n";
echo json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n\n";

echo "🎯 COMPORTEMENT ATTENDU DU FLOW\n";
echo str_repeat("-", 50) . "\n";
if ($apiResponse['client_has_name'] === 'true') {
    echo "✅ client_has_name = 'true'\n";
    echo "   → Flow devrait SKIP ask_name\n\n";

    if ($apiResponse['client_status_known'] === 'true') {
        echo "✅ client_status_known = 'true'\n";
        echo "   → Flow devrait SKIP ask_is_client\n";
        echo "   → Flow devrait aller DIRECTEMENT au menu_principal\n\n";
        echo "🎉 CLIENT DEVRAIT ÊTRE RECONNU !\n";
    } else {
        echo "❌ client_status_known = 'false'\n";
        echo "   → Flow va demander si vous êtes client\n";
    }
} else {
    echo "❌ client_has_name = 'false'\n";
    echo "   → Flow va demander le nom\n";
    echo "   → PROBLÈME : Le client n'a pas de nom en base !\n";
}

echo "\n";
echo "═══════════════════════════════════════════════\n";
