# 🔍 ANALYSE TWILIO FLOW - Conformité avec l'Application

**Date:** 2025-12-09
**Flow Version:** Mercedes-Benz by CFAO - WhatsApp Bot v3.2
**Statut:** ✅ Majoritairement conforme avec recommandations d'amélioration

---

## ✅ POINTS DE CONFORMITÉ

### 1. **Webhook Incoming Message** ✅ CONFORME

**État Flow:**
```json
{
  "name": "api_incoming",
  "url": "https://mbbot-dashboard.ywcdigital.com/api/twilio/incoming",
  "method": "POST",
  "body": {
    "From": "{{trigger.message.From}}",
    "Body": "{{trigger.message.Body}}",
    "MessageSid": "{{trigger.message.MessageSid}}",
    "ProfileName": "{{trigger.message.ProfileName}}",
    "NumMedia": "{{trigger.message.NumMedia}}",
    "MediaUrl0": "{{trigger.message.MediaUrl0}}",
    "MediaContentType0": "{{trigger.message.MediaContentType0}}"
  }
}
```

**Correspondance Application:**
- Route: `POST /api/twilio/incoming` ✅
- Controller: `TwilioWebhookController@handleIncomingMessage` ✅
- Validation: From, Body, MessageSid, ProfileName, NumMedia ✅
- Retourne: `agent_mode`, `conversation_id`, `session_id`, etc. ✅

**Verdict:** ✅ Parfaitement conforme

---

### 2. **Check Agent Mode** ✅ CONFORME avec amélioration possible

**État Flow:**
```json
{
  "name": "check_agent_mode",
  "condition": "{{widgets.api_incoming.parsed.agent_mode}} == true",
  "if_true": "end_flow_agent",
  "if_false": "send_message_welcome"
}
```

**Message envoyé si agent_mode = true:**
```
"Votre conversation est en cours avec un agent. Merci de patienter."
```

**Correspondance Application:**
- Webhook retourne `agent_mode: true` si `status = 'transferred' AND agent_id != null` ✅
- Flow arrête le bot automatique ✅
- L'agent reçoit le message via auto-refresh ✅

**⚠️ AMÉLIORATION RECOMMANDÉE:**

Le message "Votre conversation est en cours avec un agent. Merci de patienter." est **INUTILE** car :
1. L'agent a déjà pris en charge et envoyé un message de bienvenue lors du takeover
2. Chaque message du client provoque ce message générique
3. L'agent va répondre directement, donc le client recevra deux messages

**Solution proposée:**
- Option A: Ne rien envoyer (juste terminer le flow en silence)
- Option B: Envoyer ce message SEULEMENT si aucun message agent dans les 5 dernières minutes
- Option C: Vérifier dans le webhook si c'est le premier message après takeover

---

### 3. **Menu Choices** ✅ CONFORME

**États Flow utilisant menu-choice:**
- `api_menu_vn` → vehicules_neufs
- `api_menu_sav` → sav
- `api_menu_reclamation` → reclamation
- `api_menu_vip` → club_vip
- `api_menu_agent` → agent_direct
- `api_vn_catalogue` → vn_catalogue
- `api_vn_essai` → vn_essai
- `api_vn_conseiller` → vn_conseiller
- `api_vn_garantie` → vn_garantie
- `api_sav_entretien` → sav_entretien
- `api_sav_reparation` → sav_reparation
- `api_sav_pieces` → sav_pieces
- `api_vip_fonctionnement` → vip_fonctionnement

**Format d'appel:**
```json
{
  "url": "https://mbbot-dashboard.ywcdigital.com/api/twilio/menu-choice",
  "body": {
    "conversation_id": "{{widgets.api_incoming.parsed.conversation_id}}",
    "menu_choice": "vehicules_neufs",
    "user_input": "1"
  }
}
```

**Correspondance Application:**
- Route: `POST /api/twilio/menu-choice` ✅
- Controller: `TwilioWebhookController@handleMenuChoice` ✅
- Enregistre événement `menu_choice` ✅
- Met à jour `current_menu` et `menu_path` ✅

**Mapping des stats (dans CalculateDailyStatistics):**
```php
'1' => 'menu_vehicules_neufs',   // ✅ VN
'2' => 'menu_sav',                // ✅ SAV
'3' => 'menu_reclamations',       // ✅ Réclamations
'4' => 'menu_club_vip',           // ✅ VIP
'5' => 'menu_agent',              // ✅ Agent
```

**Verdict:** ✅ Parfaitement conforme

---

### 4. **Free Inputs** ✅ CONFORME

**États Flow utilisant free-input:**
- `api_free_input_name` → widget: collect_name
- `api_free_input_client` → widget: check_client
- `api_free_input_client_returning` → widget: check_client
- `api_free_input_contact` → widget: collect_contact_commercial
- `api_free_input_reparation` → widget: collect_reparation
- `api_free_input_reclamation` → widget: collect_reclamation
- `api_free_input_agent` → widget: collect_agent_reason

**Format d'appel:**
```json
{
  "url": "https://mbbot-dashboard.ywcdigital.com/api/twilio/free-input",
  "body": {
    "conversation_id": "{{widgets.api_incoming.parsed.conversation_id}}",
    "user_input": "{{widgets.ask_name.inbound.Body}}",
    "widget_name": "collect_name"
  }
}
```

**Correspondance Application:**
- Route: `POST /api/twilio/free-input` ✅
- Controller: `TwilioWebhookController@handleFreeInput` ✅
- Enregistre événement `free_input` ✅
- Extraction données (nom, email, VIN, is_client) via `updateConversationData()` ✅

**Extraction automatique:**
- **collect_name**: Extrait nom → `nom_prenom`
- **check_client**: Extrait statut (1=Oui, 2=Non) → `is_client`
- **collect_contact_commercial**: Extrait email
- **collect_reparation**: Extrait VIN
- **collect_reclamation**: Extrait infos réclamation

**Verdict:** ✅ Parfaitement conforme

---

### 5. **Agent Transfers** ✅ CONFORME

**États Flow demandant transfert agent:**

| État | Raison | Contexte |
|------|--------|----------|
| `api_agent_transfer_commercial` | "Demande contact conseiller commercial" | Véhicules neufs - Conseiller |
| `api_agent_transfer_reparation` | "Demande réparation" | SAV - Réparation |
| `api_agent_transfer_sav` | "Demande agent SAV" | SAV - Agent direct |
| `api_agent_transfer_reclamation` | "Réclamation client" | Réclamations |
| `api_agent_transfer_vip` | "Demande conseiller VIP" | Club VIP |
| `api_agent_transfer_direct` | "Demande agent direct" | Menu principal - Option 5 |

**Format d'appel:**
```json
{
  "url": "https://mbbot-dashboard.ywcdigital.com/api/twilio/agent-transfer",
  "body": {
    "conversation_id": "{{widgets.api_incoming.parsed.conversation_id}}",
    "reason": "Demande agent SAV"
  }
}
```

**Correspondance Application:**
- Route: `POST /api/twilio/agent-transfer` ✅
- Controller: `TwilioWebhookController@handleAgentTransfer` ✅
- Change status → `transferred` ✅
- Définit `transferred_at` ✅
- Enregistre événement `agent_transfer` avec `reason` en metadata ✅
- **IMPORTANT:** N'assigne PAS `agent_id` (c'est normal !) ✅

**Après transfer:**
- Conversation apparaît dans `/dashboard/pending` ✅
- Badge orange visible dans menu ✅
- Agent peut prendre en charge ✅

**Verdict:** ✅ Parfaitement conforme

---

### 6. **Complete Conversation** ✅ CONFORME

**États Flow terminant conversation:**
- `api_complete` → Quitter normal
- `api_complete_timeout` → Timeout inactivité

**Format d'appel:**
```json
{
  "url": "https://mbbot-dashboard.ywcdigital.com/api/twilio/complete",
  "body": {
    "conversation_id": "{{widgets.api_incoming.parsed.conversation_id}}"
  }
}
```

**Correspondance Application:**
- Route: `POST /api/twilio/complete` ✅
- Controller: `TwilioWebhookController@completeConversation` ✅
- Change status → `completed` ✅
- Définit `ended_at` ✅
- Calcule `duration_seconds` ✅

**Verdict:** ✅ Parfaitement conforme

---

## ⚠️ PROBLÈMES IDENTIFIÉS

### 🔴 PROBLÈME 1: Message redondant en mode agent

**État actuel:**
```
check_agent_mode → end_flow_agent
Message: "Votre conversation est en cours avec un agent. Merci de patienter."
```

**Problème:**
- Chaque message du client provoque ce message automatique
- L'agent a déjà envoyé un message de bienvenue lors du takeover :
  ```
  "Vous êtes maintenant en contact avec un agent Mercedes-Benz. Comment puis-je vous aider ?"
  ```
- Le client reçoit donc un message générique à chaque fois qu'il écrit alors que l'agent va répondre

**Impact utilisateur:**
```
Client: "Bonjour, j'ai une question"
Bot: "Votre conversation est en cours avec un agent. Merci de patienter."
[2 secondes plus tard]
Agent: "Bonjour ! Quelle est votre question ?"
→ Expérience confuse pour le client
```

**Solution recommandée:**

**Option A (Recommandée) : Ne rien envoyer**
```json
{
  "name": "end_flow_agent",
  "type": "send-message",
  "body": ""  // Message vide ou supprimer cette étape
}
```

**Option B : Vérifier si premier message après takeover**
Modifier le webhook pour retourner `first_message_after_takeover: true/false` et envoyer le message uniquement dans ce cas.

---

### 🟡 PROBLÈME 2: Pas de distinction "En attente" vs "En cours avec agent"

**État actuel:**
- `agent_mode = true` si `status = transferred AND agent_id != null`
- `agent_mode = false` sinon

**Scénario problématique:**
1. Client demande agent (option 5)
2. `api_agent_transfer` est appelé
3. Status → `transferred`, agent_id = `null`
4. Message: "Un agent vous contactera bientôt"
5. **Client envoie un autre message**
6. Webhook retourne `agent_mode = false` (car agent_id est null)
7. **Bot continue le flow automatique !**

**Impact:**
```
Client: "Je veux parler à un agent"
Bot: "Un agent vous contactera bientôt"
Client: "Ok merci"
Bot: "Comment puis-je vous aider ? 1️⃣ VN 2️⃣ SAV..." ❌ MAUVAIS !
```

**Solution recommandée:**

**Modifier le webhook `handleIncomingMessage` :**

```php
// Dans TwilioWebhookController.php ligne 121
$isAgentMode = $conversation->status === 'transferred' && $conversation->agent_id !== null;
$isPendingAgent = $conversation->status === 'transferred' && $conversation->agent_id === null;

return response()->json([
    // ... autres champs
    'agent_mode' => $isAgentMode,
    'pending_agent' => $isPendingAgent,  // NOUVEAU
]);
```

**Modifier le Flow Twilio :**

Ajouter un check supplémentaire après `check_agent_mode` :

```json
{
  "name": "check_pending_agent",
  "type": "split-based-on",
  "input": "{{widgets.api_incoming.parsed.pending_agent}}",
  "conditions": [
    {
      "if": "pending_agent == true",
      "next": "end_flow_pending"
    }
  ],
  "noMatch": "send_message_welcome"
}
```

```json
{
  "name": "end_flow_pending",
  "type": "send-message",
  "body": "Votre demande a été transmise à notre équipe. Un agent vous contactera très bientôt. Merci de votre patience."
}
```

---

### 🟢 PROBLÈME 3 (Mineur): Timeout trop long

**État actuel:**
- Tous les `send-and-wait-for-reply` ont `timeout: 3600` (1 heure)

**Problème:**
- Si le client abandonne, la conversation reste `active` pendant 1h
- Stats faussées (conversations actives surestimées)

**Solution recommandée:**
- Réduire à 600 secondes (10 minutes) pour les questions simples
- Garder 3600 pour les saisies complexes (description réclamation, coordonnées)

---

## 📊 MAPPING DES STATISTIQUES

### Menu Principal (user_input 1-5)

| user_input | menu_choice | Compteur stats | Flow State |
|------------|-------------|----------------|------------|
| 1 | vehicules_neufs | menu_vehicules_neufs | ✅ |
| 2 | sav | menu_sav | ✅ |
| 3 | reclamation | menu_reclamations | ✅ |
| 4 | club_vip | menu_club_vip | ✅ |
| 5 | agent_direct | menu_agent | ✅ |

**Calcul dans `CalculateDailyStatistics::calculateMenuStats()` :**
```php
$mapping = [
    '1' => 'menu_vehicules_neufs',
    '2' => 'menu_sav',
    '3' => 'menu_reclamations',
    '4' => 'menu_club_vip',
    '5' => 'menu_agent',
];
```

**Verdict:** ✅ Mapping correct

---

## 🔄 FLUX COMPLET DE BOUT EN BOUT

### Scénario 1 : Client demande agent (Option 5)

```mermaid
1. Client WhatsApp: "5"
2. Trigger → api_incoming
3. POST /api/twilio/incoming
4. Webhook crée conversation, retourne agent_mode=false
5. check_agent_mode → noMatch (false)
6. send_message_welcome
7. menu_principal (affiche menu)
8. Client: "5"
9. split_menu_principal → match option 5
10. api_menu_agent
11. POST /api/twilio/menu-choice (menu_choice="agent_direct", user_input="5")
12. Webhook enregistre événement menu_choice ✅
13. ask_agent_reason
14. Client: "Je veux des infos sur un véhicule"
15. api_free_input_agent
16. POST /api/twilio/free-input (widget="collect_agent_reason")
17. Webhook enregistre événement free_input ✅
18. api_agent_transfer_direct
19. POST /api/twilio/agent-transfer (reason="Demande agent direct")
20. Webhook: status="transferred", agent_id=null, transferred_at=now ✅
21. Webhook enregistre événement agent_transfer ✅
22. send_confirmation_agent: "Un agent vous contactera..."
23. menu_fin_agent

---

24. [5 minutes plus tard]
25. Client: "Vous êtes là ?"
26. Trigger → api_incoming
27. POST /api/twilio/incoming
28. ⚠️ PROBLÈME: agent_mode=false car agent_id=null
29. check_agent_mode → noMatch
30. ❌ Bot continue le flow normal au lieu d'attendre l'agent
```

**Résultat:** ❌ Comportement incorrect

---

### Scénario 2 : Agent prend en charge puis client répond

```mermaid
1. [Suite du scénario 1]
2. Agent voit badge orange dans /dashboard/pending ✅
3. Agent clique "Prendre en charge" ✅
4. POST /dashboard/chat/{id}/take-over
5. Conversation: status="transferred", agent_id=2 ✅
6. Événement agent_takeover créé ✅
7. Message WhatsApp envoyé au client: "Vous êtes en contact avec un agent" ✅

---

8. Client: "Bonjour, je veux des infos sur la Classe E"
9. Trigger → api_incoming
10. POST /api/twilio/incoming
11. Webhook: agent_mode=true (car agent_id=2) ✅
12. Événement message_received créé ✅
13. check_agent_mode → match (true)
14. end_flow_agent
15. Message: "Votre conversation est en cours avec un agent..." ⚠️
16. Flow termine

---

17. Dashboard agent: Auto-refresh (5s) ✅
18. Message client affiché dans chat ✅
19. Agent répond: "Bonjour ! Voici les infos..."
20. POST /dashboard/chat/{id}/send
21. Message envoyé via Twilio SDK ✅
22. Événement agent_message créé ✅
23. Client reçoit message WhatsApp ✅
```

**Résultat:** ✅ Fonctionne mais message "en cours avec agent" redondant

---

## ✅ RECOMMANDATIONS PRIORITAIRES

### 🔴 PRIORITÉ HAUTE

#### 1. Supprimer le message redondant en mode agent

**Fichier Flow à modifier:** `end_flow_agent`

**Avant:**
```json
{
  "name": "end_flow_agent",
  "type": "send-message",
  "body": "Votre conversation est en cours avec un agent. Merci de patienter."
}
```

**Après:**
```json
{
  "name": "end_flow_agent",
  "type": "send-message",
  "body": ""
}
```

Ou mieux, supprimer complètement cette étape et terminer directement le flow.

---

#### 2. Ajouter gestion "En attente d'agent"

**A. Modifier le webhook**

**Fichier:** `app/Http/Controllers/Api/TwilioWebhookController.php`

**Ligne 121, remplacer:**
```php
$isAgentMode = $conversation->status === 'transferred' && $conversation->agent_id !== null;

return response()->json([
    // ...
    'agent_mode' => $isAgentMode,
]);
```

**Par:**
```php
$isAgentMode = $conversation->status === 'transferred' && $conversation->agent_id !== null;
$isPendingAgent = $conversation->status === 'transferred' && $conversation->agent_id === null;

return response()->json([
    // ...
    'agent_mode' => $isAgentMode,
    'pending_agent' => $isPendingAgent,  // NOUVEAU CHAMP
]);
```

**B. Modifier le Flow Twilio**

Ajouter un nouvel état après `check_agent_mode` :

```json
{
  "name": "check_pending_agent",
  "type": "split-based-on",
  "transitions": [
    {
      "next": "send_message_welcome",
      "event": "noMatch"
    },
    {
      "next": "end_flow_pending",
      "event": "match",
      "conditions": [
        {
          "friendly_name": "Pending Agent",
          "arguments": ["{{widgets.api_incoming.parsed.pending_agent}}"],
          "type": "equal_to",
          "value": "true"
        }
      ]
    }
  ],
  "properties": {
    "input": "{{widgets.api_incoming.parsed.pending_agent}}"
  }
}
```

```json
{
  "name": "end_flow_pending",
  "type": "send-message",
  "properties": {
    "service": "{{trigger.message.InstanceSid}}",
    "channel": "{{trigger.message.ChannelSid}}",
    "from": "{{flow.channel.address}}",
    "to": "{{contact.channel.address}}",
    "body": "Votre demande a été transmise à notre équipe. Un agent vous contactera très bientôt. ⏱️"
  },
  "transitions": [
    {"event": "sent"},
    {"event": "failed"}
  ]
}
```

---

### 🟡 PRIORITÉ MOYENNE

#### 3. Réduire les timeouts

Modifier tous les `send-and-wait-for-reply` :
- Questions simples (menus) : `600` secondes (10 min)
- Saisies complexes : `1800` secondes (30 min)

---

### 🟢 PRIORITÉ BASSE

#### 4. Ajouter logs détaillés

Dans chaque appel API du Flow, ajouter un état de logging en cas d'erreur pour faciliter le debugging.

---

## 📋 CHECKLIST DE CONFORMITÉ

| Élément | Conforme | Notes |
|---------|----------|-------|
| Webhook incoming | ✅ | Parfait |
| Check agent_mode | ⚠️ | Fonctionne mais message redondant |
| Menu choices | ✅ | Tous les menus mappés correctement |
| Free inputs | ✅ | Extraction données OK |
| Agent transfers | ⚠️ | Manque gestion "en attente" |
| Complete conversation | ✅ | Parfait |
| Mapping stats | ✅ | Correspondance 1-1 |
| Timeouts | ⚠️ | Trop longs (3600s) |

---

## 🎯 RÉSUMÉ EXÉCUTIF

### ✅ Points Forts
1. Architecture webhook bien conçue
2. Tous les endpoints correspondent
3. Extraction de données automatique fonctionne
4. Statistiques bien mappées
5. Agent transfer crée bien les événements

### ⚠️ Points d'Amélioration
1. **Urgent:** Supprimer message redondant en mode agent
2. **Important:** Gérer statut "en attente d'agent" (pending_agent)
3. **Recommandé:** Réduire timeouts

### 🚀 Impact des Améliorations

**Avant améliorations:**
- Message "en cours avec agent" à chaque fois que client écrit
- Client en attente d'agent peut recevoir réponses bot
- Conversations restent actives 1h même si client parti

**Après améliorations:**
- Communication fluide client-agent
- Client en attente ne reçoit que message d'attente
- Statistiques plus précises

---

**Prochaines étapes recommandées:**
1. Implémenter les modifications webhook (5 min)
2. Modifier le Flow Twilio (10 min)
3. Tester avec conversation de test (5 min)
4. Déployer en production

**Temps total estimé:** 20 minutes
