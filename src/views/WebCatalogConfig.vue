<template>
  <div class="h-[calc(100vh-64px)] overflow-hidden flex bg-white relative" style="isolation: isolate;">
    
    <!-- ÁREA A: SIDEBAR DE NAVEGACIÓN (Izquierda - Fijo - w-64) -->
    <aside class="w-64 flex-shrink-0 border-r border-gray-200 bg-white flex flex-col z-20">
      <div class="p-6 border-b border-gray-100">
        <h1 class="text-lg font-bold text-gray-900">Configuración</h1>
        <p class="text-xs text-gray-500">Tienda Online</p>
      </div>

      <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-3">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          class="w-full flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-all duration-200 text-left group"
          :class="activeTab === tab.id 
            ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 shadow-sm' 
            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent'"
        >
          <!-- Iconos SVG dinámicos -->
          <svg v-if="tab.icon === 'palette'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
          </svg>
          <svg v-else-if="tab.icon === 'box'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
          <svg v-else-if="tab.icon === 'message'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
          <svg v-else-if="tab.icon === 'settings'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span>{{ tab.label }}</span>
        </button>
      </nav>

      <div class="p-4 border-t border-gray-100 bg-gray-50">
        <div class="flex items-center justify-between mb-4">
          <span class="text-xs font-bold uppercase text-gray-500">Estado</span>
          <button 
            @click="config.storeActive = !config.storeActive"
            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none"
            :class="config.storeActive ? 'bg-emerald-500' : 'bg-gray-300'"
          >
            <span
              class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform"
              :class="config.storeActive ? 'translate-x-5' : 'translate-x-1'"
            />
          </button>
        </div>
        
        <button 
          @click="saveConfiguration"
          :disabled="isSaving"
          class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors text-sm font-bold shadow-md disabled:opacity-70 disabled:cursor-not-allowed"
        >
          <svg v-if="!isSaving" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isSaving ? 'Guardando...' : 'Guardar Cambios' }}
        </button>
      </div>
    </aside>

    <!-- ÁREA B: PANEL DE FORMULARIOS (Centro - Scrollable - Flex-1) -->
    <main class="flex-1 bg-white overflow-y-auto relative">
      <div class="h-full">
        
        <!-- Header de Sección - Ejecutivo con Acciones -->
        <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-8 py-5">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-lg font-semibold text-gray-900">{{ currentTabLabel }}</h2>
              <p class="text-sm text-gray-500 mt-0.5">{{ currentTabDescription }}</p>
            </div>
            
            <!-- Botones de Acción -->
            <div class="flex items-center gap-3">
              <!-- Copiar Link -->
              <button 
                @click="copyStoreLink"
                class="flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-md border border-gray-200 transition-all duration-150 hover:border-gray-300"
                title="Copiar enlace de la tienda"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                </svg>
                <span>Copiar enlace</span>
              </button>
              
              <!-- Ver mi Página -->
              <button 
                @click="openCatalogInNewWindow"
                class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-all duration-150 shadow-sm"
                title="Abrir catálogo en nueva ventana"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                <span>Ver mi página</span>
              </button>
            </div>
          </div>
        </div>

        <!-- CONTENIDO DINÁMICO - Full Width -->
        <div class="px-8 py-6 space-y-8">
          
          <!-- SECCIÓN: DISEÑO - Estilo Ejecutivo -->
          <div v-if="activeTab === 'identity'" class="space-y-8 animate-fade-in pb-8">
            
            <!-- Subsección: Personalización de Marca -->
            <section>
              <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Personalización de Marca</h3>
                <p class="text-xs text-gray-500 mt-1">Define los colores y logotipos que verán tus clientes en el catálogo.</p>
              </div>
              
              <!-- Grid 2 Columnas: Logo + Color/Template -->
              <div class="grid grid-cols-2 gap-6">
                
                <!-- Logo Upload -->
                <div class="border border-gray-200 rounded-md bg-white p-4">
                  <label class="block text-xs font-medium text-gray-700 mb-3">Logo de la Tienda</label>
                  <div 
                    @click="triggerFileUpload('logo')"
                    class="border-2 border-dashed border-gray-200 rounded-md hover:border-emerald-400 hover:bg-emerald-50/50 transition-all cursor-pointer group relative flex flex-col items-center justify-center bg-gray-50 h-[160px]"
                  >
                    <div v-if="config.brandIdentity.logo" class="absolute inset-0 p-3">
                      <img :src="config.brandIdentity.logo" class="w-full h-full object-contain" />
                      <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-md">
                        <span class="text-white text-xs font-medium px-3 py-1.5 bg-black/60 rounded-md">Cambiar logo</span>
                      </div>
                    </div>
                    <div v-else class="text-gray-400 group-hover:text-emerald-600 transition-colors text-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                      </svg>
                      <span class="text-xs font-medium">Subir logo</span>
                      <span class="text-[10px] text-gray-400 block mt-1">PNG, JPG o SVG</span>
                    </div>
                    <input type="file" ref="logoInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'logo')" />
                  </div>
                </div>

                <!-- Color + Template en columna -->
                <div class="space-y-4">
                  
                  <!-- Color Picker Ejecutivo -->
                  <div class="border border-gray-200 rounded-md bg-white p-4">
                    <label class="block text-xs font-medium text-gray-700 mb-3">Color Primario</label>
                    <div class="flex items-center gap-3">
                      <div class="relative w-12 h-12 rounded-md overflow-hidden border border-gray-200 cursor-pointer hover:scale-105 transition-transform flex-shrink-0">
                        <input 
                          type="color" 
                          v-model="config.brandIdentity.primaryColor"
                          class="absolute inset-0 w-full h-full cursor-pointer border-0 p-0"
                          style="transform: scale(1.5);"
                        />
                      </div>
                      <input 
                        type="text" 
                        v-model="config.brandIdentity.primaryColor"
                        class="flex-1 h-10 px-3 rounded-md border border-gray-200 text-xs font-mono text-gray-700 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 uppercase"
                        placeholder="#10B981"
                      />
                    </div>
                  </div>
                  
                  <!-- Template Selector Compacto -->
                  <div class="border border-gray-200 rounded-md bg-white p-4">
                    <label class="block text-xs font-medium text-gray-700 mb-3">Plantilla de Diseño</label>
                    <div class="space-y-2">
                      
                      <!-- Visual Story -->
                      <button 
                        @click="config.brandIdentity.template = 'visual-story'"
                        class="w-full p-2.5 rounded-md border transition-all flex items-center gap-3 text-left group"
                        :class="config.brandIdentity.template === 'visual-story' 
                          ? 'border-emerald-500 bg-emerald-50' 
                          : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                      >
                        <div class="w-8 h-8 rounded-md bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center flex-shrink-0 border border-purple-200">
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                        </div>
                        <div class="flex-1">
                          <div class="text-xs font-semibold" :class="config.brandIdentity.template === 'visual-story' ? 'text-emerald-700' : 'text-gray-900'">
                            Historia Visual
                          </div>
                          <div class="text-[10px] text-gray-500">Boutique / Gourmet</div>
                        </div>
                        <div v-if="config.brandIdentity.template === 'visual-story'" class="w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                          </svg>
                        </div>
                      </button>

                      <!-- Speed Market -->
                      <button 
                        @click="config.brandIdentity.template = 'speed-market'"
                        class="w-full p-2.5 rounded-md border transition-all flex items-center gap-3 text-left group"
                        :class="config.brandIdentity.template === 'speed-market' 
                          ? 'border-emerald-500 bg-emerald-50' 
                          : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                      >
                        <div class="w-8 h-8 rounded-md bg-gradient-to-br from-blue-100 to-cyan-100 flex items-center justify-center flex-shrink-0 border border-blue-200">
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                          </svg>
                        </div>
                        <div class="flex-1">
                          <div class="text-xs font-semibold" :class="config.brandIdentity.template === 'speed-market' ? 'text-emerald-700' : 'text-gray-900'">
                            Mercado Rápido
                          </div>
                          <div class="text-[10px] text-gray-500">Supermercado / Rápido</div>
                        </div>
                        <div v-if="config.brandIdentity.template === 'speed-market'" class="w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                          </svg>
                        </div>
                      </button>

                      <!-- Modern Grid -->
                      <button 
                        @click="config.brandIdentity.template = 'modern-grid'"
                        class="w-full p-2.5 rounded-md border transition-all flex items-center gap-3 text-left group"
                        :class="config.brandIdentity.template === 'modern-grid' 
                          ? 'border-emerald-500 bg-emerald-50' 
                          : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                      >
                        <div class="w-8 h-8 rounded-md bg-gradient-to-br from-gray-100 to-slate-100 flex items-center justify-center flex-shrink-0 border border-gray-300">
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z" />
                          </svg>
                        </div>
                        <div class="flex-1">
                          <div class="text-xs font-semibold" :class="config.brandIdentity.template === 'modern-grid' ? 'text-emerald-700' : 'text-gray-900'">
                            Cuadrícula Moderna
                          </div>
                          <div class="text-[10px] text-gray-500">Clásico / Versátil</div>
                        </div>
                        <div v-if="config.brandIdentity.template === 'modern-grid'" class="w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                          </svg>
                        </div>
                      </button>
                      
                    </div>
                  </div>
                  
                </div>
              </div>
            </section>

            <!-- Subsección: Banner Promocional -->
            <section>
              <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Banner Promocional</h3>
                <p class="text-xs text-gray-500 mt-1">Imagen destacada en la parte superior del catálogo.</p>
              </div>
              
              <div class="border border-gray-200 rounded-md bg-white p-4">
                <div 
                  @click="triggerFileUpload('banner')"
                  class="border-2 border-dashed border-gray-200 rounded-md hover:border-emerald-400 hover:bg-emerald-50/50 transition-all cursor-pointer group relative h-32 flex items-center justify-center bg-gray-50"
                >
                  <div v-if="config.brandIdentity.banner" class="absolute inset-0 p-1">
                    <img :src="config.brandIdentity.banner" class="w-full h-full object-cover rounded" />
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded">
                      <span class="text-white text-xs font-medium px-3 py-1.5 bg-black/60 rounded-md">Cambiar banner</span>
                    </div>
                  </div>
                  <div v-else class="text-gray-400 group-hover:text-emerald-600 transition-colors flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div>
                      <span class="text-xs font-medium block">Subir banner promocional</span>
                      <span class="text-[10px] text-gray-400">Recomendado: 1200x400px</span>
                    </div>
                  </div>
                  <input type="file" ref="bannerInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'banner')" />
                </div>
              </div>
            </section>

          </div>

          <!-- SECCIÓN: PRODUCTOS - Estilo Ejecutivo -->
          <div v-else-if="activeTab === 'catalog'" class="space-y-8 animate-fade-in pb-8">
            
            <section>
              <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Visibilidad del Catálogo</h3>
                <p class="text-xs text-gray-500 mt-1">Controla qué categorías y productos se muestran en tu tienda online.</p>
              </div>
              
              <div class="border border-gray-200 rounded-md bg-white p-5">
                <label class="block text-xs font-medium text-gray-700 mb-3">Categorías Visibles</label>
                <div class="flex flex-wrap gap-2">
                  <button 
                    v-for="category in availableCategories" 
                    :key="category.id"
                    @click="toggleCategory(category.id)"
                    class="px-3 py-1.5 rounded-md text-xs font-medium border transition-all duration-150 flex items-center gap-2"
                    :class="config.inventoryVisibility.visibleCategories.includes(category.id) 
                      ? 'bg-emerald-50 border-emerald-500 text-emerald-700' 
                      : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'"
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
              <div class="border border-gray-200 rounded-md bg-white p-5 flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-semibold text-gray-900">Ocultar productos sin stock</h3>
                  <p class="text-xs text-gray-500 mt-0.5">No mostrar productos agotados en el catálogo</p>
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
                <h3 class="text-sm font-semibold text-gray-900">Configuración de Pedidos</h3>
                <p class="text-xs text-gray-500 mt-1">Define cómo tus clientes realizarán pedidos a través de WhatsApp.</p>
              </div>
              
              <!-- Grid 2 Columnas: Número + País/Horario (Placeholder) -->
              <div class="grid grid-cols-2 gap-6">
                
                <div class="border border-gray-200 rounded-md bg-white p-4">
                  <label class="block text-xs font-medium text-gray-700 mb-2">Número de WhatsApp</label>
                  <input 
                    type="text" 
                    v-model="config.ordersConfig.whatsappNumber"
                    placeholder="+57 300 123 4567"
                    class="w-full h-10 px-3 rounded-md border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                  />
                  <p class="text-[10px] text-gray-500 mt-2">Es el número donde recibirás los pedidos.</p>
                </div>

                <div class="border border-gray-200 rounded-md bg-white p-4">
                  <label class="block text-xs font-medium text-gray-700 mb-2">Horario de Atención</label>
                  <input 
                    type="text" 
                    placeholder="Lun-Vie: 9AM - 6PM"
                    class="w-full h-10 px-3 rounded-md border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                    disabled
                  />
                  <p class="text-[10px] text-gray-500 mt-2">Muestra tu disponibilidad (próximamente).</p>
                </div>
                
              </div>

              <!-- Mensaje Inicial - Full Width -->
              <div class="border border-gray-200 rounded-md bg-white p-4">
                <label class="block text-xs font-medium text-gray-700 mb-2">Mensaje Inicial Personalizado</label>
                <textarea 
                  v-model="config.ordersConfig.customMessage"
                  rows="3"
                  placeholder="Hola, quiero hacer el siguiente pedido:"
                  class="w-full px-3 py-2 rounded-md border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 resize-none"
                ></textarea>
                <p class="text-[10px] text-gray-500 mt-2">Este mensaje aparecerá automáticamente al iniciar la conversación.</p>
              </div>
              
            </section>
            
          </div>

          <!-- SECCIÓN: REGLAS - Grid 2x2 Ejecutivo -->
          <div v-else-if="activeTab === 'rules'" class="space-y-8 animate-fade-in pb-8">
            
            <section>
              <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Reglas de Negocio</h3>
                <p class="text-xs text-gray-500 mt-1">Define los parámetros operativos de tu tienda online.</p>
              </div>
              
              <!-- Grid 2 Columnas: Costo + Mínimo -->
              <div class="grid grid-cols-2 gap-6">
                
                <div class="border border-gray-200 rounded-md bg-white p-4">
                  <label class="block text-xs font-medium text-gray-700 mb-2">Costo de Domicilio</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">$</span>
                    <input 
                      type="number" 
                      v-model="config.businessRules.deliveryCost"
                      class="w-full h-10 pl-7 pr-3 rounded-md border border-gray-200 text-base font-semibold text-gray-900 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                      placeholder="0"
                    />
                  </div>
                  <p class="text-[10px] text-gray-500 mt-2">Precio del envío a domicilio.</p>
                </div>

                <div class="border border-gray-200 rounded-md bg-white p-4">
                  <label class="block text-xs font-medium text-gray-700 mb-2">Pedido Mínimo</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">$</span>
                    <input 
                      type="number" 
                      v-model="config.businessRules.minimumOrder"
                      class="w-full h-10 pl-7 pr-3 rounded-md border border-gray-200 text-base font-semibold text-gray-900 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                      placeholder="0"
                    />
                  </div>
                  <p class="text-[10px] text-gray-500 mt-2">Valor mínimo para aceptar pedidos.</p>
                </div>
                
              </div>
            </section>

            <section>
              <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Integraciones</h3>
                <p class="text-xs text-gray-500 mt-1">Conecta tu tienda con otros sistemas.</p>
              </div>
              
              <div class="border border-gray-200 rounded-md bg-white p-5 flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-semibold text-gray-900">Sincronizar con Caja Registradora</h3>
                  <p class="text-xs text-gray-500 mt-0.5">Registrar pedidos online automáticamente en el POS</p>
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

        </div>
      </div>
    </main>

    <!-- ÁREA C: LIVE PREVIEW REAL (Derecha - w-[500px] o 40%) -->
    <aside 
      class="flex-shrink-0 bg-slate-900 border-l border-gray-800 flex flex-col transition-all duration-300"
      :class="previewMode === 'mobile' ? 'w-[450px]' : 'w-[800px]'"
    >
      <!-- Preview Header -->
      <div class="p-4 border-b border-gray-800 flex items-center justify-between">
        <h3 class="text-white font-bold text-sm flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
          Vista Previa
        </h3>
        
        <!-- Toggle Mobile/Desktop -->
        <div class="flex items-center gap-2">
          <div class="flex bg-gray-800 rounded-lg p-1 border border-gray-700">
            <button 
              @click="previewMode = 'mobile'"
              class="p-1.5 rounded-md transition-all"
              :class="previewMode === 'mobile' ? 'bg-gray-600 text-white shadow-sm' : 'text-gray-400 hover:text-gray-200'"
              title="Vista Móvil"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
            </button>
            <button 
              @click="previewMode = 'desktop'"
              class="p-1.5 rounded-md transition-all"
              :class="previewMode === 'desktop' ? 'bg-gray-600 text-white shadow-sm' : 'text-gray-400 hover:text-gray-200'"
              title="Vista Escritorio"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </button>
          </div>

          <button 
            @click="openCatalogInNewWindow"
            class="text-gray-400 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10"
            title="Abrir en ventana nueva (Vista Real)"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
          </button>
        </div>

        <button 
          @click="refreshPreview"
          class="text-gray-400 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10"
          title="Recargar Vista"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>
      
      <div class="flex-1 p-6 flex items-center justify-center overflow-hidden bg-slate-900 relative">
        <!-- Marco de Dispositivo -->
        <div 
          class="relative bg-white transition-all duration-300 shadow-2xl overflow-hidden ring-1 ring-white/10 isolate"
          :class="previewMode === 'mobile' 
            ? 'w-[375px] h-[740px] rounded-[3rem] border-[10px] border-gray-800' 
            : 'w-full h-[90%] rounded-lg border-[4px] border-gray-700 max-w-5xl'"
          :style="previewMode === 'mobile' ? 'container-type: inline-size; width: 375px;' : ''"
        >
          <!-- Notch (Solo Mobile) -->
          <div v-if="previewMode === 'mobile'" class="absolute top-0 left-1/2 -translate-x-1/2 w-36 h-7 bg-gray-800 rounded-b-2xl z-50"></div>
          
          <!-- Live Preview Component - Aislado con nuevo contexto de apilamiento -->
          <div class="w-full h-full overflow-auto bg-white relative" style="isolation: isolate; transform: translateZ(0);">
            <CatalogTemplateSelector 
              :template="config.brandIdentity.template"
              :storeConfig="previewStoreConfig"
              :isMobilePreview="previewMode === 'mobile'"
              :key="`${previewKey}-${previewMode}`"
            />
          </div>
        </div>
      </div>
      
      <div class="p-3 text-center text-xs text-gray-500 border-t border-gray-800">
        {{ previewMode === 'mobile' ? 'Vista Móvil (iPhone)' : 'Vista Escritorio (Laptop)' }}
      </div>
    </aside>

    <!-- Toast Notification -->
    <div 
      v-if="showSuccessToast" 
      class="fixed bottom-6 left-6 bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-slide-up z-50"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      <div>
        <h4 class="font-bold text-sm">{{ toastMessage.title }}</h4>
        <p class="text-xs opacity-90">{{ toastMessage.description }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { appStore } from '../store/appStore.js'
import apiClient from '../services/apiClient.js'
import CatalogTemplateSelector from '../components/catalog/CatalogTemplateSelector.vue'

// Refs
const logoInput = ref(null)
const bannerInput = ref(null)
const previewKey = ref(0)

// State
const isSaving = ref(false)
const showSuccessToast = ref(false)
const toastMessage = reactive({
  title: '¡Guardado!',
  description: 'Vista previa actualizada.'
})
const isLoading = ref(true)
const activeTab = ref('identity')
const previewMode = ref('mobile') // 'mobile' | 'desktop'
const realProducts = ref([]) // Productos reales de la base de datos

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

// Preview Store Config - Se actualiza reactivamente con los cambios del config
const previewStoreConfig = computed(() => ({
  primary_color: config.brandIdentity.primaryColor,
  logo_url: config.brandIdentity.logo || 'https://via.placeholder.com/150?text=Logo',
  banner_url: config.brandIdentity.banner || 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200',
  whatsapp_number: config.ordersConfig.whatsappNumber,
  currency_symbol: '$',
  delivery_cost: config.businessRules.deliveryCost,
  min_order_value: config.businessRules.minimumOrder,
  store_name: appStore.businessName || 'Mi Tienda',
  catalog_products: realProducts.value // Productos reales de la BD
}))

// Configuration Object (Reactive)
const config = reactive({
  storeActive: true,
  brandIdentity: {
    logo: '', 
    banner: '',
    primaryColor: '#10B981', 
    template: 'visual-story' // Plantilla por defecto
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
  // Load categories
  if (appStore.categories && appStore.categories.length > 0) {
    availableCategories.value = appStore.categories
  } else {
    availableCategories.value = [
      { id: 1, name: 'Bebidas' },
      { id: 2, name: 'Snacks' },
      { id: 3, name: 'Lácteos' },
      { id: 4, name: 'Panadería' },
      { id: 5, name: 'Aseo' },
      { id: 6, name: 'Insumos' }
    ]
  }

  // Cargar productos reales para la preview
  try {
    const response = await apiClient.get('/public/catalog')
    if (response.data.success && response.data.products) {
      realProducts.value = response.data.products.map(p => ({
        id: p.id,
        name: p.name,
        description: p.description || '',
        price: p.price,
        image_url: p.image || p.image_url,
        stock: p.stock || 0,
        category: p.category || 'Sin categoría'
      }))
    }
  } catch (error) {
    console.error('Error cargando productos para preview:', error)
  }

  // Load existing configuration from backend
  await loadConfiguration()
  isLoading.value = false
})

// Refresh Preview
const refreshPreview = () => {
  previewKey.value++
}

// Open catalog in new window (real mobile view)
const openCatalogInNewWindow = () => {
  const catalogUrl = `${window.location.origin}/catalog`
  const windowFeatures = previewMode.value === 'mobile' 
    ? 'width=414,height=896,left=100,top=100'
    : 'width=1200,height=800,left=100,top=100'
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
      
      config.storeActive = data.store_active ?? true
      config.brandIdentity.logo = data.logo_url || ''
      config.brandIdentity.banner = data.banner_url || ''
      config.brandIdentity.primaryColor = data.primary_color || '#10B981'
      config.brandIdentity.template = data.template || 'visual-story'
      
      const visibleCats = Array.isArray(data.visible_categories) ? data.visible_categories : []
      
      if (visibleCats.length === 0 && availableCategories.value.length > 0) {
        config.inventoryVisibility.visibleCategories = availableCategories.value.map(c => c.id)
      } else {
        config.inventoryVisibility.visibleCategories = visibleCats
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
    if (config.inventoryVisibility.visibleCategories.length === 0 && availableCategories.value.length > 0) {
      config.inventoryVisibility.visibleCategories = availableCategories.value.map(c => c.id)
    }
  }
}

// Save configuration to backend
const saveConfiguration = async () => {
  isSaving.value = true
  
  try {
    console.log('💾 Guardando configuración:', {
      logo_length: config.brandIdentity.logo ? config.brandIdentity.logo.length : 0,
      logo_preview: config.brandIdentity.logo ? config.brandIdentity.logo.substring(0, 50) + '...' : 'null',
      banner_length: config.brandIdentity.banner ? config.brandIdentity.banner.length : 0
    })
    
    const response = await apiClient.post('/web-catalog/config', config)
    
    console.log('📤 Respuesta del servidor:', response.data)
    
    if (response.data.success) {
      toastMessage.title = '¡Guardado!'
      toastMessage.description = 'Configuración actualizada correctamente.'
      showSuccessToast.value = true
      setTimeout(() => {
        showSuccessToast.value = false
      }, 3000)
      
      // Refresh preview after save
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
