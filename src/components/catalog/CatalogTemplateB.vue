<template>
  <!-- PLANTILLA B: "SPEED MARKET" - Estilo Supermercado/Rappi -->
  <div class="catalog-speed-market min-h-screen bg-gray-50">
    
    <!-- HEADER COMPACTO: Logo + Buscador -->
    <header class="sticky top-0 z-50 bg-white shadow-md border-b border-gray-200">
      <div class="container mx-auto px-4 py-3 flex items-center gap-4">
        <!-- Logo Pequeño -->
        <img 
          :src="storeConfig.logo_url || 'https://via.placeholder.com/60'"
          alt="Logo"
          class="h-12 w-12 object-contain flex-shrink-0 rounded-lg"
        />
        
        <!-- Buscador Grande -->
        <div class="flex-1 relative">
          <input 
            type="text"
            v-model="searchQuery"
            placeholder="Buscar productos..."
            class="w-full h-11 pl-10 pr-4 rounded-xl border-2 border-gray-200 focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none transition-all text-sm"
          />
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>

        <!-- Cart Icon -->
        <button 
          @click="showCheckout = true"
          class="relative p-2 hover:bg-gray-100 rounded-full transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span v-if="cartItems.length > 0" class="absolute -top-1 -right-1 bg-brand text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
            {{ cartItems.length }}
          </span>
        </button>
      </div>
    </header>

    <!-- NAVEGACIÓN POR CATEGORÍAS: Pills Horizontales STICKY -->
    <nav class="sticky top-[72px] z-40 bg-white shadow-sm border-b border-gray-100 py-3 px-4 overflow-x-auto scrollbar-hide">
      <div class="flex gap-2 min-w-max">
        <button
          @click="selectedCategory = null"
          class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all flex-shrink-0 shadow-sm"
          :class="selectedCategory === null 
            ? 'bg-brand text-white scale-105' 
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
        >
          Todos
        </button>
        <button
          v-for="cat in categories"
          :key="cat.id"
          @click="selectedCategory = cat.id"
          class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all flex-shrink-0 shadow-sm"
          :class="selectedCategory === cat.id 
            ? 'bg-brand text-white scale-105' 
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
        >
          {{ cat.name }}
        </button>
      </div>
    </nav>

    <!-- LISTADO DE PRODUCTOS: Grid en Desktop, Lista en Móvil -->
    <main class="container mx-auto px-4 py-6 pb-32 md:pb-12 max-w-7xl">
      <!-- Grid de Cards para Desktop -->
      <div class="hidden md:grid md:grid-cols-3 lg:grid-cols-4 gap-4">
        <TransitionGroup name="list">
          <div 
            v-for="product in filteredProducts" 
            :key="product.id"
            class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all border border-gray-100 overflow-hidden group"
          >
            <!-- Imagen Card -->
            <div class="relative aspect-square bg-gray-50 overflow-hidden">
              <!-- Imagen del Producto -->
              <img 
                v-if="(product.images && product.images.length > 0) || (product.image_url && product.image_url !== 'https://via.placeholder.com/400' && !imageErrors[product.id])"
                :src="product.images && product.images.length > 0 ? product.images[0] : product.image_url"
                :alt="product.name"
                @error="handleImageError(product.id)"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              
              <!-- Placeholder cuando no hay imagen -->
              <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                <div class="text-center">
                  <svg class="w-16 h-16 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-xs text-gray-400 font-medium">Sin imagen</p>
                </div>
              </div>

              <!-- Badges -->
              <div v-if="product.stock === 0" class="absolute inset-0 bg-black/70 flex items-center justify-center">
                <span class="text-white text-sm font-bold">Agotado</span>
              </div>
              <div v-else-if="product.stock < 5" class="absolute top-2 right-2 bg-amber-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                Solo {{ product.stock }}
              </div>
            </div>

            <!-- Info Card -->
            <div class="p-3">
              <h3 class="font-bold text-gray-900 text-sm line-clamp-2 mb-1 min-h-[2.5rem]">{{ product.name }}</h3>
              <p class="text-xs text-gray-500 line-clamp-1 mb-2">{{ product.description || 'Sin descripción' }}</p>
              
              <!-- Precio y Botón -->
              <div class="flex items-center justify-between gap-2">
                <p class="text-lg font-black text-brand">
                  {{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}
                </p>
                <button
                  @click="addToCart(product)"
                  :disabled="product.stock === 0"
                  class="p-2 rounded-full bg-brand text-white hover:scale-110 transition-transform disabled:bg-gray-300 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </TransitionGroup>
      </div>

      <!-- Lista Horizontal para Móvil -->
      <TransitionGroup name="list" tag="div" class="space-y-3 md:hidden">
        <div 
          v-for="product in filteredProducts" 
          :key="product.id"
          class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 overflow-hidden group"
        >
          <div class="flex items-center gap-3 p-3">
            <!-- Foto Pequeña (Izquierda) -->
            <div class="relative w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-50">
              <!-- Imagen del Producto -->
              <img 
                v-if="(product.images && product.images.length > 0) || (product.image_url && product.image_url !== 'https://via.placeholder.com/400' && !imageErrors[product.id])"
                :src="product.images && product.images.length > 0 ? product.images[0] : product.image_url"
                :alt="product.name"
                @error="handleImageError(product.id)"
                class="w-full h-full object-cover"
              />
              
              <!-- Placeholder cuando no hay imagen -->
              <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                <div class="text-center">
                  <svg class="w-8 h-8 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-[10px] text-gray-400 font-medium mt-0.5">Sin imagen</p>
                </div>
              </div>
              <!-- Badge Stock -->
              <div v-if="product.stock === 0" class="absolute inset-0 bg-black/60 flex items-center justify-center">
                <span class="text-white text-xs font-bold">Agotado</span>
              </div>
            </div>

            <!-- Info Centro -->
            <div class="flex-1 min-w-0">
              <h3 class="font-bold text-gray-900 text-sm line-clamp-1 mb-1">{{ product.name }}</h3>
              <p class="text-xs text-gray-500 line-clamp-1 mb-2">{{ product.description || 'Sin descripción' }}</p>
              <div class="flex items-center gap-3">
                <p class="text-lg font-black text-brand">
                  {{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}
                </p>
                <span v-if="product.stock < 5 && product.stock > 0" class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                  Solo {{ product.stock }}
                </span>
              </div>
            </div>

            <!-- Stepper de Cantidad (Derecha) -->
            <div class="flex items-center gap-2 flex-shrink-0">
              <template v-if="getProductQuantity(product.id) === 0">
                <button 
                  @click="addToCart(product)"
                  :disabled="product.stock === 0"
                  class="bg-brand hover:bg-brand-dark disabled:bg-gray-300 text-white px-4 md:px-6 py-2 md:py-2.5 rounded-full font-bold text-xs md:text-sm transition-all shadow-md hover:shadow-lg disabled:cursor-not-allowed flex items-center gap-2"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                  </svg>
                  Agregar
                </button>
              </template>
              <template v-else>
                <div class="flex items-center gap-2 bg-gray-100 rounded-full px-2 py-1.5 shadow-inner">
                  <button 
                    @click="decreaseQuantity(product.id)"
                    class="w-8 h-8 rounded-full bg-white hover:bg-gray-200 flex items-center justify-center transition-colors shadow-sm active:scale-95"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  <span class="text-sm font-black text-gray-900 w-8 text-center">{{ getProductQuantity(product.id) }}</span>
                  <button 
                    @click="increaseQuantity(product.id, product)"
                    :disabled="getProductQuantity(product.id) >= product.stock"
                    class="w-8 h-8 rounded-full bg-brand hover:bg-brand-dark disabled:bg-gray-300 text-white flex items-center justify-center transition-colors shadow-sm active:scale-95 disabled:cursor-not-allowed"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
              </template>
            </div>
          </div>
        </div>
      </TransitionGroup>

      <!-- Empty State -->
      <div v-if="filteredProducts.length === 0" class="col-span-full text-center py-20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-500 text-lg font-medium">No encontramos productos</p>
        <p class="text-gray-400 text-sm mt-2">Intenta con otra búsqueda</p>
      </div>
    </main>

    <!-- FLOATING CART SUMMARY (Barra inferior fija) -->
    <Transition name="slide-up">
      <div 
        v-if="cartItems.length > 0"
        class="fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-200 shadow-2xl z-50 px-4 py-4"
      >
        <div class="container mx-auto max-w-4xl flex items-center justify-between gap-4">
          <div class="flex-1">
            <p class="text-xs text-gray-500 font-medium">Total del pedido</p>
            <p class="text-2xl font-black text-brand">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</p>
          </div>
          <button 
            @click="showCheckout = true"
            class="bg-brand hover:bg-brand-dark text-white px-8 py-3.5 rounded-full font-black text-base shadow-lg hover:shadow-xl transition-all flex items-center gap-2"
          >
            Ver Carrito
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>
    </Transition>

    <!-- CHECKOUT DRAWER -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="showCheckout" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100]" @click="showCheckout = false"></div>
      </Transition>
      
      <Transition name="slide-up">
        <div v-if="showCheckout" class="fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl z-[101] max-h-[85vh] overflow-y-auto">
          <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-3xl z-10">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-black text-gray-900">Resumen del Pedido</h3>
              <button @click="showCheckout = false" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <div class="px-6 py-4 space-y-3">
            <!-- Cart Items -->
            <div v-for="item in groupedCartItems" :key="item.id" class="flex gap-4 py-3 border-b border-gray-100">
              <img :src="item.image_url" class="w-16 h-16 object-cover rounded-lg flex-shrink-0" />
              <div class="flex-1 min-w-0">
                <h4 class="font-bold text-gray-900 text-sm line-clamp-1">{{ item.name }}</h4>
                <p class="text-brand font-black text-base mt-1">{{ storeConfig.currency_symbol }}{{ formatPrice(item.price) }}</p>
              </div>
              <div class="flex flex-col items-end gap-2">
                <div class="flex items-center gap-2 bg-gray-100 rounded-full px-2 py-1">
                  <button @click="decreaseQuantity(item.id)" class="text-gray-600 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  <span class="text-sm font-bold text-gray-900 w-6 text-center">{{ item.quantity }}</span>
                  <button @click="increaseQuantity(item.id, item)" class="text-brand hover:text-brand-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                  </button>
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

            <!-- Validación Pedido Mínimo -->
            <div v-if="cartTotal < storeConfig.min_order_value" class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              <div class="text-sm">
                <p class="font-bold text-amber-900">Pedido mínimo no alcanzado</p>
                <p class="text-amber-700 mt-1">
                  Faltan {{ storeConfig.currency_symbol }}{{ formatPrice(storeConfig.min_order_value - cartTotal) }} para completar.
                </p>
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
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  storeConfig: {
    type: Object,
    required: true,
    default: () => ({
      primary_color: '#10B981',
      logo_url: '',
      whatsapp_number: '',
      currency_symbol: '$',
      delivery_cost: 0,
      min_order_value: 0,
      catalog_products: []
    })
  },
  categories: {
    type: Array,
    default: () => []
  }
})

// Estado
const searchQuery = ref('')
const selectedCategory = ref(null)
const cartItems = ref([])
const showCheckout = ref(false)
const loadingImages = ref({})
const imageErrors = ref({})

// Computed
const filteredProducts = computed(() => {
  let products = props.storeConfig.catalog_products || []
  
  // Filtrar por categoría usando category_id
  if (selectedCategory.value !== null) {
    products = products.filter(p => p.category_id === selectedCategory.value)
  }
  if (selectedCategory.value !== null) {
    const catName = categories.value.find(c => c.id === selectedCategory.value)?.name
    products = products.filter(p => p.category === catName)
  }
  
  // Filtrar por búsqueda
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

// Lifecycle
onMounted(() => {
  // Inicializar solo imageErrors
  props.storeConfig.catalog_products?.forEach(p => {
    imageErrors.value[p.id] = false
  })
})
</script>

<style scoped>
.catalog-speed-market {
  --brand-color: v-bind('storeConfig.primary_color');
  --brand-dark: color-mix(in srgb, v-bind('storeConfig.primary_color') 85%, black);
}

.bg-brand {
  background-color: var(--brand-color);
}

.bg-brand-dark {
  background-color: var(--brand-dark);
}

.hover\:bg-brand-dark:hover {
  background-color: var(--brand-dark);
}

.text-brand {
  color: var(--brand-color);
}

.border-brand {
  border-color: var(--brand-color);
}

.focus\:border-brand:focus {
  border-color: var(--brand-color);
}

.focus\:ring-brand\/20:focus {
  --tw-ring-color: color-mix(in srgb, v-bind('storeConfig.primary_color') 20%, transparent);
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

.list-leave-active {
  position: absolute;
  width: 100%;
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
</style>
