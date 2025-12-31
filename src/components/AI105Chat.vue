<template>
  <!-- Botón Flotante de IA (solo si no es controlado externamente) -->
  <div>
    <button 
      v-if="!isControlledExternally"
      @click="toggleChat"
      class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 rounded-2xl shadow-2xl shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all duration-300 transform hover:scale-105 flex items-center justify-center group z-50"
      :class="{ 'scale-95': localChatOpen }"
    >
      <!-- Indicador de estado online -->
      <div class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-400 rounded-full border-2 border-white shadow-lg animate-pulse"></div>
      
      <!-- Icono de IA (Sparkles) -->
      <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
      </svg>
    </button>

    <!-- Overlay para cerrar en móviles -->
    <transition name="fade">
      <div
        v-if="localChatOpen"
        @click="closeChat"
        class="fixed inset-0 bg-black/30 backdrop-blur-sm z-[9998] md:bg-black/10"
      ></div>
    </transition>

    <!-- Panel de Chat IA - Diseño tipo Gemini -->
    <transition name="slide-up">
      <div 
        v-if="localChatOpen"
        class="fixed bottom-0 right-0 md:bottom-6 md:right-6 w-full md:w-[420px] h-[85vh] md:h-[700px] md:max-h-[85vh] bg-white dark:bg-zinc-900 md:rounded-3xl shadow-2xl flex flex-col z-[9999] overflow-hidden border border-gray-200/50 dark:border-zinc-700/50"
      >
        <!-- Header Minimalista -->
        <div class="px-5 py-4 flex items-center justify-between bg-white dark:bg-zinc-900 border-b border-gray-100 dark:border-zinc-800">
          <div class="flex items-center gap-3">
            <!-- Logo IA -->
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">105 IA</h3>
              <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-xs text-gray-500 dark:text-zinc-400">Online</span>
              </div>
            </div>
          </div>
          
          <div class="flex items-center gap-1">
            <!-- Botón Nueva Conversación -->
            <button 
              v-if="messages.length > 0"
              @click="startNewConversation"
              class="p-2 text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition-all"
              title="Nueva conversación"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
            </button>
            
            <!-- Botón Cerrar -->
            <button 
              @click="closeChat"
              class="p-2 text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition-all"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Área de Mensajes -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto px-5 py-4 space-y-4 bg-gray-50/50 dark:bg-zinc-900/50">
          
          <!-- Estado vacío - Bienvenida tipo Gemini -->
          <div v-if="messages.length === 0" class="h-full flex flex-col items-center justify-center text-center px-4">
            <!-- Icono grande con gradiente -->
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/30 dark:to-teal-900/30 rounded-3xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/10">
              <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
              </svg>
            </div>
            
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">¿En qué puedo ayudarte?</h2>
            <p class="text-sm text-gray-500 dark:text-zinc-400 mb-8 max-w-[280px]">
              Soy tu asistente inteligente. Puedo consultar ventas, inventario, crear productos y más.
            </p>
            
            <!-- Sugerencias rápidas tipo chips -->
            <div class="flex flex-wrap justify-center gap-2 max-w-[320px]">
              <button
                v-for="(suggestion, index) in quickSuggestions"
                :key="index"
                @click="sendQuickMessage(suggestion)"
                class="px-4 py-2 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-full text-sm text-gray-700 dark:text-zinc-300 hover:border-emerald-300 dark:hover:border-emerald-600 hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-200 shadow-sm"
              >
                {{ suggestion.text }}
              </button>
            </div>

            <!-- Límite de mensajes (visible solo si hay límite) -->
            <div v-if="messageLimit > 0" class="mt-8 flex items-center gap-2 text-xs text-gray-400 dark:text-zinc-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span>{{ messagesUsedThisHour }}/{{ messageLimit }} mensajes esta hora</span>
            </div>
          </div>

          <!-- Mensajes del Chat -->
          <div 
            v-for="(message, index) in messages" 
            :key="index"
            class="animate-fade-in"
          >
            <!-- Mensaje del usuario -->
            <div v-if="message.type === 'user'" class="flex justify-end mb-4">
              <div class="max-w-[85%] bg-emerald-600 text-white px-4 py-3 rounded-2xl rounded-br-md shadow-sm">
                <p class="text-sm whitespace-pre-line">{{ message.text }}</p>
              </div>
            </div>

            <!-- Mensaje de la IA -->
            <div v-else class="flex gap-3 mb-4">
              <!-- Avatar IA -->
              <div class="w-8 h-8 bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/50 dark:to-teal-900/50 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
              </div>

              <div class="flex-1 max-w-[85%]">
                <!-- Contenedor del mensaje -->
                <div
                  class="px-4 py-3 rounded-2xl rounded-tl-md shadow-sm text-sm"
                  :class="[
                    message.isLimit
                      ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800'
                      : message.isError
                        ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'
                        : 'bg-white dark:bg-zinc-800 border border-gray-100 dark:border-zinc-700'
                  ]"
                >
                  <!-- Mensaje de límite -->
                  <div v-if="message.isLimit" class="space-y-2">
                    <div class="flex items-center gap-2 text-amber-700 dark:text-amber-400 font-medium">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      Límite alcanzado
                    </div>
                    <p class="text-gray-600 dark:text-zinc-300 whitespace-pre-line">{{ message.text }}</p>
                  </div>
                  
                  <!-- Mensaje normal -->
                  <p v-else class="text-gray-700 dark:text-zinc-200 whitespace-pre-line leading-relaxed">{{ message.text }}</p>

                  <!-- Botón de acción sugerida -->
                  <button 
                    v-if="message.suggested_action"
                    @click="executeSuggestedAction(message.suggested_action)"
                    class="mt-3 w-full py-2 px-3 bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 text-xs font-medium rounded-xl border border-emerald-200 dark:border-emerald-800 transition-colors flex items-center justify-center gap-2"
                  >
                    <span>{{ message.suggested_action.label || 'Ver detalles' }}</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                  </button>
                </div>

                <!-- Timestamp -->
                <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-1 px-1">{{ message.timestamp }}</p>
              </div>
            </div>
          </div>

          <!-- Indicador de escritura -->
          <div v-if="isTyping" class="flex gap-3 animate-fade-in">
            <div class="w-8 h-8 bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/50 dark:to-teal-900/50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
              </svg>
            </div>
            <div class="bg-white dark:bg-zinc-800 border border-gray-100 dark:border-zinc-700 rounded-2xl rounded-tl-md px-4 py-3">
              <div class="flex gap-1">
                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer con Input y Selector de Motor -->
        <div class="bg-white dark:bg-zinc-900 border-t border-gray-100 dark:border-zinc-800 p-4">
          <!-- Input de mensaje -->
          <form @submit.prevent="sendMessage" class="mb-3">
            <div class="flex items-end gap-2 bg-gray-50 dark:bg-zinc-800 rounded-2xl p-1.5 border border-gray-200 dark:border-zinc-700 focus-within:border-emerald-400 dark:focus-within:border-emerald-500 transition-colors">
              
              <!-- Botón de adjuntar archivo -->
              <label class="p-2.5 text-gray-400 hover:text-emerald-600 dark:text-zinc-500 dark:hover:text-emerald-400 cursor-pointer transition-colors">
                <input 
                  type="file" 
                  ref="fileInput"
                  @change="handleFileSelect"
                  accept=".xlsx,.xls,.csv,image/*"
                  class="hidden"
                />
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
              </label>

              <textarea
                ref="messageInput"
                v-model="inputMessage"
                @keydown.enter.exact.prevent="sendMessage"
                @keydown.enter.shift.exact="handleShiftEnter"
                @input="autoResize"
                placeholder="Escribe un mensaje..."
                rows="1"
                class="flex-1 bg-transparent border-none focus:outline-none resize-none text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 py-2.5 px-1 max-h-[100px] overflow-y-auto"
              ></textarea>

              <button
                type="submit"
                :disabled="(!inputMessage.trim() && !selectedFile) || isTyping"
                class="p-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 dark:disabled:bg-zinc-700 text-white rounded-xl transition-all disabled:cursor-not-allowed shadow-sm"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
              </button>
            </div>
          </form>

          <!-- Archivo seleccionado (preview) -->
          <div v-if="selectedFile" class="mb-3 flex items-center gap-2 px-3 py-2 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl">
            <svg v-if="selectedFile.type.includes('image')" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <svg v-else class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="flex-1 text-sm text-emerald-700 dark:text-emerald-400 truncate">{{ selectedFile.name }}</span>
            <button @click="clearFile" class="p-1 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 rounded-lg transition-colors">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Selector de Motor IA y Uso -->
          <div class="flex items-center justify-between">
            <!-- Selector de modelo -->
            <div class="flex items-center gap-2 bg-gray-100 dark:bg-zinc-800 rounded-xl p-1">
              <button
                @click="selectedProvider = 'groq'"
                :class="[
                  'px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5',
                  selectedProvider === 'groq'
                    ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm'
                    : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300'
                ]"
              >
                <!-- Meta Logo (Llama) -->
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 6.5c-2.6 0-4.8 1.6-5.8 3.9-.3.7-1.1 1.1-1.8 1.1-1.4 0-2.5-1.1-2.5-2.5s1.1-2.5 2.5-2.5c.9 0 1.7.5 2.2 1.2.3.4.9.5 1.3.2.4-.3.5-.9.2-1.3-.8-1.1-2.1-1.9-3.6-1.9-2.5 0-4.5 2-4.5 4.5s2 4.5 4.5 4.5c1.2 0 2.3-.5 3.1-1.3 1.1-1.1 2.1-3.4 4.4-3.4 2.3 0 3.3 2.3 4.4 3.4.8.8 1.9 1.3 3.1 1.3 2.5 0 4.5-2 4.5-4.5S22 4.5 19.5 4.5c-1.5 0-2.8.8-3.6 1.9-.3.4-.2 1 .2 1.3.4.3 1 .2 1.3-.2.5-.7 1.3-1.2 2.2-1.2 1.4 0 2.5 1.1 2.5 2.5s-1.1 2.5-2.5 2.5c-.7 0-1.5-.4-1.8-1.1-1-2.3-3.2-3.9-5.8-3.9z"/>
                </svg>
                Llama 3 (Meta)
              </button>
              <button
                @click="selectedProvider = 'gemini'"
                :class="[
                  'px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5',
                  selectedProvider === 'gemini'
                    ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm'
                    : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300'
                ]"
              >
                <!-- Google Gemini Logo -->
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M19 9l1.25-2.75L23 5l-2.75-1.25L19 1l-1.25 2.75L15 5l2.75 1.25L19 9zm-7.5.5L9 4 6.5 9.5 1 12l5.5 2.5L9 20l2.5-5.5L17 12l-5.5-2.5zM19 15l-1.25 2.75L15 19l2.75 1.25L19 23l1.25-2.75L23 19l-2.75-1.25L19 15z"/>
                </svg>
                Gemini 2.5
              </button>
            </div>

            <!-- Uso de mensajes -->
            <div v-if="messageLimit > 0" class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-zinc-500">
              <div class="w-16 h-1.5 bg-gray-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                <div 
                  class="h-full bg-emerald-500 rounded-full transition-all duration-300"
                  :style="{ width: `${Math.min((messagesUsedThisHour / messageLimit) * 100, 100)}%` }"
                  :class="{ 'bg-amber-500': messagesUsedThisHour >= messageLimit * 0.8, 'bg-red-500': messagesUsedThisHour >= messageLimit }"
                ></div>
              </div>
              <span>{{ messagesUsedThisHour }}/{{ messageLimit }}</span>
            </div>
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
import api from '@/services/api'

export default {
  name: 'AI105Chat',
  props: {
    isOpen: {
      type: Boolean,
      default: undefined
    }
  },
  emits: ['close', 'navigate'],
  setup(props, { emit }) {
    const router = useRouter()
    const { navigateToModule } = useModuleNavigation()
    
    // Estado del chat
    const isChatOpen = ref(false)
    const messages = ref([])
    const inputMessage = ref('')
    const isTyping = ref(false)
    const messagesContainer = ref(null)
    const messageInput = ref(null)
    const fileInput = ref(null)
    const sessionId = ref(null)
    
    // Selector de proveedor IA
    const selectedProvider = ref('gemini') // Cambiado a gemini por defecto
    const userPlan = ref('basic') // basic, premium, enterprise
    
    // Sistema de límites
    const messageLimit = ref(10) // Por defecto plan básico
    const messagesUsedThisHour = ref(0)
    
    // Archivo seleccionado
    const selectedFile = ref(null)

    // Computed
    const isControlledExternally = computed(() => props.isOpen !== undefined)
    const localChatOpen = computed(() => isControlledExternally.value ? props.isOpen : isChatOpen.value)

    // Sugerencias rápidas
    const quickSuggestions = [
      { text: '¿Hubo ventas hoy?' },
      { text: 'Productos con stock bajo' },
      { text: 'Resumen del inventario' },
      { text: 'Crear un producto' }
    ]

    // Watch para sincronizar cuando cambia isOpen desde fuera
    watch(() => props.isOpen, (newVal) => {
      if (newVal !== undefined && newVal) {
        nextTick(() => messageInput.value?.focus())
        loadUsageStats()
      }
    })

    // Cargar estadísticas de uso y plan del usuario
    const loadUsageStats = async () => {
      try {
        // Cargar estadísticas de uso
        const usageResponse = await api.get('/ai/usage-stats')
        if (usageResponse.success && usageResponse.data) {
          const data = usageResponse.data
          messagesUsedThisHour.value = data.usage?.last_hour?.requests || 0
          
          // Obtener límites según el plan
          if (data.limits && !data.limits.unlimited) {
            messageLimit.value = data.limits.limits?.requests_per_hour || 10
          } else if (data.limits?.unlimited) {
            messageLimit.value = 0 // 0 = ilimitado
          }
          
          userPlan.value = data.plan || 'free_trial'
        }

        // Cargar configuración de proveedor según plan
        const providerResponse = await api.get('/ai/provider-config')
        if (providerResponse.success && providerResponse.data) {
          const config = providerResponse.data
          selectedProvider.value = config.default || 'groq'
          userPlan.value = config.current_plan || userPlan.value
        }
      } catch (error) {
        // Si falla, usar valores por defecto
        selectedProvider.value = 'groq'
        messageLimit.value = 10
        messagesUsedThisHour.value = 0
      }
    }

    onMounted(() => {
      loadUsageStats()
    })

    const toggleChat = () => {
      if (isControlledExternally.value) {
        emit('close')
      } else {
        isChatOpen.value = !isChatOpen.value
        if (isChatOpen.value) {
          nextTick(() => messageInput.value?.focus())
          loadUsageStats()
        }
      }
    }

    const closeChat = () => {
      if (isControlledExternally.value) {
        emit('close')
      } else {
        isChatOpen.value = false
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

    // Manejo de archivos
    const handleFileSelect = (event) => {
      const file = event.target.files[0]
      if (file) {
        selectedFile.value = file
        // Auto-generar mensaje según tipo de archivo
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

      // Verificar límite local antes de enviar
      if (messageLimit.value > 0 && messagesUsedThisHour.value >= messageLimit.value) {
        messages.value.push({
          type: 'ai',
          text: `Has alcanzado tu límite de ${messageLimit.value} mensajes por hora.\n\nActualiza tu plan para obtener más mensajes:\n• Premium: 50 mensajes/hora\n• Enterprise: Ilimitado`,
          timestamp: getCurrentTime(),
          isLimit: true
        })
        scrollToBottom()
        return
      }

      const userMessage = inputMessage.value.trim()
      const file = selectedFile.value

      console.log('🔍 [AI Chat] Enviando mensaje', {
        hasFile: !!file,
        fileName: file?.name,
        fileSize: file?.size,
        provider: selectedProvider.value
      })

      // Agregar mensaje del usuario
      messages.value.push({
        type: 'user',
        text: file ? `${userMessage}\n📎 ${file.name}` : userMessage,
        timestamp: getCurrentTime()
      })

      inputMessage.value = ''
      // NO limpiar el archivo aún - mantener referencia para la petición
      scrollToBottom()

      isTyping.value = true
      
      try {
        let response

        // Si hay archivo, usar endpoint especial
        if (file) {
          const formData = new FormData()
          formData.append('message', userMessage)
          formData.append('file', file)
          formData.append('provider', selectedProvider.value)
          if (sessionId.value) formData.append('session_id', sessionId.value)

          console.log('📤 [AI Chat] FormData preparado', {
            hasMessage: formData.has('message'),
            hasFile: formData.has('file'),
            hasProvider: formData.has('provider')
          })

          // ⚠️ IMPORTANTE: NO establecer Content-Type, dejar que el navegador lo haga automáticamente
          response = await api.post('/ai/chat-with-file', formData, {
            headers: {
              // NO poner Content-Type aquí - el navegador lo hace con boundary correcto
            }
          })
          
          // Limpiar archivo DESPUÉS de enviar
          clearFile()
        } else {
          response = await api.post('/ai/chat', {
            message: userMessage,
            provider: selectedProvider.value,
            session_id: sessionId.value
          })
        }

        // Guardar session_id
        if (response.session_id) sessionId.value = response.session_id

        // Incrementar uso local
        messagesUsedThisHour.value++

        // Parsear respuesta
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
        } catch (e) {
          console.warn('Error parsing AI response:', e)
        }

        messages.value.push({
          type: 'ai',
          text: aiReply,
          timestamp: getCurrentTime(),
          suggested_action: suggestedAction
        })

        // Ejecutar navegación si existe
        if (aiAction && aiAction.type === 'navigate' && aiAction.payload) {
          const targetModule = aiAction.payload.params?.module
          const queryParams = aiAction.payload.query || {}
          if (targetModule) navigateToModule(targetModule, queryParams)
        }

      } catch (error) {
        console.error('Error al contactar IA:', error)
        
        if (error.response?.status === 429) {
          const errorData = error.response.data
          let errorMessage = 'Has alcanzado tu límite de mensajes por hora.'
          
          if (errorData.minutes_remaining) {
            const mins = Math.ceil(errorData.minutes_remaining)
            errorMessage = `Has alcanzado tu límite de ${errorData.usage?.limit_hour || messageLimit.value} mensajes por hora.\n\nDisponible en ${mins} minuto${mins !== 1 ? 's' : ''}.\n\nActualiza tu plan para más mensajes.`
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
            text: 'Lo siento, tuve un problema al procesar tu solicitud. Por favor intenta de nuevo.',
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
      sendMessage()
    }

    const startNewConversation = async () => {
      if (sessionId.value) {
        try {
          await api.post('/ai/clear-history', { session_id: sessionId.value })
        } catch (error) {
          console.error('Error clearing history:', error)
        }
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

    const autoResize = () => {
      const textarea = messageInput.value
      if (textarea) {
        textarea.style.height = 'auto'
        textarea.style.height = Math.min(textarea.scrollHeight, 100) + 'px'
      }
    }

    return {
      isChatOpen,
      localChatOpen,
      isControlledExternally,
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
      messagesUsedThisHour,
      selectedFile,
      quickSuggestions,
      toggleChat,
      closeChat,
      sendMessage,
      sendQuickMessage,
      startNewConversation,
      handleShiftEnter,
      autoResize,
      executeSuggestedAction,
      handleFileSelect,
      clearFile
    }
  }
}
</script>

<style scoped>
/* Animación slide-up para móvil */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-up-enter-from {
  transform: translateY(100%);
  opacity: 0;
}

.slide-up-leave-to {
  transform: translateY(100%);
  opacity: 0;
}

@media (min-width: 768px) {
  .slide-up-enter-from {
    transform: translateY(20px) scale(0.95);
  }
  .slide-up-leave-to {
    transform: translateY(20px) scale(0.95);
  }
}

/* Fade */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Fade in para mensajes */
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
  animation: fadeIn 0.25s ease-out;
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
