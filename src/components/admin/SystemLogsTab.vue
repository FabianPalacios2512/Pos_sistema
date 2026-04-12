<template>
  <div class="space-y-4 lg:space-y-6">
    <!-- Filtros -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 p-4">
      <div class="flex flex-col lg:flex-row gap-3">
        <!-- Search -->
        <div class="relative flex-1">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input 
            v-model="filters.search" 
            @keyup.enter="fetchLogs"
            type="text" 
            placeholder="Buscar en logs..." 
            class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        <!-- Level Filter -->
        <select 
          v-model="filters.level" 
          @change="fetchLogs"
          class="px-3 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Todos los niveles</option>
          <option value="emergency">Emergency</option>
          <option value="alert">Alert</option>
          <option value="critical">Critical</option>
          <option value="error">Error</option>
          <option value="warning">Warning</option>
          <option value="notice">Notice</option>
          <option value="info">Info</option>
          <option value="debug">Debug</option>
        </select>

        <!-- File Selector -->
        <select 
          v-model="filters.file" 
          @change="fetchLogs"
          class="px-3 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Auto (hoy)</option>
          <option v-for="f in logFiles" :key="f.name" :value="f.name">{{ f.name }} ({{ f.size_mb }} MB)</option>
        </select>

        <!-- Actions -->
        <div class="flex gap-2">
          <button 
            @click="fetchLogs" 
            :disabled="loading"
            class="px-4 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/30 dark:shadow-slate-900/50 transition-all disabled:opacity-50 flex items-center gap-2"
          >
            <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Buscar
          </button>
          <button 
            @click="showClearConfirm = true"
            class="px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-rose-600 dark:text-rose-400 text-sm font-bold rounded-xl border border-rose-200 dark:border-rose-800/50 transition-all flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span class="hidden sm:inline">Limpiar</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Level Summary Pills -->
    <div v-if="levelCounts" class="flex flex-wrap gap-2">
      <button 
        v-for="(count, lvl) in levelCounts" :key="lvl"
        v-show="count > 0"
        @click="filters.level = filters.level === lvl ? '' : lvl; fetchLogs()"
        class="px-3 py-1.5 rounded-lg text-xs font-bold border transition-all"
        :class="getLevelPillClass(lvl, filters.level === lvl)"
      >
        {{ lvl.toUpperCase() }}: {{ count }}
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading && !logs.length" class="flex items-center justify-center py-16">
      <div class="text-center">
        <svg class="w-10 h-10 animate-spin text-blue-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-sm text-gray-500 dark:text-zinc-400">Leyendo logs...</p>
      </div>
    </div>

    <!-- Logs Table -->
    <div v-if="logs.length" class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden">
      <div class="px-5 py-3 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
        <p class="text-sm text-gray-600 dark:text-zinc-400">
          <span class="font-bold text-gray-900 dark:text-white">{{ total }}</span> entradas 
          <span v-if="currentFile" class="hidden sm:inline">en <span class="font-mono text-xs">{{ currentFile }}</span></span>
        </p>
        <div class="flex items-center gap-2">
          <button 
            @click="changePage(-1)" 
            :disabled="filters.page <= 1" 
            class="p-1.5 rounded-lg border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-30 transition-all"
          >
            <svg class="w-4 h-4 text-gray-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <span class="text-xs text-gray-500 dark:text-zinc-400">{{ filters.page }}/{{ totalPages }}</span>
          <button 
            @click="changePage(1)" 
            :disabled="filters.page >= totalPages" 
            class="p-1.5 rounded-lg border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-30 transition-all"
          >
            <svg class="w-4 h-4 text-gray-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="divide-y divide-gray-100 dark:divide-zinc-800">
        <div 
          v-for="(log, idx) in logs" 
          :key="idx" 
          class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors"
        >
          <!-- Log Entry -->
          <div class="px-5 py-3 flex items-start gap-3 cursor-pointer" @click="toggleTrace(idx)">
            <!-- Level Badge -->
            <span 
              class="mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide flex-shrink-0 border"
              :class="getLevelBadgeClass(log.level)"
            >{{ log.level }}</span>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <p class="text-sm text-gray-900 dark:text-zinc-200 font-mono leading-relaxed break-all">{{ log.message }}</p>
              <div class="flex items-center gap-3 mt-1">
                <span class="text-[10px] text-gray-400 dark:text-zinc-500 font-mono">{{ log.timestamp }}</span>
                <span class="text-[10px] text-gray-400 dark:text-zinc-500">{{ log.channel }}</span>
                <span v-if="log.has_trace" class="text-[10px] text-blue-500 dark:text-blue-400 font-medium">
                  {{ expandedTraces.has(idx) ? 'Ocultar trace' : 'Ver trace' }}
                </span>
              </div>
            </div>

            <!-- Expand icon -->
            <svg v-if="log.has_trace" class="w-4 h-4 text-gray-400 dark:text-zinc-500 flex-shrink-0 mt-1 transition-transform" :class="{'rotate-180': expandedTraces.has(idx)}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </div>

          <!-- Stack Trace (expandable) -->
          <div v-if="log.has_trace && expandedTraces.has(idx)" class="px-5 pb-4">
            <pre class="bg-gray-900 dark:bg-black text-green-400 text-[11px] p-4 rounded-lg overflow-x-auto max-h-80 font-mono leading-relaxed">{{ log.stack_trace }}</pre>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && !logs.length && !error" class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 p-12 text-center">
      <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-950/50 rounded-xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Sin logs</h3>
      <p class="text-sm text-gray-500 dark:text-zinc-400">No se encontraron entradas con los filtros seleccionados</p>
    </div>

    <!-- Error -->
    <div v-if="error" class="bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 rounded-xl p-6 text-center">
      <p class="text-sm font-medium text-rose-700 dark:text-rose-400">{{ error }}</p>
      <button @click="fetchLogs" class="mt-3 px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-lg hover:bg-rose-700 transition-colors">Reintentar</button>
    </div>

    <!-- Clear Confirm Modal -->
    <Teleport to="body">
      <div v-if="showClearConfirm" class="fixed inset-0 bg-black/60 flex items-center justify-center z-[9999]" @click.self="showClearConfirm = false">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full mx-4 border border-gray-200 dark:border-zinc-800 p-6">
          <div class="text-center">
            <div class="w-14 h-14 bg-rose-100 dark:bg-rose-950 rounded-xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-7 h-7 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Limpiar Logs</h3>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mb-6">Esto eliminara el contenido de todos los archivos de log. Esta accion no se puede deshacer.</p>
            <div class="flex gap-3">
              <button 
                @click="showClearConfirm = false" 
                class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-all"
              >Cancelar</button>
              <button 
                @click="clearLogs" 
                :disabled="clearing"
                class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-xl shadow-lg transition-all disabled:opacity-50"
              >{{ clearing ? 'Limpiando...' : 'Limpiar Todo' }}</button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const emit = defineEmits(['notify'])

const loading = ref(false)
const clearing = ref(false)
const error = ref('')
const logs = ref([])
const logFiles = ref([])
const currentFile = ref('')
const total = ref(0)
const totalPages = ref(1)
const levelCounts = ref(null)
const showClearConfirm = ref(false)
const expandedTraces = ref(new Set())

const filters = reactive({
  search: '',
  level: '',
  file: '',
  page: 1,
})

const fetchLogs = async () => {
  loading.value = true
  error.value = ''
  try {
    const params = {
      page: filters.page,
      per_page: 50,
    }
    if (filters.search) params.search = filters.search
    if (filters.level) params.level = filters.level
    if (filters.file) params.file = filters.file

    const res = await axios.get('/api/admin/system/logs', { params })
    if (res.data.success) {
      const d = res.data.data
      logs.value = d.logs || []
      logFiles.value = d.log_files || []
      currentFile.value = d.current_file || ''
      total.value = d.total || 0
      totalPages.value = d.total_pages || 1
      levelCounts.value = d.level_counts || null
      expandedTraces.value = new Set()
    }
  } catch (e) {
    error.value = e.response?.data?.message || e.message
  }
  loading.value = false
}

const clearLogs = async () => {
  clearing.value = true
  try {
    await axios.delete('/api/admin/system/logs')
    showClearConfirm.value = false
    emit('notify', 'success', 'Logs limpiados', 'Todos los archivos de log fueron vaciados')
    fetchLogs()
  } catch (e) {
    emit('notify', 'error', 'Error', e.response?.data?.message || e.message)
  }
  clearing.value = false
}

const changePage = (delta) => {
  filters.page = Math.max(1, Math.min(totalPages.value, filters.page + delta))
  fetchLogs()
}

const toggleTrace = (idx) => {
  if (expandedTraces.value.has(idx)) {
    expandedTraces.value.delete(idx)
  } else {
    expandedTraces.value.add(idx)
  }
  expandedTraces.value = new Set(expandedTraces.value)
}

const getLevelBadgeClass = (level) => {
  const classes = {
    emergency: 'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800',
    alert: 'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800',
    critical: 'bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800',
    error: 'bg-orange-100 dark:bg-orange-950 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-800',
    warning: 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
    notice: 'bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800',
    info: 'bg-sky-100 dark:bg-sky-950 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-800',
    debug: 'bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-400 border-gray-200 dark:border-zinc-700',
  }
  return classes[level] || classes.debug
}

const getLevelPillClass = (level, active) => {
  if (active) return 'bg-slate-900 dark:bg-slate-700 text-white border-slate-900 dark:border-slate-700'
  return getLevelBadgeClass(level)
}

onMounted(() => {
  fetchLogs()
})
</script>
