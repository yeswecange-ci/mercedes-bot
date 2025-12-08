<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TwilioWebhookController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes - Mercedes-Benz Bot Dashboard
|--------------------------------------------------------------------------
|
| Routes pour recevoir les événements du bot Twilio
| et pour alimenter le dashboard de supervision.
|
*/

/*
|--------------------------------------------------------------------------
| Authentication API Routes (for Postman, mobile apps, etc.)
|--------------------------------------------------------------------------
| Ces routes utilisent Sanctum pour l'authentification via token
| Pas de vérification CSRF - parfait pour Postman et les tests
*/
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Routes protégées
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

/*
|--------------------------------------------------------------------------
| Twilio Webhooks (Direct Integration)
|--------------------------------------------------------------------------
| Ces routes reçoivent les données directement depuis Twilio Flow
| Elles peuvent être appelées sans authentification pour les webhooks
*/
Route::prefix('twilio')->group(function () {

    // Message entrant WhatsApp
    Route::post('/incoming', [TwilioWebhookController::class, 'handleIncomingMessage'])
        ->middleware('throttle:60,1'); // 60 requests per minute

    // Choix de menu
    Route::post('/menu-choice', [TwilioWebhookController::class, 'handleMenuChoice']);

    // Saisie libre utilisateur
    Route::post('/free-input', [TwilioWebhookController::class, 'handleFreeInput']);

    // Transfert vers agent
    Route::post('/agent-transfer', [TwilioWebhookController::class, 'handleAgentTransfer']);

    // Fin de conversation
    Route::post('/complete', [TwilioWebhookController::class, 'completeConversation']);

    // Envoyer un message (pour le dashboard)
    Route::post('/send-message', [TwilioWebhookController::class, 'sendMessage'])
        ->middleware(['auth:sanctum']);

});

/*
|--------------------------------------------------------------------------
| Webhooks (depuis n8n) - Legacy
|--------------------------------------------------------------------------
| Ces routes reçoivent les données du bot Twilio via n8n
| Elles doivent être protégées par un token API
*/
Route::prefix('webhook')->group(function () {

    // Événement générique (choix menu, saisie, message, etc.)
    Route::post('/event', [WebhookController::class, 'handleEvent']);

    // Mise à jour des données utilisateur (nom, email, etc.)
    Route::post('/user-data', [WebhookController::class, 'updateUserData']);

    // Notification de transfert vers Chatwoot
    Route::post('/transfer', [WebhookController::class, 'handleTransfer']);

    // Fin de conversation
    Route::post('/complete', [WebhookController::class, 'handleComplete']);

});

/*
|--------------------------------------------------------------------------
| Dashboard API
|--------------------------------------------------------------------------
| Ces routes alimentent le dashboard de supervision
| Elles doivent être protégées par authentification
*/
Route::prefix('dashboard')->middleware(['auth:sanctum'])->group(function () {

    // Statistiques globales
    Route::get('/stats', [DashboardController::class, 'stats']);

    // Liste des conversations (avec filtres et pagination)
    Route::get('/conversations', [DashboardController::class, 'conversations']);

    // Détail d'une conversation
    Route::get('/conversations/{id}', [DashboardController::class, 'conversationDetail']);

    // Conversations actives en temps réel
    Route::get('/active', [DashboardController::class, 'activeConversations']);

    // Historique des statistiques quotidiennes
    Route::get('/history', [DashboardController::class, 'history']);

    // Parcours les plus fréquents
    Route::get('/paths', [DashboardController::class, 'popularPaths']);

    // Recherche dans les saisies libres
    Route::get('/search-inputs', [DashboardController::class, 'searchInputs']);

});

/*
|--------------------------------------------------------------------------
| Routes sans authentification (pour les tests)
|--------------------------------------------------------------------------
| À supprimer ou protéger en production
*/
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String()
    ]);
});
