<template>
  <!-- HERO PORTRAIT — Fotografía tipo retrato, tipografía mixta, doble CTA rectangular -->
  <!-- Inspirado en: belleza premium, distribuidoras, marcas de cabello/cosméticos con impacto visual fuerte -->
  <section class="relative w-full overflow-hidden" :class="heightClass">

    <!-- Imagen de fondo full (ideal: fotos verticales, retratos, close-up) -->
    <div class="absolute inset-0">
      <img
        v-if="backgroundImage"
        :src="backgroundImage"
        alt=""
        class="w-full h-full object-cover object-top"
        loading="eager"
      />
      <div v-else class="w-full h-full bg-zinc-900"></div>
    </div>

    <!-- Overlay: gradiente fuerte en zona inferior para máxima legibilidad del texto -->
    <div
      class="absolute inset-0"
      style="background: linear-gradient(to top, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.70) 30%, rgba(0,0,0,0.30) 55%, transparent 100%)"
    ></div>

    <!-- Contenido inferior izquierda -->
    <div class="absolute inset-x-0 bottom-0 z-10 px-5 pb-6 md:px-10 md:pb-10">

      <!-- Titular con tipografía mixta: sans bold + serif italic -->
      <h2 class="text-white mb-5 leading-none" :class="isMobilePreview ? 'text-[30px]' : 'text-[34px] md:text-[54px]'">
        <!-- Líneas del headline: las primeras en sans bold, la última en serif italic -->
        <span
          v-for="(line, i) in headlineLines"
          :key="i"
          class="block leading-tight"
        >
          <!-- Última línea: serif italic para contraste tipográfico de revista -->
          <em
            v-if="i === headlineLines.length - 1 && headlineLines.length > 1"
            class="not-italic font-normal"
            :style="{ fontFamily: fonts.heading + ', Georgia, serif', fontStyle: 'italic', fontWeight: 400 }"
          >{{ line }}</em>
          <!-- Resto: sans bold agresivo -->
          <strong
            v-else
            class="font-black not-italic"
            :style="{ fontFamily: fonts.body + ', Arial Black, sans-serif', fontWeight: 900 }"
          >{{ line }}</strong>
        </span>
      </h2>

      <!-- Doble CTA rectangular — blanco sólido + ghost outline -->
      <div class="flex items-stretch gap-3">
        <!-- Botón primario: blanco sólido, texto negro -->
        <button
          @click="$emit('cta')"
          class="flex-1 text-xs uppercase tracking-widest font-bold px-4 py-3.5 bg-white text-black hover:bg-gray-100 transition-colors duration-200 text-center"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >{{ ctaText || 'VER CATÁLOGO' }}</button>

        <!-- Botón secundario: ghost/outline — transparente con borde blanco -->
        <button
          v-if="ctaSecondary"
          @click="$emit('ctaSecondary')"
          class="flex-1 text-xs uppercase tracking-widest font-bold px-4 py-3.5 bg-transparent border border-white text-white hover:bg-white hover:text-black transition-colors duration-200 text-center"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >{{ ctaSecondary }}</button>
      </div>

      <!-- Indicadores de slide (slot para soporte futuro de carrusel) -->
      <div v-if="showDots" class="flex justify-center gap-2 mt-4">
        <span
          v-for="n in 4" :key="n"
          class="transition-all duration-300"
          :class="n === 1 ? 'w-6 h-1.5 rounded-full bg-white' : 'w-1.5 h-1.5 rounded-full bg-white/40'"
        ></span>
      </div>
    </div>

  </section>

</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  headline:        { type: String, default: 'Cabello 100% Natural\nPremium' },
  subheadline:     { type: String, default: '' },
  backgroundImage: { type: String, default: '' },
  palette:         { type: Object, default: () => ({ primary: '#e91e8c', text_dark: '#111827', background: '#ffffff', text_light: '#f9fafb' }) },
  fonts:           { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
  ctaText:         { type: String, default: 'VER CATÁLOGO' },
  ctaSecondary:    { type: String, default: 'MAYORISTA' },
  trustMessages:   { type: Array, default: () => ['Envío Gratis', 'Pago Seguro', 'Asesoría VIP'] },
  showDots:        { type: Boolean, default: true },
  isMobilePreview: { type: Boolean, default: false },
})

defineEmits(['cta', 'ctaSecondary'])

// Divide el headline por saltos de línea o por la última palabra si es corto
const headlineLines = computed(() => {
  if (props.headline.includes('\n')) {
    return props.headline.split('\n').filter(Boolean)
  }
  // Auto-split: si tiene 3+ palabras, la última va en serif italic
  const words = props.headline.split(' ')
  if (words.length >= 3) {
    const last = words.pop()
    return [words.join(' '), last]
  }
  return [props.headline]
})

const heightClass = computed(() =>
  props.isMobilePreview
    ? 'h-[92vh] min-h-[580px]'
    : 'h-[92vh] min-h-[600px] md:h-[96vh] md:min-h-[700px]'
)

// Extrae etiqueta corta de cada trust message (máx 3 palabras, máx 18 chars)
const shortMessages = computed(() =>
  props.trustMessages.slice(0, 3).map(msg => {
    const words = String(msg).trim().split(/\s+/)
    // Intentar tomar palabras clave: ignorar artículos/preposiciones al inicio
    const skip = new Set(['ofrecemos', 'nuestro', 'nuestra', 'la', 'el', 'de', 'un', 'una', 'los', 'las'])
    const filtered = words.filter(w => !skip.has(w.toLowerCase()))
    const label = (filtered.length >= 2 ? filtered : words).slice(0, 3).join(' ')
    return label.length > 18 ? label.substring(0, 18) : label
  })
)
</script>
