<template>
  <div class="space-y-4 animate-fade-in">
    
    <!-- Toolbar: Búsqueda + KPIs compactos -->
    <div class="flex items-center gap-4">
      <!-- Búsqueda -->
      <div class="relative flex-1 max-w-md">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input
          v-model="searchTerm"
          type="text"
          placeholder="Buscar producto, variante o SKU..."
          class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200"
          @input="debouncedSearch"
        >
      </div>

      <!-- KPIs compactos inline -->
      <div class="flex items-center gap-3 flex-shrink-0">
        <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-zinc-900/80 rounded-lg border border-gray-200 dark:border-zinc-800">
          <span class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wide">Filas</span>
          <span class="text-sm font-bold text-gray-900 dark:text-white">{{ matrixData.length }}</span>
        </div>
        <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-zinc-900/80 rounded-lg border border-gray-200 dark:border-zinc-800">
          <span class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wide">Sedes</span>
          <span class="text-sm font-bold text-gray-900 dark:text-white">{{ warehouses.length }}</span>
        </div>
        <div class="flex items-center gap-2 px-3 py-2 rounded-lg border"
             :class="alertCount > 0 ? 'bg-rose-50 dark:bg-rose-950 border-rose-200 dark:border-rose-800' : 'bg-white dark:bg-zinc-900/80 border-gray-200 dark:border-zinc-800'">
          <span class="text-[10px] font-semibold uppercase tracking-wide"
                :class="alertCount > 0 ? 'text-rose-500 dark:text-rose-400' : 'text-gray-400 dark:text-zinc-500'">Alertas</span>
          <span class="text-sm font-bold"
                :class="alertCount > 0 ? 'text-rose-700 dark:text-rose-400' : 'text-gray-900 dark:text-white'">{{ alertCount }}</span>
        </div>
        
        <!-- Refrescar -->
        <button @click="loadMatrix" 
                :disabled="loading"
                class="p-2.5 text-gray-500 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl border border-gray-200 dark:border-zinc-700 transition-all duration-200 disabled:opacity-40">
          <svg class="w-4 h-4" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Matriz de Stock -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
      
      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center py-24">
        <div class="flex flex-col items-center gap-3">
          <svg class="animate-spin h-7 w-7 text-gray-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
          </svg>
          <span class="text-sm text-gray-500 dark:text-zinc-500">Cargando matriz de distribución...</span>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else-if="matrixData.length === 0 && !loading" class="py-20 text-center">
        <svg class="w-14 h-14 mx-auto text-gray-300 dark:text-zinc-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5"/>
        </svg>
        <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">{{ searchTerm ? 'Sin resultados para esa búsqueda' : 'No hay productos distribuidos en bodegas' }}</p>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="bg-gray-50 dark:bg-zinc-800/60 border-b border-gray-200 dark:border-zinc-800">
              <!-- Producto / Variante (sticky) -->
              <th class="sticky left-0 z-10 bg-gray-50 dark:bg-zinc-800/60 px-4 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider min-w-[280px]">
                Producto / Variante
              </th>
              <th class="px-3 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider min-w-[90px]">
                SKU
              </th>
              <!-- Columnas dinámicas por bodega -->
              <th v-for="wh in warehouses" :key="wh.id"
                  class="px-3 py-3 text-right text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider min-w-[100px] whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  <span v-if="wh.is_default" class="text-emerald-500 dark:text-emerald-400">★</span>
                  {{ wh.name }}
                </div>
              </th>
              <!-- Stock Global -->
              <th class="px-3 py-3 text-right text-[10px] font-semibold text-gray-700 dark:text-zinc-300 uppercase tracking-wider min-w-[100px] bg-gray-100 dark:bg-zinc-800">
                Global
              </th>
              <!-- Acción -->
              <th class="px-3 py-3 text-center text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider min-w-[90px]">
                Acción
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/60">
            <tr v-for="row in paginatedData" :key="rowKey(row)" 
                class="hover:bg-gray-50/70 dark:hover:bg-zinc-800/30 transition-colors duration-100 group">
              
              <!-- Producto / Variante (sticky) -->
              <td class="sticky left-0 z-10 bg-white dark:bg-zinc-900 group-hover:bg-gray-50/70 dark:group-hover:bg-zinc-800/30 transition-colors px-4 py-2.5">
                <div class="flex items-center gap-3">
                  <!-- Avatar / Imagen -->
                  <div class="w-8 h-8 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100 dark:bg-zinc-800 flex items-center justify-center border border-gray-200 dark:border-zinc-700">
                    <img v-if="row.image_url" :src="row.image_url" :alt="row.product_name" class="w-full h-full object-cover">
                    <svg v-else class="w-4 h-4 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5"/>
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-800 dark:text-zinc-200 truncate leading-tight">{{ row.product_name }}</p>
                    <p v-if="row.variant_label" class="text-[10px] text-gray-500 dark:text-zinc-500 mt-0.5 truncate">{{ row.variant_label }}</p>
                    <span v-if="!row.variant_id" class="text-[10px] text-gray-400 dark:text-zinc-600">Simple</span>
                  </div>
                </div>
              </td>
              
              <!-- SKU -->
              <td class="px-3 py-2.5">
                <span class="text-[11px] font-mono text-gray-400 dark:text-zinc-500">{{ row.sku || '—' }}</span>
              </td>

              <!-- Stock por bodega -->
              <td v-for="wh in warehouses" :key="wh.id" class="px-3 py-2.5 text-right">
                <span :class="getCellClass(row.stock_by_warehouse[wh.id] ?? 0)" class="inline-flex items-center justify-end min-w-[32px] px-1.5 py-0.5 rounded text-xs font-bold tabular-nums">
                  {{ row.stock_by_warehouse[wh.id] ?? 0 }}
                </span>
              </td>

              <!-- Stock Global -->
              <td class="px-3 py-2.5 text-right bg-gray-50/50 dark:bg-zinc-800/20">
                <span class="text-xs font-extrabold text-gray-900 dark:text-white tabular-nums">{{ row.global_stock }}</span>
              </td>

              <!-- Acción: Trasladar -->
              <td class="px-3 py-2.5 text-center">
                <button @click="openTransferForRow(row)"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-semibold text-slate-600 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg hover:bg-slate-50 dark:hover:bg-zinc-700 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-200"
                        title="Crear traslado con esta variante">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                  </svg>
                  Trasladar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginación compacta -->
      <div v-if="totalPages > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
        <span class="text-xs text-gray-500 dark:text-zinc-500">
          {{ (currentPage - 1) * pageSize + 1 }}–{{ Math.min(currentPage * pageSize, matrixData.length) }} de {{ matrixData.length }}
        </span>
        <div class="flex items-center gap-1">
          <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage <= 1"
                  class="px-2.5 py-1.5 text-xs font-medium rounded-md border border-gray-200 dark:border-zinc-700 text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
            Anterior
          </button>
          <span class="px-3 py-1.5 text-xs font-bold text-gray-700 dark:text-zinc-300">{{ currentPage }} / {{ totalPages }}</span>
          <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage >= totalPages"
                  class="px-2.5 py-1.5 text-xs font-medium rounded-md border border-gray-200 dark:border-zinc-700 text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
            Siguiente
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Traslado (reutilizar StockTransferModal) -->
    <StockTransferModal
      v-if="showTransferModal"
      :warehouses="warehouses"
      @close="showTransferModal = false"
      @saved="onTransferSaved"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import StockTransferModal from './StockTransferModal.vue'
import { useToast } from '@/composables/useToast'

const { showSuccess, showError } = useToast()

// State
const loading = ref(false)
const matrixData = ref([])
const warehouses = ref([])
const searchTerm = ref('')
const currentPage = ref(1)
const pageSize = 50
const showTransferModal = ref(false)

// Alertas: celdas con stock ≤ 3 (pero > 0 se considera riesgo, 0 es quiebre)
const ALERT_THRESHOLD = 3

const alertCount = computed(() => {
  let count = 0
  matrixData.value.forEach(row => {
    Object.values(row.stock_by_warehouse).forEach(stock => {
      if (stock <= ALERT_THRESHOLD) count++
    })
  })
  return count
})

const totalPages = computed(() => Math.ceil(matrixData.value.length / pageSize))

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * pageSize
  return matrixData.value.slice(start, start + pageSize)
})

const rowKey = (row) => `${row.product_id}-${row.variant_id || 'simple'}`

// Estilo de celda según nivel de stock
const getCellClass = (stock) => {
  if (stock === 0) return 'text-gray-300 dark:text-zinc-700'
  if (stock <= ALERT_THRESHOLD) return 'text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60'
  return 'text-gray-700 dark:text-zinc-300'
}

// Carga de datos
const loadMatrix = async () => {
  loading.value = true
  try {
    const params = {}
    if (searchTerm.value) params.search = searchTerm.value

    const response = await api.get('/warehouses/stock-matrix', { params })
    warehouses.value = response.warehouses || response.data?.warehouses || []
    matrixData.value = response.matrix || response.data?.matrix || []
    currentPage.value = 1
  } catch (error) {
    showError('Error al cargar la matriz de distribución')
  } finally {
    loading.value = false
  }
}

// Debounce para búsqueda
let searchTimer = null
const debouncedSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    loadMatrix()
  }, 400)
}

// Abrir modal de traslado pre-cargado
const openTransferForRow = (row) => {
  showTransferModal.value = true
}

const onTransferSaved = () => {
  showTransferModal.value = false
  showSuccess('Traslado creado exitosamente')
  loadMatrix()
}

// Exponer para padre
defineExpose({ loadMatrix })

onMounted(() => {
  loadMatrix()
})
</script>
