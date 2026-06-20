<template>
  <!-- HERO VIDEO LOOP: Autoplay muted video/gif background with premium text overlay -->
  <section
    class="relative w-full flex items-center justify-center overflow-hidden"
    :style="{ height: isMobilePreview ? '420px' : '540px', backgroundColor: palette.background || '#ffffff' }"
  >
    <!-- Background Video -->
    <video
      v-if="videoUrl"
      ref="videoRef"
      autoplay
      loop
      muted
      playsinline
      class="absolute inset-0 w-full h-full object-cover z-0"
      @canplay="onVideoCanPlay"
    >
      <source :src="videoUrl" type="video/mp4">
    </video>

    <!-- Fallback image when video is loading, failed or not provided -->
    <img
      v-if="(!videoUrl || videoFailed) && backgroundImage"
      :src="backgroundImage"
      :alt="headline"
      class="absolute inset-0 w-full h-full object-cover z-0"
    />

    <div
      v-if="(!videoUrl || videoFailed) && !backgroundImage"
      class="absolute inset-0 z-0"
      :style="{ background: `linear-gradient(135deg, ${palette.primary || '#111827'}, ${palette.accent || '#1f2937'})` }"
    ></div>

    <!-- Overlay content with high-contrast gradient -->
    <div 
      class="absolute inset-0 z-10 flex flex-col justify-end px-6 pb-12 md:px-12 md:pb-16"
      style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.1) 100%);"
    >
      <div class="max-w-xl text-left">
        <!-- Live Badge -->
        <div v-if="videoUrl && !videoFailed" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-500 text-white text-[9px] font-bold uppercase tracking-wider rounded-full mb-4 shadow-sm">
          <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
          EN VIVO
        </div>

        <h2
          class="text-3xl md:text-5xl text-white leading-[1.1] mb-3 tracking-tight"
          :style="{ fontFamily: fonts.heading + ', serif', fontWeight: 600 }"
        >{{ headline }}</h2>
        <p
          v-if="subheadline"
          class="text-sm md:text-base text-white/80 leading-relaxed mb-6 max-w-md"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >{{ subheadline }}</p>

        <!-- CTA Buttons -->
        <div class="flex flex-wrap gap-3">
          <button
            v-if="ctaText"
            @click="$emit('cta')"
            class="px-8 py-3 text-[11px] font-bold uppercase tracking-[0.16em] transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]"
            :style="{ backgroundColor: palette.primary || '#ffffff', color: '#ffffff' }"
          >{{ ctaText }}</button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  headline: { type: String, default: '' },
  subheadline: { type: String, default: '' },
  backgroundImage: { type: String, default: '' },
  video: { type: String, default: '' },
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Inter' }) },
  ctaText: { type: String, default: '' },
  isMobilePreview: { type: Boolean, default: false }
})

defineEmits(['cta'])

const videoRef = ref(null)
const videoFailed = ref(false)

const videoUrl = computed(() => {
  // Use prop video first, or search inside public links if any
  return props.video || ''
})

const onVideoCanPlay = () => {
  if (videoRef.value) {
    videoRef.value.play().catch(() => {
      // Autoplay failed or blocked
    })
  }
}
</script>
