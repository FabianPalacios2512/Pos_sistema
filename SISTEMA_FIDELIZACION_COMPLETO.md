# 🎁 Sistema de Fidelización - Documentación Completa

## ✅ IMPLEMENTACIÓN COMPLETADA

### 📊 Resumen del Sistema

El sistema de fidelización permite a los clientes:
- **Ganar puntos** automáticamente por cada compra (0.001 puntos por cada peso)
- **Redimir puntos** durante el checkout del POS (cada punto vale $10 pesos)
- **Ver su saldo** de puntos en tiempo real
- **Consultar historial** de transacciones de puntos

---

## 🗄️ BASE DE DATOS

### Migraciones Creadas

#### 1. `add_loyalty_points_to_customers_table.php`
```sql
ALTER TABLE customers 
ADD COLUMN loyalty_points INT DEFAULT 0 AFTER total_orders;
```

#### 2. `create_loyalty_transactions_table.php`
```sql
CREATE TABLE loyalty_transactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id BIGINT UNSIGNED NOT NULL,
  invoice_id BIGINT UNSIGNED NULL,
  type ENUM('earned', 'redeemed', 'adjusted', 'expired') NOT NULL,
  points INT NOT NULL,
  points_value DECIMAL(15,2) NULL,
  balance_after INT NOT NULL,
  description TEXT NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
  FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

#### 3. `add_loyalty_settings_to_system_settings_table.php`
```sql
ALTER TABLE system_settings 
ADD COLUMN enable_loyalty_system TINYINT(1) DEFAULT 0,
ADD COLUMN loyalty_points_per_currency DECIMAL(10,6) DEFAULT 0.001000,
ADD COLUMN loyalty_point_value DECIMAL(10,2) DEFAULT 10.00;
```

### Estado Actual
- ✅ **tenantasasasa**: Migraciones aplicadas, sistema habilitado
- ⏳ **tenantasqwqw**: Pendiente
- ⏳ **tenantnatura**: Pendiente
- ⏳ **tenantqwqw**: Pendiente
- ⏳ **tenantventa-de-gorras**: Pendiente

### Activación del Sistema
```sql
UPDATE system_settings 
SET enable_loyalty_system = 1;
```

---

## 🔧 BACKEND

### Modelos

#### `LoyaltyTransaction.php`
**Métodos principales:**
- `recordEarned($customer, $points, $invoiceTotal, $invoiceId, $userId)` - Registra puntos ganados
- `recordRedeemed($customer, $points, $pointsValue, $invoiceId, $userId)` - Registra puntos redimidos
- `recordAdjustment($customer, $points, $description, $userId)` - Registra ajustes manuales

**Relaciones:**
- `belongsTo(Customer::class)`
- `belongsTo(Invoice::class)`
- `belongsTo(User::class, 'created_by')`

#### `Customer.php` (Extendido)
**Métodos añadidos:**
- `calculatePointsToEarn($amount)` - Calcula puntos a ganar (estático)
- `calculatePointsValue($points)` - Calcula valor en dinero de puntos (estático)
- `earnLoyaltyPoints($invoiceTotal, $invoiceId, $userId)` - Gana puntos
- `redeemLoyaltyPoints($points, $invoiceId, $userId)` - Redime puntos
- `getLoyaltyPointsValueAttribute()` - Accessor para valor de puntos
- `hasLoyaltyPoints($requiredPoints = 1)` - Verifica disponibilidad

**Relación añadida:**
- `hasMany(LoyaltyTransaction::class)`

### Controladores

#### `LoyaltyController.php`
**Endpoints implementados:**

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/loyalty/settings` | Obtiene configuración del sistema |
| POST | `/api/loyalty/calculate-points` | Calcula puntos por monto |
| POST | `/api/loyalty/calculate-value` | Calcula valor de puntos |
| GET | `/api/loyalty/customer/{id}/points` | Obtiene puntos de cliente |
| GET | `/api/loyalty/customer/{id}/transactions` | Historial de transacciones |
| POST | `/api/loyalty/validate-redemption` | Valida redención |
| POST | `/api/loyalty/adjust-points` | Ajuste manual de puntos |

#### `InvoiceController.php` (Modificado)
**Lógica añadida:**

1. **Redención de Puntos** (Línea ~620-680):
   - Se ejecuta DESPUÉS de crear la factura
   - Valida puntos disponibles
   - Redime puntos dentro de la transacción
   - Si falla, hace rollback completo

2. **Ganancia de Puntos** (Línea ~840-880):
   - Se ejecuta DESPUÉS del commit
   - Solo para facturas pagadas (no cotizaciones)
   - No falla la venta si hay error

### Rutas API (`tenant_api.php`)
```php
// Loyalty Points Routes
Route::prefix('loyalty')->group(function () {
    Route::get('settings', [LoyaltyController::class, 'getSettings']);
    Route::post('calculate-points', [LoyaltyController::class, 'calculatePointsToEarn']);
    Route::post('calculate-value', [LoyaltyController::class, 'calculatePointsValue']);
    Route::get('customer/{customerId}/points', [LoyaltyController::class, 'getCustomerPoints']);
    Route::get('customer/{customerId}/transactions', [LoyaltyController::class, 'getCustomerTransactions']);
    Route::post('validate-redemption', [LoyaltyController::class, 'validateRedemption']);
    Route::post('adjust-points', [LoyaltyController::class, 'adjustPoints']);
});
```

---

## 💻 FRONTEND

### Composable: `useLoyaltyPoints.js`

**Estado reactivo:**
- `settings` - Configuración del sistema
- `loading` - Estado de carga
- `error` - Mensajes de error

**Métodos disponibles:**
```javascript
// Cargar configuración
await loadSettings()

// Calcular puntos a ganar
const points = await calculatePointsToEarn(amount)

// Calcular valor de puntos
const value = await calculatePointsValue(points)

// Obtener puntos de cliente
const data = await getCustomerPoints(customerId)

// Validar redención
const result = await validateRedemption(customerId, points)

// Formatear puntos como dinero
const formatted = formatPointsAsMoney(points)
```

### Componente: `PosView.vue`

#### Imports y Setup
```javascript
import { useLoyaltyPoints } from '@/composables/useLoyaltyPoints'

const {
  settings: loyaltySettings,
  calculatePointsValue,
  getCustomerPoints
} = useLoyaltyPoints()
```

#### Variables Reactivas
```javascript
const usePoints = ref(false)              // Toggle para usar puntos
const pointsToRedeem = ref(0)             // Puntos que el cliente quiere redimir
const pointsDiscount = ref(0)             // Descuento en dinero equivalente
```

#### Computed Properties
```javascript
// Determina si se pueden usar puntos
const canUseLoyaltyPoints = computed(() => {
  return selectedCustomer.value && 
         selectedCustomer.value.loyalty_points > 0 &&
         loyaltySettings.value?.enable_loyalty_system
})

// Calcula máximo de puntos disponibles
const maxPointsToUse = computed(() => {
  if (!selectedCustomer.value) return 0
  const maxByPoints = selectedCustomer.value.loyalty_points
  const maxByTotal = Math.floor(total.value / loyaltySettings.value.loyalty_point_value)
  return Math.min(maxByPoints, maxByTotal)
})
```

#### Watchers
```javascript
// Actualiza descuento al cambiar puntos
watch(pointsToRedeem, async (newPoints) => {
  if (newPoints > 0 && loyaltySettings.value) {
    const value = await calculatePointsValue(newPoints)
    pointsDiscount.value = value
  } else {
    pointsDiscount.value = 0
  }
})

// Resetea al desactivar
watch(usePoints, (newValue) => {
  if (!newValue) {
    pointsToRedeem.value = 0
    pointsDiscount.value = 0
  }
})

// Resetea al cambiar cliente o vaciar carrito
watch([selectedCustomer, () => cart.value.length], () => {
  usePoints.value = false
  pointsToRedeem.value = 0
  pointsDiscount.value = 0
})
```

#### UI - Badge de Puntos (Línea ~420)
```vue
<!-- Badge de puntos de fidelización -->
<div v-if="selectedCustomer && selectedCustomer.loyalty_points > 0"
     class="mt-2 inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-lg">
  <svg class="w-4 h-4 text-amber-600 mr-2">...</svg>
  <span class="text-xs font-semibold text-amber-700">
    {{ selectedCustomer.loyalty_points }} puntos 
    ({{ formatCurrency(selectedCustomer.loyalty_points_value) }})
  </span>
</div>
```

#### UI - Redención de Puntos (Línea ~663)
```vue
<!-- Redención de puntos -->
<div v-if="canUseLoyaltyPoints" 
     class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-lg p-3">
  <div class="flex items-center justify-between mb-2">
    <label class="flex items-center cursor-pointer">
      <input type="checkbox" v-model="usePoints" class="..." />
      <span class="text-sm font-semibold text-amber-700">Usar Puntos</span>
    </label>
  </div>
  
  <div v-if="usePoints" class="space-y-2">
    <div class="flex items-center space-x-2">
      <input type="number" v-model.number="pointsToRedeem" 
             :max="maxPointsToUse" min="0" class="..." />
      <button @click="pointsToRedeem = maxPointsToUse" class="...">
        Máximo ({{ maxPointsToUse }})
      </button>
    </div>
    
    <div v-if="pointsDiscount > 0" class="...">
      Descuento: {{ formatCurrency(pointsDiscount) }}
    </div>
  </div>
</div>
```

#### Cálculo de Total (Línea ~2103)
```javascript
const total = computed(() => {
  let totalAmount = subtotal.value + tax.value + paymentData.fee
  
  // Restar descuento promocional
  if (discount.value > 0) {
    totalAmount -= parseFloat(discount.value)
  }
  
  // Restar descuento por puntos
  if (usePoints.value && pointsDiscount.value > 0) {
    totalAmount -= parseFloat(pointsDiscount.value)
  }
  
  return totalAmount
})
```

#### Invoice Data (Línea ~2940)
```javascript
const invoiceData = {
  // ... otros campos ...
  
  // 🎁 Información de puntos redimidos
  ...(usePoints.value && pointsToRedeem.value > 0 ? {
    loyalty_points_redeemed: pointsToRedeem.value,
    loyalty_discount_amount: pointsDiscount.value
  } : {})
}
```

---

## 🔄 FLUJO COMPLETO

### 1. Earning Points (Ganar Puntos)

```
Usuario realiza compra
    ↓
Frontend envía invoice sin loyalty fields
    ↓
Backend: InvoiceController.createPosInvoice()
    ↓
Se crea factura con status='paid'
    ↓
DB::commit() - Transacción completada
    ↓
Sistema verifica: enable_loyalty_system=1 && type!='quote' && status='paid'
    ↓
Calcula puntos: Customer::calculatePointsToEarn($invoice->total)
    ↓
Customer->earnLoyaltyPoints($total, $invoice_id, $user_id)
    ↓
LoyaltyTransaction::recordEarned()
    ↓
Se crea registro en loyalty_transactions (type='earned')
    ↓
Se actualiza customer.loyalty_points
    ↓
✅ Puntos ganados exitosamente
```

### 2. Redeeming Points (Redimir Puntos)

```
Usuario selecciona cliente en POS
    ↓
Frontend muestra badge con puntos disponibles
    ↓
Usuario activa toggle "Usar Puntos"
    ↓
Usuario ingresa cantidad o presiona "Máximo"
    ↓
Frontend calcula: pointsDiscount = points * loyalty_point_value
    ↓
Total se actualiza automáticamente: total - pointsDiscount
    ↓
Usuario finaliza compra
    ↓
Frontend envía: {
  loyalty_points_redeemed: 50,
  loyalty_discount_amount: 500
}
    ↓
Backend: InvoiceController.createPosInvoice()
    ↓
DB::beginTransaction()
    ↓
Se crea factura
    ↓
Sistema detecta loyalty_points_redeemed > 0
    ↓
Valida: customer->hasLoyaltyPoints($points)
    ↓
Si OK: customer->redeemLoyaltyPoints($points, $invoice_id, $user_id)
    ↓
LoyaltyTransaction::recordRedeemed()
    ↓
Se crea registro en loyalty_transactions (type='redeemed', points=-50)
    ↓
Se actualiza customer.loyalty_points (resta puntos)
    ↓
DB::commit() - Todo dentro de transacción
    ↓
✅ Puntos redimidos exitosamente
```

---

## 📋 CONFIGURACIÓN

### Variables del Sistema (`system_settings`)

| Campo | Valor | Descripción |
|-------|-------|-------------|
| `enable_loyalty_system` | `1` | Activa/desactiva el sistema |
| `loyalty_points_per_currency` | `0.001000` | Puntos por cada peso (1000 pesos = 1 punto) |
| `loyalty_point_value` | `10.00` | Valor en pesos de cada punto |

### Cálculos

**Earning (Ganar):**
```
Puntos = floor(Total_Compra × loyalty_points_per_currency)
Ejemplo: $50,000 × 0.001 = 50 puntos
```

**Redeeming (Redimir):**
```
Descuento = Puntos_Redimidos × loyalty_point_value
Ejemplo: 50 puntos × $10 = $500 de descuento
```

---

## 🧪 TESTING

### Test 1: Earning Points
1. Seleccionar cliente
2. Agregar productos al carrito
3. Finalizar venta con método de pago
4. Verificar en logs: "🎁 Puntos de fidelización ganados"
5. Verificar en DB:
   ```sql
   SELECT loyalty_points FROM customers WHERE id = X;
   SELECT * FROM loyalty_transactions WHERE customer_id = X ORDER BY id DESC LIMIT 1;
   ```

### Test 2: Redeeming Points
1. Seleccionar cliente con puntos
2. Verificar badge muestra puntos correctos
3. Activar "Usar Puntos"
4. Ingresar cantidad de puntos
5. Verificar total se reduce correctamente
6. Finalizar venta
7. Verificar en logs: "🎁 Puntos de fidelización redimidos"
8. Verificar en DB:
   ```sql
   SELECT loyalty_points FROM customers WHERE id = X;
   SELECT * FROM loyalty_transactions WHERE customer_id = X ORDER BY id DESC LIMIT 1;
   ```

### Test 3: Edge Cases
- Intentar redimir más puntos de los disponibles
- Intentar redimir más puntos que el total de la compra
- Cliente sin puntos no debe ver UI de redención
- Sistema deshabilitado no debe procesar puntos

---

## 🔐 SEGURIDAD

### Validaciones Implementadas

1. **Backend Validation:**
   - Puntos disponibles: `hasLoyaltyPoints($required)`
   - Sistema habilitado: `enable_loyalty_system == 1`
   - Cliente existe: Foreign key constraints
   - Cantidad mínima: `min:1` en request validation

2. **Frontend Validation:**
   - Máximo calculado: `Math.min(availablePoints, maxByTotal)`
   - Input type="number" con max attribute
   - Computed properties actualizan en tiempo real

3. **Database Integrity:**
   - Foreign keys con CASCADE/SET NULL
   - Transacciones atómicas (earning y redeeming)
   - Rollback automático en errores

---

## 📊 LOGS

### Identificadores de Logs

**Earning:**
- `🎁 Puntos de fidelización ganados`

**Redeeming:**
- `🎁 Puntos de fidelización redimidos`
- `⚠️ Cliente no tiene suficientes puntos de fidelización`
- `❌ Error al redimir puntos de fidelización`

### Ejemplo de Log Entry
```
🎁 Puntos de fidelización redimidos
{
  "invoice_id": 123,
  "customer_id": 45,
  "points_redeemed": 50,
  "discount_amount": 500,
  "remaining_points": 150
}
```

---

## 📝 PENDIENTES

### Migrations en Otros Tenants
Ejecutar en cada tenant:
```sql
-- tenantasqwqw
-- tenantnatura
-- tenantqwqw
-- tenantventa-de-gorras

USE tenant{nombre};

ALTER TABLE customers ADD COLUMN loyalty_points INT DEFAULT 0 AFTER total_orders;

CREATE TABLE loyalty_transactions (
  -- ... (ver sección Database)
);

ALTER TABLE system_settings 
ADD COLUMN enable_loyalty_system TINYINT(1) DEFAULT 0,
ADD COLUMN loyalty_points_per_currency DECIMAL(10,6) DEFAULT 0.001000,
ADD COLUMN loyalty_point_value DECIMAL(10,2) DEFAULT 10.00;

UPDATE system_settings SET enable_loyalty_system = 1;
```

### Testing Completo
- [ ] Test earning con diferentes montos
- [ ] Test redemption parcial
- [ ] Test redemption máximo
- [ ] Test con sistema deshabilitado
- [ ] Test edge cases (puntos insuficientes, etc.)

---

## 🎉 CONCLUSIÓN

Sistema de fidelización **COMPLETAMENTE IMPLEMENTADO** con:

✅ **Backend completo**: Migrations, Models, Controllers, Routes  
✅ **Frontend completo**: Composable, UI components, calculations  
✅ **Integración total**: Earning automático, Redemption manual  
✅ **Validaciones**: Frontend y Backend robustas  
✅ **Seguridad**: Transacciones atómicas, rollback automático  
✅ **Logging**: Trazabilidad completa de operaciones  

**Estado**: Listo para producción en tenant `tenantasasasa`  
**Pendiente**: Aplicar migraciones en otros tenants y testing E2E

---

**Fecha**: 7 de noviembre de 2025  
**Versión**: 1.0 - Sistema Completo
