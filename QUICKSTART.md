# 🚀 Démarrage Rapide - Mercedes-Benz Bot Dashboard

## ✅ Ce qui est déjà fait

- ✅ Dossiers storage créés
- ✅ Composer installé
- ✅ Clé d'application générée
- ✅ Structure Laravel complète

## 📋 Prochaines étapes (5 minutes)

### 1. Configurer la base de données

Éditez le fichier `.env` et modifiez ces lignes :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mercedes_bot
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

### 2. Créer la base de données

Ouvrez MySQL et exécutez :

```sql
CREATE DATABASE mercedes_bot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**OU via la ligne de commande :**

```bash
mysql -u root -p -e "CREATE DATABASE mercedes_bot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 3. Exécuter les migrations

```bash
php artisan migrate
```

Cela va créer les tables :
- users
- conversations
- conversation_events
- daily_statistics
- password_reset_tokens
- sessions

### 4. Charger les utilisateurs de test

```bash
php artisan db:seed
```

Cela va créer 4 utilisateurs :
- admin@mercedes-bot.com (Admin)
- supervisor@mercedes-bot.com (Superviseur)
- agent1@mercedes-bot.com (Agent)
- agent2@mercedes-bot.com (Agent)

**Mot de passe pour tous :** `password`

### 5. Lancer le serveur

```bash
php artisan serve
```

### 6. Accéder à l'application

Ouvrez votre navigateur à l'adresse :
**http://localhost:8000**

## 🎯 Connexion

Utilisez un de ces comptes :

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| admin@mercedes-bot.com | password | Administrateur |
| supervisor@mercedes-bot.com | password | Superviseur |
| agent1@mercedes-bot.com | password | Agent |
| agent2@mercedes-bot.com | password | Agent |

## 🔧 Résolution de problèmes

### Erreur de connexion à la base de données

Vérifiez que :
- MySQL est démarré
- Le nom d'utilisateur et mot de passe sont corrects dans `.env`
- La base de données `mercedes_bot` existe

### Erreur de permissions

```bash
chmod -R 775 storage bootstrap/cache
```

### Erreur "Class not found"

```bash
composer dump-autoload
```

### Réinitialiser complètement

⚠️ **ATTENTION : Supprime toutes les données !**

```bash
php artisan migrate:fresh --seed
```

## 📚 Documentation complète

- **INSTALLATION.md** - Guide d'installation détaillé
- **README.md** - Documentation complète du projet
- **STRUCTURE.md** - Structure des fichiers

## 🎉 C'est tout !

Une fois ces étapes complétées, votre application est prête à fonctionner !

---

**Besoin d'aide ?** Consultez les fichiers de documentation ou les logs dans `storage/logs/laravel.log`
