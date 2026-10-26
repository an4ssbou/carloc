# Installation locale

## Parcours recommandé

Depuis la racine du dépôt cloné, vérifier les prérequis du README, puis exécuter `bash scripts/setup-local.sh`. Ne pas lancer le script en root. Le script installe Composer et npm à partir des fichiers de verrouillage existants ; il ne met pas les dépendances à jour. Il crée les dossiers Laravel inscriptibles, sa base privée, `.env`, une clé d’application propre et le lien `public/storage`.

Le nom `location_demo` désigne exclusivement les données de cette copie. La base `testing` est réservée aux tests. Aucun serveur MariaDB système ni base existante n’est nécessaire : une instance sans réseau est démarrée dans `application/storage/local-db/`. Ne pas partager le dossier de travail avec des utilisateurs non autorisés.

```bash
bash scripts/setup-local.sh
bash scripts/showcase-local.sh  # facultatif
bash scripts/start-local.sh
```

Le serveur est disponible sur http://127.0.0.1:8000. Pour choisir un autre port :

```bash
CARLOC_PORT=8001 bash scripts/start-local.sh
```

## Configuration

`application/.env.example` liste les variables sans secret réel. Le setup génère sa propre configuration locale, avec `APP_ENV=local`, `APP_DEBUG=false`, locale française, socket privé, sessions et cache fichiers, file d’attente synchrone et courrier en mémoire (`MAIL_MAILER=array`). Aucun email n’est envoyé par cette configuration. Le paiement de démonstration dépend de l’environnement local/testing. La simulation GPS nécessite `GPS_SIMULATION_ENABLED=true`, que le setup active dans sa configuration locale neuve.

Si `.env` existe déjà, le setup le conserve : vérifier qu’il correspond bien à cette copie avant de relancer. Ne pas utiliser de configuration ou d’identifiants d’un environnement réel. Les variables du terminal peuvent aussi surcharger `.env` ; ouvrir un terminal propre sans variables DB héritées pour le setup.

## Dépannage

- `Prérequis absent` : installer l’exécutable indiqué via le gestionnaire de paquets du système, puis relancer le setup. Aucun installateur système n’est inclus.
- `pdo_mysql` absent : activer l’extension pour le PHP CLI réellement exécuté, puis contrôler `php -m`.
- Connexion à la base refusée : `bash scripts/local-db.sh status`, puis `bash scripts/local-db.sh start`. Consulter localement `application/storage/local-db/server.log` sans le publier.
- Port occupé : choisir `CARLOC_PORT=8001` ; le port par défaut est 8000.
- Photos absentes : vérifier le chargement showcase et le lien `application/public/storage`. Le setup et le script showcase créent ce lien.
- Modification de configuration non visible : depuis `application/`, lancer `php artisan optimize:clear`, puis redémarrer le serveur.

Les scripts ont été vérifiés sous Linux, PHP 8.3 et MariaDB. Windows natif, macOS et Docker ne sont pas certifiés. `compose.yaml` est une configuration de développement alternative, pas le parcours testé ni un déploiement prêt à exposer.

Documentation officielle consultée via Context7 pour Laravel 13 : [prérequis et déploiement](https://github.com/laravel/docs/blob/13.x/deployment.md), [configuration des erreurs](https://github.com/laravel/docs/blob/13.x/errors.md).
