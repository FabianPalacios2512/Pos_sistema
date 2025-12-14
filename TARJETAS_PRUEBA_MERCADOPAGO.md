# 💳 Tarjetas de Prueba - Mercado Pago

## ✅ Tarjetas APROBADAS (Para simular pagos exitosos)

### Mastercard
```
Número: 5031 7557 3453 0604
CVV: 123
Vencimiento: 11/25
Nombre: APRO
```

### Visa
```
Número: 4509 9535 6623 3704
CVV: 123
Vencimiento: 11/25
Nombre: APRO
```

### American Express
```
Número: 3711 803032 57522
CVV: 1234
Vencimiento: 11/25
Nombre: APRO
```

---

## ❌ Tarjetas RECHAZADAS (Para simular errores)

### Fondos Insuficientes
```
Número: 5031 7557 3453 0604
CVV: 123
Vencimiento: 11/25
Nombre: FUND
```

### Datos Incorrectos
```
Número: 5031 7557 3453 0604
CVV: 123
Vencimiento: 11/25
Nombre: FORM
```

### Llamar para Autorizar
```
Número: 5031 7557 3453 0604
CVV: 123
Vencimiento: 11/25
Nombre: CALL
```

### Otro Motivo de Rechazo
```
Número: 5031 7557 3453 0604
CVV: 123
Vencimiento: 11/25
Nombre: OTHE
```

---

## 🧪 Cómo Probar

### Flujo Completo de Prueba:

1. **Ir a:** `http://localhost:3000/register`

2. **Registrar Nuevo Tenant:**
   - Nombre empresa: "Tienda de Prueba"
   - Subdominio: `prueba123`
   - Email: `test@example.com`
   - Contraseña: `Test1234`

3. **Click "Elegir Mi Plan"**

4. **Seleccionar Plan de Pago:**
   - Emprendedor ($50,000/mes)
   - Frecuencia: Mensual o Anual
   - Marcar DIAN si quieres

5. **Click "Continuar con Mercado Pago"**

6. **En Checkout de Mercado Pago:**
   - Usar tarjeta `APRO` para éxito
   - Usar tarjeta `OTHE` para rechazo

7. **Verificar Redirección:**
   - Éxito → `/payment/success`
   - Error → `/payment/failure`

---

## 📊 Verificar en Base de Datos

```sql
-- Ver transacciones registradas
SELECT * FROM payment_transactions ORDER BY created_at DESC LIMIT 5;

-- Ver tenants con plan activado
SELECT tenant_id, company_name, plan, subscription_ends_at, status 
FROM tenants 
WHERE plan != 'pending' 
ORDER BY created_at DESC;
```

---

## 🔍 Debug con Logs

```bash
# En terminal del backend:
tail -f backend/storage/logs/laravel.log
```

Buscar:
- `📝 Creando preferencia de pago`
- `✅ Preferencia de pago creada`
- `📨 Webhook recibido de Mercado Pago`
- `✅ Plan activado`

---

## 🎯 Precios de Planes (Configurados)

| Plan | Mensual | Anual | Ahorro |
|------|---------|-------|--------|
| **Trial Express** | GRATIS 3 días | - | - |
| **Emprendedor** | $65,000 | $600,000 | 23% ($180k) |
| **Negocio Pro** | $65,000 | $600,000 | 23% |
| **Corporativo** | $65,000 | $600,000 | 23% |

**Addon DIAN:** +$15,000/mes (solo para planes de pago)

---

## ✅ Todo Listo Para:

- ✅ Registrar nuevos tenants
- ✅ Seleccionar planes de pago
- ✅ Procesar pagos con Mercado Pago (TEST)
- ✅ Activar suscripciones automáticamente
- ✅ Redirigir a páginas de éxito/error
- ✅ Registrar transacciones en BD
- ✅ Recibir webhooks de Mercado Pago

---

## 🚀 Próximo Paso: Ir a Producción

Cuando estés listo para **usar dinero real**:

1. **Cambiar credenciales en `.env`:**
   ```env
   MERCADOPAGO_PUBLIC_KEY=APP_USR-xxxxxxxx-xxxx-xxxx...
   MERCADOPAGO_ACCESS_TOKEN=APP_USR-1234567890123456...
   ```

2. **Activar aplicación en Mercado Pago:**
   - Ir a https://www.mercadopago.com.co/developers
   - Solicitar activación en producción

3. **Configurar webhook de producción:**
   - URL: `https://tudominio.com/api/mercadopago/webhook`

4. **Habilitar HTTPS (obligatorio):**
   ```bash
   sudo certbot --nginx -d tudominio.com
   ```

---

¡Listo para empezar a cobrar! 💰
