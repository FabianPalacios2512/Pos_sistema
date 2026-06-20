<template>
  <!-- HEADER RETAIL OVERLAY — Urbano / Streetwear / Premium E-commerce -->
  <!-- DESKTOP: Logo izq, nav center, search+icons derecha. E-commerce profesional. -->
  <header
    class="fixed top-8 left-0 right-0 z-50 bg-white shadow-[0_1px_0_0_rgba(0,0,0,0.07)]"
    :style="{ fontFamily: fonts.body + ', sans-serif', backgroundColor: palette.background || '#ffffff' }"
  >
    <div class="flex items-center justify-between h-[60px] lg:h-[76px] px-6 lg:px-10 max-w-[1440px] mx-auto">

      <!-- Izquierda: Logo -->
      <div class="flex items-center">
        <img
          v-if="logoUrl"
          :src="logoUrl"
          :alt="storeName"
          class="h-8 lg:h-10 max-w-[140px] lg:max-w-[180px] object-contain brightness-0"
        />
        <span
          v-else
          class="font-bold text-base lg:text-xl tracking-[0.12em] uppercase leading-none"
          :style="{ fontFamily: fonts.heading + ', sans-serif', color: palette.text_dark || '#111111' }"
        >{{ storeName }}</span>
      </div>

      <!-- Centro: Nav links (solo desktop) -->
      <nav class="hidden lg:flex items-center gap-8">
        <span
          v-for="link in navLinks"
          :key="link"
          class="text-[11px] lg:text-[13px] uppercase tracking-[0.14em] lg:tracking-[0.12em] font-semibold cursor-pointer transition-all duration-200 relative overlay-nav"
          :style="{ color: (palette.text_dark || '#111') + '60' }"
          @mouseenter="e => e.target.style.color = palette.text_dark || '#111'"
          @mouseleave="e => e.target.style.color = (palette.text_dark || '#111') + '60'"
        >{{ link }}</span>
      </nav>

      <!-- Derecha: Búsqueda + Cuenta + Bolsa + Hamburguesa -->
      <div class="flex items-center gap-2 lg:gap-3">

        <!-- Desktop: inline search -->
        <div class="hidden lg:flex items-center">
          <div v-if="showSearch" class="relative mr-2">
            <input
              :value="modelValue"
              @input="$emit('update:modelValue', $event.target.value)"
              placeholder="Buscar..."
              autofocus
              class="w-[220px] pl-3 pr-8 py-2 text-sm border-b-2 bg-transparent outline-none transition-all focus:w-[280px]"
              :style="{ borderColor: palette.text_dark + '30', color: palette.text_dark, fontFamily: fonts.body }"
              @blur="showSearch = false"
            />
            <button @click="showSearch = false" class="absolute right-0 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-700">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
        </div>

        <!-- Búsqueda icon -->
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

        <!-- Cuenta (solo desktop) -->
        <button
          class="hidden lg:flex items-center justify-center w-9 h-9 transition-opacity hover:opacity-60"
          :style="{ color: palette.text_dark || '#111111' }"
          aria-label="Mi cuenta"
        >
          <svg class="w-[20px] h-[20px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.25">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
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

        <!-- Hamburguesa (solo móvil) -->
        <button
          @click="$emit('menu')"
          class="lg:hidden flex items-center justify-center w-9 h-9 transition-opacity hover:opacity-60"
          :style="{ color: palette.text_dark || '#111111' }"
          aria-label="Abrir menú"
        >
          <svg class="w-[20px] h-[20px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.25">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>

      </div>
    </div>

    <!-- Mobile: Barra de búsqueda desplegable -->
    <Transition name="overlay-search">
      <div
        v-if="showSearch"
        class="lg:hidden px-6 pb-4 border-t border-gray-100"
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

const navLinks = ['Catálogo', 'Novedades', 'Colecciones', 'Contacto']
</script>

<style scoped>
.overlay-search-enter-active, .overlay-search-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.overlay-search-enter-from, .overlay-search-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
.overlay-nav::after {
  content: '';
  position: absolute;
  bottom: -3px;
  left: 0;
  width: 0;
  height: 1.5px;
  background: currentColor;
  transition: width 0.3s ease;
}
.overlay-nav:hover::after {
  width: 100%;
}
</style>
