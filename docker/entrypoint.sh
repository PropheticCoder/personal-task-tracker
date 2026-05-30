#!/bin/sh

# Fix storage permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "▶ Caching Laravel config/routes/views..."
php artisan config:cache && echo "  ✅ Config cached" || echo "  ⚠ Config cache failed"
php artisan route:cache && echo "  ✅ Routes cached" || echo "  ⚠ Route cache failed"
php artisan view:cache && echo "  ✅ Views cached" || echo "  ⚠ View cache failed"

echo "▶ Running migrations..."
php artisan migrate --force && echo "  ✅ Migrations complete" || echo "  ⚠ Migrations failed — continuing..."

echo "▶ Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
