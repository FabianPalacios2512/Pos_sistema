<template>
  <!-- Toast Notifications -->
  <ToastContainer />
  
  <!-- Layout Full Height -->
  <div class="h-full font-sans bg-gray-50 dark:bg-[#131314] transition-colors duration-300 overflow-hidden flex flex-col">
    <div class="flex-none px-6 lg:px-8 pt-5 pb-4 space-y-5 animate-fade-in">
      
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-black text-gray-900 dark:text-white">Gestión de Sedes</h1>
          <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">Administra, controla y monitorea todas las sedes de tu empresa</p>
        </div>
    
        <!-- Botones de Acción -->
        <div class="flex items-center gap-3">
          <!-- Botones para Sedes -->
          <template v-if="activeTab === 'warehouses'">
            <button
              @click="loadWarehouses"
              class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-200 text-sm font-semibold rounded-md border border-gray-300 dark:border-zinc-700 shadow-sm transition-all duration-200 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Refrescar</span>
            </button>
            
            <button
              @click="openCreateModal"
              class="px-6 py-2.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-zinc-900 text-sm font-semibold rounded-md  transition-all duration-300 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
              </svg>
              <span>Nueva Sede</span>
            </button>
          </template>

          <!-- Botones para Traslados -->
          <template v-else-if="activeTab === 'transfers'">
            <button
              @click="refreshTransfers"
              class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-200 text-sm font-semibold rounded-md border border-gray-300 dark:border-zinc-700 shadow-sm transition-all duration-200 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Refrescar</span>
            </button>
            
            <button
              @click="openTransferModal"
              class="px-6 py-2.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-zinc-900 text-sm font-semibold rounded-md  transition-all duration-300 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
              </svg>
              <span>Nuevo Traslado</span>
            </button>
          </template>

          <!-- Botones para Personal -->
          <template v-else-if="activeTab === 'staff'">
            <button
              @click="loadStaffData"
              class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-200 text-sm font-semibold rounded-md border border-gray-300 dark:border-zinc-700 shadow-sm transition-all duration-200 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Refrescar</span>
            </button>
            
            <button
              @click="showStaffTransferModal = true"
              class="px-6 py-2.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-zinc-900 text-sm font-semibold rounded-md transition-all duration-300 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
              </svg>
              <span>Trasladar Empleado</span>
            </button>
          </template>

          <!-- Botones para Matriz -->
          <template v-else-if="activeTab === 'matrix'">
            <button
              @click="$refs.matrixComponent?.loadMatrix()"
              class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-200 text-sm font-semibold rounded-md border border-gray-300 dark:border-zinc-700 shadow-sm transition-all duration-200 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Refrescar</span>
            </button>
          </template>
        </div>
      </div>

      <!-- Pestañas de navegación -->
      <div class="flex items-center gap-1 bg-gray-100/80 dark:bg-zinc-900/80 rounded-xl p-1 border border-gray-200 dark:border-zinc-800 w-fit">
        <button
          @click="activeTab = 'warehouses'"
          :class="[
            'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
            activeTab === 'warehouses'
              ? 'bg-white dark:bg-zinc-800 text-gray-900 dark:text-white shadow-sm'
              : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
          ]">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span>Sedes</span>
          </div>
        </button>
        
        <button
          @click="activeTab = 'transfers'"
          :class="[
            'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
            activeTab === 'transfers'
              ? 'bg-white dark:bg-zinc-800 text-gray-900 dark:text-white shadow-sm'
              : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
          ]">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
            </svg>
            <span>Traslados</span>
          </div>
        </button>

        <button
          @click="activeTab = 'staff'"
          :class="[
            'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
            activeTab === 'staff'
              ? 'bg-white dark:bg-zinc-800 text-gray-900 dark:text-white shadow-sm'
              : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
          ]">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
            </svg>
            <span>Personal</span>
          </div>
        </button>

        <button
          @click="activeTab = 'matrix'"
          :class="[
            'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
            activeTab === 'matrix'
              ? 'bg-white dark:bg-zinc-800 text-gray-900 dark:text-white shadow-sm'
              : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
          ]">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M10.875 12h-1.5m0 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M9.375 12c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m1.5-5.25c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125"/>
            </svg>
            <span>Matriz</span>
          </div>
        </button>
      </div>
    </div>

      <!-- Contenido Sedes -->
      <div v-if="activeTab === 'warehouses'" class="flex-1 flex flex-col min-h-0 px-6 lg:px-8 pb-6 space-y-5">
        
        <!-- KPIs removidas - ruido visual eliminado -->
    
    <!-- Master-Detail Panel -->
    <div class="flex-1 rounded-md overflow-hidden border border-gray-200 dark:border-zinc-800 shadow-sm transition-colors duration-300">
      <div class="grid grid-cols-1 lg:grid-cols-10 h-full">
        
        <!-- PANEL IZQUIERDO: Selector de Sedes (30%) -->
        <div class="lg:col-span-3 overflow-hidden flex flex-col bg-gray-50 dark:bg-zinc-900 border-r border-gray-200 dark:border-zinc-800 transition-colors duration-300">
          
          <!-- Búsqueda -->
          <div class="p-3 border-b border-gray-200 dark:border-zinc-800">
            <div class="relative">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Buscar sede..."
                class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200">
            </div>
          </div>
          
          <!-- Lista de sedes -->
          <div class="flex-1 overflow-y-auto px-2 py-2">
            
            <div
              v-for="warehouse in filteredWarehouses"
              :key="warehouse.id"
              @click="selectWarehouse(warehouse)"
              class="px-4 py-3.5 my-1 cursor-pointer transition-all duration-200 rounded-md group relative"
              :class="[
                selectedWarehouse?.id === warehouse.id 
                  ? 'bg-emerald-50 dark:bg-emerald-950/30 border-l-4 border-emerald-600 dark:border-emerald-500' 
                  : 'hover:bg-gray-50 dark:hover:bg-zinc-800/40 border-l-4 border-transparent'
              ]"
            >
              
              <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                  <!-- Icono de sede -->
                  <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 border"
                       :class="warehouse.is_default 
                         ? 'bg-emerald-50 dark:bg-emerald-950 border-emerald-100 dark:border-emerald-800' 
                         : 'bg-blue-50 dark:bg-blue-950 border-blue-100 dark:border-blue-800'">
                    <svg class="w-5 h-5" :class="warehouse.is_default ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                      <p class="text-sm font-semibold truncate"
                         :class="selectedWarehouse?.id === warehouse.id ? 'text-gray-900 dark:text-white' : 'text-gray-700 dark:text-zinc-300'">
                        {{ warehouse.name }}
                      </p>
                      <span v-if="warehouse.is_default" class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0 bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 uppercase">
                        Principal
                      </span>
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                      <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">
                        {{ warehouse.address || 'Sin dirección' }}
                      </p>
                      <span class="text-[10px] text-gray-300 dark:text-zinc-600">•</span>
                      <span class="text-xs text-gray-400 dark:text-zinc-500 flex-shrink-0 font-medium">
                        {{ warehouse.products_count || 0 }} prod.
                      </span>
                    </div>
                  </div>
                </div>
                
                <!-- Badge de estado -->
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full flex-shrink-0 border uppercase tracking-wide"
                      :class="warehouse.active 
                        ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' 
                        : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'">
                  {{ warehouse.active ? 'Activa' : 'Inactiva' }}
                </span>
              </div>
            </div>
            
            <!-- Estado vacío -->
            <div v-if="filteredWarehouses.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
              <svg class="w-10 h-10 text-gray-300 dark:text-zinc-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              <p class="text-sm font-medium text-gray-600 dark:text-zinc-300">Sin resultados</p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Intenta con otra búsqueda</p>
            </div>
          </div>
        </div>

        <!-- PANEL DERECHO: Dashboard de Sede (70%) -->
        <div class="lg:col-span-7 overflow-hidden flex flex-col bg-white dark:bg-zinc-900 transition-colors duration-300">
            
            <!-- Empty State -->
            <div v-if="!selectedWarehouse" class="flex-1 flex flex-col items-center justify-center p-12 text-center relative">
            
              <div class="mb-8 relative">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-200/20 via-transparent to-blue-200/20 dark:from-purple-500/5 dark:to-blue-500/5 rounded-3xl blur-3xl scale-150"></div>
                
                <!-- Ilustración principal -->
                <svg class="w-48 h-48 relative z-10" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <!-- Sombra del documento -->
                  <rect x="48" y="38" width="88" height="110" rx="6" class="fill-gray-200/50 dark:fill-zinc-700/30"/>
                  
                  <!-- Documento principal -->
                  <rect x="44" y="32" width="88" height="110" rx="6" class="fill-white dark:fill-zinc-800" stroke-width="0"/>
                  <rect x="44" y="32" width="88" height="110" rx="6" class="fill-none stroke-gray-200 dark:stroke-zinc-700" stroke-width="1.5"/>
                  
                  <!-- Header con icono de edificio -->
                  <rect x="54" y="44" width="40" height="5" rx="2.5" class="fill-gray-300 dark:fill-zinc-600"/>
                  <rect x="54" y="54" width="68" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                  <rect x="54" y="62" width="55" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                  <rect x="54" y="70" width="62" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                  
                  <!-- Línea separadora -->
                  <line x1="54" y1="82" x2="122" y2="82" class="stroke-gray-200 dark:stroke-zinc-700" stroke-width="1"/>
                  
                  <!-- Área de inventario -->
                  <rect x="54" y="90" width="30" height="3" rx="1.5" class="fill-gray-200 dark:fill-zinc-600"/>
                  <rect x="94" y="88" width="28" height="7" rx="3.5" class="fill-purple-100 dark:fill-purple-500/20"/>
                  <rect x="98" y="90" width="20" height="3" rx="1.5" class="fill-purple-500 dark:fill-purple-400"/>
                  
                  <!-- Segunda línea -->
                  <rect x="54" y="102" width="25" height="3" rx="1.5" class="fill-gray-200 dark:fill-zinc-600"/>
                  <rect x="94" y="100" width="28" height="7" rx="3.5" class="fill-gray-100 dark:fill-zinc-700"/>
                  
                  <!-- Total -->
                  <rect x="54" y="118" width="35" height="4" rx="2" class="fill-gray-300 dark:fill-zinc-500"/>
                  <rect x="94" y="116" width="28" height="8" rx="4" class="fill-emerald-500 dark:fill-emerald-400"/>
                  
                  <!-- Icono de sede -->
                  <circle cx="120" cy="48" r="14" class="fill-blue-100 dark:fill-blue-500/20"/>
                  <circle cx="120" cy="48" r="10" class="fill-blue-500 dark:fill-blue-400"/>
                  <path d="M116 52V45M120 52V43M124 52V47" class="stroke-white" stroke-width="2" stroke-linecap="round"/>
                  
                  <!-- Cajas de inventario -->
                  <rect x="140" y="100" width="28" height="28" rx="4" class="fill-purple-100 dark:fill-purple-500/20"/>
                  <rect x="144" y="104" width="20" height="20" rx="2" class="fill-purple-400 dark:fill-purple-500"/>
                  <path d="M149 114H159" class="stroke-white" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </div>
              
              <!-- Texto de bienvenida profesional -->
              <div class="relative z-10 max-w-md">
                <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-3">
                  Centro de Sedes
                </h3>
                <p class="text-sm text-gray-500 dark:text-zinc-400 leading-relaxed mb-2">
                  Selecciona una sede del panel izquierdo para visualizar el inventario completo, productos y estadísticas.
                </p>
              </div>
            </div>

            <!-- Estado: Sede seleccionada - Loading -->
            <div v-else-if="loadingProducts" class="flex-1 flex items-center justify-center">
              <div class="text-center">
                <div class="w-12 h-12 border-4 border-gray-200 dark:border-zinc-700 border-t-gray-600 dark:border-t-zinc-300 rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-sm text-gray-500 dark:text-zinc-400">Cargando productos...</p>
              </div>
            </div>

            <!-- Productos cargados -->
            <div v-else-if="warehouseDetails" class="flex-1 flex flex-col overflow-hidden transition-colors duration-200">
              
              <!-- Dashboard Header -->
              <div class="px-6 py-5 border-b border-gray-200 dark:border-zinc-800">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                      <h2 class="text-2xl font-black text-gray-900 dark:text-white">
                        {{ selectedWarehouse.name }}
                      </h2>
                      <span v-if="selectedWarehouse.is_default"
                        class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800">
                        PRINCIPAL
                      </span>
                      <span
                        class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide"
                        :class="selectedWarehouse.active 
                          ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' 
                          : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'">
                        {{ selectedWarehouse.active ? 'ACTIVA' : 'INACTIVA' }}
                      </span>
                    </div>
                    <div class="flex items-center gap-5 text-sm text-gray-600 dark:text-zinc-400">
                      <span v-if="selectedWarehouse.address" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ selectedWarehouse.address }}
                      </span>
                      <span v-if="selectedWarehouse.phone" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        {{ selectedWarehouse.phone }}
                      </span>
                    </div>
                  </div>

                  <!-- Acciones -->
                  <div class="flex items-center gap-1.5">
                    <button
                      @click="editWarehouse(selectedWarehouse)"
                      class="p-2.5 text-slate-400 dark:text-zinc-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-xl border border-transparent hover:border-amber-100 dark:hover:border-amber-900/30 transition-all duration-200"
                      title="Editar sede">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click="viewInventory(selectedWarehouse)"
                      class="p-2.5 text-slate-400 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl border border-transparent hover:border-blue-100 dark:hover:border-blue-900/30 transition-all duration-200"
                      title="Ver inventario completo">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    <button
                      v-if="!selectedWarehouse.is_default"
                      @click="deleteWarehouse(selectedWarehouse)"
                      class="p-2.5 text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-xl border border-transparent hover:border-rose-100 dark:hover:border-rose-900/30 transition-all duration-200"
                      title="Eliminar sede">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Métricas de la sede -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mt-5">
                  <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl px-4 py-3 border border-gray-200 dark:border-zinc-700/50">
                    <div class="flex items-center gap-2 mb-1">
                      <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                      </svg>
                      <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Productos</p>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ warehouseDetails?.summary?.total_products || 0 }}</p>
                  </div>
                  <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl px-4 py-3 border border-gray-200 dark:border-zinc-700/50">
                    <div class="flex items-center gap-2 mb-1">
                      <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                      </svg>
                      <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Stock Total</p>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ warehouseDetails?.summary?.total_stock || 0 }}</p>
                  </div>
                  <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl px-4 py-3 border border-gray-200 dark:border-zinc-700/50">
                    <div class="flex items-center gap-2 mb-1">
                      <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Valor Inventario</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">${{ formatNumber(warehouseDetails?.summary?.total_value || 0) }}</p>
                  </div>
                  <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl px-4 py-3 border border-gray-200 dark:border-zinc-700/50">
                    <div class="flex items-center gap-2 mb-1">
                      <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                      </svg>
                      <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Stock Bajo</p>
                    </div>
                    <p class="text-2xl font-bold" :class="(warehouseDetails?.summary?.low_stock_count || 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">{{ warehouseDetails?.summary?.low_stock_count || 0 }}</p>
                  </div>
                </div>
              </div>

              <!-- Inventario de productos -->
              <div class="flex-1 overflow-y-auto px-6 py-5 bg-gray-50 dark:bg-zinc-900/50">
                
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
                      class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200">
                  </div>
                </div>

                <!-- Tabla de productos -->
                <div class="bg-white dark:bg-zinc-900 rounded-md overflow-hidden border border-gray-200 dark:border-zinc-800 shadow-sm">
                  <table class="min-w-full">
                    <thead>
                      <tr class="border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
                        <th class="px-5 py-3.5 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Producto</th>
                        <th class="px-4 py-3.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">SKU</th>
                        <th class="px-4 py-3.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Categoría</th>
                        <th class="px-4 py-3.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Stock</th>
                        <th class="px-4 py-3.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Min/Max</th>
                        <th class="px-5 py-3.5 text-right text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Precio</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                      <tr v-for="product in filteredProducts" :key="product.id" 
                          class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
                        <td class="px-5 py-3">
                          <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                              <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover" />
                              <svg v-else class="w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                              </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ product.name }}</p>
                              <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">{{ product.barcode || 'Sin código' }}</p>
                            </div>
                          </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span class="text-xs font-mono text-gray-500 dark:text-zinc-400">{{ product.sku }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span class="text-xs text-gray-500 dark:text-zinc-400">{{ product.category_name || 'Sin categoría' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span class="font-medium text-sm"
                                :class="product.stock === 0 ? 'text-rose-600 dark:text-rose-400' : product.stock <= product.min_stock ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">
                            {{ product.stock }}
                          </span>
                          <span class="text-xs text-gray-500 dark:text-zinc-400 ml-1">{{ product.measurement_unit || product.unit || 'un' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span class="text-xs text-gray-500 dark:text-zinc-400">{{ product.min_stock }}/{{ product.max_stock || '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                          <div class="font-mono text-sm font-medium text-gray-900 dark:text-white">${{ formatNumber(product.sale_price) }}</div>
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
                    <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">No hay productos</p>
                    <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Esta sede no tiene productos registrados</p>
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
      <div v-if="activeTab === 'transfers'" class="flex-1 flex flex-col min-h-0 px-6 lg:px-8 pb-6">
        <StockTransfersView ref="transfersComponent" :embedded="true" />
      </div>

      <!-- Contenido Personal -->
      <div v-if="activeTab === 'staff'" class="flex-1 flex flex-col min-h-0 px-6 lg:px-8 pb-6 space-y-5">
        
        <!-- KPIs Personal -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Personal</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ staffUsers.length }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Asignados</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ staffUsers.filter(u => u.warehouse_id).length }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Sin Asignar</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ staffUsers.filter(u => !u.warehouse_id).length }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Personal por Sede -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Personal por Sede</h3>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
                </svg>
                <input
                  v-model="staffSearchTerm"
                  type="text"
                  placeholder="Buscar empleado..."
                  class="pl-10 pr-4 py-2.5 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent w-64"
                />
              </div>
            </div>
          </div>

          <div v-if="loadingStaff" class="flex items-center justify-center py-16">
            <div class="w-8 h-8 border-2 border-gray-300 dark:border-zinc-600 border-t-blue-500 rounded-full animate-spin"></div>
          </div>

          <div v-else-if="filteredStaff.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-zinc-500">
            <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
            </svg>
            <p class="text-sm font-medium">No se encontraron empleados</p>
          </div>

          <table v-else class="w-full">
            <thead>
              <tr class="border-b border-gray-200 dark:border-zinc-800">
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Empleado</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Rol</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Sede Asignada</th>
                <th class="text-right px-6 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in filteredStaff" :key="user.id" class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
                <td class="px-6 py-3">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white" :style="{ backgroundColor: getAvatarColor(user.name) }">
                      {{ user.name?.charAt(0)?.toUpperCase() || '?' }}
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ user.name }}</p>
                      <p class="text-xs text-gray-500 dark:text-zinc-500">{{ user.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-3">
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800">
                    {{ user.role?.name || 'Sin rol' }}
                  </span>
                </td>
                <td class="px-6 py-3">
                  <span v-if="user.warehouse" class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800">
                    {{ user.warehouse?.name }}
                  </span>
                  <span v-else class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800">
                    Sin asignar
                  </span>
                </td>
                <td class="px-6 py-3 text-right">
                  <button
                    @click="openStaffTransfer(user)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border transition-all duration-200 text-slate-400 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-100 dark:hover:border-blue-900/30 border-transparent"
                    title="Trasladar"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
                    </svg>
                    Trasladar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Historial de Traslados -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Historial de Traslados</h3>
          </div>

          <div v-if="staffTransferHistory.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400 dark:text-zinc-500">
            <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm">No hay traslados registrados</p>
          </div>

          <table v-else class="w-full">
            <thead>
              <tr class="border-b border-gray-200 dark:border-zinc-800">
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Fecha</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Empleado</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Origen</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Destino</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Motivo</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="t in staffTransferHistory" :key="t.id" class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
                <td class="px-6 py-3 text-sm text-gray-700 dark:text-zinc-300">{{ formatDate(t.created_at) }}</td>
                <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ t.user?.name || '-' }}</td>
                <td class="px-6 py-3 text-sm text-gray-600 dark:text-zinc-400">{{ t.from_warehouse?.name || 'Sin sede' }}</td>
                <td class="px-6 py-3 text-sm text-gray-600 dark:text-zinc-400">{{ t.to_warehouse?.name || '-' }}</td>
                <td class="px-6 py-3 text-sm text-gray-500 dark:text-zinc-500">{{ t.reason || '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Contenido Matriz de Distribución -->
      <div v-if="activeTab === 'matrix'" class="flex-1 flex flex-col min-h-0 px-6 lg:px-8 pb-6">
        <StockDistributionMatrix ref="matrixComponent" />
      </div>

  </div>

    <!-- Modal Trasladar Empleado -->
    <Teleport to="body">
      <div v-if="showStaffTransferModal" class="fixed inset-0 z-[9999] flex items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeStaffTransferModal"></div>
        <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/60 border border-gray-200 dark:border-zinc-800 w-full max-w-lg mx-4 animate-fade-in">
          <div class="px-6 py-5 border-b border-gray-200 dark:border-zinc-800">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Trasladar Empleado</h2>
            <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">Mover un empleado a otra sede. Si tiene una caja abierta, se cerrará automáticamente.</p>
          </div>

          <div class="px-6 py-5 space-y-4">
            <!-- Autocomplete Empleado -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Empleado</label>
              <div class="relative">
                <input
                  v-model="staffTransferSearch"
                  type="text"
                  placeholder="Buscar por nombre o email..."
                  class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  @focus="showStaffDropdown = true"
                  @input="showStaffDropdown = true"
                />
                <!-- Dropdown -->
                <div v-if="showStaffDropdown && staffTransferResults.length > 0" class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 shadow-xl max-h-48 overflow-y-auto">
                  <button
                    v-for="u in staffTransferResults"
                    :key="u.id"
                    @mousedown.prevent="selectStaffForTransfer(u)"
                    class="w-full text-left px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition-colors flex items-center gap-3"
                  >
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0" :style="{ backgroundColor: getAvatarColor(u.name) }">
                      {{ u.name?.charAt(0)?.toUpperCase() }}
                    </div>
                    <div class="min-w-0">
                      <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ u.name }}</p>
                      <p class="text-xs text-gray-500 dark:text-zinc-500 truncate">{{ u.role?.name }} · {{ u.warehouse?.name || 'Sin sede' }}</p>
                    </div>
                  </button>
                </div>
              </div>
            </div>

            <!-- Empleado seleccionado -->
            <div v-if="selectedStaffUser" class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white" :style="{ backgroundColor: getAvatarColor(selectedStaffUser.name) }">
                  {{ selectedStaffUser.name?.charAt(0)?.toUpperCase() }}
                </div>
                <div>
                  <p class="text-sm font-bold text-gray-900 dark:text-white">{{ selectedStaffUser.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">{{ selectedStaffUser.role?.name }}</p>
                </div>
              </div>
              <div class="text-sm text-gray-600 dark:text-zinc-400">
                <span class="font-medium">Sede actual:</span>
                <span class="ml-1" :class="selectedStaffUser.warehouse ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'">
                  {{ selectedStaffUser.warehouse?.name || 'Sin asignar' }}
                </span>
              </div>
            </div>

            <!-- Sede Destino -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Sede Destino</label>
              <select
                v-model="staffTransferWarehouseId"
                class="w-full px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
              >
                <option value="">Seleccionar sede...</option>
                <option v-for="w in availableDestinations" :key="w.id" :value="w.id">{{ w.name }}</option>
              </select>
            </div>

            <!-- Motivo -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Motivo (opcional)</label>
              <input
                v-model="staffTransferReason"
                type="text"
                placeholder="Ej: Refuerzo por alta demanda"
                class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

            <!-- Warning -->
            <div v-if="selectedStaffUser" class="flex items-start gap-2.5 p-3 bg-amber-50 dark:bg-amber-950/50 rounded-lg border border-amber-200 dark:border-amber-800/50">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
              </svg>
              <p class="text-xs text-amber-800 dark:text-amber-300">Si el empleado tiene una sesión de caja abierta en su sede actual, se cerrará automáticamente al confirmar el traslado.</p>
            </div>
          </div>

          <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-800 flex items-center justify-end gap-3">
            <button
              @click="closeStaffTransferModal"
              class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200"
            >
              Cancelar
            </button>
            <button
              @click="executeStaffTransfer"
              :disabled="!selectedStaffUser || !staffTransferWarehouseId || transferringStaff"
              class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <svg v-if="transferringStaff" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25"></circle><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-linecap="round"></path></svg>
              <span>{{ transferringStaff ? 'Trasladando...' : 'Confirmar Traslado' }}</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>

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
        <div class="bg-white dark:bg-zinc-900 rounded-2xl max-w-md w-full overflow-hidden border border-gray-200 dark:border-zinc-800 shadow-2xl dark:shadow-black/50 animate-scale-in">
          
          <!-- Header -->
          <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-amber-50 dark:bg-amber-950 border border-amber-100 dark:border-amber-800">
                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Límite de Sedes Alcanzado</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Plan {{ planInfo?.plan_name || 'Actual' }}</p>
              </div>
              <button 
                @click="showLimitModal = false"
                class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Contenido -->
          <div class="px-6 py-5">
            <p class="text-sm text-gray-700 dark:text-zinc-300 leading-relaxed">
              Has alcanzado el límite de <span class="font-semibold text-gray-900 dark:text-white">{{ planInfo?.max_allowed }} sedes</span> para tu plan actual.
            </p>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-3 bg-gray-50 dark:bg-zinc-800 rounded-xl p-3 border border-gray-200 dark:border-zinc-700">
              Actualiza a <span class="font-semibold text-gray-900 dark:text-white">Enterprise</span> para gestionar sedes ilimitadas y desbloquear todo el potencial de tu negocio.
            </p>
          </div>

          <!-- Footer -->
          <div class="bg-gray-50 dark:bg-zinc-800/50 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex justify-end space-x-3">
            <button 
              @click="showLimitModal = false"
              type="button"
              class="px-5 py-2.5 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 text-slate-600 dark:text-zinc-300 text-sm font-bold rounded-xl hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">
              Cerrar
            </button>
            <button 
              @click="goToPlans"
              class="px-5 py-2.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-zinc-900 text-sm font-semibold rounded-md  transition-all duration-300 flex items-center space-x-2">
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
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useRouter } from 'vue-router'
import { warehouseService } from '@/services/warehouseService'
import { apiCall } from '@/services/api'
import WarehouseModal from './warehouses/WarehouseModal.vue'
import StockTransfersView from './StockTransfersView.vue'
import StockDistributionMatrix from './warehouses/StockDistributionMatrix.vue'
import ToastContainer from './ToastContainer.vue'
import { useUIContextStore } from '@/store/uiContextStore'
import { useToast } from '@/composables/useToast.js'

const { showSuccess, showError, showInfo } = useToast()

// Props y Emits para evitar warnings de Vue
defineProps({
  moduleName: String,
  queryParams: Object
})

defineEmits(['navigate', 'changeModule', 'openQuotationInPos', 'openReturnInPos', 'refresh'])

// Store de contexto UI
const uiContextStore = useUIContextStore()

// Estados
const activeTab = ref('warehouses') // 'warehouses' o 'transfers'
const warehouses = ref([])
const selectedWarehouse = ref(null)
const warehouseDetails = ref(null)
const loadingProducts = ref(false)
const searchTerm = ref('')
const productSearchTerm = ref('')
const transfersComponent = ref(null)
const matrixComponent = ref(null)
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
  return warehouses.value.reduce((sum, w) => sum + (w.products_count || 0), 0)
})
const defaultWarehouse = computed(() => warehouses.value.find(w => w.is_default)?.name || '-')

// Métodos
const loadWarehouses = async () => {
  try {
    const data = await warehouseService.getAll()
    
    // El API devuelve { warehouses: [...], plan_info: {...} }
    if (data && data.warehouses && Array.isArray(data.warehouses)) {
      warehouses.value = data.warehouses
      if (data.plan_info) {
        planInfo.value = data.plan_info
      }
    } else if (data && data.data && Array.isArray(data.data)) {
      warehouses.value = data.data
    } else if (Array.isArray(data)) {
      warehouses.value = data
    } else {
      warehouses.value = []
    }
  } catch (error) {
    console.error('Error loading warehouses:', error)
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
    
    // El API devuelve { success, data: { warehouse, summary, products } }
    if (response && response.data) {
      warehouseDetails.value = response.data
    } else {
      warehouseDetails.value = response
    }
  } catch (error) {
    console.error('Error loading warehouse products:', error)
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
    showInfo(`Inventario de ${warehouse.name}: Total de productos: ${totalProducts}, Stock total: ${totalStock} unidades (Vista detallada en desarrollo)`)
  } catch (error) {
    console.error('Error al cargar inventario:', error)
    showError('Error al cargar el inventario')
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
    
    showSuccess('Sede eliminada exitosamente')
  } catch (error) {
    console.error('Error al eliminar sede:', error)
    showError(error?.message || 'Error al eliminar la sede')
  }
}

const handleSaved = () => {
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

// ========== PERSONAL / STAFF TRANSFER ==========
const staffUsers = ref([])
const staffTransferHistory = ref([])
const loadingStaff = ref(false)
const staffSearchTerm = ref('')
const showStaffTransferModal = ref(false)
const staffTransferSearch = ref('')
const showStaffDropdown = ref(false)
const selectedStaffUser = ref(null)
const staffTransferWarehouseId = ref('')
const staffTransferReason = ref('')
const transferringStaff = ref(false)

const filteredStaff = computed(() => {
  if (!staffSearchTerm.value) return staffUsers.value
  const term = staffSearchTerm.value.toLowerCase()
  return staffUsers.value.filter(u =>
    u.name?.toLowerCase().includes(term) ||
    u.email?.toLowerCase().includes(term) ||
    u.warehouse?.name?.toLowerCase().includes(term)
  )
})

const staffTransferResults = computed(() => {
  if (!staffTransferSearch.value) return staffUsers.value.slice(0, 10)
  const term = staffTransferSearch.value.toLowerCase()
  return staffUsers.value.filter(u =>
    u.name?.toLowerCase().includes(term) || u.email?.toLowerCase().includes(term)
  ).slice(0, 10)
})

const availableDestinations = computed(() => {
  if (!selectedStaffUser.value) return warehouses.value.filter(w => w.active)
  return warehouses.value.filter(w => w.active && w.id !== selectedStaffUser.value.warehouse_id)
})

const getAvatarColor = (name) => {
  if (!name) return '#6b7280'
  const colors = ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#06b6d4', '#f43f5e', '#6366f1']
  let hash = 0
  for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash)
  return colors[Math.abs(hash) % colors.length]
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const loadStaffData = async () => {
  loadingStaff.value = true
  try {
    const [usersRes, historyRes] = await Promise.all([
      apiCall('/staff-transfers/users'),
      apiCall('/staff-transfers')
    ])
    staffUsers.value = usersRes?.data || []
    staffTransferHistory.value = historyRes?.data?.data || historyRes?.data || []
  } catch (error) {
    console.error('Error loading staff data:', error)
  } finally {
    loadingStaff.value = false
  }
}

const selectStaffForTransfer = (user) => {
  selectedStaffUser.value = user
  staffTransferSearch.value = user.name
  showStaffDropdown.value = false
  staffTransferWarehouseId.value = ''
}

const openStaffTransfer = (user) => {
  selectedStaffUser.value = user
  staffTransferSearch.value = user.name
  staffTransferWarehouseId.value = ''
  staffTransferReason.value = ''
  showStaffTransferModal.value = true
}

const closeStaffTransferModal = () => {
  showStaffTransferModal.value = false
  selectedStaffUser.value = null
  staffTransferSearch.value = ''
  staffTransferWarehouseId.value = ''
  staffTransferReason.value = ''
  showStaffDropdown.value = false
}

const executeStaffTransfer = async () => {
  if (!selectedStaffUser.value || !staffTransferWarehouseId.value) return
  transferringStaff.value = true
  try {
    const result = await apiCall('/staff-transfers', {
      method: 'POST',
      body: JSON.stringify({
        user_id: selectedStaffUser.value.id,
        to_warehouse_id: Number(staffTransferWarehouseId.value),
        reason: staffTransferReason.value || null,
      })
    })
    if (result?.success) {
      showSuccess(result.message || 'Traslado exitoso')
      closeStaffTransferModal()
      await loadStaffData()
    } else {
      showError(result?.message || 'Error al trasladar')
    }
  } catch (error) {
    showError(error?.message || 'Error al ejecutar el traslado')
  } finally {
    transferringStaff.value = false
  }
}

// Cargar datos de personal cuando se activa la pestaña
watch(activeTab, (val) => {
  if (val === 'staff' && staffUsers.value.length === 0) {
    loadStaffData()
  }
})

// Handler para tecla ESC - deseleccionar sede
const handleKeyDown = (event) => {
  if (event.key === 'Escape' && selectedWarehouse.value && !showModal.value && !showLimitModal.value) {
    selectedWarehouse.value = null
    warehouseDetails.value = null
  }
}

// ========== CONTEXTO DE IA - CONCIENCIA DE PANTALLA ==========
const instrucciones = {
  sedes: 'Pestaña de Sedes: Muestra todas las tiendas y bodegas del negocio con su estado (Activa/Inactiva), dirección, productos y si es sede principal.',
  traslados: 'Pestaña de Traslados: Permite mover inventario entre sedes. Se selecciona sede origen, destino y los productos con cantidades a trasladar.',
  kpis: 'Los KPIs muestran: Sedes Activas (número de sedes operativas), Stock Global (productos totales en todas las sedes), Sede Principal (cuál es la sede por defecto), Uso del Plan (cuántas sedes tiene vs límite del plan).',
  acciones: 'Acciones disponibles: Crear nueva sede, editar sede existente, ver inventario de sede específica, eliminar sede, crear traslado entre sedes, ver historial de traslados.'
}

// Watcher para actualizar contexto de IA cuando cambian datos
watch(
  [warehouses, activeTab, selectedWarehouse, warehouseDetails, planInfo],
  () => {
    // Datos base de sedes
    const sedesData = {
      totalSedes: warehouses.value.length,
      sedesActivas: warehouses.value.filter(w => w.active !== false).length,
      stockGlobal: warehouses.value.reduce((sum, w) => sum + (w.products_count || 0), 0),
      sedePrincipal: warehouses.value.find(w => w.is_default)?.name || 'No asignada',
      pestanaActiva: activeTab.value === 'warehouses' ? 'Sedes' : 'Traslados',
      planInfo: planInfo.value ? {
        sedesUsadas: planInfo.value.used || 0,
        sedesPermitidas: planInfo.value.limit || 1,
        puedeCrearMas: planInfo.value.can_create !== false
      } : null,
      listaSedes: warehouses.value.slice(0, 10).map(w => ({
        nombre: w.name,
        estado: w.active !== false ? 'Activa' : 'Inactiva',
        esPrincipal: w.is_default || false,
        direccion: w.address || 'Sin dirección',
        productos: w.products_count || 0
      })),
      instrucciones
    }
    
    // Si hay una sede seleccionada, agregar sus detalles
    if (selectedWarehouse.value && warehouseDetails.value) {
      sedesData.sedeSeleccionada = {
        nombre: selectedWarehouse.value.name,
        direccion: selectedWarehouse.value.address || 'Sin dirección',
        esPrincipal: selectedWarehouse.value.is_default || false,
        resumenInventario: warehouseDetails.value.summary || null,
        productosEnSede: (warehouseDetails.value.products || []).slice(0, 20).map(p => ({
          nombre: p.name,
          sku: p.sku,
          stock: p.stock || p.quantity || 0,
          precio: p.price || 0
        }))
      }
    }
    
    // Resumen rápido para respuestas comunes
    sedesData.resumenRapido = {
      cuantasSedes: `Hay ${sedesData.totalSedes} sede(s) en total, ${sedesData.sedesActivas} activa(s).`,
      stockTotal: `El stock global es de ${sedesData.stockGlobal} productos en todas las sedes.`,
      sedePrincipal: `La sede principal es "${sedesData.sedePrincipal}".`,
      comoCrearSede: 'Para crear una nueva sede: haz clic en el botón "Nueva Sede" en la esquina superior derecha.',
      comoHacerTraslado: 'Para hacer un traslado: 1) Ve a la pestaña "Traslados", 2) Haz clic en "Nuevo Traslado", 3) Selecciona sede origen y destino, 4) Agrega los productos y cantidades, 5) Confirma el traslado.'
    }

    // Actualizar el contexto de pantalla para la IA
    uiContextStore.setScreenData(sedesData)
    
    // Registrar acciones disponibles para la IA
    uiContextStore.registerAction('cambiarPestanaSedes', ({ tab }) => {
      if (tab === 'transfers' || tab === 'traslados') {
        activeTab.value = 'transfers'
        return { success: true, message: 'Cambiando a la pestaña de Traslados' }
      } else if (tab === 'warehouses' || tab === 'sedes') {
        activeTab.value = 'warehouses'
        return { success: true, message: 'Cambiando a la pestaña de Sedes' }
      }
      return { success: false, message: 'Pestaña no reconocida' }
    })
    
    uiContextStore.registerAction('crearNuevaSede', () => {
      openCreateModal()
      return { success: true, message: 'Abriendo el modal para crear una nueva sede' }
    })
    
    uiContextStore.registerAction('crearNuevoTraslado', () => {
      activeTab.value = 'transfers'
      setTimeout(() => openTransferModal(), 100)
      return { success: true, message: 'Abriendo el modal para crear un nuevo traslado' }
    })
  },
  { immediate: true, deep: true }
)

// Lifecycle
onMounted(() => {
  loadWarehouses()
  document.addEventListener('keydown', handleKeyDown)
})

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleKeyDown)
})
</script>
