# Architecture et maintenance

Monolithe Laravel 13 / PHP 8.3 avec vues Blade, Alpine et assets compilés par Vite/Tailwind. MariaDB conserve les données. Chart.js et Leaflet sont fournis dans les dépendances verrouillées, sans dépendance à un runtime CDN pour les graphiques et le thème. Le fond OpenStreetMap reste optionnel, chargé sur clic avec attribution.

| Comportement | Fichiers principaux |
|---|---|
| Routes web et réception GPS | `application/routes/web.php`, `api.php` |
| Création et annulation client | `application/app/Http/Controllers/ReservationController.php`, `application/app/Models/Reservation.php` |
| Confirmation/annulation agence et admin | `application/app/Services/ReservationTransitionService.php` |
| Prix et forfaits | `application/app/Models/Vehicle.php` |
| Simulation de paiement | `application/app/Http/Controllers/PaymentController.php`, `application/config/payments.php` |
| GPS et autorisations | `GpsIngestionController.php`, `GpsTrackingController.php`, `application/app/Services/GpsPositionService.php` |
| Carte et actualisation | `application/resources/js/gps-tracking.js`, `application/resources/views/agency/gps.blade.php` |
| Notifications | `application/resources/views/components/notification-menu.blade.php`, `application/app/Http/Controllers/NotificationController.php` |
| Graphiques agence | `application/app/Http/Controllers/DashboardController.php`, `application/resources/js/agency-charts.js` |
| Comptes actifs et en-têtes HTTP | `application/app/Http/Middleware/` |
| Thème et composants | `application/tailwind.config.js`, `application/resources/views/components/` |
| Données synthétiques | `application/database/seeders/DemoSeeder.php`, `ShowcaseSeeder.php` |
| Schéma | `application/database/migrations/` |
| Tests fonctionnels | `application/tests/`, `project/qa_tests/` |
| Exploitation locale | `scripts/` |

Créer une nouvelle migration pour faire évoluer une base déjà installée. Ajouter un test comportemental avant de changer les règles métier, avec cas normal, borne et refus d’accès. Les tests QA sont séparés de la suite de l’auteur. Les pièces d’identité sont privées ; les photos publiques des véhicules utilisent un disque distinct.

Les commandes officielles de ce dépôt sont celles du README. Les scripts génériques hérités du squelette Composer (`setup`, `dev`) ne préparent pas le socket privé ni toutes les garanties du parcours local ; préférer `scripts/setup-local.sh` et `scripts/start-local.sh`.
