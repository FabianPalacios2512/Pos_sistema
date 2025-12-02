<template>
  <div class="min-h-screen font-sans bg-gray-100 dark:bg-black transition-colors duration-300">
    <div class="p-4 lg:p-6 space-y-4 lg:space-y-5 animate-fade-in">
      
      <!-- Header Profesional Limpio -->
      <div class="flex items-center justify-between">
        
        <!-- Título Simple -->
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Devoluciones</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Control y seguimiento de devoluciones</p>
        </div>
        
        <!-- KPIs Modernos -->
        <div class="flex-1 flex items-center justify-center gap-6">
          
          <!-- KPI: Total Devuelto -->
          <div class="bg-white dark:bg-zinc-800/50 rounded-2xl p-5 border border-gray-300 dark:border-zinc-700 hover:border-gray-400 dark:hover:border-zinc-600 transition-all duration-200 hover:shadow-lg min-w-[180px]">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-red-600 dark:bg-red-700 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wide">Total Devuelto</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${{ formatCurrency(stats.totalRefunded) }}</p>
              </div>
            </div>
          </div>

          <!-- KPI: Completadas -->
          <div class="bg-white dark:bg-zinc-800/50 rounded-2xl p-5 border border-gray-300 dark:border-zinc-700 hover:border-gray-400 dark:hover:border-zinc-600 transition-all duration-200 hover:shadow-lg min-w-[180px]">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-emerald-600 dark:bg-emerald-700 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wide">Completadas</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.completedCount }}</p>
              </div>
            </div>
          </div>

          <!-- KPI: Pendientes -->
          <div class="bg-white dark:bg-zinc-800/50 rounded-2xl p-5 border border-gray-300 dark:border-zinc-700 hover:border-gray-400 dark:hover:border-zinc-600 transition-all duration-200 hover:shadow-lg min-w-[180px]">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-amber-600 dark:bg-amber-700 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wide">Pendientes</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.pendingCount }}</p>
              </div>
            </div>
          </div>

          <!-- KPI: Productos -->
          <div class="bg-white dark:bg-zinc-800/50 rounded-2xl p-5 border border-gray-300 dark:border-zinc-700 hover:border-gray-400 dark:hover:border-zinc-600 transition-all duration-200 hover:shadow-lg min-w-[180px]">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-slate-600 dark:bg-slate-700 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wide">Productos</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.totalItemsReturned }}</p>
              </div>
            </div>
          </div>
          
        </div>
        
        <!-- Botones de acción -->
        <div class="flex items-center gap-3 flex-shrink-0">
          <button
            @click="loadReturns"
            class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 active:scale-95 flex items-center gap-2 group">
            <svg class="w-4 h-4 text-slate-400 dark:text-zinc-500 group-hover:text-emerald-600 dark:group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Refrescar</span>
          </button>
          
          <button
            @click="navigateToNewReturn"
            class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 hover:shadow-slate-400/60 dark:hover:shadow-slate-900/70 transition-all duration-300 transform active:scale-95 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nueva Devolución</span>
          </button>
        </div>
        
      </div>

      <!-- Master-Detail Layout Enterprise: 30/70 -->
      <div class="grid grid-cols-1 lg:grid-cols-10 gap-4" style="height: calc(100vh - 140px); min-height: 650px;">
        
        <!-- PANEL IZQUIERDO: Lista Minimalista (30%) -->
        <div class="lg:col-span-3 bg-white dark:bg-zinc-900 rounded-xl overflow-hidden flex flex-col border border-gray-200 dark:border-zinc-800">
          
          <!-- Header minimalista con búsqueda -->
          <div class="p-3 border-b border-gray-200 dark:border-zinc-800">
            <!-- Búsqueda limpia -->
            <div class="relative mb-3">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Buscar..."
                class="w-full pl-9 pr-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500">
            </div>
            
            <!-- Filtro de estado -->
            <select
              v-model="statusFilter"
              class="w-full px-2 py-1.5 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border border-gray-200 dark:border-zinc-700 text-gray-600 dark:text-zinc-300 bg-white dark:bg-zinc-800">
              <option value="">Todos los estados</option>
              <option value="pending">Pendientes</option>
              <option value="completed">Completadas</option>
              <option value="cancelled">Canceladas</option>
            </select>
          </div>
          
          <!-- Lista minimalista -->
          <div class="flex-1 overflow-y-auto">
            
            <div
              v-for="returnItem in filteredReturns"
              :key="returnItem.id"
              @click="selectReturn(returnItem)"
              class="px-4 py-4 cursor-pointer transition-all border-b border-gray-200 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800 group relative"
              :class="selectedReturn?.id === returnItem.id ? 'bg-indigo-50 dark:bg-indigo-950/30' : 'bg-white dark:bg-zinc-900'"
            >
              <!-- Indicador de selección -->
              <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 transition-transform duration-200"
                   :class="selectedReturn?.id === returnItem.id ? 'scale-y-100' : 'scale-y-0'"></div>
              
              <div class="flex items-start gap-3">
                <div class="flex-1 min-w-0">
                  <div class="flex justify-between items-start">
                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                      {{ returnItem.number }}
                    </p>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                          :class="getStatusClasses(returnItem.status)">
                      {{ getStatusLabel(returnItem.status) }}
                    </span>
                  </div>
                  
                  <p class="text-xs text-gray-500 dark:text-zinc-400 truncate mt-0.5 font-medium">
                    {{ returnItem.customer?.name || 'Cliente General' }}
                  </p>
                  
                  <div class="flex items-center justify-between mt-2">
                    <span class="text-[10px] text-gray-400 dark:text-zinc-500 flex items-center gap-1">
                       <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                       {{ formatDate(returnItem.return_date) }}
                    </span>
                    <span class="text-xs font-black text-gray-900 dark:text-white">
                      ${{ formatCurrency(returnItem.total) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Estado vacío -->
            <div v-if="filteredReturns.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
              <svg class="w-8 h-8 text-gray-300 dark:text-zinc-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
              </svg>
              <p class="text-xs font-semibold text-gray-600 dark:text-zinc-300">Sin resultados</p>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Intenta con otros filtros</p>
            </div>
          </div>
        </div>

        <!-- PANEL DERECHO: Detalle Amplio (70%) -->
        <div class="lg:col-span-7 bg-white dark:bg-zinc-900 rounded-xl overflow-hidden flex flex-col border border-gray-200 dark:border-zinc-800">
          
          <!-- Estado: No seleccionado -->
          <div v-if="!selectedReturn" class="flex-1 flex flex-col items-center justify-center p-12 text-center bg-gray-50 dark:bg-zinc-900">
            <div class="w-24 h-24 bg-white dark:bg-zinc-800 rounded-full flex items-center justify-center shadow-sm mb-6 border border-gray-200 dark:border-zinc-700">
               <svg class="w-10 h-10 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
               </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Selecciona una devolución</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400 max-w-xs mx-auto">Haz clic en cualquier devolución de la lista izquierda para ver sus detalles completos.</p>
          </div>

          <!-- Estado: Devolución seleccionada -->
          <div v-else class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header del detalle con acciones contextuales -->
            <div class="p-6 border-b border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-3">
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                      {{ selectedReturn.number }}
                    </h2>
                    <span
                      class="px-3 py-1 rounded-full text-xs font-bold border"
                      :class="getStatusClasses(selectedReturn.status)">
                      {{ getStatusLabel(selectedReturn.status) }}
                    </span>
                  </div>
                  <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-zinc-400 font-medium">
                    <span class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      {{ formatDate(selectedReturn.return_date) }}
                    </span>
                    <span class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      {{ selectedReturn.customer?.name || 'Cliente General' }}
                    </span>
                  </div>
                </div>
                
                <!-- Acciones contextuales con texto -->
                <div class="flex items-center gap-2">
                  <button
                    @click="printReturn"
                    class="px-4 py-2 rounded-lg transition-all flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800 border border-transparent hover:border-gray-200 dark:hover:border-zinc-700"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir
                  </button>
                  
                  <button
                    @click="sendByEmail"
                    class="px-4 py-2 rounded-lg transition-all flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800 border border-transparent hover:border-gray-200 dark:hover:border-zinc-700"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Email
                  </button>
                </div>
              </div>
            </div>

            <!-- Contenido scrollable - Documento digital limpio -->
            <div class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-zinc-900">
              
              <!-- Sección: Información General (PRIMERO) -->
              <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4 mb-4">
                <h4 class="text-sm font-bold mb-4 text-gray-900 dark:text-white">Información General</h4>
                
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <p class="text-xs font-medium mb-1 text-gray-600 dark:text-zinc-400">Cliente</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ selectedReturn.customer?.name || 'Cliente General' }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs font-medium mb-1 text-gray-600 dark:text-zinc-400">Factura Original</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ selectedReturn.original_invoice?.number || 'N/A' }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs font-medium mb-1 text-gray-600 dark:text-zinc-400">Método de Reembolso</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ getRefundMethodLabel(selectedReturn.refund_method) }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs font-medium mb-1 text-gray-600 dark:text-zinc-400">Usuario</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ selectedReturn.user?.name || 'N/A' }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs font-medium mb-1 text-gray-600 dark:text-zinc-400">Caja</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">#{{ selectedReturn.cash_session?.id || 'N/A' }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs font-medium mb-1 text-gray-600 dark:text-zinc-400">Fecha</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatDate(selectedReturn.created_at) }}</p>
                  </div>
                </div>
              </div>

              <!-- Sección: Productos Devueltos -->
              <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 mb-4">
                <div class="border-b border-gray-200 dark:border-zinc-700 px-4 py-3 bg-gray-50 dark:bg-zinc-800/50">
                  <h4 class="text-sm font-bold text-gray-900 dark:text-white">Productos Devueltos</h4>
                </div>
                
                <div class="overflow-x-auto">
                  <table class="min-w-full">
                    <thead>
                      <tr class="bg-gray-50 dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">
                        <th class="text-left text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">Producto</th>
                        <th class="text-center text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">Cant.</th>
                        <th class="text-right text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">P. Unit.</th>
                        <th class="text-right text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                      <tr v-for="item in selectedReturn.return_items" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-6 py-4">
                          <p class="text-sm font-bold text-gray-900 dark:text-white">{{ item.product?.name || 'N/A' }}</p>
                          <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ item.product?.code || 'N/A' }}</p>
                        </td>
                        <td class="text-center px-6 py-4">
                          <span class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-bold rounded-md bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-zinc-300">
                            {{ item.quantity }}
                          </span>
                        </td>
                        <td class="text-right px-6 py-4 text-sm font-medium text-gray-600 dark:text-zinc-300">
                          ${{ formatCurrency(item.unit_price) }}
                        </td>
                        <td class="text-right px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                          ${{ formatCurrency(item.subtotal) }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  
                  <!-- Total -->
                  <div class="border-t border-gray-200 dark:border-zinc-700 px-4 py-4 bg-gray-50 dark:bg-zinc-800/50">
                    <div class="flex justify-end">
                      <div class="text-right">
                        <p class="text-xs font-medium text-gray-600 dark:text-zinc-400">Total Devuelto</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-500 mt-1">${{ formatCurrency(selectedReturn.total) }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sección: Razón de la Devolución -->
              <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 border-l-4 border-l-amber-500 dark:border-l-amber-600 p-4">
                <div class="flex items-start gap-3">
                  <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 bg-amber-100 dark:bg-amber-900/30">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <h4 class="text-sm font-bold mb-2 text-gray-900 dark:text-white">Razón de la Devolución</h4>
                    <p class="text-sm text-gray-600 dark:text-zinc-400">{{ selectedReturn.reason || 'No especificada' }}</p>
                  </div>
                </div>
              </div>

            </div>
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

// Emit para comunicarse con el padre
const emit = defineEmits(['change-module'])

// Composables
const { showToast } = useToast()

// Estado reactivo
const loading = ref(false)
const returns = ref([])
const selectedReturn = ref(null)
const searchTerm = ref('')
const statusFilter = ref('')
const refundMethodFilter = ref('')
const dateFrom = ref('')
const dateTo = ref('')

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
    
    // NO auto-seleccionar - el usuario debe elegir manualmente
    // selectedReturn.value permanece null hasta que el usuario haga clic
    
    showToast('Devoluciones cargadas exitosamente', 'success')
  } catch (error) {
    console.error('Error loading returns:', error)
    showToast(error.message || 'Error al cargar devoluciones', 'error')
    returns.value = []
  } finally {
    loading.value = false
  }
}

// Filtrado
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

  return filtered
})

// Estadísticas
const stats = computed(() => {
  const all = returns.value
  const completed = all.filter(r => r.status === 'completed')
  const pending = all.filter(r => r.status === 'pending')
  
  return {
    totalReturns: all.length,
    totalRefunded: all.reduce((sum, r) => sum + parseFloat(r.total || 0), 0),
    completedCount: completed.length,
    completedAmount: completed.reduce((sum, r) => sum + parseFloat(r.total || 0), 0),
    pendingCount: pending.length,
    pendingAmount: pending.reduce((sum, r) => sum + parseFloat(r.total || 0), 0),
    totalItemsReturned: all.reduce((sum, r) => 
      sum + ((r.return_items || r.items || []).reduce((s, i) => s + parseInt(i.quantity || 0), 0)), 0
    )
  }
})

// Acciones
const selectReturn = async (returnItem) => {
  try {
    loading.value = true
    const response = await returnsService.getReturnDetails(returnItem.id)
    
    // El backend ya devuelve return_items con productos incluidos
    selectedReturn.value = response.data
  } catch (error) {
    console.error('Error loading return details:', error)
    showToast(error.message || 'Error al cargar detalles', 'error')
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
}

const exportData = () => {
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

const navigateToNewReturn = () => {
  // Navegar al POS y abrir el modal de devoluciones
  emit('change-module', 'pos', { openReturnsModal: true })
}

const printReturn = () => {
  if (!selectedReturn.value) return
  showToast('Función de impresión en desarrollo', 'info')
}

const sendByEmail = () => {
  if (!selectedReturn.value) return
  showToast('Función de envío por email en desarrollo', 'info')
}

// Formateo
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

const getStatusClasses = (status) => {
  const s = status?.toLowerCase() || ''
  if (s === 'completed' || s === 'completada') return 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800'
  if (s === 'pending' || s === 'pendiente') return 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800'
  if (s === 'cancelled' || s === 'cancelada') return 'bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800'
  return 'bg-slate-50 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 border-slate-200 dark:border-zinc-700'
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
  // NO auto-seleccionar - dejar que el usuario elija manualmente
  selectedReturn.value = null
}, { deep: true })

onMounted(() => {
  loadReturns()
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
