<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Mettre à jour le client manuellement
$client = \App\Models\Client::where('phone_number', '+22553989046')->first();

if ($client) {
    $client->update(['client_full_name' => 'Caleb Testeur']);  // Restaurer le nom original

    echo "✅ Client mis à jour:\n";
    echo "Phone: {$client->phone_number}\n";
    echo "Full Name: {$client->client_full_name}\n";
    echo "WhatsApp Name: {$client->whatsapp_profile_name}\n";
    echo "\nMaintenant client_has_name = true\n";
    echo "Le bot ne redemandera plus le nom ✅\n";
} else {
    echo "❌ Client non trouvé\n";
}
