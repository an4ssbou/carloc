#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
app="${CARLOC_APP_DIR:-$root/application}"
[ -f "$app/.env" ] || { echo 'Exécuter bash scripts/setup-local.sh avant le lancement.' >&2; exit 1; }
bash "$root/scripts/local-db.sh" start
cd "$app"
exec php artisan serve --host=127.0.0.1 --port="${CARLOC_PORT:-8000}"
