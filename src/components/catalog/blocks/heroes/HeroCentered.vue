<template>
  <!-- HERO CENTERED: Typography-driven, no image required -->
  <section
    class="relative w-full flex flex-col items-center justify-center text-center overflow-hidden"
    :style="{ minHeight: isMobilePreview ? '380px' : '480px', backgroundColor: palette.background || '#ffffff' }"
  >
    <!-- Decorative subtle gradient orb -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full opacity-[0.07] blur-[120px]"
        :style="{ backgroundColor: palette.primary || '#6366f1' }"
      ></div>
    </div>

    <!-- Background image (optional, with heavy overlay) -->
    <img
      v-if="backgroundImage"
      :src="backgroundImage"
      :alt="headline"
      class="absolute inset-0 w-full h-full object-cover opacity-10"
    />

    <!-- Content -->
    <div class="relative z-10 max-w-2xl mx-auto px-8">
      <!-- Badge -->
      <div
        v-if="badge"
        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full mb-6 text-[10px] font-bold uppercase tracking-[0.2em]"
        :style="{ backgroundColor: palette.primary + '12', color: palette.primary, border: '1px solid ' + palette.primary + '25' }"
      >
        <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: palette.primary }"></span>
        {{ badge }}
      </div>

      <!-- Headline -->
      <h2
        class="text-4xl md:text-6xl lg:text-7xl leading-[0.95] mb-5 tracking-tight"
        :style="{ fontFamily: fonts.heading + ', serif', fontWeight: 400, color: onBgText }"
      >{{ headline }}</h2>

      <!-- Subheadline -->
      <p
        v-if="subheadline"
        class="text-base md:text-lg leading-relaxed mb-8 mx-auto max-w-md"
        :style="{ fontFamily: fonts.body + ', sans-serif', color: onBgText, opacity: 0.55 }"
      >{{ subheadline }}</p>

      <!-- CTA -->
      <button
        v-if="ctaText"
        @click="$emit('cta')"
        class="px-10 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] border transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]"
        :style="{ borderColor: onBgText + '30', color: onBgText, backgroundColor: 'transparent' }"
        @mouseenter="e => { e.target.style.backgroundColor = palette.primary; e.target.style.color = '#ffffff'; e.target.style.borderColor = palette.primary }"
        @mouseleave="e => { e.target.style.backgroundColor = 'transparent'; e.target.style.color = onBgText; e.target.style.borderColor = onBgText + '30' }"
      >{{ ctaText }}</button>
    </div>

    <!-- Bottom decorative line -->
    <div
      class="absolute bottom-0 left-1/2 -translate-x-1/2 w-12 h-px"
      :style="{ backgroundColor: onBgText + '18' }"
    ></div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  headline: { type: String, default: 'Descubre tu estilo' },
  subheadline: { type: String, default: '' },
  backgroundImage: { type: String, default: '' },
  badge: { type: String, default: '' },
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
