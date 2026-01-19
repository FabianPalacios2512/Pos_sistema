<template>
  <div class="font-sans bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8" style="height: 100%; display: flex; flex-direction: column;">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in" style="flex: 1; display: flex; flex-direction: column; min-height: 0;">
      
      <!-- Header Simple y Elegante -->
      <div class="flex items-center justify-between pb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Compras</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Control de proveedores y órdenes de compra</p>
        </div>
        
        <div class="flex items-center gap-3">
          <button @click="refreshCurrentTab"
                  :disabled="loading"
                  class="px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-800 shadow-sm transition-all duration-200">
            <svg class="w-4 h-4 inline mr-2" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Actualizar
          </button>
          
          <button v-if="activeTab === 'suppliers'"
                  @click="viewMode = 'create-supplier'"
                  class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Proveedor
          </button>

          <button v-if="activeTab === 'orders'"
                  @click="viewMode = 'create'"
                  class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
            Nueva Orden de Compra
          </button>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="flex items-center gap-2 border-b border-gray-300 dark:border-zinc-700">
        <button 
          @click="changeTab('suppliers')" 
          :class="[
            'px-6 py-3 text-sm font-bold rounded-t-xl transition-all duration-200',
            activeTab === 'suppliers' 
              ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white border-t-2 border-x-2 border-gray-300 dark:border-zinc-700 border-b-0 -mb-px' 
              : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
          ]">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>Proveedores</span>
          </div>
        </button>
        <button 
          @click="changeTab('orders')" 
          :class="[
            'px-6 py-3 text-sm font-bold rounded-t-xl transition-all duration-200',
            activeTab === 'orders' 
              ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white border-t-2 border-x-2 border-gray-300 dark:border-zinc-700 border-b-0 -mb-px' 
              : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
          ]">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span>Órdenes de Compra</span>
          </div>
        </button>
      </div>

      <!-- TAB: PROVEEDORES -->
      <div v-if="activeTab === 'suppliers'" style="flex: 1; display: flex; flex-direction: column; min-height: 0;">
        <!-- Mostrar lista de proveedores -->
        <div v-if="viewMode === 'list'" style="flex: 1; display: flex; flex-direction: column; min-height: 0;">
          <SuppliersViewMasterDetail ref="suppliersView" />
        </div>

        <!-- Crear nuevo proveedor (inline form) -->
        <div v-else-if="viewMode === 'create-supplier'" class="animate-fade-in">
          <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
            
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-3 flex items-center justify-between bg-gray-50 dark:bg-zinc-900">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900 dark:text-white">Nuevo Proveedor</h3>
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Complete la información del proveedor</p>
                </div>
              </div>
              <button @click="cancelCreateSupplier" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Form -->
            <div class="px-6 py-3 space-y-4">
              <!-- Información Básica -->
              <div>
                <h4 class="text-xs font-bold text-gray-900 dark:text-white mb-3 pb-1.5 border-b border-gray-200 dark:border-zinc-800 uppercase tracking-wide">Información del Proveedor</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                  <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Nombre del Proveedor *</label>
                    <input
                      v-model="supplierForm.name"
                      type="text"
                      placeholder="Ej: Distribuidora XYZ"
                      :class="['w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-xl border focus:ring-2 focus:border-transparent transition-all', supplierErrors.name ? 'border-red-500 dark:border-red-600 focus:ring-red-500 dark:focus:ring-red-400' : 'border-gray-300 dark:border-zinc-700 focus:ring-blue-500 dark:focus:ring-blue-400']"
                    />
                    <p v-if="supplierErrors.name" class="mt-1 text-xs text-red-500 dark:text-red-400">{{ supplierErrors.name }}</p>
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Documento (NIT/CC)</label>
                    <input
                      v-model="supplierForm.document"
                      type="text"
                      placeholder="123456789-0"
                      class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Teléfono</label>
                    <input
                      v-model="supplierForm.phone"
                      type="text"
                      placeholder="3001234567"
                      class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                    />
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Email</label>
                    <input
                      v-model="supplierForm.email"
                      type="email"
                      placeholder="email@proveedor.com"
                      class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Ciudad</label>
                    <input
                      v-model="supplierForm.city"
                      type="text"
                      placeholder="Bogotá"
                      class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Dirección</label>
                    <input
                      v-model="supplierForm.address"
                      type="text"
                      placeholder="Calle 123 #45-67"
                      class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                    />
                  </div>
                </div>
              </div>

              <!-- Contacto y Notas en grid -->
              <div class="grid grid-cols-2 gap-3">
                <!-- Persona de Contacto -->
                <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                  <h4 class="text-xs font-bold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Persona de Contacto</h4>
                  <div class="space-y-2.5">
                    <div>
                      <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1">Nombre</label>
                      <input
                        v-model="supplierForm.contact_name"
                        type="text"
                        placeholder="Juan Pérez"
                        class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                      />
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1">Teléfono</label>
                      <input
                        v-model="supplierForm.contact_phone"
                        type="text"
                        placeholder="3009876543"
                        class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                      />
                    </div>
                  </div>
                </div>

                <!-- Notas -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Notas Adicionales</label>
                  <textarea
                    v-model="supplierForm.notes"
                    rows="4"
                    placeholder="Información adicional sobre el proveedor..."
                    class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all resize-none"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-3 flex justify-end gap-2.5">
              <button @click="cancelCreateSupplier" class="px-4 py-2 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-800 shadow-sm transition-all duration-200">
                Cancelar
              </button>
              <button @click="saveSupplier" :disabled="savingSupplier" class="px-5 py-2 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white text-sm font-bold rounded-lg shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300">
                {{ savingSupplier ? 'Guardando...' : 'Guardar Proveedor' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB: ÓRDENES DE COMPRA -->
      <div v-if="activeTab === 'orders'" style="flex: 1; display: flex; flex-direction: column; min-height: 0;">

      <!-- Master-Detail Layout Enterprise: 30/70 - Unificado como WhatsApp -->
      <div v-if="viewMode === 'list'" class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 transition-colors duration-300" style="flex: 1; min-height: 0;">
        <div class="grid grid-cols-1 lg:grid-cols-10 h-full">
        
        <!-- PANEL IZQUIERDO: Lista de Órdenes (30%) -->
        <div class="lg:col-span-3 overflow-hidden flex flex-col border-r border-gray-200 dark:border-zinc-800 transition-colors duration-300">
            
            <!-- Filtros -->
            <div class="p-4 border-b border-gray-200 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-900 flex-shrink-0">
              <div class="flex items-center gap-2 flex-wrap">
                <button v-for="status in statusFilters" :key="status.value"
                        @click="filterStatus = status.value"
                        :class="[
                          'px-3 py-2 text-xs font-bold rounded-lg transition-all',
                          filterStatus === status.value
                            ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-md'
                            : 'bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 hover:bg-gray-200 dark:hover:bg-zinc-700'
                        ]">
                  {{ status.label }}
                </button>
              </div>
            </div>

            <!-- Lista de órdenes -->
            <div class="flex-1 overflow-y-auto bg-white dark:bg-zinc-900 px-2">
              <!-- Loading -->
              <div v-if="loading" class="p-8 text-center">
                <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto"></div>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-4">Cargando órdenes...</p>
              </div>

              <!-- Empty -->
              <div v-else-if="filteredOrders.length === 0" class="p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                  <svg class="w-10 h-10 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">Sin órdenes</p>
                <p class="text-xs text-gray-500 dark:text-zinc-500">No hay órdenes con este filtro</p>
              </div>

              <!-- Order Cards -->
              <div v-else class="divide-y divide-gray-100 dark:divide-zinc-800">
                <div v-for="order in filteredOrders" :key="order.id"
                     @click="selectOrder(order)"
                     :class="[
                       'p-4 cursor-pointer transition-all duration-200',
                       selectedOrder?.id === order.id
                         ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-l-blue-600 dark:border-l-blue-500'
                         : 'hover:bg-slate-50 dark:hover:bg-zinc-800/50 hover:shadow-sm border-l-4 border-l-transparent'
                     ]">
                  
                  <div class="flex items-start justify-between mb-2">
                    <div class="flex-1 min-w-0 mr-3">
                      <p class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ order.order_number || 'Sin número' }}</p>
                      <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5 truncate">{{ order.supplier?.name || 'Sin proveedor' }}</p>
                    </div>
                    <span :class="getStatusBadgeClass(order.status)"
                          class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide flex-shrink-0 border">
                      {{ getStatusLabel(order.status) }}
                    </span>
                  </div>

                  <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500 dark:text-zinc-500">{{ formatDate(order.order_date) }}</span>
                    <span class="font-mono font-bold text-gray-900 dark:text-white">${{ formatCurrency(order.total) }}</span>
                  </div>

                  <div v-if="order.items && order.items.length > 0" class="mt-2 pt-2 border-t border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center text-xs text-gray-600 dark:text-zinc-400">
                      <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                      </svg>
                      <span>{{ order.items.length }} producto(s)</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        <!-- PANEL DERECHO: Detalles de la Orden (70%) -->
        <div class="lg:col-span-7 overflow-hidden flex flex-col bg-gray-50/30 dark:bg-zinc-950/30 transition-colors duration-300">
          <!-- Empty State Profesional estilo WhatsApp -->
          <div v-if="!selectedOrder" class="flex-1 flex flex-col items-center justify-center p-12 text-center bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-zinc-900/50 dark:via-zinc-900/30 dark:to-zinc-900/50 relative">
              
              <!-- Ilustración SVG profesional y limpia -->
              <div class="mb-8 relative">
                <!-- Efecto glow suave de fondo -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-200/30 via-transparent to-purple-200/30 dark:from-blue-500/10 dark:to-purple-500/10 rounded-3xl blur-3xl scale-150"></div>
                
                <!-- Ilustración principal de órdenes de compra -->
                <svg class="w-48 h-48 relative z-10" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <!-- Sombra del documento -->
                  <rect x="48" y="38" width="88" height="110" rx="6" class="fill-gray-200/50 dark:fill-zinc-700/30"/>
                  
                  <!-- Documento principal -->
                  <rect x="44" y="32" width="88" height="110" rx="6" class="fill-white dark:fill-zinc-800" stroke-width="0"/>
                  <rect x="44" y="32" width="88" height="110" rx="6" class="fill-none stroke-gray-200 dark:stroke-zinc-700" stroke-width="1.5"/>
                  
                  <!-- Encabezado del documento -->
                  <rect x="54" y="44" width="40" height="5" rx="2.5" class="fill-blue-400 dark:fill-blue-500"/>
                  <rect x="54" y="54" width="68" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                  <rect x="54" y="62" width="55" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                  
                  <!-- Lista de productos -->
                  <rect x="54" y="74" width="68" height="16" rx="2" class="fill-gray-50 dark:fill-zinc-700/50"/>
                  <circle cx="62" cy="82" r="4" class="fill-emerald-400 dark:fill-emerald-500"/>
                  <rect x="70" y="79" width="40" height="3" rx="1.5" class="fill-gray-300 dark:fill-zinc-600"/>
                  <rect x="115" y="79" width="12" height="6" rx="1" class="fill-emerald-100 dark:fill-emerald-500/20"/>
                  
                  <rect x="54" y="94" width="68" height="16" rx="2" class="fill-gray-50 dark:fill-zinc-700/50"/>
                  <circle cx="62" cy="102" r="4" class="fill-blue-400 dark:fill-blue-500"/>
                  <rect x="70" y="99" width="35" height="3" rx="1.5" class="fill-gray-300 dark:fill-zinc-600"/>
                  <rect x="115" y="99" width="12" height="6" rx="1" class="fill-blue-100 dark:fill-blue-500/20"/>
                  
                  <!-- Total -->
                  <rect x="94" y="118" width="28" height="8" rx="4" class="fill-emerald-500 dark:fill-emerald-400"/>
                  
                  <!-- Sello de orden -->
                  <circle cx="145" cy="55" r="18" class="fill-blue-100 dark:fill-blue-500/20"/>
                  <circle cx="145" cy="55" r="14" class="fill-blue-500 dark:fill-blue-400"/>
                  <path d="M140 55L143 58L151 50" class="stroke-white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                  
                  <!-- Caja de productos -->
                  <rect x="140" y="100" width="28" height="24" rx="4" class="fill-purple-100 dark:fill-purple-500/20"/>
                  <rect x="144" y="104" width="20" height="16" rx="2" class="fill-purple-400 dark:fill-purple-500"/>
                  <path d="M149 112H159M154 107V117" class="stroke-white" stroke-width="2" stroke-linecap="round"/>
                  
                  <!-- Icono de camión -->
                  <rect x="12" y="115" width="32" height="20" rx="3" class="fill-amber-100 dark:fill-amber-500/20"/>
                  <rect x="16" y="119" width="18" height="12" rx="2" class="fill-amber-500 dark:fill-amber-400"/>
                  <rect x="34" y="123" width="8" height="8" rx="1" class="fill-amber-400 dark:fill-amber-500"/>
                  <circle cx="22" cy="135" r="3" class="fill-gray-600 dark:fill-zinc-500"/>
                  <circle cx="36" cy="135" r="3" class="fill-gray-600 dark:fill-zinc-500"/>
                </svg>
              </div>
              
              <!-- Texto de bienvenida profesional -->
              <div class="relative z-10 max-w-md">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">
                  Selecciona una orden
                </h3>
                <p class="text-sm text-gray-500 dark:text-zinc-400 leading-relaxed mb-2">
                  Haz clic en una orden de la lista para ver sus detalles completos y gestionar la recepción de mercancía.
                </p>
                <p class="text-xs text-gray-400 dark:text-zinc-500">
                  Controla tus órdenes de compra de forma rápida y segura.
                </p>
              </div>
              
              <!-- Footer de confianza estilo WhatsApp -->
              <div class="absolute bottom-6 left-0 right-0 flex items-center justify-center gap-2 text-xs text-gray-400 dark:text-zinc-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Datos de compras sincronizados en tiempo real</span>
              </div>
          </div>

          <!-- Order Details -->
          <div v-else class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-zinc-900/50">
            
            <!-- Header con estado -->
            <div class="p-6 border-b border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex-shrink-0">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1 min-w-0 mr-4">
                  <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ selectedOrder.order_number }}</h2>
                  <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    {{ selectedOrder.supplier?.name || 'Sin proveedor' }}
                  </p>
                </div>
                <span :class="getStatusBadgeClass(selectedOrder.status)"
                      class="px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide border flex-shrink-0">
                  {{ getStatusLabel(selectedOrder.status) }}
                </span>
              </div>

              <!-- Info Grid -->
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                  <p class="text-xs text-gray-500 dark:text-zinc-500 uppercase tracking-wide mb-1">Fecha Orden</p>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatDate(selectedOrder.order_date) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                  <p class="text-xs text-gray-500 dark:text-zinc-500 uppercase tracking-wide mb-1">Total</p>
                  <p class="text-sm font-mono font-bold text-gray-900 dark:text-white">${{ formatCurrency(selectedOrder.total) }}</p>
                </div>
                <!-- Mostrar bodega solo si hay múltiples bodegas -->
                <div v-if="hasMultipleWarehouses && selectedOrder.warehouse" class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                  <p class="text-xs text-gray-500 dark:text-zinc-500 uppercase tracking-wide mb-1">Bodega Destino</p>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ selectedOrder.warehouse.name }}</p>
                </div>
                <div v-if="selectedOrder.expected_date" class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                  <p class="text-xs text-gray-500 dark:text-zinc-500 uppercase tracking-wide mb-1">Fecha Esperada</p>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatDate(selectedOrder.expected_date) }}</p>
                </div>
              </div>

              <!-- Botones de Acción -->
              <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-zinc-800">
                <button
                  @click="downloadOrderPDF"
                  class="px-4 py-2 rounded-lg transition-all flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800 border border-transparent hover:border-gray-200 dark:hover:border-zinc-700"
                  title="Descargar PDF">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  Descargar
                </button>
                
                <button
                  v-if="selectedOrder.status === 'pending'"
                  @click="sendOrderByEmail"
                  class="px-4 py-2 rounded-lg transition-all flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800 border border-transparent hover:border-gray-200 dark:hover:border-zinc-700"
                  title="Enviar por Email">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                  Email
                </button>
                
                <button
                  v-if="selectedOrder.status === 'pending'"
                  @click="sendOrderByWhatsApp"
                  class="px-4 py-2 rounded-lg transition-all flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800 border border-transparent hover:border-gray-200 dark:hover:border-zinc-700"
                  title="Enviar por WhatsApp">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                  </svg>
                  WhatsApp
                </button>

                <button
                  v-if="selectedOrder.status === 'pending'"
                  @click="markOrderAsPaid"
                  class="px-4 py-2 rounded-lg transition-all flex items-center gap-2 text-sm font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border border-transparent hover:border-emerald-200 dark:hover:border-emerald-800"
                  title="Marcar como pagada">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Marcar Pagada
                </button>
              </div>
            </div>

            <!-- Products Table -->
            <div class="p-6 flex-1 overflow-y-auto">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide flex items-center">
                  <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                  Productos ({{ selectedOrder.items?.length || 0 }})
                </h3>
              </div>
              
              <div class="border border-gray-300 dark:border-zinc-800 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                  <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-zinc-900">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Producto</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Ordenado</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Recibido</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Progreso</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Precio Unit.</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                      <tr v-for="item in selectedOrder.items" :key="item.id"
                          class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 hover:shadow-sm transition-colors">
                        <td class="px-4 py-3">
                          <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.product?.name || 'Producto sin nombre' }}</p>
                            <!-- 👗 Badge de variante -->
                            <span v-if="item.variant_id" class="px-1.5 py-0.5 text-[9px] font-bold uppercase bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-400 rounded border border-purple-200 dark:border-purple-800">
                              👗
                            </span>
                          </div>
                          <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                            {{ item.product?.sku || 'Sin SKU' }}
                            <!-- 👗 Mostrar variante -->
                            <span v-if="item.variant_options" class="ml-1 text-purple-600 dark:text-purple-400">
                              • {{ formatVariantOptions(item.variant_options) }}
                            </span>
                          </p>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 dark:bg-zinc-800 rounded-lg">
                            <span class="text-sm font-mono font-bold text-gray-900 dark:text-white">{{ item.quantity_ordered }}</span>
                          </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span :class="[
                            'inline-flex items-center justify-center w-10 h-10 rounded-lg text-sm font-mono font-bold',
                            item.quantity_received >= item.quantity_ordered
                              ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400'
                              : item.quantity_received > 0
                                ? 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400'
                                : 'bg-gray-100 dark:bg-zinc-800 text-gray-400 dark:text-zinc-600'
                          ]">
                            {{ item.quantity_received || 0 }}
                          </span>
                        </td>
                        <td class="px-4 py-3">
                          <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
                              <div :class="[
                                'h-2 rounded-full transition-all duration-300',
                                item.quantity_received >= item.quantity_ordered
                                  ? 'bg-emerald-500'
                                  : item.quantity_received > 0
                                    ? 'bg-amber-500'
                                    : 'bg-gray-300 dark:bg-zinc-600'
                              ]" :style="`width: ${Math.min(100, (item.quantity_received / item.quantity_ordered) * 100)}%`"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600 dark:text-zinc-400 w-10 text-right">
                              {{ Math.round((item.quantity_received / item.quantity_ordered) * 100) }}%
                            </span>
                          </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                          <span class="text-sm font-mono text-gray-900 dark:text-white">${{ formatCurrency(item.unit_cost) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                          <span class="text-sm font-mono font-bold text-gray-900 dark:text-white">${{ formatCurrency(item.quantity_ordered * item.unit_cost) }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="p-6 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900 flex-shrink-0">
              <div class="flex items-center justify-between gap-3">
                <div class="text-sm text-gray-600 dark:text-zinc-400">
                  <span class="font-medium">Total:</span>
                  <span class="font-mono font-bold text-lg text-gray-900 dark:text-white ml-2">${{ formatCurrency(selectedOrder.total) }}</span>
                </div>
                <div class="flex items-center gap-3">
                  <button v-if="selectedOrder.status === 'pending' || selectedOrder.status === 'partial'"
                          @click="openReceiveModal"
                          class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/30 transition-all duration-300 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Ingresar Productos a Stock
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>

      <!-- CREATE ORDER VIEW (Inline Form) -->
      <div v-else-if="viewMode === 'create'" class="animate-fade-in">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
          
          <!-- Header -->
          <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-3 flex items-center justify-between bg-gray-50 dark:bg-zinc-900">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Nueva Orden de Compra</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400">Complete la información de la orden</p>
              </div>
            </div>
            <button @click="cancelCreateOrder" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Form -->
          <div class="px-6 py-3 space-y-4">
            <!-- Información General -->
            <div>
              <h4 class="text-xs font-bold text-gray-900 dark:text-white mb-3 pb-1.5 border-b border-gray-200 dark:border-zinc-800 uppercase tracking-wide">Información de la Orden</h4>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                  <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Proveedor *</label>
                  <select v-model="orderForm.supplier_id" :class="['w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-xl border focus:ring-2 focus:border-transparent transition-all', orderErrors.supplier_id ? 'border-red-500 dark:border-red-600 focus:ring-red-500 dark:focus:ring-red-400' : 'border-gray-300 dark:border-zinc-700 focus:ring-blue-500 dark:focus:ring-blue-400']">
                    <option value="">Seleccionar proveedor...</option>
                    <option v-for="supplier in suppliers.filter(s => s.active)" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                  </select>
                  <p v-if="orderErrors.supplier_id" class="mt-1 text-xs text-red-500 dark:text-red-400">{{ orderErrors.supplier_id }}</p>
                </div>

                <div v-if="shouldShowWarehouseSelector">
                  <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Tienda/Sede *</label>
                  <select v-model="orderForm.warehouse_id" class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all">
                    <option value="">Seleccionar tienda/sede...</option>
                    <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">{{ warehouse.name }}</option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Fecha de Orden *</label>
                  <input v-model="orderForm.order_date" type="date" :class="['w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-xl border focus:ring-2 focus:border-transparent transition-all', orderErrors.order_date ? 'border-red-500 dark:border-red-600 focus:ring-red-500 dark:focus:ring-red-400' : 'border-gray-300 dark:border-zinc-700 focus:ring-blue-500 dark:focus:ring-blue-400']" />
                  <p v-if="orderErrors.order_date" class="mt-1 text-xs text-red-500 dark:text-red-400">{{ orderErrors.order_date }}</p>
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Fecha Esperada</label>
                  <input v-model="orderForm.expected_date" type="date" class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all" />
                </div>

                <div class="md:col-span-2">
                  <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Referencia</label>
                  <input v-model="orderForm.reference" type="text" placeholder="OC-2024-001, Factura #123, etc." class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all" />
                </div>
              </div>
            </div>

            <!-- Productos -->
            <div>
              <div class="flex items-center justify-between mb-3 pb-1.5 border-b border-gray-200 dark:border-zinc-800">
                <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide">Productos</h4>
                <button @click="showProductSelector = true" class="px-3 py-1.5 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-xs font-bold rounded-lg transition-all duration-200 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  Agregar Producto
                </button>
              </div>

              <div v-if="orderForm.items.length === 0" class="text-center py-6 border-2 border-dashed border-gray-300 dark:border-zinc-700 rounded-lg">
                <div class="w-10 h-10 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-2">
                  <svg class="w-5 h-5 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                  </svg>
                </div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">No hay productos</p>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Agrega productos a esta orden</p>
              </div>

              <div v-else class="space-y-1.5">
                <div v-for="(item, index) in orderForm.items" :key="index" class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-2.5 flex items-center gap-2.5">
                  <div class="flex-1 grid grid-cols-12 gap-3 items-center">
                    <div class="col-span-4">
                      <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.product?.name || 'Producto' }}</p>
                        <!-- 👗 Badge de variante -->
                        <span v-if="item.variant_id" class="px-1.5 py-0.5 text-[9px] font-bold uppercase bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-400 rounded border border-purple-200 dark:border-purple-800">
                          👗
                        </span>
                      </div>
                      <p class="text-xs text-gray-500 dark:text-zinc-500">
                        SKU: {{ item.product?.sku || 'N/A' }}
                        <span v-if="item.variant_name" class="ml-1 text-purple-600 dark:text-purple-400">• {{ item.variant_name }}</span>
                      </p>
                    </div>
                    <div class="col-span-3">
                      <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Cantidad</label>
                      <input v-model.number="item.quantity" type="number" min="0.01" step="0.01" class="w-full px-3 py-2 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent text-sm" @input="calculateItemTotal(index)" />
                    </div>
                    <div class="col-span-3">
                      <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Costo Unitario</label>
                      <input v-model.number="item.unit_cost" type="number" min="0" step="0.01" class="w-full px-3 py-2 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent text-sm" @input="calculateItemTotal(index)" />
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

            <!-- Totales y Notas en grid -->
            <div class="grid grid-cols-2 gap-3">
              <div class="bg-gradient-to-br from-slate-50 to-gray-100 dark:from-zinc-800/50 dark:to-zinc-900/50 rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                <div class="space-y-1.5">
                  <div class="flex justify-between items-center">
                    <span class="text-xs font-medium text-gray-700 dark:text-zinc-300">Subtotal:</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">${{ formatNumber(orderSubtotal) }}</span>
                  </div>
                  <div class="flex justify-between items-center pt-1.5 border-t border-gray-300 dark:border-zinc-700">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">Total:</span>
                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400">${{ formatNumber(orderSubtotal) }}</span>
                  </div>
                </div>
              </div>

              <!-- Notas -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Notas</label>
                <textarea v-model="orderForm.notes" rows="2" placeholder="Comentarios adicionales..." class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all resize-none"></textarea>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-3 flex justify-end gap-2.5">
            <button @click="cancelCreateOrder" class="px-4 py-2 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-800 shadow-sm transition-all duration-200">
              Cancelar
            </button>
            <button @click="saveOrderAsDraft" :disabled="savingOrder" class="px-4 py-2 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-800 shadow-sm transition-all duration-200">
              {{ savingOrder ? 'Guardando...' : 'Guardar Borrador' }}
            </button>
            <button @click="saveAndShowSendOptions" :disabled="savingOrder" class="px-5 py-2 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white text-sm font-bold rounded-lg shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300">
              {{ savingOrder ? 'Guardando...' : 'Crear Orden' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: Selector de Productos -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showProductSelector" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-[60] p-4" @click.self="showProductSelector = false">
          <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 max-w-3xl w-full max-h-[80vh] overflow-auto">
            <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between sticky top-0 bg-white dark:bg-zinc-900 z-10">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Seleccionar Producto</h3>
              <button @click="showProductSelector = false" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <div class="px-6 py-4">
              <input v-model="productSearch" type="text" placeholder="Buscar producto por nombre o SKU..." class="w-full px-4 py-3 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all mb-4" />

              <div v-if="loadingProducts" class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-4 border-gray-200 dark:border-zinc-700 border-t-blue-600 dark:border-t-blue-400"></div>
              </div>

              <div v-else class="space-y-2 max-h-96 overflow-y-auto">
                <button v-for="product in filteredProducts" :key="product.id" @click="addProductToOrder(product)" class="w-full text-left p-3 bg-gray-50 dark:bg-zinc-800/50 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl border border-transparent hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-200">
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ product.name }}</p>
                        <!-- 👗 Badge para productos con variantes -->
                        <span v-if="product.product_type === 'variable'" class="px-2 py-0.5 text-[10px] font-bold uppercase bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-400 rounded-full border border-purple-200 dark:border-purple-800">
                          Variantes
                        </span>
                      </div>
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
      </Transition>
    </Teleport>

    <!-- 👗 Modal: Selector de Variantes (para productos moda) -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showVariantSelector" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-[70] p-4" @click.self="closeVariantSelector">
          <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 max-w-2xl w-full max-h-[80vh] overflow-auto">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4 sticky top-0 bg-white dark:bg-zinc-900 z-10">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">Seleccionar Variante</h3>
                  <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">
                    {{ selectedProductForVariant?.name }}
                  </p>
                </div>
                <button @click="closeVariantSelector" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Loading -->
            <div v-if="loadingVariants" class="flex items-center justify-center py-12">
              <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-200 dark:border-zinc-700 border-t-purple-600 dark:border-t-purple-400"></div>
            </div>

            <!-- Lista de Variantes -->
            <div v-else class="p-4 space-y-2">
              <div class="bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 rounded-lg p-3 mb-4">
                <p class="text-sm text-purple-800 dark:text-purple-200">
                  <strong>👗 Producto con variantes:</strong> Selecciona la talla/color específico que deseas ordenar.
                </p>
              </div>

              <button 
                v-for="variant in productVariants" 
                :key="variant.id" 
                @click="addVariantToOrder(variant)"
                class="w-full text-left p-4 bg-gray-50 dark:bg-zinc-800/50 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-xl border-2 border-transparent hover:border-purple-300 dark:hover:border-purple-700 transition-all duration-200"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                      {{ formatVariantOptions(variant.options_summary) }}
                    </p>
                    <div class="flex items-center gap-3 mt-1">
                      <span class="text-xs text-gray-500 dark:text-zinc-500">SKU: {{ variant.sku }}</span>
                      <span class="text-xs font-bold" :class="(variant.stock || 0) > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                        Stock: {{ variant.stock || 0 }}
                      </span>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">${{ formatNumber(variant.cost_price || selectedProductForVariant?.cost_price || 0) }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-500">Costo</p>
                  </div>
                </div>
              </button>

              <div v-if="productVariants.length === 0" class="text-center py-8">
                <p class="text-gray-500 dark:text-zinc-400">No hay variantes disponibles</p>
              </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 dark:border-zinc-800 px-6 py-3 bg-gray-50 dark:bg-zinc-900 sticky bottom-0">
              <button @click="closeVariantSelector" class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-700 transition-all">
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Modal: Ingresar Productos -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showReceiveModal" 
             class="fixed inset-0 bg-black/60  flex items-center justify-center p-4 z-50"
             @click.self="closeReceiveModal">
          <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-3xl shadow-2xl overflow-hidden animate-fade-in max-h-[90vh] flex flex-col">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800 bg-gradient-to-r from-emerald-600 to-teal-600">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-white">Ingresar Productos a Stock</h3>
                    <p class="text-sm text-white/80">{{ selectedOrder?.order_number }}</p>
                  </div>
                </div>
                <button @click="closeReceiveModal" class="text-white/80 hover:text-white transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto p-6">
              <div class="bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-4">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                  <strong>💡 Instrucciones:</strong> Marca el checkbox si recibiste la cantidad completa, o ingresa la cantidad exacta recibida. Puedes eliminar productos que no llegaron.
                </p>
              </div>

              <div class="space-y-3">
                <div v-for="(item, index) in receiveForm.items" :key="item.item_id"
                     class="border border-gray-300 dark:border-zinc-800 rounded-xl p-4 hover:border-gray-300 dark:hover:border-zinc-700 transition-all">
                  <div class="flex items-start gap-4">
                    <!-- Checkbox -->
                    <div class="pt-1">
                      <input type="checkbox"
                             :id="`check-${item.item_id}`"
                             v-model="item.received_all"
                             @change="() => handleCheckboxChange(item)"
                             class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    </div>

                    <!-- Product Info -->
                    <div class="flex-1 min-w-0">
                      <label :for="`check-${item.item_id}`" class="block">
                        <div class="flex items-center gap-2">
                          <p class="text-sm font-bold text-gray-900 dark:text-white">{{ item.product_name }}</p>
                          <!-- 👗 Badge de variante -->
                          <span v-if="item.variant_id" class="px-1.5 py-0.5 text-[9px] font-bold uppercase bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-400 rounded border border-purple-200 dark:border-purple-800">
                            👗
                          </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                          Ordenado: {{ item.quantity_ordered }} unidades
                          <!-- 👗 Mostrar variante -->
                          <span v-if="item.variant_options" class="ml-1 text-purple-600 dark:text-purple-400">
                            • {{ typeof item.variant_options === 'string' ? formatVariantOptions(item.variant_options) : formatVariantOptions(item.variant_options) }}
                          </span>
                        </p>
                      </label>

                      <!-- Quantity Input -->
                      <div class="mt-3">
                        <label class="text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1 block">Cantidad Recibida</label>
                        <input type="number"
                               v-model.number="item.quantity_to_receive"
                               :max="item.quantity_ordered"
                               min="0"
                               step="1"
                               :disabled="item.received_all"
                               class="w-32 px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                      </div>
                    </div>

                    <!-- Delete Button -->
                    <button @click="removeProduct(index)"
                            class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                            title="Eliminar producto">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
              <div class="flex items-center justify-end gap-3">
                <button @click="closeReceiveModal"
                        class="px-4 py-2 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors font-medium">
                  Cancelar
                </button>
                <button @click="confirmReceive"
                        :disabled="receivingMerchandise"
                        class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                  <svg v-if="receivingMerchandise" class="w-4 h-4 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ receivingMerchandise ? 'Procesando...' : 'Confirmar Ingreso' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

      </div>
      <!-- Fin TAB: ÓRDENES DE COMPRA -->

    <!-- Modal Premium Feature -->
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
                @click="navigateToPlans"
                class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-base font-semibold rounded-xl transition-colors duration-200 shadow-lg"
              >
                Ver Planes
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal Solicitar Email -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showEmailModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/60 " @click="cancelEmail"></div>
          <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-zinc-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Ingresar Email
              </h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">El proveedor no tiene email registrado</p>
            </div>
            <div class="px-6 py-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Email del proveedor</label>
              <input 
                v-model="emailInput"
                type="email"
                placeholder="ejemplo@proveedor.com"
                class="w-full px-4 py-3 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                @keyup.enter="confirmEmail"
              />
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-800/50 flex items-center justify-end gap-3">
              <button @click="cancelEmail" class="px-4 py-2 text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                Cancelar
              </button>
              <button @click="confirmEmail" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all">
                Enviar
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Modal Solicitar Teléfono -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showPhoneModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/60 " @click="cancelPhone"></div>
          <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-zinc-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                Ingresar Teléfono
              </h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">El proveedor no tiene teléfono registrado</p>
            </div>
            <div class="px-6 py-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Teléfono del proveedor</label>
              <input 
                v-model="phoneInput"
                type="tel"
                placeholder="3001234567"
                class="w-full px-4 py-3 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-transparent transition-all"
                @keyup.enter="confirmPhone"
              />
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-800/50 flex items-center justify-end gap-3">
              <button @click="cancelPhone" class="px-4 py-2 text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                Cancelar
              </button>
              <button @click="confirmPhone" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all">
                Enviar
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    

    <!-- Modal: ¿Cómo deseas enviar la orden? -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showSendOptionsModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/60 " @click="closeSendOptionsModal"></div>
          <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-zinc-700 overflow-hidden animate-scale-in">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200 dark:border-zinc-700 text-center">
              <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 dark:text-white">¡Orden Creada!</h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">¿Cómo deseas enviarla al proveedor?</p>
            </div>
            
            <!-- Opciones de envío -->
            <div class="px-6 py-5 space-y-3">
              <!-- Enviar por Email -->
              <button 
                @click="sendNewOrderByEmail"
                class="w-full flex items-center gap-4 p-4 bg-gray-50 dark:bg-zinc-800/50 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl border border-gray-200 dark:border-zinc-700 hover:border-blue-300 dark:hover:border-blue-800 transition-all duration-200 group"
              >
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                  <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div class="text-left flex-1">
                  <p class="font-semibold text-gray-900 dark:text-white">Enviar por Email</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">Se enviará un PDF al correo del proveedor</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 dark:text-zinc-500 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>

              <!-- Enviar por WhatsApp -->
              <button 
                @click="sendNewOrderByWhatsApp"
                class="w-full flex items-center gap-4 p-4 bg-gray-50 dark:bg-zinc-800/50 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-xl border border-gray-200 dark:border-zinc-700 hover:border-emerald-300 dark:hover:border-emerald-800 transition-all duration-200 group"
              >
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                  <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                  </svg>
                </div>
                <div class="text-left flex-1">
                  <p class="font-semibold text-gray-900 dark:text-white">Enviar por WhatsApp</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">Se abrirá WhatsApp con el PDF adjunto</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 dark:text-zinc-500 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-800/50 border-t border-gray-200 dark:border-zinc-700">
              <button 
                @click="closeSendOptionsModal"
                class="w-full py-3 text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white font-medium transition-colors flex items-center justify-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Enviar después
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

  </div>
</template>

<script>
import { apiCall } from '../services/api.js'
import { generatePurchaseOrderPDF, downloadPDF, getPDFBlob } from '../utils/pdfTemplates/pdfGenerator'
import SuppliersViewMasterDetail from './SuppliersView_MasterDetail.vue'
import { appStore } from '../store/appStore.js'
import { invoicesService } from '../services/invoicesService.js'
import { whatsappService } from '../services/whatsappService.js'
import { useModuleNavigation } from '../composables/useModuleNavigation.js'
import { useToast } from '../composables/useToast.js'

// Obtener función de navegación
const { navigateToModule } = useModuleNavigation()

// Obtener funciones de toast (global)
const { showToast, showSuccess, showError, showWarning, showInfo } = useToast()

export default {
  name: 'PurchaseOrdersViewMasterDetail',
  components: {
    SuppliersViewMasterDetail
  },
  data() {
    return {
      activeTab: 'suppliers', // 'suppliers' | 'orders'
      loading: false,
      orders: [],
      selectedOrder: null,
      filterStatus: 'all',
      warehouses: [],
      viewMode: 'list', // 'list' | 'create' | 'create-supplier'
      
      // Premium/Email/WhatsApp Modals
      showPremiumModal: false,
      premiumFeatureName: '',
      showEmailModal: false,
      emailInput: '',
      emailModalResolve: null,
      showPhoneModal: false,
      phoneInput: '',
      phoneModalResolve: null,
      sendingEmail: false,
      
      
      // Modal de opciones de envío después de crear orden
      showSendOptionsModal: false,
      pendingOrderToSend: null,
      
      // Receive Modal
      showReceiveModal: false,
      receivingMerchandise: false,
      receiveForm: {
        items: []
      },
      
      // Create Order Form
      suppliers: [],
      products: [],
      loadingProducts: false,
      showProductSelector: false,
      productSearch: '',
      savingOrder: false,
      
      // 👗 NUEVO: Selector de variantes para productos moda
      showVariantSelector: false,
      selectedProductForVariant: null,
      productVariants: [],
      loadingVariants: false,
      
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
      tenantPlan: 'free_trial',
      
      // Create Supplier Form
      supplierForm: {
        name: '',
        email: '',
        phone: '',
        document: '',
        address: '',
        city: '',
        contact_name: '',
        contact_phone: '',
        notes: ''
      },
      supplierErrors: {},
      savingSupplier: false,
      
      statusFilters: [
        { value: 'all', label: 'Todas' },
        { value: 'pending', label: 'Pendientes' },
        { value: 'partial', label: 'Parciales' },
        { value: 'received', label: 'Recibidas' }
      ],
      
      // 🔗 QueryParams de navegación
      navigationParams: null
    }
  },
  computed: {
    filteredOrders() {
      if (this.filterStatus === 'all') {
        return this.orders
      }
      return this.orders.filter(order => order.status === this.filterStatus)
    },
    hasMultipleWarehouses() {
      return this.warehouses.length > 1
    },
    orderSubtotal() {
      return this.orderForm.items.reduce((sum, item) => {
        return sum + (item.quantity * item.unit_cost)
      }, 0)
    },
    filteredProducts() {
      let filtered = [...this.products]
      
      // Filtrar por proveedor si está seleccionado
      if (this.orderForm.supplier_id) {
        filtered = filtered.filter(p => p.supplier_id == this.orderForm.supplier_id)
      }
      
      // Filtrar por bodega si está seleccionada
      if (this.orderForm.warehouse_id) {
        filtered = filtered.filter(p => {
          if (p.warehouse_id == this.orderForm.warehouse_id) return true
          if (p.warehouse_stocks && p.warehouse_stocks.some(ws => ws.warehouse_id == this.orderForm.warehouse_id)) return true
          return true
        })
      }
      
      // Filtrar por búsqueda de texto
      if (this.productSearch) {
        const search = this.productSearch.toLowerCase()
        filtered = filtered.filter(p => 
          p.name.toLowerCase().includes(search) || 
          p.sku?.toLowerCase().includes(search)
        )
      }
      
      // Ordenar: primero sin stock, luego stock bajo
      filtered.sort((a, b) => {
        const stockA = a.current_stock || 0
        const stockB = b.current_stock || 0
        if (stockA === 0 && stockB > 0) return -1
        if (stockB === 0 && stockA > 0) return 1
        return stockA - stockB
      })
      
      return filtered
    },
    shouldShowWarehouseSelector() {
      // ✅ Solo mostrar si el usuario tiene plan Premium/Enterprise Y hay más de 1 bodega
      const hasMultiSedesPlan = ['premium', 'enterprise'].includes(this.tenantPlan)
      const hasMultipleWarehouses = this.warehouses.length > 1
      
      return hasMultiSedesPlan && hasMultipleWarehouses
    }
  },
  mounted() {
    this.loadWarehouses()
    this.loadOrders()
    this.loadSuppliers()
    this.loadProducts()
    this.loadTenantPlan()

    // Escuchar evento de actualización de productos
    window.addEventListener('products-updated', this.handleProductsUpdate)
    // Handler para tecla ESC - deseleccionar orden
    document.addEventListener('keydown', this.handleKeyDown)
    
    // 🔗 Registrar callback para navegación con queryParams
    const { onModuleChange, currentQueryGlobal } = useModuleNavigation()
    
    // Verificar si hay queryParams actuales al montar
    if (currentQueryGlobal.value && (currentQueryGlobal.value.supplierId || currentQueryGlobal.value.activeTab)) {
      this.navigationParams = { ...currentQueryGlobal.value }
      this.$nextTick(() => {
        this.processNavigationParams()
      })
    }
    
    // Escuchar cambios de navegación
    onModuleChange((module, params) => {
      if (module === 'purchase-orders' && params) {
        this.navigationParams = params
        this.processNavigationParams()
      }
    })
  },
  beforeUnmount() {
    window.removeEventListener('products-updated', this.handleProductsUpdate)
    document.removeEventListener('keydown', this.handleKeyDown)
  },
  methods: {
    handleKeyDown(event) {
      if (event.key === 'Escape' && this.selectedOrder && this.viewMode === 'list') {
        this.selectedOrder = null
      }
    },
    changeTab(tab) {
      this.activeTab = tab
      this.viewMode = 'list'
      this.selectedOrder = null
    },

    async refreshCurrentTab() {
      if (this.activeTab === 'orders') {
        await this.refreshOrders()
      }
      // Para suppliers, el componente SuppliersView maneja su propio refresh
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

    async loadOrders() {
      try {
        this.loading = true
        const response = await apiCall('/purchase-orders')
        console.log('📦 Response de órdenes:', response)
        if (response.success) {
          // El backend devuelve {data: {orders: [...], metrics: {...}}}
          this.orders = response.data?.orders || []
          console.log('✅ Órdenes cargadas:', this.orders.length, this.orders)
        } else {
          console.warn('⚠️ Response sin success:', response)
        }
      } catch (error) {
        console.error('❌ Error cargando órdenes:', error)
        showError('Error al cargar las órdenes')
      } finally {
        this.loading = false
      }
    },

    async refreshOrders() {
      await this.loadOrders()
      if (this.selectedOrder) {
        // Recargar orden seleccionada
        const updated = this.orders.find(o => o.id === this.selectedOrder.id)
        if (updated) {
          this.selectedOrder = updated
        }
      }
    },

    selectOrder(order) {
      this.selectedOrder = order
    },

    openReceiveModal() {
      if (!this.selectedOrder || !this.selectedOrder.items) {
        showWarning('No hay productos en esta orden')
        return
      }

      this.receiveForm.items = this.selectedOrder.items.map(item => ({
        item_id: item.id,
        product_name: item.product?.name || 'Producto',
        variant_id: item.variant_id || null,           // 👗 NUEVO
        variant_options: item.variant_options || null, // 👗 NUEVO
        quantity_ordered: item.quantity_ordered,
        quantity_received: item.quantity_received || 0,
        quantity_to_receive: 0,
        received_all: false
      }))

      this.showReceiveModal = true
    },

    closeReceiveModal() {
      this.showReceiveModal = false
      this.receiveForm.items = []
    },

    handleCheckboxChange(item) {
      if (item.received_all) {
        item.quantity_to_receive = item.quantity_ordered - item.quantity_received
      } else {
        item.quantity_to_receive = 0
      }
    },

    removeProduct(index) {
      this.receiveForm.items.splice(index, 1)
    },

    async confirmReceive() {
      const itemsToReceive = this.receiveForm.items.filter(item => item.quantity_to_receive > 0)
      
      if (itemsToReceive.length === 0) {
        showWarning('Ingresa al menos una cantidad a recibir')
        return
      }

      this.receivingMerchandise = true
      const orderId = this.selectedOrder?.id

      try {
        const response = await apiCall(`/purchase-orders/${orderId}/receive`, {
          method: 'POST',
          body: JSON.stringify({
            received_items: itemsToReceive.map(item => ({
              item_id: item.item_id,
              quantity: item.quantity_to_receive
            }))
          })
        })

        if (response.success) {
          showSuccess('Productos ingresados al stock correctamente')
          this.closeReceiveModal()
          await this.refreshOrders()

          // Disparar evento global para refrescar vistas de productos
          window.dispatchEvent(new CustomEvent('products-updated', {
            detail: { source: 'purchase-order-receive', orderId: orderId }
          }))
        }
      } catch (error) {
        console.error('Error ingresando productos:', error)
        showError(error.message || 'Error al ingresar productos')
      } finally {
        this.receivingMerchandise = false
      }
    },

    handleProductsUpdate() {
      // Recargar órdenes cuando se actualicen productos
      this.refreshOrders()
    },

    getStatusLabel(status) {
      const labels = {
        pending: 'Pendiente',
        partial: 'Parcial',
        received: 'Recibida'
      }
      return labels[status] || status
    },

    getStatusBadgeClass(status) {
      const classes = {
        pending: 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800',
        partial: 'bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800',
        received: 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800'
      }
      return classes[status] || 'bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-400'
    },

    formatDate(date) {
      if (!date) return 'N/A'
      return new Date(date).toLocaleDateString('es-ES', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
      })
    },

    formatCurrency(value) {
      if (!value) return '0'
      return Number(value).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
    },

    formatNumber(value) {
      if (!value) return '0'
      return new Intl.NumberFormat('es-CO').format(value)
    },

    // 🔗 Procesar parámetros de navegación
    processNavigationParams() {
      if (!this.navigationParams) return
      
      const { activeTab, action, supplierId } = this.navigationParams
      
      // Cambiar al tab correspondiente si se especifica
      if (activeTab) {
        this.activeTab = activeTab
      }
      
      // Si la acción es 'create', abrir formulario con proveedor preseleccionado
      if (action === 'create' && supplierId) {
        this.activeTab = 'orders'
        this.viewMode = 'create'
        this.orderForm.supplier_id = supplierId
      }
      
      // Si la acción es 'view' y estamos en proveedores, seleccionar el proveedor
      // (Esto lo manejará el componente SuppliersViewMasterDetail hijo)
      if (action === 'view' && supplierId && activeTab === 'suppliers') {
        // Emitir evento al componente hijo de proveedores
        this.$nextTick(() => {
          // El SuppliersViewMasterDetail escuchará los queryParams globales
        })
      }
      
      // Limpiar params después de procesar
      this.navigationParams = null
    },

    // === CREATE ORDER METHODS ===
    
    async loadSuppliers() {
      try {
        const response = await apiCall('/suppliers/analytics')
        if (response.success) {
          this.suppliers = response.data.suppliers
          
          // 🔗 Si hay queryParams pendientes, procesarlos después de cargar
          if (this.navigationParams) {
            this.$nextTick(() => {
              this.processNavigationParams()
            })
          }
        }
      } catch (error) {
        console.error('Error cargando proveedores:', error)
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
      // Cargar plan del tenant desde appStore
      const plan = appStore.tenantPlan || appStore.systemSettings?.tenant_plan || 'free_trial'
      this.tenantPlan = plan
    },

    isBasicPlan() {
      const basicPlans = ['free_trial', 'free', 'basic']
      return basicPlans.includes(this.tenantPlan)
    },

    // 👗 NUEVO: Detectar si producto tiene variantes y mostrar selector
    async addProductToOrder(product) {
      // Si es producto con variantes (tipo moda), cargar variantes primero
      if (product.product_type === 'variable' || product.type === 'variable') {
        this.selectedProductForVariant = product
        this.loadingVariants = true
        this.showProductSelector = false
        
        try {
          const response = await apiCall(`/products/${product.id}`)
          if (response.data && response.data.variants && response.data.variants.length > 0) {
            this.productVariants = response.data.variants
            this.showVariantSelector = true
          } else {
            // No tiene variantes a pesar de ser variable, agregar como producto simple
            this.addSimpleProductToOrder(product)
          }
        } catch (error) {
          console.error('Error cargando variantes:', error)
          showError('Error al cargar las variantes del producto')
          this.addSimpleProductToOrder(product)
        } finally {
          this.loadingVariants = false
        }
        return
      }
      
      // Producto simple (sin variantes)
      this.addSimpleProductToOrder(product)
    },

    // 👗 Agregar producto simple (sin variantes)
    addSimpleProductToOrder(product) {
      const exists = this.orderForm.items.find(i => i.product_id === product.id && !i.variant_id)
      if (exists) {
        showWarning('Este producto ya está en la orden')
        return
      }
      
      this.orderForm.items.push({
        product_id: product.id,
        variant_id: null,
        variant_options: null,
        product: product,
        quantity: 1,
        unit_cost: product.cost_price || 0,
        notes: ''
      })
      
      this.showProductSelector = false
      this.productSearch = ''
    },

    // 👗 NUEVO: Agregar variante específica a la orden
    addVariantToOrder(variant) {
      const product = this.selectedProductForVariant
      
      // Verificar si ya existe esta variante en la orden
      const exists = this.orderForm.items.find(
        i => i.product_id === product.id && i.variant_id === variant.id
      )
      if (exists) {
        showWarning('Esta variante ya está en la orden')
        return
      }
      
      // Parsear options_summary si es string
      const optionsSummary = typeof variant.options_summary === 'string' 
        ? JSON.parse(variant.options_summary) 
        : variant.options_summary
      
      // Crear nombre legible de la variante
      const variantName = optionsSummary
        .map(opt => `${opt.name}: ${opt.value}`)
        .join(' | ')
      
      this.orderForm.items.push({
        product_id: product.id,
        variant_id: variant.id,
        variant_options: optionsSummary,
        variant_name: variantName,  // Para mostrar en la UI
        product: {
          ...product,
          name: `${product.name} (${variantName})`  // Nombre compuesto
        },
        quantity: 1,
        unit_cost: variant.cost_price || product.cost_price || 0,
        notes: ''
      })
      
      this.showVariantSelector = false
      this.selectedProductForVariant = null
      this.productVariants = []
      this.productSearch = ''
    },

    // 👗 Cerrar selector de variantes
    closeVariantSelector() {
      this.showVariantSelector = false
      this.selectedProductForVariant = null
      this.productVariants = []
    },

    // 👗 Helper para formatear opciones de variante
    formatVariantOptions(options) {
      if (!options) return ''
      const parsed = typeof options === 'string' ? JSON.parse(options) : options
      return parsed.map(opt => `${opt.name}: ${opt.value}`).join(' | ')
    },

    removeOrderItem(index) {
      this.orderForm.items.splice(index, 1)
    },

    calculateItemTotal(index) {
      const item = this.orderForm.items[index]
      item.total = item.quantity * item.unit_cost
    },

    cancelCreateOrder() {
      this.viewMode = 'list'
      this.resetOrderForm()
    },

    resetOrderForm() {
      this.orderForm = {
        supplier_id: '',
        warehouse_id: this.warehouses.length > 0 ? this.warehouses[0].id : '',
        order_date: new Date().toISOString().split('T')[0],
        expected_date: '',
        reference: '',
        notes: '',
        items: []
      }
      this.orderErrors = {}
    },

    async saveOrderAsDraft() {
      await this.saveOrder('draft')
    },

    async saveOrderAsPending() {
      await this.saveOrder('pending')
    },

    async saveOrder(status, returnOrder = false) {
      this.orderErrors = {}
      
      // Validaciones
      if (!this.orderForm.supplier_id) {
        this.orderErrors.supplier_id = 'Selecciona un proveedor'
        return null
      }
      
      if (!this.orderForm.order_date) {
        this.orderErrors.order_date = 'La fecha de orden es requerida'
        return null
      }
      
      if (this.orderForm.items.length === 0) {
        this.orderErrors.items = 'Agrega al menos un producto'
        return null
      }
      
      this.savingOrder = true
      try {
        const payload = {
          ...this.orderForm,
          items: this.orderForm.items.map(item => ({
            product_id: item.product_id,
            variant_id: item.variant_id || null,           // 👗 NUEVO
            variant_options: item.variant_options || null, // 👗 NUEVO
            quantity: item.quantity,
            unit_cost: item.unit_cost,
            notes: item.notes
          }))
        }
        
        const response = await apiCall('/purchase-orders', {
          method: 'POST',
          body: JSON.stringify(payload)
        })
        
        if (response.success) {
          // Si se guardó como pending, actualizar el estado
          if (status === 'pending') {
            await apiCall(`/purchase-orders/${response.data.id}/status`, {
              method: 'POST',
              body: JSON.stringify({ status: 'pending' })
            })
          }
          
          // Si queremos retornar la orden para enviarla
          if (returnOrder) {
            await this.loadOrders()
            // Buscar la orden recién creada
            const createdOrder = this.orders.find(o => o.id === response.data.id)
            return createdOrder || response.data
          }
          
          showSuccess(response.message || 'Orden guardada exitosamente')
          this.viewMode = 'list'
          this.resetOrderForm()
          await this.loadOrders()
          return response.data
        }
        return null
      } catch (error) {
        console.error('Error guardando orden:', error)
        showError(error.message || 'Error al guardar orden')
        return null
      } finally {
        this.savingOrder = false
      }
    },

    // Guardar orden y mostrar opciones de envío
    async saveAndShowSendOptions() {
      const savedOrder = await this.saveOrder('pending', true)
      if (savedOrder) {
        this.pendingOrderToSend = savedOrder
        this.viewMode = 'list'
        this.resetOrderForm()
        this.showSendOptionsModal = true
      }
    },

    closeSendOptionsModal() {
      this.showSendOptionsModal = false
      this.pendingOrderToSend = null
      showSuccess('Orden creada exitosamente')
    },

    async sendNewOrderByEmail() {
      if (!this.pendingOrderToSend) return
      
      // Primero cerrar modal y seleccionar la orden
      this.showSendOptionsModal = false
      this.selectedOrder = this.pendingOrderToSend
      this.pendingOrderToSend = null
      
      // Esperar un tick para que se actualice la UI
      await this.$nextTick()
      
      // Llamar la función existente de envío por email
      await this.sendOrderByEmail()
    },

    async sendNewOrderByWhatsApp() {
      if (!this.pendingOrderToSend) return
      
      // Primero cerrar modal y seleccionar la orden
      this.showSendOptionsModal = false
      this.selectedOrder = this.pendingOrderToSend
      this.pendingOrderToSend = null
      
      // Esperar un tick para que se actualice la UI
      await this.$nextTick()
      
      // Llamar la función existente de envío por WhatsApp
      await this.sendOrderByWhatsApp()
    },

    // ========== FUNCIONES DE ACCIONES ==========
    async downloadOrderPDF() {
      try {
        if (!this.selectedOrder) {
          showWarning('Selecciona una orden primero')
          return
        }

        showInfo('Generando PDF...')

        // Cargar configuración del sistema desde el backend
        const settingsResponse = await apiCall('/system-settings')
        const settings = settingsResponse.data || {}

        // Preparar datos para el PDF
        const orderData = {
          order_number: this.selectedOrder.order_number,
          order_date: this.selectedOrder.order_date,
          expected_date: this.selectedOrder.expected_date,
          supplier_name: this.selectedOrder.supplier?.name || 'Sin proveedor',
          supplier_email: this.selectedOrder.supplier?.email || '',
          supplier_phone: this.selectedOrder.supplier?.phone || '',
          warehouse_name: this.selectedOrder.warehouse?.name || '',
          items: (this.selectedOrder.items || []).map(item => ({
            product_name: item.product?.name || item.product_name || 'Producto sin nombre',
            quantity: item.quantity_ordered || item.quantity || 0,
            unit_cost: item.unit_cost || 0
          })),
          subtotal: parseFloat(this.selectedOrder.subtotal || 0),
          tax: parseFloat(this.selectedOrder.tax || 0),
          total: parseFloat(this.selectedOrder.total || 0),
          notes: this.selectedOrder.notes || '',
          status: this.selectedOrder.status
        }

        // Configuración del sistema desde la base de datos
        const systemSettings = {
          company_name: settings.company_name || 'MI EMPRESA',
          company_address: settings.company_address || '',
          company_phone: settings.company_phone || '',
          company_email: settings.company_email || '',
          company_document: settings.company_document || ''
        }

        // Generar y descargar PDF
        const pdf = generatePurchaseOrderPDF(orderData, systemSettings)
        downloadPDF(pdf, `orden-compra-${this.selectedOrder.order_number}.pdf`)
        
        showSuccess('PDF descargado correctamente')
        
      } catch (error) {
        console.error('Error descargando PDF:', error)
        showError('Error al generar PDF')
      }
    },

    async sendOrderByEmail() {
      try {
        // Verificar plan antes de procesar
        if (this.isBasicPlan()) {
          this.premiumFeatureName = 'Envío por Email'
          this.showPremiumModal = true
          return
        }

        if (!this.selectedOrder) {
          showWarning('Selecciona una orden primero')
          return
        }

        // IMPORTANTE: Recargar órdenes para obtener datos frescos del proveedor
        await this.refreshOrders()
        
        let supplierEmail = this.selectedOrder.supplier?.email
        
        // Si no tiene email, pedirlo con el modal
        if (!supplierEmail || supplierEmail.trim() === '') {
          supplierEmail = await this.requestEmail()
          if (!supplierEmail) {
            return // Usuario canceló
          }
        }

        this.sendingEmail = true
        showInfo('Generando y enviando orden de compra...')

        // Preparar datos de la orden
        const settings = appStore.systemSettings || {}
        const orderData = {
          order_number: this.selectedOrder.order_number,
          order_date: this.formatDate(this.selectedOrder.order_date),
          expected_date: this.selectedOrder.expected_date ? this.formatDate(this.selectedOrder.expected_date) : null,
          supplier: {
            name: this.selectedOrder.supplier?.name || 'Proveedor',
            email: supplierEmail,
            phone: this.selectedOrder.supplier?.phone || '',
            document: this.selectedOrder.supplier?.document || '',
            address: this.selectedOrder.supplier?.address || ''
          },
          items: (this.selectedOrder.items || []).map(item => ({
            name: item.product?.name || item.product_name || 'Producto',
            sku: item.product?.sku || '',
            quantity: item.quantity_ordered || item.quantity || 0,
            unit_cost: item.unit_cost || 0
          })),
          subtotal: parseFloat(this.selectedOrder.subtotal || 0),
          tax: parseFloat(this.selectedOrder.tax || 0),
          total: parseFloat(this.selectedOrder.total || 0),
          notes: this.selectedOrder.notes || '',
          status: this.selectedOrder.status
        }

        const systemSettings = {
          company_name: settings.company_name || 'MI EMPRESA',
          company_address: settings.company_address || '',
          company_phone: settings.company_phone || '',
          company_email: settings.company_email || '',
          company_document: settings.company_document || ''
        }

        // Generar PDF
        const pdf = generatePurchaseOrderPDF(orderData, systemSettings)
        const pdfBlob = await getPDFBlob(pdf)

        // Enviar email usando el servicio de facturas (reutilizamos la lógica)
        await invoicesService.sendPurchaseOrderEmail(
          this.selectedOrder.id,
          supplierEmail,
          pdfBlob,
          this.selectedOrder.order_number
        )
        
        showSuccess(`✅ Orden enviada exitosamente a ${supplierEmail}`)
        
      } catch (error) {
        console.error('Error enviando email:', error)
        showError('Error al enviar email')
      } finally {
        this.sendingEmail = false
      }
    },

    async sendOrderByWhatsApp() {
      try {
        // Verificar plan antes de procesar
        if (this.isBasicPlan()) {
          this.premiumFeatureName = 'Envío por WhatsApp'
          this.showPremiumModal = true
          return
        }

        if (!this.selectedOrder) {
          showWarning('Selecciona una orden primero')
          return
        }

        // IMPORTANTE: Recargar órdenes para obtener datos frescos del proveedor
        await this.refreshOrders()

        let supplierPhone = this.selectedOrder.supplier?.phone
        
        // Si no tiene teléfono, pedirlo con el modal
        if (!supplierPhone || supplierPhone.trim() === '') {
          supplierPhone = await this.requestPhone()
          if (!supplierPhone) {
            return // Usuario canceló
          }
        }

        showInfo('Preparando WhatsApp...')

        // Preparar datos de la orden
        const settings = appStore.systemSettings || {}
        const orderData = {
          order_number: this.selectedOrder.order_number,
          order_date: this.formatDate(this.selectedOrder.order_date),
          expected_date: this.selectedOrder.expected_date ? this.formatDate(this.selectedOrder.expected_date) : null,
          supplier: {
            name: this.selectedOrder.supplier?.name || 'Proveedor',
            email: this.selectedOrder.supplier?.email || '',
            phone: supplierPhone,
            document: this.selectedOrder.supplier?.document || '',
            address: this.selectedOrder.supplier?.address || ''
          },
          items: (this.selectedOrder.items || []).map(item => ({
            name: item.product?.name || item.product_name || 'Producto',
            sku: item.product?.sku || '',
            quantity: item.quantity_ordered || item.quantity || 0,
            unit_cost: item.unit_cost || 0
          })),
          subtotal: parseFloat(this.selectedOrder.subtotal || 0),
          tax: parseFloat(this.selectedOrder.tax || 0),
          total: parseFloat(this.selectedOrder.total || 0),
          notes: this.selectedOrder.notes || '',
          status: this.selectedOrder.status
        }

        const systemSettings = {
          company_name: settings.company_name || 'MI EMPRESA',
          company_address: settings.company_address || '',
          company_phone: settings.company_phone || '',
          company_email: settings.company_email || '',
          company_document: settings.company_document || ''
        }

        // Generar PDF
        const pdf = generatePurchaseOrderPDF(orderData, systemSettings)
        const pdfBlob = await getPDFBlob(pdf)

        // Enviar por WhatsApp con nombre del proveedor
        await whatsappService.sendDocumentByWhatsApp(
          supplierPhone, 
          pdfBlob, 
          this.selectedOrder.order_number, 
          'purchase_order',
          this.selectedOrder.supplier?.name || 'Proveedor'
        )
        
        showSuccess('✅ Orden enviada por WhatsApp exitosamente')
        
      } catch (error) {
        console.error('Error enviando WhatsApp:', error)
        showError('Error al enviar por WhatsApp')
      }
    },

    // Solicitar email mediante modal
    requestEmail() {
      return new Promise((resolve) => {
        this.emailInput = ''
        this.emailModalResolve = resolve
        this.showEmailModal = true
      })
    },

    // Confirmar email del modal
    confirmEmail() {
      const email = this.emailInput.trim()
      
      // Validar formato de email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!emailRegex.test(email)) {
        showWarning('Email inválido')
        return
      }
      
      this.showEmailModal = false
      if (this.emailModalResolve) {
        this.emailModalResolve(email)
        this.emailModalResolve = null
      }
    },

    // Cancelar modal de email
    cancelEmail() {
      this.showEmailModal = false
      if (this.emailModalResolve) {
        this.emailModalResolve(null)
        this.emailModalResolve = null
      }
    },

    // Solicitar teléfono mediante modal
    requestPhone() {
      return new Promise((resolve) => {
        this.phoneInput = ''
        this.phoneModalResolve = resolve
        this.showPhoneModal = true
      })
    },

    // Confirmar teléfono del modal
    confirmPhone() {
      const phone = this.phoneInput.trim()
      
      if (phone.length < 7) {
        showWarning('Número de teléfono inválido')
        return
      }
      
      this.showPhoneModal = false
      if (this.phoneModalResolve) {
        this.phoneModalResolve(phone)
        this.phoneModalResolve = null
      }
    },

    // Cancelar modal de teléfono
    cancelPhone() {
      this.showPhoneModal = false
      if (this.phoneModalResolve) {
        this.phoneModalResolve(null)
        this.phoneModalResolve = null
      }
    },

    

    // Navegar a configuración de planes
    navigateToPlans() {
      this.showPremiumModal = false
      // Usar el composable de navegación
      navigateToModule('settings', { section: 'plans' })
    },

    async markOrderAsPaid() {
      try {
        if (!this.selectedOrder) {
          showWarning('Selecciona una orden primero')
          return
        }

        if (this.selectedOrder.status !== 'pending') {
          showWarning('Solo se pueden marcar como pagadas las órdenes pendientes')
          return
        }

        // Abrir modal de recepción para ingresar productos al stock
        this.openReceiveModal()
      } catch (error) {
        console.error('Error marcando como pagada:', error)
        showError(error.message || 'Error al actualizar estado')
      }
    },

    // ========== FUNCIONES DE PROVEEDORES ==========
    
    cancelCreateSupplier() {
      this.viewMode = 'list'
      this.resetSupplierForm()
    },

    resetSupplierForm() {
      this.supplierForm = {
        name: '',
        email: '',
        phone: '',
        document: '',
        address: '',
        city: '',
        contact_name: '',
        contact_phone: '',
        notes: ''
      }
      this.supplierErrors = {}
    },

    async saveSupplier() {
      this.supplierErrors = {}
      
      // Validación
      if (!this.supplierForm.name || this.supplierForm.name.trim() === '') {
        this.supplierErrors.name = 'El nombre del proveedor es requerido'
        showWarning('El nombre del proveedor es requerido')
        return
      }
      
      this.savingSupplier = true
      try {
        const response = await apiCall('/suppliers', {
          method: 'POST',
          body: JSON.stringify(this.supplierForm)
        })
        
        if (response.success) {
          showSuccess('Proveedor creado exitosamente')
          this.viewMode = 'list'
          this.resetSupplierForm()
          await this.loadSuppliers()
          // Refrescar la lista de proveedores en el componente hijo
          if (this.$refs.suppliersView) {
            this.$refs.suppliersView.loadSuppliers?.()
          }
        }
      } catch (error) {
        console.error('Error guardando proveedor:', error)
        showError(error.message || 'Error al guardar proveedor')
      } finally {
        this.savingSupplier = false
      }
    }
  }
}
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
  transition: transform 0.3s ease;
}

.modal-enter-from .bg-white {
  transform: scale(0.95);
}

.modal-leave-to .bg-white {
  transform: scale(0.95);
}
</style>
