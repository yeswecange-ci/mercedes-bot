# Variables d'environnement Coolify

## Variables critiques pour les assets en production

Pour que les styles et JavaScript s'appliquent correctement en production, assurez-vous que ces variables sont configurées dans Coolify :

```env
# Environnement - DOIT être "production"
APP_ENV=production
APP_DEBUG=false

# URL de l'application - DOIT correspondre à votre domaine
APP_URL=https://mbbot-dashboard.ywcdigital.com

# Asset URL (optionnel mais recommandé)
ASSET_URL=https://mbbot-dashboard.ywcdigital.com
```

## Configuration de build dans Coolify

Dans les paramètres de build de votre application Coolify, assurez-vous que :

1. **Install Command** inclut :
   ```bash
   composer install --no-dev --optimize-autoloader --no-interaction
   npm install
   ```

2. **Build Command** inclut :
   ```bash
   npm run build
   ```

3. **Start Command** :
   ```bash
   php artisan serve --host=0.0.0.0 --port=8080
   ```

## Vérification post-déploiement

Après le déploiement, vérifiez que :
- Le dossier `public/build` existe et contient les fichiers compilés
- Le fichier `public/build/manifest.json` est présent
- Les assets CSS et JS sont accessibles via `https://mbbot-dashboard.ywcdigital.com/build/assets/app-*.css` et `.js`

## Commandes de dépannage

Si les assets ne se chargent toujours pas :

```bash
# Dans le conteneur Coolify
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize
```
