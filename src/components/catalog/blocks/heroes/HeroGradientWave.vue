<template>
  <!-- HERO GRADIENT WAVE: Image-free premium animated gradient mesh header -->
  <section
    class="relative w-full overflow-hidden flex items-center justify-center gradient-mesh-wrapper"
    :style="{ height: isMobilePreview ? '360px' : '480px', '--color-p': palette.primary || '#4f46e5', '--color-s': palette.secondary || '#9333ea', '--color-a': palette.accent || '#ec4899', '--color-bg': palette.background || '#ffffff' }"
  >
    <!-- Background Animated Gradient Layers -->
    <div class="absolute inset-0 z-0 mesh-gradient"></div>
    <div class="absolute inset-0 z-0 mesh-gradient-overlay backdrop-blur-[40px] opacity-90"></div>

    <!-- Animated Wave Shape at the bottom -->
    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none z-10">
      <svg class="relative block w-full h-[60px]" viewBox="0 0 1200 120" preserveAspectRatio="none">
        <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V0C26.9,4.75,55.05,16.3,81.82,22.75,156.82,40.75,234,68.75,321.39,56.44Z" :fill="palette.background || '#ffffff'"></path>
      </svg>
    </div>

    <!-- Content Card (Glassmorphism) -->
    <div 
      class="relative z-20 text-center px-8 py-10 md:py-12 rounded-3xl max-w-xl mx-4 border backdrop-blur-md"
      :style="{ 
        backgroundColor: isBackgroundDark ? 'rgba(0, 0, 0, 0.45)' : 'rgba(255, 255, 255, 0.45)', 
        borderColor: isBackgroundDark ? 'rgba(255, 255, 255, 0.12)' : 'rgba(0, 0, 0, 0.06)',
        boxShadow: isBackgroundDark ? '0 8px 32px 0 rgba(0, 0, 0, 0.37)' : '0 8px 32px 0 rgba(31, 38, 135, 0.05)'
      }"
    >
      <!-- Mini tagline -->
      <span 
        v-if="subheadline" 
        class="inline-block px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-[0.2em] mb-4"
        :style="{ 
          backgroundColor: isBackgroundDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.04)', 
          color: onBgText 
        }"
      >
        Nueva Colección
      </span>

      <!-- Headline -->
      <h2
        class="text-3xl md:text-5xl leading-[1.1] mb-4 tracking-tight"
        :style="{ fontFamily: fonts.heading + ', serif', fontWeight: 600, color: onBgText }"
      >{{ headline }}</h2>

      <!-- Subheadline description -->
      <p
        v-if="subheadline"
        class="text-xs md:text-sm leading-relaxed mb-6 max-w-sm mx-auto"
        :style="{ fontFamily: fonts.body + ', sans-serif', color: onBgText, opacity: 0.8 }"
      >{{ subheadline }}</p>

      <!-- CTA -->
      <button
        v-if="ctaText"
        @click="$emit('cta')"
        class="px-8 py-3 rounded-full text-[11px] font-bold uppercase tracking-[0.16em] transition-all duration-300 hover:scale-[1.04]"
        :style="{ backgroundColor: palette.primary || '#000000', color: '#ffffff' }"
      >{{ ctaText }}</button>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  headline: { type: String, default: '' },
  subheadline: { type: String, default: '' },
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Inter' }) },
  ctaText: { type: String, default: '' },
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
</script>

<style scoped>
.gradient-mesh-wrapper {
  background-color: var(--color-bg);
}

.mesh-gradient {
  background-image: 
    radial-gradient(at 10% 20%, var(--color-p) 0px, transparent 50%),
    radial-gradient(at 90% 10%, var(--color-s) 0px, transparent 50%),
    radial-gradient(at 50% 80%, var(--color-a) 0px, transparent 50%),
    radial-gradient(at 80% 90%, var(--color-p) 0px, transparent 50%),
    radial-gradient(at 20% 90%, var(--color-s) 0px, transparent 50%);
  background-size: 200% 200%;
  animation: moveGradient 18s ease infinite;
}

@keyframes moveGradient {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}
</style>
