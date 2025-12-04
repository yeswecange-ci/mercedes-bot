# 📘 Guide d'Intégration du Flow Twilio Existant avec Laravel

Ce guide vous montre comment intégrer votre flow Twilio existant avec l'application Laravel Dashboard **sans modifier la logique du flow**.

## 🎯 Objectif

Ajouter des widgets HTTP à votre flow existant pour :
- ✅ Enregistrer toutes les interactions dans le dashboard
- ✅ Gérer le mode agent (transfert humain)
- ✅ Conserver 100% de la logique et navigation existantes

## 📋 Modifications à effectuer

### 1. **Au début du flow (après Trigger)**

#### Ajouter un widget "Make HTTP Request"

**Nom** : `send_to_laravel_incoming`
**Position** : Entre `Trigger` et le premier message de bienvenue
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/incoming
Content-Type: application/x-www-form-urlencoded;charset=utf-8

Parameters:
- From: {{trigger.message.From}}
- Body: {{trigger.message.Body}}
- MessageSid: {{trigger.message.MessageSid}}
- ProfileName: {{trigger.message.ProfileName}}
```

**Transitions** :
- Success → `check_agent_mode` (nouveau widget)
- Failed → `send_message_welcome` (votre message de bienvenue actuel)

---

### 2. **Vérification du mode agent**

#### Ajouter un widget "Split Based On"

**Nom** : `check_agent_mode`
**Position** : Après `send_to_laravel_incoming`
**Configuration** :
```
Input: {{widgets.send_to_laravel_incoming.parsed.agent_mode}}

Conditions:
1. If value equal_to "true" → agent_mode_notification
2. No Match → send_message_welcome
```

#### Ajouter un widget "Send Message"

**Nom** : `agent_mode_notification`
**Message** :
```
Votre message a été reçu. Un agent Mercedes-Benz vous répondra sous peu.
```

**Transition** : Sent → (fin du flow)

---

### 3. **Après collecte Nom/Prénom**

#### Ajouter un widget "Make HTTP Request"

**Nom** : `save_nomprenom`
**Position** : Entre `send_and_reply_nomprenom` et `send_and_reply_clien_yn`
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/free-input

Parameters:
- conversation_id: {{widgets.send_to_laravel_incoming.parsed.conversation_id}}
- user_input: {{widgets.send_and_reply_nomprenom.inbound.Body}}
- widget_name: nom_prenom
```

**Transitions** :
- Success → `send_and_reply_clien_yn`
- Failed → `send_and_reply_clien_yn` (continuer même si échec)

---

### 4. **Après collecte Client Oui/Non**

#### Ajouter un widget "Make HTTP Request"

**Nom** : `save_client_status`
**Position** : Entre `send_and_reply_clien_yn` et `split_1`
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/free-input

Parameters:
- conversation_id: {{widgets.send_to_laravel_incoming.parsed.conversation_id}}
- user_input: {{widgets.send_and_reply_clien_yn.inbound.Body}}
- widget_name: is_client
```

---

### 5. **Après chaque choix du menu principal**

Pour chaque option du menu (1-5), ajoutez un widget HTTP AVANT d'aller au sous-menu.

#### Option 1 : Véhicules neufs

**Nom** : `save_menu_choice_vn`
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/menu-choice

Parameters:
- conversation_id: {{widgets.send_to_laravel_incoming.parsed.conversation_id}}
- menu_choice: vehicules_neufs
- user_input: {{widgets.send_and_reply_menu_prin.inbound.Body}}
```

**Transition** : Success → `send_and_reply_vn`

#### Option 2 : Service après-vente

**Nom** : `save_menu_choice_sav`
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/menu-choice

Parameters:
- conversation_id: {{widgets.send_to_laravel_incoming.parsed.conversation_id}}
- menu_choice: service_apres_vente
- user_input: {{widgets.send_and_reply_menu_prin.inbound.Body}}
```

**Transition** : Success → `send_and_reply_option2_sav`

#### Option 3 : Réclamations

**Nom** : `save_menu_choice_reclamation`
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/menu-choice

Parameters:
- conversation_id: {{widgets.send_to_laravel_incoming.parsed.conversation_id}}
- menu_choice: reclamations
- user_input: {{widgets.send_and_reply_menu_prin.inbound.Body}}
```

**Transition** : Success → `send_and_reply_3_5_sav`

#### Option 4 : Club VIP

**Nom** : `save_menu_choice_vip`
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/menu-choice

Parameters:
- conversation_id: {{widgets.send_to_laravel_incoming.parsed.conversation_id}}
- menu_choice: club_vip
- user_input: {{widgets.send_and_reply_menu_prin.inbound.Body}}
```

**Transition** : Success → `send_and_reply_fidelite`

#### Option 5 : Parler à un agent (IMPORTANT !)

**Nom** : `save_menu_choice_agent`
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/agent-transfer

Parameters:
- conversation_id: {{widgets.send_to_laravel_incoming.parsed.conversation_id}}
- reason: user_requested
```

**Transition** : Success → `send_and_reply_3_5_sav` (ou votre widget de gestion agent)

---

### 6. **Sauvegarder les saisies libres importantes**

Pour chaque widget où l'utilisateur saisit des informations (email, téléphone, VIN, etc.), ajoutez un widget HTTP.

#### Exemple : Option 3 VN (Contact conseiller)

**Nom** : `save_vn_option3_data`
**Position** : Après `send_and_reply_vn_option3`
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/free-input

Parameters:
- conversation_id: {{widgets.send_to_laravel_incoming.parsed.conversation_id}}
- user_input: {{widgets.send_and_reply_vn_option3.inbound.Body}}
- widget_name: contact_conseiller
```

#### Exemple : Réclamations/Agent (Option 3 ou 5)

**Nom** : `save_reclamation_data`
**Position** : Après `send_and_reply_3_5_sav`
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/free-input

Parameters:
- conversation_id: {{widgets.send_to_laravel_incoming.parsed.conversation_id}}
- user_input: {{widgets.send_and_reply_3_5_sav.inbound.Body}}
- widget_name: reclamation_agent
```

#### Exemple : Demande agent VIP

**Nom** : `save_vip_demand_data`
**Position** : Après `send_and_reply_vipdemandagent`
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/free-input

Parameters:
- conversation_id: {{widgets.send_to_laravel_incoming.parsed.conversation_id}}
- user_input: {{widgets.send_and_reply_vipdemandagent.inbound.Body}}
- widget_name: vip_agent_request
```

#### Exemple : Réparation SAV

**Nom** : `save_reparation_data`
**Position** : Après `send_and_reply_suitereparation`
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/free-input

Parameters:
- conversation_id: {{widgets.send_to_laravel_incoming.parsed.conversation_id}}
- user_input: {{widgets.send_and_reply_suitereparation.inbound.Body}}
- widget_name: reparation_info
```

---

### 7. **À la fin de chaque branche (quitter)**

Pour tous les messages de fin ("Merci", "Au revoir", etc.), ajoutez un widget de complétion.

#### Ajouter un widget "Make HTTP Request"

**Nom** : `complete_conversation`
**Position** : APRÈS le message final (send_message_fin_vn, send_message_bye_sav, etc.)
**Configuration** :
```
Method: POST
URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/complete

Parameters:
- conversation_id: {{widgets.send_to_laravel_incoming.parsed.conversation_id}}
```

**Transition** : Success → (fin du flow)

**Points de complétion à ajouter** :
- Après `send_message_fin_vn` → `complete_conversation`
- Après `send_message_bye_sav` → `complete_conversation`
- Après `send_message_byesav` → `complete_conversation`

---

## 🔧 Configuration Requise

### Dans Twilio Studio

1. **Ouvrir votre flow existant** dans Twilio Studio
2. **Pour chaque widget à ajouter** (voir ci-dessus) :
   - Cliquer sur "+" pour ajouter un widget
   - Choisir le type (Make HTTP Request ou Split Based On)
   - Configurer selon les paramètres ci-dessus
   - Connecter les transitions
3. **Tester le flow** avec un message test
4. **Publier** une fois validé

### Variables importantes à utiliser

Ces variables sont disponibles dans tout le flow après le widget `send_to_laravel_incoming` :

```
{{widgets.send_to_laravel_incoming.parsed.conversation_id}}
{{widgets.send_to_laravel_incoming.parsed.session_id}}
{{widgets.send_to_laravel_incoming.parsed.phone_number}}
{{widgets.send_to_laravel_incoming.parsed.agent_mode}}
{{widgets.send_to_laravel_incoming.parsed.status}}
```

---

## 📊 Résumé des Endpoints API

| Endpoint | Usage | Quand l'appeler |
|----------|-------|-----------------|
| `/api/twilio/incoming` | Créer/charger conversation | Au début du flow (trigger) |
| `/api/twilio/menu-choice` | Enregistrer choix menu | Après chaque sélection de menu |
| `/api/twilio/free-input` | Enregistrer saisie libre | Après nom, email, VIN, etc. |
| `/api/twilio/agent-transfer` | Transférer à un agent | Option 5 ou demande agent |
| `/api/twilio/complete` | Clôturer conversation | À la fin (quitter) |

---

## 🎨 Schéma de Flux Intégré

```
[Trigger]
    ↓
[send_to_laravel_incoming] ← Appel API /incoming
    ↓
[check_agent_mode] ← Vérifie si agent actif
    ├─ Si TRUE → [agent_mode_notification] → FIN
    └─ Si FALSE → [send_message_welcome]
                       ↓
                  [function_2] (delay)
                       ↓
                  [send_and_reply_menu_prin] ← Menu principal
                       ↓
                  [send_and_reply_nomprenom] ← Collecte nom
                       ↓
                  [save_nomprenom] ← Appel API /free-input
                       ↓
                  [send_and_reply_clien_yn] ← Client?
                       ↓
                  [save_client_status] ← Appel API /free-input
                       ↓
                  [split_1] ← Route selon choix
                       ├─ Option 1 → [save_menu_choice_vn] → [send_and_reply_vn] → ...
                       ├─ Option 2 → [save_menu_choice_sav] → [send_and_reply_option2_sav] → ...
                       ├─ Option 3 → [save_menu_choice_reclamation] → [send_and_reply_3_5_sav] → ...
                       ├─ Option 4 → [save_menu_choice_vip] → [send_and_reply_fidelite] → ...
                       └─ Option 5 → [save_menu_choice_agent] → [send_and_reply_3_5_sav] → ...
                                         (Transfert agent!)
```

---

## ✅ Checklist d'Intégration

- [ ] Ajouter `send_to_laravel_incoming` après le Trigger
- [ ] Ajouter `check_agent_mode` et `agent_mode_notification`
- [ ] Ajouter `save_nomprenom` après collecte nom/prénom
- [ ] Ajouter `save_client_status` après collecte client oui/non
- [ ] Ajouter 5 widgets `save_menu_choice_*` pour chaque option du menu
- [ ] Ajouter `save_vn_option3_data` pour contact conseiller VN
- [ ] Ajouter `save_reclamation_data` pour réclamations/option 5
- [ ] Ajouter `save_vip_demand_data` pour demandes VIP
- [ ] Ajouter `save_reparation_data` pour réparations SAV
- [ ] Ajouter `complete_conversation` à toutes les fins de branche
- [ ] Tester le flow complet
- [ ] Publier le flow

---

## 🚀 Utilisation après Intégration

Une fois intégré, votre flow :

1. ✅ **Enregistre automatiquement** toutes les conversations dans le dashboard
2. ✅ **Capture tous les choix** et saisies des utilisateurs
3. ✅ **Gère le mode agent** : quand un client choisit l'option 5 ou qu'un agent prend en charge via le dashboard
4. ✅ **Calcule les statistiques** automatiquement (durée, menus utilisés, etc.)
5. ✅ **Permet aux agents** de reprendre les conversations via le dashboard

---

## 🐛 Dépannage

### Le flow ne s'exécute pas après ajout des widgets

**Solution** : Vérifiez que :
- L'URL de l'API est correcte : `https://mbbot-dashboard.ywcdigital.com`
- Les transitions sont correctement connectées
- Le widget `send_to_laravel_incoming` est AVANT `check_agent_mode`

### Les données ne s'enregistrent pas

**Solution** :
1. Vérifier les logs Laravel : `storage/logs/laravel.log`
2. Tester l'API manuellement avec Postman
3. Vérifier que `conversation_id` est bien passé à tous les widgets

### Le mode agent ne fonctionne pas

**Solution** :
1. Vérifier que `check_agent_mode` lit bien `{{widgets.send_to_laravel_incoming.parsed.agent_mode}}`
2. Tester en prenant en charge une conversation depuis le dashboard
3. Vérifier que la conversation a `status = 'transferred'` dans la base de données

---

## 📞 Support

Pour toute question :
1. Consulter `TWILIO_INTEGRATION_GUIDE.md`
2. Consulter `AGENT_CHAT_SYSTEM.md`
3. Vérifier les logs : `storage/logs/laravel.log`

---

**Version:** 1.0.0
**Date:** 3 Décembre 2025
**Auteur:** Mercedes-Benz Bot Team
