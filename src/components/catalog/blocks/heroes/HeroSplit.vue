<template>
  <!-- HERO SPLIT — Minimalismo / Cosmética -->
  <!-- Pantalla dividida 50/50: color sólido izquierda + fotografía derecha -->
  <section
    class="relative w-full overflow-hidden"
    :class="heightClass"
  >
    <div class="grid grid-cols-1 md:grid-cols-2 h-full">

      <!-- LADO IZQUIERDO: Texto sobre color sólido -->
      <div
        class="flex flex-col justify-center px-8 md:px-12 lg:px-16 py-10 order-2 md:order-1"
        :style="{ backgroundColor: bgColor }"
      >
        <!-- Eyebrow label -->
        <p
          class="text-[9px] uppercase tracking-[0.3em] mb-5 font-medium"
          :style="{ color: labelColor, fontFamily: fonts.body + ', sans-serif' }"
        >{{ seasonLabel }}</p>

        <!-- Título enorme -->
        <h2
          class="leading-[0.96] mb-5"
          :class="isMobilePreview ? 'text-[36px]' : 'text-[40px] md:text-[52px] lg:text-[64px]'"
          :style="{ fontFamily: fonts.heading + ', Georgia, serif', fontWeight: 500, color: titleColor, letterSpacing: '-0.01em' }"
        >{{ headline }}</h2>

        <!-- Línea decorativa -->
        <div class="w-10 h-[1.5px] mb-5" :style="{ backgroundColor: titleColor + '55' }"></div>

        <!-- Subtítulo -->
        <p
          v-if="subheadline"
          class="text-sm leading-relaxed mb-8 max-w-[300px]"
          :style="{ color: titleColor + 'aa', fontFamily: fonts.body + ', sans-serif' }"
        >{{ subheadline }}</p>

        <!-- CTA Sólido rectangular -->
        <div>
          <button
            @click="$emit('cta')"
            class="text-[10px] uppercase tracking-[0.2em] px-8 py-3.5 transition-all duration-300 hover:opacity-80"
            :style="{ backgroundColor: palette.primary, color: '#ffffff', fontFamily: fonts.body + ', sans-serif' }"
          >{{ ctaText || 'COMPRAR AHORA' }}</button>
        </div>
      </div>

      <!-- LADO DERECHO: Fotografía perfecta, sin bordes redondeados -->
      <div class="relative overflow-hidden order-1 md:order-2" :class="isMobilePreview ? 'h-[56vw] min-h-[220px]' : 'h-auto'">
        <img
          v-if="backgroundImage"
          :src="backgroundImage"
          alt=""
          class="w-full h-full object-cover"
          loading="eager"
        />
        <div v-else class="w-full h-full" :style="{ backgroundColor: palette.secondary + '40' }"></div>
        <!-- Overlay muy sutil para integración de color -->
        <div class="absolute inset-0 bg-black/5"></div>
      </div>

    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  headline:        { type: String, default: 'Nueva Colección' },
  subheadline:     { type: String, default: '' },
  backgroundImage: { type: String, default: '' },
  palette:         { type: Object, default: () => ({ primary: '#10B981', secondary: '#d1d5db', text_dark: '#111827', background: '#ffffff' }) },
  fonts:           { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
  ctaText:         { type: String, default: '' },
  isMobilePreview: { type: Boolean, default: false },
  mood:            { type: String, default: 'luxury' },
})
defineEmits(['cta'])

import { computed } from 'vue'

const heightClass = computed(() =>
  props.isMobilePreview ? 'min-h-[420px]' : 'min-h-[400px] md:min-h-[520px] md:h-[70vh]'
)

// El lado izquierdo usa el color más claro/neutro de la paleta
const bgColor = computed(() => props.palette.background || '#f8f7f4')
const titleColor = computed(() => props.palette.text_dark || '#111827')
const labelColor = computed(() => props.palette.primary || '#111827')

const seasonLabel = computed(() => {
  const year = new Date().getFullYear()
  const month = new Date().getMonth()
  const season = month < 3 || month >= 9 ? 'Otoño — Invierno' : 'Primavera — Verano'
  return `${season} ${year}`
})
</script>
