<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Header elegante -->
    <div class="bg-white border-b border-gray-300 sticky top-0 z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl shadow-lg flex items-center justify-center">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
            </div>
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Categorías de Gastos</h1>
              <p class="text-sm text-gray-500 mt-1">Gestiona las categorías para organizar tus gastos</p>
            </div>
          </div>
          <div class="flex items-center space-x-3">
            <button
              @click="loadCategories"
              :disabled="loading"
              class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2"
            >
              <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <span>Actualizar</span>
            </button>
            <button
              @click="openCreateModal"
              class="px-4 py-2 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white rounded-lg font-medium transition-all duration-200 flex items-center space-x-2 shadow-md"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>Nueva Categoría</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Métricas -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Categorías -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 hover:shadow-lg transition-all duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500">Total Categorías</p>
              <p class="text-3xl font-bold text-purple-600 mt-2">{{ categories.total || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Activas -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 hover:shadow-lg transition-all duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500">Activas</p>
              <p class="text-3xl font-bold text-green-600 mt-2">{{ statistics.active_count || 0 }}</p>
              <p class="text-xs text-gray-400 mt-1">{{ activePercentage }}%</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Con Gastos -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 hover:shadow-lg transition-all duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500">Con Gastos</p>
              <p class="text-3xl font-bold text-blue-600 mt-2">{{ statistics.with_expenses_count || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Inactivas -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 hover:shadow-lg transition-all duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500">Inactivas</p>
              <p class="text-3xl font-bold text-gray-600 mt-2">{{ inactiveCount }}</p>
            </div>
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Búsqueda -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
            <input
              v-model="filters.search"
              @keyup.enter="applyFilters"
              type="text"
              placeholder="Buscar por nombre..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            />
          </div>

          <!-- Estado -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
            <select
              v-model="filters.is_active"
              @change="applyFilters"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            >
              <option value="">Todas</option>
              <option value="1">Activas</option>
              <option value="0">Inactivas</option>
            </select>
          </div>

          <!-- Items por página -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Items por página</label>
            <select
              v-model.number="filters.per_page"
              @change="applyFilters"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            >
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
              <option :value="100">100</option>
            </select>
          </div>
        </div>

        <div class="mt-4 flex justify-end">
          <button
            @click="clearFilters"
            class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-lg font-medium transition-all duration-200"
          >
            Limpiar Filtros
          </button>
        </div>
      </div>

      <!-- Tabla -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm mt-6 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gastos</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Monto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="loading">
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                  <svg class="animate-spin h-8 w-8 text-purple-600 mx-auto" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <p class="mt-2">Cargando categorías...</p>
                </td>
              </tr>
              <tr v-else-if="categories.data && categories.data.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                  No se encontraron categorías
                </td>
              </tr>
              <tr v-else v-for="category in categories.data?.filter(c => c !== null && c !== undefined)" :key="category.id" class="hover:bg-gray-50 transition-colors duration-150">
                <!-- Color -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center space-x-2">
                    <div
                      class="w-6 h-6 rounded-full border-2 border-gray-300"
                      :style="{ backgroundColor: category.color }"
                    ></div>
                  </div>
                </td>
                <!-- Nombre -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                </td>
                <!-- Descripción -->
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-500 max-w-xs truncate">{{ category.description || '-' }}</div>
                </td>
                <!-- Gastos -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                    {{ category.expenses_count || 0 }}
                  </span>
                </td>
                <!-- Total Monto -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">${{ formatNumber(category.total_amount || 0) }}</div>
                </td>
                <!-- Estado -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                    :class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                  >
                    {{ category.is_active ? 'Activa' : 'Inactiva' }}
                  </span>
                </td>
                <!-- Acciones -->
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <!-- Ver -->
                    <button
                      @click="viewCategory(category)"
                      class="text-blue-600 hover:text-blue-900 transition-colors duration-150"
                      title="Ver detalles"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    <!-- Editar -->
                    <button
                      @click="editCategory(category)"
                      class="text-purple-600 hover:text-purple-900 transition-colors duration-150"
                      title="Editar"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <!-- Toggle Estado -->
                    <button
                      @click="toggleActive(category)"
                      :class="category.is_active ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900'"
                      class="transition-colors duration-150"
                      :title="category.is_active ? 'Desactivar' : 'Activar'"
                    >
                      <svg v-if="category.is_active" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                      </svg>
                      <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </button>
                    <!-- Eliminar -->
                    <button
                      @click="deleteCategory(category)"
                      class="text-red-600 hover:text-red-900 transition-colors duration-150"
                      title="Eliminar"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div v-if="categories.data && categories.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                @click="goToPage(categories.current_page - 1)"
                :disabled="categories.current_page === 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Anterior
              </button>
              <button
                @click="goToPage(categories.current_page + 1)"
                :disabled="categories.current_page === categories.last_page"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Siguiente
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Mostrando
                  <span class="font-medium">{{ categories.from || 0 }}</span>
                  a
                  <span class="font-medium">{{ categories.to || 0 }}</span>
                  de
                  <span class="font-medium">{{ categories.total || 0 }}</span>
                  resultados
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <!-- Primera -->
                  <button
                    @click="goToPage(1)"
                    :disabled="categories.current_page === 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                  </button>
                  <!-- Anterior -->
                  <button
                    @click="goToPage(categories.current_page - 1)"
                    :disabled="categories.current_page === 1"
                    class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                  </button>
                  <!-- Números de página -->
                  <button
                    v-for="page in paginationPages"
                    :key="page"
                    @click="page !== '...' ? goToPage(page) : null"
                    :disabled="page === '...'"
                    :class="[
                      page === categories.current_page
                        ? 'z-10 bg-purple-50 border-purple-500 text-purple-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                      page === '...' ? 'cursor-default' : ''
                    ]"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium disabled:cursor-default"
                  >
                    {{ page }}
                  </button>
                  <!-- Siguiente -->
                  <button
                    @click="goToPage(categories.current_page + 1)"
                    :disabled="categories.current_page === categories.last_page"
                    class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </button>
                  <!-- Última -->
                  <button
                    @click="goToPage(categories.last_page)"
                    :disabled="categories.current_page === categories.last_page"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Crear/Editar -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] flex flex-col">
        <!-- Header sticky -->
        <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">
              {{ isEditing ? 'Editar Categoría' : 'Nueva Categoría' }}
            </h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Body scrollable -->
        <div class="flex-1 overflow-y-auto px-6 py-4">
          <div class="space-y-4">
            <!-- Nombre -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Nombre <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Ej: Servicios, Mantenimiento, etc."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                required
              />
            </div>

            <!-- Color -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Color <span class="text-red-500">*</span>
              </label>
              <div class="flex items-center space-x-4">
                <input
                  v-model="form.color"
                  type="color"
                  class="h-10 w-20 border border-gray-300 rounded cursor-pointer"
                />
                <input
                  v-model="form.color"
                  type="text"
                  placeholder="#000000"
                  class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                />
                <div
                  class="w-10 h-10 rounded-lg border-2 border-gray-300"
                  :style="{ backgroundColor: form.color }"
                ></div>
              </div>
            </div>

            <!-- Descripción -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Descripción
              </label>
              <textarea
                v-model="form.description"
                rows="3"
                placeholder="Describe el propósito de esta categoría..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              ></textarea>
            </div>

            <!-- Estado -->
            <div class="flex items-center">
              <input
                v-model="form.is_active"
                type="checkbox"
                id="is_active"
                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
              />
              <label for="is_active" class="ml-2 block text-sm text-gray-900">
                Categoría activa
              </label>
            </div>
          </div>
        </div>

        <!-- Footer sticky -->
        <div class="sticky bottom-0 bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-2xl flex justify-end space-x-3">
          <button
            @click="closeModal"
            :disabled="saving"
            class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-lg font-medium transition-all duration-200"
          >
            Cancelar
          </button>
          <button
            @click="saveCategory"
            :disabled="saving || !form.name || !form.color"
            class="px-4 py-2 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white rounded-lg font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
          >
            <svg v-if="saving" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ saving ? 'Guardando...' : 'Guardar' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Ver Detalles -->
    <div v-if="showViewModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] flex flex-col">
        <!-- Header sticky -->
        <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">Detalles de la Categoría</h3>
            <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-500">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Body scrollable -->
        <div class="flex-1 overflow-y-auto px-6 py-4">
          <div v-if="selectedCategory" class="space-y-6">
            <!-- Información básica -->
            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
              <div class="flex items-center justify-between">
                <h4 class="text-lg font-semibold text-gray-900">{{ selectedCategory.name }}</h4>
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                  :class="selectedCategory.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                >
                  {{ selectedCategory.is_active ? 'Activa' : 'Inactiva' }}
                </span>
              </div>
              
              <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Color:</span>
                <div
                  class="w-8 h-8 rounded-lg border-2 border-gray-300"
                  :style="{ backgroundColor: selectedCategory.color }"
                ></div>
                <span class="text-sm font-mono text-gray-700">{{ selectedCategory.color }}</span>
              </div>

              <div v-if="selectedCategory.description">
                <span class="text-sm text-gray-500">Descripción:</span>
                <p class="text-sm text-gray-700 mt-1">{{ selectedCategory.description }}</p>
              </div>
            </div>

            <!-- Estadísticas -->
            <div>
              <h4 class="text-sm font-semibold text-gray-900 mb-3">Estadísticas</h4>
              <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                  <p class="text-sm text-blue-600 font-medium">Total de Gastos</p>
                  <p class="text-2xl font-bold text-blue-900 mt-1">{{ selectedCategory.expenses_count || 0 }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                  <p class="text-sm text-green-600 font-medium">Monto Total</p>
                  <p class="text-2xl font-bold text-green-900 mt-1">${{ formatNumber(selectedCategory.total_amount || 0) }}</p>
                </div>
              </div>
            </div>

            <!-- Gastos recientes -->
            <div v-if="selectedCategory.recent_expenses && selectedCategory.recent_expenses.length > 0">
              <h4 class="text-sm font-semibold text-gray-900 mb-3">Gastos Recientes</h4>
              <div class="space-y-2">
                <div
                  v-for="expense in selectedCategory.recent_expenses"
                  :key="expense.id"
                  class="bg-gray-50 rounded-lg p-3 flex items-center justify-between"
                >
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ expense.description }}</p>
                    <p class="text-xs text-gray-500">{{ formatDate(expense.date) }}</p>
                  </div>
                  <p class="text-sm font-semibold text-gray-900">${{ formatNumber(expense.amount) }}</p>
                </div>
              </div>
            </div>
            <div v-else>
              <h4 class="text-sm font-semibold text-gray-900 mb-3">Gastos Recientes</h4>
              <p class="text-sm text-gray-500 text-center py-4">No hay gastos registrados</p>
            </div>
          </div>
        </div>

        <!-- Footer sticky -->
        <div class="sticky bottom-0 bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-2xl flex justify-end">
          <button
            @click="showViewModal = false"
            class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-lg font-medium transition-all duration-200"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import apiClient from '@/services/apiClient'

export default {
  name: 'ExpenseCategoriesManager',
  data() {
    return {
      loading: false,
      saving: false,
      showModal: false,
      showViewModal: false,
      isEditing: false,
      categories: {
        data: [],
        total: 0,
        current_page: 1,
        last_page: 1,
        from: 0,
        to: 0
      },
      statistics: {
        active_count: 0,
        with_expenses_count: 0
      },
      filters: {
        search: '',
        is_active: '',
        per_page: 25,
        page: 1
      },
      form: {
        name: '',
        color: '#3B82F6',
        description: '',
        is_active: true
      },
      selectedCategory: null
    }
  },
  computed: {
    activePercentage() {
      if (!this.categories.total) return 0
      return Math.round((this.statistics.active_count / this.categories.total) * 100)
    },
    inactiveCount() {
      return this.categories.total - this.statistics.active_count
    },
    paginationPages() {
      const pages = []
      const current = this.categories.current_page
      const last = this.categories.last_page

      if (last <= 7) {
        for (let i = 1; i <= last; i++) {
          pages.push(i)
        }
      } else {
        if (current <= 3) {
          for (let i = 1; i <= 5; i++) {
            pages.push(i)
          }
          pages.push('...')
          pages.push(last)
        } else if (current >= last - 2) {
          pages.push(1)
          pages.push('...')
          for (let i = last - 4; i <= last; i++) {
            pages.push(i)
          }
        } else {
          pages.push(1)
          pages.push('...')
          for (let i = current - 1; i <= current + 1; i++) {
            pages.push(i)
          }
          pages.push('...')
          pages.push(last)
        }
      }

      return pages
    }
  },
  mounted() {
    this.loadCategories()
    this.loadStatistics()
  },
  methods: {
    async loadCategories() {
      this.loading = true
      try {
        const params = {
          page: this.filters.page,
          per_page: this.filters.per_page
        }

        if (this.filters.search) {
          params.search = this.filters.search
        }

        if (this.filters.is_active !== '') {
          params.is_active = this.filters.is_active
        }

        const response = await apiClient.get('/expense-categories', { params })
        this.categories = response.data
      } catch (error) {
        console.error('Error al cargar categorías:', error)
        this.$toast?.error('Error al cargar las categorías')
      } finally {
        this.loading = false
      }
    },
    async loadStatistics() {
      try {
        const response = await apiClient.get('/expense-categories/statistics')
        this.statistics = response.data
      } catch (error) {
        console.error('Error al cargar estadísticas:', error)
      }
    },
    async viewCategory(category) {
      try {
        const response = await apiClient.get(`/expense-categories/${category.id}`)
        this.selectedCategory = response.data
        this.showViewModal = true
      } catch (error) {
        console.error('Error al cargar detalles:', error)
        this.$toast?.error('Error al cargar los detalles de la categoría')
      }
    },
    openCreateModal() {
      this.isEditing = false
      this.resetForm()
      this.showModal = true
    },
    editCategory(category) {
      this.isEditing = true
      this.form = {
        id: category.id,
        name: category.name,
        color: category.color,
        description: category.description || '',
        is_active: category.is_active
      }
      this.showModal = true
    },
    async saveCategory() {
      if (!this.form.name || !this.form.color) {
        this.$toast?.warning('Por favor completa los campos requeridos')
        return
      }

      this.saving = true
      try {
        if (this.isEditing) {
          await apiClient.put(`/expense-categories/${this.form.id}`, this.form)
          this.$toast?.success('Categoría actualizada exitosamente')
        } else {
          await apiClient.post('/expense-categories', this.form)
          this.$toast?.success('Categoría creada exitosamente')
        }
        this.closeModal()
        this.loadCategories()
        this.loadStatistics()
      } catch (error) {
        console.error('Error al guardar categoría:', error)
        this.$toast?.error('Error al guardar la categoría')
      } finally {
        this.saving = false
      }
    },
    async toggleActive(category) {
      try {
        await apiClient.patch(`/expense-categories/${category.id}/toggle`)
        this.$toast?.success(`Categoría ${category.is_active ? 'desactivada' : 'activada'} exitosamente`)
        this.loadCategories()
        this.loadStatistics()
      } catch (error) {
        console.error('Error al cambiar estado:', error)
        this.$toast?.error('Error al cambiar el estado de la categoría')
      }
    },
    async deleteCategory(category) {
      if (!confirm(`¿Estás seguro de eliminar la categoría "${category.name}"?`)) {
        return
      }

      try {
        await apiClient.delete(`/expense-categories/${category.id}`)
        this.$toast?.success('Categoría eliminada exitosamente')
        this.loadCategories()
        this.loadStatistics()
      } catch (error) {
        console.error('Error al eliminar categoría:', error)
        this.$toast?.error('Error al eliminar la categoría')
      }
    },
    closeModal() {
      this.showModal = false
      this.resetForm()
    },
    resetForm() {
      this.form = {
        name: '',
        color: '#3B82F6',
        description: '',
        is_active: true
      }
    },
    applyFilters() {
      this.filters.page = 1
      this.loadCategories()
    },
    clearFilters() {
      this.filters = {
        search: '',
        is_active: '',
        per_page: 25,
        page: 1
      }
      this.loadCategories()
    },
    goToPage(page) {
      if (page >= 1 && page <= this.categories.last_page) {
        this.filters.page = page
        this.loadCategories()
      }
    },
    formatNumber(value) {
      return new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(value)
    },
    formatDate(dateString) {
      if (!dateString) return '-'
      return new Intl.DateTimeFormat('es-CO', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      }).format(new Date(dateString))
    }
  }
}
</script>
