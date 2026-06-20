<template>
  <!-- HEADER RETAIL LEFT — Consumo Masivo / Minimarkets -->
  <!-- Logo izquierda, iconos derecha. Borde inferior limpio y funcional. -->
  <!-- DESKTOP: Búsqueda integrada center, nav links, sin hamburguesa -->
  <header
    class="w-full bg-white border-b border-gray-200"
    :style="{ backgroundColor: palette.background, fontFamily: fonts.body + ', sans-serif' }"
  >
    <div class="flex items-center justify-between h-14 lg:h-[76px] px-4 lg:px-8 max-w-[1440px] mx-auto">

      <!-- Izquierda: Hamburguesa (móvil) + Logo -->
      <div class="flex items-center gap-2 lg:gap-4">
        <button
          @click="$emit('menu')"
          class="lg:hidden flex items-center justify-center w-10 h-10 -ml-2 transition-colors hover:text-black"
          :style="{ color: palette.text_dark }"
          aria-label="Abrir menú"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>

        <!-- Logo -->
        <div class="flex items-center">
          <img
            v-if="logoUrl"
            :src="logoUrl"
            :alt="storeName"
            class="h-8 lg:h-10 max-w-[130px] lg:max-w-[180px] object-contain"
          />
          <span
            v-else
            class="text-base lg:text-xl font-bold tracking-tight leading-none"
            :style="{ fontFamily: fonts.body + ', Arial Black, sans-serif', color: palette.text_dark }"
          >{{ storeName }}</span>
        </div>

        <!-- Desktop: Nav links inline -->
        <nav class="hidden lg:flex items-center gap-6 ml-6 pl-6 border-l"
          :style="{ borderColor: palette.text_dark + '15' }"
        >
          <span
            v-for="link in navLinks"
            :key="link"
            class="text-[11px] lg:text-[13px] uppercase tracking-[0.1em] font-semibold cursor-pointer transition-colors duration-200"
            :style="{ color: palette.text_dark + '65' }"
            @mouseenter="e => e.target.style.color = palette.text_dark"
            @mouseleave="e => e.target.style.color = palette.text_dark + '65'"
          >{{ link }}</span>
        </nav>
      </div>

      <!-- Centro: Búsqueda (solo desktop) -->
      <div class="hidden lg:flex flex-1 max-w-md mx-8">
        <div class="relative w-full">
          <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: palette.text_dark + '44' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
          <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            placeholder="¿Qué estás buscando?"
            class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border bg-gray-50 outline-none focus:bg-white focus:border-gray-400 transition-all placeholder-gray-400"
            :style="{ fontFamily: fonts.body + ', sans-serif', color: palette.text_dark, borderColor: palette.text_dark + '15' }"
          />
        </div>
      </div>

      <!-- Derecha: Búsqueda (móvil) + Cuenta (desktop) + Bolsa -->
      <div class="flex items-center gap-1 lg:gap-2 -mr-2 lg:mr-0">
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

        <!-- Bolsa con badge -->
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
            :style="{ backgroundColor: palette.primary || '#111827' }"
          >{{ cartCount > 9 ? '9+' : cartCount }}</span>
        </button>
      </div>

    </div>

    <!-- Barra de búsqueda expandible — estilo retail directo (solo móvil) -->
    <Transition name="search-slide">
      <div v-if="showSearch" class="lg:hidden px-4 pb-3" :style="{ backgroundColor: palette.background }">
        <div class="relative flex items-center">
          <svg class="absolute left-3 w-4 h-4 pointer-events-none" :style="{ color: palette.text_dark + '55' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
          <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            placeholder="¿Qué estás buscando?"
            autofocus
            class="w-full pl-9 pr-9 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 outline-none focus:border-gray-400 focus:bg-white transition-all"
            :style="{ fontFamily: fonts.body + ', sans-serif', color: palette.text_dark }"
          />
          <button @click="showSearch = false" class="absolute right-2.5 text-gray-400 hover:text-gray-700">
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
  storeName:   { type: String, default: 'Mi Tienda' },
  logoUrl:     { type: String, default: '' },
  cartCount:   { type: Number, default: 0 },
  modelValue:  { type: String, default: '' },
  palette:     { type: Object, default: () => ({ primary: '#111827', background: '#ffffff', text_dark: '#111827' }) },
  fonts:       { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
})

defineEmits(['menu', 'cart', 'update:modelValue'])

const showSearch = ref(false)

const navLinks = ['Productos', 'Categorías', 'Ofertas', 'Contacto']
</script>

<style scoped>
.search-slide-enter-active, .search-slide-leave-active { transition: all 0.2s ease; }
.search-slide-enter-from, .search-slide-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
