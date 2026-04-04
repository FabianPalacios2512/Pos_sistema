<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <div class="space-y-6">
      
      <!-- Header Profesional -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
              </div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Gestión de Usuarios
              </h1>
            </div>
            <p class="text-gray-600 mt-2">Administra empleados, roles y permisos del sistema</p>
          </div>
          
          <div class="flex items-center space-x-3">
            <button @click="exportUsers" 
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center space-x-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <span>Exportar</span>
            </button>
            <button @click="showAddUserModal = true" 
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center space-x-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <span>Nuevo Usuario</span>
            </button>
          </div>
        </div>
        
        <!-- Estadísticas de Usuarios -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-2xl text-white shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-100 text-sm font-semibold">Total Usuarios</p>
                <p class="text-2xl font-bold mt-1">{{ totalUsers }}</p>
                <p class="text-blue-200 text-xs mt-1">{{ activeUsers }} activos</p>
              </div>
              <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 rounded-2xl text-white shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-emerald-100 text-sm font-semibold">Administradores</p>
                <p class="text-2xl font-bold mt-1">{{ adminCount }}</p>
                <p class="text-emerald-200 text-xs mt-1">Acceso completo</p>
              </div>
              <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-6 rounded-2xl text-white shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-orange-100 text-sm font-semibold">Gerentes</p>
                <p class="text-2xl font-bold mt-1">{{ managerCount }}</p>
                <p class="text-orange-200 text-xs mt-1">Gestión operativa</p>
              </div>
              <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 rounded-2xl text-white shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-purple-100 text-sm font-semibold">Cajeros</p>
                <p class="text-2xl font-bold mt-1">{{ cashierCount }}</p>
                <p class="text-purple-200 text-xs mt-1">Punto de venta</p>
              </div>
              <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-2.599-3A2.998 2.998 0 0012 19c1.11 0 2.08-.402 2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Filtros y Búsqueda -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex-1 min-w-64">
            <input v-model="searchTerm" 
                   type="text" 
                   placeholder="Buscar por nombre, email o rol..." 
                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
          </div>
          
          <select v-model="roleFilter" 
                  class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white transition-all">
            <option value="">Todos los roles</option>
            <option value="admin">Administrador</option>
            <option value="manager">Gerente</option>
            <option value="cashier">Cajero</option>
          </select>
          
          <select v-model="statusFilter" 
                  class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white transition-all">
            <option value="">Todos los estados</option>
            <option value="active">Activos</option>
            <option value="inactive">Inactivos</option>
          </select>
        </div>
      </div>
      
      <!-- Lista de Usuarios -->
      <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Usuario</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rol</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Último Acceso</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Permisos</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="user in filteredUsers" :key="user.id" 
                  class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center space-x-3">
                    <div :class="[
                      'w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md',
                      getRoleColor(user.role?.name || 'default')
                    ]">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <p class="font-semibold text-gray-900">{{ user.name }}</p>
                      <p class="text-sm text-gray-600">{{ user.email }}</p>
                      <p class="text-xs text-gray-500">CC: {{ user.cc || user.document_number || 'No definido' }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                    getRoleBadge(user.role?.name || 'default')
                  ]">
                    {{ user.role?.name || 'Sin rol' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                    user.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                  ]">
                    {{ user.active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div>
                    <p class="text-sm text-gray-900">{{ user.last_login ? formatDate(user.last_login) : 'Nunca' }}</p>
                    <p class="text-xs text-gray-500">{{ user.last_login ? formatTime(user.last_login) : '' }}</p>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-1">
                    <template v-if="user.role?.permissions && user.role.permissions.length > 0">
                      <span v-for="permission in user.role.permissions.slice(0, 2)" :key="permission"
                            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-700">
                        {{ getPermissionName(permission) }}
                      </span>
                      <span v-if="user.role.permissions.length > 2"
                            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                        +{{ user.role.permissions.length - 2 }} más
                      </span>
                    </template>
                    <template v-else>
                      <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-500">
                        Sin permisos
                      </span>
                    </template>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center space-x-2">
                    <button @click="editUser(user)" 
                            class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm">
                      ✏️ Editar
                    </button>
                    <button @click="toggleUserStatus(user)" 
                            :class="[
                              'font-semibold text-sm',
                              user.active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'
                            ]">
                      {{ user.active ? '🚫 Desactivar' : '✅ Activar' }}
                    </button>
                    <button @click="resetPassword(user)" 
                            class="text-yellow-600 hover:text-yellow-800 font-semibold text-sm">
                      🔑 Reset
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Roles y Permisos -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Roles y Permisos del Sistema</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div v-for="role in systemRoles" :key="role.id" 
               class="p-6 border border-gray-200 rounded-xl hover:border-indigo-300 transition-colors">
            <div class="flex items-center space-x-3 mb-4">
              <div :class="['w-10 h-10 rounded-lg flex items-center justify-center text-white', role.color]">
                <span class="text-lg">{{ role.icon }}</span>
              </div>
              <div>
                <h4 class="font-bold text-gray-900">{{ role.name }}</h4>
                <p class="text-sm text-gray-600">{{ role.description }}</p>
              </div>
            </div>
            
            <div class="space-y-2">
              <h5 class="text-sm font-semibold text-gray-700">Permisos:</h5>
              <div class="flex flex-wrap gap-1">
                <span v-for="permission in role.permissions" :key="permission"
                      class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                  {{ permission }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
    
    <!-- Modal para Agregar/Editar Usuario -->
    <div v-if="showAddUserModal || editingUser" 
         class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">
          {{ editingUser ? 'Editar Usuario' : 'Nuevo Usuario' }}
        </h3>
        
        <div class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre Completo</label>
              <input v-model="userForm.name" 
                     type="text" 
                     placeholder="Ingrese el nombre completo"
                     class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Cédula de Identidad</label>
              <input v-model="userForm.cc" 
                     type="text" 
                     placeholder="Número de cédula"
                     class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
              <input v-model="userForm.email" 
                     type="email" 
                     placeholder="correo@ejemplo.com"
                     class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
              <input v-model="userForm.phone" 
                     type="text" 
                     placeholder="+57 300 000 0000"
                     class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Rol del Usuario</label>
            <select v-model="userForm.role_id" 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
              <option value="">Seleccionar rol...</option>
              <option v-for="role in availableRoles" :key="role.id" :value="role.id">
                {{ role.name }} - {{ role.description }}
              </option>
            </select>
          </div>
          
          <div v-if="!editingUser">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Contraseña</label>
            <input v-model="userForm.password" 
                   type="password" 
                   placeholder="Contraseña temporal"
                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <p class="text-xs text-gray-500 mt-1">La contraseña puede ser cambiada después por el usuario</p>
          </div>
          
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
            <span class="text-sm font-semibold text-gray-700">Usuario activo</span>
            <label class="relative inline-flex items-center cursor-pointer">
              <input v-model="userForm.active" type="checkbox" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
          </div>
        </div>
        
        <div class="flex items-center gap-3 mt-8">
          <button @click="closeUserModal" 
                  class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all">
            Cancelar
          </button>
          <button @click="saveUser" 
                  class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all">
            {{ editingUser ? 'Actualizar' : 'Crear' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import usersService from '../services/usersService.js'
import rolesService from '../services/rolesService.js'

// Estado reactivo
const searchTerm = ref('')
const roleFilter = ref('')
const statusFilter = ref('')
const showAddUserModal = ref(false)
const editingUser = ref(null)
const loading = ref(false)
const users = ref([])
const availableRoles = ref([])

// Roles del sistema para la vista informativa
const systemRoles = ref([
  {
    id: 1,
    name: 'Administrador',
    description: 'Control total del sistema',
    color: 'bg-gradient-to-br from-red-500 to-red-600',
    permissions: ['Gestión completa', 'Usuarios', 'Configuración', 'Reportes']
  },
  {
    id: 2,
    name: 'Cajero',
    description: 'Punto de venta y ventas',
    color: 'bg-gradient-to-br from-green-500 to-green-600',
    permissions: ['POS', 'Ventas', 'Clientes', 'Productos']
  },
  {
    id: 3,
    name: 'Vendedor',
    description: 'Ventas y atención',
    color: 'bg-gradient-to-br from-blue-500 to-blue-600',
    permissions: ['Ventas', 'Clientes', 'Consulta productos']
  }
])

// Formulario de usuario
const userForm = ref({
  name: '',
  email: '',
  cc: '', // Cédula
  phone: '',
  password: '',
  role_id: null,
  active: true
})

// Métodos para cargar datos
const loadUsers = async () => {
  loading.value = true
  try {
    const response = await usersService.getAllUsers()
    users.value = response.data || response
  } catch (error) {
    console.error('Error loading users:', error)
    users.value = []
  } finally {
    loading.value = false
  }
}

const loadRoles = async () => {
  try {
    const response = await rolesService.getAllRoles()
    availableRoles.value = response.data || response
  } catch (error) {
    console.error('Error loading roles:', error)
    availableRoles.value = []
  }
}

// Computed properties
const filteredUsers = computed(() => {
  let filtered = users.value

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(user => 
      user.name.toLowerCase().includes(term) ||
      user.email.toLowerCase().includes(term) ||
      (user.role?.name || '').toLowerCase().includes(term) ||
      (user.cc || user.document_number || '').includes(term)
    )
  }

  if (roleFilter.value) {
    filtered = filtered.filter(user => user.role?.id == roleFilter.value)
  }

  if (statusFilter.value) {
    const isActive = statusFilter.value === 'active'
    filtered = filtered.filter(user => user.active === isActive)
  }

  return filtered
})

const totalUsers = computed(() => users.value.length)
const activeUsers = computed(() => users.value.filter(user => user.active).length)
const adminCount = computed(() => users.value.filter(user => user.role?.name === 'Administrador').length)
const managerCount = computed(() => users.value.filter(user => user.role?.name === 'Gerente').length)
const cashierCount = computed(() => users.value.filter(user => user.role?.name === 'Cajero').length)

// Métodos
const getRoleColor = (roleName) => {
  const colors = {
    'Administrador': 'bg-gradient-to-br from-red-500 to-red-600',
    'Gerente': 'bg-gradient-to-br from-blue-500 to-blue-600',
    'Cajero': 'bg-gradient-to-br from-green-500 to-green-600',
    'Vendedor': 'bg-gradient-to-br from-purple-500 to-purple-600'
  }
  return colors[roleName] || 'bg-gradient-to-br from-gray-500 to-gray-600'
}

const getRoleBadge = (roleName) => {
  const badges = {
    'Administrador': 'bg-red-100 text-red-700',
    'Gerente': 'bg-blue-100 text-blue-700',
    'Cajero': 'bg-green-100 text-green-700',
    'Vendedor': 'bg-purple-100 text-purple-700'
  }
  return badges[roleName] || 'bg-gray-100 text-gray-700'
}

const getPermissionName = (permission) => {
  const permissionNames = {
    'dashboard.view': 'Dashboard',
    'products.view': 'Ver Productos',
    'sales.create': 'Crear Ventas',
    'users.view': 'Ver Usuarios',
    'reports.view': 'Ver Reportes'
  }
  return permissionNames[permission] || permission
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', { 
    day: '2-digit', 
    month: '2-digit', 
    year: 'numeric' 
  })
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString('es-ES', { 
    hour: '2-digit', 
    minute: '2-digit' 
  })
}

const editUser = (user) => {
  editingUser.value = user
  userForm.value = {
    name: user.name,
    email: user.email,
    cc: user.cc || user.document_number || '',
    phone: user.phone || '',
    role_id: user.role_id || user.role?.id,
    password: '',
    active: user.active
  }
}

const closeUserModal = () => {
  showAddUserModal.value = false
  editingUser.value = null
  userForm.value = {
    name: '',
    email: '',
    cc: '',
    phone: '',
    password: '',
    role_id: null,
    active: true
  }
}

const saveUser = async () => {
  // Validación frontend
  if (!userForm.value.name || !userForm.value.email || !userForm.value.cc) {
    alert('Por favor completa todos los campos obligatorios: Nombre, Email y Cédula')
    return
  }
  
  if (!editingUser.value && (!userForm.value.password || userForm.value.password.length < 6)) {
    alert('La contraseña es obligatoria y debe tener al menos 6 caracteres')
    return
  }
  
  if (!userForm.value.role_id) {
    alert('Por favor selecciona un rol para el usuario')
    return
  }

  loading.value = true
  try {
    
    if (editingUser.value) {
      // Actualizar usuario existente
      await usersService.updateUser(editingUser.value.id, userForm.value)
    } else {
      // Crear nuevo usuario
      await usersService.createUser(userForm.value)
    }
    
    // Recargar usuarios
    await loadUsers()
    
    // Cerrar modal
    closeUserModal()
    
    // Mostrar mensaje de éxito
    alert(editingUser.value ? 'Usuario actualizado exitosamente' : 'Usuario creado exitosamente')
    
  } catch (error) {
    console.error('Error saving user:', error)
    
    // Mostrar errores de validación específicos
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const errors = error.response.data.errors
      let errorMessage = 'Errores de validación:\n'
      
      Object.keys(errors).forEach(field => {
        errorMessage += `• ${field}: ${errors[field].join(', ')}\n`
      })
      
      alert(errorMessage)
    } else {
      alert('Error al guardar el usuario: ' + (error.response?.data?.message || error.message))
    }
  } finally {
    loading.value = false
  }
}

const toggleUserStatus = async (user) => {
  loading.value = true
  try {
    await usersService.updateUser(user.id, {
      ...user,
      active: !user.active
    })
    
    await loadUsers()
    alert(`Usuario ${user.active ? 'desactivado' : 'activado'} exitosamente`)
  } catch (error) {
    console.error('Error toggling user status:', error)
    alert('Error al cambiar el estado del usuario')
  } finally {
    loading.value = false
  }
}

const resetPassword = async (user) => {
  if (!confirm(`¿Está seguro de restablecer la contraseña de ${user.name}?`)) {
    return
  }
  
  loading.value = true
  try {
    await usersService.resetPassword(user.id)
    alert('Contraseña restablecida exitosamente')
  } catch (error) {
    console.error('Error resetting password:', error)
    alert('Error al restablecer la contraseña')
  } finally {
    loading.value = false
  }
}

const exportUsers = () => {
  // Implementar exportación de usuarios
  alert('Funcionalidad de exportación en desarrollo')
}

// Lifecycle
onMounted(async () => {
  await Promise.all([
    loadUsers(),
    loadRoles()
  ])
})
</script>

<style scoped>
.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}

/* Transiciones suaves */
* {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Toggle switch personalizado */
input[type="checkbox"]:checked + div {
  background-color: #4f46e5;
}

/* Scrollbar personalizada */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>