<template>
  <Transition name="fade">
    <div v-if="visible" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="handleClose"></div>
      <div class="relative bg-white dark:bg-[#1e1e24] rounded-2xl max-w-2xl w-full shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Enrolamiento Biométrico</h3>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Registrar perfil facial de <span class="font-semibold">{{ userName }}</span></p>
            </div>
            <button @click="handleClose"
                    class="p-2 rounded-lg text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-5">

          <!-- Step indicator -->
          <div class="flex items-center gap-3">
            <div v-for="(stepLabel, i) in ['Cargar modelos', 'Posicionar rostro', 'Capturar']" :key="i"
                 class="flex items-center gap-2 text-xs font-medium"
                 :class="currentStep > i ? 'text-emerald-600 dark:text-emerald-400' : currentStep === i ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-zinc-500'">
              <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold border"
                   :class="currentStep > i
                     ? 'bg-emerald-100 dark:bg-emerald-950 border-emerald-300 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400'
                     : currentStep === i
                       ? 'bg-blue-100 dark:bg-blue-950 border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-400'
                       : 'bg-gray-50 dark:bg-zinc-800 border-gray-300 dark:border-zinc-700 text-gray-400 dark:text-zinc-500'">
                <template v-if="currentStep > i">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template v-else>{{ i + 1 }}</template>
              </div>
              {{ stepLabel }}
              <svg v-if="i < 2" class="w-3 h-3 text-gray-300 dark:text-zinc-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
            </div>
          </div>

          <!-- Step 0: Loading models -->
          <div v-if="currentStep === 0" class="text-center py-10">
            <template v-if="isModelLoading">
              <svg class="animate-spin w-10 h-10 text-blue-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              <p class="text-sm font-medium text-gray-700 dark:text-zinc-300">Cargando modelos de reconocimiento facial...</p>
              <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Esto puede tomar unos segundos la primera vez</p>
            </template>
            <template v-else-if="modelError">
              <svg class="w-10 h-10 text-rose-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
              <p class="text-sm text-rose-600 dark:text-rose-400 font-medium">{{ modelError }}</p>
              <button @click="initializeModels" class="mt-3 px-4 py-2 bg-slate-900 dark:bg-slate-700 text-white text-sm font-bold rounded-lg">Reintentar</button>
            </template>
          </div>

          <!-- Step 1 & 2: Camera view -->
          <div v-show="currentStep >= 1" class="relative">
            <!-- Camera container -->
            <div class="relative rounded-xl overflow-hidden bg-black aspect-[4/3]">
              <video ref="videoRef" class="w-full h-full object-cover" playsinline muted></video>
              <canvas ref="overlayRef" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

              <!-- Camera status badges -->
              <div class="absolute top-3 left-3 flex items-center gap-2">
                <span v-if="isCameraActive" class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold bg-black/50 text-white backdrop-blur-sm">
                  <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                  CÁMARA ACTIVA
                </span>
                <span v-if="faceDetected" class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold bg-emerald-600/80 text-white backdrop-blur-sm">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 13l4 4L19 7"/></svg>
                  ROSTRO DETECTADO
                </span>
              </div>

              <!-- Captured preview overlay -->
              <div v-if="capturedImage" class="absolute inset-0 bg-black/60 flex items-center justify-center">
                <div class="text-center">
                  <img :src="capturedImage" alt="Captura" class="w-48 h-48 rounded-xl object-cover border-4 border-emerald-500 mx-auto shadow-2xl">
                  <p class="text-white text-sm font-semibold mt-3">Captura realizada</p>
                </div>
              </div>
            </div>

            <!-- Instructions -->
            <div v-if="currentStep === 1 && !capturedImage" class="mt-3 bg-blue-50 dark:bg-blue-950/50 rounded-lg p-3 border border-blue-100 dark:border-blue-900/50">
              <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                  <p class="font-semibold">Instrucciones para una buena captura:</p>
                  <ul class="list-disc list-inside space-y-0.5 text-blue-600 dark:text-blue-400">
                    <li>Mire directamente a la cámara con el rostro centrado</li>
                    <li>Asegúrese de tener buena iluminación frontal</li>
                    <li>Retire lentes oscuros, gorras o cualquier obstrucción</li>
                    <li>Mantenga una expresión neutra</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <!-- Captured confirmation (Step 2) -->
          <div v-if="capturedImage" class="bg-emerald-50 dark:bg-emerald-950/50 rounded-lg p-3 border border-emerald-100 dark:border-emerald-900/50">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <p class="text-xs text-emerald-700 dark:text-emerald-300 font-medium">Rostro capturado y descriptor facial calculado exitosamente. Puede guardar el perfil.</p>
            </div>
          </div>
        </div>

        <!-- Footer actions -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800 flex items-center gap-3">
          <button @click="handleClose"
                  class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 transition-all">
            Cancelar
          </button>

          <button v-if="currentStep === 1 && !capturedImage" @click="captureProfile"
                  :disabled="!faceDetected"
                  class="flex-1 px-4 py-2.5 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Capturar Rostro
          </button>

          <button v-if="capturedImage && !saving" @click="retake"
                  class="px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 transition-all">
            Repetir
          </button>

          <button v-if="capturedImage" @click="saveProfile"
                  :disabled="saving"
                  class="flex-1 px-4 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:opacity-50 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all flex items-center justify-center gap-2">
            <svg v-if="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ saving ? 'Guardando...' : 'Guardar Perfil' }}
          </button>
        </div>

      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch, nextTick, onUnmounted } from 'vue'
import { useFaceRecognition } from '../composables/useFaceRecognition.js'
import biometricService from '../services/biometricService.js'

const props = defineProps({
  visible: { type: Boolean, default: false },
  userId: { type: [Number, String], required: true },
  userName: { type: String, default: 'Usuario' },
})

const emit = defineEmits(['close', 'enrolled'])

const {
  isModelLoading,
  modelError,
  isCameraActive,
  faceDetected,
  loadModels,
  startCamera,
  stopCamera,
  extractDescriptor,
  captureImage,
  startFaceGuide,
} = useFaceRecognition()

const videoRef = ref(null)
const overlayRef = ref(null)
const currentStep = ref(0)
const capturedImage = ref(null)
const capturedDescriptor = ref(null)
const saving = ref(false)

const initializeModels = async () => {
  currentStep.value = 0
  const loaded = await loadModels()
  if (loaded) {
    currentStep.value = 1
    await nextTick()
    await initCamera()
  }
}

const initCamera = async () => {
  if (!videoRef.value) return
  const started = await startCamera(videoRef.value)
  if (started && overlayRef.value) {
    videoRef.value.addEventListener('loadedmetadata', () => {
      overlayRef.value.width = videoRef.value.videoWidth
      overlayRef.value.height = videoRef.value.videoHeight
      startFaceGuide(videoRef.value, overlayRef.value)
    }, { once: true })
  }
}

const captureProfile = async () => {
  if (!videoRef.value || !faceDetected.value) return

  const descriptor = await extractDescriptor(videoRef.value)
  if (!descriptor) {
    modelError.value = 'No se pudo obtener el descriptor facial. Intente de nuevo.'
    return
  }

  capturedDescriptor.value = descriptor
  capturedImage.value = captureImage(videoRef.value)
  currentStep.value = 2
}

const retake = async () => {
  capturedImage.value = null
  capturedDescriptor.value = null
  currentStep.value = 1
  await nextTick()
  if (overlayRef.value && videoRef.value) {
    startFaceGuide(videoRef.value, overlayRef.value)
  }
}

const saveProfile = async () => {
  if (!capturedDescriptor.value || !capturedImage.value) return

  saving.value = true
  try {
    const response = await biometricService.enrollProfile(
      props.userId,
      capturedImage.value,
      capturedDescriptor.value
    )

    emit('enrolled', response)
  } catch (error) {
    const msg = error.response?.data?.message || 'Error al guardar el perfil biométrico'
    modelError.value = msg
  } finally {
    saving.value = false
  }
}

const handleClose = () => {
  stopCamera()
  capturedImage.value = null
  capturedDescriptor.value = null
  currentStep.value = 0
  emit('close')
}

watch(() => props.visible, async (val) => {
  if (val) {
    await nextTick()
    await initializeModels()
  } else {
    stopCamera()
  }
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
