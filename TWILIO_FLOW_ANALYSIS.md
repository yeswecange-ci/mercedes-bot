# Analyse du Twilio Flow - Mercedes-Benz WhatsApp Bot v3.2

## Vue d'ensemble

**Nom**: Mercedes-Benz by CFAO - WhatsApp Bot v3.2
**Type**: Twilio Studio Flow avec vérification client optimisée
**États totaux**: 107 widgets
**Déclencheurs**: Messages WhatsApp entrants, appels, conversations

---

## Architecture du Flow

### 1. Point d'entrée (Trigger)

```
Trigger → api_incoming → check_agent_mode
```

**Événements déclencheurs**:
- `incomingMessage` - Message WhatsApp entrant
- `incomingCall` - Appel entrant
- `incomingConversationMessage` - Message de conversation
- `incomingRequest` - Requête générique
- `incomingParent` - Événement parent

**Tous convergent vers**: `api_incoming` (sauf incomingMessage/incomingCall qui ne font rien)

---

### 2. Traitement initial des messages

#### Widget: `api_incoming`
**Type**: `make-http-request`
**URL**: `https://mbbot-dashboard.ywcdigital.com/api/twilio/incoming`
**Méthode**: POST

**Données envoyées**:
```json
{
  "From": "{{trigger.message.From}}",
  "Body": "{{trigger.message.Body}}",
  "MessageSid": "{{trigger.message.MessageSid}}",
  "ProfileName": "{{trigger.message.ProfileName}}",
  "NumMedia": "{{trigger.message.NumMedia}}",
  "MediaUrl0": "{{trigger.message.MediaUrl0}}",
  "MediaContentType0": "{{trigger.message.MediaContentType0}}"
}
```

**Réponse attendue** (depuis `TwilioWebhookController::handleIncomingMessage()`):
```json
{
  "conversation_id": 123,
  "profile_name": "Jean Dupont",
  "client_has_name": true/false,
  "client_status_known": true/false,
  "agent_mode": true/false
}
```

**Transitions**:
- `success` → `check_agent_mode`
- `failed` → `send_error_message`

---

### 3. Détection du mode agent

#### Widget: `check_agent_mode`
**Type**: `split-based-on`
**Condition**: `{{widgets.api_incoming.parsed.agent_mode}} == "true"`

**Logique**:
- Si `agent_mode = true` → **Conversation déjà en cours avec un agent**
  - Envoie: "Votre conversation est en cours avec un agent. Merci de patienter."
  - Termine le flow (`end_flow_agent`)
  - **Le backend Laravel gère les messages suivants directement**

- Si `agent_mode = false` → **Mode bot automatique**
  - Continue vers `send_message_welcome`

**Important**: Cette vérification empêche le bot de répondre quand un agent humain a pris le contrôle.

---

### 4. Collecte des informations client

#### Étape 1: Message de bienvenue
```
send_message_welcome → delay_welcome → check_client_exists
```

**Message**:
```
Bonjour {{widgets.api_incoming.parsed.profile_name}} et bienvenue sur la chaîne WhatsApp MERCEDES-BENZ by CFAO 👋
Je suis votre assistant virtuel.
```

**Délai**: Fonction Twilio `add_delay` pour éviter les messages trop rapides

#### Étape 2: Vérification client existant

**Widget**: `check_client_exists`
**Condition**: `{{widgets.api_incoming.parsed.client_has_name}} == "true"`

**Branches**:

##### Si client_has_name = FALSE (nouveau client)
```
ask_name → api_free_input_name → ask_is_client → split_is_client → api_free_input_client → menu_principal
```

1. **ask_name**: "Quels sont vos nom et prénom ?"
2. **api_free_input_name**:
   - URL: `/api/twilio/free-input`
   - Payload: `{"conversation_id", "user_input", "widget_name": "collect_name"}`
3. **ask_is_client**: "Êtes-vous déjà client(e) Mercedes-Benz ? 1️⃣ Oui 2️⃣ Non"
4. **split_is_client**: Validation de la réponse (1 ou 2)
   - Invalid → `invalid_is_client` → Reboucle sur `ask_is_client`
5. **api_free_input_client**: Enregistre le statut client
   - Payload: `{"widget_name": "check_client"}`

##### Si client_has_name = TRUE (client connu)
```
check_client_status_known
```

**Condition**: `{{widgets.api_incoming.parsed.client_status_known}} == "true"`

- Si TRUE → **Passe directement au menu principal** (gain de temps)
- Si FALSE → `ask_is_client_returning` → Demande si client Mercedes-Benz

---

### 5. Menu principal

#### Widget: `menu_principal`
**Type**: `send-and-wait-for-reply`
**Timeout**: 3600 secondes (1 heure)

**Options**:
```
1️⃣ Véhicules neufs
2️⃣ Service après-vente
3️⃣ Réclamations
4️⃣ Club VIP Mercedes-Benz
5️⃣ Parler à un agent
```

**Traitement**: `split_menu_principal` → Dispatch vers sous-menus

**Appels API**: Chaque choix log l'action via `/api/twilio/menu-choice`:
```json
{
  "conversation_id": 123,
  "menu_choice": "vehicules_neufs" | "sav" | "reclamation" | "club_vip" | "agent_direct",
  "user_input": "1" | "2" | "3" | "4" | "5"
}
```

---

## 6. Sous-menus détaillés

### Menu 1: Véhicules Neufs (VN)

**Flow**:
```
api_menu_vn → menu_vn → split_menu_vn
```

**Options**:
```
1️⃣ Notre catalogue
2️⃣ Essai de conduite
3️⃣ Être contacté par un conseiller
4️⃣ Garantie constructeur
5️⃣ Retour au menu principal
```

#### Option 1: Catalogue
```
api_vn_catalogue → send_catalogue (PDF) → delay → delay → menu_fin_vn
```

**Média envoyé**:
- URL: `https://mercedes-9755.twil.io/G_Klasse_W465_ePaper_24_1_02_ENG.pdf`
- Type: Brochure véhicule Mercedes-Benz

**Délais multiples**: 2x `add_delay` pour éviter l'envoi trop rapide après le PDF

#### Option 2: Essai de conduite
```
api_vn_essai → send_essai → menu_fin_vn
```

**Message**:
```
Pour réserver votre essai, cliquez sur le lien ci-dessous :
https://www.mercedes-benz-rci.com/fr/concession?form=reserver-un-essai
```

#### Option 3: Contact conseiller
```
api_vn_conseiller → ask_contact_info → api_free_input_contact → api_agent_transfer_commercial → send_confirmation_conseiller → menu_fin_vn
```

**Collecte**:
```
Veuillez saisir vos coordonnées :
- Nom et prénom
- Numéro de téléphone
- Adresse e-mail
```

**Traitement**:
1. `api_free_input_contact` → Widget: `collect_contact_commercial`
2. `api_agent_transfer_commercial` → URL: `/api/twilio/agent-transfer`
   ```json
   {
     "conversation_id": 123,
     "reason": "Demande contact conseiller commercial"
   }
   ```
3. Confirmation: "Un conseiller commercial vous contactera dans les 24h (jours ouvrables)"

**Backend Laravel**:
- `status` → `transferred`
- `agent_id` → NULL (en attente)
- Apparaît dans la queue "Conversations en attente" du dashboard

#### Option 4: Garantie
```
api_vn_garantie → send_garantie → menu_fin_vn
```

**Message**:
```
La garantie constructeur Mercedes-Benz couvre les défauts de fabrication pendant 2 ans à compter de la date de réception de votre véhicule.
```

#### Menu de fin VN
**Options**:
```
1️⃣ Retour menu Véhicules neufs
2️⃣ Retour menu principal
3️⃣ Quitter
```

**Logique**:
- 1 → Reboucle sur `menu_vn`
- 2 → Retour à `menu_principal`
- 3 → `api_complete` → `send_goodbye` → FIN

---

### Menu 2: Service Après-Vente (SAV)

**Flow**:
```
api_menu_sav → menu_sav → split_menu_sav
```

**Options**:
```
1️⃣ Entretien & maintenance
2️⃣ Réparation
3️⃣ Pièces d'origine
4️⃣ Parler à un agent SAV
5️⃣ Retour menu principal
```

#### Option 1: Entretien
```
api_sav_entretien → send_entretien (PDF) → delay → delay → menu_fin_sav
```

**Média**:
- URL: `https://merco-9614.twil.io/Forfaits%20entretien%20Mercedes-benz%20by%20CFAO.pdf`
- Contenu: Forfaits d'entretien Mercedes-Benz

#### Option 2: Réparation
```
api_sav_reparation → ask_reparation_info → api_free_input_reparation → api_agent_transfer_reparation → send_confirmation_reparation → menu_fin_sav
```

**Collecte**:
```
Décrivez-nous le problème avec votre véhicule :
- Modèle du véhicule
- VIN (si possible)
- Description du problème
```

**Traitement**:
- Widget: `collect_reparation`
- Transfer reason: "Demande réparation"
- Confirmation: "Un technicien vous contactera rapidement"

#### Option 3: Pièces d'origine
```
api_sav_pieces → send_pieces → menu_fin_sav
```

**Message**:
```
Nos pièces d'origine Mercedes-Benz garantissent qualité et sécurité.

📞 Contactez notre service pièces : 07 01 52 52 52
```

#### Option 4: Agent SAV
```
api_agent_transfer_sav → send_confirmation_agent_sav → menu_fin_sav
```

**Transfer direct** sans collecte d'infos supplémentaires:
- Reason: "Demande agent SAV"
- Confirmation: "Un conseiller SAV vous contactera dans les plus brefs délais"

---

### Menu 3: Réclamations

**Flow simplifié** (pas de sous-menu):
```
api_menu_reclamation → ask_reclamation → api_free_input_reclamation → api_agent_transfer_reclamation → send_confirmation_reclamation → menu_fin_reclamation
```

**Collecte**:
```
Nous sommes désolés d'apprendre que vous rencontrez un problème.

Veuillez décrire votre réclamation :
- Nom et prénom
- Numéro VIN (si possible)
- Description détaillée
```

**Traitement**:
- Widget: `collect_reclamation`
- Transfer reason: "Réclamation client"
- Confirmation: "Votre réclamation a été enregistrée. Un conseiller vous contactera dans les plus brefs délais"

**Menu fin**:
```
1️⃣ Retour menu principal
2️⃣ Quitter
```

---

### Menu 4: Club VIP

**Flow**:
```
api_menu_vip → menu_vip → split_menu_vip
```

**Options**:
```
Bienvenue dans l'espace Club VIP Mercedes-Benz 🌟

1️⃣ Fonctionnement du club
2️⃣ Parler à un conseiller VIP
3️⃣ Retour menu principal
```

#### Option 1: Fonctionnement
```
api_vip_fonctionnement → send_brochure_vip (PDF) → delay → delay → menu_fin_vip
```

**Média**:
- URL: `https://topaz-bullfrog-4509.twil.io/assets/Notice%20de%20fonctionnement%20-%20Club%20VIP%20Mercedes-Benz%20by%20CFAO.pdf`

#### Option 2: Conseiller VIP
```
api_agent_transfer_vip → send_confirmation_vip → menu_fin_vip
```

**Transfer**:
- Reason: "Demande conseiller VIP"
- Confirmation: "Un conseiller VIP vous contactera dans les 24h (jours ouvrables)"

---

### Menu 5: Parler à un agent

**Flow direct**:
```
api_menu_agent → ask_agent_reason → api_free_input_agent → api_agent_transfer_direct → send_confirmation_agent → menu_fin_agent
```

**Collecte**:
```
Veuillez nous indiquer brièvement l'objet de votre demande afin de vous orienter vers le bon interlocuteur.
```

**Traitement**:
- Widget: `collect_agent_reason`
- Transfer reason: "Demande agent direct"
- Confirmation: "Un agent Mercedes-Benz vous contactera dans les plus brefs délais"

---

## 7. Gestion des erreurs et timeouts

### Timeout de session
**Widget**: `handle_timeout`
**Déclencheur**: Aucune réponse pendant 3600 secondes (1 heure)

**Flow**:
```
handle_timeout → api_complete_timeout → END
```

**Message**:
```
Votre session a expiré pour cause d'inactivité.
N'hésitez pas à nous recontacter.

📞 07 01 52 52 52
```

**Appel API**: `/api/twilio/complete` pour marquer la conversation comme `timeout`

### Échec de connexion API
**Widget**: `send_error_message`
**Déclencheur**: `api_incoming` failed

**Message**:
```
Une erreur est survenue. Veuillez réessayer plus tard ou nous appeler au 07 01 52 52 52.
```

**Pas d'appel API** (puisque le backend est inaccessible)

### Messages invalides
Chaque menu a un widget `invalid_*` qui :
1. Envoie un message d'erreur spécifique
2. **Reboucle sur la question précédente**

Exemples:
- `invalid_menu_principal`: "Je n'ai pas compris. Veuillez saisir un chiffre entre 1 et 5."
- `invalid_is_client`: "Je n'ai pas compris. Veuillez saisir 1 pour Oui ou 2 pour Non."
- `invalid_fin_vn`: "Je n'ai pas compris. Veuillez saisir 1, 2 ou 3."

---

## 8. Finalisation de conversation

### Quitter normalement
**Widget**: `api_complete`
**URL**: `/api/twilio/complete`
**Payload**: `{"conversation_id": 123}`

**Backend action** (TwilioWebhookController):
```php
$conversation->status = 'completed';
$conversation->ended_at = now();
$conversation->duration_seconds = calculated_duration;
```

**Suivi**:
```
send_goodbye → END
```

**Message**:
```
Merci d'avoir utilisé notre assistant virtuel ! 🌟

📞 Nous restons joignables au 07 01 52 52 52

À bientôt chez Mercedes-Benz by CFAO !
```

---

## 9. Intégration avec le backend Laravel

### Endpoints appelés par le flow

| Endpoint | Widgets utilisateurs | Données envoyées | Réponse attendue |
|----------|---------------------|------------------|------------------|
| `/api/twilio/incoming` | `api_incoming` | Message complet | `conversation_id`, `client_has_name`, `client_status_known`, `agent_mode` |
| `/api/twilio/menu-choice` | `api_menu_*`, `api_vn_*`, `api_sav_*`, etc. | `conversation_id`, `menu_choice`, `user_input` | Confirmation |
| `/api/twilio/free-input` | `api_free_input_*` | `conversation_id`, `user_input`, `widget_name` | Confirmation |
| `/api/twilio/agent-transfer` | `api_agent_transfer_*` | `conversation_id`, `reason` | Confirmation |
| `/api/twilio/complete` | `api_complete`, `api_complete_timeout` | `conversation_id` | Confirmation |

### Mapping des widgets vers les événements

**ConversationEvent.event_type**:

| Widget Name | event_type |
|-------------|------------|
| `collect_name` | `free_input` (nom/prénom) |
| `check_client` | `free_input` (statut client) |
| `collect_contact_commercial` | `free_input` (coordonnées VN) |
| `collect_reparation` | `free_input` (infos réparation) |
| `collect_reclamation` | `free_input` (réclamation) |
| `collect_agent_reason` | `free_input` (raison agent) |
| Menu choices | `menu_choice` |
| Agent transfers | `agent_transfer` |

### Statuts de conversation

| Status | Quand | Backend |
|--------|-------|---------|
| `active` | Conversation en cours avec bot | `status = 'active'` |
| `transferred` | Client demande un agent | `status = 'transferred'`, `agent_id = NULL` |
| `completed` | Conversation terminée normalement | `status = 'completed'`, `ended_at` set |
| `timeout` | Expiration après 1h | `status = 'timeout'`, `ended_at` set |

---

## 10. Fonctions Twilio utilisées

### add_delay
**Service**: ZSb7de9fd35671e380ad53677e2cf57770
**Environnement**: ZEc4ec4248edabbbb0cddf19a5c41ef926
**Fonction**: ZHe612874f4ed88f0f570e65c3a78d3411
**URL**: `https://merco-9614.twil.io/add_delay`

**Usage**:
- Après envoi de PDF (2 appels successifs pour éviter trop d'envois rapides)
- Après message de bienvenue (1 appel pour temporiser)

**Comportement probable**: `setTimeout()` de 1-2 secondes

---

## 11. Points d'optimisation identifiés

### ✅ Points forts
1. **Mode agent détecté dès l'entrée** → Empêche les conflits bot/humain
2. **Vérification client intelligente** → Saute les questions déjà répondues
3. **Validation stricte des inputs** → Toutes les réponses invalides rebouclent
4. **Timeouts configurés** → 1 heure avant expiration
5. **Logging exhaustif** → Chaque action enregistrée via API
6. **Gestion d'erreurs** → Message de fallback si API down

### ⚠️ Points d'amélioration potentiels

1. **Délais multiples après PDF**:
   - Actuellement: 2x `add_delay` successifs
   - Alternative: Un seul délai plus long

2. **Messages PDF en anglais**:
   - `G_Klasse_W465_ePaper_24_1_02_ENG.pdf` → Document en anglais
   - Devrait être en français pour cohérence

3. **Numéro de téléphone hardcodé**:
   - `07 01 52 52 52` apparaît 3 fois
   - Devrait être une variable Twilio pour faciliter les changements

4. **Pas de retry sur échec API**:
   - Si `/api/twilio/incoming` échoue → Message d'erreur immédiat
   - Pourrait tenter 1-2 retries avant d'abandonner

5. **Timeout uniforme**:
   - Tous les timeouts = 3600s (1h)
   - Certaines étapes (nom, email) pourraient avoir des timeouts plus courts (10-15 min)

6. **Pas de validation d'email/téléphone**:
   - Les coordonnées sont stockées en texte libre
   - Pourrait utiliser regex Twilio pour valider format

7. **Agent transfer sans contexte**:
   - Quand un agent prend le contrôle, il doit lire l'historique complet
   - Pourrait envoyer un résumé automatique au moment du transfer

---

## 12. Parcours utilisateur typiques

### Scénario 1: Nouveau client - Demande catalogue VN
```
1. Message entrant → Bienvenue
2. "Quels sont vos nom et prénom ?" → "Jean Dupont"
3. "Êtes-vous client MB ?" → "2" (Non)
4. Menu principal → "1" (VN)
5. Menu VN → "1" (Catalogue)
6. Envoi PDF + lien
7. Menu fin VN → "3" (Quitter)
8. Message au revoir
```

**Durée estimée**: 3-5 minutes
**Nombre d'appels API**: 6-7

### Scénario 2: Client connu - Réclamation
```
1. Message entrant → Bienvenue
2. (Skip nom - déjà connu)
3. (Skip statut client - déjà connu)
4. Menu principal → "3" (Réclamation)
5. Description réclamation → "Problème avec ma GLE..."
6. Transfer agent
7. Message confirmation
8. Menu fin → "2" (Quitter)
```

**Durée estimée**: 2-3 minutes
**Nombre d'appels API**: 4-5

### Scénario 3: Client urgent - Agent direct
```
1. Message entrant → Bienvenue
2. Menu principal → "5" (Agent)
3. Raison → "Urgence panne véhicule"
4. Transfer agent
5. Message confirmation
6. Menu fin → "2" (Quitter)
```

**Durée estimée**: 1-2 minutes
**Nombre d'appels API**: 4

### Scénario 4: Conversation reprise par agent
```
1. Message entrant
2. api_incoming.agent_mode = true
3. "Votre conversation est en cours avec un agent"
4. FIN (flow n'intervient plus)
5. Agent répond manuellement via dashboard
```

**Durée**: Instantané
**Nombre d'appels API**: 1

---

## 13. Matrice de décision - Collecte d'informations

| Information | Quand collectée | Widget | Stockage backend |
|-------------|-----------------|--------|------------------|
| Nom/Prénom | Si `client_has_name = false` | `ask_name` → `collect_name` | `clients.nom_prenom`, `conversations.nom_prenom` |
| Statut client | Si `client_status_known = false` | `ask_is_client` → `check_client` | `clients.is_client`, `conversations.is_client` |
| Coordonnées (VN) | Choix "Contact conseiller" | `ask_contact_info` → `collect_contact_commercial` | `conversation_events.user_input` |
| Infos réparation | Choix "Réparation SAV" | `ask_reparation_info` → `collect_reparation` | `conversation_events.user_input` |
| Réclamation | Menu Réclamation | `ask_reclamation` → `collect_reclamation` | `conversation_events.user_input` |
| Raison agent | Choix "Parler à un agent" | `ask_agent_reason` → `collect_agent_reason` | `conversation_events.user_input` |

**Optimisation**: Le système évite de redemander le nom et le statut client si déjà connus dans la table `clients`.

---

## 14. Flux de données complet

```
┌─────────────────┐
│  Client WhatsApp│
└────────┬────────┘
         │ Message
         ▼
┌─────────────────┐
│  Twilio Studio  │ ◄─── Flow JSON (ce fichier)
└────────┬────────┘
         │ Webhook POST
         ▼
┌──────────────────────────────────┐
│  Laravel Backend                 │
│  TwilioWebhookController         │
│  ┌────────────────────────────┐  │
│  │ handleIncomingMessage()    │  │
│  │  - Create/update Client    │  │
│  │  - Create/update Conversation│
│  │  - Log ConversationEvent   │  │
│  │  - Return agent_mode       │  │
│  └────────────────────────────┘  │
└────────┬─────────────────────────┘
         │ JSON Response
         │ {agent_mode, client_has_name, ...}
         ▼
┌─────────────────┐
│  Twilio Studio  │
│  Decision:      │
│  - If agent_mode → END
│  - Else → Continue bot flow
└────────┬────────┘
         │
         ▼
┌─────────────────────────────────┐
│  Bot Flow (menus, collecte)     │
│  - Send messages                │
│  - Wait for replies             │
│  - Call menu-choice/free-input  │
│  - Call agent-transfer si besoin│
└────────┬────────────────────────┘
         │
         ▼
┌─────────────────────────────────┐
│  Laravel Backend                │
│  - Log events                   │
│  - Update conversation status   │
│  - Store client data            │
└────────┬────────────────────────┘
         │
         ▼ (si agent transfer)
┌─────────────────────────────────┐
│  Dashboard Agent                │
│  - Voir queue "En attente"      │
│  - Prendre en charge            │
│  - Envoyer messages manuels     │
│  - Clôturer conversation        │
└─────────────────────────────────┘
```

---

## 15. Configuration Twilio requise

### Variables d'environnement Laravel
```env
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_WHATSAPP_NUMBER=whatsapp:+14155238886
```

### Webhooks Twilio Studio à configurer
```
Incoming Message:
  https://mbbot-dashboard.ywcdigital.com/api/twilio/incoming

Menu Choice:
  https://mbbot-dashboard.ywcdigital.com/api/twilio/menu-choice

Free Input:
  https://mbbot-dashboard.ywcdigital.com/api/twilio/free-input

Agent Transfer:
  https://mbbot-dashboard.ywcdigital.com/api/twilio/agent-transfer

Complete Conversation:
  https://mbbot-dashboard.ywcdigital.com/api/twilio/complete
```

### Assets Twilio à uploader
```
PDF Catalogue:
  https://mercedes-9755.twil.io/G_Klasse_W465_ePaper_24_1_02_ENG.pdf

PDF Forfaits SAV:
  https://merco-9614.twil.io/Forfaits%20entretien%20Mercedes-benz%20by%20CFAO.pdf

PDF Club VIP:
  https://topaz-bullfrog-4509.twil.io/assets/Notice%20de%20fonctionnement%20-%20Club%20VIP%20Mercedes-Benz%20by%20CFAO.pdf
```

### Fonction Twilio
```javascript
// add_delay function
exports.handler = function(context, event, callback) {
  setTimeout(() => {
    callback(null, { success: true });
  }, 2000); // 2 secondes
};
```

---

## Conclusion

Ce flow Twilio v3.2 est un **système conversationnel bien structuré** qui :

✅ **Optimise l'expérience client** en évitant de redemander des informations déjà connues
✅ **Gère intelligemment le handoff** entre bot et agents humains
✅ **Segmente clairement** les parcours (VN, SAV, Réclamation, VIP, Agent)
✅ **Log exhaustivement** toutes les interactions pour analytics
✅ **Valide strictement** les inputs utilisateur avec rebouclage
✅ **Gère les erreurs** (timeout, API down, inputs invalides)

Le système est **production-ready** et bien intégré avec le backend Laravel pour une supervision complète des conversations.
