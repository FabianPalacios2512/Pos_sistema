<template>
  <div class="space-y-6">

    <!-- ═══════════════════════════════════════════════════ -->
    <!-- KPIs de Asistencia -->
    <!-- ═══════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <!-- Entradas Hoy -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Entradas Hoy</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">{{ summary.entries_today ?? 0 }}</p>
          </div>
        </div>
      </div>

      <!-- Salidas Hoy -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Salidas Hoy</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">{{ summary.exits_today ?? 0 }}</p>
          </div>
        </div>
      </div>

      <!-- Usuarios Enrolados -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Enrolados</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">{{ summary.enrolled_users ?? 0 }}<span class="text-sm font-normal text-gray-400 dark:text-zinc-500">/{{ summary.total_users ?? 0 }}</span></p>
          </div>
        </div>
      </div>

      <!-- Pendientes -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30"
           :class="{ 'border-amber-300 dark:border-amber-800/60': (summary.pending_enroll ?? 0) > 0 }">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5"
               :class="{ 'bg-amber-50 dark:bg-amber-950 border-amber-200 dark:border-amber-800': (summary.pending_enroll ?? 0) > 0 }">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Sin Enrolar</p>
            <p class="text-xl font-bold mt-0.5" :class="(summary.pending_enroll ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">{{ summary.pending_enroll ?? 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════ -->
    <!-- Punteo Biométrico (panel principal) -->
    <!-- ═══════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- Panel de Cámara -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
              Verificación Facial
            </h3>
            <div class="flex items-center gap-2">
              <button v-if="!isCameraActive" @click="initializeCamera"
                      :disabled="isModelLoading"
                      class="px-3 py-1.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:opacity-40 text-white text-xs font-bold rounded-lg transition-all">
                {{ isModelLoading ? 'Cargando...' : 'Iniciar Cámara' }}
              </button>
              <button v-else @click="stopVerification"
                      class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold rounded-lg transition-all">
                Detener
              </button>
            </div>
          </div>
        </div>

        <div class="p-5">
          <!-- Camera viewport -->
          <div class="relative rounded-xl overflow-hidden bg-zinc-950 aspect-[4/3]">
            <video ref="verifyVideoRef" class="w-full h-full object-cover" playsinline muted></video>
            <canvas ref="verifyOverlayRef" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

            <!-- No camera state -->
            <div v-if="!isCameraActive" class="absolute inset-0 flex items-center justify-center">
              <div class="text-center">
                <svg class="w-16 h-16 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <p class="text-sm text-zinc-500 font-medium">Inicie la cámara para verificar</p>
                <p class="text-xs text-zinc-600 mt-1">Se cargarán los modelos de IA automáticamente</p>
              </div>
            </div>

            <!-- Camera active indicators -->
            <template v-if="isCameraActive">
              <div class="absolute top-3 left-3 flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold bg-black/50 text-white backdrop-blur-sm">
                  <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                  EN VIVO
                </span>
              </div>

              <!-- Match/No-match large indicator -->
              <div v-if="verificationResult" class="absolute bottom-3 left-3 right-3">
                <div class="rounded-lg px-3 py-2 backdrop-blur-sm text-center"
                     :class="verificationResult.match
                       ? 'bg-emerald-600/80 border border-emerald-400/30'
                       : 'bg-rose-600/80 border border-rose-400/30'">
                  <p class="text-white text-sm font-bold">
                    {{ verificationResult.match ? 'IDENTIDAD VERIFICADA' : 'NO RECONOCIDO' }}
                  </p>
                  <p class="text-white/70 text-[10px]">
                    Distancia: {{ verificationResult.distance }} / Umbral: {{ MATCH_THRESHOLD }}
                  </p>
                </div>
              </div>
            </template>
          </div>

          <!-- Error display -->
          <div v-if="modelError" class="mt-3 bg-rose-50 dark:bg-rose-950/50 rounded-lg p-3 border border-rose-100 dark:border-rose-900/50">
            <p class="text-xs text-rose-600 dark:text-rose-400 font-medium">{{ modelError }}</p>
          </div>

          <!-- Event type selector + Confirm button -->
          <div v-if="isCameraActive && baseDescriptor" class="mt-4 space-y-3">
            <div class="flex items-center gap-3">
              <div class="flex-1 flex bg-gray-50 dark:bg-[#252530] rounded-xl p-1 border border-gray-200 dark:border-zinc-700/60">
                <button @click="eventType = 'entry'"
                        :class="eventType === 'entry'
                          ? 'bg-white dark:bg-[#2a2a35] text-emerald-700 dark:text-emerald-400 shadow-sm'
                          : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'"
                        class="flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
                  Entrada
                </button>
                <button @click="eventType = 'exit'"
                        :class="eventType === 'exit'
                          ? 'bg-white dark:bg-[#2a2a35] text-blue-700 dark:text-blue-400 shadow-sm'
                          : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'"
                        class="flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
                  Salida
                </button>
              </div>
            </div>

            <button @click="confirmAttendance"
                    :disabled="!verificationResult?.match || recording"
                    class="w-full py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all flex items-center justify-center gap-2">
              <svg v-if="recording" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              <template v-else>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </template>
              {{ recording ? 'Registrando...' : 'Confirmar ' + (eventType === 'entry' ? 'Entrada' : 'Salida') }}
            </button>
          </div>

          <!-- No enrollment warning -->
          <div v-if="isCameraActive && !baseDescriptor && !isModelLoading" class="mt-4 bg-amber-50 dark:bg-amber-950/50 rounded-lg p-3 border border-amber-100 dark:border-amber-900/50 text-center">
            <svg class="w-6 h-6 text-amber-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <p class="text-xs text-amber-700 dark:text-amber-300 font-medium">No tienes un perfil biométrico registrado</p>
            <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">Solicita a un administrador que realice tu enrolamiento facial</p>
          </div>
        </div>
      </div>

      <!-- Panel de Historial del Día -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Registro del Día
            </h3>
            <input type="date" v-model="historyDate" @change="loadHistory"
                   class="px-2 py-1 text-xs border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>
        </div>

        <div class="p-5">
          <!-- Loading -->
          <div v-if="historyLoading" class="flex items-center justify-center py-12">
            <svg class="animate-spin w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          </div>

          <!-- Empty -->
          <div v-else-if="history.length === 0" class="text-center py-12">
            <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="text-sm text-gray-400 dark:text-zinc-500">Sin registros para esta fecha</p>
          </div>

          <!-- History list -->
          <div v-else class="space-y-2 max-h-[420px] overflow-y-auto pr-1">
            <div v-for="log in history" :key="log.id"
                 class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-zinc-800/50 border border-gray-100 dark:border-zinc-800 hover:border-gray-200 dark:hover:border-zinc-700 transition-all">
              <!-- Event icon -->
              <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                   :class="log.event_type === 'entry'
                     ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400'
                     : 'bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400'">
                <svg v-if="log.event_type === 'entry'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
              </div>

              <!-- Log info -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ log.user_name }}</p>
                  <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide"
                        :class="log.event_type === 'entry'
                          ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400'
                          : 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400'">
                    {{ log.event_type === 'entry' ? 'Entrada' : 'Salida' }}
                  </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-zinc-400">
                  {{ formatTime(log.event_at) }}
                  <span class="text-gray-300 dark:text-zinc-600 mx-1">|</span>
                  Score: {{ (log.verification_score * 100).toFixed(1) }}%
                </p>
              </div>

              <!-- Score indicator -->
              <div class="w-2 h-2 rounded-full flex-shrink-0"
                   :class="log.verification_score < 0.2 ? 'bg-emerald-500' : log.verification_score < 0.35 ? 'bg-amber-500' : 'bg-rose-500'"></div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Toast -->
    <Transition name="fade">
      <div v-if="toast.show" class="fixed bottom-6 right-6 z-[60] max-w-sm">
        <div class="rounded-xl p-4 shadow-xl border" :class="toast.type === 'success'
              ? 'bg-emerald-50 dark:bg-emerald-950 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300'
              : 'bg-rose-50 dark:bg-rose-950 border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300'">
          <p class="text-sm font-medium">{{ toast.message }}</p>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useFaceRecognition } from '../composables/useFaceRecognition.js'
import biometricService from '../services/biometricService.js'
import authService from '../services/authService.js'

const {
  isModelLoading,
  modelError,
  isCameraActive,
  faceDetected,
  matchResult: verificationResult,
  MATCH_THRESHOLD,
  loadModels,
  startCamera,
  stopCamera,
  captureImage,
  startContinuousDetection,
} = useFaceRecognition()

// State
const verifyVideoRef = ref(null)
const verifyOverlayRef = ref(null)
const baseDescriptor = ref(null)
const eventType = ref('entry')
const recording = ref(false)

// Summary
const summary = ref({})

// History
const history = ref([])
const historyDate = ref(new Date().toISOString().split('T')[0])
const historyLoading = ref(false)

// Toast
const toast = ref({ show: false, message: '', type: 'success' })

// Get current user
const currentUser = authService.getUser?.() || {}

/**
 * Initialize camera and start continuous face detection
 */
const initializeCamera = async () => {
  const loaded = await loadModels()
  if (!loaded) return

  if (!verifyVideoRef.value) return
  const started = await startCamera(verifyVideoRef.value)
  if (!started) return

  // Load base descriptor for current user
  try {
    const response = await biometricService.getDescriptor(currentUser.id)
    if (response.enrolled && response.data?.descriptors) {
      baseDescriptor.value = response.data.descriptors

      // Wait for video metadata then start continuous detection
      verifyVideoRef.value.addEventListener('loadedmetadata', () => {
        if (verifyOverlayRef.value) {
          verifyOverlayRef.value.width = verifyVideoRef.value.videoWidth
          verifyOverlayRef.value.height = verifyVideoRef.value.videoHeight
        }
        startContinuousDetection(verifyVideoRef.value, baseDescriptor.value, null, 600)
      }, { once: true })
    }
  } catch {
    baseDescriptor.value = null
  }
}

/**
 * Stop verification and camera
 */
const stopVerification = () => {
  stopCamera()
  baseDescriptor.value = null
}

/**
 * Confirm attendance record
 */
const confirmAttendance = async () => {
  if (!verificationResult.value?.match || recording.value) return

  recording.value = true
  try {
    const capturedImg = captureImage(verifyVideoRef.value)

    const response = await biometricService.recordAttendance(
      currentUser.id,
      eventType.value,
      verificationResult.value.distance,
      capturedImg
    )

    showToast(response.message || 'Punteo registrado', 'success')
    await loadSummary()
    await loadHistory()
  } catch (error) {
    const msg = error.response?.data?.message || 'Error al registrar punteo'
    showToast(msg, 'error')
  } finally {
    recording.value = false
  }
}

/**
 * Load today's summary
 */
const loadSummary = async () => {
  try {
    const response = await biometricService.getTodaySummary()
    summary.value = response.data || {}
  } catch {
    summary.value = {}
  }
}

/**
 * Load attendance history
 */
const loadHistory = async () => {
  historyLoading.value = true
  try {
    const response = await biometricService.getAttendanceHistory({ date: historyDate.value })
    history.value = response.data || []
  } catch {
    history.value = []
  } finally {
    historyLoading.value = false
  }
}

const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true }).toUpperCase()
}

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => { toast.value.show = false }, 4000)
}

onMounted(async () => {
  await Promise.all([loadSummary(), loadHistory()])
})

onUnmounted(() => {
  stopCamera()
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
