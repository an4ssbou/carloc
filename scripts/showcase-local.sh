#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
app="${CARLOC_APP_DIR:-$root/application}"
cd "$app"
# Force the private working database, never an original or external database.
export DB_CONNECTION=mysql DB_DATABASE=location_demo DB_HOST=localhost DB_USERNAME=root DB_PASSWORD= DB_URL=
export DB_SOCKET="$app/storage/local-db/db.sock"
php -r 'require "vendor/autoload.php"; $app = require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); if (! app()->environment("local", "testing")) { fwrite(STDERR, "Démonstration réservée à local/testing.\n"); exit(1); }'
bash "$root/scripts/local-db.sh" start
bash "$root/scripts/backup-local.sh"
php artisan migrate --force --no-interaction
php artisan db:seed --class=ShowcaseSeeder --force --no-interaction
if [ ! -L public/storage ]; then
  php artisan storage:link --no-interaction
fi
echo 'Données fictives prêtes. Ouvrir http://127.0.0.1:8000 ou relancer scripts/start-local.sh.'
