<template>
  <div class="min-h-screen bg-white relative">
    
    <!-- HEADER -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm" style="box-shadow: 0 1px 0 rgba(0,0,0,0.06);">
      <div class="flex items-center justify-between px-4 h-12">
        <button 
          @click="goBack"
          class="flex items-center gap-1 text-gray-700 active:text-gray-900 transition-colors -ml-1"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
          </svg>
        </button>
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-[0.08em]">
          {{ showCheckoutForm ? 'Finalizar Compra' : 'Tu Bolsa' }}
        </h2>
        <div class="w-5"></div>
      </div>
    </header>

    <!-- ========== CART VIEW ========== -->
    <template v-if="!showCheckoutForm">
      
      <!-- Empty State -->
      <div v-if="cartItems.length === 0" class="flex flex-col items-center justify-center text-center py-20 px-6">
        <div class="w-20 h-20 border border-gray-200 rounded-full flex items-center justify-center mb-4">
          <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
          </svg>
        </div>
        <h4 class="text-base font-medium text-gray-900">Tu bolsa está vacía</h4>
        <p class="text-gray-500 text-sm mt-1 max-w-[200px]">Añade artículos para comenzar</p>
        <button @click="$router.push('/catalog')" class="mt-6 text-sm font-medium text-gray-900 underline underline-offset-4 hover:no-underline">
          Continuar comprando
        </button>
      </div>

      <!-- Cart Items -->
      <div v-else>
        <div class="px-5 py-4 space-y-0">
          <div v-for="item in cartItems" :key="item.id" class="flex gap-4 py-5 border-b border-gray-100 last:border-0">
            <!-- Product thumbnail 3:4 -->
            <div class="w-24 aspect-[3/4] flex-shrink-0 bg-gray-50 overflow-hidden">
              <img 
                v-if="item.image_url" 
                :src="item.image_url" 
                class="w-full h-full object-cover"
                @error="(e) => e.target.src = ''"
              />
              <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
            <!-- Info -->
            <div class="flex-1 min-w-0 flex flex-col justify-between">
              <div>
                <h4 class="font-semibold text-gray-900 text-sm leading-snug">{{ item.name }}</h4>
                <p class="text-xs text-gray-400 mt-1">{{ item.variant_name || 'Unidad' }}</p>
              </div>
              <div class="mt-3">
                <p class="font-semibold text-gray-900 text-sm">{{ currencySymbol }}{{ formatPrice(item.price) }}</p>
                <button @click="removeItem(item.id)" class="mt-2 text-xs text-gray-400 hover:text-gray-900 underline underline-offset-2 transition-colors">
                  Eliminar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Totals + CTA -->
        <div class="px-5 py-6 space-y-4">
          <div class="space-y-3">
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">Subtotal</span>
              <span class="text-gray-900">{{ currencySymbol }}{{ formatPrice(cartTotal) }}</span>
            </div>
            <div class="flex justify-between items-baseline pt-3 border-t border-gray-100">
              <span class="text-sm font-medium text-gray-900 uppercase tracking-wide">Total</span>
              <span class="text-xl font-bold text-gray-900">{{ currencySymbol }}{{ formatPrice(cartTotal) }}</span>
            </div>
            <p class="text-[11px] text-gray-400 text-center">Envío calculado en el siguiente paso</p>
          </div>

          <!-- Min order warning -->
          <div v-if="storeConfig && cartTotal < storeConfig.min_order_value" class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-start gap-3">
            <svg class="h-4 w-4 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <div class="text-xs text-gray-600">
              <p class="font-medium">Pedido mínimo: {{ currencySymbol }}{{ formatPrice(storeConfig.min_order_value) }}</p>
              <p class="mt-0.5 text-gray-400">
                Agrega {{ currencySymbol }}{{ formatPrice(storeConfig.min_order_value - cartTotal) }} más
              </p>
            </div>
          </div>

          <button 
            @click="showCheckoutForm = true"
            :disabled="storeConfig && cartTotal < storeConfig.min_order_value"
            class="w-full bg-gray-900 hover:bg-black disabled:bg-gray-200 disabled:text-gray-400 text-white py-4 text-sm font-semibold uppercase tracking-[0.15em] transition-all disabled:cursor-not-allowed rounded-xl"
          >
            FINALIZAR COMPRA
          </button>

          <button @click="$router.push('/catalog')" class="w-full text-center text-sm font-medium text-gray-500 hover:text-gray-900 underline underline-offset-4 transition-colors py-2">
            Continuar comprando
          </button>
        </div>
      </div>
    </template>

    <!-- ========== CHECKOUT FORM - One-Page Compact ========== -->
    <template v-else>
      <div class="bg-gray-50/80 min-h-[calc(100vh-48px)] pb-32">

        <!-- Back link -->
        <div class="px-4 pt-3 pb-2">
          <button 
            @click="showCheckoutForm = false" 
            class="flex items-center gap-1.5 text-gray-400 hover:text-gray-700 text-xs font-medium transition-colors"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Volver al carrito
          </button>
        </div>

        <!-- Card: Datos del Cliente -->
        <div class="mx-4 bg-white rounded-lg shadow-sm border border-gray-100 p-4 space-y-3">
          <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wide">Datos del Cliente</h3>

          <!-- Cédula -->
          <div>
            <label class="block text-[11px] font-medium text-gray-500 mb-1">Cédula / Documento <span class="text-red-400">*</span></label>
            <div class="relative">
              <input 
                v-model="formData.customer_document"
                @blur="searchCustomerByDocument"
                type="text"
                required
                minlength="6"
                :disabled="searchingCustomer"
                placeholder="Ej: 1234567890"
                class="w-full py-2 px-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all disabled:opacity-50"
                style="font-size: 16px;"
              />
              <div v-if="searchingCustomer" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Nombre -->
          <div>
            <label class="block text-[11px] font-medium text-gray-500 mb-1">Nombre Completo <span class="text-red-400">*</span></label>
            <input 
              v-model="formData.customer_name"
              type="text"
              required
              placeholder="Tu nombre completo"
              class="w-full py-2 px-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all"
              style="font-size: 16px;"
            />
          </div>

          <!-- Teléfono + Email en fila -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-medium text-gray-500 mb-1">Teléfono <span class="text-red-400">*</span></label>
              <input 
                v-model="formData.customer_phone"
                type="tel"
                required
                placeholder="300 123 4567"
                class="w-full py-2 px-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all"
                style="font-size: 16px;"
              />
            </div>
            <div>
              <label class="block text-[11px] font-medium text-gray-500 mb-1">Email <span class="text-gray-400">(Opc.)</span></label>
              <input 
                v-model="formData.customer_email"
                type="email"
                placeholder="correo@email.com"
                class="w-full py-2 px-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all"
                style="font-size: 16px;"
              />
            </div>
          </div>
        </div>

        <!-- Card: Entrega -->
        <div class="mx-4 mt-3 bg-white rounded-lg shadow-sm border border-gray-100 p-4 space-y-3">
          <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wide">Tipo de Entrega</h3>

          <!-- Segmented Control -->
          <div class="flex bg-gray-100 rounded-lg p-0.5">
            <button 
              @click="formData.delivery_type = 'delivery'"
              type="button"
              class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-md text-xs font-semibold transition-all duration-200"
              :class="formData.delivery_type === 'delivery' 
                ? 'bg-gray-900 text-white shadow-sm' 
                : 'text-gray-500 hover:text-gray-700'"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" /></svg>
              Domicilio
            </button>
            <button 
              @click="formData.delivery_type = 'pickup'"
              type="button"
              class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-md text-xs font-semibold transition-all duration-200"
              :class="formData.delivery_type === 'pickup' 
                ? 'bg-gray-900 text-white shadow-sm' 
                : 'text-gray-500 hover:text-gray-700'"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
              Recoger en tienda
            </button>
          </div>

          <!-- Address (only delivery) -->
          <div v-if="formData.delivery_type === 'delivery'">
            <label class="block text-[11px] font-medium text-gray-500 mb-1">Dirección de Entrega <span class="text-red-400">*</span></label>
            <textarea 
              v-model="formData.customer_address"
              required
              rows="2"
              placeholder="Calle, número, barrio, ciudad"
              class="w-full py-2 px-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all resize-none"
              style="font-size: 16px;"
            ></textarea>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-[11px] font-medium text-gray-500 mb-1">Notas <span class="text-gray-400">(Opcional)</span></label>
            <input 
              v-model="formData.note"
              type="text"
              placeholder="Color, talla u otra indicación"
              class="w-full py-2 px-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all"
              style="font-size: 16px;"
            />
          </div>
        </div>
      </div>

      <!-- Sticky Footer - Compact -->
      <div class="fixed bottom-0 left-0 right-0 bg-white z-10 border-t border-gray-200">
        <div class="px-4 pt-3 pb-2">
          <div class="flex items-center justify-between mb-2.5">
            <div>
              <p class="text-[10px] text-gray-400 uppercase tracking-wide font-medium leading-none">Total</p>
              <p class="text-[11px] text-gray-500 mt-0.5">{{ cartItems.length }} {{ cartItems.length !== 1 ? 'productos' : 'producto' }} · {{ formData.delivery_type === 'delivery' ? 'Envío' : 'Recogida' }}</p>
            </div>
            <span class="text-lg font-bold text-gray-900">
              {{ currencySymbol }}{{ formatPrice(cartTotal + (formData.delivery_type === 'delivery' ? (storeConfig?.delivery_cost || 0) : 0)) }}
            </span>
          </div>
        </div>
        <div class="px-4 pb-4">
          <button 
            @click="handleCheckoutSubmit"
            :disabled="submittingOrder || !formData.customer_name || !formData.customer_phone || !formData.customer_document || formData.customer_document.length < 6 || (formData.delivery_type === 'delivery' && !formData.customer_address)"
            class="w-full bg-[#25D366] hover:bg-[#1ebe57] disabled:bg-gray-200 disabled:text-gray-400 text-white py-3 text-sm font-bold uppercase tracking-[0.08em] transition-all disabled:cursor-not-allowed flex items-center justify-center gap-2.5 rounded-xl shadow-md shadow-emerald-200/50"
          >
            <svg v-if="!submittingOrder" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
            <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ submittingOrder ? 'Procesando...' : 'Confirmar vía WhatsApp' }}</span>
          </button>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '../../services/apiClient.js'
import { useCatalogCart } from '../../stores/catalogCart.js'
import { useToast } from '../../composables/useToast.js'

const router = useRouter()
const { cartItems, cartTotal, removeItem, clearCart } = useCatalogCart()
const { showError } = useToast()

const storeConfig = ref(null)
const showCheckoutForm = ref(false)
const submittingOrder = ref(false)
const searchingCustomer = ref(false)

const formData = ref({
  customer_name: '',
  customer_phone: '',
  customer_document: '',
  customer_email: '',
  delivery_type: 'delivery',
  customer_address: '',
  note: ''
})

const currencySymbol = computed(() => storeConfig.value?.currency_symbol || '$')

const formatPrice = (price) => {
  return Number(price).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const goBack = () => {
  if (showCheckoutForm.value) {
    showCheckoutForm.value = false
  } else if (window.history.length > 1) {
    router.back()
  } else {
    router.push('/catalog')
  }
}

const loadConfig = async () => {
  try {
    const response = await apiClient.get('/public/catalog/config')
    if (response.data.success && response.data.data) {
      const d = response.data.data
      storeConfig.value = {
        currency_symbol: '$',
        delivery_cost: parseFloat(d.delivery_cost || 0),
        min_order_value: parseFloat(d.minimum_order || 0),
        whatsapp_number: d.whatsapp_number || '',
        store_name: d.store_name || 'Mi Tienda',
        custom_message: d.custom_message || 'Hola, quiero hacer el siguiente pedido:'
      }
    }
  } catch (e) {
    // Use defaults
  }
}

const searchCustomerByDocument = async () => {
  if (!formData.value.customer_document || formData.value.customer_document.length < 6) return
  try {
    searchingCustomer.value = true
    const response = await apiClient.post('/public/customers/find-by-document', {
      document: formData.value.customer_document
    })
    if (response.data.success && response.data.found) {
      formData.value.customer_name = response.data.customer.name
      formData.value.customer_phone = response.data.customer.phone
      formData.value.customer_email = response.data.customer.email || ''
      formData.value.customer_address = response.data.customer.address || ''
    }
  } catch (_) {
    // Manual fill allowed
  } finally {
    searchingCustomer.value = false
  }
}

const handleCheckoutSubmit = async () => {
  if (!storeConfig.value) return
  if (cartTotal.value < storeConfig.value.min_order_value) return

  try {
    submittingOrder.value = true

    const items = cartItems.value.map(item => ({
      product_id: item.id,
      quantity: item.quantity || 1,
      special_instructions: item.special_instructions || null
    }))

    const response = await apiClient.post('/public/orders', {
      ...formData.value,
      items
    })

    if (response.data.success) {
      const order = response.data.order
      const customerData = { ...formData.value }
      const orderItems = [...cartItems.value]

      // Clear cart
      clearCart()

      // Reset form
      formData.value = {
        customer_name: '',
        customer_phone: '',
        customer_document: '',
        customer_email: '',
        delivery_type: 'delivery',
        customer_address: '',
        note: ''
      }
      showCheckoutForm.value = false

      // Build WhatsApp message
      const greeting = storeConfig.value.custom_message || 'Hola, quiero hacer el siguiente pedido:'
      let message = `${greeting}\n\n`
      message += `*Código: ${order.order_number}*\n\n`
      message += `${customerData.customer_name}\n`
      message += `${customerData.customer_phone}\n\n`

      if (customerData.delivery_type === 'delivery') {
        message += `Envío a: ${customerData.customer_address}\n\n`
      } else {
        message += `Recoger en tienda\n\n`
      }

      message += `*Productos:*\n`
      orderItems.forEach((item, index) => {
        message += `${index + 1}. ${item.name} x${item.quantity || 1}\n`
      })

      const deliveryCost = customerData.delivery_type === 'delivery' ? parseFloat(storeConfig.value.delivery_cost || 0) : 0
      const finalTotal = parseFloat(order.total) + deliveryCost
      message += `\nTotal: ${storeConfig.value.currency_symbol}${formatPrice(finalTotal)}`

      if (customerData.note) {
        message += `\n\n${customerData.note}`
      }

      const whatsappUrl = `https://wa.me/${storeConfig.value.whatsapp_number}?text=${encodeURIComponent(message)}`
      window.open(whatsappUrl, '_blank')

      // Navigate back to catalog
      router.push('/catalog')
    }
  } catch (e) {
    showError('Error al crear el pedido. Por favor intenta nuevamente.')
  } finally {
    submittingOrder.value = false
  }
}

onMounted(() => {
  loadConfig()
})
</script>
