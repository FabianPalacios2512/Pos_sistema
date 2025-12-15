<template>
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <!-- HEADER FIJO -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 px-6 py-4">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Configuración Tienda</h1>
          <p class="text-sm text-gray-500">Administra tu catálogo online desde un solo lugar</p>
        </div>

        <div class="flex items-center gap-3">
          <!-- Switch Tienda Activa -->
          <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
            <span class="text-xs font-bold uppercase text-gray-500">Tienda {{ config.storeActive ? 'Activa' : 'Inactiva' }}</span>
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

          <!-- Botón Ver Tienda -->
          <button 
            @click="viewStore"
            class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium shadow-sm"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Ver Tienda
          </button>

          <!-- Botón Guardar -->
          <button 
            @click="saveConfiguration"
            :disabled="isSaving"
            class="flex items-center gap-2 px-6 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors text-sm font-medium shadow-sm disabled:opacity-70 disabled:cursor-not-allowed"
          >
            <svg v-if="!isSaving" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg v-else class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isSaving ? 'Guardando...' : 'Guardar' }}
          </button>
        </div>
      </div>
    </header>

    <!-- CUERPO PRINCIPAL -->
    <main class="flex-1 max-w-7xl mx-auto w-full p-6 flex flex-col md:flex-row gap-8">
      
      <!-- PANEL A: SIDEBAR DE NAVEGACIÓN (25%) -->
      <aside class="w-full md:w-1/4 flex flex-col gap-2">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 text-left group relative overflow-hidden"
          :class="activeTab === tab.id ? 'bg-white text-emerald-600 shadow-sm ring-1 ring-gray-100' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'"
        >
          <!-- Indicador lateral activo -->
          <div 
            v-if="activeTab === tab.id" 
            class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 rounded-l-lg"
          ></div>
          
          <span class="text-lg">{{ tab.icon }}</span>
          <span>{{ tab.label }}</span>
        </button>
      </aside>

      <!-- PANEL B: ÁREA DE CONTENIDO (75%) -->
      <section class="w-full md:w-3/4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 min-h-[500px] p-8 relative overflow-hidden">
          
          <!-- Transición Fade -->
          <transition name="fade" mode="out-in">
            
            <div v-if="activeTab === 'identity'" key="identity" class="space-y-8">
              <div class="border-b border-gray-100 pb-4 mb-6">
                <h2 class="text-xl font-bold text-gray-800">Identidad & Diseño</h2>
                <p class="text-sm text-gray-500">Define cómo se ve tu marca ante tus clientes.</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Columna 1: Assets -->
                <div class="space-y-6">
                  <!-- Logo -->
                  <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Logo</label>
                    <div 
                      @click="triggerFileUpload('logo')"
                      class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-emerald-500 hover:bg-emerald-50 transition-colors cursor-pointer group relative h-40 flex flex-col items-center justify-center bg-gray-50"
                    >
                      <div v-if="config.brandIdentity.logo" class="absolute inset-0 p-4">
                        <img :src="config.brandIdentity.logo" class="w-full h-full object-contain" />
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-xl">
                          <span class="text-white text-xs font-medium">Cambiar Logo</span>
                        </div>
                      </div>
                      <div v-else class="text-gray-400 group-hover:text-emerald-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span class="text-xs font-medium">Subir Logo</span>
                      </div>
                      <input type="file" ref="logoInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'logo')" />
                    </div>
                  </div>

                  <!-- Banner -->
                  <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Banner</label>
                    <div 
                      @click="triggerFileUpload('banner')"
                      class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-emerald-500 hover:bg-emerald-50 transition-colors cursor-pointer group relative h-24 flex flex-col items-center justify-center bg-gray-50"
                    >
                      <div v-if="config.brandIdentity.banner" class="absolute inset-0 p-1">
                        <img :src="config.brandIdentity.banner" class="w-full h-full object-cover rounded-lg" />
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg">
                          <span class="text-white text-xs font-medium">Cambiar Banner</span>
                        </div>
                      </div>
                      <div v-else class="text-gray-400 group-hover:text-emerald-500">
                        <span class="text-xs font-medium">Subir Banner</span>
                      </div>
                      <input type="file" ref="bannerInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'banner')" />
                    </div>
                  </div>
                </div>

                <!-- Columna 2: Estilos -->
                <div class="space-y-6">
                  <!-- Color -->
                  <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Color de Marca</label>
                    <div class="flex items-center gap-3">
                      <div class="relative w-12 h-12 rounded-lg overflow-hidden shadow-sm ring-1 ring-gray-200">
                        <input 
                          type="color" 
                          v-model="config.brandIdentity.primaryColor"
                          class="absolute -top-2 -left-2 w-16 h-16 cursor-pointer border-0 p-0"
                        />
                      </div>
                      <div class="flex-1">
                        <input 
                          type="text" 
                          v-model="config.brandIdentity.primaryColor"
                          class="w-full h-10 px-3 rounded-lg border border-gray-200 text-sm font-mono focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none uppercase"
                        />
                        <p class="text-xs text-gray-400 mt-1">Hexadecimal</p>
                      </div>
                    </div>
                  </div>

                  <!-- Plantilla -->
                  <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Plantilla</label>
                    <div class="grid grid-cols-1 gap-3">
                      <div 
                        @click="config.brandIdentity.template = 'modern-grid'"
                        class="flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition-all relative overflow-hidden"
                        :class="config.brandIdentity.template === 'modern-grid' ? 'border-emerald-500 bg-emerald-50/30 ring-1 ring-emerald-500' : 'border-gray-200 hover:bg-gray-50'"
                      >
                        <div class="p-2 bg-white rounded-lg shadow-sm border border-gray-100">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                          </svg>
                        </div>
                        <div>
                          <p class="font-bold text-gray-900">Grid Moderno</p>
                          <p class="text-xs text-gray-500">Imágenes grandes, ideal para visuales.</p>
                        </div>
                        <div v-if="config.brandIdentity.template === 'modern-grid'" class="absolute top-3 right-3 text-emerald-500">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                          </svg>
                        </div>
                      </div>

                      <div 
                        @click="config.brandIdentity.template = 'simple-list'"
                        class="flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition-all relative overflow-hidden"
                        :class="config.brandIdentity.template === 'simple-list' ? 'border-emerald-500 bg-emerald-50/30 ring-1 ring-emerald-500' : 'border-gray-200 hover:bg-gray-50'"
                      >
                        <div class="p-2 bg-white rounded-lg shadow-sm border border-gray-100">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                          </svg>
                        </div>
                        <div>
                          <p class="font-bold text-gray-900">Lista Simple</p>
                          <p class="text-xs text-gray-500">Compacto, ideal para mayoristas.</p>
                        </div>
                        <div v-if="config.brandIdentity.template === 'simple-list'" class="absolute top-3 right-3 text-emerald-500">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                          </svg>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="activeTab === 'catalog'" key="catalog" class="space-y-8">
              <div class="border-b border-gray-100 pb-4 mb-6">
                <h2 class="text-xl font-bold text-gray-800">Catálogo & Productos</h2>
                <p class="text-sm text-gray-500">Selecciona qué categorías quieres mostrar en tu tienda.</p>
              </div>

              <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-4">Categorías Visibles</label>
                
                <!-- Grid de Chips -->
                <div class="flex flex-wrap gap-3">
                  <button 
                    v-for="category in availableCategories" 
                    :key="category.id"
                    @click="toggleCategory(category.id)"
                    class="px-4 py-2 rounded-full text-sm font-medium border transition-all duration-200 flex items-center gap-2"
                    :class="config.inventoryVisibility.visibleCategories.includes(category.id) 
                      ? 'bg-emerald-50 border-emerald-500 text-emerald-700 shadow-sm' 
                      : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'"
                  >
                    <span>{{ category.name }}</span>
                    <svg v-if="config.inventoryVisibility.visibleCategories.includes(category.id)" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
                
                <p class="text-xs text-gray-400 mt-3">
                  {{ config.inventoryVisibility.visibleCategories.length }} categorías seleccionadas.
                </p>
              </div>

              <div class="pt-6 border-t border-gray-100">
                <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-200">
                  <div>
                    <p class="font-bold text-gray-900">Ocultar productos sin stock</p>
                    <p class="text-xs text-gray-500">Los productos agotados no aparecerán en el catálogo.</p>
                  </div>
                  <button 
                    @click="config.inventoryVisibility.hideOutOfStock = !config.inventoryVisibility.hideOutOfStock"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                    :class="config.inventoryVisibility.hideOutOfStock ? 'bg-emerald-500' : 'bg-gray-300'"
                  >
                    <span
                      class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                      :class="config.inventoryVisibility.hideOutOfStock ? 'translate-x-6' : 'translate-x-1'"
                    />
                  </button>
                </div>
              </div>
            </div>

            <div v-else-if="activeTab === 'orders'" key="orders" class="space-y-8">
              <div class="border-b border-gray-100 pb-4 mb-6">
                <h2 class="text-xl font-bold text-gray-800">Pedidos & WhatsApp</h2>
                <p class="text-sm text-gray-500">Configura cómo recibes los pedidos en tu celular.</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Formulario -->
                <div class="space-y-6">
                  <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Número de WhatsApp</label>
                    <input 
                      type="text" 
                      v-model="config.ordersConfig.whatsappNumber"
                      placeholder="+57 300 123 4567"
                      class="w-full h-12 px-4 rounded-xl border border-gray-200 text-base focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none"
                    />
                    <p class="text-xs text-gray-400 mt-1">Incluye el código de país (ej. +57).</p>
                  </div>

                  <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Mensaje Inicial</label>
                    <textarea 
                      v-model="config.ordersConfig.customMessage"
                      rows="4"
                      class="w-full p-4 rounded-xl border border-gray-200 text-base focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none resize-none"
                    ></textarea>
                  </div>
                </div>

                <!-- Preview -->
                <div class="bg-gray-100 rounded-2xl p-4 border border-gray-200 flex flex-col items-center justify-center">
                  <div class="w-64 bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-[#075E54] px-3 py-2 flex items-center gap-2">
                      <div class="w-6 h-6 rounded-full bg-white/20"></div>
                      <div class="text-white text-xs font-bold">WhatsApp</div>
                    </div>
                    <div class="bg-[#E5DDD5] p-4 min-h-[200px] relative">
                      <!-- Mensaje simulado -->
                      <div class="bg-white p-2 rounded-lg shadow-sm text-xs text-gray-800 max-w-[90%] relative ml-auto rounded-tr-none">
                        <p class="mb-1">{{ config.ordersConfig.customMessage }}</p>
                        <p class="font-mono text-[10px] text-gray-500 bg-gray-50 p-1 rounded border border-gray-100">
                          - 1x Producto A<br>
                          - 2x Producto B<br>
                          Total: $50.000
                        </p>
                        <div class="text-[9px] text-gray-400 text-right mt-1">10:30 AM</div>
                      </div>
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 mt-3 font-medium">Vista previa del mensaje</p>
                </div>
              </div>
            </div>

            <div v-else-if="activeTab === 'rules'" key="rules" class="space-y-8">
              <div class="border-b border-gray-100 pb-4 mb-6">
                <h2 class="text-xl font-bold text-gray-800">Reglas de Negocio</h2>
                <p class="text-sm text-gray-500">Establece condiciones para tus ventas.</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                  <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Costo de Domicilio</label>
                  <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                    <input 
                      type="number" 
                      v-model="config.businessRules.deliveryCost"
                      class="w-full h-12 pl-8 pr-4 rounded-xl border border-gray-200 text-lg font-bold text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white"
                    />
                  </div>
                  <p class="text-xs text-gray-500 mt-2">Se sumará al total del pedido.</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                  <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Pedido Mínimo</label>
                  <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                    <input 
                      type="number" 
                      v-model="config.businessRules.minimumOrder"
                      class="w-full h-12 pl-8 pr-4 rounded-xl border border-gray-200 text-lg font-bold text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white"
                    />
                  </div>
                  <p class="text-xs text-gray-500 mt-2">El cliente no podrá pedir menos de esto.</p>
                </div>
              </div>

              <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                  <h3 class="font-bold text-gray-900">Sincronizar con Caja</h3>
                  <p class="text-sm text-gray-500">Registrar automáticamente los pedidos web como ventas en el sistema POS.</p>
                </div>
                <button 
                  @click="config.businessRules.syncWithCashRegister = !config.businessRules.syncWithCashRegister"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                  :class="config.businessRules.syncWithCashRegister ? 'bg-emerald-500' : 'bg-gray-300'"
                >
                  <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                    :class="config.businessRules.syncWithCashRegister ? 'translate-x-6' : 'translate-x-1'"
                  />
                </button>
              </div>
            </div>

          </transition>
        </div>
      </section>

    </main>

    <!-- Toast Notification -->
    <div 
      v-if="showSuccessToast" 
      class="fixed bottom-6 right-6 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-slide-up z-50"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      <div>
        <h4 class="font-bold text-sm">¡Cambios Guardados!</h4>
        <p class="text-xs opacity-90">Tu tienda se ha actualizado correctamente.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { appStore } from '../store/appStore.js'
import apiClient from '../services/apiClient.js'

// Refs for file inputs
const logoInput = ref(null)
const bannerInput = ref(null)

// State
const isSaving = ref(false)
const showSuccessToast = ref(false)
const isLoading = ref(true)
const activeTab = ref('identity')

// Tabs Configuration
const tabs = [
  { id: 'identity', label: 'Identidad & Diseño', icon: '🖌️' },
  { id: 'catalog', label: 'Catálogo & Productos', icon: '📦' },
  { id: 'orders', label: 'Pedidos & WhatsApp', icon: '💬' },
  { id: 'rules', label: 'Reglas de Negocio', icon: '⚖️' }
]

// Configuration Object (Reactive)
const config = reactive({
  storeActive: true,
  brandIdentity: {
    logo: '', // URL or base64
    banner: '',
    primaryColor: '#10B981', // Emerald default
    template: 'modern-grid' // 'simple-list' or 'modern-grid'
  },
  inventoryVisibility: {
    visibleCategories: [], // Array of category IDs
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
    // Fallback mock data if store is empty
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

  // Validate file size (max 2MB)
  if (file.size > 2 * 1024 * 1024) {
    alert('El archivo es muy grande. Máximo 2MB.')
    return
  }

  // Convert to base64 for preview (in production, upload to server)
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

// View store (open catalog URL)
const viewStore = () => {
  // Get tenant subdomain from current URL or construct it
  const currentHost = window.location.hostname
  let catalogUrl = ''
  
  if (currentHost.includes('localhost') || currentHost.includes('127.0.0.1')) {
    catalogUrl = `${window.location.protocol}//${currentHost}:${window.location.port}/catalog`
  } else {
    catalogUrl = `${window.location.protocol}//${currentHost}/catalog`
  }
  
  window.open(catalogUrl, '_blank')
}

// Load configuration from backend
const loadConfiguration = async () => {
  try {
    const response = await apiClient.get('/web-catalog/config')
    
    if (response.data.success) {
      const data = response.data.data
      
      // Map backend data to config structure
      config.storeActive = data.store_active ?? true
      config.brandIdentity.logo = data.logo_url || ''
      config.brandIdentity.banner = data.banner_url || ''
      config.brandIdentity.primaryColor = data.primary_color || '#10B981'
      config.brandIdentity.template = data.template || 'modern-grid'
      
      // Parse visible categories (comes as JSON array)
      const visibleCats = Array.isArray(data.visible_categories) ? data.visible_categories : []
      
      // If empty, select all categories by default
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
      
      console.log('✅ Configuración cargada:', config)
    }
  } catch (error) {
    console.error('Error loading configuration:', error)
    // If no configuration exists, use defaults (already set)
    // Select all categories by default if none are selected
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
      console.log('✅ Configuración guardada:', config)
      
      // Show success toast
      showSuccessToast.value = true
      setTimeout(() => {
        showSuccessToast.value = false
      }, 3000)
    }
  } catch (error) {
    console.error('Error saving configuration:', error)
    alert('Error al guardar la configuración. Por favor intenta de nuevo.')
  } finally {
    isSaving.value = false
  }
}
</script>

<style scoped>
/* Fade Transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes slide-up {
  from {
    transform: translateY(100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
.animate-slide-up {
  animation: slide-up 0.3s ease-out forwards;
}
</style>
