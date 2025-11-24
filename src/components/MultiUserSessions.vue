<template>
  <div class="multi-user-sessions bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
          <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          Sesiones de Caja Activas
        </h3>
        <p class="text-sm text-gray-500">Estado de todas las cajas en tiempo real</p>
      </div>
      
      <button 
        @click="refreshSessions"
        :disabled="loading"
        class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 disabled:opacity-50"
      >
        <svg :class="['w-4 h-4', loading ? 'animate-spin' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
      </button>
    </div>

    <!-- Lista de sesiones activas -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-flex items-center space-x-2 text-gray-500">
        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        <span>Cargando sesiones...</span>
      </div>
    </div>

    <div v-else-if="activeSessions.length === 0" class="text-center py-8">
      <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
      </svg>
      <p class="text-gray-500 font-medium">No hay sesiones de caja activas</p>
      <p class="text-sm text-gray-400">Los usuarios deben abrir sus cajas para comenzar a trabajar</p>
    </div>

    <div v-else class="space-y-3">
      <div 
        v-for="session in activeSessions" 
        :key="session.id"
        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition-colors"
      >
        <div class="flex items-center space-x-4">
          <!-- Avatar del usuario -->
          <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
            {{ getInitials(session.user.name) }}
          </div>
          
          <!-- Info del usuario y sesión -->
          <div>
            <p class="font-semibold text-gray-900">{{ session.user.name }}</p>
            <div class="flex items-center space-x-4 text-sm text-gray-500">
              <span>Sesión #{{ session.id }}</span>
              <span>•</span>
              <span>Desde {{ formatTime(session.opening_time) }}</span>
              <span>•</span>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></div>
                Activa
              </span>
            </div>
          </div>
        </div>
        
        <!-- Métricas de la sesión -->
        <div class="flex items-center space-x-6">
          <div class="text-right">
            <p class="text-lg font-bold text-gray-900">${{ formatCurrency(session.total_sales) }}</p>
            <p class="text-xs text-gray-500">Total ventas</p>
          </div>
          
          <div class="text-right">
            <p class="text-sm font-semibold text-green-600">${{ formatCurrency(session.opening_amount) }}</p>
            <p class="text-xs text-gray-500">Monto inicial</p>
          </div>
          
          <!-- Acciones -->
          <div class="flex items-center space-x-2">
            <button 
              @click="viewSessionDetails(session)"
              class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
              title="Ver detalles"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
            </button>
            
            <button 
              @click="closeSession(session)"
              class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors"
              title="Cerrar sesión"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Resumen total -->
    <div v-if="activeSessions.length > 0" class="mt-6 pt-4 border-t border-gray-200">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-gray-600">
            <span class="font-medium">{{ activeSessions.length }}</span> 
            {{ activeSessions.length === 1 ? 'sesión activa' : 'sesiones activas' }}
          </p>
        </div>
        <div class="text-right">
          <p class="text-lg font-bold text-gray-900">
            ${{ formatCurrency(totalSalesAllSessions) }}
          </p>
          <p class="text-xs text-gray-500">Total todas las cajas</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Estados reactivos
const activeSessions = ref([])
const loading = ref(false)

// Computed para totales
const totalSalesAllSessions = computed(() => {
  return activeSessions.value.reduce((total, session) => total + (session.total_sales || 0), 0)
})

// Funciones utilitarias
const getInitials = (name) => {
  if (!name) return '?'
  return name.split(' ').map(word => word.charAt(0)).join('').toUpperCase().slice(0, 2)
}

const formatCurrency = (amount) => {
  if (!amount && amount !== 0) return '0'
  return new Intl.NumberFormat('es-CO', { 
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount)
}

const formatTime = (timeString) => {
  if (!timeString) return 'N/A'
  return timeString.slice(0, 5) // Tomar solo HH:MM
}

// Función para cargar sesiones activas
const loadActiveSessions = async () => {
  try {
    loading.value = true
    
    // Obtener lista de usuarios
    const usersResponse = await fetch('http://localhost:8000/api/users', {
      headers: { 'Accept': 'application/json' }
    })
    
    if (!usersResponse.ok) {
      // Si no tenemos endpoint de usuarios, simular con datos conocidos
      const mockUsers = [
        { id: 1, name: 'Administrador Sistema', email: 'admin@pos.com' },
        { id: 2, name: 'Vendedor Principal', email: 'vendedor@pos.com' }
      ]
      
      await loadSessionsForUsers(mockUsers)
      return
    }
    
    const usersData = await usersResponse.json()
    await loadSessionsForUsers(usersData.data || usersData)
    
  } catch (error) {
    console.error('Error cargando sesiones activas:', error)
  } finally {
    loading.value = false
  }
}

// Función para cargar sesiones de usuarios específicos
const loadSessionsForUsers = async (users) => {
  const sessionsPromises = users.map(async (user) => {
    try {
      const response = await fetch(`http://localhost:8000/api/cash-sessions/user/${user.id}/current`, {
        headers: { 'Accept': 'application/json' }
      })
      
      const data = await response.json()
      
      if (data.success && data.session) {
        return {
          ...data.session,
          user: user
        }
      }
      return null
    } catch (error) {
      console.error(`Error cargando sesión del usuario ${user.id}:`, error)
      return null
    }
  })
  
  const sessions = await Promise.all(sessionsPromises)
  activeSessions.value = sessions.filter(session => session !== null)
}

// Función para refrescar sesiones
const refreshSessions = () => {
  loadActiveSessions()
}

// Funciones de acción
const viewSessionDetails = (session) => {
  console.log('Ver detalles de sesión:', session)
  // Aquí podrías abrir un modal con detalles de la sesión
}

const closeSession = (session) => {
  console.log('Cerrar sesión:', session)
  // Aquí podrías abrir un modal para cerrar la sesión
}

// Cargar sesiones al montar
onMounted(() => {
  loadActiveSessions()
  
  // Refrescar cada 30 segundos
  setInterval(loadActiveSessions, 30000)
})
</script>

<style scoped>
/* Componente MultiUserSessions */
</style>