<template>
  <div class="min-h-screen font-sans bg-gray-50 dark:bg-[#131314] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header sin icono -->
      <div class="flex items-center justify-between pb-4">
        <div>
          <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Movimientos de Caja</h1>
          <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">Gestiona egresos operativos e ingresos manuales de caja</p>
        </div>
        
        <div class="flex items-center gap-3">
          <!-- Botón Actualizar -->
          <button @click="loadExpenses" 
                  class="px-5 py-2.5 bg-gray-50 dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 text-sm font-medium rounded-md border border-gray-200 dark:border-zinc-700 transition-all duration-200">
            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Actualizar
          </button>
          
          <button @click="openCashIncomeModal" 
                  class="px-6 py-2.5 bg-white dark:bg-zinc-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 text-sm font-medium rounded-md border border-emerald-200 dark:border-emerald-800/40 transition-all duration-300">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Registrar Ingreso Caja
          </button>

          <!-- Botón Registrar Gasto -->
          <button @click="openCreateModal" 
                  class="px-6 py-2.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 text-sm font-medium rounded-md transition-all duration-300">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Registrar Egreso
          </button>
        </div>
      </div>
      
      <!-- KPIs — Metrics Ribbon (Vercel/Linear) -->
      <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100 dark:divide-zinc-800">
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Total Egresos Mes</p>
            <svg class="w-4 h-4 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">${{ formatNumber(statistics.current_month || 0) }}</p>
          <div class="flex items-center gap-2">
            <span v-if="statistics.percentage_change !== undefined" class="text-xs font-semibold" :class="statistics.percentage_change > 0 ? 'text-rose-500 dark:text-rose-400' : 'text-emerald-500 dark:text-emerald-400'">
              {{ statistics.percentage_change > 0 ? '↑' : '↓' }} {{ Math.abs(statistics.percentage_change) }}%
            </span>
            <span class="text-xs text-gray-400 dark:text-zinc-500">{{ expenses.total || 0 }} gastos</span>
          </div>
        </div>
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Ingresos de Caja</p>
            <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          </div>
          <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tabular-nums">${{ formatNumber(cashMovementsSummary.total_ingresos || 0) }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500">{{ cashMovements.filter(m => m.type === 'ingreso').length }} ingresos sesión</p>
        </div>
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">En Efectivo</p>
            <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">${{ formatNumber(getPaymentMethodTotal('efectivo')) }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500">{{ getPaymentMethodCount('efectivo') }} gastos</p>
        </div>
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Transferencia</p>
            <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">${{ formatNumber(getPaymentMethodTotal('transferencia')) }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500">{{ getPaymentMethodCount('transferencia') }} gastos</p>
        </div>
      </div>

      <!-- Tabs: Egresos / Ingresos de Caja -->
      <div class="flex items-center gap-1 bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 p-1 w-fit">
        <button @click="activeTab = 'expenses'" 
                :class="activeTab === 'expenses' ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-sm' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'"
                class="px-4 py-2 text-sm font-semibold rounded transition-all duration-200">
          Egresos
          <span v-if="expenses.total" class="ml-1.5 text-xs opacity-70">({{ expenses.total }})</span>
        </button>
        <button @click="activeTab = 'incomes'" 
                :class="activeTab === 'incomes' ? 'bg-emerald-600 dark:bg-emerald-500 text-white shadow-sm' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'"
                class="px-4 py-2 text-sm font-semibold rounded transition-all duration-200">
          Ingresos de Caja
          <span v-if="cashMovements.filter(m => m.type === 'ingreso').length" class="ml-1.5 text-xs opacity-70">({{ cashMovements.filter(m => m.type === 'ingreso').length }})</span>
        </button>
      </div>

      <!-- ===== EGRESOS TAB ===== -->
      <template v-if="activeTab === 'expenses'">
      <!-- Filtros -->
      <div class="bg-white dark:bg-[#131314] rounded-md shadow-sm p-3 border border-gray-200 dark:border-zinc-800">
        <div class="flex flex-wrap items-center gap-3">
          <!-- Búsqueda -->
          <div class="flex-1 min-w-48 relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input v-model="filters.search" 
                   @input="loadExpenses"
                   type="text" 
                   placeholder="Buscar por descripción, proveedor o recibo..."
                   class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>
          
          <!-- Filtro por Categoría -->
          <select v-model="filters.category_id" 
                  @change="loadExpenses"
                  class="px-3 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-36">
            <option value="">Todas las Categorías</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
          
          <!-- Filtro por Método de Pago -->
          <select v-model="filters.payment_method" 
                  @change="loadExpenses"
                  class="px-3 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-36">
            <option value="">Todos los Métodos</option>
            <option value="efectivo">Efectivo</option>
            <option value="transferencia">Transferencia</option>
            <option value="tarjeta">Tarjeta</option>
          </select>
          
          <!-- Fecha Inicio -->
          <input v-model="filters.start_date" 
                 @change="loadExpenses"
                 type="date" 
                 class="px-3 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
          
          <!-- Fecha Fin -->
          <input v-model="filters.end_date" 
                 @change="loadExpenses"
                 type="date" 
                 class="px-3 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
          
          <!-- Botón Limpiar Filtros -->
          <button @click="clearFilters" 
                  class="p-2.5 text-gray-500 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg border border-transparent hover:border-red-100 dark:hover:border-red-900/30 transition-all duration-200"
                  title="Limpiar filtros">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Tabla de Gastos -->
      <div class="bg-white dark:bg-[#131314] rounded-md shadow-sm border border-gray-200 dark:border-zinc-800 overflow-hidden">
        <div class="bg-gray-50 dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-800 px-4 py-3 flex items-center justify-between">
          <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Lista de Gastos</h2>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
              {{ expenses.total || 0 }} gastos registrados
            </p>
          </div>
        </div>
        
        <div v-if="loading" class="px-4 py-12 text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400"></div>
          <p class="text-sm text-gray-500 dark:text-zinc-400 mt-3">Cargando gastos...</p>
        </div>

        <div v-else-if="expenses.data && expenses.data.length === 0" class="px-4 py-12 text-center">
          <div class="flex flex-col items-center space-y-3">
            <div class="w-12 h-12 bg-gray-50 dark:bg-zinc-800 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-700 dark:text-zinc-300">No hay gastos registrados</p>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Comienza registrando tu primer gasto</p>
            </div>
            <button @click="openCreateModal" 
                    class="px-4 py-2 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 text-sm font-medium rounded-md">
              Registrar Gasto
            </button>
          </div>
        </div>

        <table v-else class="min-w-full">
          <thead class="border-b border-gray-200 dark:border-zinc-800">
            <tr>
              <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Fecha</th>
              <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Categoría</th>
              <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Descripción</th>
              <th class="px-3 py-3 text-right text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Monto</th>
              <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Método</th>
              <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Usuario</th>
              <th class="px-3 py-3 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-transparent">
            <tr v-for="expense in expenses.data" :key="expense.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors duration-200 border-b border-gray-100 dark:border-zinc-800/50">
              <td class="px-3 py-3 text-sm text-gray-900 dark:text-zinc-200">
                {{ formatDate(expense.date) }}
              </td>
              <td class="px-3 py-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border uppercase tracking-wide"
                      :style="{ backgroundColor: expense.category?.color + '20', color: expense.category?.color, borderColor: expense.category?.color + '40' }">
                  {{ expense.category?.name }}
                </span>
              </td>
              <td class="px-3 py-3 text-sm text-gray-700 dark:text-zinc-300">
                <div class="max-w-xs truncate">{{ expense.description }}</div>
                <div v-if="expense.supplier" class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                  {{ expense.supplier }}
                </div>
              </td>
              <td class="px-3 py-3 text-sm font-semibold text-gray-900 dark:text-white text-right font-mono">
                ${{ formatNumber(expense.amount) }}
              </td>
              <td class="px-3 py-3">
                <span :class="[
                  'px-2 py-0.5 rounded-md text-xs border font-medium',
                  expense.payment_method === 'efectivo' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' :
                  expense.payment_method === 'transferencia' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800' :
                  'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-800'
                ]">
                  {{ expense.payment_method }}
                </span>
              </td>
              <td class="px-3 py-3 text-sm text-gray-600 dark:text-zinc-400">
                {{ expense.user?.name }}
              </td>
              <td class="px-3 py-3 text-center">
                <div class="flex items-center justify-center gap-1">
                  <button @click="viewExpense(expense)" 
                          class="p-2 text-gray-400 dark:text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg border border-transparent hover:border-blue-100 dark:hover:border-blue-900/30 transition-all duration-200"
                          title="Ver detalles">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                  <button @click="editExpense(expense)" 
                          class="p-2 text-gray-400 dark:text-zinc-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg border border-transparent hover:border-amber-100 dark:hover:border-amber-900/30 transition-all duration-200"
                          title="Editar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button @click="confirmDelete(expense)" 
                          class="p-2 text-gray-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg border border-transparent hover:border-rose-100 dark:hover:border-rose-900/30 transition-all duration-200"
                          title="Eliminar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Paginación - Solo mostrar si hay más de 10 registros -->
        <div v-if="expenses.data && expenses.data.length > 0 && expenses.total > 10" 
             class="bg-white dark:bg-[#131314] border-t border-gray-200 dark:border-zinc-800 px-4 py-3 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
              <span class="text-xs font-medium text-gray-700 dark:text-zinc-300">Mostrar:</span>
              <select v-model="filters.per_page" 
                      @change="loadExpenses"
                      class="border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
              <span class="text-xs text-gray-700 dark:text-zinc-300">por página</span>
            </div>
            
            <div class="text-xs text-gray-700 dark:text-zinc-300">
              Mostrando {{ expenses.from }} a {{ expenses.to }} de {{ expenses.total }} registros
            </div>
          </div>
          
          <div class="flex items-center space-x-1">
            <button @click="goToPage(1)" 
                    :disabled="expenses.current_page === 1"
                    class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-[#131314] border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
              </svg>
            </button>
            
            <button @click="goToPage(expenses.current_page - 1)" 
                    :disabled="expenses.current_page === 1"
                    class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-[#131314] border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            
            <div class="flex items-center space-x-1">
              <span class="px-2.5 py-1 text-xs font-medium bg-blue-600 dark:bg-blue-500 text-white border border-blue-600 dark:border-blue-500 rounded-lg">
                {{ expenses.current_page }}
              </span>
              <span class="text-xs text-gray-500 dark:text-zinc-400">de {{ expenses.last_page }}</span>
            </div>
            
            <button @click="goToPage(expenses.current_page + 1)" 
                    :disabled="expenses.current_page === expenses.last_page"
                    class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-[#131314] border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            
            <button @click="goToPage(expenses.last_page)" 
                    :disabled="expenses.current_page === expenses.last_page"
                    class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-[#131314] border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </template>

    <!-- ===== INGRESOS DE CAJA TAB ===== -->
    <template v-if="activeTab === 'incomes'">
      <div class="bg-white dark:bg-[#131314] rounded-md shadow-sm border border-gray-200 dark:border-zinc-800 overflow-hidden">
        <div class="bg-gray-50 dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-800 px-4 py-3 flex items-center justify-between">
          <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Ingresos de Caja</h2>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
              {{ cashMovements.filter(m => m.type === 'ingreso').length }} ingresos en la sesión activa
            </p>
          </div>
          <button @click="loadCashMovements" 
                  class="px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-md hover:bg-gray-50 dark:hover:bg-zinc-800 transition-all">
            Refrescar
          </button>
        </div>

        <div v-if="cashMovements.filter(m => m.type === 'ingreso').length === 0" class="px-4 py-12 text-center">
          <div class="flex flex-col items-center space-y-3">
            <div class="w-12 h-12 bg-gray-50 dark:bg-zinc-800 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-700 dark:text-zinc-300">No hay ingresos de caja en esta sesión</p>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Registra un ingreso manual usando el botón superior</p>
            </div>
            <button @click="openCashIncomeModal" 
                    class="px-4 py-2 bg-emerald-600 dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-emerald-600 text-white text-sm font-medium rounded-md transition-all">
              Registrar Ingreso
            </button>
          </div>
        </div>

        <table v-else class="min-w-full">
          <thead class="border-b border-gray-200 dark:border-zinc-800">
            <tr>
              <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Fecha/Hora</th>
              <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Tipo</th>
              <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Concepto</th>
              <th class="px-3 py-3 text-right text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Monto</th>
              <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Referencia</th>
              <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Usuario</th>
            </tr>
          </thead>
          <tbody class="bg-transparent">
            <tr v-for="movement in cashMovements.filter(m => m.type === 'ingreso')" :key="movement.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors duration-200 border-b border-gray-100 dark:border-zinc-800/50">
              <td class="px-3 py-3 text-sm text-gray-900 dark:text-zinc-200">
                {{ formatDateTime(movement.created_at) }}
              </td>
              <td class="px-3 py-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs border font-medium bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800">
                  Ingreso
                </span>
              </td>
              <td class="px-3 py-3 text-sm text-gray-700 dark:text-zinc-300">
                <div class="max-w-xs truncate">{{ movement.concept }}</div>
                <div v-if="movement.notes" class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5 truncate max-w-xs">
                  {{ movement.notes }}
                </div>
              </td>
              <td class="px-3 py-3 text-sm font-semibold text-emerald-600 dark:text-emerald-400 text-right font-mono">
                +${{ formatNumber(movement.amount) }}
              </td>
              <td class="px-3 py-3 text-sm text-gray-600 dark:text-zinc-400">
                {{ movement.reference || '—' }}
              </td>
              <td class="px-3 py-3 text-sm text-gray-600 dark:text-zinc-400">
                {{ movement.user?.name || '—' }}
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Summary footer -->
        <div v-if="cashMovements.filter(m => m.type === 'ingreso').length > 0" class="bg-gray-50 dark:bg-zinc-800/50 border-t border-gray-200 dark:border-zinc-800 px-4 py-3 flex items-center justify-between">
          <span class="text-xs text-gray-500 dark:text-zinc-400">Total ingresos de la sesión activa</span>
          <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 font-mono">${{ formatNumber(cashMovementsSummary.total_ingresos || 0) }}</span>
        </div>
      </div>
    </template>

    <!-- Modal de Confirmación de Eliminación -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/60 dark:bg-black/75  flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-zinc-800 rounded-md shadow-sm dark:shadow-black/40 max-w-md w-full border border-gray-200 dark:border-zinc-800 overflow-hidden">
        <div class="bg-rose-50 dark:bg-rose-900/20 border-b border-rose-100 dark:border-rose-900/30 px-6 py-4">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-rose-100 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800/50 rounded-md flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">¿Eliminar Gasto?</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Esta acción no se puede deshacer</p>
            </div>
          </div>
        </div>

        <div v-if="expenseToDelete" class="p-6 space-y-4">
          <div class="bg-gray-50 dark:bg-[#131314] rounded-md p-4 border border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between mb-3">
              <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Monto del gasto:</span>
              <span class="text-2xl font-black text-gray-900 dark:text-white">${{ formatNumber(expenseToDelete.amount) }}</span>
            </div>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-zinc-400">Categoría:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ expenseToDelete.category?.name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-zinc-400">Descripción:</span>
                <span class="font-medium text-gray-900 dark:text-white truncate ml-2 max-w-[200px]">{{ expenseToDelete.description }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-zinc-400">Fecha:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ formatDate(expenseToDelete.date) }}</span>
              </div>
            </div>
          </div>

          <p class="text-sm text-gray-600 dark:text-zinc-400 text-center">
            ¿Estás seguro que deseas eliminar este gasto?
          </p>
        </div>

        <div class="bg-gray-50 dark:bg-[#131314] border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex gap-3 rounded-b-md">
          <button @click="showDeleteModal = false" 
                  class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 text-sm font-medium rounded-md border border-gray-200 dark:border-zinc-700 transition-all duration-200">
            Cancelar
          </button>
          <button @click="deleteExpense" 
                  class="flex-1 px-4 py-2.5 bg-rose-600 dark:bg-rose-500 hover:bg-rose-700 dark:hover:bg-rose-600 text-white text-sm font-medium rounded-md shadow-sm transition-all duration-300">
            Eliminar Gasto
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Crear/Editar Movimiento -->
    <div v-if="showModal" class="fixed inset-0 bg-black/60 dark:bg-black/75  flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-zinc-800 rounded-md shadow-sm dark:shadow-black/40 border border-gray-200 dark:border-zinc-800 max-w-5xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header Modal -->
        <div class="bg-gray-50 dark:bg-[#131314] border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between flex-shrink-0">
          <div class="flex items-center gap-3">
            <div :class="entryMode === 'cash-income' ? 'bg-emerald-50 dark:bg-emerald-900/30 border-emerald-200 dark:border-emerald-800/50' : 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800/50'" class="w-11 h-11 border rounded-md flex items-center justify-center">
              <svg v-if="entryMode === 'cash-income'" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              <svg v-else class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                {{ entryMode === 'cash-income' ? 'Registrar Ingreso de Caja' : (isEditing ? 'Editar Gasto' : 'Registrar Egreso') }}
              </h3>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ entryMode === 'cash-income' ? 'Registra base, sencillo o dinero entregado a la caja' : 'Complete la información del egreso operativo' }}</p>
            </div>
          </div>
          <button @click="closeModal" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Body Modal con Layout Horizontal -->
        <form @submit.prevent="entryMode === 'cash-income' ? saveCashIncome() : saveExpense()" class="flex-1 overflow-y-auto">
          <div v-if="!isEditing" class="px-6 pt-5">
            <div class="inline-flex p-1 rounded-md bg-gray-100 dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800">
              <button
                type="button"
                @click="entryMode = 'expense'"
                :class="entryMode === 'expense' ? 'bg-white dark:bg-zinc-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-zinc-400'"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-all"
              >
                Egreso
              </button>
              <button
                type="button"
                @click="entryMode = 'cash-income'"
                :class="entryMode === 'cash-income' ? 'bg-white dark:bg-zinc-800 text-emerald-700 dark:text-emerald-300 shadow-sm' : 'text-gray-500 dark:text-zinc-400'"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-all"
              >
                Ingreso Caja
              </button>
            </div>
          </div>

          <div v-if="entryMode === 'expense'" class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
            
            <!-- Columna Izquierda: Información Principal (2/3) -->
            <div class="lg:col-span-2 space-y-5">
              <!-- Sección: Información Básica -->
              <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-800 rounded-md p-5">
                <div class="flex items-center space-x-2 mb-4 pb-3 border-b border-gray-200 dark:border-zinc-800">
                  <svg class="w-4 h-4 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Información del Gasto</h4>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                  <!-- Categoría -->
                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Categoría *</label>
                    <select v-model="form.category_id" 
                            required
                            class="w-full px-3 py-2.5 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-[#131314] text-gray-900 dark:text-zinc-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                      <option value="">Seleccionar categoría</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                  </div>

                  <!-- Monto -->
                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Monto *</label>
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-zinc-400 text-sm font-medium">$</span>
                      <input v-model="form.amount" 
                             type="text"
                             inputmode="decimal"
                             @input="formatAmountInput"
                             required
                             placeholder="0.00"
                             class="w-full pl-8 pr-3 py-2.5 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-[#131314] text-gray-900 dark:text-white rounded-lg text-sm font-semibold font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                  </div>
                </div>

                <!-- Descripción -->
                <div class="mt-4">
                  <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Descripción del Gasto *</label>
                  <textarea v-model="form.description" 
                            rows="3"
                            required
                            placeholder="Describe el propósito y detalles del gasto..."
                            class="w-full px-3 py-2.5 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-[#131314] text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all"></textarea>
                </div>
              </div>

              <!-- Sección: Información de Pago -->
              <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-800 rounded-md p-5">
                <div class="flex items-center space-x-2 mb-4 pb-3 border-b border-gray-200 dark:border-zinc-800">
                  <svg class="w-4 h-4 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                  </svg>
                  <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Método de Pago</h4>
                </div>

                <div class="space-y-4">
                  <!-- Método de Pago -->
                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Forma de Pago *</label>
                    <select v-model="form.payment_method" 
                            required
                            class="w-full px-3 py-2.5 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-[#131314] text-gray-900 dark:text-zinc-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                      <option value="efectivo">Efectivo</option>
                      <option value="transferencia">Transferencia Bancaria</option>
                      <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                    </select>
                  </div>

                  <!-- Fuente del Gasto (solo para efectivo) -->
                  <div v-if="form.payment_method === 'efectivo'">
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Fuente *</label>
                    <div class="space-y-2">
                      <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" 
                               v-model="form.expense_source" 
                               name="expense_source"
                               value="caja"
                               required
                               class="w-4 h-4 text-blue-600">
                        <span class="text-sm text-gray-700 dark:text-zinc-300">Descontar de caja actual</span>
                      </label>
                      <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" 
                               v-model="form.expense_source" 
                               name="expense_source"
                               value="general"
                               required
                               class="w-4 h-4 text-blue-600">
                        <span class="text-sm text-gray-700 dark:text-zinc-300">Gasto general</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Columna Derecha: Información Complementaria (1/3) -->
            <div class="space-y-5">
              <!-- Sección: Detalles Adicionales -->
              <div class="bg-gray-50 dark:bg-[#131314] border border-gray-200 dark:border-zinc-800 rounded-md p-5">
                <div class="flex items-center space-x-2 mb-4 pb-3 border-b border-gray-200 dark:border-zinc-800">
                  <svg class="w-4 h-4 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                  <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Información Complementaria</h4>
                </div>

                <div class="space-y-4">
                  <!-- Fecha -->
                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Fecha</label>
                    <input v-model="form.date" 
                           type="date"
                           class="w-full px-3 py-2.5 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                  </div>

                  <!-- Proveedor -->
                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Proveedor</label>
                    <input v-model="form.supplier" 
                           type="text"
                           placeholder="Nombre del proveedor"
                           class="w-full px-3 py-2.5 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-[#131314] text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                  </div>

                  <!-- Número de Recibo -->
                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Nº Recibo</label>
                    <input v-model="form.receipt_number" 
                           type="text"
                           placeholder="FAC-001234"
                           class="w-full px-3 py-2.5 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-[#131314] text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                  </div>

                  <!-- Notas Adicionales -->
                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Notas</label>
                    <textarea v-model="form.notes" 
                              rows="3"
                              placeholder="Información adicional..."
                              class="w-full px-3 py-2.5 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-[#131314] text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
            <div class="lg:col-span-2 space-y-5">
              <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-800 rounded-md p-5">
                <div class="flex items-center space-x-2 mb-4 pb-3 border-b border-gray-200 dark:border-zinc-800">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Ingreso Manual a Caja</h4>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Monto *</label>
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-zinc-400 text-sm font-medium">$</span>
                      <input v-model="cashMovementForm.amount"
                             type="text"
                             inputmode="decimal"
                             @input="formatCashMovementAmountInput"
                             required
                             placeholder="0.00"
                             class="w-full pl-8 pr-3 py-2.5 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-[#131314] text-gray-900 dark:text-white rounded-lg text-sm font-semibold font-mono focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                    </div>
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Referencia</label>
                    <input v-model="cashMovementForm.reference"
                           type="text"
                           placeholder="Base caja, sencillo, aporte..."
                           class="w-full px-3 py-2.5 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-[#131314] text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                  </div>
                </div>

                <div class="mt-4">
                  <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Concepto *</label>
                  <textarea v-model="cashMovementForm.concept"
                            rows="3"
                            required
                            placeholder="Ejemplo: Administrador dejó sencillo para vueltas"
                            class="w-full px-3 py-2.5 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-[#131314] text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none transition-all"></textarea>
                </div>
              </div>
            </div>

            <div class="space-y-5">
              <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 rounded-md p-5">
                <div class="flex items-center space-x-2 mb-4 pb-3 border-b border-emerald-200 dark:border-emerald-800/40">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Notas del Movimiento</h4>
                </div>

                <div class="space-y-4">
                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Notas</label>
                    <textarea v-model="cashMovementForm.notes"
                              rows="4"
                              placeholder="Detalle opcional del ingreso a caja"
                              class="w-full px-3 py-2.5 border border-emerald-200 dark:border-emerald-800/40 bg-white dark:bg-[#131314] text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none transition-all"></textarea>
                  </div>

                  <div class="rounded-md bg-white dark:bg-[#131314] border border-emerald-200 dark:border-emerald-800/40 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Impacto</p>
                    <p class="text-sm text-gray-600 dark:text-zinc-400 mt-2">Este ingreso se suma al efectivo esperado al cerrar caja y quedará visible en la auditoría de la sesión.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer Modal dentro del form -->
          <div class="bg-gray-50 dark:bg-[#131314] border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex justify-between items-center flex-shrink-0">
            <p class="text-xs text-gray-500 dark:text-zinc-400">
              <span class="font-medium">*</span> Campos obligatorios
            </p>
            <div class="flex gap-3">
              <button @click="closeModal" 
                      type="button"
                      class="px-5 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 text-sm font-medium rounded-md border border-gray-200 dark:border-zinc-700 transition-all duration-200">
                Cancelar
              </button>
              <button type="submit"
                      :disabled="saving"
                      class="px-5 py-2.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 text-sm font-medium rounded-md shadow-sm transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <svg v-if="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ saving ? 'Guardando...' : (entryMode === 'cash-income' ? 'Guardar Ingreso' : (isEditing ? 'Actualizar Gasto' : 'Guardar Egreso')) }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Ver Detalles -->
    <div v-if="showViewModal" class="fixed inset-0 bg-black/60 dark:bg-black/75  flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-zinc-800 rounded-md shadow-sm dark:shadow-black/40 border border-gray-200 dark:border-zinc-800 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gray-50 dark:bg-[#131314] border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between rounded-t-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Detalles del Gasto</h3>
          <button @click="showViewModal = false" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div v-if="selectedExpense" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Categoría</p>
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border uppercase tracking-wide"
                    :style="{ backgroundColor: selectedExpense.category?.color + '20', color: selectedExpense.category?.color, borderColor: selectedExpense.category?.color + '40' }">
                {{ selectedExpense.category?.name }}
              </span>
            </div>
            <div>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Monto</p>
              <p class="text-lg font-semibold text-gray-900 dark:text-white">${{ formatNumber(selectedExpense.amount) }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Método de Pago</p>
              <p class="text-sm font-medium text-gray-900 dark:text-zinc-200">{{ selectedExpense.payment_method }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Fecha</p>
              <p class="text-sm font-medium text-gray-900 dark:text-zinc-200">{{ formatDate(selectedExpense.date) }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Registrado por</p>
              <p class="text-sm font-medium text-gray-900 dark:text-zinc-200">{{ selectedExpense.user?.name }}</p>
            </div>
            <div v-if="selectedExpense.supplier">
              <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Proveedor</p>
              <p class="text-sm font-medium text-gray-900 dark:text-zinc-200">{{ selectedExpense.supplier }}</p>
            </div>
            <div v-if="selectedExpense.receipt_number">
              <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Número de Recibo</p>
              <p class="text-sm font-medium text-gray-900 dark:text-zinc-200">{{ selectedExpense.receipt_number }}</p>
            </div>
          </div>

          <div>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Descripción</p>
            <p class="text-sm text-gray-900 dark:text-zinc-200">{{ selectedExpense.description }}</p>
          </div>

          <div v-if="selectedExpense.notes">
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Notas</p>
            <p class="text-sm text-gray-900 dark:text-zinc-200">{{ selectedExpense.notes }}</p>
          </div>

          <div v-if="selectedExpense.cash_session_id" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 rounded-lg p-3">
            <p class="text-xs text-blue-700 dark:text-blue-400">
              <strong>Vinculado a Sesión de Caja:</strong> #{{ selectedExpense.cash_session_id }}
            </p>
          </div>
        </div>

        <div class="bg-gray-50 dark:bg-[#131314] border-t border-gray-200 dark:border-zinc-800 px-6 py-3 flex justify-end rounded-b-md">
          <button @click="showViewModal = false" 
                  class="px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 text-sm font-medium rounded-md border border-gray-200 dark:border-zinc-700 transition-all duration-200">
            Cerrar
          </button>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script>
import apiClient from '@/services/apiClient';
import { useUIContextStore } from '@/store/uiContextStore';
import { appStore } from '@/store/appStore';

export default {
  name: 'ExpensesManager',
  data() {
    return {
      loading: false,
      saving: false,
      showModal: false,
      showViewModal: false,
      showDeleteModal: false,
      isEditing: false,
      expenses: {
        data: [],
        total: 0,
        current_page: 1,
        last_page: 1,
        from: 0,
        to: 0
      },
      activeTab: 'expenses',
      cashMovements: [],
      cashMovementsSummary: { total_ingresos: 0, total_egresos: 0, count: 0 },
      categories: [],
      statistics: {},
      entryMode: 'expense',
      filters: {
        search: '',
        category_id: '',
        payment_method: '',
        start_date: '',
        end_date: '',
        per_page: 15,
        page: 1
      },
      form: {
        category_id: '',
        amount: '',
        description: '',
        payment_method: 'efectivo',
        expense_source: 'caja', // 'caja' o 'general'
        date: '',
        receipt_number: '',
        supplier: '',
        notes: '',
      },
      cashMovementForm: {
        amount: '',
        concept: '',
        reference: '',
        notes: '',
      },
      selectedExpense: null,
      expenseToDelete: null,
      // Contexto IA
      uiContext: null,
      pendingExpense: null // Para gastos por voz que necesitan confirmación
    };
  },
  mounted() {
    // Inicializar contexto IA
    this.uiContext = useUIContextStore();
    
    this.loadCategories();
    this.loadExpenses();
    this.loadStatistics();
    this.loadCashMovements();
    
    // Actualizar contexto IA cuando los datos estén listos
    this.$nextTick(() => {
      this.actualizarContextoIA();
    });
  },
  beforeUnmount() {
    // Limpiar contexto al salir del módulo
    if (this.uiContext) {
      this.uiContext.clearSelection();
    }
  },
  methods: {
    // ========================================
    // 🤖 CONTEXTO DE IA - Gastos Operativos
    // ========================================
    actualizarContextoIA() {
      if (!this.uiContext) return;
      
      const screenData = {
        modulo: 'expenses',
        titulo: 'Movimientos de Caja',
        descripcion: 'Gestión de movimientos de caja, egresos e ingresos manuales',
        
        // KPIs principales
        kpis: {
          totalMesActual: this.statistics.current_month || 0,
          cambioVsMesAnterior: this.statistics.percentage_change || 0,
          gastosPorMetodo: this.statistics.by_payment_method || [],
          totalGastos: this.expenses.total || 0
        },
        
        // Categorías disponibles (muy importante para registro por voz)
        categoriasDisponibles: this.categories.map(c => ({
          id: c.id,
          nombre: c.name,
          color: c.color,
          descripcion: c.description
        })),
        
        // Últimos gastos para referencia
        ultimosGastos: (this.expenses.data || []).slice(0, 5).map(g => ({
          id: g.id,
          fecha: g.date,
          categoria: g.category?.name,
          descripcion: g.description,
          monto: g.amount,
          metodoPago: g.payment_method,
          usuario: g.user?.name
        })),
        
        // Estado de filtros
        filtrosActivos: {
          busqueda: this.filters.search,
          categoria: this.filters.category_id,
          metodoPago: this.filters.payment_method,
          fechaInicio: this.filters.start_date,
          fechaFin: this.filters.end_date
        },
        
        // Modal abierto
        modalAbierto: this.showModal ? (this.entryMode === 'cash-income' ? 'formulario_ingreso_caja' : 'formulario_gasto') : null,
        editando: this.isEditing,
        
        // Guía para la IA
        guiaIA: `
          Estás en Movimientos de Caja. Puedes:
          1. Registrar gastos por voz: "registra un gasto de $50.000 en servicios públicos"
          2. Registrar ingresos manuales: "agrega $100.000 de base a la caja"
          3. Consultar gastos: "¿cuánto hemos gastado este mes?", "¿en qué gastamos más?"
          4. Ver categorías: Las categorías son ${this.categories.map(c => c.name).join(', ')}
          5. Filtrar gastos por categoría, método de pago o fecha
          
          IMPORTANTE: Al registrar un gasto, siempre pregunta:
          - Si falta el monto
          - Si debe salir de la caja actual o de ganancias generales (para efectivo)
        `
      };
      
      this.uiContext.setScreenData(screenData);
      this.registrarAccionesIA();
    },
    
    registrarAccionesIA() {
      if (!this.uiContext) return;
      
      // Registrar gasto por voz - acción principal
      this.uiContext.registerAction('registrarGastoVoz', async (params) => {
        const { descripcion, monto, categoria, fuente, proveedor, metodo_pago } = params;
        
        // Si no tenemos monto, pedirlo
        if (!monto) {
          return {
            success: false,
            necesitaDatos: true,
            message: `Entendido: "${descripcion}". ¿Cuánto costó?`,
            datosActuales: { descripcion, categoria, proveedor, fuente, metodo_pago }
          };
        }
        
        // Buscar categoría por nombre
        let categoriaId = null;
        if (categoria) {
          const cat = this.categories.find(c => 
            c.name.toLowerCase().includes(categoria.toLowerCase()) ||
            categoria.toLowerCase().includes(c.name.toLowerCase())
          );
          if (cat) {
            categoriaId = cat.id;
          } else {
            // Categoría no encontrada, usar "Otros Gastos"
            const otros = this.categories.find(c => c.name.includes('Otros'));
            categoriaId = otros?.id;
          }
        } else {
          // Sin categoría especificada - inferir o usar "Otros"
          categoriaId = this.inferirCategoria(descripcion);
        }
        
        if (!categoriaId) {
          const otros = this.categories.find(c => c.name.includes('Otros'));
          categoriaId = otros?.id || this.categories[0]?.id;
        }
        
        // Si es efectivo y no especificó fuente, preguntar
        const metodoPagoFinal = metodo_pago || 'efectivo';
        if (metodoPagoFinal === 'efectivo' && !fuente) {
          return {
            success: false,
            necesitaDatos: true,
            message: `Un gasto de $${monto.toLocaleString()} por "${descripcion}". ¿Quieres descontarlo de la caja actual o es un gasto general?`,
            datosActuales: { descripcion, monto, categoria: categoriaId, proveedor, metodo_pago: metodoPagoFinal }
          };
        }
        
        // Tenemos todos los datos, crear el gasto
        try {
          const formData = {
            category_id: categoriaId,
            amount: parseFloat(monto),
            description: descripcion,
            payment_method: metodoPagoFinal,
            expense_source: metodoPagoFinal === 'efectivo' ? (fuente || 'general') : null,
            supplier: proveedor || '',
            date: new Date().toISOString().split('T')[0],
            notes: 'Registrado por voz'
          };
          
          const response = await apiClient.post('/expenses', formData);
          
          if (response.data.success) {
            this.$toast?.success('Gasto registrado exitosamente');
            this.loadExpenses();
            this.loadStatistics();
            this.actualizarContextoIA();
            
            const categoriaName = this.categories.find(c => c.id === categoriaId)?.name || 'Sin categoría';
            return {
              success: true,
              message: `✅ Listo! Registré el gasto: $${monto.toLocaleString()} en "${categoriaName}" por "${descripcion}".`
            };
          } else {
            return { success: false, message: response.data.message || 'Error al registrar el gasto' };
          }
        } catch (error) {
          console.error('Error al registrar gasto por voz:', error);
          return { 
            success: false, 
            message: error.response?.data?.message || 'Error al registrar el gasto. Intenta de nuevo.'
          };
        }
      });
      
      // Consultar gastos
      this.uiContext.registerAction('consultarGastos', async (params) => {
        const { consulta, periodo } = params;
        
        // Recargar datos si es necesario
        if (!this.statistics.current_month && this.statistics.current_month !== 0) {
          await this.loadStatistics();
        }
        
        let mensaje = '';
        
        switch(consulta) {
          case 'total_mes':
            mensaje = `📊 Total de gastos este mes: $${this.formatNumber(this.statistics.current_month || 0)}`;
            if (this.statistics.percentage_change !== undefined) {
              const trend = this.statistics.percentage_change > 0 ? '📈 +' : '📉 ';
              mensaje += ` (${trend}${this.statistics.percentage_change}% vs mes anterior)`;
            }
            break;
            
          case 'por_categoria':
            if (this.statistics.by_category && this.statistics.by_category.length > 0) {
              mensaje = '📊 Gastos por categoría este mes:\n';
              this.statistics.by_category.forEach(cat => {
                mensaje += `• ${cat.category}: $${this.formatNumber(cat.total)}\n`;
              });
            } else {
              mensaje = 'No tengo desglose por categoría disponible.';
            }
            break;
            
          case 'ultimos':
            if (this.expenses.data && this.expenses.data.length > 0) {
              mensaje = '📋 Últimos gastos registrados:\n';
              this.expenses.data.slice(0, 5).forEach(g => {
                mensaje += `• ${g.description}: $${this.formatNumber(g.amount)} (${g.category?.name})\n`;
              });
            } else {
              mensaje = 'No hay gastos registrados todavía.';
            }
            break;
            
          case 'resumen':
          default:
            mensaje = `📊 Resumen de gastos:\n`;
            mensaje += `• Total mes: $${this.formatNumber(this.statistics.current_month || 0)}\n`;
            mensaje += `• Cantidad: ${this.expenses.total || 0} gastos registrados\n`;
            
            if (this.statistics.by_payment_method) {
              mensaje += `• Por método: `;
              const metodos = this.statistics.by_payment_method.map(m => 
                `${m.payment_method}: $${this.formatNumber(m.total)}`
              ).join(', ');
              mensaje += metodos || 'Sin datos';
            }
            break;
        }
        
        return { success: true, message: mensaje, datos: this.statistics };
      });
      
      // Ver categorías de gastos
      this.uiContext.registerAction('verCategoriasGastos', async () => {
        if (this.categories.length === 0) {
          await this.loadCategories();
        }
        
        const categorias = this.categories.map(c => {
          let desc = c.name;
          if (c.description) desc += ` (${c.description})`;
          return desc;
        }).join(', ');
        
        return {
          success: true,
          message: `Las categorías de gastos disponibles son: ${categorias}.`,
          categorias: this.categories
        };
      });
      
      // Abrir modal de crear gasto
      this.uiContext.registerAction('abrirCrearGasto', () => {
        this.openCreateModal();
        return { success: true, message: 'Abriendo formulario de nuevo gasto' };
      });
      
      // Filtrar gastos por categoría
      this.uiContext.registerAction('filtrarPorCategoria', (params) => {
        const { categoria } = params;
        const cat = this.categories.find(c => 
          c.name.toLowerCase().includes(categoria.toLowerCase())
        );
        if (cat) {
          this.filters.category_id = cat.id;
          this.loadExpenses();
          return { success: true, message: `Filtrando por ${cat.name}` };
        }
        return { success: false, message: `No encontré la categoría "${categoria}"` };
      });
      
      // Limpiar filtros
      this.uiContext.registerAction('limpiarFiltros', () => {
        this.clearFilters();
        return { success: true, message: 'Filtros limpiados' };
      });
    },
    
    // Inferir categoría basada en palabras clave
    inferirCategoria(descripcion) {
      const desc = descripcion.toLowerCase();
      
      // Mapeo de palabras clave a categorías
      const keywords = {
        'Servicios Públicos': ['luz', 'agua', 'gas', 'internet', 'telefono', 'teléfono', 'electricidad', 'servicios', 'epm', 'claro', 'movistar'],
        'Nómina y Salarios': ['salario', 'nomina', 'nómina', 'sueldo', 'prestaciones', 'empleado', 'pago personal', 'trabajador'],
        'Mantenimiento': ['reparacion', 'reparación', 'arreglo', 'mantenimiento', 'técnico', 'tecnico', 'daño'],
        'Suministros y Materiales': ['papeleria', 'papelería', 'materiales', 'insumos', 'limpieza', 'aseo', 'oficina', 'bolsas', 'papel'],
        'Arriendo': ['arriendo', 'alquiler', 'renta', 'local', 'bodega'],
        'Transporte': ['transporte', 'taxi', 'uber', 'gasolina', 'combustible', 'envio', 'envío', 'domicilio', 'flete']
      };
      
      for (const [catName, words] of Object.entries(keywords)) {
        if (words.some(w => desc.includes(w))) {
          const cat = this.categories.find(c => c.name === catName);
          if (cat) return cat.id;
        }
      }
      
      // Por defecto, usar "Otros Gastos"
      const otros = this.categories.find(c => c.name.includes('Otros'));
      return otros?.id;
    },

    async loadExpenses() {
      this.loading = true;
      try {
        const params = {
          ...this.filters,
          page: this.filters.page
        };
        
        const response = await apiClient.get('/expenses', { params });
        
        if (response.data.success) {
          this.expenses = response.data.data;
        }
      } catch (error) {
        console.error('Error al cargar gastos:', error);
        this.$toast?.error('Error al cargar los gastos');
      } finally {
        this.loading = false;
      }
    },

    async loadCategories() {
      try {
        const response = await apiClient.get('/expenses/categories');
        if (response.data.success) {
          this.categories = response.data.data;
        }
      } catch (error) {
        console.error('Error al cargar categorías:', error);
      }
    },

    async loadStatistics() {
      try {
        const response = await apiClient.get('/expenses/statistics');
        if (response.data.success) {
          this.statistics = response.data.data;
          // Actualizar contexto IA con nuevos datos
          this.$nextTick(() => this.actualizarContextoIA());
        }
      } catch (error) {
        console.error('Error al cargar estadísticas:', error);
      }
    },

    async loadCashMovements() {
      try {
        const response = await apiClient.get('/cash-movements');
        if (response.data.success) {
          this.cashMovements = response.data.data || [];
          this.cashMovementsSummary = response.data.summary || { total_ingresos: 0, total_egresos: 0, count: 0 };
        }
      } catch (error) {
        console.error('Error al cargar movimientos de caja:', error);
      }
    },

    openCreateModal() {
      this.isEditing = false;
      this.entryMode = 'expense';
      this.resetForm();
      this.showModal = true;
    },

    openCashIncomeModal() {
      this.isEditing = false;
      this.entryMode = 'cash-income';
      this.resetCashMovementForm();
      this.showModal = true;
    },

    editExpense(expense) {
      this.isEditing = true;
      this.entryMode = 'expense';
      this.form = {
        id: expense.id,
        category_id: expense.category_id,
        amount: expense.amount,
        description: expense.description,
        payment_method: expense.payment_method,
        date: expense.date ? expense.date.split('T')[0] : '',
        receipt_number: expense.receipt_number || '',
        supplier: expense.supplier || '',
        notes: expense.notes || ''
      };
      this.showModal = true;
    },

    viewExpense(expense) {
      this.selectedExpense = expense;
      this.showViewModal = true;
    },

    confirmDelete(expense) {
      this.expenseToDelete = expense;
      this.showDeleteModal = true;
    },

    async deleteExpense() {
      if (!this.expenseToDelete) return;

      try {
        const response = await apiClient.delete(`/expenses/${this.expenseToDelete.id}`);
        if (response.data.success) {
          this.$toast?.success('Gasto eliminado exitosamente');
          this.showDeleteModal = false;
          this.expenseToDelete = null;
          await this.loadExpenses();
          await this.loadStatistics();
          await appStore.loadCashSession(true);
        }
      } catch (error) {
        console.error('Error al eliminar gasto:', error);
        this.$toast?.error(error.response?.data?.message || 'Error al eliminar el gasto');
      }
    },

    formatAmountInput(event) {
      // Permitir solo números, punto decimal y comas
      let value = event.target.value.replace(/[^\d.,]/g, '');
      
      // Reemplazar comas por puntos para formato decimal estándar
      value = value.replace(/,/g, '.');
      
      // Permitir solo un punto decimal
      const parts = value.split('.');
      if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
      }
      
      // Limitar decimales a 2
      if (parts.length === 2 && parts[1].length > 2) {
        value = parts[0] + '.' + parts[1].substring(0, 2);
      }
      
      // Actualizar el valor del input
      event.target.value = value;
      this.form.amount = value;
    },

    formatCashMovementAmountInput(event) {
      let value = event.target.value.replace(/[^\d.,]/g, '');
      value = value.replace(/,/g, '.');

      const parts = value.split('.');
      if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
      }

      if (parts.length === 2 && parts[1].length > 2) {
        value = parts[0] + '.' + parts[1].substring(0, 2);
      }

      event.target.value = value;
      this.cashMovementForm.amount = value;
    },

    async saveExpense() {
      // Validaciones frontend antes de enviar
      const errors = [];
      
      if (!this.form.category_id) {
        errors.push('Debe seleccionar una categoría');
      }

      if (!this.form.amount || parseFloat(this.form.amount) <= 0) {
        errors.push('El monto debe ser mayor a 0');
      }

      if (!this.form.description || this.form.description.trim() === '') {
        errors.push('La descripción es obligatoria');
      }

      if (!this.form.payment_method) {
        errors.push('Debe seleccionar un método de pago');
      }

      // Validar fuente si es efectivo
      if (this.form.payment_method === 'efectivo' && !this.form.expense_source) {
        errors.push('Debe seleccionar la fuente del gasto (caja actual o gasto general)');
      }

      // Mostrar todos los errores
      if (errors.length > 0) {
        this.$toast?.error(errors.join('. '));
        return;
      }

      this.saving = true;
      
      try {
        // Convertir amount a número antes de enviar
        const formData = {
          ...this.form,
          amount: parseFloat(this.form.amount) || 0
        };
        
        // Si el método NO es efectivo, no enviar expense_source
        if (formData.payment_method !== 'efectivo') {
          delete formData.expense_source;
        }
        
        const url = this.isEditing ? `/expenses/${formData.id}` : '/expenses';
        const method = this.isEditing ? 'put' : 'post';
        
        const response = await apiClient[method](url, formData);
        
        if (response.data.success) {
          this.$toast?.success(response.data.message || 'Gasto guardado exitosamente');
          this.closeModal();
          await this.loadExpenses();
          await this.loadStatistics();
          await appStore.loadCashSession(true);
        }
      } catch (error) {
        // Mostrar mensaje de error en toast
        let errorMessage = 'Error al guardar el gasto';
        
        if (error.response?.data?.message) {
          errorMessage = error.response.data.message;
        } else if (error.response?.data?.errors) {
          // Si hay múltiples errores de validación
          const errors = Object.values(error.response.data.errors).flat();
          errorMessage = errors.join('. ');
        } else if (error.message) {
          errorMessage = error.message;
        }
        
        this.$toast?.error(errorMessage);
      } finally {
        this.saving = false;
      }
    },

    async saveCashIncome() {
      const errors = [];

      if (!this.cashMovementForm.amount || parseFloat(this.cashMovementForm.amount) <= 0) {
        errors.push('El monto del ingreso debe ser mayor a 0');
      }

      if (!this.cashMovementForm.concept || this.cashMovementForm.concept.trim() === '') {
        errors.push('El concepto del ingreso es obligatorio');
      }

      if (errors.length > 0) {
        this.$toast?.error(errors.join('. '));
        return;
      }

      this.saving = true;

      try {
        const response = await apiClient.post('/cash-movements', {
          type: 'ingreso',
          amount: parseFloat(this.cashMovementForm.amount) || 0,
          concept: this.cashMovementForm.concept.trim(),
          reference: this.cashMovementForm.reference || null,
          notes: this.cashMovementForm.notes || null,
        });

        if (response.data.success) {
          this.$toast?.success(response.data.message || 'Ingreso registrado correctamente');
          this.closeModal();
          await appStore.loadCashSession(true);
          await this.loadCashMovements();
          this.activeTab = 'incomes';
        }
      } catch (error) {
        let errorMessage = 'Error al registrar el ingreso';

        if (error.response?.data?.message) {
          errorMessage = error.response.data.message;
        } else if (error.response?.data?.errors) {
          const errors = Object.values(error.response.data.errors).flat();
          errorMessage = errors.join('. ');
        } else if (error.message) {
          errorMessage = error.message;
        }

        this.$toast?.error(errorMessage);
      } finally {
        this.saving = false;
      }
    },

    closeModal() {
      this.showModal = false;
      this.resetForm();
      this.resetCashMovementForm();
      this.entryMode = 'expense';
    },

    resetForm() {
      this.form = {
        category_id: '',
        amount: '',
        description: '',
        payment_method: 'efectivo',
        expense_source: 'caja',
        date: '',
        receipt_number: '',
        supplier: '',
        notes: ''
      };
    },

    resetCashMovementForm() {
      this.cashMovementForm = {
        amount: '',
        concept: '',
        reference: '',
        notes: '',
      };
    },

    clearFilters() {
      this.filters = {
        search: '',
        category_id: '',
        payment_method: '',
        start_date: '',
        end_date: '',
        per_page: 15,
        page: 1
      };
      this.loadExpenses();
    },

    goToPage(page) {
      this.filters.page = page;
      this.loadExpenses();
    },

    formatNumber(value) {
      return new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(value || 0);
    },

    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },

    formatDateTime(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    getPaymentMethodTotal(method) {
      const data = this.statistics.by_payment_method?.find(item => item.payment_method === method);
      return data?.total || 0;
    },

    getPaymentMethodCount(method) {
      const data = this.statistics.by_payment_method?.find(item => item.payment_method === method);
      return data?.count || 0;
    }
  }
};
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}
</style>
