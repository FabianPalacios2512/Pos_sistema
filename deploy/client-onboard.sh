#!/usr/bin/env bash
# Onboarding semi-automático para crear un cliente
# Uso: sudo ./deploy/client-onboard.sh cliente_slug 8081 32001
#  - cliente_slug: nombre de carpeta y etiquetas (sin espacios)
#  - 8081: puerto Nginx para pruebas (no 80/443)
#  - 32001: puerto WhatsApp interno

set -euo pipefail

if [[ $(id -u) -ne 0 ]]; then
  echo "Este script debe ejecutarse como root (sudo)." >&2
  exit 1
fi

CLIENT=${1:-}
NGINX_PORT=${2:-}
WHATSAPP_PORT=${3:-}

if [[ -z "$CLIENT" || -z "$NGINX_PORT" || -z "$WHATSAPP_PORT" ]]; then
  echo "Parámetros faltantes. Ejemplo: sudo ./deploy/client-onboard.sh tienda1 8081 32001" >&2
  exit 1
fi

BASE=/var/www/$CLIENT
REPO_SRC=$(cd "$(dirname "$0")/.." && pwd)

# 1) Copia código
mkdir -p "$BASE"
rsync -a --delete --exclude node_modules --exclude vendor --exclude storage/logs --exclude .git "$REPO_SRC/" "$BASE/"

# 2) Backend
cd "$BASE/backend"
cp -n .env.example .env || true
sed -i "s|^APP_URL=.*|APP_URL=http://127.0.0.1:$NGINX_PORT|" .env
sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=mysql|" .env
sed -i "s|^# DB_HOST=.*|DB_HOST=127.0.0.1|" .env
sed -i "s|^# DB_PORT=.*|DB_PORT=3306|" .env
sed -i "s|^# DB_DATABASE=.*|DB_DATABASE=${CLIENT}_db|" .env
sed -i "s|^# DB_USERNAME=.*|DB_USERNAME=root|" .env
sed -i "s|^# DB_PASSWORD=.*|DB_PASSWORD=|" .env

# WHATSAPP_PORT lo usamos vía PM2 env, no en .env por defecto

# Crear DB si existe mysql y root sin contraseña
if command -v mysql >/dev/null 2>&1; then
  mysql -uroot -e "CREATE DATABASE IF NOT EXISTS ${CLIENT}_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || true
fi

# Composer
if ! command -v composer >/dev/null 2>&1; then
  echo "Instala composer primero" >&2
  exit 1
fi
composer install --no-dev --optimize-autoloader
php artisan key:generate || true
php artisan migrate --force || true
php artisan db:seed --force || true
php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# 3) Frontend build (raíz)
cd "$BASE"
if command -v npm >/dev/null 2>&1; then
  npm install --omit=dev || npm install
  npm run build || true
else
  echo "npm no encontrado; construye el frontend manualmente" >&2
fi

# 4) PM2 WhatsApp
cd "$BASE/backend"
if command -v pm2 >/dev/null 2>&1; then
  npm install || true
  WHATSAPP_PORT=$WHATSAPP_PORT pm2 start whatsapp-server.js --name whatsapp-$CLIENT || pm2 restart whatsapp-$CLIENT
  pm2 save
else
  echo "PM2 no encontrado. Instala con: npm i -g pm2" >&2
fi

# 5) Nginx site
SITE=/etc/nginx/sites-available/$CLIENT.conf
TPL="$REPO_SRC/deploy/nginx-site-template.conf"
if [[ -f "$TPL" ]]; then
  sed -e "s|{LISTEN_PORT}|$NGINX_PORT|g" \
      -e "s|{SERVER_NAME}|_|g" \
      -e "s|{CLIENT_ROOT}|$BASE|g" \
      -e "s|{WHATSAPP_PORT}|$WHATSAPP_PORT|g" "$TPL" > "$SITE"
  ln -snf "$SITE" /etc/nginx/sites-enabled/$CLIENT.conf
  nginx -t && systemctl reload nginx
else
  echo "No se encontró template de Nginx: $TPL" >&2
fi

echo "\nListo. Prueba: http://<IP>:$NGINX_PORT  | WhatsApp: proxied en /whatsapp/ (puerto $WHATSAPP_PORT interno)"