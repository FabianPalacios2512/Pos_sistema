<template>
  <Teleport to="body">
    <Transition name="fade">
      <div 
        v-if="isOpen" 
        class="fixed inset-0 bg-black/60 z-[100] flex items-center justify-center p-4"
        @click="closeModal"
      >
        <!-- Contenedor Principal - Estilo Linear/Vercel -->
        <div 
          class="radio-modal-container w-full max-w-[1100px] h-[85vh] max-h-[720px] rounded-2xl shadow-xl overflow-hidden flex flex-col relative font-sans"
          :class="isDarkMode ? 'bg-[#09090b] border border-zinc-800' : 'bg-white border border-slate-200'"
          @click.stop
        >
          
          <!-- Botón Cerrar - Minimalista -->
          <button 
            @click="closeModal"
            class="absolute top-5 right-5 z-50 rounded-lg p-2 transition-all duration-200"
            :class="isDarkMode 
              ? 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800' 
              : 'text-slate-400 hover:text-slate-600 hover:bg-slate-100'"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- LAYOUT PRINCIPAL - Sidebar + Content -->
          <div class="flex-1 flex overflow-hidden">
            
            <!-- SIDEBAR - Estilo Linear -->
            <aside 
              class="w-56 flex flex-col flex-shrink-0 border-r"
              :class="isDarkMode ? 'bg-[#09090b] border-zinc-800/80' : 'bg-slate-50/50 border-slate-200'"
            >
              <div class="p-5">
                <!-- Logo Radio 105 - Clean -->
                <div class="flex items-center gap-2.5 mb-8">
                  <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                    <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                    </svg>
                  </div>
                  <span class="font-semibold text-base" :class="isDarkMode ? 'text-white' : 'text-slate-900'">Radio 105</span>
                </div>

                <!-- Navegación Principal -->
                <nav class="space-y-1">
                  <button 
                    @click="goHome"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-100 text-sm font-medium"
                    :class="radioStore.currentView === 'home' 
                      ? (isDarkMode ? 'bg-zinc-800 text-white' : 'bg-slate-200/80 text-slate-900')
                      : (isDarkMode ? 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100')"
                  >
                    <!-- Lucide: Home -->
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.5L12 4l9 5.5M4 10v9a1 1 0 001 1h4v-5a1 1 0 011-1h4a1 1 0 011 1v5h4a1 1 0 001-1v-9" />
                    </svg>
                    Inicio
                  </button>
                  
                  <!-- Favoritos -->
                  <button 
                    @click="goToFavorites"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-100 text-sm font-medium"
                    :class="radioStore.currentView === 'favorites' 
                      ? (isDarkMode ? 'bg-zinc-800 text-white' : 'bg-slate-200/80 text-slate-900')
                      : (isDarkMode ? 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100')"
                  >
                    <!-- Lucide: Heart -->
                    <svg class="h-4 w-4" :fill="radioStore.currentView === 'favorites' ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                    </svg>
                    Favoritos
                    <span 
                      v-if="radioStore.favorites.length > 0"
                      class="ml-auto text-[10px] font-semibold px-1.5 py-0.5 rounded-md"
                      :class="isDarkMode ? 'bg-zinc-700 text-zinc-300' : 'bg-slate-200 text-slate-600'"
                    >
                      {{ radioStore.favorites.length }}
                    </span>
                  </button>

                  <!-- Tendencias -->
                  <button 
                    @click="goHome"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-100 text-sm font-medium"
                    :class="isDarkMode ? 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                  >
                    <!-- Lucide: TrendingUp -->
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M23 6l-9.5 9.5-5-5L1 18" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 6h6v6" />
                    </svg>
                    Tendencias
                  </button>
                </nav>
                  
                <!-- Ciudades -->
                <div class="mt-8">
                  <h3 class="px-3 text-[11px] font-semibold uppercase tracking-wider mb-2" :class="isDarkMode ? 'text-zinc-600' : 'text-slate-400'">Ciudades</h3>
                  <div class="space-y-0.5">
                    <button @click="filterCity('Medellín', 'Antioquia')" 
                      class="w-full text-left px-3 py-2 text-sm rounded-lg transition-all duration-100 flex items-center gap-2.5"
                      :class="isDarkMode ? 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Medellín
                    </button>
                    <button @click="filterCity('Bogotá', 'Bogota')" 
                      class="w-full text-left px-3 py-2 text-sm rounded-lg transition-all duration-100 flex items-center gap-2.5"
                      :class="isDarkMode ? 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">
                      <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Bogotá
                    </button>
                    <button @click="filterCity('Cali', 'Valle del Cauca')" 
                      class="w-full text-left px-3 py-2 text-sm rounded-lg transition-all duration-100 flex items-center gap-2.5"
                      :class="isDarkMode ? 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">
                      <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Cali
                    </button>
                    <button @click="filterCity('Costa', 'Atlantico')" 
                      class="w-full text-left px-3 py-2 text-sm rounded-lg transition-all duration-100 flex items-center gap-2.5"
                      :class="isDarkMode ? 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">
                      <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Costa
                    </button>
                  </div>
                </div>
              </div>
            </aside>

            <!-- ÁREA PRINCIPAL -->
            <main 
              class="flex-1 relative overflow-y-auto custom-scrollbar pb-28"
              :class="isDarkMode ? 'bg-[#09090b]' : 'bg-white'"
            >
              
              <!-- Header con Search - Estilo Linear -->
              <header 
                class="sticky top-0 z-30 px-6 py-4 border-b"
                :class="isDarkMode ? 'bg-[#09090b] border-zinc-800/80' : 'bg-white border-slate-100'"
              >
                <div class="flex items-center gap-4">
                  <span class="text-sm font-medium" :class="isDarkMode ? 'text-zinc-400' : 'text-slate-500'">{{ greeting }}</span>
                  <div class="relative flex-1 max-w-sm">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2" :class="isDarkMode ? 'text-zinc-500' : 'text-slate-400'">
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                    </span>
                    <input 
                      type="text" 
                      v-model="searchQuery"
                      @input="handleSearch"
                      placeholder="Buscar emisoras..." 
                      class="w-full pl-9 pr-4 py-2 rounded-lg text-sm transition-all duration-100 focus:outline-none focus:ring-2"
                      :class="isDarkMode 
                        ? 'bg-zinc-800/50 text-white placeholder-zinc-500 focus:ring-zinc-700 border border-zinc-700/50' 
                        : 'bg-slate-100 text-slate-900 placeholder-slate-400 focus:ring-slate-200 border border-transparent'"
                    />
                  </div>
                </div>
              </header>

              <div class="p-6 space-y-8">
                
                <!-- LOADING STATE con Skeleton -->
                <div v-if="radioStore.isLoading" class="space-y-8">
                  <!-- Skeleton Section Title -->
                  <div class="h-6 w-48 rounded-md animate-pulse" :class="isDarkMode ? 'bg-zinc-800' : 'bg-slate-200'"></div>
                  <!-- Skeleton Grid -->
                  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div v-for="n in 8" :key="n" class="space-y-3">
                      <div class="aspect-square rounded-xl animate-pulse" :class="isDarkMode ? 'bg-zinc-800' : 'bg-slate-100'"></div>
                      <div class="h-4 w-3/4 rounded animate-pulse" :class="isDarkMode ? 'bg-zinc-800' : 'bg-slate-200'"></div>
                    </div>
                  </div>
                </div>

                <!-- VIEW: SEARCH RESULTS -->
                <div v-else-if="radioStore.currentView === 'search'">
                  <h2 class="text-lg font-semibold mb-5" :class="isDarkMode ? 'text-white' : 'text-slate-900'">Resultados</h2>
                  <div v-if="radioStore.searchResults.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <StationTile 
                      v-for="station in radioStore.searchResults" 
                      :key="station.id" 
                      :station="station"
                      :isDarkMode="isDarkMode"
                    />
                  </div>
                  <div v-else class="text-center py-16" :class="isDarkMode ? 'text-zinc-500' : 'text-slate-400'">
                    <svg class="h-10 w-10 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <p class="text-sm">Sin resultados</p>
                  </div>
                </div>

                <!-- VIEW: CITY FILTER -->
                <div v-else-if="radioStore.currentView === 'city'">
                  <h2 class="text-lg font-semibold mb-5" :class="isDarkMode ? 'text-white' : 'text-slate-900'">{{ radioStore.currentCityTitle }}</h2>
                  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <StationTile 
                      v-for="station in radioStore.activeCityStations" 
                      :key="station.id" 
                      :station="station"
                      :isDarkMode="isDarkMode"
                    />
                  </div>
                </div>

                <!-- VIEW: FAVORITES -->
                <div v-else-if="radioStore.currentView === 'favorites'">
                  <div class="flex items-center gap-3 mb-5">
                    <h2 class="text-lg font-semibold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">Favoritos</h2>
                    <span 
                      v-if="radioStore.favorites.length > 0"
                      class="text-xs px-2 py-0.5 rounded-md font-medium"
                      :class="isDarkMode ? 'bg-zinc-800 text-zinc-400' : 'bg-slate-100 text-slate-500'"
                    >
                      {{ radioStore.favorites.length }}
                    </span>
                  </div>
                  
                  <div v-if="radioStore.favorites.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <StationTile 
                      v-for="station in radioStore.favorites" 
                      :key="station.id" 
                      :station="station"
                      :isDarkMode="isDarkMode"
                    />
                  </div>
                  
                  <div v-else class="text-center py-16">
                    <div 
                      class="w-14 h-14 rounded-xl mx-auto mb-4 flex items-center justify-center"
                      :class="isDarkMode ? 'bg-zinc-800' : 'bg-slate-100'"
                    >
                      <svg class="h-6 w-6" :class="isDarkMode ? 'text-zinc-600' : 'text-slate-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                      </svg>
                    </div>
                    <p class="text-sm font-medium mb-1" :class="isDarkMode ? 'text-zinc-400' : 'text-slate-600'">Sin favoritos</p>
                    <p class="text-xs" :class="isDarkMode ? 'text-zinc-600' : 'text-slate-400'">
                      Haz clic en el corazón para guardar
                    </p>
                  </div>
                </div>

                <!-- VIEW: HOME -->
                <div v-else class="space-y-8">
                  
                  <!-- Section 1: Top Colombia -->
                  <section>
                    <h2 class="text-base font-semibold mb-4" :class="isDarkMode ? 'text-white' : 'text-slate-900'">Lo más escuchado</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                      <StationTile 
                        v-for="station in radioStore.topStations" 
                        :key="station.id" 
                        :station="station"
                        :isDarkMode="isDarkMode"
                      />
                    </div>
                  </section>

                  <!-- Section 2: Noticias -->
                  <section>
                    <h2 class="text-base font-semibold mb-4" :class="isDarkMode ? 'text-white' : 'text-slate-900'">Noticias</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                      <StationTile 
                        v-for="station in radioStore.newsStations" 
                        :key="station.id" 
                        :station="station"
                        :isDarkMode="isDarkMode"
                      />
                    </div>
                  </section>

                  <!-- Section 3: Música -->
                  <section>
                    <h2 class="text-base font-semibold mb-4" :class="isDarkMode ? 'text-white' : 'text-slate-900'">Música popular</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                      <StationTile 
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

          <!-- PLAYER BAR - Sticky Bottom - Estilo Linear/Vercel -->
          <div 
            class="absolute bottom-0 left-0 right-0 h-20 flex items-center justify-between px-5 z-50 border-t"
            :class="isDarkMode 
              ? 'bg-[#09090b] border-zinc-800' 
              : 'bg-white border-slate-200'"
          >
            
            <!-- Info Emisora Actual -->
            <div class="w-[28%] flex items-center gap-3">
              <div v-if="radioStore.currentStation" class="flex items-center gap-3">
                <div 
                  class="w-12 h-12 rounded-lg overflow-hidden relative flex-shrink-0"
                  :class="isDarkMode ? 'bg-zinc-800' : 'bg-slate-100'"
                >
                  <img 
                    :src="getStationLogo(radioStore.currentStation)" 
                    :alt="radioStore.currentStation.name"
                    class="w-full h-full object-cover" 
                    @error="handleImageError" 
                  />
                  <!-- Indicador reproduciéndose -->
                  <div v-if="radioStore.isPlaying" class="absolute inset-0 bg-black/40 flex items-center justify-center gap-0.5">
                    <div class="w-0.5 bg-emerald-400 rounded-full animate-music-bar-1"></div>
                    <div class="w-0.5 bg-emerald-400 rounded-full animate-music-bar-2"></div>
                    <div class="w-0.5 bg-emerald-400 rounded-full animate-music-bar-3"></div>
                  </div>
                </div>
                <div class="min-w-0">
                  <div class="font-medium text-sm truncate max-w-[160px]" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                    {{ radioStore.currentStation.name }}
                  </div>
                  <div class="flex items-center gap-1.5 mt-0.5">
                    <span 
                      v-if="radioStore.isPlaying" 
                      class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wide bg-emerald-500/15 text-emerald-500"
                    >
                      <span class="w-1 h-1 bg-emerald-500 rounded-full animate-pulse"></span>
                      En vivo
                    </span>
                    <span v-else class="text-xs" :class="isDarkMode ? 'text-zinc-500' : 'text-slate-400'">Pausado</span>
                  </div>
                </div>
                <!-- Botón Favorito -->
                <button 
                  class="p-1.5 rounded-md transition-colors duration-100"
                  :class="[
                    radioStore.isFavorite(radioStore.currentStation?.id)
                      ? 'text-rose-500'
                      : (isDarkMode ? 'text-zinc-500 hover:text-zinc-300' : 'text-slate-400 hover:text-slate-600')
                  ]"
                  @click="toggleCurrentFavorite"
                >
                  <svg class="h-4 w-4" :fill="radioStore.isFavorite(radioStore.currentStation?.id) ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                  </svg>
                </button>
              </div>
              <!-- Estado vacío -->
              <div v-else class="flex items-center gap-2.5" :class="isDarkMode ? 'text-zinc-600' : 'text-slate-400'">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" :class="isDarkMode ? 'bg-zinc-800' : 'bg-slate-100'">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                  </svg>
                </div>
                <span class="text-sm">Selecciona una emisora</span>
              </div>
            </div>

            <!-- Controles Centrales -->
            <div class="w-[44%] flex flex-col items-center justify-center gap-2">
              <div class="flex items-center gap-3">
                <!-- Random -->
                <button 
                  @click="radioStore.playRandom()"
                  class="p-2 rounded-lg transition-colors duration-100"
                  :class="isDarkMode ? 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-100'"
                  title="Aleatorio"
                >
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                  </svg>
                </button>
                
                <!-- Previous -->
                <button 
                  @click="radioStore.playPrevious()"
                  class="p-2 rounded-lg transition-colors duration-100"
                  :class="isDarkMode ? 'text-zinc-400 hover:text-white hover:bg-zinc-800' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100'"
                >
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/>
                  </svg>
                </button>

                <!-- Play/Pause Principal -->
                <button 
                  @click="radioStore.togglePlay()"
                  class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-150"
                  :class="radioStore.isPlaying 
                    ? 'bg-emerald-500 hover:bg-emerald-400 text-white' 
                    : (isDarkMode ? 'bg-white hover:bg-zinc-200 text-black' : 'bg-slate-900 hover:bg-slate-800 text-white')"
                >
                  <svg v-if="!radioStore.isPlaying" class="h-5 w-5 ml-0.5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5v14l11-7z" />
                  </svg>
                  <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                  </svg>
                </button>

                <!-- Next -->
                <button 
                  @click="radioStore.playNext()"
                  class="p-2 rounded-lg transition-colors duration-100"
                  :class="isDarkMode ? 'text-zinc-400 hover:text-white hover:bg-zinc-800' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100'"
                >
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/>
                  </svg>
                </button>

                <!-- Repeat -->
                <button 
                  class="p-2 rounded-lg transition-colors duration-100"
                  :class="isDarkMode ? 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-100'"
                >
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                </button>
              </div>
              
              <!-- Indicador de progreso -->
              <div class="w-full max-w-sm flex items-center gap-2">
                <span 
                  class="text-[9px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded"
                  :class="radioStore.isPlaying 
                    ? 'bg-red-500/15 text-red-500' 
                    : (isDarkMode ? 'bg-zinc-800 text-zinc-500' : 'bg-slate-100 text-slate-400')"
                >
                  {{ radioStore.isPlaying ? 'LIVE' : 'OFF' }}
                </span>
                <div class="flex-1 h-1 rounded-full overflow-hidden" :class="isDarkMode ? 'bg-zinc-800' : 'bg-slate-200'">
                  <div 
                    v-if="radioStore.isPlaying"
                    class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 animate-progress-wave"
                    style="width: 200%;"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Volumen -->
            <div class="w-[28%] flex items-center justify-end gap-2">
              <button 
                @click="radioStore.toggleMute()" 
                class="p-2 rounded-lg transition-colors duration-100"
                :class="isDarkMode ? 'text-zinc-400 hover:text-white hover:bg-zinc-800' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100'"
              >
                <svg v-if="!radioStore.isMuted && radioStore.volume > 0" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                </svg>
                <svg v-else class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                </svg>
              </button>
              <div class="w-24 h-1 rounded-full relative group cursor-pointer" :class="isDarkMode ? 'bg-zinc-800' : 'bg-slate-200'">
                <input 
                  type="range" 
                  v-model="radioStore.volume" 
                  @input="radioStore.setVolume($event.target.value)"
                  min="0" 
                  max="100" 
                  class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                />
                <div 
                  class="h-full rounded-full transition-all"
                  :class="isDarkMode ? 'bg-zinc-400' : 'bg-slate-500'"
                  :style="{ width: radioStore.volume + '%' }"
                ></div>
              </div>
              <span class="text-[10px] w-7 text-right font-mono" :class="isDarkMode ? 'text-zinc-500' : 'text-slate-400'">{{ radioStore.volume }}%</span>
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

// Saludo dinámico según la hora (sin emojis)
const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour >= 5 && hour < 12) return 'Buenos días'
  if (hour >= 12 && hour < 19) return 'Buenas tardes'
  return 'Buenas noches'
})

// Logos de emisoras colombianas - CDN de myTuner (verificado y estable)
const knownLogos = {
  // Principales Cadenas Nacionales
  'olimpica': 'https://static.mytuner.mobi/media/tvos_radios/919/olimpica-stereo-medellin-1049-fm.1f60f975.png',
  'olímpica': 'https://static.mytuner.mobi/media/tvos_radios/919/olimpica-stereo-medellin-1049-fm.1f60f975.png',
  'la mega': 'https://static.mytuner.mobi/media/tvos_radios/660/la-mega-medellin.36fe2c68.png',
  'mega': 'https://static.mytuner.mobi/media/tvos_radios/660/la-mega-medellin.36fe2c68.png',
  'tropicana': 'https://static.mytuner.mobi/media/tvos_radios/626/tropicana-medellin.28271e0c.png',
  'caracol': 'https://static.mytuner.mobi/media/tvos_radios/303/caracol-radio.18b43470.jpg',
  'la w': 'https://static.mytuner.mobi/media/tvos_radios/402/la-w-radio.61065ed8.jpg',
  'w radio': 'https://static.mytuner.mobi/media/tvos_radios/402/la-w-radio.61065ed8.jpg',
  'bésame': 'https://static.mytuner.mobi/media/tvos_radios/302/besame-fm.bc8f3bdf.jpg',
  'besame': 'https://static.mytuner.mobi/media/tvos_radios/302/besame-fm.bc8f3bdf.jpg',
  
  // Emisoras Populares  
  'estrella': 'https://static.mytuner.mobi/media/tvos_radios/825/estrella-estereo.d1ff4458.jpg',
  'el sol': 'https://static.mytuner.mobi/media/tvos_radios/045/el-sol-medellin.a4aa6e39.jpg',
  'mix': 'https://static.mytuner.mobi/media/tvos_radios/819/mix-899-fm-medellin.9117dedc.jpg',
  'la mix': 'https://static.mytuner.mobi/media/tvos_radios/819/mix-899-fm-medellin.9117dedc.jpg',
  'candela': 'https://static.mytuner.mobi/media/tvos_radios/833/candela-stereo.97aa41b3.png',
  'vibra': 'https://static.mytuner.mobi/media/tvos_radios/832/vibra-fm-1049.f9c0981a.png',
  'la fm': 'https://static.mytuner.mobi/media/tvos_radios/667/la-fm-bogota.a944bdf2.png',
  
  // Vallenato y Popular
  'radio tiempo': 'https://static.mytuner.mobi/media/tvos_radios/821/radio-tiempo-medellin.jpg',
  'vallenata': 'https://static.mytuner.mobi/media/tvos_radios/820/vallenata-stereo.jpg',
  
  // Noticias y Deportes
  'blu radio': 'https://static.mytuner.mobi/media/tvos_radios/666/blu-radio.2bf5a8c8.jpg',
  'blu': 'https://static.mytuner.mobi/media/tvos_radios/666/blu-radio.2bf5a8c8.jpg',
  'rcn': 'https://static.mytuner.mobi/media/tvos_radios/304/rcn-radio.jpg',
  'antena 2': 'https://static.mytuner.mobi/media/tvos_radios/657/antena-2.5540d5cc.jpg',
  'antena2': 'https://static.mytuner.mobi/media/tvos_radios/657/antena-2.5540d5cc.jpg',
  
  // Rock y Alternativa
  'radioacktiva': 'https://static.mytuner.mobi/media/tvos_radios/859/radioacktiva.c5cdb359.png',
  'radio acktiva': 'https://static.mytuner.mobi/media/tvos_radios/859/radioacktiva.c5cdb359.png',
  'la x': 'https://static.mytuner.mobi/media/tvos_radios/797/la-x-mas-musica.7ae2ec50.png',
  'la x más': 'https://static.mytuner.mobi/media/tvos_radios/797/la-x-mas-musica.7ae2ec50.png',
  'oxigeno': 'https://static.mytuner.mobi/media/tvos_radios/830/oxigeno.jpg',
  'oxígeno': 'https://static.mytuner.mobi/media/tvos_radios/830/oxigeno.jpg',
  'radionica': 'https://static.mytuner.mobi/media/tvos_radios/935/rtvc-radionica.03a04ab8.jpg',
  'radiónica': 'https://static.mytuner.mobi/media/tvos_radios/935/rtvc-radionica.03a04ab8.jpg',
  'rtvc radionica': 'https://static.mytuner.mobi/media/tvos_radios/935/rtvc-radionica.03a04ab8.jpg',
  
  // Los 40 (Prisa Radio)
  'los 40': 'https://static.mytuner.mobi/media/tvos_radios/845/los-40-principales-colombia.b3eabffc.jpg',
  'los40': 'https://static.mytuner.mobi/media/tvos_radios/845/los-40-principales-colombia.b3eabffc.jpg',
  'los 40 principales': 'https://static.mytuner.mobi/media/tvos_radios/845/los-40-principales-colombia.b3eabffc.jpg',
  
  // Urban y Reggaeton
  'la kalle': 'https://static.mytuner.mobi/media/tvos_radios/860/la-kalle-969-fm.c8a228d5.png',
  'kalle': 'https://static.mytuner.mobi/media/tvos_radios/860/la-kalle-969-fm.c8a228d5.png',
  
  // Religiosas
  'radio maria': 'https://static.mytuner.mobi/media/tvos_radios/098/radio-maria-colombia.caed66ee.png',
  'radio maría': 'https://static.mytuner.mobi/media/tvos_radios/098/radio-maria-colombia.caed66ee.png',
  
  // Otras populares
  'radio uno': 'https://static.mytuner.mobi/media/tvos_radios/822/radio-uno-medellin.jpg',
  'latina stereo': 'https://static.mytuner.mobi/media/tvos_radios/824/latina-stereo.jpg',
  'radio bolivariana': 'https://static.mytuner.mobi/media/tvos_radios/826/radio-bolivariana.jpg',
}

// Función para obtener logo de la emisora - prioriza el logo del store
const getStationLogo = (station) => {
  if (!station) return null
  
  // Primero buscar en logos conocidos por nombre
  const nameLower = station.name.toLowerCase()
  for (const [key, url] of Object.entries(knownLogos)) {
    if (nameLower.includes(key)) {
      return url
    }
  }
  
  // El store mapea favicon de la API a logo
  if (station.logo && station.logo.startsWith('http') && !station.logo.includes('placeholder')) {
    return station.logo
  }
  if (station.favicon && station.favicon.startsWith('http')) {
    return station.favicon
  }
  
  // Si no hay logo válido, retornar null para usar fallback de iniciales
  return null
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
  
  // 🎯 IMPORTANTE: Cargar favoritos ANTES de fetchHomeData
  radioStore.loadFavorites()
  radioStore.fetchHomeData()
  
  // También verificar periódicamente el tema (por si se cambia en la misma pestaña)
  themeInterval = setInterval(syncThemeWithPOS, 500)
})

onUnmounted(() => {
  if (themeInterval) clearInterval(themeInterval)
  window.removeEventListener('storage', handleStorageChange)
})

// Sub-component: StationTile - Minimalista estilo Linear/Vercel con Skeleton
const StationTile = defineComponent({
  props: ['station', 'isDarkMode'],
  setup(props) {
    const store = useRadioStore()
    const imageLoaded = ref(false)
    const imageError = ref(false)
    
    // Usar logos conocidos del CDN myTuner, sino API, sino iniciales
    const getCardLogo = (station) => {
      // Primero buscar en logos conocidos por nombre
      const nameLower = station.name.toLowerCase()
      for (const [key, url] of Object.entries(knownLogos)) {
        if (nameLower.includes(key)) {
          return url
        }
      }
      
      // Fallback a logo de la API
      if (station.logo && 
          !station.logo.includes('placeholder') && 
          station.logo.startsWith('http')) {
        return station.logo
      }
      if (station.favicon && station.favicon.startsWith('http')) {
        return station.favicon
      }
      return null
    }

    // Colores para fallback - más variedad y profesionales
    const getGradientColor = (name) => {
      const colors = [
        'from-emerald-500 to-teal-600',
        'from-blue-500 to-indigo-600', 
        'from-violet-500 to-purple-600',
        'from-amber-500 to-orange-600',
        'from-rose-500 to-pink-600',
        'from-cyan-500 to-sky-600',
        'from-fuchsia-500 to-pink-600',
        'from-lime-500 to-green-600',
        'from-red-500 to-rose-600',
        'from-indigo-500 to-blue-600',
        'from-teal-500 to-cyan-600',
        'from-orange-500 to-amber-600',
      ]
      // Usar hash del nombre para mejor distribución
      const hash = name.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0)
      return colors[hash % colors.length]
    }
    
    const getInitials = (name) => {
      return name
        .split(' ')
        .filter(word => word.length > 0)
        .slice(0, 2)
        .map(word => word[0].toUpperCase())
        .join('')
    }

    const handleImageLoad = () => {
      imageLoaded.value = true
    }

    const handleImageError = () => {
      imageError.value = true
      imageLoaded.value = true
    }

    const isCurrentPlaying = computed(() => 
      store.currentStation?.id === props.station.id && store.isPlaying
    )

    return () => h('div', {
      class: [
        'group cursor-pointer',
      ],
      onClick: () => store.playStation(props.station)
    }, [
      // Image Container - Tile cuadrado minimalista
      h('div', { 
        class: [
          'relative aspect-square rounded-xl overflow-hidden mb-2.5 border transition-all duration-100',
          props.isDarkMode 
            ? 'bg-zinc-800 border-zinc-700/50 group-hover:border-zinc-600' 
            : 'bg-slate-50 border-slate-100 group-hover:border-slate-200 group-hover:shadow-md'
        ] 
      }, [
        // Skeleton Loader
        !imageLoaded.value && !imageError.value && getCardLogo(props.station) 
          ? h('div', { 
              class: [
                'absolute inset-0 animate-pulse',
                props.isDarkMode ? 'bg-zinc-700' : 'bg-slate-200'
              ] 
            })
          : null,
        
        // Image
        getCardLogo(props.station) && !imageError.value
          ? h('img', { 
              src: getCardLogo(props.station), 
              class: [
                'w-full h-full object-contain p-4 transition-opacity duration-150',
                imageLoaded.value ? 'opacity-100' : 'opacity-0'
              ],
              onLoad: handleImageLoad,
              onError: handleImageError
            })
          : h('div', { 
              class: ['w-full h-full flex items-center justify-center bg-gradient-to-br', getGradientColor(props.station.name)] 
            }, [
              h('span', { 
                class: 'text-white font-bold text-xl'
              }, getInitials(props.station.name))
            ]),
        
        // Hover overlay con play
        h('div', { 
          class: [
            'absolute inset-0 flex items-center justify-center transition-opacity duration-100',
            'opacity-0 group-hover:opacity-100',
            props.isDarkMode ? 'bg-black/40' : 'bg-black/30'
          ] 
        }, [
          h('button', { 
            class: [
              'w-10 h-10 rounded-full flex items-center justify-center transition-transform duration-100 group-hover:scale-100 scale-90',
              isCurrentPlaying.value
                ? 'bg-emerald-500 text-white'
                : 'bg-white text-slate-900'
            ]
          }, [
            isCurrentPlaying.value
              ? h('svg', { class: 'h-4 w-4', viewBox: '0 0 24 24', fill: 'currentColor' }, [ 
                  h('path', { d: 'M6 19h4V5H6v14zm8-14v14h4V5h-4z' }) 
                ])
              : h('svg', { class: 'h-4 w-4 ml-0.5', viewBox: '0 0 24 24', fill: 'currentColor' }, [ 
                  h('path', { d: 'M8 5v14l11-7z' }) 
                ])
          ])
        ]),
        
        // Botón favorito
        h('button', { 
          class: [
            'absolute top-2 right-2 z-10 w-7 h-7 rounded-lg flex items-center justify-center transition-all duration-100',
            store.isFavorite(props.station.id) 
              ? 'bg-rose-500 text-white' 
              : [
                  'opacity-0 group-hover:opacity-100',
                  props.isDarkMode ? 'bg-zinc-800/80 text-zinc-400 hover:text-white' : 'bg-white/80 text-slate-400 hover:text-slate-600'
                ]
          ],
          onClick: (e) => {
            e.stopPropagation()
            store.toggleFavorite(props.station)
          }
        }, [
          h('svg', { 
            class: 'h-3.5 w-3.5', 
            fill: store.isFavorite(props.station.id) ? 'currentColor' : 'none',
            viewBox: '0 0 24 24', 
            stroke: 'currentColor',
            'stroke-width': '2'
          }, [
            h('path', { 
              'stroke-linecap': 'round', 
              'stroke-linejoin': 'round',
              d: 'M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z'
            })
          ])
        ]),
        
        // Live indicator
        isCurrentPlaying.value
          ? h('div', { 
              class: 'absolute top-2 left-2 px-1.5 py-0.5 bg-emerald-500 rounded text-[8px] font-semibold text-white uppercase tracking-wide flex items-center gap-1' 
            }, [
              h('span', { class: 'w-1 h-1 bg-white rounded-full animate-pulse' }),
              'LIVE'
            ])
          : null
      ]),
      
      // Nombre - Pequeño y elegante
      h('h3', { 
        class: [
          'font-medium text-sm truncate', 
          isCurrentPlaying.value 
            ? 'text-emerald-500' 
            : (props.isDarkMode ? 'text-zinc-300' : 'text-slate-700')
        ] 
      }, props.station.name)
    ])
  }
})
</script>

<style scoped>
/* Transiciones optimizadas para mejor rendimiento */
.fade-enter-active {
  transition: opacity 0.15s ease-out;
}
.fade-leave-active {
  transition: opacity 0.12s ease-in;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Custom Scrollbar - Minimalista */
.custom-scrollbar::-webkit-scrollbar { 
  width: 6px; 
}
.custom-scrollbar::-webkit-scrollbar-track { 
  background: transparent; 
}
.custom-scrollbar::-webkit-scrollbar-thumb { 
  background: #3f3f46; 
  border-radius: 10px; 
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover { 
  background: #52525b; 
}

/* Optimizar animaciones con GPU acceleration */
.group {
  will-change: auto;
}

.group:hover {
  will-change: transform, opacity;
}

/* Animaciones de barras de música - Optimizadas con GPU */
@keyframes music-bar {
  0%, 100% { transform: scaleY(0.3); }
  50% { transform: scaleY(1); }
}
.animate-music-bar-1,
.animate-music-bar-2,
.animate-music-bar-3 {
  will-change: transform;
  transform-origin: center;
  height: 12px;
}
.animate-music-bar-1 { animation: music-bar 0.5s ease-in-out infinite; }
.animate-music-bar-2 { animation: music-bar 0.5s ease-in-out infinite 0.1s; }
.animate-music-bar-3 { animation: music-bar 0.5s ease-in-out infinite 0.2s; }

/* Animación de barra de progreso LIVE */
@keyframes progress-wave {
  0% { transform: translateX(-50%); }
  100% { transform: translateX(0%); }
}
.animate-progress-wave {
  animation: progress-wave 1.5s linear infinite;
  will-change: transform;
}

/* Font Inter */
.font-sans {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Optimizar rendering del modal */
.radio-modal-container {
  transform: translateZ(0);
  backface-visibility: hidden;
}
</style>
