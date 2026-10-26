# Sécurité et périmètre

Cette livraison prépare une démonstration locale et un dépôt privé. Ne pas exposer directement le serveur PHP de développement ou la base locale sur Internet.

Les protections vérifiées incluent autorisations par compte/agence, refus des comptes désactivés, révocation des autres sessions après changement de mot de passe, documents privés, limitation de requêtes sensibles, transactions de changement de statut, secrets GPS hashés et en-têtes de confidentialité. Ces mesures ne constituent pas une certification ni une garantie d’absence de vulnérabilité.

## Secrets et données

`.env`, clés, dumps, uploads, logs, base locale et dépendances installées sont exclus du dépôt. Ne jamais ajouter de jeton à une URL Git, un ticket, une capture ou un exemple. Les identifiants du README sont des exemples synthétiques publics réservés à local/testing. Les photos CC0 illustrent les véhicules fictifs. Les captures montrent exclusivement cette démonstration.

Pour signaler un défaut, utiliser un canal privé avec le propriétaire du dépôt ; ne pas ouvrir de ticket public contenant des données personnelles, secrets ou instructions d’exploitation sensibles.

## Avant toute mise en production

Préparer séparément l’hébergement, HTTPS, reverse proxy, cookies sécurisés, stockage privé, sauvegardes chiffrées/restauration, quotas d’upload et politique de rétention. La racine web doit pointer uniquement sur `application/public`, jamais sur la racine du dépôt. `storage` et `bootstrap/cache` nécessitent des droits d’écriture adaptés, sans permissions globales excessives. `APP_ENV=production`, `APP_DEBUG=false`, clé unique et secrets gérés hors Git sont nécessaires. Ne pas charger les seeders de démonstration ni transférer leur base vers la production.

Paiement bancaire, remboursement réel, signature électronique, IA d’identité, e-mail réel et boîtier GPS nécessitent leurs propres intégrations et validations. La simulation GPS doit rester désactivée hors local/testing. Vérifier les limites et conditions du fournisseur de tuiles avant un usage cartographique réel.

## Contrôles et limites

Les audits de dépendances détectent les avis connus à leur date d’exécution ; ils ne détectent pas toutes les failles métier. Les scans de fichiers préparés recherchent les formats usuels de secrets, sans garantie exhaustive. Aucun test de charge, audit d’infrastructure de production, fuzzing exhaustif ou contrôle de matériel GPS n’est revendiqué. Voir [les résultats datés](docs/verification.md).
