# Vérifications de la livraison GitHub

Vérifications locales du 26 octobre 2026, sur l’export propre destiné au dépôt. Empreinte du code et scripts : `sha256:00ea6f168d09bd19da0aba2673b8cca881b0bd0fab5efc4dd70eef2631a22ae9`.

| Vérification exécutée | Résultat |
|---|---|
| Export sans dépendances, .env ni base préexistants | Réussi |
| Installation documentée depuis composer.lock et package-lock.json | Réussie, migrations et build Vite inclus |
| Chargement showcase depuis cette installation neuve | 25 véhicules, 4 agences, 180 réservations fictives |
| Suite applicative : bash scripts/test-local.sh | 89 tests, 1630 assertions, réussis |
| Suite QA séparée : bash scripts/test-local.sh ../project/qa_tests | 24 tests, 1640 assertions, réussis |
| Catalogue et bandeau de démonstration en HTTP | 200, contenu attendu |
| Connexion propriétaire et tableau de bord agence en HTTP | Réussis |
| Photos illustratives | 12 sources rapprochées des SHA256 du manifeste, photo Clio servie en HTTP 200 |
| Sauvegarde et restauration dans la base de contrôle distincte | Réussies ; 180 réservations restaurées |
| MariaDB privée | Réseau désactivé vérifié |
| Verrous de dépendances | Identiques aux fichiers de l’export avant installation |
| Avis de sécurité connus des dépendances | Composer audit --locked : 0 ; npm audit : 0, au 26 octobre 2026 |

Le protocole QA et la suite indépendante ont été préparés séparément. L’agent parent a exécuté les commandes comme opérateur et transmis les résultats à QA ; aucune exécution directe par cet agent QA n’est prétendue. Les traces brutes locales ne sont pas publiées dans ce dépôt.

Un premier contrôle d’emballage attendait à tort 184 réservations, nombre présent dans l’ancienne copie de travail avec quatre essais précédents. Le seeder crée bien 180 réservations sur une base neuve. L’attendu de ce protocole a été corrigé après inspection du seeder et de la restauration, sans modifier l’application ni ses tests. Les contrôles HTTP et photos ont ensuite été exécutés avec le protocole corrigé.

Les cinq captures datent du 25 octobre 2026 et montrent des données synthétiques, avec bandeau de démonstration. Les parcours navigateur bureau/mobile/clavier ont été vérifiés lors de la tranche précédente ; ils n’ont pas été intégralement rejoués pour ce changement de documentation et d’emballage. Le code métier et visuel exporté est inchangé.

## Contrôles non exécutés et limites

Aucun pipeline GitHub Actions n’est configuré ou exécuté. Pas de déploiement, audit de serveur de production, test de charge, matériel GPS, paiement bancaire, signature électronique ou IA d’identité. Aucun résultat local ne garantit l’absence de faille ou un temps de réponse en production. Le scan de secrets recherche des formats usuels dans les fichiers retenus ; il n’est pas exhaustif. Aucun historique Git antérieur, donnée privée, sauvegarde ou configuration locale n’est repris.
