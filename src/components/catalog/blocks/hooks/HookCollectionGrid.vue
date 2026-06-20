<template>
  <!-- HOOK COLLECTION GRID: Visual grid of collections/categories -->
  <section class="py-12 md:py-16" :style="{ backgroundColor: palette.background || '#ffffff' }">
    <div class="max-w-6xl mx-auto px-5">
      <!-- Header -->
      <div class="flex items-end justify-between mb-8">
        <div>
          <p
            class="text-[10px] uppercase tracking-[0.24em] font-semibold mb-1.5"
            :style="{ color: palette.primary }"
          >{{ label }}</p>
          <h3
            class="text-2xl md:text-3xl tracking-tight"
            :style="{ fontFamily: fonts.heading + ', serif', fontWeight: 500, color: onBgText }"
          >{{ headline }}</h3>
        </div>
        <button
          v-if="ctaText"
          @click="$emit('cta')"
          class="hidden md:inline-flex items-center gap-1.5 text-[11px] font-semibold tracking-[0.06em] transition-colors"
          :style="{ color: palette.primary }"
        >
          {{ ctaText }}
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
          </svg>
        </button>
      </div>

      <!-- Grid: 2 tall + 2 short on desktop, 2-col scroll on mobile -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3">
        <div
          v-for="(collection, idx) in displayCollections"
          :key="'col-' + idx"
          class="relative overflow-hidden group cursor-pointer"
          :class="idx < 2 ? 'md:row-span-2 aspect-[3/4]' : 'aspect-square'"
          @click="$emit('cta')"
        >
          <!-- Image -->
          <img
            v-if="collection.image"
            :src="collection.image"
            :alt="collection.label"
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.06]"
          />
          <div
            v-else
            class="absolute inset-0"
            :style="{ backgroundColor: getCollectionBg(idx) }"
          ></div>

          <!-- Overlay -->
          <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/10 to-transparent group-hover:from-black/65 transition-all duration-500"></div>

          <!-- Label -->
          <div class="absolute bottom-0 left-0 right-0 p-4 md:p-5 z-10">
            <p
              class="text-white text-sm md:text-base font-semibold tracking-wide leading-tight"
              :style="{ fontFamily: fonts.heading + ', serif' }"
            >{{ collection.label }}</p>
            <p
              v-if="collection.count"
              class="text-white/60 text-[10px] uppercase tracking-[0.14em] mt-1 font-medium"
            >{{ collection.count }} productos</p>
          </div>

          <!-- Hover arrow -->
          <div class="absolute top-3 right-3 w-7 h-7 bg-white/0 group-hover:bg-white/90 rounded-full flex items-center justify-center transition-all duration-300 opacity-0 group-hover:opacity-100 z-10">
            <svg class="w-3 h-3 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Mobile CTA -->
      <div v-if="ctaText" class="md:hidden text-center mt-6">
        <button
          @click="$emit('cta')"
          class="text-[12px] font-semibold tracking-[0.06em]"
          :style="{ color: palette.primary }"
        >{{ ctaText }} →</button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  headline: { type: String, default: 'Nuestras Colecciones' },
  label: { type: String, default: 'Explora' },
  collections: {
    type: Array,
    default: () => [
      { label: 'Primavera', image: '', count: null },
      { label: 'Casual', image: '', count: null },
      { label: 'Formal', image: '', count: null },
      { label: 'Accesorios', image: '', count: null }
    ]
  },
  ctaText: { type: String, default: 'Ver todo' },
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Inter' }) }
})

defineEmits(['cta'])

const isBackgroundDark = computed(() => {
  const bg = props.palette.background || '#ffffff'
  const hex = bg.replace('#', '')
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16)
  const b = parseInt(hex.substr(4, 2), 16)
  return (r * 299 + g * 587 + b * 114) / 1000 < 128
})

const onBgText = computed(() => isBackgroundDark.value ? '#ffffff' : (props.palette.text_dark || '#0a0a0a'))

const displayCollections = computed(() => props.collections.slice(0, 4))

const getCollectionBg = (idx) => {
  const shades = [
    props.palette.primary || '#6366f1',
    props.palette.accent || '#3b82f6',
    props.palette.secondary || '#94a3b8',
    props.palette.primary + 'AA' || '#6366f1AA'
  ]
  return shades[idx % shades.length]
}
</script>
