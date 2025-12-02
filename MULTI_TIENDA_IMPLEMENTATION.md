# 🏪 Sistema Multi-Tienda - Plan de Implementación

## 📋 Objetivo del Sistema

Permitir que un negocio opere múltiples tiendas/sedes donde:
- Cada tienda tiene su propio inventario de productos
- Al abrir caja, el cajero especifica en qué tienda trabaja
- El POS muestra principalmente productos de la tienda actual
- Se permite vender productos de otras tiendas (venta cruzada) con descuento de stock correcto

---

## ✅ CAMBIOS YA IMPLEMENTADOS

### 1. Modal de Apertura de Caja (`CashOpenModal.vue`)
- ✅ Agregado selector de tienda/bodega OBLIGATORIO
- ✅ Carga automática de bodegas activas al abrir el modal
- ✅ Auto-selección de bodega predeterminada
- ✅ Validación de warehouse_id antes de abrir sesión
- ✅ warehouse_id incluido en el objeto sessionData emitido

### 2. Backend - CashSessionController
- ✅ Validación de `warehouse_id` requerido: `'warehouse_id' => 'required|exists:warehouses,id'`
- ✅ warehouse_id pasado al modelo al crear sesión
- ✅ Relación `warehouse` cargada en respuestas de sesiones

### 3. Backend - CashSession Model
- ✅ Método `openSession()` actualizado para recibir `$warehouseId`
- ✅ warehouse_id guardado en la tabla `cash_sessions`
- ✅ Relación `warehouse()` ya existente en el modelo

### 4. Base de Datos
- ✅ Tabla `cash_sessions` ya tiene columna `warehouse_id`
- ✅ Tabla `product_warehouse` con pivot stock por bodega
- ✅ Tabla `warehouses` con bodegas/sedes activas

---

## 🔄 CAMBIOS PENDIENTES (Próximos Pasos)

### 5. Backend - ProductController

**Archivo:** `backend/app/Http/Controllers/Api/ProductController.php`

#### Modificar el método `getForPos()`:

```php
/**
 * Obtener productos optimizados para POS con filtro de bodega
 */
public function getForPos(Request $request)
{
    $warehouseId = $request->query('warehouse_id');
    
    $query = Product::select([
        'id', 'code', 'name', 'description', 'price', 'current_stock',
        'category_id', 'image', 'active', 'stock_min', 'stock_max'
    ])
    ->with([
        'category:id,name,color',
        'warehouses' => function($query) {
            $query->select('warehouses.id', 'warehouses.name', 'warehouse_id', 'stock');
        }
    ])
    ->where('active', true);
    
    // Si se especifica bodega, filtrar productos con stock en ESA bodega
    if ($warehouseId) {
        $query->whereHas('warehouses', function($q) use ($warehouseId) {
            $q->where('warehouse_id', $warehouseId)
              ->where('stock', '>', 0);
        });
    } else {
        // Sin filtro, mostrar productos con stock en CUALQUIER bodega
        $query->where('current_stock', '>', 0);
    }
    
    $products = $query->orderBy('name')->get();
    
    // Transformar datos para el POS
    $products = $products->map(function($product) use ($warehouseId) {
        $productData = [
            'id' => $product->id,
            'code' => $product->code,
            'name' => $product->name,
            'description' => $product->description,
            'price' => (float) $product->price,
            'current_stock' => (int) $product->current_stock,
            'stock_min' => $product->stock_min,
            'stock_max' => $product->stock_max,
            'category_id' => $product->category_id,
            'category_name' => $product->category->name ?? 'Sin categoría',
            'category_color' => $product->category->color ?? '#6b7280',
            'image' => $product->image,
            'active' => $product->active,
            'warehouses' => $product->warehouses // Todas las bodegas con stock
        ];
        
        // Si hay bodega específica, agregar stock de ESA bodega
        if ($warehouseId && $product->warehouses) {
            $currentWarehouse = $product->warehouses->firstWhere('id', $warehouseId);
            if ($currentWarehouse) {
                $productData['stock_in_current_warehouse'] = (int) $currentWarehouse->pivot->stock;
            }
        }
        
        return $productData;
    });
    
    return response()->json([
        'success' => true,
        'data' => $products,
        'warehouse_id' => $warehouseId
    ]);
}
```

---

### 6. Frontend - appStore.js

**Archivo:** `src/store/appStore.js`

#### Modificar `loadProducts()` para aceptar warehouse_id:

```javascript
async loadProducts(warehouseId = null) {
  if (this.loading.products) return // Evitar cargas duplicadas

  try {
    this.loading.products = true
    
    // Si hay warehouse_id, pasarlo como parámetro
    const params = warehouseId ? { warehouse_id: warehouseId } : {}
    
    const response = await productsService.getForPos(params)
    if (response.success) {
      this.products = response.data.map(product => ({
        ...product,
        stock: product.stock_in_current_warehouse || product.current_stock,
        warehouse_stock: product.warehouses || [],
        price: parseFloat(product.price || 0),
        category_name: product.category_name || 'Sin categoría',
        category_color: product.category_color || '#6b7280'
      }))
      
      console.log(`✅ Productos cargados para bodega ${warehouseId || 'todas'}:`, this.products.length)
    }
  } catch (error) {
    console.error('❌ Error precargando productos:', error)
  } finally {
    this.loading.products = false
  }
},
```

---

### 7. Frontend - productsService.js

**Archivo:** `src/services/productsService.js`

#### Modificar método `getForPos()`:

```javascript
async getForPos(params = {}) {
  try {
    const response = await api.get('/products/for-pos', { params })
    return response.data
  } catch (error) {
    console.error('Error obteniendo productos para POS:', error)
    throw error
  }
}
```

---

### 8. Frontend - PosView.vue

**Ubicación de cambios:**

#### A. Al inicializar el POS, cargar productos de la bodega de la sesión:

```javascript
// En el onMounted o donde se inicializa el POS
const initializePOS = async () => {
  // ... código existente ...
  
  // Obtener sesión de caja activa
  await appStore.loadCashSession()
  
  // Si hay sesión, cargar productos de esa bodega
  if (appStore.cashSession.current?.warehouse_id) {
    const warehouseId = appStore.cashSession.current.warehouse_id
    console.log(`🏪 Cargando productos de bodega: ${appStore.cashSession.current.warehouse?.name}`)
    await appStore.loadProducts(warehouseId)
  } else {
    // Sin sesión, cargar todos los productos
    await appStore.loadProducts()
  }
  
  // ... resto del código ...
}
```

#### B. Mostrar indicador de bodega actual en el POS:

```html
<!-- Agregar en el header del POS -->
<div v-if="appStore.cashSession.current?.warehouse" 
     class="flex items-center space-x-2 px-3 py-2 bg-blue-50 rounded-lg border border-blue-200">
  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
  </svg>
  <div>
    <p class="text-xs text-blue-600 font-medium">Tienda Actual</p>
    <p class="text-sm font-bold text-blue-900">{{ appStore.cashSession.current.warehouse.name }}</p>
  </div>
</div>
```

#### C. Modificar visualización de productos para mostrar disponibilidad:

```html
<!-- En la tarjeta de producto -->
<div class="product-card">
  <!-- ... resto del contenido ... -->
  
  <!-- Stock con indicador de bodega -->
  <div class="stock-info">
    <span class="stock-badge">{{ product.stock }} en esta tienda</span>
    
    <!-- Si hay stock en otras bodegas -->
    <button v-if="hasStockInOtherWarehouses(product)" 
            @click="showWarehouseStock(product)"
            class="text-xs text-blue-600 hover:underline">
      Ver disponibilidad en otras tiendas
    </button>
  </div>
</div>
```

#### D. Funciones auxiliares:

```javascript
// Verificar si hay stock en otras bodegas
const hasStockInOtherWarehouses = (product) => {
  if (!product.warehouse_stock || !appStore.cashSession.current?.warehouse_id) return false
  
  return product.warehouse_stock.some(wh => 
    wh.id !== appStore.cashSession.current.warehouse_id && wh.pivot.stock > 0
  )
}

// Mostrar modal con stock por bodega
const showWarehouseStock = (product) => {
  // Crear modal mostrando:
  // - Bodega actual con su stock
  // - Otras bodegas con stock disponible
  // - Permitir agregar al carrito desde cualquier bodega
}

// Al agregar producto al carrito, especificar de qué bodega
const addToCart = (product, quantity, warehouseId = null) => {
  const itemWarehouse = warehouseId || appStore.cashSession.current?.warehouse_id
  
  cart.value.push({
    ...product,
    quantity,
    warehouse_id: itemWarehouse,
    warehouse_name: getWarehouseName(itemWarehouse)
  })
}
```

---

### 9. Frontend - Modificar Proceso de Venta

**Archivo:** `backend/app/Http/Controllers/Api/InvoiceController.php`

#### Al crear factura, descontar stock de la bodega correcta:

```php
public function store(Request $request)
{
    // ... validación existente ...
    
    DB::transaction(function () use ($request, &$invoice) {
        // ... crear factura ...
        
        // Procesar items
        foreach ($request->items as $item) {
            // Crear item de factura
            $invoiceItem = $invoice->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['subtotal'],
                'warehouse_id' => $item['warehouse_id'] ?? null // NUEVO
            ]);
            
            // Descontar stock de la bodega específica
            if (isset($item['warehouse_id'])) {
                $product = Product::find($item['product_id']);
                
                // Descontar de product_warehouse
                DB::table('product_warehouse')
                    ->where('product_id', $item['product_id'])
                    ->where('warehouse_id', $item['warehouse_id'])
                    ->decrement('stock', $item['quantity']);
                
                // Actualizar current_stock total del producto
                $totalStock = DB::table('product_warehouse')
                    ->where('product_id', $item['product_id'])
                    ->sum('stock');
                
                $product->update(['current_stock' => $totalStock]);
            }
        }
        
        // ... resto del código ...
    });
}
```

---

## 🎯 FLUJO COMPLETO DEL SISTEMA

### Escenario 1: Apertura de Caja Normal

```
Usuario hace clic en "Abrir Caja"
    ↓
CashOpenModal se abre
    ↓
loadWarehouses() carga las tiendas activas
    ↓
Auto-selecciona tienda predeterminada (o usuario elige manualmente)
    ↓
Usuario ingresa monto inicial
    ↓
Submit → emit('success', { warehouse_id, opening_amount, notes })
    ↓
POST /api/cash-sessions/open con warehouse_id
    ↓
Backend crea cash_session con warehouse_id
    ↓
POS recarga productos SOLO de esa bodega
    ↓
Usuario ve productos disponibles en SU tienda
```

### Escenario 2: Venta de Producto de la Tienda Actual

```
Usuario busca producto "Laptop HP"
    ↓
POS muestra: "Stock: 5 en esta tienda (Tienda Centro)"
    ↓
Usuario agrega 2 unidades al carrito
    ↓
cart.items.push({ ...product, quantity: 2, warehouse_id: 1 })
    ↓
Usuario procesa venta
    ↓
Backend descuenta 2 unidades del stock de warehouse_id = 1
    ↓
product_warehouse.stock (warehouse_id=1) pasa de 5 a 3
```

### Escenario 3: Venta Cruzada (Producto de Otra Tienda)

```
Usuario busca producto "Monitor LG"
    ↓
POS muestra: "Stock: 0 en esta tienda"
    ↓
Botón: "Ver disponibilidad en otras tiendas"
    ↓
Usuario hace clic → Modal muestra:
  ✅ Tienda Norte: 8 unidades
  ✅ Tienda Sur: 3 unidades
    ↓
Usuario selecciona "Agregar 1 de Tienda Norte"
    ↓
cart.items.push({ ...product, quantity: 1, warehouse_id: 2 })
    ↓
En el carrito se muestra: "Monitor LG (1x) - Tienda Norte"
    ↓
Usuario procesa venta
    ↓
Backend descuenta 1 unidad del stock de warehouse_id = 2
    ↓
product_warehouse.stock (warehouse_id=2) pasa de 8 a 7
```

---

## 📊 MODIFICACIONES EN BASE DE DATOS

### Tabla: invoice_items

Necesita agregar columna `warehouse_id` para saber de qué bodega se vendió:

```sql
ALTER TABLE invoice_items 
ADD COLUMN warehouse_id BIGINT UNSIGNED NULL AFTER product_id,
ADD CONSTRAINT fk_invoice_items_warehouse 
FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE SET NULL;
```

---

## 🧪 TESTING RECOMENDADO

### Test 1: Apertura de Caja con Tienda

1. Ir al POS
2. Hacer clic en "Abrir Caja"
3. Verificar que selector de tienda aparece y tiene opciones
4. Seleccionar "Tienda Centro"
5. Ingresar monto inicial
6. Abrir caja
7. Verificar en consola: `🏪 Cargando productos de bodega: Tienda Centro`
8. Verificar que aparece indicador de tienda en header del POS

### Test 2: Filtrado de Productos por Tienda

1. Con caja abierta en "Tienda Centro"
2. Verificar que solo aparecen productos con stock en Tienda Centro
3. Buscar producto que existe en otra tienda pero no en la actual
4. Verificar que NO aparece en resultados principales
5. Hacer clic en "Buscar en todas las tiendas"
6. Verificar que ahora SÍ aparece con indicador de otra tienda

### Test 3: Venta Normal (Misma Tienda)

1. Agregar producto al carrito
2. Verificar que muestra "Stock: X en esta tienda"
3. Procesar venta
4. Verificar en base de datos:
   ```sql
   SELECT pw.warehouse_id, pw.stock 
   FROM product_warehouse pw 
   WHERE product_id = [ID_PRODUCTO];
   ```
5. Confirmar que stock se descontó de la bodega correcta

### Test 4: Venta Cruzada (Otra Tienda)

1. Buscar producto con stock en otra tienda
2. Hacer clic en "Ver disponibilidad"
3. Seleccionar bodega con stock
4. Agregar al carrito
5. Verificar que indica origen: "(Producto) - Tienda Norte"
6. Procesar venta
7. Verificar que stock se descontó de la bodega origen

---

## ⚠️ CONSIDERACIONES IMPORTANTES

### 1. Permisos y Seguridad
- Verificar que solo usuarios autorizados puedan vender de otras tiendas
- Opción en configuración: `allow_cross_warehouse_sales` (true/false)

### 2. Reportes
- Los reportes de caja deben mostrar ventas por bodega
- Reporte de traslados entre bodegas
- Reporte de ventas cruzadas

### 3. Inventario
- El stock total (`current_stock`) es la suma de todas las bodegas
- El stock por bodega está en `product_warehouse.stock`
- Al hacer inventario físico, debe ser por bodega

### 4. UI/UX
- Indicador claro de qué tienda está activa
- Al buscar, distinguir entre productos locales y de otras tiendas
- Badges de colores:
  - 🟢 Verde: Stock en tienda actual
  - 🔵 Azul: Stock en otra tienda
  - 🔴 Rojo: Sin stock en ninguna tienda

---

## 📝 CHECKLIST DE IMPLEMENTACIÓN

- [x] Modal de apertura con selector de tienda
- [x] Backend: Validar y guardar warehouse_id en cash_sessions
- [x] Backend: Cargar relación warehouse en sesiones
- [ ] Backend: Modificar ProductController::getForPos() con filtro warehouse_id
- [ ] Backend: Agregar columna warehouse_id a invoice_items
- [ ] Backend: Modificar InvoiceController::store() para descontar de bodega correcta
- [ ] Frontend: Modificar appStore.loadProducts() para aceptar warehouse_id
- [ ] Frontend: Modificar productsService.getForPos() para pasar parámetros
- [ ] Frontend: Mostrar indicador de tienda actual en POS
- [ ] Frontend: Modificar visualización de productos con badges de bodega
- [ ] Frontend: Agregar modal de disponibilidad por bodega
- [ ] Frontend: Modificar addToCart para incluir warehouse_id
- [ ] Frontend: Mostrar origen de productos en el carrito
- [ ] Testing: Probar flujo completo de apertura y venta

---

**Fecha:** 30 de noviembre de 2025  
**Estado:** 30% Completado (Backend listo, Frontend pendiente)  
**Prioridad:** ALTA - Funcionalidad crítica del negocio
