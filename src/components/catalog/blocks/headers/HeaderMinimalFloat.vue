<template>
  <!-- HEADER MINIMAL FLOAT: Appears on scroll-up only, minimal pill style -->
  <header
    class="fixed top-8 left-0 right-0 z-50 transition-all duration-500"
    :class="visible ? 'translate-y-0 opacity-100' : '-translate-y-full opacity-0'"
  >
    <div class="mx-auto max-w-lg px-4 pt-2">
      <div
        class="flex items-center justify-between px-5 h-12 rounded-full shadow-lg backdrop-blur-xl transition-all"
        :style="{ backgroundColor: pillBg, border: '1px solid ' + borderColor }"
      >
        <!-- Store name -->
        <h1
          class="text-[13px] font-semibold truncate"
          :style="{ fontFamily: fonts.heading + ', sans-serif', color: textColor }"
        >{{ storeName }}</h1>

        <!-- Actions -->
        <div class="flex items-center gap-1">
          <!-- Search toggle -->
          <button @click="searchActive = !searchActive" class="w-8 h-8 flex items-center justify-center rounded-full transition-colors" :style="{ color: textColor }">
            <svg v-if="!searchActive" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <!-- Cart -->
          <button @click="$emit('cart')" class="relative w-8 h-8 flex items-center justify-center rounded-full" :style="{ color: textColor }">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
            </svg>
            <span
              v-if="cartCount > 0"
              class="absolute -top-0.5 -right-0.5 text-white text-[7px] font-bold w-3.5 h-3.5 rounded-full flex items-center justify-center"
              :style="{ backgroundColor: palette.primary }"
            >{{ cartCount }}</span>
          </button>
          <!-- Menu -->
          <button @click="$emit('menu')" class="w-8 h-8 flex items-center justify-center rounded-full lg:hidden" :style="{ color: textColor }">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Expandable search -->
      <Transition name="search-expand">
        <div v-if="searchActive" class="mt-2 px-1">
          <div
            class="flex items-center h-10 rounded-full px-4 shadow-md"
            :style="{ backgroundColor: pillBg, border: '1px solid ' + borderColor }"
          >
            <svg class="w-4 h-4 flex-shrink-0" :style="{ color: textColor + '50' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              :value="modelValue"
              @input="$emit('update:modelValue', $event.target.value)"
              type="text"
              placeholder="Buscar..."
              class="flex-1 bg-transparent text-sm outline-none ml-3 placeholder-gray-400"
              :style="{ color: textColor }"
              autofocus
            />
          </div>
        </div>
      </Transition>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  storeName: { type: String, default: '' },
  logoUrl: { type: String, default: '' },
  cartCount: { type: Number, default: 0 },
  modelValue: { type: String, default: '' },
  palette: { type: Object, default: () => ({}) },
  fonts: { type: Object, default: () => ({ heading: 'Inter', body: 'Inter' }) }
})

defineEmits(['menu', 'cart', 'update:modelValue'])

const visible = ref(false)
const searchActive = ref(false)
let lastScrollY = 0
const SCROLL_THRESHOLD = 200

const isBackgroundDark = computed(() => {
  const bg = props.palette.background || '#ffffff'
  const hex = bg.replace('#', '')
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16)
  const b = parseInt(hex.substr(4, 2), 16)
  return (r * 299 + g * 587 + b * 114) / 1000 < 128
})

const textColor = computed(() => isBackgroundDark.value ? '#ffffff' : (props.palette.text_dark || '#0a0a0a'))
const pillBg = computed(() => isBackgroundDark.value ? 'rgba(20,20,20,0.85)' : 'rgba(255,255,255,0.92)')
const borderColor = computed(() => isBackgroundDark.value ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)')

const onScroll = () => {
  const y = window.scrollY
  // Show when scrolling UP and past threshold
  visible.value = y > SCROLL_THRESHOLD && y < lastScrollY
  lastScrollY = y
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<style scoped>
.search-expand-enter-active, .search-expand-leave-active { transition: all 0.25s ease; }
.search-expand-enter-from, .search-expand-leave-to { opacity: 0; transform: translateY(-6px) scale(0.97); }
</style>
