# Utilisation et démonstration

## Client

Consulter `/vehicles`, ouvrir une fiche puis se connecter avec le client synthétique du README. Choisir deux dates libres, vérifier le montant calculé par le serveur et créer la réservation. Le paiement local est une simulation persistante sans carte ni débit. Le détail donne accès au récapitulatif privé, imprimable avec le navigateur. L’annulation est possible jusqu’à 24 heures après création, inclusivement, selon l’échéance affichée.

## Agence et administration

Le compte `agence@carloc.test` ouvre son tableau de bord et gère ses véhicules et réservations. Le compte administrateur showcase est disponible seulement après le chargement enrichi. Les écrans sont filtrés selon les droits du compte.

La cloche ouvre les cinq dernières notifications. L’aperçu ne les marque pas lues ; « Voir tout » ouvre l’historique et déclenche son comportement de lecture. Escape, la croix ou un clic extérieur ferment le panneau.

## Données enrichies

`bash scripts/showcase-local.sh` sauvegarde la base puis ajoute un jeu identifié par `showcase-v1`, sans reset. Sur une installation neuve : 25 véhicules au total, 4 agences, 40 nouveaux clients fictifs et 180 réservations sur une installation neuve (la copie de travail initiale en comptait 184 avec quatre réservations créées lors des essais précédents). Aucun CIN ou document d’identité réel n’est fourni.

Les 12 modèles illustrés ont des photos cohérentes avec leur génération et leur couleur. Sources et empreintes : `application/resources/demo/vehicles.json`, crédits consultables à `/demo/vehicles/credits.html`. Il s’agit de photos illustratives, pas d’une flotte commerciale. Un bandeau permanent signale les chiffres fictifs dans le mode local enrichi.

Le jeu est ancré au 25 octobre 2026 : six mois de débuts de location, de mai à octobre. Les tableaux suivent leurs propres périodes, donc leurs graphiques changent naturellement avec la date d’exécution. Le script ne redécale pas les réservations à chaque lancement et ne remplace pas les personnalisations. Une collision de namespace sans registre est refusée.

## GPS

Cocher GPS lors de l’ajout ou de l’édition d’un véhicule, puis ouvrir son suivi. La dernière position, sa source et ses dates sont privées ; actualisation toutes les 5 secondes, signal ancien après 60 secondes. La simulation locale est explicitement affichée et n’utilise pas la géolocalisation de l’ordinateur.

Pour préparer un futur émetteur, créer une clé dans l’écran GPS. Elle n’est révélée qu’une fois et seul son hash est conservé. Révoquer la clé pour réutiliser la simulation propriétaire. Désactiver GPS efface les coordonnées et révoque la clé. Voir [le contrat HTTP](gps-api.md). Aucun boîtier réel ni historique de trajet n’est inclus.
