<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="isOpen"
        @click="closeModal"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
      >
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div 
            v-if="isOpen"
            @click.stop
            class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
          >
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 px-6 py-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-base font-bold text-gray-900">Cargar Pedido Web</h3>
                    <p class="text-xs text-gray-500">Ingresa el código que recibió el cliente</p>
                  </div>
                </div>
                <button 
                  @click="closeModal"
                  class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Content -->
            <div class="p-6">
              <div v-if="!orderLoaded">
                <!-- Input de código -->
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                      Código de Pedido
                    </label>
                    <div class="relative">
                      <input 
                        ref="codeInput"
                        v-model="orderCode"
                        @keyup.enter="searchOrder"
                        type="text"
                        placeholder="Ej: PED-123 o solo 123"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-base font-medium focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all placeholder-gray-400"
                        :disabled="isLoading"
                      >
                      <div v-if="isLoading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                        <div class="animate-spin rounded-full h-5 w-5 border-2 border-blue-600 border-t-transparent"></div>
                      </div>
                    </div>
                    <p v-if="error" class="text-red-600 text-sm mt-2 font-medium">{{ error }}</p>
                  </div>

                  <button 
                    @click="searchOrder"
                    :disabled="!orderCode.trim() || isLoading"
                    class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-200"
                  >
                    {{ isLoading ? 'Buscando...' : 'Buscar Pedido' }}
                  </button>
                </div>

                <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                  <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                      <p class="text-sm font-bold text-blue-900 mb-1">¿Cómo funciona?</p>
                      <p class="text-xs text-blue-700 leading-relaxed">
                        Los clientes realizan pedidos desde el catálogo web y reciben un código. 
                        Ingresa ese código aquí para cargar automáticamente el pedido al carrito.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

                <!-- Orden encontrada -->
              <div v-else class="space-y-4">
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                      <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <span class="font-bold text-green-800">¡Pedido Encontrado!</span>
                  </div>
                  <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between items-center">
                      <span class="text-gray-600 font-medium">Código:</span>
                      <span class="font-bold text-gray-900 bg-white px-2 py-1 rounded-lg border border-gray-200">{{ foundOrder.order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600 font-medium">Cliente:</span>
                      <span class="font-bold text-gray-900">{{ foundOrder.customer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600 font-medium">Teléfono:</span>
                      <span class="font-semibold text-gray-700">{{ foundOrder.customer_phone }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-green-200">
                      <span class="text-gray-600 font-medium">Total:</span>
                      <span class="font-bold text-green-700 text-lg">${{ formatPrice(foundOrder.total) }}</span>
                    </div>
                  </div>
                </div>                <!-- Productos -->
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                  <div class="bg-gray-50 border-b border-gray-200 px-4 py-2.5">
                    <div class="flex items-center justify-between">
                      <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wide">Productos del Pedido</h4>
                      <span class="text-xs font-semibold text-gray-500">{{ foundOrder.items.length }} items</span>
                    </div>
                  </div>
                  <div class="p-3 max-h-48 overflow-y-auto">
                    <div class="space-y-2">
                      <div 
                        v-for="item in foundOrder.items" 
                        :key="item.product_id"
                        class="flex justify-between items-center text-sm py-2 px-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                      >
                        <div class="flex items-center space-x-2">
                          <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-lg text-xs">{{ item.quantity }}x</span>
                          <span class="text-gray-700 font-medium">{{ item.product_name }}</span>
                        </div>
                        <span class="font-bold text-gray-900">${{ formatPrice(item.subtotal) }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Alertas de stock -->
                <div v-if="stockIssues.length > 0" class="bg-gradient-to-br from-amber-50 to-yellow-50 border border-amber-200 rounded-xl p-4">
                  <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                      <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <p class="text-sm font-bold text-amber-900 mb-2">⚠️ Advertencia de Stock</p>
                      <div class="space-y-1.5">
                        <div v-for="issue in stockIssues" :key="issue.product" class="text-xs text-amber-800 bg-white px-2 py-1.5 rounded-lg">
                          <span class="font-bold">{{ issue.product }}:</span> 
                          Solo <span class="font-bold text-amber-700">{{ issue.available }}</span> disponibles 
                          (pedido: <span class="font-bold">{{ issue.requested }}</span>)
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex space-x-3">
                  <button 
                    @click="resetSearch"
                    class="flex-1 px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 hover:shadow-md hover:border-slate-300"
                  >
                    Cancelar
                  </button>
                  <button 
                    @click="loadOrder"
                    class="flex-1 px-5 py-2.5 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105"
                  >
                    Cargar al Carrito
                  </button>
                </div>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue'
import axios from 'axios'

const props = defineProps({
  isOpen: { type: Boolean, default: false }
})

const emit = defineEmits(['close', 'order-loaded'])

const orderCode = ref('')
const isLoading = ref(false)
const error = ref('')
const orderLoaded = ref(false)
const foundOrder = ref(null)
const stockIssues = ref([])
const codeInput = ref(null)

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(price)
}

const searchOrder = async () => {
  if (!orderCode.value.trim()) return

  isLoading.value = true
  error.value = ''

  try {
    const response = await axios.post('/api/public/orders/find-by-code', {
      code: orderCode.value.trim()
    })

    if (response.data.success) {
      foundOrder.value = response.data.order
      stockIssues.value = response.data.stock_issues || []
      orderLoaded.value = true
    }
  } catch (err) {
    if (err.response?.status === 404) {
      error.value = 'Pedido no encontrado. Verifica el código.'
    } else {
      error.value = 'Error al buscar el pedido. Intenta nuevamente.'
    }
    console.error('Error buscando pedido:', err)
  } finally {
    isLoading.value = false
  }
}

const loadOrder = () => {
  emit('order-loaded', foundOrder.value)
  closeModal()
}

const resetSearch = () => {
  orderCode.value = ''
  orderLoaded.value = false
  foundOrder.value = null
  stockIssues.value = []
  error.value = ''
}

const closeModal = () => {
  resetSearch()
  emit('close')
}

// Focus en el input al abrir
watch(() => props.isOpen, async (newVal) => {
  if (newVal) {
    await nextTick()
    codeInput.value?.focus()
  }
})
</script>

<style scoped>
/* Scrollbar personalizado */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
