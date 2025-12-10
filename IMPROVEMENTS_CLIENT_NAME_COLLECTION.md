# Améliorations - Collecte du Nom Client et Historique Complet

## Résumé des modifications

Ce document détaille les améliorations apportées au système de collecte des informations clients et à l'affichage de l'historique complet.

---

## 🎯 Problématique identifiée

**Problème** : Le `ProfileName` WhatsApp ne correspond pas toujours au vrai nom du client (pseudos, surnoms, etc.)

**Solution** : Séparation claire entre :
- **`whatsapp_profile_name`** : Nom du profil WhatsApp (automatique)
- **`client_full_name`** : Nom réel saisi manuellement par le client (utilisé dans l'app)

---

## 📋 Modifications effectuées

### 1. Base de données

#### Migration 1 : Table `clients`
**Fichier** : `database/migrations/2025_12_10_033414_add_client_full_name_to_clients_table.php`

```php
// Renommer nom_prenom → whatsapp_profile_name
$table->renameColumn('nom_prenom', 'whatsapp_profile_name');

// Ajouter le champ pour le vrai nom
$table->string('client_full_name')->nullable()
    ->comment('Nom complet réel du client (saisi manuellement)');

// Index pour recherche rapide
$table->index('client_full_name');
```

**Résultat** :
- `whatsapp_profile_name` : Nom WhatsApp (mis à jour automatiquement)
- `client_full_name` : Nom réel (demandé une seule fois, stocké définitivement)

#### Migration 2 : Table `conversations`
**Fichier** : `database/migrations/2025_12_10_033449_add_client_full_name_to_conversations_table.php`

```php
// Même structure que pour clients
$table->renameColumn('nom_prenom', 'whatsapp_profile_name');
$table->string('client_full_name')->nullable();
$table->index('client_full_name');
```

---

### 2. Modèles Eloquent

#### Modèle `Client`
**Fichier** : `app/Models/Client.php`

**Modifications** :
```php
protected $fillable = [
    'phone_number',
    'whatsapp_profile_name',    // Nouveau : profil WhatsApp
    'client_full_name',         // Nouveau : nom réel
    'email',
    // ...
];
```

**Nouvelles méthodes** :
```php
// Attribut calculé : nom à afficher (priorité au nom réel)
public function getDisplayNameAttribute(): string
{
    return $this->client_full_name ?? $this->whatsapp_profile_name ?? 'Client inconnu';
}

// Vérifie si le client a fourni son nom complet
public function hasFullName(): bool
{
    return !empty($this->client_full_name);
}

// Durée totale de connexion (nouvelle fonctionnalité)
public function getTotalDurationAttribute(): int
{
    return $this->conversations()
        ->whereNotNull('duration_seconds')
        ->sum('duration_seconds');
}

// Récupère tous les événements du client
public function getAllEvents()
{
    return ConversationEvent::whereIn(
        'conversation_id',
        $this->conversations()->pluck('id')
    )->orderBy('event_at', 'desc');
}
```

**Mise à jour de `updateFromConversation()`** :
```php
// Mise à jour du profil WhatsApp (toujours)
if ($conversation->whatsapp_profile_name) {
    $updates['whatsapp_profile_name'] = $conversation->whatsapp_profile_name;
}

// Mise à jour du nom complet (uniquement si pas déjà renseigné)
if ($conversation->client_full_name && !$this->client_full_name) {
    $updates['client_full_name'] = $conversation->client_full_name;
}
```

#### Modèle `Conversation`
**Fichier** : `app/Models/Conversation.php`

**Même structure** :
```php
protected $fillable = [
    'session_id',
    'phone_number',
    'whatsapp_profile_name',
    'client_full_name',
    // ...
];

// Même méthode getDisplayNameAttribute()
// Même méthode hasFullName()
```

---

### 3. Contrôleur Twilio Webhook

**Fichier** : `app/Http/Controllers/Api/TwilioWebhookController.php`

#### Méthode `handleIncomingMessage()`
```php
// Création de conversation
Conversation::create([
    'phone_number' => $phoneNumber,
    'session_id' => uniqid('session_', true),
    'whatsapp_profile_name' => $profileName ?? 'Client WhatsApp',  // Changé
    // ...
]);

// Mise à jour du profil WhatsApp (toujours à jour)
if ($profileName) {
    $updates['whatsapp_profile_name'] = $profileName;
}

// Synchronisation avec Client
if ($profileName) {
    $client->update(['whatsapp_profile_name' => $profileName]);
}

// Détection : client a un nom complet ?
$clientExists = $client->wasRecentlyCreated === false && $client->client_full_name !== null;
```

**Réponse JSON mise à jour** :
```php
return response()->json([
    'success' => true,
    'conversation_id' => $conversation->id,
    'client_full_name' => $client->client_full_name ?? $conversation->client_full_name,
    'whatsapp_profile_name' => $client->whatsapp_profile_name ?? $conversation->whatsapp_profile_name,
    'profile_name' => $profileName ?? $conversation->whatsapp_profile_name,
    'client_has_name' => $client->client_full_name !== null,  // Changé
    'client_status_known' => $client->is_client !== null,
    // ...
]);
```

#### Méthode `updateConversationData()`
```php
case 'collect_name':
    // Stocker le nom saisi manuellement dans client_full_name
    $conversation->update(['client_full_name' => $userInput]);

    // Synchroniser avec la table clients
    $client = \App\Models\Client::findOrCreateByPhone($conversation->phone_number);
    $client->update(['client_full_name' => $userInput]);
    break;

// Ajout de synchronisation pour tous les autres champs
case 'collect_email':
    $conversation->update(['email' => $userInput]);
    $client = \App\Models\Client::findOrCreateByPhone($conversation->phone_number);
    if (!$client->email) {
        $client->update(['email' => $userInput]);
    }
    break;

case 'check_client':
    $isClient = in_array($userInput, ['1', 'oui', 'yes']);
    $conversation->update(['is_client' => $isClient]);

    $client = \App\Models\Client::findOrCreateByPhone($conversation->phone_number);
    if ($client->is_client === null) {
        $client->update(['is_client' => $isClient]);
    }
    break;
```

---

### 4. Contrôleur Client Web

**Fichier** : `app/Http/Controllers/Web/ClientController.php`

#### Méthode `index()` - Recherche améliorée
```php
// Recherche dans les deux champs de nom
$query->where(function($q) use ($search) {
    $q->where('phone_number', 'like', "%{$search}%")
      ->orWhere('client_full_name', 'like', "%{$search}%")
      ->orWhere('whatsapp_profile_name', 'like', "%{$search}%")
      ->orWhere('email', 'like', "%{$search}%");
});
```

#### Méthode `show()` - Historique complet
```php
// Récupération des conversations avec agent
$conversations = Conversation::where('phone_number', $client->phone_number)
    ->with(['events' => function($query) {
        $query->orderBy('event_at', 'desc');
    }, 'agent'])
    ->orderBy('created_at', 'desc')
    ->paginate(10);

// NOUVEAU : Tous les événements du client
$allEvents = ConversationEvent::whereIn('conversation_id',
    Conversation::where('phone_number', $client->phone_number)->pluck('id')
)->orderBy('event_at', 'desc')->paginate(20, ['*'], 'events_page');

// Statistiques enrichies
$interactionStats = [
    'total_messages' => ...,
    'menu_choices' => ...,
    'agent_transfers' => ...,          // NOUVEAU
    'total_duration' => $client->total_duration,  // NOUVEAU
    'avg_duration' => ...,              // NOUVEAU
];

// NOUVEAU : Répartition des types d'événements
$eventBreakdown = ConversationEvent::whereIn('conversation_id', $conversationIds)
    ->selectRaw('event_type, count(*) as count')
    ->groupBy('event_type')
    ->pluck('count', 'event_type')
    ->toArray();

return view('dashboard.clients.show', compact(
    'client',
    'conversations',
    'interactionStats',
    'allEvents',           // NOUVEAU
    'eventBreakdown'       // NOUVEAU
));
```

#### Méthode `update()` - Validation mise à jour
```php
$validated = $request->validate([
    'client_full_name' => 'nullable|string|max:255',
    'whatsapp_profile_name' => 'nullable|string|max:255',
    'email' => 'nullable|email|max:255',
    'phone_number' => 'required|string|max:50',
    'is_client' => 'nullable|boolean',
    'vin' => 'nullable|string|max:50',
    'carte_vip' => 'nullable|string|max:50',
]);

// Log avec display_name
\App\Models\ActivityLog::log(
    'client_updated',
    "Client {$client->display_name} ({$client->phone_number}) a été mis à jour",
    $client,
    [...]
);
```

---

### 5. Vues (Blade Templates)

#### Vue `clients/show.blade.php`

**En-tête client amélioré** :
```blade
<div class="flex-shrink-0 w-16 h-16 rounded-full ...">
    {{ strtoupper(substr($client->display_name, 0, 1)) }}
</div>
<div class="ml-4">
    <h2 class="text-2xl font-bold text-gray-900">{{ $client->display_name }}</h2>
    <p class="text-sm text-gray-500">{{ $client->phone_number }}</p>

    @if($client->whatsapp_profile_name && $client->client_full_name)
    <p class="text-xs text-gray-400">Profil WhatsApp: {{ $client->whatsapp_profile_name }}</p>
    @endif

    @if($client->email)
    <p class="text-sm text-gray-500">{{ $client->email }}</p>
    @endif
</div>
```

**NOUVEAU : Section "Historique complet des événements"** :
```blade
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <h3>Historique complet des événements</h3>
    <span>{{ number_format($allEvents->total()) }} événements au total</span>

    <!-- Répartition par type d'événement -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($eventBreakdown as $type => $count)
        <div class="bg-gray-50 rounded-lg p-3">
            <p class="text-xs">{{ str_replace('_', ' ', $type) }}</p>
            <p class="text-2xl font-bold">{{ number_format($count) }}</p>
        </div>
        @endforeach
    </div>

    <!-- Timeline avec ligne verticale -->
    <div class="relative">
        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gray-200 ml-3"></div>

        @foreach($allEvents as $event)
        <div class="relative flex items-start pl-8">
            <!-- Point de couleur selon le type -->
            <div class="absolute left-0 w-6 h-6 rounded-full
                @if($event->event_type === 'message_received') bg-green-500
                @elseif($event->event_type === 'agent_message') bg-purple-500
                ...
                @endif">
                <svg class="w-3 h-3 text-white" ...>
            </div>

            <!-- Carte événement -->
            <div class="flex-1 bg-white border rounded-lg p-4">
                <span>{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</span>
                <span>{{ $event->event_at->format('d/m/Y H:i:s') }}</span>

                @if($event->user_input)
                <div>
                    <p><strong>Input:</strong></p>
                    <p class="bg-gray-50 rounded px-3 py-2">{{ $event->user_input }}</p>
                </div>
                @endif

                @if($event->bot_message)
                <div>
                    <p><strong>Réponse bot:</strong></p>
                    <p class="bg-blue-50 rounded px-3 py-2">{{ $event->bot_message }}</p>
                </div>
                @endif

                @if($event->metadata)
                <details>
                    <summary>Métadonnées</summary>
                    <pre>{{ json_encode($event->metadata, JSON_PRETTY_PRINT) }}</pre>
                </details>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    {{ $allEvents->links() }}
</div>
```

**NOUVEAU : Section "Statistiques de temps"** :
```blade
<div class="bg-white shadow rounded-lg p-6">
    <h3>Statistiques de temps</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="text-sm">Durée totale de connexion</p>
            <p class="text-3xl font-bold">
                {{ gmdate('H:i:s', $interactionStats['total_duration'] ?? 0) }}
            </p>
        </div>

        <div>
            <p class="text-sm">Durée moyenne par conversation</p>
            <p class="text-3xl font-bold">
                {{ gmdate('i:s', $interactionStats['avg_duration'] ?? 0) }}
            </p>
        </div>

        @if($interactionStats['agent_transfers'] > 0)
        <div>
            <p class="text-sm">Transferts vers agents</p>
            <p class="text-3xl font-bold text-orange-600">
                {{ $interactionStats['agent_transfers'] }}
            </p>
        </div>
        @endif
    </div>
</div>
```

---

## 🔄 Flux de collecte des informations

### Ancien flux
```
1. Message entrant
2. Stockage ProfileName dans nom_prenom
3. Si nom_prenom existe → skip demande nom
4. Affichage de nom_prenom partout dans l'app
```
**Problème** : nom_prenom contient souvent un pseudo WhatsApp

### Nouveau flux
```
1. Message entrant
2. Stockage ProfileName dans whatsapp_profile_name (automatique)
3. Vérification : client_full_name existe ?
   - OUI → skip demande nom (client connu)
   - NON → demander "Quels sont vos nom et prénom ?"
4. Stockage réponse dans client_full_name
5. Affichage de display_name dans l'app :
   - Priorité 1 : client_full_name (nom réel)
   - Priorité 2 : whatsapp_profile_name (fallback)
   - Priorité 3 : "Client inconnu"
```

---

## 📊 Structure de la base de données

### Table `clients`

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | bigint | ID auto-incrémenté |
| `phone_number` | varchar(255) UNIQUE | Numéro WhatsApp |
| `whatsapp_profile_name` | varchar(255) NULL | Nom du profil WhatsApp (automatique) |
| `client_full_name` | varchar(255) NULL | **Nom réel saisi par le client** |
| `email` | varchar(255) NULL | Email |
| `is_client` | boolean NULL | Client Mercedes ou non |
| `vin` | varchar(255) NULL | VIN du véhicule |
| `carte_vip` | varchar(255) NULL | Numéro carte VIP |
| `interaction_count` | int | Nombre total d'interactions |
| `conversation_count` | int | Nombre de conversations |
| `first_interaction_at` | timestamp NULL | Première interaction |
| `last_interaction_at` | timestamp NULL | Dernière interaction |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Index** :
- `phone_number` (unique)
- `client_full_name`
- `is_client`
- `last_interaction_at`

### Table `conversations`

Même structure :
- `whatsapp_profile_name` : Nom WhatsApp au moment de la conversation
- `client_full_name` : Nom réel si collecté pendant cette conversation

---

## 🎨 Nouvelles fonctionnalités de la page client

### 1. Affichage du nom
- **Nom principal** : `$client->display_name` (nom réel prioritaire)
- **Sous-titre** : Profil WhatsApp affiché si différent du nom réel

### 2. Historique complet des événements
- **Timeline visuelle** avec ligne verticale
- **Couleurs par type d'événement** :
  - 🟢 Vert : `message_received`
  - 🔵 Bleu : `message_sent`
  - 🟣 Violet : `agent_message`
  - 🟠 Orange : `agent_transfer`
  - 🔷 Indigo : `menu_choice`
  - 🔷 Cyan : `free_input`
  - ⚪ Gris : Autre

### 3. Répartition des événements
Cartes affichant le nombre d'événements par type :
- Message received : 45
- Menu choice : 23
- Free input : 18
- Agent transfer : 5
- ...

### 4. Statistiques de temps
- **Durée totale de connexion** : Somme de toutes les conversations
- **Durée moyenne** : Moyenne par conversation
- **Transferts vers agents** : Nombre de fois où un agent a pris le contrôle

### 5. Détails des événements
Pour chaque événement :
- Type et widget
- Date/heure précise
- Input utilisateur
- Réponse du bot
- Métadonnées (repliables)
- Temps de réponse (si disponible)

### 6. Pagination séparée
- Conversations : 10 par page
- Événements : 20 par page
- Navigation indépendante

---

## 🔧 Migration et mise à jour

### Commandes exécutées
```bash
# Installation de doctrine/dbal pour renameColumn
composer require doctrine/dbal

# Exécution des migrations
php artisan migrate
```

### Migrations créées
1. `2025_12_10_033414_add_client_full_name_to_clients_table.php`
2. `2025_12_10_033449_add_client_full_name_to_conversations_table.php`

### Compatibilité
✅ **Rétrocompatible** : Les anciennes données sont préservées
- `nom_prenom` → `whatsapp_profile_name`
- `client_full_name` commence à NULL
- Le système fonctionne même sans nom réel (fallback sur WhatsApp name)

### Migration des données existantes (optionnel)
```sql
-- Si vous voulez copier les anciens noms dans client_full_name
UPDATE clients
SET client_full_name = whatsapp_profile_name
WHERE client_full_name IS NULL
  AND whatsapp_profile_name IS NOT NULL
  AND whatsapp_profile_name != 'Client WhatsApp';
```

---

## 📝 Tests recommandés

### Test 1 : Nouveau client
1. Envoyer un message WhatsApp d'un nouveau numéro
2. Vérifier que le bot demande "Quels sont vos nom et prénom ?"
3. Saisir "Jean Dupont"
4. Vérifier dans le dashboard :
   - `client_full_name` = "Jean Dupont"
   - `whatsapp_profile_name` = ProfileName WhatsApp
   - Affichage = "Jean Dupont"

### Test 2 : Client existant
1. Envoyer un nouveau message du même numéro
2. Vérifier que le bot NE demande PAS le nom
3. Passer directement au menu principal

### Test 3 : Page détail client
1. Aller sur `/dashboard/clients/{id}`
2. Vérifier l'affichage de :
   - Nom réel en titre
   - Profil WhatsApp en sous-titre (si différent)
   - Timeline des événements avec couleurs
   - Répartition par type d'événement
   - Durée totale et moyenne
   - Pagination des événements

### Test 4 : Recherche
1. Aller sur `/dashboard/clients`
2. Rechercher par :
   - Nom réel (client_full_name)
   - Nom WhatsApp (whatsapp_profile_name)
   - Téléphone
3. Vérifier que tous les résultats apparaissent

---

## 🚀 Prochaines étapes possibles

### Améliorations suggérées

1. **Export de l'historique client**
   - Bouton "Exporter en PDF" sur la page client
   - Include timeline complète des événements

2. **Filtres sur la timeline**
   - Filtrer par type d'événement
   - Filtrer par plage de dates
   - Recherche dans les messages

3. **Graphiques de temps**
   - Graphique de la répartition des types d'événements
   - Courbe d'activité par jour/semaine

4. **Notifications**
   - Alerter quand un client VIP se connecte
   - Alerter après X jours d'inactivité

5. **Segments clients**
   - Créer des segments basés sur l'activité
   - Clients actifs vs inactifs
   - Clients ayant contacté un agent vs autonomes

---

## 📚 Documentation Twilio Flow

### Modifications à apporter au flow Twilio

Le flow doit maintenant vérifier `client_has_name` au lieu de vérifier juste le ProfileName :

```json
{
  "name": "check_client_exists",
  "type": "split-based-on",
  "conditions": [{
    "friendly_name": "Client Has Full Name",
    "arguments": ["{{widgets.api_incoming.parsed.client_has_name}}"],
    "type": "equal_to",
    "value": "true"
  }]
}
```

**Logique** :
- Si `client_has_name = true` → Client a déjà fourni son nom réel → Skip
- Si `client_has_name = false` → Nouveau client → Demander nom via `ask_name` widget

---

## ✅ Checklist de déploiement

- [x] Créer les migrations
- [x] Mettre à jour les modèles
- [x] Mettre à jour TwilioWebhookController
- [x] Mettre à jour ClientController
- [x] Mettre à jour les vues Blade
- [x] Installer doctrine/dbal
- [x] Exécuter les migrations
- [ ] Mettre à jour le Twilio Flow (si nécessaire)
- [ ] Tester avec de nouveaux clients
- [ ] Tester avec des clients existants
- [ ] Vérifier l'affichage dans le dashboard
- [ ] Tester la recherche
- [ ] Documenter pour l'équipe

---

## 🔐 Sécurité et confidentialité

### Données personnelles
- `client_full_name` contient des données personnelles
- Respecter le RGPD/lois locales sur la protection des données
- Ajouter une politique de conservation des données si nécessaire

### Recommandations
1. Informer les utilisateurs que leur nom sera stocké
2. Permettre la modification/suppression du nom
3. Anonymiser les anciennes conversations si requis

---

## 📊 Métriques à suivre

Après déploiement, suivre :
1. **Taux de complétion du nom** : % de clients ayant fourni leur nom réel
2. **Différence WhatsApp vs Réel** : % de clients dont le nom WhatsApp ≠ nom réel
3. **Utilisation de la timeline** : Nombre de vues de la page détail client
4. **Temps passé sur la page** : Engagement avec la timeline

---

**Date de mise en œuvre** : 10 décembre 2025
**Version** : 1.0
**Auteur** : Claude Sonnet 4.5
