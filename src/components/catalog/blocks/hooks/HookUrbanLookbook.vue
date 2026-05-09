<template>
  <!-- HOOK: URBAN LOOKBOOK — Streetwear / Moda Juvenil / Sneakers -->
  <!-- Full-bleed imagen con texto superpuesto + botón sólido de conversión. Cero sutilezas. -->
  <section class="w-full overflow-hidden" :style="{ backgroundColor: '#111111' }">

    <!-- Imagen full-bleed con overlay y texto superpuesto -->
    <div class="relative">
      <!-- Imagen: portrait en móvil, cinematic en desktop -->
      <div class="aspect-[3/4] sm:aspect-[16/8] overflow-hidden">
        <!-- Video cuando el medio activo es video -->
        <video
          v-if="activeMedia.type === 'video'"
          :src="activeMedia.src"
          autoplay
          muted
          loop
          playsinline
          class="w-full h-full object-cover object-center"
          style="filter: brightness(0.88);"
        />
        <!-- Imagen cuando el medio activo es imagen -->
        <img
          v-else-if="activeMedia.src"
          :src="activeMedia.src"
          :alt="headline"
          class="w-full h-full object-cover object-center transition-opacity duration-700"
          style="filter: brightness(0.88);"
        />
        <div
          v-else
          class="w-full h-full min-h-[420px]"
          :style="{ backgroundColor: '#1a1a1a' }"
        ></div>
      </div>

      <!-- Gradiente oscuro desde abajo — da legibilidad al texto -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/25 to-transparent pointer-events-none"></div>

      <!-- TEXTO SUPERPUESTO — Bottom-Left, tipografía brutal -->
      <div class="absolute bottom-0 left-0 right-0 px-5 pb-6 pt-20">

        <!-- Etiqueta de Drop — micro uppercase -->
        <p
          class="text-[9px] uppercase tracking-[0.5em] font-black mb-2.5"
          :style="{ color: palette.primary }"
        >{{ dropLabel }}</p>

        <!-- Título enorme y pesado -->
        <h2
          class="text-[40px] sm:text-5xl lg:text-6xl font-black uppercase tracking-tighter leading-[0.92] text-white mb-4 whitespace-pre-line"
          :style="{ fontFamily: fonts.heading + ', Impact, Arial Black, sans-serif' }"
        >{{ headline }}</h2>

        <!-- Subheadline opcional — solo si existe -->
        <p
          v-if="subheadline"
          class="text-[13px] font-semibold text-white/60 mb-5 max-w-xs uppercase tracking-wide"
        >{{ subheadline }}</p>

        <!-- BOTÓN DE CONVERSIÓN: Sólido, ancho completo en móvil -->
        <button
          @click="$emit('cta')"
          class="block w-full sm:inline-block sm:w-auto px-8 py-3.5 text-[11px] font-black uppercase tracking-[0.2em] text-center border-2 transition-all duration-150 active:scale-95"
          :style="{
            backgroundColor: palette.primary,
            borderColor: palette.primary,
            color: primaryContrastText,
            fontFamily: fonts.body + ', sans-serif',
          }"
          @mouseover="e => { e.currentTarget.style.backgroundColor = 'transparent'; e.currentTarget.style.color = palette.primary }"
          @mouseleave="e => { e.currentTarget.style.backgroundColor = palette.primary; e.currentTarget.style.color = primaryContrastText }"
        >{{ ctaText }}</button>

        <!-- Indicadores de slide — solo si hay más de 1 medio -->
        <div v-if="allMedia.length > 1" class="flex items-center gap-1.5 mt-4">
          <button
            v-for="(_, i) in allMedia"
            :key="i"
            @click="currentIndex = i"
            class="h-[3px] rounded-full transition-all duration-300"
            :style="{
              width: i === currentIndex ? '20px' : '6px',
              backgroundColor: i === currentIndex ? palette.primary : 'rgba(255,255,255,0.35)'
            }"
          />
        </div>

      </div>
    </div>

    <!-- TIRA INFERIOR: Estadísticas o claims de marca — complemento editorial -->
    <div
      v-if="stats && stats.length"
      class="flex items-center justify-around px-5 py-4 border-t"
      style="border-color: rgba(255,255,255,0.08);"
    >
      <div
        v-for="(stat, i) in stats.slice(0, 3)"
        :key="i"
        class="flex flex-col items-center text-center"
      >
        <span class="text-[20px] font-black text-white leading-none" :style="{ fontFamily: fonts.heading + ', sans-serif' }">{{ stat.value }}</span>
        <span class="text-[8px] uppercase tracking-[0.25em] text-white/40 font-bold mt-0.5">{{ stat.label }}</span>
      </div>
    </div>

  </section>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  headline:    { type: String, default: 'Nueva\nColección' },
  subheadline: { type: String, default: '' },
  dropLabel:   { type: String, default: 'Drop 01 — 2026' },
  ctaText:     { type: String, default: 'Shop Now' },
  image:       { type: String, default: '' },
  images:      { type: Array, default: () => [] },
  video:       { type: String, default: '' },
  stats:       { type: Array, default: () => [] },
  palette:     { type: Object, default: () => ({ primary: '#facc15', background: '#111111', text_dark: '#111111', secondary: '#27272a', text_light: '#ffffff' }) },
  fonts:       { type: Object, default: () => ({ heading: 'Montserrat', body: 'Montserrat' }) },
})

defineEmits(['cta'])

// ── Carrusel de medios ──────────────────────────────────────────────────────
const currentIndex = ref(0)
let timer = null

// Construye la lista ordenada: imágenes → video al final
const allMedia = computed(() => {
  const items = []
  const imgs = props.images.filter(Boolean)
  if (imgs.length > 0) {
    imgs.forEach(src => items.push({ type: 'image', src }))
  } else if (props.image) {
    items.push({ type: 'image', src: props.image })
  }
  if (props.video) items.push({ type: 'video', src: props.video })
  return items
})

const activeMedia = computed(() => allMedia.value[currentIndex.value] || { type: 'image', src: props.image })

function advance() {
  if (allMedia.value.length > 1) {
    currentIndex.value = (currentIndex.value + 1) % allMedia.value.length
  }
}

onMounted(() => {
  if (allMedia.value.length > 1) {
    timer = setInterval(advance, 4500)
  }
})

onUnmounted(() => { if (timer) clearInterval(timer) })
// ────────────────────────────────────────────────────────────────────────────

// Helper W3C: calcula luminancia relativa de un color hex
// Retorna true si el color es claro (necesita texto oscuro encima)
function isLightColor(hex) {
  if (!hex || !hex.startsWith('#')) return false
  const c = hex.replace('#', '')
  if (c.length < 6) return false
  const r = parseInt(c.substring(0, 2), 16)
  const g = parseInt(c.substring(2, 4), 16)
  const b = parseInt(c.substring(4, 6), 16)
  return (0.299 * r + 0.587 * g + 0.114 * b) / 255 > 0.55
}

// Color de texto que contrasta automáticamente sobre palette.primary
const primaryContrastText = computed(() =>
  isLightColor(props.palette.primary) ? '#111111' : '#ffffff'
)
</script>
