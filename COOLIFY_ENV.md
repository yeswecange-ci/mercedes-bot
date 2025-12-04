# Configuration Coolify - Guide Complet

## 🚨 SOLUTION POUR L'ERREUR "Vite manifest not found"

Cette erreur signifie que les assets frontend n'ont pas été compilés. Suivez ce guide.

---

## 📋 Configuration dans Coolify

### 1. Variables d'environnement

Dans **Coolify > Votre App > Environment Variables**, ajoutez/vérifiez :

```env
# Application
APP_NAME="Mercedes-Benz Bot Dashboard"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:Pa1jnEgql8OI4ZrvOR6bvDjj7SjTZpKI9xN6v3Kx4mo=
APP_URL=https://mbbot-dashboard.ywcdigital.com

# Database
DB_CONNECTION=mysql
DB_HOST=142.93.236.118
DB_PORT=3309
DB_DATABASE=mercedesbot
DB_USERNAME=mercedesbduser
DB_PASSWORD=KPeeICwVGGU9m2zPcsLhGcvEakDEt3e69RBksHCzcuZ7GPbeXxNDXEDVpyGgutRu

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Twilio (si utilisé)
TWILIO_ACCOUNT_SID=votre_sid
TWILIO_AUTH_TOKEN=votre_token
TWILIO_WHATSAPP_NUMBER=+2250716700900
```

### 2. Commandes de Build

Dans **Coolify > Votre App > Build Settings** :

#### **Option A : Utiliser le script deploy.sh (RECOMMANDÉ)**

```bash
chmod +x deploy.sh && ./deploy.sh
```

#### **Option B : Commandes manuelles**

**Install Command:**
```bash
composer install --no-dev --optimize-autoloader --no-interaction && npm install
```

**Build Command:**
```bash
npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

**Start Command:**
```bash
php artisan serve --host=0.0.0.0 --port=8080
```

---

## 🔧 Solution Immédiate (Via Terminal Coolify)

Si l'application est déjà déployée mais affiche l'erreur, exécutez dans le **Terminal Coolify** :

```bash
# Rendre le script exécutable
chmod +x deploy.sh

# Exécuter le déploiement complet
./deploy.sh
```

Ou manuellement :

```bash
# 1. Installer les dépendances
npm install

# 2. Compiler les assets
npm run build

# 3. Vérifier que ça a marché
ls -la public/build/manifest.json

# 4. Nettoyer les caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 5. Optimiser
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ✅ Vérification post-déploiement

Dans le terminal Coolify, vérifiez :

```bash
# Le manifest doit exister
cat public/build/manifest.json

# Les assets doivent être présents
ls -la public/build/assets/

# Résultat attendu :
# app-xxxxx.css
# app-xxxxx.js
```

---

## 🐛 Dépannage

### Erreur persiste après npm run build ?

```bash
# Vérifier les logs de build
npm run build 2>&1 | tee build.log

# Vérifier Node.js version (doit être >= 18)
node -v

# Vérifier NPM version
npm -v
```

### Assets se chargent mais pas de styles ?

```bash
# Vérifier le APP_ENV
php artisan config:show | grep app.env

# Doit afficher : production
```

### Toujours l'erreur 500 ?

```bash
# Voir les logs Laravel
tail -50 storage/logs/laravel.log

# Activer le debug temporairement
# Puis recharger la page pour voir l'erreur exacte
```
