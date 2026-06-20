<template>
  <!-- App-like Bottom Navigation Bar — Mobile Only -->
  <nav
    class="fixed bottom-0 left-0 right-0 z-[70] lg:hidden"
    :style="{
      backgroundColor: bgColor,
      borderTop: `1px solid ${borderColor}`,
      backdropFilter: 'blur(20px) saturate(180%)',
      WebkitBackdropFilter: 'blur(20px) saturate(180%)',
    }"
  >
    <div class="flex items-center justify-around h-[58px] px-1 safe-area-bottom">
      <!-- Inicio -->
      <button
        @click="$emit('navigate', 'home')"
        class="flex flex-col items-center justify-center gap-0.5 w-16 h-full transition-colors duration-200"
        :class="active === 'home' ? 'opacity-100' : 'opacity-50'"
      >
        <svg class="w-[22px] h-[22px]" :style="{ color: active === 'home' ? accentColor : textColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
        </svg>
        <span class="text-[9px] font-semibold tracking-wide" :style="{ color: active === 'home' ? accentColor : textColor }">Inicio</span>
      </button>

      <!-- Búsqueda -->
      <button
        @click="$emit('navigate', 'search')"
        class="flex flex-col items-center justify-center gap-0.5 w-16 h-full transition-colors duration-200"
        :class="active === 'search' ? 'opacity-100' : 'opacity-50'"
      >
        <svg class="w-[22px] h-[22px]" :style="{ color: active === 'search' ? accentColor : textColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <span class="text-[9px] font-semibold tracking-wide" :style="{ color: active === 'search' ? accentColor : textColor }">Búsqueda</span>
      </button>

      <!-- Colecciones -->
      <button
        @click="$emit('navigate', 'collections')"
        class="flex flex-col items-center justify-center gap-0.5 w-16 h-full transition-colors duration-200"
        :class="active === 'collections' ? 'opacity-100' : 'opacity-50'"
      >
        <svg class="w-[22px] h-[22px]" :style="{ color: active === 'collections' ? accentColor : textColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
        </svg>
        <span class="text-[9px] font-semibold tracking-wide" :style="{ color: active === 'collections' ? accentColor : textColor }">Colecciones</span>
      </button>

      <!-- Cuenta -->
      <button
        @click="$emit('navigate', 'account')"
        class="flex flex-col items-center justify-center gap-0.5 w-16 h-full transition-colors duration-200"
        :class="active === 'account' ? 'opacity-100' : 'opacity-50'"
      >
        <svg class="w-[22px] h-[22px]" :style="{ color: active === 'account' ? accentColor : textColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
        </svg>
        <span class="text-[9px] font-semibold tracking-wide" :style="{ color: active === 'account' ? accentColor : textColor }">Cuenta</span>
      </button>

      <!-- Carrito -->
      <button
        @click="$emit('navigate', 'cart')"
        class="relative flex flex-col items-center justify-center gap-0.5 w-16 h-full transition-colors duration-200"
        :class="active === 'cart' ? 'opacity-100' : 'opacity-50'"
      >
        <div class="relative">
          <svg class="w-[22px] h-[22px]" :style="{ color: active === 'cart' ? accentColor : textColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
          </svg>
          <!-- Badge count -->
          <span
            v-if="cartCount > 0"
            class="absolute -top-1.5 -right-2 min-w-[16px] h-[16px] rounded-full flex items-center justify-center text-[8px] font-bold text-white px-1"
            :style="{ backgroundColor: accentColor }"
          >
            {{ cartCount > 99 ? '99+' : cartCount }}
          </span>
        </div>
        <span class="text-[9px] font-semibold tracking-wide" :style="{ color: active === 'cart' ? accentColor : textColor }">Carrito</span>
      </button>
    </div>

    <!-- iOS safe area spacer -->
    <div class="h-safe-area-bottom" :style="{ backgroundColor: bgColor }"></div>
  </nav>
</template>

<script setup>
defineProps({
  active: {
    type: String,
    default: 'home'
  },
  cartCount: {
    type: Number,
    default: 0
  },
  accentColor: {
    type: String,
    default: '#000000'
  },
  textColor: {
    type: String,
    default: '#374151'
  },
  bgColor: {
    type: String,
    default: 'rgba(255,255,255,0.92)'
  },
  borderColor: {
    type: String,
    default: 'rgba(0,0,0,0.06)'
  }
})

defineEmits(['navigate'])
</script>

<style scoped>
.safe-area-bottom {
  padding-bottom: env(safe-area-inset-bottom, 0px);
}
.h-safe-area-bottom {
  height: env(safe-area-inset-bottom, 0px);
}
</style>
