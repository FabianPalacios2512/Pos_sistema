<template>
  <!-- Botón Flotante de IA (solo si no es controlado externamente) -->
  <div>
    <button 
      v-if="!isControlledExternally"
      @click="toggleChat"
      class="fixed bottom-6 right-6 w-14 h-14 bg-slate-900 hover:bg-slate-800 rounded-full shadow-2xl hover:shadow-slate-900/30 transition-all duration-300 transform hover:scale-105 flex items-center justify-center group z-50 border border-slate-700/50"
      :class="{ 'scale-95': localChatOpen }"
    >
      <!-- Indicador de estado online -->
      <div class="absolute top-0 right-0 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white animate-pulse"></div>
      
      <!-- Icono de IA -->
      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
      </svg>
      
      <!-- Tooltip -->
      <div class="absolute bottom-full right-0 mb-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
        <div class="bg-slate-900 text-white text-xs font-medium px-3 py-2 rounded-lg whitespace-nowrap shadow-xl border border-slate-700">
          Asistente 105
          <div class="absolute top-full right-6 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-slate-900"></div>
        </div>
      </div>
    </button>

    <!-- Overlay para móviles -->
    <transition name="fade">
      <div
        v-if="localChatOpen"
        @click="closeChat"
        class="fixed inset-0 bg-slate-900/20 z-40 md:hidden backdrop-blur-sm"
      ></div>
    </transition>

    <!-- Panel de Chat IA (Slide desde la derecha) -->
    <transition name="slide-right">
      <div 
        v-if="localChatOpen"
        class="fixed top-0 right-0 h-screen w-full md:w-[450px] bg-white dark:bg-[#0a0a0c] shadow-2xl flex flex-col z-[9999] border-l border-gray-100 dark:border-zinc-800"
      >
        <!-- Header del Chat - Modo Oscuro Mejorado -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 dark:from-zinc-900 dark:via-zinc-800 dark:to-zinc-900 px-6 py-5 flex items-center justify-between shadow-lg border-b border-slate-700/50 dark:border-zinc-700/50 z-10">
          <div class="flex items-center space-x-4">
            <!-- Icono con gradiente verde esmeralda -->
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center relative shadow-lg shadow-emerald-900/30">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              <!-- Indicator online único -->
              <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-400 rounded-full border-2 border-slate-900 dark:border-zinc-900 animate-pulse shadow-lg shadow-emerald-500/50"></div>
            </div>
            <div>
              <h3 class="text-white font-bold text-lg tracking-tight">105 IA</h3>
              <p class="text-emerald-400 dark:text-emerald-300 text-xs font-semibold">
                Asistente Inteligente
              </p>
            </div>
          </div>
          
          <div class="flex items-center space-x-1.5">
            <!-- Botón Nueva Conversación -->
            <button 
              v-if="messages.length > 0"
              @click="startNewConversation"
              class="text-slate-300 dark:text-zinc-400 hover:text-white dark:hover:text-white transition-colors p-2.5 hover:bg-white/10 dark:hover:bg-white/5 rounded-xl"
              title="Nueva conversación"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
            </button>
            
            <!-- Botón Cerrar -->
            <button 
              @click="closeChat"
              class="text-slate-300 dark:text-zinc-400 hover:text-white dark:hover:text-white transition-colors p-2.5 hover:bg-white/10 dark:hover:bg-white/5 rounded-xl"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Sugerencias Rápidas (Solo cuando no hay mensajes) -->
        <div v-if="messages.length === 0" class="bg-gradient-to-b from-gray-50 to-white dark:from-zinc-900 dark:to-zinc-900/50 px-6 py-6 border-b border-gray-100 dark:border-zinc-800">
          <p class="text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-4">Comienza con una sugerencia</p>
          <div class="grid grid-cols-2 gap-3">
            <button
              v-for="(suggestion, index) in quickSuggestions"
              :key="index"
              @click="sendQuickMessage(suggestion)"
              class="bg-white dark:bg-zinc-800 border border-gray-100 dark:border-zinc-700 hover:border-emerald-200 dark:hover:border-emerald-600 rounded-xl p-4 text-xs text-left text-gray-600 dark:text-zinc-300 hover:text-gray-900 dark:hover:text-white transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1 group"
            >
              <i :class="suggestion.icon" class="text-emerald-600 dark:text-emerald-400 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 text-sm mb-2 block transition-colors"></i>
              <span class="font-medium">{{ suggestion.text }}</span>
            </button>
          </div>
        </div>

        <!-- Área de Mensajes -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-6 bg-white dark:bg-zinc-900">
          <!-- Mensaje de Bienvenida con Icono Hero -->
          <div v-if="messages.length === 0" class="text-center py-8 animate-fade-in">
            <!-- Icono Hero -->
            <div class="flex justify-center mb-6">
              <div class="w-20 h-20 bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-800/30 rounded-full flex items-center justify-center shadow-lg border border-emerald-200 dark:border-emerald-700/50">
                <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
            </div>
            
            <!-- Título y descripción -->
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Bienvenido a 105 IA</h2>
            <p class="text-sm text-gray-500 dark:text-zinc-400 mb-6 max-w-sm mx-auto leading-relaxed">
              Tu asistente inteligente para la gestión de inventario. Optimiza tu negocio con análisis avanzados.
            </p>
            
            <!-- Lista de Capacidades -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-100 dark:border-zinc-700/50 text-left max-w-md mx-auto">
              <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-3">Capacidades</p>
              <ul class="space-y-2.5">
                <li class="flex items-start space-x-3">
                  <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  <span class="text-sm text-gray-700 dark:text-zinc-300">Análisis de inventario y stock en tiempo real</span>
                </li>
                <li class="flex items-start space-x-3">
                  <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  <span class="text-sm text-gray-700 dark:text-zinc-300">Recomendaciones inteligentes de compra</span>
                </li>
                <li class="flex items-start space-x-3">
                  <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  <span class="text-sm text-gray-700 dark:text-zinc-300">Predicciones y tendencias de ventas</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Mensajes del Chat -->
          <div 
            v-for="(message, index) in messages" 
            :key="index"
            class="flex items-start space-x-3 animate-fade-in"
            :class="message.type === 'user' ? 'flex-row-reverse space-x-reverse' : ''"
          >
            <!-- Avatar -->
            <div 
              v-if="message.type === 'ai'"
              class="w-8 h-8 bg-slate-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center flex-shrink-0 border border-slate-200 dark:border-zinc-700 mt-1"
            >
              <svg class="w-4 h-4 text-slate-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <div 
              v-else
              class="w-8 h-8 bg-slate-900 dark:bg-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1"
            >
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>

            <!-- Mensaje -->
            <div 
              class="flex-1 max-w-[85%]"
            >
              <div
                class="rounded-2xl p-4 shadow-sm text-sm leading-relaxed"
                :class="[
                  message.type === 'user' 
                    ? 'bg-slate-900 dark:bg-emerald-600 text-white rounded-tr-none' 
                    : message.isLimit
                      ? 'bg-white dark:bg-zinc-800 border-l-4 border-orange-500 dark:border-orange-400 text-slate-800 dark:text-zinc-200 rounded-tl-none shadow-md'
                      : message.isError
                        ? 'bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-slate-800 dark:text-red-300 rounded-tl-none shadow-sm'
                        : 'bg-white dark:bg-zinc-800 border border-gray-100 dark:border-zinc-700 text-slate-700 dark:text-zinc-200 rounded-tl-none shadow-sm'
                ]"
              >
                <!-- Mensaje de límite con diseño profesional -->
                <div v-if="message.isLimit" class="space-y-3">
                  <div class="flex items-center gap-2 pb-2 border-b border-gray-200 dark:border-zinc-700">
                    <svg class="w-5 h-5 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wide">Límite Alcanzado</span>
                  </div>
                  <p class="whitespace-pre-line text-slate-700 dark:text-zinc-200">{{ message.text }}</p>
                </div>
                
                <!-- Mensajes normales -->
                <p v-else class="whitespace-pre-line">{{ message.text }}</p>

                <!-- Botón de Acción Sugerida -->
                <div v-if="message.suggested_action" class="mt-3 pt-3 border-t border-gray-100 dark:border-zinc-700">
                  <button 
                    @click="executeSuggestedAction(message.suggested_action)"
                    class="w-full py-2 px-3 bg-slate-50 dark:bg-zinc-700 hover:bg-slate-100 dark:hover:bg-zinc-600 text-slate-700 dark:text-zinc-200 text-xs font-semibold rounded-lg border border-slate-200 dark:border-zinc-600 transition-colors flex items-center justify-center space-x-2 group"
                  >
                    <span>{{ message.suggested_action.label || 'Ver detalles' }}</span>
                    <svg class="w-3 h-3 text-slate-400 dark:text-zinc-400 group-hover:text-slate-600 dark:group-hover:text-zinc-200 transform group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                  </button>
                </div>
              </div>
              <p 
                class="text-[10px] mt-1.5 px-1"
                :class="message.type === 'user' ? 'text-right text-slate-400 dark:text-zinc-500' : 'text-slate-400 dark:text-zinc-500'"
              >
                {{ message.timestamp }}
              </p>
            </div>
          </div>

          <!-- Indicador de escritura -->
          <div v-if="isTyping" class="flex items-start space-x-3 animate-fade-in">
            <div class="w-8 h-8 bg-slate-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center flex-shrink-0 border border-slate-200 dark:border-zinc-700 mt-1">
              <svg class="w-4 h-4 text-slate-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <div class="bg-slate-50 dark:bg-zinc-800 rounded-2xl rounded-tl-none p-4 border border-slate-100 dark:border-zinc-700">
              <div class="flex space-x-1.5">
                <div class="w-2 h-2 bg-slate-400 dark:bg-zinc-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                <div class="w-2 h-2 bg-slate-400 dark:bg-zinc-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                <div class="w-2 h-2 bg-slate-400 dark:bg-zinc-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Input de Mensaje - Diseño Flotante -->
        <div class="p-5 bg-gradient-to-t from-gray-50 to-white dark:from-zinc-900 dark:to-zinc-900/50 border-t border-gray-200 dark:border-zinc-800">
          <form @submit.prevent="sendMessage">
            <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg dark:shadow-black/50 border border-gray-200 dark:border-zinc-700 p-1.5 flex items-end gap-2 focus-within:ring-2 focus-within:ring-emerald-500/20 dark:focus-within:ring-emerald-400/20 focus-within:border-emerald-300 dark:focus-within:border-emerald-600 transition-all">
              <textarea
                ref="messageInput"
                v-model="inputMessage"
                @keydown.enter.exact.prevent="sendMessage"
                @keydown.enter.shift.exact="handleShiftEnter"
                @input="autoResize"
                placeholder=""
                rows="1"
                class="flex-1 px-4 py-3 text-sm bg-transparent border-none focus:outline-none resize-none placeholder-gray-400 dark:placeholder-zinc-500 text-gray-900 dark:text-white overflow-hidden"
              ></textarea>
              <button
                type="submit"
                :disabled="!inputMessage.trim() || isTyping"
                class="w-10 h-10 bg-emerald-600 dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-emerald-600 text-white rounded-xl transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center flex-shrink-0 shadow-md hover:shadow-lg disabled:hover:shadow-md"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
              </button>
            </div>
          </form>
          <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-3 text-center font-medium">
            💡 IA potenciada por 105 POS Pro
          </p>
        </div>
      </div>
    </transition>

    <!-- Overlay de fondo (móvil) -->
    <transition name="fade">
      <div 
        v-if="isChatOpen"
        @click="toggleChat"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 md:hidden"
      ></div>
    </transition>
  </div>
</template>

<script>
import { ref, nextTick, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useModuleNavigation } from '@/composables/useModuleNavigation'
import api from '@/services/api'

export default {
  name: 'AI105Chat',
  props: {
    isOpen: {
      type: Boolean,
      default: undefined // undefined = control interno, true/false = control externo
    }
  },
  emits: ['close', 'navigate'],
  setup(props, { emit }) {
    const router = useRouter()
    const { navigateToModule } = useModuleNavigation()
    const isChatOpen = ref(false)
    const messages = ref([])
    const inputMessage = ref('')
    const isTyping = ref(false)
    const messagesContainer = ref(null)
    const messageInput = ref(null)
    const sessionId = ref(null) // 🆕 ID de sesión conversacional

    // Computed para saber si se controla externamente
    const isControlledExternally = computed(() => props.isOpen !== undefined)

    // Computed para el estado local del chat
    const localChatOpen = computed(() => {
      return isControlledExternally.value ? props.isOpen : isChatOpen.value
    })

    // Watch para sincronizar cuando cambia isOpen desde fuera
    watch(() => props.isOpen, (newVal) => {
      if (newVal !== undefined && newVal) {
        nextTick(() => {
          messageInput.value?.focus()
        })
      }
    })

    const quickSuggestions = [
      { text: 'Análisis de stock', icon: 'fas fa-chart-bar' },
      { text: 'Productos críticos', icon: 'fas fa-exclamation-triangle' },
      { text: 'Recomendaciones', icon: 'fas fa-lightbulb' },
      { text: 'Predicciones', icon: 'fas fa-crystal-ball' }
    ]

    const toggleChat = () => {
      if (isControlledExternally.value) {
        emit('close')
      } else {
        isChatOpen.value = !isChatOpen.value
        if (isChatOpen.value) {
          nextTick(() => {
            messageInput.value?.focus()
          })
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
      const now = new Date()
      return now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
    }

    const executeSuggestedAction = (action) => {
      if (action && action.type === 'navigate' && action.payload) {
         try {
             const targetModule = action.payload.params?.module;
             const queryParams = action.payload.query || {};
             
             console.log('🔄 [AI Chat] Executing suggested action:', targetModule, queryParams);
             navigateToModule(targetModule, queryParams);
         } catch (err) {
            console.error('❌ [AI Chat] Suggested action error:', err);
         }
      }
    }

    const sendMessage = async () => {
      if (!inputMessage.value.trim() || isTyping.value) return

      // Agregar mensaje del usuario
      messages.value.push({
        type: 'user',
        text: inputMessage.value.trim(),
        timestamp: getCurrentTime()
      })

      const userMessage = inputMessage.value.trim()
      inputMessage.value = ''
      scrollToBottom()

      // Llamada real al backend
      isTyping.value = true
      try {
        // Usar el servicio de API centralizado
        const response = await api.post('/ai/chat', {
          message: userMessage,
          session_id: sessionId.value // 🆕 Enviar session_id si existe
        });

        // 🆕 Guardar session_id devuelto por el backend
        if (response.session_id) {
          sessionId.value = response.session_id;
          console.log('💾 [Session] ID guardado:', sessionId.value);
        }

        // Parsear la respuesta de la IA (que ahora es un JSON string dentro de response.reply)
        let aiReply = response.reply;
        let aiAction = null;
        let suggestedAction = null;
        let executeAction = null;
        let actionResult = null;

        console.log('🔍 [AI Chat Debug] Raw response:', response);
        console.log('🔍 [AI Chat Debug] response.reply type:', typeof response.reply);
        console.log('🔍 [AI Chat Debug] response.reply:', response.reply);

        try {
          // Intentar parsear si la respuesta viene como string JSON
          // A veces la IA puede devolver texto plano si falla, así que manejamos ambos casos
          if (typeof response.reply === 'string' && (response.reply.trim().startsWith('{') || response.reply.trim().startsWith('['))) {
             const parsed = JSON.parse(response.reply);
             aiReply = parsed.reply || parsed.text || response.reply;
             aiAction = parsed.action;
             suggestedAction = parsed.suggested_action;
             executeAction = parsed.execute_action;
             actionResult = parsed.action_result;
             console.log('✅ [AI Chat Debug] Parsed JSON - Reply:', aiReply);
             console.log('✅ [AI Chat Debug] Parsed JSON - Action:', aiAction);
             console.log('💡 [AI Chat Debug] Parsed JSON - Suggested Action:', suggestedAction);
             console.log('🚀 [AI Chat Debug] Parsed JSON - Execute Action:', executeAction);
             console.log('📊 [AI Chat Debug] Parsed JSON - Action Result:', actionResult);
          } else if (typeof response.reply === 'object') {
             // Si ya viene parseado (dependiendo de cómo lo devuelva el controller/axios)
             aiReply = response.reply.reply;
             aiAction = response.reply.action;
             suggestedAction = response.reply.suggested_action;
             executeAction = response.reply.execute_action;
             actionResult = response.reply.action_result;
             console.log('✅ [AI Chat Debug] Object - Reply:', aiReply);
             console.log('✅ [AI Chat Debug] Object - Action:', aiAction);
             console.log('💡 [AI Chat Debug] Object - Suggested Action:', suggestedAction);
             console.log('🚀 [AI Chat Debug] Object - Execute Action:', executeAction);
             console.log('📊 [AI Chat Debug] Object - Action Result:', actionResult);
          }
        } catch (e) {
          console.warn('❌ [AI Chat Debug] Error parsing AI JSON response, using raw text', e);
        }

        // Mostrar resultado de acción ejecutada si existe
        if (actionResult && actionResult.success) {
          console.log('✅ [Action Result] Mostrando resultado exitoso:', actionResult);
          
          // Construir mensaje enriquecido con los datos reales
          let enrichedReply = aiReply;
          
          if (actionResult.discount) {
            enrichedReply += `\n\n✅ **Descuento creado:**\n`;
            enrichedReply += `🆔 ID: ${actionResult.discount.id}\n`;
            enrichedReply += `🎁 Código: ${actionResult.discount.code}\n`;
            enrichedReply += `💰 Valor: ${actionResult.discount.value}${actionResult.discount.type === 'percentage' ? '%' : ' $'}\n`;
            enrichedReply += `📅 Expira: ${actionResult.discount.expires_at}\n`;
            enrichedReply += `🎫 Usos: ${actionResult.discount.usage_limit}`;
          }
          
          // Verificar stats de WhatsApp (viene en actionResult.stats)
          if (actionResult.stats) {
            enrichedReply += `\n\n📱 **WhatsApp enviado:**\n`;
            enrichedReply += `✅ Enviados: ${actionResult.stats.sent}\n`;
            enrichedReply += `📊 Números únicos: ${actionResult.stats.unique}`;
            if (actionResult.stats.duplicates_removed > 0) {
              enrichedReply += `\n🔄 Duplicados omitidos: ${actionResult.stats.duplicates_removed}`;
            }
            if (actionResult.stats.failed > 0) {
              enrichedReply += `\n❌ Fallidos: ${actionResult.stats.failed}`;
            }
          }
          
          aiReply = enrichedReply;
        } else if (actionResult && !actionResult.success) {
          console.error('❌ [Action Result] Acción falló:', actionResult);
          aiReply += `\n\n❌ Error: ${actionResult.message || 'No se pudo ejecutar la acción'}`;
        }

        messages.value.push({
          type: 'ai',
          text: aiReply,
          timestamp: getCurrentTime(),
          suggested_action: suggestedAction
        })

        // Ejecutar acción de navegación si existe
        console.log('🔍 [AI Chat Debug] Checking action...', aiAction);
        if (aiAction && aiAction.type === 'navigate' && aiAction.payload) {
             console.log('🚀 [AI Chat Debug] Navigating with payload:', aiAction.payload);
             
             // Navegar directamente usando el composable global
             try {
                 const targetModule = aiAction.payload.params?.module;
                 const queryParams = aiAction.payload.query || {};
                 
                 console.log('🎯 [AI Chat Debug] Target module:', targetModule);
                 console.log('🔍 [AI Chat Debug] Query params:', queryParams);
                 
                 if (targetModule) {
                   // Usar navegación global con query params (filtros)
                   console.log('🔄 [AI Chat Debug] Calling navigateToModule:', targetModule, queryParams);
                   navigateToModule(targetModule, queryParams);
                   console.log('✅ [AI Chat Debug] Navigation successful to:', targetModule, 'with query:', queryParams);
                 } else {
                   console.warn('⚠️ [AI Chat Debug] No target module specified');
                 }
                 
             } catch (err) {
                console.error('❌ [AI Chat Debug] Navigation error:', err);
                messages.value.push({
                  type: 'ai',
                  text: 'Hubo un error al navegar. Por favor usa el menú lateral para ir a ' + (aiAction.payload.params?.module || 'ese módulo'),
                  timestamp: getCurrentTime()
                })
             }
        } else {
          console.log('ℹ️ [AI Chat Debug] No navigation action to perform');
        }
      } catch (error) {
        console.error('Error al contactar IA:', error);
        
        // Manejar específicamente el error 429 (límite de IA alcanzado)
        if (error.response && error.response.status === 429) {
          const errorData = error.response.data;
          let errorMessage = 'LÍMITE DE PETICIONES ALCANZADO';
          
          // Mostrar tiempo de espera si está disponible
          if (errorData.minutes_remaining && errorData.wait_until) {
            const mins = Math.ceil(errorData.minutes_remaining); // Redondear hacia arriba
            errorMessage = `LÍMITE DE PETICIONES ALCANZADO\n\n`;
            errorMessage += `Has alcanzado tu límite de ${errorData.usage?.limit_hour || 8} peticiones por hora.\n\n`;
            errorMessage += `Disponible nuevamente en ${mins} minuto${mins !== 1 ? 's' : ''} (${errorData.wait_until}).\n\n`;
            errorMessage += `USO ACTUAL\n`;
            errorMessage += `Esta hora: ${errorData.usage?.current_hour || 0} de ${errorData.usage?.limit_hour || 8}\n`;
            errorMessage += `Hoy: ${errorData.usage?.current_day || 0} de ${errorData.usage?.limit_day || 50}\n\n`;
            errorMessage += `Actualiza tu plan para obtener más peticiones.`;
          } else if (errorData.message) {
            errorMessage = errorData.message;
          }
          
          messages.value.push({
            type: 'ai',
            text: errorMessage,
            timestamp: getCurrentTime(),
            isError: true,
            isLimit: true
          });
        } else {
          // Error genérico
          messages.value.push({
            type: 'ai',
            text: 'Lo siento, tuve un problema al procesar tu solicitud. Por favor verifica tu conexión o intenta más tarde.',
            timestamp: getCurrentTime()
          });
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
      if (!sessionId.value) {
        // Si no hay sesión activa, solo limpiamos localmente
        messages.value = [];
        return;
      }

      try {
        // Llamar al endpoint para limpiar el historial en el backend
        await api.post('/ai/clear-history', {
          session_id: sessionId.value
        });

        // Limpiar mensajes locales y session_id
        messages.value = [];
        sessionId.value = null;
        
        console.log('🔄 [Session] Nueva conversación iniciada');
      } catch (error) {
        console.error('❌ [Session] Error limpiando historial:', error);
        // Limpiar localmente de todos modos
        messages.value = [];
        sessionId.value = null;
      }
    }

    const handleShiftEnter = (e) => {
      // Permitir Shift+Enter para nueva línea
      const textarea = e.target
      const start = textarea.selectionStart
      const end = textarea.selectionEnd
      inputMessage.value = inputMessage.value.substring(0, start) + '\n' + inputMessage.value.substring(end)
      nextTick(() => {
        textarea.selectionStart = textarea.selectionEnd = start + 1
      })
    }

    const autoResize = () => {
      // Auto-expandir textarea según contenido (máximo 5 líneas)
      const textarea = messageInput.value
      if (textarea) {
        textarea.style.height = 'auto'
        const maxHeight = 120 // ~5 líneas
        textarea.style.height = Math.min(textarea.scrollHeight, maxHeight) + 'px'
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
      sessionId,
      quickSuggestions,
      toggleChat,
      closeChat,
      sendMessage,
      sendQuickMessage,
      startNewConversation,
      handleShiftEnter,
      autoResize,
      executeSuggestedAction
    }
  }
}
</script>

<style scoped>
/* Animación de slide desde la derecha */
.slide-right-enter-active,
.slide-right-leave-active {
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-right-enter-from {
  transform: translateX(100%);
}

.slide-right-leave-to {
  transform: translateX(100%);
}

/* Animación de fade */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Animación de fade-in para mensajes */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

/* Scrollbar personalizado */
.overflow-y-auto::-webkit-scrollbar {
  width: 5px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
