# 🚀 Guide d'Intégration Twilio - Mercedes-Benz Bot Dashboard

## 📋 Table des Matières
1. [Prérequis](#prérequis)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Déploiement](#déploiement)
5. [Configuration Twilio](#configuration-twilio)
6. [Utilisation](#utilisation)
7. [Webhooks disponibles](#webhooks-disponibles)
8. [Troubleshooting](#troubleshooting)

---

## 🔧 Prérequis

- PHP 8.1+
- Composer
- MySQL 8.0+
- Node.js 18+ & NPM
- Compte Twilio actif
- Sandbox WhatsApp Twilio activé
- Domaine : `https://mbbot-dashboard.ywcdigital.com`

---

## 📦 Installation

### 1. Installer le SDK Twilio

```bash
cd laravel
composer require twilio/sdk
```

### 2. Copier et configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configurer la base de données

Éditez `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mercedes_bot
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

### 4. Migrer la base de données

```bash
php artisan migrate
```

**Note importante:** Si vous mettez à jour une installation existante, assurez-vous que la colonne `agent_id` existe dans la table `conversations`. La migration récente l'ajoute automatiquement.

### 5. Créer un utilisateur admin

```bash
php artisan tinker
```

Puis :

```php
\App\Models\User::create([
    'name' => 'Admin Mercedes',
    'email' => 'admin@mercedes.com',
    'password' => bcrypt('votre_mot_de_passe_secure'),
    'role' => 'admin',
]);
```

---

## ⚙️ Configuration

### 1. Variables d'environnement Twilio

Éditez `.env` et ajoutez vos credentials Twilio :

```env
# App Configuration
APP_NAME="Mercedes-Benz Bot Dashboard"
APP_URL=https://mbbot-dashboard.ywcdigital.com

# Twilio Configuration
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_WHATSAPP_NUMBER=+14155238886
TWILIO_PHONE_NUMBER=+1234567890
```

### 2. Trouver vos credentials Twilio

1. Connectez-vous à [Twilio Console](https://console.twilio.com/)
2. Dashboard → Account Info → copier :
   - **Account SID**
   - **Auth Token**
3. WhatsApp → Senders → copier le numéro WhatsApp

---

## 🌐 Déploiement

### 1. Build les assets

```bash
npm install
npm run build
```

### 2. Optimiser Laravel pour production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Permissions (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Configuration Nginx (exemple)

```nginx
server {
    listen 80;
    listen 443 ssl;
    server_name mbbot-dashboard.ywcdigital.com;

    root /var/www/mercedes-bot/laravel/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    ssl_certificate /etc/letsencrypt/live/mbbot-dashboard.ywcdigital.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/mbbot-dashboard.ywcdigital.com/privkey.pem;
}
```

---

## 📱 Configuration Twilio

### 1. Importer le Flow Twilio

Vous avez deux options de flow disponibles :

#### Option A: Flow avec mode agent (Recommandé)
Ce flow gère automatiquement le basculement entre le bot et les agents humains.

1. Aller sur [Twilio Console - Studio](https://console.twilio.com/us1/develop/studio/flows)
2. Cliquer sur **Create new Flow**
3. Choisir **Import from JSON**
4. Coller le contenu de `twilio-flow-agent-mode.json`
5. Cliquer sur **Next** puis **Publish**

#### Option B: Flow simple (Sans support agent)
Ce flow ne gère que les conversations automatiques sans transfert d'agent.

1. Utiliser `twilio-flow-updated.json` au lieu de `twilio-flow-agent-mode.json`

### 2. Configuration du Sandbox WhatsApp

1. Aller sur [WhatsApp Sandbox](https://console.twilio.com/us1/develop/sms/try-it-out/whatsapp-learn)
2. Dans **Sandbox Configuration** :
   - **WHEN A MESSAGE COMES IN** : Select "Studio Flow"
   - Choisir votre Flow importé
3. Sauvegarder

### 3. Webhooks Laravel disponibles

Toutes les routes sont sous : `https://mbbot-dashboard.ywcdigital.com/api/twilio/`

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/twilio/incoming` | POST | Message WhatsApp entrant |
| `/api/twilio/menu-choice` | POST | Choix de menu utilisateur |
| `/api/twilio/free-input` | POST | Saisie libre utilisateur |
| `/api/twilio/agent-transfer` | POST | Transfert vers agent humain |
| `/api/twilio/complete` | POST | Fin de conversation |
| `/api/twilio/send-message` | POST | Envoyer un message (Auth requise) |

---

## 🔄 Utilisation

### Flux de conversation automatique (Bot)

1. **Client envoie "mercedes" sur WhatsApp**
   ```
   Client → Twilio → Laravel API (/incoming) → BDD
   ```

2. **Laravel stocke la conversation**
   - Crée une entrée dans `conversations`
   - Génère un `session_id`
   - Vérifie si la conversation est en mode agent
   - Retourne les données à Twilio avec flag `agent_mode`

3. **Twilio exécute le sous-flow (si agent_mode = false)**
   - Affiche le menu principal
   - À chaque choix → appel API `/menu-choice`
   - Saisies libres → appel API `/free-input`

4. **Fin de conversation**
   ```
   Twilio → Laravel API (/complete) → Calcul durée → Statut "completed"
   ```

### Flux de conversation avec agent (Mode humain)

1. **Agent prend en charge la conversation**
   - Depuis le dashboard : bouton "Prendre en charge"
   - Route: `POST /dashboard/chat/{id}/take-over`
   - Statut conversation : `active` → `transferred`
   - Message automatique envoyé au client

2. **Client envoie un message**
   ```
   Client → Twilio → Laravel API (/incoming) → Détecte agent_mode=true
   ```
   - Twilio répond : "Votre message a été reçu. Un agent vous répondra sous peu."
   - Message visible dans l'interface de chat du dashboard
   - Auto-refresh toutes les 5 secondes

3. **Agent répond via le dashboard**
   - Interface de chat en temps réel
   - Route: `POST /dashboard/chat/{id}/send`
   - Message envoyé via Twilio API
   - Enregistré dans `conversation_events`

4. **Clôture de la conversation**
   - Agent clique sur "Clôturer"
   - Route: `POST /dashboard/chat/{id}/close`
   - Statut : `transferred` → `completed`
   - Message de fermeture envoyé au client

### Envoyer un message depuis le Dashboard

Depuis le dashboard, vous pouvez répondre aux clients :

```javascript
// Exemple API call
POST https://mbbot-dashboard.ywcdigital.com/api/twilio/send-message
Headers: {
  "Authorization": "Bearer YOUR_SANCTUM_TOKEN",
  "Content-Type": "application/json"
}
Body: {
  "phone_number": "+212XXXXXXXXX",
  "message": "Bonjour, comment puis-je vous aider ?",
  "conversation_id": 123
}
```

---

## 🛠 Webhooks disponibles

### 1. Message entrant

**Endpoint:** `POST /api/twilio/incoming`

**Paramètres:**
```json
{
  "From": "whatsapp:+212XXXXXXXXX",
  "Body": "mercedes",
  "MessageSid": "SMxxxxxxxxxxxxxxxx",
  "ProfileName": "John Doe"
}
```

**Réponse:**
```json
{
  "success": true,
  "conversation_id": 123,
  "session_id": "session_abc123",
  "phone_number": "+212XXXXXXXXX",
  "current_menu": "main_menu",
  "is_client": false,
  "profile_name": "John Doe",
  "message": "mercedes"
}
```

### 2. Choix de menu

**Endpoint:** `POST /api/twilio/menu-choice`

**Paramètres:**
```json
{
  "conversation_id": 123,
  "menu_choice": "vehicules_neufs",
  "user_input": "1"
}
```

### 3. Saisie libre

**Endpoint:** `POST /api/twilio/free-input`

**Paramètres:**
```json
{
  "conversation_id": 123,
  "user_input": "jean.dupont@email.com",
  "widget_name": "collect_email"
}
```

### 4. Transfert agent

**Endpoint:** `POST /api/twilio/agent-transfer`

**Paramètres:**
```json
{
  "conversation_id": 123,
  "reason": "demande_complexe"
}
```

### 5. Compléter conversation

**Endpoint:** `POST /api/twilio/complete`

**Paramètres:**
```json
{
  "conversation_id": 123
}
```

---

## 🔐 Sécurité

### Validation des signatures Twilio (Recommandé)

Pour sécuriser vos webhooks, créez un middleware :

```bash
php artisan make:middleware ValidateTwilioSignature
```

**app/Http/Middleware/ValidateTwilioSignature.php:**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Twilio\Security\RequestValidator;

class ValidateTwilioSignature
{
    public function handle(Request $request, Closure $next)
    {
        $validator = new RequestValidator(config('services.twilio.auth_token'));

        $signature = $request->header('X-Twilio-Signature');
        $url = $request->fullUrl();
        $params = $request->all();

        if (!$validator->validate($signature, $url, $params)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        return $next($request);
    }
}
```

Ensuite dans `routes/api.php` :

```php
Route::prefix('twilio')->middleware(['validate.twilio.signature'])->group(function () {
    // vos routes...
});
```

---

## 🐛 Troubleshooting

### Problème : Webhooks ne reçoivent rien

**Solution :**
1. Vérifier que l'URL est accessible : `curl https://mbbot-dashboard.ywcdigital.com/api/health`
2. Vérifier les logs Laravel : `tail -f storage/logs/laravel.log`
3. Tester avec Postman

### Problème : Erreur 500 sur les webhooks

**Solution :**
1. Désactiver le cache : `php artisan config:clear && php artisan cache:clear`
2. Vérifier les permissions : `chmod -R 775 storage`
3. Vérifier le `.env` (DB credentials)

### Problème : Messages ne s'envoient pas

**Solution :**
1. Vérifier les credentials Twilio dans `.env`
2. Vérifier le numéro WhatsApp Twilio
3. Tester manuellement :

```bash
php artisan tinker
```

```php
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

echo $message->sid;
```

### Logs utiles

```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Logs Nginx
tail -f /var/log/nginx/error.log

# Logs PHP-FPM
tail -f /var/log/php8.1-fpm.log
```

---

## 📊 Dashboard

Accédez au dashboard sur : **https://mbbot-dashboard.ywcdigital.com**

Fonctionnalités disponibles :
- ✅ Tableau de bord en temps réel
- ✅ Conversations actives
- ✅ Historique complet
- ✅ Statistiques détaillées
- ✅ Recherche dans les messages
- ✅ Graphiques interactifs

---

## 🆘 Support

Pour toute question ou problème :
1. Vérifier les logs (`storage/logs/laravel.log`)
2. Consulter la [documentation Twilio](https://www.twilio.com/docs/whatsapp)
3. Vérifier que tous les webhooks sont correctement configurés

---

**Version:** 1.0.0
**Dernière mise à jour:** 2025
**Auteur:** Mercedes-Benz Bot Team
