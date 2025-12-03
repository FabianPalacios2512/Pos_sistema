<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 flex items-center justify-center z-[60] p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl border border-gray-200">
      <!-- Header Compacto con saludo personalizado -->
      <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100 px-6 py-5">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center shadow-sm">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-black text-gray-900">¡Hola, {{ userInfo.name ? userInfo.name.split(' ')[0] : 'Compañero' }}! 👋</h3>
              <p class="text-sm text-gray-600 font-medium">Vamos a abrir la caja para comenzar</p>
            </div>
          </div>
          <button 
            v-if="!forceOpen"
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
      <div class="p-6">
        <!-- Grid horizontal de información -->
        <div class="grid grid-cols-4 gap-3 mb-6">
          <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Usuario</p>
            <p class="text-sm font-semibold text-gray-900 truncate">{{ userInfo.name || 'Usuario Actual' }}</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Fecha</p>
            <p class="text-sm font-semibold text-gray-900">{{ currentDate }}</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Hora</p>
            <p class="text-sm font-semibold text-gray-900">{{ currentTime }}</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Terminal</p>
            <p class="text-sm font-semibold text-gray-900">POS-001</p>
          </div>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Selector de Tienda/Bodega -->
          <!-- Selector de Tienda/Sede (solo si es Premium/Enterprise y tiene más de una bodega) -->
          <div v-if="shouldShowWarehouseSelector">
            <label for="warehouse_id" class="block text-sm font-semibold text-gray-900 mb-2">
              Tienda / Sede <span class="text-red-600">*</span>
            </label>
            <div class="relative">
              <select
                id="warehouse_id"
                v-model="formData.warehouse_id"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base font-medium appearance-none"
                :class="{ 'border-red-500 focus:ring-red-500': errors.warehouse_id }"
                required
                :disabled="loadingWarehouses"
              >
                <option value="" disabled>{{ loadingWarehouses ? 'Cargando tiendas...' : 'Seleccionar tienda' }}</option>
                <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
                  {{ warehouse.name }} {{ warehouse.is_default ? '(Principal)' : '' }}
                </option>
              </select>
              <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </div>
            <p v-if="errors.warehouse_id" class="mt-1 text-xs text-red-600">{{ errors.warehouse_id }}</p>
            <p class="mt-1 text-xs text-gray-500">Seleccione la tienda donde va a trabajar hoy</p>
          </div>

          <!-- Monto inicial -->
          <div>
            <label for="opening_amount" class="block text-sm font-semibold text-gray-900 mb-2">
              Monto Inicial en Caja <span class="text-red-600">*</span>
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">$</span>
              <input
                id="opening_amount"
                ref="amountInput"
                v-model="formData.opening_amount"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                class="w-full pl-8 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base font-medium"
                :class="{ 'border-red-500 focus:ring-red-500': errors.opening_amount }"
                required
              />
            </div>
            <p v-if="errors.opening_amount" class="mt-1 text-xs text-red-600">{{ errors.opening_amount }}</p>
            <p class="mt-1 text-xs text-gray-500">Ingrese el monto físico en la caja registradora</p>
          </div>

          <!-- Notas de apertura -->
          <div>
            <label for="opening_notes" class="block text-sm font-semibold text-gray-900 mb-2">
              Notas de Apertura <span class="text-gray-400 font-normal">(opcional)</span>
            </label>
            <textarea
              id="opening_notes"
              v-model="formData.opening_notes"
              rows="2"
              placeholder="Observaciones..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none text-sm"
              :class="{ 'border-red-500 focus:ring-red-500': errors.opening_notes }"
              maxlength="500"
            ></textarea>
            <div class="flex justify-between mt-1">
              <p v-if="errors.opening_notes" class="text-xs text-red-600">{{ errors.opening_notes }}</p>
              <p class="text-xs text-gray-500 ml-auto">{{ formData.opening_notes?.length || 0 }}/500</p>
            </div>
          </div>

          <!-- Warning mejorado y amigable -->
          <div v-if="forceOpen" class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 flex items-start space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="text-sm font-bold text-blue-900 mb-1">Para acceder a esta vista necesitas:</h4>
              <p class="text-sm text-blue-800">Abrir una caja para registrar ventas, o activar el modo cotización para crear presupuestos sin afectar el inventario.</p>
            </div>
          </div>
          <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex items-start space-x-2">
            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
              <h4 class="text-sm font-bold text-yellow-900">Importante</h4>
              <p class="text-xs text-yellow-800 mt-0.5">Asegúrese de contar correctamente el dinero inicial.</p>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex space-x-3 pt-2">
            <button
              v-if="forceOpen"
              type="button"
              @click="$emit('quotation-mode')"
              class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors flex items-center justify-center space-x-2"
              :disabled="isLoading"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <span>Solo Cotizaciones</span>
            </button>
            
            <button
              v-if="!forceOpen"
              type="button"
              @click="$emit('close')"
              class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium text-sm transition-colors"
              :disabled="isLoading"
            >
              Cancelar
            </button>
            <button
              type="submit"
              class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              :class="forceOpen ? 'flex-1' : 'flex-1'"
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
    
    console.log('🏪 Bodegas cargadas:', warehouses.value)
    console.log('📊 Plan actual:', appStore.tenantPlan)
    
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
      console.log('✅ Auto-seleccionada bodega:', formData.value.warehouse_id)
    } else {
      // Si es premium/enterprise y tiene múltiples bodegas, auto-seleccionar la por defecto si existe
      const defaultWarehouse = warehouses.value.find(w => w.is_default)
      if (defaultWarehouse) {
        formData.value.warehouse_id = defaultWarehouse.id
        console.log('✅ Auto-seleccionada bodega por defecto:', formData.value.warehouse_id)
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

/* Estilos para inputs focus */
input:focus, textarea:focus {
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

/* Disabled button styles */
button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

/* Loading spinner animation */
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