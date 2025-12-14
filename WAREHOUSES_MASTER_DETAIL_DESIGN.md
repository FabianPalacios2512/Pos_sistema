# 🏢 Rediseño Master-Detail: Gestión de Sedes

## 📋 Estructura Propuesta

### Layout 30/70 (Como Facturación)

```
┌─────────────────────────────────────────────────────────┐
│  Header: Gestión de Sedes + KPIs + Botón Nueva Sede    │
├──────────────────┬──────────────────────────────────────┤
│                  │                                      │
│  LISTA SEDES     │    DETALLE DE SEDE SELECCIONADA     │
│  (30%)           │    (70%)                             │
│                  │                                      │
│  🏢 Sede Princ.  │    📊 KPIs de la Sede               │
│  🏪 ASAS         │    ├─ Productos: 28                  │
│  🏪 Sede Norte   │    ├─ Stock Total: 571 unidades      │
│  ➕ Nueva Sede   │    └─ Valor Inventario: $X           │
│                  │                                      │
│                  │    📦 TABLA DE PRODUCTOS             │
│                  │    ┌──────┬────────┬──────┬────────┐│
│                  │    │ SKU  │ Nombre │Stock │ Precio ││
│                  │    ├──────┼────────┼──────┼────────┤│
│                  │    │ P001 │ Prod 1 │  9   │ $1.9M  ││
│                  │    │ P002 │ Prod 2 │  0   │ $21    ││
│                  │    └──────┴────────┴──────┴────────┘│
│                  │                                      │
└──────────────────┴──────────────────────────────────────┘
```

## 🎨 Características del Diseño

### Panel Izquierdo (Master - 30%)
- ✅ **Header con búsqueda**: Filtrar sedes por nombre
- ✅ **Lista minimalista**: Cada sede muestra:
  - Nombre
  - Icono (🏢 principal, 🏪 secundarias)
  - Badge de estado (Activa/Inactiva)
  - Número de productos
  - Indicador de sede principal
- ✅ **Selección visual**: Borde izquierdo azul cuando está seleccionada
- ✅ **Hover suave**: `hover:bg-gray-50 dark:hover:bg-zinc-800/50`

### Panel Derecho (Detail - 70%)
- ✅ **Estado vacío**: Cuando no hay sede seleccionada
  - Ilustración SVG
  - Mensaje: "Selecciona una sede para ver sus productos"
  
- ✅ **Vista completa cuando hay selección**:
  1. **Header de sede**:
     - Nombre completo
     - Badges: Principal/Activa
     - Botones: Editar, Ver Inventario, Eliminar
  
  2. **KPIs de la sede** (grid 2x2 o 1x4):
     - Total Productos
     - Stock Total (unidades)
     - Valor Inventario
     - Productos Bajo Stock
  
  3. **Tabla de productos** (con paginación):
     - Columnas: Imagen, SKU, Nombre, Categoría, Stock, Min/Max, Precio, Acciones
     - Búsqueda y filtros
     - Ordenamiento
     - Estados visuales (stock bajo = rojo, sin stock = gris)

## 🔧 API Endpoints Necesarios

```typescript
// GET /api/warehouses/active - Ya existe
// GET /api/warehouses/{id}/products - Nuevo endpoint
{
  success: true,
  data: {
    warehouse: { id, name, address, is_default, active },
    summary: {
      total_products: 28,
      total_stock: 571,
      total_value: 75185752,
      low_stock_count: 3
    },
    products: [
      {
        id, name, sku, barcode, image_url,
        category_name, stock, min_stock, max_stock,
        cost_price, sale_price, unit
      }
    ]
  }
}
```

## 📝 Notas de Implementación

1. Mantener diseño consistente con InvoicesView
2. Usar mismos colores y transiciones
3. Responsive: En móvil, mostrar solo lista o detalle (toggle)
4. Preservar funcionalidad de crear/editar/eliminar sedes
5. Agregar loading states
