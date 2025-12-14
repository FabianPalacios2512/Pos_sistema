<template>
  <div class="min-h-screen bg-gray-50 p-4 lg:p-6">
    <div class="space-y-4">
      
      <!-- Header con Tabs -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios y Roles</h1>
              <p class="text-sm text-gray-500 mt-0.5">Administra empleados, roles y permisos del sistema</p>
            </div>
          </div>
          
          <!-- Tabs Navigation -->
          <div class="flex space-x-1 border-b border-gray-200">
            <button @click="activeTab = 'users'"
                    :class="activeTab === 'users' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                <span>Usuarios</span>
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">{{ users.length }}</span>
              </div>
            </button>
            <button @click="activeTab = 'roles'"
                    :class="activeTab === 'roles' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Roles y Permisos</span>
                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">{{ roles.length }}</span>
              </div>
            </button>
          </div>
        </div>
        
        <!-- Métricas según Tab Activo -->
        <div class="p-4">
          <!-- Métricas de Usuarios -->
          <div v-if="activeTab === 'users'" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow duration-200" 
                 style="background-color: #EBF2FF;">
              <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                  </svg>
                </div>
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Total</span>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5">Total Usuarios</p>
                <p class="text-xl font-bold text-gray-900">{{ users.length }}</p>
                <p class="text-xs text-gray-500 mt-1.5">{{ activeUsersCount }} activos</p>
              </div>
            </div>
            
            <div class="rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow duration-200" 
                 style="background-color: #E6FFF1;">
              <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Activo</span>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5">Usuarios Activos</p>
                <p class="text-xl font-bold text-gray-900">{{ activeUsersCount }}</p>
                <p class="text-xs text-gray-500 mt-1.5">{{ inactiveUsersCount }} inactivos</p>
              </div>
            </div>
            
            <div class="rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow duration-200" 
                 style="background-color: #FFF9E6;">
              <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center">
                  <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                  </svg>
                </div>
                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Sesiones</span>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5">Sesiones Hoy</p>
                <p class="text-xl font-bold text-gray-900">{{ todayLoginCount }}</p>
                <p class="text-xs text-gray-500 mt-1.5">Últimas 24 horas</p>
              </div>
            </div>
            
            <div class="rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow duration-200" 
                 style="background-color: #FFE6E6;">
              <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                  <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                </div>
                <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Alertas</span>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5">Sin Actividad</p>
                <p class="text-xl font-bold text-gray-900">{{ inactiveUsersCount }}</p>
                <p class="text-xs text-gray-500 mt-1.5">Últimos 30 días</p>
              </div>
            </div>
          </div>
          
          <!-- Métricas de Roles -->
          <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow duration-200" 
                 style="background-color: #F3E8FF;">
              <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                  </svg>
                </div>
                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">Total</span>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5">Total Roles</p>
                <p class="text-xl font-bold text-gray-900">{{ roles.length }}</p>
                <p class="text-xs text-gray-500 mt-1.5">En el sistema</p>
              </div>
            </div>
            
            <div class="rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow duration-200" 
                 style="background-color: #E6FFF1;">
              <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Activo</span>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5">Roles Activos</p>
                <p class="text-xl font-bold text-gray-900">{{ activeRolesCount }}</p>
                <p class="text-xs text-gray-500 mt-1.5">Con usuarios asignados</p>
              </div>
            </div>
            
            <div class="rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow duration-200" 
                 style="background-color: #EBF2FF;">
              <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                  </svg>
                </div>
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Info</span>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5">Permisos Totales</p>
                <p class="text-xl font-bold text-gray-900">{{ totalPermissionsCount }}</p>
                <p class="text-xs text-gray-500 mt-1.5">Configurables</p>
              </div>
            </div>
            
            <div class="rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow duration-200" 
                 style="background-color: #FFF9E6;">
              <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center">
                  <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                </div>
                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Admin</span>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5">Rol Admin</p>
                <p class="text-xl font-bold text-gray-900">{{ adminRoleUsers }}</p>
                <p class="text-xs text-gray-500 mt-1.5">Usuarios con acceso completo</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Contenido según Tab -->
      <div v-if="activeTab === 'users'">
        <!-- USUARIOS TAB CONTENT AQUÍ -->
        <p class="text-gray-500">Tab de Usuarios en desarrollo...</p>
      </div>
      
      <div v-else>
        <!-- ROLES TAB CONTENT AQUÍ -->
        <p class="text-gray-500">Tab de Roles en desarrollo...</p>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Estado reactivo
const activeTab = ref('users')
const loading = ref(false)
const users = ref([])
const roles = ref([])

// Computed properties para Usuarios
const activeUsersCount = computed(() => users.value.filter(u => u.active).length)
const inactiveUsersCount = computed(() => users.value.filter(u => !u.active).length)
const todayLoginCount = computed(() => {
  // TODO: Implementar lógica real
  return 12
})

// Computed properties para Roles
const activeRolesCount = computed(() => roles.value.filter(r => r.users_count > 0).length)
const totalPermissionsCount = computed(() => {
  // 12 módulos con permisos granulares
  return 65
})
const adminRoleUsers = computed(() => {
  const adminRole = roles.value.find(r => r.name.toLowerCase() === 'administrador')
  return adminRole ? adminRole.users_count : 0
})

// Métodos
const loadUsers = async () => {
  try {
    loading.value = true
    // TODO: Implementar llamada API
    users.value = []
  } catch (error) {
    console.error('Error cargando usuarios:', error)
  } finally {
    loading.value = false
  }
}

const loadRoles = async () => {
  try {
    loading.value = true
    // TODO: Implementar llamada API
    roles.value = []
  } catch (error) {
    console.error('Error cargando roles:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadUsers()
  loadRoles()
})
</script>
