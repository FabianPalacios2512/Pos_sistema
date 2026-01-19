<template>
  <!-- Fashion Product Card - Digital Showroom Style -->
  <div class="group cursor-pointer transition-all duration-300">
    
    <!-- Imagen Principal (Aspect Ratio 3:4 Portrait) -->
    <div class="relative aspect-[3/4] rounded-2xl overflow-hidden mb-3 shadow-sm dark:shadow-black/40 ring-1 ring-gray-200/60 dark:ring-white/5 group-hover:ring-gray-300 dark:group-hover:ring-white/10 group-hover:shadow-lg dark:group-hover:shadow-black/60 transition-all duration-300" 
         :class="getProductImage(product) ? 'bg-gradient-to-b from-gray-50 to-gray-100 dark:from-zinc-900 dark:to-zinc-950' : 'bg-gradient-to-br from-gray-100 to-gray-200 dark:from-zinc-800 dark:to-zinc-900'">
      <img v-if="getProductImage(product)"
           :src="getProductImage(product)" 
           :alt="product.name" 
           @error="handleImageError"
           @click="$emit('view', product)"
           class="w-full h-full object-contain transform group-hover:scale-105 transition-transform duration-700 ease-out">
      
      <!-- Placeholder elegante cuando NO hay imagen -->
      <div v-else 
           @click="$emit('view', product)"
           class="w-full h-full flex items-center justify-center cursor-pointer bg-gradient-to-br from-gray-100 to-gray-200 dark:from-zinc-800/80 dark:to-zinc-900">
        <div class="text-center">
          <div class="w-20 h-20 mx-auto rounded-2xl bg-white/60 dark:bg-zinc-700/40 flex items-center justify-center mb-2 ">
            <svg class="w-10 h-10 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
          </div>
        </div>
      </div>
      
      <!-- Overlay gradiente sutil para mejorar legibilidad -->
      <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none dark:from-black/40"></div>
      
      <!-- Badge Stock Bajo -->
      <div v-if="isLowStock" 
           class="absolute top-3 left-3 px-2 py-1 bg-red-500/90 dark:bg-red-600/90  rounded-full flex items-center gap-1.5 shadow-lg"
           title="Stock bajo">
        <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
        <span class="text-[9px] font-bold text-white uppercase tracking-wide">Bajo stock</span>
      </div>
      
      <!-- Botón Editar (Solo visible en hover, flotante) -->
      <button @click.stop="$emit('edit', product)"
              class="absolute bottom-3 right-3 w-10 h-10 bg-white/95 dark:bg-zinc-800/95  rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white dark:hover:bg-zinc-700 shadow-lg dark:shadow-black/50 ring-1 ring-black/5 dark:ring-white/10">
        <svg class="w-4 h-4 text-gray-700 dark:text-zinc-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
        </svg>
      </button>
    </div>

    <!-- Info del Producto (Minimalista y Elegante) -->
    <div class="px-0.5" @click="$emit('view', product)">
      <!-- Categoría -->
      <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-1.5">
        {{ product.category?.name || 'Sin categoría' }}
      </p>
      
      <!-- Nombre del Producto (Tipografía Elegante) -->
      <h3 class="text-sm font-normal text-gray-800 dark:text-zinc-100 leading-snug mb-2 line-clamp-2 group-hover:text-gray-600 dark:group-hover:text-white transition-colors" 
          :title="product.name">
        {{ product.name }}
      </h3>
      
      <!-- Precio con mejor contraste -->
      <div class="mb-3">
        <span v-if="priceRange.isRange" class="text-sm font-semibold text-gray-900 dark:text-white">
          Desde ${{ formatCurrency(priceRange.min) }}
        </span>
        <span v-else class="text-sm font-semibold text-gray-900 dark:text-white">
          ${{ formatCurrency(priceRange.min) }}
        </span>
      </div>
      
      <!-- Visualizador de Variantes (Swatches) Mejorado -->
      <div v-if="hasVariants" class="space-y-2.5">
        <!-- Colores disponibles (Círculos con mejor estilo) -->
        <div v-if="variantColors.length > 0" class="flex items-center gap-2 flex-wrap">
          <span 
            v-for="(color, index) in variantColors.slice(0, 8)" 
            :key="index"
            :style="{ backgroundColor: getColorDisplay(color) }"
            :title="color"
            :class="[
              'w-5 h-5 rounded-full shadow-md ring-1 ring-offset-1 ring-offset-white dark:ring-offset-zinc-900 transition-transform duration-200 hover:scale-110',
              (color.toUpperCase() === '#FFFFFF' || getColorDisplay(color).toUpperCase() === '#FFFFFF') ? 'ring-gray-300 dark:ring-zinc-600' : 'ring-black/10 dark:ring-white/20'
            ]">
          </span>
          <span v-if="variantColors.length > 8" 
                class="text-[10px] text-gray-500 dark:text-zinc-400 font-semibold ml-0.5">
            +{{ variantColors.length - 8 }}
          </span>
        </div>
        
        <!-- Tallas disponibles con mejor estilo -->
        <div v-if="variantSizes.length > 0" class="flex items-center gap-1.5 flex-wrap">
          <span v-for="(size, index) in variantSizes" 
                :key="index"
                class="text-[10px] px-2 py-0.5 bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-300 font-medium rounded-md">
            {{ size }}
          </span>
        </div>
      </div>
      
      <!-- Stock para productos sin variantes -->
      <div v-else class="text-[11px] text-gray-500 dark:text-zinc-500 font-medium">
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
  // 1. Primero verificar si hay imágenes en la galería (relación images)
  if (product.images && Array.isArray(product.images) && product.images.length > 0) {
    const primaryImage = product.images.find(img => img.is_primary) || product.images[0]
    const imageUrl = primaryImage?.image_url || primaryImage?.url
    if (imageUrl) {
      return processImageUrl(imageUrl)
    }
  }
  
  // 2. Verificar image_url del producto
  if (product.image_url && product.image_url.length > 10) {
    return processImageUrl(product.image_url)
  }
  
  // Si no hay imagen, no devolver nada (se mostrará el placeholder)
  return null
}

// 📸 Helper: Procesar URL de imagen para el backend
const processImageUrl = (url) => {
  if (!url || typeof url !== 'string') return null
  
  const trimmedUrl = url.trim()
  
  // Si ya es URL completa, devolverla
  if (trimmedUrl.startsWith('http://') || trimmedUrl.startsWith('https://')) {
    return trimmedUrl
  }
  
  // Si es data URI
  if (trimmedUrl.startsWith('data:image')) {
    return trimmedUrl
  }
  
  // Si es ruta de storage, construir URL completa al backend
  if (trimmedUrl.startsWith('/storage')) {
    const backendUrl = `http://${window.location.hostname}:8000`
    return `${backendUrl}${trimmedUrl}`
  }
  
  // Si no empieza con /, agregar /storage/ y construir URL
  if (!trimmedUrl.startsWith('/')) {
    const backendUrl = `http://${window.location.hostname}:8000`
    return `${backendUrl}/storage/${trimmedUrl}`
  }
  
  // Ruta relativa genérica
  const backendUrl = `http://${window.location.hostname}:8000`
  return `${backendUrl}${trimmedUrl}`
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
  filter: brightness(1.02);
}

/* Efecto de elevación en hover - más suave */
.group {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.group:hover {
  transform: translateY(-6px);
}

/* Mejora de antialiasing para textos pequeños */
p, span, h3 {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
</style>
