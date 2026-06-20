<template>
  <!-- HOOK BRAND MANIFESTO: Diseño premium editorial — elegante y profesional -->
  <section
    class="relative py-20 md:py-28 lg:py-32 overflow-hidden"
    :style="{ backgroundColor: sectionBg }"
  >
    <!-- Subtle decorative grid pattern -->
    <div class="absolute inset-0 pointer-events-none opacity-[0.03]">
      <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0); background-size: 32px 32px;" :style="{ color: onBgText }"></div>
    </div>

    <!-- Decorative vertical lines -->
    <div class="absolute top-0 bottom-0 left-1/4 w-px pointer-events-none" :style="{ backgroundColor: onBgText + '06' }"></div>
    <div class="absolute top-0 bottom-0 right-1/4 w-px pointer-events-none" :style="{ backgroundColor: onBgText + '06' }"></div>

    <!-- Gradient accent orb -->
    <div
      class="absolute -top-32 -right-32 w-96 h-96 rounded-full opacity-[0.06] blur-[120px] pointer-events-none"
      :style="{ backgroundColor: palette.primary }"
    ></div>

    <div class="relative z-10 max-w-3xl mx-auto px-8 text-center">
      <!-- Decorative top element — línea + label + línea -->
      <div class="flex items-center justify-center gap-5 mb-12">
        <div class="w-16 h-px" :style="{ background: `linear-gradient(to right, transparent, ${onBgText}20)` }"></div>
        <span
          class="text-[8px] uppercase tracking-[0.5em] font-bold relative"
          :style="{ color: palette.primary }"
        >
          <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: palette.primary + '40' }"></span>
          {{ label }}
          <span class="absolute -right-3 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: palette.primary + '40' }"></span>
        </span>
        <div class="w-16 h-px" :style="{ background: `linear-gradient(to left, transparent, ${onBgText}20)` }"></div>
      </div>

      <!-- Big statement headline — tipografía editorial grande -->
      <h3
        class="text-3xl md:text-4xl lg:text-[3.2rem] leading-[1.12] tracking-[-0.02em] mb-8"
        :style="{ fontFamily: fonts.heading + ', serif', fontWeight: 300, color: onBgText, fontStyle: 'italic' }"
      >{{ headline }}</h3>

      <!-- Separador decorativo horizontal -->
      <div class="flex items-center justify-center gap-3 mb-8">
        <div class="w-8 h-px" :style="{ backgroundColor: palette.primary + '50' }"></div>
        <div class="w-2 h-2 rotate-45 border" :style="{ borderColor: palette.primary + '40' }"></div>
        <div class="w-8 h-px" :style="{ backgroundColor: palette.primary + '50' }"></div>
      </div>

      <!-- Body text — refinado -->
      <p
        v-if="body"
        class="text-sm md:text-[15px] leading-[2] max-w-md mx-auto mb-12"
        :style="{ fontFamily: fonts.body + ', sans-serif', color: onBgText, opacity: 0.45 }"
      >{{ body }}</p>

      <!-- Signature / brand mark — con línea vertical elegante -->
      <div class="flex flex-col items-center gap-4">
        <div class="w-px h-10" :style="{ background: `linear-gradient(to bottom, ${onBgText}15, ${onBgText}08)` }"></div>
        <p
          class="text-[10px] uppercase tracking-[0.3em] font-semibold"
          :style="{ color: onBgText, opacity: 0.25, fontFamily: fonts.heading + ', serif' }"
        >{{ signature }}</p>
      </div>

      <!-- CTA — elegante, outline con efecto hover premium -->
      <button
        v-if="ctaText"
        @click="$emit('cta')"
        class="mt-12 px-10 py-3.5 text-[10px] font-bold uppercase tracking-[0.22em] border transition-all duration-400 hover:scale-[1.02] active:scale-[0.98] relative overflow-hidden group"
        :style="{ borderColor: palette.primary + '30', color: palette.primary, backgroundColor: 'transparent' }"
        @mouseenter="e => { e.target.style.backgroundColor = palette.primary + '08'; e.target.style.borderColor = palette.primary + '60' }"
        @mouseleave="e => { e.target.style.backgroundColor = 'transparent'; e.target.style.borderColor = palette.primary + '30' }"
      >
        <!-- Subtle shimmer effect on hover -->
        <span class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/5 to-transparent pointer-events-none"></span>
        {{ ctaText }}
      </button>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  headline: { type: String, default: 'Creemos en la belleza de lo auténtico' },
  body: { type: String, default: '' },
  label: { type: String, default: 'Nuestro Manifiesto' },
  signature: { type: String, default: '' },
  ctaText: { type: String, default: '' },
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Inter' }) },
  isMobilePreview: { type: Boolean, default: false }
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
const sectionBg = computed(() => {
  if (isBackgroundDark.value) return 'rgba(255,255,255,0.02)'
  return '#fafaf8'
})
</script>
