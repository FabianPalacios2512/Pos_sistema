<template>
  <!-- Modal de Devoluciones -->
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
      <!-- Header del Modal -->
      <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Devolucio</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Procesar devoluciones de productos vendidos
            </p>
          </div>

          <!-- Botón de cerrar -->
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <!-- Estados de proceso -->
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-2">
            <div class="flex items-center space-x-1">
              <div class="w-3 h-3 rounded-full" :class="currentStep >= 1 ? 'bg-blue-500' : 'bg-gray-300'"></div>
              <span class="text-sm text-gray-600 dark:text-gray-400">Buscar Factura</span>
            </div>
            <div class="w-8 h-0.5 bg-gray-300"></div>
            <div class="flex items-center space-x-1">
              <div class="w-3 h-3 rounded-full" :class="currentStep >= 2 ? 'bg-blue-500' : 'bg-gray-300'"></div>
              <span class="text-sm text-gray-600 dark:text-gray-400">Seleccionar Items</span>
            </div>
            <div class="w-8 h-0.5 bg-gray-300"></div>
            <div class="flex items-center space-x-1">
              <div class="w-3 h-3 rounded-full" :class="currentStep >= 3 ? 'bg-blue-500' : 'bg-gray-300'"></div>
              <span class="text-sm text-gray-600 dark:text-gray-400">Confirmar</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenido principal con scroll -->
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
      <!-- Paso 1: Buscar Factura -->
      <div v-if="currentStep === 1" class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Buscar Factura Original
          </h2>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Número de Factura
              </label>
              <div class="flex space-x-3">
                <input
                  v-model="searchInvoiceNumber"
                  type="text"
                  placeholder="Ej: FAC-000123 o escanee QR"
                  class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  @keyup.enter="searchInvoice"
                  :disabled="isLoading"
                >
                <button
                  @click="startQRScanner"
                  :disabled="isLoading"
                  class="px-4 py-3 bg-gray-600 hover:bg-gray-700 disabled:bg-gray-300 text-white rounded-lg font-medium transition-colors"
                  title="Escanear código QR de la factura"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h.01M12 12v4.01"></path>
                  </svg>
                </button>
                <button
                  @click="searchInvoice"
                  :disabled="isLoading || !searchInvoiceNumber.trim()"
                  class="px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded-lg font-medium transition-colors"
                >
                  <span v-if="!isLoading">Buscar</span>
                  <span v-else>Buscando...</span>
                </button>
              </div>
              
              <!-- Modal de escáner QR -->
              <div v-if="showQRScanner" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4">
                  <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Escanear QR de Factura</h3>
                    <button @click="closeQRScanner" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  <div class="text-center">
                    <div class="w-48 h-48 bg-gray-100 dark:bg-gray-700 rounded-lg mx-auto mb-4 flex items-center justify-center">
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
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
              Factura {{ invoiceData.invoice.number }}
            </h2>
            <button
              @click="goToStep(1)"
              class="text-blue-600 hover:text-blue-700 font-medium"
            >
              Cambiar Factura
            </button>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
              <label class="block text-sm text-gray-500 dark:text-gray-400">Cliente</label>
              <p class="font-medium text-gray-900 dark:text-white">{{ invoiceData.invoice.customer.name }}</p>
            </div>
            <div>
              <label class="block text-sm text-gray-500 dark:text-gray-400">Fecha</label>
              <p class="font-medium text-gray-900 dark:text-white">{{ formatDate(invoiceData.invoice.date) }}</p>
            </div>
            <div>
              <label class="block text-sm text-gray-500 dark:text-gray-400">Total</label>
              <p class="font-medium text-gray-900 dark:text-white">${{ formatCurrency(invoiceData.invoice.total) }}</p>
            </div>
          </div>

          <!-- Productos de la factura -->
          <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">
            Productos a Devolver
          </h3>
          
          <div class="overflow-hidden border border-gray-200 dark:border-gray-600 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Producto
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Cant. Original
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Disponible
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Devolver
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Precio Unit.
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Subtotal
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
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
                      class="w-20 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
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
          <div class="mt-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <div class="flex justify-between items-center text-sm">
              <span class="text-gray-600 dark:text-gray-300">Subtotal:</span>
              <span class="font-medium text-gray-900 dark:text-white">${{ formatCurrency(returnTotals.subtotal) }}</span>
            </div>
            <div class="flex justify-between items-center text-sm mt-1">
              <span class="text-gray-600 dark:text-gray-300">IVA:</span>
              <span class="font-medium text-gray-900 dark:text-white">${{ formatCurrency(returnTotals.taxAmount) }}</span>
            </div>
            <div class="flex justify-between items-center text-lg font-bold mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
              <span class="text-gray-900 dark:text-white">Total a Devolver:</span>
              <span class="text-gray-900 dark:text-white">${{ formatCurrency(returnTotals.total) }}</span>
            </div>
          </div>

          <!-- Botones de navegación -->
          <div class="flex justify-between mt-6">
            <button
              @click="goToStep(1)"
              class="px-6 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition-colors"
            >
              ← Volver
            </button>
            <button
              @click="goToStep(3)"
              :disabled="!hasSelectedItems"
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded-lg font-medium transition-colors"
            >
              Continuar →
            </button>
          </div>
        </div>
      </div>

      <!-- Paso 3: Confirmar Devolución -->
      <div v-if="currentStep === 3" class="max-w-2xl mx-auto space-y-6">
        <!-- Resumen de devolución -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Confirmar Devolución
          </h2>

          <!-- Detalles de productos seleccionados -->
          <div class="mb-6">
            <h3 class="text-md font-medium text-gray-900 dark:text-white mb-3">Productos a Devolver</h3>
            <div class="space-y-2">
              <div v-for="item in selectedReturnItems" :key="item.product_id" 
                   class="flex justify-between items-center py-2 px-3 bg-gray-50 dark:bg-gray-700 rounded">
                <div>
                  <span class="font-medium text-gray-900 dark:text-white">{{ item.product.name }}</span>
                  <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">x{{ item.quantity }}</span>
                </div>
                <span class="font-medium text-gray-900 dark:text-white">
                  ${{ formatCurrency(item.quantity * item.unit_price) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Información de devolución -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Motivo de la Devolución *
              </label>
              <textarea
                v-model="returnReason"
                rows="3"
                placeholder="Describe el motivo de la devolución..."
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Método de Reembolso *
              </label>
              <select
                v-model="refundMethod"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Notas Adicionales
              </label>
              <textarea
                v-model="additionalNotes"
                rows="2"
                placeholder="Notas adicionales (opcional)..."
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              ></textarea>
            </div>
          </div>

          <!-- Total final -->
          <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
            <div class="text-center">
              <p class="text-sm text-blue-600 dark:text-blue-400">Total a Reembolsar</p>
              <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                ${{ formatCurrency(returnTotals.total) }}
              </p>
            </div>
          </div>

          <!-- Botones de acción -->
          <div class="flex justify-between mt-6">
            <button
              @click="goToStep(2)"
              class="px-6 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition-colors"
            >
              ← Volver
            </button>
            <button
              @click="processReturn"
              :disabled="!canProcessReturn || isLoading"
              class="px-8 py-3 bg-red-600 hover:bg-red-700 disabled:bg-gray-300 text-white rounded-lg font-medium transition-colors"
            >
              <span v-if="!isLoading">Procesar Devolución</span>
              <span v-else>Procesando...</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de éxito -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
        <div class="text-center">
          <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">¡Devolución Procesada!</h3>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            La devolución {{ processedReturn?.number || 'sin número' }} ha sido procesada exitosamente.
          </p>
          <div class="flex space-x-3">
            <button
              @click="closeModalAndReset"
              class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors"
            >
              Aceptar
            </button>
          </div>
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

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['close', 'success'])

const { showSuccess, showError, showInfo } = useToast()

// Estados reactivos
const currentStep = ref(1)
const isLoading = ref(false)
const searchInvoiceNumber = ref('')
const invoiceData = ref(null)
const returnReason = ref('')
const refundMethod = ref('')
const additionalNotes = ref('')
const showSuccessModal = ref(false)
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
    showSuccess('Factura encontrada exitosamente')
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

const printReturn = () => {
  // TODO: Implementar impresión de devolución
  showInfo('Función de impresión en desarrollo')
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