<template>
  <div class="h-[calc(100vh-64px)] overflow-hidden flex bg-white">
    
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
          <span class="text-lg">{{ tab.icon }}</span>
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
    <main class="flex-1 bg-slate-50 overflow-y-auto relative">
      <div class="max-w-3xl mx-auto p-8 pb-24">
        
        <!-- Header de Sección -->
        <div class="mb-6 border-b border-gray-200 pb-4">
          <h2 class="text-xl font-bold text-gray-900">{{ currentTabLabel }}</h2>
          <p class="text-sm text-gray-500">Personaliza esta sección.</p>
        </div>

        <!-- CONTENIDO DINÁMICO -->
        <div class="space-y-6">
          
          <!-- SECCIÓN: DISEÑO (COMPACTA) -->
          <div v-if="activeTab === 'identity'" class="space-y-6 animate-fade-in">
            
            <!-- Grid Superior: Logo + (Color/Template) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              <!-- Columna Izquierda: Logo (Cuadrado) -->
              <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 h-full flex flex-col">
                <label class="block text-xs font-bold uppercase text-gray-500 mb-3">Logo de la Tienda</label>
                <div 
                  @click="triggerFileUpload('logo')"
                  class="flex-1 border-2 border-dashed border-gray-200 rounded-lg hover:border-emerald-500 hover:bg-emerald-50 transition-all cursor-pointer group relative flex flex-col items-center justify-center bg-gray-50 min-h-[140px]"
                >
                  <div v-if="config.brandIdentity.logo" class="absolute inset-0 p-4">
                    <img :src="config.brandIdentity.logo" class="w-full h-full object-contain" />
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg">
                      <span class="text-white text-xs font-medium px-3 py-1 bg-black/50 rounded backdrop-blur-sm">Cambiar</span>
                    </div>
                  </div>
                  <div v-else class="text-gray-400 group-hover:text-emerald-600 transition-colors text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span class="text-xs font-medium">Subir Logo</span>
                  </div>
                  <input type="file" ref="logoInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'logo')" />
                </div>
              </div>

              <!-- Columna Derecha: Color y Template (Apilados) -->
              <div class="space-y-4">
                <!-- Color Picker -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                  <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Color de Marca</label>
                  <div class="flex items-center gap-3">
                    <div class="relative w-10 h-10 rounded-lg overflow-hidden shadow-sm ring-1 ring-gray-200 cursor-pointer hover:scale-105 transition-transform">
                      <input 
                        type="color" 
                        v-model="config.brandIdentity.primaryColor"
                        class="absolute -top-2 -left-2 w-14 h-14 cursor-pointer border-0 p-0"
                      />
                    </div>
                    <div class="flex-1">
                      <input 
                        type="text" 
                        v-model="config.brandIdentity.primaryColor"
                        class="w-full h-10 px-3 rounded-lg border border-gray-200 text-sm font-mono focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none uppercase"
                      />
                    </div>
                  </div>
                </div>
                
                <!-- Plantilla Selector Compacto -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                  <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Diseño</label>
                  <div class="flex gap-2">
                    <div 
                      @click="config.brandIdentity.template = 'modern-grid'"
                      class="flex-1 p-2 rounded-lg border cursor-pointer transition-all text-center"
                      :class="config.brandIdentity.template === 'modern-grid' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-200 hover:bg-gray-50 text-gray-600'"
                    >
                      <div class="text-xs font-bold">Grid</div>
                    </div>
                    <div 
                      @click="config.brandIdentity.template = 'simple-list'"
                      class="flex-1 p-2 rounded-lg border cursor-pointer transition-all text-center"
                      :class="config.brandIdentity.template === 'simple-list' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-200 hover:bg-gray-50 text-gray-600'"
                    >
                      <div class="text-xs font-bold">Lista</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Banner (Full Width pero Slim) -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
              <label class="block text-xs font-bold uppercase text-gray-500 mb-3">Banner Principal</label>
              <div 
                @click="triggerFileUpload('banner')"
                class="border-2 border-dashed border-gray-200 rounded-lg hover:border-emerald-500 hover:bg-emerald-50 transition-all cursor-pointer group relative h-24 flex flex-col items-center justify-center bg-gray-50"
              >
                <div v-if="config.brandIdentity.banner" class="absolute inset-0 p-1">
                  <img :src="config.brandIdentity.banner" class="w-full h-full object-cover rounded-md" />
                  <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-md">
                    <span class="text-white text-xs font-medium px-3 py-1 bg-black/50 rounded backdrop-blur-sm">Cambiar Banner</span>
                  </div>
                </div>
                <div v-else class="text-gray-400 group-hover:text-emerald-600 transition-colors flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span class="text-xs font-medium">Subir Banner Promocional</span>
                </div>
                <input type="file" ref="bannerInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'banner')" />
              </div>
            </div>

          </div>

          <!-- SECCIÓN: PRODUCTOS -->
          <div v-else-if="activeTab === 'catalog'" class="space-y-6 animate-fade-in">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
              <label class="block text-xs font-bold uppercase text-gray-500 mb-4">Categorías Visibles</label>
              <div class="flex flex-wrap gap-2">
                <button 
                  v-for="category in availableCategories" 
                  :key="category.id"
                  @click="toggleCategory(category.id)"
                  class="px-4 py-2 rounded-lg text-xs font-bold border transition-all duration-200 flex items-center gap-2"
                  :class="config.inventoryVisibility.visibleCategories.includes(category.id) 
                    ? 'bg-emerald-50 border-emerald-500 text-emerald-700' 
                    : 'bg-white border-gray-200 text-gray-500 hover:border-gray-300'"
                >
                  <span>{{ category.name }}</span>
                  <div v-if="config.inventoryVisibility.visibleCategories.includes(category.id)" class="bg-emerald-500 rounded-full p-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </button>
              </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
              <div>
                <h3 class="text-sm font-bold text-gray-900">Ocultar sin Stock</h3>
                <p class="text-xs text-gray-500">No mostrar productos agotados</p>
              </div>
              <button 
                @click="config.inventoryVisibility.hideOutOfStock = !config.inventoryVisibility.hideOutOfStock"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                :class="config.inventoryVisibility.hideOutOfStock ? 'bg-emerald-500' : 'bg-gray-200'"
              >
                <span
                  class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm"
                  :class="config.inventoryVisibility.hideOutOfStock ? 'translate-x-6' : 'translate-x-1'"
                />
              </button>
            </div>
          </div>

          <!-- SECCIÓN: WHATSAPP -->
          <div v-else-if="activeTab === 'orders'" class="space-y-6 animate-fade-in">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
              <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Número de WhatsApp</label>
              <input 
                type="text" 
                v-model="config.ordersConfig.whatsappNumber"
                placeholder="+57 300 123 4567"
                class="w-full h-11 px-4 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-colors"
              />
              <p class="text-xs text-gray-400 mt-2">Es el número donde recibirás los pedidos.</p>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
              <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Mensaje Inicial</label>
              <textarea 
                v-model="config.ordersConfig.customMessage"
                rows="3"
                class="w-full p-4 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none resize-none transition-colors"
              ></textarea>
            </div>
          </div>

          <!-- SECCIÓN: REGLAS -->
          <div v-else-if="activeTab === 'rules'" class="space-y-6 animate-fade-in">
            <div class="grid grid-cols-2 gap-6">
              <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Costo Domicilio</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">$</span>
                  <input 
                    type="number" 
                    v-model="config.businessRules.deliveryCost"
                    class="w-full h-11 pl-7 pr-3 rounded-lg border border-gray-200 text-lg font-bold text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-colors"
                  />
                </div>
              </div>

              <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Pedido Mínimo</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">$</span>
                  <input 
                    type="number" 
                    v-model="config.businessRules.minimumOrder"
                    class="w-full h-11 pl-7 pr-3 rounded-lg border border-gray-200 text-lg font-bold text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-colors"
                  />
                </div>
              </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
              <div>
                <h3 class="text-sm font-bold text-gray-900">Sincronizar con Caja</h3>
                <p class="text-xs text-gray-500">Crear ventas en POS automáticamente</p>
              </div>
              <button 
                @click="config.businessRules.syncWithCashRegister = !config.businessRules.syncWithCashRegister"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                :class="config.businessRules.syncWithCashRegister ? 'bg-emerald-500' : 'bg-gray-200'"
              >
                <span
                  class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm"
                  :class="config.businessRules.syncWithCashRegister ? 'translate-x-6' : 'translate-x-1'"
                />
              </button>
            </div>
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
          class="relative bg-white transition-all duration-300 shadow-2xl overflow-hidden ring-1 ring-white/10"
          :class="previewMode === 'mobile' 
            ? 'w-[340px] h-[680px] rounded-[3rem] border-[8px] border-gray-800' 
            : 'w-full h-[90%] rounded-lg border-[4px] border-gray-700 max-w-5xl'"
        >
          <!-- Notch (Solo Mobile) -->
          <div v-if="previewMode === 'mobile'" class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-gray-800 rounded-b-xl z-20"></div>
          
          <!-- Iframe -->
          <iframe 
            ref="previewIframe"
            :src="catalogUrl"
            class="w-full h-full bg-white"
            frameborder="0"
          ></iframe>
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
        <h4 class="font-bold text-sm">¡Guardado!</h4>
        <p class="text-xs opacity-90">Vista previa actualizada.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { appStore } from '../store/appStore.js'
import apiClient from '../services/apiClient.js'

// Refs
const logoInput = ref(null)
const bannerInput = ref(null)
const previewIframe = ref(null)

// State
const isSaving = ref(false)
const showSuccessToast = ref(false)
const isLoading = ref(true)
const activeTab = ref('identity')
const catalogUrl = ref('')
const previewMode = ref('mobile') // 'mobile' | 'desktop'

// Tabs Configuration
const tabs = [
  { id: 'identity', label: 'Diseño', icon: '🎨' },
  { id: 'catalog', label: 'Productos', icon: '📦' },
  { id: 'orders', label: 'WhatsApp', icon: '💬' },
  { id: 'rules', label: 'Reglas', icon: '⚙️' }
]

const currentTabLabel = computed(() => {
  const tab = tabs.find(t => t.id === activeTab.value)
  return tab ? tab.label : ''
})

// Configuration Object (Reactive)
const config = reactive({
  storeActive: true,
  brandIdentity: {
    logo: '', 
    banner: '',
    primaryColor: '#10B981', 
    template: 'modern-grid'
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
  // Determine Catalog URL
  const currentHost = window.location.hostname
  const port = window.location.port ? `:${window.location.port}` : ''
  catalogUrl.value = `${window.location.protocol}//${currentHost}${port}/catalog`

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

  // Load existing configuration from backend
  await loadConfiguration()
  isLoading.value = false
})

// Refresh Preview Iframe
const refreshPreview = () => {
  if (previewIframe.value) {
    previewIframe.value.src = previewIframe.value.src
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
    alert('El archivo es muy grande. Máximo 2MB.')
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    if (type === 'logo') {
      config.brandIdentity.logo = e.target.result
    } else if (type === 'banner') {
      config.brandIdentity.banner = e.target.result
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
      
      config.storeActive = data.store_active ?? true
      config.brandIdentity.logo = data.logo_url || ''
      config.brandIdentity.banner = data.banner_url || ''
      config.brandIdentity.primaryColor = data.primary_color || '#10B981'
      config.brandIdentity.template = data.template || 'modern-grid'
      
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
    const response = await apiClient.post('/web-catalog/config', config)
    
    if (response.data.success) {
      showSuccessToast.value = true
      setTimeout(() => {
        showSuccessToast.value = false
      }, 3000)
      
      // Refresh preview after save
      refreshPreview()
    }
  } catch (error) {
    console.error('Error saving configuration:', error)
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
</style>
