# 🚨 SOLUCIÓN DE ERRORES CRÍTICOS - 105POS

## Problemas Detectados

### 1. ❌ Pago no asigna plan inmediatamente
**Síntoma**: Link de pago `https://105pos.pro/payment/success?tenant_id=fabiana&plan=premium&reference=plan_fabiana_1766942956271` no activó el plan en el primer intento, pero sí en el segundo.

**Causa Raíz**:
- Race condition: Múltiples requests procesando el mismo pago simultáneamente
- Posible falta de registro `pending_payment` al momento de procesar
- Sin validación idempotente

### 2. ❌ Error 500 al hacer login después de crear cuenta
**Síntoma**: "Tenant could not be identified on domain maria-jose.105pos.pro"

**Causa Raíz**:
- El tenant se creó pero NO se creó el registro en la tabla `domains`
- O el domain se creó pero el tenant asociado no existe
- O `CENTRAL_DOMAIN` no está configurado en `.env`

---

## 🛠️ SOLUCIONES IMPLEMENTADAS

### A. Scripts de Diagnóstico y Reparación

Se crearon 3 herramientas:

1. **`backend/diagnose_tenant_errors.php`** - Script de diagnóstico
   - Analiza problemas de pagos duplicados
   - Verifica tenants sin dominio
   - Detecta configuración incorrecta

2. **`backend/app/Console/Commands/FixMissingDomains.php`** - Comando Artisan
   - Crea dominios faltantes automáticamente
   - Modo dry-run para preview

3. **`backend/fix_tenant_errors_vps.sh`** - Script bash automatizado
   - Ejecuta todas las reparaciones en orden
   - Interactivo y con confirmaciones

### B. Mejoras en el Código

1. **Validación Idempotente en Pagos** (`TenantPlanController`)
   - Detecta si el pago ya fue procesado
   - Evita activar el mismo plan múltiples veces
   - Usa atomic updates para prevenir race conditions

2. **Índice Único en `pending_payments`**
   - Ya existe `unique` en `reference` (migración existente)
   - Previene duplicados a nivel de base de datos

3. **Comando para Reparar Dominios**
   - `php artisan tenants:fix-missing-domains`
   - Detecta y crea dominios faltantes automáticamente

---

## 📋 INSTRUCCIONES PARA EL VPS (72.61.73.245)

### PASO 1: Conectar al VPS

```bash
ssh root@72.61.73.245
# O el usuario correspondiente
```

### PASO 2: Subir los archivos nuevos

Desde tu máquina local, sube los archivos:

```bash
# Subir script de diagnóstico
scp backend/diagnose_tenant_errors.php root@72.61.73.245:/var/www/105pos.pro/backend/

# Subir comando Artisan
scp backend/app/Console/Commands/FixMissingDomains.php root@72.61.73.245:/var/www/105pos.pro/backend/app/Console/Commands/

# Subir script de reparación
scp backend/fix_tenant_errors_vps.sh root@72.61.73.245:/var/www/105pos.pro/backend/

# Subir actualización del controlador (si no está en Git)
scp backend/app/Http/Controllers/Api/TenantPlanController.php root@72.61.73.245:/var/www/105pos.pro/backend/app/Http/Controllers/Api/
```

**O mejor aún**: Haz `git pull` si los cambios están en el repositorio.

### PASO 3: Ejecutar el diagnóstico

```bash
cd /var/www/105pos.pro/backend
php diagnose_tenant_errors.php
```

**Lee cuidadosamente el output**. Te mostrará:
- Estado del pago de "fabiana"
- Si hay duplicados en `pending_payments`
- Estado del tenant "maria-jose"
- Si falta el registro en la tabla `domains`

### PASO 4: Ejecutar la reparación automatizada

```bash
cd /var/www/105pos.pro/backend
chmod +x fix_tenant_errors_vps.sh
./fix_tenant_errors_vps.sh
```

El script te guiará paso a paso. Hará:
1. ✅ Verificar `.env` y agregar `CENTRAL_DOMAIN=105pos.pro` si falta
2. ✅ Reparar dominios faltantes
3. ✅ Limpiar duplicados en `pending_payments`
4. ✅ Verificar caso específico de maria-jose
5. ✅ Limpiar caché de Laravel

### PASO 5: Verificación Manual (Opcional)

#### A. Verificar configuración

```bash
cd /var/www/105pos.pro/backend
grep CENTRAL_DOMAIN .env
# Debe mostrar: CENTRAL_DOMAIN=105pos.pro
```

#### B. Ver todos los tenants y dominios

```bash
php artisan tenants:list
```

#### C. Verificar caso maria-jose en MySQL

```bash
mysql -u root -p
```

```sql
USE tu_base_de_datos_central;

-- Ver el tenant
SELECT * FROM tenants WHERE id LIKE '%maria%' OR business_name LIKE '%maria%';

-- Ver el dominio
SELECT * FROM domains WHERE domain LIKE '%maria%';

-- Si el tenant existe pero el domain no:
INSERT INTO domains (tenant_id, domain, created_at, updated_at)
VALUES ('maria_jose', 'maria-jose.105pos.pro', NOW(), NOW());
-- Cambia 'maria_jose' por el ID real del tenant
```

#### D. Verificar pagos duplicados

```sql
-- Ver si hay duplicados
SELECT reference, COUNT(*) as count
FROM pending_payments
GROUP BY reference
HAVING count > 1;

-- Eliminar duplicados (deja solo el más reciente)
DELETE t1 FROM pending_payments t1
INNER JOIN pending_payments t2 
WHERE t1.reference = t2.reference 
AND t1.id < t2.id;
```

### PASO 6: Probar que funciona

#### A. Probar login en maria-jose

1. Abre: `https://maria-jose.105pos.pro/login`
2. Intenta iniciar sesión
3. **Esperado**: No debe dar error 500, debe mostrar el formulario de login

#### B. Probar procesamiento de pago

1. Copia el link de pago problemático:
   ```
   https://105pos.pro/payment/success?tenant_id=fabiana&plan=premium&reference=plan_fabiana_1766942956271&subdomain=fabiana&ref_payco=b2de659f946f91b0ed1e8ab9
   ```

2. Ábrelo en el navegador
3. **Esperado**: 
   - Debe mostrar "¡Pago Exitoso!"
   - Si ya fue procesado, debe decir "Plan ya está activo" (idempotente)
   - No debe dar errores

---

## 🔍 LOGS PARA DEBUG

Si algo falla, revisa los logs:

```bash
# Laravel logs
tail -f /var/www/105pos.pro/backend/storage/logs/laravel.log

# Nginx error logs
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log  # Ajusta la versión de PHP
```

**Busca por**:
- "PlanUpgradeController"
- "TenantPlanController"
- "TenantRegisterController"
- "Tenant could not be identified"
- "pending_payment"

---

## ⚠️ PREVENCIÓN FUTURA

### Para evitar estos problemas en el futuro:

1. **Monitorear creación de tenants**:
   ```bash
   # Verificar que cada tenant tenga su domain
   php artisan tinker --execute="
   \$tenants = \\App\\Models\\Tenant::all();
   foreach (\$tenants as \$t) {
       \$domainCount = \$t->domains()->count();
       if (\$domainCount === 0) {
           echo \"⚠️ Tenant sin dominio: {\$t->id}\\n\";
       }
   }
   "
   ```

2. **Monitorear pagos**:
   ```bash
   # Ver pagos pendientes por más de 1 hora
   php artisan tinker --execute="
   \$old = \\App\\Models\\PendingPayment::where('status', 'pending')
       ->where('created_at', '<', now()->subHour())
       ->get();
   echo \"Pagos pendientes antiguos: {\$old->count()}\\n\";
   "
   ```

3. **Validar al registrar**:
   - El código ya tiene validaciones
   - Pero puedes agregar alertas por email si falla

---

## 📞 SOPORTE

Si después de ejecutar estos pasos siguen los problemas:

1. **Guarda los logs**:
   ```bash
   cp /var/www/105pos.pro/backend/storage/logs/laravel.log ~/laravel-$(date +%F).log
   ```

2. **Exporta info de la BD**:
   ```sql
   SELECT * FROM tenants WHERE id IN ('fabiana', 'maria_jose', 'maria-jose');
   SELECT * FROM domains WHERE domain LIKE '%maria%' OR domain LIKE '%fabiana%';
   SELECT * FROM pending_payments WHERE reference LIKE '%fabiana%';
   ```

3. **Comparte**:
   - El output del `diagnose_tenant_errors.php`
   - Los logs relevantes
   - Los resultados de las queries SQL

---

## ✅ CHECKLIST DE REPARACIÓN

- [ ] Conectado al VPS 72.61.73.245
- [ ] Archivos subidos o `git pull` ejecutado
- [ ] Ejecutado `php diagnose_tenant_errors.php`
- [ ] Ejecutado `./fix_tenant_errors_vps.sh`
- [ ] Verificado que `CENTRAL_DOMAIN=105pos.pro` está en `.env`
- [ ] Comando `php artisan tenants:fix-missing-domains` ejecutado
- [ ] Duplicados en `pending_payments` eliminados
- [ ] Caché limpiado (`php artisan config:cache`)
- [ ] Login en maria-jose.105pos.pro funciona
- [ ] Link de pago de fabiana funciona (idempotente)
- [ ] No hay errores 500 en los logs

---

**Fecha**: 28 de diciembre de 2025
**Versión**: 1.0
