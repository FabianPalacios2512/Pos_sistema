<template>
  <!-- Modal de Devoluciones -->
  <div v-if="show" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden border border-gray-200 dark:border-zinc-800">
      <!-- Header del Modal -->
      <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-8 pt-6 pb-5">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Procesar Devolución</h1>
            <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">
              Sigue los pasos para completar la devolución
            </p>
          </div>

          <!-- Botón de cerrar -->
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-white p-2.5 transition-all rounded-xl hover:bg-gray-100 dark:hover:bg-zinc-800 border border-transparent hover:border-gray-200 dark:hover:border-zinc-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <!-- Stepper profesional -->
        <div class="flex items-center justify-center">
          <!-- Paso 1 -->
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                 :class="currentStep >= 1 ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-gray-200 dark:bg-zinc-700 text-gray-500 dark:text-zinc-400'">
              <svg v-if="currentStep > 1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
              <span v-else>1</span>
            </div>
            <span class="text-sm font-semibold transition-colors" :class="currentStep >= 1 ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-500'">Buscar Factura</span>
          </div>
          
          <!-- Línea conectora -->
          <div class="w-16 h-[2px] mx-3 rounded-full transition-colors duration-300" :class="currentStep >= 2 ? 'bg-blue-600' : 'bg-gray-200 dark:bg-zinc-700'"></div>
          
          <!-- Paso 2 -->
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                 :class="currentStep >= 2 ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-gray-200 dark:bg-zinc-700 text-gray-500 dark:text-zinc-400'">
              <svg v-if="currentStep > 2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
              <span v-else>2</span>
            </div>
            <span class="text-sm font-semibold transition-colors" :class="currentStep >= 2 ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-500'">Seleccionar Items</span>
          </div>
          
          <!-- Línea conectora -->
          <div class="w-16 h-[2px] mx-3 rounded-full transition-colors duration-300" :class="currentStep >= 3 ? 'bg-blue-600' : 'bg-gray-200 dark:bg-zinc-700'"></div>
          
          <!-- Paso 3 -->
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                 :class="currentStep >= 3 ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-gray-200 dark:bg-zinc-700 text-gray-500 dark:text-zinc-400'">
              3
            </div>
            <span class="text-sm font-semibold transition-colors" :class="currentStep >= 3 ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-500'">Confirmar</span>
          </div>
        </div>
      </div>

      <!-- Contenido principal con scroll -->
      <div class="p-8 overflow-y-auto max-h-[calc(90vh-160px)]">
      <!-- Paso 1: Buscar Factura -->
      <div v-if="currentStep === 1" class="max-w-xl mx-auto pt-8">
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-blue-50 dark:bg-blue-950/50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-blue-100 dark:border-blue-900/40">
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
            Buscar Factura Original
          </h2>
          <p class="text-sm text-gray-500 dark:text-zinc-400">Ingresa el número de factura o escanea el QR</p>
        </div>
          
        <div class="space-y-4">
          <div>
            <div class="relative">
              <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="searchInvoiceNumber"
                type="text"
                placeholder="FAC-000123"
                class="w-full pl-12 pr-14 py-4 text-base font-medium border-2 border-gray-200 dark:border-zinc-700 rounded-2xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent focus:bg-white dark:focus:bg-zinc-800 shadow-sm transition-all duration-200"
                @keyup.enter="searchInvoice"
                :disabled="isLoading"
              >
              <button
                @click="startQRScanner"
                :disabled="isLoading"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-200 dark:hover:bg-zinc-700 rounded-xl transition-all"
                title="Escanear código QR"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"></path>
                </svg>
              </button>
            </div>
            
            <p class="text-xs text-gray-400 dark:text-zinc-500 text-center mt-2">Presiona <kbd class="px-1.5 py-0.5 bg-gray-100 dark:bg-zinc-800 rounded text-gray-500 dark:text-zinc-400 font-mono text-[10px] border border-gray-200 dark:border-zinc-700">Enter</kbd> para buscar</p>
              
              <!-- Modal de escáner QR -->
              <div v-if="showQRScanner" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-50">
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

      <!-- Paso 2: Mostrar Factura y Seleccionar Items -->
      <div v-if="currentStep === 2 && invoiceData" class="space-y-6">
        <!-- Información de la factura -->
        <div class="bg-white dark:bg-zinc-800/50 rounded-2xl border border-gray-200 dark:border-zinc-700/60 p-6">
          <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950/50 rounded-xl flex items-center justify-center border border-blue-100 dark:border-blue-900/40">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                  Factura {{ invoiceData.invoice.number }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-zinc-400">Selecciona los productos a devolver</p>
              </div>
            </div>
            <button
              @click="goToStep(1)"
              class="px-4 py-2 text-sm font-semibold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-xl transition-all border border-transparent hover:border-blue-100 dark:hover:border-blue-900/40"
            >
              Cambiar Factura
            </button>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
            <div class="bg-gray-50 dark:bg-zinc-800 px-4 py-3 rounded-xl border border-gray-200 dark:border-zinc-700">
              <label class="block text-[10px] font-semibold text-gray-500 dark:text-zinc-400 mb-1 uppercase tracking-wider">Cliente</label>
              <p class="font-bold text-gray-900 dark:text-white text-sm">{{ invoiceData.invoice.customer.name }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800 px-4 py-3 rounded-xl border border-gray-200 dark:border-zinc-700">
              <label class="block text-[10px] font-semibold text-gray-500 dark:text-zinc-400 mb-1 uppercase tracking-wider">Fecha</label>
              <p class="font-bold text-gray-900 dark:text-white text-sm">{{ formatDate(invoiceData.invoice.date) }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800 px-4 py-3 rounded-xl border border-gray-200 dark:border-zinc-700">
              <label class="block text-[10px] font-semibold text-gray-500 dark:text-zinc-400 mb-1 uppercase tracking-wider">Total Factura</label>
              <p class="font-bold text-gray-900 dark:text-white text-sm">${{ formatCurrency(invoiceData.invoice.total) }}</p>
            </div>
          </div>

          <!-- Productos de la factura -->
          <h3 class="text-[11px] font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">
            Productos a Devolver
          </h3>
          
          <div class="overflow-hidden border border-gray-200 dark:border-zinc-700 rounded-xl">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
              <thead class="bg-gray-50 dark:bg-zinc-800/80">
                <tr>
                  <th class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    Producto
                  </th>
                  <th class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    Original
                  </th>
                  <th class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    Disponible
                  </th>
                  <th class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    Devolver
                  </th>
                  <th class="px-5 py-3.5 text-right text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    Precio
                  </th>
                  <th class="px-5 py-3.5 text-right text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    Subtotal
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-800/30 divide-y divide-gray-100 dark:divide-zinc-700/50">
                <tr v-for="item in invoiceData.invoice.invoice_items" :key="item.id" 
                    class="transition-colors duration-150"
                    :class="(item.returnQuantity || 0) > 0 ? 'bg-blue-50/50 dark:bg-blue-950/20' : 'hover:bg-gray-50 dark:hover:bg-zinc-800/50'">
                  <td class="px-5 py-4">
                    <div>
                      <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ item.product?.name || item.product_name || 'Producto eliminado' }}</p>
                      <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ item.product?.sku || item.product_sku || '—' }}</p>
                    </div>
                  </td>
                  <td class="px-5 py-4 text-center">
                    <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">{{ item.quantity }}</span>
                  </td>
                  <td class="px-5 py-4 text-center">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ item.available_for_return }}</span>
                    <span v-if="item.returned_quantity > 0" class="block text-[10px] text-amber-600 dark:text-amber-400 font-medium mt-0.5">
                      {{ item.returned_quantity }} ya devueltas
                    </span>
                  </td>
                  <td class="px-5 py-4">
                    <div class="flex items-center justify-center gap-1">
                      <button
                        @click="item.returnQuantity = Math.max(0, (item.returnQuantity || 0) - 1); updateReturnTotals()"
                        :disabled="!item.returnQuantity || item.available_for_return <= 0"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 dark:text-zinc-400 bg-gray-100 dark:bg-zinc-700 hover:bg-gray-200 dark:hover:bg-zinc-600 disabled:opacity-30 disabled:cursor-not-allowed transition-all font-bold text-sm"
                      >−</button>
                      <span class="w-10 text-center text-sm font-bold" 
                            :class="(item.returnQuantity || 0) > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-zinc-500'">
                        {{ item.returnQuantity || 0 }}
                      </span>
                      <button
                        @click="item.returnQuantity = Math.min(item.available_for_return, (item.returnQuantity || 0) + 1); updateReturnTotals()"
                        :disabled="(item.returnQuantity || 0) >= item.available_for_return || item.available_for_return <= 0"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 dark:text-zinc-400 bg-gray-100 dark:bg-zinc-700 hover:bg-blue-100 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 disabled:opacity-30 disabled:cursor-not-allowed transition-all font-bold text-sm"
                      >+</button>
                    </div>
                  </td>
                  <td class="px-5 py-4 text-right text-sm font-medium text-gray-600 dark:text-zinc-300">
                    ${{ formatCurrency(item.unit_price) }}
                  </td>
                  <td class="px-5 py-4 text-right">
                    <span class="text-sm font-bold" :class="(item.returnQuantity || 0) > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-500'">
                      ${{ formatCurrency((item.returnQuantity || 0) * item.unit_price) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Totales de devolución -->
          <div class="mt-6 flex justify-end">
            <div class="w-80 bg-white dark:bg-zinc-800 rounded-xl p-5 border border-gray-200 dark:border-zinc-700 shadow-sm">
              <div class="flex justify-between items-center text-sm mb-2.5">
                <span class="text-gray-500 dark:text-zinc-400 font-medium">Subtotal:</span>
                <span class="font-semibold text-gray-900 dark:text-white">${{ formatCurrency(returnTotals.subtotal) }}</span>
              </div>
              <div class="flex justify-between items-center text-sm mb-3">
                <span class="text-gray-500 dark:text-zinc-400 font-medium">IVA:</span>
                <span class="font-semibold text-gray-900 dark:text-white">${{ formatCurrency(returnTotals.taxAmount) }}</span>
              </div>
              <div class="flex justify-between items-center pt-3 border-t-2 border-gray-200 dark:border-zinc-700">
                <span class="text-sm font-bold text-gray-900 dark:text-white">Total a Devolver:</span>
                <span class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">${{ formatCurrency(returnTotals.total) }}</span>
              </div>
            </div>
          </div>

          <!-- Botones de navegación -->
          <div class="flex justify-between mt-6">
            <button
              @click="goToStep(1)"
              class="px-5 py-2.5 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700 border border-gray-200 dark:border-zinc-700 rounded-xl font-semibold text-sm transition-all"
            >
              ← Volver
            </button>
            <button
              @click="goToStep(3)"
              :disabled="!hasSelectedItems"
              class="px-8 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:bg-gray-200 dark:disabled:bg-zinc-800 disabled:text-gray-400 dark:disabled:text-zinc-600 disabled:cursor-not-allowed text-white rounded-xl font-bold text-sm shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300"
            >
              Continuar →
            </button>
          </div>
        </div>
      </div>

      <!-- Paso 3: Confirmar Devolución -->
      <div v-if="currentStep === 3" class="max-w-5xl mx-auto">
        <div class="bg-white dark:bg-zinc-800/50 rounded-2xl border border-gray-200 dark:border-zinc-700/60 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-950/50 rounded-xl flex items-center justify-center border border-emerald-100 dark:border-emerald-900/40">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h2 class="text-xl font-bold text-gray-900 dark:text-white">Confirmar Devolución</h2>
              <p class="text-xs text-gray-500 dark:text-zinc-400">Revisa los datos y confirma</p>
            </div>
          </div>

          <!-- Grid de 2 columnas -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Columna Izquierda: Productos a Devolver -->
            <div>
              <h3 class="text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-3 uppercase tracking-wider">Productos a Devolver</h3>
              <div class="space-y-2 max-h-[260px] overflow-y-auto pr-1">
                <div v-for="item in selectedReturnItems" :key="item.product_id" 
                     class="flex justify-between items-center py-3 px-4 bg-gray-50 dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 transition-colors">
                  <div class="flex-1 min-w-0">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white block truncate">{{ item.product?.name || item.product_name || 'Producto eliminado' }}</span>
                    <span class="text-xs text-blue-600 dark:text-blue-400 font-bold">x{{ item.quantity }}</span>
                  </div>
                  <span class="text-sm font-bold text-gray-900 dark:text-white ml-3 tabular-nums">
                    ${{ formatCurrency(item.quantity * item.unit_price) }}
                  </span>
                </div>
              </div>
              
              <!-- Total destacado -->
              <div class="mt-4 bg-emerald-50 dark:bg-emerald-950/30 rounded-xl p-5 border border-emerald-200 dark:border-emerald-900/50">
                <div class="flex items-center justify-between">
                  <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Total a Reembolsar</p>
                  <p class="text-[2rem] font-extrabold text-emerald-600 dark:text-emerald-400 tabular-nums leading-none">
                    ${{ formatCurrency(returnTotals.total) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Columna Derecha: Formulario -->
            <div class="space-y-5">
              <div>
                <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-2 uppercase tracking-wider">
                  Motivo de la Devolución *
                </label>
                <textarea
                  v-model="returnReason"
                  rows="3"
                  placeholder="Describe el motivo..."
                  class="w-full px-4 py-3 text-sm border border-gray-200 dark:border-zinc-700 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent focus:bg-white dark:focus:bg-zinc-800 transition-all"
                  required
                ></textarea>
              </div>

              <div>
                <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-2 uppercase tracking-wider">
                  Método de Reembolso *
                </label>
                <select
                  v-model="refundMethod"
                  class="w-full px-4 py-3 text-sm border border-gray-200 dark:border-zinc-700 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all font-medium cursor-pointer"
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
                <label class="block text-[11px] font-bold text-gray-700 dark:text-zinc-300 mb-2 uppercase tracking-wider">
                  Notas Adicionales
                </label>
                <textarea
                  v-model="additionalNotes"
                  rows="2"
                  placeholder="Notas adicionales (opcional)..."
                  class="w-full px-4 py-3 text-sm border border-gray-200 dark:border-zinc-700 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent focus:bg-white dark:focus:bg-zinc-800 transition-all"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Botones de acción -->
          <div class="flex justify-between mt-8 pt-6 border-t border-gray-200 dark:border-zinc-700">
            <button
              @click="goToStep(2)"
              class="px-5 py-2.5 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700 border border-gray-200 dark:border-zinc-700 rounded-xl font-semibold text-sm transition-all"
            >
              ← Volver
            </button>
            <button
              @click="processReturn"
              :disabled="!canProcessReturn || isLoading"
              class="px-10 py-3 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 disabled:bg-gray-200 dark:disabled:bg-zinc-800 disabled:text-gray-400 dark:disabled:text-zinc-600 disabled:cursor-not-allowed text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all duration-300 flex items-center gap-2"
            >
              <svg v-if="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
              </svg>
              <span v-if="!isLoading">Procesar Devolución</span>
              <span v-else>Procesando...</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de éxito con acciones -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black/70 dark:bg-black/80 flex items-center justify-center z-50 animate-fade-in">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 border border-gray-200 dark:border-zinc-800 transform scale-100 animate-scale-in">
        <div class="text-center">
          <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-950/50 rounded-2xl flex items-center justify-center mx-auto mb-5 border border-emerald-100 dark:border-emerald-900/40">
            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Devolución Procesada</h3>
          <p class="text-sm text-gray-500 dark:text-zinc-400 mb-6">
            <strong class="text-gray-900 dark:text-white">{{ processedReturn?.number || 'sin número' }}</strong> procesada exitosamente.
          </p>
          
          <!-- Botones de acción -->
          <div class="grid grid-cols-2 gap-2.5 mb-4">
            <button
              @click="printReturn"
              class="px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold rounded-xl border border-gray-200 dark:border-zinc-700 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
              </svg>
              Imprimir
            </button>
            
            <button
              @click="downloadReturn"
              class="px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold rounded-xl border border-gray-200 dark:border-zinc-700 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
              </svg>
              Descargar
            </button>
          </div>
          
          <button
            @click="closeModalAndReset"
            class="w-full px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-lg shadow-emerald-600/30 transition-all duration-200"
          >
            Cerrar
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
import { appStore } from '../store/appStore.js'

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
// appStore se importa directamente desde ../store/appStore.js

// Estados reactivos
const currentStep = ref(1)
const isLoading = ref(false)
const searchInvoiceNumber = ref('')
const invoiceData = ref(null)
const returnReason = ref('')
const refundMethod = ref('')
const additionalNotes = ref('')
const showSuccessModal = ref(false)

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
    
    // Procesar la respuesta para parsear items JSON (igual que en ReturnsManagementView)
    let returnResponse = result.data
    if (returnResponse.items && typeof returnResponse.items === 'string') {
      try {
        returnResponse.items = JSON.parse(returnResponse.items)
      } catch (e) {
        console.error('Error parsing items JSON:', e)
        returnResponse.items = []
      }
    }
    
    // Si return_items no existe o no es array, usar items
    if (!Array.isArray(returnResponse.return_items)) {
      returnResponse.return_items = returnResponse.items || []
    }
    
    processedReturn.value = returnResponse
    
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