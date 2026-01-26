<template>
  <div class="min-h-screen font-sans bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="text-center">
          <svg class="animate-spin w-12 h-12 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="text-sm text-gray-600">Cargando categorías...</p>
        </div>
      </div>

      <template v-else>
      
      <!-- Header Simple y Elegante (Sin icono) -->
      <div class="flex items-center justify-between pb-4">
        <!-- Título sin icono -->
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-white tracking-tight">Categorías</h1>
          <p class="text-sm text-gray-500 dark:text-zinc-500 mt-1 font-normal">Organiza y clasifica tus productos</p>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Neutro (Exportar) -->
          <button @click="exportCategories"
                  class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar</span>
          </button>
          
          <!-- Botón Principal (Nueva Categoría) -->
          <button @click="showAddCategoryModal = true"
                  class="px-6 py-2.5 bg-gray-900 dark:bg-zinc-700 hover:bg-black dark:hover:bg-zinc-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-gray-900/20 dark:shadow-black/50 transition-all duration-300 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Nueva Categoría</span>
          </button>
        </div>
      </div>

      <!-- Métricas Principales - Estilo Fantasma Elegante -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <!-- Total Categorías -->
        <div class="bg-white/80 dark:bg-zinc-800/40  rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Total Categorías</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">{{ totalCategories }}</p>
            </div>
          </div>
        </div>

        <!-- Productos Total -->
        <div class="bg-white/80 dark:bg-zinc-800/40  rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Productos Total</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">{{ totalProducts }}</p>
            </div>
          </div>
        </div>

        <!-- Más Popular -->
        <div class="bg-white/80 dark:bg-zinc-800/40  rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Más Popular</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5 truncate">{{ mostPopularCategory?.name || 'N/A' }}</p>
            </div>
          </div>
        </div>

        <!-- Con Productos -->
        <div class="bg-white/80 dark:bg-zinc-800/40  rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-50 dark:bg-purple-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Con Productos</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">{{ categoriesWithProducts }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenedor Unificado: Búsqueda + Tabla -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.4)] border border-gray-200/60 dark:border-zinc-800 overflow-hidden">
        <!-- Filtros/Búsqueda -->
        <div class="p-4 bg-gray-50/50 dark:bg-zinc-900/50">
          <div class="flex flex-wrap items-center gap-3">
          <!-- Búsqueda -->
          <div class="flex-1 min-w-48 relative">
            <svg class="absolute left-3.5 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Buscar categorías..."
              class="w-full pl-11 pr-4 py-3 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40 dark:focus:ring-blue-400/40 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-300">
          </div>
          
          <!-- Estado -->
          <select
            v-model="statusFilter"
            class="px-4 py-3 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/40 dark:focus:ring-blue-400/40 transition-all duration-300 min-w-36 cursor-pointer">
            <option value="all">Todas</option>
            <option value="withProducts">Con productos</option>
            <option value="active">Activas</option>
            <option value="inactive">Inactivas</option>
          </select>
          
          <!-- Toggle Vista -->
          <div class="flex items-center bg-white dark:bg-zinc-800 rounded-xl p-1 border border-gray-200 dark:border-zinc-700 h-[46px]">
            <button
              @click="setViewMode('grid')"
              :class="[
                'flex items-center justify-center px-4 h-full rounded-lg transition-all duration-200 text-xs font-semibold gap-2',
                viewMode === 'grid' 
                  ? 'bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-white' 
                  : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-700/50'
              ]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
              Tarjetas
            </button>
            
            <button
              @click="setViewMode('table')"
              :class="[
                'flex items-center justify-center px-4 h-full rounded-lg transition-all duration-200 text-xs font-semibold gap-2',
                viewMode === 'table' 
                  ? 'bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-white' 
                  : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-700/50'
              ]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
              </svg>
              Tabla
            </button>
          </div>
          
          <!-- Limpiar Filtros -->
          <button
            @click="clearFilters"
            class="p-3 text-gray-400 dark:text-zinc-500 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all duration-200"
            title="Limpiar filtros">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
          </div>
        </div>

      <!-- Vista de Tarjetas -->
      <div v-if="viewMode === 'grid'">
        <!-- Sin resultados -->
        <div v-if="paginatedCategories.length === 0" class="p-12 text-center">
          <div class="w-16 h-16 bg-gray-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
          <h3 class="text-base font-semibold text-gray-700 dark:text-zinc-300 mb-1">No hay categorías</h3>
          <p class="text-sm text-gray-500 dark:text-zinc-500 mb-4">
            {{ filteredCategories.length === 0 ? 'No se encontraron categorías.' : 'No hay categorías en esta página.' }}
          </p>
          <button
            v-if="statusFilter !== 'all' || searchTerm"
            @click="clearFilters"
            class="px-4 py-2.5 bg-gray-900 dark:bg-zinc-700 text-white text-sm font-medium rounded-xl hover:bg-black dark:hover:bg-zinc-600 transition-all duration-200">
            Limpiar filtros
          </button>
        </div>
        
        <!-- Grid de categorías -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 p-5">
          <div
            v-for="category in paginatedCategories"
            :key="category.id"
            @click="viewCategoryProducts(category)"
            :class="[
              'bg-white/80 dark:bg-zinc-800/40  rounded-2xl border-0 hover:bg-white dark:hover:bg-zinc-800/60 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)] transition-all duration-300 overflow-hidden group flex flex-col cursor-pointer',
              !category.active && 'opacity-60'
            ]">
            
            <!-- Header -->
            <div class="p-4 pb-3">
              <div class="flex items-center space-x-3 mb-2">
                <!-- Icono de la categoría -->
                <div 
                  class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                  :style="{ backgroundColor: category.color || '#3b82f6' }">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="getIconSvg(category.icon)"></svg>
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="text-sm font-bold text-gray-800 dark:text-white truncate">{{ category.name }}</h3>
                  <span
                    v-if="!category.active"
                    class="inline-block px-2 py-0.5 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[10px] font-semibold rounded-full mt-1 uppercase tracking-wide">
                    Inactiva
                  </span>
                </div>
              </div>
              <p class="text-xs text-gray-500 dark:text-zinc-500 line-clamp-2">{{ category.description }}</p>
            </div>
            
            <!-- Contenido -->
            <div class="px-4 pb-4 flex-1 flex flex-col">
              <!-- Estadísticas -->
              <div class="grid grid-cols-2 gap-2 mb-4 flex-1">
                <div class="text-center bg-gray-50/80 dark:bg-zinc-800/50 rounded-xl p-3">
                  <div class="text-xl font-bold text-gray-800 dark:text-white">{{ category.products_count || 0 }}</div>
                  <div class="text-[10px] text-gray-500 dark:text-zinc-500 font-medium uppercase tracking-wider">Productos</div>
                </div>
                <div class="text-center bg-emerald-50/80 dark:bg-emerald-500/10 rounded-xl p-3">
                  <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">${{ formatCurrency(category.revenue || 0) }}</div>
                  <div class="text-[10px] text-gray-500 dark:text-zinc-500 font-medium uppercase tracking-wider">Ventas</div>
                </div>
              </div>

              <!-- Estado y fecha -->
              <div class="flex justify-between items-center text-xs text-gray-500 dark:text-zinc-500 mb-3 pt-3 border-t border-gray-100 dark:border-zinc-700/50">
                <span class="font-medium">{{ formatDate(category.created_at) }}</span>
                <span :class="category.active ? 'text-emerald-500 font-semibold' : 'text-rose-500 font-semibold'">
                  {{ category.active ? 'Activa' : 'Inactiva' }}
                </span>
              </div>

              <!-- Acciones (Ghost Style) -->
              <div class="grid grid-cols-3 gap-2">
                <button
                  @click.stop="viewCategoryProducts(category)"
                  class="flex items-center justify-center py-2.5 rounded-xl text-gray-400 dark:text-zinc-500 hover:text-blue-500 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-all duration-200"
                  title="Ver Productos">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
                
                <button
                  @click.stop="editCategory(category)"
                  class="flex items-center justify-center py-2.5 rounded-xl text-gray-400 dark:text-zinc-500 hover:text-amber-500 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-all duration-200"
                  title="Editar">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                
                <button
                  @click.stop="toggleCategoryStatus(category)"
                  class="flex items-center justify-center py-2.5 rounded-xl transition-all duration-200"
                  :class="category.active 
                    ? 'text-gray-400 dark:text-zinc-500 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10' 
                    : 'text-gray-400 dark:text-zinc-500 hover:text-emerald-500 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10'"
                  :title="category.active ? 'Desactivar' : 'Activar'">
                  <svg v-if="category.active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-10 5a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista de Tabla -->
      <div v-else>
        <!-- Header de tabla -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-zinc-800/50">
          <div>
            <h2 class="text-base font-bold text-gray-800 dark:text-white">Lista de Categorías</h2>
            <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-0.5 font-medium">{{ totalCategories }} categorías en total</p>
          </div>
        </div>
      
      <!-- Tabla -->
      <table class="min-w-full">
        <thead>
          <tr class="border-b border-gray-100 dark:border-zinc-800/50">
            <th class="px-6 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">
              Categoría
            </th>
            <th class="px-6 py-3.5 text-center text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">
              Productos
            </th>
            <th class="px-6 py-3.5 text-right text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">
              Ventas
            </th>
            <th class="px-6 py-3.5 text-center text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">
              Estado
            </th>
            <th class="px-6 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">
              Fecha
            </th>
            <th class="px-6 py-3.5 text-center text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="category in paginatedCategories"
            :key="category.id"
            @click="viewCategoryProducts(category)"
            :class="['hover:bg-gray-50/80 dark:hover:bg-zinc-800/30 transition-colors duration-200 cursor-pointer border-b border-gray-50 dark:border-zinc-800/30 last:border-0', !category.active && 'opacity-60']">
            <td class="px-6 py-4">
              <div class="flex items-center space-x-3">
                <!-- Icono de la categoría -->
                <div 
                  class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                  :style="{ backgroundColor: category.color || '#3b82f6' }">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="getIconSvg(category.icon)"></svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-gray-800 dark:text-white">{{ category.name }}</div>
                  <div class="text-xs text-gray-400 dark:text-zinc-500 line-clamp-1">{{ category.description }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <span class="text-sm font-bold text-gray-800 dark:text-white">{{ category.products_count || 0 }}</span>
            </td>
            <td class="px-6 py-4 text-right">
              <span class="text-sm font-bold text-gray-800 dark:text-white">${{ formatCurrency(category.revenue || 0) }}</span>
            </td>
            <td class="px-6 py-4 text-center">
              <span
                :class="[
                  'px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wide',
                  category.active 
                    ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' 
                    : 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400'
                ]">
                {{ category.active ? 'Activa' : 'Inactiva' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <span class="text-sm text-gray-500 dark:text-zinc-500">{{ formatDate(category.created_at) }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-1">
                <button
                  @click.stop="viewCategoryProducts(category)"
                  class="p-2 text-gray-400 dark:text-zinc-500 hover:text-blue-500 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-all duration-200"
                  title="Ver productos">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
                <button
                  @click.stop="editCategory(category)"
                  class="p-2 text-gray-400 dark:text-zinc-500 hover:text-amber-500 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-lg transition-all duration-200"
                  title="Editar">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button
                  @click.stop="toggleCategoryStatus(category)"
                  class="p-2 rounded-lg transition-all duration-200"
                  :class="category.active 
                    ? 'text-gray-400 dark:text-zinc-500 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10' 
                    : 'text-gray-400 dark:text-zinc-500 hover:text-emerald-500 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10'"
                  :title="category.active ? 'Desactivar' : 'Activar'">
                  <svg v-if="category.active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-10 5a9 9 0 1118 0 9 9 0 01-18 0z"></path>
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

    <!-- Modal de Confirmación para Cambio de Estado -->
    <div v-if="showStatusConfirmModal" 
         class="fixed inset-0 bg-black/40 dark:bg-black/60  flex items-center justify-center p-4 z-50"
         @click.self="showStatusConfirmModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-md shadow-[0_25px_50px_-12px_rgba(0,0,0,0.25)] dark:shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] overflow-hidden animate-fade-in border border-gray-200/50 dark:border-zinc-800">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-500/10 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-800 dark:text-white">Confirmar Cambio</h3>
              <p class="text-sm text-gray-500 dark:text-zinc-500">Esta acción modificará el estado</p>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="p-6">
          <p class="text-gray-600 dark:text-zinc-400 mb-4">
            ¿Estás seguro que deseas <span class="font-semibold text-gray-800 dark:text-white">{{ pendingStatusChange?.newStatus ? 'activar' : 'desactivar' }}</span> la categoría:
          </p>
          <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 mb-5">
            <p class="font-bold text-gray-800 dark:text-white">{{ pendingStatusChange?.category?.name }}</p>
            <p class="text-sm text-gray-500 dark:text-zinc-500 mt-1">{{ pendingStatusChange?.category?.products_count || 0 }} productos</p>
          </div>
          <p class="text-sm text-gray-500 dark:text-zinc-500">
            {{ pendingStatusChange?.newStatus 
              ? 'La categoría estará disponible en el POS. Los productos que fueron desactivados con la categoría se reactivarán automáticamente.' 
              : 'La categoría NO estará disponible en el POS. Todos los productos activos de esta categoría se desactivarán automáticamente.' }}
          </p>
          <div v-if="!pendingStatusChange?.newStatus" class="mt-4 bg-amber-50 dark:bg-amber-500/10 rounded-xl p-3">
            <p class="text-xs text-amber-700 dark:text-amber-400">
              <strong>Nota:</strong> Los productos que ya estaban inactivos permanecerán inactivos. Solo se desactivarán los productos que están actualmente activos.
            </p>
          </div>
          <div v-else class="mt-4 bg-blue-50 dark:bg-blue-500/10 rounded-xl p-3">
            <p class="text-xs text-blue-700 dark:text-blue-400">
              <strong>Reactivación inteligente:</strong> Solo se reactivarán los productos que fueron desactivados cuando se desactivó esta categoría.
            </p>
          </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 bg-gray-50/50 dark:bg-zinc-800/30 flex items-center justify-end space-x-3">
          <button @click="showStatusConfirmModal = false" 
                  class="px-4 py-2.5 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 rounded-xl hover:bg-gray-50 dark:hover:bg-zinc-700 transition-all duration-200 font-medium text-sm">
            Cancelar
          </button>
          <button @click="confirmStatusChange" 
                  class="px-5 py-2.5 bg-gray-900 dark:bg-zinc-700 hover:bg-black dark:hover:bg-zinc-600 text-white rounded-xl transition-all duration-200 font-medium text-sm">
            Confirmar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Agregar/Editar Categoría -->
    <div
      v-if="showAddCategoryModal || showEditCategoryModal"
      class="fixed inset-0 bg-black/40 dark:bg-black/60  flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.25)] dark:shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] max-w-md w-full border border-gray-200/50 dark:border-zinc-800">
        <!-- Header -->
        <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-blue-50 dark:bg-blue-500/10 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-base font-bold text-gray-800 dark:text-white">
                  {{ showAddCategoryModal ? 'Nueva Categoría' : 'Editar Categoría' }}
                </h2>
                <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-0.5">
                  {{ showAddCategoryModal ? 'Crear nueva categoría' : 'Modificar categoría existente' }}
                </p>
              </div>
            </div>
            <button
              @click="closeModals"
              class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition-all duration-200">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Contenido -->
        <div class="p-5 space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 mb-1.5">Nombre</label>
            <input
              v-model="categoryForm.name"
              type="text"
              placeholder="Nombre de la categoría"
              class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-800 dark:text-zinc-200 rounded-xl focus:ring-2 focus:ring-blue-500/40 dark:focus:ring-blue-400/40 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200 placeholder-gray-400 dark:placeholder-zinc-500">
          </div>
          
          <div>
            <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 mb-1.5">Descripción</label>
            <textarea
              v-model="categoryForm.description"
              placeholder="Descripción de la categoría"
              rows="3"
              class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-800 dark:text-zinc-200 rounded-xl focus:ring-2 focus:ring-blue-500/40 dark:focus:ring-blue-400/40 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200 resize-none placeholder-gray-400 dark:placeholder-zinc-500"></textarea>
          </div>

          <!-- Selector de Icono -->
          <div>
            <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 mb-2">Icono</label>
            <div class="grid grid-cols-6 gap-2 max-h-48 overflow-y-auto p-3 border border-gray-200 dark:border-zinc-700 rounded-xl bg-gray-50 dark:bg-zinc-800/50">
              <button
                v-for="icon in availableIcons"
                :key="icon.id"
                type="button"
                @click="categoryForm.icon = icon.id"
                :class="[
                  'p-2 rounded-xl border-2 transition-all duration-200 hover:scale-105',
                  categoryForm.icon === icon.id 
                    ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-500/10' 
                    : 'border-transparent bg-white dark:bg-zinc-800 hover:border-gray-300 dark:hover:border-zinc-600'
                ]"
                :title="icon.name">
                <span class="text-2xl">{{ icon.emoji }}</span>
              </button>
            </div>
            <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-1.5">
              Seleccionado: {{ availableIcons.find(i => i.id === categoryForm.icon)?.name || 'Ninguno' }}
            </p>
          </div>

          <!-- Selector de Color -->
          <div>
            <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 mb-1.5">Color</label>
            <div class="flex items-center space-x-2">
              <input
                v-model="categoryForm.color"
                type="color"
                class="w-12 h-10 rounded-xl border border-gray-200 dark:border-zinc-700 cursor-pointer bg-white dark:bg-zinc-800">
              <input
                v-model="categoryForm.color"
                type="text"
                placeholder="#3b82f6"
                class="flex-1 px-3.5 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-800 dark:text-zinc-200 rounded-xl focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition-all duration-200">
            </div>
          </div>
          
          <div class="flex items-center space-x-3 pt-2">
            <input
              v-model="categoryForm.active"
              type="checkbox"
              id="active"
              class="w-4 h-4 text-blue-500 border-gray-300 dark:border-zinc-600 rounded focus:ring-blue-500 bg-white dark:bg-zinc-800">
            <label for="active" class="text-sm text-gray-600 dark:text-zinc-400">Categoría activa</label>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-5 py-4 bg-gray-50/50 dark:bg-zinc-800/30 border-t border-gray-100 dark:border-zinc-800 flex justify-between">
          <div>
            <button
              v-if="showEditCategoryModal"
              @click="deleteCategory"
              class="px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-rose-50 dark:hover:bg-rose-500/10 text-rose-500 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 rounded-xl text-sm font-medium transition-all duration-200">
              Eliminar
            </button>
          </div>
          <div class="flex gap-2">
            <button
              @click="closeModals"
              class="px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm font-medium transition-all duration-200">
              Cancelar
            </button>
            <button
              @click="saveCategory"
              class="px-5 py-2.5 bg-gray-900 dark:bg-zinc-700 hover:bg-black dark:hover:bg-zinc-600 text-white rounded-xl text-sm font-medium transition-all duration-200">
              {{ showAddCategoryModal ? 'Crear' : 'Guardar' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ver Productos de Categoría -->
    <Teleport to="body">
      <div
        v-if="showProductsModal"
        @click.self="showProductsModal = false"
        class="fixed inset-0 bg-black/50 dark:bg-black/70  flex items-center justify-center z-[9999] p-4"
        style="pointer-events: auto;">
        <div 
          @click.stop
          class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.25)] dark:shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] max-w-6xl w-full max-h-[95vh] overflow-hidden border border-gray-200/50 dark:border-zinc-800"
          style="pointer-events: auto;">
          <!-- Header -->
          <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-base font-bold text-gray-800 dark:text-white">
                  Productos: {{ selectedCategory?.name }}
                </h2>
                <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-0.5">
                  {{ categoryProducts.length }} productos encontrados
                </p>
              </div>
            </div>
            <button
              @click="showProductsModal = false"
              class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition-all duration-200">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Contenido -->
        <div class="p-5 overflow-y-auto max-h-[calc(90vh-120px)]">
          <div v-if="loadingProducts" class="flex justify-center items-center py-12">
            <svg class="animate-spin w-8 h-8 text-blue-500 dark:text-blue-400" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>
          
          <div v-else-if="categoryProducts.length === 0" class="text-center py-12">
            <div class="w-14 h-14 bg-gray-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-7 h-7 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <p class="text-sm text-gray-600 dark:text-zinc-400">No hay productos en esta categoría</p>
          </div>
          
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="product in categoryProducts"
              :key="product.id"
              class="bg-white/80 dark:bg-zinc-800/40  rounded-2xl p-4 hover:bg-white dark:hover:bg-zinc-800/60 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)] transition-all duration-300">
              <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                  <h4 class="text-sm font-semibold text-gray-800 dark:text-white line-clamp-1">{{ product.name }}</h4>
                  <p class="text-[11px] text-gray-400 dark:text-zinc-500">SKU: {{ product.sku }}</p>
                </div>
                <span
                  :class="[
                    'px-2 py-0.5 rounded-full text-[10px] font-semibold',
                    product.active ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400'
                  ]">
                  {{ product.active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
              
              <div class="grid grid-cols-2 gap-2 mb-3">
                <div class="text-center bg-gray-50/80 dark:bg-zinc-800/50 rounded-xl p-2.5">
                  <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400">${{ formatCurrency(product.price) }}</div>
                  <div class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase">Precio</div>
                </div>
                <div class="text-center bg-gray-50/80 dark:bg-zinc-800/50 rounded-xl p-2.5">
                  <div class="text-sm font-bold text-gray-800 dark:text-white">{{ product.stock }}</div>
                  <div class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase">Stock</div>
                </div>
              </div>
              
              <button
                @click="goToProductEdit(product)"
                class="w-full px-3 py-2 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-xl text-xs font-medium transition-all duration-200">
                Editar Producto
              </button>
            </div>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="px-5 py-4 bg-gray-50/50 dark:bg-zinc-800/30 border-t border-gray-100 dark:border-zinc-800 flex justify-end">
          <button
            @click="showProductsModal = false"
            class="px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm font-medium transition-all duration-200">
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
  console.log('🔄 Iniciando carga de categorías...')
  loading.value = true
  try {
    console.log('📡 Llamando a categoriesService.getAll()...')
    const response = await categoriesService.getAll()
    console.log('✅ Respuesta recibida:', response)
    categories.value = response.data || []
    console.log(`✅ Categorías cargadas: ${categories.value.length}`, categories.value)
  } catch (error) {
    console.error('❌ Error cargando categorías:', error)
    showToast('Error al cargar categorías', 'error')
    categories.value = []
  } finally {
    loading.value = false
    console.log('✅ Carga de categorías finalizada. Loading:', loading.value)
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
    
    console.log(`Productos encontrados para ${category.name}:`, categoryProducts.value.length)
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
  console.log(`Navegando a gestión de productos para editar: ${product.name}`)
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

// Lifecycle
onMounted(() => {
  loadCategories()
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