<template>
  <!-- HOOK: URBAN LOOKBOOK — Solo imagen full-bleed, sin texto duplicado -->
  <!-- El usuario puede subir imágenes diferentes a las del hero. Botón CTA flotante elegante. -->
  <section class="w-full overflow-hidden" :style="{ backgroundColor: '#111111' }">

    <!-- Imagen full-bleed -->
    <div class="relative">
      <!-- Imagen: portrait en móvil, cinematic en desktop -->
      <div class="aspect-[3/4] sm:aspect-[16/8] lg:aspect-[21/9] overflow-hidden">
        <!-- Video cuando el medio activo es video -->
        <video
          v-if="activeMedia.type === 'video'"
          :src="activeMedia.src"
          autoplay
          muted
          loop
          playsinline
          class="w-full h-full object-cover object-center"
          style="filter: brightness(0.92);"
        />
        <!-- Imagen cuando el medio activo es imagen -->
        <img
          v-else-if="activeMedia.src"
          :src="activeMedia.src"
          alt=""
          class="w-full h-full object-cover object-center transition-opacity duration-700"
          style="filter: brightness(0.92);"
        />
        <div
          v-else
          class="w-full h-full min-h-[420px]"
          :style="{ backgroundColor: '#1a1a1a' }"
        ></div>
      </div>

      <!-- Gradiente sutil inferior — solo para dar elegancia al CTA -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent pointer-events-none"></div>

      <!-- BOTÓN CTA flotante — Elegante, solo en la esquina inferior -->
      <div class="absolute bottom-0 left-0 right-0 px-5 pb-5 lg:px-14 lg:pb-10 flex items-end justify-between">
        <!-- CTA discreto -->
        <button
          @click="$emit('cta')"
          class="flex items-center gap-2.5 text-[10px] lg:text-[11px] uppercase tracking-[0.2em] font-bold px-5 lg:px-7 py-2.5 lg:py-3 transition-all duration-200 active:scale-95 backdrop-blur-sm"
          :style="{
            backgroundColor: 'rgba(255,255,255,0.12)',
            border: '1px solid rgba(255,255,255,0.25)',
            color: '#ffffff',
            fontFamily: fonts.body + ', sans-serif',
          }"
          @mouseover="e => { e.currentTarget.style.backgroundColor = palette.primary; e.currentTarget.style.borderColor = palette.primary }"
          @mouseleave="e => { e.currentTarget.style.backgroundColor = 'rgba(255,255,255,0.12)'; e.currentTarget.style.borderColor = 'rgba(255,255,255,0.25)' }"
        >{{ ctaText }}
          <svg class="w-3.5 h-3.5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
          </svg>
        </button>

        <!-- Indicadores de slide — solo si hay más de 1 medio -->
        <div v-if="allMedia.length > 1" class="flex items-center gap-1.5">
          <button
            v-for="(_, i) in allMedia"
            :key="i"
            @click="currentIndex = i"
            class="h-[3px] rounded-full transition-all duration-300"
            :style="{
              width: i === currentIndex ? '20px' : '6px',
              backgroundColor: i === currentIndex ? '#ffffff' : 'rgba(255,255,255,0.35)'
            }"
          />
        </div>
      </div>
    </div>

  </section>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  headline:    { type: String, default: '' },
  subheadline: { type: String, default: '' },
  dropLabel:   { type: String, default: '' },
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
</script>
