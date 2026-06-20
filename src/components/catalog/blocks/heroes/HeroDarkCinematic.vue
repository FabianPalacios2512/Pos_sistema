<template>
  <!-- HERO DARK CINEMATIC — Luxury / Alta Costura / Tech Premium -->
  <!-- Full-bleed, overlay DRAMÁTICO, título BOLD serif, CTA blanco sólido prominente -->
  <section class="relative w-full overflow-hidden" :class="heightClass">

    <!-- Imagen de fondo full-bleed -->
    <div class="absolute inset-0">
      <img
        v-if="backgroundImage"
        :src="backgroundImage"
        alt=""
        class="w-full h-full object-cover object-center"
        loading="eager"
      />
      <div
        v-else
        class="w-full h-full"
        style="background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 40%, #111 100%)"
      ></div>
    </div>

    <!-- Overlay DRAMÁTICO -->
    <div
      class="absolute inset-0"
      style="background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.65) 40%, rgba(0,0,0,0.25) 75%, rgba(0,0,0,0.10) 100%)"
    ></div>

    <!-- Contenido: alineado abajo-izquierda -->
    <div class="relative z-10 h-full flex flex-col justify-end px-6 pb-10 md:px-14 md:pb-16 lg:px-20 lg:pb-24 xl:px-28">
      <div class="max-w-[1200px]">

        <!-- Micro etiqueta editorial -->
        <p
          class="text-white/45 text-[9px] lg:text-[11px] xl:text-[12px] uppercase tracking-[0.35em] mb-5 lg:mb-7 font-semibold"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >{{ label || 'Colección Exclusiva' }}</p>

        <!-- Título BOLD serif de alto impacto -->
        <h2
          class="text-white leading-[1.01] mb-6 lg:mb-9"
          :class="isMobilePreview ? 'text-[36px]' : 'text-[42px] md:text-[58px] lg:text-[72px] xl:text-[82px]'"
          :style="{
            fontFamily: fonts.heading + ', Georgia, \'Times New Roman\', serif',
            fontWeight: 700,
            letterSpacing: '-0.01em',
            textShadow: '0 4px 60px rgba(0,0,0,0.4)'
          }"
        >{{ headline }}</h2>

        <!-- Subtítulo — más visible -->
        <p
          v-if="subheadline"
          class="text-white/60 text-[12px] lg:text-[14px] uppercase tracking-[0.18em] mb-10 lg:mb-12 font-medium max-w-[360px] lg:max-w-[540px] leading-relaxed"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >{{ subheadline }}</p>
        <div v-else class="mb-10 lg:mb-12"></div>

        <!-- CTA: blanco sólido, texto negro, máximo contraste, BOLD -->
        <div class="flex items-center gap-4 lg:gap-6">
          <button
            @click="$emit('cta')"
            class="bg-white text-black text-[11px] lg:text-[13px] font-black uppercase tracking-widest px-8 lg:px-14 py-4 lg:py-5 hover:bg-gray-100 active:bg-gray-200 transition-colors duration-200"
            :style="{ fontFamily: fonts.body + ', sans-serif' }"
          >{{ ctaText || 'VER COLECCIÓN' }}</button>

          <!-- CTA secundario outline grueso -->
          <button
            v-if="ctaSecondaryText"
            @click="$emit('ctaSecondary')"
            class="border-2 border-white/30 text-white text-[11px] lg:text-[13px] uppercase tracking-widest font-bold px-8 lg:px-14 py-4 lg:py-5 hover:border-white/70 transition-colors duration-200"
            :style="{ fontFamily: fonts.body + ', sans-serif' }"
          >{{ ctaSecondaryText }}</button>
        </div>

      </div>
    </div>

    <slot name="indicators" />

    <!-- Desktop: Scroll hint -->
    <div class="hidden lg:flex absolute bottom-8 right-12 flex-col items-center gap-1.5 text-white/25 animate-pulse">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 5.25l-7.5 7.5-7.5-7.5m15 6l-7.5 7.5-7.5-7.5" />
      </svg>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  headline:          { type: String, default: 'Noir Collection' },
  subheadline:       { type: String, default: '' },
  label:             { type: String, default: '' },
  backgroundImage:   { type: String, default: '' },
  palette:           { type: Object, default: () => ({ primary: '#ffffff', text_dark: '#000000', background: '#000000' }) },
  fonts:             { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
  ctaText:           { type: String, default: '' },
  ctaSecondaryText:  { type: String, default: '' },
  isMobilePreview:   { type: Boolean, default: false },
  mood:              { type: String, default: 'noir' },
  textPosition:      { type: String, default: 'bottom-left' },
})

defineEmits(['cta', 'ctaSecondary'])

const heightClass = computed(() =>
  props.isMobilePreview
    ? 'h-[80vh] min-h-[460px]'
    : 'h-[80vh] min-h-[500px] md:h-screen md:min-h-[600px] lg:max-h-[950px]'
)
</script>
