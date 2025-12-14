# 🚀 Guía Rápida de Implementación - Sistema Multisede

## ✅ Lo que YA está hecho

### Backend (100% Completo)
- ✅ 7 Migraciones creadas
- ✅ 4 Modelos creados/actualizados (Warehouse, StockTransfer, StockTransferItem, Product)
- ✅ 2 Controladores completos (WarehouseController, StockTransferController)
- ✅ Rutas API configuradas
- ✅ Sistema de migración automática de datos

### Frontend (30% Completo)
- ✅ Vista principal de sedes (Warehouses.vue)
- ✅ Modal de crear/editar sede (WarehouseModal.vue)
- ✅ 2 Servicios (warehouseService.js, stockTransferService.js)

---

## 📋 Pasos para Implementar AHORA

### 1️⃣ Ejecutar Migraciones (5 minutos)

```bash
cd /home/kali/Escritorio/definitivo/01_POS_BASICO\ \(2\)/backend

# Dar permisos al script
chmod +x implement_multisede.sh

# Ejecutar
./implement_multisede.sh
```

O manualmente:
```bash
php artisan migrate
```

### 2️⃣ Verificar en la Base de Datos (2 minutos)

```bash
mysql -u root

USE pos_tenant_1;  # O el nombre de tu base de datos tenant

-- Ver la sede principal creada
SELECT * FROM warehouses;

-- Ver stock migrado
SELECT 
    p.name AS producto, 
    w.name AS sede, 
    pw.stock 
FROM products p
JOIN product_warehouse pw ON p.id = pw.product_id
JOIN warehouses w ON pw.warehouse_id = w.id
LIMIT 10;
```

### 3️⃣ Agregar Rutas en el Frontend (2 minutos)

Edita `/src/router/index.js` y agrega:

```javascript
// Dentro del array de rutas
{
  path: '/warehouses',
  name: 'Warehouses',
  component: () => import('@/views/Warehouses.vue'),
  meta: { requiresAuth: true, title: 'Gestión de Sedes' }
},
```

### 4️⃣ Agregar al Menú de Navegación (3 minutos)

Encuentra el archivo del menú principal (probablemente `PosCompleto.vue` o `Sidebar.vue`) y agrega:

```vue
<!-- Opción de menú -->
<router-link 
  to="/warehouses"
  class="flex items-center space-x-3 px-4 py-3 hover:bg-gray-100 rounded-lg">
  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
  </svg>
  <span>Gestión de Sedes</span>
</router-link>
```

### 5️⃣ Probar el Sistema (5 minutos)

1. Levanta el servidor:
```bash
# Terminal 1 - Backend
cd backend
php artisan serve

# Terminal 2 - Frontend
cd ..
npm run dev
```

2. Abre el navegador en `http://localhost:5173`

3. Ve a "Gestión de Sedes"

4. Verifica que aparece la "Sede Principal" con todo tu stock

5. Intenta crear una nueva sede de prueba

---

## 🎯 Lo que Falta (Opcional - Para después)

### Componentes Adicionales (1-2 horas)

1. **Vista de Traslados** (`StockTransfers.vue`)
   - Lista de traslados
   - Filtros por estado, sede, fecha
   - Acciones: completar, cancelar, eliminar

2. **Modal de Traslados** (`StockTransferModal.vue`)
   - Seleccionar sede origen/destino
   - Buscar y agregar productos
   - Validar stock disponible

3. **Vista de Inventario por Sede** (`WarehouseInventory.vue`)
   - Tabla de productos con su stock en esa sede
   - Ajustes manuales de stock
   - Historial de movimientos

### Integración con POS (30 minutos)

1. **Modificar Apertura de Caja**
   - Agregar selector de sede al abrir caja
   - Guardar `warehouse_id` en la sesión

2. **Actualizar Lógica de Ventas**
   - Descontar stock de la bodega de la sesión activa
   - Actualizar `current_stock` global del producto

---

## 🧪 Script de Prueba Rápida

Crea este archivo para probar todo:

```bash
# test_multisede.sh
cd backend

echo "🧪 Probando Sistema Multisede..."
echo ""

# 1. Ver sedes
echo "📦 Sedes registradas:"
php artisan tinker --execute="
  \$warehouses = App\Models\Warehouse::all();
  foreach (\$warehouses as \$w) {
    echo '- ' . \$w->name . ' (ID: ' . \$w->id . ')' . PHP_EOL;
  }
"

echo ""

# 2. Ver productos con stock
echo "📊 Stock por sede (primeros 5 productos):"
php artisan tinker --execute="
  \$products = App\Models\Product::with('warehouses')->take(5)->get();
  foreach (\$products as \$p) {
    echo PHP_EOL . '🏷️  ' . \$p->name . ':' . PHP_EOL;
    foreach (\$p->warehouses as \$w) {
      echo '   ' . \$w->name . ': ' . \$w->pivot->stock . ' und' . PHP_EOL;
    }
  }
"

echo ""
echo "✅ Pruebas completadas"
```

---

## ⚠️ Problemas Comunes y Soluciones

### Error: "Class Warehouse not found"
**Solución**: Ejecuta `composer dump-autoload` en el backend

### Error: "SQLSTATE[42S01]: Base table or view already exists"
**Solución**: Ya ejecutaste las migraciones. Revisa con `php artisan migrate:status`

### Error: "warehouse_id cannot be null"
**Solución**: La migración de datos no corrió. Ejecuta:
```bash
php artisan migrate:refresh --step=1
```

### Frontend no muestra la vista
**Solución**: Verifica que:
1. Agregaste la ruta en `router/index.js`
2. El archivo `Warehouses.vue` existe
3. No hay errores en la consola del navegador

---

## 📊 Resultado Esperado

Después de estos pasos deberías poder:

✅ Ver la vista "Gestión de Sedes"
✅ Listar tu "Sede Principal" con todo el stock actual
✅ Crear nuevas sedes
✅ Editar información de sedes
✅ Ver métricas globales del inventario

---

## 🎉 ¿Listo para Producción?

Para usar en producción real necesitas:

1. ✅ **Migraciones ejecutadas** (Lo harás ahora)
2. ⚠️ **Backup de la base de datos** (Antes de migrar)
3. ⚠️ **Completar vistas de traslados** (Para mover inventario)
4. ⚠️ **Integrar con apertura de caja** (Para seleccionar sede)
5. ⚠️ **Actualizar lógica de ventas** (Para descontar de la sede correcta)

---

**¿Necesitas ayuda?**
Revisa el archivo `MULTISEDE_IMPLEMENTACION.md` para documentación completa.
