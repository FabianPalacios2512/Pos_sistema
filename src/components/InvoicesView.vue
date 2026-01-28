<template>
  <!-- Layout Full Height estilo WhatsApp Web - Sin doble scroll -->
  <div class="h-full font-sans bg-white dark:bg-[#131314] transition-colors duration-300 overflow-hidden flex flex-col">
    <div class="flex-none px-4 lg:px-6 pt-4 pb-3 space-y-4 animate-fade-in">
      
      <!-- NIVEL 1: Header con Título y Botones de Acción -->
          <div class="flex items-center justify-between">
            
            <!-- Título y Subtítulo -->
            <div>
              <h1 class="text-xl font-semibold text-gray-900 dark:text-white tracking-tight">Facturas</h1>
            </div>
        
        <!-- Botones de Acción -->
        <div class="flex items-center gap-2">
          <button
            @click="loadInvoices"
            class="px-4 py-2 bg-[#f8f9fa] dark:bg-[#1e1f20] hover:bg-gray-100 dark:hover:bg-[#282a2c] text-gray-600 dark:text-zinc-300 text-[13px] font-medium rounded-full transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Refrescar</span>
          </button>
          
          <button
            @click="navigateToPos"
            class="px-5 py-2 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 text-[13px] font-medium rounded-full transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nueva Factura</span>
          </button>
        </div>
        
      </div>

      <!-- NIVEL 2: KPIs Compactos - Ribbon horizontal tipo dashboard moderno -->
      <div class="flex items-center gap-3">
        
        <!-- KPI: Facturas del Mes -->
        <div class="flex-1 bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-2xl px-4 py-3 hover:bg-gray-100 dark:hover:bg-[#282a2c] transition-all duration-200">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white dark:bg-[#282a2c] rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wider">Facturas del Mes</p>
              <p class="text-lg font-semibold text-gray-900 dark:text-white leading-tight">{{ monthlyInvoices }}</p>
            </div>
          </div>
        </div>

        <!-- KPI: Total Facturado -->
        <div class="flex-1 bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-2xl px-4 py-3 hover:bg-gray-100 dark:hover:bg-[#282a2c] transition-all duration-200">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white dark:bg-[#282a2c] rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wider">Total Facturado</p>
              <p class="text-lg font-semibold text-gray-900 dark:text-white leading-tight">${{ formatCurrency(totalInvoiced) }}</p>
            </div>
          </div>
        </div>

        <!-- KPI: Cotizaciones -->
        <div class="flex-1 bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-2xl px-4 py-3 hover:bg-gray-100 dark:hover:bg-[#282a2c] transition-all duration-200">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white dark:bg-[#282a2c] rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wider">Cotizaciones</p>
              <p class="text-lg font-semibold text-gray-900 dark:text-white leading-tight">{{ quotations }}</p>
            </div>
          </div>
        </div>
        
      </div>

    </div>
    
    <!-- Master-Detail Layout WhatsApp Web Style - Ocupa todo el espacio restante -->
    <div class="flex-1 mx-3 lg:mx-4 rounded-2xl overflow-hidden transition-colors duration-300 border border-gray-200 dark:border-[#282a2c]">
      <div class="grid grid-cols-1 lg:grid-cols-10 h-full">
        
        <!-- PANEL IZQUIERDO: Lista con fondo gris sutil (Master) -->
        <div class="lg:col-span-3 overflow-hidden flex flex-col bg-[#f8f9fa] dark:bg-[#1a1a1d] border-r border-gray-200 dark:border-[#282a2c] transition-colors duration-300">
          
          <!-- Header compacto con búsqueda y filtros en UNA línea -->
          <div class="p-3 border-b border-gray-200 dark:border-[#282a2c] bg-[#f8f9fa] dark:bg-[#1a1a1d]">
            <!-- Búsqueda + Filtros en una sola fila -->
            <div class="flex items-center gap-2">
              <!-- Búsqueda -->
              <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                  v-model="searchTerm"
                  type="text"
                  placeholder="Buscar..."
                  class="w-full pl-9 pr-3 py-2 text-[13px] rounded-xl bg-white dark:bg-[#282a2c] border-none text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-zinc-600 transition-all duration-200">
              </div>
              
              <!-- Filtros compactos -->
              <select
                v-model="typeFilter"
                class="px-3 py-2 text-[13px] rounded-xl bg-white dark:bg-[#282a2c] border-none text-gray-600 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-zinc-600 transition-colors duration-200">
                <option value="">Todos</option>
                <option value="invoice">Facturas</option>
                <option value="quote">Cotizaciones</option>
              </select>
              
              <select
                v-model="statusFilter"
                class="px-3 py-2 text-[13px] rounded-xl bg-white dark:bg-[#282a2c] border-none text-gray-600 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-zinc-600 transition-colors duration-200">
                <option value="">Estado</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Pagada">Pagada</option>
                <option value="Devuelta">Devuelta</option>
              </select>
            </div>
          </div>
          
          <!-- Lista con scroll independiente - Fondo diferenciado -->
          <div class="flex-1 overflow-y-auto bg-[#f8f9fa] dark:bg-[#1a1a1d] px-2 py-1">
            
            <div
              v-for="invoice in displayedInvoices"
              :key="invoice.id"
              @click="selectInvoice(invoice)"
              class="px-3 py-3 my-1 cursor-pointer transition-all rounded-xl group relative"
              :class="[
                selectedInvoice?.id === invoice.id 
                  ? 'bg-white dark:bg-[#282a2c] shadow-sm' 
                  : 'hover:bg-white dark:hover:bg-[#252528]'
              ]"
            >
              <!-- Borde izquierdo de selección -->
              <div 
                v-if="selectedInvoice?.id === invoice.id"
                class="absolute left-0 top-2 bottom-2 w-1 bg-gray-900 dark:bg-white rounded-r-full"
              ></div>
              
              <div class="flex items-center justify-between gap-3">
                <!-- Info principal -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <p class="text-[13px] font-medium text-gray-800 dark:text-zinc-200 truncate">
                      {{ invoice.invoiceNumber || invoice.number || `DOC-${String(invoice.id).padStart(4, '0')}` }}
                    </p>
                    <span class="text-[9px] font-medium px-2 py-0.5 rounded-full flex-shrink-0"
                          :class="getStatusClasses(invoice.status)">
                      {{ getStatusLabel(invoice.status) }}
                    </span>
                  </div>
                  <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">
                      {{ invoice.customer || invoice.customer_name || 'Cliente General' }}
                    </p>
                    <span class="text-[10px] text-gray-400 dark:text-zinc-500">•</span>
                    <span class="text-[10px] text-gray-400 dark:text-zinc-500 flex-shrink-0">
                      {{ formatDateSmart(invoice.date) }}
                    </span>
                  </div>
                </div>
                <!-- Precio a la derecha -->
                <span class="text-[13px] font-medium text-gray-700 dark:text-zinc-300 flex-shrink-0">
                  ${{ formatCurrency(invoice.total) }}
                </span>
              </div>
            </div>
            
            <!-- Botón Cargar Más -->
            <div v-if="hasMoreInvoices" class="p-3 border-t border-gray-200 dark:border-[#282a2c] bg-[#f8f9fa] dark:bg-[#1a1a1d]">
              <button
                @click="loadMoreInvoices"
                class="w-full py-2.5 text-xs font-medium rounded-full transition-all flex items-center justify-center gap-2 bg-white dark:bg-[#282a2c] hover:bg-gray-50 dark:hover:bg-[#333338] text-gray-600 dark:text-zinc-400 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                </svg>
                Cargar más facturas... ({{ filteredInvoices.length - displayedInvoicesCount }} restantes)
              </button>
            </div>
            
            <!-- Estado vacío -->
            <div v-if="filteredInvoices.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
              <svg class="w-8 h-8 text-gray-300 dark:text-zinc-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="text-xs font-medium text-gray-600 dark:text-zinc-300">Sin resultados</p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Intenta con otros filtros</p>
            </div>
          </div>
        </div>

        <!-- PANEL DERECHO: Detalle con fondo blanco prominente (efecto "encima") -->
        <div class="lg:col-span-7 overflow-hidden flex flex-col bg-white dark:bg-[#212124] transition-colors duration-300">
          
          <!-- Estado: No seleccionado - Empty State estilo WhatsApp Web -->
          <div v-if="!selectedInvoice" class="flex-1 flex flex-col items-center justify-center p-12 text-center bg-white dark:bg-[#212124] relative">
            
            <!-- Ilustración SVG profesional y limpia -->
            <div class="mb-8 relative">
              <!-- Efecto glow suave de fondo -->
              <div class="absolute inset-0 bg-gradient-to-br from-emerald-200/30 via-transparent to-blue-200/30 dark:from-emerald-500/10 dark:to-blue-500/10 rounded-3xl blur-3xl scale-150"></div>
              
              <!-- Ilustración principal -->
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
                <rect x="94" y="88" width="28" height="7" rx="3.5" class="fill-emerald-100 dark:fill-emerald-500/20"/>
                <rect x="98" y="90" width="20" height="3" rx="1.5" class="fill-emerald-500 dark:fill-emerald-400"/>
                
                <!-- Segunda línea de total -->
                <rect x="54" y="102" width="25" height="3" rx="1.5" class="fill-gray-200 dark:fill-zinc-600"/>
                <rect x="94" y="100" width="28" height="7" rx="3.5" class="fill-gray-100 dark:fill-zinc-700"/>
                
                <!-- Total grande -->
                <rect x="54" y="118" width="35" height="4" rx="2" class="fill-gray-300 dark:fill-zinc-500"/>
                <rect x="94" y="116" width="28" height="8" rx="4" class="fill-emerald-500 dark:fill-emerald-400"/>
                
                <!-- Sello de verificación (check) -->
                <circle cx="120" cy="48" r="14" class="fill-emerald-100 dark:fill-emerald-500/20"/>
                <circle cx="120" cy="48" r="10" class="fill-emerald-500 dark:fill-emerald-400"/>
                <path d="M115 48L118 51L126 43" class="stroke-white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                
                <!-- Moneda principal -->
                <circle cx="148" cy="115" r="18" class="fill-amber-100 dark:fill-amber-500/20"/>
                <circle cx="148" cy="115" r="14" class="fill-amber-400 dark:fill-amber-500"/>
                <text x="148" y="120" text-anchor="middle" class="fill-white font-bold" style="font-size: 14px; font-family: system-ui;">$</text>
                
                <!-- Moneda secundaria -->
                <circle cx="158" cy="132" r="12" class="fill-amber-100 dark:fill-amber-500/15"/>
                <circle cx="158" cy="132" r="9" class="fill-amber-300 dark:fill-amber-400"/>
                <text x="158" y="136" text-anchor="middle" class="fill-white font-bold" style="font-size: 10px; font-family: system-ui;">$</text>
                
                <!-- Gráfico de línea -->
                <path d="M22 130 L32 115 L44 120 L56 100 L68 108 L80 88" class="stroke-blue-400 dark:stroke-blue-400" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                <circle cx="80" cy="88" r="4" class="fill-blue-500 dark:fill-blue-400"/>
                
                <!-- Punto de datos -->
                <circle cx="56" cy="100" r="3" class="fill-blue-400/50 dark:fill-blue-400/50"/>
                <circle cx="44" cy="120" r="2.5" class="fill-blue-300/50 dark:fill-blue-400/30"/>
              </svg>
            </div>
            
            <!-- Texto de bienvenida profesional -->
            <div class="relative z-10 max-w-md">
              <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-3">
                Centro de Facturación
              </h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400 leading-relaxed mb-2">
                Selecciona una factura o cotización del panel izquierdo para visualizar el desglose completo, imprimir o exportar a PDF.
              </p>
              <p class="text-xs text-gray-400 dark:text-zinc-500">
                Gestiona tus documentos fiscales de forma rápida y segura.
              </p>
            </div>
          </div>

          <!-- Estado: Documento seleccionado -->
          <div v-else class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-[#212124] transition-colors duration-300">
            
            <!-- Header del detalle con acciones contextuales -->
            <div class="px-5 py-3 border-b border-gray-100 dark:border-[#282a2c] bg-white dark:bg-[#212124]">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-zinc-200 tracking-tight">
                      {{ selectedInvoice.invoiceNumber || selectedInvoice.number || `DOC-${String(selectedInvoice.id).padStart(4, '0')}` }}
                    </h2>
                    <span
                      class="px-3 py-1 rounded-full text-xs font-medium"
                      :class="getStatusClasses(selectedInvoice.status)">
                      {{ getStatusLabel(selectedInvoice.status) }}
                    </span>
                    <span v-if="selectedInvoice.type === 'Cotización' || selectedInvoice.type === 'quote'" 
                          class="px-3 py-1 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400">
                      COTIZACIÓN
                    </span>
                  </div>
                  <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-zinc-400 font-medium">
                    <span class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      {{ formatDate(selectedInvoice.date) }}
                    </span>
                    <span class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      {{ selectedInvoice.customer || selectedInvoice.customer_name || 'Cliente General' }}
                    </span>
                    <span class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      {{ selectedInvoice.seller_name || 'Vendedor' }}
                    </span>
                  </div>
                </div>
                
                <!-- Acciones contextuales - AQUÍ va 'Facturar en POS' para cotizaciones -->
                <div class="flex items-center gap-2">
                  <!-- Botón principal: Facturar en POS (solo para cotizaciones) -->
                  <button
                    v-if="selectedInvoice.type === 'Cotización' || selectedInvoice.type === 'quote'"
                    @click="openInPos(selectedInvoice)"
                    class="px-5 py-2 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 text-sm font-medium rounded-full transition-all duration-200 flex items-center gap-2"
                    title="Convertir a factura">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Facturar en POS
                  </button>
                  
                  <!-- Botones de acción con texto descriptivo -->
                  <button
                    @click="viewAndPrintInvoice(selectedInvoice)"
                    class="px-3 py-2 rounded-full transition-all flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-[#f8f9fa] dark:hover:bg-[#282a2c]"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir
                  </button>
                  
                  <button
                    @click="downloadPDF(selectedInvoice)"
                    class="px-3 py-2 rounded-full transition-all flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-[#f8f9fa] dark:hover:bg-[#282a2c]"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Descargar
                  </button>
                  
                  <button
                    @click="sendByEmail(selectedInvoice)"
                    class="px-3 py-2 rounded-full transition-all flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-[#f8f9fa] dark:hover:bg-[#282a2c]"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Email
                  </button>
                  
                  <button
                    @click="sendByWhatsApp(selectedInvoice)"
                    class="px-3 py-2 rounded-full transition-all flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    WhatsApp
                  </button>
                  
                  <!-- Menú de más acciones -->
                  <div class="relative" @click.stop="">
                    <button
                      @click="toggleActionsMenu(selectedInvoice.id)"
                      class="p-2 rounded-full text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300 hover:bg-[#f8f9fa] dark:hover:bg-[#282a2c] transition-all"
                      title="Más opciones">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                      </svg>
                    </button>
                    
                    <!-- Dropdown menu -->
                    <div v-if="activeMenuId === selectedInvoice.id" 
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#282a2c] rounded-2xl border border-gray-100 dark:border-[#3a3a3f] z-50 py-1 overflow-hidden">
                      <button @click="editInvoice(selectedInvoice); closeActionsMenu()" 
                              class="w-full text-left px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-zinc-300 hover:bg-[#f8f9fa] dark:hover:bg-[#282a2c] flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Editar</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contenido scrollable - Con scroll independiente -->
            <div class="flex-1 overflow-y-auto p-3 bg-[#f8f9fa] dark:bg-[#1a1a1d]">
              
              <!-- Factura digital ocupa todo el ancho -->
              <div class="bg-white dark:bg-[#282a2c] rounded-2xl border border-gray-100 dark:border-[#3a3a3f] p-4">
                
                <!-- Encabezado de la factura digital -->
                <div class="pb-4 mb-4 border-b border-gray-100 dark:border-[#282a2c]">
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <h4 class="text-xs font-medium uppercase mb-2 text-gray-500 dark:text-zinc-400" style="letter-spacing: 0.05em;">Información del Cliente</h4>
                      <p class="text-sm font-medium text-gray-900 dark:text-zinc-200">{{ selectedInvoice.customer || selectedInvoice.customer_name || 'Cliente General' }}</p>
                      <p class="text-xs mt-1 text-gray-500 dark:text-zinc-400">ID Cliente: {{ selectedInvoice.customer_id || 'N/A' }}</p>
                    </div>
                    
                    <div class="text-right">
                      <h4 class="text-xs font-medium uppercase mb-2 text-gray-500 dark:text-zinc-400" style="letter-spacing: 0.05em;">Información del Documento</h4>
                      <p class="text-sm font-medium text-gray-900 dark:text-zinc-200">Documento: {{ selectedInvoice.invoiceNumber || selectedInvoice.number }}</p>
                      <p class="text-xs mt-1 text-gray-500 dark:text-zinc-400">Fecha: {{ formatDate(selectedInvoice.date) }}</p>
                      <p class="text-xs text-gray-500 dark:text-zinc-400">Vencimiento: {{ formatDate(selectedInvoice.due_date) }}</p>
                      <p v-if="selectedInvoice.status === 'returned' && selectedInvoice.return_reference" 
                         class="text-xs mt-2 px-2 py-1 bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 rounded-md inline-block font-medium">
                        🔁 Devuelta: {{ selectedInvoice.return_reference }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Productos - Tabla limpia y espaciosa -->
                <div class="mb-4">
                  <h4 class="text-xs font-medium mb-3 uppercase text-gray-900 dark:text-zinc-300" style="letter-spacing: 0.05em;">Productos / Servicios</h4>
                  
                  <div class="bg-white dark:bg-[#282a2c] rounded-xl overflow-hidden">
                    <table class="min-w-full">
                      <thead>
                        <tr class="bg-[#f8f9fa] dark:bg-[#282a2c] border-b border-gray-100 dark:border-[#282a2c]">
                          <th class="text-left text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">#</th>
                          <th class="text-left text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">Descripción</th>
                          <th class="text-center text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">Cant.</th>
                          <th class="text-right text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">Precio</th>
                          <th class="text-right text-[10px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider px-6 py-3">Total</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-50 dark:divide-[#282a2c]">
                        <tr v-if="!selectedInvoice.items || selectedInvoice.items.length === 0">
                          <td colspan="5" class="px-6 py-12 text-center">
                            <p class="text-sm text-gray-400 dark:text-zinc-500">No hay productos registrados</p>
                          </td>
                        </tr>
                        <tr v-else v-for="(item, index) in selectedInvoice.items" :key="`item-${index}`" class="hover:bg-[#f8f9fa] dark:hover:bg-[#282a2c] transition-colors">
                          <td class="px-4 py-2.5 text-xs text-gray-400 dark:text-zinc-500 font-medium">{{ index + 1 }}</td>
                          <td class="px-4 py-2.5">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ item.product_name || item.name || 'N/A' }}</p>
                            <p class="text-[10px] text-gray-500 dark:text-zinc-400">SKU: {{ item.product_code || item.code || 'N/A' }}</p>
                          </td>
                          <td class="text-center px-4 py-2.5">
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full bg-[#f8f9fa] dark:bg-[#282a2c] text-gray-600 dark:text-zinc-300">
                              {{ formatQuantity(item.quantity) }}
                            </span>
                          </td>
                          <td class="text-right px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-zinc-300">
                            ${{ formatCurrency(item.price || item.unit_price) }}
                          </td>
                          <td class="text-right px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-zinc-300">
                            ${{ formatCurrency(item.subtotal || (item.quantity * (item.price || item.unit_price))) }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    
                    <!-- Totales dentro de la factura digital -->
                    <div class="px-4 py-3 border-t border-gray-100 dark:border-[#282a2c] bg-[#f8f9fa] dark:bg-[#282a2c]">
                      <div class="flex justify-end">
                        <div class="w-64 space-y-1.5 text-sm">
                          <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-zinc-400">Subtotal:</span>
                            <span class="font-medium text-gray-900 dark:text-zinc-300">${{ formatCurrency(selectedInvoice.subtotal || selectedInvoice.total) }}</span>
                          </div>
                          <!-- Descuento aplicado -->
                          <div v-if="selectedInvoice.discount_amount && selectedInvoice.discount_amount > 0" class="flex justify-between">
                            <span class="text-rose-600 dark:text-rose-400 font-medium">Descuento:</span>
                            <span class="font-medium text-rose-600 dark:text-rose-400">-${{ formatCurrency(selectedInvoice.discount_amount) }}</span>
                          </div>
                          <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-zinc-400">IVA ({{ displayTaxRate }}%):</span>
                            <span class="font-medium text-gray-900 dark:text-zinc-300">${{ formatCurrency(selectedInvoice.tax || 0) }}</span>
                          </div>
                          <!-- Recargo por crédito -->
                          <div v-if="selectedInvoice.surcharge_amount && selectedInvoice.surcharge_amount > 0" class="flex justify-between">
                            <span class="text-amber-600 dark:text-amber-400 font-medium">Recargo Crédito:</span>
                            <span class="font-medium text-amber-600 dark:text-amber-400">+${{ formatCurrency(selectedInvoice.surcharge_amount) }}</span>
                          </div>
                          <div class="pt-2 mt-1.5 border-t border-gray-200 dark:border-[#282a2c]">
                            <div class="flex justify-between items-center">
                              <span class="text-sm font-medium text-gray-900 dark:text-zinc-200">TOTAL:</span>
                              <span class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400">${{ formatCurrency(selectedInvoice.total) }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Información adicional -->
                <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-100 dark:border-[#282a2c]">
                  <div>
                    <h4 class="text-xs font-medium uppercase mb-2 text-gray-500 dark:text-zinc-400" style="letter-spacing: 0.05em;">Método de Pago</h4>
                    <p class="text-sm text-gray-900 dark:text-zinc-200">
                      <span v-if="selectedInvoice.payment_method === 'credit'" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400">
                        Crédito
                      </span>
                      <span v-else>{{ getPaymentMethodName(selectedInvoice.payment_method) }}</span>
                    </p>
                  </div>
                  
                  <div>
                    <h4 class="text-xs font-medium uppercase mb-2 text-gray-500 dark:text-zinc-400" style="letter-spacing: 0.05em;">Vendedor</h4>
                    <p class="text-sm text-gray-900 dark:text-zinc-200">{{ selectedInvoice.seller_name || 'Vendedor' }}</p>
                  </div>
                </div>

                <!-- Nota/Observaciones si existen -->
                <div v-if="selectedInvoice.notes" class="mt-3 pt-3 border-t border-gray-100 dark:border-[#282a2c]">
                  <h4 class="text-xs font-medium uppercase mb-1.5 text-gray-600 dark:text-zinc-400 tracking-wider">Observaciones</h4>
                  <p class="text-sm p-2 rounded-xl text-gray-700 dark:text-zinc-300 bg-[#f8f9fa] dark:bg-[#282a2c]">{{ selectedInvoice.notes }}</p>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modales existentes se mantienen igual -->
  <!-- Modal de Cotización -->
  <QuotationModal 
    v-if="showQuotationModal"
    :show="showQuotationModal"
    :type="quotationModalType"
    :quotation-code="quotationData?.code || quotationData?.id || ''"
    :quotation-data="quotationData"
    @close="handleCloseQuotationModal"
    @print="handlePrintQuotation"
    @send-whatsapp="handleSendQuotationWhatsApp"
  />

  <!-- Modal para solicitar número de WhatsApp -->
  <PhoneInputModal
    :show="showPhoneModal"
    :message="phoneModalMessage"
    @confirm="handlePhoneConfirm"
    @cancel="handlePhoneCancel"
  />

  <!-- Modal para solicitar Email -->
  <Teleport to="body">
    <Transition name="modal-fade">
      <div v-if="showEmailModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-[9999] p-4">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-300 dark:border-zinc-800">
          <!-- Header con icono -->
          <div class="p-6 pb-4">
            <div class="text-center">
              <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-xl bg-blue-50 dark:bg-blue-950/50 mb-4 border border-blue-100 dark:border-blue-900/50">
                <svg class="h-7 w-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Email del Cliente</h3>
              <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed">
                El cliente no tiene un email registrado. Por favor ingrese el email donde desea enviar la factura.
              </p>
            </div>
          </div>

          <!-- Body -->
          <div class="px-6 pb-6">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                Email
              </label>
              <input
                v-model="emailInput"
                type="email"
                placeholder="cliente@ejemplo.com"
                class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                @keyup.enter="confirmEmail"
                autofocus
              >
            </div>
          </div>

          <!-- Footer -->
          <div class="px-6 pb-6 flex gap-3">
            <button 
              @click="cancelEmail"
              :disabled="sendingEmail"
              class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 rounded-xl font-semibold border border-gray-200 dark:border-zinc-700 transition-all duration-200 disabled:opacity-50"
            >
              Cancelar
            </button>
            <button 
              @click="confirmEmail"
              :disabled="sendingEmail || !emailInput"
              class="flex-1 px-4 py-2.5 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-xl font-bold shadow-lg shadow-blue-400/40 dark:shadow-blue-900/50 transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="!sendingEmail" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <svg v-else class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ sendingEmail ? 'Enviando...' : 'Enviar' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Modal de Recibo para Facturas Normales -->
  <ReceiptModal
    v-if="showReceiptModal"
    :sale="posCompatibleSale"
    :system-settings="{}"
    @close="closeInvoiceModal"
    @new-sale="handleNewSale"
    @send-whatsapp="handleSendWhatsApp"
  />

  <!-- Modal de Edición Bloqueada - Profesional y Limpio -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showEditModal" class="fixed inset-0 bg-black/50  flex items-center justify-center z-[60] p-4">
        <Transition
          enter-active-class="transition ease-out duration-300"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition ease-in duration-200"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div v-if="showEditModal" class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-zinc-800" @click.stop>
            
            <!-- Contenido del Modal -->
            <div class="p-8 text-center">
              <!-- Icono de Información -->
              <div class="w-16 h-16 bg-blue-50 dark:bg-blue-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              
              <!-- Título -->
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Edición No Disponible
              </h3>
              
              <!-- Mensaje -->
              <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed mb-6">
                Por razones de <strong class="text-gray-900 dark:text-white">seguridad y auditoría</strong>, las facturas no pueden ser modificadas una vez emitidas.
              </p>
              
              <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed mb-8">
                Si necesitas corregir un error, puedes <strong class="text-gray-900 dark:text-white">procesar una devolución</strong> del documento.
              </p>
              
              <!-- Botones -->
              <div class="flex gap-3">
                <button
                  @click="showEditModal = false"
                  class="flex-1 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-700 transition-all duration-200"
                >
                  Aceptar
                </button>
                
                <button
                  @click="handleReturnFromModal"
                  class="flex-1 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition-all duration-200 flex items-center justify-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                  </svg>
                  Hacer Devolución
                </button>
              </div>
            </div>
            
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>

  <!-- Modal de Nueva Factura (simplificado) -->
  <div v-if="showNewInvoiceModal" class="fixed inset-0 bg-slate-900/60  flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6">
      <h3 class="text-lg font-bold text-gray-900 mb-4">Nueva Factura</h3>
      <p class="text-sm text-gray-600">Funcionalidad próximamente...</p>
      <button @click="showNewInvoiceModal = false" class="mt-4 px-4 py-2 bg-gray-200 rounded-lg">
        Cerrar
      </button>
    </div>
  </div>

  <!-- Modal de Función Premium -->
  <Teleport to="body">
    <div v-if="showPremiumModal" class="fixed inset-0 bg-black/70  flex items-center justify-center z-[60] p-4 animate-fade-in">
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
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">¡Mejora tu Plan!</h3>
          
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
              @click="goToPlans"
              class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-base font-semibold rounded-xl transition-colors duration-200 shadow-lg"
            >
              Ver Planes
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useModuleNavigation } from '../composables/useModuleNavigation.js'
import { useToast } from '../composables/useToast.js'
import { useAuth } from '../store/auth.js'
import { appStore } from '../store/appStore.js'
import { useUIContextStore } from '../store/uiContextStore.js'
import QuotationModal from './QuotationModal.vue'
import PhoneInputModal from './PhoneInputModal.vue'
import ReceiptModal from './ReceiptModal.vue'
import { invoiceService } from '../services/invoiceService.js'

const { navigateToModule } = useModuleNavigation()
const uiContext = useUIContextStore()
import { invoicesService } from '../services/invoicesService.js'
import { formatInvoiceDate } from '@/utils/dateFormatter.js'
import { generateInvoicePDF, generateQuotationPDF, downloadPDF as downloadPDFHelper, getPDFBlob } from '../utils/pdfTemplates/pdfGenerator.js'
import { whatsappService } from '../services/whatsappService.js'

// Props
const props = defineProps({
  invoices: {
    type: Array,
    default: () => []
  },
  moduleName: {
    type: String,
    default: 'invoices'
    
  },
  queryParams: {
    type: Object,
    default: () => ({})
  },
  customers: {
    type: Array,
    default: () => []
  },
  products: {
    type: Array,
    default: () => []
  }
})

// Emits
const emit = defineEmits(['changeModule', 'open-quotation-in-pos', 'navigate', 'refresh', 'open-return-in-pos'])

// Composables
const { showToast, showSuccess, showError } = useToast()
const auth = useAuth()

// Computed para IVA
const displayTaxRate = computed(() => {
  const settings = appStore.systemSettings
  if (!settings || !settings.iva_enabled) return 0
  return parseFloat(settings.iva_percentage || 0)
})

// Estado
const selectedInvoice = ref(null)
const searchTerm = ref('')
const statusFilter = ref('')
const typeFilter = ref('')
const activeMenuId = ref(null)
const showNewInvoiceModal = ref(false)
const displayLimit = ref(20) // Cargar solo 20 facturas inicialmente
const showEditModal = ref(false)
const showQuotationModal = ref(false)
const quotationModalType = ref('success')
const quotationData = ref(null)
const showReceiptModal = ref(false)
const showPhoneModal = ref(false)
const phoneModalMessage = ref('')
const phoneModalResolve = ref(null)
const showEmailModal = ref(false)
const emailInput = ref('')
const emailModalResolve = ref(null)
const sendingEmail = ref(false)
const showPremiumModal = ref(false)
const premiumFeatureName = ref('')

// Función para verificar si el usuario tiene un plan básico
const isBasicPlan = () => {
  const plan = (appStore.tenantPlan || 'free_trial').toLowerCase()
  const isBasic = plan === 'free_trial' || plan === 'free' || plan === 'basic'
  console.log('📊 [InvoicesView] Plan detectado:', plan, '| Es plan básico:', isBasic)
  return isBasic
}

const goToPlans = () => {
  showPremiumModal.value = false
  navigateToModule('settings', { section: 'plans' })
}

// Computed
const filteredInvoices = computed(() => {
  let filtered = props.invoices
  
  // Excluir cotizaciones canceladas por defecto
  filtered = filtered.filter(invoice => invoice.status !== 'cancelled')

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(invoice => 
      invoice.invoiceNumber?.toLowerCase().includes(term) ||
      invoice.number?.toLowerCase().includes(term) ||
      invoice.customer?.toLowerCase().includes(term) ||
      invoice.customer_name?.toLowerCase().includes(term) ||
      invoice.total?.toString().includes(term)
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(invoice => invoice.status === statusFilter.value)
  }

  if (typeFilter.value) {
    filtered = filtered.filter(invoice => {
      if (typeFilter.value === 'invoice') {
        return invoice.type !== 'Cotización' && invoice.type !== 'quote'
      } else if (typeFilter.value === 'quote') {
        return invoice.type === 'Cotización' || invoice.type === 'quote'
      }
      return true
    })
  }

  return filtered.sort((a, b) => new Date(b.date) - new Date(a.date))
})

// Invoices paginadas para optimizar rendimiento
const displayedInvoices = computed(() => {
  return filteredInvoices.value.slice(0, displayLimit.value)
})

const hasMoreInvoices = computed(() => {
  return filteredInvoices.value.length > displayLimit.value
})

const displayedInvoicesCount = computed(() => {
  return Math.min(displayLimit.value, filteredInvoices.value.length)
})

const monthlyInvoices = computed(() => {
  const thisMonth = new Date().getMonth()
  const thisYear = new Date().getFullYear()
  return props.invoices.filter(invoice => {
    const invoiceDate = new Date(invoice.date)
    const isThisMonth = invoiceDate.getMonth() === thisMonth && invoiceDate.getFullYear() === thisYear
    const isInvoice = invoice.type === 'Factura' || invoice.type === 'invoice'
    const isPaid = invoice.status?.toLowerCase() === 'pagada' || invoice.status?.toLowerCase() === 'paid'
    return isThisMonth && isInvoice && isPaid
  }).length
})

const totalInvoiced = computed(() => {
  const thisMonth = new Date().getMonth()
  const thisYear = new Date().getFullYear()
  return props.invoices.filter(invoice => {
    const invoiceDate = new Date(invoice.date)
    const isThisMonth = invoiceDate.getMonth() === thisMonth && invoiceDate.getFullYear() === thisYear
    const isInvoice = invoice.type === 'Factura' || invoice.type === 'invoice'
    const isPaid = invoice.status?.toLowerCase() === 'pagada' || invoice.status?.toLowerCase() === 'paid'
    return isThisMonth && isInvoice && isPaid
  }).reduce((sum, invoice) => sum + (invoice.total || 0), 0)
})

const pendingInvoices = computed(() => {
  return props.invoices.filter(invoice => 
    invoice.status?.toLowerCase() === 'pendiente' || invoice.status?.toLowerCase() === 'pending'
  ).length
})

const quotations = computed(() => {
  return props.invoices.filter(invoice => 
    invoice.type === 'Cotización' || invoice.type === 'quote'
  ).length
})

const posCompatibleSale = computed(() => {
  if (!selectedInvoice.value) return {}
  
  // Parsear items
  let items = []
  try {
    if (selectedInvoice.value.items) {
      items = typeof selectedInvoice.value.items === 'string' 
        ? JSON.parse(selectedInvoice.value.items)
        : selectedInvoice.value.items
    }
  } catch (error) {
    items = []
  }

  const posItems = items.map((item, index) => ({
    id: item.id || item.product_id || index,
    name: item.name || item.product_name || `Producto ${index + 1}`,
    quantity: parseFloat(item.quantity || 1),
    price: parseFloat(item.price || item.unit_price || 0),
    subtotal: parseFloat(item.subtotal || (item.quantity * (item.price || item.unit_price)) || 0)
  }))

  return {
    invoiceNumber: selectedInvoice.value.number || selectedInvoice.value.invoiceNumber || `FV-${selectedInvoice.value.id}`,
    date: selectedInvoice.value.date || new Date().toISOString(),
    cashier: selectedInvoice.value.seller_name || 'Vendedor',
    customer: selectedInvoice.value.customer_name || selectedInvoice.value.customer || 'Cliente General',
    items: posItems,
    subtotal: parseFloat(selectedInvoice.value.subtotal || 0),
    discount: parseFloat(selectedInvoice.value.discount_amount || 0),
    tax: parseFloat(selectedInvoice.value.tax_amount || 0),
    total: parseFloat(selectedInvoice.value.total || 0),
    payments: [{
      method: selectedInvoice.value.payment_method || 'efectivo',
      amount: parseFloat(selectedInvoice.value.total || 0)
    }]
  }
})

// Métodos
const loadMoreInvoices = () => {
  displayLimit.value += 20
  console.log(`📄 Cargando más facturas. Mostrando: ${displayLimit.value}`)
}

const selectInvoice = async (invoice) => {
  try {
    const fullInvoice = await invoiceService.getInvoice(invoice.id)
    selectedInvoice.value = fullInvoice
    // El watcher de selectedInvoice se encarga de notificar al contexto UI
  } catch (error) {
    console.error('Error al cargar factura:', error)
    selectedInvoice.value = invoice
    // El watcher de selectedInvoice se encarga de notificar al contexto UI
  }
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  try {
    const [year, month, day] = dateString.split('-').map(num => parseInt(num, 10))
    return `${day.toString().padStart(2, '0')}/${month.toString().padStart(2, '0')}/${year}`
  } catch (error) {
    return '-'
  }
}

// Formato inteligente de fecha: "Hoy", "Ayer" o fecha
const formatDateSmart = (dateString) => {
  if (!dateString) return '-'
  
  try {
    // Parsear la fecha de la factura
    const [year, month, day] = dateString.split('-').map(num => parseInt(num, 10))
    const invoiceDate = new Date(year, month - 1, day)
    invoiceDate.setHours(0, 0, 0, 0) // Normalizar a medianoche
    
    // Fecha actual
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    
    // Calcular diferencia en días
    const diffTime = today - invoiceDate
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))
    
    if (diffDays === 0) {
      return 'Hoy'
    } else if (diffDays === 1) {
      return 'Ayer'
    } else if (diffDays < 7) {
      // Menos de una semana: mostrar día de la semana
      const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb']
      return days[invoiceDate.getDay()]
    } else {
      // Más de una semana: mostrar fecha DD/MM/YYYY
      return `${day.toString().padStart(2, '0')}/${month.toString().padStart(2, '0')}/${year}`
    }
  } catch (error) {
    return '-'
  }
}

const formatCurrency = (value) => {
  if (!value) return '0'
  return parseFloat(value).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

// Formatear cantidades: mostrar decimales solo si los tiene
const formatQuantity = (quantity) => {
  if (!quantity) return '0'
  const num = parseFloat(quantity)
  
  // Si tiene decimales, mostrar hasta 2 decimales
  if (num % 1 !== 0) {
    return num.toLocaleString('es-CO', { minimumFractionDigits: 1, maximumFractionDigits: 2 })
  }
  
  // Si es entero, no mostrar decimales
  return num.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const getStatusLabel = (status) => {
  const labels = {
    'Pendiente': 'Pendiente',
    'Pagada': 'Pagada',
    'Devuelta': 'Devuelta',
    'pending': 'Pendiente',
    'paid': 'Pagada',
    'returned': 'Devuelta'
  }
  return labels[status] || 'Pendiente'
}

const getPaymentMethodName = (method) => {
  const methodNames = {
    efectivo: 'Efectivo',
    tarjeta_debito: 'Tarjeta Débito',
    tarjeta_credito: 'Tarjeta Crédito',
    transferencia: 'Transferencia',
    nequi: 'Nequi',
    daviplata: 'Daviplata',
    cash: 'Efectivo'
  }
  return methodNames[method] || 'Efectivo'
}

const clearFilters = () => {
  searchTerm.value = ''
  statusFilter.value = ''
  typeFilter.value = ''
}

// Helpers para Avatar
const getInitials = (name) => {
  if (!name) return 'C'
  const parts = name.split(' ')
  if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase()
  return (parts[0][0] + parts[1][0]).toUpperCase()
}

const getColorForName = (name) => {
  if (!name) return { bg: '#F1F5F9', text: '#64748B' }
  const colors = [
    { bg: '#EEF2FF', text: '#6366F1' }, // Indigo
    { bg: '#F0FDF4', text: '#22C55E' }, // Green
    { bg: '#FEF2F2', text: '#EF4444' }, // Red
    { bg: '#FFF7ED', text: '#F97316' }, // Orange
    { bg: '#FAF5FF', text: '#A855F7' }, // Purple
    { bg: '#ECFEFF', text: '#06B6D4' }, // Cyan
    { bg: '#FDF4FF', text: '#D946EF' }, // Fuchsia
  ]
  let hash = 0
  for (let i = 0; i < name.length; i++) {
    hash = name.charCodeAt(i) + ((hash << 5) - hash)
  }
  return colors[Math.abs(hash) % colors.length]
}

const getStatusClasses = (status) => {
  const s = status?.toLowerCase() || ''
  if (s === 'pagada' || s === 'paid') return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400'
  if (s === 'pendiente' || s === 'pending') return 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-400'
  if (s === 'anulada' || s === 'cancelled') return 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-400'
  if (s === 'devuelta' || s === 'returned') return 'bg-purple-50 text-purple-700 dark:bg-purple-950 dark:text-purple-400'
  return 'bg-gray-100 text-gray-600 dark:bg-[#282a2c] dark:text-zinc-400'
}

const isQuotation = (invoice) => {
  if (invoice.type === 'Cotización' || invoice.type === 'quote') return true
  const num = invoice.invoiceNumber || invoice.number || ''
  return num.toString().toUpperCase().startsWith('COT')
}

const toggleActionsMenu = (id) => {
  activeMenuId.value = activeMenuId.value === id ? null : id
}

const closeActionsMenu = () => {
  activeMenuId.value = null
}

const openInPos = (invoice) => {
  // Mapear el invoice al formato que espera PosView (con campo 'code')
  const quotationData = {
    code: invoice.invoiceNumber || invoice.number || invoice.invoice_number || `DOC-${invoice.id}`,
    id: invoice.id,
    customer: invoice.customer || invoice.customer_name,
    customer_id: invoice.customer_id,
    items: invoice.items,
    total: invoice.total,
    status: invoice.status,
    type: invoice.type,
    date: invoice.date
  }
  
  emit('open-quotation-in-pos', quotationData)
  emit('changeModule', 'pos')
}

const viewAndPrintInvoice = async (invoice) => {
  try {
    // Determinar tipo de documento
    const isQuote = invoice.type === 'quote' || invoice.status === 'quotation'
    const docType = isQuote ? 'cotización' : 'factura'
    
    // Preparar datos del documento
    let items = []
    try {
      if (invoice.items) {
        items = typeof invoice.items === 'string' 
          ? JSON.parse(invoice.items)
          : invoice.items
      }
    } catch (error) {
      console.error('Error parseando items:', error)
      items = []
    }

    const invoiceData = {
      invoice_number: invoice.number || invoice.invoiceNumber || `FV-${invoice.id}`,
      quotation_number: invoice.number || invoice.invoiceNumber || `FV-${invoice.id}`,
      date: invoice.date || new Date(),
      created_at: invoice.created_at || invoice.date || new Date(),
      customer_name: invoice.customer_name || invoice.customer || 'Cliente General',
      customer: invoice.customer_name || invoice.customer || 'Cliente General',
      cashier: invoice.seller_name || 'Vendedor',
      items: items,
      subtotal: parseFloat(invoice.subtotal || 0),
      discount: parseFloat(invoice.discount_amount || 0),
      tax: parseFloat(invoice.tax || invoice.tax_amount || 0),
      tax_amount: parseFloat(invoice.tax || invoice.tax_amount || 0),
      total: parseFloat(invoice.total || 0),
      payments: invoice.payments || [{
        method: invoice.payment_method || 'efectivo',
        amount: parseFloat(invoice.total || 0)
      }],
      change: 0,
      notes: invoice.notes || '',
      validity_days: 15
    }

    // Generar PDF usando la plantilla correcta
    const pdf = isQuote 
      ? await generateQuotationPDF(invoiceData, appStore.systemSettings)
      : await generateInvoicePDF(invoiceData, appStore.systemSettings)
    
    // Imprimir directamente
    pdf.autoPrint()
    window.open(pdf.output('bloburl'), '_blank')
    
    showToast(`Preparando impresión de ${docType}...`, 'success')
  } catch (error) {
    console.error('Error al imprimir:', error)
    showToast('Error al preparar la impresión', 'error')
  }
}

const downloadPDF = async (invoice) => {
  try {
    // Determinar tipo de documento
    const isQuote = invoice.type === 'quote' || invoice.status === 'quotation'
    const docType = isQuote ? 'cotización' : 'factura'
    
    // Preparar datos de la factura
    let items = []
    try {
      if (invoice.items) {
        items = typeof invoice.items === 'string' 
          ? JSON.parse(invoice.items)
          : invoice.items
      }
    } catch (error) {
      console.error('Error parseando items:', error)
      items = []
    }

    const invoiceData = {
      invoice_number: invoice.number || invoice.invoiceNumber || `FV-${invoice.id}`,
      quotation_number: invoice.number || invoice.invoiceNumber || `FV-${invoice.id}`,
      date: invoice.date || new Date(),
      created_at: invoice.created_at || invoice.date || new Date(),
      customer_name: invoice.customer_name || invoice.customer || 'Cliente General',
      customer: invoice.customer_name || invoice.customer || 'Cliente General',
      cashier: invoice.seller_name || 'Vendedor',
      items: items,
      subtotal: parseFloat(invoice.subtotal || 0),
      discount: parseFloat(invoice.discount_amount || 0),
      tax: parseFloat(invoice.tax || invoice.tax_amount || 0),
      tax_amount: parseFloat(invoice.tax || invoice.tax_amount || 0),
      surcharge_amount: parseFloat(invoice.surcharge_amount || 0), // 🎯 Recargo por crédito
      total: parseFloat(invoice.total || 0),
      payments: invoice.payments || [{
        method: invoice.payment_method || 'efectivo',
        amount: parseFloat(invoice.total || 0)
      }],
      change: 0,
      notes: invoice.notes || '',
      validity_days: 15, // Para cotizaciones
      payment_method: invoice.payment_method || '' // Para identificar si es crédito
    }

    // Generar PDF usando la plantilla correcta según el tipo de documento
    const pdf = isQuote 
      ? await generateQuotationPDF(invoiceData, appStore.systemSettings)
      : await generateInvoicePDF(invoiceData, appStore.systemSettings)
    
    // Descargar con nombre apropiado
    const filename = isQuote 
      ? `cotizacion-${invoiceData.quotation_number}.pdf`
      : `factura-${invoiceData.invoice_number}.pdf`
    downloadPDFHelper(pdf, filename)
    
    showToast(`PDF de ${docType} descargado correctamente`, 'success')
  } catch (error) {
    console.error('Error descargando PDF:', error)
    showToast(`Error al descargar el PDF de ${docType || 'documento'}`, 'error')
  }
}

const sendByEmail = async (invoice) => {
  try {
    // Verificar plan antes de procesar
    if (isBasicPlan()) {
      premiumFeatureName.value = 'Envío por Email'
      showPremiumModal.value = true
      return
    }
    
    // Determinar tipo de documento
    const isQuote = invoice.type === 'quote' || invoice.status === 'quotation'
    const docType = isQuote ? 'cotización' : 'factura'
    const docTypeCapitalized = isQuote ? 'Cotización' : 'Factura'
    
    // Verificar si el cliente tiene email (probar múltiples campos)
    let email = invoice.customer_email || invoice.customer?.customer_email || invoice.customer?.email || invoice.email
    
    // Si no tiene email, pedirlo con el modal
    if (!email || email.trim() === '') {
      email = await requestEmail()
      if (!email) {
        return // Usuario canceló
      }
    }
    
    sendingEmail.value = true
    showToast(`Generando y enviando ${docType}...`, 'info')
    
    // Preparar datos de la factura (igual que downloadPDF)
    let items = []
    try {
      if (invoice.items) {
        items = typeof invoice.items === 'string' 
          ? JSON.parse(invoice.items)
          : invoice.items
      }
    } catch (error) {
      console.error('Error parseando items:', error)
      items = []
    }

    const invoiceData = {
      invoice_number: invoice.number || invoice.invoiceNumber || `FV-${invoice.id}`,
      quotation_number: invoice.number || invoice.invoiceNumber || `FV-${invoice.id}`,
      date: invoice.date || new Date(),
      created_at: invoice.created_at || invoice.date || new Date(),
      customer_name: invoice.customer_name || invoice.customer || 'Cliente General',
      customer: invoice.customer_name || invoice.customer || 'Cliente General',
      cashier: invoice.seller_name || 'Vendedor',
      items: items,
      subtotal: parseFloat(invoice.subtotal || 0),
      discount: parseFloat(invoice.discount_amount || 0),
      tax: parseFloat(invoice.tax || invoice.tax_amount || 0),
      tax_amount: parseFloat(invoice.tax || invoice.tax_amount || 0),
      surcharge_amount: parseFloat(invoice.surcharge_amount || 0), // 🎯 Recargo por crédito
      total: parseFloat(invoice.total || 0),
      payments: invoice.payments || [{
        method: invoice.payment_method || 'efectivo',
        amount: parseFloat(invoice.total || 0)
      }],
      change: 0,
      notes: invoice.notes || '',
      validity_days: 15, // Para cotizaciones
      payment_method: invoice.payment_method || '' // Para identificar si es crédito
    }

    // Generar PDF usando la plantilla correcta según el tipo de documento
    const pdf = isQuote 
      ? await generateQuotationPDF(invoiceData, appStore.systemSettings)
      : await generateInvoicePDF(invoiceData, appStore.systemSettings)
    
    // Obtener blob del PDF
    const pdfBlob = await getPDFBlob(pdf)
    
    // Enviar email con tipo de documento correcto
    await invoicesService.sendInvoiceEmail(
      invoice.id, 
      email, 
      pdfBlob, 
      isQuote,
      invoice.number || invoice.invoiceNumber || `DOC-${invoice.id}`
    )
    
    showToast(`✅ ${docTypeCapitalized} enviada exitosamente a ${email}`, 'success')
  } catch (error) {
    console.error('Error enviando email:', error)
    showToast(`Error al enviar la ${docType}`, 'error')
  } finally {
    sendingEmail.value = false
  }
}

const sendByWhatsApp = async (invoice) => {
  try {
    // Verificar plan antes de procesar
    if (isBasicPlan()) {
      premiumFeatureName.value = 'Envío por WhatsApp'
      showPremiumModal.value = true
      return
    }
    
    // Determinar tipo de documento
    const isQuote = invoice.type === 'quote' || invoice.status === 'quotation'
    const docType = isQuote ? 'cotización' : 'factura'
    
    // Verificar si el cliente tiene teléfono
    const phone = invoice.customer_phone || invoice.customer?.phone || invoice.customer?.customer_phone
    
    if (!phone || phone.trim() === '') {
      showToast('El cliente no tiene número de teléfono registrado', 'error')
      return
    }
    
    // Preparar datos del documento
    let items = []
    try {
      if (invoice.items) {
        items = typeof invoice.items === 'string' 
          ? JSON.parse(invoice.items)
          : invoice.items
      }
    } catch (error) {
      console.error('Error parseando items:', error)
      items = []
    }

    const documentData = {
      invoice_number: invoice.number || invoice.invoiceNumber || `FV-${invoice.id}`,
      quotation_number: invoice.number || invoice.invoiceNumber || `FV-${invoice.id}`,
      code: invoice.number || invoice.invoiceNumber || `FV-${invoice.id}`,
      date: invoice.date || new Date(),
      created_at: invoice.created_at || invoice.date || new Date(),
      customer_name: invoice.customer_name || invoice.customer || 'Cliente General',
      customer: invoice.customer_name || invoice.customer || 'Cliente General',
      cashier: invoice.seller_name || 'Vendedor',
      items: items,
      subtotal: parseFloat(invoice.subtotal || 0),
      discount: parseFloat(invoice.discount_amount || 0),
      tax: parseFloat(invoice.tax || invoice.tax_amount || 0),
      tax_amount: parseFloat(invoice.tax || invoice.tax_amount || 0),
      total: parseFloat(invoice.total || 0),
      payments: invoice.payments || [{
        method: invoice.payment_method || 'efectivo',
        amount: parseFloat(invoice.total || 0)
      }],
      change: 0,
      notes: invoice.notes || '',
      validity_days: 15
    }

    // Generar PDF usando la plantilla correcta
    const pdf = isQuote 
      ? await generateQuotationPDF(documentData, appStore.systemSettings)
      : await generateInvoicePDF(documentData, appStore.systemSettings)
    
    const pdfBlob = await getPDFBlob(pdf)
    
    // Enviar por WhatsApp con nombre del cliente
    showToast(`Enviando ${docType} por WhatsApp...`, 'info')
    await whatsappService.sendDocumentByWhatsApp(
      phone, 
      pdfBlob, 
      documentData.code || documentData.invoice_number, 
      isQuote ? 'quotation' : 'invoice',
      documentData.customer_name || 'Cliente'
    )
    
    showToast(`✅ ${docType} enviada por WhatsApp exitosamente`, 'success')
  } catch (error) {
    console.error('Error enviando por WhatsApp:', error)
    showToast('Error al enviar por WhatsApp', 'error')
  }
}

// Solicitar email mediante modal
const requestEmail = () => {
  return new Promise((resolve) => {
    emailInput.value = ''
    emailModalResolve.value = resolve
    showEmailModal.value = true
  })
}

// Confirmar email del modal
const confirmEmail = () => {
  const email = emailInput.value.trim()
  
  // Validar formato de email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(email)) {
    showToast('Email inválido', 'error')
    return
  }
  
  showEmailModal.value = false
  if (emailModalResolve.value) {
    emailModalResolve.value(email)
    emailModalResolve.value = null
  }
}

// Cancelar modal de email
const cancelEmail = () => {
  showEmailModal.value = false
  if (emailModalResolve.value) {
    emailModalResolve.value(null)
    emailModalResolve.value = null
  }
}

const editInvoice = (invoice) => {
  showEditModal.value = true
}

const handleReturnFromModal = () => {
  showEditModal.value = false
  // Emitir evento para abrir devoluciones en POS con el número de factura precargado
  if (selectedInvoice.value) {
    const invoiceNumber = selectedInvoice.value.invoiceNumber || selectedInvoice.value.number || selectedInvoice.value.invoice_number || ''
    emit('open-return-in-pos', invoiceNumber)
  }
}

const confirmDeleteInvoice = (invoice) => {
  if (confirm('¿Está seguro de que desea anular este documento?')) {
    showToast('Anulación próximamente', 'info')
  }
}

const loadInvoices = () => {
  showToast('Actualizando...', 'info')
  emit('refresh')
}

const navigateToPos = () => {
  // Navegar al POS para crear nueva factura
  emit('changeModule', 'pos')
}

const generateReport = () => {
  emit('navigate', 'reports')
}

const closeInvoiceModal = () => {
  showReceiptModal.value = false
}

const handleNewSale = () => {
  emit('changeModule', 'pos')
}

const handleSendWhatsApp = async () => {
  try {
    // Verificar plan antes de procesar
    if (isBasicPlan()) {
      premiumFeatureName.value = 'Envío por WhatsApp'
      showPremiumModal.value = true
      return
    }
    
    if (!selectedInvoice.value) {
      showToast('Seleccione una factura primero', 'warning')
      return
    }

    // Mostrar modal de teléfono
    showPhoneModal.value = true
    
    // Esperar confirmación del teléfono
    const phone = await new Promise((resolve) => {
      phoneModalResolve.value = resolve
    })

    if (!phone) {
      showToast('Envío cancelado', 'info')
      return
    }

    showToast('Generando PDF...', 'info')

    // Preparar datos de la factura
    let items = []
    try {
      if (selectedInvoice.value.items) {
        items = typeof selectedInvoice.value.items === 'string' 
          ? JSON.parse(selectedInvoice.value.items)
          : selectedInvoice.value.items
      }
    } catch (error) {
      console.error('Error parseando items:', error)
      items = []
    }

    const invoiceData = {
      invoice_number: selectedInvoice.value.number || selectedInvoice.value.invoiceNumber || `FV-${selectedInvoice.value.id}`,
      date: selectedInvoice.value.date || new Date(),
      customer_name: selectedInvoice.value.customer_name || selectedInvoice.value.customer || 'Cliente General',
      cashier: selectedInvoice.value.seller_name || 'Vendedor',
      items: items,
      subtotal: parseFloat(selectedInvoice.value.subtotal || 0),
      discount: parseFloat(selectedInvoice.value.discount_amount || 0),
      tax: parseFloat(selectedInvoice.value.tax_amount || selectedInvoice.value.tax || 0),
      total: parseFloat(selectedInvoice.value.total || 0),
      // 🎯 Recargo CrediTienda
      surcharge_amount: parseFloat(selectedInvoice.value.surcharge_amount || 0),
      payment_method: selectedInvoice.value.payment_method || 'efectivo',
      payments: selectedInvoice.value.payments || [{
        method: selectedInvoice.value.payment_method || 'efectivo',
        amount: parseFloat(selectedInvoice.value.total || 0)
      }],
      change: 0,
      notes: selectedInvoice.value.notes || ''
    }

    // Generar PDF usando plantilla centralizada
    const pdf = await generateInvoicePDF(invoiceData, appStore.systemSettings)
    const pdfBlob = getPDFBlob(pdf)

    // Enviar por WhatsApp
    const companyName = appStore.systemSettings.company_name || 'Nuestra Empresa'
    const message = `¡Hola! ${companyName} le envía su factura No. ${invoiceData.invoice_number}. Total: $${invoiceData.total.toLocaleString()}. ¡Gracias por su compra! 🙏`

    await whatsappService.sendInvoiceWithPDF(phone, pdfBlob, message, invoiceData.invoice_number)
    
    showToast('Factura enviada por WhatsApp correctamente', 'success')
  } catch (error) {
    console.error('Error enviando por WhatsApp:', error)
    showToast(error.message || 'Error al enviar por WhatsApp', 'error')
  }
}

const handleCloseQuotationModal = () => {
  showQuotationModal.value = false
}

const handlePrintQuotation = async () => {
  try {
    if (!selectedInvoice.value) {
      showToast('⚠️ No hay cotización seleccionada', 'warning')
      return
    }

    const quotationData = selectedInvoice.value

    // Generar el QR como imagen base64
    const QRCode = await import('qrcode')
    const qrDataURL = await QRCode.default.toDataURL(quotationData.invoice_number, {
      width: 150,
      height: 150,
      margin: 1,
      color: {
        dark: '#000000',
        light: '#FFFFFF'
      }
    })

    // Función para formatear moneda
    const formatCurrencyForPrint = (amount) => {
      return new Intl.NumberFormat('es-CO').format(amount)
    }

    // Crear contenido HTML del ticket
    const ticketContent = `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <style>
        @media print {
          body { margin: 0; }
          .no-print { display: none; }
        }
        body {
          font-family: 'Courier New', monospace;
          font-size: 12px;
          line-height: 1.2;
          max-width: 300px;
          margin: 0 auto;
          padding: 10px;
        }
        .header {
          text-align: center;
          border-bottom: 2px solid #000;
          padding-bottom: 10px;
          margin-bottom: 15px;
        }
        .company-name {
          font-size: 16px;
          font-weight: bold;
          margin-bottom: 5px;
        }
        .ticket-type {
          font-size: 14px;
          font-weight: bold;
          margin-bottom: 10px;
        }
        .section {
          margin-bottom: 15px;
        }
        .section-title {
          font-weight: bold;
          border-bottom: 1px solid #000;
          margin-bottom: 5px;
        }
        .code-section {
          text-align: center;
          border: 2px solid #000;
          padding: 10px;
          margin: 15px 0;
        }
        .quotation-code {
          font-size: 16px;
          font-weight: bold;
          margin-bottom: 10px;
        }
        .qr-code {
          margin: 10px 0;
        }
        .qr-instructions {
          font-size: 10px;
          text-align: center;
          margin-top: 5px;
        }
        .products-table {
          width: 100%;
          border-collapse: collapse;
          margin-bottom: 10px;
        }
        .products-table th,
        .products-table td {
          border: 1px solid #000;
          padding: 3px;
          text-align: left;
          font-size: 10px;
        }
        .products-table th {
          background-color: #f0f0f0;
          font-weight: bold;
        }
        .total-section {
          border-top: 2px solid #000;
          padding-top: 10px;
          text-align: right;
        }
        .total-amount {
          font-size: 16px;
          font-weight: bold;
        }
        .footer {
          text-align: center;
          border-top: 1px solid #000;
          padding-top: 10px;
          margin-top: 15px;
          font-size: 10px;
        }
        .print-button {
          text-align: center;
          margin: 20px 0;
        }
        .print-btn {
          background-color: #007bff;
          color: white;
          border: none;
          padding: 10px 20px;
          border-radius: 5px;
          cursor: pointer;
          font-size: 14px;
        }
      </style>
    </head>
    <body>
      <div class="header">
        <div class="company-name">SISTEMA POS</div>
        <div class="ticket-type">COTIZACIÓN</div>
        <div>Fecha: ${new Date(quotationData.date).toLocaleDateString('es-CO')}</div>
        <div>Hora: ${new Date(quotationData.date).toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: true })}</div>
        <div>Atendido por: ${quotationData.cashier || 'Vendedor'}</div>
      </div>

      <div class="section">
        <div class="section-title">CLIENTE</div>
        <div><strong>${quotationData.customer || 'Cliente General'}</strong></div>
      </div>

      <div class="code-section">
        <div class="quotation-code">CÓDIGO: ${quotationData.invoice_number}</div>
        <div class="qr-code">
          <img src="${qrDataURL}" alt="QR Code" style="width: 120px; height: 120px;">
        </div>
        <div class="qr-instructions">
          📱 Escanee para cargar automáticamente
        </div>
      </div>

      <div class="section">
        <div class="section-title">PRODUCTOS COTIZADOS</div>
        <table class="products-table">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cant.</th>
              <th>Precio</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            ${quotationData.items?.map(item => `
              <tr>
                <td>${item.product_name || item.name || 'Producto'}</td>
                <td>${item.quantity || 1}</td>
                <td>$${formatCurrencyForPrint(item.price || 0)}</td>
                <td>$${formatCurrencyForPrint((item.quantity || 1) * (item.price || 0))}</td>
              </tr>
            `).join('') || '<tr><td colspan="4">No hay productos</td></tr>'}
          </tbody>
        </table>
      </div>

      <div class="total-section">
        <div class="total-amount">
          TOTAL: $${formatCurrencyForPrint(quotationData.total || 0)}
        </div>
      </div>

      <div class="footer">
        <div>El cliente puede usar este código para</div>
        <div>realizar la compra posteriormente.</div>
        <div style="margin-top: 10px;">
          <strong>¡Gracias por su preferencia!</strong>
        </div>
      </div>

      <div class="print-button no-print">
        <button class="print-btn" onclick="window.print();">
          🖨️ Imprimir Ticket
        </button>
      </div>
    </body>
    </html>
    `

    // Abrir ventana de impresión
    const printWindow = window.open('', '_blank', 'width=400,height=600')
    if (printWindow) {
      printWindow.document.write(ticketContent)
      printWindow.document.close()
      printWindow.focus()
      
      // Auto-imprimir después de un delay
      setTimeout(() => {
        printWindow.print()
      }, 250)
    }

  } catch (error) {
    console.error('❌ Error al imprimir cotización:', error)
    showToast('❌ Error al imprimir cotización', 'error')
  }
}

const handleSendQuotationWhatsApp = () => {
  showToast('WhatsApp próximamente', 'info')
}

const handlePhoneConfirm = (phone) => {
  showPhoneModal.value = false
  if (phoneModalResolve.value) {
    phoneModalResolve.value(phone)
    phoneModalResolve.value = null
  }
}

const handlePhoneCancel = () => {
  showPhoneModal.value = false
  if (phoneModalResolve.value) {
    phoneModalResolve.value(null)
    phoneModalResolve.value = null
  }
}

// Watchers - Resetear paginación cuando cambien filtros
watch([searchTerm, statusFilter, typeFilter], () => {
  displayLimit.value = 20 // Resetear a 20 cuando cambien los filtros
})

// 🎯 Watcher para actualizar contexto cuando cambie la factura seleccionada
watch(selectedInvoice, (newInvoice) => {
  if (newInvoice) {
    // Notificar al contexto UI para la IA de voz
    uiContext.setSelectedElement('invoice', newInvoice, [
      { id: 'sendEmail', label: 'Enviar por Email' },
      { id: 'sendWhatsApp', label: 'Enviar por WhatsApp' },
      { id: 'downloadPDF', label: 'Descargar PDF' },
      { id: 'printInvoice', label: 'Imprimir' }
    ])
  } else {
    // Si se deselecciona, limpiar contexto
    uiContext.clearSelection()
  }
})

// 🎯 Watcher para query params de navegación AI (búsqueda y filtros automáticos)
watch(() => props.queryParams, async (newParams) => {
  if (!newParams || Object.keys(newParams).length === 0) return
  
  console.log('🔍 [InvoicesView] Query params detectados:', newParams)
  
  // Seleccionar factura por ID (para mostrar detalle)
  if (newParams.selectId) {
    // Esperar a que las facturas estén cargadas
    await new Promise(resolve => {
      if (props.invoices.length > 0) {
        resolve()
      } else {
        const unwatch = watch(() => props.invoices.length, (len) => {
          if (len > 0) {
            unwatch()
            resolve()
          }
        })
        // Timeout de seguridad
        setTimeout(resolve, 2000)
      }
    })
    
    const facturaId = parseInt(newParams.selectId)
    const factura = props.invoices.find(f => f.id === facturaId)
    if (factura) {
      // Usar selectInvoice para que notifique al contexto UI
      await selectInvoice(factura)
      console.log('✅ [InvoicesView] Factura seleccionada por AI:', factura.invoice_number || factura.id)
      showToast(`Mostrando factura ${factura.invoice_number || 'seleccionada'}`, 'success', 3000)
    }
  }
  
  // Aplicar búsqueda si hay query.search
  if (newParams.search) {
    searchTerm.value = newParams.search
    console.log('✅ [InvoicesView] Búsqueda aplicada:', newParams.search)
    showToast(`Buscando: "${newParams.search}"`, 'info', 3000)
  }
  
  // Aplicar filtro de fecha si hay query.date
  if (newParams.date) {
    const today = new Date().toISOString().split('T')[0]
    const yesterday = new Date(Date.now() - 86400000).toISOString().split('T')[0]
    
    switch(newParams.date) {
      case 'today':
        // Filtrar facturas de hoy (esto requeriría un filtro de fecha)
        console.log('✅ [InvoicesView] Filtro de fecha: HOY', today)
        showToast('Mostrando facturas de hoy', 'info', 3000)
        break
      case 'yesterday':
        console.log('✅ [InvoicesView] Filtro de fecha: AYER', yesterday)
        showToast('Mostrando facturas de ayer', 'info', 3000)
        break
    }
  }
}, { deep: true, immediate: true })

// Handler para tecla ESC - deseleccionar factura
const handleKeyDown = (event) => {
  if (event.key === 'Escape' && selectedInvoice.value) {
    selectedInvoice.value = null
  }
}

// 🧠 Actualizar contexto de pantalla para IA de voz
const updateScreenContextForAI = () => {
  // Calcular estadísticas por estado (sin pasar la lista completa)
  const facturasPagadas = props.invoices.filter(i => 
    (i.status?.toLowerCase() === 'pagada' || i.status?.toLowerCase() === 'paid') &&
    (i.type !== 'Cotización' && i.type !== 'quote')
  ).length
  
  const facturasPendientes = props.invoices.filter(i => 
    i.status?.toLowerCase() === 'pendiente' || i.status?.toLowerCase() === 'pending'
  ).length
  
  const facturasAnuladas = props.invoices.filter(i => 
    i.status?.toLowerCase() === 'anulada' || i.status?.toLowerCase() === 'cancelled'
  ).length
  
  const facturasDevueltas = props.invoices.filter(i => 
    i.status?.toLowerCase() === 'devuelta' || i.status?.toLowerCase() === 'returned'
  ).length
  
  const cotizacionesPendientes = props.invoices.filter(i => 
    (i.type === 'Cotización' || i.type === 'quote') &&
    (i.status?.toLowerCase() !== 'cancelled' && i.status?.toLowerCase() !== 'anulada')
  ).length
  
  // Datos resumidos para la IA (NO la lista completa)
  const contextData = {
    resumenFacturas: {
      total: props.invoices.length,
      facturasDelMes: monthlyInvoices.value,
      totalFacturado: `$${formatCurrency(totalInvoiced.value)}`,
      porEstado: {
        pagadas: facturasPagadas,
        pendientes: facturasPendientes,
        anuladas: facturasAnuladas,
        devueltas: facturasDevueltas
      },
      cotizaciones: cotizacionesPendientes
    },
    // Info de la factura seleccionada (si hay alguna)
    facturaSeleccionada: selectedInvoice.value ? {
      numero: selectedInvoice.value.number || selectedInvoice.value.invoiceNumber || `FV-${selectedInvoice.value.id}`,
      tipo: isQuotation(selectedInvoice.value) ? 'Cotización' : 'Factura',
      estado: getStatusLabel(selectedInvoice.value.status),
      cliente: selectedInvoice.value.customer_name || selectedInvoice.value.customer || 'Cliente General',
      total: `$${formatCurrency(selectedInvoice.value.total)}`,
      fecha: formatDate(selectedInvoice.value.date),
      // 🔥 Validación de datos de contacto para envíos
      tieneEmail: !!(selectedInvoice.value.customer_email || selectedInvoice.value.email),
      tieneTelefono: !!(selectedInvoice.value.customer_phone || selectedInvoice.value.phone),
      email: selectedInvoice.value.customer_email || selectedInvoice.value.email || null,
      telefono: selectedInvoice.value.customer_phone || selectedInvoice.value.phone || null
    } : null,
    // Instrucciones para la IA
    instrucciones: {
      enviarWhatsApp: selectedInvoice.value 
        ? (selectedInvoice.value.customer_phone || selectedInvoice.value.phone 
            ? 'Puedes enviar por WhatsApp - el cliente tiene teléfono registrado'
            : '⚠️ El cliente NO tiene teléfono registrado. Pídele al usuario que ingrese el número manualmente o que actualice los datos del cliente primero')
        : 'Primero debes seleccionar una factura',
      enviarEmail: selectedInvoice.value 
        ? (selectedInvoice.value.customer_email || selectedInvoice.value.email 
            ? 'Puedes enviar por Email - el cliente tiene email registrado'
            : '⚠️ El cliente NO tiene email registrado. Pídele al usuario que ingrese el email manualmente o que actualice los datos del cliente primero')
        : 'Primero debes seleccionar una factura'
    }
  }
  
  // Actualizar el store de contexto
  uiContext.setScreenData(contextData)
}

// Lifecycle
onMounted(() => {
  document.addEventListener('click', closeActionsMenu)
  document.addEventListener('keydown', handleKeyDown)
  
  // 🧠 Inicializar contexto de pantalla para IA
  updateScreenContextForAI()
  
  // 🎯 Registrar callbacks de acciones para la IA de voz (mejorados con validación)
  uiContext.registerAction('sendEmail', async () => {
    if (!selectedInvoice.value) {
      return { success: false, message: 'No hay factura seleccionada. Primero selecciona una factura de la lista.' }
    }
    // Verificar si tiene email
    const hasEmail = selectedInvoice.value.customer_email || selectedInvoice.value.email
    if (!hasEmail) {
      return { 
        success: false, 
        message: `El cliente "${selectedInvoice.value.customer_name || 'Cliente General'}" no tiene email registrado. Dile al usuario que ingrese el email manualmente usando el botón de enviar, o que primero actualice los datos del cliente en el módulo de Clientes.`
      }
    }
    await sendByEmail(selectedInvoice.value)
    return { success: true, message: 'Email enviado correctamente' }
  })
  
  uiContext.registerAction('sendWhatsApp', async () => {
    if (!selectedInvoice.value) {
      return { success: false, message: 'No hay factura seleccionada. Primero selecciona una factura de la lista.' }
    }
    // Verificar si tiene teléfono
    const hasPhone = selectedInvoice.value.customer_phone || selectedInvoice.value.phone
    if (!hasPhone) {
      return { 
        success: false, 
        message: `El cliente "${selectedInvoice.value.customer_name || 'Cliente General'}" no tiene teléfono registrado. Dile al usuario que ingrese el número manualmente usando el botón de WhatsApp, o que primero actualice los datos del cliente en el módulo de Clientes.`
      }
    }
    await handleSendWhatsApp()
    return { success: true, message: 'WhatsApp enviado correctamente' }
  })
  
  uiContext.registerAction('downloadPDF', async () => {
    if (!selectedInvoice.value) {
      return { success: false, message: 'No hay factura seleccionada. Primero selecciona una factura de la lista.' }
    }
    await downloadPDF(selectedInvoice.value)
    return { success: true, message: 'PDF descargado correctamente' }
  })
  
  uiContext.registerAction('printInvoice', async () => {
    if (!selectedInvoice.value) {
      return { success: false, message: 'No hay factura seleccionada. Primero selecciona una factura de la lista.' }
    }
    await viewAndPrintInvoice(selectedInvoice.value)
    return { success: true, message: 'Documento listo para imprimir' }
  })
  
  // NO seleccionar automáticamente - dejar en blanco para que el usuario elija
})

// 🧠 Watcher para actualizar contexto cuando cambien las facturas
watch(() => props.invoices.length, () => {
  updateScreenContextForAI()
})

// 🧠 Watcher para actualizar contexto cuando cambie la factura seleccionada
watch(selectedInvoice, () => {
  updateScreenContextForAI()
})

onBeforeUnmount(() => {
  document.removeEventListener('click', closeActionsMenu)
  document.removeEventListener('keydown', handleKeyDown)
})
</script>

<style scoped>
/* Fuente Inter */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

/* Animaciones */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

/* Scrollbar personalizado eliminado para usar estilos globales */
</style>
