# 📋 Implementación Formulario de Checkout - Catálogo Web

## ✅ Backend Completado

El backend ya está 100% funcional:
- ✅ Endpoint: `POST /api/orders` - Crear pedido
- ✅ Endpoint: `POST /api/orders/find-by-code` - Buscar pedido por código (para POS)
- ✅ Validaciones completas
- ✅ Genera código único de pedido (PED-XXX)
- ✅ Verifica stock antes de crear
- ✅ Modelo `OnlineOrder` con UUID

## 🎯 Lo que Falta Implementar en Frontend

### 1. Componente CheckoutForm.vue ✅ CREADO

Ubicación: `/src/components/catalog/CheckoutForm.vue`

Campos:
- ✅ Nombre completo (obligatorio)
- ✅ Teléfono (obligatorio)
- ✅ Cédula/Documento (obligatorio, mín 6 caracteres)
- ✅ Correo electrónico (opcional)
- ✅ Tipo de entrega: Envío a domicilio / Recoger en tienda
- ✅ Dirección (si es envío a domicilio)
- ✅ Nota o instrucciones especiales (opcional)

### 2. Modificar Template B (CatalogTemplateB.vue)

**Imports necesarios:**
```javascript
import CheckoutForm from './CheckoutForm.vue'
import axios from 'axios'
```

**Estados nuevos:**
```javascript
const showCheckoutForm = ref(false)
const submittingOrder = ref(false)
const orderCreated = ref(null) // Para mostrar código de pedido
```

**Cambiar botón WhatsApp por:**
```vue
<button 
  @click="showCheckoutForm = true"
  :disabled="cartTotal < storeConfig.min_order_value || cartItems.length === 0"
  class="w-full bg-gradient-to-r from-[#25D366] to-[#1ebe57] ... "
>
  <svg>...</svg>
  <span>Completar Pedido</span>
</button>
```

**Agregar componente CheckoutForm antes de closing </div>:**
```vue
<!-- Formulario de Checkout -->
<CheckoutForm 
  :show="showCheckoutForm"
  :cartItems="cartItems"
  @close="showCheckoutForm = false"
  @submit="handleCheckoutSubmit"
/>
```

**Función nueva para crear pedido:**
```javascript
const handleCheckoutSubmit = async (formData) => {
  try {
    submittingOrder.value = true
    
    // Preparar items del pedido
    const items = groupedCartItems.value.map(item => ({
      product_id: item.id,
      quantity: item.quantity,
      special_instructions: item.special_instructions || null
    }))
    
    // Enviar al backend
    const response = await axios.post('/api/orders', {
      ...formData,
      items
    })
    
    if (response.data.success) {
      orderCreated.value = response.data.order
      
      // Cerrar formulario y carrito
      showCheckoutForm.value = false
      showCheckout.value = false
      
      // Vaciar carrito
      cartItems.value = []
      
      // Mostrar modal de éxito con código de pedido
      showSuccessModal(response.data.order)
    }
  } catch (error) {
    console.error('Error al crear pedido:', error)
    alert('Error al procesar tu pedido. Por favor intenta nuevamente.')
  } finally {
    submittingOrder.value = false
  }
}

const showSuccessModal = (order) => {
  // Crear mensaje para WhatsApp
  const message = `¡Hola! Mi código de pedido es: *${order.order_number}*\n\nTotal: ${props.storeConfig.currency_symbol}${formatPrice(order.total)}`
  
  const whatsappUrl = `https://wa.me/${props.storeConfig.whatsapp_number}?text=${encodeURIComponent(message)}`
  
  // Mostrar modal con código y botón de WhatsApp
  alert(`✅ ¡Pedido creado exitosamente!\n\nCódigo: ${order.order_number}\n\nGuarda este código para rastrearlo.`)
  
  // Abrir WhatsApp
  window.open(whatsappUrl, '_blank')
}
```

### 3. Aplicar lo Mismo a Template A y Template C

Copiar la misma lógica:
- Importar `CheckoutForm`
- Agregar estados
- Cambiar botón WhatsApp
- Agregar componente
- Implementar `handleCheckoutSubmit`

### 4. Modal de Éxito (Opcional - Mejorar UX)

Crear un componente `OrderSuccessModal.vue` para mostrar:
- ✅ Pedido creado exitosamente
- 📦 Código del pedido (grande y destacado)
- 💾 Botón "Copiar código"
- 💬 Botón "Enviar por WhatsApp"
- ℹ️ Instrucciones: "Guarda este código para rastrearlo en tienda"

### 5. Integración con POS

**Ya existe** el endpoint en backend: `POST /api/orders/find-by-code`

En el POS, agregar un botón/opción para:
1. Ingresar código de pedido (PED-XXX)
2. Llamar al endpoint
3. Cargar automáticamente:
   - Cliente (nombre, teléfono, documento)
   - Productos en el carrito
   - Cantidades
   - Total

**Código ejemplo para POS:**
```javascript
const loadOnlineOrder = async (orderCode) => {
  try {
    const response = await axios.post('/api/orders/find-by-code', {
      code: orderCode
    })
    
    if (response.data.success) {
      const order = response.data.order
      
      // Cargar cliente
      selectedCustomer.value = {
        name: order.customer_name,
        phone: order.customer_phone,
        document: order.customer_document,
        address: order.customer_address
      }
      
      // Cargar productos al carrito
      order.items.forEach(item => {
        addToCart({
          id: item.product_id,
          name: item.product_name,
          price: item.unit_price,
          quantity: item.quantity
        })
      })
      
      alert(`✅ Pedido ${order.order_number} cargado exitosamente!`)
    }
  } catch (error) {
    alert('❌ Pedido no encontrado')
  }
}
```

## 🔧 Pasos de Implementación

1. ✅ CheckoutForm.vue ya está creado
2. ⏳ Modificar Template B con la lógica completa
3. ⏳ Modificar Template A (copiar lógica de B)
4. ⏳ Modificar Template C (copiar lógica de B)
5. ⏳ Agregar botón en POS para cargar pedidos online
6. ⏳ Probar flujo completo:
   - Crear pedido desde catálogo
   - Recibir código
   - Cargar en POS con el código
   - Completar venta

## 📝 Notas Importantes

- El código de pedido es único y se genera automáticamente (formato: PED-001, PED-002, etc.)
- El backend ya valida stock antes de crear el pedido
- Los pedidos tienen estados: pending, processing, completed, cancelled
- El formulario tiene validación completa
- El correo es opcional, los demás campos son obligatorios

## 🐛 Error de WhatsApp Actual

El error que ves:
```
ErrorUtils caught an error: multiple-uim-roots
```

Es porque el código actual intenta abrir WhatsApp directamente sin crear el pedido primero. Con la nueva implementación esto se soluciona porque:
1. Primero se crea el pedido en BD
2. Se obtiene el código único
3. Luego se envía por WhatsApp con el código incluido
