FROM php:8.4-apache AS php-base
RUN apt-get update && apt-get install -y --no-install-recommends libsqlite3-dev libonig-dev libcurl4-openssl-dev unzip git && docker-php-ext-install pdo_sqlite mbstring opcache curl && a2enmod rewrite headers && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
FROM php-base AS dependencies
COPY application/composer.json application/composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --no-autoloader
COPY application/ ./
RUN composer dump-autoload --no-dev --optimize --no-scripts && composer check-platform-reqs --no-dev
FROM node:22-bookworm-slim AS assets
WORKDIR /build
COPY application/package.json application/package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY application/resources ./resources
COPY application/vite.config.js application/postcss.config.js application/tailwind.config.js ./
COPY --from=dependencies /var/www/html/vendor/laravel/framework/src/Illuminate/Pagination/resources/views ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views
RUN npm run build
FROM php-base AS runtime
COPY --from=dependencies /var/www/html /var/www/html
COPY --from=assets /build/public/build /var/www/html/public/build
COPY docker/apache-demo.conf /etc/apache2/sites-available/000-default.conf
COPY docker/demo-bootstrap.php /usr/local/bin/demo-bootstrap.php
COPY docker/entrypoint-demo.sh /usr/local/bin/entrypoint-demo
RUN chmod 755 /usr/local/bin/entrypoint-demo && mkdir -p storage/app/private storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache && chown -R www-data:www-data storage bootstrap/cache
ENV APP_ENV=production APP_DEBUG=false DB_CONNECTION=sqlite DB_DATABASE=/tmp/carloc-demo.sqlite MAIL_MAILER=array QUEUE_CONNECTION=sync SESSION_DRIVER=file CACHE_STORE=file LOG_CHANNEL=stderr LOG_LEVEL=warning
EXPOSE 10000
ENTRYPOINT ["/usr/local/bin/entrypoint-demo"]
