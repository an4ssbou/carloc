#!/bin/sh
set -eu
cd /var/www/html
export APP_ENV="${APP_ENV:-production}" APP_DEBUG="${APP_DEBUG:-false}"
export DB_CONNECTION="${DB_CONNECTION:-sqlite}" DB_DATABASE="${DB_DATABASE:-/tmp/carloc-demo.sqlite}"
export MAIL_MAILER=array QUEUE_CONNECTION=sync SESSION_DRIVER=file CACHE_STORE=file
export APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-}}"
php /usr/local/bin/demo-bootstrap.php
find bootstrap/cache -maxdepth 1 -type f -name '*.php' -delete
find storage/framework/sessions storage/framework/cache/data -type f -delete
if [ -z "${APP_KEY:-}" ]; then APP_KEY="$(php -r 'echo "base64:".base64_encode(random_bytes(32));')"; export APP_KEY; fi
if [ "${DEMO_ALLOW_LOCAL_HTTP:-false}" = true ]; then export SESSION_SECURE_COOKIE=false; else export SESSION_SECURE_COOKIE=true; fi
php artisan package:discover --ansi --no-interaction
php artisan migrate --force --no-interaction
php artisan db:seed --class=PublicDemoSeeder --force --no-interaction
php artisan storage:link --no-interaction
php artisan config:cache --no-interaction
php artisan view:cache --no-interaction
chown -R www-data:www-data /tmp/carloc-demo.sqlite storage bootstrap/cache
case "${PORT:-10000}" in *[!0-9]*|'') echo 'Port invalide' >&2; exit 1;; esac
sed -i "s/Listen 80/Listen ${PORT:-10000}/" /etc/apache2/ports.conf
sed -i "s/:10000>/:${PORT:-10000}>/" /etc/apache2/sites-available/000-default.conf
exec apache2-foreground
