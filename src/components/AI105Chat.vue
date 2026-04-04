<template>
  <!-- Panel Lateral de Chat IA - Diseño GEMINI Style -->
  <div>
    <transition name="slide-right">
      <div 
        v-if="localChatOpen"
        class="fixed right-0 w-full sm:w-[400px] bg-white dark:bg-[#131314] flex flex-col z-[45] shadow-[-8px_0_30px_-5px_rgba(0,0,0,0.15)] dark:shadow-[-8px_0_30px_-5px_rgba(0,0,0,0.5)] border-l border-gray-200/80 dark:border-zinc-800"
        :style="{ top: dynamicHeaderHeight + 'px', height: `calc(100% - ${dynamicHeaderHeight}px)` }"
      >
        <!-- ═══════════════════════════════════════════════════════════════
             🔴 LIVE CALL OVERLAY - Vista de llamada en vivo estilo Futurista Light Mode
             Diseño: Google Gemini / Apple Intelligence / Siri
        ═══════════════════════════════════════════════════════════════ -->
        <transition name="fade">
          <div 
            v-if="liveCall.isActive.value"
            class="absolute inset-0 z-50 flex flex-col overflow-hidden"
          >
            <!-- Fondo Light/Dark Mode con Ambient Glow futurista -->
            <div class="absolute inset-0 bg-gradient-to-b from-[#F9FAFB] via-white to-[#F3F4F6] dark:from-[#0a0a0c] dark:via-[#131316] dark:to-[#1a1a1f]">
              <!-- Ambient Glow - Luz difusa cyan/violeta tipo Gemini -->
              <div class="absolute inset-0 overflow-hidden">
                <!-- Glow superior izquierdo (Cyan suave) -->
                <div 
                  class="absolute -top-20 -left-20 w-[400px] h-[400px] rounded-full opacity-30 dark:opacity-40"
                  :class="{ 'animate-ambient-pulse': liveCall.isSpeaking.value }"
                  style="background: radial-gradient(circle, rgba(34,211,238,0.4) 0%, rgba(34,211,238,0.1) 40%, transparent 70%);"
                ></div>
                <!-- Glow superior derecho (Violeta pálido) -->
                <div 
                  class="absolute -top-10 -right-20 w-[350px] h-[350px] rounded-full opacity-25 dark:opacity-35"
                  :class="{ 'animate-ambient-pulse-delayed': liveCall.isSpeaking.value }"
                  style="background: radial-gradient(circle, rgba(167,139,250,0.4) 0%, rgba(167,139,250,0.1) 40%, transparent 70%);"
                ></div>
                <!-- Glow inferior central (Rosa muy suave) -->
                <div 
                  class="absolute -bottom-10 left-1/2 -translate-x-1/2 w-[300px] h-[300px] rounded-full opacity-20 dark:opacity-30"
                  style="background: radial-gradient(circle, rgba(251,146,180,0.3) 0%, transparent 60%);"
                ></div>
              </div>
            </div>
            
            <!-- ═══════════════════════════════════════════════════════════
                 SELECTOR DE VOZ (Primera vez) - Estilo Light Mode
            ═══════════════════════════════════════════════════════════ -->
            <div 
              v-if="liveCall.showVoiceSelector.value"
              class="relative z-10 flex-1 flex flex-col px-6 py-6"
            >
              <!-- Header -->
              <div class="text-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-1 tracking-tight">Elige tu asistente</h2>
                <p class="text-gray-400 text-xs">Toca el círculo para escuchar la voz</p>
              </div>
              
              <!-- Círculo central con ondas -->
              <div class="flex-1 flex flex-col items-center justify-center relative">
                <!-- Flechas de navegación (Glassmorphism) -->
                <button
                  @click="liveCall.prevVoice()"
                  class="absolute left-2 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/70 backdrop-blur-md hover:bg-white/90 flex items-center justify-center transition-all shadow-lg shadow-gray-200/50 border border-gray-100"
                  :class="{ 'opacity-30 pointer-events-none': liveCall.currentVoiceIndex.value === 0 }"
                >
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                  </svg>
                </button>
                
                <!-- Círculo principal con ondas - Diseño Orbe Futurista -->
                <div class="relative">
                  <!-- Ondas de sonido (anillos que se expanden) - Light Mode -->
                  <div 
                    v-if="liveCall.isPlayingPreview.value"
                    class="absolute inset-0 flex items-center justify-center"
                  >
                    <div class="absolute w-32 h-32 rounded-full border-2 border-cyan-300/40 animate-ping" style="animation-duration: 1.5s;"></div>
                    <div class="absolute w-40 h-40 rounded-full border border-violet-300/30 animate-ping" style="animation-duration: 2s;"></div>
                    <div class="absolute w-48 h-48 rounded-full border border-pink-200/20 animate-ping" style="animation-duration: 2.5s;"></div>
                  </div>
                  
                  <!-- Orbe con gradiente Gemini Colors -->
                  <button
                    @click="liveCall.playVoicePreview()"
                    :disabled="liveCall.isPlayingPreview.value"
                    class="relative w-28 h-28 rounded-full flex items-center justify-center transition-all duration-500 hover:scale-105 disabled:hover:scale-100"
                    :class="[`bg-gradient-to-br ${liveCall.voices.value[liveCall.currentVoiceIndex.value]?.color || 'from-gray-400 to-gray-600'}`]"
                    :style="{ 
                      boxShadow: liveCall.isPlayingPreview.value 
                        ? '0 0 60px rgba(34,211,238,0.4), 0 0 100px rgba(167,139,250,0.3)' 
                        : '0 20px 50px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.5) inset'
                    }"
                  >
                    <!-- Icono interior - Barras de audio cuando reproduce -->
                    <div 
                      v-if="liveCall.isPlayingPreview.value"
                      class="flex items-end gap-0.5 h-6"
                    >
                      <div class="w-1 bg-white rounded-full animate-bounce" style="height: 40%; animation-delay: 0ms;"></div>
                      <div class="w-1 bg-white rounded-full animate-bounce" style="height: 80%; animation-delay: 100ms;"></div>
                      <div class="w-1 bg-white rounded-full animate-bounce" style="height: 50%; animation-delay: 200ms;"></div>
                      <div class="w-1 bg-white rounded-full animate-bounce" style="height: 90%; animation-delay: 300ms;"></div>
                      <div class="w-1 bg-white rounded-full animate-bounce" style="height: 60%; animation-delay: 400ms;"></div>
                    </div>
                    <svg 
                      v-else
                      class="w-10 h-10 text-white/90" 
                      fill="none" 
                      stroke="currentColor" 
                      viewBox="0 0 24 24" 
                      stroke-width="1.5"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/>
                    </svg>
                  </button>
                </div>
                
                <!-- Nombre y descripción debajo del círculo - Light Mode -->
                <div class="text-center mt-6">
                  <h3 class="text-xl font-semibold text-gray-800">
                    {{ liveCall.voices.value[liveCall.currentVoiceIndex.value]?.name }}
                  </h3>
                  <p class="text-gray-500 text-sm mt-1">
                    {{ liveCall.voices.value[liveCall.currentVoiceIndex.value]?.description }}
                  </p>
                  <!-- Género - Pill elegante -->
                  <span 
                    class="inline-block mt-2 px-3 py-1 rounded-full text-[10px] font-medium uppercase tracking-wider backdrop-blur-sm"
                    :class="liveCall.voices.value[liveCall.currentVoiceIndex.value]?.gender === 'female' 
                      ? 'bg-pink-100 text-pink-600 border border-pink-200' 
                      : 'bg-blue-100 text-blue-600 border border-blue-200'"
                  >
                    {{ liveCall.voices.value[liveCall.currentVoiceIndex.value]?.gender === 'female' ? 'Femenina' : 'Masculina' }}
                  </span>
                </div>
                
                <!-- Flecha siguiente (Glassmorphism) -->
                <button
                  @click="liveCall.nextVoice()"
                  class="absolute right-2 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/70 backdrop-blur-md hover:bg-white/90 flex items-center justify-center transition-all shadow-lg shadow-gray-200/50 border border-gray-100"
                  :class="{ 'opacity-30 pointer-events-none': liveCall.currentVoiceIndex.value === liveCall.voices.value.length - 1 }"
                >
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                  </svg>
                </button>
              </div>
              
              <!-- Indicadores (dots) - Light Mode -->
              <div class="flex justify-center gap-2 mt-4">
                <button
                  v-for="(voice, index) in liveCall.voices.value"
                  :key="voice.id"
                  @click="liveCall.goToVoice(index)"
                  class="w-2 h-2 rounded-full transition-all duration-300"
                  :class="[
                    index === liveCall.currentVoiceIndex.value 
                      ? 'bg-gray-800 w-5' 
                      : 'bg-gray-300 hover:bg-gray-400'
                  ]"
                ></button>
              </div>
              
              <!-- Botón continuar - Glassmorphism elegante -->
              <div class="mt-6 space-y-2">
                <button
                  @click="liveCall.confirmVoiceSelection()"
                  :disabled="liveCall.isPlayingPreview.value"
                  class="w-full py-3.5 rounded-2xl font-semibold transition-all disabled:opacity-50 text-white shadow-xl"
                  :class="[`bg-gradient-to-r ${liveCall.voices.value[liveCall.currentVoiceIndex.value]?.color || 'from-gray-500 to-gray-700'}`]"
                  :style="{ boxShadow: '0 10px 30px rgba(0,0,0,0.15)' }"
                >
                  Continuar con {{ liveCall.voices.value[liveCall.currentVoiceIndex.value]?.name }}
                </button>
                <button
                  @click="liveCall.cancelVoiceSelection()"
                  class="w-full py-2 text-gray-400 hover:text-gray-600 text-sm transition-all"
                >
                  Cancelar
                </button>
              </div>
            </div>
            
            <!-- ═══════════════════════════════════════════════════════════
                 LLAMADA EN CURSO - Diseño Futurista Light Mode
            ═══════════════════════════════════════════════════════════ -->
            <template v-else>
              <!-- Header de la llamada - Minimalista -->
              <div class="relative z-10 flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-2">
                  <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-lg shadow-emerald-500/50"></div>
                  <span class="text-gray-600 dark:text-gray-300 text-sm font-medium tracking-wide">Live</span>
                </div>
                <span class="text-gray-400 dark:text-gray-500 text-sm font-mono tabular-nums">{{ liveCall.formattedDuration.value }}</span>
              </div>
              
              <!-- Contenido central - Visualizador Abstracto -->
              <div class="relative z-10 flex-1 flex flex-col items-center justify-center px-6">
                
                <!-- ════════════════════════════════════════════════════════
                     VISUALIZADOR DE VOZ ABSTRACTO - El Centro de Atención
                ════════════════════════════════════════════════════════ -->
                <div class="mb-10">
                  
                  <!-- Estado: Conectando -->
                  <div 
                    v-if="liveCall.isConnecting.value"
                    class="flex flex-col items-center"
                  >
                    <!-- Orbe pulsante de conexión -->
                    <div class="relative w-24 h-24 flex items-center justify-center">
                      <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-violet-400 rounded-full animate-ping opacity-20"></div>
                      <div class="absolute inset-2 bg-gradient-to-r from-cyan-300 to-violet-300 rounded-full animate-pulse opacity-30"></div>
                      <div class="w-16 h-16 bg-gradient-to-br from-cyan-400 via-violet-400 to-pink-400 rounded-full animate-spin-slow"
                           style="animation: spin 3s linear infinite;"></div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 text-lg mt-6 font-light tracking-wide animate-fade-in">Conectando...</p>
                  </div>
                  
                  <!-- Estado: IA Hablando - Onda de Sonido Fluida tipo Siri/Gemini -->
                  <div 
                    v-else-if="liveCall.isSpeaking.value"
                    class="flex flex-col items-center"
                  >
                    <!-- Visualizador Geométrico Animado -->
                    <div class="relative flex items-center justify-center">
                      <div class="voice-loader">
                        <svg width="100" height="100" viewBox="0 0 100 100">
                          <defs>
                            <mask id="clipping">
                              <polygon points="0,0 100,0 100,100 0,100" fill="black"></polygon>
                              <polygon points="25,25 75,25 50,75" fill="white"></polygon>
                              <polygon points="50,25 75,75 25,75" fill="white"></polygon>
                              <polygon points="35,35 65,35 50,65" fill="white"></polygon>
                              <polygon points="35,35 65,35 50,65" fill="white"></polygon>
                              <polygon points="35,35 65,35 50,65" fill="white"></polygon>
                              <polygon points="35,35 65,35 50,65" fill="white"></polygon>
                            </mask>
                          </defs>
                        </svg>
                        <div class="voice-loader-box"></div>
                      </div>
                    </div>
                    
                    <!-- Transcripción flotante -->
                    <p class="text-[#1F2937] dark:text-white text-xl font-light mt-6 tracking-wide text-center animate-fade-in max-w-[280px]">
                      105 IA está hablando...
                    </p>
                  </div>
                  
                  <!-- Estado: Escuchando - Orbe Pulsante Suave -->
                  <div 
                    v-else
                    class="flex flex-col items-center"
                  >
                    <!-- Orbe de escucha - Azul metálico suave -->
                    <div class="relative">
                      <!-- Anillos de pulso suave -->
                      <div class="absolute inset-0 flex items-center justify-center">
                        <div class="absolute w-28 h-28 rounded-full border-2 border-blue-200 animate-ping opacity-30" style="animation-duration: 2s;"></div>
                        <div class="absolute w-36 h-36 rounded-full border border-cyan-200 animate-ping opacity-20" style="animation-duration: 2.5s;"></div>
                      </div>
                      
                      <!-- Orbe central - Gradiente metálico -->
                      <div class="relative w-20 h-20 rounded-full flex items-center justify-center"
                           style="background: linear-gradient(135deg, #60A5FA 0%, #818CF8 50%, #A78BFA 100%);
                                  box-shadow: 0 10px 40px rgba(96,165,250,0.4), 0 0 0 1px rgba(255,255,255,0.3) inset;">
                        <!-- Icono de micrófono minimalista -->
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"/>
                        </svg>
                      </div>
                    </div>
                    
                    <!-- Texto flotante - Tipografía moderna -->
                    <p class="text-[#1F2937] dark:text-white text-xl font-light mt-8 tracking-wide animate-fade-in">
                      Escuchándote...
                    </p>
                  </div>
                </div>
                
                <!-- Voz actual - Pill elegante glassmorphism -->
                <div class="flex items-center gap-2 px-4 py-2 bg-white/60 dark:bg-zinc-800/60 backdrop-blur-md rounded-full border border-gray-200 dark:border-zinc-700 shadow-lg shadow-gray-200/30 dark:shadow-black/30">
                  <span class="text-lg">{{ liveCall.currentVoice.value?.emoji }}</span>
                  <span class="text-gray-600 dark:text-gray-300 text-sm font-medium">{{ liveCall.currentVoice.value?.name }}</span>
                </div>
              </div>
              
              <!-- ════════════════════════════════════════════════════════
                   CONTROLES FLOTANTES - Glassmorphism
              ════════════════════════════════════════════════════════ -->
              <div class="relative z-10 px-6 pb-8 pt-4">
                <!-- Barra flotante de controles -->
                <div class="flex items-center justify-center gap-4 p-3 bg-white/70 dark:bg-zinc-900/70 backdrop-blur-xl rounded-3xl border border-gray-200/80 dark:border-zinc-700/80 shadow-xl shadow-gray-300/30 dark:shadow-black/40 max-w-[280px] mx-auto">
                  
                  <!-- Botón de micrófono - Glassmorphism con brillo activo -->
                  <button
                    class="w-14 h-14 rounded-full flex items-center justify-center transition-all duration-300"
                    :class="liveCall.isListening.value 
                      ? 'bg-gradient-to-br from-blue-400 to-violet-500 text-white shadow-lg shadow-blue-400/40' 
                      : 'bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-600 dark:text-gray-300'"
                    :style="liveCall.isListening.value ? { boxShadow: '0 0 25px rgba(96,165,250,0.5), 0 0 50px rgba(167,139,250,0.3)' } : {}"
                  >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"/>
                    </svg>
                  </button>
                  
                  <!-- Separador sutil -->
                  <div class="w-px h-8 bg-gray-200 dark:bg-zinc-700"></div>
                  
                  <!-- Botón de cerrar - X elegante minimalista -->
                  <button
                    @click="endLiveCall"
                    class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300 bg-gray-100 dark:bg-zinc-800 hover:bg-rose-50 dark:hover:bg-rose-900/30 text-gray-400 dark:text-gray-500 hover:text-rose-500 dark:hover:text-rose-400 group"
                  >
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
                
                <!-- Texto de ayuda - Tipografía elegante -->
                <p class="text-center text-gray-400 dark:text-gray-500 text-xs mt-4 tracking-wide font-light">
                  Habla naturalmente • 105 IA te escucha
                </p>
              </div>
            </template>
          </div>
        </transition>
        
        <!-- ═══════════════════════════════════════════════════════════════
             HEADER - Estilo Gemini Minimalista
        ═══════════════════════════════════════════════════════════════ -->
        <div class="flex items-center justify-between h-14 px-4 flex-shrink-0">
          <!-- Izquierda: Menu hamburguesa -->
          <button 
            @click="activeTab = activeTab === 'chat' ? 'history' : 'chat'"
            class="w-10 h-10 flex items-center justify-center text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-full transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
          </button>
          
          <!-- Centro: Título -->
          <h1 class="text-lg font-normal text-gray-900 dark:text-white tracking-tight">105 IA</h1>
          
          <!-- Derecha: Avatar usuario + Nueva conversación -->
          <div class="flex items-center gap-1">
            <button 
              v-if="messages.length > 0"
              @click="startNewConversation"
              class="w-10 h-10 flex items-center justify-center text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-full transition-all"
              title="Nueva conversación"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
              </svg>
            </button>
            <button 
              @click="closeChat"
              class="w-10 h-10 flex items-center justify-center text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-full transition-all"
              title="Cerrar chat"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════
             🎵 Mini Controles de Radio (flotante)
        ═══════════════════════════════════════════════════════════════ -->
        <transition name="slide-down">
          <div 
            v-if="showRadioControls"
            class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-zinc-900 mx-4 rounded-2xl"
          >
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" :class="{ 'animate-pulse': isRadioPlaying }" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ currentStationName }}</p>
                <p class="text-xs text-gray-500 dark:text-zinc-500">{{ isRadioPlaying ? 'Reproduciendo' : 'Pausado' }}</p>
              </div>
            </div>
            
            <div class="flex items-center gap-1">
              <button @click="radioControlPrevious" class="w-9 h-9 flex items-center justify-center text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white rounded-full hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>
              </button>
              <button @click="radioControlPlayPause" class="w-11 h-11 flex items-center justify-center bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full transition-all hover:scale-105">
                <svg v-if="isRadioPlaying" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                <svg v-else class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
              </button>
              <button @click="radioControlNext" class="w-9 h-9 flex items-center justify-center text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white rounded-full hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg>
              </button>
              <button @click="closeRadioControls" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300 ml-1 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
          </div>
        </transition>

        <!-- ═══════════════════════════════════════════════════════════════
             CONTENIDO PRINCIPAL - Chat Tab
        ═══════════════════════════════════════════════════════════════ -->
        <div v-show="activeTab === 'chat'" class="flex-1 flex flex-col overflow-hidden">
          <div ref="messagesContainer" class="flex-1 overflow-y-auto">
            
            <!-- ══════════════════════════════════════════════════════════
                 🔒 BLOQUEO POR PLAN - Free Trial y Básico sin IA
            ══════════════════════════════════════════════════════════ -->
            <div v-if="!hasAIAccess" class="h-full flex flex-col items-center justify-center px-6 text-center">
              <div class="w-20 h-20 bg-gradient-to-br from-purple-500/20 to-blue-500/20 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                Desbloquea 105 IA
              </h3>
              <p class="text-gray-500 dark:text-zinc-400 mb-6 max-w-[280px]">
                El asistente de inteligencia artificial está disponible en planes Premium y Enterprise
              </p>
              <button 
                @click="$emit('navigate', 'upgrade')"
                class="px-6 py-3 bg-gradient-to-r from-purple-500 to-blue-500 text-white font-medium rounded-full hover:shadow-lg hover:shadow-purple-500/25 transition-all"
              >
                ✨ Actualizar Plan
              </button>
              <p class="text-xs text-gray-400 dark:text-zinc-500 mt-4">
                Plan actual: {{ tenantPlan.replace('_', ' ').toUpperCase() }}
              </p>
            </div>
            
            <!-- ══════════════════════════════════════════════════════════
                 ESTADO VACÍO - Bienvenida Estilo Gemini (solo si tiene acceso)
            ══════════════════════════════════════════════════════════ -->
            <div v-else-if="messages.length === 0" class="h-full flex flex-col px-6 pt-16">
              <!-- Saludo Grande -->
              <div class="mb-8">
                <h2 class="text-[28px] leading-tight font-normal text-gray-900 dark:text-white mb-1">
                  Hola, {{ userName }}
                </h2>
                <p class="text-[28px] leading-tight font-normal text-gray-400 dark:text-zinc-500">
                  ¿Por dónde empezamos?
                </p>
              </div>
              
              <!-- Chips de Sugerencias - Estilo Gemini -->
              <div class="space-y-3">
                <button
                  v-for="(chip, index) in geminiChips"
                  :key="index"
                  @click="sendQuickMessage(chip)"
                  @mouseenter="hoverSuggestion = chip.text"
                  @mouseleave="hoverSuggestion = ''"
                  class="flex items-center gap-3 px-5 py-4 bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-2xl hover:bg-gray-100 dark:hover:bg-[#282a2c] transition-all cursor-pointer group w-fit max-w-full"
                >
                  <span class="text-xl flex-shrink-0">{{ chip.icon }}</span>
                  <span class="text-[15px] text-gray-700 dark:text-zinc-300 group-hover:text-gray-900 dark:group-hover:text-white">{{ chip.text }}</span>
                </button>
              </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════
                 MENSAJES DEL CHAT
            ══════════════════════════════════════════════════════════ -->
            <div v-else class="py-4 px-4 space-y-5">
              <div 
                v-for="(message, index) in messages" 
                :key="index"
                class="animate-fade-in"
              >
                <!-- Mensaje del usuario -->
                <div v-if="message.type === 'user'" class="flex justify-end">
                  <div class="max-w-[85%] bg-[#e3f2fd] dark:bg-blue-900/40 text-gray-900 dark:text-blue-100 px-4 py-3 rounded-[20px] rounded-br-md">
                    <p class="text-[15px] whitespace-pre-line leading-relaxed">{{ message.text }}</p>
                  </div>
                </div>

                <!-- Mensaje de la IA -->
                <div v-else class="flex gap-3">
                  <!-- Avatar Gemini -->
                  <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 via-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 0L14.59 8.41L23 11L14.59 13.59L12 22L9.41 13.59L1 11L9.41 8.41L12 0Z"/>
                    </svg>
                  </div>

                  <div class="flex-1 max-w-[88%]">
                    <div
                      class="text-[15px] leading-relaxed"
                      :class="[
                        message.isLimit ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200/50 dark:border-amber-800/30 px-4 py-3 rounded-2xl' :
                        message.isError ? 'bg-red-50 dark:bg-red-900/20 border border-red-200/50 dark:border-red-800/30 px-4 py-3 rounded-2xl' :
                        message.isInfo ? 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200/50 dark:border-blue-800/30 px-4 py-3 rounded-2xl' :
                        message.isWarning ? 'bg-orange-50 dark:bg-orange-900/20 border border-orange-200/50 dark:border-orange-800/30 px-4 py-3 rounded-2xl' :
                        'text-gray-800 dark:text-zinc-200'
                      ]"
                    >
                      <div v-if="message.isLimit" class="space-y-3">
                        <div class="flex items-center gap-2">
                          <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                          </svg>
                          <p class="text-amber-700 dark:text-amber-400 font-medium text-sm">Límite diario alcanzado</p>
                        </div>
                        <p class="text-gray-600 dark:text-zinc-300 text-sm">Has utilizado los {{ message.limitData?.used || 5 }} mensajes disponibles en tu plan. Se renovarán mañana.</p>
                        <button @click="$emit('navigate', 'upgrade')" class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-medium rounded-xl transition-all shadow-sm">
                          Obtener mensajes ilimitados
                        </button>
                      </div>
                      <div v-else-if="message.isInfo" class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                        </svg>
                        <p class="text-blue-700 dark:text-blue-400 text-sm">{{ message.text.replace(/^ℹ️\s*/, '') }}</p>
                      </div>
                      <div v-else-if="message.isWarning" class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        <p class="text-amber-700 dark:text-amber-400 text-sm">{{ message.text.replace(/^⚠️\s*/, '') }}</p>
                      </div>
                      <p v-else class="whitespace-pre-line">{{ message.text }}</p>
                      
                      <button 
                        v-if="message.suggested_action"
                        @click="executeSuggestedAction(message.suggested_action)"
                        class="mt-3 px-4 py-2 bg-[#f8f9fa] dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-medium rounded-full flex items-center gap-2 transition-all"
                      >
                        {{ message.suggested_action.label || 'Ver detalles' }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Typing indicator - Estilo Gemini -->
              <div v-if="isTyping" class="flex gap-3 animate-fade-in">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 via-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 0L14.59 8.41L23 11L14.59 13.59L12 22L9.41 13.59L1 11L9.41 8.41L12 0Z"/>
                  </svg>
                </div>
                <div class="flex items-center gap-1 py-3">
                  <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                  <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                  <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- ══════════════════════════════════════════════════════════
               INPUT BAR - Estilo Gemini Exacto
          ══════════════════════════════════════════════════════════ -->
          <div v-if="hasAIAccess" class="flex-shrink-0 px-4 pb-5 pt-3 bg-white dark:bg-[#131314]">
            
            <!-- Input Container - Estilo Gemini -->
            <form @submit.prevent="sendMessage">
              <!-- Línea superior decorativa como Gemini -->
              <div class="bg-[#f0f4f9] dark:bg-[#1e1f20] rounded-t-[28px] h-3 mb-[-12px] mx-2"></div>
              
              <div class="bg-[#f0f4f9] dark:bg-[#1e1f20] rounded-[28px] flex flex-col px-4 py-3">
                
                <!-- Input de texto -->
                <textarea
                  ref="messageInput"
                  v-model="inputMessage"
                  @keydown.enter.exact.prevent="sendMessage"
                  @input="autoResizeTextarea"
                  @focus="hoverSuggestion = ''"
                  placeholder="Pregúntale a 105 IA"
                  rows="1"
                  class="w-full bg-transparent border-none focus:outline-none resize-none text-[15px] text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-zinc-400 min-h-[24px] max-h-[120px]"
                  style="line-height: 1.5;"
                ></textarea>

                <!-- Archivo seleccionado -->
                <div v-if="selectedFile" class="flex items-center gap-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl mt-2">
                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                  </svg>
                  <span class="text-sm text-blue-700 dark:text-blue-300 flex-1 truncate">{{ selectedFile.name }}</span>
                  <button
                    type="button"
                    @click="selectedFile = null; fileInput.value = ''"
                    class="w-5 h-5 flex items-center justify-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
                    title="Eliminar archivo"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>

                <!-- Input file oculto -->
                <input
                  ref="fileInput"
                  type="file"
                  accept=".xlsx,.xls,.csv"
                  @change="handleFileSelect"
                  class="hidden"
                />
                
                <!-- Fila de botones debajo del input -->
                <div class="flex items-center justify-between mt-3">
                  <!-- Izquierda: + y Ajustes -->
                  <div class="flex items-center gap-1">
                    <!-- Botón Adjuntar Archivo (Excel/CSV) -->
                    <button
                      type="button"
                      @click="fileInput?.click()"
                      :disabled="selectedFile !== null"
                      :title="selectedFile ? 'Ya hay un archivo seleccionado' : 'Adjuntar archivo Excel o CSV'"
                      class="w-9 h-9 flex items-center justify-center text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:bg-white/60 dark:hover:bg-zinc-700/50 rounded-full transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                      </svg>
                    </button>
                    <button
                      type="button"
                      class="w-9 h-9 flex items-center justify-center text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:bg-white/60 dark:hover:bg-zinc-700/50 rounded-full transition-all"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/>
                      </svg>
                    </button>
                  </div>
                  
                  <!-- Derecha: Mic + Enviar -->
                  <div class="flex items-center gap-1">
                    <!-- 📞 Botón Live Call (solo para Premium y Enterprise) -->
                    <button
                      v-if="hasVoiceAccess"
                      type="button"
                      @click="startLiveCall"
                      :disabled="isTyping || !canUseVoice"
                      class="relative w-10 h-10 flex items-center justify-center rounded-full transition-all"
                      :class="[
                        canUseVoice 
                          ? 'text-gray-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20'
                          : 'text-gray-300 dark:text-zinc-600 cursor-not-allowed'
                      ]"
                      :title="canUseVoice ? (voiceLimitSeconds === 0 ? 'Llamada en vivo (ilimitado)' : `Llamada en vivo (${Math.floor(voiceSecondsRemaining / 60)}:${String(voiceSecondsRemaining % 60).padStart(2, '0')} restantes)`) : 'Sin minutos de voz disponibles'"
                    >
                      <!-- Indicador de tiempo restante (muestra si hay límite y ha usado algo) -->
                      <span 
                        v-if="voiceLimitSeconds > 0 && voiceSecondsRemaining < voiceLimitSeconds"
                        class="absolute -top-1 -right-1 px-1 py-0.5 bg-blue-500 text-white text-[8px] font-bold rounded-full"
                      >
                        {{ Math.floor(voiceSecondsRemaining / 60) }}m
                      </span>
                      <!-- Icono de onda de audio / Live -->
                      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3v18M8 7v10M4 10v4M16 7v10M20 10v4"/>
                        <path stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M12 3v18M8 7v10M4 10v4M16 7v10M20 10v4"/>
                      </svg>
                    </button>

                    <!-- Enviar / Ecualizer -->
                    <button
                      type="submit"
                      :disabled="!inputMessage.trim() || isTyping"
                      :class="[
                        'w-10 h-10 flex items-center justify-center rounded-full transition-all',
                        inputMessage.trim() && !isTyping
                          ? 'bg-gray-800 dark:bg-zinc-700 text-white hover:bg-gray-900 dark:hover:bg-zinc-600'
                          : 'text-gray-500 dark:text-zinc-500 hover:bg-white/60 dark:hover:bg-zinc-700/50'
                      ]"
                    >
                      <!-- Icono de ecualizer cuando no hay texto -->
                      <svg v-if="!inputMessage.trim()" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 18h2V6H7v12zm4 4h2V2h-2v20zm4-8h2v-4h-2v4z"/>
                      </svg>
                      <!-- Flecha de enviar cuando hay texto -->
                      <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </form>

            <!-- Indicador de mensajes restantes (solo para planes con límite) -->
            <div v-if="chatMessageLimit > 0" class="flex items-center justify-center gap-2 mt-2">
              <span class="text-[11px] text-gray-400 dark:text-zinc-500">
                <template v-if="chatMessagesRemaining > 0">
                  {{ chatMessagesRemaining }} de {{ chatMessageLimit }} mensajes restantes hoy
                </template>
                <template v-else>
                  Sin mensajes disponibles • Renueva mañana
                </template>
              </span>
              <div class="flex-1 max-w-[100px] h-1.5 bg-gray-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div 
                  class="h-full transition-all duration-300 rounded-full"
                  :class="chatMessagesRemaining > 3 ? 'bg-blue-500' : chatMessagesRemaining > 0 ? 'bg-amber-500' : 'bg-red-500'"
                  :style="{ width: `${(chatMessagesRemaining / chatMessageLimit) * 100}%` }"
                ></div>
              </div>
            </div>

            <!-- Disclaimer -->
            <p class="text-center text-[11px] text-gray-400 dark:text-zinc-600 mt-3">
              105 IA puede mostrar información inexacta, así que verifica las respuestas.
            </p>
          </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════
             TAB HISTORIAL
        ══════════════════════════════════════════════════════════ -->
        <div v-show="activeTab === 'history'" class="flex-1 overflow-y-auto px-4 py-6">
          <!-- Header del historial -->
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-normal text-gray-900 dark:text-white">Historial</h2>
            <button 
              @click="activeTab = 'chat'"
              class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-full transition-all"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <div v-if="chatHistory.length === 0" class="flex flex-col items-center justify-center text-center py-16">
            <div class="w-16 h-16 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
              <svg class="w-7 h-7 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-gray-700 dark:text-zinc-300 font-medium mb-1">Sin historial</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-500 max-w-[200px]">Tus conversaciones anteriores aparecerán aquí</p>
          </div>
          
          <div v-else class="space-y-2">
            <button
              v-for="(session, index) in chatHistory"
              :key="index"
              @click="loadSession(session)"
              class="w-full text-left p-4 bg-[#f8f9fa] dark:bg-[#1e1f20] hover:bg-gray-100 dark:hover:bg-[#282a2c] rounded-2xl transition-all cursor-pointer group"
            >
              <p class="text-[15px] font-medium text-gray-700 dark:text-zinc-300 truncate group-hover:text-gray-900 dark:group-hover:text-white">{{ session.title || 'Conversación' }}</p>
              <p class="text-sm text-gray-500 dark:text-zinc-500 mt-1">{{ session.date }}</p>
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref, nextTick, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useModuleNavigation } from '@/composables/useModuleNavigation'
import { useLiveCall } from '@/composables/useLiveCall'
import { aiChatStore } from '@/store/aiChatStore'
import { useRadioStore } from '@/store/radioStore'
import { appStore } from '@/store/appStore'
import { useAIContextStore } from '@/stores/aiContext'
import api from '@/services/api'

export default {
  name: 'AI105Chat',
  props: {
    isOpen: {
      type: Boolean,
      default: undefined
    },
    headerHeight: {
      type: Number,
      default: 64
    },
    currentModule: {
      type: String,
      default: ''
    }
  },
  emits: ['close', 'navigate', 'toggle-radio'],
  setup(props, { emit }) {
    const router = useRouter()
    const { navigateToModule } = useModuleNavigation()
    const radioStore = useRadioStore()
    // appStore ya está importado directamente
    
    // 🧠 Store de contexto de pantalla para la IA
    const aiContextStore = useAIContextStore()
    
    // 📞 Composable para llamada en vivo con Gemini Live API
    const liveCall = useLiveCall()
    
    // 🔒 Control de acceso a IA por plan - ESCALERA DE BENEFICIOS
    // Todos tienen IA, pero con límites diferentes según el plan
    const tenantPlan = computed(() => appStore.tenantPlan || 'free_trial')
    
    // ⚠️ MODO DESARROLLO - Cambiar a false en producción
    const DEV_MODE_UNLIMITED = false
    
    // Límites de mensajes de chat por día según plan
    const CHAT_LIMITS = {
      'free_trial': DEV_MODE_UNLIMITED ? 0 : 30,     // 30 mensajes/día - para que prueben
      'starter': DEV_MODE_UNLIMITED ? 0 : 30,        // 30 mensajes/día - plan inicial
      'basico': DEV_MODE_UNLIMITED ? 0 : 30,         // 30 mensajes/día - uso básico
      'premium': 0,         // 0 = ilimitado
      'enterprise': 0       // 0 = ilimitado
    }
    
    // Límites de voz en segundos por día según plan
    // Presupuesto máximo diario: Premium ~$285 COP, Enterprise ~$2,850 COP
    // ⚠️ En producción: premium=180 (3min), enterprise=1800 (30min)
    const VOICE_LIMITS = {
      'free_trial': DEV_MODE_UNLIMITED ? 0 : 0,      // Sin voz (0 = sin límite si DEV_MODE)
      'basico': DEV_MODE_UNLIMITED ? 0 : 0,          // Sin voz
      'premium': DEV_MODE_UNLIMITED ? 0 : 180,       // 3 minutos/día (~$285 COP) - 0 = ilimitado en dev
      'enterprise': DEV_MODE_UNLIMITED ? 0 : 1800    // 30 minutos/día - 0 = ilimitado en dev
    }
    
    // Todos tienen acceso a IA (chat de texto)
    const hasAIAccess = computed(() => true)
    
    // Solo Premium y Enterprise tienen voz (en dev mode, todos tienen)
    // 🚫 Planes BLOQUEADOS: free_trial y starter
    const hasVoiceAccess = computed(() => {
      if (DEV_MODE_UNLIMITED) return true
      const plan = tenantPlan.value.toLowerCase()
      // Bloquear solo planes básicos (free_trial y starter)
      return !['free_trial', 'starter', 'basico'].includes(plan)
    })
    
    const isEnterprise = computed(() => {
      return tenantPlan.value.toLowerCase() === 'enterprise'
    })
    
    const isPremium = computed(() => {
      return tenantPlan.value.toLowerCase() === 'premium'
    })
    
    // Límite de mensajes para el plan actual
    const chatMessageLimit = computed(() => {
      const plan = tenantPlan.value.toLowerCase()
      return CHAT_LIMITS[plan] ?? 5
    })
    
    // Límite de voz para el plan actual
    const voiceLimitSeconds = computed(() => {
      const plan = tenantPlan.value.toLowerCase()
      return VOICE_LIMITS[plan] ?? 0
    })
    
    // Contador de mensajes usados hoy
    const chatMessagesUsedToday = ref(0)
    const voiceSecondsUsedToday = ref(0)
    
    // ¿Puede enviar más mensajes de chat?
    const canSendChat = computed(() => {
      if (chatMessageLimit.value === 0) return true // Ilimitado
      return chatMessagesUsedToday.value < chatMessageLimit.value
    })
    
    const chatMessagesRemaining = computed(() => {
      if (chatMessageLimit.value === 0) return Infinity
      return Math.max(0, chatMessageLimit.value - chatMessagesUsedToday.value)
    })
    
    // ¿Puede usar voz?
    const canUseVoice = computed(() => {
      if (!hasVoiceAccess.value) return false
      // Si el límite es 0, significa ilimitado (DEV_MODE o plan premium/enterprise)
      if (voiceLimitSeconds.value === 0) return true
      // Si hay límite, verificar que no se haya excedido
      return voiceSecondsUsedToday.value < voiceLimitSeconds.value
    })
    
    const voiceSecondsRemaining = computed(() => {
      // Si es ilimitado (0), retornar un número grande para display
      if (voiceLimitSeconds.value === 0) return 999999
      // Si hay límite, calcular restantes
      return Math.max(0, voiceLimitSeconds.value - voiceSecondsUsedToday.value)
    })
    
    // Cargar uso del día
    const loadDailyUsage = () => {
      const today = new Date().toISOString().split('T')[0]
      
      // Chat
      const chatStored = JSON.parse(localStorage.getItem('ai_chat_usage') || '{}')
      if (chatStored.date !== today) {
        chatMessagesUsedToday.value = 0
        localStorage.setItem('ai_chat_usage', JSON.stringify({ date: today, count: 0 }))
      } else {
        chatMessagesUsedToday.value = chatStored.count || 0
      }
      
      // Voz
      const voiceStored = JSON.parse(localStorage.getItem('ai_voice_usage') || '{}')
      if (voiceStored.date !== today) {
        voiceSecondsUsedToday.value = 0
        localStorage.setItem('ai_voice_usage', JSON.stringify({ date: today, seconds: 0 }))
      } else {
        voiceSecondsUsedToday.value = voiceStored.seconds || 0
      }
    }
    
    // Registrar uso de chat
    const recordChatUsage = () => {
      const today = new Date().toISOString().split('T')[0]
      chatMessagesUsedToday.value += 1
      localStorage.setItem('ai_chat_usage', JSON.stringify({ date: today, count: chatMessagesUsedToday.value }))
    }
    
    // Registrar uso de voz
    const recordVoiceUsage = (seconds) => {
      const today = new Date().toISOString().split('T')[0]
      voiceSecondsUsedToday.value += seconds
      localStorage.setItem('ai_voice_usage', JSON.stringify({ date: today, seconds: voiceSecondsUsedToday.value }))
    }
    
    // Altura dinámica del chat (detecta si hay banner trial)
    const dynamicHeaderHeight = ref(props.headerHeight)
    
    // Calcular altura real del header + banners
    const calculateHeaderOffset = () => {
      // Buscar el header real en el DOM
      const header = document.querySelector('header.sticky')
      if (header) {
        const rect = header.getBoundingClientRect()
        // El offset es donde termina el header (bottom del header desde el top del viewport)
        dynamicHeaderHeight.value = Math.max(rect.bottom, props.headerHeight)
      } else {
        dynamicHeaderHeight.value = props.headerHeight
      }
    }
    
    // Estado del chat
    const messages = ref([])
    const inputMessage = ref('')
    const isTyping = ref(false)
    const messagesContainer = ref(null)
    const messageInput = ref(null)
    const fileInput = ref(null)
    const sessionId = ref(null)
    
    // Hover suggestion para preview
    const hoverSuggestion = ref('')
    
    // Estado para controles de radio en el chat
    // Solo se muestran si la radio fue iniciada desde el chat (para no gastar créditos)
    const radioStartedFromChat = ref(false)
    
    // Selector de proveedor IA
    const selectedProvider = ref('gemini')
    
    // Archivo seleccionado
    const selectedFile = ref(null)
    
    // Tab activo (Chat o Historia)
    const activeTab = ref('chat')
    const chatHistory = ref([])
    const selectedCategory = ref(null)

    // Computed
    const isControlledExternally = computed(() => props.isOpen !== undefined)
    const localChatOpen = computed(() => isControlledExternally.value ? props.isOpen : aiChatStore.isOpen.value)
    
    // Nombre del usuario para saludo personalizado
    const userName = computed(() => {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}')
        return user.name?.split(' ')[0] || '105 Code'
      } catch {
        return '105 Code'
      }
    })
    
    // Computed para mostrar controles de radio (solo si fue iniciada desde el chat)
    const showRadioControls = computed(() => {
      return radioStartedFromChat.value && radioStore.currentStation
    })
    
    // Computed para estado de reproducción
    const isRadioPlaying = computed(() => radioStore.isPlaying)
    const currentStationName = computed(() => radioStore.currentStation?.name || 'Radio 105')
    
    // Categorías con sugerencias específicas
    const categories = [
      { 
        id: 'ventas', 
        name: 'Ventas',
        suggestions: [
          { text: '¿Cuánto vendí hoy?', category: 'ventas' },
          { text: 'Mostrar ventas de esta semana', category: 'ventas' },
          { text: '¿Cuál es mi producto más vendido?', category: 'ventas' },
          { text: 'Comparar ventas del mes pasado', category: 'ventas' }
        ]
      },
      { 
        id: 'inventario', 
        name: 'Inventario',
        suggestions: [
          { text: '¿Qué productos tienen stock bajo?', category: 'inventario' },
          { text: 'Crear un nuevo producto', category: 'inventario' },
          { text: 'Ver el valor total de mi inventario', category: 'inventario' },
          { text: 'Productos sin movimiento', category: 'inventario' }
        ]
      },
      { 
        id: 'facturas', 
        name: 'Facturas',
        suggestions: [
          { text: 'Facturas pendientes de pago', category: 'facturas' },
          { text: 'Crear una nueva factura', category: 'facturas' },
          { text: 'Buscar factura por número', category: 'facturas' },
          { text: 'Facturas vencidas', category: 'facturas' }
        ]
      },
      { 
        id: 'clientes', 
        name: 'Clientes',
        suggestions: [
          { text: 'Mis mejores clientes', category: 'clientes' },
          { text: 'Agregar un nuevo cliente', category: 'clientes' },
          { text: 'Clientes con deuda pendiente', category: 'clientes' },
          { text: 'Historial de compras de un cliente', category: 'clientes' }
        ]
      },
      { 
        id: 'reportes', 
        name: 'Reportes',
        suggestions: [
          { text: 'Generar reporte de ventas mensual', category: 'reportes' },
          { text: 'Reporte de productos más rentables', category: 'reportes' },
          { text: 'Análisis de ganancias', category: 'reportes' },
          { text: 'Reporte de gastos operativos', category: 'reportes' }
        ]
      }
    ]

    // 🎨 Chips estilo Gemini con emojis
    const geminiChips = computed(() => [
      { icon: '📊', text: '¿Cuánto vendí hoy?', category: 'ventas' },
      { icon: '📦', text: 'Productos con stock bajo', category: 'inventario' },
      { icon: '➕', text: 'Crear un nuevo producto', category: 'inventario' },
      { icon: '📄', text: 'Facturas pendientes', category: 'facturas' },
      { icon: '🎵', text: 'Pon música', category: 'radio' }
    ])

    // Sugerencias que cambian según la categoría seleccionada
    const quickSuggestions = computed(() => {
      if (selectedCategory.value) {
        const category = categories.find(c => c.id === selectedCategory.value)
        return category?.suggestions || []
      }
      // Sugerencias por defecto (mezcla de las más útiles)
      return [
        { text: '¿Cuánto vendí hoy?', category: 'ventas' },
        { text: '¿Qué productos tienen stock bajo?', category: 'inventario' },
        { text: 'Crear un nuevo producto', category: 'inventario' },
        { text: 'Facturas pendientes de pago', category: 'facturas' }
      ]
    })
    
    const setCategory = (category) => {
      selectedCategory.value = selectedCategory.value === category.id ? null : category.id
    }
    
    const loadSession = (session) => {
      messages.value = session.messages || []
      activeTab.value = 'chat'
    }

    // Watch para sincronizar cuando cambia isOpen desde fuera
    watch(() => props.isOpen, (newVal) => {
      if (newVal !== undefined && newVal) {
        nextTick(() => messageInput.value?.focus())
        loadUsageStats()
      }
    })

    // Mapeo inteligente de módulos a categorías
    const moduleToCategory = {
      'sales': 'ventas',
      'pos': 'ventas',
      'invoices': 'facturas',
      'quotations': 'facturas',
      'returns': 'facturas',
      'inventory': 'inventario',
      'products': 'inventario',
      'customers': 'clientes',
      'reports': 'reportes',
      'expenses': 'reportes'
    }

    // Auto-seleccionar categoría según el módulo actual
    watch(() => props.currentModule, (newModule) => {
      if (newModule && moduleToCategory[newModule]) {
        selectedCategory.value = moduleToCategory[newModule]
      }
    }, { immediate: true })

    // Verificar si es SuperAdmin (no tiene tenant)
    const isSuperAdmin = () => {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}')
        return user?.role === 'superadmin' || user?.is_super_admin
      } catch {
        return false
      }
    }

    // Cargar configuración del proveedor de IA
    const loadUsageStats = async () => {
      // Omitir si es SuperAdmin (no hay tenant)
      if (isSuperAdmin()) {
        selectedProvider.value = 'gemini'
        return
      }
      
      try {
        const providerResponse = await api.get('/ai/provider-config')
        if (providerResponse.success && providerResponse.data) {
          const config = providerResponse.data
          // Forzar siempre Gemini (Groq descontinuado por problemas de fecha)
          selectedProvider.value = 'gemini'
        }
      } catch (error) {
        selectedProvider.value = 'gemini'
      }
    }

    onMounted(() => {
      loadUsageStats()
      loadDailyUsage()
      calculateHeaderOffset()
      // Recalcular cuando cambie el tamaño de la ventana
      window.addEventListener('resize', calculateHeaderOffset)
    })
    
    // Watch para registrar uso de voz cuando termine la llamada
    watch(() => liveCall.callDuration.value, (newDuration, oldDuration) => {
      // Solo registrar si la llamada terminó (pasó de valor a 0 o el estado cambió a desconectado)
    })
    
    watch(() => liveCall.isConnected.value, (isConnected, wasConnected) => {
      // Cuando la llamada termine (pasa de conectado a desconectado)
      if (wasConnected && !isConnected && liveCall.callDuration.value > 0) {
        const secondsUsed = liveCall.callDuration.value
        
        // Registrar uso de voz
        if (voiceLimitSeconds.value > 0) {
          recordVoiceUsage(secondsUsed)
        }
        
        // 🔒 Mostrar mensaje si la llamada fue terminada automáticamente por límite
        if (liveCall.wasAutoTerminated.value) {
          messages.value.push({
            type: 'ai',
            text: `⏰ Tu tiempo de voz de hoy se agotó. Has usado ${Math.floor(voiceLimitSeconds.value / 60)} minutos. Se renovará mañana.`,
            timestamp: getCurrentTime(),
            isLimit: true
          })
        }
      }
    })

    // Recalcular cuando se abre el chat
    watch(localChatOpen, (isOpen) => {
      if (isOpen) {
        nextTick(() => {
          calculateHeaderOffset()
          messageInput.value?.focus()
        })
      }
    })

    const toggleChat = () => {
      if (isControlledExternally.value) {
        emit('close')
      } else {
        aiChatStore.toggle()
        if (aiChatStore.isOpen.value) {
          nextTick(() => messageInput.value?.focus())
          loadUsageStats()
        }
      }
    }

    const closeChat = () => {
      if (isControlledExternally.value) {
        emit('close')
      } else {
        aiChatStore.close()
      }
    }

    const scrollToBottom = () => {
      nextTick(() => {
        if (messagesContainer.value) {
          messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
        }
      })
    }

    const getCurrentTime = () => {
      return new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
    }

    const handleFileSelect = (event) => {
      const file = event.target.files[0]
      if (file) {
        selectedFile.value = file
        if (file.name.endsWith('.xlsx') || file.name.endsWith('.xls') || file.name.endsWith('.csv')) {
          inputMessage.value = `Analiza este archivo Excel y crea los productos que contiene`
        } else if (file.type.startsWith('image/')) {
          inputMessage.value = `Crea un producto con esta imagen`
        }
      }
    }

    const clearFile = () => {
      selectedFile.value = null
      if (fileInput.value) fileInput.value.value = ''
    }

    const executeSuggestedAction = (action) => {
      if (action && action.type === 'navigate' && action.payload) {
        try {
          const targetModule = action.payload.params?.module
          const queryParams = action.payload.query || {}
          navigateToModule(targetModule, queryParams)
        } catch (err) {
          console.error('Error en acción sugerida:', err)
        }
      }
    }

    const sendMessage = async () => {
      if ((!inputMessage.value.trim() && !selectedFile.value) || isTyping.value) return

      // Verificar límite de chat (0 = ilimitado)
      if (!canSendChat.value) {
        messages.value.push({
          type: 'ai',
          text: '',
          timestamp: getCurrentTime(),
          isLimit: true,
          limitData: {
            used: chatMessageLimit.value,
            renewalText: `Mañana tendrás ${chatMessageLimit.value} mensajes nuevos disponibles.`
          }
        })
        scrollToBottom()
        return
      }

      const userMessage = inputMessage.value.trim()
      const file = selectedFile.value

      messages.value.push({
        type: 'user',
        text: file ? `${userMessage}\n📎 ${file.name}` : userMessage,
        timestamp: getCurrentTime()
      })

      inputMessage.value = ''
      scrollToBottom()

      isTyping.value = true
      
      // 🧠 OPTIMIZACIÓN: Detectar si el mensaje necesita contexto de negocio
      // Mensajes simples como "hola", "gracias", "ok" NO necesitan todos los datos
      const needsBusinessContext = (() => {
        const msg = userMessage.toLowerCase().trim()
        
        // Palabras clave que NO necesitan contexto de negocio
        const simpleMessages = [
          // Saludos
          'hola', 'hi', 'hey', 'hello', 'buenas', 'buenos dias', 'buenos días',
          'buenas tardes', 'buenas noches', 'que tal', 'qué tal', 'como estas',
          'cómo estás', 'como va', 'que onda', 'saludos',
          // Agradecimientos
          'gracias', 'thanks', 'muchas gracias', 'genial', 'perfecto', 'excelente',
          'ok', 'okay', 'vale', 'entendido', 'claro', 'listo', 'bien', 'bueno',
          // Despedidas
          'adios', 'adiós', 'bye', 'chao', 'hasta luego', 'nos vemos',
          // Confirmaciones simples
          'si', 'sí', 'no', 'ya', 'aja', 'ajá', 'mm', 'mmm', 'ah', 'oh'
        ]
        
        // Si el mensaje es exactamente una palabra simple, no necesita contexto
        if (simpleMessages.includes(msg)) {
          return false
        }
        
        // Si el mensaje tiene menos de 3 palabras y empieza con saludo, no necesita contexto
        const words = msg.split(/\s+/)
        if (words.length <= 2) {
          const startsWithGreeting = simpleMessages.some(s => msg.startsWith(s))
          if (startsWithGreeting) {
            return false
          }
        }
        
        // Palabras clave que SÍ requieren contexto de negocio
        const businessKeywords = [
          'venta', 'ventas', 'producto', 'productos', 'stock', 'inventario',
          'cliente', 'clientes', 'factura', 'facturas', 'precio', 'precios',
          'ganancia', 'ganancias', 'caja', 'dinero', 'cuanto', 'cuánto',
          'total', 'hoy', 'mes', 'semana', 'ayer', 'reporte', 'reportes',
          'devolución', 'devoluciones', 'gasto', 'gastos', 'categoria',
          'categorías', 'bajo', 'agotado', 'vendido', 'vendí', 'vendimos',
          'mejor', 'peor', 'top', 'ranking', 'estadistica', 'estadísticas',
          'análisis', 'analisis', 'proveedor', 'proveedores', 'compra', 'compras',
          'margen', 'rentabilidad', 'importar', 'exportar', 'excel', 'csv'
        ]
        
        // Verificar si contiene palabras de negocio
        return businessKeywords.some(keyword => msg.includes(keyword))
      })()
      
      // 🧠 Solo obtener contexto si el mensaje lo necesita
      const screenContext = needsBusinessContext ? aiContextStore.getSystemPrompt() : null
      
      try {
        let response

        if (file) {
          const formData = new FormData()
          formData.append('message', userMessage)
          formData.append('file', file)
          formData.append('provider', selectedProvider.value)
          if (sessionId.value) formData.append('session_id', sessionId.value)
          // Agregar contexto de pantalla al FormData
          if (screenContext) formData.append('screen_context', screenContext)

          response = await api.post('/ai/chat-with-file', formData, { headers: {} })
          clearFile()
        } else {
          response = await api.post('/ai/chat', {
            message: userMessage,
            provider: selectedProvider.value,
            session_id: sessionId.value,
            // 🧠 Enviar contexto de pantalla al backend
            screen_context: screenContext || null
          })
        }

        if (response.session_id) sessionId.value = response.session_id

        // Registrar uso de chat (solo si tiene límite)
        recordChatUsage()

        let aiReply = response.reply
        let aiAction = null
        let suggestedAction = null

        // Primero verificar si la acción viene directamente en response.action (del backend)
        if (response.action) {
          aiAction = response.action
        }

        try {
          if (typeof response.reply === 'string' && response.reply.trim().startsWith('{')) {
            const parsed = JSON.parse(response.reply)
            aiReply = parsed.reply || parsed.text || response.reply
            aiAction = aiAction || parsed.action // No sobreescribir si ya existe
            suggestedAction = parsed.suggested_action
          } else if (typeof response.reply === 'object') {
            aiReply = response.reply.reply
            aiAction = aiAction || response.reply.action
            suggestedAction = response.reply.suggested_action
          }
        } catch (e) {}

        messages.value.push({
          type: 'ai',
          text: aiReply,
          timestamp: getCurrentTime(),
          suggested_action: suggestedAction
        })

        // Mostrar advertencia cuando quedan pocos mensajes (solo si tiene límite)
        if (chatMessageLimit.value > 0) {
          const remaining = chatMessagesRemaining.value
          
          if (remaining === 3) {
            messages.value.push({
              type: 'ai',
              text: `Te quedan 3 mensajes disponibles hoy`,
              timestamp: getCurrentTime(),
              isInfo: true
            })
          } else if (remaining === 1) {
            messages.value.push({
              type: 'ai',
              text: `Este es tu último mensaje del día`,
              timestamp: getCurrentTime(),
              isWarning: true
            })
          }
        }

        if (aiAction && aiAction.type === 'navigate' && aiAction.payload) {
          const targetModule = aiAction.payload.params?.module
          const queryParams = aiAction.payload.query || {}
          if (targetModule) navigateToModule(targetModule, queryParams)
        }

        // 🎵 Procesar acciones de radio
        if (aiAction && aiAction.type === 'radio' && aiAction.payload) {
          const radioAction = aiAction.payload.action
          const volume = aiAction.payload.volume
          
          // Asegurar que el audio esté inicializado
          if (!radioStore.audio) {
            radioStore.initAudio()
          }
          
          // Cargar estaciones si están vacías
          if (radioStore.topStations.length === 0) {
            await radioStore.fetchHomeData()
          }
          
          switch (radioAction) {
            case 'play':
              radioStartedFromChat.value = true // Marcar que la radio fue iniciada desde el chat
              if (!radioStore.currentStation) {
                // Si no hay estación, reproducir una aleatoria
                radioStore.playRandom()
              } else {
                radioStore.audio?.play()
              }
              emit('toggle-radio') // Abrir panel de radio
              break
            case 'pause':
              radioStore.audio?.pause()
              break
            case 'toggle':
              radioStore.togglePlay()
              break
            case 'next':
              radioStore.playNext()
              break
            case 'previous':
              radioStore.playPrevious()
              break
            case 'volume_up':
              radioStore.setVolume(Math.min(100, radioStore.volume + 10))
              break
            case 'volume_down':
              radioStore.setVolume(Math.max(0, radioStore.volume - 10))
              break
            case 'mute':
              radioStore.toggleMute()
              break
          }
          
          if (volume !== undefined) {
            radioStore.setVolume(volume)
          }
        }

      } catch (error) {
        if (error.response?.status === 429) {
          const errorData = error.response.data
          let errorMessage = 'Has alcanzado tu límite de mensajes por hora.'
          
          if (errorData.minutes_remaining) {
            const mins = Math.ceil(errorData.minutes_remaining)
            errorMessage = `Límite alcanzado. Disponible en ${mins} minuto${mins !== 1 ? 's' : ''}.`
          }
          
          messages.value.push({
            type: 'ai',
            text: errorMessage,
            timestamp: getCurrentTime(),
            isLimit: true
          })
        } else {
          messages.value.push({
            type: 'ai',
            text: 'Lo siento, tuve un problema. Por favor intenta de nuevo.',
            timestamp: getCurrentTime(),
            isError: true
          })
        }
      } finally {
        isTyping.value = false
        scrollToBottom()
      }
    }

    const sendQuickMessage = (suggestion) => {
      inputMessage.value = suggestion.text
      hoverSuggestion.value = ''
      sendMessage()
    }

    const startNewConversation = async () => {
      if (sessionId.value) {
        try {
          await api.post('/ai/clear-history', { session_id: sessionId.value })
        } catch (error) {}
      }
      messages.value = []
      sessionId.value = null
    }

    const handleShiftEnter = (e) => {
      const textarea = e.target
      const start = textarea.selectionStart
      const end = textarea.selectionEnd
      inputMessage.value = inputMessage.value.substring(0, start) + '\n' + inputMessage.value.substring(end)
      nextTick(() => {
        textarea.selectionStart = textarea.selectionEnd = start + 1
      })
    }
    
    // Controles de radio directos (sin gastar créditos de IA)
    const radioControlPrevious = () => {
      radioStore.playPrevious()
    }
    
    const radioControlPlayPause = () => {
      radioStore.togglePlay()
    }
    
    const radioControlNext = () => {
      radioStore.playNext()
    }
    
    const closeRadioControls = () => {
      radioStartedFromChat.value = false
    }

    const autoResizeTextarea = () => {
      const textarea = messageInput.value
      if (textarea) {
        textarea.style.height = 'auto'
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px'
      }
    }

    // 📞 Iniciar llamada en vivo
    const startLiveCall = async () => {
      // Verificar acceso por plan
      if (!hasVoiceAccess.value) {
        messages.value.push({
          type: 'ai',
          text: 'La asistencia por voz está disponible en planes Premium y Enterprise.',
          timestamp: getCurrentTime(),
          isWarning: true
        })
        return
      }
      
      // Verificar límite de minutos para Premium (voiceLimitSeconds = 0 significa ilimitado)
      if (voiceLimitSeconds.value > 0 && voiceSecondsUsedToday.value >= voiceLimitSeconds.value) {
        messages.value.push({
          type: 'ai',
          text: `Has usado tus ${Math.floor(voiceLimitSeconds.value / 60)} minutos de voz de hoy. Se renovarán mañana.`,
          timestamp: getCurrentTime(),
          isLimit: true
        })
        return
      }
      
      try {
        // Guardar tiempo de inicio para calcular duración
        const startTime = Date.now()
        
        // 🔒 Establecer límite máximo de duración para corte automático
        const remainingSeconds = voiceSecondsRemaining.value
        liveCall.setMaxDuration(remainingSeconds)
        
        await liveCall.startCall()
        
        // Cuando termine la llamada, registrar el uso
        // Esto se hace mediante un watch al estado de conexión
      } catch (err) {
        messages.value.push({
          type: 'ai',
          text: `📞 No se pudo iniciar la llamada: ${err.message || 'Error desconocido'}`,
          timestamp: getCurrentTime(),
          isWarning: true
        })
      }
    }
    
    // 📞 Terminar llamada en vivo
    const endLiveCall = () => {
      liveCall.endCall()
    }

    return {
      localChatOpen,
      isControlledExternally,
      dynamicHeaderHeight,
      messages,
      inputMessage,
      isTyping,
      messagesContainer,
      messageInput,
      fileInput,
      sessionId,
      selectedProvider,
      selectedFile,
      quickSuggestions,
      geminiChips,
      activeTab,
      chatHistory,
      selectedCategory,
      categories,
      userName,
      hoverSuggestion,
      setCategory,
      loadSession,
      toggleChat,
      closeChat,
      sendMessage,
      sendQuickMessage,
      startNewConversation,
      handleShiftEnter,
      autoResizeTextarea,
      executeSuggestedAction,
      handleFileSelect,
      clearFile,
      // Controles de radio
      showRadioControls,
      isRadioPlaying,
      currentStationName,
      radioControlPrevious,
      radioControlPlayPause,
      radioControlNext,
      closeRadioControls,
      // 📞 Llamada en vivo
      liveCall,
      startLiveCall,
      endLiveCall,
      // 🔒 Control de acceso por plan
      tenantPlan,
      hasAIAccess,
      hasVoiceAccess,
      isEnterprise,
      canUseVoice,
      voiceSecondsRemaining,
      voiceLimitSeconds,
      // 💬 Límites de chat por plan
      chatMessageLimit,
      chatMessagesUsedToday,
      chatMessagesRemaining,
      canSendChat
    }
  }
}
</script>

<style scoped>
/* Animación slide-right para panel lateral estilo Hostinger */
.slide-right-enter-active,
.slide-right-leave-active {
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-right-enter-from,
.slide-right-leave-to {
  transform: translateX(100%);
}

/* Animación slide-down para controles de radio */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-100%);
  max-height: 0;
}

.slide-down-enter-to,
.slide-down-leave-from {
  opacity: 1;
  transform: translateY(0);
  max-height: 80px;
}

/* Fade in para mensajes - más suave */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Fade animation para Live Call overlay */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Scrollbar ultra minimalista */
.overflow-y-auto::-webkit-scrollbar {
  width: 5px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 10px;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.2);
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}

/* Pulse animation para typing dots */
@keyframes pulse-dot {
  0%, 80%, 100% {
    transform: scale(1);
    opacity: 0.5;
  }
  40% {
    transform: scale(1.2);
    opacity: 1;
  }
}

/* Font para el chat - Inter/System */
.font-chat {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  -webkit-font-smoothing: antialiased;
}

/* ════════════════════════════════════════════════════════════════════════════
   🎨 ANIMACIONES FUTURISTAS PARA VOICE AI - Estilo Gemini/Siri/Apple Intelligence
════════════════════════════════════════════════════════════════════════════ */

/* Ambient Glow Pulse - Para los fondos de luz difusa */
@keyframes ambient-pulse {
  0%, 100% {
    opacity: 0.3;
    transform: scale(1);
  }
  50% {
    opacity: 0.5;
    transform: scale(1.05);
  }
}

@keyframes ambient-pulse-delayed {
  0%, 100% {
    opacity: 0.25;
    transform: scale(1);
  }
  50% {
    opacity: 0.4;
    transform: scale(1.08);
  }
}

.animate-ambient-pulse {
  animation: ambient-pulse 3s ease-in-out infinite;
}

.animate-ambient-pulse-delayed {
  animation: ambient-pulse-delayed 4s ease-in-out infinite 0.5s;
}

/* ════════════════════════════════════════════════════════
   BLOB DINÁMICO MULTI-CAPA - Estilo Premium
   ════════════════════════════════════════════════════════ */

/* ═══════════════════════════════════════════════════════════
   VOICE LOADER - Visualizador geométrico animado (optimizado GPU)
═══════════════════════════════════════════════════════════ */
.voice-loader {
  --color-one: #60A5FA;
  --color-two: #8B5CF6;
  --color-three: #60A5FA80;
  --color-four: #8B5CF680;
  --color-five: #60A5FA40;
  --time-animation: 3s;
  --size: 0.8;
  position: relative;
  border-radius: 50%;
  transform: scale(var(--size));
  will-change: transform;
  box-shadow:
    0 0 20px 0 var(--color-three),
    0 15px 35px 0 var(--color-four);
  animation: voice-colorize 8s ease-in-out infinite;
}

.voice-loader::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100px;
  height: 100px;
  border-radius: 50%;
  border-top: solid 1px var(--color-one);
  border-bottom: solid 1px var(--color-two);
  background: linear-gradient(180deg, var(--color-five), var(--color-four));
  box-shadow:
    inset 0 10px 10px 0 var(--color-three),
    inset 0 -10px 10px 0 var(--color-four);
}

.voice-loader .voice-loader-box {
  width: 100px;
  height: 100px;
  background: linear-gradient(
    180deg,
    var(--color-one) 30%,
    var(--color-two) 70%
  );
  mask: url(#clipping);
  -webkit-mask: url(#clipping);
}

.voice-loader svg {
  position: absolute;
}

.voice-loader svg #clipping {
  filter: contrast(10);
}

.voice-loader svg #clipping polygon {
  filter: blur(5px);
  will-change: transform;
}

.voice-loader svg #clipping polygon:nth-child(1) {
  transform-origin: 75% 25%;
  transform: rotate(90deg);
}

.voice-loader svg #clipping polygon:nth-child(2) {
  transform-origin: 50% 50%;
  animation: voice-rotation var(--time-animation) linear infinite reverse;
}

.voice-loader svg #clipping polygon:nth-child(3) {
  transform-origin: 50% 60%;
  animation: voice-rotation var(--time-animation) linear infinite;
  animation-delay: calc(var(--time-animation) / -3);
}

.voice-loader svg #clipping polygon:nth-child(4) {
  transform-origin: 40% 40%;
  animation: voice-rotation var(--time-animation) linear infinite reverse;
}

.voice-loader svg #clipping polygon:nth-child(5) {
  transform-origin: 40% 40%;
  animation: voice-rotation var(--time-animation) linear infinite reverse;
  animation-delay: calc(var(--time-animation) / -2);
}

.voice-loader svg #clipping polygon:nth-child(6) {
  transform-origin: 60% 40%;
  animation: voice-rotation var(--time-animation) linear infinite;
}

.voice-loader svg #clipping polygon:nth-child(7) {
  transform-origin: 60% 40%;
  animation: voice-rotation var(--time-animation) linear infinite;
  animation-delay: calc(var(--time-animation) / -1.5);
}

@keyframes voice-rotation {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes voice-colorize {
  0% { filter: hue-rotate(0deg); }
  50% { filter: hue-rotate(-60deg); }
  100% { filter: hue-rotate(0deg); }
}

/* Spin lento para el orbe de conexión */
@keyframes spin-slow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-spin-slow {
  animation: spin-slow 3s linear infinite;
}
</style>
