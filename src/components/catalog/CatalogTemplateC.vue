<template>
  <!-- PLANTILLA C: "MODERN GRID" - Estilo Original Lime/Green -->
  <div class="catalog-modern-grid min-h-screen bg-white font-sans selection:bg-brand selection:text-white overflow-x-hidden">
    
    <!-- Background Blobs -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden opacity-30">
      <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-brand/15 rounded-full blur-[120px] animate-blob"></div>
      <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-blue-50 rounded-full blur-[120px] animate-blob animation-delay-2000"></div>
      <div class="absolute top-[40%] left-[40%] w-[40%] h-[40%] bg-purple-50 rounded-full blur-[120px] animate-blob animation-delay-4000"></div>
    </div>

    <!-- Floating Glass Header -->
    <header 
      class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
      :class="isScrolled ? 'bg-white/95 backdrop-blur-xl shadow-md py-2.5' : 'bg-white/80 backdrop-blur-xl py-3 shadow-sm'"
    >
      <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 md:gap-6">
          
          <!-- Logo -->
          <div class="flex items-center gap-2.5 cursor-pointer group" @click="clearFilters">
            <div v-if="storeConfig.logo_url" class="w-9 h-9 md:w-10 md:h-10 rounded-lg overflow-hidden shadow-md group-hover:scale-105 transition-transform duration-300">
              <img :src="storeConfig.logo_url" class="w-full h-full object-cover" />
            </div>
            <div v-else class="w-9 h-9 md:w-10 md:h-10 bg-gradient-to-br from-brand to-brand-dark rounded-lg flex items-center justify-center shadow-md group-hover:rotate-6 transition-transform duration-300">
              <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
            </div>
            <div class="hidden sm:block">
              <h1 class="text-base md:text-lg font-black text-slate-800 tracking-tight leading-tight">{{ storeConfig.store_name }}</h1>
            </div>
          </div>

          <!-- Search (Desktop) -->
          <div class="hidden md:block flex-1 max-w-xl mx-6">
            <div class="relative group">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400 group-focus-within:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <input 
                v-model="searchQuery"
                type="text"
                placeholder="Buscar productos..."
                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 focus:bg-white focus:border-brand focus:ring-2 focus:ring-brand/20 transition-all"
              />
            </div>
          </div>

          <!-- Cart Button -->
          <button 
            @click="showCheckout = true"
            class="hidden md:flex items-center gap-2.5 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl transition-all shadow-md hover:shadow-lg group"
          >
            <div class="relative">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
              <span v-if="cartItems.length > 0" class="absolute -top-2 -right-2 min-w-[18px] h-[18px] bg-brand text-slate-900 text-[10px] font-bold rounded-full flex items-center justify-center px-1">
                {{ cartItems.length }}
              </span>
            </div>
            <span class="font-bold text-sm">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Hero Section - Compacto para desktop -->
    <div class="relative pt-24 pb-6 md:pt-28 md:pb-8 px-4">
      <div class="max-w-7xl mx-auto text-center relative z-10">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 mb-3 tracking-tight leading-none">
          Calidad que <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand to-brand-dark">Inspira</span>
        </h2>
        <p class="text-base md:text-lg text-slate-500 max-w-2xl mx-auto leading-relaxed">
          Descubre productos seleccionados con el mejor diseño y calidad garantizada.
        </p>
      </div>
    </div>

    <!-- Categories Pills -->
    <div class="sticky top-[52px] md:top-[58px] z-40 mb-6 md:mb-8 bg-white/80 backdrop-blur-md border-b border-slate-200/50 py-3 md:py-4 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="flex justify-start md:justify-center overflow-x-auto scrollbar-hide">
          <div class="flex space-x-2 md:space-x-3">
            <button 
              @click="selectedCategory = null"
              class="px-4 md:px-5 py-2 md:py-2.5 rounded-lg text-sm md:text-base font-bold transition-all duration-200 whitespace-nowrap"
              :class="selectedCategory === null ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            >
              Todos
            </button>
            <button 
              v-for="cat in categories"
              :key="cat.id"
              @click="selectedCategory = cat.id"
              class="px-4 md:px-5 py-2 md:py-2.5 rounded-lg text-sm md:text-base font-bold transition-all duration-200 whitespace-nowrap"
              :class="selectedCategory === cat.id ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            >
              {{ cat.name }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32 md:pb-12 relative z-10">
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
        <TransitionGroup name="list">
          <div 
            v-for="product in filteredProducts"
            :key="product.id"
            class="bg-white rounded-2xl p-3 md:p-4 border border-slate-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group"
          >
            <!-- Product Image -->
            <div class="relative aspect-square rounded-xl overflow-hidden mb-3 bg-gradient-to-br from-gray-100 to-gray-200">
              
              <!-- Imagen del Producto -->
              <img 
                v-if="(product.images && product.images.length > 0) || (product.image_url && product.image_url !== 'https://via.placeholder.com/400' && !imageErrors[product.id])"
                :src="product.images && product.images.length > 0 ? product.images[0] : product.image_url"
                :alt="product.name"
                @error="handleImageError(product.id)"
                class="w-full h-full object-cover"
              />
              
              <!-- Placeholder cuando no hay imagen -->
              <div v-else class="w-full h-full flex items-center justify-center">
                <div class="text-center">
                  <svg class="w-16 h-16 md:w-20 md:h-20 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-xs text-gray-400 font-medium">Sin imagen</p>
                </div>
              </div>
              <!-- Stock Badge -->
              <div v-if="product.stock === 0" class="absolute inset-0 bg-black/60 flex items-center justify-center">
                <span class="text-white text-xs font-bold bg-red-500 px-3 py-1 rounded-full">Agotado</span>
              </div>
              <div v-else-if="product.stock < 5" class="absolute top-2 right-2 bg-amber-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                Solo {{ product.stock }}
              </div>
            </div>

            <!-- Product Info -->
            <div class="space-y-2">
              <h3 class="font-bold text-slate-900 text-sm md:text-base line-clamp-2 min-h-[40px] md:min-h-[48px]">{{ product.name }}</h3>
              <div class="flex items-center justify-between">
                <p class="text-lg md:text-xl font-black text-brand">
                  {{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}
                </p>
              </div>
              
              <!-- Add to Cart -->
              <button
                v-if="getProductQuantity(product.id) === 0"
                @click="addToCart(product)"
                :disabled="product.stock === 0"
                class="w-full py-2 md:py-2.5 bg-slate-900 hover:bg-slate-800 disabled:bg-gray-300 text-white font-bold rounded-lg transition-colors text-sm md:text-base disabled:cursor-not-allowed"
              >
                Agregar
              </button>
              <div v-else class="flex items-center gap-2">
                <button @click="decreaseQuantity(product.id)" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition-colors">
                  -
                </button>
                <span class="px-3 font-bold text-slate-900">{{ getProductQuantity(product.id) }}</span>
                <button @click="increaseQuantity(product.id, product)" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg transition-colors">
                  +
                </button>
              </div>
            </div>
          </div>
        </TransitionGroup>
      </div>

      <!-- Empty State -->
      <div v-if="filteredProducts.length === 0" class="text-center py-20">
        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Sin resultados</h3>
        <button @click="clearFilters" class="px-6 py-2 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition-colors">
          Ver Todo
        </button>
      </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white pt-12 pb-24 md:pb-8 relative z-10">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="text-slate-400 text-sm">© 2025 {{ storeConfig.store_name }}</p>
      </div>
    </footer>

    <!-- Floating Cart FAB (Mobile) -->
    <Transition name="scale">
      <button 
        v-if="cartItems.length > 0"
        @click="showCheckout = true"
        class="md:hidden fixed bottom-6 right-6 bg-slate-900 text-white pl-6 pr-5 py-4 rounded-full shadow-2xl flex items-center gap-3 hover:scale-105 transition-all z-50"
      >
        <div class="relative">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span class="absolute -top-2 -right-2 bg-brand text-slate-900 text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
            {{ cartItems.length }}
          </span>
        </div>
        <span class="font-black">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</span>
      </button>
    </Transition>

    <!-- Checkout Drawer (Mismo que las otras plantillas) -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="showCheckout" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100]" @click="showCheckout = false"></div>
      </Transition>
      
      <Transition name="slide-up">
        <div v-if="showCheckout" class="fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl z-[101] max-h-[85vh] overflow-y-auto">
          <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-3xl z-10">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-black text-gray-900">Tu Pedido</h3>
              <button @click="showCheckout = false" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <div class="px-6 py-4 space-y-4">
            <!-- Cart Items -->
            <div v-for="item in groupedCartItems" :key="item.id" class="flex gap-4 py-3 border-b border-gray-100">
              <img :src="item.image_url" class="w-16 h-16 object-cover rounded-lg flex-shrink-0" />
              <div class="flex-1 min-w-0">
                <h4 class="font-bold text-gray-900 text-sm line-clamp-1">{{ item.name }}</h4>
                <p class="text-brand font-black text-base mt-1">{{ storeConfig.currency_symbol }}{{ formatPrice(item.price) }}</p>
              </div>
              <div class="flex flex-col items-end gap-2">
                <div class="flex items-center gap-2 bg-gray-100 rounded-full px-2 py-1">
                  <button @click="decreaseQuantity(item.id)" class="text-gray-600">-</button>
                  <span class="text-sm font-bold text-gray-900 w-6 text-center">{{ item.quantity }}</span>
                  <button @click="increaseQuantity(item.id, item)" class="text-brand">+</button>
                </div>
                <p class="text-sm font-bold text-gray-900">{{ storeConfig.currency_symbol }}{{ formatPrice(item.price * item.quantity) }}</p>
              </div>
            </div>

            <!-- Totals -->
            <div class="space-y-2 pt-4">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-bold text-gray-900">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Domicilio</span>
                <span class="font-bold text-gray-900">{{ storeConfig.currency_symbol }}{{ formatPrice(storeConfig.delivery_cost) }}</span>
              </div>
              <div class="flex justify-between text-lg pt-2 border-t-2 border-gray-200">
                <span class="font-black text-gray-900">Total</span>
                <span class="font-black text-brand text-2xl">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal + storeConfig.delivery_cost) }}</span>
              </div>
            </div>

            <!-- Validación -->
            <div v-if="cartTotal < storeConfig.min_order_value" class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              <div class="text-sm">
                <p class="font-bold text-amber-900">Pedido mínimo no alcanzado</p>
                <p class="text-amber-700 mt-1">Faltan {{ storeConfig.currency_symbol }}{{ formatPrice(storeConfig.min_order_value - cartTotal) }}</p>
              </div>
            </div>

            <!-- WhatsApp Button -->
            <button 
              @click="sendWhatsAppOrder"
              :disabled="cartTotal < storeConfig.min_order_value"
              class="w-full bg-[#25D366] hover:bg-[#1ebe57] disabled:bg-gray-300 text-white py-4 rounded-xl font-black text-lg flex items-center justify-center gap-3 shadow-lg transition-all disabled:cursor-not-allowed"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
              </svg>
              Enviar Pedido por WhatsApp
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  storeConfig: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    default: () => []
  }
})

// Estado
const isScrolled = ref(false)
const searchQuery = ref('')
const selectedCategory = ref(null)
const cartItems = ref([])
const showCheckout = ref(false)
const loadingImages = ref({})
const imageErrors = ref({})

// Computed
const filteredProducts = computed(() => {
  let products = props.storeConfig.catalog_products || []
  
  if (selectedCategory.value !== null) {
    products = products.filter(p => p.category_id === selectedCategory.value)
  }
  
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    products = products.filter(p => 
      p.name.toLowerCase().includes(query) || 
      p.description?.toLowerCase().includes(query)
    )
  }
  
  return products
})

const groupedCartItems = computed(() => {
  const grouped = {}
  cartItems.value.forEach(item => {
    if (grouped[item.id]) {
      grouped[item.id].quantity++
    } else {
      grouped[item.id] = { ...item, quantity: 1 }
    }
  })
  return Object.values(grouped)
})

const cartTotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + parseFloat(item.price), 0)
})

// Métodos
const formatPrice = (price) => {
  return parseFloat(price).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const handleImageError = (productId) => {
  imageErrors.value[productId] = true
}

const getProductQuantity = (productId) => {
  return cartItems.value.filter(item => item.id === productId).length
}

const addToCart = (product) => {
  if (product.stock === 0) return
  cartItems.value.push({ ...product })
}

const increaseQuantity = (productId, product) => {
  const currentQty = getProductQuantity(productId)
  if (currentQty < product.stock) {
    cartItems.value.push({ ...product })
  }
}

const decreaseQuantity = (productId) => {
  const index = cartItems.value.findIndex(item => item.id === productId)
  if (index > -1) {
    cartItems.value.splice(index, 1)
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = null
}

const sendWhatsAppOrder = () => {
  if (cartTotal.value < props.storeConfig.min_order_value) return

  const total = cartTotal.value + props.storeConfig.delivery_cost
  let message = `¡Hola! 🛒 Quiero hacer este pedido:\n\n`
  
  groupedCartItems.value.forEach((item, index) => {
    message += `${index + 1}. ${item.name} x${item.quantity} - ${props.storeConfig.currency_symbol}${formatPrice(item.price * item.quantity)}\n`
  })
  
  message += `\n📦 Subtotal: ${props.storeConfig.currency_symbol}${formatPrice(cartTotal.value)}`
  message += `\n🚚 Domicilio: ${props.storeConfig.currency_symbol}${formatPrice(props.storeConfig.delivery_cost)}`
  message += `\n💰 *Total: ${props.storeConfig.currency_symbol}${formatPrice(total)}*`

  const whatsappUrl = `https://wa.me/${props.storeConfig.whatsapp_number}?text=${encodeURIComponent(message)}`
  window.open(whatsappUrl, '_blank')
}

const handleScroll = () => {
  isScrolled.value = window.scrollY > 100
}

// Lifecycle
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  // Inicializar solo imageErrors
  props.storeConfig.catalog_products?.forEach(p => {
    imageErrors.value[p.id] = false
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.catalog-modern-grid {
  --brand-color: v-bind('storeConfig.primary_color');
  --brand-dark: color-mix(in srgb, v-bind('storeConfig.primary_color') 85%, black);
}

.bg-brand {
  background-color: var(--brand-color);
}

.bg-brand-dark {
  background-color: var(--brand-dark);
}

.text-brand {
  color: var(--brand-color);
}

.border-brand {
  border-color: var(--brand-color);
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* Skeleton Loader */
.skeleton-loader {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* Blob Animations */
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

/* Fade In Up */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in-up {
  animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
  opacity: 0;
}

.delay-100 { animation-delay: 100ms; }
.delay-200 { animation-delay: 200ms; }

/* Transitions */
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.4s cubic-bezier(0.55, 0, 0.1, 1);
}

.list-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active, .slide-up-leave-active {
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-up-enter-from, .slide-up-leave-to {
  transform: translateY(100%);
}

.scale-enter-active, .scale-leave-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.scale-enter-from, .scale-leave-to {
  opacity: 0;
  transform: scale(0.8);
}
</style>
