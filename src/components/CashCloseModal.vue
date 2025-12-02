<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="show"
        @click="$emit('close')"
        class="fixed inset-0 bg-black/70 dark:bg-black/80 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
      >
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div 
            v-if="show"
            @click.stop
            class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-6xl border border-gray-200 dark:border-zinc-700"
          >
            <!-- Header -->
            <div class="bg-white dark:bg-zinc-900 px-6 py-4 border-b border-gray-200 dark:border-zinc-700 sticky top-0 z-10">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gray-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" 
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Cierre de Caja</h3>
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Finalizar sesión y realizar arqueo</p>
                  </div>
                </div>
                <button 
                  @click="$emit('close')" 
                  class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-200 transition-all"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Body: Layout 2 Columnas -->
            <div class="p-6">
              <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- COLUMNA IZQUIERDA (2/3): Información -->
                <div class="lg:col-span-2 space-y-5">
                  
                  <!-- Información de Sesión - Diseño Horizontal -->
                  <div class="bg-gray-100 dark:bg-zinc-900/30 rounded-lg px-4 py-3">
                    <div class="flex items-center justify-between">
                      <!-- Izquierda: Info del Usuario -->
                      <div>
                        <p class="text-[10px] text-gray-500 dark:text-zinc-500 font-medium uppercase tracking-wide mb-0.5">Información de la Sesión</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ sessionData?.user?.name || 'Usuario' }}</p>
                        <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">
                          Apertura: {{ formatDate(sessionData?.opening_date) }} • {{ formatTime(sessionData?.opening_time) }}
                        </p>
                      </div>
                      <!-- Derecha: Monto Inicial -->
                      <div class="text-right">
                        <p class="text-[10px] text-gray-500 dark:text-zinc-500 font-medium uppercase tracking-wide mb-0.5">Monto Inicial</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ formatCurrency(sessionData?.opening_amount) }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Resumen de Ventas - Hero Numbers -->
                  <div class="space-y-4">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Ventas del Día</h4>
                    <div class="grid grid-cols-4 gap-6">
                      <!-- Total Ventas -->
                      <div class="space-y-1">
                        <div class="flex items-center gap-2 mb-2">
                          <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                          </svg>
                          <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Total</p>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(sessionData?.total_sales) }}</p>
                      </div>
                      <!-- Efectivo -->
                      <div class="space-y-1">
                        <div class="flex items-center gap-2 mb-2">
                          <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                          </svg>
                          <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Efectivo</p>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(sessionData?.cash_sales) }}</p>
                      </div>
                      <!-- Tarjeta -->
                      <div class="space-y-1">
                        <div class="flex items-center gap-2 mb-2">
                          <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                          </svg>
                          <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Tarjeta</p>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(sessionData?.card_sales) }}</p>
                      </div>
                      <!-- Transferencia -->
                      <div class="space-y-1">
                        <div class="flex items-center gap-2 mb-2">
                          <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                          </svg>
                          <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Transfer</p>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(sessionData?.transfer_sales) }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Cálculo de Caja - Clean -->
                  <div class="space-y-4 pt-2">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Cálculo de Caja</h4>
                    <div class="space-y-3">
                      <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600 dark:text-zinc-400">Monto Inicial</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatCurrency(sessionData?.opening_amount) }}</span>
                      </div>
                      <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600 dark:text-zinc-400">+ Ventas Efectivo</span>
                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(sessionData?.cash_sales) }}</span>
                      </div>
                      <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600 dark:text-zinc-400">− Gastos/Retiros</span>
                        <span class="text-sm font-semibold text-red-600 dark:text-red-400">{{ formatCurrency(sessionData?.total_expenses) }}</span>
                      </div>
                      <div class="border-t border-gray-200 dark:border-zinc-700/50 pt-4 mt-2">
                        <div class="flex justify-between items-center">
                          <span class="text-xs font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Monto Esperado</span>
                          <span class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(expectedAmount) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>

                <!-- COLUMNA DERECHA (1/3): Arqueo y Acciones -->
                <div class="space-y-5">
                  <form @submit.prevent="handleSubmit" class="space-y-4">
                    <!-- Arqueo de Efectivo - Hero Input -->
                    <div class="space-y-3">
                      <div>
                        <label for="actual_amount" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-3">
                          Monto Real en Caja <span class="text-red-500 dark:text-red-400">*</span>
                        </label>
                        <div class="relative">
                          <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-zinc-500 text-2xl font-bold">$</span>
                          <input
                            id="actual_amount"
                            ref="amountInput"
                            v-model="formData.actual_amount"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-zinc-900/50 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-3xl font-bold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-600"
                            :class="{ 'border-red-500 focus:ring-red-500': errors.actual_amount }"
                            required
                          />
                        </div>
                        <p v-if="errors.actual_amount" class="mt-2 text-xs text-red-600 dark:text-red-400 font-semibold">{{ errors.actual_amount }}</p>
                        <p class="mt-2 text-xs text-gray-500 dark:text-zinc-500">Cuente el efectivo físicamente</p>
                      </div>

                      <!-- Diferencia -->
                      <div v-if="formData.actual_amount" class="mt-4 pt-4 border-t border-gray-200 dark:border-zinc-800">
                        <div class="flex items-center justify-between">
                          <span class="text-xs font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">{{ differenceLabel }}</span>
                          <span class="text-2xl font-bold" :class="differenceTextClass">
                            {{ formatCurrency(Math.abs(difference)) }}
                          </span>
                        </div>
                        <p class="text-xs mt-2" :class="differenceDescClass">{{ differenceDescription }}</p>
                      </div>
                    </div>

                    <!-- Notas de Cierre -->
                    <div class="space-y-3">
                      <label for="closing_notes" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wide">
                        Notas de Cierre
                      </label>
                      <textarea
                        id="closing_notes"
                        v-model="formData.closing_notes"
                        rows="3"
                        placeholder="Agregar observaciones sobre el cierre..."
                        class="w-full px-3 py-3 bg-gray-50 dark:bg-zinc-900/50 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-600"
                        :class="{ 'border-red-500 focus:ring-red-500': errors.closing_notes }"
                        maxlength="500"
                      ></textarea>
                      <div class="flex justify-between mt-2">
                        <p v-if="errors.closing_notes" class="text-xs text-red-600 dark:text-red-400 font-semibold">{{ errors.closing_notes }}</p>
                        <p class="text-xs text-gray-500 dark:text-zinc-500 ml-auto">{{ formData.closing_notes?.length || 0 }}/500</p>
                      </div>
                    </div>

                    <!-- Warning de Diferencia -->
                    <div v-if="Math.abs(difference) > 1000" class="bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/20 rounded-lg p-3 flex items-start gap-3">
                      <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                      </svg>
                      <div>
                        <h4 class="text-sm font-semibold text-orange-800 dark:text-orange-300 mb-1">Diferencia significativa detectada</h4>
                        <p class="text-xs text-orange-700 dark:text-orange-400/80">Verifique nuevamente el conteo de efectivo</p>
                      </div>
                    </div>

                    <!-- Confirmación -->
                    <div class="pt-2">
                      <label class="flex items-start gap-3 cursor-pointer">
                        <input
                          v-model="confirmClosure"
                          type="checkbox"
                          class="mt-1 w-5 h-5 text-emerald-600 bg-gray-50 dark:bg-zinc-900/50 border-gray-300 dark:border-zinc-700 rounded focus:ring-emerald-500 cursor-pointer"
                        />
                        <span class="text-sm text-gray-700 dark:text-zinc-300 font-medium leading-relaxed">
                          Confirmo que he realizado el arqueo correctamente y los datos son exactos
                        </span>
                      </label>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-2">
                      <button
                        type="button"
                        @click="$emit('close')"
                        class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-300 text-xs font-bold rounded-lg border-2 border-slate-300 dark:border-zinc-600 shadow-sm transition-all duration-200 hover:shadow-md hover:border-slate-400 dark:hover:border-zinc-500 flex items-center justify-center gap-2"
                        :disabled="isLoading"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Cancelar</span>
                      </button>
                      <button
                        type="submit"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white text-xs font-bold rounded-lg shadow-lg transition-all duration-200 hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:from-gray-400 disabled:to-gray-500 flex items-center justify-center gap-2"
                        :disabled="isLoading || !confirmClosure"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>{{ isLoading ? 'Cerrando...' : 'Cerrar Caja' }}</span>
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
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