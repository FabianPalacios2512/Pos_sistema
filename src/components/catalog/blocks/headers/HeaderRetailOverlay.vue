<template>
  <!-- HEADER RETAIL OVERLAY — Urbano / Streetwear / Premium E-commerce -->
  <!-- Transparente sobre el hero. Sólido al scroll. Full-width sin bordes redondeados. -->
  <header
    class="fixed top-8 left-0 right-0 z-50 bg-white shadow-[0_1px_0_0_rgba(0,0,0,0.07)]"
    :style="{ fontFamily: fonts.body + ', sans-serif', backgroundColor: palette.background || '#ffffff' }"
  >
    <div class="flex items-center justify-between h-[60px] px-6 lg:px-8">

      <!-- Izquierda: Logo o nombre de tienda -->
      <div class="flex items-center">
        <img
          v-if="logoUrl"
          :src="logoUrl"
          :alt="storeName"
          class="h-8 max-w-[140px] object-contain brightness-0"
        />
        <span
          v-else
          class="font-bold text-base tracking-[0.12em] uppercase leading-none"
          :style="{ fontFamily: fonts.heading + ', sans-serif', color: palette.text_dark || '#111111' }"
        >{{ storeName }}</span>
      </div>

      <!-- Derecha: Lupa + Bolsa + Hamburguesa -->
      <div class="flex items-center gap-5">

        <!-- Búsqueda -->
        <button
          @click="showSearch = !showSearch"
          class="flex items-center justify-center w-9 h-9 transition-opacity hover:opacity-60"
          :style="{ color: palette.text_dark || '#111111' }"
          aria-label="Buscar"
        >
          <svg class="w-[20px] h-[20px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.25">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
        </button>

        <!-- Bolsa -->
        <button
          @click="$emit('cart')"
          class="relative flex items-center justify-center w-9 h-9 transition-opacity hover:opacity-60"
          :style="{ color: palette.text_dark || '#111111' }"
          aria-label="Ver bolsa"
        >
          <svg class="w-[20px] h-[20px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.25">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
          </svg>
          <span
            v-if="cartCount > 0"
            class="absolute -top-0.5 -right-0.5 w-[15px] h-[15px] rounded-full flex items-center justify-center text-[8px] font-bold text-white leading-none"
            :style="{ backgroundColor: palette.primary || '#111827' }"
          >{{ cartCount > 9 ? '9+' : cartCount }}</span>
        </button>

        <!-- Hamburguesa -->
        <button
          @click="$emit('menu')"
          class="flex items-center justify-center w-9 h-9 transition-opacity hover:opacity-60"
          :style="{ color: palette.text_dark || '#111111' }"
          aria-label="Abrir menú"
        >
          <svg class="w-[20px] h-[20px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.25">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>

      </div>
    </div>

    <!-- Barra de búsqueda desplegable — underline limpio, sin caja -->
    <Transition name="overlay-search">
      <div
        v-if="showSearch"
        class="px-6 lg:px-8 pb-4 border-t border-gray-100"
        :style="{ backgroundColor: palette.background || '#ffffff' }"
      >
        <div class="relative flex items-center max-w-lg mt-3">
          <svg class="absolute left-0 w-4 h-4 pointer-events-none text-gray-400"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
          <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            placeholder="Buscar productos..."
            autofocus
            class="w-full pl-7 pr-8 py-2 text-sm bg-transparent outline-none border-b-2 border-gray-200 focus:border-gray-900 transition-colors duration-200"
            :style="{ fontFamily: fonts.body + ', sans-serif', color: palette.text_dark }"
          />
          <button
            @click="showSearch = false"
            class="absolute right-0 p-1 text-gray-400 hover:text-gray-700 transition-colors"
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
import { ref } from 'vue'

defineProps({
  storeName:  { type: String,  default: 'BRAND' },
  logoUrl:    { type: String,  default: '' },
  cartCount:  { type: Number,  default: 0 },
  modelValue: { type: String,  default: '' },
  palette:    { type: Object,  default: () => ({ primary: '#111827', background: '#ffffff', text_dark: '#111827' }) },
  fonts:      { type: Object,  default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
})

defineEmits(['menu', 'cart', 'update:modelValue'])

const showSearch = ref(false)
</script>

<style scoped>
.overlay-search-enter-active, .overlay-search-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.overlay-search-enter-from, .overlay-search-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
