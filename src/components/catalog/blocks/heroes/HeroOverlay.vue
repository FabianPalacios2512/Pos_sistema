<template>
  <!-- HERO OVERLAY: Dense gradient, centered text, dramatic feel -->
  <section
    class="relative w-full flex items-center justify-center overflow-hidden"
    :style="{ height: isMobilePreview ? '400px' : '520px' }"
  >
    <!-- Background image -->
    <img
      v-if="backgroundImage"
      :src="backgroundImage"
      :alt="headline"
      class="absolute inset-0 w-full h-full object-cover"
    />
    <div
      v-else
      class="absolute inset-0"
      :style="{ background: `linear-gradient(135deg, ${palette.primary || '#1a1a1a'}, ${palette.accent || '#333333'})` }"
    ></div>

    <!-- Dense overlay -->
    <div class="absolute inset-0" :style="{ background: `linear-gradient(to bottom, ${overlayColor}CC, ${overlayColor}E6)` }"></div>

    <!-- Animated subtle grain texture -->
    <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%270 0 256 256%27 xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cfilter id=%27noise%27%3E%3CfeTurbulence type=%27fractalNoise%27 baseFrequency=%270.9%27 numOctaves=%274%27 stitchTiles=%27stitch%27/%3E%3C/filter%3E%3Crect width=%27100%25%27 height=%27100%25%27 filter=%27url(%23noise)%27/%3E%3C/svg%3E');"></div>

    <!-- Content -->
    <div class="relative z-10 text-center px-8 max-w-xl">
      <!-- Decorative top element -->
      <div class="flex items-center justify-center gap-3 mb-6">
        <div class="w-8 h-px" :style="{ backgroundColor: textColor + '40' }"></div>
        <span
          class="text-[10px] uppercase tracking-[0.3em] font-semibold"
          :style="{ color: textColor + '70' }"
        >{{ badge || 'Colección' }}</span>
        <div class="w-8 h-px" :style="{ backgroundColor: textColor + '40' }"></div>
      </div>

      <!-- Headline -->
      <h2
        class="text-3xl md:text-5xl lg:text-6xl leading-[1.02] mb-4 tracking-tight"
        :style="{ fontFamily: fonts.heading + ', serif', fontWeight: 400, color: textColor }"
      >{{ headline }}</h2>

      <!-- Subheadline -->
      <p
        v-if="subheadline"
        class="text-sm md:text-base leading-relaxed mb-8 mx-auto max-w-sm"
        :style="{ fontFamily: fonts.body + ', sans-serif', color: textColor, opacity: 0.65 }"
      >{{ subheadline }}</p>

      <!-- CTA -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <button
          v-if="ctaText"
          @click="$emit('cta')"
          class="px-8 py-3.5 text-[11px] font-bold uppercase tracking-[0.18em] transition-all duration-300 hover:scale-[1.02]"
          :style="{ backgroundColor: textColor, color: overlayColor }"
        >{{ ctaText }}</button>
        <button
          v-if="ctaSecondary"
          @click="$emit('ctaSecondary')"
          class="px-8 py-3.5 text-[11px] font-bold uppercase tracking-[0.18em] border transition-all duration-300 hover:scale-[1.02]"
          :style="{ borderColor: textColor + '40', color: textColor, backgroundColor: 'transparent' }"
        >{{ ctaSecondary }}</button>
      </div>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10">
      <div class="w-px h-8 mx-auto animate-pulse" :style="{ backgroundColor: textColor + '30' }"></div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  headline: { type: String, default: '' },
  subheadline: { type: String, default: '' },
  backgroundImage: { type: String, default: '' },
  badge: { type: String, default: '' },
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Inter' }) },
  ctaText: { type: String, default: '' },
  ctaSecondary: { type: String, default: '' },
  isMobilePreview: { type: Boolean, default: false }
})

defineEmits(['cta', 'ctaSecondary'])

const isBackgroundDark = computed(() => {
  const bg = props.palette.background || '#ffffff'
  const hex = bg.replace('#', '')
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16)
  const b = parseInt(hex.substr(4, 2), 16)
  return (r * 299 + g * 587 + b * 114) / 1000 < 128
})

const overlayColor = computed(() => isBackgroundDark.value ? '#000000' : (props.palette.primary || '#1a1a1a'))
const textColor = computed(() => '#ffffff')
</script>
