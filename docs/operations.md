# Exploitation locale

## Arrêter et redémarrer

Ctrl+C arrête le serveur lancé par `scripts/start-local.sh`. Arrêter ensuite la base de cette copie :

```bash
bash scripts/local-db.sh stop
```

`bash scripts/start-local.sh` la redémarre en conservant les données.

## Sauvegarder et contrôler une restauration

```bash
bash scripts/backup-local.sh
bash scripts/restore-check.sh application/storage/backups/nom-de-la-sauvegarde.sql
```

Remplacer le nom par celui affiché par la première commande. La base doit être démarrée. La restauration importe dans `location_restore_check`, une base séparée, et ne remplace pas la démonstration. Ce contrôle réutilise cette base de contrôle ; ne pas y stocker de données utiles.

Les dumps sont privés, locaux et non chiffrés par le script. Les copies hors machine doivent être protégées et chiffrées selon le contexte. Une sauvegarde complète inclut aussi les fichiers privés/publics de `storage/app`, les secrets de configuration conservés dans un coffre séparé et la révision du code. Ne pas envoyer ces éléments dans Git.

## Mettre à jour et revenir en arrière

Avant une évolution, sauvegarder base et fichiers puis conserver la révision du code et la configuration privée. Tester les nouvelles migrations sur une copie isolée. Un retour arrière nécessite un code compatible avec le schéma restauré ; ne pas exécuter `migrate:fresh` ni restaurer un dump par dessus une base utile sans procédure validée. Le script fourni vérifie une restauration séparée, il n’automatise pas un rollback de production.

Pour retirer le showcase, revoir les IDs détenus dans `demo_datasets` et les personnalisations avant toute suppression. Aucun effacement automatique n’est fourni.
