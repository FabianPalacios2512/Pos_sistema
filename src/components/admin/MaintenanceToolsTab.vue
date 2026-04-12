<template>
  <div class="space-y-4 lg:space-y-6">
    <!-- Quick Actions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      
      <!-- Clear All Cache -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 p-5 group hover:border-blue-300 dark:hover:border-blue-800 transition-all">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/50 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Limpiar Todo el Cache</h4>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-3">Cache de app, config, rutas y vistas de una vez</p>
            <button 
              @click="runAction('clear-all')"
              :disabled="runningAction === 'clear-all'"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all disabled:opacity-50 flex items-center gap-2"
            >
              <svg v-if="runningAction === 'clear-all'" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ runningAction === 'clear-all' ? 'Ejecutando...' : 'Ejecutar' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Clear App Cache -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 p-5 group hover:border-emerald-300 dark:hover:border-emerald-800 transition-all">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Cache de Aplicacion</h4>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-3">Limpiar cache almacenado por la app (datos, queries)</p>
            <button 
              @click="runAction('clear-cache')"
              :disabled="runningAction === 'clear-cache'"
              class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all disabled:opacity-50 flex items-center gap-2"
            >
              <svg v-if="runningAction === 'clear-cache'" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ runningAction === 'clear-cache' ? 'Limpiando...' : 'Limpiar Cache' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Clear Config Cache -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 p-5 group hover:border-amber-300 dark:hover:border-amber-800 transition-all">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/50 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Cache de Configuracion</h4>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-3">Recargar archivos de config (.env, config/*)</p>
            <button 
              @click="runAction('clear-config')"
              :disabled="runningAction === 'clear-config'"
              class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all disabled:opacity-50 flex items-center gap-2"
            >
              <svg v-if="runningAction === 'clear-config'" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ runningAction === 'clear-config' ? 'Limpiando...' : 'Limpiar Config' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Clear Route Cache -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 p-5 group hover:border-purple-300 dark:hover:border-purple-800 transition-all">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/50 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Cache de Rutas</h4>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-3">Recargar rutas de la API y web routes</p>
            <button 
              @click="runAction('clear-route')"
              :disabled="runningAction === 'clear-route'"
              class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all disabled:opacity-50 flex items-center gap-2"
            >
              <svg v-if="runningAction === 'clear-route'" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ runningAction === 'clear-route' ? 'Limpiando...' : 'Limpiar Rutas' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Clear View Cache -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 p-5 group hover:border-rose-300 dark:hover:border-rose-800 transition-all">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/50 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
            <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Cache de Vistas</h4>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-3">Recompilar templates Blade y vistas</p>
            <button 
              @click="runAction('clear-view')"
              :disabled="runningAction === 'clear-view'"
              class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all disabled:opacity-50 flex items-center gap-2"
            >
              <svg v-if="runningAction === 'clear-view'" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ runningAction === 'clear-view' ? 'Limpiando...' : 'Limpiar Vistas' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Optimize -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 p-5 group hover:border-indigo-300 dark:hover:border-indigo-800 transition-all">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Optimizar</h4>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-3">Cachear config, rutas y vistas para mejor rendimiento</p>
            <button 
              @click="runAction('optimize')"
              :disabled="runningAction === 'optimize'"
              class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all disabled:opacity-50 flex items-center gap-2"
            >
              <svg v-if="runningAction === 'optimize'" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ runningAction === 'optimize' ? 'Optimizando...' : 'Optimizar' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Migration Status -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 p-5 group hover:border-cyan-300 dark:hover:border-cyan-800 transition-all">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-xl bg-cyan-50 dark:bg-cyan-950/50 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
            <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Estado de Migraciones</h4>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-3">Ver estado de migraciones de la base de datos</p>
            <button 
              @click="runAction('migrate-status')"
              :disabled="runningAction === 'migrate-status'"
              class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all disabled:opacity-50 flex items-center gap-2"
            >
              <svg v-if="runningAction === 'migrate-status'" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ runningAction === 'migrate-status' ? 'Consultando...' : 'Ver Estado' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Storage Link -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 p-5 group hover:border-teal-300 dark:hover:border-teal-800 transition-all">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-950/50 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
            <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Storage Link</h4>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-3">Crear/recrear el symlink public/storage</p>
            <button 
              @click="runAction('storage-link')"
              :disabled="runningAction === 'storage-link'"
              class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all disabled:opacity-50 flex items-center gap-2"
            >
              <svg v-if="runningAction === 'storage-link'" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ runningAction === 'storage-link' ? 'Creando...' : 'Crear Link' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Execution History -->
    <div v-if="history.length" class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Historial de Ejecucion</h4>
        <button @click="history = []" class="text-xs text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">Limpiar</button>
      </div>
      <div class="divide-y divide-gray-100 dark:divide-zinc-800 max-h-96 overflow-y-auto">
        <div v-for="(entry, idx) in history" :key="idx" class="px-5 py-3">
          <div class="flex items-center gap-3 mb-1">
            <span 
              class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border"
              :class="entry.success 
                ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'"
            >{{ entry.success ? 'OK' : 'FAIL' }}</span>
            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ entry.action }}</span>
            <span class="text-xs text-gray-400 dark:text-zinc-500 ml-auto">{{ entry.time }}</span>
          </div>
          <pre v-if="entry.output" class="text-[11px] text-gray-600 dark:text-zinc-400 font-mono whitespace-pre-wrap bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 mt-2">{{ entry.output }}</pre>
          <p v-if="entry.error" class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ entry.error }}</p>
        </div>
      </div>
    </div>

    <!-- Environment Info -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Configuracion del Entorno</h4>
        <button 
          @click="fetchEnvironment" 
          :disabled="loadingEnv"
          class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors"
        >{{ loadingEnv ? 'Cargando...' : 'Cargar' }}</button>
      </div>
      
      <div v-if="envInfo" class="divide-y divide-gray-100 dark:divide-zinc-800">
        <template v-for="(section, sectionName) in envInfo" :key="sectionName">
          <div class="px-5 py-3 bg-gray-50 dark:bg-zinc-800/30">
            <span class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">{{ sectionName }}</span>
          </div>
          <div v-for="(value, key) in section" :key="key" class="px-5 py-2.5 flex justify-between items-center">
            <span class="text-sm text-gray-500 dark:text-zinc-400">{{ key }}</span>
            <span class="text-sm font-medium text-gray-900 dark:text-white font-mono">
              <template v-if="typeof value === 'boolean'">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold border"
                  :class="value 
                    ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                    : 'bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700'"
                >{{ value ? 'YES' : 'NO' }}</span>
              </template>
              <template v-else-if="Array.isArray(value)">
                {{ value.join(', ') || 'N/A' }}
              </template>
              <template v-else>{{ value || 'N/A' }}</template>
            </span>
          </div>
        </template>
      </div>
      <div v-else class="px-5 py-8 text-center text-sm text-gray-400 dark:text-zinc-500">
        Haz clic en "Cargar" para ver la configuracion del entorno
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const emit = defineEmits(['notify'])

const runningAction = ref('')
const history = ref([])
const loadingEnv = ref(false)
const envInfo = ref(null)

const runAction = async (action) => {
  runningAction.value = action
  try {
    const res = await axios.post('/api/admin/system/maintenance', { action })
    const entry = {
      action,
      success: res.data.success,
      output: res.data.output || '',
      error: '',
      time: new Date().toLocaleTimeString('es-ES'),
    }
    history.value.unshift(entry)
    emit('notify', 'success', res.data.message || `${action} completado`)
  } catch (e) {
    const entry = {
      action,
      success: false,
      output: '',
      error: e.response?.data?.message || e.message,
      time: new Date().toLocaleTimeString('es-ES'),
    }
    history.value.unshift(entry)
    emit('notify', 'error', 'Error', e.response?.data?.message || e.message)
  }
  runningAction.value = ''
}

const fetchEnvironment = async () => {
  loadingEnv.value = true
  try {
    const res = await axios.get('/api/admin/system/environment')
    if (res.data.success) {
      envInfo.value = res.data.data
    }
  } catch (e) {
    emit('notify', 'error', 'Error', e.response?.data?.message || e.message)
  }
  loadingEnv.value = false
}
</script>
