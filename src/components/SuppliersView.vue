<template>
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header -->
      <div class="flex items-center justify-between pb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Compr</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Control de proveedores y órdenes de compra</p>
        </div>
        
        <div class="flex items-center space-x-3">
          <button @click="refreshCurrentTab" class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Refrescar</span>
          </button>
          <button v-if="activeTab === 'suppliers'" @click="showSupplierModal = true" class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Nuevo Proveedor</span>
          </button>
          <button v-if="activeTab === 'orders'" @click="showOrderModal = true" class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Nueva Orden</span>
          </button>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl border border-gray-300 dark:border-white/5 p-1 inline-flex">
        <button 
          @click="activeTab = 'suppliers'" 
          :class="[
            'px-6 py-2.5 text-sm font-bold rounded-lg transition-all duration-200',
            activeTab === 'suppliers' 
              ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-md' 
              : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
          ]"
        >
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>Proveedores</span>
          </div>
        </button>
        <button 
          @click="activeTab = 'orders'" 
          :class="[
            'px-6 py-2.5 text-sm font-bold rounded-lg transition-all duration-200',
            activeTab === 'orders' 
              ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-md' 
              : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
          ]"
        >
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span>Órdenes de Compra</span>
          </div>
        </button>
      </div>

      <!-- TAB: PROVEEDORES -->
      <div v-if="activeTab === 'suppliers'">
      <!-- Métricas con Glassmorphism -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:hover:shadow-lg dark:hover:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-blue-50 dark:bg-blue-950 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Proveedores</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ summary.total_suppliers || 0 }}</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">{{ summary.active_suppliers || 0 }} activos</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:hover:shadow-lg dark:hover:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-red-50 dark:bg-red-950 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Cuentas por Pagar</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatNumber(summary.total_debt || 0) }}</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Deuda total</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:hover:shadow-lg dark:hover:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-950 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Mejor Proveedor</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white mt-0.5 truncate" :title="summary.best_supplier?.name">
                {{ summary.best_supplier?.name || 'N/A' }}
              </p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1" v-if="summary.best_supplier">
                ${{ formatNumber(summary.best_supplier.total_purchases) }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de Proveedores -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-5 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Lista de Proveedores</h2>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Información de contacto y métricas de compra</p>
          </div>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-200 dark:border-zinc-700 border-t-blue-600 dark:border-t-blue-400"></div>
        </div>

        <div v-else-if="suppliers.length === 0" class="text-center py-12">
          <div class="w-16 h-16 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <p class="text-sm font-semibold text-gray-900 dark:text-white">No hay proveedores</p>
          <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Agrega proveedores para empezar</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Proveedor</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Productos</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Última Compra</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Total Compras</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Deuda</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Estado</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
              <tr v-for="supplier in paginatedSuppliers" :key="supplier.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-all duration-200 border-b border-gray-200 dark:border-zinc-800">
                <td class="px-4 py-4">
                  <div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ supplier.name }}</div>
                    <div class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">
                      {{ supplier.contact_name }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5 flex items-center space-x-2">
                      <span v-if="supplier.phone">📞 {{ supplier.phone }}</span>
                      <span v-if="supplier.city">📍 {{ supplier.city }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="font-mono text-base font-bold text-gray-900 dark:text-white">{{ supplier.products_count }}</div>
                  <div class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">productos</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="text-sm text-gray-900 dark:text-white" v-if="supplier.last_purchase_date">
                    {{ formatDate(supplier.last_purchase_date) }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-zinc-500" v-else>Sin compras</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="font-mono text-base font-bold text-gray-900 dark:text-white">${{ formatNumber(supplier.total_purchases_amount) }}</div>
                  <div class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">{{ supplier.purchase_orders_count }} órdenes</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="font-mono text-base font-bold" :class="[
                    supplier.current_debt > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400 dark:text-zinc-600'
                  ]">${{ formatNumber(supplier.current_debt) }}</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <span :class="[
                    'px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide',
                    supplier.active ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
                  ]">
                    {{ supplier.active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <button @click="viewProducts(supplier)" class="px-3 py-1.5 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-lg text-xs font-bold transition-all duration-200">
                      Ver Productos
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div v-if="suppliers.length > itemsPerPage" class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-4 py-3 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
              <span class="text-xs font-medium text-gray-700 dark:text-zinc-300">Mostrar:</span>
              <select v-model="itemsPerPage" @change="currentPage = 1" class="px-3 py-2 rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
              </select>
              <span class="text-xs text-gray-700 dark:text-zinc-300">por página</span>
            </div>
            <div class="text-xs text-gray-700 dark:text-zinc-300">
              Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} a {{ Math.min(currentPage * itemsPerPage, suppliers.length) }} de {{ suppliers.length }}
            </div>
          </div>
          
          <div class="flex items-center space-x-1">
            <button @click="currentPage = 1" :disabled="currentPage === 1" class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
              </svg>
            </button>
            <button @click="currentPage--" :disabled="currentPage === 1" class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            
            <div class="flex items-center space-x-1">
              <button v-for="page in totalPages" :key="page" @click="currentPage = page" :class="[
                'px-3 py-2 text-xs font-bold rounded-lg transition-all duration-200',
                page === currentPage 
                  ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50' 
                  : 'text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800'
              ]">
                {{ page }}
              </button>
            </div>
            
            <button @click="currentPage++" :disabled="currentPage === totalPages" class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            <button @click="currentPage = totalPages" :disabled="currentPage === totalPages" class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
      </div>
      <!-- FIN TAB: PROVEEDORES -->

      <!-- TAB: ÓRDENES DE COMPRA -->
      <div v-if="activeTab === 'orders'" class="space-y-6">
        <!-- Métricas de Órdenes -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:hover:shadow-lg dark:hover:shadow-black/50">
            <div class="flex items-center gap-3">
              <div class="w-11 h-11 bg-blue-50 dark:bg-blue-950 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Órdenes</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ ordersMetrics.total_orders || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:hover:shadow-lg dark:hover:shadow-black/50">
            <div class="flex items-center gap-3">
              <div class="w-11 h-11 bg-amber-50 dark:bg-amber-950 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Pendientes</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ ordersMetrics.pending_orders || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:hover:shadow-lg dark:hover:shadow-black/50">
            <div class="flex items-center gap-3">
              <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-950 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Recibidas</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ ordersMetrics.received_orders || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:hover:shadow-lg dark:hover:shadow-black/50">
            <div class="flex items-center gap-3">
              <div class="w-11 h-11 bg-purple-50 dark:bg-purple-950 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Monto</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatNumber(ordersMetrics.total_amount || 0) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-white/5">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <select v-model="orderFilters.status" @change="loadOrders" class="px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
              <option value="">Todos los estados</option>
              <option value="draft">Borrador</option>
              <option value="pending">Pendiente</option>
              <option value="partial">Parcial</option>
              <option value="received">Recibida</option>
              <option value="cancelled">Cancelada</option>
            </select>

            <select v-model="orderFilters.supplier_id" @change="loadOrders" class="px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
              <option value="">Todos los proveedores</option>
              <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
            </select>

            <input type="date" v-model="orderFilters.date_from" @change="loadOrders" class="px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">

            <input type="date" v-model="orderFilters.date_to" @change="loadOrders" class="px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
          </div>
        </div>

        <!-- Tabla de Órdenes -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
          <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-5 py-4">
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Órdenes de Compra</h2>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Historial de órdenes a proveedores</p>
          </div>

          <div v-if="loadingOrders" class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-200 dark:border-zinc-700 border-t-blue-600 dark:border-t-blue-400"></div>
          </div>

          <div v-else-if="purchaseOrders.length === 0" class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">No hay órdenes</p>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Crea tu primera orden de compra</p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Orden</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Proveedor</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Fecha</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Productos</th>
                  <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Total</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Estado</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
                <tr v-for="order in purchaseOrders" :key="order.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-all duration-200">
                  <td class="px-4 py-4">
                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ order.order_number }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-500" v-if="order.reference">Ref: {{ order.reference }}</div>
                  </td>
                  <td class="px-4 py-4">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ order.supplier?.name }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-500" v-if="order.warehouse">{{ order.warehouse.name }}</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="text-sm text-gray-900 dark:text-white">{{ formatDate(order.order_date) }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-500" v-if="order.expected_date">
                      Esp: {{ formatDate(order.expected_date) }}
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ order.items?.length || 0 }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-500">productos</div>
                  </td>
                  <td class="px-4 py-4 text-right">
                    <div class="text-sm font-bold text-gray-900 dark:text-white">${{ formatNumber(order.total) }}</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <span :class="getOrderStatusClass(order.status)">
                      {{ getOrderStatusText(order.status) }}
                    </span>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                      <button @click="viewOrder(order)" class="p-2 text-slate-400 dark:text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg border border-transparent hover:border-blue-100 dark:hover:border-blue-900/30 transition-all duration-200" title="Ver detalles">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                      </button>
                      <button v-if="order.status === 'pending' || order.status === 'partial'" @click="receiveOrder(order)" class="p-2 text-slate-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg border border-transparent hover:border-emerald-100 dark:hover:border-emerald-900/30 transition-all duration-200" title="Recibir mercancía">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                      </button>
                      <button v-if="order.status === 'draft'" @click="editOrder(order)" class="p-2 text-slate-400 dark:text-zinc-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg border border-transparent hover:border-amber-100 dark:hover:border-amber-900/30 transition-all duration-200" title="Editar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- FIN TAB: ÓRDENES DE COMPRA -->

    </div>

    <!-- Modal Nuevo/Editar Proveedor -->
    <div v-if="showSupplierModal" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 max-w-2xl w-full max-h-[90vh] overflow-auto">
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between sticky top-0 bg-white dark:bg-zinc-900 z-10">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ isEditing ? 'Editar' : 'Nuevo' }} Proveedor</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400">Complete la información del proveedor</p>
            </div>
          </div>
          <button @click="closeModal" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Form -->
        <div class="px-6 py-4 space-y-6">
          <!-- Información General -->
          <div>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-zinc-800">Información General</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Nombre del Proveedor *</label>
                <input v-model="supplierForm.name" type="text" :class="['w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg border focus:ring-2 focus:border-transparent transition-all', formErrors.name ? 'border-red-500 dark:border-red-600 focus:ring-red-500 dark:focus:ring-red-400' : 'border-gray-300 dark:border-zinc-700 focus:ring-blue-500 dark:focus:ring-blue-400']" placeholder="Ej: Distribuidora ABC" />
                <p v-if="formErrors.name" class="mt-1 text-xs text-red-500 dark:text-red-400">{{ formErrors.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">NIT/RUT/Documento *</label>
                <input v-model="supplierForm.document" type="text" :class="['w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg border focus:ring-2 focus:border-transparent transition-all', formErrors.document ? 'border-red-500 dark:border-red-600 focus:ring-red-500 dark:focus:ring-red-400' : 'border-gray-300 dark:border-zinc-700 focus:ring-blue-500 dark:focus:ring-blue-400']" placeholder="900123456-7" />
                <p v-if="formErrors.document" class="mt-1 text-xs text-red-500 dark:text-red-400">{{ formErrors.document }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Persona de Contacto</label>
                <input v-model="supplierForm.contact_person" type="text" class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent" placeholder="Nombre del contacto" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Teléfono</label>
                <input v-model="supplierForm.phone" type="text" class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent" placeholder="300 123 4567" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Email</label>
                <input v-model="supplierForm.email" type="email" class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent" placeholder="proveedor@ejemplo.com" />
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Dirección</label>
                <input v-model="supplierForm.address" type="text" class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent" placeholder="Calle 123 #45-67" />
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Notas <span class="text-gray-400 dark:text-zinc-500 font-normal">(opcional)</span></label>
                <textarea v-model="supplierForm.notes" rows="3" class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent" placeholder="Notas adicionales sobre el proveedor..."></textarea>
              </div>
            </div>
          </div>

          <!-- Estado -->
          <div>
            <label class="flex items-center gap-3 cursor-pointer">
              <input v-model="supplierForm.active" type="checkbox" class="w-4 h-4 bg-gray-100 dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 rounded text-blue-600 dark:text-blue-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" />
              <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Proveedor activo</span>
            </label>
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex justify-end gap-3 sticky bottom-0">
          <button @click="closeModal" class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
            Cancelar
          </button>
          <button @click="saveSupplier" :disabled="saving" class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300">
            {{ saving ? 'Guardando...' : 'Guardar Proveedor' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Nueva Orden de Compra -->
    <div v-if="showOrderModal" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-50 p-4" @click.self="closeOrderModal">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 max-w-5xl w-full max-h-[90vh] overflow-auto">
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between sticky top-0 bg-white dark:bg-zinc-900 z-10">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ editingOrder ? 'Editar' : 'Nueva' }} Orden de Compra</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400">Complete la información de la orden</p>
            </div>
          </div>
          <button @click="closeOrderModal" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Form -->
        <div class="px-6 py-4 space-y-6">
          <!-- Información General -->
          <div>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-zinc-800">Información de la Orden</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Proveedor *</label>
                <select v-model="orderForm.supplier_id" :class="['w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-lg border focus:ring-2 focus:border-transparent transition-all', orderErrors.supplier_id ? 'border-red-500 dark:border-red-600 focus:ring-red-500 dark:focus:ring-red-400' : 'border-gray-300 dark:border-zinc-700 focus:ring-blue-500 dark:focus:ring-blue-400']">
                  <option value="">Seleccionar proveedor...</option>
                  <option v-for="supplier in suppliers.filter(s => s.active)" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                </select>
                <p v-if="orderErrors.supplier_id" class="mt-1 text-xs text-red-500 dark:text-red-400">{{ orderErrors.supplier_id }}</p>
              </div>

              <div v-if="shouldShowWarehouseSelector">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Bodega/Sede *</label>
                <select v-model="orderForm.warehouse_id" class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all">
                  <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">{{ warehouse.name }}</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Fecha de Orden *</label>
                <input v-model="orderForm.order_date" type="date" :class="['w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-lg border focus:ring-2 focus:border-transparent transition-all', orderErrors.order_date ? 'border-red-500 dark:border-red-600 focus:ring-red-500 dark:focus:ring-red-400' : 'border-gray-300 dark:border-zinc-700 focus:ring-blue-500 dark:focus:ring-blue-400']" />
                <p v-if="orderErrors.order_date" class="mt-1 text-xs text-red-500 dark:text-red-400">{{ orderErrors.order_date }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Fecha Esperada</label>
                <input v-model="orderForm.expected_date" type="date" class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all" />
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Referencia</label>
                <input v-model="orderForm.reference" type="text" placeholder="OC-2024-001, Factura #123, etc." class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all" />
              </div>
            </div>
          </div>

          <!-- Productos -->
          <div>
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200 dark:border-zinc-800">
              <h4 class="text-sm font-bold text-gray-900 dark:text-white">Productos</h4>
              <button @click="showProductSelector = true" class="px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-xs font-bold rounded-lg transition-all duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Agregar Producto
              </button>
            </div>

            <div v-if="orderForm.items.length === 0" class="text-center py-8 border-2 border-dashed border-gray-300 dark:border-zinc-700 rounded-xl">
              <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
              </div>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">No hay productos</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Agrega productos a esta orden</p>
            </div>

            <div v-else class="space-y-2">
              <div v-for="(item, index) in orderForm.items" :key="index" class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 flex items-center gap-3">
                <div class="flex-1 grid grid-cols-12 gap-3 items-center">
                  <div class="col-span-4">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.product?.name || 'Producto' }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-500">SKU: {{ item.product?.sku || 'N/A' }}</p>
                  </div>
                  <div class="col-span-3">
                    <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Cantidad</label>
                    <input v-model.number="item.quantity" type="number" min="0.01" step="0.01" class="w-full px-3 py-2 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent text-sm" @input="calculateItemTotal(index)" />
                  </div>
                  <div class="col-span-3">
                    <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Costo Unitario</label>
                    <input v-model.number="item.unit_cost" type="number" min="0" step="0.01" class="w-full px-3 py-2 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent text-sm" @input="calculateItemTotal(index)" />
                  </div>
                  <div class="col-span-2 text-right">
                    <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Subtotal</label>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">${{ formatNumber(item.quantity * item.unit_cost) }}</p>
                  </div>
                </div>
                <button @click="removeOrderItem(index)" class="p-2 text-red-400 dark:text-red-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>

            <p v-if="orderErrors.items" class="mt-2 text-xs text-red-500 dark:text-red-400">{{ orderErrors.items }}</p>
          </div>

          <!-- Totales -->
          <div class="bg-gradient-to-br from-slate-50 to-gray-100 dark:from-zinc-800/50 dark:to-zinc-900/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
            <div class="space-y-2">
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Subtotal:</span>
                <span class="text-base font-bold text-gray-900 dark:text-white">${{ formatNumber(orderSubtotal) }}</span>
              </div>
              <div class="flex justify-between items-center pt-2 border-t border-gray-300 dark:border-zinc-700">
                <span class="text-base font-bold text-gray-900 dark:text-white">Total:</span>
                <span class="text-xl font-bold text-blue-600 dark:text-blue-400">${{ formatNumber(orderSubtotal) }}</span>
              </div>
            </div>
          </div>

          <!-- Notas -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Notas</label>
            <textarea v-model="orderForm.notes" rows="3" placeholder="Comentarios adicionales sobre la orden..." class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all resize-none"></textarea>
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex justify-end gap-3 sticky bottom-0">
          <button @click="closeOrderModal" class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
            Cancelar
          </button>
          <button @click="saveOrderAsDraft" :disabled="savingOrder" class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
            {{ savingOrder ? 'Guardando...' : 'Guardar Borrador' }}
          </button>
          <button @click="saveOrderAsPending" :disabled="savingOrder" class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300">
            {{ savingOrder ? 'Enviando...' : 'Enviar Orden' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Selector de Productos -->
    <div v-if="showProductSelector" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-[60] p-4" @click.self="showProductSelector = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 max-w-3xl w-full max-h-[80vh] overflow-auto">
        <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between sticky top-0 bg-white dark:bg-zinc-900 z-10">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">Seleccionar Producto</h3>
          <button @click="showProductSelector = false" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="px-6 py-4">
          <input v-model="productSearch" type="text" placeholder="Buscar producto por nombre o SKU..." class="w-full px-4 py-3 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all mb-4" />

          <div v-if="loadingProducts" class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-4 border-gray-200 dark:border-zinc-700 border-t-blue-600 dark:border-t-blue-400"></div>
          </div>

          <div v-else class="space-y-2 max-h-96 overflow-y-auto">
            <button v-for="product in filteredProducts" :key="product.id" @click="addProductToOrder(product)" class="w-full text-left p-3 bg-gray-50 dark:bg-zinc-800/50 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg border border-transparent hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-200">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ product.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500">SKU: {{ product.sku }} | Stock: {{ product.current_stock }}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-bold text-gray-900 dark:text-white">${{ formatNumber(product.cost_price || 0) }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500">Costo</p>
                </div>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ver Detalles de Orden -->
    <div v-if="showOrderDetailModal" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-50 p-4" @click.self="closeOrderDetailModal">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 max-w-4xl w-full max-h-[90vh] overflow-auto">
        <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between sticky top-0 bg-white dark:bg-zinc-900 z-10">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ selectedOrder?.order_number }}</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400">Detalles de la orden</p>
            </div>
          </div>
          <button @click="closeOrderDetailModal" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="px-6 py-4 space-y-6">
          <!-- Info General -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-zinc-500 mb-1">Estado</p>
              <span :class="getOrderStatusClass(selectedOrder?.status)">
                {{ getOrderStatusText(selectedOrder?.status) }}
              </span>
            </div>
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-zinc-500 mb-1">Proveedor</p>
              <p class="text-sm font-bold text-gray-900 dark:text-white">{{ selectedOrder?.supplier?.name }}</p>
            </div>
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-zinc-500 mb-1">Fecha Orden</p>
              <p class="text-sm font-bold text-gray-900 dark:text-white">{{ formatDate(selectedOrder?.order_date) }}</p>
            </div>
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-zinc-500 mb-1">Fecha Esperada</p>
              <p class="text-sm font-bold text-gray-900 dark:text-white">{{ formatDate(selectedOrder?.expected_date) || 'N/A' }}</p>
            </div>
          </div>

          <!-- Productos -->
          <div>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-zinc-800">Productos</h4>
            <div class="space-y-2">
              <div v-for="item in selectedOrder?.items" :key="item.id" class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3">
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.product?.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">SKU: {{ item.product?.sku }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ item.quantity_ordered }} {{ item.unit }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-500">x ${{ formatNumber(item.unit_cost) }}</p>
                    <p class="text-sm font-bold text-blue-600 dark:text-blue-400 mt-1">${{ formatNumber(item.total) }}</p>
                  </div>
                </div>
                <div v-if="item.quantity_received > 0" class="mt-2 pt-2 border-t border-gray-200 dark:border-zinc-700">
                  <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                    ✓ Recibido: {{ item.quantity_received }} de {{ item.quantity_ordered }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Total -->
          <div class="bg-gradient-to-br from-slate-50 to-gray-100 dark:from-zinc-800/50 dark:to-zinc-900/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
            <div class="flex justify-between items-center">
              <span class="text-base font-bold text-gray-900 dark:text-white">Total:</span>
              <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ formatNumber(selectedOrder?.total) }}</span>
            </div>
          </div>

          <!-- Notas -->
          <div v-if="selectedOrder?.notes">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Notas</h4>
            <p class="text-sm text-gray-600 dark:text-zinc-400 bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3">{{ selectedOrder.notes }}</p>
          </div>
        </div>

        <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex justify-between">
          <div class="flex gap-2">
            <button @click="downloadOrderPDF(selectedOrder)" class="px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              PDF
            </button>
            <button @click="sendOrderByEmail(selectedOrder)" class="px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Email
            </button>
            <button v-if="selectedOrder?.status !== 'received'" @click="showPaymentModal = true" class="px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Pagar
            </button>
          </div>
          <div class="flex gap-3">
            <button @click="closeOrderDetailModal" class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
              Cerrar
            </button>
            <button v-if="selectedOrder?.status === 'pending' || selectedOrder?.status === 'partial'" @click="receiveOrder(selectedOrder); closeOrderDetailModal()" class="px-6 py-2.5 bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-700 dark:hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300">
              Recibir Mercancía
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Recibir Mercancía -->
    <div v-if="showReceiveModal" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-50 p-4" @click.self="closeReceiveModal">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 max-w-4xl w-full max-h-[90vh] overflow-auto">
        <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between sticky top-0 bg-white dark:bg-zinc-900 z-10">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-950 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recibir Mercancía</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400">{{ receivingOrder?.order_number }}</p>
            </div>
          </div>
          <button @click="closeReceiveModal" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="px-6 py-4 space-y-4">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
            <p class="text-sm text-blue-800 dark:text-blue-300">
              <strong>💡 Nota:</strong> Ingresa la cantidad recibida para cada producto. El inventario se actualizará automáticamente.
            </p>
          </div>

          <div class="space-y-3">
            <div v-for="(item, index) in receiveForm.items" :key="item.item_id" class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-4">
              <div class="flex items-start gap-4">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.product_name }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">
                    SKU: {{ item.product_sku }} | 
                    Ordenado: {{ item.quantity_ordered }} | 
                    Ya recibido: {{ item.quantity_received }}
                  </p>
                  <div class="mt-2">
                    <div class="flex items-center gap-2">
                      <label class="text-xs font-medium text-gray-600 dark:text-zinc-400 min-w-[120px]">Cantidad a Recibir:</label>
                      <input v-model.number="item.quantity_to_receive" type="number" min="0" :max="item.quantity_ordered - item.quantity_received" step="0.01" class="flex-1 px-3 py-2 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-transparent text-sm" />
                      <button @click="item.quantity_to_receive = item.quantity_ordered - item.quantity_received" class="px-3 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-xs font-bold rounded-lg transition-all">
                        Completo
                      </button>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-xs text-gray-500 dark:text-zinc-500">Pendiente</p>
                  <p class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ item.quantity_ordered - item.quantity_received }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex justify-end gap-3">
          <button @click="closeReceiveModal" class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
            Cancelar
          </button>
          <button @click="confirmReceive" :disabled="receivingMerchandise" class="px-6 py-2.5 bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-700 dark:hover:bg-emerald-600 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300">
            {{ receivingMerchandise ? 'Procesando...' : 'Confirmar Recepción' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Registrar Pago -->
    <div v-if="showPaymentModal" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-[70] p-4" @click.self="closePaymentModal">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 max-w-md w-full">
        <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-950 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Registrar Pago</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400">{{ selectedOrder?.order_number }}</p>
            </div>
          </div>
          <button @click="closePaymentModal" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="px-6 py-4 space-y-4">
          <!-- Resumen de deuda -->
          <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
            <div class="space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-700 dark:text-zinc-300">Total Orden:</span>
                <span class="font-bold text-gray-900 dark:text-white">${{ formatNumber(selectedOrder?.total || 0) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-700 dark:text-zinc-300">Pagado:</span>
                <span class="font-bold text-emerald-600 dark:text-emerald-400">${{ formatNumber(selectedOrder?.paid_amount || 0) }}</span>
              </div>
              <div class="flex justify-between text-base pt-2 border-t border-purple-300 dark:border-purple-700">
                <span class="font-bold text-gray-900 dark:text-white">Saldo Pendiente:</span>
                <span class="font-bold text-purple-600 dark:text-purple-400">${{ formatNumber((selectedOrder?.total || 0) - (selectedOrder?.paid_amount || 0)) }}</span>
              </div>
            </div>
          </div>

          <!-- Monto a pagar -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Monto a Pagar *</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-zinc-400 font-bold">$</span>
              <input v-model.number="paymentForm.amount" type="number" min="0" :max="(selectedOrder?.total || 0) - (selectedOrder?.paid_amount || 0)" step="0.01" class="w-full pl-8 pr-4 py-3 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-all text-lg font-bold" />
            </div>
            <div class="flex gap-2 mt-2">
              <button @click="paymentForm.amount = (selectedOrder?.total || 0) - (selectedOrder?.paid_amount || 0)" class="px-3 py-1.5 bg-purple-600 dark:bg-purple-700 hover:bg-purple-700 dark:hover:bg-purple-600 text-white text-xs font-bold rounded-lg transition-all">
                Pagar Total
              </button>
              <button @click="paymentForm.amount = ((selectedOrder?.total || 0) - (selectedOrder?.paid_amount || 0)) / 2" class="px-3 py-1.5 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-xs font-bold rounded-lg transition-all">
                50%
              </button>
            </div>
          </div>

          <!-- Método de pago -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Método de Pago</label>
            <select v-model="paymentForm.payment_method" class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-all">
              <option value="efectivo">Efectivo</option>
              <option value="transferencia">Transferencia</option>
              <option value="cheque">Cheque</option>
              <option value="tarjeta">Tarjeta</option>
            </select>
          </div>

          <!-- Referencia -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Referencia/Comprobante</label>
            <input v-model="paymentForm.reference" type="text" placeholder="Número de transferencia, cheque, etc." class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-all" />
          </div>

          <!-- Notas -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Notas</label>
            <textarea v-model="paymentForm.notes" rows="2" placeholder="Comentarios sobre el pago..." class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-all resize-none"></textarea>
          </div>
        </div>

        <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex justify-end gap-3">
          <button @click="closePaymentModal" class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
            Cancelar
          </button>
          <button @click="confirmPayment" :disabled="processingPayment || !paymentForm.amount || paymentForm.amount <= 0" class="px-6 py-2.5 bg-purple-600 dark:bg-purple-700 hover:bg-purple-700 dark:hover:bg-purple-600 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300">
            {{ processingPayment ? 'Procesando...' : 'Confirmar Pago' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { apiCall } from '../services/api.js'
import { appStore } from '../store/appStore.js'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

export default {
  name: 'SuppliersView',
  data() {
    return {
      // Tabs
      activeTab: 'suppliers', // 'suppliers' | 'orders'
      
      // Proveedores
      loading: false,
      saving: false,
      suppliers: [],
      summary: {},
      currentPage: 1,
      itemsPerPage: 25,
      showSupplierModal: false,
      isEditing: false,
      supplierForm: {
        name: '',
        document: '',
        contact_person: '',
        phone: '',
        email: '',
        address: '',
        payment_terms: '30_days',
        credit_limit: 0,
        current_debt: 0,
        notes: '',
        active: true
      },
      formErrors: {},
      
      // Órdenes de Compra
      purchaseOrders: [],
      ordersMetrics: {},
      loadingOrders: false,
      showOrderModal: false,
      editingOrder: false,
      savingOrder: false,
      orderFilters: {
        status: '',
        supplier_id: '',
        date_from: '',
        date_to: ''
      },
      orderForm: {
        supplier_id: '',
        warehouse_id: '',
        order_date: new Date().toISOString().split('T')[0],
        expected_date: '',
        reference: '',
        notes: '',
        items: []
      },
      orderErrors: {},
      
      // Selector de productos
      showProductSelector: false,
      products: [],
      loadingProducts: false,
      productSearch: '',
      
      // Bodegas
      warehouses: [],
      tenantPlan: 'free_trial', // Se cargará del appStore
      
      // Ver detalle de orden
      showOrderDetailModal: false,
      selectedOrder: null,
      
      // Recibir mercancía
      showReceiveModal: false,
      receivingOrder: null,
      receivingMerchandise: false,
      receiveForm: {
        items: []
      },
      
      // Pagos
      showPaymentModal: false,
      processingPayment: false,
      paymentForm: {
        amount: 0,
        payment_method: 'efectivo',
        reference: '',
        notes: ''
      }
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.suppliers.length / this.itemsPerPage)
    },
    paginatedSuppliers() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.suppliers.slice(start, end)
    },
    orderSubtotal() {
      return this.orderForm.items.reduce((sum, item) => {
        return sum + (item.quantity * item.unit_cost)
      }, 0)
    },
    filteredProducts() {
      let filtered = [...this.products]
      
      // 1. Filtrar por proveedor si está seleccionado (relación uno a uno)
      if (this.orderForm.supplier_id) {
        filtered = filtered.filter(p => p.supplier_id == this.orderForm.supplier_id)
      }
      
      // 2. Filtrar por bodega si está seleccionada
      if (this.orderForm.warehouse_id) {
        filtered = filtered.filter(p => {
          // Si el producto tiene stock en esa bodega específica
          if (p.warehouse_id == this.orderForm.warehouse_id) return true
          // Si tiene inventario multi-bodega
          if (p.warehouse_stocks && p.warehouse_stocks.some(ws => ws.warehouse_id == this.orderForm.warehouse_id)) return true
          return true // Por defecto mostrar todos si no hay info de bodega
        })
      }
      
      // 3. Filtrar por búsqueda de texto
      if (this.productSearch) {
        const search = this.productSearch.toLowerCase()
        filtered = filtered.filter(p => 
          p.name.toLowerCase().includes(search) || 
          p.sku?.toLowerCase().includes(search)
        )
      }
      
      // 4. Ordenar: primero sin stock, luego stock bajo, luego el resto
      filtered.sort((a, b) => {
        const stockA = a.current_stock || 0
        const stockB = b.current_stock || 0
        
        // Sin stock primero
        if (stockA === 0 && stockB > 0) return -1
        if (stockB === 0 && stockA > 0) return 1
        
        // Luego por cantidad (menor primero)
        return stockA - stockB
      })
      
      return filtered
    },
    
    // Verificar si debe mostrar selector de bodega
    shouldShowWarehouseSelector() {
      // Solo mostrar si es premium/enterprise Y tiene más de 1 bodega
      const isPremium = ['premium', 'enterprise'].includes(this.tenantPlan)
      const hasMultipleWarehouses = this.warehouses.length > 1
      return isPremium && hasMultipleWarehouses
    }
  },
  mounted() {
    this.loadData()
    this.loadWarehouses()
    this.loadProducts()
    this.loadTenantPlan()
  },
  methods: {
    async loadData() {
      this.loading = true
      try {
        const response = await apiCall('/suppliers/analytics')
        if (response.success) {
          this.suppliers = response.data.suppliers
          this.summary = response.data.summary
        }
      } catch (error) {
        console.error('Error cargando proveedores:', error)
      } finally {
        this.loading = false
      }
    },
    formatNumber(value) {
      return new Intl.NumberFormat('es-CO').format(value)
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return date.toLocaleDateString('es-CO', { year: 'numeric', month: 'short', day: 'numeric' })
    },
    closeModal() {
      this.showSupplierModal = false
      this.isEditing = false
      this.formErrors = {}
      this.supplierForm = {
        name: '',
        document: '',
        contact_person: '',
        phone: '',
        email: '',
        address: '',
        payment_terms: '30_days',
        credit_limit: 0,
        current_debt: 0,
        notes: '',
        active: true
      }
    },
    async saveSupplier() {
      this.formErrors = {}
      
      if (!this.supplierForm.name || this.supplierForm.name.trim() === '') {
        this.formErrors.name = 'El nombre del proveedor es obligatorio'
        return
      }

      this.saving = true
      try {
        const endpoint = this.isEditing ? `/suppliers/${this.supplierForm.id}` : '/suppliers'
        const method = this.isEditing ? 'PUT' : 'POST'
        
        const response = await apiCall(endpoint, {
          method: method,
          body: JSON.stringify(this.supplierForm)
        })
        
        if (response.success) {
          this.$toast?.success(response.message || 'Proveedor guardado exitosamente')
          this.closeModal()
          this.loadData()
        }
      } catch (error) {
        console.error('Error guardando proveedor:', error)
        // Mostrar mensaje de error
        const errorMessage = error.message || 'Error al guardar proveedor'
        this.$toast?.error(errorMessage)
      } finally {
        this.saving = false
      }
    },
    
    // === MÉTODOS PARA ÓRDENES DE COMPRA ===
    refreshCurrentTab() {
      if (this.activeTab === 'suppliers') {
        this.loadData()
      } else {
        this.loadOrders()
      }
    },
    
    async loadOrders() {
      this.loadingOrders = true
      try {
        const params = new URLSearchParams()
        if (this.orderFilters.status) params.append('status', this.orderFilters.status)
        if (this.orderFilters.supplier_id) params.append('supplier_id', this.orderFilters.supplier_id)
        if (this.orderFilters.date_from) params.append('date_from', this.orderFilters.date_from)
        if (this.orderFilters.date_to) params.append('date_to', this.orderFilters.date_to)
        
        const queryString = params.toString()
        const response = await apiCall(`/purchase-orders${queryString ? '?' + queryString : ''}`)
        
        if (response.success) {
          this.purchaseOrders = response.data.orders
          this.ordersMetrics = response.data.metrics
        }
      } catch (error) {
        console.error('Error cargando órdenes:', error)
        this.$toast?.error('Error al cargar órdenes de compra')
      } finally {
        this.loadingOrders = false
      }
    },
    
    getOrderStatusClass(status) {
      const classes = {
        draft: 'px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide bg-gray-50 dark:bg-gray-950 text-gray-700 dark:text-gray-400 border-gray-100 dark:border-gray-800',
        pending: 'px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800',
        partial: 'px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
        received: 'px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
        cancelled: 'px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
      }
      return classes[status] || classes.draft
    },
    
    getOrderStatusText(status) {
      const texts = {
        draft: 'Borrador',
        pending: 'Pendiente',
        partial: 'Parcial',
        received: 'Recibida',
        cancelled: 'Cancelada'
      }
      return texts[status] || status
    },
    
    async viewOrder(order) {
      try {
        const response = await apiCall(`/purchase-orders/${order.id}`)
        if (response.success) {
          this.selectedOrder = response.data
          this.showOrderDetailModal = true
        }
      } catch (error) {
        console.error('Error cargando detalles:', error)
        this.$toast?.error('Error al cargar detalles de la orden')
      }
    },
    
    async receiveOrder(order) {
      try {
        // Validar que la orden exista
        if (!order || !order.id) {
          this.$toast?.error('No se pudo cargar la orden')
          return
        }
        
        // Si no tiene items cargados, obtener detalles completos
        if (!order.items || order.items.length === 0) {
          console.log('📦 Cargando items de la orden:', order.id)
          const response = await apiCall(`/purchase-orders/${order.id}`)
          if (response.success) {
            order = response.data
          } else {
            this.$toast?.error('Error al cargar los items de la orden')
            return
          }
        }
        
        console.log('✅ Orden preparada para recepción:', order)
        
        this.receivingOrder = order
        this.receiveForm.items = (order.items || []).map(item => ({
          item_id: item.id,
          product_name: item.product?.name,
          product_sku: item.product?.sku,
          quantity_ordered: item.quantity_ordered,
          quantity_received: item.quantity_received || 0,
          quantity_to_receive: 0
        }))
        this.showReceiveModal = true
      } catch (error) {
        console.error('Error preparando recepción:', error)
        this.$toast?.error('Error al preparar la recepción de mercancía')
      }
    },
    
    async editOrder(order) {
      try {
        const response = await apiCall(`/purchase-orders/${order.id}`)
        if (response.success) {
          const orderData = response.data
          this.orderForm = {
            id: orderData.id,
            supplier_id: orderData.supplier_id,
            warehouse_id: orderData.warehouse_id,
            order_date: orderData.order_date,
            expected_date: orderData.expected_date,
            reference: orderData.reference,
            notes: orderData.notes,
            items: orderData.items.map(item => ({
              product_id: item.product_id,
              product: item.product,
              quantity: item.quantity_ordered,
              unit_cost: item.unit_cost,
              notes: item.notes
            }))
          }
          this.editingOrder = true
          this.showOrderModal = true
        }
      } catch (error) {
        console.error('Error cargando orden:', error)
        this.$toast?.error('Error al cargar orden')
      }
    },
    
    // === GESTIÓN DE MODAL DE ORDEN ===
    closeOrderModal() {
      this.showOrderModal = false
      this.editingOrder = false
      this.orderErrors = {}
      this.orderForm = {
        supplier_id: '',
        warehouse_id: '',
        order_date: new Date().toISOString().split('T')[0],
        expected_date: '',
        reference: '',
        notes: '',
        items: []
      }
    },
    
    async loadWarehouses() {
      try {
        const response = await apiCall('/warehouses/active')
        if (response.success) {
          this.warehouses = response.data
          // Si no hay bodega seleccionada y hay bodegas disponibles, seleccionar la primera
          if (!this.orderForm.warehouse_id && this.warehouses.length > 0) {
            this.orderForm.warehouse_id = this.warehouses[0].id
          }
        }
      } catch (error) {
        console.error('Error cargando bodegas:', error)
      }
    },
    
    async loadProducts() {
      this.loadingProducts = true
      try {
        const response = await apiCall('/products/analytics')
        if (response.success) {
          this.products = response.data.products
        }
      } catch (error) {
        console.error('Error cargando productos:', error)
      } finally {
        this.loadingProducts = false
      }
    },
    
    loadTenantPlan() {
      // Cargar del appStore o desde configuración del sistema
      this.tenantPlan = appStore.tenantPlan || 'free_trial'
    },
    
    addProductToOrder(product) {
      // Verificar si ya existe
      const exists = this.orderForm.items.find(i => i.product_id === product.id)
      if (exists) {
        this.$toast?.warning('Este producto ya está en la orden')
        return
      }
      
      this.orderForm.items.push({
        product_id: product.id,
        product: product,
        quantity: 1,
        unit_cost: product.cost_price || 0,
        notes: ''
      })
      
      this.showProductSelector = false
      this.productSearch = ''
    },
    
    removeOrderItem(index) {
      this.orderForm.items.splice(index, 1)
    },
    
    calculateItemTotal(index) {
      const item = this.orderForm.items[index]
      item.total = item.quantity * item.unit_cost
    },
    
    async saveOrderAsDraft() {
      await this.saveOrder('draft')
    },
    
    async saveOrderAsPending() {
      await this.saveOrder('pending')
    },
    
    async saveOrder(status) {
      this.orderErrors = {}
      
      // Validaciones
      if (!this.orderForm.supplier_id) {
        this.orderErrors.supplier_id = 'Selecciona un proveedor'
        return
      }
      
      if (!this.orderForm.order_date) {
        this.orderErrors.order_date = 'La fecha de orden es requerida'
        return
      }
      
      if (this.orderForm.items.length === 0) {
        this.orderErrors.items = 'Agrega al menos un producto'
        return
      }
      
      this.savingOrder = true
      try {
        const payload = {
          ...this.orderForm,
          items: this.orderForm.items.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity,
            unit_cost: item.unit_cost,
            notes: item.notes
          }))
        }
        
        const endpoint = this.editingOrder 
          ? `/purchase-orders/${this.orderForm.id}` 
          : '/purchase-orders'
        const method = this.editingOrder ? 'PUT' : 'POST'
        
        const response = await apiCall(endpoint, {
          method: method,
          body: JSON.stringify(payload)
        })
        
        if (response.success) {
          // Si se guardó como pending, actualizar el estado
          if (status === 'pending' && !this.editingOrder) {
            await apiCall(`/purchase-orders/${response.data.id}/status`, {
              method: 'POST',
              body: JSON.stringify({ status: 'pending' })
            })
          }
          
          this.$toast?.success(response.message || 'Orden guardada exitosamente')
          this.closeOrderModal()
          this.loadOrders()
        }
      } catch (error) {
        console.error('Error guardando orden:', error)
        this.$toast?.error(error.message || 'Error al guardar orden')
      } finally {
        this.savingOrder = false
      }
    },
    
    // === DETALLE DE ORDEN ===
    closeOrderDetailModal() {
      this.showOrderDetailModal = false
      this.selectedOrder = null
    },
    
    // === RECIBIR MERCANCÍA ===
    closeReceiveModal() {
      this.showReceiveModal = false
      this.receivingOrder = null
      this.receiveForm.items = []
    },
    
    async confirmReceive() {
      // Validar que al menos un producto tenga cantidad
      const hasQuantity = this.receiveForm.items.some(item => item.quantity_to_receive > 0)
      if (!hasQuantity) {
        this.$toast?.warning('Ingresa al menos una cantidad a recibir')
        return
      }
      
      this.receivingMerchandise = true
      
      // 🔧 Guardar ID antes de que se limpie por closeReceiveModal()
      const orderId = this.receivingOrder?.id
      
      try {
        const response = await apiCall(`/purchase-orders/${orderId}/receive`, {
          method: 'POST',
          body: JSON.stringify({
            received_items: this.receiveForm.items
              .filter(item => item.quantity_to_receive > 0)
              .map(item => ({
                item_id: item.item_id,
                quantity: item.quantity_to_receive
              }))
          })
        })
        
        if (response.success) {
          this.$toast?.success('Mercancía recibida e inventario actualizado correctamente')
          this.closeReceiveModal()
          this.loadOrders()
          
          // Recargar productos en el appStore para actualizar inventario global
          if (window.appStore && typeof window.appStore.loadProducts === 'function') {
            window.appStore.loadProducts()
          }
          
          // 🔄 CRÍTICO: Disparar evento global para refrescar TODAS las vistas de productos
          window.dispatchEvent(new CustomEvent('products-updated', {
            detail: { source: 'purchase-order-receive', orderId: orderId }
          }))
        }
      } catch (error) {
        console.error('Error recibiendo mercancía:', error)
        this.$toast?.error(error.message || 'Error al recibir mercancía')
      } finally {
        this.receivingMerchandise = false
      }
    },
    
    // === GENERAR PDF ===
    async downloadOrderPDF(order) {
      try {
        const doc = new jsPDF()
        
        // Configurar fuente
        doc.setFont('helvetica', 'bold')
        doc.setFontSize(20)
        
        // Título
        doc.text('ORDEN DE COMPRA', 105, 20, { align: 'center' })
        
        // Número de orden
        doc.setFontSize(12)
        doc.text(order.order_number, 105, 28, { align: 'center' })
        
        // Información general
        doc.setFont('helvetica', 'normal')
        doc.setFontSize(10)
        
        const startY = 40
        
        // Proveedor
        doc.setFont('helvetica', 'bold')
        doc.text('PROVEEDOR:', 20, startY)
        doc.setFont('helvetica', 'normal')
        doc.text(order.supplier?.name || 'N/A', 20, startY + 6)
        if (order.supplier?.phone) doc.text(`Tel: ${order.supplier.phone}`, 20, startY + 12)
        if (order.supplier?.email) doc.text(`Email: ${order.supplier.email}`, 20, startY + 18)
        
        // Datos de la orden (SIN bodega destino - es información interna)
        doc.setFont('helvetica', 'bold')
        doc.text('Fecha Orden:', 120, startY)
        doc.setFont('helvetica', 'normal')
        doc.text(this.formatDate(order.order_date), 120, startY + 6)
        if (order.expected_date) {
          doc.setFont('helvetica', 'bold')
          doc.text('Fecha Esperada:', 120, startY + 12)
          doc.setFont('helvetica', 'normal')
          doc.text(this.formatDate(order.expected_date), 120, startY + 18)
        }
        if (order.reference) {
          doc.text(`Ref: ${order.reference}`, 120, startY + 24)
        }
        doc.setFont('helvetica', 'bold')
        doc.setFontSize(11)
        doc.setTextColor(0, 102, 204)
        doc.text(this.getOrderStatusText(order.status), 120, startY + 30)
        doc.setTextColor(0, 0, 0)
        doc.setFontSize(10)
        
        // Tabla de productos
        const tableData = order.items.map(item => [
          item.product?.name || 'N/A',
          item.product?.sku || 'N/A',
          `${item.quantity_ordered} ${item.unit}`,
          `$${this.formatNumber(item.unit_cost)}`,
          `$${this.formatNumber(item.total)}`
        ])
        
        doc.autoTable({
          startY: startY + 35,
          head: [['Producto', 'SKU', 'Cantidad', 'Precio Unit.', 'Total']],
          body: tableData,
          theme: 'grid',
          headStyles: { fillColor: [51, 65, 85], textColor: 255, fontStyle: 'bold' },
          styles: { fontSize: 9, cellPadding: 3 },
          columnStyles: {
            0: { cellWidth: 60 },
            1: { cellWidth: 30 },
            2: { cellWidth: 25, halign: 'center' },
            3: { cellWidth: 30, halign: 'right' },
            4: { cellWidth: 30, halign: 'right' }
          }
        })
        
        // Total
        const finalY = doc.lastAutoTable.finalY + 10
        doc.setFont('helvetica', 'bold')
        doc.setFontSize(12)
        doc.text('TOTAL:', 140, finalY)
        doc.text(`$${this.formatNumber(order.total)}`, 175, finalY, { align: 'right' })
        
        // Notas
        if (order.notes) {
          doc.setFont('helvetica', 'bold')
          doc.setFontSize(10)
          doc.text('NOTAS:', 20, finalY + 10)
          doc.setFont('helvetica', 'normal')
          doc.setFontSize(9)
          const splitNotes = doc.splitTextToSize(order.notes, 170)
          doc.text(splitNotes, 20, finalY + 16)
        }
        
        // Footer
        doc.setFontSize(8)
        doc.setTextColor(128, 128, 128)
        doc.text(`Generado el ${new Date().toLocaleString('es-CO')}`, 105, 280, { align: 'center' })
        
        // Descargar
        doc.save(`Orden_${order.order_number}.pdf`)
        this.$toast?.success('PDF generado exitosamente')
        
      } catch (error) {
        console.error('Error generando PDF:', error)
        this.$toast?.error('Error al generar PDF')
      }
    },
    
    // === ENVIAR POR EMAIL ===
    async sendOrderByEmail(order) {
      try {
        const email = order.supplier?.email
        if (!email) {
          this.$toast?.warning('El proveedor no tiene email registrado')
          return
        }
        
        const confirmation = confirm(`¿Enviar orden ${order.order_number} a ${email}?`)
        if (!confirmation) return
        
        // Aquí deberías implementar el endpoint en el backend
        this.$toast?.info('Funcionalidad de envío por email en desarrollo. Por ahora descarga el PDF y envíalo manualmente.')
        
        // Descargar PDF automáticamente
        this.downloadOrderPDF(order)
        
        // TODO: Implementar endpoint backend para envío de email
        // const response = await apiCall(`/purchase-orders/${order.id}/send-email`, {
        //   method: 'POST',
        //   body: JSON.stringify({ email })
        // })
        
      } catch (error) {
        console.error('Error enviando email:', error)
        this.$toast?.error('Error al enviar email')
      }
    },
    
    // === GESTIÓN DE PAGOS ===
    closePaymentModal() {
      this.showPaymentModal = false
      this.paymentForm = {
        amount: 0,
        payment_method: 'efectivo',
        reference: '',
        notes: ''
      }
    },
    
    async confirmPayment() {
      if (!this.paymentForm.amount || this.paymentForm.amount <= 0) {
        this.$toast?.warning('Ingresa un monto válido')
        return
      }
      
      const maxAmount = (this.selectedOrder?.total || 0) - (this.selectedOrder?.paid_amount || 0)
      if (this.paymentForm.amount > maxAmount) {
        this.$toast?.warning('El monto excede el saldo pendiente')
        return
      }
      
      this.processingPayment = true
      try {
        // Calcular nuevo monto pagado
        const newPaidAmount = (this.selectedOrder?.paid_amount || 0) + this.paymentForm.amount
        const newPaymentStatus = newPaidAmount >= this.selectedOrder?.total ? 'paid' : 'partial'
        
        // Actualizar en backend
        const response = await apiCall(`/purchase-orders/${this.selectedOrder.id}`, {
          method: 'PUT',
          body: JSON.stringify({
            paid_amount: newPaidAmount,
            payment_status: newPaymentStatus
          })
        })
        
        if (response.success) {
          this.$toast?.success(`Pago de $${this.formatNumber(this.paymentForm.amount)} registrado exitosamente`)
          
          // Actualizar selectedOrder
          this.selectedOrder.paid_amount = newPaidAmount
          this.selectedOrder.payment_status = newPaymentStatus
          
          this.closePaymentModal()
          this.loadOrders()
          
          // Si se pagó todo, cerrar también el modal de detalles
          if (newPaymentStatus === 'paid') {
            setTimeout(() => {
              this.closeOrderDetailModal()
            }, 1000)
          }
        }
      } catch (error) {
        console.error('Error registrando pago:', error)
        this.$toast?.error('Error al registrar pago')
      } finally {
        this.processingPayment = false
      }
    }
  },
  
  watch: {
    activeTab(newTab) {
      if (newTab === 'orders' && this.purchaseOrders.length === 0) {
        this.loadOrders()
      }
    }
  }
}
</script>
