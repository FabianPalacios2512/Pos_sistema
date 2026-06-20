<template>
  <!-- HOOK: DYNAMIC BENTO — Deportivo / Tecnología / Consumo -->
  <!-- Mobile: stack vertical. Desktop: bento horizontal (imagen izquierda | columna derecha). -->
  <section class="w-full px-4 py-5 lg:py-8" :style="{ backgroundColor: palette.background || '#f9fafb' }">

    <!-- Bento: flex-col en mobile, flex-row en desktop -->
    <div class="flex flex-col lg:flex-row gap-2 lg:gap-3">

      <!-- CELDA PRINCIPAL: Imagen lifestyle -->
      <!-- Mobile: aspect 16/8 (landscape). Desktop: portrait, toma el lado izquierdo -->
      <div class="relative overflow-hidden rounded-lg min-h-[180px] aspect-[16/8] lg:aspect-auto lg:w-[57%] lg:min-h-[480px]">
        <img
          v-if="image"
          :src="image"
          :alt="headline"
          class="absolute inset-0 w-full h-full object-cover object-center"
        />
        <div
          v-else
          class="absolute inset-0"
          :style="{ backgroundColor: palette.primary + '22' }"
        ></div>

        <!-- Overlay + título -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/65 via-black/10 to-transparent flex items-end p-4 lg:p-8">
          <div>
            <p class="text-[8px] uppercase tracking-[0.4em] font-bold text-white/50 mb-1">{{ label }}</p>
            <h2
              class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-black uppercase tracking-tighter text-white leading-[0.95] whitespace-pre-line"
              :style="{ fontFamily: fonts.heading + ', Impact, sans-serif' }"
            >{{ headline }}</h2>
          </div>
        </div>
      </div>

      <!-- COLUMNA DERECHA: celdas + CTA (apiladas en desktop) -->
      <div class="flex flex-col gap-2 lg:gap-3 lg:flex-1">

        <!-- Celdas: 50/50 en mobile → stack vertical en desktop -->
        <div class="grid grid-cols-2 lg:grid-cols-1 gap-2 lg:gap-3 lg:flex-1">

          <!-- CELDA A: Color sólido + claim -->
          <div
            class="rounded-lg flex flex-col items-start justify-end px-4 py-4 sm:py-5 lg:py-7"
            style="min-height: 130px;"
            :style="{ backgroundColor: palette.primary }"
          >
            <p class="text-[8px] uppercase tracking-[0.3em] font-bold mb-1.5" :style="{ color: primaryContrastMuted }">Tecnología</p>
            <p
              class="text-[16px] sm:text-[18px] lg:text-[22px] font-black uppercase leading-tight whitespace-pre-line"
              :style="{ color: primaryContrastText, fontFamily: fonts.heading + ', sans-serif' }"
            >{{ benefit }}</p>
          </div>

          <!-- CELDA B: Foto de detalle -->
          <div class="relative overflow-hidden rounded-lg" style="min-height: 130px;">
            <img
              v-if="detailImage"
              :src="detailImage"
              :alt="benefit"
              class="absolute inset-0 w-full h-full object-cover object-center"
            />
            <div v-else class="absolute inset-0" :style="{ backgroundColor: palette.secondary + '50' }"></div>
            <div
              class="absolute top-2 left-2 px-2 py-[3px] rounded text-[7.5px] font-bold uppercase tracking-wider"
              :style="{ backgroundColor: badgeBgColor, color: badgeTextColor }"
            >Detalle</div>
          </div>

        </div>

        <!-- CTA Row (ahora dentro de la columna derecha) -->
        <div class="flex items-center justify-between gap-3 lg:mt-1">
          <p class="text-[12px] font-medium" style="color: #9ca3af;">{{ subheadline }}</p>
          <button
            @click="$emit('cta')"
            class="flex-shrink-0 px-5 py-2.5 rounded-md text-[10px] font-black uppercase tracking-widest transition-opacity duration-150 hover:opacity-80 active:scale-95"
            :style="{ backgroundColor: ctaBgColor, color: ctaTextColor, fontFamily: fonts.body + ', sans-serif' }"
          >{{ ctaText }}</button>
        </div>

      </div>

    </div>

  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  headline:    { type: String, default: 'Rendimiento\nSin Límites' },
  subheadline: { type: String, default: 'Colección de alto desempeño' },
  benefit:     { type: String, default: 'DRY-FIT\nPRO' },
  label:       { type: String, default: 'Colección 2026' },
  ctaText:     { type: String, default: 'Ver Colección' },
  image:       { type: String, default: '' },
  detailImage: { type: String, default: '' },
  palette:     { type: Object, default: () => ({ primary: '#3b82f6', background: '#f9fafb', text_dark: '#111827', text_light: '#ffffff', secondary: '#e5e7eb' }) },
  fonts:       { type: Object, default: () => ({ heading: 'Montserrat', body: 'Montserrat' }) },
})

defineEmits(['cta'])

// Helper W3C: retorna true si el color es claro (luminancia > 55%)
function isLightColor(hex) {
  if (!hex || !hex.startsWith('#')) return false
  const c = hex.replace('#', '')
  if (c.length < 6) return false
  const r = parseInt(c.substring(0, 2), 16)
  const g = parseInt(c.substring(2, 4), 16)
  const b = parseInt(c.substring(4, 6), 16)
  return (0.299 * r + 0.587 * g + 0.114 * b) / 255 > 0.55
}

// Texto que contrasta sobre palette.primary (celda sólida del bento)
const primaryContrastText = computed(() =>
  isLightColor(props.palette.primary) ? '#111111' : '#ffffff'
)

// Versión semitransparente del mismo color para el micro-label "Tecnología"
const primaryContrastMuted = computed(() =>
  isLightColor(props.palette.primary) ? 'rgba(0,0,0,0.5)' : 'rgba(255,255,255,0.6)'
)

// Fondo oscuro = luminancia < 50%
const isBgDark = computed(() => !isLightColor(props.palette.background || '#f9fafb'))

// Badge "Detalle" — visible en ambos modos
const badgeBgColor = computed(() =>
  isBgDark.value ? (props.palette.text_light || '#ffffff') : (props.palette.background || '#ffffff')
)
const badgeTextColor = computed(() =>
  isBgDark.value ? '#111111' : (props.palette.text_dark || '#111827')
)

// Botón CTA — en modo oscuro usa primary (visible), en modo claro usa text_dark (clásico)
const ctaBgColor = computed(() =>
  isBgDark.value ? (props.palette.primary || '#3b82f6') : (props.palette.text_dark || '#111827')
)
const ctaTextColor = computed(() =>
  isBgDark.value ? primaryContrastText.value : (props.palette.text_light || '#ffffff')
)
</script>
