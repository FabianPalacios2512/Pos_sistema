<template>
  <div 
    class="group relative flex flex-col h-full cursor-pointer transition-all duration-500 overflow-hidden rounded-2xl"
    :style="{ 
      backgroundColor: isBackgroundDark ? 'rgba(255, 255, 255, 0.04)' : '#ffffff', 
      border: '1px solid var(--border-subtle, rgba(0, 0, 0, 0.06))',
      boxShadow: isBackgroundDark ? 'none' : '0 2px 8px rgba(0,0,0,0.02)'
    }"
    @click="$emit('click')"
  >
    <!-- IMAGE CONTAINER -->
    <div 
      class="relative w-full overflow-hidden bg-neutral-50 dark:bg-zinc-950 transition-all duration-300"
      :class="aspectRatioClass"
    >
      <!-- Main Image -->
      <img
        v-if="hasImage"
        :src="currentImage"
        :alt="product.name"
        class="w-full h-full object-cover transition-all duration-700 ease-out"
        :class="[
          hoverImage && 'absolute inset-0 opacity-100 group-hover:opacity-0 group-hover:scale-105',
          !hoverImage && 'group-hover:scale-[1.04]'
        ]"
        @error="handleImageError"
      />

      <!-- Hover Image (If exists, shown on hover with smooth crossfade) -->
      <img
        v-if="hasImage && hoverImage"
        :src="hoverImage"
        :alt="product.name + ' - vista alternativa'"
        class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 scale-100 group-hover:scale-[1.04] transition-all duration-700 ease-out"
        @error="handleHoverImageError"
      />

      <!-- Fallback when no image -->
      <div 
        v-if="!hasImage || imageFailed" 
        class="w-full h-full flex flex-col items-center justify-center p-4 text-center opacity-40"
      >
        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
        </svg>
      </div>

      <!-- Quick Add Overlay Button (Micro-animation, slide up on hover in desktop) -->
      <button
        @click.stop="$emit('add-to-cart')"
        :disabled="product.stock === 0"
        class="absolute bottom-3 right-3 lg:bottom-4 lg:right-4 w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 z-10 shadow-lg active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed"
        :style="{ 
          backgroundColor: product.stock > 0 ? (palette.primary || '#000000') : '#9ca3af',
          color: '#ffffff'
        }"
      >
        <!-- Plus icon -->
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
      </button>

      <!-- BADGES SYSTEM (Top Left pills) -->
      <div class="absolute top-2.5 left-2.5 flex flex-col gap-1.5 z-10">
        <!-- New Badge -->
        <span 
          v-if="product.is_new" 
          class="px-2.5 py-1 bg-neutral-900/90 text-white text-[9px] font-bold uppercase tracking-wider rounded-md backdrop-blur-sm shadow-sm"
        >
          Nuevo
        </span>
        <!-- Savings / Off % -->
        <span 
          v-if="discountPercentage > 0" 
          class="px-2.5 py-1 bg-red-600/95 text-white text-[11px] font-black uppercase tracking-wider rounded-md backdrop-blur-sm shadow-md border border-red-500/30"
          style="box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);"
        >
          -{{ discountPercentage }}%
        </span>
        <!-- Urgent Stock -->
        <span 
          v-if="product.stock > 0 && product.stock <= 5" 
          class="px-2.5 py-1 bg-amber-500/95 text-white text-[9px] font-bold uppercase tracking-wider rounded-md backdrop-blur-sm shadow-sm text-shadow-sm"
        >
          Últimas {{ product.stock }}
        </span>
        <!-- Out of stock -->
        <span 
          v-if="product.stock === 0" 
          class="px-2.5 py-1 bg-neutral-900/85 text-white text-[9px] font-bold uppercase tracking-wider rounded-md backdrop-blur-sm"
        >
          Agotado
        </span>
      </div>
    </div>

    <!-- PRODUCT INFO -->
    <div class="p-3.5 flex flex-col flex-1">
      <!-- Category/Tag -->
      <p 
        class="text-[9px] uppercase tracking-[0.18em] mb-1 font-semibold truncate"
        :style="{ color: palette.primary || '#000000', opacity: 0.8 }"
      >
        {{ product.category || product.category_name || 'Colección' }}
      </p>

      <!-- Product Title -->
      <h3 
        class="text-xs md:text-sm font-normal line-clamp-2 leading-snug flex-1 group-hover:opacity-80 transition-opacity"
        :style="{ fontFamily: fonts.heading + ', serif', color: isBackgroundDark ? '#ffffff' : '#0a0a0a' }"
      >
        {{ product.name }}
      </h3>

      <!-- Fake Stars (Retail Psychology) -->
      <div class="flex items-center gap-1 mt-1.5 mb-2 opacity-90">
        <div class="flex text-[#FFB800]">
          <svg v-for="i in 5" :key="i" class="w-2.5 h-2.5" :class="i === 5 && (product.id % 3 === 0) ? 'text-gray-300' : 'text-[#FFB800]'" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
        </div>
        <span class="text-[9px] font-medium text-gray-500">({{ (product.id % 45) + 12 }})</span>
      </div>

      <!-- Price area with optional compare price -->
      <div class="flex items-center justify-between mt-auto pt-2 gap-2 border-t border-neutral-100 dark:border-neutral-800">
        <div class="flex items-baseline gap-1.5 flex-wrap">
          <!-- Discounted Price -->
          <span 
            class="text-sm font-bold tracking-tight"
            :style="{ color: isBackgroundDark ? '#ffffff' : '#0a0a0a' }"
          >
            {{ currencySymbol }}{{ formatPrice(product.price) }}
          </span>
          <!-- Original Compare Price -->
          <span 
            v-if="product.compare_at_price && parseFloat(product.compare_at_price) > parseFloat(product.price)" 
            class="text-[10px] text-neutral-400 line-through"
          >
            {{ currencySymbol }}{{ formatPrice(product.compare_at_price) }}
          </span>
        </div>

        <!-- Buy text CTA button on hover -->
        <span 
          class="text-[10px] font-bold uppercase tracking-[0.12em] opacity-80 group-hover:opacity-100 transition-all duration-300"
          :style="{ color: palette.primary || '#000000' }"
        >
          Ver detalle
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  palette: {
    type: Object,
    default: () => ({})
  },
  fonts: {
    type: Object,
    default: () => ({ heading: 'Playfair Display', body: 'Inter' })
  },
  currencySymbol: {
    type: String,
    default: '$'
  },
  aspectRatio: {
    type: String,
    default: '3/4'
  }
})

defineEmits(['click', 'add-to-cart'])

const imageFailed = ref(false)
const hoverImageFailed = ref(false)

const isBackgroundDark = computed(() => {
  const hex = (props.palette.background || '#ffffff').replace('#', '')
  if (hex.length !== 6) return false
  const r = parseInt(hex.substring(0, 2), 16)
  const g = parseInt(hex.substring(2, 4), 16)
  const b = parseInt(hex.substring(4, 6), 16)
  return (0.299 * r + 0.587 * g + 0.114 * b) / 255 < 0.5
})

const hasImage = computed(() => {
  return (props.product.images && props.product.images.length > 0) || 
         (props.product.image_url && props.product.image_url !== 'https://via.placeholder.com/400')
})

const currentImage = computed(() => {
  if (imageFailed.value) return ''
  return props.product.images && props.product.images.length > 0 
    ? props.product.images[0] 
    : props.product.image_url
})

const hoverImage = computed(() => {
  if (hoverImageFailed.value) return null
  return props.product.images && props.product.images.length > 1 
    ? props.product.images[1] 
    : null
})

const aspectRatioClass = computed(() => {
  switch (props.aspectRatio) {
    case '1/1': return 'aspect-square'
    case '4/5': return 'aspect-[4/5]'
    case '2/3': return 'aspect-[2/3]'
    default: return 'aspect-[3/4]'
  }
})

const discountPercentage = computed(() => {
  const price = parseFloat(props.product.price || 0)
  const compare = parseFloat(props.product.compare_at_price || 0)
  if (compare > price) {
    return Math.round(((compare - price) / compare) * 100)
  }
  return 0
})

const formatPrice = (price) => {
  return parseFloat(price).toLocaleString('es-CO', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  })
}

const handleImageError = () => {
  imageFailed.value = true
}

const handleHoverImageError = () => {
  hoverImageFailed.value = true
}
</script>