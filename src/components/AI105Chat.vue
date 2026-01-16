<template>
  <!-- Panel Lateral de Chat IA - Estilo Hostinger hPanel -->
  <div>
    <!-- Panel Lateral de Chat IA - Estilo Hostinger hPanel -->
    <transition name="slide-right">
      <div 
        v-if="localChatOpen"
        class="fixed right-0 w-full sm:w-[380px] bg-white dark:bg-[#18181b] flex flex-col z-[45] shadow-[-2px_0_20px_rgba(0,0,0,0.08)] dark:shadow-[-2px_0_30px_rgba(0,0,0,0.5)]"
        :style="{ top: dynamicHeaderHeight + 'px', height: `calc(100% - ${dynamicHeaderHeight}px)` }"
      >
        <!-- Header del Chat -->
        <div class="flex items-center justify-between h-11 px-3 border-b border-gray-100 dark:border-zinc-800 flex-shrink-0">
          <!-- Tabs -->
          <div class="flex items-center gap-1">
            <button
              @click="activeTab = 'chat'"
              :class="[
                'px-2.5 py-1 text-sm font-medium rounded-md transition-all',
                activeTab === 'chat'
                  ? 'bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white'
                  : 'text-gray-500 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300'
              ]"
            >Chat</button>
            <button
              @click="activeTab = 'history'"
              :class="[
                'px-2.5 py-1 text-sm font-medium rounded-md transition-all',
                activeTab === 'history'
                  ? 'bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white'
                  : 'text-gray-500 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300'
              ]"
            >Historia</button>
          </div>
          
          <!-- Controles -->
          <div class="flex items-center gap-0.5">
            <button 
              v-if="messages.length > 0"
              @click="startNewConversation"
              class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded transition-all"
              title="Nueva conversación"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
              </svg>
            </button>
            <!-- Cerrar con línea vertical estilo Hostinger -->
            <button 
              @click="closeChat"
              class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all"
              title="Cerrar chat"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Contenido del Chat -->
        <div v-show="activeTab === 'chat'" class="flex-1 flex flex-col overflow-hidden">
          <!-- Área de Mensajes -->
          <div ref="messagesContainer" class="flex-1 overflow-y-auto">
            
            <!-- Estado vacío - Bienvenida -->
            <div v-if="messages.length === 0" class="h-full flex flex-col items-center justify-center px-5 py-6">
              <!-- Logo 105 elegante -->
              <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-emerald-500/20">
                <span class="text-white font-bold text-lg">105</span>
              </div>
              
              <!-- Saludo -->
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                Hola, {{ userName }} 👋
              </h2>
              <p class="text-sm text-gray-600 dark:text-zinc-400 mb-6 font-medium">
                ¿En qué te puedo ayudar hoy?
              </p>
              
              <!-- Sugerencias con hover que muestra en input -->
              <div class="w-full space-y-2 mb-5">
                <button
                  v-for="(suggestion, index) in quickSuggestions"
                  :key="index"
                  @click="sendQuickMessage(suggestion)"
                  @mouseenter="hoverSuggestion = suggestion.text"
                  @mouseleave="hoverSuggestion = ''"
                  class="w-full flex items-center gap-3 px-4 py-3 text-left rounded-lg border border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-all duration-150 cursor-pointer group"
                >
                  <svg class="w-4 h-4 text-gray-400 dark:text-zinc-600 group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span class="text-sm font-medium text-gray-700 dark:text-zinc-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">{{ suggestion.text }}</span>
                </button>
              </div>
            </div>

            <!-- Mensajes del Chat -->
            <div v-else class="px-4 py-3 space-y-3">
              <div 
                v-for="(message, index) in messages" 
                :key="index"
                class="animate-fade-in"
              >
                <!-- Mensaje del usuario -->
                <div v-if="message.type === 'user'" class="flex justify-end mb-3">
                  <div class="max-w-[85%] bg-gray-900 dark:bg-zinc-700 text-white px-3.5 py-2 rounded-2xl rounded-br-sm">
                    <p class="text-sm whitespace-pre-line leading-relaxed">{{ message.text }}</p>
                  </div>
                </div>

                <!-- Mensaje de la IA -->
                <div v-else class="flex gap-2 mb-3">
                  <!-- Avatar IA -->
                  <div class="w-6 h-6 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-md flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold text-[8px]">105</span>
                  </div>

                  <div class="flex-1 max-w-[88%]">
                    <div
                      class="px-3 py-2 rounded-2xl rounded-tl-sm text-sm"
                      :class="[
                        message.isLimit ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50' :
                        message.isError ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50' :
                        message.isInfo ? 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50' :
                        message.isWarning ? 'bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800/50' :
                        'bg-gray-100 dark:bg-zinc-800'
                      ]"
                    >
                      <!-- Límite -->
                      <div v-if="message.isLimit" class="space-y-2">
                        <p class="text-amber-700 dark:text-amber-400 font-medium text-xs">⏰ Límite diario alcanzado</p>
                        <p class="text-gray-600 dark:text-zinc-300 text-sm">Has usado tus {{ message.limitData?.used || 10 }} mensajes. ¡Mañana tendrás más!</p>
                        <button @click="$emit('navigate', 'upgrade')" class="text-xs bg-gradient-to-r from-violet-500 to-purple-600 text-white px-3 py-1.5 rounded-lg font-medium">
                          💎 Obtener Premium
                        </button>
                      </div>
                      <div v-else-if="message.isInfo"><p class="text-blue-700 dark:text-blue-400">{{ message.text }}</p></div>
                      <div v-else-if="message.isWarning"><p class="text-orange-700 dark:text-orange-400">{{ message.text }}</p></div>
                      <p v-else class="text-gray-700 dark:text-zinc-200 whitespace-pre-line leading-relaxed">{{ message.text }}</p>
                      
                      <button 
                        v-if="message.suggested_action"
                        @click="executeSuggestedAction(message.suggested_action)"
                        class="mt-2 w-full py-1.5 px-3 bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 text-xs font-medium rounded-lg border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-center gap-1.5"
                      >
                        {{ message.suggested_action.label || 'Ver detalles' }}
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Typing indicator -->
              <div v-if="isTyping" class="flex gap-2 animate-fade-in">
                <div class="w-6 h-6 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-md flex items-center justify-center flex-shrink-0">
                  <span class="text-white font-bold text-[8px]">105</span>
                </div>
                <div class="bg-gray-100 dark:bg-zinc-800 rounded-2xl rounded-tl-sm px-3 py-2.5">
                  <div class="flex gap-1">
                    <div class="w-1.5 h-1.5 bg-gray-400 dark:bg-zinc-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-1.5 h-1.5 bg-gray-400 dark:bg-zinc-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-1.5 h-1.5 bg-gray-400 dark:bg-zinc-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="flex-shrink-0 border-t border-gray-100 dark:border-zinc-800 p-3">
            <!-- Categorías inteligentes -->
            <div class="flex flex-wrap gap-1.5 mb-3">
              <button
                v-for="category in categories"
                :key="category.id"
                @click="setCategory(category)"
                :class="[
                  'px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all',
                  selectedCategory === category.id
                    ? 'bg-emerald-500 dark:bg-emerald-600 border-emerald-500 dark:border-emerald-600 text-white shadow-sm'
                    : 'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 hover:border-gray-300 dark:hover:border-zinc-600 hover:bg-gray-50 dark:hover:bg-zinc-700'
                ]"
              >{{ category.name }}</button>
            </div>
            
            <!-- Input inteligente -->
            <form @submit.prevent="sendMessage" class="mb-2">
              <div class="relative bg-gray-50 dark:bg-zinc-800/60 rounded-xl border border-gray-200 dark:border-zinc-700 focus-within:border-gray-300 dark:focus-within:border-zinc-600 transition-colors">
                <textarea
                  ref="messageInput"
                  v-model="inputMessage"
                  @keydown.enter.exact.prevent="sendMessage"
                  @input="autoResizeTextarea"
                  @focus="hoverSuggestion = ''"
                  :placeholder="hoverSuggestion || 'Escribe tu pregunta...'"
                  rows="1"
                  class="w-full bg-transparent border-none focus:outline-none resize-none text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 py-2.5 pl-3 pr-11 min-h-[40px] max-h-[120px]"
                  style="line-height: 1.5;"
                ></textarea>
                <button
                  type="submit"
                  :disabled="!inputMessage.trim() || isTyping"
                  :class="[
                    'absolute right-1.5 bottom-1.5 p-1.5 rounded-lg transition-all',
                    inputMessage.trim() && !isTyping
                      ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100'
                      : 'bg-gray-200 dark:bg-zinc-700 text-gray-400 dark:text-zinc-500'
                  ]"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                  </svg>
                </button>
              </div>
            </form>

            <!-- Selector IA compacto -->
            <div class="flex items-center justify-center gap-1.5">
              <button
                @click="selectedProvider = 'gemini'"
                :class="[
                  'flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-medium transition-all border',
                  selectedProvider === 'gemini'
                    ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-400 dark:border-emerald-600 text-emerald-700 dark:text-emerald-400'
                    : 'border-gray-200 dark:border-zinc-700 text-gray-400 dark:text-zinc-500 hover:border-gray-300'
                ]"
              >
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0L14.59 8.41L23 11L14.59 13.59L12 22L9.41 13.59L1 11L9.41 8.41L12 0Z"/></svg>
                Gemini
              </button>
              <button
                @click="selectedProvider = 'groq'"
                :class="[
                  'flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-medium transition-all border',
                  selectedProvider === 'groq'
                    ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-400 dark:border-blue-600 text-blue-700 dark:text-blue-400'
                    : 'border-gray-200 dark:border-zinc-700 text-gray-400 dark:text-zinc-500 hover:border-gray-300'
                ]"
              >
                <!-- Meta Infinity Logo -->
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M6.5 8c-1.93 0-3.5 1.57-3.5 3.5 0 1.05.47 1.99 1.2 2.64L1.93 18.5h3.57l1.5-3h2.5l1.5 3h3.57l-2.27-4.36c.73-.65 1.2-1.59 1.2-2.64C13.5 9.57 11.93 8 10 8H6.5zm0 2h3.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5H6.5c-.83 0-1.5-.67-1.5-1.5S5.67 10 6.5 10zm8.5-2c-1.93 0-3.5 1.57-3.5 3.5 0 1.05.47 1.99 1.2 2.64L10.43 18.5H14l1.5-3h2.5l1.5 3h3.57l-2.27-4.36c.73-.65 1.2-1.59 1.2-2.64C22 9.57 20.43 8 18.5 8H15zm0 2h3.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5H15c-.83 0-1.5-.67-1.5-1.5S14.17 10 15 10z"/>
                </svg>
                Meta
              </button>
            </div>

            <p class="text-center text-[9px] text-gray-400 dark:text-zinc-600 mt-1.5">La IA puede producir información inexacta</p>
          </div>
        </div>

        <!-- Tab Historia -->
        <div v-show="activeTab === 'history'" class="flex-1 overflow-y-auto p-4 bg-gray-50/50 dark:bg-[#141417]">
          <div v-if="chatHistory.length === 0" class="h-full flex flex-col items-center justify-center text-center px-4">
            <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800/60 rounded-xl flex items-center justify-center mb-3">
              <svg class="w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-gray-700 dark:text-zinc-300 font-medium text-sm mb-0.5">Sin historial</h3>
            <p class="text-xs text-gray-500 dark:text-zinc-500">Tus conversaciones aparecerán aquí</p>
          </div>
          
          <div v-else class="space-y-2">
            <button
              v-for="(session, index) in chatHistory"
              :key="index"
              @click="loadSession(session)"
              class="w-full text-left p-3 bg-white dark:bg-zinc-800/60 hover:bg-gray-50 dark:hover:bg-zinc-800 border border-gray-100 dark:border-zinc-700/50 rounded-lg transition-all cursor-pointer group hover:shadow-sm"
            >
              <p class="text-sm font-medium text-gray-700 dark:text-zinc-300 truncate group-hover:text-gray-900 dark:group-hover:text-white">{{ session.title || 'Conversación' }}</p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">{{ session.date }}</p>
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref, nextTick, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useModuleNavigation } from '@/composables/useModuleNavigation'
import { aiChatStore } from '@/store/aiChatStore'
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
  emits: ['close', 'navigate'],
  setup(props, { emit }) {
    const router = useRouter()
    const { navigateToModule } = useModuleNavigation()
    
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
    
    // Selector de proveedor IA
    const selectedProvider = ref('gemini')
    const userPlan = ref('basic')
    
    // Sistema de límites - 10 mensajes por día para Basic
    const messageLimit = ref(10)
    const messagesUsedToday = ref(0)
    
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

    // Cargar estadísticas de uso y plan del usuario
    const loadUsageStats = async () => {
      try {
        const providerResponse = await api.get('/ai/provider-config')
        if (providerResponse.success && providerResponse.data) {
          const config = providerResponse.data
          selectedProvider.value = config.default || 'groq'
          userPlan.value = config.current_plan || 'basic'
        }
        
        const today = new Date().toISOString().split('T')[0]
        const storedData = JSON.parse(localStorage.getItem('ai_daily_usage') || '{}')
        
        if (storedData.date !== today) {
          messagesUsedToday.value = 0
          localStorage.setItem('ai_daily_usage', JSON.stringify({ date: today, count: 0 }))
        } else {
          messagesUsedToday.value = storedData.count || 0
        }
        
        if (userPlan.value === 'premium' || userPlan.value === 'enterprise') {
          messageLimit.value = 0
        } else {
          messageLimit.value = 10
        }
      } catch (error) {
        selectedProvider.value = 'groq'
        userPlan.value = 'basic'
        messageLimit.value = 10
        messagesUsedToday.value = 0
      }
    }

    onMounted(() => {
      loadUsageStats()
      calculateHeaderOffset()
      // Recalcular cuando cambie el tamaño de la ventana
      window.addEventListener('resize', calculateHeaderOffset)
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

      if (messageLimit.value > 0 && messagesUsedToday.value >= messageLimit.value) {
        messages.value.push({
          type: 'ai',
          text: '',
          timestamp: getCurrentTime(),
          isLimit: true,
          limitData: {
            used: messageLimit.value,
            renewalText: `Mañana tendrás ${messageLimit.value} mensajes nuevos disponibles.`
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
      
      try {
        let response

        if (file) {
          const formData = new FormData()
          formData.append('message', userMessage)
          formData.append('file', file)
          formData.append('provider', selectedProvider.value)
          if (sessionId.value) formData.append('session_id', sessionId.value)

          response = await api.post('/ai/chat-with-file', formData, { headers: {} })
          clearFile()
        } else {
          response = await api.post('/ai/chat', {
            message: userMessage,
            provider: selectedProvider.value,
            session_id: sessionId.value
          })
        }

        if (response.session_id) sessionId.value = response.session_id

        messagesUsedToday.value++
        const today = new Date().toISOString().split('T')[0]
        localStorage.setItem('ai_daily_usage', JSON.stringify({ date: today, count: messagesUsedToday.value }))

        let aiReply = response.reply
        let aiAction = null
        let suggestedAction = null

        try {
          if (typeof response.reply === 'string' && response.reply.trim().startsWith('{')) {
            const parsed = JSON.parse(response.reply)
            aiReply = parsed.reply || parsed.text || response.reply
            aiAction = parsed.action
            suggestedAction = parsed.suggested_action
          } else if (typeof response.reply === 'object') {
            aiReply = response.reply.reply
            aiAction = response.reply.action
            suggestedAction = response.reply.suggested_action
          }
        } catch (e) {}

        messages.value.push({
          type: 'ai',
          text: aiReply,
          timestamp: getCurrentTime(),
          suggested_action: suggestedAction
        })

        if (messageLimit.value > 0) {
          const messagesRemaining = messageLimit.value - messagesUsedToday.value
          
          if (messagesRemaining === 3) {
            messages.value.push({
              type: 'ai',
              text: `ℹ️ Te quedan 3 mensajes hoy.`,
              timestamp: getCurrentTime(),
              isInfo: true
            })
          } else if (messagesRemaining === 1) {
            messages.value.push({
              type: 'ai',
              text: `⚠️ Este es tu último mensaje de hoy.`,
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

    const autoResizeTextarea = () => {
      const textarea = messageInput.value
      if (textarea) {
        textarea.style.height = 'auto'
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px'
      }
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
      userPlan,
      messageLimit,
      messagesUsedToday,
      selectedFile,
      quickSuggestions,
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
      clearFile
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

/* Fade in para mensajes */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.2s ease-out;
}

/* Scrollbar personalizado */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 2px;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: #3f3f46;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #52525b;
}
</style>
