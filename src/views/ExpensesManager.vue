<template>
  <div class="min-h-screen bg-gray-100 font-sans">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      
      <!-- Header Elegante -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gradient-to-br from-orange-600 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Gastos Operativos</h1>
            <p class="text-sm text-gray-600">Gestiona los gastos del negocio</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Actualizar -->
          <button @click="loadExpenses" 
                  class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Actualizar</span>
          </button>
          
          <!-- Botón Registrar Gasto -->
          <button @click="openCreateModal" 
                  class="px-5 py-2.5 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Registrar Nuevo Gasto</span>
          </button>
        </div>
      </div>
      
      <!-- Métricas Principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Gastos Mes -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Total Mes Actual</h3>
                <span v-if="statistics.percentage_change !== undefined" 
                      :class="[
                        'text-xs font-medium px-2 py-1 rounded-lg border',
                        statistics.percentage_change > 0 ? 'text-red-700 bg-red-50 border-red-200' : 'text-green-700 bg-green-50 border-green-200'
                      ]">
                  {{ statistics.percentage_change > 0 ? '+' : '' }}{{ statistics.percentage_change }}%
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ formatNumber(statistics.current_month || 0) }}</p>
              <p class="text-sm text-gray-500">{{ expenses.total || 0 }} gastos</p>
            </div>
          </div>
        </div>

        <!-- Efectivo -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-semibold text-gray-700 mb-2">En Efectivo</h3>
              <p class="text-2xl font-bold text-gray-900 mb-1">
                ${{ formatNumber(getPaymentMethodTotal('efectivo')) }}
              </p>
              <p class="text-sm text-gray-500">{{ getPaymentMethodCount('efectivo') }} gastos</p>
            </div>
          </div>
        </div>

        <!-- Transferencia -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-semibold text-gray-700 mb-2">Transferencia</h3>
              <p class="text-2xl font-bold text-gray-900 mb-1">
                ${{ formatNumber(getPaymentMethodTotal('transferencia')) }}
              </p>
              <p class="text-sm text-gray-500">{{ getPaymentMethodCount('transferencia') }} gastos</p>
            </div>
          </div>
        </div>

        <!-- Tarjeta -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-semibold text-gray-700 mb-2">Tarjeta</h3>
              <p class="text-2xl font-bold text-gray-900 mb-1">
                ${{ formatNumber(getPaymentMethodTotal('tarjeta')) }}
              </p>
              <p class="text-sm text-gray-500">{{ getPaymentMethodCount('tarjeta') }} gastos</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded-lg shadow-sm p-3 border border-gray-200">
        <div class="flex flex-wrap items-center gap-3">
          <!-- Búsqueda -->
          <div class="flex-1 min-w-48 relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input v-model="filters.search" 
                   @input="loadExpenses"
                   type="text" 
                   placeholder="Buscar por descripción, proveedor o recibo..."
                   class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          
          <!-- Filtro por Categoría -->
          <select v-model="filters.category_id" 
                  @change="loadExpenses"
                  class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-36">
            <option value="">Todas las Categorías</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
          
          <!-- Filtro por Método de Pago -->
          <select v-model="filters.payment_method" 
                  @change="loadExpenses"
                  class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-36">
            <option value="">Todos los Métodos</option>
            <option value="efectivo">Efectivo</option>
            <option value="transferencia">Transferencia</option>
            <option value="tarjeta">Tarjeta</option>
          </select>
          
          <!-- Fecha Inicio -->
          <input v-model="filters.start_date" 
                 @change="loadExpenses"
                 type="date" 
                 class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
          
          <!-- Fecha Fin -->
          <input v-model="filters.end_date" 
                 @change="loadExpenses"
                 type="date" 
                 class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
          
          <!-- Botón Limpiar Filtros -->
          <button @click="clearFilters" 
                  class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                  title="Limpiar filtros">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Tabla de Gastos -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 px-4 py-3 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900">Lista de Gastos</h2>
            <p class="text-xs text-gray-500 mt-0.5">
              {{ expenses.total || 0 }} gastos registrados
            </p>
          </div>
        </div>
        
        <div v-if="loading" class="px-4 py-12 text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <p class="text-sm text-gray-500 mt-3">Cargando gastos...</p>
        </div>

        <div v-else-if="expenses.data && expenses.data.length === 0" class="px-4 py-12 text-center">
          <div class="flex flex-col items-center space-y-3">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-700">No hay gastos registrados</p>
              <p class="text-xs text-gray-500 mt-1">Comienza registrando tu primer gasto</p>
            </div>
            <button @click="openCreateModal" 
                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
              Registrar Gasto
            </button>
          </div>
        </div>

        <table v-else class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Fecha</th>
              <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Categoría</th>
              <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Descripción</th>
              <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Monto</th>
              <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Método</th>
              <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Usuario</th>
              <th class="px-3 py-2 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="expense in expenses.data" :key="expense.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-3 py-3 text-sm text-gray-900">
                {{ formatDate(expense.date) }}
              </td>
              <td class="px-3 py-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                      :style="{ backgroundColor: expense.category?.color + '20', color: expense.category?.color }">
                  {{ expense.category?.name }}
                </span>
              </td>
              <td class="px-3 py-3 text-sm text-gray-700">
                <div class="max-w-xs truncate">{{ expense.description }}</div>
                <div v-if="expense.supplier" class="text-xs text-gray-500 mt-0.5">
                  {{ expense.supplier }}
                </div>
              </td>
              <td class="px-3 py-3 text-sm font-semibold text-gray-900">
                ${{ formatNumber(expense.amount) }}
              </td>
              <td class="px-3 py-3">
                <span :class="[
                  'px-2 py-0.5 rounded-full text-xs font-semibold',
                  expense.payment_method === 'efectivo' ? 'bg-green-100 text-green-700' :
                  expense.payment_method === 'transferencia' ? 'bg-blue-100 text-blue-700' :
                  'bg-purple-100 text-purple-700'
                ]">
                  {{ expense.payment_method }}
                </span>
              </td>
              <td class="px-3 py-3 text-sm text-gray-600">
                {{ expense.user?.name }}
              </td>
              <td class="px-3 py-3 text-center">
                <div class="flex items-center justify-center space-x-1">
                  <button @click="viewExpense(expense)" 
                          class="p-1.5 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg transition-colors"
                          title="Ver detalles">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                  <button @click="editExpense(expense)" 
                          class="p-1.5 bg-orange-100 hover:bg-orange-200 text-orange-600 rounded-lg transition-colors"
                          title="Editar">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button @click="deleteExpense(expense)" 
                          class="p-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors"
                          title="Eliminar">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Paginación -->
        <div v-if="expenses.data && expenses.data.length > 0" 
             class="bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
              <span class="text-xs font-medium text-gray-700">Mostrar:</span>
              <select v-model="filters.per_page" 
                      @change="loadExpenses"
                      class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
              <span class="text-xs text-gray-700">por página</span>
            </div>
            
            <div class="text-xs text-gray-700">
              Mostrando {{ expenses.from }} a {{ expenses.to }} de {{ expenses.total }} registros
            </div>
          </div>
          
          <div class="flex items-center space-x-1">
            <button @click="goToPage(1)" 
                    :disabled="expenses.current_page === 1"
                    class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
              </svg>
            </button>
            
            <button @click="goToPage(expenses.current_page - 1)" 
                    :disabled="expenses.current_page === 1"
                    class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            
            <div class="flex items-center space-x-1">
              <span class="px-2.5 py-1 text-xs font-medium bg-blue-600 text-white border border-blue-600 rounded-lg">
                {{ expenses.current_page }}
              </span>
              <span class="text-xs text-gray-500">de {{ expenses.last_page }}</span>
            </div>
            
            <button @click="goToPage(expenses.current_page + 1)" 
                    :disabled="expenses.current_page === expenses.last_page"
                    class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            
            <button @click="goToPage(expenses.last_page)" 
                    :disabled="expenses.current_page === expenses.last_page"
                    class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Crear/Editar Gasto -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header Modal -->
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center shadow-sm">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-gray-900">
                {{ isEditing ? 'Editar Gasto' : 'Registrar Nuevo Gasto' }}
              </h3>
              <p class="text-xs text-gray-500">Complete la información del gasto operativo</p>
            </div>
          </div>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Body Modal con Layout Horizontal -->
        <form @submit.prevent="saveExpense" class="flex-1 overflow-y-auto">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
            
            <!-- Columna Izquierda: Información Principal (2/3) -->
            <div class="lg:col-span-2 space-y-5">
              <!-- Sección: Información Básica -->
              <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="flex items-center space-x-2 mb-4 pb-3 border-b border-gray-200">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <h4 class="text-sm font-bold text-gray-900">Información del Gasto</h4>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                  <!-- Categoría -->
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Categoría *</label>
                    <select v-model="form.category_id" 
                            required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                      <option value="">Seleccionar categoría</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                  </div>

                  <!-- Monto -->
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Monto *</label>
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">$</span>
                      <input v-model="form.amount" 
                             type="text"
                             inputmode="decimal"
                             @input="formatAmountInput"
                             required
                             placeholder="0.00"
                             class="w-full pl-8 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                  </div>
                </div>

                <!-- Descripción -->
                <div class="mt-4">
                  <label class="block text-xs font-semibold text-gray-700 mb-2">Descripción del Gasto *</label>
                  <textarea v-model="form.description" 
                            rows="3"
                            required
                            placeholder="Describe el propósito y detalles del gasto..."
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all"></textarea>
                </div>
              </div>

              <!-- Sección: Información de Pago -->
              <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="flex items-center space-x-2 mb-4 pb-3 border-b border-gray-200">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                  </svg>
                  <h4 class="text-sm font-bold text-gray-900">Método de Pago</h4>
                </div>

                <div class="space-y-4">
                  <!-- Método de Pago -->
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Forma de Pago *</label>
                    <select v-model="form.payment_method" 
                            required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                      <option value="efectivo">Efectivo</option>
                      <option value="transferencia">Transferencia Bancaria</option>
                      <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                    </select>
                  </div>

                  <!-- Fuente del Gasto (solo para efectivo) -->
                  <div v-if="form.payment_method === 'efectivo'">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Fuente *</label>
                    <div class="space-y-2">
                      <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" 
                               v-model="form.expense_source" 
                               name="expense_source"
                               value="caja"
                               required
                               class="w-4 h-4 text-blue-600">
                        <span class="text-sm text-gray-700">Descontar de caja actual</span>
                      </label>
                      <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" 
                               v-model="form.expense_source" 
                               name="expense_source"
                               value="general"
                               required
                               class="w-4 h-4 text-blue-600">
                        <span class="text-sm text-gray-700">Gasto general</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Columna Derecha: Información Complementaria (1/3) -->
            <div class="space-y-5">
              <!-- Sección: Detalles Adicionales -->
              <div class="bg-gradient-to-br from-gray-50 to-slate-50 border border-gray-200 rounded-xl p-5">
                <div class="flex items-center space-x-2 mb-4 pb-3 border-b border-gray-200">
                  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                  <h4 class="text-sm font-bold text-gray-900">Información Complementaria</h4>
                </div>

                <div class="space-y-4">
                  <!-- Fecha -->
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Fecha</label>
                    <input v-model="form.date" 
                           type="date"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                  </div>

                  <!-- Proveedor -->
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Proveedor</label>
                    <input v-model="form.supplier" 
                           type="text"
                           placeholder="Nombre del proveedor"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                  </div>

                  <!-- Número de Recibo -->
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Nº Recibo</label>
                    <input v-model="form.receipt_number" 
                           type="text"
                           placeholder="FAC-001234"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                  </div>

                  <!-- Notas Adicionales -->
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Notas</label>
                    <textarea v-model="form.notes" 
                              rows="3"
                              placeholder="Información adicional..."
                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer Modal dentro del form -->
          <div class="bg-gradient-to-r from-gray-50 to-slate-50 border-t border-gray-200 px-6 py-4 flex justify-between items-center flex-shrink-0">
            <p class="text-xs text-gray-500">
              <span class="font-semibold">*</span> Campos obligatorios
            </p>
            <div class="flex space-x-3">
              <button @click="closeModal" 
                      type="button"
                      class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all">
                Cancelar
              </button>
              <button type="submit"
                      :disabled="saving"
                      class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-semibold rounded-lg shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
                <svg v-if="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ saving ? 'Guardando...' : (isEditing ? 'Actualizar Gasto' : 'Guardar Gasto') }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Ver Detalles -->
    <div v-if="showViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
          <h3 class="text-base font-bold text-gray-900">Detalles del Gasto</h3>
          <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div v-if="selectedExpense" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-xs text-gray-500 mb-1">Categoría</p>
              <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-semibold"
                    :style="{ backgroundColor: selectedExpense.category?.color + '20', color: selectedExpense.category?.color }">
                {{ selectedExpense.category?.name }}
              </span>
            </div>
            <div>
              <p class="text-xs text-gray-500 mb-1">Monto</p>
              <p class="text-lg font-bold text-gray-900">${{ formatNumber(selectedExpense.amount) }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 mb-1">Método de Pago</p>
              <p class="text-sm font-medium text-gray-900">{{ selectedExpense.payment_method }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 mb-1">Fecha</p>
              <p class="text-sm font-medium text-gray-900">{{ formatDate(selectedExpense.date) }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 mb-1">Registrado por</p>
              <p class="text-sm font-medium text-gray-900">{{ selectedExpense.user?.name }}</p>
            </div>
            <div v-if="selectedExpense.supplier">
              <p class="text-xs text-gray-500 mb-1">Proveedor</p>
              <p class="text-sm font-medium text-gray-900">{{ selectedExpense.supplier }}</p>
            </div>
            <div v-if="selectedExpense.receipt_number">
              <p class="text-xs text-gray-500 mb-1">Número de Recibo</p>
              <p class="text-sm font-medium text-gray-900">{{ selectedExpense.receipt_number }}</p>
            </div>
          </div>

          <div>
            <p class="text-xs text-gray-500 mb-1">Descripción</p>
            <p class="text-sm text-gray-900">{{ selectedExpense.description }}</p>
          </div>

          <div v-if="selectedExpense.notes">
            <p class="text-xs text-gray-500 mb-1">Notas</p>
            <p class="text-sm text-gray-900">{{ selectedExpense.notes }}</p>
          </div>

          <div v-if="selectedExpense.cash_session_id" class="bg-blue-50 border border-blue-200 rounded-lg p-3">
            <p class="text-xs text-blue-700">
              <strong>Vinculado a Sesión de Caja:</strong> #{{ selectedExpense.cash_session_id }}
            </p>
          </div>
        </div>

        <div class="bg-gray-50 border-t px-6 py-3 flex justify-end rounded-b-2xl">
          <button @click="showViewModal = false" 
                  class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import apiClient from '@/services/apiClient';

export default {
  name: 'ExpensesManager',
  data() {
    return {
      loading: false,
      saving: false,
      showModal: false,
      showViewModal: false,
      isEditing: false,
      expenses: {
        data: [],
        total: 0,
        current_page: 1,
        last_page: 1,
        from: 0,
        to: 0
      },
      categories: [],
      statistics: {},
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
      selectedExpense: null
    };
  },
  mounted() {
    this.loadCategories();
    this.loadExpenses();
    this.loadStatistics();
  },
  methods: {
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
        }
      } catch (error) {
        console.error('Error al cargar estadísticas:', error);
      }
    },

    openCreateModal() {
      this.isEditing = false;
      this.resetForm();
      this.showModal = true;
    },

    editExpense(expense) {
      this.isEditing = true;
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

    async deleteExpense(expense) {
      if (!confirm(`¿Estás seguro de eliminar el gasto de $${this.formatNumber(expense.amount)}?`)) {
        return;
      }

      try {
        const response = await apiClient.delete(`/expenses/${expense.id}`);
        if (response.data.success) {
          this.$toast?.success('Gasto eliminado exitosamente');
          this.loadExpenses();
          this.loadStatistics();
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
          this.loadExpenses();
          this.loadStatistics();
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

    closeModal() {
      this.showModal = false;
      this.resetForm();
    },

    resetForm() {
      this.form = {
        category_id: '',
        amount: '',
        description: '',
        payment_method: 'efectivo',
        date: '',
        receipt_number: '',
        supplier: '',
        notes: ''
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
