# 🔧 Solución al Error 404 en Web Catalog Config

## 🎯 Problema
Al intentar guardar la configuración en el Catálogo Web, aparece el error:
```
Error al guardar la configuración.
XHR GET/POST https://sai-a.105pos.pro/api/web-catalog/config [HTTP/1.1 404 Not Found]
```

## ✅ Solución Aplicada

### Cambios Realizados:
1. **Reorganización de rutas** en `backend/routes/tenant_api.php`
   - Las rutas de web-catalog ahora están mejor organizadas
   - Endpoint de debug agregado: `/api/web-catalog/debug-test`

### 📋 Pasos para Resolver en Producción:

#### Opción 1: Deploy Automático (Git)
```bash
# 1. Commit y push de los cambios
git add backend/routes/tenant_api.php
git commit -m "Fix: Reorganizar rutas de web-catalog para resolver 404"
git push origin main

# 2. El VPS debería recibir automáticamente los cambios
# Si tienes GitHub Actions configurado, espera a que termine el deploy
```

#### Opción 2: Deploy Manual
```bash
# Ejecutar desde la raíz del proyecto:
./deploy-manual.sh
```

#### Opción 3: Ejecutar Comandos Directamente en el VPS
Si tienes acceso SSH al VPS, ejecuta:

```bash
# Conectarse al VPS
ssh root@142.93.24.42

# Ir al directorio del proyecto
cd /var/www/105pos/backend

# Limpiar todas las caches
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Recrear cache optimizada
php artisan config:cache
php artisan route:cache

# Verificar que las rutas existan
php artisan route:list --path=web-catalog

# Reiniciar PHP-FPM
sudo systemctl restart php8.3-fpm

# Salir del VPS
exit
```

## 🔍 Verificación

### 1. Probar el endpoint de debug:
Abre en el navegador (o usa Postman):
```
https://sai-a.105pos.pro/api/web-catalog/debug-test
```

**Respuesta esperada:**
```json
{
  "success": true,
  "message": "Route works! tenant_api.php is loaded",
  "tenant_id": "algún-id",
  "controller_exists": true,
  "timestamp": "2025-12-16 ..."
}
```

### 2. Si el debug funciona pero las rutas principales no:
- **Problema**: El middleware de autenticación está bloqueando
- **Solución**: Verificar que el token de autenticación se esté enviando correctamente
- Abrir DevTools → Network → Headers y verificar que exista:
  ```
  Authorization: Bearer <token>
  ```

### 3. Si el debug tampoco funciona:
- **Problema**: El dominio `sai-a.105pos.pro` no está configurado como tenant
- **Solución**: Verificar en la base de datos:
  ```sql
  SELECT * FROM domains WHERE domain = 'sai-a.105pos.pro';
  ```

## 🐛 Debugging Adicional

### Verificar logs de Laravel en el VPS:
```bash
ssh root@142.93.24.42
tail -f /var/www/105pos/backend/storage/logs/laravel.log
```

### Verificar logs de Nginx:
```bash
ssh root@142.93.24.42
tail -f /var/log/nginx/error.log
```

## 📝 Notas Importantes

1. **Las rutas están en `tenant_api.php`**: Esto significa que solo funcionan para subdominios tenant (como `sai-a.105pos.pro`)
2. **Requieren autenticación**: Las rutas principales usan `auth:sanctum`, así que el usuario debe estar logueado
3. **Cache de rutas**: Después de cualquier cambio en rutas, SIEMPRE ejecutar `php artisan route:cache`

## 🎯 Próximos Pasos

1. Hacer commit y push de los cambios
2. Esperar a que se complete el deploy
3. Probar el endpoint de debug
4. Intentar guardar la configuración nuevamente
5. Si persiste el error, revisar los logs

---

**Archivo creado**: `backend/fix-web-catalog-routes.sh` (script auxiliar para limpiar cache en VPS)
