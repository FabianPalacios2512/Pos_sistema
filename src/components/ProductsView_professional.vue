<template>
  <!-- Loading State durante inicialización (evita parpadeo) -->
  <div v-if="isInitializing" class="min-h-screen font-sans bg-gray-50 dark:bg-[#131314] transition-colors duration-300 px-8">
    <div class="flex items-center justify-center min-h-screen">
      <div class="flex flex-col items-center space-y-4">
        <div class="w-16 h-16 border-4 border-slate-200 dark:border-zinc-700 border-t-slate-900 dark:border-t-slate-500 rounded-full animate-spin"></div>
        <p class="text-base text-gray-600 dark:text-zinc-400 font-medium">Cargando productos...</p>
      </div>
    </div>
  </div>
  
  <!-- Contenido principal (solo cuando la inicialización está completa) -->
  <div v-else class="min-h-screen font-sans bg-gray-50 dark:bg-[#131314] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header - Condicional Fashion/Standard -->
      <div class="flex items-center justify-between" :class="isFashionStore ? 'pb-6' : 'pb-4'">
        <!-- Título Fashion (Tipografía elegante) -->
        <div v-if="isFashionStore">
          <h1 class="text-3xl font-light text-gray-900 dark:text-white tracking-tight">Colección</h1>
          <p class="text-base text-gray-500 dark:text-zinc-400 mt-1 font-light">Explora nuestros productos</p>
        </div>
        <!-- Título Standard -->
        <div v-else>
          <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Productos</h1>
          <p class="text-base text-gray-600 dark:text-zinc-400 mt-1 font-normal">Gestiona tu inventario y catalogo</p>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Importar Excel (NUEVO) -->
          <button id="tour-import-excel" @click="showExcelImportModal = true"
                  class="px-4 py-2 bg-white dark:bg-zinc-900/60 hover:bg-gray-50 dark:hover:bg-zinc-800/60 text-gray-600 dark:text-zinc-300 text-[15px] font-medium rounded-lg border border-gray-200 dark:border-zinc-700/60 transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <span>Importar Excel</span>
          </button>
          
          <!-- Botón Neutro (Exportar) -->
          <button id="tour-export-products" @click="exportProducts"
                  class="px-4 py-2 bg-white dark:bg-zinc-900/60 hover:bg-gray-50 dark:hover:bg-zinc-800/60 text-gray-600 dark:text-zinc-300 text-[15px] font-medium rounded-lg border border-gray-200 dark:border-zinc-700/60 transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar</span>
          </button>
          
          <!-- Botón Secundario (Actualizar) -->
          <button @click="refreshProducts"
                  :disabled="loading"
                  class="px-4 py-2 bg-white dark:bg-zinc-900/60 hover:bg-gray-50 dark:hover:bg-zinc-800/60 text-gray-600 dark:text-zinc-300 text-[15px] font-medium rounded-lg border border-gray-200 dark:border-zinc-700/60 transition-all duration-200 flex items-center gap-2"
                  :class="{ 'opacity-50 cursor-not-allowed': loading }">
            <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>{{ loading ? 'Sincronizando' : 'Actualizar' }}</span>
          </button>
          
          <!-- Bot�n Principal (Nuevo Producto) -->
          <button id="tour-new-product"
                  @click="openCreateModal"
                  class="px-5 py-2 bg-gray-900 dark:bg-white/10 hover:bg-black dark:hover:bg-white/15 text-white text-[15px] font-semibold rounded-lg shadow-sm transition-all duration-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nuevo Producto</span>
          </button>
        </div>
      </div>

      <!-- M�tricas Principales - Condicional Fashion/Standard -->
      <div v-if="isFashionStore" class="flex items-center gap-10 mb-6 flex-wrap">
        <!-- KPIs Minimalistas Fashion (Sin iconos) -->
        <div>
          <p class="text-sm font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-0.5">Total</p>
          <p class="text-2xl font-light text-gray-900 dark:text-white">{{ products.length }}</p>
        </div>
        <div class="w-px h-8 bg-gray-200 dark:bg-zinc-800 hidden md:block"></div>
        <div>
          <p class="text-sm font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-0.5">Activos</p>
          <p class="text-2xl font-light text-gray-900 dark:text-white">{{ activeProducts }}</p>
        </div>
        <div class="w-px h-8 bg-gray-200 dark:bg-zinc-800 hidden md:block"></div>
        <div>
          <p class="text-sm font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-0.5">Stock Bajo</p>
          <p class="text-2xl font-light text-gray-900 dark:text-white">{{ lowStockProducts }}</p>
        </div>
        <div class="w-px h-8 bg-gray-200 dark:bg-zinc-800 hidden md:block"></div>
        <div>
          <p class="text-sm font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-0.5">Valor Inventario</p>
          <p class="text-2xl font-light text-gray-900 dark:text-white">${{ formatCurrency(totalValue) }}</p>
        </div>
      </div>
      
      <!-- KPIs Standard - Inline metrics (Stripe-style) -->
      <div v-else class="flex items-center gap-8 mb-2 flex-wrap">
        <!-- Total Productos -->
        <div class="flex items-center gap-2.5 group">
          <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 bg-blue-50 dark:bg-blue-500/10">
            <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
          </div>
          <div>
            <p class="text-[13px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wider leading-none">Total Productos</p>
            <p class="text-base font-semibold text-[#374151] dark:text-zinc-300 leading-tight">{{ products.length }}</p>
          </div>
        </div>

        <div class="w-px h-8 bg-gray-200 dark:bg-zinc-800 hidden md:block"></div>

        <!-- Productos Activos -->
        <div class="flex items-center gap-2.5 group">
          <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 bg-emerald-50 dark:bg-emerald-500/10">
            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div>
            <p class="text-[13px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wider leading-none">Activos</p>
            <p class="text-base font-semibold text-[#374151] dark:text-zinc-300 leading-tight">{{ activeProducts }}</p>
          </div>
        </div>

        <div class="w-px h-8 bg-gray-200 dark:bg-zinc-800 hidden md:block"></div>

        <!-- Stock Bajo -->
        <div class="flex items-center gap-2.5 group">
          <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 bg-amber-50 dark:bg-amber-500/10">
            <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
          <div>
            <p class="text-[13px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wider leading-none">Stock Bajo</p>
            <p class="text-base font-semibold text-[#374151] dark:text-zinc-300 leading-tight">{{ lowStockProducts }}</p>
          </div>
        </div>

        <div class="w-px h-8 bg-gray-200 dark:bg-zinc-800 hidden md:block"></div>

        <!-- Valor Total -->
        <div class="flex items-center gap-2.5 group">
          <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 bg-blue-50 dark:bg-blue-500/10">
            <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <div>
            <p class="text-[13px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wider leading-none">Valor Total</p>
            <p class="text-base font-semibold text-[#374151] dark:text-zinc-300 leading-tight">${{ formatCurrency(totalValue) }}</p>
          </div>
        </div>

        <div class="w-px h-8 bg-gray-200 dark:bg-zinc-800 hidden md:block"></div>

        <!-- Categorías -->
        <div class="flex items-center gap-2.5 group">
          <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 bg-indigo-50 dark:bg-indigo-500/10">
            <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
          <div>
            <p class="text-[13px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wider leading-none">Categorías</p>
            <p class="text-base font-semibold text-[#374151] dark:text-zinc-300 leading-tight">{{ uniqueCategories }}</p>
          </div>
        </div>
      </div>

      <!-- Contenedor Principal Tour - Filtros + Productos -->
      <div id="tour-products-main">
      <!-- Filtros - Sin card contenedora, directamente en el layout -->
      <div class="flex flex-wrap items-center gap-3 mb-5">
        <!-- B�squeda -->
        <div id="tour-search-products" class="flex-1 min-w-[220px] relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar productos por nombre, SKU..."
            class="w-full pl-9 pr-4 py-2.5 text-[15px] rounded-lg border border-gray-200 dark:border-zinc-700/60 bg-white dark:bg-zinc-900/60 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200">
        </div>
        
        <!-- Categor�a -->
        <div id="tour-filter-category">
          <select
            v-model="categoryFilter"
            class="px-3 py-2.5 text-[15px] rounded-lg border border-gray-200 dark:border-zinc-700/60 bg-white dark:bg-zinc-900/60 text-gray-600 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 cursor-pointer">
            <option value="">Todas las categorías</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
          </select>
        </div>
        
        <!-- Estado -->
        <div id="tour-filter-status">
          <select
            v-model="statusFilter"
            class="px-3 py-2.5 text-[15px] rounded-lg border border-gray-200 dark:border-zinc-700/60 bg-white dark:bg-zinc-900/60 text-gray-600 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 cursor-pointer">
            <option value="">Todos los estados</option>
            <option value="active">Activos</option>
            <option value="inactive">Inactivos</option>
            <option value="low-stock">Stock Bajo</option>
          </select>
        </div>
        
        <!-- Ordenar -->
        <div>
          <select
            v-model="sortBy"
            class="px-3 py-2.5 text-[15px] rounded-lg border border-gray-200 dark:border-zinc-700/60 bg-white dark:bg-zinc-900/60 text-gray-600 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 cursor-pointer">
            <option value="name">Por Nombre</option>
            <option value="price">Por Precio</option>
            <option value="stock">Por Stock</option>
            <option value="created_at">Por Fecha</option>
          </select>
        </div>
        
        <!-- Toggle Vista -->
        <div id="tour-view-toggle" class="flex items-center bg-gray-100/60 dark:bg-zinc-800/40 rounded-lg p-0.5 h-[38px]">
          <button
            @click="setViewMode('grid')"
            :class="[
              'flex items-center justify-center px-3 py-1.5 rounded-md transition-all text-sm font-medium',
              viewMode === 'grid' 
                ? 'bg-white dark:bg-zinc-700/60 text-gray-900 dark:text-white shadow-sm' 
                : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
            ]">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            Tarjetas
          </button>
          
          <button
            @click="setViewMode('table')"
            :class="[
              'flex items-center justify-center px-3 py-1.5 rounded-md transition-all text-sm font-medium',
              viewMode === 'table' 
                ? 'bg-white dark:bg-zinc-700/60 text-gray-900 dark:text-white shadow-sm' 
                : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
            ]">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            Tabla
          </button>
        </div>
      </div>

    <!-- Vista de Tarjetas -->
    <div v-if="viewMode === 'grid'">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="flex flex-col items-center space-y-3">
          <div class="w-12 h-12 border-4 border-blue-200 dark:border-zinc-700 border-t-blue-600 dark:border-t-blue-500 rounded-full animate-spin"></div>
          <p class="text-[15px] text-gray-500 dark:text-zinc-400">Cargando productos...</p>
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
            <p class="text-base text-gray-500 dark:text-zinc-400 leading-relaxed mb-1">
              {{ searchTerm ? 'No se encontraron productos que coincidan con tu búsqueda' : 'Comienza agregando productos para gestionar tu catálogo' }}
            </p>
            <p v-if="!searchTerm" class="text-sm text-gray-400 dark:text-zinc-500">Puedes agregar productos manualmente o importar desde Excel</p>
          </div>
          
          <button v-if="!searchTerm" 
                  @click="openCreateModal" 
                  class="mt-6 px-6 py-3 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white rounded-xl text-[15px] font-bold  transition-all duration-300 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Agregar Primer Producto</span>
          </button>
        </div>
      </div>

      <!-- Grid de productos -->
      <div v-else class="grid" :class="isFashionStore ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-8' : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-6'">
        
        <!-- �x FASHION CARD - Para tiendas de MODA (estilo Lookbook) -->
        <template v-if="isFashionStore">
          <FashionProductCard
            v-for="product in paginatedProducts"
            :key="product.id"
            :product="product"
            @view="viewProduct"
            @edit="editProduct"
          />
        </template>
        
        <!-- �x: RETAIL CARD - Diseño Gemini minimalista -->
        <template v-else>
          <div v-for="product in paginatedProducts" 
               :key="product.id" 
               @click="viewProduct(product)"
               class="bg-white dark:bg-[#1e1f20] rounded-2xl hover:bg-gray-50 dark:hover:bg-[#282a2c] transition-all duration-200 overflow-hidden group cursor-pointer">
          
            <!-- Imagen del producto con overlay en hover -->
            <div class="relative aspect-[4/3] bg-gradient-to-br from-gray-100 to-gray-50 dark:from-[#1e1f20] dark:to-[#282a2c] overflow-hidden">
              <!-- Si hay imagen real, mostrarla -->
              <img v-if="getProductImage(product)"
                   :src="getProductImage(product)" 
                   :alt="product.name" 
                   @error="(e) => handleImageError(e, product)"
                   class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
              <!-- Placeholder elegante cuando NO hay imagen -->
              <div v-else class="w-full h-full flex items-center justify-center">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-16 h-16 rounded-2xl bg-gray-200/80 dark:bg-[#3a3a3f] flex items-center justify-center mb-2">
                    <svg class="w-8 h-8 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-15.75 0l1.5-1.875A2.625 2.625 0 018.25 4.5h7.5a2.625 2.625 0 012.25 1.125l1.5 1.875"/>
                    </svg>
                  </div>
                  <span class="text-[13px] font-medium text-gray-400 dark:text-zinc-500">Sin imagen</span>
                </div>
              </div>
              
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
                <button @click.stop="deleteProduct(product)" 
                        class="w-9 h-9 rounded-full bg-white/95 hover:bg-rose-500 text-gray-700 hover:text-white flex items-center justify-center shadow-lg transform scale-90 group-hover:scale-100 transition-all duration-200 delay-150"
                        title="Eliminar">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
              
              <!-- Badge Estado - Esquina superior derecha -->
              <div class="absolute top-3 right-3">
                <span :class="getProductStatus(product) 
                  ? 'bg-[#1e8e3e] text-white' 
                  : 'bg-[#5f6368] text-white'" 
                      class="px-2.5 py-1 rounded-full text-[13px] font-medium uppercase tracking-wide">
                  {{ getProductStatus(product) ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
              
              <!-- Badge Stock Bajo - Esquina superior izquierda -->
              <div v-if="(product.current_stock || 0) <= (product.min_stock || 0)" class="absolute top-3 left-3">
                <span class="bg-[#f9ab00] text-white px-2.5 py-1 rounded-full text-[13px] font-medium uppercase tracking-wide">
                  Stock Bajo
                </span>
              </div>
            </div>

            <!-- Información del producto - Diseño Gemini limpio -->
            <div class="p-4">
              <!-- Nombre -->
              <h3 class="font-medium text-[#1e1f20] dark:text-[#e3e3e3] text-base leading-snug line-clamp-1 mb-1" :title="product.name">
                {{ product.name }}
              </h3>
              
              <!-- Categoría -->
              <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mb-3">
                {{ product.category?.name || 'Sin categoría' }}
              </p>
              
              <!-- Precio y Stock en línea -->
              <div class="flex items-center justify-between">
                <span class="font-semibold text-[#1e1f20] dark:text-[#e3e3e3] text-lg">
                  ${{ formatCurrency(product.sale_price) }}
                </span>
                <span :class="[
                  'text-sm font-medium px-2.5 py-1 rounded-full',
                  (product.current_stock || 0) <= (product.min_stock || 0) 
                    ? 'bg-[#fef7e0] dark:bg-[#f9ab00]/20 text-[#e37400] dark:text-[#f9ab00]' 
                    : 'bg-gray-50 dark:bg-[#282a2c] text-[#5f6368] dark:text-[#9aa0a6]'
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

    <!-- Vista de Tabla - Integrada al layout, sin card pesada -->
  <div v-else>
      <!-- Header de tabla - Inline, sin contenedor -->
      <div class="flex items-center justify-between mb-3">
        <div>
          <h2 class="text-[15px] font-semibold text-[#111827] dark:text-white">Catalogo de Productos</h2>
          <p class="text-[13px] text-[#6B7280] dark:text-zinc-500 mt-0.5">{{ totalItems }} productos encontrados</p>
        </div>
      </div>
      
      <!-- Tabla con borde sutil -->
      <div class="mt-4 bg-white dark:bg-zinc-900 rounded-[10px] overflow-hidden border border-[#E5E7EB] dark:border-zinc-800">
      <table class="min-w-full">
        <thead>
          <tr class="bg-gray-50 dark:bg-zinc-900/80 border-b border-[#E5E7EB] dark:border-zinc-800">
            <th class="px-6 py-3.5 text-left text-[13px] font-semibold text-[#374151] dark:text-zinc-400 uppercase tracking-[0.04em]">
              Producto
            </th>
            <th class="px-6 py-3.5 text-left text-[13px] font-semibold text-[#374151] dark:text-zinc-400 uppercase tracking-[0.04em]">
              Categoría
            </th>
            <th class="px-6 py-3.5 text-left text-[13px] font-semibold text-[#374151] dark:text-zinc-400 uppercase tracking-[0.04em]">
              Proveedor
            </th>
            <th class="px-6 py-3.5 text-right text-[13px] font-semibold text-[#374151] dark:text-zinc-400 uppercase tracking-[0.04em]">
              Precio
            </th>
            
            <!-- Columnas de Stock Dinámicas -->
            <template v-if="showMultipleStockColumns">
              <th v-for="warehouse in availableWarehouses" :key="warehouse.id"
                  class="px-4 py-3.5 text-center text-[13px] font-semibold text-[#374151] dark:text-zinc-400 uppercase tracking-[0.04em]">
                <div class="flex items-center justify-center space-x-1">
                  <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  <span>{{ warehouse.name }}</span>
                </div>
              </th>
            </template>
            <template v-else>
              <th class="px-6 py-3.5 text-center text-[13px] font-semibold text-[#374151] dark:text-zinc-400 uppercase tracking-[0.04em]">
                Stock
              </th>
            </template>
            
            <th class="px-6 py-3.5 text-center text-[13px] font-semibold text-[#374151] dark:text-zinc-400 uppercase tracking-[0.04em]">
              Estado
            </th>
            <th class="px-6 py-3.5 text-center text-[13px] font-semibold text-[#374151] dark:text-zinc-400 uppercase tracking-[0.04em]">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-[#E5E7EB] dark:divide-zinc-800">
          <!-- Loading State -->
          <tr v-if="loading">
            <td :colspan="6 + availableWarehouses.length" class="px-4 py-12 text-center">
              <div class="flex flex-col items-center space-y-3">
                <div class="w-8 h-8 border-4 border-gray-200 dark:border-zinc-700 border-t-blue-600 dark:border-t-blue-400 rounded-full animate-spin"></div>
                <p class="text-[15px] text-gray-500 dark:text-zinc-400 font-medium">Cargando productos...</p>
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
                  <p class="text-base text-gray-500 dark:text-zinc-400 leading-relaxed">
                    {{ searchTerm ? 'No se encontraron productos que coincidan con tu búsqueda' : 'Comienza agregando productos para gestionar tu catálogo' }}
                  </p>
                </div>
                
                <button v-if="!searchTerm" 
                        @click="openCreateModal" 
                        class="mt-5 px-6 py-3 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white rounded-xl text-[15px] font-bold  transition-all duration-300 inline-flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  <span>Agregar Primer Producto</span>
                </button>
              </div>
            </td>
          </tr>

          <!-- Product Rows -->
          <template v-for="(product, index) in paginatedProducts" :key="product.id">
            <tr @click="viewProduct(product)"
                :class="[index % 2 === 1 ? 'bg-[#FAFAFA] dark:bg-zinc-900/60' : 'bg-white dark:bg-zinc-900', expandedProducts.has(product.id) ? 'border-b-0' : 'border-b border-[#E5E7EB] dark:border-zinc-800']"
                class="hover:bg-[#F1F5F9] dark:hover:bg-zinc-800/50 transition-colors duration-150 group cursor-pointer">
              <!-- Columna Producto (La Estrella) -->
            <td class="px-6 py-[16px]">
              <div class="flex items-center gap-4">
                <!-- Botón de expandir variantes -->
                <button v-if="hasVariants(product)"
                        @click.stop="toggleProduct(product.id)"
                        class="p-1 rounded-md text-gray-400 hover:text-blue-500 hover:bg-blue-50 dark:text-zinc-500 dark:hover:text-blue-400 dark:hover:bg-blue-900/20 transition-all focus:outline-none"
                        title="Ver variantes">
                  <svg v-if="expandedProducts.has(product.id)" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                  <svg v-else class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
                <div v-else class="w-7"></div> <!-- Espaciador si no hay botón -->

                <div class="relative w-10 h-10 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 dark:bg-zinc-800/60" 
                     :class="!getProductImage(product) ? 'flex items-center justify-center' : ''">
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
                    <div class="text-[16px] font-semibold text-[#111827] dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">{{ product.name }}</div>
                    <!-- Badge "Producto con Variantes" -->
                    <span v-if="hasVariants(product)" 
                          class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 rounded-full text-[13px] font-bold whitespace-nowrap border border-purple-100 dark:border-purple-800">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                      </svg>
                      {{ getVariantsCount(product) }} Variantes
                    </span>
                  </div>
                  <div class="text-[13px] text-[#6B7280] dark:text-zinc-400 font-mono">
                    {{ hasVariants(product) ? 'Múltiples SKUs' : (product.sku || 'SIN SKU') }}
                  </div>
                </div>
              </div>
            </td>
            <!-- Columna Categoría (Pills Gemini) -->
            <td class="px-6 py-[16px]">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[14px] bg-gray-100/80 dark:bg-zinc-800/60 text-[#374151] dark:text-zinc-300 font-medium">
                {{ product.category?.name || 'Sin categoría' }}
              </span>
            </td>
            <!-- Columna Proveedor -->
            <td class="px-6 py-[16px]">
              <span v-if="product.supplier?.name" class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[14px] bg-blue-50/80 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 font-medium">
                {{ product.supplier.name }}
              </span>
              <span v-else class="text-[13px] text-[#6B7280] dark:text-zinc-500">
                Sin proveedor
              </span>
            </td>
            <!-- Columna Precio (Con Rango para Variantes) -->
            <td class="px-6 py-[16px] text-right">
              <div class="text-[17px] font-mono font-bold text-[#111827] dark:text-white tabular-nums">
                {{ getPriceRange(product) }}
              </div>
            </td>
            <!-- Columnas de Stock Dinámicas -->
            <template v-if="showMultipleStockColumns">
              <td v-for="warehouse in availableWarehouses" :key="warehouse.id"
                  class="px-4 py-[16px] text-center">
                <div v-if="getWarehouseStock(product, warehouse.id) !== null" class="flex flex-col items-center relative group/stock-wh" :title="hasVariants(product) ? getVariantWarehouseStockAnalysis(product, warehouse.id)?.tooltipText : null">
                  <!-- �S& Producto EXISTE en esta sede -->
                  <span :class="[
                    'text-[14px] font-mono font-bold tabular-nums text-gray-900 dark:text-gray-200'
                  ]">
                    {{ getWarehouseStock(product, warehouse.id) }}
                  </span>
                  <!-- Alerta de stock bajo para esta bodega -->
                  <template v-if="hasVariants(product)">
                    <div v-if="getVariantWarehouseStockAnalysis(product, warehouse.id)?.status === 'critical'" class="mt-1.5 flex items-center gap-1 text-xs font-bold text-rose-600 dark:text-rose-400">
                      <span class="text-xs leading-none">✖</span>
                      <span>{{ getVariantWarehouseStockAnalysis(product, warehouse.id).message }}</span>
                    </div>
                    <div v-else-if="getVariantWarehouseStockAnalysis(product, warehouse.id)?.status === 'low'" class="mt-1.5 flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400">
                      <span class="text-xs leading-none">⚠</span>
                      <span>{{ getVariantWarehouseStockAnalysis(product, warehouse.id).message }}</span>
                    </div>
                    <div v-else class="mt-1 flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-500">
                      <span>✓ Todas OK</span>
                    </div>
                  </template>
                  <template v-else>
                     <span v-if="getWarehouseStock(product, warehouse.id) === 0" class="text-xs font-bold text-rose-600 dark:text-rose-400 mt-1.5">
                       ✖ Sin stock
                     </span>
                     <span v-else-if="getWarehouseStock(product, warehouse.id) <= (product.min_stock || 0)" class="text-xs font-bold text-amber-600 dark:text-amber-400 mt-1.5">
                       ⚠ Bajo
                     </span>
                  </template>
                </div>
                <div v-else class="flex flex-col items-center">
                  <!-- �R Producto NO existe en esta sede -->
                  <span class="text-[15px] font-medium text-gray-500 dark:text-zinc-600 italic">
                    N/A
                  </span>
                  <span class="text-[13px] text-gray-500 dark:text-zinc-600">
                    No aplica
                  </span>
                </div>
              </td>
            </template>
            <template v-else>
              <!-- Columna Stock (Con Análisis de Variantes) -->
                <td class="px-6 py-[16px] text-center">
                  <div class="flex flex-col items-center relative group/stock" :title="hasVariants(product) ? getVariantStockAnalysis(product)?.tooltipText : null"> 
                    <span :class="[
                        'inline-flex items-center justify-center gap-1 min-w-[2.5rem] px-2 py-0.5 rounded-md text-[14px] font-mono font-bold tabular-nums cursor-default transition-colors',
                        getTotalStock(product) === 0 ? 'text-gray-900 dark:text-gray-200' : 
                        (hasVariants(product) && hasVariantWithLowStock(product)) || (!hasVariants(product) && getTotalStock(product) <= (product.min_stock || 0)) ? 'text-gray-900 dark:text-gray-200' : 
                        'text-gray-900 dark:text-gray-200'
                      ]">
                        {{ hasVariants(product) ? getStockSummary(product).total : getTotalStock(product) }}
                      </span>

                      <!-- State below the main total -->
                      <template v-if="hasVariants(product)">
                        <div v-if="getVariantStockAnalysis(product)?.status === 'critical'" class="mt-1.5 flex items-center gap-1 text-xs font-bold text-rose-600 dark:text-rose-400">
                          <span class="text-xs leading-none">✖</span>
                          <span>{{ getVariantStockAnalysis(product).message }}</span>
                        </div>
                        <div v-else-if="getVariantStockAnalysis(product)?.status === 'low'" class="mt-1.5 flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400">
                          <span class="text-xs leading-none">⚠</span>
                          <span>{{ getVariantStockAnalysis(product).message }}</span>
                        </div>
                        <div v-else class="mt-1 flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-500">
                          <span>✓ Todas OK</span>
                        </div>
                      </template>
                      <template v-else>
                         <span v-if="getTotalStock(product) === 0" class="text-xs font-bold text-rose-600 dark:text-rose-400 mt-1.5">
                           ✖ Sin stock
                         </span>
                         <span v-else-if="getTotalStock(product) <= (product.min_stock || 0)" class="text-xs font-bold text-amber-600 dark:text-amber-400 mt-1.5">
                           ⚠ Bajo
                         </span>
                      </template>
                  
                  <!-- Tooltip / Popover Multi-sede (Glassmorphism) -->
                  <div v-if="product.warehouses && product.warehouses.length > 0" 
                       class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover/stock:opacity-100 transition-all duration-200 pointer-events-none z-10 w-48 bg-white dark:bg-zinc-900/95 backdrop-blur-md shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-white/10 rounded-xl p-3">
                    <p class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wide mb-2 text-left">Stock por Sede</p>
                    <ul class="space-y-1.5 text-sm text-left">
                      <li v-for="wh in product.warehouses" :key="wh.warehouse_id" class="flex justify-between items-center text-gray-700 dark:text-zinc-300">
                         <span class="flex items-center gap-1.5 truncate max-w-[120px]">
                           <svg class="w-3.5 h-3.5 text-gray-400 dark:text-zinc-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                           </svg> 
                           <span class="truncate text-[13px]">{{ wh.warehouse_name || 'Desconocida' }}</span>
                         </span>
                         <span class="font-mono font-semibold text-[13px]" :class="wh.stock_quantity > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400'">
                           {{ wh.stock_quantity }}
                         </span>
                      </li>
                    </ul>
                    <div class="absolute -bottom-1.5 left-1/2 transform -translate-x-1/2 w-3 h-3 bg-white dark:bg-zinc-900/95 border-b border-r border-gray-200 dark:border-white/10 rotate-45"></div>
                  </div>
                </div>
              </td>
            </template>
            <td class="px-6 py-[16px] text-center">
              <span :class="getProductStatus(product) ? 
                'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 
                'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400'" 
                class="px-2 py-0.5 rounded-md text-[13px] font-semibold uppercase tracking-wide">
                {{ getProductStatus(product) ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td class="px-6 py-[16px]">
              <div class="flex items-center justify-center gap-1">
                <button @click.stop="viewProduct(product)" 
                  class="p-1.5 text-gray-900 dark:text-zinc-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-zinc-700/40 rounded-md transition-all"
                  title="Ver detalles">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </button>
                <button @click.stop="editProduct(product)" 
                  class="p-1.5 text-gray-900 dark:text-zinc-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-zinc-700/40 rounded-md transition-all"
                  title="Editar producto">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button @click.stop="toggleProductStatus(product)" 
                  class="p-1.5 rounded-md transition-all"
                  :class="getProductStatus(product) !== false 
                    ? 'text-gray-900 dark:text-zinc-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-zinc-700/40' 
                    : 'text-gray-900 dark:text-zinc-300 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-slate-100 dark:hover:bg-zinc-700/40'"
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
                <button @click.stop="deleteProduct(product)" 
                  class="p-1.5 text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition-all"
                  title="Eliminar producto">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>

          <!-- Sub-tabla Variantes (Nested Table) -->
          <tr v-if="expandedProducts.has(product.id) && hasVariants(product)" class="bg-gray-50/50 dark:bg-zinc-800/30 border-b border-[#E5E7EB] dark:border-zinc-800 relative">
            <td :colspan="showMultipleStockColumns ? 5 + availableWarehouses.length : 6" class="py-4 px-6 pl-14">
              <!-- Indicador visual de jerarquía -->
              <div class="absolute left-6 top-0 bottom-0 w-px bg-gray-200 dark:bg-zinc-700/50"></div>
              <div class="absolute left-6 top-6 w-5 h-px bg-gray-200 dark:bg-zinc-700/50"></div>

              <div class="transition-all bg-slate-50 dark:bg-zinc-800/20 border-t border-b border-gray-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                  <table class="w-full text-left text-sm">
                    <thead class="text-gray-500 dark:text-zinc-400 border-b border-gray-200 dark:border-zinc-800">
                      <tr>
                        <th class="py-3 px-6 pl-12 font-semibold tracking-wide uppercase text-xs">Variante</th>
                        <th class="py-3 px-6 font-semibold tracking-wide uppercase text-xs">SKU</th>
                        <th class="py-3 px-6 w-32 font-semibold tracking-wide uppercase text-xs text-right">Precio</th>
                        <th class="py-3 px-6 pr-12 w-32 font-semibold tracking-wide uppercase text-xs text-right">Stock</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/60">
                      <tr v-for="variant in product.variants" :key="variant.id" class="text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-800/40 transition-colors">
                        <td class="py-3 px-6 pl-12">
                          <div class="flex items-center gap-2.5">
                            <div v-if="getVariantColor(variant)" 
                                 class="w-5 h-5 rounded-full border-2 border-gray-300 dark:border-zinc-500 shrink-0 shadow-sm"
                                 :style="{ backgroundColor: getVariantColor(variant) }"
                                 :title="'Color: ' + getVariantColor(variant)">
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                              <template v-if="getVariantOptionsArray(variant).length">
                                <template v-for="(opt, i) in getVariantOptionsArray(variant)" :key="i">
                                  <!-- Si es color con valor hex, solo mostrar el swatch (ya visible arriba) -->
                                  <template v-if="opt.name.toLowerCase() === 'color' && String(opt.value).startsWith('#')">
                                    <span class="text-gray-500 dark:text-zinc-400 text-sm">{{ opt.name }}:</span>
                                    <span v-if="opt.text && opt.text !== opt.value" class="font-semibold text-sm text-gray-800 dark:text-zinc-200">{{ opt.text }}</span>
                                    <span v-else class="inline-block w-4 h-4 rounded-full border-2 border-gray-300 dark:border-zinc-500 align-middle shadow-sm" :style="{ backgroundColor: opt.value }"></span>
                                  </template>
                                  <!-- Opciones normales -->
                                  <template v-else>
                                    <span class="text-gray-500 dark:text-zinc-400 text-sm">{{ opt.name }}:</span>
                                    <span class="font-semibold text-sm text-gray-800 dark:text-zinc-200">{{ opt.text || opt.value }}</span>
                                  </template>
                                  <span v-if="i < getVariantOptionsArray(variant).length - 1" class="text-gray-300 dark:text-zinc-600 mx-1">•</span>
                                </template>
                              </template>
                              <span v-else class="font-semibold text-sm">
                                {{ variant.name || 'Variante Base' }}
                              </span>
                            </div>
                          </div>
                        </td>
                        <td class="py-3 px-6">
                          <span class="font-mono text-[13px] text-gray-600 dark:text-zinc-400">{{ variant.sku || 'N/A' }}</span>
                        </td>
                        <td class="py-3 px-6 text-right tabular-nums font-semibold text-gray-900 dark:text-white">
                          ${{ formatCurrency(variant.price || product.price) }}
                        </td>
                        <td class="py-3 px-6 pr-12 text-right">
                          <span :class="[
                            'inline-flex items-center justify-end gap-1 font-mono text-sm tabular-nums font-bold',
                            (variant.stock || 0) === 0 ? 'text-rose-600 dark:text-rose-400' : 
                            (variant.stock || 0) <= (product.min_stock || 0) ? 'text-amber-600 dark:text-amber-400' :
                            'text-gray-900 dark:text-white'
                          ]">
                            {{ variant.stock || 0 }}
                            <span v-if="(variant.stock || 0) === 0" class="text-xs leading-none ml-0.5" title="Sin stock">⚠</span>
                            <span v-else-if="(variant.stock || 0) <= (product.min_stock || 0)" class="text-xs leading-none ml-0.5" title="Bajo stock">⚠</span>
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </td>
          </tr>
          </template>
        </tbody>
      </table>
      </div>
      
      <!-- Paginador -->
      <div class="mt-4 mb-6">
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
    <div class="fixed top-4 right-4 space-y-2" style="z-index: 2147483647;" v-if="notifications.length > 0">
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
            <p class="text-[15px] font-medium text-gray-900">{{ notification.title }}</p>
            <p class="text-[15px] text-gray-500" v-if="notification.message">{{ notification.message }}</p>
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
          <div class="fixed inset-0 bg-black/60 " @click="showStatusConfirmModal = false"></div>
          
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
                  <p class="text-[15px] text-gray-600 dark:text-zinc-400 mt-1">
                    Esta acción modificará el estado del producto
                  </p>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="px-6 pb-6">
              <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50">
                <p class="font-semibold text-gray-900 dark:text-white">{{ pendingStatusChange?.product?.name }}</p>
                <p class="text-[15px] text-gray-500 dark:text-zinc-400 mt-1">SKU: {{ pendingStatusChange?.product?.sku || 'N/A' }}</p>
              </div>
              <p class="text-[15px] text-gray-600 dark:text-zinc-400 mt-4">
                {{ pendingStatusChange?.newStatus 
                  ? 'El producto estará disponible para la venta.' 
                  : 'El producto no estará disponible para la venta.' }}
              </p>
            </div>

            <!-- Actions -->
            <div class="bg-gray-50 dark:bg-zinc-900 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-zinc-800">
              <button @click="showStatusConfirmModal = false" 
                      class="px-5 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 text-[15px] font-bold rounded-md border border-gray-200 dark:border-zinc-700 shadow-sm transition-all duration-200">
                Cancelar
              </button>
              <button @click="confirmStatusChange" 
                      :class="pendingStatusChange?.newStatus 
                        ? 'bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-700' 
                        : 'bg-amber-600 hover:bg-amber-700 dark:bg-amber-600 dark:hover:bg-amber-700'"
                      class="px-6 py-2.5 text-white text-[15px] font-bold rounded-xl shadow-lg transition-all duration-200">
                Confirmar
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Modal Confirmar Eliminación de Producto -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-200" leave-to-class="opacity-0">
        <div v-if="showDeleteConfirmModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showDeleteConfirmModal = false"></div>
          <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 max-w-md w-full overflow-hidden border border-gray-200 dark:border-zinc-800">
            <!-- Header -->
            <div class="px-6 pt-6 pb-4">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-red-100 dark:bg-red-950/60 border border-red-200 dark:border-red-800/50">
                  <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="text-lg font-bold text-red-700 dark:text-red-400">Eliminar Producto</h3>
                  <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">
                    Esta acción moverá el producto a la papelera
                  </p>
                </div>
              </div>
            </div>

            <!-- Product info -->
            <div class="px-6 pb-4">
              <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50">
                <p class="font-bold text-gray-900 dark:text-white text-base">{{ pendingDelete?.name }}</p>
                <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">SKU: {{ pendingDelete?.sku || 'N/A' }}</p>
              </div>

              <!-- Reason textarea -->
              <div class="mt-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2">Motivo de eliminación <span class="text-gray-400 dark:text-zinc-500 font-normal">(opcional)</span></label>
                <textarea v-model="deleteReason" rows="3"
                  placeholder="Ej: Producto descontinuado, error de carga, duplicado..."
                  class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"></textarea>
              </div>

              <!-- Warning callout -->
              <div class="mt-4 bg-amber-50 dark:bg-amber-950/30 rounded-xl p-3.5 border border-amber-200 dark:border-amber-800/50">
                <div class="flex items-start gap-2.5">
                  <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                  </svg>
                  <div class="text-sm text-amber-800 dark:text-amber-300">
                    <p>Las facturas y movimientos de inventario asociados se mantendrán en el historial.</p>
                    <p class="mt-1 font-medium">Podrás restaurar el producto desde <span class="underline">Configuración → Papelera</span>.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-zinc-900/80 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-zinc-800">
              <button @click="showDeleteConfirmModal = false; deleteReason = ''"
                      class="px-5 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 text-sm font-bold rounded-md border border-gray-200 dark:border-zinc-700 shadow-sm transition-all duration-200">
                Cancelar
              </button>
              <button @click="confirmDeleteProduct"
                      :disabled="deletingProduct"
                      class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white dark:text-zinc-900 text-sm font-semibold rounded-md shadow-lg shadow-red-500/25 dark:shadow-red-900/40 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <svg v-if="!deletingProduct" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <div v-else class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                {{ deletingProduct ? 'Eliminando...' : 'Eliminar Producto' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Modal de Categoría Inactiva -->
    <div v-if="showCategoryInactiveModal" 
         class="fixed inset-0 bg-gray-900/50 dark:bg-black/70  flex items-center justify-center p-4 z-50"
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
              <p class="text-[15px] text-white/80">La categoría debe estar activa primero</p>
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
            <p class="text-[15px] text-blue-700 dark:text-blue-400">Estado: Inactiva</p>
          </div>
          <div class="bg-green-50 dark:bg-green-950 rounded-lg p-4 border border-green-200 dark:border-green-800">
            <p class="text-[15px] text-green-800 dark:text-green-300">
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
           class="fixed inset-0 bg-gray-900/50 dark:bg-black/70  flex items-center justify-center"
           style="z-index: 50000"
           @click.self="showProductModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-7xl shadow-2xl max-h-[95vh] overflow-hidden border border-gray-200 dark:border-zinc-800 mx-4 flex flex-col">
        
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-8 py-3 flex-shrink-0">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                {{ isEditing ? 'Editar Producto' : 'Nuevo Producto' }}
              </h3>
              <span class="text-sm text-gray-400 dark:text-zinc-500">—</span>
              <p class="text-sm text-gray-500 dark:text-zinc-400">
                {{ isEditing ? 'Modifica la información del producto' : 'Agrega un nuevo producto al inventario' }}
              </p>
            </div>
            <button @click="showProductModal = false" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="flex flex-1 overflow-hidden">
          <!-- Formulario Principal -->
          <div class="flex-1 overflow-y-auto" :class="isFashionMode ? 'bg-[#FAFBFC] dark:bg-zinc-950 p-8' : 'bg-[#F9FAFB] dark:bg-zinc-950 p-6'">
            
            <!-- �x Formulario Fashion (integrado sin header ni footer propio) -->
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
              
              <!-- �"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"� -->
              <!-- SECCI�N 1: INFORMACI�N BÁSICA (Compacta) -->
              <!-- SECCIÓN 1: INFORMACIÓN BÁSICA (Compacta) -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 border border-[#E5E7EB] dark:border-zinc-800">
                <h4 class="text-[14px] font-semibold text-[#374151] dark:text-zinc-300 uppercase tracking-wide mb-5">Información Básica</h4>
                
                <!-- Nombre del Producto (Full width) -->
                <div class="mb-5">
                  <label class="block text-[13px] font-medium text-[#374151] dark:text-zinc-300 mb-1.5">
                    Nombre del Producto <span class="text-rose-500">*</span>
                  </label>
                  <input v-model="productForm.name" 
                         type="text" 
                         required
                         class="w-full px-4 py-2.5 border border-[#D1D5DB] dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-600/10 focus:border-[#2563EB] text-[15px] bg-white dark:bg-zinc-800 text-[#111827] dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 transition-all"
                         placeholder="Ej: iPhone 13 Pro Max">
                </div>

                <!-- Fila 1: Categoría + SKU + Código de Barras -->
                <div class="grid grid-cols-3 gap-4 mb-5">
                  <div>
                    <label class="block text-[13px] font-medium text-[#374151] dark:text-zinc-300 mb-1.5">Categoría *</label>
                    <select v-model="productForm.category_id" 
                            required
                            @change="handleCategoryChange"
                            class="w-full px-3 py-2.5 border border-[#D1D5DB] dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-600/10 focus:border-[#2563EB] text-[14px] bg-white dark:bg-zinc-800 text-[#111827] dark:text-white transition-all">
                      <option value="">Seleccionar</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                      <option value="__new__" class="font-medium text-blue-600">+ Nueva</option>
                    </select>
                  </div>
                  
                  <div>
                    <label class="block text-[13px] font-medium text-[#374151] dark:text-zinc-300 mb-1.5">SKU</label>
                    <input v-model="productForm.sku" 
                           type="text" 
                           class="w-full px-3 py-2.5 border border-[#D1D5DB] dark:border-zinc-700 rounded-lg text-[14px] bg-[#F9FAFB] dark:bg-zinc-800/50 text-[#6B7280] dark:text-zinc-400 font-mono placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-600/10 focus:border-[#2563EB] transition-all"
                           placeholder="Auto-generado">
                  </div>
                  
                  <div>
                    <label class="block text-[13px] font-medium text-[#374151] dark:text-zinc-300 mb-1.5">Código de Barras</label>
                    <div class="relative">
                      <input v-model="productForm.barcode" 
                             type="text" 
                             class="w-full px-3 pr-9 py-2.5 border border-[#D1D5DB] dark:border-zinc-700 rounded-lg text-[14px] bg-white dark:bg-zinc-800 text-[#111827] dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-600/10 focus:border-[#2563EB] transition-all"
                             placeholder="Escanear o generar">
                      <button type="button" 
                              @click="generateBarcode"
                              title="Generar código"
                              class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-[#6B7280] hover:text-[#374151] dark:hover:text-zinc-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Fila 2: Proveedor + Descripción -->
                <div class="grid grid-cols-3 gap-4">
                  <div>
                    <label class="block text-[13px] font-medium text-[#374151] dark:text-zinc-300 mb-1.5">Proveedor</label>
                    <select v-model="productForm.supplier_id" 
                            @change="handleSupplierChange"
                            class="w-full px-3 py-2.5 border border-[#D1D5DB] dark:border-zinc-700 rounded-lg text-[14px] bg-white dark:bg-zinc-800 text-[#111827] dark:text-white focus:ring-2 focus:ring-blue-600/10 focus:border-[#2563EB] transition-all">
                      <option :value="null">Sin proveedor</option>
                      <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                        {{ supplier.name }}
                      </option>
                      <option value="__new__" class="font-medium text-blue-600">+ Nuevo</option>
                    </select>
                  </div>
                  
                  <div class="col-span-2">
                    <label class="block text-[13px] font-medium text-[#374151] dark:text-zinc-300 mb-1.5">Descripción</label>
                    <input v-model="productForm.description" 
                           type="text"
                           class="w-full px-3 py-2.5 border border-[#D1D5DB] dark:border-zinc-700 rounded-lg text-[14px] bg-white dark:bg-zinc-800 text-[#111827] dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-600/10 focus:border-[#2563EB] transition-all"
                           placeholder="Descripción breve del producto">
                  </div>
                </div>
              </div>

              <!-- SECCIÓN 2: UNIDAD DE MEDIDA (Compacta) -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 border border-[#E5E7EB] dark:border-zinc-800">
                <h4 class="text-[13px] font-semibold text-[#374151] dark:text-zinc-300 mb-4 uppercase tracking-wide">Unidad de Medida</h4>
                <div class="flex items-center gap-4">
                  <div class="flex-1">
                    <label class="block text-[13px] font-medium text-[#374151] dark:text-zinc-300 mb-1.5">
                      ¿Cómo se vende? <span class="text-rose-500">*</span>
                    </label>
                    <select
                      v-model="productForm.measurement_unit"
                      @change="updateAllowDecimal"
                      class="w-full px-3 py-2.5 border border-[#D1D5DB] dark:border-zinc-700 bg-white dark:bg-zinc-800 rounded-lg focus:ring-2 focus:ring-blue-600/10 focus:border-[#2563EB] text-[14px] text-[#111827] dark:text-white transition-all"
                    >
                      <option value="unit">Unidades (und) - celular, TV</option>
                      <option value="kg">Kilogramos (kg) - carne, papas</option>
                      <option value="g">Gramos (g) - especias, café</option>
                      <option value="m">Metros (m) - tela, cable</option>
                      <option value="cm">Centímetros (cm) - cinta</option>
                      <option value="l">Litros (L) - gasolina, leche</option>
                      <option value="ml">Mililitros (ml) - perfume</option>
                    </select>
                  </div>
                  
                  <!-- Toggle Decimales -->
                  <div class="flex items-center gap-3 px-4 py-3 bg-[#F9FAFB] dark:bg-zinc-800 rounded-lg border border-[#E5E7EB] dark:border-zinc-700">
                    <div>
                      <span class="text-[13px] font-medium text-[#374151] dark:text-zinc-300 block">Decimales</span>
                      <span class="text-[12px] text-[#6B7280] dark:text-zinc-500">{{ productForm.allow_decimal ? '0.5, 1.25...' : '1, 2, 3...' }}</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input type="checkbox" v-model="productForm.allow_decimal" class="sr-only peer">
                      <div class="w-10 h-5 bg-gray-300 dark:bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-slate-700 dark:peer-checked:bg-slate-600"></div>
                    </label>
                  </div>
                </div>
              </div>

              <!-- �"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"� -->
              <!-- SECCI�N 3: PRECIOS + STOCK (Todo en uno) -->
              <!-- �"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"� -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 border border-[#E5E7EB] dark:border-zinc-800">
                <h4 class="text-[14px] font-semibold text-[#111827] dark:text-white mb-4 flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                  </svg>
                  Precios y Stock
                </h4>
                
                <div class="grid grid-cols-4 gap-4">
                  <!-- Costo -->
                  <div>
                    <label class="block text-[13px] font-medium text-[#374151] dark:text-zinc-300 mb-1.5">
                      Costo {{ getUnitLabel(productForm.measurement_unit) }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[14px] text-[#6B7280] dark:text-zinc-500 font-medium">$</span>
                      <input v-model="productForm.cost" 
                             type="number" 
                             step="0.01"
                             min="0"
                             required
                             class="w-full pl-8 pr-3 py-2.5 border border-[#D1D5DB] dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-600/10 focus:border-[#2563EB] text-[14px] bg-white dark:bg-zinc-800 text-[#111827] dark:text-white tabular-nums transition-all"
                             :placeholder="getUnitPlaceholder(productForm.measurement_unit)">
                    </div>
                  </div>
                  
                  <!-- Precio de Venta -->
                  <div>
                    <label class="block text-[13px] font-medium text-[#374151] dark:text-zinc-300 mb-1.5">
                      Precio venta {{ getUnitLabel(productForm.measurement_unit) }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[14px] text-[#6B7280] dark:text-zinc-500 font-medium">$</span>
                      <input v-model="productForm.price" 
                             type="number" 
                             step="0.01"
                             min="0"
                             required
                             class="w-full pl-8 pr-3 py-2.5 border border-[#D1D5DB] dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-600/10 focus:border-[#2563EB] text-[14px] bg-white dark:bg-zinc-800 text-emerald-600 dark:text-emerald-400 font-semibold tabular-nums transition-all"
                             :placeholder="getUnitPlaceholder(productForm.measurement_unit)">
                    </div>
                    <p class="text-[11px] text-[#9CA3AF] dark:text-zinc-500 mt-1">Precio final que verá el cliente {{ getUnitLabel(productForm.measurement_unit) }}</p>
                  </div>
                  
                  <!-- Margen -->
                  <div>
                    <label class="block text-[13px] font-medium text-[#374151] dark:text-zinc-300 mb-1.5">Margen</label>
                    <div class="px-3 py-2.5 bg-[#F9FAFB] dark:bg-zinc-800 border border-[#E5E7EB] dark:border-zinc-700 rounded-lg text-[14px] font-bold text-center tabular-nums"
                         :class="productForm.price && productForm.cost && ((productForm.price - productForm.cost) / productForm.cost * 100) >= 20 
                           ? 'text-emerald-600 dark:text-emerald-400' 
                           : productForm.price && productForm.cost && ((productForm.price - productForm.cost) / productForm.cost * 100) >= 10 
                             ? 'text-amber-600 dark:text-amber-400' 
                             : 'text-gray-600 dark:text-zinc-400'">
                      {{ productForm.price && productForm.cost ? 
                        (((productForm.price - productForm.cost) / productForm.cost) * 100).toFixed(0) + '%' : 
                        '0%' }}
                    </div>
                  </div>

                  <!-- Stock Inicial (Solo si hay 1 bodega) -->
                  <div v-if="availableWarehouses.length === 1">
                    <label class="block text-[13px] font-medium text-[#374151] dark:text-zinc-300 mb-1.5">
                      Stock disponible ({{ getUnitAbbreviation(productForm.measurement_unit) }}) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                      <input v-model="productForm.stock" 
                             type="number" 
                             :step="productForm.allow_decimal ? '0.01' : '1'"
                             min="0"
                             class="w-full px-3 pr-12 py-2.5 border border-[#D1D5DB] dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-600/10 focus:border-[#2563EB] text-[14px] bg-white dark:bg-zinc-800 text-[#111827] dark:text-white text-center font-semibold tabular-nums transition-all"
                             :placeholder="productForm.allow_decimal ? '0.00' : '0'">
                      <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[12px] text-[#6B7280] dark:text-zinc-500 font-medium">
                        {{ getUnitAbbreviation(productForm.measurement_unit) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- �"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"� -->
              <!-- SECCI�N 4: STOCK MULTI-BODEGA (Solo si hay 2+ bodegas) -->
              <!-- �"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"� -->
              <div v-if="availableWarehouses.length >= 2" class="bg-white dark:bg-zinc-900 rounded-xl p-6 border border-[#E5E7EB] dark:border-zinc-800">
                <div class="flex items-center justify-between mb-4">
                  <h4 class="text-[15px] font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Stock por Sede
                    <span class="text-[13px] font-normal text-gray-400 dark:text-zinc-500">({{ getUnitAbbreviation(productForm.measurement_unit) }})</span>
                  </h4>
                  <span class="px-2 py-1 bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 text-[13px] font-bold rounded-lg">
                    Total: {{ calculateTotalStock() }} {{ getUnitAbbreviation(productForm.measurement_unit) }}
                  </span>
                </div>
                    
                <!-- Grid de Sedes -->
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                  <div v-for="warehouse in availableWarehouses" :key="warehouse.id"
                       class="p-3 rounded-xl border-2 transition-all"
                       :class="productForm.warehouseEnabled[warehouse.id] 
                         ? 'border-blue-300 dark:border-blue-700 bg-blue-50/50 dark:bg-blue-950/30' 
                         : 'border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50'">
                    
                    <!-- Checkbox + Nombre -->
                    <label class="flex items-center gap-2 cursor-pointer mb-2">
                      <input 
                        v-model="productForm.warehouseEnabled[warehouse.id]"
                        type="checkbox"
                        class="w-4 h-4 text-blue-600 bg-white dark:bg-zinc-700 border-gray-300 dark:border-zinc-600 rounded focus:ring-blue-500"
                      />
                      <span class="text-[15px] font-semibold text-gray-900 dark:text-white truncate">{{ warehouse.name }}</span>
                      <span v-if="warehouse.is_default" class="text-[12px] font-bold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/50 px-1.5 py-0.5 rounded">PRINCIPAL</span>
                    </label>
                    
                    <!-- Input Stock -->
                    <div v-if="productForm.warehouseEnabled[warehouse.id]">
                      <div class="relative">
                        <input 
                          v-model.number="productForm.warehouseStock[warehouse.id]"
                          type="number" 
                          :step="productForm.allow_decimal ? '0.01' : '1'"
                          min="0"
                          :placeholder="productForm.allow_decimal ? '0.00' : '0'"
                          class="w-full px-3 pr-10 py-2 border border-gray-200 dark:border-zinc-700 rounded-lg text-[15px] font-semibold text-center bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-400 transition-all"
                        />
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px] text-gray-400 dark:text-zinc-500">
                          {{ getUnitAbbreviation(productForm.measurement_unit) }}
                        </span>
                      </div>
                    </div>
                    <div v-else class="text-center py-2">
                      <span class="text-[13px] text-gray-400 dark:text-zinc-500">No disponible</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- �"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"� -->
              <!-- SECCI�N 5: ESTADO (Inline compacto) -->
              <!-- �"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"� -->
              <div class="flex items-center justify-between bg-white dark:bg-zinc-900 rounded-xl p-5 border border-[#E5E7EB] dark:border-zinc-800">
                <div class="flex items-center gap-3">
                  <svg class="w-5 h-5" :class="productForm.active ? 'text-emerald-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <div>
                    <span class="text-[15px] font-bold text-gray-900 dark:text-white">Producto {{ productForm.active ? 'Activo' : 'Inactivo' }}</span>
                    <p class="text-[13px] text-gray-500 dark:text-zinc-400">{{ productForm.active ? 'Disponible para la venta' : 'No aparecerá en el POS' }}</p>
                  </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="productForm.active" type="checkbox" class="sr-only peer">
                  <div class="w-11 h-6 bg-gray-300 dark:bg-zinc-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                </label>
              </div>
            </form>
          </div>
          
          <!-- Sidebar para Imagen (Compacto) -->
          <div v-if="!isFashionMode" class="w-72 bg-[#FAFAFA] dark:bg-zinc-900 border-l border-[#E5E7EB] dark:border-zinc-800 p-6 flex flex-col">
            
            <!-- Header -->
            <div class="flex items-center gap-2 mb-4">
              <div class="w-8 h-8 bg-gray-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center border border-gray-200 dark:border-zinc-700">
                <svg class="w-4 h-4 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div>
                <h4 class="text-[14px] font-semibold text-[#111827] dark:text-white">Imagen</h4>
                <p class="text-[12px] text-[#6B7280] dark:text-zinc-400">Foto del producto</p>
              </div>
            </div>
            
            <!-- Imagen Actual (Modo Edición) -->
            <!-- Mostrar cuando: 1) Estamos editando, 2) Hay imagen actual, 3) NO hay preview nuevo -->
            <div v-if="isEditing && currentProductImage && !previewImage" class="mb-4">
              <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-3">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-[13px] font-semibold text-gray-600 dark:text-zinc-400">�x�️ Imagen actual</span>
                  <button type="button" @click="deleteProductImage" :disabled="deletingImage"
                          class="text-[13px] text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-semibold flex items-center gap-1 disabled:opacity-50">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ deletingImage ? 'Eliminando...' : 'Eliminar' }}
                  </button>
                </div>
                <div class="relative inline-block w-full">
                  <img :src="currentProductImage" 
                       class="w-full rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700"
                       @error="currentProductImage = null"
                       alt="Imagen actual">
                </div>
                <p class="text-[12px] text-gray-500 dark:text-zinc-500 mt-2 text-center">Cambia el método abajo para reemplazar</p>
              </div>
            </div>
            
            <!-- Toggle URL/Archivo -->
            <div class="flex bg-gray-100 dark:bg-zinc-800 rounded-lg p-1 mb-3 border border-gray-200 dark:border-zinc-700">
              <button type="button"
                      @click="changeImageMethod('url')"
                      :class="[
                        'flex-1 px-2 py-1.5 rounded-md text-[13px] font-semibold transition-all flex items-center justify-center gap-1',
                        imageUploadMethod === 'url' 
                          ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm' 
                          : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300'
                      ]">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                URL
              </button>
              <button type="button"
                      @click="changeImageMethod('file')"
                      :class="[
                        'flex-1 px-2 py-1.5 rounded-md text-[13px] font-semibold transition-all flex items-center justify-center gap-1',
                        imageUploadMethod === 'file' 
                          ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm' 
                          : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300'
                      ]">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Archivo
              </button>
            </div>
            
            <!-- Área de Subida -->
            <div class="flex-1">
              <!-- Subida de archivo -->
              <div v-if="imageUploadMethod === 'file'">
                <div @click="$refs.fileInput?.click()" 
                     class="border-2 border-dashed border-gray-300 dark:border-zinc-700 rounded-xl p-4 text-center cursor-pointer hover:border-gray-400 dark:hover:border-zinc-600 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all">
                  
                  <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="hidden">
                  
                  <div v-if="!previewImage">
                    <div class="w-10 h-10 bg-gray-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-2">
                      <svg class="w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                    </div>
                    <p class="text-[13px] font-medium text-gray-600 dark:text-zinc-400">Haz clic o arrastra</p>
                    <p class="text-[12px] text-gray-400 dark:text-zinc-500 mt-0.5">PNG, JPG hasta 5MB</p>
                  </div>
                  
                  <!-- Preview -->
                  <div v-else class="relative inline-block">
                    <img :src="previewImage" class="max-h-24 rounded-lg shadow-sm mx-auto">
                    <button type="button" @click.stop="clearImageUpload"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center hover:bg-red-600 transition-colors shadow">
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
                       type="text" 
                       class="w-full px-3 py-2.5 border border-[#D1D5DB] dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-600/10 focus:border-[#2563EB] text-[14px] bg-white dark:bg-zinc-800 text-[#111827] dark:text-white placeholder-gray-400 dark:placeholder-zinc-500"
                       placeholder="https://ejemplo.com/imagen.jpg">
                
                <!-- Preview de URL -->
                <div v-if="productForm.image && (productForm.image.startsWith('data:image') || isValidUrl(productForm.image))" class="text-center">
                  <div class="inline-block relative">
                    <img :src="productForm.image" 
                         @error="imageLoadError = true"
                         @load="imageLoadError = false"
                         class="max-h-20 rounded-lg border border-gray-200 dark:border-zinc-700 shadow-sm"
                         alt="Preview">
                    <div v-if="imageLoadError" class="absolute inset-0 bg-red-50 dark:bg-red-950 rounded-lg flex items-center justify-center">
                      <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-4 space-y-2">
              <button v-if="isFashionMode" type="button"
                      @click="saveFashionProduct"
                      :disabled="loading"
                      class="w-full px-4 h-[44px] bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white rounded-xl font-semibold text-[15px] shadow-lg transition-all disabled:opacity-50">
                {{ loading ? 'Guardando...' : 'Crear Producto' }}
              </button>
              
              <button v-else type="button" 
                      @click="saveProduct"
                      :disabled="loading"
                      class="w-full px-4 h-[44px] bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white rounded-xl font-semibold text-[15px] shadow-lg transition-all disabled:opacity-50">
                {{ loading ? 'Guardando...' : (isEditing ? 'Actualizar' : 'Crear Producto') }}
              </button>
              
              <button type="button" @click="showProductModal = false"
                      class="w-full px-4 h-[40px] bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-[#374151] dark:text-zinc-300 rounded-xl font-medium text-[14px] transition-colors border border-[#D1D5DB] dark:border-zinc-700">
                Cancelar
              </button>
            </div>
          </div>
        </div>
        
        <!-- Footer con botones (solo para fashion mode) -->
        <div v-if="isFashionMode" class="flex-shrink-0 bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-8 py-2.5 flex items-center justify-between">
          <button type="button" 
                  @click="showProductModal = false"
                  class="px-6 py-2 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 rounded-xl font-bold text-sm transition-colors border border-gray-300 dark:border-zinc-600">
            Cancelar
          </button>
          
          <button type="button" 
                  @click="saveFashionProduct"
                  :disabled="loading"
                  class="px-8 py-2 bg-[#0f172a] dark:bg-slate-700 hover:bg-[#020617] dark:hover:bg-slate-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-slate-400/30 dark:shadow-slate-900/50 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
            {{ loading ? 'Guardando...' : (isEditing ? 'Actualizar Producto' : 'Crear Producto') }}
          </button>
        </div>
      </div>
      </div>
    </Teleport>

    <!-- �a�️ Modal de Confirmación: Producto sin Stock -->
    <div v-if="showStockWarningModal" 
         class="fixed inset-0 bg-gray-900/70 dark:bg-black/80  flex items-center justify-center p-4 z-[60]"
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
              <p class="text-[15px] text-gray-600 dark:text-zinc-400 mt-0.5">Aún faltan datos importantes por llenar</p>
            </div>
          </div>
        </div>
        
        <!-- Contenido -->
        <div class="p-6">
          <p class="text-[15px] text-gray-700 dark:text-zinc-300 mb-4">
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
                <p class="text-[15px] font-bold"
                   :class="{
                     'text-red-900 dark:text-red-300': item.severity === 'high',
                     'text-amber-900 dark:text-amber-300': item.severity === 'medium',
                     'text-gray-700 dark:text-zinc-300': item.severity === 'low'
                   }">
                  {{ item.field }}
                </p>
                <p class="text-[13px] mt-0.5"
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
                <p class="text-[15px] font-semibold text-blue-900 dark:text-blue-300">Recomendación</p>
                <p class="text-[13px] text-blue-700 dark:text-blue-400 mt-1">
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
           class="fixed inset-0 bg-gray-900/50 dark:bg-black/70  flex items-center justify-center p-4"
           style="z-index: 50000"
           @click.self="showViewModal = false">
      <!-- Modal Full Width para productos FASHION (con o sin variantes) -->
      <div v-if="selectedProduct && isFashionProduct(selectedProduct)" 
           class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-6xl shadow-2xl max-h-[90vh] overflow-hidden border border-gray-200 dark:border-zinc-800 flex flex-col">
        
        <!-- Header -->
        <div class="bg-gray-50 dark:bg-zinc-900 px-6 py-5 border-b border-gray-200 dark:border-zinc-800 flex-shrink-0">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div>
                <div class="flex items-center gap-3 mb-1">
                  <h3 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">{{ selectedProduct.name }}</h3>
                  <span class="px-2 py-0.5 bg-gray-200 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border border-gray-300 dark:border-zinc-700 rounded text-[11px] font-bold uppercase tracking-wider">
                    Gestión de Variantes
                  </span>
                </div>
                <p class="text-[13px] text-gray-600 dark:text-zinc-500">Administración de inventario y valores por SKU</p>
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
          <!-- SECCI�N SUPERIOR: Galería + Resumen Global -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Galería de Imágenes -->
            <div class="lg:col-span-1">
              <div class="bg-white dark:bg-zinc-900/60  rounded-2xl p-3 border border-gray-200 dark:border-zinc-800 shadow-sm">
                <!-- Imagen Principal -->
                <div class="relative aspect-square rounded-xl overflow-hidden mb-3 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-zinc-800 dark:to-zinc-900">
                  <!-- Si hay imágenes reales, mostrar con <img> -->
                  <img v-if="selectedProductImages.length > 0" 
                       :src="selectedProductMainImage" 
                       @error="handleImageError" 
                       :alt="selectedProduct.name" 
                       class="w-full h-full object-contain">
                  <!-- Si NO hay imágenes, mostrar placeholder elegante -->
                  <div v-else class="w-full h-full flex items-center justify-center">
                    <div class="text-center">
                      <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-950/50 dark:to-purple-900/30 flex items-center justify-center mb-2">
                        <span class="text-3xl font-black text-purple-600 dark:text-purple-400">
                          {{ (selectedProduct.name || 'P').substring(0, 2).toUpperCase() }}
                        </span>
                      </div>
                      <p class="text-[13px] text-gray-400 dark:text-zinc-500">Sin imagen</p>
                    </div>
                  </div>
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
                    <img :src="img" class="w-full h-full object-cover" @error="handleImageError">
                  </button>
                </div>
                <!-- Placeholders solo cuando tiene exactamente 1 imagen -->
                <div v-else-if="selectedProductImages.length === 1" class="grid grid-cols-4 gap-2">
                  <div v-for="i in 4" :key="i" 
                       class="aspect-square bg-gray-100 dark:bg-zinc-800/50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
                <!-- NO mostrar nada si no hay imágenes (selectedProductImages.length === 0) -->
              </div>
            </div>
            
            <!-- Resumen Global (Estadísticas) -->
            <div class="lg:col-span-2 flex flex-col">
              <div class="flex-1 bg-white dark:bg-zinc-900/60 rounded-md border border-gray-200 dark:border-zinc-800">
                <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-zinc-800">
                  <!-- Stock Total Global -->
                  <div class="flex flex-col gap-1 px-5 py-4">
                    <div class="flex items-center justify-between">
                      <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Stock Total</p>
                      <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatNumber(getStockSummary(selectedProduct).total) }}</p>
                    <p class="text-xs text-gray-400 dark:text-zinc-500">unidades</p>
                  </div>
                  
                  <!-- Valor Total Inventario -->
                  <div class="flex flex-col gap-1 px-5 py-4">
                    <div class="flex items-center justify-between">
                      <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Valor Total</p>
                      <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">${{ formatCurrency(getTotalInventoryValue(selectedProduct)) }}</p>
                    <p class="text-xs text-gray-400 dark:text-zinc-500">inventario</p>
                  </div>
                  
                  <!-- Total Variantes -->
                  <div class="flex flex-col gap-1 px-5 py-4">
                    <div class="flex items-center justify-between">
                      <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Variantes</p>
                      <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ getVariantsCount(selectedProduct) }}</p>
                    <p class="text-xs text-gray-400 dark:text-zinc-500">combinaciones</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- TABLA EXCEL-STYLE: Edición en Línea -->
          <div class="bg-white dark:bg-zinc-900/60  rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden shadow-sm dark:shadow-none">
            <div class="bg-gray-50 dark:bg-zinc-800/50 px-5 py-3 border-b border-gray-200 dark:border-zinc-700/50">
              <h4 class="text-[15px] font-bold text-gray-900 dark:text-white">
                {{ hasVariants(selectedProduct) ? 'Gestión de Stock por Variante' : 'Información del Producto' }}
              </h4>
            </div>
            
            <!-- Tabla para productos CON variantes -->
            <div v-if="hasVariants(selectedProduct)" class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-gray-50 dark:bg-zinc-800/30 border-b border-gray-200 dark:border-zinc-700/50">
                    <th class="px-5 py-3 text-left text-[12px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Variante</th>
                    <th class="px-4 py-3 text-left text-[12px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-32">SKU</th>
                    <th class="px-4 py-3 text-center text-[12px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-40">Costo Unit.</th>
                    <th class="px-4 py-3 text-center text-[12px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-40">Precio Venta</th>
                    <th class="px-4 py-3 text-center text-[12px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-36">Stock</th>
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
                        <!-- Thumbnail o placeholder -->
                        <div v-if="getProductImage(selectedProduct)" 
                             class="w-10 h-10 bg-gray-100 dark:bg-zinc-800 rounded-lg overflow-hidden flex-shrink-0 ring-1 ring-gray-200 dark:ring-zinc-700">
                          <img :src="getProductImage(selectedProduct)" 
                               @error="(e) => e.target.style.display = 'none'"
                               class="w-full h-full object-cover">
                        </div>
                        <div v-else 
                             class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-950/50 dark:to-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                          <span class="text-[13px] font-black text-purple-600 dark:text-purple-400">
                            {{ (selectedProduct.name || 'P').substring(0, 2).toUpperCase() }}
                          </span>
                        </div>
                        <div class="flex items-center gap-2">
                          <div v-if="getVariantColor(variant)"
                               class="w-4 h-4 rounded-full shadow-sm border border-gray-300 dark:border-zinc-600 shrink-0"
                               :title="getVariantColor(variant)"
                               :style="{ backgroundColor: getVariantColor(variant) }">
                          </div>
                          <span class="text-[14px] font-medium text-gray-900 dark:text-gray-100">
                            <template v-if="getVariantOptionsArray(variant).length">
                              <template v-for="(opt, i) in getVariantOptionsArray(variant)" :key="i">
                                <template v-if="opt.name.toLowerCase() === 'color' && String(opt.value).startsWith('#')">
                                  <span class="text-gray-500 dark:text-zinc-400 capitalize">{{ opt.name }}:</span>
                                  <span v-if="opt.text && opt.text !== opt.value" class="font-semibold ml-0.5">{{ opt.text }}</span>
                                  <span v-else class="inline-block w-4 h-4 rounded-full border border-gray-300 dark:border-zinc-600 align-middle ml-0.5" :style="{ backgroundColor: opt.value }"></span>
                                </template>
                                <template v-else>
                                  <span class="text-gray-500 dark:text-zinc-400 capitalize">{{ opt.name }}:</span>
                                  <span class="font-semibold ml-0.5">{{ opt.text || opt.value }}</span>
                                </template>
                                <span v-if="i < getVariantOptionsArray(variant).length - 1" class="mx-1.5 text-gray-300 dark:text-zinc-600">•</span>
                              </template>
                            </template>
                            <template v-else>
                              {{ variant.name || selectedProduct.name }}
                            </template>
                          </span>
                        </div>
                      </div>
                    </td>
                    
                    <!-- SKU -->
                    <td class="px-4 py-3">
                      <span class="font-mono text-[13px] text-gray-600 dark:text-zinc-400 bg-gray-100 dark:bg-zinc-800/50 px-2 py-1 rounded">{{ variant.sku }}</span>
                    </td>
                    
                    <!-- Costo Unitario (Input Editable) -->
                    <td class="px-4 py-3">
                      <div class="flex items-center justify-center gap-0 group">      
                        <span class="text-[14px] text-gray-400 dark:text-zinc-500 font-medium px-1.5">$</span>
                        <input type="text"
                               :value="formatInputNumber(variant.editableCost)" 
                               @input="handleCostInput($event, variant)"
                               @focus="$event.target.select()"
                               class="w-28 px-2 py-1.5 text-right text-[14px] font-semibold bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700/80 rounded block text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all tabular-nums"
                               :class="variantChanges[variant.id]?.cost ? 'ring-2 ring-amber-500/20 border-amber-500' : ''">
                      </div>
                    </td>

                    <!-- Precio Venta (Input Editable) -->
                    <td class="px-4 py-3">
                      <div class="flex items-center justify-center gap-0 group">      
                        <span class="text-[14px] text-gray-400 dark:text-zinc-500 font-medium px-1.5">$</span>
                        <input type="text"
                               :value="formatInputNumber(variant.editablePrice)"
                               @input="handlePriceInput($event, variant)"       
                               @focus="$event.target.select()"
                               class="w-28 px-2 py-1.5 text-right text-[14px] font-bold bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700/80 rounded block text-[#1a1a20] dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all tabular-nums"
                               :class="variantChanges[variant.id]?.price ? 'ring-2 ring-amber-500/20 border-amber-500' : ''">
                      </div>
                    </td>

                    <!-- Stock (Input Numérico + Botones [-]/[+]) -->
                    <td class="px-4 py-3">
                      <div class="flex items-center justify-center gap-1">
                        <button @click="decrementStock(variant)" 
                                class="w-7 h-7 flex items-center justify-center bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded text-gray-700 dark:text-zinc-300 font-bold text-[15px] transition-colors">
                          <span class="mb-[2px] leading-none">-</span>
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
                                class="w-7 h-7 flex items-center justify-center bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 border border-gray-200 dark:border-zinc-700/80 rounded text-gray-500 dark:text-zinc-400 font-bold text-[16px] leading-none transition-colors">
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
                  <p class="text-[12px] text-gray-500 dark:text-zinc-500 mb-1 uppercase tracking-wide font-bold">SKU</p>
                  <p class="text-base font-bold text-gray-900 dark:text-white font-mono">{{ selectedProduct.sku || 'No definido' }}</p>
                </div>
                
                <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50">
                  <p class="text-[12px] text-gray-500 dark:text-zinc-500 mb-1 uppercase tracking-wide font-bold">Precio Venta</p>
                  <p class="text-base font-bold text-emerald-600 dark:text-emerald-400">${{ formatCurrency(selectedProduct.sale_price) }}</p>
                </div>
                
                <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50">
                  <p class="text-[12px] text-gray-500 dark:text-zinc-500 mb-1 uppercase tracking-wide font-bold">Stock Actual</p>
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
                      class="px-5 py-2.5 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-700 border border-gray-300 dark:border-zinc-700 rounded-xl text-[15px] font-medium transition-colors">
                Cerrar
              </button>
              <div v-if="hasUnsavedChanges" class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 rounded-lg">
                <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
                <span class="text-[13px] font-medium text-amber-600 dark:text-amber-400">{{ changesCount }} cambio(s) sin guardar</span>
              </div>
            </div>
            
            <!-- Botón Principal -->
            <button v-if="hasUnsavedChanges"
                    @click="saveInventoryChanges" 
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-[15px] font-bold shadow-lg shadow-blue-500/30 transition-all inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                <span>Guardar Cambios</span>
            </button>
            <button v-else
                    @click="editProduct(selectedProduct)" 
                    class="px-6 py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-xl text-[15px] font-bold shadow-lg transition-all inline-flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              <span>Editar Producto Padre</span>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Modal Simple para productos sin variantes (Original) - Gemini Style -->
      <div v-else-if="selectedProduct"
           class="bg-white dark:bg-[#1e1f20] rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        
        <!-- Header (Gemini clean) -->
        <div class="bg-gray-50 dark:bg-[#282a2c] px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-[#e8f0fe] dark:bg-[#8ab4f8]/20 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Detalles del Producto</h3>
                <p class="text-[13px] text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Información completa del producto</p>
              </div>
            </div>
            <button @click="showViewModal = false" 
                    class="p-2 hover:bg-[#e8eaed] dark:hover:bg-[#3a3a3f] rounded-md transition-colors">
              <svg class="w-5 h-5 text-[#5f6368] dark:text-[#9aa0a6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <!-- Imagen y detalles principales -->
          <div class="flex flex-col items-center mb-8">
            <div class="relative mb-4">
              <!-- Si hay imagen real, mostrarla -->
              <img v-if="getProductImage(selectedProduct)" 
                   :src="getProductImage(selectedProduct)" 
                   @error="handleImageError" 
                   :alt="selectedProduct.name" 
                   class="w-40 h-40 object-cover rounded-2xl border border-gray-100 dark:border-[#3a3a3f]">
              <!-- Placeholder elegante cuando NO hay imagen -->
              <div v-else class="w-40 h-40 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-50 dark:from-[#1e1f20] dark:to-[#282a2c] flex items-center justify-center border border-gray-100 dark:border-[#3a3a3f]">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-14 h-14 rounded-xl bg-gray-200/80 dark:bg-[#3a3a3f] flex items-center justify-center mb-2">
                    <svg class="w-7 h-7 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-15.75 0l1.5-1.875A2.625 2.625 0 018.25 4.5h7.5a2.625 2.625 0 012.25 1.125l1.5 1.875"/>
                    </svg>
                  </div>
                  <span class="text-[12px] font-medium text-gray-400 dark:text-zinc-500">Sin imagen</span>
                </div>
              </div>
            </div>
            <h2 class="text-xl font-medium text-[#1e1f20] dark:text-[#e3e3e3] mb-2">{{ selectedProduct.name }}</h2>
            <div class="flex items-center space-x-2 text-[15px]">
              <span class="px-3 py-1 bg-[#e8f0fe] dark:bg-[#8ab4f8]/20 text-[#1a73e8] dark:text-[#8ab4f8] rounded-full text-[13px] font-medium">
                {{ selectedProduct.category?.name || 'Sin categoría' }}
              </span>
              <span :class="[
                'px-3 py-1 rounded-full text-[13px] font-medium',
                getProductStatus(selectedProduct) 
                  ? 'bg-[#e6f4ea] dark:bg-[#1e8e3e]/20 text-[#1e8e3e] dark:text-[#81c995]' 
                  : 'bg-[#fce8e6] dark:bg-[#d93025]/20 text-[#d93025] dark:text-[#f28b82]'
              ]">
                {{ getProductStatus(selectedProduct) ? 'Activo' : 'Inactivo' }}
              </span>
              <span v-if="(selectedProduct.current_stock || 0) <= (selectedProduct.min_stock || 0)" 
                    class="px-3 py-1 bg-[#fef7e0] dark:bg-[#f9ab00]/20 text-[#e37400] dark:text-[#fdd663] rounded-full text-[13px] font-medium">
                Stock Bajo
              </span>
            </div>
          </div>
          
          <!-- Detalles en grid (Gemini style) -->
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 dark:bg-[#282a2c] rounded-2xl p-4">
              <p class="text-[13px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide mb-1">SKU</p>
              <p class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">{{ selectedProduct.sku || 'Sin SKU' }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-[#282a2c] rounded-2xl p-4">
              <p class="text-[13px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide mb-1">Precio</p>
              <p class="text-base font-semibold text-[#1e8e3e] dark:text-[#81c995]">${{ formatCurrency(selectedProduct.sale_price) }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-[#282a2c] rounded-2xl p-4">
              <p class="text-[13px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide mb-1">Stock Actual</p>
              <p class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">{{ selectedProduct.current_stock || 0 }}</p>
              <div class="flex items-center mt-1 text-[13px] text-[#5f6368] dark:text-[#9aa0a6]">
                <span>Mín: {{ selectedProduct.min_stock || 0 }}</span>
                <span class="mx-2">⬢</span>
                <span>Máx: {{ selectedProduct.max_stock || 0 }}</span>
              </div>
            </div>
            <div class="bg-gray-50 dark:bg-[#282a2c] rounded-2xl p-4">
              <p class="text-[13px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide mb-1">Costo</p>
              <p class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">${{ formatCurrency(selectedProduct.cost_price) }}</p>
              <p class="text-[13px] text-[#5f6368] dark:text-[#9aa0a6] mt-1">
                Margen: {{ selectedProduct.sale_price && selectedProduct.cost_price ? 
                  ((selectedProduct.sale_price - selectedProduct.cost_price) / selectedProduct.cost_price * 100).toFixed(1) + '%' : 
                  '0%' }}
              </p>
            </div>
          </div>
          
          <!-- Sección de descripción -->
          <div v-if="selectedProduct.description" class="bg-gray-50 dark:bg-[#282a2c] rounded-2xl p-4 mb-6">
            <div class="flex items-center mb-2 space-x-2">
              <svg class="w-4 h-4 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
              </svg>
              <h4 class="text-[15px] font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Descripción del Producto</h4>
            </div>
            <p class="text-[15px] text-[#5f6368] dark:text-[#9aa0a6] leading-relaxed">{{ selectedProduct.description }}</p>
          </div>

          <!-- Pie del modal con acciones (Gemini style) -->
          <div class="flex items-center justify-end space-x-3">
            <button @click="editProduct(selectedProduct)" 
                    class="px-5 py-2.5 bg-[#f0f4f9] dark:bg-[#1e1f20] hover:bg-[#e4e9ef] dark:hover:bg-[#282a2c] text-[#1e1f20] dark:text-[#e3e3e3] rounded-full text-[15px] font-medium inline-flex items-center gap-2 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              <span>Editar Producto</span>
            </button>
            <button @click="showViewModal = false" 
                    class="px-5 py-2.5 bg-[#1a73e8] dark:bg-[#8ab4f8] text-white dark:text-[#1e1f20] hover:bg-[#1557b0] dark:hover:bg-[#aecbfa] rounded-full text-[15px] font-medium transition-colors">
              Cerrar
            </button>
          </div>
        </div>
      </div>
      </div>
    </Teleport>

    </div>
  </div>

  <!-- Modal: Crear Proveedor Rápido -->
  <div v-if="showSupplierModal" 
       class="fixed top-0 left-0 right-0 bottom-0 w-full h-full bg-black/80  flex items-center justify-center p-4"
       style="z-index: 60000"
       @click.self="showSupplierModal = false">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-zinc-800 max-w-md w-full animate-fade-in">
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
          <label class="block text-[15px] font-bold text-gray-900 dark:text-white mb-2">Nombre del Proveedor *</label>
          <input v-model="supplierForm.name" 
                 type="text" 
                 required
                 class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                 placeholder="Ej: Distribuidora XYZ">
        </div>

        <div>
          <label class="block text-[15px] font-bold text-gray-900 dark:text-white mb-2">Documento (NIT/CC) *</label>
          <input v-model="supplierForm.document" 
                 type="text" 
                 required
                 class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                 placeholder="Ej: 900123456-7">
        </div>

        <div>
          <label class="block text-[15px] font-bold text-gray-900 dark:text-white mb-2">Persona de Contacto</label>
          <input v-model="supplierForm.contact_name" 
                 type="text"
                 class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                 placeholder="Ej: Juan Pérez">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-[15px] font-bold text-gray-900 dark:text-white mb-2">Teléfono</label>
            <input v-model="supplierForm.phone" 
                   type="tel"
                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="3001234567">
          </div>

          <div>
            <label class="block text-[15px] font-bold text-gray-900 dark:text-white mb-2">Email</label>
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
                  class="px-4 py-2.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white font-bold rounded-lg  transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
            Crear Proveedor
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- �x<span class="mb-[2px] leading-none">-</span> Tooltip: Stock por Bodega -->
  <Teleport to="body">
    <div v-if="stockTooltip.visible" 
         class="fixed z-[9999] bg-white dark:bg-zinc-900 shadow-xl rounded-xl border border-gray-300 dark:border-zinc-700 p-3 min-w-[200px]"
         :style="{ left: stockTooltip.x + 'px', top: stockTooltip.y + 'px' }">
      <div class="flex items-center space-x-2 mb-2 pb-2 border-b border-gray-200 dark:border-zinc-700">
        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <h4 class="text-[15px] font-bold text-gray-900 dark:text-white">Stock por Bodega</h4>
      </div>
      <div v-if="stockTooltip.warehouses && stockTooltip.warehouses.length > 0" class="space-y-2">
        <div v-for="wh in stockTooltip.warehouses" :key="wh.id" class="flex items-center justify-between text-[15px]">
          <div class="flex items-center space-x-2">
            <span class="w-2 h-2 bg-blue-500 dark:bg-blue-400 rounded-full"></span>
            <span class="text-gray-700 dark:text-zinc-300">{{ wh.name }}</span>
          </div>
          <span class="font-bold text-gray-900 dark:text-white">{{ wh.pivot.stock }}</span>
        </div>
      </div>
      <div v-else class="text-[13px] text-gray-500 dark:text-zinc-400 text-center py-2">
        Sin stock asignado
      </div>
    </div>
  </Teleport>

  <!-- Modal: No hay categorías (Teleport para escapar del contenedor) -->
  <Teleport to="body">
    <div v-if="showNoCategoriesModal" 
         class="fixed top-0 left-0 right-0 bottom-0 w-full h-full bg-black/70  flex items-center justify-center p-4"
         style="z-index: 50000; margin: 0 !important;"
         @click.self="showNoCategoriesModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-zinc-800 max-w-md w-full p-6 animate-fade-in">
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
                    class="w-full px-4 py-3 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white font-bold rounded-lg  transition-all duration-200 flex items-center justify-center space-x-2">
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
  </Teleport>

  <!-- Modal: Crear Categoría (Teleport para escapar del contenedor) -->
  <Teleport to="body">
    <div v-if="showCategoryModal" 
         class="fixed top-0 left-0 right-0 bottom-0 w-full h-full bg-black/80  flex items-center justify-center p-4"
         style="z-index: 60000; margin: 0 !important;"
         @click.self="showCategoryModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-zinc-800 max-w-lg w-full animate-fade-in">
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
            <label class="block text-[15px] font-bold text-gray-700 dark:text-zinc-300 mb-2">Nombre de la Categoría *</label>
            <input v-model="categoryForm.name" 
                   type="text" 
                   required
                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Ej: Electrónica, Ropa, Alimentos">
          </div>

          <div>
            <label class="block text-[15px] font-bold text-gray-700 dark:text-zinc-300 mb-2">Descripción</label>
            <textarea v-model="categoryForm.description" 
                      rows="2"
                      class="w-full px-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Descripción opcional de la categoría">
            </textarea>
          </div>

          <!-- Selector de Color -->
          <div>
            <label class="block text-[15px] font-bold text-gray-700 dark:text-zinc-300 mb-2">Color de la Categoría</label>
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
                    class="px-4 py-2.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white font-bold rounded-lg  transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
              Crear Categoría
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>

  <!-- Excel Import Modal -->
  <ExcelImportModal 
    :is-open="showExcelImportModal" 
    @close="showExcelImportModal = false"
    @imported="handleExcelImported"
  />
 
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, watch, nextTick, Teleport, Transition, onActivated } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import apiClient from '../services/apiClient'
import { productsService } from '../services/productsService.js'
import { categoriesService } from '../services/categoriesService.js'
import { warehouseService } from '../services/warehouseService.js'
import { apiCall } from '../services/api.js' // �x<span class="mb-[2px] leading-none">-</span> Para cargar proveedores
import { appStore } from '../store/appStore.js'
import { useUIContextStore } from '../store/uiContextStore.js'
import { useAutoRefresh } from '../composables/useRouteState.js'
import { useScreenScaling } from '../composables/useScreenScaling.js'
import TablePaginator from './TablePaginator.vue'
import ContextualTour from './ContextualTour.vue'
import ExcelImportModal from './ExcelImportModal.vue'
import FashionProductForm from './FashionProductForm.vue'
import FashionProductCard from './FashionProductCard.vue'

// --- ESTADOS PARA TABLA ANIDADA (Variantes) ---
const expandedProducts = ref(new Set());
const toggleProduct = (productId) => {
  if (expandedProducts.value.has(productId)) {
    expandedProducts.value.delete(productId);
  } else {
    expandedProducts.value.add(productId);
  }
};
// ----------------------------------------------

// �x� Store de contexto para IA
const uiContext = useUIContextStore()

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
const emit = defineEmits(['navigate', 'changeModule', 'change-module', 'openQuotationInPos', 'openReturnInPos', 'refresh'])

// Router - DEBE estar a nivel de setup, NO dentro de onMounted
const route = useRoute()
const router = useRouter()

// �x�️ Función utilitaria para manejo inteligente de imágenes
const getProductImage = (product) => {
  // Usar el origen actual (mismo puerto que el frontend/proxy)
  const baseUrl = window.location.origin
  
  // 1. Intentar con el array de imágenes (relación images)
  if (product?.images && Array.isArray(product.images) && product.images.length > 0) {
    const primaryImage = product.images.find(img => img.is_primary) || product.images[0]
    if (primaryImage?.image_url) {
      const imageUrl = primaryImage.image_url
      // Si la imagen es base64, devolverla directamente
      if (imageUrl.startsWith('data:image')) {
        return imageUrl
      }
      // Fix relative URLs for tenant backend - usar el mismo origen
      if (imageUrl.startsWith('/storage')) {
        return `${baseUrl}${imageUrl}`
      }
      // URL externa completa
      if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
        return imageUrl
      }
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
    // Fix relative URLs for tenant backend - usar el mismo origen
    if (imageUrl.startsWith('/storage')) {
      return `${baseUrl}${imageUrl}`
    }
    // URL externa completa
    if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
      return imageUrl
    }
    return imageUrl
  }
  
  return null
}

// �x}� Generar avatar dinámico SVG con iniciales del producto
const generateDynamicAvatar = (name) => {
  // Obtener las primeras 2 letras del nombre
  const initials = (name || 'P')
    .split(' ')
    .map(word => word.charAt(0))
    .slice(0, 2)
    .join('')
    .toUpperCase() || 'P'
  
  // Generar un color único basado en el nombre
  let hash = 0
  for (let i = 0; i < (name || '').length; i++) {
    hash = name.charCodeAt(i) + ((hash << 5) - hash)
  }
  
  // Usar hue del hash para generar color HSL (sin rojo ni rosa)
  const hue = Math.abs(hash % 220) + 140 // Rango 140-360 (evita rojos y rosas)
  const saturation = 70 // Saturación moderada
  const lightness = 35 // Oscuridad adecuada para buen contraste
  
  // Crear SVG inline
  const svg = `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="80" height="80">
      <rect width="80" height="80" rx="12" fill="hsl(${hue}, ${saturation}%, ${lightness}%)"/>
      <text x="40" y="46" font-size="28" font-weight="700" fill="white" text-anchor="middle" dominant-baseline="middle" font-family="Inter, system-ui, sans-serif">${initials}</text>
    </svg>
  `
  
  // Retornar como data URI
  return `data:image/svg+xml,${encodeURIComponent(svg)}`
}

// �xa� Manejar errores de carga de imagen
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
const suppliers = ref([]) // �x<span class="mb-[2px] leading-none">-</span> Lista de proveedores
const searchTerm = ref('')
const categoryFilter = ref('')
const statusFilter = ref('')
const sortBy = ref('name')
const viewMode = ref('table')

// Sistema de notificaciones
const notifications = ref([])
let notificationId = 0

// �x� Sistema de Preferencias del Usuario
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
    }
  } catch (error) {
    console.warn('�a�️ Error cargando preferencias del usuario:', error)
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
    
  } catch (error) {
    console.warn('�a�️ Error guardando preferencias:', error)
    showNotification(
      '�a�️ Error guardando',
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

// �x� Función helper para normalizar el estado del producto
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
const isFashionMode = ref(false) // �x Modo Ropa/Variantes (para formulario)
const isInitializing = ref(true) // �x Control de inicialización para evitar parpadeo

// �x` Sistema de Edición en Línea para Modal de Inventario
const variantChanges = ref({}) // Objeto: { variantId: { stock: true, price: true, cost: true } }
const hasUnsavedChanges = computed(() => Object.keys(variantChanges.value).length > 0)
const changesCount = computed(() => Object.keys(variantChanges.value).length)

// �x<span class="mb-[2px] leading-none">-</span> Computed: Detectar si la tienda es de tipo Fashion (para vista de tarjetas)
const isFashionStore = computed(() => {
  const storeType = appStore.systemSettings?.store_type
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

// �x}� Lista de iconos disponibles (95 iconos) - sincronizada con CategoriesView
const availableIcons = [
  // General
  { id: 'shopping-bag', emoji: '�x:�️', name: 'Compras', category: 'general' },
  { id: 'gift', emoji: '�x}�', name: 'Regalos', category: 'general' },
  { id: 'package', emoji: '�x�', name: 'Paquetería', category: 'general' },
  { id: 'money', emoji: '�x�', name: 'Finanzas', category: 'general' },
  // Comida y Bebidas
  { id: 'food', emoji: '�x<span class="mb-[2px] leading-none">-</span>️', name: 'Comida', category: 'food' },
  { id: 'drink', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Bebidas', category: 'food' },
  { id: 'coffee', emoji: '<span class="mb-[2px] leading-none">-</span>"', name: 'Café', category: 'food' },
  { id: 'wine', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Vino/Licor', category: 'food' },
  { id: 'beer', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Cerveza', category: 'food' },
  { id: 'bread', emoji: '�x�~', name: 'Panadería', category: 'food' },
  { id: 'meat', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Carnes', category: 'food' },
  { id: 'fruit', emoji: '�x�}', name: 'Frutas', category: 'food' },
  { id: 'vegetable', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Verduras', category: 'food' },
  { id: 'candy', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Dulces', category: 'food' },
  { id: 'ice-cream', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Helados', category: 'food' },
  { id: 'pizza', emoji: '�x�"', name: 'Pizza', category: 'food' },
  { id: 'burger', emoji: '�x�', name: 'Hamburguesas', category: 'food' },
  { id: 'chicken', emoji: '�x�', name: 'Pollo', category: 'food' },
  { id: 'fish', emoji: '�x�x', name: 'Pescado', category: 'food' },
  { id: 'cheese', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Lácteos', category: 'food' },
  // Belleza y Cuidado Personal
  { id: 'perfume', emoji: '�x�', name: 'Perfumes', category: 'beauty' },
  { id: 'cosmetics', emoji: '�x', name: 'Cosméticos', category: 'beauty' },
  { id: 'nail', emoji: '�x&', name: 'Manicura', category: 'beauty' },
  { id: 'haircut', emoji: '�x!', name: 'Peluquería', category: 'beauty' },
  { id: 'mirror', emoji: '�x�~', name: 'Espejos', category: 'beauty' },
  // Limpieza
  { id: 'soap', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Jabones', category: 'cleaning' },
  { id: 'cleaning', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Limpieza', category: 'cleaning' },
  { id: 'toilet', emoji: '�xa�', name: 'Baño', category: 'cleaning' },
  // Papelería y Oficina
  { id: 'book', emoji: '�xa', name: 'Libros', category: 'office' },
  { id: 'pencil', emoji: '�S�️', name: 'Papelería', category: 'office' },
  { id: 'scissors', emoji: '�S️', name: 'Artículos Escolares', category: 'office' },
  { id: 'printer', emoji: '�x�️', name: 'Impresión', category: 'office' },
  { id: 'folder', emoji: '�x�', name: 'Archivos', category: 'office' },
  // Moda y Ropa
  { id: 'tshirt', emoji: '�x"', name: 'Ropa', category: 'fashion' },
  { id: 'dress', emoji: '�x', name: 'Vestidos', category: 'fashion' },
  { id: 'jeans', emoji: '�x', name: 'Pantalones', category: 'fashion' },
  { id: 'shoe', emoji: '�xx', name: 'Calzado', category: 'fashion' },
  { id: 'heels', emoji: '�x�', name: 'Tacones', category: 'fashion' },
  { id: 'hat', emoji: '�x}�', name: 'Sombreros', category: 'fashion' },
  { id: 'watch', emoji: '�Ra', name: 'Relojes', category: 'fashion' },
  { id: 'glasses', emoji: '�x', name: 'Gafas', category: 'fashion' },
  { id: 'bag', emoji: '�xS', name: 'Bolsos', category: 'fashion' },
  { id: 'jewelry', emoji: '�x�', name: 'Joyería', category: 'fashion' },
  { id: 'necktie', emoji: '�x', name: 'Corbatas', category: 'fashion' },
  // Salud y Farmacia
  { id: 'pill', emoji: '�x`', name: 'Medicamentos', category: 'health' },
  { id: 'medical', emoji: '�a"️', name: 'Salud', category: 'health' },
  { id: 'syringe', emoji: '�x0', name: 'Inyectables', category: 'health' },
  { id: 'thermometer', emoji: '�xR�️', name: 'Instrumentos', category: 'health' },
  // Niños y Bebés
  { id: 'toy', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Juguetes', category: 'kids' },
  { id: 'baby', emoji: '�x�', name: 'Bebés', category: 'kids' },
  { id: 'bottle', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Biberones', category: 'kids' },
  { id: 'stroller', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Carriolas', category: 'kids' },
  // Tecnología y Electrónica
  { id: 'electronics', emoji: '�x�', name: 'Electrónica', category: 'tech' },
  { id: 'computer', emoji: '�x�', name: 'Computadoras', category: 'tech' },
  { id: 'camera', emoji: '�x�', name: 'Cámaras', category: 'tech' },
  { id: 'headphones', emoji: '�x}�', name: 'Audífonos', category: 'tech' },
  { id: 'keyboard', emoji: '�R�️', name: 'Teclados', category: 'tech' },
  { id: 'mouse', emoji: '�x�️', name: 'Mouse', category: 'tech' },
  { id: 'tv', emoji: '�x�', name: 'Televisores', category: 'tech' },
  { id: 'game', emoji: '�x}�', name: 'Videojuegos', category: 'tech' },
  // Ferretería y Herramientas
  { id: 'tools', emoji: '�x�', name: 'Herramientas', category: 'hardware' },
  { id: 'hammer', emoji: '�x�', name: 'Construcción', category: 'hardware' },
  { id: 'saw', emoji: '�x�a', name: 'Carpintería', category: 'hardware' },
  { id: 'wrench', emoji: '�x�', name: 'Tornillería', category: 'hardware' },
  { id: 'paint', emoji: '�x}�', name: 'Pintura', category: 'hardware' },
  // Mascotas
  { id: 'pet', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Mascotas', category: 'pets' },
  { id: 'dog', emoji: '�x�"', name: 'Perros', category: 'pets' },
  { id: 'cat', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Gatos', category: 'pets' },
  { id: 'fish-pet', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Peces', category: 'pets' },
  { id: 'bird', emoji: '�x�S', name: 'Aves', category: 'pets' },
  // Jardín y Plantas
  { id: 'plant', emoji: '�xR�', name: 'Plantas', category: 'garden' },
  { id: 'flower', emoji: '�xR�', name: 'Flores', category: 'garden' },
  { id: 'tree', emoji: '�xR�', name: 'Árboles', category: 'garden' },
  { id: 'garden-tools', emoji: '�xR�', name: 'Jardinería', category: 'garden' },
  // Deportes
  { id: 'sport', emoji: '�a�', name: 'Deportes', category: 'sports' },
  { id: 'basketball', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Basketball', category: 'sports' },
  { id: 'tennis', emoji: '�x}�', name: 'Tenis', category: 'sports' },
  { id: 'gym', emoji: '�x�', name: 'Gimnasio', category: 'sports' },
  { id: 'bike', emoji: '�xa�', name: 'Ciclismo', category: 'sports' },
  // Automotriz
  { id: 'car', emoji: '�xa', name: 'Automotriz', category: 'automotive' },
  { id: 'motorcycle', emoji: '�x<span class="mb-[2px] leading-none">-</span>️', name: 'Motocicletas', category: 'automotive' },
  { id: 'tire', emoji: '�x:~', name: 'Llantas', category: 'automotive' },
  { id: 'gas', emoji: '�:�', name: 'Combustible', category: 'automotive' },
  // Hogar y Muebles
  { id: 'home', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Hogar', category: 'home' },
  { id: 'furniture', emoji: '�x:9️', name: 'Muebles', category: 'home' },
  { id: 'bed', emoji: '�x:�️', name: 'Colchones', category: 'home' },
  { id: 'lamp', emoji: '�x�', name: 'Iluminación', category: 'home' },
  { id: 'kitchen', emoji: '�x<span class="mb-[2px] leading-none">-</span>', name: 'Cocina', category: 'home' },
  { id: 'decoration', emoji: '�x�️', name: 'Decoración', category: 'home' },
  { id: 'door', emoji: '�xa�', name: 'Puertas', category: 'home' },
  { id: 'key', emoji: '�x', name: 'Cerrajería', category: 'home' }
]

// Función helper para obtener emoji del icono
const getIconEmoji = (iconId) => {
  const icon = availableIcons.find(i => i.id === iconId)
  return icon ? icon.emoji : '�x:�️'
}

// Sistema de imagen dual
const imageUploadMethod = ref('url') // 'file' o 'url'
const previewImage = ref(null)
const currentProductImage = ref(null) // �x�️ Imagen actual del producto (en modo edición)
const deletingImage = ref(false) // �x️ Estado de eliminación de imagen
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
  supplier_id: null, // �x<span class="mb-[2px] leading-none">-</span> Proveedor principal (opcional)
  warehouse_id: null, // �x<span class="mb-[2px] leading-none">-</span> Bodega donde se guardará el producto
  warehouseStock: {}, // �x<span class="mb-[2px] leading-none">-</span> Stock por cada tienda { warehouse_id: cantidad }
  warehouseEnabled: {}, // �S& Control de qué sedes tendrán el producto { warehouse_id: boolean }
  image: '',
  imageFile: null, // �x� Archivo de imagen para subir
  active: true,
  measurement_unit: 'unit', // �x� Unidad de medida (unit, kg, g, m, cm, l, ml)
  allow_decimal: false // �x� Permite cantidades decimales (0.5, 1.25, etc)
})

// �x<span class="mb-[2px] leading-none">-</span> Lista de bodegas disponibles
const warehouses = ref([])
const loadingWarehouses = ref(false)

// �x<span class="mb-[2px] leading-none">-</span> Tooltip de stock por bodega
const stockTooltip = ref({
  visible: false,
  productId: null,
  x: 0,
  y: 0,
  warehouses: []
})

// �a�️ Modal de confirmación para productos sin stock
const showStockWarningModal = ref(false)
const missingFields = ref([])

// Computed properties
const filteredProducts = computed(() => {
  let filtered = products.value

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

const activeProducts = computed(() => products.value.filter(p => getProductStatus(p) !== false).length)
const lowStockProducts = computed(() => {
  let count = 0
  products.value.forEach(p => {
    if (p.variants && p.variants.length > 0) {
      // Contar variantes individuales con stock bajo
      p.variants.forEach(v => {
        if ((v.stock || 0) <= (p.min_stock || 0)) count++
      })
    } else {
      if ((p.current_stock || 0) <= (p.min_stock || 0)) count++
    }
  })
  return count
})
const totalValue = computed(() => 
  products.value.reduce((sum, p) => sum + (parseFloat(p.sale_price || 0) * (p.current_stock || 0)), 0)
)
const uniqueCategories = computed(() => {
  const categoryIds = products.value.map(p => p.category_id).filter(Boolean)
  return new Set(categoryIds).size
})

// �"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"�
// �x� CONTEXTO PARA IA - Información visible en pantalla
// �"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"�
const updateScreenContextForAI = () => {
  // Obtener productos con stock bajo para alertas
  const productosStockBajo = products.value
    .filter(p => (p.current_stock || 0) <= (p.min_stock || 0))
    .slice(0, 10)
    .map(p => ({
      nombre: p.name,
      stockActual: p.current_stock || 0,
      stockMinimo: p.min_stock || 0,
      categoria: p.category_name || 'Sin categoría'
    }))

  // �x<span class="mb-[2px] leading-none">-</span> IMPORTANTE: Obtener productos VISIBLES en pantalla (filtrados)
  // Si hay pocos productos filtrados, incluirlos para que la IA sepa cuáles ve el usuario
  // Helper para obtener nombre del proveedor
  const getSupplierName = (supplierId) => {
    if (!supplierId) return null
    const supplier = suppliers.value.find(s => s.id === supplierId)
    return supplier ? supplier.name : null
  }
  
  const productosVisibles = filteredProducts.value.length <= 10 
    ? filteredProducts.value.map(p => ({
        id: p.id,
        nombre: p.name,
        sku: p.sku || '',
        precio: p.sale_price,
        costo: p.cost_price,
        stock: p.current_stock || 0,
        categoria: p.category_name || 'Sin categoría',
        proveedor: getSupplierName(p.supplier_id) || 'Sin proveedor asignado',
        proveedorId: p.supplier_id || null,
        activo: getProductStatus(p) !== false
      }))
    : null // Si hay muchos, no los incluimos para no sobrecargar

  // �x<span class="mb-[2px] leading-none">-</span> Si hay exactamente 1 producto filtrado, automáticamente es el "producto en contexto"
  const productoEnContexto = filteredProducts.value.length === 1 
    ? filteredProducts.value[0] 
    : selectedProduct.value

  // Datos del contexto
  const contextData = {
    resumenProductos: {
      total: products.value.length,
      activos: activeProducts.value,
      inactivos: products.value.length - activeProducts.value,
      stockBajo: lowStockProducts.value,
      valorInventario: totalValue.value,
      categorias: uniqueCategories.value
    },
    tipoTienda: isFashionStore.value ? 'moda' : 'general',
    // �x� CRÍTICO: Información de categorías para que la IA sepa si puede crear productos
    totalCategorias: categories.value.length,
    sinCategorias: categories.value.length === 0,
    filtrosActivos: {
      busqueda: searchTerm.value || null,
      categoria: categoryFilter.value || null,
      estado: statusFilter.value || null,
      ordenarPor: sortBy.value
    },
    vistaActual: viewMode.value, // 'table' o 'grid'
    productosVisibles: productosVisibles, // �x<span class="mb-[2px] leading-none">-</span> Lista de productos en pantalla (si son pocos)
    cantidadFiltrada: filteredProducts.value.length, // �x<span class="mb-[2px] leading-none">-</span> Cuántos productos ve el usuario
    alertasStockBajo: productosStockBajo,
    // �x<span class="mb-[2px] leading-none">-</span> Usar productoEnContexto (automático si hay 1 filtrado, o el seleccionado)
    productoSeleccionado: productoEnContexto ? {
      id: productoEnContexto.id,
      nombre: productoEnContexto.name,
      sku: productoEnContexto.sku,
      precio: productoEnContexto.sale_price,
      costo: productoEnContexto.cost_price,
      stock: productoEnContexto.current_stock,
      categoria: productoEnContexto.category_name,
      proveedor: getSupplierName(productoEnContexto.supplier_id) || 'Sin proveedor asignado',
      proveedorId: productoEnContexto.supplier_id || null,
      activo: getProductStatus(productoEnContexto)
    } : null,
    
    // �x<span class="mb-[2px] leading-none">-</span> Lista de proveedores disponibles (para que la IA pueda sugerir)
    proveedoresDisponibles: suppliers.value.slice(0, 15).map(s => ({ id: s.id, nombre: s.name })),
    modalAbierto: showProductModal.value ? (isEditing.value ? 'editar' : 'crear') : null,
    sedesDisponibles: availableWarehouses.value.map(w => ({ id: w.id, nombre: w.name })),
    
    // �x� Categorías disponibles para crear productos
    categoriasDisponibles: categories.value.map(c => ({ id: c.id, nombre: c.name })),
    
    // �x� Estado del formulario cuando está abierto
    formularioProducto: showProductModal.value ? {
      nombre: productForm.value.name || '(vacío)',
      precio: productForm.value.price || '(vacío)',
      costo: productForm.value.cost || '(vacío)',
      stock: productForm.value.stock || 0,
      stockMinimo: productForm.value.min_stock || 5,
      sku: productForm.value.sku || '(vacío)',
      descripcion: productForm.value.description || '(vacío)',
      categoriaSeleccionada: categories.value.find(c => c.id === productForm.value.category_id)?.name || '(sin categoría)',
      modoEdicion: isEditing.value,
      camposCompletos: !!(productForm.value.name && productForm.value.category_id && productForm.value.cost > 0 && productForm.value.price > 0),
      camposFaltantes: [
        !productForm.value.name?.trim() ? 'nombre' : null,
        !productForm.value.category_id ? 'categoría' : null,
        !productForm.value.cost || productForm.value.cost <= 0 ? 'precio de costo' : null,
        !productForm.value.price || productForm.value.price <= 0 ? 'precio de venta' : null
      ].filter(Boolean)
    } : null,
    
    instrucciones: {
      buscar: 'Puedo buscar productos por nombre, SKU o código de barras. Solo dime qué buscar.',
      crear: isFashionStore.value 
        ? 'Puedo ayudarte a crear un producto de moda con tallas y colores. Dime el nombre del producto.' 
        : 'Puedo ayudarte a crear un producto. Dime el nombre y te guío paso a paso.',
      camposObligatorios: 'Para crear un producto necesito: nombre, categoría, precio de costo y precio de venta. El stock es opcional (por defecto 0).',
      flujoCreacion: 'Flujo: 1) Abro el modal, 2) Me dices los datos uno por uno, 3) Yo los voy llenando visualmente, 4) Cuando estén listos, guardo el producto.',
      editar: productoEnContexto 
        ? `Puedo editar "${productoEnContexto.name}" directamente. Dime qué campo cambiar (stock, precio, costo).` 
        : 'Busca un producto o dime cuál quieres editar.',
      filtrar: 'Puedo filtrar por productos inactivos, stock bajo, o por categoría.'
    }
  }

  // �x<span class="mb-[2px] leading-none">-</span> Función para normalizar términos de búsqueda (un litro �  1L, medio �  500, etc.)
  const normalizarBusqueda = (texto) => {
    let normalizado = texto.toLowerCase()
    
    // Normalizar unidades de medida
    const reemplazos = [
      // Litros
      { patron: /\bun litro\b/gi, reemplazo: '1L' },
      { patron: /\bun lt\b/gi, reemplazo: '1L' },
      { patron: /\b1 litro\b/gi, reemplazo: '1L' },
      { patron: /\bdos litros?\b/gi, reemplazo: '2L' },
      { patron: /\b2 litros?\b/gi, reemplazo: '2L' },
      { patron: /\bmedio litro\b/gi, reemplazo: '500ml' },
      { patron: /\bcuarto de litro\b/gi, reemplazo: '250ml' },
      // Mililitros
      { patron: /\b(\d+)\s*mililitros?\b/gi, reemplazo: '$1ml' },
      { patron: /\b(\d+)\s*ml\b/gi, reemplazo: '$1ml' },
      // Gramos/Kilos
      { patron: /\bun kilo\b/gi, reemplazo: '1kg' },
      { patron: /\b1 kilo\b/gi, reemplazo: '1kg' },
      { patron: /\b(\d+)\s*kilos?\b/gi, reemplazo: '$1kg' },
      { patron: /\b(\d+)\s*gramos?\b/gi, reemplazo: '$1g' },
      { patron: /\bmedio kilo\b/gi, reemplazo: '500g' },
      // Unidades
      { patron: /\buna unidad\b/gi, reemplazo: '1' },
      { patron: /\b(\d+)\s*unidades?\b/gi, reemplazo: '$1' },
    ]
    
    reemplazos.forEach(({ patron, reemplazo }) => {
      normalizado = normalizado.replace(patron, reemplazo)
    })
    
    return normalizado
  }

  // �x<span class="mb-[2px] leading-none">-</span> Función para buscar con coincidencia flexible
  const buscarProductoFlexible = (texto) => {
    const textoNormalizado = normalizarBusqueda(texto)
    const palabras = textoNormalizado.split(/\s+/).filter(p => p.length > 1)
    
    // Buscar productos que coincidan con TODAS las palabras importantes
    const resultados = products.value.filter(producto => {
      const nombreProducto = producto.name.toLowerCase()
      const skuProducto = (producto.sku || '').toLowerCase()
      const textoCompleto = `${nombreProducto} ${skuProducto}`
      
      // Verificar si todas las palabras están en el nombre/sku
      return palabras.every(palabra => textoCompleto.includes(palabra))
    })
    
    // Si no hay resultados exactos, buscar coincidencia parcial
    if (resultados.length === 0) {
      return products.value.filter(producto => {
        const nombreProducto = producto.name.toLowerCase()
        // Al menos el 60% de las palabras deben coincidir
        const coincidencias = palabras.filter(p => nombreProducto.includes(p))
        return coincidencias.length >= Math.ceil(palabras.length * 0.6)
      })
    }
    
    return resultados
  }

  // Registrar acciones disponibles
  uiContext.registerAction('buscarProducto', async (params) => {
    if (params?.texto) {
      // Usar búsqueda normalizada
      const textoNormalizado = normalizarBusqueda(params.texto)
      searchTerm.value = textoNormalizado
      
      // Esperar un momento para que se actualice el filtro
      await nextTick()
      
      const resultados = filteredProducts.value.length
      
      // Si no hay resultados con la búsqueda normalizada, intentar búsqueda flexible
      if (resultados === 0) {
        const resultadosFlexibles = buscarProductoFlexible(params.texto)
        if (resultadosFlexibles.length > 0) {
          // Buscar por el nombre del primer resultado
          searchTerm.value = resultadosFlexibles[0].name.split(' ')[0]
          await nextTick()
          return { 
            success: true, 
            message: `Encontré "${resultadosFlexibles[0].name}" que podría ser lo que buscas`,
            resultados: filteredProducts.value.length,
            productoEncontrado: resultadosFlexibles[0].name
          }
        }
      }
      
      return { success: true, message: `Buscando "${textoNormalizado}"...`, resultados }
    }
    return { success: false, message: 'Dime qué producto buscar' }
  })

  uiContext.registerAction('limpiarBusqueda', async () => {
    searchTerm.value = ''
    categoryFilter.value = ''
    statusFilter.value = ''
    return { success: true, message: 'Filtros limpiados' }
  })

  uiContext.registerAction('filtrarPorEstado', async (params) => {
    if (params?.estado === 'inactivos') {
      statusFilter.value = 'inactive'
      return { success: true, message: 'Mostrando productos inactivos', cantidad: products.value.filter(p => !getProductStatus(p)).length }
    } else if (params?.estado === 'activos') {
      statusFilter.value = 'active'
      return { success: true, message: 'Mostrando productos activos', cantidad: activeProducts.value }
    } else if (params?.estado === 'stock_bajo') {
      statusFilter.value = 'low-stock'
      return { success: true, message: 'Mostrando productos con stock bajo', cantidad: lowStockProducts.value }
    }
    return { success: false, message: 'Estado no reconocido. Usa: activos, inactivos, stock_bajo' }
  })

  // �x<span class="mb-[2px] leading-none">-</span> NUEVA ACCI�N: Buscar proveedor de un producto
  uiContext.registerAction('buscarProveedorDeProducto', ({ nombreProducto }) => {
    if (!nombreProducto) {
      return { success: false, message: 'Dime el nombre del producto para buscar su proveedor' }
    }
    
    // Buscar el producto
    const resultados = buscarProductoFlexible(nombreProducto)
    
    if (resultados.length === 0) {
      return { 
        success: false, 
        message: `No encontré el producto "${nombreProducto}"`,
        productosDisponibles: products.value.slice(0, 10).map(p => p.name)
      }
    }
    
    const producto = resultados[0]
    const proveedor = suppliers.value.find(s => s.id === producto.supplier_id)
    
    if (proveedor) {
      return {
        success: true,
        productoEncontrado: producto.name,
        proveedorAsignado: {
          id: proveedor.id,
          nombre: proveedor.name,
          telefono: proveedor.phone,
          email: proveedor.email
        },
        message: `El producto "${producto.name}" es de "${proveedor.name}". ¿Quieres crear una orden de compra para este proveedor?`
      }
    } else {
      return {
        success: true,
        productoEncontrado: producto.name,
        proveedorAsignado: null,
        message: `El producto "${producto.name}" no tiene proveedor asignado. ¿Quieres asignarle uno?`,
        proveedoresDisponibles: suppliers.value.slice(0, 10).map(s => s.name)
      }
    }
  })

  uiContext.registerAction('abrirCrearProducto', async () => {
    // �x� VALIDACI�N: Verificar si hay categorías antes de intentar abrir
    if (!categories.value || categories.value.length === 0) {
      showNoCategoriesModal.value = true
      return { 
        success: false, 
        code: 'SIN_CATEGORIAS',
        message: 'No hay categorías disponibles. Antes de crear productos, necesitas tener al menos una categoría. ¿Cómo quieres que se llame tu primera categoría?',
        totalCategorias: 0
      }
    }
    
    await openCreateModal()
    
    // Verificar si realmente se abrió el modal de crear producto
    if (!showProductModal.value) {
      return { 
        success: false, 
        message: 'No se pudo abrir el formulario de crear producto.' 
      }
    }
    
    return { 
      success: true, 
      message: isFashionStore.value 
        ? 'Modal de nuevo producto de moda abierto. ¿Cómo se llama el producto?' 
        : 'Modal de nuevo producto abierto. ¿Cómo se llama el producto?',
      totalCategorias: categories.value.length
    }
  })

  // �x� Acción para recargar categorías (usada cuando la IA crea una categoría rápida)
  uiContext.registerAction('recargarCategorias', async () => {
    try {
      await loadCategories()
      return { 
        success: true, 
        message: `Categorías recargadas. Ahora tienes ${categories.value.length} categorías.`,
        totalCategorias: categories.value.length
      }
    } catch (err) {
      console.error('Error recargando categorías:', err)
      return { success: false, message: 'Error al recargar categorías' }
    }
  })

  uiContext.registerAction('seleccionarProducto', async (params) => {
    if (params?.id) {
      const producto = products.value.find(p => p.id === params.id)
      if (producto) {
        selectedProduct.value = producto
        showViewModal.value = true
        return { success: true, message: `Mostrando detalles de ${producto.name}` }
      }
    } else if (params?.nombre) {
      // Usar búsqueda flexible para encontrar el producto
      const resultados = buscarProductoFlexible(params.nombre)
      if (resultados.length > 0) {
        selectedProduct.value = resultados[0]
        showViewModal.value = true
        return { success: true, message: `Mostrando detalles de ${resultados[0].name}` }
      }
      return { success: false, message: `No encontré un producto con "${params.nombre}"` }
    }
    return { success: false, message: 'Dime el nombre o ID del producto' }
  })

  uiContext.registerAction('editarProductoSeleccionado', async () => {
    if (selectedProduct.value) {
      await editProduct(selectedProduct.value)
      return { success: true, message: `Abriendo editor para ${selectedProduct.value.name}` }
    }
    return { success: false, message: 'Primero selecciona un producto' }
  })

  // �x� NUEVA ACCI�N: Editar campo específico y guardar automáticamente
  uiContext.registerAction('editarCampoProducto', async (params) => {
    const { nombreProducto, campo, nuevoValor } = params || {}
    
    if (!campo || nuevoValor === undefined) {
      return { success: false, message: 'Necesito saber qué campo cambiar y el nuevo valor' }
    }
    
    // �x<span class="mb-[2px] leading-none">-</span> INTELIGENCIA: Si no se especifica producto, usar el que está en contexto (filtrado único)
    let producto = null
    
    if (nombreProducto) {
      // Buscar el producto por nombre
      const resultados = buscarProductoFlexible(nombreProducto)
      if (resultados.length === 0) {
        return { success: false, message: `No encontré el producto "${nombreProducto}"` }
      }
      producto = resultados[0]
    } else {
      // Si hay exactamente 1 producto filtrado, usar ese automáticamente
      if (filteredProducts.value.length === 1) {
        producto = filteredProducts.value[0]
      } else if (selectedProduct.value) {
        producto = selectedProduct.value
      } else {
        return { success: false, message: 'Hay varios productos. Dime cuál quieres editar por nombre.' }
      }
    }
    
    try {
      // Mapear campos a los nombres del API
      const campoMap = {
        'stock': 'current_stock',
        'precio': 'sale_price',
        'costo': 'cost_price',
        'nombre': 'name',
        'descripcion': 'description',
        'sku': 'sku'
      }
      
      const campoAPI = campoMap[campo] || campo
      
      // �x� IMPORTANTE: Obtener datos completos del producto primero
      const productoCompleto = await productsService.getById(producto.id)
      
      if (!productoCompleto.success || !productoCompleto.data) {
        return { success: false, message: 'Error obteniendo datos del producto' }
      }
      
      const datosOriginales = productoCompleto.data
      
      // Preparar datos COMPLETOS para actualizar (solo cambiando el campo necesario)
      const datosActualizacion = {
        name: datosOriginales.name,
        description: datosOriginales.description || '',
        sku: datosOriginales.sku || '',
        category_id: datosOriginales.category_id,
        supplier_id: datosOriginales.supplier_id,
        cost_price: datosOriginales.cost_price,
        sale_price: datosOriginales.sale_price,
        current_stock: datosOriginales.current_stock,
        min_stock: datosOriginales.min_stock,
        max_stock: datosOriginales.max_stock,
        is_active: datosOriginales.is_active ?? datosOriginales.active ?? true,
        // Sobrescribir solo el campo que queremos cambiar
        [campoAPI]: campo === 'stock' ? parseInt(nuevoValor) : 
                    (campo === 'precio' || campo === 'costo') ? parseFloat(nuevoValor) : 
                    nuevoValor
      }
      
      // Llamar al API para actualizar
      const response = await productsService.update(producto.id, datosActualizacion)
      
      if (response.success) {
        // Refrescar la lista de productos
        await loadProducts()
        
        // �a�️ NO limpiar searchTerm - mantener el producto filtrado para ediciones consecutivas
        // searchTerm.value = ''  // REMOVIDO: Esto causaba que el contexto se perdiera
        
        // Cerrar cualquier modal abierto
        showProductModal.value = false
        showViewModal.value = false
        
        const campoLabels = {
          stock: 'stock',
          precio: 'precio de venta',
          costo: 'precio de costo',
          nombre: 'nombre',
          descripcion: 'descripción',
          sku: 'SKU'
        }
        
        return { 
          success: true, 
          message: `�S& Listo! Actualicé el ${campoLabels[campo] || campo} de "${producto.name}" a ${nuevoValor}. Puedes verificarlo en la lista.`
        }
      } else {
        return { success: false, message: `Error al actualizar: ${response.message || 'Error desconocido'}` }
      }
    } catch (error) {
      console.error('Error editando producto:', error)
      return { success: false, message: `Error: ${error.message}` }
    }
  })

  // Actualizar el store de contexto
  uiContext.setScreenData(contextData)

  // �"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"�
  // �x<span class="mb-[2px] leading-none">-</span> ACCIONES PARA CREAR PRODUCTOS - CONCIENCIA DE PANTALLA
  // �"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"<span class="mb-[2px] leading-none">-</span>"�
  
  // Acción para llenar campos del formulario visualmente
  uiContext.registerAction('llenarCampoProducto', ({ campo, valor }) => {
    if (!showProductModal.value) {
      return { success: false, message: 'Primero abre el modal con abrirCrearProducto' }
    }
    
    // ═══════════════════════════════════════════════════════════
    // MODO FASHION: Llenar FashionProductForm
    // ═══════════════════════════════════════════════════════════
    if (isFashionMode.value && fashionFormRef.value) {
      const fashionForm = fashionFormRef.value.form
      const camposFashion = {
        'nombre': 'name', 'name': 'name',
        'descripcion': 'description', 'description': 'description',
        'sku': 'sku', 'codigo': 'sku',
        'categoria': 'category_id', 'category': 'category_id', 'category_id': 'category_id',
        'proveedor': 'supplier_id', 'supplier': 'supplier_id',
        'talla': '_talla', 'tallas': '_talla', 'size': '_talla', 'sizes': '_talla',
        'color': '_color', 'colores': '_color', 'colors': '_color',
        'precio': '_precio', 'price': '_precio', 'precio_venta': '_precio',
        'costo': '_costo', 'cost': '_costo', 'precio_costo': '_costo',
        'stock': '_stock', 'stock_inicial': '_stock'
      }
      
      const campoReal = camposFashion[campo.toLowerCase().trim()]
      if (!campoReal) {
        return { success: false, message: `Campo "${campo}" no reconocido. Campos válidos para moda: nombre, precio, costo, stock, categoria, tallas, colores, descripcion, sku, proveedor` }
      }
      
      // Categoría
      if (campoReal === 'category_id') {
        const categoria = categories.value.find(c => c.name.toLowerCase().includes(valor.toLowerCase()))
        if (categoria) {
          fashionForm.category_id = categoria.id
          return { success: true, message: `Categoría "${categoria.name}" seleccionada`, formularioActual: { nombre: fashionForm.name || '(vacío)', categoria: categoria.name } }
        }
        const cats = categories.value.map(c => c.name).join(', ')
        return { success: false, message: `No encontré categoría "${valor}". Disponibles: ${cats}` }
      }
      
      // Proveedor
      if (campoReal === 'supplier_id') {
        // Try to find by partial name - suppliers may already be loaded
        return { success: true, message: `Proveedor se asigna manualmente en el formulario.` }
      }
      
      // Tallas (valores separados por coma)
      if (campoReal === '_talla') {
        const tallaOption = fashionForm.options.find(o => o.name === 'Talla')
        if (tallaOption) {
          const nuevasTallas = valor.split(/[,;\/\s]+/).map(t => t.trim().toUpperCase()).filter(Boolean)
          nuevasTallas.forEach(t => { if (!tallaOption.values.includes(t)) tallaOption.values.push(t) })
          return { success: true, message: `Tallas agregadas: ${tallaOption.values.join(', ')}`, formularioActual: { nombre: fashionForm.name || '(vacío)', tallas: tallaOption.values.join(', '), colores: fashionForm.options.find(o => o.name === 'Color')?.values.join(', ') || '(vacío)' } }
        }
      }
      
      // Colores (valores separados por coma)
      if (campoReal === '_color') {
        const colorOption = fashionForm.options.find(o => o.name === 'Color')
        if (colorOption) {
          const nuevosColores = valor.split(/[,;\/]+/).map(c => c.trim()).filter(Boolean)
          nuevosColores.forEach(c => { if (!colorOption.values.find(v => v.toLowerCase() === c.toLowerCase())) colorOption.values.push(c) })
          return { success: true, message: `Colores agregados: ${colorOption.values.join(', ')}`, formularioActual: { nombre: fashionForm.name || '(vacío)', tallas: fashionForm.options.find(o => o.name === 'Talla')?.values.join(', ') || '(vacío)', colores: colorOption.values.join(', ') } }
        }
      }
      
      // Precio/Costo/Stock → se aplican a simpleProduct del fashion form
      if (campoReal === '_precio' || campoReal === '_costo' || campoReal === '_stock') {
        const numVal = parseFloat(valor) || 0
        // Access the simpleProduct reactive from the exposed form ref
        // Fashion form uses simpleProduct for base price/cost when no variants yet
        const spKey = campoReal === '_precio' ? 'price' : campoReal === '_costo' ? 'cost' : 'stock'
        // These will be applied when variants are generated, or if it's a simple fashion product
        if (fashionFormRef.value.simpleProduct) {
          fashionFormRef.value.simpleProduct[spKey] = numVal
        }
        return { success: true, message: `${campo} establecido a ${numVal}`, formularioActual: { nombre: fashionForm.name || '(vacío)', precio: fashionFormRef.value.simpleProduct?.price || '(vacío)', costo: fashionFormRef.value.simpleProduct?.cost || '(vacío)', stock: fashionFormRef.value.simpleProduct?.stock || 0 } }
      }
      
      // Campos de texto directos
      fashionForm[campoReal] = valor
      return { success: true, message: `Campo "${campo}" actualizado a "${valor}"`, formularioActual: { nombre: fashionForm.name || '(vacío)', categoria: categories.value.find(c => c.id === fashionForm.category_id)?.name || '(sin categoría)', tallas: fashionForm.options.find(o => o.name === 'Talla')?.values.join(', ') || '(vacío)', colores: fashionForm.options.find(o => o.name === 'Color')?.values.join(', ') || '(vacío)' } }
    }
    
    // ═══════════════════════════════════════════════════════════
    // MODO GENERAL: Llenar productForm (formulario normal)
    // ═══════════════════════════════════════════════════════════
    const camposValidos = {
      'nombre': 'name',
      'name': 'name',
      'precio': 'price',
      'price': 'price',
      'precio_venta': 'price',
      'costo': 'cost',
      'cost': 'cost',
      'precio_costo': 'cost',
      'descripcion': 'description',
      'description': 'description',
      'sku': 'sku',
      'codigo': 'sku',
      'barcode': 'barcode',
      'codigo_barras': 'barcode',
      'stock': 'stock',
      'stock_inicial': 'stock',
      'stock_minimo': 'min_stock',
      'min_stock': 'min_stock',
      'stock_maximo': 'max_stock',
      'max_stock': 'max_stock',
      'categoria': 'category_id',
      'category': 'category_id',
      'category_id': 'category_id'
    }
    
    const campoReal = camposValidos[campo.toLowerCase().trim()]
    
    if (!campoReal) {
      return { success: false, message: `Campo "${campo}" no reconocido. Campos válidos: nombre, precio, costo, descripcion, sku, codigo_barras, stock, stock_minimo, categoria` }
    }
    
    // Si es categoría, buscar por nombre
    if (campoReal === 'category_id') {
      const categoria = categories.value.find(c => 
        c.name.toLowerCase().includes(valor.toLowerCase())
      )
      if (categoria) {
        productForm.value.category_id = categoria.id
        return { 
          success: true, 
          message: `Categoría "${categoria.name}" seleccionada`,
          formularioActual: {
            nombre: productForm.value.name,
            precio: productForm.value.price,
            costo: productForm.value.cost,
            categoria: categoria.name
          }
        }
      } else {
        // Listar categorías disponibles
        const cats = categories.value.map(c => c.name).join(', ')
        return { 
          success: false, 
          message: `No encontré categoría "${valor}". Categorías disponibles: ${cats}. ¿Quieres que cree una nueva categoría con ese nombre?`
        }
      }
    }
    
    // Convertir valores numéricos
    if (['price', 'cost', 'stock', 'min_stock', 'max_stock'].includes(campoReal)) {
      const valorNumerico = parseFloat(valor) || 0
      productForm.value[campoReal] = valorNumerico
      
      // SMART STOCK: Si se asigna stock y hay múltiples bodegas,
      // auto-habilitar la bodega principal y asignar el stock ahí
      if (campoReal === 'stock' && valorNumerico > 0 && availableWarehouses.value.length >= 2) {
        const defaultWarehouse = availableWarehouses.value.find(w => w.is_default) || availableWarehouses.value[0]
        if (defaultWarehouse) {
          productForm.value.warehouseEnabled[defaultWarehouse.id] = true
          productForm.value.warehouseStock[defaultWarehouse.id] = valorNumerico
        }
      }
      
      // Si hay solo 1 bodega, sincronizar también con warehouseStock
      if (campoReal === 'stock' && valorNumerico > 0 && availableWarehouses.value.length === 1) {
        const warehouse = availableWarehouses.value[0]
        if (warehouse) {
          productForm.value.warehouseEnabled[warehouse.id] = true
          productForm.value.warehouseStock[warehouse.id] = valorNumerico
        }
      }
    } else {
      productForm.value[campoReal] = valor
    }
    
    // Mensaje contextual según asignación de stock a sede
    let mensajeExtra = ''
    if (campoReal === 'stock' && (parseFloat(valor) || 0) > 0 && availableWarehouses.value.length >= 2) {
      const sedeAsignada = availableWarehouses.value.find(w => w.is_default) || availableWarehouses.value[0]
      if (sedeAsignada) {
        mensajeExtra = ` (asignado a sede "${sedeAsignada.name}")`
      }
    }
    
    return { 
      success: true, 
      message: `Campo "${campo}" actualizado a "${valor}"${mensajeExtra}`,
      formularioActual: {
        nombre: productForm.value.name || '(vacío)',
        precio: productForm.value.price || '(vacío)',
        costo: productForm.value.cost || '(vacío)',
        stock: productForm.value.stock || 0,
        categoria: categories.value.find(c => c.id === productForm.value.category_id)?.name || '(sin categoría)'
      }
    }
  })
  
  // Acción para guardar el producto
  uiContext.registerAction('guardarProducto', async () => {
    if (!showProductModal.value) {
      return { success: false, message: 'No hay formulario de producto abierto' }
    }
    
    // ═══ MODO FASHION ═══
    if (isFashionMode.value && fashionFormRef.value) {
      const ff = fashionFormRef.value.form
      const faltantes = []
      if (!ff.name?.trim()) faltantes.push('nombre')
      if (!ff.category_id) faltantes.push('categoría')
      if (faltantes.length > 0) {
        return { success: false, message: `Faltan campos obligatorios: ${faltantes.join(', ')}` }
      }
      try {
        fashionFormRef.value.handleSubmit()
        return { success: true, message: `Producto de moda "${ff.name}" guardado exitosamente.` }
      } catch (error) {
        return { success: false, message: `Error al guardar: ${error.message}` }
      }
    }
    
    // ═══ MODO GENERAL ═══
    // Verificar campos obligatorios
    const faltantes = []
    if (!productForm.value.name?.trim()) faltantes.push('nombre')
    if (!productForm.value.category_id) faltantes.push('categoría')
    if (!productForm.value.cost || productForm.value.cost <= 0) faltantes.push('precio de costo')
    if (!productForm.value.price || productForm.value.price <= 0) faltantes.push('precio de venta')
    
    if (faltantes.length > 0) {
      return { 
        success: false, 
        message: `Faltan campos obligatorios: ${faltantes.join(', ')}. Por favor proporciona estos datos.` 
      }
    }
    
    try {
      await saveProduct(true) // skip validation modal
      return { 
        success: true, 
        message: `Producto "${productForm.value.name}" creado exitosamente.` 
      }
    } catch (error) {
      return { success: false, message: `Error al guardar: ${error.message}` }
    }
  })
  
  // Acción para cerrar el modal
  uiContext.registerAction('cerrarModalProducto', () => {
    showProductModal.value = false
    return { success: true, message: 'Modal cerrado' }
  })
  
  // �x� Acción para cerrar el modal de advertencia de "sin categorías"
  uiContext.registerAction('cerrarModalAdvertencia', () => {
    showNoCategoriesModal.value = false
    return { success: true, message: 'Modal de advertencia cerrado' }
  })
  
  // �x� Acción para abrir el modal de crear categoría (desde la advertencia)
  uiContext.registerAction('abrirModalCrearCategoria', () => {
    // Cerrar modal de advertencia si está abierto
    showNoCategoriesModal.value = false
    // Abrir modal de crear categoría
    categoryForm.value = {
      name: '',
      description: '',
      icon: 'shopping-bag',
      color: '#3b82f6'
    }
    showCategoryModal.value = true
    return { 
      success: true, 
      message: 'Modal de crear categoría abierto. Escribe el nombre de la categoría.',
      modalAbierto: 'crear-categoria'
    }
  })
  
  // �x� Acción para llenar el nombre de la categoría en el modal
  uiContext.registerAction('llenarNombreCategoria', ({ nombre }) => {
    if (!nombre?.trim()) {
      return { success: false, message: 'Proporciona un nombre para la categoría' }
    }
    categoryForm.value.name = nombre.trim()
    return { 
      success: true, 
      message: `Nombre "${nombre}" asignado. ¿Quieres guardar la categoría?`,
      nombreAsignado: nombre.trim()
    }
  })
  
  // �x� Acción para guardar la categoría desde el modal y luego abrir crear producto
  uiContext.registerAction('guardarCategoriaYAbrirProducto', async () => {
    try {
      if (!categoryForm.value.name?.trim()) {
        return { success: false, message: 'El nombre de la categoría está vacío' }
      }
      
      const response = await categoriesService.create(categoryForm.value)
      
      if (response.success || response.data) {
        // Recargar categorías
        await loadCategories()
        
        // Cerrar modal de categoría
        showCategoryModal.value = false
        
        // Obtener el ID de la nueva categoría
        const nuevaCat = categories.value.find(c => c.name.toLowerCase() === categoryForm.value.name.toLowerCase().trim())
        
        // Ahora abrir el modal de crear producto
        await openCreateModal()
        
        // Pre-seleccionar la categoría recién creada
        if (nuevaCat) {
          productForm.value.category_id = nuevaCat.id
        }
        
        return { 
          success: true, 
          message: `¡Categoría "${categoryForm.value.name}" creada! Ahora el formulario de producto está abierto. ¿Cómo se llama el producto?`,
          categoriaCreada: categoryForm.value.name,
          modalProductoAbierto: true
        }
      }
      return { success: false, message: 'Error al crear la categoría' }
    } catch (error) {
      return { success: false, message: `Error: ${error.message}` }
    }
  })
  
  // Acción para crear categoría rápida
  uiContext.registerAction('crearCategoriaRapida', async ({ nombre }) => {
    if (!nombre?.trim()) {
      return { success: false, message: 'Debes proporcionar un nombre para la categoría' }
    }
    
    try {
      const response = await categoriesService.create({
        name: nombre.trim(),
        description: `Categoría creada por voz: ${nombre.trim()}`,
        is_active: true
      })
      
      if (response.success || response.data) {
        // Recargar categorías
        await loadCategories()
        
        // Si el modal está abierto, seleccionar la nueva categoría
        const nuevaCat = categories.value.find(c => c.name.toLowerCase() === nombre.toLowerCase().trim())
        if (nuevaCat && showProductModal.value) {
          productForm.value.category_id = nuevaCat.id
        }
        
        return { 
          success: true, 
          message: `Categoría "${nombre}" creada exitosamente${showProductModal.value ? ' y seleccionada en el formulario' : ''}` 
        }
      }
      return { success: false, message: 'Error al crear la categoría' }
    } catch (error) {
      return { success: false, message: `Error: ${error.message}` }
    }
  })
}

// Watcher para actualizar contexto cuando cambian los productos o filtros
watch([products, searchTerm, categoryFilter, statusFilter, selectedProduct, showProductModal, productForm], () => {
  updateScreenContextForAI()
}, { deep: true })

// Métodos de utilidad
const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO').format(parseFloat(value || 0))
}

// �x� Formatear número simple (para stock)
const formatNumber = (value) => {
  return new Intl.NumberFormat('es-CO').format(parseInt(value || 0))
}

// �x� Formatear número para inputs (con separadores)
const formatInputNumber = (value) => {
  const num = parseFloat(value || 0)
  if (isNaN(num)) return '0'
  return new Intl.NumberFormat('es-CO', { 
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
  }).format(num)
}

// �x}� Handler para input de costo con formato
const handleCostInput = (event, variant) => {
  const rawValue = event.target.value.replace(/\./g, '').replace(/,/g, '.')
  const numValue = parseFloat(rawValue) || 0
  variant.editableCost = numValue
  markVariantChanged(variant.id)
}

// �x}� Handler para input de precio con formato
const handlePriceInput = (event, variant) => {
  const rawValue = event.target.value.replace(/\./g, '').replace(/,/g, '.')
  const numValue = parseFloat(rawValue) || 0
  variant.editablePrice = numValue
  markVariantChanged(variant.id)
}

// �x<span class="mb-[2px] leading-none">-</span>️ Formatear opciones de variante para mostrar
const getVariantOptionsArray = (variant) => {
  try {
    const summary = variant.options_summary || variant.options;
    if (!summary) return [];
    const options = typeof summary === 'string' ? JSON.parse(summary) : summary;
    return Array.isArray(options) ? options : [];
  } catch (e) { return []; }
};

const getVariantColor = (variant) => {
  if (variant.sku_fields?.Color?.value) return variant.sku_fields.Color.value;
  if (variant.colorCode) return variant.colorCode;
  const opts = getVariantOptionsArray(variant);
  const colorOpt = opts.find(o => (o.name || '').toLowerCase() === 'color' || (o.value && String(o.value).startsWith('#')));
  return colorOpt ? colorOpt.value : null;
};

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

// �x�️ Índice de imagen seleccionada para galería
const selectedImageIndex = ref(0)

// �x� Helper: Convertir URL de imagen a URL completa del backend
const processImageUrl = (url) => {
  if (!url || typeof url !== 'string' || !url.trim()) return null
  
  const trimmedUrl = url.trim()
  
  // Si ya es una URL HTTP completa, devolverla
  if (trimmedUrl.startsWith('http://') || trimmedUrl.startsWith('https://')) {
    return trimmedUrl
  }
  
  // Si es data URI (base64), devolverla
  if (trimmedUrl.startsWith('data:image')) {
    return trimmedUrl
  }
  
  // Usar el origen actual (mismo puerto que el frontend/proxy)
  const backendUrl = window.location.origin
  
  // Si es ruta relativa de Laravel Storage, convertir a URL absoluta
  if (trimmedUrl.startsWith('/storage')) {
    return `${backendUrl}${trimmedUrl}`
  }
  
  // Si no empieza con /, agregar /storage/
  if (!trimmedUrl.startsWith('/')) {
    return `${backendUrl}/storage/${trimmedUrl}`
  }
  
  // Ruta relativa genérica
  return `${backendUrl}${trimmedUrl}`
}

// �x� Computed: Lista de imágenes del producto seleccionado
const selectedProductImages = computed(() => {
  if (!selectedProduct.value) return []
  
  const images = []
  
  // 1. Cargar imágenes de la galería (product_images)
  if (selectedProduct.value.images && Array.isArray(selectedProduct.value.images)) {
    selectedProduct.value.images.forEach(img => {
      const url = img.url || img.image_url
      const processedUrl = processImageUrl(url)
      if (processedUrl) {
        images.push(processedUrl)
      }
    })
  }
  
  // 2. Si no hay galería, usar image_url principal
  if (images.length === 0 && selectedProduct.value.image_url) {
    const processedUrl = processImageUrl(selectedProduct.value.image_url)
    if (processedUrl) {
      images.push(processedUrl)
    }
  }
  
  // 3. NO usar fallback de imagen rota - dejamos array vacío para mostrar placeholder elegante
  return images
})

// �x� Computed: Imagen principal seleccionada
const selectedProductMainImage = computed(() => {
  const images = selectedProductImages.value
  const index = selectedImageIndex.value
  // Si no hay imágenes, usar función getProductImage que genera placeholder elegante
  if (images.length === 0) {
    return getProductImage(selectedProduct.value)
  }
  return images[index] || images[0]
})

// �x Helpers para productos con variantes (FASHION)
const hasVariants = (product) => {
  return product.variants && product.variants.length > 0
}

// �x}� Determinar si debe mostrar el modal de Fashion (independiente de si tiene variantes)
const isFashionProduct = (product) => {
  // �S& PRIORIDAD 1: Campo store_type explícito (nueva lógica)
  if (product.store_type) {
    return product.store_type === 'fashion'
  }
  
  // �S& PRIORIDAD 2: Campo legacy store_category
  if (product.store_category) {
    return product.store_category === 'fashion'
  }
  
  // �S& PRIORIDAD 3: Detectar por product_type 'variable' (productos con variantes = fashion)
  if (product.product_type === 'variable') {
    return true
  }
  
  // �S& PRIORIDAD 4: Fallback - si tiene variantes reales, es fashion
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

const hasVariantWithLowStock = (product) => {
  if (!product.variants || !Array.isArray(product.variants)) return false;
  return product.variants.some(v => (v.stock || 0) <= (product.min_stock || 0));
};

const getVariantStockAnalysis = (product) => {
  if (!hasVariants(product)) return null;

  let criticalCount = 0;
  let lowCount = 0;
  let tooltipLines = [];

  product.variants.forEach(v => {
    const stock = v.stock || 0;
    const minStock = product.min_stock || 0;
    
    let variantName = v.name || 'Variante';
    const opts = getVariantOptionsArray(v);
    if (opts && opts.length) {
        variantName = opts.map(o => `${o.name}: ${o.text || o.value}`).join(', ');
    }
    tooltipLines.push(`${variantName} \u2192 ${stock} unidades`);

    if (stock === 0) {
      criticalCount++;
    } else if (stock <= minStock) {
      lowCount++;
    }
  });

  let status = 'ok';
  let message = '';
  if (criticalCount > 0) {
      status = 'critical';
      message = `${criticalCount} sin stock`;
  } else if (lowCount > 0) {
      status = 'low';
      message = `${lowCount} variante baja`;
  }

  return {
    status,
    criticalCount,
    lowCount,
    message,
    tooltipText: tooltipLines.join('\n')
  };
};

const getVariantWarehouseStockAnalysis = (product, warehouseId) => {
  if (!hasVariants(product)) return null;

  let criticalCount = 0;
  let lowCount = 0;
  let tooltipLines = [];

  product.variants.forEach(v => {
    let stock = 0;
    if (v.warehouses) {
      const whStockObj = v.warehouses.find(w => w.warehouse_id === warehouseId || w.id === warehouseId);
      if (whStockObj) stock = parseFloat(whStockObj.pivot?.stock ?? whStockObj.stock) || 0;
    }
    const minStock = product.min_stock || 0;
    
    let variantName = v.name || 'Variante';
    const opts = getVariantOptionsArray(v);
    if (opts && opts.length) {
        variantName = opts.map(o => `${o.name}: ${o.text || o.value}`).join(', ');
    }
    tooltipLines.push(`${variantName} \u2192 ${stock} unidades`);

    if (stock === 0) {
      criticalCount++;
    } else if (stock <= minStock) {
      lowCount++;
    }
  });

  let status = 'ok';
  let message = '';
  if (criticalCount > 0) {
      status = 'critical';
      message = `${criticalCount} sin stock`;
  } else if (lowCount > 0) {
      status = 'low';
      message = `${lowCount} variante baja`;
  }

  return {
    status,
    criticalCount,
    lowCount,
    message,
    tooltipText: tooltipLines.join('\n')
  };
};

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
      _t: Date.now() // �S& Cache busting para forzar recarga real
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
    
    const response = await productsService.getAll(params)
    
    // La API devuelve datos paginados, extraer el array de productos
    if (response.data && response.data.data) {
      products.value = response.data.data || []
    } else {
      products.value = response.data || []
    }
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
  } catch (error) {
    console.error('Error cargando categorías:', error)
  }
}

// �x<span class="mb-[2px] leading-none">-</span> Cargar proveedores activos (optimizado - endpoint ligero sin analytics)
const loadSuppliers = async () => {
  try {
    // �S& OPTIMIZADO: Usar /suppliers (simple) en lugar de /suppliers/analytics (pesado)
    const response = await apiCall('/suppliers')
    if (response.success) {
      // Filtrar solo proveedores activos
      suppliers.value = (response.data || []).filter(s => s.active)
    }
  } catch (error) {
    console.error('�R Error cargando proveedores:', error)
    suppliers.value = []
  }
}

// �x<span class="mb-[2px] leading-none">-</span> Computed: Filtrar bodegas según plan del tenant
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

// �x<span class="mb-[2px] leading-none">-</span> Computed: Determinar si mostrar múltiples columnas de stock
const showMultipleStockColumns = computed(() => {
  return availableWarehouses.value.length > 1
})

// �x<span class="mb-[2px] leading-none">-</span> Helper: Obtener stock de un producto en una bodega específica
// Devuelve null si el producto NO existe en esa sede (para mostrar "N/A")
const getWarehouseStock = (product, warehouseId) => {
  // Si el producto tiene relación con bodegas Y tiene datos, usar esa data
  if (product.warehouses && product.warehouses.length > 0) {
    const warehouse = product.warehouses.find(w => w.id === warehouseId || w.warehouse_id === warehouseId)
    if (warehouse) {
      // �S& Producto existe en esta sede
      const pivotStock = warehouse.pivot?.stock ?? warehouse.stock ?? 0
      
      // �x� FALLBACK CRÍTICO: Si pivot.stock es 0 pero current_stock tiene valor,
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
  
  return null  // �R Producto NO existe en esta sede
}

// �x<span class="mb-[2px] leading-none">-</span> Helper: Obtener stock total de un producto
const getTotalStock = (product) => {
  // �S& SIEMPRE usar current_stock como fuente de verdad
  // Este campo se actualiza automáticamente en el backend cuando se ajusta stock
  // en cualquier bodega (ver Product::updateStock y Product::updateStockInWarehouse)
  return product.current_stock || 0
}

// �x<span class="mb-[2px] leading-none">-</span> Cargar bodegas disponibles
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

// �x<span class="mb-[2px] leading-none">-</span> Mostrar tooltip de stock por bodega
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

// �x<span class="mb-[2px] leading-none">-</span> Ocultar tooltip de stock por bodega
const hideStockTooltip = () => {
  stockTooltip.value.visible = false
  stockTooltip.value.productId = null
  stockTooltip.value.warehouses = []
  stockTooltip.value.x = 0
  stockTooltip.value.y = 0
}

const refreshProducts = async () => {
  await loadProducts()
}

// �x� Helper: Obtener abreviación de unidad de medida
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

const getUnitLabel = (unit) => {
  const labels = {
    unit: 'por unidad',
    kg: 'por kg',
    g: 'por gramo',
    m: 'por metro',
    cm: 'por cm',
    l: 'por litro',
    ml: 'por ml'
  }
  return labels[unit] || 'por unidad'
}

const getUnitPlaceholder = (unit) => {
  const examples = {
    unit: 'Ej: $15.000 por unidad',
    kg: 'Ej: $5.000 por kg',
    g: 'Ej: $50 por gramo',
    m: 'Ej: $3.000 por metro',
    cm: 'Ej: $200 por cm',
    l: 'Ej: $4.500 por litro',
    ml: 'Ej: $100 por ml'
  }
  return examples[unit] || 'Ej: $15.000 por unidad'
}

// �x� Auto-actualizar allow_decimal según la unidad seleccionada
const updateAllowDecimal = () => {
  const decimalUnits = ['kg', 'g', 'm', 'cm', 'l', 'ml']
  productForm.value.allow_decimal = decimalUnits.includes(productForm.value.measurement_unit)
}

const openCreateModal = async () => {
  // Limpiar producto seleccionado (importante para que el watcher en FashionProductForm no cargue datos)
  selectedProduct.value = null
  
  // �x Restaurar el modo según la configuración del SISTEMA (no del producto anterior editado)
  const storeType = appStore.systemSettings?.store_type
  isFashionMode.value = storeType === 'fashion' || storeType === 'moda'
  
  // VALIDACI�N: Verificar si existen categorías primero
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
  
  // �x<span class="mb-[2px] leading-none">-</span> El modo fashion ya fue detectado en onMounted, no necesitamos volver a detectarlo
  
  // Cargar bodegas antes de abrir el modal
  await loadWarehouses()
  
  // Inicializar warehouseStock y warehouseEnabled con todas las tiendas disponibles según el plan
  const warehouseStock = {}
  const warehouseEnabled = {}
  availableWarehouses.value.forEach(warehouse => {
    warehouseStock[warehouse.id] = 0
    warehouseEnabled[warehouse.id] = false // Por defecto no está habilitada ninguna sede
  })
  
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
    measurement_unit: 'unit', // �x� Unidad de medida por defecto
    allow_decimal: false // �x� No permite decimales por defecto
  }
  
  // �S& Si solo hay 1 bodega disponible, sincronizar stock inicial (0)
  if (availableWarehouses.value.length === 1) {
    const warehouseId = availableWarehouses.value[0].id
    productForm.value.warehouseStock[warehouseId] = 0
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
    // �x� SIEMPRE obtener datos completos del producto desde el API
    const response = await productsService.getById(product.id)
    
    if (!response.success || !response.data) {
      throw new Error('No se pudieron obtener los datos del producto')
    }
    
    // Usar el producto completo del API
    product = response.data
  } catch (error) {
    console.error('�R Error obteniendo producto:', error)
    showNotification(
      'Error',
      'No se pudieron cargar los datos del producto',
      'error'
    )
    return
  }
  
  // �x INTELIGENTE: Detectar si el producto es de moda basándose en el PRODUCTO, no en la configuración del sistema
  const productIsFashion = isFashionProduct(product)
  
  // Temporalmente establecer isFashionMode según el producto (no la config del sistema)
  isFashionMode.value = productIsFashion
  
  // Si es producto fashion, guardar el producto seleccionado para el form
  if (productIsFashion) {
    selectedProduct.value = product
    isEditing.value = true
    showProductModal.value = true
    return // El modal de fashion manejará todo
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
  
  // �x}� Detectar si es producto SIMPLE o VARIABLE
  const isSimpleProduct = !product.product_type || product.product_type === 'simple'
  const hasVariants = product.variants && product.variants.length > 0 && product.product_type === 'variable'
  
  // �x� CARGAR STOCK DESDE product.current_stock (SOLO para productos SIMPLES)
  // �S& Usar current_stock como fuente única de verdad
  if (isSimpleProduct) {
    const totalStock = parseInt(product.current_stock || 0) || 0
    
    // Si hay solo 1 bodega, asignar todo el stock a esa bodega
    if (warehouses.value.length === 1 && warehouses.value[0]) {
      const warehouseId = warehouses.value[0].id
      warehouseStock[warehouseId] = totalStock
      warehouseEnabled[warehouseId] = true
    } 
    // Si hay múltiples bodegas, intentar leer desde product.warehouses
    else if (product.warehouses && Array.isArray(product.warehouses)) {
      const stockByWarehouse = new Map()
      
      product.warehouses.forEach(warehouse => {
        if (warehouse.id) {
          const stock = warehouse.pivot?.stock || warehouse.stock || 0
          const parsedStock = parseInt(stock) || 0
          
          const currentStock = stockByWarehouse.get(warehouse.id) || 0
          stockByWarehouse.set(warehouse.id, Math.max(currentStock, parsedStock))
        }
      })
      
      // Aplicar el stock agrupado
      stockByWarehouse.forEach((stock, warehouseId) => {
        warehouseStock[warehouseId] = stock
        if (stock > 0) {
          warehouseEnabled[warehouseId] = true
        }
      })
    }
  }
  
  // Fallback: Si el producto tiene warehouse_id (tienda actual) - Solo para compatibilidad
  if (product.warehouse_id && warehouseStock[product.warehouse_id] === 0) {
    const parsedStock = parseInt(product.current_stock || product.stock || 0)
    if (parsedStock > 0) {
      warehouseStock[product.warehouse_id] = parsedStock
      warehouseEnabled[product.warehouse_id] = true
    }
  }
  
  isEditing.value = true
  
  // �x<span class="mb-[2px] leading-none">-</span> Si hay solo 1 bodega, obtener el stock de esa bodega para el campo "Stock Inicial"
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
    supplier_id: product.supplier_id || null, // �x<span class="mb-[2px] leading-none">-</span> Cargar proveedor
    warehouseStock: warehouseStock,
    warehouseEnabled: warehouseEnabled,
    image: '', // Dejar vacío inicialmente
    active: getProductStatus(product) !== false,
    measurement_unit: product.measurement_unit || 'unit', // �x� Cargar unidad de medida
    allow_decimal: product.allow_decimal || false // �x� Cargar si permite decimales
  }
  
  // Configurar estado de imágenes para edición
  previewImage.value = null
  imageLoadError.value = false
  
  // �x�️ Guardar imagen actual del producto
  // Buscar en: 1) image_url directo, 2) image, 3) array images[0]
  let productImageUrl = product.image_url || product.image || ''
  
  // Si no hay imagen directa, buscar en el array de imágenes
  if (!productImageUrl && product.images && Array.isArray(product.images) && product.images.length > 0) {
    const firstImage = product.images[0]
    // La imagen puede estar en .image_url o .url dependiendo de la estructura
    productImageUrl = firstImage?.image_url || firstImage?.url || firstImage || ''
  }
  
  if (productImageUrl && typeof productImageUrl === 'string' && productImageUrl.trim().length > 0) {
    // Verificar si es una URL válida (empieza con http o /storage)
    if (productImageUrl.startsWith('http') || productImageUrl.startsWith('/storage') || productImageUrl.startsWith('storage/')) {
      currentProductImage.value = productImageUrl
    } else {
      currentProductImage.value = null
    }
  } else {
    currentProductImage.value = null
  }
  
  // Por defecto, modo URL
  imageUploadMethod.value = 'url'
  
  showProductModal.value = true
}

const viewProduct = async (product) => {
  selectedProduct.value = product
  variantChanges.value = {} // Limpiar cambios previos
  selectedImageIndex.value = 0 // �x�️ Reset índice de imagen
  
  // Fetch full details including variants
  try {
    const response = await productsService.getById(product.id)
    if (response.success) {
      selectedProduct.value = response.data
      
      // Inicializar campos editables para cada variante (solo si es producto fashion)
      if (isFashionProduct(selectedProduct.value) && selectedProduct.value.variants) {
        selectedProduct.value.variants.forEach(variant => {
          variant.editableStock = variant.stock || 0
          variant.editablePrice = variant.price || 0
          variant.editableCost = variant.cost_price || 0
        })
      }
      
      // �x}� Mostrar el modal apropiado
      showViewModal.value = true
    }
  } catch (error) {
    console.error("Error fetching product details", error)
    showViewModal.value = true // Abrir modal aunque falle la carga completa
  }
}

// �x� Funciones de Edición en Línea (Excel-Style)
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
      '�S& Cambios Guardados', 
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

// �x Sistema de notificaciones elegantes
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

// �x Función para habilitar/deshabilitar productos
// Estado para confirmación de cambio de estado
const showStatusConfirmModal = ref(false)
const showCategoryInactiveModal = ref(false)
const pendingStatusChange = ref(null)

const toggleProductStatus = async (product) => {
  const currentStatus = getProductStatus(product)
  const newStatus = currentStatus !== false ? false : true
  
  // Si se intenta activar un producto, verificar que la categoría esté activa
  if (newStatus === true) {
    const productCategory = categories.value.find(c => c.id === product.category_id)
    
    if (!productCategory) {
      showNotification(
        'Error',
        'No se encontró la categoría del producto. Por favor, recarga la página.',
        'error'
      )
      return
    }
    
    if (!productCategory.active) {
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
    
    // �S& SOLO enviar campos necesarios (NO todo el objeto)
    await productsService.update(product.id, { 
      active: newStatus 
    })
    
    // �x RECARGAR DESDE BASE DE DATOS (Sin lógica local)
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

// Eliminar producto (soft delete)
const showDeleteConfirmModal = ref(false)
const pendingDelete = ref(null)
const deletingProduct = ref(false)
const deleteReason = ref('')

const deleteProduct = (product) => {
  pendingDelete.value = product
  deleteReason.value = ''
  showDeleteConfirmModal.value = true
}

const confirmDeleteProduct = async () => {
  if (!pendingDelete.value) return
  
  try {
    deletingProduct.value = true
    const productName = pendingDelete.value.name
    
    await productsService.delete(pendingDelete.value.id, deleteReason.value || null)
    
    showDeleteConfirmModal.value = false
    
    await loadProducts()
    
    showNotification(
      'Producto eliminado',
      `"${productName}" ha sido movido a la papelera. Puedes restaurarlo desde Configuración.`,
      'success'
    )
  } catch (error) {
    showNotification(
      'Error al eliminar',
      'No se pudo eliminar el producto: ' + (error.message || 'Error desconocido'),
      'error'
    )
  } finally {
    deletingProduct.value = false
    pendingDelete.value = null
    deleteReason.value = ''
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
  
  // �S& Guardar el archivo para enviarlo al backend
  productForm.value.imageFile = file
  
  // Crear preview
  const reader = new FileReader()
  reader.onload = (e) => {
    previewImage.value = e.target.result
    // Guardar preview para mostrar (NO enviar esto al backend)
    productForm.value.image = e.target.result
  }
  reader.readAsDataURL(file)
}

const clearImageUpload = () => {
  previewImage.value = null
  productForm.value.image = ''
  productForm.value.imageFile = null // �S& Limpiar también el archivo
  // Limpiar el input file
  const fileInput = document.querySelector('input[type="file"]')
  if (fileInput) fileInput.value = ''
}

// �x Cambiar método de imagen (URL/Archivo) - Limpia la imagen actual
const changeImageMethod = (method) => {
  // Limpiar todo cuando cambiamos de método
  clearImageUpload()
  currentProductImage.value = null // Ocultar imagen actual
  imageUploadMethod.value = method
}

// Eliminar imagen del producto (físicamente del servidor)
const deleteProductImage = async () => {
  if (!productForm.value.id || !currentProductImage.value) {
    return
  }
  
  try {
    deletingImage.value = true
    
    // Llamar al backend para eliminar la imagen físicamente
    const response = await productsService.deleteImage(productForm.value.id)
    
    if (response.success) {
      showNotification(
        'Imagen eliminada',
        'La imagen se ha eliminado correctamente',
        'success'
      )
      
      // Limpiar UI
      currentProductImage.value = null
      productForm.value.image = ''
      
      // Recargar productos para reflejar el cambio
      await loadProducts()
    } else {
      throw new Error(response.message || 'Error al eliminar imagen')
    }
  } catch (error) {
    console.error('[deleteProductImage] Error:', error)
    showNotification(
      'Error',
      error.message || 'No se pudo eliminar la imagen',
      'error'
    )
  } finally {
    deletingImage.value = false
  }
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
      const newCategoryId = response.data?.id
      if (newCategoryId) {
        if (isFashionMode.value && fashionFormRef.value) {
          fashionFormRef.value.setCategory(newCategoryId)
        } else {
          productForm.value.category_id = newCategoryId
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
      const newSupplierId = response.data.data?.id
      if (newSupplierId) {
        productForm.value.supplier_id = newSupplierId
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

// �a�️ Validar si faltan datos importantes (especialmente stock)
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

// �x Guardar producto tipo Ropa/Variantes
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
    
    // �x� CRÍTICO: Detectar si es edición usando el ID del productData (viene del form fashion)
    const productId = productData.id || selectedProduct.value?.id || null
    const isEditingFashion = isEditing.value && productId

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
      
      // �S& AGREGAR sale_price y stock si es producto simple
      if (isSimpleProduct && firstVariant) {
        formData.append('sale_price', firstVariant.price || 0)
        formData.append('current_stock', firstVariant.stock || 0)
      }
      
      // �S& AGREGAR store_category para recordar que fue creado como moda
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
          formData.append(`variants[${index}][cost_price]`, variant.cost_price || variant.cost || 0)
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

      // Images - �S& Enviar como array para que PHP lo interprete correctamente
      if (productData.images && productData.images.length > 0) {
        productData.images.forEach((img) => {
          if (img.file) {
            formData.append(`images[]`, img.file) // �S& Usar images[] en lugar de images[index]
          }
        })
      }

      // Detectar si es edición o creación
      if (isEditingFashion) {
        formData.append('_method', 'PUT')
        response = await productsService.update(productId, formData)
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
        store_category: 'fashion', // �S& Recordar que fue creado como moda
        options: productData.options || [],
        variants: productData.variants || []
      }
      
      // �S& AGREGAR sale_price y stock si es producto simple
      if (isSimpleProduct && productData.variants?.[0]) {
        payload.sale_price = productData.variants[0].price || 0
        payload.current_stock = productData.variants[0].stock || 0
      }
      
      // Detectar si es edición o creación
      if (isEditingFashion) {
        response = await productsService.update(productId, payload)
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
    
    // �a�️ Validar campos importantes y mostrar confirmación si faltan (solo en creación)
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
    let totalStock = calculateTotalStock()
    if (totalStock === undefined || totalStock === null || totalStock < 0) {
      throw new Error('El stock total debe ser un número válido (0 o mayor)')
    }
    
    // SAFETY NET: Si el stock del formulario es > 0 pero ninguna bodega
    // está habilitada (ej: asignado por voz), auto-habilitar la principal
    const stockFormulario = parseInt(productForm.value.stock) || 0
    if (stockFormulario > 0 && totalStock === 0 && availableWarehouses.value.length >= 1) {
      const defaultWarehouse = availableWarehouses.value.find(w => w.is_default) || availableWarehouses.value[0]
      if (defaultWarehouse) {
        productForm.value.warehouseEnabled[defaultWarehouse.id] = true
        productForm.value.warehouseStock[defaultWarehouse.id] = stockFormulario
        totalStock = calculateTotalStock()
      }
    }
    
    // �S& Detectar si hay un archivo de imagen para subir O convertir base64 a archivo
    let hasImageFile = productForm.value.imageFile instanceof File
    
    // �x Detectar y convertir base64 a File si es necesario
    const imageUrl = (productForm.value.image || '').trim()
    const isBase64 = imageUrl.startsWith('data:image/')
    
    if (isBase64 && !hasImageFile) {
      try {
        // Extraer tipo MIME y datos
        const matches = imageUrl.match(/^data:([^;]+);base64,(.+)$/)
        if (matches) {
          const mimeType = matches[1]
          const base64Data = matches[2]
          
          // Convertir base64 a blob
          const byteCharacters = atob(base64Data)
          const byteNumbers = new Array(byteCharacters.length)
          for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i)
          }
          const byteArray = new Uint8Array(byteNumbers)
          const blob = new Blob([byteArray], { type: mimeType })
          
          // Convertir blob a File
          const fileName = `product_${Date.now()}.${mimeType.split('/')[1]}`
          productForm.value.imageFile = new File([blob], fileName, { type: mimeType })
          hasImageFile = true
        }
      } catch (error) {
        console.error('�R [saveProduct] Error convirtiendo base64:', error)
      }
    }
    
    // �S& Detectar si es una URL externa (http/https)
    const isExternalUrl = imageUrl.length > 0 && 
                          !isBase64 &&
                          (imageUrl.startsWith('http://') || 
                           imageUrl.startsWith('https://') ||
                           imageUrl.startsWith('/storage') ||
                           imageUrl.startsWith('storage/'))
    
    let response
    
    if (hasImageFile) {
      // ===== USAR FORMDATA SI HAY ARCHIVO DE IMAGEN =====
      const formData = new FormData()
      formData.append('name', productForm.value.name.trim())
      formData.append('description', productForm.value.description?.trim() || '')
      formData.append('product_type', 'simple')
      formData.append('store_category', 'general')
      formData.append('sku', productForm.value.sku?.trim() || `SKU-${Date.now()}`)
      formData.append('barcode', productForm.value.barcode?.trim() || '')
      formData.append('category_id', parseInt(productForm.value.category_id))
      if (productForm.value.supplier_id) {
        formData.append('supplier_id', parseInt(productForm.value.supplier_id))
      }
      formData.append('cost_price', parseFloat(productForm.value.cost))
      formData.append('sale_price', parseFloat(productForm.value.price))
      formData.append('current_stock', totalStock)
      formData.append('min_stock', 5)
      formData.append('max_stock', 100)
      formData.append('unit', productForm.value.unit?.trim() || 'unidad')
      formData.append('manage_stock', true)
      formData.append('active', productForm.value.active !== false ? 1 : 0)
      formData.append('measurement_unit', productForm.value.measurement_unit || 'unit')
      formData.append('allow_decimal', productForm.value.allow_decimal ? 1 : 0)
      
      // �x� Agregar la imagen como archivo
      formData.append('images[]', productForm.value.imageFile)
      
      // �x<span class="mb-[2px] leading-none">-</span> Stock por bodegas
      Object.keys(productForm.value.warehouseStock || {}).forEach(warehouseId => {
        if (productForm.value.warehouseEnabled[warehouseId]) {
          formData.append(`warehouse_stocks[${warehouseId}]`, productForm.value.warehouseStock[warehouseId])
        }
      })
      
      if (isEditing.value) {
        formData.append('_method', 'PUT')
        response = await productsService.update(productForm.value.id, formData)
      } else {
        response = await productsService.create(formData)
      }
    } else {
      // ===== USAR JSON SI NO HAY ARCHIVO (solo URL o sin imagen) =====
      const apiData = {
        name: productForm.value.name.trim(),
        description: productForm.value.description?.trim() || '',
        product_type: 'simple',
        store_category: 'general',
        sku: productForm.value.sku?.trim() || `SKU-${Date.now()}`,
        barcode: productForm.value.barcode?.trim() || '',
        category_id: parseInt(productForm.value.category_id),
        supplier_id: productForm.value.supplier_id ? parseInt(productForm.value.supplier_id) : null,
        cost_price: parseFloat(productForm.value.cost),
        sale_price: parseFloat(productForm.value.price),
        wholesale_price: null,
        current_stock: totalStock,
        min_stock: 5,
        max_stock: 100,
        unit: productForm.value.unit?.trim() || 'unidad',
        manage_stock: true,
        active: productForm.value.active !== false,
        // Solo enviar image_url si es una URL externa válida
        image_url: isExternalUrl ? imageUrl : null,
        tags: null,
        warehouse_stocks: Object.keys(productForm.value.warehouseStock || {}).reduce((acc, warehouseId) => {
          if (productForm.value.warehouseEnabled[warehouseId]) {
            acc[warehouseId] = productForm.value.warehouseStock[warehouseId]
          }
          return acc
        }, {}),
        measurement_unit: productForm.value.measurement_unit || 'unit',
        allow_decimal: productForm.value.allow_decimal || false,
      }
      
      if (isEditing.value) {
        response = await productsService.update(productForm.value.id, apiData)
      } else {
        response = await productsService.create(apiData)
      }
    }
    
    showNotification(
      isEditing.value ? 'Producto actualizado' : 'Producto creado',
      `El producto "${productForm.value.name}" se ha ${isEditing.value ? 'actualizado' : 'creado'} exitosamente`,
      'success'
    )
    
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

// �x<span class="mb-[2px] leading-none">-</span> Sincronizar stock con bodega única cuando hay solo 1 bodega disponible
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
  }
})

watch(statusFilter, async () => {
  await loadProducts()
})

// �x}� Watcher para query params de navegación AI (filtros automáticos)
watch(() => props.queryParams, async (newParams) => {
  if (!newParams || Object.keys(newParams).length === 0) return
  
  // Aplicar filtro según queryParams
  if (newParams.filter) {
    switch(newParams.filter) {
      case 'inactive':
        statusFilter.value = 'inactive'
        showNotification(
          'Filtro aplicado',
          'Mostrando solo productos inactivos',
          'info'
        )
        break
      case 'active':
        statusFilter.value = 'active'
        break
      case 'low-stock':
        statusFilter.value = 'low-stock'
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
      showNotification(
        'Categoría seleccionada',
        `Mostrando productos de ${categoryName}`,
        'info'
      )
    }
  }

  // �x� Manejar acciones (create/edit) desde props (AI Navigation)
  if (newParams.action === 'create') {
    setTimeout(() => openCreateModal(), 500);
  } else if (newParams.action === 'edit' && newParams.id) {
    // Asegurar que los productos estén cargados
    if (products.value.length === 0) await loadProducts();
    
    const productToEdit = products.value.find(p => p.id == newParams.id);
    if (productToEdit) {
      editProduct(productToEdit);
    }
  }
}, { deep: true, immediate: true })

// �x Configurar listener de eventos global ANTES de onMounted (para evitar warning)
const handleProductsUpdate = (event) => {
  // Recargar productos automáticamente cuando se reciba el evento
  loadProducts()
}

// �x�️ Watch: Limpiar cambios al cerrar modal de inventario
watch(showViewModal, (newValue) => {
  if (!newValue) {
    // Modal cerrado - limpiar cambios pendientes
    variantChanges.value = {}
  }
})

// Inicialización
onMounted(async () => {
  // �x}� PRIMERO: Detectar tipo de tienda ANTES de renderizar para evitar parpadeo
  const storeType = appStore.systemSettings?.store_type || 'general'
  isFashionMode.value = storeType === 'fashion' || storeType === 'moda'
  
  // �x� Cargar preferencias del usuario primero (PROBLEMA 1: Restaurar vista guardada)
  loadUserPreferences()
  
  // �x}� VISTA POR DEFECTO INTELIGENTE: Grid para Fashion, Table para Retail
  // Solo establecer si no hay preferencia guardada
  if (!localStorage.getItem(USER_PREFERENCES_KEY)) {
    viewMode.value = isFashionStore.value ? 'grid' : 'table'
  }
  
  // Registrar listener ANTES del primer await
  window.addEventListener('products-updated', handleProductsUpdate)
  
  // �xa� OPTIMIZACI�N: Cargar todo EN PARALELO en lugar de secuencial
  // Esto reduce el tiempo de carga de 4-6 segundos a ~1-2 segundos
  await Promise.all([
    loadCategories(),
    loadSuppliers(),
    loadWarehouses()
  ])
  
  // �S& Inicialización completa - permitir renderizado
  isInitializing.value = false
  
  // Verificar si hay acción de creación desde la URL (Deep Linking) O desde props
  const action = route.query.action || props.queryParams?.action;
  const actionId = route.query.id || props.queryParams?.id;
  
  if (action === 'create') {
    // Esperar un momento para asegurar que las categorías estén cargadas
    setTimeout(() => {
      openCreateModal()
      // Limpiar la query para evitar que se abra al recargar
      if (route.query.action) router.replace({ query: null })
    }, 500)
  } else if (action === 'edit' && actionId) {
    // Esperar a que carguen los productos
    await loadProducts()
    
    const productToEdit = products.value.find(p => p.id == actionId)
    if (productToEdit) {
      editProduct(productToEdit)
      // Limpiar la query
      if (route.query.action) router.replace({ query: null })
    } else {
      console.warn('�a�️ [ProductsView] Producto no encontrado para edición:', actionId)
    }
  } else {
    // Verificar si hay datos de producto para editar desde otra vista (solo si no es creación)
    const editProductData = sessionStorage.getItem('editProductData')
    if (editProductData) {
      try {
        const productData = JSON.parse(editProductData)
        
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
  
  // �x� Inicializar contexto para IA después de cargar datos
  updateScreenContextForAI()
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











