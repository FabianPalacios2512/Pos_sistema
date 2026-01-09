<template>
  <!-- Loading State durante inicialización (evita parpadeo) -->
  <div v-if="isInitializing" class="min-h-screen font-sans bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="flex items-center justify-center min-h-screen">
      <div class="flex flex-col items-center space-y-4">
        <div class="w-16 h-16 border-4 border-slate-200 dark:border-zinc-700 border-t-slate-900 dark:border-t-slate-500 rounded-full animate-spin"></div>
        <p class="text-sm text-gray-600 dark:text-zinc-400 font-medium">Cargando productos...</p>
      </div>
    </div>
  </div>
  
  <!-- Contenido principal (solo cuando la inicialización está completa) -->
  <div v-else class="min-h-screen font-sans bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header - Condicional Fashion/Standard -->
      <div class="flex items-center justify-between" :class="isFashionStore ? 'pb-6' : 'pb-4'">
        <!-- Título Fashion (Tipografía elegante) -->
        <div v-if="isFashionStore">
          <h1 class="text-3xl font-light text-gray-900 dark:text-white tracking-tight">Colección</h1>
          <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1 font-light">Explora nuestros productos</p>
        </div>
        <!-- Título Standard -->
        <div v-else>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Product</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Manage your inventory and catalog</p>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Importar Excel (NUEVO) -->
          <button id="tour-import-excel" @click="showExcelImportModal = true"
                  class="px-4 py-2.5 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 text-sm font-semibold rounded-xl border border-indigo-200 dark:border-indigo-800 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:border-indigo-300 dark:hover:border-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <span>Importar Excel</span>
          </button>
          
          <!-- Botón Neutro (Exportar) -->
          <button id="tour-export-products" @click="exportProducts"
                  class="px-4 py-2.5 bg-slate-50 dark:bg-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-200 text-sm font-semibold rounded-xl border border-slate-200 dark:border-zinc-700 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:border-slate-300 dark:hover:border-zinc-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar</span>
          </button>
          
          <!-- Botón Secundario (Actualizar) -->
          <button @click="refreshProducts"
                  :disabled="loading"
                  class="px-4 py-2.5 bg-slate-50 dark:bg-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-200 text-sm font-semibold rounded-xl border border-slate-200 dark:border-zinc-700 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:border-slate-300 dark:hover:border-zinc-600"
                  :class="{ 'opacity-50 cursor-not-allowed': loading }">
            <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>{{ loading ? 'Sincronizando' : 'Actualizar' }}</span>
          </button>
          
          <!-- Botón Principal (Nuevo Producto) -->
          <button id="tour-new-product"
                  @click="openCreateModal"
                  class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 hover:shadow-slate-400/60 dark:hover:shadow-slate-900/70 transition-all duration-300 transform active:scale-95 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nuevo Producto</span>
          </button>
        </div>
      </div>

      <!-- Métricas Principales - Condicional Fashion/Standard -->
      <div v-if="isFashionStore" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- KPIs Minimalistas Fashion (Sin iconos) -->
        <div class="bg-white/70 dark:bg-zinc-900/40 backdrop-blur-sm rounded-xl px-5 py-4 border border-gray-200 dark:border-zinc-800 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200">
          <p class="text-xs font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-1">Total</p>
          <p class="text-3xl font-light text-gray-900 dark:text-white">{{ displayProducts.length }}</p>
        </div>
        <div class="bg-white/70 dark:bg-zinc-900/40 backdrop-blur-sm rounded-xl px-5 py-4 border border-gray-200 dark:border-zinc-800 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200">
          <p class="text-xs font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-1">Activos</p>
          <p class="text-3xl font-light text-gray-900 dark:text-white">{{ activeProducts }}</p>
        </div>
        <div class="bg-white/70 dark:bg-zinc-900/40 backdrop-blur-sm rounded-xl px-5 py-4 border border-gray-200 dark:border-zinc-800 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200">
          <p class="text-xs font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-1">Stock Bajo</p>
          <p class="text-3xl font-light text-gray-900 dark:text-white">{{ lowStockProducts }}</p>
        </div>
        <div class="bg-white/70 dark:bg-zinc-900/40 backdrop-blur-sm rounded-xl px-5 py-4 border border-gray-200 dark:border-zinc-800 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200">
          <p class="text-xs font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-1">Valor Inventario</p>
          <p class="text-3xl font-light text-gray-900 dark:text-white">${{ formatCurrency(totalValue) }}</p>
        </div>
      </div>
      
      <!-- KPIs Standard (Con iconos) - Estilo fantasma profesional -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <!-- Total Productos -->
        <div class="bg-white/80 dark:bg-zinc-900/60 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-200/60 dark:border-zinc-800/60 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200 hover:shadow-md dark:shadow-black/20">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-slate-100 dark:bg-zinc-800/50 border border-slate-200/50 dark:border-zinc-700/30 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Total Productos</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ displayProducts.length }}</p>
            </div>
          </div>
        </div>

        <!-- Productos Activos -->
        <div class="bg-white/80 dark:bg-zinc-900/60 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-200/60 dark:border-zinc-800/60 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200 hover:shadow-md dark:shadow-black/20">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100/50 dark:border-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Activos</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ activeProducts }}</p>
            </div>
          </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white/80 dark:bg-zinc-900/60 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-200/60 dark:border-zinc-800/60 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200 hover:shadow-md dark:shadow-black/20">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-amber-50 dark:bg-amber-950/30 border border-amber-100/50 dark:border-amber-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Stock Bajo</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ lowStockProducts }}</p>
            </div>
          </div>
        </div>

        <!-- Valor Total -->
        <div class="bg-white/80 dark:bg-zinc-900/60 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-200/60 dark:border-zinc-800/60 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200 hover:shadow-md dark:shadow-black/20">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-blue-50 dark:bg-blue-950/30 border border-blue-100/50 dark:border-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Valor Total</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatCurrency(totalValue) }}</p>
            </div>
          </div>
        </div>

        <!-- Categorías -->
        <div class="bg-white/80 dark:bg-zinc-900/60 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-200/60 dark:border-zinc-800/60 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200 hover:shadow-md dark:shadow-black/20">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100/50 dark:border-indigo-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Categorías</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ uniqueCategories }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenedor Principal Tour - Filtros + Productos -->
      <div id="tour-products-main">
      <!-- Filtros - Diseño limpio y profesional -->
    <div class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-sm rounded-xl p-4 mb-6 border border-gray-200/40 dark:border-zinc-800/40 transition-colors duration-300">
      <div class="flex flex-wrap items-center gap-4">
        <!-- Búsqueda limpia (como Facturas) -->
        <div id="tour-search-products" class="flex-1 min-w-[200px] relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar productos por nombre, SKU..."
            class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-300">
        </div>
        
        <!-- Categoría -->
        <div id="tour-filter-category" class="min-w-[160px]">
          <select
            v-model="categoryFilter"
            class="w-full px-3 py-3 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300">
            <option value="">Todas las categorías</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
          </select>
        </div>
        
        <!-- Estado -->
        <div id="tour-filter-status" class="min-w-[140px]">
          <select
            v-model="statusFilter"
            class="w-full px-3 py-3 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300">
            <option value="">Todos los estados</option>
            <option value="active">Activos</option>
            <option value="inactive">Inactivos</option>
            <option value="low-stock">Stock Bajo</option>
          </select>
        </div>
        
        <!-- Ordenar -->
        <div class="min-w-[140px]">
          <select
            v-model="sortBy"
            class="w-full px-3 py-3 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300">
            <option value="name">Por Nombre</option>
            <option value="price">Por Precio</option>
            <option value="stock">Por Stock</option>
            <option value="created_at">Por Fecha</option>
          </select>
        </div>
        
        <!-- Toggle Vista (Diseño limpio como Facturas) -->
        <div id="tour-view-toggle" class="flex items-center bg-gray-50 dark:bg-zinc-800 rounded-lg p-1 border border-gray-200 dark:border-zinc-700">
          <button
            @click="setViewMode('grid')"
            :class="[
              'flex items-center justify-center px-3 py-1.5 rounded-md transition-all text-xs font-medium',
              viewMode === 'grid' 
                ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm' 
                : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
            ]">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            Tarjetas
          </button>
          
          <button
            @click="setViewMode('table')"
            :class="[
              'flex items-center justify-center px-3 py-1.5 rounded-md transition-all text-xs font-medium',
              viewMode === 'table' 
                ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm' 
                : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
            ]">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            Tabla
          </button>
        </div>
      </div>
    </div>

    <!-- Vista de Tarjetas -->
    <div v-if="viewMode === 'grid'">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="flex flex-col items-center space-y-3">
          <div class="w-12 h-12 border-4 border-blue-200 dark:border-zinc-700 border-t-blue-600 dark:border-t-blue-500 rounded-full animate-spin"></div>
          <p class="text-sm text-gray-500 dark:text-zinc-400">Cargando productos...</p>
        </div>
      </div>

      <!-- Empty State - Icono Limpio y Profesional -->
      <div v-else-if="!loading && !paginatedProducts.length" class="flex justify-center items-center py-20">
        <div class="flex flex-col items-center text-center max-w-md mx-auto">
          
          <!-- Icono de Caja/Paquete Profesional -->
          <div class="w-24 h-24 mb-6 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-50 dark:from-zinc-800 dark:to-zinc-800/50 border border-slate-200 dark:border-zinc-700 flex items-center justify-center shadow-lg shadow-slate-200/50 dark:shadow-black/30 mx-auto">
            <svg class="w-12 h-12 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          
          <!-- Texto -->
          <div class="relative z-10 text-center">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2 text-center">Tu inventario está vacío</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400 leading-relaxed mb-1">
              {{ searchTerm ? 'No se encontraron productos que coincidan con tu búsqueda' : 'Comienza agregando productos para gestionar tu catálogo' }}
            </p>
            <p v-if="!searchTerm" class="text-xs text-gray-400 dark:text-zinc-500">Puedes agregar productos manualmente o importar desde Excel</p>
          </div>
          
          <button v-if="!searchTerm" 
                  @click="openCreateModal" 
                  class="mt-6 px-6 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Agregar Primer Producto</span>
          </button>
        </div>
      </div>

      <!-- Grid de productos -->
      <div v-else class="grid" :class="isFashionStore ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-8' : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-6'">
        
        <!-- 👗 FASHION CARD - Para tiendas de MODA (estilo Lookbook) -->
        <template v-if="isFashionStore">
          <FashionProductCard
            v-for="product in paginatedProducts"
            :key="product.id"
            :product="product"
            @view="viewProduct"
            @edit="editProduct"
          />
        </template>
        
        <!-- 🛒 RETAIL CARD - Diseño minimalista profesional -->
        <template v-else>
          <div v-for="product in paginatedProducts" 
               :key="product.id" 
               @click="viewProduct(product)"
               class="bg-white dark:bg-zinc-900/90 rounded-2xl border border-gray-100 dark:border-zinc-800/60 hover:border-gray-200 dark:hover:border-zinc-700 hover:shadow-xl dark:hover:shadow-black/40 transition-all duration-300 overflow-hidden group cursor-pointer">
          
            <!-- Imagen del producto con overlay en hover -->
            <div class="relative aspect-[4/3] bg-gradient-to-br from-gray-50 to-gray-100 dark:from-zinc-800 dark:to-zinc-900 overflow-hidden">
              <img :src="getProductImage(product)" 
                   :alt="product.name" 
                   @error="(e) => handleImageError(e, product)"
                   class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
              
              <!-- Overlay con acciones en hover -->
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-300 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
                <button @click.stop="viewProduct(product)" 
                        class="w-9 h-9 rounded-full bg-white/95 hover:bg-white text-gray-700 hover:text-gray-900 flex items-center justify-center shadow-lg transform scale-90 group-hover:scale-100 transition-all duration-200"
                        title="Ver">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </button>
                <button @click.stop="editProduct(product)" 
                        class="w-9 h-9 rounded-full bg-white/95 hover:bg-white text-gray-700 hover:text-gray-900 flex items-center justify-center shadow-lg transform scale-90 group-hover:scale-100 transition-all duration-200 delay-75"
                        title="Editar">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
              </div>
              
              <!-- Badge Estado - Esquina superior derecha -->
              <div class="absolute top-3 right-3">
                <span :class="getProductStatus(product) 
                  ? 'bg-emerald-500 text-white' 
                  : 'bg-gray-500 text-white'" 
                      class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide shadow-sm">
                  {{ getProductStatus(product) ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
              
              <!-- Badge Stock Bajo - Esquina superior izquierda -->
              <div v-if="(product.current_stock || 0) <= (product.min_stock || 0)" class="absolute top-3 left-3">
                <span class="bg-amber-500 text-white px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide shadow-sm">
                  Stock Bajo
                </span>
              </div>
            </div>

            <!-- Información del producto - Diseño limpio -->
            <div class="p-4">
              <!-- Nombre -->
              <h3 class="font-semibold text-gray-900 dark:text-white text-[13px] leading-snug line-clamp-1 mb-1" :title="product.name">
                {{ product.name }}
              </h3>
              
              <!-- Categoría -->
              <p class="text-[11px] text-gray-500 dark:text-zinc-500 mb-3">
                {{ product.category?.name || 'Sin categoría' }}
              </p>
              
              <!-- Precio y Stock en línea -->
              <div class="flex items-center justify-between">
                <span class="font-bold text-gray-900 dark:text-white text-lg">
                  ${{ formatCurrency(product.sale_price) }}
                </span>
                <span :class="[
                  'text-sm font-medium px-2 py-0.5 rounded-md',
                  (product.current_stock || 0) <= (product.min_stock || 0) 
                    ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400' 
                    : 'bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400'
                ]">
                  {{ product.current_stock || 0 }} uds
                </span>
              </div>
            </div>
          </div>
        </template>
      </div>

      <!-- Paginador para vista de tarjetas -->
      <div class="mb-6">
        <TablePaginator
          v-if="filteredProducts.length > 10"
          v-model:currentPage="currentPage"
          v-model:itemsPerPage="itemsPerPage"
          :totalPages="totalPages"
          :totalItems="totalItems"
          label="productos"
        />
      </div>
    </div>

    <!-- Vista de Tabla - Diseño limpio y profesional -->
  <div v-else class="bg-white dark:bg-zinc-900 rounded-2xl shadow-lg dark:shadow-black/40 border border-gray-200/80 dark:border-zinc-800/80 overflow-hidden transition-colors duration-300">
      <!-- Header de tabla -->
      <div class="bg-gray-50/50 dark:bg-zinc-800/30 border-b border-gray-100 dark:border-zinc-800/60 px-6 py-4 flex items-center justify-between">
        <div>
          <h2 class="text-base font-bold text-gray-900 dark:text-white">Catálogo de Productos</h2>
          <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">{{ totalItems }} productos encontrados</p>
        </div>
      </div>
      
      <!-- Tabla -->
      <table class="min-w-full">
        <thead>
          <tr class="bg-slate-50/50 dark:bg-zinc-800/20 border-b border-gray-100 dark:border-zinc-800/60">
            <th class="px-6 py-3.5 text-left text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider">
              Producto
            </th>
            <th class="px-6 py-3.5 text-left text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider">
              Categoría
            </th>
            <th class="px-6 py-3.5 text-left text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider">
              Proveedor
            </th>
            <th class="px-6 py-3.5 text-right text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider">
              Precio
            </th>
            
            <!-- Columnas de Stock Dinámicas -->
            <template v-if="showMultipleStockColumns">
              <th v-for="warehouse in availableWarehouses" :key="warehouse.id"
                  class="px-4 py-3.5 text-center text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider">
                <div class="flex items-center justify-center space-x-1">
                  <svg class="w-3 h-3 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  <span>{{ warehouse.name }}</span>
                </div>
              </th>
            </template>
            <template v-else>
              <th class="px-6 py-3.5 text-center text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider">
                Stock
              </th>
            </template>
            
            <th class="px-6 py-3.5 text-center text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider">
              Estado
            </th>
            <th class="px-6 py-3.5 text-center text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-100 dark:divide-zinc-800/60">
          <!-- Loading State -->
          <tr v-if="loading">
            <td :colspan="6 + availableWarehouses.length" class="px-4 py-12 text-center">
              <div class="flex flex-col items-center space-y-3">
                <div class="w-8 h-8 border-4 border-slate-300 dark:border-zinc-600 border-t-slate-600 dark:border-t-slate-400 rounded-full animate-spin"></div>
                <p class="text-sm text-gray-500 dark:text-zinc-500 font-medium">Cargando productos...</p>
              </div>
            </td>
          </tr>

          <!-- Empty State - Icono Limpio y Profesional -->
          <tr v-else-if="!loading && !paginatedProducts.length">
            <td :colspan="6 + availableWarehouses.length" class="px-4 py-16">
              <div class="flex flex-col items-center justify-center text-center max-w-lg mx-auto">
                
                <!-- Icono de Caja/Paquete Profesional -->
                <div class="w-20 h-20 mb-5 rounded-xl bg-gradient-to-br from-slate-100 to-slate-50 dark:from-zinc-800 dark:to-zinc-800/50 border border-slate-200 dark:border-zinc-700 flex items-center justify-center shadow-lg shadow-slate-200/50 dark:shadow-black/30 mx-auto">
                  <svg class="w-10 h-10 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                </div>
                
                <div class="relative z-10 text-center">
                  <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2 text-center">Tu inventario está vacío</h3>
                  <p class="text-sm text-gray-500 dark:text-zinc-400 leading-relaxed">
                    {{ searchTerm ? 'No se encontraron productos que coincidan con tu búsqueda' : 'Comienza agregando productos para gestionar tu catálogo' }}
                  </p>
                </div>
                
                <button v-if="!searchTerm" 
                        @click="openCreateModal" 
                        class="mt-5 px-6 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 inline-flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  <span>Agregar Primer Producto</span>
                </button>
              </div>
            </td>
          </tr>

          <!-- Product Rows - Diseño limpio y profesional -->
          <tr v-for="product in paginatedProducts" 
              :key="product.id"
              @click="viewProduct(product)"
              class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-all duration-200 group cursor-pointer border-b border-gray-100 dark:border-zinc-800/50 last:border-0">
            <!-- Columna Producto (La Estrella) -->
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <div class="relative w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden ring-1 ring-gray-200 dark:ring-zinc-700/50 group-hover:ring-gray-300 dark:group-hover:ring-zinc-600 transition-all" 
                     :class="getProductImage(product) ? 'bg-white dark:bg-zinc-800' : 'bg-gray-100 dark:bg-zinc-800 flex items-center justify-center'">
                  <img v-if="getProductImage(product)"
                       :src="getProductImage(product)" 
                       :alt="product.name" 
                       class="w-full h-full object-cover">
                  <svg v-else class="w-6 h-6 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-15.75 0l1.5-1.875A2.625 2.625 0 018.25 4.5h7.5a2.625 2.625 0 012.25 1.125l1.5 1.875"/>
                  </svg>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <div class="text-sm font-medium text-gray-800 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors truncate">{{ product.name }}</div>
                    <!-- Badge "Producto con Variantes" -->
                    <span v-if="hasVariants(product)" 
                          class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800 rounded-md text-[10px] font-bold uppercase tracking-wide whitespace-nowrap">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                      </svg>
                      {{ getVariantsCount(product) }} Variantes
                    </span>
                  </div>
                  <div class="text-xs text-gray-500 dark:text-zinc-400 font-mono">
                    {{ hasVariants(product) ? 'Múltiples SKUs' : (product.sku || 'SIN SKU') }}
                  </div>
                </div>
              </div>
            </td>
            <!-- Columna Categoría (Badges/Pills) -->
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs bg-slate-50 dark:bg-zinc-800/60 text-slate-600 dark:text-zinc-400 border border-slate-200/60 dark:border-zinc-700/40 font-medium">
                {{ product.category?.name || 'Sin categoría' }}
              </span>
            </td>
            <!-- Columna Proveedor -->
            <td class="px-6 py-4">
              <span v-if="product.supplier?.name" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs bg-indigo-50/80 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-100/60 dark:border-indigo-900/40 font-medium">
                {{ product.supplier.name }}
              </span>
              <span v-else class="text-xs text-gray-400 dark:text-zinc-600 italic">
                Sin proveedor
              </span>
            </td>
            <!-- Columna Precio (Con Rango para Variantes) -->
            <td class="px-6 py-4 text-right">
              <div class="text-sm font-mono font-bold text-gray-900 dark:text-white tabular-nums">
                {{ getPriceRange(product) }}
              </div>
            </td>
            <!-- Columnas de Stock Dinámicas -->
            <template v-if="showMultipleStockColumns">
              <td v-for="warehouse in availableWarehouses" :key="warehouse.id"
                  class="px-4 py-4 text-center">
                <div v-if="getWarehouseStock(product, warehouse.id) !== null" class="flex flex-col items-center">
                  <!-- ✅ Producto EXISTE en esta sede -->
                  <span :class="[
                    'text-sm font-mono font-semibold tabular-nums',
                    getWarehouseStock(product, warehouse.id) <= (product.min_stock || 0) ? 'text-amber-600 dark:text-amber-500' : 'text-gray-900 dark:text-zinc-200'
                  ]">
                    {{ getWarehouseStock(product, warehouse.id) }}
                  </span>
                  <!-- Alerta de stock bajo para esta bodega -->
                  <span v-if="getWarehouseStock(product, warehouse.id) <= (product.min_stock || 0)" 
                        class="text-[10px] font-semibold text-amber-600 dark:text-amber-500 mt-0.5">
                    Bajo
                  </span>
                </div>
                <div v-else class="flex flex-col items-center">
                  <!-- ❌ Producto NO existe en esta sede -->
                  <span class="text-xs font-medium text-gray-400 dark:text-zinc-600 italic">
                    N/A
                  </span>
                  <span class="text-[10px] text-gray-400 dark:text-zinc-600">
                    No aplica
                  </span>
                </div>
              </td>
            </template>
            <template v-else>
              <!-- Columna Stock (Con Resumen para Variantes) -->
              <td class="px-6 py-4 text-center">
                <div class="flex flex-col items-center relative group/stock">
                  <span :class="[
                    'text-base font-mono font-semibold tabular-nums',
                    getTotalStock(product) <= (product.min_stock || 0) ? 'text-amber-600 dark:text-amber-500' : 'text-gray-900 dark:text-zinc-200'
                  ]">
                    {{ hasVariants(product) ? getStockSummary(product).total : getTotalStock(product) }}
                  </span>
                  <!-- Resumen si tiene variantes -->
                  <span v-if="hasVariants(product)" class="text-[10px] text-gray-500 dark:text-zinc-400 mt-0.5">
                    ({{ getStockSummary(product).variants }} variantes)
                  </span>
                  
                  <!-- Tooltip con desglose -->
                  <div v-if="hasVariants(product) && getStockBreakdown(product)" 
                       class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover/stock:opacity-100 transition-opacity pointer-events-none z-10 whitespace-nowrap">
                    <div class="bg-gray-900 dark:bg-zinc-800 text-white text-xs px-3 py-2 rounded-lg shadow-xl border border-gray-700 dark:border-zinc-600">
                      {{ getStockBreakdown(product) }}
                      <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                        <div class="w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900 dark:border-t-zinc-800"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </template>
            <td class="px-6 py-4 text-center">
              <span :class="getProductStatus(product) ? 
                'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : 
                'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'" 
                class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide">
                {{ getProductStatus(product) ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <button @click.stop="viewProduct(product)" 
                  class="p-2.5 text-slate-400 dark:text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950 rounded-lg transition-all"
                  title="Ver detalles">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </button>
                <button @click.stop="editProduct(product)" 
                  class="p-2.5 text-slate-400 dark:text-zinc-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950 rounded-lg transition-all"
                  title="Editar producto">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button @click.stop="toggleProductStatus(product)" 
                  class="p-2.5 rounded-lg transition-all"
                  :class="getProductStatus(product) !== false 
                    ? 'text-slate-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950' 
                    : 'text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950'"
                  :title="getProductStatus(product) !== false ? 'Desactivar producto' : 'Activar producto'">
                  <!-- Activo: Toggle ON (verde cuando hover) -->
                  <svg v-if="getProductStatus(product) !== false" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <!-- Inactivo: Toggle OFF (rojo cuando hover) -->
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Paginador con margen inferior -->
      <div class="mb-6">
        <TablePaginator
          v-if="filteredProducts.length > 10"
          v-model:currentPage="currentPage"
          v-model:itemsPerPage="itemsPerPage"
          :totalPages="totalPages"
          :totalItems="totalItems"
          label="productos"
        />
      </div>
    </div>
  </div>
    <!-- Fin Contenedor Tour -->

    <!-- Sistema de Notificaciones Toast -->
    <div class="fixed top-4 right-4 z-[9999] space-y-2" v-if="notifications.length > 0">
      <div v-for="notification in notifications" 
           :key="notification.id"
           class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg border-l-4 p-4 max-w-sm transform transition-all duration-300 ease-in-out"
           :class="{
             'border-green-500': notification.type === 'success',
             'border-red-500': notification.type === 'error',
             'border-yellow-500': notification.type === 'warning',
             'border-blue-500': notification.type === 'info'
           }">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg v-if="notification.type === 'success'" class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <svg v-if="notification.type === 'error'" class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <svg v-if="notification.type === 'warning'" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <svg v-if="notification.type === 'info'" class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="ml-3 flex-1">
            <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
            <p class="text-sm text-gray-500" v-if="notification.message">{{ notification.message }}</p>
          </div>
          <button @click="removeNotification(notification.id)" 
                  class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmación para Cambio de Estado -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showStatusConfirmModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
          <!-- Overlay -->
          <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showStatusConfirmModal = false"></div>
          
          <!-- Modal -->
          <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 max-w-md w-full overflow-hidden border border-gray-200 dark:border-zinc-800">
            <!-- Icon + Title -->
            <div class="px-6 pt-6 pb-4">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     :class="pendingStatusChange?.newStatus 
                       ? 'bg-emerald-100 dark:bg-emerald-950' 
                       : 'bg-amber-100 dark:bg-amber-950'">
                  <svg v-if="pendingStatusChange?.newStatus" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <svg v-else class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ pendingStatusChange?.newStatus ? 'Habilitar Producto' : 'Deshabilitar Producto' }}
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
                    Esta acción modificará el estado del producto
                  </p>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="px-6 pb-6">
              <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50">
                <p class="font-semibold text-gray-900 dark:text-white">{{ pendingStatusChange?.product?.name }}</p>
                <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">SKU: {{ pendingStatusChange?.product?.sku || 'N/A' }}</p>
              </div>
              <p class="text-sm text-gray-600 dark:text-zinc-400 mt-4">
                {{ pendingStatusChange?.newStatus 
                  ? 'El producto estará disponible para la venta.' 
                  : 'El producto no estará disponible para la venta.' }}
              </p>
            </div>

            <!-- Actions -->
            <div class="bg-gray-50 dark:bg-zinc-900 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-zinc-800">
              <button @click="showStatusConfirmModal = false" 
                      class="px-5 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm transition-all duration-200">
                Cancelar
              </button>
              <button @click="confirmStatusChange" 
                      :class="pendingStatusChange?.newStatus 
                        ? 'bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-700' 
                        : 'bg-amber-600 hover:bg-amber-700 dark:bg-amber-600 dark:hover:bg-amber-700'"
                      class="px-6 py-2.5 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-200">
                Confirmar
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Modal de Categoría Inactiva -->
    <div v-if="showCategoryInactiveModal" 
         class="fixed inset-0 bg-gray-900/50 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         @click.self="showCategoryInactiveModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-xl w-full max-w-md shadow-2xl overflow-hidden animate-fade-in">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4">
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-white">Categoría Inactiva</h3>
              <p class="text-sm text-white/80">La categoría debe estar activa primero</p>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="p-6">
          <p class="text-slate-700 dark:text-zinc-300 mb-4">
            No puedes activar el producto <span class="font-bold">{{ pendingStatusChange?.product?.name }}</span> porque su categoría está inactiva.
          </p>
          <div class="bg-blue-50 dark:bg-blue-950 rounded-lg p-4 border border-blue-200 dark:border-blue-800 mb-6">
            <div class="flex items-center space-x-2 mb-2">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
              <p class="font-bold text-blue-900 dark:text-blue-300">{{ pendingStatusChange?.category?.name }}</p>
            </div>
            <p class="text-sm text-blue-700 dark:text-blue-400">Estado: Inactiva</p>
          </div>
          <div class="bg-green-50 dark:bg-green-950 rounded-lg p-4 border border-green-200 dark:border-green-800">
            <p class="text-sm text-green-800 dark:text-green-300">
              <strong>Solución:</strong> Al activar la categoría, este producto se reactivará automáticamente junto con todos los demás productos que fueron desactivados con ella.
            </p>
          </div>
        </div>

        <!-- Actions -->
        <div class="bg-slate-50 dark:bg-zinc-800 px-6 py-4 flex items-center justify-end space-x-3">
          <button @click="showCategoryInactiveModal = false" 
                  class="px-4 py-2 bg-white dark:bg-zinc-700 border border-slate-300 dark:border-zinc-600 text-slate-700 dark:text-zinc-200 rounded-lg hover:bg-slate-50 dark:hover:bg-zinc-600 transition-colors font-medium">
            Cancelar
          </button>
          <button @click="activateCategoryAndProduct" 
                  class="px-4 py-2 bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white rounded-lg transition-colors font-medium">
            Activar Categoría
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Crear/Editar Producto -->
    <Teleport to="body">
      <div v-if="showProductModal" 
           class="fixed inset-0 bg-gray-900/50 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center z-50"
           style="z-index: 99999"
           @click.self="showProductModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-7xl shadow-2xl max-h-[95vh] overflow-hidden border border-gray-300 dark:border-zinc-800 mx-4 flex flex-col">
        
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-8 py-5 flex-shrink-0">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800 rounded-xl flex items-center justify-center border border-gray-200 dark:border-zinc-700">
                <svg class="w-6 h-6 text-gray-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                  {{ isEditing ? 'Editar Producto' : 'Nuevo Producto' }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                  {{ isEditing ? 'Modifica la información del producto' : 'Agrega un nuevo producto al inventario' }}
                </p>
              </div>
            </div>
            <button @click="showProductModal = false" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all">
              <svg class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="flex flex-1 overflow-hidden">
          <!-- Formulario Principal -->
          <div class="flex-1 overflow-y-auto" :class="isFashionMode ? 'bg-white dark:bg-zinc-900 p-8' : 'bg-gray-50 dark:bg-zinc-950 p-8'">
            
            <!-- 👗 Formulario Fashion (integrado sin header ni footer propio) -->
            <div v-if="isFashionMode">
              <FashionProductForm 
                ref="fashionFormRef"
                :categories="categories"
                :suppliers="suppliers"
                :editing-product="selectedProduct"
                @save="handleFashionSave"
                @cancel="showProductModal = false"
                @create-category="showCategoryModal = true"
                @create-supplier="showSupplierModal = true"
              />
            </div>

            <form v-else @submit.prevent="saveProduct" class="space-y-6">
              
              <!-- Información Básica -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-5 shadow-sm border border-gray-300 dark:border-zinc-800">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center">
                  <div class="w-7 h-7 bg-gray-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center mr-2.5 border border-gray-200 dark:border-zinc-700">
                    <svg class="w-4 h-4 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  Información Básica
                </h4>
                
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-bold text-gray-900 dark:text-white mb-1.5">Nombre del Producto *</label>
                    <input v-model="productForm.name" 
                           type="text" 
                           required
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium"
                           placeholder="Ej: iPhone 13 Pro Max">
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-900 dark:text-white mb-1.5">Categoría *</label>
                    <select v-model="productForm.category_id" 
                            required
                            @change="handleCategoryChange"
                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium transition-all">
                      <option value="">Seleccionar categoría</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                      <option value="__new__" style="color: #10b981; font-weight: 600;">+ Nueva categoría</option>
                    </select>
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-900 dark:text-white mb-1.5">
                      Proveedor <span class="text-gray-500 dark:text-zinc-500 font-normal text-[10px]">(opcional)</span>
                    </label>
                    <select v-model="productForm.supplier_id" 
                            @change="handleSupplierChange"
                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium transition-all">
                      <option :value="null">Sin proveedor asignado</option>
                      <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                        {{ supplier.name }}
                      </option>
                      <option value="__new__" style="color: #10b981; font-weight: 600;">+ Nuevo proveedor</option>
                    </select>
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-900 dark:text-white mb-1.5">SKU (Código Único)</label>
                    <input v-model="productForm.sku" 
                           type="text" 
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium"
                           placeholder="Ej: IP13-PRO-256">
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-1.5">Código de Barras</label>
                    <div class="relative">
                      <input v-model="productForm.barcode" 
                             type="text" 
                             class="w-full pl-3 pr-10 py-2 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-gray-400 dark:focus:ring-blue-600 focus:border-transparent text-sm transition-all bg-white dark:bg-zinc-800 text-gray-900 dark:text-white"
                             placeholder="Escanear o generar código">
                      <button type="button" 
                              @click="generateBarcode"
                              title="Generar código de barras"
                              class="absolute right-2 top-1/2 transform -translate-y-1/2 p-1.5 text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                  
                  <div class="col-span-2 mt-3">
                    <label class="block text-xs font-bold text-gray-900 dark:text-white mb-1.5">Descripción <span class="text-gray-500 dark:text-zinc-500 font-normal text-[10px]">(opcional)</span></label>
                    <textarea v-model="productForm.description" 
                              rows="2"
                              class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all resize-none bg-white dark:bg-zinc-800 text-gray-900 dark:text-white"
                              placeholder="Descripción breve del producto">
                    </textarea>
                  </div>
                </div>
              </div>

              <!-- PASO 1: UNIDAD DE MEDIDA (LO PRIMERO) -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-sm border border-gray-300 dark:border-zinc-800">
                <div class="flex items-center justify-between mb-5">
                  <h4 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                    <div class="w-10 h-10 bg-gray-50 dark:bg-zinc-800 rounded-xl flex items-center justify-center mr-3 border border-gray-200 dark:border-zinc-700">
                      <svg class="w-6 h-6 text-gray-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                      </svg>
                    </div>
                    PASO 1: ¿Cómo se vende este producto?
                  </h4>
                  <span class="px-3 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-bold rounded-full">Requerido</span>
                </div>
                
                <select
                  v-model="productForm.measurement_unit"
                  @change="updateAllowDecimal"
                  class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 rounded-lg focus:ring-2 focus:ring-gray-400 dark:focus:ring-blue-600 focus:border-gray-400 dark:focus:border-blue-600 text-sm font-medium transition-all text-gray-900 dark:text-white"
                >
                  <option value="unit">Unidades (und) - ej: celular, TV</option>
                  <option value="kg">Kilogramos (kg) - ej: carne, papas</option>
                  <option value="g">Gramos (g) - ej: especias, café</option>
                  <option value="m">Metros (m) - ej: tela, cable</option>
                  <option value="cm">Centímetros (cm) - ej: cinta, hilo</option>
                  <option value="l">Litros (L) - ej: gasolina, leche</option>
                  <option value="ml">Mililitros (ml) - ej: perfume, jarabe</option>
                </select>
                
                <div class="mt-4 p-4 bg-gray-50 dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <svg class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                      </svg>
                      <span class="text-sm font-bold text-gray-900 dark:text-white">Cantidades Decimales</span>
                      <span class="px-2 py-0.5 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-xs font-semibold rounded-full">Auto</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        v-model="productForm.allow_decimal"
                        class="sr-only peer"
                      />
                      <div class="w-11 h-6 bg-gray-300 dark:bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300/30 dark:peer-focus:ring-blue-600/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-zinc-700 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-blue-600 peer-checked:to-indigo-600 shadow-inner"></div>
                      <span class="ml-3 text-sm font-semibold" :class="productForm.allow_decimal ? 'text-slate-900 dark:text-white' : 'text-gray-600 dark:text-zinc-400'">
                        {{ productForm.allow_decimal ? 'Activado' : 'Desactivado' }}
                      </span>
                    </label>
                  </div>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 mt-3 flex items-start">
                    <svg class="w-3.5 h-3.5 mr-1.5 mt-0.5 flex-shrink-0" :class="productForm.allow_decimal ? 'text-slate-900 dark:text-white' : 'text-gray-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ productForm.allow_decimal ? 'Permite cantidades como 0.5, 1.25, 2.75, etc.' : 'Solo números enteros: 1, 2, 3, 4...' }}
                  </p>
                </div>
              </div>

              <!-- PASO 2: PRECIOS (DINÁMICO CON UNIDAD) -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-sm border border-gray-300 dark:border-zinc-800">
                <h4 class="text-base font-bold text-gray-900 dark:text-white mb-5 flex items-center">
                  <div class="w-10 h-10 bg-gray-50 dark:bg-zinc-800 rounded-xl flex items-center justify-center mr-3 border border-gray-200 dark:border-zinc-700">
                    <svg class="w-6 h-6 text-gray-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                  </div>
                  PASO 2: Precios por {{ getUnitAbbreviation(productForm.measurement_unit) }}
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                  <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                      Precio de Costo *
                      <span class="text-gray-500 dark:text-zinc-400 font-normal ml-1">(por {{ getUnitAbbreviation(productForm.measurement_unit) }})</span>
                    </label>
                    <div class="relative">
                      <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-zinc-400 text-sm font-bold">$</span>
                      <input v-model="productForm.cost" 
                             type="number" 
                             step="0.01"
                             min="0"
                             required
                             class="w-full pl-9 pr-16 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-sky-500 dark:focus:ring-blue-600 focus:border-transparent text-sm transition-all bg-white dark:bg-zinc-800 text-gray-900 dark:text-white"
                             placeholder="0.00">
                      <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-sky-600 dark:text-blue-400 text-xs font-bold bg-sky-50 dark:bg-blue-950 px-2 py-1 rounded">
                        / {{ getUnitAbbreviation(productForm.measurement_unit) }}
                      </span>
                    </div>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                      Precio de Venta *
                      <span class="text-gray-500 dark:text-zinc-400 font-normal ml-1">(por {{ getUnitAbbreviation(productForm.measurement_unit) }})</span>
                    </label>
                    <div class="relative">
                      <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-zinc-400 text-sm font-bold">$</span>
                      <input v-model="productForm.price" 
                             type="number" 
                             step="0.01"
                             min="0"
                             required
                             class="w-full pl-9 pr-16 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-sky-500 dark:focus:ring-blue-600 focus:border-transparent text-sm transition-all bg-white dark:bg-zinc-800 text-gray-900 dark:text-white"
                             placeholder="0.00">
                      <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-sky-600 dark:text-blue-400 text-xs font-bold bg-sky-50 dark:bg-blue-950 px-2 py-1 rounded">
                        / {{ getUnitAbbreviation(productForm.measurement_unit) }}
                      </span>
                    </div>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">Margen de Ganancia</label>
                    <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm font-bold text-gray-700 dark:text-zinc-300 flex items-center justify-center">
                      {{ productForm.price && productForm.cost ? 
                        (((productForm.price - productForm.cost) / productForm.cost) * 100).toFixed(1) + '%' : 
                        '0%' }}
                    </div>
                  </div>
                </div>

                <!-- Información Visual de Precio con Unidad -->
                <div v-if="productForm.price" class="mt-5 p-5 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                      <div>
                        <p class="text-xs font-semibold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Vista Previa del POS</p>
                        <p class="text-xs text-gray-600 dark:text-zinc-400">Así se mostrará al vender</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="text-2xl font-black text-gray-900 dark:text-white">
                        ${{ productForm.price.toLocaleString() }}
                      </p>
                      <p class="text-sm font-bold text-gray-600 dark:text-zinc-400">
                        por {{ getUnitAbbreviation(productForm.measurement_unit) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- PASO 3: INVENTARIO (DINÁMICO CON UNIDAD) -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-sm border border-gray-300 dark:border-zinc-800">
                <h4 class="text-base font-bold text-gray-900 dark:text-white mb-5 flex items-center">
                  <div class="w-10 h-10 bg-gray-50 dark:bg-zinc-800 rounded-xl flex items-center justify-center mr-3 border border-gray-200 dark:border-zinc-700">
                    <svg class="w-6 h-6 text-gray-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                  </div>
                  PASO 3: ¿Cuánto tienes en inventario?
                  <span class="ml-3 text-sm font-normal text-gray-500 dark:text-zinc-400">(en {{ getUnitAbbreviation(productForm.measurement_unit) }})</span>
                </h4>

                <div class="grid grid-cols-1 gap-5">
                  <!-- Stock Inicial (Solo si hay 1 bodega disponible según plan) -->
                  <div v-if="availableWarehouses.length === 1" class="grid grid-cols-3 gap-5">
                    <div>
                      <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                        Stock Inicial
                        <span class="text-gray-500 dark:text-zinc-400 font-normal ml-1">(en {{ getUnitAbbreviation(productForm.measurement_unit) }})</span>
                      </label>
                      <div class="relative">
                        <input v-model="productForm.stock" 
                               type="number" 
                               :step="productForm.allow_decimal ? '0.01' : '1'"
                               min="0"
                               class="w-full px-4 pr-16 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-gray-400 dark:focus:ring-blue-600 focus:border-transparent text-sm transition-all bg-white dark:bg-zinc-800 text-gray-900 dark:text-white"
                               :placeholder="productForm.allow_decimal ? '0.00' : '0'">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-zinc-400 text-xs font-medium bg-gray-100 dark:bg-zinc-700 px-2 py-1 rounded">
                          {{ getUnitAbbreviation(productForm.measurement_unit) }}
                        </span>
                      </div>
                      <p class="text-xs text-gray-500 dark:text-zinc-400 mt-2">Cantidad actual que tienes</p>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                        Stock Mínimo
                        <span class="text-gray-500 dark:text-zinc-400 font-normal ml-1">(en {{ getUnitAbbreviation(productForm.measurement_unit) }})</span>
                      </label>
                      <div class="relative">
                        <input v-model="productForm.min_stock" 
                               type="number" 
                               :step="productForm.allow_decimal ? '0.01' : '1'"
                               min="0"
                               class="w-full px-4 pr-16 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-gray-400 dark:focus:ring-blue-600 focus:border-transparent text-sm transition-all bg-white dark:bg-zinc-800 text-gray-900 dark:text-white"
                               :placeholder="productForm.allow_decimal ? '5.00' : '5'">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-zinc-400 text-xs font-medium bg-gray-100 dark:bg-zinc-700 px-2 py-1 rounded">
                          {{ getUnitAbbreviation(productForm.measurement_unit) }}
                        </span>
                      </div>
                      <p class="text-xs text-gray-600 dark:text-zinc-400 mt-2">Alerta cuando baje de este valor</p>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                        Stock Máximo
                        <span class="text-gray-500 dark:text-zinc-400 font-normal ml-1">(en {{ getUnitAbbreviation(productForm.measurement_unit) }})</span>
                      </label>
                      <div class="relative">
                        <input v-model="productForm.max_stock" 
                               type="number" 
                               :step="productForm.allow_decimal ? '0.01' : '1'"
                               min="0"
                               class="w-full px-4 pr-16 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-gray-400 dark:focus:ring-blue-600 focus:border-transparent text-sm transition-all bg-white dark:bg-zinc-800 text-gray-900 dark:text-white"
                               :placeholder="productForm.allow_decimal ? '100.00' : '100'">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-zinc-400 text-xs font-medium bg-gray-100 dark:bg-zinc-700 px-2 py-1 rounded">
                          {{ getUnitAbbreviation(productForm.measurement_unit) }}
                        </span>
                      </div>
                      <p class="text-xs text-gray-600 dark:text-zinc-400 mt-2">Capacidad máxima de almacenamiento</p>
                    </div>
                  </div>
                  
                  <!-- Stock por Tienda (Solo si hay 2+ bodegas disponibles según el plan) -->
                  <div v-if="availableWarehouses.length >= 2" class="col-span-full">
                    <div class="flex items-center justify-between mb-4">
                      <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Stock por Tienda *
                        <span class="ml-2 text-sm font-normal text-gray-500 dark:text-zinc-400">(en {{ getUnitAbbreviation(productForm.measurement_unit) }})</span>
                      </label>
                      <button type="button"
                              @click="showStockHelp = !showStockHelp"
                              class="px-3 py-1.5 bg-blue-100 dark:bg-blue-950 hover:bg-blue-200 dark:hover:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-semibold transition-all">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ showStockHelp ? 'Ocultar' : 'Ayuda' }}
                      </button>
                    </div>
                    
                    <!-- Ayuda -->
                    <div v-if="showStockHelp" class="mb-4 p-4 bg-gradient-to-r from-blue-50 dark:from-blue-950/50 to-indigo-50 dark:to-indigo-950/50 border-2 border-blue-200 dark:border-blue-800 rounded-xl">
                      <p class="font-bold mb-2 text-blue-900 dark:text-blue-300 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        ¿Cómo funciona el stock multi-tienda?
                      </p>
                      <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-300">
                        <li class="flex items-start">
                          <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                          Asigna <strong>diferente stock a cada sede</strong> según tu inventario real
                        </li>
                        <li class="flex items-start">
                          <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                          Ejemplo: <strong>20 {{ getUnitAbbreviation(productForm.measurement_unit) }}</strong> en Sede Principal, <strong>5 {{ getUnitAbbreviation(productForm.measurement_unit) }}</strong> en Sucursal Norte
                        </li>
                        <li class="flex items-start">
                          <svg class="w-5 h-5 mr-2 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                          </svg>
                          Si dejas <strong>0</strong>, el producto NO estará disponible en esa tienda
                        </li>
                      </ul>
                    </div>
                    
                    <!-- Sedes con checkbox + input condicional -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                      <div v-for="warehouse in availableWarehouses" :key="warehouse.id"
                           class="p-4 bg-white dark:bg-zinc-800 rounded-xl border-2 transition-all"
                           :class="productForm.warehouseEnabled[warehouse.id] ? 'border-blue-400 dark:border-blue-600 shadow-sm' : 'border-gray-200 dark:border-zinc-700'">
                        
                        <!-- Checkbox para habilitar/deshabilitar sede -->
                        <div class="flex items-center gap-3 mb-3">
                          <input 
                            :id="`warehouse-check-${warehouse.id}`"
                            v-model="productForm.warehouseEnabled[warehouse.id]"
                            type="checkbox"
                            class="w-5 h-5 text-blue-600 bg-gray-100 dark:bg-zinc-700 border-gray-300 dark:border-zinc-600 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                          />
                          <label 
                            :for="`warehouse-check-${warehouse.id}`"
                            class="flex items-center gap-2 flex-1 cursor-pointer"
                          >
                            <div class="w-8 h-8 bg-gray-50 dark:bg-zinc-700 rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-zinc-600">
                              <svg class="w-4 h-4 text-gray-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                              </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                              <span class="text-sm font-bold text-gray-900 dark:text-white block truncate">
                                {{ warehouse.name }}
                              </span>
                              <div class="flex items-center gap-2 mt-0.5">
                                <span v-if="warehouse.is_default" class="text-xs font-semibold text-gray-700 dark:text-zinc-300 bg-gray-100 dark:bg-zinc-700 px-2 py-0.5 rounded-full border border-gray-200 dark:border-zinc-600 inline-block">
                                  Principal
                                </span>
                              </div>
                              <span v-if="!productForm.warehouseEnabled[warehouse.id]" class="text-xs text-gray-400 dark:text-zinc-500 block mt-0.5">
                                (No disponible)
                              </span>
                            </div>
                          </label>
                        </div>
                        
                        <!-- Input de stock (solo si la sede está habilitada) -->
                        <div v-if="productForm.warehouseEnabled[warehouse.id]" class="space-y-2">
                          <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400">
                            Stock {{ isEditing ? 'actual' : 'inicial' }}:
                          </label>
                          <div class="relative">
                            <input 
                              v-model.number="productForm.warehouseStock[warehouse.id]"
                              type="number" 
                              :step="productForm.allow_decimal ? '0.01' : '1'"
                              min="0"
                              :placeholder="productForm.allow_decimal ? '0.00' : '0'"
                              class="w-full px-4 pr-14 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-600 text-sm font-bold text-center bg-white dark:bg-zinc-800 text-gray-900 dark:text-white transition-all"
                            />
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-zinc-400 text-xs font-medium bg-gray-100 dark:bg-zinc-700 px-2 py-1 rounded">
                              {{ getUnitAbbreviation(productForm.measurement_unit) }}
                            </span>
                          </div>
                        </div>
                        
                        <!-- Mensaje cuando no está habilitada -->
                        <div v-else class="p-3 bg-gray-50 dark:bg-zinc-900/50 rounded-xl border border-dashed border-gray-300 dark:border-zinc-700">
                          <p class="text-xs text-gray-500 dark:text-zinc-500 text-center font-medium">
                            Producto no disponible en esta sede
                          </p>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Stock Total -->
                    <div class="mt-4 p-5 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl">
                      <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                          <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-700 rounded-xl flex items-center justify-center border border-gray-200 dark:border-zinc-600">
                            <svg class="w-7 h-7 text-gray-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                          </div>
                          <div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white block">Stock Total del Producto</span>
                            <span class="text-xs text-gray-600 dark:text-zinc-400 font-medium">(suma de todas las sedes)</span>
                          </div>
                        </div>
                        <div class="text-right">
                          <span class="text-4xl font-black text-gray-900 dark:text-white block">
                            {{ calculateTotalStock() }}
                          </span>
                          <span class="text-sm font-bold text-gray-600 dark:text-zinc-400">
                            {{ getUnitAbbreviation(productForm.measurement_unit) }} totales
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Estado Activo -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-sm border border-gray-300 dark:border-zinc-800">
                <div class="flex items-center space-x-4">
                  <div class="flex-shrink-0">
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input v-model="productForm.active" 
                             type="checkbox" 
                             class="sr-only peer">
                      <div class="w-14 h-7 bg-gray-300 dark:bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300/30 dark:peer-focus:ring-blue-600/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-zinc-700 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-blue-600 peer-checked:to-indigo-600 shadow-inner"></div>
                    </label>
                  </div>
                  <div class="flex-1">
                    <label class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                      <svg class="w-5 h-5 mr-2" :class="productForm.active ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-zinc-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Producto {{ productForm.active ? 'Activo' : 'Inactivo' }}
                    </label>
                    <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
                      {{ productForm.active ? 'El producto está disponible para la venta' : 'El producto NO aparecerá en el sistema de ventas' }}
                    </p>
                  </div>
                </div>
              </div>
            </form>
          </div>
          
          <!-- Sidebar para Imagen -->
          <div v-if="!isFashionMode" class="w-80 bg-gray-50 dark:bg-zinc-900 border-l border-gray-200 dark:border-zinc-800 p-6">
            <div class="mb-5">
              <div class="flex items-center space-x-3 mb-2">
                <div class="w-9 h-9 bg-gray-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center border border-gray-200 dark:border-zinc-700">
                  <svg class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
                <div>
                  <h4 class="text-lg font-bold text-gray-900 dark:text-white">Imagen del Producto</h4>
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Añade una foto atractiva</p>
                </div>
              </div>
            </div>
            
            <!-- Selector de método -->
            <div class="flex bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-1 mb-4 border border-gray-200 dark:border-zinc-700">
              <button type="button"
                      @click="imageUploadMethod = 'url'"
                      :class="[
                        'flex-1 px-3 py-2 rounded-md text-xs font-semibold transition-all flex items-center justify-center space-x-1.5',
                        imageUploadMethod === 'url' 
                          ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-md' 
                          : 'text-gray-600 dark:text-zinc-400 hover:bg-white dark:hover:bg-zinc-700'
                      ]">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                URL Web
              </button>
              <button type="button"
                      @click="imageUploadMethod = 'file'"
                      :class="[
                        'flex-1 px-3 py-2 rounded-md text-xs font-semibold transition-all flex items-center justify-center space-x-1.5',
                        imageUploadMethod === 'file' 
                          ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-md' 
                          : 'text-gray-600 dark:text-zinc-400 hover:bg-white dark:hover:bg-zinc-700'
                      ]">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Subir Archivo
              </button>
            </div>
            
            <!-- Subida de archivo -->
            <div v-if="imageUploadMethod === 'file'" class="space-y-3">
              <div @click="$refs.fileInput?.click()" 
                   class="border-2 border-dashed border-gray-300 dark:border-zinc-700 rounded-lg p-5 text-center cursor-pointer hover:border-gray-400 dark:hover:border-zinc-600 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all">
                
                <input type="file" 
                       ref="fileInput"
                       @change="handleFileUpload"
                       accept="image/*"
                       class="hidden">
                
                <div v-if="!previewImage">
                  <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-2 border border-gray-200 dark:border-zinc-700">
                    <svg class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <h5 class="font-semibold text-gray-900 dark:text-white mb-1 text-xs">Subir Imagen</h5>
                  <p class="text-gray-600 dark:text-zinc-400 text-xs mb-0.5">
                    <span class="font-semibold text-gray-900 dark:text-white">Haz clic aquí</span> o arrastra una imagen
                  </p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500">PNG, JPG hasta 5MB</p>
                </div>
                
                <!-- Preview de imagen -->
                <div v-else class="relative">
                  <img :src="previewImage" class="max-h-28 mx-auto rounded-lg shadow-sm">
                  <button type="button" 
                          @click.stop="clearImageUpload"
                          class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm hover:bg-red-600 transition-colors shadow-sm">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
            
            <!-- URL web -->
            <div v-if="imageUploadMethod === 'url'" class="space-y-3">
              <input v-model="productForm.image" 
                     type="url" 
                     class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-gray-400 dark:focus:ring-blue-600 focus:border-transparent text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white"
                     placeholder="https://ejemplo.com/imagen.jpg">
              
              <!-- Preview de URL -->
              <div v-if="productForm.image && isValidUrl(productForm.image)" class="text-center">
                <div class="inline-block relative">
                  <img :src="getProductImage(productForm)" 
                       @error="(e) => { handleImageError(e); imageLoadError = true; }"
                       @load="() => imageLoadError = false"
                       class="max-h-24 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm"
                       alt="Preview">
                  <div v-if="imageLoadError" class="absolute inset-0 bg-red-50 dark:bg-red-950 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                      <svg class="w-4 h-4 text-red-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                      </svg>
                      <p class="text-xs text-red-600 dark:text-red-400 font-medium">Error</p>
                    </div>
                  </div>
                </div>
                <p v-if="imageLoadError" class="text-xs text-red-500 dark:text-red-400 mt-1">No se pudo cargar la imagen</p>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-6 space-y-2.5">
              <button type="button" 
                      v-if="isFashionMode"
                      @click="saveFashionProduct"
                      :disabled="loading"
                      class="w-full px-5 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white rounded-lg font-bold shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                {{ loading ? 'Guardando...' : 'Crear Producto' }}
              </button>
              
              <button v-else type="button" 
                      @click="saveProduct"
                      :disabled="loading"
                      class="w-full px-5 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white rounded-lg font-bold shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                {{ loading ? 'Guardando...' : (isEditing ? 'Actualizar Producto' : 'Crear Producto') }}
              </button>
              
              <button type="button" 
                      @click="showProductModal = false"
                      class="w-full px-5 py-3 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-lg font-semibold transition-colors border border-gray-300 dark:border-zinc-700">
                Cancelar
              </button>
            </div>
          </div>
        </div>
        
        <!-- Footer con botones (solo para fashion mode) -->
        <div v-if="isFashionMode" class="flex-shrink-0 bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-8 py-4 flex items-center justify-end gap-3">
          <button type="button" 
                  @click="showProductModal = false"
                  class="px-6 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-lg font-semibold transition-colors border border-gray-300 dark:border-zinc-700">
            Cancelar
          </button>
          
          <button type="button" 
                  @click="saveFashionProduct"
                  :disabled="loading"
                  class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white rounded-lg font-bold shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
            {{ loading ? 'Guardando...' : (isEditing ? 'Actualizar Producto' : 'Crear Producto') }}
          </button>
        </div>
      </div>
      </div>
    </Teleport>

    <!-- ⚠️ Modal de Confirmación: Producto sin Stock -->
    <div v-if="showStockWarningModal" 
         class="fixed inset-0 bg-gray-900/70 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-[60]"
         @click.self="showStockWarningModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-lg shadow-2xl transform transition-all border dark:border-zinc-800">
        
        <!-- Header con icono de advertencia -->
        <div class="bg-gradient-to-r from-amber-50 dark:from-amber-950/50 to-orange-50 dark:to-orange-950/50 border-b border-amber-200 dark:border-amber-800 px-6 py-5">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-500 dark:bg-amber-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-xl font-bold text-gray-900 dark:text-white">Datos Incompletos</h3>
              <p class="text-sm text-gray-600 dark:text-zinc-400 mt-0.5">Aún faltan datos importantes por llenar</p>
            </div>
          </div>
        </div>
        
        <!-- Contenido -->
        <div class="p-6">
          <p class="text-sm text-gray-700 dark:text-zinc-300 mb-4">
            Detectamos que algunos campos importantes no han sido completados:
          </p>
          
          <!-- Lista de campos faltantes -->
          <div class="space-y-3 mb-5">
            <div v-for="(item, index) in missingFields" :key="index"
                 class="flex items-start space-x-3 p-3 rounded-lg"
                 :class="{
                   'bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800': item.severity === 'high',
                   'bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800': item.severity === 'medium',
                   'bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700': item.severity === 'low'
                 }">
              <div class="flex-shrink-0 mt-0.5">
                <svg v-if="item.severity === 'high'" class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg v-else-if="item.severity === 'medium'" class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <svg v-else class="w-5 h-5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold"
                   :class="{
                     'text-red-900 dark:text-red-300': item.severity === 'high',
                     'text-amber-900 dark:text-amber-300': item.severity === 'medium',
                     'text-gray-700 dark:text-zinc-300': item.severity === 'low'
                   }">
                  {{ item.field }}
                </p>
                <p class="text-xs mt-0.5"
                   :class="{
                     'text-red-700 dark:text-red-400': item.severity === 'high',
                     'text-amber-700 dark:text-amber-400': item.severity === 'medium',
                     'text-gray-600 dark:text-zinc-400': item.severity === 'low'
                   }">
                  {{ item.message }}
                </p>
              </div>
            </div>
          </div>
          
          <div class="bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-5">
            <div class="flex items-start space-x-3">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
              </svg>
              <div>
                <p class="text-sm font-semibold text-blue-900 dark:text-blue-300">Recomendación</p>
                <p class="text-xs text-blue-700 dark:text-blue-400 mt-1">
                  Te sugerimos completar estos datos ahora para tener un control preciso de tu inventario desde el inicio.
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Botones de acción -->
        <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between space-x-3">
          <button type="button" 
                  @click="showStockWarningModal = false"
                  class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-lg font-semibold transition-all border border-gray-300 dark:border-zinc-700 shadow-sm">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            Volver a Editar
          </button>
          
          <button type="button" 
                  @click="() => { showStockWarningModal = false; saveProduct(true) }"
                  class="flex-1 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-lg font-bold transition-all shadow-md">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Continuar Así
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Ver Producto AVANZADO (Fashion/Variantes) -->
    <Teleport to="body">
      <div v-if="showViewModal" 
           class="fixed inset-0 bg-gray-900/50 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50"
           style="z-index: 99999"
           @click.self="showViewModal = false">
      <!-- Modal Full Width para productos FASHION (con o sin variantes) -->
      <div v-if="selectedProduct && isFashionProduct(selectedProduct)" 
           class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-5xl shadow-2xl max-h-[90vh] overflow-hidden border border-gray-200 dark:border-zinc-800 flex flex-col">
        
        <!-- Header -->
        <div class="bg-gray-50 dark:bg-zinc-900 px-6 py-4 border-b border-gray-200 dark:border-zinc-800 flex-shrink-0">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="w-11 h-11 bg-purple-100 dark:bg-purple-950/80 rounded-xl flex items-center justify-center ring-1 ring-purple-200 dark:ring-purple-800/50">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
              </div>
              <div>
                <div class="flex items-center gap-3">
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ selectedProduct.name }}</h3>
                  <span class="px-2.5 py-1 bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800/50 rounded-lg text-[10px] font-bold uppercase tracking-wide">
                    Moda/Variantes
                  </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">Gestión de Inventario por Variante</p>
              </div>
            </div>
            <button @click="showViewModal = false" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
              <svg class="w-5 h-5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1 bg-gray-50 dark:bg-zinc-900/50">
          <!-- SECCIÓN SUPERIOR: Galería + Resumen Global -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Galería de Imágenes -->
            <div class="lg:col-span-1">
              <div class="bg-white dark:bg-zinc-900/60 backdrop-blur-sm rounded-2xl p-3 border border-gray-200 dark:border-zinc-800 shadow-sm">
                <!-- Imagen Principal -->
                <div class="relative aspect-square rounded-xl overflow-hidden mb-3 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-zinc-800 dark:to-zinc-900">
                  <img :src="selectedProductMainImage" 
                       @error="handleImageError" 
                       :alt="selectedProduct.name" 
                       class="w-full h-full object-contain">
                </div>
                <!-- Thumbnails Reales -->
                <div v-if="selectedProductImages.length > 1" class="grid grid-cols-4 gap-2">
                  <button v-for="(img, index) in selectedProductImages" 
                          :key="index"
                          @click="selectedImageIndex = index"
                          :class="[
                            'aspect-square rounded-lg overflow-hidden border-2 transition-all duration-200',
                            selectedImageIndex === index 
                              ? 'border-purple-500 ring-2 ring-purple-500/30' 
                              : 'border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 opacity-70 hover:opacity-100'
                          ]">
                    <img :src="img" class="w-full h-full object-cover">
                  </button>
                </div>
                <div v-else class="grid grid-cols-4 gap-2">
                  <div v-for="i in 4" :key="i" 
                       class="aspect-square bg-gray-100 dark:bg-zinc-800/50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Resumen Global (Estadísticas) -->
            <div class="lg:col-span-2 flex flex-col">
              <div class="flex-1 bg-white dark:bg-zinc-900/60 backdrop-blur-sm rounded-2xl p-5 border border-gray-200 dark:border-zinc-800 shadow-sm">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                  </svg>
                  Estadísticas Globales
                </h4>
                
                <div class="grid grid-cols-3 gap-3">
                  <!-- Stock Total Global -->
                  <div class="bg-gray-50 dark:bg-zinc-800/80 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50 text-center hover:border-emerald-300 dark:hover:border-emerald-800/50 transition-colors">
                    <div class="w-9 h-9 bg-emerald-100 dark:bg-emerald-950/80 rounded-lg flex items-center justify-center mx-auto mb-2.5">
                      <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                      </svg>
                    </div>
                    <p class="text-[9px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Stock Total</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatNumber(getStockSummary(selectedProduct).total) }}</p>
                    <p class="text-[10px] text-gray-500 dark:text-zinc-500 mt-0.5">unidades</p>
                  </div>
                  
                  <!-- Valor Total Inventario -->
                  <div class="bg-gray-50 dark:bg-zinc-800/80 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50 text-center hover:border-blue-300 dark:hover:border-blue-800/50 transition-colors">
                    <div class="w-9 h-9 bg-blue-100 dark:bg-blue-950/80 rounded-lg flex items-center justify-center mx-auto mb-2.5">
                      <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                      </svg>
                    </div>
                    <p class="text-[9px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Valor Total</p>
                    <p class="text-xl font-black text-gray-900 dark:text-white tabular-nums">${{ formatCurrency(getTotalInventoryValue(selectedProduct)) }}</p>
                    <p class="text-[10px] text-gray-500 dark:text-zinc-500 mt-0.5">inventario</p>
                  </div>
                  
                  <!-- Total Variantes -->
                  <div class="bg-gray-50 dark:bg-zinc-800/80 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50 text-center hover:border-purple-300 dark:hover:border-purple-800/50 transition-colors">
                    <div class="w-9 h-9 bg-purple-100 dark:bg-purple-950/80 rounded-lg flex items-center justify-center mx-auto mb-2.5">
                      <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                      </svg>
                    </div>
                    <p class="text-[9px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Variantes</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ getVariantsCount(selectedProduct) }}</p>
                    <p class="text-[10px] text-gray-500 dark:text-zinc-500 mt-0.5">combinaciones</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- TABLA EXCEL-STYLE: Edición en Línea -->
          <div class="bg-white dark:bg-zinc-900/60 backdrop-blur-sm rounded-2xl border border-gray-200 dark:border-zinc-800 overflow-hidden shadow-sm dark:shadow-none">
            <div class="bg-gray-50 dark:bg-zinc-800/50 px-5 py-3 border-b border-gray-200 dark:border-zinc-700/50">
              <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                {{ hasVariants(selectedProduct) ? 'Gestión de Stock por Variante - Edición Rápida' : 'Información del Producto' }}
              </h4>
            </div>
            
            <!-- Tabla para productos CON variantes -->
            <div v-if="hasVariants(selectedProduct)" class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-gray-50 dark:bg-zinc-800/30 border-b border-gray-200 dark:border-zinc-700/50">
                    <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Variante</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-32">SKU</th>
                    <th class="px-4 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-40">Costo Unit.</th>
                    <th class="px-4 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-40">Precio Venta</th>
                    <th class="px-4 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-36">Stock</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/50 bg-white dark:bg-transparent">
                  <tr v-for="variant in selectedProduct.variants" :key="variant.id"
                      :class="[
                        'hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors',
                        variantChanges[variant.id] ? 'bg-amber-50 dark:bg-amber-950/20' : ''
                      ]">
                    <!-- Variante (Combinación) -->
                    <td class="px-5 py-3">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-zinc-800 rounded-lg overflow-hidden flex-shrink-0 ring-1 ring-gray-200 dark:ring-zinc-700">
                          <img :src="getProductImage(selectedProduct)" 
                               class="w-full h-full object-cover">
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                          <span v-if="variant.options_summary">
                            {{ formatVariantOptions(variant.options_summary) }}
                          </span>
                          <span v-else>Variante #{{ variant.id }}</span>
                        </p>
                      </div>
                    </td>
                    
                    <!-- SKU -->
                    <td class="px-4 py-3">
                      <span class="font-mono text-xs text-gray-600 dark:text-zinc-400 bg-gray-100 dark:bg-zinc-800/50 px-2 py-1 rounded">{{ variant.sku }}</span>
                    </td>
                    
                    <!-- Costo Unitario (Input Editable) -->
                    <td class="px-4 py-3">
                      <div class="flex items-center justify-center gap-1">
                        <span class="text-sm text-gray-400 dark:text-zinc-500 font-medium">$</span>
                        <input type="text" 
                               :value="formatInputNumber(variant.editableCost)"
                               @input="handleCostInput($event, variant)"
                               @focus="$event.target.select()"
                               class="w-28 px-3 py-2 text-center text-sm font-semibold bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all tabular-nums"
                               :class="variantChanges[variant.id]?.cost ? 'ring-2 ring-amber-500 border-amber-500' : ''">
                      </div>
                    </td>
                    
                    <!-- Precio Venta (Input Editable) -->
                    <td class="px-4 py-3">
                      <div class="flex items-center justify-center gap-1">
                        <span class="text-sm text-gray-400 dark:text-zinc-500 font-medium">$</span>
                        <input type="text" 
                               :value="formatInputNumber(variant.editablePrice)"
                               @input="handlePriceInput($event, variant)"
                               @focus="$event.target.select()"
                               class="w-28 px-3 py-2 text-center text-sm font-bold bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg text-emerald-600 dark:text-emerald-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all tabular-nums"
                               :class="variantChanges[variant.id]?.price ? 'ring-2 ring-amber-500 border-amber-500' : ''">
                      </div>
                    </td>
                    
                    <!-- Stock (Input Numérico + Botones [-]/[+]) -->
                    <td class="px-4 py-3">
                      <div class="flex items-center justify-center gap-1">
                        <button @click="decrementStock(variant)" 
                                class="w-7 h-7 flex items-center justify-center bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded text-gray-700 dark:text-zinc-300 font-bold text-sm transition-colors">
                          −
                        </button>
                        <input type="number" 
                               v-model.number="variant.editableStock"
                               @input="markVariantChanged(variant.id)"
                               class="w-16 px-2 py-1.5 text-center text-base font-bold bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded tabular-nums transition-all"
                               :class="[
                                 variantChanges[variant.id]?.stock ? 'ring-2 ring-amber-400 dark:ring-amber-500 border-amber-400 dark:border-amber-500' : '',
                                 variant.editableStock <= 5 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white'
                               ]">
                        <button @click="incrementStock(variant)" 
                                class="w-7 h-7 flex items-center justify-center bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded text-gray-700 dark:text-zinc-300 font-bold text-sm transition-colors">
                          +
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <!-- Vista para productos FASHION SIN variantes -->
            <div v-else class="p-5">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50">
                  <p class="text-[10px] text-gray-500 dark:text-zinc-500 mb-1 uppercase tracking-wide font-bold">SKU</p>
                  <p class="text-base font-bold text-gray-900 dark:text-white font-mono">{{ selectedProduct.sku || 'No definido' }}</p>
                </div>
                
                <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50">
                  <p class="text-[10px] text-gray-500 dark:text-zinc-500 mb-1 uppercase tracking-wide font-bold">Precio Venta</p>
                  <p class="text-base font-bold text-emerald-600 dark:text-emerald-400">${{ formatCurrency(selectedProduct.sale_price) }}</p>
                </div>
                
                <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50">
                  <p class="text-[10px] text-gray-500 dark:text-zinc-500 mb-1 uppercase tracking-wide font-bold">Stock Actual</p>
                  <p class="text-base font-bold text-gray-900 dark:text-white">{{ formatNumber(selectedProduct.current_stock || 0) }} unidades</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer con Botón de Guardar (FUERA del scroll) -->
        <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-4">
          <div class="flex items-center justify-between">
            <!-- Indicador de Cambios + Cerrar -->
            <div class="flex items-center gap-3">
              <button @click="showViewModal = false" 
                      class="px-5 py-2.5 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-700 border border-gray-300 dark:border-zinc-700 rounded-xl text-sm font-medium transition-colors">
                Cerrar
              </button>
              <div v-if="hasUnsavedChanges" class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 rounded-lg">
                <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
                <span class="text-xs font-medium text-amber-600 dark:text-amber-400">{{ changesCount }} cambio(s) sin guardar</span>
              </div>
            </div>
            
            <!-- Botón Principal -->
            <button v-if="hasUnsavedChanges"
                    @click="saveInventoryChanges" 
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/30 transition-all inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                <span>Guardar Cambios</span>
            </button>
            <button v-else
                    @click="editProduct(selectedProduct)" 
                    class="px-6 py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-xl text-sm font-bold shadow-lg transition-all inline-flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              <span>Editar Producto Padre</span>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Modal Simple para productos sin variantes (Original) -->
      <div v-else-if="selectedProduct"
           class="bg-white dark:bg-zinc-900 rounded-lg w-full max-w-2xl shadow-xl max-h-[90vh] overflow-hidden border dark:border-zinc-800">
        
        <!-- Header -->
        <div class="bg-gray-50 dark:bg-zinc-900 px-6 py-4 border-b border-gray-200 dark:border-zinc-800">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Detalles del Producto</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Información completa del producto</p>
              </div>
            </div>
            <button @click="showViewModal = false" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
              <svg class="w-5 h-5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <!-- Imagen y detalles principales -->
          <div class="flex flex-col items-center mb-8">
            <div class="relative mb-4">
              <img :src="getProductImage(selectedProduct)" @error="handleImageError" 
                   :alt="selectedProduct.name" 
                   class="w-40 h-40 object-cover rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700">
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ selectedProduct.name }}</h2>
            <div class="flex items-center space-x-2 text-sm">
              <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-xs font-medium">
                {{ selectedProduct.category?.name || 'Sin categoría' }}
              </span>
              <span :class="[
                'px-3 py-1 rounded-full text-xs font-medium',
                getProductStatus(selectedProduct) 
                  ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' 
                  : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
              ]">
                {{ getProductStatus(selectedProduct) ? 'Activo' : 'Inactivo' }}
              </span>
              <span v-if="(selectedProduct.current_stock || 0) <= (selectedProduct.min_stock || 0)" 
                    class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-xs font-medium">
                Stock Bajo
              </span>
            </div>
          </div>
          
          <!-- Detalles en grid -->
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
              <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">SKU</p>
              <p class="text-base font-bold text-gray-900 dark:text-white">{{ selectedProduct.sku || 'Sin SKU' }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
              <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Precio</p>
              <p class="text-base font-bold text-green-600 dark:text-green-400">${{ formatCurrency(selectedProduct.sale_price) }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
              <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Stock Actual</p>
              <p class="text-base font-bold text-gray-900 dark:text-white">{{ selectedProduct.current_stock || 0 }}</p>
              <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-zinc-400">
                <span>Mín: {{ selectedProduct.min_stock || 0 }}</span>
                <span class="mx-2">•</span>
                <span>Máx: {{ selectedProduct.max_stock || 0 }}</span>
              </div>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
              <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Costo</p>
              <p class="text-base font-bold text-gray-900 dark:text-white">${{ formatCurrency(selectedProduct.cost_price) }}</p>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                Margen: {{ selectedProduct.sale_price && selectedProduct.cost_price ? 
                  ((selectedProduct.sale_price - selectedProduct.cost_price) / selectedProduct.cost_price * 100).toFixed(1) + '%' : 
                  '0%' }}
              </p>
            </div>
          </div>
          
          <!-- Sección de descripción -->
          <div v-if="selectedProduct.description" class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4 mb-6">
            <div class="flex items-center mb-2 space-x-2">
              <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
              </svg>
              <h4 class="text-sm font-bold text-gray-900 dark:text-white">Descripción del Producto</h4>
            </div>
            <p class="text-sm text-gray-600 dark:text-zinc-300 leading-relaxed">{{ selectedProduct.description }}</p>
          </div>

          <!-- Pie del modal con acciones -->
          <div class="flex items-center justify-end space-x-3">
            <button @click="editProduct(selectedProduct)" 
                    class="px-4 py-2 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 hover:bg-gray-200 dark:hover:bg-zinc-700 rounded-lg text-sm font-medium inline-flex items-center space-x-2 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              <span>Editar Producto</span>
            </button>
            <button @click="showViewModal = false" 
                    class="px-4 py-2 bg-blue-600 dark:bg-blue-700 text-white hover:bg-blue-700 dark:hover:bg-blue-600 rounded-lg text-sm font-medium transition-colors">
              Cerrar
            </button>
          </div>
        </div>
      </div>
      </div>
    </Teleport>

    <!-- Modal: No hay categorías -->
    <div v-if="showNoCategoriesModal" 
         class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4"
         @click.self="showNoCategoriesModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-300 dark:border-zinc-800 max-w-md w-full p-6 animate-fade-in">
        <div class="text-center">
          <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/20 border-2 border-amber-200 dark:border-amber-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No hay categorías disponibles</h3>
          <p class="text-gray-700 dark:text-zinc-300 mb-6">Antes de crear un producto, debes crear al menos una categoría para organizarlo correctamente.</p>
          
          <div class="flex flex-col space-y-3">
            <button @click="openCategoryModal" 
                    class="w-full px-4 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white font-bold rounded-lg shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-200 flex items-center justify-center space-x-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              <span>Crear Categoría</span>
            </button>
            <button @click="showNoCategoriesModal = false" 
                    class="w-full px-4 py-3 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-medium rounded-lg transition-colors">
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: Crear Categoría -->
    <div v-if="showCategoryModal" 
         class="fixed top-0 left-0 right-0 bottom-0 w-screen h-screen bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
         style="z-index: 100000"
         @click.self="showCategoryModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-300 dark:border-zinc-800 max-w-lg w-full animate-fade-in">
        <!-- Header Simple -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-zinc-800">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gray-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Nueva Categoría</h2>
          </div>
          <button @click="showCategoryModal = false" 
                  class="text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form @submit.prevent="saveCategory" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">Nombre de la Categoría *</label>
            <input v-model="categoryForm.name" 
                   type="text" 
                   required
                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Ej: Electrónica, Ropa, Alimentos">
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">Descripción</label>
            <textarea v-model="categoryForm.description" 
                      rows="2"
                      class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Descripción opcional de la categoría">
            </textarea>
          </div>

          <!-- Selector de Color -->
          <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">Color de la Categoría</label>
            <div class="flex items-center gap-3">
              <input 
                v-model="categoryForm.color" 
                type="color"
                class="w-16 h-10 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 cursor-pointer"
              />
              <input 
                v-model="categoryForm.color" 
                type="text"
                class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="#3b82f6"
              />
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-zinc-800">
            <button type="button" 
                    @click="showCategoryModal = false"
                    class="px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium rounded-lg border border-gray-300 dark:border-zinc-700 transition-colors">
              Cancelar
            </button>
            <button type="submit" 
                    :disabled="!categoryForm.name"
                    class="px-4 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white font-bold rounded-lg shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
              Crear Categoría
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: Crear Proveedor Rápido -->
  <div v-if="showSupplierModal" 
       class="fixed top-0 left-0 right-0 bottom-0 w-screen h-screen bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
       style="z-index: 100000"
       @click.self="showSupplierModal = false">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-300 dark:border-zinc-800 max-w-md w-full animate-fade-in">
      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-zinc-800">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
          <h2 class="text-lg font-bold text-gray-900 dark:text-white">Nuevo Proveedor</h2>
        </div>
        <button @click="showSupplierModal = false" 
                class="text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form @submit.prevent="saveSupplier" class="p-6 space-y-4">
        <div>
          <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Nombre del Proveedor *</label>
          <input v-model="supplierForm.name" 
                 type="text" 
                 required
                 class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                 placeholder="Ej: Distribuidora XYZ">
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Documento (NIT/CC) *</label>
          <input v-model="supplierForm.document" 
                 type="text" 
                 required
                 class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                 placeholder="Ej: 900123456-7">
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Persona de Contacto</label>
          <input v-model="supplierForm.contact_name" 
                 type="text"
                 class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                 placeholder="Ej: Juan Pérez">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Teléfono</label>
            <input v-model="supplierForm.phone" 
                   type="tel"
                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="3001234567">
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Email</label>
            <input v-model="supplierForm.email" 
                   type="email"
                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="email@ejemplo.com">
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-zinc-800">
          <button type="button" 
                  @click="showSupplierModal = false"
                  class="px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium rounded-lg border border-gray-300 dark:border-zinc-700 transition-colors">
            Cancelar
          </button>
          <button type="submit" 
                  :disabled="!supplierForm.name || !supplierForm.document || loading"
                  class="px-4 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white font-bold rounded-lg shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
            Crear Proveedor
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- TOUR FINAL ÉPICO -->
  <ContextualTour
    ref="productsTourRef"
    module-name="products"
    :steps="productsTourSteps"
    :auto-start="false"
    @complete="handleProductsTourComplete"
    @skip="handleProductsTourSkip"
  />

  <!-- Modal de Bienvenida al Tour Final -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showProductsWelcomeModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 max-w-lg w-full overflow-hidden border border-gray-300 dark:border-zinc-800">
          <!-- Header con diseño profesional -->
          <div class="bg-gradient-to-br from-slate-800 via-slate-900 to-black dark:from-zinc-800 dark:via-zinc-900 dark:to-black px-8 py-8 text-center border-b border-slate-700 dark:border-zinc-700">
            <div class="w-20 h-20 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-5 border border-white/20 shadow-lg shadow-black/20">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Gestión de Productos</h2>
            <p class="text-slate-300 dark:text-zinc-400 text-sm">Tu inventario, tu control total</p>
          </div>
          
          <div class="px-8 py-6">
            <p class="text-gray-600 dark:text-zinc-400 text-center mb-6 leading-relaxed">
              Te mostraremos las herramientas clave para gestionar tu catálogo de forma eficiente y profesional.
            </p>
            
            <!-- Cards de características -->
            <div class="grid grid-cols-2 gap-3 mb-6">
              <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30 rounded-xl p-4 border border-blue-100 dark:border-blue-900/50">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center mb-2">
                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                </div>
                <p class="text-xs font-semibold text-blue-900 dark:text-blue-300">Búsqueda Inteligente</p>
              </div>
              
              <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/30 rounded-xl p-4 border border-emerald-100 dark:border-emerald-900/50">
                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/50 rounded-lg flex items-center justify-center mb-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                  </svg>
                </div>
                <p class="text-xs font-semibold text-emerald-900 dark:text-emerald-300">Importar Excel</p>
              </div>
              
              <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/30 dark:to-orange-950/30 rounded-xl p-4 border border-amber-100 dark:border-amber-900/50">
                <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/50 rounded-lg flex items-center justify-center mb-2">
                  <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                  </svg>
                </div>
                <p class="text-xs font-semibold text-amber-900 dark:text-amber-300">Filtros Avanzados</p>
              </div>
              
              <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-950/30 dark:to-pink-950/30 rounded-xl p-4 border border-purple-100 dark:border-purple-900/50">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center mb-2">
                  <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                  </svg>
                </div>
                <p class="text-xs font-semibold text-purple-900 dark:text-purple-300">Múltiples Vistas</p>
              </div>
            </div>
            
            <!-- Tiempo estimado -->
            <div class="flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-zinc-500 mb-6">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Tour rápido: ~30 segundos</span>
            </div>
            
            <div class="flex gap-3">
              <button
                @click="handleProductsWelcomeSkip"
                class="flex-1 px-5 py-3 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-600 dark:text-zinc-300 text-sm font-semibold rounded-xl border border-gray-200 dark:border-zinc-700 transition-all duration-200"
              >
                Omitir
              </button>
              <button
                @click="handleProductsWelcomeStart"
                class="flex-1 px-6 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/30 dark:shadow-slate-900/50 transition-all duration-300 flex items-center justify-center gap-2"
              >
                <span>Comenzar Tour</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Modal de Finalización del Tour -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div v-if="showFinalModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 max-w-md w-full overflow-hidden border border-gray-300 dark:border-zinc-800">
          <!-- Header con diseño profesional y gradiente -->
          <div class="bg-gradient-to-br from-slate-800 via-slate-900 to-black dark:from-zinc-800 dark:via-zinc-900 dark:to-black px-8 py-8 text-center border-b border-slate-700 dark:border-zinc-700 relative overflow-hidden">
            <!-- Efecto de brillo -->
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -skew-x-12 animate-pulse"></div>
            
            <div class="relative">
              <div class="w-20 h-20 bg-emerald-500/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-5 border border-emerald-500/30 shadow-lg shadow-emerald-500/20">
                <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-white mb-2">¡Excelente Trabajo!</h2>
              <p class="text-slate-300 dark:text-zinc-400 text-sm">Has completado el tour de Productos</p>
            </div>
          </div>
          
          <div class="px-8 py-6">
            <p class="text-gray-600 dark:text-zinc-400 text-center mb-6 leading-relaxed">
              Ya conoces todas las herramientas para gestionar tu inventario de forma profesional.
            </p>
            
            <!-- Próximos pasos -->
            <div class="space-y-3 mb-6">
              <p class="text-xs font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Próximos pasos:</p>
              
              <div class="flex items-start gap-3 p-3 bg-blue-50 dark:bg-blue-950/30 rounded-xl border border-blue-100 dark:border-blue-900/50">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="text-sm font-bold text-blue-600 dark:text-blue-400">1</span>
                </div>
                <div>
                  <p class="text-sm font-semibold text-blue-900 dark:text-blue-300">Agrega tu primer producto</p>
                  <p class="text-xs text-blue-700/70 dark:text-blue-400/70">Haz clic en "Nuevo Producto" para empezar</p>
                </div>
              </div>
              
              <div class="flex items-start gap-3 p-3 bg-emerald-50 dark:bg-emerald-950/30 rounded-xl border border-emerald-100 dark:border-emerald-900/50">
                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">2</span>
                </div>
                <div>
                  <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-300">O importa desde Excel</p>
                  <p class="text-xs text-emerald-700/70 dark:text-emerald-400/70">Carga masiva con detección automática de IA</p>
                </div>
              </div>
              
              <div class="flex items-start gap-3 p-3 bg-purple-50 dark:bg-purple-950/30 rounded-xl border border-purple-100 dark:border-purple-900/50">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="text-sm font-bold text-purple-600 dark:text-purple-400">3</span>
                </div>
                <div>
                  <p class="text-sm font-semibold text-purple-900 dark:text-purple-300">¡Empieza a vender!</p>
                  <p class="text-xs text-purple-700/70 dark:text-purple-400/70">Dirígete al POS y realiza tu primera venta</p>
                </div>
              </div>
            </div>
            
            <button
              @click="closeFinalModal"
              class="w-full px-6 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/30 dark:shadow-slate-900/50 transition-all duration-300 flex items-center justify-center gap-2"
            >
              <span>Comenzar a Gestionar</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- 🏭 Tooltip: Stock por Bodega -->
  <Teleport to="body">
    <div v-if="stockTooltip.visible" 
         class="fixed z-[9999] bg-white dark:bg-zinc-900 shadow-xl rounded-xl border border-gray-300 dark:border-zinc-700 p-3 min-w-[200px]"
         :style="{ left: stockTooltip.x + 'px', top: stockTooltip.y + 'px' }">
      <div class="flex items-center space-x-2 mb-2 pb-2 border-b border-gray-200 dark:border-zinc-700">
        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Stock por Bodega</h4>
      </div>
      <div v-if="stockTooltip.warehouses && stockTooltip.warehouses.length > 0" class="space-y-2">
        <div v-for="wh in stockTooltip.warehouses" :key="wh.id" class="flex items-center justify-between text-sm">
          <div class="flex items-center space-x-2">
            <span class="w-2 h-2 bg-blue-500 dark:bg-blue-400 rounded-full"></span>
            <span class="text-gray-700 dark:text-zinc-300">{{ wh.name }}</span>
          </div>
          <span class="font-bold text-gray-900 dark:text-white">{{ wh.pivot.stock }}</span>
        </div>
      </div>
      <div v-else class="text-xs text-gray-500 dark:text-zinc-400 text-center py-2">
        Sin stock asignado
      </div>
    </div>
  </Teleport>

  <!-- Excel Import Modal -->
  <ExcelImportModal 
    :is-open="showExcelImportModal" 
    @close="showExcelImportModal = false"
    @imported="handleExcelImported"
  />
  </div>
 
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, watch, nextTick, Teleport, Transition, onActivated } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import apiClient from '../services/apiClient'
import { productsService } from '../services/productsService.js'
import { categoriesService } from '../services/categoriesService.js'
import { warehouseService } from '../services/warehouseService.js'
import { apiCall } from '../services/api.js' // 🏭 Para cargar proveedores
import { appStore } from '../store/appStore.js'
import { useAutoRefresh } from '../composables/useRouteState.js'
import { useScreenScaling } from '../composables/useScreenScaling.js'
import TablePaginator from './TablePaginator.vue'
import ContextualTour from './ContextualTour.vue'
import ExcelImportModal from './ExcelImportModal.vue'
import FashionProductForm from './FashionProductForm.vue'
import FashionProductCard from './FashionProductCard.vue'

// Excel Import Modal state
const showExcelImportModal = ref(false)

const handleExcelImported = (result) => {
  showExcelImportModal.value = false
  if (result.success && result.stats?.imported > 0) {
    // Refrescar la lista de productos
    refreshProducts()
  }
}

// Props (para recibir filtros desde navegación AI)
const props = defineProps({
  moduleName: {
    type: String,
    default: 'products'
  },
  products: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  },
  queryParams: {
    type: Object,
    default: () => ({})
  }
})

// Emits
const emit = defineEmits(['navigate', 'changeModule', 'change-module', 'openQuotationInPos', 'refresh'])

// Router - DEBE estar a nivel de setup, NO dentro de onMounted
const route = useRoute()
const router = useRouter()

// 🖼️ Función utilitaria para manejo inteligente de imágenes
const getProductImage = (product) => {
  // 1. Intentar con el array de imágenes (relación images)
  if (product?.images && Array.isArray(product.images) && product.images.length > 0) {
    const primaryImage = product.images.find(img => img.is_primary) || product.images[0]
    if (primaryImage?.image_url) {
      const imageUrl = primaryImage.image_url
      // Si la imagen es base64, devolverla directamente
      if (imageUrl.startsWith('data:image')) {
        return imageUrl
      }
      // Fix relative URLs for tenant backend
      if (imageUrl.startsWith('/storage')) {
        const fullUrl = `http://${window.location.hostname}:8000${imageUrl}`
        console.log('✅ Imagen storage encontrada en galería para:', product.name, fullUrl)
        return fullUrl
      }
      // URL externa completa
      if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
        console.log('✅ URL externa encontrada en galería para:', product.name, imageUrl)
        return imageUrl
      }
      console.log('✅ Imagen en galería para:', product.name, imageUrl)
      return imageUrl
    }
  }
  
  // 2. Intentar múltiples propiedades de imagen (fallback)
  const imageUrl = product?.image_url || product?.image || product?.img || product?.photo
  
  if (!imageUrl) {
    return null
  }
  
  // Si la imagen es base64 (data:image), devolverla directamente
  if (imageUrl.startsWith('data:image')) {
    return imageUrl
  }
  
  // Si hay imagen URL, procesarla
  if (imageUrl.length > 5) {
    // Fix relative URLs for tenant backend
    if (imageUrl.startsWith('/storage')) {
      const fullUrl = `http://${window.location.hostname}:8000${imageUrl}`
      console.log('✅ Imagen storage encontrada para:', product.name, fullUrl)
      return fullUrl
    }
    // URL externa completa
    if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
      console.log('✅ URL externa encontrada para:', product.name, imageUrl)
      return imageUrl
    }
    console.log('✅ Imagen encontrada para:', product.name, imageUrl)
    return imageUrl
  }
  
  console.log('⚠️ Imagen no válida para:', product.name, imageUrl)
  return null
}

// 🚨 Manejar errores de carga de imagen
const handleImageError = (event, product) => {
  // Evitar bucle infinito
  if (event.target.dataset.errorHandled) return
  event.target.dataset.errorHandled = true
  
  // Usar el nombre del producto si está disponible en el contexto, o 'Producto' por defecto
  const name = product?.name || event.target.alt || 'Producto'
  event.target.src = generateDynamicAvatar(name)
}

// Estado reactivo
const loading = ref(true) // Iniciar en true para evitar parpadeo al cargar
const products = ref([])
const categories = ref([])
const suppliers = ref([]) // 🏭 Lista de proveedores
const searchTerm = ref('')
const categoryFilter = ref('')
const statusFilter = ref('')
const sortBy = ref('name')
const viewMode = ref('table')

// 🎓 TOUR FINAL ÉPICO - Prueba final antes de empezar a vender
const DEV_MODE_PRODUCTS = false // false = Tour solo primera vez | true = Tour siempre
const isFirstVisitProducts = ref(DEV_MODE_PRODUCTS || !localStorage.getItem('products_tour_completed'))
const showProductsWelcomeModal = ref(false)
const productsTourRef = ref(null)
const isTourActive = ref(false)

// 👻 PRODUCTOS FANTASMA para demostración del tour
const demoProducts = ref([
  {
    id: 'demo-1',
    name: 'Coca Cola 350ml',
    sku: 'BEB-001',
    barcode: '7891234567890',
    description: 'Bebida gaseosa sabor cola',
    sale_price: 2500,
    cost_price: 1500,
    current_stock: 150,
    min_stock: 20,
    max_stock: 300,
    category_id: 1,
    category_name: 'Bebidas',
    image_url: 'https://images.unsplash.com/photo-1554866585-cd94860890b7?w=400',
    active: true
  },
  {
    id: 'demo-2',
    name: 'Pan Integral',
    sku: 'PAN-002',
    barcode: '7899876543210',
    description: 'Pan de molde integral 500g',
    sale_price: 4200,
    cost_price: 2800,
    current_stock: 45,
    min_stock: 10,
    max_stock: 80,
    category_id: 2,
    category_name: 'Panadería',
    image_url: 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400',
    active: true
  },
  {
    id: 'demo-3',
    name: 'Leche Entera 1L',
    sku: 'LAC-003',
    barcode: '7891111222333',
    description: 'Leche entera pasteurizada',
    sale_price: 3800,
    cost_price: 2500,
    current_stock: 85,
    min_stock: 15,
    max_stock: 150,
    category_id: 3,
    category_name: 'Lácteos',
    image_url: 'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=400',
    active: true
  },
  {
    id: 'demo-4',
    name: 'Arroz Diana 500g',
    sku: 'GRA-004',
    barcode: '7894444555666',
    description: 'Arroz blanco premium',
    sale_price: 3200,
    cost_price: 2000,
    current_stock: 120,
    min_stock: 25,
    max_stock: 200,
    category_id: 4,
    category_name: 'Granos',
    image_url: 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400',
    active: true
  },
  {
    id: 'demo-5',
    name: 'Aceite Girasol 900ml',
    sku: 'ACE-005',
    barcode: '7897777888999',
    description: 'Aceite vegetal de girasol',
    sale_price: 8500,
    cost_price: 5500,
    current_stock: 35,
    min_stock: 8,
    max_stock: 60,
    category_id: 5,
    category_name: 'Aceites',
    image_url: 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400',
    active: true
  },
  {
    id: 'demo-6',
    name: 'Huevos AA x30',
    sku: 'HUE-006',
    barcode: '7892222333444',
    description: 'Huevos frescos cubeta x30',
    sale_price: 12000,
    cost_price: 8000,
    current_stock: 25,
    min_stock: 5,
    max_stock: 40,
    category_id: 3,
    category_name: 'Lácteos',
    image_url: 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400',
    active: true
  }
])

// Computed para mostrar productos reales o fantasma según el tour
const displayProducts = computed(() => {
  if (isTourActive.value && products.value.length === 0) {
    return demoProducts.value
  }
  return products.value
})

const productsTourSteps = ref([
  {
    selector: '#tour-search-products',
    title: 'Buscador Inteligente',
    content: `
      <p>Encuentra productos al instante. Busca por:</p>
      <div class="flex flex-wrap gap-2 mt-3">
        <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-semibold rounded-lg border border-gray-200 dark:border-zinc-700">Nombre</span>
        <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-semibold rounded-lg border border-gray-200 dark:border-zinc-700">SKU</span>
        <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-semibold rounded-lg border border-gray-200 dark:border-zinc-700">Código de barras</span>
      </div>
      <p class="mt-3 text-xs text-gray-500">Tip: Conecta un escáner de códigos para búsqueda instantánea.</p>
    `,
    position: 'bottom'
  },
  {
    selector: '#tour-filter-category',
    title: 'Filtrar por Categoría',
    content: `
      <p>Organiza tu catálogo filtrando por <strong>categorías</strong>. Ideal para tiendas con amplio inventario.</p>
      <p class="mt-2 text-xs text-gray-500">Las categorías se crean automáticamente al agregar productos.</p>
    `,
    position: 'bottom'
  },
  {
    selector: '#tour-filter-status',
    title: 'Estados de Producto',
    content: `
      <p>Filtra productos por su estado actual:</p>
      <div class="space-y-2 mt-3">
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
          <span class="text-xs text-gray-600 dark:text-zinc-400"><strong>Activos:</strong> Disponibles para venta</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
          <span class="text-xs text-gray-600 dark:text-zinc-400"><strong>Inactivos:</strong> Ocultos del POS</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
          <span class="text-xs text-gray-600 dark:text-zinc-400"><strong>Stock Bajo:</strong> Requieren reabastecimiento</span>
        </div>
      </div>
    `,
    position: 'bottom'
  },
  {
    selector: '#tour-view-toggle',
    title: 'Cambia tu Vista',
    content: `
      <p>Elige cómo visualizar tu catálogo:</p>
      <div class="grid grid-cols-2 gap-3 mt-3">
        <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-3 text-center border border-gray-200 dark:border-zinc-700">
          <svg class="w-6 h-6 mx-auto mb-1 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/></svg>
          <span class="text-xs font-medium text-gray-700 dark:text-zinc-300">Tarjetas</span>
          <p class="text-[10px] text-gray-500 mt-0.5">Visual + Imágenes</p>
        </div>
        <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-3 text-center border border-gray-200 dark:border-zinc-700">
          <svg class="w-6 h-6 mx-auto mb-1 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          <span class="text-xs font-medium text-gray-700 dark:text-zinc-300">Tabla</span>
          <p class="text-[10px] text-gray-500 mt-0.5">Datos compactos</p>
        </div>
      </div>
      <p class="text-xs text-gray-500 mt-3 italic">Observa el cambio automático...</p>
    `,
    position: 'bottom'
  },
  {
    selector: '#tour-import-excel',
    title: 'Importación con IA',
    content: `
      <p><span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400 text-xs font-semibold border border-indigo-200 dark:border-indigo-800">Powered by AI</span></p>
      <p class="mt-2">Carga masiva de productos desde Excel. Nuestra <strong>IA detecta automáticamente</strong> las columnas y mapea los datos.</p>
      <p class="mt-2 text-xs text-gray-500">Soporta formatos: .xlsx, .xls, .csv</p>
    `,
    position: 'left'
  },
  {
    selector: '#tour-export-products',
    title: 'Exportar Catálogo',
    content: `
      <p>Descarga tu inventario completo en formato Excel para:</p>
      <ul class="mt-2 space-y-1 text-xs text-gray-600 dark:text-zinc-400">
        <li class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
          Respaldo de datos
        </li>
        <li class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
          Análisis en hojas de cálculo
        </li>
        <li class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
          Compartir con proveedores
        </li>
      </ul>
    `,
    position: 'left'
  },
  {
    selector: '#tour-new-product',
    title: 'Crear Nuevo Producto',
    content: `
      <p>Agrega productos a tu inventario con toda la información necesaria:</p>
      <div class="grid grid-cols-2 gap-2 mt-3 text-xs">
        <div class="flex items-center gap-1.5 text-gray-600 dark:text-zinc-400">
          <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          Nombre y descripción
        </div>
        <div class="flex items-center gap-1.5 text-gray-600 dark:text-zinc-400">
          <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          Precios y costos
        </div>
        <div class="flex items-center gap-1.5 text-gray-600 dark:text-zinc-400">
          <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          Stock inicial
        </div>
        <div class="flex items-center gap-1.5 text-gray-600 dark:text-zinc-400">
          <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          Código de barras
        </div>
      </div>
      <div class="mt-3 p-2 bg-blue-50 dark:bg-blue-950/30 rounded-lg border border-blue-100 dark:border-blue-900/50">
        <p class="text-xs text-blue-700 dark:text-blue-400">
          <strong>Pro tip:</strong> Puedes generar códigos de barras automáticamente.
        </p>
      </div>
    `,
    position: 'left'
  }
])

// Variable para guardar la vista original del usuario
const originalViewMode = ref('table')
let viewChangeTimeout = null

// 🚫 Usuario omite el tour
const handleProductsWelcomeSkip = () => {
  showProductsWelcomeModal.value = false
  isTourActive.value = false
  // Marcar como completado para no mostrar de nuevo
  if (!DEV_MODE_PRODUCTS) {
    localStorage.setItem('products_tour_completed', 'true')
  }
}

// ▶️ Usuario inicia el tour
const handleProductsWelcomeStart = () => {
  showProductsWelcomeModal.value = false
  isTourActive.value = true // Activar productos fantasma
  loading.value = false // Desactivar loading para mostrar productos
  
  // Guardar vista original del usuario
  originalViewMode.value = viewMode.value
  
  productsTourRef.value.startTourConfirmed()
  
  // 🎬 Demo AUTOMÁTICA: cambiar vista cuando llegue al paso de "Cambia tu Vista" (paso 4)
  // El tour tardará aprox 12 segundos en llegar al paso 4 (3 pasos x 4 segundos cada uno)
  viewChangeTimeout = setTimeout(() => {
    // Cambiar a la vista contraria para demostrar
    viewMode.value = originalViewMode.value === 'table' ? 'grid' : 'table'
  }, 12000)
}

const showFinalModal = ref(false)

// 🎉 Función para crear efecto de confeti ÉPICO
const triggerConfetti = () => {
  const duration = 3000
  const animationEnd = Date.now() + duration
  const colors = ['#3b82f6', '#8b5cf6', '#ec4899', '#10b981', '#f59e0b', '#ef4444']
  
  // 🎯 Obtener factor de compensación de escalado
  const { appliedZoom, isCompensating } = useScreenScaling()
  const zoomFactor = isCompensating.value ? appliedZoom.value : 1

  function randomInRange(min, max) {
    return Math.random() * (max - min) + min
  }

  // Explosión inicial desde el centro
  if (window.confetti) {
    window.confetti({
      particleCount: 150,
      spread: 180,
      origin: { y: 0.5 },
      colors: colors,
      zIndex: 9999
    })

    // Ráfagas continuas desde los lados
    const interval = setInterval(function() {
      const timeLeft = animationEnd - Date.now()

      if (timeLeft <= 0) {
        return clearInterval(interval)
      }

      const particleCount = 30 * (timeLeft / duration)

      // Desde la izquierda (no necesita compensación, x:0 siempre es el borde izquierdo)
      window.confetti({
        particleCount,
        angle: 60,
        spread: 55,
        origin: { x: 0, y: 0.6 },
        colors: colors,
        zIndex: 9999
      })
      
      // Desde la derecha (compensar para pantallas escaladas)
      // Si está escalado, ajustamos la posición para que salga del borde real
      window.confetti({
        particleCount,
        angle: 120,
        spread: 55,
        origin: { x: 1 / zoomFactor, y: 0.6 },
        colors: colors,
        zIndex: 9999
      })
    }, 250)
  }
}

const handleProductsTourComplete = () => {
  // Limpiar timeout de cambio de vista si existe
  if (viewChangeTimeout) {
    clearTimeout(viewChangeTimeout)
    viewChangeTimeout = null
  }
  
  // Mostrar modal de finalización
  showFinalModal.value = true
  isTourActive.value = false // Desactivar productos fantasma
  
  // 🎉 EFECTO DE CONFETI AL COMPLETAR EL TOUR
  triggerConfetti()
  
  // Restaurar vista original del usuario
  viewMode.value = originalViewMode.value
}

const handleProductsTourSkip = () => {
  // Limpiar timeout de cambio de vista si existe
  if (viewChangeTimeout) {
    clearTimeout(viewChangeTimeout)
    viewChangeTimeout = null
  }
  
  isTourActive.value = false // Desactivar productos fantasma
  
  // Restaurar vista original del usuario
  viewMode.value = originalViewMode.value
  
  // Refrescar productos reales
  refreshProducts()
}

const closeFinalModal = () => {
  showFinalModal.value = false
  localStorage.setItem('products_tour_completed', 'true')
  isFirstVisitProducts.value = false
  
  // 🔥 IMPORTANTE: Marcar tour del POS como completado también
  // Esto asegura que el modal de bloqueo de caja ahora SÍ aparezca
  localStorage.setItem('pos_tour_completed', 'true')
}

// Sistema de notificaciones
const notifications = ref([])
let notificationId = 0

// 💾 Sistema de Preferencias del Usuario
const USER_PREFERENCES_KEY = 'products_user_preferences'

// Cargar preferencias del usuario
const loadUserPreferences = () => {
  try {
    const savedPreferences = localStorage.getItem(USER_PREFERENCES_KEY)
    if (savedPreferences) {
      const preferences = JSON.parse(savedPreferences)
      
      // Aplicar preferencias guardadas
      viewMode.value = preferences.viewMode || 'table'
      itemsPerPage.value = preferences.itemsPerPage || 25
      sortBy.value = preferences.sortBy || 'name'
      
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
      sortBy: sortBy.value,
      lastUpdated: new Date().toISOString()
    }
    
    localStorage.setItem(USER_PREFERENCES_KEY, JSON.stringify(preferences))
    console.log('💾 Preferencias guardadas:', preferences)
    
  } catch (error) {
    console.warn('⚠️ Error guardando preferencias:', error)
    showNotification(
      '⚠️ Error guardando',
      'No se pudieron guardar las preferencias',
      'error',
      2000
    )
  }
}

// Método mejorado para cambiar vista
const setViewMode = (mode) => {
  viewMode.value = mode
  saveUserPreferences()
}

// 🔧 Función helper para normalizar el estado del producto
const getProductStatus = (product) => {
  // La base de datos puede devolver 'active' o 'is_active'
  return product?.is_active !== undefined ? product.is_active : product?.active
}

// Paginación
const currentPage = ref(1)
const itemsPerPage = ref(25)

// Modals
const showProductModal = ref(false)
const showViewModal = ref(false)
const showNoCategoriesModal = ref(false)
const showCategoryModal = ref(false)
const showSupplierModal = ref(false)
const isEditing = ref(false)
const selectedProduct = ref(null)
const isFashionMode = ref(false) // 👗 Modo Ropa/Variantes (para formulario)
const isInitializing = ref(true) // 🔄 Control de inicialización para evitar parpadeo

// 📊 Sistema de Edición en Línea para Modal de Inventario
const variantChanges = ref({}) // Objeto: { variantId: { stock: true, price: true, cost: true } }
const hasUnsavedChanges = computed(() => Object.keys(variantChanges.value).length > 0)
const changesCount = computed(() => Object.keys(variantChanges.value).length)

// 🏪 Computed: Detectar si la tienda es de tipo Fashion (para vista de tarjetas)
const isFashionStore = computed(() => {
  const storeType = appStore.systemSettings?.store_type
  console.log('🏪 Tipo de tienda detectado:', storeType)
  return storeType === 'fashion' || storeType === 'moda'
})

// Form de categoría
const categoryForm = ref({
  name: '',
  description: '',
  icon: 'shopping-bag',
  color: '#3b82f6'
})

// Form de proveedor
const supplierForm = ref({
  name: '',
  document: '',
  contact_name: '',
  phone: '',
  email: ''
})

// 🎨 Lista de iconos disponibles (95 iconos) - sincronizada con CategoriesView
const availableIcons = [
  // General
  { id: 'shopping-bag', emoji: '🛍️', name: 'Compras', category: 'general' },
  { id: 'gift', emoji: '🎁', name: 'Regalos', category: 'general' },
  { id: 'package', emoji: '📦', name: 'Paquetería', category: 'general' },
  { id: 'money', emoji: '💰', name: 'Finanzas', category: 'general' },
  // Comida y Bebidas
  { id: 'food', emoji: '🍽️', name: 'Comida', category: 'food' },
  { id: 'drink', emoji: '🥤', name: 'Bebidas', category: 'food' },
  { id: 'coffee', emoji: '☕', name: 'Café', category: 'food' },
  { id: 'wine', emoji: '🍷', name: 'Vino/Licor', category: 'food' },
  { id: 'beer', emoji: '🍺', name: 'Cerveza', category: 'food' },
  { id: 'bread', emoji: '🍞', name: 'Panadería', category: 'food' },
  { id: 'meat', emoji: '🥩', name: 'Carnes', category: 'food' },
  { id: 'fruit', emoji: '🍎', name: 'Frutas', category: 'food' },
  { id: 'vegetable', emoji: '🥬', name: 'Verduras', category: 'food' },
  { id: 'candy', emoji: '🍬', name: 'Dulces', category: 'food' },
  { id: 'ice-cream', emoji: '🍦', name: 'Helados', category: 'food' },
  { id: 'pizza', emoji: '🍕', name: 'Pizza', category: 'food' },
  { id: 'burger', emoji: '🍔', name: 'Hamburguesas', category: 'food' },
  { id: 'chicken', emoji: '🍗', name: 'Pollo', category: 'food' },
  { id: 'fish', emoji: '🐟', name: 'Pescado', category: 'food' },
  { id: 'cheese', emoji: '🧀', name: 'Lácteos', category: 'food' },
  // Belleza y Cuidado Personal
  { id: 'perfume', emoji: '💐', name: 'Perfumes', category: 'beauty' },
  { id: 'cosmetics', emoji: '💄', name: 'Cosméticos', category: 'beauty' },
  { id: 'nail', emoji: '💅', name: 'Manicura', category: 'beauty' },
  { id: 'haircut', emoji: '💇', name: 'Peluquería', category: 'beauty' },
  { id: 'mirror', emoji: '🪞', name: 'Espejos', category: 'beauty' },
  // Limpieza
  { id: 'soap', emoji: '🧼', name: 'Jabones', category: 'cleaning' },
  { id: 'cleaning', emoji: '🧹', name: 'Limpieza', category: 'cleaning' },
  { id: 'toilet', emoji: '🚽', name: 'Baño', category: 'cleaning' },
  // Papelería y Oficina
  { id: 'book', emoji: '📚', name: 'Libros', category: 'office' },
  { id: 'pencil', emoji: '✏️', name: 'Papelería', category: 'office' },
  { id: 'scissors', emoji: '✂️', name: 'Artículos Escolares', category: 'office' },
  { id: 'printer', emoji: '🖨️', name: 'Impresión', category: 'office' },
  { id: 'folder', emoji: '📁', name: 'Archivos', category: 'office' },
  // Moda y Ropa
  { id: 'tshirt', emoji: '👕', name: 'Ropa', category: 'fashion' },
  { id: 'dress', emoji: '👗', name: 'Vestidos', category: 'fashion' },
  { id: 'jeans', emoji: '👖', name: 'Pantalones', category: 'fashion' },
  { id: 'shoe', emoji: '👟', name: 'Calzado', category: 'fashion' },
  { id: 'heels', emoji: '👠', name: 'Tacones', category: 'fashion' },
  { id: 'hat', emoji: '🎩', name: 'Sombreros', category: 'fashion' },
  { id: 'watch', emoji: '⌚', name: 'Relojes', category: 'fashion' },
  { id: 'glasses', emoji: '👓', name: 'Gafas', category: 'fashion' },
  { id: 'bag', emoji: '👜', name: 'Bolsos', category: 'fashion' },
  { id: 'jewelry', emoji: '💍', name: 'Joyería', category: 'fashion' },
  { id: 'necktie', emoji: '👔', name: 'Corbatas', category: 'fashion' },
  // Salud y Farmacia
  { id: 'pill', emoji: '💊', name: 'Medicamentos', category: 'health' },
  { id: 'medical', emoji: '⚕️', name: 'Salud', category: 'health' },
  { id: 'syringe', emoji: '💉', name: 'Inyectables', category: 'health' },
  { id: 'thermometer', emoji: '🌡️', name: 'Instrumentos', category: 'health' },
  // Niños y Bebés
  { id: 'toy', emoji: '🧸', name: 'Juguetes', category: 'kids' },
  { id: 'baby', emoji: '👶', name: 'Bebés', category: 'kids' },
  { id: 'bottle', emoji: '🍼', name: 'Biberones', category: 'kids' },
  { id: 'stroller', emoji: '🍼', name: 'Carriolas', category: 'kids' },
  // Tecnología y Electrónica
  { id: 'electronics', emoji: '📱', name: 'Electrónica', category: 'tech' },
  { id: 'computer', emoji: '💻', name: 'Computadoras', category: 'tech' },
  { id: 'camera', emoji: '📷', name: 'Cámaras', category: 'tech' },
  { id: 'headphones', emoji: '🎧', name: 'Audífonos', category: 'tech' },
  { id: 'keyboard', emoji: '⌨️', name: 'Teclados', category: 'tech' },
  { id: 'mouse', emoji: '🖱️', name: 'Mouse', category: 'tech' },
  { id: 'tv', emoji: '📺', name: 'Televisores', category: 'tech' },
  { id: 'game', emoji: '🎮', name: 'Videojuegos', category: 'tech' },
  // Ferretería y Herramientas
  { id: 'tools', emoji: '🔧', name: 'Herramientas', category: 'hardware' },
  { id: 'hammer', emoji: '🔨', name: 'Construcción', category: 'hardware' },
  { id: 'saw', emoji: '🪚', name: 'Carpintería', category: 'hardware' },
  { id: 'wrench', emoji: '🔩', name: 'Tornillería', category: 'hardware' },
  { id: 'paint', emoji: '🎨', name: 'Pintura', category: 'hardware' },
  // Mascotas
  { id: 'pet', emoji: '🐾', name: 'Mascotas', category: 'pets' },
  { id: 'dog', emoji: '🐕', name: 'Perros', category: 'pets' },
  { id: 'cat', emoji: '🐈', name: 'Gatos', category: 'pets' },
  { id: 'fish-pet', emoji: '🐠', name: 'Peces', category: 'pets' },
  { id: 'bird', emoji: '🦜', name: 'Aves', category: 'pets' },
  // Jardín y Plantas
  { id: 'plant', emoji: '🌱', name: 'Plantas', category: 'garden' },
  { id: 'flower', emoji: '🌸', name: 'Flores', category: 'garden' },
  { id: 'tree', emoji: '🌳', name: 'Árboles', category: 'garden' },
  { id: 'garden-tools', emoji: '🌿', name: 'Jardinería', category: 'garden' },
  // Deportes
  { id: 'sport', emoji: '⚽', name: 'Deportes', category: 'sports' },
  { id: 'basketball', emoji: '🏀', name: 'Basketball', category: 'sports' },
  { id: 'tennis', emoji: '🎾', name: 'Tenis', category: 'sports' },
  { id: 'gym', emoji: '💪', name: 'Gimnasio', category: 'sports' },
  { id: 'bike', emoji: '🚴', name: 'Ciclismo', category: 'sports' },
  // Automotriz
  { id: 'car', emoji: '🚗', name: 'Automotriz', category: 'automotive' },
  { id: 'motorcycle', emoji: '🏍️', name: 'Motocicletas', category: 'automotive' },
  { id: 'tire', emoji: '🛞', name: 'Llantas', category: 'automotive' },
  { id: 'gas', emoji: '⛽', name: 'Combustible', category: 'automotive' },
  // Hogar y Muebles
  { id: 'home', emoji: '🏠', name: 'Hogar', category: 'home' },
  { id: 'furniture', emoji: '🛋️', name: 'Muebles', category: 'home' },
  { id: 'bed', emoji: '🛏️', name: 'Colchones', category: 'home' },
  { id: 'lamp', emoji: '💡', name: 'Iluminación', category: 'home' },
  { id: 'kitchen', emoji: '🍳', name: 'Cocina', category: 'home' },
  { id: 'decoration', emoji: '🖼️', name: 'Decoración', category: 'home' },
  { id: 'door', emoji: '🚪', name: 'Puertas', category: 'home' },
  { id: 'key', emoji: '🔑', name: 'Cerrajería', category: 'home' }
]

// Función helper para obtener emoji del icono
const getIconEmoji = (iconId) => {
  const icon = availableIcons.find(i => i.id === iconId)
  return icon ? icon.emoji : '🛍️'
}

// Sistema de imagen dual
const imageUploadMethod = ref('url') // 'file' o 'url'
const previewImage = ref(null)
const imageLoadError = ref(false)

const showStockHelp = ref(false)
const fashionFormRef = ref(null)

const productForm = ref({
  name: '',
  sku: '',
  barcode: '',
  description: '',
  price: 0,
  cost: 0,
  stock: 0,
  min_stock: 5,
  max_stock: 100,
  category_id: '',
  supplier_id: null, // 🏭 Proveedor principal (opcional)
  warehouse_id: null, // 🏢 Bodega donde se guardará el producto
  warehouseStock: {}, // 🏢 Stock por cada tienda { warehouse_id: cantidad }
  warehouseEnabled: {}, // ✅ Control de qué sedes tendrán el producto { warehouse_id: boolean }
  image: '',
  active: true,
  measurement_unit: 'unit', // 📏 Unidad de medida (unit, kg, g, m, cm, l, ml)
  allow_decimal: false // 🔢 Permite cantidades decimales (0.5, 1.25, etc)
})

// 🏢 Lista de bodegas disponibles
const warehouses = ref([])
const loadingWarehouses = ref(false)

// 🏢 Tooltip de stock por bodega
const stockTooltip = ref({
  visible: false,
  productId: null,
  x: 0,
  y: 0,
  warehouses: []
})

// ⚠️ Modal de confirmación para productos sin stock
const showStockWarningModal = ref(false)
const missingFields = ref([])

// Computed properties
const filteredProducts = computed(() => {
  let filtered = displayProducts.value

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(product => 
      (product.name || '').toLowerCase().includes(term) ||
      (product.sku || '').toLowerCase().includes(term) ||
      ((product.category?.name || product.category_name || '').toLowerCase().includes(term))
    )
  }

  if (categoryFilter.value) {
    filtered = filtered.filter(product => product.category_id == categoryFilter.value)
  }

  if (statusFilter.value) {
    filtered = filtered.filter(product => {
      const isActive = getProductStatus(product)
      
      if (statusFilter.value === 'active') return isActive !== false
      if (statusFilter.value === 'inactive') return isActive === false
      if (statusFilter.value === 'low-stock') return (product.current_stock || 0) <= (product.min_stock || 0)
      return true
    })
  }

  // Ordenamiento
  filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'price':
        return parseFloat(a.sale_price || 0) - parseFloat(b.sale_price || 0)
      case 'stock':
        return (a.current_stock || 0) - (b.current_stock || 0)
      case 'created_at':
        return new Date(b.created_at || 0) - new Date(a.created_at || 0)
      default:
        return (a.name || '').localeCompare(b.name || '')
    }
  })

  return filtered
})

// Computed properties para paginación
const totalItems = computed(() => filteredProducts.value.length)
const totalPages = computed(() => {
  const filtered = filteredProducts.value || []
  const perPage = itemsPerPage.value || 10
  return Math.ceil(filtered.length / perPage)
})

const paginatedProducts = computed(() => {
  const filtered = filteredProducts.value || []
  const perPage = itemsPerPage.value || 10
  const page = currentPage.value || 1
  const start = (page - 1) * perPage
  const end = start + perPage
  return filtered.slice(start, end)
})

const activeProducts = computed(() => displayProducts.value.filter(p => getProductStatus(p) !== false).length)
const lowStockProducts = computed(() => displayProducts.value.filter(p => (p.current_stock || 0) <= (p.min_stock || 0)).length)
const totalValue = computed(() => 
  displayProducts.value.reduce((sum, p) => sum + (parseFloat(p.sale_price || 0) * (p.current_stock || 0)), 0)
)
const uniqueCategories = computed(() => {
  const categoryIds = displayProducts.value.map(p => p.category_id).filter(Boolean)
  return new Set(categoryIds).size
})

// Métodos de utilidad
const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO').format(parseFloat(value || 0))
}

// 🔢 Formatear número simple (para stock)
const formatNumber = (value) => {
  return new Intl.NumberFormat('es-CO').format(parseInt(value || 0))
}

// 💵 Formatear número para inputs (con separadores)
const formatInputNumber = (value) => {
  const num = parseFloat(value || 0)
  if (isNaN(num)) return '0'
  return new Intl.NumberFormat('es-CO', { 
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
  }).format(num)
}

// 🎯 Handler para input de costo con formato
const handleCostInput = (event, variant) => {
  const rawValue = event.target.value.replace(/\./g, '').replace(/,/g, '.')
  const numValue = parseFloat(rawValue) || 0
  variant.editableCost = numValue
  markVariantChanged(variant.id)
}

// 🎯 Handler para input de precio con formato
const handlePriceInput = (event, variant) => {
  const rawValue = event.target.value.replace(/\./g, '').replace(/,/g, '.')
  const numValue = parseFloat(rawValue) || 0
  variant.editablePrice = numValue
  markVariantChanged(variant.id)
}

// 🏷️ Formatear opciones de variante para mostrar
const formatVariantOptions = (optionsSummary) => {
  try {
    const options = typeof optionsSummary === 'string' 
      ? JSON.parse(optionsSummary) 
      : optionsSummary
    
    if (!Array.isArray(options)) return 'Variante'
    
    return options.map(o => `${o.name}: ${o.value}`).join(' / ')
  } catch (e) {
    return 'Variante'
  }
}

// 🖼️ Índice de imagen seleccionada para galería
const selectedImageIndex = ref(0)

// 📸 Computed: Lista de imágenes del producto seleccionado
const selectedProductImages = computed(() => {
  if (!selectedProduct.value) return []
  
  const images = []
  
  // 1. Cargar imágenes de la galería
  if (selectedProduct.value.images && Array.isArray(selectedProduct.value.images)) {
    selectedProduct.value.images.forEach(img => {
      const url = img.url || img.image_url
      if (url) {
        images.push(url.startsWith('http') ? url : (url.startsWith('/') ? url : `/storage/${url}`))
      }
    })
  }
  
  // 2. Si no hay galería, usar image_url principal
  if (images.length === 0 && selectedProduct.value.image_url) {
    const url = selectedProduct.value.image_url
    images.push(url.startsWith('http') ? url : (url.startsWith('/') ? url : `/storage/${url}`))
  }
  
  // 3. Fallback a imagen por defecto
  if (images.length === 0) {
    images.push('/images/no-image.png')
  }
  
  return images
})

// 📸 Computed: Imagen principal seleccionada
const selectedProductMainImage = computed(() => {
  const images = selectedProductImages.value
  const index = selectedImageIndex.value
  return images[index] || images[0] || '/images/no-image.png'
})

// 👗 Helpers para productos con variantes (FASHION)
const hasVariants = (product) => {
  return product.variants && product.variants.length > 0
}

// 🎯 Determinar si debe mostrar el modal de Fashion (independiente de si tiene variantes)
const isFashionProduct = (product) => {
  // ✅ PRIORIDAD 1: Campo store_type explícito (nueva lógica)
  if (product.store_type) {
    return product.store_type === 'fashion'
  }
  
  // ✅ PRIORIDAD 2: Campo legacy store_category
  if (product.store_category) {
    return product.store_category === 'fashion'
  }
  
  // ✅ PRIORIDAD 3: Detectar por product_type 'variable' (productos con variantes = fashion)
  if (product.product_type === 'variable') {
    return true
  }
  
  // ✅ PRIORIDAD 4: Fallback - si tiene variantes reales, es fashion
  return hasVariants(product)
}

const getVariantsCount = (product) => {
  return hasVariants(product) ? product.variants.length : 0
}

const getPriceRange = (product) => {
  if (!hasVariants(product)) {
    return `$${formatCurrency(product.sale_price)}`
  }
  
  const prices = product.variants.map(v => parseFloat(v.price))
  const minPrice = Math.min(...prices)
  const maxPrice = Math.max(...prices)
  
  if (minPrice === maxPrice) {
    return `$${formatCurrency(minPrice)} (Base)`
  }
  
  return `$${formatCurrency(minPrice)} - $${formatCurrency(maxPrice)}`
}

const getStockSummary = (product) => {
  if (!hasVariants(product)) {
    return {
      total: product.current_stock || 0,
      variants: 0
    }
  }
  
  const totalStock = product.variants.reduce((sum, v) => sum + (v.stock || 0), 0)
  return {
    total: totalStock,
    variants: product.variants.length
  }
}

const getStockBreakdown = (product) => {
  if (!hasVariants(product)) return ''
  
  // Agrupar por la primera opción (generalmente talla)
  const breakdown = {}
  product.variants.forEach(variant => {
    if (variant.options_summary) {
      const options = typeof variant.options_summary === 'string' 
        ? JSON.parse(variant.options_summary) 
        : variant.options_summary
      
      if (options && options.length > 0) {
        const firstOption = options[0]
        const key = firstOption.value
        breakdown[key] = (breakdown[key] || 0) + (variant.stock || 0)
      }
    }
  })
  
  return Object.entries(breakdown)
    .map(([key, value]) => `${key}: ${value}`)
    .join(' | ')
}

const getTotalInventoryValue = (product) => {
  if (!hasVariants(product)) {
    return product.current_stock * product.sale_price
  }
  
  return product.variants.reduce((sum, v) => {
    return sum + ((v.stock || 0) * (v.price || 0))
  }, 0)
}

// Métodos para manejo de imágenes
const isValidUrl = (string) => {
  try {
    new URL(string)
    return true
  } catch (_) {
    return false
  }
}

// Métodos principales
const loadProducts = async () => {
  try {
    loading.value = true
    
    // Preparar parámetros de consulta
    const params = {
      _t: Date.now() // ✅ Cache busting para forzar recarga real
    }
    
    // Determinar el estado a consultar
    if (statusFilter.value === 'active') {
      params.status = 'active'
    } else if (statusFilter.value === 'inactive') {
      params.status = 'inactive'
    } else if (statusFilter.value === 'low-stock') {
      params.status = 'active' // Los productos con stock bajo deben estar activos
    } else {
      params.status = 'all' // Mostrar todos los productos
    }
    
    // Obtener TODOS los productos sin paginación del servidor
    params.per_page = 1000 // Obtener un número alto para evitar paginación del servidor
    
    console.log('📦 Cargando productos con params:', params)
    const response = await productsService.getAll(params)
    console.log('✅ Respuesta productos:', response)
    
    // La API devuelve datos paginados, extraer el array de productos
    if (response.data && response.data.data) {
      products.value = response.data.data || []
    } else {
      products.value = response.data || []
    }
    
    console.log('Productos cargados:', products.value.length)
  } catch (error) {
    console.error('Error cargando productos:', error)
    showNotification(
      'Error al cargar productos',
      'No se pudieron cargar los productos. Por favor, intenta nuevamente.',
      'error'
    )
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await categoriesService.getAll()
    categories.value = response.data || []
    console.log('Categorías cargadas:', categories.value.length)
  } catch (error) {
    console.error('Error cargando categorías:', error)
  }
}

// 🏭 Cargar proveedores activos
const loadSuppliers = async () => {
  try {
    const response = await apiCall('/suppliers/analytics')
    if (response.success) {
      // Filtrar solo proveedores activos
      suppliers.value = (response.data.suppliers || []).filter(s => s.active)
      console.log('✅ Proveedores cargados:', suppliers.value.length)
    }
  } catch (error) {
    console.error('❌ Error cargando proveedores:', error)
    suppliers.value = []
  }
}

// 🏢 Computed: Filtrar bodegas según plan del tenant
const availableWarehouses = computed(() => {
  const plan = appStore.tenantPlan
  const isPremiumOrEnterprise = plan === 'premium' || plan === 'enterprise'
  
  // Si es premium/enterprise, mostrar todas las bodegas activas
  if (isPremiumOrEnterprise) {
    return warehouses.value.filter(w => w.active)
  }
  
  // Si es basic/free, mostrar solo la bodega actual de la sesión
  const currentWarehouseId = appStore.cashSession.current?.warehouse_id
  if (currentWarehouseId) {
    return warehouses.value.filter(w => w.active && w.id === currentWarehouseId)
  }
  
  // Si no hay sesión activa, mostrar la bodega por defecto
  const defaultWarehouse = warehouses.value.find(w => w.is_default && w.active)
  return defaultWarehouse ? [defaultWarehouse] : warehouses.value.filter(w => w.active).slice(0, 1)
})

// 🏢 Computed: Determinar si mostrar múltiples columnas de stock
const showMultipleStockColumns = computed(() => {
  return availableWarehouses.value.length > 1
})

// 🏢 Helper: Obtener stock de un producto en una bodega específica
// Devuelve null si el producto NO existe en esa sede (para mostrar "N/A")
const getWarehouseStock = (product, warehouseId) => {
  // Si el producto tiene relación con bodegas Y tiene datos, usar esa data
  if (product.warehouses && product.warehouses.length > 0) {
    const warehouse = product.warehouses.find(w => w.id === warehouseId || w.warehouse_id === warehouseId)
    if (warehouse) {
      // ✅ Producto existe en esta sede
      const pivotStock = warehouse.pivot?.stock ?? warehouse.stock ?? 0
      
      // 🔧 FALLBACK CRÍTICO: Si pivot.stock es 0 pero current_stock tiene valor,
      // significa que la tabla product_warehouse no está sincronizada.
      // En sistemas de bodega única (solo 1 warehouse), usar current_stock.
      if (pivotStock === 0 && product.current_stock > 0) {
        // Si solo hay UNA bodega en la relación, usar current_stock
        if (product.warehouses.length === 1) {
          return product.current_stock
        }
      }
      
      return pivotStock
    }
    // Si tiene warehouses pero no está en esta bodega, devolver null (N/A)
    return null
  }
  
  // Si NO tiene relación con bodegas (warehouses vacío o null), 
  // usar current_stock solo en la bodega principal (ID 1)
  if (warehouseId === 1) {
    return product.current_stock || 0
  }
  
  return null  // ❌ Producto NO existe en esta sede
}

// 🏢 Helper: Obtener stock total de un producto
const getTotalStock = (product) => {
  if (!product.warehouses || product.warehouses.length === 0) {
    return product.current_stock || 0
  }
  
  const totalFromWarehouses = product.warehouses.reduce((total, warehouse) => {
    const stock = warehouse.pivot?.stock || warehouse.stock || 0
    return total + stock
  }, 0)
  
  // 🔧 FALLBACK: Si warehouses suma 0 pero current_stock tiene valor, usar current_stock
  if (totalFromWarehouses === 0 && product.current_stock > 0) {
    return product.current_stock
  }
  
  return totalFromWarehouses
}

// 🏢 Cargar bodegas disponibles
const loadWarehouses = async () => {
  try {
    loadingWarehouses.value = true
    const data = await warehouseService.getAll()
    
    // La API ahora devuelve { warehouses: [], plan_info: {} }
    if (data && data.warehouses) {
      warehouses.value = Array.isArray(data.warehouses) ? data.warehouses : []
    } else {
      // Fallback por si la API cambia o devuelve array directo
      warehouses.value = Array.isArray(data) ? data : []
    }
    
    console.log('✅ Bodegas cargadas:', warehouses.value.length)
    console.log('📦 Detalle de bodegas:', warehouses.value)
    
    // Seleccionar automáticamente la bodega predeterminada
    const defaultWarehouse = warehouses.value.find(w => w.is_default)
    if (defaultWarehouse && !productForm.value.warehouse_id) {
      productForm.value.warehouse_id = defaultWarehouse.id
    }
  } catch (error) {
    console.error('Error cargando bodegas:', error)
    showNotification(
      'Error al cargar bodegas',
      'No se pudieron cargar las bodegas disponibles',
      'error'
    )
  } finally {
    loadingWarehouses.value = false
  }
}

// 🏭 Mostrar tooltip de stock por bodega
const showStockTooltip = (event, product) => {
  if (product.warehouses && product.warehouses.length > 0) {
    stockTooltip.value.visible = true
    stockTooltip.value.productId = product.id
    stockTooltip.value.warehouses = product.warehouses
    
    // Posicionar tooltip cerca del mouse
    stockTooltip.value.x = event.clientX + 10
    stockTooltip.value.y = event.clientY + 10
  }
}

// 🏭 Ocultar tooltip de stock por bodega
const hideStockTooltip = () => {
  stockTooltip.value.visible = false
  stockTooltip.value.productId = null
  stockTooltip.value.warehouses = []
  stockTooltip.value.x = 0
  stockTooltip.value.y = 0
}

const refreshProducts = async () => {
  console.log('Refrescando productos...')
  await loadProducts()
}

// 📏 Helper: Obtener abreviación de unidad de medida
const getUnitAbbreviation = (unit) => {
  const units = {
    unit: 'und',
    kg: 'kg',
    g: 'g',
    m: 'm',
    cm: 'cm',
    l: 'L',
    ml: 'ml'
  }
  return units[unit] || 'und'
}

// 🔢 Auto-actualizar allow_decimal según la unidad seleccionada
const updateAllowDecimal = () => {
  const decimalUnits = ['kg', 'g', 'm', 'cm', 'l', 'ml']
  productForm.value.allow_decimal = decimalUnits.includes(productForm.value.measurement_unit)
}

const openCreateModal = async () => {
  // Limpiar producto seleccionado (importante para que el watcher en FashionProductForm no cargue datos)
  selectedProduct.value = null
  
  // VALIDACIÓN: Verificar si existen categorías primero
  if (!categories.value || categories.value.length === 0) {
    showNotification(
      'Sin categorías',
      'Debes crear al menos una categoría antes de agregar productos',
      'warning'
    )
    showNoCategoriesModal.value = true
    return
  }
  
  isEditing.value = false
  
  // 🏪 El modo fashion ya fue detectado en onMounted, no necesitamos volver a detectarlo
  
  // Cargar bodegas antes de abrir el modal
  await loadWarehouses()
  
  // Inicializar warehouseStock y warehouseEnabled con todas las tiendas disponibles según el plan
  const warehouseStock = {}
  const warehouseEnabled = {}
  availableWarehouses.value.forEach(warehouse => {
    warehouseStock[warehouse.id] = 0
    warehouseEnabled[warehouse.id] = false // Por defecto no está habilitada ninguna sede
  })
  console.log('🏭 Inicializando formulario nuevo producto')
  console.log('📦 Warehouses totales:', warehouses.value.length)
  console.log('✅ Warehouses disponibles según plan:', availableWarehouses.value.length)
  console.log('🔢 warehouseStock inicializado:', warehouseStock)
  console.log('✅ warehouseEnabled inicializado:', warehouseEnabled)
  
  productForm.value = {
    name: '',
    sku: '',
    barcode: '',
    description: '',
    price: '',
    cost: '',
    stock: 0, // Solo se usa si hay 1 bodega
    min_stock: 5,
    max_stock: 100,
    category_id: '',
    warehouse_id: warehouses.value.find(w => w.is_default)?.id || null,
    warehouseStock: warehouseStock,
    warehouseEnabled: warehouseEnabled,
    image: '',
    active: true,
    measurement_unit: 'unit', // 📏 Unidad de medida por defecto
    allow_decimal: false // 🔢 No permite decimales por defecto
  }
  
  // ✅ Si solo hay 1 bodega disponible, sincronizar stock inicial (0)
  if (availableWarehouses.value.length === 1) {
    const warehouseId = availableWarehouses.value[0].id
    productForm.value.warehouseStock[warehouseId] = 0
    console.log('✅ Sincronización inicial para bodega única:', warehouseId, '= 0')
  }
  
  // Limpiar estado de imágenes
  previewImage.value = null
  imageLoadError.value = false
  imageUploadMethod.value = 'url'
  
  showProductModal.value = true
}

const editProduct = async (product) => {
  // Cerrar modal de detalles si está abierto
  showViewModal.value = false
  
  try {
    // 🔥 SIEMPRE obtener datos completos del producto desde el API
    console.log('🔍 [editProduct] Obteniendo producto completo desde API:', product.id)
    const response = await productsService.getById(product.id)
    
    if (!response.success || !response.data) {
      throw new Error('No se pudieron obtener los datos del producto')
    }
    
    // Usar el producto completo del API
    product = response.data
    console.log('✅ [editProduct] Producto completo obtenido:', product)
  } catch (error) {
    console.error('❌ Error obteniendo producto:', error)
    showNotification(
      'Error',
      'No se pudieron cargar los datos del producto',
      'error'
    )
    return
  }
  
  // Cargar bodegas para obtener todas las tiendas disponibles
  await loadWarehouses()
  
  // Inicializar warehouseStock y warehouseEnabled con todas las tiendas
  const warehouseStock = {}
  const warehouseEnabled = {}
  warehouses.value.forEach(warehouse => {
    warehouseStock[warehouse.id] = 0
    warehouseEnabled[warehouse.id] = false // Por defecto no está habilitada
  })
  
  console.log('🔍 [editProduct] Producto recibido:', {
    id: product.id,
    name: product.name,
    product_type: product.product_type,
    warehouses: product.warehouses,
    variants: product.variants?.length || 0
  })
  
  // 🎯 Detectar si es producto SIMPLE o VARIABLE
  const isSimpleProduct = !product.product_type || product.product_type === 'simple'
  const hasVariants = product.variants && product.variants.length > 0 && product.product_type === 'variable'
  
  console.log('🔍 [editProduct] Tipo de producto:', isSimpleProduct ? 'SIMPLE (stock por bodega)' : 'VARIABLE (stock en variantes)')
  
  // 🔥 CARGAR STOCK DESDE product.warehouses (SOLO para productos SIMPLES)
  if (isSimpleProduct && product.warehouses && Array.isArray(product.warehouses)) {
    // Agrupar por warehouse_id para evitar duplicados
    const stockByWarehouse = new Map()
    
    product.warehouses.forEach(warehouse => {
      if (warehouse.id) {
        const stock = warehouse.pivot?.stock || warehouse.stock || 0
        const parsedStock = parseInt(stock) || 0
        
        // Si ya existe, sumar el stock (por si hay duplicados)
        const currentStock = stockByWarehouse.get(warehouse.id) || 0
        stockByWarehouse.set(warehouse.id, Math.max(currentStock, parsedStock))
      }
    })
    
    // Aplicar el stock agrupado
    stockByWarehouse.forEach((stock, warehouseId) => {
      warehouseStock[warehouseId] = stock
      if (stock > 0) {
        warehouseEnabled[warehouseId] = true
        console.log(`✅ Tienda ${warehouseId} con stock ${stock}`)
      }
    })
  }
  
  // Fallback: Si el producto tiene warehouse_id (tienda actual) - Solo para compatibilidad
  if (product.warehouse_id && warehouseStock[product.warehouse_id] === 0) {
    const parsedStock = parseInt(product.current_stock || product.stock || 0)
    if (parsedStock > 0) {
      warehouseStock[product.warehouse_id] = parsedStock
      warehouseEnabled[product.warehouse_id] = true
      console.log(`✅ Fallback: Tienda ${product.warehouse_id} con stock ${parsedStock}`)
    }
  }
  
  console.log('📦 [editProduct] warehouseStock final:', JSON.parse(JSON.stringify(warehouseStock)))
  console.log('✅ [editProduct] warehouseEnabled final:', JSON.parse(JSON.stringify(warehouseEnabled)))
  
  isEditing.value = true
  
  // 🏢 Si hay solo 1 bodega, obtener el stock de esa bodega para el campo "Stock Inicial"
  let singleWarehouseStock = parseInt(product.current_stock || product.stock || 0)
  if (warehouses.value.length === 1 && warehouses.value[0]) {
    const warehouseId = warehouses.value[0].id
    if (warehouseStock[warehouseId] !== undefined) {
      singleWarehouseStock = warehouseStock[warehouseId]
    }
  }
  
  // Mapear correctamente los campos del API a los campos del formulario
  productForm.value = {
    id: product.id,
    name: product.name,
    sku: product.sku || '',
    barcode: product.barcode || '',
    description: product.description || '',
    price: parseFloat(product.sale_price || product.price || 0),
    cost: parseFloat(product.cost_price || product.cost || 0),
    stock: singleWarehouseStock, // Usar el stock de la bodega única si aplica
    min_stock: parseInt(product.min_stock || 5),
    max_stock: parseInt(product.max_stock || 100),
    category_id: product.category_id,
    supplier_id: product.supplier_id || null, // 🏭 Cargar proveedor
    warehouseStock: warehouseStock,
    warehouseEnabled: warehouseEnabled,
    image: product.image_url || product.image || '',
    active: getProductStatus(product) !== false,
    measurement_unit: product.measurement_unit || 'unit', // 📏 Cargar unidad de medida
    allow_decimal: product.allow_decimal || false // 🔢 Cargar si permite decimales
  }
  
  // Configurar estado de imágenes para edición
  previewImage.value = null
  imageLoadError.value = false
  
  // Detectar si es URL o archivo base64
  if (productForm.value.image) {
    if (productForm.value.image.startsWith('data:')) {
      imageUploadMethod.value = 'file'
      previewImage.value = productForm.value.image
    } else {
      imageUploadMethod.value = 'url'
    }
  } else {
    imageUploadMethod.value = 'url'
  }
  
  // 👗 Si es modo fashion, cargar detalles completos del producto
  if (isFashionMode.value) {
    try {
      const response = await productsService.getById(product.id)
      if (response.success && response.data) {
        selectedProduct.value = response.data
      }
    } catch (error) {
      console.error('Error cargando detalles del producto:', error)
    }
  }
  
  showProductModal.value = true
}

const viewProduct = async (product) => {
  selectedProduct.value = product
  variantChanges.value = {} // Limpiar cambios previos
  selectedImageIndex.value = 0 // 🖼️ Reset índice de imagen
  
  console.log('👁️ [viewProduct] Abriendo modal para producto:', {
    id: product.id,
    name: product.name,
    store_type: product.store_type,
    product_type: product.product_type,
    hasVariants: hasVariants(product),
    isFashion: isFashionProduct(product)
  })
  
  // Fetch full details including variants
  try {
    const response = await productsService.getById(product.id)
    if (response.success) {
      selectedProduct.value = response.data
      
      console.log('📦 DEBUG viewProduct:', {
        id: selectedProduct.value.id,
        name: selectedProduct.value.name,
        store_category: selectedProduct.value.store_category,
        store_type: selectedProduct.value.store_type,
        product_type: selectedProduct.value.product_type
      })
      
      // Inicializar campos editables para cada variante (solo si es producto fashion)
      if (isFashionProduct(selectedProduct.value) && selectedProduct.value.variants) {
        selectedProduct.value.variants.forEach(variant => {
          variant.editableStock = variant.stock || 0
          variant.editablePrice = variant.price || 0
          variant.editableCost = variant.cost_price || 0
        })
      }
      
      // 🎯 Mostrar el modal apropiado
      showViewModal.value = true
    }
  } catch (error) {
    console.error("Error fetching product details", error)
    showViewModal.value = true // Abrir modal aunque falle la carga completa
  }
}

// 📝 Funciones de Edición en Línea (Excel-Style)
const markVariantChanged = (variantId) => {
  if (!variantChanges.value[variantId]) {
    variantChanges.value[variantId] = {}
  }
  // Marcar que esta variante tiene cambios pendientes
  variantChanges.value = { ...variantChanges.value }
}

const incrementStock = (variant) => {
  variant.editableStock = (variant.editableStock || 0) + 1
  markVariantChanged(variant.id)
}

const decrementStock = (variant) => {
  if (variant.editableStock > 0) {
    variant.editableStock = (variant.editableStock || 0) - 1
    markVariantChanged(variant.id)
  }
}

const saveInventoryChanges = async () => {
  try {
    const changedVariants = selectedProduct.value.variants.filter(v => variantChanges.value[v.id])
    
    if (changedVariants.length === 0) {
      showNotification('Información', 'No hay cambios para guardar', 'info')
      return
    }
    
    // Preparar datos para actualizar (usar valores editables)
    const updates = changedVariants.map(variant => ({
      id: variant.id,
      stock: variant.editableStock,
      price: variant.editablePrice,
      cost_price: variant.editableCost
    }))
    
    // Llamada al backend usando apiCall
    const data = await apiCall('/products/variants/bulk-update', {
      method: 'PUT',
      body: JSON.stringify({ variants: updates })
    })
    
    if (!data.success) {
      throw new Error(data.message || 'Error al guardar cambios')
    }
    
    showNotification(
      '✅ Cambios Guardados', 
      `Se actualizaron ${changedVariants.length} variante(s) correctamente`, 
      'success'
    )
    
    // Limpiar cambios
    variantChanges.value = {}
    
    // Recargar productos
    await loadProducts()
    
    // Actualizar el producto seleccionado con los datos frescos
    if (selectedProduct.value) {
      const updatedProduct = products.value.find(p => p.id === selectedProduct.value.id)
      if (updatedProduct) {
        // Fetch detalles completos del producto actualizado
        try {
          const response = await productsService.getById(updatedProduct.id)
          if (response.success) {
            selectedProduct.value = response.data
            
            // Reinicializar campos editables
            if (selectedProduct.value.variants) {
              selectedProduct.value.variants.forEach(variant => {
                variant.editableStock = variant.stock || 0
                variant.editablePrice = variant.price || 0
                variant.editableCost = variant.cost_price || 0
              })
            }
          }
        } catch (error) {
          console.error('Error actualizando producto seleccionado:', error)
        }
      }
    }
    
  } catch (error) {
    console.error('Error saving inventory changes:', error)
    showNotification('Error', error.message || 'No se pudieron guardar los cambios', 'error')
  }
}

// 🔔 Sistema de notificaciones elegantes
const showNotification = (title, message = '', type = 'info', duration = 5000) => {
  const notification = {
    id: ++notificationId,
    title,
    message,
    type
  }
  
  notifications.value.push(notification)
  
  // Auto-remover después del tiempo especificado
  setTimeout(() => {
    removeNotification(notification.id)
  }, duration)
}

const removeNotification = (id) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value.splice(index, 1)
  }
}

// 🔄 Función para habilitar/deshabilitar productos
// Estado para confirmación de cambio de estado
const showStatusConfirmModal = ref(false)
const showCategoryInactiveModal = ref(false)
const pendingStatusChange = ref(null)

const toggleProductStatus = async (product) => {
  const currentStatus = getProductStatus(product)
  const newStatus = currentStatus !== false ? false : true
  
  console.log('toggleProductStatus called:', {
    product: product.name,
    currentStatus,
    newStatus,
    categoryId: product.category_id,
    categoriesLoaded: categories.value.length
  })
  
  // Si se intenta activar un producto, verificar que la categoría esté activa
  if (newStatus === true) {
    const productCategory = categories.value.find(c => c.id === product.category_id)
    console.log('Category found:', productCategory)
    
    if (!productCategory) {
      showNotification(
        'Error',
        'No se encontró la categoría del producto. Por favor, recarga la página.',
        'error'
      )
      return
    }
    
    if (!productCategory.active) {
      console.log('Category is inactive, showing modal')
      // Mostrar modal para activar categoría primero
      pendingStatusChange.value = {
        product,
        newStatus,
        category: productCategory
      }
      showCategoryInactiveModal.value = true
      return
    }
  }
  
  // Mostrar modal de confirmación normal
  pendingStatusChange.value = {
    product,
    newStatus
  }
  showStatusConfirmModal.value = true
}

const confirmStatusChange = async () => {
  if (!pendingStatusChange.value) return
  
  try {
    loading.value = true
    showStatusConfirmModal.value = false
    
    const { product, newStatus } = pendingStatusChange.value
    
    // ✅ SOLO enviar campos necesarios (NO todo el objeto)
    await productsService.update(product.id, { 
      active: newStatus 
    })
    
    // 🔄 RECARGAR DESDE BASE DE DATOS (Sin lógica local)
    await loadProducts()
    
    showNotification(
      newStatus ? 'Producto habilitado' : 'Producto deshabilitado',
      `El producto "${product.name}" ha sido ${newStatus ? 'habilitado' : 'deshabilitado'} exitosamente`,
      'success'
    )
  } catch (error) {
    console.error('Error al cambiar estado del producto:', error)
    showNotification(
      'Error al cambiar estado',
      'No se pudo cambiar el estado del producto: ' + (error.message || 'Error desconocido'),
      'error'
    )
  } finally {
    loading.value = false
    pendingStatusChange.value = null
  }
}

const activateCategoryAndProduct = async () => {
  if (!pendingStatusChange.value) return
  
  try {
    loading.value = true
    showCategoryInactiveModal.value = false
    
    const { product, category } = pendingStatusChange.value
    
    // Primero activar la categoría
    await categoriesService.update(category.id, {
      name: category.name,
      description: category.description || '',
      icon: category.icon || 'shopping-bag',
      color: category.color || '#3b82f6',
      active: true
    })
    
    // Luego activar el producto
    await productsService.update(product.id, { 
      ...product, 
      is_active: true,
      active: true 
    })
    
    // Recargar datos
    await loadProducts()
    await loadCategories()
    
    showNotification(
      'Categoría y Producto activados',
      `La categoría "${category.name}" y el producto "${product.name}" han sido activados exitosamente`,
      'success'
    )
  } catch (error) {
    console.error('Error al activar categoría y producto:', error)
    showNotification(
      'Error al activar',
      'No se pudo activar la categoría y el producto: ' + (error.message || 'Error desconocido'),
      'error'
    )
  } finally {
    loading.value = false
    pendingStatusChange.value = null
  }
}

const exportProducts = () => {
  try {
    // Crear datos para Excel
    const headers = ['Código', 'Nombre', 'SKU', 'Categoría', 'Precio Costo', 'Precio Venta', 'Stock Actual', 'Stock Mínimo', 'Estado']
    
    const rows = filteredProducts.value.map(product => [
      product.barcode || 'N/A',
      product.name || 'N/A',
      product.sku || 'N/A',
      product.category?.name || 'Sin categoría',
      `$${formatCurrency(product.cost_price || product.cost || 0)}`,
      `$${formatCurrency(product.sale_price || product.price || 0)}`,
      product.current_stock || product.stock || 0,
      product.min_stock || 0,
      getProductStatus(product) !== false ? 'Activo' : 'Inactivo'
    ])
    
    // Crear CSV
    const csvContent = [
      headers.join(','),
      ...rows.map(row => row.map(cell => `"${cell}"`).join(','))
    ].join('\n')
    
    // Crear blob y descargar
    const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `productos_${new Date().toISOString().split('T')[0]}.csv`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
    
    showToast('Productos exportados exitosamente', 'success')
  } catch (error) {
    console.error('Error exportando productos:', error)
    showToast('Error al exportar productos', 'error')
  }
}

// Métodos para manejo de imágenes
const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  // Validar tipo de archivo
  if (!file.type.startsWith('image/')) {
    showNotification(
      'Archivo no válido',
      'Por favor selecciona un archivo de imagen válido (JPG, PNG, GIF, etc.)',
      'warning'
    )
    return
  }
  
  // Validar tamaño (5MB máximo)
  if (file.size > 5 * 1024 * 1024) {
    showNotification(
      'Archivo muy grande',
      'La imagen debe ser menor a 5MB. Por favor selecciona una imagen más pequeña.',
      'warning'
    )
    return
  }
  
  // Crear preview
  const reader = new FileReader()
  reader.onload = (e) => {
    previewImage.value = e.target.result
    // Asignar al formulario (base64 para offline o procesamiento posterior)
    productForm.value.image = e.target.result
  }
  reader.readAsDataURL(file)
}

const clearImageUpload = () => {
  previewImage.value = null
  productForm.value.image = ''
  // Limpiar el input file
  const fileInput = document.querySelector('input[type="file"]')
  if (fileInput) fileInput.value = ''
}

const generateBarcode = () => {
  // Generar código de barras EAN-13 simulado
  const prefix = '789' // Código de país (ejemplo)
  const company = '4900' // Código de empresa (ejemplo)
  const random = Math.floor(Math.random() * 100000).toString().padStart(5, '0')
  const partial = prefix + company + random
  
  // Calcular dígito de verificación EAN-13
  let sum = 0
  for (let i = 0; i < partial.length; i++) {
    const digit = parseInt(partial[i])
    sum += i % 2 === 0 ? digit : digit * 3
  }
  const checkDigit = (10 - (sum % 10)) % 10
  
  productForm.value.barcode = partial + checkDigit
}

// Abrir modal de crear categoría
const openCategoryModal = () => {
  showNoCategoriesModal.value = false
  categoryForm.value = {
    name: '',
    description: '',
    icon: 'shopping-bag',
    color: '#3b82f6'
  }
  showCategoryModal.value = true
}

// Manejar cambio en select de categoría
const handleCategoryChange = (event) => {
  if (productForm.value.category_id === '__new__') {
    // Resetear el select
    productForm.value.category_id = ''
    // Abrir modal de crear categoría
    openCategoryModal()
  }
}

// Manejar cambio en select de proveedor
const handleSupplierChange = (event) => {
  if (productForm.value.supplier_id === '__new__') {
    // Resetear el select
    productForm.value.supplier_id = null
    // Abrir modal de crear proveedor
    showSupplierModal.value = true
  }
}

// Guardar nueva categoría
const saveCategory = async () => {
  try {
    loading.value = true
    
    const response = await categoriesService.create(categoryForm.value)
    
    if (response.success) {
      showNotification(
        'Categoría creada',
        'La categoría se ha creado exitosamente',
        'success'
      )
      
      // Recargar categorías
      await loadCategories()
      
      // Cerrar modal de categoría
      showCategoryModal.value = false
      
      // Seleccionar automáticamente la nueva categoría
      if (response.category?.id) {
        if (isFashionMode.value && fashionFormRef.value) {
          fashionFormRef.value.setCategory(response.category.id)
        } else {
          productForm.value.category_id = response.category.id
        }
      }
    } else {
      throw new Error(response.message || 'Error al crear categoría')
    }
  } catch (error) {
    console.error('Error creando categoría:', error)
    showNotification(
      'Error al crear categoría',
      error.message || 'No se pudo crear la categoría',
      'error'
    )
  } finally {
    loading.value = false
  }
}

// Guardar nuevo proveedor
const saveSupplier = async () => {
  try {
    loading.value = true
    
    const response = await apiClient.post('/suppliers', {
      ...supplierForm.value,
      active: true
    })
    
    if (response.data.success) {
      showNotification(
        'Proveedor creado',
        'El proveedor se ha creado exitosamente',
        'success'
      )
      
      // Recargar proveedores
      await loadSuppliers()
      
      // Cerrar modal
      showSupplierModal.value = false
      
      // Seleccionar automáticamente el nuevo proveedor
      if (response.data.supplier?.id) {
        productForm.value.supplier_id = response.data.supplier.id
      }
      
      // Limpiar formulario
      supplierForm.value = {
        name: '',
        document: '',
        contact_name: '',
        phone: '',
        email: ''
      }
    } else {
      throw new Error(response.data.message || 'Error al crear proveedor')
    }
  } catch (error) {
    console.error('Error creando proveedor:', error)
    showNotification(
      'Error al crear proveedor',
      error.response?.data?.message || error.message || 'No se pudo crear el proveedor',
      'error'
    )
  } finally {
    loading.value = false
  }
}

// Calcular stock total sumando todas las tiendas
const calculateTotalStock = () => {
  // Si hay solo 1 bodega, usar el campo stock directamente
  if (availableWarehouses.value.length === 1) {
    return parseInt(productForm.value.stock) || 0
  }
  
  // Si hay múltiples bodegas, sumar SOLO el stock de las sedes habilitadas
  if (!productForm.value.warehouseStock) return 0
  return Object.keys(productForm.value.warehouseStock).reduce((sum, warehouseId) => {
    // Solo contar si la sede está habilitada
    if (productForm.value.warehouseEnabled[warehouseId]) {
      return sum + (parseInt(productForm.value.warehouseStock[warehouseId]) || 0)
    }
    return sum
  }, 0)
}

// ⚠️ Validar si faltan datos importantes (especialmente stock)
const checkMissingImportantFields = () => {
  const missing = []
  const totalStock = calculateTotalStock()
  
  // Validar stock (campo más importante)
  if (totalStock === 0 || totalStock === null || totalStock === undefined) {
    missing.push({
      field: 'Stock Inicial',
      message: 'No has especificado cuántas unidades tienes en inventario',
      severity: 'high' // high = crítico, medium = importante, low = opcional
    })
  }
  
  // Validar stock mínimo (importante para alertas)
  if (!productForm.value.min_stock || productForm.value.min_stock === 0) {
    missing.push({
      field: 'Stock Mínimo',
      message: 'No recibirás alertas cuando el inventario esté bajo',
      severity: 'medium'
    })
  }
  
  // Validar descripción (recomendado pero no crítico)
  if (!productForm.value.description || productForm.value.description.trim() === '') {
    missing.push({
      field: 'Descripción',
      message: 'El producto no tiene descripción',
      severity: 'low'
    })
  }
  
  return missing
}

// 👗 Guardar producto tipo Ropa/Variantes
const saveFashionProduct = async () => {
  // Llamar al método handleSubmit del componente hijo
  if (fashionFormRef.value) {
    fashionFormRef.value.handleSubmit()
  }
}

const handleFashionSave = async (productData) => {
  try {
    loading.value = true
    showProductModal.value = false // Cerrar modal inmediatamente
    
    console.log('👗 Enviando producto fashion al backend:', productData)
    console.log('💰 Verificando costos:', {
      'productData.variants': productData.variants,
      'variant[0].cost': productData.variants?.[0]?.cost,
      'variant[0].cost_price': productData.variants?.[0]?.cost_price
    })

    // Detectar si hay imágenes nuevas (archivos)
    const hasNewImages = productData.images && productData.images.some(img => img.file)
    
    // Determinar si es producto simple
    const isSimpleProduct = productData.type === 'simple'
    
    let response
    
    if (hasNewImages) {
      // ===== USAR FORMDATA SI HAY IMÁGENES =====
      const formData = new FormData()
      formData.append('name', productData.name)
      formData.append('product_type', productData.type || 'variable')
      
      if (productData.category_id) {
        formData.append('category_id', productData.category_id)
      }
      if (productData.supplier_id) {
        formData.append('supplier_id', productData.supplier_id)
      }
      formData.append('description', productData.description || '')
      formData.append('sku', productData.sku || `SKU-${Date.now()}`)
      
      const firstVariant = productData.variants && productData.variants[0]
      formData.append('cost_price', firstVariant?.cost || 0)
      
      // ✅ AGREGAR sale_price y stock si es producto simple
      if (isSimpleProduct && firstVariant) {
        formData.append('sale_price', firstVariant.price || 0)
        formData.append('current_stock', firstVariant.stock || 0)
      }
      
      // ✅ AGREGAR store_category para recordar que fue creado como moda
      formData.append('store_category', 'fashion')
      
      // Options
      if (productData.options) {
        productData.options.forEach((opt, index) => {
          formData.append(`options[${index}][name]`, opt.name)
          if (opt.values) {
            opt.values.forEach((val, vIndex) => {
              formData.append(`options[${index}][values][${vIndex}]`, val)
            })
          }
        })
      }

      // Variants
      if (productData.variants) {
        productData.variants.forEach((variant, index) => {
          formData.append(`variants[${index}][sku]`, variant.sku)
          formData.append(`variants[${index}][price]`, variant.price)
          formData.append(`variants[${index}][cost_price]`, variant.cost || 0)
          formData.append(`variants[${index}][stock]`, variant.stock)
          formData.append(`variants[${index}][active]`, variant.active ? 1 : 0)
          
          // Variant options
          if (variant.options) {
            variant.options.forEach((opt, oIndex) => {
              formData.append(`variants[${index}][options][${oIndex}][name]`, opt.name)
              formData.append(`variants[${index}][options][${oIndex}][value]`, opt.value)
            })
          }
        })
      }

      // Images - ✅ Enviar como array para que PHP lo interprete correctamente
      if (productData.images && productData.images.length > 0) {
        productData.images.forEach((img) => {
          if (img.file) {
            formData.append(`images[]`, img.file) // ✅ Usar images[] en lugar de images[index]
          }
        })
      }

      // Detectar si es edición o creación
      if (isEditing.value && productForm.value.id) {
        formData.append('_method', 'PUT')
        response = await productsService.update(productForm.value.id, formData)
      } else {
        response = await productsService.create(formData)
      }
    } else {
      // ===== USAR JSON SI NO HAY IMÁGENES =====
      const payload = {
        name: productData.name,
        product_type: productData.type || 'variable',
        category_id: productData.category_id,
        supplier_id: productData.supplier_id,
        description: productData.description || '',
        sku: productData.sku || `SKU-${Date.now()}`,
        cost_price: productData.variants?.[0]?.cost || 0,
        store_category: 'fashion', // ✅ Recordar que fue creado como moda
        options: productData.options || [],
        variants: productData.variants || []
      }
      
      // ✅ AGREGAR sale_price y stock si es producto simple
      if (isSimpleProduct && productData.variants?.[0]) {
        payload.sale_price = productData.variants[0].price || 0
        payload.current_stock = productData.variants[0].stock || 0
      }
      
      // Detectar si es edición o creación
      if (isEditing.value && productForm.value.id) {
        response = await productsService.update(productForm.value.id, payload)
      } else {
        response = await productsService.create(payload)
      }
    }
    
    if (response.success) {
      showNotification(
        isEditing.value ? 'Producto actualizado' : 'Producto creado',
        isEditing.value 
          ? 'El producto se ha actualizado correctamente'
          : 'El producto se ha creado correctamente',
        'success'
      )
      await loadProducts() // Recargar lista
    } else {
      throw new Error(response.message || 'Error al guardar')
    }
  } catch (error) {
    console.error('Error saving fashion product:', error)
    showNotification(
      'Error',
      'No se pudo guardar el producto: ' + error.message,
      'error'
    )
    showProductModal.value = true // Reabrir modal si hay error
  } finally {
    loading.value = false
  }
}

const saveProduct = async (skipValidation = false) => {
  try {
    loading.value = true
    
    // Validar campos requeridos
    if (!productForm.value.name?.trim()) {
      loading.value = false
      throw new Error('El nombre del producto es requerido')
    }
    if (!productForm.value.category_id) {
      loading.value = false
      throw new Error('La categoría es requerida')
    }
    if (!productForm.value.cost || productForm.value.cost <= 0) {
      loading.value = false
      throw new Error('El precio de costo debe ser mayor a 0')
    }
    if (!productForm.value.price || productForm.value.price <= 0) {
      loading.value = false
      throw new Error('El precio de venta debe ser mayor a 0')
    }
    
    // ⚠️ Validar campos importantes y mostrar confirmación si faltan (solo en creación)
    if (!skipValidation && !isEditing.value) {
      const missing = checkMissingImportantFields()
      
      // Si hay campos críticos o importantes sin llenar, mostrar confirmación
      const hasCriticalMissing = missing.some(m => m.severity === 'high' || m.severity === 'medium')
      
      if (hasCriticalMissing) {
        missingFields.value = missing
        showStockWarningModal.value = true
        loading.value = false
        return // Detener el guardado y esperar confirmación del usuario
      }
    }
    
    // Calcular stock total
    const totalStock = calculateTotalStock()
    if (totalStock === undefined || totalStock === null || totalStock < 0) {
      throw new Error('El stock total debe ser un número válido (0 o mayor)')
    }
    
    // Debug: Verificar estado del stock antes de enviar
    console.log('📦 Stock en productForm.stock:', productForm.value.stock)
    console.log('📦 Stock en warehouseStock:', productForm.value.warehouseStock)
    console.log('✅ warehouseEnabled:', productForm.value.warehouseEnabled)
    console.log('📦 Total stock calculado:', totalStock)
    console.log('✅ Bodegas disponibles:', availableWarehouses.value.map(w => ({ id: w.id, name: w.name })))
    
    // Transformar los datos para que coincidan con los campos esperados por la API
    const apiData = {
      name: productForm.value.name.trim(),
      description: productForm.value.description?.trim() || '',
      product_type: 'simple', // ✅ Productos generales son SIEMPRE 'simple'
      store_type: 'general', // ✅ Marcar como producto general (tienda normal)
      sku: productForm.value.sku?.trim() || `SKU-${Date.now()}`, // Generar SKU automático si está vacío
      barcode: productForm.value.barcode?.trim() || '',
      category_id: parseInt(productForm.value.category_id),
      supplier_id: productForm.value.supplier_id ? parseInt(productForm.value.supplier_id) : null,
      cost_price: parseFloat(productForm.value.cost),
      sale_price: parseFloat(productForm.value.price),
      wholesale_price: null,
      current_stock: totalStock, // 🏢 Stock total calculado de todas las tiendas
      min_stock: productForm.value.min_stock ? parseInt(productForm.value.min_stock) : 0,
      max_stock: productForm.value.max_stock ? parseInt(productForm.value.max_stock) : totalStock * 3,
      unit: productForm.value.unit?.trim() || 'unidad',
      manage_stock: true,
      active: productForm.value.active !== false,
      // Manejo inteligente de imágenes: URLs normales o base64 (archivos subidos)
      image_url: productForm.value.image ? productForm.value.image.trim() : null,
      tags: null,
      // 🏢 Stock por cada tienda - SOLO enviar las sedes habilitadas (con checkbox marcado)
      warehouse_stocks: Object.keys(productForm.value.warehouseStock || {}).reduce((acc, warehouseId) => {
        // Solo incluir la sede si está habilitada en el checkbox
        if (productForm.value.warehouseEnabled[warehouseId]) {
          acc[warehouseId] = productForm.value.warehouseStock[warehouseId]
          console.log(`✅ Incluyendo sede ${warehouseId} con stock ${productForm.value.warehouseStock[warehouseId]}`)
        } else {
          console.log(`❌ Excluyendo sede ${warehouseId} (checkbox no marcado)`)
        }
        return acc
      }, {}),
      // 📏 Unidades de Medida (NUEVO)
      measurement_unit: productForm.value.measurement_unit || 'unit',
      allow_decimal: productForm.value.allow_decimal || false,
      // ⚠️ NO ENVIAR 'variants' para productos simples - esto causa que el backend cree variantes innecesarias
    }

    console.log('📦 Datos enviados a la API:', apiData)
    console.log('🏭 warehouse_stocks FILTRADO que se envía:', JSON.stringify(apiData.warehouse_stocks, null, 2))
    console.log('🔢 Cantidad de sedes en warehouse_stocks:', Object.keys(apiData.warehouse_stocks).length)
    
    if (isEditing.value) {
      await productsService.update(productForm.value.id, apiData)
      showNotification(
        'Producto actualizado',
        `El producto "${apiData.name}" se ha actualizado exitosamente`,
        'success'
      )
    } else {
      await productsService.create(apiData)
      showNotification(
        'Producto creado',
        `El producto "${apiData.name}" se ha creado exitosamente`,
        'success'
      )
    }
    
    await loadProducts()
    showProductModal.value = false
  } catch (error) {
    console.error('Error guardando producto:', error)
    showNotification(
      'Error al guardar',
      'No se pudo guardar el producto: ' + (error.message || 'Error desconocido'),
      'error'
    )
  } finally {
    loading.value = false
  }
}

// Watchers para guardar preferencias automáticamente
watch(itemsPerPage, (newValue, oldValue) => {
  if (oldValue !== undefined && newValue !== oldValue) {
    saveUserPreferences()
  }
}, { immediate: false })

watch(sortBy, (newValue, oldValue) => {
  if (oldValue !== undefined && newValue !== oldValue) {
    saveUserPreferences()
  }
}, { immediate: false })

// Watchers para resetear paginación cuando cambien los filtros
watch([searchTerm, categoryFilter, statusFilter, sortBy], () => {
  currentPage.value = 1
})

// 🏢 Sincronizar stock con bodega única cuando hay solo 1 bodega disponible
watch(() => productForm.value.stock, (newStock) => {
  // Usar availableWarehouses que ya filtra según el plan
  if (availableWarehouses.value.length === 1 && availableWarehouses.value[0]) {
    const warehouseId = availableWarehouses.value[0].id
    
    // Asegurar que warehouseStock existe
    if (!productForm.value.warehouseStock) {
      productForm.value.warehouseStock = {}
    }
    
    // Sincronizar el stock
    productForm.value.warehouseStock[warehouseId] = parseInt(newStock) || 0
    console.log('✅ Stock sincronizado con bodega única:', warehouseId, '=', newStock, 'Total bodegas disponibles:', availableWarehouses.value.length)
  }
})

watch(statusFilter, async () => {
  console.log('Filtro de estado cambió:', statusFilter.value)
  await loadProducts()
})

// 🎯 Watcher para query params de navegación AI (filtros automáticos)
watch(() => props.queryParams, async (newParams) => {
  if (!newParams || Object.keys(newParams).length === 0) return
  
  console.log('🔍 [ProductsView] Query params detectados:', newParams)
  
  // Aplicar filtro según queryParams
  if (newParams.filter) {
    switch(newParams.filter) {
      case 'inactive':
        statusFilter.value = 'inactive'
        console.log('✅ [ProductsView] Filtro aplicado: Productos Inactivos')
        showNotification(
          'Filtro aplicado',
          'Mostrando solo productos inactivos',
          'info'
        )
        break
      case 'active':
        statusFilter.value = 'active'
        console.log('✅ [ProductsView] Filtro aplicado: Productos Activos')
        break
      case 'low-stock':
        statusFilter.value = 'low-stock'
        console.log('✅ [ProductsView] Filtro aplicado: Productos con Stock Bajo')
        showNotification(
          'Filtro aplicado',
          'Mostrando productos con stock bajo',
          'warning'
        )
        break
    }
  }
  
  // Si hay filtro de categoría (ej: category:Alimentos)
  if (newParams.filter && newParams.filter.startsWith('category:')) {
    const categoryName = newParams.filter.replace('category:', '')
    const category = categories.value.find(c => c.name.toLowerCase() === categoryName.toLowerCase())
    if (category) {
      categoryFilter.value = category.id
      console.log('✅ [ProductsView] Filtro de categoría aplicado:', categoryName)
      showNotification(
        'Categoría seleccionada',
        `Mostrando productos de ${categoryName}`,
        'info'
      )
    }
  }

  // 🔍 Manejar acciones (create/edit) desde props (AI Navigation)
  if (newParams.action === 'create') {
    console.log('✅ [ProductsView] Acción de creación detectada desde Props');
    setTimeout(() => openCreateModal(), 500);
  } else if (newParams.action === 'edit' && newParams.id) {
    console.log('✅ [ProductsView] Acción de edición detectada desde Props');
    // Asegurar que los productos estén cargados
    if (products.value.length === 0) await loadProducts();
    
    const productToEdit = products.value.find(p => p.id == newParams.id);
    if (productToEdit) {
      console.log('🚀 [ProductsView] Abriendo modal de edición para:', productToEdit.name);
      editProduct(productToEdit);
    }
  }
}, { deep: true, immediate: true })

// 🔄 Configurar listener de eventos global ANTES de onMounted (para evitar warning)
const handleProductsUpdate = (event) => {
  console.log('🔄 Evento products-updated recibido en ProductsView:', event.detail)
  console.log('🔄 Recargando productos para sincronizar stock...')
  // Recargar productos automáticamente cuando se reciba el evento
  loadProducts()
}

// 👁️ Watch: Limpiar cambios al cerrar modal de inventario
watch(showViewModal, (newValue) => {
  if (!newValue) {
    // Modal cerrado - limpiar cambios pendientes
    variantChanges.value = {}
  }
})

// Inicialización
onMounted(async () => {
  console.log('Módulo de productos inicializado')
  
  // 🎯 PRIMERO: Detectar tipo de tienda ANTES de renderizar para evitar parpadeo
  const storeType = appStore.systemSettings?.store_type || 'general'
  isFashionMode.value = storeType === 'fashion' || storeType === 'moda'
  console.log('🏪 Tipo de tienda detectado:', storeType, '| Fashion Mode:', isFashionMode.value)
  
  // 🔧 Cargar preferencias del usuario primero (PROBLEMA 1: Restaurar vista guardada)
  loadUserPreferences()
  
  // 🎨 VISTA POR DEFECTO INTELIGENTE: Grid para Fashion, Table para Retail
  // Solo establecer si no hay preferencia guardada
  if (!localStorage.getItem(USER_PREFERENCES_KEY)) {
    viewMode.value = isFashionStore.value ? 'grid' : 'table'
    console.log('📊 Vista por defecto establecida:', viewMode.value, '(basada en tipo de tienda:', appStore.systemSettings?.store_type, ')')
  } else {
    console.log('✅ Vista restaurada desde preferencias:', viewMode.value)
  }
  
  // Registrar listener ANTES del primer await
  window.addEventListener('products-updated', handleProductsUpdate)
  
  await loadCategories()
  await loadSuppliers() // 🏭 Cargar proveedores activos
  await loadWarehouses() // 🏢 Cargar bodegas disponibles
  
  // ✅ Inicialización completa - permitir renderizado
  isInitializing.value = false
  
  // 🎓 Mostrar tour si es primera visita
  if (isFirstVisitProducts.value) {
    await nextTick()
    setTimeout(() => {
      showProductsWelcomeModal.value = true
    }, 800)
  }
  
  // Verificar si hay acción de creación desde la URL (Deep Linking) O desde props
  const action = route.query.action || props.queryParams?.action;
  const actionId = route.query.id || props.queryParams?.id;

  console.log('🔍 [ProductsView] route.query:', route.query);
  console.log('🔍 [ProductsView] props.queryParams:', props.queryParams);
  
  if (action === 'create') {
    console.log('✅ [ProductsView] Acción de creación detectada');
    // Esperar un momento para asegurar que las categorías estén cargadas
    setTimeout(() => {
      console.log('🚀 [ProductsView] Llamando a openCreateModal()');
      openCreateModal()
      // Limpiar la query para evitar que se abra al recargar
      if (route.query.action) router.replace({ query: null })
    }, 500)
  } else if (action === 'edit' && actionId) {
    console.log('✅ [ProductsView] Acción de edición detectada');
    // Esperar a que carguen los productos
    await loadProducts()
    
    const productToEdit = products.value.find(p => p.id == actionId)
    if (productToEdit) {
      console.log('🚀 [ProductsView] Abriendo modal de edición para:', productToEdit.name)
      editProduct(productToEdit)
      // Limpiar la query
      if (route.query.action) router.replace({ query: null })
    } else {
      console.warn('⚠️ [ProductsView] Producto no encontrado para edición:', actionId)
    }
  } else {
    console.log('ℹ️ [ProductsView] No hay acción de creación en la URL/Props');
    // Verificar si hay datos de producto para editar desde otra vista (solo si no es creación)
    const editProductData = sessionStorage.getItem('editProductData')
    if (editProductData) {
      try {
        const productData = JSON.parse(editProductData)
        console.log('Producto detectado para edición inmediata:', productData)
        
        // Configurar el producto en productForm directamente sin esperar a cargar todos los productos
        productForm.value = {
          id: productData.id,
          name: productData.name,
          sku: productData.sku || '',
          barcode: productData.barcode || '',
          description: productData.description || '',
          price: parseFloat(productData.price || 0),
          cost: parseFloat(productData.cost || 0),
          stock: parseInt(productData.stock || 0),
          min_stock: parseInt(productData.min_stock || 5),
          max_stock: parseInt(productData.max_stock || 100),
          category_id: productData.category_id,
          image: productData.image || '',
          active: productData.active !== false
        }
        
        // Abrir el modal inmediatamente
        isEditing.value = true
        showProductModal.value = true
        
        // Configurar estado de imágenes para edición
        previewImage.value = null
        imageLoadError.value = false
        
        // Detectar si es URL o archivo base64
        if (productForm.value.image) {
          if (productForm.value.image.startsWith('data:')) {
            imageUploadMethod.value = 'file'
            previewImage.value = productForm.value.image
          } else {
            imageUploadMethod.value = 'url'
          }
        } else {
          imageUploadMethod.value = 'url'
        }
        
        // Limpiar sessionStorage
        sessionStorage.removeItem('editProductData')
        
        // Cargar productos en background sin bloquear la UI
        loadProducts()
        
      } catch (error) {
        console.error('Error al parsear datos del producto:', error)
        await loadProducts()
      }
    } else {
      // Cargar productos normalmente
      await loadProducts()
    }
  }
})

// Limpiar listener al desmontar componente
onUnmounted(() => {
  window.removeEventListener('products-updated', handleProductsUpdate)
})
</script>

<style scoped>
/* Transiciones suaves */
* {
  transition: all 0.2s ease-in-out;
}
</style>