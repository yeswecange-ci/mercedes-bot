<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════\n";
echo "  LISTE DE TOUS LES CLIENTS\n";
echo "═══════════════════════════════════════════════\n\n";

$clients = \App\Models\Client::orderBy('last_interaction_at', 'desc')->get();

if ($clients->isEmpty()) {
    echo "❌ Aucun client en base\n";
    exit;
}

echo "Nombre total de clients: {$clients->count()}\n\n";

foreach($clients as $client) {
    echo str_repeat("=", 70) . "\n";
    echo "📱 {$client->phone_number}\n";
    echo str_repeat("-", 70) . "\n";
    echo "Full Name: " . ($client->client_full_name ?? '❌ NULL') . "\n";
    echo "WhatsApp Name: " . ($client->whatsapp_profile_name ?? 'NULL') . "\n";
    echo "Is Client: " . ($client->is_client === null ? '❌ NULL' : ($client->is_client ? '✅ OUI' : '❌ NON')) . "\n";
    echo "Last Interaction: " . ($client->last_interaction_at ? $client->last_interaction_at->format('Y-m-d H:i:s') : 'NULL') . "\n";

    // Vérifier ce que l'API retournerait
    $hasName = $client->client_full_name !== null;
    $hasStatus = $client->is_client !== null;

    echo "\nCe que l'API retournera:\n";
    echo "  client_has_name: " . ($hasName ? '✅ "true"' : '❌ "false"') . "\n";
    echo "  client_status_known: " . ($hasStatus ? '✅ "true"' : '❌ "false"') . "\n";

    if ($hasName && $hasStatus) {
        echo "  🎉 Ce client sera RECONNU (pas de redemande)\n";
    } elseif ($hasName && !$hasStatus) {
        echo "  ⚠️  Le flow demandera SI vous êtes client\n";
    } elseif (!$hasName && $hasStatus) {
        echo "  ⚠️  Le flow demandera votre NOM\n";
    } else {
        echo "  ❌ Le flow demandera NOM + STATUT\n";
    }
    echo "\n";
}

echo str_repeat("=", 70) . "\n";
echo "\nPour tester:\n";
echo "1. Envoyez un message WhatsApp depuis un de ces numéros\n";
echo "2. Vérifiez que le numéro correspond EXACTEMENT (avec +)\n";
echo "3. Les clients avec ✅ sur les deux lignes devraient être reconnus\n\n";
