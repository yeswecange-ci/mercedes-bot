# 🔧 Guide de Modification du Twilio Flow

**Objectif:** Corriger les 2 problèmes identifiés dans le Flow Twilio
**Temps estimé:** 10 minutes
**Niveau:** Facile (copier-coller)

---

## 🎯 CORRECTIONS À APPORTER

### ✅ Correction déjà appliquée (Backend)

Le webhook retourne maintenant `pending_agent: true/false` ✅

---

## 📝 MODIFICATIONS DU FLOW TWILIO

### 🔴 CORRECTION 1: Supprimer le message redondant

**État à modifier:** `end_flow_agent`

#### Option A : Supprimer le message (RECOMMANDÉ)

1. Ouvrir le Flow Twilio dans Studio
2. Trouver l'état `end_flow_agent`
3. **Supprimer complètement le widget "send-message"**
4. Remplacer par un widget vide qui termine le flow

**OU**

#### Option B : Laisser le message vide

1. Ouvrir l'état `end_flow_agent`
2. Dans le champ `body`, **effacer le texte**
3. Laisser vide : `""`
4. Sauvegarder

**Résultat attendu:**
- Quand `agent_mode = true`, le flow se termine sans envoyer de message
- L'agent peut répondre directement sans confusion

---

### 🟡 CORRECTION 2: Gérer les conversations en attente d'agent

#### Étape 1: Ajouter un nouveau état `check_pending_agent`

**Emplacement:** Juste après `check_agent_mode`, avant `send_message_welcome`

**Configuration du widget:**

```json
{
  "name": "check_pending_agent",
  "type": "split-based-on",
  "properties": {
    "input": "{{widgets.api_incoming.parsed.pending_agent}}",
    "offset": {
      "x": -200,
      "y": 600
    }
  },
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
          "friendly_name": "En attente agent",
          "arguments": [
            "{{widgets.api_incoming.parsed.pending_agent}}"
          ],
          "type": "equal_to",
          "value": "true"
        }
      ]
    }
  ]
}
```

**Comment ajouter dans Twilio Studio:**

1. Cliquer sur l'état `check_agent_mode`
2. Modifier la transition `noMatch`
3. Au lieu d'aller vers `send_message_welcome`, rediriger vers `check_pending_agent`
4. Ajouter un nouveau widget "Split Based On"
5. Nom : `check_pending_agent`
6. Input : `{{widgets.api_incoming.parsed.pending_agent}}`
7. Condition : `equal_to` → `true`
8. Si match → `end_flow_pending` (nouveau widget)
9. Si noMatch → `send_message_welcome`

---

#### Étape 2: Ajouter l'état `end_flow_pending`

**Configuration du widget:**

```json
{
  "name": "end_flow_pending",
  "type": "send-message",
  "properties": {
    "offset": {
      "x": -400,
      "y": 800
    },
    "service": "{{trigger.message.InstanceSid}}",
    "channel": "{{trigger.message.ChannelSid}}",
    "from": "{{flow.channel.address}}",
    "to": "{{contact.channel.address}}",
    "body": "Votre demande a été transmise à notre équipe. Un agent vous contactera très bientôt. ⏱️\n\nMerci de votre patience."
  },
  "transitions": [
    {
      "event": "sent"
    },
    {
      "event": "failed"
    }
  ]
}
```

**Comment ajouter dans Twilio Studio:**

1. Ajouter un nouveau widget "Send Message"
2. Nom : `end_flow_pending`
3. Message Body :
   ```
   Votre demande a été transmise à notre équipe. Un agent vous contactera très bientôt. ⏱️

   Merci de votre patience.
   ```
4. From : `{{flow.channel.address}}`
5. To : `{{contact.channel.address}}`
6. Transitions : Laisser vides (fin de flow)

---

## 📊 SCHÉMA DU NOUVEAU FLUX

### Avant (problématique)

```
api_incoming
    ↓
check_agent_mode
    ↓ agent_mode=true
    end_flow_agent (message redondant) ❌
    ↓ agent_mode=false
    send_message_welcome
        ↓
    [Bot continue même si en attente d'agent] ❌
```

### Après (corrigé)

```
api_incoming
    ↓
check_agent_mode
    ↓ agent_mode=true (agent déjà assigné)
    end_flow_pending (VIDE - pas de message) ✅
    ↓ agent_mode=false
    check_pending_agent
        ↓ pending_agent=true (en attente d'agent)
        end_flow_pending (message d'attente) ✅
        ↓ pending_agent=false (conversation normale)
        send_message_welcome ✅
```

---

## 🧪 TESTS DE VALIDATION

### Test 1 : Conversation normale (sans agent)

**Scénario:**
```
1. Client: "Bonjour"
2. Vérifier: agent_mode=false, pending_agent=false
3. Résultat attendu: Message de bienvenue + menu principal
```

**Validation:** ✅ Bot fonctionne normalement

---

### Test 2 : Conversation en attente d'agent

**Scénario:**
```
1. Client demande agent (option 5)
2. Agent transfer appelé → status=transferred, agent_id=null
3. Client envoie nouveau message: "Vous êtes là ?"
4. Vérifier: agent_mode=false, pending_agent=true
5. Résultat attendu: "Votre demande a été transmise..."
6. Flow se termine, pas de menu
```

**Validation:** ✅ Client sait qu'il est en attente

---

### Test 3 : Conversation avec agent actif

**Scénario:**
```
1. Agent prend en charge → agent_id=2
2. Client envoie message: "Bonjour"
3. Vérifier: agent_mode=true, pending_agent=false
4. Résultat attendu: Aucun message automatique
5. Agent voit message et répond
```

**Validation:** ✅ Pas de message redondant

---

## 📱 ÉTAPES COMPLÈTES DANS TWILIO STUDIO

### 1. Connexion

1. Se connecter à Twilio Console
2. Aller dans **Studio** → **Flows**
3. Sélectionner le Flow "Mercedes-Benz by CFAO"

---

### 2. Modifier `end_flow_agent`

1. Cliquer sur le widget `end_flow_agent`
2. Dans le panneau de droite, section "MESSAGE"
3. **Effacer complètement le texte** dans "Message Body"
4. Laisser vide
5. Cliquer "Save"

---

### 3. Modifier `check_agent_mode`

1. Cliquer sur le widget `check_agent_mode`
2. Trouver la transition "No Match"
3. Actuellement elle pointe vers `send_message_welcome`
4. **Changer pour pointer vers** `check_pending_agent` (nouveau widget à créer)
5. Cliquer "Save"

---

### 4. Créer `check_pending_agent`

1. Cliquer sur le "+" entre `check_agent_mode` et `send_message_welcome`
2. Sélectionner **"Split Based On..."**
3. Widget Name : `check_pending_agent`
4. Variable to Test : `{{widgets.api_incoming.parsed.pending_agent}}`
5. Ajouter une condition :
   - Friendly Name : `En attente agent`
   - Condition : `equal_to`
   - Value : `true`
   - Transition To : `end_flow_pending` (à créer)
6. No Match → `send_message_welcome`
7. Cliquer "Save"

---

### 5. Créer `end_flow_pending`

1. Créer un nouveau widget **"Send Message"**
2. Widget Name : `end_flow_pending`
3. From : `{{flow.channel.address}}`
4. To : `{{contact.channel.address}}`
5. Message Body :
   ```
   Votre demande a été transmise à notre équipe. Un agent vous contactera très bientôt. ⏱️

   Merci de votre patience.
   ```
6. Transitions : Laisser par défaut (sent / failed)
7. Cliquer "Save"

---

### 6. Publier le Flow

1. En haut à droite, cliquer **"Publish"**
2. Confirmer la publication
3. Attendre quelques secondes

✅ **Modifications appliquées !**

---

## 🔍 VÉRIFICATION POST-DÉPLOIEMENT

### Checklist

- [ ] Widget `end_flow_agent` a un message vide
- [ ] Transition `check_agent_mode` noMatch → `check_pending_agent`
- [ ] Widget `check_pending_agent` existe et teste `pending_agent`
- [ ] Widget `end_flow_pending` existe avec message d'attente
- [ ] Flow publié (version la plus récente)

### Tests manuels

1. **Test message normal:**
   - Envoyer "Bonjour" → Doit recevoir menu

2. **Test demande agent:**
   - Choisir option 5
   - Envoyer un autre message
   - Doit recevoir "Votre demande a été transmise..."

3. **Test avec agent actif:**
   - Agent prend en charge
   - Client envoie message
   - Ne doit PAS recevoir message automatique

---

## 🐛 DÉPANNAGE

### Problème : "pending_agent" non reconnu

**Cause:** Le webhook n'a pas été déployé

**Solution:**
```bash
# Sur le serveur
git pull
php artisan cache:clear
php artisan config:clear
```

---

### Problème : Flow ne trouve pas check_pending_agent

**Cause:** Widget mal nommé ou non créé

**Solution:**
- Vérifier le nom exact : `check_pending_agent` (pas d'espace, pas de majuscule)
- Vérifier que le widget existe bien dans le Flow
- Republier le Flow

---

### Problème : Message d'attente s'affiche tout le temps

**Cause:** Condition mal configurée

**Solution:**
- Vérifier que la condition est `equal_to` et non `not_equal_to`
- Vérifier que la valeur est `true` (string) et non `TRUE` ou `1`

---

## 📞 SUPPORT

En cas de problème :

1. Vérifier les logs Twilio : Console → Debugger
2. Vérifier les logs Laravel : `storage/logs/laravel.log`
3. Tester avec le Debug Tool de Twilio Studio
4. Vérifier que le webhook retourne bien `pending_agent`

---

## ✅ RÉSULTAT FINAL

Après ces modifications :

✅ Client avec agent → Pas de message automatique
✅ Client en attente → Message d'attente approprié
✅ Client normal → Flow bot normal
✅ Expérience utilisateur fluide
✅ Pas de confusion avec messages multiples

---

**Temps de modification:** 10 minutes
**Impact utilisateur:** MAJEUR (meilleure expérience)
**Complexité:** Faible
**Priorité:** HAUTE

**Recommandation:** Appliquer dès que possible en production
