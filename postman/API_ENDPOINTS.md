# 📋 Liste des Endpoints API - Mercedes-Benz Bot

## Base URL

- **Production** : `https://mbbot-dashboard.ywcdigital.com`
- **Local** : `http://localhost:8000`

---

## 🔐 Authentication

### POST `/login`
- **Description** : Authentification utilisateur
- **Auth** : Aucune
- **Body** :
  ```json
  {
    "email": "admin@mercedes-bot.com",
    "password": "password123"
  }
  ```
- **Response** :
  ```json
  {
    "token": "1|abc123...",
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@mercedes-bot.com"
    }
  }
  ```

### POST `/register`
- **Description** : Créer un nouveau compte
- **Auth** : Aucune
- **Body** :
  ```json
  {
    "name": "Agent Test",
    "email": "agent@test.com",
    "password": "password123",
    "password_confirmation": "password123"
  }
  ```

### POST `/logout`
- **Description** : Déconnexion
- **Auth** : Bearer Token

---

## 📱 Twilio Webhooks

### POST `/api/twilio/incoming`
- **Description** : Réception message WhatsApp entrant
- **Auth** : Aucune (webhook)
- **Body** :
  ```json
  {
    "SessionId": "session_12345",
    "From": "whatsapp:+225xxxxxxxx",
    "Body": "Bonjour",
    "MessageSid": "SM1234567890",
    "ProfileName": "John Doe"
  }
  ```

### POST `/api/twilio/menu-choice`
- **Description** : Choix utilisateur dans un menu
- **Auth** : Aucune (webhook)
- **Body** :
  ```json
  {
    "SessionId": "session_12345",
    "MenuName": "menu_principal",
    "Choice": "1",
    "ChoiceLabel": "Prendre rendez-vous",
    "From": "whatsapp:+225xxxxxxxx"
  }
  ```

### POST `/api/twilio/free-input`
- **Description** : Saisie libre utilisateur
- **Auth** : Aucune (webhook)
- **Body** :
  ```json
  {
    "SessionId": "session_12345",
    "FieldName": "nom_prenom",
    "FieldLabel": "Nom et Prénom",
    "Value": "Jean Dupont",
    "From": "whatsapp:+225xxxxxxxx"
  }
  ```

### POST `/api/twilio/agent-transfer`
- **Description** : Transfert vers un agent
- **Auth** : Aucune (webhook)
- **Body** :
  ```json
  {
    "SessionId": "session_12345",
    "From": "whatsapp:+225xxxxxxxx",
    "Reason": "Demande de contact agent",
    "ChatwootConversationId": 123
  }
  ```

### POST `/api/twilio/complete`
- **Description** : Terminer une conversation
- **Auth** : Aucune (webhook)
- **Body** :
  ```json
  {
    "SessionId": "session_12345",
    "From": "whatsapp:+225xxxxxxxx"
  }
  ```

### POST `/api/twilio/send-message`
- **Description** : Envoyer un message WhatsApp (Agent)
- **Auth** : Bearer Token
- **Body** :
  ```json
  {
    "to": "whatsapp:+225xxxxxxxx",
    "message": "Bonjour, un agent va vous répondre."
  }
  ```

---

## 📊 Dashboard API (Protected)

> ⚠️ **Tous ces endpoints nécessitent un Bearer Token**
>
> Header : `Authorization: Bearer {token}`

### GET `/api/dashboard/stats`
- **Description** : Statistiques globales
- **Response** :
  ```json
  {
    "total_conversations": 150,
    "active_conversations": 5,
    "completed_today": 20,
    "transferred_today": 3,
    "avg_duration_seconds": 300
  }
  ```

### GET `/api/dashboard/conversations`
- **Description** : Liste des conversations avec filtres
- **Query Parameters** :
  - `status` : `active`, `completed`, `transferred`
  - `page` : Numéro de page (défaut: 1)
  - `per_page` : Résultats par page (défaut: 20)
  - `phone` : Filtrer par numéro
  - `date_from` : Date début (YYYY-MM-DD)
  - `date_to` : Date fin (YYYY-MM-DD)
- **Example** : `/api/dashboard/conversations?status=active&page=1&per_page=20`

### GET `/api/dashboard/conversations/{id}`
- **Description** : Détail complet d'une conversation
- **Response** :
  ```json
  {
    "id": 1,
    "session_id": "session_12345",
    "phone_number": "+225xxxxxxxx",
    "nom_prenom": "Jean Dupont",
    "status": "active",
    "started_at": "2024-12-04T10:00:00Z",
    "events": [
      {
        "event_type": "menu_choice",
        "menu_name": "menu_principal",
        "choice": "1"
      }
    ]
  }
  ```

### GET `/api/dashboard/active`
- **Description** : Conversations actives en temps réel

### GET `/api/dashboard/history`
- **Description** : Historique des statistiques quotidiennes
- **Query Parameters** :
  - `days` : Nombre de jours (défaut: 30)
- **Example** : `/api/dashboard/history?days=7`

### GET `/api/dashboard/paths`
- **Description** : Parcours les plus fréquents
- **Query Parameters** :
  - `limit` : Nombre de résultats (défaut: 10)

### GET `/api/dashboard/search-inputs`
- **Description** : Recherche dans les saisies libres
- **Query Parameters** :
  - `query` : Terme de recherche (requis)
  - `field` : Filtrer par champ (`nom_prenom`, `email`, `vin`, `carte_vip`)
- **Example** : `/api/dashboard/search-inputs?query=dupont&field=nom_prenom`

---

## 💬 Agent Chat (Protected)

> ⚠️ **Tous ces endpoints nécessitent une authentification web (session)**

### GET `/dashboard/chat/{id}`
- **Description** : Afficher l'interface de chat (page web)

### POST `/dashboard/chat/{id}/take-over`
- **Description** : Prendre en charge une conversation
- **Response** :
  ```json
  {
    "success": true,
    "message": "Conversation prise en charge"
  }
  ```

### POST `/dashboard/chat/{id}/send`
- **Description** : Envoyer un message au client
- **Body** :
  ```json
  {
    "message": "Bonjour, comment puis-je vous aider ?"
  }
  ```

### POST `/dashboard/chat/{id}/close`
- **Description** : Fermer la conversation
- **Response** :
  ```json
  {
    "success": true,
    "message": "Conversation fermée"
  }
  ```

---

## 🔄 Legacy Webhooks (n8n)

### POST `/api/webhook/event`
- **Description** : Événement générique
- **Auth** : Aucune (webhook)

### POST `/api/webhook/user-data`
- **Description** : Mise à jour données utilisateur
- **Auth** : Aucune (webhook)

### POST `/api/webhook/transfer`
- **Description** : Transfert vers Chatwoot
- **Auth** : Aucune (webhook)

### POST `/api/webhook/complete`
- **Description** : Fin de conversation
- **Auth** : Aucune (webhook)

---

## ❤️ Health Check

### GET `/api/health`
- **Description** : Vérifier le statut de l'API
- **Auth** : Aucune
- **Response** :
  ```json
  {
    "status": "ok",
    "timestamp": "2024-12-04T10:00:00Z"
  }
  ```

---

## 📝 Codes de statut HTTP

| Code | Signification |
|------|---------------|
| 200 | Succès |
| 201 | Créé avec succès |
| 400 | Requête invalide |
| 401 | Non authentifié |
| 403 | Non autorisé |
| 404 | Ressource non trouvée |
| 422 | Erreur de validation |
| 500 | Erreur serveur |

---

## 🔐 Authentification Bearer Token

Pour les endpoints protégés, incluez le token dans le header :

```
Authorization: Bearer 1|abc123def456...
```

### Obtenir un token

1. Appelez `POST /login` avec email et mot de passe
2. Récupérez le token dans la réponse
3. Utilisez-le dans le header `Authorization`

### Token expiré

Si vous recevez une erreur 401, reconnectez-vous pour obtenir un nouveau token.

---

## 🧪 Tests avec cURL

### Login
```bash
curl -X POST https://mbbot-dashboard.ywcdigital.com/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@mercedes-bot.com",
    "password": "password123"
  }'
```

### Get Statistics (avec token)
```bash
curl -X GET https://mbbot-dashboard.ywcdigital.com/api/dashboard/stats \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Twilio Incoming Message
```bash
curl -X POST https://mbbot-dashboard.ywcdigital.com/api/twilio/incoming \
  -H "Content-Type: application/json" \
  -d '{
    "SessionId": "test_session_123",
    "From": "whatsapp:+225xxxxxxxx",
    "Body": "Bonjour",
    "MessageSid": "SM123456",
    "ProfileName": "Test User"
  }'
```

---

## 📖 Plus d'informations

Consultez le fichier `postman/README.md` pour importer la collection Postman complète.
