<template>
  <div class="min-h-screen font-sans bg-[#f8f9fa] dark:bg-[#131314] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Loading - Gemini style -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="text-center">
          <svg class="animate-spin w-10 h-10 text-[#1a73e8] dark:text-[#8ab4f8] mx-auto mb-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6]">Cargando categorías...</p>
        </div>
      </div>

      <template v-else>
      
      <!-- Header - Gemini Style -->
      <div class="flex items-center justify-between pb-4">
        <!-- Título sin icono -->
        <div>
          <h1 class="text-2xl font-semibold text-[#1e1f20] dark:text-[#e3e3e3] tracking-tight">Categorías</h1>
          <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-1">Organiza y clasifica tus productos</p>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Neutro (Exportar) - Gemini style -->
          <button @click="exportCategories"
                  class="px-5 py-2.5 bg-[#f0f4f9] dark:bg-[#1e1f20] hover:bg-[#e4e9ef] dark:hover:bg-[#282a2c] text-[#1e1f20] dark:text-[#e3e3e3] text-sm font-medium rounded-full transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar</span>
          </button>
          
          <!-- Botón Principal (Nueva Categoría) - Gemini style -->
          <button @click="showAddCategoryModal = true"
                  class="px-6 py-2.5 bg-[#1e1f20] dark:bg-[#8ab4f8] hover:bg-black dark:hover:bg-[#aecbfa] text-white dark:text-[#1e1f20] text-sm font-semibold rounded-full transition-all duration-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Nueva Categoría</span>
          </button>
        </div>
      </div>

      <!-- Métricas Principales - Estilo Gemini -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <!-- Total Categorías -->
        <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-2xl px-5 py-4 hover:bg-[#f0f4f9] dark:hover:bg-[#282a2c] transition-all duration-200">
          <div class="flex items-center gap-4">
            <div class="w-11 h-11 bg-white dark:bg-[#282a2c] rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">Total Categorías</p>
              <p class="text-xl font-semibold text-[#1e1f20] dark:text-[#e3e3e3] mt-0.5">{{ totalCategories }}</p>
            </div>
          </div>
        </div>

        <!-- Productos Total -->
        <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-2xl px-5 py-4 hover:bg-[#f0f4f9] dark:hover:bg-[#282a2c] transition-all duration-200">
          <div class="flex items-center gap-4">
            <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">Productos Total</p>
              <p class="text-xl font-semibold text-[#1e1f20] dark:text-[#e3e3e3] mt-0.5">{{ totalProducts }}</p>
            </div>
          </div>
        </div>

        <!-- Más Popular -->
        <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-2xl px-5 py-4 hover:bg-[#f0f4f9] dark:hover:bg-[#282a2c] transition-all duration-200">
          <div class="flex items-center gap-4">
            <div class="w-11 h-11 bg-amber-50 dark:bg-amber-950/40 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">Más Popular</p>
              <p class="text-xl font-semibold text-[#1e1f20] dark:text-[#e3e3e3] mt-0.5 truncate">{{ mostPopularCategory?.name || 'N/A' }}</p>
            </div>
          </div>
        </div>

        <!-- Con Productos -->
        <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-2xl px-5 py-4 hover:bg-[#f0f4f9] dark:hover:bg-[#282a2c] transition-all duration-200">
          <div class="flex items-center gap-4">
            <div class="w-11 h-11 bg-purple-50 dark:bg-purple-950/40 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">Con Productos</p>
              <p class="text-xl font-semibold text-[#1e1f20] dark:text-[#e3e3e3] mt-0.5">{{ categoriesWithProducts }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenedor Unificado: Búsqueda + Tabla - Gemini style -->
      <div class="bg-white dark:bg-[#1e1f20] rounded-2xl overflow-hidden">
        <!-- Filtros/Búsqueda - Gemini style -->
        <div class="p-4 bg-[#f8f9fa] dark:bg-[#282a2c]">
          <div class="flex flex-wrap items-center gap-3">
          <!-- Búsqueda -->
          <div class="flex-1 min-w-48 relative">
            <svg class="absolute left-3.5 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#5f6368] dark:text-[#9aa0a6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Buscar categorías..."
              class="w-full pl-11 pr-4 py-3 text-sm rounded-full bg-white dark:bg-[#3a3a3f] text-[#1e1f20] dark:text-[#e3e3e3] placeholder-[#5f6368] dark:placeholder-[#9aa0a6] focus:outline-none focus:ring-2 focus:ring-[#8ab4f8] transition-all duration-200">
          </div>
          
          <!-- Estado -->
          <select
            v-model="statusFilter"
            class="px-4 py-3 text-sm rounded-full bg-white dark:bg-[#3a3a3f] text-[#1e1f20] dark:text-[#e3e3e3] font-medium focus:outline-none focus:ring-2 focus:ring-[#8ab4f8] transition-all duration-200 min-w-36 cursor-pointer">
            <option value="all">Todas</option>
            <option value="withProducts">Con productos</option>
            <option value="active">Activas</option>
            <option value="inactive">Inactivas</option>
          </select>
          
          <!-- Toggle Vista - Gemini pill style -->
          <div class="flex items-center bg-white dark:bg-[#3a3a3f] rounded-full p-1">
            <button
              @click="setViewMode('grid')"
              :class="[
                'flex items-center justify-center px-3.5 py-1.5 rounded-full transition-all duration-200 text-xs font-medium gap-1.5',
                viewMode === 'grid' 
                  ? 'bg-[#e8f0fe] dark:bg-[#8ab4f8]/20 text-[#1a73e8] dark:text-[#8ab4f8]' 
                  : 'text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#f8f9fa] dark:hover:bg-[#282a2c]'
              ]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
              Tarjetas
            </button>
            
            <button
              @click="setViewMode('table')"
              :class="[
                'flex items-center justify-center px-3.5 py-1.5 rounded-full transition-all duration-200 text-xs font-medium gap-1.5',
                viewMode === 'table' 
                  ? 'bg-[#e8f0fe] dark:bg-[#8ab4f8]/20 text-[#1a73e8] dark:text-[#8ab4f8]' 
                  : 'text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#f8f9fa] dark:hover:bg-[#282a2c]'
              ]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
              </svg>
              Tabla
            </button>
          </div>
          
          <!-- Limpiar Filtros -->
          <button
            @click="clearFilters"
            class="p-2.5 text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#d93025] dark:hover:text-[#f28b82] hover:bg-[#fce8e6] dark:hover:bg-[#d93025]/20 rounded-full transition-all duration-200"
            title="Limpiar filtros">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
          </div>
        </div>

      <!-- Vista de Tarjetas - Gemini style -->
      <div v-if="viewMode === 'grid'">
        <!-- Sin resultados -->
        <div v-if="paginatedCategories.length === 0" class="p-16 text-center">
          <div class="w-16 h-16 bg-[#f8f9fa] dark:bg-[#282a2c] rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-[#5f6368] dark:text-[#9aa0a6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
          <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3] mb-1">No hay categorías</h3>
          <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mb-4">
            {{ filteredCategories.length === 0 ? 'No se encontraron categorías.' : 'No hay categorías en esta página.' }}
          </p>
          <button
            v-if="statusFilter !== 'all' || searchTerm"
            @click="clearFilters"
            class="px-5 py-2.5 bg-[#1a73e8] hover:bg-[#1557b0] text-white text-sm font-medium rounded-full transition-all duration-200">
            Limpiar filtros
          </button>
        </div>
        
        <!-- Grid de categorías - Gemini style -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 p-5">
          <div
            v-for="category in paginatedCategories"
            :key="category.id"
            @click="viewCategoryProducts(category)"
            :class="[
              'bg-[#f8f9fa] dark:bg-[#282a2c] rounded-2xl hover:bg-[#f0f4f9] dark:hover:bg-[#3a3a3f] transition-all duration-200 overflow-hidden group flex flex-col cursor-pointer',
              !category.active && 'opacity-60'
            ]">
            
            <!-- Header -->
            <div class="p-4 pb-3">
              <div class="flex items-center space-x-3 mb-2">
                <!-- Icono de la categoría -->
                <div 
                  class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0"
                  :style="{ backgroundColor: category.color || '#1a73e8' }">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" v-html="getIconSvg(category.icon)"></svg>
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="text-sm font-semibold text-[#1e1f20] dark:text-[#e3e3e3] truncate">{{ category.name }}</h3>
                  <span
                    v-if="!category.active"
                    class="inline-block px-2 py-0.5 bg-[#fce8e6] dark:bg-[#d93025]/20 text-[#d93025] dark:text-[#f28b82] text-[10px] font-medium rounded-full mt-1 uppercase tracking-wide">
                    Inactiva
                  </span>
                </div>
              </div>
              <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] line-clamp-2">{{ category.description }}</p>
            </div>
            
            <!-- Contenido -->
            <div class="px-4 pb-4 flex-1 flex flex-col">
              <!-- Estadísticas -->
              <div class="grid grid-cols-2 gap-3 mb-4 flex-1">
                <div class="text-center bg-white dark:bg-[#1e1f20] rounded-xl p-3">
                  <div class="text-xl font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">{{ category.products_count || 0 }}</div>
                  <div class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider mt-0.5">Productos</div>
                </div>
                <div class="text-center bg-white dark:bg-[#1e1f20] rounded-xl p-3">
                  <div class="text-xl font-semibold text-[#1e8e3e] dark:text-[#81c995]">${{ formatCurrency(category.revenue || 0) }}</div>
                  <div class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider mt-0.5">Ventas</div>
                </div>
              </div>

              <!-- Estado y fecha -->
              <div class="flex justify-between items-center text-xs text-[#5f6368] dark:text-[#9aa0a6] mb-3 pt-3 border-t border-[#e8eaed] dark:border-[#3a3a3f]">
                <span>{{ formatDate(category.created_at) }}</span>
                <span :class="category.active ? 'text-[#1e8e3e] dark:text-[#81c995] font-medium' : 'text-[#d93025] dark:text-[#f28b82] font-medium'">
                  {{ category.active ? 'Activa' : 'Inactiva' }}
                </span>
              </div>

              <!-- Acciones (Ghost Style) - Gemini -->
              <div class="grid grid-cols-3 gap-2">
                <button
                  @click.stop="viewCategoryProducts(category)"
                  class="flex items-center justify-center py-2.5 rounded-full text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#1a73e8] dark:hover:text-[#8ab4f8] hover:bg-[#e8f0fe] dark:hover:bg-[#8ab4f8]/20 transition-all duration-200"
                  title="Ver Productos">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
                
                <button
                  @click.stop="editCategory(category)"
                  class="flex items-center justify-center py-2.5 rounded-full text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#f9ab00] dark:hover:text-[#fdd663] hover:bg-[#fef7e0] dark:hover:bg-[#f9ab00]/20 transition-all duration-200"
                  title="Editar">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                
                <button
                  @click.stop="toggleCategoryStatus(category)"
                  class="flex items-center justify-center py-2.5 rounded-full transition-all duration-200"
                  :class="category.active 
                    ? 'text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#d93025] dark:hover:text-[#f28b82] hover:bg-[#fce8e6] dark:hover:bg-[#d93025]/20' 
                    : 'text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#1e8e3e] dark:hover:text-[#81c995] hover:bg-[#e6f4ea] dark:hover:bg-[#1e8e3e]/20'"
                  :title="category.active ? 'Desactivar' : 'Activar'">
                  <svg v-if="category.active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-10 5a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista de Tabla -->
      <div v-else>
        <!-- Header de tabla - Gemini style -->
        <div class="px-6 py-4 flex items-center justify-between bg-[#f8f9fa] dark:bg-[#282a2c]">
          <div>
            <h2 class="text-base font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">Lista de Categorías</h2>
            <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">{{ totalCategories }} categorías en total</p>
          </div>
        </div>
      
      <!-- Tabla - Gemini style -->
      <table class="min-w-full">
        <thead>
          <tr class="border-b border-[#e8eaed] dark:border-[#3a3a3f]">
            <th class="px-6 py-3.5 text-left text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">
              Categoría
            </th>
            <th class="px-6 py-3.5 text-center text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">
              Productos
            </th>
            <th class="px-6 py-3.5 text-right text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">
              Ventas
            </th>
            <th class="px-6 py-3.5 text-center text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">
              Estado
            </th>
            <th class="px-6 py-3.5 text-left text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">
              Fecha
            </th>
            <th class="px-6 py-3.5 text-center text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="category in paginatedCategories"
            :key="category.id"
            @click="viewCategoryProducts(category)"
            :class="['hover:bg-[#f8f9fa] dark:hover:bg-[#282a2c] transition-colors duration-200 cursor-pointer border-b border-[#f0f4f9] dark:border-[#282a2c] last:border-0', !category.active && 'opacity-60']">
            <td class="px-6 py-4">
              <div class="flex items-center space-x-3">
                <!-- Icono de la categoría -->
                <div 
                  class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                  :style="{ backgroundColor: category.color || '#3b82f6' }">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" v-html="getIconSvg(category.icon)"></svg>
                </div>
                <div>
                  <div class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">{{ category.name }}</div>
                  <div class="text-xs text-[#5f6368] dark:text-[#9aa0a6] line-clamp-1">{{ category.description }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <span class="text-sm font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">{{ category.products_count || 0 }}</span>
            </td>
            <td class="px-6 py-4 text-right">
              <span class="text-sm font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">${{ formatCurrency(category.revenue || 0) }}</span>
            </td>
            <td class="px-6 py-4 text-center">
              <span
                :class="[
                  'px-2.5 py-1 rounded-full text-[10px] font-medium uppercase tracking-wide',
                  category.active 
                    ? 'bg-[#e6f4ea] dark:bg-[#1e8e3e]/20 text-[#1e8e3e] dark:text-[#81c995]' 
                    : 'bg-[#fce8e6] dark:bg-[#d93025]/20 text-[#d93025] dark:text-[#f28b82]'
                ]">
                {{ category.active ? 'Activa' : 'Inactiva' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <span class="text-sm text-[#5f6368] dark:text-[#9aa0a6]">{{ formatDate(category.created_at) }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-1">
                <button
                  @click.stop="viewCategoryProducts(category)"
                  class="p-2 text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#1a73e8] dark:hover:text-[#8ab4f8] hover:bg-[#e8f0fe] dark:hover:bg-[#8ab4f8]/20 rounded-full transition-all duration-200"
                  title="Ver productos">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
                <button
                  @click.stop="editCategory(category)"
                  class="p-2 text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#f9ab00] dark:hover:text-[#fdd663] hover:bg-[#fef7e0] dark:hover:bg-[#f9ab00]/20 rounded-full transition-all duration-200"
                  title="Editar">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button
                  @click.stop="toggleCategoryStatus(category)"
                  class="p-2 rounded-full transition-all duration-200"
                  :class="category.active 
                    ? 'text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#d93025] dark:hover:text-[#f28b82] hover:bg-[#fce8e6] dark:hover:bg-[#d93025]/20' 
                    : 'text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#1e8e3e] dark:hover:text-[#81c995] hover:bg-[#e6f4ea] dark:hover:bg-[#1e8e3e]/20'"
                  :title="category.active ? 'Desactivar' : 'Activar'">
                  <svg v-if="category.active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-10 5a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          
          <!-- Sin resultados -->
          <tr v-if="paginatedCategories.length === 0">
            <td colspan="6" class="px-4 py-12 text-center">
              <div class="flex flex-col items-center space-y-3">
                <div class="w-14 h-14 bg-gray-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center">
                  <svg class="w-7 h-7 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-700 dark:text-zinc-300">No hay categorías</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">No se encontraron categorías con los filtros actuales</p>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Paginador dentro del contenedor unificado - Solo mostrar si hay más de 10 categorías -->
      <div v-if="filteredCategories.length > 10" class="border-t border-gray-100 dark:border-zinc-800/50 bg-gray-50/50 dark:bg-zinc-900/50">
        <TablePaginator
          :currentPage="currentPage"
          :totalPages="totalPages"
          :itemsPerPage="itemsPerPage"
          :totalItems="filteredCategories.length"
          @update:currentPage="currentPage = $event"
          @update:itemsPerPage="itemsPerPage = $event; currentPage = 1" />
      </div>
      </div>
      </div>

      </template>

    <!-- Modal de Confirmación para Cambio de Estado - Gemini Style -->
    <div v-if="showStatusConfirmModal" 
         class="fixed inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         @click.self="showStatusConfirmModal = false">
      <div class="bg-white dark:bg-[#1e1f20] rounded-2xl w-full max-w-md overflow-hidden animate-fade-in">
        <!-- Header - Gemini style -->
        <div class="px-6 py-5 bg-[#f8f9fa] dark:bg-[#282a2c]">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-[#fef7e0] dark:bg-[#f9ab00]/20 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-[#f9ab00] dark:text-[#fdd663]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">Confirmar Cambio</h3>
              <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6]">Esta acción modificará el estado</p>
            </div>
          </div>
        </div>

        <!-- Content - Gemini style -->
        <div class="p-6">
          <p class="text-[#5f6368] dark:text-[#9aa0a6] mb-4">
            ¿Estás seguro que deseas <span class="font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">{{ pendingStatusChange?.newStatus ? 'activar' : 'desactivar' }}</span> la categoría:
          </p>
          <div class="bg-[#f8f9fa] dark:bg-[#282a2c] rounded-xl p-4 mb-5">
            <p class="font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">{{ pendingStatusChange?.category?.name }}</p>
            <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-1">{{ pendingStatusChange?.category?.products_count || 0 }} productos</p>
          </div>
          <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6]">
            {{ pendingStatusChange?.newStatus 
              ? 'La categoría estará disponible en el POS. Los productos que fueron desactivados con la categoría se reactivarán automáticamente.' 
              : 'La categoría NO estará disponible en el POS. Todos los productos activos de esta categoría se desactivarán automáticamente.' }}
          </p>
          <div v-if="!pendingStatusChange?.newStatus" class="mt-4 bg-[#fef7e0] dark:bg-[#f9ab00]/20 rounded-xl p-3">
            <p class="text-xs text-[#b06000] dark:text-[#fdd663]">
              <strong>Nota:</strong> Los productos que ya estaban inactivos permanecerán inactivos. Solo se desactivarán los productos que están actualmente activos.
            </p>
          </div>
          <div v-else class="mt-4 bg-[#e8f0fe] dark:bg-[#8ab4f8]/20 rounded-xl p-3">
            <p class="text-xs text-[#1a73e8] dark:text-[#8ab4f8]">
              <strong>Reactivación inteligente:</strong> Solo se reactivarán los productos que fueron desactivados cuando se desactivó esta categoría.
            </p>
          </div>
        </div>

        <!-- Actions - Gemini style -->
        <div class="px-6 py-4 bg-[#f8f9fa] dark:bg-[#282a2c] flex items-center justify-end space-x-3">
          <button @click="showStatusConfirmModal = false" 
                  class="px-5 py-2.5 bg-white dark:bg-[#3a3a3f] hover:bg-[#f0f4f9] dark:hover:bg-[#4a4a4f] text-[#1e1f20] dark:text-[#e3e3e3] rounded-full transition-all duration-200 font-medium text-sm">
            Cancelar
          </button>
          <button @click="confirmStatusChange" 
                  class="px-6 py-2.5 bg-[#1a73e8] hover:bg-[#1557b0] text-white rounded-full transition-all duration-200 font-medium text-sm">
            Confirmar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Agregar/Editar Categoría - Gemini Style -->
    <div
      v-if="showAddCategoryModal || showEditCategoryModal"
      class="fixed inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-[#1e1f20] rounded-2xl max-w-md w-full">
        <!-- Header - Gemini style -->
        <div class="px-6 py-5 bg-[#f8f9fa] dark:bg-[#282a2c] rounded-t-2xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-11 h-11 bg-[#e8f0fe] dark:bg-[#8ab4f8]/20 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">
                  {{ showAddCategoryModal ? 'Nueva Categoría' : 'Editar Categoría' }}
                </h2>
                <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">
                  {{ showAddCategoryModal ? 'Crear nueva categoría' : 'Modificar categoría existente' }}
                </p>
              </div>
            </div>
            <button
              @click="closeModals"
              class="p-2.5 text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#1e1f20] dark:hover:text-[#e3e3e3] hover:bg-[#e8eaed] dark:hover:bg-[#3a3a3f] rounded-full transition-all duration-200">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Contenido - Gemini style -->
        <div class="p-6 space-y-5">
          <div>
            <label class="block text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] mb-2">Nombre</label>
            <input
              v-model="categoryForm.name"
              type="text"
              placeholder="Nombre de la categoría"
              class="w-full px-4 py-3 text-sm bg-[#f8f9fa] dark:bg-[#282a2c] text-[#1e1f20] dark:text-[#e3e3e3] rounded-xl focus:ring-2 focus:ring-[#8ab4f8] focus:outline-none transition-all duration-200 placeholder-[#5f6368] dark:placeholder-[#9aa0a6]">
          </div>
          
          <div>
            <label class="block text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] mb-2">Descripción</label>
            <textarea
              v-model="categoryForm.description"
              placeholder="Descripción de la categoría"
              rows="3"
              class="w-full px-4 py-3 text-sm bg-[#f8f9fa] dark:bg-[#282a2c] text-[#1e1f20] dark:text-[#e3e3e3] rounded-xl focus:ring-2 focus:ring-[#8ab4f8] focus:outline-none transition-all duration-200 resize-none placeholder-[#5f6368] dark:placeholder-[#9aa0a6]"></textarea>
          </div>

          <!-- Selector de Icono - Gemini style -->
          <div>
            <label class="block text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] mb-2">Icono</label>
            <div class="grid grid-cols-6 gap-2 max-h-48 overflow-y-auto p-3 bg-[#f8f9fa] dark:bg-[#282a2c] rounded-xl">
              <button
                v-for="icon in availableIcons"
                :key="icon.id"
                type="button"
                @click="categoryForm.icon = icon.id"
                :class="[
                  'p-2 rounded-xl transition-all duration-200 hover:scale-105',
                  categoryForm.icon === icon.id 
                    ? 'bg-[#e8f0fe] dark:bg-[#8ab4f8]/20 ring-2 ring-[#1a73e8] dark:ring-[#8ab4f8]' 
                    : 'bg-white dark:bg-[#3a3a3f] hover:bg-[#f0f4f9] dark:hover:bg-[#4a4a4f]'
                ]"
                :title="icon.name">
                <span class="text-2xl">{{ icon.emoji }}</span>
              </button>
            </div>
            <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-2">
              Seleccionado: {{ availableIcons.find(i => i.id === categoryForm.icon)?.name || 'Ninguno' }}
            </p>
          </div>

          <!-- Selector de Color - Gemini style -->
          <div>
            <label class="block text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] mb-2">Color</label>
            <div class="flex items-center space-x-3">
              <input
                v-model="categoryForm.color"
                type="color"
                class="w-12 h-10 rounded-xl cursor-pointer bg-[#f8f9fa] dark:bg-[#282a2c]">
              <input
                v-model="categoryForm.color"
                type="text"
                placeholder="#1a73e8"
                class="flex-1 px-4 py-2.5 text-sm bg-[#f8f9fa] dark:bg-[#282a2c] text-[#1e1f20] dark:text-[#e3e3e3] rounded-xl focus:ring-2 focus:ring-[#8ab4f8] focus:outline-none transition-all duration-200">
            </div>
          </div>
          
          <div class="flex items-center space-x-3 pt-1">
            <input
              v-model="categoryForm.active"
              type="checkbox"
              id="active"
              class="w-4 h-4 text-[#1a73e8] rounded focus:ring-[#8ab4f8] bg-[#f8f9fa] dark:bg-[#282a2c]">
            <label for="active" class="text-sm text-[#5f6368] dark:text-[#9aa0a6]">Categoría activa</label>
          </div>
        </div>

        <!-- Footer - Gemini style -->
        <div class="px-6 py-4 bg-[#f8f9fa] dark:bg-[#282a2c] rounded-b-2xl flex justify-between">
          <div>
            <button
              v-if="showEditCategoryModal"
              @click="deleteCategory"
              class="px-4 py-2.5 bg-white dark:bg-[#3a3a3f] hover:bg-[#fce8e6] dark:hover:bg-[#d93025]/20 text-[#d93025] dark:text-[#f28b82] rounded-full text-sm font-medium transition-all duration-200">
              Eliminar
            </button>
          </div>
          <div class="flex gap-3">
            <button
              @click="closeModals"
              class="px-5 py-2.5 bg-white dark:bg-[#3a3a3f] hover:bg-[#f0f4f9] dark:hover:bg-[#4a4a4f] text-[#1e1f20] dark:text-[#e3e3e3] rounded-full text-sm font-medium transition-all duration-200">
              Cancelar
            </button>
            <button
              @click="saveCategory"
              class="px-6 py-2.5 bg-[#1a73e8] hover:bg-[#1557b0] text-white rounded-full text-sm font-medium transition-all duration-200">
              {{ showAddCategoryModal ? 'Crear' : 'Guardar' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ver Productos de Categoría - Gemini Style -->
    <Teleport to="body">
      <div
        v-if="showProductsModal"
        @click.self="showProductsModal = false"
        class="fixed inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm flex items-center justify-center z-[9999] p-4"
        style="pointer-events: auto;">
        <div 
          @click.stop
          class="bg-white dark:bg-[#1e1f20] rounded-2xl max-w-4xl w-full max-h-[85vh] overflow-hidden"
          style="pointer-events: auto;">
          <!-- Header - Gemini style -->
          <div class="px-6 py-5 bg-[#f8f9fa] dark:bg-[#282a2c]">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-11 h-11 bg-[#e8f0fe] dark:bg-[#8ab4f8]/20 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
                <div>
                  <h2 class="text-lg font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">
                    {{ selectedCategory?.name }}
                  </h2>
                  <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">
                    {{ categoryProducts.length }} productos en esta categoría
                  </p>
                </div>
              </div>
              <button
                @click="showProductsModal = false"
                class="p-2.5 text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#1e1f20] dark:hover:text-[#e3e3e3] hover:bg-[#e8eaed] dark:hover:bg-[#3a3a3f] rounded-full transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Contenido - Tabla Gemini style -->
          <div class="overflow-y-auto max-h-[calc(90vh-160px)] bg-white dark:bg-[#1e1f20]">
            <div v-if="loadingProducts" class="flex flex-col justify-center items-center py-16">
              <svg class="animate-spin w-8 h-8 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-3">Cargando productos...</p>
            </div>
            
            <div v-else-if="categoryProducts.length === 0" class="text-center py-16">
              <div class="w-16 h-16 bg-[#f8f9fa] dark:bg-[#282a2c] rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#5f6368] dark:text-[#9aa0a6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3] mb-1">Sin productos</h3>
              <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6]">Esta categoría aún no tiene productos asignados</p>
            </div>
            
            <!-- Tabla de productos -->
            <table v-else class="min-w-full">
              <thead class="bg-[#f8f9fa] dark:bg-[#282a2c] sticky top-0">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">
                    Producto
                  </th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">
                    Precio
                  </th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">
                    Stock
                  </th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">
                    Estado
                  </th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wider">
                    Acción
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[#e8eaed] dark:divide-[#3a3a3f]">
                <tr
                  v-for="product in categoryProducts"
                  :key="product.id"
                  class="hover:bg-[#f8f9fa] dark:hover:bg-[#282a2c] transition-colors duration-150">
                  <!-- Producto -->
                  <td class="px-6 py-3">
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3] truncate">{{ product.name }}</p>
                      <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] font-mono">{{ product.sku }}</p>
                    </div>
                  </td>
                  <!-- Precio -->
                  <td class="px-4 py-3 text-right">
                    <span class="text-sm font-semibold text-[#1e8e3e] dark:text-[#81c995]">${{ formatCurrency(product.price) }}</span>
                  </td>
                  <!-- Stock -->
                  <td class="px-4 py-3 text-center">
                    <span class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">{{ product.stock }}</span>
                  </td>
                  <!-- Estado -->
                  <td class="px-4 py-3 text-center">
                    <span
                      :class="[
                        'px-2.5 py-1 rounded-full text-[10px] font-medium',
                        product.active 
                          ? 'bg-[#e6f4ea] dark:bg-[#1e8e3e]/20 text-[#1e8e3e] dark:text-[#81c995]' 
                          : 'bg-[#fce8e6] dark:bg-[#d93025]/20 text-[#d93025] dark:text-[#f28b82]'
                      ]">
                      {{ product.active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <!-- Acción -->
                  <td class="px-4 py-3 text-center">
                    <button
                      @click="goToProductEdit(product)"
                      class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#1a73e8] dark:text-[#8ab4f8] hover:bg-[#e8f0fe] dark:hover:bg-[#8ab4f8]/20 rounded-full transition-all duration-200">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                      </svg>
                      Ver
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Footer - Gemini style -->
          <div class="px-6 py-4 bg-[#f8f9fa] dark:bg-[#282a2c] flex justify-end">
            <button
              @click="showProductsModal = false"
              class="px-5 py-2.5 bg-white dark:bg-[#3a3a3f] hover:bg-[#f0f4f9] dark:hover:bg-[#4a4a4f] text-[#1e1f20] dark:text-[#e3e3e3] rounded-full text-sm font-medium transition-all duration-200">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </Teleport>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useToast } from '../composables/useToast.js'
import { categoriesService } from '../services/categoriesService.js'
import { productsService } from '../services/productsService.js'
import { appStore } from '../store/appStore.js'
import { useUIContextStore } from '../store/uiContextStore.js'
import TablePaginator from './TablePaginator.vue'

// Props y Emits
const emit = defineEmits(['navigate'])

// Composables
const { showToast } = useToast()

// Estados
const loading = ref(true)
const loadingProducts = ref(false)
const categories = ref([])
const categoryProducts = ref([])
const selectedCategory = ref(null)

// Filtros
const searchTerm = ref('')
const statusFilter = ref('all')
// Cargar preferencia de vista desde localStorage
const viewMode = ref(localStorage.getItem('categoriesViewMode') || 'table')

// Paginación
const currentPage = ref(1)
const itemsPerPage = ref(12)

// Modales
const showAddCategoryModal = ref(false)
const showEditCategoryModal = ref(false)
const showProductsModal = ref(false)

// Lista de iconos disponibles (emojis profesionales) - 60+ iconos
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
  { id: 'fish', emoji: '�', name: 'Pescado', category: 'food' },
  { id: 'cheese', emoji: '🧀', name: 'Lácteos', category: 'food' },
  
  // Belleza y Cuidado Personal
  { id: 'perfume', emoji: '�💐', name: 'Perfumes', category: 'beauty' },
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
  { id: 'jeans', emoji: '�', name: 'Pantalones', category: 'fashion' },
  { id: 'shoe', emoji: '�👟', name: 'Calzado', category: 'fashion' },
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
  { id: 'mouse', emoji: '�️', name: 'Mouse', category: 'tech' },
  { id: 'tv', emoji: '📺', name: 'Televisores', category: 'tech' },
  { id: 'game', emoji: '🎮', name: 'Videojuegos', category: 'tech' },
  
  // Ferretería y Herramientas
  { id: 'tools', emoji: '�🔧', name: 'Herramientas', category: 'hardware' },
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
  { id: 'gym', emoji: '�', name: 'Gimnasio', category: 'sports' },
  { id: 'bike', emoji: '🚴', name: 'Ciclismo', category: 'sports' },
  
  // Automotriz
  { id: 'car', emoji: '�🚗', name: 'Automotriz', category: 'automotive' },
  { id: 'motorcycle', emoji: '🏍️', name: 'Motocicletas', category: 'automotive' },
  { id: 'tire', emoji: '🛞', name: 'Llantas', category: 'automotive' },
  { id: 'gas', emoji: '⛽', name: 'Combustible', category: 'automotive' },
  
  // Hogar y Muebles
  { id: 'home', emoji: '🏠', name: 'Hogar', category: 'home' },
  { id: 'furniture', emoji: '🛋️', name: 'Muebles', category: 'home' },
  { id: 'bed', emoji: '🛏️', name: 'Colchones', category: 'home' },
  { id: 'lamp', emoji: '💡', name: 'Iluminación', category: 'home' },
  { id: 'kitchen', emoji: '�', name: 'Cocina', category: 'home' },
  { id: 'decoration', emoji: '🖼️', name: 'Decoración', category: 'home' },
  { id: 'door', emoji: '🚪', name: 'Puertas', category: 'home' },
  { id: 'key', emoji: '🔑', name: 'Cerrajería', category: 'home' }
]

// Formulario
const categoryForm = ref({
  name: '',
  description: '',
  icon: 'shopping-bag',
  color: '#3b82f6',
  active: true
})

// Computed
const filteredCategories = computed(() => {
  let filtered = categories.value

  // Filtro de búsqueda
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(category => 
      category.name.toLowerCase().includes(term) ||
      (category.description || '').toLowerCase().includes(term)
    )
  }

  // Filtro por estado
  if (statusFilter.value !== 'all') {
    if (statusFilter.value === 'withProducts') {
      filtered = filtered.filter(category => (category.products_count || 0) > 0)
    } else if (statusFilter.value === 'active') {
      filtered = filtered.filter(category => category.active)
    } else if (statusFilter.value === 'inactive') {
      filtered = filtered.filter(category => !category.active)
    }
  }

  return filtered
})

const totalItems = computed(() => filteredCategories.value.length)
const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage.value))

const paginatedCategories = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredCategories.value.slice(start, end)
})

// Estadísticas
const totalCategories = computed(() => categories.value.length)
const totalProducts = computed(() => categories.value.reduce((sum, cat) => sum + (cat.products_count || 0), 0))
const mostPopularCategory = computed(() => {
  return categories.value.reduce((prev, current) => 
    (prev.products_count || 0) > (current.products_count || 0) ? prev : current
  , {})
})
const categoriesWithProducts = computed(() => 
  categories.value.filter(cat => (cat.products_count || 0) > 0).length
)

// Métodos
const loadCategories = async () => {
  loading.value = true
  try {
    const response = await categoriesService.getAll()
    categories.value = response.data || []
  } catch (error) {
    console.error('Error cargando categorías:', error)
    showToast('Error al cargar categorías', 'error')
    categories.value = []
  } finally {
    loading.value = false
  }
}

const viewCategoryProducts = async (category) => {
  selectedCategory.value = category
  showProductsModal.value = true
  loadingProducts.value = true
  
  try {
    // Usar productos del appStore (ya tiene todos los productos cargados)
    // Si no hay productos en el store, cargarlos
    if (!appStore.products || appStore.products.length === 0) {
      await appStore.loadProducts({ force: true })
    }
    
    const allProducts = appStore.products || []
    
    // Convertir IDs a número para comparación consistente
    const categoryId = parseInt(category.id)
    
    categoryProducts.value = allProducts
      .filter(product => parseInt(product.category_id) === categoryId)
      .map(product => ({
        ...product,
        price: parseFloat(product.sale_price || product.price || 0),
        stock: parseInt(product.current_stock || product.stock || 0),
        active: product.active !== false
      }))
  } catch (error) {
    console.error('Error cargando productos:', error)
    showToast('Error al cargar productos', 'error')
    categoryProducts.value = []
  } finally {
    loadingProducts.value = false
  }
}

const editCategory = (category) => {
  selectedCategory.value = category
  categoryForm.value = {
    name: category.name,
    description: category.description || '',
    icon: category.icon || 'shopping-bag',
    color: category.color || '#3b82f6',
    active: category.active
  }
  showEditCategoryModal.value = true
}

const saveCategory = async () => {
  if (!categoryForm.value.name.trim()) {
    showToast('El nombre es requerido', 'warning')
    return
  }

  try {
    if (showAddCategoryModal.value) {
      await categoriesService.create(categoryForm.value)
      showToast('Categoría creada exitosamente', 'success')
    } else {
      await categoriesService.update(selectedCategory.value.id, categoryForm.value)
      showToast('Categoría actualizada exitosamente', 'success')
    }
    
    closeModals()
    await loadCategories()
  } catch (error) {
    console.error('Error guardando categoría:', error)
    showToast('Error al guardar categoría', 'error')
  }
}

const deleteCategory = async () => {
  if (!confirm('¿Está seguro de eliminar esta categoría?')) return

  try {
    await categoriesService.delete(selectedCategory.value.id)
    showToast('Categoría eliminada exitosamente', 'success')
    closeModals()
    await loadCategories()
  } catch (error) {
    console.error('Error eliminando categoría:', error)
    showToast('Error al eliminar categoría', 'error')
  }
}

// Estado para confirmación de cambio de estado
const showStatusConfirmModal = ref(false)
const pendingStatusChange = ref(null)

const toggleCategoryStatus = async (category) => {
  const newStatus = !category.active
  
  // Mostrar modal de confirmación
  pendingStatusChange.value = {
    category,
    newStatus
  }
  showStatusConfirmModal.value = true
}

const confirmStatusChange = async () => {
  if (!pendingStatusChange.value) return
  
  try {
    showStatusConfirmModal.value = false
    const { category, newStatus } = pendingStatusChange.value
    
    // Enviar todos los campos de la categoría, no solo active
    await categoriesService.update(category.id, {
      name: category.name,
      description: category.description || '',
      icon: category.icon || 'shopping-bag',
      color: category.color || '#3b82f6',
      active: newStatus
    })
    showToast(`Categoría ${newStatus ? 'activada' : 'desactivada'} exitosamente`, 'success')
    await loadCategories()
  } catch (error) {
    console.error('Error cambiando estado:', error)
    showToast('Error al cambiar estado', 'error')
  } finally {
    pendingStatusChange.value = null
  }
}

const closeModals = () => {
  showAddCategoryModal.value = false
  showEditCategoryModal.value = false
  showProductsModal.value = false
  selectedCategory.value = null
  categoryForm.value = { 
    name: '', 
    description: '', 
    icon: 'shopping-bag',
    color: '#3b82f6',
    active: true 
  }
}

const setViewMode = (mode) => {
  viewMode.value = mode
  // Guardar preferencia en localStorage
  localStorage.setItem('categoriesViewMode', mode)
}

const clearFilters = () => {
  searchTerm.value = ''
  statusFilter.value = 'all'
  currentPage.value = 1
}

const exportCategories = () => {
  // Función para escapar valores CSV
  const escapeCSV = (value) => {
    if (value === null || value === undefined) return ''
    const str = String(value)
    // Si contiene coma, comillas o saltos de línea, envolver en comillas
    if (str.includes(',') || str.includes('"') || str.includes('\n')) {
      return `"${str.replace(/"/g, '""')}"`
    }
    return str
  }

  // Headers
  const headers = ['ID', 'Nombre', 'Descripción', 'Icono', 'Color', 'Productos', 'Estado', 'Fecha Creación']
  
  // Datos
  const rows = filteredCategories.value.map(cat => [
    cat.id,
    escapeCSV(cat.name),
    escapeCSV(cat.description || ''),
    escapeCSV(cat.icon || ''),
    escapeCSV(cat.color || ''),
    cat.products_count || 0,
    cat.active ? 'Activa' : 'Inactiva',
    formatDate(cat.created_at)
  ])
  
  // Construir CSV
  const csvContent = [
    headers.join(','),
    ...rows.map(row => row.join(','))
  ].join('\n')
  
  // Agregar BOM para UTF-8 (para que Excel lo abra correctamente)
  const BOM = '\uFEFF'
  const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `categorias_${new Date().toISOString().slice(0, 10)}.csv`
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  window.URL.revokeObjectURL(url)

  showToast('Categorías exportadas exitosamente', 'success')
}

const goToProductEdit = (product) => {
  sessionStorage.setItem('editProductData', JSON.stringify({
    id: product.id,
    name: product.name,
    sku: product.sku || '',
    barcode: product.barcode || '',
    description: product.description || '',
    price: product.sale_price || product.price || 0,
    cost: product.cost_price || product.cost || 0,
    stock: product.current_stock || product.stock || 0,
    min_stock: product.min_stock || 5,
    max_stock: product.max_stock || 100,
    category_id: product.category_id,
    image: product.image_url || product.image || '',
    active: product.active !== false
  }))
  
  showProductsModal.value = false
  emit('navigate', 'products')
}

// Utilidades
const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO').format(value)
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  try {
    const date = new Date(dateString)
    if (isNaN(date.getTime())) return '-'
    
    const day = date.getDate()
    const month = date.getMonth() + 1
    const year = date.getFullYear()
    return `${day.toString().padStart(2, '0')}/${month.toString().padStart(2, '0')}/${year}`
  } catch (error) {
    return '-'
  }
}

// Helper para obtener el SVG del icono
const getIconSvg = (iconId) => {
  const iconMap = {
    'shopping-bag': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
    'gift': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>',
    'package': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
    'money': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>',
    'food': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>',
    'drink': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
    'coffee': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
    'perfume': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>',
    'cosmetics': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>',
    'book': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
    'pencil': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>',
    'tshirt': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
    'shoe': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>',
    'watch': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'pill': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    'toy': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-10 5a9 9 0 1118 0 9 9 0 01-18 0z"/>',
    'baby': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
  }
  
  return iconMap[iconId] || iconMap['shopping-bag'] // Default: shopping bag
}


// Watchers
watch([searchTerm, statusFilter], () => {
  currentPage.value = 1
})

// 🧠 CONCIENCIA DE PANTALLA PARA IA
const updateScreenContextForAI = () => {
  const uiContext = useUIContextStore()
  
  // Formatear moneda para la IA
  const formatCurrencyForAI = (value) => {
    return new Intl.NumberFormat('es-CO').format(value)
  }
  
  // Lista de categorías visible (máximo 10 para no sobrecargar)
  const categoriasVisibles = filteredCategories.value.slice(0, 10).map(cat => ({
    id: cat.id,
    nombre: cat.name,
    productos: cat.products_count || 0,
    ingresos: formatCurrencyForAI(cat.revenue || 0),
    estado: cat.active ? 'activa' : 'inactiva',
    fecha: formatDate(cat.created_at)
  }))
  
  // Datos del contexto
  const contextData = {
    resumenCategorias: {
      total: totalCategories.value,
      productosTotal: totalProducts.value,
      masPopular: mostPopularCategory.value?.name || 'N/A',
      conProductos: categoriesWithProducts.value
    },
    filtrosActivos: {
      busqueda: searchTerm.value || null,
      estado: statusFilter.value || 'all'
    },
    vistaActual: viewMode.value, // 'table' o 'grid'
    categoriasVisibles: categoriasVisibles,
    cantidadFiltrada: filteredCategories.value.length,
    categoriaSeleccionada: selectedCategory.value ? {
      id: selectedCategory.value.id,
      nombre: selectedCategory.value.name,
      productos: selectedCategory.value.products_count,
      activa: selectedCategory.value.active
    } : null,
    modalAbierto: showAddCategoryModal.value ? 'crear' : (showEditCategoryModal.value ? 'editar' : null),
    instrucciones: {
      buscar: 'Puedo buscar categorías por nombre. Solo dime qué buscar.',
      crear: 'Puedo ayudarte a crear una categoría. Dime el nombre de la nueva categoría.',
      editar: selectedCategory.value 
        ? `Puedo editar "${selectedCategory.value.name}". Dime qué cambiar.` 
        : 'Selecciona una categoría o dime cuál quieres editar.',
      ver: 'Puedo mostrarte los productos de cualquier categoría.'
    }
  }
  
  // Registrar acciones disponibles
  uiContext.registerAction('buscarCategoria', (params) => {
    const texto = params?.texto || ''
    searchTerm.value = texto
    return { 
      success: true, 
      message: `Buscando "${texto}"...`,
      resultados: filteredCategories.value.length
    }
  })
  
  uiContext.registerAction('limpiarBusquedaCategorias', () => {
    searchTerm.value = ''
    statusFilter.value = 'all'
    return { success: true, message: 'Filtros limpiados' }
  })
  
  uiContext.registerAction('filtrarCategorias', (params) => {
    const filtro = params?.filtro || 'all'
    statusFilter.value = filtro
    return { 
      success: true, 
      message: `Filtrando por: ${filtro}`,
      resultados: filteredCategories.value.length
    }
  })
  
  uiContext.registerAction('abrirCrearCategoria', () => {
    showAddCategoryModal.value = true
    return { success: true, message: 'Modal de crear categoría abierto. Escribe el nombre de la categoría.' }
  })
  
  uiContext.registerAction('verProductosCategoria', async (params) => {
    const nombreCategoria = params?.nombre
    if (!nombreCategoria) {
      return { success: false, message: 'Dime el nombre de la categoría' }
    }
    
    // Buscar categoría por nombre
    const categoria = categories.value.find(c => 
      c.name.toLowerCase().includes(nombreCategoria.toLowerCase())
    )
    
    if (!categoria) {
      return { success: false, message: `No encontré la categoría "${nombreCategoria}"` }
    }
    
    await viewCategoryProducts(categoria)
    return { 
      success: true, 
      message: `Mostrando ${categoria.products_count || 0} productos de "${categoria.name}"` 
    }
  })
  
  uiContext.registerAction('editarCategoria', async (params) => {
    const nombreCategoria = params?.nombre
    if (!nombreCategoria) {
      return { success: false, message: 'Dime el nombre de la categoría a editar' }
    }
    
    const categoria = categories.value.find(c => 
      c.name.toLowerCase().includes(nombreCategoria.toLowerCase())
    )
    
    if (!categoria) {
      return { success: false, message: `No encontré la categoría "${nombreCategoria}"` }
    }
    
    editCategory(categoria)
    return { success: true, message: `Abriendo editor para "${categoria.name}"` }
  })
  
  // Actualizar el store de contexto
  uiContext.setScreenData(contextData)
}

// Watcher para actualizar contexto cuando cambian las categorías o filtros
watch([categories, searchTerm, statusFilter, selectedCategory, showAddCategoryModal, showEditCategoryModal], () => {
  updateScreenContextForAI()
}, { deep: true })

// Lifecycle
onMounted(() => {
  loadCategories()
  // Inicializar contexto para IA después de cargar
  setTimeout(() => {
    updateScreenContextForAI()
  }, 500)
})
</script>

<style scoped>
.line-clamp-1 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  line-clamp: 1;
}

.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  line-clamp: 2;
}
</style>