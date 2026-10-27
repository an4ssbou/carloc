# CarLoc — plateforme de location de voitures

CarLoc réunit un catalogue public, un espace client, la gestion des agences et une administration. Les propriétaires gèrent leurs véhicules et réservations ; les clients réservent des dates et retrouvent leur récapitulatif de location.

Cette version est une **démonstration locale**. Le paiement est simulé, le récapitulatif est imprimable sans signature électronique et aucune IA ne vérifie les pièces d’identité. La réception GPS est préparée ; aucun boîtier physique n’a été testé.

## Fonctionnalités

- Catalogue de véhicules et agences, recherche et pagination.
- Réservation persistante avec calcul du prix côté serveur et contrôle des disponibilités.
- Simulation de paiement sans carte ni débit ; annulation jusqu’à 24 heures après création.
- Espaces client, propriétaire d’agence et superadministrateur avec contrôles d’accès.
- Tableaux de bord, gestion des véhicules et notifications dans un panneau accessible par la cloche.
- Option GPS par véhicule, réception HTTP protégée par clé et affichage privé de la dernière position. Actualisation toutes les 5 secondes ; positions simulées identifiées.
- Démonstration optionnelle : 25 véhicules, 12 modèles illustrés, 4 agences et 180 nouvelles réservations fictives sur six mois.

## Installation et lancement

Les commandes suivantes se lancent depuis **la racine du dépôt**, dans un terminal Linux. Le code Laravel se trouve dans `application/`.

Prérequis : PHP ≥ 8.3, Composer 2, Node.js 20 ≥ 20.19 ou 22 ≥ 22.12 (ou version compatible avec le verrou), npm, MariaDB et ripgrep (`rg`). Les exécutables MariaDB requis sont `mariadb`, `mariadbd`, `mariadb-install-db`, `mariadb-admin` et `mariadb-dump`. PHP doit disposer de `pdo_mysql`, ctype, curl, DOM, fileinfo, filter, hash, mbstring, openssl, PCRE, PDO, session, tokenizer et XML ; ZIP facilite les installations Composer.

```bash
git clone git@github.com:an4ssbou/carloc.git
cd carloc
bash scripts/setup-local.sh
# Facultatif : enrichir les tableaux de bord et ajouter les photos de démonstration
bash scripts/showcase-local.sh
bash scripts/start-local.sh
```

Ouvrir **http://127.0.0.1:8000**. Le setup installe les dépendances depuis les verrous, crée une configuration `.env` privée et une clé locale, applique les migrations, charge les comptes synthétiques de base et construit les assets. Le premier téléchargement nécessite Internet. Ne pas copier manuellement `.env.example` avant ce parcours : le script génère les paramètres de son socket privé.

La base MariaDB appartient à cette copie, utilise `application/storage/local-db/db.sock` et n’écoute pas sur le réseau. Les scripts sont conçus pour un utilisateur Linux non root ; ils ne configurent pas un hébergement de production.

| Compte local | Adresse |
|---|---|
| Client | `demo@carloc.test` |
| Propriétaire de l’agence de démonstration | `agence@carloc.test` |
| Administrateur, après chargement showcase | `admin@showcase.carloc.test` |

Mot de passe **public et synthétique**, réservé à ces exemples : `Demo-Local-2026!`. Les seeders de démonstration refusent les environnements autres que `local` et `testing`. Ne jamais utiliser ces comptes ou cette base pour un service réel.

Après connexion, les menus donnent accès aux écrans autorisés. Le tableau de bord agence se trouve à `/agency/{slug}/dashboard` (le menu fournit le slug de votre agence) et l’administration à `/admin/dashboard` ; les véhicules publics sont à `/vehicles`.

## Tests

```bash
bash scripts/test-local.sh
bash scripts/test-local.sh ../project/qa_tests
```

Les tests utilisent une base **`testing` distincte**, recréée par les tests. Ne pas y placer de données à conserver. Les dernières suites exécutées le 26 octobre 2026 ont réussi : 89 tests applicatifs (1630 assertions) et 24 tests indépendants (1640 assertions). Les dépendances sont figées dans `application/composer.lock` et `application/package-lock.json`.

## Arrêt et sauvegarde

Ctrl+C arrête le serveur web. Pour arrêter la base privée de cette copie :

```bash
bash scripts/local-db.sh stop
```

Avec la base démarrée, sauvegarder puis vérifier une restauration séparée :

```bash
bash scripts/backup-local.sh
bash scripts/restore-check.sh application/storage/backups/nom-de-la-sauvegarde.sql
```

Remplacer le nom par celui affiché lors de la sauvegarde. La restauration utilise `location_restore_check` sans remplacer la démonstration. Les dumps restent privés et ne sont pas chiffrés par le script. Pour une sauvegarde complète, conserver aussi les fichiers de `storage/app` et la configuration privée dans un emplacement protégé, hors Git.

Le setup conserve un `.env` existant : vérifier qu’il appartient à cette copie et qu’aucune variable DB héritée du terminal ne surcharge sa configuration. Pour un port différent : `CARLOC_PORT=8001 bash scripts/start-local.sh`. Le jeu showcase reste ancré au 25 octobre 2026 ; les graphiques évoluent avec la date courante et les réservations ne sont pas redatées au relancement.

## Informations essentielles

- [Sécurité et préparation d’un futur déploiement](SECURITY.md)
- [Contribution et évolutions](CONTRIBUTING.md)
- [Licences et crédits des ressources](THIRD_PARTY_NOTICES.md)

Aucun service payant n’est nécessaire pour cette démonstration. La mise en production, les transactions bancaires, le matériel GPS, l’envoi d’e-mails réel et les tests de charge restent à préparer séparément.
