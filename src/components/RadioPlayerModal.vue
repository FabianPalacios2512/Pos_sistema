<template>
  <Teleport to="body">
    <Transition name="fade">
      <div 
        v-if="isOpen" 
        class="fixed inset-0 bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4"
        @click="closeModal"
      >
        <!-- Contenedor Principal - Responsive con max-width/max-height + clase para scaling -->
        <div 
          class="radio-modal-container w-full max-w-[1100px] h-[85vh] max-h-[700px] rounded-2xl shadow-2xl overflow-hidden flex flex-col relative transition-colors duration-300"
          :class="isDarkMode ? 'bg-[#0a0a0c] border border-white/10' : 'bg-white border border-gray-200'"
          @click.stop
        >
          
          <!-- Botón Cerrar -->
          <button 
            @click="closeModal"
            class="absolute top-4 right-4 z-50 rounded-full p-2 transition-all duration-200"
            :class="isDarkMode 
              ? 'text-zinc-400 hover:text-white bg-white/5 hover:bg-white/10' 
              : 'text-gray-500 hover:text-gray-900 bg-gray-100 hover:bg-gray-200'"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- LAYOUT PRINCIPAL -->
          <div class="flex-1 flex overflow-hidden pb-24">
            
            <!-- A. SIDEBAR -->
            <aside 
              class="w-64 flex flex-col flex-shrink-0 border-r transition-colors duration-300"
              :class="isDarkMode ? 'bg-[#0a0a0c] border-white/5' : 'bg-gray-50 border-gray-200'"
            >
              <div class="p-6">
                <!-- Logo Radio 105 -->
                <div class="flex items-center gap-3 mb-8">
                  <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z" />
                    </svg>
                  </div>
                  <span class="font-bold text-xl tracking-tight" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Radio 105</span>
                </div>

                <nav class="space-y-2">
                  <button 
                    @click="goHome"
                    class="w-full flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 font-bold group"
                    :class="radioStore.currentView === 'home' 
                      ? (isDarkMode ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-emerald-50 text-emerald-700 border border-emerald-200')
                      : (isDarkMode ? 'text-zinc-400 hover:text-white hover:bg-white/5' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100')"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Inicio
                  </button>
                  
                  <!-- 🎯 Favoritos -->
                  <button 
                    @click="goToFavorites"
                    class="w-full flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 font-bold group"
                    :class="radioStore.currentView === 'favorites' 
                      ? (isDarkMode ? 'bg-pink-500/20 text-pink-400 border border-pink-500/30' : 'bg-pink-50 text-pink-700 border border-pink-200')
                      : (isDarkMode ? 'text-zinc-400 hover:text-white hover:bg-white/5' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100')"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :fill="radioStore.currentView === 'favorites' ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    Favoritos
                    <span 
                      v-if="radioStore.favorites.length > 0"
                      class="ml-auto text-[10px] font-bold px-2 py-0.5 rounded-full"
                      :class="isDarkMode ? 'bg-pink-500/30 text-pink-400' : 'bg-pink-100 text-pink-700'"
                    >
                      {{ radioStore.favorites.length }}
                    </span>
                  </button>
                  
                  <!-- Ciudades -->
                  <div class="pt-6 pb-2">
                    <h3 class="px-4 text-[10px] font-bold uppercase tracking-widest mb-3" :class="isDarkMode ? 'text-zinc-600' : 'text-gray-400'">Ciudades</h3>
                    <div class="space-y-1">
                      <button @click="filterCity('Medellín', 'Antioquia')" 
                        class="w-full text-left px-4 py-2.5 text-sm rounded-lg transition-all duration-200 flex items-center gap-3"
                        :class="isDarkMode ? 'text-zinc-400 hover:text-white hover:bg-white/5' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-lg shadow-emerald-500/50"></span> Medellín
                      </button>
                      <button @click="filterCity('Bogotá', 'Bogota')" 
                        class="w-full text-left px-4 py-2.5 text-sm rounded-lg transition-all duration-200 flex items-center gap-3"
                        :class="isDarkMode ? 'text-zinc-400 hover:text-white hover:bg-white/5' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'">
                        <span class="w-2 h-2 rounded-full bg-blue-500 shadow-lg shadow-blue-500/50"></span> Bogotá
                      </button>
                      <button @click="filterCity('Cali', 'Valle del Cauca')" 
                        class="w-full text-left px-4 py-2.5 text-sm rounded-lg transition-all duration-200 flex items-center gap-3"
                        :class="isDarkMode ? 'text-zinc-400 hover:text-white hover:bg-white/5' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'">
                        <span class="w-2 h-2 rounded-full bg-red-500 shadow-lg shadow-red-500/50"></span> Cali
                      </button>
                      <button @click="filterCity('Costa', 'Atlantico')" 
                        class="w-full text-left px-4 py-2.5 text-sm rounded-lg transition-all duration-200 flex items-center gap-3"
                        :class="isDarkMode ? 'text-zinc-400 hover:text-white hover:bg-white/5' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'">
                        <span class="w-2 h-2 rounded-full bg-amber-500 shadow-lg shadow-amber-500/50"></span> Costa
                      </button>
                    </div>
                  </div>
                </nav>
              </div>
            </aside>

            <!-- B. ÁREA PRINCIPAL -->
            <main 
              class="flex-1 relative overflow-y-auto custom-scrollbar transition-colors duration-300"
              :class="isDarkMode ? 'bg-gradient-to-b from-[#0f0f12] to-[#0a0a0c]' : 'bg-gradient-to-b from-white to-gray-50'"
            >
              
              <!-- Header & Search -->
              <header 
                class="sticky top-0 z-30 backdrop-blur-xl px-8 py-4 flex items-center justify-between border-b transition-colors duration-300"
                :class="isDarkMode ? 'bg-[#0f0f12]/90 border-white/5' : 'bg-white/90 border-gray-200'"
              >
                <div class="relative w-96">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2" :class="isDarkMode ? 'text-zinc-500' : 'text-gray-400'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </span>
                  <input 
                    type="text" 
                    v-model="searchQuery"
                    @input="handleSearch"
                    placeholder="Buscar emisoras, artistas..." 
                    class="w-full pl-12 pr-4 py-3 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2"
                    :class="isDarkMode 
                      ? 'bg-white/5 text-white placeholder-zinc-500 focus:ring-emerald-500/50 border border-white/10' 
                      : 'bg-gray-100 text-gray-900 placeholder-gray-500 focus:ring-emerald-500/50 border border-gray-200'"
                  />
                </div>
                
                <div class="flex items-center gap-4">
                  <span class="text-sm font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ greeting }}</span>
                </div>
              </header>

              <div class="p-8 space-y-10">
                
                <!-- LOADING STATE -->
                <div v-if="radioStore.isLoading" class="flex flex-col items-center justify-center py-20">
                  <div class="relative w-16 h-16">
                    <div class="absolute inset-0 rounded-full border-4 border-emerald-500/20"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-emerald-500 animate-spin"></div>
                  </div>
                  <p class="mt-4 text-sm font-medium" :class="isDarkMode ? 'text-zinc-500' : 'text-gray-500'">Sintonizando emisoras...</p>
                </div>

                <!-- VIEW: SEARCH RESULTS -->
                <div v-else-if="radioStore.currentView === 'search'">
                  <h2 class="text-2xl font-bold mb-6" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Resultados de búsqueda</h2>
                  <div v-if="radioStore.searchResults.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    <StationCard 
                      v-for="station in radioStore.searchResults" 
                      :key="station.id" 
                      :station="station"
                      :isDarkMode="isDarkMode"
                    />
                  </div>
                  <div v-else class="text-center py-16" :class="isDarkMode ? 'text-zinc-500' : 'text-gray-500'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <p>No encontramos emisoras con ese nombre.</p>
                  </div>
                </div>

                <!-- VIEW: CITY FILTER -->
                <div v-else-if="radioStore.currentView === 'city'">
                  <h2 class="text-2xl font-bold mb-6" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Emisoras de {{ radioStore.currentCityTitle }}</h2>
                  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    <StationCard 
                      v-for="station in radioStore.activeCityStations" 
                      :key="station.id" 
                      :station="station"
                      :isDarkMode="isDarkMode"
                    />
                  </div>
                </div>

                <!-- 🎯 VIEW: FAVORITES -->
                <div v-else-if="radioStore.currentView === 'favorites'">
                  <div class="flex items-center gap-4 mb-6">
                    <h2 class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">❤️ Mis Favoritos</h2>
                    <span 
                      v-if="radioStore.favorites.length > 0"
                      class="text-sm px-3 py-1 rounded-full"
                      :class="isDarkMode ? 'bg-pink-500/20 text-pink-400' : 'bg-pink-100 text-pink-700'"
                    >
                      {{ radioStore.favorites.length }} emisoras
                    </span>
                  </div>
                  
                  <div v-if="radioStore.favorites.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    <StationCard 
                      v-for="station in radioStore.favorites" 
                      :key="station.id" 
                      :station="station"
                      :isDarkMode="isDarkMode"
                    />
                  </div>
                  
                  <div v-else class="text-center py-16">
                    <div 
                      class="w-20 h-20 rounded-full mx-auto mb-6 flex items-center justify-center"
                      :class="isDarkMode ? 'bg-zinc-800' : 'bg-gray-100'"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" :class="isDarkMode ? 'text-zinc-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                      </svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Sin favoritos aún</h3>
                    <p class="text-sm" :class="isDarkMode ? 'text-zinc-500' : 'text-gray-500'">
                      Presiona el ❤️ en cualquier emisora para agregarla aquí
                    </p>
                  </div>
                </div>

                <!-- VIEW: HOME (SHOWCASE) -->
                <div v-else class="space-y-10">
                  
                  <!-- Section 1: Top Colombia -->
                  <section>
                    <div class="flex items-center justify-between mb-5">
                      <h2 class="text-xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">🔥 Lo Más Escuchado en Colombia</h2>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                      <StationCard 
                        v-for="station in radioStore.topStations" 
                        :key="station.id" 
                        :station="station"
                        :isDarkMode="isDarkMode"
                      />
                    </div>
                  </section>

                  <!-- Section 2: Noticias -->
                  <section>
                    <div class="flex items-center justify-between mb-5">
                      <h2 class="text-xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">📰 Noticias y Actualidad</h2>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                      <StationCard 
                        v-for="station in radioStore.newsStations" 
                        :key="station.id" 
                        :station="station"
                        :isDarkMode="isDarkMode"
                      />
                    </div>
                  </section>

                  <!-- Section 3: Música Popular -->
                  <section>
                    <div class="flex items-center justify-between mb-5">
                      <h2 class="text-xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">🎵 Vallenato y Popular</h2>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                      <StationCard 
                        v-for="station in radioStore.musicStations" 
                        :key="station.id" 
                        :station="station"
                        :isDarkMode="isDarkMode"
                      />
                    </div>
                  </section>

                </div>
              </div>
            </main>
          </div>

          <!-- C. REPRODUCTOR PERSISTENTE (Footer) - MEJORADO -->
          <div 
            class="absolute bottom-0 left-0 right-0 h-24 flex items-center justify-between px-6 z-50 transition-colors duration-300"
            :class="isDarkMode 
              ? 'bg-gradient-to-t from-black via-[#0a0a0c] to-[#0a0a0c]/95 border-t border-white/5' 
              : 'bg-gradient-to-t from-gray-100 via-white to-white/95 border-t border-gray-200 shadow-[0_-4px_20px_rgba(0,0,0,0.1)]'"
          >
            
            <!-- Info de la Emisora Actual -->
            <div class="w-[30%] flex items-center gap-4">
              <div v-if="radioStore.currentStation" class="flex items-center gap-4 group">
                <div 
                  class="w-14 h-14 rounded-xl shadow-lg overflow-hidden relative ring-2 transition-all duration-300"
                  :class="radioStore.isPlaying 
                    ? 'ring-emerald-500/50 shadow-emerald-500/20' 
                    : (isDarkMode ? 'ring-zinc-800' : 'ring-gray-200')"
                >
                  <img 
                    :src="getStationLogo(radioStore.currentStation)" 
                    :alt="radioStore.currentStation.name"
                    class="w-full h-full object-cover" 
                    @error="handleImageError" 
                  />
                  <!-- Ondas de Audio Animadas -->
                  <div v-if="radioStore.isPlaying" class="absolute inset-0 bg-black/50 flex items-center justify-center gap-0.5">
                    <div class="w-1 bg-emerald-400 rounded-full animate-music-bar-1"></div>
                    <div class="w-1 bg-emerald-400 rounded-full animate-music-bar-2"></div>
                    <div class="w-1 bg-emerald-400 rounded-full animate-music-bar-3"></div>
                    <div class="w-1 bg-emerald-400 rounded-full animate-music-bar-2"></div>
                  </div>
                </div>
                <div class="min-w-0">
                  <div class="font-bold text-sm truncate max-w-[180px]" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
                    {{ radioStore.currentStation.name }}
                  </div>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span 
                      v-if="radioStore.isPlaying" 
                      class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-emerald-500/20 text-emerald-500"
                    >
                      <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                      EN VIVO
                    </span>
                    <span v-else class="text-xs" :class="isDarkMode ? 'text-zinc-500' : 'text-gray-500'">Pausado</span>
                  </div>
                </div>
                <button 
                  class="ml-2 p-2 rounded-full transition-all duration-200"
                  :class="[
                    radioStore.isFavorite(radioStore.currentStation?.id)
                      ? 'text-pink-500 hover:text-pink-400'
                      : (isDarkMode ? 'text-zinc-400 hover:text-pink-400 hover:bg-white/5' : 'text-gray-400 hover:text-pink-500 hover:bg-gray-100')
                  ]"
                  @click="toggleCurrentFavorite"
                  title="Agregar a favoritos"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :fill="radioStore.isFavorite(radioStore.currentStation?.id) ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                </button>
              </div>
              <!-- Estado vacío -->
              <div v-else class="flex items-center gap-3" :class="isDarkMode ? 'text-zinc-600' : 'text-gray-400'">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" :class="isDarkMode ? 'bg-zinc-900' : 'bg-gray-100'">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                  </svg>
                </div>
                <span class="text-sm">Selecciona una emisora</span>
              </div>
            </div>

            <!-- Controles Centrales -->
            <div class="w-[40%] flex flex-col items-center justify-center gap-3">
              <div class="flex items-center gap-5">
                <button 
                  @click="radioStore.playRandom()"
                  class="p-2 rounded-full transition-all duration-200"
                  :class="isDarkMode ? 'text-zinc-500 hover:text-white hover:bg-white/5' : 'text-gray-400 hover:text-gray-700 hover:bg-gray-100'"
                  title="Emisora aleatoria"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                  </svg>
                </button>
                
                <button 
                  @click="radioStore.playPrevious()"
                  class="p-2 rounded-full transition-all duration-200"
                  :class="isDarkMode ? 'text-zinc-400 hover:text-white hover:bg-white/5' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                  title="Emisora anterior"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/>
                  </svg>
                </button>

                <!-- Botón Play/Pause Principal -->
                <button 
                  @click="radioStore.togglePlay()"
                  class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-200 shadow-lg"
                  :class="radioStore.isPlaying 
                    ? 'bg-emerald-500 hover:bg-emerald-400 text-white shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:scale-105' 
                    : (isDarkMode ? 'bg-white hover:bg-gray-100 text-black' : 'bg-gray-900 hover:bg-black text-white')"
                >
                  <svg v-if="!radioStore.isPlaying" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-0.5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5v14l11-7z" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                  </svg>
                </button>

                <button 
                  @click="radioStore.playNext()"
                  class="p-2 rounded-full transition-all duration-200"
                  :class="isDarkMode ? 'text-zinc-400 hover:text-white hover:bg-white/5' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                  title="Siguiente emisora"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/>
                  </svg>
                </button>

                <button 
                  class="p-2 rounded-full transition-all duration-200"
                  :class="isDarkMode ? 'text-zinc-500 hover:text-white hover:bg-white/5' : 'text-gray-400 hover:text-gray-700 hover:bg-gray-100'"
                  title="Repetir"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                </button>
              </div>
              
              <!-- 🎯 Barra de Progreso Mejorada con Animación Dinámica -->
              <div class="w-full max-w-md flex items-center gap-3">
                <span 
                  class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-md flex items-center gap-1.5"
                  :class="radioStore.isPlaying 
                    ? 'bg-red-500/20 text-red-500' 
                    : (isDarkMode ? 'bg-zinc-800 text-zinc-500' : 'bg-gray-200 text-gray-500')"
                >
                  <span v-if="radioStore.isPlaying" class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                  {{ radioStore.isPlaying ? 'LIVE' : 'PAUSA' }}
                </span>
                <div class="flex-1 h-1.5 rounded-full overflow-hidden relative" :class="isDarkMode ? 'bg-zinc-800' : 'bg-gray-200'">
                  <!-- Barra animada que se mueve cuando está sonando -->
                  <div 
                    v-if="radioStore.isPlaying"
                    class="absolute inset-0 overflow-hidden"
                  >
                    <div class="h-full bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-500 animate-progress-wave" style="width: 200%;"></div>
                  </div>
                  <div 
                    v-else
                    class="h-full rounded-full"
                    :class="isDarkMode ? 'bg-zinc-700 w-0' : 'bg-gray-300 w-0'"
                  ></div>
                </div>
                <span class="text-xs font-mono" :class="isDarkMode ? 'text-zinc-600' : 'text-gray-400'">∞</span>
              </div>
            </div>

            <!-- Volumen -->
            <div class="w-[30%] flex items-center justify-end gap-3">
              <button 
                @click="radioStore.toggleMute()" 
                class="p-2 rounded-full transition-colors"
                :class="isDarkMode ? 'text-zinc-400 hover:text-white hover:bg-white/5' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
              >
                <svg v-if="!radioStore.isMuted && radioStore.volume > 0" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                </svg>
              </button>
              <div class="w-28 h-1.5 rounded-full relative group cursor-pointer" :class="isDarkMode ? 'bg-zinc-800' : 'bg-gray-200'">
                <input 
                  type="range" 
                  v-model="radioStore.volume" 
                  @input="radioStore.setVolume($event.target.value)"
                  min="0" 
                  max="100" 
                  class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                />
                <div 
                  class="h-full rounded-full transition-all duration-200 group-hover:bg-emerald-400"
                  :class="isDarkMode ? 'bg-white' : 'bg-gray-900'"
                  :style="{ width: radioStore.volume + '%' }"
                ></div>
                <div 
                  class="absolute top-1/2 -translate-y-1/2 w-3 h-3 rounded-full opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                  :class="isDarkMode ? 'bg-white' : 'bg-gray-900'"
                  :style="{ left: `calc(${radioStore.volume}% - 6px)` }"
                ></div>
              </div>
              <span class="text-xs w-8 text-right font-mono" :class="isDarkMode ? 'text-zinc-500' : 'text-gray-500'">{{ radioStore.volume }}%</span>
            </div>

          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, defineComponent, h } from 'vue'
import { useRadioStore } from '../store/radioStore'

const props = defineProps({
  isOpen: Boolean
})

const emit = defineEmits(['close'])
const radioStore = useRadioStore()
const searchQuery = ref('')

// 🎯 Usar el modo oscuro del POS (localStorage: 'pos-dark-mode')
const isDarkMode = ref(true)

// Sincronizar con el tema del POS
const syncThemeWithPOS = () => {
  const posDarkMode = localStorage.getItem('pos-dark-mode')
  isDarkMode.value = posDarkMode === 'true'
}

// Observar cambios en localStorage (cuando el usuario cambia el tema en el POS)
const handleStorageChange = (e) => {
  if (e.key === 'pos-dark-mode') {
    isDarkMode.value = e.newValue === 'true'
  }
}

// Saludo dinámico según la hora
const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour >= 5 && hour < 12) return '☀️ Buenos días'
  if (hour >= 12 && hour < 19) return '🌤️ Buenas tardes'
  return '🌙 Buenas noches'
})

// Logos de emisoras conocidas colombianas - AMPLIADO
const knownLogos = {
  // 🔥 Principales Cadenas
  'la mega': 'https://cdn-profiles.tunein.com/s22867/images/logog.png',
  'mega 90': 'https://cdn-profiles.tunein.com/s22867/images/logog.png',
  'mega 92': 'https://cdn-profiles.tunein.com/s22867/images/logog.png',
  'olimpica': 'https://cdn-profiles.tunein.com/s13980/images/logog.png',
  'olímpica': 'https://cdn-profiles.tunein.com/s13980/images/logog.png',
  'caracol': 'https://cdn-profiles.tunein.com/s2906/images/logog.png',
  'tropicana': 'https://cdn-profiles.tunein.com/s6556/images/logog.png',
  'w radio': 'https://cdn-profiles.tunein.com/s23177/images/logog.png',
  'la w': 'https://cdn-profiles.tunein.com/s23177/images/logog.png',
  'rcn': 'https://cdn-profiles.tunein.com/s2895/images/logog.png',
  'rcn radio': 'https://cdn-profiles.tunein.com/s2895/images/logog.png',
  
  // 📻 Noticias y Talk
  'blu radio': 'https://cdn-profiles.tunein.com/s187198/images/logog.png',
  'blu': 'https://cdn-profiles.tunein.com/s187198/images/logog.png',
  'la fm': 'https://cdn-profiles.tunein.com/s6500/images/logog.png',
  
  // 🎵 Música
  'los 40': 'https://cdn-profiles.tunein.com/s23013/images/logog.png',
  'los40': 'https://cdn-profiles.tunein.com/s23013/images/logog.png',
  'radio uno': 'https://cdn-profiles.tunein.com/s130293/images/logog.png',
  'candela': 'https://cdn-profiles.tunein.com/s6635/images/logog.png',
  'oxigeno': 'https://cdn-profiles.tunein.com/s27347/images/logog.png',
  'oxígeno': 'https://cdn-profiles.tunein.com/s27347/images/logog.png',
  'vibra': 'https://cdn-profiles.tunein.com/s197697/images/logog.png',
  'vibra bogotá': 'https://cdn-profiles.tunein.com/s197697/images/logog.png',
  'radioacktiva': 'https://cdn-profiles.tunein.com/s6536/images/logog.png',
  'la x': 'https://cdn-profiles.tunein.com/s6525/images/logog.png',
  'bésame': 'https://cdn-profiles.tunein.com/s6565/images/logog.png',
  'besame': 'https://cdn-profiles.tunein.com/s6565/images/logog.png',
  'radio tiempo': 'https://cdn-profiles.tunein.com/s6574/images/logog.png',
  'la z': 'https://cdn-profiles.tunein.com/s133595/images/logog.png',
  
  // ☀️ El Sol y Mix
  'el sol': 'https://cdn-profiles.tunein.com/s6578/images/logog.png',
  'sol caracol': 'https://cdn-profiles.tunein.com/s6578/images/logog.png',
  'mix': 'https://cdn-profiles.tunein.com/s6543/images/logog.png',
  'la mix': 'https://cdn-profiles.tunein.com/s6543/images/logog.png',
  
  // 🎶 Vallenato y Popular
  'radio cristal': 'https://cdn-profiles.tunein.com/s6591/images/logog.png',
  'cristal': 'https://cdn-profiles.tunein.com/s6591/images/logog.png',
  'vallenata': 'https://cdn-profiles.tunein.com/s127044/images/logog.png',
  'la vallenata': 'https://cdn-profiles.tunein.com/s127044/images/logog.png',
  'radio santa fe': 'https://cdn-profiles.tunein.com/s6511/images/logog.png',
  'santa fe': 'https://cdn-profiles.tunein.com/s6511/images/logog.png',
  'radio sutatenza': 'https://cdn-profiles.tunein.com/s6520/images/logog.png',
  
  // 🎸 Rock y Alternativa
  'radio hit': 'https://cdn-profiles.tunein.com/s57314/images/logog.png',
  'hit fm': 'https://cdn-profiles.tunein.com/s57314/images/logog.png',
  'super radio': 'https://cdn-profiles.tunein.com/s7221/images/logog.png',
  
  // 🌴 Costa/Caribe
  'radio libertad': 'https://cdn-profiles.tunein.com/s6584/images/logog.png',
  'atlántico': 'https://cdn-profiles.tunein.com/s23212/images/logog.png',
  'atlantico': 'https://cdn-profiles.tunein.com/s23212/images/logog.png',
  
  // 🏔️ Regionales
  'ecos del combeima': 'https://cdn-profiles.tunein.com/s22869/images/logog.png',
  'radio paisa': 'https://cdn-profiles.tunein.com/s6500/images/logog.png',
  'minuto de dios': 'https://cdn-profiles.tunein.com/s22889/images/logog.png',
  'emisora minuto': 'https://cdn-profiles.tunein.com/s22889/images/logog.png',
  
  // 🎧 Electrónica/Urbana  
  'energia': 'https://cdn-profiles.tunein.com/s138085/images/logog.png',
  'energía': 'https://cdn-profiles.tunein.com/s138085/images/logog.png',
  'planet radio': 'https://cdn-profiles.tunein.com/s300013/images/logog.png',
}

// Función para obtener logo de la emisora
const getStationLogo = (station) => {
  if (!station) return null
  
  // Primero intentar con el logo de la API
  if (station.logo && !station.logo.includes('placeholder')) {
    return station.logo
  }
  
  // Buscar en logos conocidos
  const nameLower = station.name.toLowerCase()
  for (const [key, url] of Object.entries(knownLogos)) {
    if (nameLower.includes(key)) {
      return url
    }
  }
  
  return station.logo || null
}

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

const goToFavorites = () => {
  radioStore.currentView = 'favorites'
  searchQuery.value = ''
}

const toggleCurrentFavorite = () => {
  if (radioStore.currentStation) {
    radioStore.toggleFavorite(radioStore.currentStation)
  }
}

const filterCity = (city, state) => {
  radioStore.fetchByCity(city, state)
}

const handleImageError = (e) => {
  if (e.target && e.target.parentElement) {
    e.target.style.display = 'none'
    const parent = e.target.parentElement
    
    // Generar color basado en nombre de la estación
    const colors = [
      'from-emerald-500 to-teal-600',
      'from-blue-500 to-indigo-600', 
      'from-purple-500 to-pink-600',
      'from-amber-500 to-orange-600'
    ]
    const stationName = radioStore.currentStation?.name || 'Radio'
    const colorIndex = stationName.length % colors.length
    
    // Obtener iniciales
    const initials = stationName
      .split(' ')
      .filter(word => word.length > 0)
      .slice(0, 2)
      .map(word => word[0].toUpperCase())
      .join('')
    
    parent.classList.add('flex', 'items-center', 'justify-center', 'bg-gradient-to-br', ...colors[colorIndex].split(' '))
    parent.innerHTML = `
      <span class="text-white font-bold text-lg drop-shadow-lg">${initials || '📻'}</span>
    `
  }
}

let themeInterval = null

onMounted(() => {
  syncThemeWithPOS()
  window.addEventListener('storage', handleStorageChange)
  radioStore.fetchHomeData()
  
  // También verificar periódicamente el tema (por si se cambia en la misma pestaña)
  themeInterval = setInterval(syncThemeWithPOS, 500)
})

onUnmounted(() => {
  if (themeInterval) clearInterval(themeInterval)
  window.removeEventListener('storage', handleStorageChange)
})

// Sub-component for Station Card - MEJORADO con más logos
const StationCard = defineComponent({
  props: ['station', 'isDarkMode'],
  setup(props) {
    const store = useRadioStore()
    
    // Usar el mismo mapeo de logos que el componente padre
    const getCardLogo = (station) => {
      // Primero intentar con el logo de la API si es válido
      if (station.logo && 
          !station.logo.includes('placeholder') && 
          station.logo.startsWith('http')) {
        return station.logo
      }
      
      // Buscar en logos conocidos
      const nameLower = station.name.toLowerCase()
      for (const [key, url] of Object.entries(knownLogos)) {
        if (nameLower.includes(key)) {
          return url
        }
      }
      
      // Si tiene logo de la API, usarlo como fallback
      if (station.logo && station.logo.startsWith('http')) {
        return station.logo
      }
      
      return null
    }
    
    const handleCardImageError = (e) => {
       e.target.style.display = 'none'
       const parent = e.target.parentElement
       
       // Generar color de fondo basado en el nombre de la estación
       const colors = [
         'from-emerald-500 to-teal-600',
         'from-blue-500 to-indigo-600', 
         'from-purple-500 to-pink-600',
         'from-amber-500 to-orange-600',
         'from-rose-500 to-red-600',
         'from-cyan-500 to-blue-600',
         'from-violet-500 to-purple-600',
         'from-fuchsia-500 to-pink-600'
       ]
       const colorIndex = props.station.name.length % colors.length
       const gradientClass = colors[colorIndex]
       
       // Obtener iniciales (máximo 2 caracteres)
       const initials = props.station.name
         .split(' ')
         .filter(word => word.length > 0)
         .slice(0, 2)
         .map(word => word[0].toUpperCase())
         .join('')
       
       parent.classList.add('flex', 'items-center', 'justify-center', 'bg-gradient-to-br', ...gradientClass.split(' '))
       parent.innerHTML = `
        <span class="text-white font-black text-2xl tracking-tight drop-shadow-lg">${initials || '📻'}</span>
      `
    }

    // Generar color basado en nombre
    const getGradientColor = (name) => {
      const colors = [
        'bg-gradient-to-br from-emerald-500 to-teal-600',
        'bg-gradient-to-br from-blue-500 to-indigo-600', 
        'bg-gradient-to-br from-purple-500 to-pink-600',
        'bg-gradient-to-br from-amber-500 to-orange-600',
        'bg-gradient-to-br from-rose-500 to-red-600',
        'bg-gradient-to-br from-cyan-500 to-blue-600',
        'bg-gradient-to-br from-violet-500 to-purple-600',
        'bg-gradient-to-br from-fuchsia-500 to-pink-600'
      ]
      return colors[name.length % colors.length]
    }
    
    // Obtener iniciales
    const getInitials = (name) => {
      return name
        .split(' ')
        .filter(word => word.length > 0)
        .slice(0, 2)
        .map(word => word[0].toUpperCase())
        .join('')
    }

    return () => h('div', {
      class: [
        'group p-4 rounded-xl transition-all duration-300 cursor-pointer relative border',
        props.isDarkMode 
          ? 'bg-zinc-900/50 hover:bg-zinc-800/80 border-zinc-800/50 hover:border-zinc-700' 
          : 'bg-white hover:bg-gray-50 border-gray-200 hover:border-gray-300 shadow-sm hover:shadow-md'
      ],
      onClick: () => store.playStation(props.station)
    }, [
      // Image Container
      h('div', { class: 'relative aspect-square mb-4 rounded-xl overflow-hidden shadow-lg' }, [
        getCardLogo(props.station)
          ? h('img', { 
              src: getCardLogo(props.station), 
              class: 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105',
              onError: handleCardImageError
            })
          : h('div', { 
              class: ['w-full h-full flex items-center justify-center', getGradientColor(props.station.name)] 
            }, [
              h('span', { 
                class: 'text-white font-black text-2xl tracking-tight drop-shadow-lg'
              }, getInitials(props.station.name) || '📻')
            ]),
        // Play Button Overlay
        h('div', { 
          class: 'absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end justify-end p-3' 
        }, [
          h('button', { 
            class: 'w-11 h-11 bg-emerald-500 rounded-full flex items-center justify-center shadow-xl transform translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 hover:scale-110 hover:bg-emerald-400' 
          }, [
            store.currentStation?.id === props.station.id && store.isPlaying
              ? h('svg', { class: 'h-5 w-5 text-black', viewBox: '0 0 24 24', fill: 'currentColor' }, [ 
                  h('path', { d: 'M6 19h4V5H6v14zm8-14v14h4V5h-4z' }) 
                ])
              : h('svg', { class: 'h-5 w-5 text-black ml-0.5', viewBox: '0 0 24 24', fill: 'currentColor' }, [ 
                  h('path', { d: 'M8 5v14l11-7z' }) 
                ])
          ])
        ]),
        // Favorite Button (esquina superior derecha)
        h('button', { 
          class: [
            'absolute top-2 right-2 z-10 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200',
            store.isFavorite(props.station.id) 
              ? 'bg-pink-500 text-white shadow-lg shadow-pink-500/30' 
              : 'bg-black/40 text-white/70 hover:bg-black/60 hover:text-white backdrop-blur-sm opacity-0 group-hover:opacity-100'
          ],
          onClick: (e) => {
            e.stopPropagation()
            store.toggleFavorite(props.station)
          }
        }, [
          h('svg', { 
            class: 'h-4 w-4', 
            fill: store.isFavorite(props.station.id) ? 'currentColor' : 'none',
            viewBox: '0 0 24 24', 
            stroke: 'currentColor',
            'stroke-width': '2'
          }, [
            h('path', { 
              'stroke-linecap': 'round', 
              'stroke-linejoin': 'round',
              d: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'
            })
          ])
        ]),
        // Live indicator if playing
        store.currentStation?.id === props.station.id && store.isPlaying
          ? h('div', { 
              class: 'absolute top-2 left-2 px-2 py-0.5 bg-red-500 rounded-full text-[9px] font-bold text-white uppercase tracking-wider flex items-center gap-1.5' 
            }, [
              h('span', { class: 'w-1.5 h-1.5 bg-white rounded-full animate-pulse' }),
              'LIVE'
            ])
          : null
      ]),
      // Info
      h('h3', { 
        class: [
          'font-bold truncate mb-1 text-sm', 
          store.currentStation?.id === props.station.id 
            ? 'text-emerald-500' 
            : (props.isDarkMode ? 'text-white' : 'text-gray-900')
        ] 
      }, props.station.name),
      h('p', { 
        class: ['text-xs line-clamp-1', props.isDarkMode ? 'text-zinc-500' : 'text-gray-500'] 
      }, props.station.state || props.station.country || 'Radio Online')
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

/* Custom Scrollbar - Adaptativo */
.custom-scrollbar::-webkit-scrollbar { width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #52525b; }

/* Animaciones de barras de música */
@keyframes music-bar {
  0%, 100% { height: 4px; }
  50% { height: 16px; }
}
.animate-music-bar-1 { animation: music-bar 0.5s ease-in-out infinite; animation-delay: 0s; }
.animate-music-bar-2 { animation: music-bar 0.5s ease-in-out infinite; animation-delay: 0.15s; }
.animate-music-bar-3 { animation: music-bar 0.5s ease-in-out infinite; animation-delay: 0.3s; }

/* 🎯 Animación de barra de progreso que se mueve como onda */
@keyframes progress-wave {
  0% { transform: translateX(-50%); }
  100% { transform: translateX(0%); }
}
.animate-progress-wave {
  animation: progress-wave 2s linear infinite;
}

/* Animación de barra LIVE (respaldo) */
@keyframes live-bar {
  0% { opacity: 0.7; }
  50% { opacity: 1; }
  100% { opacity: 0.7; }
}
.animate-live-bar {
  animation: live-bar 2s ease-in-out infinite;
}
</style>
