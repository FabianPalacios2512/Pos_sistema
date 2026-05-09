<template>
  <!-- HERO EDITORIAL — Alta Moda / Boutique -->
  <!-- Full-bleed fotografía, overlay sutil, tipografía serif, botón outline blanco -->
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

    <!-- Overlay oscuro sutil -->
    <div class="absolute inset-0 bg-black/30"></div>

    <!-- Contenido -->
    <div
      class="relative z-10 h-full flex flex-col justify-end px-6 pb-10 md:px-14 md:pb-14"
      :class="textAlign"
    >
      <!-- Label editorial -->
      <p
        class="text-white/60 text-[9px] uppercase tracking-[0.3em] mb-4 font-light"
        :style="{ fontFamily: fonts.body + ', sans-serif' }"
      >Nueva Colección</p>

      <!-- Título principal -->
      <h2
        class="text-white leading-[1.04] mb-4"
        :class="isMobilePreview ? 'text-[32px]' : 'text-[38px] md:text-[58px] lg:text-[68px]'"
        :style="{ fontFamily: fonts.heading + ', Georgia, serif', fontWeight: 400, letterSpacing: mood === 'luxury' ? '0.04em' : '0.01em' }"
      >{{ headline }}</h2>

      <!-- Subtítulo -->
      <p
        v-if="subheadline"
        class="text-white/75 text-[11px] md:text-sm uppercase tracking-[0.18em] mb-7 font-light max-w-[340px]"
        :class="{ 'mx-auto': textAlign.includes('items-center') }"
        :style="{ fontFamily: fonts.body + ', sans-serif' }"
      >{{ subheadline }}</p>

      <!-- CTA Outline Blanco -->
      <div :class="textAlign.includes('items-center') ? 'flex justify-center' : 'flex'">
        <button
          @click="$emit('cta')"
          class="border border-white text-white text-[10px] uppercase tracking-[0.22em] px-8 py-3 transition-all duration-400 hover:bg-white hover:text-black"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >{{ ctaText || 'VER COLECCIÓN' }}</button>
      </div>
    </div>

    <!-- Indicadores de slide (si hay múltiples imágenes) -->
    <slot name="indicators" />
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
  props.isMobilePreview ? 'h-[65vh] min-h-[360px]' : 'h-[65vh] min-h-[400px] md:h-[80vh] md:min-h-[520px]'
)

const textAlign = computed(() => {
  if (props.textPosition === 'center')        return 'items-center text-center'
  if (props.textPosition === 'bottom-center') return 'items-center text-center'
  return 'items-start text-left'
})
</script>
