#!/bin/sh
set -e

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
