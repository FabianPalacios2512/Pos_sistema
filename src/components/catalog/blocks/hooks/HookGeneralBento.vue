<template>
  <!-- GENERAL BENTO DEPARTMENTS: Square grid for retail categories -->
  <section class="w-full py-16 px-4 sm:px-6 lg:px-8" :style="{ backgroundColor: bgPrimary }">
    <div class="max-w-7xl mx-auto">
      <div class="mb-10 text-center md:text-left flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <h3 
            class="text-3xl lg:text-4xl font-black mb-2 tracking-tight"
            :style="{ fontFamily: fonts.heading + ', sans-serif', color: textContrast }"
          >
            Nuestros Departamentos
          </h3>
          <p class="text-base" :style="{ fontFamily: fonts.body + ', sans-serif', color: textContrast + 'cc' }">
            Encuentra de todo al mejor precio
          </p>
        </div>
        <button 
          class="hidden md:inline-flex items-center gap-2 font-bold hover:underline"
          :style="{ color: palette.primary }"
        >
          Ver todo el catálogo
          <span>&rarr;</span>
        </button>
      </div>

      <!-- Bento Grid -->
      <div v-if="displayCategories.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <!-- Main Large Block (Category 0) -->
        <div 
          v-if="displayCategories[0]"
          @click="$emit('select-category', displayCategories[0].id)"
          class="col-span-2 row-span-2 relative rounded-2xl overflow-hidden group aspect-square md:aspect-auto cursor-pointer" 
          :style="{ backgroundColor: palette.secondary || '#f1f5f9' }"
        >
          <img :src="displayCategories[0].image || 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&q=80&w=800'" :alt="displayCategories[0].name" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
          <div class="absolute bottom-0 left-0 p-6 md:p-8">
            <h4 class="text-white text-2xl md:text-3xl font-bold mb-2">{{ displayCategories[0].name }}</h4>
            <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full bg-white text-sm font-bold text-black group-hover:bg-opacity-90 transition-colors">
              Explorar
            </span>
          </div>
        </div>

        <!-- Small Blocks (Categories 1+) -->
        <div 
          v-for="(cat, i) in smallCategories" 
          :key="i" 
          @click="$emit('select-category', cat.id)"
          class="relative rounded-2xl overflow-hidden group aspect-square cursor-pointer bg-slate-200"
        >
          <img :src="cat.image || 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?auto=format&fit=crop&q=80&w=400'" :alt="cat.name" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
          <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-colors"></div>
          <div class="absolute inset-0 flex flex-col items-center justify-center p-4 text-center">
            <h4 class="text-white text-lg md:text-xl font-bold">{{ cat.name }}</h4>
          </div>
        </div>
      </div>
      
      <button 
        class="mt-8 w-full md:hidden py-4 rounded-xl font-bold border-2"
        :style="{ borderColor: palette.primary, color: palette.primary }"
      >
        Ver todo el catálogo
      </button>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  aboutUs: { type: String, default: '' },
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Inter', body: 'Inter' }) },
  categories: { type: Array, default: () => [] }
})

defineEmits(['select-category'])

const bgPrimary = computed(() => props.palette.background || '#ffffff')

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

// Use real categories, fallback to mock data if empty
const mockDepartments = [
  { id: null, name: 'Tecnología', image: 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?auto=format&fit=crop&q=80&w=400' },
  { id: null, name: 'Herramientas', image: 'https://images.unsplash.com/photo-1581166397057-235af2b3c6dd?auto=format&fit=crop&q=80&w=400' },
  { id: null, name: 'Cuidado Personal', image: 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400' },
  { id: null, name: 'Miscelánea', image: 'https://images.unsplash.com/photo-1605810230434-7631ac76ec81?auto=format&fit=crop&q=80&w=400' }
]

const displayCategories = computed(() => {
  if (props.categories && props.categories.length > 0) {
    // Only take the first 5 categories to fit the bento grid perfectly (1 large + 4 small)
    return props.categories.slice(0, 5)
  }
  return [
    { id: null, name: 'Hogar y Cocina', image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&q=80&w=800' },
    ...mockDepartments
  ]
})

const smallCategories = computed(() => {
  if (displayCategories.value.length > 1) {
    return displayCategories.value.slice(1)
  }
  return []
})
</script>
