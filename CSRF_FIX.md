# 🔒 Fix : Erreur CSRF Token Mismatch dans Postman

## Problème

Lorsque vous essayez de vous connecter via Postman, vous obtenez l'erreur :
```json
{
    "message": "CSRF token mismatch."
}
```

## Cause

Laravel protège les routes web avec un token CSRF pour prévenir les attaques CSRF. Postman ne peut pas gérer automatiquement ces tokens comme le ferait un navigateur.

## ✅ Solution : Routes API avec Sanctum

J'ai créé des routes API dédiées qui utilisent **Laravel Sanctum** au lieu de la session web. Ces routes n'ont pas besoin de token CSRF.

---

## 🔑 Nouvelles Routes API d'Authentification

### Connexion
**POST** `/api/auth/login`

**Body :**
```json
{
    "email": "admin@mercedes-bot.com",
    "password": "password123"
}
```

**Response :**
```json
{
    "success": true,
    "message": "Connexion réussie",
    "token": "1|abc123def456...",
    "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@mercedes-bot.com",
        "role": "admin"
    }
}
```

### Inscription
**POST** `/api/auth/register`

**Body :**
```json
{
    "name": "Agent Test",
    "email": "agent@test.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Déconnexion
**POST** `/api/auth/logout`

**Headers :**
```
Authorization: Bearer {your_token}
```

### Informations utilisateur
**GET** `/api/auth/me`

**Headers :**
```
Authorization: Bearer {your_token}
```

---

## 🔄 Mise à Jour Postman

### Option 1 : Réimporter la collection mise à jour

1. Supprimez l'ancienne collection dans Postman
2. Allez dans le dossier `postman/`
3. Importez le fichier `Mercedes-Bot-API.postman_collection.json`
4. Les routes sont maintenant correctes :
   - `/api/auth/login` au lieu de `/login`
   - `/api/auth/register` au lieu de `/register`
   - `/api/auth/logout` au lieu de `/logout`

### Option 2 : Modifier manuellement

Dans Postman, modifiez les URLs :

| Ancienne URL | Nouvelle URL |
|--------------|--------------|
| `{{base_url}}/login` | `{{base_url}}/api/auth/login` |
| `{{base_url}}/register` | `{{base_url}}/api/auth/register` |
| `{{base_url}}/logout` | `{{base_url}}/api/auth/logout` |

---

## 🧪 Test dans Postman

### Étape 1 : Login

1. Ouvrez **1. Authentication > Login**
2. Vérifiez que l'URL est : `https://mbbot-dashboard.ywcdigital.com/api/auth/login`
3. Cliquez sur **Send**
4. ✅ Le token est automatiquement sauvegardé dans `{{access_token}}`

### Étape 2 : Tester les autres endpoints

Les autres endpoints protégés fonctionnent maintenant automatiquement :
- **3. Dashboard API > Get Statistics**
- **3. Dashboard API > Get Conversations**
- **4. Agent Chat > Take Over Conversation**

---

## 📝 Différences entre Routes Web et API

### Routes Web (`/login`, `/dashboard`, etc.)
- ✅ Pour les navigateurs
- ✅ Utilise les sessions Laravel
- ✅ Nécessite un token CSRF
- ✅ Redirige vers les pages HTML
- ❌ Ne fonctionne pas avec Postman

### Routes API (`/api/auth/login`, `/api/dashboard/stats`, etc.)
- ✅ Pour Postman, applications mobiles, SPA
- ✅ Utilise Laravel Sanctum (tokens)
- ✅ Pas de token CSRF nécessaire
- ✅ Retourne du JSON
- ✅ Fonctionne parfaitement avec Postman

---

## 🔐 Sécurité

### Token Sanctum

Le token Sanctum est un token Bearer personnel qui :
- Est stocké dans la base de données (table `personal_access_tokens`)
- Peut être révoqué à tout moment
- Expire selon la configuration de Sanctum
- Est unique par utilisateur et par device

### Utilisation du Token

Tous les endpoints protégés nécessitent le header :
```
Authorization: Bearer 1|abc123def456...
```

Postman ajoute automatiquement ce header grâce à la configuration de la collection.

---

## 🛠️ Pour les développeurs

### Fichiers créés/modifiés

1. **`app/Http/Controllers/Api/AuthController.php`**
   - Controller API pour l'authentification
   - Méthodes : `login()`, `register()`, `logout()`, `me()`

2. **`routes/api.php`**
   - Ajout du groupe `auth` avec les routes d'authentification

3. **`postman/Mercedes-Bot-API.postman_collection.json`**
   - Mise à jour des URLs d'authentification
   - Ajout de l'endpoint "Get User Info"

---

## 🚀 Avantages

✅ Pas de problème CSRF avec Postman
✅ Authentification stateless (idéale pour API)
✅ Support multi-devices (plusieurs tokens par utilisateur)
✅ Révocation facile des tokens
✅ Compatible avec applications mobiles et SPA
✅ Tokens stockés en base de données (traçabilité)

---

## 📚 Documentation

- **Laravel Sanctum** : https://laravel.com/docs/sanctum
- **Routes API** : `postman/API_ENDPOINTS.md`
- **Collection Postman** : `postman/README.md`

---

## ❓ FAQ

### Dois-je supprimer les routes web ?

Non ! Les routes web (`/login`, `/dashboard`, etc.) restent pour l'interface web du dashboard. Les routes API sont juste une alternative pour Postman et les tests.

### Le token expire ?

Par défaut, les tokens Sanctum n'expirent jamais, mais vous pouvez configurer une expiration dans `config/sanctum.php`.

### Puis-je avoir plusieurs tokens ?

Oui ! Chaque login crée un nouveau token. Vous pouvez avoir un token pour Postman, un pour mobile, etc.

### Comment révoquer un token ?

Utilisez l'endpoint `POST /api/auth/logout` pour révoquer le token actuel.

### Les anciennes routes fonctionnent-elles toujours ?

Oui ! Les routes web (`/login`, `/dashboard`) fonctionnent toujours dans le navigateur. Les nouvelles routes API sont une alternative pour Postman.
