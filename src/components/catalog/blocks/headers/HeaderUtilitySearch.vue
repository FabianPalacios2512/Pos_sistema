<template>
  <!-- HEADER UTILITY SEARCH — Ferreterías / Catálogos Grandes -->
  <!-- Dos filas: fila 1 logo+bolsa, fila 2 barra de búsqueda full-width. -->
  <header
    class="w-full bg-white border-b border-gray-200"
    :style="{ backgroundColor: palette.background, fontFamily: fonts.body + ', sans-serif' }"
  >
    <!-- Fila 1: Logo izquierda + Hamburguesa + Bolsa derecha -->
    <div class="flex items-center justify-between h-12 px-4">

      <!-- Izquierda: Hamburguesa + Logo -->
      <div class="flex items-center gap-2">
        <button
          @click="$emit('menu')"
          class="flex items-center justify-center w-9 h-9 -ml-1.5 transition-colors hover:text-black"
          :style="{ color: palette.text_dark }"
          aria-label="Abrir menú"
        >
          <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>

        <img
          v-if="logoUrl"
          :src="logoUrl"
          :alt="storeName"
          class="h-7 max-w-[120px] object-contain"
        />
        <span
          v-else
          class="text-sm font-bold leading-none tracking-tight"
          :style="{ fontFamily: fonts.body + ', Arial Black, sans-serif', color: palette.text_dark }"
        >{{ storeName }}</span>
      </div>

      <!-- Derecha: Solo Bolsa en fila 1 -->
      <button
        @click="$emit('cart')"
        class="relative flex items-center justify-center w-9 h-9 -mr-1.5 transition-colors hover:text-black"
        :style="{ color: palette.text_dark }"
        aria-label="Ver bolsa"
      >
        <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
        </svg>
        <span
          v-if="cartCount > 0"
          class="absolute top-1 right-1 w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-bold text-white leading-none"
          :style="{ backgroundColor: palette.primary || '#111827' }"
        >{{ cartCount > 9 ? '9+' : cartCount }}</span>
      </button>
    </div>

    <!-- Fila 2: Barra de búsqueda full-width -->
    <div class="px-3 pb-2.5">
      <div class="relative flex items-center">
        <!-- Ícono lupa interior -->
        <svg
          class="absolute left-3 w-4 h-4 pointer-events-none"
          :style="{ color: palette.text_dark + '66' }"
          fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>

        <input
          type="search"
          :placeholder="searchPlaceholder"
          :value="modelValue"
          @input="$emit('update:modelValue', $event.target.value)"
          @focus="$emit('search-focus')"
          class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 outline-none transition-all duration-200 focus:border-gray-400 focus:bg-white placeholder-gray-400"
          :style="{ fontFamily: fonts.body + ', sans-serif', color: palette.text_dark }"
        />
      </div>
    </div>
  </header>
</template>

<script setup>
defineProps({
  storeName:         { type: String, default: 'Mi Tienda' },
  logoUrl:           { type: String, default: '' },
  cartCount:         { type: Number, default: 0 },
  modelValue:        { type: String, default: '' },
  searchPlaceholder: { type: String, default: 'Buscar productos...' },
  palette:           { type: Object, default: () => ({ primary: '#111827', background: '#ffffff', text_dark: '#111827' }) },
  fonts:             { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
})

defineEmits(['menu', 'cart', 'search-focus', 'update:modelValue'])
</script>
