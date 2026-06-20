<template>
  <!-- TRUST STRIP: MARQUEE — Urbano / Streetwear -->
  <!-- Solo texto, separado por ✦. Animación CSS infinita de derecha a izquierda. -->
  <!-- DESKTOP: Bigger text, taller separators, slightly slower animation -->
  <div
    class="w-full overflow-hidden relative"
    :style="{ backgroundColor: bgColor }"
  >
    <!-- Track animado — duplicado para loop seamless -->
    <div
      class="flex items-center"
      :class="animate ? 'marquee-track' : 'justify-center flex-wrap gap-x-6 py-2.5 lg:py-4 px-4'"
    >
      <template v-for="pass in (animate ? 2 : 1)" :key="pass">
        <span
          v-for="(item, i) in items"
          :key="`${pass}-${i}`"
          class="flex items-center gap-4 lg:gap-6 flex-shrink-0 py-2.5 lg:py-4 px-2 lg:px-3"
        >
          <span
            class="text-[10px] lg:text-[12px] uppercase font-semibold tracking-[0.14em] lg:tracking-[0.18em] whitespace-nowrap"
            :style="{ color: textColor }"
          >{{ item.label }}</span>
          <span
            class="flex-shrink-0 w-px h-3 lg:h-4 opacity-40"
            :style="{ backgroundColor: textColor }"
            aria-hidden="true"
          ></span>
        </span>
      </template>
    </div>
  </div>
</template>

<script setup>
defineProps({
  items: {
    type: Array,
    default: () => [
      { label: 'Envío gratis' },
      { label: 'Pago seguro' },
      { label: 'Devolución fácil' },
      { label: 'Asesoría VIP' },
      { label: 'Productos originales' },
      { label: 'Envío gratis' },
    ]
  },
  bgColor:     { type: String, default: '#111111' },
  textColor:   { type: String, default: '#ffffff' },
  accentColor: { type: String, default: '#ffffff' },
  animate:     { type: Boolean, default: true },
})
</script>

<style scoped>
.marquee-track {
  display: flex;
  width: max-content;
  animation: marquee-scroll 35s linear infinite;
}

@media (min-width: 1024px) {
  .marquee-track {
    animation-duration: 42s;
  }
}

.marquee-track:hover {
  animation-play-state: paused;
}

@keyframes marquee-scroll {
  from { transform: translateX(0); }
  to   { transform: translateX(-50%); }
}
</style>
