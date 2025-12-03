# Mercedes-Benz Bot Dashboard - Guide d'installation

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- MySQL ou PostgreSQL
- Node.js et NPM (optionnel, pour le développement frontend)

## Installation

### 1. Installation de Laravel (si pas déjà installé)

Si vous n'avez pas encore une installation Laravel complète, vous devez d'abord créer le projet Laravel :

```bash
# Dans le dossier mercedes-bot
composer create-project laravel/laravel temp-laravel
```

Ensuite, copiez les fichiers nécessaires :
```bash
# Copiez les fichiers de base Laravel dans le dossier laravel/
cp -r temp-laravel/* laravel/
rm -rf temp-laravel
```

### 2. Configuration de l'environnement

Créez votre fichier `.env` dans le dossier `laravel/` :

```bash
cd laravel
cp .env.example .env
```

Modifiez le fichier `.env` avec vos paramètres :

```env
APP_NAME="Mercedes-Benz Bot Dashboard"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mercedes_bot
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### 3. Génération de la clé d'application

```bash
php artisan key:generate
```

### 4. Installation des dépendances

```bash
composer install
```

### 5. Création de la base de données

Créez une base de données MySQL :

```sql
CREATE DATABASE mercedes_bot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Exécution des migrations

```bash
php artisan migrate
```

Cela créera les tables suivantes :
- `users` - Utilisateurs de l'application
- `conversations` - Conversations WhatsApp
- `conversation_events` - Événements des conversations
- `daily_statistics` - Statistiques quotidiennes
- `password_reset_tokens` - Tokens de réinitialisation de mot de passe
- `sessions` - Sessions utilisateur

### 7. Chargement des données de test

Pour créer des utilisateurs de test :

```bash
php artisan db:seed
```

Cela créera les utilisateurs suivants :

| Nom | Email | Mot de passe | Rôle |
|-----|-------|--------------|------|
| Administrateur | admin@mercedes-bot.com | password | admin |
| Superviseur | supervisor@mercedes-bot.com | password | supervisor |
| Agent 1 | agent1@mercedes-bot.com | password | agent |
| Agent 2 | agent2@mercedes-bot.com | password | agent |

### 8. Lancement du serveur

```bash
php artisan serve
```

L'application sera accessible à l'adresse : `http://localhost:8000`

## Structure du projet

```
laravel/
├── migrations/              # Migrations de base de données
│   ├── create_users_table.php
│   ├── create_conversations_table.php
│   ├── create_conversation_events_table.php
│   └── create_daily_statistics_table.php
├── models/                  # Modèles Eloquent
│   ├── User.php
│   ├── Conversation.php
│   ├── ConversationEvent.php
│   └── DailyStatistic.php
├── controllers/            # Contrôleurs
│   ├── Auth/              # Authentification
│   │   ├── LoginController.php
│   │   └── RegisterController.php
│   ├── Web/               # Dashboard web
│   │   └── DashboardWebController.php
│   ├── WebhookController.php
│   └── DashboardController.php (API)
├── resources/views/        # Vues Blade
│   ├── layouts/
│   │   └── app.blade.php
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   └── dashboard/
│       ├── index.blade.php
│       ├── active.blade.php
│       ├── conversations.blade.php
│       ├── show.blade.php
│       ├── statistics.blade.php
│       └── search.blade.php
├── routes/
│   ├── web.php            # Routes web
│   └── api.php            # Routes API
└── database/seeders/      # Seeders
    ├── DatabaseSeeder.php
    └── UserSeeder.php
```

## Fonctionnalités

### 1. Authentification
- Connexion/Déconnexion
- Inscription de nouveaux utilisateurs
- 3 niveaux de rôles : Admin, Superviseur, Agent

### 2. Dashboard principal
- Vue d'ensemble des statistiques
- Graphiques de distribution des menus
- Tendances quotidiennes
- Conversations récentes

### 3. Conversations actives
- Monitoring en temps réel
- Liste des conversations en cours
- Détails de chaque conversation active

### 4. Liste des conversations
- Filtres avancés (statut, type de client, date)
- Recherche par nom, téléphone, email
- Pagination

### 5. Détail d'une conversation
- Informations client complètes
- Timeline complète des événements
- Parcours client visualisé
- Métadonnées de chaque événement

### 6. Statistiques détaillées
- Graphiques avancés
- Distribution des choix de menu
- Répartition par statut
- Heures de pointe
- Parcours populaires

### 7. Recherche
- Recherche dans les saisies texte des utilisateurs
- Filtres par date
- Liens vers les conversations complètes

## API Webhooks

L'application expose également des endpoints API pour recevoir les webhooks de n8n/Twilio :

```
POST /api/webhook/event          - Logger un événement
POST /api/webhook/user-data      - Mettre à jour les données utilisateur
POST /api/webhook/transfer       - Marquer un transfert agent
POST /api/webhook/complete       - Terminer une conversation
```

## API Dashboard

Des endpoints API sont disponibles pour l'intégration avec d'autres systèmes :

```
GET /api/dashboard/stats              - Statistiques globales
GET /api/dashboard/conversations      - Liste des conversations
GET /api/dashboard/conversations/{id} - Détail d'une conversation
GET /api/dashboard/active             - Conversations actives
GET /api/dashboard/history            - Historique des tendances
GET /api/dashboard/paths              - Parcours populaires
GET /api/dashboard/search-inputs      - Recherche dans les saisies
```

**Note :** Les routes API sont protégées par Laravel Sanctum. Vous devez générer un token d'authentification pour y accéder.

## Technologies utilisées

- **Backend :** Laravel 11
- **Frontend :** Blade templates, Tailwind CSS (CDN)
- **Graphiques :** Chart.js (CDN)
- **Interactions :** Alpine.js (CDN)
- **Base de données :** MySQL/PostgreSQL
- **Authentification :** Laravel Sanctum

## Maintenance

### Rafraîchir les statistiques quotidiennes

Vous pouvez recalculer les statistiques pour une date spécifique :

```bash
php artisan tinker
>>> \App\Models\DailyStatistic::recalculateForDate('2025-01-15');
```

### Créer un nouvel utilisateur admin

```bash
php artisan tinker
>>> \App\Models\User::create([
    'name' => 'Nouvel Admin',
    'email' => 'nouvel.admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);
```

## Dépannage

### Erreur "Class not found"

```bash
composer dump-autoload
```

### Erreur de permissions

```bash
chmod -R 775 storage bootstrap/cache
```

### Erreur de migration

```bash
php artisan migrate:fresh --seed
```
⚠️ Attention : Cette commande supprime toutes les données !

## Support

Pour toute question ou problème, consultez la documentation Laravel : https://laravel.com/docs

## Licence

Ce projet est propriétaire de Mercedes-Benz by CFAO.
