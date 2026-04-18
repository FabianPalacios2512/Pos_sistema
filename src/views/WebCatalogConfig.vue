<template>
  <div>
  <!-- Layout de 3 Columnas: Menú Lateral + Contenido + Preview - Gemini Style -->
  <div class="flex overflow-hidden bg-[#f8f9fa] dark:bg-gradient-to-b dark:from-[#131314] dark:via-[#1e1f20] dark:to-[#131314]" style="height: 100%;">
    
    <!-- SIDEBAR IZQUIERDO - Menú de Navegación - Gemini -->
    <aside class="w-64 bg-white dark:bg-[#1e1f20] border-r border-[#e8eaed] dark:border-[#3a3a3f] flex flex-col" style="min-height: 0;">
      <!-- Header Sidebar - Gemini -->
      <div class="px-4 py-3 border-b border-[#e8eaed] dark:border-[#3a3a3f] flex-shrink-0">
        <div class="flex items-center gap-2 mb-3">
          <div class="w-8 h-8 bg-[#1e1f20] dark:bg-[#3a3a3f] rounded-full flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </div>
          <div>
            <h2 class="text-xs font-medium text-[#1e1f20] dark:text-[#e3e3e3] tracking-tight">Configuración</h2>
            <p class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6]">Catálogo Web</p>
          </div>
        </div>
        
        <!-- Estado de la Tienda - Gemini -->
        <div class="flex items-center justify-between p-2 bg-[#f8f9fa] dark:bg-[#282a2c] rounded-full">
          <span class="text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] ml-1">Estado:</span>
          <div class="flex items-center gap-1.5">
            <button 
              @click="config.storeActive = !config.storeActive"
              class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none"
              :class="config.storeActive ? 'bg-[#1e8e3e]' : 'bg-[#dadce0] dark:bg-[#5f6368]'"
            >
              <span
                class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform"
                :class="config.storeActive ? 'translate-x-5' : 'translate-x-0.5'"
              />
            </button>
            <span class="text-[9px] font-medium px-2 py-0.5 rounded-full" 
                  :class="config.storeActive 
                    ? 'bg-[#e6f4ea] dark:bg-[#1e8e3e]/20 text-[#1e8e3e] dark:text-[#81c995]' 
                    : 'bg-[#f0f4f9] dark:bg-[#3a3a3f] text-[#5f6368] dark:text-[#9aa0a6]'">
              {{ config.storeActive ? 'Activa' : 'Inactiva' }}
            </span>
          </div>
        </div>
      </div>
      
      <!-- Navegación - Gemini -->
      <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          class="w-full text-left px-3 py-2.5 rounded-full transition-all duration-150 flex items-center gap-2.5 group"
          :class="activeTab === tab.id 
            ? 'bg-[#e8f0fe] dark:bg-[#1a73e8]/20 text-[#1a73e8] dark:text-[#8ab4f8]' 
            : 'text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#f0f4f9] dark:hover:bg-[#282a2c]'"
        >
          <div class="w-4 h-4 flex items-center justify-center flex-shrink-0">
            <svg v-if="tab.icon === 'sparkles'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#5f6368] dark:text-[#9aa0a6]'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
            </svg>
            <svg v-else-if="tab.icon === 'palette'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#5f6368] dark:text-[#9aa0a6]'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
            </svg>
            <svg v-else-if="tab.icon === 'box'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#5f6368] dark:text-[#9aa0a6]'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <svg v-else-if="tab.icon === 'message'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#5f6368] dark:text-[#9aa0a6]'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <svg v-else-if="tab.icon === 'settings'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#5f6368] dark:text-[#9aa0a6]'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <span class="text-xs font-medium">{{ tab.label }}</span>
        </button>
      </nav>
      
      <!-- Footer Sidebar con botones de acción - Gemini -->
      <div class="px-3 pb-3 border-t border-[#e8eaed] dark:border-[#3a3a3f] pt-3 space-y-1.5 flex-shrink-0 bg-[#f8f9fa] dark:bg-[#282a2c]">
        <button 
          @click="saveConfiguration"
          :disabled="isSaving"
          class="w-full px-3 py-2.5 bg-[#1e8e3e] hover:bg-[#168936] text-white text-xs font-medium rounded-full transition-all duration-150 flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg v-if="!isSaving" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isSaving ? 'Guardando...' : 'Guardar Todo' }}
        </button>
        
        <button 
          @click="copyStoreLink"
          class="w-full px-3 py-2 bg-white dark:bg-[#3a3a3f] hover:bg-[#f0f4f9] dark:hover:bg-[#4a4a4f] text-[#1e1f20] dark:text-[#e3e3e3] text-xs font-medium rounded-full transition-all duration-150 flex items-center justify-center gap-1.5">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
          </svg>
          Copiar Enlace
        </button>
        
        <button 
          @click="openCatalogInNewWindow"
          class="w-full px-3 py-2 bg-[#1e1f20] dark:bg-[#e3e3e3] hover:bg-black dark:hover:bg-white text-white dark:text-[#1e1f20] text-xs font-medium rounded-full transition-all duration-150 flex items-center justify-center gap-1.5"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
          Ver Página
        </button>
      </div>
    </aside>
    
    <!-- CONTENIDO CENTRAL - Gemini -->
    <main class="flex-1 overflow-y-auto bg-[#f8f9fa] dark:bg-transparent">
      <div class="p-8 space-y-6 max-w-5xl mx-auto">
        
        <!-- Barra de Advertencia - Configuración Incompleta - Gemini -->
        <div v-if="showWarningMessage" 
             class="bg-[#fef7e0] dark:bg-[#ea8600]/15 border-l-4 border-[#ea8600] rounded-2xl p-4 flex items-start gap-3 animate-fade-in">
          <svg class="w-5 h-5 text-[#ea8600] dark:text-[#fdd663] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <div class="flex-1">
            <h4 class="text-sm font-medium text-[#ea8600] dark:text-[#fdd663]">Configuración Incompleta</h4>
            <p v-if="warningType === 'categories'" class="text-xs text-[#b06000] dark:text-[#fcc934] mt-1">
              No puedes activar el catálogo sin seleccionar ninguna categoría para mostrar. 
              Ve a la pestaña <span class="font-medium">"Catálogo"</span> y selecciona al menos una categoría.
            </p>
            <p v-else-if="warningType === 'whatsapp'" class="text-xs text-[#b06000] dark:text-[#fcc934] mt-1">
              No puedes activar el catálogo sin configurar un número de WhatsApp válido. 
              Ve a la pestaña <span class="font-medium">"Pedidos"</span> e ingresa tu número completo (ej: +573001234567).
            </p>
          </div>
          <button 
            @click="activeTab = warningType === 'categories' ? 'catalog' : 'orders'; showWarningMessage = false" 
            class="px-3 py-1.5 bg-[#ea8600] hover:bg-[#b06000] text-white text-xs font-medium rounded-full transition-colors flex-shrink-0">
            {{ warningType === 'categories' ? 'Ir a Catálogo' : 'Ir a Pedidos' }}
          </button>
        </div>
          
          <!-- SECCIÓN: IA MARCA - Motor de Diseño Adaptativo -->
          <div v-if="activeTab === 'ai-brand'" class="space-y-6 animate-fade-in">

            <!-- TARJETA 1: Describe tu Negocio -->
            <div class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
              <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                  </svg>
                  <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Describe tu Negocio</h3>
                </div>
                <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-1">Cuéntanos sobre tu tienda y la IA creará una identidad de marca completa</p>
              </div>
              
              <div class="p-6 space-y-4">
                <div class="relative">
                  <textarea 
                    v-model="aiBrandDescription"
                    rows="5"
                    placeholder="Ej: Vendo ropa casual para mujer, mi estilo es boho chic y minimalista. Me enfoco en telas naturales y colores tierra. Mi público son mujeres de 25-40 años que buscan comodidad con estilo..."
                    class="w-full px-4 py-3 rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] text-sm text-[#1e1f20] dark:text-[#e3e3e3] placeholder-[#5f6368] dark:placeholder-[#9aa0a6] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent resize-none"
                    :disabled="isGeneratingBrand"
                  ></textarea>
                  <span class="absolute bottom-3 right-3 text-[10px] text-[#9aa0a6]">{{ aiBrandDescription.length }}/2000</span>
                </div>

                <div class="flex items-center gap-3">
                  <!-- Botón Generar -->
                  <button 
                    @click="generateAiBrand"
                    :disabled="isGeneratingBrand || aiBrandDescription.trim().length < 10"
                    class="flex-1 px-6 py-3 bg-[#1a73e8] hover:bg-[#1557b0] text-white text-sm font-medium rounded-full transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg v-if="!isGeneratingBrand" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                    <svg v-else class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ isGeneratingBrand ? aiGenerationProgress || 'Generando...' : 'Generar Identidad con IA' }}
                  </button>

                  <!-- Botón Micrófono -->
                  <button 
                    @click="isRecordingVoice ? stopVoiceRecording() : startVoiceRecording()"
                    class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-200 flex-shrink-0"
                    :class="isRecordingVoice 
                      ? 'bg-[#ea4335] text-white animate-pulse shadow-lg shadow-[#ea4335]/30' 
                      : 'bg-[#f8f9fa] dark:bg-[#282a2c] text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#e8eaed] dark:hover:bg-[#3a3a3f] border border-[#e8eaed] dark:border-[#3a3a3f]'"
                    :title="isRecordingVoice ? 'Detener grabación' : 'Dictar descripción'"
                  >
                    <svg v-if="!isRecordingVoice" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" />
                    </svg>
                  </button>
                </div>

                <p v-if="isRecordingVoice" class="text-xs text-[#ea4335] dark:text-[#f28b82] flex items-center gap-1.5">
                  <span class="w-2 h-2 bg-[#ea4335] rounded-full animate-pulse"></span>
                  Grabando... Habla sobre tu negocio y presiona el botón rojo para terminar.
                </p>
              </div>
            </div>

            <!-- RESULTADOS DE IA (Solo si hay datos generados) -->
            <template v-if="aiBrandData">
              
              <!-- TARJETA 2: Paleta de Colores Generada -->
              <div v-if="aiBrandData.color_palette" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f] flex items-center justify-between">
                  <div>
                    <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Paleta de Colores</h3>
                    <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Generada por IA para tu identidad de marca</p>
                  </div>
                  <button 
                    @click="applyAiColors"
                    class="px-4 py-2 bg-[#1a73e8] hover:bg-[#1557b0] text-white text-xs font-medium rounded-full transition-all"
                  >
                    Aplicar Color
                  </button>
                </div>
                <div class="p-6">
                  <div class="grid grid-cols-6 gap-3">
                    <div v-for="(color, key) in aiBrandData.color_palette" :key="key" class="text-center">
                      <div 
                        class="w-full aspect-square rounded-2xl border-2 border-[#e8eaed] dark:border-[#3a3a3f] mb-2 cursor-pointer hover:scale-105 transition-transform"
                        :style="{ backgroundColor: color }"
                        :title="color"
                        @click="navigator.clipboard.writeText(color)"
                      ></div>
                      <p class="text-[9px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">{{ key.replace('_', ' ') }}</p>
                      <p class="text-[10px] font-mono text-[#1e1f20] dark:text-[#e3e3e3]">{{ color }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- TARJETA 3: Tipografías -->
              <div v-if="aiBrandData.fonts" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                  <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Tipografías Recomendadas</h3>
                  <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Par de fuentes seleccionado para tu marca</p>
                </div>
                <div class="p-6">
                  <div class="grid grid-cols-2 gap-6">
                    <div class="p-4 rounded-2xl bg-[#f8f9fa] dark:bg-[#282a2c] border border-[#e8eaed] dark:border-[#3a3a3f]">
                      <p class="text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide mb-2">Títulos</p>
                      <p class="text-2xl text-[#1e1f20] dark:text-[#e3e3e3]" :style="{ fontFamily: aiBrandData.fonts.heading + ', serif' }">
                        {{ aiBrandData.fonts.heading }}
                      </p>
                    </div>
                    <div class="p-4 rounded-2xl bg-[#f8f9fa] dark:bg-[#282a2c] border border-[#e8eaed] dark:border-[#3a3a3f]">
                      <p class="text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide mb-2">Cuerpo de texto</p>
                      <p class="text-base text-[#1e1f20] dark:text-[#e3e3e3]" :style="{ fontFamily: aiBrandData.fonts.body + ', sans-serif' }">
                        {{ aiBrandData.fonts.body }}
                      </p>
                    </div>
                  </div>
                  <p v-if="aiBrandData.fonts.style_rationale" class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-4 italic">
                    {{ aiBrandData.fonts.style_rationale }}
                  </p>
                </div>
              </div>

              <!-- TARJETA 4: Plantilla Recomendada -->
              <div v-if="aiBrandData.recommended_template" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f] flex items-center justify-between">
                  <div>
                    <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Plantilla Recomendada</h3>
                    <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">
                      La IA recomienda: <span class="font-medium text-[#1a73e8] dark:text-[#8ab4f8]">
                        {{ aiBrandData.recommended_template === 'visual-story' ? 'Historia Visual' : aiBrandData.recommended_template === 'speed-market' ? 'Mercado Rápido' : 'Cuadrícula Moderna' }}
                      </span>
                    </p>
                  </div>
                  <button 
                    @click="applyAiTemplate"
                    class="px-4 py-2 bg-[#1a73e8] hover:bg-[#1557b0] text-white text-xs font-medium rounded-full transition-all"
                  >
                    Aplicar Plantilla
                  </button>
                </div>
              </div>

              <!-- TARJETA 5: Textos del Banner -->
              <div v-if="aiBrandData.banner_texts" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                  <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Textos del Banner</h3>
                  <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Copy profesional generado para tu banner principal</p>
                </div>
                <div class="p-6 space-y-4">
                  <div class="relative rounded-2xl overflow-hidden" :style="{ backgroundColor: aiBrandData.color_palette?.primary || '#1a1a1a' }">
                    <div class="p-8 text-center">
                      <h2 class="text-2xl font-light text-white mb-2" style="text-shadow: 0 2px 12px rgba(0,0,0,0.3);">
                        {{ aiBrandData.banner_texts.headline }}
                      </h2>
                      <p class="text-sm text-white/80 tracking-wider uppercase">
                        {{ aiBrandData.banner_texts.subheadline }}
                      </p>
                      <span v-if="aiBrandData.banner_texts.cta_text" class="inline-block mt-4 px-6 py-2 bg-white text-gray-900 text-xs font-semibold uppercase tracking-wide rounded-full">
                        {{ aiBrandData.banner_texts.cta_text }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- TARJETA 6: Sobre Nosotros -->
              <div v-if="aiBrandData.about_us" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                  <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Nuestra Historia</h3>
                  <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Sección "Sobre Nosotros" generada por IA</p>
                </div>
                <div class="p-6">
                  <p class="text-sm text-[#1e1f20] dark:text-[#e3e3e3] leading-relaxed whitespace-pre-line">
                    {{ aiBrandData.about_us }}
                  </p>
                </div>
              </div>

              <!-- TARJETA 7: Mensajes de Valor + Anuncios -->
              <div class="grid grid-cols-2 gap-4">
                <div v-if="aiBrandData.value_messages" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                  <div class="px-5 py-3 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                    <h4 class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Mensajes de Valor</h4>
                  </div>
                  <div class="p-5 space-y-2">
                    <div v-for="(msg, i) in aiBrandData.value_messages" :key="'val-'+i" class="flex items-start gap-2">
                      <span class="w-5 h-5 rounded-full bg-[#e6f4ea] dark:bg-[#1e8e3e]/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3 h-3 text-[#1e8e3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                      </span>
                      <span class="text-xs text-[#1e1f20] dark:text-[#e3e3e3]">{{ msg }}</span>
                    </div>
                  </div>
                </div>

                <div v-if="aiBrandData.announcements" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                  <div class="px-5 py-3 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                    <h4 class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Barra de Anuncios</h4>
                  </div>
                  <div class="p-5 space-y-2">
                    <div v-for="(ann, i) in aiBrandData.announcements" :key="'ann-'+i" class="flex items-center gap-2">
                      <span class="w-1.5 h-1.5 rounded-full bg-[#1a73e8] dark:bg-[#8ab4f8] flex-shrink-0"></span>
                      <span class="text-xs text-[#1e1f20] dark:text-[#e3e3e3]">{{ ann }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- TARJETA 8: Mensajes de Venta Cruzada -->
              <div v-if="aiBrandData.cross_sell_messages" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                  <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Mensajes de Recomendación</h3>
                  <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Venta cruzada profesional para tu catálogo</p>
                </div>
                <div class="p-6 space-y-3">
                  <div v-for="(msg, i) in aiBrandData.cross_sell_messages" :key="'cross-'+i" 
                       class="px-4 py-3 rounded-xl bg-[#f8f9fa] dark:bg-[#282a2c] border border-[#e8eaed] dark:border-[#3a3a3f]">
                    <p class="text-sm text-[#1e1f20] dark:text-[#e3e3e3]">{{ msg }}</p>
                  </div>
                </div>
              </div>

              <!-- Botón Aplicar Todo + Timestamp -->
              <div class="flex items-center justify-between">
                <p v-if="aiBrandData.generated_at" class="text-[10px] text-[#9aa0a6]">
                  Generado: {{ new Date(aiBrandData.generated_at).toLocaleString('es-CO') }}
                </p>
                <button 
                  @click="applyAllAiSettings"
                  class="px-6 py-3 bg-[#1e8e3e] hover:bg-[#168936] text-white text-sm font-medium rounded-full transition-all duration-200 flex items-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                  Aplicar Todo y Guardar
                </button>
              </div>

            </template>

          </div>

          <!-- SECCIÓN: DISEÑO - Estilo Gemini -->
          <div v-if="activeTab === 'identity'" class="space-y-6 animate-fade-in">
            
            <!-- TARJETA 1: Logo y Color Primario - Gemini -->
            <div class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
              <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Identidad Visual</h3>
                <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Logo y color principal de tu tienda</p>
              </div>
              
              <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                  
                  <!-- Logo Upload - Gemini -->
                  <div class="space-y-3">
                    <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6]">Logo de la Tienda</label>
                    <div 
                      @click="triggerFileUpload('logo')"
                      class="relative w-full h-32 border-2 border-dashed border-[#dadce0] dark:border-[#3a3a3f] rounded-2xl hover:border-[#1a73e8] dark:hover:border-[#8ab4f8] hover:bg-[#e8f0fe] dark:hover:bg-[#1a73e8]/10 transition-all cursor-pointer group bg-[#f8f9fa] dark:bg-[#282a2c] flex items-center justify-center"
                    >
                      <div v-if="config.brandIdentity.logo" class="absolute inset-0 p-3 flex items-center justify-center">
                        <img :src="config.brandIdentity.logo" class="max-w-full max-h-full object-contain" />
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 rounded-2xl">
                          <button 
                            @click.stop="config.brandIdentity.logo = ''"
                            class="text-white text-xs font-medium px-3 py-2 bg-[#ea4335] hover:bg-[#d33426] rounded-full transition-colors flex items-center gap-1.5"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Eliminar
                          </button>
                          <span class="text-white text-xs font-medium px-3 py-2 bg-white/20 rounded-full">Cambiar</span>
                        </div>
                      </div>
                      <div v-else class="text-[#5f6368] dark:text-[#9aa0a6] group-hover:text-[#1a73e8] dark:group-hover:text-[#8ab4f8] transition-colors flex flex-col items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div class="text-center">
                          <span class="text-xs font-medium block">Subir logo</span>
                          <span class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6]">PNG, JPG o SVG</span>
                        </div>
                      </div>
                      <input type="file" ref="logoInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'logo')" />
                    </div>
                  </div>
                  
                  <!-- Color Picker - Gemini -->
                  <div class="space-y-3">
                    <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6]">Color Primario</label>
                    <div class="flex items-center gap-3 p-4 border border-[#e8eaed] dark:border-[#3a3a3f] rounded-2xl bg-white dark:bg-[#282a2c] hover:border-[#1a73e8] dark:hover:border-[#8ab4f8] transition-all">
                      <input 
                        type="color" 
                        v-model="config.brandIdentity.primaryColor"
                        class="w-12 h-12 rounded-xl cursor-pointer border border-[#e8eaed] dark:border-[#3a3a3f]"
                      />
                      <div class="flex-1">
                        <input 
                          type="text"
                          v-model="config.brandIdentity.primaryColor"
                          class="w-full px-3 py-2 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f] text-sm font-mono text-[#1e1f20] dark:text-[#e3e3e3] bg-[#f8f9fa] dark:bg-[#1e1f20] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] uppercase"
                          placeholder="#10B981"
                        />
                        <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1.5">Este color se aplicará en botones y acentos</p>
                      </div>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
            
            <!-- TARJETA 2: Selección de Plantilla - Gemini -->
            <div class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
              <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Plantilla de Diseño</h3>
                <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Elige el estilo visual de tu catálogo web</p>
              </div>
              
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  
                  <!-- Visual Story (Solo para Fashion) - Gemini -->
                  <button 
                    v-if="isFashionStore"
                    @click="config.brandIdentity.template = 'visual-story'"
                    class="group relative p-5 rounded-2xl border transition-all text-left"
                    :class="config.brandIdentity.template === 'visual-story' 
                      ? 'border-[#1a73e8] dark:border-[#8ab4f8] bg-[#e8f0fe] dark:bg-[#1a73e8]/15' 
                      : 'border-[#e8eaed] dark:border-[#3a3a3f] hover:border-[#1a73e8]/50 dark:hover:border-[#8ab4f8]/50 bg-white dark:bg-[#282a2c]'"
                  >
                    <div class="flex flex-col items-center text-center space-y-3">
                      <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#e8eaed] to-[#f0f4f9] dark:from-[#3a3a3f] dark:to-[#282a2c] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-[#5f6368] dark:text-[#9aa0a6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-medium" :class="config.brandIdentity.template === 'visual-story' ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#1e1f20] dark:text-[#e3e3e3]'">
                          Historia Visual
                        </div>
                        <div class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Boutique / Gourmet</div>
                      </div>
                      <div v-if="config.brandIdentity.template === 'visual-story'" class="absolute top-3 right-3 w-5 h-5 rounded-full bg-[#1a73e8] dark:bg-[#8ab4f8] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white dark:text-[#1e1f20]" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </button>

                  <!-- Speed Market - Gemini -->
                  <button 
                    @click="config.brandIdentity.template = 'speed-market'"
                    class="group relative p-5 rounded-2xl border transition-all text-left"
                    :class="config.brandIdentity.template === 'speed-market' 
                      ? 'border-[#1a73e8] dark:border-[#8ab4f8] bg-[#e8f0fe] dark:bg-[#1a73e8]/15' 
                      : 'border-[#e8eaed] dark:border-[#3a3a3f] hover:border-[#1a73e8]/50 dark:hover:border-[#8ab4f8]/50 bg-white dark:bg-[#282a2c]'"
                  >
                    <div class="flex flex-col items-center text-center space-y-3">
                      <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#e8eaed] to-[#f0f4f9] dark:from-[#3a3a3f] dark:to-[#282a2c] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-[#5f6368] dark:text-[#9aa0a6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-medium" :class="config.brandIdentity.template === 'speed-market' ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#1e1f20] dark:text-[#e3e3e3]'">
                          Mercado Rápido
                        </div>
                        <div class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Supermercado / Rápido</div>
                      </div>
                      <div v-if="config.brandIdentity.template === 'speed-market'" class="absolute top-3 right-3 w-5 h-5 rounded-full bg-[#1a73e8] dark:bg-[#8ab4f8] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white dark:text-[#1e1f20]" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </button>

                  <!-- Modern Grid - Gemini -->
                  <button 
                    @click="config.brandIdentity.template = 'modern-grid'"
                    class="group relative p-5 rounded-2xl border transition-all text-left"
                    :class="config.brandIdentity.template === 'modern-grid' 
                      ? 'border-[#1a73e8] dark:border-[#8ab4f8] bg-[#e8f0fe] dark:bg-[#1a73e8]/15' 
                      : 'border-[#e8eaed] dark:border-[#3a3a3f] hover:border-[#1a73e8]/50 dark:hover:border-[#8ab4f8]/50 bg-white dark:bg-[#282a2c]'"
                  >
                    <div class="flex flex-col items-center text-center space-y-3">
                      <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#e8eaed] to-[#f0f4f9] dark:from-[#3a3a3f] dark:to-[#282a2c] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-[#5f6368] dark:text-[#9aa0a6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z" />
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-medium" :class="config.brandIdentity.template === 'modern-grid' ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#1e1f20] dark:text-[#e3e3e3]'">
                          Cuadrícula Moderna
                        </div>
                        <div class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Clásico / Versátil</div>
                      </div>
                      <div v-if="config.brandIdentity.template === 'modern-grid'" class="absolute top-3 right-3 w-5 h-5 rounded-full bg-[#1a73e8] dark:bg-[#8ab4f8] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white dark:text-[#1e1f20]" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </button>
                  
                </div>
              </div>
            </div>
            
            <!-- TARJETA 3: Banner Promocional (Solo Fashion) - Gemini -->
            <div v-if="isFashionStore" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
              <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Banner Promocional</h3>
                <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Imagen destacada en la parte superior (exclusivo para tiendas de moda)</p>
              </div>
              
              <div class="p-6">
                <div 
                  @click="triggerFileUpload('banner')"
                  class="relative w-full h-48 border-2 border-dashed border-[#dadce0] dark:border-[#3a3a3f] rounded-2xl hover:border-[#1a73e8] dark:hover:border-[#8ab4f8] hover:bg-[#e8f0fe] dark:hover:bg-[#1a73e8]/10 transition-all cursor-pointer group bg-[#f8f9fa] dark:bg-[#282a2c] flex items-center justify-center overflow-hidden"
                >
                  <div v-if="config.brandIdentity.banner" class="absolute inset-0 p-2">
                    <img :src="config.brandIdentity.banner" class="w-full h-full object-cover rounded-xl" />
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3 rounded-xl">
                      <button 
                        @click.stop="config.brandIdentity.banner = ''"
                        class="text-white text-sm font-medium px-4 py-2.5 bg-[#ea4335] hover:bg-[#d33426] rounded-full transition-colors flex items-center gap-2"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Eliminar Banner
                      </button>
                      <span class="text-white text-sm font-medium px-4 py-2.5 bg-white/20 rounded-full">Cambiar Imagen</span>
                    </div>
                  </div>
                  <div v-else class="text-[#5f6368] dark:text-[#9aa0a6] group-hover:text-[#1a73e8] dark:group-hover:text-[#8ab4f8] transition-colors flex flex-col items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div class="text-center">
                      <span class="text-sm font-medium block">Subir banner promocional</span>
                      <span class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1 block">Recomendado: 1200x400px</span>
                    </div>
                  </div>
                  <input type="file" ref="bannerInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'banner')" />
                </div>
              </div>
            </div>

          </div>

          <!-- SECCIÓN: PRODUCTOS - Estilo Gemini -->
          <div v-else-if="activeTab === 'catalog'" class="space-y-6 animate-fade-in pb-8">
            
            <section>
              <div class="mb-4">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Visibilidad del Catálogo</h3>
                <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Controla qué categorías y productos se muestran en tu tienda online.</p>
              </div>
              
              <!-- Advertencia si no hay categorías cargadas -->
              <div v-if="availableCategories.length === 0" 
                   class="bg-[#e8f0fe] dark:bg-[#1a73e8]/15 border-l-4 border-[#1a73e8] dark:border-[#8ab4f8] rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-[#1a73e8] dark:text-[#8ab4f8] flex-shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm text-[#1a73e8] dark:text-[#8ab4f8]">Cargando categorías desde la base de datos...</p>
              </div>
              
              <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-5 border border-[#e8eaed] dark:border-[#3a3a3f]">
                <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6] mb-3">Categorías Visibles</label>
                <div class="flex flex-wrap gap-2">
                  <button 
                    v-for="category in availableCategories" 
                    :key="category.id"
                    @click="toggleCategory(category.id)"
                    class="px-3 py-1.5 rounded-full text-xs font-medium transition-all duration-150 flex items-center gap-2"
                    :class="config.inventoryVisibility.visibleCategories.includes(category.id) 
                      ? 'bg-[#e8f0fe] dark:bg-[#1a73e8]/20 text-[#1a73e8] dark:text-[#8ab4f8] ring-2 ring-[#1a73e8]/30 dark:ring-[#8ab4f8]/30' 
                      : 'bg-[#f8f9fa] dark:bg-[#282a2c] text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#e8eaed] dark:hover:bg-[#3a3a3f]'"
                  >
                    <span>{{ category.name }}</span>
                    <div v-if="config.inventoryVisibility.visibleCategories.includes(category.id)" class="bg-[#1a73e8] dark:bg-[#8ab4f8] rounded-full p-0.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-2 w-2 text-white dark:text-[#1e1f20]" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                  </button>
                </div>
              </div>
            </section>

            <section>
              <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-5 border border-[#e8eaed] dark:border-[#3a3a3f] flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Ocultar productos sin stock</h3>
                  <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">No mostrar productos agotados en el catálogo</p>
                </div>
                <button 
                  @click="config.inventoryVisibility.hideOutOfStock = !config.inventoryVisibility.hideOutOfStock"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                  :class="config.inventoryVisibility.hideOutOfStock ? 'bg-[#1a73e8]' : 'bg-[#dadce0] dark:bg-[#5f6368]'"
                >
                  <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                    :class="config.inventoryVisibility.hideOutOfStock ? 'translate-x-6' : 'translate-x-1'"
                  />
                </button>
              </div>
            </section>
            
          </div>

          <!-- SECCIÓN: WHATSAPP - Estilo Gemini -->
          <div v-else-if="activeTab === 'orders'" class="space-y-6 animate-fade-in pb-8">
            
            <section>
              <div class="mb-4">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Configuración de Pedidos</h3>
                <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Define cómo tus clientes realizarán pedidos a través de WhatsApp.</p>
              </div>
              
              <!-- Grid 2 Columnas: Número + País/Horario - Gemini -->
              <div class="grid grid-cols-2 gap-6">
                
                <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-4 border border-[#e8eaed] dark:border-[#3a3a3f]">
                  <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6] mb-2">Número de WhatsApp</label>
                  <input 
                    type="text" 
                    v-model="config.ordersConfig.whatsappNumber"
                    placeholder="+57 300 123 4567"
                    class="w-full h-10 px-3 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] text-sm text-[#1e1f20] dark:text-[#e3e3e3] placeholder-[#5f6368] dark:placeholder-[#9aa0a6] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent"
                  />
                  <p class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6] mt-2">Es el número donde recibirás los pedidos.</p>
                </div>

                <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-4 border border-[#e8eaed] dark:border-[#3a3a3f] opacity-60">
                  <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6] mb-2">Horario de Atención</label>
                  <input 
                    type="text" 
                    placeholder="Lun-Vie: 9AM - 6PM"
                    class="w-full h-10 px-3 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] text-sm text-[#1e1f20] dark:text-[#e3e3e3] placeholder-[#5f6368] dark:placeholder-[#9aa0a6]"
                    disabled
                  />
                  <p class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6] mt-2">Muestra tu disponibilidad (próximamente).</p>
                </div>
                
              </div>

              <!-- Mensaje Inicial - Full Width - Gemini -->
              <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-4 border border-[#e8eaed] dark:border-[#3a3a3f] mt-6">
                <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6] mb-2">Mensaje Inicial Personalizado</label>
                <textarea 
                  v-model="config.ordersConfig.customMessage"
                  rows="3"
                  placeholder="Hola, quiero hacer el siguiente pedido:"
                  class="w-full px-3 py-2.5 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] text-sm text-[#1e1f20] dark:text-[#e3e3e3] placeholder-[#5f6368] dark:placeholder-[#9aa0a6] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent resize-none"
                ></textarea>
                <p class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6] mt-2">Este mensaje aparecerá automáticamente al iniciar la conversación.</p>
              </div>
              
            </section>
            
          </div>

          <!-- SECCIÓN: REGLAS - Estilo Gemini -->
          <div v-else-if="activeTab === 'rules'" class="space-y-6 animate-fade-in pb-8">
            
            <section>
              <div class="mb-4">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Reglas de Negocio</h3>
                <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Define los parámetros operativos de tu tienda online.</p>
              </div>
              
              <!-- Grid 2 Columnas: Costo + Mínimo - Gemini -->
              <div class="grid grid-cols-2 gap-6">
                
                <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-4 border border-[#e8eaed] dark:border-[#3a3a3f]">
                  <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6] mb-2">Costo de Domicilio</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#5f6368] dark:text-[#9aa0a6] text-sm font-medium">$</span>
                    <input 
                      type="number" 
                      v-model="config.businessRules.deliveryCost"
                      class="w-full h-10 pl-7 pr-3 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent"
                      placeholder="0"
                    />
                  </div>
                  <p class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6] mt-2">Precio del envío a domicilio.</p>
                </div>

                <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-4 border border-[#e8eaed] dark:border-[#3a3a3f]">
                  <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6] mb-2">Pedido Mínimo</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#5f6368] dark:text-[#9aa0a6] text-sm font-medium">$</span>
                    <input 
                      type="number" 
                      v-model="config.businessRules.minimumOrder"
                      class="w-full h-10 pl-7 pr-3 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent"
                      placeholder="0"
                    />
                  </div>
                  <p class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6] mt-2">Valor mínimo para aceptar pedidos.</p>
                </div>
                
              </div>
            </section>

            <section>
              <div class="mb-4">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Integraciones</h3>
                <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Conecta tu tienda con otros sistemas.</p>
              </div>
              
              <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-5 border border-[#e8eaed] dark:border-[#3a3a3f] flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Sincronizar con Caja Registradora</h3>
                  <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Registrar pedidos online automáticamente en el POS</p>
                </div>
                <button 
                  @click="config.businessRules.syncWithCashRegister = !config.businessRules.syncWithCashRegister"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                  :class="config.businessRules.syncWithCashRegister ? 'bg-[#1a73e8]' : 'bg-[#dadce0] dark:bg-[#5f6368]'"
                >
                  <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                    :class="config.businessRules.syncWithCashRegister ? 'translate-x-6' : 'translate-x-1'"
                  />
                </button>
              </div>
            </section>
            
          </div>
      
      </div> <!-- Cierra p-8 space-y-6 max-w-5xl mx-auto -->
    </main>
    
    <!-- PREVIEW DERECHO - Solo Vista Móvil (Sticky) - Gemini -->
    <aside class="flex-shrink-0 w-[480px] bg-white dark:bg-[#1e1f20] border-l border-[#e8eaed] dark:border-[#3a3a3f] flex flex-col overflow-hidden">
      <div class="h-full overflow-y-auto py-8 px-8">
        <div class="sticky top-0">
          <!-- Preview Header - Gemini -->
          <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-[#1e8e3e] animate-pulse"></span>
              <h3 class="text-[#1e1f20] dark:text-[#e3e3e3] font-medium text-sm">Vista Previa</h3>
            </div>
            
            <button 
              @click="refreshPreview"
              class="text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#1e1f20] dark:hover:text-[#e3e3e3] transition-colors p-2 rounded-full hover:bg-[#f0f4f9] dark:hover:bg-[#282a2c]"
              title="Recargar Vista"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
          </div>
        
          <!-- Marco de Dispositivo Móvil con Iframe Real -->
          <div class="flex items-center justify-center">
            <div 
              class="relative bg-white transition-all duration-300 overflow-hidden isolate w-[375px] h-[740px] rounded-[3rem] shadow-2xl dark:shadow-black/80"
              style="container-type: inline-size; width: 375px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(0, 0, 0, 0.1);"
            >
              <!-- Borde exterior del teléfono (marco negro) -->
              <div class="absolute inset-0 rounded-[3rem] border-[14px] border-black pointer-events-none z-50">
                <!-- Notch -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-40 h-7 bg-black rounded-b-3xl -mt-[14px]"></div>
                
                <!-- Botones laterales -->
                <div class="absolute -left-[14px] top-24 w-1 h-12 bg-black rounded-l"></div>
                <div class="absolute -left-[14px] top-40 w-1 h-16 bg-black rounded-l"></div>
                <div class="absolute -right-[14px] top-32 w-1 h-20 bg-black rounded-r"></div>
              </div>
              
              <!-- Pantalla del teléfono - Iframe Real del Catálogo -->
              <div class="w-full h-full overflow-hidden bg-white relative rounded-[2.2rem]" style="isolation: isolate; transform: translateZ(0);">
                <iframe 
                  :src="catalogUrl"
                  :key="previewKey"
                  class="w-full h-full border-0"
                  style="width: 375px; height: 740px;"
                  title="Vista Previa del Catálogo"
                ></iframe>
              </div>
            </div>
          </div>
        </div>
      </div>
    </aside>

  </div> <!-- Cierra flex min-h-screen -->
  
  <!-- Toast de Éxito -->
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-4"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-4"
  >
    <div 
      v-if="showSuccessToast" 
      class="fixed bottom-6 right-6 bg-[#1e8e3e] text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 z-50 border border-[#1e8e3e]/50"
    >
      <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <div>
        <h4 class="font-medium text-sm">{{ toastMessage.title }}</h4>
        <p class="text-xs opacity-90 mt-0.5">{{ toastMessage.description }}</p>
      </div>
      <button @click="showSuccessToast = false" class="ml-2 text-white/70 hover:text-white transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </Transition>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch, nextTick } from 'vue'
import { appStore } from '../store/appStore.js'
import apiClient from '../services/apiClient.js'
import axios from 'axios'
import { useToast } from '../composables/useToast.js'

const { showError, showWarning } = useToast()

// Props & Emits
const props = defineProps({
  moduleName: {
    type: String,
    default: ''
  },
  queryParams: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['navigate', 'changeModule', 'openQuotationInPos', 'refresh'])

// Refs
const logoInput = ref(null)
const bannerInput = ref(null)
const previewKey = ref(0)

// Catalog URL - Se construye dinámicamente según el entorno
const catalogUrl = computed(() => {
  // En desarrollo usa la ruta relativa /catalog
  return `${window.location.origin}/catalog`
})

// State
const isSaving = ref(false)
const showSuccessToast = ref(false)
const toastMessage = reactive({
  title: '¡Guardado!',
  description: 'Vista previa actualizada.'
})
const isLoading = ref(true)
const activeTab = ref('ai-brand')
const showWarningMessage = ref(false) // Control independiente del mensaje de alerta
const warningType = ref('categories') // 'categories' | 'whatsapp' - tipo de advertencia

// AI Brand State
const aiBrandDescription = ref('')
const isGeneratingBrand = ref(false)
const aiBrandData = ref(null)
const isRecordingVoice = ref(false)
const voiceRecognition = ref(null)
const aiGenerationProgress = ref('')

// Helper: Verificar si el número de WhatsApp es válido (más de solo el código de país)
const isValidWhatsappNumber = (number) => {
  if (!number) return false
  // Remover espacios y caracteres especiales
  const cleanNumber = number.replace(/[\s\-\(\)]/g, '')
  // Debe tener más de 4 caracteres (mínimo código país + algunos dígitos)
  // +57 solo tiene 3 caracteres, necesitamos al menos el código + número real
  return cleanNumber.length >= 10
}

// Tabs Configuration
const tabs = [
  { 
    id: 'ai-brand', 
    label: 'IA Marca', 
    icon: 'sparkles',
    description: 'Genera tu identidad de marca con Inteligencia Artificial'
  },
  { 
    id: 'identity', 
    label: 'Diseño', 
    icon: 'palette',
    description: 'Define la identidad visual de tu catálogo online'
  },
  { 
    id: 'catalog', 
    label: 'Catálogo', 
    icon: 'box',
    description: 'Controla la visibilidad de categorías y productos'
  },
  { 
    id: 'orders', 
    label: 'Pedidos', 
    icon: 'message',
    description: 'Configura el sistema de pedidos por WhatsApp'
  },
  { 
    id: 'rules', 
    label: 'Reglas', 
    icon: 'settings',
    description: 'Establece las reglas de negocio y integraciones'
  }
]

const currentTabLabel = computed(() => {
  const tab = tabs.find(t => t.id === activeTab.value)
  return tab ? tab.label : ''
})

const currentTabDescription = computed(() => {
  const tab = tabs.find(t => t.id === activeTab.value)
  return tab ? tab.description : ''
})

// Detectar si la tienda es tipo Fashion (para mostrar/ocultar plantillas y banner)
const isFashionStore = computed(() => {
  // Primero intentar desde appStore.systemSettings
  if (appStore.systemSettings?.store_type) {
    return appStore.systemSettings.store_type === 'fashion'
  }
  
  // Si no está en systemSettings, buscar en localStorage
  const storedType = localStorage.getItem('pending_store_type')
  return storedType === 'fashion'
})

// Función helper para obtener plantilla válida según tipo de tienda
const getValidTemplate = (template) => {
  // Si es fashion, puede usar cualquier plantilla
  if (isFashionStore.value) {
    return template || 'speed-market'
  }
  // Si NO es fashion, NUNCA puede ser visual-story
  if (template === 'visual-story') {
    console.warn('Plantilla "visual-story" no disponible para tiendas no-fashion. Usando "speed-market"')
    return 'speed-market'
  }
  return template || 'speed-market'
}

// Configuration Object (Reactive)
const config = reactive({
  storeActive: false, // Por defecto inactivo hasta que seleccionen categorías
  brandIdentity: {
    logo: '', 
    banner: '',
    primaryColor: '#10B981', 
    template: getValidTemplate('speed-market') // Plantilla por defecto validada
  },
  inventoryVisibility: {
    visibleCategories: [], 
    hideOutOfStock: false
  },
  ordersConfig: {
    whatsappNumber: '+57',
    customMessage: 'Hola, quiero hacer el siguiente pedido:'
  },
  businessRules: {
    deliveryCost: 0,
    minimumOrder: 0,
    syncWithCashRegister: false
  }
})

// Mock categories (will be loaded from appStore)
const availableCategories = ref([])

// Load categories from store
onMounted(async () => {
  isLoading.value = true
  
  // Cargar categorías desde la API con autenticación
  try {
    const response = await apiClient.get('/categories-pos')
    
    // La API devuelve {success: true, data: Array, message: '...'}
    const categoriesData = response.data?.data || response.data
    
    if (categoriesData && Array.isArray(categoriesData)) {
      availableCategories.value = categoriesData.map(cat => ({
        id: cat.id,
        name: cat.name
      }))
    } else {
      console.warn('No se encontraron categorías en la respuesta')
      availableCategories.value = []
    }
  } catch (error) {
    console.error('Error cargando categorías:', error)
    // Fallback: usar categorías del appStore si falló la API
    if (appStore.categories && appStore.categories.length > 0) {
      availableCategories.value = appStore.categories
    } else {
      availableCategories.value = []
    }
  }

  // Load existing configuration from backend
  await loadConfiguration()
  
  // Load AI brand data
  await loadAiBrandData()
  
  // Validación final: Asegurar que la plantilla sea válida
  config.brandIdentity.template = getValidTemplate(config.brandIdentity.template)
  
  isLoading.value = false
})

// Validación: Desactivar catálogo automáticamente si no hay categorías O número WhatsApp
watch(() => config.storeActive, async (newValue) => {
  if (newValue) {
    // Verificar categorías
    if (config.inventoryVisibility.visibleCategories.length === 0) {
      console.warn('Intento de activar catálogo sin categorías seleccionadas')
      
      // Desactivar INMEDIATAMENTE el toggle
      config.storeActive = false
      
      // Esperar a que se actualice el DOM
      await nextTick()
      
      // Guardar automáticamente el cambio en el backend
      await saveConfiguration()
      // Mostrar mensaje de alerta por 40 segundos
      warningType.value = 'categories'
      showWarningMessage.value = true
      setTimeout(() => {
        showWarningMessage.value = false
      }, 40000)
      return
    }
    
    // Verificar número de WhatsApp
    if (!isValidWhatsappNumber(config.ordersConfig.whatsappNumber)) {
      console.warn('Intento de activar catálogo sin número de WhatsApp válido')
      
      // Desactivar INMEDIATAMENTE el toggle
      config.storeActive = false
      
      // Esperar a que se actualice el DOM
      await nextTick()
      
      // Guardar automáticamente el cambio en el backend
      await saveConfiguration()
      // Mostrar mensaje de alerta por 40 segundos
      warningType.value = 'whatsapp'
      showWarningMessage.value = true
      setTimeout(() => {
        showWarningMessage.value = false
      }, 40000)
      return
    }
    
    // Si llegó aquí, el cambio es válido - Guardar automáticamente
    await nextTick()
    await saveConfiguration()
  } else if (!newValue) {
    // Usuario desactivó la tienda manualmente - Guardar automáticamente
    await nextTick()
    await saveConfiguration()
  }
})

// Validación inteligente al cambiar categorías
watch(() => config.inventoryVisibility.visibleCategories.length, async (newLength, oldLength) => {
  // 1️⃣ Si se selecciona al menos una categoría, cerrar mensaje de alerta inmediatamente
  if (newLength > 0 && showWarningMessage.value) {
    showWarningMessage.value = false
  }
  
  // 2️⃣ Si se quitan TODAS las categorías y el catálogo está activo, desactivarlo automáticamente
  if (config.storeActive && newLength === 0 && oldLength > 0) {
    console.warn('Se quitaron todas las categorías - Desactivando catálogo automáticamente')
    
    // Desactivar el toggle
    config.storeActive = false
    
    // Esperar a que se actualice el DOM
    await nextTick()
    
    // Guardar automáticamente el cambio en el backend
    await saveConfiguration()
    // Mostrar mensaje de alerta por 40 segundos
    warningType.value = 'categories'
    showWarningMessage.value = true
    setTimeout(() => {
      showWarningMessage.value = false
    }, 40000)
  }
})

// Validación del número de WhatsApp
watch(() => config.ordersConfig.whatsappNumber, (newValue) => {
  // Si se ingresa un número válido, cerrar mensaje de alerta si era de WhatsApp
  if (isValidWhatsappNumber(newValue) && showWarningMessage.value && warningType.value === 'whatsapp') {
    showWarningMessage.value = false
  }
})

// Refresh Preview - Recarga el iframe completamente
const refreshPreview = () => {
  previewKey.value++
  // Forzar recarga completa del iframe con timestamp para evitar caché
  const iframe = document.querySelector('iframe')
  if (iframe) {
    const currentSrc = iframe.src.split('?')[0] // Remover query params anteriores
    iframe.src = `${currentSrc}?t=${Date.now()}` // Agregar timestamp único
  }
}

// Open catalog in new window (always mobile view)
const openCatalogInNewWindow = () => {
  const catalogUrl = `${window.location.origin}/catalog`
  const windowFeatures = 'width=414,height=896,left=100,top=100'
  window.open(catalogUrl, 'CatalogPreview', windowFeatures)
}

// Copy store link to clipboard
const copyStoreLink = async () => {
  const catalogUrl = `${window.location.origin}/catalog`
  try {
    await navigator.clipboard.writeText(catalogUrl)
    
    // Mostrar toast de éxito
    toastMessage.title = '¡Enlace copiado!'
    toastMessage.description = 'El enlace se copió al portapapeles.'
    showSuccessToast.value = true
    setTimeout(() => {
      showSuccessToast.value = false
    }, 3000)
    
  } catch (error) {
    console.error('Error al copiar enlace:', error)
    // Fallback para navegadores antiguos
    const textArea = document.createElement('textarea')
    textArea.value = catalogUrl
    textArea.style.position = 'fixed'
    textArea.style.left = '-999999px'
    document.body.appendChild(textArea)
    textArea.select()
    try {
      document.execCommand('copy')
      toastMessage.title = '¡Enlace copiado!'
      toastMessage.description = 'El enlace se copió al portapapeles.'
      showSuccessToast.value = true
      setTimeout(() => {
        showSuccessToast.value = false
      }, 3000)
    } catch (err) {
      console.error('Error en fallback de copia:', err)
      showError('No se pudo copiar el enlace. Por favor, cópialo manualmente.')
    }
    document.body.removeChild(textArea)
  }
}

// Toggle category selection
const toggleCategory = (categoryId) => {
  const index = config.inventoryVisibility.visibleCategories.indexOf(categoryId)
  if (index > -1) {
    config.inventoryVisibility.visibleCategories.splice(index, 1)
  } else {
    config.inventoryVisibility.visibleCategories.push(categoryId)
  }
}

// Trigger file upload
const triggerFileUpload = (type) => {
  if (type === 'logo') {
    logoInput.value.click()
  } else if (type === 'banner') {
    bannerInput.value.click()
  }
}

// Handle file upload
const handleFileUpload = (event, type) => {
  const file = event.target.files[0]
  if (!file) return


  if (file.size > 2 * 1024 * 1024) {
    showWarning('El archivo es muy grande. Máximo 2MB.')
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    const base64String = e.target.result
    
    if (type === 'logo') {
      config.brandIdentity.logo = base64String
    } else if (type === 'banner') {
      config.brandIdentity.banner = base64String
    }
  }
  reader.readAsDataURL(file)
}

// Load configuration from backend
const loadConfiguration = async () => {
  try {
    const response = await apiClient.get('/web-catalog/config')
    
    if (response.data.success) {
      const data = response.data.data
      
      // Por defecto inactivo si no está explícitamente configurado
      config.storeActive = data.store_active ?? false
      config.brandIdentity.logo = data.logo_url || ''
      config.brandIdentity.banner = data.banner_url || ''
      config.brandIdentity.primaryColor = data.primary_color || '#10B981'
      // Validar plantilla usando helper
      const loadedTemplate = data.template || 'speed-market'
      config.brandIdentity.template = getValidTemplate(loadedTemplate)
      
      const visibleCats = Array.isArray(data.visible_categories) ? data.visible_categories : []
      
      // Respetar la configuración guardada, incluso si está vacía
      config.inventoryVisibility.visibleCategories = visibleCats
      
      // REGLA: Si no hay categorías seleccionadas, FORZAR catálogo inactivo
      if (visibleCats.length === 0) {
        console.warn('No hay categorías seleccionadas - Forzando catálogo inactivo')
        config.storeActive = false
      }
      
      config.inventoryVisibility.hideOutOfStock = data.hide_out_of_stock ?? false
      config.ordersConfig.whatsappNumber = data.whatsapp_number || '+57'
      config.ordersConfig.customMessage = data.custom_message || 'Hola, quiero hacer el siguiente pedido:'
      config.businessRules.deliveryCost = parseFloat(data.delivery_cost || 0)
      config.businessRules.minimumOrder = parseFloat(data.minimum_order || 0)
      config.businessRules.syncWithCashRegister = data.sync_with_cash_register ?? false
    }
  } catch (error) {
    console.error('Error loading configuration:', error)
    // No forzar selección de todas las categorías en caso de error
    // config.inventoryVisibility.visibleCategories permanecerá vacío
  }
}

// Save configuration to backend
const saveConfiguration = async () => {
  isSaving.value = true
  
  try {
    // Validar plantilla antes de guardar
    const validTemplate = getValidTemplate(config.brandIdentity.template)
    if (validTemplate !== config.brandIdentity.template) {
      config.brandIdentity.template = validTemplate
    }
    
    // Transformar estructura del frontend al formato que espera el backend
    const payload = {
      storeActive: config.storeActive,
      brandIdentity: {
        logo: config.brandIdentity.logo,
        banner: config.brandIdentity.banner,
        primaryColor: config.brandIdentity.primaryColor,
        template: config.brandIdentity.template
      },
      products: {
        visibleCategories: config.inventoryVisibility.visibleCategories, // ← MAPEO CORRECTO
        showPrices: true, // Por ahora hardcodeado
        hideOutOfStock: config.inventoryVisibility.hideOutOfStock
      },
      orders: {
        allowOrders: true, // Por ahora hardcodeado
        whatsappNumber: config.ordersConfig.whatsappNumber,
        customMessage: config.ordersConfig.customMessage
      },
      businessRules: {
        deliveryCost: config.businessRules.deliveryCost,
        minimumOrder: config.businessRules.minimumOrder,
        syncWithCashRegister: config.businessRules.syncWithCashRegister
      }
    }
    
    
    const response = await apiClient.post('/web-catalog/config', payload)
    
    if (response.data.success) {
      toastMessage.title = '¡Guardado!'
      toastMessage.description = 'Configuración actualizada. Vista previa recargada.'
      showSuccessToast.value = true
      setTimeout(() => {
        showSuccessToast.value = false
      }, 3000)
      
      // Recargar iframe automáticamente después de guardar
      refreshPreview()
    }
  } catch (error) {
    console.error('Error saving configuration:', error)
    showError('Error al guardar la configuración.')
  } finally {
    isSaving.value = false
  }
}

// ==================== AI BRAND METHODS ====================

// Load existing AI brand data
const loadAiBrandData = async () => {
  try {
    const response = await apiClient.get('/web-catalog/ai-brand/data')
    if (response.data.success && response.data.data) {
      aiBrandData.value = response.data.data
      if (response.data.data.business_description) {
        aiBrandDescription.value = response.data.data.business_description
      }
    }
  } catch (error) {
    // Silent fail - AI data is optional
  }
}

// Generate brand identity with Groq AI
const generateAiBrand = async () => {
  if (!aiBrandDescription.value || aiBrandDescription.value.trim().length < 10) {
    toastMessage.title = 'Descripción muy corta'
    toastMessage.description = 'Escribe al menos 10 caracteres describiendo tu negocio.'
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 4000)
    return
  }

  isGeneratingBrand.value = true
  aiGenerationProgress.value = 'Analizando tu negocio...'

  try {
    // Progress updates
    setTimeout(() => { if (isGeneratingBrand.value) aiGenerationProgress.value = 'Generando paleta de colores...' }, 2000)
    setTimeout(() => { if (isGeneratingBrand.value) aiGenerationProgress.value = 'Seleccionando tipografías...' }, 4000)
    setTimeout(() => { if (isGeneratingBrand.value) aiGenerationProgress.value = 'Redactando textos persuasivos...' }, 6000)
    setTimeout(() => { if (isGeneratingBrand.value) aiGenerationProgress.value = 'Creando identidad de marca...' }, 8000)

    const response = await apiClient.post('/web-catalog/ai-brand/generate', {
      business_description: aiBrandDescription.value.trim()
    })

    if (response.data.success) {
      aiBrandData.value = {
        ...response.data.data,
        business_description: aiBrandDescription.value,
        generated_at: new Date().toISOString()
      }

      // Aplicar automáticamente color + plantilla recomendada en backend
      await apiClient.post('/web-catalog/ai-brand/apply', {
        apply_colors: true,
        apply_template: true
      })

      // Reflejar en estado local para vista previa inmediata
      if (response.data.data?.color_palette?.primary) {
        config.brandIdentity.primaryColor = response.data.data.color_palette.primary
      }
      if (response.data.data?.recommended_template) {
        config.brandIdentity.template = getValidTemplate(response.data.data.recommended_template)
      }

      // Recargar configuración y preview para ver cambios de IA en tiempo real
      await loadConfiguration()
      refreshPreview()

      toastMessage.title = 'Identidad generada'
      toastMessage.description = 'IA aplicada: plantilla, color, fuentes y textos actualizados.'
      showSuccessToast.value = true
      setTimeout(() => showSuccessToast.value = false, 4000)

      aiGenerationProgress.value = ''
    } else {
      toastMessage.title = 'Error'
      toastMessage.description = response.data.message || 'No se pudo generar la identidad.'
      showSuccessToast.value = true
      setTimeout(() => showSuccessToast.value = false, 5000)
    }
  } catch (error) {
    toastMessage.title = 'Error de conexión'
    toastMessage.description = 'No se pudo conectar con el servicio de IA.'
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 5000)
  } finally {
    isGeneratingBrand.value = false
    aiGenerationProgress.value = ''
  }
}

// Apply AI colors to catalog config
const applyAiColors = () => {
  if (aiBrandData.value?.color_palette?.primary) {
    config.brandIdentity.primaryColor = aiBrandData.value.color_palette.primary
    toastMessage.title = 'Color aplicado'
    toastMessage.description = 'El color primario de IA se aplicó a tu tienda.'
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 3000)
  }
}

// Apply AI recommended template
const applyAiTemplate = () => {
  if (aiBrandData.value?.recommended_template) {
    const template = aiBrandData.value.recommended_template
    const validTemplate = getValidTemplate(template)
    config.brandIdentity.template = validTemplate
    toastMessage.title = 'Plantilla aplicada'
    toastMessage.description = `Se seleccionó "${validTemplate === 'visual-story' ? 'Historia Visual' : validTemplate === 'speed-market' ? 'Mercado Rápido' : 'Cuadrícula Moderna'}".`
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 3000)
  }
}

// Apply ALL AI settings at once
const applyAllAiSettings = async () => {
  if (!aiBrandData.value) return

  applyAiColors()
  applyAiTemplate()

  // Save config after applying
  await saveConfiguration()
}

// Voice-to-Text (Web Speech API)
const startVoiceRecording = () => {
  if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
    toastMessage.title = 'No soportado'
    toastMessage.description = 'Tu navegador no soporta reconocimiento de voz. Usa Chrome.'
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 4000)
    return
  }

  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
  voiceRecognition.value = new SpeechRecognition()
  voiceRecognition.value.lang = 'es-CO'
  voiceRecognition.value.continuous = true
  voiceRecognition.value.interimResults = true

  voiceRecognition.value.onstart = () => {
    isRecordingVoice.value = true
  }

  voiceRecognition.value.onresult = (event) => {
    let transcript = ''
    for (let i = 0; i < event.results.length; i++) {
      transcript += event.results[i][0].transcript
    }
    aiBrandDescription.value = transcript
  }

  voiceRecognition.value.onerror = () => {
    isRecordingVoice.value = false
  }

  voiceRecognition.value.onend = () => {
    isRecordingVoice.value = false
  }

  voiceRecognition.value.start()
}

const stopVoiceRecording = () => {
  if (voiceRecognition.value) {
    voiceRecognition.value.stop()
    isRecordingVoice.value = false
  }
}
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fade-in 0.3s ease-out forwards;
}

@keyframes slide-up {
  from { transform: translateY(100%); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.animate-slide-up {
  animation: slide-up 0.3s ease-out forwards;
}

/* Simular viewport móvil real */
.mobile-preview-viewport {
  width: 375px !important;
  max-width: 375px !important;
}

/* Forzar estilos móviles en la preview */
.mobile-preview-viewport * {
  max-width: 100%;
}
</style>
