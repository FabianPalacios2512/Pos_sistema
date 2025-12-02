# 🏢 Sistema Multi-Tienda para Productos

## 📦 ¿Cómo funciona ahora?

### ✅ **Crear Producto con Stock por Tienda**

Cuando creas un nuevo producto, puedes asignar stock diferente a cada tienda:

1. **Llenar información básica** (nombre, precio, categoría)
2. **Sección "Inventario por Tienda"** muestra todas tus tiendas
3. **Asignar stock individual:**
   - Tienda A: 4 unidades
   - Tienda B: 10 unidades
   - Tienda C: 0 unidades (no disponible en esa tienda)
4. **Stock Total** se calcula automáticamente: 14 unidades

### ✅ **Editar Producto Existente**

Cuando editas un producto:
- El modal carga el stock actual de cada tienda
- Puedes modificar el stock de cualquier tienda
- El stock total se recalcula automáticamente

### 🎯 **Casos de Uso**

#### Caso 1: Mismo producto, diferente stock
```
Producto: "Cuaderno 100 hojas"
- Sede Principal: 4 unidades
- Margaritas: 10 unidades
- Stock Total: 14 unidades
```

#### Caso 2: Producto solo en algunas tiendas
```
Producto: "Gorra Premium"
- Sede Principal: 20 unidades
- Margaritas: 0 unidades (no se vende ahí)
- Stock Total: 20 unidades
```

#### Caso 3: Mismo stock en todas las tiendas
```
Producto: "Bolígrafo Azul"
- Sede Principal: 50 unidades
- Margaritas: 50 unidades
- Stock Total: 100 unidades
```

### ❓ **NO necesitas crear productos duplicados**

**Antes:** ❌
- Cuaderno (Sede A)
- Cuaderno (Sede B)

**Ahora:** ✅
- Cuaderno
  - Stock en Sede A: 4
  - Stock en Sede B: 10

## 🔧 **Cambios Técnicos Implementados**

### Frontend (ProductModal.vue)
- ✅ Nueva sección "Inventario por Tienda"
- ✅ Input de stock por cada tienda
- ✅ Cálculo automático de stock total
- ✅ Ayuda contextual con tooltip
- ✅ Props `warehouses` agregado
- ✅ Estado `warehouseStock` en formData
- ✅ Computed `totalStock` para cálculo
- ✅ Datos enviados con `warehouse_stocks`

### Backend (PENDIENTE - Siguiente paso)
⚠️ **Falta actualizar el ProductController para recibir y guardar el stock por tienda**

Necesitamos modificar:
1. `store()` - Crear producto con stock en múltiples tiendas
2. `update()` - Actualizar stock en múltiples tiendas
3. Validación de `warehouse_stocks` en request

## 📝 **Estructura de Datos**

### Envío al Backend
```json
{
  "name": "Cuaderno",
  "price": 2500,
  "cost": 1500,
  "category_id": 3,
  "stock": 14,
  "warehouse_stocks": {
    "1": 4,
    "2": 10
  }
}
```

### Tabla `product_warehouse` (ya existe)
```
| product_id | warehouse_id | stock |
|------------|--------------|-------|
| 123        | 1            | 4     |
| 123        | 2            | 10    |
```

## 🚀 **Siguiente Paso: Actualizar Backend**

Necesitamos modificar `ProductController.php` para:

1. **Al crear producto:**
   - Recibir `warehouse_stocks` del request
   - Insertar múltiples registros en `product_warehouse`
   - Calcular `current_stock` sumando todos

2. **Al actualizar producto:**
   - Recibir `warehouse_stocks`
   - Actualizar/insertar/eliminar en `product_warehouse`
   - Recalcular `current_stock`

3. **Al mostrar producto:**
   - Incluir stock de todas las tiendas en response
   - Array `alternative_warehouses` con stock por tienda

## ✅ **Estado Actual**

- ✅ UI Multi-tienda completa
- ✅ Toggle de búsqueda global persistente
- ✅ Diseño minimalista del header
- ⏳ Backend multi-tienda (siguiente paso)

---

**Fecha:** 30 de noviembre de 2025  
**Versión:** 1.0 - Sistema Multi-Tienda
