<template>
  <!-- HEADER FLOATING PILL — Streetwear / Tecnología / Urbano -->
  <!-- Píldora flotante que no toca los bordes. Sombra fuerte, logo izquierda, iconos derecha. -->
  <div class="fixed top-8 left-0 right-0 z-50 pointer-events-none">
    <div class="pt-3 px-4">
      <header
        class="pointer-events-auto flex items-center justify-between h-12 px-4 rounded-full shadow-lg"
        :style="{
          backgroundColor: palette.background || '#ffffff',
          fontFamily: fonts.body + ', sans-serif',
          boxShadow: '0 4px 24px rgba(0,0,0,0.14), 0 1px 4px rgba(0,0,0,0.08)'
        }"
      >

        <!-- Izquierda: Hamburguesa + Logo -->
        <div class="flex items-center gap-2.5">
          <button
            @click="$emit('menu')"
            class="flex items-center justify-center w-8 h-8 transition-colors hover:opacity-70"
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
            class="h-7 max-w-[110px] object-contain"
          />
          <span
            v-else
            class="text-sm font-bold tracking-widest uppercase leading-none"
            :style="{ fontFamily: fonts.body + ', Arial Black, sans-serif', color: palette.text_dark }"
          >{{ storeName }}</span>
        </div>

        <!-- Derecha: Búsqueda + Bolsa -->
        <div class="flex items-center gap-0.5">
          <button
            @click="showSearch = !showSearch"
            class="flex items-center justify-center w-8 h-8 transition-colors hover:opacity-70"
            :style="{ color: palette.text_dark }"
            aria-label="Buscar"
          >
            <svg class="w-[16px] h-[16px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
          </button>

          <button
            @click="$emit('cart')"
            class="relative flex items-center justify-center w-8 h-8 transition-colors hover:opacity-70"
            :style="{ color: palette.text_dark }"
            aria-label="Ver bolsa"
          >
            <svg class="w-[16px] h-[16px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
            </svg>
            <span
              v-if="cartCount > 0"
              class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 rounded-full flex items-center justify-center text-[8px] font-bold text-white leading-none"
              :style="{ backgroundColor: palette.primary || '#111827' }"
            >{{ cartCount > 9 ? '9+' : cartCount }}</span>
          </button>
        </div>

      </header>

      <!-- Barra de búsqueda expandible — debajo de la píldora, misma sombra -->
      <Transition name="pill-search">
        <div
          v-if="showSearch"
          class="pointer-events-auto mt-2 mx-1 rounded-2xl px-3 py-2.5"
          :style="{
            backgroundColor: palette.background || '#ffffff',
            boxShadow: '0 4px 24px rgba(0,0,0,0.14), 0 1px 4px rgba(0,0,0,0.08)'
          }"
        >
          <div class="relative flex items-center">
            <svg class="absolute left-3 w-4 h-4 pointer-events-none" :style="{ color: palette.text_dark + '55' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input
              :value="modelValue"
              @input="$emit('update:modelValue', $event.target.value)"
              placeholder="Buscar..."
              autofocus
              class="w-full pl-9 pr-8 py-1.5 text-sm bg-gray-100 rounded-xl outline-none focus:bg-gray-200 transition-colors"
              :style="{ fontFamily: fonts.body + ', sans-serif', color: palette.text_dark }"
            />
            <button @click="showSearch = false" class="absolute right-2 text-gray-400 hover:text-gray-700">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </div>
  <!-- Espaciador para empujar el contenido debajo de la píldora flotante -->
  <div class="h-[72px]"></div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  storeName:   { type: String, default: 'BRAND' },
  logoUrl:     { type: String, default: '' },
  cartCount:   { type: Number, default: 0 },
  modelValue:  { type: String, default: '' },
  palette:     { type: Object, default: () => ({ primary: '#111827', background: '#ffffff', text_dark: '#111827' }) },
  fonts:       { type: Object, default: () => ({ heading: 'Playfair Display', body: 'Montserrat' }) },
})

defineEmits(['menu', 'cart', 'update:modelValue'])

const showSearch = ref(false)
</script>

<style scoped>
.pill-search-enter-active, .pill-search-leave-active { transition: all 0.2s ease; }
.pill-search-enter-from, .pill-search-leave-to { opacity: 0; transform: translateY(-8px); }
</style>
