# Despliegue en VPS (Hostinger) por cliente

Este proyecto tiene frontend (Vue + Vite), backend (Laravel PHP 8.2) y un microservicio de WhatsApp (Node + Baileys). Aquí automatizamos el despliegue por cliente, sin interrumpir servicios existentes, usando puertos separados por instancia.

## Estructura por cliente

- /var/www/<cliente>/
  - frontend/ (build estático "dist/")
  - backend/ (Laravel; public/index.php)
  - whatsapp/ (el mismo backend, ejecuta `whatsapp-server.js` vía PM2)

Sugerimos clonar/copiar el repositorio completo dentro de `/var/www/<cliente>` y usar estos subdirectorios lógicos.

## Requisitos en el VPS

- Nginx
- PHP 8.2 + php-fpm + extensiones típicas (pdo_mysql, mbstring, bcmath, intl, zip, gd)
- MySQL/MariaDB (se recomienda crear una DB por cliente)
- Node.js 18+ y PM2 para el servicio de WhatsApp
- Certbot (si usarás HTTPS con dominios)

## Puertos y no-interrupción

- Nginx puede escuchar en un puerto único por cliente (p. ej. 8081, 8082…) si aún no tienes dominio. Así no tocamos vhosts/productivos.
- El microservicio WhatsApp usa un puerto interno único por cliente (p. ej. 32001, 32002…). Ya está parametrizado por `WHATSAPP_PORT`.

## Pasos rápidos (manuales)

1) Copia código al VPS

- En el VPS:
  - `mkdir -p /var/www/CLIENTE`
  - Sube el código (scp/rsync/git) dentro de `/var/www/CLIENTE`.

2) Backend (Laravel)

- `cd /var/www/CLIENTE/backend`
- `cp .env.example .env`
- Edita `.env`:
  - APP_KEY: genera con `php artisan key:generate`
  - APP_URL: http://IP:PUERTO_NGINX o tu dominio
  - DB_CONNECTION=mysql
  - DB_HOST=127.0.0.1
  - DB_PORT=3306
  - DB_DATABASE=cliente_db
  - DB_USERNAME=root
  - DB_PASSWORD=""  (según tu entorno)
- Instala dependencias y prepara la app:
  - `composer install --no-dev --optimize-autoloader`
  - `php artisan key:generate`
  - `php artisan migrate --force`
  - `php artisan db:seed --force` (crea usuario admin: CC 1001504182 / password admin123)
  - `php artisan storage:link`
  - `php artisan config:cache && php artisan route:cache && php artisan view:cache`

3) Frontend (Vue)

- `cd /var/www/CLIENTE`
- `npm ci` (o `npm install`)
- `npm run build`  → genera `/dist`
- Mueve o sirve `/dist` como raíz estática del vhost Nginx.

4) WhatsApp (PM2)

- `cd /var/www/CLIENTE/backend`
- `npm ci` (o `npm install`)
- Asigna un puerto libre (ej: 32001) y arranca:
  - `WHATSAPP_PORT=32001 pm2 start whatsapp-server.js --name whatsapp-CLIENTE`
- Revisión:
  - `curl http://127.0.0.1:32001/status`
  - Si es la primera vez: `curl http://127.0.0.1:32001/qr` para obtener el QR y vincular el dispositivo.

5) Nginx (sitio por cliente)

Usa el template `nginx-site-template.conf` y reemplaza variables:

- {LISTEN_PORT} → ej. 8081
- {SERVER_NAME} → dominio o `_` si no hay
- {CLIENT_ROOT} → `/var/www/CLIENTE`
- {WHATSAPP_PORT} → ej. 32001

Habilita el sitio y recarga Nginx:

- `ln -s /etc/nginx/sites-available/CLIENTE.conf /etc/nginx/sites-enabled/`
- `nginx -t && systemctl reload nginx`

6) Persistencia PM2

- `pm2 save`
- `pm2 startup` (sigue las instrucciones para systemd)

## Template de Nginx

Revisa `deploy/nginx-site-template.conf`.

## Script opcional de onboarding

`deploy/client-onboard.sh` contiene una guía semi-automatizada para crear un cliente nuevo con puertos dedicados. Revísalo/edítalo antes de usarlo.

## Notas

- Para SSL, una vez tengas `server_name`, usa Certbot en el vhost definitivo.
- No edites servicios existentes, usa nuevos puertos para pruebas.
- WhatsApp: cada cliente tendrá una sesión independiente (carpeta `whatsapp_session` dentro de su backend).
