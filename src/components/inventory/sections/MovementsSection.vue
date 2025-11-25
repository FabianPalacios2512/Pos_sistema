<template>
  <div class="movements-section animate-fade-in">
    <!-- Tarjetas de Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6" v-if="data && data.summary">
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center">
          <div class="p-2 bg-blue-50 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Total Movimientos</p>
            <p class="text-lg font-black text-slate-900">{{ data.summary.total_movements }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center">
          <div class="p-2 bg-green-50 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Entradas</p>
            <p class="text-lg font-black text-green-600">{{ data.summary.total_entries }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center">
          <div class="p-2 bg-red-50 rounded-lg">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Salidas</p>
            <p class="text-lg font-black text-red-600">{{ data.summary.total_exits }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center">
          <div class="p-2 bg-emerald-50 rounded-lg">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Valor Entradas</p>
            <p class="text-lg font-black text-emerald-600">{{ formatCurrency(data.summary.total_entry_value) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center">
          <div class="p-2 bg-orange-50 rounded-lg">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Valor Salidas</p>
            <p class="text-lg font-black text-orange-600">{{ formatCurrency(data.summary.total_exit_value) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center">
          <div class="p-2 bg-indigo-50 rounded-lg">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Movimiento Neto</p>
            <p class="text-lg font-black" :class="data.summary.net_movement >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ formatCurrency(data.summary.net_movement) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Controles y Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-slate-900">Movimientos de Inventario</h2>
        <div class="flex items-center space-x-4">
          <!-- Notificación de nuevos movimientos -->
          <div v-if="hasNewMovements" class="relative">
            <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
            <span class="text-sm font-bold text-red-600 flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
              </svg>
              {{ newMovementsCount }} nuevos
            </span>
          </div>
          
          <button 
            @click="refreshData" 
            :disabled="loading"
            class="text-blue-600 hover:text-blue-800 disabled:opacity-50 font-semibold flex items-center transition-colors"
          >
            <svg class="w-4 h-4 mr-2" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Actualizar
          </button>
        </div>
      </div>
      
      <!-- Filtros -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Período</label>
          <select 
            v-model="filters.period"
            @change="applyFilters"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-900 focus:border-transparent shadow-sm"
          >
            <option value="day">Hoy</option>
            <option value="week">Esta Semana</option>
            <option value="month">Este Mes</option>
            <option value="year">Este Año</option>
          </select>
        </div>
        
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Tipo</label>
          <select 
            v-model="filters.type"
            @change="applyFilters"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-900 focus:border-transparent shadow-sm"
          >
            <option value="">Todos</option>
            <option value="entry">Entradas</option>
            <option value="exit">Salidas</option>
            <option value="adjustment">Ajustes</option>
          </select>
        </div>
        
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Usuario</label>
          <select 
            v-model="filters.user"
            @change="applyFilters"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-900 focus:border-transparent shadow-sm"
          >
            <option value="">Todos</option>
            <option v-for="user in availableUsers" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div>
        
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Producto</label>
          <input 
            v-model="filters.productSearch"
            @input="applyFilters"
            type="text" 
            placeholder="Buscar producto..."
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-900 focus:border-transparent shadow-sm"
          >
        </div>
      </div>
    </div>

    <!-- Tabla de Movimientos -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Historial Detallado</h3>
      </div>
      
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span class="ml-3 text-slate-600 font-medium">Cargando movimientos...</span>
      </div>
      
      <div v-else-if="!data || !data.movements || data.movements.length === 0" class="text-center py-12 text-slate-500">
        <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
        </svg>
        <p class="text-lg font-bold text-slate-700">No hay movimientos</p>
        <p class="text-sm">No se encontraron movimientos con los filtros aplicados</p>
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tipo</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Producto</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Cantidad</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Precio Unit.</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Usuario</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Referencia</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-slate-200">
            <tr 
              v-for="movement in data.movements" 
              :key="movement.movement_id" 
              class="hover:bg-slate-50 transition-colors"
              :class="{ 'bg-blue-50/50': isNewMovement(movement) }"
            >
              <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-medium">
                <div class="flex items-center">
                  <div v-if="isNewMovement(movement)" class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
                  {{ formatDate(movement.movement_date) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide"
                  :class="getMovementTypeClass(movement.movement_type)"
                >
                  <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getMovementIconPath(movement.movement_type)"></path>
                  </svg>
                  {{ formatMovementType(movement.movement_type) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                {{ movement.product_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-bold" :class="movement.quantity >= 0 ? 'text-green-600' : 'text-red-600'">
                {{ Math.abs(movement.quantity) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                {{ formatCurrency(movement.unit_price) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-bold" :class="movement.total_value >= 0 ? 'text-green-600' : 'text-red-600'">
                {{ formatCurrency(Math.abs(movement.total_value)) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                {{ movement.user_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                {{ movement.reference || '-' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Paginación -->
      <div v-if="data && data.pagination" class="bg-white px-4 py-3 border-t border-slate-200 sm:px-6">
        <div class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="changePage(currentPage - 1)"
              :disabled="currentPage <= 1"
              class="relative inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Anterior
            </button>
            <button
              @click="changePage(currentPage + 1)"
              :disabled="currentPage >= totalPages"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Siguiente
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-slate-700">
                Mostrando
                <span class="font-bold text-slate-900">{{ startItem }}</span>
                a
                <span class="font-bold text-slate-900">{{ endItem }}</span>
                de
                <span class="font-bold text-slate-900">{{ totalItems }}</span>
                resultados
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button
                  @click="changePage(1)"
                  :disabled="currentPage <= 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-lg border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                  </svg>
                </button>
                <button
                  @click="changePage(currentPage - 1)"
                  :disabled="currentPage <= 1"
                  class="relative inline-flex items-center px-2 py-2 border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </button>
                
                <template v-for="page in visiblePages" :key="page">
                  <button
                    @click="changePage(page)"
                    :class="[
                      'relative inline-flex items-center px-4 py-2 border text-sm font-bold',
                      page === currentPage
                        ? 'z-10 bg-slate-900 border-slate-900 text-white'
                        : 'bg-white border-slate-300 text-slate-500 hover:bg-slate-50'
                    ]"
                  >
                    {{ page }}
                  </button>
                </template>
                
                <button
                  @click="changePage(currentPage + 1)"
                  :disabled="currentPage >= totalPages"
                  class="relative inline-flex items-center px-2 py-2 border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
                <button
                  @click="changePage(totalPages)"
                  :disabled="currentPage >= totalPages"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-lg border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
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
        'transfer': 'Transferencia'
      }
      return types[type] || type
    }
    
    const getMovementTypeClass = (type) => {
      const classes = {
        'sale': 'bg-red-100 text-red-800',
        'purchase': 'bg-green-100 text-green-800',
        'adjustment': 'bg-blue-100 text-blue-800',
        'return': 'bg-yellow-100 text-yellow-800',
        'transfer': 'bg-purple-100 text-purple-800'
      }
      return classes[type] || 'bg-gray-100 text-gray-800'
    }
    
    const getMovementIconPath = (type) => {
      const paths = {
        'sale': 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'purchase': 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v3M4 15h16a1 1 0 001-1V9a1 1 0 00-1-1h-3m-9.418 5H19a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z',
        'adjustment': 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        'return': 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'transfer': 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'
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