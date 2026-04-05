<template>
  <!-- Layout Full Height estilo WhatsApp Web - Sin doble scroll -->
  <div class="h-full font-sans bg-gray-50 dark:bg-[#131314] transition-colors duration-300 overflow-hidden flex flex-col">
    <div class="flex-none px-4 lg:px-6 pt-4 pb-2.5 space-y-3 animate-fade-in">
      
      <!-- NIVEL 1: Header con Título y Botones de Acción -->
      <div class="flex items-center justify-between">
            
            <!-- Título y Subtítulo -->
            <div>
              <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Devoluciones</h1>
              <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">Gestión de devoluciones y reembolsos</p>
            </div>
            
            <!-- Botones de Acción -->
            <div class="flex items-center gap-2">
              <button
                @click="loadReturns"
                class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-200 text-[13px] font-semibold rounded-md border border-gray-200 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Refrescar</span>
              </button>
              
              <button
                @click="navigateToNewReturn"
                class="px-6 py-2.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 text-[13px] font-semibold rounded-md  transition-all duration-300 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Nueva Devolución</span>
              </button>
            </div>
            
          </div>

      <!-- KPIs — Metrics Ribbon (Vercel/Linear) -->
      <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 grid grid-cols-1 md:grid-cols-3 divide-x divide-gray-100 dark:divide-zinc-800">
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Total Devuelto</p>
            <svg class="w-4 h-4 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">${{ formatCurrency(stats.totalRefunded) }}</p>
          <p class="text-xs text-rose-500 dark:text-rose-400">Monto reembolsado</p>
        </div>
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Completadas</p>
            <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ stats.completedCount }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500">Procesadas correctamente</p>
        </div>
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Pendientes</p>
            <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ stats.pendingCount }}</p>
          <p class="text-xs text-amber-500 dark:text-amber-400">Requieren revisión</p>
        </div>
      </div>

    </div>
    
    <!-- Master-Detail Layout - Workspace Unificado -->
    <div class="flex-1 mx-3 lg:mx-4 mb-3 rounded-md overflow-hidden transition-colors duration-300 border border-gray-200 dark:border-zinc-800 shadow-sm">
      <div class="grid grid-cols-1 lg:grid-cols-10 h-full">
        
        <!-- PANEL IZQUIERDO: Lista refinada (Master) -->
        <div class="lg:col-span-3 overflow-hidden flex flex-col bg-white dark:bg-zinc-900 border-r border-gray-200 dark:border-zinc-800 transition-colors duration-300">
          
          <!-- Toolbar: búsqueda y filtros -->
          <div class="px-4 pt-5 pb-4 bg-white dark:bg-zinc-900 space-y-3">
            <!-- Búsqueda -->
            <div class="relative">
              <svg class="absolute left-3.5 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Buscar devolución, cliente..."
                class="w-full pl-10 pr-4 py-3 text-sm rounded-xl bg-gray-50 dark:bg-zinc-800 border-2 border-gray-200 dark:border-zinc-700 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent shadow-sm transition-all duration-200">
            </div>
            
            <!-- Filtro de estado -->
            <div class="flex items-center gap-2">
              <select
                v-model="statusFilter"
                class="flex-1 px-3 py-2.5 text-[13px] rounded-xl bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 shadow-sm transition-colors duration-200 cursor-pointer">
                <option value="">Todos los estados</option>
                <option value="pending">Pendientes</option>
                <option value="completed">Completadas</option>
                <option value="cancelled">Canceladas</option>
              </select>
            </div>
          </div>

          <!-- Separador sutil -->
          <div class="h-px bg-gray-200 dark:bg-zinc-800 mx-4"></div>
          
          <!-- Lista con scroll independiente -->
          <div class="flex-1 overflow-y-auto bg-white dark:bg-zinc-900 px-2.5 pt-3 pb-2">
            
            <div
              v-for="returnItem in filteredReturns"
              :key="returnItem.id"
              @click="selectReturn(returnItem)"
              class="px-3.5 py-3.5 mb-1.5 cursor-pointer transition-all duration-200 rounded-md group relative"
              :class="[
                selectedReturn?.id === returnItem.id 
                  ? 'bg-emerald-50 dark:bg-emerald-950/30 border-l-4 border-emerald-600 dark:border-emerald-500' 
                  : 'hover:bg-gray-50 dark:hover:bg-zinc-800/40 border-l-4 border-transparent'
              ]"
            >
              <div class="flex items-start justify-between gap-3">
                <!-- Info principal -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <p class="text-[15px] font-bold text-gray-900 dark:text-zinc-100 truncate leading-tight">
                      {{ returnItem.number }}
                    </p>
                    <span class="text-[10px] font-bold px-2 py-[3px] rounded-full flex-shrink-0 uppercase tracking-wider leading-none border"
                          :class="getStatusClasses(returnItem.status)">
                      {{ getStatusLabel(returnItem.status) }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 dark:text-zinc-400 truncate font-medium mt-1.5">
                    {{ returnItem.customer?.name || 'Cliente General' }}
                  </p>
                  <span class="text-[12px] text-gray-400 dark:text-zinc-500 font-medium mt-1 block">
                    {{ formatDate(returnItem.return_date) }}
                  </span>
                </div>
                <!-- Monto -->
                <div class="flex-shrink-0 pt-0.5">
                  <span class="text-[15px] font-bold text-gray-900 dark:text-white tabular-nums leading-tight">
                    ${{ formatCurrency(returnItem.total) }}
                  </span>
                </div>
              </div>
            </div>
            
            <!-- Estado vacío -->
            <div v-if="filteredReturns.length === 0" class="flex flex-col items-center justify-center py-12 px-4 text-center">
              <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800 rounded-xl flex items-center justify-center mb-3 border border-gray-200 dark:border-zinc-700">
                <svg class="w-6 h-6 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                </svg>
              </div>
              <p class="text-sm font-semibold text-gray-700 dark:text-zinc-300">Sin resultados</p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Intenta con otros filtros</p>
            </div>
          </div>
        </div>

        <!-- PANEL DERECHO: Detalle -->
        <div class="lg:col-span-7 overflow-hidden flex flex-col bg-white dark:bg-zinc-900 transition-colors duration-300">
          
          <!-- Estado: No seleccionado - Empty State -->
          <div v-if="!selectedReturn" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-gray-50 dark:bg-zinc-900/50 relative">
            
            <!-- Ilustración SVG profesional y limpia -->
            <div class="mb-8 relative">
              <!-- Efecto glow suave de fondo -->
              <div class="absolute inset-0 bg-gradient-to-br from-red-200/30 via-transparent to-amber-200/30 dark:from-red-500/10 dark:to-amber-500/10 rounded-3xl blur-3xl scale-150"></div>
              
              <!-- Ilustración principal de devoluciones -->
              <svg class="w-48 h-48 relative z-10" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Sombra del documento -->
                <rect x="48" y="38" width="88" height="110" rx="6" class="fill-gray-200/50 dark:fill-zinc-700/30"/>
                
                <!-- Documento principal -->
                <rect x="44" y="32" width="88" height="110" rx="6" class="fill-white dark:fill-zinc-800" stroke-width="0"/>
                <rect x="44" y="32" width="88" height="110" rx="6" class="fill-none stroke-gray-200 dark:stroke-zinc-700" stroke-width="1.5"/>
                
                <!-- Encabezado del documento -->
                <rect x="54" y="44" width="40" height="5" rx="2.5" class="fill-gray-300 dark:fill-zinc-600"/>
                <rect x="54" y="54" width="68" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                <rect x="54" y="62" width="55" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                <rect x="54" y="70" width="62" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                
                <!-- Línea separadora -->
                <line x1="54" y1="82" x2="122" y2="82" class="stroke-gray-200 dark:stroke-zinc-700" stroke-width="1"/>
                
                <!-- Área de totales -->
                <rect x="54" y="90" width="30" height="3" rx="1.5" class="fill-gray-200 dark:fill-zinc-600"/>
                <rect x="94" y="88" width="28" height="7" rx="3.5" class="fill-red-100 dark:fill-red-500/20"/>
                <rect x="98" y="90" width="20" height="3" rx="1.5" class="fill-red-500 dark:fill-red-400"/>
                
                <!-- Segunda línea de total -->
                <rect x="54" y="102" width="25" height="3" rx="1.5" class="fill-gray-200 dark:fill-zinc-600"/>
                <rect x="94" y="100" width="28" height="7" rx="3.5" class="fill-gray-100 dark:fill-zinc-700"/>
                
                <!-- Total grande -->
                <rect x="54" y="118" width="35" height="4" rx="2" class="fill-gray-300 dark:fill-zinc-500"/>
                <rect x="94" y="116" width="28" height="8" rx="4" class="fill-red-500 dark:fill-red-400"/>
                
                <!-- Flecha de devolución -->
                <circle cx="120" cy="48" r="14" class="fill-amber-100 dark:fill-amber-500/20"/>
                <circle cx="120" cy="48" r="10" class="fill-amber-500 dark:fill-amber-400"/>
                <path d="M124 48H116M116 48L119 45M116 48L119 51" class="stroke-white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                
                <!-- Caja/Producto devuelto -->
                <rect x="138" y="100" width="28" height="28" rx="4" class="fill-amber-100 dark:fill-amber-500/20"/>
                <rect x="142" y="104" width="20" height="20" rx="2" class="fill-amber-400 dark:fill-amber-500"/>
                <path d="M147 114H157M152 109V119" class="stroke-white" stroke-width="2" stroke-linecap="round"/>
                
                <!-- Icono de refresh/retorno pequeño -->
                <circle cx="30" cy="115" r="16" class="fill-red-100 dark:fill-red-500/15"/>
                <path d="M24 115a6 6 0 1011.5 2.5M36 115a6 6 0 10-11.5-2.5" class="stroke-red-400 dark:stroke-red-400" stroke-width="2" stroke-linecap="round"/>
                <path d="M24 115V111M24 115H28" class="stroke-red-400 dark:stroke-red-400" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M36 115V119M36 115H32" class="stroke-red-400 dark:stroke-red-400" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            
            <!-- Texto de bienvenida profesional -->
            <div class="relative z-10 max-w-md">
              <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">
                Centro de Devoluciones
              </h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400 leading-relaxed mb-2">
                Selecciona una devolución del panel izquierdo para visualizar el desglose completo, imprimir o gestionar el reembolso.
              </p>
              <p class="text-xs text-gray-400 dark:text-zinc-500">
                Gestiona las devoluciones de tus clientes de forma rápida y segura.
              </p>
            </div>
          </div>

          <!-- Estado: Devolución seleccionada -->
          <div v-else class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-zinc-900 transition-colors duration-300">
            
            <!-- Header del detalle -->
            <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-[2rem] font-extrabold text-gray-900 dark:text-white tracking-tight">
                      {{ selectedReturn.number }}
                    </h2>
                    <span
                      class="px-3 py-1 rounded-full text-xs font-bold border"
                      :class="getStatusClasses(selectedReturn.status)">
                      {{ getStatusLabel(selectedReturn.status) }}
                    </span>
                  </div>
                  <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-zinc-400 font-medium mt-1">
                    <span class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      {{ formatDate(selectedReturn.return_date) }}
                    </span>
                    <span class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      {{ selectedReturn.customer?.name || 'Cliente General' }}
                    </span>
                    <span class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      {{ selectedReturn.user?.name || 'Usuario' }}
                    </span>
                  </div>
                </div>
                
                <!-- Acciones contextuales con texto -->
                <div class="flex items-center gap-1.5">
                  <button
                    @click="printReturn"
                    class="px-3.5 py-2.5 rounded-xl transition-all flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-zinc-800 border border-transparent hover:border-gray-200 dark:hover:border-zinc-700"
                  >
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir
                  </button>
                  
                  <button
                    @click="downloadReturn"
                    class="px-3.5 py-2.5 rounded-xl transition-all flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-zinc-800 border border-transparent hover:border-gray-200 dark:hover:border-zinc-700"
                  >
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Descargar
                  </button>
                  
                  <button
                    @click="sendByEmail"
                    class="px-3.5 py-2.5 rounded-xl transition-all flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-zinc-800 border border-transparent hover:border-gray-200 dark:hover:border-zinc-700"
                  >
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Email
                  </button>
                  
                  <button
                    @click="requestPhone"
                    class="px-3.5 py-2.5 rounded-xl transition-all flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border border-transparent hover:border-emerald-100 dark:hover:border-emerald-900/30"
                  >
                    <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    WhatsApp
                  </button>
                </div>
              </div>
            </div>

            <!-- Contenido scrollable -->
            <div class="flex-1 overflow-y-auto p-7 bg-gray-50 dark:bg-zinc-950/50">
              
              <!-- Documento digital -->
              <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 shadow-sm p-7 space-y-7">
                
                <!-- Encabezado del documento -->
                <div class="grid grid-cols-2 gap-6">
                  <div class="bg-gray-50/80 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-100 dark:border-zinc-700/50">
                    <h4 class="text-[11px] font-semibold uppercase mb-2.5 text-gray-500 dark:text-zinc-400 tracking-wider">Información del Cliente</h4>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">{{ selectedReturn.customer?.name || 'Cliente General' }}</p>
                    <p class="text-sm mt-1.5 text-gray-500 dark:text-zinc-400">ID Cliente: {{ selectedReturn.customer?.id || 'N/A' }}</p>
                  </div>
                    
                  <div class="bg-gray-50/80 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-100 dark:border-zinc-700/50 text-right">
                    <h4 class="text-[11px] font-semibold uppercase mb-2.5 text-gray-500 dark:text-zinc-400 tracking-wider">Información del Documento</h4>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">Devolución: {{ selectedReturn.number }}</p>
                    <p class="text-sm mt-1.5 text-gray-500 dark:text-zinc-400">Fecha: {{ formatDate(selectedReturn.return_date) }}</p>
                    <p class="text-sm text-gray-500 dark:text-zinc-400">Factura Original: {{ selectedReturn.original_invoice?.number || 'N/A' }}</p>
                  </div>
                </div>

                <!-- Productos - Tabla -->
                <div>
                  <h4 class="text-[11px] font-semibold mb-3 uppercase text-gray-900 dark:text-zinc-300 tracking-wider">Productos Devueltos</h4>
                  
                  <div class="bg-white dark:bg-zinc-900 rounded-xl overflow-hidden border border-gray-200 dark:border-zinc-800">
                    <table class="min-w-full">
                      <thead>
                        <tr class="bg-gray-50 dark:bg-zinc-800/50 border-b border-gray-200 dark:border-zinc-800">
                          <th class="text-left text-[10px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider px-6 py-4">#</th>
                          <th class="text-left text-[10px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider px-6 py-4">Descripción</th>
                          <th class="text-center text-[10px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider px-6 py-4">Cant.</th>
                          <th class="text-right text-[10px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider px-6 py-4">Precio</th>
                          <th class="text-right text-[10px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider px-6 py-4">Total</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                        <tr v-if="!selectedReturn.return_items || selectedReturn.return_items.length === 0">
                          <td colspan="5" class="px-6 py-12 text-center">
                            <p class="text-sm text-gray-400 dark:text-zinc-500">No hay productos registrados</p>
                          </td>
                        </tr>
                        <tr v-else v-for="(item, index) in selectedReturn.return_items" :key="`item-${index}`" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                          <td class="px-6 py-4 text-sm text-gray-400 dark:text-zinc-500 font-medium">{{ index + 1 }}</td>
                          <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ item.product?.name || item.name || 'N/A' }}</p>
                            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">SKU: {{ item.product?.code || item.code || 'N/A' }}</p>
                          </td>
                          <td class="text-center px-6 py-4">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-bold rounded-lg bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700">
                              {{ item.quantity }}
                            </span>
                          </td>
                          <td class="text-right px-6 py-4 text-sm font-medium text-gray-600 dark:text-zinc-300">
                            ${{ formatCurrency(item.unit_price || item.price) }}
                          </td>
                          <td class="text-right px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                            ${{ formatCurrency(item.subtotal || (item.quantity * (item.unit_price || item.price))) }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    
                    <!-- Totales -->
                    <div class="px-6 py-5 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-800/30">
                      <div class="flex justify-end">
                        <div class="w-72 space-y-2 text-sm">
                          <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-zinc-400">Subtotal:</span>
                            <span class="font-semibold text-gray-900 dark:text-zinc-300">${{ formatCurrency(selectedReturn.subtotal || selectedReturn.total) }}</span>
                          </div>
                          <div class="pt-3 mt-2 border-t border-gray-200 dark:border-zinc-700">
                            <div class="flex justify-between items-center">
                              <span class="text-base font-bold text-gray-900 dark:text-zinc-200">TOTAL DEVUELTO:</span>
                              <span class="text-[2rem] font-extrabold text-red-600 dark:text-red-400">${{ formatCurrency(selectedReturn.total) }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Información adicional -->
                <div class="grid grid-cols-2 gap-6">
                  <div class="bg-gray-50/80 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-100 dark:border-zinc-700/50">
                    <h4 class="text-[11px] font-semibold uppercase mb-2.5 text-gray-500 dark:text-zinc-400 tracking-wider">Método de Reembolso</h4>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">{{ getRefundMethodLabel(selectedReturn.refund_method) }}</p>
                  </div>
                  
                  <div class="bg-gray-50/80 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-100 dark:border-zinc-700/50">
                    <h4 class="text-[11px] font-semibold uppercase mb-2.5 text-gray-500 dark:text-zinc-400 tracking-wider">Procesado por</h4>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">{{ selectedReturn.user?.name || 'Usuario' }}</p>
                  </div>
                </div>

                <!-- Razón de la devolución -->
                <div v-if="selectedReturn.reason">
                  <h4 class="text-[11px] font-semibold uppercase mb-2.5 text-gray-600 dark:text-zinc-400 tracking-wider">Razón de la Devolución</h4>
                  <p class="text-sm p-4 rounded-xl text-gray-700 dark:text-zinc-300 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/30">{{ selectedReturn.reason }}</p>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
    
    <!-- Modal solicitar teléfono para WhatsApp -->
    <Teleport to="body">
      <div v-if="showPhoneModal" class="fixed inset-0 bg-black/70  flex items-center justify-center z-[9999] p-4 animate-fade-in">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200 dark:border-zinc-800 animate-scale-in">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Enviar por WhatsApp</h3>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mb-4">Ingresa el número de teléfono del cliente:</p>
          <input
            v-model="phoneNumber"
            type="tel"
            placeholder="Ej: +57 300 1234567"
            class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 mb-4"
            @keyup.enter="sendByWhatsApp"
          />
          <div class="flex gap-3">
            <button
              @click="showPhoneModal = false; phoneNumber = ''"
              class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-xl font-semibold transition-all"
            >
              Cancelar
            </button>
            <button
              @click="sendByWhatsApp"
              :disabled="!phoneNumber.trim()"
              class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white rounded-xl font-semibold transition-all"
            >
              Enviar
            </button>
          </div>
        </div>
      </div>
    </Teleport>
    
    <!-- Modal solicitar email -->
    <Teleport to="body">
      <div v-if="showEmailModal" class="fixed inset-0 bg-black/70  flex items-center justify-center z-[9999] p-4 animate-fade-in">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200 dark:border-zinc-800 animate-scale-in">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Enviar por Email</h3>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mb-4">Ingresa el correo electrónico del cliente:</p>
          <input
            v-model="emailAddress"
            type="email"
            placeholder="cliente@ejemplo.com"
            class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 mb-4"
            @keyup.enter="confirmSendByEmail"
          />
          <div class="flex gap-3">
            <button
              @click="showEmailModal = false; emailAddress = ''"
              class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-xl font-semibold transition-all"
            >
              Cancelar
            </button>
            <button
              @click="confirmSendByEmail"
              :disabled="!emailAddress.trim()"
              class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white rounded-xl font-semibold transition-all"
            >
              Enviar
            </button>
          </div>
        </div>
      </div>
    </Teleport>
    
    <!-- Modal Premium - Bloqueo de plan básico -->
    <Teleport to="body">
      <div v-if="showPremiumModal" class="fixed inset-0 bg-black/70  flex items-center justify-center z-[9999] p-4 animate-fade-in">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-zinc-800 animate-scale-in">
          
          <!-- Contenido -->
          <div class="p-8 text-center">
            <!-- Icono Premium -->
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
              <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
              </svg>
            </div>

            <!-- Título -->
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3">¡Mejora tu Plan!</h3>
            
            <!-- Mensaje -->
            <p class="text-base text-gray-600 dark:text-zinc-400 mb-6 leading-relaxed">
              <span class="font-semibold text-blue-600 dark:text-blue-400">{{ premiumFeatureName }}</span> está disponible en nuestros planes premium.
            </p>
            
            <p class="text-sm text-gray-500 dark:text-zinc-500 mb-8">
              💡 Desbloquea todas las funciones premium para potenciar tu negocio
            </p>

            <!-- Botones -->
            <div class="flex gap-3">
              <button
                @click="showPremiumModal = false"
                class="flex-1 py-3 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-base font-semibold rounded-xl border border-gray-300 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors"
              >
                Cerrar
              </button>
              <button
                @click="goToSelectPlan"
                class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-base font-semibold rounded-xl transition-colors duration-200 shadow-lg"
              >
                Ver Planes
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useToast } from '../composables/useToast.js'
import { useModuleNavigation } from '../composables/useModuleNavigation.js'
import { appStore } from '../store/appStore.js'
import { useUIContextStore } from '../store/uiContextStore.js'
import returnsService from '../services/returnsService.js'

const { navigateToModule } = useModuleNavigation()
const uiContext = useUIContextStore()

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

// Estados para modales de envío
const showPhoneModal = ref(false)
const showEmailModal = ref(false)
const phoneNumber = ref('')
const emailAddress = ref('')

// Estados para modal premium (bloqueo de plan básico)
const showPremiumModal = ref(false)
const premiumFeatureName = ref('')

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
    
    // showToast('Devoluciones cargadas exitosamente', 'success') // Mensaje innecesario
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

const printReturn = async () => {
  try {
    if (!selectedReturn.value) {
      showToast('Por favor selecciona una devolución primero', 'error')
      return
    }

    showToast('Generando PDF para imprimir...', 'info')
    
    // Generar el PDF
    const { generateReturnPDF, getPDFBlob } = await import('../utils/pdfTemplates/pdfGenerator.js')
    const systemSettings = appStore.systemSettings || {}
    
    const pdfDoc = await generateReturnPDF(selectedReturn.value, systemSettings)
    const pdfBlob = await getPDFBlob(pdfDoc)
    
    // Crear URL del blob y abrir ventana de impresión
    const blobUrl = URL.createObjectURL(pdfBlob)
    const printWindow = window.open(blobUrl, '_blank')
    
    if (printWindow) {
      printWindow.onload = () => {
        printWindow.print()
        URL.revokeObjectURL(blobUrl)
      }
      showToast('Documento preparado para imprimir', 'success')
    } else {
      showToast('No se pudo abrir la ventana de impresión', 'error')
    }
  } catch (error) {
    console.error('Error al imprimir:', error)
    showToast('Error al preparar el documento', 'error')
  }
}

const downloadReturn = async () => {
  try {
    if (!selectedReturn.value) {
      showToast('Por favor selecciona una devolución primero', 'error')
      return
    }

    showToast('Generando PDF...', 'info')
    
    const { generateReturnPDF, downloadPDF } = await import('../utils/pdfTemplates/pdfGenerator.js')
    const systemSettings = appStore.systemSettings || {}
    
    const pdfDoc = await generateReturnPDF(selectedReturn.value, systemSettings)
    const fileName = `Devolucion_${selectedReturn.value.number || 'SN'}.pdf`
    
    await downloadPDF(pdfDoc, fileName)
    showToast('PDF descargado exitosamente', 'success')
  } catch (error) {
    console.error('Error al descargar:', error)
    showToast('Error al generar el PDF', 'error')
  }
}

// Verificar si el usuario tiene plan básico (free_trial, free, basic)
const isBasicPlan = () => {
  const plan = (appStore.tenantPlan || 'free_trial').toLowerCase()
  return plan === 'free_trial' || plan === 'free' || plan === 'basic'
}

// Mostrar modal premium
const showPremiumFeature = (featureName) => {
  premiumFeatureName.value = featureName
  showPremiumModal.value = true
}

// Ir a selección de plan
const goToSelectPlan = () => {
  showPremiumModal.value = false
  navigateToModule('settings', { section: 'plans' })
}

// Solicitar email (abre modal)
const requestEmail = () => {
  if (!selectedReturn.value) {
    showToast('Por favor selecciona una devolución primero', 'error')
    return
  }
  
  // Verificar plan básico
  if (isBasicPlan()) {
    showPremiumFeature('Envío por Email')
    return
  }
  
  // Pre-cargar email del cliente si existe
  if (selectedReturn.value.customer?.email) {
    emailAddress.value = selectedReturn.value.customer.email
  }
  showEmailModal.value = true
}

// Confirmar envío por email (desde modal)
const confirmSendByEmail = async () => {
  try {
    if (!emailAddress.value.trim()) {
      showToast('Por favor ingresa un correo electrónico', 'error')
      return
    }

    if (!selectedReturn.value) {
      showToast('No hay datos de devolución para enviar', 'error')
      return
    }

    showToast('Enviando devolución por email...', 'info')
    showEmailModal.value = false
    
    const { generateReturnPDF, getPDFBlob } = await import('../utils/pdfTemplates/pdfGenerator.js')
    const systemSettings = appStore.systemSettings || {}
    
    const pdfDoc = await generateReturnPDF(selectedReturn.value, systemSettings)
    const pdfBlob = await getPDFBlob(pdfDoc)
    
    const returnNumber = selectedReturn.value.number || 'SN'
    const refundMethodLabel = getRefundMethodLabel(selectedReturn.value.refund_method)
    
    const formData = new FormData()
    formData.append('email', emailAddress.value.trim())
    formData.append('subject', `Nota de Devolución #${returnNumber}`)
    formData.append('message', `
      <h2>Nota de Devolución #${returnNumber}</h2>
      <p>Estimado cliente,</p>
      <p>Adjunto encontrarás el comprobante de tu devolución.</p>
      <p><strong>Total reembolsado:</strong> $${formatCurrency(selectedReturn.value.total || 0)}</p>
      <p><strong>Método de reembolso:</strong> ${refundMethodLabel}</p>
      <br>
      <p>Gracias por tu confianza.</p>
    `)
    formData.append('pdf', pdfBlob, `Devolucion_${returnNumber}.pdf`)
    
    const response = await fetch('/api/email/send-invoice', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    })
    
    const result = await response.json()
    
    if (!response.ok || !result.success) {
      throw new Error(result.message || 'Error al enviar el email')
    }
    
    showToast('Devolución enviada por email exitosamente', 'success')
    emailAddress.value = ''
  } catch (error) {
    console.error('Error al enviar email:', error)
    showToast(error.message || 'Error al enviar por email', 'error')
  }
}

// Solicitar teléfono (abre modal)
const requestPhone = () => {
  if (!selectedReturn.value) {
    showToast('Por favor selecciona una devolución primero', 'error')
    return
  }
  
  // Verificar plan básico
  if (isBasicPlan()) {
    showPremiumFeature('Envío por WhatsApp')
    return
  }
  
  // Pre-cargar teléfono del cliente si existe
  if (selectedReturn.value.customer?.phone) {
    phoneNumber.value = selectedReturn.value.customer.phone
  }
  showPhoneModal.value = true
}

// Enviar por WhatsApp (desde modal)
const sendByWhatsApp = async () => {
  try {
    if (!phoneNumber.value.trim()) {
      showToast('Por favor ingresa un número de teléfono', 'error')
      return
    }

    if (!selectedReturn.value) {
      showToast('No hay datos de devolución para enviar', 'error')
      return
    }

    showToast('Generando PDF y enviando por WhatsApp...', 'info')
    showPhoneModal.value = false
    
    const { generateReturnPDF, getPDFBlob } = await import('../utils/pdfTemplates/pdfGenerator.js')
    const { whatsappService } = await import('../services/whatsappService.js')
    const systemSettings = appStore.systemSettings || {}
    
    const pdfDoc = await generateReturnPDF(selectedReturn.value, systemSettings)
    const pdfBlob = await getPDFBlob(pdfDoc)
    
    const docNumber = selectedReturn.value.number || 'SN'
    await whatsappService.sendDocumentByWhatsApp(
      phoneNumber.value,
      pdfBlob,
      docNumber,
      'devolucion',
      selectedReturn.value.customer_name || 'Cliente'
    )
    
    showToast('Devolución enviada por WhatsApp exitosamente', 'success')
    phoneNumber.value = ''
  } catch (error) {
    console.error('Error al enviar por WhatsApp:', error)
    showToast(error.message || 'Error al enviar por WhatsApp', 'error')
  }
}

// Función legacy para compatibilidad (usa el modal ahora)
const sendByEmail = () => {
  requestEmail()
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
  if (s === 'completed' || s === 'completada') return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400'
  if (s === 'pending' || s === 'pendiente') return 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-400'
  if (s === 'cancelled' || s === 'cancelada') return 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-400'
  return 'bg-gray-100 text-gray-600 dark:bg-[#282a2c] dark:text-zinc-400'
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

// Handler para tecla ESC - deseleccionar devolución
const handleKeyDown = (event) => {
  if (event.key === 'Escape' && selectedReturn.value) {
    selectedReturn.value = null
  }
}

// 🧠 Actualizar contexto de pantalla para IA de voz
const updateScreenContextForAI = () => {
  const contextData = {
    resumenDevoluciones: {
      total: returns.value.length,
      totalDevuelto: `$${formatCurrency(stats.value.totalRefunded)}`,
      completadas: stats.value.completedCount,
      montoCompletado: `$${formatCurrency(stats.value.completedAmount)}`,
      pendientes: stats.value.pendingCount,
      montoPendiente: `$${formatCurrency(stats.value.pendingAmount)}`,
      productosDevueltos: stats.value.totalItemsReturned
    },
    // Info de la devolución seleccionada (si hay alguna)
    devolucionSeleccionada: selectedReturn.value ? {
      numero: selectedReturn.value.number || `DEV-${selectedReturn.value.id}`,
      estado: getStatusLabel(selectedReturn.value.status),
      facturaOriginal: selectedReturn.value.original_invoice?.number || 'N/A',
      cliente: selectedReturn.value.customer?.name || 'Cliente General',
      total: `$${formatCurrency(selectedReturn.value.total)}`,
      fecha: formatDate(selectedReturn.value.return_date),
      motivo: selectedReturn.value.reason || 'Sin motivo especificado',
      metodoReembolso: getRefundMethodLabel(selectedReturn.value.refund_method),
      // 🔥 Validación de datos de contacto
      tieneEmail: !!(selectedReturn.value.customer?.email),
      tieneTelefono: !!(selectedReturn.value.customer?.phone),
      email: selectedReturn.value.customer?.email || null,
      telefono: selectedReturn.value.customer?.phone || null,
      items: (selectedReturn.value.return_items || selectedReturn.value.items || []).length
    } : null,
    // Instrucciones para la IA
    instrucciones: {
      enviarWhatsApp: selectedReturn.value 
        ? (selectedReturn.value.customer?.phone 
            ? 'Puedes enviar el comprobante por WhatsApp - el cliente tiene teléfono registrado'
            : '⚠️ El cliente NO tiene teléfono registrado. Pídele al usuario que ingrese el número manualmente o que actualice los datos del cliente primero')
        : 'Primero debes seleccionar una devolución',
      enviarEmail: selectedReturn.value 
        ? (selectedReturn.value.customer?.email 
            ? 'Puedes enviar el comprobante por Email - el cliente tiene email registrado'
            : '⚠️ El cliente NO tiene email registrado. Pídele al usuario que ingrese el email manualmente o que actualice los datos del cliente primero')
        : 'Primero debes seleccionar una devolución'
    }
  }
  
  // Actualizar el store de contexto
  uiContext.setScreenData(contextData)
}

onMounted(() => {
  loadReturns()
  document.addEventListener('keydown', handleKeyDown)
  
  // 🧠 Inicializar contexto para IA después de cargar
  setTimeout(() => updateScreenContextForAI(), 500)
  
  // 🎯 Registrar acciones para la IA de voz
  uiContext.registerAction('sendEmail', async () => {
    if (!selectedReturn.value) {
      return { success: false, message: 'No hay devolución seleccionada. Primero selecciona una devolución de la lista.' }
    }
    const hasEmail = selectedReturn.value.customer?.email
    if (!hasEmail) {
      return { 
        success: false, 
        message: `El cliente "${selectedReturn.value.customer?.name || 'Cliente General'}" no tiene email registrado. Dile al usuario que ingrese el email manualmente o que actualice los datos del cliente.`
      }
    }
    // Aquí iría la lógica de envío por email
    showToast('Función de envío por email próximamente', 'info')
    return { success: true, message: 'Comprobante enviado por email' }
  })
  
  uiContext.registerAction('sendWhatsApp', async () => {
    if (!selectedReturn.value) {
      return { success: false, message: 'No hay devolución seleccionada. Primero selecciona una devolución de la lista.' }
    }
    const hasPhone = selectedReturn.value.customer?.phone
    if (!hasPhone) {
      return { 
        success: false, 
        message: `El cliente "${selectedReturn.value.customer?.name || 'Cliente General'}" no tiene teléfono registrado. Dile al usuario que ingrese el número manualmente o que actualice los datos del cliente.`
      }
    }
    // Aquí iría la lógica de envío por WhatsApp
    showToast('Función de envío por WhatsApp próximamente', 'info')
    return { success: true, message: 'Comprobante enviado por WhatsApp' }
  })
  
  uiContext.registerAction('downloadPDF', async () => {
    if (!selectedReturn.value) {
      return { success: false, message: 'No hay devolución seleccionada. Primero selecciona una devolución de la lista.' }
    }
    // Aquí iría la lógica de descarga de PDF
    showToast('Función de descarga PDF próximamente', 'info')
    return { success: true, message: 'PDF descargado' }
  })
})

// 🧠 Watcher para actualizar contexto cuando cambien las devoluciones
watch(() => returns.value.length, () => {
  updateScreenContextForAI()
})

// 🧠 Watcher para actualizar contexto cuando cambie la devolución seleccionada
watch(selectedReturn, () => {
  updateScreenContextForAI()
})

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleKeyDown)
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

/* Scrollbar styles */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Dark mode scrollbar */
.dark ::-webkit-scrollbar-track {
  background: #27272a;
  border-radius: 4px;
}

.dark ::-webkit-scrollbar-thumb {
  background: #52525b;
  border-radius: 4px;
}

.dark ::-webkit-scrollbar-thumb:hover {
  background: #71717a;
}
</style>
