<template>
  <!-- HERO EDITORIAL — Alta Moda / Boutique -->
  <!-- Full-bleed fotografía, overlay dramático, tipografía bold serif, botón outline blanco -->
  <!-- DESKTOP: Título impactante, contenido con presencia, CTA prominente -->
  <section
    class="relative w-full overflow-hidden"
    :class="heightClass"
  >
    <!-- Imagen de fondo -->
    <div class="absolute inset-0">
      <img
        v-if="backgroundImage"
        :src="backgroundImage"
        alt=""
        class="w-full h-full object-cover"
        loading="eager"
      />
      <div v-else class="w-full h-full" :style="{ backgroundColor: palette.text_dark }"></div>
    </div>

    <!-- Overlay dramático — más oscuro para contraste fuerte -->
    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.50) 35%, rgba(0,0,0,0.25) 60%, rgba(0,0,0,0.10) 100%)"></div>

    <!-- Contenido -->
    <div
      class="relative z-10 h-full flex flex-col justify-end px-6 pb-10 md:px-12 md:pb-14 lg:px-20 lg:pb-20 xl:px-28"
      :class="textAlign"
    >
      <div class="max-w-[1200px]" :class="textAlign.includes('items-center') ? 'mx-auto' : ''">
        <!-- Label editorial -->
        <p
          class="text-white/50 text-[9px] lg:text-[11px] xl:text-[12px] uppercase tracking-[0.3em] mb-4 lg:mb-6 font-medium"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >Nueva Colección</p>

        <!-- Título principal — BOLD SERIF, alto impacto -->
        <h2
          class="text-white leading-[1.02] mb-5 lg:mb-7"
          :class="isMobilePreview ? 'text-[32px]' : 'text-[38px] md:text-[52px] lg:text-[66px] xl:text-[76px]'"
          :style="{
            fontFamily: fonts.heading + ', Georgia, serif',
            fontWeight: 700,
            letterSpacing: mood === 'luxury' ? '0.02em' : '-0.01em',
            textShadow: '0 2px 40px rgba(0,0,0,0.3)'
          }"
        >{{ headline }}</h2>

        <!-- Subtítulo — más visible -->
        <p
          v-if="subheadline"
          class="text-white/80 text-[12px] md:text-[14px] lg:text-[16px] uppercase tracking-[0.14em] mb-8 lg:mb-10 font-medium max-w-[360px] lg:max-w-[520px] leading-relaxed"
          :class="{ 'mx-auto': textAlign.includes('items-center') }"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >{{ subheadline }}</p>

        <!-- CTA — GRANDE, BOLD, visible -->
        <div :class="textAlign.includes('items-center') ? 'flex justify-center' : 'flex'">
          <button
            @click="$emit('cta')"
            class="border-2 border-white text-white text-[11px] lg:text-[13px] uppercase tracking-[0.2em] font-bold px-8 lg:px-14 py-3.5 lg:py-5 transition-all duration-400 hover:bg-white hover:text-black"
            :style="{ fontFamily: fonts.body + ', sans-serif' }"
          >{{ ctaText || 'VER COLECCIÓN' }}</button>
        </div>
      </div>
    </div>

    <!-- Indicadores de slide -->
    <slot name="indicators" />

    <!-- Desktop: Scroll indicator sutil -->
    <div class="hidden lg:flex absolute bottom-8 left-1/2 -translate-x-1/2 flex-col items-center gap-2 animate-bounce-slow">
      <span class="text-white/30 text-[9px] uppercase tracking-[0.3em] font-medium">Scroll</span>
      <svg class="w-4 h-4 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
      </svg>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  headline:        { type: String, default: 'Nueva Colección' },
  subheadline:     { type: String, default: '' },
  backgroundImage: { type: String, default: '' },
  palette:         { type: Object, default: () => ({ primary: '#10B981', text_dark: '#111827', background: '#ffffff' }) },
  fonts:           { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
  ctaText:         { type: String, default: '' },
  isMobilePreview: { type: Boolean, default: false },
  mood:            { type: String, default: 'luxury' },
  textPosition:    { type: String, default: 'bottom-left' },
})
defineEmits(['cta'])

import { computed } from 'vue'

const heightClass = computed(() =>
  props.isMobilePreview ? 'h-[65vh] min-h-[360px]' : 'h-[65vh] min-h-[400px] md:h-[80vh] md:min-h-[520px] lg:h-[85vh] lg:max-h-[850px]'
)

const textAlign = computed(() => {
  if (props.textPosition === 'center')        return 'items-center text-center'
  if (props.textPosition === 'bottom-center') return 'items-center text-center'
  return 'items-start text-left'
})
</script>

<style scoped>
@keyframes bounce-slow {
  0%, 100% { transform: translateY(0) translateX(-50%); }
  50% { transform: translateY(6px) translateX(-50%); }
}
.animate-bounce-slow {
  animation: bounce-slow 2.5s infinite ease-in-out;
}
</style>
