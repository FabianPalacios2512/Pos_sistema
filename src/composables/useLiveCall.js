/**
 * 📞 useLiveCall - Composable para llamadas en vivo bidireccionales con Gemini
 * 
 * Usa la Gemini Live API para conversaciones de audio en tiempo real vía WebSocket.
 * 
 * Características:
 * - Audio bidireccional en tiempo real
 * - Detección automática de voz (VAD)
 * - Interrupciones naturales
 * - Selector de voz inicial
 * - La IA inicia la conversación
 * - Contexto del sistema POS integrado
 */

import { ref, computed, onUnmounted } from 'vue'
import { getAuthToken, getTenantId } from '@/services/api'
import { useRadioStore } from '@/store/radioStore'
import { useUIContextStore } from '@/store/uiContextStore'
import { useModuleNavigation } from '@/composables/useModuleNavigation'
import api from '@/services/api'

// Configuración de audio
const SEND_SAMPLE_RATE = 16000    // 16kHz para entrada (micrófono)
const RECEIVE_SAMPLE_RATE = 24000 // 24kHz para salida (respuestas)
const CHUNK_SIZE = 4096           // Tamaño de cada chunk de audio

// Voces disponibles de Gemini - 8 mejores (alternando géneros para variedad)
const AVAILABLE_VOICES = [
  // Alternando: Femenina - Masculino - Femenina - Masculino...
  { id: 'Kore', name: 'Kore', description: 'Firme y profesional', gender: 'female', color: 'from-rose-400 to-pink-500' },
  { id: 'Puck', name: 'Puck', description: 'Optimista y amigable', gender: 'male', color: 'from-emerald-400 to-teal-500' },
  { id: 'Aoede', name: 'Aoede', description: 'Fresca y natural', gender: 'female', color: 'from-violet-400 to-purple-500' },
  { id: 'Charon', name: 'Charon', description: 'Informativo y claro', gender: 'male', color: 'from-slate-400 to-gray-600' },
  { id: 'Leda', name: 'Leda', description: 'Juvenil y alegre', gender: 'female', color: 'from-fuchsia-400 to-pink-500' },
  { id: 'Fenrir', name: 'Fenrir', description: 'Enérgico y dinámico', gender: 'male', color: 'from-orange-400 to-red-500' },
  { id: 'Orus', name: 'Orus', description: 'Firme y confiable', gender: 'male', color: 'from-blue-400 to-indigo-500' },
  { id: 'Achird', name: 'Achird', description: 'Amistoso y cercano', gender: 'male', color: 'from-cyan-400 to-blue-500' }
]

// Caché de audios pre-descargados
const voiceCache = new Map()

// Frases de preview para cada voz
const VOICE_PREVIEWS = {
  'Kore': 'Hola, soy Kore. Te ayudaré a gestionar tu negocio con claridad.',
  'Aoede': 'Hola, soy Aoede. Estoy aquí para asistirte de forma natural.',
  'Leda': 'Hola, soy Leda. Será un gusto ayudarte hoy.',
  'Puck': 'Hola, soy Puck. Cuenta conmigo para lo que necesites.',
  'Charon': 'Hola, soy Charon. Te guiaré con precisión en cada paso.',
  'Fenrir': 'Hola, soy Fenrir. Estoy listo para ayudarte con energía.',
  'Orus': 'Hola, soy Orus. Puedes confiar en mí para asistirte.',
  'Achird': 'Hola, soy Achird. Estaré aquí como un amigo para ayudarte.'
}

// Frase de confirmación
const CONFIRMATION_PHRASE = 'Perfecto, sabía que me ibas a elegir. Estoy lista para ayudarte.'

// ═══════════════════════════════════════════════════════════════
// HELPER: Obtener fecha en zona horaria de Colombia (UTC-5)
// ═══════════════════════════════════════════════════════════════
const getColombiaDate = (offsetDays = 0) => {
  // Usar Intl para obtener la fecha correcta en Colombia
  const now = new Date()
  
  // Obtener fecha en formato Colombia
  const colombiaFormatter = new Intl.DateTimeFormat('en-CA', {
    timeZone: 'America/Bogota',
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
  
  // Si hay offset de días, ajustar
  const targetDate = new Date(now)
  if (offsetDays !== 0) {
    targetDate.setDate(targetDate.getDate() + offsetDays)
  }
  
  return colombiaFormatter.format(targetDate) // YYYY-MM-DD
}

// Helper: Obtener rango de fechas para un período
const getDateRange = (periodo) => {
  const hoy = getColombiaDate(0)
  const ayer = getColombiaDate(-1)
  
  switch(periodo) {
    case 'hoy':
      return { inicio: hoy, fin: hoy, label: 'hoy' }
    case 'ayer':
      return { inicio: ayer, fin: ayer, label: 'ayer' }
    case 'semana':
      const hace7dias = getColombiaDate(-7)
      return { inicio: hace7dias, fin: hoy, label: 'esta semana' }
    case 'mes':
      const hace30dias = getColombiaDate(-30)
      return { inicio: hace30dias, fin: hoy, label: 'este mes' }
    default:
      return { inicio: hoy, fin: hoy, label: 'hoy' }
  }
}

export function useLiveCall() {
  // ═══════════════════════════════════════════════════════════════
  // ESTADO
  // ═══════════════════════════════════════════════════════════════
  const isConnected = ref(false)
  const isConnecting = ref(false)
  const isListening = ref(false)      // Micrófono activo
  const isSpeaking = ref(false)       // IA está hablando
  const error = ref(null)
  const callDuration = ref(0)         // Duración en segundos
  const transcript = ref('')          // Último texto reconocido
  
  // Configuración de voz - Selector mejorado
  const showVoiceSelector = ref(false)
  const selectedVoice = ref(localStorage.getItem('105ia_voice') || 'Kore')
  const isFirstTime = ref(!localStorage.getItem('105ia_voice'))
  const voices = ref(AVAILABLE_VOICES)
  const currentVoiceIndex = ref(0)    // Índice para navegación en carrusel
  const isPlayingPreview = ref(false) // Reproduciendo preview de voz
  const previewAudio = ref(null)      // Audio element para preview
  
  // WebSocket y Audio Context
  let websocket = null
  let audioContext = null
  let outputAudioContext = null
  let mediaStream = null
  let audioProcessor = null
  let audioQueue = []
  let isProcessingAudio = false
  let durationInterval = null
  let setupCompleteReceived = false
  let inactivityTimeout = null
  const INACTIVITY_TIMEOUT_MS = 40000  // 40 segundos de inactividad para cerrar
  
  // ═══════════════════════════════════════════════════════════════
  // COMPUTED
  // ═══════════════════════════════════════════════════════════════
  const isActive = computed(() => isConnected.value || isConnecting.value || showVoiceSelector.value)
  
  const formattedDuration = computed(() => {
    const mins = Math.floor(callDuration.value / 60)
    const secs = callDuration.value % 60
    return `${mins}:${secs.toString().padStart(2, '0')}`
  })
  
  const currentVoice = computed(() => {
    return voices.value.find(v => v.id === selectedVoice.value) || voices.value[0]
  })
  
  // ═══════════════════════════════════════════════════════════════
  // OBTENER TOKEN EFÍMERO DEL BACKEND
  // ═══════════════════════════════════════════════════════════════
  const getEphemeralToken = async () => {
    try {
      const token = getAuthToken()
      const tenantId = getTenantId()
      
      const response = await fetch('/api/ai/live-token', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
          'X-Tenant-ID': tenantId
        },
        body: JSON.stringify({
          model: 'gemini-2.5-flash-native-audio-preview-12-2025'
        })
      })
      
      if (!response.ok) {
        throw new Error('No se pudo obtener token de sesión')
      }
      
      const data = await response.json()
      return data.token
    } catch (err) {
      console.error('Error obteniendo token efímero:', err)
      throw err
    }
  }
  
  // ═══════════════════════════════════════════════════════════════
  // TIMEOUT DE INACTIVIDAD - Cierra la llamada después de 40s sin actividad
  // ═══════════════════════════════════════════════════════════════
  const resetInactivityTimeout = () => {
    // Limpiar timeout anterior
    if (inactivityTimeout) {
      clearTimeout(inactivityTimeout)
      inactivityTimeout = null
    }
    
    // Solo activar si estamos conectados
    if (!isConnected.value) return
    
    // Iniciar nuevo timeout
    inactivityTimeout = setTimeout(() => {
      console.log('⏰ [LiveCall] Cerrando por inactividad (40s)')
      endCall()
    }, INACTIVITY_TIMEOUT_MS)
  }
  
  const clearInactivityTimeout = () => {
    if (inactivityTimeout) {
      clearTimeout(inactivityTimeout)
      inactivityTimeout = null
    }
  }
  
  // ═══════════════════════════════════════════════════════════════
  // INICIAR LLAMADA - Paso 1: Mostrar selector de voz si es primera vez
  // ═══════════════════════════════════════════════════════════════
  const startCall = async () => {
    if (isConnected.value || isConnecting.value) {
      console.warn('Ya hay una llamada en curso')
      return
    }
    
    // Verificar si ya eligió voz (siempre leer de localStorage para evitar problemas con HMR)
    const savedVoice = localStorage.getItem('105ia_voice')
    if (savedVoice) {
      selectedVoice.value = savedVoice
      isFirstTime.value = false
    }
    
    // Si es primera vez, mostrar selector de voz
    if (isFirstTime.value) {
      showVoiceSelector.value = true
      
      // Reproducir bienvenida con la primera voz (sin pre-cache que bloquee)
      await playVoicePreview(0, true)
      return
    }
    
    // Si ya eligió voz, iniciar directamente
    await connectToLiveAPI()
  }
  
  // ═══════════════════════════════════════════════════════════════
  // NAVEGACIÓN DE VOCES - Carrusel horizontal
  // ═══════════════════════════════════════════════════════════════
  
  // Navegar a la siguiente voz
  const nextVoice = async () => {
    if (isPlayingPreview.value) return
    const newIndex = (currentVoiceIndex.value + 1) % AVAILABLE_VOICES.length
    currentVoiceIndex.value = newIndex
    selectedVoice.value = AVAILABLE_VOICES[newIndex].id
    await playVoicePreview(newIndex)
  }
  
  // Navegar a la voz anterior
  const prevVoice = async () => {
    if (isPlayingPreview.value) return
    const newIndex = currentVoiceIndex.value === 0 
      ? AVAILABLE_VOICES.length - 1 
      : currentVoiceIndex.value - 1
    currentVoiceIndex.value = newIndex
    selectedVoice.value = AVAILABLE_VOICES[newIndex].id
    await playVoicePreview(newIndex)
  }
  
  // Ir a una voz específica
  const goToVoice = async (index) => {
    if (isPlayingPreview.value || index === currentVoiceIndex.value) return
    currentVoiceIndex.value = index
    selectedVoice.value = AVAILABLE_VOICES[index].id
    await playVoicePreview(index)
  }
  
  // ═══════════════════════════════════════════════════════════════
  // PRE-CACHE: Pre-cargar audios estáticos de las voces siguientes
  // ═══════════════════════════════════════════════════════════════
  const precacheNextVoices = (currentIndex) => {
    // Pre-cachear las 3 voces siguientes en segundo plano
    setTimeout(() => {
      for (let i = 1; i <= 3; i++) {
        const nextIndex = (currentIndex + i) % AVAILABLE_VOICES.length
        const voice = AVAILABLE_VOICES[nextIndex]
        const voiceId = voice.id.toLowerCase()
        const cacheKey = `preview_${voice.id}`
        
        // Si ya está en caché, saltar
        if (voiceCache.has(cacheKey)) continue
        
        // Pre-cargar archivo estático (sin await, sin bloquear)
        fetch(`/storage/voice-previews/${voiceId}_preview.wav`)
          .then(async (response) => {
            if (response.ok) {
              const blob = await response.blob()
              voiceCache.set(cacheKey, blob)
            }
          })
          .catch(() => {})
      }
    }, 100) // Pequeño delay
  }
  
  // Reproducir preview usando archivos estáticos pre-generados (INSTANTÁNEO)
  const playVoicePreview = async (index = null, isWelcome = false) => {
    const voiceIndex = index !== null ? index : currentVoiceIndex.value
    const voice = AVAILABLE_VOICES[voiceIndex]
    const voiceId = voice.id.toLowerCase()
    
    // Determinar archivo a cargar
    const audioType = isWelcome ? 'welcome' : 'preview'
    const cacheKey = `${audioType}_${voice.id}`
    const audioPath = `/storage/voice-previews/${voiceId}_${audioType}.wav`
    
    isPlayingPreview.value = true
    
    try {
      // Cancelar audio anterior si existe
      if (previewAudio.value) {
        previewAudio.value.pause()
        previewAudio.value = null
      }
      
      let audioUrl
      
      // Verificar si está en caché
      if (voiceCache.has(cacheKey)) {
        const blob = voiceCache.get(cacheKey)
        audioUrl = URL.createObjectURL(blob)
      } else {
        // Cargar archivo estático directamente (muy rápido)
        const response = await fetch(audioPath)
        if (!response.ok) {
          throw new Error('Audio no encontrado')
        }
        const blob = await response.blob()
        voiceCache.set(cacheKey, blob)
        audioUrl = URL.createObjectURL(blob)
      }
      
      // Reproducir audio
      previewAudio.value = new Audio(audioUrl)
      previewAudio.value.onended = () => {
        isPlayingPreview.value = false
        URL.revokeObjectURL(audioUrl)
      }
      previewAudio.value.onerror = () => {
        isPlayingPreview.value = false
        URL.revokeObjectURL(audioUrl)
      }
      
      await previewAudio.value.play()
      
      // Pre-cachear las voces siguientes
      precacheNextVoices(voiceIndex)
      
    } catch (err) {
      console.error('Error reproduciendo preview:', err)
      isPlayingPreview.value = false
    }
  }
  
  // Confirmar selección de voz (usa archivo estático)
  const confirmVoiceSelection = async () => {
    const voice = AVAILABLE_VOICES[currentVoiceIndex.value]
    const voiceId = voice.id.toLowerCase()
    
    // Activar animación de ondas
    isPlayingPreview.value = true
    
    try {
      // Cancelar audio anterior
      if (previewAudio.value) {
        previewAudio.value.pause()
        previewAudio.value = null
      }
      
      // Determinar archivo según género
      const confirmType = voice.gender === 'female' ? 'confirm_female' : 'confirm_male'
      const audioPath = `/storage/voice-previews/${voiceId}_${confirmType}.wav`
      
      const response = await fetch(audioPath)
      if (!response.ok) {
        throw new Error('Audio de confirmación no encontrado')
      }
      
      const blob = await response.blob()
      const audioUrl = URL.createObjectURL(blob)
      
      previewAudio.value = new Audio(audioUrl)
      previewAudio.value.onended = async () => {
        isPlayingPreview.value = false
        URL.revokeObjectURL(audioUrl)
        
        // Guardar selección
        selectedVoice.value = voice.id
        localStorage.setItem('105ia_voice', voice.id)
        isFirstTime.value = false
        showVoiceSelector.value = false
        
        // Iniciar la llamada real
        await connectToLiveAPI()
      }
      previewAudio.value.onerror = async () => {
        isPlayingPreview.value = false
        URL.revokeObjectURL(audioUrl)
        // Continuar aunque falle
        selectedVoice.value = voice.id
        localStorage.setItem('105ia_voice', voice.id)
        isFirstTime.value = false
        showVoiceSelector.value = false
        await connectToLiveAPI()
      }
      
      await previewAudio.value.play()
      
    } catch (err) {
      console.error('Error en confirmación:', err)
      isPlayingPreview.value = false
      // Continuar aunque falle
      selectedVoice.value = voice.id
      localStorage.setItem('105ia_voice', voice.id)
      isFirstTime.value = false
      showVoiceSelector.value = false
      await connectToLiveAPI()
    }
  }
  
  // Seleccionar voz directamente (legacy)
  const selectVoice = async (voiceId) => {
    selectedVoice.value = voiceId
    localStorage.setItem('105ia_voice', voiceId)
    isFirstTime.value = false
    showVoiceSelector.value = false
    
    // Iniciar la llamada con la voz seleccionada
    await connectToLiveAPI()
  }
  
  // Cancelar selección de voz
  const cancelVoiceSelection = () => {
    // Cancelar audio de preview si está reproduciéndose
    if (previewAudio.value) {
      previewAudio.value.pause()
      previewAudio.value = null
    }
    showVoiceSelector.value = false
    isPlayingPreview.value = false
  }
  
  // ═══════════════════════════════════════════════════════════════
  // CONECTAR A GEMINI LIVE API
  // ═══════════════════════════════════════════════════════════════
  const connectToLiveAPI = async () => {
    isConnecting.value = true
    error.value = null
    callDuration.value = 0
    setupCompleteReceived = false
    
    try {
      // 1. Obtener token efímero del backend
      const ephemeralToken = await getEphemeralToken()
      
      // 2. Solicitar permisos de micrófono
      mediaStream = await navigator.mediaDevices.getUserMedia({
        audio: {
          channelCount: 1,
          sampleRate: SEND_SAMPLE_RATE,
          echoCancellation: true,
          noiseSuppression: true,
          autoGainControl: true
        }
      })
      
      // 3. Crear AudioContext para procesar audio
      audioContext = new (window.AudioContext || window.webkitAudioContext)({
        sampleRate: SEND_SAMPLE_RATE
      })
      
      // 4. Conectar WebSocket a Gemini Live API
      // URL según documentación oficial
      const wsUrl = `wss://generativelanguage.googleapis.com/ws/google.ai.generativelanguage.v1beta.GenerativeService.BidiGenerateContent?key=${ephemeralToken}`
      
      websocket = new WebSocket(wsUrl)
      
      websocket.onopen = () => {
        // Obtener nombre del usuario para saludo personalizado
        let userName = '105 Code'
        try {
          const user = JSON.parse(localStorage.getItem('user') || '{}')
          userName = user.name?.split(' ')[0] || '105 Code'
        } catch {}
        
        // Obtener fecha actual en Colombia para el contexto
        const fechaHoyColombia = getColombiaDate(0)
        const fechaFormateada = new Date().toLocaleDateString('es-CO', {
          timeZone: 'America/Bogota',
          weekday: 'long',
          year: 'numeric', 
          month: 'long',
          day: 'numeric'
        })
        
        // Obtener hora actual en Colombia
        const horaFormateada = new Date().toLocaleTimeString('es-CO', {
          timeZone: 'America/Bogota',
          hour: '2-digit',
          minute: '2-digit',
          hour12: true
        })
        
        // Enviar configuración inicial con instrucciones para que inicie la conversación
        const setupMessage = {
          setup: {
            model: 'models/gemini-2.5-flash-native-audio-preview-12-2025',
            generationConfig: {
              responseModalities: ['AUDIO'],
              speechConfig: {
                voiceConfig: {
                  prebuiltVoiceConfig: {
                    voiceName: selectedVoice.value
                  }
                }
              }
            },
            systemInstruction: {
              parts: [{
                text: `Eres el asistente de voz de ciento cinco pos. Tu nombre es "ciento cinco i a". El usuario se llama ${userName}.

FECHA Y HORA ACTUAL EN COLOMBIA: ${fechaFormateada}, ${horaFormateada} (Bogotá, UTC-5)

IMPORTANTE PARA PRONUNCIACIÓN:
- Cuando menciones el sistema, di "ciento cinco pos" (NO deletrees P-O-S)
- Tu nombre es "ciento cinco i a" (NO deletrees I-A)

PERSONALIDAD:
- Amigable y cercana, como un buen compañero de trabajo
- Usa español natural y breve (1-2 oraciones)
- Puedes usar expresiones casuales: "¡Claro!", "Perfecto", "Dale"
- Si el usuario te saluda, responde cálidamente con tu nombre

REGLA IMPORTANTE - DATOS EN TIEMPO REAL:
- Para cualquier dato numérico (productos, ventas, facturas), SIEMPRE usa las herramientas
- Nunca inventes números, consulta siempre

HERRAMIENTAS:
- obtenerEstadisticas: resumen del negocio (productos, ventas de hoy)
- consultarProductos: productos activos, inactivos, stock bajo, buscar
- consultarFacturas: info de facturas de hoy/ayer/semana/mes
- mostrarFactura: abre una factura específica
- navegarModulo: ir a secciones del sistema
- controlarRadio: play/pause/next música
- obtenerContexto: qué está viendo el usuario ahora
- ejecutarAccion: enviar email, WhatsApp, descargar PDF

Después de dar datos, ofrece llevar al módulo si tiene sentido.`
              }]
            },
            tools: [{
              functionDeclarations: [
                {
                  name: 'controlarRadio',
                  description: 'Controla la radio/música',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      accion: { type: 'STRING', enum: ['play', 'pause', 'next', 'previous'] }
                    },
                    required: ['accion']
                  }
                },
                {
                  name: 'mostrarFactura',
                  description: 'Navega a facturas y abre una factura específica. Usa para: muéstrame la factura más cara de hoy/ayer/semana/mes, abre la última factura, busca factura por número.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      tipo: { 
                        type: 'STRING', 
                        description: 'Qué factura buscar',
                        enum: ['mas_cara', 'ultima', 'por_numero']
                      },
                      periodo: {
                        type: 'STRING',
                        description: 'Período de tiempo: hoy, ayer, semana, mes',
                        enum: ['hoy', 'ayer', 'semana', 'mes']
                      },
                      numero: { type: 'STRING', description: 'Número de factura si tipo=por_numero' }
                    },
                    required: ['tipo']
                  }
                },
                {
                  name: 'consultarFacturas',
                  description: 'Consulta información de facturas sin navegar: total vendido, cantidad, promedio. Puede ser de hoy, ayer, semana o mes.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      consulta: { 
                        type: 'STRING', 
                        description: 'Qué consultar',
                        enum: ['total', 'cantidad', 'promedio', 'resumen']
                      },
                      periodo: {
                        type: 'STRING',
                        description: 'Período: hoy, ayer, semana, mes',
                        enum: ['hoy', 'ayer', 'semana', 'mes']
                      }
                    },
                    required: ['consulta']
                  }
                },
                {
                  name: 'consultarProductos',
                  description: 'Consulta productos: inactivos, stock bajo, buscar por nombre',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      filtro: { 
                        type: 'STRING', 
                        enum: ['inactivos', 'stock_bajo', 'sin_stock', 'buscar', 'todos']
                      },
                      busqueda: { type: 'STRING', description: 'Término de búsqueda si filtro=buscar' }
                    },
                    required: ['filtro']
                  }
                },
                {
                  name: 'navegarModulo',
                  description: 'Navega a módulo con filtros. Ej: productos mostrando inactivos',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      modulo: { 
                        type: 'STRING', 
                        enum: ['dashboard', 'pos', 'productos', 'clientes', 'facturas', 'reportes', 'configuracion', 'proveedores', 'categorias', 'stock']
                      },
                      filtro: { type: 'STRING', description: 'Filtro opcional' }
                    },
                    required: ['modulo']
                  }
                },
                {
                  name: 'obtenerEstadisticas',
                  description: 'Estadísticas: productos, ventas hoy, stock bajo',
                  parameters: { type: 'OBJECT', properties: {} }
                },
                {
                  name: 'obtenerContexto',
                  description: 'Pregunta qué está viendo el usuario ahora: módulo actual, factura/producto/cliente seleccionado, modales abiertos',
                  parameters: { type: 'OBJECT', properties: {} }
                },
                {
                  name: 'ejecutarAccion',
                  description: 'Ejecuta una acción en el elemento actual. Ej: enviar factura por email, WhatsApp, descargar PDF. PRIMERO usa obtenerContexto para saber qué acciones están disponibles.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      accion: { 
                        type: 'STRING', 
                        description: 'Acción a ejecutar: sendEmail, sendWhatsApp, downloadPDF, printInvoice',
                        enum: ['sendEmail', 'sendWhatsApp', 'downloadPDF', 'printInvoice']
                      }
                    },
                    required: ['accion']
                  }
                }
              ]
            }]
          }
        }
        
        websocket.send(JSON.stringify(setupMessage))
      }
      
      websocket.onmessage = async (event) => {
        try {
          // Manejar Blob o texto
          let rawData = event.data
          if (rawData instanceof Blob) {
            rawData = await rawData.text()
          }
          
          const data = JSON.parse(rawData)
          
          // Configuración completada - Iniciar todo
          if (data.setupComplete) {
            setupCompleteReceived = true
            
            isConnected.value = true
            isConnecting.value = false
            isListening.value = true
            
            // Iniciar timer de duración
            durationInterval = setInterval(() => {
              callDuration.value++
            }, 1000)
            
            // Iniciar captura de audio
            startAudioCapture()
            
            // Iniciar timeout de inactividad (40s)
            resetInactivityTimeout()
            
            // Enviar mensaje inicial para que la IA salude
            sendInitialGreeting()
            return
          }
          
          // Procesar respuesta de audio
          if (data.serverContent?.modelTurn?.parts) {
            isSpeaking.value = true
            
            for (const part of data.serverContent.modelTurn.parts) {
              if (part.inlineData?.data) {
                // Es audio PCM en base64
                const audioData = base64ToArrayBuffer(part.inlineData.data)
                audioQueue.push(audioData)
                
                if (!isProcessingAudio) {
                  processAudioQueue()
                }
              }
              
              // Si hay texto (transcripción)
              if (part.text) {
                transcript.value = part.text
              }
            }
          }
          
          // Transcripción de entrada (usuario habló)
          if (data.serverContent?.inputTranscription?.text) {
            transcript.value = data.serverContent.inputTranscription.text
            // Resetear timeout de inactividad cuando el usuario habla
            resetInactivityTimeout()
          }
          
          // Transcripción de salida
          if (data.serverContent?.outputTranscription?.text) {
            transcript.value = data.serverContent.outputTranscription.text
          }
          
          // Detectar cuando el modelo termina de hablar
          if (data.serverContent?.turnComplete) {
            isSpeaking.value = false
            // Resetear timeout después de que la IA responde
            resetInactivityTimeout()
          }
          
          // Interrumpido
          if (data.serverContent?.interrupted) {
            isSpeaking.value = false
            // Limpiar cola de audio para detener reproducción
            audioQueue = []
            nextPlayTime = 0  // Resetear tiempo de reproducción
          }
          
          // === MANEJO DE TOOL CALLS (Function Calling) ===
          if (data.toolCall) {
            await handleToolCalls(data.toolCall.functionCalls || [])
          }
          
        } catch (err) {
          console.error('Error procesando mensaje:', err)
        }
      }
      
      websocket.onerror = (err) => {
        console.error('WebSocket error:', err)
        error.value = 'Error de conexión'
        endCall()
      }
      
      websocket.onclose = (event) => {
        if (isConnected.value) {
          error.value = event.reason || 'Conexión cerrada'
          endCall()
        }
      }
      
    } catch (err) {
      console.error('Error iniciando llamada:', err)
      error.value = err.message || 'Error al iniciar llamada'
      isConnecting.value = false
      cleanup()
    }
  }
  
  // ═══════════════════════════════════════════════════════════════
  // ENVIAR SALUDO INICIAL PARA QUE LA IA RESPONDA
  // ═══════════════════════════════════════════════════════════════
  const sendInitialGreeting = () => {
    if (!websocket || websocket.readyState !== WebSocket.OPEN) return
    
    // Mensaje corto para saludo rápido
    const message = {
      clientContent: {
        turns: [{
          role: 'user',
          parts: [{
            text: 'Hola'
          }]
        }],
        turnComplete: true
      }
    }
    
    websocket.send(JSON.stringify(message))
  }
  
  // ═══════════════════════════════════════════════════════════════
  // CAPTURA DE AUDIO DEL MICRÓFONO
  // ═══════════════════════════════════════════════════════════════
  const startAudioCapture = async () => {
    if (!mediaStream || !audioContext || !websocket) return
    
    try {
      // Crear nodo de fuente desde el micrófono
      const source = audioContext.createMediaStreamSource(mediaStream)
      
      // Usar ScriptProcessor para capturar audio
      const processor = audioContext.createScriptProcessor(CHUNK_SIZE, 1, 1)
      
      processor.onaudioprocess = (e) => {
        if (!isConnected.value || !websocket || websocket.readyState !== WebSocket.OPEN) {
          return
        }
        
        // Obtener datos del buffer de entrada
        const inputData = e.inputBuffer.getChannelData(0)
        
        // Convertir Float32 a Int16 (PCM 16-bit)
        const pcmData = float32ToInt16(inputData)
        
        // Convertir a Base64
        const base64Audio = arrayBufferToBase64(pcmData.buffer)
        
        // Enviar al WebSocket usando el formato correcto
        const message = {
          realtimeInput: {
            audio: {
              mimeType: 'audio/pcm;rate=16000',
              data: base64Audio
            }
          }
        }
        
        websocket.send(JSON.stringify(message))
      }
      
      source.connect(processor)
      processor.connect(audioContext.destination)
      
      audioProcessor = processor
      
    } catch (err) {
      console.error('Error capturando audio:', err)
      error.value = 'Error de micrófono'
    }
  }
  
  // ═══════════════════════════════════════════════════════════════
  // REPRODUCCIÓN DE AUDIO DE RESPUESTA (OPTIMIZADO)
  // ═══════════════════════════════════════════════════════════════
  let nextPlayTime = 0  // Tiempo para el próximo chunk (para reproducción continua)
  
  const processAudioQueue = async () => {
    if (audioQueue.length === 0) {
      isProcessingAudio = false
      return
    }
    
    isProcessingAudio = true
    
    // Crear contexto de salida si no existe
    if (!outputAudioContext) {
      outputAudioContext = new (window.AudioContext || window.webkitAudioContext)({
        sampleRate: RECEIVE_SAMPLE_RATE
      })
      nextPlayTime = 0
    }
    
    // Resumir si está suspendido
    if (outputAudioContext.state === 'suspended') {
      await outputAudioContext.resume()
    }
    
    while (audioQueue.length > 0 && isConnected.value) {
      const audioData = audioQueue.shift()
      
      try {
        // Convertir PCM 16-bit a Float32
        const int16Array = new Int16Array(audioData)
        const float32Array = new Float32Array(int16Array.length)
        
        for (let i = 0; i < int16Array.length; i++) {
          float32Array[i] = int16Array[i] / 32768
        }
        
        const audioBuffer = outputAudioContext.createBuffer(1, float32Array.length, RECEIVE_SAMPLE_RATE)
        audioBuffer.getChannelData(0).set(float32Array)
        
        // Reproducir de forma continua (sin gaps)
        const source = outputAudioContext.createBufferSource()
        source.buffer = audioBuffer
        source.connect(outputAudioContext.destination)
        
        // Calcular tiempo de inicio para evitar gaps
        const currentTime = outputAudioContext.currentTime
        const startTime = Math.max(currentTime, nextPlayTime)
        source.start(startTime)
        
        // Actualizar próximo tiempo de reproducción
        nextPlayTime = startTime + audioBuffer.duration
        
      } catch (err) {
        console.error('Error reproduciendo audio:', err)
      }
    }
    
    isProcessingAudio = false
  }
  
  // ═══════════════════════════════════════════════════════════════
  // TERMINAR LLAMADA
  // ═══════════════════════════════════════════════════════════════
  const endCall = () => {
    cleanup()
    
    isConnected.value = false
    isConnecting.value = false
    isListening.value = false
    isSpeaking.value = false
    showVoiceSelector.value = false
  }
  
  // Limpiar recursos
  const cleanup = () => {
    // Detener timer de duración
    if (durationInterval) {
      clearInterval(durationInterval)
      durationInterval = null
    }
    
    // Detener timeout de inactividad
    clearInactivityTimeout()
    
    // Cerrar WebSocket
    if (websocket) {
      websocket.close()
      websocket = null
    }
    
    // Detener micrófono
    if (mediaStream) {
      mediaStream.getTracks().forEach(track => track.stop())
      mediaStream = null
    }
    
    // Cerrar AudioContext
    if (audioContext) {
      audioContext.close()
      audioContext = null
    }
    
    if (outputAudioContext) {
      outputAudioContext.close()
      outputAudioContext = null
    }
    
    // Limpiar cola de audio
    audioQueue = []
    isProcessingAudio = false
    nextPlayTime = 0  // Resetear tiempo de reproducción continua
  }
  
  // ═══════════════════════════════════════════════════════════════
  // MANEJO DE TOOL CALLS (Function Calling)
  // ═══════════════════════════════════════════════════════════════
  
  // Flag para evitar duplicación de tool calls
  let processingToolCall = false
  
  const handleToolCalls = async (functionCalls) => {
    // Evitar procesar si ya estamos procesando
    if (processingToolCall) return
    processingToolCall = true
    
    const radioStore = useRadioStore()
    const { navigateToModule } = useModuleNavigation()
    
    const functionResponses = []
    
    for (const fc of functionCalls) {
      let result = { success: false, message: 'Función no reconocida' }
      
      try {
        switch (fc.name) {
          case 'controlarRadio':
            const accion = fc.args?.accion || 'play'
            
            // Inicializar audio si no existe
            if (!radioStore.audio) {
              radioStore.initAudio()
            }
            
            if (accion === 'play') {
              // Si hay estación actual, reproducir
              if (radioStore.currentStation) {
                radioStore.togglePlay()
              } else {
                // Cargar estaciones y reproducir la primera
                await radioStore.loadTopStations()
                if (radioStore.topStations.length > 0) {
                  await radioStore.playStation(radioStore.topStations[0])
                }
              }
              result = { success: true, message: 'Radio reproduciendo' }
            } else if (accion === 'pause') {
              if (radioStore.isPlaying) {
                radioStore.togglePlay()
              }
              result = { success: true, message: 'Radio pausada' }
            } else if (accion === 'next') {
              radioStore.nextStation()
              result = { success: true, message: 'Siguiente estación' }
            } else if (accion === 'previous') {
              radioStore.prevStation()
              result = { success: true, message: 'Estación anterior' }
            }
            break
          
          case 'mostrarFactura':
            try {
              const tipo = fc.args?.tipo || 'mas_cara'
              const periodo = fc.args?.periodo || 'hoy'
              const numero = fc.args?.numero || ''
              
              // Obtener rango de fechas usando zona horaria de Colombia
              const rango = getDateRange(periodo)
              
              // Obtener facturas
              const respFacturas = await api.get('/invoices', {
                params: { status: 'completed', limit: 200 }
              })
              
              const facturas = respFacturas.data?.data || respFacturas.data || []
              
              // Filtrar facturas por rango de fechas
              const facturasFiltradas = facturas.filter(f => {
                const fechaFactura = f.created_at?.split('T')[0] || f.date?.split('T')[0]
                return fechaFactura >= rango.inicio && fechaFactura <= rango.fin
              })
              
              let facturaSeleccionada = null
              let descripcion = ''
              
              if (tipo === 'mas_cara') {
                if (facturasFiltradas.length > 0) {
                  facturaSeleccionada = facturasFiltradas.reduce((max, f) => 
                    (parseFloat(f.total) > parseFloat(max.total)) ? f : max
                  , facturasFiltradas[0])
                  descripcion = `la factura más cara de ${rango.label}: ${facturaSeleccionada.invoice_number} por $${parseFloat(facturaSeleccionada.total).toLocaleString()}`
                }
              } else if (tipo === 'ultima') {
                if (facturasFiltradas.length > 0) {
                  // Ordenar por fecha desc
                  const ordenadas = [...facturasFiltradas].sort((a, b) => 
                    new Date(b.created_at || b.date) - new Date(a.created_at || a.date)
                  )
                  facturaSeleccionada = ordenadas[0]
                  descripcion = `la última factura de ${rango.label}: ${facturaSeleccionada.invoice_number} por $${parseFloat(facturaSeleccionada.total).toLocaleString()}`
                }
              } else if (tipo === 'por_numero' && numero) {
                facturaSeleccionada = facturas.find(f => 
                  f.invoice_number?.toLowerCase().includes(numero.toLowerCase())
                )
                if (facturaSeleccionada) {
                  descripcion = `la factura ${facturaSeleccionada.invoice_number}`
                }
              }
              
              if (facturaSeleccionada) {
                // Navegar al módulo de facturas Y seleccionar la factura
                navigateToModule('invoices', { selectId: facturaSeleccionada.id })
                result = { 
                  success: true, 
                  message: `Listo, te muestro ${descripcion}. Ya la abrí en pantalla.`
                }
              } else {
                result = { 
                  success: false, 
                  message: tipo === 'por_numero' 
                    ? `No encontré la factura ${numero}` 
                    : `No hay facturas ${rango.label}`
                }
              }
            } catch (err) {
              result = { success: false, message: 'Error buscando la factura' }
            }
            break
            
          case 'consultarFacturas':
            try {
              const consulta = fc.args?.consulta || 'total'
              const periodo = fc.args?.periodo || 'hoy'
              
              // Obtener rango de fechas usando zona horaria de Colombia
              const rango = getDateRange(periodo)
              
              // Llamar a la API de facturas
              const response = await api.get('/invoices', {
                params: { status: 'completed', limit: 200 }
              })
              
              const facturas = response.data?.data || response.data || []
              
              // Filtrar facturas por rango de fechas
              const facturasFiltradas = facturas.filter(f => {
                const fechaFactura = f.created_at?.split('T')[0] || f.date?.split('T')[0]
                return fechaFactura >= rango.inicio && fechaFactura <= rango.fin
              })
              
              const total = facturasFiltradas.reduce((sum, f) => sum + parseFloat(f.total || 0), 0)
              const cantidad = facturasFiltradas.length
              const promedio = cantidad > 0 ? total / cantidad : 0
              
              if (consulta === 'total') {
                result = { success: true, data: `Total vendido ${rango.label}: $${total.toLocaleString()}` }
              } else if (consulta === 'cantidad') {
                result = { success: true, data: `${rango.label} hay ${cantidad} facturas` }
              } else if (consulta === 'promedio') {
                result = { success: true, data: `Promedio por factura ${rango.label}: $${promedio.toLocaleString()}` }
              } else {
                // Resumen
                result = { 
                  success: true, 
                  data: `${rango.label}: ${cantidad} facturas por $${total.toLocaleString()} total. Promedio: $${Math.round(promedio).toLocaleString()}`
                }
              }
            } catch (err) {
              result = { success: false, message: 'Error consultando facturas' }
            }
            break
            
          case 'navegarModulo':
            const modulo = fc.args?.modulo || 'dashboard'
            const filtroNav = fc.args?.filtro || null
            const moduloMap = {
              'productos': 'products',
              'inventario': 'products',
              'clientes': 'customers',
              'facturas': 'invoices',
              'reportes': 'reports',
              'configuracion': 'settings',
              'proveedores': 'suppliers',
              'categorias': 'categories',
              'stock': 'stock',
              'dashboard': 'dashboard',
              'pos': 'pos'
            }
            const moduloFinal = moduloMap[modulo] || modulo
            
            // Mapear filtros a valores que los módulos entienden
            const filtroMap = {
              'inactivos': 'inactive',
              'inactivo': 'inactive',
              'stock_bajo': 'low-stock',
              'sin_stock': 'low-stock',
              'activos': 'active'
            }
            const filtroFinal = filtroNav ? (filtroMap[filtroNav] || filtroNav) : null
            
            // Navegar con filtro si existe
            if (filtroFinal) {
              navigateToModule(moduloFinal, { filter: filtroFinal })
            } else {
              navigateToModule(moduloFinal)
            }
            result = { success: true, message: `Listo, estás en ${modulo}${filtroNav ? ` mostrando ${filtroNav}` : ''}` }
            break
          
          case 'consultarProductos':
            try {
              const filtro = fc.args?.filtro || 'todos'
              const busqueda = fc.args?.busqueda || ''
              
              // Obtener productos según filtro
              // NOTA: El backend usa 'status' (active/inactive/all) no 'is_active'
              // y per_page para evitar paginación de 15 por defecto
              let params = { per_page: 1000 }  // Obtener todos los productos
              if (filtro === 'inactivos') {
                params.status = 'inactive'
              } else if (filtro === 'stock_bajo' || filtro === 'sin_stock') {
                params.status = 'active'
              } else if (filtro === 'todos') {
                params.status = 'all'
              } else {
                params.status = 'active'
              }
              
              const response = await api.get('/products', { params })
              // La respuesta tiene formato: { current_page, data: [...], per_page, total }
              const paginatedData = response.data?.data
              let productos = paginatedData?.data || paginatedData || response.data || []
              
              // Aplicar filtros adicionales
              if (filtro === 'stock_bajo') {
                productos = productos.filter(p => (p.stock || 0) < (p.min_stock || 5) && (p.stock || 0) > 0)
              } else if (filtro === 'sin_stock') {
                productos = productos.filter(p => (p.stock || 0) === 0)
              } else if (filtro === 'buscar' && busqueda) {
                const term = busqueda.toLowerCase()
                productos = productos.filter(p => p.name?.toLowerCase().includes(term))
              }
              
              if (filtro === 'inactivos') {
                if (productos.length === 0) {
                  result = { success: true, data: 'No tienes productos inactivos. ¡Todos están activos!' }
                } else {
                  const nombres = productos.slice(0, 5).map(p => p.name).join(', ')
                  result = { 
                    success: true, 
                    data: `Tienes ${productos.length} productos inactivos. Algunos: ${nombres}${productos.length > 5 ? '...' : ''}`
                  }
                }
              } else if (filtro === 'stock_bajo') {
                if (productos.length === 0) {
                  result = { success: true, data: 'No hay productos con stock bajo. ¡Todo bien!' }
                } else {
                  const lista = productos.slice(0, 3).map(p => `${p.name} (${p.stock})`).join(', ')
                  result = { success: true, data: `${productos.length} productos con stock bajo: ${lista}` }
                }
              } else if (filtro === 'sin_stock') {
                if (productos.length === 0) {
                  result = { success: true, data: 'No hay productos sin stock.' }
                } else {
                  const lista = productos.slice(0, 3).map(p => p.name).join(', ')
                  result = { success: true, data: `${productos.length} productos sin stock: ${lista}` }
                }
              } else if (filtro === 'buscar') {
                if (productos.length === 0) {
                  result = { success: true, data: `No encontré productos con "${busqueda}"` }
                } else {
                  const lista = productos.slice(0, 3).map(p => `${p.name} ($${p.sale_price})`).join(', ')
                  result = { success: true, data: `Encontré ${productos.length}: ${lista}` }
                }
              } else {
                result = { success: true, data: `Tienes ${productos.length} productos activos` }
              }
            } catch (err) {
              result = { success: false, message: 'Error consultando productos' }
            }
            break
            
          case 'obtenerEstadisticas':
            try {
              // Obtener datos directamente
              // NOTA: El backend usa 'status' (active/inactive/all) y per_page para paginación
              const [productsRes, invoicesRes] = await Promise.all([
                api.get('/products', { params: { status: 'active', per_page: 1000 } }),
                api.get('/invoices', { params: { status: 'completed', limit: 50 } })
              ])
              
              // La respuesta tiene formato: { current_page, data: [...], per_page, total }
              const paginatedProducts = productsRes.data?.data
              const productos = paginatedProducts?.data || paginatedProducts || productsRes.data || []
              const facturas = invoicesRes.data?.data || invoicesRes.data || []
              
              const hoy = new Date().toISOString().split('T')[0]
              
              const facturasHoy = facturas.filter(f => {
                const fecha = f.created_at?.split('T')[0] || f.date?.split('T')[0]
                return fecha === hoy
              })
              
              const totalHoy = facturasHoy.reduce((sum, f) => sum + parseFloat(f.total || 0), 0)
              const stockBajo = productos.filter(p => (p.stock || 0) < (p.min_stock || 5)).length
              
              result = { 
                success: true, 
                data: `Tienes ${productos.length} productos activos. Hoy: ${facturasHoy.length} ventas por $${totalHoy.toLocaleString()}. ${stockBajo} productos con stock bajo.`
              }
            } catch (err) {
              result = { success: false, message: 'Error obteniendo estadísticas' }
            }
            break
          
          case 'obtenerContexto':
            try {
              const uiContext = useUIContextStore()
              const contexto = uiContext.getContextForAI()
              result = { 
                success: true, 
                data: contexto.summary,
                context: contexto // Datos estructurados para la IA
              }
            } catch (err) {
              result = { success: false, message: 'Error obteniendo contexto' }
            }
            break
          
          case 'ejecutarAccion':
            try {
              const accionId = fc.args?.accion
              if (!accionId) {
                result = { success: false, message: 'No especificaste qué acción ejecutar' }
                break
              }
              
              const uiContext = useUIContextStore()
              const contexto = uiContext.getContextForAI()
              
              // Verificar si la acción está disponible
              if (!contexto.availableActions.includes(accionId)) {
                result = { 
                  success: false, 
                  message: `La acción "${accionId}" no está disponible. Acciones disponibles: ${contexto.availableActions.join(', ') || 'ninguna'}`
                }
                break
              }
              
              // Ejecutar la acción
              const actionResult = await uiContext.executeAction(accionId)
              
              const actionLabels = {
                sendEmail: 'enviando por email',
                sendWhatsApp: 'enviando por WhatsApp', 
                downloadPDF: 'descargando PDF',
                printInvoice: 'imprimiendo'
              }
              
              if (actionResult.success) {
                result = { 
                  success: true, 
                  message: `Listo, ${actionLabels[accionId] || accionId}`
                }
              } else {
                result = actionResult
              }
            } catch (err) {
              result = { success: false, message: 'Error ejecutando la acción' }
            }
            break
        }
      } catch (err) {
        result = { success: false, message: 'Error ejecutando la función' }
      }
      
      functionResponses.push({
        id: fc.id,
        name: fc.name,
        response: result
      })
    }
    
    // Enviar respuestas de las funciones al modelo
    if (websocket && websocket.readyState === WebSocket.OPEN) {
      websocket.send(JSON.stringify({
        toolResponse: { functionResponses }
      }))
    }
    
    processingToolCall = false
  }
  
  // ═══════════════════════════════════════════════════════════════
  // UTILIDADES
  // ═══════════════════════════════════════════════════════════════
  
  // Float32 a Int16 (PCM)
  const float32ToInt16 = (float32Array) => {
    const int16Array = new Int16Array(float32Array.length)
    for (let i = 0; i < float32Array.length; i++) {
      const s = Math.max(-1, Math.min(1, float32Array[i]))
      int16Array[i] = s < 0 ? s * 0x8000 : s * 0x7FFF
    }
    return int16Array
  }
  
  // ArrayBuffer a Base64
  const arrayBufferToBase64 = (buffer) => {
    const bytes = new Uint8Array(buffer)
    let binary = ''
    for (let i = 0; i < bytes.byteLength; i++) {
      binary += String.fromCharCode(bytes[i])
    }
    return btoa(binary)
  }
  
  // Base64 a ArrayBuffer
  const base64ToArrayBuffer = (base64) => {
    const binary = atob(base64)
    const bytes = new Uint8Array(binary.length)
    for (let i = 0; i < binary.length; i++) {
      bytes[i] = binary.charCodeAt(i)
    }
    return bytes.buffer
  }
  
  // Limpiar al desmontar el componente
  onUnmounted(() => {
    if (isConnected.value) {
      endCall()
    }
  })
  
  // ═══════════════════════════════════════════════════════════════
  // EXPORT
  // ═══════════════════════════════════════════════════════════════
  return {
    // Estado
    isConnected,
    isConnecting,
    isListening,
    isSpeaking,
    isActive,
    error,
    callDuration,
    formattedDuration,
    transcript,
    
    // Selector de voz
    showVoiceSelector,
    voices,
    selectedVoice,
    currentVoice,
    isFirstTime,
    
    // Carrusel de voces
    currentVoiceIndex,
    isPlayingPreview,
    
    // Métodos
    startCall,
    endCall,
    selectVoice,
    cancelVoiceSelection,
    
    // Navegación carrusel
    nextVoice,
    prevVoice,
    goToVoice,
    playVoicePreview,
    confirmVoiceSelection
  }
}
