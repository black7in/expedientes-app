#!/bin/sh
set -e

echo "→ Sincronizando assets compilados..."
cp -rf /tmp/public-build /var/www/html/public/build

echo "→ Ejecutando migraciones..."
php artisan migrate --force

echo "→ Cacheando configuración, rutas y vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "→ Ajustando permisos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "→ Iniciando PHP-FPM..."
exec php-fpm
