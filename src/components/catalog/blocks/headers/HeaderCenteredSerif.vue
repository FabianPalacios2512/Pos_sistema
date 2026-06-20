<template>
  <!-- HEADER CENTERED SERIF — Boutique / Distribuidora / Kharis-inspired -->
  <!-- Nombre de marca centrado en serif, hamburger izquierda, iconos derecha. -->
  <!-- DESKTOP: Nav bar segundo nivel, búsqueda integrada, user icon -->
  <header
    class="w-full bg-white"
    :style="{
      backgroundColor: palette.background || '#ffffff',
      fontFamily: fonts.body + ', sans-serif',
      boxShadow: '0 1px 0 0 rgba(0,0,0,0.06)'
    }"
  >
    <!-- Línea decorativa top: gradiente del color primario -->
    <div
      class="h-[2px] w-full"
      :style="{ background: `linear-gradient(90deg, transparent, ${palette.primary}55, transparent)` }"
    ></div>

    <div class="relative flex items-center justify-between h-[68px] lg:h-[86px] px-4 lg:px-10 xl:px-16 max-w-[1440px] mx-auto">

      <!-- Izquierda: Hamburguesa (móvil) / Búsqueda (desktop) -->
      <div class="w-10 lg:w-[200px] flex items-center">
        <button
          @click="$emit('menu')"
          class="lg:hidden flex items-center justify-center w-10 h-10 -ml-2 transition-colors hover:opacity-60"
          :style="{ color: palette.text_dark }"
          aria-label="Abrir menú"
        >
          <svg class="w-[22px] h-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
          </svg>
        </button>

        <!-- Desktop: búsqueda inline -->
        <button
          @click="showSearch = !showSearch"
          class="hidden lg:flex items-center gap-2.5 text-[13px] tracking-[0.12em] uppercase font-medium transition-all hover:opacity-60"
          :style="{ color: palette.text_dark + 'aa' }"
        >
          <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
          Buscar
        </button>
      </div>

      <!-- Centro: Logo o nombre en serif (absolutamente centrado) -->
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center">
        <img
          v-if="logoUrl"
          :src="logoUrl"
          :alt="storeName"
          class="h-9 lg:h-12 max-w-[160px] lg:max-w-[260px] object-contain"
        />
        <template v-else>
          <span
            class="leading-none font-bold"
            :style="{
              fontFamily: fonts.heading + ', Georgia, Times New Roman, serif',
              fontSize: storeName.length > 12 ? '18px' : '22px',
              letterSpacing: '0.12em',
              color: palette.text_dark || '#111111'
            }"
            :class="storeName.length > 12 ? 'lg:!text-[28px]' : 'lg:!text-[34px]'"
          >{{ storeName.toUpperCase() }}</span>
          <span
            v-if="storeSubtitle"
            class="mt-[3px] lg:mt-[5px] text-[8px] lg:text-[9px] tracking-[0.28em] uppercase font-normal"
            :style="{ color: (palette.text_dark || '#111111') + '70', fontFamily: fonts.body + ', sans-serif' }"
          >{{ storeSubtitle }}</span>
        </template>
      </div>

      <!-- Derecha: Lupa (móvil) + Cuenta (desktop) + Bolsa -->
      <div class="w-10 lg:w-[200px] flex items-center justify-end gap-1 lg:gap-2">

        <!-- Lupa solo en móvil -->
        <button
          @click="showSearch = !showSearch"
          class="lg:hidden flex items-center justify-center w-10 h-10 transition-colors hover:opacity-60"
          :style="{ color: palette.text_dark }"
          aria-label="Buscar"
        >
          <svg class="w-[20px] h-[20px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
        </button>

        <!-- Cuenta (solo desktop) -->
        <button
          class="hidden lg:flex items-center justify-center w-10 h-10 transition-colors hover:opacity-60"
          :style="{ color: palette.text_dark }"
          aria-label="Mi cuenta"
        >
          <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
          </svg>
        </button>

        <!-- Bolsa -->
        <button
          @click="$emit('cart')"
          class="relative flex items-center justify-center w-10 h-10 transition-colors hover:opacity-60"
          :style="{ color: palette.text_dark }"
          aria-label="Ver bolsa"
        >
          <svg class="w-[20px] h-[20px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
          </svg>
          <span
            v-if="cartCount > 0"
            class="absolute -top-0.5 -right-0.5 w-[15px] h-[15px] rounded-full flex items-center justify-center text-[8px] font-bold text-white leading-none"
            :style="{ backgroundColor: palette.primary || '#111827' }"
          >{{ cartCount > 9 ? '9+' : cartCount }}</span>
        </button>
      </div>
    </div>

    <!-- Desktop: Nav de categorías centradas -->
    <nav class="hidden lg:flex items-center justify-center gap-10 border-t py-4 max-w-[1440px] mx-auto"
      :style="{ borderColor: (palette.text_dark || '#111') + '08' }"
    >
      <span
        v-for="link in navLinks"
        :key="link"
        class="text-[13px] uppercase tracking-[0.16em] font-semibold cursor-pointer transition-all duration-300 hover:opacity-100 relative serif-nav-link"
        :style="{ color: (palette.text_dark || '#111') + '60', fontFamily: fonts.body + ', sans-serif' }"
        @mouseenter="e => e.target.style.color = palette.text_dark || '#111'"
        @mouseleave="e => e.target.style.color = (palette.text_dark || '#111') + '60'"
      >{{ link }}</span>
    </nav>

    <!-- Barra de búsqueda expandible — underline editorial, sin caja -->
    <Transition name="serif-search">
      <div v-if="showSearch" class="px-4 lg:px-8 pb-4 border-t border-gray-100">
        <div class="relative flex items-center max-w-lg mx-auto mt-3">
          <svg
            class="absolute left-0 w-4 h-4 pointer-events-none"
            :style="{ color: (palette.text_dark || '#111') + '55' }"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
          <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            placeholder="Buscar en la colección..."
            autofocus
            class="w-full pl-7 pr-8 py-2 lg:py-3 text-sm lg:text-base bg-transparent outline-none border-b-2 transition-colors duration-200"
            :style="{
              fontFamily: fonts.body + ', sans-serif',
              color: palette.text_dark,
              borderColor: (palette.text_dark || '#111') + '25'
            }"
            @focus="e => e.target.style.borderColor = palette.primary || '#111'"
            @blur="e => e.target.style.borderColor = (palette.text_dark || '#111') + '25'"
          />
          <button
            @click="showSearch = false"
            class="absolute right-0 p-1 transition-opacity hover:opacity-60"
            :style="{ color: (palette.text_dark || '#111') + '80' }"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </Transition>

    <!-- Línea decorativa inferior sutil -->
    <div class="h-[1px] w-full bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
  </header>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  storeName:     { type: String, default: 'BRAND' },
  storeSubtitle: { type: String, default: '' },
  logoUrl:       { type: String, default: '' },
  cartCount:     { type: Number, default: 0 },
  modelValue:    { type: String, default: '' },
  palette:       { type: Object, default: () => ({ primary: '#c9a96e', background: '#ffffff', text_dark: '#111111' }) },
  fonts:         { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
})

defineEmits(['menu', 'cart', 'update:modelValue'])

const showSearch = ref(false)

const navLinks = ['Catálogo', 'Novedades', 'Colecciones', 'Mayoreo', 'Contacto']
</script>

<style scoped>
.serif-search-enter-active, .serif-search-leave-active {
  transition: opacity 0.22s ease, transform 0.22s ease;
}
.serif-search-enter-from, .serif-search-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
.serif-nav-link::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 1px;
  background: currentColor;
  transition: width 0.3s ease;
}
.serif-nav-link:hover::after {
  width: 100%;
}
</style>
