# Mercedes-Benz Bot Dashboard

Application web complète pour la supervision et l'analyse du chatbot WhatsApp Mercedes-Benz.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38bdf8)

## 📋 Description

Cette application Laravel fournit un dashboard complet pour :
- 🔐 **Authentification sécurisée** avec gestion des rôles
- 📊 **Visualisation des statistiques** en temps réel
- 💬 **Monitoring des conversations** WhatsApp actives
- 🔍 **Recherche et analyse** des interactions utilisateurs
- 📈 **Graphiques et tendances** détaillés
- 🎯 **Suivi du parcours client** dans le bot

## ✨ Fonctionnalités principales

### 1. Dashboard Principal
- Vue d'ensemble des métriques clés
- Graphiques interactifs (Chart.js)
- Conversations récentes
- Filtres par période

### 2. Conversations Actives
- Monitoring en temps réel
- Détails de chaque conversation en cours
- Indicateur visuel de statut
- Historique des événements

### 3. Gestion des Conversations
- Liste complète avec pagination
- Filtres avancés (statut, type de client, date)
- Recherche multicritères
- Export des données

### 4. Détail Conversation
- Timeline complète des événements
- Informations client détaillées
- Visualisation du parcours utilisateur
- Métadonnées techniques

### 5. Statistiques Avancées
- Distribution des choix de menu
- Répartition par statut
- Heures de pointe
- Parcours les plus populaires
- Tendances quotidiennes

### 6. Recherche Intelligente
- Recherche full-text dans les saisies utilisateurs
- Filtres temporels
- Liens contextuels

## 🚀 Installation rapide

### Prérequis
- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Extension PHP : BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

### Étapes d'installation

1. **Cloner le projet** (si depuis Git) ou naviguer dans le dossier
```bash
cd laravel
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurer la base de données**
Éditez `.env` et configurez vos paramètres de connexion :
```env
DB_DATABASE=mercedes_bot
DB_USERNAME=root
DB_PASSWORD=
```

5. **Créer la base de données**
```sql
CREATE DATABASE mercedes_bot;
```

6. **Exécuter les migrations**
```bash
php artisan migrate
```

7. **Charger les données de test**
```bash
php artisan db:seed
```

8. **Lancer le serveur**
```bash
php artisan serve
```

9. **Accéder à l'application**
Ouvrez votre navigateur : `http://localhost:8000`

## 👥 Comptes de test

Après le seeding, vous pouvez vous connecter avec :

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| admin@mercedes-bot.com | password | Administrateur |
| supervisor@mercedes-bot.com | password | Superviseur |
| agent1@mercedes-bot.com | password | Agent |
| agent2@mercedes-bot.com | password | Agent |

## 📁 Structure du projet

```
laravel/
├── migrations/           # Schéma de base de données
├── models/              # Modèles Eloquent
│   ├── User.php
│   ├── Conversation.php
│   ├── ConversationEvent.php
│   └── DailyStatistic.php
├── controllers/
│   ├── Auth/           # Authentification
│   ├── Web/            # Dashboard web
│   ├── WebhookController.php
│   └── DashboardController.php
├── resources/views/    # Templates Blade
│   ├── layouts/
│   ├── auth/
│   └── dashboard/
├── routes/
│   ├── web.php        # Routes web
│   └── api.php        # Routes API
└── database/seeders/  # Données de test
```

## 🎨 Technologies

- **Framework :** Laravel 11
- **Frontend :** Blade Templates
- **CSS :** Tailwind CSS (CDN)
- **JavaScript :** Alpine.js (interactivité)
- **Graphiques :** Chart.js
- **Auth :** Laravel Sanctum
- **Base de données :** MySQL/PostgreSQL

## 📡 API Endpoints

### Webhooks (Entrée)
```
POST /api/webhook/event          # Logger un événement
POST /api/webhook/user-data      # MAJ données utilisateur
POST /api/webhook/transfer       # Marquer transfert agent
POST /api/webhook/complete       # Terminer conversation
```

### Dashboard API (Sortie)
```
GET /api/dashboard/stats              # Statistiques globales
GET /api/dashboard/conversations      # Liste conversations
GET /api/dashboard/conversations/{id} # Détail conversation
GET /api/dashboard/active             # Conversations actives
GET /api/dashboard/history            # Historique 30 jours
GET /api/dashboard/paths              # Parcours populaires
GET /api/dashboard/search-inputs      # Recherche saisies
```

**Note :** Les routes API nécessitent une authentification Sanctum.

## 🔧 Configuration

### Variables d'environnement importantes

```env
# Application
APP_NAME="Mercedes-Benz Bot Dashboard"
APP_URL=http://localhost

# Base de données
DB_CONNECTION=mysql
DB_DATABASE=mercedes_bot

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Sanctum (API)
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
```

## 📊 Modèles de données

### User
- Authentification et gestion des rôles
- Rôles : admin, supervisor, agent

### Conversation
- Session de chat complète
- Statuts : active, completed, transferred, timeout, abandoned
- Relations : events, statistics

### ConversationEvent
- Événements individuels
- Types : menu_choice, free_input, message_sent, agent_transfer, etc.
- Timeline complète

### DailyStatistic
- Métriques agrégées par jour
- Optimisé pour les graphiques
- Recalculable à la demande

## 🎯 Cas d'usage

### Pour les Administrateurs
- Vision globale des performances du bot
- Analyse des tendances
- Identification des points d'amélioration

### Pour les Superviseurs
- Monitoring des conversations actives
- Suivi des transferts agents
- Analyse des parcours clients

### Pour les Agents
- Consultation de l'historique
- Contexte avant prise en charge
- Recherche d'informations spécifiques

## 🔒 Sécurité

- Authentification requise pour toutes les routes dashboard
- Hachage des mots de passe (bcrypt)
- Protection CSRF sur tous les formulaires
- Sessions sécurisées
- Validation des entrées utilisateur
- Protection contre les injections SQL (Eloquent ORM)

## 🐛 Dépannage

### Problème de permissions
```bash
chmod -R 775 storage bootstrap/cache
```

### Erreur "Class not found"
```bash
composer dump-autoload
```

### Réinitialiser la base de données
```bash
php artisan migrate:fresh --seed
```
⚠️ Attention : Supprime toutes les données !

### Vider le cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## 📚 Documentation complémentaire

- [INSTALLATION.md](INSTALLATION.md) - Guide d'installation détaillé
- [Laravel Documentation](https://laravel.com/docs) - Documentation officielle Laravel
- [Tailwind CSS](https://tailwindcss.com/docs) - Documentation Tailwind
- [Chart.js](https://www.chartjs.org/docs) - Documentation Chart.js

## 🤝 Contribution

Ce projet est propriétaire. Pour toute modification ou amélioration, contactez l'équipe de développement Mercedes-Benz by CFAO.

## 📝 Licence

Propriétaire - Mercedes-Benz by CFAO © 2025

## 👨‍💻 Développement

Projet développé avec l'assistance de Claude (Anthropic).

---

**Version :** 2.0
**Dernière mise à jour :** Janvier 2025
**Statut :** Production Ready ✅
