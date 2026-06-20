<template>
  <!-- HOOK TESTIMONIALS: Social proof carousel with avatars -->
  <section class="py-14 md:py-20 overflow-hidden" :style="{ backgroundColor: palette.background || '#ffffff' }">
    <div class="max-w-5xl mx-auto px-6">
      <!-- Section header -->
      <div class="text-center mb-10">
        <p
          class="text-[10px] uppercase tracking-[0.24em] font-semibold mb-2"
          :style="{ color: palette.primary }"
        >{{ label }}</p>
        <h3
          class="text-2xl md:text-3xl tracking-tight"
          :style="{ fontFamily: fonts.heading + ', serif', fontWeight: 500, color: onBgText }"
        >{{ headline }}</h3>
      </div>

      <!-- Testimonials carousel -->
      <div class="relative">
        <!-- Cards -->
        <div class="flex gap-5 overflow-x-auto pb-4 scrollbar-hide snap-x snap-mandatory -mx-2 px-2">
          <div
            v-for="(testimonial, idx) in testimonials"
            :key="'test-' + idx"
            class="snap-center flex-shrink-0 w-[300px] md:w-[340px] p-6 rounded-2xl border transition-all duration-300 hover:-translate-y-1"
            :style="{ backgroundColor: cardBg, borderColor: borderColor }"
          >
            <!-- Stars -->
            <div class="flex gap-0.5 mb-4">
              <svg v-for="s in 5" :key="'star-' + s" class="w-3.5 h-3.5" :style="{ color: s <= (testimonial.rating || 5) ? '#fbbf24' : onBgText + '15' }" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </div>

            <!-- Quote -->
            <p
              class="text-sm leading-relaxed mb-5"
              :style="{ fontFamily: fonts.body + ', sans-serif', color: onBgText, opacity: 0.7 }"
            >"{{ testimonial.text || testimonial.quote }}"</p>

            <!-- Author -->
            <div class="flex items-center gap-3">
              <img
                :src="getAvatar(testimonial.name, idx)"
                :alt="testimonial.name"
                class="w-10 h-10 rounded-full object-cover border-2 shadow-sm"
                :style="{ borderColor: palette.primary + '30' }"
              />
              <div>
                <p class="text-[13px] font-semibold" :style="{ color: onBgText }">{{ testimonial.name }}</p>
                <p v-if="testimonial.date" class="text-[11px]" :style="{ color: onBgText + '60' }">{{ testimonial.date }}</p>
                <p v-else-if="testimonial.location" class="text-[11px]" :style="{ color: onBgText + '60' }">{{ testimonial.location }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CTA -->
      <div v-if="ctaText" class="text-center mt-8">
        <button
          @click="$emit('cta')"
          class="inline-flex items-center gap-2 text-[12px] font-semibold tracking-[0.08em] transition-all duration-300 group"
          :style="{ color: palette.primary }"
        >
          {{ ctaText }}
          <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
          </svg>
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  headline: { type: String, default: 'Lo que dicen nuestros clientes' },
  label: { type: String, default: 'Testimonios' },
  testimonials: {
    type: Array,
    default: () => [
      { name: 'María García', date: 'Hace 2 días', text: 'Excelente calidad y atención. Mis compras siempre llegan en perfecto estado.', rating: 5 },
      { name: 'Carlos Rodríguez', date: 'Hace 1 semana', text: 'Los mejores productos que he encontrado. Totalmente recomendado para cualquier ocasión.', rating: 5 },
      { name: 'Ana Martínez', date: 'Hace 3 días', text: 'Me encanta la variedad y los precios. Ya soy clienta frecuente desde hace meses.', rating: 5 }
    ]
  },
  ctaText: { type: String, default: '' },
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Inter' }) }
})

defineEmits(['cta'])

const isBackgroundDark = computed(() => {
  const bg = props.palette.background || '#ffffff'
  const hex = bg.replace('#', '')
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16)
  const b = parseInt(hex.substr(4, 2), 16)
  return (r * 299 + g * 587 + b * 114) / 1000 < 128
})

const onBgText = computed(() => isBackgroundDark.value ? '#ffffff' : (props.palette.text_dark || '#0a0a0a'))
const cardBg = computed(() => isBackgroundDark.value ? 'rgba(255,255,255,0.05)' : '#ffffff')
const borderColor = computed(() => isBackgroundDark.value ? 'rgba(255,255,255,0.08)' : '#f0f0f0')

const getInitials = (name) => {
  if (!name) return '?'
  return name.split(' ').map(w => w[0]).join('').slice(0, 2)
}

const getAvatar = (name, index) => {
  // Simple heuristic for generic names to look slightly more real
  const femaleNames = ['maria', 'ana', 'laura', 'sofia', 'valeria', 'isabella', 'camila', 'lucia', 'daniela', 'paula', 'andrea', 'diana', 'carolina']
  const firstWord = name ? name.toLowerCase().split(' ')[0] : ''
  const isFemale = femaleNames.includes(firstWord) || firstWord.endsWith('a')
  
  // Use index to pick a specific face number (1-99)
  const faceNum = (index % 50) + 10
  
  return isFemale 
    ? `https://randomuser.me/api/portraits/women/${faceNum}.jpg`
    : `https://randomuser.me/api/portraits/men/${faceNum}.jpg`
}
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
