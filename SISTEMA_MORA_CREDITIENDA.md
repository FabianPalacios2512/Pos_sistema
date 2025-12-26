# 🎯 Sistema de Mora Profesional - CreditiTenda

## ✅ Implementado con Éxito

### 📊 Nuevo Sistema de Estados Basado en Tiempo Real

Antes el sistema clasificaba clientes solo por **porcentaje de deuda** (70%, 90%), lo cual NO reflejaba el tiempo de mora. Ahora implementamos un sistema profesional que evalúa **cuánto tiempo lleva el cliente con deuda pendiente**.

---

## 🟢 Estados del Sistema (En orden de prioridad)

### 1. **Al Día** (Verde)
- **Condición**: Sin deuda (`current_debt = 0`)
- **Color**: `bg-emerald-50 dark:bg-emerald-950`
- **Ejemplo**: Cliente que pagó todo o no ha comprado a crédito

### 2. **Crítico** (Negro/Gris oscuro) ⚫
- **Condición**: Deuda excede el límite (`current_debt > credit_limit`)
- **Color**: `bg-gray-900 dark:bg-gray-950 text-white`
- **Ejemplo**: Cliente con $100k límite pero debe $107k (por recargos)
- **Prioridad**: SIEMPRE primero, sin importar tiempo

### 3. **Al Día (con deuda reciente)** (Azul) 🔵
- **Condición**: Tiene deuda pero menos de 30 días
- **Color**: `bg-blue-50 dark:bg-blue-950`
- **Ejemplo**: Cliente compró hace 5 días, debe $50k de $100k
- **Lógica**: Es normal, el cliente acaba de comprar

### 4. **Por Vencer** (Amarillo) 🟡
- **Condición**: 30-59 días con deuda
- **Color**: `bg-amber-50 dark:bg-amber-950`
- **Acción**: Comenzar seguimiento preventivo

### 5. **Vencido** (Naranja) 🟠
- **Condición**: 60-89 días con deuda
- **Color**: `bg-orange-50 dark:bg-orange-950`
- **Acción**: Recordatorios más frecuentes

### 6. **Mora** (Rojo) 🔴
- **Condición**: 90+ días con deuda
- **Color**: `bg-rose-50 dark:bg-rose-950`
- **Acción**: Proceso de cobranza formal

---

## 🔧 Implementación Técnica

### Base de Datos
```sql
ALTER TABLE customers ADD COLUMN debt_since TIMESTAMP NULL;
```

**Campo**: `debt_since`
- Se registra cuando `current_debt` pasa de $0 a > $0
- Se limpia cuando `current_debt` llega a $0 (pago completo)

### Backend (Laravel)

#### 1. InvoiceController.php - Al crear venta a crédito
```php
if ($previousDebt == 0 && $customer->current_debt > 0) {
    $customer->debt_since = now();
}
```

#### 2. CreditPaymentController.php - Al registrar pago
```php
if ($customer->current_debt <= 0) {
    $customer->current_debt = 0;
    $customer->debt_since = null; // Limpia fecha
}
```

### Frontend (Vue 3)

#### Función getStatusColor()
```javascript
const getStatusColor = (customer) => {
  const debt = customer.current_debt || 0
  const limit = customer.credit_limit || 0
  
  if (debt === 0) return 'emerald' // Sin deuda
  if (debt > limit) return 'gray-900' // Crítico (excede límite)
  
  const daysDiff = getDaysSince(customer.debt_since)
  
  if (daysDiff >= 90) return 'rose' // Mora
  if (daysDiff >= 60) return 'orange' // Vencido
  if (daysDiff >= 30) return 'amber' // Por Vencer
  
  return 'blue' // Al día (reciente)
}
```

---

## 📈 Ejemplo Real

### Escenario: Cliente compra hoy $93,500 con $100,000 de límite

#### **Inmediatamente después de la compra:**
- Deuda: $100,000 (93,500 + 7% recargo = $6,500)
- Estado: **"Al Día"** 🔵 (azul)
- Días: 0
- Lógica: Acaba de comprar, es normal

#### **Día 35:**
- Deuda: $100,000 (sin cambios)
- Estado: **"Por Vencer"** 🟡 (amarillo)
- Días: 35
- Acción: Enviar recordatorio amigable

#### **Día 70:**
- Deuda: $100,000
- Estado: **"Vencido"** 🟠 (naranja)
- Días: 70
- Acción: Recordatorios más firmes

#### **Día 95:**
- Deuda: $100,000
- Estado: **"Mora"** 🔴 (rojo)
- Días: 95
- Acción: Proceso de cobranza formal

#### **Cliente paga $50,000 (quedan $50,000):**
- Deuda: $50,000
- Estado: **Sigue en "Mora"** 🔴
- Días: 95 (NO se resetea)
- Lógica: Sigue siendo mora hasta pagar TODO

#### **Cliente paga los $50,000 restantes:**
- Deuda: $0
- Estado: **"Al Día"** 🟢 (verde)
- `debt_since`: `null` (se limpia)
- Puede volver a comprar limpiamente

---

## 🎨 Visualización en Tabla

```
┌─────────────────┬─────────┬─────────┬────────────┬────────────┐
│ Cliente         │ Cupo    │ Deuda   │ Disponible │ Estado     │
├─────────────────┼─────────┼─────────┼────────────┼────────────┤
│ Juan Pérez      │ $100k   │ $0      │ $100k      │ Al Día 🟢  │
│ María López     │ $100k   │ $93k    │ $7k        │ Al Día 🔵  │ (2 días)
│ Carlos Gómez    │ $100k   │ $80k    │ $20k       │ Por Vencer │ (35 días)
│ Ana Ruiz        │ $100k   │ $95k    │ $5k        │ Vencido 🟠 │ (65 días)
│ Luis Torres     │ $100k   │ $100k   │ $0         │ Mora 🔴    │ (100 días)
│ Sofia Castro    │ $100k   │ $107k   │ $0         │ Crítico ⚫ │ (5 días)
└─────────────────┴─────────┴─────────┴────────────┴────────────┘
```

---

## ✅ Ventajas del Nuevo Sistema

### 1. **Realista**
- Cliente que compró ayer NO aparece como "Crítico"
- Refleja el ciclo natural de crédito comercial

### 2. **Accionable**
- Estados claros: saber CUÁNDO enviar recordatorios
- Priorizar cobranza por tiempo, no solo monto

### 3. **Profesional**
- Similar a bancos y sistemas financieros
- Compatible con contabilidad estándar

### 4. **Automático**
- No requiere cálculos manuales
- Se actualiza con cada venta/pago

---

## 🚀 Archivos Modificados

1. **backend/database/migrations/tenant/2025_12_26_add_debt_since_to_customers.php** - Nueva columna
2. **backend/app/Models/Customer.php** - Agregado `debt_since` a fillable y casts
3. **backend/app/Http/Controllers/InvoiceController.php** - Registra fecha inicial de deuda
4. **backend/app/Http/Controllers/Api/CreditPaymentController.php** - Limpia fecha al liquidar
5. **backend/app/Http/Controllers/Api/CustomerController.php** - Incluye campo en respuestas API
6. **src/components/CreditiTendaView.vue** - Nuevo sistema de estados con mora

---

## 📝 Notas de Migración

### Clientes Existentes con Deuda
Los clientes que ya tienen deuda (`current_debt > 0`) pero no tienen `debt_since` se mostrarán como **"Activo"** (azul) hasta que:
1. Paguen completamente y vuelvan a comprar (se registra nueva fecha)
2. O se les asigne manualmente una fecha retroactiva si se desea

### Compatibilidad
El sistema es **backward compatible** - funciona con clientes nuevos y existentes sin romper datos actuales.

---

## 🎯 Resultado Final

**Antes**: "Este cliente tiene 90% de deuda = Crítico" ❌
**Ahora**: "Este cliente compró ayer y usó su cupo = Al Día" ✅

El sistema ahora refleja la **realidad comercial** del negocio. 🎉
