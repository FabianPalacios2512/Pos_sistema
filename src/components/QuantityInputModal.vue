<template>
  <!-- Overlay oscuro -->
  <Transition name="modal">
    <div v-if="isOpen" 
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm"
         @click.self="close">
      
      <!-- Modal Container -->
      <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl shadow-2xl border border-slate-700 w-full max-w-md mx-4 transform transition-all"
           @click.stop>
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-white">Ingrese Cantidad</h3>
                <p class="text-xs text-blue-100">{{ product?.name }}</p>
              </div>
            </div>
            <button @click="close" 
                    class="text-white/70 hover:text-white hover:bg-white/10 rounded-lg p-2 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-5">
          
          <!-- Información del Producto -->
          <div class="bg-slate-700/50 rounded-xl p-4 border border-slate-600">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-slate-300">Precio por {{ product?.unit_name || 'Unidad' }}</span>
              <span class="text-2xl font-black text-emerald-400">
                ${{ formatCurrency(product?.price || 0) }}
              </span>
            </div>
            <div class="flex items-center justify-between text-xs">
              <span class="text-slate-400">Unidad de Medida</span>
              <span class="text-slate-200 font-semibold">{{ product?.unit_abbreviation || 'und' }}</span>
            </div>
          </div>

          <!-- Input de Cantidad -->
          <div class="space-y-3">
            <label class="block text-sm font-semibold text-slate-300">
              Cantidad ({{ product?.unit_abbreviation || 'und' }})
            </label>
            
            <!-- Input Grande Numérico -->
            <div class="relative">
              <input 
                ref="quantityInput"
                v-model.number="quantity"
                type="number"
                :step="product?.quantity_step || '0.001'"
                :min="product?.allow_decimal ? '0.001' : '1'"
                class="w-full px-6 py-5 bg-slate-900 border-2 border-slate-600 rounded-xl text-4xl font-black text-center text-white placeholder-slate-500 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all"
                placeholder="0.00"
                @keydown.enter="confirm"
                @keydown.esc="close"
              />
              <div class="absolute right-4 top-1/2 -translate-y-1/2 text-xl font-bold text-slate-400">
                {{ product?.unit_abbreviation || 'und' }}
              </div>
            </div>

            <!-- Ayuda visual -->
            <div class="flex items-center space-x-2 text-xs text-slate-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span v-if="product?.allow_decimal">
                Puede usar decimales (Ej: 0.5, 1.25, 2.750)
              </span>
              <span v-else>
                Solo números enteros (Ej: 1, 2, 3)
              </span>
            </div>
          </div>

          <!-- Cálculo del Total (Preview) -->
          <div v-if="quantity > 0" 
               class="bg-gradient-to-r from-emerald-500/10 to-teal-500/10 border border-emerald-500/30 rounded-xl p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-slate-300">Total a Cobrar</span>
              </div>
              <span class="text-2xl font-black text-emerald-400">
                ${{ formatCurrency(calculateTotal) }}
              </span>
            </div>
          </div>

          <!-- Teclado Numérico Rápido (Opcional) -->
          <div class="grid grid-cols-3 gap-2">
            <button v-for="num in [0.25, 0.5, 0.75, 1, 1.5, 2, 2.5, 5, 10]" 
                    :key="num"
                    @click="quantity = num"
                    class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition-colors">
              {{ num }}
            </button>
          </div>

        </div>

        <!-- Footer -->
        <div class="bg-slate-800/50 px-6 py-4 rounded-b-2xl flex items-center justify-between border-t border-slate-700">
          <button @click="close" 
                  class="px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-xl transition-all">
            Cancelar
          </button>
          <button @click="confirm" 
                  :disabled="!isValidQuantity"
                  :class="[
                    'px-6 py-2.5 font-bold rounded-xl transition-all shadow-lg',
                    isValidQuantity 
                      ? 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white transform hover:scale-105' 
                      : 'bg-slate-600 text-slate-400 cursor-not-allowed'
                  ]">
            <span class="flex items-center space-x-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span>Agregar al Carrito</span>
            </span>
          </button>
        </div>

      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  product: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['close', 'confirm'])

// Refs
const quantity = ref(1)
const quantityInput = ref(null)

// Computed
const calculateTotal = computed(() => {
  if (!props.product || !quantity.value) return 0
  
  // Cálculo sin errores de redondeo
  const priceInCents = Math.round((props.product.price || 0) * 100)
  const quantityInMillis = Math.round(quantity.value * 1000)
  const totalInCents = (priceInCents * quantityInMillis) / 1000
  
  return Math.round(totalInCents) / 100
})

const isValidQuantity = computed(() => {
  if (!quantity.value || quantity.value <= 0) return false
  
  // Si no permite decimales, validar que sea entero
  if (props.product && !props.product.allow_decimal) {
    return quantity.value === Math.floor(quantity.value)
  }
  
  return true
})

// Methods
const formatCurrency = (value) => {
  return value.toLocaleString('es-CO', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  })
}

const close = () => {
  quantity.value = 1
  emit('close')
}

const confirm = () => {
  if (!isValidQuantity.value) return
  
  emit('confirm', {
    product: props.product,
    quantity: quantity.value,
    total: calculateTotal.value
  })
  
  quantity.value = 1
  emit('close')
}

// Watchers
watch(() => props.isOpen, async (newVal) => {
  if (newVal) {
    // Resetear cantidad
    quantity.value = props.product?.allow_decimal ? 0 : 1
    
    // Enfocar el input después de que el modal se abra
    await nextTick()
    quantityInput.value?.focus()
    quantityInput.value?.select()
  }
})

// Validar cantidad mientras escribe
watch(quantity, (newVal) => {
  if (props.product && !props.product.allow_decimal && newVal) {
    // Forzar entero si no permite decimales
    quantity.value = Math.floor(newVal)
  }
})
</script>

<style scoped>
/* Transiciones suaves para el modal */
.modal-enter-active {
  transition: all 0.3s ease-out;
}

.modal-leave-active {
  transition: all 0.2s ease-in;
}

.modal-enter-from {
  opacity: 0;
}

.modal-enter-from > div {
  transform: scale(0.95);
  opacity: 0;
}

.modal-leave-to {
  opacity: 0;
}

.modal-leave-to > div {
  transform: scale(0.95);
  opacity: 0;
}

/* Remover arrows del input number */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}
</style>
