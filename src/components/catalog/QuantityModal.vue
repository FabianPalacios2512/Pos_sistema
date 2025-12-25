<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="show" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[150] p-4" @click.self="$emit('close')">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl max-w-md w-full animate-scale-in">
          <!-- Header -->
          <div class="bg-white dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700 px-6 py-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Ingresa la Cantidad</h3>
                <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">{{ product?.name }}</p>
              </div>
              <button @click="$emit('close')" 
                      class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Body -->
          <div class="p-6 space-y-4">
            <!-- Toggle de unidad (solo para kg/g) -->
            <div v-if="product?.measurement_unit === 'kg' || product?.unit === 'kg'" 
                 class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-700 rounded-lg">
              <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">
                Ingresar en g
              </span>
              <button 
                @click="useGrams = !useGrams"
                :class="[
                  'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                  useGrams ? 'bg-blue-600' : 'bg-gray-300 dark:bg-zinc-600'
                ]">
                <span :class="[
                  'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                  useGrams ? 'translate-x-6' : 'translate-x-1'
                ]" />
              </button>
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                Cantidad en {{ useGrams ? 'g' : (product?.measurement_unit === 'kg' ? 'kg' : product?.unit || 'kg') }}
              </label>
              <div class="relative">
                <input 
                  v-model="quantity" 
                  type="number" 
                  step="0.01"
                  min="0.01"
                  @keyup.enter="handleConfirm"
                  class="w-full px-4 py-3 border-2 border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-semibold"
                  :placeholder="useGrams ? 'Ej: 500, 1000, 2000' : 'Ej: 0.5, 1.25, 2.75'"
                  autofocus>
                <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-sm font-semibold text-gray-500 dark:text-zinc-400">
                  {{ useGrams ? 'g' : (product?.measurement_unit === 'kg' ? 'kg' : product?.unit || 'kg') }}
                </span>
              </div>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-2">
                Stock disponible: {{ product?.stock }} {{ product?.measurement_unit === 'kg' ? 'kg' : product?.unit || 'kg' }}
              </p>
            </div>

            <!-- Botones rápidos -->
            <div class="grid grid-cols-4 gap-2">
              <button 
                v-for="quick in quickButtons"
                :key="quick"
                @click="quantity = quick"
                class="px-4 py-2 bg-gray-100 dark:bg-zinc-700 hover:bg-gray-200 dark:hover:bg-zinc-600 text-gray-700 dark:text-zinc-300 rounded-lg font-semibold transition-colors">
                {{ quick }}
              </button>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 rounded-b-2xl flex gap-3">
            <button 
              @click="$emit('close')"
              class="flex-1 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-bold rounded-xl border border-gray-300 dark:border-zinc-600 transition-colors">
              Cancelar
            </button>
            <button 
              @click="handleConfirm"
              :disabled="!quantity || quantity <= 0"
              class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-gray-400 disabled:to-gray-400 text-white font-bold rounded-xl transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
              Agregar al Carrito
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  show: Boolean,
  product: Object
})

const emit = defineEmits(['close', 'confirm'])

const quantity = ref('')
const useGrams = ref(false)

const quickButtons = computed(() => {
  if (useGrams.value) {
    return [250, 500, 1000, 2000]
  }
  return [0.25, 0.5, 1, 2]
})

const handleConfirm = () => {
  if (!quantity.value || quantity.value <= 0) return
  
  let finalQuantity = parseFloat(quantity.value)
  
  // Convertir gramos a kg si es necesario
  const isKg = props.product?.measurement_unit === 'kg' || props.product?.unit === 'kg'
  if (useGrams.value && isKg) {
    finalQuantity = finalQuantity / 1000
  }
  
  emit('confirm', {
    product: props.product,
    quantity: finalQuantity
  })
  
  // Reset
  quantity.value = ''
  useGrams.value = false
}

// Reset cuando se abre/cierra
watch(() => props.show, (newVal) => {
  if (newVal) {
    quantity.value = ''
    useGrams.value = false
  }
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.animate-scale-in {
  animation: scaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes scaleIn {
  0% {
    transform: scale(0.9);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}
</style>
