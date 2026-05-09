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

  <!-- Trust Strip — integrada visualmente justo debajo del hero -->
  <div
    v-if="trustMessages && trustMessages.length"
    class="w-full border-b"
    :style="{ backgroundColor: palette.background || '#ffffff', borderColor: palette.text_dark + '14' }"
  >
    <div class="flex items-center justify-around px-4 py-3">
      <div
        v-for="(msg, i) in trustMessages.slice(0, 3)"
        :key="i"
        class="flex items-center gap-2"
      >
        <!-- Iconos: envío / seguridad / soporte -->
        <svg v-if="i === 0" class="w-4 h-4 flex-shrink-0" :style="{ color: palette.primary }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
        </svg>
        <svg v-else-if="i === 1" class="w-4 h-4 flex-shrink-0" :style="{ color: palette.primary }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
        </svg>
        <svg v-else class="w-4 h-4 flex-shrink-0" :style="{ color: palette.primary }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
        </svg>
        <span
          class="text-[10px] font-bold uppercase tracking-[0.1em] whitespace-nowrap"
          :style="{ color: palette.text_dark || '#111827', fontFamily: fonts.body + ', sans-serif' }"
        >{{ shortMessages[i] }}</span>
      </div>
    </div>
  </div>
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
