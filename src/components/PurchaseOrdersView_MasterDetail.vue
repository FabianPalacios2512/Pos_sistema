<template>
  <div class="h-full flex flex-col bg-white dark:bg-[#131314]">
    <div class="flex-1 flex flex-col p-6 space-y-5 overflow-hidden">
      
      <!-- Header Simple y Elegante -->
      <div class="flex-none flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Gestión de Compras</h1>
        
        <div class="flex items-center gap-3">
          <button @click="refreshCurrentTab"
                  :disabled="loading"
                  class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-[#f8f9fa] dark:bg-[#1e1f20] hover:bg-gray-200 dark:hover:bg-[#2a2b2e] rounded-full transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Actualizar</span>
          </button>
          
          <button v-if="activeTab === 'suppliers'"
                  @click="viewMode = 'create-supplier'"
                  class="px-4 py-2 text-sm font-medium text-white dark:text-gray-900 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 rounded-full transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nuevo Proveedor</span>
          </button>

          <button v-if="activeTab === 'orders'"
                  @click="viewMode = 'create'"
                  class="px-4 py-2 text-sm font-medium text-white dark:text-gray-900 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 rounded-full transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nueva Orden de Compra</span>
          </button>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="flex-none flex items-center gap-1 bg-gray-100 dark:bg-[#1e1f20] p-1 rounded-full w-fit">
        <button 
          @click="changeTab('suppliers')" 
          :class="[
            'px-4 py-2 text-sm font-medium rounded-full transition-colors flex items-center gap-2',
            activeTab === 'suppliers' 
              ? 'bg-white dark:bg-[#282a2c] text-gray-900 dark:text-white' 
              : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
          ]">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <span>Proveedores</span>
        </button>
        <button 
          @click="changeTab('orders')" 
          :class="[
            'px-4 py-2 text-sm font-medium rounded-full transition-colors flex items-center gap-2',
            activeTab === 'orders' 
              ? 'bg-white dark:bg-[#282a2c] text-gray-900 dark:text-white' 
              : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
          ]">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          <span>Órdenes de Compra</span>
        </button>
      </div>

      <!-- TAB: PROVEEDORES -->
      <div v-if="activeTab === 'suppliers'" class="flex-1 flex flex-col min-h-0">
        <!-- Mostrar lista de proveedores -->
        <div v-if="viewMode === 'list'" class="flex-1 flex flex-col min-h-0">
          <SuppliersViewMasterDetail ref="suppliersView" @supplier-selected="onSupplierSelected" />
        </div>

        <!-- Crear nuevo proveedor (inline form) -->
        <div v-else-if="viewMode === 'create-supplier'" class="animate-fade-in">
          <div class="bg-[#f8f9fa] dark:bg-[#1a1a1d] rounded-xl overflow-hidden">
            
            <!-- Header -->
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 dark:border-gray-800">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Nuevo Proveedor</h3>
              </div>
              <button @click="cancelCreateSupplier" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Form -->
            <div class="px-5 py-4 space-y-4 bg-white dark:bg-[#212124]">
              <!-- Información Básica -->
              <div>
                <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wide">Información del Proveedor</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                  <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre del Proveedor *</label>
                    <input
                      v-model="supplierForm.name"
                      type="text"
                      placeholder="Ej: Distribuidora XYZ"
                      :class="['w-full px-3 py-2 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white rounded-lg border focus:ring-1 focus:border-gray-400 transition-colors', supplierErrors.name ? 'border-red-500 focus:ring-red-500' : 'border-gray-200 dark:border-gray-700 focus:ring-gray-400']"
                    />
                    <p v-if="supplierErrors.name" class="mt-1 text-xs text-red-500">{{ supplierErrors.name }}</p>
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Documento (NIT/CC)</label>
                    <input
                      v-model="supplierForm.document"
                      type="text"
                      placeholder="123456789-0"
                      class="w-full px-3 py-2 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-1 focus:ring-gray-400 focus:border-gray-400 transition-colors"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Teléfono</label>
                    <input
                      v-model="supplierForm.phone"
                      type="text"
                      placeholder="3001234567"
                      class="w-full px-3 py-2 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-1 focus:ring-gray-400 focus:border-gray-400 transition-colors"
                    />
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                    <input
                      v-model="supplierForm.email"
                      type="email"
                      placeholder="email@proveedor.com"
                      class="w-full px-3 py-2 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-1 focus:ring-gray-400 focus:border-gray-400 transition-colors"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ciudad</label>
                    <input
                      v-model="supplierForm.city"
                      type="text"
                      placeholder="Bogotá"
                      class="w-full px-3 py-2 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-1 focus:ring-gray-400 focus:border-gray-400 transition-colors"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Dirección</label>
                    <input
                      v-model="supplierForm.address"
                      type="text"
                      placeholder="Calle 123 #45-67"
                      class="w-full px-3 py-2 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-1 focus:ring-gray-400 focus:border-gray-400 transition-colors"
                    />
                  </div>
                </div>
              </div>

              <!-- Contacto y Notas en grid -->
              <div class="grid grid-cols-2 gap-3">
                <!-- Persona de Contacto -->
                <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-lg p-3 border border-gray-100 dark:border-gray-800">
                  <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wide">Persona de Contacto</h4>
                  <div class="space-y-2.5">
                    <div>
                      <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
                      <input
                        v-model="supplierForm.contact_name"
                        type="text"
                        placeholder="Juan Pérez"
                        class="w-full px-3 py-2 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-1 focus:ring-gray-400 focus:border-gray-400 transition-colors"
                      />
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                      <input
                        v-model="supplierForm.contact_phone"
                        type="text"
                        placeholder="3009876543"
                        class="w-full px-3 py-2 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-1 focus:ring-gray-400 focus:border-gray-400 transition-colors"
                      />
                    </div>
                  </div>
                </div>

                <!-- Notas -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notas Adicionales</label>
                  <textarea
                    v-model="supplierForm.notes"
                    rows="4"
                    placeholder="Información adicional sobre el proveedor..."
                    class="w-full px-3 py-2 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-1 focus:ring-gray-400 focus:border-gray-400 transition-colors resize-none"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="bg-[#f8f9fa] dark:bg-[#1a1a1d] border-t border-gray-200 dark:border-gray-800 px-5 py-3 flex justify-end gap-3">
              <button @click="cancelCreateSupplier" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-[#212124] hover:bg-gray-100 dark:hover:bg-[#2a2a2d] rounded-lg border border-gray-200 dark:border-gray-700 transition-colors">
                Cancelar
              </button>
              <button @click="saveSupplier" :disabled="savingSupplier" class="px-4 py-2 text-sm font-medium text-white dark:text-gray-900 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors">
                {{ savingSupplier ? 'Guardando...' : 'Guardar Proveedor' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB: ÓRDENES DE COMPRA -->
      <div v-if="activeTab === 'orders'" class="flex-1 flex flex-col min-h-0">

      <!-- Master-Detail Layout Enterprise: 30/70 -->
      <div v-if="viewMode === 'list'" class="flex-1 flex rounded-xl overflow-hidden shadow-lg border border-gray-200 dark:border-gray-800">
        <div class="grid grid-cols-1 lg:grid-cols-10 h-full w-full">
        
        <!-- PANEL IZQUIERDO: Lista de Órdenes (30%) -->
        <div class="lg:col-span-3 overflow-hidden flex flex-col bg-white dark:bg-[#18181b] relative">
          <!-- Sombra lateral para dar profundidad -->
          <div class="absolute inset-y-0 right-0 w-px bg-gradient-to-b from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700"></div>
            
            <!-- Filtros -->
            <div class="flex-none p-4 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-gray-50 to-white dark:from-[#1a1a1d] dark:to-[#18181b]">
              <div class="flex items-center gap-1.5 flex-wrap">
                <button v-for="status in statusFilters" :key="status.value"
                        @click="filterStatus = status.value"
                        :class="[
                          'px-3.5 py-1.5 text-xs font-medium rounded-full transition-all duration-200',
                          filterStatus === status.value
                            ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-md'
                            : 'bg-gray-100 dark:bg-[#252528] text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-[#2d2d30] hover:shadow-sm'
                        ]">
                  {{ status.label }}
                </button>
              </div>
            </div>

            <!-- Lista de órdenes -->
            <div class="flex-1 overflow-y-auto bg-gray-50/50 dark:bg-[#131316] px-3 py-2">
              <!-- Loading -->
              <div v-if="loading" class="flex-1 flex items-center justify-center py-8">
                <div class="w-8 h-8 border-2 border-gray-300 dark:border-gray-600 border-t-gray-900 dark:border-t-white rounded-full animate-spin"></div>
              </div>

              <!-- Empty -->
              <div v-else-if="filteredOrders.length === 0" class="flex-1 flex flex-col items-center justify-center py-12">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800/50 flex items-center justify-center mb-4">
                  <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">Sin órdenes</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">No hay órdenes con este filtro</p>
              </div>

              <!-- Order Cards -->
              <div v-else class="space-y-2">
                <div v-for="order in filteredOrders" :key="order.id"
                     @click="selectOrder(order)"
                     :class="[
                       'p-3.5 cursor-pointer transition-all duration-200 rounded-lg border',
                       selectedOrder?.id === order.id
                         ? 'bg-white dark:bg-[#1e1f22] border-gray-900 dark:border-white shadow-md ring-1 ring-gray-900/5 dark:ring-white/10'
                         : 'bg-white dark:bg-[#1a1a1d] border-gray-100 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm'
                     ]">
                  
                  <div class="flex items-start justify-between mb-2">
                    <div class="flex-1 min-w-0 mr-3">
                      <p class="font-semibold text-sm text-gray-900 dark:text-white truncate">{{ order.order_number || 'Sin número' }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ order.supplier?.name || 'Sin proveedor' }}</p>
                    </div>
                    <span :class="getStatusBadgeClass(order.status)"
                          class="px-2.5 py-1 rounded-full text-[10px] font-semibold flex-shrink-0 uppercase tracking-wide">
                      {{ getStatusLabel(order.status) }}
                    </span>
                  </div>

                  <div class="flex items-center justify-between text-xs mt-3">
                    <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                      {{ formatDate(order.order_date) }}
                    </span>
                    <span class="font-bold text-gray-900 dark:text-white">${{ formatCurrency(order.total) }}</span>
                  </div>

                  <div v-if="order.items && order.items.length > 0" class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/50">
                    <div class="flex items-center justify-between text-xs">
                      <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        {{ order.items.length }} producto(s)
                      </span>
                      <!-- Indicador visual de selección -->
                      <svg v-if="selectedOrder?.id === order.id" class="w-4 h-4 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        <!-- PANEL DERECHO: Detalles de la Orden (70%) -->
        <div class="lg:col-span-7 overflow-hidden flex flex-col bg-gradient-to-br from-white via-gray-50/30 to-gray-100/50 dark:from-[#0f0f11] dark:via-[#131316] dark:to-[#0f0f11]">
          <!-- Empty State con diseño mejorado -->
          <div v-if="!selectedOrder" class="flex-1 flex flex-col items-center justify-center p-12 text-center relative">
              
              <!-- Fondo decorativo sutil -->
              <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_30%,rgba(59,130,246,0.03),transparent_50%)] dark:bg-[radial-gradient(circle_at_50%_30%,rgba(59,130,246,0.05),transparent_50%)]"></div>
              
              <!-- Contenedor con glassmorphism -->
              <div class="relative z-10 bg-white/50 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-10 border border-gray-200/50 dark:border-white/10 shadow-lg max-w-md">
                <!-- Ilustración SVG profesional más pequeña -->
                <div class="mb-6 flex justify-center">
                  <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center shadow-inner">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                  </div>
                </div>
                
                <!-- Texto de bienvenida profesional -->
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">
                  Selecciona una orden
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-2">
                  Haz clic en una orden de la lista para ver sus detalles completos y gestionar la recepción de mercancía.
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                  Controla tus órdenes de compra de forma rápida y segura.
                </p>
                
                <!-- Indicador visual -->
                <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-400 dark:text-gray-500">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                  </svg>
                  <span>Selecciona una orden de la lista</span>
                </div>
              </div>
          </div>

          <!-- Order Details -->
          <div v-else class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header con estado -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-[#18181b]/80 backdrop-blur-sm flex-shrink-0">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1 min-w-0 mr-4">
                  <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ selectedOrder.order_number }}</h2>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mt-1.5 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    {{ selectedOrder.supplier?.name || 'Sin proveedor' }}
                  </p>
                </div>
                <span :class="getStatusBadgeClass(selectedOrder.status)"
                      class="px-3 py-1.5 rounded-full text-xs font-semibold flex-shrink-0 uppercase tracking-wide">
                  {{ getStatusLabel(selectedOrder.status) }}
                </span>
              </div>

              <!-- Info Grid -->
              <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-900/20 dark:to-blue-800/10 rounded-xl p-4 border border-blue-100 dark:border-blue-800/30">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-blue-500/10 dark:bg-blue-400/10 flex items-center justify-center">
                      <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                    </div>
                    <p class="text-xs font-medium text-blue-600 dark:text-blue-400">Fecha Orden</p>
                  </div>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatDate(selectedOrder.order_date) }}</p>
                </div>
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-900/20 dark:to-emerald-800/10 rounded-xl p-4 border border-emerald-100 dark:border-emerald-800/30">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/10 dark:bg-emerald-400/10 flex items-center justify-center">
                      <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Total</p>
                  </div>
                  <p class="text-lg font-bold text-gray-900 dark:text-white">${{ formatCurrency(selectedOrder.total) }}</p>
                </div>
                <!-- Mostrar bodega solo si hay múltiples bodegas -->
                <div v-if="hasMultipleWarehouses && selectedOrder.warehouse" class="bg-gradient-to-br from-purple-50 to-purple-100/50 dark:from-purple-900/20 dark:to-purple-800/10 rounded-xl p-4 border border-purple-100 dark:border-purple-800/30">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-purple-500/10 dark:bg-purple-400/10 flex items-center justify-center">
                      <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                      </svg>
                    </div>
                    <p class="text-xs font-medium text-purple-600 dark:text-purple-400">Bodega Destino</p>
                  </div>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ selectedOrder.warehouse.name }}</p>
                </div>
                <div v-if="selectedOrder.expected_date" class="bg-gradient-to-br from-amber-50 to-amber-100/50 dark:from-amber-900/20 dark:to-amber-800/10 rounded-xl p-4 border border-amber-100 dark:border-amber-800/30">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-500/10 dark:bg-amber-400/10 flex items-center justify-center">
                      <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <p class="text-xs font-medium text-amber-600 dark:text-amber-400">Fecha Esperada</p>
                  </div>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatDate(selectedOrder.expected_date) }}</p>
                </div>
              </div>

              <!-- Botones de Acción -->
              <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                <button
                  @click="downloadOrderPDF"
                  class="px-3 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#2a2a2d]"
                  title="Descargar PDF">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  Descargar
                </button>
                
                <button
                  v-if="selectedOrder.status === 'pending'"
                  @click="sendOrderByEmail"
                  class="px-3 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#2a2a2d]"
                  title="Enviar por Email">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                  Email
                </button>
                
                <button
                  v-if="selectedOrder.status === 'pending'"
                  @click="sendOrderByWhatsApp"
                  class="px-3 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#2a2a2d]"
                  title="Enviar por WhatsApp">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                  </svg>
                  WhatsApp
                </button>

                <button
                  v-if="selectedOrder.status === 'pending'"
                  @click="markOrderAsPaid"
                  class="px-3 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20"
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
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Productos de la Orden</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ selectedOrder.items?.length || 0 }} productos en esta orden</p>
                  </div>
                </div>
              </div>
              
              <div class="bg-white dark:bg-[#18181b] border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                  <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-[#1e1f22]">
                      <tr>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Producto</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Ordenado</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Recibido</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Progreso</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Precio Unit.</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                      <tr v-for="item in selectedOrder.items" :key="item.id"
                          class="hover:bg-gray-50 dark:hover:bg-[#2a2a2d] transition-colors">
                        <td class="px-4 py-3">
                          <div class="flex items-center gap-2">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ item.product?.name || 'Producto sin nombre' }}</p>
                            <!-- Badge de variante -->
                            <span v-if="item.variant_id" class="px-1.5 py-0.5 text-[9px] font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded">
                              Variante
                            </span>
                          </div>
                          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ item.product?.sku || 'Sin SKU' }}
                            <!-- Mostrar variante -->
                            <span v-if="item.variant_options" class="ml-1 text-purple-600 dark:text-purple-400">
                              • {{ formatVariantOptions(item.variant_options) }}
                            </span>
                          </p>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span class="inline-flex items-center justify-center w-10 h-10 bg-[#f8f9fa] dark:bg-[#2a2a2d] rounded-lg">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ item.quantity_ordered }}</span>
                          </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span :class="[
                            'inline-flex items-center justify-center w-10 h-10 rounded-lg text-sm font-medium',
                            item.quantity_received >= item.quantity_ordered
                              ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                              : item.quantity_received > 0
                                ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'
                                : 'bg-gray-100 dark:bg-[#2a2a2d] text-gray-400 dark:text-gray-500'
                          ]">
                            {{ item.quantity_received || 0 }}
                          </span>
                        </td>
                        <td class="px-4 py-3">
                          <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                              <div :class="[
                                'h-2 rounded-full transition-all duration-300',
                                item.quantity_received >= item.quantity_ordered
                                  ? 'bg-emerald-500'
                                  : item.quantity_received > 0
                                    ? 'bg-amber-500'
                                    : 'bg-gray-300 dark:bg-gray-600'
                              ]" :style="`width: ${Math.min(100, (item.quantity_received / item.quantity_ordered) * 100)}%`"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400 w-10 text-right">
                              {{ Math.round((item.quantity_received / item.quantity_ordered) * 100) }}%
                            </span>
                          </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                          <span class="text-sm text-gray-900 dark:text-white">${{ formatCurrency(item.unit_cost) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                          <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatCurrency(item.quantity_ordered * item.unit_cost) }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="p-6 border-t border-gray-200 dark:border-gray-800 bg-[#f8f9fa] dark:bg-[#1a1a1d] flex-shrink-0">
              <div class="flex items-center justify-between gap-3">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                  <span class="font-medium">Total:</span>
                  <span class="font-semibold text-lg text-gray-900 dark:text-white ml-2">${{ formatCurrency(selectedOrder.total) }}</span>
                </div>
                <div class="flex items-center gap-3">
                  <button v-if="selectedOrder.status === 'pending' || selectedOrder.status === 'partial'"
                          @click="openReceiveModal"
                          class="px-5 py-2.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 text-sm font-medium rounded-full transition-colors flex items-center">
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
        <div class="bg-white dark:bg-[#212124] rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
          
          <!-- Header -->
          <div class="border-b border-gray-200 dark:border-gray-800 px-6 py-4 flex items-center justify-between bg-[#f8f9fa] dark:bg-[#1a1a1d]">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nueva Orden de Compra</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400">Complete la información de la orden</p>
              </div>
            </div>
            <button @click="cancelCreateOrder" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Form -->
          <div class="px-6 py-4 space-y-4">
            <!-- Información General -->
            <div>
              <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-3 pb-1.5 border-b border-gray-200 dark:border-gray-800">Información de la Orden</h4>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                  <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Proveedor *</label>
                  <select v-model="orderForm.supplier_id" :class="['w-full px-3 py-2.5 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white rounded-lg border focus:ring-2 focus:border-transparent transition-colors', orderErrors.supplier_id ? 'border-red-500 dark:border-red-600 focus:ring-red-500 dark:focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-blue-500 dark:focus:ring-blue-400']">
                    <option value="">Seleccionar proveedor...</option>
                    <option v-for="supplier in suppliers.filter(s => s.active)" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                  </select>
                  <p v-if="orderErrors.supplier_id" class="mt-1 text-xs text-red-500 dark:text-red-400">{{ orderErrors.supplier_id }}</p>
                </div>

                <div v-if="shouldShowWarehouseSelector">
                  <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tienda/Sede *</label>
                  <select v-model="orderForm.warehouse_id" class="w-full px-3 py-2.5 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors">
                    <option value="">Seleccionar tienda/sede...</option>
                    <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">{{ warehouse.name }}</option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha de Orden *</label>
                  <input v-model="orderForm.order_date" type="date" :class="['w-full px-3 py-2.5 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white rounded-lg border focus:ring-2 focus:border-transparent transition-colors', orderErrors.order_date ? 'border-red-500 dark:border-red-600 focus:ring-red-500 dark:focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-blue-500 dark:focus:ring-blue-400']" />
                  <p v-if="orderErrors.order_date" class="mt-1 text-xs text-red-500 dark:text-red-400">{{ orderErrors.order_date }}</p>
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha Esperada</label>
                  <input v-model="orderForm.expected_date" type="date" class="w-full px-3 py-2.5 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors" />
                </div>

                <div class="md:col-span-2">
                  <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Referencia</label>
                  <input v-model="orderForm.reference" type="text" placeholder="OC-2024-001, Factura #123, etc." class="w-full px-3 py-2.5 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors" />
                </div>
              </div>
            </div>

            <!-- Productos -->
            <div>
              <div class="flex items-center justify-between mb-3 pb-1.5 border-b border-gray-200 dark:border-gray-800">
                <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300">Productos</h4>
                <button @click="showProductSelector = true" class="px-3 py-1.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 text-xs font-medium rounded-full transition-colors flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  Agregar Producto
                </button>
              </div>

              <div v-if="orderForm.items.length === 0" class="text-center py-6 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                <div class="w-10 h-10 bg-[#f8f9fa] dark:bg-[#2a2a2d] rounded-full flex items-center justify-center mx-auto mb-2">
                  <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                  </svg>
                </div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">No hay productos</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Agrega productos a esta orden</p>
              </div>

              <div v-else class="space-y-1.5">
                <div v-for="(item, index) in orderForm.items" :key="index" class="bg-[#f8f9fa] dark:bg-[#2a2a2d] rounded-lg p-2.5 flex items-center gap-2.5">
                  <div class="flex-1 grid grid-cols-12 gap-3 items-center">
                    <div class="col-span-4">
                      <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ item.product?.name || 'Producto' }}</p>
                        <!-- Badge de variante -->
                        <span v-if="item.variant_id" class="px-1.5 py-0.5 text-[9px] font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded">
                          Variante
                        </span>
                      </div>
                      <p class="text-xs text-gray-500 dark:text-gray-400">
                        SKU: {{ item.product?.sku || 'N/A' }}
                        <span v-if="item.variant_name" class="ml-1 text-purple-600 dark:text-purple-400">• {{ item.variant_name }}</span>
                      </p>
                    </div>
                    <div class="col-span-3">
                      <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Cantidad</label>
                      <input v-model.number="item.quantity" type="number" min="0.01" step="0.01" class="w-full px-3 py-2 bg-white dark:bg-[#1e1f20] text-gray-900 dark:text-white rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent text-sm" @input="calculateItemTotal(index)" />
                    </div>
                    <div class="col-span-3">
                      <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Costo Unitario</label>
                      <input v-model.number="item.unit_cost" type="number" min="0" step="0.01" class="w-full px-3 py-2 bg-white dark:bg-[#1e1f20] text-gray-900 dark:text-white rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent text-sm" @input="calculateItemTotal(index)" />
                    </div>
                    <div class="col-span-2 text-right">
                      <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Subtotal</label>
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatNumber(item.quantity * item.unit_cost) }}</p>
                    </div>
                  </div>
                  <button @click="removeOrderItem(index)" class="p-2 text-red-400 dark:text-red-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
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
              <div class="bg-[#f8f9fa] dark:bg-[#2a2a2d] rounded-lg p-3">
                <div class="space-y-1.5">
                  <div class="flex justify-between items-center">
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Subtotal:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatNumber(orderSubtotal) }}</span>
                  </div>
                  <div class="flex justify-between items-center pt-1.5 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Total:</span>
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">${{ formatNumber(orderSubtotal) }}</span>
                  </div>
                </div>
              </div>

              <!-- Notas -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notas</label>
                <textarea v-model="orderForm.notes" rows="2" placeholder="Comentarios adicionales..." class="w-full px-3 py-2.5 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors resize-none"></textarea>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="bg-[#f8f9fa] dark:bg-[#1a1a1d] border-t border-gray-200 dark:border-gray-800 px-6 py-4 flex justify-end gap-2.5">
            <button @click="cancelCreateOrder" class="px-4 py-2.5 bg-[#f8f9fa] dark:bg-[#1e1f20] hover:bg-gray-100 dark:hover:bg-[#282a2c] text-gray-700 dark:text-gray-300 text-sm font-medium rounded-full transition-colors">
              Cancelar
            </button>
            <button @click="saveOrderAsDraft" :disabled="savingOrder" class="px-4 py-2.5 bg-[#f8f9fa] dark:bg-[#1e1f20] hover:bg-gray-100 dark:hover:bg-[#282a2c] text-gray-700 dark:text-gray-300 text-sm font-medium rounded-full transition-colors">
              {{ savingOrder ? 'Guardando...' : 'Guardar Borrador' }}
            </button>
            <button @click="saveAndShowSendOptions" :disabled="savingOrder" class="px-5 py-2.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 disabled:bg-gray-300 dark:disabled:bg-gray-700 disabled:cursor-not-allowed text-white dark:text-gray-900 text-sm font-medium rounded-full transition-colors">
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
          <div class="bg-white dark:bg-[#212124] rounded-xl border border-gray-200 dark:border-gray-800 max-w-3xl w-full max-h-[80vh] overflow-auto">
            <div class="border-b border-gray-200 dark:border-gray-800 px-6 py-4 flex items-center justify-between sticky top-0 bg-white dark:bg-[#212124] z-10">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Seleccionar Producto</h3>
              <button @click="showProductSelector = false" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <div class="px-6 py-4">
              <input v-model="productSearch" type="text" placeholder="Buscar producto por nombre o SKU..." class="w-full px-4 py-3 bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors mb-4" />

              <div v-if="loadingProducts" class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-4 border-gray-200 dark:border-gray-700 border-t-blue-600 dark:border-t-blue-400"></div>
              </div>

              <div v-else class="space-y-2 max-h-96 overflow-y-auto">
                <button v-for="product in filteredProducts" :key="product.id" @click="addProductToOrder(product)" class="w-full text-left p-3 bg-[#f8f9fa] dark:bg-[#2a2a2d] hover:bg-gray-100 dark:hover:bg-[#333338] rounded-lg transition-colors">
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ product.name }}</p>
                        <!-- Badge para productos con variantes -->
                        <span v-if="product.product_type === 'variable'" class="px-2 py-0.5 text-[10px] font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded">
                          Variantes
                        </span>
                      </div>
                      <p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ product.sku }} | Stock: {{ product.current_stock }}</p>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatNumber(product.cost_price || 0) }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">Costo</p>
                    </div>
                  </div>
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Modal: Selector de Variantes (para productos moda) -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showVariantSelector" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-[70] p-4" @click.self="closeVariantSelector">
          <div class="bg-white dark:bg-[#212124] rounded-xl border border-gray-200 dark:border-gray-800 max-w-2xl w-full max-h-[80vh] overflow-auto">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-800 px-6 py-4 sticky top-0 bg-white dark:bg-[#212124] z-10">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Seleccionar Variante</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ selectedProductForVariant?.name }}
                  </p>
                </div>
                <button @click="closeVariantSelector" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Loading -->
            <div v-if="loadingVariants" class="flex items-center justify-center py-12">
              <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-200 dark:border-gray-700 border-t-purple-600 dark:border-t-purple-400"></div>
            </div>

            <!-- Lista de Variantes -->
            <div v-else class="p-4 space-y-2">
              <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/50 rounded-lg p-3 mb-4">
                <p class="text-sm text-purple-800 dark:text-purple-200">
                  <strong>Producto con variantes:</strong> Selecciona la talla/color específico que deseas ordenar.
                </p>
              </div>

              <button 
                v-for="variant in productVariants" 
                :key="variant.id" 
                @click="addVariantToOrder(variant)"
                class="w-full text-left p-4 bg-[#f8f9fa] dark:bg-[#2a2a2d] hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg border border-transparent hover:border-purple-300 dark:hover:border-purple-700/50 transition-colors"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ formatVariantOptions(variant.options_summary) }}
                    </p>
                    <div class="flex items-center gap-3 mt-1">
                      <span class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ variant.sku }}</span>
                      <span class="text-xs font-medium" :class="(variant.stock || 0) > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                        Stock: {{ variant.stock || 0 }}
                      </span>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatNumber(variant.cost_price || selectedProductForVariant?.cost_price || 0) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Costo</p>
                  </div>
                </div>
              </button>

              <div v-if="productVariants.length === 0" class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">No hay variantes disponibles</p>
              </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 dark:border-gray-800 px-6 py-4 bg-[#f8f9fa] dark:bg-[#1a1a1d] sticky bottom-0">
              <button @click="closeVariantSelector" class="w-full px-4 py-2.5 bg-[#f8f9fa] dark:bg-[#1e1f20] hover:bg-gray-100 dark:hover:bg-[#282a2c] text-gray-700 dark:text-gray-300 text-sm font-medium rounded-full transition-colors">
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
             class="fixed inset-0 bg-black/60 flex items-center justify-center p-4 z-50"
             @click.self="closeReceiveModal">
          <div class="bg-white dark:bg-[#212124] rounded-xl w-full max-w-3xl overflow-hidden animate-fade-in max-h-[90vh] flex flex-col">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-[#f8f9fa] dark:bg-[#1a1a1d]">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ingresar Productos a Stock</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ selectedOrder?.order_number }}</p>
                  </div>
                </div>
                <button @click="closeReceiveModal" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto p-6">
              <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 rounded-lg p-3 mb-4">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                  <strong>Instrucciones:</strong> Marca el checkbox si recibiste la cantidad completa, o ingresa la cantidad exacta recibida. Puedes eliminar productos que no llegaron.
                </p>
              </div>

              <div class="space-y-3">
                <div v-for="(item, index) in receiveForm.items" :key="item.item_id"
                     class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-300 dark:hover:border-gray-700 transition-colors">
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
                          <p class="text-sm font-medium text-gray-900 dark:text-white">{{ item.product_name }}</p>
                          <!-- Badge de variante -->
                          <span v-if="item.variant_id" class="px-1.5 py-0.5 text-[9px] font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded">
                            Variante
                          </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                          Ordenado: {{ item.quantity_ordered }} unidades
                          <!-- Mostrar variante -->
                          <span v-if="item.variant_options" class="ml-1 text-purple-600 dark:text-purple-400">
                            • {{ typeof item.variant_options === 'string' ? formatVariantOptions(item.variant_options) : formatVariantOptions(item.variant_options) }}
                          </span>
                        </p>
                      </label>

                      <!-- Quantity Input -->
                      <div class="mt-3">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 block">Cantidad Recibida</label>
                        <input type="number"
                               v-model.number="item.quantity_to_receive"
                               :max="item.quantity_ordered"
                               min="0"
                               step="1"
                               :disabled="item.received_all"
                               class="w-32 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed">
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
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-[#f8f9fa] dark:bg-[#1a1a1d]">
              <div class="flex items-center justify-end gap-3">
                <button @click="closeReceiveModal"
                        class="px-4 py-2.5 bg-[#f8f9fa] dark:bg-[#1e1f20] hover:bg-gray-100 dark:hover:bg-[#282a2c] text-gray-700 dark:text-gray-300 rounded-full transition-colors font-medium">
                  Cancelar
                </button>
                <button @click="confirmReceive"
                        :disabled="receivingMerchandise"
                        class="px-6 py-2.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 rounded-full transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
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
      <div v-if="showPremiumModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-[60] p-4 animate-fade-in">
        <div class="bg-white dark:bg-[#212124] rounded-xl max-w-md w-full border border-gray-200 dark:border-gray-800 animate-scale-in">
          
          <!-- Contenido -->
          <div class="p-8 text-center">
            <!-- Icono Premium -->
            <div class="w-20 h-20 bg-[#f8f9fa] dark:bg-[#2a2a2d] rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-10 h-10 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
              </svg>
            </div>

            <!-- Título -->
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-3">¡Mejora tu Plan!</h3>
            
            <!-- Mensaje -->
            <p class="text-base text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
              <span class="font-semibold text-gray-900 dark:text-white">{{ premiumFeatureName }}</span> está disponible en nuestros planes premium.
            </p>
            
            <p class="text-sm text-gray-500 dark:text-gray-500 mb-8">
              Desbloquea todas las funciones premium para potenciar tu negocio
            </p>

            <!-- Botones -->
            <div class="flex gap-3">
              <button
                @click="showPremiumModal = false"
                class="flex-1 py-3 bg-[#f8f9fa] dark:bg-[#1e1f20] text-gray-700 dark:text-gray-300 text-base font-medium rounded-full hover:bg-gray-100 dark:hover:bg-[#282a2c] transition-colors"
              >
                Cerrar
              </button>
              <button
                @click="navigateToPlans"
                class="flex-1 py-3 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 text-base font-medium rounded-full transition-colors"
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
          <div class="absolute inset-0 bg-black/60" @click="cancelEmail"></div>
          <div class="relative bg-white dark:bg-[#212124] rounded-xl w-full max-w-md border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Ingresar Email
              </h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">El proveedor no tiene email registrado</p>
            </div>
            <div class="px-6 py-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email del proveedor</label>
              <input 
                v-model="emailInput"
                type="email"
                placeholder="ejemplo@proveedor.com"
                class="w-full px-4 py-3 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors"
                @keyup.enter="confirmEmail"
              />
            </div>
            <div class="px-6 py-4 bg-[#f8f9fa] dark:bg-[#1a1a1d] flex items-center justify-end gap-3">
              <button @click="cancelEmail" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                Cancelar
              </button>
              <button @click="confirmEmail" class="px-5 py-2.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 font-medium rounded-full transition-colors">
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
          <div class="absolute inset-0 bg-black/60" @click="cancelPhone"></div>
          <div class="relative bg-white dark:bg-[#212124] rounded-xl w-full max-w-md border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                Ingresar Teléfono
              </h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">El proveedor no tiene teléfono registrado</p>
            </div>
            <div class="px-6 py-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Teléfono del proveedor</label>
              <input 
                v-model="phoneInput"
                type="tel"
                placeholder="3001234567"
                class="w-full px-4 py-3 text-sm bg-white dark:bg-[#2a2a2d] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors"
                @keyup.enter="confirmPhone"
              />
            </div>
            <div class="px-6 py-4 bg-[#f8f9fa] dark:bg-[#1a1a1d] flex items-center justify-end gap-3">
              <button @click="cancelPhone" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                Cancelar
              </button>
              <button @click="confirmPhone" class="px-5 py-2.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 font-medium rounded-full transition-colors">
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
          <div class="absolute inset-0 bg-black/60" @click="closeSendOptionsModal"></div>
          <div class="relative bg-white dark:bg-[#212124] rounded-xl w-full max-w-md border border-gray-200 dark:border-gray-800 overflow-hidden animate-scale-in">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 text-center">
              <div class="w-14 h-14 bg-[#f8f9fa] dark:bg-[#2a2a2d] rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white">¡Orden Creada!</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">¿Cómo deseas enviarla al proveedor?</p>
            </div>
            
            <!-- Opciones de envío -->
            <div class="px-6 py-5 space-y-3">
              <!-- Enviar por Email -->
              <button 
                @click="sendNewOrderByEmail"
                class="w-full flex items-center gap-4 p-4 bg-[#f8f9fa] dark:bg-[#2a2a2d] hover:bg-gray-100 dark:hover:bg-[#333338] rounded-lg transition-colors group"
              >
                <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                  <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div class="text-left flex-1">
                  <p class="font-medium text-gray-900 dark:text-white">Enviar por Email</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Se enviará un PDF al correo del proveedor</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>

              <!-- Enviar por WhatsApp -->
              <button 
                @click="sendNewOrderByWhatsApp"
                class="w-full flex items-center gap-4 p-4 bg-[#f8f9fa] dark:bg-[#2a2a2d] hover:bg-gray-100 dark:hover:bg-[#333338] rounded-lg transition-colors group"
              >
                <div class="w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                  <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                  </svg>
                </div>
                <div class="text-left flex-1">
                  <p class="font-medium text-gray-900 dark:text-white">Enviar por WhatsApp</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Se abrirá WhatsApp con el PDF adjunto</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-[#f8f9fa] dark:bg-[#1a1a1d] border-t border-gray-200 dark:border-gray-800">
              <button 
                @click="closeSendOptionsModal"
                class="w-full py-3 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium transition-colors flex items-center justify-center gap-2"
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
import { useUIContextStore } from '../store/uiContextStore.js'

// Obtener función de navegación
const { navigateToModule } = useModuleNavigation()

// Obtener funciones de toast (global)
const { showToast, showSuccess, showError, showWarning, showInfo } = useToast()

// Store de contexto para IA
const uiContextStore = useUIContextStore()

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
    
    // 🤖 CONTEXTO IA: Inicializar context awareness
    this.setupAIContext()
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
      this.updateAIContext() // Actualizar contexto para que la IA vea la orden seleccionada
    },

    // Cuando se selecciona un proveedor en el componente hijo
    onSupplierSelected(supplier) {
      this.updateAIContext() // Actualizar contexto para que la IA vea el proveedor seleccionado
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
      this.updateAIContext() // Actualizar contexto al cerrar
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
        pending: 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50',
        partial: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/50',
        received: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50'
      }
      return classes[status] || 'bg-gray-100 dark:bg-gray-800/50 text-gray-700 dark:text-gray-400 border border-gray-200 dark:border-gray-700'
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
          
          // Debug: ver proveedores cargados
          const activos = this.suppliers.filter(s => s.active !== false && s.active !== 0)
          console.log(`✅ Proveedores cargados: ${this.suppliers.length} (${activos.length} activos)`)
          
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
          // Actualizar contexto IA
          this.updateAIContext()
        }
      } catch (error) {
        console.error('Error guardando proveedor:', error)
        showError(error.message || 'Error al guardar proveedor')
      } finally {
        this.savingSupplier = false
      }
    },
    
    // ========== 🤖 CONTEXTO IA - SCREEN AWARENESS ==========
    
    setupAIContext() {
      // Inicializar contexto
      this.updateAIContext()
      
      // Registrar acciones disponibles para la IA
      this.registerAIActions()
    },
    
    updateAIContext() {
      const formatCurrency = (val) => Number(val || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
      
      // Datos del contexto de compras
      const comprasData = {
        modulo: 'compras',
        pestanaActual: this.activeTab === 'suppliers' ? 'Proveedores' : 'Órdenes de Compra',
        modoVista: this.viewMode,
        
        // Tab Proveedores
        proveedores: {
          total: this.suppliers.length,
          // Considerar activo si active es true, 1 o undefined (no explícitamente false o 0)
          activos: this.suppliers.filter(s => s.active !== false && s.active !== 0).length,
          inactivos: this.suppliers.filter(s => s.active === false || s.active === 0).length,
          listaProveedores: this.suppliers.slice(0, 20).map(s => ({
            id: s.id,
            nombre: s.name,
            documento: s.document,
            telefono: s.phone,
            email: s.email,
            activo: s.active !== false && s.active !== 0,
            productosAsociados: s.products_count || 0,
            deuda: s.current_debt || 0
          }))
        },
        
        // Proveedor seleccionado (del componente hijo)
        proveedorSeleccionado: (() => {
          const suppView = this.$refs.suppliersView
          if (suppView && suppView.selectedSupplier) {
            const s = suppView.selectedSupplier
            return {
              id: s.id,
              nombre: s.name,
              documento: s.document,
              telefono: s.phone,
              email: s.email,
              activo: s.active !== false && s.active !== 0,
              totalCompras: s.total_purchases_amount || 0,
              ordenesCompra: s.purchase_orders_count || 0,
              productosAsociados: s.products_count || 0,
              deuda: s.current_debt || 0,
              productosLista: (suppView.supplierProducts || []).slice(0, 10).map(p => ({
                nombre: p.name,
                sku: p.sku,
                precioCompra: p.cost_price,
                precioVenta: p.price,
                stock: p.current_stock
              }))
            }
          }
          return null
        })(),
        
        // Tab Órdenes de Compra
        ordenes: {
          total: this.orders.length,
          pendientes: this.orders.filter(o => o.status === 'pending').length,
          parciales: this.orders.filter(o => o.status === 'partial').length,
          recibidas: this.orders.filter(o => o.status === 'received').length,
          listaOrdenes: this.orders.slice(0, 15).map(o => ({
            id: o.id,
            numero: o.order_number,
            proveedor: o.supplier?.name,
            fecha: o.order_date,
            estado: o.status,
            total: o.total,
            items: o.items?.length || 0
          }))
        },
        
        // Bodegas disponibles
        bodegas: {
          tieneMultiples: this.warehouses.length > 1,
          lista: this.warehouses.map(w => ({
            id: w.id,
            nombre: w.name,
            principal: w.is_main || false
          }))
        },
        
        // Formulario de proveedor (si está abierto)
        formularioProveedor: this.viewMode === 'create-supplier' ? {
          abierto: true,
          datos: {
            nombre: this.supplierForm.name,
            documento: this.supplierForm.document,
            telefono: this.supplierForm.phone,
            email: this.supplierForm.email,
            ciudad: this.supplierForm.city,
            direccion: this.supplierForm.address,
            contacto: this.supplierForm.contact_name,
            telefonoContacto: this.supplierForm.contact_phone
          }
        } : { abierto: false },
        
        // Formulario de orden (si está abierto)
        formularioOrden: this.viewMode === 'create' ? {
          abierto: true,
          modalProductosAbierto: this.showProductSelector,
          datos: {
            proveedorId: this.orderForm.supplier_id,
            proveedorNombre: this.suppliers.find(s => s.id == this.orderForm.supplier_id)?.name || '',
            bodegaId: this.orderForm.warehouse_id,
            bodegaNombre: this.warehouses.find(w => w.id == this.orderForm.warehouse_id)?.name || '',
            fechaOrden: this.orderForm.order_date,
            fechaEsperada: this.orderForm.expected_date,
            referencia: this.orderForm.reference,
            notas: this.orderForm.notes,
            productos: this.orderForm.items.map(item => ({
              id: item.product_id,
              nombre: item.product?.name,
              cantidad: item.quantity,
              costoUnitario: item.unit_cost,
              subtotal: item.quantity * item.unit_cost
            })),
            subtotal: this.orderSubtotal
          }
        } : { abierto: false },
        
        // Productos disponibles para agregar
        productosDisponibles: this.products.slice(0, 50).map(p => ({
          id: p.id,
          nombre: p.name,
          sku: p.sku,
          stockActual: p.current_stock || 0,
          costoBase: p.cost_price || 0,
          proveedorId: p.supplier_id
        })),
        
        // 🔴 ORDEN SELECCIONADA (para ver detalles y acciones)
        ordenSeleccionada: this.selectedOrder ? {
          id: this.selectedOrder.id,
          numero: this.selectedOrder.order_number,
          proveedor: this.selectedOrder.supplier?.name,
          proveedorEmail: this.selectedOrder.supplier?.email,
          proveedorTelefono: this.selectedOrder.supplier?.phone,
          fecha: this.selectedOrder.order_date,
          fechaEsperada: this.selectedOrder.expected_date,
          estado: this.selectedOrder.status,
          total: this.selectedOrder.total,
          bodega: this.selectedOrder.warehouse?.name,
          productos: (this.selectedOrder.items || []).map(item => ({
            nombre: item.product?.name || item.product_name,
            cantidad: item.quantity_ordered || item.quantity,
            recibido: item.quantity_received || 0,
            costo: item.unit_cost,
            subtotal: (item.quantity_ordered || item.quantity) * item.unit_cost
          })),
          // Acciones disponibles según estado
          accionesDisponibles: this.selectedOrder.status === 'pending' 
            ? ['descargarPDF', 'enviarEmail', 'enviarWhatsApp', 'marcarPagada', 'ingresarProductosStock']
            : this.selectedOrder.status === 'partial'
              ? ['descargarPDF', 'ingresarProductosStock']
              : ['descargarPDF']
        } : null,
        
        // Modal de recepción de productos
        modalRecepcion: {
          abierto: this.showReceiveModal,
          productos: this.receiveForm.items.map(item => ({
            nombre: item.product_name,
            ordenado: item.quantity_ordered,
            recibidoPrevio: item.quantity_received,
            porRecibir: item.quantity_to_receive
          }))
        }
      }
      
      // Resumen rápido para respuestas comunes
      comprasData.resumenRapido = {
        cuantosProveedores: `Hay ${this.suppliers.length} proveedores registrados, ${this.suppliers.filter(s => s.active !== false).length} activos.`,
        ordenesPendientes: `Hay ${this.orders.filter(o => o.status === 'pending').length} órdenes de compra pendientes de recibir.`,
        comoCrearProveedor: 'Para crear un proveedor, dame el nombre y opcionalmente documento, teléfono, email, ciudad y dirección.',
        comoCrearOrden: 'Para crear una orden: 1) Selecciona el proveedor, 2) Agrega productos con cantidad y costo, 3) Si hay múltiples sedes, selecciona la bodega destino.',
        tieneMultiplesSedes: this.warehouses.length > 1 ? `Tiene ${this.warehouses.length} sedes/bodegas. Al crear orden de compra, preguntaré a qué sede va.` : 'Solo tiene una sede.',
        verProductos: 'Para ver productos disponibles DURANTE la orden, usa abrirSelectorProductos() - NO navegues al módulo de productos.'
      }
      
      uiContextStore.setScreenData(comprasData)
    },
    
    registerAIActions() {
      const self = this
      
      // === ACCIONES DE PROVEEDORES ===
      
      uiContextStore.registerAction('cambiarPestanaCompras', ({ pestana }) => {
        const tab = pestana.toLowerCase().includes('orden') || pestana.toLowerCase().includes('compra') 
          ? 'orders' 
          : 'suppliers'
        self.changeTab(tab)
        self.updateAIContext()
        return { success: true, message: `Cambiado a pestaña de ${tab === 'orders' ? 'Órdenes de Compra' : 'Proveedores'}` }
      })
      
      uiContextStore.registerAction('crearNuevoProveedor', async () => {
        // Asegurar que estamos en tab suppliers
        if (self.activeTab !== 'suppliers') {
          self.changeTab('suppliers')
          await new Promise(resolve => setTimeout(resolve, 200))
        }
        self.viewMode = 'create-supplier'
        self.resetSupplierForm()
        
        // Esperar a que Vue renderice el formulario
        await new Promise(resolve => setTimeout(resolve, 100))
        
        self.updateAIContext()
        
        return { 
          success: true, 
          message: 'Formulario de proveedor abierto. Dame los datos: nombre del proveedor (obligatorio), documento/NIT, teléfono, email, ciudad, dirección.',
          vistaActual: 'create-supplier',
          formularioVisible: true,
          camposDelFormulario: ['nombre (obligatorio)', 'documento/NIT', 'telefono', 'email', 'ciudad', 'direccion', 'contacto', 'notas']
        }
      })
      
      // Acción para verificar qué hay en el formulario actualmente
      uiContextStore.registerAction('verificarFormularioProveedor', () => {
        if (self.viewMode !== 'create-supplier' && self.viewMode !== 'edit-supplier') {
          return { 
            success: false, 
            message: 'No hay formulario de proveedor visible',
            vistaActual: self.viewMode
          }
        }
        
        return {
          success: true,
          vistaActual: self.viewMode,
          formularioEnPantalla: {
            nombre: self.supplierForm.name || '(vacío)',
            documento: self.supplierForm.document || '(vacío)',
            telefono: self.supplierForm.phone || '(vacío)',
            email: self.supplierForm.email || '(vacío)',
            ciudad: self.supplierForm.city || '(vacío)',
            direccion: self.supplierForm.address || '(vacío)',
            contacto: self.supplierForm.contact_name || '(vacío)',
            telefonoContacto: self.supplierForm.contact_phone || '(vacío)',
            notas: self.supplierForm.notes || '(vacío)'
          },
          camposConDatos: Object.entries(self.supplierForm)
            .filter(([k, v]) => v && v.toString().trim())
            .map(([k]) => k),
          listoParaGuardar: !!self.supplierForm.name
        }
      })
      
      uiContextStore.registerAction('llenarCampoProveedor', async ({ campo, valor }) => {
        if (self.viewMode !== 'create-supplier' && self.viewMode !== 'edit-supplier') {
          return { success: false, message: 'Primero abre el formulario con crearNuevoProveedor' }
        }
        
        const camposValidos = {
          'nombre': 'name',
          'name': 'name',
          'razon_social': 'name',
          'documento': 'document',
          'nit': 'document',
          'rut': 'document',
          'telefono': 'phone',
          'phone': 'phone',
          'celular': 'phone',
          'email': 'email',
          'correo': 'email',
          'ciudad': 'city',
          'city': 'city',
          'direccion': 'address',
          'address': 'address',
          'contacto': 'contact_name',
          'persona_contacto': 'contact_name',
          'contact_name': 'contact_name',
          'telefono_contacto': 'contact_phone',
          'notas': 'notes',
          'notes': 'notes'
        }
        
        const campoReal = camposValidos[campo.toLowerCase().trim()]
        
        if (!campoReal) {
          return { success: false, message: `Campo "${campo}" no reconocido. Campos válidos: nombre, documento/nit, telefono, email, ciudad, direccion, contacto, notas` }
        }
        
        // Guardar valor anterior para verificar
        const valorAnterior = self.supplierForm[campoReal]
        
        // Asignar nuevo valor
        self.supplierForm[campoReal] = valor
        
        // Esperar un tick para que Vue actualice
        await new Promise(resolve => setTimeout(resolve, 50))
        
        // Verificar que el valor se guardó correctamente
        const valorActual = self.supplierForm[campoReal]
        const seGuardo = valorActual === valor
        
        self.updateAIContext()
        
        // Retornar estado COMPLETO del formulario para que la IA pueda ver TODO
        return { 
          success: seGuardo, 
          message: seGuardo 
            ? `✅ Campo "${campo}" ahora muestra: "${valor}"`
            : `⚠️ Error: El campo no se actualizó correctamente`,
          campoActualizado: campo,
          valorPuesto: valor,
          seGuardoCorrectamente: seGuardo,
          // Estado completo del formulario visible en pantalla
          formularioEnPantalla: {
            nombre: self.supplierForm.name || '(vacío)',
            documento: self.supplierForm.document || '(vacío)',
            telefono: self.supplierForm.phone || '(vacío)',
            email: self.supplierForm.email || '(vacío)',
            ciudad: self.supplierForm.city || '(vacío)',
            direccion: self.supplierForm.address || '(vacío)',
            contacto: self.supplierForm.contact_name || '(vacío)',
            telefonoContacto: self.supplierForm.contact_phone || '(vacío)',
            notas: self.supplierForm.notes || '(vacío)'
          },
          // Campos que faltan por llenar
          camposFaltantes: [
            !self.supplierForm.name && 'nombre (obligatorio)',
          ].filter(Boolean)
        }
      })
      
      uiContextStore.registerAction('guardarProveedor', async () => {
        if (self.viewMode !== 'create-supplier') {
          return { success: false, message: 'No hay formulario de proveedor abierto' }
        }
        
        if (!self.supplierForm.name?.trim()) {
          return { success: false, message: 'Falta el nombre del proveedor (obligatorio)' }
        }
        
        try {
          await self.saveSupplier()
          return { success: true, message: `Proveedor "${self.supplierForm.name}" creado exitosamente.` }
        } catch (error) {
          return { success: false, message: `Error al guardar: ${error.message}` }
        }
      })
      
      uiContextStore.registerAction('cerrarFormularioProveedor', () => {
        if (self.viewMode === 'create-supplier') {
          self.cancelCreateSupplier()
          self.updateAIContext()
          return { success: true, message: 'Formulario de proveedor cerrado' }
        }
        return { success: false, message: 'No hay formulario de proveedor abierto' }
      })
      
      uiContextStore.registerAction('buscarProveedor', ({ texto }) => {
        // Buscar proveedores que coincidan
        const textoLower = texto.toLowerCase().trim()
        const proveedoresEncontrados = self.suppliers.filter(s => 
          s.name.toLowerCase().includes(textoLower) ||
          (s.document && s.document.toLowerCase().includes(textoLower))
        )
        
        // Activar el filtro visual si hay ref
        if (self.$refs.suppliersView) {
          self.$refs.suppliersView.searchQuery = texto
        }
        
        if (proveedoresEncontrados.length === 0) {
          return { 
            success: false, 
            message: `No encontré proveedores con "${texto}". Hay ${self.suppliers.length} proveedores en total.`,
            proveedoresDisponibles: self.suppliers.slice(0, 10).map(s => s.name)
          }
        }
        
        return { 
          success: true, 
          message: `Encontré ${proveedoresEncontrados.length} proveedor(es) que coinciden con "${texto}"`,
          proveedoresEncontrados: proveedoresEncontrados.map(s => ({
            id: s.id,
            nombre: s.name,
            documento: s.document,
            telefono: s.phone,
            activo: s.active !== false && s.active !== 0
          }))
        }
      })
      
      // Acción para listar todos los proveedores disponibles
      uiContextStore.registerAction('listarProveedores', () => {
        const proveedoresActivos = self.suppliers.filter(s => s.active !== false && s.active !== 0)
        const proveedoresInactivos = self.suppliers.filter(s => s.active === false || s.active === 0)
        
        return {
          success: true,
          totalProveedores: self.suppliers.length,
          activos: proveedoresActivos.length,
          inactivos: proveedoresInactivos.length,
          listaProveedores: self.suppliers.slice(0, 20).map(s => ({
            id: s.id,
            nombre: s.name,
            documento: s.document,
            telefono: s.phone,
            activo: s.active !== false && s.active !== 0,
            productosAsociados: s.products_count || 0
          }))
        }
      })
      
      // Acción para seleccionar/ver un proveedor específico
      uiContextStore.registerAction('seleccionarProveedor', async ({ nombre }) => {
        // Buscar el proveedor por nombre
        const nombreLower = nombre.toLowerCase().trim()
        const proveedor = self.suppliers.find(s => 
          s.name.toLowerCase().includes(nombreLower) ||
          (s.document && s.document.toLowerCase().includes(nombreLower))
        )
        
        if (!proveedor) {
          return {
            success: false,
            message: `No encontré proveedor "${nombre}"`,
            proveedoresDisponibles: self.suppliers.slice(0, 10).map(s => s.name)
          }
        }
        
        // Cambiar a tab de proveedores si no estamos ahí
        if (self.activeTab !== 'suppliers') {
          self.changeTab('suppliers')
          await new Promise(resolve => setTimeout(resolve, 200))
        }
        
        // Si hay ref al componente hijo, seleccionar el proveedor
        if (self.$refs.suppliersView && self.$refs.suppliersView.selectSupplier) {
          self.$refs.suppliersView.selectSupplier(proveedor)
        }
        
        self.updateAIContext()
        
        return {
          success: true,
          message: `Proveedor "${proveedor.name}" seleccionado`,
          datosProveedor: {
            id: proveedor.id,
            nombre: proveedor.name,
            documento: proveedor.document,
            telefono: proveedor.phone,
            email: proveedor.email,
            direccion: proveedor.address,
            ciudad: proveedor.city,
            activo: proveedor.active !== false && proveedor.active !== 0,
            productosAsociados: proveedor.products_count || 0,
            deuda: proveedor.current_debt || 0,
            totalComprado: proveedor.total_purchased || 0
          }
        }
      })
      
      // === ACCIONES DE ÓRDENES DE COMPRA ===
      
      // Acción mejorada que acepta proveedor opcional
      uiContextStore.registerAction('crearNuevaOrdenCompra', async ({ nombreProveedor } = {}) => {
        // Asegurar que estamos en tab orders
        if (self.activeTab !== 'orders') {
          self.changeTab('orders')
          await new Promise(resolve => setTimeout(resolve, 200))
        }
        self.viewMode = 'create'
        self.resetOrderForm()
        
        let mensajeProveedor = ''
        let proveedorSeleccionado = null
        
        // Si se proporcionó nombre de proveedor, seleccionarlo automáticamente
        if (nombreProveedor) {
          const proveedor = self.suppliers.find(s => 
            s.name.toLowerCase().includes(nombreProveedor.toLowerCase())
          )
          
          if (proveedor) {
            self.orderForm.supplier_id = proveedor.id
            proveedorSeleccionado = proveedor
            mensajeProveedor = `Proveedor "${proveedor.name}" ya seleccionado.`
          } else {
            mensajeProveedor = `No encontré proveedor "${nombreProveedor}". Dime cuál quieres.`
          }
        }
        
        self.updateAIContext()
        
        // Mensaje personalizado según bodegas
        const msgBodega = self.warehouses.length > 1 
          ? ` Hay ${self.warehouses.length} sedes, ¿a cuál va?` 
          : ''
        
        // Mensaje según si se seleccionó proveedor o no
        const mensaje = proveedorSeleccionado 
          ? `Formulario abierto. ${mensajeProveedor}${msgBodega} ¿Qué productos agregamos?`
          : `Formulario de orden abierto.${msgBodega} ¿Para cuál proveedor?`
        
        return { 
          success: true, 
          message: mensaje,
          proveedorSeleccionado: proveedorSeleccionado ? {
            id: proveedorSeleccionado.id,
            nombre: proveedorSeleccionado.name
          } : null,
          proveedoresDisponibles: self.suppliers.slice(0, 10).map(s => s.name)
        }
      })
      
      uiContextStore.registerAction('seleccionarProveedorOrden', async ({ nombre }) => {
        if (self.viewMode !== 'create') {
          return { success: false, message: 'Primero abre el formulario de orden con crearNuevaOrdenCompra' }
        }
        
        const proveedor = self.suppliers.find(s => 
          s.name.toLowerCase().includes(nombre.toLowerCase())
        )
        
        if (!proveedor) {
          return { 
            success: false, 
            message: `No encontré proveedor "${nombre}". Proveedores disponibles: ${self.suppliers.slice(0, 5).map(s => s.name).join(', ')}` 
          }
        }
        
        self.orderForm.supplier_id = proveedor.id
        self.updateAIContext()
        
        // Filtrar productos de este proveedor
        const productosProveedor = self.products.filter(p => p.supplier_id == proveedor.id)
        
        return { 
          success: true, 
          message: `Proveedor "${proveedor.name}" seleccionado. Tiene ${productosProveedor.length} productos asociados. ¿Qué productos quieres agregar a la orden?`,
          productosDelProveedor: productosProveedor.slice(0, 10).map(p => ({
            nombre: p.name,
            sku: p.sku,
            stock: p.current_stock,
            costo: p.cost_price
          }))
        }
      })
      
      uiContextStore.registerAction('seleccionarBodegaOrden', ({ nombre }) => {
        if (self.viewMode !== 'create') {
          return { success: false, message: 'Primero abre el formulario de orden' }
        }
        
        if (self.warehouses.length <= 1) {
          return { success: true, message: 'Solo hay una bodega, ya está seleccionada automáticamente.' }
        }
        
        const bodega = self.warehouses.find(w => 
          w.name.toLowerCase().includes(nombre.toLowerCase())
        )
        
        if (!bodega) {
          return { 
            success: false, 
            message: `No encontré bodega "${nombre}". Bodegas disponibles: ${self.warehouses.map(w => w.name).join(', ')}` 
          }
        }
        
        self.orderForm.warehouse_id = bodega.id
        self.updateAIContext()
        
        return { success: true, message: `Bodega "${bodega.name}" seleccionada como destino de la orden.` }
      })
      
      uiContextStore.registerAction('agregarProductoOrden', async ({ nombre, cantidad, costo }) => {
        console.log('📦 [agregarProductoOrden] viewMode:', self.viewMode, 'items:', self.orderForm.items.length)
        
        // Si no estamos en modo crear, intentar abrir el formulario primero
        if (self.viewMode !== 'create') {
          self.viewMode = 'create'
          await new Promise(resolve => setTimeout(resolve, 100))
        }
        
        // Buscar producto
        const producto = self.products.find(p => 
          p.name.toLowerCase().includes(nombre.toLowerCase()) ||
          p.sku?.toLowerCase().includes(nombre.toLowerCase())
        )
        
        if (!producto) {
          return { 
            success: false, 
            message: `No encontré producto "${nombre}". Intenta buscar con otro nombre o SKU.`,
            productosDisponibles: self.products.slice(0, 10).map(p => p.name)
          }
        }
        
        // Verificar si ya existe en la orden
        const existente = self.orderForm.items.find(i => i.product_id === producto.id && !i.variant_id)
        if (existente) {
          existente.quantity += parseInt(cantidad) || 1
          self.updateAIContext()
          return { 
            success: true, 
            message: `Cantidad actualizada. Ahora hay ${existente.quantity} unidades de "${producto.name}" en la orden.`,
            productosEnOrden: self.orderForm.items.map(i => ({
              nombre: i.product?.name,
              cantidad: i.quantity,
              costo: i.unit_cost
            }))
          }
        }
        
        // Agregar nuevo producto
        const costoFinal = parseFloat(costo) || producto.cost_price || 0
        const cantidadFinal = parseInt(cantidad) || 1
        
        self.orderForm.items.push({
          product_id: producto.id,
          variant_id: null,
          variant_options: null,
          product: producto,
          quantity: cantidadFinal,
          unit_cost: costoFinal,
          notes: ''
        })
        
        console.log('✅ [agregarProductoOrden] Producto agregado, total items:', self.orderForm.items.length)
        
        self.updateAIContext()
        
        return { 
          success: true, 
          message: `Agregado: ${cantidadFinal} x "${producto.name}" a $${costoFinal.toLocaleString('es-CO')} c/u. Subtotal: $${(cantidadFinal * costoFinal).toLocaleString('es-CO')}. ¿Agregar más productos?`,
          productosEnOrden: self.orderForm.items.map(i => ({
            nombre: i.product?.name,
            cantidad: i.quantity,
            costo: i.unit_cost
          })),
          ordenActual: {
            productos: self.orderForm.items.length,
            subtotal: self.orderSubtotal
          }
        }
      })
      
      uiContextStore.registerAction('buscarProductoOrden', ({ texto }) => {
        if (self.viewMode !== 'create') {
          return { success: false, message: 'Primero abre el formulario de orden' }
        }
        
        self.productSearch = texto
        const resultados = self.filteredProducts.slice(0, 10)
        
        if (resultados.length === 0) {
          return { success: true, message: `No encontré productos con "${texto}".` }
        }
        
        return {
          success: true,
          message: `Encontré ${resultados.length} productos:`,
          productos: resultados.map(p => ({
            nombre: p.name,
            sku: p.sku,
            stock: p.current_stock,
            costo: p.cost_price
          }))
        }
      })
      
      // Acción para abrir el modal de selector de productos
      uiContextStore.registerAction('abrirSelectorProductos', () => {
        if (self.viewMode !== 'create') {
          return { success: false, message: 'Primero debes abrir el formulario de orden de compra con crearNuevaOrdenCompra' }
        }
        
        self.showProductSelector = true
        self.productSearch = ''
        
        // Retornar lista de productos disponibles
        const productosDisponibles = self.products.slice(0, 20).map(p => ({
          nombre: p.name,
          sku: p.sku,
          stock: p.current_stock,
          costo: p.cost_price,
          proveedor: p.supplier?.name || 'Sin proveedor'
        }))
        
        return {
          success: true,
          message: `Modal de productos abierto. Hay ${self.products.length} productos disponibles. El usuario puede buscar y seleccionar visualmente.`,
          productosVisibles: productosDisponibles
        }
      })
      
      uiContextStore.registerAction('llenarCampoOrden', ({ campo, valor }) => {
        if (self.viewMode !== 'create') {
          return { success: false, message: 'Primero abre el formulario de orden' }
        }
        
        const camposValidos = {
          'fecha_orden': 'order_date',
          'fecha_esperada': 'expected_date',
          'fecha_entrega': 'expected_date',
          'referencia': 'reference',
          'notas': 'notes',
          'observaciones': 'notes'
        }
        
        const campoReal = camposValidos[campo.toLowerCase().trim()]
        
        if (!campoReal) {
          return { success: false, message: `Campo "${campo}" no reconocido. Campos válidos: fecha_orden, fecha_esperada, referencia, notas` }
        }
        
        self.orderForm[campoReal] = valor
        self.updateAIContext()
        
        return { success: true, message: `Campo "${campo}" actualizado a "${valor}"` }
      })
      
      uiContextStore.registerAction('guardarOrdenCompra', async ({ comoBorrador }) => {
        if (self.viewMode !== 'create') {
          return { success: false, message: 'No hay formulario de orden abierto' }
        }
        
        if (!self.orderForm.supplier_id) {
          return { success: false, message: 'Falta seleccionar el proveedor' }
        }
        
        if (self.orderForm.items.length === 0) {
          return { success: false, message: 'Agrega al menos un producto a la orden' }
        }
        
        try {
          if (comoBorrador) {
            await self.saveOrderAsDraft()
            return { success: true, message: 'Orden guardada como borrador.' }
          } else {
            await self.saveOrderAsPending()
            return { 
              success: true, 
              message: `Orden de compra creada con ${self.orderForm.items.length} productos. Total: $${self.orderSubtotal.toLocaleString('es-CO')}. ¿Quieres enviarla al proveedor por email o WhatsApp?` 
            }
          }
        } catch (error) {
          return { success: false, message: `Error al guardar: ${error.message}` }
        }
      })
      
      uiContextStore.registerAction('cerrarFormularioOrden', () => {
        if (self.viewMode === 'create') {
          self.cancelCreateOrder()
          self.updateAIContext()
          return { success: true, message: 'Formulario de orden cerrado' }
        }
        return { success: false, message: 'No hay formulario de orden abierto' }
      })
      
      uiContextStore.registerAction('seleccionarOrdenCompra', ({ numero }) => {
        const orden = self.orders.find(o => 
          o.order_number?.toLowerCase().includes(numero.toLowerCase())
        )
        
        if (!orden) {
          return { success: false, message: `No encontré orden con número "${numero}"` }
        }
        
        self.selectOrder(orden)
        self.updateAIContext()
        
        return {
          success: true,
          message: `Orden ${orden.order_number} seleccionada. Proveedor: ${orden.supplier?.name}. Total: $${Number(orden.total || 0).toLocaleString('es-CO')}. Estado: ${self.getStatusLabel(orden.status)}.`
        }
      })
      
      uiContextStore.registerAction('filtrarOrdenesCompra', ({ estado }) => {
        const estadoMap = {
          'todas': 'all',
          'all': 'all',
          'pendientes': 'pending',
          'pending': 'pending',
          'parciales': 'partial',
          'partial': 'partial',
          'recibidas': 'received',
          'received': 'received',
          'completadas': 'received'
        }
        
        const filtro = estadoMap[estado.toLowerCase()] || 'all'
        self.filterStatus = filtro
        self.updateAIContext()
        
        const count = filtro === 'all' ? self.orders.length : self.orders.filter(o => o.status === filtro).length
        return { success: true, message: `Mostrando ${count} órdenes con estado: ${estado}` }
      })
      
      // === ACCIONES PARA ORDEN SELECCIONADA ===
      
      uiContextStore.registerAction('descargarOrdenPDF', async () => {
        if (!self.selectedOrder) {
          return { success: false, message: 'No hay orden seleccionada. Primero selecciona una orden con seleccionarOrdenCompra.' }
        }
        
        try {
          await self.downloadOrderPDF()
          return { success: true, message: `PDF de la orden ${self.selectedOrder.order_number} descargado correctamente.` }
        } catch (error) {
          return { success: false, message: `Error al descargar PDF: ${error.message}` }
        }
      })
      
      uiContextStore.registerAction('enviarOrdenEmail', async () => {
        if (!self.selectedOrder) {
          return { success: false, message: 'No hay orden seleccionada. Primero selecciona una orden.' }
        }
        
        if (self.selectedOrder.status !== 'pending') {
          return { success: false, message: 'Solo se pueden enviar por email las órdenes pendientes.' }
        }
        
        try {
          await self.sendOrderByEmail()
          return { success: true, message: `Orden ${self.selectedOrder.order_number} enviada por email al proveedor.` }
        } catch (error) {
          return { success: false, message: `Error al enviar email: ${error.message}` }
        }
      })
      
      uiContextStore.registerAction('enviarOrdenWhatsApp', async () => {
        if (!self.selectedOrder) {
          return { success: false, message: 'No hay orden seleccionada. Primero selecciona una orden.' }
        }
        
        if (self.selectedOrder.status !== 'pending') {
          return { success: false, message: 'Solo se pueden enviar por WhatsApp las órdenes pendientes.' }
        }
        
        try {
          await self.sendOrderByWhatsApp()
          return { success: true, message: `Abriendo WhatsApp para enviar orden ${self.selectedOrder.order_number} al proveedor.` }
        } catch (error) {
          return { success: false, message: `Error al enviar WhatsApp: ${error.message}` }
        }
      })
      
      uiContextStore.registerAction('abrirModalIngresarStock', () => {
        if (!self.selectedOrder) {
          return { success: false, message: 'No hay orden seleccionada. Primero selecciona una orden.' }
        }
        
        if (self.selectedOrder.status !== 'pending' && self.selectedOrder.status !== 'partial') {
          return { success: false, message: 'Esta orden ya fue recibida completamente.' }
        }
        
        self.openReceiveModal()
        self.updateAIContext()
        
        return {
          success: true,
          message: `Modal de ingreso a stock abierto para orden ${self.selectedOrder.order_number}. El usuario puede marcar los productos recibidos y las cantidades.`,
          productosParaRecibir: self.receiveForm.items.map(item => ({
            nombre: item.product_name,
            ordenado: item.quantity_ordered,
            yRecibido: item.quantity_received,
            pendiente: item.quantity_ordered - item.quantity_received
          }))
        }
      })
      
      uiContextStore.registerAction('confirmarIngresoStock', async () => {
        if (!self.showReceiveModal) {
          return { success: false, message: 'El modal de ingreso a stock no está abierto. Usa abrirModalIngresarStock primero.' }
        }
        
        const itemsConCantidad = self.receiveForm.items.filter(item => item.quantity_to_receive > 0)
        
        if (itemsConCantidad.length === 0) {
          return { success: false, message: 'No hay productos con cantidad a recibir. El usuario debe marcar los checkbox o ingresar cantidades.' }
        }
        
        try {
          await self.confirmReceive()
          self.updateAIContext() // Actualizar después de confirmar
          return {
            success: true,
            message: `Productos ingresados al stock correctamente. Se recibieron ${itemsConCantidad.length} productos.`
          }
        } catch (error) {
          return { success: false, message: `Error al ingresar productos: ${error.message}` }
        }
      })
      
      // Acción para marcar cantidades recibidas en el modal
      uiContextStore.registerAction('marcarCantidadRecibida', ({ producto, cantidad, recibirTodo }) => {
        if (!self.showReceiveModal) {
          return { success: false, message: 'El modal de ingreso no está abierto.' }
        }
        
        // Buscar el producto en el formulario
        const item = self.receiveForm.items.find(i => 
          i.product_name.toLowerCase().includes(producto.toLowerCase())
        )
        
        if (!item) {
          const disponibles = self.receiveForm.items.map(i => i.product_name).join(', ')
          return { success: false, message: `No encontré "${producto}". Productos disponibles: ${disponibles}` }
        }
        
        const pendiente = item.quantity_ordered - item.quantity_received
        
        if (recibirTodo) {
          item.received_all = true
          item.quantity_to_receive = pendiente
          self.updateAIContext()
          return { success: true, message: `Marcado ${item.product_name}: recibir todo (${pendiente} unidades)` }
        }
        
        if (cantidad !== undefined) {
          if (cantidad > pendiente) {
            return { success: false, message: `Máximo ${pendiente} unidades pendientes para ${item.product_name}` }
          }
          item.quantity_to_receive = cantidad
          item.received_all = cantidad === pendiente
          self.updateAIContext()
          return { success: true, message: `Marcado ${item.product_name}: ${cantidad} unidades` }
        }
        
        return { success: false, message: 'Debes indicar cantidad o recibirTodo: true' }
      })
      
      // Acción para marcar TODOS los productos como recibidos
      uiContextStore.registerAction('recibirTodosProductos', () => {
        if (!self.showReceiveModal) {
          return { success: false, message: 'El modal de ingreso no está abierto.' }
        }
        
        let totalMarcados = 0
        self.receiveForm.items.forEach(item => {
          const pendiente = item.quantity_ordered - item.quantity_received
          if (pendiente > 0) {
            item.received_all = true
            item.quantity_to_receive = pendiente
            totalMarcados++
          }
        })
        
        self.updateAIContext()
        return { success: true, message: `Marcados ${totalMarcados} productos para recibir todo. Ahora confirma con confirmarIngresoStock.` }
      })
      
      uiContextStore.registerAction('marcarOrdenPagada', async () => {
        if (!self.selectedOrder) {
          return { success: false, message: 'No hay orden seleccionada.' }
        }
        
        if (self.selectedOrder.status !== 'pending') {
          return { success: false, message: 'Solo se pueden marcar como pagadas las órdenes pendientes.' }
        }
        
        // Esto abre el modal de recepción
        self.openReceiveModal()
        self.updateAIContext()
        
        return {
          success: true,
          message: `Modal de ingreso a stock abierto. Para marcar como pagada, el usuario debe confirmar los productos recibidos.`
        }
      })
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
