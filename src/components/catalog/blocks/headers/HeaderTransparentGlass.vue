<template>
  <!-- HEADER TRANSPARENT GLASS — Cosmética / Sobre el Hero Portrait o Editorial -->
  <!-- top-8: respeta la barra de anuncios. Transparente → glass al scroll. -->
  <!-- Pairing perfecto: hero_style = 'portrait' o 'editorial' -->
  <header
    class="fixed top-8 left-0 right-0 z-50 transition-all duration-300"
    :class="isScrolled
      ? 'bg-white/90 backdrop-blur-md shadow-sm'
      : 'bg-transparent'"
  >
    <div class="relative flex items-center justify-between h-14 px-4">

      <!-- Izquierda: Hamburguesa -->
      <button
        @click="$emit('menu')"
        class="flex items-center justify-center w-10 h-10 -ml-2 transition-colors"
        :class="isScrolled ? 'text-gray-800 hover:text-black' : 'text-white hover:text-white/80'"
        aria-label="Abrir menú"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
      </button>

      <!-- Centro: Logo -->
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center">
        <img
          v-if="logoUrl"
          :src="logoUrl"
          :alt="storeName"
          class="h-8 max-w-[140px] object-contain transition-all duration-300"
          :class="isScrolled ? 'brightness-0' : 'brightness-0 invert'"
        />
        <span
          v-else
          class="text-base tracking-[0.25em] uppercase font-medium leading-none transition-colors duration-300"
          :class="isScrolled ? '' : 'text-white'"
          :style="{
            fontFamily: fonts.heading + ', Georgia, serif',
            color: isScrolled ? palette.text_dark : undefined
          }"
        >{{ storeName }}</span>
      </div>

      <!-- Derecha: Búsqueda + Bolsa -->
      <div class="flex items-center gap-1 -mr-2">
        <button
          @click="showSearch = !showSearch"
          class="flex items-center justify-center w-10 h-10 transition-colors"
          :class="isScrolled ? 'text-gray-800 hover:text-black' : 'text-white hover:text-white/80'"
          aria-label="Buscar"
        >
          <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
        </button>

        <button
          @click="$emit('cart')"
          class="relative flex items-center justify-center w-10 h-10 transition-colors"
          :class="isScrolled ? 'text-gray-800 hover:text-black' : 'text-white hover:text-white/80'"
          aria-label="Ver bolsa"
        >
          <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
          </svg>
          <span
            v-if="cartCount > 0"
            class="absolute top-1.5 right-1.5 w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-bold text-white leading-none"
            :style="{ backgroundColor: palette.primary || '#111827' }"
          >{{ cartCount > 9 ? '9+' : cartCount }}</span>
        </button>
      </div>

    </div>

    <!-- Barra de búsqueda expandible — glass style -->
    <Transition name="glass-search">
      <div
        v-if="showSearch"
        class="px-4 pb-3 transition-colors duration-300"
        :class="isScrolled ? 'bg-white/90' : 'bg-black/40 backdrop-blur-md'"
      >
        <div class="relative flex items-center">
          <svg
            class="absolute left-3 w-4 h-4 pointer-events-none transition-colors"
            :class="isScrolled ? 'text-gray-500' : 'text-white/70'"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
          <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            placeholder="Buscar productos..."
            autofocus
            class="w-full pl-9 pr-10 py-2 text-sm rounded-full border outline-none transition-all duration-200"
            :class="isScrolled
              ? 'bg-gray-100 text-gray-900 placeholder-gray-400 border-transparent focus:bg-white focus:border-gray-300'
              : 'bg-white/15 text-white placeholder-white/60 border-white/30 focus:bg-white/25'"
            :style="{ fontFamily: fonts.body + ', sans-serif' }"
          />
          <button
            @click="showSearch = false"
            class="absolute right-3 transition-colors"
            :class="isScrolled ? 'text-gray-400 hover:text-gray-700' : 'text-white/70 hover:text-white'"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </Transition>

  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

defineProps({
  storeName:   { type: String, default: 'Mi Tienda' },
  logoUrl:     { type: String, default: '' },
  cartCount:   { type: Number, default: 0 },
  modelValue:  { type: String, default: '' },
  palette:     { type: Object, default: () => ({ primary: '#111827', background: '#ffffff', text_dark: '#111827' }) },
  fonts:       { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
})

defineEmits(['menu', 'cart', 'update:modelValue'])

const isScrolled = ref(false)
const showSearch = ref(false)

const handleScroll = () => {
  isScrolled.value = window.scrollY > 60
}

onMounted(() => window.addEventListener('scroll', handleScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<style scoped>
.glass-search-enter-active, .glass-search-leave-active { transition: all 0.2s ease; }
.glass-search-enter-from, .glass-search-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
