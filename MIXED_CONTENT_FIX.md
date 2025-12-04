# 🔒 Fix : CSS bloqué (Mixed Content)

## Problème identifié

Dans l'onglet Network du navigateur, le fichier **app-fN596C0N.css** apparaît avec le statut **"blocked"**.

### Cause

**Mixed Content** : Le site est en HTTPS (`https://mbbot-dashboard.ywcdigital.com`) mais Laravel générait des URLs en HTTP (`http://...`) pour les assets, ce que les navigateurs modernes bloquent pour des raisons de sécurité.

### Pourquoi ça arrive ?

Coolify utilise un reverse proxy (Traefik/Nginx) qui :
1. Reçoit les requêtes HTTPS des clients
2. Les transmet en HTTP à l'application Laravel
3. Laravel pensait donc être en HTTP et générait des URLs HTTP

---

## ✅ Solution appliquée

### 1. Configuration du Proxy (`TrustProxies.php`)

Configuré Laravel pour faire confiance au proxy de Coolify :

```php
protected $proxies = '*';
```

Cela permet à Laravel de détecter correctement le protocole HTTPS via les headers `X-Forwarded-Proto`.

### 2. Force HTTPS (`AppServiceProvider.php`)

Ajout de la directive pour forcer HTTPS en production :

```php
if (config('app.env') === 'production') {
    URL::forceScheme('https');
}
```

Toutes les URLs générées utilisent maintenant HTTPS.

### 3. Middleware HTTPS (`ForceHttps.php`)

Créé un middleware qui :
- Redirige les requêtes HTTP vers HTTPS (301)
- Ajoute des headers de sécurité (HSTS, X-Content-Type-Options, etc.)

### 4. Activation du middleware (`Kernel.php`)

Ajouté `ForceHttps` au groupe middleware `web`.

---

## 🚀 Déploiement

### Étape 1 : Vérifier les variables d'environnement

Dans **Coolify > Environment Variables**, assurez-vous que :

```env
APP_ENV=production
APP_URL=https://mbbot-dashboard.ywcdigital.com
```

⚠️ **IMPORTANT** : `APP_URL` DOIT commencer par `https://`

### Étape 2 : Redéployer

1. Dans Coolify, cliquez sur **Deploy**
2. L'application va redémarrer avec les nouvelles configurations

### Étape 3 : Nettoyer les caches (dans le Terminal Coolify)

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### Étape 4 : Tester

1. Ouvrez https://mbbot-dashboard.ywcdigital.com/login
2. Appuyez sur **Ctrl+Shift+R** (hard refresh)
3. Ouvrez **F12 > Network**
4. Vérifiez que :
   - ✅ `app-fN596C0N.css` est **200 OK** (plus bloqué)
   - ✅ `app-kGY04szw.js` est **200 OK**
   - ✅ Les styles s'appliquent correctement

---

## 🔍 Vérification

### Dans le navigateur (F12 > Console)

Vous ne devriez plus voir d'erreur comme :
```
Mixed Content: The page at 'https://...' was loaded over HTTPS, but requested an insecure stylesheet 'http://...'.
```

### Dans le Terminal Coolify

```bash
# Vérifier que les URLs générées sont en HTTPS
php artisan tinker
>>> url('build/assets/app-fN596C0N.css')
=> "https://mbbot-dashboard.ywcdigital.com/build/assets/app-fN596C0N.css"
```

Doit commencer par `https://` ✅

---

## 📝 Résumé des changements

| Fichier | Modification |
|---------|--------------|
| `app/Http/Middleware/TrustProxies.php` | `$proxies = '*'` |
| `app/Providers/AppServiceProvider.php` | Force HTTPS en production |
| `app/Http/Middleware/ForceHttps.php` | Nouveau middleware |
| `app/Http/Kernel.php` | Ajout du middleware ForceHttps |

---

## 🎯 Résultat attendu

✅ CSS chargé sans blocage
✅ Styles Tailwind appliqués
✅ JavaScript Alpine.js fonctionnel
✅ Toutes les URLs en HTTPS
✅ Headers de sécurité ajoutés
