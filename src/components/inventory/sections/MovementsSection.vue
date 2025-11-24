<template>
  <div class="movements-section">
    <!-- Tarjetas de Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6" v-if="data && data.summary">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <i class="fas fa-exchange-alt text-blue-600"></i>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Total Movimientos</p>
            <p class="text-lg font-bold text-gray-900">{{ data.summary.total_movements }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <i class="fas fa-arrow-up text-green-600"></i>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Entradas</p>
            <p class="text-lg font-bold text-green-600">{{ data.summary.total_entries }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
          <div class="p-2 bg-red-100 rounded-lg">
            <i class="fas fa-arrow-down text-red-600"></i>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Salidas</p>
            <p class="text-lg font-bold text-red-600">{{ data.summary.total_exits }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
          <div class="p-2 bg-emerald-100 rounded-lg">
            <i class="fas fa-dollar-sign text-emerald-600"></i>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Valor Entradas</p>
            <p class="text-lg font-bold text-emerald-600">{{ formatCurrency(data.summary.total_entry_value) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
          <div class="p-2 bg-orange-100 rounded-lg">
            <i class="fas fa-dollar-sign text-orange-600"></i>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Valor Salidas</p>
            <p class="text-lg font-bold text-orange-600">{{ formatCurrency(data.summary.total_exit_value) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
          <div class="p-2 bg-indigo-100 rounded-lg">
            <i class="fas fa-chart-line text-indigo-600"></i>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Movimiento Neto</p>
            <p class="text-lg font-bold" :class="data.summary.net_movement >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ formatCurrency(data.summary.net_movement) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Controles y Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Movimientos de Inventario</h2>
        <div class="flex items-center space-x-4">
          <!-- Notificación de nuevos movimientos -->
          <div v-if="hasNewMovements" class="relative">
            <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
            <span class="text-sm font-medium text-red-600">
              <i class="fas fa-bell mr-1"></i>
              {{ newMovementsCount }} nuevos
            </span>
          </div>
          
          <button 
            @click="refreshData" 
            :disabled="loading"
            class="text-indigo-600 hover:text-indigo-800 disabled:opacity-50"
          >
            <i class="fas fa-sync-alt mr-2" :class="{ 'animate-spin': loading }"></i>
            Actualizar
          </button>
        </div>
      </div>
      
      <!-- Filtros -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
          <select 
            v-model="filters.period"
            @change="applyFilters"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
          >
            <option value="day">Hoy</option>
            <option value="week">Esta Semana</option>
            <option value="month">Este Mes</option>
            <option value="year">Este Año</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
          <select 
            v-model="filters.type"
            @change="applyFilters"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">Todos</option>
            <option value="entry">Entradas</option>
            <option value="exit">Salidas</option>
            <option value="adjustment">Ajustes</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
          <select 
            v-model="filters.user"
            @change="applyFilters"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">Todos</option>
            <option v-for="user in availableUsers" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Producto</label>
          <input 
            v-model="filters.productSearch"
            @input="applyFilters"
            type="text" 
            placeholder="Buscar producto..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
          >
        </div>
      </div>
    </div>

    <!-- Tabla de Movimientos -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Historial de Movimientos</h3>
      </div>
      
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        <span class="ml-3 text-gray-600">Cargando movimientos...</span>
      </div>
      
      <div v-else-if="!data || !data.movements || data.movements.length === 0" class="text-center py-12 text-gray-500">
        <i class="fas fa-exchange-alt text-4xl mb-4 text-gray-300"></i>
        <p class="text-lg font-medium">No hay movimientos</p>
        <p class="text-sm">No se encontraron movimientos con los filtros aplicados</p>
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referencia</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr 
              v-for="movement in data.movements" 
              :key="movement.movement_id" 
              class="hover:bg-gray-50"
              :class="{ 'bg-blue-50': isNewMovement(movement) }"
            >
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <div class="flex items-center">
                  <div v-if="isNewMovement(movement)" class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></div>
                  {{ formatDate(movement.movement_date) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getMovementTypeClass(movement.movement_type)"
                >
                  <i :class="getMovementTypeIcon(movement.movement_type)" class="mr-1"></i>
                  {{ formatMovementType(movement.movement_type) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ movement.product_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm" :class="movement.quantity >= 0 ? 'text-green-600' : 'text-red-600'">
                <span class="font-medium">{{ Math.abs(movement.quantity) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatCurrency(movement.unit_price) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" :class="movement.total_value >= 0 ? 'text-green-600' : 'text-red-600'">
                {{ formatCurrency(Math.abs(movement.total_value)) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ movement.user_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ movement.reference || '-' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Paginación -->
      <div v-if="data && data.pagination" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        <div class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="changePage(currentPage - 1)"
              :disabled="currentPage <= 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Anterior
            </button>
            <button
              @click="changePage(currentPage + 1)"
              :disabled="currentPage >= totalPages"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Siguiente
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Mostrando
                <span class="font-medium">{{ startItem }}</span>
                a
                <span class="font-medium">{{ endItem }}</span>
                de
                <span class="font-medium">{{ totalItems }}</span>
                resultados
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button
                  @click="changePage(1)"
                  :disabled="currentPage <= 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <i class="fas fa-angle-double-left"></i>
                </button>
                <button
                  @click="changePage(currentPage - 1)"
                  :disabled="currentPage <= 1"
                  class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <i class="fas fa-angle-left"></i>
                </button>
                
                <template v-for="page in visiblePages" :key="page">
                  <button
                    @click="changePage(page)"
                    :class="[
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                      page === currentPage
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                    ]"
                  >
                    {{ page }}
                  </button>
                </template>
                
                <button
                  @click="changePage(currentPage + 1)"
                  :disabled="currentPage >= totalPages"
                  class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <i class="fas fa-angle-right"></i>
                </button>
                <button
                  @click="changePage(totalPages)"
                  :disabled="currentPage >= totalPages"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <i class="fas fa-angle-double-right"></i>
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
    
    const getMovementTypeIcon = (type) => {
      const icons = {
        'sale': 'fas fa-shopping-cart',
        'purchase': 'fas fa-truck',
        'adjustment': 'fas fa-tools',
        'return': 'fas fa-undo',
        'transfer': 'fas fa-exchange-alt'
      }
      return icons[type] || 'fas fa-circle'
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
      getMovementTypeIcon,
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