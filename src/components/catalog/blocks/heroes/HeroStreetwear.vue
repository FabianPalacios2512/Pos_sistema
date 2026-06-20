<template>
  <!-- HERO STREETWEAR — Urbano / BOLD / Agresivo -->
  <!-- Imagen full con texto BOLD superpuesto, tipografía de máximo impacto, CTA sólido -->
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

    <!-- Overlay: gradiente agresivo -->
    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.60) 45%, rgba(0,0,0,0.12) 75%, transparent 100%)"></div>

    <!-- Badge superior izquierda — PROMINENTE -->
    <div class="absolute top-4 left-4 lg:top-8 lg:left-10 z-10">
      <span
        class="text-[9px] lg:text-[12px] uppercase tracking-[0.25em] font-black px-3.5 lg:px-5 py-2 lg:py-2.5"
        :style="{ backgroundColor: palette.primary, color: '#ffffff', fontFamily: fonts.body + ', sans-serif' }"
      >DROP 01</span>
    </div>

    <!-- Contenido inferior: título + subheadline + CTA -->
    <div class="absolute inset-x-0 bottom-0 z-10 px-5 pb-7 md:px-10 md:pb-10 lg:px-16 lg:pb-16 xl:px-24 xl:pb-20">
      <div class="max-w-[1200px]">

        <!-- Eyebrow label -->
        <p
          class="text-white/55 text-[10px] lg:text-[12px] uppercase tracking-[0.25em] mb-3 lg:mb-5 font-semibold"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >Temporada 2026</p>

        <!-- Título de MÁXIMO impacto — BLACK weight -->
        <h2
          class="text-white leading-[0.91] uppercase mb-5 lg:mb-9"
          :class="isMobilePreview ? 'text-[30px]' : 'text-[36px] md:text-[54px] lg:text-[72px] xl:text-[82px]'"
          :style="{
            fontFamily: fonts.heading + ', Arial Black, Impact, sans-serif',
            fontWeight: 900,
            letterSpacing: '-0.02em',
            textShadow: '0 2px 40px rgba(0,0,0,0.5)'
          }"
        >{{ headline }}</h2>

        <!-- Fila: subtítulo izquierda + CTA derecha -->
        <div class="flex items-center justify-between gap-4 lg:gap-8">
          <p
            v-if="subheadline"
            class="text-white/65 text-[11px] lg:text-[14px] uppercase tracking-[0.12em] leading-relaxed max-w-[200px] lg:max-w-[380px] font-medium"
            :style="{ fontFamily: fonts.body + ', sans-serif' }"
          >{{ subheadline }}</p>
          <div v-else class="flex-1"></div>

          <!-- CTA rectangular sólido BOLD -->
          <button
            @click="$emit('cta')"
            class="flex-shrink-0 flex items-center gap-3 lg:gap-4 text-[11px] lg:text-[13px] uppercase tracking-[0.18em] font-black px-6 lg:px-10 py-3.5 lg:py-5 transition-all duration-200"
            :style="{
              backgroundColor: palette.primary,
              color: '#ffffff',
              fontFamily: fonts.body + ', sans-serif'
            }"
          >
            {{ ctaText || 'EXPLORAR' }}
            <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
            </svg>
          </button>
        </div>
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
    : 'h-[65vh] min-h-[420px] md:h-[80vh] md:min-h-[540px] lg:h-[88vh] lg:max-h-[900px]'
)
</script>
