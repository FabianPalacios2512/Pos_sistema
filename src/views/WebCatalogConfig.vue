<template>
  <div>
  <!-- Layout de 3 Columnas: Menú Lateral + Contenido + Preview -->
  <div class="flex overflow-hidden bg-gray-50 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c]" style="height: 100%;">
    
    <!-- SIDEBAR IZQUIERDO - Menú de Navegación -->
    <aside class="w-64 bg-white/95 dark:bg-zinc-900/95  border-r border-gray-200/80 dark:border-zinc-800/50 flex flex-col shadow-lg shadow-gray-200/50 dark:shadow-black/30" style="min-height: 0;">
      <!-- Header Sidebar - Compacto -->
      <div class="px-4 py-3 border-b border-gray-100/80 dark:border-zinc-800/50 flex-shrink-0">
        <div class="flex items-center gap-2 mb-3">
          <div class="w-8 h-8 bg-slate-900 dark:bg-slate-700 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </div>
          <div>
            <h2 class="text-xs font-bold text-gray-900 dark:text-white">Configuración</h2>
            <p class="text-[10px] text-gray-500 dark:text-zinc-500">Catálogo Web</p>
          </div>
        </div>
        
        <!-- Estado de la Tienda - Compacto -->
        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-zinc-800/50 rounded-lg">
          <span class="text-[10px] font-semibold text-gray-700 dark:text-zinc-300">Estado:</span>
          <div class="flex items-center gap-1.5">
            <button 
              @click="config.storeActive = !config.storeActive"
              class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none"
              :class="config.storeActive ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-zinc-600'"
            >
              <span
                class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform shadow-sm"
                :class="config.storeActive ? 'translate-x-5' : 'translate-x-0.5'"
              />
            </button>
            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md" 
                  :class="config.storeActive 
                    ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400' 
                    : 'bg-gray-200 dark:bg-zinc-700 text-gray-600 dark:text-zinc-400'">
              {{ config.storeActive ? 'Activa' : 'Inactiva' }}
            </span>
          </div>
        </div>
      </div>
      
      <!-- Navegación - Compacta -->
      <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          class="w-full text-left px-2.5 py-2 rounded-lg transition-all duration-150 flex items-center gap-2.5 group"
          :class="activeTab === tab.id 
            ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400' 
            : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800/50'"
        >
          <div class="w-4 h-4 flex items-center justify-center flex-shrink-0">
            <svg v-if="tab.icon === 'palette'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-zinc-500'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
            </svg>
            <svg v-else-if="tab.icon === 'box'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-zinc-500'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <svg v-else-if="tab.icon === 'message'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-zinc-500'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <svg v-else-if="tab.icon === 'settings'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-zinc-500'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <span class="text-xs font-medium">{{ tab.label }}</span>
        </button>
      </nav>
      
      <!-- Footer Sidebar con botones de acción - Compacto -->
      <div class="px-3 pb-3 border-t border-gray-100/80 dark:border-zinc-800/50 pt-3 space-y-1.5 flex-shrink-0 bg-gray-50/50 dark:bg-zinc-800/30">
        <button 
          @click="saveConfiguration"
          :disabled="isSaving"
          class="w-full px-2.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-all duration-150 flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
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
          class="w-full px-2.5 py-1.5 bg-white/80 dark:bg-zinc-800/80 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 text-xs font-medium rounded-lg transition-all duration-150 flex items-center justify-center gap-1.5 shadow-sm hover:shadow">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
          </svg>
          Copiar Enlace
        </button>
        
        <button 
          @click="openCatalogInNewWindow"
          class="w-full px-2.5 py-1.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-xs font-semibold rounded-lg transition-all duration-150 flex items-center justify-center gap-1.5"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
          Ver Página
        </button>
      </div>
    </aside>
    
    <!-- CONTENIDO CENTRAL -->
    <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-transparent">
      <div class="p-8 space-y-6 max-w-5xl mx-auto">
        
        <!-- ⚠️ Barra de Advertencia - Configuración Incompleta -->
        <div v-if="showWarningMessage" 
             class="bg-amber-50 dark:bg-amber-950/30 border-l-4 border-amber-500 rounded-xl p-4 flex items-start gap-3 shadow-sm animate-fade-in">
          <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <div class="flex-1">
            <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300">Configuración Incompleta</h4>
            <p v-if="warningType === 'categories'" class="text-xs text-amber-700 dark:text-amber-400 mt-1">
              No puedes activar el catálogo sin seleccionar ninguna categoría para mostrar. 
              Ve a la pestaña <span class="font-semibold">"Catálogo"</span> y selecciona al menos una categoría.
            </p>
            <p v-else-if="warningType === 'whatsapp'" class="text-xs text-amber-700 dark:text-amber-400 mt-1">
              No puedes activar el catálogo sin configurar un número de WhatsApp válido. 
              Ve a la pestaña <span class="font-semibold">"Pedidos"</span> e ingresa tu número completo (ej: +573001234567).
            </p>
          </div>
          <button 
            @click="activeTab = warningType === 'categories' ? 'catalog' : 'orders'; showWarningMessage = false" 
            class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg transition-colors flex-shrink-0">
            {{ warningType === 'categories' ? 'Ir a Catálogo' : 'Ir a Pedidos' }}
          </button>
        </div>
          
          <!-- SECCIÓN: DISEÑO - Estilo SaaS Profesional -->
          <div v-if="activeTab === 'identity'" class="space-y-6 animate-fade-in">
            
            <!-- 🎨 TARJETA 1: Logo y Color Primario -->
            <div class="bg-white/90 dark:bg-zinc-900/90  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
              <div class="px-6 py-5 border-b border-gray-100/80 dark:border-zinc-800/50">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Identidad Visual</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Logo y color principal de tu tienda</p>
              </div>
              
              <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                  
                  <!-- Logo Upload -->
                  <div class="space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-zinc-300">Logo de la Tienda</label>
                    <div 
                      @click="triggerFileUpload('logo')"
                      class="relative w-full h-32 border-2 border-dashed border-gray-300 dark:border-zinc-700 rounded-xl hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50/30 dark:hover:bg-emerald-950/20 transition-all cursor-pointer group bg-gray-50 dark:bg-zinc-800/50 flex items-center justify-center"
                    >
                      <div v-if="config.brandIdentity.logo" class="absolute inset-0 p-3 flex items-center justify-center">
                        <img :src="config.brandIdentity.logo" class="max-w-full max-h-full object-contain" />
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 rounded-xl">
                          <button 
                            @click.stop="config.brandIdentity.logo = ''"
                            class="text-white text-xs font-semibold px-3 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition-colors flex items-center gap-1.5"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Eliminar
                          </button>
                          <span class="text-white text-xs font-semibold px-3 py-2 bg-white/20  rounded-lg">Cambiar</span>
                        </div>
                      </div>
                      <div v-else class="text-gray-400 dark:text-zinc-500 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors flex flex-col items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div class="text-center">
                          <span class="text-xs font-semibold block">Subir logo</span>
                          <span class="text-[10px] text-gray-400 dark:text-zinc-500">PNG, JPG o SVG</span>
                        </div>
                      </div>
                      <input type="file" ref="logoInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'logo')" />
                    </div>
                  </div>
                  
                  <!-- Color Picker -->
                  <div class="space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-zinc-300">Color Primario</label>
                    <div class="flex items-center gap-3 p-4 border-2 border-gray-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800/50 hover:border-emerald-500 dark:hover:border-emerald-500 transition-all">
                      <input 
                        type="color" 
                        v-model="config.brandIdentity.primaryColor"
                        class="w-12 h-12 rounded-lg cursor-pointer border-2 border-gray-300 dark:border-zinc-600"
                      />
                      <div class="flex-1">
                        <input 
                          type="text"
                          v-model="config.brandIdentity.primaryColor"
                          class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-zinc-700 text-sm font-mono text-gray-900 dark:text-zinc-200 bg-gray-50 dark:bg-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 uppercase"
                          placeholder="#10B981"
                        />
                        <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1.5">Este color se aplicará en botones y acentos</p>
                      </div>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
            
            <!-- 🎨 TARJETA 2: Selección de Plantilla -->
            <div class="bg-white/90 dark:bg-zinc-900/90  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
              <div class="px-6 py-5 border-b border-gray-100/80 dark:border-zinc-800/50">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Plantilla de Diseño</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Elige el estilo visual de tu catálogo web</p>
              </div>
              
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  
                  <!-- Visual Story (Solo para Fashion) -->
                  <button 
                    v-if="isFashionStore"
                    @click="config.brandIdentity.template = 'visual-story'"
                    class="group relative p-5 rounded-xl border-2 transition-all text-left hover:shadow-md"
                    :class="config.brandIdentity.template === 'visual-story' 
                      ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30 shadow-md' 
                      : 'border-gray-200 dark:border-zinc-700 hover:border-emerald-300 dark:hover:border-emerald-700 bg-white dark:bg-zinc-800/50'"
                  >
                    <div class="flex flex-col items-center text-center space-y-3">
                      <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/50 dark:to-pink-900/50 flex items-center justify-center border border-purple-200 dark:border-purple-800/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-bold" :class="config.brandIdentity.template === 'visual-story' ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-900 dark:text-white'">
                          Historia Visual
                        </div>
                        <div class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Boutique / Gourmet</div>
                      </div>
                      <div v-if="config.brandIdentity.template === 'visual-story'" class="absolute top-3 right-3 w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </button>

                  <!-- Speed Market -->
                  <button 
                    @click="config.brandIdentity.template = 'speed-market'"
                    class="group relative p-5 rounded-xl border-2 transition-all text-left hover:shadow-md"
                    :class="config.brandIdentity.template === 'speed-market' 
                      ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30 shadow-md' 
                      : 'border-gray-200 dark:border-zinc-700 hover:border-emerald-300 dark:hover:border-emerald-700 bg-white dark:bg-zinc-800/50'"
                  >
                    <div class="flex flex-col items-center text-center space-y-3">
                      <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-100 to-cyan-100 dark:from-blue-900/50 dark:to-cyan-900/50 flex items-center justify-center border border-blue-200 dark:border-blue-800/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-bold" :class="config.brandIdentity.template === 'speed-market' ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-900 dark:text-white'">
                          Mercado Rápido
                        </div>
                        <div class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Supermercado / Rápido</div>
                      </div>
                      <div v-if="config.brandIdentity.template === 'speed-market'" class="absolute top-3 right-3 w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </button>

                  <!-- Modern Grid -->
                  <button 
                    @click="config.brandIdentity.template = 'modern-grid'"
                    class="group relative p-5 rounded-xl border-2 transition-all text-left hover:shadow-md"
                    :class="config.brandIdentity.template === 'modern-grid' 
                      ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30 shadow-md' 
                      : 'border-gray-200 dark:border-zinc-700 hover:border-emerald-300 dark:hover:border-emerald-700 bg-white dark:bg-zinc-800/50'"
                  >
                    <div class="flex flex-col items-center text-center space-y-3">
                      <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-gray-100 to-slate-100 dark:from-gray-800/50 dark:to-slate-800/50 flex items-center justify-center border border-gray-300 dark:border-gray-700/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z" />
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-bold" :class="config.brandIdentity.template === 'modern-grid' ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-900 dark:text-white'">
                          Cuadrícula Moderna
                        </div>
                        <div class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Clásico / Versátil</div>
                      </div>
                      <div v-if="config.brandIdentity.template === 'modern-grid'" class="absolute top-3 right-3 w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </button>
                  
                </div>
              </div>
            </div>
            
            <!-- 🎨 TARJETA 3: Banner Promocional (Solo Fashion) -->
            <div v-if="isFashionStore" class="bg-white/90 dark:bg-zinc-900/90  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
              <div class="px-6 py-5 border-b border-gray-100/80 dark:border-zinc-800/50">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Banner Promocional</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Imagen destacada en la parte superior (exclusivo para tiendas de moda)</p>
              </div>
              
              <div class="p-6">
                <div 
                  @click="triggerFileUpload('banner')"
                  class="relative w-full h-48 border-2 border-dashed border-gray-300 dark:border-zinc-700 rounded-xl hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50/30 dark:hover:bg-emerald-950/20 transition-all cursor-pointer group bg-gray-50 dark:bg-zinc-800/50 flex items-center justify-center overflow-hidden"
                >
                  <div v-if="config.brandIdentity.banner" class="absolute inset-0 p-2">
                    <img :src="config.brandIdentity.banner" class="w-full h-full object-cover rounded-lg" />
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3 rounded-lg">
                      <button 
                        @click.stop="config.brandIdentity.banner = ''"
                        class="text-white text-sm font-semibold px-4 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg transition-colors flex items-center gap-2"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Eliminar Banner
                      </button>
                      <span class="text-white text-sm font-semibold px-4 py-2.5 bg-white/20  rounded-lg">Cambiar Imagen</span>
                    </div>
                  </div>
                  <div v-else class="text-gray-400 dark:text-zinc-500 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors flex flex-col items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div class="text-center">
                      <span class="text-sm font-semibold block">Subir banner promocional</span>
                      <span class="text-xs text-gray-400 dark:text-zinc-500 mt-1 block">Recomendado: 1200x400px</span>
                    </div>
                  </div>
                  <input type="file" ref="bannerInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'banner')" />
                </div>
              </div>
            </div>

          </div>

          <!-- SECCIÓN: PRODUCTOS - Estilo Ejecutivo -->
          <div v-else-if="activeTab === 'catalog'" class="space-y-8 animate-fade-in pb-8">
            
            <section>
              <div class="mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Visibilidad del Catálogo</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Controla qué categorías y productos se muestran en tu tienda online.</p>
              </div>
              
              <!-- ⚠️ Advertencia si no hay categorías cargadas -->
              <div v-if="availableCategories.length === 0" 
                   class="bg-blue-50 dark:bg-blue-950/30 border-l-4 border-blue-500 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm text-blue-700 dark:text-blue-300">Cargando categorías desde la base de datos...</p>
              </div>
              
              <div class="bg-white/90 dark:bg-zinc-900/90  rounded-xl p-5 shadow-lg shadow-gray-200/50 dark:shadow-black/30">
                <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-400 mb-3">Categorías Visibles</label>
                <div class="flex flex-wrap gap-2">
                  <button 
                    v-for="category in availableCategories" 
                    :key="category.id"
                    @click="toggleCategory(category.id)"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-150 flex items-center gap-2 shadow-sm"
                    :class="config.inventoryVisibility.visibleCategories.includes(category.id) 
                      ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 ring-2 ring-emerald-500/50' 
                      : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700'"
                  >
                    <span>{{ category.name }}</span>
                    <div v-if="config.inventoryVisibility.visibleCategories.includes(category.id)" class="bg-emerald-500 rounded-full p-0.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-2 w-2 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                  </button>
                </div>
              </div>
            </section>

            <section>
              <div class="bg-white/90 dark:bg-zinc-900/90  rounded-xl p-5 shadow-lg shadow-gray-200/50 dark:shadow-black/30 flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ocultar productos sin stock</h3>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">No mostrar productos agotados en el catálogo</p>
                </div>
                <button 
                  @click="config.inventoryVisibility.hideOutOfStock = !config.inventoryVisibility.hideOutOfStock"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                  :class="config.inventoryVisibility.hideOutOfStock ? 'bg-emerald-500' : 'bg-gray-200'"
                >
                  <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                    :class="config.inventoryVisibility.hideOutOfStock ? 'translate-x-6' : 'translate-x-1'"
                  />
                </button>
              </div>
            </section>
            
          </div>

          <!-- SECCIÓN: WHATSAPP - Estilo Ejecutivo con Grid -->
          <div v-else-if="activeTab === 'orders'" class="space-y-8 animate-fade-in pb-8">
            
            <section>
              <div class="mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Configuración de Pedidos</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Define cómo tus clientes realizarán pedidos a través de WhatsApp.</p>
              </div>
              
              <!-- Grid 2 Columnas: Número + País/Horario (Placeholder) -->
              <div class="grid grid-cols-2 gap-6">
                
                <div class="bg-white/90 dark:bg-zinc-900/90  rounded-xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30">
                  <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-400 mb-2">Número de WhatsApp</label>
                  <input 
                    type="text" 
                    v-model="config.ordersConfig.whatsappNumber"
                    placeholder="+57 300 123 4567"
                    class="w-full h-10 px-3 rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-sm text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                  />
                  <p class="text-[10px] text-gray-500 dark:text-zinc-500 mt-2">Es el número donde recibirás los pedidos.</p>
                </div>

                <div class="bg-white/90 dark:bg-zinc-900/90  rounded-xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 opacity-60">
                  <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-400 mb-2">Horario de Atención</label>
                  <input 
                    type="text" 
                    placeholder="Lun-Vie: 9AM - 6PM"
                    class="w-full h-10 px-3 rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-sm text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500"
                    disabled
                  />
                  <p class="text-[10px] text-gray-500 dark:text-zinc-500 mt-2">Muestra tu disponibilidad (próximamente).</p>
                </div>
                
              </div>

              <!-- Mensaje Inicial - Full Width -->
              <div class="bg-white/90 dark:bg-zinc-900/90  rounded-xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 mt-6">
                <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-400 mb-2">Mensaje Inicial Personalizado</label>
                <textarea 
                  v-model="config.ordersConfig.customMessage"
                  rows="3"
                  placeholder="Hola, quiero hacer el siguiente pedido:"
                  class="w-full px-3 py-2.5 rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-sm text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent resize-none"
                ></textarea>
                <p class="text-[10px] text-gray-500 dark:text-zinc-500 mt-2">Este mensaje aparecerá automáticamente al iniciar la conversación.</p>
              </div>
              
            </section>
            
          </div>

          <!-- SECCIÓN: REGLAS - Grid 2x2 Ejecutivo -->
          <div v-else-if="activeTab === 'rules'" class="space-y-8 animate-fade-in pb-8">
            
            <section>
              <div class="mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Reglas de Negocio</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Define los parámetros operativos de tu tienda online.</p>
              </div>
              
              <!-- Grid 2 Columnas: Costo + Mínimo -->
              <div class="grid grid-cols-2 gap-6">
                
                <div class="bg-white/90 dark:bg-zinc-900/90  rounded-xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30">
                  <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-400 mb-2">Costo de Domicilio</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 text-sm font-medium">$</span>
                    <input 
                      type="number" 
                      v-model="config.businessRules.deliveryCost"
                      class="w-full h-10 pl-7 pr-3 rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-base font-semibold text-gray-900 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                      placeholder="0"
                    />
                  </div>
                  <p class="text-[10px] text-gray-500 dark:text-zinc-500 mt-2">Precio del envío a domicilio.</p>
                </div>

                <div class="bg-white/90 dark:bg-zinc-900/90  rounded-xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30">
                  <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-400 mb-2">Pedido Mínimo</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 text-sm font-medium">$</span>
                    <input 
                      type="number" 
                      v-model="config.businessRules.minimumOrder"
                      class="w-full h-10 pl-7 pr-3 rounded-lg border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-base font-semibold text-gray-900 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                      placeholder="0"
                    />
                  </div>
                  <p class="text-[10px] text-gray-500 dark:text-zinc-500 mt-2">Valor mínimo para aceptar pedidos.</p>
                </div>
                
              </div>
            </section>

            <section>
              <div class="mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Integraciones</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Conecta tu tienda con otros sistemas.</p>
              </div>
              
              <div class="bg-white/90 dark:bg-zinc-900/90  rounded-xl p-5 shadow-lg shadow-gray-200/50 dark:shadow-black/30 flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-bold text-gray-900 dark:text-white">Sincronizar con Caja Registradora</h3>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Registrar pedidos online automáticamente en el POS</p>
                </div>
                <button 
                  @click="config.businessRules.syncWithCashRegister = !config.businessRules.syncWithCashRegister"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                  :class="config.businessRules.syncWithCashRegister ? 'bg-emerald-500' : 'bg-gray-200'"
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
    
    <!-- PREVIEW DERECHO - Solo Vista Móvil (Sticky) -->
    <aside class="flex-shrink-0 w-[480px] bg-white dark:bg-zinc-900 border-l border-gray-200 dark:border-zinc-800 flex flex-col overflow-hidden shadow-lg">
      <div class="h-full overflow-y-auto py-8 px-8">
        <div class="sticky top-0">
          <!-- Preview Header -->
          <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-lg shadow-emerald-500/50"></span>
              <h3 class="text-gray-900 dark:text-white font-bold text-sm">Vista Previa</h3>
            </div>
            
            <button 
              @click="refreshPreview"
              class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800"
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
      class="fixed bottom-6 right-6 bg-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 z-50 border border-emerald-500"
    >
      <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <div>
        <h4 class="font-bold text-sm">{{ toastMessage.title }}</h4>
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
const activeTab = ref('identity')
const showWarningMessage = ref(false) // Control independiente del mensaje de alerta
const warningType = ref('categories') // 'categories' | 'whatsapp' - tipo de advertencia

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
    console.warn('⚠️ Plantilla "visual-story" no disponible para tiendas no-fashion. Usando "speed-market"')
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
  
  // 🔄 Cargar categorías desde la API con autenticación
  try {
    console.log('📦 Cargando categorías desde la API...')
    const response = await apiClient.get('/categories-pos')
    
    // La API devuelve {success: true, data: Array, message: '...'}
    const categoriesData = response.data?.data || response.data
    
    if (categoriesData && Array.isArray(categoriesData)) {
      availableCategories.value = categoriesData.map(cat => ({
        id: cat.id,
        name: cat.name
      }))
      console.log('✅ Categorías cargadas:', availableCategories.value.length, 'categorías')
    } else {
      console.warn('⚠️ No se encontraron categorías en la respuesta')
      availableCategories.value = []
    }
  } catch (error) {
    console.error('❌ Error cargando categorías:', error)
    // Fallback: usar categorías del appStore si falló la API
    if (appStore.categories && appStore.categories.length > 0) {
      console.log('🔄 Usando categorías del appStore como fallback')
      availableCategories.value = appStore.categories
    } else {
      availableCategories.value = []
    }
  }

  // Load existing configuration from backend
  await loadConfiguration()
  
  // 🛡️ Validación final: Asegurar que la plantilla sea válida
  config.brandIdentity.template = getValidTemplate(config.brandIdentity.template)
  
  isLoading.value = false
})

// ⚠️ Validación: Desactivar catálogo automáticamente si no hay categorías O número WhatsApp
watch(() => config.storeActive, async (newValue) => {
  if (newValue) {
    // Verificar categorías
    if (config.inventoryVisibility.visibleCategories.length === 0) {
      console.warn('⚠️ Intento de activar catálogo sin categorías seleccionadas')
      
      // Desactivar INMEDIATAMENTE el toggle
      config.storeActive = false
      
      // Esperar a que se actualice el DOM
      await nextTick()
      
      // 💾 Guardar automáticamente el cambio en el backend
      console.log('💾 Guardando estado inactivo automáticamente...')
      await saveConfiguration()
      console.log('✅ Estado guardado en backend')
      
      // Mostrar mensaje de alerta por 40 segundos
      warningType.value = 'categories'
      showWarningMessage.value = true
      setTimeout(() => {
        showWarningMessage.value = false
        console.log('🔄 Mensaje de alerta ocultado después de 40 segundos')
      }, 40000)
      return
    }
    
    // Verificar número de WhatsApp
    if (!isValidWhatsappNumber(config.ordersConfig.whatsappNumber)) {
      console.warn('⚠️ Intento de activar catálogo sin número de WhatsApp válido')
      
      // Desactivar INMEDIATAMENTE el toggle
      config.storeActive = false
      
      // Esperar a que se actualice el DOM
      await nextTick()
      
      // 💾 Guardar automáticamente el cambio en el backend
      console.log('💾 Guardando estado inactivo automáticamente...')
      await saveConfiguration()
      console.log('✅ Estado guardado en backend')
      
      // Mostrar mensaje de alerta por 40 segundos
      warningType.value = 'whatsapp'
      showWarningMessage.value = true
      setTimeout(() => {
        showWarningMessage.value = false
        console.log('🔄 Mensaje de alerta ocultado después de 40 segundos')
      }, 40000)
      return
    }
    
    // ✅ Si llegó aquí, el cambio es válido - Guardar automáticamente
    await nextTick()
    console.log('💾 Toggle cambiado - Guardando configuración automáticamente...')
    await saveConfiguration()
    console.log('✅ Configuración guardada automáticamente')
  } else if (!newValue) {
    // 🔴 Usuario desactivó la tienda manualmente - Guardar automáticamente
    await nextTick()
    console.log('💾 Tienda desactivada - Guardando configuración automáticamente...')
    await saveConfiguration()
    console.log('✅ Configuración guardada automáticamente')
  }
})

// 🛡️ Validación inteligente al cambiar categorías
watch(() => config.inventoryVisibility.visibleCategories.length, async (newLength, oldLength) => {
  console.log('📊 Cambio en categorías detectado:', { newLength, oldLength, storeActive: config.storeActive })
  
  // 1️⃣ Si se selecciona al menos una categoría, cerrar mensaje de alerta inmediatamente
  if (newLength > 0 && showWarningMessage.value) {
    showWarningMessage.value = false
    console.log('✅ Categoría seleccionada - Mensaje de alerta cerrado')
  }
  
  // 2️⃣ Si se quitan TODAS las categorías y el catálogo está activo, desactivarlo automáticamente
  if (config.storeActive && newLength === 0 && oldLength > 0) {
    console.warn('⚠️ Se quitaron todas las categorías - Desactivando catálogo automáticamente')
    
    // Desactivar el toggle
    config.storeActive = false
    
    // Esperar a que se actualice el DOM
    await nextTick()
    
    // 💾 Guardar automáticamente el cambio en el backend
    console.log('💾 Guardando estado inactivo automáticamente...')
    await saveConfiguration()
    console.log('✅ Estado guardado en backend')
    
    // Mostrar mensaje de alerta por 40 segundos
    warningType.value = 'categories'
    showWarningMessage.value = true
    setTimeout(() => {
      showWarningMessage.value = false
      console.log('🔄 Mensaje de alerta ocultado después de 40 segundos')
    }, 40000)
  }
})

// 🛡️ Validación del número de WhatsApp
watch(() => config.ordersConfig.whatsappNumber, (newValue) => {
  // Si se ingresa un número válido, cerrar mensaje de alerta si era de WhatsApp
  if (isValidWhatsappNumber(newValue) && showWarningMessage.value && warningType.value === 'whatsapp') {
    showWarningMessage.value = false
    console.log('✅ Número de WhatsApp válido - Mensaje de alerta cerrado')
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
    
    console.log('✅ Enlace copiado al portapapeles:', catalogUrl)
  } catch (error) {
    console.error('❌ Error al copiar enlace:', error)
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
      console.error('❌ Error en fallback de copia:', err)
      alert('No se pudo copiar el enlace. Por favor, cópialo manualmente.')
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

  console.log('📁 Archivo seleccionado:', {
    name: file.name,
    size: file.size,
    type: file.type
  })

  if (file.size > 2 * 1024 * 1024) {
    alert('El archivo es muy grande. Máximo 2MB.')
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    const base64String = e.target.result
    console.log('✅ Archivo convertido a base64:', {
      type,
      length: base64String.length,
      preview: base64String.substring(0, 50) + '...'
    })
    
    if (type === 'logo') {
      config.brandIdentity.logo = base64String
      console.log('🖼️ Logo asignado a config.brandIdentity.logo')
    } else if (type === 'banner') {
      config.brandIdentity.banner = base64String
      console.log('🖼️ Banner asignado a config.brandIdentity.banner')
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
      
      console.log('📥 Configuración cargada desde BD:', {
        logo_url: data.logo_url ? `${data.logo_url.substring(0, 50)}...` : 'null',
        logo_length: data.logo_url ? data.logo_url.length : 0,
        banner_url: data.banner_url ? `${data.banner_url.substring(0, 50)}...` : 'null'
      })
      
      // Por defecto inactivo si no está explícitamente configurado
      config.storeActive = data.store_active ?? false
      config.brandIdentity.logo = data.logo_url || ''
      config.brandIdentity.banner = data.banner_url || ''
      config.brandIdentity.primaryColor = data.primary_color || '#10B981'
      // 🛡️ Validar plantilla usando helper
      const loadedTemplate = data.template || 'speed-market'
      config.brandIdentity.template = getValidTemplate(loadedTemplate)
      
      const visibleCats = Array.isArray(data.visible_categories) ? data.visible_categories : []
      
      // Respetar la configuración guardada, incluso si está vacía
      config.inventoryVisibility.visibleCategories = visibleCats
      
      // 🛡️ REGLA: Si no hay categorías seleccionadas, FORZAR catálogo inactivo
      if (visibleCats.length === 0) {
        console.warn('⚠️ No hay categorías seleccionadas - Forzando catálogo inactivo')
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
    // 🛡️ Validar plantilla antes de guardar
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
    
    console.log('💾 Guardando configuración:', {
      storeActive: payload.storeActive,
      visibleCategories: payload.products.visibleCategories,
      logo_length: payload.brandIdentity.logo ? payload.brandIdentity.logo.length : 0,
      banner_length: payload.brandIdentity.banner ? payload.brandIdentity.banner.length : 0
    })
    
    const response = await apiClient.post('/web-catalog/config', payload)
    
    console.log('📤 Respuesta del servidor:', response.data)
    
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
    console.error('❌ Error saving configuration:', error)
    alert('Error al guardar la configuración.')
  } finally {
    isSaving.value = false
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
