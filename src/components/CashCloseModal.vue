<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        @click="$emit('close')"
        class="fixed inset-0 bg-black/50 dark:bg-black/70 z-[60] flex items-center justify-center p-4"
      >
        <div
          v-if="show"
          @click.stop
          class="bg-white dark:bg-zinc-900 w-full max-w-5xl border border-gray-200 dark:border-zinc-800 shadow-2xl dark:shadow-black/60 overflow-hidden rounded-lg"
        >
          <!-- ══ HEADER CORPORATIVO ══ -->
          <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Cierre de Caja y Arqueo</h3>
              <p class="text-[11px] text-gray-500 dark:text-zinc-500 font-medium mt-0.5">Finalizar sesión activa · {{ sessionData?.user?.name || 'Usuario' }}</p>
            </div>
            <button
              @click="$emit('close')"
              class="w-8 h-8 flex items-center justify-center rounded text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- ══ BODY: DOS PANELES ══ -->
          <div class="flex flex-col lg:flex-row">

            <!-- ▸ PANEL IZQUIERDO (40%): Contexto y Resumen -->
            <div class="lg:w-[40%] bg-gray-50 dark:bg-zinc-950/50 border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-zinc-800 p-6 space-y-5">

              <!-- Info del operario -->
              <div class="space-y-3">
                <p class="text-[10px] font-bold text-gray-400 dark:text-zinc-600 uppercase tracking-widest">Datos de la Sesión</p>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-xs text-gray-500 dark:text-zinc-500">Operario</span>
                    <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ sessionData?.user?.name || 'Usuario' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-xs text-gray-500 dark:text-zinc-500">Apertura</span>
                    <span class="text-xs font-semibold text-gray-600 dark:text-zinc-300">{{ formatDateTime(sessionData?.opened_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-xs text-gray-500 dark:text-zinc-500">Monto Inicial</span>
                    <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ formatCurrency(sessionData?.opening_amount) }}</span>
                  </div>
                </div>
              </div>

              <!-- Resumen operativo en cuadrícula -->
              <div class="space-y-2">
                <p class="text-[10px] font-bold text-gray-400 dark:text-zinc-600 uppercase tracking-widest">Ventas del Día</p>
                <div class="border border-gray-200 dark:border-zinc-800 rounded-md bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
                  <div class="grid grid-cols-2 divide-x divide-gray-200 dark:divide-zinc-800">
                    <div class="p-3">
                      <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Total</p>
                      <p class="text-xl font-black text-gray-900 dark:text-white tabular-nums mt-1">{{ formatCurrency(sessionData?.total_sales) }}</p>
                    </div>
                    <div class="p-3">
                      <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Efectivo</p>
                      <p class="text-xl font-black text-gray-900 dark:text-white tabular-nums mt-1">{{ formatCurrency(sessionData?.cash_sales) }}</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 divide-x divide-gray-200 dark:divide-zinc-800">
                    <div class="p-3">
                      <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Tarjeta</p>
                      <p class="text-xl font-black text-gray-900 dark:text-white tabular-nums mt-1">{{ formatCurrency(sessionData?.card_sales) }}</p>
                    </div>
                    <div class="p-3">
                      <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Transferencia</p>
                      <p class="text-xl font-black text-gray-900 dark:text-white tabular-nums mt-1">{{ formatCurrency(sessionData?.transfer_sales) }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cálculo de Caja -->
              <div class="space-y-2">
                <p class="text-[10px] font-bold text-gray-400 dark:text-zinc-600 uppercase tracking-widest">Cálculo de Caja</p>
                <div class="space-y-2 text-xs">
                  <div class="flex justify-between py-1">
                    <span class="text-gray-500 dark:text-zinc-500">Monto Inicial</span>
                    <span class="font-semibold text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(sessionData?.opening_amount) }}</span>
                  </div>
                  <div class="flex justify-between py-1">
                    <span class="text-gray-500 dark:text-zinc-500">+ Ventas Efectivo</span>
                    <span class="font-semibold text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(sessionData?.cash_sales) }}</span>
                  </div>
                  <div class="flex justify-between py-1">
                    <span class="text-gray-500 dark:text-zinc-500">− Gastos / Retiros</span>
                    <span class="font-semibold text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(sessionData?.total_expenses) }}</span>
                  </div>
                  <div class="border-t border-gray-200 dark:border-zinc-800 pt-3 mt-1">
                    <div class="flex justify-between items-baseline">
                      <span class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-widest">Efectivo Esperado</span>
                      <span class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(expectedAmount) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ▸ PANEL DERECHO (60%): Acción y Arqueo -->
            <div class="lg:w-[60%] bg-white dark:bg-zinc-900 p-6">
              <form @submit.prevent="handleSubmit" class="space-y-5">

                <!-- Ingreso de monto -->
                <div class="space-y-2">
                  <label for="actual_amount" class="block text-[10px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">
                    Ingrese monto real contado en caja <span class="text-red-600 dark:text-red-400">*</span>
                  </label>
                  <p class="text-[11px] text-gray-400 dark:text-zinc-600">Ingrese el monto exacto del efectivo físico</p>
                  <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 text-xl font-bold">$</span>
                    <input
                      id="actual_amount"
                      ref="amountInput"
                      v-model="formData.actual_amount"
                      type="number"
                      step="0.01"
                      min="0"
                      placeholder="0"
                      class="w-full pl-10 pr-4 py-3 text-xl font-semibold tabular-nums border-2 border-gray-300 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-zinc-400 focus:border-transparent"
                      :class="{ 'border-red-500 focus:ring-red-500': errors.actual_amount }"
                      required
                    />
                  </div>
                  <p v-if="errors.actual_amount" class="text-[11px] text-red-700 dark:text-red-400 font-medium">{{ errors.actual_amount }}</p>
                </div>

                <!-- Diferencia calculada -->
                <div class="space-y-2">
                  <p class="text-[10px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">Diferencia Calculada <span class="font-normal normal-case text-gray-400 dark:text-zinc-600">(Esperado vs Contado)</span></p>
                  <div v-if="formData.actual_amount" class="flex items-baseline gap-3">
                    <span class="text-2xl font-black tabular-nums"
                      :class="difference < -0.01 ? 'text-red-700 dark:text-red-400' : 'text-gray-900 dark:text-white'">
                      {{ difference > 0 ? '+' : '' }}{{ formatCurrency(Math.abs(difference)) }}
                    </span>
                    <span class="text-xs font-semibold uppercase tracking-wide"
                      :class="difference < -0.01 ? 'text-red-600 dark:text-red-400' : difference > 0.01 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-zinc-500'">
                      {{ differenceLabel }}
                    </span>
                  </div>
                  <div v-else class="text-sm text-gray-300 dark:text-zinc-700 font-medium">—</div>

                  <!-- Alerta de diferencia significativa -->
                  <div v-if="formData.actual_amount && Math.abs(difference) > 1000" class="flex items-start gap-2 mt-1">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-[11px] text-orange-700 dark:text-orange-400 font-medium">Diferencia significativa detectada. Verifique nuevamente el conteo de efectivo.</p>
                  </div>
                </div>

                <!-- Notas de cierre -->
                <div class="space-y-2">
                  <label for="closing_notes" class="block text-[10px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">
                    Observaciones <span class="font-normal normal-case text-gray-400 dark:text-zinc-600">(opcional)</span>
                  </label>
                  <textarea
                    id="closing_notes"
                    v-model="formData.closing_notes"
                    name="cash_close_obs_normal"
                    rows="2"
                    placeholder="Agregar observaciones sobre el cierre..."
                    autocomplete="one-time-code"
                    data-form-type="other"
                    class="w-full px-3 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-600 rounded-md resize-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-zinc-400 focus:border-transparent"
                    :class="{ 'border-red-500 focus:ring-red-500': errors.closing_notes }"
                    maxlength="500"
                  ></textarea>
                  <div class="flex justify-between">
                    <p v-if="errors.closing_notes" class="text-[11px] text-red-700 dark:text-red-400 font-medium">{{ errors.closing_notes }}</p>
                    <p class="text-[10px] text-gray-400 dark:text-zinc-600 ml-auto tabular-nums">{{ formData.closing_notes?.length || 0 }}/500</p>
                  </div>
                </div>

                <!-- ═══ AUTORIZACIÓN CONDICIONAL ═══ -->

                <!-- Admin: paso directo -->
                <div v-if="isAdminUser" class="flex items-center gap-2 py-2">
                  <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                  <span class="text-xs font-semibold text-gray-600 dark:text-zinc-400">Cierre rápido habilitado · Modo Administrador</span>
                </div>

                <!-- Vendedor: requiere firma -->
                <div v-else class="space-y-2">
                  <p class="text-[10px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">Autorización de Cierre</p>
                  <div class="relative">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2">
                      <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                      </svg>
                    </div>
                    <input
                      :type="showClosePassword ? 'text' : 'password'"
                      v-model="closePassword"
                      name="cash_close_auth_normal"
                      placeholder="Ingresa tu contraseña para firmar"
                      autocomplete="new-password"
                      data-form-type="other"
                      class="w-full pl-10 pr-10 py-2.5 text-sm border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-600 rounded-md focus:ring-2 focus:ring-gray-900 dark:focus:ring-zinc-400 focus:border-transparent"
                      @keydown.enter="handleSubmit"
                    />
                    <button
                      type="button"
                      @click="showClosePassword = !showClosePassword"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors"
                    >
                      <svg v-if="!showClosePassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      </svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                      </svg>
                    </button>
                  </div>
                  <p v-if="closePasswordError" class="text-[11px] text-red-700 dark:text-red-400 font-medium">{{ closePasswordError }}</p>
                </div>

                <!-- ═══ BOTONES ═══ -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-zinc-800">
                  <button
                    type="button"
                    @click="$emit('close')"
                    :disabled="isLoading"
                    class="px-5 py-2.5 text-xs font-semibold uppercase tracking-wide rounded-md transition-all duration-150
                      bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300
                      border border-gray-300 dark:border-zinc-600
                      hover:bg-gray-50 dark:hover:bg-zinc-700"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    :disabled="isLoading || !canSubmitClose"
                    class="px-6 py-2.5 text-xs font-semibold uppercase tracking-wide rounded-md transition-all duration-150
                      bg-gray-900 dark:bg-zinc-200 text-white dark:text-zinc-900
                      hover:bg-black dark:hover:bg-white
                      disabled:opacity-40 disabled:cursor-not-allowed"
                  >
                    <span v-if="isLoading" class="inline-flex items-center gap-2">
                      <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                      Verificando...
                    </span>
                    <span v-else>Confirmar Datos y Cerrar Caja</span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { useToast } from '../composables/useToast'
import authService from '../services/authService.js'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  sessionData: {
    type: Object,
    default: () => ({})
  },
  isAdminUser: {
    type: Boolean,
    default: false
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

// Autorización condicional
const closePassword = ref('')
const showClosePassword = ref(false)
const closePasswordError = ref('')
const passwordVerified = ref(false)

const canSubmitClose = computed(() => {
  if (props.isAdminUser) {
    return true
  }
  return closePassword.value.length > 0
})

// Computed
const expectedAmount = computed(() => {
  if (props.sessionData?.expected_amount !== undefined && props.sessionData?.expected_amount !== null) {
    return parseFloat(props.sessionData.expected_amount || 0)
  }

  const opening = parseFloat(props.sessionData?.opening_amount || 0)
  const cashSales = parseFloat(props.sessionData?.cash_sales || 0)
  const expenses = parseFloat(props.sessionData?.total_expenses || 0)
  const manualIncomes = parseFloat(props.sessionData?.closing_breakdown?.manual_cash_incomes || props.sessionData?.closing_breakdown?.cash_movements?.ingresos || 0)
  const manualEgresos = parseFloat(props.sessionData?.closing_breakdown?.manual_cash_egresos || props.sessionData?.closing_breakdown?.cash_movements?.egresos || 0)
  return opening + cashSales + manualIncomes - expenses - manualEgresos
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
         canSubmitClose.value &&
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
  
  return amountValid && notesValid && canSubmitClose.value
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

const formatDateTime = (datetime) => {
  if (!datetime) return '- - -'
  const d = new Date(datetime)
  if (isNaN(d.getTime())) return '- - -'
  return d.toLocaleDateString('es-CO') + ' · ' + d.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: true })
}

// Manejo del formulario
const handleSubmit = async () => {
  if (!validateForm()) {
    showToast('Por favor, complete todos los campos requeridos', 'error')
    return
  }

  if (!canSubmitClose.value) return

  isLoading.value = true
  closePasswordError.value = ''
  
  try {
    // Si es vendedor, verificar contraseña primero
    if (!props.isAdminUser) {
      const verify = await authService.verifyMyPassword(closePassword.value)
      if (!verify.valid) {
        closePasswordError.value = verify.message || 'Contraseña incorrecta'
        isLoading.value = false
        return
      }
    }

    const closeData = {
      actual_amount: parseFloat(formData.value.actual_amount),
      closing_notes: formData.value.closing_notes || null
    }

    emit('success', closeData)
  } catch (error) {
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
  closePassword.value = ''
  showClosePassword.value = false
  closePasswordError.value = ''
  passwordVerified.value = false
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