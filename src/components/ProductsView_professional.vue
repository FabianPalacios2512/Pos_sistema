<template>
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-zinc-900 dark:via-zinc-950 dark:to-black transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header Simple y Elegante (Sin borde) -->
      <div class="flex items-center justify-between pb-4">
        <!-- Título y Subtítulo (Sin icono) -->
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Productos</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Manage your inventory and catalog</p>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Neutro (Exportar) -->
          <button @click="exportProducts"
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
      </div>

      <!-- Métricas Principales - SaaS Premium Design -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <!-- Total Productos -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-purple-50 dark:bg-purple-950 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Productos</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ displayProducts.length }}</p>
            </div>
          </div>
        </div>

        <!-- Productos Activos -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-950 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Activos</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ activeProducts }}</p>
            </div>
          </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-amber-50 dark:bg-amber-950 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Stock Bajo</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ lowStockProducts }}</p>
            </div>
          </div>
        </div>

        <!-- Valor Total -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-blue-50 dark:bg-blue-950 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Valor Total</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatCurrency(totalValue) }}</p>
            </div>
          </div>
        </div>

        <!-- Categorías -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-indigo-50 dark:bg-indigo-950 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Categorías</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ uniqueCategories }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenedor Principal Tour - Filtros + Productos -->
      <div id="tour-products-main">
      <!-- Filtros - Diseño exacto de Facturas -->
    <div class="bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm rounded-xl p-4 mb-6 transition-colors duration-300">
      <div class="flex flex-wrap items-center gap-4">
        <!-- Búsqueda limpia (como Facturas) -->
        <div class="flex-1 min-w-[200px] relative">
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
        <div class="min-w-[160px]">
          <select
            v-model="categoryFilter"
            class="w-full px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300">
            <option value="">Todas las categorías</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
          </select>
        </div>
        
        <!-- Estado -->
        <div class="min-w-[140px]">
          <select
            v-model="statusFilter"
            class="w-full px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300">
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
            class="w-full px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300">
            <option value="name">Por Nombre</option>
            <option value="price">Por Precio</option>
            <option value="stock">Por Stock</option>
            <option value="created_at">Por Fecha</option>
          </select>
        </div>
        
        <!-- Toggle Vista (Diseño limpio como Facturas) -->
        <div class="flex items-center bg-gray-50 dark:bg-zinc-800 rounded-lg p-1 border border-gray-200 dark:border-zinc-700">
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

      <!-- Empty State -->
      <div v-else-if="!loading && !paginatedProducts.length" class="flex justify-center items-center py-20">
        <div class="flex flex-col items-center space-y-3">
          <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-700 dark:text-zinc-300">No hay productos</p>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">{{ searchTerm ? 'No se encontraron productos que coincidan con tu búsqueda' : 'Comienza agregando tu primer producto' }}</p>
          </div>
          <button v-if="!searchTerm" 
                  @click="openCreateModal" 
                  class="mt-2 px-3 py-2 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white rounded-lg text-sm font-medium transition-all inline-flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Nuevo Producto</span>
          </button>
        </div>
      </div>

      <!-- Grid de productos -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-6">
        <div v-for="product in paginatedProducts" 
             :key="product.id" 
             @click="viewProduct(product)"
             class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-slate-200 dark:border-zinc-800 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 overflow-hidden group flex flex-col cursor-pointer">
          
          <!-- Imagen del producto -->
          <div class="relative h-40 bg-slate-50 dark:bg-zinc-800 overflow-hidden">
            <img :src="getProductImage(product)" 
                 :alt="product.name" 
                 @error="(e) => handleImageError(e, product)"
                 class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
            
            <!-- Badges Flotantes -->
            <div class="absolute top-2 right-2 flex flex-col gap-1">
              <span :class="getProductStatus(product) ? 'bg-emerald-500/90 text-white' : 'bg-rose-500/90 text-white'" 
                    class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider shadow-sm backdrop-blur-sm">
                {{ getProductStatus(product) ? 'Activo' : 'Inactivo' }}
              </span>
            </div>
            
            <div v-if="(product.current_stock || 0) <= (product.min_stock || 0)" class="absolute top-2 left-2">
              <span class="bg-amber-500/90 text-white px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider shadow-sm backdrop-blur-sm animate-pulse">
                Stock Bajo
              </span>
            </div>
          </div>

          <!-- Información del producto -->
          <div class="p-4 flex-1 flex flex-col">
            <div class="mb-3 flex-1">
              <div class="flex items-start justify-between gap-2 mb-1">
                <h3 class="font-bold text-slate-900 dark:text-white text-sm leading-tight line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" :title="product.name">
                  {{ product.name }}
                </h3>
              </div>
              <p class="text-[11px] font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide mb-2">
                {{ product.category?.name || 'Sin categoría' }}
              </p>
              <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono text-slate-400 dark:text-zinc-500 bg-slate-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded">
                  {{ product.sku || 'SIN SKU' }}
                </span>
              </div>
            </div>
            
            <!-- Precio y Stock -->
            <div class="flex items-end justify-between mb-4 pt-3 border-t border-slate-100 dark:border-zinc-800">
              <div>
                <p class="text-[10px] text-slate-500 dark:text-zinc-400 font-medium mb-0.5">Precio</p>
                <span class="font-black text-slate-900 dark:text-white text-lg leading-none">
                  ${{ formatCurrency(product.sale_price) }}
                </span>
              </div>
              <div class="text-right">
                <p class="text-[10px] text-slate-500 dark:text-zinc-400 font-medium mb-0.5">Stock</p>
                <span class="font-bold text-slate-700 dark:text-zinc-300 text-sm bg-slate-50 dark:bg-zinc-800 px-2 py-0.5 rounded-md border border-slate-100 dark:border-zinc-700">
                  {{ product.current_stock || 0 }}
                </span>
              </div>
            </div>

            <!-- Acciones (Ghost Style) -->
            <div class="grid grid-cols-3 gap-2">
              <button @click.stop="viewProduct(product)" 
                      class="flex items-center justify-center py-2 rounded-lg text-slate-400 dark:text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950 transition-all border border-transparent hover:border-blue-100 dark:hover:border-blue-800"
                      title="Ver Detalles">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
              
              <button @click.stop="editProduct(product)" 
                      class="flex items-center justify-center py-2 rounded-lg text-slate-400 dark:text-zinc-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950 transition-all border border-transparent hover:border-amber-100 dark:hover:border-amber-800"
                      title="Editar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>
              
              <button @click.stop="toggleProductStatus(product)" 
                      class="flex items-center justify-center py-2 rounded-lg transition-all border border-transparent"
                      :class="getProductStatus(product) !== false 
                        ? 'text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950 hover:border-rose-100 dark:hover:border-rose-800' 
                        : 'text-slate-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950 hover:border-emerald-100 dark:hover:border-emerald-800'"
                      :title="getProductStatus(product) !== false ? 'Desactivar' : 'Activar'">
                <svg v-if="getProductStatus(product) !== false" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Paginador para vista de tarjetas -->
      <div class="mb-6">
        <TablePaginator
          v-if="filteredProducts.length > 0"
          v-model:currentPage="currentPage"
          v-model:itemsPerPage="itemsPerPage"
          :totalPages="totalPages"
          :totalItems="totalItems"
          label="productos"
        />
      </div>
    </div>

    <!-- Vista de Tabla - Colores unificados con panel izquierdo de Facturas -->
  <div v-else class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden transition-colors duration-300">
      <!-- Header de tabla -->
      <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between">
        <div>
          <h2 class="text-base font-bold text-gray-900 dark:text-white">Catálogo de Productos</h2>
          <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">{{ totalItems }} productos encontrados</p>
        </div>
      </div>
      
      <!-- Tabla -->
      <table class="min-w-full">
        <thead>
          <tr class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800">
            <th class="px-6 py-3.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
              Producto
            </th>
            <th class="px-6 py-3.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
              Categoría
            </th>
            <th class="px-6 py-3.5 text-right text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
              Precio
            </th>
            
            <!-- Columnas de Stock Dinámicas -->
            <template v-if="showMultipleStockColumns">
              <th v-for="warehouse in availableWarehouses" :key="warehouse.id"
                  class="px-4 py-3.5 text-center text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                <div class="flex items-center justify-center space-x-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  <span>{{ warehouse.name }}</span>
                </div>
              </th>
            </template>
            <template v-else>
              <th class="px-6 py-3.5 text-center text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                Stock
              </th>
            </template>
            
            <th class="px-6 py-3.5 text-center text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
              Estado
            </th>
            <th class="px-6 py-3.5 text-center text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
          <!-- Loading State -->
          <tr v-if="loading">
            <td :colspan="5 + availableWarehouses.length" class="px-4 py-12 text-center">
              <div class="flex flex-col items-center space-y-3">
                <div class="w-8 h-8 border-4 border-blue-600 dark:border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-sm text-gray-600 dark:text-zinc-400 font-medium">Cargando productos...</p>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-else-if="!loading && !paginatedProducts.length">
            <td :colspan="5 + availableWarehouses.length" class="px-4 py-12 text-center">
              <div class="flex flex-col items-center space-y-3">
                <div class="w-16 h-16 bg-slate-50 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-2">
                  <svg class="w-8 h-8 text-slate-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                </div>
                <div>
                  <p class="text-base font-bold text-slate-800 dark:text-zinc-200">No hay productos</p>
                  <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">{{ searchTerm ? 'No se encontraron productos que coincidan con tu búsqueda' : 'Comienza agregando tu primer producto' }}</p>
                </div>
                <button v-if="!searchTerm" 
                        @click="openCreateModal" 
                        class="mt-4 px-4 py-2 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white rounded-lg text-sm font-bold transition-all shadow-lg hover:shadow-xl inline-flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  <span>Nuevo Producto</span>
                </button>
              </div>
            </td>
          </tr>

          <!-- Product Rows - Premium Design con colores de Facturas -->
          <tr v-for="product in paginatedProducts" 
              :key="product.id"
              @click="viewProduct(product)"
              class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-all duration-200 group cursor-pointer border-b border-gray-100 dark:border-zinc-800 last:border-0">
            <!-- Columna Producto (La Estrella) -->
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <div class="relative w-14 h-14 flex-shrink-0">
                  <img :src="getProductImage(product)" 
                       :alt="product.name" 
                       @error="(e) => handleImageError(e, product)"
                       class="w-full h-full object-cover rounded-lg ring-1 ring-gray-300 dark:ring-zinc-700 group-hover:ring-gray-400 dark:group-hover:ring-zinc-600 transition-all">
                </div>
                <div class="min-w-0 flex-1">
                  <div class="text-sm font-medium text-gray-900 dark:text-zinc-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">{{ product.name }}</div>
                  <div class="text-xs text-gray-500 dark:text-zinc-400 font-mono mt-1">{{ product.sku || 'SIN SKU' }}</div>
                </div>
              </div>
            </td>
            <!-- Columna Categoría (Badges/Pills) -->
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border border-gray-300 dark:border-zinc-700 font-medium">
                {{ product.category?.name || 'Sin categoría' }}
              </span>
            </td>
            <!-- Columna Precio (Monoespaciada) -->
            <td class="px-6 py-4 text-right">
              <div class="text-sm font-mono font-bold text-gray-900 dark:text-white tabular-nums">
                ${{ formatCurrency(product.sale_price) }}
              </div>
            </td>
            <!-- Columnas de Stock Dinámicas -->
            <template v-if="showMultipleStockColumns">
              <td v-for="warehouse in availableWarehouses" :key="warehouse.id"
                  class="px-4 py-4 text-center">
                <div class="flex flex-col items-center">
                  <span :class="[
                    'text-sm font-mono font-bold tabular-nums',
                    getWarehouseStock(product, warehouse.id) <= (product.min_stock || 0) ? 'text-orange-500' : 'text-emerald-400'
                  ]">
                    {{ getWarehouseStock(product, warehouse.id) }}
                  </span>
                  <!-- Alerta de stock bajo para esta bodega -->
                  <span v-if="getWarehouseStock(product, warehouse.id) <= (product.min_stock || 0)" 
                        class="text-[10px] font-bold text-orange-500 mt-0.5">
                    Bajo
                  </span>
                </div>
              </td>
            </template>
            <template v-else>
              <!-- Columna Stock (Color-Coded) -->
              <td class="px-6 py-4 text-center">
                <div class="flex flex-col items-center">
                  <span :class="[
                    'text-sm font-mono font-bold tabular-nums',
                    getTotalStock(product) <= (product.min_stock || 0) ? 'text-orange-500' : 'text-emerald-400'
                  ]">
                    {{ getTotalStock(product) }}
                  </span>
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
                    ? 'text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950' 
                    : 'text-slate-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950'"
                  :title="getProductStatus(product) !== false ? 'Desactivar' : 'Activar'">
                  <svg v-if="getProductStatus(product) !== false" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
          v-if="filteredProducts.length > 0"
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
    <div class="fixed top-4 right-4 z-50 space-y-2" v-if="notifications.length > 0">
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
    <div v-if="showStatusConfirmModal" 
         class="fixed inset-0 bg-gray-900/50 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         @click.self="showStatusConfirmModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-xl w-full max-w-md shadow-2xl overflow-hidden animate-fade-in">
        <!-- Header -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-white">Confirmar Cambio de Estado</h3>
              <p class="text-sm text-white/80">Esta acción modificará el estado del producto</p>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="p-6">
          <p class="text-slate-700 dark:text-zinc-300 mb-4">
            ¿Estás seguro que deseas <span class="font-bold">{{ pendingStatusChange?.newStatus ? 'habilitar' : 'deshabilitar' }}</span> el producto:
          </p>
          <div class="bg-slate-50 dark:bg-zinc-800 rounded-lg p-4 border border-slate-200 dark:border-zinc-700 mb-6">
            <p class="font-bold text-slate-900 dark:text-white">{{ pendingStatusChange?.product?.name }}</p>
            <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">SKU: {{ pendingStatusChange?.product?.sku || 'N/A' }}</p>
          </div>
          <p class="text-sm text-slate-600 dark:text-zinc-400">
            {{ pendingStatusChange?.newStatus ? 'El producto estará disponible para la venta.' : 'El producto no estará disponible para la venta.' }}
          </p>
        </div>

        <!-- Actions -->
        <div class="bg-slate-50 dark:bg-zinc-800 px-6 py-4 flex items-center justify-end space-x-3">
          <button @click="showStatusConfirmModal = false" 
                  class="px-4 py-2 bg-white dark:bg-zinc-700 border border-slate-300 dark:border-zinc-600 text-slate-700 dark:text-zinc-200 rounded-lg hover:bg-slate-50 dark:hover:bg-zinc-600 transition-colors font-medium">
            Cancelar
          </button>
          <button @click="confirmStatusChange" 
                  class="px-4 py-2 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white rounded-lg transition-colors font-medium">
            Confirmar
          </button>
        </div>
      </div>
    </div>

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
    <div v-if="showProductModal" 
         class="fixed inset-0 bg-gray-900/50 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center z-50"
         @click.self="showProductModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-7xl shadow-2xl max-h-[95vh] overflow-hidden border border-gray-200 dark:border-zinc-800 flex mx-4">
        
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-8 py-5">
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

        <div class="flex h-[calc(95vh-160px)]">
          <!-- Formulario Principal -->
          <div class="flex-1 p-8 overflow-y-auto bg-gray-50 dark:bg-zinc-950">
            <form @submit.prevent="saveProduct" class="space-y-6">
              
              <!-- Información Básica -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-zinc-800">
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
                    <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-1.5">Nombre del Producto *</label>
                    <input v-model="productForm.name" 
                           type="text" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-gray-400 dark:focus:ring-blue-600 focus:border-transparent text-sm transition-all bg-white dark:bg-zinc-800 text-gray-900 dark:text-white"
                           placeholder="Ej: iPhone 13 Pro Max">
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-1.5">Categoría *</label>
                    <select v-model="productForm.category_id" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-gray-400 dark:focus:ring-blue-600 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white transition-all">
                      <option value="">Seleccionar categoría</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-1.5">SKU (Código Único)</label>
                    <input v-model="productForm.sku" 
                           type="text" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-gray-400 dark:focus:ring-blue-600 focus:border-transparent text-sm transition-all bg-white dark:bg-zinc-800 text-gray-900 dark:text-white"
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
                    <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-1.5">Descripción <span class="text-gray-400 dark:text-zinc-500 font-normal">(opcional)</span></label>
                    <textarea v-model="productForm.description" 
                              rows="2"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-gray-400 dark:focus:ring-blue-600 focus:border-transparent text-sm transition-all resize-none bg-white dark:bg-zinc-800 text-gray-900 dark:text-white"
                              placeholder="Descripción breve del producto">
                    </textarea>
                  </div>
                </div>
              </div>

              <!-- PASO 1: UNIDAD DE MEDIDA (LO PRIMERO) -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-zinc-800">
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
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-zinc-800">
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
                      <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-sky-600 text-xs font-bold bg-sky-50 px-2 py-1 rounded">
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
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-zinc-800">
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
                    
                    <!-- Inputs por cada tienda -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                      <div v-for="warehouse in availableWarehouses" :key="warehouse.id"
                           class="p-4 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 hover:shadow-sm transition-all">
                        <div class="flex items-center gap-3 mb-3">
                          <div class="w-10 h-10 bg-gray-50 dark:bg-zinc-700 rounded-xl flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-zinc-600">
                            <svg class="w-5 h-5 text-gray-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                          </div>
                          <div class="flex-1 min-w-0">
                            <span class="text-sm font-bold text-gray-900 dark:text-white block truncate">
                              {{ warehouse.name }}
                            </span>
                            <span v-if="warehouse.is_default" class="text-xs font-semibold text-gray-700 dark:text-zinc-300 bg-gray-100 dark:bg-zinc-700 px-2 py-0.5 rounded-full border border-gray-200 dark:border-zinc-600">
                              Principal
                            </span>
                          </div>
                        </div>
                        
                        <div class="relative">
                          <input 
                            v-model.number="productForm.warehouseStock[warehouse.id]"
                            type="number" 
                            :step="productForm.allow_decimal ? '0.01' : '1'"
                            min="0"
                            :placeholder="productForm.allow_decimal ? '0.00' : '0'"
                            class="w-full px-4 pr-14 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-gray-400 dark:focus:ring-blue-600 focus:border-gray-400 dark:focus:border-blue-600 text-sm font-bold text-center bg-white dark:bg-zinc-800 text-gray-900 dark:text-white transition-all"
                          />
                          <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-zinc-400 text-xs font-medium bg-gray-100 dark:bg-zinc-700 px-2 py-1 rounded">
                            {{ getUnitAbbreviation(productForm.measurement_unit) }}
                          </span>
                        </div>
                        
                        <p class="text-xs text-gray-600 dark:text-zinc-400 text-center mt-2 font-medium">
                          Disponible en esta sede
                        </p>
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
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-zinc-800">
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
          <div class="w-80 bg-gray-50 dark:bg-zinc-900 border-l border-gray-200 dark:border-zinc-800 p-6">
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
            <div class="flex bg-white dark:bg-zinc-800 rounded-lg p-1 mb-4 shadow-sm border border-gray-200 dark:border-zinc-700">
              <button type="button"
                      @click="imageUploadMethod = 'url'"
                      :class="[
                        'flex-1 px-3 py-2 rounded-md text-xs font-semibold transition-all flex items-center justify-center space-x-1.5',
                        imageUploadMethod === 'url' 
                          ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white' 
                          : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700'
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
                          ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white' 
                          : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700'
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
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
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
                       class="max-h-24 rounded-lg border border-gray-200 dark:border-zinc-700 shadow-sm"
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
                      @click="saveProduct"
                      :disabled="loading"
                      class="w-full px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md">
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
      </div>
    </div>

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

    <!-- Modal Ver Producto -->
    <div v-if="showViewModal" 
         class="fixed inset-0 bg-gray-900/50 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         @click.self="showViewModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-lg w-full max-w-2xl shadow-xl max-h-[90vh] overflow-hidden border dark:border-zinc-800">
        
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
        
        <div v-if="selectedProduct" class="p-6 overflow-y-auto">
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

    <!-- Modal: No hay categorías -->
    <div v-if="showNoCategoriesModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="showNoCategoriesModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-2xl max-w-md w-full p-6 animate-fade-in">
        <div class="text-center">
          <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No hay categorías disponibles</h3>
          <p class="text-gray-600 mb-6">Antes de crear un producto, debes crear al menos una categoría para organizarlo correctamente.</p>
          
          <div class="flex flex-col space-y-3">
            <button @click="openCategoryModal" 
                    class="w-full px-4 py-3 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white font-bold rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
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
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="showCategoryModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-2xl max-w-lg w-full animate-fade-in">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-lg">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-white/20 dark:bg-white/10 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
              <h2 class="text-lg font-bold text-white">Nueva Categoría</h2>
            </div>
            <button @click="showCategoryModal = false" 
                    class="text-white/80 hover:text-white hover:bg-white/10 dark:hover:bg-white/5 p-2 rounded-lg transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <form @submit.prevent="saveCategory" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Nombre de la Categoría *</label>
            <input v-model="categoryForm.name" 
                   type="text" 
                   required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="Ej: Electrónica, Ropa, Alimentos">
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Descripción</label>
            <textarea v-model="categoryForm.description" 
                      rows="2"
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Descripción opcional de la categoría">
            </textarea>
          </div>

          <!-- Selector de Icono -->
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Icono de la Categoría</label>
            <div class="grid grid-cols-8 gap-2 max-h-32 overflow-y-auto border border-gray-300 rounded-lg p-2">
              <button
                v-for="icon in availableIcons"
                :key="icon.id"
                type="button"
                @click="categoryForm.icon = icon.id"
                :class="[
                  'p-2 rounded-lg text-xl transition-all duration-200',
                  categoryForm.icon === icon.id
                    ? 'bg-purple-100 border-2 border-purple-500 shadow-md'
                    : 'bg-gray-50 border border-gray-200 hover:bg-gray-100 hover:border-gray-300'
                ]"
                :title="icon.name"
              >
                {{ icon.emoji }}
              </button>
            </div>
          </div>

          <!-- Selector de Color -->
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Color de la Categoría</label>
            <div class="flex items-center gap-3">
              <input 
                v-model="categoryForm.color" 
                type="color"
                class="w-16 h-10 rounded-lg border border-gray-300 cursor-pointer"
              />
              <input 
                v-model="categoryForm.color" 
                type="text"
                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="#3b82f6"
              />
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button type="button" 
                    @click="showCategoryModal = false"
                    class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
              Cancelar
            </button>
            <button type="submit" 
                    :disabled="!categoryForm.name"
                    class="px-4 py-2.5 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white font-bold rounded-lg shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
              Crear Categoría
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- TOUR FINAL ÉPICO -->
  <ContextualTour
    ref="productsTourRef"
    :steps="productsTourSteps"
    :auto-start="false"
    @complete="handleProductsTourComplete"
    @skip="handleProductsTourComplete"
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
      <div v-if="showProductsWelcomeModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.7);">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
          <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-6 text-center">
            <div class="w-20 h-20 bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">¡Prueba Final!</h2>
            <p class="text-indigo-100 text-sm">Última lección antes de empezar a vender</p>
          </div>
          
          <div class="px-8 py-6">
            <p class="text-gray-700 dark:text-zinc-300 text-center mb-6 leading-relaxed">
              Esta es tu última prueba. Te mostraremos cómo funciona el módulo de productos y las diferentes formas de visualizar tu inventario.
            </p>
            
            <div class="bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4 mb-6">
              <p class="text-sm text-indigo-900 font-medium">Con esto quedarás listo para:</p>
              <ul class="mt-2 space-y-1 text-sm text-indigo-800">
                <li>✓ Gestionar tu inventario completo</li>
                <li>✓ Crear y editar productos</li>
                <li>✓ Cambiar entre vistas (Tarjetas/Tabla)</li>
                <li>✓ ¡Empezar a vender!</li>
              </ul>
            </div>
            
            <div class="flex gap-3">
              <button
                @click="showProductsWelcomeModal = false"
                class="flex-1 px-4 py-3 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-700 dark:text-zinc-300 font-bold rounded-xl transition-colors"
              >
                Omitir
              </button>
              <button
                @click="handleProductsWelcomeStart"
                class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold rounded-xl shadow-lg transition-all"
              >
                ¡Vamos!
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
      <div v-if="showFinalModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.7);">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform">
          <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-6 text-center">
            <div class="w-20 h-20 bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">¡Felicitaciones!</h2>
            <p class="text-green-100 text-sm">Entrenamiento completado exitosamente</p>
          </div>
          
          <div class="px-8 py-6">
            <p class="text-gray-700 dark:text-zinc-300 text-center mb-6 leading-relaxed font-medium">
              Ya dominas todas las funciones del sistema POS
            </p>
            
            <div class="bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
              <p class="text-sm text-green-900 font-bold mb-2">Ahora puedes:</p>
              <ul class="space-y-1.5 text-sm text-green-800">
                <li class="flex items-center space-x-2">
                  <span class="text-green-600">✓</span>
                  <span>Gestionar el control de caja</span>
                </li>
                <li class="flex items-center space-x-2">
                  <span class="text-green-600">✓</span>
                  <span>Realizar ventas y cotizaciones</span>
                </li>
                <li class="flex items-center space-x-2">
                  <span class="text-green-600">✓</span>
                  <span>Administrar clientes y descuentos</span>
                </li>
                <li class="flex items-center space-x-2">
                  <span class="text-green-600">✓</span>
                  <span>Controlar tu inventario completo</span>
                </li>
              </ul>
            </div>

            <div class="bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-6 text-center">
              <p class="text-sm text-blue-900 dark:text-blue-300 font-bold">
                ¡A trabajar! Crea tus productos y empieza a vender 🚀
              </p>
            </div>
            
            <button
              @click="closeFinalModal"
              class="w-full px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-xl shadow-lg transition-all"
            >
              Entendido
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- 🏭 Tooltip: Stock por Bodega -->
  <Teleport to="body">
    <div v-if="stockTooltip.visible" 
         class="fixed z-[9999] bg-white dark:bg-zinc-900 shadow-xl rounded-lg border border-gray-300 dark:border-zinc-700 p-3 min-w-[200px]"
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
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, nextTick, Teleport, Transition } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { productsService } from '../services/productsService.js'
import { categoriesService } from '../services/categoriesService.js'
import { warehouseService } from '../services/warehouseService.js'
import { appStore } from '../store/appStore.js'
import TablePaginator from './TablePaginator.vue'
import ContextualTour from './ContextualTour.vue'

// Props (para recibir filtros desde navegación AI)
const props = defineProps({
  queryParams: {
    type: Object,
    default: () => ({})
  }
})

// Router - DEBE estar a nivel de setup, NO dentro de onMounted
const route = useRoute()
const router = useRouter()

// 🖼️ Función utilitaria para manejo inteligente de imágenes
const getProductImage = (product) => {
  // Intentar múltiples propiedades de imagen
  const imageUrl = product?.image_url || product?.image || product?.img || product?.photo
  
  // Si la imagen es base64 (data:image), devolverla directamente
  if (imageUrl && imageUrl.startsWith('data:image')) {
    return imageUrl
  }
  
  // Si hay imagen URL normal, devolverla
  if (imageUrl && imageUrl.length > 10) {
    return imageUrl
  }
  
  // Generar avatar dinámico si no hay imagen
  return generateDynamicAvatar(product?.name || 'Producto')
}

// Generar avatar dinámico SVG
const generateDynamicAvatar = (name) => {
  const initials = name
    .split(' ')
    .map(word => word[0])
    .join('')
    .substring(0, 2)
    .toUpperCase() || 'P'
    
  // Colores pastel modernos para el fondo
  const colors = [
    '#F1F5F9', // Slate
    '#EFF6FF', // Blue
    '#ECFDF5', // Emerald
    '#FEF3C7', // Amber
    '#FEE2E2', // Red
    '#F3E8FF', // Purple
    '#E0E7FF', // Indigo
    '#FAE8FF', // Fuchsia
    '#ECFEFF'  // Cyan
  ]
  
  // Colores de texto correspondientes (más oscuros)
  const textColors = [
    '#64748B', // Slate
    '#3B82F6', // Blue
    '#10B981', // Emerald
    '#F59E0B', // Amber
    '#EF4444', // Red
    '#8B5CF6', // Purple
    '#6366F1', // Indigo
    '#D946EF', // Fuchsia
    '#06B6D4'  // Cyan
  ]
  
  // Hash simple para seleccionar color consistente
  let hash = 0
  for (let i = 0; i < name.length; i++) {
    hash = name.charCodeAt(i) + ((hash << 5) - hash)
  }
  const index = Math.abs(hash) % colors.length
  const bgColor = colors[index]
  const textColor = textColors[index]
  
  return `data:image/svg+xml;base64,${btoa(`
    <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect width="200" height="200" fill="${bgColor}"/>
      <text x="50%" y="50%" dy=".35em" fill="${textColor}" font-family="Inter, system-ui, sans-serif" font-size="80" font-weight="bold" text-anchor="middle">${initials}</text>
    </svg>
  `)}`
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
    selector: '#tour-view-toggle',
    title: 'Vistas de Productos',
    content: `
      <p class="text-sm text-gray-700 mb-2">
        Cambia entre <strong>Tarjetas</strong> (visual) y <strong>Tabla</strong> (compacta).
      </p>
      <p class="text-xs text-gray-600 italic">
        Observa el cambio automático en unos segundos...
      </p>
    `,
    position: 'bottom'
  },
  {
    selector: '#tour-new-product',
    title: 'Crear Productos',
    content: `
      <p class="text-sm text-gray-700 mb-2">
        Haz clic aquí para <strong>agregar nuevos productos</strong> a tu inventario.
      </p>
      <div class="bg-blue-50 rounded-lg p-2 mt-2">
        <p class="text-xs text-blue-800">
          <strong>Datos necesarios:</strong> Nombre, precio, stock, categoría y código de barras.
        </p>
      </div>
    `,
    position: 'left'
  }
])

// Variable para guardar la vista original del usuario
const originalViewMode = ref('table')

const handleProductsWelcomeStart = () => {
  showProductsWelcomeModal.value = false
  isTourActive.value = true // Activar productos fantasma
  loading.value = false // Desactivar loading para mostrar productos
  
  // Guardar vista original del usuario
  originalViewMode.value = viewMode.value
  
  productsTourRef.value.startTourConfirmed()
  
  // 🎬 Demo AUTOMÁTICA: cambiar vista después de 3 segundos
  setTimeout(() => {
    // Cambiar a la vista contraria
    viewMode.value = originalViewMode.value === 'table' ? 'grid' : 'table'
  }, 3000)
}

const showFinalModal = ref(false)

const handleProductsTourComplete = () => {
  // Mostrar modal de finalización
  showFinalModal.value = true
  isTourActive.value = false // Desactivar productos fantasma
  
  // Restaurar vista original del usuario
  viewMode.value = originalViewMode.value
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
const isEditing = ref(false)
const selectedProduct = ref(null)

// Form de categoría
const categoryForm = ref({
  name: '',
  description: '',
  icon: 'shopping-bag',
  color: '#3b82f6'
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
  warehouse_id: null, // 🏢 Bodega donde se guardará el producto
  warehouseStock: {}, // 🏢 Stock por cada tienda { warehouse_id: cantidad }
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
    const params = {}
    
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
    console.log('Respuesta productos:', response)
    
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
const getWarehouseStock = (product, warehouseId) => {
  if (!product.warehouses || product.warehouses.length === 0) {
    return 0
  }
  
  const warehouse = product.warehouses.find(w => w.id === warehouseId || w.warehouse_id === warehouseId)
  if (!warehouse) return 0
  
  // Intentar obtener desde pivot o directamente
  return warehouse.pivot?.stock || warehouse.stock || 0
}

// 🏢 Helper: Obtener stock total de un producto
const getTotalStock = (product) => {
  if (!product.warehouses || product.warehouses.length === 0) {
    return product.current_stock || 0
  }
  
  return product.warehouses.reduce((total, warehouse) => {
    const stock = warehouse.pivot?.stock || warehouse.stock || 0
    return total + stock
  }, 0)
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
  
  // Cargar bodegas antes de abrir el modal
  await loadWarehouses()
  
  // Inicializar warehouseStock con todas las tiendas disponibles según el plan en 0
  const warehouseStock = {}
  availableWarehouses.value.forEach(warehouse => {
    warehouseStock[warehouse.id] = 0
  })
  console.log('🏭 Inicializando formulario nuevo producto')
  console.log('📦 Warehouses totales:', warehouses.value.length)
  console.log('✅ Warehouses disponibles según plan:', availableWarehouses.value.length)
  console.log('🔢 warehouseStock inicializado:', warehouseStock)
  
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
  
  // Cargar bodegas para obtener todas las tiendas disponibles
  await loadWarehouses()
  
  // Inicializar warehouseStock con todas las tiendas en 0
  const warehouseStock = {}
  warehouses.value.forEach(warehouse => {
    warehouseStock[warehouse.id] = 0
  })
  
  // Cargar el stock por tienda si existe
  if (product.alternative_warehouses && Array.isArray(product.alternative_warehouses)) {
    product.alternative_warehouses.forEach(warehouse => {
      if (warehouse.id) {
        warehouseStock[warehouse.id] = parseInt(warehouse.stock || 0)
      }
    })
  }
  // Si el producto tiene warehouse_id (tienda actual), cargar su stock
  if (product.warehouse_id) {
    warehouseStock[product.warehouse_id] = parseInt(product.current_stock || product.stock || 0)
  }
  
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
    warehouseStock: warehouseStock,
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
  
  showProductModal.value = true
}

const viewProduct = (product) => {
  selectedProduct.value = product
  showViewModal.value = true
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
    
    await productsService.update(product.id, { 
      ...product, 
      is_active: newStatus,
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
      
      // Abrir modal de crear producto automáticamente
      openCreateModal()
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

// Calcular stock total sumando todas las tiendas
const calculateTotalStock = () => {
  // Si hay solo 1 bodega, usar el campo stock directamente
  if (availableWarehouses.value.length === 1) {
    return parseInt(productForm.value.stock) || 0
  }
  
  // Si hay múltiples bodegas, sumar el stock de cada una
  if (!productForm.value.warehouseStock) return 0
  return Object.values(productForm.value.warehouseStock).reduce((sum, stock) => {
    return sum + (parseInt(stock) || 0)
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
    console.log('📦 Total stock calculado:', totalStock)
    console.log('✅ Bodegas disponibles:', availableWarehouses.value.map(w => ({ id: w.id, name: w.name })))
    
    // Transformar los datos para que coincidan con los campos esperados por la API
    const apiData = {
      name: productForm.value.name.trim(),
      description: productForm.value.description?.trim() || '',
      sku: productForm.value.sku?.trim() || `SKU-${Date.now()}`, // Generar SKU automático si está vacío
      barcode: productForm.value.barcode?.trim() || '',
      category_id: parseInt(productForm.value.category_id),
      supplier_id: null, // Permitir nulo ya que no hay selector de proveedor en el formulario
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
      // 🏢 Stock por cada tienda (nuevo sistema multi-tienda)
      warehouse_stocks: productForm.value.warehouseStock || {},
      // 📏 Unidades de Medida (NUEVO)
      measurement_unit: productForm.value.measurement_unit || 'unit',
      allow_decimal: productForm.value.allow_decimal || false
    }

    console.log('📦 Datos enviados a la API:', apiData)
    console.log('🏭 warehouse_stocks detallado:', JSON.stringify(apiData.warehouse_stocks, null, 2))
    
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

// Inicialización
onMounted(async () => {
  console.log('Módulo de productos inicializado')
  
  // 🔧 Cargar preferencias del usuario primero
  loadUserPreferences()
  
  await loadCategories()
  await loadWarehouses() // 🏢 Cargar bodegas disponibles
  
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
</script>

<style scoped>
/* Transiciones suaves */
* {
  transition: all 0.2s ease-in-out;
}
</style>