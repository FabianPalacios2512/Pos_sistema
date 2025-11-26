<template>
  <div class="min-h-screen bg-gray-100 font-sans">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      
      <!-- Header Elegante -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Panel de Administrador</h1>
            <p class="text-sm text-gray-600">Monitoreo de IA y API Keys de Groq</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Selector de período -->
          <select v-model="selectedPeriod" @change="loadData" 
                  class="px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-purple-500">
            <option value="24h">Últimas 24h</option>
            <option value="7d">Últimos 7 días</option>
            <option value="30d">Últimos 30 días</option>
            <option value="all">Todo el tiempo</option>
          </select>
          
          <!-- Botón Refrescar -->
          <button @click="loadData" 
                  class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Actualizar</span>
          </button>
        </div>
      </div>

      <!-- Métricas Principales -->
      <div v-if="!loading && summary" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Peticiones -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-semibold text-gray-700">Total Peticiones</h3>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ summary.total_requests }}</p>
              <p class="text-sm text-gray-500">{{ summary.success_rate }}% exitosas</p>
            </div>
          </div>
        </div>

        <!-- Tokens Usados -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-semibold text-gray-700">Tokens Consumidos</h3>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatNumber(summary.total_tokens) }}</p>
              <p class="text-sm text-gray-500">{{ summary.successful }} exitosos</p>
            </div>
          </div>
        </div>

        <!-- Rate Limited -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-semibold text-gray-700">Rate Limited</h3>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ summary.rate_limited }}</p>
              <p class="text-sm text-gray-500">Keys saturadas</p>
            </div>
          </div>
        </div>

        <!-- Tiempo Promedio -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-semibold text-gray-700">Tiempo Promedio</h3>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ summary.avg_response_time_ms }}ms</p>
              <p class="text-sm text-gray-500">Respuesta IA</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Estado de las API Keys -->
      <div class="bg-white rounded-2xl p-5 border border-gray-300">
        <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 -mx-5 -mt-5 mb-5 flex items-center justify-between rounded-t-2xl">
          <div>
            <h2 class="text-base font-bold text-gray-900">Estado de API Keys en Tiempo Real</h2>
            <p class="text-xs text-gray-500 mt-0.5">Semáforo de disponibilidad de las {{ keysStatus.length }} keys activas</p>
          </div>
          
          <!-- Indicador Global -->
          <div v-if="keysStatus.length > 0" class="flex items-center space-x-3">
            <div v-if="availableKeysCount > 0" class="flex items-center space-x-2 px-4 py-2 bg-green-50 border border-green-200 rounded-lg">
              <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
              <span class="text-sm font-bold text-green-700">{{ availableKeysCount }} keys disponibles</span>
            </div>
            <div v-else class="flex items-center space-x-2 px-4 py-2 bg-red-50 border border-red-200 rounded-lg">
              <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
              <span class="text-sm font-bold text-red-700">TODAS SATURADAS</span>
            </div>
          </div>
        </div>

        <div v-if="loading" class="flex justify-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
        </div>

        <div v-else-if="keysStatus.length === 0" class="text-center py-12">
          <p class="text-gray-500">No hay datos disponibles</p>
        </div>

        <!-- TABLA DE SEMÁFORO -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Estado</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Key</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Peticiones</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Exitosas</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Rate Limited</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Tokens</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Tiempo Prom.</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="key in keysStatus" :key="key.key_index"
                  class="hover:bg-gray-50 transition-colors"
                  :class="{
                    'bg-green-50': key.status === 'active',
                    'bg-red-50': key.status === 'rate_limited'
                  }">
                
                <!-- SEMÁFORO -->
                <td class="px-4 py-4">
                  <div class="flex items-center space-x-2">
                    <!-- Luz de semáforo -->
                    <div class="relative w-8 h-8 rounded-full flex items-center justify-center"
                         :class="{
                           'bg-green-100': key.status === 'active',
                           'bg-red-100': key.status === 'rate_limited'
                         }">
                      <div class="w-4 h-4 rounded-full animate-pulse"
                           :class="{
                             'bg-green-500': key.status === 'active',
                             'bg-red-500': key.status === 'rate_limited'
                           }"></div>
                    </div>
                    <!-- Label -->
                    <span class="text-xs font-bold uppercase"
                          :class="{
                            'text-green-700': key.status === 'active',
                            'text-red-700': key.status === 'rate_limited'
                          }">
                      {{ key.status === 'active' ? 'DISPONIBLE' : 'SATURADA' }}
                    </span>
                  </div>
                </td>

                <!-- KEY INFO -->
                <td class="px-4 py-4">
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm bg-purple-100 text-purple-700">
                      #{{ key.key_index }}
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900">API Key {{ key.key_index }}</p>
                      <p class="text-xs text-gray-500 font-mono">...{{ key.key_last_4 }}</p>
                    </div>
                  </div>
                </td>

                <!-- PETICIONES -->
                <td class="px-4 py-4 text-center">
                  <span class="text-lg font-bold text-gray-900">{{ key.total_requests }}</span>
                </td>

                <!-- EXITOSAS -->
                <td class="px-4 py-4 text-center">
                  <div class="flex flex-col items-center">
                    <span class="text-lg font-bold text-green-600">{{ key.successful }}</span>
                    <span class="text-xs text-gray-500">{{ getSuccessRate(key) }}%</span>
                  </div>
                </td>

                <!-- RATE LIMITED -->
                <td class="px-4 py-4 text-center">
                  <span class="text-lg font-bold text-red-600">{{ key.rate_limited }}</span>
                </td>

                <!-- TOKENS -->
                <td class="px-4 py-4 text-center">
                  <span class="text-sm font-semibold text-gray-900">{{ formatNumber(key.total_tokens) }}</span>
                </td>

                <!-- TIEMPO PROMEDIO -->
                <td class="px-4 py-4 text-center">
                  <span class="text-sm font-semibold"
                        :class="{
                          'text-green-600': key.avg_response_time < 1000,
                          'text-yellow-600': key.avg_response_time >= 1000 && key.avg_response_time < 2000,
                          'text-red-600': key.avg_response_time >= 2000
                        }">
                    {{ key.avg_response_time }}ms
                  </span>
                </td>

                <!-- ACCIONES -->
                <td class="px-4 py-4 text-center">
                  <button @click="viewKeyDetails(key.key_index)" 
                          class="px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 text-xs font-semibold rounded-lg transition-colors">
                    Detalles
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Leyenda del semáforo -->
        <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-center space-x-6 text-xs">
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            <span class="text-gray-600">Disponible (puede enviar mensajes)</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
            <span class="text-gray-600">Saturada (rate limited)</span>
          </div>
        </div>
      </div>

      <!-- Peticiones Recientes -->
      <div class="bg-white rounded-2xl p-5 border border-gray-300">
        <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 -mx-5 -mt-5 mb-5 flex items-center justify-between rounded-t-2xl">
          <div>
            <h2 class="text-base font-bold text-gray-900">Peticiones Recientes</h2>
            <p class="text-xs text-gray-500 mt-0.5">Últimas 20 consultas a la IA</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Usuario</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Mensaje</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Key</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Tokens</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Estado</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Tiempo</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Fecha</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="request in recentRequests" :key="request.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-3 py-3 text-sm text-gray-900 font-medium">{{ request.user }}</td>
                <td class="px-3 py-3 text-sm text-gray-600 max-w-md truncate">{{ request.message_preview }}</td>
                <td class="px-3 py-3 text-sm">
                  <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">
                    #{{ request.key_index }}
                  </span>
                </td>
                <td class="px-3 py-3 text-sm text-gray-900 font-semibold">{{ request.tokens }}</td>
                <td class="px-3 py-3 text-sm">
                  <span class="px-2 py-1 rounded-full text-xs font-semibold"
                        :class="{
                          'bg-green-100 text-green-700': request.status === 'success',
                          'bg-red-100 text-red-700': request.status === 'rate_limited',
                          'bg-gray-100 text-gray-700': request.status === 'error'
                        }">
                    {{ request.status === 'success' ? 'Éxito' : request.status === 'rate_limited' ? 'Rate Limited' : 'Error' }}
                  </span>
                </td>
                <td class="px-3 py-3 text-sm text-gray-600">{{ request.response_time }}ms</td>
                <td class="px-3 py-3 text-sm text-gray-500">{{ formatDate(request.timestamp) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top Usuarios -->
      <div v-if="topUsers.length > 0" class="bg-white rounded-2xl p-5 border border-gray-300">
        <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 -mx-5 -mt-5 mb-5 rounded-t-2xl">
          <h2 class="text-base font-bold text-gray-900">Top Usuarios</h2>
          <p class="text-xs text-gray-500 mt-0.5">Quienes más usan la IA</p>
        </div>

        <div class="space-y-3">
          <div v-for="(user, index) in topUsers" :key="user.user_id"
               class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-sm">
                {{ index + 1 }}
              </div>
              <div>
                <p class="font-semibold text-gray-900">{{ user.user_name }}</p>
                <p class="text-xs text-gray-500">{{ user.user_email }}</p>
              </div>
            </div>
            <div class="flex items-center space-x-6 text-sm">
              <div class="text-right">
                <p class="text-gray-600">Peticiones</p>
                <p class="font-bold text-gray-900">{{ user.total_requests }}</p>
              </div>
              <div class="text-right">
                <p class="text-gray-600">Tokens</p>
                <p class="font-bold text-gray-900">{{ formatNumber(user.total_tokens) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import apiClient from '@/services/apiClient'

const loading = ref(true)
const selectedPeriod = ref('24h')
const summary = ref(null)
const keysStatus = ref([])
const recentRequests = ref([])
const topUsers = ref([])

const loadData = async () => {
  loading.value = true
  try {
    const response = await apiClient.get(`/admin/ai-monitoring/dashboard?period=${selectedPeriod.value}`)
    
    summary.value = response.data.summary
    keysStatus.value = response.data.keys_status
    recentRequests.value = response.data.recent_requests
    topUsers.value = response.data.top_users
    
    console.log('📊 Dashboard data loaded:', response.data)
  } catch (error) {
    console.error('Error loading dashboard:', error)
  } finally {
    loading.value = false
  }
}

const viewKeyDetails = (keyIndex) => {
  console.log('Ver detalles de key:', keyIndex)
  // TODO: Abrir modal con detalles de la key
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('es-CO').format(num)
}

const formatDate = (timestamp) => {
  const date = new Date(timestamp)
  const now = new Date()
  const diff = now - date
  
  // Si es menos de 1 hora, mostrar minutos
  if (diff < 3600000) {
    const minutes = Math.floor(diff / 60000)
    return `Hace ${minutes}m`
  }
  
  // Si es menos de 24 horas, mostrar horas
  if (diff < 86400000) {
    const hours = Math.floor(diff / 3600000)
    return `Hace ${hours}h`
  }
  
  // Si no, mostrar fecha
  return date.toLocaleDateString('es-CO', {
    day: '2-digit',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Calcular tasa de éxito
const getSuccessRate = (key) => {
  if (key.total_requests === 0) return 0;
  return Math.round((key.successful / key.total_requests) * 100);
}

// Computed properties
const availableKeysCount = computed(() => {
  return keysStatus.value.filter(key => key.status === 'active').length;
})

onMounted(() => {
  loadData()
  
  // Auto-refresh cada 30 segundos
  setInterval(() => {
    loadData()
  }, 30000)
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
