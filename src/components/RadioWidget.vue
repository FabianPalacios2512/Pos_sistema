<template>
  <!-- Widget Flotante de Radio Premium -->
  <Teleport to="body">
    <!-- Overlay de fondo -->
    <Transition name="fade">
      <div 
        v-if="isOpen" 
        @click="closeWidget"
        class="fixed inset-0 bg-black/50 z-[60]"
      ></div>
    </Transition>

    <!-- Panel del Reproductor -->
    <Transition name="slide-up">
      <div 
        v-show="isOpen"
        class="fixed bottom-6 right-6 w-96 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-zinc-800 z-[70] overflow-hidden flex flex-col h-[600px] max-h-[85vh] font-sans"
      >
        <!-- 1. HEADER (Ambiente) -->
        <div class="bg-gradient-to-br from-emerald-600 to-teal-800 p-6 relative overflow-hidden flex-shrink-0 text-white">
          <!-- Efectos de fondo simplificados -->
          <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
          <div class="absolute bottom-0 left-0 w-24 h-24 bg-emerald-400/20 rounded-full translate-y-1/2 -translate-x-1/2"></div>
          
          <div class="relative z-10 flex justify-between items-start">
            <div>
              <h2 class="text-2xl font-bold tracking-tight">Ambiente Musical</h2>
              <p class="text-emerald-100 text-sm font-medium opacity-90">Radio 105 FM</p>
            </div>
            
            <!-- Indicador Visualizador -->
            <div v-if="isPlaying" class="flex items-end gap-1 h-6">
              <div class="w-1 bg-white/80 rounded-full animate-music-bar-1"></div>
              <div class="w-1 bg-white/80 rounded-full animate-music-bar-2"></div>
              <div class="w-1 bg-white/80 rounded-full animate-music-bar-3"></div>
              <div class="w-1 bg-white/80 rounded-full animate-music-bar-2"></div>
            </div>
          </div>

          <!-- Botón Cerrar Absoluto -->
          <button 
            @click="closeWidget"
            class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors p-1 rounded-full hover:bg-white/10"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>

        <!-- 2. TABS DE NAVEGACIÓN -->
        <div class="flex border-b border-gray-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
          <button 
            @click="activeTab = 'explore'"
            class="flex-1 py-3 text-sm font-bold transition-colors relative"
            :class="activeTab === 'explore' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700'"
          >
            Explorar
            <div v-if="activeTab === 'explore'" class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-500"></div>
          </button>
          <button 
            @click="activeTab = 'favorites'"
            class="flex-1 py-3 text-sm font-bold transition-colors relative"
            :class="activeTab === 'favorites' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700'"
          >
            Mis Favoritas
            <div v-if="activeTab === 'favorites'" class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-500"></div>
          </button>
        </div>

        <!-- 3. LISTA DE EMISORAS (El Core) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-2 bg-gray-50 dark:bg-zinc-900/50 custom-scrollbar">
          
          <!-- Estado Vacío Favoritos -->
          <div v-if="activeTab === 'favorites' && favoriteRadios.length === 0" class="flex flex-col items-center justify-center h-40 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <p class="text-sm">Aún no tienes favoritas</p>
          </div>

          <!-- Lista -->
          <div 
            v-for="radio in displayedRadios" 
            :key="radio.id"
            class="group flex items-center p-2 rounded-xl transition-all duration-200 hover:bg-white dark:hover:bg-zinc-800 hover:shadow-sm border border-transparent hover:border-gray-100 dark:hover:border-zinc-700 relative"
            :class="{'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-800/30': currentRadio?.id === radio.id}"
          >
            <!-- Icono / Cover -->
            <div class="relative w-12 h-12 rounded-lg bg-gray-200 dark:bg-zinc-700 flex-shrink-0 overflow-hidden shadow-sm group-hover:shadow-md transition-shadow">
              <img :src="radio.logo" class="w-full h-full object-cover" @error="handleImageError" />
              
              <!-- Overlay Play (Hover) -->
              <div 
                @click="selectRadio(radio)"
                class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                :class="{'opacity-100 bg-black/20': currentRadio?.id === radio.id && isPlaying}"
              >
                <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                  <svg v-if="currentRadio?.id === radio.id && isPlaying" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                  <svg v-else class="w-4 h-4 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
              </div>
            </div>

            <!-- Info -->
            <div class="ml-3 flex-1 min-w-0 cursor-pointer" @click="selectRadio(radio)">
              <h4 
                class="font-bold text-sm truncate"
                :class="currentRadio?.id === radio.id ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-800 dark:text-zinc-200'"
              >
                {{ radio.name }}
              </h4>
              <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">
                {{ currentRadio?.id === radio.id && isPlaying ? 'Reproduciendo...' : 'Frecuencia FM' }}
              </p>
            </div>

            <!-- Acciones -->
            <div class="flex items-center gap-2 px-2">
              <!-- Visualizador Mini (Solo si suena) -->
              <div v-if="currentRadio?.id === radio.id && isPlaying" class="flex items-end gap-0.5 h-3 mr-2">
                <div class="w-0.5 bg-emerald-500 animate-music-bar-1"></div>
                <div class="w-0.5 bg-emerald-500 animate-music-bar-2"></div>
                <div class="w-0.5 bg-emerald-500 animate-music-bar-3"></div>
              </div>

              <!-- Botón Favorito -->
              <button 
                @click.stop="toggleFavorite(radio)"
                class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors focus:outline-none"
              >
                <svg 
                  xmlns="http://www.w3.org/2000/svg" 
                  class="h-5 w-5 transition-colors"
                  :class="isFavorite(radio.id) ? 'text-red-500 fill-current' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                  viewBox="0 0 24 24" 
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </button>
            </div>
          </div>

          <!-- 4. SECCIÓN "PIDE TU EMISORA" -->
          <div class="mt-6 pt-4 border-t border-gray-200 dark:border-zinc-700">
            <div v-if="!showRequestForm" class="text-center">
              <button 
                @click="showRequestForm = true"
                class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 flex items-center justify-center gap-1 w-full py-2 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                ¿Falta tu música? Pídela aquí
              </button>
            </div>

            <div v-else class="bg-white dark:bg-zinc-800 p-3 rounded-xl border border-gray-100 dark:border-zinc-700 shadow-sm animate-fade-in">
              <p class="text-xs font-bold text-gray-700 dark:text-zinc-300 mb-2">Solicitar Emisora</p>
              <div class="flex gap-2">
                <input 
                  v-model="requestStationName"
                  type="text" 
                  placeholder="Nombre o URL..." 
                  class="flex-1 text-xs px-3 py-2 rounded-lg border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-900 text-gray-900 dark:text-zinc-100 placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-emerald-500 outline-none"
                >
                <button 
                  @click="submitRequest"
                  class="bg-emerald-600 text-white text-xs font-bold px-3 py-2 rounded-lg hover:bg-emerald-700 transition-colors"
                >
                  Enviar
                </button>
              </div>
              <button @click="showRequestForm = false" class="text-[10px] text-gray-400 mt-2 hover:text-gray-600 block w-full text-center">Cancelar</button>
            </div>
          </div>
        </div>

        <!-- 5. FOOTER (Control Maestro) -->
        <div class="bg-white dark:bg-zinc-800 border-t border-gray-100 dark:border-zinc-700 p-4 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] z-20">
          <div class="flex items-center gap-4">
            <!-- Botón Mute -->
            <button 
              @click="toggleMute"
              class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
            >
              <svg v-if="!isMuted && volume > 0" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
              </svg>
            </button>

            <!-- Slider Volumen -->
            <div class="flex-1 relative group">
              <input 
                v-model="volume"
                @input="updateVolume"
                type="range" 
                min="0" 
                max="100" 
                class="w-full h-1.5 bg-gray-200 dark:bg-zinc-700 rounded-full appearance-none cursor-pointer accent-emerald-500"
              >
            </div>

            <span class="text-xs font-mono font-medium text-gray-400 w-8 text-right">{{ volume }}%</span>
          </div>
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
import { ref, computed, watch, onUnmounted, onMounted } from 'vue'
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

// Composable
const { isPlaying, setPlaying, setCurrentRadio, stopRadio } = useRadioState()

// State
const audioPlayer = ref(null)
const currentRadio = ref(null)
const volume = ref(70)
const isMuted = ref(false)
const activeTab = ref('explore') // 'explore' | 'favorites'
const showRequestForm = ref(false)
const requestStationName = ref('')
const favorites = ref(new Set()) // Set of IDs

// Data de Emisoras
const radios = [
  { id: 'oli_med', name: 'Olímpica Medellín', logo: '/img-radio/olimpica-stereo-medellin-1049-fm.e6a71dc8.png', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/OLP_MEDELLIN.mp3' },
  { id: 'mix_med', name: 'Mix Medellín', logo: '/img-radio/mix-899-fm-medellin.9117dedc.jpg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/MIX_MEDELLIN.mp3' },
  { id: 'mix_cali', name: 'Mix Cali', logo: '/img-radio/mix-899-fm-medellin.9117dedc.jpg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/MIX_CALI.mp3' },
  { id: 'mix_bog', name: 'Mix Bogotá', logo: '/img-radio/mix-899-fm-medellin.9117dedc.jpg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/MIX_BOGOTA.mp3' },
  { id: 'elsol_med', name: 'El Sol Medellín', logo: '/img-radio/el-sol-placeholder.svg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/ELSOL_MEDELLIN.mp3' },
  { id: 'elsol_cali', name: 'El Sol Cali', logo: '/img-radio/el-sol-placeholder.svg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/ELSOL_CALI.mp3' },
  { id: 'elsol_bog', name: 'El Sol Bogotá', logo: '/img-radio/el-sol-placeholder.svg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/ELSOL_BOGOTA.mp3' },
  { id: 'mega', name: 'La Mega', logo: '/img-radio/la-mega-medellin.60573223.jpg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/LA_MEGA.mp3' },
  { id: 'w', name: 'W Radio', logo: '/img-radio/la-w-radio.61065ed8.jpg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/WRADIO.mp3' },
  { id: 'caracol', name: 'Caracol Radio', logo: 'https://caracol.com.co/themes/custom/caracol/logo.svg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/CARACOL_RADIO.mp3' },
  { id: 'blu', name: 'Blu Radio', logo: 'https://www.bluradio.com/themes/custom/bluradio/logo.svg', url: 'https://ice41.securenetsystems.net/BLURADIO?&playSessionID=5F967923-863D-4D39-9523-267957973059' },
  { id: 'tropi', name: 'Tropicana FM', logo: 'https://www.tropicanafm.com/wp-content/uploads/2020/07/Logo-Tropicana-2020.png', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/TROPICANA.mp3' },
  { id: 'besame', name: 'Bésame FM', logo: 'https://www.besame.fm/themes/custom/besame/logo.svg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/BESAME_MEDELLIN.mp3' },
  { id: 'aktiva', name: 'Radioacktiva', logo: 'https://www.radioacktiva.com/themes/custom/radioacktiva/logo.svg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/RADIOACKTIVA.mp3' },
  { id: 'rcn', name: 'RCN Radio', logo: 'https://www.rcnradio.com/themes/custom/rcnradio/logo.svg', url: 'https://playerservices.streamtheworld.com/api/livestream-redirect/RCN_RADIO.mp3' }
]

// Computed
const displayedRadios = computed(() => {
  if (activeTab.value === 'favorites') {
    return radios.filter(r => favorites.value.has(r.id))
  }
  return radios
})

const favoriteRadios = computed(() => {
  return radios.filter(r => favorites.value.has(r.id))
})

// Methods
const closeWidget = () => emit('close')

const selectRadio = (radio) => {
  if (currentRadio.value?.id === radio.id && isPlaying.value) {
    togglePlay()
  } else {
    currentRadio.value = radio
    setCurrentRadio(radio)
    playRadio()
  }
}

const playRadio = async () => {
  if (!currentRadio.value || !audioPlayer.value) return
  try {
    audioPlayer.value.src = currentRadio.value.url
    audioPlayer.value.volume = volume.value / 100
    await audioPlayer.value.play()
    setPlaying(true)
  } catch (error) {
    console.error('Error reproduciendo radio:', error)
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
    if (isMuted.value && volume.value > 0) isMuted.value = false
  }
}

const toggleMute = () => {
  if (audioPlayer.value) {
    isMuted.value = !isMuted.value
    audioPlayer.value.muted = isMuted.value
  }
}

// Favorites Logic
const toggleFavorite = (radio) => {
  if (favorites.value.has(radio.id)) {
    favorites.value.delete(radio.id)
  } else {
    favorites.value.add(radio.id)
  }
  // Persist to localStorage
  localStorage.setItem('radio_favorites', JSON.stringify([...favorites.value]))
}

const isFavorite = (id) => favorites.value.has(id)

// Request Logic
const submitRequest = () => {
  if (!requestStationName.value) return
  alert('¡Gracias! Hemos recibido tu solicitud. ')
  requestStationName.value = ''
  showRequestForm.value = false
}

const handleImageError = (e) => {
  e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%2394a3b8"%3E%3Cpath d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/%3E%3C/svg%3E'
}

// Audio Handlers (Simplified)
const handleAudioError = () => setPlaying(false)
const handleLoadStart = () => {}
const handleCanPlay = () => {}
const handlePlaying = () => {}

// Lifecycle
onMounted(() => {
  // Load favorites
  const saved = localStorage.getItem('radio_favorites')
  if (saved) {
    favorites.value = new Set(JSON.parse(saved))
  }
})

onUnmounted(() => {
  if (audioPlayer.value) {
    audioPlayer.value.pause()
    audioPlayer.value.src = ''
  }
  stopRadio()
})
</script>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from, .slide-up-leave-to {
  transform: translateY(20px);
  opacity: 0;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

/* Dark mode scrollbar */
:root.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; }
:root.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #52525b; }

/* Visualizer Animation */
@keyframes music-bar {
  0%, 100% { height: 4px; }
  50% { height: 16px; }
}
.animate-music-bar-1 { animation: music-bar 0.5s ease-in-out infinite; animation-delay: 0s; }
.animate-music-bar-2 { animation: music-bar 0.5s ease-in-out infinite; animation-delay: 0.15s; }
.animate-music-bar-3 { animation: music-bar 0.5s ease-in-out infinite; animation-delay: 0.3s; }

/* Range Input Styling */
input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 12px; height: 12px;
  border-radius: 50%; background: #10b981;
  cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.3);
  transition: transform 0.1s;
}
input[type="range"]::-webkit-slider-thumb:hover { transform: scale(1.2); }
</style>
