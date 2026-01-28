<template>
  <div class="movements-section animate-fade-in">
    <!-- Tarjetas de Resumen - Dentro de un contenedor -->
    <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-4 mb-4" v-if="data && data.summary">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">Resumen del Período</h3>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-[#f8f9fa] dark:bg-[#282a2c] rounded-xl px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#e8f0fe] dark:bg-[#1a73e8]/20 rounded-full flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
              </svg>
            </div>
            <div class="min-w-0">
              <p class="text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">Total</p>
              <p class="text-xl font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">{{ data.summary.total_movements }}</p>
            </div>
          </div>
        </div>

        <div class="bg-[#f8f9fa] dark:bg-[#282a2c] rounded-xl px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#e6f4ea] dark:bg-[#1e8e3e]/20 rounded-full flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-[#1e8e3e] dark:text-[#81c995]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"></path>
              </svg>
            </div>
            <div class="min-w-0">
              <p class="text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">Entradas</p>
              <p class="text-xl font-semibold text-[#1e8e3e] dark:text-[#81c995]">{{ data.summary.total_entries }}</p>
            </div>
          </div>
        </div>

        <div class="bg-[#f8f9fa] dark:bg-[#282a2c] rounded-xl px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#fce8e6] dark:bg-[#d93025]/20 rounded-full flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-[#d93025] dark:text-[#f28b82]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3"></path>
              </svg>
            </div>
            <div class="min-w-0">
              <p class="text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">Salidas</p>
              <p class="text-xl font-semibold text-[#d93025] dark:text-[#f28b82]">{{ data.summary.total_exits }}</p>
            </div>
          </div>
        </div>

        <div class="bg-[#f8f9fa] dark:bg-[#282a2c] rounded-xl px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#e6f4ea] dark:bg-[#1e8e3e]/20 rounded-full flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-[#1e8e3e] dark:text-[#81c995]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="min-w-0">
              <p class="text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">Valor Entradas</p>
              <p class="text-xl font-semibold text-[#1e8e3e] dark:text-[#81c995]">{{ formatCurrency(data.summary.total_entry_value) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Controles y Filtros - Card separado -->
    <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-4 mb-4">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">Historial de Movimientos</h2>
        <button 
          @click="refreshData" 
          :disabled="loading"
          class="text-[#1a73e8] dark:text-[#8ab4f8] hover:bg-[#e8f0fe] dark:hover:bg-[#1a73e8]/20 disabled:opacity-50 text-sm font-medium flex items-center gap-1.5 transition-colors px-3 py-1.5 rounded-full"
        >
          <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path>
          </svg>
          Actualizar
        </button>
      </div>
      
      <!-- Filtros en una línea - Gemini Style -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div>
          <label class="block text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide mb-1">Período</label>
          <select 
            v-model="filters.period"
            @change="applyFilters"
            class="w-full bg-[#f8f9fa] dark:bg-[#3a3a3f] text-[#1e1f20] dark:text-[#e3e3e3] rounded-full px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#8ab4f8] transition-colors cursor-pointer"
          >
            <option value="day">Hoy</option>
            <option value="week">Esta Semana</option>
            <option value="month">Este Mes</option>
            <option value="year">Este Año</option>
          </select>
        </div>
        
        <div>
          <label class="block text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide mb-1">Tipo</label>
          <select 
            v-model="filters.type"
            @change="applyFilters"
            class="w-full bg-[#f8f9fa] dark:bg-[#3a3a3f] text-[#1e1f20] dark:text-[#e3e3e3] rounded-full px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#8ab4f8] transition-colors cursor-pointer"
          >
            <option value="">Todos</option>
            <option value="entry">Entradas</option>
            <option value="exit">Salidas</option>
            <option value="adjustment">Ajustes</option>
          </select>
        </div>
        
        <div>
          <label class="block text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide mb-1">Usuario</label>
          <select 
            v-model="filters.user"
            @change="applyFilters"
            class="w-full bg-[#f8f9fa] dark:bg-[#3a3a3f] text-[#1e1f20] dark:text-[#e3e3e3] rounded-full px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#8ab4f8] transition-colors cursor-pointer"
          >
            <option value="">Todos</option>
            <option v-for="user in availableUsers" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div>
        
        <div>
          <label class="block text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide mb-1">Producto</label>
          <input 
            v-model="filters.productSearch"
            @input="applyFilters"
            type="text" 
            placeholder="Buscar..."
            class="w-full bg-[#f8f9fa] dark:bg-[#3a3a3f] text-[#1e1f20] dark:text-[#e3e3e3] rounded-full px-4 py-2.5 text-sm placeholder-[#5f6368] dark:placeholder-[#9aa0a6] focus:outline-none focus:ring-2 focus:ring-[#8ab4f8] transition-colors"
          >
        </div>
      </div>
    </div>

    <!-- Tabla de Movimientos - Gemini Style -->
    <div class="bg-white dark:bg-[#1e1f20] rounded-2xl overflow-hidden">
      <div v-if="loading" class="flex items-center justify-center py-12">
        <svg class="animate-spin w-6 h-6 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="ml-3 text-sm text-[#5f6368] dark:text-[#9aa0a6]">Cargando...</span>
      </div>
      
      <div v-else-if="!data || !data.movements || data.movements.length === 0" class="text-center py-12">
        <svg class="w-12 h-12 text-[#5f6368] dark:text-[#9aa0a6] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
        </svg>
        <p class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Sin movimientos</p>
        <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6]">No se encontraron registros con los filtros aplicados</p>
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-[#f8f9fa] dark:bg-[#282a2c]">
            <tr>
              <th class="px-4 py-3 text-left text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">Fecha</th>
              <th class="px-4 py-3 text-left text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">Tipo</th>
              <th class="px-4 py-3 text-left text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">Producto</th>
              <th class="px-4 py-3 text-center text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">Cantidad</th>
              <th class="px-4 py-3 text-right text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">P. Unit.</th>
              <th class="px-4 py-3 text-right text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">Total</th>
              <th class="px-4 py-3 text-left text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">Usuario</th>
              <th class="px-4 py-3 text-left text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">Referencia</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#e8eaed] dark:divide-[#3a3a3f] bg-white dark:bg-[#1e1f20]">
            <tr 
              v-for="movement in data.movements" 
              :key="movement.movement_id" 
              class="hover:bg-[#f8f9fa] dark:hover:bg-[#282a2c] transition-colors"
            >
              <td class="px-4 py-3 whitespace-nowrap text-xs text-[#5f6368] dark:text-[#9aa0a6]">
                {{ formatDate(movement.movement_date) }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span 
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium uppercase"
                  :class="getMovementTypeClass(movement.movement_type)"
                >
                  {{ formatMovementType(movement.movement_type) }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-[#1e1f20] dark:text-[#e3e3e3]">
                {{ movement.product_name }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-center">
                <span class="text-xs font-semibold" :class="movement.quantity >= 0 ? 'text-[#1e8e3e] dark:text-[#81c995]' : 'text-[#d93025] dark:text-[#f28b82]'">
                  {{ movement.quantity >= 0 ? '+' : '' }}{{ Math.abs(movement.quantity) }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-right text-xs text-[#5f6368] dark:text-[#9aa0a6]">
                {{ formatCurrency(movement.unit_price) }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-right">
                <span class="text-xs font-medium" :class="movement.total_value >= 0 ? 'text-[#1e8e3e] dark:text-[#81c995]' : 'text-[#d93025] dark:text-[#f28b82]'">
                  {{ formatCurrency(Math.abs(movement.total_value)) }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-xs text-[#5f6368] dark:text-[#9aa0a6]">
                {{ movement.user_name }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-xs text-[#5f6368] dark:text-[#9aa0a6]">
                {{ movement.reference || '-' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Paginación - Gemini Style -->
      <div v-if="data && data.pagination" class="bg-white dark:bg-[#1e1f20] px-4 py-3 border-t border-[#e8eaed] dark:border-[#3a3a3f] sm:px-6">
        <div class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="changePage(currentPage - 1)"
              :disabled="currentPage <= 1"
              class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-full text-[#1a73e8] dark:text-[#8ab4f8] bg-[#f8f9fa] dark:bg-[#282a2c] hover:bg-[#e8f0fe] dark:hover:bg-[#1a73e8]/20 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Anterior
            </button>
            <button
              @click="changePage(currentPage + 1)"
              :disabled="currentPage >= totalPages"
              class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-full text-[#1a73e8] dark:text-[#8ab4f8] bg-[#f8f9fa] dark:bg-[#282a2c] hover:bg-[#e8f0fe] dark:hover:bg-[#1a73e8]/20 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Siguiente
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6]">
                Mostrando
                <span class="font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">{{ startItem }}</span>
                a
                <span class="font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">{{ endItem }}</span>
                de
                <span class="font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">{{ totalItems }}</span>
                resultados
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-full overflow-hidden" aria-label="Pagination">
                <button
                  @click="changePage(1)"
                  :disabled="currentPage <= 1"
                  class="relative inline-flex items-center px-3 py-2 bg-[#f8f9fa] dark:bg-[#282a2c] text-sm font-medium text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#e8eaed] dark:hover:bg-[#3a3a3f] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                  </svg>
                </button>
                <button
                  @click="changePage(currentPage - 1)"
                  :disabled="currentPage <= 1"
                  class="relative inline-flex items-center px-3 py-2 bg-[#f8f9fa] dark:bg-[#282a2c] text-sm font-medium text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#e8eaed] dark:hover:bg-[#3a3a3f] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </button>
                
                <template v-for="page in visiblePages" :key="page">
                  <button
                    @click="changePage(page)"
                    :class="[
                      'relative inline-flex items-center px-4 py-2 text-sm font-medium',
                      page === currentPage
                        ? 'z-10 bg-[#1a73e8] dark:bg-[#8ab4f8] text-white dark:text-[#131314]'
                        : 'bg-[#f8f9fa] dark:bg-[#282a2c] text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#e8eaed] dark:hover:bg-[#3a3a3f]'
                    ]"
                  >
                    {{ page }}
                  </button>
                </template>
                
                <button
                  @click="changePage(currentPage + 1)"
                  :disabled="currentPage >= totalPages"
                  class="relative inline-flex items-center px-3 py-2 bg-[#f8f9fa] dark:bg-[#282a2c] text-sm font-medium text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#e8eaed] dark:hover:bg-[#3a3a3f] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
                <button
                  @click="changePage(totalPages)"
                  :disabled="currentPage >= totalPages"
                  class="relative inline-flex items-center px-3 py-2 bg-[#f8f9fa] dark:bg-[#282a2c] text-sm font-medium text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#e8eaed] dark:hover:bg-[#3a3a3f] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                  </svg>
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { notificationStore } from '../../../store/notifications'
import { inventoryStore } from '../../../store/inventory'

export default {
  name: 'MovementsSection',
  props: {
    data: Object,
    loading: Boolean
  },
  emits: ['refresh', 'filter-change'],
  setup(props, { emit }) {
    const filters = ref({
      period: 'year',
      type: '',
      user: '',
      productSearch: ''
    })
    
    const availableUsers = ref([])
    
    // Computed para notificaciones
    const hasNewMovements = computed(() => {
      return notificationStore.unreadMovementsCount.value > 0
    })
    
    const newMovementsCount = computed(() => {
      return notificationStore.unreadMovementsCount.value
    })
    
    // Métodos de formateo
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(amount || 0)
    }
    
    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
    
    const formatMovementType = (type) => {
      const types = {
        'sale': 'Venta',
        'purchase': 'Compra',
        'adjustment': 'Ajuste',
        'return': 'Devolución',
        'transfer': 'Transferencia',
        'entry': 'Entrada',
        'exit': 'Salida',
        'in': 'Entrada',      // 🔄 Backend usa 'in' para entradas
        'out': 'Salida',      // 🔄 Backend usa 'out' para salidas (ventas)
        'manual_entry': 'Entrada Manual',
        'manual_exit': 'Salida Manual'
      }
      return types[type] || type
    }
    
    const getMovementTypeClass = (type) => {
      const classes = {
        'sale': 'bg-[#fce8e6] text-[#d93025] dark:bg-[#d93025]/20 dark:text-[#f28b82]',
        'purchase': 'bg-[#e6f4ea] text-[#1e8e3e] dark:bg-[#1e8e3e]/20 dark:text-[#81c995]',
        'adjustment': 'bg-[#e8f0fe] text-[#1a73e8] dark:bg-[#1a73e8]/20 dark:text-[#8ab4f8]',
        'return': 'bg-[#fef7e0] text-[#f9ab00] dark:bg-[#f9ab00]/20 dark:text-[#fdd663]',
        'transfer': 'bg-[#f3e8ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]',
        'entry': 'bg-[#e6f4ea] text-[#1e8e3e] dark:bg-[#1e8e3e]/20 dark:text-[#81c995]',
        'exit': 'bg-[#fce8e6] text-[#d93025] dark:bg-[#d93025]/20 dark:text-[#f28b82]',
        'in': 'bg-[#e6f4ea] text-[#1e8e3e] dark:bg-[#1e8e3e]/20 dark:text-[#81c995]',   // 🔄 Backend usa 'in'
        'out': 'bg-[#fce8e6] text-[#d93025] dark:bg-[#d93025]/20 dark:text-[#f28b82]',             // 🔄 Backend usa 'out'
        'manual_entry': 'bg-[#e6f4ea] text-[#1e8e3e] dark:bg-[#1e8e3e]/20 dark:text-[#81c995]',
        'manual_exit': 'bg-[#fce8e6] text-[#d93025] dark:bg-[#d93025]/20 dark:text-[#f28b82]'
      }
      return classes[type] || 'bg-[#f8f9fa] text-[#5f6368] dark:bg-[#3a3a3f] dark:text-[#9aa0a6]'
    }
    
    const getMovementIconPath = (type) => {
      const paths = {
        'sale': 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'purchase': 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v3M4 15h16a1 1 0 001-1V9a1 1 0 00-1-1h-3m-9.418 5H19a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z',
        'adjustment': 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        'return': 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'transfer': 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
        'entry': 'M5 10l7-7m0 0l7 7m-7-7v18',
        'exit': 'M19 14l-7 7m0 0l-7-7m7 7V3',
        'manual_entry': 'M5 10l7-7m0 0l7 7m-7-7v18',
        'manual_exit': 'M19 14l-7 7m0 0l-7-7m7 7V3'
      }
      return paths[type] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    }
    
    const isNewMovement = (movement) => {
      const lastVisited = notificationStore.notificationCounts.value.last_movements_view
      if (!lastVisited) return false
      
      return new Date(movement.movement_date) > new Date(lastVisited)
    }
    
    // Métodos de acción
    const refreshData = async () => {
      await emit('refresh')
      await notificationStore.loadNotifications()
    }
    
    const applyFilters = () => {
      emit('filter-change', filters.value)
    }
    
    // Paginación
    const currentPage = computed(() => props.data?.pagination?.current_page || 1)
    const totalPages = computed(() => props.data?.pagination?.last_page || 1)
    const totalItems = computed(() => props.data?.pagination?.total || 0)
    const perPage = computed(() => props.data?.pagination?.per_page || 50)
    
    const startItem = computed(() => {
      if (totalItems.value === 0) return 0
      return ((currentPage.value - 1) * perPage.value) + 1
    })
    
    const endItem = computed(() => {
      const end = currentPage.value * perPage.value
      return Math.min(end, totalItems.value)
    })
    
    const visiblePages = computed(() => {
      const pages = []
      const start = Math.max(1, currentPage.value - 2)
      const end = Math.min(totalPages.value, currentPage.value + 2)
      
      for (let i = start; i <= end; i++) {
        pages.push(i)
      }
      
      return pages
    })
    
    const changePage = (page) => {
      if (page >= 1 && page <= totalPages.value && page !== currentPage.value) {
        emit('page-change', page)
      }
    }
    
    // Marcar como visto cuando se monta el componente
    onMounted(async () => {
      await notificationStore.markMovementsAsViewed()
      await notificationStore.loadNotifications()
    })
    
    return {
      filters,
      availableUsers,
      hasNewMovements,
      newMovementsCount,
      formatCurrency,
      formatDate,
      formatMovementType,
      getMovementTypeClass,
      getMovementIconPath,
      isNewMovement,
      refreshData,
      applyFilters,
      currentPage,
      totalPages,
      totalItems,
      startItem,
      endItem,
      visiblePages,
      changePage
    }
  }
}
</script>