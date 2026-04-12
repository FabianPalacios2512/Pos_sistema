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
      <div v-if="show" class="fixed inset-0 bg-black/50 dark:bg-black/70 z-[60] flex items-center justify-center p-4">
        <div
          @click.stop
          class="bg-white dark:bg-zinc-900 w-full max-w-5xl border border-gray-200 dark:border-zinc-800 shadow-2xl dark:shadow-black/60 overflow-hidden rounded-lg"
        >
          <!-- ══ HEADER CORPORATIVO ══ -->
          <div class="px-8 py-5 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
            <div>
              <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Apertura de Caja</h3>
              <p class="text-xs text-gray-500 dark:text-zinc-500 font-medium mt-1">Inicia tu jornada registrando el dinero base en caja</p>
            </div>
            <button
              v-if="!forceOpen"
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

            <!-- ▸ PANEL IZQUIERDO (38%): Contexto de la sesión -->
            <div class="lg:w-[38%] bg-gray-50 dark:bg-zinc-950/50 border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-zinc-800 p-7 space-y-6">

              <!-- Info del operario -->
              <div class="space-y-3">
                <p class="text-[11px] font-bold text-gray-400 dark:text-zinc-600 uppercase tracking-widest">Información de Apertura</p>
                <div class="space-y-2.5">
                  <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 dark:text-zinc-500">Operador</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white truncate ml-4 max-w-[180px]">{{ userInfo.name || 'Usuario' }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 dark:text-zinc-500">Fecha</span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-zinc-300">{{ currentDate }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 dark:text-zinc-500">Hora</span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-zinc-300 tabular-nums">{{ currentTime }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 dark:text-zinc-500">Terminal</span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-zinc-300">POS-001</span>
                  </div>
                </div>
              </div>

              <!-- Indicador de estado -->
              <div class="space-y-2.5">
                <p class="text-[11px] font-bold text-gray-400 dark:text-zinc-600 uppercase tracking-widest">Estado del Sistema</p>
                <div class="border border-gray-200 dark:border-zinc-800 rounded-md bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
                  <div class="p-3.5 flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-zinc-500">Sesión actual</span>
                    <span class="text-[11px] font-bold text-orange-700 dark:text-orange-400 uppercase tracking-widest bg-orange-50 dark:bg-orange-950/50 border border-orange-200 dark:border-orange-800/50 px-2.5 py-1 rounded">Sin abrir</span>
                  </div>
                  <div class="p-3.5 flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-zinc-500">Ventas habilitadas</span>
                    <span class="text-xs font-bold text-gray-400 dark:text-zinc-600">No</span>
                  </div>
                </div>
              </div>

              <!-- Nota informativa -->
              <div class="bg-gray-100 dark:bg-zinc-800/80 border border-gray-200 dark:border-zinc-700 rounded-md p-3.5">
                <p class="text-xs text-gray-600 dark:text-zinc-400 leading-relaxed">El monto inicial será la base de referencia para el arqueo de cierre. Asegúrese de contar el efectivo físico antes de registrar.</p>
              </div>
            </div>

            <!-- ▸ PANEL DERECHO (62%): Formulario -->
            <div class="lg:w-[62%] bg-white dark:bg-zinc-900 p-7">
              <form @submit.prevent="handleSubmit" class="space-y-5">

                <!-- Selector de Sede -->
                <div v-if="shouldShowWarehouseSelector" class="space-y-2">
                  <label for="warehouse_id" class="block text-[11px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">
                    Sede de trabajo <span class="text-red-600 dark:text-red-400">*</span>
                  </label>
                  <div class="relative">
                    <select
                      id="warehouse_id"
                      v-model="formData.warehouse_id"
                      class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 text-sm font-medium appearance-none focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-zinc-400 focus:border-transparent transition-colors"
                      :class="{ 'border-red-500 focus:ring-red-500': errors.warehouse_id }"
                      required
                      :disabled="loadingWarehouses"
                    >
                      <option value="" disabled>{{ loadingWarehouses ? 'Cargando sedes...' : 'Seleccionar sede' }}</option>
                      <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
                        {{ warehouse.name }} {{ warehouse.is_default ? '(Principal)' : '' }}
                      </option>
                    </select>
                    <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                  </div>
                  <p v-if="errors.warehouse_id" class="text-[11px] text-red-700 dark:text-red-400 font-medium">{{ errors.warehouse_id }}</p>
                  <p class="text-xs text-gray-400 dark:text-zinc-600">Selecciona la sede donde operarás durante esta jornada</p>
                </div>

                <!-- Fondo inicial -->
                <div class="space-y-2">
                  <label for="opening_amount" class="block text-[11px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">
                    Fondo inicial en caja <span class="text-red-600 dark:text-red-400">*</span>
                  </label>
                  <p class="text-xs text-gray-400 dark:text-zinc-600">Ingresa el dinero físico con el que inicia la caja</p>
                  <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 text-xl font-bold">$</span>
                    <input
                      id="opening_amount"
                      ref="amountInput"
                      v-model="formData.opening_amount"
                      type="number"
                      step="0.01"
                      min="0"
                      placeholder="0"
                      class="w-full pl-10 pr-4 py-3.5 text-2xl font-semibold tabular-nums border-2 border-gray-300 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-zinc-400 focus:border-transparent"
                      :class="{ 'border-red-500 focus:ring-red-500': errors.opening_amount }"
                      required
                    />
                  </div>
                  <p v-if="errors.opening_amount" class="text-[11px] text-red-700 dark:text-red-400 font-medium">{{ errors.opening_amount }}</p>
                </div>

                <!-- Observaciones de apertura -->
                <div class="space-y-2">
                  <label for="opening_notes" class="block text-[11px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">
                    Observaciones de apertura <span class="font-normal normal-case text-gray-400 dark:text-zinc-600">(opcional)</span>
                  </label>
                  <textarea
                    id="opening_notes"
                    v-model="formData.opening_notes"
                    name="cash_open_obs"
                    rows="2"
                    placeholder="Ej: cambio inicial, billetes, monedas"
                    autocomplete="one-time-code"
                    data-form-type="other"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-600 rounded-md resize-none text-sm focus:ring-2 focus:ring-gray-900 dark:focus:ring-zinc-400 focus:border-transparent"
                    :class="{ 'border-red-500 focus:ring-red-500': errors.opening_notes }"
                    maxlength="500"
                  ></textarea>
                  <div class="flex justify-between">
                    <p v-if="errors.opening_notes" class="text-[11px] text-red-700 dark:text-red-400 font-medium">{{ errors.opening_notes }}</p>
                    <p class="text-[10px] text-gray-400 dark:text-zinc-600 ml-auto tabular-nums">{{ formData.opening_notes?.length || 0 }}/500</p>
                  </div>
                </div>

                <!-- ═══ BOTONES ═══ -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-zinc-800">
                  <button
                    v-if="forceOpen"
                    type="button"
                    @click="$emit('quotation-mode')"
                    class="px-5 py-2.5 text-xs font-semibold uppercase tracking-wide rounded-md transition-all duration-150
                      bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300
                      border border-gray-300 dark:border-zinc-600
                      hover:bg-gray-50 dark:hover:bg-zinc-700
                      inline-flex items-center gap-2"
                    :disabled="isLoading"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Solo Cotizaciones
                  </button>

                  <button
                    v-if="!forceOpen"
                    type="button"
                    @click="$emit('close')"
                    class="px-5 py-2.5 text-xs font-semibold uppercase tracking-wide rounded-md transition-all duration-150
                      bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300
                      border border-gray-300 dark:border-zinc-600
                      hover:bg-gray-50 dark:hover:bg-zinc-700"
                    :disabled="isLoading"
                  >
                    Cancelar
                  </button>

                  <button
                    type="submit"
                    :disabled="isLoading || !isFormValid"
                    class="px-7 py-3 text-sm font-semibold uppercase tracking-wide rounded-md transition-all duration-150
                      bg-gray-900 dark:bg-zinc-200 text-white dark:text-zinc-900
                      hover:bg-black dark:hover:bg-white
                      disabled:opacity-40 disabled:cursor-not-allowed"
                  >
                    <span v-if="isLoading" class="inline-flex items-center gap-2">
                      <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                      Abriendo...
                    </span>
                    <span v-else>Abrir Caja</span>
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
import { warehouseService } from '../services/warehouseService'
import { appStore } from '../store/appStore'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  userInfo: {
    type: Object,
    default: () => ({})
  },
  forceOpen: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['close', 'success', 'quotation-mode'])

// Composables
const { showToast } = useToast()

// Referencias
const amountInput = ref(null)

// Estado del formulario
const formData = ref({
  opening_amount: '',
  opening_notes: '',
  warehouse_id: null
})

const warehouses = ref([])
const loadingWarehouses = ref(false)
const errors = ref({})
const isLoading = ref(false)

// Computed
const currentDate = computed(() => {
  return new Date().toLocaleDateString('es-CO', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
})

const currentTime = computed(() => {
  return new Date().toLocaleTimeString('es-CO', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
})

// Computed para determinar si debe mostrar el selector de tienda
const shouldShowWarehouseSelector = computed(() => {
  const plan = appStore.tenantPlan
  const isPremiumOrEnterprise = plan === 'premium' || plan === 'enterprise'
  
  // Si no es premium/enterprise, no mostrar
  if (!isPremiumOrEnterprise) return false
  
  // Si es premium/enterprise, verificar si tiene más de una bodega
  return warehouses.value.length > 1
})

const isFormValid = computed(() => {
  // Si el selector de bodega está oculto, no requerir validación de warehouse_id
  // (se auto-selecciona automáticamente)
  const warehouseValid = shouldShowWarehouseSelector.value 
    ? formData.value.warehouse_id  // Si se muestra, debe estar seleccionada
    : true  // Si está oculto, es válido (se auto-selecciona)
  
  return warehouseValid &&
         formData.value.opening_amount && 
         parseFloat(formData.value.opening_amount) >= 0 &&
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

watch(() => formData.value.opening_amount, (newVal) => {
  if (errors.value.opening_amount) {
    validateAmount()
  }
})

watch(() => formData.value.opening_notes, (newVal) => {
  if (errors.value.opening_notes) {
    validateNotes()
  }
})

// Métodos de validación
const validateWarehouse = () => {
  // Si el selector está oculto, no validar (se auto-selecciona)
  if (!shouldShowWarehouseSelector.value) {
    delete errors.value.warehouse_id
    return true
  }
  
  // Si el selector está visible, validar que esté seleccionado
  if (!formData.value.warehouse_id) {
    errors.value.warehouse_id = 'Debe seleccionar una tienda'
    return false
  }
  
  delete errors.value.warehouse_id
  return true
}

const validateAmount = () => {
  const amount = parseFloat(formData.value.opening_amount)
  
  if (!formData.value.opening_amount) {
    errors.value.opening_amount = 'El monto inicial es obligatorio'
    return false
  }
  
  if (isNaN(amount) || amount < 0) {
    errors.value.opening_amount = 'Ingrese un monto válido mayor o igual a 0'
    return false
  }
  
  delete errors.value.opening_amount
  return true
}

const validateNotes = () => {
  if (formData.value.opening_notes && formData.value.opening_notes.length > 500) {
    errors.value.opening_notes = 'Las notas no pueden exceder 500 caracteres'
    return false
  }
  
  delete errors.value.opening_notes
  return true
}

const validateForm = () => {
  const warehouseValid = validateWarehouse()
  const amountValid = validateAmount()
  const notesValid = validateNotes()
  
  return warehouseValid && amountValid && notesValid
}

// Manejo del formulario
const handleSubmit = async () => {
  // Asegurar que las bodegas estén cargadas
  if (loadingWarehouses.value) {
    showToast('Cargando información, espere un momento...', 'info')
    return
  }
  
  // Si no hay warehouse_id y no se debe mostrar el selector, auto-seleccionar
  if (!formData.value.warehouse_id && !shouldShowWarehouseSelector.value) {
    const defaultWarehouse = warehouses.value.find(w => w.is_default)
    formData.value.warehouse_id = defaultWarehouse ? defaultWarehouse.id : warehouses.value[0]?.id
  }
  
  if (!validateForm()) {
    showToast('Por favor, corrija los errores en el formulario', 'error')
    return
  }
  
  // Verificar que warehouse_id esté presente
  if (!formData.value.warehouse_id) {
    showToast('Error: No se pudo determinar la tienda/bodega', 'error')
    return
  }

  isLoading.value = true
  
  try {
    const sessionData = {
      warehouse_id: formData.value.warehouse_id,
      opening_amount: parseFloat(formData.value.opening_amount),
      opening_notes: formData.value.opening_notes || null
    }

    emit('success', sessionData)
  } catch (error) {
    console.error('Error en manejo de apertura:', error)
    showToast('Error al procesar la apertura de caja', 'error')
  } finally {
    isLoading.value = false
  }
}

// Cargar bodegas/tiendas
const loadWarehouses = async () => {
  try {
    loadingWarehouses.value = true
    const response = await warehouseService.getAll()
    
    // El backend devuelve { warehouses: [], plan_info: {} }
    const data = response.warehouses || response.data?.warehouses || response
    warehouses.value = Array.isArray(data) ? data.filter(w => w.active) : []
    
    // Si no hay bodegas, intentar crear una por defecto o mostrar error
    if (warehouses.value.length === 0) {
      console.error('No hay bodegas disponibles')
      showToast('No hay tiendas/bodegas configuradas', 'error')
      return
    }
    
    // Verificar si el usuario tiene acceso a multi-warehouse
    const plan = appStore.tenantPlan
    const isPremiumOrEnterprise = plan === 'premium' || plan === 'enterprise'
    
    // Si NO es premium/enterprise O solo tiene una bodega, auto-seleccionar
    if (!isPremiumOrEnterprise || warehouses.value.length === 1) {
      // Seleccionar la primera bodega disponible (o la por defecto)
      const defaultWarehouse = warehouses.value.find(w => w.is_default)
      formData.value.warehouse_id = defaultWarehouse ? defaultWarehouse.id : warehouses.value[0]?.id
    } else {
      // Si es premium/enterprise y tiene múltiples bodegas, auto-seleccionar la por defecto si existe
      const defaultWarehouse = warehouses.value.find(w => w.is_default)
      if (defaultWarehouse) {
        formData.value.warehouse_id = defaultWarehouse.id
      }
    }
  } catch (error) {
    console.error('Error cargando tiendas:', error)
    showToast('Error al cargar las tiendas disponibles', 'error')
  } finally {
    loadingWarehouses.value = false
  }
}

// Reset form cuando se cierra
const resetForm = () => {
  formData.value = {
    opening_amount: '',
    opening_notes: '',
    warehouse_id: null
  }
  errors.value = {}
}

// Watch para reset cuando se cierra
watch(() => props.show, (newVal) => {
  if (!newVal) {
    setTimeout(resetForm, 300) // Delay para animación
  }
})

// Lifecycle
onMounted(() => {
  loadWarehouses()
  
  if (props.show) {
    nextTick(() => {
      amountInput.value?.focus()
    })
  }
})
</script>

<style scoped>
/* Remove number input spinners */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type=number] {
  -moz-appearance: textfield;
}
</style>