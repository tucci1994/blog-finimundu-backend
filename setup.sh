#!/bin/bash
set -e

echo ""
echo "======================================================"
echo "  Blog API — Setup automatico"
echo "======================================================"
echo ""

WORK="/var/www"
TMP="/tmp/laravel-fresh"

echo "[1/6] Installazione Laravel 11..."
composer create-project laravel/laravel "$TMP" --quiet

echo "[2/6] Copia scaffolding Laravel..."
for item in "$TMP"/*; do
    name=$(basename "$item")
    case "$name" in
        app|routes|database|bootstrap|docker|docker-compose.yml|.env|setup.sh|README.md)
            continue ;;
    esac
    cp -r "$item" "$WORK/"
done
# Copy hidden files except .env
for item in "$TMP"/.[!.]*; do
    name=$(basename "$item")
    [ "$name" = ".env" ] && continue
    cp -r "$item" "$WORK/" 2>/dev/null || true
done

echo "[3/6] Installazione Laravel Sanctum..."
cd "$WORK"
composer require laravel/sanctum --quiet

echo "[4/6] Configurazione .env..."
cat > "$WORK/.env" << 'ENVEOF'
APP_NAME="Blog API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Europe/Rome

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=blog_api
DB_USERNAME=blog
DB_PASSWORD=blog

SESSION_DRIVER=database
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
CACHE_STORE=file

SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:5173
ENVEOF

php artisan key:generate

echo "[5/6] Pubblicazione config Sanctum..."
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --quiet || true
php artisan migrate --force

echo "[6/6] Seed utenti di test..."
php artisan db:seed --class=AdminUserSeeder --force
php artisan storage:link 2>/dev/null || true
chmod -R 777 "$WORK/storage" "$WORK/bootstrap/cache"

echo ""
echo "======================================================"
echo "  Setup completato!"
echo "======================================================"
echo "  API:  http://localhost:8000/api/v1"
echo ""
echo "  admin@blog.it  / password  (admin)"
echo "  editor@blog.it / password  (editor)"
echo "  author@blog.it / password  (author)"
echo "======================================================"
echo ""
