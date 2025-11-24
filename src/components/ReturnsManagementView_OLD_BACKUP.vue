<template>
  <div class="min-h-screen font-sans" style="background-color: #F4F6F8;">
    <div class="p-4 lg:p-6 space-y-4 animate-fade-in">
      
      <!-- Header Simple y Elegante -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-black rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Devoluciones</h1>
            <p class="text-sm text-gray-600">Sistema de devoluciones y reembolsos</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Exportar (Neutro) -->
          <button @click="exportReturns"
                  class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar</span>
          </button>
          
          <!-- Botón Actualizar (Primario - Negro) -->
          <button @click="loadReturns"
                  :disabled="loading"
                  class="px-5 py-2.5 bg-black hover:bg-slate-900 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg v-if="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>{{ loading ? 'Sincronizando' : 'Actualizar' }}</span>
          </button>
        </div>
      </div>

      <!-- Métricas Principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Total Devoluciones -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 hover:border-slate-300 transition-shadow duration-200 shadow-sm hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-sky-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-slate-700">Total Devoluciones</h3>
                <span class="text-xs font-medium text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">
                  TOTAL
                </span>
              </div>
              <p class="text-2xl font-bold text-slate-900 mb-1">{{ returnStats.totalReturns }}</p>
              <p class="text-sm text-slate-500">registradas</p>
            </div>
          </div>
        </div>

        <!-- Valor Total Devuelto -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 hover:border-slate-300 transition-shadow duration-200 shadow-sm hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-slate-700">Valor Devuelto</h3>
                <span class="text-xs font-medium text-red-700 bg-red-50 px-2 py-0.5 rounded-full">
                  MONTO
                </span>
              </div>
              <p class="text-2xl font-bold text-slate-900 mb-1">${{ formatCurrency(returnStats.totalAmount) }}</p>
              <p class="text-sm text-slate-500">total reembolsado</p>
            </div>
          </div>
        </div>

        <!-- Devoluciones Hoy -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 hover:border-slate-300 transition-shadow duration-200 shadow-sm hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-slate-700">Devoluciones Hoy</h3>
                <span class="text-xs font-medium text-orange-700 bg-orange-50 px-2 py-0.5 rounded-full">
                  HOY
                </span>
              </div>
              <p class="text-2xl font-bold text-slate-900 mb-1">{{ returnStats.todayReturns }}</p>
              <p class="text-sm text-slate-500">procesadas</p>
            </div>
          </div>
        </div>

        <!-- Devoluciones Este Mes -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 hover:border-slate-300 transition-shadow duration-200 shadow-sm hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-slate-700">Este Mes</h3>
                <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-0.5 rounded-full">
                  MES
                </span>
              </div>
              <p class="text-2xl font-bold text-slate-900 mb-1">{{ returnStats.monthReturns }}</p>
              <p class="text-sm text-slate-500">del período</p>
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
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar por número o cliente..."
            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        
        <!-- Estado -->
        <select
          v-model="statusFilter"
          class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-36">
          <option value="">Estado</option>
          <option value="pending">Pendientes</option>
          <option value="completed">Completadas</option>
          <option value="cancelled">Canceladas</option>
        </select>
        
        <!-- Método de Reembolso -->
        <select
          v-model="refundMethodFilter"
          class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-36">
          <option value="">Método</option>
          <option value="cash">Efectivo</option>
          <option value="card">Tarjeta</option>
          <option value="transfer">Transferencia</option>
          <option value="store_credit">Crédito</option>
        </select>
        
        <!-- Fecha Desde -->
        <input
          v-model="dateFrom"
          type="date"
          class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        
        <!-- Fecha Hasta -->
        <input
          v-model="dateTo"
          type="date"
          class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        
        <!-- Botón Limpiar Filtros -->
        <button
          @click="clearFilters"
          class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
          title="Limpiar filtros">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Tabla de devoluciones -->
    <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
      <!-- Header de tabla -->
      <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center justify-between">
        <div>
          <h2 class="text-base font-bold text-slate-900">Lista de Devoluciones</h2>
          <p class="text-xs text-slate-600 mt-0.5">{{ totalItems }} devoluciones encontradas</p>
        </div>
      </div>
      
      <!-- Tabla -->
      <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wide">
              Devolución
            </th>
            <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">
              Factura
            </th>
            <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">
              Cliente
            </th>
            <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">
              Fecha
            </th>
            <th class="px-3 py-2 text-right text-xs font-bold text-gray-600 uppercase tracking-wide">
              Total
            </th>
            <th class="px-3 py-2 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">
              Estado
            </th>
            <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">
              Método
            </th>
            <th class="px-3 py-2 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
          <tr v-for="returnItem in paginatedReturns" :key="returnItem.id" class="hover:bg-slate-50/50 transition-colors">
            <td class="px-6 py-4">
              <div class="text-sm font-semibold text-slate-900">
                {{ returnItem.number }}
              </div>
              <div class="text-xs text-slate-500">
                Caja #{{ returnItem.cash_session?.id || 'N/A' }}
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-700">
                {{ returnItem.original_invoice?.number || 'N/A' }}
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm font-medium text-slate-900">
                {{ returnItem.customer?.name || 'Cliente General' }}
              </div>
              <div class="text-xs text-slate-500">
                {{ returnItem.user?.name || 'N/A' }}
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-700">
                {{ formatDate(returnItem.return_date) }}
              </div>
            </td>
            <td class="px-6 py-4 text-right">
              <div class="text-base font-black text-red-600">
                ${{ formatCurrency(returnItem.total) }}
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <span :class="[
                'px-3 py-1 rounded-full text-xs font-semibold inline-block',
                returnItem.status === 'completed' ? 'bg-emerald-100 text-emerald-700' :
                returnItem.status === 'pending' ? 'bg-amber-100 text-amber-700' :
                'bg-rose-100 text-rose-700'
              ]">
                {{ getStatusLabel(returnItem.status) }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-700">
                {{ getRefundMethodLabel(returnItem.refund_method) }}
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-1.5">
                <button
                  @click="viewReturnDetails(returnItem)"
                  class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg transition-all hover:shadow-sm"
                  title="Ver detalles">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
                <button
                  v-if="returnItem.status === 'pending'"
                  @click="cancelReturn(returnItem)"
                  class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-all hover:shadow-sm"
                  title="Cancelar">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          
          <!-- Sin resultados -->
          <tr v-if="filteredReturns.length === 0">
            <td colspan="8" class="px-6 py-12 text-center">
              <div class="flex flex-col items-center space-y-3">
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center">
                  <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-700">No hay devoluciones</p>
                  <p class="text-xs text-slate-500 mt-1">No se encontraron devoluciones con los filtros actuales</p>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Paginador -->
      <TablePaginator
        v-if="filteredReturns.length > 0"
        v-model:currentPage="currentPage"
        v-model:itemsPerPage="itemsPerPage"
        :totalPages="totalPages"
        :totalItems="totalItems"
        label="devoluciones"
      />
    </div>

    <!-- Modal de detalles -->
    <div v-if="showDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
        <!-- Header del modal -->
        <div class="bg-white border-b border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-base font-bold text-gray-900">
                  Devolución {{ selectedReturn?.number }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                  {{ formatDate(selectedReturn?.return_date) }}
                </p>
              </div>
            </div>
            <button @click="closeDetailsModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Contenido del modal -->
        <div class="p-4 overflow-y-auto max-h-[calc(90vh-120px)]" v-if="selectedReturn">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            
            <!-- Contenido Principal (2/3) -->
            <div class="lg:col-span-2 space-y-4">
              <!-- Productos devueltos -->
              <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-3 py-2">
                  <h4 class="text-sm font-bold text-gray-900">Productos Devueltos</h4>
                </div>
                <div class="p-3">
                  <div class="space-y-2 max-h-96 overflow-y-auto">
                    <div
                      v-for="item in selectedReturn.return_items"
                      :key="item.id"
                      class="bg-gray-50 p-2 rounded-lg border border-gray-200">
                      <div class="flex justify-between items-start">
                        <div class="flex-1">
                          <h5 class="text-sm font-semibold text-gray-900">
                            {{ item.product?.name || 'Producto N/A' }}
                          </h5>
                          <p class="text-xs text-gray-500">
                            Código: {{ item.product?.code || 'N/A' }}
                          </p>
                        </div>
                        <div class="text-right">
                          <p class="text-sm font-bold text-gray-900">
                            x{{ item.quantity }}
                          </p>
                          <p class="text-xs text-gray-500">
                            ${{ formatCurrency(item.unit_price) }}
                          </p>
                        </div>
                      </div>
                      <div class="mt-2 flex justify-between items-center border-t border-gray-200 pt-2">
                        <span class="text-xs font-medium text-gray-600">
                          Subtotal:
                        </span>
                        <span class="text-sm font-bold text-gray-900">
                          ${{ formatCurrency(item.subtotal) }}
                        </span>
                      </div>
                      <div v-if="item.reason" class="mt-2 bg-yellow-50 p-2 rounded border border-yellow-200">
                        <p class="text-xs font-medium text-yellow-800">Motivo:</p>
                        <p class="text-xs text-yellow-700">{{ item.reason }}</p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Totales integrados en el footer -->
                <div class="border-t bg-gray-50 px-3 py-3">
                  <div class="flex justify-end">
                    <div class="space-y-2 min-w-64">
                      <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold text-gray-900">${{ formatCurrency(selectedReturn.subtotal) }}</span>
                      </div>
                      <div class="flex justify-between text-sm">
                        <span class="text-gray-600">IVA:</span>
                        <span class="font-semibold text-gray-900">${{ formatCurrency(selectedReturn.tax_amount) }}</span>
                      </div>
                      <div class="border-t-2 border-gray-300 pt-2">
                        <div class="flex justify-between">
                          <span class="text-sm font-bold text-gray-900">Total Devuelto:</span>
                          <span class="text-xl font-bold text-red-600">${{ formatCurrency(selectedReturn.total) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Motivos y notas -->
              <div v-if="selectedReturn.reason || selectedReturn.notes" class="space-y-3">
                <div v-if="selectedReturn.reason" class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg">
                  <h4 class="text-sm font-bold text-yellow-800 mb-1">Motivo de la Devolución</h4>
                  <p class="text-xs text-yellow-700">{{ selectedReturn.reason }}</p>
                </div>
                
                <div v-if="selectedReturn.notes" class="bg-blue-50 border border-blue-200 p-3 rounded-lg">
                  <h4 class="text-sm font-bold text-blue-800 mb-1">Notas Adicionales</h4>
                  <p class="text-xs text-blue-700">{{ selectedReturn.notes }}</p>
                </div>
              </div>
            </div>
            
            <!-- Sidebar (1/3) -->
            <div class="space-y-4">
              <!-- Información general -->
              <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-3 py-2">
                  <h4 class="text-sm font-bold text-gray-900">Información General</h4>
                </div>
                <div class="p-3 space-y-2">
                  <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Número:</span>
                    <span class="font-semibold text-gray-900">{{ selectedReturn.number }}</span>
                  </div>
                  <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Fecha:</span>
                    <span class="font-semibold text-gray-900">{{ formatDate(selectedReturn.return_date) }}</span>
                  </div>
                  <div class="flex justify-between text-xs items-center">
                    <span class="text-gray-600">Estado:</span>
                    <span :class="[
                      'px-3 py-1 rounded-full text-xs font-semibold',
                      selectedReturn.status === 'completed' ? 'bg-emerald-100 text-emerald-700' :
                      selectedReturn.status === 'pending' ? 'bg-amber-100 text-amber-700' :
                      'bg-rose-100 text-rose-700'
                    ]">
                      {{ getStatusLabel(selectedReturn.status) }}
                    </span>
                  </div>
                  <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Método:</span>
                    <span class="font-semibold text-gray-900">{{ getRefundMethodLabel(selectedReturn.refund_method) }}</span>
                  </div>
                  <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Factura:</span>
                    <span class="font-semibold text-gray-900">{{ selectedReturn.original_invoice?.number || 'N/A' }}</span>
                  </div>
                </div>
              </div>

              <!-- Cliente -->
              <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-3 py-2">
                  <h4 class="text-sm font-bold text-gray-900">Cliente</h4>
                </div>
                <div class="p-3 space-y-2">
                  <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Nombre:</span>
                    <span class="font-semibold text-gray-900">{{ selectedReturn.customer?.name || 'Cliente General' }}</span>
                  </div>
                  <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-semibold text-gray-900">{{ selectedReturn.customer?.email || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Teléfono:</span>
                    <span class="font-semibold text-gray-900">{{ selectedReturn.customer?.phone || 'N/A' }}</span>
                  </div>
                </div>
              </div>

              <!-- Personal y Sesión -->
              <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-3 py-2">
                  <h4 class="text-sm font-bold text-gray-900">Personal y Sesión</h4>
                </div>
                <div class="p-3 space-y-2">
                  <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Cajero:</span>
                    <span class="font-semibold text-gray-900">{{ selectedReturn.user?.name || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Sesión:</span>
                    <span class="font-semibold text-gray-900">Caja #{{ selectedReturn.cash_session?.id || 'N/A' }}</span>
                  </div>
                  <div v-if="selectedReturn.cash_session?.opening_date" class="flex justify-between text-xs">
                    <span class="text-gray-600">Fecha Sesión:</span>
                    <span class="font-semibold text-gray-900">{{ formatDate(selectedReturn.cash_session.opening_date) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="bg-gray-50 border-t border-gray-200 px-4 py-3 flex justify-between">
          <div>
            <button
              v-if="selectedReturn?.status === 'pending'"
              @click="cancelReturn(selectedReturn)"
              class="px-3 py-2 bg-white hover:bg-red-50 text-red-600 border border-red-300 rounded-lg text-sm font-medium transition-colors">
              Cancelar Devolución
            </button>
          </div>
          <button
            @click="closeDetailsModal"
            class="px-3 py-2 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 rounded-lg text-sm font-medium transition-colors">
            Cerrar
          </button>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useToast } from '../composables/useToast.js'
import returnsService from '../services/returnsService.js'
import TablePaginator from './TablePaginator.vue'

// Composables
const { showToast } = useToast()

// Estado reactivo
const loading = ref(false)
const returns = ref([])
const returnStats = ref({
  totalReturns: 0,
  totalAmount: 0,
  todayReturns: 0,
  monthReturns: 0
})

// Filtros
const searchTerm = ref('')
const statusFilter = ref('')
const refundMethodFilter = ref('')
const dateFrom = ref('')
const dateTo = ref('')

// Paginación
const currentPage = ref(1)
const itemsPerPage = ref(25)
const totalItems = ref(0)

// Modal de detalles
const showDetailsModal = ref(false)
const selectedReturn = ref(null)

// Computed properties
const filteredReturns = computed(() => {
  let filtered = returns.value

  // Filtro de búsqueda
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(returnItem => 
      (returnItem.number || '').toLowerCase().includes(term) ||
      (returnItem.customer?.name || '').toLowerCase().includes(term) ||
      (returnItem.customer?.email || '').toLowerCase().includes(term) ||
      (returnItem.original_invoice?.number || '').toLowerCase().includes(term)
    )
  }

  // Filtro por estado
  if (statusFilter.value) {
    filtered = filtered.filter(returnItem => returnItem.status === statusFilter.value)
  }

  // Filtro por método de reembolso
  if (refundMethodFilter.value) {
    filtered = filtered.filter(returnItem => returnItem.refund_method === refundMethodFilter.value)
  }

  // Filtro por fechas
  if (dateFrom.value) {
    filtered = filtered.filter(returnItem => returnItem.return_date >= dateFrom.value)
  }
  
  if (dateTo.value) {
    filtered = filtered.filter(returnItem => returnItem.return_date <= dateTo.value)
  }

  totalItems.value = filtered.length
  return filtered
})

const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage.value))

const paginatedReturns = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredReturns.value.slice(start, end)
})

// Métodos
const loadReturns = async () => {
  loading.value = true
  try {
    const params = {
      per_page: 1000
    }

    const response = await returnsService.getReturns(params)
    
    // Manejar tanto estructura paginada como directa
    let returnsList = []
    if (response.data && response.data.data) {
      returnsList = response.data.data
    } else if (response.data && Array.isArray(response.data)) {
      returnsList = response.data
    } else if (response.success && response.data) {
      if (Array.isArray(response.data.data)) {
        returnsList = response.data.data
      } else if (Array.isArray(response.data)) {
        returnsList = response.data
      }
    }
    
    // Procesar cada devolución para parsear items JSON
    returns.value = returnsList.map(returnItem => {
      // Si items es string, parsear a JSON
      if (returnItem.items && typeof returnItem.items === 'string') {
        try {
          returnItem.items = JSON.parse(returnItem.items)
        } catch (e) {
          console.error('Error parsing items JSON:', e)
          returnItem.items = []
        }
      }
      
      // Si items no es array, convertirlo
      if (!Array.isArray(returnItem.items)) {
        returnItem.items = []
      }
      
      // Crear return_items a partir de items para compatibilidad con el template
      returnItem.return_items = returnItem.items
      
      return returnItem
    })
    
    calculateStats()
    showToast('Devoluciones cargadas exitosamente', 'success')
  } catch (error) {
    console.error('Error loading returns:', error)
    showToast(error.message || 'Error al cargar devoluciones', 'error')
    returns.value = []
  } finally {
    loading.value = false
  }
}

const calculateStats = () => {
  const today = new Date().toISOString().split('T')[0]
  const thisMonth = new Date().toISOString().slice(0, 7)

  returnStats.value = {
    totalReturns: returns.value.length,
    totalAmount: returns.value.reduce((sum, returnItem) => sum + parseFloat(returnItem.total || 0), 0),
    todayReturns: returns.value.filter(returnItem => {
      // Normalizar fecha del item (soporta múltiples formatos)
      let itemDate = returnItem.return_date || ''
      
      // Si es objeto Date o tiene formato ISO
      if (typeof itemDate === 'object' || itemDate.includes('T')) {
        itemDate = new Date(itemDate).toISOString().split('T')[0]
      } else {
        // Si es string 'YYYY-MM-DD HH:MM:SS', tomar solo la fecha
        itemDate = itemDate.split(' ')[0]
      }
      
      return itemDate === today
    }).length,
    monthReturns: returns.value.filter(returnItem => {
      let itemDate = returnItem.return_date || ''
      
      if (typeof itemDate === 'object' || itemDate.includes('T')) {
        itemDate = new Date(itemDate).toISOString().split('T')[0]
      } else {
        itemDate = itemDate.split(' ')[0]
      }
      
      return itemDate.startsWith(thisMonth)
    }).length
  }
}

const viewReturnDetails = async (returnItem) => {
  try {
    loading.value = true
    const response = await returnsService.getReturnDetails(returnItem.id)
    
    // El backend ya devuelve return_items con productos incluidos
    selectedReturn.value = response.data
    showDetailsModal.value = true
  } catch (error) {
    console.error('Error loading return details:', error)
    showToast(error.message || 'Error al cargar detalles', 'error')
  } finally {
    loading.value = false
  }
}

const closeDetailsModal = () => {
  showDetailsModal.value = false
  selectedReturn.value = null
}

const cancelReturn = async (returnItem) => {
  if (!confirm('¿Está seguro de cancelar esta devolución?')) return

  try {
    loading.value = true
    await returnsService.cancelReturn(returnItem.id)
    showToast('Devolución cancelada exitosamente', 'success')
    await loadReturns()
    closeDetailsModal()
  } catch (error) {
    console.error('Error canceling return:', error)
    showToast(error.message || 'Error al cancelar devolución', 'error')
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  searchTerm.value = ''
  statusFilter.value = ''
  refundMethodFilter.value = ''
  dateFrom.value = ''
  dateTo.value = ''
  currentPage.value = 1
}

const exportReturns = () => {
  const csvContent = [
    ['Número', 'Factura Original', 'Cliente', 'Fecha', 'Total', 'Estado', 'Método', 'Motivo'].join(','),
    ...filteredReturns.value.map(returnItem => [
      returnItem.number,
      returnItem.original_invoice?.number || 'N/A',
      returnItem.customer?.name || 'Cliente General',
      formatDate(returnItem.return_date),
      returnItem.total,
      getStatusLabel(returnItem.status),
      getRefundMethodLabel(returnItem.refund_method),
      returnItem.reason || ''
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `devoluciones_${new Date().toISOString().slice(0, 10)}.csv`
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  window.URL.revokeObjectURL(url)

  showToast('Reporte exportado exitosamente', 'success')
}

// Utilidades
const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO').format(value)
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  
  try {
    // Manejar diferentes formatos de fecha
    let date
    
    if (typeof dateString === 'string') {
      // Si es formato YYYY-MM-DD, agregar hora local
      if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
        date = new Date(dateString + 'T12:00:00')
      } else {
        date = new Date(dateString)
      }
    } else {
      date = new Date(dateString)
    }
    
    // Verificar si la fecha es válida
    if (isNaN(date.getTime())) return '-'
    
    const day = date.getDate()
    const month = date.getMonth() + 1
    const year = date.getFullYear()
    
    return `${day.toString().padStart(2, '0')}/${month.toString().padStart(2, '0')}/${year}`
  } catch (error) {
    console.error('Error formatting date:', error, dateString)
    return '-'
  }
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pendiente',
    completed: 'Completada',
    cancelled: 'Cancelada'
  }
  return labels[status] || status
}

const getRefundMethodLabel = (method) => {
  const labels = {
    cash: 'Efectivo',
    card: 'Tarjeta',
    transfer: 'Transferencia',
    store_credit: 'Crédito'
  }
  return labels[method] || method
}

// Watchers
watch([searchTerm, statusFilter, refundMethodFilter, dateFrom, dateTo], () => {
  currentPage.value = 1
}, { deep: true })

// Lifecycle
onMounted(() => {
  loadReturns()
})
</script>
