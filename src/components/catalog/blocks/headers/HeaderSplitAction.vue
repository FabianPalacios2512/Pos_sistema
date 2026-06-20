<template>
  <!-- HEADER SPLIT ACTION: Amazon/MercadoLibre style — logo left, search center, icons right -->
  <header
    ref="headerEl"
    class="transition-all duration-300"
    :style="{ backgroundColor: headerBg, boxShadow: scrolled ? '0 2px 12px rgba(0,0,0,0.08)' : '0 1px 3px rgba(0,0,0,0.05)' }"
  >
    <!-- Main row -->
    <div class="w-full px-4 lg:px-6 h-14 lg:h-16 flex items-center gap-3">
      <!-- Logo / Store name -->
      <div class="flex items-center gap-2 flex-shrink-0 min-w-0">
        <button @click="$emit('menu')" class="lg:hidden w-9 h-9 flex items-center justify-center -ml-1 flex-shrink-0">
          <svg class="w-5 h-5" :style="{ color: textColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.3">
            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
          </svg>
        </button>
        <img v-if="logoUrl" :src="logoUrl" :alt="storeName" class="h-7 lg:h-8 w-auto object-contain" />
        <h1
          v-else
          class="text-base lg:text-lg font-bold truncate"
          :style="{ fontFamily: fonts.heading + ', sans-serif', color: textColor }"
        >{{ storeName }}</h1>
      </div>

      <!-- Search bar (center, flexible) -->
      <div class="flex-1 max-w-xl mx-2 lg:mx-6 hidden sm:block">
        <div class="relative">
          <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            type="text"
            placeholder="Buscar en la tienda..."
            class="w-full h-10 pl-4 pr-10 rounded-full text-sm transition-all outline-none"
            :style="{ backgroundColor: searchBg, border: '1.5px solid ' + searchBorder, color: palette.text_dark || '#1a1a1a' }"
          />
          <button class="absolute right-1 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full flex items-center justify-center" :style="{ backgroundColor: palette.primary, color: '#ffffff' }">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Right actions -->
      <div class="flex items-center gap-1 flex-shrink-0">
        <!-- Mobile search toggle -->
        <button @click="showMobileSearch = !showMobileSearch" class="sm:hidden w-9 h-9 flex items-center justify-center">
          <svg class="w-5 h-5" :style="{ color: textColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
        </button>
        <!-- Cart -->
        <button @click="$emit('cart')" class="relative w-9 h-9 flex items-center justify-center">
          <svg class="w-5 h-5" :style="{ color: textColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
          </svg>
          <span
            v-if="cartCount > 0"
            class="absolute -top-0.5 -right-0.5 text-white text-[8px] font-bold min-w-[16px] h-4 rounded-full flex items-center justify-center"
            :style="{ backgroundColor: palette.primary }"
          >{{ cartCount }}</span>
        </button>
      </div>
    </div>

    <!-- Mobile search expanded -->
    <Transition name="slide-down">
      <div v-if="showMobileSearch" class="sm:hidden px-4 pb-3">
        <div class="relative">
          <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            type="text"
            placeholder="Buscar productos..."
            class="w-full h-10 pl-4 pr-10 rounded-full text-sm outline-none"
            :style="{ backgroundColor: searchBg, border: '1.5px solid ' + searchBorder, color: palette.text_dark || '#1a1a1a' }"
            autofocus
          />
          <button @click="showMobileSearch = false" class="absolute right-3 top-1/2 -translate-y-1/2">
            <svg class="w-4 h-4" :style="{ color: textColor + '60' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </Transition>
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

const scrolled = ref(false)
const showMobileSearch = ref(false)

const isBackgroundDark = computed(() => {
  const bg = props.palette.background || '#ffffff'
  const hex = bg.replace('#', '')
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16)
  const b = parseInt(hex.substr(4, 2), 16)
  return (r * 299 + g * 587 + b * 114) / 1000 < 128
})

const headerBg = computed(() => props.palette.background || '#ffffff')
const textColor = computed(() => isBackgroundDark.value ? '#ffffff' : (props.palette.text_dark || '#0a0a0a'))
const searchBg = computed(() => isBackgroundDark.value ? 'rgba(255,255,255,0.08)' : '#f5f5f5')
const searchBorder = computed(() => isBackgroundDark.value ? 'rgba(255,255,255,0.12)' : '#e5e5e5')

const onScroll = () => { scrolled.value = window.scrollY > 20 }
onMounted(() => window.addEventListener('scroll', onScroll))
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-8px); }
</style>
