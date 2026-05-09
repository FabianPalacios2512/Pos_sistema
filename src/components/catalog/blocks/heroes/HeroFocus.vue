<template>
  <!-- HERO FOCUS — Consumo / Supermercados / Catálogos rápidos -->
  <!-- Producto destacado a la derecha, copy directo y badge de oferta a la izquierda -->
  <section
    class="relative w-full overflow-hidden"
    :style="{ backgroundColor: bgSurface }"
    :class="isMobilePreview ? 'py-8' : 'py-10 md:py-14'"
  >
    <div class="max-w-[1280px] mx-auto px-5 md:px-10">
      <div
        class="grid items-center gap-6 md:gap-10"
        :class="isMobilePreview ? 'grid-cols-1' : 'grid-cols-1 md:grid-cols-2'"
      >

        <!-- COLUMNA IZQUIERDA: Copy y CTA -->
        <div class="flex flex-col" :class="isMobilePreview ? 'items-center text-center' : 'items-start text-left'">

          <!-- Badge de oferta / novedad -->
          <div class="flex items-center gap-2 mb-4">
            <span
              class="text-[8px] uppercase tracking-[0.22em] font-bold px-2.5 py-1"
              :style="{ backgroundColor: palette.primary + '18', color: palette.primary, fontFamily: fonts.body + ', sans-serif' }"
            >{{ badge }}</span>
            <span class="text-[9px] text-gray-400 uppercase tracking-[0.16em]" :style="{ fontFamily: fonts.body + ', sans-serif' }">2026</span>
          </div>

          <!-- Título directo, sin adornos -->
          <h2
            class="leading-tight mb-3"
            :class="isMobilePreview ? 'text-[28px]' : 'text-[32px] md:text-[44px]'"
            :style="{ fontFamily: fonts.heading + ', Georgia, serif', fontWeight: 600, color: palette.text_dark, letterSpacing: '-0.01em' }"
          >{{ headline }}</h2>

          <!-- Subtítulo informativo -->
          <p
            v-if="subheadline"
            class="text-sm leading-relaxed mb-7 max-w-[320px]"
            :style="{ color: palette.text_dark + '88', fontFamily: fonts.body + ', sans-serif' }"
            :class="{ 'mx-auto': isMobilePreview }"
          >{{ subheadline }}</p>

          <!-- CTA sólido, conversión directa -->
          <button
            @click="$emit('cta')"
            class="text-[11px] uppercase tracking-[0.18em] font-semibold px-7 py-3.5 transition-all duration-200 hover:opacity-85"
            :style="{ backgroundColor: palette.primary, color: '#ffffff', fontFamily: fonts.body + ', sans-serif' }"
          >{{ ctaText || 'VER PRODUCTOS' }}</button>

          <!-- Trust micro-copy -->
          <p class="text-[9px] text-gray-400 mt-3 uppercase tracking-[0.12em]" :style="{ fontFamily: fonts.body + ', sans-serif' }">Envío gratis · Devolución fácil</p>
        </div>

        <!-- COLUMNA DERECHA: Imagen del producto destacado -->
        <div class="relative" :class="isMobilePreview ? 'h-[46vw] min-h-[160px]' : 'h-[320px] md:h-[440px]'">
          <img
            v-if="backgroundImage"
            :src="backgroundImage"
            alt=""
            class="w-full h-full object-cover"
            loading="eager"
          />
          <!-- Placeholder limpio sin imagen -->
          <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center">
            <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
          </div>
          <!-- Número de productos flotante, minimalista -->
          <div
            v-if="productCount > 0"
            class="absolute bottom-3 right-3 px-2.5 py-1.5"
            :style="{ backgroundColor: palette.text_dark }"
          >
            <p class="text-[9px] text-white uppercase tracking-[0.14em]" :style="{ fontFamily: fonts.body + ', sans-serif' }">{{ productCount }} productos</p>
          </div>
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
  palette:         { type: Object, default: () => ({ primary: '#10B981', text_dark: '#111827', background: '#ffffff' }) },
  fonts:           { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
  ctaText:         { type: String, default: '' },
  badge:           { type: String, default: 'Nueva Colección' },
  productCount:    { type: Number, default: 0 },
  isMobilePreview: { type: Boolean, default: false },
})
defineEmits(['cta'])

import { computed } from 'vue'
const bgSurface = computed(() => props.palette.background === '#ffffff' ? '#f9f9f8' : props.palette.background)
</script>
