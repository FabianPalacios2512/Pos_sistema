<template>
  <div class="min-h-screen font-sans mx-8" style="background-color: #F4F6F8;">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header Simple y Elegante -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-black rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Productos</h1>
            <p class="text-sm text-gray-600">Administra tu inventario y catálogo</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Neutro (Exportar) -->
          <button @click="exportProducts"
                  class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-600 text-sm font-bold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:border-slate-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar</span>
          </button>
          
          <!-- Botón Secundario (Actualizar) -->
          <button @click="refreshProducts"
                  :disabled="loading"
                  class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-600 text-sm font-bold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:border-slate-300"
                  :class="{ 'opacity-50 cursor-not-allowed': loading }">
            <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>{{ loading ? 'Sincronizando' : 'Actualizar' }}</span>
          </button>
          
          <!-- Botón Principal (Nuevo Producto) - Negro -->
          <button id="tour-new-product"
                  @click="openCreateModal"
                  class="px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-900/20 transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Nuevo Producto</span>
          </button>
        </div>
      </div>
      </div>

      <!-- Métricas Principales -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
        <!-- Total Productos -->
        <div class="bg-white rounded-xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Total Productos</h3>
              </div>
              <p class="text-2xl font-black text-slate-900 mb-0.5">{{ displayProducts.length }}</p>
              <p class="text-xs text-slate-400 font-medium">en el sistema</p>
            </div>
          </div>
        </div>

        <!-- Productos Activos -->
        <div class="bg-white rounded-xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Activos</h3>
              </div>
              <p class="text-2xl font-black text-slate-900 mb-0.5">{{ activeProducts }}</p>
              <p class="text-xs text-slate-400 font-medium">disponibles</p>
            </div>
          </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white rounded-xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Stock Bajo</h3>
              </div>
              <p class="text-2xl font-black text-slate-900 mb-0.5">{{ lowStockProducts }}</p>
              <p class="text-xs text-slate-400 font-medium">alertas</p>
            </div>
          </div>
        </div>

        <!-- Valor Total -->
        <div class="bg-white rounded-xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Valor Total</h3>
              </div>
              <p class="text-2xl font-black text-slate-900 mb-0.5">${{ formatCurrency(totalValue) }}</p>
              <p class="text-xs text-slate-400 font-medium">inventario</p>
            </div>
          </div>
        </div>

        <!-- Categorías -->
        <div class="bg-white rounded-xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Categorías</h3>
              </div>
              <p class="text-2xl font-black text-slate-900 mb-0.5">{{ uniqueCategories }}</p>
              <p class="text-xs text-slate-400 font-medium">tipos</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenedor Principal Tour - Filtros + Productos -->
      <div id="tour-products-main">
      <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-200 mb-6">
      <div class="flex flex-wrap items-center gap-4">
        <!-- Búsqueda -->
        <div class="flex-1 min-w-[200px] relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar productos por nombre, SKU..."
            class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400">
        </div>
        
        <!-- Categoría -->
        <div class="min-w-[160px]">
          <select
            v-model="categoryFilter"
            class="w-full pl-3 pr-8 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50 text-slate-600 font-medium">
            <option value="">Todas las categorías</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
          </select>
        </div>
        
        <!-- Estado -->
        <div class="min-w-[140px]">
          <select
            v-model="statusFilter"
            class="w-full pl-3 pr-8 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50 text-slate-600 font-medium">
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
            class="w-full pl-3 pr-8 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50 text-slate-600 font-medium">
            <option value="name">Por Nombre</option>
            <option value="price">Por Precio</option>
            <option value="stock">Por Stock</option>
            <option value="created_at">Por Fecha</option>
          </select>
        </div>
        
        <!-- Toggle Vista -->
        <div id="tour-view-toggle" class="flex items-center bg-slate-100 rounded-lg p-1 border border-slate-200">
          <button
            @click="setViewMode('grid')"
            :class="[
              'flex items-center justify-center px-3 py-1.5 rounded-md transition-all text-xs font-bold',
              viewMode === 'grid' 
                ? 'bg-white text-slate-900 shadow-sm' 
                : 'text-slate-500 hover:text-slate-700'
            ]">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            Tarjetas
          </button>
          
          <button
            @click="setViewMode('table')"
            :class="[
              'flex items-center justify-center px-3 py-1.5 rounded-md transition-all text-xs font-bold',
              viewMode === 'table' 
                ? 'bg-white text-slate-900 shadow-sm' 
                : 'text-slate-500 hover:text-slate-700'
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
          <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
          <p class="text-sm text-gray-500">Cargando productos...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="!loading && !paginatedProducts.length" class="flex justify-center items-center py-20">
        <div class="flex flex-col items-center space-y-3">
          <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-700">No hay productos</p>
            <p class="text-xs text-gray-500 mt-1">{{ searchTerm ? 'No se encontraron productos que coincidan con tu búsqueda' : 'Comienza agregando tu primer producto' }}</p>
          </div>
          <button v-if="!searchTerm" 
                  @click="openCreateModal" 
                  class="mt-2 px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors inline-flex items-center space-x-2">
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
             class="bg-white rounded-xl shadow-sm border border-slate-200 hover:shadow-md hover:border-blue-300 transition-all duration-300 overflow-hidden group flex flex-col cursor-pointer">
          
          <!-- Imagen del producto -->
          <div class="relative h-40 bg-slate-50 overflow-hidden">
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
                <h3 class="font-bold text-slate-900 text-sm leading-tight line-clamp-2 group-hover:text-blue-600 transition-colors" :title="product.name">
                  {{ product.name }}
                </h3>
              </div>
              <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wide mb-2">
                {{ product.category?.name || 'Sin categoría' }}
              </p>
              <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
                  {{ product.sku || 'SIN SKU' }}
                </span>
              </div>
            </div>
            
            <!-- Precio y Stock -->
            <div class="flex items-end justify-between mb-4 pt-3 border-t border-slate-100">
              <div>
                <p class="text-[10px] text-slate-500 font-medium mb-0.5">Precio</p>
                <span class="font-black text-slate-900 text-lg leading-none">
                  ${{ formatCurrency(product.sale_price) }}
                </span>
              </div>
              <div class="text-right">
                <p class="text-[10px] text-slate-500 font-medium mb-0.5">Stock</p>
                <span class="font-bold text-slate-700 text-sm bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100">
                  {{ product.current_stock || 0 }}
                </span>
              </div>
            </div>

            <!-- Acciones (Ghost Style) -->
            <div class="grid grid-cols-3 gap-2">
              <button @click.stop="viewProduct(product)" 
                      class="flex items-center justify-center py-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                      title="Ver Detalles">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
              
              <button @click.stop="editProduct(product)" 
                      class="flex items-center justify-center py-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all border border-transparent hover:border-amber-100"
                      title="Editar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>
              
              <button @click.stop="toggleProductStatus(product)" 
                      class="flex items-center justify-center py-2 rounded-lg transition-all border border-transparent"
                      :class="getProductStatus(product) !== false 
                        ? 'text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100' 
                        : 'text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 hover:border-emerald-100'"
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

    <!-- Vista de Tabla -->
  <div v-else class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden mb-8">
      <!-- Header de tabla -->
      <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center justify-between">
        <div>
          <h2 class="text-base font-bold text-slate-900">Catálogo de Productos</h2>
          <p class="text-xs text-slate-600 mt-0.5">{{ totalItems }} productos encontrados</p>
        </div>
      </div>
      
      <!-- Tabla -->
      <table class="min-w-full">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200">
            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">
              Producto
            </th>
            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">
              Categoría
            </th>
            <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-500 uppercase tracking-wider">
              Precio
            </th>
            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">
              Stock
            </th>
            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">
              Estado
            </th>
            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-100">
          <!-- Loading State -->
          <tr v-if="loading">
            <td colspan="6" class="px-4 py-12 text-center">
              <div class="flex flex-col items-center space-y-3">
                <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-sm text-gray-600 font-medium">Cargando productos...</p>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-else-if="!loading && !paginatedProducts.length">
            <td colspan="6" class="px-4 py-12 text-center">
              <div class="flex flex-col items-center space-y-3">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-2">
                  <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                </div>
                <div>
                  <p class="text-base font-bold text-slate-800">No hay productos</p>
                  <p class="text-sm text-slate-500 mt-1">{{ searchTerm ? 'No se encontraron productos que coincidan con tu búsqueda' : 'Comienza agregando tu primer producto' }}</p>
                </div>
                <button v-if="!searchTerm" 
                        @click="openCreateModal" 
                        class="mt-4 px-4 py-2 bg-black hover:bg-slate-800 text-white rounded-lg text-sm font-bold transition-all shadow-lg hover:shadow-xl inline-flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  <span>Nuevo Producto</span>
                </button>
              </div>
            </td>
          </tr>

          <!-- Product Rows -->
          <tr v-for="product in paginatedProducts" 
              :key="product.id"
              @click="viewProduct(product)"
              class="hover:bg-slate-50/80 transition-colors group cursor-pointer">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <div class="relative w-10 h-10 flex-shrink-0">
                  <img :src="getProductImage(product)" 
                       :alt="product.name" 
                       @error="(e) => handleImageError(e, product)"
                       class="w-full h-full object-cover rounded-lg shadow-sm border border-slate-100">
                </div>
                <div>
                  <div class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ product.name }}</div>
                  <div class="text-[11px] font-medium text-slate-500 font-mono mt-0.5">{{ product.sku || 'SIN SKU' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                {{ product.category?.name || 'Sin categoría' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right">
              <div class="text-sm font-black text-slate-900">
                ${{ formatCurrency(product.sale_price) }}
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <div class="flex flex-col items-center">
                <span class="text-sm font-bold text-slate-700">{{ product.current_stock || 0 }}</span>
                <span v-if="(product.current_stock || 0) <= (product.min_stock || 0)" 
                      class="text-[10px] font-bold text-rose-500 animate-pulse">
                  ¡Stock Bajo!
                </span>
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <span :class="getProductStatus(product) ? 
                'bg-emerald-50 text-emerald-700 border-emerald-100' : 
                'bg-rose-50 text-rose-700 border-rose-100'" 
                class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide">
                {{ getProductStatus(product) ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <button @click.stop="viewProduct(product)" 
                  class="p-2.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                  title="Ver detalles">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </button>
                <button @click.stop="editProduct(product)" 
                  class="p-2.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all"
                  title="Editar producto">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button @click.stop="toggleProductStatus(product)" 
                  class="p-2.5 rounded-lg transition-all"
                  :class="getProductStatus(product) !== false 
                    ? 'text-slate-400 hover:text-rose-600 hover:bg-rose-50' 
                    : 'text-slate-400 hover:text-emerald-600 hover:bg-emerald-50'"
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
           class="bg-white rounded-lg shadow-lg border-l-4 p-4 max-w-sm transform transition-all duration-300 ease-in-out"
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
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         @click.self="showStatusConfirmModal = false">
      <div class="bg-white rounded-xl w-full max-w-md shadow-2xl overflow-hidden animate-fade-in">
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
          <p class="text-slate-700 mb-4">
            ¿Estás seguro que deseas <span class="font-bold">{{ pendingStatusChange?.newStatus ? 'habilitar' : 'deshabilitar' }}</span> el producto:
          </p>
          <div class="bg-slate-50 rounded-lg p-4 border border-slate-200 mb-6">
            <p class="font-bold text-slate-900">{{ pendingStatusChange?.product?.name }}</p>
            <p class="text-sm text-slate-500 mt-1">SKU: {{ pendingStatusChange?.product?.sku || 'N/A' }}</p>
          </div>
          <p class="text-sm text-slate-600">
            {{ pendingStatusChange?.newStatus ? 'El producto estará disponible para la venta.' : 'El producto no estará disponible para la venta.' }}
          </p>
        </div>

        <!-- Actions -->
        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end space-x-3">
          <button @click="showStatusConfirmModal = false" 
                  class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors font-medium">
            Cancelar
          </button>
          <button @click="confirmStatusChange" 
                  class="px-4 py-2 bg-slate-900 hover:bg-black text-white rounded-lg transition-colors font-medium">
            Confirmar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Categoría Inactiva -->
    <div v-if="showCategoryInactiveModal" 
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         @click.self="showCategoryInactiveModal = false">
      <div class="bg-white rounded-xl w-full max-w-md shadow-2xl overflow-hidden animate-fade-in">
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
          <p class="text-slate-700 mb-4">
            No puedes activar el producto <span class="font-bold">{{ pendingStatusChange?.product?.name }}</span> porque su categoría está inactiva.
          </p>
          <div class="bg-blue-50 rounded-lg p-4 border border-blue-200 mb-6">
            <div class="flex items-center space-x-2 mb-2">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
              <p class="font-bold text-blue-900">{{ pendingStatusChange?.category?.name }}</p>
            </div>
            <p class="text-sm text-blue-700">Estado: Inactiva</p>
          </div>
          <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <p class="text-sm text-green-800">
              <strong>Solución:</strong> Al activar la categoría, este producto se reactivará automáticamente junto con todos los demás productos que fueron desactivados con ella.
            </p>
          </div>
        </div>

        <!-- Actions -->
        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end space-x-3">
          <button @click="showCategoryInactiveModal = false" 
                  class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors font-medium">
            Cancelar
          </button>
          <button @click="activateCategoryAndProduct" 
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
            Activar Categoría
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Crear/Editar Producto -->
    <div v-if="showProductModal" 
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         @click.self="showProductModal = false">
      <div class="bg-white rounded-lg w-full max-w-4xl shadow-xl max-h-[90vh] overflow-hidden">
        
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-900">
                  {{ isEditing ? 'Editar Producto' : 'Nuevo Producto' }}
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                  {{ isEditing ? 'Modifica la información del producto' : 'Agrega un nuevo producto al inventario' }}
                </p>
              </div>
            </div>
            <button @click="showProductModal = false" 
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="flex h-[calc(90vh-120px)]">
          <!-- Formulario Principal -->
          <div class="flex-1 p-6 overflow-y-auto">
            <form @submit.prevent="saveProduct" class="space-y-4">
              
              <!-- Información Básica -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                  <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Información Básica
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nombre del Producto *</label>
                    <input v-model="productForm.name" 
                           type="text" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="Nombre del producto">
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Categoría *</label>
                    <select v-model="productForm.category_id" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                      <option value="">Seleccionar categoría</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">SKU</label>
                    <input v-model="productForm.sku" 
                           type="text" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="Código único del producto">
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Código de Barras</label>
                    <div class="relative">
                      <input v-model="productForm.barcode" 
                             type="text" 
                             class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                             placeholder="Código de barras">
                      <button type="button" 
                              @click="generateBarcode"
                              title="Generar código de barras"
                              class="absolute right-2 top-1/2 transform -translate-y-1/2 p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
                
                <div class="mt-4">
                  <label class="block text-xs font-bold text-gray-700 mb-1">Descripción</label>
                  <textarea v-model="productForm.description" 
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                            placeholder="Descripción detallada del producto">
                  </textarea>
                </div>
              </div>

              <!-- Precios e Inventario -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                  <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                  </svg>
                  Precios e Inventario
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Precio de Costo *</label>
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">$</span>
                      <input v-model="productForm.cost" 
                             type="number" 
                             step="0.01"
                             min="0"
                             required
                             class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                             placeholder="0.00">
                    </div>
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Precio de Venta *</label>
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">$</span>
                      <input v-model="productForm.price" 
                             type="number" 
                             step="0.01"
                             min="0"
                             required
                             class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                             placeholder="0.00">
                    </div>
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Margen de Ganancia</label>
                    <div class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-medium text-gray-700">
                      {{ productForm.price && productForm.cost ? 
                        (((productForm.price - productForm.cost) / productForm.cost) * 100).toFixed(1) + '%' : 
                        '0%' }}
                    </div>
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Stock Inicial</label>
                    <input v-model="productForm.stock" 
                           type="number" 
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="0">
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Stock Mínimo</label>
                    <input v-model="productForm.min_stock" 
                           type="number" 
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="5">
                  </div>
                  
                  <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Stock Máximo</label>
                    <input v-model="productForm.max_stock" 
                           type="number" 
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="100">
                  </div>
                </div>
              </div>

              <!-- Estado Activo -->
              <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center space-x-3">
                  <input v-model="productForm.active" 
                         type="checkbox" 
                         class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                  <div>
                    <label class="text-sm font-bold text-gray-900">Producto Activo</label>
                    <p class="text-xs text-gray-500">El producto estará disponible para la venta</p>
                  </div>
                </div>
              </div>
            </form>
          </div>
          
          <!-- Sidebar para Imagen -->
          <div class="w-80 bg-gray-50 border-l border-gray-200 p-6">
            <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
              <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Imagen del Producto
            </h4>
            
            <!-- Selector de método -->
            <div class="flex bg-gray-100 rounded-lg p-1 mb-4">
              <button type="button"
                      @click="imageUploadMethod = 'url'"
                      :class="[
                        'flex-1 px-3 py-2 rounded-md text-xs font-medium transition-all',
                        imageUploadMethod === 'url' 
                          ? 'bg-white text-blue-600 shadow-sm' 
                          : 'text-gray-600 hover:text-gray-800'
                      ]">
                URL Web
              </button>
              <button type="button"
                      @click="imageUploadMethod = 'file'"
                      :class="[
                        'flex-1 px-3 py-2 rounded-md text-xs font-medium transition-all',
                        imageUploadMethod === 'file' 
                          ? 'bg-white text-blue-600 shadow-sm' 
                          : 'text-gray-600 hover:text-gray-800'
                      ]">
                Subir Archivo
              </button>
            </div>
            
            <!-- Subida de archivo -->
            <div v-if="imageUploadMethod === 'file'" class="space-y-3">
              <div @click="$refs.fileInput?.click()" 
                   class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-purple-400 hover:bg-purple-50/30 transition-all">
                
                <input type="file" 
                       ref="fileInput"
                       @change="handleFileUpload"
                       accept="image/*"
                       class="hidden">
                
                <div v-if="!previewImage">
                  <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <h5 class="font-bold text-gray-900 mb-1">Subir Imagen</h5>
                  <p class="text-gray-600 text-sm mb-1">
                    <span class="font-semibold text-purple-600">Haz clic aquí</span> o arrastra una imagen
                  </p>
                  <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 5MB</p>
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
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                     placeholder="https://ejemplo.com/imagen.jpg">
              
              <!-- Preview de URL -->
              <div v-if="productForm.image && isValidUrl(productForm.image)" class="text-center">
                <div class="inline-block relative">
                  <img :src="getProductImage(productForm)" 
                       @error="(e) => { handleImageError(e); imageLoadError = true; }"
                       @load="() => imageLoadError = false"
                       class="max-h-24 rounded-lg border border-gray-200 shadow-sm"
                       alt="Preview">
                  <div v-if="imageLoadError" class="absolute inset-0 bg-red-50 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                      <svg class="w-4 h-4 text-red-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                      </svg>
                      <p class="text-xs text-red-600 font-medium">Error</p>
                    </div>
                  </div>
                </div>
                <p v-if="imageLoadError" class="text-xs text-red-500 mt-1">No se pudo cargar la imagen</p>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-6 space-y-3">
              <button type="button" 
                      @click="saveProduct"
                      :disabled="loading"
                      class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                {{ loading ? 'Guardando...' : (isEditing ? 'Actualizar Producto' : 'Crear Producto') }}
              </button>
              
              <button type="button" 
                      @click="showProductModal = false"
                      class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ver Producto -->
    <div v-if="showViewModal" 
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         @click.self="showViewModal = false">
      <div class="bg-white rounded-lg w-full max-w-2xl shadow-xl max-h-[90vh] overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-900">Detalles del Producto</h3>
                <p class="text-xs text-gray-500 mt-0.5">Información completa del producto</p>
              </div>
            </div>
            <button @click="showViewModal = false" 
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                   class="w-40 h-40 object-cover rounded-lg shadow-sm border border-gray-200">
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ selectedProduct.name }}</h2>
            <div class="flex items-center space-x-2 text-sm">
              <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                {{ selectedProduct.category?.name || 'Sin categoría' }}
              </span>
              <span :class="[
                'px-3 py-1 rounded-full text-xs font-medium',
                getProductStatus(selectedProduct) 
                  ? 'bg-green-100 text-green-700' 
                  : 'bg-red-100 text-red-700'
              ]">
                {{ getProductStatus(selectedProduct) ? 'Activo' : 'Inactivo' }}
              </span>
              <span v-if="(selectedProduct.current_stock || 0) <= (selectedProduct.min_stock || 0)" 
                    class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
                Stock Bajo
              </span>
            </div>
          </div>
          
          <!-- Detalles en grid -->
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">SKU</p>
              <p class="text-base font-bold text-gray-900">{{ selectedProduct.sku || 'Sin SKU' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Precio</p>
              <p class="text-base font-bold text-green-600">${{ formatCurrency(selectedProduct.sale_price) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Stock Actual</p>
              <p class="text-base font-bold text-gray-900">{{ selectedProduct.current_stock || 0 }}</p>
              <div class="flex items-center mt-1 text-xs text-gray-500">
                <span>Mín: {{ selectedProduct.min_stock || 0 }}</span>
                <span class="mx-2">•</span>
                <span>Máx: {{ selectedProduct.max_stock || 0 }}</span>
              </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Costo</p>
              <p class="text-base font-bold text-gray-900">${{ formatCurrency(selectedProduct.cost_price) }}</p>
              <p class="text-xs text-gray-500 mt-1">
                Margen: {{ selectedProduct.sale_price && selectedProduct.cost_price ? 
                  ((selectedProduct.sale_price - selectedProduct.cost_price) / selectedProduct.cost_price * 100).toFixed(1) + '%' : 
                  '0%' }}
              </p>
            </div>
          </div>
          
          <!-- Sección de descripción -->
          <div v-if="selectedProduct.description" class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="flex items-center mb-2 space-x-2">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
              </svg>
              <h4 class="text-sm font-bold text-gray-900">Descripción del Producto</h4>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">{{ selectedProduct.description }}</p>
          </div>

          <!-- Pie del modal con acciones -->
          <div class="flex items-center justify-end space-x-3">
            <button @click="editProduct(selectedProduct)" 
                    class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-sm font-medium inline-flex items-center space-x-2 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              <span>Editar Producto</span>
            </button>
            <button @click="showViewModal = false" 
                    class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg text-sm font-medium transition-colors">
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
      <div class="bg-white rounded-lg shadow-2xl max-w-md w-full p-6 animate-fade-in">
        <div class="text-center">
          <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">No hay categorías disponibles</h3>
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
                    class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
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
      <div class="bg-white rounded-lg shadow-2xl max-w-lg w-full animate-fade-in">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-lg">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
              <h2 class="text-lg font-bold text-white">Nueva Categoría</h2>
            </div>
            <button @click="showCategoryModal = false" 
                    class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-lg transition-colors">
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
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
          <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-6 text-center">
            <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">¡Prueba Final!</h2>
            <p class="text-indigo-100 text-sm">Última lección antes de empezar a vender</p>
          </div>
          
          <div class="px-8 py-6">
            <p class="text-gray-700 text-center mb-6 leading-relaxed">
              Esta es tu última prueba. Te mostraremos cómo funciona el módulo de productos y las diferentes formas de visualizar tu inventario.
            </p>
            
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
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
                class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors"
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
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform">
          <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-6 text-center">
            <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">¡Felicitaciones!</h2>
            <p class="text-green-100 text-sm">Entrenamiento completado exitosamente</p>
          </div>
          
          <div class="px-8 py-6">
            <p class="text-gray-700 text-center mb-6 leading-relaxed font-medium">
              Ya dominas todas las funciones del sistema POS
            </p>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
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

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6 text-center">
              <p class="text-sm text-blue-900 font-bold">
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
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, nextTick, Teleport, Transition } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { productsService } from '../services/productsService.js'
import { categoriesService } from '../services/categoriesService.js'
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
  image: '',
  active: true
})

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

const refreshProducts = async () => {
  console.log('Refrescando productos...')
  await loadProducts()
}

const openCreateModal = () => {
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
  productForm.value = {
    name: '',
    sku: '',
    barcode: '',
    description: '',
    price: '',
    cost: '',
    stock: 0,
    min_stock: 5,
    max_stock: 100,
    category_id: '',
    image: '',
    active: true
  }
  
  // Limpiar estado de imágenes
  previewImage.value = null
  imageLoadError.value = false
  imageUploadMethod.value = 'url'
  
  showProductModal.value = true
}

const editProduct = (product) => {
  // Cerrar modal de detalles si está abierto
  showViewModal.value = false
  
  isEditing.value = true
  // Mapear correctamente los campos del API a los campos del formulario
  productForm.value = {
    id: product.id,
    name: product.name,
    sku: product.sku || '',
    barcode: product.barcode || '',
    description: product.description || '',
    price: parseFloat(product.sale_price || product.price || 0),
    cost: parseFloat(product.cost_price || product.cost || 0),
    stock: parseInt(product.current_stock || product.stock || 0),
    min_stock: parseInt(product.min_stock || 5),
    max_stock: parseInt(product.max_stock || 100),
    category_id: product.category_id,
    image: product.image_url || product.image || '',
    active: getProductStatus(product) !== false
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

const saveProduct = async () => {
  try {
    loading.value = true
    
    // Validar campos requeridos
    if (!productForm.value.name?.trim()) {
      throw new Error('El nombre del producto es requerido')
    }
    if (!productForm.value.category_id) {
      throw new Error('La categoría es requerida')
    }
    if (!productForm.value.cost || productForm.value.cost <= 0) {
      throw new Error('El precio de costo debe ser mayor a 0')
    }
    if (!productForm.value.price || productForm.value.price <= 0) {
      throw new Error('El precio de venta debe ser mayor a 0')
    }
    if (productForm.value.stock === undefined || productForm.value.stock === null || productForm.value.stock < 0) {
      throw new Error('El stock inicial debe ser un número válido (0 o mayor)')
    }

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
      current_stock: parseInt(productForm.value.stock),
      min_stock: productForm.value.min_stock ? parseInt(productForm.value.min_stock) : 0,
      max_stock: productForm.value.max_stock ? parseInt(productForm.value.max_stock) : (parseInt(productForm.value.stock) || 0) * 3,
      unit: productForm.value.unit?.trim() || 'unidad',
      manage_stock: true,
      active: productForm.value.active !== false,
      // Manejo inteligente de imágenes: URLs normales o base64 (archivos subidos)
      image_url: productForm.value.image ? productForm.value.image.trim() : null,
      tags: null
    }

    console.log('Datos enviados a la API:', apiData)
    
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