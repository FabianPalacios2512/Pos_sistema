<template>
  <!-- HEADER EDITORIAL CENTER — Alta Moda / Boutique -->
  <!-- Logo centrado, hamburger izquierda, iconos derecha. Línea inferior ultra sutil. -->
  <!-- DESKTOP: Nav links centrados debajo, búsqueda underline, sin hamburguesa -->
  <header
    class="w-full bg-white border-b border-gray-100"
    :style="{ backgroundColor: palette.background, fontFamily: fonts.body + ', sans-serif' }"
  >
    <!-- Fila principal -->
    <div class="relative flex items-center justify-between h-14 lg:h-[82px] px-4 lg:px-8 max-w-[1440px] mx-auto">

      <!-- Izquierda: Menú hamburguesa (solo móvil) -->
      <div class="w-10 lg:w-[180px] flex items-center">
        <button
          @click="$emit('menu')"
          class="lg:hidden flex items-center justify-center w-10 h-10 -ml-2 text-gray-800 hover:text-black transition-colors"
          :style="{ color: palette.text_dark }"
          aria-label="Abrir menú"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>

        <!-- Desktop: Búsqueda con label -->
        <button
          @click="showSearch = !showSearch"
          class="hidden lg:flex items-center gap-2.5 text-[13px] tracking-[0.12em] uppercase font-medium transition-all hover:opacity-60"
          :style="{ color: palette.text_dark + 'aa' }"
        >
          <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
          Buscar
        </button>
      </div>

      <!-- Centro: Logo (imagen o nombre en serif) -->
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center">
        <img
          v-if="logoUrl"
          :src="logoUrl"
          :alt="storeName"
          class="h-8 lg:h-12 max-w-[140px] lg:max-w-[240px] object-contain"
        />
        <span
          v-else
          class="text-base lg:text-2xl tracking-[0.25em] lg:tracking-[0.3em] uppercase font-semibold leading-none"
          :style="{ fontFamily: fonts.heading + ', Georgia, serif', color: palette.text_dark }"
        >{{ storeName }}</span>
      </div>

      <!-- Derecha: Búsqueda (móvil) + Bolsa -->
      <div class="flex items-center gap-1 lg:gap-3 -mr-2 lg:mr-0 w-10 lg:w-[180px] justify-end">
        <!-- Búsqueda: toggle barra interna (solo móvil) -->
        <button
          @click="showSearch = !showSearch"
          class="lg:hidden flex items-center justify-center w-10 h-10 transition-colors hover:text-black"
          :style="{ color: palette.text_dark }"
          aria-label="Buscar"
        >
          <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
        </button>

        <!-- Cuenta (solo desktop) -->
        <button
          class="hidden lg:flex items-center justify-center w-10 h-10 transition-colors hover:opacity-60"
          :style="{ color: palette.text_dark }"
          aria-label="Mi cuenta"
        >
          <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
          </svg>
        </button>

        <!-- Bolsa de compras -->
        <button
          @click="$emit('cart')"
          class="relative flex items-center justify-center w-10 h-10 transition-colors hover:text-black lg:hover:opacity-60"
          :style="{ color: palette.text_dark }"
          aria-label="Ver bolsa"
        >
          <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
          </svg>
          <span
            v-if="cartCount > 0"
            class="absolute top-1.5 right-1.5 w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-bold text-white leading-none"
            :style="{ backgroundColor: palette.primary || palette.text_dark }"
          >{{ cartCount > 9 ? '9+' : cartCount }}</span>
        </button>
      </div>

    </div>

    <!-- Desktop: Barra de navegación centrada con links -->
    <nav class="hidden lg:flex items-center justify-center gap-10 border-t py-3.5 max-w-[1440px] mx-auto"
      :style="{ borderColor: palette.text_dark + '0a' }"
    >
      <span
        v-for="link in navLinks"
        :key="link"
        class="text-[13px] uppercase tracking-[0.16em] font-semibold cursor-pointer transition-all duration-200 hover:opacity-100 relative nav-link-hover"
        :style="{ color: palette.text_dark + '70', fontFamily: fonts.body + ', sans-serif' }"
        @mouseenter="e => e.target.style.color = palette.text_dark"
        @mouseleave="e => e.target.style.color = palette.text_dark + '70'"
      >{{ link }}</span>
    </nav>

    <!-- Barra de búsqueda expandible — estilo editorial sutil -->
    <Transition name="search-slide">
      <div v-if="showSearch" class="px-4 lg:px-8 pb-3 border-b border-gray-100" :style="{ backgroundColor: palette.background }">
        <div class="relative flex items-center max-w-lg mx-auto">
          <svg class="absolute left-3 lg:left-0 w-4 h-4 pointer-events-none" :style="{ color: palette.text_dark + '66' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
          <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            placeholder="Buscar en la colección..."
            autofocus
            class="w-full pl-9 lg:pl-7 pr-9 py-2 lg:py-3 text-sm lg:text-base border-b-2 border-gray-200 bg-transparent outline-none focus:border-gray-900 transition-colors"
            :style="{ fontFamily: fonts.body + ', sans-serif', color: palette.text_dark }"
          />
          <button @click="showSearch = false" class="absolute right-1 text-gray-400 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
      </div>
    </Transition>

  </header>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  storeName:   { type: String, default: 'BOUTIQUE' },
  logoUrl:     { type: String, default: '' },
  cartCount:   { type: Number, default: 0 },
  modelValue:  { type: String, default: '' },
  palette:     { type: Object, default: () => ({ primary: '#111827', background: '#ffffff', text_dark: '#111827' }) },
  fonts:       { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
})

defineEmits(['menu', 'cart', 'update:modelValue'])

const showSearch = ref(false)

const navLinks = ['Catálogo', 'Novedades', 'Colecciones', 'Contacto']
</script>

<style scoped>
.search-slide-enter-active, .search-slide-leave-active { transition: all 0.2s ease; }
.search-slide-enter-from, .search-slide-leave-to { opacity: 0; transform: translateY(-6px); }
.nav-link-hover::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 1px;
  background: currentColor;
  transition: width 0.3s ease;
}
.nav-link-hover:hover::after {
  width: 100%;
}
</style>
