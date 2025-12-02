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
        class="fixed inset-0 bg-black/70 dark:bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
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
            class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-gray-200 dark:border-zinc-700"
          >
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 dark:from-purple-700 dark:to-indigo-700 px-6 py-5">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-xl font-bold text-white">Cargar Pedido Web</h3>
                    <p class="text-sm text-purple-100">Busca el pedido del cliente por código</p>
                  </div>
                </div>
                <button 
                  @click="closeModal"
                  class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/10 text-white/80 hover:text-white transition-all"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Content -->
            <div class="p-6">
              <div v-if="!orderLoaded">
                <!-- Input de código -->
                <div class="space-y-5">
                  <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-3 flex items-center gap-2">
                      <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                      </svg>
                      Código de Pedido
                    </label>
                    <div class="relative">
                      <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                        <svg class="w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                      </div>
                      <input 
                        ref="codeInput"
                        v-model="orderCode"
                        @keyup.enter="searchOrder"
                        type="text"
                        placeholder="Ejemplo: PED-123 o 123"
                        class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-xl text-base font-semibold text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-600 focus:border-transparent outline-none transition-all placeholder-gray-400 dark:placeholder-zinc-500"
                        :disabled="isLoading"
                      >
                      <div v-if="isLoading" class="absolute right-4 top-1/2 transform -translate-y-1/2">
                        <div class="animate-spin rounded-full h-6 w-6 border-3 border-purple-600 border-t-transparent"></div>
                      </div>
                    </div>
                    <div v-if="error" class="mt-3 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900 rounded-xl flex items-center gap-2">
                      <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <p class="text-red-700 dark:text-red-300 text-sm font-semibold">{{ error }}</p>
                    </div>
                  </div>

                  <button 
                    @click="searchOrder"
                    :disabled="!orderCode.trim() || isLoading"
                    class="w-full py-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-300 text-white font-bold rounded-xl transition-all duration-200 disabled:cursor-not-allowed shadow-lg hover:scale-105 disabled:hover:scale-100 flex items-center justify-center gap-2"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    {{ isLoading ? 'Buscando pedido...' : 'Buscar Pedido' }}
                  </button>
                </div>

                <!-- Info card -->
                <div class="mt-6 p-5 bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-950/30 dark:to-indigo-950/30 rounded-xl border border-purple-200 dark:border-purple-900">
                  <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-purple-500 dark:bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <p class="text-sm font-bold text-purple-900 dark:text-purple-300 mb-2">💡 ¿Cómo funciona?</p>
                      <p class="text-sm text-purple-800 dark:text-purple-400 leading-relaxed">
                        Los clientes realizan pedidos desde tu <span class="font-bold">catálogo web</span> y reciben un código único. 
                        Ingresa ese código aquí para cargar automáticamente todos los productos al carrito del POS.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

                <!-- Orden encontrada -->
              <div v-else class="space-y-5">
                <!-- Success Banner -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/30 dark:to-emerald-950/30 border-2 border-green-300 dark:border-green-800 rounded-2xl p-5">
                  <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <div>
                      <span class="font-bold text-lg text-green-800 dark:text-green-300">¡Pedido Encontrado!</span>
                      <p class="text-xs text-green-700 dark:text-green-400">Información verificada correctamente</p>
                    </div>
                  </div>
                  <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center bg-white dark:bg-zinc-800 p-3 rounded-xl">
                      <span class="text-gray-600 dark:text-zinc-400 font-semibold">Código:</span>
                      <span class="font-bold text-gray-900 dark:text-white bg-green-100 dark:bg-green-900/40 px-3 py-1.5 rounded-lg border border-green-300 dark:border-green-700">{{ foundOrder.order_number }}</span>
                    </div>
                    <div class="flex justify-between items-center bg-white dark:bg-zinc-800 p-3 rounded-xl">
                      <span class="text-gray-600 dark:text-zinc-400 font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Cliente:
                      </span>
                      <span class="font-bold text-gray-900 dark:text-white">{{ foundOrder.customer_name }}</span>
                    </div>
                    <div class="flex justify-between items-center bg-white dark:bg-zinc-800 p-3 rounded-xl">
                      <span class="text-gray-600 dark:text-zinc-400 font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Teléfono:
                      </span>
                      <span class="font-bold text-gray-900 dark:text-white">{{ foundOrder.customer_phone }}</span>
                    </div>
                    <div class="flex justify-between items-center bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 p-4 rounded-xl border-2 border-green-300 dark:border-green-700 mt-3">
                      <span class="text-green-800 dark:text-green-300 font-bold text-base">Total del Pedido:</span>
                      <span class="font-bold text-green-700 dark:text-green-400 text-2xl">${{ formatPrice(foundOrder.total) }}</span>
                    </div>
                  </div>
                </div>                <!-- Productos -->
                <div class="bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-2xl overflow-hidden shadow-lg">
                  <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-5 py-4">
                    <div class="flex items-center justify-between">
                      <h4 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Productos del Pedido
                      </h4>
                      <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold text-white">{{ foundOrder.items.length }} items</span>
                    </div>
                  </div>
                  <div class="p-4 max-h-48 overflow-y-auto">
                    <div class="space-y-3">
                      <div 
                        v-for="item in foundOrder.items" 
                        :key="item.product_id"
                        class="flex justify-between items-center text-sm py-3 px-4 bg-gray-50 dark:bg-zinc-800 rounded-xl hover:bg-gray-100 dark:hover:bg-zinc-700 transition-all border border-gray-200 dark:border-zinc-700"
                      >
                        <div class="flex items-center gap-3">
                          <span class="font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/40 px-2.5 py-1 rounded-lg text-xs border border-purple-300 dark:border-purple-700">{{ item.quantity }}x</span>
                          <span class="text-gray-900 dark:text-white font-semibold">{{ item.product_name }}</span>
                        </div>
                        <span class="font-bold text-gray-900 dark:text-white text-base">${{ formatPrice(item.subtotal) }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Alertas de stock -->
                <div v-if="stockIssues.length > 0" class="bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-950/30 dark:to-yellow-950/30 border-2 border-amber-300 dark:border-amber-800 rounded-2xl p-5">
                  <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-yellow-500 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <p class="text-base font-bold text-amber-900 dark:text-amber-300 mb-3">⚠️ Advertencia de Stock</p>
                      <div class="space-y-2">
                        <div v-for="issue in stockIssues" :key="issue.product" class="text-sm text-amber-800 dark:text-amber-400 bg-white dark:bg-zinc-800 px-3 py-2.5 rounded-xl font-semibold border border-amber-200 dark:border-amber-700">
                          <span class="font-bold">{{ issue.product }}:</span> 
                          Solo <span class="font-bold text-amber-700 dark:text-amber-400">{{ issue.available }}</span> disponibles 
                          (pedido: <span class="font-bold text-amber-900 dark:text-amber-300">{{ issue.requested }}</span>)
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-3 pt-2">
                  <button 
                    @click="resetSearch"
                    class="flex-1 px-5 py-3 bg-white dark:bg-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-300 text-sm font-bold rounded-xl border-2 border-slate-300 dark:border-zinc-600 shadow-md transition-all duration-200 hover:shadow-lg hover:border-slate-400 dark:hover:border-zinc-500 flex items-center justify-center gap-2"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>Cancelar</span>
                  </button>
                  <button 
                    @click="loadOrder"
                    class="flex-1 px-5 py-3 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Cargar al Carrito</span>
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
