<template>
  <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-gray-900 rounded-2xl w-full max-w-6xl border border-gray-700 shadow-2xl my-8">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-800">
        <div>
          <h3 class="text-2xl font-bold text-white">Configuración de Tienda</h3>
          <p class="text-sm text-gray-400 mt-1">{{ tenant.business_name }} - {{ tenant.primary_domain }}</p>
        </div>
        <button @click="$emit('close')" class="text-gray-400 hover:text-white transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Tabs -->
      <div class="flex border-b border-gray-800 px-6">
        <button @click="activeTab = 'users'" :class="activeTab === 'users' ? 'border-purple-500 text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-300'" class="px-4 py-3 border-b-2 font-semibold text-sm transition-colors">
          👥 Usuarios
        </button>
        <button @click="activeTab = 'products'" :class="activeTab === 'products' ? 'border-purple-500 text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-300'" class="px-4 py-3 border-b-2 font-semibold text-sm transition-colors">
          📦 Productos
        </button>
        <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-purple-500 text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-300'" class="px-4 py-3 border-b-2 font-semibold text-sm transition-colors">
          ℹ️ Información
        </button>
      </div>

      <!-- Content -->
      <div class="p-6 max-h-[600px] overflow-y-auto">
        <!-- Tab: Usuarios -->
        <div v-if="activeTab === 'users'">
          <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-bold text-white">Usuarios de la Tienda</h4>
            <button @click="loadUsers" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-sm">
              <svg class="w-4 h-4" :class="{'animate-spin': loadingUsers}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
            </button>
          </div>

          <div v-if="loadingUsers" class="text-center py-8 text-gray-400">Cargando usuarios...</div>
          
          <div v-else-if="users.length === 0" class="text-center py-8 text-gray-400">No hay usuarios registrados</div>

          <div v-else class="space-y-3">
            <div v-for="user in users" :key="user.id" class="bg-gray-800 rounded-lg p-4 flex items-center justify-between border border-gray-700">
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-purple-700 rounded-full flex items-center justify-center">
                  <span class="text-white font-bold">{{ user.name.charAt(0).toUpperCase() }}</span>
                </div>
                <div>
                  <p class="text-white font-semibold">{{ user.name }}</p>
                  <p class="text-sm text-gray-400">{{ user.email }}</p>
                  <span v-if="user.role" class="text-xs px-2 py-1 bg-blue-500/20 text-blue-400 rounded-full mt-1 inline-block">
                    {{ user.role.name || user.role }}
                  </span>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button @click="resetPassword(user)" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-semibold transition-colors">
                  🔑 Resetear Contraseña
                </button>
                <span :class="user.active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'" class="px-3 py-1 text-xs font-bold rounded-full">
                  {{ user.active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab: Productos -->
        <div v-if="activeTab === 'products'">
          <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-bold text-white">Productos de la Tienda</h4>
            <button @click="loadProducts" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-sm">
              <svg class="w-4 h-4" :class="{'animate-spin': loadingProducts}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
            </button>
          </div>

          <div v-if="loadingProducts" class="text-center py-8 text-gray-400">Cargando productos...</div>
          
          <div v-else-if="products.length === 0" class="text-center py-8 text-gray-400">No hay productos registrados</div>

          <div v-else>
            <p class="text-sm text-gray-400 mb-3">Total: {{ products.length }} productos</p>
            <div class="grid grid-cols-2 gap-4">
              <div v-for="product in products.slice(0, 20)" :key="product.id" class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                <p class="text-white font-semibold text-sm mb-1">{{ product.name }}</p>
                <p class="text-emerald-400 font-bold">${{ parseFloat(product.price).toFixed(2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Stock: {{ product.stock || 0 }}</p>
              </div>
            </div>
            <p v-if="products.length > 20" class="text-center text-gray-500 text-sm mt-4">
              ... y {{ products.length - 20 }} productos más
            </p>
          </div>
        </div>

        <!-- Tab: Información -->
        <div v-if="activeTab === 'info'">
          <h4 class="text-lg font-bold text-white mb-4">Información de la Tienda</h4>
          <div class="space-y-4">
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
              <p class="text-sm text-gray-400 mb-1">ID de Tenant</p>
              <p class="text-white font-mono">{{ tenant.id }}</p>
            </div>
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
              <p class="text-sm text-gray-400 mb-1">Nombre del Negocio</p>
              <p class="text-white">{{ tenant.business_name || 'N/A' }}</p>
            </div>
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
              <p class="text-sm text-gray-400 mb-1">Dominio Principal</p>
              <p class="text-white font-mono">{{ tenant.primary_domain }}</p>
            </div>
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
              <p class="text-sm text-gray-400 mb-1">Plan Actual</p>
              <span class="px-3 py-1 bg-purple-600 text-white text-sm font-bold rounded-full">
                {{ tenant.plan.toUpperCase() }}
              </span>
            </div>
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
              <p class="text-sm text-gray-400 mb-1">Estado</p>
              <span :class="tenant.status === 'active' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'" class="px-3 py-1 text-sm font-bold rounded-full">
                {{ tenant.status === 'active' ? 'ACTIVO' : tenant.status.toUpperCase() }}
              </span>
            </div>
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
              <p class="text-sm text-gray-400 mb-1">Fecha de Creación</p>
              <p class="text-white">{{ new Date(tenant.created_at).toLocaleString('es-ES') }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-800">
        <button @click="$emit('close')" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
          Cerrar
        </button>
      </div>
    </div>

    <!-- Modal: Resetear Contraseña -->
    <div v-if="showResetPasswordModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-[60] p-4">
      <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full border border-amber-500">
        <h4 class="text-xl font-bold text-white mb-4">🔑 Resetear Contraseña</h4>
        <p class="text-gray-300 mb-4">Usuario: <strong>{{ selectedUser?.name }}</strong></p>
        <input v-model="newPassword" type="text" placeholder="Nueva contraseña" class="w-full px-4 py-3 bg-gray-900 text-white rounded-lg border border-gray-700 focus:border-amber-500 focus:outline-none mb-4">
        <div class="flex space-x-3">
          <button @click="showResetPasswordModal = false; selectedUser = null; newPassword = ''" class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold">
            Cancelar
          </button>
          <button @click="confirmResetPassword" class="flex-1 px-4 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-semibold">
            Confirmar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  tenant: {
    type: Object,
    required: true
  }
})

defineEmits(['close', 'refresh'])

const activeTab = ref('users')
const users = ref([])
const products = ref([])
const loadingUsers = ref(false)
const loadingProducts = ref(false)
const showResetPasswordModal = ref(false)
const selectedUser = ref(null)
const newPassword = ref('')

// Cargar usuarios
const loadUsers = async () => {
  loadingUsers.value = true
  try {
    const res = await axios.get(`/admin/api/tenants/${props.tenant.id}/users`)
    if (res.data.success) {
      users.value = res.data.data
    }
  } catch (error) {
    console.error('Error loading users:', error)
    alert('Error al cargar usuarios')
  }
  loadingUsers.value = false
}

// Cargar productos
const loadProducts = async () => {
  loadingProducts.value = true
  try {
    const res = await axios.get(`/admin/api/tenants/${props.tenant.id}/products`)
    if (res.data.success) {
      products.value = res.data.data
    }
  } catch (error) {
    console.error('Error loading products:', error)
    alert('Error al cargar productos')
  }
  loadingProducts.value = false
}

// Resetear contraseña
const resetPassword = (user) => {
  selectedUser.value = user
  newPassword.value = ''
  showResetPasswordModal.value = true
}

const confirmResetPassword = async () => {
  if (!newPassword.value) {
    alert('Por favor ingresa una contraseña')
    return
  }
  
  try {
    const res = await axios.post(`/admin/api/tenants/${props.tenant.id}/users/${selectedUser.value.id}/reset-password`, {
      password: newPassword.value
    })
    
    if (res.data.success) {
      alert(`✅ Contraseña actualizada!\n\nUsuario: ${selectedUser.value.email}\nNueva contraseña: ${newPassword.value}`)
      showResetPasswordModal.value = false
      selectedUser.value = null
      newPassword.value = ''
    }
  } catch (error) {
    console.error('Error:', error)
    alert('❌ Error al resetear contraseña: ' + (error.response?.data?.message || error.message))
  }
}

// Cargar usuarios al montar
onMounted(() => {
  loadUsers()
})
</script>
