# 🚨 FIX URGENT : Erreur "Vite manifest not found"

## Problème
L'application affiche une erreur 500 avec le message : `Vite manifest not found at: /app/public/build/manifest.json`

## Cause
Les assets frontend (CSS/JS) n'ont pas été compilés pendant le déploiement.

## ✅ Solution Rapide (2 minutes)

### Dans le Terminal Coolify :

```bash
# Étape 1 : Compiler les assets
npm install && npm run build

# Étape 2 : Vérifier que ça a marché
ls -la public/build/manifest.json

# Si le fichier existe, passez à l'étape 3
# Si le fichier n'existe pas, il y a un problème avec npm ou node

# Étape 3 : Nettoyer les caches Laravel
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Étape 4 : Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Rechargez votre navigateur. L'application devrait fonctionner ! 🎉

---

## 🔧 Solution Permanente

Pour que les assets soient toujours compilés lors des futurs déploiements :

### Dans Coolify > Build Settings

Configurez les commandes suivantes :

**Install Command:**
```bash
composer install --no-dev --optimize-autoloader --no-interaction && npm install
```

**Build Command:**
```bash
npm run build && php artisan config:cache && php artisan route:cache
```

**Start Command:**
```bash
php artisan serve --host=0.0.0.0 --port=8080
```

### OU utilisez le script automatisé :

```bash
chmod +x deploy.sh && ./deploy.sh
```

---

## 📝 Vérifications après le fix

Une fois que vous avez exécuté les commandes ci-dessus :

1. ✅ Vérifier que le manifest existe :
   ```bash
   cat public/build/manifest.json
   ```

2. ✅ Vérifier que les assets sont là :
   ```bash
   ls -la public/build/assets/
   # Vous devriez voir :
   # app-xxxxx.css
   # app-xxxxx.js
   ```

3. ✅ Tester l'application dans le navigateur

---

## ❌ Si ça ne marche toujours pas

### Vérifier Node.js et NPM :
```bash
node -v  # Doit être >= 18
npm -v   # Doit être >= 9
```

### Voir les erreurs de build :
```bash
npm run build 2>&1 | tee build-error.log
cat build-error.log
```

### Voir les logs Laravel :
```bash
tail -100 storage/logs/laravel.log
```

### Activer le mode debug pour voir l'erreur exacte :
Dans Coolify > Environment Variables, changez temporairement :
```
APP_DEBUG=true
```
Redéployez et rechargez la page pour voir l'erreur complète.

**N'oubliez pas de remettre `APP_DEBUG=false` après !**
