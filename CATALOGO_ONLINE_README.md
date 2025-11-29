# 🛒 Módulo de Catálogo Online / Tienda Digital

## 📋 Descripción General

Este módulo permite que cada tenant (cliente del SaaS) tenga su propia **tienda online pública** donde los clientes finales pueden:

- ✅ Ver productos disponibles con fotos y precios
- ✅ Buscar y filtrar por categorías
- ✅ Agregar productos al carrito de forma intuitiva
- ✅ Realizar pedidos sin necesidad de registro
- ✅ Confirmar pedido directamente por WhatsApp

## 🎨 Características de Diseño

### Mobile-First & Moderno
- Diseño inspirado en apps de delivery (Rappi, UberEats, Instacart)
- Interfaz minimalista con fotos grandes y espacios en blanco
- Animaciones suaves y transiciones elegantes
- Header sticky con búsqueda y filtros horizontales scrolleables

### Componentes Destacados

#### 1. **ProductCard.vue** - Tarjeta de Producto Inteligente
- Foto grande en formato cuadrado (aspect-ratio 1:1)
- Botón de acción que cambia de estado:
  - **Inactivo**: Botón "Agregar" verde brillante
  - **Activo**: Control `[ - 1 + ]` para ajustar cantidad
- Badge de "Últimas X unidades" cuando el stock es bajo
- Overlay de "Agotado" cuando no hay stock

#### 2. **FloatingCartBar.vue** - Barra Flotante del Carrito
- **Posición**: Fixed en la parte inferior (z-50)
- **Animación**: Bounce sutil al aparecer
- **Contenido**: 
  - Icono de carrito con badge animado de cantidad
  - Total del pedido en grande
  - Flecha con animación de bounce continuo
- **Acción**: Click abre el Checkout Drawer

#### 3. **CheckoutDrawer.vue** - Panel de Checkout
- **Desktop**: Desliza desde la derecha (480px ancho)
- **Mobile**: Desliza desde abajo (90% altura viewport)
- **Formulario ultra-simple**:
  - Nombre completo
  - Teléfono/WhatsApp
  - Método de entrega (Pickup/Delivery)
  - Dirección (solo si es delivery)
  - Notas opcionales
- **Botón final**: "Enviar por WhatsApp" con icono

## 🗄️ Estructura Backend (Laravel)

### Migraciones Creadas

#### 1. `add_public_fields_to_products_table.php`
```sql
ALTER TABLE products ADD:
- is_public (BOOLEAN, default: TRUE)
- public_description (TEXT, nullable)
- public_image (VARCHAR, nullable)
- INDEX (is_public, active)
```

#### 2. `create_online_orders_table.php`
```sql
CREATE TABLE online_orders (
  id, uuid, order_number,
  customer_name, customer_phone, customer_address,
  delivery_type ENUM('pickup', 'delivery'),
  status ENUM('pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled'),
  subtotal, tax, delivery_fee, total,
  note, confirmed_at, completed_at, cancelled_at,
  created_at, updated_at
)
```

#### 3. `create_online_order_items_table.php`
```sql
CREATE TABLE online_order_items (
  id, online_order_id, product_id,
  product_name, product_sku,
  quantity, unit_price, subtotal,
  special_instructions,
  created_at, updated_at
)
```

### Modelos

#### **OnlineOrder.php**
- **Métodos principales**:
  - `generateOrderNumber()`: Genera código único (PED-001, PED-002...)
  - `calculateTotal()`: Calcula el total basado en items
  - `confirm()`, `complete()`, `cancel()`: Cambio de estados
  - `generateWhatsAppMessage()`: Formatea el pedido para WhatsApp
  - `getWhatsAppLink()`: Genera el enlace wa.me con mensaje pre-llenado

#### **OnlineOrderItem.php**
- Snapshot de información del producto (nombre, SKU, precio)
- Calcula subtotal automáticamente al crear

#### **Product.php** (extendido)
- Nuevos scopes:
  - `public()`: Productos marcados como públicos y activos
  - `availableForOnline()`: Públicos + con stock > 0

### Controlador Público

#### **PublicCatalogController.php**
**Rutas NO autenticadas** (prefijo: `/api/public`)

```php
GET  /catalog              → index()      // Lista productos públicos
GET  /catalog/categories   → categories() // Categorías con productos disponibles
POST /orders               → store()      // Crea nuevo pedido
GET  /orders/{uuid}        → show()       // Consulta pedido por UUID
```

**Características del `store()`**:
- ✅ Valida stock en tiempo real antes de crear el pedido
- ✅ Usa transacciones para garantizar integridad
- ✅ Genera UUID único y order_number amigable
- ✅ Crea snapshot de productos (nombre, precio, SKU)
- ✅ Retorna enlace de WhatsApp listo para usar

## 🎯 Flujo de Usuario

1. **Exploración**:
   - Usuario entra a `mitienda.tupos.com/catalogo`
   - Ve grid de productos con fotos, nombres y precios
   - Puede buscar o filtrar por categorías

2. **Selección**:
   - Click en "Agregar" → Producto se agrega al carrito
   - Aparece control `[ - 1 + ]` en la tarjeta
   - FloatingCartBar aparece en la parte inferior con animación

3. **Checkout**:
   - Click en FloatingCartBar → Se abre CheckoutDrawer
   - Usuario ve resumen de su pedido
   - Llena formulario simple (nombre, teléfono, método de entrega)

4. **Confirmación**:
   - Click en "Enviar por WhatsApp"
   - Sistema valida stock y crea el pedido
   - Muestra modal de éxito con número de pedido
   - Botón directo a WhatsApp con mensaje pre-llenado

5. **WhatsApp**:
   - Se abre WhatsApp con mensaje formateado:
     ```
     🛒 Nuevo Pedido #PED-042
     
     👤 Cliente: Juan Pérez
     📱 Teléfono: 3001234567
     📦 Tipo: Domicilio
     📍 Dirección: Calle 123 #45-67
     
     Productos:
     • 2x Producto A - $20,000
     • 1x Producto B - $15,000
     
     💰 Total: $55,000
     
     📝 Nota: Sin cebolla
     ```

## 🔧 Configuración Necesaria

### 1. Actualizar `Product.php` (Modelo)
```php
protected $fillable = [
    // ... campos existentes ...
    'is_public',
    'public_description',
    'public_image'
];

protected $casts = [
    // ... casts existentes ...
    'is_public' => 'boolean',
];
```

### 2. Configurar Número de WhatsApp del Negocio
En `system_settings` o variable de entorno, agregar el número de WhatsApp del negocio para que el controlador lo use al generar los enlaces.

Ejemplo:
```php
// En SystemSettings
'whatsapp_business_phone' => '573001234567'
```

### 3. Rutas Públicas (Sin Autenticación)
Ya configuradas en `routes/tenant.php`:
```php
Route::prefix('api/public')->group(function () {
    Route::get('/catalog', [PublicCatalogController::class, 'index']);
    Route::get('/catalog/categories', [PublicCatalogController::class, 'categories']);
    Route::post('/orders', [PublicCatalogController::class, 'store']);
    Route::get('/orders/{uuid}', [PublicCatalogController::class, 'show']);
});
```

## 📱 Componentes Vue

### Ubicación de Archivos
```
src/
├── components/
│   └── catalog/
│       ├── ProductCard.vue         → Tarjeta de producto
│       ├── FloatingCartBar.vue     → Barra flotante del carrito
│       └── CheckoutDrawer.vue      → Panel de checkout
└── views/
    └── PublicCatalog.vue           → Vista principal del catálogo
```

### Estado Global (Vue Composition API)
```javascript
const products = ref([])        // Productos del catálogo
const categories = ref([])      // Categorías disponibles
const cart = ref([])            // Carrito de compras
const searchQuery = ref('')     // Búsqueda actual
const selectedCategory = ref(null) // Categoría seleccionada
```

### Gestión del Carrito
```javascript
// Estructura de un item en el carrito
{
  id: 1,
  name: 'Producto A',
  price: 10000,
  image: '/path/to/image.jpg',
  quantity: 2
}

// Actualizar cantidad
updateCart({ productId, quantity })
```

## 🎨 Paleta de Colores

### Verde Empresarial (Acciones Principales)
- **Botones primarios**: `from-lime-400 to-green-400`
- **Hover**: `from-lime-500 to-green-500`
- **Textos destacados**: `text-lime-600`

### Backgrounds
- **Página**: `bg-gradient-to-br from-gray-50 to-gray-100`
- **Tarjetas**: `bg-white`
- **Header sticky**: `bg-white bg-opacity-95`

### Estados
- **Stock bajo**: `bg-orange-500` (badge)
- **Sin stock**: `bg-red-500` (overlay)
- **Éxito**: `bg-green-100` (modal de confirmación)

## 🚀 Próximos Pasos (PASO 3)

1. **Panel de Gestión de Pedidos Online** (Admin)
   - Vista para ver todos los pedidos recibidos
   - Cambiar estados (pending → confirmed → preparing → ready → completed)
   - Filtros por fecha, estado, cliente
   - Notificaciones en tiempo real

2. **Configuración del Catálogo**
   - Toggle para activar/desactivar catálogo público
   - Personalizar mensaje de bienvenida
   - Configurar horarios de atención
   - Gestionar métodos de entrega y precios

3. **Analytics del Catálogo**
   - Productos más vistos
   - Tasa de conversión (visitas → pedidos)
   - Promedio de ticket
   - Productos abandonados en carrito

## 📊 Base de Datos - Estado Actual

### Tablas Creadas ✅
- ✅ `online_orders` - Pedidos del catálogo
- ✅ `online_order_items` - Items de cada pedido
- ✅ `products` (extendida) - Campos públicos agregados

### Datos de Ejemplo
Para probar el catálogo, asegúrate de tener productos con:
```sql
UPDATE products 
SET is_public = TRUE, 
    public_description = 'Descripción vendedora del producto',
    public_image = 'url_de_imagen_optimizada.jpg'
WHERE active = TRUE;
```

## 🔐 Seguridad

- ✅ Rutas públicas sin autenticación (solo lectura de productos)
- ✅ Validación de stock en tiempo real al crear pedido
- ✅ Transacciones de base de datos para integridad
- ✅ Sanitización de inputs en el controlador
- ✅ Rate limiting recomendado (configurable en Laravel)

## 📝 Notas Importantes

1. **Dominio del Tenant**: El catálogo funciona en el subdominio del tenant (ej: `cliente1.tupos.com/catalogo`)

2. **Gestión de Imágenes**: 
   - `public_image` puede ser diferente a `image_url`
   - Permite tener una imagen optimizada para web pública
   - Si está vacío, se usa `image_url` como fallback

3. **Stock Management**: 
   - El catálogo solo muestra productos con `current_stock > 0`
   - Validación de stock al crear pedido previene overselling

4. **WhatsApp Integration**:
   - Enlace `wa.me` funciona en cualquier dispositivo
   - Mensaje pre-formateado con emojis para mejor UX
   - El cliente puede editar el mensaje antes de enviar

## 🎉 Resultado Final

Un **catálogo online moderno y funcional** que:
- ✨ Se ve profesional en móviles y desktop
- 🚀 Carga rápido y es fácil de usar
- 💚 Convierte visitantes en clientes por WhatsApp
- 📦 Permite gestionar pedidos desde el POS

---

**Versión**: 1.0  
**Fecha**: 28 de Noviembre de 2025  
**Estado**: Completado ✅
