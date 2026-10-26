#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
app="${CARLOC_APP_DIR:-$root/application}"
umask 077
mkdir -p "$app/storage/backups"
backup="${1:-$app/storage/backups/demo-$(date +%Y%m%d-%H%M%S).sql}"
mariadb-dump --no-defaults --protocol=socket --socket="$app/storage/local-db/db.sock" -uroot --single-transaction location_demo > "$backup"
echo "Sauvegarde : $backup"
