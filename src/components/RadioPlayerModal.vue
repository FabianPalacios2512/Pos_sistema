<template>
  <Teleport to="body">
    <Transition name="fade">
      <div 
        v-if="isOpen" 
        class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
        @click="closeModal"
      >
        <!-- Contenedor Principal -->
        <div 
          class="w-[1100px] h-[700px] bg-[#121212] rounded-xl shadow-2xl overflow-hidden flex flex-col relative border border-white/5"
          @click.stop
        >
          
          <!-- Botón Cerrar -->
          <button 
            @click="closeModal"
            class="absolute top-4 right-4 z-50 text-zinc-400 hover:text-white bg-black/50 rounded-full p-1.5 transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- LAYOUT PRINCIPAL -->
          <div class="flex-1 flex overflow-hidden pb-24"> <!-- pb-24 para espacio del player -->
            
            <!-- A. SIDEBAR -->
            <aside class="w-64 bg-black flex flex-col flex-shrink-0 border-r border-white/5">
              <div class="p-6">
                <div class="flex items-center gap-3 text-white mb-8">
                  <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-black" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z" />
                    </svg>
                  </div>
                  <span class="font-bold text-xl tracking-tight">Radio 105</span>
                </div>

                <nav class="space-y-2">
                  <button 
                    @click="goHome"
                    class="w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-colors font-bold group"
                    :class="radioStore.currentView === 'home' ? 'bg-white/10 text-white' : 'text-zinc-400 hover:text-white hover:bg-white/5'"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Inicio
                  </button>
                  
                  <div class="pt-6 pb-2">
                    <h3 class="px-4 text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Ciudades</h3>
                    <div class="space-y-1">
                      <button @click="filterCity('Medellín', 'Antioquia')" class="w-full text-left px-4 py-2 text-sm text-zinc-400 hover:text-white hover:bg-white/5 rounded-md transition-colors flex items-center gap-3">
                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span> Medellín
                      </button>
                      <button @click="filterCity('Bogotá', 'Bogota')" class="w-full text-left px-4 py-2 text-sm text-zinc-400 hover:text-white hover:bg-white/5 rounded-md transition-colors flex items-center gap-3">
                        <span class="w-1 h-1 rounded-full bg-blue-500"></span> Bogotá
                      </button>
                      <button @click="filterCity('Cali', 'Valle del Cauca')" class="w-full text-left px-4 py-2 text-sm text-zinc-400 hover:text-white hover:bg-white/5 rounded-md transition-colors flex items-center gap-3">
                        <span class="w-1 h-1 rounded-full bg-red-500"></span> Cali
                      </button>
                      <button @click="filterCity('Costa', 'Atlantico')" class="w-full text-left px-4 py-2 text-sm text-zinc-400 hover:text-white hover:bg-white/5 rounded-md transition-colors flex items-center gap-3">
                        <span class="w-1 h-1 rounded-full bg-yellow-500"></span> Costa
                      </button>
                    </div>
                  </div>
                </nav>
              </div>
            </aside>

            <!-- B. ÁREA PRINCIPAL -->
            <main class="flex-1 bg-[#121212] relative overflow-y-auto custom-scrollbar">
              
              <!-- Header & Search -->
              <header class="sticky top-0 z-30 bg-[#121212]/95 backdrop-blur-md px-8 py-4 flex items-center justify-between border-b border-white/5">
                <div class="relative w-96">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </span>
                  <input 
                    type="text" 
                    v-model="searchQuery"
                    @input="handleSearch"
                    placeholder="Buscar emisoras, artistas..." 
                    class="w-full bg-zinc-800 text-white pl-10 pr-4 py-2.5 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 placeholder-zinc-500 transition-all"
                  />
                </div>
                
                <div class="flex items-center gap-3">
                  <span class="text-sm font-bold text-white">Buenas tardes</span>
                  <div class="w-8 h-8 bg-zinc-800 rounded-full flex items-center justify-center text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                </div>
              </header>

              <div class="p-8 space-y-12">
                
                <!-- LOADING STATE -->
                <div v-if="radioStore.isLoading" class="flex flex-col items-center justify-center py-20">
                  <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-emerald-500 mb-4"></div>
                  <p class="text-zinc-500 text-sm">Sintonizando...</p>
                </div>

                <!-- VIEW: SEARCH RESULTS -->
                <div v-else-if="radioStore.currentView === 'search'">
                  <h2 class="text-2xl font-bold text-white mb-6">Resultados de búsqueda</h2>
                  <div v-if="radioStore.searchResults.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    <StationCard 
                      v-for="station in radioStore.searchResults" 
                      :key="station.id" 
                      :station="station" 
                    />
                  </div>
                  <div v-else class="text-zinc-500 text-center py-10">
                    No encontramos emisoras con ese nombre.
                  </div>
                </div>

                <!-- VIEW: CITY FILTER -->
                <div v-else-if="radioStore.currentView === 'city'">
                  <h2 class="text-2xl font-bold text-white mb-6">Emisoras de {{ radioStore.currentCityTitle }}</h2>
                  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    <StationCard 
                      v-for="station in radioStore.activeCityStations" 
                      :key="station.id" 
                      :station="station" 
                    />
                  </div>
                </div>

                <!-- VIEW: HOME (SHOWCASE) -->
                <div v-else class="space-y-12">
                  
                  <!-- Section 1: Top Colombia -->
                  <section>
                    <div class="flex items-center justify-between mb-6">
                      <h2 class="text-2xl font-bold text-white hover:underline cursor-pointer">Lo Más Escuchado en Colombia</h2>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                      <StationCard 
                        v-for="station in radioStore.topStations" 
                        :key="station.id" 
                        :station="station" 
                      />
                    </div>
                  </section>

                  <!-- Section 2: Noticias -->
                  <section>
                    <div class="flex items-center justify-between mb-6">
                      <h2 class="text-2xl font-bold text-white hover:underline cursor-pointer">Noticias y Actualidad</h2>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                      <StationCard 
                        v-for="station in radioStore.newsStations" 
                        :key="station.id" 
                        :station="station" 
                      />
                    </div>
                  </section>

                  <!-- Section 3: Música Popular -->
                  <section>
                    <div class="flex items-center justify-between mb-6">
                      <h2 class="text-2xl font-bold text-white hover:underline cursor-pointer">Vallenato y Popular</h2>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                      <StationCard 
                        v-for="station in radioStore.musicStations" 
                        :key="station.id" 
                        :station="station" 
                      />
                    </div>
                  </section>

                </div>
              </div>
            </main>
          </div>

          <!-- C. REPRODUCTOR PERSISTENTE (Footer) -->
          <div class="absolute bottom-0 left-0 right-0 h-24 bg-[#18181b] border-t border-white/10 flex items-center justify-between px-4 z-50 shadow-[0_-4px_20px_rgba(0,0,0,0.5)]">
            
            <!-- Info -->
            <div class="w-[30%] flex items-center gap-4">
              <div v-if="radioStore.currentStation" class="flex items-center gap-4 group">
                <div class="w-14 h-14 bg-zinc-800 rounded shadow-sm overflow-hidden relative">
                  <img :src="radioStore.currentStation.logo" class="w-full h-full object-cover" @error="handleImageError" />
                  <!-- Animation Waves -->
                  <div v-if="radioStore.isPlaying" class="absolute inset-0 bg-black/40 flex items-center justify-center gap-1">
                    <div class="w-1 bg-emerald-500 animate-music-bar-1"></div>
                    <div class="w-1 bg-emerald-500 animate-music-bar-2"></div>
                    <div class="w-1 bg-emerald-500 animate-music-bar-3"></div>
                  </div>
                </div>
                <div>
                  <div class="text-white text-sm font-bold hover:underline cursor-pointer line-clamp-1">{{ radioStore.currentStation.name }}</div>
                  <div class="text-xs text-zinc-400 hover:text-white cursor-pointer transition-colors">En vivo</div>
                </div>
                <button class="text-zinc-400 hover:text-emerald-500 ml-2 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Controles -->
            <div class="w-[40%] flex flex-col items-center justify-center gap-2">
              <div class="flex items-center gap-6">
                <button class="text-zinc-400 hover:text-white transition-colors" title="Shuffle">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                  </svg>
                </button>
                
                <button class="text-zinc-400 hover:text-white transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/>
                  </svg>
                </button>

                <!-- Play/Pause Circular -->
                <button 
                  @click="radioStore.togglePlay()"
                  class="w-10 h-10 bg-white rounded-full flex items-center justify-center hover:scale-105 transition-transform shadow-lg shadow-white/10"
                >
                  <svg v-if="!radioStore.isPlaying" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-black ml-0.5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5v14l11-7z" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-black" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                  </svg>
                </button>

                <button class="text-zinc-400 hover:text-white transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/>
                  </svg>
                </button>

                <button class="text-zinc-400 hover:text-white transition-colors" title="Repeat">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                </button>
              </div>
              
              <!-- Progress Bar (Fake) -->
              <div class="w-full max-w-md flex items-center gap-2 text-xs text-zinc-400 font-mono">
                <span>{{ radioStore.isPlaying ? 'LIVE' : '--:--' }}</span>
                <div class="flex-1 h-1 bg-zinc-600 rounded-full overflow-hidden">
                  <div class="h-full bg-white rounded-full w-full" :class="radioStore.isPlaying ? 'animate-pulse' : ''"></div>
                </div>
                <span>--:--</span>
              </div>
            </div>

            <!-- Volumen -->
            <div class="w-[30%] flex items-center justify-end gap-3">
              <button @click="radioStore.toggleMute()" class="text-zinc-400 hover:text-white">
                <svg v-if="!radioStore.isMuted && radioStore.volume > 0" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                </svg>
              </button>
              <div class="w-24 h-1 bg-zinc-600 rounded-full relative group cursor-pointer">
                <input 
                  type="range" 
                  v-model="radioStore.volume" 
                  @input="radioStore.setVolume($event.target.value)"
                  min="0" 
                  max="100" 
                  class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                />
                <div class="h-full bg-white group-hover:bg-emerald-500 rounded-full transition-colors" :style="{ width: radioStore.volume + '%' }"></div>
              </div>
            </div>

          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, defineComponent, h } from 'vue'
import { useRadioStore } from '../store/radioStore'
import _ from 'lodash' // Assuming lodash is available or we use debounce manually

const props = defineProps({
  isOpen: Boolean
})

const emit = defineEmits(['close'])
const radioStore = useRadioStore()
const searchQuery = ref('')

// Debounced Search
let searchTimeout = null
const handleSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    radioStore.searchStations(searchQuery.value)
  }, 500)
}

const closeModal = () => emit('close')

const goHome = () => {
  radioStore.currentView = 'home'
  searchQuery.value = ''
}

const filterCity = (city, state) => {
  radioStore.fetchByCity(city, state)
}

const handleImageError = (e) => {
  // Fallback icon (Microphone)
  if (e.target && e.target.parentElement) {
    e.target.style.display = 'none'
    e.target.parentElement.classList.add('flex', 'items-center', 'justify-center', 'bg-zinc-800')
    e.target.parentElement.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" class="h-1/2 w-1/2 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
      </svg>
    `
  }
}

onMounted(() => {
  radioStore.fetchHomeData()
})

// Sub-component for Station Card (Inline for simplicity in this file)
const StationCard = defineComponent({
  props: ['station'],
  setup(props) {
    const store = useRadioStore()
    
    const handleCardImageError = (e) => {
       e.target.style.display = 'none'
       e.target.parentElement.classList.add('flex', 'items-center', 'justify-center', 'bg-zinc-800')
       e.target.parentElement.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
        </svg>
      `
    }

    return () => h('div', {
      class: 'group bg-[#18181b] p-4 rounded-lg hover:bg-[#282828] transition-all duration-300 cursor-pointer relative',
      onClick: () => store.playStation(props.station)
    }, [
      // Image Container
      h('div', { class: 'relative aspect-square mb-4 rounded-md overflow-hidden shadow-lg bg-zinc-800' }, [
        props.station.logo 
          ? h('img', { 
              src: props.station.logo, 
              class: 'w-full h-full object-cover',
              onError: handleCardImageError
            })
          : h('div', { class: 'w-full h-full flex items-center justify-center bg-zinc-800' }, [
              h('svg', { class: 'h-12 w-12 text-zinc-600', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
                h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1', d: 'M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z' })
              ])
            ]),
        // Play Button Overlay
        h('div', { class: 'absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end justify-end p-2' }, [
          h('button', { class: 'w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center shadow-xl transform translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 hover:scale-105 hover:bg-emerald-400' }, [
            store.currentStation?.id === props.station.id && store.isPlaying
              ? h('svg', { class: 'h-6 w-6 text-black', viewBox: '0 0 24 24', fill: 'currentColor' }, [ h('path', { d: 'M6 19h4V5H6v14zm8-14v14h4V5h-4z' }) ])
              : h('svg', { class: 'h-6 w-6 text-black ml-1', viewBox: '0 0 24 24', fill: 'currentColor' }, [ h('path', { d: 'M8 5v14l11-7z' }) ])
          ])
        ])
      ]),
      // Info
      h('h3', { class: ['text-white font-bold truncate mb-1', store.currentStation?.id === props.station.id ? 'text-emerald-500' : ''] }, props.station.name),
      h('p', { class: 'text-sm text-zinc-400 line-clamp-1' }, props.station.state || props.station.country || 'Radio Online')
    ])
  }
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #52525b; }

@keyframes music-bar {
  0%, 100% { height: 4px; }
  50% { height: 16px; }
}
.animate-music-bar-1 { animation: music-bar 0.5s ease-in-out infinite; animation-delay: 0s; }
.animate-music-bar-2 { animation: music-bar 0.5s ease-in-out infinite; animation-delay: 0.15s; }
.animate-music-bar-3 { animation: music-bar 0.5s ease-in-out infinite; animation-delay: 0.3s; }
</style>
