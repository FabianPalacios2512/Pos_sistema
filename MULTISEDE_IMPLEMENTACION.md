# 🏢 Sistema Multisede/Multitienda - Documentación de Implementación

## 📊 Resumen Ejecutivo

Se ha implementado un sistema completo de múltiples sedes (bodegas/tiendas) para el POS, permitiendo:

✅ **Gestión de Inventario por Sede**: Cada producto tiene stock independiente en cada sede
✅ **Traslados de Mercancía**: Mover productos entre sedes con trazabilidad
✅ **Sesiones de Caja por Sede**: El cajero trabaja en una sede específica
✅ **Migración Transparente**: Los datos existentes se migran automáticamente a "Sede Principal"

---

## 🗄️ Base de Datos - Migraciones Creadas

### 1. **warehouses** - Tabla de Sedes/Bodegas
```
- id
- name (nombre de la sede)
- address (dirección)
- phone (teléfono)
- is_default (sede por defecto)
- active (activa/inactiva)
- timestamps
```

### 2. **product_warehouse** - Stock por Sede (Pivote)
```
- id
- product_id
- warehouse_id
- stock (cantidad en esa sede)
- timestamps
```

### 3. **stock_transfers** - Traslados de Mercancía
```
- id
- source_warehouse_id (origen)
- destination_warehouse_id (destino)
- user_id (quien hace el traslado)
- reference_number (TRF-000001)
- notes
- status (pending/in_transit/completed/cancelled)
- transferred_at
- received_at
- timestamps
```

### 4. **stock_transfer_items** - Items de Traslado
```
- id
- stock_transfer_id
- product_id
- quantity
- received_quantity
- notes
- timestamps
```

### 5. **Modificaciones a Tablas Existentes**

#### cash_sessions
- ➕ `warehouse_id` - Sede donde opera la caja

#### inventory_movements
- ➕ `warehouse_id` - Sede del movimiento
- ➕ `reason` - Razón del movimiento

---

## 🔄 Migración de Datos Existentes

La migración `2025_11_30_000007_migrate_existing_stock_to_warehouses.php` realiza automáticamente:

1. ✅ Crea una "Sede Principal" por defecto para cada tenant
2. ✅ Migra el `current_stock` de todos los productos a `product_warehouse`
3. ✅ Asigna la "Sede Principal" a todas las sesiones de caja existentes
4. ✅ Asigna la "Sede Principal" a todos los movimientos de inventario

**⚠️ CRÍTICO**: Después de ejecutar las migraciones, el campo `current_stock` en la tabla `products` se mantiene pero ahora representa la **suma de todas las bodegas**.

---

## 🏗️ Modelos PHP Creados

### 1. **Warehouse.php**
Métodos principales:
- `getProductStock($productId)` - Obtener stock de un producto
- `updateProductStock($productId, $quantity)` - Actualizar stock
- `incrementProductStock($productId, $quantity)` - Sumar stock
- `decrementProductStock($productId, $quantity)` - Restar stock

### 2. **StockTransfer.php**
Métodos principales:
- `generateReferenceNumber()` - Generar TRF-000001
- `complete()` - Ejecutar el traslado (descontar origen, sumar destino)

### 3. **StockTransferItem.php**
Items individuales de cada traslado

### 4. **Product.php** (Actualizado)
Nuevas relaciones y métodos:
- `warehouses()` - Relación con bodegas
- `getTotalStockAttribute()` - Stock total sumado
- `getStockInWarehouse($warehouseId)` - Stock en una sede específica
- `getStockBreakdown()` - Desglose por bodega

### 5. **CashSession.php** (Actualizado)
- ➕ `warehouse()` - Relación con la bodega

### 6. **InventoryMovement.php** (Actualizado)
- ➕ `warehouse()` - Relación con la bodega

---

## 🎛️ Controladores API Creados

### 1. **WarehouseController.php**

Endpoints:
```
GET    /warehouses              - Listar todas las sedes
GET    /warehouses/default      - Obtener sede por defecto
GET    /warehouses/{id}         - Ver una sede
POST   /warehouses              - Crear sede
PUT    /warehouses/{id}         - Actualizar sede
DELETE /warehouses/{id}         - Eliminar sede (sin stock)
GET    /warehouses/{id}/inventory - Inventario de la sede
POST   /warehouses/{id}/update-stock - Actualizar stock
```

### 2. **StockTransferController.php**

Endpoints:
```
GET    /stock-transfers         - Listar traslados
GET    /stock-transfers/{id}    - Ver traslado
POST   /stock-transfers         - Crear traslado
POST   /stock-transfers/{id}/complete - Completar traslado
POST   /stock-transfers/{id}/cancel   - Cancelar traslado
DELETE /stock-transfers/{id}    - Eliminar traslado
```

---

## 🎨 Frontend - Vistas Creadas

### 1. **Warehouses.vue** - Gestión de Sedes ✅
Ubicación: `/src/views/Warehouses.vue`

Características:
- ✅ Diseño empresarial profesional con métricas
- ✅ Listado de sedes con información completa
- ✅ Botones para crear, editar, ver inventario y eliminar
- ✅ Indicador visual de sede principal
- ✅ Contador de productos por sede

### 2. **Archivos Frontend Pendientes de Crear**

Necesitas crear:

1. **WarehouseModal.vue** - Modal para crear/editar sedes
   Ubicación: `/src/components/warehouses/WarehouseModal.vue`

2. **WarehouseInventory.vue** - Vista de inventario de una sede
   Ubicación: `/src/views/WarehouseInventory.vue`

3. **StockTransfers.vue** - Gestión de traslados
   Ubicación: `/src/views/StockTransfers.vue`

4. **StockTransferModal.vue** - Modal para crear traslados
   Ubicación: `/src/components/warehouses/StockTransferModal.vue`

---

## 🔧 Pasos para Completar la Implementación

### Paso 1: Ejecutar Migraciones

```bash
cd backend
php artisan migrate
```

Esto creará todas las tablas y migrará los datos existentes.

### Paso 2: Verificar Datos Migrados

Ejecuta en MySQL:

```sql
-- Ver sedes creadas
SELECT * FROM warehouses;

-- Ver stock migrado
SELECT p.name, w.name as warehouse, pw.stock
FROM products p
JOIN product_warehouse pw ON p.id = pw.product_id
JOIN warehouses w ON pw.warehouse_id = w.id
LIMIT 20;
```

### Paso 3: Crear Componentes Frontend Faltantes

Necesitas crear 4 archivos más en el frontend:

1. `WarehouseModal.vue` - Para crear/editar sedes
2. `WarehouseInventory.vue` - Para ver stock de una sede
3. `StockTransfers.vue` - Para gestionar traslados
4. `StockTransferModal.vue` - Para crear nuevos traslados

### Paso 4: Actualizar Rutas del Frontend

Agregar en `/src/router/index.js`:

```javascript
{
  path: '/warehouses',
  name: 'Warehouses',
  component: () => import('@/views/Warehouses.vue'),
  meta: { requiresAuth: true }
},
{
  path: '/warehouses/:id/inventory',
  name: 'WarehouseInventory',
  component: () => import('@/views/WarehouseInventory.vue'),
  meta: { requiresAuth: true }
},
{
  path: '/stock-transfers',
  name: 'StockTransfers',
  component: () => import('@/views/StockTransfers.vue'),
  meta: { requiresAuth: true }
}
```

### Paso 5: Actualizar el POS

Modificar el componente de apertura de caja para que seleccione la sede:

```javascript
// En CashSessionModal.vue o similar
<select v-model="selectedWarehouseId">
  <option v-for="w in warehouses" :key="w.id" :value="w.id">
    {{ w.name }}
  </option>
</select>
```

### Paso 6: Modificar Lógica de Ventas

En el controlador de ventas, al descontar stock, ahora debe:

1. Obtener `warehouse_id` desde la `CashSession` activa
2. Descontar stock de esa bodega específica:

```php
$warehouse = $cashSession->warehouse;
$warehouse->decrementProductStock($productId, $quantity);

// Actualizar current_stock global
$product->update([
    'current_stock' => $product->warehouses()->sum('product_warehouse.stock')
]);
```

---

## 📊 Flujo de Uso del Sistema

### Escenario 1: Negocio con Una Sola Tienda
- ✅ El sistema crea automáticamente "Sede Principal"
- ✅ Todo el stock está ahí
- ✅ El usuario no nota diferencia alguna
- ✅ Sistema listo para cuando abran sucursales

### Escenario 2: Abrir una Nueva Sucursal

1. **Crear la nueva sede**
   - Ir a "Gestión de Sedes"
   - Clic en "Nueva Sede"
   - Llenar: Nombre, Dirección, Teléfono

2. **Trasladar mercancía inicial**
   - Ir a "Traslados"
   - Seleccionar: Origen (Sede Principal) → Destino (Sucursal Norte)
   - Agregar productos y cantidades
   - Completar traslado

3. **Apertura de caja en la nueva sede**
   - El cajero abre turno
   - Selecciona "Sucursal Norte"
   - Solo puede vender el stock de esa sede

### Escenario 3: Traslado Entre Sedes

```
1. Gerente crea traslado:
   - Origen: Sede Centro
   - Destino: Sede Norte
   - Productos: 10 Coca-Colas, 5 Pepsis
   - Estado: Pendiente

2. Sistema valida stock disponible en origen

3. Gerente completa traslado:
   - Stock se descuenta de Centro
   - Stock se suma en Norte
   - Se registran movimientos de inventario
   - Estado: Completado
```

---

## 🔐 Validaciones de Seguridad Implementadas

✅ No se puede eliminar la sede por defecto
✅ No se puede eliminar una sede con stock
✅ No se puede trasladar más stock del disponible
✅ No se puede completar un traslado dos veces
✅ Solo se pueden cancelar traslados pendientes
✅ Los traslados completados no se pueden eliminar

---

## 🎯 Próximos Pasos Recomendados

### Prioridad Alta 🔴
1. [ ] Crear componentes modal faltantes (WarehouseModal, StockTransferModal)
2. [ ] Crear vista de inventario por sede (WarehouseInventory.vue)
3. [ ] Crear vista de traslados (StockTransfers.vue)
4. [ ] Modificar apertura de caja para seleccionar sede
5. [ ] Actualizar ProductController para manejar stock por bodega

### Prioridad Media 🟡
6. [ ] Agregar tooltip en lista de productos mostrando desglose de stock
7. [ ] Agregar opción en detalle de producto para ver stock por sede
8. [ ] Agregar filtros en traslados (por sede, estado, fecha)
9. [ ] Agregar reportes de traslados

### Prioridad Baja 🟢
10. [ ] Agregar estados intermedios (en_tránsito) para traslados
11. [ ] Agregar notificaciones cuando llegue un traslado
12. [ ] Dashboard de actividad por sede
13. [ ] Comparación de rendimiento entre sedes

---

## 📞 Soporte y Preguntas

Si encuentras algún error o necesitas ayuda:

1. Verifica que las migraciones se ejecutaron correctamente
2. Revisa los logs de Laravel: `backend/storage/logs/laravel.log`
3. Verifica la consola del navegador para errores de frontend
4. Asegúrate de que todos los servicios están importados correctamente

---

**Fecha de Implementación**: 30 de noviembre de 2025
**Versión del Sistema**: 2.0 - Multisede
**Estado**: Backend Completo ✅ | Frontend Parcial ⚠️
