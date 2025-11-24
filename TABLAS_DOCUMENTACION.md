# 📋 DOCUMENTACIÓN DE TABLAS - SISTEMA POS

## ✅ SISTEMA PRINCIPAL (UNIFICADO)

### 🏪 **VENTAS Y FACTURACIÓN**
- **`invoices`** - Facturas principales (134 registros, $33.7M)
  - Tipos: `invoice` (facturas), `quote` (cotizaciones), `credit_note` (notas de crédito)
  - Estados: `draft`, `sent`, `paid`, `overdue`, `cancelled`
  - Usado por: PosView, DashboardView, InvoicesView, reportsService, DashboardController ✅

- **`invoice_items`** - Detalles de productos en facturas (166 registros)
  - Campos: product_id, quantity, unit_price, subtotal, tax_amount, discount_amount
  - Usado por: InvoiceController, InventoryController ✅, DashboardController ✅

### 📦 **PRODUCTOS E INVENTARIO**
- **`products`** - Catálogo de productos
  - Usado por: Todos los módulos ✅
- **`categories`** - Categorías de productos
  - Usado por: Gestión de productos ✅
- **`inventory_movements`** - Movimientos de inventario
  - Usado por: Control de stock ✅

### 👥 **CLIENTES**
- **`customers`** - Base de clientes
  - Usado por: POS, reportes, facturación ✅

### 💰 **DESCUENTOS**
- **`discounts`** - Configuración de descuentos (6 registros)
  - Tipos: percentage, fixed_amount
  - Aplicable a: productos, categorías, clientes ✅
- **`applied_discounts`** - Descuentos aplicados en facturas (6 registros)
  - Relacionado con: invoices ✅

### 🏪 **PROVEEDORES**
- **`suppliers`** - Proveedores
  - Usado por: Gestión de productos ✅

### 👤 **USUARIOS Y PERMISOS**
- **`users`** - Usuarios del sistema
- **`roles`** - Roles y permisos
- **`personal_access_tokens`** - Tokens de autenticación

### ⚙️ **CONFIGURACIÓN**
- **`system_settings`** - Configuración del sistema (1 registro)
- **`payment_methods`** - Métodos de pago disponibles

### 🔔 **ALERTAS Y NOTIFICACIONES**
- **`active_alerts`** - Alertas activas (21 registros)
- **`dismissed_alerts`** - Alertas descartadas (15 registros)
- **`user_notification_views`** - Vistas de notificaciones (2 registros)

## ⚠️ SISTEMA LEGACY (CONSERVADO PARA COMPATIBILIDAD)

### 💀 **VENTAS ANTIGUAS**
- **`sales`** - Ventas del sistema anterior (42 registros, $9.4M)
  - ❌ **NO USAR** para nuevas funcionalidades
  - 🔒 **CONSERVAR** - Puede contener datos históricos importantes
  - Estados: completed, pending, cancelled, refunded, quotation
  - Último uso: Solo por SalesController (mantenido para compatibilidad)

- **`sale_items`** - Items del sistema anterior (139 registros)
  - ❌ **NO USAR** para nuevas funcionalidades
  - 🔒 **CONSERVAR** - Datos históricos vinculados a `sales`

## 🗂️ TABLAS VACÍAS/SISTEMA

### 📋 **TABLAS VACÍAS (SEGURAS PARA LIMPIEZA FUTURA)**
- **`invoice_discounts`** - 0 registros (posiblemente obsoleta)
- **`cache`** - 0 registros (sistema Laravel)
- **`cache_locks`** - 0 registros (sistema Laravel)
- **`failed_jobs`** - 0 registros (sistema Laravel)
- **`jobs`** - 0 registros (sistema Laravel)

### 🔧 **TABLAS DE SISTEMA (ACTIVAS)**
- **`sessions`** - 3 registros (sesiones activas)
- **`migrations`** - Historial de migraciones
- **`password_reset_tokens`** - Tokens de recuperación

## 🎯 CONTROLADORES Y ENDPOINTS

### ✅ **ENDPOINTS UNIFICADOS (USAR SIEMPRE)**
- `/api/invoices` - InvoiceController (facturas y cotizaciones) ✅
- `/api/dashboard/stats` - DashboardController ✅ (actualizado)
- `/api/inventory` - InventoryController ✅ (actualizado)

### ⚠️ **ENDPOINTS LEGACY (MANTENER PARA COMPATIBILIDAD)**
- `/api/sales` - SalesController (solo para compatibilidad)
  - 🔒 NO eliminar hasta migrar datos históricos completamente

## 🚀 SERVICIOS FRONTEND

### ✅ **SERVICIOS UNIFICADOS**
- `invoicesService.js` ✅ - Usa solo `/invoices`
- `reportsService.js` ✅ - Usa `invoicesService`
- `productsService.js` ✅ - Productos e inventario

### 📊 **FLUJO DE DATOS UNIFICADO**
```
POS → invoicesService → /api/invoices → invoices table ✅
Dashboard → reportsService → invoicesService → invoices table ✅
Reportes → reportsService → invoicesService → invoices table ✅
Inventario → InventoryController → invoice_items table ✅
```

## ⚡ ACCIONES COMPLETADAS

1. ✅ **DashboardController** actualizado a tabla `invoices`
2. ✅ **InventoryController** actualizado a tabla `invoice_items`
3. ✅ **invoicesService.js** unificado a endpoints `/invoices`
4. ✅ **Consistencia numérica** verificada
5. ✅ **Todos los reportes** usan la misma fuente de datos

## 🎯 RESULTADO

**SISTEMA 100% UNIFICADO Y CONSISTENTE**
- ✅ Sin duplicaciones activas
- ✅ Números consistentes entre frontend y backend
- ✅ Una sola fuente de verdad: tabla `invoices`
- ✅ Sistema legacy preservado para no perder datos históricos
- ✅ POS comercial listo para usuarios finales

---
*Documento generado: 3 de noviembre de 2025*
*Estado: Sistema completamente unificado y funcional*