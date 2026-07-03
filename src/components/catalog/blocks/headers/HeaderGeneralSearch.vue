<template>
  <!-- HEADER GENERAL SEARCH: Amazon/MercadoLibre style for mass retail -->
  <header
    ref="headerEl"
    class="transition-all duration-300 w-full"
    :style="{ backgroundColor: headerBg, boxShadow: scrolled ? '0 4px 12px rgba(0,0,0,0.06)' : '0 1px 3px rgba(0,0,0,0.04)' }"
  >
    <!-- Top small bar (optional utility bar for general retail) -->
    <div class="hidden sm:flex w-full px-4 lg:px-8 h-8 items-center justify-end gap-4 text-[11px] lg:text-xs font-medium" :style="{ backgroundColor: 'rgba(0,0,0,0.03)', color: textColor + '99' }">
      <span>🛍️ Tu tienda de confianza</span>
      <span>🚚 Envíos seguros</span>
      <span>💳 Pago protegido</span>
    </div>

    <!-- Main row -->
    <div class="w-full px-4 lg:px-8 h-16 lg:h-20 flex items-center gap-4 lg:gap-8">
      <!-- Hamburger Menu (Mobile) -->
      <button @click="$emit('menu')" class="lg:hidden w-10 h-10 flex items-center justify-center -ml-2 rounded-full hover:bg-black/5">
        <svg class="w-6 h-6" :style="{ color: textColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
      </button>

      <!-- Logo / Store name -->
      <div class="flex items-center gap-2 flex-shrink-0 min-w-0 cursor-pointer" @click="$emit('home')">
        <img v-if="logoUrl" :src="logoUrl" :alt="storeName" class="h-8 lg:h-10 w-auto object-contain" />
        <h1
          v-else
          class="text-lg lg:text-xl font-black truncate tracking-tight"
          :style="{ fontFamily: fonts.heading + ', sans-serif', color: textColor }"
        >{{ storeName }}</h1>
      </div>

      <!-- Search bar (center, massive) -->
      <div class="flex-1 max-w-3xl mx-auto hidden sm:block">
        <div class="relative group">
          <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            type="text"
            placeholder="Buscar productos, marcas y más..."
            class="w-full h-11 lg:h-12 pl-5 pr-14 rounded-lg text-sm transition-all outline-none focus:ring-2 focus:ring-opacity-50"
            :style="{ backgroundColor: searchBg, border: '1px solid ' + searchBorder, color: palette.text_dark || '#1a1a1a', '--tw-ring-color': palette.primary }"
          />
          <button class="absolute right-1 top-1 bottom-1 w-12 rounded-md flex items-center justify-center transition-transform active:scale-95" :style="{ backgroundColor: palette.primary, color: '#ffffff' }">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Right actions -->
      <div class="flex items-center gap-2 lg:gap-4 flex-shrink-0">
        <!-- Mobile search toggle -->
        <button @click="showMobileSearch = !showMobileSearch" class="sm:hidden w-10 h-10 flex items-center justify-center rounded-full hover:bg-black/5">
          <svg class="w-5 h-5" :style="{ color: textColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
        </button>

        <!-- User / Account (Desktop only placeholder for retail look) -->
        <div class="hidden md:flex flex-col items-start justify-center text-xs cursor-pointer">
          <span :style="{ color: textColor + '99' }">Bienvenido</span>
          <span class="font-bold" :style="{ color: textColor }">Ingresa / Regístrate</span>
        </div>

        <!-- Cart -->
        <button @click="$emit('cart')" class="relative w-10 h-10 lg:w-12 lg:h-12 flex items-center justify-center rounded-full hover:bg-black/5 transition-colors">
          <svg class="w-6 h-6" :style="{ color: textColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
          </svg>
          <span
            v-if="cartCount > 0"
            class="absolute top-0 right-0 text-white text-[10px] font-bold min-w-[18px] h-[18px] px-1 rounded-full flex items-center justify-center shadow-sm border border-white"
            :style="{ backgroundColor: palette.primary }"
          >{{ cartCount }}</span>
        </button>
      </div>
    </div>

    <!-- Mobile search expanded -->
    <Transition name="slide-down">
      <div v-if="showMobileSearch" class="sm:hidden px-4 pb-4">
        <div class="relative shadow-lg rounded-lg">
          <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            type="text"
            placeholder="Buscar productos..."
            class="w-full h-12 pl-4 pr-12 rounded-lg text-sm outline-none focus:ring-2"
            :style="{ backgroundColor: searchBg, border: '1px solid ' + searchBorder, color: palette.text_dark || '#1a1a1a', '--tw-ring-color': palette.primary }"
            autofocus
          />
          <button @click="showMobileSearch = false" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-md hover:bg-black/5">
            <svg class="w-5 h-5" :style="{ color: textColor + '80' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
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

defineEmits(['menu', 'cart', 'update:modelValue', 'home'])

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
const searchBg = computed(() => isBackgroundDark.value ? 'rgba(255,255,255,0.05)' : '#ffffff')
const searchBorder = computed(() => isBackgroundDark.value ? 'rgba(255,255,255,0.1)' : '#d1d5db')

const onScroll = () => { scrolled.value = window.scrollY > 20 }
onMounted(() => window.addEventListener('scroll', onScroll))
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-8px); }
</style>
