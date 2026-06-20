<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="isOpen"
        @click="closeModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
      >
        <Transition
          enter-active-class="transition-all duration-200 ease-out"
          enter-from-class="opacity-0 translate-y-4 scale-95"
          enter-to-class="opacity-100 translate-y-0 scale-100"
          leave-active-class="transition-all duration-150 ease-in"
          leave-from-class="opacity-100 translate-y-0 scale-100"
          leave-to-class="opacity-0 translate-y-2 scale-95"
        >
          <div 
            v-if="isOpen"
            @click.stop
            class="bg-white dark:bg-[#09090b] rounded-xl shadow-2xl max-w-sm w-full overflow-hidden border border-gray-200 dark:border-zinc-800"
          >
            <!-- Header -->
            <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-zinc-800">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-zinc-800 flex items-center justify-center text-gray-700 dark:text-zinc-300">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-[15px] font-bold text-gray-900 dark:text-white leading-tight">Cargar Pedido Web</h3>
                  <p class="text-[11px] text-gray-500 dark:text-zinc-400">Sincroniza la tienda en línea</p>
                </div>
              </div>
              <button 
                @click="closeModal"
                class="w-7 h-7 flex items-center justify-center rounded-md hover:bg-gray-100 dark:hover:bg-zinc-800 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              </button>
            </div>

            <!-- Content -->
            <div class="p-5">
              <div v-if="!orderLoaded">
                <!-- Input State -->
                <div class="space-y-4">
                  <div>
                    <label class="block text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">
                      Código de Referencia
                    </label>
                    <div class="relative group">
                      <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-zinc-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                      </div>
                      <input 
                        ref="codeInput"
                        v-model="orderCode"
                        @keyup.enter="searchOrder"
                        type="text"
                        placeholder="Ej: PED-12345"
                        class="w-full pl-9 pr-3 py-2.5 bg-gray-50 dark:bg-zinc-900/50 border border-gray-200 dark:border-zinc-800 rounded-lg text-sm font-semibold text-gray-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:ring-1 focus:ring-gray-900 dark:focus:ring-white focus:border-gray-900 dark:focus:border-white outline-none transition-all placeholder-gray-400 dark:placeholder-zinc-600 uppercase"
                        :disabled="isLoading"
                      >
                      <div v-if="isLoading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                        <div class="animate-spin rounded-full h-3.5 w-3.5 border border-gray-900 dark:border-white border-t-transparent"></div>
                      </div>
                    </div>
                    <Transition enter-active-class="transition-all duration-200" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition-all duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-1">
                      <div v-if="error" class="mt-2 flex items-center gap-1.5 text-red-600 dark:text-red-400">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-[11px] font-semibold">{{ error }}</p>
                      </div>
                    </Transition>
                  </div>

                  <button 
                    @click="searchOrder"
                    :disabled="!orderCode.trim() || isLoading"
                    class="w-full py-2.5 bg-gray-900 hover:bg-black dark:bg-white dark:hover:bg-gray-100 disabled:opacity-50 text-white dark:text-gray-900 text-[13px] font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                  >
                    <span v-if="!isLoading">Buscar pedido</span>
                    <span v-else>Buscando...</span>
                  </button>
                </div>
              </div>

              <div v-else class="space-y-4">
                <!-- Success State -->
                <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 p-3 rounded-lg">
                  <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-md flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                  </div>
                  <div>
                    <h4 class="text-[13px] font-bold text-gray-900 dark:text-white leading-tight">Pedido Encontrado</h4>
                    <p class="text-[11px] text-gray-600 dark:text-zinc-400 font-medium">Cod: {{ foundOrder.order_number }}</p>
                  </div>
                </div>

                <!-- Customer Summary -->
                <div class="bg-gray-50 dark:bg-zinc-900/50 border border-gray-200 dark:border-zinc-800 rounded-lg p-3 flex items-center justify-between">
                  <div>
                    <p class="text-[9px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">Cliente</p>
                    <p class="text-[12px] font-semibold text-gray-900 dark:text-white">{{ foundOrder.customer_name }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-[9px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">Contacto</p>
                    <p class="text-[12px] font-semibold text-gray-700 dark:text-zinc-300">{{ foundOrder.customer_phone || 'N/A' }}</p>
                  </div>
                </div>

                <!-- Items List -->
                <div>
                  <p class="text-[11px] font-bold text-gray-900 dark:text-white mb-2">Resumen ({{ foundOrder.items.length }} prod.)</p>
                  <div class="space-y-1.5 max-h-[140px] overflow-y-auto pr-1 custom-scrollbar">
                    <div 
                      v-for="item in foundOrder.items" 
                      :key="item.product_id"
                      class="flex justify-between items-start py-2 px-2.5 bg-white dark:bg-[#09090b] border border-gray-200 dark:border-zinc-800 rounded-md"
                    >
                      <div class="flex items-start gap-2">
                        <span class="font-bold text-[10px] bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-300 px-1.5 py-0.5 rounded mt-0.5">
                          {{ item.quantity }}x
                        </span>
                        <div>
                          <p class="text-[12px] font-semibold text-gray-900 dark:text-white leading-tight">{{ item.product_name }}</p>
                          <p v-if="item.options_summary" class="text-[10px] text-gray-500 dark:text-zinc-400 mt-0.5">{{ formatOptionsSummary(item.options_summary) }}</p>
                        </div>
                      </div>
                      <span class="font-bold text-[12px] text-gray-900 dark:text-white ml-2 whitespace-nowrap">${{ formatPrice(item.subtotal) }}</span>
                    </div>
                  </div>
                  
                  <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200 dark:border-zinc-800">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Total</span>
                    <span class="text-[16px] font-black text-gray-900 dark:text-white">${{ formatPrice(foundOrder.total) }}</span>
                  </div>
                </div>

                <!-- Stock Warnings -->
                <div v-if="stockIssues.length > 0" class="bg-orange-50 dark:bg-orange-950/30 border border-orange-200 dark:border-orange-900/40 rounded-lg p-3">
                  <div class="flex items-center gap-1.5 mb-1.5">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p class="text-[10px] font-bold text-orange-800 dark:text-orange-400 uppercase tracking-wide">Falta de Inventario</p>
                  </div>
                  <ul class="space-y-0.5">
                    <li v-for="issue in stockIssues" :key="issue.product" class="text-[10px] text-orange-700 dark:text-orange-300 font-medium">
                      {{ issue.product }} (Req: {{ issue.requested }}, Hay: {{ issue.available }})
                    </li>
                  </ul>
                </div>

                <!-- Actions -->
                <div class="flex gap-2.5 pt-1">
                  <button 
                    @click="resetSearch"
                    class="px-4 py-2 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-[12px] font-semibold rounded-lg border border-gray-300 dark:border-zinc-700 transition-colors"
                  >
                    Atrás
                  </button>
                  <button 
                    @click="loadOrder"
                    class="flex-1 py-2 bg-gray-900 hover:bg-black dark:bg-white dark:hover:bg-gray-100 text-white dark:text-gray-900 text-[12px] font-semibold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2"
                  >
                    Cargar y Pagar
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

const formatOptionsSummary = (summary) => {
  if (!summary) return ''
  try {
    let parsed = typeof summary === 'string' ? JSON.parse(summary) : summary
    if (Array.isArray(parsed)) {
      return parsed.map(opt => `${opt.name}: ${opt.value}`).join(' / ')
    }
    return ''
  } catch (e) {
    return summary
  }
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
      error.value = 'No se encontró el pedido. Verifica el código.'
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
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #3f3f46;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #52525b;
}
</style>
