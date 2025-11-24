<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[60] p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto border border-gray-200">
      <!-- Header Compacto -->
      <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 sticky top-0 z-10">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-gray-900">Cierre de Caja</h3>
              <p class="text-xs text-gray-500">Finalizar sesión y realizar arqueo</p>
            </div>
          </div>
          <button 
            @click="$emit('close')" 
            class="text-gray-400 hover:text-gray-600 transition-colors min-w-[32px] min-h-[32px] flex items-center justify-center"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Body -->
      <div class="p-6 space-y-4">
        <!-- Grid horizontal de información de sesión -->
        <div class="grid grid-cols-4 gap-3">
          <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Usuario</p>
            <p class="text-sm font-semibold text-gray-900 truncate">{{ sessionData?.user?.name || 'Usuario' }}</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Apertura</p>
            <p class="text-sm font-semibold text-gray-900">{{ formatDate(sessionData?.opening_date) }}</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Hora</p>
            <p class="text-sm font-semibold text-gray-900">{{ formatTime(sessionData?.opening_time) }}</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Monto Inicial</p>
            <p class="text-sm font-bold text-green-700">{{ formatCurrency(sessionData?.opening_amount) }}</p>
          </div>
        </div>

        <!-- Resumen de ventas en grid horizontal -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
          <h4 class="text-sm font-bold text-gray-900 mb-3">Resumen de Ventas del Día</h4>
          <div class="grid grid-cols-4 gap-3">
            <div class="text-center p-3 rounded-lg border border-gray-200" style="background-color: #EBF2FF;">
              <p class="text-xs font-medium text-gray-500 mb-1">Total Ventas</p>
              <p class="text-xl font-bold text-gray-900">{{ formatCurrency(sessionData?.total_sales) }}</p>
            </div>
            <div class="text-center p-3 rounded-lg border border-gray-200" style="background-color: #E6FFF1;">
              <p class="text-xs font-medium text-gray-500 mb-1">Efectivo</p>
              <p class="text-xl font-bold text-gray-900">{{ formatCurrency(sessionData?.cash_sales) }}</p>
            </div>
            <div class="text-center p-3 rounded-lg border border-gray-200" style="background-color: #F3E8FF;">
              <p class="text-xs font-medium text-gray-500 mb-1">Tarjeta</p>
              <p class="text-xl font-bold text-gray-900">{{ formatCurrency(sessionData?.card_sales) }}</p>
            </div>
            <div class="text-center p-3 rounded-lg border border-gray-200" style="background-color: #EBF2FF;">
              <p class="text-xs font-medium text-gray-500 mb-1">Transferencia</p>
              <p class="text-xl font-bold text-gray-900">{{ formatCurrency(sessionData?.transfer_sales) }}</p>
            </div>
          </div>
        </div>

        <!-- Cálculo en formato compacto -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
          <h4 class="text-sm font-bold text-gray-900 mb-3">Cálculo de Caja</h4>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Monto Inicial:</span>
              <span class="font-semibold">{{ formatCurrency(sessionData?.opening_amount) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">+ Ventas en Efectivo:</span>
              <span class="font-semibold text-green-600">{{ formatCurrency(sessionData?.cash_sales) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">- Gastos/Retiros:</span>
              <span class="font-semibold text-red-600">{{ formatCurrency(sessionData?.total_expenses) }}</span>
            </div>
            <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between">
              <span class="font-bold text-gray-900">Monto Esperado:</span>
              <span class="text-lg font-bold text-yellow-600">{{ formatCurrency(expectedAmount) }}</span>
            </div>
          </div>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-3">
          <!-- Arqueo - Grid de 2 columnas -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label for="actual_amount" class="block text-xs font-bold text-gray-900 mb-1.5">
                Monto Real en Caja (Arqueo) <span class="text-red-600">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">$</span>
                <input
                  id="actual_amount"
                  ref="amountInput"
                  v-model="formData.actual_amount"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="0.00"
                  class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-medium"
                  :class="{ 'border-red-500 focus:ring-red-500': errors.actual_amount }"
                  required
                />
              </div>
              <p v-if="errors.actual_amount" class="mt-1 text-[10px] text-red-600">{{ errors.actual_amount }}</p>
              <p class="mt-1 text-[10px] text-gray-500">Cuente físicamente el efectivo</p>
            </div>

            <!-- Mostrar diferencia al lado -->
            <div v-if="formData.actual_amount">
              <label class="block text-xs font-bold text-gray-900 mb-1.5">Diferencia</label>
              <div class="rounded-lg p-2 border-2" :class="differenceClass">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-medium" :class="differenceTextClass">{{ differenceLabel }}</span>
                  <span class="text-base font-bold" :class="differenceTextClass">
                    {{ formatCurrency(Math.abs(difference)) }}
                  </span>
                </div>
                <p class="text-[10px] mt-0.5" :class="differenceDescClass">{{ differenceDescription }}</p>
              </div>
            </div>
          </div>

          <!-- Notas de cierre -->
          <div>
            <label for="closing_notes" class="block text-xs font-bold text-gray-900 mb-1.5">
              Notas de Cierre <span class="text-gray-400 font-normal">(opcional)</span>
            </label>
            <textarea
              id="closing_notes"
              v-model="formData.closing_notes"
              rows="2"
              placeholder="Observaciones..."
              class="w-full px-2.5 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none text-xs"
              :class="{ 'border-red-500 focus:ring-red-500': errors.closing_notes }"
              maxlength="500"
            ></textarea>
            <div class="flex justify-between mt-1">
              <p v-if="errors.closing_notes" class="text-[10px] text-red-600">{{ errors.closing_notes }}</p>
              <p class="text-[10px] text-gray-500 ml-auto">{{ formData.closing_notes?.length || 0 }}/500</p>
            </div>
          </div>

          <!-- Warning compacto -->
          <div v-if="Math.abs(difference) > 1000" class="bg-red-50 border border-red-200 rounded-lg p-2 flex items-start space-x-2">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
              <h4 class="text-xs font-bold text-red-900">Diferencia Significativa</h4>
              <p class="text-[10px] text-red-800 mt-0.5">La diferencia supera $1,000. Verifique el conteo.</p>
            </div>
          </div>

          <!-- Confirmación -->
          <div class="bg-gray-50 border border-gray-200 rounded-lg p-2">
            <label class="flex items-start space-x-2 cursor-pointer">
              <input
                v-model="confirmClosure"
                type="checkbox"
                class="mt-0.5 w-3.5 h-3.5 text-red-600 border-gray-300 rounded focus:ring-red-500 cursor-pointer"
              />
              <span class="text-[10px] text-gray-800 leading-tight">
                Confirmo que he realizado el arqueo correctamente. Los datos son exactos y no podrán modificarse.
              </span>
            </label>
          </div>

          <!-- Buttons -->
          <div class="flex space-x-2 pt-1">
            <button
              type="button"
              @click="$emit('close')"
              class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium text-sm transition-colors"
              :disabled="isLoading"
            >
              Cancelar
            </button>
            <button
              type="submit"
              class="flex-1 px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="isLoading || !confirmClosure"
            >
              {{ isLoading ? 'Cerrando...' : 'Cerrar Caja' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { useToast } from '../composables/useToast'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  sessionData: {
    type: Object,
    default: () => ({})
  }
})

// Emits
const emit = defineEmits(['close', 'success'])

// Composables
const { showToast } = useToast()

// Referencias
const amountInput = ref(null)

// Estado del formulario
const formData = ref({
  actual_amount: '',
  closing_notes: ''
})

const errors = ref({})
const isLoading = ref(false)
const confirmClosure = ref(false)

// Computed
const expectedAmount = computed(() => {
  const opening = parseFloat(props.sessionData?.opening_amount || 0)
  const cashSales = parseFloat(props.sessionData?.cash_sales || 0)
  const expenses = parseFloat(props.sessionData?.total_expenses || 0)
  return opening + cashSales - expenses
})

const difference = computed(() => {
  const actual = parseFloat(formData.value.actual_amount || 0)
  return actual - expectedAmount.value
})

const differenceClass = computed(() => {
  if (!formData.value.actual_amount) return 'bg-gray-50 border border-gray-200'
  
  if (difference.value > 0) return 'bg-green-50 border border-green-200'
  if (difference.value < 0) return 'bg-red-50 border border-red-200'
  return 'bg-blue-50 border border-blue-200'
})

const differenceIconClass = computed(() => {
  if (difference.value > 0) return 'text-green-600'
  if (difference.value < 0) return 'text-red-600'
  return 'text-blue-600'
})

const differenceTextClass = computed(() => {
  if (difference.value > 0) return 'text-green-800'
  if (difference.value < 0) return 'text-red-800'
  return 'text-blue-800'
})

const differenceDescClass = computed(() => {
  if (difference.value > 0) return 'text-green-700'
  if (difference.value < 0) return 'text-red-700'
  return 'text-blue-700'
})

const differenceLabel = computed(() => {
  if (difference.value > 0) return 'Sobrante'
  if (difference.value < 0) return 'Faltante'
  return 'Exacto'
})

const differenceDescription = computed(() => {
  if (difference.value > 0) return 'Hay más dinero del esperado en caja'
  if (difference.value < 0) return 'Hay menos dinero del esperado en caja'
  return 'El dinero en caja coincide con lo esperado'
})

const isFormValid = computed(() => {
  return formData.value.actual_amount && 
         parseFloat(formData.value.actual_amount) >= 0 &&
         confirmClosure.value &&
         !Object.keys(errors.value).length
})

// Watchers
watch(() => props.show, (newVal) => {
  if (newVal) {
    nextTick(() => {
      amountInput.value?.focus()
    })
  }
})

watch(() => formData.value.actual_amount, (newVal) => {
  if (errors.value.actual_amount) {
    validateAmount()
  }
})

watch(() => formData.value.closing_notes, (newVal) => {
  if (errors.value.closing_notes) {
    validateNotes()
  }
})

// Métodos de validación
const validateAmount = () => {
  const amount = parseFloat(formData.value.actual_amount)
  
  if (!formData.value.actual_amount) {
    errors.value.actual_amount = 'El monto real en caja es obligatorio'
    return false
  }
  
  if (isNaN(amount) || amount < 0) {
    errors.value.actual_amount = 'Ingrese un monto válido mayor o igual a 0'
    return false
  }
  
  delete errors.value.actual_amount
  return true
}

const validateNotes = () => {
  if (formData.value.closing_notes && formData.value.closing_notes.length > 500) {
    errors.value.closing_notes = 'Las notas no pueden exceder 500 caracteres'
    return false
  }
  
  delete errors.value.closing_notes
  return true
}

const validateForm = () => {
  const amountValid = validateAmount()
  const notesValid = validateNotes()
  
  return amountValid && notesValid && confirmClosure.value
}

// Utilidades
const formatCurrency = (amount) => {
  if (amount === null || amount === undefined) return '$0'
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-CO')
}

const formatTime = (time) => {
  if (!time) return '-'
  return time
}

// Manejo del formulario
const handleSubmit = async () => {
  if (!validateForm()) {
    showToast('Por favor, complete todos los campos requeridos', 'error')
    return
  }

  isLoading.value = true
  
  try {
    const closeData = {
      actual_amount: parseFloat(formData.value.actual_amount),
      closing_notes: formData.value.closing_notes || null
    }

    emit('success', closeData)
  } catch (error) {
    console.error('Error en manejo de cierre:', error)
    showToast('Error al procesar el cierre de caja', 'error')
  } finally {
    isLoading.value = false
  }
}

// Reset form cuando se cierra
const resetForm = () => {
  formData.value = {
    actual_amount: '',
    closing_notes: ''
  }
  errors.value = {}
  confirmClosure.value = false
}

// Watch para reset cuando se cierra
watch(() => props.show, (newVal) => {
  if (!newVal) {
    setTimeout(resetForm, 300) // Delay para animación
  }
})

// Lifecycle
onMounted(() => {
  if (props.show) {
    nextTick(() => {
      amountInput.value?.focus()
    })
  }
})
</script>

<style scoped>
/* Reutilizar estilos del modal de apertura */
.fixed {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.bg-white {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

input:focus, textarea:focus {
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>