# Collection Postman - Mercedes-Benz Bot API

Cette collection Postman contient tous les endpoints de l'API Mercedes-Benz Bot Dashboard pour faciliter les tests.

## 📦 Contenu

- **Mercedes-Bot-API.postman_collection.json** : Collection complète avec tous les endpoints
- **Production.postman_environment.json** : Variables d'environnement pour la production
- **Local.postman_environment.json** : Variables d'environnement pour le développement local

## 🚀 Installation

### 1. Importer la collection

1. Ouvrez Postman
2. Cliquez sur **Import** (en haut à gauche)
3. Glissez-déposez le fichier `Mercedes-Bot-API.postman_collection.json`
4. La collection apparaît dans le panneau de gauche

### 2. Importer les environnements

1. Cliquez sur **Import**
2. Glissez-déposez les fichiers :
   - `Production.postman_environment.json`
   - `Local.postman_environment.json`
3. Les environnements apparaissent dans le menu déroulant en haut à droite

### 3. Sélectionner un environnement

En haut à droite de Postman :
- Cliquez sur le menu déroulant "No Environment"
- Sélectionnez **Production - Mercedes Bot** ou **Local - Mercedes Bot**

## 🔐 Configuration de l'authentification

### Première utilisation

1. Sélectionnez l'environnement (Production ou Local)
2. Allez dans **1. Authentication > Login**
3. Modifiez l'email et le mot de passe dans le body si nécessaire
4. Cliquez sur **Send**
5. Le token sera automatiquement sauvegardé dans la variable `{{access_token}}`
6. Tous les autres endpoints utiliseront ce token automatiquement

### Script automatique

La requête de login contient un script qui sauvegarde automatiquement le token :

```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    if (jsonData.token) {
        pm.environment.set('access_token', jsonData.token);
    }
}
```

## 📚 Structure de la collection

### 1. Authentication
- **Login** : Obtenir le token Bearer (sauvegarde automatique)
- **Register** : Créer un nouveau compte
- **Logout** : Se déconnecter

### 2. Twilio Webhooks
Endpoints appelés par Twilio Flow :
- **Incoming Message** : Réception d'un message WhatsApp
- **Menu Choice** : Choix dans un menu
- **Free Input** : Saisie libre (nom, email, VIN, etc.)
- **Agent Transfer** : Transfert vers un agent
- **Complete Conversation** : Fin de conversation
- **Send Message (Agent)** : Envoyer un message (nécessite auth)

### 3. Dashboard API
Endpoints protégés par authentification :
- **Get Statistics** : Statistiques globales
- **Get Conversations** : Liste avec filtres et pagination
- **Get Conversation Detail** : Détail complet d'une conversation
- **Get Active Conversations** : Conversations en cours
- **Get History** : Historique quotidien
- **Get Popular Paths** : Parcours fréquents
- **Search Free Inputs** : Recherche dans les saisies

### 4. Agent Chat
Interface de chat agent :
- **View Chat** : Afficher l'interface web
- **Take Over Conversation** : Prendre en charge une conversation
- **Send Message to Client** : Envoyer un message au client
- **Close Conversation** : Fermer la conversation

### 5. Legacy Webhooks (n8n)
Anciens endpoints (compatibilité) :
- **Generic Event** : Événement générique
- **Update User Data** : MAJ données utilisateur
- **Handle Transfer** : Transfert Chatwoot
- **Complete (Legacy)** : Fin de conversation

### 6. Health Check
- **Health Check** : Vérifier le statut de l'API

## 🔧 Variables d'environnement

### Variables globales

| Variable | Description | Exemple |
|----------|-------------|---------|
| `base_url` | URL de base de l'API | `https://mbbot-dashboard.ywcdigital.com` |
| `access_token` | Token Bearer (auto-généré) | `1\|abc123...` |
| `admin_email` | Email de test | `admin@mercedes-bot.com` |
| `admin_password` | Mot de passe de test | `password123` |
| `test_phone` | Numéro WhatsApp de test | `whatsapp:+225xxxxxxxx` |
| `test_session_id` | ID de session de test | `session_test_12345` |

### Modifier les variables

1. Cliquez sur l'icône "œil" 👁️ en haut à droite
2. Cliquez sur **Edit** à côté de l'environnement actif
3. Modifiez les valeurs
4. Cliquez sur **Save**

## 📝 Exemples d'utilisation

### Scénario 1 : Tester le webhook Twilio

1. Sélectionnez **2. Twilio Webhooks > Incoming Message**
2. Modifiez le `SessionId` et `From` dans le body
3. Cliquez sur **Send**
4. Vérifiez la réponse (statut 200)

### Scénario 2 : Consulter les conversations

1. Connectez-vous avec **1. Authentication > Login**
2. Sélectionnez **3. Dashboard API > Get Conversations**
3. Modifiez les paramètres de filtre si nécessaire
4. Cliquez sur **Send**

### Scénario 3 : Prendre en charge une conversation

1. Connectez-vous
2. Trouvez une conversation active avec **Get Active Conversations**
3. Notez l'ID de la conversation
4. Allez dans **4. Agent Chat > Take Over Conversation**
5. Remplacez `:id` dans l'URL par l'ID trouvé
6. Cliquez sur **Send**
7. Utilisez **Send Message to Client** pour répondre

## 🐛 Dépannage

### Erreur 401 Unauthorized

**Cause** : Token expiré ou invalide

**Solution** :
1. Allez dans **1. Authentication > Login**
2. Cliquez sur **Send** pour obtenir un nouveau token
3. Réessayez votre requête

### Erreur 404 Not Found

**Cause** : URL incorrecte ou environnement mal configuré

**Solution** :
1. Vérifiez que l'environnement est sélectionné
2. Vérifiez la valeur de `{{base_url}}`
3. Vérifiez que l'application est déployée

### Erreur 422 Validation Error

**Cause** : Données invalides dans le body

**Solution** :
1. Vérifiez le format du JSON dans le body
2. Consultez le message d'erreur pour identifier le champ problématique
3. Corrigez les données et réessayez

### Token non sauvegardé automatiquement

**Solution** :
1. Après le login, copiez le token de la réponse
2. Cliquez sur l'icône "œil" 👁️ en haut à droite
3. Cliquez sur **Edit**
4. Collez le token dans la variable `access_token`
5. Cliquez sur **Save**

## 📖 Documentation API

Pour plus de détails sur chaque endpoint :
- Consultez les fichiers de documentation dans le projet
- Lisez les descriptions dans chaque requête Postman
- Consultez les commentaires dans les controllers Laravel

## 🔗 Liens utiles

- **Application Production** : https://mbbot-dashboard.ywcdigital.com
- **Documentation Laravel** : https://laravel.com/docs
- **Documentation Postman** : https://learning.postman.com

## 💡 Conseils

1. **Utilisez les variables** : Ne codez jamais les valeurs en dur
2. **Sauvegardez vos tests** : Créez des exemples de réponses
3. **Organisez vos dossiers** : Dupliquez la collection pour différents projets
4. **Partagez avec l'équipe** : Exportez et versionnez la collection

## 🆘 Support

En cas de problème :
1. Vérifiez la console Postman (View > Show Postman Console)
2. Consultez les logs Laravel (`storage/logs/laravel.log`)
3. Vérifiez les logs Coolify
