
# 🧪 PLAN DE TESTING COMPLETO - POS 105 PRO

## 📋 ANÁLISIS PREVIO DE ARQUITECTURA

### ✅ Estructura Detectada

#### **Base de Datos (Multi-tenant)**
- **Central**: `tenants`, `domains`, `signup_tokens`, `central_users`, `payment_transactions`
- **Tenant**: 85 tablas por inquilino (usuarios, productos, ventas, gastos, etc.)
- **Migraciones**: Bien estructuradas con timestamps y orden cronológico

#### **Backend (Laravel)**
- **Controladores**: 20+ controladores API identificados
- **Modelos**: 42 modelos con relaciones Eloquent
- **Multi-tenancy**: Stancl/Tenancy implementado
- **Middlewares**: Autenticación, permisos, tenant scope

#### **Frontend (Vue 3)**
- **Store Global**: Pinia con precarga de datos
- **Componentes**: POS, Inventario, Ventas, Reportes, etc.
- **Router**: Protección de rutas con guards

---

## 🎯 ÁREAS CRÍTICAS A TESTEAR

### 1. REGISTRO Y ONBOARDING (CRITICAL 🔴)

#### 1.1 Flujo de Registro
**URL**: `http://las-nanas.localhost:3000/register`

**Test Cases**:
```
✅ TC001: Registro con datos válidos
   - Nombre negocio: "Tienda Test"
   - Email: "test@test.com"
   - Contraseña: "Admin123!"
   - Resultado esperado: Cuenta creada + DB tenant + usuario admin

❌ TC002: Registro con email duplicado
   - Email existente
   - Resultado esperado: Error "Email ya registrado"

❌ TC003: Registro con contraseña débil
   - Contraseña: "123"
   - Resultado esperado: Error de validación

❌ TC004: Registro sin aceptar términos
   - Checkbox términos: false
   - Resultado esperado: Error o bloqueo de envío

⚠️ TC005: Registro con caracteres especiales en nombre
   - Nombre: "Tienda @#$%"
   - Resultado esperado: ¿Acepta o rechaza?

💾 TC006: Verificar datos en DB
   - Tabla `tenants`: Debe existir registro con id='tienda-test'
   - Tabla `domains`: Debe existir dominio 'tienda-test.localhost'
   - Tabla tenant.`users`: Debe existir usuario con role_id=1 (admin)
```

**Queries de Verificación**:
```sql
-- Verificar tenant creado
SELECT * FROM tenants WHERE id = 'tienda-test';

-- Verificar dominio asignado
SELECT * FROM domains WHERE tenant_id = 'tienda-test';

-- Verificar usuario admin (dentro de la DB del tenant)
USE tienda_test;
SELECT * FROM users WHERE email = 'test@test.com';
SELECT * FROM roles WHERE id = 1;
```

---

### 2. LOGIN Y AUTENTICACIÓN (CRITICAL 🔴)

#### 2.1 Login Normal
**URL**: `http://las-nanas.localhost:3000/login`

**Test Cases**:
```
✅ TC010: Login con credenciales correctas
   - Email: admin@tienda.com
   - Password: CorrectPassword123!
   - Resultado: Token generado + Redirección a /pos

❌ TC011: Login con email incorrecto
   - Email: noexiste@test.com
   - Resultado: Error "Credenciales inválidas"

❌ TC012: Login con contraseña incorrecta
   - Password: WrongPassword
   - Resultado: Error "Credenciales inválidas"

⚠️ TC013: Login con cuenta desactivada
   - Usuario con active=0
   - Resultado: Error "Cuenta desactivada"

⚠️ TC014: Token expirado
   - Token antiguo
   - Resultado: Forzar re-login

💾 TC015: Verificar personal_access_tokens
   - Tabla tenant.`personal_access_tokens`
   - Debe crear token con abilities=['*']
```

#### 2.2 Google Login
**Test Cases**:
```
✅ TC020: Login con Google exitoso
   - Google ID: "1234567890"
   - Email: "user@gmail.com"
   - Resultado: Vincula con usuario existente o crea uno nuevo

⚠️ TC021: Google login con email no registrado
   - Resultado: ¿Crea usuario automáticamente o requiere registro?

💾 TC022: Verificar google_id en users
   - Campo `google_id` debe poblarse
```

---

### 3. CONFIGURACIÓN DEL SISTEMA (HIGH 🟡)

#### 3.1 System Settings
**URL**: `/settings`

**Test Cases**:
```
✅ TC030: Actualizar configuración básica
   - Nombre negocio
   - Tax rate: 19%
   - Currency: COP
   - Resultado: Guardado exitoso

⚠️ TC031: Tax rate con valores extremos
   - 0%, 100%, -10%
   - Resultado: ¿Acepta negativos? ¿Límite máximo?

⚠️ TC032: Crédito - Porcentaje de recargo
   - credit_surcharge_percentage: 15%
   - Resultado: ¿Se aplica correctamente en ventas?

💾 TC033: Verificar en DB
   - Tabla `system_settings`
   - Debe actualizarse con JSON o campos específicos
```

---

### 4. GESTIÓN DE USUARIOS Y PERMISOS (HIGH 🟡)

#### 4.1 Crear Usuario
**URL**: `/users`

**Test Cases**:
```
✅ TC040: Crear usuario vendedor
   - Nombre: "Juan Vendedor"
   - Email: "vendedor@tienda.com"
   - Role: Vendedor (id=2)
   - Permisos: [ver_productos, crear_ventas]
   - Resultado: Usuario creado + permisos asignados

❌ TC041: Crear usuario con email duplicado
   - Email existente
   - Resultado: Error de validación

⚠️ TC042: Usuario sin rol
   - role_id: null
   - Resultado: ¿Permite crear? ¿Asigna rol por defecto?

💾 TC043: Verificar permisos en DB
   - Tabla `users`: role_id correcto
   - Relación users <-> roles <-> permissions
```

#### 4.2 Probar Permisos
**Test Cases**:
```
✅ TC050: Vendedor sin permiso de eliminar
   - Login como vendedor
   - Intentar eliminar producto
   - Resultado: Error 403 "Sin permisos"

✅ TC051: Admin puede todo
   - Login como admin
   - Probar: crear, editar, eliminar en todos los módulos
   - Resultado: Todas las acciones permitidas

⚠️ TC052: Permisos en cascada
   - Desactivar permiso padre: "gestionar_inventario"
   - Resultado: ¿Se bloquean subpermisos como "editar_producto"?
```

---

### 5. PRODUCTOS E INVENTARIO (CRITICAL 🔴)

#### 5.1 Crear Producto Simple
**URL**: `/inventory`

**Test Cases**:
```
✅ TC060: Crear producto básico
   - Nombre: "Producto Test"
   - SKU: "TEST001"
   - Precio: 10000
   - Stock: 50
   - Resultado: Producto creado + stock inicial

❌ TC061: Crear producto con SKU duplicado
   - SKU existente
   - Resultado: Error "SKU ya existe"

⚠️ TC062: Precio con valores extremos
   - Precio: 0, -100, 9999999999
   - Resultado: ¿Acepta negativos? ¿Límite máximo?

⚠️ TC063: Stock negativo
   - Stock: -10
   - Resultado: ¿Permite o bloquea?

💾 TC064: Verificar en DB
   - Tabla `products`: Registro creado
   - Tabla `inventory_movements`: Movimiento de stock inicial tipo "initial_stock"
```

#### 5.2 Crear Producto con Variantes (Fashion)
**Test Cases**:
```
✅ TC070: Producto con variantes (Talla/Color)
   - Nombre: "Camiseta Polo"
   - Variantes:
     * Talla S / Color Rojo / Stock 10
     * Talla M / Color Azul / Stock 15
   - Resultado: Producto padre + variantes en `product_variants`

⚠️ TC071: Variante con stock negativo
   - Resultado: ¿Permite vender sin stock?

💾 TC072: Verificar relaciones en DB
   - Tabla `product_variants`: variant_options en JSON
   - Relación product_id -> products.id
```

#### 5.3 Multi-Warehouse (Bodegas)
**Test Cases**:
```
✅ TC080: Crear bodega secundaria
   - Nombre: "Bodega Norte"
   - Dirección: "Calle 123"
   - Resultado: Bodega creada

✅ TC081: Transferir stock entre bodegas
   - Producto: "TEST001"
   - Origen: Bodega Principal
   - Destino: Bodega Norte
   - Cantidad: 10
   - Resultado: Stock actualizado en ambas + registro en `stock_transfers`

⚠️ TC082: Transferir más stock del disponible
   - Origen tiene 5, intentar transferir 10
   - Resultado: Error de validación

💾 TC083: Verificar en DB
   - Tabla `product_warehouse`: Stock por bodega
   - Tabla `stock_transfers`: Registro de transferencia con status='completed'
   - Tabla `stock_transfer_items`: Items transferidos
```

---

### 6. VENTAS Y FACTURACIÓN (CRITICAL 🔴)

#### 6.1 Venta Simple (Efectivo)
**URL**: `/pos`

**Test Cases**:
```
✅ TC090: Venta con un producto
   - Producto: "TEST001" x2
   - Cliente: "Cliente Final"
   - Método pago: Efectivo
   - Resultado: Venta exitosa + factura + stock descontado

⚠️ TC091: Venta sin stock suficiente
   - Stock disponible: 1
   - Intentar vender: 5
   - Resultado: Error "Stock insuficiente"

⚠️ TC092: Venta sin caja abierta
   - No hay sesión en `cash_sessions` con status='open'
   - Resultado: Error "Debe abrir caja"

💾 TC093: Verificar en DB
   - Tabla `invoices`: Registro con invoice_number, total_amount
   - Tabla `invoice_items`: Items vendidos con prices
   - Tabla `inventory_movements`: Movimiento tipo "sale" con cantidad negativa
   - Tabla `cash_sessions`: total_sales actualizado
```

#### 6.2 Venta con Descuento
**Test Cases**:
```
✅ TC100: Aplicar cupón de descuento
   - Producto: 100.000
   - Cupón: "DESC20" (20% off)
   - Resultado: Total = 80.000

⚠️ TC101: Cupón expirado
   - valid_until < fecha actual
   - Resultado: Error "Cupón expirado"

⚠️ TC102: Cupón sin uses restantes
   - max_uses = 10, current_uses = 10
   - Resultado: Error "Cupón agotado"

💾 TC103: Verificar uso de cupón
   - Tabla `discount_usages`: Registro con discount_id, invoice_id
   - Tabla `discounts`: usage_count incrementado
```

#### 6.3 Venta a Crédito
**Test Cases**:
```
✅ TC110: Venta a crédito con cupo disponible
   - Cliente con credit_limit=500.000, current_debt=100.000
   - Venta: 200.000
   - Resultado: Venta exitosa + current_debt=300.000

⚠️ TC111: Venta que excede cupo
   - Cupo disponible: 100.000
   - Intentar vender: 150.000
   - Resultado: Error "Cupo insuficiente"

⚠️ TC112: Cliente sin crédito habilitado
   - credit_active = 0
   - Resultado: Error "Cliente sin crédito"

💾 TC113: Verificar en DB
   - Tabla `customers`: current_debt actualizado
   - Tabla `invoices`: payment_method='credit'
   - Tabla `pending_payments`: Registro de deuda
```

#### 6.4 Venta con Puntos de Fidelización
**Test Cases**:
```
✅ TC120: Redimir puntos
   - Cliente con 1000 puntos
   - Redimir: 500 puntos = 5.000 COP
   - Resultado: Descuento aplicado + puntos descontados

⚠️ TC121: Redimir más puntos de los disponibles
   - Disponibles: 100, intentar redimir: 500
   - Resultado: Error de validación

💾 TC122: Verificar en DB
   - Tabla `customers`: loyalty_points actualizado
   - Tabla `loyalty_transactions`: Registro tipo "redeemed"
```

---

### 7. CONTROL DE CAJA (CRITICAL 🔴)

#### 7.1 Abrir Caja
**Test Cases**:
```
✅ TC130: Abrir caja con monto inicial
   - Usuario: "Cajero 1"
   - Monto inicial: 50.000
   - Bodega: "Principal"
   - Resultado: Sesión creada con status='open'

⚠️ TC131: Intentar abrir caja ya abierta
   - Ya existe sesión open
   - Resultado: Error "Caja ya abierta"

💾 TC132: Verificar en DB
   - Tabla `cash_sessions`: status='open', user_id, warehouse_id
```

#### 7.2 Cerrar Caja
**Test Cases**:
```
✅ TC140: Cerrar caja con arqueo correcto
   - Monto esperado: 250.000
   - Monto contado: 250.000
   - Diferencia: 0
   - Resultado: Sesión cerrada con status='closed'

⚠️ TC141: Arqueo con diferencia
   - Esperado: 250.000
   - Contado: 248.000
   - Diferencia: -2.000 (faltante)
   - Resultado: Alerta de diferencia registrada

💾 TC142: Verificar cierre en DB
   - Tabla `cash_sessions`: closed_at, actual_amount, difference
```

---

### 8. DEVOLUCIONES (HIGH 🟡)

#### 8.1 Devolución Total
**Test Cases**:
```
✅ TC150: Devolver factura completa
   - Factura: #FACT-001 (2 productos)
   - Tipo: Total
   - Resultado: Factura marcada como returned, stock restaurado

💾 TC151: Verificar en DB
   - Tabla `invoices`: status='returned'
   - Tabla `returns`: Registro con invoice_id
   - Tabla `return_items`: Items devueltos
   - Tabla `inventory_movements`: Movimientos tipo "return" positivos
```

#### 8.2 Devolución Parcial
**Test Cases**:
```
✅ TC160: Devolver solo 1 producto de 3
   - Resultado: Factura con returned_partial=1, stock parcial restaurado

⚠️ TC161: Devolver más de lo vendido
   - Vendido: 2 unidades
   - Intentar devolver: 5
   - Resultado: Error de validación
```

---

### 9. GASTOS OPERATIVOS (MEDIUM 🟢)

#### 9.1 Crear Gasto
**Test Cases**:
```
✅ TC170: Registrar gasto operativo
   - Categoría: "Servicios Públicos"
   - Monto: 150.000
   - Fecha: Hoy
   - Resultado: Gasto registrado + cash_sessions actualizado

💾 TC171: Verificar en DB
   - Tabla `expenses`: Registro con category_id, amount
   - Tabla `cash_sessions`: total_expenses actualizado
```

---

### 10. REPORTES (MEDIUM 🟢)

#### 10.1 Reporte de Ventas
**Test Cases**:
```
✅ TC180: Reporte diario
   - Fecha: Hoy
   - Resultado: Total ventas, productos vendidos, métodos de pago

⚠️ TC181: Reporte con rango sin datos
   - Fechas: 01/01/2020 - 01/02/2020
   - Resultado: Reporte vacío o mensaje "Sin datos"

💾 TC182: Verificar query
   - JOIN entre invoices, invoice_items, products
   - Filtro por fecha correcta
```

---

### 11. COTIZACIONES (MEDIUM 🟢)

#### 11.1 Crear Cotización
**Test Cases**:
```
✅ TC190: Crear cotización
   - Productos: 3 items
   - Cliente: "Juan Pérez"
   - Resultado: Cotización con código COT-XXXXXX

✅ TC191: Cargar cotización en POS
   - Código: "COT-000123"
   - Resultado: Productos cargados en carrito + cliente asignado

⚠️ TC192: Cargar cotización con productos sin stock
   - Resultado: Alerta de productos no disponibles
```

---

### 12. CATÁLOGO WEB (LOW 🔵)

#### 12.1 Pedidos Online
**Test Cases**:
```
✅ TC200: Cliente hace pedido web
   - Producto: "TEST001" x1
   - Resultado: Registro en `online_orders` con status='pending'

✅ TC201: Administrador procesa pedido
   - Cambiar status: 'pending' -> 'processing' -> 'completed'
   - Resultado: Stock descontado, factura generada

💾 TC202: Verificar en DB
   - Tabla `online_orders`: status actualizado
   - Tabla `online_order_items`: Items del pedido
```

---

## 🛠️ HERRAMIENTAS DE TESTING

### Queries SQL Útiles

```sql
-- Ver todas las tenants
SELECT id, business_name, plan, created_at FROM tenants;

-- Ver usuarios de un tenant
USE nombre_tenant;
SELECT id, name, email, role_id, active FROM users;

-- Ver productos con bajo stock
SELECT name, sku, stock FROM products WHERE stock < min_stock;

-- Ver ventas del día
SELECT 
  DATE(created_at) as fecha,
  COUNT(*) as num_facturas,
  SUM(total_amount) as total_ventas
FROM invoices 
WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'
GROUP BY DATE(created_at);

-- Ver sesiones de caja abiertas
SELECT * FROM cash_sessions WHERE status = 'open';

-- Ver movimientos de inventario
SELECT 
  im.*, 
  p.name as product_name
FROM inventory_movements im
JOIN products p ON im.product_id = p.id
ORDER BY im.created_at DESC
LIMIT 20;
```

---

## 📝 CHECKLIST DE EJECUCIÓN

### Fase 1: Preparación (30 min)
- [ ] Backup completo de base de datos
- [ ] Crear tenant de prueba: "test-store"
- [ ] Crear usuarios: admin, vendedor, cajero
- [ ] Cargar productos de prueba (10-20 productos)

### Fase 2: Tests Críticos (2 horas)
- [ ] Registro y login (TC001-TC022)
- [ ] Crear productos (TC060-TC083)
- [ ] Realizar ventas (TC090-TC122)
- [ ] Control de caja (TC130-TC142)

### Fase 3: Tests de Seguridad (1 hora)
- [ ] Permisos de usuario (TC050-TC052)
- [ ] Validaciones de stock (TC091, TC161)
- [ ] Validaciones de crédito (TC110-TC112)

### Fase 4: Tests de Estrés (1 hora)
- [ ] Vender 100 productos seguidos
- [ ] Crear 50 usuarios simultáneos
- [ ] Generar reporte con 10.000 ventas

### Fase 5: Edge Cases (1 hora)
- [ ] Valores extremos (TC031, TC062, TC141)
- [ ] Datos duplicados (TC002, TC061)
- [ ] Estados inválidos (TC013, TC092, TC131)

---

## 🐛 REGISTRO DE BUGS ENCONTRADOS

### Template de Bug Report
```
BUG ID: #XXX
Prioridad: [CRITICAL / HIGH / MEDIUM / LOW]
Módulo: [Ventas / Inventario / Usuarios / etc.]
Descripción: [Descripción clara del problema]
Pasos para reproducir:
  1. ...
  2. ...
  3. ...
Resultado esperado: ...
Resultado actual: ...
Query afectado: [Si aplica]
Screenshot: [Si aplica]
```

---

## ✅ CRITERIOS DE ACEPTACIÓN

### Para ir a Producción
- [ ] **0 bugs CRÍTICOS** sin resolver
- [ ] **< 3 bugs HIGH** sin resolver
- [ ] **Todos los flujos principales** funcionando
- [ ] **Permisos** correctamente implementados
- [ ] **Validaciones** en frontend y backend
- [ ] **Reportes** generando datos correctos
- [ ] **Base de datos** con índices optimizados
- [ ] **Backup automático** configurado
- [ ] **Logs de errores** funcionando
- [ ] **Documentación** de API actualizada

---

## 🎯 PRÓXIMOS PASOS

1. **Revisar este plan** y confirmar prioridades
2. **Ejecutar Fase 1**: Preparación del ambiente de pruebas
3. **Ejecutar Fase 2**: Tests críticos de venta y stock
4. **Documentar bugs** encontrados con template
5. **Iterar** hasta cumplir criterios de aceptación

---

**Fecha de creación**: 24 de diciembre de 2025
**Última actualización**: Pendiente
**Responsable**: Equipo de Desarrollo
**Estado**: DRAFT - Pendiente de ejecución
