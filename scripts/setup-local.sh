#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
app="${CARLOC_APP_DIR:-$root/application}"
for binary in php composer npm rg mariadb mariadbd mariadb-install-db mariadb-admin mariadb-dump; do
  command -v "$binary" >/dev/null || { echo "Prérequis absent : $binary" >&2; exit 1; }
done
php -r 'exit(extension_loaded("pdo_mysql") ? 0 : 1);' || { echo 'Extension PHP pdo_mysql requise.' >&2; exit 1; }
mkdir -p "$app/storage/framework/cache/data" "$app/storage/framework/sessions" "$app/storage/framework/views" "$app/storage/logs" "$app/bootstrap/cache" "$app/storage/app/private" "$app/storage/app/public"
bash "$root/scripts/local-db.sh" start
cd "$app"
if [ ! -f .env ]; then
  umask 077
  cat > .env <<EOF
APP_NAME=CarLoc
APP_ENV=local
APP_KEY=
APP_DEBUG=false
APP_URL=http://127.0.0.1:8000
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
DB_CONNECTION=mysql
DB_HOST=localhost
DB_SOCKET=$app/storage/local-db/db.sock
DB_DATABASE=location_demo
DB_USERNAME=root
DB_PASSWORD=
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
MAIL_MAILER=array
MAIL_FROM_ADDRESS=demo@carloc.test
MAIL_FROM_NAME=CarLoc
LOG_CHANNEL=single
LOG_LEVEL=error
GPS_SIMULATION_ENABLED=true
EOF
fi
composer install --no-interaction --prefer-dist
if ! rg -q '^APP_KEY=base64:.+' .env; then
  php artisan key:generate --no-interaction --quiet
fi
php artisan migrate --force --no-interaction
php artisan db:seed --class=DemoSeeder --force --no-interaction
if [ ! -L public/storage ]; then
  php artisan storage:link --no-interaction
fi
npm ci --no-audit --no-fund
npm run build
echo 'Installation locale prête. Lancer : bash scripts/start-local.sh'
