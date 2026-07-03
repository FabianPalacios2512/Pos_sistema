<template>
  <!-- GENERAL TRUST BENEFITS: Functional large blocks highlighting free shipping, support, etc. -->
  <section class="w-full py-12 px-4 sm:px-6 lg:px-8 border-y" :style="{ backgroundColor: bgPrimary, borderColor: palette.secondary || '#e5e7eb' }">
    <div class="max-w-7xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-10">
        <!-- Benefit Blocks -->
        <div 
          v-for="(msg, i) in displayMessages" 
          :key="i"
          class="flex flex-col items-center text-center p-6 rounded-2xl bg-white shadow-sm border border-gray-100 hover:shadow-md transition-shadow"
        >
          <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4" :style="{ backgroundColor: (palette.primary || '#3b82f6') + '15', color: palette.primary || '#3b82f6' }">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="icons[i % icons.length]">
            </svg>
          </div>
          <h4 class="text-lg font-bold mb-2" :style="{ color: textContrast, fontFamily: fonts.heading }">{{ msg.title }}</h4>
          <p class="text-sm" :style="{ color: textContrast + 'cc', fontFamily: fonts.body }">{{ msg.desc }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Inter', body: 'Inter' }) },
  trustMessages: { type: Array, default: () => [] }
})

const bgPrimary = computed(() => props.palette.background === '#ffffff' || !props.palette.background ? '#f8fafc' : props.palette.background)

const isBackgroundDark = computed(() => {
  const bg = bgPrimary.value
  const hex = bg.replace('#', '')
  if (hex.length !== 6) return false
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16)
  const b = parseInt(hex.substr(4, 2), 16)
  return (r * 299 + g * 587 + b * 114) / 1000 < 128
})

const textContrast = computed(() => isBackgroundDark.value ? '#ffffff' : (props.palette.text_dark || '#0a0a0a'))

const icons = [
  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />',
  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />',
  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z" />'
]

const displayMessages = computed(() => {
  if (props.trustMessages && props.trustMessages.length > 0) {
    return props.trustMessages.slice(0, 3).map((msg, index) => {
      // Split message into title and desc if it's long, or just use it as desc
      let title = `Beneficio ${index + 1}`
      let desc = msg
      
      if (typeof msg === 'string' && msg.includes(':')) {
        const parts = msg.split(':')
        title = parts[0].trim()
        desc = parts.slice(1).join(':').trim()
      } else if (typeof msg === 'string' && msg.length > 30) {
        // If it's a long sentence, just make up a short title based on index
        const defaultTitles = ['Tu Mejor Opción', 'Confianza Total', 'Soporte Garantizado']
        title = defaultTitles[index]
      } else {
        // Short text, use as title, no desc
        title = msg
        desc = ''
      }
      return { title, desc }
    })
  }
  
  return [
    { title: 'Envíos Seguros', desc: 'Recibe tus compras en la puerta de tu casa con total seguridad.' },
    { title: 'Pago 100% Protegido', desc: 'Tus transacciones y datos están protegidos con la mejor tecnología.' },
    { title: 'Soporte Dedicado', desc: 'Atención personalizada para resolver cualquier duda que tengas.' }
  ]
})
</script>
