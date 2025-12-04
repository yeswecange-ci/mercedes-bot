# Guide de dépannage - Erreur 500

## Diagnostic rapide dans Coolify

### Étape 1 : Vérifier les logs

Dans Coolify, allez dans votre application > **Logs** et cherchez les erreurs récentes.

### Étape 2 : Accéder au terminal du conteneur

Dans Coolify, ouvrez un **Terminal** dans votre application et exécutez :

```bash
# Rendre le script de diagnostic exécutable
chmod +x diagnose.sh

# Exécuter le diagnostic
./diagnose.sh
```

## Causes communes d'erreur 500

### 1. APP_KEY manquant ou invalide

**Symptômes :** Erreur "No application encryption key has been specified"

**Solution :**
```bash
php artisan key:generate --force
php artisan config:clear
```

### 2. Permissions incorrectes sur storage/

**Symptômes :** Erreur "Unable to write to storage/logs/laravel.log"

**Solution :**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 3. Fichier .env manquant

**Symptômes :** Erreur "The environment file is missing"

**Solution :** Vérifier que toutes les variables d'environnement sont configurées dans Coolify

### 4. Base de données inaccessible

**Symptômes :** Erreur "SQLSTATE[HY000] [2002] Connection refused"

**Solution :** Vérifier les variables DB_* dans Coolify :
```env
DB_CONNECTION=mysql
DB_HOST=142.93.236.118
DB_PORT=3309
DB_DATABASE=mercedesbot
DB_USERNAME=mercedesbduser
DB_PASSWORD=KPeeICwVGGU9m2zPcsLhGcvEakDEt3e69RBksHCzcuZ7GPbeXxNDXEDVpyGgutRu
```

### 5. Assets/Manifest manquant

**Symptômes :** Erreur "Vite manifest not found"

**Solution :**
```bash
# Vérifier que le build a été exécuté
ls -la public/build/

# Si vide, rebuilder les assets
npm install
npm run build
```

### 6. Cache corrompu

**Symptômes :** Erreurs variées après un déploiement

**Solution :**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

## Commandes de dépannage complètes

Exécutez ces commandes dans l'ordre dans le terminal Coolify :

```bash
# 1. Nettoyer tous les caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 2. Vérifier les permissions
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/build

# 3. Régénérer les caches de configuration
php artisan config:cache
php artisan route:cache

# 4. Tester la connexion à la base de données
php artisan migrate:status

# 5. Vérifier que les assets sont compilés
ls -la public/build/manifest.json
```

## Obtenir plus d'informations

Pour voir l'erreur exacte, activez temporairement le mode debug :

1. Dans Coolify, ajoutez/modifiez la variable :
   ```env
   APP_DEBUG=true
   ```

2. Redéployez

3. Rechargez la page pour voir l'erreur détaillée

4. **IMPORTANT :** Une fois le problème identifié, remettez :
   ```env
   APP_DEBUG=false
   ```

## Vérifier les logs Laravel

Dans le terminal Coolify :
```bash
tail -100 storage/logs/laravel.log
```

Ou voir les logs en temps réel :
```bash
tail -f storage/logs/laravel.log
```
