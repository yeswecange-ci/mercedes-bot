# 🎨 Les styles ne s'appliquent pas - Solution

## Diagnostic

✅ **Bonne nouvelle** : Plus d'erreur 500
✅ Les fichiers CSS/JS sont bien accessibles :
- https://mbbot-dashboard.ywcdigital.com/build/manifest.json
- https://mbbot-dashboard.ywcdigital.com/build/assets/app-fN596C0N.css

❌ **Problème** : Laravel ne charge pas les assets correctement

---

## 🔍 Diagnostic rapide

### Dans le Terminal Coolify, exécutez :

```bash
chmod +x quick-fix.sh && ./quick-fix.sh
```

Ce script va :
1. Vérifier que APP_ENV=production
2. Vérifier que le manifest existe
3. Nettoyer tous les caches
4. Reconstruire les caches
5. Afficher un diagnostic complet

---

## 🚨 Solution la plus probable : APP_ENV

Le problème le plus fréquent est que **APP_ENV n'est pas défini sur "production"** dans Coolify.

### Dans Coolify > Environment Variables :

**Vérifiez/Ajoutez ces variables :**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mbbot-dashboard.ywcdigital.com
```

**⚠️ IMPORTANT : Après avoir modifié les variables d'environnement :**

1. Sauvegardez les modifications
2. Dans le Terminal Coolify :
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan config:cache
   ```
3. Redémarrez l'application dans Coolify

---

## 🧪 Test manuel

Si le script ne fonctionne pas, testez manuellement :

```bash
# 1. Vérifier l'environnement
php artisan config:show | grep app.env
# Doit afficher : production

# 2. Vérifier que le manifest existe
cat public/build/manifest.json

# 3. Tester la génération des assets
php check-vite.php

# 4. Nettoyer et reconstruire
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan view:cache
```

---

## 🔍 Diagnostic détaillé

```bash
# Exécutez ce script pour un diagnostic complet
php check-vite.php
```

Le script va vérifier :
- ✓ APP_ENV est bien "production"
- ✓ Le manifest.json existe
- ✓ Les fichiers CSS/JS sont présents
- ✓ Laravel peut générer les bonnes URLs

---

## 🌐 Test dans le navigateur

1. Ouvrez https://mbbot-dashboard.ywcdigital.com/login
2. Ouvrez les **Outils de développement** (F12)
3. Allez dans l'onglet **Network** (Réseau)
4. Rechargez la page avec **Ctrl+Shift+R** (hard refresh)
5. Cherchez les fichiers :
   - `app-fN596C0N.css` - doit être **200 OK** et **~38 KB**
   - `app-kGY04szw.js` - doit être **200 OK** et **~81 KB**

**Si les fichiers sont en 404** : Le problème vient des URLs générées par Laravel

**Si les fichiers sont en 200** mais pas de styles : Problème de cache navigateur

---

## ✅ Checklist de vérification

Dans Coolify :

- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] APP_URL=https://mbbot-dashboard.ywcdigital.com
- [ ] Exécuté `php artisan config:clear`
- [ ] Exécuté `php artisan cache:clear`
- [ ] Exécuté `php artisan config:cache`
- [ ] Hard refresh du navigateur (Ctrl+Shift+R)

---

## 💡 Solution alternative : Forcer les assets

Si rien ne fonctionne, on peut forcer Laravel à utiliser les bons chemins.

Contactez-moi avec le résultat de `php check-vite.php` pour une solution personnalisée.
