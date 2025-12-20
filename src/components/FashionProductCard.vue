<template>
  <!-- Fashion Product Card - Digital Showroom Style -->
  <div class="group cursor-pointer">
    
    <!-- Imagen Principal (Aspect Ratio 3:4 Portrait - 70% de la tarjeta) -->
    <div class="relative aspect-[3/4] bg-gray-100 dark:bg-zinc-800 rounded-2xl overflow-hidden mb-3">
      <img :src="getProductImage(product)" 
           :alt="product.name" 
           @error="handleImageError"
           @click="$emit('view', product)"
           class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out">
      
      <!-- Badges Discretos -->
      <div class="absolute top-3 left-3 flex flex-col gap-2">
        <!-- Badge "Nuevo" si producto es reciente -->
        <span v-if="isNewProduct" 
              class="px-2 py-1 bg-white/90 dark:bg-black/60 backdrop-blur-sm text-gray-900 dark:text-white text-[10px] font-medium uppercase tracking-wider rounded-md">
          Nuevo
        </span>
        
        <!-- Punto Rojo para Stock Bajo -->
        <div v-if="isLowStock" 
             class="w-2 h-2 bg-red-500 rounded-full animate-pulse"
             title="Stock bajo"></div>
      </div>
      
      <!-- Botón Editar (Solo visible en hover, flotante) -->
      <button @click.stop="$emit('edit', product)"
              class="absolute bottom-3 right-3 w-10 h-10 bg-white/90 dark:bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:bg-white dark:hover:bg-black/80">
        <svg class="w-4 h-4 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
        </svg>
      </button>
    </div>

    <!-- Info del Producto (30% restante - Minimalista) -->
    <div class="px-1" @click="$emit('view', product)">
      <!-- Categoría -->
      <p class="text-[10px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-1">
        {{ product.category?.name || 'Sin categoría' }}
      </p>
      
      <!-- Nombre del Producto (Tipografía Elegante) -->
      <h3 class="text-sm font-light text-gray-900 dark:text-white leading-tight mb-2 line-clamp-2 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors" 
          :title="product.name">
        {{ product.name }}
      </h3>
      
      <!-- Precio -->
      <div class="mb-3">
        <span v-if="priceRange.isRange" class="text-sm font-medium text-gray-900 dark:text-white">
          Desde ${{ formatCurrency(priceRange.min) }}
        </span>
        <span v-else class="text-sm font-medium text-gray-900 dark:text-white">
          ${{ formatCurrency(priceRange.min) }}
        </span>
      </div>
      
      <!-- Visualizador de Variantes (Swatches) -->
      <div v-if="hasVariants" class="space-y-2">
        <!-- Color Swatches (Círculos pequeños) -->
        <div v-if="variantColors.length > 0" class="flex items-center gap-1.5">
          <div v-for="(color, index) in variantColors.slice(0, 5)" 
               :key="index"
               class="w-5 h-5 rounded-full border-2 border-gray-300 dark:border-zinc-700"
               :style="{ backgroundColor: getColorHex(color) }"
               :title="color"></div>
          <span v-if="variantColors.length > 5" 
                class="text-[10px] text-gray-400 dark:text-zinc-500">
            +{{ variantColors.length - 5 }}
          </span>
        </div>
        
        <!-- Tallas disponibles -->
        <div v-if="variantSizes.length > 0" class="flex items-center gap-1 flex-wrap">
          <span v-for="(size, index) in variantSizes" 
                :key="index"
                class="text-[10px] text-gray-400 dark:text-zinc-500 font-light">
            {{ size }}<span v-if="index !== variantSizes.length - 1"> ·</span>
          </span>
        </div>
      </div>
      
      <!-- Stock para productos sin variantes -->
      <div v-else class="text-[10px] text-gray-400 dark:text-zinc-500 font-light">
        Stock: {{ product.current_stock || 0 }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

defineEmits(['view', 'edit'])

// 🎨 Helper: Detectar si tiene variantes
const hasVariants = computed(() => {
  const variants = props.product.variants || []
  return variants.length > 1 // Más de 1 variante = producto con opciones
})

// 📊 Helper: Calcular rango de precios
const priceRange = computed(() => {
  const variants = props.product.variants || []
  
  if (variants.length <= 1) {
    return {
      isRange: false,
      min: props.product.sale_price || 0,
      max: props.product.sale_price || 0
    }
  }
  
  const prices = variants
    .map(v => parseFloat(v.sale_price || v.price || 0))
    .filter(p => p > 0)
  
  if (prices.length === 0) {
    return {
      isRange: false,
      min: props.product.sale_price || 0,
      max: props.product.sale_price || 0
    }
  }
  
  const min = Math.min(...prices)
  const max = Math.max(...prices)
  
  return {
    isRange: min !== max,
    min,
    max
  }
})

// 🎨 Helper: Extraer colores de variantes
const variantColors = computed(() => {
  const variants = props.product.variants || []
  const colors = []
  
  variants.forEach(variant => {
    const optionValues = variant.option_values || []
    optionValues.forEach(ov => {
      const optionName = ov.option?.name?.toLowerCase() || ''
      if (optionName.includes('color') || optionName.includes('colour')) {
        const colorValue = ov.value?.toLowerCase()
        // Mapear nombres de colores a códigos hex
        const colorMap = {
          'rojo': '#EF4444',
          'red': '#EF4444',
          'azul': '#3B82F6',
          'blue': '#3B82F6',
          'verde': '#10B981',
          'green': '#10B981',
          'amarillo': '#F59E0B',
          'yellow': '#F59E0B',
          'negro': '#1F2937',
          'black': '#1F2937',
          'blanco': '#F9FAFB',
          'white': '#F9FAFB',
          'gris': '#6B7280',
          'gray': '#6B7280',
          'rosa': '#EC4899',
          'pink': '#EC4899',
          'morado': '#8B5CF6',
          'purple': '#8B5CF6',
          'naranja': '#F97316',
          'orange': '#F97316'
        }
        
        if (colorValue && !colors.includes(colorMap[colorValue])) {
          colors.push(colorMap[colorValue] || '#6B7280')
        }
      }
    })
  })
  
  return colors
})

// 📏 Helper: Extraer tallas de variantes
const variantSizes = computed(() => {
  const variants = props.product.variants || []
  const sizes = []
  
  variants.forEach(variant => {
    const optionValues = variant.option_values || []
    optionValues.forEach(ov => {
      const optionName = ov.option?.name?.toLowerCase() || ''
      if (optionName.includes('talla') || optionName.includes('size') || optionName.includes('tamaño')) {
        const sizeValue = ov.value
        if (sizeValue && !sizes.includes(sizeValue)) {
          sizes.push(sizeValue)
        }
      }
    })
  })
  
  // Ordenar tallas de forma lógica
  const sizeOrder = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL']
  return sizes.sort((a, b) => {
    const indexA = sizeOrder.indexOf(a.toUpperCase())
    const indexB = sizeOrder.indexOf(b.toUpperCase())
    if (indexA !== -1 && indexB !== -1) return indexA - indexB
    return a.localeCompare(b)
  })
})

// ⚠️ Helper: Stock bajo
const isLowStock = computed(() => {
  const currentStock = props.product.current_stock || 0
  const minStock = props.product.min_stock || 0
  return currentStock <= minStock
})

// 🆕 Helper: Producto nuevo (menos de 7 días)
const isNewProduct = computed(() => {
  if (!props.product.created_at) return false
  const createdDate = new Date(props.product.created_at)
  const now = new Date()
  const diffDays = (now - createdDate) / (1000 * 60 * 60 * 24)
  return diffDays <= 7
})

// 🎨 Helper: Mapeo de nombres de colores a hex
const getColorHex = (colorName) => {
  const colorMap = {
    'rojo': '#EF4444', 'red': '#EF4444',
    'azul': '#3B82F6', 'blue': '#3B82F6',
    'verde': '#10B981', 'green': '#10B981',
    'amarillo': '#F59E0B', 'yellow': '#F59E0B',
    'negro': '#1F2937', 'black': '#1F2937',
    'blanco': '#F9FAFB', 'white': '#F9FAFB',
    'gris': '#6B7280', 'gray': '#6B7280', 'grey': '#6B7280',
    'rosa': '#EC4899', 'pink': '#EC4899',
    'morado': '#8B5CF6', 'purple': '#8B5CF6',
    'naranja': '#F97316', 'orange': '#F97316',
    'café': '#92400E', 'brown': '#92400E',
    'beige': '#D6C8B0', 'crema': '#FEF3C7', 'cream': '#FEF3C7'
  }
  return colorMap[colorName.toLowerCase()] || '#94A3B8'
}

// 🖼️ Helper: Obtener imagen del producto
const getProductImage = (product) => {
  if (product.image_url) {
    // Si es URL externa, devolverla directamente
    if (product.image_url.startsWith('http://') || product.image_url.startsWith('https://')) {
      return product.image_url
    }
    // Si es ruta del servidor, asegurar que esté correcta
    if (product.image_url.startsWith('/storage/')) {
      return product.image_url
    }
    return `/storage/${product.image_url}`
  }
  
  // Placeholder por defecto
  return 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=400&h=400&fit=crop'
}

const handleImageError = (e) => {
  e.target.src = 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=400&h=400&fit=crop'
}

// 💰 Helper: Formatear moneda
const formatCurrency = (value) => {
  const num = parseFloat(value || 0)
  return num.toLocaleString('es-CL', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}
</script>

<style scoped>
/* Animación suave para hover de imagen */
.group:hover img {
  filter: brightness(1.05);
}

/* Efecto de elevación en hover */
.group:hover {
  transform: translateY(-4px);
}
</style>
