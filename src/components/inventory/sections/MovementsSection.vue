<template>
  <div class="movements-section animate-fade-in">
    <!-- Tarjetas de Resumen - Estilo Ghost/Glass compacto -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5" v-if="data && data.summary">
      <div class="bg-white/60 dark:bg-zinc-800/30 backdrop-blur-sm rounded-lg px-3 py-2.5 border-0 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.08)] dark:shadow-[0_2px_12px_-3px_rgba(0,0,0,0.25)]">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-blue-50 dark:bg-blue-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Total</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ data.summary.total_movements }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white/60 dark:bg-zinc-800/30 backdrop-blur-sm rounded-lg px-3 py-2.5 border-0 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.08)] dark:shadow-[0_2px_12px_-3px_rgba(0,0,0,0.25)]">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-emerald-50 dark:bg-emerald-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"></path>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Entradas</p>
            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ data.summary.total_entries }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white/60 dark:bg-zinc-800/30 backdrop-blur-sm rounded-lg px-3 py-2.5 border-0 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.08)] dark:shadow-[0_2px_12px_-3px_rgba(0,0,0,0.25)]">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-rose-50 dark:bg-rose-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3"></path>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Salidas</p>
            <p class="text-lg font-bold text-rose-600 dark:text-rose-400">{{ data.summary.total_exits }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white/60 dark:bg-zinc-800/30 backdrop-blur-sm rounded-lg px-3 py-2.5 border-0 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.08)] dark:shadow-[0_2px_12px_-3px_rgba(0,0,0,0.25)]">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-emerald-50 dark:bg-emerald-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">$ Entradas</p>
            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(data.summary.total_entry_value) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white/60 dark:bg-zinc-800/30 backdrop-blur-sm rounded-lg px-3 py-2.5 border-0 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.08)] dark:shadow-[0_2px_12px_-3px_rgba(0,0,0,0.25)]">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-orange-50 dark:bg-orange-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">$ Salidas</p>
            <p class="text-lg font-bold text-orange-600 dark:text-orange-400">{{ formatCurrency(data.summary.total_exit_value) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white/60 dark:bg-zinc-800/30 backdrop-blur-sm rounded-lg px-3 py-2.5 border-0 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.08)] dark:shadow-[0_2px_12px_-3px_rgba(0,0,0,0.25)]">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-indigo-50 dark:bg-indigo-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"></path>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Neto</p>
            <p class="text-lg font-bold" :class="data.summary.net_movement >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
              {{ formatCurrency(data.summary.net_movement) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Controles y Filtros - Compacto -->
    <div class="bg-white/60 dark:bg-zinc-800/30 backdrop-blur-sm rounded-xl border-0 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.08)] dark:shadow-[0_2px_12px_-3px_rgba(0,0,0,0.25)] p-4 mb-4">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Historial de Movimientos</h2>
        <button 
          @click="refreshData" 
          :disabled="loading"
          class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 disabled:opacity-50 text-sm font-medium flex items-center gap-1.5 transition-colors"
        >
          <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path>
          </svg>
          Actualizar
        </button>
      </div>
      
      <!-- Filtros en una línea -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div>
          <label class="block text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Período</label>
          <select 
            v-model="filters.period"
            @change="applyFilters"
            class="w-full border-0 bg-gray-100 dark:bg-zinc-700/50 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 transition-colors"
          >
            <option value="day">Hoy</option>
            <option value="week">Esta Semana</option>
            <option value="month">Este Mes</option>
            <option value="year">Este Año</option>
          </select>
        </div>
        
        <div>
          <label class="block text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Tipo</label>
          <select 
            v-model="filters.type"
            @change="applyFilters"
            class="w-full border-0 bg-gray-100 dark:bg-zinc-700/50 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 transition-colors"
          >
            <option value="">Todos</option>
            <option value="entry">Entradas</option>
            <option value="exit">Salidas</option>
            <option value="adjustment">Ajustes</option>
          </select>
        </div>
        
        <div>
          <label class="block text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Usuario</label>
          <select 
            v-model="filters.user"
            @change="applyFilters"
            class="w-full border-0 bg-gray-100 dark:bg-zinc-700/50 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 transition-colors"
          >
            <option value="">Todos</option>
            <option v-for="user in availableUsers" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div>
        
        <div>
          <label class="block text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Producto</label>
          <input 
            v-model="filters.productSearch"
            @input="applyFilters"
            type="text" 
            placeholder="Buscar..."
            class="w-full border-0 bg-gray-100 dark:bg-zinc-700/50 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 transition-colors"
          >
        </div>
      </div>
    </div>

    <!-- Tabla de Movimientos - Estilo limpio -->
    <div class="bg-white/60 dark:bg-zinc-800/30 backdrop-blur-sm rounded-xl border-0 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.08)] dark:shadow-[0_2px_12px_-3px_rgba(0,0,0,0.25)] overflow-hidden">
      <div class="px-4 py-3 border-b border-gray-100 dark:border-zinc-700/50">
        <h3 class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Detalle de Movimientos</h3>
      </div>
      
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-6 w-6 border-2 border-blue-500 border-t-transparent"></div>
        <span class="ml-3 text-sm text-gray-500 dark:text-zinc-400">Cargando...</span>
      </div>
      
      <div v-else-if="!data || !data.movements || data.movements.length === 0" class="text-center py-12">
        <svg class="w-12 h-12 text-gray-300 dark:text-zinc-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
        </svg>
        <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">Sin movimientos</p>
        <p class="text-xs text-gray-400 dark:text-zinc-500">No se encontraron registros con los filtros aplicados</p>
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-gray-50/50 dark:bg-zinc-800/50">
            <tr>
              <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Fecha</th>
              <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Tipo</th>
              <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Producto</th>
              <th class="px-4 py-3 text-center text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Cantidad</th>
              <th class="px-4 py-3 text-right text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">P. Unit.</th>
              <th class="px-4 py-3 text-right text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Total</th>
              <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Usuario</th>
              <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Referencia</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-zinc-700/50">
            <tr 
              v-for="movement in data.movements" 
              :key="movement.movement_id" 
              class="hover:bg-gray-50/50 dark:hover:bg-zinc-700/20 transition-colors"
            >
              <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600 dark:text-zinc-300">
                {{ formatDate(movement.movement_date) }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span 
                  class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase"
                  :class="getMovementTypeClass(movement.movement_type)"
                >
                  {{ formatMovementType(movement.movement_type) }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-gray-900 dark:text-white">
                {{ movement.product_name }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-center">
                <span class="text-xs font-bold" :class="movement.quantity >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                  {{ movement.quantity >= 0 ? '+' : '' }}{{ Math.abs(movement.quantity) }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-right text-xs text-gray-500 dark:text-zinc-400">
                {{ formatCurrency(movement.unit_price) }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-right">
                <span class="text-xs font-semibold" :class="movement.total_value >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                  {{ formatCurrency(Math.abs(movement.total_value)) }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-zinc-400">
                {{ movement.user_name }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-zinc-400">
                {{ movement.reference || '-' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Paginación -->
      <div v-if="data && data.pagination" class="bg-white dark:bg-zinc-900 px-4 py-3 border-t border-slate-200 dark:border-zinc-800 sm:px-6">
        <div class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="changePage(currentPage - 1)"
              :disabled="currentPage <= 1"
              class="relative inline-flex items-center px-4 py-2 border border-slate-300 dark:border-zinc-700 text-sm font-medium rounded-lg text-slate-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Anterior
            </button>
            <button
              @click="changePage(currentPage + 1)"
              :disabled="currentPage >= totalPages"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-slate-300 dark:border-zinc-700 text-sm font-medium rounded-lg text-slate-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Siguiente
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-slate-700 dark:text-zinc-400">
                Mostrando
                <span class="font-bold text-slate-900 dark:text-white">{{ startItem }}</span>
                a
                <span class="font-bold text-slate-900 dark:text-white">{{ endItem }}</span>
                de
                <span class="font-bold text-slate-900 dark:text-white">{{ totalItems }}</span>
                resultados
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button
                  @click="changePage(1)"
                  :disabled="currentPage <= 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-lg border border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm font-medium text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                  </svg>
                </button>
                <button
                  @click="changePage(currentPage - 1)"
                  :disabled="currentPage <= 1"
                  class="relative inline-flex items-center px-2 py-2 border border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm font-medium text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed"
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
                        ? 'z-10 bg-slate-900 dark:bg-blue-600 border-slate-900 dark:border-blue-600 text-white'
                        : 'bg-white dark:bg-zinc-800 border-slate-300 dark:border-zinc-700 text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-700'
                    ]"
                  >
                    {{ page }}
                  </button>
                </template>
                
                <button
                  @click="changePage(currentPage + 1)"
                  :disabled="currentPage >= totalPages"
                  class="relative inline-flex items-center px-2 py-2 border border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm font-medium text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
                <button
                  @click="changePage(totalPages)"
                  :disabled="currentPage >= totalPages"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-lg border border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm font-medium text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed"
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
        'transfer': 'Transferencia',
        'entry': 'Entrada',
        'exit': 'Salida',
        'manual_entry': 'Entrada Manual',
        'manual_exit': 'Salida Manual'
      }
      return types[type] || type
    }
    
    const getMovementTypeClass = (type) => {
      const classes = {
        'sale': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        'purchase': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        'adjustment': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'return': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        'transfer': 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
        'entry': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
        'exit': 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
        'manual_entry': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
        'manual_exit': 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400'
      }
      return classes[type] || 'bg-gray-100 text-gray-800 dark:bg-zinc-800 dark:text-zinc-300'
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