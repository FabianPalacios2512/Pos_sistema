<template>
  <!-- HERO DARK CINEMATIC — Luxury / Alta Costura / Tech Premium -->
  <!-- h-screen full-bleed, overlay dramático en V, título masivo serif, CTA blanco sólido. -->
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
      <!-- Placeholder noir si no hay imagen -->
      <div
        v-else
        class="w-full h-full"
        style="background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 40%, #111 100%)"
      ></div>
    </div>

    <!-- Overlay dramático: oscuro abajo, más claro arriba, fondo que 'respira' -->
    <div
      class="absolute inset-0"
      style="background: linear-gradient(to top, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.60) 40%, rgba(0,0,0,0.22) 75%, rgba(0,0,0,0.10) 100%)"
    ></div>

    <!-- Contenido: alineado abajo-izquierda -->
    <div class="relative z-10 h-full flex flex-col justify-end px-6 pb-10 md:px-14 md:pb-16">

      <!-- Micro etiqueta editorial — casi invisible, high class -->
      <p
        class="text-white/40 text-[9px] uppercase tracking-[0.38em] mb-5 font-light"
        :style="{ fontFamily: fonts.body + ', sans-serif' }"
      >{{ label || 'Colección Exclusiva' }}</p>

      <!-- Título masivo — serif de alto impacto -->
      <h2
        class="text-white leading-[1.01] mb-6"
        :class="isMobilePreview ? 'text-[36px]' : 'text-[42px] md:text-[68px] lg:text-[82px]'"
        :style="{
          fontFamily: fonts.heading + ', Georgia, \'Times New Roman\', serif',
          fontWeight: 400,
          letterSpacing: '-0.01em',
        }"
      >{{ headline }}</h2>

      <!-- Subtítulo — elegante, casi evanescente -->
      <p
        v-if="subheadline"
        class="text-white/50 text-[11px] uppercase tracking-[0.2em] mb-9 font-light max-w-[340px] leading-relaxed"
        :style="{ fontFamily: fonts.body + ', sans-serif' }"
      >{{ subheadline }}</p>
      <div v-else class="mb-9"></div>

      <!-- CTA: blanco sólido, texto negro, máximo contraste -->
      <div class="flex items-center gap-4">
        <button
          @click="$emit('cta')"
          class="bg-white text-black text-[10px] font-bold uppercase tracking-widest px-8 py-3.5 hover:bg-gray-100 active:bg-gray-200 transition-colors duration-200"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >{{ ctaText || 'VER COLECCIÓN' }}</button>

        <!-- CTA secundario outline, solo si hay texto -->
        <button
          v-if="ctaSecondaryText"
          @click="$emit('ctaSecondary')"
          class="border border-white/25 text-white text-[10px] uppercase tracking-widest px-8 py-3.5 hover:border-white/60 transition-colors duration-200"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >{{ ctaSecondaryText }}</button>
      </div>

    </div>

    <!-- Indicadores de slide (slot para carruseles) -->
    <slot name="indicators" />
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
    : 'h-[80vh] min-h-[500px] md:h-screen md:min-h-[600px]'
)
</script>
