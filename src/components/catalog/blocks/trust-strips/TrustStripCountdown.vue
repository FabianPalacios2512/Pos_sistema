<template>
  <!-- TRUST STRIP COUNTDOWN: Urgency strip with timer + promotional text -->
  <section
    class="relative overflow-hidden"
    :style="{ backgroundColor: bgColor, borderTop: '1px solid ' + borderColor, borderBottom: '1px solid ' + borderColor }"
  >
    <div class="max-w-5xl mx-auto px-4 py-3 md:py-4 flex flex-col md:flex-row items-center justify-center gap-3 md:gap-6">
      <!-- Promo text -->
      <div class="flex items-center gap-2">
        <span
          class="w-1.5 h-1.5 rounded-full animate-pulse"
          :style="{ backgroundColor: accentColor }"
        ></span>
        <p
          class="text-[11px] md:text-[12px] font-semibold uppercase tracking-[0.1em]"
          :style="{ color: textColor }"
        >{{ promoText }}</p>
      </div>

      <!-- Countdown timer -->
      <div class="flex items-center gap-1.5">
        <div v-for="(unit, idx) in timeUnits" :key="'unit-' + idx" class="flex items-center gap-1.5">
          <div
            class="min-w-[36px] h-8 md:h-9 rounded-lg flex items-center justify-center"
            :style="{ backgroundColor: timerBg, border: '1px solid ' + timerBorder }"
          >
            <span class="text-[13px] md:text-[14px] font-bold tabular-nums" :style="{ color: timerTextColor, fontFamily: 'monospace' }">
              {{ String(unit.value).padStart(2, '0') }}
            </span>
          </div>
          <span v-if="idx < timeUnits.length - 1" class="text-xs font-bold" :style="{ color: textColor, opacity: 0.3 }">:</span>
        </div>
      </div>

      <!-- CTA link -->
      <button
        v-if="ctaText"
        @click="$emit('cta')"
        class="text-[10px] md:text-[11px] font-bold uppercase tracking-[0.12em] underline underline-offset-2 transition-opacity hover:opacity-80"
        :style="{ color: accentColor }"
      >{{ ctaText }}</button>
    </div>

    <!-- Animated gradient accent line -->
    <div class="absolute bottom-0 left-0 right-0 h-[2px] overflow-hidden">
      <div
        class="h-full w-[200%] animate-shimmer"
        :style="{ background: `linear-gradient(90deg, transparent, ${accentColor}40, ${accentColor}, ${accentColor}40, transparent)` }"
      ></div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  promoText: { type: String, default: 'Oferta Flash — Envío Gratis' },
  ctaText: { type: String, default: 'Ver Ofertas' },
  endTime: { type: [String, Number], default: '' },
  durationHours: { type: Number, default: 24 },
  bgColor: { type: String, default: '#ffffff' },
  textColor: { type: String, default: '#1a1a1a' },
  accentColor: { type: String, default: '#ef4444' },
  timerBg: { type: String, default: '#f5f5f5' },
  timerBorder: { type: String, default: '#e5e5e5' },
  timerTextColor: { type: String, default: '#1a1a1a' },
  borderColor: { type: String, default: '#f0f0f0' }
})

defineEmits(['cta'])

const hours = ref(0)
const minutes = ref(0)
const seconds = ref(0)
let interval = null

const timeUnits = computed(() => [
  { value: hours.value, label: 'h' },
  { value: minutes.value, label: 'm' },
  { value: seconds.value, label: 's' }
])

const updateTimer = () => {
  let endMs
  if (props.endTime) {
    endMs = new Date(props.endTime).getTime()
  } else {
    // Create a rolling 24h countdown from midnight
    const now = new Date()
    const midnight = new Date(now)
    midnight.setHours(24, 0, 0, 0)
    endMs = midnight.getTime()
  }
  const diff = Math.max(0, endMs - Date.now())
  hours.value = Math.floor(diff / 3600000)
  minutes.value = Math.floor((diff % 3600000) / 60000)
  seconds.value = Math.floor((diff % 60000) / 1000)
}

onMounted(() => {
  updateTimer()
  interval = setInterval(updateTimer, 1000)
})
onUnmounted(() => clearInterval(interval))
</script>

<style scoped>
@keyframes shimmer {
  0% { transform: translateX(-50%); }
  100% { transform: translateX(0%); }
}
.animate-shimmer { animation: shimmer 3s linear infinite; }
</style>
