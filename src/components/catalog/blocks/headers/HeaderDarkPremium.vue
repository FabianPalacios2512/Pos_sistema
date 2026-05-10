<template>
  <!-- HEADER DARK PREMIUM — Alta Costura / Tecnología / Luxury Black -->
  <!-- Fondo negro sólido con glass oscuro al scroll. Íconos y texto siempre en blanco. -->
  <header
    class="w-full border-b border-white/5 transition-all duration-300"
    :class="scrolled ? 'bg-black/90 backdrop-blur-md' : 'bg-black'"
  >
    <div class="flex items-center justify-between h-14 px-5">

      <!-- Izquierda: Hamburguesa + Logo -->
      <div class="flex items-center gap-3">
        <button
          @click="$emit('menu')"
          class="flex items-center justify-center w-9 h-9 -ml-1.5 text-white/70 hover:text-white transition-colors"
          aria-label="Abrir menú"
        >
          <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>

        <!-- Logo -->
        <div class="flex items-center">
          <img
            v-if="logoUrl"
            :src="logoUrl"
            :alt="storeName"
            class="h-7 max-w-[120px] object-contain brightness-0 invert"
          />
          <span
            v-else
            class="text-[13px] font-bold tracking-[0.18em] uppercase text-white leading-none"
            :style="{ fontFamily: fonts.body + ', Arial Black, sans-serif' }"
          >{{ storeName }}</span>
        </div>
      </div>

      <!-- Derecha: Búsqueda + Bolsa -->
      <div class="flex items-center gap-0.5 -mr-1.5">
        <!-- Búsqueda -->
        <button
          @click="showSearch = !showSearch"
          class="flex items-center justify-center w-10 h-10 text-white/60 hover:text-white transition-colors"
          aria-label="Buscar"
        >
          <svg class="w-[17px] h-[17px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
        </button>

        <!-- Bolsa con badge -->
        <button
          @click="$emit('cart')"
          class="relative flex items-center justify-center w-10 h-10 text-white/60 hover:text-white transition-colors"
          aria-label="Ver bolsa"
        >
          <svg class="w-[17px] h-[17px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
          </svg>
          <span
            v-if="cartCount > 0"
            class="absolute top-1.5 right-1.5 w-[15px] h-[15px] rounded-full flex items-center justify-center text-[8px] font-bold text-black leading-none"
            :style="{ backgroundColor: palette.primary || '#ffffff' }"
          >{{ cartCount > 9 ? '9+' : cartCount }}</span>
        </button>
      </div>

    </div>

    <!-- Barra de búsqueda expandible dark -->
    <Transition name="search-slide">
      <div v-if="showSearch" class="px-5 pb-3 bg-black">
        <div class="relative flex items-center">
          <svg class="absolute left-3 w-4 h-4 pointer-events-none text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
          <input
            v-model="internalSearch"
            type="search"
            placeholder="Buscar productos..."
            class="w-full pl-9 pr-4 py-2.5 bg-white/5 border border-white/10 text-white placeholder-white/30 text-[13px] outline-none focus:border-white/25 transition-colors"
            @input="$emit('update:modelValue', internalSearch)"
            @keydown.enter="showSearch = false"
          />
        </div>
      </div>
    </Transition>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  storeName:  { type: String, default: '' },
  logoUrl:    { type: String, default: '' },
  cartCount:  { type: Number, default: 0 },
  modelValue: { type: String, default: '' },
  palette:    { type: Object, default: () => ({ primary: '#ffffff', text_dark: '#111827', background: '#000000' }) },
  fonts:      { type: Object, default: () => ({ heading: 'Montserrat', body: 'Montserrat' }) },
})

defineEmits(['menu', 'cart', 'update:modelValue'])

const showSearch   = ref(false)
const internalSearch = ref(props.modelValue)
const scrolled     = ref(false)

function onScroll() {
  scrolled.value = window.scrollY > 10
}

onMounted(()  => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<style scoped>
.search-slide-enter-active,
.search-slide-leave-active {
  transition: max-height 0.22s ease, opacity 0.18s ease;
  overflow: hidden;
}
.search-slide-enter-from,
.search-slide-leave-to {
  max-height: 0;
  opacity: 0;
}
.search-slide-enter-to,
.search-slide-leave-from {
  max-height: 80px;
  opacity: 1;
}
</style>
