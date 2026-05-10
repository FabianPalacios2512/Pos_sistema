<template>
  <!-- HOOK DARK NOIR — Luxury / Alta Costura / Tech Premium -->
  <!-- Reemplaza el Brand Story blanco. Se funde con bg-black. Texto blanc + gray-400. -->
  <!-- Mobile: imagen arriba, texto abajo. Desktop: imagen izquierda asimétrica (60/40). -->
  <section class="w-full" style="background-color: #000000">

    <div class="flex flex-col lg:flex-row" :class="isMobilePreview ? '' : 'lg:min-h-[500px]'">

      <!-- IMAGEN: Ocupa 60% en desktop, arriba en móvil -->
      <div
        class="relative overflow-hidden order-1 lg:order-1"
        :class="isMobilePreview ? 'h-[56vw] min-h-[220px]' : 'h-[62vw] min-h-[260px] lg:h-auto lg:w-[58%]'"
      >
        <img
          v-if="image"
          :src="image"
          :alt="headline"
          class="absolute inset-0 w-full h-full object-cover object-center"
        />
        <!-- Placeholder noir -->
        <div
          v-else
          class="absolute inset-0 flex items-center justify-center"
          style="background: #111111"
        >
          <svg class="w-16 h-16 opacity-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.7"
              d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
          </svg>
        </div>

        <!-- Degradado de fusión: imagen → negro hacia la derecha en desktop -->
        <div
          class="absolute inset-0 hidden lg:block"
          style="background: linear-gradient(to right, transparent 55%, rgba(0,0,0,0.55) 80%, #000000 100%)"
        ></div>
        <!-- Degradado abajo en móvil -->
        <div
          class="absolute inset-0 lg:hidden"
          style="background: linear-gradient(to bottom, transparent 60%, rgba(0,0,0,0.6) 90%, #000000 100%)"
        ></div>
      </div>

      <!-- TEXTO: Derecha en desktop (40%), abajo en móvil -->
      <div
        class="flex flex-col justify-center order-2 lg:order-2 px-6 py-10 lg:w-[42%] lg:px-12 xl:px-16 lg:py-16"
        :style="{ fontFamily: fonts.body + ', sans-serif' }"
      >

        <!-- Micro etiqueta -->
        <p
          class="text-[9px] uppercase tracking-[0.38em] font-bold mb-6"
          :style="{ color: palette.primary || '#ffffff' }"
        >{{ label }}</p>

        <!-- Título serif grande — blanco puro -->
        <h2
          class="text-white leading-[1.06] font-light mb-5 whitespace-pre-line"
          :class="isMobilePreview ? 'text-[26px]' : 'text-[28px] lg:text-[38px] xl:text-[46px]'"
          :style="{ fontFamily: fonts.heading + ', Georgia, \'Times New Roman\', serif' }"
        >{{ headline }}</h2>

        <!-- Cuerpo — gris plata, máximo 60 chars por línea -->
        <p
          class="text-gray-400 text-[13.5px] leading-relaxed max-w-[310px] mb-10"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >{{ body }}</p>

        <!-- CTA: outline sutil noir -->
        <a
          href="#"
          @click.prevent="$emit('cta')"
          class="inline-flex items-center gap-3 self-start group border border-white/20 hover:border-white/60 transition-colors duration-300 px-6 py-3"
        >
          <span
            class="text-[9px] uppercase tracking-[0.28em] font-semibold text-white"
            :style="{ fontFamily: fonts.body + ', sans-serif' }"
          >{{ ctaText }}</span>
          <svg
            class="w-3 h-3 text-white/60 transition-transform duration-200 group-hover:translate-x-1 group-hover:text-white"
            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
          </svg>
        </a>

      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  headline:        { type: String, default: 'Oscuridad que\nenamora' },
  body:            { type: String, default: 'Piezas concebidas en silencio, construidas con precisión. Para quienes no necesitan explicar su estilo.' },
  label:           { type: String, default: 'Brand Story' },
  ctaText:         { type: String, default: 'Ver Colección' },
  image:           { type: String, default: '' },
  palette:         { type: Object, default: () => ({ primary: '#ffffff', text_dark: '#000000', background: '#000000' }) },
  fonts:           { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
  isMobilePreview: { type: Boolean, default: false },
})

defineEmits(['cta'])
</script>
