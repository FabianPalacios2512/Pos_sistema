<template>
  <!-- Toast Notifications -->
  <ToastContainer />
  
  <!-- Gradiente en TODO el fondo -->
  <div class="font-sans bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8" style="height: 100%; display: flex; flex-direction: column;">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in" style="flex: 1; display: flex; flex-direction: column; min-height: 0;">
      
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
        <!-- KPIs Ejecutivos - Grid de 4 Columnas (Estilo Fantasma) -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- KPI: Sedes Activas (Estilo Fantasma) -->
            <div class="bg-white/80 dark:bg-zinc-800/40 backdrop-blur-sm rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Sedes Activas</p>
                  <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">{{ activeWarehouses }}</p>
                </div>
              </div>
            </div>

            <!-- KPI: Stock Global (Estilo Fantasma) -->
            <div class="bg-white/80 dark:bg-zinc-800/40 backdrop-blur-sm rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Stock Global</p>
                  <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">{{ totalProducts }}</p>
                </div>
              </div>
            </div>

            <!-- KPI: Sede Principal (Estilo Fantasma) -->
            <div class="bg-white/80 dark:bg-zinc-800/40 backdrop-blur-sm rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Sede Principal</p>
                  <p class="text-sm font-bold text-gray-800 dark:text-white mt-0.5 truncate">{{ defaultWarehouse || '-' }}</p>
                </div>
              </div>
            </div>

            <!-- KPI: Uso del Plan (Estilo Fantasma) -->
            <div v-if="planInfo" class="bg-white/80 dark:bg-zinc-800/40 backdrop-blur-sm rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Uso del Plan</p>
                  <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">
                    {{ planInfo.max_allowed === -1 ? 'Ilimitado' : `${planInfo.current_count}/${planInfo.max_allowed}` }}
                  </p>
                </div>
              </div>
            </div>
            
        </div>

        <!-- Master-Detail Layout Enterprise: 30/70 - Unificado como WhatsApp -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 transition-colors duration-300" style="height: calc(100vh - 280px); min-height: 550px;">
          <div class="grid grid-cols-1 lg:grid-cols-10 h-full">
        
          <!-- PANEL IZQUIERDO: Lista Minimalista (30%) -->
          <div class="lg:col-span-3 overflow-hidden flex flex-col border-r border-gray-200 dark:border-zinc-800 transition-colors duration-300">
            
            <!-- Header minimalista con búsqueda -->
            <div class="p-4 border-b border-gray-200 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-900">
              <!-- Búsqueda limpia -->
              <div class="relative mb-4">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                  v-model="searchTerm"
                  type="text"
                  placeholder="Buscar sedes..."
                  class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-300">
              </div>
              
              <!-- Contador -->
              <div class="text-xs text-gray-500 dark:text-zinc-400 font-medium">
                {{ filteredWarehouses.length }} sede{{ filteredWarehouses.length !== 1 ? 's' : '' }} encontrada{{ filteredWarehouses.length !== 1 ? 's' : '' }}
              </div>
            </div>
            
            <!-- Lista minimalista estilo WhatsApp -->
            <div class="flex-1 overflow-y-auto bg-white dark:bg-zinc-900 px-2">
              
              <div
                v-for="warehouse in filteredWarehouses"
                :key="warehouse.id"
                @click="selectWarehouse(warehouse)"
                class="px-3 py-3.5 my-1 cursor-pointer transition-all rounded-xl group relative"
                :class="[
                  selectedWarehouse?.id === warehouse.id 
                    ? 'bg-indigo-50 dark:bg-indigo-500/10 shadow-sm' 
                    : 'bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800/60'
                ]"
              >
                <!-- Borde izquierdo de selección -->
                <div 
                  v-if="selectedWarehouse?.id === warehouse.id"
                  class="absolute left-0 top-2 bottom-2 w-1 bg-indigo-500 rounded-r-full"
                ></div>
                
                <div class="flex items-center justify-between gap-3">
                  <!-- Info principal -->
                  <div class="flex items-center gap-3 flex-1 min-w-0">
                    <!-- Icono de sede -->
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                         :class="warehouse.is_default 
                           ? 'bg-emerald-50 dark:bg-emerald-500/10' 
                           : 'bg-blue-50 dark:bg-blue-500/10'">
                      <svg class="w-5 h-5" :class="warehouse.is_default ? 'text-emerald-500 dark:text-emerald-400' : 'text-blue-500 dark:text-blue-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                      </svg>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center gap-2">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-zinc-200 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                          {{ warehouse.name }}
                        </p>
                        <span v-if="warehouse.is_default" class="text-[9px] font-bold px-2 py-0.5 rounded-full border flex-shrink-0 bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800">
                          Principal
                        </span>
                      </div>
                      <div class="flex items-center gap-2 mt-1">
                        <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">
                          {{ warehouse.address || 'Sin dirección' }}
                        </p>
                        <span class="text-[10px] text-gray-400 dark:text-zinc-500">•</span>
                        <span class="text-[10px] text-gray-400 dark:text-zinc-500 flex-shrink-0">
                          {{ warehouse.products_count || 0 }} productos
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Badge de estado -->
                  <span class="text-[9px] font-bold px-2 py-0.5 rounded-full border flex-shrink-0"
                        :class="warehouse.active 
                          ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' 
                          : 'bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700'">
                    {{ warehouse.active ? 'Activa' : 'Inactiva' }}
                  </span>
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

          <!-- PANEL DERECHO: Detalle Amplio (70%) -->
          <div class="lg:col-span-7 overflow-hidden flex flex-col bg-gray-50/30 dark:bg-zinc-950/30 transition-colors duration-300">
            
            <!-- Estado: No seleccionado - Empty State Profesional estilo WhatsApp -->
            <div v-if="!selectedWarehouse" class="flex-1 flex flex-col items-center justify-center p-12 text-center bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-zinc-900/50 dark:via-zinc-900/30 dark:to-zinc-900/50 relative">
            
              <!-- Ilustración SVG profesional - Estilo igual a Facturas -->
              <div class="mb-8 relative">
                <!-- Efecto glow suave de fondo -->
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-200/30 via-transparent to-purple-200/30 dark:from-indigo-500/10 dark:to-purple-500/10 rounded-3xl blur-3xl scale-150"></div>
                
                <!-- Ilustración profesional estilo dashboard -->
                <svg class="w-48 h-48 relative z-10" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <!-- Sombra del documento -->
                  <rect x="48" y="38" width="88" height="110" rx="6" class="fill-gray-200/50 dark:fill-zinc-700/30"/>
                  
                  <!-- Documento principal -->
                  <rect x="44" y="32" width="88" height="110" rx="6" class="fill-white dark:fill-zinc-800" stroke-width="0"/>
                  <rect x="44" y="32" width="88" height="110" rx="6" class="fill-none stroke-gray-200 dark:stroke-zinc-700" stroke-width="1.5"/>
                  
                  <!-- Header del documento con icono de edificio -->
                  <rect x="54" y="44" width="40" height="5" rx="2.5" class="fill-gray-300 dark:fill-zinc-600"/>
                  <rect x="54" y="54" width="68" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                  <rect x="54" y="62" width="55" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                  <rect x="54" y="70" width="62" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                  
                  <!-- Línea separadora -->
                  <line x1="54" y1="82" x2="122" y2="82" class="stroke-gray-200 dark:stroke-zinc-700" stroke-width="1"/>
                  
                  <!-- Área de inventario/stock -->
                  <rect x="54" y="90" width="30" height="3" rx="1.5" class="fill-gray-200 dark:fill-zinc-600"/>
                  <rect x="94" y="88" width="28" height="7" rx="3.5" class="fill-indigo-100 dark:fill-indigo-500/20"/>
                  <rect x="98" y="90" width="20" height="3" rx="1.5" class="fill-indigo-500 dark:fill-indigo-400"/>
                  
                  <!-- Segunda línea -->
                  <rect x="54" y="102" width="25" height="3" rx="1.5" class="fill-gray-200 dark:fill-zinc-600"/>
                  <rect x="94" y="100" width="28" height="7" rx="3.5" class="fill-gray-100 dark:fill-zinc-700"/>
                  
                  <!-- Total/resumen -->
                  <rect x="54" y="118" width="35" height="4" rx="2" class="fill-gray-300 dark:fill-zinc-500"/>
                  <rect x="94" y="116" width="28" height="8" rx="4" class="fill-purple-500 dark:fill-purple-400"/>
                  
                  <!-- Icono de ubicación/sede -->
                  <circle cx="120" cy="48" r="14" class="fill-indigo-100 dark:fill-indigo-500/20"/>
                  <circle cx="120" cy="48" r="10" class="fill-indigo-500 dark:fill-indigo-400"/>
                  <path d="M120 43V48M120 48V53M120 48H115M120 48H125" class="stroke-white" stroke-width="2" stroke-linecap="round"/>
                  
                  <!-- Cajas de inventario -->
                  <rect x="142" y="95" width="24" height="20" rx="4" class="fill-purple-100 dark:fill-purple-500/20"/>
                  <rect x="145" y="98" width="18" height="14" rx="2" class="fill-purple-400 dark:fill-purple-500"/>
                  <path d="M149 105H159" class="stroke-white" stroke-width="2" stroke-linecap="round"/>
                  
                  <rect x="142" y="118" width="24" height="16" rx="4" class="fill-emerald-100 dark:fill-emerald-500/20"/>
                  <rect x="145" y="121" width="18" height="10" rx="2" class="fill-emerald-400 dark:fill-emerald-500"/>
                  
                  <!-- Gráfico de barras pequeño -->
                  <rect x="18" y="125" width="8" height="20" rx="2" class="fill-indigo-300 dark:fill-indigo-400/50"/>
                  <rect x="30" y="115" width="8" height="30" rx="2" class="fill-indigo-400 dark:fill-indigo-400/70"/>
                  <rect x="42" y="105" width="8" height="40" rx="2" class="fill-indigo-500 dark:fill-indigo-400"/>
                </svg>
              </div>
              
              <!-- Texto de bienvenida profesional -->
              <div class="relative z-10 max-w-md">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">
                  Centro de Sedes
                </h3>
                <p class="text-sm text-gray-500 dark:text-zinc-400 leading-relaxed mb-2">
                  Selecciona una sede del panel izquierdo para visualizar el inventario completo, productos y estadísticas.
                </p>
                <p class="text-xs text-gray-400 dark:text-zinc-500">
                  Gestiona el inventario de cada sede de forma rápida y segura.
                </p>
              </div>
              
              <!-- Footer de confianza estilo WhatsApp -->
              <div class="absolute bottom-6 left-0 right-0 flex items-center justify-center gap-2 text-xs text-gray-400 dark:text-zinc-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Datos de inventario sincronizados en tiempo real</span>
              </div>
            </div>

            <!-- Estado: Sede seleccionada - Loading -->
            <div v-else-if="loadingProducts" class="flex-1 flex items-center justify-center">
              <div class="text-center">
                <div class="w-16 h-16 border-4 border-indigo-200 dark:border-indigo-900 border-t-indigo-600 dark:border-t-indigo-400 rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-sm text-gray-600 dark:text-zinc-400">Cargando productos...</p>
              </div>
            </div>

            <!-- Estado: Productos cargados -->
            <div v-else-if="warehouseDetails" class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-zinc-900/50 transition-colors duration-300">
              
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
                    <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide mb-1">Stock Bajo</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ warehouseDetails?.summary?.low_stock_count || 0 }}</p>
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
      <div v-if="activeTab === 'transfers'" style="flex: 1; display: flex; flex-direction: column; min-height: 0;">
        <StockTransfersView ref="transfersComponent" :embedded="true" />
      </div>

    </div>
  </div>

    <!-- Modal Crear/Editar Sede -->
    <WarehouseModal
      v-if="showModal"
      :warehouse="selectedWarehouse"
      @close="closeModal"
      @saved="handleSaved"
    />

    <!-- Modal de Límite de Plan -->
    <Teleport to="body">
      <div 
        v-if="showLimitModal" 
        class="fixed top-0 left-0 right-0 bottom-0 bg-black/50 dark:bg-black/70 flex items-center justify-center p-4"
        style="z-index: 99999; position: fixed; inset: 0;">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 max-w-md w-full overflow-hidden border border-gray-300 dark:border-zinc-800 animate-scale-in">
          
          <!-- Header profesional -->
          <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-blue-50 dark:bg-blue-950">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Límite de Sedes Alcanzado</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Plan {{ planInfo?.plan_name || 'Actual' }}</p>
              </div>
              <button 
                @click="showLimitModal = false"
                class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Contenido -->
          <div class="px-6 py-5">
            <p class="text-sm text-gray-700 dark:text-zinc-300 leading-relaxed">
              Has alcanzado el límite de <span class="font-bold text-blue-600 dark:text-blue-400">{{ planInfo?.max_allowed }} sedes</span> para tu plan actual.
            </p>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-3 bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-gray-200 dark:border-zinc-700/50">
              Actualiza a <span class="font-bold text-gray-900 dark:text-white">Enterprise</span> para gestionar sedes ilimitadas y desbloquear todo el potencial de tu negocio.
            </p>
          </div>

          <!-- Footer -->
          <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex justify-end space-x-3">
            <button 
              @click="showLimitModal = false"
              type="button"
              class="px-5 py-2.5 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 text-sm font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
              Cerrar
            </button>
            <button 
              @click="goToPlans"
              class="px-5 py-2.5 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-sm font-bold rounded-lg shadow-lg shadow-blue-400/40 dark:shadow-blue-900/50 transition-all duration-300 flex items-center space-x-2">
              <span>Ver Planes</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
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
const planInfo = ref(null)

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
      if (data.plan_info) {
        planInfo.value = data.plan_info
      }
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
const showLimitModal = ref(false)
const router = useRouter()

const goToPlans = () => {
  showLimitModal.value = false
  router.push('/pos/settings?section=plan')
}

// CRUD operations
const openCreateModal = () => {
  if (planInfo.value && !planInfo.value.can_create) {
    showLimitModal.value = true
    return
  }
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

// Handler para tecla ESC - deseleccionar sede
const handleKeyDown = (event) => {
  if (event.key === 'Escape' && selectedWarehouse.value && !showModal.value && !showLimitModal.value) {
    selectedWarehouse.value = null
    warehouseDetails.value = null
  }
}

// Lifecycle
onMounted(() => {
  loadWarehouses()
  document.addEventListener('keydown', handleKeyDown)
})

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleKeyDown)
})
</script>
