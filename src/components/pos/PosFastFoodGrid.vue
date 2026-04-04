<template>
  <!-- 🍔 MODO RESTAURANTE / COMIDA RÁPIDA - Diseño Premium Profesional -->
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4 pb-20 content-start">
    <div
      v-for="product in products"
      :key="product.id"
      class="group relative cursor-pointer"
      @click="$emit('add-to-cart', product)"
    >
      <!-- Card con diseño profesional -->
      <div class="relative bg-white dark:bg-zinc-800/90 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg dark:shadow-black/20 dark:hover:shadow-black/40 border border-gray-100 dark:border-zinc-700/60 transition-all duration-200 hover:-translate-y-0.5">
        
        <!-- 🖼️ Contenedor de imagen -->
        <div class="aspect-[4/3] relative flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-zinc-800">
           
           <!-- Badge de cantidad en carrito -->
           <div v-if="getProductQuantityInCart(product.id) > 0" 
                class="absolute top-2.5 left-2.5 z-20 min-w-7 h-7 px-2 rounded-lg bg-orange-500 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-orange-500/25 ring-2 ring-white/90 dark:ring-zinc-900/90">
             {{ getProductQuantityInCart(product.id) }}
           </div>
           
           <!-- Badge "Popular" o "Nuevo" -->
           <div v-if="product.is_new || product.is_popular" 
                class="absolute top-2.5 right-2.5 z-20 px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wide"
                :class="product.is_popular 
                  ? 'bg-emerald-500/90 text-white shadow-sm' 
                  : 'bg-amber-500/90 text-white shadow-sm'">
             {{ product.is_popular ? 'Popular' : 'Nuevo' }}
           </div>

           <!-- Imagen del producto -->
           <img
            v-if="product.image_url || product.image"
            :src="getProductImage(product)"
            :alt="product.name"
            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
            loading="lazy"
            @error="(e) => handleImageError(e, product)"
          />
          
          <!-- Placeholder minimalista -->
          <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-zinc-800 dark:to-zinc-800/80">
            <div class="w-16 h-16 rounded-2xl bg-white dark:bg-zinc-700/50 flex items-center justify-center shadow-inner">
              <svg class="w-8 h-8 text-gray-300 dark:text-zinc-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.20-1.10-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z"/>
              </svg>
            </div>
          </div>
          
          <!-- 🔘 BOTÓN AGREGAR - Profesional con micro-interacción -->
          <button 
            class="absolute bottom-2.5 right-2.5 w-10 h-10 rounded-xl bg-white dark:bg-zinc-700 text-orange-500 dark:text-orange-400 shadow-md shadow-black/10 dark:shadow-black/30 flex items-center justify-center transition-all duration-150 hover:bg-orange-500 hover:text-white hover:shadow-lg hover:shadow-orange-500/25 hover:scale-105 active:scale-95 z-10 border border-gray-100 dark:border-zinc-600"
            @click.stop="$emit('add-to-cart', product)"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
          </button>
        </div>

        <!-- 📝 Info del producto -->
        <div class="p-3">
          
          <!-- Categoria sutil -->
          <span class="text-[10px] font-medium uppercase tracking-wide text-gray-400 dark:text-zinc-500">
            {{ product.category_name || 'Menú' }}
          </span>
          
          <!-- Nombre del producto -->
          <h3 class="text-sm font-semibold text-gray-800 dark:text-zinc-100 leading-tight line-clamp-2 mt-0.5 mb-2 min-h-[36px]" :title="product.name">
            {{ product.name }}
          </h3>
          
          <!-- 💰 Precio - Grande y claro -->
          <div class="flex items-end justify-between">
            <span class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">
              ${{ product.price.toLocaleString() }}
            </span>
            <!-- Stock bajo -->
            <span v-if="getTotalStock(product) <= 5 && getTotalStock(product) > 0" 
                  class="text-[9px] font-semibold px-1.5 py-0.5 rounded bg-red-50 dark:bg-red-900/20 text-red-500 dark:text-red-400">
              Últimos {{ getTotalStock(product) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

// Props que recibe del padre
const props = defineProps({
  products: {
    type: Array,
    required: true,
    default: () => []
  },
  cartItems: {
    type: Array,
    default: () => []
  }
})

// Eventos que emite al padre
defineEmits(['add-to-cart'])

// Función para obtener cantidad de producto en el carrito
const getProductQuantityInCart = (productId) => {
  const item = props.cartItems.find(item => item.id === productId)
  return item ? item.quantity : 0
}

// Función para obtener stock total (incluyendo almacenes alternativos)
const getTotalStock = (product) => {
  let total = product.current_stock || product.stock || 0
  if (product.alternative_warehouses && Array.isArray(product.alternative_warehouses)) {
    total += product.alternative_warehouses.reduce((sum, w) => sum + (w.stock || 0), 0)
  }
  return total
}

// Generar avatar SVG para productos sin imagen
const generateAvatarSVG = (name) => {
  const initial = name ? name.charAt(0).toUpperCase() : '?'
  const colors = [
    { bg: '#FFF7ED', text: '#EA580C' },
    { bg: '#FEF3C7', text: '#D97706' },
    { bg: '#FFEDD5', text: '#C2410C' },
    { bg: '#FED7AA', text: '#9A3412' }
  ]
  const colorIndex = name ? name.charCodeAt(0) % colors.length : 0
  const color = colors[colorIndex]
  
  return `data:image/svg+xml,${encodeURIComponent(`
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
      <rect width="100" height="100" fill="${color.bg}"/>
      <text x="50" y="50" dominant-baseline="central" text-anchor="middle" 
            font-family="system-ui, -apple-system, sans-serif" font-size="42" font-weight="600" fill="${color.text}">
        ${initial}
      </text>
    </svg>
  `)}`
}

// Obtener imagen del producto
const getProductImage = (product) => {
  const url = product.image_url || product.image
  if (!url) {
    return generateAvatarSVG(product.name || 'Producto')
  }
  
  const trimmedUrl = url.trim()
  
  // Si es una ruta relativa de Laravel Storage
  if (trimmedUrl.startsWith('/storage/')) {
    const backendUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin
    return `${backendUrl}${trimmedUrl}`
  }
  
  // Si ya es una URL HTTP completa
  if ((trimmedUrl.startsWith('http://') || trimmedUrl.startsWith('https://')) && trimmedUrl.length > 20) {
    if (!trimmedUrl.includes('placeholder') && !trimmedUrl.includes('default') && !trimmedUrl.includes('no-image')) {
      return trimmedUrl
    }
  }
  
  // Si es un data URI
  if (trimmedUrl.startsWith('data:image')) {
    return trimmedUrl
  }
  
  return generateAvatarSVG(product.name || 'Producto')
}

// Manejar error de imagen
const handleImageError = (event, product) => {
  event.target.src = generateAvatarSVG(product.name || 'Producto')
}
</script>
