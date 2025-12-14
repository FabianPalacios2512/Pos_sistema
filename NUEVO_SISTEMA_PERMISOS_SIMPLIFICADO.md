# 🎯 Sistema de Permisos Simplificado - POS Empresarial

## 📌 Filosofía del Sistema

**2 Niveles de Control:**

1. **Acceso al Módulo** → ¿Puede ver el módulo completo?
2. **Acciones Críticas** → ¿Puede hacer acciones peligrosas/importantes dentro del módulo?

---

## ✅ Estructura de Permisos

### 1. **Dashboard** 
```javascript
{
  module: 'dashboard',
  view: true/false  // Ver dashboard
}
```
- **Sin acciones críticas** (solo visualización)

---

### 2. **POS (Punto de Venta)**
```javascript
{
  module: 'pos',
  view: true/false,  // Acceso al POS
  actions: {
    create_return: true/false,        // ⚠️ Crear devoluciones
    apply_discount: true/false,       // ⚠️ Aplicar descuentos
    change_price: true/false,         // ⚠️ Cambiar precio de productos
    delete_cart_item: true/false      // ⚠️ Eliminar productos del carrito
  }
}
```

**Explicación:**
- Usuario básico: Solo puede agregar productos y cobrar
- Con permisos críticos: Puede hacer devoluciones, descuentos, cambiar precios

---

### 3. **Facturas**
```javascript
{
  module: 'invoices',
  view: true/false,  // Ver listado de facturas
  actions: {
    void: true/false,         // ⚠️ Anular facturas
    delete: true/false,       // ⚠️ Eliminar facturas
    view_details: true/false, // Ver detalles completos
    print: true/false         // Imprimir factura
  }
}
```

**Explicación:**
- Usuario básico: Solo ve el listado
- Con `void`: Puede anular facturas (CRÍTICO)
- Con `delete`: Puede eliminar facturas (MUY CRÍTICO)

---

### 4. **Devoluciones**
```javascript
{
  module: 'returns',
  view: true/false,  // Ver módulo de devoluciones
  actions: {
    create: true/false,   // ⚠️ Crear devoluciones
    delete: true/false    // ⚠️ Eliminar devoluciones
  }
}
```

---

### 5. **Productos**
```javascript
{
  module: 'products',
  view: true/false,  // Ver catálogo
  actions: {
    create: true/false,       // ⚠️ Crear productos
    edit: true/false,         // ⚠️ Editar productos
    delete: true/false,       // ⚠️ Eliminar productos
    import: true/false,       // ⚠️ Importar productos
    export: true/false        // Exportar productos
  }
}
```

---

### 6. **Categorías**
```javascript
{
  module: 'categories',
  view: true/false,
  actions: {
    create: true/false,   // ⚠️ Crear categorías
    edit: true/false,     // ⚠️ Editar categorías
    delete: true/false    // ⚠️ Eliminar categorías
  }
}
```

---

### 7. **Control de Stock**
```javascript
{
  module: 'stock',
  view: true/false,
  actions: {
    adjust: true/false,       // ⚠️ Ajustar inventario
    transfer: true/false      // ⚠️ Transferir entre bodegas
  }
}
```

---

### 8. **Inventario IA** (Premium)
```javascript
{
  module: 'intelligent_inventory',
  view: true/false  // Ver predicciones
}
```

---

### 9. **Multisede (Bodegas)** (Premium)
```javascript
{
  module: 'warehouses',
  view: true/false,
  actions: {
    create: true/false,   // ⚠️ Crear bodegas
    edit: true/false,     // ⚠️ Editar bodegas
    delete: true/false,   // ⚠️ Eliminar bodegas
    transfer: true/false  // ⚠️ Transferir stock
  }
}
```

---

### 10. **Clientes**
```javascript
{
  module: 'customers',
  view: true/false,
  actions: {
    create: true/false,   // ⚠️ Crear clientes
    edit: true/false,     // ⚠️ Editar clientes
    delete: true/false,   // ⚠️ Eliminar clientes
    export: true/false    // Exportar clientes
  }
}
```

---

### 11. **Cuentas por Cobrar** (Premium)
```javascript
{
  module: 'accounts_receivable',
  view: true/false,
  actions: {
    create_credit: true/false,    // ⚠️ Crear crédito
    mark_paid: true/false,        // ⚠️ Marcar como pagado
    cancel: true/false            // ⚠️ Cancelar crédito
  }
}
```

---

### 12. **Proveedores**
```javascript
{
  module: 'suppliers',
  view: true/false,
  actions: {
    create: true/false,   // ⚠️ Crear proveedores
    edit: true/false,     // ⚠️ Editar proveedores
    delete: true/false    // ⚠️ Eliminar proveedores
  }
}
```

---

### 13. **Usuarios y Roles**
```javascript
{
  module: 'users',
  view: true/false,
  actions: {
    create_user: true/false,      // ⚠️ Crear usuarios
    edit_user: true/false,        // ⚠️ Editar usuarios
    delete_user: true/false,      // ⚠️ Eliminar usuarios
    create_role: true/false,      // ⚠️ Crear roles
    edit_role: true/false,        // ⚠️ Editar roles
    delete_role: true/false       // ⚠️ Eliminar roles
  }
}
```

---

### 14. **Caja (Administración)**
```javascript
{
  module: 'cash_register',
  view: true/false,
  actions: {
    open_session: true/false,     // ⚠️ Abrir turno
    close_session: true/false,    // ⚠️ Cerrar turno
    view_history: true/false      // Ver historial
  }
}
```

---

### 15. **Gastos**
```javascript
{
  module: 'expenses',
  view: true/false,
  actions: {
    create: true/false,   // ⚠️ Crear gastos
    edit: true/false,     // ⚠️ Editar gastos
    delete: true/false    // ⚠️ Eliminar gastos
  }
}
```

---

### 16. **Reportes**
```javascript
{
  module: 'reports',
  view: true/false,
  actions: {
    export: true/false  // Exportar reportes
  }
}
```

---

### 17. **Configuración**
```javascript
{
  module: 'settings',
  view: true/false,
  actions: {
    edit_company: true/false,         // ⚠️ Editar datos empresa
    edit_tax: true/false,             // ⚠️ Editar impuestos
    edit_payment_methods: true/false  // ⚠️ Editar métodos de pago
  }
}
```

---

## 🎯 Resumen de Permisos Totales

**17 módulos** x (1 permiso view + acciones) = **~60 permisos**

### Por Módulo:
- Dashboard: 1 permiso
- POS: 5 permisos (view + 4 acciones críticas)
- Facturas: 5 permisos (view + 4 acciones)
- Devoluciones: 3 permisos
- Productos: 6 permisos
- Categorías: 4 permisos
- Stock: 3 permisos
- Inventario IA: 1 permiso
- Bodegas: 5 permisos
- Clientes: 5 permisos
- Cuentas por Cobrar: 4 permisos
- Proveedores: 4 permisos
- Usuarios: 7 permisos
- Caja: 4 permisos
- Gastos: 4 permisos
- Reportes: 2 permisos
- Configuración: 4 permisos

**Total: ~60 permisos** (vs 116 anteriores)

---

## 🔑 Ejemplo de Roles Predefinidos

### 🔴 **Administrador**
```javascript
// TODOS los permisos activados
```

### 🟢 **Vendedor**
```javascript
{
  pos: { view: true, create_return: false, apply_discount: false, change_price: false, delete_cart_item: false },
  invoices: { view: true, void: false, delete: false, view_details: true, print: true },
  customers: { view: true, create: true, edit: true, delete: false },
  products: { view: true, create: false, edit: false, delete: false }
}
```
**Puede:** Vender, ver facturas, imprimir, agregar clientes
**NO puede:** Anular facturas, cambiar precios, eliminar productos

### 🟡 **Cajero**
```javascript
{
  pos: { view: true, create_return: true, apply_discount: false, change_price: false, delete_cart_item: true },
  invoices: { view: true, void: false, delete: false, view_details: true, print: true },
  cash_register: { view: true, open_session: true, close_session: true, view_history: true },
  customers: { view: true, create: true, edit: true, delete: false }
}
```
**Puede:** Vender, devoluciones, abrir/cerrar caja
**NO puede:** Anular facturas, cambiar precios

### 🔵 **Gerente**
```javascript
{
  // Casi todos los permisos
  // NO puede: delete_user, delete_role, edit_company, edit_tax
}
```
**Puede:** Ver reportes, anular facturas, gestionar inventario
**NO puede:** Eliminar usuarios, cambiar configuración fiscal

### 🟣 **Contador**
```javascript
{
  dashboard: { view: true },
  invoices: { view: true, void: false, delete: false, view_details: true, print: true },
  expenses: { view: true, create: true, edit: true, delete: false },
  reports: { view: true, export: true },
  customers: { view: true },
  suppliers: { view: true }
}
```
**Puede:** Ver facturas, gastos, reportes, clientes, proveedores
**NO puede:** Vender, anular facturas, editar productos

---

## ⚡ Implementación en Frontend

### Verificar acceso a módulo:
```javascript
function hasModuleAccess(module) {
  const permissions = user.value.role?.permissions || [];
  return permissions.some(p => p.startsWith(`${module}.view`));
}
```

### Verificar acción crítica:
```javascript
function hasPermission(permission) {
  const permissions = user.value.role?.permissions || [];
  return permissions.includes(permission);
}

// Ejemplo en POS:
if (!hasPermission('pos.apply_discount')) {
  alert('⚠️ No tienes permiso para aplicar descuentos');
  return;
}
```

### Ocultar botones según permisos:
```vue
<!-- Botón de devoluciones solo si tiene permiso -->
<button
  v-if="hasPermission('pos.create_return')"
  @click="showReturnsModal = true"
>
  Devoluciones
</button>

<!-- Botón de anular factura -->
<button
  v-if="hasPermission('invoices.void')"
  @click="voidInvoice(invoice)"
  class="text-rose-600"
>
  Anular
</button>
```

---

## ✅ Ventajas del Nuevo Sistema

✅ **Simple**: 60 permisos vs 116 anteriores
✅ **Práctico**: Solo permisos que realmente importan
✅ **Claro**: "view" para módulo, "actions" para cosas críticas
✅ **Escalable**: Fácil agregar nuevos permisos
✅ **Mantenible**: Menos código, menos confusión

---

## 🚫 Lo que NO incluimos

❌ Permisos súper granulares innecesarios:
- "invoices.view_details" vs "invoices.view" (demasiado específico)
- "products.view_images" (si ve productos, ve imágenes)
- "pos.add_to_cart" (obvio, si tiene acceso al POS puede agregar)

✅ **Regla de oro:** Si un usuario tiene acceso al módulo, puede hacer operaciones BÁSICAS. Solo controlamos las CRÍTICAS.
