#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
app="${CARLOC_APP_DIR:-$root/application}"
data="$app/storage/local-db"
socket="$data/db.sock"
mkdir -p "$data"
chmod 700 "$data"
db=(mariadb --no-defaults --protocol=socket --socket="$socket" -uroot)
case "${1:-start}" in
  start)
    if mariadb-admin --no-defaults --socket="$socket" -uroot ping >/dev/null 2>&1; then
      echo "Base locale déjà démarrée."
      exit 0
    fi
    if [ ! -d "$data/mysql" ]; then
      mariadb-install-db --no-defaults --datadir="$data" --auth-root-authentication-method=normal --skip-test-db >"$data/install.log" 2>&1
    fi
    nohup mariadbd --no-defaults --datadir="$data" --socket="$socket" --pid-file="$data/db.pid" --skip-networking --skip-log-bin --log-error="$data/server.log" </dev/null >/dev/null 2>&1 &
    for attempt in $(seq 1 60); do
      if mariadb-admin --no-defaults --socket="$socket" -uroot ping >/dev/null 2>&1; then
        "${db[@]}" -e 'CREATE DATABASE IF NOT EXISTS location_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE DATABASE IF NOT EXISTS testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
        echo "Base locale prête (socket privé, réseau désactivé)."
        exit 0
      fi
      sleep 0.25
    done
    echo "Échec du démarrage; consulter $data/server.log" >&2
    exit 1
    ;;
  stop)
    mariadb-admin --no-defaults --socket="$socket" -uroot shutdown
    echo "Base locale arrêtée."
    ;;
  status)
    mariadb-admin --no-defaults --socket="$socket" -uroot ping
    ;;
  *) echo 'Usage: bash scripts/local-db.sh {start|stop|status}' >&2; exit 2 ;;
esac
