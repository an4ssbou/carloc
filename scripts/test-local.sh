#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
app="${CARLOC_APP_DIR:-$root/application}"
bash "$root/scripts/local-db.sh" start
cd "$app"
# Environnement explicite : ne jamais réutiliser une connexion de travail extérieure.
export DB_CONNECTION=mysql DB_DATABASE=testing DB_HOST=localhost DB_USERNAME=root DB_PASSWORD= DB_URL=
export DB_SOCKET="$app/storage/local-db/db.sock"
exec php artisan test "$@"
