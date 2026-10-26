# Faire évoluer CarLoc

Installer selon le README, travailler sur une branche dédiée et utiliser uniquement des données synthétiques. Exécuter les deux suites de tests avec `scripts/test-local.sh` et reconstruire les assets après une modification visuelle (`cd application` puis `npm run build`).

Une évolution métier doit inclure un cas normal, un échec observable et les contrôles d’accès pertinents. Ne pas affaiblir un test pour obtenir un résultat vert. Préserver les verrous et utiliser une nouvelle migration pour toute évolution de schéma.

Ne jamais committer `.env`, identifiants réels, uploads, sauvegardes ou logs. Actualiser la documentation lorsque le comportement ou le lancement change. Les commandes de développement n’autorisent aucune publication ou mise en production automatique.
