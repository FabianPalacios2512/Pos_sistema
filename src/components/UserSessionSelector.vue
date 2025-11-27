<template>
  <div class="user-session-info">
    <!-- Info del usuario actual -->
    <div class="current-user-info bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
            {{ getInitials(currentUser.name) }}
          </div>
          <div>
            <p class="font-semibold text-gray-900">{{ currentUser.name }}</p>
            <p class="text-xs text-gray-500">{{ currentUser.email }}</p>
          </div>
        </div>
        
        <!-- Selector de usuario -->
        <div class="relative">
          <button 
            @click="showUserSelector = !showUserSelector"
            class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100"
            title="Cambiar usuario"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
            </svg>
          </button>
          
          <!-- Dropdown de usuarios -->
          <div v-if="showUserSelector" class="absolute right-0 top-full mt-1 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
            <div class="p-3 border-b border-gray-100">
              <p class="text-sm font-medium text-gray-900">Usuarios Disponibles</p>
              <p class="text-xs text-gray-500">Selecciona un usuario para trabajar</p>
            </div>
            <div class="max-h-60 overflow-y-auto">
              <div 
                v-for="user in availableUsers" 
                :key="user.id"
                @click="selectUser(user)"
                :class="[
                  'flex items-center space-x-3 p-3 hover:bg-gray-50 cursor-pointer border-l-4 transition-colors',
                  user.id === currentUser.id ? 'border-blue-500 bg-blue-50' : 'border-transparent'
                ]"
              >
                <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-xs">
                  {{ getInitials(user.name) }}
                </div>
                <div class="flex-1">
                  <p class="font-medium text-gray-900 text-sm">{{ user.name }}</p>
                  <p class="text-xs text-gray-500">{{ user.email }}</p>
                  <div class="flex items-center space-x-2 mt-1">
                    <span v-if="user.hasOpenSession" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      Caja Abierta
                    </span>
                    <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      Caja Cerrada
                    </span>
                  </div>
                </div>
                <div v-if="user.id === currentUser.id" class="text-blue-500">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Botón para refrescar información de usuarios -->
    <button 
      @click="refreshUsers"
      :disabled="loading"
      class="text-xs text-blue-600 hover:text-blue-800 font-medium disabled:opacity-50"
    >
      {{ loading ? 'Actualizando...' : 'Refrescar usuarios' }}
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import authService from '../services/authService.js'
import { cashSessionService } from '../services/cashSessionService.js'
import { apiCall } from '../services/api.js'

const props = defineProps({
  currentUser: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['user-changed'])

// Estados reactivos
const showUserSelector = ref(false)
const availableUsers = ref([])
const loading = ref(false)

// Función para obtener iniciales del nombre
const getInitials = (name) => {
  if (!name) return '?'
  return name.split(' ').map(word => word.charAt(0)).join('').toUpperCase().slice(0, 2)
}

// Función para cargar usuarios disponibles
const loadAvailableUsers = async () => {
  try {
    loading.value = true
    
    // Obtener lista de usuarios (necesitaremos crear este endpoint)
    const usersData = await apiCall('/users')
    
    if (usersData && usersData.data) {
      
      // Para cada usuario, verificar si tiene sesión abierta
      const usersWithSessions = await Promise.all(
        usersData.data.map(async (user) => {
          try {
            // Verificar sesión de caja de cada usuario
            const sessionData = await apiCall(`/cash-sessions/user/${user.id}/current`)
            
            return {
              ...user,
              hasOpenSession: sessionData.success && sessionData.session !== null
            }
          } catch (error) {
            return {
              ...user,
              hasOpenSession: false
            }
          }
        })
      )
      
      availableUsers.value = usersWithSessions
    }
  } catch (error) {
    console.error('Error cargando usuarios:', error)
  } finally {
    loading.value = false
  }
}

// Función para seleccionar usuario
const selectUser = async (user) => {
  if (user.id === props.currentUser.id) {
    showUserSelector.value = false
    return
  }
  
  try {
    // Cambiar usuario actual (esto requerirá implementar cambio de contexto)
    emit('user-changed', user)
    showUserSelector.value = false
  } catch (error) {
    console.error('Error cambiando usuario:', error)
  }
}

// Función para refrescar usuarios
const refreshUsers = () => {
  loadAvailableUsers()
}

// Cargar usuarios al montar
onMounted(() => {
  loadAvailableUsers()
})

// Cerrar dropdown al hacer clic fuera
const handleClickOutside = (event) => {
  if (!event.target.closest('.user-session-info')) {
    showUserSelector.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Componente UserSessionSelector */
</style>