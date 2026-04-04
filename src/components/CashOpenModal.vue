<template>
  <div v-if="show" class="fixed inset-0 bg-black/70 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center z-[60] p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 w-full max-w-2xl border border-gray-200 dark:border-zinc-800">
      <!-- Header con info integrada -->
      <div class="px-6 pt-5 pb-4 bg-gradient-to-r from-emerald-50/80 to-transparent dark:from-emerald-950/20 dark:to-transparent rounded-t-2xl">
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center border border-emerald-200/60 dark:border-emerald-800/40">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold text-gray-900 dark:text-white">Apertura de Caja</h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">Inicia tu jornada registrando el dinero base en caja</p>
            </div>
          </div>
          <button 
            v-if="!forceOpen"
            @click="$emit('close')" 
            class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 mt-1"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <!-- Info inline en el header -->
        <div class="grid grid-cols-4 gap-4">
          <div>
            <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">Operador</p>
            <p class="text-sm font-semibold text-gray-800 dark:text-zinc-200 truncate">{{ userInfo.name || 'Usuario' }}</p>
          </div>
          <div>
            <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">Fecha</p>
            <p class="text-sm font-semibold text-gray-800 dark:text-zinc-200">{{ currentDate }}</p>
          </div>
          <div>
            <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">Hora</p>
            <p class="text-sm font-semibold text-gray-800 dark:text-zinc-200">{{ currentTime }}</p>
          </div>
          <div>
            <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">Terminal</p>
            <p class="text-sm font-semibold text-gray-800 dark:text-zinc-200">POS-001</p>
          </div>
        </div>
      </div>

      <!-- Body -->
      <div class="px-6 pb-6 pt-5">

        <form @submit.prevent="handleSubmit" class="space-y-5">
          <!-- Selector de Sede -->
          <div v-if="shouldShowWarehouseSelector">
            <label for="warehouse_id" class="block text-sm font-semibold text-gray-800 dark:text-zinc-200 mb-2">
              Sede de trabajo <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <select
                id="warehouse_id"
                v-model="formData.warehouse_id"
                class="w-full px-3 py-3 border border-gray-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 text-sm font-medium appearance-none focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-300 dark:focus:border-emerald-600 transition-all"
                :class="{ 'border-red-500 focus:ring-red-500': errors.warehouse_id }"
                required
                :disabled="loadingWarehouses"
              >
                <option value="" disabled>{{ loadingWarehouses ? 'Cargando sedes...' : 'Seleccionar sede' }}</option>
                <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
                  {{ warehouse.name }} {{ warehouse.is_default ? '(Principal)' : '' }}
                </option>
              </select>
              <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </div>
            <p v-if="errors.warehouse_id" class="mt-1 text-xs text-red-600">{{ errors.warehouse_id }}</p>
            <p class="mt-1.5 text-xs text-gray-500 dark:text-zinc-400">Selecciona la sede donde operarás durante esta jornada</p>
          </div>

          <!-- Fondo inicial - protagonista -->
          <div>
            <label for="opening_amount" class="block text-sm font-semibold text-gray-800 dark:text-zinc-200 mb-2">
              Fondo inicial en caja <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-emerald-600 dark:text-emerald-400 text-lg font-bold">$</span>
              <input
                id="opening_amount"
                ref="amountInput"
                v-model="formData.opening_amount"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                class="w-full pl-10 pr-4 py-4 border-2 border-gray-300 dark:border-zinc-600 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-xl font-bold placeholder-gray-300 dark:placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-400 dark:focus:border-emerald-500 transition-all"
                :class="{ 'border-red-500 focus:ring-red-500': errors.opening_amount }"
                required
              />
            </div>
            <p v-if="errors.opening_amount" class="mt-1 text-xs text-red-600">{{ errors.opening_amount }}</p>
            <p class="mt-1.5 text-xs text-gray-500 dark:text-zinc-400">Ingresa el dinero físico con el que inicia la caja</p>
          </div>

          <!-- Observaciones de apertura -->
          <div>
            <label for="opening_notes" class="block text-sm font-semibold text-gray-800 dark:text-zinc-200 mb-2">
              Observaciones de apertura <span class="text-gray-400 dark:text-zinc-500 font-normal">(opcional)</span>
            </label>
            <textarea
              id="opening_notes"
              v-model="formData.opening_notes"
              rows="2"
              placeholder="Ej: cambio inicial, billetes, monedas"
              class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 resize-none text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-300 dark:focus:border-emerald-600 transition-all"
              :class="{ 'border-red-500 focus:ring-red-500': errors.opening_notes }"
              maxlength="500"
            ></textarea>
            <div class="flex justify-between mt-1">
              <p v-if="errors.opening_notes" class="text-xs text-red-600">{{ errors.opening_notes }}</p>
              <p class="text-xs text-gray-400 dark:text-zinc-500 ml-auto">{{ formData.opening_notes?.length || 0 }}/500</p>
            </div>
          </div>

          <!-- Mensaje informativo -->
          <div class="flex items-center gap-2.5 py-3 px-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-800/40">
            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-xs font-medium text-blue-800 dark:text-blue-300">Este valor será usado como base para el control de ventas del día</p>
          </div>

          <!-- Buttons -->
          <div class="flex gap-3 pt-3">
            <button
              v-if="forceOpen"
              type="button"
              @click="$emit('quotation-mode')"
              class="flex-1 px-5 py-3 bg-white dark:bg-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-700 text-slate-700 dark:text-zinc-200 rounded-xl font-semibold text-sm border border-slate-200 dark:border-zinc-700 transition-all duration-200 flex items-center justify-center gap-2"
              :disabled="isLoading"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <span>Iniciar solo cotizaciones</span>
            </button>
            
            <button
              v-if="!forceOpen"
              type="button"
              @click="$emit('close')"
              class="flex-1 px-5 py-3 bg-white dark:bg-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-300 rounded-xl font-semibold text-sm border border-slate-200 dark:border-zinc-700 transition-all duration-200"
              :disabled="isLoading"
            >
              Cancelar
            </button>
            <button
              type="submit"
              class="flex-1 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/30 dark:shadow-emerald-900/40 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="isLoading || !isFormValid"
            >
              <span v-if="isLoading">Abriendo...</span>
              <span v-else>Abrir Caja</span>
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
      console.error('⚠️ No hay bodegas disponibles')
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
/* Animaciones para el modal */
.fixed {
  animation: fadeIn 0.25s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.bg-white,
.dark\:bg-zinc-900 {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateY(-12px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Focus ring */
input:focus, textarea:focus, select:focus {
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
}

/* Remove number input spinners for cleaner amount field */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type=number] {
  -moz-appearance: textfield;
}

/* Disabled button styles */
button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

/* Loading spinner animation */
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>