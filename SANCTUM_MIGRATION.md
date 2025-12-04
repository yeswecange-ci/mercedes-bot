# 🔐 Migration Sanctum - Création de la table personal_access_tokens

## Problème

Erreur lors du login via API :
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'mercedesbot.personal_access_tokens' doesn't exist
```

## Cause

La table `personal_access_tokens` nécessaire pour Laravel Sanctum n'existe pas dans la base de données.

## ✅ Solution : Exécuter la migration

### Sur Coolify (Après le déploiement)

1. **Redéployez l'application** pour obtenir la nouvelle migration
2. **Ouvrez le Terminal Coolify**
3. **Exécutez la migration :**

```bash
php artisan migrate
```

Vous devriez voir :
```
Running migrations.
2025_12_04_135203_create_personal_access_tokens_table ............. DONE
```

### Vérifier que la table existe

```bash
php artisan tinker
```

Puis dans tinker :
```php
DB::table('personal_access_tokens')->count();
// Devrait retourner 0 (table vide mais existante)
```

Tapez `exit` pour quitter.

## 🧪 Tester à nouveau dans Postman

Après avoir exécuté la migration :

1. **POST** `https://mbbot-dashboard.ywcdigital.com/api/auth/login`
2. Body :
   ```json
   {
       "email": "admin@mercedes-bot.com",
       "password": "password123"
   }
   ```
3. ✅ **Devrait maintenant fonctionner et retourner un token**

## 📝 Structure de la table

La migration crée la table `personal_access_tokens` avec :

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | ID unique du token |
| tokenable_type | string | Type du modèle (User) |
| tokenable_id | bigint | ID de l'utilisateur |
| name | text | Nom du token (ex: "api-token") |
| token | string(64) | Hash du token (unique) |
| abilities | text | Permissions du token (JSON) |
| last_used_at | timestamp | Dernière utilisation |
| expires_at | timestamp | Date d'expiration (nullable) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de mise à jour |

## 🔍 Vérifier les tokens créés

Pour voir les tokens dans la base de données :

```bash
php artisan tinker
```

```php
DB::table('personal_access_tokens')->get();
// Liste tous les tokens créés
```

## 🗑️ Supprimer tous les tokens (si besoin)

Si vous voulez révoquer tous les tokens :

```bash
php artisan tinker
```

```php
DB::table('personal_access_tokens')->truncate();
```

Ou supprimer les tokens d'un utilisateur spécifique :

```php
\App\Models\User::find(1)->tokens()->delete();
```

## ⚠️ Important

- **Redéployez l'application** pour obtenir la migration
- **Exécutez `php artisan migrate`** dans Coolify
- La migration est **idempotente** (peut être exécutée plusieurs fois sans problème)
- Les tokens sont stockés **hashés** en base de données pour la sécurité

## 📚 Fichiers concernés

- **Migration** : `database/migrations/2025_12_04_135203_create_personal_access_tokens_table.php`
- **Model** : `app/Models/User.php` (utilise le trait `HasApiTokens`)
- **Controller** : `app/Http/Controllers/Api/AuthController.php`

## 🚀 Prochaines étapes

Une fois la migration exécutée :

1. ✅ Le login API fonctionne
2. ✅ Les tokens sont stockés en base de données
3. ✅ Vous pouvez tester tous les endpoints protégés dans Postman
4. ✅ Les utilisateurs peuvent avoir plusieurs tokens (multi-devices)

---

## 💡 Astuce : Automatiser la migration

Pour que les migrations s'exécutent automatiquement lors des déploiements futurs, ajoutez ceci dans votre script de déploiement Coolify :

```bash
php artisan migrate --force
```

Le flag `--force` permet d'exécuter les migrations en production sans confirmation.
