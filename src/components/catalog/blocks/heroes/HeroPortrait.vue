<template>
  <!-- HERO PORTRAIT — Fotografía tipo retrato, tipografía BOLD mixta, doble CTA -->
  <!-- DESKTOP: Presencia fuerte, CTAs anchos, título impactante -->
  <section class="relative w-full overflow-hidden" :class="heightClass">

    <!-- Imagen de fondo full -->
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

    <!-- Overlay FUERTE: gradiente agresivo para máxima legibilidad -->
    <div
      class="absolute inset-0"
      style="background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.75) 30%, rgba(0,0,0,0.35) 55%, transparent 100%)"
    ></div>

    <!-- Contenido inferior -->
    <div class="absolute inset-x-0 bottom-0 z-10 px-5 pb-6 md:px-10 md:pb-10 lg:px-16 lg:pb-16 xl:px-24 xl:pb-20">
      <div class="max-w-[1200px]">

        <!-- Titular con tipografía mixta: sans BOLD + serif italic -->
        <h2 class="text-white mb-5 lg:mb-8 leading-none" :class="isMobilePreview ? 'text-[30px]' : 'text-[34px] md:text-[50px] lg:text-[62px] xl:text-[72px]'">
          <span
            v-for="(line, i) in headlineLines"
            :key="i"
            class="block leading-tight"
          >
            <!-- Última línea: serif italic -->
            <em
              v-if="i === headlineLines.length - 1 && headlineLines.length > 1"
              class="not-italic font-semibold"
              :style="{ fontFamily: fonts.heading + ', Georgia, serif', fontStyle: 'italic', fontWeight: 600 }"
            >{{ line }}</em>
            <!-- Resto: sans BOLD agresivo -->
            <strong
              v-else
              class="font-black not-italic"
              :style="{ fontFamily: fonts.body + ', Arial Black, sans-serif', fontWeight: 900 }"
            >{{ line }}</strong>
          </span>
        </h2>

        <!-- Doble CTA rectangular — BOLD y prominente -->
        <div class="flex items-stretch gap-3 lg:gap-5" :class="isMobilePreview ? '' : 'lg:max-w-[560px]'">
          <!-- Botón primario: blanco sólido -->
          <button
            @click="$emit('cta')"
            class="flex-1 lg:flex-none text-[11px] lg:text-[13px] uppercase tracking-widest font-black px-5 lg:px-12 py-4 lg:py-5 bg-white text-black hover:bg-gray-100 transition-colors duration-200 text-center"
            :style="{ fontFamily: fonts.body + ', sans-serif' }"
          >{{ ctaText || 'VER CATÁLOGO' }}</button>

          <!-- Botón secundario: outline blanco grueso -->
          <button
            v-if="ctaSecondary"
            @click="$emit('ctaSecondary')"
            class="flex-1 lg:flex-none text-[11px] lg:text-[13px] uppercase tracking-widest font-bold px-5 lg:px-12 py-4 lg:py-5 bg-transparent border-2 border-white text-white hover:bg-white hover:text-black transition-colors duration-200 text-center"
            :style="{ fontFamily: fonts.body + ', sans-serif' }"
          >{{ ctaSecondary }}</button>
        </div>

        <!-- Dots de slide -->
        <div v-if="showDots" class="flex justify-center lg:justify-start gap-2.5 mt-5 lg:mt-7">
          <span
            v-for="n in 4" :key="n"
            class="transition-all duration-300"
            :class="n === 1 ? 'w-8 lg:w-10 h-[3px] rounded-full bg-white' : 'w-2 lg:w-2.5 h-[3px] rounded-full bg-white/35'"
          ></span>
        </div>
      </div>
    </div>

  </section>
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

const headlineLines = computed(() => {
  if (props.headline.includes('\n')) {
    return props.headline.split('\n').filter(Boolean)
  }
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
    : 'h-[92vh] min-h-[600px] md:h-[96vh] md:min-h-[700px] lg:max-h-[950px]'
)
</script>
