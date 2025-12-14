# 🏢 Implementación Filtro de Sede en Control de Stock

## 📋 Análisis del Problema

### Problema Crítico Actual:
- Papa capira tiene **90 kg en Sede Norte**
- Control de Stock muestra **1 kg total** (suma de todas las sedes)
- Al ajustar stock, **no se especifica la sede** → se aplica a Sede Principal por defecto
- Esto causa **descuadres** entre sedes

### Solución:
Agregar **selector de sede** en Control de Stock que:
- ✅ Solo aparece para planes **Premium/Enterprise**
- ✅ Solo aparece si tenant tiene **múltiples sedes** (> 1 warehouse)
- ✅ Filtra productos por sede seleccionada
- ✅ Ajustes de stock se aplican a la sede correcta

---

## 🔧 Cambios Necesarios

### 1. Backend - Nueva API para Warehouses

**Archivo**: `backend/routes/tenant_api.php`
```php
// Obtener warehouses del tenant (solo para Premium/Enterprise)
Route::get('/warehouses/list', [WarehouseController::class, 'listForTenant']);

// Obtener plan del tenant
Route::get('/tenant/plan-info', [TenantController::class, 'getPlanInfo']);
```

**Archivo**: `backend/app/Http/Controllers/WarehouseController.php`
```php
public function listForTenant(Request $request)
{
    $tenant = tenancy()->tenant;
    
    // Validar que el tenant tiene plan Premium o Enterprise
    if (!in_array($tenant->plan, ['premium', 'enterprise'])) {
        return response()->json([
            'success' => false,
            'message' => 'Esta función solo está disponible para planes Premium y Enterprise',
            'warehouses' => []
        ]);
    }
    
    $warehouses = Warehouse::select('id', 'name', 'is_default')
        ->orderBy('is_default', 'desc')
        ->orderBy('name')
        ->get();
    
    return response()->json([
        'success' => true,
        'warehouses' => $warehouses,
        'count' => $warehouses->count()
    ]);
}
```

### 2. Backend - API para Plan del Tenant

**Nuevo archivo**: `backend/app/Http/Controllers/Api/TenantController.php`
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function getPlanInfo(Request $request)
    {
        $tenant = tenancy()->tenant;
        
        return response()->json([
            'success' => true,
            'plan' => $tenant->plan, // 'trial', 'basic', 'premium', 'enterprise'
            'business_name' => $tenant->business_name,
            'subscription_ends_at' => $tenant->subscription_ends_at,
            'features' => [
                'multi_warehouse' => in_array($tenant->plan, ['premium', 'enterprise'])
            ]
        ]);
    }
}
```

### 3. Backend - Modificar Stock por Warehouse

**Archivo**: `backend/app/Http/Controllers/Api/ProductController.php`

Modificar `updateStock` para aceptar `warehouse_id`:

```php
public function updateStock(Request $request, Product $product)
{
    $request->validate([
        'quantity' => 'required|integer',
        'type' => 'required|in:purchase,sale,adjustment,return,transfer',
        'warehouse_id' => 'nullable|exists:warehouses,id' // NUEVO
    ]);

    $warehouseId = $request->warehouse_id;
    
    // Si no se especifica warehouse, usar el por defecto
    if (!$warehouseId) {
        $defaultWarehouse = \App\Models\Warehouse::where('is_default', true)->first();
        $warehouseId = $defaultWarehouse?->id ?? 1;
    }

    // Actualizar stock en el warehouse específico
    $product->updateStockInWarehouse(
        $warehouseId,
        $request->quantity,
        $request->type,
        $request->reference ?? 'Manual',
        auth()->id() ?? 1
    );

    return response()->json([
        'success' => true,
        'data' => $product->fresh(),
        'message' => 'Stock actualizado exitosamente en la sede seleccionada'
    ]);
}
```

### 4. Backend - Nuevo método en Product Model

**Archivo**: `backend/app/Models/Product.php`

```php
public function updateStockInWarehouse($warehouseId, $quantity, $type = 'adjustment', $reference = null, $userId = null)
{
    // Obtener stock actual en ese warehouse
    $pivot = $this->warehouses()->wherePivot('warehouse_id', $warehouseId)->first();
    $previousStock = $pivot ? $pivot->pivot->stock : 0;

    // Determinar si es entrada o salida
    $movementType = ($quantity >= 0) ? 'in' : 'out';
    
    // Mapear el type a reason
    $reasonMap = [
        'sale' => 'sale',
        'purchase' => 'purchase',
        'adjustment' => ($quantity >= 0) ? 'adjustment_positive' : 'adjustment_negative',
        'return' => 'returned',
        'transfer' => 'transfer'
    ];
    
    $reason = $reasonMap[$type] ?? (($quantity >= 0) ? 'adjustment_positive' : 'adjustment_negative');

    // Calcular nuevo stock
    $newStock = $previousStock + $quantity;

    // Actualizar en product_warehouse
    $this->warehouses()->syncWithoutDetaching([
        $warehouseId => ['stock' => $newStock]
    ]);

    // Actualizar current_stock del producto (suma de todos los warehouses)
    $this->current_stock = $this->warehouses()->sum('product_warehouse.stock');
    $this->save();

    // Registrar movimiento de inventario
    InventoryMovement::create([
        'product_id' => $this->id,
        'warehouse_id' => $warehouseId,
        'type' => $movementType,
        'reason' => $reason,
        'quantity' => abs($quantity),
        'previous_stock' => $previousStock,
        'new_stock' => $newStock,
        'reference' => $reference,
        'user_id' => $userId ?? auth()->id(),
        'movement_date' => now()
    ]);

    return $this;
}
```

### 5. Frontend - Nuevo Servicio para Warehouses

**Archivo**: `src/services/warehouseService.js`

```javascript
import { api } from './api'

export const warehouseService = {
  // Obtener lista de warehouses del tenant
  async getWarehousesList() {
    try {
      const response = await api.get('/warehouses/list')
      return response.data
    } catch (error) {
      console.error('Error obteniendo warehouses:', error)
      throw error
    }
  },

  // Obtener info del plan del tenant
  async getTenantPlanInfo() {
    try {
      const response = await api.get('/tenant/plan-info')
      return response.data
    } catch (error) {
      console.error('Error obteniendo info del plan:', error)
      throw error
    }
  }
}
```

### 6. Frontend - Modificar InventoryView

**Archivo**: `src/components/InventoryView_professional.vue`

Agregar al `<script setup>`:

```javascript
import { warehouseService } from '../services/warehouseService'

// Nuevas variables reactivas
const warehouses = ref([])
const selectedWarehouse = ref(null)
const showWarehouseFilter = ref(false)
const tenantPlan = ref(null)

// Cargar warehouses y plan al montar
onMounted(async () => {
  await loadTenantInfo()
  await loadInitialData()
})

async function loadTenantInfo() {
  try {
    // Obtener plan del tenant
    const planInfo = await warehouseService.getTenantPlanInfo()
    tenantPlan.value = planInfo.plan
    
    // Solo cargar warehouses si es Premium/Enterprise
    if (planInfo.features.multi_warehouse) {
      const warehousesData = await warehouseService.getWarehousesList()
      warehouses.value = warehousesData.warehouses || []
      
      // Mostrar filtro solo si hay más de 1 sede
      showWarehouseFilter.value = warehouses.value.length > 1
      
      // Seleccionar warehouse por defecto
      const defaultWh = warehouses.value.find(w => w.is_default)
      selectedWarehouse.value = defaultWh?.id || warehouses.value[0]?.id
    }
  } catch (error) {
    console.error('Error cargando info del tenant:', error)
  }
}

// Modificar inventoryService.adjustStock para incluir warehouse_id
async function processStockAdjustment() {
  try {
    isProcessingAdjustment.value = true

    await inventoryService.adjustStock(
      stockAdjustmentData.product_id,
      stockAdjustmentData.adjusted_quantity,
      'adjustment',
      stockAdjustmentData.reason || 'Ajuste manual',
      selectedWarehouse.value // NUEVO: pasar warehouse_id
    )

    showStockAdjustmentModal.value = false
    await refreshInventoryData()
    // ... resto del código
  } catch (error) {
    // ... manejo de error
  }
}
```

Agregar en el template, después del header y antes de las métricas:

```vue
<!-- Filtro de Sede (Solo Premium/Enterprise con múltiples sedes) -->
<div v-if="showWarehouseFilter" class="mb-6">
  <div class="bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/50 rounded-xl p-4">
    <div class="flex items-center gap-4">
      <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
      </div>
      <div class="flex-1">
        <label class="text-sm font-bold text-blue-900 dark:text-blue-300 mb-2 block">
          Sede / Bodega Activa
        </label>
        <select 
          v-model="selectedWarehouse"
          @change="refreshInventoryData"
          class="w-full md:w-auto px-4 py-2.5 bg-white dark:bg-zinc-900 border border-blue-300 dark:border-blue-800 rounded-lg text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
            {{ warehouse.name }}
            <span v-if="warehouse.is_default"> (Principal)</span>
          </option>
        </select>
      </div>
      <div class="text-xs text-blue-700 dark:text-blue-400">
        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Los ajustes se aplicarán a esta sede
      </div>
    </div>
  </div>
</div>
```

### 7. Frontend - Modificar inventoryService

**Archivo**: `src/services/inventoryService.js`

```javascript
async adjustStock(productId, quantity, type, reference, warehouseId = null) {
  try {
    const response = await api.post(`/products/${productId}/update-stock`, {
      quantity,
      type,
      reference,
      warehouse_id: warehouseId // NUEVO: incluir warehouse_id
    })
    return response.data
  } catch (error) {
    console.error('Error adjusting stock:', error)
    throw error
  }
}
```

---

## ✅ Resultado Final

### Para usuarios **Trial / Basic**:
- ❌ No ven filtro de sede
- ✅ Stock se gestiona en Sede Principal (comportamiento actual)

### Para usuarios **Premium / Enterprise** con 1 sede:
- ❌ No ven filtro de sede (innecesario)
- ✅ Stock se gestiona en su única sede

### Para usuarios **Premium / Enterprise** con múltiples sedes:
- ✅ Ven filtro de sede al inicio de la vista
- ✅ Seleccionan sede antes de hacer ajustes
- ✅ Los ajustes se aplican a la sede correcta
- ✅ Métricas y tabla filtradas por sede seleccionada

---

## 🚀 Orden de Implementación

1. Backend: Agregar rutas API
2. Backend: Crear TenantController con getPlanInfo
3. Backend: Modificar WarehouseController con listForTenant
4. Backend: Agregar updateStockInWarehouse en Product model
5. Backend: Modificar ProductController.updateStock para aceptar warehouse_id
6. Frontend: Crear warehouseService.js
7. Frontend: Modificar InventoryView para agregar filtro
8. Frontend: Modificar inventoryService para enviar warehouse_id

