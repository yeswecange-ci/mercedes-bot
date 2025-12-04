# 💬 Système de Chat Agent - Mercedes-Benz Bot Dashboard

## 📋 Vue d'ensemble

Le système de chat agent permet aux agents humains de prendre en charge les conversations WhatsApp et de communiquer directement avec les clients via le dashboard, sans dépendre de Chatwoot ou n8n.

## ✨ Fonctionnalités

### 1. **Interface de chat en temps réel**
- Vue de conversation complète avec historique des messages
- Distinction visuelle entre messages client, bot et agent
- Auto-refresh toutes les 5 secondes
- Envoi de messages en temps réel via AJAX

### 2. **Prise en charge des conversations**
- Bouton "Prendre en charge" pour les conversations actives
- Notification automatique au client lors de la prise en charge
- Statut de conversation mis à jour automatiquement

### 3. **Communication bidirectionnelle**
- Agent → Client : Envoi de messages via le dashboard
- Client → Agent : Messages automatiquement affichés dans l'interface
- Messages envoyés via l'API Twilio

### 4. **Gestion des conversations**
- Clôture de conversation avec message de fermeture
- Calcul automatique de la durée
- Historique complet des interactions

## 🗂️ Fichiers créés/modifiés

### Nouveaux fichiers

#### 1. **ChatController.php** (`app/Http/Controllers/Web/ChatController.php`)
Contrôleur gérant toutes les interactions agent-client :
- `show()` : Affiche l'interface de chat
- `takeOver()` : Prise en charge d'une conversation
- `send()` : Envoi de message au client
- `close()` : Clôture de conversation

#### 2. **chat.blade.php** (`resources/views/dashboard/chat.blade.php`)
Interface de chat complète avec :
- Affichage des messages en temps réel
- Formulaire d'envoi de messages
- Sidebar avec informations client
- Auto-scroll et auto-refresh

#### 3. **twilio-flow-agent-mode.json**
Flow Twilio mis à jour avec support du mode agent :
- Détection automatique du statut de conversation
- Bypass du bot quand agent actif
- Message d'attente automatique pour le client

#### 4. **Migration** (`database/migrations/2025_12_03_085052_add_agent_id_to_conversations_table.php`)
Ajoute les colonnes nécessaires :
- `agent_id` : Clé étrangère vers la table users
- Vérification d'existence pour éviter les doublons

#### 5. **AGENT_CHAT_SYSTEM.md** (ce fichier)
Documentation complète du système

### Fichiers modifiés

#### 1. **TwilioWebhookController.php**
- Détection du mode agent (`agent_mode` flag)
- Retour de statut de conversation étendu
- Recherche de conversations actives ou transférées

#### 2. **Conversation.php** (Model)
- Ajout de `agent_id` dans fillable
- Relation `agent()` avec User
- Import de `BelongsTo`

#### 3. **routes/web.php**
Routes ajoutées :
```php
Route::prefix('dashboard/chat')->name('dashboard.chat.')->group(function () {
    Route::get('/{id}', [ChatController::class, 'show'])->name('show');
    Route::post('/{id}/take-over', [ChatController::class, 'takeOver'])->name('take-over');
    Route::post('/{id}/send', [ChatController::class, 'send'])->name('send');
    Route::post('/{id}/close', [ChatController::class, 'close'])->name('close');
});
```

#### 4. **conversations.blade.php**
- Boutons "Chat" pour conversations transférées
- Boutons "Prendre en charge" pour conversations actives
- Actions conditionnelles selon le statut

#### 5. **active.blade.php**
- Actions adaptées au statut de conversation
- Boutons de prise en charge et chat
- Amélioration de l'UI

#### 6. **TWILIO_INTEGRATION_GUIDE.md**
- Section sur les deux flows disponibles
- Documentation du flux agent
- Instructions de migration

## 🔄 Flux de fonctionnement

### Scénario 1 : Conversation automatique (Bot uniquement)

```
1. Client envoie "mercedes" sur WhatsApp
   └─> Twilio reçoit le message
       └─> POST /api/twilio/incoming
           └─> Laravel crée/trouve la conversation
               └─> Retourne agent_mode = false
                   └─> Twilio lance le sous-flow principal
                       └─> Bot gère la conversation
```

### Scénario 2 : Transfert à un agent

```
1. Client parle avec le bot
   └─> Agent consulte le dashboard
       └─> Clique sur "Prendre en charge"
           └─> POST /dashboard/chat/{id}/take-over
               └─> Statut: active → transferred
                   └─> Message envoyé au client via Twilio
                       └─> Interface de chat ouverte

2. Client envoie un nouveau message
   └─> Twilio reçoit le message
       └─> POST /api/twilio/incoming
           └─> Laravel détecte agent_mode = true
               └─> Twilio envoie message d'attente
                   └─> Message visible dans le chat agent
                       └─> Auto-refresh affiche le nouveau message

3. Agent répond via le dashboard
   └─> Agent tape un message
       └─> AJAX POST /dashboard/chat/{id}/send
           └─> Laravel envoie via Twilio API
               └─> Message reçu par le client
                   └─> Enregistré dans conversation_events

4. Agent clôture la conversation
   └─> Clique sur "Clôturer"
       └─> POST /dashboard/chat/{id}/close
           └─> Statut: transferred → completed
               └─> Message de fermeture envoyé
                   └─> Durée calculée et enregistrée
```

## 🚀 Utilisation

### Pour les agents

#### 1. Prendre en charge une conversation

1. Aller sur **Dashboard → Conversations actives** ou **Toutes les conversations**
2. Trouver une conversation avec statut "Active"
3. Cliquer sur **"Prendre en charge →"**
4. Vous serez redirigé vers l'interface de chat
5. Le client reçoit automatiquement un message de notification

#### 2. Communiquer avec le client

1. Dans l'interface de chat, vous voyez :
   - **Gauche** : Historique complet des messages
   - **Droite** : Informations du client (nom, téléphone, VIN, etc.)
2. Tapez votre message dans le champ de saisie (max 1600 caractères)
3. Cliquez sur le bouton d'envoi ou appuyez sur Entrée
4. Le message est envoyé instantanément au client via WhatsApp
5. La page se rafraîchit automatiquement pour afficher votre message

#### 3. Surveiller les nouveaux messages

- L'interface se rafraîchit automatiquement toutes les 5 secondes
- Les nouveaux messages du client apparaissent automatiquement
- Vous pouvez voir l'heure de chaque message
- Les messages sont différenciés par couleur :
  - **Blanc** : Messages du client
  - **Bleu** : Messages de l'agent ou du bot

#### 4. Clôturer une conversation

1. Une fois la demande du client traitée
2. Cliquez sur le bouton **"Clôturer"**
3. Le client reçoit un message de remerciement
4. Le statut passe à "Completed"
5. La durée de la conversation est calculée automatiquement

### Pour les administrateurs

#### Configuration initiale

1. **Vérifier les credentials Twilio** dans `.env` :
```env
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_WHATSAPP_NUMBER=+14155238886
```

2. **Importer le flow Twilio** :
   - Utiliser `twilio-flow-agent-mode.json`
   - Suivre les instructions dans `TWILIO_INTEGRATION_GUIDE.md`

3. **Configurer le sous-flow** :
   - Dans `twilio-flow-agent-mode.json`, ligne 169
   - Remplacer `FWd86ff8b300bff4355cbc57c7f5e44765` par votre Flow SID
   - Le Flow SID se trouve dans Twilio Studio

4. **Tester la configuration** :
```bash
# Test manuel via Tinker
php artisan tinker

$twilio = new \Twilio\Rest\Client(
    config('services.twilio.account_sid'),
    config('services.twilio.auth_token')
);

$message = $twilio->messages->create(
    "whatsapp:+212XXXXXXXXX",
    [
        'from' => 'whatsapp:' . config('services.twilio.whatsapp_number'),
        'body' => 'Test message',
    ]
);

echo $message->sid; // Devrait retourner un SID
```

## 📊 Structure de la base de données

### Table `conversations`

Nouvelles colonnes ajoutées :
- `agent_id` (unsignedBigInteger, nullable) : ID de l'agent qui a pris en charge
- Foreign key vers `users.id` avec `onDelete('set null')`

### Table `conversation_events`

Types d'événements pour le mode agent :
- `agent_takeover` : Prise en charge par un agent
- `agent_message` : Message envoyé par l'agent
- `conversation_closed` : Conversation clôturée par l'agent

## 🔐 Sécurité

### Authentification

- Toutes les routes du chat sont protégées par `auth` middleware
- Les agents doivent être connectés pour accéder au chat

### Autorisation

- Un agent ne peut envoyer des messages que s'il a pris en charge la conversation
- Vérification de `agent_id === auth()->id()` avant envoi
- Seul l'agent assigné peut clôturer la conversation

### Validation

- Messages limités à 1600 caractères (limite WhatsApp)
- Validation des inputs côté serveur
- Protection CSRF sur tous les formulaires

## 🐛 Dépannage

### Problème : Messages ne s'envoient pas

**Vérifications :**
1. Credentials Twilio corrects dans `.env`
2. Logs Laravel : `tail -f storage/logs/laravel.log`
3. Tester l'API Twilio manuellement (voir section Configuration)

### Problème : Auto-refresh ne fonctionne pas

**Solutions :**
1. Vérifier la console JavaScript du navigateur (F12)
2. Vérifier que la route `/dashboard/chat/{id}` est accessible
3. Désactiver les bloqueurs de pub qui peuvent bloquer le fetch

### Problème : Agent ne peut pas prendre en charge

**Causes possibles :**
1. Conversation déjà prise en charge par un autre agent
2. Statut de conversation incorrect
3. Vérifier dans la base de données : `SELECT * FROM conversations WHERE phone_number = '...'`

### Problème : Client ne reçoit pas les messages

**Vérifications :**
1. Numéro WhatsApp du client au bon format : `+212XXXXXXXXX`
2. Sandbox WhatsApp Twilio configuré correctement
3. Vérifier les logs Twilio : [Twilio Console - Logs](https://console.twilio.com/us1/monitor/logs/messages)

## 📈 Améliorations futures possibles

### Court terme
- [ ] Notifications en temps réel (WebSocket/Pusher)
- [ ] Typing indicator (indicateur de frappe)
- [ ] Envoi de fichiers/images
- [ ] Templates de réponses rapides

### Moyen terme
- [ ] Statistiques par agent (temps de réponse, nombre de conversations)
- [ ] Routing automatique des conversations aux agents
- [ ] File d'attente des conversations
- [ ] Notes internes sur les conversations

### Long terme
- [ ] Chatbot IA pour suggestions de réponses
- [ ] Analyse de sentiment des conversations
- [ ] Intégration CRM
- [ ] Application mobile pour les agents

## 📞 Support

Pour toute question ou problème :
1. Consulter `TWILIO_INTEGRATION_GUIDE.md`
2. Vérifier les logs : `storage/logs/laravel.log`
3. Consulter la [documentation Twilio](https://www.twilio.com/docs/whatsapp)

---

**Version:** 1.0.0
**Date:** 3 Décembre 2025
**Auteur:** Mercedes-Benz Bot Team
