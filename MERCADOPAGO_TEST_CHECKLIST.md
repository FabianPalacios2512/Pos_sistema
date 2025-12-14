# ✅ Checklist de Pruebas - Mercado Pago Integration

## 🎯 Problema Resuelto

### Error Anterior
```
❌ SQLSTATE[HY000]: General error: 1364 Field 'frequency' doesn't have a default value
```

### Solución Aplicada
✅ Corregido en `PaymentController.php` línea 120-129
- Agregado campo `frequency` 
- Agregado campo `include_dian`
- Eliminado campo `currency` (no existe en modelo)

---

## 🧪 Cómo Hacer Pruebas Completas

### 1️⃣ **Verificar que el Backend esté corriendo**
```bash
# En terminal, verificar que PHP esté activo
ps aux | grep "php.*artisan serve"

# Si no está corriendo, iniciarlo:
cd backend
php artisan serve
```

### 2️⃣ **Verificar que el Frontend esté corriendo**
```bash
# En otra terminal
npm run dev

# Debe mostrar:
# VITE v5.x.x ready in xxx ms
# ➜ Local: http://localhost:3000/
```

### 3️⃣ **Acceder a la Selección de Planes**
```
URL: http://localhost:3000/plan-selection
```

**Deberías ver:**
- ⚠️ Banner amarillo con tarjetas de prueba
- 4 planes disponibles
- Selector de frecuencia de pago

### 4️⃣ **Seleccionar un Plan de Pago**

**Opciones:**
- Plan Emprendedor: $25,000/mes
- Plan Negocio Pro: $60,000/mes  
- Plan Enterprise: $150,000/mes

**Acciones:**
1. Click en cualquier plan (excepto Trial)
2. Hacer scroll y click en "Continuar con Mercado Pago"

### 5️⃣ **En la Pasarela de Mercado Pago**

**Deberías ver:**
- Logo de Mercado Pago
- Información del plan seleccionado
- Formulario de tarjeta

**⚠️ IMPORTANTE - Usar Tarjetas de Prueba:**

#### ✅ Para Simular PAGO APROBADO:
```
Número de tarjeta:  5031 7557 3453 0604
CVV:                123
Vencimiento:        11/25
Nombre del titular: APRO
Email:              test@test.com
```

#### ❌ Para Simular PAGO RECHAZADO:
```
Número de tarjeta:  5031 7557 3453 0604
CVV:                123
Vencimiento:        11/25
Nombre del titular: OTHE
Email:              test@test.com
```

### 6️⃣ **Verificar el Resultado**

#### Si usaste tarjeta "APRO" (aprobada):
- ✅ Redirige a: `/payment/success`
- ✅ Muestra mensaje de éxito
- ✅ Guarda transacción en BD

#### Si usaste tarjeta "OTHE" (rechazada):
- ❌ Redirige a: `/payment/failure`
- ❌ Muestra mensaje de error
- ❌ NO activa el plan

### 7️⃣ **Verificar en Base de Datos**

```sql
-- Ver transacciones creadas
SELECT * FROM payment_transactions 
ORDER BY created_at DESC 
LIMIT 5;

-- Ver detalles de una transacción específica
SELECT 
  id,
  tenant_id,
  preference_id,
  plan,
  frequency,
  amount,
  status,
  created_at
FROM payment_transactions 
WHERE tenant_id = 'TU_TENANT_ID';
```

---

## 🔍 Verificación de Logs

### Ver logs del backend en tiempo real:
```bash
cd backend
tail -f storage/logs/laravel.log
```

**Mensajes esperados al crear preferencia:**
```
[2025-12-09 15:XX:XX] local.INFO: 📝 Creando preferencia de pago
[2025-12-09 15:XX:XX] local.INFO: 🔗 URLs de redirección
[2025-12-09 15:XX:XX] local.INFO: ✅ Preferencia de pago creada
```

**Si hay error:**
```
[2025-12-09 15:XX:XX] local.ERROR: ❌ Error creando preferencia MP
```

---

## ⚠️ Errores Comunes y Soluciones

### Error: "Una de las partes con la que intentas hacer el pago es de prueba"
**Causa:** Estás usando credenciales de TEST pero tarjeta REAL (o viceversa)
**Solución:** 
- Usar tarjetas de PRUEBA (ver sección 5️⃣)
- Verificar que `.env` tenga credenciales TEST

### Error: "Request failed with status code 500"
**Causa:** Error en backend
**Solución:**
1. Revisar logs: `tail -f backend/storage/logs/laravel.log`
2. Verificar que tabla `payment_transactions` tenga todas las columnas
3. Reiniciar servidor PHP

### Error: "Network Error" o timeout
**Causa:** Backend no está corriendo
**Solución:**
```bash
cd backend
php artisan serve
```

---

## 📊 Estructura de Datos Enviados

### Request a `/api/create-payment-preference`:
```json
{
  "tenant_id": "asjas",
  "plan": "emprendedor",
  "payment_frequency": "monthly",
  "amount": 25000,
  "include_dian": false,
  "company_name": "Mi Empresa"
}
```

### Response exitoso:
```json
{
  "success": true,
  "preference_id": "3052668646-xxxxx-xxxx-xxxx-xxxxxxxxxxxx",
  "init_point": "https://www.mercadopago.com.co/checkout/v1/redirect?pref_id=..."
}
```

---

## 🚀 Pasar a Producción

### Cuando quieras aceptar pagos REALES:

1. **Obtener credenciales de PRODUCCIÓN:**
   - Ir a: https://www.mercadopago.com.co/developers/panel
   - Sección: "Tus integraciones"
   - Copiar credenciales de PRODUCCIÓN

2. **Actualizar `.env`:**
```env
# Cambiar de TEST a PRODUCCIÓN
MERCADOPAGO_PUBLIC_KEY=APP_USR-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
MERCADOPAGO_ACCESS_TOKEN=APP_USR-xxxxxxxxxxxxxxxx-xxxxxx-xxxxxxxx
```

3. **Reiniciar servidor:**
```bash
cd backend
php artisan config:clear
php artisan serve
```

4. **ELIMINAR el banner de prueba:**
   - Editar: `src/views/PlanSelection.vue`
   - Buscar: `<!-- ⚠️ Banner MODO TEST`
   - Eliminar todo el bloque del banner

---

## ✅ Checklist Final

- [ ] Backend corriendo (`php artisan serve`)
- [ ] Frontend corriendo (`npm run dev`)
- [ ] Banner de tarjetas de prueba visible
- [ ] Seleccionar plan → Redirige a Mercado Pago
- [ ] Usar tarjeta APRO → Pago aprobado
- [ ] Usar tarjeta OTHE → Pago rechazado
- [ ] Verificar registro en `payment_transactions`
- [ ] Logs sin errores

---

## 🆘 Soporte

Si algo falla:

1. **Ver logs completos:**
```bash
tail -200 backend/storage/logs/laravel.log | grep -A 5 "Error\|ERROR"
```

2. **Verificar estructura de BD:**
```sql
SHOW COLUMNS FROM payment_transactions;
```

3. **Limpiar caché:**
```bash
cd backend
php artisan config:clear
php artisan cache:clear
```

---

## 📝 Notas Importantes

- ✅ Las credenciales actuales son de **TEST**
- ✅ Solo funcionan con **tarjetas de prueba**
- ✅ NO intentes usar tarjetas reales en modo TEST
- ✅ El nombre del titular **APRO/OTHE** determina el resultado
- ✅ Para producción, necesitas credenciales PROD
