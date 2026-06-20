<template>
  <!-- HERO MINIMAL: Ultra-clean, Apple-inspired whitespace -->
  <section
    class="relative w-full flex flex-col items-center justify-center text-center overflow-hidden"
    :style="{ minHeight: isMobilePreview ? '320px' : '400px', backgroundColor: palette.background || '#ffffff' }"
  >
    <!-- Content -->
    <div class="relative z-10 px-6">
      <!-- Minimal headline -->
      <h2
        class="text-3xl md:text-5xl leading-[1.08] tracking-tight mb-4"
        :style="{ fontFamily: fonts.heading + ', serif', fontWeight: 300, color: onBgText }"
      >{{ headline }}</h2>

      <!-- Thin separator -->
      <div class="w-8 h-px mx-auto mb-4" :style="{ backgroundColor: onBgText + '20' }"></div>

      <!-- Subheadline -->
      <p
        v-if="subheadline"
        class="text-sm leading-relaxed max-w-sm mx-auto mb-8"
        :style="{ fontFamily: fonts.body + ', sans-serif', color: onBgText, opacity: 0.4, letterSpacing: '0.02em' }"
      >{{ subheadline }}</p>

      <!-- CTA: understated link style -->
      <button
        v-if="ctaText"
        @click="$emit('cta')"
        class="inline-flex items-center gap-2 text-[12px] font-medium tracking-[0.08em] transition-all duration-300 group"
        :style="{ color: palette.primary || onBgText }"
      >
        {{ ctaText }}
        <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
        </svg>
      </button>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  headline: { type: String, default: '' },
  subheadline: { type: String, default: '' },
  backgroundImage: { type: String, default: '' },
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
