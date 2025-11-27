<template>
  <!-- Widget Flotante de Radio -->
  <Teleport to="body">
    <!-- Overlay de fondo (opcional, para cerrar al hacer click fuera) -->
    <Transition name="fade">
      <div 
        v-if="isOpen" 
        @click="closeWidget"
        class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[60] transition-all duration-300"
      ></div>
    </Transition>

    <!-- Panel del Reproductor -->
    <Transition name="slide-up">
      <div 
        v-show="isOpen"
        class="fixed bottom-4 right-4 w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 z-[70] overflow-hidden"
      >
        <!-- Header con Glassmorphism -->
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-4 relative overflow-hidden">
          <!-- Efecto de onda animada -->
          <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
          
          <div class="relative flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <!-- Icono de Radio -->
              <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.651a3.75 3.75 0 010-5.303m5.304 0a3.75 3.75 0 010 5.303m-7.425 2.122a6.75 6.75 0 010-9.546m9.546 0a6.75 6.75 0 010 9.546M5.106 18.894c-3.808-3.808-3.808-9.98 0-13.789m13.788 0c3.808 3.808 3.808 9.981 0 13.79M12 12h.008v.007H12V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
              </div>
              <div>
                <h3 class="text-white font-black text-sm tracking-wide">RADIO EN VIVO</h3>
                <p class="text-emerald-50 text-xs font-medium">Emisoras de Colombia</p>
              </div>
            </div>
            
            <!-- Botón Cerrar -->
            <button 
              @click="closeWidget"
              class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-all backdrop-blur-md"
            >
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Indicador "En Vivo" animado -->
          <div v-if="isPlaying" class="absolute top-2 right-14 flex items-center space-x-1.5 bg-red-500 px-2 py-1 rounded-full">
            <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
            <span class="text-white text-xs font-bold uppercase tracking-wider">En Vivo</span>
          </div>
        </div>

        <!-- Lista de Emisoras (Scrollable) -->
        <div class="max-h-96 overflow-y-auto p-4 space-y-2 custom-scrollbar">
          <div 
            v-for="radio in radios" 
            :key="radio.id"
            @click="selectRadio(radio)"
            :class="[
              'flex items-center p-3 rounded-xl cursor-pointer transition-all duration-200 border-2',
              currentRadio?.id === radio.id 
                ? 'bg-gradient-to-r from-emerald-50 to-teal-50 border-emerald-300 shadow-md' 
                : 'bg-slate-50 border-slate-100 hover:border-emerald-200 hover:bg-emerald-50/50'
            ]"
          >
            <!-- Logo de la Emisora -->
            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm border border-slate-200 overflow-hidden">
              <img 
                :src="radio.logo" 
                :alt="radio.name"
                class="w-10 h-10 object-contain"
                @error="handleImageError"
              >
            </div>

            <!-- Nombre de la Emisora -->
            <div class="flex-1 ml-3">
              <p :class="[
                'font-bold text-sm',
                currentRadio?.id === radio.id ? 'text-emerald-700' : 'text-slate-900'
              ]">
                {{ radio.name }}
              </p>
              <p class="text-xs text-slate-500 font-medium">
                {{ currentRadio?.id === radio.id && isPlaying ? 'Reproduciendo...' : 'Toca para escuchar' }}
              </p>
            </div>

            <!-- Indicador de Reproducción -->
            <div v-if="currentRadio?.id === radio.id && isPlaying" class="flex items-center space-x-1">
              <div class="w-1 h-3 bg-emerald-500 rounded-full animate-sound-wave" style="animation-delay: 0s"></div>
              <div class="w-1 h-4 bg-emerald-500 rounded-full animate-sound-wave" style="animation-delay: 0.1s"></div>
              <div class="w-1 h-5 bg-emerald-500 rounded-full animate-sound-wave" style="animation-delay: 0.2s"></div>
              <div class="w-1 h-4 bg-emerald-500 rounded-full animate-sound-wave" style="animation-delay: 0.3s"></div>
            </div>
          </div>
        </div>

        <!-- Footer con Controles -->
        <div class="bg-slate-50 border-t border-slate-200 p-4">
          <!-- Emisora Actual -->
          <div v-if="currentRadio" class="mb-3 flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm border border-slate-200 overflow-hidden">
                <img 
                  :src="currentRadio.logo" 
                  :alt="currentRadio.name"
                  class="w-7 h-7 object-contain"
                  @error="handleImageError"
                >
              </div>
              <div>
                <p class="text-sm font-bold text-slate-900">{{ currentRadio.name }}</p>
                <p class="text-xs text-slate-500">{{ isPlaying ? '🔊 Sonando' : '⏸️ Pausado' }}</p>
              </div>
            </div>

            <!-- Control de Volumen -->
            <div class="flex items-center space-x-2">
              <button 
                @click="toggleMute"
                class="w-8 h-8 bg-white hover:bg-slate-100 rounded-lg flex items-center justify-center transition-all shadow-sm"
              >
                <svg v-if="!isMuted" class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                </svg>
                <svg v-else class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                </svg>
              </button>
              <input 
                v-model="volume"
                @input="updateVolume"
                type="range" 
                min="0" 
                max="100" 
                class="w-24 h-2 bg-slate-200 rounded-full appearance-none cursor-pointer accent-emerald-500"
              >
            </div>
          </div>

          <!-- Botones de Control -->
          <div class="flex items-center justify-center space-x-3">
            <button 
              @click="previousRadio"
              :disabled="!currentRadio"
              class="w-10 h-10 bg-white hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl flex items-center justify-center transition-all shadow-sm border border-slate-200"
            >
              <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 00-2-2h-1l-5-3v14l5-3h1a2 2 0 002-2zM3 12h8M3 12l3-3m-3 3l3 3" />
              </svg>
            </button>

            <!-- Play/Pause Principal -->
            <button 
              @click="togglePlay"
              :disabled="!currentRadio"
              class="w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-full flex items-center justify-center transition-all shadow-lg active:scale-95"
            >
              <svg v-if="!isPlaying" class="w-7 h-7 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
              </svg>
              <svg v-else class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z" />
              </svg>
            </button>

            <button 
              @click="nextRadio"
              :disabled="!currentRadio"
              class="w-10 h-10 bg-white hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl flex items-center justify-center transition-all shadow-sm border border-slate-200"
            >
              <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8v8a2 2 0 002 2h1l5 3V5L6 8H5a2 2 0 00-2 2zm13 0l3-3m0 0l3 3m-3-3v8" />
              </svg>
            </button>
          </div>

          <!-- Mensaje de Estado -->
          <Transition name="fade">
            <div v-if="statusMessage" class="mt-3 text-center">
              <p :class="[
                'text-xs font-semibold px-3 py-2 rounded-lg inline-block',
                statusMessage.type === 'error' 
                  ? 'bg-red-50 text-red-700 border border-red-200' 
                  : 'bg-emerald-50 text-emerald-700 border border-emerald-200'
              ]">
                {{ statusMessage.text }}
              </p>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>

    <!-- Elemento de Audio (Siempre vivo, aunque el widget esté cerrado) -->
    <audio 
      ref="audioPlayer"
      @error="handleAudioError"
      @loadstart="handleLoadStart"
      @canplay="handleCanPlay"
      @playing="handlePlaying"
      preload="none"
    ></audio>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, onUnmounted } from 'vue'
import { useRadioState } from '../composables/useRadioState'

// Props
defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['close'])

// Composable de estado global de radio
const { isPlaying, setPlaying, setCurrentRadio, stopRadio } = useRadioState()

// State local
const audioPlayer = ref(null)
const currentRadio = ref(null)
const volume = ref(70)
const isMuted = ref(false)
const statusMessage = ref(null)

// Data de Emisoras
const radios = [
  { id: 'oli_med', name: 'Olímpica Medellín', logo: 'https://upload.wikimedia.org/wikipedia/commons/9/91/Olimpica_Stereo_logo.png', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/OLP_MEDELLIN.mp3' },
  { id: 'mix_med', name: 'Mix 89.9 FM', logo: 'https://mixradio.co/assets/img/logo-mix.png', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/MIX_MEDELLIN.mp3' },
  { id: 'mega', name: 'La Mega', logo: 'https://www.lamega.com.co/themes/custom/lamega/logo.svg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/LA_MEGA.mp3' },
  { id: 'w', name: 'W Radio', logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/W_Radio_logo.svg/1200px-W_Radio_logo.svg.png', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/WRADIO.mp3' },
  { id: 'caracol', name: 'Caracol Radio', logo: 'https://caracol.com.co/themes/custom/caracol/logo.svg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/CARACOL_RADIO.mp3' },
  { id: 'blu', name: 'Blu Radio', logo: 'https://www.bluradio.com/themes/custom/bluradio/logo.svg', url: 'https://ice41.securenetsystems.net/BLURADIO?&playSessionID=5F967923-863D-4D39-9523-267957973059' },
  { id: 'tropi', name: 'Tropicana FM', logo: 'https://www.tropicanafm.com/wp-content/uploads/2020/07/Logo-Tropicana-2020.png', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/TROPICANA.mp3' },
  { id: 'besame', name: 'Bésame FM', logo: 'https://www.besame.fm/themes/custom/besame/logo.svg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/BESAME_MEDELLIN.mp3' },
  { id: 'aktiva', name: 'Radioacktiva', logo: 'https://www.radioacktiva.com/themes/custom/radioacktiva/logo.svg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/RADIOACKTIVA.mp3' },
  { id: 'rcn', name: 'RCN Radio', logo: 'https://www.rcnradio.com/themes/custom/rcnradio/logo.svg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/RCN_RADIO.mp3' }
]

// Methods
const closeWidget = () => {
  emit('close')
}

const selectRadio = (radio) => {
  if (currentRadio.value?.id === radio.id && isPlaying.value) {
    // Si es la misma emisora y está sonando, pausar
    togglePlay()
  } else {
    // Cambiar a nueva emisora
    currentRadio.value = radio
    setCurrentRadio(radio) // Actualizar estado global
    playRadio()
  }
}

const playRadio = async () => {
  if (!currentRadio.value || !audioPlayer.value) return

  try {
    audioPlayer.value.src = currentRadio.value.url
    audioPlayer.value.volume = volume.value / 100
    
    showStatus('Conectando...', 'info')
    
    await audioPlayer.value.play()
    setPlaying(true)
    
  } catch (error) {
    console.error('Error reproduciendo radio:', error)
    showStatus('Error al conectar con la emisora', 'error')
    setPlaying(false)
  }
}

const togglePlay = () => {
  if (!audioPlayer.value || !currentRadio.value) return

  if (isPlaying.value) {
    audioPlayer.value.pause()
    setPlaying(false)
  } else {
    playRadio()
  }
}

const updateVolume = () => {
  if (audioPlayer.value) {
    audioPlayer.value.volume = volume.value / 100
    if (isMuted.value && volume.value > 0) {
      isMuted.value = false
    }
  }
}

const toggleMute = () => {
  if (audioPlayer.value) {
    isMuted.value = !isMuted.value
    audioPlayer.value.muted = isMuted.value
  }
}

const nextRadio = () => {
  if (!currentRadio.value) return
  
  const currentIndex = radios.findIndex(r => r.id === currentRadio.value.id)
  const nextIndex = (currentIndex + 1) % radios.length
  selectRadio(radios[nextIndex])
}

const previousRadio = () => {
  if (!currentRadio.value) return
  
  const currentIndex = radios.findIndex(r => r.id === currentRadio.value.id)
  const prevIndex = (currentIndex - 1 + radios.length) % radios.length
  selectRadio(radios[prevIndex])
}

const showStatus = (text, type = 'info') => {
  statusMessage.value = { text, type }
  setTimeout(() => {
    statusMessage.value = null
  }, 3000)
}

const handleAudioError = (e) => {
  console.error('Error de audio:', e)
  setPlaying(false)
  showStatus('No se pudo cargar la emisora. Intenta con otra.', 'error')
}

const handleLoadStart = () => {
  showStatus('Cargando emisora...', 'info')
}

const handleCanPlay = () => {
  showStatus('Listo para reproducir', 'info')
}

const handlePlaying = () => {
  statusMessage.value = null
}

const handleImageError = (e) => {
  e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%2394a3b8"%3E%3Cpath d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/%3E%3C/svg%3E'
}

// Cleanup on unmount
onUnmounted(() => {
  if (audioPlayer.value) {
    audioPlayer.value.pause()
    audioPlayer.value.src = ''
  }
  stopRadio() // Limpiar estado global
})
</script>

<style scoped>
/* Animación de Slide-Up */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.slide-up-enter-from {
  transform: translateY(100%);
  opacity: 0;
}

.slide-up-leave-to {
  transform: translateY(100%);
  opacity: 0;
}

/* Animación de Fade */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Scrollbar Personalizado */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Animación de Barras de Sonido */
@keyframes sound-wave {
  0%, 100% {
    height: 0.75rem;
  }
  50% {
    height: 1.25rem;
  }
}

.animate-sound-wave {
  animation: sound-wave 0.6s ease-in-out infinite;
}

/* Estilo del Range Input */
input[type="range"] {
  appearance: none;
  -webkit-appearance: none;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #10b981;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

input[type="range"]::-moz-range-thumb {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #10b981;
  cursor: pointer;
  border: none;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
</style>
