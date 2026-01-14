<template>
  <!-- Modal de Devoluciones -->
  <div v-if="show" class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden border border-gray-200 dark:border-zinc-800">
      <!-- Header del Modal -->
      <div class="bg-gray-50 dark:bg-zinc-900 shadow-sm border-b border-gray-200 dark:border-zinc-800 p-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Devoluciones</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Procesar devoluciones de productos vendidos
            </p>
          </div>

          <!-- Botón de cerrar -->
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-white p-2 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <!-- Estados de proceso -->
        <div class="flex items-center space-x-4 mt-4">
          <div class="flex items-center space-x-2">
            <div class="flex items-center space-x-1">
              <div class="w-3 h-3 rounded-full transition-colors" :class="currentStep >= 1 ? 'bg-blue-600 dark:bg-blue-500' : 'bg-gray-300 dark:bg-zinc-700'"></div>
              <span class="text-sm font-medium" :class="currentStep >= 1 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-zinc-500'">Buscar Factura</span>
            </div>
            <div class="w-8 h-0.5" :class="currentStep >= 2 ? 'bg-blue-600 dark:bg-blue-500' : 'bg-gray-300 dark:bg-zinc-700'"></div>
            <div class="flex items-center space-x-1">
              <div class="w-3 h-3 rounded-full transition-colors" :class="currentStep >= 2 ? 'bg-blue-600 dark:bg-blue-500' : 'bg-gray-300 dark:bg-zinc-700'"></div>
              <span class="text-sm font-medium" :class="currentStep >= 2 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-zinc-500'">Seleccionar Items</span>
            </div>
            <div class="w-8 h-0.5" :class="currentStep >= 3 ? 'bg-blue-600 dark:bg-blue-500' : 'bg-gray-300 dark:bg-zinc-700'"></div>
            <div class="flex items-center space-x-1">
              <div class="w-3 h-3 rounded-full transition-colors" :class="currentStep >= 3 ? 'bg-blue-600 dark:bg-blue-500' : 'bg-gray-300 dark:bg-zinc-700'"></div>
              <span class="text-sm font-medium" :class="currentStep >= 3 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-zinc-500'">Confirmar</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenido principal con scroll -->
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
      <!-- Paso 1: Buscar Factura -->
      <div v-if="currentStep === 1" class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Buscar Factura Original
          </h2>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                Número de Factura
              </label>
              <div class="flex space-x-3">
                <input
                  v-model="searchInvoiceNumber"
                  type="text"
                  placeholder="Ej: FAC-000123 o escanee QR"
                  class="flex-1 px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-xl bg-white dark:bg-zinc-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all"
                  @keyup.enter="searchInvoice"
                  :disabled="isLoading"
                >
                <button
                  @click="startQRScanner"
                  :disabled="isLoading"
                  class="px-4 py-3 bg-slate-600 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:text-gray-500 text-white rounded-xl font-medium transition-all duration-200 shadow-sm"
                  title="Escanear código QR de la factura"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h.01M12 12v4.01"></path>
                  </svg>
                </button>
                <button
                  @click="searchInvoice"
                  :disabled="isLoading || !searchInvoiceNumber.trim()"
                  class="px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:text-gray-500 text-white rounded-xl font-bold transition-all duration-200 shadow-lg"
                >
                  <span v-if="!isLoading">Buscar</span>
                  <span v-else>Buscando...</span>
                </button>
              </div>
              
              <!-- Modal de escáner QR -->
              <div v-if="showQRScanner" class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center z-50">
                <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 max-w-md w-full mx-4 border border-gray-200 dark:border-zinc-800 shadow-2xl">
                  <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Escanear QR de Factura</h3>
                    <button @click="closeQRScanner" class="text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-white transition-colors rounded-lg p-1 hover:bg-gray-100 dark:hover:bg-zinc-800">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  <div class="text-center">
                    <div class="w-48 h-48 bg-gray-50 dark:bg-zinc-800 rounded-xl mx-auto mb-4 flex items-center justify-center border border-gray-200 dark:border-zinc-700">
                      <div v-if="!qrResult" class="text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h.01M12 12v4.01"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Esperando código QR...</p>
                      </div>
                      <div v-else class="text-center">
                        <svg class="w-12 h-12 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-sm text-gray-900 dark:text-white font-medium">{{ qrResult }}</p>
                      </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                      Apunte la cámara hacia el código QR de la factura
                    </p>
                    <button
                      @click="useQRResult"
                      :disabled="!qrResult"
                      class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded-lg font-medium transition-colors"
                    >
                      Usar este número
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Paso 2: Mostrar Factura y Seleccionar Items -->
      <div v-if="currentStep === 2 && invoiceData" class="space-y-6">
        <!-- Información de la factura -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
              Factura {{ invoiceData.invoice.number }}
            </h2>
            <button
              @click="goToStep(1)"
              class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors"
            >
              Cambiar Factura
            </button>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-50 dark:bg-zinc-900 p-3 rounded-xl border border-gray-200 dark:border-zinc-700">
              <label class="block text-xs font-medium text-gray-500 dark:text-zinc-400 mb-1">Cliente</label>
              <p class="font-semibold text-gray-900 dark:text-white">{{ invoiceData.invoice.customer.name }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-900 p-3 rounded-xl border border-gray-200 dark:border-zinc-700">
              <label class="block text-xs font-medium text-gray-500 dark:text-zinc-400 mb-1">Fecha</label>
              <p class="font-semibold text-gray-900 dark:text-white">{{ formatDate(invoiceData.invoice.date) }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-900 p-3 rounded-xl border border-gray-200 dark:border-zinc-700">
              <label class="block text-xs font-medium text-gray-500 dark:text-zinc-400 mb-1">Total</label>
              <p class="font-semibold text-gray-900 dark:text-white">${{ formatCurrency(invoiceData.invoice.total) }}</p>
            </div>
          </div>

          <!-- Productos de la factura -->
          <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">
            Productos a Devolver
          </h3>
          
          <div class="overflow-hidden border border-gray-200 dark:border-zinc-700 rounded-xl">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
              <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    Producto
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    Cant. Original
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    Disponible
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    Devolver
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    Precio Unit.
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    Subtotal
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                <tr v-for="item in invoiceData.invoice.invoice_items" :key="item.id">
                  <td class="px-4 py-3">
                    <div>
                      <p class="font-medium text-gray-900 dark:text-white">{{ item.product.name }}</p>
                      <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.product.sku }}</p>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-gray-900 dark:text-white">
                    {{ item.quantity }}
                  </td>
                  <td class="px-4 py-3">
                    <span class="text-gray-900 dark:text-white">{{ item.available_for_return }}</span>
                    <span v-if="item.returned_quantity > 0" class="text-xs text-orange-600 ml-2">
                      ({{ item.returned_quantity }} devueltas)
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <input
                      v-model.number="item.returnQuantity"
                      type="number"
                      :min="0"
                      :max="item.available_for_return"
                      class="w-20 px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-gray-900 dark:text-white text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 transition-all"
                      :disabled="item.available_for_return <= 0"
                      @input="updateReturnTotals"
                    >
                  </td>
                  <td class="px-4 py-3 text-gray-900 dark:text-white">
                    ${{ formatCurrency(item.unit_price) }}
                  </td>
                  <td class="px-4 py-3 text-gray-900 dark:text-white">
                    ${{ formatCurrency((item.returnQuantity || 0) * item.unit_price) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Totales de devolución -->
          <div class="mt-6 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30 rounded-xl p-5 border border-blue-100 dark:border-blue-900/50">
            <div class="flex justify-between items-center text-sm mb-2">
              <span class="text-gray-600 dark:text-zinc-400 font-medium">Subtotal:</span>
              <span class="font-semibold text-gray-900 dark:text-white">${{ formatCurrency(returnTotals.subtotal) }}</span>
            </div>
            <div class="flex justify-between items-center text-sm mb-3">
              <span class="text-gray-600 dark:text-zinc-400 font-medium">IVA:</span>
              <span class="font-semibold text-gray-900 dark:text-white">${{ formatCurrency(returnTotals.taxAmount) }}</span>
            </div>
            <div class="flex justify-between items-center text-xl font-bold pt-3 border-t-2 border-blue-200 dark:border-blue-900">
              <span class="text-gray-900 dark:text-white">Total a Devolver:</span>
              <span class="text-blue-600 dark:text-blue-400">${{ formatCurrency(returnTotals.total) }}</span>
            </div>
          </div>

          <!-- Botones de navegación -->
          <div class="flex justify-between mt-6">
            <button
              @click="goToStep(1)"
              class="px-6 py-3 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-xl font-semibold transition-all hover:shadow-md"
            >
              ← Volver
            </button>
            <button
              @click="goToStep(3)"
              :disabled="!hasSelectedItems"
              class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-300 disabled:cursor-not-allowed text-white rounded-xl font-bold shadow-lg transition-all hover:scale-105 disabled:hover:scale-100"
            >
              Continuar →
            </button>
          </div>
        </div>
      </div>

      <!-- Paso 3: Confirmar Devolución - Diseño Compacto Horizontal -->
      <div v-if="currentStep === 3" class="max-w-5xl mx-auto">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-xl border border-gray-200 dark:border-zinc-700 p-6">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
            Confirmar Devolución
          </h2>

          <!-- Grid de 2 columnas: Izquierda (Productos) | Derecha (Formulario) -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Columna Izquierda: Productos a Devolver -->
            <div>
              <h3 class="text-sm font-bold text-gray-700 dark:text-zinc-300 mb-3 uppercase tracking-wide">Productos a Devolver</h3>
              <div class="space-y-2 max-h-[280px] overflow-y-auto pr-2">
                <div v-for="item in selectedReturnItems" :key="item.product_id" 
                     class="flex justify-between items-center py-2.5 px-3 bg-gray-50 dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-700">
                  <div class="flex-1 min-w-0">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white block truncate">{{ item.product.name }}</span>
                    <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">x{{ item.quantity }}</span>
                  </div>
                  <span class="text-sm font-bold text-gray-900 dark:text-white ml-3">
                    ${{ formatCurrency(item.quantity * item.unit_price) }}
                  </span>
                </div>
              </div>
              
              <!-- Total destacado -->
              <div class="mt-4 bg-blue-50 dark:bg-blue-950/40 rounded-xl p-4 border border-blue-200 dark:border-blue-900">
                <div class="flex items-center justify-between">
                  <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide">Total a Reembolsar</p>
                  <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                    ${{ formatCurrency(returnTotals.total) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Columna Derecha: Formulario de Devolución -->
            <div class="space-y-4">
              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2 uppercase tracking-wide">
                  Motivo de la Devolución *
                </label>
                <textarea
                  v-model="returnReason"
                  rows="3"
                  placeholder="Describe el motivo de la devolución..."
                  class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all"
                  required
                ></textarea>
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2 uppercase tracking-wide">
                  Método de Reembolso *
                </label>
                <select
                  v-model="refundMethod"
                  class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all font-medium"
                  required
                >
                  <option value="">Seleccionar método</option>
                  <option value="cash">Efectivo</option>
                  <option value="card">Tarjeta</option>
                  <option value="transfer">Transferencia</option>
                  <option value="store_credit">Crédito en Tienda</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2 uppercase tracking-wide">
                  Notas Adicionales
                </label>
                <textarea
                  v-model="additionalNotes"
                  rows="2"
                  placeholder="Notas adicionales (opcional)..."
                  class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent transition-all"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Botones de acción -->
          <div class="flex justify-between mt-6">
            <button
              @click="goToStep(2)"
              class="px-6 py-3 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-xl font-semibold transition-all hover:shadow-md"
            >
              ← Volver
            </button>
            <button
              @click="processReturn"
              :disabled="!canProcessReturn || isLoading"
              class="px-8 py-3 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 disabled:from-gray-300 disabled:to-gray-300 disabled:cursor-not-allowed text-white rounded-xl font-bold shadow-lg transition-all hover:scale-105 disabled:hover:scale-100"
            >
              <span v-if="!isLoading">Procesar Devolución</span>
              <span v-else>Procesando...</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de éxito con acciones -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black/70 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 animate-fade-in">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-lg w-full mx-4 p-8 border border-gray-200 dark:border-zinc-700 transform scale-100 animate-scale-in">
        <div class="text-center">
          <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-emerald-500 dark:from-green-500 dark:to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">¡Devolución Procesada!</h3>
          <p class="text-gray-600 dark:text-zinc-400 mb-6 leading-relaxed">
            La devolución <strong class="text-gray-900 dark:text-white">{{ processedReturn?.number || 'sin número' }}</strong> ha sido procesada exitosamente.
          </p>
          
          <!-- Botones de acción -->
          <div class="grid grid-cols-2 gap-3 mb-4">
            <button
              @click="printReturn"
              class="px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-700 shadow-sm transition-all duration-200 flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
              </svg>
              Imprimir
            </button>
            
            <button
              @click="downloadReturn"
              class="px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-700 shadow-sm transition-all duration-200 flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
              </svg>
              Descargar
            </button>
            
            <button
              @click="requestPhone"
              class="px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-700 shadow-sm transition-all duration-200 flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
              </svg>
              WhatsApp
            </button>
            
            <button
              @click="requestEmail"
              class="px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-700 shadow-sm transition-all duration-200 flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              Email
            </button>
          </div>
          
          <button
            @click="closeModalAndReset"
            class="w-full px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white rounded-xl font-bold shadow-lg transition-all hover:scale-105"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
    
    <!-- Modal solicitar teléfono -->
    <div v-if="showPhoneModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-[60] p-4">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200 dark:border-zinc-800">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Enviar por WhatsApp</h3>
        <p class="text-sm text-gray-600 dark:text-zinc-400 mb-4">Ingresa el número de teléfono del cliente:</p>
        <input
          v-model="phoneNumber"
          type="tel"
          placeholder="Ej: +57 300 1234567"
          class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 mb-4"
          @keyup.enter="sendByWhatsApp"
        />
        <div class="flex gap-3">
          <button
            @click="showPhoneModal = false; phoneNumber = ''"
            class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-xl font-semibold transition-all"
          >
            Cancelar
          </button>
          <button
            @click="sendByWhatsApp"
            :disabled="!phoneNumber.trim()"
            class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white rounded-xl font-semibold transition-all"
          >
            Enviar
          </button>
        </div>
      </div>
    </div>
    
    <!-- Modal solicitar email -->
    <div v-if="showEmailModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-[60] p-4">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200 dark:border-zinc-800">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Enviar por Email</h3>
        <p class="text-sm text-gray-600 dark:text-zinc-400 mb-4">Ingresa el correo electrónico del cliente:</p>
        <input
          v-model="emailAddress"
          type="email"
          placeholder="cliente@ejemplo.com"
          class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 mb-4"
          @keyup.enter="sendByEmail"
        />
        <div class="flex gap-3">
          <button
            @click="showEmailModal = false; emailAddress = ''"
            class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-xl font-semibold transition-all"
          >
            Cancelar
          </button>
          <button
            @click="sendByEmail"
            :disabled="!emailAddress.trim()"
            class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white rounded-xl font-semibold transition-all"
          >
            Enviar
          </button>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { useToast } from '../composables/useToast.js'
import returnsService from '../services/returnsService.js'
import { generateReturnPDF, downloadPDF as downloadPDFHelper, getPDFBlob } from '../utils/pdfTemplates/pdfGenerator.js'
import { whatsappService } from '../services/whatsappService.js'
import { useAppStore } from '../stores/app.js'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  preloadInvoiceNumber: {
    type: String,
    default: ''
  }
})

// Emits
const emit = defineEmits(['close', 'success'])

const { showSuccess, showError, showInfo } = useToast()
const appStore = useAppStore()

// Estados reactivos
const currentStep = ref(1)
const isLoading = ref(false)
const searchInvoiceNumber = ref('')
const invoiceData = ref(null)
const returnReason = ref('')
const refundMethod = ref('')
const additionalNotes = ref('')
const showSuccessModal = ref(false)

// Estados para modales de envío
const showPhoneModal = ref(false)
const showEmailModal = ref(false)
const phoneNumber = ref('')
const emailAddress = ref('')

// Watcher para precargar número de factura
watch(() => props.preloadInvoiceNumber, (newValue) => {
  if (newValue && props.show) {
    searchInvoiceNumber.value = newValue
    // Buscar automáticamente después de un pequeño delay
    nextTick(() => {
      setTimeout(() => {
        searchInvoice()
      }, 500)
    })
  }
}, { immediate: true })

// También resetear cuando se cierra el modal
watch(() => props.show, (newValue) => {
  if (!newValue) {
    // Limpiar datos cuando se cierra
    setTimeout(() => {
      currentStep.value = 1
      searchInvoiceNumber.value = ''
      invoiceData.value = null
      returnReason.value = ''
      refundMethod.value = ''
      additionalNotes.value = ''
    }, 300)
  } else if (props.preloadInvoiceNumber) {
    // Precargar cuando se abre con número
    searchInvoiceNumber.value = props.preloadInvoiceNumber
    nextTick(() => {
      setTimeout(() => {
        searchInvoice()
      }, 500)
    })
  }
})
const processedReturn = ref(null)

// Variables para escáner QR
const showQRScanner = ref(false)
const qrResult = ref('')

// Computadas
const hasSelectedItems = computed(() => {
  if (!invoiceData.value) return false
  return invoiceData.value.invoice.invoice_items.some(item => (item.returnQuantity || 0) > 0)
})

const selectedReturnItems = computed(() => {
  if (!invoiceData.value) return []
  return invoiceData.value.invoice.invoice_items
    .filter(item => (item.returnQuantity || 0) > 0)
    .map(item => ({
      product_id: item.product_id,
      quantity: item.returnQuantity,
      unit_price: item.unit_price,
      product: item.product
    }))
})

const returnTotals = ref({
  subtotal: 0,
  taxAmount: 0,
  total: 0
})

const canProcessReturn = computed(() => {
  return hasSelectedItems.value && 
         returnReason.value.trim() && 
         refundMethod.value
})

// Métodos
const searchInvoice = async () => {
  if (!searchInvoiceNumber.value.trim()) return
  
  try {
    isLoading.value = true
    const result = await returnsService.searchInvoice(searchInvoiceNumber.value.trim())
    
    invoiceData.value = result.data
    
    // Inicializar cantidades de devolución
    invoiceData.value.invoice.invoice_items.forEach(item => {
      item.returnQuantity = 0
    })
    
    currentStep.value = 2
    // showSuccess('Factura encontrada exitosamente') // Solo mostrar mensajes de error
  } catch (error) {
    showError(error.message)
  } finally {
    isLoading.value = false
  }
}

// Funciones para escáner QR
const startQRScanner = () => {
  showQRScanner.value = true
  qrResult.value = ''
  
  // Simular detección de QR - En una implementación real usarías una librería como ZXing
  // Por ahora, permitimos entrada manual o simulamos el escaneo
  setTimeout(() => {
    // Simular que se detectó un QR (esto sería reemplazado por un escáner real)
    const simulatedQR = prompt('Ingrese el número de factura del QR (simulación):')
    if (simulatedQR) {
      qrResult.value = simulatedQR.trim()
    }
  }, 1000)
}

const closeQRScanner = () => {
  showQRScanner.value = false
  qrResult.value = ''
}

const useQRResult = () => {
  if (qrResult.value) {
    searchInvoiceNumber.value = qrResult.value
    closeQRScanner()
    searchInvoice()
  }
}

const updateReturnTotals = () => {
  if (!invoiceData.value || !invoiceData.value.invoice) {
    returnTotals.value = { subtotal: 0, taxAmount: 0, total: 0 }
    return
  }
  
  try {
    const totals = returnsService.calculateReturnTotals(
      selectedReturnItems.value,
      invoiceData.value.invoice
    )
    returnTotals.value = totals
  } catch (error) {
    console.error('Error calculating return totals:', error)
    returnTotals.value = { subtotal: 0, taxAmount: 0, total: 0 }
  }
}

const goToStep = (step) => {
  if (step === 1) {
    // Reset
    invoiceData.value = null
    searchInvoiceNumber.value = ''
    returnReason.value = ''
    refundMethod.value = ''
    additionalNotes.value = ''
  }
  currentStep.value = step
}

const processReturn = async () => {
  try {
    isLoading.value = true
    
    // Validar items seleccionados
    const validation = returnsService.validateReturnItems(
      selectedReturnItems.value,
      invoiceData.value.invoice
    )
    
    if (!validation.isValid) {
      showError(validation.errors.join(', '))
      return
    }
    
    // Formatear datos para enviar
    const returnData = returnsService.formatReturnData(
      invoiceData.value,
      selectedReturnItems.value,
      {
        reason: returnReason.value,
        refundMethod: refundMethod.value,
        notes: additionalNotes.value
      }
    )
    
    const result = await returnsService.createReturn(returnData)
    
    console.log('Resultado de devolución:', result.data)
    processedReturn.value = result.data
    
    // Esperar un tick para asegurar que los datos se actualicen
    await nextTick()
    console.log('processedReturn después de asignar:', processedReturn.value)
    showSuccessModal.value = true
    
    // NO emitir success aquí, lo haremos desde el modal de éxito
    
  } catch (error) {
    if (error.type === 'cash_session_required') {
      showError(error.message + ' Por favor, abre una caja antes de procesar devoluciones.')
    } else {
      showError(error.message)
    }
  } finally {
    isLoading.value = false
  }
}

const printReturn = async () => {
  try {
    if (!processedReturn.value) {
      showError('No hay datos de devolución para imprimir')
      return
    }

    showInfo('Generando PDF para imprimir...')
    
    // Generar el PDF con los datos del sistema
    const pdfDoc = await generateReturnPDF(processedReturn.value, appStore.systemSettings)
    const pdfBlob = await getPDFBlob(pdfDoc)
    
    // Crear URL del blob y abrir ventana de impresión
    const blobUrl = URL.createObjectURL(pdfBlob)
    const printWindow = window.open(blobUrl, '_blank')
    
    if (printWindow) {
      printWindow.onload = () => {
        printWindow.print()
        URL.revokeObjectURL(blobUrl)
      }
      showSuccess('Documento preparado para imprimir')
    } else {
      showError('No se pudo abrir la ventana de impresión. Verifica los permisos del navegador.')
    }
  } catch (error) {
    console.error('Error al imprimir devolución:', error)
    showError('Error al preparar el documento para imprimir')
  }
}

const downloadReturn = async () => {
  try {
    if (!processedReturn.value) {
      showError('No hay datos de devolución para descargar')
      return
    }

    showInfo('Generando PDF...')
    
    const pdfDoc = await generateReturnPDF(processedReturn.value, appStore.systemSettings)
    const fileName = `Devolucion_${processedReturn.value.number || 'SN'}.pdf`
    
    await downloadPDFHelper(pdfDoc, fileName)
    showSuccess('PDF descargado exitosamente')
  } catch (error) {
    console.error('Error al descargar devolución:', error)
    showError('Error al generar el PDF')
  }
}

const requestPhone = () => {
  // Pre-cargar teléfono del cliente si existe
  if (invoiceData.value?.customer?.phone) {
    phoneNumber.value = invoiceData.value.customer.phone
  }
  showPhoneModal.value = true
}

const sendByWhatsApp = async () => {
  try {
    if (!phoneNumber.value.trim()) {
      showError('Por favor ingresa un número de teléfono')
      return
    }

    if (!processedReturn.value) {
      showError('No hay datos de devolución para enviar')
      return
    }

    showInfo('Generando PDF y enviando por WhatsApp...')
    showPhoneModal.value = false
    
    // Generar PDF
    const pdfDoc = await generateReturnPDF(processedReturn.value, appStore.systemSettings)
    const pdfBlob = await getPDFBlob(pdfDoc)
    
    // Enviar por WhatsApp
    const docNumber = processedReturn.value.number || 'SN'
    await whatsappService.sendDocumentByWhatsApp(
      phoneNumber.value,
      pdfBlob,
      docNumber,
      'devolucion'
    )
    
    showSuccess('Devolución enviada por WhatsApp exitosamente')
    phoneNumber.value = ''
  } catch (error) {
    console.error('Error al enviar por WhatsApp:', error)
    showError(error.message || 'Error al enviar por WhatsApp')
  }
}

const requestEmail = () => {
  // Pre-cargar email del cliente si existe
  if (invoiceData.value?.customer?.email) {
    emailAddress.value = invoiceData.value.customer.email
  }
  showEmailModal.value = true
}

const sendByEmail = async () => {
  try {
    if (!emailAddress.value.trim()) {
      showError('Por favor ingresa un correo electrónico')
      return
    }

    if (!processedReturn.value) {
      showError('No hay datos de devolución para enviar')
      return
    }

    showInfo('Enviando devolución por email...')
    showEmailModal.value = false
    
    // Generar PDF
    const pdfDoc = await generateReturnPDF(processedReturn.value, appStore.systemSettings)
    const pdfBlob = await getPDFBlob(pdfDoc)
    
    const returnNumber = processedReturn.value.number || 'SN'
    
    // Crear FormData para enviar
    const formData = new FormData()
    formData.append('email', emailAddress.value)
    formData.append('subject', `Nota de Devolución #${returnNumber}`)
    formData.append('message', `
      <h2>Nota de Devolución #${returnNumber}</h2>
      <p>Estimado cliente,</p>
      <p>Adjunto encontrarás el comprobante de tu devolución.</p>
      <p><strong>Total reembolsado:</strong> $${parseFloat(processedReturn.value.total || 0).toLocaleString('es-CO')}</p>
      <p><strong>Método de reembolso:</strong> ${processedReturn.value.refund_method || 'Efectivo'}</p>
      <br>
      <p>Gracias por tu confianza.</p>
    `)
    formData.append('pdf', pdfBlob, `Devolucion_${returnNumber}.pdf`)
    
    // Enviar por email usando el endpoint de Laravel
    const response = await fetch('/api/email/send-invoice', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    })
    
    const result = await response.json()
    
    if (!response.ok || !result.success) {
      throw new Error(result.message || 'Error al enviar el email')
    }
    
    showSuccess('Devolución enviada por email exitosamente')
    emailAddress.value = ''
  } catch (error) {
    console.error('Error al enviar por email:', error)
    showError(error.message || 'Error al enviar por email')
  }
}

const startNewReturn = () => {
  showSuccessModal.value = false
  processedReturn.value = null
  goToStep(1)
}

const closeModalAndReset = () => {
  // Guardar datos antes de resetear
  const returnData = processedReturn.value
  
  // Cerrar modal de éxito
  showSuccessModal.value = false
  
  // Resetear todo el componente
  currentStep.value = 1
  invoiceData.value = null
  searchInvoiceNumber.value = ''
  returnReason.value = ''
  refundMethod.value = ''
  additionalNotes.value = ''
  returnTotals.value = { subtotal: 0, taxAmount: 0, total: 0 }
  processedReturn.value = null
  
  // Emitir evento de éxito al componente padre para cerrar el modal principal
  emit('success', returnData)
}

// Utilidades
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-CO')
}

const formatCurrency = (amount) => {
  return Number(amount).toLocaleString('es-CO', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

// Watchers
watch(selectedReturnItems, () => {
  updateReturnTotals()
}, { deep: true })

// Reset component when modal opens
watch(() => props.show, (newValue) => {
  if (newValue) {
    // Reset everything when modal opens
    currentStep.value = 1
    invoiceData.value = null
    searchInvoiceNumber.value = ''
    returnReason.value = ''
    refundMethod.value = ''
    additionalNotes.value = ''
    returnTotals.value = { subtotal: 0, taxAmount: 0, total: 0 }
    showSuccessModal.value = false
    processedReturn.value = null
    isLoading.value = false
  }
})
</script>

<style scoped>
/* Estilos específicos del componente */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>