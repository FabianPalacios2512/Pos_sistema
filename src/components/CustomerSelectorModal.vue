<template>
  <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="$emit('close')">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-2xl w-full animate-scale-in border border-gray-200 dark:border-zinc-800">
      
      <!-- Header -->
      <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Seleccionar Cliente</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">Elige un cliente para la venta</p>
          </div>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl p-2 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Búsqueda y Crear Cliente -->
      <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-900">
        <div class="flex gap-3">
          <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
              <svg class="h-4 w-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <input
              ref="searchInputRef"
              v-model="searchTerm"
              type="text"
              placeholder="Buscar por nombre o documento..."
              class="block w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
              @input="currentPage = 1"
            />
          </div>
          <button @click="showCreateCustomer = true" 
                  class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2 shadow-lg shadow-emerald-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Nuevo</span>
          </button>
        </div>
        
        <p class="text-xs text-gray-500 dark:text-zinc-500 mt-2">
          {{ loading ? 'Cargando...' : `Mostrando ${paginatedCustomers.length} de ${filteredCustomers.length} clientes` }}
        </p>
      </div>

      <!-- Lista de Clientes -->
      <div class="px-6 py-4 max-h-[400px] overflow-y-auto">
        
        <!-- Cliente General (por defecto) -->
        <div 
          @click="selectCustomer(null)"
          class="flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 dark:border-zinc-800 cursor-pointer hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:border-emerald-300 dark:hover:border-emerald-800 transition-all mb-3 group"
        >
          <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center border-2 border-gray-200 dark:border-zinc-700">
            <svg class="w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="font-semibold text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Consumidor Final</h4>
            <p class="text-xs text-gray-500 dark:text-zinc-500">CC: 222222222222 · Sin datos adicionales</p>
          </div>
          <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide rounded-full bg-gray-100 dark:bg-zinc-800 text-gray-500 dark:text-zinc-500">General</span>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-10">
          <div class="w-8 h-8 border-3 border-gray-200 dark:border-zinc-700 border-t-emerald-500 rounded-full animate-spin mx-auto"></div>
          <p class="text-sm text-gray-400 dark:text-zinc-500 mt-3">Cargando clientes...</p>
        </div>

        <!-- Sin resultados -->
        <div v-else-if="filteredCustomers.length === 0 && searchTerm" class="text-center py-10">
          <div class="w-14 h-14 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">No se encontraron clientes</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">para "{{ searchTerm }}"</p>
          <button 
            @click="showCreateCustomer = true"
            class="mt-3 text-sm font-semibold text-emerald-600 dark:text-emerald-400 hover:underline"
          >
            + Crear nuevo cliente
          </button>
        </div>

        <!-- Lista de clientes reales -->
        <div v-else class="space-y-2">
          <div 
            v-for="customer in paginatedCustomers"
            :key="customer.id"
            @click="selectCustomer(customer)"
            class="flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 dark:border-zinc-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-800/50 hover:border-gray-300 dark:hover:border-zinc-700 transition-all group"
          >
            <!-- Avatar con color pastel según inicial -->
            <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold" 
                 :style="{ backgroundColor: getAvatarColor(customer.name).bg, color: getAvatarColor(customer.name).text }">
              {{ customer.name?.charAt(0).toUpperCase() || '?' }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <h4 class="font-semibold text-gray-900 dark:text-white truncate">
                  {{ customer.name }}
                </h4>
                <span v-if="customer.active" class="px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide rounded bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400">Activo</span>
                <span v-else class="px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide rounded bg-gray-100 dark:bg-zinc-800 text-gray-500 dark:text-zinc-500">Inactivo</span>
              </div>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                {{ customer.document_type || 'CC' }}: {{ customer.document_number }}
                <span v-if="customer.phone"> · {{ customer.phone }}</span>
              </p>
              
            </div>
            
            <!-- Indicadores compactos a la derecha -->
            <div class="flex items-center gap-2 flex-shrink-0">
              <!-- Crédito disponible -->
              <span v-if="isCreditiendaEnabled && customer.credit_active && (customer.credit_limit - customer.current_debt) > 0" 
                    class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                ${{ formatCurrency(customer.credit_limit - customer.current_debt) }}
              </span>
              <!-- Puntos -->
              <span v-if="isLoyaltyEnabled && (customer.loyalty_points || 0) > 0"
                    class="text-xs text-amber-600 dark:text-amber-400 font-medium">
                {{ customer.loyalty_points }}pts
              </span>
              <!-- Flecha -->
              <svg class="w-4 h-4 text-gray-300 dark:text-zinc-600 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>
          
          <!-- Botón cargar más -->
          <button 
            v-if="hasMoreCustomers"
            @click="loadMoreCustomers"
            class="w-full py-3 mt-2 text-center text-sm font-medium text-gray-500 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-xl transition-all border border-dashed border-gray-200 dark:border-zinc-700"
          >
            Cargar más clientes ({{ filteredCustomers.length - paginatedCustomers.length }} restantes)
          </button>
        </div>
      </div>

      <!-- Modal Crear Cliente Rápido -->
      <div v-if="showCreateCustomer" 
           class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-60"
           @click.self="showCreateCustomer = false">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl max-w-md w-full shadow-2xl border border-gray-200 dark:border-zinc-800 overflow-hidden">
          
          <!-- Header -->
          <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800 bg-gradient-to-r from-emerald-50 to-white dark:from-zinc-900 dark:to-zinc-900">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/40 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nuevo Cliente</h3>
                <p class="text-sm text-gray-500 dark:text-zinc-400">Registro rápido para la venta</p>
              </div>
            </div>
          </div>
          
          <!-- Form -->
          <div class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">Nombre completo *</label>
              <input v-model="quickCustomer.name" 
                     type="text" 
                     class="w-full px-4 py-3 text-sm border border-gray-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                     placeholder="Ej: María García">
            </div>
            
            <div class="grid grid-cols-5 gap-3">
              <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">Tipo Doc.</label>
                <select v-model="quickCustomer.document_type" 
                        class="w-full px-3 py-3 text-sm border border-gray-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                  <option value="CC">CC</option>
                  <option value="NIT">NIT</option>
                  <option value="CE">CE</option>
                  <option value="TI">TI</option>
                  <option value="PP">Pasaporte</option>
                </select>
              </div>
              <div class="col-span-3">
                <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">Número *</label>
                <input v-model="quickCustomer.document_number" 
                       type="text" 
                       class="w-full px-4 py-3 text-sm border border-gray-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="12345678">
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">Teléfono</label>
                <input v-model="quickCustomer.phone" 
                       type="text" 
                       class="w-full px-4 py-3 text-sm border border-gray-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="300 123 4567">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">Email</label>
                <input v-model="quickCustomer.email" 
                       type="email" 
                       class="w-full px-4 py-3 text-sm border border-gray-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="correo@ejemplo.com">
              </div>
            </div>
          </div>
          
          <!-- Footer -->
          <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-950 border-t border-gray-100 dark:border-zinc-800 flex gap-3">
            <button @click="showCreateCustomer = false" 
                    class="flex-1 px-4 py-3 text-sm font-semibold text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition-colors border border-gray-200 dark:border-zinc-700">
              Cancelar
            </button>
            <button @click="createQuickCustomer" 
                    :disabled="!quickCustomer.name || !quickCustomer.document_number || creating"
                    class="flex-1 px-4 py-3 text-sm font-semibold bg-emerald-500 hover:bg-emerald-600 disabled:bg-gray-200 dark:disabled:bg-zinc-800 disabled:text-gray-400 dark:disabled:text-zinc-600 text-white rounded-xl transition-all shadow-lg shadow-emerald-500/20 disabled:shadow-none flex items-center justify-center gap-2">
              <svg v-if="creating" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ creating ? 'Creando...' : 'Crear y Seleccionar' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { customersService } from '../services/customersService.js'
import { appStore } from '../store/appStore.js'
import { useToast } from '../composables/useToast.js'

// Emits
const emit = defineEmits(['select', 'close', 'view-history'])

const props = defineProps({
  startCreating: {
    type: Boolean,
    default: false
  }
})

// Sistema de toasts
const { showSuccess, showError } = useToast()

// Referencia al input de búsqueda
const searchInputRef = ref(null)

// Estado
const searchTerm = ref('')
const loading = ref(false)
const showCreateCustomer = ref(false)
const creating = ref(false)

// Paginación
const currentPage = ref(1)
const itemsPerPage = 15

// Usar clientes del store global
const customers = computed(() => appStore.customers)

// Verificar si las funcionalidades están activas
const isCreditiendaEnabled = computed(() => {
  return appStore.systemSettings?.creditienda_enabled === 1 || appStore.systemSettings?.creditienda_enabled === true
})

const isLoyaltyEnabled = computed(() => {
  return appStore.systemSettings?.enable_loyalty_system === 1 || appStore.systemSettings?.enable_loyalty_system === true
})

// Colores pastel para avatares
const avatarColors = [
  { bg: '#FEE2E2', text: '#991B1B' }, // Rose
  { bg: '#FEF3C7', text: '#92400E' }, // Amber
  { bg: '#D1FAE5', text: '#065F46' }, // Emerald
  { bg: '#DBEAFE', text: '#1E40AF' }, // Blue
  { bg: '#E0E7FF', text: '#3730A3' }, // Indigo
  { bg: '#EDE9FE', text: '#5B21B6' }, // Violet
  { bg: '#FCE7F3', text: '#9D174D' }, // Pink
  { bg: '#CFFAFE', text: '#0E7490' }, // Cyan
  { bg: '#F3E8FF', text: '#6B21A8' }, // Purple
  { bg: '#ECFCCB', text: '#3F6212' }, // Lime
]

const getAvatarColor = (name) => {
  if (!name) return avatarColors[0]
  const charCode = name.charCodeAt(0)
  return avatarColors[charCode % avatarColors.length]
}

const quickCustomer = ref({
  name: '',
  document_type: 'CC',
  document_number: '',
  phone: '',
  email: '',
  active: true
})

// Computed
const filteredCustomers = computed(() => {
  // 🛡️ Filtrar el Consumidor Final (cliente del sistema) - No debe mostrarse en selectores
  const systemCustomers = (customers.value || []).filter(c => c.document_number !== '222222222222')
  
  if (!searchTerm.value) return systemCustomers
  
  const term = searchTerm.value.toLowerCase()
  return systemCustomers.filter(customer => 
    customer.name && customer.name.toLowerCase().includes(term) ||
    customer.document_number && customer.document_number.includes(term) ||
    (customer.email && customer.email.toLowerCase().includes(term)) ||
    (customer.phone && customer.phone.toLowerCase().includes(term))
  )
})

// Paginación: mostrar solo los primeros N clientes
const paginatedCustomers = computed(() => {
  const end = currentPage.value * itemsPerPage
  return filteredCustomers.value.slice(0, end)
})

// Verificar si hay más clientes por cargar
const hasMoreCustomers = computed(() => {
  return paginatedCustomers.value.length < filteredCustomers.value.length
})

// Cargar más clientes
const loadMoreCustomers = () => {
  currentPage.value++
}

// Métodos
const selectCustomer = (customer) => {
  console.log('Cliente seleccionado:', customer)
  emit('select', customer)
}

const viewCustomerHistory = (customer) => {
  console.log('Ver historial del cliente:', customer)
  emit('view-history', customer)
}

const createQuickCustomer = async () => {
  try {
    creating.value = true
    
    // Preparar datos del cliente
    const customerData = {
      name: quickCustomer.value.name,
      document_type: quickCustomer.value.document_type,
      document_number: quickCustomer.value.document_number,
      phone: quickCustomer.value.phone,
      active: quickCustomer.value.active
    }
    
    // Solo agregar email si tiene contenido y es válido
    if (quickCustomer.value.email && quickCustomer.value.email.includes('@')) {
      customerData.email = quickCustomer.value.email
    }
    
    // Crear el cliente
    const response = await customersService.create(customerData)
    console.log('Cliente creado:', response)
    
    // Recargar lista de clientes
  // Refrescar lista desde el store global
  await appStore.loadCustomers()
    
    // Seleccionar el cliente recién creado
    const newCustomer = response.data
    selectCustomer(newCustomer)
    
    // Limpiar formulario
    quickCustomer.value = {
      name: '',
      document_type: 'CC',
      document_number: '',
      phone: '',
      email: '',
      active: true
    }
    
    showCreateCustomer.value = false
    
    showSuccess('Cliente creado y seleccionado exitosamente')
    
  } catch (error) {
    console.error('Error creando cliente:', error)
    showError('Error al crear el cliente: ' + (error.message || 'Error desconocido'))
  } finally {
    creating.value = false
  }
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO').format(parseFloat(value || 0))
}

// Inicialización
onMounted(async () => {
  if (props.startCreating) {
    showCreateCustomer.value = true
  }

  // Usar el loader del store global para evitar fetchs locales redundantes
  if (!appStore.customers || appStore.customers.length === 0) {
    appStore.loadCustomers()
  }

  // 🎯 AUTOFOCUS: Enfocar el input de búsqueda cuando se abre el modal
  await nextTick()
  if (searchInputRef.value && !props.startCreating) {
    searchInputRef.value.focus()
  }
})
</script>

<style scoped>
@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-scale-in {
  animation: scaleIn 0.2s ease-out;
}

.z-60 {
  z-index: 60;
}
</style>