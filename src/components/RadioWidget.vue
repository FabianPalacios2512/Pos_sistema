<template>
  <!-- Widget Flotante de Radio Enterprise -->
  <Teleport to="body">
    <!-- Overlay de fondo -->
    <Transition name="fade">
      <div 
        v-if="isOpen" 
        @click="closeWidget"
        class="fixed inset-0 bg-black/20 dark:bg-black/60 backdrop-blur-sm z-[60] transition-all duration-300"
      ></div>
    </Transition>

    <!-- Panel del Reproductor -->
    <Transition name="slide-up">
      <div 
        v-show="isOpen"
        class="fixed bottom-4 right-4 w-96 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-zinc-800 z-[70] overflow-hidden flex flex-col max-h-[80vh]"
      >
        <!-- Header Enterprise -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-900 dark:to-teal-900 p-4 relative overflow-hidden flex-shrink-0">
          <!-- Efecto de fondo -->
          <div class="absolute inset-0 bg-white/5 backdrop-blur-sm"></div>
          <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
          
          <div class="relative flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <!-- Icono de Radio -->
              <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md shadow-inner border border-white/10">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.651a3.75 3.75 0 010-5.303m5.304 0a3.75 3.75 0 010 5.303m-7.425 2.122a6.75 6.75 0 010-9.546m9.546 0a6.75 6.75 0 010 9.546M5.106 18.894c-3.808-3.808-3.808-9.98 0-13.789m13.788 0c3.808 3.808 3.808 9.981 0 13.79M12 12h.008v.007H12V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
              </div>
              <div>
                <h3 class="text-white font-bold text-sm tracking-wide">RADIO EN VIVO</h3>
                <p class="text-emerald-100 text-xs font-medium">Emisoras de Colombia</p>
              </div>
            </div>
            
            <!-- Controles Header -->
            <div class="flex items-center gap-3">
              <!-- Indicador "En Vivo" -->
              <div v-if="isPlaying" class="flex items-center justify-center gap-1.5 bg-red-500/90 backdrop-blur-sm px-2 py-0.5 rounded-full border border-red-400/50 shadow-sm">
                <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                <span class="text-white text-[10px] font-bold uppercase tracking-wider leading-none mt-[1px]">LIVE</span>
              </div>

              <!-- Botón Cerrar -->
              <button 
                @click="closeWidget"
                class="w-8 h-8 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all backdrop-blur-md text-white/80 hover:text-white"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Lista de Emisoras (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-3 space-y-2 custom-scrollbar bg-gray-50 dark:bg-zinc-900/50">
          <div 
            v-for="radio in radios" 
            :key="radio.id"
            @click="selectRadio(radio)"
            :class="[
              'flex items-center p-3 rounded-xl cursor-pointer transition-all duration-200 border group',
              currentRadio?.id === radio.id 
                ? 'bg-white dark:bg-zinc-800 border-emerald-500/50 dark:border-emerald-500/50 shadow-md ring-1 ring-emerald-500/20' 
                : 'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-sm'
            ]"
          >
            <!-- Logo de la Emisora -->
            <div class="w-10 h-10 bg-white dark:bg-zinc-700 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm border border-gray-100 dark:border-zinc-600 overflow-hidden p-1">
              <img 
                :src="radio.logo" 
                :alt="radio.name"
                class="w-full h-full object-contain"
                @error="handleImageError"
              >
            </div>

            <!-- Nombre de la Emisora -->
            <div class="flex-1 ml-3 min-w-0">
              <p :class="[
                'font-bold text-sm truncate transition-colors',
                currentRadio?.id === radio.id ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-700 dark:text-gray-200 group-hover:text-gray-900 dark:group-hover:text-white'
              ]">
                {{ radio.name }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400 font-medium truncate">
                {{ currentRadio?.id === radio.id && isPlaying ? 'Reproduciendo ahora...' : 'Toca para escuchar' }}
              </p>
            </div>

            <!-- Indicador de Reproducción (Ecualizador) -->
            <div v-if="currentRadio?.id === radio.id && isPlaying" class="flex items-end space-x-0.5 h-4 ml-2">
              <div class="w-1 bg-emerald-500 rounded-t-sm animate-music-bar-1"></div>
              <div class="w-1 bg-emerald-500 rounded-t-sm animate-music-bar-2"></div>
              <div class="w-1 bg-emerald-500 rounded-t-sm animate-music-bar-3"></div>
            </div>
          </div>
        </div>

        <!-- Footer con Controles -->
        <div class="bg-white dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700 p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-10">
          <!-- Emisora Actual Info -->
          <div v-if="currentRadio" class="mb-4 flex items-center justify-between">
            <div class="flex items-center space-x-3 min-w-0 flex-1 mr-4">
              <div class="w-10 h-10 bg-gray-50 dark:bg-zinc-700 rounded-lg flex items-center justify-center shadow-sm border border-gray-200 dark:border-zinc-600 overflow-hidden p-1">
                <img 
                  :src="currentRadio.logo" 
                  :alt="currentRadio.name"
                  class="w-full h-full object-contain"
                  @error="handleImageError"
                >
              </div>
              <div class="min-w-0">
                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ currentRadio.name }}</p>
                <div class="flex items-center space-x-1.5">
                  <span class="relative flex h-2 w-2">
                    <span v-if="isPlaying" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span :class="isPlaying ? 'bg-emerald-500' : 'bg-gray-400'" class="relative inline-flex rounded-full h-2 w-2"></span>
                  </span>
                  <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ isPlaying ? 'En vivo' : 'Pausado' }}</p>
                </div>
              </div>
            </div>

            <!-- Control de Volumen Compacto -->
            <div class="flex items-center space-x-2 group relative">
              <button 
                @click="toggleMute"
                class="w-8 h-8 bg-gray-100 dark:bg-zinc-700 hover:bg-gray-200 dark:hover:bg-zinc-600 rounded-lg flex items-center justify-center transition-all text-gray-600 dark:text-gray-300"
              >
                <svg v-if="!isMuted && volume > 0" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                </svg>
                <svg v-else class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                </svg>
              </button>
              
              <!-- Slider de volumen (visible en hover o siempre) -->
              <div class="w-20">
                <input 
                  v-model="volume"
                  @input="updateVolume"
                  type="range" 
                  min="0" 
                  max="100" 
                  class="w-full h-1.5 appearance-none cursor-pointer"
                >
              </div>
            </div>
          </div>

          <!-- Botones de Control Principales -->
          <div class="flex items-center justify-center space-x-6">
            <button 
              @click="previousRadio"
              :disabled="!currentRadio"
              class="p-2 text-gray-400 hover:text-emerald-600 dark:text-gray-500 dark:hover:text-emerald-400 disabled:opacity-30 transition-colors"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/>
              </svg>
            </button>

            <!-- Play/Pause Principal -->
            <button 
              @click="togglePlay"
              :disabled="!currentRadio"
              class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 disabled:opacity-50 disabled:cursor-not-allowed rounded-full flex items-center justify-center transition-all shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:scale-105 active:scale-95"
            >
              <svg v-if="!isPlaying" class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
              </svg>
              <svg v-else class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
              </svg>
            </button>

            <button 
              @click="nextRadio"
              :disabled="!currentRadio"
              class="p-2 text-gray-400 hover:text-emerald-600 dark:text-gray-500 dark:hover:text-emerald-400 disabled:opacity-30 transition-colors"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/>
              </svg>
            </button>
          </div>

          <!-- Mensaje de Estado -->
          <Transition name="fade">
            <div v-if="statusMessage" class="mt-3 text-center absolute bottom-20 left-0 right-0 pointer-events-none">
              <span :class="[
                'text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm inline-block backdrop-blur-md',
                statusMessage.type === 'error' 
                  ? 'bg-red-100/90 text-red-700 border border-red-200 dark:bg-red-900/90 dark:text-red-100 dark:border-red-800' 
                  : 'bg-emerald-100/90 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/90 dark:text-emerald-100 dark:border-emerald-800'
              ]">
                {{ statusMessage.text }}
              </span>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>

    <!-- Elemento de Audio -->
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
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(20px);
  opacity: 0;
}

/* Animación de Fade */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Scrollbar Personalizado */
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #52525b;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #71717a;
}

/* Animación de Barras de Música */
@keyframes music-bar {
  0%, 100% { height: 4px; }
  50% { height: 12px; }
}

.animate-music-bar-1 {
  animation: music-bar 0.5s ease-in-out infinite;
  animation-delay: 0s;
}

.animate-music-bar-2 {
  animation: music-bar 0.5s ease-in-out infinite;
  animation-delay: 0.15s;
}

.animate-music-bar-3 {
  animation: music-bar 0.5s ease-in-out infinite;
  animation-delay: 0.3s;
}

/* Estilo del Range Input */
input[type="range"] {
  appearance: none;
  -webkit-appearance: none;
  background: transparent;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #10b981;
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
  transition: transform 0.1s;
  margin-top: -3.5px; /* Ajuste fino de centrado */
}

input[type="range"]::-webkit-slider-thumb:hover {
  transform: scale(1.2);
}

input[type="range"]::-webkit-slider-runnable-track {
  width: 100%;
  height: 6px;
  cursor: pointer;
  border-radius: 999px;
  background: #e2e8f0; /* slate-200 */
}

.dark input[type="range"]::-webkit-slider-runnable-track {
  background: #52525b; /* zinc-600 */
}

/* Firefox */
input[type="range"]::-moz-range-thumb {
  width: 12px;
  height: 12px;
  border: none;
  border-radius: 50%;
  background: #10b981;
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
  transition: transform 0.1s;
}

input[type="range"]::-moz-range-thumb:hover {
  transform: scale(1.2);
}

input[type="range"]::-moz-range-track {
  width: 100%;
  height: 6px;
  cursor: pointer;
  border-radius: 999px;
  background: #e2e8f0;
}

.dark input[type="range"]::-moz-range-track {
  background: #52525b;
}
</style>
