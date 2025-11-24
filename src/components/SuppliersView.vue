<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 font-sans">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      
      <!-- 🎯 Header Elegante y Profesional OBLIGATORIO -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-black rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Proveedores</h1>
            <p class="text-sm text-gray-600">Administra tu red de proveedores y relaciones comerciales</p>
          </div>
        </div>
        
  <div class="flex items-center space-x-3">
          <!-- Botón Secundario (Neutro Slate) -->
          <button @click="refreshSuppliers" 
                  :disabled="loading"
                  class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>{{ loading ? 'Actualizando...' : 'Actualizar' }}</span>
          </button>
          
          <!-- Botón Principal (Negro, consistente con otras vistas) -->
          <button @click="openCreateModal"
                  class="px-5 py-2.5 bg-black hover:bg-gray-900 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Nuevo Proveedor</span>
          </button>
        </div>
      </div>
      
      <!-- 📊 Métricas Principales OBLIGATORIAS -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Proveedores -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 shadow-sm hover:shadow-lg transition-all duration-200 group">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Total Proveedores</h3>
                <span class="text-xs font-medium text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full border border-blue-200">
                  Total
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ suppliers.length }}</p>
              <p class="text-sm text-gray-500">Registrados en sistema</p>
            </div>
          </div>
        </div>

        <!-- Proveedores Activos -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 shadow-sm hover:shadow-lg transition-all duration-200 group">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Activos</h3>
                <span class="text-xs font-medium text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-200">
                  Operando
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ activeSuppliers }}</p>
              <p class="text-sm text-gray-500">En operación activa</p>
            </div>
          </div>
        </div>

        <!-- Deuda Total -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 shadow-sm hover:shadow-lg transition-all duration-200 group">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Deuda Total</h3>
                <span class="text-xs font-medium text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full border border-amber-200">
                  Pendiente
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ formatCurrency(totalDebt) }}</p>
              <p class="text-sm text-gray-500">Cuentas por pagar</p>
            </div>
          </div>
        </div>

        <!-- Sobre Límite -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 shadow-sm hover:shadow-lg transition-all duration-200 group">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Sobre Límite</h3>
                <span class="text-xs font-medium text-red-700 bg-red-100 px-2 py-0.5 rounded-full border border-red-200">
                  Alerta
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ suppliersOverLimit }}</p>
              <p class="text-sm text-gray-500">Exceden crédito</p>
            </div>
          </div>
        </div>

      </div>

      <!-- 🔍 Panel de Filtros OBLIGATORIO -->
      <div class="bg-gradient-to-br from-slate-50 to-blue-50/30 rounded-lg shadow-sm border border-slate-200 p-3">
        <div class="flex flex-wrap items-center gap-3">
          <!-- Búsqueda -->
          <div class="flex-1 min-w-[200px] relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
            <input v-model="searchTerm" 
                   type="text" 
                   placeholder="Buscar proveedores..."
                   class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200">
          </div>
          
          <!-- Filtro Estado -->
          <select v-model="statusFilter" 
                  class="min-w-[140px] px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200">
            <option value="">Todos los estados</option>
            <option value="active">Activos</option>
            <option value="inactive">Inactivos</option>
          </select>
          
          <!-- Filtro Ciudad -->
          <select v-model="cityFilter" 
                  class="min-w-[140px] px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200">
            <option value="">Todas las ciudades</option>
            <option v-for="city in uniqueCities" :key="city" :value="city">{{ city }}</option>
          </select>

          <!-- Toggle Vista -->
          <div class="flex items-center space-x-2">
            <button @click="viewMode = 'table'" 
                    :class="viewMode === 'table' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600'"
                    class="p-2.5 rounded-lg transition-all duration-200 hover:bg-blue-700">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
              </svg>
            </button>
            <button @click="viewMode = 'grid'" 
                    :class="viewMode === 'grid' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600'"
                    class="p-2.5 rounded-lg transition-all duration-200 hover:bg-blue-700">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
            </button>
          </div>
          
          <!-- Botón Limpiar Filtros (Papelería) - Compacto -->
          <button @click="clearFilters" 
                  class="p-2 bg-white hover:bg-slate-50 text-slate-500 hover:text-slate-700 rounded-lg border border-slate-200 hover:border-slate-300 transition-all duration-200 flex items-center justify-center"
                  title="Limpiar filtros">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Vista de Tarjetas -->
      <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="supplier in paginatedSuppliers" 
             :key="supplier.id" 
             class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden">
          
          <!-- Header de la tarjeta -->
          <div class="p-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                  <span class="text-emerald-600 font-bold text-sm">{{ supplier.name.charAt(0) }}</span>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 text-sm">{{ supplier.name }}</h3>
                  <p class="text-xs text-gray-500">{{ supplier.contact_name }}</p>
                </div>
              </div>
              <span :class="supplier.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" 
                    class="px-2 py-0.5 rounded-full text-xs font-semibold">
                {{ supplier.active ? 'Activo' : 'Inactivo' }}
              </span>
            </div>
          </div>

          <!-- Información de la tarjeta -->
          <div class="p-4 space-y-3">
            <div class="flex items-center space-x-2">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
              <span class="text-sm text-gray-600">{{ supplier.phone || 'Sin teléfono' }}</span>
            </div>
            
            <div class="flex items-center space-x-2">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <span class="text-sm text-gray-600">{{ supplier.email || 'Sin email' }}</span>
            </div>
            
            <div class="flex items-center space-x-2">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span class="text-sm text-gray-600">{{ supplier.city }}, {{ supplier.country }}</span>
            </div>

            <div class="pt-2 border-t border-gray-100">
              <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500">Límite:</span>
                <span class="font-medium">${{ formatCurrency(supplier.credit_limit) }}</span>
              </div>
              <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500">Deuda:</span>
                <span :class="parseFloat(supplier.current_debt) > parseFloat(supplier.credit_limit) ? 'text-red-600 font-semibold' : 'text-gray-900'" 
                      class="font-medium">
                  ${{ formatCurrency(supplier.current_debt) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Acciones de la tarjeta -->
          <div class="p-4 border-t border-gray-100 bg-gray-50">
            <div class="flex space-x-2">
              <button @click="viewSupplierOrders(supplier)" 
                      class="flex-1 px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm font-medium transition-all flex items-center justify-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span>Órdenes</span>
              </button>
              <button @click="editSupplier(supplier)" 
                      class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista de Tabla Profesional -->
      <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header de Tabla -->
        <div class="bg-gray-50 border-b border-gray-200 px-4 py-3 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900">Lista de Proveedores</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ paginatedSuppliers.length }} de {{ filteredSuppliers.length }} proveedores</p>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Proveedor</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Contacto</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Ubicación</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Finanzas</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Estado</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="supplier in paginatedSuppliers" :key="supplier.id" class="hover:bg-gray-50 transition-colors">
                <!-- Proveedor con Avatar -->
                <td class="px-3 py-3">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                         :style="{ backgroundColor: getSupplierColor(supplier.name) + '20' }">
                      <span class="font-bold text-sm" :style="{ color: getSupplierColor(supplier.name) }">
                        {{ supplier.name.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    <div class="min-w-0">
                      <div class="text-sm font-semibold text-gray-900 truncate">{{ supplier.name }}</div>
                      <div class="text-xs text-gray-500">{{ supplier.tax_id || 'Sin NIT' }}</div>
                    </div>
                  </div>
                </td>
                
                <!-- Contacto -->
                <td class="px-3 py-3">
                  <div class="text-sm text-gray-900 font-medium">{{ supplier.contact_name || '-' }}</div>
                  <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span>{{ supplier.phone || 'Sin teléfono' }}</span>
                  </div>
                  <div class="text-xs text-gray-500 truncate mt-0.5" v-if="supplier.email">{{ supplier.email }}</div>
                </td>
                
                <!-- Ubicación -->
                <td class="px-3 py-3">
                  <div class="text-sm font-medium text-gray-900">{{ supplier.city || '-' }}</div>
                  <div class="text-xs text-gray-500">{{ supplier.country || 'Colombia' }}</div>
                </td>
                
                <!-- Finanzas -->
                <td class="px-3 py-3">
                  <div class="text-sm text-gray-900">
                    <span class="text-xs text-gray-500">Límite:</span> 
                    <span class="font-semibold">${{ formatCurrency(supplier.credit_limit) }}</span>
                  </div>
                  <div class="text-sm mt-0.5" 
                       :class="parseFloat(supplier.current_debt) > parseFloat(supplier.credit_limit) ? 'text-red-600 font-bold' : 'text-gray-600'">
                    <span class="text-xs text-gray-500">Deuda:</span> 
                    <span class="font-semibold">${{ formatCurrency(supplier.current_debt) }}</span>
                  </div>
                </td>
                
                <!-- Estado -->
                <td class="px-3 py-3">
                  <span :class="supplier.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" 
                        class="px-2 py-0.5 rounded-full text-xs font-semibold inline-flex items-center">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5" 
                          :class="supplier.active ? 'bg-green-500' : 'bg-red-500'"></span>
                    {{ supplier.active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                
                <!-- Acciones -->
                <td class="px-3 py-3">
                  <div class="flex items-center gap-1.5">
                    <button @click="viewSupplierInfo(supplier)" 
                            class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg transition-all hover:shadow-sm"
                            title="Ver información">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </button>
                    <button @click="editSupplier(supplier)" 
                            class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200 rounded-lg transition-all hover:shadow-sm"
                            title="Editar">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <button @click="openToggleConfirm(supplier)" 
                            :class="[
                              'p-1.5 rounded-lg transition-all hover:shadow-sm border',
                              supplier.active 
                                ? 'bg-red-50 hover:bg-red-100 text-red-600 border-red-200' 
                                : 'bg-green-50 hover:bg-green-100 text-green-600 border-green-200'
                            ]"
                            :title="supplier.active ? 'Inhabilitar' : 'Habilitar'">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="supplier.active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              
              <!-- Estado vacío -->
              <tr v-if="paginatedSuppliers.length === 0">
                <td colspan="6" class="px-4 py-12 text-center">
                  <div class="flex flex-col items-center space-y-3">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-700">No hay proveedores</p>
                      <p class="text-xs text-gray-500 mt-1">No se encontraron proveedores con los filtros aplicados</p>
                    </div>
                    <button @click="clearFilters" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                      Limpiar Filtros
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 📄 PAGINADOR ESTÁNDAR EMPRESARIAL -->
        <div class="bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-between">
          <!-- Información izquierda -->
          <div class="flex items-center space-x-3">
            <!-- Selector items por página -->
            <div class="flex items-center space-x-2">
              <span class="text-xs font-medium text-gray-700">Mostrar:</span>
              <select v-model="itemsPerPage" 
                      @change="currentPage = 1"
                      class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
              <span class="text-xs text-gray-700">por página</span>
            </div>
            
            <!-- Info de paginación -->
            <div class="text-xs text-gray-700">
              Mostrando {{ startItem }} a {{ endItem }} de {{ filteredSuppliers.length }} registros
            </div>
          </div>
          
          <!-- Controles derecha -->
          <div class="flex items-center space-x-1">
            <!-- Primera página -->
            <button @click="currentPage = 1" 
                    :disabled="currentPage === 1"
                    class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
              </svg>
            </button>
            
            <!-- Anterior -->
            <button @click="currentPage--" 
                    :disabled="currentPage === 1"
                    class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
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
                            ? 'bg-blue-600 text-white border border-blue-600' 
                            : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
                        ]">
                  {{ page }}
                </button>
                <span v-else-if="Math.abs(page - currentPage) === 3" class="px-1 text-gray-400 text-xs">...</span>
              </template>
            </div>
            
            <!-- Siguiente -->
            <button @click="currentPage++" 
                    :disabled="currentPage === totalPages"
                    class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
            
            <!-- Última página -->
            <button @click="currentPage = totalPages" 
                    :disabled="currentPage === totalPages"
                    class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Modal Crear/Editar Proveedor -->
      <div v-if="showSupplierModal" 
           class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
           @click.self="closeSupplierModal">
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full shadow-xl max-h-[90vh] overflow-y-auto">
          <h3 class="text-lg font-semibold text-gray-900 mb-6">
            {{ isEditing ? 'Editar Proveedor' : 'Nuevo Proveedor' }}
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 space-y-4 md:space-y-0">
            <!-- Información General -->
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Empresa</label>
                <input v-model="supplierForm.name" 
                       type="text" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Ej: Distribuidora Central">
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Persona de Contacto</label>
                <input v-model="supplierForm.contact_name" 
                       type="text" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Nombre del contacto">
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">NIT / RUT</label>
                <input v-model="supplierForm.tax_id" 
                       type="text" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="123456789-0">
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                <input v-model="supplierForm.phone" 
                       type="text" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="+57 300 123 4567">
              </div>
            </div>

            <!-- Información de Contacto y Financiera -->
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input v-model="supplierForm.email" 
                       type="email" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="contacto@empresa.com">
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                <input v-model="supplierForm.address" 
                       type="text" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Calle 123 #45-67">
              </div>
              
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad</label>
                  <input v-model="supplierForm.city" 
                         type="text" 
                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                         placeholder="Bogotá">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">País</label>
                  <input v-model="supplierForm.country" 
                         type="text" 
                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                         placeholder="Colombia">
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Límite de Crédito</label>
                <input v-model="supplierForm.credit_limit" 
                       type="number" 
                       min="0" 
                       step="0.01"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="0.00">
              </div>
            </div>
          </div>

          <!-- Estado Activo -->
          <div class="mt-4 flex items-center">
            <input v-model="supplierForm.active" 
                   type="checkbox" 
                   class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
            <label class="ml-2 text-sm font-medium text-gray-700">Proveedor activo</label>
          </div>
          
          <!-- Botones de Acción -->
          <div class="flex items-center gap-3 mt-6">
            <button @click="closeSupplierModal" 
                    class="flex-1 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50">
              Cancelar
            </button>
            <button @click="saveSupplier" 
                    :disabled="loading || !supplierForm.name || !supplierForm.contact_name"
                    class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-medium">
              {{ loading ? 'Guardando...' : (isEditing ? 'Actualizar' : 'Crear') }} Proveedor
            </button>
          </div>
        </div>
      </div>

      <!-- 🔔 Toast Notification -->
      <div v-if="toast.show" 
           :class="[
             'fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-2xl transform transition-all duration-300 ease-in-out flex items-center space-x-3 max-w-md',
             toast.type === 'success' ? 'bg-green-500 text-white' : 
             toast.type === 'error' ? 'bg-red-500 text-white' : 
             toast.type === 'warning' ? 'bg-amber-500 text-white' : 
             'bg-blue-500 text-white'
           ]">
        <svg v-if="toast.type === 'success'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <svg v-else-if="toast.type === 'error'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <svg v-else-if="toast.type === 'warning'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <svg v-else class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
          <p class="font-semibold text-sm">{{ toast.message }}</p>
        </div>
        <button @click="toast.show = false" class="text-white hover:text-gray-200 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- ⚠️ Modal de Confirmación Inhabilitar/Habilitar -->
      <div v-if="showConfirmModal" 
           class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
           @click.self="closeConfirmModal">
        <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-2xl animate-fade-in">
          <div class="flex items-start space-x-4">
            <div :class="[
              'w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0',
              supplierToToggle?.active ? 'bg-amber-100' : 'bg-green-100'
            ]">
              <svg v-if="supplierToToggle?.active" class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              <svg v-else class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-bold text-gray-900 mb-2">
                {{ supplierToToggle?.active ? 'Inhabilitar Proveedor' : 'Habilitar Proveedor' }}
              </h3>
              <p class="text-sm text-gray-600">
                ¿Está seguro de {{ supplierToToggle?.active ? 'inhabilitar' : 'habilitar' }} al proveedor 
                <strong>{{ supplierToToggle?.name }}</strong>?
              </p>
              <p class="text-xs text-gray-500 mt-2">
                {{ supplierToToggle?.active 
                  ? 'El proveedor dejará de estar disponible para nuevas órdenes.' 
                  : 'El proveedor volverá a estar disponible para órdenes.' }}
              </p>
            </div>
          </div>
          
          <div class="flex gap-3 mt-6">
            <button @click="closeConfirmModal" 
                    class="flex-1 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
              Cancelar
            </button>
            <button @click="toggleSupplierStatus" 
                    :disabled="loading"
                    :class="[
                      'flex-1 px-4 py-2.5 rounded-xl font-semibold text-white transition-all duration-200',
                      loading ? 'bg-gray-400 cursor-not-allowed' : 
                      supplierToToggle?.active ? 'bg-amber-600 hover:bg-amber-700' : 'bg-green-600 hover:bg-green-700'
                    ]">
              {{ loading ? 'Procesando...' : (supplierToToggle?.active ? 'Inhabilitar' : 'Habilitar') }}
            </button>
          </div>
        </div>
      </div>

      <!-- ℹ️ Modal Ver Información del Proveedor -->
      <div v-if="showInfoModal" 
           class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
           @click.self="closeInfoModal">
        <div class="bg-white rounded-xl max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">
          <!-- Header -->
          <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex items-center justify-between rounded-t-xl">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-white">Información del Proveedor</h3>
                <p class="text-sm text-blue-100">{{ selectedSupplierInfo?.name }}</p>
              </div>
            </div>
            <button @click="closeInfoModal" class="text-white hover:text-gray-200 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Contenido -->
          <div class="p-6">
            <div v-if="loading" class="text-center py-12">
              <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"></div>
              <p class="text-sm text-gray-600 mt-4">Cargando información...</p>
            </div>

            <div v-else-if="selectedSupplierInfo" class="space-y-4">
              <!-- Información General -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                  <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                  Información General
                </h4>
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <p class="text-xs text-gray-500">Empresa</p>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedSupplierInfo.name }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Contacto</p>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedSupplierInfo.contact_name || 'N/A' }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">NIT / RUT</p>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedSupplierInfo.tax_id || 'N/A' }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Estado</p>
                    <span :class="[
                      'inline-block px-2 py-1 text-xs font-semibold rounded-full',
                      selectedSupplierInfo.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                    ]">
                      {{ selectedSupplierInfo.active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Contacto -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                  <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                  Datos de Contacto
                </h4>
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <p class="text-xs text-gray-500">Teléfono</p>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedSupplierInfo.phone || 'N/A' }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Email</p>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedSupplierInfo.email || 'N/A' }}</p>
                  </div>
                  <div class="col-span-2">
                    <p class="text-xs text-gray-500">Dirección</p>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedSupplierInfo.address || 'N/A' }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Ciudad</p>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedSupplierInfo.city || 'N/A' }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">País</p>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedSupplierInfo.country || 'N/A' }}</p>
                  </div>
                </div>
              </div>

              <!-- Información Financiera -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                  <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Información Financiera
                </h4>
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <p class="text-xs text-gray-500">Límite de Crédito</p>
                    <p class="text-sm font-bold text-gray-900">${{ selectedSupplierInfo.credit_limit || '0.00' }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Deuda Actual</p>
                    <p class="text-sm font-bold text-gray-900">${{ selectedSupplierInfo.current_debt || '0.00' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="bg-gray-50 border-t px-6 py-4 rounded-b-xl flex justify-end">
            <button @click="closeInfoModal" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { suppliersService } from '../services/suppliersService.js'

// Estado reactivo
const loading = ref(false)
const suppliers = ref([])
const searchTerm = ref('')
const statusFilter = ref('')
const cityFilter = ref('')
const viewMode = ref('table')

// Paginación
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Modales
const showSupplierModal = ref(false)
const isEditing = ref(false)
const showConfirmModal = ref(false)
const showInfoModal = ref(false)
const supplierToToggle = ref(null)
const selectedSupplierInfo = ref(null)

// Toast
const toast = ref({
  show: false,
  message: '',
  type: 'success' // success, error, warning, info
})

const supplierForm = ref({
  name: '',
  contact_name: '',
  phone: '',
  email: '',
  address: '',
  city: '',
  country: 'Colombia',
  tax_id: '',
  credit_limit: 0,
  current_debt: 0,
  active: true
})

// Computed properties
const filteredSuppliers = computed(() => {
  let filtered = suppliers.value

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(supplier => 
      supplier.name.toLowerCase().includes(term) ||
      supplier.contact_name.toLowerCase().includes(term) ||
      supplier.email.toLowerCase().includes(term) ||
      supplier.phone.includes(term)
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(supplier => {
      if (statusFilter.value === 'active') return supplier.active
      if (statusFilter.value === 'inactive') return !supplier.active
      return true
    })
  }

  if (cityFilter.value) {
    filtered = filtered.filter(supplier => supplier.city === cityFilter.value)
  }

  return filtered
})

const activeSuppliers = computed(() => suppliers.value.filter(s => s.active).length)

const totalDebt = computed(() => 
  suppliers.value.reduce((sum, s) => sum + parseFloat(s.current_debt || 0), 0)
)

const suppliersOverLimit = computed(() => 
  suppliers.value.filter(s => parseFloat(s.current_debt || 0) > parseFloat(s.credit_limit || 0)).length
)

// Computed - Paginación
const totalPages = computed(() => 
  Math.ceil(filteredSuppliers.value.length / itemsPerPage.value)
)

const paginatedSuppliers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredSuppliers.value.slice(start, end)
})

const startItem = computed(() => {
  if (filteredSuppliers.value.length === 0) return 0
  return (currentPage.value - 1) * itemsPerPage.value + 1
})

const endItem = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return Math.min(end, filteredSuppliers.value.length)
})

const uniqueCities = computed(() => {
  const cities = suppliers.value.map(s => s.city).filter(Boolean)
  return [...new Set(cities)].sort()
})

// Métodos
const loadSuppliers = async () => {
  try {
    loading.value = true
    const response = await suppliersService.getAll()
    console.log('Respuesta proveedores:', response)
    
    suppliers.value = response.data || []
    console.log('Proveedores cargados:', suppliers.value.length)
  } catch (error) {
    console.error('Error cargando proveedores:', error)
    alert('Error al cargar los proveedores')
  } finally {
    loading.value = false
  }
}

const refreshSuppliers = async () => {
  console.log('Refrescando proveedores...')
  await loadSuppliers()
}

const toggleView = () => {
  viewMode.value = viewMode.value === 'grid' ? 'table' : 'grid'
}

const clearFilters = () => {
  searchTerm.value = ''
  statusFilter.value = ''
  cityFilter.value = ''
}

const openCreateModal = () => {
  isEditing.value = false
  supplierForm.value = {
    name: '',
    contact_name: '',
    phone: '',
    email: '',
    address: '',
    city: '',
    country: 'Colombia',
    tax_id: '',
    credit_limit: 0,
    current_debt: 0,
    active: true
  }
  showSupplierModal.value = true
}

const editSupplier = (supplier) => {
  isEditing.value = true
  supplierForm.value = { ...supplier }
  showSupplierModal.value = true
}

const closeSupplierModal = () => {
  showSupplierModal.value = false
  isEditing.value = false
}

// Función del Toast
const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => {
    toast.value.show = false
  }, 3000)
}

const saveSupplier = async () => {
  try {
    loading.value = true
    
    if (isEditing.value) {
      await suppliersService.update(supplierForm.value.id, supplierForm.value)
      showToast('Proveedor actualizado exitosamente', 'success')
    } else {
      await suppliersService.create(supplierForm.value)
      showToast('Proveedor creado exitosamente', 'success')
    }
    
    await loadSuppliers()
    closeSupplierModal()
  } catch (error) {
    console.error('Error guardando proveedor:', error)
    showToast('Error al guardar el proveedor', 'error')
  } finally {
    loading.value = false
  }
}

const viewSupplierInfo = async (supplier) => {
  try {
    loading.value = true
    const response = await suppliersService.getSupplierInfo(supplier.id)
    selectedSupplierInfo.value = response.data || supplier
    showInfoModal.value = true
  } catch (error) {
    console.error('Error cargando información:', error)
    showToast('Error al cargar la información del proveedor', 'error')
  } finally {
    loading.value = false
  }
}

const openToggleConfirm = (supplier) => {
  supplierToToggle.value = supplier
  showConfirmModal.value = true
}

const toggleSupplierStatus = async () => {
  if (!supplierToToggle.value) return
  
  try {
    loading.value = true
    await suppliersService.toggleStatus(supplierToToggle.value.id)
    
    const action = supplierToToggle.value.active ? 'inhabilitado' : 'habilitado'
    showToast(`Proveedor ${action} exitosamente`, 'success')
    
    await loadSuppliers()
    showConfirmModal.value = false
    supplierToToggle.value = null
  } catch (error) {
    console.error('Error cambiando estado:', error)
    showToast('Error al cambiar el estado del proveedor', 'error')
  } finally {
    loading.value = false
  }
}

const closeConfirmModal = () => {
  showConfirmModal.value = false
  supplierToToggle.value = null
}

const closeInfoModal = () => {
  showInfoModal.value = false
  selectedSupplierInfo.value = null
}

const getSupplierColor = (name) => {
  const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316']
  const index = name.charCodeAt(0) % colors.length
  return colors[index]
}

const exportSuppliers = () => {
  console.log('Exportar proveedores')
  alert('Funcionalidad de exportación próximamente')
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO').format(parseFloat(value || 0))
}

// Inicialización
onMounted(async () => {
  console.log('Módulo de proveedores inicializado')
  await loadSuppliers()
})
</script>

<style scoped>
/* 🎨 ESTILOS EMPRESARIALES OBLIGATORIOS */

/* Animación de entrada principal */
@keyframes fade-in {
  from { 
    opacity: 0; 
    transform: translateY(10px); 
  }
  to { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

/* Transiciones suaves globales */
* {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover effects para tarjetas */
.group:hover {
  transform: translateY(-2px);
}

/* Focus states mejorados */
input:focus,
select:focus {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

/* Botones con efectos mejorados */
button:hover {
  transform: translateY(-1px);
}

button:active {
  transform: translateY(0);
}

/* Estados de las métricas */
.bg-white:hover {
  transform: translateY(-1px);
}

/* Animaciones de carga */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Grid responsive mejorado */
@media (max-width: 768px) {
  .grid {
    gap: 1rem;
  }
}

/* Scrollbar personalizado */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Estados de formulario mejorados */
.border-gray-300:focus-within {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Tipografía optimizada */
.font-sans {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Sombras sutiles */
.shadow-sm {
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.shadow-md:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>