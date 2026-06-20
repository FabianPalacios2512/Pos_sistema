<template>
  <!-- TRUST STRIP ICON GRID: Large icons + descriptions, fashion brand style -->
  <section class="py-10 md:py-14" :style="{ backgroundColor: bgColor }">
    <div class="max-w-5xl mx-auto px-6">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
        <div
          v-for="(item, idx) in displayItems"
          :key="'trust-icon-' + idx"
          class="flex flex-col items-center text-center"
        >
          <!-- Icon circle -->
          <div
            class="w-14 h-14 md:w-16 md:h-16 rounded-full flex items-center justify-center mb-3 transition-transform duration-300 hover:scale-105"
            :style="{ backgroundColor: iconBgColor, border: '1px solid ' + iconBorderColor }"
          >
            <component :is="getIcon(item, idx)" class="w-5 h-5 md:w-6 md:h-6" :style="{ color: iconColor }" />
          </div>
          <!-- Title -->
          <p
            class="text-[12px] md:text-[13px] font-semibold mb-1 leading-tight"
            :style="{ color: textColor }"
          >{{ item.title || item }}</p>
          <!-- Description -->
          <p
            v-if="item.description"
            class="text-[10px] md:text-[11px] leading-relaxed max-w-[160px]"
            :style="{ color: textColor, opacity: 0.5 }"
          >{{ item.description }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, h } from 'vue'

const props = defineProps({
  items: {
    type: Array,
    default: () => [
      { title: 'Envío Gratis', description: 'En compras desde $99.900' },
      { title: 'Pago Seguro', description: 'Todas las plataformas' },
      { title: 'Cambios Gratis', description: '30 días para cambio' },
      { title: 'Soporte 24/7', description: 'Te ayudamos siempre' }
    ]
  },
  bgColor: { type: String, default: '#ffffff' },
  textColor: { type: String, default: '#1a1a1a' },
  iconColor: { type: String, default: '#1a1a1a' },
  iconBgColor: { type: String, default: '#f5f5f5' },
  iconBorderColor: { type: String, default: '#e8e8e8' }
})

const displayItems = computed(() => props.items.slice(0, 4))

// Predefined icons for common trust messages
const iconComponents = [
  // Shipping truck
  (p, { color }) => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '1.3', style: { color } }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25m-2.25 0h-2.25m0 0V3.375c0-.621-.504-1.125-1.125-1.125H4.125C3.504 2.25 3 2.754 3 3.375v11.25' })
  ]),
  // Shield check (security)
  (p, { color }) => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '1.3', style: { color } }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z' })
  ]),
  // Arrow path (returns)
  (p, { color }) => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '1.3', style: { color } }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3' })
  ]),
  // Chat bubbles (support)
  (p, { color }) => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '1.3', style: { color } }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155' })
  ])
]

const getIcon = (item, idx) => iconComponents[idx % iconComponents.length]
</script>
