<template>
  <Transition
    enter-active-class="transition-all duration-300 cubic-bezier(0.4, 0, 0.2, 1)"
    enter-from-class="opacity-0 translate-y-10 scale-90"
    enter-to-class="opacity-100 translate-y-0 scale-100"
    leave-active-class="transition-all duration-300 cubic-bezier(0.4, 0, 0.2, 1)"
    leave-from-class="opacity-100 translate-y-0 scale-100"
    leave-to-class="opacity-0 translate-y-10 scale-90"
  >
    <div 
      v-if="cartItemCount > 0"
      class="fixed bottom-6 right-6 z-50"
    >
      <button 
        @click="$emit('open-checkout')"
        class="group relative flex items-center justify-center w-16 h-16 bg-lime-500 hover:bg-lime-400 text-slate-900 rounded-full shadow-2xl shadow-lime-500/40 transition-all duration-300 hover:scale-110 active:scale-95"
      >
        <!-- Icono Bolsa -->
        <svg class="w-8 h-8 transition-transform group-hover:-rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>

        <!-- Badge Contador -->
        <span class="absolute -top-1 -right-1 w-6 h-6 bg-slate-900 text-white text-xs font-bold rounded-full flex items-center justify-center border-2 border-white shadow-sm animate-bounce-small">
          {{ cartItemCount }}
        </span>
      </button>
      
      <!-- Label (Opcional, aparece al inicio) -->
      <div class="absolute bottom-full right-0 mb-2 whitespace-nowrap px-3 py-1 bg-slate-900 text-white text-xs font-bold rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
        Ver Carrito (${{ formatPrice(cartTotal) }})
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  cart: {
    type: Array,
    required: true
  }
})

defineEmits(['open-checkout'])

const cartItemCount = computed(() => {
  return props.cart.reduce((sum, item) => sum + item.quantity, 0)
})

const cartTotal = computed(() => {
  return props.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(price)
}
</script>

<style scoped>
@keyframes bounce-small {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-3px); }
}
.animate-bounce-small {
  animation: bounce-small 2s infinite;
}
</style>
