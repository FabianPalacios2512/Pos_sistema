# 🔧 ERROR 500 al Crear Clientes - Solución Permanente

## 🔴 EL PROBLEMA

**Error:** HTTP 500 al crear un cliente nuevo

**Error en logs:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'credit_photo' in 'INSERT INTO'
```

**Causa Raíz:**
- En un sistema SaaS multitenancy, cada tenant tiene su propia base de datos
- Las migraciones de Laravel NO se ejecutan automáticamente en las bases de datos de tenants existentes
- Cuando agregas una nueva columna al modelo (ej: `credit_photo` a `Customer`), solo afecta a:
  - ✅ Tenants nuevos creados después de la migración
  - ❌ Tenants existentes **NO reciben la actualización**
  
**Por qué esto es crítico en SaaS:**
- Cada vez que se registra un cliente nuevo → nueva base de datos tenant
- Si no validamos el esquema → error 500 inmediatamente
- Los clientes ven errores en producción

---

## ✅ SOLUCIÓN PERMANENTE

### 1️⃣ Validar Esquemas Automáticamente

**Comando Artisan Creado:**
```bash
php artisan tenants:validate-schemas        # Solo verificar
php artisan tenants:validate-schemas --fix  # Verificar Y corregir
```

**¿Qué hace?**
- ✅ Detecta todos los tenants activos
- ✅ Verifica que cada tabla tenga las columnas correctas
- ✅ Aplica correcciones automáticamente (con `--fix`)
- ✅ Genera reporte detallado en consola

**Output ejemplo:**
```
═══════════════════════════════════════════════
   VALIDACIÓN DE ESQUEMAS TENANTS
═══════════════════════════════════════════════

📊 Tenants encontrados: 1

┌─ Validando: tenantmatimaa (Negocio: matimaa)
│  ✅ Esquema correcto
└─

═══════════════════════════════════════════════
                  RESUMEN FINAL
═══════════════════════════════════════════════

Total tenants:       1
✅ Sin problemas:   1
🔧 Corregidos:      0
❌ Con errores:     0

🎉 ¡Todos los esquemas están sincronizados!
```

---

### 2️⃣ Agregar Nuevas Columnas al Validador

**Archivo:** `/backend/app/Console/Commands/ValidateAndFixTenantSchemas.php`

**Editar la propiedad `$expectedSchema`:**

```php
protected $expectedSchema = [
    'credit_photo' => [
        'type' => 'text',
        'nullable' => true,
        'after' => 'credit_active',
        'migration' => 'ALTER TABLE customers ADD COLUMN credit_photo TEXT NULL AFTER credit_active;'
    ],
    
    // 🆕 AGREGAR AQUÍ NUEVAS COLUMNAS CUANDO SE DETECTEN PROBLEMAS
    // Ejemplo: si falta una columna 'whatsapp_number' en customers:
    // 'whatsapp_number' => [
    //     'type' => 'varchar(20)',
    //     'nullable' => true,
    //     'after' => 'phone',
    //     'migration' => 'ALTER TABLE customers ADD COLUMN whatsapp_number VARCHAR(20) NULL AFTER phone;'
    // ],
];
```

---

### 3️⃣ CUÁNDO EJECUTAR EL COMANDO

#### Escenario A: Después de Agregar una Nueva Columna

1. Creas una migración nueva:
   ```bash
   php artisan make:migration add_whatsapp_to_customers
   ```

2. Ejecutas la migración (solo afecta a NUEVOS tenants):
   ```bash
   php artisan migrate
   ```

3. **⚠️ CRÍTICO:** Ejecutas el comando de validación para tenants existentes:
   ```bash
   php artisan tenants:validate-schemas --fix
   ```

#### Escenario B: Cliente Nuevo Reporta Error 500

1. Identificas el error en logs:
   ```bash
   tail -50 storage/logs/laravel.log
   ```

2. Ejecutas validación en modo detección:
   ```bash
   php artisan tenants:validate-schemas
   ```

3. Si encuentra problemas, aplicas correcciones:
   ```bash
   php artisan tenants:validate-schemas --fix
   ```

#### Escenario C: Despliegue a Producción

**SIEMPRE antes de hacer deploy:**

```bash
# 1. Ejecutar migraciones
php artisan migrate

# 2. Validar y corregir todos los tenants
php artisan tenants:validate-schemas --fix

# 3. Hacer deploy
./deploy-manual.sh
```

---

### 4️⃣ Agregar al Deploy Manual

**Editar:** `/deploy-manual.sh`

Agregar ANTES del build de frontend:

```bash
echo "🔍 Validando esquemas de tenants..."
cd backend
php artisan tenants:validate-schemas --fix
if [ $? -ne 0 ]; then
    echo "❌ Error al validar esquemas de tenants"
    exit 1
fi
cd ..
```

---

## 📝 CHECKLIST DE PREVENCIÓN

Cuando agregues una nueva columna a un modelo:

- [ ] 1. Crear migración con `php artisan make:migration`
- [ ] 2. Ejecutar migración con `php artisan migrate`
- [ ] 3. Agregar columna al array `$expectedSchema` del validador
- [ ] 4. Ejecutar `php artisan tenants:validate-schemas --fix`
- [ ] 5. Verificar que todos los tenants estén `✅ Sin problemas`
- [ ] 6. Commit y push

---

## 🎯 REGLA DE ORO

> **NUNCA hacer deploy de una migración sin validar los esquemas de tenants existentes.**

Esto es especialmente importante porque:
- ✅ Los tenants nuevos recibirán la migración automáticamente
- ❌ Los tenants existentes NO la recibirán
- 💥 Resultado: error 500 para clientes existentes

---

## 🛠️ SCRIPTS DISPONIBLES

### 1. Comando Artisan (RECOMENDADO)
```bash
php artisan tenants:validate-schemas --fix
```
**Ubicación:** `/backend/app/Console/Commands/ValidateAndFixTenantSchemas.php`

### 2. Script PHP Standalone (Alternativo)
```bash
cd backend
php validate_and_fix_tenant_schemas.php
```
**Ubicación:** `/backend/validate_and_fix_tenant_schemas.php`

---

## 📊 LOGS Y DEBUGGING

**Ver logs de Laravel:**
```bash
tail -100 backend/storage/logs/laravel.log
```

**Ver estructura de tabla:**
```bash
mysql -u root tenantmatimaa -e "DESCRIBE customers;"
```

**Ver todos los tenants:**
```bash
mysql -u root pos_sistema -e "SELECT id, business_name FROM tenants;"
```

**Agregar columna manualmente (emergencia):**
```bash
mysql -u root tenantmatimaa -e "ALTER TABLE customers ADD COLUMN credit_photo TEXT NULL AFTER credit_active;"
```

---

## 🔄 APLICADO EN:

- [x] `tenantmatimaa` - Columna `credit_photo` agregada (15/01/2026)
- [x] Comando Artisan creado: `tenants:validate-schemas`
- [x] Script PHP standalone creado
- [ ] Integrar en `deploy-manual.sh` (pendiente)
- [ ] Configurar CI/CD para ejecutar automáticamente (pendiente)

---

## 💡 MEJORAS FUTURAS

1. **Webhook post-registro:** Ejecutar validación automáticamente cuando se crea un tenant nuevo
2. **Dashboard de salud:** Página admin que muestre estado de esquemas en tiempo real
3. **Alertas automáticas:** Notificar por email/Slack si se detectan esquemas desactualizados
4. **Rollback seguro:** Crear respaldos antes de aplicar correcciones

---

## ⚠️ NOTAS IMPORTANTES

- El script NO ejecuta `DROP COLUMN` - solo agrega columnas faltantes
- Si una columna ya existe, la omite silenciosamente
- Las correcciones son inmediatas, no reversibles (por eso hace backup recomendado)
- Funciona con la estructura actual de tenancy (prefijo `tenant` + id)

---

## 🎓 LECCIONES APRENDIDAS

1. **Multitenancy ≠ Migraciones Automáticas**
   - Laravel no propaga migraciones a tenants existentes por defecto
   - Debes hacerlo manualmente

2. **Validación Proactiva > Reaccionar**
   - Mejor detectar antes que esperar errores de clientes
   - Un comando automatizado ahorra horas de soporte

3. **Documenta los Cambios de Esquema**
   - Mantén `$expectedSchema` sincronizado con tus modelos
   - Comenta por qué se agregó cada columna

4. **Testing en Producción es Riesgoso**
   - Siempre valida en local primero
   - Usa `--fix` solo cuando estés seguro
