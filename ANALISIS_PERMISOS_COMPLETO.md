# 📋 ANÁLISIS COMPLETO DE PERMISOS Y ROLES - SISTEMA POS

## 🎯 OBJETIVO
Reestructurar completamente el sistema de roles y permisos para que refleje las **funcionalidades REALES** del sistema, eliminando permisos ficticios y agregando las acciones granulares que realmente existen en cada módulo.

---

## 📊 MÓDULOS Y FUNCIONALIDADES REALES IDENTIFICADAS

### 🏠 1. DASHBOARD
**Ubicación**: `src/components/DashboardView.vue`

**Funcionalidades detectadas**:
- ✅ Ver dashboard principal con métricas
- ✅ Ver gráficos de ventas
- ✅ Ver productos con stock bajo
- ✅ Ver últimas ventas
- ✅ Ver actividad reciente

**Permisos propuestos**:
```javascript
{
  id: 'dashboard',
  name: 'Dashboard',
  permissions: [
    { id: 'dashboard.view', name: 'Ver Dashboard', description: 'Acceder al panel principal' },
    { id: 'dashboard.analytics', name: 'Ver Analíticas', description: 'Acceder a gráficos y estadísticas' }
  ]
}
```

---

### 💰 2. PUNTO DE VENTA (POS)
**Ubicación**: `src/components/PosView.vue`

**Funcionalidades detectadas**:
- ✅ Agregar productos al carrito
- ✅ Eliminar productos del carrito
- ✅ Modificar cantidades
- ✅ Aplicar descuentos
- ✅ Cambiar precio de venta
- ✅ Procesar venta
- ✅ Cancelar venta
- ✅ Seleccionar cliente
- ✅ Crear cliente rápido
- ✅ Seleccionar método de pago
- ✅ Generar ticket/factura
- ✅ Cambiar sede/bodega activa

**Permisos propuestos**:
```javascript
{
  id: 'pos',
  name: 'Punto de Venta',
  permissions: [
    { id: 'pos.access', name: 'Acceder al POS', description: 'Usar el punto de venta' },
    { id: 'pos.create_sale', name: 'Crear Ventas', description: 'Registrar ventas' },
    { id: 'pos.apply_discount', name: 'Aplicar Descuentos', description: 'Modificar precios y descuentos' },
    { id: 'pos.change_price', name: 'Cambiar Precios', description: 'Modificar precio de venta temporal' },
    { id: 'pos.cancel_sale', name: 'Cancelar Ventas', description: 'Anular transacciones en proceso' },
    { id: 'pos.view_history', name: 'Ver Historial', description: 'Consultar ventas anteriores' },
    { id: 'pos.print', name: 'Imprimir Tickets', description: 'Generar comprobantes' },
    { id: 'pos.select_warehouse', name: 'Cambiar Sede', description: 'Seleccionar bodega activa' }
  ]
}
```

---

### 📄 3. FACTURAS
**Ubicación**: `src/components/InvoicesView.vue`

**Funcionalidades detectadas**:
- ✅ Ver lista de facturas
- ✅ Crear factura
- ✅ Ver detalle de factura
- ✅ Imprimir factura
- ✅ Enviar factura por email
- ✅ Anular factura
- ✅ Filtrar facturas

**Permisos propuestos**:
```javascript
{
  id: 'invoices',
  name: 'Facturas',
  permissions: [
    { id: 'invoices.view', name: 'Ver Facturas', description: 'Acceder a la lista de facturas' },
    { id: 'invoices.create', name: 'Crear Facturas', description: 'Generar nuevas facturas' },
    { id: 'invoices.view_detail', name: 'Ver Detalle', description: 'Ver información completa de factura' },
    { id: 'invoices.print', name: 'Imprimir', description: 'Generar impresión de facturas' },
    { id: 'invoices.email', name: 'Enviar por Email', description: 'Enviar facturas electrónicas' },
    { id: 'invoices.cancel', name: 'Anular Facturas', description: 'Cancelar facturas emitidas' }
  ]
}
```

---

### ↩️ 4. DEVOLUCIONES
**Ubicación**: `src/components/ReturnsManagementView.vue`

**Funcionalidades detectadas**:
- ✅ Ver devoluciones
- ✅ Crear devolución
- ✅ Aprobar devolución
- ✅ Rechazar devolución
- ✅ Ver detalle de devolución
- ✅ Generar reembolso

**Permisos propuestos**:
```javascript
{
  id: 'returns',
  name: 'Devoluciones',
  permissions: [
    { id: 'returns.view', name: 'Ver Devoluciones', description: 'Acceder a gestión de devoluciones' },
    { id: 'returns.create', name: 'Crear Devolución', description: 'Registrar nueva devolución' },
    { id: 'returns.approve', name: 'Aprobar Devolución', description: 'Autorizar devoluciones' },
    { id: 'returns.reject', name: 'Rechazar Devolución', description: 'Denegar solicitudes' },
    { id: 'returns.refund', name: 'Procesar Reembolso', description: 'Generar devolución de dinero' }
  ]
}
```

---

### 📦 5. PRODUCTOS
**Ubicación**: `src/components/ProductsView_professional.vue`

**Funcionalidades detectadas**:
- ✅ Ver lista de productos
- ✅ Crear producto
- ✅ Editar producto
- ✅ Eliminar producto (soft delete)
- ✅ Activar/Desactivar producto
- ✅ Ver detalle de producto
- ✅ Exportar productos
- ✅ Importar productos (masivo)
- ✅ Cambiar imagen de producto
- ✅ Gestionar precios múltiples
- ✅ Ver historial de cambios

**Permisos propuestos**:
```javascript
{
  id: 'products',
  name: 'Productos',
  permissions: [
    { id: 'products.view', name: 'Ver Productos', description: 'Acceder al catálogo' },
    { id: 'products.create', name: 'Crear Productos', description: 'Agregar nuevos productos' },
    { id: 'products.edit', name: 'Editar Productos', description: 'Modificar información' },
    { id: 'products.delete', name: 'Eliminar Productos', description: 'Borrar productos' },
    { id: 'products.toggle_status', name: 'Activar/Desactivar', description: 'Cambiar estado de productos' },
    { id: 'products.view_cost', name: 'Ver Precio de Costo', description: 'Ver costos y márgenes' },
    { id: 'products.export', name: 'Exportar Productos', description: 'Exportar catálogo' },
    { id: 'products.import', name: 'Importar Productos', description: 'Carga masiva' },
    { id: 'products.manage_images', name: 'Gestionar Imágenes', description: 'Subir/modificar fotos' }
  ]
}
```

---

### 🏷️ 6. CATEGORÍAS
**Ubicación**: `src/components/CategoriesView.vue`

**Funcionalidades detectadas**:
- ✅ Ver categorías
- ✅ Crear categoría
- ✅ Editar categoría
- ✅ Eliminar categoría
- ✅ Activar/Desactivar categoría
- ✅ Ver productos por categoría
- ✅ Reordenar categorías

**Permisos propuestos**:
```javascript
{
  id: 'categories',
  name: 'Categorías',
  permissions: [
    { id: 'categories.view', name: 'Ver Categorías', description: 'Acceder a categorías' },
    { id: 'categories.create', name: 'Crear Categorías', description: 'Agregar nuevas' },
    { id: 'categories.edit', name: 'Editar Categorías', description: 'Modificar existentes' },
    { id: 'categories.delete', name: 'Eliminar Categorías', description: 'Borrar categorías' },
    { id: 'categories.toggle_status', name: 'Activar/Desactivar', description: 'Cambiar estado' }
  ]
}
```

---

### 📊 7. GESTIÓN DE STOCK
**Ubicación**: `src/components/InventoryView_professional.vue`

**Funcionalidades detectadas**:
- ✅ Ver inventario
- ✅ Ajustar stock
- ✅ Crear movimiento de inventario
- ✅ Ver historial de movimientos
- ✅ Transferir entre bodegas
- ✅ Ver alertas de stock bajo
- ✅ Generar reporte de inventario

**Permisos propuestos**:
```javascript
{
  id: 'stock',
  name: 'Gestión de Stock',
  permissions: [
    { id: 'stock.view', name: 'Ver Inventario', description: 'Acceder a control de stock' },
    { id: 'stock.adjust', name: 'Ajustar Stock', description: 'Realizar ajustes manuales' },
    { id: 'stock.create_movement', name: 'Crear Movimientos', description: 'Registrar entradas/salidas' },
    { id: 'stock.view_history', name: 'Ver Historial', description: 'Consultar movimientos anteriores' },
    { id: 'stock.transfer', name: 'Transferir Stock', description: 'Mover entre bodegas' },
    { id: 'stock.view_cost', name: 'Ver Valores', description: 'Ver costo del inventario' }
  ]
}
```

---

### 🤖 8. INVENTARIO IA (PREMIUM/ENTERPRISE)
**Ubicación**: `src/components/IntelligentInventoryView_Simple.vue`

**Funcionalidades detectadas**:
- ✅ Ver predicciones de demanda
- ✅ Ver análisis de tendencias
- ✅ Recibir recomendaciones de compra
- ✅ Ver productos de rotación lenta
- ✅ Ver pronósticos de ventas

**Permisos propuestos**:
```javascript
{
  id: 'intelligent_inventory',
  name: 'Inventario Inteligente',
  isPremium: true, // Solo Premium/Enterprise
  permissions: [
    { id: 'intelligent_inventory.view', name: 'Ver Análisis IA', description: 'Acceder a análisis inteligente' },
    { id: 'intelligent_inventory.predictions', name: 'Ver Predicciones', description: 'Acceder a pronósticos' },
    { id: 'intelligent_inventory.recommendations', name: 'Ver Recomendaciones', description: 'Sugerencias de compra' }
  ]
}
```

---

### 🏢 9. MULTISEDE / GESTIÓN DE SEDES (PREMIUM/ENTERPRISE)
**Ubicación**: `src/components/WarehousesView_MasterDetail.vue`

**Funcionalidades detectadas**:
- ✅ Ver sedes
- ✅ Crear sede
- ✅ Editar sede
- ✅ Eliminar sede
- ✅ Activar/Desactivar sede
- ✅ Ver inventario por sede
- ✅ Crear traslado entre sedes
- ✅ Aprobar traslado
- ✅ Ver historial de traslados
- ✅ Designar sede principal

**Permisos propuestos**:
```javascript
{
  id: 'warehouses',
  name: 'Gestión de Sedes',
  isPremium: true, // Solo Premium/Enterprise
  permissions: [
    { id: 'warehouses.view', name: 'Ver Sedes', description: 'Acceder a gestión de sedes' },
    { id: 'warehouses.create', name: 'Crear Sedes', description: 'Registrar nuevas sedes' },
    { id: 'warehouses.edit', name: 'Editar Sedes', description: 'Modificar información' },
    { id: 'warehouses.delete', name: 'Eliminar Sedes', description: 'Borrar sedes' },
    { id: 'warehouses.view_inventory', name: 'Ver Inventario por Sede', description: 'Stock por ubicación' },
    { id: 'warehouses.create_transfer', name: 'Crear Traslados', description: 'Mover stock entre sedes' },
    { id: 'warehouses.approve_transfer', name: 'Aprobar Traslados', description: 'Autorizar movimientos' },
    { id: 'warehouses.manage_main', name: 'Gestionar Sede Principal', description: 'Designar sede principal' }
  ]
}
```

---

### 👥 10. CLIENTES
**Ubicación**: `src/components/CustomersView_clean.vue`

**Funcionalidades detectadas**:
- ✅ Ver lista de clientes
- ✅ Crear cliente
- ✅ Editar cliente
- ✅ Eliminar cliente
- ✅ Activar/Desactivar cliente
- ✅ Ver historial de compras
- ✅ Ver saldo de cliente
- ✅ Exportar clientes
- ✅ Ver estadísticas del cliente

**Permisos propuestos**:
```javascript
{
  id: 'customers',
  name: 'Clientes',
  permissions: [
    { id: 'customers.view', name: 'Ver Clientes', description: 'Acceder a lista de clientes' },
    { id: 'customers.create', name: 'Crear Clientes', description: 'Registrar nuevos clientes' },
    { id: 'customers.edit', name: 'Editar Clientes', description: 'Modificar datos' },
    { id: 'customers.delete', name: 'Eliminar Clientes', description: 'Borrar clientes' },
    { id: 'customers.toggle_status', name: 'Activar/Desactivar', description: 'Cambiar estado' },
    { id: 'customers.view_history', name: 'Ver Historial', description: 'Ver compras anteriores' },
    { id: 'customers.view_balance', name: 'Ver Saldo', description: 'Consultar cuentas' },
    { id: 'customers.export', name: 'Exportar Clientes', description: 'Exportar base de datos' }
  ]
}
```

---

### 💳 11. CUENTAS POR COBRAR (PREMIUM/ENTERPRISE)
**Ubicación**: `src/components/AccountsReceivableView.vue`

**Funcionalidades detectadas**:
- ✅ Ver cuentas por cobrar
- ✅ Registrar pago
- ✅ Ver historial de pagos
- ✅ Enviar recordatorio de pago
- ✅ Generar reporte de cartera
- ✅ Ver clientes morosos
- ✅ Configurar términos de crédito

**Permisos propuestos**:
```javascript
{
  id: 'accounts_receivable',
  name: 'Cuentas por Cobrar',
  isPremium: true, // Solo Premium/Enterprise
  permissions: [
    { id: 'accounts_receivable.view', name: 'Ver Cuentas', description: 'Acceder a cartera' },
    { id: 'accounts_receivable.register_payment', name: 'Registrar Pagos', description: 'Cobrar cuentas' },
    { id: 'accounts_receivable.view_history', name: 'Ver Historial', description: 'Pagos anteriores' },
    { id: 'accounts_receivable.send_reminder', name: 'Enviar Recordatorios', description: 'Notificar pagos pendientes' },
    { id: 'accounts_receivable.manage_credit', name: 'Gestionar Crédito', description: 'Configurar términos' },
    { id: 'accounts_receivable.reports', name: 'Reportes de Cartera', description: 'Análisis de cobros' }
  ]
}
```

---

### 🏭 12. PROVEEDORES
**Ubicación**: `src/components/PurchaseOrdersView_MasterDetail.vue`

**Funcionalidades detectadas**:
- ✅ Ver proveedores
- ✅ Crear proveedor
- ✅ Editar proveedor
- ✅ Eliminar proveedor
- ✅ Activar/Desactivar proveedor
- ✅ Ver órdenes de compra
- ✅ Crear orden de compra
- ✅ Aprobar orden de compra
- ✅ Recepcionar orden
- ✅ Ver historial de compras

**Permisos propuestos**:
```javascript
{
  id: 'suppliers',
  name: 'Proveedores',
  permissions: [
    { id: 'suppliers.view', name: 'Ver Proveedores', description: 'Acceder a proveedores' },
    { id: 'suppliers.create', name: 'Crear Proveedores', description: 'Registrar proveedores' },
    { id: 'suppliers.edit', name: 'Editar Proveedores', description: 'Modificar datos' },
    { id: 'suppliers.delete', name: 'Eliminar Proveedores', description: 'Borrar proveedores' },
    { id: 'suppliers.toggle_status', name: 'Activar/Desactivar', description: 'Cambiar estado' },
    { id: 'suppliers.create_order', name: 'Crear Órdenes de Compra', description: 'Generar OC' },
    { id: 'suppliers.approve_order', name: 'Aprobar Órdenes', description: 'Autorizar compras' },
    { id: 'suppliers.receive_order', name: 'Recepcionar Órdenes', description: 'Confirmar recepción' },
    { id: 'suppliers.view_history', name: 'Ver Historial', description: 'Compras anteriores' }
  ]
}
```

---

### 👤 13. USUARIOS
**Ubicación**: `src/components/UsersManagementView_WORKING.vue`

**Funcionalidades detectadas**:
- ✅ Ver usuarios
- ✅ Crear usuario
- ✅ Editar usuario
- ✅ Eliminar usuario
- ✅ Activar/Desactivar usuario
- ✅ Cambiar contraseña
- ✅ Asignar rol
- ✅ Ver roles
- ✅ Crear rol
- ✅ Editar rol
- ✅ Eliminar rol
- ✅ Gestionar permisos

**Permisos propuestos**:
```javascript
{
  id: 'users',
  name: 'Usuarios',
  permissions: [
    { id: 'users.view', name: 'Ver Usuarios', description: 'Acceder a gestión de usuarios' },
    { id: 'users.create', name: 'Crear Usuarios', description: 'Registrar empleados' },
    { id: 'users.edit', name: 'Editar Usuarios', description: 'Modificar datos' },
    { id: 'users.delete', name: 'Eliminar Usuarios', description: 'Borrar usuarios' },
    { id: 'users.toggle_status', name: 'Activar/Desactivar', description: 'Cambiar estado de acceso' },
    { id: 'users.change_password', name: 'Cambiar Contraseñas', description: 'Resetear acceso' },
    { id: 'users.manage_roles', name: 'Gestionar Roles', description: 'Crear y editar roles' },
    { id: 'users.assign_permissions', name: 'Asignar Permisos', description: 'Configurar accesos' }
  ]
}
```

---

### 💰 14. PANEL ADMIN (CAJA)
**Ubicación**: `src/components/CashAdminView.vue`

**Funcionalidades detectadas**:
- ✅ Ver caja
- ✅ Abrir caja
- ✅ Cerrar caja
- ✅ Registrar ingreso
- ✅ Registrar egreso
- ✅ Ver movimientos
- ✅ Cuadrar caja
- ✅ Ver reporte de caja
- ✅ Ver historial de turnos

**Permisos propuestos**:
```javascript
{
  id: 'cash_register',
  name: 'Panel Admin (Caja)',
  permissions: [
    { id: 'cash_register.view', name: 'Ver Caja', description: 'Acceder al módulo' },
    { id: 'cash_register.open', name: 'Abrir Caja', description: 'Iniciar turno' },
    { id: 'cash_register.close', name: 'Cerrar Caja', description: 'Finalizar y cuadrar' },
    { id: 'cash_register.register_income', name: 'Registrar Ingresos', description: 'Agregar entradas' },
    { id: 'cash_register.register_expense', name: 'Registrar Egresos', description: 'Agregar salidas' },
    { id: 'cash_register.view_movements', name: 'Ver Movimientos', description: 'Consultar transacciones' },
    { id: 'cash_register.reports', name: 'Reportes de Caja', description: 'Generar reportes' }
  ]
}
```

---

### 💸 15. GASTOS OPERATIVOS
**Ubicación**: `src/views/ExpensesManager.vue`

**Funcionalidades detectadas**:
- ✅ Ver gastos
- ✅ Crear gasto
- ✅ Editar gasto
- ✅ Eliminar gasto
- ✅ Ver categorías de gastos
- ✅ Crear categoría de gasto
- ✅ Ver reportes de gastos
- ✅ Exportar gastos

**Permisos propuestos**:
```javascript
{
  id: 'expenses',
  name: 'Gastos Operativos',
  permissions: [
    { id: 'expenses.view', name: 'Ver Gastos', description: 'Acceder a gastos' },
    { id: 'expenses.create', name: 'Crear Gastos', description: 'Registrar nuevos gastos' },
    { id: 'expenses.edit', name: 'Editar Gastos', description: 'Modificar gastos' },
    { id: 'expenses.delete', name: 'Eliminar Gastos', description: 'Borrar gastos' },
    { id: 'expenses.manage_categories', name: 'Gestionar Categorías', description: 'Administrar tipos de gastos' },
    { id: 'expenses.reports', name: 'Reportes de Gastos', description: 'Análisis de gastos' },
    { id: 'expenses.export', name: 'Exportar Gastos', description: 'Exportar datos' }
  ]
}
```

---

### 📈 16. REPORTES
**Ubicación**: `src/components/ReportsView.vue`

**Funcionalidades detectadas**:
- ✅ Ver reportes
- ✅ Reporte de ventas
- ✅ Reporte de inventario
- ✅ Reportes financieros
- ✅ Reporte de clientes
- ✅ Exportar reportes (Excel/PDF)
- ✅ Programar reportes automáticos

**Permisos propuestos**:
```javascript
{
  id: 'reports',
  name: 'Reportes',
  permissions: [
    { id: 'reports.view', name: 'Ver Reportes', description: 'Acceder a reportes' },
    { id: 'reports.sales', name: 'Reporte de Ventas', description: 'Estadísticas de ventas' },
    { id: 'reports.inventory', name: 'Reporte de Inventario', description: 'Estado del stock' },
    { id: 'reports.financial', name: 'Reportes Financieros', description: 'Análisis financiero' },
    { id: 'reports.customers', name: 'Reporte de Clientes', description: 'Análisis de clientes' },
    { id: 'reports.export', name: 'Exportar Reportes', description: 'Excel/PDF' },
    { id: 'reports.schedule', name: 'Programar Reportes', description: 'Automatizar generación' }
  ]
}
```

---

### ⚙️ 17. CONFIGURACIÓN
**Ubicación**: `src/components/SettingsView.vue`

**Funcionalidades detectadas**:
- ✅ Ver configuración
- ✅ Editar configuración general
- ✅ Gestionar datos de la empresa
- ✅ Configurar impuestos
- ✅ Configurar métodos de pago
- ✅ Configurar impresoras
- ✅ Configurar notificaciones
- ✅ Gestionar respaldos

**Permisos propuestos**:
```javascript
{
  id: 'settings',
  name: 'Configuración',
  permissions: [
    { id: 'settings.view', name: 'Ver Configuración', description: 'Acceder a configuración' },
    { id: 'settings.edit_general', name: 'Editar Configuración General', description: 'Modificar ajustes' },
    { id: 'settings.manage_business', name: 'Gestionar Empresa', description: 'Datos de la empresa' },
    { id: 'settings.manage_taxes', name: 'Configurar Impuestos', description: 'Administrar tasas' },
    { id: 'settings.manage_payments', name: 'Métodos de Pago', description: 'Configurar pagos' },
    { id: 'settings.manage_printers', name: 'Configurar Impresoras', description: 'Setup de impresión' },
    { id: 'settings.manage_notifications', name: 'Notificaciones', description: 'Alertas del sistema' },
    { id: 'settings.manage_backups', name: 'Gestionar Respaldos', description: 'Backup y restauración' }
  ]
}
```

---

## 🎯 RESUMEN DE PERMISOS TOTALES

### Por Módulo:
1. **Dashboard**: 2 permisos
2. **POS**: 8 permisos
3. **Facturas**: 6 permisos
4. **Devoluciones**: 5 permisos
5. **Productos**: 9 permisos
6. **Categorías**: 5 permisos
7. **Stock**: 6 permisos
8. **Inventario IA** ⭐: 3 permisos (Premium/Enterprise)
9. **Multisede** ⭐: 8 permisos (Premium/Enterprise)
10. **Clientes**: 8 permisos
11. **Cuentas por Cobrar** ⭐: 6 permisos (Premium/Enterprise)
12. **Proveedores**: 9 permisos
13. **Usuarios**: 8 permisos
14. **Caja**: 7 permisos
15. **Gastos**: 7 permisos
16. **Reportes**: 7 permisos
17. **Configuración**: 8 permisos

**TOTAL**: **116 permisos granulares** (vs 65 anteriores mal definidos)

---

## 🔐 MÓDULOS EXCLUSIVOS POR PLAN

### 🆓 FREE TRIAL (Todos los módulos básicos sin límites premium)
- Dashboard
- POS
- Facturas
- Devoluciones
- Productos
- Categorías
- Stock
- Clientes
- Proveedores
- Usuarios
- Caja
- Gastos
- Reportes
- Configuración

### 💎 PREMIUM (Agrega funcionalidades avanzadas)
- ✅ Todo lo del Free Trial
- **+ Inventario IA**: Predicciones y análisis inteligente
- **+ Cuentas por Cobrar**: Gestión de cartera
- **+ Multisede Básico**: Hasta 3 sedes

### 🏆 ENTERPRISE (Sin límites)
- ✅ Todo lo de Premium
- **+ Multisede Ilimitado**: Sin límite de sedes
- **+ API Access**: Integraciones personalizadas
- **+ Soporte Prioritario**
- **+ Reportes Avanzados**

---

## 📋 IMPLEMENTACIÓN EN SIDEBAR

El archivo `Sidebar.vue` ya tiene la estructura correcta pero hay que agregar validación de planes:

```vue
<!-- MULTISEDE (Premium/Enterprise Only) -->
<div v-if="showMultisede" class="mt-7 px-4">
  <!-- Solo visible si: tenantPlan === 'premium' || tenantPlan === 'enterprise' -->
</div>

<!-- Cuentas por Cobrar (Premium/Enterprise) -->
<div v-if="hasModuleAccess('customers') && isCreditiendaEnabled && ['premium', 'enterprise'].includes(appStore.tenantPlan)">
  <!-- Ya implementado correctamente -->
</div>
```

**Lógica necesaria**:
```javascript
const showMultisede = computed(() => {
  const plan = appStore.tenantPlan
  return (plan === 'premium' || plan === 'enterprise') && hasModuleAccess('warehouses')
})

const showInventoryIA = computed(() => {
  const plan = appStore.tenantPlan
  return (plan === 'premium' || plan === 'enterprise') && hasModuleAccess('intelligent_inventory')
})
```

---

## 🎨 DISEÑO NUEVO PARA USUARIOS/ROLES

**Problemas del diseño actual**:
- ❌ Demasiado colorido y "infantil"
- ❌ No sigue el sistema de diseño SaaS del archivo `.instructions.md`
- ❌ Permisos desorganizados y mal agrupados
- ❌ Falta claridad visual en la jerarquía

**Nuevo diseño debe tener**:
- ✅ Paleta sobria: slate-900, zinc, white (siguiendo `.instructions.md`)
- ✅ KPIs con glassmorphism
- ✅ Tabs limpios y minimalistas
- ✅ Permisos agrupados por categoría visual clara
- ✅ Checkboxes grandes y fáciles de usar
- ✅ Botón "Marcar todos" por módulo
- ✅ Contador de permisos seleccionados en tiempo real
- ✅ Badges de estado profesionales
- ✅ Sin iconos innecesarios en headers

---

## ✅ SIGUIENTES PASOS

1. **Actualizar `permissionsModules` en UsersManagementView_WORKING.vue** con la estructura completa de 116 permisos
2. **Crear nuevo diseño SaaS** siguiendo `.instructions.md`
3. **Implementar filtrado de sidebar** por plan de suscripción
4. **Actualizar backend** `PermissionController.php` con permisos reales
5. **Migrar base de datos** para actualizar roles existentes
6. **Documentar roles predefinidos** (Administrador, Gerente, Cajero, Vendedor, etc.)

---

**Generado**: 5 de diciembre de 2025  
**Sistema**: 105 POS - Sistema Empresarial  
**Versión**: 2.0 (Reestructuración de Permisos)
