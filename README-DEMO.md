# CarLoc — démonstration publique

[Préparer le service Render gratuit](https://render.com/deploy?repo=https%3A%2F%2Fgithub.com%2Fan4ssbou%2Fcarloc%2Ftree%2Fportfolio-demo)

Cette branche est une copie de démonstration : trois véhicules, une agence et trois comptes fictifs. PHP 8.4, Apache et SQLite temporaire. Les modifications sont partagées et effacées au redémarrage, au redéploiement ou à la mise en veille. Aucun paiement, courriel, GPS matériel ni document réel.

## Essayer

Ouvrir `/login`. Mot de passe public commun : `Demo-Portfolio-2026!`.

| Compte | Adresse fictive | Parcours |
|---|---|---|
| Client | client@demo.carloc.test | `/vehicles` → réserver → `/my-reservations` |
| Agence | agence@demo.carloc.test | `/agency/agence-demo-publique/reservations` → confirmer/annuler |
| Administration | admin@demo.carloc.test | `/admin/reservations` → consulter/traiter |

Réserver de 1 à 30 jours sur des dates libres. Les inscriptions, changements de comptes, téléversements, contact, paiement, GPS et changements du catalogue sont refusés côté serveur. Limite partagée : 100 réservations et 10 actives pour le client, 30 mutations par minute. Ne saisir aucune donnée personnelle; ne réutiliser ces mots de passe nulle part ailleurs.

## Render

1. Utiliser le bouton ci-dessus ou **New → Blueprint**, dépôt `an4ssbou/carloc`, branche `portfolio-demo`.
2. Vérifier **carloc-portfolio-demo**, **Docker**, **Free**, aucun autre service ni disque. Ne pas sélectionner une offre payante ni acheter d'option.
3. Les variables sont fournies par `render.yaml`. Aucune clé manuelle : l'entrée crée une clé Laravel temporaire et utilise l'adresse Render comme `APP_URL`. Ne copier aucun `.env` ni base existante.
4. Attendre **Live**, puis ouvrir `/up` et tester le parcours. L'URL publique n'est validée qu'après cet essai.

Render Free peut mettre le service en veille après 15 minutes; le réveil prend environ une minute. Les 750 heures mensuelles sont partagées par l'espace. Les limites réseau/build peuvent suspendre le service; vérifier le plan et l'usage dans votre compte. [Limites officielles](https://render.com/docs/free), consultées le 26 octobre 2026.

## Installation locale isolée

Depuis cette copie :

```sh
docker build -t portfolio-carloc-demo:local .
docker run --rm --name carloc-demo-local -p 127.0.0.1:8080:10000 -e DEMO_MODE=true -e APP_URL=http://127.0.0.1:8080 -e DEMO_ALLOW_LOCAL_HTTP=true portfolio-carloc-demo:local
```

Ouvrir http://127.0.0.1:8080. Arrêter avec Ctrl+C. Aucun montage d'un dossier ou d'une base d'origine. Le mode HTTP de test refuse une origine publique. Le démarrage refuse debug actif, environnement hors production, DB hors `/tmp/carloc-demo.sqlite` ou fichier existant sans marqueur de provenance. Le reset ne concerne que ce conteneur éphémère; aucun endpoint HTTP de reset.

## Mise à jour et retour arrière

L'auto-déploiement est désactivé. Après modifications et tests, choisir un déploiement manuel dans Render. Pour revenir en arrière, sélectionner un ancien déploiement ou suspendre le service et retirer le lien du portfolio. Ne jamais utiliser cette configuration avec des clients réels : aucun stockage durable et aucune sauvegarde de données métier ne sont prévus.

Tests et preuves de la préparation sont conservés dans le projet portfolio sous `project/proofs/carloc-demo-*` et `demo-security-*`. Les tests locaux ne prouvent pas la disponibilité de la future URL publique.
