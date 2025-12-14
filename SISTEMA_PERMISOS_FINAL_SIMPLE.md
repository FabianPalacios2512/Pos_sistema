# 🎯 Sistema de Permisos ULTRA SIMPLE - POS Empresarial

## ✅ Filosofía Final

**1 módulo = 1 permiso**

Si un usuario tiene permiso para ver el módulo → Puede hacer TODO lo que ese módulo permite.
Si NO tiene permiso → El módulo NO aparece en el menú.

---

## 📊 Lista de Permisos (17 Total)

### 1. Dashboard
**ID**: `dashboard.view`
**Descripción**: Panel principal con estadísticas y KPIs

### 2. Punto de Venta (POS)
**ID**: `pos.view`
**Descripción**: Sistema de ventas y cobros completo

### 3. Facturas
**ID**: `invoices.view`
**Descripción**: Gestión de facturas y documentos

### 4. Devoluciones
**ID**: `returns.view`
**Descripción**: Gestión de devoluciones y reembolsos

### 5. Productos
**ID**: `products.view`
**Descripción**: Catálogo de productos y servicios

### 6. Categorías
**ID**: `categories.view`
**Descripción**: Organización de productos por categorías

### 7. Gestión de Stock
**ID**: `stock.view`
**Descripción**: Control de inventario y movimientos

### 8. Inventario IA (Premium)
**ID**: `intelligent_inventory.view`
**Descripción**: Análisis inteligente de inventario con IA

### 9. Multisede / Bodegas (Premium)
**ID**: `warehouses.view`
**Descripción**: Gestión de múltiples sedes y traslados

### 10. Clientes
**ID**: `customers.view`
**Descripción**: Base de datos de clientes

### 11. Cuentas por Cobrar (Premium)
**ID**: `accounts_receivable.view`
**Descripción**: Gestión de créditos y cobranzas

### 12. Proveedores
**ID**: `suppliers.view`
**Descripción**: Gestión de proveedores y compras

### 13. Usuarios y Roles
**ID**: `users.view`
**Descripción**: Administración de usuarios y permisos

### 14. Caja (Administración)
**ID**: `cash_register.view`
**Descripción**: Control de turnos de caja

### 15. Gastos Operativos
**ID**: `expenses.view`
**Descripción**: Registro y control de gastos

### 16. Reportes
**ID**: `reports.view`
**Descripción**: Informes y estadísticas del negocio

### 17. Configuración
**ID**: `settings.view`
**Descripción**: Configuración general del sistema

---

## 🎭 Ejemplos de Roles

### 👑 Administrador
**Permisos**: TODOS los 17 módulos
```json
[
  "dashboard.view",
  "pos.view",
  "invoices.view",
  "returns.view",
  "products.view",
  "categories.view",
  "stock.view",
  "intelligent_inventory.view",
  "warehouses.view",
  "customers.view",
  "accounts_receivable.view",
  "suppliers.view",
  "users.view",
  "cash_register.view",
  "expenses.view",
  "reports.view",
  "settings.view"
]
```

---

### 🟢 Vendedor
**Permisos**: Solo lo necesario para vender
```json
[
  "pos.view",
  "invoices.view",
  "customers.view",
  "products.view"
]
```

**Puede:**
- ✅ Usar el POS para vender
- ✅ Ver facturas (sus propias ventas)
- ✅ Agregar clientes
- ✅ Ver catálogo de productos

**NO puede:**
- ❌ Ver Dashboard
- ❌ Hacer devoluciones (no tiene `returns.view`)
- ❌ Editar productos (puede verlos pero no modificarlos)
- ❌ Ver reportes, gastos, configuración

---

### 🟡 Cajero
**Permisos**: Vender y gestionar caja
```json
[
  "pos.view",
  "invoices.view",
  "returns.view",
  "customers.view",
  "cash_register.view"
]
```

**Puede:**
- ✅ Usar el POS
- ✅ Ver facturas
- ✅ Hacer devoluciones
- ✅ Abrir/cerrar turnos de caja
- ✅ Agregar clientes

**NO puede:**
- ❌ Ver Dashboard
- ❌ Editar productos
- ❌ Ver reportes financieros
- ❌ Crear usuarios

---

### 🔵 Gerente
**Permisos**: Todo menos configuración crítica
```json
[
  "dashboard.view",
  "pos.view",
  "invoices.view",
  "returns.view",
  "products.view",
  "categories.view",
  "stock.view",
  "customers.view",
  "suppliers.view",
  "cash_register.view",
  "expenses.view",
  "reports.view"
]
```

**Puede:**
- ✅ Ver Dashboard completo
- ✅ Usar POS
- ✅ Gestionar productos, categorías, stock
- ✅ Ver reportes financieros
- ✅ Ver gastos y caja

**NO puede:**
- ❌ Crear usuarios (`users.view`)
- ❌ Cambiar configuración del sistema (`settings.view`)
- ❌ Acceder a módulos premium sin plan

---

### 🟣 Contador
**Permisos**: Solo visualización financiera
```json
[
  "dashboard.view",
  "invoices.view",
  "expenses.view",
  "reports.view",
  "customers.view",
  "suppliers.view"
]
```

**Puede:**
- ✅ Ver Dashboard
- ✅ Ver facturas (sin crear)
- ✅ Ver gastos
- ✅ Exportar reportes
- ✅ Ver clientes y proveedores

**NO puede:**
- ❌ Usar el POS (no tiene `pos.view`)
- ❌ Editar productos
- ❌ Crear gastos (puede verlos pero no editarlos)

---

## 🔧 Implementación en Código

### Verificar Acceso al Módulo
```javascript
function hasModuleAccess(module) {
  const permissions = user.value.role?.permissions || [];
  return permissions.includes(`${module}.view`);
}

// Ejemplo de uso:
if (!hasModuleAccess('pos')) {
  router.push('/dashboard'); // Redirigir si no tiene acceso
}
```

### Ocultar Menú en Sidebar
```vue
<!-- Solo mostrar "Punto de Venta" si tiene permiso -->
<div v-if="hasModuleAccess('pos')">
  <router-link to="/pos">
    Punto de Venta
  </router-link>
</div>

<!-- Solo mostrar "Configuración" si tiene permiso -->
<div v-if="hasModuleAccess('settings')">
  <router-link to="/settings">
    Configuración
  </router-link>
</div>
```

### Redirección Inteligente en Login
```javascript
const permissionRoutes = [
  { permission: 'dashboard.view', route: '/dashboard' },
  { permission: 'pos.view', route: '/pos' },
  { permission: 'invoices.view', route: '/invoices' },
  { permission: 'products.view', route: '/products' },
  { permission: 'customers.view', route: '/customers' },
  { permission: 'reports.view', route: '/reports' },
  { permission: 'users.view', route: '/users' },
  { permission: 'cash_register.view', route: '/cash-admin' },
  { permission: 'expenses.view', route: '/expenses' },
  { permission: 'settings.view', route: '/settings' }
];

// Redirigir al primer módulo que tenga permiso
for (const { permission, route } of permissionRoutes) {
  if (userPermissions.includes(permission)) {
    router.push(route);
    break;
  }
}
```

---

## ✅ Ventajas del Sistema Final

### ✅ Ultra Simple
- Solo 17 permisos (1 por módulo)
- Fácil de entender para cualquier usuario
- Sin confusiones de sub-permisos

### ✅ Escalable
- Agregar nuevo módulo = Agregar 1 permiso
- Estructura clara y consistente

### ✅ Práctico
- Control de visibilidad por módulo
- Acciones críticas se validan en backend

### ✅ Mantenible
- Menos código
- Menos errores
- Más rápido de implementar

---

## 🚫 Lo que NO controlamos en Frontend

❌ **Acciones específicas dentro del módulo:**
- Anular facturas
- Eliminar productos
- Cambiar precios
- Crear devoluciones

✅ **Estas validaciones van en el BACKEND:**
```php
// Ejemplo en backend
if ($user->role->name !== 'Administrador') {
    return response()->json(['error' => 'No autorizado'], 403);
}
```

**Razón**: Las validaciones de acciones críticas en frontend se pueden burlar con DevTools. El backend es la única fuente de verdad para seguridad.

---

## 📝 Migración desde el Sistema Anterior

### Base de Datos
```sql
-- Actualizar tabla roles
-- La columna permissions sigue siendo JSON, pero ahora guarda arrays simples:

-- Antes (ejemplo):
["dashboard.view", "dashboard.analytics", "pos.access", "pos.create_sale", "pos.apply_discount", ...]

-- Ahora (ejemplo):
["dashboard.view", "pos.view", "invoices.view", "customers.view"]
```

### Conversión de Roles Existentes
```javascript
// Script de migración (ejecutar una sola vez)
const oldToNew = {
  'dashboard.view': 'dashboard.view',
  'dashboard.analytics': null, // Eliminar (incluido en dashboard.view)
  'pos.access': 'pos.view',
  'pos.create_sale': null, // Eliminar (incluido en pos.view)
  'pos.apply_discount': null, // Eliminar
  'invoices.view': 'invoices.view',
  'invoices.create': null, // Eliminar
  // ... etc
};

function migratePermissions(oldPermissions) {
  const newPermissions = new Set();
  
  oldPermissions.forEach(perm => {
    const newPerm = oldToNew[perm];
    if (newPerm) {
      newPermissions.add(newPerm);
    }
  });
  
  return Array.from(newPermissions);
}
```

---

## 🎯 Conclusión

Este sistema es **10 veces más simple** que el anterior pero **igual de funcional**:

- **116 permisos complejos** → **17 permisos simples** ✅
- **Acciones críticas en frontend** → **Validación en backend** ✅
- **Confusión de sub-permisos** → **1 módulo = 1 permiso** ✅

**Resultado**: Sistema profesional, fácil de entender y mantener.
