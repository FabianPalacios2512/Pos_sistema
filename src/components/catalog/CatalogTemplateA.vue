<template>
  <!-- PLANTILLA A: "VISUAL STORY" - Estilo Boutique/Gourmet -->
  <div class="catalog-visual-story bg-gray-50 relative overflow-x-hidden min-h-screen">
    
    <!-- HERO CARRUSEL: Full Height con Transiciones Automáticas -->
    <section class="relative w-full overflow-hidden" style="height: 100vh;">
      <!-- Carrusel de Imágenes con Transición -->
      <div class="absolute inset-0">
        <TransitionGroup name="fade-slide">
          <div 
            v-for="(image, index) in carouselImages" 
            :key="index"
            v-show="currentSlide === index"
            class="absolute inset-0"
          >
            <img 
              :src="image"
              alt="Banner"
              class="w-full h-full object-cover"
            />
            <!-- Gradiente para legibilidad -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-black/60"></div>
          </div>
        </TransitionGroup>
      </div>

      <!-- Logo en la Parte Superior Izquierda -->
      <div class="absolute top-8 left-8 z-20">
        <img 
          v-if="storeConfig.logo_url"
          :src="storeConfig.logo_url"
          alt="Logo"
          class="w-16 h-16 md:w-20 md:h-20 object-contain rounded-xl drop-shadow-2xl"
        />
      </div>

      <!-- Contenido Central -->
      <div class="absolute inset-0 flex flex-col items-center justify-center z-10 px-6 text-center">
        <h2 class="text-white text-4xl md:text-6xl font-black mb-4 drop-shadow-2xl animate-fade-in">
          Productos de Calidad
        </h2>
        <p class="text-white/90 text-lg md:text-xl font-light max-w-2xl drop-shadow-lg animate-fade-in-delay">
          Descubre nuestra selección exclusiva
        </p>
        
        <!-- Botón Scroll Down -->
        <button 
          @click="scrollToProducts"
          class="mt-12 animate-bounce bg-white/10 backdrop-blur-md border border-white/30 rounded-full p-4 hover:bg-white/20 transition-all"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
          </svg>
        </button>
      </div>

      <!-- Indicadores del Carrusel -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        <button 
          v-for="(img, index) in 3" 
          :key="index"
          @click="currentSlide = index"
          class="w-2 h-2 rounded-full transition-all duration-300"
          :class="currentSlide === index ? 'bg-white w-8' : 'bg-white/50 hover:bg-white/80'"
        ></button>
      </div>
    </section>

    <!-- HEADER STICKY: Glass Effect al hacer scroll -->
    <header 
      ref="stickyHeader"
      :class="['fixed top-0 left-0 right-0 z-50 transition-all duration-300', isScrolled ? 'backdrop-blur-xl bg-white/90 shadow-lg py-3' : 'bg-transparent py-4']"
    >
      <div class="container mx-auto px-6 flex items-center justify-between">
        <img 
          :src="storeConfig.logo_url"
          alt="Logo"
          class="h-10 w-10 object-contain rounded-lg"
          :class="{ 'opacity-100': isScrolled, 'opacity-0': !isScrolled }"
        />
        <h2 
          class="text-xl font-bold transition-colors"
          :class="isScrolled ? 'text-gray-900' : 'text-white'"
        >
          {{ storeName }}
        </h2>
        <div class="w-10"></div> <!-- Spacer for symmetry -->
      </div>
    </header>

    <!-- PRODUCTS SECTION: Layout con Sidebar Lateral -->
    <section class="relative z-10 px-4 pt-8 pb-32 md:pb-12">
      <div class="container mx-auto max-w-7xl">
        
        <div class="flex gap-6">
          <!-- SIDEBAR IZQUIERDO: Filtros Estilo Mercado Libre -->
          <aside v-if="!isMobilePreview" class="hidden lg:block w-64 flex-shrink-0">
            <div class="sticky top-24 space-y-6">
              
              <!-- Filtro por Categoría -->
              <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wide">Categorías</h3>
                <div class="space-y-2">
                  <button
                    @click="selectedCategory = null"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm transition-all"
                    :class="selectedCategory === null 
                      ? 'bg-emerald-50 text-emerald-700 font-semibold' 
                      : 'text-gray-700 hover:bg-gray-50'"
                  >
                    Todas las categorías
                  </button>
                  <button
                    v-for="cat in categories"
                    :key="cat.id"
                    @click="selectedCategory = cat.id"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm transition-all"
                    :class="selectedCategory === cat.id 
                      ? 'bg-emerald-50 text-emerald-700 font-semibold' 
                      : 'text-gray-700 hover:bg-gray-50'"
                  >
                    {{ cat.name }}
                  </button>
                </div>
              </div>

              <!-- Filtro por Precio -->
              <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wide">Ordenar por</h3>
                <div class="space-y-2">
                  <label class="flex items-center gap-2 cursor-pointer group">
                    <input 
                      type="radio" 
                      name="sort" 
                      value="" 
                      v-model="sortOrder"
                      class="w-4 h-4 text-emerald-600 focus:ring-emerald-500"
                    />
                    <span class="text-sm text-gray-700 group-hover:text-emerald-600">Más relevantes</span>
                  </label>
                  <label class="flex items-center gap-2 cursor-pointer group">
                    <input 
                      type="radio" 
                      name="sort" 
                      value="price-asc" 
                      v-model="sortOrder"
                      class="w-4 h-4 text-emerald-600 focus:ring-emerald-500"
                    />
                    <span class="text-sm text-gray-700 group-hover:text-emerald-600">Menor precio</span>
                  </label>
                  <label class="flex items-center gap-2 cursor-pointer group">
                    <input 
                      type="radio" 
                      name="sort" 
                      value="price-desc" 
                      v-model="sortOrder"
                      class="w-4 h-4 text-emerald-600 focus:ring-emerald-500"
                    />
                    <span class="text-sm text-gray-700 group-hover:text-emerald-600">Mayor precio</span>
                  </label>
                  <label class="flex items-center gap-2 cursor-pointer group">
                    <input 
                      type="radio" 
                      name="sort" 
                      value="name-asc" 
                      v-model="sortOrder"
                      class="w-4 h-4 text-emerald-600 focus:ring-emerald-500"
                    />
                    <span class="text-sm text-gray-700 group-hover:text-emerald-600">A-Z</span>
                  </label>
                </div>
              </div>

              <!-- Filtro de Disponibilidad -->
              <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wide">Disponibilidad</h3>
                <label class="flex items-center gap-2 cursor-pointer group">
                  <input 
                    type="checkbox" 
                    v-model="showOnlyAvailable"
                    class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500"
                  />
                  <span class="text-sm text-gray-700 group-hover:text-emerald-600">Solo con stock</span>
                </label>
              </div>

              <!-- Botón Limpiar Filtros -->
              <button
                v-if="selectedCategory || showOnlyAvailable || sortOrder"
                @click="clearFilters"
                class="w-full px-4 py-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-all border border-emerald-200"
              >
                Limpiar filtros
              </button>
            </div>
          </aside>

          <!-- ÁREA PRINCIPAL: Productos -->
          <div class="flex-1 min-w-0">
            
            <!-- Barra Superior: Contador y Filtros Móviles -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
              <div class="text-sm text-gray-600 font-medium">
                {{ filteredProducts.length }} productos
                <span v-if="selectedCategory || showOnlyAvailable || sortOrder" class="text-emerald-600 font-semibold">
                  (filtrados)
                </span>
              </div>

              <!-- Filtros Móviles (solo en pantallas pequeñas) -->
              <div class="lg:hidden">
                <select 
                  v-model="sortOrder"
                  class="appearance-none bg-white border border-gray-300 rounded-lg px-3 py-2 pr-8 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                  <option value="">Ordenar</option>
                  <option value="price-asc">Menor precio</option>
                  <option value="price-desc">Mayor precio</option>
                  <option value="name-asc">A-Z</option>
                </select>
              </div>
            </div>

            <!-- Grid de Productos -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
              <TransitionGroup name="list">
                <div 
                  v-for="product in filteredProducts" 
                  :key="product.id"
                  class="group"
            >
              <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <!-- Product Image -->
                <div class="relative aspect-square overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                  
                  <!-- Imagen del Producto -->
                  <img 
                    v-if="product.image_url && product.image_url !== 'https://via.placeholder.com/400' && !imageErrors[product.id]"
                    :src="product.image_url"
                    :alt="product.name"
                    @error="handleImageError(product.id)"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                  />
                  
                  <!-- Placeholder cuando no hay imagen -->
                  <div v-else class="w-full h-full flex items-center justify-center">
                    <div class="text-center">
                      <svg class="w-20 h-20 md:w-24 md:h-24 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <p class="text-sm text-gray-400 font-medium">Sin imagen</p>
                    </div>
                  </div>
                  
                  <!-- Floating Add Button (Círculo) -->
                  <button
                    @click="addToCart(product)"
                    :disabled="product.stock === 0"
                    class="absolute bottom-2 right-2 w-10 h-10 rounded-full shadow-lg flex items-center justify-center bg-brand text-white hover:scale-110 transition-transform active:scale-95 z-10 disabled:bg-gray-400 disabled:cursor-not-allowed"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                  </button>

                  <!-- Badge de Stock Bajo -->
                  <div v-if="product.stock < 5 && product.stock > 0" class="absolute top-2 left-2 bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md">
                    Solo {{ product.stock }}
                  </div>
                  <div v-else-if="product.stock === 0" class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md">
                    Agotado
                  </div>
                </div>

                <!-- Product Info -->
                <div class="p-3">
                  <h3 class="text-sm font-bold text-gray-900 line-clamp-2 mb-2 h-10 leading-tight">{{ product.name }}</h3>
                  <div class="flex items-center justify-between">
                    <p class="text-base font-black text-brand">
                      {{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </TransitionGroup>
            </div>

            <!-- Empty State -->
            <div v-if="filteredProducts.length === 0" class="text-center py-20">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <p class="text-gray-500 text-lg font-medium">No hay productos disponibles</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FLOATING CART BUTTON (FAB) -->
    <Transition name="scale">
      <button 
        v-if="cartItems.length > 0"
        @click="showCheckout = true"
        class="fixed bottom-6 right-6 bg-brand text-white pl-6 pr-5 py-4 rounded-full shadow-2xl hover:shadow-3xl flex items-center gap-3 transform hover:scale-105 transition-all z-50 animate-bounce-slow"
      >
        <div class="relative">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
            {{ cartItems.length }}
          </span>
        </div>
        <div class="text-left">
          <p class="text-xs font-medium opacity-90">Ver Pedido</p>
          <p class="text-sm font-black">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</p>
        </div>
      </button>
    </Transition>

    <!-- CHECKOUT DRAWER (Modal desde abajo) -->
      <Transition name="fade">
        <div v-if="showCheckout" class="absolute inset-0 bg-black/60 backdrop-blur-sm z-[100]" @click="showCheckout = false"></div>
      </Transition>
      
      <Transition name="slide-up">
        <div v-if="showCheckout" class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl z-[101] max-h-[85vh] overflow-y-auto">
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
            <div v-for="item in cartItems" :key="item.id" class="flex gap-4 py-3 border-b border-gray-100">
              <img :src="item.image_url" class="w-16 h-16 object-cover rounded-lg" />
              <div class="flex-1">
                <h4 class="font-bold text-gray-900 text-sm">{{ item.name }}</h4>
                <p class="text-brand font-black text-lg mt-1">{{ storeConfig.currency_symbol }}{{ formatPrice(item.price) }}</p>
              </div>
              <button @click="removeFromCart(item.id)" class="text-red-500 hover:text-red-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </button>
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
                  Faltan {{ storeConfig.currency_symbol }}{{ formatPrice(storeConfig.min_order_value - cartTotal) }} para completar tu pedido.
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  storeConfig: {
    type: Object,
    required: true,
    default: () => ({
      primary_color: '#10B981',
      logo_url: '',
      banner_url: '',
      whatsapp_number: '',
      currency_symbol: '$',
      delivery_cost: 0,
      min_order_value: 0,
      catalog_products: []
    })
  },
  isMobilePreview: {
    type: Boolean,
    default: false
  }
})

// Estado
const isScrolled = ref(false)
const isLoadingBanner = ref(true)
const loadingImages = ref({})
const imageErrors = ref({})
const selectedCategory = ref(null)
const sortOrder = ref('') // Filtro de ordenamiento
const showOnlyAvailable = ref(false) // Filtro de disponibilidad
const cartItems = ref([])
const showCheckout = ref(false)
const stickyHeader = ref(null)
const productsSection = ref(null)
const currentSlide = ref(0)

// Imágenes del carrusel - 3 imágenes diferentes de tiendas modernas
const carouselImages = [
  props.storeConfig.banner_url || 'https://images.unsplash.com/photo-1441986300917-64674bd600d8',
  'https://images.unsplash.com/photo-1555421689-d68471e189f2',
  'https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5',
]

// Computed
const storeName = computed(() => props.storeConfig.store_name || 'Mi Tienda')

const categories = computed(() => {
  const cats = new Set()
  props.storeConfig.catalog_products?.forEach(p => {
    if (p.category) cats.add(p.category)
  })
  return Array.from(cats).map((name, index) => ({ id: index + 1, name }))
})

const filteredProducts = computed(() => {
  let products = props.storeConfig.catalog_products || []
  
  // Filtro por categoría
  if (selectedCategory.value !== null) {
    const catName = categories.value.find(c => c.id === selectedCategory.value)?.name
    products = products.filter(p => p.category === catName)
  }
  
  // Filtro por disponibilidad (stock > 0)
  if (showOnlyAvailable.value) {
    products = products.filter(p => p.stock && p.stock > 0)
  }
  
  // Ordenamiento
  if (sortOrder.value) {
    products = [...products] // Clonar para no mutar el original
    
    switch (sortOrder.value) {
      case 'price-asc':
        products.sort((a, b) => parseFloat(a.price || 0) - parseFloat(b.price || 0))
        break
      case 'price-desc':
        products.sort((a, b) => parseFloat(b.price || 0) - parseFloat(a.price || 0))
        break
      case 'name-asc':
        products.sort((a, b) => (a.name || '').localeCompare(b.name || ''))
        break
      case 'name-desc':
        products.sort((a, b) => (b.name || '').localeCompare(a.name || ''))
        break
    }
  }
  
  return products
})

const cartTotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + parseFloat(item.price), 0)
})

// Métodos
const formatPrice = (price) => {
  return parseFloat(price).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const clearFilters = () => {
  selectedCategory.value = null
  sortOrder.value = ''
  showOnlyAvailable.value = false
}

const handleImageError = (productId) => {
  imageErrors.value[productId] = true
}

const addToCart = (product) => {
  if (product.stock === 0) return
  cartItems.value.push({ ...product })
  // Animación visual
  const event = new CustomEvent('cart-updated', { detail: { action: 'add' } })
  window.dispatchEvent(event)
}

const removeFromCart = (productId) => {
  const index = cartItems.value.findIndex(item => item.id === productId)
  if (index > -1) cartItems.value.splice(index, 1)
}

const sendWhatsAppOrder = () => {
  if (cartTotal.value < props.storeConfig.min_order_value) return

  const total = cartTotal.value + props.storeConfig.delivery_cost
  let message = `¡Hola! 👋 Quiero hacer un pedido:\n\n`
  
  cartItems.value.forEach((item, index) => {
    message += `${index + 1}. ${item.name} - ${props.storeConfig.currency_symbol}${formatPrice(item.price)}\n`
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

const scrollToProducts = () => {
  productsSection.value?.scrollIntoView({ behavior: 'smooth' })
}

// Autoplay del carrusel
let carouselInterval = null
const startCarousel = () => {
  carouselInterval = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % 3 // 3 slides
  }, 5000) // Cambia cada 5 segundos
}

const stopCarousel = () => {
  if (carouselInterval) {
    clearInterval(carouselInterval)
  }
}

// Lifecycle
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  startCarousel()
  // Inicializar solo imageErrors
  props.storeConfig.catalog_products?.forEach(p => {
    imageErrors.value[p.id] = false
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  stopCarousel()
})
</script>

<style scoped>
.catalog-visual-story {
  --brand-color: v-bind('storeConfig.primary_color');
}

.bg-brand {
  background-color: var(--brand-color);
}

.text-brand {
  color: var(--brand-color);
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
  position: relative;
  overflow: hidden;
}

.skeleton-loader::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  to { left: 100%; }
}

/* Transitions */
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.5s cubic-bezier(0.55, 0, 0.1, 1);
}

.list-enter-from {
  opacity: 0;
  transform: scale(0.8) translateY(30px);
}

.list-leave-to {
  opacity: 0;
  transform: scale(0.8);
}

.list-leave-active {
  position: absolute;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Transición del Carrusel */
.fade-slide-enter-active {
  transition: opacity 1.5s ease;
}

.fade-slide-leave-active {
  transition: opacity 1s ease;
}

.fade-slide-enter-from {
  opacity: 0;
}

.fade-slide-leave-to {
  opacity: 0;
}

/* Animación Fade In */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 1s ease-out;
}

.animate-fade-in-delay {
  animation: fadeIn 1s ease-out 0.3s both;
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

@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}

.animate-bounce-slow {
  animation: bounce-slow 2s ease-in-out infinite;
}

/* Masonry Responsive */
@media (max-width: 640px) {
  .columns-2 {
    columns: 1;
  }
}
</style>
