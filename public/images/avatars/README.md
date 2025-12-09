# Avatars Images

Ce dossier contient les images d'avatar standard pour les clients et non-clients.

## Fichiers requis :

1. **client.gif** - Image GIF pour les clients Mercedes-Benz (is_client = true)
   - Recommandation : Logo Mercedes-Benz animé ou avatar professionnel
   - Dimensions recommandées : 200x200px

2. **non-client.gif** - Image GIF pour les non-clients (is_client = false)
   - Recommandation : Avatar générique ou icône utilisateur animée
   - Dimensions recommandées : 200x200px

## Images temporaires

En attendant vos propres images, le système utilise des images placeholder depuis des services externes.

Pour ajouter vos propres images :
1. Placez vos fichiers GIF dans ce dossier
2. Nommez-les exactement : `client.gif` et `non-client.gif`
3. Les vues s'adapteront automatiquement

## URLs des images :
- Client : `/images/avatars/client.gif`
- Non-client : `/images/avatars/non-client.gif`
