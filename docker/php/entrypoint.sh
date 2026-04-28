#!/bin/sh
set -e

# Corregir permisos en cada arranque (necesario con bind-mount en Windows)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

exec php-fpm
