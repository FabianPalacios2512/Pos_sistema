<template>
  <div class="min-h-screen font-sans" style="background-color: #F4F6F8;">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      
      <!-- Header Simple y Elegante -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-4 border-b border-slate-200/60">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-black rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-slate-900">Gestión de Facturas</h1>
            <p class="text-sm text-slate-600">Sistema de facturación y documentos tributarios</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3 w-full md:w-auto">
          <!-- Botón Reportes (Neutro) -->
          <button @click="generateReport" 
                  class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span>Reportes</span>
          </button>
          
          <!-- Botón Nueva Factura (Primario Oscuro) -->
          <button @click="redirectToPos" 
                  class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-200 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Nueva Factura</span>
          </button>
        </div>
      </div>
      
      <!-- Métricas Principales Compactas -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Facturas del Mes --  >
        <div class="bg-white rounded-lg p-3.5 border border-slate-200 hover:border-indigo-200 transition-all duration-200 hover:shadow-md shadow-sm">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-slate-500">Facturas del Mes</p>
              <p class="text-xl font-bold text-slate-900 tracking-tight">{{ monthlyInvoices }}</p>
            </div>
          </div>
        </div>
        
        <!-- Total Facturado -->
        <div class="bg-white rounded-lg p-3.5 border border-slate-200 hover:border-emerald-200 transition-all duration-200 hover:shadow-md shadow-sm">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-slate-500">Total Facturado</p>
              <p class="text-xl font-bold text-slate-900 tracking-tight">${{ (totalInvoiced / 1000).toFixed(0) }}k</p>
            </div>
          </div>
        </div>
        
        <!-- Pendientes de Pago -->
        <div class="bg-white rounded-lg p-3.5 border border-slate-200 hover:border-amber-200 transition-all duration-200 hover:shadow-md shadow-sm">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-slate-500">Pendientes de Pago</p>
              <p class="text-xl font-bold text-slate-900 tracking-tight">{{ pendingInvoices }}</p>
            </div>
          </div>
        </div>
        
        <!-- Cotizaciones Activas -->
        <div class="bg-white rounded-lg p-3.5 border border-slate-200 hover:border-blue-200 transition-all duration-200 hover:shadow-md shadow-sm">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-slate-500">Cotizaciones Activas</p>
              <p class="text-xl font-bold text-slate-900 tracking-tight">{{ quotations }}</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Filtros Empresariales -->
      <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <div class="flex flex-wrap items-center gap-3">
          
          <!-- Búsqueda -->
          <div class="flex-1 min-w-48 relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input v-model="searchTerm" 
                   type="text" 
                   placeholder="Buscar por número, cliente o monto..." 
                   class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50 focus:bg-white transition-all">
          </div>
          
          <!-- Filtro de Estado -->
          <div class="relative">
            <select v-model="statusFilter" 
                    class="appearance-none pl-3 pr-8 py-2 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 min-w-36 bg-slate-50 focus:bg-white transition-all text-slate-600 font-medium">
              <option value="">Estado</option>
              <option value="draft">Borrador</option>
              <option value="sent">Enviada</option>
              <option value="paid">Pagada</option>
              <option value="overdue">Vencida</option>
            </select>
            <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </div>
          
          <!-- Filtro de Tipo -->
          <div class="relative">
            <select v-model="typeFilter" 
                    class="appearance-none pl-3 pr-8 py-2 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 min-w-36 bg-slate-50 focus:bg-white transition-all text-slate-600 font-medium">
              <option value="">Tipo</option>
              <option value="invoice">Factura</option>
              <option value="quote">Cotización</option>
              <option value="credit_note">Nota Crédito</option>
            </select>
            <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </div>
          
          <!-- Filtro de Fecha -->
          <input v-model="dateFilter" 
                 type="date" 
                 class="px-3 py-2 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-slate-50 focus:bg-white transition-all text-slate-600 font-medium">
          
          <!-- Botón Limpiar Filtros (Papelería) -->
          <button @click="clearFilters" 
                  class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors"
                  title="Limpiar filtros">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
      </div>
      
      <!-- Tabla de Facturas -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden">
        
        <!-- Header de Tabla Empresarial -->
        <div class="bg-gradient-to-r from-slate-50 to-slate-50/50 border-b border-slate-200 px-6 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-slate-900">Documentos de Facturación</h2>
            <p class="text-xs text-slate-500 mt-0.5 font-medium">{{ filteredInvoices.length }} documento{{ filteredInvoices.length !== 1 ? 's' : '' }} • Total: ${{ filteredInvoices.reduce((sum, inv) => sum + (inv.total || 0), 0).toLocaleString() }}</p>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50/80 border-b border-slate-200">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wide">
                  Documento
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wide">
                  Cliente
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wide">
                  Fecha
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wide">
                  Vencimiento
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wide">
                  Monto
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wide">
                  Estado
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wide">
                  Acciones
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <tr v-for="(invoice, index) in paginatedInvoices" 
                  :key="invoice.id" 
                  class="hover:bg-slate-50/50 transition-all duration-150">
                  
                <!-- Documento -->
                <td class="px-6 py-4">
                  <div class="flex items-center space-x-3">
                    <!-- Icono diferente para Cotización vs Factura -->
                    <div v-if="invoice.type === 'Cotización'" 
                         class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0 border border-blue-200">
                      <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                      </svg>
                    </div>
                    <div v-else 
                         class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center flex-shrink-0 border border-slate-200">
                      <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                    <div>
                      <div class="flex items-center space-x-2">
                        <p class="text-sm font-bold text-slate-900">
                          {{ invoice.invoiceNumber || invoice.number || `DOC-${String(index + 1).padStart(4, '0')}` }}
                        </p>
                        <!-- Etiqueta COT para cotizaciones -->
                        <span v-if="invoice.type === 'Cotización'" 
                              class="px-1.5 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded border border-blue-200">
                          COT
                        </span>
                      </div>
                      <p class="text-xs text-slate-500 font-medium mt-0.5">{{ getDocumentTypeLabel(invoice.type || 'invoice') }}</p>
                    </div>
                  </div>
                </td>
                
                <!-- Cliente -->
                <td class="px-6 py-4">
                  <p class="text-sm font-semibold text-slate-900">{{ invoice.customer || invoice.customer_name || 'Cliente General' }}</p>
                  <p class="text-xs text-slate-500 font-medium">ID: {{ invoice.customer_id || 'N/A' }}</p>
                </td>
                
                <!-- Fecha -->
                <td class="px-6 py-4">
                  <p class="text-sm font-medium text-slate-700">{{ formatDate(invoice.date) }}</p>
                </td>
                
                <!-- Vencimiento -->
                <td class="px-6 py-4">
                  <p class="text-sm font-medium text-slate-700">{{ formatDate(invoice.due_date) }}</p>
                </td>
                
                <!-- Monto -->
                <td class="px-6 py-4">
                  <p class="text-base font-black text-slate-900">${{ (invoice.total || 0).toLocaleString() }}</p>
                </td>
                
                <!-- Estado con Badge Pill -->
                <td class="px-6 py-4">
                  <span :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                    getStatusClass(invoice.status)
                  ]">
                    {{ getStatusLabel(invoice.status) }}
                  </span>
                </td>
                
                <!-- Acciones Limpias y Profesionales -->
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center space-x-2">
                    <!-- Bot\u00f3n ESPECIAL para Cotizaciones: "Facturar en POS" (Verde Destacado) -->
                    <button v-if="invoice.type === 'Cotizaci\u00f3n'" 
                            @click="openInPos(invoice)" 
                            class="px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white text-xs font-bold rounded-lg shadow-sm hover:shadow-md transition-all duration-200 flex items-center space-x-1.5"
                            title="Abrir en POS para facturar">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                      </svg>
                      <span>Facturar en POS</span>
                    </button>
                    
                    <!-- Acciones Normales (GRIS NEUTRO - SIN colores) -->
                    <button @click="viewInvoice(invoice)" 
                            class="p-1.5 bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 rounded-lg transition-all duration-150 border border-slate-200 hover:border-slate-300"
                            title="Ver documento">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    
                    <button @click="viewAndPrintInvoice(invoice)" 
                            class="p-1.5 bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 rounded-lg transition-all duration-150 border border-slate-200 hover:border-slate-300"
                            :title="invoice.type === 'Cotizaci\u00f3n' ? 'Opciones de impresi\u00f3n' : 'Ver e imprimir factura'">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                      </svg>
                    </button>
                    
                    <!-- Men\u00fa de tres puntos para acciones secundarias (GRIS NEUTRO) -->
                    <div class="relative" @click.stop="">
                      <button @click="toggleActionsMenu(invoice.id)" 
                              class="p-1.5 bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 rounded-lg transition-all duration-150 border border-slate-200 hover:border-slate-300"
                              title="M\u00e1s acciones">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                      </button>
                      
                      <!-- Dropdown menu -->
                      <div v-if="activeMenuId === invoice.id" 
                           class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-slate-200 z-50 py-1">
                        <button @click="editInvoice(invoice); closeActionsMenu()" 
                                class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 flex items-center space-x-2">
                          <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                          </svg>
                          <span>Editar</span>
                        </button>
                        <button @click="confirmDeleteInvoice(); closeActionsMenu()" 
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center space-x-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                          <span>Anular</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
              
              <!-- Estado vacío empresarial -->
              <tr v-if="paginatedInvoices.length === 0 && filteredInvoices.length === 0">
                <td colspan="7" class="px-4 py-12 text-center">
                  <div class="flex flex-col items-center space-y-3">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-700">No hay documentos</p>
                      <p class="text-xs text-gray-500 mt-1">No se encontraron documentos para tu búsqueda</p>
                    </div>
                    <button @click="clearFilters" 
                            class="px-3 py-2 bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white text-sm font-medium rounded-lg">
                      Limpiar filtros
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Paginador -->
        <div v-if="filteredInvoices.length > 0" class="bg-white border-t border-slate-200 px-4 py-3 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <!-- Selector de elementos por página -->
            <div class="flex items-center space-x-2">
              <span class="text-xs font-medium text-slate-600">Mostrar:</span>
              <select v-model="itemsPerPage" 
                      @change="currentPage = 1"
                      class="border border-slate-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700 bg-slate-50">
                <option v-for="option in itemsPerPageOptions" :key="option" :value="option">
                  {{ option }}
                </option>
              </select>
              <span class="text-xs text-slate-600">por página</span>
            </div>
            
            <!-- Información de paginación -->
            <div class="text-xs text-slate-500 font-medium">
              Mostrando {{ paginationInfo.start }} a {{ paginationInfo.end }} de {{ paginationInfo.total }} documentos
            </div>
          </div>
          
          <!-- Controles de paginación -->
          <div class="flex items-center space-x-1">
            <!-- Botón Primera página -->
            <button @click="currentPage = 1" 
                    :disabled="currentPage === 1"
                    class="p-1.5 text-slate-500 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
              </svg>
            </button>
            
            <!-- Botón Anterior -->
            <button @click="currentPage--" 
                    :disabled="currentPage === 1"
                    class="p-1.5 text-slate-500 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            
            <!-- Números de página -->
            <div class="flex items-center space-x-1">
              <template v-for="page in totalPages" :key="page">
                <button v-if="page === 1 || page === totalPages || Math.abs(page - currentPage) <= 2"
                        @click="currentPage = page"
                        :class="[
                          'px-2.5 py-1 text-xs font-medium rounded-lg transition-colors',
                          page === currentPage 
                            ? 'bg-indigo-600 text-white border border-indigo-600 shadow-sm' 
                            : 'text-slate-600 bg-white border border-slate-200 hover:bg-slate-50'
                        ]">
                  {{ page }}
                </button>
                <span v-else-if="Math.abs(page - currentPage) === 3" class="px-1 text-slate-400 text-xs">...</span>
              </template>
            </div>
            
            <!-- Botón Siguiente -->
            <button @click="currentPage++" 
                    :disabled="currentPage === totalPages"
                    class="p-1.5 text-slate-500 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            
            <!-- Botón Última página -->
            <button @click="currentPage = totalPages" 
                    :disabled="currentPage === totalPages"
                    class="p-1.5 text-slate-500 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
      
    </div>
    
    <!-- Modal Nueva Factura Mejorado -->
    <div v-if="showNewInvoiceModal" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fadeIn">
      <div class="bg-white rounded-2xl p-8 max-w-lg w-full shadow-2xl shadow-slate-900/20 transform animate-slideUp border border-slate-200">
        
        <!-- Header del Modal Empresarial -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-slate-900">Nuevo Documento</h3>
              <p class="text-xs text-slate-500 font-medium">Crear factura o cotización</p>
            </div>
          </div>
          <button @click="showNewInvoiceModal = false" 
                  class="p-2 hover:bg-slate-100 rounded-xl transition-colors text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-3">Tipo de Documento</label>
            <div class="grid grid-cols-3 gap-3">
              <label class="relative cursor-pointer group">
                <input v-model="newInvoiceForm.type" 
                       type="radio" 
                       value="invoice" 
                       class="sr-only">
                <div :class="[
                  'p-4 border-2 rounded-2xl text-center transition-all duration-200',
                  newInvoiceForm.type === 'invoice' 
                    ? 'border-indigo-500 bg-indigo-50 text-indigo-700 shadow-sm' 
                    : 'border-slate-200 hover:border-indigo-200 text-slate-600 hover:bg-slate-50'
                ]">
                  <div class="text-2xl mb-1 transform group-hover:scale-110 transition-transform">🧾</div>
                  <div class="text-xs font-bold">Factura</div>
                </div>
              </label>
              <label class="relative cursor-pointer group">
                <input v-model="newInvoiceForm.type" 
                       type="radio" 
                       value="quote" 
                       class="sr-only">
                <div :class="[
                  'p-4 border-2 rounded-2xl text-center transition-all duration-200',
                  newInvoiceForm.type === 'quote' 
                    ? 'border-violet-500 bg-violet-50 text-violet-700 shadow-sm' 
                    : 'border-slate-200 hover:border-violet-200 text-slate-600 hover:bg-slate-50'
                ]">
                  <div class="text-2xl mb-1 transform group-hover:scale-110 transition-transform">💼</div>
                  <div class="text-xs font-bold">Cotización</div>
                </div>
              </label>
              <label class="relative cursor-pointer group">
                <input v-model="newInvoiceForm.type" 
                       type="radio" 
                       value="credit_note" 
                       class="sr-only">
                <div :class="[
                  'p-4 border-2 rounded-2xl text-center transition-all duration-200',
                  newInvoiceForm.type === 'credit_note' 
                    ? 'border-emerald-500 bg-emerald-50 text-emerald-700 shadow-sm' 
                    : 'border-slate-200 hover:border-emerald-200 text-slate-600 hover:bg-slate-50'
                ]">
                  <div class="text-2xl mb-1 transform group-hover:scale-110 transition-transform">📋</div>
                  <div class="text-xs font-bold">N. Crédito</div>
                </div>
              </label>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Cliente</label>
            <select v-model="newInvoiceForm.customer_id" 
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-all duration-300 text-slate-700 font-medium">
              <option value="">🔍 Seleccionar cliente</option>
              <option value="1">👤 María González</option>
              <option value="2">👤 Carlos Rodríguez</option>
              <option value="3">👤 Ana Martínez</option>
            </select>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">📅 Fecha</label>
              <input v-model="newInvoiceForm.date" 
                     type="date" 
                     class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-all duration-300 text-slate-700 font-medium">
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">⏰ Vencimiento</label>
              <input v-model="newInvoiceForm.due_date" 
                     type="date" 
                     class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-all duration-300 text-slate-700 font-medium">
            </div>
          </div>
        </div>
        
        <!-- Footer Empresarial -->
        <div class="bg-slate-50 border-t border-slate-200 px-4 py-4 flex justify-between -mx-8 -mb-8 mt-8 rounded-b-2xl">
          <div></div>
          <div class="flex gap-3">
            <button @click="showNewInvoiceModal = false" 
                    class="px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-600 text-sm font-bold rounded-xl border border-slate-200 shadow-sm transition-all duration-200">
              Cancelar
            </button>
            <button @click="createInvoice" 
                    class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/50 transition-all duration-300 transform hover:-translate-y-0.5 flex items-center space-x-2">
              <span>Crear Documento</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Visualización de Factura -->
  <div v-if="showViewModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fadeIn">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden border border-slate-200 animate-slideUp">
      
      <!-- Header -->
      <div class="bg-white border-b border-slate-200 p-5">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center shadow-sm">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-slate-900 tracking-tight">{{ getDocumentTypeLabel(selectedInvoice?.type) }} N° {{ selectedInvoice?.invoiceNumber || selectedInvoice?.number }}</h3>
              <span :class="[
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold mt-1 border',
                getStatusClass(selectedInvoice?.status)
              ]">
                {{ getStatusLabel(selectedInvoice?.status) }}
              </span>
            </div>
          </div>
          
          <button @click="showViewModal = false" 
                  class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
      
      <!-- Contenido Principal -->
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]" v-if="selectedInvoice">
        
        <!-- Información de Encabezado -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          
          <!-- Información del Documento -->
          <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
            <h4 class="text-xs font-black text-slate-400 mb-3 uppercase tracking-wider">Información del Documento</h4>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-slate-500 font-medium">Fecha:</span>
                <span class="font-bold text-slate-700">{{ formatDate(selectedInvoice.date) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500 font-medium">Hora:</span>
                <span class="font-bold text-slate-700">{{ formatTime(selectedInvoice.created_at || selectedInvoice.date) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500 font-medium">Vencimiento:</span>
                <span class="font-bold text-slate-700">{{ formatDate(selectedInvoice.due_date) }}</span>
              </div>
            </div>
          </div>
          
          <!-- Información del Cliente -->
          <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
            <h4 class="text-xs font-black text-slate-400 mb-3 uppercase tracking-wider">Cliente</h4>
            <div class="space-y-1 text-sm">
              <p class="font-bold text-slate-800 text-base">{{ selectedInvoice.customer_name || selectedInvoice.customer || 'Cliente General' }}</p>
              <p v-if="selectedInvoice.customer_email" class="text-slate-500 text-xs font-medium flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                {{ selectedInvoice.customer_email }}
              </p>
              <p v-if="selectedInvoice.customer_phone" class="text-slate-500 text-xs font-medium flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                {{ selectedInvoice.customer_phone }}
              </p>
            </div>
          </div>
          
          <!-- Resumen Financiero -->
          <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
            <h4 class="text-xs font-black text-slate-400 mb-3 uppercase tracking-wider">Totales</h4>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-slate-500 font-medium">Subtotal:</span>
                <span class="font-bold text-slate-700">${{ (selectedInvoice.subtotal || 0).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500 font-medium">Impuestos:</span>
                <span class="font-bold text-slate-700">${{ (selectedInvoice.tax_amount || selectedInvoice.tax || 0).toLocaleString() }}</span>
              </div>
              <div class="border-t border-slate-200 pt-2 mt-2">
                <div class="flex justify-between items-center">
                  <span class="font-black text-slate-900">Total:</span>
                  <span class="font-black text-indigo-600 text-lg">${{ (selectedInvoice.total || 0).toLocaleString() }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Tabla de Productos -->
        <div class="mb-6">
          <h4 class="text-base font-bold text-slate-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            Productos
          </h4>
          
          <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Producto</th>
                  <th class="text-center px-3 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Cant.</th>
                  <th class="text-right px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Precio</th>
                  <th class="text-right px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-if="!selectedInvoice.items || selectedInvoice.items.length === 0">
                  <td colspan="4" class="px-4 py-8 text-center">
                    <p class="text-sm text-slate-500 font-medium">No hay productos en esta factura</p>
                  </td>
                </tr>
                <tr v-else v-for="(item, index) in selectedInvoice.items" 
                    :key="`view-item-${index}-${item.product_id}`" 
                    class="hover:bg-slate-50/80 transition-colors">
                  <td class="px-4 py-3">
                    <div class="flex items-center space-x-3">
                      <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold">
                        {{ index + 1 }}
                      </span>
                      <span class="font-bold text-slate-700">{{ item.product_name || item.name }}</span>
                    </div>
                  </td>
                  <td class="px-3 py-3 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 bg-slate-100 rounded-md text-xs font-bold text-slate-700 border border-slate-200">
                      {{ item.quantity }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-right font-medium text-slate-600">
                    ${{ ((item.unit_price || item.price) || 0).toLocaleString() }}
                  </td>
                  <td class="px-4 py-3 text-right font-bold text-indigo-600">
                    ${{ (item.quantity * (item.unit_price || item.price) || item.subtotal || 0).toLocaleString() }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="flex gap-3 justify-end pt-5 border-t border-slate-200">
          <button @click="printInvoice(selectedInvoice)" 
                  class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-colors text-sm shadow-sm shadow-emerald-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Imprimir
          </button>
          <button @click="editInvoice(selectedInvoice)" 
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-colors text-sm shadow-sm shadow-indigo-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Editar
          </button>
          <button @click="showViewModal = false" 
                  class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl font-bold transition-colors text-sm">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Autenticación de Administrador - Compacto -->
  <div v-if="showAdminAuthModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fadeIn">
    <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full transform animate-slideUp">
      
      <!-- Header de Seguridad -->
      <div class="bg-gradient-to-r from-red-600 to-red-700 p-4 rounded-t-xl">
        <div class="flex items-center justify-center mb-2">
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
          </div>
        </div>
        <div class="text-center text-white">
          <h3 class="text-lg font-bold mb-1">Autorización Requerida</h3>
          <p class="text-red-100 text-sm">Se requieren credenciales de administrador</p>
        </div>
      </div>
      
      <!-- Contenido del Formulario -->
      <div class="p-6">
        <div class="space-y-4">
          
          <!-- Campo Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email del Administrador</label>
            <input v-model="adminCredentials.email" 
                   type="email" 
                   class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 focus:bg-white transition-all text-gray-900 text-sm"
                   placeholder="admin@empresa.com"
                   autocomplete="username">
          </div>
          
          <!-- Campo Contraseña -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
            <input v-model="adminCredentials.password" 
                   type="password" 
                   class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 focus:bg-white transition-all text-gray-900 text-sm"
                   placeholder="••••••••"
                   autocomplete="current-password">
          </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="flex space-x-3 mt-6">
          <button @click="cancelAdminAuth" 
                  class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors text-sm">
            Cancelar
          </button>
          <button @click="validateAdminAuth" 
                  class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors text-sm">
            Autorizar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Edición de Factura - Rediseñado -->
  <div v-if="showEditModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fadeIn">
    <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden border border-slate-200 animate-slideUp">
      
      <!-- Header con información clave -->
      <div class="bg-white border-b border-slate-200 px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center shadow-sm">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-slate-900 tracking-tight">{{ getDocumentTypeLabel(selectedInvoice?.type) }} N° {{ selectedInvoice?.invoiceNumber || selectedInvoice?.number }}</h3>
              <p class="text-xs font-medium text-slate-500 mt-0.5 flex items-center gap-2">
                <span class="font-bold text-slate-700">{{ selectedInvoice?.customer_name || 'Cliente General' }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span>{{ formatDate(selectedInvoice?.date) }}</span>
              </p>
            </div>
          </div>
          
          <button @click="showEditModal = false" 
                  class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
      
      <!-- Contenido Principal - Layout de 2 columnas -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6 overflow-y-auto max-h-[calc(90vh-140px)]" v-if="selectedInvoice">
        
        <!-- Columna Izquierda - Productos y Totales (2/3) -->
        <div class="lg:col-span-2 space-y-6">
          
          <!-- Productos -->
          <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="bg-slate-50 border-b border-slate-200 px-4 py-3 flex items-center gap-2">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
              <h4 class="text-sm font-bold text-slate-900">Productos</h4>
            </div>
            
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead class="bg-slate-50/50 border-b border-slate-200">
                  <tr>
                    <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">#</th>
                    <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Producto</th>
                    <th class="text-center px-3 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Cant.</th>
                    <th class="text-right px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Precio Unit.</th>
                    <th class="text-right px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-if="!selectedInvoice.items || selectedInvoice.items.length === 0">
                    <td colspan="5" class="px-4 py-8 text-center">
                      <p class="text-sm text-slate-500 font-medium">No hay productos</p>
                    </td>
                  </tr>
                  <tr v-else v-for="(item, index) in selectedInvoice.items" 
                      :key="`edit-item-${index}-${item.product_id}`" 
                      class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-4 py-3">
                      <span class="text-xs font-bold text-slate-400">{{ index + 1 }}</span>
                    </td>
                    <td class="px-4 py-3">
                      <p class="font-bold text-slate-700 text-sm">{{ item.product_name }}</p>
                    </td>
                    <td class="px-3 py-3 text-center">
                      <span class="inline-flex items-center px-2.5 py-0.5 bg-indigo-50 text-indigo-700 rounded-md text-xs font-bold border border-indigo-100">
                        {{ item.quantity }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-medium text-slate-600">
                      ${{ (item.price || 0).toLocaleString() }}
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-sm text-indigo-600">
                      ${{ ((item.subtotal || item.quantity * item.price) || 0).toLocaleString() }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <!-- Totales dentro de la tarjeta de productos -->
            <div class="border-t border-slate-200 bg-slate-50/50 px-6 py-4">
              <div class="flex justify-end">
                <div class="space-y-2 text-sm min-w-[280px]">
                  <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Subtotal:</span>
                    <span class="font-bold text-slate-700">${{ (selectedInvoice.subtotal || 0).toLocaleString() }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">IVA (0%):</span>
                    <span class="font-bold text-slate-700">${{ (selectedInvoice.tax || 0).toLocaleString() }}</span>
                  </div>
                  <div class="border-t border-slate-200 pt-3 mt-1">
                    <div class="flex justify-between items-center">
                      <span class="text-base font-black text-slate-900">Total:</span>
                      <span class="text-xl font-black text-indigo-600">${{ (selectedInvoice.total || 0).toLocaleString() }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
        </div>
        
        <!-- Columna Derecha - Información y Edición (1/3) -->
        <div class="space-y-6">
          
          <!-- Información del Cliente -->
          <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="bg-slate-50 border-b border-slate-200 px-4 py-3 flex items-center gap-2">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
              <h4 class="text-sm font-bold text-slate-900">Cliente</h4>
            </div>
            <div class="p-4 space-y-4">
              <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nombre</p>
                <p class="text-sm font-bold text-slate-800">{{ selectedInvoice.customer_name || selectedInvoice.customer || 'Cliente General' }}</p>
              </div>
              <div v-if="selectedInvoice.customer_document">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Documento</p>
                <p class="text-sm font-medium text-slate-600">{{ selectedInvoice.customer_document }}</p>
              </div>
              <div v-if="selectedInvoice.customer_phone">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Teléfono</p>
                <p class="text-sm font-medium text-slate-600 flex items-center gap-1">
                  <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                  {{ selectedInvoice.customer_phone }}
                </p>
              </div>
              <div v-if="selectedInvoice.customer_email">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email</p>
                <p class="text-sm font-medium text-slate-600 break-all flex items-center gap-1">
                  <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                  {{ selectedInvoice.customer_email }}
                </p>
              </div>
            </div>
          </div>
          
          <!-- Campos Editables -->
          <div class="bg-white rounded-2xl border border-indigo-100 shadow-sm overflow-hidden">
            <div class="bg-indigo-50/50 border-b border-indigo-100 px-4 py-3 flex items-center gap-2">
              <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
              <h4 class="text-sm font-bold text-indigo-900">Editar Documento</h4>
            </div>
            <div class="p-4 space-y-4">
              <!-- Estado -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Estado</label>
                <div class="relative">
                  <select v-model="selectedInvoice.status" 
                          class="w-full pl-3 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium text-slate-700 appearance-none transition-all">
                    <option value="draft">Borrador</option>
                    <option value="sent">Enviada</option>
                    <option value="paid">Pagada</option>
                    <option value="overdue">Vencida</option>
                    <option value="cancelled">Cancelada</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
                </div>
              </div>
              
              <!-- Fecha de Vencimiento -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Fecha Vencimiento</label>
                <input v-model="selectedInvoice.due_date" 
                       type="date" 
                       class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium text-slate-700 transition-all">
              </div>
              
              <!-- Notas -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Notas</label>
                <textarea v-model="selectedInvoice.notes" 
                          rows="3" 
                          class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium text-slate-700 resize-none transition-all"
                          placeholder="Observaciones..."></textarea>
              </div>
            </div>
          </div>
          
        </div>
        
      </div>
      
      <!-- Footer con botones de acción -->
      <div class="bg-white border-t border-slate-200 px-6 py-4 flex items-center justify-between">
        <button @click="confirmDeleteInvoice" 
                class="inline-flex items-center px-4 py-2.5 bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100 hover:border-rose-200 rounded-xl font-bold transition-all text-sm group">
          <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
          </svg>
          Eliminar
        </button>
        
        <div class="flex gap-3">
          <button @click="showEditModal = false" 
                  class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 rounded-xl font-bold transition-colors text-sm">
            Cancelar
          </button>
          <button @click="saveInvoiceChanges" 
                  class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all text-sm shadow-lg shadow-indigo-200 hover:shadow-indigo-300 hover:-translate-y-0.5">
            Guardar Cambios
          </button>
        </div>
      </div>
      
    </div>
  </div>

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

  <!-- Modal de Recibo para Facturas Normales -->
  <ReceiptModal
    v-if="showReceiptModal"
    :sale="posCompatibleSale"
    :system-settings="{}"
    @close="closeInvoiceModal"
    @new-sale="handleNewSale"
    @send-whatsapp="handleSendWhatsApp"
  />
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import { invoiceService } from '../services/invoiceService.js'
import { useToast } from '../composables/useToast.js'
import { useAuth } from '../store/auth.js'
import QuotationModal from './QuotationModal.vue'
import PhoneInputModal from './PhoneInputModal.vue'
import ReceiptModal from './ReceiptModal.vue'
import { whatsappService } from '../services/whatsappService.js'
import jsPDF from 'jspdf'
import QRCode from 'qrcode'
import html2canvas from 'html2canvas'
import { formatInvoiceDate } from '@/utils/dateFormatter.js'

// Props
const props = defineProps({
  invoices: {
    type: Array,
    default: () => []
  },
  customers: {
    type: Array,
    default: () => []
  },
  products: {
    type: Array,
    default: () => []
  },
  moduleName: {
    type: String,
    default: 'invoices'
  }
})

// Definir emits
const emit = defineEmits(['changeModule', 'open-quotation-in-pos', 'navigate'])

// Store de autenticación
const auth = useAuth()

// Estado reactivo
const searchTerm = ref('')
const statusFilter = ref('')
const typeFilter = ref('')
const dateFilter = ref('')
const showNewInvoiceModal = ref(false)
const loading = ref(false)
const isLoading = ref(false)

// Modales
const showViewModal = ref(false)
const showEditModal = ref(false)
const showAdminAuthModal = ref(false)
const showQuotationModal = ref(false)
const quotationModalType = ref('success')
const quotationData = ref(null)

// Modal de recibo para facturas normales
const showReceiptModal = ref(false)
const selectedInvoice = ref(null)

// Modal de teléfono para WhatsApp
const showPhoneModal = ref(false)
const phoneModalMessage = ref('')
const phoneModalResolve = ref(null)

// Variables para funciones
const adminAuthAction = ref('')
const adminCredentials = ref({
  email: '',
  password: ''
})

// Variables para el menú de acciones dropdown
const activeMenuId = ref(null)

// Toast notifications
const { showToast, showSuccess, showError } = useToast()

// Paginación
const currentPage = ref(1)
const itemsPerPage = ref(10)
const itemsPerPageOptions = [5, 10, 25, 50, 100]

// Formulario nueva factura
const newInvoiceForm = ref({
  type: 'invoice',
  customer_id: '',
  date: new Date().toISOString().split('T')[0],
  due_date: ''
})

// Datos locales - ahora se usan los props principalmente
const localInvoices = ref([])
const allInvoices = ref([])
const stats = ref({
  monthly_invoices: 0,
  total_invoiced: 0,
  pending_invoices: 0,
  quotations: 0
})

// Computed properties
const filteredInvoices = computed(() => {
  let filtered = props.invoices

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(invoice => 
      invoice.invoiceNumber?.toLowerCase().includes(term) ||
      invoice.customer?.toLowerCase().includes(term) ||
      invoice.total?.toString().includes(term)
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(invoice => invoice.status === statusFilter.value)
  }

  if (typeFilter.value) {
    filtered = filtered.filter(invoice => invoice.type === typeFilter.value)
  }

  if (dateFilter.value) {
    filtered = filtered.filter(invoice => invoice.date === dateFilter.value)
  }

  return filtered.sort((a, b) => new Date(b.date) - new Date(a.date))
})

// Computed properties para paginación
const totalPages = computed(() => Math.ceil(filteredInvoices.value.length / itemsPerPage.value))

const paginatedInvoices = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredInvoices.value.slice(start, end)
})

const paginationInfo = computed(() => {
  const start = Math.min((currentPage.value - 1) * itemsPerPage.value + 1, filteredInvoices.value.length)
  const end = Math.min(currentPage.value * itemsPerPage.value, filteredInvoices.value.length)
  return {
    start,
    end,
    total: filteredInvoices.value.length
  }
})

// Transformar datos de factura al formato compatible con ReceiptModal del POS
const posCompatibleSale = computed(() => {
  if (!selectedInvoice.value) return {}
  
  console.log('📋 Transformando factura al formato POS:', selectedInvoice.value)
  console.log('📅 Fecha original:', selectedInvoice.value.date || selectedInvoice.value.created_at)
  
  // Asegurar que la fecha esté en formato correcto
  let invoiceDate = selectedInvoice.value.date || selectedInvoice.value.created_at
  
  // Si no hay fecha, usar la fecha actual
  if (!invoiceDate) {
    invoiceDate = new Date().toISOString()
  } else {
    // Convertir a string si es un objeto Date
    if (typeof invoiceDate !== 'string') {
      invoiceDate = invoiceDate.toString()
    }
    
    // Si es solo fecha (YYYY-MM-DD), agregar hora
    if (invoiceDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
      invoiceDate = invoiceDate + 'T12:00:00.000Z'
    }
  }
  
  console.log('📅 Fecha procesada:', invoiceDate)
  
  // Verificar formato de fecha para debug
  console.log('📅 Probando formatInvoiceDate:', formatInvoiceDate(invoiceDate))

  // Parsear items de la factura
  let items = []
  try {
    if (selectedInvoice.value.items) {
      items = typeof selectedInvoice.value.items === 'string' 
        ? JSON.parse(selectedInvoice.value.items)
        : selectedInvoice.value.items
    }
  } catch (error) {
    console.error('Error parseando items de factura:', error)
    items = []
  }

  // Transformar items al formato POS
  const posItems = items.map((item, index) => ({
    id: item.id || item.product_id || index,
    name: item.name || item.product_name || `Producto ${index + 1}`,
    quantity: parseFloat(item.quantity || 1),
    price: parseFloat(item.price || item.unit_price || 0),
    subtotal: parseFloat(item.subtotal || (item.quantity * (item.price || item.unit_price)) || 0)
  }))

  console.log('📦 Items transformados para POS:', posItems)

  // Procesar fecha de vencimiento también
  let dueDateProcessed = selectedInvoice.value.due_date
  if (dueDateProcessed && dueDateProcessed.match(/^\d{4}-\d{2}-\d{2}$/)) {
    dueDateProcessed = dueDateProcessed + 'T12:00:00.000Z'
  }

  // Crear estructura compatible con ReceiptModal del POS
  const posCompatible = {
    invoiceNumber: selectedInvoice.value.number || selectedInvoice.value.invoice_number || selectedInvoice.value.invoiceNumber || `FV-${selectedInvoice.value.id}`,
    date: invoiceDate,
    due_date: dueDateProcessed,
    cashier: selectedInvoice.value.seller_name || selectedInvoice.value.cashier || 'Vendedor',
    customer: selectedInvoice.value.customer_name || selectedInvoice.value.customer || 'Cliente General',
    items: posItems,
    subtotal: parseFloat(selectedInvoice.value.subtotal || 0),
    discount: parseFloat(selectedInvoice.value.discount_amount || selectedInvoice.value.discount || 0),
    tax: parseFloat(selectedInvoice.value.tax_amount || selectedInvoice.value.tax || 0),
    taxRate: parseFloat(selectedInvoice.value.tax_rate || 19),
    total: parseFloat(selectedInvoice.value.total || 0),
    totalWithFees: parseFloat(selectedInvoice.value.total || 0),
    change: parseFloat(selectedInvoice.value.change_given || selectedInvoice.value.change || 0),
    payments: [{
      method: selectedInvoice.value.payment_method || 'efectivo',
      methodName: getPaymentMethodName(selectedInvoice.value.payment_method || 'efectivo'),
      amount: parseFloat(selectedInvoice.value.total || 0),
      fee: 0
    }],
    appliedDiscount: selectedInvoice.value.discount_amount > 0 ? {
      name: `Descuento ${selectedInvoice.value.discount_percentage || 0}%`,
      amount: parseFloat(selectedInvoice.value.discount_amount || 0)
    } : null
  }

  console.log('✅ Datos compatibles con POS generados:', posCompatible)
  
  return posCompatible
})

// Estadísticas basadas en las facturas del POS
const monthlyInvoices = computed(() => {
  const thisMonth = new Date().getMonth() // Noviembre = 10
  const thisYear = new Date().getFullYear() // 2025
  
  
  const filteredInvoices = props.invoices.filter(invoice => {
    const invoiceDate = new Date(invoice.date)
    const invoiceMonth = invoiceDate.getMonth()
    const invoiceYear = invoiceDate.getFullYear()
    const isCurrentMonth = invoiceMonth === thisMonth && invoiceYear === thisYear
    
    if (props.invoices.indexOf(invoice) < 5) { // Log solo las primeras 5
    }
    
    return isCurrentMonth
  })
  
  return filteredInvoices.length
})

const totalInvoiced = computed(() => {
  const thisMonth = new Date().getMonth()
  const thisYear = new Date().getFullYear()
  
  const filteredInvoices = props.invoices.filter(invoice => {
    const invoiceDate = new Date(invoice.date)
    return invoiceDate.getMonth() === thisMonth && invoiceDate.getFullYear() === thisYear
  })
  
  const total = filteredInvoices.reduce((sum, invoice) => sum + (invoice.total || 0), 0)
  
  
  return total
})

const pendingInvoices = computed(() => {
  return props.invoices.filter(invoice => 
    invoice.status?.toLowerCase() === 'pendiente' || 
    invoice.status?.toLowerCase() === 'pending'
  ).length
})

const quotations = computed(() => {
  return props.invoices.filter(invoice => 
    invoice.type?.toLowerCase() === 'cotización' || 
    invoice.type?.toLowerCase() === 'quote'
  ).length
})

// Funciones para cargar datos
const loadInvoices = async () => {
  try {
    loading.value = true
    const response = await invoiceService.getInvoices()
    allInvoices.value = response.data
  } catch (error) {
    console.error('Error al cargar facturas:', error)
    showToast('Error al cargar facturas', 'error')
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    const response = await invoiceService.getStats()
    stats.value = response.data
  } catch (error) {
    console.error('Error al cargar estadísticas:', error)
    showToast('Error al cargar estadísticas', 'error')
  }
}

// Función para obtener el vendedor actual
const getCurrentSeller = () => {
  try {
    const user = auth.user.value
    if (user && user.name) {
      return user.name
    }
    return 'Vendedor'
  } catch (error) {
    console.error('Error getting current seller:', error)
    return 'Vendedor'
  }
}

// Métodos
const formatDate = (dateString) => {
  if (!dateString) return '-'
  
  try {
    // Asegurarse de que la fecha se procese en la zona horaria local
    const [year, month, day] = dateString.split('-').map(num => parseInt(num, 10))
    return `${day.toString().padStart(2, '0')}/${month.toString().padStart(2, '0')}/${year}`
  } catch (error) {
    console.error('Error formatting date:', error, dateString)
    return '-'
  }
}

const formatTime = (dateString) => {
  if (!dateString) return '-'
  
  try {
    const date = new Date(dateString)
    // Verificar si la fecha es válida
    if (isNaN(date.getTime())) {
      return '-'
    }
    
    // Formatear hora en formato 24h (HH:mm)
    const hours = date.getHours().toString().padStart(2, '0')
    const minutes = date.getMinutes().toString().padStart(2, '0')
    return `${hours}:${minutes}`
  } catch (error) {
    console.error('Error formatting time:', error, dateString)
    return '-'
  }
}

const getDocumentTypeLabel = (type) => {
  const labels = {
    invoice: 'Factura',
    quote: 'Cotización',
    credit_note: 'Nota Crédito'
  }
  return labels[type] || type
}

const getStatusLabel = (status) => {
  const labels = {
    draft: 'Borrador',
    sent: 'Enviada', 
    paid: 'Pagada',
    overdue: 'Vencida',
    cancelled: 'Cancelada',
    'Pendiente': 'Pendiente',
    'Pagada': 'Pagada'
  }
  return labels[status] || 'Pendiente'
}

// Función para obtener nombre del método de pago (igual que en POS)
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

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-slate-100 text-slate-700',
    sent: 'bg-blue-100 text-blue-700',
    paid: 'bg-emerald-100 text-emerald-700', 
    overdue: 'bg-rose-100 text-rose-700',
    cancelled: 'bg-slate-100 text-slate-600',
    'Pendiente': 'bg-amber-100 text-amber-700',
    'Pagada': 'bg-emerald-100 text-emerald-700'
  }
  return classes[status] || 'bg-slate-100 text-slate-600'
}

const getStatusDotClass = (status) => {
  const classes = {
    draft: 'bg-slate-400',
    sent: 'bg-blue-500',
    paid: 'bg-emerald-500', 
    overdue: 'bg-rose-500',
    cancelled: 'bg-slate-400',
    'Pendiente': 'bg-amber-500',
    'Pagada': 'bg-emerald-500'
  }
  return classes[status] || 'bg-slate-400'
}

const clearFilters = () => {
  searchTerm.value = ''
  statusFilter.value = ''
  typeFilter.value = ''
  dateFilter.value = ''
  currentPage.value = 1
}

const viewInvoice = async (invoice) => {
  try {
    // Obtener los detalles completos de la factura desde el backend
    const fullInvoice = await invoiceService.getInvoice(invoice.id)
    selectedInvoice.value = fullInvoice
    showViewModal.value = true
  } catch (error) {
    console.error('Error al obtener detalles de la factura:', error)
    showError('Error al cargar los detalles de la factura')
  }
}

const editInvoice = async (invoice) => {
  try {
    // Obtener los detalles completos de la factura desde el backend
    const fullInvoice = await invoiceService.getInvoice(invoice.id)
    
    // Verificar si el usuario actual es admin usando el store
    const userRole = auth.userRole.value
    
    if (userRole === 'Administrador') {
      // Si es admin, permitir editar directamente
      selectedInvoice.value = fullInvoice
      showEditModal.value = true
    } else {
      // Si no es admin, solicitar credenciales de administrador
      selectedInvoice.value = fullInvoice
      showAdminAuthModal.value = true
      adminAuthAction.value = 'edit'
    }
  } catch (error) {
    console.error('Error al obtener detalles de la factura para edición:', error)
    showError('Error al cargar los detalles de la factura para edición')
  }
}

// Función para confirmar eliminación desde el menú dropdown
const confirmDeleteInvoice = (invoice) => {
  if (invoice) {
    selectedInvoice.value = invoice
  }
  
  // Verificar si el usuario es admin
  const userRole = auth.userRole.value
  
  if (userRole === 'Administrador') {
    // Si es admin, mostrar confirmación directa
    if (confirm('¿Está seguro de que desea anular este documento?')) {
      deleteInvoiceDirectly()
    }
  } else {
    // Si no es admin, solicitar credenciales de administrador
    showAdminAuthModal.value = true
    adminAuthAction.value = 'delete'
  }
}

// Función para eliminar factura sin autenticación (cuando ya es admin)
const deleteInvoiceDirectly = async () => {
  if (!selectedInvoice.value) return

  try {
    isLoading.value = true
    
    // Como es admin, no necesita validación adicional
    const result = await invoiceService.deleteInvoice(selectedInvoice.value.id)

    if (result.success) {
      showSuccess('Documento anulado exitosamente')
      await loadInvoices()
      selectedInvoice.value = null
    } else {
      showError(result.message || 'Error al anular el documento')
    }
  } catch (error) {
    console.error('Error eliminando factura:', error)
    showError('Error al anular el documento')
  } finally {
    isLoading.value = false
  }
}

const printInvoice = async (invoice) => {
  try {
    isLoading.value = true
    
    // Verificar si es una cotización
    const isQuotation = invoice.type === 'Cotización' || 
                       invoice.type === 'quote' || 
                       invoice.type?.toLowerCase() === 'cotización'
    
    if (isQuotation) {
      // Si es cotización, obtener información completa desde el backend
      console.log('📋 Detected quotation, obtaining complete information...')
      console.log('📋 Invoice data from list:', invoice)
      
      try {
        // Obtener información completa de la cotización desde el backend
        const fullInvoice = await invoiceService.getInvoice(invoice.id)
        
        console.log('📋 Full invoice information from backend:', fullInvoice)
        
        // Preparar datos de la cotización para el modal con información completa
        quotationData.value = {
          id: fullInvoice.id,
          code: fullInvoice.number || fullInvoice.invoiceNumber,
          total: fullInvoice.total || 0,
          subtotal: fullInvoice.subtotal || fullInvoice.total || 0,
          discount_amount: fullInvoice.discount_amount || 0,
          customer: fullInvoice.customer_name || fullInvoice.customer || 'Cliente General',
          customer_phone: fullInvoice.customer_phone || null,
          customer_email: fullInvoice.customer_email || null,
          customer_id: fullInvoice.customer_id || null,
          items: fullInvoice.items || [],
          created_at: fullInvoice.date || fullInvoice.created_at
        }
        
        console.log('📋 Prepared quotation data for modal:', quotationData.value)
        console.log('📋 Customer phone extracted:', quotationData.value.customer_phone)
        console.log('📋 Quotation code for QR:', quotationData.value.code)
        console.log('📋 Items for display:', quotationData.value.items)
        
        quotationModalType.value = 'success'
        showQuotationModal.value = true
        
      } catch (error) {
        console.error('Error obteniendo información completa de cotización:', error)
        // Si falla, usar la información básica disponible
        quotationData.value = {
          id: invoice.id,
          code: invoice.number || invoice.invoiceNumber,
          total: invoice.total || 0,
          subtotal: invoice.subtotal || invoice.total || 0,
          discount_amount: invoice.discount_amount || 0,
          customer: invoice.customer_name || invoice.customer || 'Cliente General',
          customer_phone: invoice.customer_phone || null,
          customer_email: invoice.customer_email || null,
          customer_id: invoice.customer_id || null,
          items: invoice.items || [],
          created_at: invoice.date
        }
        
        console.log('📋 Using basic quotation data (fallback):', quotationData.value)
        
        quotationModalType.value = 'success'
        showQuotationModal.value = true
      }
      
    } else {
      // Si es factura normal, también preparar datos para mostrar opciones de impresión/WhatsApp
      console.log('📋 Detected normal invoice, preparing data for print options...')
      console.log('📋 Invoice data from list:', invoice)
      
      try {
        // Obtener información completa de la factura desde el backend
        const fullInvoice = await invoiceService.getInvoice(invoice.id)
        
        console.log('📋 Full invoice information from backend:', fullInvoice)
        
        // Preparar datos de la factura para el modal de recibo con información completa
        selectedInvoice.value = {
          id: fullInvoice.id || invoice.id,
          invoice_number: fullInvoice.number || fullInvoice.invoice_number || fullInvoice.invoiceNumber || `FV-${String(fullInvoice.id || invoice.id).padStart(6, '0')}`,
          total: fullInvoice.total || 0,
          subtotal: fullInvoice.subtotal || fullInvoice.total || 0,
          discount_amount: fullInvoice.discount_amount || fullInvoice.discount || 0,
          discount_percentage: fullInvoice.discount_percentage || 0,
          tax_amount: fullInvoice.tax_amount || fullInvoice.tax || 0,
          commission_amount: fullInvoice.commission_amount || 0,
          customer_name: fullInvoice.customer_name || fullInvoice.customer || 'Cliente General',
          customer_phone: fullInvoice.customer_phone || null,
          customer_email: fullInvoice.customer_email || null,
          customer_id: fullInvoice.customer_id || null,
          seller_name: fullInvoice.seller_name || fullInvoice.seller || fullInvoice.created_by || 'Vendedor',
          created_at: fullInvoice.created_at || new Date().toISOString(),
          items: fullInvoice.items || [],
          payment_method: fullInvoice.payment_method || 'efectivo',
          cash_received: fullInvoice.cash_received || fullInvoice.total || 0,
          change_given: fullInvoice.change_given || 0,
          type: fullInvoice.type || 'Factura'
        }
        
        console.log('📋 Prepared invoice data for receipt modal:', selectedInvoice.value)
        console.log('📋 Customer phone extracted:', selectedInvoice.value.customer_phone)
        console.log('📋 Invoice number for QR:', selectedInvoice.value.invoice_number)
        console.log('📋 Seller information:', selectedInvoice.value.seller_name)
        
        // Mostrar modal de recibo para facturas normales
        showReceiptModal.value = true
        
      } catch (error) {
        console.error('Error obteniendo información completa de factura:', error)
        // Si falla, usar la información básica disponible
        selectedInvoice.value = {
          id: invoice.id,
          invoice_number: invoice.number || invoice.invoice_number || invoice.invoiceNumber || `FV-${String(invoice.id).padStart(6, '0')}`,
          total: invoice.total || 0,
          subtotal: invoice.subtotal || invoice.total || 0,
          discount_amount: invoice.discount_amount || invoice.discount || 0,
          discount_percentage: invoice.discount_percentage || 0,
          tax_amount: invoice.tax_amount || invoice.tax || 0,
          commission_amount: invoice.commission_amount || 0,
          customer_name: invoice.customer_name || invoice.customer || 'Cliente General',
          customer_phone: invoice.customer_phone || null,
          customer_email: invoice.customer_email || null,
          customer_id: invoice.customer_id || null,
          seller_name: invoice.seller_name || invoice.seller || invoice.created_by || 'Vendedor',
          created_at: invoice.created_at || new Date().toISOString(),
          items: invoice.items || [],
          payment_method: invoice.payment_method || 'efectivo',
          cash_received: invoice.cash_received || invoice.total || 0,
          change_given: invoice.change_given || 0,
          type: invoice.type || 'Factura'
        }
        
        console.log('📋 Using basic invoice data (fallback):', selectedInvoice.value)
        
        // Mostrar modal de recibo para facturas normales
        showReceiptModal.value = true
      }
    }
    
  } catch (error) {
    console.error('Error generando PDF:', error)
    showError('Error al generar el PDF de la factura')
  } finally {
    isLoading.value = false
  }
}

// Nueva función que combina ver factura con opciones de impresión/WhatsApp
const viewAndPrintInvoice = async (invoice) => {
  try {
    // Verificar si es una cotización
    const isQuotation = invoice.type === 'Cotización' || 
                       invoice.type === 'quote' || 
                       invoice.type?.toLowerCase() === 'cotización'
    
    if (isQuotation) {
      // Para cotizaciones, usar la función original con modal de opciones
      await printInvoice(invoice)
    } else {
      // Para facturas normales, abrir directamente el nuevo InvoiceReceiptModal
      console.log('📋 Opening InvoiceReceiptModal for normal invoice:', invoice)
      
      // Obtener información completa de la factura desde el backend si es necesario
      let fullInvoice = invoice
      try {
        fullInvoice = await invoiceService.getInvoice(invoice.id)
      } catch (error) {
        console.warn('Could not fetch full invoice details, using provided data:', error)
      }
      
      // Usar directamente los datos de la factura para el modal
      selectedInvoice.value = {
        id: fullInvoice.id || invoice.id,
        invoice_number: fullInvoice.number || fullInvoice.invoice_number || fullInvoice.invoiceNumber || `FV-${String(fullInvoice.id || invoice.id).padStart(6, '0')}`,
        total: fullInvoice.total || 0,
        subtotal: fullInvoice.subtotal || fullInvoice.total || 0,
        discount_amount: fullInvoice.discount_amount || fullInvoice.discount || 0,
        discount_percentage: fullInvoice.discount_percentage || 0,
        tax_amount: fullInvoice.tax_amount || fullInvoice.tax || 0,
        commission_amount: fullInvoice.commission_amount || 0,
        customer_name: fullInvoice.customer_name || fullInvoice.customer || 'Cliente General',
        customer_phone: fullInvoice.customer_phone || null,
        customer_email: fullInvoice.customer_email || null,
        customer_id: fullInvoice.customer_id || null,
        seller_name: fullInvoice.seller_name || fullInvoice.seller || fullInvoice.created_by || 'Vendedor',
        created_at: fullInvoice.created_at || new Date().toISOString(),
        items: fullInvoice.items || [],
        payment_method: fullInvoice.payment_method || 'efectivo',
        cash_received: fullInvoice.cash_received || fullInvoice.total || 0,
        change_given: fullInvoice.change_given || 0,
        type: fullInvoice.type || 'Factura'
      }
      
      // Mostrar modal de recibo con funcionalidad completa de WhatsApp automático
      showReceiptModal.value = true
    }
    
  } catch (error) {
    console.error('Error opening invoice view:', error)
    showError('Error al abrir la vista de factura')
  }
}

const createInvoice = () => {
  console.log('Crear nueva factura:', newInvoiceForm.value)
  showNewInvoiceModal.value = false
}

// Funciones para manejar el modal de cotización
const handleCloseQuotationModal = () => {
  showQuotationModal.value = false
  quotationData.value = null
  quotationModalType.value = 'success'
}

const handlePrintQuotation = async () => {
  try {
    console.log('📋 Imprimiendo cotización desde gestión de facturas:', quotationData.value)
    
    if (!quotationData.value) {
      showError('No hay datos de cotización para imprimir')
      return
    }

    // Generar PDF de la cotización usando la misma función del POS
    const pdfBlob = await generateQuotationPDFBlob(quotationData.value)
    
    // Crear URL del blob y abrir para imprimir
    const pdfUrl = window.URL.createObjectURL(pdfBlob)
    const printWindow = window.open(pdfUrl)
    
    printWindow.onload = () => {
      printWindow.print()
      // Limpiar URL después de imprimir
      setTimeout(() => window.URL.revokeObjectURL(pdfUrl), 1000)
    }
    
    showSuccess('PDF de cotización generado exitosamente')
    handleCloseQuotationModal()
    
  } catch (error) {
    console.error('❌ Error al imprimir cotización:', error)
    showError('Error al generar el PDF de la cotización')
  }
}

const handleSendQuotationWhatsApp = async () => {
  try {
    console.log('📱 Enviando cotización por WhatsApp desde gestión de facturas:', quotationData.value)
    
    if (!quotationData.value) {
      showError('No hay datos de cotización para enviar')
      return
    }

    // ==========================================
    // VALIDACIÓN DE TELÉFONO MEJORADA
    // ==========================================
    
    let phoneNumber = null
    let cleanPhone = null
    const customerName = quotationData.value.customer || 'Cliente'
    const customerPhone = quotationData.value.customer_phone
    
    console.log('📱 Validando teléfono del cliente:', { customerName, customerPhone })
    
    if (customerName === 'Cliente General' || customerName.toLowerCase().includes('general')) {
      // Cliente General - solicitar teléfono
      phoneNumber = await promptForPhoneNumber('Este es un Cliente General. Ingrese el número de WhatsApp:')
      if (!phoneNumber) {
        console.log('📱 Envío cancelado por el usuario')
        return
      }
      cleanPhone = cleanPhoneNumber(phoneNumber)
      if (!isValidPhoneNumber(cleanPhone)) {
        showError('Número de teléfono inválido. Use formato: +57 300 1234567 o 3001234567')
        return
      }
    } else if (customerPhone && customerPhone.trim() !== '') {
      // Cliente con teléfono registrado - validar
      cleanPhone = cleanPhoneNumber(customerPhone)
      
      if (isValidPhoneNumber(cleanPhone)) {
        // Teléfono válido - usar automáticamente (sin alerta)
        console.log(`📱 Enviando a: ${customerName} (${cleanPhone})`)
      } else {
        // Teléfono inválido - solicitar uno nuevo
        phoneNumber = await promptForPhoneNumber(`El teléfono del cliente "${customerName}" (${customerPhone}) no es válido. Ingrese un número correcto:`)
        if (!phoneNumber) {
          console.log('📱 Envío cancelado por el usuario')
          return
        }
        cleanPhone = cleanPhoneNumber(phoneNumber)
        if (!isValidPhoneNumber(cleanPhone)) {
          showError('Número de teléfono inválido. Use formato: +57 300 1234567 o 3001234567')
          return
        }
      }
    } else {
      // No tiene teléfono registrado - solicitarlo
      phoneNumber = await promptForPhoneNumber(`El cliente "${customerName}" no tiene teléfono registrado. Ingrese el número de WhatsApp:`)
      if (!phoneNumber) {
        console.log('📱 Envío cancelado por el usuario')
        return
      }
      cleanPhone = cleanPhoneNumber(phoneNumber)
      if (!isValidPhoneNumber(cleanPhone)) {
        showError('Número de teléfono inválido. Use formato: +57 300 1234567 o 3001234567')
        return
      }
    }

    // Actualizar el teléfono en los datos de la cotización
    quotationData.value.customer_phone = cleanPhone
    
    console.log('📱 Teléfono validado correctamente:', cleanPhone)

    // Generar PDF usando ReceiptModal (método idéntico al POS)
    const pdfBlob = await generatePDFFromReceiptModal()
    
    // Enviar por WhatsApp usando la función simplificada
    const result = await whatsappService.sendQuotationFromData(quotationData.value, pdfBlob)
    
    if (result.success) {
      // Mensaje toast más informativo
      showSuccess(`📱 Cotización ${quotationData.value.code} enviada a ${customerName} (${cleanPhone})`)
      handleCloseQuotationModal()
    } else {
      showError(result.message || 'Error al enviar por WhatsApp')
    }
    
  } catch (error) {
    console.error('❌ Error al enviar cotización por WhatsApp:', error)
    showError('Error al enviar la cotización por WhatsApp')
  }
}

// Función para generar PDF idéntico al POS capturando ReceiptModal
const generatePDFFromReceiptModal = async () => {
  try {
    console.log('🧾 Iniciando generación de PDF desde ReceiptModal...')
    
    // Verificar que tenemos datos de factura
    if (!selectedInvoice.value) {
      throw new Error('No hay datos de factura para generar el PDF')
    }

    console.log('📊 Datos de la factura para PDF:', selectedInvoice.value)

    // Mostrar el recibo temporalmente si no está visible
    const wasReceiptVisible = showReceiptModal.value
    if (!wasReceiptVisible) {
      showReceiptModal.value = true
      await nextTick()
      await new Promise(resolve => setTimeout(resolve, 800)) // Esperar renderizado completo
    }

    // Obtener el elemento del recibo visible
    const receiptElement = document.getElementById('receipt-content')
    if (!receiptElement) {
      throw new Error('No se encontró el recibo en pantalla con ID receipt-content')
    }

    console.log('📋 Elemento de recibo encontrado, creando captura...')

    // Crear canvas del recibo usando html2canvas (configuración idéntica al POS)
    const canvas = await html2canvas(receiptElement, {
      scale: 2,
      useCORS: true,
      backgroundColor: '#ffffff',
      width: receiptElement.scrollWidth,
      height: receiptElement.scrollHeight,
      logging: false
    })

    console.log('🖼️ Canvas creado exitosamente')

    // Crear PDF con jsPDF (configuración idéntica al POS)
    const imgWidth = 80 // Ancho del ticket en mm
    const imgHeight = (canvas.height * imgWidth) / canvas.width

    const pdf = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: [imgWidth, Math.max(imgHeight, 100)]
    })

    const imgData = canvas.toDataURL('image/png', 1.0)
    pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight, '', 'FAST')

    console.log('📄 PDF generado exitosamente')

    // Ocultar el recibo si no estaba visible antes
    if (!wasReceiptVisible) {
      showReceiptModal.value = false
    }

    // Convertir a blob
    return pdf.output('blob')

  } catch (error) {
    console.error('❌ Error generando PDF desde ReceiptModal:', error)
    throw new Error('No se pudo generar el PDF de la factura: ' + error.message)
  }
}
const generateQuotationPDFBlob = async (quotationData) => {
  try {
    console.log('📋 Generando PDF de factura desde ReceiptModal (idéntico al POS)...')
    
    // Verificar que el ReceiptModal esté visible
    if (!showReceiptModal.value) {
      console.log('ReceiptModal no visible, mostrándolo temporalmente...')
      showReceiptModal.value = true
      await nextTick()
      await new Promise(resolve => setTimeout(resolve, 500)) // Esperar renderizado completo
    }

    // Obtener el elemento del recibo visible (igual que POS)
    const receiptElement = document.getElementById('receipt-content')
    if (!receiptElement) {
      throw new Error('No se encontró el elemento del recibo en pantalla')
    }

    console.log('📸 Capturando recibo con html2canvas...')
    
    // Crear canvas del recibo usando html2canvas (método idéntico al POS)
    const canvas = await html2canvas(receiptElement, {
      scale: 2,
      useCORS: true,
      backgroundColor: '#ffffff',
      width: receiptElement.scrollWidth,
      height: receiptElement.scrollHeight,
      logging: false
    })

    console.log('📄 Creando PDF con jsPDF (formato idéntico al POS)...')
    
    // Crear PDF con jsPDF (configuración idéntica al POS)
    const imgWidth = 80 // Ancho del ticket en mm (igual que POS)
    const imgHeight = (canvas.height * imgWidth) / canvas.width

    const pdf = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: [imgWidth, Math.max(imgHeight, 100)]
    })

    const imgData = canvas.toDataURL('image/png', 1.0)
    pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight, '', 'FAST')

    // Ocultar el recibo si no estaba visible antes (igual que POS)
    if (!wasReceiptVisible) {
      showReceiptModal.value = false
    }
    
    // Convertir a blob (igual que POS)
    return pdf.output('blob')

  } catch (error) {
    console.error('❌ Error generando PDF desde ReceiptModal:', error)
    throw new Error('No se pudo generar el PDF: ' + error.message)
  }
}

const redirectToPos = () => {
  emit('changeModule', 'pos')
}

const openInPos = (invoice) => {
  console.log('Abriendo cotización en POS:', invoice.invoiceNumber || invoice.invoice_number)
  
  // Emitir evento para abrir cotización en POS
  emit('open-quotation-in-pos', {
    code: invoice.invoiceNumber || invoice.invoice_number,
    data: invoice
  })
  
  // Cambiar al módulo POS
  emit('changeModule', 'pos')
}

const generateReport = () => {
  // Navegar al módulo de reportes
  emit('navigate', 'reports')
}

// Funciones para el menú dropdown de acciones
const toggleActionsMenu = (invoiceId) => {
  activeMenuId.value = activeMenuId.value === invoiceId ? null : invoiceId
}

const closeActionsMenu = () => {
  activeMenuId.value = null
}

// Cerrar menú cuando se hace clic fuera
onMounted(() => {
  document.addEventListener('click', closeActionsMenu)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', closeActionsMenu)
})

// Configurar token de autenticación
const setupAuth = () => {
  const authToken = '2|9bqn3alwfSdQZVAYFp10z4RqcFHUcS6X8IiFMIJDb632dab8'
  
  if (!localStorage.getItem('auth_token')) {
    localStorage.setItem('auth_token', authToken)
  }
}

// Watchers para resetear paginación cuando cambien los filtros
watch([searchTerm, statusFilter, typeFilter, dateFilter], () => {
  currentPage.value = 1
})

// Funciones de autenticación de administrador
const cancelAdminAuth = () => {
  showAdminAuthModal.value = false
  adminCredentials.value = { email: '', password: '' }
  adminAuthAction.value = ''
  selectedInvoice.value = null
}

const validateAdminAuth = async () => {
  if (!adminCredentials.value.email || !adminCredentials.value.password) {
    showError('Por favor complete todos los campos')
    return
  }

  try {
    // Validar credenciales con el servicio de autenticación
    const isValid = await auth.validateAdminCredentials(
      adminCredentials.value.email, 
      adminCredentials.value.password
    )
    
    if (!isValid) {
      showError('Credenciales de administrador incorrectas')
      return
    }
    
    if (adminAuthAction.value === 'edit') {
      showAdminAuthModal.value = false
      showEditModal.value = true
    } else if (adminAuthAction.value === 'delete') {
      await deleteInvoice()
    }
    
    adminCredentials.value = { email: '', password: '' }
  } catch (error) {
    console.error('Error validando credenciales:', error)
    showError('Error validando credenciales de administrador')
  }
}

// Función para eliminar factura con validación de admin
const deleteInvoice = async () => {
  if (!selectedInvoice.value) return

  try {
    isLoading.value = true
    
    const result = await invoiceService.deleteInvoice(selectedInvoice.value.id, {
      admin_email: adminCredentials.value.email,
      admin_password: adminCredentials.value.password
    })

    if (result.success) {
      showSuccess(result.message)
      await loadInvoices()
      showAdminAuthModal.value = false
      selectedInvoice.value = null
    } else {
      showError(result.message)
    }
  } catch (error) {
    console.error('Error eliminando factura:', error)
    showError('Error al eliminar la factura')
  } finally {
    isLoading.value = false
  }
}

// Función para guardar cambios en la factura
const saveInvoiceChanges = async () => {
  if (!selectedInvoice.value) return

  try {
    isLoading.value = true
    
    const result = await invoiceService.updateInvoice(selectedInvoice.value.id, {
      status: selectedInvoice.value.status,
      due_date: selectedInvoice.value.due_date,
      notes: selectedInvoice.value.notes
    })

    if (result.success) {
      showSuccess('Factura actualizada exitosamente')
      await loadInvoices()
      showEditModal.value = false
      selectedInvoice.value = null
    } else {
      showError(result.message)
    }
  } catch (error) {
    console.error('Error actualizando factura:', error)
    showError('Error al actualizar la factura')
  } finally {
    isLoading.value = false
  }
}

// Funciones para el nuevo modal de recibo de factura
const closeInvoiceModal = () => {
  showReceiptModal.value = false
  selectedInvoice.value = null
}

const handleNewSale = () => {
  redirectToPos()
}

// Funciones para el modal de teléfono
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

// Función para manejar envío de WhatsApp desde ReceiptModal (simplificado)
const handleSendWhatsApp = async () => {
  try {
    console.log('📱 Iniciando envío de factura por WhatsApp')
    
    if (!selectedInvoice.value) {
      throw new Error('No hay datos de factura para enviar')
    }

    // Obtener teléfono del cliente (si está disponible)
    let phoneNumber = selectedInvoice.value.customer_phone
    let isPhoneFromModal = false
    
    // Función para solicitar teléfono al usuario
    const requestPhoneNumber = async () => {
      phoneModalMessage.value = `Ingrese el número de teléfono para enviar la factura de ${selectedInvoice.value.customer_name || 'Cliente General'}:`
      
      try {
        const newPhone = await new Promise((resolve, reject) => {
          phoneModalResolve.value = resolve
          showPhoneModal.value = true
        })
        
        if (!newPhone) {
          return null
        }
        
        return newPhone
      } catch (error) {
        return null
      }
    }
    
    // Si no hay teléfono registrado O es "Cliente General", solicitar número
    if (!phoneNumber || phoneNumber.trim() === '' || selectedInvoice.value.customer_name === 'Cliente General') {
      phoneNumber = await requestPhoneNumber()
      if (!phoneNumber) return
      isPhoneFromModal = true
    }

    // Función para validar y formatear teléfono
    const validateAndFormatPhone = (phone) => {
      // Limpiar y formatear teléfono para Colombia
      let cleanPhone = phone.replace(/\D/g, '')
      
      // Si no tiene código de país, agregar +57
      if (cleanPhone.length === 10 && cleanPhone.startsWith('3')) {
        cleanPhone = '+57' + cleanPhone
      } else if (cleanPhone.length === 12 && cleanPhone.startsWith('57')) {
        cleanPhone = '+' + cleanPhone
      } else if (!cleanPhone.startsWith('+57') && cleanPhone.length === 12) {
        cleanPhone = '+' + cleanPhone
      }
      
      // Validar formato final
      if (!/^\+57[0-9]{10}$/.test(cleanPhone)) {
        return null
      }
      
      return cleanPhone
    }
    
    // Validar teléfono en bucle si viene del modal
    let cleanPhone = validateAndFormatPhone(phoneNumber)
    
    while (!cleanPhone) {
      if (isPhoneFromModal) {
        // Si viene del modal y está mal, solicitar de nuevo
        showError('Formato de número inválido. Use: 3001234567 o +573001234567')
        phoneNumber = await requestPhoneNumber()
        if (!phoneNumber) return
        cleanPhone = validateAndFormatPhone(phoneNumber)
      } else {
        // Si es teléfono registrado y está mal, mostrar error y salir
        showError('El teléfono registrado del cliente no es válido. Use formato: 3001234567 o +573001234567')
        return
      }
    }

    // Generar PDF usando la función idéntica al POS
    console.log('📄 Generando PDF para WhatsApp...')
    const pdfBlob = await generatePDFFromReceiptModal()
    
    // Crear datos de factura para WhatsApp
    const invoiceData = {
      id: selectedInvoice.value.id,
      invoice_number: selectedInvoice.value.invoice_number || selectedInvoice.value.invoiceNumber,
      total: selectedInvoice.value.total,
      customer_name: selectedInvoice.value.customer_name || selectedInvoice.value.customer,
      customer_phone: cleanPhone,
      date: selectedInvoice.value.created_at || selectedInvoice.value.date
    }
    
    // Crear mensaje para WhatsApp
    const message = `¡Hola ${invoiceData.customer_name}!\n\nTe enviamos tu factura:\n\n📋 Factura: ${invoiceData.invoice_number}\n💰 Total: $${invoiceData.total.toLocaleString()}\n� Fecha: ${formatInvoiceDate(invoiceData.date)}\n\n¡Gracias por tu compra! 🙏`
    const fileName = `Factura_${invoiceData.invoice_number}.pdf`
    
    console.log('📱 Enviando PDF por WhatsApp:', { phone: cleanPhone, fileName, messageLength: message.length })
    
    // Enviar usando el servicio de WhatsApp con PDF
    const result = await whatsappService.sendInvoiceWithPDF(
      cleanPhone, 
      pdfBlob, 
      message, 
      fileName, 
      invoiceData.customer_name
    )
    
    if (result.success) {
      showSuccess(`📱 Factura enviada por WhatsApp a ${cleanPhone}`)
    } else {
      showError(result.message || 'Error al enviar por WhatsApp')
    }
    
  } catch (error) {
    console.error('❌ Error enviando por WhatsApp:', error)
    showError('Error al enviar la factura por WhatsApp')
  }
}

// Inicialización
onMounted(async () => {
  console.log('Módulo de facturación inicializado')
  setupAuth()
  await Promise.all([
    loadInvoices(),
    loadStats()
  ])
})

// ==========================================
// FUNCIONES DE VALIDACIÓN DE TELÉFONO
// ==========================================

// Función para limpiar número de teléfono
const cleanPhoneNumber = (phone) => {
  if (!phone) return ''
  
  // Remover espacios, guiones, paréntesis
  let cleaned = phone.replace(/[\s\-\(\)]/g, '')
  
  // Si empieza con 57, agregar +
  if (cleaned.startsWith('57') && !cleaned.startsWith('+57')) {
    cleaned = '+' + cleaned
  }
  
  // Si empieza con 3 (celular), agregar +57
  if (cleaned.startsWith('3') && cleaned.length === 10) {
    cleaned = '+57' + cleaned
  }
  
  return cleaned
}

// Función para validar número de teléfono colombiano
const isValidPhoneNumber = (phone) => {
  if (!phone) return false
  
  // Formato: +57 + 3XXXXXXXXX (10 dígitos después del +57)
  const phoneRegex = /^\+57[3][0-9]{9}$/
  return phoneRegex.test(phone)
}

// Función para solicitar número de teléfono
const promptForPhoneNumber = (message) => {
  return new Promise((resolve) => {
    phoneModalMessage.value = message
    phoneModalResolve.value = resolve
    showPhoneModal.value = true
  })
}


</script>

<style scoped>
/* Fuente Inter */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

/* Animaciones personalizadas */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { 
    opacity: 0; 
    transform: translateY(20px) scale(0.95); 
  }
  to { 
    opacity: 1; 
    transform: translateY(0) scale(1); 
  }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}

.animate-slideUp {
  animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Efectos de hover mejorados */
.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}

.group:hover .group-hover\:scale-150 {
  transform: scale(1.5);
}

/* Transiciones globales */
* {
  transition-property: all;
  transition-duration: 200ms;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Scrollbar personalizado */
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

/* Efectos de focus mejorados */
input:focus, select:focus {
  transform: translateY(-1px);
  box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.15);
}

/* Gradient text */
.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}
</style>