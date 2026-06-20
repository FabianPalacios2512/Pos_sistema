<template>
  <!-- HERO FOCUS — Consumo / Supermercados / Catálogos rápidos -->
  <!-- Producto destacado derecha, copy BOLD directo y badge izquierda -->
  <section
    class="relative w-full overflow-hidden"
    :style="{ backgroundColor: bgSurface }"
    :class="isMobilePreview ? 'py-8' : 'py-10 md:py-14 lg:py-20'"
  >
    <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-16">
      <div
        class="grid items-center gap-6 md:gap-10 lg:gap-16"
        :class="isMobilePreview ? 'grid-cols-1' : 'grid-cols-1 md:grid-cols-2'"
      >

        <!-- COLUMNA IZQUIERDA: Copy y CTA -->
        <div class="flex flex-col" :class="isMobilePreview ? 'items-center text-center' : 'items-start text-left'">

          <!-- Badge de oferta / novedad -->
          <div class="flex items-center gap-2 mb-5 lg:mb-7">
            <span
              class="text-[9px] lg:text-[11px] uppercase tracking-[0.2em] font-bold px-3 lg:px-4 py-1.5 lg:py-2"
              :style="{ backgroundColor: palette.primary + '20', color: palette.primary, fontFamily: fonts.body + ', sans-serif' }"
            >{{ badge }}</span>
            <span class="text-[10px] lg:text-[11px] text-gray-400 uppercase tracking-[0.14em] font-medium" :style="{ fontFamily: fonts.body + ', sans-serif' }">2026</span>
          </div>

          <!-- Título directo BOLD -->
          <h2
            class="leading-tight mb-4 lg:mb-6"
            :class="isMobilePreview ? 'text-[28px]' : 'text-[32px] md:text-[42px] lg:text-[52px] xl:text-[58px]'"
            :style="{
              fontFamily: fonts.heading + ', Georgia, serif',
              fontWeight: 700,
              color: palette.text_dark,
              letterSpacing: '-0.01em'
            }"
          >{{ headline }}</h2>

          <!-- Subtítulo informativo -->
          <p
            v-if="subheadline"
            class="text-[13px] lg:text-[15px] leading-relaxed mb-8 lg:mb-10 max-w-[320px] lg:max-w-[420px] font-medium"
            :style="{ color: palette.text_dark + '88', fontFamily: fonts.body + ', sans-serif' }"
            :class="{ 'mx-auto': isMobilePreview }"
          >{{ subheadline }}</p>

          <!-- CTA sólido BOLD -->
          <button
            @click="$emit('cta')"
            class="text-[11px] lg:text-[13px] uppercase tracking-[0.16em] font-bold px-8 lg:px-12 py-4 lg:py-5 transition-all duration-200 hover:opacity-85"
            :style="{ backgroundColor: palette.primary, color: '#ffffff', fontFamily: fonts.body + ', sans-serif' }"
          >{{ ctaText || 'VER PRODUCTOS' }}</button>

          <!-- Trust micro-copy -->
          <p class="text-[10px] lg:text-[11px] text-gray-400 mt-4 lg:mt-5 uppercase tracking-[0.12em] font-medium" :style="{ fontFamily: fonts.body + ', sans-serif' }">Envío gratis · Devolución fácil</p>
        </div>

        <!-- COLUMNA DERECHA: Imagen del producto -->
        <div class="relative" :class="isMobilePreview ? 'h-[46vw] min-h-[160px]' : 'h-[320px] md:h-[440px] lg:h-[520px] xl:h-[560px]'">
          <img
            v-if="backgroundImage"
            :src="backgroundImage"
            alt=""
            class="w-full h-full object-cover lg:rounded-sm"
            loading="eager"
          />
          <div v-else class="w-full h-full bg-gray-100 lg:rounded-sm flex items-center justify-center">
            <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
          </div>
          <!-- Número de productos flotante -->
          <div
            v-if="productCount > 0"
            class="absolute bottom-3 right-3 lg:bottom-5 lg:right-5 px-3 lg:px-5 py-2 lg:py-2.5"
            :style="{ backgroundColor: palette.text_dark }"
          >
            <p class="text-[10px] lg:text-[11px] text-white uppercase tracking-[0.14em] font-bold" :style="{ fontFamily: fonts.body + ', sans-serif' }">{{ productCount }} productos</p>
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
