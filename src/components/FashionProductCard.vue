<template>
  <!-- Fashion Product Card - Digital Showroom Style -->
  <div class="group cursor-pointer">
    
    <!-- Imagen Principal (Aspect Ratio 3:4 Portrait) - MÁS PEQUEÑA -->
    <div class="relative aspect-[3/4] rounded-xl overflow-hidden mb-2.5 border border-gray-200 dark:border-zinc-700" 
         :class="(product.image_url && product.image_url.length > 10) ? 'bg-white dark:bg-zinc-800' : 'bg-gradient-to-br from-gray-100 to-gray-200 dark:from-zinc-700 dark:to-zinc-800'">
      <img v-if="product.image_url && product.image_url.length > 10"
           :src="getProductImage(product)" 
           :alt="product.name" 
           @error="handleImageError"
           @click="$emit('view', product)"
           class="w-full h-full object-contain transform group-hover:scale-105 transition-transform duration-700 ease-out">
      
      <!-- Placeholder elegante cuando NO hay imagen -->
      <div v-else 
           @click="$emit('view', product)"
           class="w-full h-full flex items-center justify-center cursor-pointer">
        <div class="text-center">
          <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
        </div>
      </div>
      
      <!-- Badge Stock Bajo -->
      <div v-if="isLowStock" 
           class="absolute top-3 left-3 w-2 h-2 bg-red-500 rounded-full animate-pulse"
           title="Stock bajo"></div>
      
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
        <!-- Colores disponibles (Solo círculos) -->
        <div v-if="variantColors.length > 0" class="flex items-center gap-1.5 flex-wrap">
          <span 
            v-for="(color, index) in variantColors.slice(0, 8)" 
            :key="index"
            :style="{ backgroundColor: getColorDisplay(color) }"
            :title="color"
            :class="[
              'w-5 h-5 rounded-full shadow-sm border',
              (color.toUpperCase() === '#FFFFFF' || getColorDisplay(color).toUpperCase() === '#FFFFFF') ? 'border-gray-300 dark:border-zinc-600' : 'border-white/20'
            ]">
          </span>
          <span v-if="variantColors.length > 8" 
                class="text-[10px] text-gray-500 dark:text-zinc-400 font-medium ml-1">
            +{{ variantColors.length - 8 }}
          </span>
        </div>
        
        <!-- Tallas disponibles -->
        <div v-if="variantSizes.length > 0" class="flex items-center gap-1 flex-wrap">
          <span class="text-[10px] text-gray-500 dark:text-zinc-400 font-medium">Tallas:</span>
          <span v-for="(size, index) in variantSizes" 
                :key="index"
                class="text-[10px] text-gray-600 dark:text-zinc-300 font-light">
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

// 🎨 Helper: Detectar si tiene variantes REALES (no productos simples)
const hasVariants = computed(() => {
  const variants = props.product.variants || []
  if (variants.length === 0) return false
  // Producto simple: 1 variante sin opciones
  if (variants.length === 1) {
    return variants[0].options && variants[0].options.length > 0
  }
  // Múltiples variantes
  return true
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
    // Usar options_summary que ya viene formateado desde el backend
    const optionsSummary = variant.options_summary || []
    optionsSummary.forEach(option => {
      const optionName = option.name?.toLowerCase() || ''
      if (optionName.includes('color') || optionName.includes('colour')) {
        const colorValue = option.value
        if (colorValue && !colors.includes(colorValue)) {
          colors.push(colorValue)
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
    // Usar options_summary que ya viene formateado desde el backend
    const optionsSummary = variant.options_summary || []
    optionsSummary.forEach(option => {
      const optionName = option.name?.toLowerCase() || ''
      if (optionName.includes('talla') || optionName.includes('size') || optionName.includes('tamaño')) {
        const sizeValue = option.value
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

// 🎨 Helper: Detecta si es HEX o nombre de color y devuelve HEX
const getColorDisplay = (value) => {
  // Si ya es un código HEX, devolverlo directamente
  if (value.startsWith('#')) {
    return value
  }
  
  // Si es un nombre de color, convertirlo usando el diccionario
  return getColorHex(value) || value
}

// 🎨 Helper: Mapeo de nombres de colores a hex (expandido)
const getColorHex = (colorName) => {
  const colorMap = {
    // ROJOS
    'rojo': '#EF4444', 'red': '#EF4444',
    'rojo oscuro': '#B91C1C', 'rojo claro': '#FCA5A5', 'rojo brillante': '#DC2626',
    'carmesí': '#DC143C', 'carmesi': '#DC143C', 'crimson': '#DC143C',
    'escarlata': '#FF2400', 'granate': '#800000', 'burdeos': '#7C0A02',
    'vino': '#722F37', 'cereza': '#DE3163',
    
    // AZULES
    'azul': '#3B82F6', 'blue': '#3B82F6',
    'azul oscuro': '#1E3A8A', 'azul claro': '#93C5FD', 'azul cielo': '#87CEEB',
    'azul marino': '#000080', 'marino': '#000080', 'navy': '#000080',
    'azul rey': '#4169E1', 'azul eléctrico': '#7DF9FF', 'azul turquesa': '#40E0D0',
    'turquesa': '#40E0D0', 'cian': '#00FFFF', 'celeste': '#B0E0E6',
    
    // VERDES
    'verde': '#10B981', 'green': '#10B981',
    'verde oscuro': '#065F46', 'verde claro': '#86EFAC', 'verde limón': '#32CD32',
    'verde marino': '#2E8B57', 'verde oliva': '#808000', 'oliva': '#808000',
    'verde menta': '#98FF98', 'menta': '#98FF98', 'esmeralda': '#50C878',
    
    // AMARILLOS
    'amarillo': '#F59E0B', 'yellow': '#F59E0B',
    'amarillo claro': '#FDE68A', 'amarillo oscuro': '#D97706',
    'oro': '#FFD700', 'dorado': '#FFD700', 'gold': '#FFD700',
    
    // NARANJAS
    'naranja': '#F97316', 'orange': '#F97316',
    'naranja claro': '#FDBA74', 'naranja oscuro': '#C2410C',
    'coral': '#FF7F50', 'durazno': '#FFDAB9', 'salmón': '#FA8072', 'salmon': '#FA8072',
    
    // ROSAS
    'rosa': '#EC4899', 'pink': '#EC4899',
    'rosa claro': '#FBB6CE', 'rosa oscuro': '#BE185D',
    'magenta': '#FF00FF', 'fucsia': '#FF00FF', 'fuchsia': '#FF00FF',
    
    // MORADOS
    'morado': '#8B5CF6', 'purple': '#8B5CF6',
    'morado claro': '#C4B5FD', 'morado oscuro': '#6B21A8',
    'púrpura': '#A020F0', 'purpura': '#A020F0', 'violeta': '#8F00FF', 'lila': '#C8A2C8',
    
    // MARRONES
    'café': '#92400E', 'brown': '#92400E', 'marrón': '#A52A2A', 'marron': '#A52A2A',
    'café claro': '#D2691E', 'café oscuro': '#654321', 'chocolate': '#D2691E',
    
    // NEUTROS
    'beige': '#D4A373', 'crema': '#FFFDD0', 'cream': '#FFFDD0',
    'gris': '#6B7280', 'gray': '#6B7280', 'grey': '#6B7280',
    'gris claro': '#D1D5DB', 'gris oscuro': '#374151',
    'plata': '#C0C0C0', 'plateado': '#C0C0C0', 'silver': '#C0C0C0',
    
    // BLANCOS/NEGROS
    'negro': '#1F2937', 'black': '#1F2937',
    'blanco': '#F9FAFB', 'white': '#F9FAFB'
  }
  
  return colorMap[colorName.toLowerCase()] || '#94A3B8'
}

// 🖼️ Helper: Obtener imagen del producto
const getProductImage = (product) => {
  if (product.image_url && product.image_url.length > 10) {
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
  
  // Si no hay imagen, no devolver nada (se mostrará el placeholder)
  return null
}

const handleImageError = (e) => {
  // Si falla la imagen, ocultar el elemento para mostrar el placeholder
  e.target.style.display = 'none'
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
