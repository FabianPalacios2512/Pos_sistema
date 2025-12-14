<template>
  <!-- Master-Detail Layout para Proveedores -->
  <div class="grid grid-cols-12 gap-6">
    
    <!-- MASTER: Lista de Proveedores (Izquierda) -->
    <div class="col-span-12 lg:col-span-4">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden flex flex-col" style="height: calc(100vh - 200px);">
        
        <!-- Filtros / Búsqueda -->
        <div class="p-4 border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900/50 flex-shrink-0">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input v-model="searchQuery"
                   type="text"
                   placeholder="Buscar proveedor..."
                   class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent">
          </div>
          
          <!-- Filtros de estado -->
          <div class="flex items-center gap-2 mt-3">
            <button @click="filterActive = null"
                    :class="[
                      'px-3 py-1.5 text-xs font-bold rounded-lg transition-all',
                      filterActive === null
                        ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-md'
                        : 'bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 hover:bg-gray-200 dark:hover:bg-zinc-700'
                    ]">
              Todos
            </button>
            <button @click="filterActive = true"
                    :class="[
                      'px-3 py-1.5 text-xs font-bold rounded-lg transition-all',
                      filterActive === true
                        ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-md'
                        : 'bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 hover:bg-gray-200 dark:hover:bg-zinc-700'
                    ]">
              Activos
            </button>
            <button @click="filterActive = false"
                    :class="[
                      'px-3 py-1.5 text-xs font-bold rounded-lg transition-all',
                      filterActive === false
                        ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-md'
                        : 'bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 hover:bg-gray-200 dark:hover:bg-zinc-700'
                    ]">
              Inactivos
            </button>
          </div>
        </div>

        <!-- Lista de proveedores -->
        <div class="flex-1 overflow-y-auto">
          <!-- Loading -->
          <div v-if="loading" class="p-8 text-center">
            <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto"></div>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-4">Cargando proveedores...</p>
          </div>

          <!-- Empty -->
          <div v-else-if="filteredSuppliers.length === 0" class="p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-10 h-10 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">Sin proveedores</p>
            <p class="text-xs text-gray-500 dark:text-zinc-500">No hay proveedores que coincidan</p>
          </div>

          <!-- Supplier Cards -->
          <div v-else class="divide-y divide-gray-100 dark:divide-zinc-800">
            <div v-for="supplier in filteredSuppliers" :key="supplier.id"
                 @click="selectSupplier(supplier)"
                 :class="[
                   'p-4 cursor-pointer transition-all duration-200',
                   selectedSupplier?.id === supplier.id
                     ? 'bg-blue-50 dark:bg-blue-950/30 border-l-4 border-l-blue-600 dark:border-l-blue-500'
                     : 'hover:bg-gray-50 dark:hover:bg-zinc-800/50 border-l-4 border-l-transparent'
                 ]">
              
              <div class="flex items-start justify-between mb-2">
                <div class="flex-1 min-w-0 mr-3">
                  <p class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ supplier.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5 truncate" v-if="supplier.contact_person">
                    {{ supplier.contact_person }}
                  </p>
                </div>
                <span :class="[
                        'px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide flex-shrink-0 border',
                        supplier.active 
                          ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' 
                          : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
                      ]">
                  {{ supplier.active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>

              <div class="flex items-center justify-between text-xs mt-2">
                <div class="flex items-center gap-3 text-gray-500 dark:text-zinc-500">
                  <span v-if="supplier.phone" class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    {{ supplier.phone }}
                  </span>
                  <span class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    {{ supplier.products_count || 0 }} prod.
                  </span>
                </div>
                <span class="font-mono font-bold text-gray-900 dark:text-white">
                  ${{ formatNumber(supplier.total_purchases_amount || 0) }}
                </span>
              </div>

              <div v-if="supplier.current_debt > 0" class="mt-2 pt-2 border-t border-gray-100 dark:border-zinc-800">
                <div class="flex items-center justify-between">
                  <span class="text-xs text-gray-600 dark:text-zinc-400">Deuda:</span>
                  <span class="text-xs font-bold text-red-600 dark:text-red-400">
                    ${{ formatNumber(supplier.current_debt) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- DETAIL: Detalles del Proveedor (Derecha) -->
    <div class="col-span-12 lg:col-span-8">
      <!-- Estado vacío -->
      <div v-if="!selectedSupplier" class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-12 text-center" style="height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center;">
        <div>
          <div class="w-24 h-24 bg-gray-100 dark:bg-zinc-800 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Selecciona un proveedor</h3>
          <p class="text-sm text-gray-600 dark:text-zinc-400">
            Haz clic en un proveedor de la lista para ver sus detalles completos,<br>historial de compras y gestionar la información
          </p>
        </div>
      </div>

      <!-- Detalles del proveedor seleccionado -->
      <div v-else-if="viewMode === 'create'" class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden" style="height: calc(100vh - 200px); display: flex; flex-direction: column;">
        
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 dark:border-zinc-800 bg-gradient-to-r from-blue-50 to-white dark:from-blue-950/20 dark:to-zinc-900 flex-shrink-0">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-bold text-gray-900 dark:text-white">Nuevo Proveedor</h2>
              <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Complete la información del proveedor</p>
            </div>
            <button @click="cancelCreate"
                    class="p-2.5 text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Formulario -->
        <div class="flex-1 overflow-y-auto p-6">
          <div class="space-y-6 max-w-2xl">
            
            <!-- Información básica -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-200 dark:border-zinc-700">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wide">Información Básica</h3>
              
              <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Nombre del Proveedor *</label>
                  <input v-model="newSupplier.name" type="text" required
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Documento/NIT *</label>
                  <input v-model="newSupplier.document" type="text" required
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Persona de Contacto</label>
                  <input v-model="newSupplier.contact_person" type="text"
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
              </div>
            </div>
            
            <!-- Información de contacto -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-200 dark:border-zinc-700">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wide">Contacto</h3>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Teléfono</label>
                  <input v-model="newSupplier.phone" type="text"
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Email</label>
                  <input v-model="newSupplier.email" type="email"
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="col-span-2">
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Dirección</label>
                  <input v-model="newSupplier.address" type="text"
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
              </div>
            </div>
            
            <!-- Términos comerciales -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-200 dark:border-zinc-700">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wide">Términos Comerciales</h3>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Plazo de Pago</label>
                  <select v-model="newSupplier.payment_terms"
                          class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="immediate">Inmediato</option>
                    <option value="15_days">15 días</option>
                    <option value="30_days">30 días</option>
                    <option value="45_days">45 días</option>
                    <option value="60_days">60 días</option>
                    <option value="90_days">90 días</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Límite de Crédito</label>
                  <input v-model.number="newSupplier.credit_limit" type="number" min="0"
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="col-span-2">
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Notas</label>
                  <textarea v-model="newSupplier.notes" rows="3"
                            class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
              </div>
            </div>
            
            <!-- Estado -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-200 dark:border-zinc-700">
              <label class="flex items-center gap-3 cursor-pointer">
                <input v-model="newSupplier.active" type="checkbox"
                       class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <div>
                  <div class="text-sm font-bold text-gray-900 dark:text-white">Proveedor Activo</div>
                  <div class="text-xs text-gray-600 dark:text-zinc-400">El proveedor estará disponible para órdenes de compra</div>
                </div>
              </label>
            </div>
            
          </div>
        </div>
        
        <!-- Footer con botones -->
        <div class="p-6 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900 flex-shrink-0">
          <div class="flex items-center justify-end gap-3">
            <button @click="cancelCreate"
                    class="px-6 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
              Cancelar
            </button>
            <button @click="saveNewSupplier"
                    :disabled="savingSupplier || !newSupplier.name || !newSupplier.document"
                    class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
              <svg v-if="savingSupplier" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              {{ savingSupplier ? 'Guardando...' : 'Guardar Proveedor' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Formulario de Edición COMPACTO -->
      <div v-else-if="viewMode === 'edit' && editingSupplier" class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden" style="height: calc(100vh - 200px); display: flex; flex-direction: column;">
        
        <!-- Header Compacto -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800 bg-gradient-to-r from-amber-50 to-white dark:from-amber-950/20 dark:to-zinc-900 flex items-center justify-between flex-shrink-0">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-amber-100 dark:bg-amber-950 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Editar Proveedor</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400">{{ editingSupplier.name }}</p>
            </div>
          </div>
          <button @click="cancelEdit" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <!-- Formulario ULTRA Compacto - Aprovecha TODO el espacio -->
        <div class="flex-1 overflow-y-auto p-6">
          <div class="grid grid-cols-3 gap-3">
            <!-- Fila 1: Nombre completo en 3 columnas -->
            <div class="col-span-3">
              <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Nombre del Proveedor *</label>
              <input v-model="editingSupplier.name" type="text" required
                     class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            
            <!-- Fila 2: Documento, Contacto, Teléfono -->
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Documento/NIT *</label>
              <input v-model="editingSupplier.document" type="text" required
                     class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Contacto</label>
              <input v-model="editingSupplier.contact_person" type="text"
                     class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Teléfono</label>
              <input v-model="editingSupplier.phone" type="text"
                     class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            
            <!-- Fila 3: Email y Dirección (2 columnas) + Checkbox -->
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Email</label>
              <input v-model="editingSupplier.email" type="email"
                     class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Dirección</label>
              <input v-model="editingSupplier.address" type="text"
                     class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            
            <!-- Estado (alineado con los inputs) -->
            <div class="flex items-end">
              <label class="flex items-center gap-2 cursor-pointer pb-2">
                <input v-model="editingSupplier.active" type="checkbox"
                       class="w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                <span class="text-sm font-medium text-gray-900 dark:text-white">Activo</span>
              </label>
            </div>
          </div>
        </div>
        
        <!-- Footer Compacto -->
        <div class="px-6 py-3 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900 flex items-center justify-end gap-2.5 flex-shrink-0">
          <button @click="cancelEdit"
                  class="px-4 py-2 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-lg border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
            Cancelar
          </button>
          <button @click="saveEditedSupplier"
                  :disabled="savingSupplier || !editingSupplier.name || !editingSupplier.document"
                  class="px-5 py-2 bg-amber-600 dark:bg-amber-600 hover:bg-amber-700 dark:hover:bg-amber-500 text-white text-sm font-bold rounded-lg shadow-lg shadow-amber-400/40 dark:shadow-amber-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
            <svg v-if="savingSupplier" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            {{ savingSupplier ? 'Guardando...' : 'Guardar Cambios' }}
          </button>
        </div>
      </div>
      
      <!-- Detalles del proveedor seleccionado -->
      <div v-else-if="viewMode === 'detail' && selectedSupplier" class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden" style="height: calc(100vh - 200px); display: flex; flex-direction: column;">
        
        <!-- Header del proveedor -->
        <div class="p-6 border-b border-gray-200 dark:border-zinc-800 bg-gradient-to-r from-gray-50 to-white dark:from-zinc-900 dark:to-zinc-900 flex-shrink-0">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ selectedSupplier.name }}</h2>
                <span :class="[
                        'px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border',
                        selectedSupplier.active 
                          ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' 
                          : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
                      ]">
                  {{ selectedSupplier.active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
              <p class="text-sm text-gray-600 dark:text-zinc-400" v-if="selectedSupplier.contact_person">
                Contacto: {{ selectedSupplier.contact_person }}
              </p>
            </div>
            
            <div class="flex items-center gap-2">
              <button @click="editSupplier(selectedSupplier)"
                      class="p-2.5 text-slate-400 dark:text-zinc-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg border border-transparent hover:border-amber-100 dark:hover:border-amber-900/30 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              
              <button @click="toggleSupplierStatus"
                      :class="[
                        'p-2.5 rounded-lg border transition-all duration-200',
                        selectedSupplier.active
                          ? 'text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 border-transparent hover:border-rose-100 dark:hover:border-rose-900/30'
                          : 'text-slate-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border-transparent hover:border-emerald-100 dark:hover:border-emerald-900/30'
                      ]">
                <svg v-if="selectedSupplier.active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- KPIs rápidos -->
          <div class="grid grid-cols-3 gap-3 mt-4">
            <div class="bg-white dark:bg-zinc-800/80 rounded-lg p-3 border border-gray-200 dark:border-zinc-700 shadow-sm dark:shadow-black/30">
              <p class="text-xs text-gray-600 dark:text-zinc-400 mb-1">Total Compras</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white">
                ${{ formatNumber(selectedSupplier.total_purchases_amount || 0) }}
              </p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                {{ selectedSupplier.purchase_orders_count || 0 }} órdenes
              </p>
            </div>

            <div class="bg-white dark:bg-zinc-800/80 rounded-lg p-3 border border-gray-200 dark:border-zinc-700 shadow-sm dark:shadow-black/30">
              <p class="text-xs text-gray-600 dark:text-zinc-400 mb-1">Productos</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white">
                {{ selectedSupplier.products_count || 0 }}
              </p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                en catálogo
              </p>
            </div>

            <div class="bg-white dark:bg-zinc-800/80 rounded-lg p-3 border border-gray-200 dark:border-zinc-700 shadow-sm dark:shadow-black/30">
              <p class="text-xs text-gray-600 dark:text-zinc-400 mb-1">Deuda Actual</p>
              <p :class="[
                    'text-lg font-bold',
                    selectedSupplier.current_debt > 0 
                      ? 'text-red-600 dark:text-red-400' 
                      : 'text-gray-400 dark:text-zinc-600'
                  ]">
                ${{ formatNumber(selectedSupplier.current_debt || 0) }}
              </p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                saldo pendiente
              </p>
            </div>
          </div>
        </div>

        <!-- Contenido scrollable -->
        <div class="flex-1 overflow-y-auto">
          
          <!-- RESUMEN - Información ULTRA Compacta (Solo 1 fila) -->
          <div class="p-4 border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900/50">
            <div class="grid grid-cols-6 gap-2">
              <div v-if="selectedSupplier.phone" class="bg-white dark:bg-zinc-800 rounded-lg px-2 py-1.5 border border-gray-200 dark:border-zinc-700">
                <p class="text-[10px] text-gray-500 dark:text-zinc-500 mb-0.5">Teléfono</p>
                <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ selectedSupplier.phone }}</p>
              </div>

              <div v-if="selectedSupplier.email" class="bg-white dark:bg-zinc-800 rounded-lg px-2 py-1.5 border border-gray-200 dark:border-zinc-700">
                <p class="text-[10px] text-gray-500 dark:text-zinc-500 mb-0.5">Email</p>
                <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ selectedSupplier.email }}</p>
              </div>

              <div v-if="selectedSupplier.document" class="bg-white dark:bg-zinc-800 rounded-lg px-2 py-1.5 border border-gray-200 dark:border-zinc-700">
                <p class="text-[10px] text-gray-500 dark:text-zinc-500 mb-0.5">Documento</p>
                <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ selectedSupplier.document }}</p>
              </div>

              <div v-if="selectedSupplier.payment_terms" class="bg-white dark:bg-zinc-800 rounded-lg px-2 py-1.5 border border-gray-200 dark:border-zinc-700">
                <p class="text-[10px] text-gray-500 dark:text-zinc-500 mb-0.5">Plazo</p>
                <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ formatPaymentTerms(selectedSupplier.payment_terms) }}</p>
              </div>

              <div v-if="selectedSupplier.last_purchase_date" class="bg-white dark:bg-zinc-800 rounded-lg px-2 py-1.5 border border-gray-200 dark:border-zinc-700">
                <p class="text-[10px] text-gray-500 dark:text-zinc-500 mb-0.5">Última Compra</p>
                <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ formatDate(selectedSupplier.last_purchase_date) }}</p>
              </div>

              <div v-if="selectedSupplier.last_purchase_date" class="bg-white dark:bg-zinc-800 rounded-lg px-2 py-1.5 border border-gray-200 dark:border-zinc-700">
                <p class="text-[10px] text-gray-500 dark:text-zinc-500 mb-0.5">Hace</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ daysSinceLastPurchase(selectedSupplier.last_purchase_date) }}d</p>
              </div>
            </div>
          </div>

          <!-- TABLA DE PRODUCTOS -->
          <div class="p-4">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Productos del Proveedor
              </h3>
              <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 rounded-full text-[10px] font-bold border border-blue-100 dark:border-blue-800">
                {{ supplierProducts.length }}
              </span>
            </div>

            <!-- Loading -->
            <div v-if="loadingProducts" class="flex items-center justify-center py-12">
              <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <!-- Empty -->
            <div v-else-if="supplierProducts.length === 0" class="text-center py-12 bg-gray-50 dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
              <div class="w-16 h-16 bg-gray-100 dark:bg-zinc-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <p class="text-sm font-medium text-gray-900 dark:text-white">Sin productos</p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Este proveedor no tiene productos asignados</p>
            </div>

            <!-- Tabla de Productos COMPACTA -->
            <div v-else class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-300 dark:border-zinc-800 overflow-hidden shadow-sm">
              <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800">
                  <tr>
                    <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">#</th>
                    <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Producto</th>
                    <th class="px-3 py-2 text-center text-[10px] font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">SKU</th>
                    <th class="px-3 py-2 text-right text-[10px] font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">P. Compra</th>
                    <th class="px-3 py-2 text-right text-[10px] font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">P. Venta</th>
                    <th class="px-3 py-2 text-center text-[10px] font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Stock</th>
                    <th class="px-3 py-2 text-center text-[10px] font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Estado</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
                  <tr v-for="(product, index) in supplierProducts" :key="product?.id || `product-${index}`"
                      class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-all duration-200">
                    <td class="px-3 py-2 text-xs text-gray-500 dark:text-zinc-500">
                      {{ index + 1 }}
                    </td>
                    <td class="px-3 py-2">
                      <div class="flex items-center gap-2">
                        <div v-if="product.image_url" class="w-8 h-8 rounded-lg overflow-hidden bg-gray-100 dark:bg-zinc-800 flex-shrink-0">
                          <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover">
                        </div>
                        <div v-else class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                          <svg class="w-4 h-4 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                          </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ product.name }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="px-3 py-2 text-center">
                      <span class="text-[10px] font-mono text-gray-600 dark:text-zinc-400">
                        {{ product.sku || '-' }}
                      </span>
                    </td>
                    <td class="px-3 py-2 text-right">
                      <span class="text-xs font-mono font-medium text-gray-900 dark:text-white">
                        ${{ formatNumber(product.purchase_price || product.cost_price || 0) }}
                      </span>
                    </td>
                    <td class="px-3 py-2 text-right">
                      <span class="text-xs font-mono font-bold text-gray-900 dark:text-white">
                        ${{ formatNumber(product.sale_price || product.price || 0) }}
                      </span>
                    </td>
                    <td class="px-3 py-2 text-center">
                      <span :class="[
                              'px-2 py-0.5 rounded-lg text-[10px] font-bold',
                              product.current_stock <= 0 
                                ? 'bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-400' 
                                : product.current_stock <= (product.min_stock || 5)
                                  ? 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400'
                                  : 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400'
                            ]">
                        {{ product.current_stock || 0 }}
                      </span>
                    </td>
                    <td class="px-3 py-2 text-center">
                      <span :class="[
                              'px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide border',
                              product.active 
                                ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' 
                                : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
                            ]">
                        {{ product.active ? 'OK' : 'NO' }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>

  <!-- 🎭 MODAL: Crear Proveedor -->
  <Transition name="modal">
    <div v-if="viewMode === 'create'" 
         @click.self="cancelCreate"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
      
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col border border-gray-300 dark:border-zinc-800 animate-modal-in">
        
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 dark:border-zinc-800 bg-gradient-to-r from-blue-50 to-white dark:from-blue-950/20 dark:to-zinc-900 flex-shrink-0 rounded-t-2xl">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-600 dark:bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white">Nuevo Proveedor</h2>
              <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Complete la información del proveedor</p>
            </div>
            <button @click="cancelCreate"
                    class="p-2.5 text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Formulario (scrolleable) -->
        <div class="flex-1 overflow-y-auto p-6">
          <div class="space-y-5">
            
            <!-- Información básica -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-200 dark:border-zinc-700">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wide">Información Básica</h3>
              
              <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Nombre del Proveedor *</label>
                  <input v-model="newSupplier.name" type="text" required
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Documento/NIT *</label>
                  <input v-model="newSupplier.document" type="text" required
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Persona de Contacto</label>
                  <input v-model="newSupplier.contact_person" type="text"
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
              </div>
            </div>
            
            <!-- Información de contacto -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-200 dark:border-zinc-700">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wide">Contacto</h3>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Teléfono</label>
                  <input v-model="newSupplier.phone" type="text"
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Email</label>
                  <input v-model="newSupplier.email" type="email"
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="col-span-2">
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Dirección</label>
                  <input v-model="newSupplier.address" type="text"
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
              </div>
            </div>
            
            <!-- Términos comerciales -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-200 dark:border-zinc-700">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wide">Términos Comerciales</h3>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Plazo de Pago</label>
                  <select v-model="newSupplier.payment_terms"
                          class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="immediate">Inmediato</option>
                    <option value="15_days">15 días</option>
                    <option value="30_days">30 días</option>
                    <option value="45_days">45 días</option>
                    <option value="60_days">60 días</option>
                    <option value="90_days">90 días</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Límite de Crédito</label>
                  <input v-model.number="newSupplier.credit_limit" type="number" min="0"
                         class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="col-span-2">
                  <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Notas</label>
                  <textarea v-model="newSupplier.notes" rows="3"
                            class="w-full px-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
              </div>
            </div>
            
            <!-- Estado -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-200 dark:border-zinc-700">
              <label class="flex items-center gap-3 cursor-pointer">
                <input v-model="newSupplier.active" type="checkbox"
                       class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <div>
                  <div class="text-sm font-bold text-gray-900 dark:text-white">Proveedor Activo</div>
                  <div class="text-xs text-gray-600 dark:text-zinc-400">El proveedor estará disponible para órdenes de compra</div>
                </div>
              </label>
            </div>
            
          </div>
        </div>
        
        <!-- Footer con botones -->
        <div class="p-6 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900 flex-shrink-0 rounded-b-2xl">
          <div class="flex items-center justify-end gap-3">
            <button @click="cancelCreate"
                    class="px-6 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
              Cancelar
            </button>
            <button @click="saveNewSupplier"
                    :disabled="savingSupplier || !newSupplier.name || !newSupplier.document"
                    class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
              <svg v-if="savingSupplier" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              {{ savingSupplier ? 'Guardando...' : 'Guardar Proveedor' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script>
import { apiCall } from '../services/api.js'

export default {
  name: 'SuppliersViewMasterDetail',
  emits: ['supplier-created'],
  data() {
    return {
      loading: false,
      suppliers: [],
      selectedSupplier: null,
      searchQuery: '',
      filterActive: null, // null = todos, true = activos, false = inactivos
      viewMode: 'list', // 'list' | 'create' | 'detail' | 'edit'
      
      // Productos del proveedor seleccionado
      loadingProducts: false,
      supplierProducts: [],
      
      // Edición de proveedor
      editingSupplier: null,
      
      // Formulario de nuevo proveedor
      newSupplier: {
        name: '',
        document: '',
        email: '',
        phone: '',
        address: '',
        contact_person: '',
        payment_terms: '30_days',
        credit_limit: 0,
        notes: '',
        active: true
      },
      savingSupplier: false
    }
  },
  computed: {
    filteredSuppliers() {
      let filtered = [...this.suppliers]
      
      // Filtrar por búsqueda
      if (this.searchQuery) {
        const search = this.searchQuery.toLowerCase()
        filtered = filtered.filter(s => 
          s.name.toLowerCase().includes(search) ||
          s.contact_person?.toLowerCase().includes(search) ||
          s.phone?.includes(search) ||
          s.email?.toLowerCase().includes(search)
        )
      }
      
      // Filtrar por estado
      if (this.filterActive !== null) {
        filtered = filtered.filter(s => s.active === this.filterActive)
      }
      
      return filtered
    }
  },
  mounted() {
    this.loadSuppliers()
  },
  methods: {
    async loadSuppliers() {
      try {
        this.loading = true
        const response = await apiCall('/suppliers/analytics')
        if (response.success) {
          this.suppliers = response.data.suppliers || []
        }
      } catch (error) {
        console.error('Error cargando proveedores:', error)
        this.$toast?.error('Error al cargar proveedores')
      } finally {
        this.loading = false
      }
    },

    selectSupplier(supplier) {
      this.selectedSupplier = supplier
      this.viewMode = 'detail'
      this.loadSupplierProducts(supplier.id)
    },
    
    showCreateForm() {
      this.viewMode = 'create'
      // NO resetear selectedSupplier - el modal se muestra sobre la vista actual
      // Resetear formulario
      this.newSupplier = {
        name: '',
        document: '',
        email: '',
        phone: '',
        address: '',
        contact_person: '',
        payment_terms: '30_days',
        credit_limit: 0,
        notes: '',
        active: true
      }
    },
    
    cancelCreate() {
      this.viewMode = this.selectedSupplier ? 'detail' : 'list'
      // NO resetear selectedSupplier
    },
    
    async saveNewSupplier() {
      try {
        // Validar campos requeridos
        if (!this.newSupplier.name || !this.newSupplier.document) {
          this.$toast?.error('El nombre y documento son obligatorios')
          return
        }
        
        this.savingSupplier = true
        const response = await apiCall('/suppliers', {
          method: 'POST',
          body: JSON.stringify(this.newSupplier)
        })
        
        if (response.success) {
          this.$toast?.success('Proveedor creado exitosamente')
          // Recargar lista
          await this.loadSuppliers()
          // Seleccionar el nuevo proveedor
          this.selectSupplier(response.data)
        }
      } catch (error) {
        console.error('Error creando proveedor:', error)
        this.$toast?.error(error.message || 'Error al crear proveedor')
      } finally {
        this.savingSupplier = false
      }
    },

    async loadSupplierProducts(supplierId) {
      try {
        this.loadingProducts = true
        this.supplierProducts = []
        
        // Usar endpoint de productos con filtro de proveedor
        // status=all para incluir activos e inactivos
        // per_page=1000 para obtener todos sin paginación
        const response = await apiCall(`/products?supplier_id=${supplierId}&status=all&per_page=1000`)
        console.log('📦 Respuesta productos del proveedor:', response)
        
        if (response.success) {
          // Laravel pagina los resultados, los datos están en response.data.data
          let products = response.data?.data || response.data?.products || response.data || []
          
          // Asegurar que sea un array
          if (!Array.isArray(products)) {
            console.log('⚠️ Respuesta no es un array:', products)
            products = []
          }
          
          // Filtrar productos nulos o sin ID
          this.supplierProducts = products.filter(p => p && p.id)
          console.log('✅ Productos del proveedor cargados:', this.supplierProducts.length, this.supplierProducts)
        }
      } catch (error) {
        console.error('❌ Error cargando productos del proveedor:', error)
        this.supplierProducts = []
      } finally {
        this.loadingProducts = false
      }
    },

    editSupplier(supplier) {
      this.editingSupplier = { ...supplier }
      this.viewMode = 'edit'
    },

    cancelEdit() {
      this.viewMode = 'detail'
      this.editingSupplier = null
    },

    async saveEditedSupplier() {
      try {
        if (!this.editingSupplier.name || !this.editingSupplier.document) {
          this.$toast?.error('El nombre y documento son obligatorios')
          return
        }
        
        this.savingSupplier = true
        const response = await apiCall(`/suppliers/${this.editingSupplier.id}`, {
          method: 'PUT',
          body: JSON.stringify(this.editingSupplier)
        })
        
        if (response.success) {
          this.$toast?.success('Proveedor actualizado exitosamente')
          // Actualizar en la lista
          const index = this.suppliers.findIndex(s => s.id === this.editingSupplier.id)
          if (index !== -1) {
            this.suppliers[index] = { ...this.suppliers[index], ...this.editingSupplier }
          }
          // Actualizar selectedSupplier
          this.selectedSupplier = { ...this.selectedSupplier, ...this.editingSupplier }
          this.viewMode = 'detail'
          this.editingSupplier = null
        }
      } catch (error) {
        console.error('Error actualizando proveedor:', error)
        this.$toast?.error(error.message || 'Error al actualizar proveedor')
      } finally {
        this.savingSupplier = false
      }
    },

    async toggleSupplierStatus() {
      if (!this.selectedSupplier) return
      
      try {
        const newStatus = !this.selectedSupplier.active
        const response = await apiCall(`/suppliers/${this.selectedSupplier.id}`, {
          method: 'PUT',
          body: JSON.stringify({
            active: newStatus
          })
        })
        
        if (response.success) {
          this.selectedSupplier.active = newStatus
          // Actualizar en la lista
          const index = this.suppliers.findIndex(s => s.id === this.selectedSupplier.id)
          if (index !== -1) {
            this.suppliers[index].active = newStatus
          }
          this.$toast?.success(`Proveedor ${newStatus ? 'activado' : 'desactivado'} correctamente`)
        }
      } catch (error) {
        console.error('Error actualizando estado:', error)
        this.$toast?.error('Error al actualizar estado')
      }
    },

    formatNumber(value) {
      return new Intl.NumberFormat('es-CO').format(value || 0)
    },

    formatDate(dateString) {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return date.toLocaleDateString('es-CO', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
      })
    },

    formatPaymentTerms(terms) {
      const termsMap = {
        'immediate': 'Inmediato',
        '15_days': '15 días',
        '30_days': '30 días',
        '45_days': '45 días',
        '60_days': '60 días',
        '90_days': '90 días'
      }
      return termsMap[terms] || terms
    },

    daysSinceLastPurchase(date) {
      if (!date) return 0
      const lastDate = new Date(date)
      const today = new Date()
      const diffTime = Math.abs(today - lastDate)
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
      return diffDays
    }
  }
}
</script>

<style scoped>
/* Animación del modal */
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .animate-modal-in,
.modal-leave-to .animate-modal-in {
  transform: scale(0.95);
}

@keyframes modal-in {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-modal-in {
  animation: modal-in 0.2s ease-out;
}
</style>
