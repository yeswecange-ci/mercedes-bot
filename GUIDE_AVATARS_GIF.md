# 🎨 Guide d'Installation des Avatars GIF

Ce guide explique comment personnaliser les avatars clients et non-clients avec vos propres images GIF.

---

## 📍 Emplacement des fichiers

Les avatars sont stockés dans :
```
public/images/avatars/
```

---

## 📝 Fichiers requis

Vous devez ajouter **2 fichiers GIF** dans ce dossier :

### 1. **client.gif**
- **Usage** : Affiché pour tous les clients Mercedes-Benz (`is_client = true`)
- **Recommandation** : Logo Mercedes-Benz animé, avatar professionnel
- **Dimensions** : 200x200px (recommandé)
- **Format** : GIF animé ou statique
- **Poids** : < 500 KB pour performance optimale

### 2. **non-client.gif**
- **Usage** : Affiché pour tous les non-clients (`is_client = false`)
- **Recommandation** : Avatar générique, icône utilisateur
- **Dimensions** : 200x200px (recommandé)
- **Format** : GIF animé ou statique
- **Poids** : < 500 KB pour performance optimale

---

## 🚀 Installation

### Étape 1 : Préparer vos GIFs

1. Créez ou trouvez vos 2 images GIF
2. Renommez-les exactement :
   - `client.gif`
   - `non-client.gif`
3. Optimisez-les si nécessaire (< 500 KB chacun)

### Étape 2 : Copier dans le projet

**Sur Windows :**
```bash
copy "chemin\vers\client.gif" "public\images\avatars\client.gif"
copy "chemin\vers\non-client.gif" "public\images\avatars\non-client.gif"
```

**Sur Linux/Mac :**
```bash
cp /chemin/vers/client.gif public/images/avatars/client.gif
cp /chemin/vers/non-client.gif public/images/avatars/non-client.gif
```

### Étape 3 : Vérifier l'installation

Vérifiez que les fichiers existent :
```bash
ls public/images/avatars/
```

Vous devriez voir :
```
README.md
client.gif
non-client.gif
```

### Étape 4 : Vider le cache (optionnel)

Si les images ne s'affichent pas immédiatement :
```bash
php artisan cache:clear
php artisan view:clear
```

---

## 🔍 Où les avatars sont affichés

Les nouveaux avatars GIF apparaissent dans **7 vues** :

1. **Dashboard principal** (`/dashboard`)
   - Tableau des conversations récentes

2. **Conversations actives** (`/dashboard/active`)
   - Cartes des conversations en cours

3. **Conversations en attente** (`/dashboard/pending`)
   - Cartes des clients en attente d'agent

4. **Interface Chat** (`/dashboard/chat/{id}`)
   - En-tête de conversation
   - Messages du client dans la timeline

5. **Liste des clients** (`/dashboard/clients`)
   - Tableau de tous les clients

6. **Détail client** (`/dashboard/clients/{id}`)
   - En-tête de la page détail

7. **Toutes les listes de conversations**
   - Partout où un client/non-client est affiché

---

## 💡 Fonctionnement du système

### Logique de sélection

Le système sélectionne automatiquement l'avatar en fonction du champ `is_client` :

```php
// Si is_client = true → client.gif
// Si is_client = false → non-client.gif
```

### Système de fallback

Si les fichiers GIF n'existent pas, le système utilise automatiquement des **avatars générés** :

- **Client** : Avatar bleu avec texte "Client"
- **Non-client** : Avatar gris avec texte "Guest"

**Avantage** : L'application fonctionne même sans vos GIFs personnalisés !

---

## 🎨 Recommandations de design

### Pour `client.gif`
- ✅ Logo Mercedes-Benz avec étoile à 3 branches
- ✅ Couleurs : Argent, noir, bleu marine
- ✅ Style professionnel et élégant
- ✅ Animation subtile (optionnel)

### Pour `non-client.gif`
- ✅ Avatar générique neutre
- ✅ Couleurs : Gris, blanc
- ✅ Icône utilisateur simple
- ✅ Style minimaliste

### Optimisation
- Format : GIF (animé ou statique)
- Taille fichier : < 500 KB
- Dimensions : 200x200px minimum
- Fond : Transparent ou uni
- Qualité : Haute résolution pour netteté

---

## 🔧 Dépannage

### Problème : Les GIFs ne s'affichent pas

**Solution 1 : Vérifier les permissions**
```bash
# Linux/Mac
chmod 644 public/images/avatars/*.gif

# Windows (PowerShell en admin)
icacls "public\images\avatars\*.gif" /grant Everyone:R
```

**Solution 2 : Vérifier les noms de fichiers**
- Doivent être **exactement** `client.gif` et `non-client.gif` (minuscules)
- Pas d'espaces, pas de majuscules

**Solution 3 : Vider le cache navigateur**
- Ctrl + F5 (Windows)
- Cmd + Shift + R (Mac)

### Problème : Un seul GIF s'affiche

Vérifiez que les **deux fichiers** existent :
```bash
ls -la public/images/avatars/
```

### Problème : GIF trop lourd (lent à charger)

Optimisez vos GIFs :
- Utilisez [ezgif.com](https://ezgif.com/optimize)
- Réduisez le nombre de frames
- Compressez la qualité

---

## 📊 Exemples de sources d'images

### Gratuit
- [Flaticon](https://www.flaticon.com/) - Icônes et avatars
- [FreePik](https://www.freepik.com/) - Illustrations
- [Giphy](https://giphy.com/) - GIFs animés
- [Icons8](https://icons8.com/animated-icons) - Icônes animées

### Payant (haute qualité)
- [LottieFiles](https://lottiefiles.com/) - Animations
- [Shutterstock](https://www.shutterstock.com/) - Images premium

### Créer vos propres GIFs
- [Canva](https://www.canva.com/) - Création graphique
- [Photoshop](https://www.adobe.com/products/photoshop.html) - Professionnel
- [GIMP](https://www.gimp.org/) - Gratuit et open-source

---

## ✅ Checklist d'installation

- [ ] Créer/Obtenir `client.gif` et `non-client.gif`
- [ ] Renommer les fichiers correctement
- [ ] Optimiser la taille (< 500 KB chacun)
- [ ] Copier dans `public/images/avatars/`
- [ ] Vérifier les permissions (644)
- [ ] Tester dans le navigateur
- [ ] Vider le cache si nécessaire
- [ ] Vérifier sur toutes les pages

---

## 🆘 Support

Si vous rencontrez des problèmes :

1. Vérifiez les logs Laravel : `storage/logs/laravel.log`
2. Vérifiez la console navigateur (F12)
3. Testez les URLs directes :
   - `http://votre-domaine.com/images/avatars/client.gif`
   - `http://votre-domaine.com/images/avatars/non-client.gif`

---

**Note importante** : Même sans ajouter vos propres GIFs, l'application fonctionne parfaitement avec les avatars de fallback automatiques. Les GIFs personnalisés sont optionnels mais recommandés pour une meilleure identité visuelle.
