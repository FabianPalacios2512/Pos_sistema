<template>
  <div class="min-h-screen bg-[#f8fafc] font-sans selection:bg-lime-500 selection:text-white overflow-x-hidden">
    <!-- ☀️ LIGHT THEME BACKGROUND -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
      <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-lime-200/30 rounded-full blur-[100px] mix-blend-multiply animate-blob"></div>
      <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-blue-100/40 rounded-full blur-[100px] mix-blend-multiply animate-blob animation-delay-2000"></div>
      <div class="absolute top-[40%] left-[40%] w-[40%] h-[40%] bg-purple-100/40 rounded-full blur-[100px] mix-blend-multiply animate-blob animation-delay-4000"></div>
    </div>

    <!-- 🛍️ FLOATING GLASS HEADER (Light) -->
    <header 
      class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
      :class="isScrolled ? 'bg-white/80 backdrop-blur-xl shadow-sm py-2' : 'bg-transparent py-4'"
    >
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between gap-4">
          
          <!-- Logo -->
          <div class="flex items-center gap-2 cursor-pointer group" @click="clearFilters">
            <div class="w-10 h-10 bg-gradient-to-br from-lime-400 to-green-500 rounded-xl flex items-center justify-center shadow-lg shadow-lime-500/20 group-hover:rotate-6 transition-transform duration-300">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
            </div>
            <div>
              <h1 class="text-xl font-black text-slate-800 tracking-tight leading-none">{{ storeName }}</h1>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Store</p>
            </div>
          </div>

          <!-- 🔍 Search (Desktop) -->
          <div class="hidden md:block flex-1 max-w-md mx-8">
            <div class="relative group">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400 group-focus-within:text-lime-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <input 
                v-model="searchQuery"
                @input="onSearch"
                type="text"
                placeholder="Buscar productos..."
                class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:border-lime-500 focus:ring-2 focus:ring-lime-500/20 transition-all shadow-sm"
              >
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-3">
             <!-- Mobile Search Toggle -->
            <button class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </button>

            <!-- Desktop Cart Button -->
            <button 
              @click="isCheckoutOpen = true"
              class="hidden md:flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl transition-all shadow-lg hover:shadow-xl group"
            >
              <div class="relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span v-if="cartItemCount > 0" class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-lime-500 text-slate-900 text-[10px] font-bold rounded-full flex items-center justify-center border border-slate-900">
                  {{ cartItemCount }}
                </span>
              </div>
              <span class="font-bold text-sm">${{ formatPrice(cartTotal) }}</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- 🌟 HERO SECTION (Clean Light) -->
    <div class="relative pt-28 pb-12 md:pt-40 md:pb-20 px-4">
      <div class="max-w-7xl mx-auto text-center relative z-10">
        <span class="inline-block px-4 py-1.5 rounded-full bg-white border border-slate-200 text-lime-600 text-xs font-bold tracking-[0.2em] uppercase mb-6 shadow-sm animate-fade-in-up">
          Nueva Colección
        </span>
        <h2 class="text-4xl md:text-6xl lg:text-7xl font-black text-slate-900 mb-6 tracking-tight leading-none animate-fade-in-up delay-100">
          Calidad que <span class="text-transparent bg-clip-text bg-gradient-to-r from-lime-500 to-green-600">Inspira</span>
        </h2>
        <p class="text-lg text-slate-500 max-w-xl mx-auto mb-8 leading-relaxed animate-fade-in-up delay-200">
          Descubre productos seleccionados con el mejor diseño y calidad garantizada.
        </p>
      </div>
    </div>

    <!-- 📂 CATEGORIES (Pills) -->
    <div class="sticky top-[60px] z-40 mb-8 transition-all duration-300" :class="isScrolled ? 'py-2' : 'py-4'">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-center overflow-x-auto scrollbar-hide pb-2">
          <div class="flex space-x-2 bg-white/60 backdrop-blur-md p-1.5 rounded-2xl border border-slate-200/50 shadow-sm">
            <button 
              @click="selectedCategory = null"
              class="px-5 py-2 rounded-xl text-sm font-bold transition-all duration-300 whitespace-nowrap"
              :class="selectedCategory === null ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100'"
            >
              Todos
            </button>
            <button 
              v-for="category in categories"
              :key="category.id"
              @click="selectedCategory = category.id"
              class="px-5 py-2 rounded-xl text-sm font-bold transition-all duration-300 whitespace-nowrap"
              :class="selectedCategory === category.id ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100'"
            >
              {{ category.name }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 🛍️ MAIN GRID (Compact Mobile) -->
    <main id="products-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32 relative z-10">
      
      <!-- Loading State -->
      <div v-if="isLoading" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div v-for="n in 8" :key="n" class="bg-white rounded-2xl p-3 h-[280px] md:h-[350px] animate-pulse border border-slate-100">
          <div class="bg-slate-100 h-32 md:h-48 rounded-xl mb-3"></div>
          <div class="h-3 bg-slate-100 rounded w-3/4 mb-2"></div>
          <div class="h-3 bg-slate-100 rounded w-1/2"></div>
        </div>
      </div>
      
      <!-- Products Grid -->
      <div 
        v-else-if="filteredProducts.length > 0"
        class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-6"
      >
        <ProductCard 
          v-for="(product, index) in filteredProducts"
          :key="product.id"
          :product="product"
          :initial-quantity="getProductQuantity(product.id)"
          @update-quantity="updateCart"
          class="animate-fade-in-up"
          :style="{ animationDelay: `${index * 50}ms` }"
        />
      </div>
      
      <!-- Empty State -->
      <div v-else class="text-center py-20">
        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Sin resultados</h3>
        <button 
          @click="clearFilters"
          class="px-6 py-2 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition-colors"
        >
          Ver Todo
        </button>
      </div>
    </main>

    <!-- 🦶 FOOTER -->
    <footer class="border-t border-slate-200 bg-white pt-12 pb-24 md:pb-8 relative z-10">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="text-slate-400 text-sm">© 2025 {{ storeName }}.</p>
      </div>
    </footer>
    
    <!-- Floating Cart FAB (Mobile Only) -->
    <FloatingCartBar 
      class="md:hidden"
      :cart="cart"
      @open-checkout="isCheckoutOpen = true"
    />
    
    <!-- Checkout Drawer -->
    <CheckoutDrawer 
      ref="checkoutDrawer"
      :is-open="isCheckoutOpen"
      :cart="cart"
      @close="isCheckoutOpen = false"
      @order-submitted="submitOrder"
    />
    
    <!-- Modal de Confirmación -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div 
          v-if="showSuccessModal"
          class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
          @click="closeSuccessModal"
        >
          <div 
            @click.stop
            class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 text-center transform transition-all"
          >
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">¡Pedido Recibido!</h2>
            <p class="text-slate-500 mb-6">Tu orden <span class="font-bold text-slate-900">{{ orderResult.order_number }}</span> ha sido creada.</p>
            <a 
              :href="orderResult.whatsapp_link"
              target="_blank"
              class="inline-flex items-center justify-center space-x-2 w-full px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl shadow-lg shadow-green-500/30 transition-all mb-3"
            >
              <span>Continuar en WhatsApp</span>
            </a>
            <button @click="closeSuccessModal" class="text-slate-400 hover:text-slate-600 font-medium text-sm">Cerrar</button>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import ProductCard from '@/components/catalog/ProductCard.vue'
import FloatingCartBar from '@/components/catalog/FloatingCartBar.vue'
import CheckoutDrawer from '@/components/catalog/CheckoutDrawer.vue'

const route = useRoute()

// Estado
const storeName = ref('Mi Tienda')
const products = ref([])
const categories = ref([])
const cart = ref([])
const searchQuery = ref('')
const selectedCategory = ref(null)
const isLoading = ref(true)
const isCheckoutOpen = ref(false)
const showSuccessModal = ref(false)
const orderResult = ref({})
const checkoutDrawer = ref(null)
const isScrolled = ref(false)

// Scroll Listener
const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
}

// Computados
const filteredProducts = computed(() => {
  let filtered = products.value

  // Filtrar por categoría
  if (selectedCategory.value !== null) {
    filtered = filtered.filter(p => p.category_id === selectedCategory.value)
  }

  // Filtrar por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(p => 
      p.name.toLowerCase().includes(query) ||
      (p.description && p.description.toLowerCase().includes(query)) ||
      (p.category && p.category.toLowerCase().includes(query))
    )
  }

  return filtered
})

const cartItemCount = computed(() => {
  return cart.value.reduce((sum, item) => sum + item.quantity, 0)
})

const cartTotal = computed(() => {
  return cart.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

// Métodos
const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(price)
}

const getCategoryName = (id) => {
  const cat = categories.value.find(c => c.id === id)
  return cat ? cat.name : ''
}

const scrollToProducts = () => {
  document.getElementById('products-section').scrollIntoView({ behavior: 'smooth' })
}

const loadProducts = async () => {
  try {
    isLoading.value = true
    // Usar la URL base del API configurada (Laravel backend en puerto 8000)
    const response = await axios.get('/api/public/catalog', {
      params: {
        category_id: selectedCategory.value
      }
    })
    
    if (response.data.success) {
      products.value = response.data.products
    }
  } catch (error) {
    console.error('Error cargando productos:', error)
  } finally {
    isLoading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await axios.get('/api/public/catalog/categories')
    
    if (response.data.success) {
      categories.value = response.data.categories
    }
  } catch (error) {
    console.error('Error cargando categorías:', error)
  }
}

const getProductQuantity = (productId) => {
  const item = cart.value.find(i => i.id === productId)
  return item ? item.quantity : 0
}

const updateCart = ({ productId, quantity }) => {
  if (quantity === 0) {
    cart.value = cart.value.filter(item => item.id !== productId)
  } else {
    const existingItem = cart.value.find(item => item.id === productId)
    
    if (existingItem) {
      existingItem.quantity = quantity
    } else {
      const product = products.value.find(p => p.id === productId)
      if (product) {
        cart.value.push({
          id: product.id,
          name: product.name,
          price: product.price,
          image: product.image,
          quantity: quantity
        })
      }
    }
  }
}

const onSearch = () => {
  // La búsqueda se maneja reactivamente a través del computed
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = null
}

const submitOrder = async (orderData) => {
  try {
    const response = await axios.post('/api/public/orders', orderData)
    
    if (response.data.success) {
      const order = response.data.order
      
      // Construir mensaje de WhatsApp personalizado
      const whatsappNumber = '573134540533' // Número de desarrollo
      const message = `👋 ¡Hola! Nuevo pedido WEB\n\n` +
        `🧾 *Código: ${order.order_number}*\n` +
        `👤 Cliente: ${orderData.customer_name}\n` +
        `📱 Teléfono: ${orderData.customer_phone}\n` +
        `💰 Total: $${formatPrice(order.total)}\n\n` +
        `📦 Tipo: ${orderData.delivery_type === 'delivery' ? 'Domicilio' : 'Recoger en tienda'}\n` +
        `${orderData.delivery_type === 'delivery' ? `📍 Dirección: ${orderData.customer_address}\n` : ''}\n` +
        `*Productos:*\n` +
        orderData.items.map(item => `• ${item.quantity}x ${item.name} - $${formatPrice(item.price * item.quantity)}`).join('\n') +
        `${orderData.note ? `\n\n📝 Nota: ${orderData.note}` : ''}`
      
      const whatsappLink = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`
      
      // Guardar resultado
      orderResult.value = {
        order_number: order.order_number,
        whatsapp_link: whatsappLink
      }
      
      // Limpiar carrito y cerrar drawer
      cart.value = []
      isCheckoutOpen.value = false
      
      // Mostrar modal de éxito
      showSuccessModal.value = true
      
      // Reset del estado de submitting en el drawer
      if (checkoutDrawer.value) {
        checkoutDrawer.value.resetSubmitting()
      }
    }
  } catch (error) {
    console.error('Error enviando pedido:', error)
    alert('Error al enviar el pedido. Por favor intenta nuevamente.')
    
    if (checkoutDrawer.value) {
      checkoutDrawer.value.resetSubmitting()
    }
  }
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
  orderResult.value = {}
}

// Lifecycle
onMounted(() => {
  loadProducts()
  loadCategories()
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* 🎨 BLOB ANIMATIONS */
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

/* FADE IN UP */
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
</style>
