<template>
  <!-- Toast Notifications -->
  <ToastContainer />
  
  <!-- Gradiente en TODO el fondo -->
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-zinc-900 dark:via-zinc-950 dark:to-black transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header sin borde, sin contenedor separado -->
      <div class="flex items-center justify-between pb-4">
        <!-- Título y Subtítulo -->
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Sedes</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
            {{ activeTab === 'warehouses' ? 'Administra las tiendas y bodegas del negocio' : 'Gestiona movimientos de inventario entre sedes' }}
          </p>
        </div>
    
        <!-- Botones de Acción -->
        <div class="flex items-center gap-3">
          <!-- Botones para Sedes -->
          <template v-if="activeTab === 'warehouses'">
            <button
              @click="loadWarehouses"
              class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 active:scale-95 flex items-center gap-2 group">
              <svg class="w-4 h-4 text-slate-400 dark:text-zinc-500 group-hover:text-emerald-600 dark:group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Actualizar</span>
            </button>
            
            <button
              @click="openCreateModal"
              class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 hover:shadow-slate-400/60 dark:hover:shadow-slate-900/70 transition-all duration-300 transform active:scale-95 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
              </svg>
              <span>Nueva Sede</span>
            </button>
          </template>

          <!-- Botones para Traslados -->
          <template v-else-if="activeTab === 'transfers'">
            <button
              @click="refreshTransfers"
              class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 active:scale-95 flex items-center gap-2 group">
              <svg class="w-4 h-4 text-slate-400 dark:text-zinc-500 group-hover:text-emerald-600 dark:group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Actualizar</span>
            </button>
            
            <button
              @click="openTransferModal"
              class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 hover:shadow-slate-400/60 dark:hover:shadow-slate-900/70 transition-all duration-300 transform active:scale-95 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
              </svg>
              <span>Nuevo Traslado</span>
            </button>
          </template>
        </div>
      </div>

      <!-- Pestañas de navegación -->
      <div class="flex items-center gap-2 border-b border-gray-300 dark:border-zinc-700">
        <button
          @click="activeTab = 'warehouses'"
          :class="[
            'px-6 py-3 text-sm font-bold rounded-t-xl transition-all duration-200',
            activeTab === 'warehouses'
              ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white border-t-2 border-x-2 border-gray-300 dark:border-zinc-700 border-b-0 -mb-px'
              : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
          ]">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span>Sedes</span>
          </div>
        </button>
        
        <button
          @click="activeTab = 'transfers'"
          :class="[
            'px-6 py-3 text-sm font-bold rounded-t-xl transition-all duration-200',
            activeTab === 'transfers'
              ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white border-t-2 border-x-2 border-gray-300 dark:border-zinc-700 border-b-0 -mb-px'
              : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
          ]">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
            </svg>
            <span>Traslados</span>
          </div>
        </button>
      </div>

      <!-- Contenido según pestaña activa -->
      <div v-if="activeTab === 'warehouses'" class="space-y-6">
        <!-- KPIs Ejecutivos - Grid de 4 Columnas -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- KPI: Total Sedes -->
            <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-lg dark:hover:shadow-black/50">
              <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-blue-50 dark:bg-blue-950 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Sedes</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ totalWarehouses }}</p>
                </div>
              </div>
            </div>

            <!-- KPI: Sedes Activas -->
            <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-lg dark:hover:shadow-black/50">
              <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-950 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Sedes Activas</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ activeWarehouses }}</p>
                </div>
              </div>
            </div>

            <!-- KPI: Stock Global -->
            <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-lg dark:hover:shadow-black/50">
              <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-purple-50 dark:bg-purple-950 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Stock Global</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ totalProducts }}</p>
                </div>
              </div>
            </div>

            <!-- KPI: Sede Principal -->
            <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-lg dark:hover:shadow-black/50">
              <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-indigo-50 dark:bg-indigo-950 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Sede Principal</p>
                  <p class="text-sm font-bold text-gray-900 dark:text-white mt-0.5 truncate">{{ defaultWarehouse || '-' }}</p>
                </div>
              </div>
            </div>
            
        </div>

        <!-- Master-Detail Layout: Reducir ancho del panel izquierdo -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6" style="height: calc(100vh - 240px); min-height: 650px;">
        
          <!-- PANEL IZQUIERDO: Lista Minimalista (25%) -->
          <div class="lg:col-span-3 bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden flex flex-col border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 transition-colors duration-300">
            
            <!-- Header minimalista con búsqueda -->
            <div class="p-4 border-b border-gray-200 dark:border-zinc-800">
              <!-- Búsqueda limpia -->
              <div class="relative mb-4">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                  v-model="searchTerm"
                  type="text"
                  placeholder="Buscar sedes..."
                  class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-300">
              </div>
              
              <!-- Contador -->
              <div class="text-xs text-gray-500 dark:text-zinc-400 font-medium">
                {{ filteredWarehouses.length }} sede{{ filteredWarehouses.length !== 1 ? 's' : '' }} encontrada{{ filteredWarehouses.length !== 1 ? 's' : '' }}
              </div>
            </div>
            
            <!-- Lista minimalista de sedes -->
            <div class="flex-1 overflow-y-auto bg-white dark:bg-zinc-900">
              
              <div
                v-for="warehouse in filteredWarehouses"
                :key="warehouse.id"
                @click="selectWarehouse(warehouse)"
                class="px-4 py-4 cursor-pointer transition-all border-b border-gray-200 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 group relative"
                :class="[
                  selectedWarehouse?.id === warehouse.id ? 'bg-indigo-50 dark:bg-indigo-500/10' : 'bg-white dark:bg-zinc-900',
                  selectedWarehouse?.id === warehouse.id ? 'border-l-4 border-indigo-500 pl-3' : 'border-l-4 border-transparent'
                ]"
              >
                <!-- Indicador de selección -->
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 transition-transform duration-200"
                     :class="selectedWarehouse?.id === warehouse.id ? 'scale-y-100' : 'scale-y-0'"></div>
                
                <div class="flex items-start gap-3">
                  <!-- Icono de sede -->
                  <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                       :class="warehouse.is_default 
                         ? 'bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800' 
                         : 'bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800'">
                    <svg class="w-5 h-5" :class="warehouse.is_default ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  
                  <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                      <p class="text-sm font-bold text-gray-800 dark:text-zinc-200 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        {{ warehouse.name }}
                      </p>
                      <span v-if="warehouse.is_default" class="text-[10px] font-bold px-2 py-0.5 rounded-full border bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800">
                        Principal
                      </span>
                    </div>
                    
                    <p class="text-xs text-gray-500 dark:text-zinc-400 truncate mt-0.5 font-medium">
                      {{ warehouse.address || 'Sin dirección' }}
                    </p>
                    
                    <div class="flex items-center justify-between mt-2">
                      <span class="text-[10px] text-gray-400 dark:text-zinc-500 flex items-center gap-1">
                         <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                         {{ warehouse.product_count || 0 }} productos
                      </span>
                      <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                            :class="warehouse.active 
                              ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' 
                              : 'bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700'">
                        {{ warehouse.active ? 'Activa' : 'Inactiva' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Estado vacío -->
              <div v-if="filteredWarehouses.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
                <svg class="w-8 h-8 text-gray-300 dark:text-zinc-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <p class="text-xs font-semibold text-gray-600 dark:text-zinc-300">Sin resultados</p>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Intenta con otra búsqueda</p>
              </div>
            </div>
          </div>

          <!-- PANEL DERECHO: Detalle Amplio (75%) -->
          <div class="lg:col-span-9 bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden flex flex-col border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 transition-colors duration-300">
            
            <!-- Estado: No seleccionado -->
            <div v-if="!selectedWarehouse" class="flex-1 flex flex-col items-center justify-center p-12 text-center bg-gray-50 dark:bg-zinc-900/50">
              <div class="w-24 h-24 bg-white dark:bg-zinc-800 rounded-full flex items-center justify-center shadow-sm mb-6 border border-gray-100 dark:border-zinc-700">
                 <svg class="w-10 h-10 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                 </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Selecciona una sede</h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400 max-w-xs mx-auto">Haz clic en cualquier sede de la lista izquierda para ver sus productos y detalles completos.</p>
            </div>

            <!-- Estado: Sede seleccionada -->
            <div v-else-if="loadingProducts" class="flex-1 flex items-center justify-center">
              <div class="text-center">
                <div class="w-16 h-16 border-4 border-indigo-200 dark:border-indigo-900 border-t-indigo-600 dark:border-t-indigo-400 rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-sm text-gray-600 dark:text-zinc-400">Cargando productos...</p>
              </div>
            </div>

            <!-- Estado: Productos cargados -->
            <div v-else-if="warehouseDetails" class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-zinc-900 transition-colors duration-300">
              
              <!-- Header del detalle con acciones contextuales -->
              <div class="p-8 border-b border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                      <h2 class="text-3xl font-black text-gray-900 dark:text-zinc-200 tracking-tight">
                        {{ selectedWarehouse.name }}
                      </h2>
                      <span v-if="selectedWarehouse.is_default"
                        class="px-3 py-1 rounded-full text-xs font-bold border bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800">
                        PRINCIPAL
                      </span>
                      <span
                        class="px-3 py-1 rounded-full text-xs font-bold border"
                        :class="selectedWarehouse.active 
                          ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' 
                          : 'bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700'">
                        {{ selectedWarehouse.active ? 'ACTIVA' : 'INACTIVA' }}
                      </span>
                    </div>
                    <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-zinc-400 font-medium">
                      <span v-if="selectedWarehouse.address" class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ selectedWarehouse.address }}
                      </span>
                      <span v-if="selectedWarehouse.phone" class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        {{ selectedWarehouse.phone }}
                      </span>
                    </div>
                  </div>

                  <!-- Botones de acción -->
                  <div class="flex items-center gap-2">
                    <button
                      @click="editWarehouse(selectedWarehouse)"
                      class="p-2 text-gray-600 dark:text-zinc-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg border border-transparent hover:border-amber-100 dark:hover:border-amber-900/30 transition-all duration-200">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click="viewInventory(selectedWarehouse)"
                      class="p-2 text-gray-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg border border-transparent hover:border-blue-100 dark:hover:border-blue-900/30 transition-all duration-200">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    <button
                      v-if="!selectedWarehouse.is_default"
                      @click="deleteWarehouse(selectedWarehouse)"
                      class="p-2 text-gray-600 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg border border-transparent hover:border-rose-100 dark:hover:border-rose-900/30 transition-all duration-200">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- KPIs de la sede -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                  <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
                    <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide mb-1">Productos</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ warehouseDetails?.summary?.total_products || 0 }}</p>
                  </div>
                  <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
                    <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide mb-1">Stock Total</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ warehouseDetails?.summary?.total_stock || 0 }}</p>
                  </div>
                  <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
                    <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide mb-1">Valor Inventario</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">${{ formatNumber(warehouseDetails?.summary?.total_value || 0) }}</p>
                  </div>
                  <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
                    <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide mb-1">Sin Stock</p>
                    <p class="text-2xl font-bold text-rose-600 dark:text-rose-400">{{ warehouseDetails?.summary?.out_of_stock_count || 0 }}</p>
                  </div>
                </div>
              </div>

              <!-- Contenido scrolleable: Tabla de productos -->
              <div class="flex-1 overflow-y-auto p-6 bg-gray-50 dark:bg-zinc-900/50">
                
                <!-- Búsqueda de productos -->
                <div class="mb-4">
                  <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                      v-model="productSearchTerm"
                      type="text"
                      placeholder="Buscar productos por nombre o SKU..."
                      class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all duration-300">
                  </div>
                </div>

                <!-- Tabla de productos -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl overflow-hidden border border-gray-200 dark:border-zinc-800 shadow-sm">
                  <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800">
                    <thead class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Producto</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">SKU</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Categoría</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Stock</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Min/Max</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Precio</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
                      <tr v-for="product in filteredProducts" :key="product.id" 
                          class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-all duration-200">
                        <td class="px-4 py-3">
                          <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                              <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover" />
                              <svg v-else class="w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                              </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                              <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ product.name }}</p>
                              <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">{{ product.barcode || 'Sin código' }}</p>
                            </div>
                          </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span class="text-xs font-mono text-gray-600 dark:text-zinc-400">{{ product.sku }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span class="text-xs text-gray-600 dark:text-zinc-400">{{ product.category_name || 'Sin categoría' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span class="font-bold text-sm"
                                :class="product.stock === 0 ? 'text-rose-600 dark:text-rose-400' : product.stock <= product.min_stock ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">
                            {{ product.stock }}
                          </span>
                          <span class="text-xs text-gray-500 dark:text-zinc-400 ml-1">{{ product.measurement_unit || product.unit || 'un' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span class="text-xs text-gray-500 dark:text-zinc-400">{{ product.min_stock }}/{{ product.max_stock || '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                          <div class="font-mono text-sm font-bold text-gray-900 dark:text-white">${{ formatNumber(product.sale_price) }}</div>
                          <div class="text-xs text-gray-500 dark:text-zinc-400">Costo: ${{ formatNumber(product.cost_price) }}</div>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <!-- Estado vacío de productos -->
                  <div v-if="filteredProducts.length === 0" class="py-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 dark:text-zinc-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p class="text-sm font-semibold text-gray-600 dark:text-zinc-300">No hay productos</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Esta sede no tiene productos registrados</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>
      <!-- Fin contenido Sedes -->

      <!-- Contenido Traslados -->
      <div v-if="activeTab === 'transfers'">
        <StockTransfersView ref="transfersComponent" :embedded="true" />
      </div>

    <!-- Modal Crear/Editar Sede -->
    <WarehouseModal
      v-if="showModal"
      :warehouse="selectedWarehouse"
      @close="closeModal"
      @saved="handleSaved"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { warehouseService } from '@/services/warehouseService'
import WarehouseModal from './warehouses/WarehouseModal.vue'
import StockTransfersView from './StockTransfersView.vue'
import ToastContainer from './ToastContainer.vue'

// Estados
const activeTab = ref('warehouses') // 'warehouses' o 'transfers'
const warehouses = ref([])
const selectedWarehouse = ref(null)
const warehouseDetails = ref(null)
const loadingProducts = ref(false)
const searchTerm = ref('')
const productSearchTerm = ref('')
const transfersComponent = ref(null)

// Computed
const filteredWarehouses = computed(() => {
  if (!searchTerm.value) return warehouses.value
  const term = searchTerm.value.toLowerCase()
  return warehouses.value.filter(w => 
    w.name.toLowerCase().includes(term) || 
    (w.address && w.address.toLowerCase().includes(term))
  )
})

const filteredProducts = computed(() => {
  if (!warehouseDetails.value?.products) return []
  if (!productSearchTerm.value) return warehouseDetails.value.products
  
  const term = productSearchTerm.value.toLowerCase()
  return warehouseDetails.value.products.filter(p =>
    p.name.toLowerCase().includes(term) ||
    p.sku.toLowerCase().includes(term) ||
    (p.barcode && p.barcode.toLowerCase().includes(term))
  )
})

const totalWarehouses = computed(() => warehouses.value.length)
const activeWarehouses = computed(() => warehouses.value.filter(w => w.active).length)
const totalProducts = computed(() => {
  const total = warehouses.value.reduce((sum, w) => sum + (w.products_count || 0), 0)
  console.log('📊 Total productos calculado:', total, 'warehouses:', warehouses.value.map(w => ({ name: w.name, count: w.products_count })))
  return total
})
const defaultWarehouse = computed(() => warehouses.value.find(w => w.is_default)?.name || '-')

// Métodos
const loadWarehouses = async () => {
  try {
    console.log('🔄 Cargando warehouses...')
    const data = await warehouseService.getAll()
    console.log('📦 Respuesta del servicio:', data)
    
    // El API devuelve { warehouses: [...], plan_info: {...} }
    if (data && data.warehouses && Array.isArray(data.warehouses)) {
      warehouses.value = data.warehouses
      console.log('✅ Warehouses cargados:', warehouses.value.length)
    } else if (data && data.data && Array.isArray(data.data)) {
      warehouses.value = data.data
      console.log('✅ Warehouses cargados (desde data.data):', warehouses.value.length)
    } else if (Array.isArray(data)) {
      warehouses.value = data
      console.log('✅ Warehouses cargados (array directo):', warehouses.value.length)
    } else {
      warehouses.value = []
      console.warn('⚠️ Formato inesperado de respuesta:', data)
    }
  } catch (error) {
    console.error('❌ Error loading warehouses:', error)
    warehouses.value = []
  }
}

const selectWarehouse = async (warehouse) => {
  selectedWarehouse.value = warehouse
  productSearchTerm.value = ''
  await loadWarehouseProducts(warehouse.id)
}

const loadWarehouseProducts = async (warehouseId) => {
  loadingProducts.value = true
  try {
    const response = await warehouseService.getInventory(warehouseId)
    console.log('📦 Respuesta inventory:', response)
    
    // El API devuelve { success, data: { warehouse, summary, products } }
    if (response && response.data) {
      warehouseDetails.value = response.data
    } else {
      warehouseDetails.value = response
    }
    
    console.log('✅ Detalles cargados:', warehouseDetails.value)
  } catch (error) {
    console.error('❌ Error loading warehouse products:', error)
    warehouseDetails.value = null
  } finally {
    loadingProducts.value = false
  }
}

const formatNumber = (value) => {
  return new Intl.NumberFormat('es-CO').format(value || 0)
}

// Modal state
const showModal = ref(false)

// CRUD operations
const openCreateModal = () => {
  selectedWarehouse.value = null
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedWarehouse.value = null
}

const editWarehouse = (warehouse) => {
  selectedWarehouse.value = warehouse
  showModal.value = true
}

const viewInventory = async (warehouse) => {
  try {
    const data = await warehouseService.getInventory(warehouse.id)
    const totalProducts = data.summary?.total_products || 0
    const totalStock = data.summary?.total_stock || 0
    alert(`📦 Inventario de ${warehouse.name}\n\n` +
          `Total de productos: ${totalProducts}\n` +
          `Stock total: ${totalStock} unidades\n\n` +
          `(Vista detallada en desarrollo)`)
  } catch (error) {
    console.error('Error al cargar inventario:', error)
    alert('Error al cargar el inventario')
  }
}

const deleteWarehouse = async (warehouse) => {
  if (!confirm(`¿Eliminar la sede "${warehouse.name}"?`)) return
  
  try {
    await warehouseService.delete(warehouse.id)
    await loadWarehouses()
    
    // Si se eliminó la sede seleccionada, limpiar selección
    if (selectedWarehouse.value?.id === warehouse.id) {
      selectedWarehouse.value = null
      warehouseDetails.value = null
    }
    
    alert('Sede eliminada exitosamente')
  } catch (error) {
    console.error('Error al eliminar sede:', error)
    alert(error?.message || 'Error al eliminar la sede')
  }
}

const handleSaved = () => {
  console.log('✅ handleSaved ejecutado - cerrando modal y recargando...')
  closeModal()
  setTimeout(() => {
    loadWarehouses()
  }, 100)
}

// Métodos para traslados
const refreshTransfers = () => {
  if (transfersComponent.value && typeof transfersComponent.value.fetchTransfers === 'function') {
    transfersComponent.value.fetchTransfers()
  }
}

const openTransferModal = () => {
  if (transfersComponent.value && typeof transfersComponent.value.openCreateModal === 'function') {
    transfersComponent.value.openCreateModal()
  }
}

// Lifecycle
onMounted(() => {
  loadWarehouses()
})
</script>
