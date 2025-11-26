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
        class="fixed top-0 right-0 h-screen w-full md:w-[450px] bg-white shadow-2xl flex flex-col z-50 border-l border-gray-100"
      >
        <!-- Header del Chat -->
        <div class="bg-slate-900 px-6 py-4 flex items-center justify-between shadow-md z-10">
          <div class="flex items-center space-x-4">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center relative border border-white/10">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
              <!-- Indicator online -->
              <div class="absolute -bottom-1 -right-1 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-slate-900"></div>
            </div>
            <div>
              <h3 class="text-white font-semibold text-base tracking-wide">105 IA</h3>
              <p class="text-slate-400 text-xs flex items-center space-x-1.5">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                <span>Sistema Operativo</span>
              </p>
            </div>
          </div>
          
          <div class="flex items-center space-x-1">
            <!-- Botón Nueva Conversación -->
            <button 
              v-if="messages.length > 0"
              @click="startNewConversation"
              class="text-slate-400 hover:text-white transition-colors p-2 hover:bg-white/5 rounded-lg"
              title="Nueva conversación"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
            </button>
            
            <!-- Botón Cerrar -->
            <button 
              @click="closeChat"
              class="text-slate-400 hover:text-white transition-colors p-2 hover:bg-white/5 rounded-lg"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Sugerencias Rápidas (Solo cuando no hay mensajes) -->
        <div v-if="messages.length === 0" class="bg-gray-50 px-6 py-5 border-b border-gray-100">
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Sugerencias</p>
          <div class="grid grid-cols-2 gap-2.5">
            <button
              v-for="(suggestion, index) in quickSuggestions"
              :key="index"
              @click="sendQuickMessage(suggestion)"
              class="bg-white border border-gray-200 hover:border-slate-300 hover:bg-white rounded-lg p-3 text-xs text-left text-slate-600 hover:text-slate-900 transition-all duration-200 shadow-sm hover:shadow-md group"
            >
              <i :class="suggestion.icon" class="text-slate-400 group-hover:text-slate-600 mr-2 transition-colors"></i>
              {{ suggestion.text }}
            </button>
          </div>
        </div>

        <!-- Área de Mensajes -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-6 bg-white">
          <!-- Mensaje de Bienvenida -->
          <div v-if="messages.length === 0" class="flex items-start space-x-4 animate-fade-in">
            <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0 border border-slate-200">
              <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <div class="bg-slate-50 rounded-2xl rounded-tl-none p-5 border border-slate-100">
                <p class="text-sm text-slate-800 mb-3 font-medium">
                  Bienvenido a <span class="text-slate-900 font-bold">105 IA</span>
                </p>
                <p class="text-sm text-slate-600 mb-4 leading-relaxed">
                  Soy tu asistente inteligente para la gestión de inventario. Estoy aquí para ayudarte a optimizar tu negocio.
                </p>
                <ul class="text-xs text-slate-500 space-y-2.5">
                  <li class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Análisis de inventario y stock</span>
                  </li>
                  <li class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Recomendaciones de compra</span>
                  </li>
                  <li class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Predicciones de ventas</span>
                  </li>
                </ul>
              </div>
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
              class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0 border border-slate-200 mt-1"
            >
              <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <div 
              v-else
              class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center flex-shrink-0 mt-1"
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
                    ? 'bg-slate-900 text-white rounded-tr-none' 
                    : 'bg-white border border-gray-100 text-slate-700 rounded-tl-none shadow-sm'
                ]"
              >
                <p class="whitespace-pre-line">{{ message.text }}</p>

                <!-- Botón de Acción Sugerida -->
                <div v-if="message.suggested_action" class="mt-3 pt-3 border-t border-gray-100">
                  <button 
                    @click="executeSuggestedAction(message.suggested_action)"
                    class="w-full py-2 px-3 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 transition-colors flex items-center justify-center space-x-2 group"
                  >
                    <span>{{ message.suggested_action.label || 'Ver detalles' }}</span>
                    <svg class="w-3 h-3 text-slate-400 group-hover:text-slate-600 transform group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                  </button>
                </div>
              </div>
              <p 
                class="text-[10px] mt-1.5 px-1"
                :class="message.type === 'user' ? 'text-right text-slate-400' : 'text-slate-400'"
              >
                {{ message.timestamp }}
              </p>
            </div>
          </div>

          <!-- Indicador de escritura -->
          <div v-if="isTyping" class="flex items-start space-x-3 animate-fade-in">
            <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0 border border-slate-200 mt-1">
              <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <div class="bg-slate-50 rounded-2xl rounded-tl-none p-4 border border-slate-100">
              <div class="flex space-x-1.5">
                <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Input de Mensaje -->
        <div class="p-4 bg-white border-t border-gray-100">
          <form @submit.prevent="sendMessage" class="flex items-end space-x-3">
            <div class="flex-1">
              <textarea
                ref="messageInput"
                v-model="inputMessage"
                @keydown.enter.exact.prevent="sendMessage"
                @keydown.enter.shift.exact="handleShiftEnter"
                placeholder="Escribe tu consulta..."
                rows="1"
                class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-slate-900 focus:border-transparent resize-none transition-all bg-gray-50 focus:bg-white placeholder-gray-400"
                style="max-height: 120px;"
              ></textarea>
            </div>
            <button
              type="submit"
              :disabled="!inputMessage.trim() || isTyping"
              class="px-4 py-3 bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg transform hover:scale-105"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
              </svg>
            </button>
          </form>
          <p class="text-[10px] text-gray-400 mt-2 text-center font-medium">
            IA potenciada por 105
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
        messages.value.push({
          type: 'ai',
          text: 'Lo siento, tuve un problema al procesar tu solicitud. Por favor verifica tu conexión o intenta más tarde.',
          timestamp: getCurrentTime()
        })
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
