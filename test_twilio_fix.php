<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Client;
use App\Models\Conversation;

echo "═══════════════════════════════════════════════\n";
echo "  TEST CORRECTION TWILIO FLOW\n";
echo "═══════════════════════════════════════════════\n\n";

// Test avec le client "Caleb Testeur" qui existe
$phoneNumber = '+22553989046';
$client = Client::where('phone_number', str_replace('whatsapp:', '', $phoneNumber))->first();

if (!$client) {
    echo "❌ Client non trouvé\n";
    exit;
}

echo "📋 CLIENT TESTÉ\n";
echo str_repeat("-", 50) . "\n";
echo "Phone: {$client->phone_number}\n";
echo "Full Name: " . ($client->client_full_name ?? 'NULL') . "\n";
echo "Is Client: " . ($client->is_client === null ? 'NULL' : ($client->is_client ? 'TRUE' : 'FALSE')) . "\n\n";

// Simuler ce que l'API retourne maintenant (après correction)
$clientExists = $client->wasRecentlyCreated === false && $client->client_full_name !== null;
$isAgentMode = false;
$isPendingAgent = false;

$apiResponse = [
    'success' => true,
    'conversation_id' => 999,
    'client_full_name' => $client->client_full_name ?? null,
    'whatsapp_profile_name' => $client->whatsapp_profile_name ?? null,
    'agent_mode' => $isAgentMode ? 'true' : 'false',
    'pending_agent' => $isPendingAgent ? 'true' : 'false',
    'has_media' => false ? 'true' : 'false',
    'client_exists' => $clientExists ? 'true' : 'false',
    'client_has_name' => $client->client_full_name !== null ? 'true' : 'false',
    'client_status_known' => $client->is_client !== null ? 'true' : 'false',
];

echo "✅ RÉPONSE API CORRIGÉE\n";
echo str_repeat("-", 50) . "\n";
echo "client_has_name: \"" . $apiResponse['client_has_name'] . "\" (type: " . gettype($apiResponse['client_has_name']) . ")\n";
echo "client_status_known: \"" . $apiResponse['client_status_known'] . "\" (type: " . gettype($apiResponse['client_status_known']) . ")\n";
echo "agent_mode: \"" . $apiResponse['agent_mode'] . "\" (type: " . gettype($apiResponse['agent_mode']) . ")\n\n";

echo "📊 JSON ENVOYÉ À TWILIO\n";
echo str_repeat("-", 50) . "\n";
echo json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n\n";

echo "🔍 VÉRIFICATION FLOW TWILIO\n";
echo str_repeat("-", 50) . "\n";

// Simuler la logique du flow
if ($apiResponse['client_has_name'] === 'true') {
    echo "✅ client_has_name === 'true' : SKIP demande nom\n";
    echo "   → Le flow va aller directement à check_client_status_known\n\n";

    if ($apiResponse['client_status_known'] === 'true') {
        echo "✅ client_status_known === 'true' : SKIP demande statut\n";
        echo "   → Le flow va aller directement au menu_principal\n\n";
        echo "🎉 SUCCÈS : Le client ne sera PAS redemandé son nom et statut !\n";
    } else {
        echo "❌ client_status_known === 'false'\n";
        echo "   → Le flow va demander le statut client\n";
    }
} else {
    echo "❌ client_has_name === 'false'\n";
    echo "   → Le flow va demander le nom\n";
}

echo "\n";
echo "═══════════════════════════════════════════════\n";
echo "  FIN DU TEST\n";
echo "═══════════════════════════════════════════════\n";
