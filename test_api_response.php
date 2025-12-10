<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════\n";
echo "  TEST RÉPONSE API POUR TWILIO\n";
echo "═══════════════════════════════════════════════\n\n";

// Simuler un client existant
$client = \App\Models\Client::where('phone_number', '+22553989046')->first();

if (!$client) {
    echo "❌ Client non trouvé\n";
    exit;
}

echo "📋 CLIENT TESTÉ\n";
echo str_repeat("-", 50) . "\n";
echo "Phone: {$client->phone_number}\n";
echo "Full Name: " . ($client->client_full_name ?? 'NULL') . "\n";
echo "Is Client: " . ($client->is_client === null ? 'NULL' : ($client->is_client ? 'TRUE' : 'FALSE')) . "\n\n";

// Simuler ce que l'API retourne
$apiResponse = [
    'client_has_name' => $client->client_full_name !== null,
    'client_status_known' => $client->is_client !== null,
];

echo "🔍 RÉPONSE API ACTUELLE\n";
echo str_repeat("-", 50) . "\n";
echo "client_has_name: ";
var_dump($apiResponse['client_has_name']);
echo "Type: " . gettype($apiResponse['client_has_name']) . "\n\n";

echo "client_status_known: ";
var_dump($apiResponse['client_status_known']);
echo "Type: " . gettype($apiResponse['client_status_known']) . "\n\n";

echo "📊 ENCODAGE JSON\n";
echo str_repeat("-", 50) . "\n";
echo json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n\n";

echo "⚠️  PROBLÈME DÉTECTÉ\n";
echo str_repeat("-", 50) . "\n";
echo "Le flow Twilio compare avec la CHAÎNE \"true\"\n";
echo "Mais l'API retourne le BOOLÉEN true\n\n";

echo "✅ SOLUTION\n";
echo str_repeat("-", 50) . "\n";
echo "L'API doit retourner des chaînes \"true\"/\"false\"\n";
echo "au lieu de booléens true/false\n\n";

// Simuler la solution
$apiResponseFixed = [
    'client_has_name' => $client->client_full_name !== null ? 'true' : 'false',
    'client_status_known' => $client->is_client !== null ? 'true' : 'false',
];

echo "🔧 RÉPONSE API CORRIGÉE\n";
echo str_repeat("-", 50) . "\n";
echo "client_has_name: " . $apiResponseFixed['client_has_name'] . "\n";
echo "Type: " . gettype($apiResponseFixed['client_has_name']) . "\n\n";

echo "client_status_known: " . $apiResponseFixed['client_status_known'] . "\n";
echo "Type: " . gettype($apiResponseFixed['client_status_known']) . "\n\n";

echo "📊 ENCODAGE JSON CORRIGÉ\n";
echo str_repeat("-", 50) . "\n";
echo json_encode($apiResponseFixed, JSON_PRETTY_PRINT) . "\n\n";

echo "═══════════════════════════════════════════════\n";
