<template>
  <!-- HERO GENERAL PROMO: Mass retail banner slider style -->
  <section class="relative w-full h-[50vh] lg:h-[60vh] min-h-[400px] bg-slate-100 flex items-center overflow-hidden">
    <!-- Split Layout: Image on right, Content on left (like Amazon/MercadoLibre promos) -->
    <div class="absolute inset-0 z-0 flex md:flex-row flex-col">
      <!-- Left side (solid color) -->
      <div class="w-full md:w-1/2 h-full" :style="{ backgroundColor: bgPrimary }"></div>
      <!-- Right side (image with fade) -->
      <div class="w-full md:w-1/2 h-full relative">
        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-black/20 z-10 hidden md:block" :style="{ '--tw-gradient-from': bgPrimary + ' 0%', '--tw-gradient-to': 'transparent 100%' }"></div>
        <img
          :src="backgroundImage || 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&q=80&w=1200'"
          alt="Retail Promo"
          class="w-full h-full object-cover object-center"
        />
        <!-- Dark overlay for text readability on mobile -->
        <div class="absolute inset-0 bg-black/40 md:hidden z-10"></div>
      </div>
    </div>

    <div class="relative z-20 w-full max-w-7xl mx-auto px-6 lg:px-12 flex flex-col items-start text-left">
      <div class="max-w-xl">
        <span
          v-if="storeName"
          class="inline-block px-3 py-1 mb-4 rounded-full text-xs font-bold uppercase tracking-wider"
          :style="{ backgroundColor: palette.secondary || '#e2e8f0', color: '#1a1a1a' }"
        >
          OFERTAS EN {{ storeName }}
        </span>

        <h2
          class="text-4xl md:text-5xl lg:text-6xl font-black mb-4 leading-tight tracking-tight"
          :style="{ fontFamily: fonts.heading + ', sans-serif', color: textContrast }"
        >
          {{ headline || 'Encuentra todo lo que necesitas' }}
        </h2>

        <p
          class="text-lg md:text-xl opacity-90 mb-8 max-w-md font-medium leading-relaxed"
          :style="{ fontFamily: fonts.body + ', sans-serif', color: textContrast }"
        >
          {{ subheadline || 'Los mejores precios y la mayor variedad de productos en un solo lugar. ¡Aprovecha hoy!' }}
        </p>

        <div class="flex flex-wrap gap-4">
          <button
            class="px-8 py-4 rounded-lg font-bold text-base transition-transform active:scale-95 shadow-lg"
            :style="{ backgroundColor: palette.primary, color: '#ffffff' }"
          >
            {{ ctaText || 'Ver Todas las Ofertas' }}
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  headline: { type: String, default: '' },
  subheadline: { type: String, default: '' },
  ctaText: { type: String, default: '' },
  ctaSecondary: { type: String, default: '' },
  backgroundImage: { type: String, default: '' },
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Inter', body: 'Inter' }) },
  storeName: { type: String, default: '' }
})

const bgPrimary = computed(() => {
  return props.palette.background === '#ffffff' || !props.palette.background 
    ? '#f8fafc' // subtle gray if background is pure white
    : props.palette.background
})

const isBackgroundDark = computed(() => {
  const bg = bgPrimary.value
  const hex = bg.replace('#', '')
  if (hex.length !== 6) return false
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16)
  const b = parseInt(hex.substr(4, 2), 16)
  return (r * 299 + g * 587 + b * 114) / 1000 < 128
})

// Ensures text is always legible
const textContrast = computed(() => isBackgroundDark.value ? '#ffffff' : (props.palette.text_dark || '#0f172a'))
</script>
