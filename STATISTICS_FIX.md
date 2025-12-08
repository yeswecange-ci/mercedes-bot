# Correction des Statistiques - Guide de Déploiement

Date : 08 Décembre 2025

## 🎯 Problème Identifié

La page des statistiques ne s'affichait pas correctement car :
1. ❌ La table `daily_statistics` était vide (aucune donnée)
2. ❌ Les statistiques de menus n'étaient pas calculées à cause d'un filtre trop restrictif

## ✅ Solution Implémentée

### 1. Nouvelle Commande Artisan : `stats:calculate`

**Fichier créé :** `app/Console/Commands/CalculateDailyStatistics.php`

**Fonctionnalités :**
- ✅ Calcule automatiquement toutes les statistiques quotidiennes
- ✅ Peuple la table `daily_statistics` à partir des conversations existantes
- ✅ Calcule les distributions de menus (Véhicules neufs, SAV, Réclamations, etc.)
- ✅ Calcule les distributions de statuts (completed, transferred, timeout, etc.)
- ✅ Calcule les durées moyennes de session
- ✅ Compte les clients vs non-clients
- ✅ Compte les erreurs et saisies invalides
- ✅ Support de plages de dates personnalisées
- ✅ Option de recalcul forcé

**Correction du bug :**
- Suppression du filtre `where('menu_name', 'menu_prin')` car ce champ est souvent vide
- Les événements `menu_choice` avec `user_input` 1-5 sont maintenant correctement comptés

---

## 🚀 Déploiement sur le Serveur

### Étape 1 : Récupérer les modifications

```bash
# Se connecter au serveur et aller dans le répertoire du projet
cd /path/to/mercedes-bot

# Récupérer les dernières modifications
git pull origin main
```

### Étape 2 : Calculer les statistiques

```bash
# Calculer toutes les statistiques depuis le début
php artisan stats:calculate

# Résultat attendu :
# Starting daily statistics calculation...
# Calculating stats from 2025-12-04 to 2025-12-08
# Found 3 days with conversations
# ============================] 100%
# Statistics calculation completed!
# - New stats: 3
# - Updated stats: 0
# - Total processed: 3
```

### Étape 3 : Vérifier les résultats

```bash
# Vérifier que les statistiques sont créées
php artisan tinker
> App\Models\DailyStatistic::count();
# Devrait retourner un nombre > 0

# Voir les statistiques récentes
> App\Models\DailyStatistic::orderBy('date', 'desc')->take(3)->get(['date', 'total_conversations', 'menu_vehicules_neufs', 'menu_sav']);
```

### Étape 4 : Tester l'interface

1. Accéder à `/dashboard/statistics`
2. Vérifier que les graphiques s'affichent
3. Vérifier que les distributions de menus affichent des valeurs > 0
4. Vérifier que les tendances quotidiennes sont visibles

---

## 📊 Utilisation de la Commande

### Syntaxe complète

```bash
php artisan stats:calculate [OPTIONS]
```

### Options disponibles

| Option | Description | Exemple |
|--------|-------------|---------|
| `--from=DATE` | Date de début (Y-m-d) | `--from=2025-12-01` |
| `--to=DATE` | Date de fin (Y-m-d) | `--to=2025-12-31` |
| `--force` | Recalculer les stats existantes | `--force` |

### Exemples d'utilisation

```bash
# Calculer toutes les statistiques (défaut)
php artisan stats:calculate

# Calculer uniquement pour décembre 2025
php artisan stats:calculate --from=2025-12-01 --to=2025-12-31

# Recalculer toutes les statistiques existantes
php artisan stats:calculate --force

# Calculer pour une semaine spécifique
php artisan stats:calculate --from=2025-12-01 --to=2025-12-07
```

---

## 🔄 Automatisation (Recommandé)

### Option 1 : Cron Job Quotidien

Ajouter au crontab pour calculer automatiquement les stats chaque jour :

```bash
# Ouvrir le crontab
crontab -e

# Ajouter cette ligne (s'exécute tous les jours à 1h du matin)
0 1 * * * cd /path/to/mercedes-bot && php artisan stats:calculate --from=$(date -d "yesterday" +\%Y-\%m-\%d) >> /var/log/mercedes-bot-stats.log 2>&1
```

### Option 2 : Laravel Scheduler

Dans `app/Console/Kernel.php` :

```php
protected function schedule(Schedule $schedule)
{
    // Calculer les statistiques quotidiennes à 1h du matin
    $schedule->command('stats:calculate --from=' . now()->subDay()->format('Y-m-d'))
             ->daily()
             ->at('01:00');
}
```

Puis s'assurer que le scheduler Laravel est configuré dans le cron :
```bash
* * * * * cd /path/to/mercedes-bot && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📈 Résultats Attendus

Après l'exécution de la commande, vous devriez voir :

### Dans la base de données

**Table `daily_statistics` :**
```sql
SELECT date, total_conversations, menu_vehicules_neufs, menu_sav
FROM daily_statistics
ORDER BY date DESC
LIMIT 5;
```

**Exemple de résultats :**
| date | total_conversations | menu_vehicules_neufs | menu_sav |
|------|---------------------|---------------------|----------|
| 2025-12-08 | 3 | 5 | 3 |
| 2025-12-05 | 1 | 0 | 0 |
| 2025-12-04 | 2 | 0 | 0 |

### Sur la page Statistiques

- ✅ **Cartes de distribution de menus** : Affichage des chiffres (Véhicules neufs, SAV, etc.)
- ✅ **Graphiques en donut** : Distribution visuelle des choix de menu
- ✅ **Graphique de tendance quotidienne** : Ligne montrant l'évolution des conversations
- ✅ **Heures de pointe** : Graphique en barres des heures d'activité
- ✅ **Parcours populaires** : Liste des chemins de navigation fréquents
- ✅ **Résumé de période** : Total conversations, utilisateurs uniques, taux de transfert, durée moyenne

---

## 🐛 Dépannage

### Problème : "No conversations found in the database"

**Solution :**
```bash
# Vérifier qu'il y a des conversations
php artisan tinker
> App\Models\Conversation::count();
```

Si le résultat est 0, il n'y a pas encore de conversations dans la base de données.

### Problème : Les stats de menus restent à 0

**Causes possibles :**
1. Les événements `menu_choice` n'existent pas
2. Les `user_input` ne sont pas 1, 2, 3, 4 ou 5

**Vérification :**
```bash
php artisan tinker
> App\Models\ConversationEvent::where('event_type', 'menu_choice')->count();
> App\Models\ConversationEvent::where('event_type', 'menu_choice')
    ->whereIn('user_input', ['1', '2', '3', '4', '5'])
    ->get(['user_input', 'menu_name']);
```

### Problème : Les graphiques ne s'affichent pas

**Solutions :**
1. Vider le cache du navigateur (Ctrl+F5)
2. Vérifier la console JavaScript pour des erreurs
3. S'assurer que Chart.js est bien chargé (vérifié dans le fichier HTML)

---

## 📝 Fichiers Modifiés

| Fichier | Type | Description |
|---------|------|-------------|
| `app/Console/Commands/CalculateDailyStatistics.php` | Nouveau | Commande de calcul des statistiques |

---

## ✅ Checklist de Déploiement

- [ ] Récupérer les modifications (`git pull`)
- [ ] Exécuter la commande de calcul (`php artisan stats:calculate`)
- [ ] Vérifier que la table `daily_statistics` contient des données
- [ ] Tester la page `/dashboard/statistics`
- [ ] Vérifier que les graphiques s'affichent correctement
- [ ] Configurer le cron job pour l'automatisation quotidienne
- [ ] Documenter la procédure pour l'équipe

---

## 🎓 Formation pour l'Équipe

### Commandes à connaître

```bash
# Calculer les stats (à exécuter après le déploiement initial)
php artisan stats:calculate

# Recalculer toutes les stats (si besoin de corriger)
php artisan stats:calculate --force

# Calculer pour une période spécifique
php artisan stats:calculate --from=2025-12-01 --to=2025-12-31
```

### Surveillance

```bash
# Vérifier le nombre de stats calculées
php artisan tinker
> App\Models\DailyStatistic::count();

# Voir les dernières stats
> App\Models\DailyStatistic::latest('date')->take(5)->get();
```

---

## 📞 Support

Si vous rencontrez des problèmes :

1. Vérifier les logs Laravel : `storage/logs/laravel.log`
2. Vérifier la connexion à la base de données
3. S'assurer que toutes les migrations sont exécutées : `php artisan migrate:status`
4. Consulter ce guide de dépannage

---

**Dernière mise à jour :** 08 Décembre 2025
**Version :** 1.0
**Auteur :** Claude Code Assistant
