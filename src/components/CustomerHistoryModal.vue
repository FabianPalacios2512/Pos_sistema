<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-hidden border border-gray-200 dark:border-gray-700">
      
      <!-- Header Compacto -->
      <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <div>
              <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ customer?.name || 'Cliente' }}</h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">Historial de Compras</p>
            </div>
          </div>
          <button
            @click="$emit('close')"
            class="min-w-[40px] min-h-[40px] hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg flex items-center justify-center transition-all text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Contenido del Modal -->
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
        
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p class="text-gray-600 dark:text-gray-400">Cargando historial...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="text-red-600 dark:text-red-400 font-medium">{{ error }}</p>
          <button
            @click="loadCustomerHistory"
            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            Intentar de nuevo
          </button>
        </div>

        <!-- Contenido Principal -->
        <div v-else class="space-y-4">
          
          <!-- Resumen Estadísticas -->
          <div class="grid grid-cols-4 gap-3">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3" style="background-color: #EBF2FF;">
              <div class="flex items-center gap-2 mb-1">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Facturas</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ summary.totalInvoices }}</p>
            </div>

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3" style="background-color: #E6FFF1;">
              <div class="flex items-center gap-2 mb-1">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                </div>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Total Gastado</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">${{ summary.totalSpent.toLocaleString() }}</p>
            </div>

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3" style="background-color: #F3E8FF;">
              <div class="flex items-center gap-2 mb-1">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Productos</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ summary.totalProducts }}</p>
            </div>

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3" style="background-color: #FFF9E6;">
              <div class="flex items-center gap-2 mb-1">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                  </svg>
                </div>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Promedio</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">${{ summary.averageOrder.toLocaleString() }}</p>
            </div>
          </div>

          <!-- Productos Más Comprados -->
          <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
              <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
              </div>
              <span>Productos Favoritos</span>
            </h3>
            
            <div v-if="frequentProducts.length === 0" class="text-center py-8">
              <p class="text-gray-500 dark:text-gray-400">No hay productos frecuentes aún</p>
            </div>
            
            <div v-else class="grid grid-cols-3 gap-3">
              <div
                v-for="product in frequentProducts"
                :key="product.id"
                class="bg-white dark:bg-gray-700 rounded-lg p-3 border border-gray-200 dark:border-gray-600"
              >
                <div class="flex items-center gap-2">
                  <!-- Imagen del producto o placeholder SVG -->
                  <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-600 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <img
                      v-if="product.image"
                      :src="product.image"
                      :alt="product.name"
                      class="w-full h-full object-cover"
                      @error="handleImageError"
                    />
                    <svg v-else class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ product.name }}</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ product.quantity }} veces • ${{ product.total.toLocaleString() }}
                    </p>
                  </div>
                  <button
                    @click="addToCart(product)"
                    class="w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center justify-center transition-colors"
                    title="Agregar al carrito"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Historial de Facturas -->
          <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
            <div class="p-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600 rounded-t-lg">
              <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <span>Historial de Compras ({{ invoices.length }})</span>
              </h3>
            </div>
            
            <div v-if="invoices.length === 0" class="p-6 text-center">
              <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="text-sm text-gray-500 dark:text-gray-400">No hay compras registradas</p>
            </div>
            
            <div v-else class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                  <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                      Factura
                    </th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                      Fecha
                    </th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                      Productos
                    </th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                      Total
                    </th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                      Estado
                    </th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                      Acciones
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                  <tr
                    v-for="invoice in paginatedInvoices"
                    :key="invoice.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-600"
                  >
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ invoice.custom_number || invoice.number || `#${invoice.id}` }}
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ formatDate(invoice.date) }}
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="text-sm text-gray-900 dark:text-white">
                        {{ invoice.items?.length || 0 }} artículos
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400 max-w-xs truncate">
                        {{ getItemsPreview(invoice.items) }}
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="text-sm font-semibold text-gray-900 dark:text-white">
                        ${{ parseFloat(invoice.total || 0).toLocaleString() }}
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <span
                        :class="[
                          'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                          invoice.status === 'paid' 
                            ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
                        ]"
                      >
                        {{ invoice.status === 'paid' ? 'Pagada' : 'Pendiente' }}
                      </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="flex gap-2">
                        <button
                          @click="viewInvoice(invoice)"
                          class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                          title="Ver detalles"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                          </svg>
                        </button>
                        <button
                          @click="reorderInvoice(invoice)"
                          class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
                          title="Reordenar productos"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13v5a2 2 0 002 2h9a2 2 0 002-2v-5m-9 0V9a2 2 0 012-2h5a2 2 0 012 2v4.1"></path>
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Paginación -->
            <div v-if="invoices.length > itemsPerPage" class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-600">
              <div class="flex items-center justify-between">
                <div class="text-xs text-gray-700 dark:text-gray-300">
                  {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, invoices.length) }} de {{ invoices.length }}
                </div>
                <div class="flex gap-2">
                  <button
                    @click="currentPage = Math.max(1, currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Anterior
                  </button>
                  <button
                    @click="currentPage = Math.min(totalPages, currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    class="px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Siguiente
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer con acciones -->
      <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-600">
        <div class="flex justify-end">
          <button
            @click="$emit('close')"
            class="px-4 py-2.5 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Detalles de Factura -->
  <div v-if="showInvoiceDetails" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click="closeInvoiceDetails">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden" @click.stop>
      <!-- Header -->
      <div class="bg-gray-50 dark:bg-gray-900 p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div>
              <h2 class="text-base font-bold text-gray-900 dark:text-white">Detalles de Factura</h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ selectedInvoice?.custom_number || selectedInvoice?.invoice_number }}</p>
            </div>
          </div>
          <button
            @click="closeInvoiceDetails"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg min-w-[40px] min-h-[40px] flex items-center justify-center transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Contenido -->
      <div class="p-4 overflow-y-auto max-h-[calc(90vh-120px)]" v-if="selectedInvoice">
        <!-- Información de la factura -->
        <div class="grid grid-cols-2 gap-3 mb-3">
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Información General</h3>
            <div class="space-y-1.5 text-xs">
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Número:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ selectedInvoice.custom_number || selectedInvoice.invoice_number }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Fecha:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ formatDate(selectedInvoice.date) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Vencimiento:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ formatDate(selectedInvoice.due_date) || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Estado:</span>
                <span 
                  :class="[
                    'inline-flex px-1.5 py-0.5 text-xs font-medium rounded',
                    selectedInvoice.status === 'paid' 
                      ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                      : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                  ]"
                >
                  {{ selectedInvoice.status === 'paid' ? 'Pagada' : 'Pendiente' }}
                </span>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Totales</h3>
            <div class="space-y-1.5 text-xs">
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                <span class="font-medium text-gray-900 dark:text-white">${{ Math.abs(parseFloat(selectedInvoice.subtotal || 0)).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Descuento:</span>
                <span class="font-medium text-gray-900 dark:text-white">-${{ Math.abs(parseFloat(selectedInvoice.discount || 0)).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Impuestos:</span>
                <span class="font-medium text-gray-900 dark:text-white">${{ Math.abs(parseFloat(selectedInvoice.tax || 0)).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between border-t border-gray-300 dark:border-gray-600 pt-1.5 mt-1.5">
                <span class="text-gray-900 dark:text-white font-semibold">Total:</span>
                <span class="font-bold text-sm text-blue-600 dark:text-blue-400">${{ Math.abs(parseFloat(selectedInvoice.total || 0)).toLocaleString() }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Productos de la factura -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
          <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Productos</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
              <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <tr>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Producto</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Cantidad</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Precio Unit.</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                <tr v-for="item in selectedInvoice.items" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="px-3 py-2 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                      <img 
                        :src="item.product?.image || item.product_image || item.image || '/placeholder-product.jpg'" 
                        :alt="item.product?.name || item.product_name || item.name"
                        class="w-8 h-8 object-cover rounded bg-gray-100 dark:bg-gray-600"
                      />
                      <div>
                        <div class="text-xs font-medium text-gray-900 dark:text-white">
                          {{ item.product?.name || item.product_name || item.name || item.description || item.title || item.product?.description || 'Producto sin nombre' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                          SKU: {{ item.product?.sku || item.product_sku || item.sku || item.product_code || item.code || item.product?.code || 'N/A' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">
                    {{ Math.abs(parseInt(item.quantity || 1)) }}
                  </td>
                  <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">
                    ${{ Math.abs(parseFloat(item.unit_price || item.price || item.sale_price || item.product?.price || item.product?.sale_price || 0)).toLocaleString() }}
                  </td>
                  <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-gray-900 dark:text-white">
                    ${{ Math.abs(parseFloat(item.unit_price || item.price || item.sale_price || item.product?.price || item.product?.sale_price || 0) * parseInt(item.quantity || 1)).toLocaleString() }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-600">
        <div class="flex justify-between items-center">
          <button
            @click="smartReorder(selectedInvoice)"
            :disabled="reorderStatus.loading"
            class="px-4 py-2.5 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white text-sm rounded-lg transition-colors flex items-center gap-2"
          >
            <svg v-if="!reorderStatus.loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13v5a2 2 0 002 2h9a2 2 0 002-2v-5m-9 0V9a2 2 0 012-2h5a2 2 0 012 2v4.1"></path>
            </svg>
            <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ reorderStatus.loading ? 'Verificando...' : 'Reordenar' }}</span>
          </button>
          <button
            @click="closeInvoiceDetails"
            class="px-4 py-2.5 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Notificación de productos no disponibles -->
  <div v-if="reorderStatus.message" class="fixed top-4 right-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-3 rounded-lg shadow-lg z-50 max-w-md">
    <div class="flex gap-2">
      <div class="flex-shrink-0">
        <svg class="h-4 w-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
        </svg>
      </div>
      <div class="ml-3">
        <p class="text-sm font-medium">{{ reorderStatus.message }}</p>
        <div v-if="reorderStatus.unavailable.length > 0" class="mt-2">
          <p class="text-xs">Productos no disponibles:</p>
          <ul class="list-disc list-inside text-xs mt-1">
            <li v-for="product in reorderStatus.unavailable" :key="product.id">{{ product.name }}</li>
          </ul>
        </div>
        <button @click="reorderStatus.message = ''" class="mt-2 text-xs underline">Cerrar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { invoicesService } from '../services/invoicesService.js'

// Props
const props = defineProps({
  customer: {
    type: Object,
    required: true
  }
})

// Emits
const emit = defineEmits(['close', 'add-to-cart', 'view-invoice', 'reorder-items'])

// Estado reactivo
const loading = ref(false)
const error = ref(null)
const invoices = ref([])
const currentPage = ref(1)
const itemsPerPage = 10
const showInvoiceDetails = ref(false)
const selectedInvoice = ref(null)
const reorderStatus = ref({ loading: false, unavailable: [], message: '' })

// Computed
const summary = computed(() => {
  const totalInvoices = invoices.value.length
  const totalSpent = invoices.value.reduce((sum, invoice) => sum + parseFloat(invoice.total || 0), 0)
  const totalProducts = invoices.value.reduce((sum, invoice) => sum + (invoice.items?.length || 0), 0)
  const averageOrder = totalInvoices > 0 ? totalSpent / totalInvoices : 0

  return {
    totalInvoices,
    totalSpent,
    totalProducts,
    averageOrder
  }
})

const frequentProducts = computed(() => {
  const productStats = {}
  
  invoices.value.forEach(invoice => {
    if (invoice.items && Array.isArray(invoice.items)) {
      invoice.items.forEach(item => {
        const productId = item.product_id || item.id
        if (!productStats[productId]) {
          productStats[productId] = {
            id: productId,
            name: item.product?.name || item.product_name || item.name || item.description || item.title || 'Producto sin nombre',
            image: item.image_url || item.product?.image_url || item.product_image || item.image,
            quantity: 0,
            total: 0
          }
        }
        productStats[productId].quantity += Math.abs(parseInt(item.quantity || 1))
        productStats[productId].total += Math.abs(parseFloat(item.total_price || item.price * item.quantity || 0))
      })
    }
  })
  
  return Object.values(productStats)
    .sort((a, b) => b.quantity - a.quantity)
    .slice(0, 6)
})

const totalPages = computed(() => Math.ceil(invoices.value.length / itemsPerPage))

const paginatedInvoices = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return invoices.value.slice(start, end)
})

// Métodos
const loadCustomerHistory = async () => {
  if (!props.customer?.id) {
    error.value = 'Cliente no válido'
    return
  }

  try {
    loading.value = true
    error.value = null
    
    // Cargar todas las facturas y filtrar por cliente
    const response = await invoicesService.getInvoices()
    
    if (response.success) {
      // Filtrar facturas por cliente
      const customerInvoices = response.data.filter(invoice => 
        invoice.customer_id === props.customer.id ||
        invoice.customer_name === props.customer.name
      )
      
      // Ordenar por fecha más reciente
      invoices.value = customerInvoices.sort((a, b) => new Date(b.date) - new Date(a.date))
    } else {
      throw new Error('Error al cargar facturas')
    }
  } catch (err) {
    console.error('Error cargando historial del cliente:', err)
    error.value = 'Error al cargar el historial de compras'
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch {
    return 'Fecha inválida'
  }
}

const getItemsPreview = (items) => {
  if (!items || !Array.isArray(items) || items.length === 0) return 'Sin productos'
  
  const firstItems = items.slice(0, 2).map(item => item.product_name || item.name).join(', ')
  const remaining = items.length > 2 ? ` y ${items.length - 2} más` : ''
  
  return firstItems + remaining
}

const addToCart = (product) => {
  emit('add-to-cart', product)
}

// Método para manejar errores de carga de imagen
const handleImageError = (event) => {
  event.target.style.display = 'none'
}

// Método mejorado para ver factura
const viewInvoice = (invoice) => {
  selectedInvoice.value = invoice
  showInvoiceDetails.value = true
}

// Método para cerrar modal de detalles
const closeInvoiceDetails = () => {
  showInvoiceDetails.value = false
  selectedInvoice.value = null
  reorderStatus.value = { loading: false, unavailable: [], message: '' }
}

// Método mejorado para reordenar con verificación de disponibilidad
const reorderInvoice = (invoice) => {
  smartReorder(invoice)
}

// Reordenar inteligente con verificación de stock
const smartReorder = async (invoice) => {
  if (!invoice.items || !Array.isArray(invoice.items)) {
    reorderStatus.value.message = 'No se encontraron productos en esta factura'
    setTimeout(() => reorderStatus.value.message = '', 5000)
    return
  }

  try {
    reorderStatus.value.loading = true
    reorderStatus.value.unavailable = []
    reorderStatus.value.message = ''

    // Importar el servicio de productos para verificar disponibilidad
    const { productsService } = await import('../services/productsService.js')
    const productsResponse = await productsService.getAll()
    
    if (!productsResponse.success) {
      throw new Error('No se pudieron verificar los productos disponibles')
    }

    const availableProducts = productsResponse.data.data || []
    const availableItems = []
    const unavailableItems = []

    // Verificar cada producto
    invoice.items.forEach(item => {
      const productId = item.product_id || item.id
      const requestedQuantity = parseInt(item.quantity || 1)
      
      // Buscar el producto en la lista actual
      const currentProduct = availableProducts.find(p => p.id === productId)
      
      if (!currentProduct) {
        unavailableItems.push({
          id: productId,
          name: item.product_name || item.name,
          reason: 'Producto no encontrado'
        })
      } else if (!currentProduct.active) {
        unavailableItems.push({
          id: productId,
          name: item.product_name || item.name,
          reason: 'Producto inactivo'
        })
      } else if (currentProduct.current_stock < requestedQuantity) {
        unavailableItems.push({
          id: productId,
          name: item.product_name || item.name,
          reason: `Stock insuficiente (disponible: ${currentProduct.current_stock}, solicitado: ${requestedQuantity})`
        })
      } else {
        // Producto disponible
        availableItems.push({
          id: currentProduct.id,
          name: currentProduct.name,
          price: parseFloat(currentProduct.sale_price),
          image: currentProduct.image,
          quantity: requestedQuantity,
          current_stock: currentProduct.current_stock
        })
      }
    })

    // Agregar productos disponibles al carrito
    availableItems.forEach(product => {
      emit('add-to-cart', product)
    })

    // Mostrar resumen
    if (unavailableItems.length === 0) {
      reorderStatus.value.message = `¡Perfecto! Se agregaron ${availableItems.length} productos al carrito.`
    } else if (availableItems.length === 0) {
      reorderStatus.value.message = 'Ningún producto está disponible actualmente.'
      reorderStatus.value.unavailable = unavailableItems
    } else {
      reorderStatus.value.message = `Se agregaron ${availableItems.length} productos. ${unavailableItems.length} no están disponibles.`
      reorderStatus.value.unavailable = unavailableItems
    }

  } catch (error) {
    console.error('Error al verificar disponibilidad:', error)
    reorderStatus.value.message = 'Error al verificar la disponibilidad de productos. Se agregarán los productos sin verificar.'
    
    // Fallback: agregar todos los productos sin verificar
    invoice.items.forEach(item => {
      const product = {
        id: item.product_id || item.id,
        name: item.product_name || item.name,
        price: parseFloat(item.unit_price || item.price || 0),
        image: item.product_image || item.image,
        quantity: parseInt(item.quantity || 1)
      }
      emit('add-to-cart', product)
    })
  } finally {
    reorderStatus.value.loading = false
    
    // Auto-cerrar mensaje después de 8 segundos
    setTimeout(() => {
      reorderStatus.value.message = ''
      reorderStatus.value.unavailable = []
    }, 8000)
  }
}

// Lifecycle
onMounted(() => {
  if (props.customer?.id) {
    loadCustomerHistory()
  } else {
    error.value = 'Cliente no válido para cargar historial'
  }
})

// Watchers
watch(() => props.customer, () => {
  if (props.customer?.id) {
    loadCustomerHistory()
  }
}, { deep: true })
</script>

<style scoped>
/* Estilos específicos del modal */
.scrollbar-thin {
  scrollbar-width: thin;
}

.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>