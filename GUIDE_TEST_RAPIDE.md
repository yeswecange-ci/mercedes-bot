# 🧪 Guide de Test Rapide - Système Agent

**Objectif :** Vérifier que toutes les améliorations fonctionnent correctement

---

## 🚀 PRÉPARATION

### 1. Lancer le serveur
```bash
php artisan serve
```

### 2. Activer le scheduler (optionnel pour test stats)
```bash
# Dans un terminal séparé
php artisan schedule:work
```

### 3. Se connecter au dashboard
```
URL : http://localhost:8000
Email : admin@mercedes-bot.com
Password : password
```

---

## ✅ TEST 1 : Badge d'alerte conversations en attente

### Étape 1 : Créer une conversation en attente
```bash
php artisan tinker
```
```php
$conv = \App\Models\Conversation::create([
    'session_id' => 'test_' . uniqid(),
    'phone_number' => '+212600000001',
    'nom_prenom' => 'Test Client',
    'status' => 'transferred',
    'agent_id' => null,  // IMPORTANT : pas d'agent assigné
    'started_at' => now(),
    'transferred_at' => now(),
]);

\App\Models\ConversationEvent::create([
    'conversation_id' => $conv->id,
    'event_type' => 'agent_transfer',
    'bot_message' => 'Client demande à parler à un agent',
]);
```

### Étape 2 : Vérifier le badge
- ✅ Rafraîchir le dashboard (F5)
- ✅ Vérifier badge orange "1" sur "En attente agent" dans le menu
- ✅ Badge doit pulser (animation)

### Étape 3 : Ouvrir la vue
- ✅ Cliquer sur "En attente agent"
- ✅ Vérifier conversation "Test Client" affichée
- ✅ Vérifier bordure orange gauche
- ✅ Vérifier temps d'attente affiché
- ✅ Bouton "Prendre en charge maintenant" visible

**✓ TEST RÉUSSI SI** : Badge orange visible + conversation dans la liste

---

## ✅ TEST 2 : Prise en charge par agent

### Étape 1 : Prendre en charge
- ✅ Dans `/dashboard/pending`, cliquer "Prendre en charge maintenant"
- ✅ Redirection automatique vers interface chat
- ✅ Message de succès : "Vous avez pris en charge cette conversation"

### Étape 2 : Vérifier assignment
```bash
php artisan tinker
```
```php
$conv = \App\Models\Conversation::where('phone_number', '+212600000001')->first();
echo "Agent ID: " . $conv->agent_id . "\n";  // Doit afficher votre user ID
echo "Status: " . $conv->status . "\n";      // Doit être 'transferred'
```

### Étape 3 : Vérifier badge disparaît
- ✅ Retourner au dashboard
- ✅ Badge "En attente agent" doit être à "0" ou invisible
- ✅ Conversation ne doit plus apparaître dans `/dashboard/pending`

### Étape 4 : Tenter double prise en charge
- ✅ Se déconnecter
- ✅ Se connecter avec un autre compte agent
- ✅ Aller dans `/dashboard/conversations`
- ✅ Chercher conversation "Test Client"
- ✅ Cliquer "Détails" puis bouton "Prendre en charge"
- ✅ **Doit afficher erreur** : "déjà prise en charge par [nom]"

**✓ TEST RÉUSSI SI** : Agent assigné + erreur si autre agent tente

---

## ✅ TEST 3 : Interface Chat complète

### Étape 1 : Créer des messages de test
```bash
php artisan tinker
```
```php
$conv = \App\Models\Conversation::where('phone_number', '+212600000001')->first();

// Message du client
\App\Models\ConversationEvent::create([
    'conversation_id' => $conv->id,
    'event_type' => 'message_received',
    'user_input' => 'Bonjour, j\'ai une question sur mon véhicule',
    'created_at' => now()->subMinutes(5),
]);

// Message du bot
\App\Models\ConversationEvent::create([
    'conversation_id' => $conv->id,
    'event_type' => 'message_sent',
    'bot_message' => 'Bienvenue ! Comment puis-je vous aider ?',
    'created_at' => now()->subMinutes(4),
]);

// Message du client
\App\Models\ConversationEvent::create([
    'conversation_id' => $conv->id,
    'event_type' => 'message_received',
    'user_input' => 'Je veux parler à un agent humain',
    'created_at' => now()->subMinutes(3),
]);

// Événement agent takeover
\App\Models\ConversationEvent::create([
    'conversation_id' => $conv->id,
    'event_type' => 'agent_takeover',
    'bot_message' => 'Conversation prise en charge par Admin',
    'created_at' => now()->subMinutes(2),
]);

// Message de l'agent
\App\Models\ConversationEvent::create([
    'conversation_id' => $conv->id,
    'event_type' => 'agent_message',
    'bot_message' => 'Bonjour ! Je suis là pour vous aider.',
    'created_at' => now()->subMinute(),
]);

// Message du client
\App\Models\ConversationEvent::create([
    'conversation_id' => $conv->id,
    'event_type' => 'message_received',
    'user_input' => 'Merci ! Quels sont vos horaires ?',
    'created_at' => now(),
]);
```

### Étape 2 : Vérifier affichage
- ✅ Aller sur `/dashboard/chat/{id}` (remplacer {id} par l'ID de la conversation)
- ✅ Vérifier **6 éléments** affichés dans l'ordre chronologique :

1. **Message client** (gauche, blanc) : "Bonjour, j'ai une question..."
2. **Message bot** (droite, bleu, label "Bot") : "Bienvenue ! Comment..."
3. **Message client** (gauche, blanc) : "Je veux parler..."
4. **Événement système** (centre, bandeau bleu) : "Conversation prise en charge..."
5. **Message agent** (droite, bleu, label "Agent") : "Bonjour ! Je suis là..."
6. **Message client** (gauche, blanc) : "Merci ! Quels sont..."

### Étape 3 : Tester envoi message
- ✅ Zone de saisie visible en bas (si vous êtes l'agent assigné)
- ✅ Taper un message : "Nos horaires sont 9h-18h du lundi au vendredi"
- ✅ Cliquer bouton Envoyer
- ✅ Page se rafraîchit
- ✅ Votre message apparaît en bleu à droite avec label "(Agent)"

**✓ TEST RÉUSSI SI** : Tous les types de messages affichés correctement

---

## ✅ TEST 4 : Statistiques correctes

### Étape 1 : Calculer stats
```bash
php artisan stats:calculate --force
```

### Étape 2 : Vérifier dashboard principal
- ✅ Aller sur `/dashboard`
- ✅ Sélectionner période incluant vos conversations de test
- ✅ Vérifier :
  - **Total conversations** : Nombre correct
  - **Actives** : Nombre correct (status = 'active')
  - **Terminées** : Nombre correct (status = 'completed')
  - **Transférées** : Nombre correct (status = 'transferred')

### Étape 3 : Vérifier page statistiques
- ✅ Aller sur `/dashboard/statistics`
- ✅ Vérifier graphiques chargent
- ✅ Vérifier :
  - Graphique "Distribution des menus" : Données affichées
  - Graphique "Répartition par statut" : Vos conversations apparaissent
  - Graphique "Tendance quotidienne" : Courbe visible
  - Section "Parcours les plus populaires" : Au moins un parcours

**✓ TEST RÉUSSI SI** : Stats cohérentes et graphiques affichent données

---

## ✅ TEST 5 : Historique complet

### Étape 1 : Page détail conversation
- ✅ Aller sur `/dashboard/conversations`
- ✅ Cliquer "Détails" sur conversation "Test Client"
- ✅ Vérifier timeline complète affichée
- ✅ Tous événements visibles en ordre chronologique

### Étape 2 : Page clients
- ✅ Aller sur `/dashboard/clients`
- ✅ Cliquer "Synchroniser" en haut à droite
- ✅ Vérifier message succès
- ✅ Chercher client "+212600000001"
- ✅ Cliquer "Détails"
- ✅ Vérifier :
  - Toutes conversations du client listées
  - Stats interactions affichées
  - Historique complet

**✓ TEST RÉUSSI SI** : Historique accessible et complet partout

---

## ✅ TEST 6 : Clôture conversation

### Étape 1 : Clôturer
- ✅ Aller sur chat de la conversation test
- ✅ Cliquer bouton "Clôturer" en haut
- ✅ Vérifier redirection vers `/dashboard/conversations`
- ✅ Message succès : "Conversation clôturée avec succès"

### Étape 2 : Vérifier status
```bash
php artisan tinker
```
```php
$conv = \App\Models\Conversation::where('phone_number', '+212600000001')->first();
echo "Status: " . $conv->status . "\n";           // Doit être 'completed'
echo "Ended at: " . $conv->ended_at . "\n";       // Doit avoir date/heure
echo "Duration: " . $conv->duration_seconds . "s\n";  // Doit avoir durée
```

### Étape 3 : Vérifier événement
```php
$event = \App\Models\ConversationEvent::where('conversation_id', $conv->id)
    ->where('event_type', 'conversation_closed')
    ->first();
echo "Event: " . $event->bot_message . "\n";  // Doit contenir nom agent
```

### Étape 4 : Vérifier dans dashboard
- ✅ Conversation apparaît dans "Terminées"
- ✅ Badge "Terminée" visible
- ✅ Ne peut plus être prise en charge

**✓ TEST RÉUSSI SI** : Conversation clôturée avec durée calculée

---

## 🎯 RÉSUMÉ DES TESTS

| Test | Fonctionnalité | Statut |
|------|----------------|--------|
| 1 | Badge alerte en attente | ☐ |
| 2 | Prise en charge agent | ☐ |
| 3 | Interface chat complète | ☐ |
| 4 | Statistiques correctes | ☐ |
| 5 | Historique complet | ☐ |
| 6 | Clôture conversation | ☐ |

---

## 🧹 NETTOYAGE APRÈS TESTS

### Supprimer conversations de test
```bash
php artisan tinker
```
```php
\App\Models\Conversation::where('phone_number', 'LIKE', '+212600000%')->delete();
// Les événements seront supprimés automatiquement (cascade)
```

### Recalculer stats propres
```bash
php artisan stats:calculate --force
```

---

## 🆘 DÉPANNAGE

### Problème : Badge ne s'affiche pas
**Solution :**
```bash
php artisan cache:clear
php artisan view:clear
```

### Problème : Stats vides
**Solution :**
```bash
php artisan stats:calculate --from=2025-01-01
```

### Problème : Erreur 500 lors prise en charge
**Vérifier :**
- `storage/logs/laravel.log`
- User authentifié correctement
- Conversation existe bien

### Problème : Messages n'apparaissent pas dans chat
**Vérifier :**
- Auto-refresh activé (attendre 5s)
- Événements bien créés en base de données
- Types d'événements corrects

---

## ✅ VALIDATION FINALE

**Tous les tests passent ?**

✅ OUI → Système opérationnel, prêt pour production
❌ NON → Consulter `AMELIORATIONS_AGENT_SYSTEM.md` et logs

---

**Temps estimé :** 15-20 minutes
**Niveau :** Débutant OK (étapes détaillées)
