<template>
  <!-- HERO IMAGE GRID: Bento layout 1 large + 2 small -->
  <section class="relative w-full overflow-hidden" :style="{ backgroundColor: palette.background || '#ffffff' }">
    <div
      class="grid gap-1"
      :class="isMobilePreview ? 'grid-cols-2' : 'grid-cols-3'"
      :style="{ height: isMobilePreview ? '360px' : '480px' }"
    >
      <!-- Large image (spans 2 rows on desktop) -->
      <div
        class="relative overflow-hidden group"
        :class="isMobilePreview ? 'col-span-1 row-span-2' : 'col-span-2 row-span-2'"
      >
        <img
          v-if="backgroundImage"
          :src="backgroundImage"
          :alt="headline"
          class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
        />
        <div v-else class="w-full h-full" :style="{ backgroundColor: palette.primary + '10' }"></div>
        <!-- Overlay gradient -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/15 to-transparent"></div>
        <!-- Text content -->
        <div class="absolute bottom-0 left-0 right-0 p-5 md:p-8 z-10">
          <p
            class="text-[10px] uppercase tracking-[0.22em] text-white/70 font-semibold mb-2"
            :style="{ fontFamily: fonts.body + ', sans-serif' }"
          >{{ badge || 'Nueva Colección' }}</p>
          <h2
            class="text-2xl md:text-4xl text-white leading-[1.05] mb-3"
            :style="{ fontFamily: fonts.heading + ', serif', fontWeight: 500 }"
          >{{ headline }}</h2>
          <button
            v-if="ctaText"
            @click="$emit('cta')"
            class="px-6 py-2.5 bg-white text-[11px] font-semibold uppercase tracking-[0.14em] transition-all duration-300 hover:bg-opacity-90 active:scale-[0.98]"
            :style="{ color: palette.text_dark || '#0a0a0a' }"
          >{{ ctaText }}</button>
        </div>
      </div>

      <!-- Small images column -->
      <div class="col-span-1 grid grid-rows-2 gap-1">
        <!-- Top small -->
        <div class="relative overflow-hidden group">
          <img
            v-if="images[1]"
            :src="images[1]"
            alt="Collection"
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
          />
          <div v-else class="w-full h-full" :style="{ backgroundColor: palette.secondary || palette.primary + '20' }"></div>
          <div class="absolute inset-0 bg-black/25 group-hover:bg-black/35 transition-all"></div>
          <div class="absolute bottom-3 left-3 z-10">
            <p class="text-[10px] text-white font-semibold uppercase tracking-[0.14em]" :style="{ fontFamily: fonts.body + ', sans-serif' }">
              {{ subLabels[0] || 'Tendencias' }}
            </p>
          </div>
        </div>
        <!-- Bottom small -->
        <div class="relative overflow-hidden group">
          <img
            v-if="images[2]"
            :src="images[2]"
            alt="Collection"
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
          />
          <div v-else class="w-full h-full" :style="{ backgroundColor: palette.accent || palette.primary + '30' }"></div>
          <div class="absolute inset-0 bg-black/25 group-hover:bg-black/35 transition-all"></div>
          <div class="absolute bottom-3 left-3 z-10">
            <p class="text-[10px] text-white font-semibold uppercase tracking-[0.14em]" :style="{ fontFamily: fonts.body + ', sans-serif' }">
              {{ subLabels[1] || 'Esenciales' }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  headline: { type: String, default: '' },
  subheadline: { type: String, default: '' },
  backgroundImage: { type: String, default: '' },
  images: { type: Array, default: () => [] },
  badge: { type: String, default: '' },
  subLabels: { type: Array, default: () => ['Tendencias', 'Esenciales'] },
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Inter' }) },
  ctaText: { type: String, default: '' },
  isMobilePreview: { type: Boolean, default: false }
})
defineEmits(['cta'])
</script>
