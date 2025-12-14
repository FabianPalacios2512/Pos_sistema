<template>
  <div class="min-h-screen font-sans bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header sin icono -->
      <div class="flex items-center justify-between pb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Clientes</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Administra tu base de clientes y historial de compras</p>
        </div>
        
        <div class="flex items-center gap-3">
          <!-- Botón Secundario -->
          <button @click="refreshCustomers" 
                  :disabled="loading"
                  class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-800 shadow-sm transition-all duration-200 disabled:opacity-50 flex items-center gap-2">
            <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>{{ loading ? 'Actualizando...' : 'Actualizar' }}</span>
          </button>
          
          <!-- Botón Principal -->
          <button @click="openCreateModal" 
                  class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nuevo Cliente</span>
          </button>
        </div>
      </div>
      
      <!-- Métricas Principales con Glassmorphism -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Clientes -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Clientes</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ customers.length }}</p>
            </div>
          </div>
        </div>
        
        <!-- Clientes Activos -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Clientes Activos</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ activeCustomers }}</p>
            </div>
          </div>
        </div>
        
        <!-- Ventas Totales -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Ventas Totales</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatCurrency(totalSales) }}</p>
            </div>
          </div>
        </div>
        
        <!-- Promedio Compra -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Ticket Promedio</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatCurrency(averagePurchase) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenedor Unificado: Filtros + Tabla -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <!-- Panel de Filtros -->
        <div class="p-4 border-b border-gray-200 dark:border-zinc-800">
        <div class="flex flex-wrap items-center gap-3">
          <!-- Búsqueda -->
          <div class="flex-1 min-w-[200px] relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <input v-model="searchTerm" 
                   type="text" 
                   placeholder="Buscar clientes..."
                   class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200">
          </div>
          
          <!-- Filtro Estado -->
          <select v-model="statusFilter" 
                  class="min-w-[140px] px-3 py-3 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200">
            <option value="">Todos los estados</option>
            <option value="active">Activos</option>
            <option value="inactive">Inactivos</option>
          </select>
          
          <!-- Filtro Ciudad -->
          <select v-model="cityFilter" 
                  class="min-w-[140px] px-3 py-3 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200">
            <option value="">Todas las ciudades</option>
            <option v-for="city in uniqueCities" :key="city" :value="city">{{ city }}</option>
          </select>
          
          <!-- Limpiar Filtros -->
          <button @click="searchTerm = ''; statusFilter = ''; cityFilter = ''" 
                  title="Limpiar filtros"
                  class="p-3 text-gray-500 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl border border-transparent hover:border-red-100 dark:hover:border-red-900/30 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
          
          <!-- Toggle Vista -->
          <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-1 border border-gray-200 dark:border-zinc-700 h-[46px] flex items-center gap-1">
            <button @click="setViewMode('table')" 
                    :class="viewMode === 'table' ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'"
                    class="p-2.5 rounded-lg transition-all duration-200">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
              </svg>
            </button>
            <button @click="setViewMode('grid')" 
                    :class="viewMode === 'grid' ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'"
                    class="p-2.5 rounded-lg transition-all duration-200">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Vista de Tarjetas -->
      <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <div v-for="customer in filteredCustomers" 
             :key="customer.id" 
             class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm dark:shadow-black/20 border border-gray-300 dark:border-zinc-800 hover:shadow-md dark:hover:shadow-black/40 transition-all duration-200 overflow-hidden">
          
          <!-- Header de la tarjeta -->
          <div class="p-4 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center">
                  <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">{{ customer.name.charAt(0) }}</span>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ customer.name }}</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">{{ customer.document_type }} {{ customer.document_number }}</p>
                </div>
              </div>
              <span :class="customer.active ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'" 
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide">
                {{ customer.active ? 'Activo' : 'Inactivo' }}
              </span>
            </div>
          </div>

          <!-- Información de la tarjeta -->
          <div class="p-4 space-y-3">
            <div class="flex items-center space-x-2">
              <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
              <span class="text-sm text-gray-600 dark:text-zinc-300">{{ customer.phone || 'Sin teléfono' }}</span>
            </div>
            
            <div class="flex items-center space-x-2">
              <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <span class="text-sm text-gray-600 dark:text-zinc-300">{{ customer.email || 'Sin email' }}</span>
            </div>
            
            <div class="flex items-center space-x-2">
              <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span class="text-sm text-gray-600 dark:text-zinc-300">{{ customer.city }}</span>
            </div>

            <div class="pt-2 border-t border-gray-200 dark:border-zinc-800">
              <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                  <span class="text-gray-500 dark:text-zinc-400">Compras:</span>
                  <span class="font-medium text-gray-900 dark:text-white block">${{ formatCurrency(customer.total_purchases) }}</span>
                </div>
                <div>
                  <span class="text-gray-500 dark:text-zinc-400">Órdenes:</span>
                  <span class="font-medium text-gray-900 dark:text-white block">{{ customer.total_orders || 0 }}</span>
                </div>
              </div>
              
              <template v-if="isCreditiendaEnabled">
                <div class="flex justify-between items-center text-sm mt-2">
                  <span class="text-gray-500 dark:text-zinc-400">Crédito:</span>
                  <span class="font-medium text-gray-900 dark:text-white">${{ formatCurrency(customer.credit_limit) }}</span>
                </div>
                
                <div class="flex justify-between items-center text-sm">
                  <span class="text-gray-500 dark:text-zinc-400">Deuda:</span>
                  <span :class="parseFloat(customer.current_debt) > parseFloat(customer.credit_limit) ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-900 dark:text-white'" 
                        class="font-medium">
                    ${{ formatCurrency(customer.current_debt) }}
                  </span>
                </div>
              </template>
            </div>
          </div>

          <!-- Acciones de la tarjeta -->
          <div class="p-4 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-800/50">
            <div class="flex space-x-2">
              <button @click="viewCustomerHistory(customer)" 
                      class="flex-1 px-3 py-2 bg-blue-50 dark:bg-blue-950 hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-700 dark:text-blue-400 rounded-lg text-sm font-medium transition-all flex items-center justify-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Historial</span>
              </button>
              <button @click="editCustomer(customer)" 
                      class="px-3 py-2 bg-amber-50 dark:bg-amber-950 hover:bg-amber-100 dark:hover:bg-amber-900 text-amber-700 dark:text-amber-400 rounded-lg text-sm font-medium transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista de Tabla -->
      <div v-else>
        <!-- Header de Tabla -->
        <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Lista de Clientes</h2>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">{{ filteredCustomers.length }} clientes encontrados</p>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800">
            <thead class="bg-gray-50 dark:bg-zinc-900">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Cliente</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Contacto</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Compras</th>
                <th v-if="isCreditiendaEnabled" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Crédito</th>
                <th v-if="isLoyaltyEnabled" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Puntos</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Estado</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
              <tr v-for="customer in paginatedCustomers" :key="customer.id" class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 hover:shadow-sm transition-all duration-200 border-b border-gray-200 dark:border-zinc-800">
                <!-- Cliente con Avatar -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                      <span class="font-bold text-base text-blue-600 dark:text-blue-400">
                        {{ customer.name.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    <div class="min-w-0">
                      <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ customer.name }}</div>
                      <div class="text-xs text-gray-500 dark:text-zinc-400">{{ customer.document_type }} {{ customer.document_number }}</div>
                    </div>
                  </div>
                </td>
                
                <!-- Contacto -->
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-700 dark:text-zinc-300 font-medium flex items-center gap-1">
                    <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span>{{ customer.phone || 'Sin teléfono' }}</span>
                  </div>
                  <div class="text-xs text-gray-500 dark:text-zinc-400 truncate mt-0.5" v-if="customer.email">{{ customer.email }}</div>
                  <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ customer.city || '-' }}</div>
                </td>
                
                <!-- Compras -->
                <td class="px-6 py-4">
                  <div class="text-base font-black text-gray-900 dark:text-white">${{ formatCurrency(customer.total_purchases) }}</div>
                  <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ customer.total_orders || 0 }} órdenes</div>
                </td>
                
                <!-- Crédito -->
                <td v-if="isCreditiendaEnabled" class="px-6 py-4">
                  <div class="text-sm text-gray-700 dark:text-zinc-300">
                    <span class="text-xs text-gray-500 dark:text-zinc-400">Límite:</span> 
                    <span class="font-semibold">${{ formatCurrency(customer.credit_limit) }}</span>
                  </div>
                  <div class="text-sm mt-0.5" 
                       :class="parseFloat(customer.current_debt) > parseFloat(customer.credit_limit) ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-gray-700 dark:text-zinc-300'">
                    <span class="text-xs text-gray-500 dark:text-zinc-400">Deuda:</span> 
                    <span class="font-semibold">${{ formatCurrency(customer.current_debt) }}</span>
                  </div>
                </td>
                
                <!-- Puntos de Fidelidad -->
                <td v-if="isLoyaltyEnabled" class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-amber-50 dark:bg-amber-950 rounded-lg flex items-center justify-center flex-shrink-0 border border-amber-100 dark:border-amber-800">
                      <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                      </svg>
                    </div>
                    <div class="min-w-0">
                      <div class="text-base font-bold text-amber-700 dark:text-amber-400">{{ customer.loyalty_points || 0 }}</div>
                      <div class="text-xs text-gray-500 dark:text-zinc-400">puntos</div>
                    </div>
                  </div>
                </td>
                
                <!-- Estado -->
                <td class="px-6 py-4">
                  <span :class="customer.active ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'" 
                        class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide inline-flex items-center">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5" 
                          :class="customer.active ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-rose-500 dark:bg-rose-400'"></span>
                    {{ customer.active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                
                <!-- Acciones -->
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-1.5">
                    <button @click="viewCustomerHistory(customer)" 
                            class="p-2 text-slate-400 dark:text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl border border-transparent hover:border-blue-100 dark:hover:border-blue-900/30 transition-all duration-200"
                            title="Ver historial">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                    <button @click="editCustomer(customer)" 
                            class="p-2 text-slate-400 dark:text-zinc-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-xl border border-transparent hover:border-amber-100 dark:hover:border-amber-900/30 transition-all duration-200"
                            title="Editar">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <button @click="deleteCustomer(customer)" 
                            class="p-2 text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-xl border border-transparent hover:border-rose-100 dark:hover:border-rose-900/30 transition-all duration-200"
                            title="Eliminar">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              
              <!-- Estado vacío -->
              <tr v-if="filteredCustomers.length === 0">
                <td colspan="6" class="px-4 py-12 text-center">
                  <div class="flex flex-col items-center space-y-3">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-700 dark:text-zinc-300">No hay clientes</p>
                      <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">No se encontraron clientes con los filtros aplicados</p>
                    </div>
                    <button @click="clearFilters" class="px-3 py-2 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-medium rounded-lg shadow-lg transition-all duration-200">
                      Limpiar Filtros
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Paginador - Solo mostrar si hay más de 10 clientes -->
        <div v-if="totalItems > 10" class="bg-gray-50 dark:bg-zinc-900 px-6 py-4 border-t border-gray-200 dark:border-zinc-800">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <span class="text-sm text-gray-700 dark:text-zinc-300">
                Mostrando <span class="font-medium">{{ startItem }}</span> a <span class="font-medium">{{ endItem }}</span> de <span class="font-medium">{{ totalItems }}</span> clientes
              </span>
              <select v-model="itemsPerPage" @change="currentPage = 1" class="text-sm px-3 py-2 rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                <option value="10">10 por página</option>
                <option value="25">25 por página</option>
                <option value="50">50 por página</option>
                <option value="100">100 por página</option>
              </select>
            </div>
            
            <div class="flex items-center space-x-2">
              <button @click="currentPage = 1" :disabled="currentPage === 1" 
                      class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                Primero
              </button>
              <button @click="currentPage--" :disabled="currentPage === 1" 
                      class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                Anterior
              </button>
              
              <div class="flex space-x-1">
                <button v-for="page in visiblePages" :key="page" @click="currentPage = page"
                        :class="[
                          'px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                          page === currentPage 
                            ? 'bg-slate-900 dark:bg-slate-700 text-white border border-slate-900 dark:border-slate-700 shadow-lg' 
                            : 'text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800'
                        ]">
                  {{ page }}
                </button>
              </div>
              
              <button @click="currentPage++" :disabled="currentPage === totalPages" 
                      class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                Siguiente
              </button>
              <button @click="currentPage = totalPages" :disabled="currentPage === totalPages" 
                      class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                Último
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!-- Modal Crear/Editar Cliente -->
    </div>
  </div>

  <!-- 🎨 MODAL CREAR/EDITAR CLIENTE - Teleport al Body (evita conflictos z-index) -->
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="showCustomerModal" 
           class="fixed inset-0 bg-black/70 dark:bg-black/85 backdrop-blur-sm flex items-center justify-center p-4 z-[9999] animate-fade-in"
           @click.self="closeCustomerModal">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/60 max-w-4xl w-full max-h-[92vh] overflow-hidden flex flex-col border border-gray-200 dark:border-zinc-800/80"
             @click.stop>
          
          <!-- 🎯 Header Profesional -->
          <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-white dark:from-zinc-900 dark:to-zinc-900/95 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">
                  {{ isEditing ? 'Editar Cliente' : 'Nuevo Cliente' }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-0.5">
                  {{ isEditing ? 'Actualiza la información del cliente' : 'Completa los datos del nuevo cliente' }}
                </p>
              </div>
            </div>
          </div>
          
          <!-- 📋 Contenido con Grid Inteligente -->
          <div class="flex-1 overflow-y-auto px-8 py-6 bg-gray-50/30 dark:bg-zinc-950/30">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6">
              
              <!-- 🧑 COLUMNA 1: Información Personal -->
              <div class="space-y-5">
                <div class="pb-3 border-b-2 border-emerald-500/20 dark:border-emerald-400/20">
                  <h4 class="text-base font-extrabold text-gray-800 dark:text-white uppercase tracking-wide">Información Personal</h4>
                </div>
                
                <!-- Nombre Completo -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2">Nombre Completo *</label>
                  <input v-model="customerForm.name" 
                         type="text" 
                         :class="[
                           'w-full h-12 px-4 border-2 rounded-xl font-medium text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 transition-all duration-200',
                           formErrors.name 
                             ? 'bg-red-50 dark:bg-red-950/30 border-red-400 dark:border-red-600 focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400' 
                             : 'bg-gray-50 dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20'
                         ]"
                         placeholder="Ej: Juan Carlos Pérez Gómez">
                  <p v-if="formErrors.name" class="text-red-600 dark:text-red-400 text-xs font-semibold mt-2 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ formErrors.name }}
                  </p>
                </div>
                
                <!-- Tipo y Número de Documento -->
                <div class="grid grid-cols-5 gap-3">
                  <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2">Tipo Doc.</label>
                    <select v-model="customerForm.document_type" 
                            class="w-full h-12 px-3 border-2 border-gray-200 dark:border-zinc-700 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20 transition-all duration-200">
                      <option value="CC">CC</option>
                      <option value="TI">TI</option>
                      <option value="CE">CE</option>
                      <option value="NIT">NIT</option>
                      <option value="PP">Pasaporte</option>
                    </select>
                  </div>
                  <div class="col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2">Número *</label>
                    <input v-model="customerForm.document_number" 
                           type="text" 
                           :class="[
                             'w-full h-12 px-4 border-2 rounded-xl font-medium text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 transition-all duration-200',
                             formErrors.document_number 
                               ? 'bg-red-50 dark:bg-red-950/30 border-red-400 dark:border-red-600 focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400' 
                               : 'bg-gray-50 dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20'
                           ]"
                           placeholder="1234567890">
                    <p v-if="formErrors.document_number" class="text-red-600 dark:text-red-400 text-xs font-semibold mt-2">{{ formErrors.document_number }}</p>
                  </div>
                </div>
                
                <!-- Fecha Nacimiento y Género -->
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2">Fecha Nacimiento</label>
                    <input v-model="customerForm.birth_date" 
                           type="date" 
                           class="w-full h-12 px-4 border-2 border-gray-200 dark:border-zinc-700 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20 transition-all duration-200">
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2">Género</label>
                    <select v-model="customerForm.gender" 
                            class="w-full h-12 px-4 border-2 border-gray-200 dark:border-zinc-700 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20 transition-all duration-200">
                      <option value="">Seleccionar</option>
                      <option value="M">Masculino</option>
                      <option value="F">Femenino</option>
                      <option value="O">Otro</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- 📞 COLUMNA 2: Contacto y Financiero -->
              <div class="space-y-5">
                <div class="pb-3 border-b-2 border-blue-500/20 dark:border-blue-400/20">
                  <h4 class="text-base font-extrabold text-gray-800 dark:text-white uppercase tracking-wide">Contacto</h4>
                </div>
                
                <!-- Teléfono -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2">Teléfono *</label>
                  <input v-model="customerForm.phone" 
                         type="text" 
                         :class="[
                           'w-full h-12 px-4 border-2 rounded-xl font-medium text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 transition-all duration-200',
                           formErrors.phone 
                             ? 'bg-red-50 dark:bg-red-950/30 border-red-400 dark:border-red-600 focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400' 
                             : 'bg-gray-50 dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20'
                         ]"
                         placeholder="+57 300 123 4567">
                  <p v-if="formErrors.phone" class="text-red-600 dark:text-red-400 text-xs font-semibold mt-2">{{ formErrors.phone }}</p>
                </div>
                
                <!-- Email -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2">Email *</label>
                  <input v-model="customerForm.email" 
                         type="email" 
                         :class="[
                           'w-full h-12 px-4 border-2 rounded-xl font-medium text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 transition-all duration-200',
                           formErrors.email 
                             ? 'bg-red-50 dark:bg-red-950/30 border-red-400 dark:border-red-600 focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400' 
                             : 'bg-gray-50 dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20'
                         ]"
                         placeholder="correo@ejemplo.com">
                  <p v-if="formErrors.email" class="text-red-600 dark:text-red-400 text-xs font-semibold mt-2">{{ formErrors.email }}</p>
                </div>
                
                <!-- Dirección -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2">Dirección</label>
                  <input v-model="customerForm.address" 
                         type="text" 
                         class="w-full h-12 px-4 border-2 border-gray-200 dark:border-zinc-700 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white font-medium placeholder-gray-400 dark:placeholder-zinc-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20 transition-all duration-200"
                         placeholder="Calle 123 #45-67">
                </div>
                
                <!-- Ciudad -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2">Ciudad</label>
                  <input v-model="customerForm.city" 
                         type="text" 
                         class="w-full h-12 px-4 border-2 border-gray-200 dark:border-zinc-700 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white font-medium placeholder-gray-400 dark:placeholder-zinc-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20 transition-all duration-200"
                         placeholder="Bogotá, Colombia">
                </div>
                
                <!-- 💳 Crédito (si está habilitado) -->
                <div v-if="isCreditiendaEnabled" class="pt-2 border-t border-gray-200 dark:border-zinc-800">
                  <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2">Límite de Crédito</label>
                  <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-zinc-400 font-bold">$</span>
                    <input v-model="customerForm.credit_limit" 
                           type="number" 
                           min="0" 
                           step="10000"
                           :class="[
                             'w-full h-12 pl-8 pr-4 border-2 rounded-xl font-bold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 transition-all duration-200',
                             formErrors.credit_limit 
                               ? 'bg-red-50 dark:bg-red-950/30 border-red-400 dark:border-red-600 focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400' 
                               : 'bg-amber-50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800/50 focus:border-amber-500 dark:focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 dark:focus:ring-amber-400/20'
                           ]"
                           placeholder="500000">
                  </div>
                  <p v-if="formErrors.credit_limit" class="text-red-600 dark:text-red-400 text-xs font-semibold mt-2">{{ formErrors.credit_limit }}</p>
                </div>
              </div>
            </div>

            <!-- ✅ Checkboxes de Estado -->
            <div class="mt-8 pt-6 border-t-2 border-gray-200 dark:border-zinc-800 flex flex-wrap items-center gap-6">
              <label class="flex items-center gap-3 cursor-pointer group">
                <div class="relative">
                  <input v-model="customerForm.active" 
                         type="checkbox" 
                         class="peer w-6 h-6 cursor-pointer appearance-none rounded-lg border-2 border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 checked:bg-emerald-600 dark:checked:bg-emerald-500 checked:border-emerald-600 dark:checked:border-emerald-500 transition-all duration-200">
                  <svg class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <span class="text-sm font-bold text-gray-700 dark:text-zinc-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Cliente Activo</span>
              </label>

              <label class="flex items-center gap-3 cursor-pointer group" :class="{'opacity-50 cursor-not-allowed': !isCreditiendaEnabled}">
                <div class="relative">
                  <input v-model="customerForm.credit_active" 
                         type="checkbox" 
                         :disabled="!isCreditiendaEnabled"
                         @click="handleCreditCheckboxClick"
                         class="peer w-6 h-6 cursor-pointer appearance-none rounded-lg border-2 border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 checked:bg-amber-600 dark:checked:bg-amber-500 checked:border-amber-600 dark:checked:border-amber-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                  <svg class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <span class="text-sm font-bold text-gray-700 dark:text-zinc-300 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                  Habilitar Crédito
                  <span v-if="!isCreditiendaEnabled" class="ml-2 px-2 py-0.5 bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 text-xs font-extrabold rounded-md uppercase tracking-wider">Premium</span>
                </span>
              </label>
            </div>
          </div>
          
          <!-- 🎯 Footer con Botones Profesionales -->
          <div class="px-8 py-5 bg-gray-50 dark:bg-zinc-950/50 border-t border-gray-200 dark:border-zinc-800 flex items-center justify-between">
            <p class="text-xs text-gray-500 dark:text-zinc-500">
              <span class="text-red-500">*</span> Campos requeridos
            </p>
            <div class="flex items-center gap-3">
              <button @click="closeCustomerModal" 
                      class="px-6 py-3 text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white text-sm font-bold rounded-xl hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all duration-200">
                Cancelar
              </button>
              <button @click="saveCustomer" 
                      :disabled="loading || !customerForm.name || !customerForm.document_number"
                      class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white text-sm font-extrabold rounded-xl shadow-lg shadow-emerald-500/30 dark:shadow-emerald-400/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none transition-all duration-300 flex items-center gap-2">
                <svg v-if="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ loading ? 'Guardando...' : (isEditing ? 'Actualizar Cliente' : 'Crear Cliente') }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Modal Historial de Cliente -->
  <CustomerHistoryModal
        v-if="showHistoryModal"
        :customer="selectedCustomer"
        @close="showHistoryModal = false"
      />
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { customersService } from '../services/customersService.js'
import { useToast } from '../composables/useToast.js'
import { useCreditienda } from '../composables/useCreditienda.js'
import CustomerHistoryModal from './CustomerHistoryModal.vue'
import { appStore } from '../store/appStore.js'

// Sistema de toasts
const { showSuccess, showError, showWarning, showInfo } = useToast()

// Sistema de Creditienda
const { isCreditiendaEnabled, showCreditiendaUpgradeModal } = useCreditienda()

// Sistema de Fidelización (Loyalty Points)
const isLoyaltyEnabled = computed(() => {
  return appStore.systemSettings?.enable_loyalty_system || false
})

// Estado reactivo
const loading = ref(false)
const customers = ref([])
const searchTerm = ref('')
const statusFilter = ref('')
const cityFilter = ref('')
const viewMode = ref('table')

// 💾 Sistema de Preferencias del Usuario
const USER_PREFERENCES_KEY = 'customers_user_preferences'

// Paginación
const currentPage = ref(1)
const itemsPerPage = ref(25)

// Modals
const showCustomerModal = ref(false)
const showHistoryModal = ref(false)
const isEditing = ref(false)
const selectedCustomer = ref(null)

const customerForm = ref({
  name: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  document_type: 'CC',
  document_number: '',
  birth_date: '',
  gender: '',
  credit_limit: 0,
  current_debt: 0,
  active: true,
  credit_active: false
})

// Cargar preferencias del usuario
const loadUserPreferences = () => {
  try {
    const savedPreferences = localStorage.getItem(USER_PREFERENCES_KEY)
    if (savedPreferences) {
      const preferences = JSON.parse(savedPreferences)
      
      // Aplicar preferencias guardadas
      viewMode.value = preferences.viewMode || 'table'
      itemsPerPage.value = preferences.itemsPerPage || 25
      statusFilter.value = preferences.statusFilter || ''
      
      console.log('✅ Preferencias del usuario cargadas:', preferences)
    }
  } catch (error) {
    console.warn('⚠️ Error cargando preferencias del usuario:', error)
  }
}

// Guardar preferencias del usuario
const saveUserPreferences = () => {
  try {
    const preferences = {
      viewMode: viewMode.value,
      itemsPerPage: itemsPerPage.value,
      statusFilter: statusFilter.value,
      lastUpdated: new Date().toISOString()
    }
    
    localStorage.setItem(USER_PREFERENCES_KEY, JSON.stringify(preferences))
    console.log('💾 Preferencias guardadas:', preferences)
    
  } catch (error) {
    console.warn('⚠️ Error guardando preferencias:', error)
  }
}

// Método para cambiar vista
const setViewMode = (mode) => {
  viewMode.value = mode
  saveUserPreferences()
}

// Validaciones del formulario
const formErrors = ref({})

const validateForm = () => {
  formErrors.value = {}
  
  // Validar nombre (requerido)
  if (!customerForm.value.name || !customerForm.value.name.trim()) {
    formErrors.value.name = 'El nombre es requerido'
  }
  
  // Validar email (requerido y formato)
  if (!customerForm.value.email || !customerForm.value.email.trim()) {
    formErrors.value.email = 'El email es requerido'
  } else if (!/\S+@\S+\.\S+/.test(customerForm.value.email)) {
    formErrors.value.email = 'El formato del email no es válido'
  }
  
  // Validar teléfono (requerido)
  if (!customerForm.value.phone || !customerForm.value.phone.trim()) {
    formErrors.value.phone = 'El teléfono es requerido'
  }
  
  // Validar número de documento (requerido)
  if (!customerForm.value.document_number || !customerForm.value.document_number.trim()) {
    formErrors.value.document_number = 'El número de documento es requerido'
  }
  
  // Validar dirección (opcional pero si existe debe tener contenido)
  if (customerForm.value.address && !customerForm.value.address.trim()) {
    formErrors.value.address = 'La dirección no puede estar vacía'
  }
  
  // Validar ciudad (opcional pero si existe debe tener contenido)
  if (customerForm.value.city && !customerForm.value.city.trim()) {
    formErrors.value.city = 'La ciudad no puede estar vacía'
  }
  
  // Validar límite de crédito (no negativo)
  if (customerForm.value.credit_limit < 0) {
    formErrors.value.credit_limit = 'El límite de crédito no puede ser negativo'
  }
  
  return Object.keys(formErrors.value).length === 0
}

// Computed properties
const filteredCustomers = computed(() => {
  let filtered = customers.value

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(customer => 
      (customer.name || '').toLowerCase().includes(term) ||
      (customer.email || '').toLowerCase().includes(term) ||
      (customer.phone || '').includes(term) ||
      (customer.document_number || '').includes(term)
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(customer => {
      if (statusFilter.value === 'active') return customer.active
      if (statusFilter.value === 'inactive') return !customer.active
      return true
    })
  }

  if (cityFilter.value) {
    filtered = filtered.filter(customer => customer.city === cityFilter.value)
  }

  return filtered
})

// Computed properties para paginación
const totalItems = computed(() => filteredCustomers.value.length)
const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage.value))
const startItem = computed(() => (currentPage.value - 1) * itemsPerPage.value + 1)
const endItem = computed(() => Math.min(currentPage.value * itemsPerPage.value, totalItems.value))

const paginatedCustomers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredCustomers.value.slice(start, end)
})

const visiblePages = computed(() => {
  const delta = 2
  const range = []
  const rangeWithDots = []

  for (let i = Math.max(2, currentPage.value - delta); 
       i <= Math.min(totalPages.value - 1, currentPage.value + delta); 
       i++) {
    range.push(i)
  }

  if (currentPage.value - delta > 2) {
    rangeWithDots.push(1, '...')
  } else {
    rangeWithDots.push(1)
  }

  rangeWithDots.push(...range)

  if (currentPage.value + delta < totalPages.value - 1) {
    rangeWithDots.push('...', totalPages.value)
  } else {
    rangeWithDots.push(totalPages.value)
  }

  return rangeWithDots.filter((item, index, array) => array.indexOf(item) === index && item !== '...' && item <= totalPages.value)
})

const activeCustomers = computed(() => customers.value.filter(c => c.active).length)

const totalSales = computed(() => 
  customers.value.reduce((sum, c) => sum + parseFloat(c.total_purchases || 0), 0)
)

const totalDebt = computed(() => 
  customers.value.reduce((sum, c) => sum + parseFloat(c.current_debt || 0), 0)
)

const averagePurchase = computed(() => {
  const totalCustomers = customers.value.length
  return totalCustomers > 0 ? totalSales.value / totalCustomers : 0
})

const uniqueCities = computed(() => {
  const cities = customers.value.map(c => c.city).filter(Boolean)
  return [...new Set(cities)].sort()
})

// Métodos
const loadCustomers = async () => {
  try {
    loading.value = true
    const response = await customersService.getAll()
    console.log('Respuesta clientes:', response)
    
    customers.value = response.data || []
    console.log('Clientes cargados:', customers.value.length)
  } catch (error) {
    console.error('Error cargando clientes:', error)
    alert('Error al cargar los clientes')
  } finally {
    loading.value = false
  }
}

const refreshCustomers = async () => {
  console.log('Refrescando clientes...')
  await loadCustomers()
}

const openCreateModal = () => {
  isEditing.value = false
  formErrors.value = {} // Limpiar errores
  customerForm.value = {
    name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    document_type: 'CC',
    document_number: '',
    birth_date: '',
    gender: '',
    credit_limit: 0,
    current_debt: 0,
    active: true
  }
  showCustomerModal.value = true
}

// Manejar click en checkbox de crédito
const handleCreditCheckboxClick = (event) => {
  if (!isCreditiendaEnabled.value) {
    event.preventDefault()
    showCreditiendaUpgradeModal()
  }
}

const editCustomer = (customer) => {
  isEditing.value = true
  customerForm.value = { ...customer }
  showCustomerModal.value = true
}

const closeCustomerModal = () => {
  showCustomerModal.value = false
  isEditing.value = false
}

const saveCustomer = async () => {
  // Validar formulario antes de guardar
  if (!validateForm()) {
    showError('Por favor corrige los errores en el formulario')
    return
  }
  
  try {
    loading.value = true
    
    // Preparar datos del cliente
    const customerData = { ...customerForm.value }
    
    if (isEditing.value) {
      await customersService.update(customerForm.value.id, customerData)
      showSuccess('Cliente actualizado exitosamente')
    } else {
      await customersService.create(customerData)
      showSuccess('Cliente creado exitosamente')
    }
    
    await loadCustomers()
    closeCustomerModal()
  } catch (error) {
    console.error('Error guardando cliente:', error)
    showError('Error al guardar el cliente: ' + (error.message || 'Error desconocido'))
  } finally {
    loading.value = false
  }
}

const viewCustomerHistory = (customer) => {
  selectedCustomer.value = customer
  showHistoryModal.value = true
}

const deleteCustomer = async (customer) => {
  if (!confirm(`¿Estás seguro de eliminar el cliente "${customer.name}"?`)) return
  
  try {
    loading.value = true
    await customersService.delete(customer.id)
    showSuccess('Cliente eliminado exitosamente')
    await loadCustomers()
  } catch (error) {
    console.error('Error eliminando cliente:', error)
    showError('Error al eliminar el cliente')
  } finally {
    loading.value = false
  }
}

const getCustomerColor = (name) => {
  const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316']
  const index = name.charCodeAt(0) % colors.length
  return colors[index]
}

const exportCustomers = () => {
  console.log('Exportar clientes')
  alert('Funcionalidad de exportación próximamente')
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO').format(parseFloat(value || 0))
}

// Funciones para el punto de ventas
const createQuickCustomer = async (customerData) => {
  try {
    const response = await customersService.create(customerData)
    await loadCustomers()
    return response
  } catch (error) {
    throw error
  }
}

// Exportar funciones para uso en punto de ventas
defineExpose({
  createQuickCustomer,
  loadCustomers,
  customers
})

// Watchers para guardar preferencias automáticamente
watch(viewMode, (newValue, oldValue) => {
  if (oldValue !== undefined && newValue !== oldValue) {
    saveUserPreferences()
  }
}, { immediate: false })

watch(itemsPerPage, (newValue, oldValue) => {
  if (oldValue !== undefined && newValue !== oldValue) {
    saveUserPreferences()
  }
}, { immediate: false })

watch(statusFilter, (newValue, oldValue) => {
  if (oldValue !== undefined && newValue !== oldValue) {
    saveUserPreferences()
  }
}, { immediate: false })

// Inicialización
onMounted(async () => {
  // 🔧 Cargar preferencias del usuario primero
  loadUserPreferences()
  
  await loadCustomers()
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
.bg-gradient-to-br:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

/* Estilos para estados vacíos */
.empty-state {
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* 🎭 Transiciones Modal - Animación suave entrada/salida */
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
  transform: scale(0.95) translateY(20px);
}
</style>