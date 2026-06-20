<template>
  <!-- HOOK: EDITORIAL STORY — Boutique / Lencería / Alta Costura -->
  <!-- Mobile: texto arriba, imagen abajo. Desktop: texto izquierda, imagen derecha full-height. -->
  <section class="w-full" :style="{ backgroundColor: palette.background || '#ffffff' }">
    <div class="flex flex-col lg:flex-row-reverse lg:min-h-[560px]">

      <!-- TEXTO: Arriba en móvil, izquierda en desktop -->
      <div class="flex flex-col justify-center px-6 py-10 lg:w-[44%] lg:px-16 xl:px-20 lg:py-24 order-1">

        <!-- Etiqueta superior — micro label -->
        <p
          class="text-[9px] uppercase tracking-[0.38em] font-bold mb-5"
          :style="{ color: palette.primary }"
        >{{ label }}</p>

        <!-- Título serif grande, saltos de línea respetados -->
        <h2
          class="text-[30px] lg:text-[42px] xl:text-[56px] leading-[1.06] font-semibold mb-5 whitespace-pre-line"
          :style="{ fontFamily: fonts.heading + ', Georgia, \'Times New Roman\', serif', color: adaptiveTextColor }"
        >{{ headline }}</h2>

        <!-- Párrafo — color adaptativo al fondo -->
        <p
          class="text-[13.5px] lg:text-[15px] leading-relaxed max-w-[320px] lg:max-w-[360px] mb-9"
          :style="{ color: adaptiveBodyColor }"
        >{{ body }}</p>

        <!-- CTA minimalista: texto + línea inferior + flecha -->
        <a
          href="#"
          @click.prevent="$emit('cta')"
          class="inline-flex items-center gap-2 self-start group"
        >
          <span
            class="text-[10px] uppercase tracking-[0.28em] font-bold border-b pb-[3px] transition-opacity duration-200 group-hover:opacity-50"
            :style="{ color: adaptiveTextColor, borderColor: adaptiveTextColor }"
          >{{ ctaText }}</span>
          <svg
            class="w-3 h-3 transition-transform duration-200 group-hover:translate-x-1"
            :style="{ color: adaptiveTextColor }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
          </svg>
        </a>

      </div>

      <!-- IMAGEN: Abajo en móvil, derecha en desktop. Corte recto, sin bordes. -->
      <div class="relative overflow-hidden min-h-[300px] lg:flex-1 order-2">
        <img
          v-if="image"
          :src="image"
          :alt="headline"
          class="absolute inset-0 w-full h-full object-cover object-center"
        />
        <!-- Placeholder editorial cuando no hay imagen -->
        <div
          v-else
          class="absolute inset-0 flex items-center justify-center"
          :style="{ backgroundColor: palette.primary + '0f' }"
        >
          <svg class="w-16 h-16 opacity-15" :style="{ color: palette.primary }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.7"
              d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
          </svg>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  headline: { type: String, default: 'Diseñada para\nsentirte única' },
  body:     { type: String, default: 'Cada pieza es pensada con materiales de primera calidad. Tu comodidad y estilo son nuestra obsesión desde el primer boceto.' },
  label:    { type: String, default: 'Brand Story' },
  ctaText:  { type: String, default: 'Ver Colección' },
  image:    { type: String, default: '' },
  palette:  { type: Object, default: () => ({ primary: '#111827', background: '#ffffff', text_dark: '#111827', secondary: '#d1d5db', text_light: '#f9fafb' }) },
  fonts:    { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
})

defineEmits(['cta'])

// Detecta si el fondo es oscuro (luminancia percibida < 50%)
const isBackgroundDark = computed(() => {
  const hex = (props.palette.background || '#ffffff').replace('#', '')
  if (hex.length !== 6) return false
  const r = parseInt(hex.substring(0, 2), 16)
  const g = parseInt(hex.substring(2, 4), 16)
  const b = parseInt(hex.substring(4, 6), 16)
  return (0.299 * r + 0.587 * g + 0.114 * b) / 255 < 0.5
})

// Título y CTA: blanco sobre oscuro, oscuro sobre claro
const adaptiveTextColor = computed(() =>
  isBackgroundDark.value
    ? (props.palette.text_light || '#ffffff')
    : (props.palette.text_dark  || '#111827')
)

// Párrafo: versión más suave del texto adaptativo
const adaptiveBodyColor = computed(() =>
  isBackgroundDark.value
    ? 'rgba(255,255,255,0.55)'
    : '#6b7280'
)
</script>
