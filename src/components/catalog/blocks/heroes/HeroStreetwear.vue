<template>
  <!-- HERO STREETWEAR — Urbano / Bold -->
  <!-- Imagen full con texto superpuesto desde abajo, tipografía de impacto, CTA sólido -->
  <section class="relative w-full overflow-hidden" :class="heightClass">

    <!-- Imagen de fondo full -->
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

    <!-- Overlay: gradiente de abajo hacia arriba, más agresivo en la zona del texto -->
    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.88) 0%, rgba(0,0,0,0.55) 45%, rgba(0,0,0,0.1) 75%, transparent 100%)"></div>

    <!-- Badge superior izquierda -->
    <div class="absolute top-4 left-4 z-10">
      <span
        class="text-[8px] uppercase tracking-[0.28em] font-bold px-3 py-1.5"
        :style="{ backgroundColor: palette.primary, color: '#ffffff', fontFamily: fonts.body + ', sans-serif' }"
      >DROP 01</span>
    </div>

    <!-- Contenido inferior: título + subheadline + CTA -->
    <div class="absolute inset-x-0 bottom-0 z-10 px-5 pb-7 md:px-10 md:pb-10">

      <!-- Eyebrow label -->
      <p
        class="text-white/50 text-[9px] uppercase tracking-[0.3em] mb-2 font-light"
        :style="{ fontFamily: fonts.body + ', sans-serif' }"
      >Temporada 2026</p>

      <!-- Título de alto impacto -->
      <h2
        class="text-white leading-[0.93] uppercase mb-4"
        :class="isMobilePreview ? 'text-[30px]' : 'text-[34px] md:text-[62px] lg:text-[78px]'"
        :style="{
          fontFamily: fonts.heading + ', Arial Black, Impact, sans-serif',
          fontWeight: 900,
          letterSpacing: '-0.02em'
        }"
      >{{ headline }}</h2>

      <!-- Fila: subtítulo izquierda + CTA derecha -->
      <div class="flex items-center justify-between gap-4">
        <p
          v-if="subheadline"
          class="text-white/60 text-[10px] uppercase tracking-[0.14em] leading-relaxed max-w-[200px]"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >{{ subheadline }}</p>
        <div v-else class="flex-1"></div>

        <!-- CTA rectangular sólido -->
        <button
          @click="$emit('cta')"
          class="flex-shrink-0 flex items-center gap-2.5 text-[10px] uppercase tracking-[0.2em] font-bold px-5 py-2.5 transition-all duration-200"
          :style="{
            backgroundColor: palette.primary,
            color: '#ffffff',
            fontFamily: fonts.body + ', sans-serif'
          }"
        >
          {{ ctaText || 'EXPLORAR' }}
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
          </svg>
        </button>
      </div>
    </div>

  </section>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  headline:        { type: String, default: 'New Season' },
  subheadline:     { type: String, default: '' },
  backgroundImage: { type: String, default: '' },
  palette:         { type: Object, default: () => ({ primary: '#111827', text_dark: '#111827', background: '#ffffff' }) },
  fonts:           { type: Object, default: () => ({ heading: 'Montserrat', body: 'Montserrat' }) },
  ctaText:         { type: String, default: '' },
  isMobilePreview: { type: Boolean, default: false },
})
defineEmits(['cta'])

const heightClass = computed(() =>
  props.isMobilePreview
    ? 'h-[65vh] min-h-[380px]'
    : 'h-[65vh] min-h-[420px] md:h-[80vh] md:min-h-[540px]'
)
</script>
