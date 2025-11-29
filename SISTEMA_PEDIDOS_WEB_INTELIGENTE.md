# 🎯 Sistema de Pedidos Web Inteligente - Documentación Completa

## 📋 Resumen General

Sistema profesional de gestión de pedidos web con validación inteligente de clientes basada en documento/cédula. Los clientes realizan pedidos desde un catálogo público, reciben un código único (PED-XXX), y el cajero puede cargar el pedido directamente en el POS con confirmación inteligente de creación de clientes.

---

## 🚀 Características Implementadas

### ✅ 1. Catálogo Público
- **Ubicación**: `/catalogo` (acceso público sin autenticación)
- **Funcionalidad**:
  - Visualización de productos disponibles
  - Carrito de compras funcional
  - **Campo obligatorio**: Documento/Cédula (mínimo 6 caracteres)
  - Campos: Nombre, Teléfono, Dirección, Tipo de entrega
  - Generación automática de código único (PED-XXX)
  - Integración WhatsApp con mensaje personalizado

### ✅ 2. Validación Backend Robusta
- **Endpoint**: `POST /api/public/orders`
- **Validaciones**:
  ```php
  'customer_document' => 'required|string|min:6|max:20'
  ```
- **Modelo**: `OnlineOrder` con campo `customer_document` en fillable
- **Base de datos**: Columna `customer_document` indexada para búsquedas rápidas

### ✅ 3. Carga Inteligente en POS

#### 🎯 Flujo de Búsqueda de Cliente (Prioridad):
1. **Buscar por Documento** (prioridad máxima)
   - Si se encuentra → Asignar automáticamente
2. **Buscar por Teléfono** (segunda prioridad)
   - Si se encuentra → Asignar automáticamente
3. **Cliente NO existe**:
   - Mostrar modal de confirmación
   - Usuario decide si crear o cancelar

#### 🔍 Métodos de Búsqueda Implementados:
```javascript
// customersService.js
- findByDocument(document)  // Normaliza y busca por cédula
- findByPhone(phone)        // Normaliza y busca por teléfono
```

### ✅ 4. Modal de Confirmación Profesional
- **Componente**: `ConfirmCustomerModal.vue`
- **Diseño**: Empresarial con gradiente naranja/amarillo (advertencia)
- **Información mostrada**:
  - Nombre del cliente
  - Documento/Cédula
  - Teléfono
  - Dirección (si existe)
- **Acciones**:
  - "Sí, Agregar Cliente" (verde empresarial)
  - "Cancelar" (neutro slate)

---

## 📁 Archivos Modificados/Creados

### Backend

#### 1. **PublicCatalogController.php**
```php
// Validación de documento obligatorio
'customer_document' => 'required|string|min:6|max:20',

// Incluir documento en respuesta findByCode
'customer_document' => $order->customer_document,
```

#### 2. **OnlineOrder.php** (Modelo)
```php
protected $fillable = [
    'customer_name',
    'customer_phone',
    'customer_document', // ✅ NUEVO
    'customer_address',
    // ...
];
```

#### 3. **Migración de Base de Datos**
```sql
-- Tabla online_orders
CREATE TABLE online_orders (
    -- ...
    customer_document VARCHAR(20) NULL,
    -- ...
    INDEX idx_customer_document (customer_document)
);
```

### Frontend

#### 1. **CheckoutDrawer.vue** (Catálogo)
```vue
<!-- Campo de documento obligatorio -->
<input
  v-model="checkoutData.customer_document"
  type="text"
  placeholder="Ej: 1234567890"
  required
  minlength="6"
/>
```

#### 2. **customersService.js**
```javascript
// Método nuevo: Buscar por documento
async findByDocument(document) {
  const customersList = Array.isArray(response) ? response : 
                        (response.data || response.customers || [])
  
  const normalizedDocument = document.replace(/[\s\-\.]/g, '').toUpperCase()
  return customersList.find(c => {
    const customerDocument = (c.document_number || '')
      .replace(/[\s\-\.]/g, '').toUpperCase()
    return customerDocument === normalizedDocument
  })
}
```

#### 3. **ConfirmCustomerModal.vue** (NUEVO)
- Modal profesional de confirmación
- Z-index: 110 (sobre el modal de carga)
- Gradiente naranja/amarillo empresarial
- Botones con jerarquía visual clara

#### 4. **PosView.vue**
```javascript
// Lógica inteligente de búsqueda
const handleWebOrderLoaded = async (order) => {
  // 1. Buscar por documento (prioridad)
  if (order.customer_document) {
    customer = await customersService.findByDocument(order.customer_document)
  }
  
  // 2. Buscar por teléfono (fallback)
  if (!customer) {
    customer = await customersService.findByPhone(order.customer_phone)
  }
  
  // 3. Si existe: asignar automáticamente
  if (customer) {
    currentTab.customer = customer
    await loadOrderProductsToCart(order)
    showSuccess(`Pedido cargado. Cliente: ${customer.name}`)
  } 
  // 4. Si NO existe: pedir confirmación
  else {
    pendingCustomerData.value = { name, document, phone, address }
    pendingWebOrder.value = order
    showConfirmCustomerModal.value = true
  }
}

// Confirmar creación de cliente
const handleConfirmNewCustomer = async () => {
  const newCustomer = await customersService.create({
    name: pendingCustomerData.value.name,
    phone: pendingCustomerData.value.phone,
    document_number: pendingCustomerData.value.document,
    // ...
  })
  
  currentTab.customer = newCustomer
  await loadOrderProductsToCart(pendingWebOrder.value)
  showSuccess(`Cliente creado: ${newCustomer.name}`)
}

// Cancelar creación
const handleCancelNewCustomer = () => {
  showInfo('Pedido web cancelado.')
  pendingCustomerData.value = null
  pendingWebOrder.value = null
}
```

---

## 🎨 Diseño Empresarial Aplicado

### Modal de Confirmación
```vue
<!-- Header -->
<div class="bg-gradient-to-r from-amber-500 to-orange-500">
  <div class="w-10 h-10 bg-white/20 rounded-lg">
    <svg>⚠️ Icono de advertencia</svg>
  </div>
  <h3>Cliente Nuevo</h3>
  <p>Confirmar registro</p>
</div>

<!-- Content -->
<div class="bg-orange-50 border border-orange-200">
  <p>Este cliente no está registrado en el sistema.</p>
  <p class="font-semibold">¿Deseas agregar este cliente?</p>
</div>

<!-- Customer Info -->
<div class="space-y-3">
  <div class="bg-gray-50 rounded-lg p-3">
    <label>Nombre</label>
    <p class="font-semibold">{{ customerData.name }}</p>
  </div>
  <!-- Documento, Teléfono, Dirección -->
</div>

<!-- Footer -->
<div class="bg-gray-50 flex justify-end">
  <button class="bg-white border">Cancelar</button>
  <button class="bg-gradient-to-r from-lime-400 to-green-400">
    Sí, Agregar Cliente
  </button>
</div>
```

---

## 🔄 Flujo Completo del Sistema

### 1️⃣ Cliente en el Catálogo
```
Cliente navega → Selecciona productos → Checkout
→ Completa formulario (NOMBRE, DOCUMENTO*, TELÉFONO, DIRECCIÓN)
→ Finaliza pedido
→ Recibe código PED-XXX
→ Mensaje WhatsApp automático al negocio
```

### 2️⃣ Cajero en el POS
```
Cajero abre "Cargar Pedido Web" (botón en toolbar)
→ Ingresa código: PED-XXX o solo XXX
→ Sistema busca pedido
→ ✅ Verifica stock disponible
```

### 3️⃣ Búsqueda Inteligente de Cliente
```
Sistema busca cliente por DOCUMENTO (prioridad)
↓
¿Encontrado?
├─ SÍ → Asigna automáticamente + Carga productos
└─ NO → Busca por TELÉFONO
    ↓
    ¿Encontrado?
    ├─ SÍ → Asigna automáticamente + Carga productos
    └─ NO → Muestra modal de confirmación
        ↓
        Usuario decide:
        ├─ "Sí, Agregar Cliente" → Crea cliente + Asigna + Carga productos
        └─ "Cancelar" → Cancela operación (no crea cliente ni carga pedido)
```

### 4️⃣ Finalización
```
Carrito cargado con:
- Cliente asignado (existente o nuevo)
- Productos del pedido con cantidades
- Nota: "Pedido Web #PED-XXX\n{nota del cliente}"
→ Cajero procesa pago normalmente
```

---

## 🧪 Pruebas Sugeridas

### Caso 1: Cliente Ya Registrado (Por Documento)
```
1. Cliente hace pedido con documento: 1234567890
2. Cajero carga pedido PED-XXX
3. Sistema encuentra cliente por documento
4. ✅ Asigna automáticamente sin confirmación
```

### Caso 2: Cliente Ya Registrado (Por Teléfono)
```
1. Cliente hace pedido con documento nuevo pero teléfono conocido
2. Cajero carga pedido PED-XXX
3. Sistema NO encuentra por documento
4. Sistema encuentra por teléfono
5. ✅ Asigna automáticamente sin confirmación
```

### Caso 3: Cliente Completamente Nuevo
```
1. Cliente hace pedido con documento y teléfono nuevos
2. Cajero carga pedido PED-XXX
3. Sistema NO encuentra por documento
4. Sistema NO encuentra por teléfono
5. ⚠️ Muestra modal de confirmación
6a. Cajero acepta → Cliente creado + Pedido cargado
6b. Cajero cancela → Operación cancelada
```

### Caso 4: Stock Insuficiente
```
1. Pedido cargado tiene productos sin stock
2. Modal muestra advertencias de stock
3. Cajero decide si continuar o ajustar
```

---

## 📊 Estructura de Base de Datos

### Tabla: `online_orders`
```sql
id                  BIGINT UNSIGNED AUTO_INCREMENT
uuid                VARCHAR(36) UNIQUE
order_number        VARCHAR(20) UNIQUE          -- PED-XXX
customer_name       VARCHAR(255)
customer_phone      VARCHAR(20)
customer_document   VARCHAR(20) NULL            -- ✅ NUEVO
customer_address    TEXT NULL
delivery_type       ENUM('pickup', 'delivery')
status              ENUM('pending', 'confirmed', 'completed', 'cancelled')
note                TEXT NULL
subtotal            DECIMAL(12, 2)
total               DECIMAL(12, 2)
created_at          TIMESTAMP
updated_at          TIMESTAMP

-- Índices
INDEX idx_order_number (order_number)
INDEX idx_customer_phone (customer_phone)
INDEX idx_customer_document (customer_document)  -- ✅ NUEVO
INDEX idx_status (status)
```

### Tabla: `online_order_items`
```sql
id                      BIGINT UNSIGNED AUTO_INCREMENT
online_order_id         BIGINT UNSIGNED
product_id              BIGINT UNSIGNED
product_name            VARCHAR(255)
product_sku             VARCHAR(100)
quantity                INT
unit_price              DECIMAL(12, 2)
subtotal                DECIMAL(12, 2)
special_instructions    TEXT NULL
created_at              TIMESTAMP
updated_at              TIMESTAMP

FOREIGN KEY (online_order_id) REFERENCES online_orders(id) ON DELETE CASCADE
FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
```

---

## 🎯 Beneficios del Sistema

### ✅ Para el Negocio
- **Eficiencia**: Carga rápida de pedidos sin re-ingresar datos
- **Control**: Confirmación antes de crear clientes nuevos
- **Trazabilidad**: Cada pedido tiene código único y registro completo
- **Inteligencia**: Búsqueda múltiple (documento + teléfono) previene duplicados

### ✅ Para el Cliente
- **Comodidad**: Hace pedido desde casa con WhatsApp automático
- **Profesionalismo**: Recibe código de seguimiento (PED-XXX)
- **Rapidez**: Su pedido ya está preparado al llegar a recoger

### ✅ Para el Cajero
- **Simplicidad**: Solo ingresa código PED-XXX
- **Seguridad**: Valida stock antes de cargar
- **Claridad**: Sabe si el cliente es nuevo o existente
- **Autonomía**: Decide si crear o no un cliente nuevo

---

## 🔧 Configuración Post-Implementación

### 1. Verificar Base de Datos
```bash
# Verificar que las tablas existen en todos los tenants
mysql -u root -e "SELECT TABLE_NAME FROM information_schema.TABLES 
WHERE TABLE_SCHEMA LIKE 'tenant%' AND TABLE_NAME = 'online_orders'"
```

### 2. Probar Endpoints
```bash
# Crear pedido de prueba
curl -X POST http://localhost/api/public/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Test Cliente",
    "customer_phone": "3001234567",
    "customer_document": "1234567890",
    "customer_address": "Calle 123",
    "delivery_type": "pickup",
    "items": [...]
  }'

# Buscar pedido
curl -X POST http://localhost/api/public/orders/find-by-code \
  -H "Content-Type: application/json" \
  -d '{"code": "PED-123"}'
```

### 3. Verificar Frontend
```bash
# Build de producción
npm run build

# Verificar que ConfirmCustomerModal.vue existe
ls src/components/pos/ConfirmCustomerModal.vue
```

---

## 📝 Notas Técnicas

### Normalización de Datos
```javascript
// Documentos: Remueve espacios, guiones, puntos y convierte a mayúsculas
const normalized = document.replace(/[\s\-\.]/g, '').toUpperCase()
// "1.234.567-8" → "12345678"

// Teléfonos: Remueve espacios, guiones, paréntesis
const normalized = phone.replace(/[\s\-()]/g, '')
// "(300) 123-4567" → "3001234567"
```

### Manejo de Errores
- **Stock insuficiente**: Se muestra pero permite continuar
- **Pedido no encontrado**: Mensaje claro en modal
- **Error de red**: Toast notification con mensaje amigable
- **Cliente cancelado**: Info notification (no es un error)

### Estados del Modal
```javascript
showLoadWebOrderModal      // Modal búsqueda de pedido (z-100)
showConfirmCustomerModal   // Modal confirmación cliente (z-110)
pendingCustomerData        // Datos del cliente a crear
pendingWebOrder            // Pedido pendiente de procesar
```

---

## 🚀 Próximas Mejoras Sugeridas

1. **Historial de Pedidos Web**
   - Vista en POS de todos los pedidos web
   - Filtros por estado, fecha, cliente
   - Marcar pedidos como "completados"

2. **Notificaciones Automáticas**
   - WhatsApp cuando el pedido está listo
   - SMS/Email con código de seguimiento
   - Actualización de estado en tiempo real

3. **Panel de Administración**
   - Estadísticas de pedidos web
   - Productos más solicitados online
   - Tiempos de respuesta promedio

4. **Integración con Delivery**
   - Asignar repartidor desde POS
   - Tracking de entregas
   - Firma digital del cliente

---

## 📞 Soporte

Si encuentras algún problema:
1. Verifica que las migraciones se ejecutaron correctamente
2. Revisa los logs de Laravel: `backend/storage/logs/laravel.log`
3. Verifica la consola del navegador (F12) para errores JavaScript
4. Confirma que el build del frontend se completó sin errores

---

**Fecha de Implementación**: 28 de Noviembre de 2025  
**Versión del Sistema**: 1.0.0  
**Estado**: ✅ Completamente Funcional

