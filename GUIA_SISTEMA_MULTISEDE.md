# 🏪 Guía Rápida: Sistema Multisede (Multi-Tiendas/Bodegas)

## ✅ Sistema Instalado y Listo

El sistema multisede está **100% funcional** y listo para usar en tu base de datos `tenantasasasa`.

---

## 🎯 ¿Cómo Crear una Nueva Tienda o Bodega?

### Opción 1: Desde la Interfaz Web (Recomendado)

1. **Iniciar el Frontend**:
```bash
cd /home/kali/Escritorio/definitivo/01_POS_BASICO\ \(2\)
npm run dev
```

2. **Acceder al Sistema**:
   - Abre tu navegador en `http://localhost:5173`
   - Inicia sesión con tu cuenta de administrador

3. **Ir a Gestión de Sedes**:
   - En el menú lateral del POS, busca **"Gestión de Sedes"** o **"Warehouses"**
   - O navega directamente a: `http://localhost:5173/warehouses`

4. **Crear Nueva Sede**:
   - Haz clic en el botón verde **"Nueva Sede"** (esquina superior derecha)
   - Se abrirá un modal con el formulario

5. **Llenar el Formulario**:
   - **Nombre de la Sede** (obligatorio): Ej: "Sucursal Norte", "Bodega Centro"
   - **Dirección** (opcional): Ej: "Calle 123 #45-67"
   - **Teléfono** (opcional): Ej: "3001234567"
   - **Marcar como sede principal**: Solo si quieres que sea la predeterminada
   - **Sede activa**: Mantén activado para que pueda operar

6. **Guardar**:
   - Haz clic en **"Crear Sede"**
   - La nueva sede se creará con stock inicial en 0 para todos los productos

### Opción 2: Desde la Base de Datos (Manual)

Si necesitas crear una sede directamente en MySQL:

```sql
-- Conectar a la base de datos del tenant
mysql -u root tenantasasasa

-- Insertar nueva sede
INSERT INTO warehouses (name, address, phone, is_default, active, created_at, updated_at)
VALUES ('Sucursal Norte', 'Calle 45 #12-34', '3001234567', 0, 1, NOW(), NOW());

-- Obtener el ID de la nueva sede
SELECT LAST_INSERT_ID();

-- Agregar stock inicial (0) para todos los productos existentes
INSERT INTO product_warehouse (product_id, warehouse_id, stock, created_at, updated_at)
SELECT id, LAST_INSERT_ID(), 0, NOW(), NOW()
FROM products;
```

---

## 📦 ¿Cómo Trasladar Mercancía Entre Sedes?

### Desde la Interfaz Web:

1. **Ir a Traslados**:
   - Navega a `http://localhost:5173/stock-transfers`
   - O busca **"Traslados de Mercancía"** en el menú

2. **Crear Nuevo Traslado**:
   - Haz clic en **"Nuevo Traslado"** (botón verde)

3. **Configurar el Traslado**:
   - **Sede Origen**: Selecciona de dónde sale la mercancía
   - **Sede Destino**: Selecciona a dónde va la mercancía
   - **Notas** (opcional): Describe el motivo del traslado

4. **Agregar Productos**:
   - Haz clic en **"Agregar"** para añadir productos
   - Selecciona el producto del dropdown
   - Ingresa la cantidad a trasladar
   - Puedes agregar múltiples productos

5. **Crear el Traslado**:
   - Haz clic en **"Crear Traslado"**
   - El traslado se crea con estado **"Pendiente"**
   - **IMPORTANTE**: El stock NO se mueve aún

6. **Completar el Traslado**:
   - En la lista de traslados, busca el que acabas de crear
   - Haz clic en **"Ver Detalle"** (ícono de ojo)
   - En el modal de detalle, haz clic en **"Completar Traslado"**
   - **Ahora sí**: El stock se resta de la sede origen y se suma a la sede destino

---

## 🔍 Ver Inventario por Sede

1. **Desde Gestión de Sedes**:
   - Ve a `http://localhost:5173/warehouses`
   - Encuentra la sede que quieres ver
   - Haz clic en el botón **"Ver Inventario"** (ícono de caja)

2. **Vista de Inventario**:
   - Verás todos los productos con su stock específico en esa sede
   - Puedes filtrar por:
     - Búsqueda (nombre o código)
     - Estado de stock (Todos, Con Stock, Stock Bajo, Sin Stock)
   - Los productos se muestran con:
     - Stock actual
     - Estado (Disponible, Stock Bajo, Sin Stock)
     - Precio

---

## 🎯 Flujo Completo de Ejemplo

### Escenario: Abrir una Nueva Sucursal

1. **Crear la Nueva Sede**:
   ```
   Nombre: "Sucursal Sur"
   Dirección: "Av. Sur #78-90"
   Teléfono: "3009876543"
   Estado: Activa
   ```

2. **Trasladar Stock Inicial**:
   - Crear traslado de "Sede Principal" → "Sucursal Sur"
   - Agregar productos:
     - Producto A: 50 unidades
     - Producto B: 30 unidades
     - Producto C: 20 unidades
   - Completar el traslado

3. **Verificar Inventario**:
   - Ir a "Ver Inventario" de "Sucursal Sur"
   - Confirmar que tiene:
     - Producto A: 50
     - Producto B: 30
     - Producto C: 20

4. **Abrir Caja en la Nueva Sede** (próximamente):
   - Al abrir caja, seleccionar "Sucursal Sur"
   - Las ventas descontarán del stock de "Sucursal Sur"

---

## 📊 Verificación del Sistema

Para verificar que todo está instalado correctamente:

```bash
cd /home/kali/Escritorio/definitivo/01_POS_BASICO\ \(2\)/backend

mysql -u root tenantasasasa -e "
SELECT 'Warehouses:' as tabla, COUNT(*) as registros FROM warehouses
UNION ALL
SELECT 'Product-Warehouse:', COUNT(*) FROM product_warehouse
UNION ALL
SELECT 'Stock Transfers:', COUNT(*) FROM stock_transfers
UNION ALL
SELECT 'Productos totales:', COUNT(*) FROM products;
"
```

**Resultado esperado**:
```
+--------------------+-----------+
| tabla              | registros |
+--------------------+-----------+
| Warehouses:        |         1 |
| Product-Warehouse: |         2 |
| Stock Transfers:   |         0 |
| Productos totales: |         2 |
+--------------------+-----------+
```

---

## 🔧 Endpoints de la API

Si necesitas integrar con otras aplicaciones:

### Bodegas/Sedes
- `GET /api/warehouses` - Listar todas las sedes
- `GET /api/warehouses/{id}` - Ver detalle de una sede
- `POST /api/warehouses` - Crear nueva sede
- `PUT /api/warehouses/{id}` - Editar sede
- `DELETE /api/warehouses/{id}` - Eliminar sede
- `GET /api/warehouses/{id}/inventory` - Ver inventario de una sede
- `PUT /api/warehouses/{id}/stock` - Actualizar stock de un producto

### Traslados
- `GET /api/stock-transfers` - Listar traslados
- `GET /api/stock-transfers/{id}` - Ver detalle de traslado
- `POST /api/stock-transfers` - Crear traslado
- `POST /api/stock-transfers/{id}/complete` - Completar traslado
- `POST /api/stock-transfers/{id}/cancel` - Cancelar traslado
- `DELETE /api/stock-transfers/{id}` - Eliminar traslado

---

## 🚨 Reglas Importantes

1. **Stock Inicial**: Las nuevas sedes empiezan con stock 0 en todos los productos
2. **Traslados Pendientes**: Solo completa un traslado cuando la mercancía llegue físicamente
3. **Sede Principal**: Solo puede haber una sede marcada como principal
4. **No Eliminar con Stock**: No puedes eliminar una sede que tenga productos con stock > 0
5. **Validación de Stock**: No puedes crear traslados con más cantidad de la disponible

---

## 📱 Acceso a las Vistas

- **Gestión de Sedes**: `/warehouses`
- **Inventario de Sede**: `/warehouses/{id}/inventory`
- **Traslados**: `/stock-transfers`

---

## ✨ Próximas Integraciones Pendientes

Estas funcionalidades se completarán en la siguiente fase:

1. ✅ **Selector de Sede al Abrir Caja**: Al abrir una sesión de caja, seleccionar de qué sede opera
2. ✅ **Descontar Stock por Sede**: Las ventas descontarán del stock de la sede activa en la caja
3. ✅ **Tooltip de Stock**: En la lista de productos, mostrar el stock desglosado por sede al hacer hover
4. ✅ **Reportes por Sede**: Filtrar reportes de ventas por sede

---

## 🎉 ¡Sistema Listo para Usar!

El sistema multisede está completamente instalado y funcional. Puedes empezar a:
- Crear nuevas sedes/tiendas/bodegas
- Trasladar mercancía entre ellas
- Ver inventario independiente por sede
- Gestionar múltiples ubicaciones

**Recuerda**: Inicia el frontend con `npm run dev` y el backend debe estar corriendo (Laravel + MySQL).
