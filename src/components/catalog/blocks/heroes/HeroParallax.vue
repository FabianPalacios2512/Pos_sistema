<template>
  <!-- HERO PARALLAX: Scroll-reactive parallax background with clean fixed text panel -->
  <section
    ref="parallaxSection"
    class="relative w-full overflow-hidden flex items-center justify-center"
    :style="{ height: isMobilePreview ? '420px' : '560px', backgroundColor: palette.background || '#ffffff' }"
  >
    <!-- Background Image with Parallax translation -->
    <div
      class="absolute inset-0 w-full h-[120%] -top-[10%] z-0"
      :style="{ transform: `translate3d(0, ${yTranslation}px, 0)` }"
    >
      <img
        v-if="backgroundImage"
        :src="backgroundImage"
        :alt="headline"
        class="w-full h-full object-cover"
      />
      <div
        v-else
        class="w-full h-full"
        :style="{ background: `linear-gradient(135deg, ${palette.accent || '#111827'}, ${palette.primary || '#1a1a1a'})` }"
      ></div>
    </div>

    <!-- Soft Overlay -->
    <div
      class="absolute inset-0 z-10"
      style="background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.5) 60%, rgba(0,0,0,0.75) 100%);"
    ></div>

    <!-- Content Panel -->
    <div class="relative z-20 text-center px-6 max-w-2xl text-white">
      <span
        v-if="subheadline"
        class="text-[10px] uppercase tracking-[0.3em] font-semibold text-white/70 block mb-3"
        :style="{ fontFamily: fonts.body + ', sans-serif' }"
      >
        Exclusivo
      </span>
      <h2
        class="text-4xl md:text-6xl leading-[1.05] tracking-tight mb-6"
        :style="{ fontFamily: fonts.heading + ', serif', fontWeight: 500 }"
      >
        {{ headline }}
      </h2>

      <!-- Subtle line divider -->
      <div class="w-12 h-[1px] bg-white/35 mx-auto mb-6"></div>

      <!-- CTA -->
      <button
        v-if="ctaText"
        @click="$emit('cta')"
        class="px-8 py-3.5 bg-white text-gray-900 hover:bg-white/90 text-[11px] font-bold uppercase tracking-[0.2em] transition-all duration-300 hover:scale-[1.02]"
        :style="{ fontFamily: fonts.body + ', sans-serif' }"
      >
        {{ ctaText }}
      </button>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  headline: { type: String, default: '' },
  subheadline: { type: String, default: '' },
  backgroundImage: { type: String, default: '' },
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Inter' }) },
  ctaText: { type: String, default: '' },
  isMobilePreview: { type: Boolean, default: false }
})

defineEmits(['cta'])

const parallaxSection = ref(null)
const yTranslation = ref(0)
let ticking = false

const handleScroll = () => {
  if (!ticking) {
    window.requestAnimationFrame(() => {
      if (parallaxSection.value) {
        const rect = parallaxSection.value.getBoundingClientRect()
        const scrollPosition = window.scrollY || window.pageYOffset
        const sectionTop = rect.top + scrollPosition
        
        // Calculate relative position of scroll compared to this component
        const relativeScroll = scrollPosition - sectionTop
        
        // Parallax speed coefficient: 0.15 (slower movement)
        yTranslation.value = relativeScroll * 0.15
      }
      ticking = false
    })
    ticking = true
  }
}

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('scroll', handleScroll, { passive: true })
  }
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('scroll', handleScroll)
  }
})
</script>
