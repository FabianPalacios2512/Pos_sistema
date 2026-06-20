<template>
  <!-- HERO CAROUSEL: Auto-rotating slides with fade/slide transitions -->
  <section class="relative w-full overflow-hidden" :style="{ height: isMobilePreview ? '420px' : '520px', backgroundColor: palette.background || '#ffffff' }">
    <!-- Slides -->
    <TransitionGroup :name="transitionMode">
      <div
        v-for="(img, idx) in validImages"
        :key="'slide-' + idx"
        v-show="currentSlide === idx"
        class="absolute inset-0 w-full h-full"
      >
        <img
          :src="img"
          :alt="headline + ' - slide ' + (idx + 1)"
          class="w-full h-full object-cover"
        />
        <!-- Gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/25 to-transparent"></div>
      </div>
    </TransitionGroup>

    <!-- Placeholder when no images -->
    <div v-if="validImages.length === 0" class="absolute inset-0 flex items-center justify-center" :style="{ backgroundColor: palette.primary + '15' }">
      <div class="text-center px-6">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-20" :style="{ color: palette.text_dark }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />
        </svg>
      </div>
    </div>

    <!-- Content overlay -->
    <div class="absolute inset-0 flex flex-col justify-end px-6 pb-12 md:px-12 md:pb-16 z-10">
      <div class="max-w-xl">
        <h2
          class="text-3xl md:text-5xl text-white leading-[1.1] mb-3 tracking-tight"
          :style="{ fontFamily: fonts.heading + ', serif', fontWeight: 500 }"
        >{{ headline }}</h2>
        <p
          v-if="subheadline"
          class="text-sm md:text-base text-white/80 leading-relaxed mb-6 max-w-md"
          :style="{ fontFamily: fonts.body + ', sans-serif' }"
        >{{ subheadline }}</p>
        <button
          v-if="ctaText"
          @click="$emit('cta')"
          class="px-8 py-3 text-[12px] font-semibold uppercase tracking-[0.16em] transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]"
          :style="{ backgroundColor: '#ffffff', color: palette.text_dark || '#0a0a0a' }"
        >{{ ctaText }}</button>
      </div>
    </div>

    <!-- Progress bars -->
    <div v-if="validImages.length > 1" class="absolute bottom-4 left-6 right-6 md:left-12 md:right-12 flex gap-1 z-20">
      <button
        v-for="(_, idx) in validImages"
        :key="'bar-' + idx"
        @click="goToSlide(idx)"
        class="h-[3px] flex-1 rounded-full overflow-hidden transition-all duration-300"
        :style="{ backgroundColor: 'rgba(255,255,255,0.25)' }"
      >
        <div
          class="h-full rounded-full transition-all"
          :class="currentSlide === idx ? 'carousel-bar-active' : ''"
          :style="{ backgroundColor: currentSlide === idx ? '#ffffff' : 'transparent', width: currentSlide === idx ? progressWidth + '%' : '0%' }"
        ></div>
      </button>
    </div>

    <!-- Swipe zones (mobile) -->
    <div
      class="absolute inset-0 z-10 md:hidden"
      @touchstart="onTouchStart"
      @touchmove="onTouchMove"
      @touchend="onTouchEnd"
    ></div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  headline: { type: String, default: '' },
  subheadline: { type: String, default: '' },
  backgroundImage: { type: String, default: '' },
  images: { type: Array, default: () => [] },
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Inter' }) },
  ctaText: { type: String, default: '' },
  isMobilePreview: { type: Boolean, default: false },
  autoPlayInterval: { type: Number, default: 5000 },
  transitionMode: { type: String, default: 'carousel-fade' }
})

defineEmits(['cta'])

const currentSlide = ref(0)
const progressWidth = ref(0)
let autoPlayTimer = null
let progressTimer = null
let touchStartX = 0

const validImages = computed(() => {
  const imgs = [...(props.images || [])]
  if (props.backgroundImage && !imgs.includes(props.backgroundImage)) {
    imgs.unshift(props.backgroundImage)
  }
  return imgs.filter(Boolean)
})

const goToSlide = (idx) => {
  currentSlide.value = idx
  resetAutoPlay()
}

const nextSlide = () => {
  if (validImages.value.length <= 1) return
  currentSlide.value = (currentSlide.value + 1) % validImages.value.length
}

const prevSlide = () => {
  if (validImages.value.length <= 1) return
  currentSlide.value = (currentSlide.value - 1 + validImages.value.length) % validImages.value.length
}

const startAutoPlay = () => {
  if (validImages.value.length <= 1) return
  progressWidth.value = 0
  const step = 50 // ms
  const totalSteps = props.autoPlayInterval / step
  let currentStep = 0
  progressTimer = setInterval(() => {
    currentStep++
    progressWidth.value = (currentStep / totalSteps) * 100
    if (currentStep >= totalSteps) {
      nextSlide()
      currentStep = 0
      progressWidth.value = 0
    }
  }, step)
}

const resetAutoPlay = () => {
  clearInterval(progressTimer)
  progressWidth.value = 0
  startAutoPlay()
}

// Touch handling
const onTouchStart = (e) => { touchStartX = e.touches[0].clientX }
const onTouchMove = () => {}
const onTouchEnd = (e) => {
  const dx = e.changedTouches[0].clientX - touchStartX
  if (Math.abs(dx) > 50) {
    dx < 0 ? nextSlide() : prevSlide()
    resetAutoPlay()
  }
}

onMounted(() => { startAutoPlay() })
onUnmounted(() => { clearInterval(progressTimer) })

watch(() => props.images, () => { currentSlide.value = 0; resetAutoPlay() })
</script>

<style scoped>
.carousel-fade-enter-active,
.carousel-fade-leave-active {
  transition: opacity 0.8s ease;
}
.carousel-fade-enter-from,
.carousel-fade-leave-to {
  opacity: 0;
}
</style>
