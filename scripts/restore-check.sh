#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
app="${CARLOC_APP_DIR:-$root/application}"
backup="${1:?Usage: bash scripts/restore-check.sh sauvegarde.sql}"
db=(mariadb --no-defaults --protocol=socket --socket="$app/storage/local-db/db.sock" -uroot)
# Restauration dans une base distincte : ne remplace pas la démonstration.
"${db[@]}" -e 'CREATE DATABASE IF NOT EXISTS location_restore_check CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
"${db[@]}" location_restore_check < "$backup"
"${db[@]}" location_restore_check -e 'SELECT COUNT(*) AS users_restored FROM users; SELECT COUNT(*) AS reservations_restored FROM reservations;'
