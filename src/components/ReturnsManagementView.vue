<template>
  <div class="min-h-screen font-sans bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- NIVEL 1: Header con Título y Botones de Acción -->
      <div class="flex items-center justify-between">
            
            <!-- Título y Subtítulo -->
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Devoluciones</h1>
              <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Control y seguimiento de devoluciones</p>
            </div>
            
            <!-- Botones de Acción -->
            <div class="flex items-center gap-3">
              <button
                @click="loadReturns"
                class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-800 shadow-sm transition-all duration-200 active:scale-95 flex items-center gap-2 group">
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

      <!-- NIVEL 2: KPIs Ejecutivos - Grid de 3 Columnas (Estilo Fantasma) -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <!-- KPI: Total Devuelto (Estilo Fantasma - Sin borde, solo sombra difusa) -->
        <div class="bg-white/80 dark:bg-zinc-800/40 backdrop-blur-sm rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-red-50 dark:bg-red-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Total Devuelto</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">${{ formatCurrency(stats.totalRefunded) }}</p>
            </div>
          </div>
        </div>

        <!-- KPI: Completadas (Estilo Fantasma) -->
        <div class="bg-white/80 dark:bg-zinc-800/40 backdrop-blur-sm rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Completadas</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">{{ stats.completedCount }}</p>
            </div>
          </div>
        </div>

        <!-- KPI: Pendientes (Estilo Fantasma) -->
        <div class="bg-white/80 dark:bg-zinc-800/40 backdrop-blur-sm rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Pendientes</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">{{ stats.pendingCount }}</p>
            </div>
          </div>
        </div>
        
      </div>

      <!-- Master-Detail Layout Enterprise: 30/70 - Unificado como WhatsApp -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 transition-colors duration-300" style="height: calc(100vh - 240px); min-height: 650px;">
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
                placeholder="Buscar devoluciones o clientes..."
                class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-300">
            </div>
            
            <!-- Filtro de estado -->
            <select
              v-model="statusFilter"
              class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300">
              <option value="">Estado</option>
              <option value="pending">⏳ Pendientes</option>
              <option value="completed">✅ Completadas</option>
              <option value="cancelled">❌ Canceladas</option>
            </select>
          </div>
          
          <!-- Lista minimalista estilo WhatsApp -->
          <div class="flex-1 overflow-y-auto bg-white dark:bg-zinc-900 px-2">
            
            <div
              v-for="returnItem in filteredReturns"
              :key="returnItem.id"
              @click="selectReturn(returnItem)"
              class="px-3 py-3.5 my-1 cursor-pointer transition-all rounded-xl group relative"
              :class="[
                selectedReturn?.id === returnItem.id 
                  ? 'bg-indigo-50 dark:bg-indigo-500/10 shadow-sm' 
                  : 'bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800/60'
              ]"
            >
              <!-- Borde izquierdo de selección -->
              <div 
                v-if="selectedReturn?.id === returnItem.id"
                class="absolute left-0 top-2 bottom-2 w-1 bg-indigo-500 rounded-r-full"
              ></div>
              
              <div class="flex items-center justify-between gap-3">
                <!-- Info principal -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <p class="text-[13px] font-semibold text-gray-800 dark:text-zinc-200 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                      {{ returnItem.number }}
                    </p>
                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full border flex-shrink-0"
                          :class="getStatusClasses(returnItem.status)">
                      {{ getStatusLabel(returnItem.status) }}
                    </span>
                  </div>
                  <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">
                      {{ returnItem.customer?.name || 'Cliente General' }}
                    </p>
                    <span class="text-[10px] text-gray-400 dark:text-zinc-500">•</span>
                    <span class="text-[10px] text-gray-400 dark:text-zinc-500 flex-shrink-0">
                      {{ formatDate(returnItem.return_date) }}
                    </span>
                  </div>
                </div>
                <!-- Precio a la derecha -->
                <span class="text-sm font-bold text-gray-800 dark:text-zinc-300 flex-shrink-0">
                  ${{ formatCurrency(returnItem.total) }}
                </span>
              </div>
            </div>
            
            <!-- Estado vacío -->
            <div v-if="filteredReturns.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
              <svg class="w-8 h-8 text-gray-300 dark:text-zinc-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
              </svg>
              <p class="text-xs font-semibold text-gray-600 dark:text-zinc-300">Sin resultados</p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Intenta con otros filtros</p>
            </div>
          </div>
        </div>

        <!-- PANEL DERECHO: Detalle Amplio (70%) -->
        <div class="lg:col-span-7 overflow-hidden flex flex-col bg-gray-50/30 dark:bg-zinc-950/30 transition-colors duration-300">
          
          <!-- Estado: No seleccionado - Empty State Profesional estilo WhatsApp -->
          <div v-if="!selectedReturn" class="flex-1 flex flex-col items-center justify-center p-12 text-center bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-zinc-900/50 dark:via-zinc-900/30 dark:to-zinc-900/50 relative">
            
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
            
            <!-- Footer de confianza estilo WhatsApp -->
            <div class="absolute bottom-6 left-0 right-0 flex items-center justify-center gap-2 text-xs text-gray-400 dark:text-zinc-500">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              <span>Tus datos de devoluciones están protegidos y sincronizados en tiempo real</span>
            </div>
          </div>

          <!-- Estado: Devolución seleccionada -->
          <div v-else class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-zinc-900/50 transition-colors duration-300">
            
            <!-- Header del detalle con acciones contextuales -->
            <div class="p-6 border-b border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-3">
                    <h2 class="text-2xl font-black text-gray-900 dark:text-zinc-200 tracking-tight">
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
                      <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      {{ selectedReturn.customer?.name || 'Cliente General' }}
                    </span>
                    <span class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      {{ selectedReturn.user?.name || 'Usuario' }}
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
                    @click="downloadReturn"
                    class="px-4 py-2 rounded-lg transition-all flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800 border border-transparent hover:border-gray-200 dark:hover:border-zinc-700"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Descargar
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

            <!-- Contenido scrollable - Documento digital limpio igual que Facturas -->
            <div class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-zinc-950/50">
              
              <!-- Documento digital ocupa todo el ancho -->
              <div class="bg-white dark:bg-zinc-800/50 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
                
                <!-- Encabezado del documento digital -->
                <div class="pb-5 mb-5 border-b border-gray-200 dark:border-zinc-700">
                  <div class="grid grid-cols-2 gap-6">
                    <div>
                      <h4 class="text-xs font-bold uppercase mb-2 text-gray-500 dark:text-zinc-400" style="letter-spacing: 0.05em;">Información del Cliente</h4>
                      <p class="text-sm font-bold text-gray-900 dark:text-zinc-200">{{ selectedReturn.customer?.name || 'Cliente General' }}</p>
                      <p class="text-xs mt-1 text-gray-500 dark:text-zinc-400">ID Cliente: {{ selectedReturn.customer?.id || 'N/A' }}</p>
                    </div>
                    
                    <div class="text-right">
                      <h4 class="text-xs font-bold uppercase mb-2 text-gray-500 dark:text-zinc-400" style="letter-spacing: 0.05em;">Información del Documento</h4>
                      <p class="text-sm font-bold text-gray-900 dark:text-zinc-200">Devolución: {{ selectedReturn.number }}</p>
                      <p class="text-xs mt-1 text-gray-500 dark:text-zinc-400">Fecha: {{ formatDate(selectedReturn.return_date) }}</p>
                      <p class="text-xs text-gray-500 dark:text-zinc-400">Factura Original: {{ selectedReturn.original_invoice?.number || 'N/A' }}</p>
                    </div>
                  </div>
                </div>

                <!-- Productos - Tabla limpia y espaciosa -->
                <div class="mb-6">
                  <h4 class="text-xs font-bold mb-4 uppercase text-gray-900 dark:text-zinc-300" style="letter-spacing: 0.05em;">Productos Devueltos</h4>
                  
                  <div class="bg-white dark:bg-zinc-900/50 rounded-lg overflow-hidden">
                    <table class="min-w-full">
                      <thead>
                        <tr class="bg-gray-50 dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">
                          <th class="text-left text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">#</th>
                          <th class="text-left text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">Descripción</th>
                          <th class="text-center text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">Cant.</th>
                          <th class="text-right text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">Precio</th>
                          <th class="text-right text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">Total</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                        <tr v-if="!selectedReturn.return_items || selectedReturn.return_items.length === 0">
                          <td colspan="5" class="px-6 py-12 text-center">
                            <p class="text-sm text-gray-400 dark:text-zinc-500">No hay productos registrados</p>
                          </td>
                        </tr>
                        <tr v-else v-for="(item, index) in selectedReturn.return_items" :key="`item-${index}`" class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 hover:shadow-sm transition-colors">
                          <td class="px-6 py-4 text-xs text-gray-400 dark:text-zinc-500 font-medium">{{ index + 1 }}</td>
                          <td class="px-6 py-4">
                            <p class="text-sm font-bold text-gray-800 dark:text-white">{{ item.product?.name || item.name || 'N/A' }}</p>
                            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">SKU: {{ item.product?.code || item.code || 'N/A' }}</p>
                          </td>
                          <td class="text-center px-6 py-4">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-bold rounded-md bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-zinc-300">
                              {{ item.quantity }}
                            </span>
                          </td>
                          <td class="text-right px-6 py-4 text-sm font-medium text-gray-600 dark:text-zinc-300">
                            ${{ formatCurrency(item.unit_price || item.price) }}
                          </td>
                          <td class="text-right px-6 py-4 text-sm font-bold text-gray-900 dark:text-zinc-300">
                            ${{ formatCurrency(item.subtotal || (item.quantity * (item.unit_price || item.price))) }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    
                    <!-- Totales dentro del documento digital -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900/30">
                      <div class="flex justify-end">
                        <div class="w-72 space-y-2 text-sm">
                          <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-zinc-400">Subtotal:</span>
                            <span class="font-semibold text-gray-900 dark:text-zinc-300">${{ formatCurrency(selectedReturn.subtotal || selectedReturn.total) }}</span>
                          </div>
                          <div class="pt-3 mt-2 border-t border-gray-300 dark:border-zinc-600">
                            <div class="flex justify-between items-center">
                              <span class="text-base font-bold text-gray-900 dark:text-zinc-200">TOTAL DEVUELTO:</span>
                              <span class="text-2xl font-bold text-red-600 dark:text-red-400">${{ formatCurrency(selectedReturn.total) }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Información adicional -->
                <div class="grid grid-cols-2 gap-6 pt-5 border-t border-gray-200 dark:border-zinc-700">
                  <div>
                    <h4 class="text-xs font-bold uppercase mb-2 text-gray-500 dark:text-zinc-400" style="letter-spacing: 0.05em;">Método de Reembolso</h4>
                    <p class="text-sm text-gray-900 dark:text-zinc-200">{{ getRefundMethodLabel(selectedReturn.refund_method) }}</p>
                  </div>
                  
                  <div>
                    <h4 class="text-xs font-bold uppercase mb-2 text-gray-500 dark:text-zinc-400" style="letter-spacing: 0.05em;">Procesado por</h4>
                    <p class="text-sm text-gray-900 dark:text-zinc-200">{{ selectedReturn.user?.name || 'Usuario' }}</p>
                  </div>
                </div>

                <!-- Nota/Razón de la devolución -->
                <div v-if="selectedReturn.reason" class="mt-5 pt-5 border-t border-gray-200 dark:border-zinc-700">
                  <h4 class="text-xs font-bold uppercase mb-2 text-gray-600 dark:text-zinc-400 tracking-wider">Razón de la Devolución</h4>
                  <p class="text-sm p-3 rounded-lg text-gray-700 dark:text-zinc-300 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30">{{ selectedReturn.reason }}</p>
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
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
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
  if (s === 'completed' || s === 'completada') return 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-900/50'
  if (s === 'pending' || s === 'pendiente') return 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-950/50 dark:text-amber-400 dark:border-amber-900/50'
  if (s === 'cancelled' || s === 'cancelada') return 'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-950/50 dark:text-rose-400 dark:border-rose-900/50'
  return 'bg-slate-50 text-slate-600 border-slate-100 dark:bg-zinc-800/50 dark:text-zinc-400 dark:border-zinc-700/50'
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

onMounted(() => {
  loadReturns()
  document.addEventListener('keydown', handleKeyDown)
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
