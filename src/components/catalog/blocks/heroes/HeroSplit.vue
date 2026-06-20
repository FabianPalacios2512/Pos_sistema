<template>
  <!-- HERO SPLIT — Minimalismo / Cosmética -->
  <!-- Pantalla dividida: color sólido + fotografía. Tipografía BOLD con presencia. -->
  <section
    class="relative w-full overflow-hidden"
    :class="heightClass"
  >
    <div class="grid grid-cols-1 lg:grid-cols-[44%_56%] h-full">

      <!-- LADO IZQUIERDO: Texto sobre color sólido -->
      <div
        class="flex flex-col justify-center px-8 md:px-12 lg:px-16 xl:px-24 2xl:px-28 py-10 lg:py-16 order-2 lg:order-1"
        :style="{ backgroundColor: bgColor }"
      >
        <!-- Eyebrow label -->
        <p
          class="text-[9px] lg:text-[11px] xl:text-[12px] uppercase tracking-[0.3em] mb-5 lg:mb-8 font-semibold"
          :style="{ color: labelColor, fontFamily: fonts.body + ', sans-serif' }"
        >{{ seasonLabel }}</p>

        <!-- Título BOLD con impacto -->
        <h2
          class="leading-[0.94] mb-5 lg:mb-8"
          :class="isMobilePreview ? 'text-[36px]' : 'text-[40px] lg:text-[58px] xl:text-[70px] 2xl:text-[78px]'"
          :style="{
            fontFamily: fonts.heading + ', Georgia, serif',
            fontWeight: 700,
            color: titleColor,
            letterSpacing: '-0.02em'
          }"
        >{{ headline }}</h2>

        <!-- Línea decorativa gruesa -->
        <div class="w-12 lg:w-16 h-[2.5px] mb-5 lg:mb-7" :style="{ backgroundColor: labelColor }"></div>

        <!-- Subtítulo -->
        <p
          v-if="subheadline"
          class="text-sm lg:text-base xl:text-lg leading-relaxed mb-8 lg:mb-12 max-w-[300px] lg:max-w-[420px] font-medium"
          :style="{ color: titleColor + 'bb', fontFamily: fonts.body + ', sans-serif' }"
        >{{ subheadline }}</p>

        <!-- CTA BOLD rectangular -->
        <div class="flex items-center gap-5 lg:gap-7">
          <button
            @click="$emit('cta')"
            class="text-[11px] lg:text-[13px] uppercase tracking-[0.18em] font-bold px-8 lg:px-14 py-4 lg:py-5 transition-all duration-300 hover:opacity-85 active:scale-[0.98]"
            :style="{ backgroundColor: palette.primary, color: '#ffffff', fontFamily: fonts.body + ', sans-serif' }"
          >{{ ctaText || 'COMPRAR AHORA' }}</button>

          <!-- Desktop: hint de scroll -->
          <span class="hidden lg:flex items-center gap-1.5 text-[10px] uppercase tracking-[0.2em] font-medium opacity-35" :style="{ color: titleColor }">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
            Explorar
          </span>
        </div>
      </div>

      <!-- LADO DERECHO: Fotografía -->
      <div class="relative overflow-hidden order-1 lg:order-2" :class="isMobilePreview ? 'h-[56vw] min-h-[220px]' : 'h-[62vw] min-h-[280px] lg:h-auto'">
        <img
          v-if="backgroundImage"
          :src="backgroundImage"
          alt=""
          class="w-full h-full object-cover"
          loading="eager"
        />
        <div v-else class="w-full h-full" :style="{ backgroundColor: palette.secondary + '40' }"></div>
        <!-- Overlay sutil -->
        <div class="absolute inset-0 bg-black/5"></div>

        <!-- Desktop: año editorial flotante -->
        <div class="hidden lg:flex absolute bottom-8 right-8 xl:bottom-12 xl:right-12 flex-col items-end pointer-events-none select-none">
          <span class="text-[10px] uppercase tracking-[0.3em] text-white/40 font-semibold">Colección</span>
          <span class="font-black text-white leading-none" style="font-size: 60px; font-family: Georgia, serif; opacity: 0.08;">{{ new Date().getFullYear() }}</span>
        </div>
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
  props.isMobilePreview ? 'min-h-[420px]' : 'min-h-[420px] lg:min-h-[620px] lg:h-[88vh] lg:max-h-[900px]'
)

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
