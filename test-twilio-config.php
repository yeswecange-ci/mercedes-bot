<?php
/**
 * Script de test de configuration Twilio
 *
 * Exécutez ce script depuis la racine du projet Laravel :
 * php test-twilio-config.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "  TEST DE CONFIGURATION TWILIO\n";
echo "========================================\n\n";

// Vérifier les variables d'environnement
$accountSid = config('services.twilio.account_sid');
$authToken = config('services.twilio.auth_token');
$whatsappNumber = config('services.twilio.whatsapp_number');

echo "1. Variables d'environnement :\n";
echo "   ---------------------------\n";
echo "   TWILIO_ACCOUNT_SID    : " . ($accountSid ? substr($accountSid, 0, 8) . '...' : '❌ NON DÉFINI') . "\n";
echo "   TWILIO_AUTH_TOKEN     : " . ($authToken ? '✅ DÉFINI (masqué)' : '❌ NON DÉFINI') . "\n";
echo "   TWILIO_WHATSAPP_NUMBER: " . ($whatsappNumber ?: '❌ NON DÉFINI') . "\n\n";

// Vérifier que les credentials sont valides (format)
$errors = [];

if (empty($accountSid)) {
    $errors[] = "❌ TWILIO_ACCOUNT_SID est vide";
} elseif (!str_starts_with($accountSid, 'AC')) {
    $errors[] = "❌ TWILIO_ACCOUNT_SID doit commencer par 'AC'";
} elseif (strlen($accountSid) !== 34) {
    $errors[] = "❌ TWILIO_ACCOUNT_SID doit faire 34 caractères";
}

if (empty($authToken)) {
    $errors[] = "❌ TWILIO_AUTH_TOKEN est vide";
} elseif (strlen($authToken) !== 32) {
    $errors[] = "⚠️  TWILIO_AUTH_TOKEN devrait faire 32 caractères (actuellement: " . strlen($authToken) . ")";
}

if (empty($whatsappNumber)) {
    $errors[] = "❌ TWILIO_WHATSAPP_NUMBER est vide";
} elseif (!preg_match('/^\+[0-9]{10,15}$/', $whatsappNumber)) {
    $errors[] = "❌ TWILIO_WHATSAPP_NUMBER doit être au format +2250716700900";
}

echo "2. Validation des credentials :\n";
echo "   ---------------------------\n";

if (count($errors) > 0) {
    foreach ($errors as $error) {
        echo "   $error\n";
    }
    echo "\n";
    echo "❌ CONFIGURATION INVALIDE\n\n";
    echo "📝 Actions requises :\n";
    echo "   1. Allez sur https://console.twilio.com/\n";
    echo "   2. Récupérez votre Account SID et Auth Token\n";
    echo "   3. Modifiez le fichier .env :\n";
    echo "      TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\n";
    echo "      TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\n";
    echo "      TWILIO_WHATSAPP_NUMBER=+2250716700900\n";
    echo "   4. Exécutez : php artisan config:clear\n\n";
    exit(1);
}

echo "   ✅ Format des credentials valide\n\n";

// Test de connexion à Twilio (optionnel)
echo "3. Test de connexion à Twilio :\n";
echo "   ---------------------------\n";

try {
    $twilio = new \Twilio\Rest\Client($accountSid, $authToken);

    // Récupérer les informations du compte
    $account = $twilio->api->v2010->accounts($accountSid)->fetch();

    echo "   ✅ Connexion réussie !\n";
    echo "   📋 Informations du compte :\n";
    echo "      - Nom     : " . $account->friendlyName . "\n";
    echo "      - Statut  : " . $account->status . "\n";
    echo "      - Type    : " . $account->type . "\n\n";

    // Test d'accès aux messages WhatsApp (ne pas envoyer de message)
    echo "4. Vérification du numéro WhatsApp :\n";
    echo "   ---------------------------\n";

    try {
        // Liste des numéros WhatsApp du compte
        $phoneNumbers = $twilio->messaging->v1->services->read();

        if (count($phoneNumbers) > 0) {
            echo "   ✅ Numéros WhatsApp disponibles :\n";
            foreach ($phoneNumbers as $service) {
                echo "      - " . $service->friendlyName . " (SID: " . $service->sid . ")\n";
            }
        } else {
            echo "   ⚠️  Aucun service de messagerie trouvé\n";
            echo "   Note : Cela peut être normal si vous utilisez directement un sandbox WhatsApp\n";
        }

    } catch (\Exception $e) {
        echo "   ⚠️  Impossible de lister les numéros WhatsApp\n";
        echo "   Note : Vérifiez que votre compte a accès à l'API WhatsApp\n";
    }

    echo "\n";
    echo "========================================\n";
    echo "✅ CONFIGURATION TWILIO VALIDE\n";
    echo "========================================\n";
    echo "Vous pouvez maintenant envoyer des messages WhatsApp depuis votre application.\n\n";

    exit(0);

} catch (\Twilio\Exceptions\AuthenticationException $e) {
    echo "   ❌ ERREUR D'AUTHENTIFICATION\n";
    echo "   Message : " . $e->getMessage() . "\n\n";
    echo "   📝 Vérifiez que :\n";
    echo "      - Votre ACCOUNT_SID est correct\n";
    echo "      - Votre AUTH_TOKEN est correct\n";
    echo "      - Vous n'avez pas de restriction d'IP sur votre compte Twilio\n\n";
    exit(1);

} catch (\Exception $e) {
    echo "   ❌ ERREUR DE CONNEXION\n";
    echo "   Message : " . $e->getMessage() . "\n\n";
    echo "   📝 Vérifiez que :\n";
    echo "      - Votre serveur a accès à Internet\n";
    echo "      - L'extension PHP cURL est activée\n";
    echo "      - Aucun firewall ne bloque les requêtes vers api.twilio.com\n\n";
    exit(1);
}
