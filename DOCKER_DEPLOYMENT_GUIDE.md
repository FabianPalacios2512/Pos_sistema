# 🐋 Guía de Deployment con Docker - 105POS Pro

## 📋 Índice
1. [Arquitectura del Sistema](#arquitectura-del-sistema)
2. [Requisitos Previos](#requisitos-previos)
3. [Estructura del Proyecto](#estructura-del-proyecto)
4. [Configuración Inicial](#configuración-inicial)
5. [Deployment Completo](#deployment-completo)
6. [Deployment Rápido (Cambios de Código)](#-deployment-rápido-cambios-de-código)
7. [Comandos Útiles](#comandos-útiles)
8. [Troubleshooting](#troubleshooting)
9. [Actualizaciones y Mantenimiento](#actualizaciones-y-mantenimiento)
10. [Funcionalidades Especiales](#-funcionalidades-especiales)

---

## 🚨 INFORMACIÓN CRÍTICA PARA OTRAS IAs

### Datos de Conexión al VPS
```
IP del VPS: 72.61.73.245
Usuario SSH: root
Puerto SSH: 22
Contenedor Backend: pos_backend
Contenedor Frontend: pos_frontendIO 
Contenedor Database: pos_database
```

### Rutas del Proyecto
```
LOCAL:  /home/kali/Escritorio/definitivo/01_POS_BASICO (2)/
VPS:    /var/www/105pos/
```

### ⚡ Comandos de Deploy Rápido (USAR SIEMPRE)

**🚀 Deploy Incremental Inteligente (RECOMENDADO):**
```bash
# Script automático que solo sube archivos modificados
./deploy-fast.sh

# Primera vez: Sube todos los archivos (111 archivos)
# Siguientes veces: Solo archivos modificados (típicamente 1-5 archivos)
# Ahorro de tiempo: ~80-95% más rápido
```

**Para cambios en BACKEND (PHP/Laravel):**
```bash
# 1. Subir archivo modificado
scp backend/app/Http/Controllers/Api/ARCHIVO.php root@72.61.73.245:/tmp/

# 2. Copiar al contenedor y limpiar caché
ssh root@72.61.73.245 "docker cp /tmp/ARCHIVO.php pos_backend:/var/www/html/app/Http/Controllers/Api/ARCHIVO.php && docker exec pos_backend php artisan config:clear && docker exec pos_backend php artisan cache:clear"
```

**Para cambios en FRONTEND (Vue.js):**
```bash
# 1. Compilar localmente
cd "/home/kali/Escritorio/definitivo/01_POS_BASICO (2)" && npm run build

# 2. Subir dist al servidor
scp -r dist/* root@72.61.73.245:/tmp/dist/

# 3. Copiar al contenedor frontend
ssh root@72.61.73.245 "docker cp /tmp/dist/. pos_frontend:/usr/share/nginx/html/"
```

**Deploy completo (Backend + Frontend):**
```bash
# Compilar frontend
cd "/home/kali/Escritorio/definitivo/01_POS_BASICO (2)" && npm run build

# Subir archivo backend específico
scp backend/app/RUTA/ARCHIVO.php root@72.61.73.245:/tmp/

# Subir frontend compilado
scp -r dist/* root@72.61.73.245:/tmp/dist/

# Aplicar todo en el servidor
ssh root@72.61.73.245 "docker cp /tmp/ARCHIVO.php pos_backend:/var/www/html/app/RUTA/ARCHIVO.php && docker cp /tmp/dist/. pos_frontend:/usr/share/nginx/html/ && docker exec pos_backend php artisan config:clear"
```

---

## 🏗️ Arquitectura del Sistema

### Componentes Docker
```
┌─────────────────────────────────────────────────────────────┐
│                    Nginx (Host VPS)                         │
│              Puerto 80/443 → Reverse Proxy                  │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ↓
┌─────────────────────────────────────────────────────────────┐
│              Docker Compose Network (pos_network)           │
│                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌─────────────┐  │
│  │  Frontend    │    │   Backend    │    │  Database   │  │
│  │  (Nginx)     │◄───┤  (Laravel)   │◄───┤  (MySQL)    │  │
│  │  Puerto 80   │    │  Puerto 8000 │    │  Puerto 3306│  │
│  └──────────────┘    └──────────────┘    └─────────────┘  │
│   Vue.js + SPA        PHP 8.3 + Artisan   MySQL 8.0       │
└─────────────────────────────────────────────────────────────┘
```

### Sistema Multi-Tenant
- **Framework**: Laravel 11 con `stancl/tenancy`
- **Identificación**: Por dominio (ej: `tenant-name.105pos.pro`)
- **Base de datos**: Una DB por tenant (aislamiento completo)
- **Multi-sede/Warehouse**: Tabla `product_warehouse` para stock por bodega

### Flujo de Peticiones
1. Cliente → `https://tenant.105pos.pro`
2. Nginx Host → Puerto 8080 (Docker frontend)
3. Frontend Nginx → `/api/*` → Backend Laravel (puerto 8000)
4. Backend → MySQL Database (puerto 3306)

---

## 📦 Requisitos Previos

### En el VPS (Ubuntu 24.04)
```bash
# Instalar Docker Engine
curl -fsSL https://get.docker.com | sh

# Instalar Docker Compose
curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Verificar instalación
docker --version
docker-compose --version
```

### DNS Configurado
- Dominio: `105pos.pro` y `www.105pos.pro`
- Registro A apuntando a la IP del VPS: `72.61.73.245`

---

## 📂 Estructura del Proyecto

```
01_POS_BASICO (2)/
├── docker/
│   ├── backend.Dockerfile       # PHP 8.3-FPM + Laravel
│   ├── frontend.Dockerfile      # Node build + Nginx
│   └── nginx.conf               # Config Nginx para frontend
├── docker-compose.yml           # Orquestación de servicios
├── .env.docker                  # Variables de entorno producción
├── backend/                     # Código Laravel
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   ├── ExcelImportController.php  # Importador Excel con IA
│   │   │   └── ...
│   │   ├── Services/
│   │   │   ├── ExcelParserService.php     # Parser de archivos Excel/CSV
│   │   │   ├── AIColumnMapperService.php  # Mapeo con Groq AI
│   │   │   └── ...
│   │   └── Models/
│   │       ├── Product.php
│   │       ├── Warehouse.php
│   │       └── ...
│   ├── config/
│   ├── database/
│   ├── routes/
│   │   └── tenant_api.php       # Rutas API del tenant
│   └── ...
├── src/                         # Código Vue.js
│   ├── components/
│   │   ├── ExcelImportModal.vue # Modal importador de productos
│   │   └── ...
│   ├── views/
│   ├── store/
│   └── ...
└── 105pos-nginx.conf           # Config Nginx del host VPS
```

---

## ⚙️ Configuración Inicial

### 1. Archivo `.env.docker` (Variables de Producción)

```env
# Aplicación
APP_NAME="105POS Pro"
APP_ENV=production
APP_KEY=base64:CDXaXg11d7CNQ/HjnaHZxOes6hrETufg5rX7vcrCLl0=
APP_DEBUG=false
APP_URL=https://105pos.pro

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=database
DB_PORT=3306
DB_DATABASE=pos_production
DB_USERNAME=pos_user
DB_PASSWORD=Pos2024Secure!Pass

# Cache y Sesiones
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Otros
LOG_CHANNEL=stack
LOG_LEVEL=error
```

**⚠️ IMPORTANTE**: El `APP_KEY` se genera con:
```bash
php artisan key:generate --show
```

### 2. Dockerfile Backend (`docker/backend.Dockerfile`)

**Características clave:**
- Base: `php:8.3-fpm`
- Extensiones: pdo_mysql, mbstring, gd, zip, intl, opcache
- Composer con `--no-dev --optimize-autoloader`
- Permisos correctos: `www-data:www-data`
- PHP-FPM optimizado: 50 max children, 10 start servers

### 3. Dockerfile Frontend (`docker/frontend.Dockerfile`)

**Multi-stage build:**
- **Stage 1**: Node 20 Alpine → `npm ci` + `npm run build`
- **Stage 2**: Nginx Alpine → Copia `dist/` + config Nginx

### 4. Docker Compose (`docker-compose.yml`)

**Servicios:**
1. **database**: MySQL 8.0 con health check
2. **backend**: Laravel con `php artisan serve` en puerto 8000
3. **frontend**: Nginx sirviendo Vue SPA en puerto 80

**Redes y Volúmenes:**
- Red: `pos_network` (bridge)
- Volúmenes persistentes:
  - `mysql_data`: Datos de MySQL
  - `backend_storage`: Laravel storage/

---

## 🚀 Deployment Completo

### Paso 1: Preparar el Proyecto Local

```bash
# Navegar al directorio del proyecto
cd '/home/kali/Escritorio/definitivo/01_POS_BASICO (2)'

# Asegurarse que .env.docker existe
ls -la .env.docker
```

### Paso 2: Subir Proyecto al VPS

```bash
# Crear directorio en VPS
ssh root@72.61.73.245 "mkdir -p /var/www/105pos"

# Sincronizar proyecto (excluyendo node_modules, vendor, .git)
rsync -avz --progress \
  --exclude 'node_modules' \
  --exclude 'vendor' \
  --exclude '.git' \
  --exclude 'storage/logs/*' \
  --exclude 'bootstrap/cache/*' \
  . root@72.61.73.245:/var/www/105pos/
```

### Paso 3: Configurar Variables de Entorno

```bash
# Copiar .env.docker a múltiples ubicaciones
ssh root@72.61.73.245 "cd /var/www/105pos && \
  cp .env.docker backend/.env && \
  cp .env.docker .env"
```

### Paso 4: Construir Imágenes Docker

```bash
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose build"
```

**Tiempo estimado**: 5-10 minutos (primera vez)

### Paso 5: Iniciar Servicios

```bash
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose up -d"
```

### Paso 6: Verificar Contenedores

```bash
ssh root@72.61.73.245 "docker-compose -f /var/www/105pos/docker-compose.yml ps"
```

**Salida esperada:**
```
NAME           STATUS              PORTS
pos_backend    Up X minutes        8000/tcp
pos_database   Up X minutes (healthy)
pos_frontend   Up X minutes        0.0.0.0:8080->80/tcp
```

### Paso 7: Configurar Nginx en el Host

```bash
# Subir configuración de Nginx
scp 105pos-nginx.conf root@72.61.73.245:/etc/nginx/sites-available/105pos

# Activar site
ssh root@72.61.73.245 "ln -sf /etc/nginx/sites-available/105pos /etc/nginx/sites-enabled/"

# Verificar sintaxis
ssh root@72.61.73.245 "nginx -t"

# Recargar Nginx
ssh root@72.61.73.245 "systemctl reload nginx"
```

### Paso 8: Configurar SSL con Certbot

```bash
ssh root@72.61.73.245 "certbot --nginx \
  -d 105pos.pro \
  -d www.105pos.pro \
  --non-interactive \
  --agree-tos \
  --email admin@105pos.pro \
  --redirect"
```

### Paso 9: Ejecutar Migraciones (Opcional)

```bash
# Migrar base de datos central
ssh root@72.61.73.245 "docker-compose -f /var/www/105pos/docker-compose.yml exec -T backend php artisan migrate --force"

# Si usas multi-tenancy, crear tenant inicial
ssh root@72.61.73.245 "docker-compose -f /var/www/105pos/docker-compose.yml exec -T backend php artisan tenants:seed"
```

---

## 🛠️ Comandos Útiles

### Ver Logs en Tiempo Real

```bash
# Todos los servicios
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose logs -f"

# Solo backend
ssh root@72.61.73.245 "docker logs -f pos_backend"

# Solo frontend
ssh root@72.61.73.245 "docker logs -f pos_frontend"

# Solo database
ssh root@72.61.73.245 "docker logs -f pos_database"
```

### Reiniciar Servicios

```bash
# Reiniciar todos
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose restart"

# Reiniciar solo backend
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose restart backend"
```

### Detener y Eliminar Contenedores

```bash
# Detener servicios (conserva datos)
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose down"

# Detener y eliminar volúmenes (⚠️ ELIMINA BASE DE DATOS)
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose down -v"
```

### Ejecutar Comandos en Contenedores

```bash
# Artisan commands
ssh root@72.61.73.245 "docker-compose -f /var/www/105pos/docker-compose.yml exec backend php artisan cache:clear"

# Composer
ssh root@72.61.73.245 "docker-compose -f /var/www/105pos/docker-compose.yml exec backend composer install"

# Shell interactivo
ssh root@72.61.73.245 "docker exec -it pos_backend bash"
```

### Backup de Base de Datos

```bash
# Crear backup
ssh root@72.61.73.245 "docker exec pos_database mysqldump -u pos_user -pPos2024Secure!Pass pos_production > /root/backup_$(date +%Y%m%d).sql"

# Restaurar backup
ssh root@72.61.73.245 "docker exec -i pos_database mysql -u pos_user -pPos2024Secure!Pass pos_production < /root/backup_20251211.sql"
```

---

## 🔧 Troubleshooting

### Error 502 Bad Gateway

**Síntoma**: `POST /api/... 502 (Bad Gateway)`

**Causa**: Backend no está respondiendo en el puerto correcto

**Solución**:
```bash
# Verificar logs del backend
ssh root@72.61.73.245 "docker logs pos_backend --tail 50"

# Asegurarse que sirve en puerto 8000
# Debe mostrar: "Server running on [http://0.0.0.0:8000]"

# Si no, verificar docker-compose.yml línea command:
# command: php artisan serve --host=0.0.0.0 --port=8000
```

### Contenedor Backend en Loop (Reiniciando)

**Síntoma**: `STATUS: Restarting`

**Causa**: Falta archivo `vendor/autoload.php`

**Solución**:
```bash
# El volumen ./backend:/var/www/html está montando el directorio local
# que NO tiene vendor/ sobre la imagen Docker que SÍ tiene vendor/
# QUITAR ese volumen del docker-compose.yml

# En docker-compose.yml, sección backend:
# ELIMINAR: - ./backend:/var/www/html
# MANTENER: - backend_storage:/var/www/html/storage
```

### Error de Conexión a Base de Datos

**Síntoma**: `SQLSTATE[HY000] [2002] Connection refused`

**Solución**:
```bash
# Verificar que database está healthy
ssh root@72.61.73.245 "docker-compose ps"

# Verificar variables de entorno
ssh root@72.61.73.245 "cat /var/www/105pos/.env | grep DB_"

# Debe mostrar:
# DB_HOST=database (nombre del servicio, NO localhost)
# DB_PORT=3306
```

### Imágenes No Se Actualizan

**Síntoma**: Cambios de código no se reflejan en producción

**Solución**:
```bash
# Rebuild sin cache
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose build --no-cache"

# Recrear contenedores
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose up -d --force-recreate"
```

---

## 🔄 Actualizaciones y Mantenimiento

### Actualizar Código (Deploy Incremental)

```bash
# 1. Sincronizar cambios
rsync -avz --progress \
  --exclude 'node_modules' \
  --exclude 'vendor' \
  --exclude '.git' \
  . root@72.61.73.245:/var/www/105pos/

# 2. Rebuild solo las imágenes necesarias
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose build backend frontend"

# 3. Recrear contenedores
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose up -d --force-recreate backend frontend"

# 4. Limpiar cache de Laravel
ssh root@72.61.73.245 "docker-compose -f /var/www/105pos/docker-compose.yml exec backend php artisan cache:clear"
ssh root@72.61.73.245 "docker-compose -f /var/www/105pos/docker-compose.yml exec backend php artisan config:cache"
ssh root@72.61.73.245 "docker-compose -f /var/www/105pos/docker-compose.yml exec backend php artisan route:cache"
```

### Actualizar Dependencias

**Backend (Composer):**
```bash
# Local: actualizar composer.lock
composer update

# Subir y rebuild
scp backend/composer.lock root@72.61.73.245:/var/www/105pos/backend/
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose build --no-cache backend"
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose up -d backend"
```

**Frontend (NPM):**
```bash
# Local: actualizar package-lock.json
npm update

# Subir y rebuild
scp package-lock.json root@72.61.73.245:/var/www/105pos/
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose build --no-cache frontend"
ssh root@72.61.73.245 "cd /var/www/105pos && docker-compose up -d frontend"
```

### Renovar Certificado SSL

```bash
# Certbot renueva automáticamente, pero puedes forzarlo:
ssh root@72.61.73.245 "certbot renew --force-renewal"
ssh root@72.61.73.245 "systemctl reload nginx"
```

### Limpieza de Recursos Docker

```bash
# Eliminar imágenes no usadas
ssh root@72.61.73.245 "docker image prune -a"

# Eliminar contenedores detenidos
ssh root@72.61.73.245 "docker container prune"

# Eliminar volúmenes no usados
ssh root@72.61.73.245 "docker volume prune"

# Limpieza completa del sistema
ssh root@72.61.73.245 "docker system prune -a --volumes"
```

---

## 📊 Monitoreo

### Ver Estado de Recursos

```bash
# CPU y memoria de contenedores
ssh root@72.61.73.245 "docker stats --no-stream"

# Espacio en disco
ssh root@72.61.73.245 "df -h"
ssh root@72.61.73.245 "docker system df"
```

### Logs de Acceso Nginx

```bash
# Logs del host
ssh root@72.61.73.245 "tail -f /var/log/nginx/105pos_access.log"

# Logs del contenedor frontend
ssh root@72.61.73.245 "docker logs pos_frontend --tail 100"
```

---

## 🔐 Seguridad

### Firewall (UFW)

```bash
# Permitir solo puertos necesarios
ssh root@72.61.73.245 "ufw allow 22/tcp"   # SSH
ssh root@72.61.73.245 "ufw allow 80/tcp"   # HTTP
ssh root@72.61.73.245 "ufw allow 443/tcp"  # HTTPS
ssh root@72.61.73.245 "ufw enable"
```

### Actualizar Sistema

```bash
ssh root@72.61.73.245 "apt update && apt upgrade -y"
ssh root@72.61.73.245 "reboot"  # Si es necesario
```

---

## 📞 Información de Contacto

- **Dominio**: https://105pos.pro
- **VPS IP**: 72.61.73.245
- **Sistema**: Ubuntu 24.04 LTS
- **Docker**: 29.1.2
- **Docker Compose**: v5.0.0

---

## 📝 Notas Importantes

1. **Nunca** hacer `docker-compose down -v` en producción (elimina la base de datos)
2. **Siempre** hacer backup antes de actualizaciones mayores
3. **El archivo** `.env.docker` contiene credenciales sensibles - NO subir a Git
4. **Los volúmenes** `mysql_data` y `backend_storage` persisten entre reinicios
5. **Si cambias** el `APP_KEY`, invalida todas las sesiones activas

---

## 🎯 Funcionalidades Especiales

### 📊 Importador Inteligente de Productos (Excel/CSV con IA)

**Archivos involucrados:**
- `backend/app/Http/Controllers/Api/ExcelImportController.php`
- `backend/app/Services/ExcelParserService.php`
- `backend/app/Services/AIColumnMapperService.php`
- `src/components/ExcelImportModal.vue`
- `backend/routes/tenant_api.php` (rutas públicas sin auth)

**Características:**
1. **Wizard de 4 pasos**: Subir → Mapear → Revisar → Importar
2. **IA con Groq**: Usa modelo `llama-3.3-70b-versatile` para detectar columnas automáticamente
3. **14 API Keys de Groq**: Rotación automática para evitar rate limits
4. **Soporte multi-warehouse**: Asigna stock automáticamente a bodega principal
5. **Soporte de imágenes**: Columna para pegar/subir URL de imagen por producto
6. **Rutas públicas**: `/api/excel-import/*` no requieren autenticación (para onboarding)

**Endpoints:**
```
POST /api/excel-import/upload   → Sube archivo y analiza con IA
POST /api/excel-import/preview  → Genera preview con datos mapeados
POST /api/excel-import/import   → Importa productos a la DB
GET  /api/excel-import/template → Descarga plantilla CSV de ejemplo
POST /api/excel-import/cancel   → Cancela importación en progreso
```

**Campos soportados:**
- `name` (requerido) - Nombre del producto
- `sale_price` (requerido) - Precio de venta
- `cost_price` - Precio de costo
- `current_stock` - Stock actual
- `sku` - Código SKU
- `barcode` - Código de barras
- `category` - Categoría (crea si no existe)
- `description` - Descripción
- `min_stock` - Stock mínimo
- `wholesale_price` - Precio mayorista
- `image_url` - URL de imagen del producto

**Troubleshooting común:**
- Error `is_main column not found`: La tabla warehouses de algunos tenants no tiene esa columna. El código ya maneja esto buscando `Warehouse::first()`.
- Error 500 en import: Revisar logs con `docker exec pos_backend tail -100 /var/www/html/storage/logs/laravel.log`

---

### 🏪 Sistema Multi-Tenant

**Modelo**: Una base de datos por tenant
**Middleware**: `InitializeTenancyByDomain`
**Dominio**: `{tenant_id}.105pos.pro`

**Comandos Tinker útiles:**
```bash
# Listar todos los tenants
ssh root@72.61.73.245 "docker exec pos_backend php artisan tinker --execute=\"App\\Models\\Tenant::all(['id'])->pluck('id');\""

# Ejecutar código en contexto de un tenant
ssh root@72.61.73.245 "docker exec pos_backend php artisan tinker --execute=\"
tenancy()->initialize('nombre_tenant');
// ... código aquí
\""

# Eliminar productos de un tenant
ssh root@72.61.73.245 "docker exec pos_backend php artisan tinker --execute=\"
tenancy()->initialize('nombre_tenant');
DB::table('product_warehouse')->delete();
App\\Models\\Product::query()->delete();
\""
```

---

### 📦 Sistema Multi-Warehouse (Multi-Sede)

**Tablas:**
- `warehouses` - Lista de bodegas/sedes
- `product_warehouse` - Tabla pivote con stock por bodega

**Relación:**
```php
// En Product.php
public function warehouses() {
    return $this->belongsToMany(Warehouse::class, 'product_warehouse')
                ->withPivot('stock');
}

// current_stock = SUM de todos los stocks en product_warehouse
```

**Al importar productos:**
El sistema automáticamente:
1. Busca la primera bodega disponible
2. Si no existe ninguna, crea "Bodega Principal"
3. Asigna el stock al pivot `product_warehouse`
4. Actualiza `current_stock` del producto

---

## 🔄 Flujo de Trabajo para Cambios

### Para CUALQUIER cambio de código, seguir este flujo:

```
1. Hacer cambios localmente
2. Si es Frontend (Vue): npm run build
3. Subir archivos al servidor (scp)
4. Copiar al contenedor Docker (docker cp)
5. Limpiar caché si es Backend (php artisan cache:clear)
```

### Ejemplo completo de un cambio típico:

```bash
# === CAMBIO EN BACKEND ===
# Editar archivo localmente, luego:
cd "/home/kali/Escritorio/definitivo/01_POS_BASICO (2)"

scp backend/app/Http/Controllers/Api/ExcelImportController.php root@72.61.73.245:/tmp/

ssh root@72.61.73.245 "docker cp /tmp/ExcelImportController.php pos_backend:/var/www/html/app/Http/Controllers/Api/ExcelImportController.php && docker exec pos_backend php artisan config:clear && docker exec pos_backend php artisan cache:clear"

# === CAMBIO EN FRONTEND ===
cd "/home/kali/Escritorio/definitivo/01_POS_BASICO (2)"
npm run build

scp -r dist/* root@72.61.73.245:/tmp/dist/

ssh root@72.61.73.245 "docker cp /tmp/dist/. pos_frontend:/usr/share/nginx/html/"
```

---

## 🐛 Debug y Logs

### Ver logs de Laravel en tiempo real:
```bash
ssh root@72.61.73.245 "docker exec pos_backend tail -f /var/www/html/storage/logs/laravel.log"
```

### Ver errores específicos:
```bash
ssh root@72.61.73.245 "docker exec pos_backend tail -200 /var/www/html/storage/logs/laravel.log | grep -A 20 'Error\|Exception'"
```

### Ver logs de Nginx frontend:
```bash
ssh root@72.61.73.245 "docker logs pos_frontend --tail 100"
```

---

**Fecha de última actualización**: 13 de diciembre de 2025
**Autor**: Sistema automatizado de deployment 105POS Pro
