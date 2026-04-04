<template>
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="text-center">
          <svg class="animate-spin w-10 h-10 text-blue-600 dark:text-blue-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="text-sm text-gray-500 dark:text-zinc-400">Cargando categorías...</p>
        </div>
      </div>

      <template v-else>
      
      <!-- Header -->
      <div class="flex items-center justify-between pb-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Categorías</h1>
          <p class="text-base text-gray-600 dark:text-zinc-400 mt-1">{{ totalCategories }} categorías &middot; {{ totalProducts }} productos asignados</p>
        </div>
        
        <div class="flex items-center gap-3">
          <button @click="exportCategories"
                  class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-base font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar</span>
          </button>
          
          <button @click="openCreateModal"
                  class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-base font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Nueva Categoría</span>
          </button>
        </div>
      </div>

      <!-- Tabla principal -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
        <!-- Barra de filtros -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800">
          <div class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-52 relative">
              <svg class="absolute left-3.5 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Buscar categorías..."
                class="w-full pl-11 pr-4 py-3 text-base rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200">
            </div>
            
            <select
              v-model="statusFilter"
              class="px-3 py-3 text-base rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 min-w-36 cursor-pointer">
              <option value="all">Todas</option>
              <option value="withProducts">Con productos</option>
              <option value="active">Activas</option>
              <option value="inactive">Inactivas</option>
            </select>
            
            <button
              v-if="searchTerm || statusFilter !== 'all'"
              @click="clearFilters"
              class="p-3 text-gray-500 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl border border-transparent hover:border-red-100 dark:hover:border-red-900/30 transition-all duration-200"
              title="Limpiar filtros">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>

            <!-- Contador inline -->
            <span class="text-sm text-gray-400 dark:text-zinc-500 ml-auto tabular-nums">{{ filteredCategories.length }} resultado{{ filteredCategories.length !== 1 ? 's' : '' }}</span>
          </div>
        </div>
      
        <!-- Tabla -->
        <table class="min-w-full">
          <thead>
            <tr class="border-b border-gray-200 dark:border-zinc-800">
              <th class="px-6 py-3.5 text-left text-sm font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider">
                Categoría
              </th>
              <th class="px-6 py-3.5 text-center text-sm font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider w-32">
                Productos
              </th>
              <th class="px-6 py-3.5 text-right text-sm font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider w-40">
                Ventas
              </th>
              <th class="px-6 py-3.5 text-center text-sm font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider w-32">
                Estado
              </th>
              <th class="px-6 py-3.5 text-left text-sm font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider w-36">
                Creación
              </th>
              <th class="px-6 py-3.5 text-right text-sm font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider w-36">
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/70">
            <tr
              v-for="category in paginatedCategories"
              :key="category.id"
              @click="viewCategoryProducts(category)"
              :class="[
                'group transition-all duration-150 cursor-pointer',
                category.active 
                  ? 'hover:bg-gray-50 dark:hover:bg-zinc-800/50' 
                  : 'opacity-50 hover:opacity-75 hover:bg-gray-50/50 dark:hover:bg-zinc-800/30'
              ]">
              <!-- Categoría: avatar + nombre + descripción -->
              <td class="px-6 py-5">
                <div class="flex items-center gap-3.5">
                  <div class="w-11 h-11 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <span class="text-base font-semibold text-gray-900 dark:text-white block leading-tight">{{ category.name }}</span>
                    <span v-if="category.description" class="text-sm text-gray-400 dark:text-zinc-500 mt-0.5 block line-clamp-1">{{ category.description }}</span>
                  </div>
                </div>
              </td>
              <!-- Productos -->
              <td class="px-6 py-5 text-center">
                <span 
                  :class="[
                    'text-base tabular-nums',
                    (category.products_count || 0) > 0 
                      ? 'font-semibold text-gray-900 dark:text-white' 
                      : 'font-normal text-gray-300 dark:text-zinc-600'
                  ]">
                  {{ category.products_count || 0 }}
                </span>
              </td>
              <!-- Ventas -->
              <td class="px-6 py-5 text-right">
                <span 
                  :class="[
                    'text-base tabular-nums',
                    (category.revenue || 0) > 0 
                      ? 'font-semibold text-emerald-600 dark:text-emerald-400' 
                      : 'font-normal text-gray-300 dark:text-zinc-600'
                  ]">
                  ${{ formatCurrency(category.revenue || 0) }}
                </span>
              </td>
              <!-- Estado -->
              <td class="px-6 py-5 text-center">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border',
                    category.active 
                      ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' 
                      : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
                  ]">
                  {{ category.active ? 'Activa' : 'Inactiva' }}
                </span>
              </td>
              <!-- Fecha -->
              <td class="px-6 py-5">
                <span class="text-base text-gray-500 dark:text-zinc-400 tabular-nums">{{ formatDate(category.created_at) }}</span>
              </td>
              <!-- Acciones -->
              <td class="px-6 py-5">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click.stop="editCategory(category)"
                    class="p-2.5 text-gray-400 dark:text-zinc-600 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all duration-200"
                    title="Editar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"></path>
                    </svg>
                  </button>
                  <button
                    @click.stop="toggleCategoryStatus(category)"
                    class="p-2.5 rounded-lg transition-all duration-200"
                    :class="category.active 
                      ? 'text-gray-400 dark:text-zinc-600 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20' 
                      : 'text-gray-400 dark:text-zinc-600 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20'"
                    :title="category.active ? 'Desactivar' : 'Activar'">
                    <svg v-if="category.active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            
            <!-- Sin resultados -->
            <tr v-if="paginatedCategories.length === 0">
              <td colspan="6" class="px-6 py-20 text-center">
                <div class="flex flex-col items-center space-y-4">
                  <div class="w-14 h-14 bg-gray-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-base font-semibold text-gray-700 dark:text-zinc-300">No hay categorías</p>
                    <p class="text-sm text-gray-500 dark:text-zinc-500 mt-1">No se encontraron categorías con los filtros actuales</p>
                  </div>
                  <button
                    v-if="statusFilter !== 'all' || searchTerm"
                    @click="clearFilters"
                    class="px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-lg transition-all duration-200">
                    Limpiar filtros
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        
        <!-- Paginador -->
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

      </template>

    <!-- Modal Confirmación de Cambio de Estado -->
    <div v-if="showStatusConfirmModal" 
         class="fixed inset-0 bg-black/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         @click.self="showStatusConfirmModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-md overflow-hidden animate-fade-in border border-gray-200 dark:border-zinc-800 shadow-2xl dark:shadow-black/50">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950 rounded-xl flex items-center justify-center border border-amber-100 dark:border-amber-800">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Confirmar Cambio</h3>
              <p class="text-xs text-gray-500 dark:text-zinc-400">Esta acción modificará el estado</p>
            </div>
          </div>
        </div>

        <div class="p-6">
          <p class="text-sm text-gray-500 dark:text-zinc-400 mb-4">
            ¿Estás seguro que deseas <span class="font-bold text-gray-900 dark:text-white">{{ pendingStatusChange?.newStatus ? 'activar' : 'desactivar' }}</span> la categoría:
          </p>
          <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-4 mb-5 border border-gray-100 dark:border-zinc-700">
            <p class="font-bold text-gray-900 dark:text-white">{{ pendingStatusChange?.category?.name }}</p>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">{{ pendingStatusChange?.category?.products_count || 0 }} productos</p>
          </div>
          <p class="text-xs text-gray-500 dark:text-zinc-400">
            {{ pendingStatusChange?.newStatus 
              ? 'La categoría estará disponible en el POS. Los productos que fueron desactivados con la categoría se reactivarán automáticamente.' 
              : 'La categoría NO estará disponible en el POS. Todos los productos activos de esta categoría se desactivarán automáticamente.' }}
          </p>
          <div v-if="!pendingStatusChange?.newStatus" class="mt-3 bg-amber-50 dark:bg-amber-950 rounded-lg p-3 border border-amber-100 dark:border-amber-800">
            <p class="text-xs text-amber-700 dark:text-amber-400">
              <strong>Nota:</strong> Los productos que ya estaban inactivos permanecerán inactivos. Solo se desactivarán los productos que están actualmente activos.
            </p>
          </div>
          <div v-else class="mt-3 bg-blue-50 dark:bg-blue-950 rounded-lg p-3 border border-blue-100 dark:border-blue-800">
            <p class="text-xs text-blue-700 dark:text-blue-400">
              <strong>Reactivación inteligente:</strong> Solo se reactivarán los productos que fueron desactivados cuando se desactivó esta categoría.
            </p>
          </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-900 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-end space-x-3">
          <button @click="showStatusConfirmModal = false" 
                  class="px-5 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 rounded-xl transition-all duration-200 font-bold text-sm border border-gray-200 dark:border-zinc-700">
            Cancelar
          </button>
          <button @click="confirmStatusChange" 
                  class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white rounded-xl transition-all duration-200 font-bold text-sm shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50">
            Confirmar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Crear/Editar Categoría -->
    <div
      v-if="showAddCategoryModal || showEditCategoryModal"
      class="fixed inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click.self="closeModals">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl max-w-md w-full border border-gray-200 dark:border-zinc-800 shadow-2xl dark:shadow-black/50">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900 dark:text-white">
                {{ showAddCategoryModal ? 'Nueva Categoría' : 'Editar Categoría' }}
              </h2>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                {{ showAddCategoryModal ? 'Crear nueva categoría de productos' : 'Modificar categoría existente' }}
              </p>
            </div>
            <button
              @click="closeModals"
              class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all duration-200">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Formulario -->
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Nombre <span class="text-rose-500">*</span></label>
            <input
              ref="nameInput"
              v-model="categoryForm.name"
              type="text"
              placeholder="Ej: Bebidas, Electrónica, Ropa..."
              class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
              @keyup.enter="saveCategory">
          </div>
          
          <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Descripción <span class="text-gray-400 dark:text-zinc-500 font-normal">(opcional)</span></label>
            <textarea
              v-model="categoryForm.description"
              placeholder="Descripción breve de la categoría"
              rows="2"
              class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"></textarea>
          </div>

          <div class="flex items-center justify-between pt-1">
            <div>
              <label class="text-xs font-medium text-gray-700 dark:text-zinc-300">Estado</label>
              <p class="text-[11px] text-gray-400 dark:text-zinc-500">{{ categoryForm.active ? 'Visible en el POS' : 'Oculta del POS' }}</p>
            </div>
            <button
              type="button"
              @click="categoryForm.active = !categoryForm.active"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900',
                categoryForm.active ? 'bg-blue-600' : 'bg-gray-200 dark:bg-zinc-700'
              ]">
              <span
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  categoryForm.active ? 'translate-x-5' : 'translate-x-0'
                ]" />
            </button>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 rounded-b-2xl flex justify-between">
          <div>
            <button
              v-if="showEditCategoryModal"
              @click="deleteCategory"
              class="px-4 py-2.5 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950 rounded-lg text-sm font-medium transition-all duration-200">
              Eliminar
            </button>
          </div>
          <div class="flex gap-3">
            <button
              @click="closeModals"
              class="px-5 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 rounded-xl text-sm font-bold transition-all duration-200 border border-gray-200 dark:border-zinc-700">
              Cancelar
            </button>
            <button
              @click="saveCategory"
              class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white rounded-xl text-sm font-bold transition-all duration-200 shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50">
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
        class="fixed inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm flex items-center justify-center z-[9999] p-4"
        style="pointer-events: auto;">
        <div 
          @click.stop
          class="bg-white dark:bg-zinc-900 rounded-2xl max-w-4xl w-full max-h-[85vh] overflow-hidden border border-gray-200 dark:border-zinc-800 shadow-2xl dark:shadow-black/50"
          style="pointer-events: auto;">
          <!-- Header -->
          <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-base font-bold text-gray-900 dark:text-white">
                  {{ selectedCategory?.name }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                  {{ categoryProducts.length }} productos en esta categoría
                </p>
              </div>
              <button
                @click="showProductsModal = false"
                class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Contenido -->
          <div class="overflow-y-auto max-h-[calc(85vh-120px)] bg-white dark:bg-zinc-900">
            <div v-if="loadingProducts" class="flex flex-col justify-center items-center py-16">
              <svg class="animate-spin w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="text-sm text-gray-500 dark:text-zinc-400 mt-3">Cargando productos...</p>
            </div>
            
            <div v-else-if="categoryProducts.length === 0" class="text-center py-16">
              <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800 rounded-xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <p class="text-sm font-medium text-gray-700 dark:text-zinc-300">Sin productos</p>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Esta categoría aún no tiene productos asignados</p>
            </div>
            
            <!-- Tabla de productos -->
            <table v-else class="min-w-full">
              <thead class="bg-gray-50 dark:bg-zinc-900 sticky top-0 border-b border-gray-200 dark:border-zinc-800">
                <tr>
                  <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-widest">
                    Producto
                  </th>
                  <th class="px-4 py-3 text-right text-[11px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-widest">
                    Precio
                  </th>
                  <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-widest">
                    Stock
                  </th>
                  <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-widest">
                    Estado
                  </th>
                  <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-widest">
                    Acción
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="product in categoryProducts"
                  :key="product.id"
                  class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-150 border-b border-gray-100 dark:border-zinc-800 last:border-0">
                  <td class="px-5 py-3">
                    <div class="min-w-0">
                      <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ product.name }}</p>
                      <p class="text-xs text-gray-500 dark:text-zinc-400 font-mono">{{ product.sku }}</p>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">${{ formatCurrency(product.price) }}</span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span class="text-sm font-medium text-gray-900 dark:text-white tabular-nums">{{ product.stock }}</span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span
                      :class="[
                        'px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border',
                        product.active 
                          ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' 
                          : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
                      ]">
                      {{ product.active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <button
                      @click="goToProductEdit(product)"
                      class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950 rounded-lg transition-all duration-200">
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
          
          <!-- Footer -->
          <div class="px-6 py-3 bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 flex justify-end">
            <button
              @click="showProductsModal = false"
              class="px-5 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 rounded-xl text-sm font-bold transition-all duration-200 border border-gray-200 dark:border-zinc-700">
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
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useToast } from '../composables/useToast.js'
import { categoriesService } from '../services/categoriesService.js'
import { productsService } from '../services/productsService.js'
import { appStore } from '../store/appStore.js'
import { useUIContextStore } from '../store/uiContextStore.js'
import TablePaginator from './TablePaginator.vue'

const emit = defineEmits(['navigate'])
const { showToast } = useToast()

// Refs
const nameInput = ref(null)

// Estados
const loading = ref(true)
const loadingProducts = ref(false)
const categories = ref([])
const categoryProducts = ref([])
const selectedCategory = ref(null)

// Filtros
const searchTerm = ref('')
const statusFilter = ref('all')

// Paginación
const currentPage = ref(1)
const itemsPerPage = ref(12)

// Modales
const showAddCategoryModal = ref(false)
const showEditCategoryModal = ref(false)
const showProductsModal = ref(false)

// Formulario
const categoryForm = ref({
  name: '',
  description: '',
  active: true
})

// Computed
const filteredCategories = computed(() => {
  let filtered = categories.value

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(category => 
      category.name.toLowerCase().includes(term) ||
      (category.description || '').toLowerCase().includes(term)
    )
  }

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

const openCreateModal = () => {
  categoryForm.value = { name: '', description: '', active: true }
  showAddCategoryModal.value = true
  nextTick(() => {
    nameInput.value?.focus()
  })
}

const viewCategoryProducts = async (category) => {
  selectedCategory.value = category
  showProductsModal.value = true
  loadingProducts.value = true
  
  try {
    if (!appStore.products || appStore.products.length === 0) {
      await appStore.loadProducts({ force: true })
    }
    
    const allProducts = appStore.products || []
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
    active: category.active
  }
  showEditCategoryModal.value = true
  nextTick(() => {
    nameInput.value?.focus()
  })
}

const saveCategory = async () => {
  if (!categoryForm.value.name.trim()) {
    showToast('El nombre es requerido', 'warning')
    return
  }

  try {
    const payload = {
      name: categoryForm.value.name,
      description: categoryForm.value.description,
      active: categoryForm.value.active
    }

    if (showAddCategoryModal.value) {
      await categoriesService.create(payload)
      showToast('Categoría creada exitosamente', 'success')
    } else {
      await categoriesService.update(selectedCategory.value.id, payload)
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

const showStatusConfirmModal = ref(false)
const pendingStatusChange = ref(null)

const toggleCategoryStatus = async (category) => {
  pendingStatusChange.value = {
    category,
    newStatus: !category.active
  }
  showStatusConfirmModal.value = true
}

const confirmStatusChange = async () => {
  if (!pendingStatusChange.value) return
  
  try {
    showStatusConfirmModal.value = false
    const { category, newStatus } = pendingStatusChange.value
    
    await categoriesService.update(category.id, {
      name: category.name,
      description: category.description || '',
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
  categoryForm.value = { name: '', description: '', active: true }
}

const clearFilters = () => {
  searchTerm.value = ''
  statusFilter.value = 'all'
  currentPage.value = 1
}

const exportCategories = () => {
  const escapeCSV = (value) => {
    if (value === null || value === undefined) return ''
    const str = String(value)
    if (str.includes(',') || str.includes('"') || str.includes('\n')) {
      return `"${str.replace(/"/g, '""')}"`
    }
    return str
  }

  const headers = ['ID', 'Nombre', 'Descripción', 'Productos', 'Estado', 'Fecha Creación']
  
  const rows = filteredCategories.value.map(cat => [
    cat.id,
    escapeCSV(cat.name),
    escapeCSV(cat.description || ''),
    cat.products_count || 0,
    cat.active ? 'Activa' : 'Inactiva',
    formatDate(cat.created_at)
  ])
  
  const csvContent = [
    headers.join(','),
    ...rows.map(row => row.join(','))
  ].join('\n')
  
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

// Watchers
watch([searchTerm, statusFilter], () => {
  currentPage.value = 1
})

// Conciencia de pantalla para IA
const updateScreenContextForAI = () => {
  const uiContext = useUIContextStore()
  
  const formatCurrencyForAI = (value) => {
    return new Intl.NumberFormat('es-CO').format(value)
  }
  
  const categoriasVisibles = filteredCategories.value.slice(0, 10).map(cat => ({
    id: cat.id,
    nombre: cat.name,
    productos: cat.products_count || 0,
    ingresos: formatCurrencyForAI(cat.revenue || 0),
    estado: cat.active ? 'activa' : 'inactiva',
    fecha: formatDate(cat.created_at)
  }))
  
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
    vistaActual: 'table',
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
    openCreateModal()
    return { success: true, message: 'Modal de crear categoría abierto. Escribe el nombre de la categoría.' }
  })
  
  uiContext.registerAction('verProductosCategoria', async (params) => {
    const nombreCategoria = params?.nombre
    if (!nombreCategoria) {
      return { success: false, message: 'Dime el nombre de la categoría' }
    }
    
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
  
  uiContext.setScreenData(contextData)
}

watch([categories, searchTerm, statusFilter, selectedCategory, showAddCategoryModal, showEditCategoryModal], () => {
  updateScreenContextForAI()
}, { deep: true })

onMounted(() => {
  loadCategories()
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
</style>
