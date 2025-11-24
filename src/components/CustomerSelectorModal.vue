<template>
  <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="$emit('close')">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full animate-scale-in">
      
      <!-- Header -->
      <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Seleccionar Cliente</h3>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg p-2 transition-all min-w-[40px] min-h-[40px] flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Búsqueda y Crear Cliente -->
      <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex space-x-3 mb-4">
          <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Buscar por nombre, cédula, email o teléfono..."
              class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <button @click="showCreateCustomer = true" 
                  class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Crear Cliente</span>
          </button>
        </div>
        
        <p class="text-sm text-gray-500 dark:text-gray-400">
          {{ loading ? 'Cargando clientes...' : `${filteredCustomers.length} clientes encontrados` }}
        </p>
      </div>

      <!-- Lista de Clientes -->
      <div class="p-6 max-h-96 overflow-y-auto">
        
        <!-- Cliente General (por defecto) -->
        <div 
          @click="selectCustomer(null)"
          class="flex items-center space-x-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors mb-3"
        >
          <div class="w-12 h-12 bg-gray-400 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="font-medium text-gray-900 dark:text-white">Cliente General</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">Venta sin cliente específico</p>
          </div>
          <div class="text-blue-600 dark:text-blue-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-8">
          <div class="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full mx-auto mb-4"></div>
          <p class="text-gray-500 dark:text-gray-400">Cargando clientes...</p>
        </div>

        <!-- Sin resultados -->
        <div v-else-if="filteredCustomers.length === 0 && searchTerm" class="text-center py-8">
          <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          <p class="text-gray-500 dark:text-gray-400 mb-3">No se encontraron clientes</p>
          <button 
            @click="showCreateCustomer = true"
            class="text-blue-600 dark:text-blue-400 font-medium hover:underline"
          >
            ¿Crear nuevo cliente?
          </button>
        </div>

        <!-- Lista de clientes reales -->
        <div v-else class="space-y-2">
          <div 
            v-for="customer in filteredCustomers"
            :key="customer.id"
            @click="selectCustomer(customer)"
            class="flex items-center space-x-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300 dark:hover:border-blue-600 transition-all group"
          >
            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center group-hover:bg-blue-700 transition-colors">
              <span class="text-white font-medium text-sm">{{ customer.name.charAt(0).toUpperCase() }}</span>
            </div>
            <div class="flex-1">
              <h4 class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                {{ customer.name }}
              </h4>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ customer.document_type }}: {{ customer.document_number }}
              </p>
              <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400 mt-1">
                <span class="flex items-center space-x-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                  <span>{{ customer.phone || 'Sin teléfono' }}</span>
                </span>
                <span class="flex items-center space-x-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                  </svg>
                  <span>${{ formatCurrency(customer.total_purchases || 0) }}</span>
                </span>
              </div>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <div :class="customer.active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'" 
                   class="px-2 py-1 rounded-full text-xs font-medium">
                {{ customer.active ? 'Activo' : 'Inactivo' }}
              </div>
              
              <!-- Botones de acción -->
              <div class="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button
                  @click.stop="viewCustomerHistory(customer)"
                  class="w-8 h-8 bg-purple-600 hover:bg-purple-700 text-white rounded-full flex items-center justify-center transition-colors"
                  title="Ver historial de compras"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </button>
                <div class="text-blue-600 dark:text-blue-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Crear Cliente Rápido -->
      <div v-if="showCreateCustomer" 
           class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-60"
           @click.self="showCreateCustomer = false">
        <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-xl">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Crear Cliente Rápido</h3>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo</label>
              <input v-model="quickCustomer.name" 
                     type="text" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                     placeholder="Ej: María García">
            </div>
            
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Doc.</label>
                <select v-model="quickCustomer.document_type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="CC">Cédula</option>
                  <option value="NIT">NIT</option>
                  <option value="CE">C.E.</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Número</label>
                <input v-model="quickCustomer.document_number" 
                       type="text" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="12345678">
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
              <input v-model="quickCustomer.phone" 
                     type="text" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                     placeholder="300 123 4567">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email (opcional)</label>
              <input v-model="quickCustomer.email" 
                     type="email" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                     placeholder="correo@ejemplo.com">
            </div>
          </div>
          
          <div class="flex space-x-3 mt-6">
            <button @click="showCreateCustomer = false" 
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300">
              Cancelar
            </button>
            <button @click="createQuickCustomer" 
                    :disabled="!quickCustomer.name || !quickCustomer.document_number || creating"
                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-medium">
              {{ creating ? 'Creando...' : 'Crear y Seleccionar' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { customersService } from '../services/customersService.js'
import { appStore } from '../store/appStore.js'
import { useToast } from '../composables/useToast.js'

// Emits
const emit = defineEmits(['select', 'close', 'view-history'])

// Sistema de toasts
const { showSuccess, showError } = useToast()

// Estado
const searchTerm = ref('')
const loading = ref(false)
const showCreateCustomer = ref(false)
const creating = ref(false)

// Usar clientes del store global
const customers = computed(() => appStore.customers)

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
  if (!searchTerm.value) return customers.value
  
  const term = searchTerm.value.toLowerCase()
  return customers.value.filter(customer => 
    customer.name && customer.name.toLowerCase().includes(term) ||
    customer.document_number && customer.document_number.includes(term) ||
    (customer.email && customer.email.toLowerCase().includes(term)) ||
    (customer.phone && customer.phone.toLowerCase().includes(term))
  )
})

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
onMounted(() => {
  // Usar el loader del store global para evitar fetchs locales redundantes
  if (!appStore.customers || appStore.customers.length === 0) {
    appStore.loadCustomers()
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