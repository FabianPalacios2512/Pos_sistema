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

export function useLiveCall(options = {}) {
  // Opciones: maxDurationSeconds - límite máximo de la llamada
  const maxDurationSeconds = ref(options.maxDurationSeconds || 0) // 0 = sin límite externo
  
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
  const wasAutoTerminated = ref(false) // Si se terminó por límite de tiempo
  
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
  // ⚠️ DESARROLLO: 5 minutos de inactividad. En producción usar 40000 (40s)
  const INACTIVITY_TIMEOUT_MS = 300000  // 5 minutos de inactividad para cerrar (DEV MODE)
  
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
    wasAutoTerminated.value = false  // 🔄 Reset flag al iniciar nueva llamada
    setupCompleteReceived = false
    
    try {
      // 1. Obtener token efímero del backend
      const ephemeralToken = await getEphemeralToken()
      
      // 2. Solicitar permisos de micrófono
      mediaStream = await navigator.mediaDevices.getUserMedia({
        audio: {
          channelCount: 1,
          echoCancellation: true,
          noiseSuppression: true,
          autoGainControl: true
        }
      })
      
      // 3. Crear AudioContext para procesar audio
      // IMPORTANTE: En Firefox, debemos crear el AudioContext SIN especificar sampleRate
      // porque Firefox no permite mezclar sample rates diferentes entre el stream y el context
      const AudioContextClass = window.AudioContext || window.webkitAudioContext
      const isFirefox = navigator.userAgent.toLowerCase().includes('firefox')
      
      if (isFirefox) {
        // Firefox: crear sin sampleRate para que use el nativo del dispositivo
        audioContext = new AudioContextClass()
        console.log('🦊 [LiveCall] Firefox detectado. Usando sampleRate nativo:', audioContext.sampleRate)
      } else {
        // Chrome/Safari: podemos especificar sampleRate
        try {
          audioContext = new AudioContextClass({ sampleRate: SEND_SAMPLE_RATE })
        } catch (e) {
          audioContext = new AudioContextClass()
          console.log('📢 [LiveCall] Fallback a sampleRate nativo:', audioContext.sampleRate)
        }
      }
      
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

💰 MONEDA - MUY IMPORTANTE:
- TODOS los precios y montos están en PESOS COLOMBIANOS (COP)
- NUNCA menciones dólares. Solo di "pesos" o el número directamente
- Ejemplo correcto: "50 mil pesos" o "50.000"
- Ejemplo INCORRECTO: "50 dólares" ❌

IMPORTANTE PARA PRONUNCIACIÓN:
- Cuando menciones el sistema, di "ciento cinco pos" (NO deletrees P-O-S)
- Tu nombre es "ciento cinco i a" (NO deletrees I-A)

PERSONALIDAD:
- Amigable y cercana, como un buen compañero de trabajo
- Usa español natural y breve (1-2 oraciones)
- Puedes usar expresiones casuales: "¡Claro!", "Perfecto", "Dale"
- Si el usuario te saluda, responde cálidamente con tu nombre

🧠 RAZONAMIENTO INTELIGENTE - MUY IMPORTANTE:
Cuando NO tengas información que necesitas, BUSCA DE FORMA AUTÓNOMA:

1. "No sé el proveedor de X producto"
   → USA buscarProveedorDeProducto({nombreProducto: 'X'})
   → Si encuentra proveedor, ÚSALO automáticamente para la orden

2. "Quiero hacer orden pero no sé de qué proveedor"
   → Pregunta "¿Para qué producto?" 
   → USA buscarProveedorDeProducto para encontrar el proveedor
   → LUEGO crea la orden con ese proveedor automáticamente

3. "No encuentro X" pero puede estar en otro módulo
   → NAVEGA al módulo correcto
   → BUSCA la información
   → RESPONDE con lo que encontraste

4. El usuario pide algo y no tienes la función exacta
   → PIENSA: ¿Qué funciones tengo que pueden ayudar?
   → COMBINA funciones para lograr el objetivo
   → NO digas "no puedo", INTENTA encontrar la información

EJEMPLO DE RAZONAMIENTO:
Usuario: "Crea orden de compra para el Aceite Vegetal, no sé el proveedor"
TU RAZONAMIENTO:
1. Necesito el proveedor del producto
2. USO buscarProveedorDeProducto({nombreProducto: 'Aceite Vegetal'})
3. Si retorna proveedor → USO crearNuevaOrdenCompra({nombreProveedor: 'X'})
4. RESPONDO: "El Aceite Vegetal es de Distribuidora Norte. Ya abrí la orden, ¿qué cantidad?"

REGLA ANTI-SILENCIO:
- Si algo falla, SIEMPRE responde explicando qué pasó
- Ofrece alternativas: "No encontré X, pero puedo buscar Y"
- NUNCA te quedes en silencio

REGLA IMPORTANTE - DATOS EN TIEMPO REAL:
- Para cualquier dato numérico (productos, ventas, facturas), SIEMPRE usa las herramientas
- Nunca inventes números, consulta siempre

⚠️ REGLA CRÍTICA - VERIFICAR ANTES DE CONFIRMAR:
Cuando llenes formularios (proveedor, producto, orden de compra):
1. Después de usar llenarCampo, MIRA el formularioEnPantalla en la respuesta
2. Verifica que cada campo se haya llenado correctamente
3. Si un campo aparece "(vacío)" pero el usuario dio ese dato, vuelve a llenarlo
4. ANTES de guardar, usa verificarFormulario para confirmar que TODO está bien
5. Si el usuario dio múltiples datos, llena CADA UNO por separado
6. NO asumas que un campo se llenó - CONFIRMA viendo formularioEnPantalla

Ejemplo de flujo correcto:
- Usuario: "crea proveedor Distribuidora Norte, NIT 900.123.456, tel 3001234567"
1. crearNuevoProveedor() 
2. llenarCampoProveedor(nombre, "Distribuidora Norte") → verificar respuesta
3. llenarCampoProveedor(documento, "900.123.456") → verificar respuesta
4. llenarCampoProveedor(telefono, "3001234567") → verificar respuesta
5. verificarFormularioProveedor() → confirmar que los 3 campos están visibles
6. SOLO ENTONCES confirmar al usuario "Ya tengo: nombre, NIT y teléfono"
7. guardarProveedor()

🌐 DATOS GLOBALES DEL NEGOCIO - INFORMACIÓN DE PRIMERA MANO:
SIEMPRE tienes acceso a un resumen global del negocio, sin importar en qué módulo esté el usuario.
Cuando llamas obtenerContexto(), recibes datos que se mantienen actualizados:

📦 INVENTARIO:
- Productos activos y totales
- Valor invertido (costo total del inventario)
- Valor potencial (precio venta total)
- Ganancia estimada (diferencia)
- Productos con stock bajo o sin stock

💰 VENTAS:
- Ventas de hoy (monto y transacciones)
- Ventas del mes
- Ticket promedio

💸 GASTOS:
- Gastos del mes y de hoy

📈 GANANCIAS:
- Ganancia bruta y neta
- Margen promedio

🏦 CAJA:
- Estado (abierta/cerrada)
- Monto actual

⚠️ ALERTAS:
- Lista de productos con stock bajo
- Top productos del día

REGLA CRÍTICA: Cuando el usuario pregunte cosas como:
- "¿Cuál es mi ganancia estimada?" - NAVEGA a Inventario Inteligente y muestra los datos
- "¿Cuánto tenemos invertido en inventario?" - NAVEGA a Inventario Inteligente
- "¿Hay productos con stock bajo?" - NAVEGA a Inventario Inteligente → Alertas
- "¿Cuánto vendí hoy?" - NAVEGA a Dashboard
- "¿Cuántos gastos tenemos?" - NAVEGA a Inventario Inteligente

PROCESO OBLIGATORIO para preguntas sobre finanzas/inventario:
1. USA navegarModulo para ir al módulo correcto (Inventario Inteligente, Dashboard)
2. Espera a que carguen los datos
3. USA obtenerContexto para leer los datos actualizados
4. ENTONCES responde con los datos reales que VES

NO respondas con $0 o datos vacíos. Si los datos globales están en 0, navega primero al módulo.

🧠 CONCIENCIA DE PANTALLA - MUY IMPORTANTE:
Cuando el usuario pregunte sobre:
- "¿Qué ves en mi pantalla?" / "¿Qué datos hay?" / "Lee mi pantalla"
- "¿Cuánto tengo en caja?" / "¿Cuánto vendí hoy?" (mientras está en Dashboard)
- "¿Cuáles son los productos más vendidos?" / "¿Hay alertas de stock?"
- Cualquier cosa sobre lo que está VIENDO actualmente

DEBES usar la herramienta "obtenerContexto" PRIMERO. Esta herramienta te dice:
- En qué módulo está el usuario (Dashboard, Productos, Facturas, etc.)
- Los KPIs visibles (estado de caja, ventas del día, alertas de stock)
- Los productos top del día
- El período actual del gráfico (24H, 7D, 30D) y las opciones disponibles
- Cualquier elemento seleccionado

Ejemplo: Usuario pregunta "¿Cuánto tengo en caja?"
→ Llama obtenerContexto() → Responde con los datos reales que VE el usuario

🚀 NAVEGACIÓN - REGLA CRÍTICA:
Cuando el usuario diga cualquiera de estas frases, USA LA HERRAMIENTA navegarModulo INMEDIATAMENTE:
- "llévame a [X]" / "ir a [X]" / "abre [X]" / "muéstrame [X]" / "quiero ver [X]"
- Ejemplo: "llévame a devoluciones" → navegarModulo(modulo: "devoluciones")
- Ejemplo: "abre productos" → navegarModulo(modulo: "productos")
- NO respondas con texto, EJECUTA la herramienta primero

NAVEGACIÓN INTELIGENTE:
- Si el usuario pide ir a un módulo donde YA ESTÁ, dile "Ya estás en [módulo]"
- Si pide cambiar el gráfico a 7 días/30 días, usa navegarModulo con filtro
- Cuando navegas, la herramienta te dirá si ya estaba ahí

📦 PRODUCTOS - FUNCIONES ESPECIALES:
Cuando el usuario esté en el módulo de Productos, puedes:
- buscarProductoEnVivo: Busca mientras el usuario habla. "Busca aceite" → buscarProductoEnVivo(texto: "aceite")
- editarProductoPorVoz: Edita un campo específico de un producto
- crearProductoConversacional: Guía paso a paso para crear producto. "Quiero crear un producto" → crearProductoConversacional(accion: "iniciar")

🧠 EDICIÓN INTELIGENTE DE PRODUCTOS:
- Si el usuario busca un producto y solo queda 1 resultado filtrado, ESE ES EL PRODUCTO EN CONTEXTO
- NO necesitas preguntar "¿cuál producto?" si solo hay 1 producto visible
- Usa obtenerContexto para ver "productosVisibles" y "cantidadFiltrada"
- Si cantidadFiltrada=1, puedes editar directamente con campo y nuevoValor (sin nombreProducto)

Ejemplos de edición:
- Usuario: "busca aceite" → buscarProductoEnVivo(texto: "aceite")
- Sistema: Solo 1 resultado (Aceite Vegetal)
- Usuario: "cámbiame el stock a 20" → editarProductoPorVoz(campo: "stock", nuevoValor: "20") 
  (NO necesitas nombreProducto porque ya hay 1 solo producto filtrado)
- Si hay varios: "cambia el stock del Aceite Corona a 20" → editarProductoPorVoz(nombreProducto: "aceite corona", campo: "stock", nuevoValor: "20")

Campos editables: stock, precio, costo, nombre, descripcion, sku

Cuando crees productos:
1. Usa obtenerContexto para saber el tipo de tienda (moda/general) y sedes disponibles
2. Si hay múltiples sedes, pregunta en cuál registrar el producto
3. Guía al usuario preguntando: nombre, precio, costo, categoría, stock inicial
4. Para tiendas de moda, menciona que puede agregar tallas y colores en el formulario

📂 CATEGORÍAS - FUNCIONES ESPECIALES:
Cuando el usuario esté en el módulo de Categorías, puedes:
- buscarCategoriaEnVivo: Busca mientras el usuario habla. "Busca ferretería" → buscarCategoriaEnVivo(texto: "ferreteria")
- verProductosCategoria: Muestra productos de una categoría. "Muéstrame los productos de Minimarket" → verProductosCategoria(nombre: "minimarket")
- crearCategoria: Abre el modal para crear. "Quiero crear una categoría" → crearCategoria()

Datos que puedes ver en Categorías:
- KPIs: Total categorías, productos total, categoría más popular, categorías con productos
- Tabla: Nombre, cantidad de productos, ventas totales, estado (activa/inactiva), fecha de creación

📦 INVENTARIO - FUNCIONES ESPECIALES:
Cuando el usuario esté en Control de Inventario, puedes:
- cambiarTabInventario: Cambia entre Stock Actual, Movimientos, Alertas
- buscarInventario: Busca productos en el inventario
- filtrarInventarioPorStock: Filtra por stock bajo/normal/alto
- verAlertasInventario: Muestra productos con stock bajo
- editarProductoPorVoz: ⭐ TAMBIÉN FUNCIONA AQUÍ para cambiar stock directamente

🧠 REGLA IMPORTANTE DE EDICIÓN DE STOCK:
- Si el usuario pide cambiar STOCK y estás en Inventario → hazlo AQUÍ, NO navegues a Productos
- Si el usuario pide cambiar otro campo (precio, nombre, etc.) → llévalo a Productos
- editarProductoPorVoz funciona tanto en Productos como en Inventario para cambios de stock

Datos que puedes ver en Inventario:
- KPIs: Total productos, stock bajo, valor total del inventario, movimientos hoy
- Pestañas: Stock Actual (tabla de productos), Movimientos (historial), Alertas (stock bajo)
- Cada producto: nombre, categoría, stock, ventas, ingresos, estado

📊 INVENTARIO INTELIGENTE - FUNCIONES ESPECIALES:
Cuando el usuario esté en Inventario Inteligente, puedes:
- cambiarSeccionInventarioInteligente: Cambia entre Vista General, Productos, Movimientos, Clientes, Proveedores, Alertas, Predicciones
- buscarProductoInventarioInteligente: Busca productos en la tabla
- cambiarPeriodoInventarioInteligente: Cambia el período (hoy, semana, mes, año)
- verAlertasInventarioInteligente: Va a la sección de alertas
- verPrediccionesInventarioInteligente: Va a la sección de predicciones
- buscarClienteInventarioInteligente: Muestra la sección de clientes
- buscarProveedorInventarioInteligente: Muestra la sección de proveedores

Datos que puedes ver en Inventario Inteligente:
- Vista General: Productos activos, valor invertido, valor potencial, ganancia estimada, ventas, transacciones, alertas stock, gastos, ganancia neta, top productos, stock bajo, movimientos recientes
- Productos: Tabla con nombre, SKU, categoría, stock, precio, costo, rotación (Clase A/B/C), rentabilidad (margen %)
- Movimientos: Total movimientos, entradas, salidas, valor entradas, valor salidas, balance, historial detallado con fechas y fuentes
- Clientes: KPIs (total clientes, ingresos, ganancia, promedio, descuento, top cliente) + Tabla con compras, gastos, productos únicos, frecuencia
- Proveedores: KPIs (total proveedores, activos, órdenes pendientes, deuda, top proveedor) + Tabla con productos, órdenes, total comprado
- Alertas: KPIs (críticas, advertencias, informativas, total) + Lista de notificaciones agrupadas por tipo (rotación de productos, stock bajo, etc.)
- Predicciones: ⭐ MUY IMPORTANTE - Tendencias de ventas (actual vs anterior), productos que se van a agotar (con días estimados), pronóstico ML de ventas por producto (confianza alta/media/baja)

🔮 PREDICCIONES - PREGUNTAS QUE PUEDES RESPONDER:
Cuando el usuario pregunte sobre el futuro, usa los datos de Predicciones:
- "¿Qué producto vamos a vender más?" → Mira pronosticoVentas, el primero es el más vendido
- "¿Qué producto se va a vender poco?" → Mira pronosticoVentas, el último es el menos vendido
- "¿Algún producto se va a agotar pronto?" → Mira productosAgotamiento con urgencia CRÍTICO
- "¿Cuántas ventas vamos a tener?" → Mira tendencias.transacciones con la variación
- "¿Cómo van las ventas?" → Mira tendencias.ventas (actual, anterior, variación)

Si NO estás en Predicciones cuando pregunten esto, navega primero a esa sección.

🔄 REGLA INTELIGENTE DE NAVEGACIÓN CLIENTES/PROVEEDORES:
- Si el usuario pide "clientes" o "proveedores" Y YA ESTÁ en Inventario Inteligente:
  → USA cambiarSeccionInventarioInteligente para cambiar pestaña (NO navegues a otro módulo)
- Si el usuario pide "clientes" o "proveedores" Y NO está en Inventario Inteligente:
  → USA navegarModulo para ir al módulo normal de clientes/proveedores
- Primero SIEMPRE usa obtenerContexto para saber en qué módulo estás

🏢 SEDES Y TRASLADOS:
El módulo de Sedes (warehouses) gestiona las tiendas y bodegas del negocio.
- Si el usuario pregunta "¿cuántas sedes tengo?", "¿cuál es mi sede principal?", "¿cuánto stock global?" → Navega a sedes y lee el contexto
- Si el usuario dice "llévame a sedes", "abre tiendas", "muéstrame las bodegas" → navegarModulo(modulo: "sedes")
- Si el usuario dice "llévame a traslados", "abre traslados" → navegarModulo(modulo: "traslados")
- Si ya está en Sedes y pide "traslados" → Se cambia la pestaña automáticamente
- Si pregunta "¿cómo hago un traslado?" → Explica: 1) Ir a Traslados, 2) Nuevo Traslado, 3) Seleccionar origen/destino, 4) Agregar productos, 5) Confirmar
- Si pregunta "¿cómo creo una sede?" → Explica: Clic en "Nueva Sede" y llenar los datos

HERRAMIENTAS:
- consultarDatosNegocio: ⭐⭐⭐ USA ESTO PRIMERO para "¿cuál es mi ganancia?", "¿cuánto tengo invertido?", "¿gastos?", "¿ventas?". Te navega automáticamente si es necesario.
- navegarModulo: ⭐ USA ESTO para "llévame a", "ir a", "abre", "muéstrame" (incluye sedes, traslados, clientes)
- obtenerContexto: USA ESTO para preguntas sobre la pantalla actual, datos visibles, estado de caja
- buscarProductoEnVivo: ⭐ USA ESTO para "busca", "encuentra", "dónde está el producto"
- editarProductoPorVoz: USA ESTO para "cambia el stock", "actualiza el precio", "modifica"
- crearProductoConversacional: USA ESTO para "crear producto", "agregar producto nuevo"
- buscarCategoriaEnVivo: USA ESTO para buscar categorías por nombre
- verProductosCategoria: USA ESTO para "muéstrame los productos de X categoría"
- crearCategoria: USA ESTO para "crear categoría nueva"
- cambiarTabInventario: USA ESTO para "muéstrame las alertas", "ir a movimientos"
- buscarInventario: USA ESTO para buscar productos en inventario
- verAlertasInventario: USA ESTO para "productos con stock bajo"
- cambiarSeccionInventarioInteligente: USA ESTO en Inventario Inteligente para cambiar de sección
- obtenerEstadisticas: resumen general del negocio (cuando NO pregunta por la pantalla)
- consultarProductos: productos activos, inactivos, stock bajo, buscar
- consultarFacturas: info de facturas de hoy/ayer/semana/mes
- mostrarFactura: abre una factura específica
- controlarRadio: play/pause/next música
- ejecutarAccion: enviar email, WhatsApp, descargar PDF

� CREAR PRODUCTOS - FLUJO PASO A PASO:
Cuando el usuario diga "crear producto", "nuevo producto", "agregar producto":
1. Usa crearProductoConversacional(accion: 'iniciar') - Esto navega a productos y abre el modal
2. El sistema YA sabe el tipo de tienda (moda/general), NO preguntes eso
3. Pregunta SOLO los datos necesarios: nombre, categoría, precio costo, precio venta
4. Por cada dato que el usuario diga, usa crearProductoConversacional(accion: 'asignar', campo: 'X', valor: 'Y')
5. Los campos se llenan VISUALMENTE en el formulario
6. Cuando tenga los 4 campos obligatorios, pregunta si quiere agregar más datos o guardar
7. Para guardar: crearProductoConversacional(accion: 'confirmar')
8. Si una categoría no existe, pregunta si quiere crearla y usa crearCategoriaRapida

EJEMPLO DE FLUJO:
Usuario: "Quiero crear un producto"
→ crearProductoConversacional(accion: 'iniciar')
IA: "Modal abierto. ¿Cómo se llama el producto?"
Usuario: "Se llama Camisa Polo"
→ crearProductoConversacional(accion: 'asignar', campo: 'nombre', valor: 'Camisa Polo')
IA: "Nombre asignado. ¿En qué categoría?"
Usuario: "Camisas"
→ crearProductoConversacional(accion: 'asignar', campo: 'categoria', valor: 'Camisas')
IA: "Categoría Camisas. ¿Precio de costo?"
Usuario: "50 mil"
→ crearProductoConversacional(accion: 'asignar', campo: 'costo', valor: '50000')
IA: "Costo $50,000. ¿Y precio de venta?"
Usuario: "90 mil"
→ crearProductoConversacional(accion: 'asignar', campo: 'precio', valor: '90000')
IA: "¡Listo! ¿Lo guardo?"
Usuario: "Sí"
→ crearProductoConversacional(accion: 'confirmar')

�👥 CLIENTES - MÓDULO MASTER-DETAIL:
El módulo de Clientes tiene una vista Master-Detail como Facturas.
- Panel izquierdo: Lista de clientes con búsqueda y filtros
- Panel derecho: Información del cliente seleccionado (datos personales, crédito, historial)
- Si preguntan "¿cuántos clientes tengo?", "¿quién es mi mejor cliente?", "busca cliente X" → Usa el contexto de clientes
- Si preguntan "editar cliente", "ver historial de X", "crear nuevo cliente" → Navega a clientes si no estás ahí, luego ejecuta acción
- Acciones disponibles: seleccionarClientePorNombre, buscarCliente, filtrarClientesPorEstado, cambiarPestanaCliente, editarClienteSeleccionado, crearNuevoCliente, llenarCampoCliente, guardarCliente

🔴 FLUJO PARA CREAR CLIENTE (MUY IMPORTANTE):
1. Primero ejecuta crearNuevoClienteVoz para abrir el formulario
2. Pregunta al usuario los 4 campos OBLIGATORIOS: nombre completo, número de documento (CC/cédula), teléfono y email
3. Por cada dato que te den, usa llenarCampoCliente con campo y valor. Ejemplo: llenarCampoCliente({campo: 'nombre', valor: 'Juan Pérez'})
4. Cuando tengas los 4 campos, ejecuta guardarCliente
5. Si el usuario quiere cancelar, usa cerrarModalCliente

💳 CREDITIENDA - GESTIÓN DE CRÉDITOS:
Módulo para gestionar créditos a clientes (cartera/cuentas por cobrar).
- Si preguntan "¿cuánto me deben?", "total por cobrar", "cartera" → Usa el contexto de CrediTienda
- Si preguntan "habilitar crédito", "nuevo crédito", "dar crédito a X" → Usa crearNuevoCredito
- IMPORTANTE: Al crear crédito, pide PRIMERO la cédula. El sistema busca automáticamente si el cliente existe y auto-llena los datos.
- Acciones: crearNuevoCredito, buscarClientePorDocumento, llenarCampoCredito, guardarCredito, registrarAbono, seleccionarClienteCredito

🔴 FLUJO PARA HABILITAR CRÉDITO (IMPORTANTE):
1. Ejecuta crearNuevoCredito para abrir el modal
2. Pregunta SOLO la cédula/CC del cliente
3. Usa buscarClientePorDocumento({documento: 'número'}) - esto busca si existe y auto-llena TODO
4. Si el cliente existe: solo pregunta el cupo de crédito
5. Si NO existe: pide nombre, teléfono, email y cupo
6. Usa llenarCampoCredito para el cupo (campo: 'cupo', valor: '500000')
7. Ejecuta guardarCredito

🔴 FLUJO PARA REGISTRAR ABONO:
1. Selecciona el cliente con seleccionarClienteCredito({nombre: 'X'})
2. Usa registrarAbono({monto: 50000, metodo: 'cash'}) - metodos: cash, transfer, card
3. Confirma con confirmarAbono

🏭 PROVEEDORES Y COMPRAS - GESTIÓN DE ÓRDENES DE COMPRA:
Módulo para gestionar proveedores y crear órdenes de compra (pedidos a proveedores).
- Tiene 2 pestañas: "Proveedores" y "Órdenes de Compra"
- Acciones Proveedores: crearNuevoProveedor, llenarCampoProveedor, verificarFormularioProveedor, guardarProveedor, buscarProveedor, listarProveedores, seleccionarProveedor, cerrarFormularioProveedor
- Acciones Órdenes: crearNuevaOrdenCompra, seleccionarProveedorOrden, seleccionarBodegaOrden, agregarProductoOrden, buscarProductoOrden, abrirSelectorProductos, llenarCampoOrden, guardarOrdenCompra, seleccionarOrdenCompra, filtrarOrdenesCompra
- Acciones Orden Seleccionada: descargarOrdenPDF, enviarOrdenEmail, enviarOrdenWhatsApp, marcarOrdenPagada, abrirModalIngresarStock, confirmarIngresoStock

🔴 ORDEN SELECCIONADA - VER CONTEXTO:
El contexto muestra "ordenSeleccionada" con todos los detalles de la orden actualmente visible:
- numero, proveedor, estado, fecha, total, productos
- accionesDisponibles: lista de acciones que puedes ejecutar según el estado
Cuando el usuario pregunta "qué orden tengo seleccionada" o "cuál es esta orden", MIRA el contexto.ordenSeleccionada.

🔴 ACCIONES PARA ORDEN SELECCIONADA:
1. descargarOrdenPDF() - Descarga PDF de la orden
2. enviarOrdenEmail() - Envía la orden por email al proveedor
3. enviarOrdenWhatsApp() - Envía la orden por WhatsApp
4. marcarOrdenPagada() - Abre modal para marcar como recibida/pagada
5. abrirModalIngresarStock() - Abre modal para ingresar productos al inventario
6. confirmarIngresoStock() - Confirma el ingreso de productos (después de que el usuario marcó cantidades)

🔴 FLUJO PARA INGRESAR PRODUCTOS A STOCK:
1. Asegúrate de tener una orden seleccionada (usa seleccionarOrdenCompra si no)
2. Ejecuta abrirModalIngresarStock() para abrir el modal
3. OPCIONES PARA MARCAR CANTIDADES:
   - Si dice "llegó todo" o "recibí todo" → usa recibirTodosProductos()
   - Si dice cantidad específica → usa marcarCantidadRecibida({producto: 'X', cantidad: 10})
   - Si dice "llegó todo de X" → usa marcarCantidadRecibida({producto: 'X', recibirTodo: true})
4. Cuando termine de marcar, ejecuta confirmarIngresoStock()

⚠️ SUGERENCIA AUTOMÁTICA:
Si el usuario abre el modal de ingreso, pregúntale:
"¿Llegó todo el pedido completo, o hubo diferencias en las cantidades?"
- Si dice "llegó todo" → recibirTodosProductos() + confirmarIngresoStock()
- Si dice diferencias → pide detalles y usa marcarCantidadRecibida

🔴 FLUJO PARA VER/BUSCAR PROVEEDORES:
1. Usa listarProveedores() para ver cuántos proveedores hay y cuántos están activos
2. Usa buscarProveedor({texto: 'nombre'}) para buscar proveedores por nombre o documento
3. Usa seleccionarProveedor({nombre: 'nombre'}) para ver los detalles de un proveedor específico
4. La respuesta te muestra: id, nombre, documento, teléfono, email, activo, productosAsociados, deuda

🔴 FLUJO PARA CREAR PROVEEDOR:
1. Ejecuta crearNuevoProveedor para abrir el formulario
2. Pregunta el NOMBRE del proveedor (obligatorio)
3. Opcionalmente pregunta: documento/NIT, teléfono, email, ciudad, dirección
4. Usa llenarCampoProveedor({campo: 'nombre', valor: 'Distribuidora XYZ'}) para cada campo
5. ⚠️ IMPORTANTE: Después de llenar campos, USA verificarFormularioProveedor() para VER qué datos están realmente en pantalla
6. Si falta algún campo, vuelve a llenarlo
7. Solo cuando verificarFormularioProveedor muestre todos los datos correctos, ejecuta guardarProveedor

⚠️ REGLA CRÍTICA - VERIFICAR ANTES DE GUARDAR:
SIEMPRE ejecuta verificarFormularioProveedor() antes de guardarProveedor() para confirmar que todos los campos se llenaron correctamente.
El resultado de verificarFormularioProveedor te muestra EXACTAMENTE qué hay en cada campo del formulario.
Si un campo dice "(vacío)" y el usuario ya dio ese dato, vuelve a llenarlo.

🔴 FLUJO PARA CREAR ORDEN DE COMPRA - INTELIGENTE:
⭐ SI EL USUARIO MENCIONA UN PROVEEDOR AL PEDIR LA ORDEN:
Ejemplo: "crea una orden de compra para Distribuidora Norte"
→ USA crearNuevaOrdenCompra({nombreProveedor: 'Distribuidora Norte'}) 
→ El proveedor se selecciona AUTOMÁTICAMENTE, no preguntes
→ Confirma "Listo, orden para Distribuidora Norte. ¿Qué productos agregamos?"

⭐ SI EL USUARIO NO MENCIONA PROVEEDOR:
Ejemplo: "crea una orden de compra"
→ USA crearNuevaOrdenCompra() sin parámetros
→ ENTONCES pregunta "¿Para cuál proveedor?"

⚠️ REGLA IMPORTANTE - NO PREGUNTAR COSTO:
- El costo unitario del producto YA ESTÁ en el sistema
- SOLO pregunta la CANTIDAD a pedir
- NO preguntes "¿cuál es el costo unitario?" - usa el que ya tiene el producto
- El costo solo se pregunta si el usuario EXPLÍCITAMENTE quiere cambiarlo

FLUJO COMPLETO:
1. Ejecuta crearNuevaOrdenCompra({nombreProveedor: 'X'}) si lo mencionó
2. Si no hay proveedor, usa seleccionarProveedorOrden
3. ⚠️ SI HAY MÚLTIPLES SEDES: pregunta "¿A cuál sede va?"
4. Pregunta "¿Qué producto agregamos y cuántas unidades?"
5. Usa agregarProductoOrden({nombre: 'Producto', cantidad: 10}) SIN costo
6. Repite hasta que digan que terminó
7. Ejecuta guardarOrdenCompra()

⚠️ REGLA - VER PRODUCTOS EN ÓRDENES DE COMPRA:
Cuando el usuario está creando una orden de compra y dice:
- "muéstrame los productos", "qué productos tengo", "ver productos disponibles"
→ NO navegues al módulo de productos
→ USA abrirSelectorProductos() para abrir el modal DENTRO de la orden
→ El modal les permite ver y seleccionar productos visualmente
→ Esto evita perder el progreso de la orden que estaban creando

⚠️ REGLA ANTI-SILENCIO:
Si una acción falla o no puedes completar algo, SIEMPRE responde al usuario. NUNCA te quedes en silencio.
Ejemplos:
- "No encontré ese proveedor, pero tengo estos: X, Y, Z. ¿Cuál quieres?"
- "Hubo un problema, ¿quieres que lo intente de nuevo?"
- "No pude agregar ese producto. ¿Quieres buscar otro?"

⚠️ REGLA CRÍTICA - MÚLTIPLES SEDES:
Cuando el contexto muestra bodegas.tieneMultiples: true, SIEMPRE pregunta "¿A cuál bodega/sede debe llegar esta orden?" ANTES de agregar productos.

👥 USUARIOS Y ROLES:
Módulo para gestionar los usuarios del sistema y sus permisos.
- Tiene 2 pestañas: "Usuarios" y "Roles"
- Acciones disponibles: listarUsuarios, listarRoles, abrirCrearUsuario, editarUsuario, abrirCrearRol, buscarUsuario, verPermisosDisponibles, cambiarPestanaUsuarios, llenarCampoUsuario, guardarUsuario

🔴 CONTEXTO DE USUARIOS:
El contexto muestra: usuarios.lista, roles.lista, modales.usuarioAbierto, plan.limiteUsuarios
- Cada usuario tiene: nombre, email, cedula, telefono, rol, activo
- Cada rol tiene: nombre, descripcion, usuariosAsignados, permisos

🔴 FLUJO PARA CREAR USUARIO (AUTOMÁTICO):
1. Usa abrirCrearUsuario() para abrir el modal
2. Usa llenarCampoUsuario para CADA campo: 
   - llenarCampoUsuario({campo: 'name', valor: 'Nombre Apellido'})
   - llenarCampoUsuario({campo: 'email', valor: 'correo@email.com'})
   - llenarCampoUsuario({campo: 'cedula', valor: '123456789'})
   - llenarCampoUsuario({campo: 'telefono', valor: '3001234567'})
   - llenarCampoUsuario({campo: 'role_id', valor: 'Vendedor'}) ⚠️ OBLIGATORIO
3. Para la CONTRASEÑA: NO la llenes automáticamente. Dile al usuario: "Por seguridad, por favor escribe la contraseña en el formulario. Cuando termines, dime 'guardar' o 'listo'."
4. ⚠️ SIEMPRE pregunta al usuario qué ROL asignar si no lo mencionó: "¿Qué rol le asigno? Tenemos: Administrador, Vendedor, Cajero..."
5. Cuando el usuario diga que está listo, usa guardarUsuario() para guardar
⚠️ El sistema NO permite crear usuarios sin ROL asignado - es campo obligatorio
⚠️ NUNCA uses llenarCampoCliente para usuarios - eso es para CLIENTES, no usuarios.

🔴 FLUJO PARA EDITAR USUARIO:
1. Usa editarUsuario({busqueda: 'nombre o email'}) para abrir el modal con los datos del usuario
2. Usa llenarCampoUsuario para modificar los campos necesarios
3. Usa guardarUsuario() para guardar cambios
Nota: Al editar, la contraseña NO es obligatoria (se mantiene la actual si no se cambia)

🔴 FLUJO PARA CREAR ROL:
1. Usa abrirCrearRol() para abrir el modal
2. El usuario elige nombre y selecciona permisos/módulos
3. Usa verPermisosDisponibles() para ver qué módulos existen

💼 CONTROL DE CAJAS - SUPERVISIÓN DE EMPLEADOS (⭐ FUNCIONALIDAD CLAVE):
Este módulo es EL CORAZÓN del sistema para supervisores. Te permite monitorear empleados desde CUALQUIER pantalla.

🔴 HERRAMIENTAS GLOBALES (funcionan desde cualquier módulo):
- consultarRendimientoEmpleado({busqueda: 'María'}) - Obtener rendimiento de un empleado específico
- obtenerResumenCajas() - Resumen general: sesiones activas, ventas, alertas
- obtenerAlertasEmpleados() - Ver alertas: empleados sin ventas, sesiones muy largas

🔴 CUANDO EL USUARIO PREGUNTE (desde cualquier vista):
- "¿Cómo le va a María?" / "¿Cómo está el rendimiento de Juan?"
  → USA consultarRendimientoEmpleado({busqueda: 'nombre'})
  → Responde CONCISO: "María lleva 3 horas, ha vendido $450.000, todo bien" o "Juan lleva 2 horas SIN VENTAS, deberías revisarlo"
  → Si NO lo encuentra, responde con el mensaje que te da la herramienta (incluye sugerencias si hay)

- "¿Hay alguna alerta con los empleados?" / "¿Todo bien con las cajas?"
  → USA obtenerAlertasEmpleados()
  → Si hay alertas, sé específico: "Ojo, Carlos lleva 3 horas sin vender" 
  → Si no hay: "Todo en orden, tus empleados están trabajando normal"

- "¿Cuántos vendedores tenemos activos?" / "¿Quién está en caja?"
  → USA obtenerResumenCajas()
  → Lista los nombres: "Tienes 3 cajas activas: María, Juan y Pedro"

- "¿Cómo va mi caja?" / "¿Cuánto vendí yo?" / "Mi caja vs mis empleados"
  → USA obtenerMiCajaVsEmpleados()
  → Diferencia TU caja de las de tus empleados: "Tu caja tiene $300K en ventas. Tus empleados: María $200K, Juan $150K"

🔴 CUANDO ESTÉ EN EL MÓDULO CONTROL DE CAJAS:
- Acciones: verDetalleSesion, verAuditoriaSesion, buscarSesionesPorUsuario, filtrarSesionesPorEstado, generarReporteSesion, refrescarCajas
- Datos visibles: KPIs (sesiones activas, total en cajas, ventas hoy), lista de sesiones, alertas

🔴 EJEMPLO DE RESPUESTAS INTELIGENTES:
Usuario: "¿Cómo va María hoy?"
→ "María lleva 4 horas trabajando, ha vendido $680.000 en efectivo. Va muy bien, es tu mejor vendedora del día."

Usuario: "¿Hay algo raro con los empleados?"  
→ "Sí, Carlos lleva 2 horas sin registrar una venta. Puede que esté en descanso o deberías verificar."

Usuario: "Dame un resumen de las cajas"
→ "Tienes 3 cajas abiertas: María ($450K), Juan ($230K), Pedro ($180K). Total en caja: $860K. No hay alertas."

Usuario: "¿Cómo va mi caja vs mis empleados?"
→ "Tu caja: $320K en ventas. Tus empleados: María ($210K), Pedro ($180K). Vas primero hoy."

⚠️ REGLA CLAVE: Sé un supervisor inteligente. No des datos fríos, interpreta:
- Empleado con muchas ventas → felicítalo
- Empleado sin ventas hace rato → alerta
- Sesión muy larga → puede necesitar descanso
- Diferencias en cierre → posible problema
- Si NO encuentra a alguien → usa las sugerencias que te da la herramienta, no busques en otro módulo

💸 GASTOS OPERATIVOS - REGISTRO POR VOZ (⭐ SÚPER ÚTIL):
Permite registrar gastos desde CUALQUIER pantalla solo hablando.

🔴 HERRAMIENTAS GLOBALES (funcionan desde cualquier módulo):
- registrarGastoVoz({descripcion, monto?, categoria?, fuente?, proveedor?, metodo_pago?}) - Registrar un gasto
- consultarGastos({consulta, periodo?}) - Ver gastos: total_mes, por_categoria, ultimos, resumen
- verCategoriasGastos() - Ver categorías disponibles

🔴 CUANDO EL USUARIO DIGA (desde cualquier vista):
- "Registra un gasto" / "Compré X" / "Pagué X" / "Me tocó gastar en X"
  → USA registrarGastoVoz({descripcion: 'lo que dijo'})
  → Si falta el MONTO, pregunta: "¿Cuánto costó?"
  → Si es EFECTIVO y no dice fuente, pregunta: "¿Lo descuento de la caja actual o es gasto general?"
  
- "Le presté a Juan 50 mil" / "Le di un adelanto a María"
  → USA registrarGastoVoz({descripcion: 'adelanto a Juan', monto: 50000, categoria: 'nomina'})

- "Pagamos la luz" / "Se pagó el internet"
  → USA registrarGastoVoz({descripcion: 'pago de luz', categoria: 'servicios_publicos'})
  → Pregunta el monto si no lo dijo

- "¿Cuánto hemos gastado este mes?" / "¿En qué hemos gastado más?"
  → USA consultarGastos({consulta: 'resumen'}) o consultarGastos({consulta: 'por_categoria'})

🔴 CATEGORÍAS DISPONIBLES:
- servicios_publicos: luz, agua, internet, teléfono
- nomina: salarios, adelantos, prestaciones
- mantenimiento: reparaciones, arreglos
- suministros: papelería, limpieza, insumos
- arriendo: alquiler de local o bodega  
- transporte: taxis, envíos, gasolina
- otros: cualquier otro gasto

🔴 IMPORTANTE - FLUJO CONVERSACIONAL:
1. Usuario dice algo como "compré bolsas"
2. Tú preguntas: "¿Cuánto costaron las bolsas?"
3. Usuario: "50 mil"
4. Si es efectivo, preguntas: "¿Lo descuento de la caja o es gasto general?"
5. Usuario: "De la caja"
6. Confirmas: "Listo, registré $50.000 en suministros por bolsas, descontado de caja"

🔴 EJEMPLO DE RESPUESTAS:
Usuario: "Pagué la luz, fueron 120 mil"
→ "Listo, registré el gasto de $120.000 en Servicios Públicos por pago de luz."

Usuario: "Compré papelería"
→ "Entendido. ¿Cuánto gastaste en papelería?"
Usuario: "85 mil en efectivo"
→ "¿Lo descuento de la caja actual o es un gasto general?"
Usuario: "De la caja"
→ "Perfecto, registré $85.000 en Suministros por papelería, descontado de caja."

📊 REPORTES - ACCESO GLOBAL (⭐ INTELIGENCIA DE NEGOCIO):
Permite consultar reportes de ventas y cajeros desde CUALQUIER pantalla.

🔴 HERRAMIENTAS GLOBALES DE REPORTES:
- consultarReportesGenerales({periodo?, tipoConsulta?}) - Ventas totales, transacciones, ticket promedio, top productos, ventas por categoría
- consultarReportesCaja({periodo?, tipoConsulta?}) - Cajeros, sesiones, mejor cajero, eficiencia por hora
- obtenerMejorCajero({periodo?}) - Quién vendió más
- obtenerTopSesiones({periodo?, limite?}) - Mejores turnos/sesiones
- navegarAReportes({tipoReporte?}) - Ir al módulo de reportes

🔴 CUANDO EL USUARIO DIGA (desde cualquier vista):
- "¿Cuánto vendimos hoy?" / "¿Cómo van las ventas?" / "Dame el reporte del día"
  → USA consultarReportesGenerales({periodo: 'hoy', tipoConsulta: 'resumen'})
  
- "¿Cuál es el ticket promedio?" / "¿Cuántas transacciones hemos hecho?"
  → USA consultarReportesGenerales({tipoConsulta: 'ventas'})
  
- "¿Qué productos vendemos más?" / "¿Cuáles son los top productos?"
  → USA consultarReportesGenerales({tipoConsulta: 'productos'})
  
- "¿Quién es el mejor cajero?" / "¿Quién vendió más hoy?"
  → USA obtenerMejorCajero({periodo: 'hoy'})
  
- "¿Cómo van los cajeros?" / "Dame la comparativa de vendedores"
  → USA consultarReportesCaja({tipoConsulta: 'comparativa_cajeros'})
  
- "¿Cuáles fueron las mejores sesiones?" / "¿Cuál fue el mejor turno?"
  → USA obtenerTopSesiones({periodo: 'semana'})
  
- "¿Cuántas cajas están activas?" / "Dame el reporte de cajas"
  → USA consultarReportesCaja({tipoConsulta: 'resumen'})

- "Llévame a reportes" / "Quiero ver los reportes"
  → USA navegarAReportes({tipoReporte: 'general'}) o navegarModulo({modulo: 'reportes'})

🔴 PERÍODOS DISPONIBLES: hoy, semana, mes, año
🔴 TIPOS DE CONSULTA GENERALES: resumen, ventas, productos, categorias, tendencia
🔴 TIPOS DE CONSULTA CAJA: resumen, mejor_cajero, comparativa_cajeros, top_sesiones, eficiencia_hora

🧾 VENTAS Y FACTURAS - AUDITORÍA INTERNA (⭐⭐⭐ LO MÁS IMPORTANTE DEL POS):
Eres el AUDITOR INTERNO del negocio. Puedes consultar ventas de CUALQUIER FECHA, CUALQUIER EMPLEADO.

🔴 HERRAMIENTAS GLOBALES DE VENTAS (funcionan desde cualquier módulo):
- consultarVentasFecha({fecha, fechaFin?, incluirDetalle?}) - Ventas de una fecha específica
- ventasPorEmpleado({empleado, fecha?, periodo?}) - Ventas de un empleado específico
- buscarFactura({busqueda, tipo?, estado?}) - Buscar factura por número/cliente
- detalleFactura({identificador}) - Detalle completo de una factura
- resumenVentasHoy() - Resumen rápido del día actual
- navegarAFacturas({facturaId?, busqueda?}) - Ir al módulo de facturas

🔴 CUANDO EL USUARIO PREGUNTE (desde cualquier vista):

📅 VENTAS POR FECHA:
- "¿Cómo fueron las ventas ayer?" / "¿Cuánto se vendió ayer?"
  → USA consultarVentasFecha({fecha: 'ayer'})
  
- "¿Ventas del 13 de agosto?" / "¿Cómo estuvo el 15?"
  → USA consultarVentasFecha({fecha: '2024-08-13'})
  
- "¿Cómo estuvo el viernes?" / "¿Ventas del lunes?"
  → USA consultarVentasFecha({fecha: 'viernes'}) - Calcula la fecha automáticamente
  
- "¿Cuánto vendimos la semana pasada?" / "¿Ventas de esta semana?"
  → USA consultarReportesGenerales({periodo: 'semana', tipoConsulta: 'ventas'})

👤 VENTAS POR EMPLEADO:
- "¿Cuánto vendió María ayer?" / "¿Cómo le fue a Juan?"
  → USA ventasPorEmpleado({empleado: 'María', fecha: 'ayer'})
  
- "¿Ventas de Pedro hoy?" / "¿Qué ha vendido Carlos?"
  → USA ventasPorEmpleado({empleado: 'Pedro', fecha: 'hoy'})
  
- "¿Cuánto lleva vendiendo Lucía este mes?"
  → USA ventasPorEmpleado({empleado: 'Lucía', periodo: 'mes'})

🔍 BUSCAR FACTURAS:
- "Busca la factura 1234" / "¿Existe la factura FV-5678?"
  → USA buscarFactura({busqueda: '1234'})
  
- "Facturas de Don Carlos" / "¿Hay facturas del cliente X?"
  → USA buscarFactura({busqueda: 'Don Carlos'})
  
- "Dame el detalle de la factura 1234" / "¿Qué tiene esa factura?"
  → USA detalleFactura({identificador: '1234'})

📊 RESUMEN RÁPIDO:
- "¿Cómo vamos hoy?" / "¿Qué tal las ventas?" / "¿Cómo va el día?"
  → USA resumenVentasHoy()

🔴 FECHAS QUE PUEDES USAR:
- "hoy" → Fecha actual
- "ayer" → Día anterior
- "anteayer" → Hace 2 días
- "lunes", "martes", etc. → El último día de la semana mencionado
- "2024-08-13" → Fecha específica en formato ISO

🔴 EJEMPLO DE RESPUESTAS INTELIGENTES:
Usuario: "¿Cómo fueron las ventas ayer?"
→ "Ayer vendimos $1.850.000 en 23 facturas. El ticket promedio fue de $80.435. María fue la mejor vendedora con $620.000."

Usuario: "¿Cuánto vendió Juan el viernes?"
→ "Juan vendió $345.000 el viernes pasado en 8 facturas. Buen rendimiento, su ticket promedio fue de $43.125."

Usuario: "¿Ventas del 15 de agosto?"
→ "El 15 de agosto vendimos $2.100.000. Fue un buen día con 31 facturas."

Usuario: "Busca la factura del cliente Rodríguez"
→ "Encontré 3 facturas de Rodríguez: FV-1234 ($85.000), FV-1189 ($120.000), FV-1156 ($45.000). ¿Cuál necesitas?"

⚠️ REGLA CLAVE: Sé un auditor profesional pero amigable. Da datos precisos e interpreta:
- Si las ventas fueron altas → felicita y motiva
- Si fueron bajas → sugiere revisar o animar al equipo
- Si un empleado vendió mucho → destácalo
- Si no hay ventas de un día → confirma que fue así y pregunta si quiere otro día

REGLA IMPORTANTE: Para preguntas sobre ganancias, inventario, gastos, ventas:
1. USA consultarDatosNegocio PRIMERO
2. Si te dice que navegó, espera un momento y vuelve a consultar
3. Luego responde con los datos reales

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
                  description: 'SIEMPRE usa esta herramienta cuando el usuario diga "llévame a", "ir a", "abre", "muéstrame el módulo de", "quiero ver". También úsala para preguntas sobre ganancias, inventario, gastos, sedes, traslados, control de cajas - navega primero, luego consulta. Módulos disponibles: dashboard, pos, productos, clientes, facturas, devoluciones, reportes (reportes generales y de caja), configuracion, proveedores, categorias, stock, inventario_inteligente, sedes, cash-admin (control de cajas), users-management (usuarios y roles), expenses (gastos operativos).',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      modulo: { 
                        type: 'STRING', 
                        description: 'El módulo al que navegar. Usa: dashboard, pos, productos, clientes, facturas, devoluciones, reportes (o reports), configuracion, proveedores, categorias, stock, inventario_inteligente, sedes, cash-admin (control de cajas), users-management (usuarios y roles), expenses (gastos operativos, egresos)',
                        enum: ['dashboard', 'pos', 'productos', 'clientes', 'facturas', 'devoluciones', 'reportes', 'reports', 'configuracion', 'proveedores', 'categorias', 'stock', 'inventario_inteligente', 'sedes', 'traslados', 'cash-admin', 'users-management', 'expenses']
                      },
                      filtro: { 
                        type: 'STRING', 
                        description: 'Filtro opcional: inactivos, stock_bajo para productos. 7_dias, 30_dias, hoy para dashboard. traslados para ir a la pestaña de traslados en sedes.' 
                      }
                    },
                    required: ['modulo']
                  }
                },
                {
                  name: 'consultarDatosNegocio',
                  description: 'Consulta datos financieros del negocio: ganancias, inventario, ventas, gastos. USA ESTO cuando pregunten: ¿cuál es mi ganancia?, ¿cuánto tengo invertido?, ¿cuántos gastos tenemos?. Esta función navega al módulo correcto y obtiene los datos actualizados.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      tipo: { 
                        type: 'STRING', 
                        description: 'Qué tipo de dato consultar',
                        enum: ['ganancias', 'inventario', 'ventas', 'gastos', 'alertas', 'resumen_completo']
                      }
                    },
                    required: ['tipo']
                  }
                },
                {
                  name: 'obtenerEstadisticas',
                  description: 'Estadísticas: productos, ventas hoy, stock bajo',
                  parameters: { type: 'OBJECT', properties: {} }
                },
                {
                  name: 'obtenerContexto',
                  description: 'IMPORTANTE: Usa esta herramienta cuando el usuario pregunte sobre la pantalla actual, datos visibles, estado de caja, ventas del día, productos top, alertas de stock, o cualquier cosa que esté VIENDO en este momento. Devuelve toda la información visible en pantalla.',
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
                        description: 'Acción a ejecutar: sendEmail, sendWhatsApp, downloadPDF, printInvoice, buscarProducto, limpiarBusqueda, filtrarPorEstado, abrirCrearProducto, seleccionarProducto, editarProductoSeleccionado',
                        enum: ['sendEmail', 'sendWhatsApp', 'downloadPDF', 'printInvoice', 'buscarProducto', 'limpiarBusqueda', 'filtrarPorEstado', 'abrirCrearProducto', 'seleccionarProducto', 'editarProductoSeleccionado']
                      },
                      params: {
                        type: 'OBJECT',
                        description: 'Parámetros adicionales. Para buscarProducto: {texto: "..."}, filtrarPorEstado: {estado: "activos|inactivos|stock_bajo"}, seleccionarProducto: {nombre: "..."}'
                      }
                    },
                    required: ['accion']
                  }
                },
                {
                  name: 'buscarProductoEnVivo',
                  description: 'Busca productos mientras el usuario habla. Escribe en el buscador en tiempo real. Usa cuando digan: "busca", "encuentra", "muéstrame el producto", "dónde está".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      texto: { 
                        type: 'STRING', 
                        description: 'Texto a buscar (nombre, SKU o código de barras)'
                      }
                    },
                    required: ['texto']
                  }
                },
                {
                  name: 'buscarProveedorDeProducto',
                  description: 'Busca qué proveedor tiene asignado un producto específico. Usa cuando el usuario quiera saber el proveedor de un producto, o quiera crear orden de compra pero no sepa el proveedor. Retorna el producto encontrado y su proveedor asignado.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nombreProducto: { 
                        type: 'STRING', 
                        description: 'Nombre del producto para buscar su proveedor'
                      }
                    },
                    required: ['nombreProducto']
                  }
                },
                {
                  name: 'editarProductoPorVoz',
                  description: 'Edita un campo específico de un producto. Si solo hay 1 producto filtrado en pantalla, NO necesitas nombreProducto. Si hay varios, especifica nombreProducto. Usa cuando digan: "cambia el stock a Y", "actualiza el precio a X", "ponle 20 unidades".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nombreProducto: { 
                        type: 'STRING', 
                        description: 'Nombre del producto (OPCIONAL si solo hay 1 producto filtrado en pantalla)'
                      },
                      campo: { 
                        type: 'STRING', 
                        description: 'Campo a modificar',
                        enum: ['stock', 'precio', 'costo', 'nombre', 'descripcion', 'sku']
                      },
                      nuevoValor: { 
                        type: 'STRING', 
                        description: 'Nuevo valor para el campo'
                      }
                    },
                    required: ['campo', 'nuevoValor']
                  }
                },
                {
                  name: 'crearProductoConversacional',
                  description: 'Inicia o continúa la creación de un producto paso a paso. La IA guía al usuario preguntando nombre, precio, categoría, stock, etc. Detecta automáticamente si es tienda de moda o general.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      accion: { 
                        type: 'STRING', 
                        description: 'iniciar=abre el modal, asignar=asigna un valor a un campo, confirmar=guarda el producto',
                        enum: ['iniciar', 'asignar', 'confirmar', 'cancelar']
                      },
                      campo: { 
                        type: 'STRING', 
                        description: 'Campo del producto a asignar',
                        enum: ['nombre', 'precio', 'costo', 'stock', 'categoria', 'descripcion', 'sku', 'sede']
                      },
                      valor: { 
                        type: 'STRING', 
                        description: 'Valor a asignar al campo'
                      }
                    },
                    required: ['accion']
                  }
                },
                // 📂 CATEGORÍAS
                {
                  name: 'buscarCategoriaEnVivo',
                  description: 'Busca categorías mientras el usuario habla. Escribe en el buscador en tiempo real. Solo funciona en el módulo de categorías.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      texto: { 
                        type: 'STRING', 
                        description: 'Texto a buscar (nombre de categoría)'
                      }
                    },
                    required: ['texto']
                  }
                },
                {
                  name: 'verProductosCategoria',
                  description: 'Muestra los productos de una categoría específica. Abre el modal con la lista de productos.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nombre: { 
                        type: 'STRING', 
                        description: 'Nombre o parte del nombre de la categoría'
                      }
                    },
                    required: ['nombre']
                  }
                },
                {
                  name: 'crearCategoria',
                  description: 'Abre el modal para crear una nueva categoría. El usuario podrá escribir el nombre en el formulario.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'crearCategoriaRapida',
                  description: 'Crea una categoría rápidamente sin navegar al módulo de categorías. Útil cuando estás creando un producto y la categoría no existe.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nombre: { 
                        type: 'STRING', 
                        description: 'Nombre de la nueva categoría'
                      }
                    },
                    required: ['nombre']
                  }
                },
                // 📦 INVENTARIO
                {
                  name: 'cambiarTabInventario',
                  description: 'Cambia entre las pestañas del inventario: Stock Actual, Movimientos, Alertas. Solo funciona en el módulo de inventario.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      tab: { 
                        type: 'STRING', 
                        description: 'Nombre de la pestaña: stock, movimientos, alertas'
                      }
                    },
                    required: ['tab']
                  }
                },
                {
                  name: 'buscarInventario',
                  description: 'Busca productos en el inventario mientras el usuario habla. Solo funciona en el módulo de inventario.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      texto: { 
                        type: 'STRING', 
                        description: 'Texto a buscar (nombre de producto)'
                      }
                    },
                    required: ['texto']
                  }
                },
                {
                  name: 'filtrarInventarioPorStock',
                  description: 'Filtra el inventario por nivel de stock: bajo, normal, alto. Solo funciona en el módulo de inventario.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nivel: { 
                        type: 'STRING', 
                        description: 'Nivel de stock a filtrar: bajo, normal, alto, todos'
                      }
                    },
                    required: ['nivel']
                  }
                },
                {
                  name: 'verAlertasInventario',
                  description: 'Muestra la lista de productos con stock bajo. Cambia a la pestaña de alertas automáticamente.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                // 📊 INVENTARIO INTELIGENTE
                {
                  name: 'cambiarSeccionInventarioInteligente',
                  description: 'Cambia entre las secciones de Inventario Inteligente: Vista General, Productos, Movimientos, Clientes, Proveedores, Alertas, Predicciones.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      seccion: { 
                        type: 'STRING', 
                        description: 'Nombre de la seccion: general, productos, movimientos, clientes, proveedores, alertas, predicciones'
                      }
                    },
                    required: ['seccion']
                  }
                },
                {
                  name: 'buscarProductoInventarioInteligente',
                  description: 'Busca productos en la tabla de Inventario Inteligente.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      texto: { 
                        type: 'STRING', 
                        description: 'Texto a buscar (nombre de producto)'
                      }
                    },
                    required: ['texto']
                  }
                },
                {
                  name: 'cambiarPeriodoInventarioInteligente',
                  description: 'Cambia el periodo de analisis en Inventario Inteligente: hoy, semana, mes, año.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      periodo: { 
                        type: 'STRING', 
                        description: 'Periodo: hoy, semana, mes, año'
                      }
                    },
                    required: ['periodo']
                  }
                },
                {
                  name: 'verAlertasInventarioInteligente',
                  description: 'Va a la seccion de alertas en Inventario Inteligente.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'verPrediccionesInventarioInteligente',
                  description: 'Va a la seccion de predicciones en Inventario Inteligente.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'buscarClienteInventarioInteligente',
                  description: 'Muestra la seccion de clientes en Inventario Inteligente. Solo usar si YA ESTAMOS en Inventario Inteligente.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      texto: { type: 'STRING', description: 'Texto opcional para buscar cliente' }
                    },
                    required: []
                  }
                },
                {
                  name: 'buscarProveedorInventarioInteligente',
                  description: 'Muestra la seccion de proveedores en Inventario Inteligente. Solo usar si YA ESTAMOS en Inventario Inteligente.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      texto: { type: 'STRING', description: 'Texto opcional para buscar proveedor' }
                    },
                    required: []
                  }
                },
                // ========== FUNCIONES DE CLIENTES ==========
                {
                  name: 'buscarClienteModulo',
                  description: 'Busca un cliente por nombre en el módulo de Clientes. Usa esto cuando el usuario diga "busca cliente X", "encuentra a cliente Y".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nombre: { type: 'STRING', description: 'Nombre o parte del nombre del cliente a buscar' }
                    },
                    required: ['nombre']
                  }
                },
                {
                  name: 'seleccionarCliente',
                  description: 'Selecciona un cliente de la lista para ver sus detalles. Usa cuando digan "selecciona cliente X", "muéstrame los datos de X".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nombre: { type: 'STRING', description: 'Nombre del cliente a seleccionar' }
                    },
                    required: ['nombre']
                  }
                },
                {
                  name: 'verHistorialCliente',
                  description: 'Muestra el historial de compras del cliente seleccionado. Usa cuando digan "ver historial", "qué ha comprado", "compras de este cliente".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'editarClienteActual',
                  description: 'Abre el editor para modificar el cliente seleccionado. Usa cuando digan "editar cliente", "modificar datos del cliente".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'crearNuevoClienteVoz',
                  description: 'Abre el formulario para crear un nuevo CLIENTE (persona que nos compra productos). NO confundir con proveedor. Usa cuando digan "crear cliente", "nuevo cliente", "agregar cliente", "registrar cliente".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'filtrarClientesPorEstado',
                  description: 'Filtra la lista de clientes por estado: activos, inactivos o todos.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      estado: { 
                        type: 'STRING', 
                        description: 'Estado para filtrar: activos, inactivos, todos',
                        enum: ['activos', 'inactivos', 'todos']
                      }
                    },
                    required: ['estado']
                  }
                },
                {
                  name: 'llenarCampoCliente',
                  description: 'Llena un campo del formulario de cliente. IMPORTANTE: Debes usar esta función para cada campo por separado. Campos válidos: nombre, documento/cedula/cc, email, telefono, direccion, ciudad.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      campo: { 
                        type: 'STRING', 
                        description: 'Nombre del campo a llenar: nombre, documento, cedula, cc, email, telefono, direccion, ciudad'
                      },
                      valor: {
                        type: 'STRING',
                        description: 'Valor a poner en el campo'
                      }
                    },
                    required: ['campo', 'valor']
                  }
                },
                {
                  name: 'guardarCliente',
                  description: 'Guarda el cliente con los datos del formulario. Solo usar después de haber llenado TODOS los campos obligatorios: nombre, documento, email, telefono.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'cerrarModalCliente',
                  description: 'Cierra el formulario de cliente sin guardar.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                // === CREDITIENDA - GESTIÓN DE CRÉDITOS ===
                {
                  name: 'crearNuevoCredito',
                  description: 'Abre el formulario para habilitar crédito a un cliente. Usa cuando digan "habilitar crédito", "dar crédito", "nuevo crédito".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'buscarClientePorDocumento',
                  description: 'Busca un cliente por su cédula/documento. Si existe, auto-llena todos sus datos. MUY IMPORTANTE: usar esto PRIMERO al crear crédito.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      documento: { 
                        type: 'STRING', 
                        description: 'Número de cédula o documento del cliente'
                      }
                    },
                    required: ['documento']
                  }
                },
                {
                  name: 'llenarCampoCredito',
                  description: 'Llena un campo del formulario de crédito. Campos: nombre, documento, email, telefono, direccion, ciudad, cupo/limite.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      campo: { 
                        type: 'STRING', 
                        description: 'Campo a llenar: nombre, documento, email, telefono, direccion, ciudad, cupo, limite'
                      },
                      valor: {
                        type: 'STRING',
                        description: 'Valor a poner en el campo'
                      }
                    },
                    required: ['campo', 'valor']
                  }
                },
                {
                  name: 'guardarCredito',
                  description: 'Guarda el crédito habilitado para el cliente. Requiere: documento y nombre como mínimo.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'seleccionarClienteCredito',
                  description: 'Selecciona un cliente en CrediTienda para ver sus detalles de crédito o registrar abono.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nombre: { 
                        type: 'STRING', 
                        description: 'Nombre del cliente a seleccionar'
                      }
                    },
                    required: ['nombre']
                  }
                },
                {
                  name: 'registrarAbono',
                  description: 'Registra un abono/pago a la deuda del cliente seleccionado.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      monto: { 
                        type: 'NUMBER', 
                        description: 'Monto del abono a registrar'
                      },
                      metodo: {
                        type: 'STRING',
                        description: 'Método de pago: cash, transfer, card',
                        enum: ['cash', 'transfer', 'card']
                      }
                    },
                    required: ['monto']
                  }
                },
                {
                  name: 'confirmarAbono',
                  description: 'Confirma y procesa el abono que está pendiente en el modal.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'cerrarModalCredito',
                  description: 'Cierra el modal de crédito o abono sin guardar.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                // === PROVEEDORES Y COMPRAS ===
                {
                  name: 'cambiarPestanaCompras',
                  description: 'Cambia entre las pestañas de Proveedores y Órdenes de Compra.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      pestana: { 
                        type: 'STRING', 
                        description: 'Pestaña: proveedores, ordenes',
                        enum: ['proveedores', 'ordenes']
                      }
                    },
                    required: ['pestana']
                  }
                },
                {
                  name: 'crearNuevoProveedor',
                  description: 'Abre el formulario para crear un nuevo PROVEEDOR (empresa que nos vende productos). NO confundir con cliente. Usa cuando digan "crear proveedor", "nuevo proveedor", "agregar proveedor", "registrar proveedor", "añadir distribuidor".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'llenarCampoProveedor',
                  description: 'Llena un campo del formulario de proveedor. Campos: nombre, documento/nit, telefono, email, ciudad, direccion, contacto, notas.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      campo: { 
                        type: 'STRING', 
                        description: 'Campo: nombre, documento, nit, telefono, email, ciudad, direccion, contacto'
                      },
                      valor: {
                        type: 'STRING',
                        description: 'Valor a poner en el campo'
                      }
                    },
                    required: ['campo', 'valor']
                  }
                },
                {
                  name: 'guardarProveedor',
                  description: 'Guarda el proveedor con los datos del formulario. Requiere al menos el nombre.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'verificarFormularioProveedor',
                  description: 'Verifica qué datos están actualmente visibles en el formulario de proveedor. SIEMPRE usa esto antes de guardar para confirmar que todos los campos se llenaron correctamente.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'buscarProveedor',
                  description: 'Busca proveedores por nombre o documento. Retorna lista de proveedores encontrados con su ID, nombre, documento, teléfono y estado activo.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      texto: { type: 'STRING', description: 'Nombre o documento del proveedor a buscar' }
                    },
                    required: ['texto']
                  }
                },
                {
                  name: 'listarProveedores',
                  description: 'Lista todos los proveedores disponibles en el sistema. Usa esto para ver cuántos proveedores hay y cuáles están activos.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'cerrarFormularioProveedor',
                  description: 'Cierra el formulario de proveedor sin guardar.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'seleccionarProveedor',
                  description: 'Selecciona un proveedor para ver sus detalles. Usa cuando el usuario diga "selecciona proveedor X", "muéstrame el proveedor X", "abre el proveedor X".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nombre: { type: 'STRING', description: 'Nombre o documento del proveedor a seleccionar' }
                    },
                    required: ['nombre']
                  }
                },
                {
                  name: 'crearNuevaOrdenCompra',
                  description: 'Abre el formulario para crear una orden de compra. Si el usuario menciona un proveedor en su petición, pásalo como nombreProveedor para seleccionarlo automáticamente. Ejemplo: "orden de compra para Distribuidora Norte" → nombreProveedor: "Distribuidora Norte".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nombreProveedor: { 
                        type: 'STRING', 
                        description: 'Nombre del proveedor mencionado por el usuario (opcional). Si lo pasas, se selecciona automáticamente.' 
                      }
                    },
                    required: []
                  }
                },
                {
                  name: 'seleccionarProveedorOrden',
                  description: 'Selecciona el proveedor para la orden de compra.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nombre: { type: 'STRING', description: 'Nombre del proveedor' }
                    },
                    required: ['nombre']
                  }
                },
                {
                  name: 'seleccionarBodegaOrden',
                  description: 'Selecciona la bodega/sede destino para la orden. IMPORTANTE: Usar solo si hay múltiples bodegas.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nombre: { type: 'STRING', description: 'Nombre de la bodega/sede' }
                    },
                    required: ['nombre']
                  }
                },
                {
                  name: 'agregarProductoOrden',
                  description: 'Agrega un producto a la orden de compra. El costo se toma automáticamente del producto, NO preguntes el costo al usuario a menos que él lo mencione específicamente.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      nombre: { type: 'STRING', description: 'Nombre o SKU del producto' },
                      cantidad: { type: 'NUMBER', description: 'Cantidad a pedir (SOLO pregunta esto)' },
                      costo: { type: 'NUMBER', description: 'Costo unitario OPCIONAL - solo si el usuario lo menciona explícitamente. Si no lo menciona, se usa el costo del producto.' }
                    },
                    required: ['nombre', 'cantidad']
                  }
                },
                {
                  name: 'buscarProductoOrden',
                  description: 'Busca productos para agregar a la orden de compra.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      texto: { type: 'STRING', description: 'Nombre o SKU a buscar' }
                    },
                    required: ['texto']
                  }
                },
                {
                  name: 'abrirSelectorProductos',
                  description: 'Abre el modal visual de selección de productos dentro de la orden de compra. El usuario podrá ver y seleccionar productos visualmente. Úsalo cuando el usuario pida "ver productos", "mostrar productos" o "qué productos tengo" mientras está en una orden de compra.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'llenarCampoOrden',
                  description: 'Llena un campo de la orden. Campos: fecha_orden, fecha_esperada, referencia, notas.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      campo: { type: 'STRING', description: 'Campo: fecha_orden, fecha_esperada, referencia, notas' },
                      valor: { type: 'STRING', description: 'Valor del campo' }
                    },
                    required: ['campo', 'valor']
                  }
                },
                {
                  name: 'guardarOrdenCompra',
                  description: 'Guarda la orden de compra. Puede guardarse como borrador o como pendiente.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      comoBorrador: { type: 'BOOLEAN', description: 'Si es true, guarda como borrador' }
                    },
                    required: []
                  }
                },
                {
                  name: 'cerrarFormularioOrden',
                  description: 'Cierra el formulario de orden sin guardar.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'seleccionarOrdenCompra',
                  description: 'Selecciona una orden de compra existente para ver sus detalles.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      numero: { type: 'STRING', description: 'Número de la orden' }
                    },
                    required: ['numero']
                  }
                },
                {
                  name: 'filtrarOrdenesCompra',
                  description: 'Filtra las órdenes de compra por estado.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      estado: { 
                        type: 'STRING', 
                        description: 'Estado: todas, pendientes, parciales, recibidas',
                        enum: ['todas', 'pendientes', 'parciales', 'recibidas']
                      }
                    },
                    required: ['estado']
                  }
                },
                // === ACCIONES PARA ORDEN SELECCIONADA ===
                {
                  name: 'descargarOrdenPDF',
                  description: 'Descarga el PDF de la orden de compra seleccionada. Requiere tener una orden seleccionada.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'enviarOrdenEmail',
                  description: 'Envía la orden de compra seleccionada por email al proveedor.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'enviarOrdenWhatsApp',
                  description: 'Envía la orden de compra seleccionada por WhatsApp al proveedor.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'abrirModalIngresarStock',
                  description: 'Abre el modal para ingresar los productos de la orden al inventario/stock. El usuario puede marcar qué productos recibió y en qué cantidad.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'confirmarIngresoStock',
                  description: 'Confirma el ingreso de productos al stock después de que el usuario marcó las cantidades en el modal.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'marcarCantidadRecibida',
                  description: 'Marca la cantidad recibida de un producto específico en el modal de ingreso a stock.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      producto: { type: 'STRING', description: 'Nombre del producto a marcar' },
                      cantidad: { type: 'NUMBER', description: 'Cantidad recibida (opcional si usas recibirTodo)' },
                      recibirTodo: { type: 'BOOLEAN', description: 'Si es true, marca como recibida toda la cantidad pendiente' }
                    },
                    required: ['producto']
                  }
                },
                {
                  name: 'recibirTodosProductos',
                  description: 'Marca TODOS los productos del modal como recibidos completamente. Útil cuando el usuario dice "llegó todo", "recibí todo", "confirmar todo".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'marcarOrdenPagada',
                  description: 'Marca la orden como pagada/recibida. Abre el modal de ingreso a stock.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                // === USUARIOS Y ROLES ===
                {
                  name: 'listarUsuarios',
                  description: 'Lista todos los usuarios del sistema con su rol y estado.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'listarRoles',
                  description: 'Lista todos los roles configurados con sus permisos.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'abrirCrearUsuario',
                  description: 'Abre el modal para crear un nuevo usuario. El usuario llenará el formulario.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'llenarCampoUsuario',
                  description: 'Llena un campo del formulario de usuario. IMPORTANTE: Usa esta función para cada campo por separado. Campos válidos: name (nombre completo), email, password, role_id. NUNCA uses llenarCampoCliente para usuarios.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      campo: { 
                        type: 'STRING', 
                        description: 'Campo a llenar: name, email, password, role_id (1=Admin, 2=Vendedor, etc.)'
                      },
                      valor: {
                        type: 'STRING',
                        description: 'Valor a poner en el campo'
                      }
                    },
                    required: ['campo', 'valor']
                  }
                },
                {
                  name: 'guardarUsuario',
                  description: 'Guarda el usuario con los datos del formulario. Solo usar después de haber llenado TODOS los campos obligatorios: name, email, password Y rol (obligatorio).',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'editarUsuario',
                  description: 'Abre el modal para editar un usuario existente. Busca por nombre o email.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      busqueda: { 
                        type: 'STRING', 
                        description: 'Nombre o email del usuario a editar'
                      }
                    },
                    required: ['busqueda']
                  }
                },
                {
                  name: 'abrirCrearRol',
                  description: 'Abre el modal para crear un nuevo rol con permisos.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'buscarUsuario',
                  description: 'Busca usuarios por nombre, email o cédula.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      texto: { type: 'STRING', description: 'Texto a buscar' }
                    },
                    required: ['texto']
                  }
                },
                {
                  name: 'verPermisosDisponibles',
                  description: 'Muestra todos los módulos/permisos que se pueden asignar a los roles.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'cambiarPestanaUsuarios',
                  description: 'Cambia entre la pestaña de Usuarios y Roles.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      pestana: { type: 'STRING', description: 'usuarios o roles', enum: ['usuarios', 'roles'] }
                    },
                    required: ['pestana']
                  }
                },
                // ========================================
                // 💼 CONTROL DE CAJAS - Herramientas GLOBALES
                // ========================================
                {
                  name: 'consultarRendimientoEmpleado',
                  description: 'Consulta el rendimiento de un empleado específico. FUNCIONA DESDE CUALQUIER MÓDULO. Usa cuando pregunten: "¿cómo le va a María?", "¿cómo está Juan?", "rendimiento de Pedro".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      busqueda: { 
                        type: 'STRING', 
                        description: 'Nombre o email del empleado a consultar'
                      }
                    },
                    required: ['busqueda']
                  }
                },
                {
                  name: 'obtenerResumenCajas',
                  description: 'Obtiene resumen de Control de Cajas: sesiones activas, empleados trabajando, total en cajas, ventas del día. FUNCIONA DESDE CUALQUIER MÓDULO. Usa para: "¿quién está en caja?", "¿cuántas cajas abiertas?", "resumen de cajas".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'obtenerAlertasEmpleados',
                  description: 'Obtiene alertas de empleados: sin ventas, sesiones muy largas. FUNCIONA DESDE CUALQUIER MÓDULO. Usa para: "¿hay alertas?", "¿todo bien con los empleados?", "¿algo raro con las cajas?".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'verDetalleSesionCaja',
                  description: 'Muestra detalles de una sesión de caja específica. Funciona cuando estás en Control de Cajas.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      idSesion: { type: 'STRING', description: 'ID de la sesión' },
                      busqueda: { type: 'STRING', description: 'Nombre del usuario para buscar su sesión' }
                    },
                    required: []
                  }
                },
                {
                  name: 'filtrarSesionesCaja',
                  description: 'Filtra las sesiones de caja por estado. Solo funciona en el módulo Control de Cajas.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      estado: { 
                        type: 'STRING', 
                        description: 'Estado a filtrar',
                        enum: ['activas', 'cerradas', 'todas']
                      }
                    },
                    required: ['estado']
                  }
                },
                {
                  name: 'obtenerMiCajaVsEmpleados',
                  description: 'Diferencia entre TU caja actual y las cajas de tus empleados. FUNCIONA DESDE CUALQUIER MÓDULO. Usa para: "¿cómo va mi caja?", "¿cuánto vendí yo?", "mi caja vs empleados", "¿cómo van mis vendedores comparado conmigo?".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                // ========================================
                // 💸 GASTOS OPERATIVOS - Herramientas GLOBALES
                // ========================================
                {
                  name: 'registrarGastoVoz',
                  description: 'Registra un gasto operativo por voz. FUNCIONA DESDE CUALQUIER MÓDULO. Usa cuando digan: "registra un gasto de", "compré X", "pagué X", "me tocó gastar en", "le presté a", "pagamos la luz". El sistema te preguntará lo que falte (categoría, monto, si sale de caja o ganancias generales).',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      descripcion: { 
                        type: 'STRING', 
                        description: 'Descripción del gasto: qué se compró o pagó'
                      },
                      monto: { 
                        type: 'NUMBER', 
                        description: 'Monto del gasto (opcional, si no se dice, se preguntará)'
                      },
                      categoria: { 
                        type: 'STRING', 
                        description: 'Categoría sugerida del gasto. Categorías: servicios_publicos, nomina, mantenimiento, suministros, arriendo, transporte, otros',
                        enum: ['servicios_publicos', 'nomina', 'mantenimiento', 'suministros', 'arriendo', 'transporte', 'otros']
                      },
                      fuente: { 
                        type: 'STRING', 
                        description: 'De dónde sale el dinero: caja (descuenta de caja actual) o general (gasto general sin descontar de caja)',
                        enum: ['caja', 'general']
                      },
                      proveedor: { 
                        type: 'STRING', 
                        description: 'Nombre del proveedor o a quién se le pagó (opcional)'
                      },
                      metodo_pago: {
                        type: 'STRING',
                        description: 'Método de pago: efectivo, transferencia, tarjeta',
                        enum: ['efectivo', 'transferencia', 'tarjeta']
                      }
                    },
                    required: ['descripcion']
                  }
                },
                {
                  name: 'consultarGastos',
                  description: 'Consulta información de gastos operativos: total del mes, por categoría, últimos gastos. FUNCIONA DESDE CUALQUIER MÓDULO. Usa para: "¿cuánto hemos gastado?", "¿gastos del mes?", "¿en qué hemos gastado más?".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      consulta: { 
                        type: 'STRING', 
                        description: 'Tipo de consulta',
                        enum: ['total_mes', 'por_categoria', 'ultimos', 'resumen']
                      },
                      periodo: {
                        type: 'STRING',
                        description: 'Período: hoy, semana, mes',
                        enum: ['hoy', 'semana', 'mes']
                      }
                    },
                    required: ['consulta']
                  }
                },
                {
                  name: 'verCategoriasGastos',
                  description: 'Muestra las categorías de gastos disponibles. Útil antes de registrar un gasto para saber qué categorías hay.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                // ========================================
                // 📊 REPORTES - Herramientas GLOBALES
                // ========================================
                {
                  name: 'consultarReportesGenerales',
                  description: 'Consulta reportes generales del negocio: ventas totales, transacciones, ticket promedio, margen, top productos, ventas por categoría. FUNCIONA DESDE CUALQUIER MÓDULO. Usa para: "¿cuánto vendimos hoy?", "¿cómo van las ventas?", "dame el reporte del día", "¿cuál es el ticket promedio?", "¿qué productos vendemos más?".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      periodo: { 
                        type: 'STRING', 
                        description: 'Período del reporte: hoy, semana, mes, año',
                        enum: ['hoy', 'semana', 'mes', 'año']
                      },
                      tipoConsulta: {
                        type: 'STRING',
                        description: 'Tipo de información: resumen (KPIs principales), ventas (total ventas), productos (top productos), categorias (ventas por categoría), tendencia (ventas diarias)',
                        enum: ['resumen', 'ventas', 'productos', 'categorias', 'tendencia']
                      }
                    },
                    required: []
                  }
                },
                {
                  name: 'consultarReportesCaja',
                  description: 'Consulta reportes de caja y rendimiento de cajeros. FUNCIONA DESDE CUALQUIER MÓDULO. Usa para: "¿quién es el mejor cajero?", "¿cuántas cajas hay activas?", "¿cómo van los cajeros?", "¿cuál es el promedio por hora?", "dame el reporte de cajas".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      periodo: { 
                        type: 'STRING', 
                        description: 'Período del reporte: hoy, semana, mes, año',
                        enum: ['hoy', 'semana', 'mes', 'año']
                      },
                      tipoConsulta: {
                        type: 'STRING',
                        description: 'Tipo de información: resumen (KPIs caja), mejor_cajero, comparativa_cajeros, top_sesiones, eficiencia_hora',
                        enum: ['resumen', 'mejor_cajero', 'comparativa_cajeros', 'top_sesiones', 'eficiencia_hora']
                      }
                    },
                    required: []
                  }
                },
                {
                  name: 'obtenerMejorCajero',
                  description: 'Obtiene información del mejor cajero del período. GLOBAL. Usa para: "¿quién vendió más?", "¿cuál es el mejor vendedor?", "¿quién es el mejor cajero hoy?".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      periodo: { 
                        type: 'STRING', 
                        description: 'Período: hoy, semana, mes',
                        enum: ['hoy', 'semana', 'mes']
                      }
                    },
                    required: []
                  }
                },
                {
                  name: 'obtenerTopSesiones',
                  description: 'Obtiene las mejores sesiones de caja del período. GLOBAL. Usa para: "¿cuáles fueron las mejores sesiones?", "¿cuál fue el mejor turno?", "mejores ventas por turno".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      periodo: { 
                        type: 'STRING', 
                        description: 'Período: hoy, semana, mes',
                        enum: ['hoy', 'semana', 'mes']
                      },
                      limite: {
                        type: 'NUMBER',
                        description: 'Cantidad de sesiones a mostrar (1-10)'
                      }
                    },
                    required: []
                  }
                },
                {
                  name: 'navegarAReportes',
                  description: 'Navega al módulo de reportes. Usa para: "llévame a reportes", "quiero ver los reportes", "abre reportes".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      tipoReporte: {
                        type: 'STRING',
                        description: 'Tipo de reporte a abrir: general o caja',
                        enum: ['general', 'caja']
                      }
                    },
                    required: []
                  }
                },
                // ========================================
                // 🧾 VENTAS/FACTURAS - Herramientas GLOBALES (⭐ CORE DEL POS)
                // ========================================
                {
                  name: 'consultarVentasFecha',
                  description: 'Consulta ventas de una FECHA ESPECÍFICA. ⭐ HERRAMIENTA PRINCIPAL para preguntas como: "¿cómo fueron las ventas ayer?", "¿ventas del 13 de agosto?", "¿cuánto vendimos el lunes?", "¿cómo estuvo el viernes?". FUNCIONA DESDE CUALQUIER MÓDULO.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      fecha: { 
                        type: 'STRING', 
                        description: 'Fecha a consultar. Usa: "ayer", "hoy", "anteayer", o fecha ISO "2024-08-13". Para días de la semana como "lunes" o "viernes", calcula la fecha correcta.'
                      },
                      fechaFin: {
                        type: 'STRING',
                        description: 'Fecha fin para rango (opcional). Formato ISO "2024-08-15"'
                      },
                      incluirDetalle: {
                        type: 'BOOLEAN',
                        description: 'Si incluir lista de facturas individuales (false por defecto para respuestas concisas)'
                      }
                    },
                    required: ['fecha']
                  }
                },
                {
                  name: 'ventasPorEmpleado',
                  description: 'Consulta ventas de un EMPLEADO ESPECÍFICO en una fecha. Para: "¿cuánto vendió María ayer?", "¿ventas de Juan hoy?", "¿cómo le fue a Pedro el lunes?". FUNCIONA DESDE CUALQUIER MÓDULO.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      empleado: { 
                        type: 'STRING', 
                        description: 'Nombre del empleado/vendedor a consultar'
                      },
                      fecha: { 
                        type: 'STRING', 
                        description: 'Fecha específica: "ayer", "hoy", "anteayer", o ISO "2024-08-13"'
                      },
                      periodo: {
                        type: 'STRING',
                        description: 'Período alternativo si no hay fecha específica: hoy, semana, mes',
                        enum: ['hoy', 'semana', 'mes']
                      }
                    },
                    required: ['empleado']
                  }
                },
                {
                  name: 'buscarFactura',
                  description: 'Busca una factura específica por número, cliente o referencia. Para: "busca la factura 1234", "facturas de Don Carlos", "¿hay factura del cliente X?". FUNCIONA DESDE CUALQUIER MÓDULO.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      busqueda: { 
                        type: 'STRING', 
                        description: 'Número de factura, nombre de cliente, o término de búsqueda'
                      },
                      tipo: {
                        type: 'STRING',
                        description: 'Tipo de documento: factura, cotizacion, todos',
                        enum: ['factura', 'cotizacion', 'todos']
                      },
                      estado: {
                        type: 'STRING',
                        description: 'Estado: pagada, pendiente, anulada, todos',
                        enum: ['pagada', 'pendiente', 'anulada', 'todos']
                      }
                    },
                    required: ['busqueda']
                  }
                },
                {
                  name: 'detalleFactura',
                  description: 'Obtiene el detalle completo de una factura específica: productos, totales, cliente, método de pago. Para: "dame el detalle de la factura 1234", "¿qué tiene la factura X?".',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      identificador: { 
                        type: 'STRING', 
                        description: 'Número de factura (FV-1234) o ID'
                      }
                    },
                    required: ['identificador']
                  }
                },
                {
                  name: 'resumenVentasHoy',
                  description: 'Resumen rápido de ventas del día actual. Para: "¿cómo vamos hoy?", "¿qué tal las ventas?", "dame el resumen de hoy". OPTIMIZADO para respuestas rápidas.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {},
                    required: []
                  }
                },
                {
                  name: 'navegarAFacturas',
                  description: 'Navega al módulo de facturas. Opcionalmente puede seleccionar una factura específica o aplicar filtro de búsqueda.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      facturaId: {
                        type: 'NUMBER',
                        description: 'ID de factura a seleccionar automáticamente'
                      },
                      busqueda: {
                        type: 'STRING',
                        description: 'Término de búsqueda a aplicar'
                      }
                    },
                    required: []
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
            
            // Iniciar timer de duración con verificación de límite
            durationInterval = setInterval(() => {
              callDuration.value++
              
              // 🔒 Verificar límite de tiempo y cortar automáticamente
              if (maxDurationSeconds.value > 0 && callDuration.value >= maxDurationSeconds.value) {
                console.log(`⏰ [LiveCall] Límite de ${maxDurationSeconds.value}s alcanzado - terminando llamada`)
                wasAutoTerminated.value = true
                endCall('limit_reached')
              }
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
            // Resetear timeout cuando la IA está hablando (hay actividad)
            resetInactivityTimeout()
            
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
  
  // 🔊 Función de resampleo mejorada con filtro sinc + Lanczos window
  // Esto evita el aliasing y produce audio más limpio que la interpolación lineal
  const resampleAudio = (inputData, inputSampleRate, outputSampleRate) => {
    if (inputSampleRate === outputSampleRate) {
      return inputData
    }
    
    const ratio = inputSampleRate / outputSampleRate
    const outputLength = Math.floor(inputData.length / ratio)
    const output = new Float32Array(outputLength)
    
    // Parámetros del filtro Lanczos (a=3 es buen balance calidad/velocidad)
    const a = 3  // Tamaño de la ventana Lanczos
    
    // Función sinc: sin(πx) / (πx)
    const sinc = (x) => {
      if (x === 0) return 1
      const px = Math.PI * x
      return Math.sin(px) / px
    }
    
    // Ventana Lanczos
    const lanczos = (x, a) => {
      if (x === 0) return 1
      if (Math.abs(x) >= a) return 0
      return sinc(x) * sinc(x / a)
    }
    
    for (let i = 0; i < outputLength; i++) {
      const srcIndex = i * ratio
      const srcIndexFloor = Math.floor(srcIndex)
      
      let sum = 0
      let weightSum = 0
      
      // Aplicar kernel Lanczos sobre vecinos
      for (let j = srcIndexFloor - a + 1; j <= srcIndexFloor + a; j++) {
        if (j >= 0 && j < inputData.length) {
          const weight = lanczos(srcIndex - j, a)
          sum += inputData[j] * weight
          weightSum += weight
        }
      }
      
      // Normalizar
      output[i] = weightSum > 0 ? sum / weightSum : 0
    }
    
    return output
  }
  
  const startAudioCapture = async () => {
    if (!mediaStream || !audioContext || !websocket) return
    
    try {
      // Crear nodo de fuente desde el micrófono
      const source = audioContext.createMediaStreamSource(mediaStream)
      
      // Calcular el tamaño del chunk basado en el sampleRate actual
      const nativeSampleRate = audioContext.sampleRate
      const needsResample = nativeSampleRate !== SEND_SAMPLE_RATE
      
      console.log(`🎤 [LiveCall] Audio captura iniciada. SampleRate nativo: ${nativeSampleRate}, Necesita resample: ${needsResample}`)
      
      // ScriptProcessor requiere buffer que sea potencia de 2 (256, 512, 1024, 2048, 4096, 8192, 16384)
      // Para 48kHz nativo y enviar a 16kHz, usamos 4096 que es un buen balance
      const bufferSize = needsResample ? 4096 : CHUNK_SIZE
      
      // Usar ScriptProcessor para capturar audio
      const processor = audioContext.createScriptProcessor(bufferSize, 1, 1)
      
      processor.onaudioprocess = (e) => {
        if (!isConnected.value || !websocket || websocket.readyState !== WebSocket.OPEN) {
          return
        }
        
        // Obtener datos del buffer de entrada
        let inputData = e.inputBuffer.getChannelData(0)
        
        // Resamplear a 16kHz si es necesario (para Firefox)
        if (needsResample) {
          inputData = resampleAudio(inputData, nativeSampleRate, SEND_SAMPLE_RATE)
        }
        
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
  const endCall = async (status = 'success') => {
    // Guardar duración antes de limpiar
    const finalDuration = callDuration.value
    
    cleanup()
    
    isConnected.value = false
    isConnecting.value = false
    isListening.value = false
    isSpeaking.value = false
    showVoiceSelector.value = false
    
    // Registrar uso de voz en el backend (si duró más de 1 segundo)
    if (finalDuration > 1) {
      try {
        await api.post('/ai/log-voice-usage', {
          duration_seconds: finalDuration,
          status: status
        })
        console.log(`📊 [LiveCall] Uso de voz registrado: ${finalDuration}s`)
      } catch (err) {
        console.warn('[LiveCall] No se pudo registrar uso de voz:', err)
      }
    }
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
    
    // 🔍 Log para debug: mostrar todas las funciones que se van a ejecutar
    console.log('🔧 [LiveCall] Procesando function calls:', functionCalls.map(fc => fc.name))
    
    for (const fc of functionCalls) {
      console.log(`🔧 [LiveCall] Ejecutando: ${fc.name}`, fc.args)
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
              'devoluciones': 'returns-management',
              'reportes': 'reports',
              'configuracion': 'settings',
              'proveedores': 'purchase-orders',
              'suppliers': 'purchase-orders',
              'compras': 'purchase-orders',
              'ordenes_compra': 'purchase-orders',
              'ordenes': 'purchase-orders',
              'purchase-orders': 'purchase-orders',
              'categorias': 'categories',
              'stock': 'stock',
              'dashboard': 'dashboard',
              'pos': 'pos',
              'inventario_inteligente': 'intelligent_inventory',
              'intelligent_inventory': 'intelligent_inventory',
              'sedes': 'warehouses',
              'warehouses': 'warehouses',
              'bodegas': 'warehouses',
              'tiendas': 'warehouses',
              'traslados': 'warehouses',
              'creditienda': 'accounts-receivable',
              'creditos': 'accounts-receivable',
              'cuentas_por_cobrar': 'accounts-receivable',
              'accounts-receivable': 'accounts-receivable',
              'cartera': 'accounts-receivable',
              'usuarios': 'users-management',
              'users': 'users-management',
              'roles': 'users-management',
              'empleados': 'users-management',
              'users-management': 'users-management',
              // Control de Cajas
              'cash-admin': 'cash-admin',
              'cajas': 'cash-admin',
              'control_cajas': 'cash-admin',
              'control_de_cajas': 'cash-admin',
              'sesiones': 'cash-admin',
              'sesiones_caja': 'cash-admin',
              'supervisar': 'cash-admin',
              'supervision': 'cash-admin',
              // Gastos Operativos
              'expenses': 'expenses',
              'gastos': 'expenses',
              'gastos_operativos': 'expenses',
              'egresos': 'expenses',
              'gastos operativos': 'expenses'
            }
            const moduloFinal = moduloMap[modulo] || modulo
            
            // Mapear filtros a valores que los módulos entienden
            const filtroMap = {
              'inactivos': 'inactive',
              'inactivo': 'inactive',
              'stock_bajo': 'low-stock',
              'sin_stock': 'low-stock',
              'activos': 'active',
              // Filtros de período para Dashboard
              '7_dias': '7 días',
              '7d': '7 días',
              '7 dias': '7 días',
              'semana': '7 días',
              'semanal': '7 días',
              '30_dias': '30 días',
              '30d': '30 días',
              '30 dias': '30 días',
              'mes': '30 días',
              'mensual': '30 días',
              '24h': '24 horas',
              'hoy': '24 horas',
              '24_horas': '24 horas',
              // Filtros para Sedes
              'traslados': 'transfers',
              'sedes': 'warehouses'
            }
            const filtroFinal = filtroNav ? (filtroMap[filtroNav] || filtroNav) : null
            
            // Verificar si ya estamos en ese módulo
            const uiContext = useUIContextStore()
            const moduloActual = uiContext.currentModule
            
            console.log(`🧭 [navegarModulo] Módulo actual: "${moduloActual}" → Destino: "${moduloFinal}"`)
            
            // 🔄 LÓGICA INTELIGENTE: Si pide clientes/proveedores y estamos en Inventario Inteligente
            // → Cambiar pestaña en lugar de navegar a otro módulo
            if (moduloActual === 'intelligent_inventory' && (modulo === 'clientes' || modulo === 'proveedores')) {
              const seccion = modulo === 'clientes' ? 'customers' : 'suppliers'
              const seccionNombre = modulo === 'clientes' ? 'Clientes' : 'Proveedores'
              
              // Ejecutar cambio de sección
              const cambioResult = await uiContext.executeAction('cambiarSeccionInventarioInteligente', { seccion })
              result = cambioResult || { 
                success: true, 
                message: `Cambiando a la pestaña de ${seccionNombre} en Inventario Inteligente` 
              }
              console.log(`🔄 [navegarModulo] Estamos en Inventario Inteligente, cambiando a pestaña ${seccionNombre}`)
              break
            }
            
            // 🔄 LÓGICA INTELIGENTE PARA SEDES: Si pide traslados y estamos en sedes, cambiar pestaña
            if (moduloActual === 'warehouses' && modulo === 'traslados') {
              const cambioResult = await uiContext.executeAction('cambiarPestanaSedes', { tab: 'transfers' })
              result = cambioResult || { 
                success: true, 
                message: `Cambiando a la pestaña de Traslados` 
              }
              console.log(`🔄 [navegarModulo] Estamos en Sedes, cambiando a pestaña Traslados`)
              break
            }
            
            const yaEstaEnModulo = moduloActual === moduloFinal
            
            // Si ya está en el módulo y no hay filtro nuevo, avisar
            if (yaEstaEnModulo && !filtroFinal) {
              const nombresModulo = {
                'dashboard': 'el Panel de Control',
                'products': 'Productos',
                'invoices': 'Facturas',
                'returns-management': 'Devoluciones',
                'customers': 'Clientes',
                'pos': 'el Punto de Venta',
                'reports': 'Reportes',
                'settings': 'Configuración',
                'warehouses': 'Gestión de Sedes'
              }
              result = { success: true, message: `Ya estás en ${nombresModulo[moduloFinal] || moduloFinal}` }
              console.log(`⚠️ [navegarModulo] Ya está en el módulo, no navegando`)
              break
            }
            
            // Navegar con filtro si existe
            console.log(`✅ [navegarModulo] Navegando a ${moduloFinal}${filtroFinal ? ' con filtro: ' + filtroFinal : ''}`)
            if (filtroFinal) {
              navigateToModule(moduloFinal, { filter: filtroFinal })
              result = { success: true, message: yaEstaEnModulo 
                ? `Listo, cambié el gráfico a ${filtroNav}` 
                : `Listo, estás en ${modulo} con ${filtroNav}` }
            } else {
              navigateToModule(moduloFinal)
              result = { success: true, message: `Listo, estás en ${modulo}` }
            }
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
          
          case 'consultarDatosNegocio':
            try {
              const tipoConsulta = fc.args?.tipo || 'resumen_completo'
              const uiContextBiz = useUIContextStore()
              const datosGlobales = uiContextBiz.globalBusinessData
              const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
              
              // Verificar si los datos globales tienen información real
              const tienesDatos = datosGlobales.inventario.productosActivos > 0 || 
                                  datosGlobales.ventas.ventasHoy > 0 ||
                                  datosGlobales.inventario.valorInvertido > 0
              
              // Si no hay datos, navegar a Inventario Inteligente primero
              if (!tienesDatos) {
                navigateToModule('intelligent_inventory')
                result = { 
                  success: true, 
                  message: 'Te estoy llevando a Inventario Inteligente para cargar los datos actualizados. En un momento te digo los números exactos.',
                  navigated: true,
                  needsRecheck: true
                }
                break
              }
              
              // Construir respuesta según el tipo de consulta
              let respuesta = ''
              
              switch (tipoConsulta) {
                case 'ganancias':
                  respuesta = `📈 GANANCIAS DEL NEGOCIO:
• Ganancia estimada en inventario: ${formatMoney(datosGlobales.inventario.gananciaEstimada)}
• Ganancia bruta del mes: ${formatMoney(datosGlobales.ganancias.gananciaBrutaMes)}
• Gastos del mes: ${formatMoney(datosGlobales.gastos.gastosMes)}
• Ganancia neta: ${formatMoney(datosGlobales.ganancias.gananciaNeta)}
• Margen promedio: ${datosGlobales.ganancias.margenPromedio}%`
                  break
                  
                case 'inventario':
                  respuesta = `📦 ESTADO DEL INVENTARIO:
• Productos activos: ${datosGlobales.inventario.productosActivos} de ${datosGlobales.inventario.productosTotal}
• Valor invertido (costo): ${formatMoney(datosGlobales.inventario.valorInvertido)}
• Valor potencial (precio venta): ${formatMoney(datosGlobales.inventario.valorPotencial)}
• Ganancia estimada: ${formatMoney(datosGlobales.inventario.gananciaEstimada)}
• Stock bajo: ${datosGlobales.inventario.stockBajo} productos
• Sin stock: ${datosGlobales.inventario.sinStock} productos`
                  break
                  
                case 'ventas':
                  respuesta = `💰 VENTAS:
• Ventas de hoy: ${formatMoney(datosGlobales.ventas.ventasHoy)} (${datosGlobales.ventas.transaccionesHoy} transacciones)
• Ventas del mes: ${formatMoney(datosGlobales.ventas.ventasMes)} (${datosGlobales.ventas.transaccionesMes} transacciones)
• Ticket promedio: ${formatMoney(datosGlobales.ventas.ticketPromedio)}`
                  break
                  
                case 'gastos':
                  respuesta = `💸 GASTOS:
• Gastos del mes: ${formatMoney(datosGlobales.gastos.gastosMes)}
• Gastos de hoy: ${formatMoney(datosGlobales.gastos.gastosHoy)}`
                  break
                  
                case 'alertas':
                  const alertas = datosGlobales.alertas.productosStockBajo || []
                  if (alertas.length === 0) {
                    respuesta = '✅ No hay alertas de stock. Todo está en orden.'
                  } else {
                    respuesta = `⚠️ ALERTAS DE STOCK (${alertas.length} productos):\n`
                    alertas.slice(0, 5).forEach(p => {
                      respuesta += `• ${p.nombre}: ${p.stock} unidades\n`
                    })
                    if (alertas.length > 5) {
                      respuesta += `...y ${alertas.length - 5} más`
                    }
                  }
                  break
                  
                case 'resumen_completo':
                default:
                  respuesta = `📊 RESUMEN DEL NEGOCIO:

💰 VENTAS:
• Hoy: ${formatMoney(datosGlobales.ventas.ventasHoy)} (${datosGlobales.ventas.transaccionesHoy} ventas)
• Mes: ${formatMoney(datosGlobales.ventas.ventasMes)}

📦 INVENTARIO:
• ${datosGlobales.inventario.productosActivos} productos activos
• Valor invertido: ${formatMoney(datosGlobales.inventario.valorInvertido)}
• Ganancia estimada: ${formatMoney(datosGlobales.inventario.gananciaEstimada)}
• Stock bajo: ${datosGlobales.inventario.stockBajo} productos

💸 GASTOS Y GANANCIAS:
• Gastos del mes: ${formatMoney(datosGlobales.gastos.gastosMes)}
• Ganancia neta: ${formatMoney(datosGlobales.ganancias.gananciaNeta)}

🏦 CAJA: ${datosGlobales.caja.estado === 'abierta' ? 'Abierta' : 'Cerrada'} - ${formatMoney(datosGlobales.caja.montoActual)}`
                  break
              }
              
              result = { success: true, data: respuesta }
            } catch (err) {
              result = { success: false, message: 'Error consultando datos del negocio' }
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
              const params = fc.args?.params || {}
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
              
              // Ejecutar la acción con parámetros
              const actionResult = await uiContext.executeAction(accionId, params)
              
              const actionLabels = {
                sendEmail: 'enviando por email',
                sendWhatsApp: 'enviando por WhatsApp', 
                downloadPDF: 'descargando PDF',
                printInvoice: 'imprimiendo',
                buscarProducto: 'buscando producto',
                limpiarBusqueda: 'limpiando búsqueda',
                filtrarPorEstado: 'filtrando productos',
                abrirCrearProducto: 'abriendo formulario de producto',
                seleccionarProducto: 'seleccionando producto',
                editarProductoSeleccionado: 'abriendo editor de producto'
              }
              
              if (actionResult.success) {
                result = { 
                  success: true, 
                  message: actionResult.message || `Listo, ${actionLabels[accionId] || accionId}`
                }
              } else {
                result = actionResult
              }
            } catch (err) {
              result = { success: false, message: 'Error ejecutando la acción' }
            }
            break
          
          case 'buscarProductoEnVivo':
            try {
              const texto = fc.args?.texto
              if (!texto) {
                result = { success: false, message: 'Dime qué producto buscar' }
                break
              }
              
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en el módulo de productos
              if (uiContext.currentModule !== 'products') {
                // Navegar primero a productos
                navigateToModule('products')
                // Esperar un momento para que se cargue
                await new Promise(resolve => setTimeout(resolve, 500))
              }
              
              // Ejecutar la búsqueda
              const searchResult = await uiContext.executeAction('buscarProducto', { texto })
              
              if (searchResult?.success) {
                // Si encontró un producto específico con búsqueda flexible, usar ese mensaje
                if (searchResult.productoEncontrado) {
                  result = { 
                    success: true, 
                    message: `Encontré "${searchResult.productoEncontrado}". ¿Es ese el que buscas?`
                  }
                } else {
                  result = { 
                    success: true, 
                    message: searchResult.resultados > 0 
                      ? `Encontré ${searchResult.resultados} producto${searchResult.resultados !== 1 ? 's' : ''}`
                      : `No encontré productos con "${texto}". Intenta con menos palabras.`
                  }
                }
              } else {
                // Si no está la acción registrada, navegar y avisar
                navigateToModule('products', { search: texto })
                result = { success: true, message: `Te llevo a productos para buscar "${texto}"` }
              }
            } catch (err) {
              console.error('Error en buscarProductoEnVivo:', err)
              result = { success: false, message: 'Error al buscar producto' }
            }
            break
          
          case 'buscarProveedorDeProducto':
            try {
              const uiContext = useUIContextStore()
              const nombreProducto = fc.args?.nombreProducto
              
              if (!nombreProducto) {
                result = { success: false, message: 'Dime el nombre del producto para buscar su proveedor' }
                break
              }
              
              // Primero ir a productos para tener acceso a la acción
              if (uiContext.currentModule !== 'products') {
                navigateToModule('products')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const buscarProvResult = await uiContext.executeAction('buscarProveedorDeProducto', { nombreProducto })
              
              if (buscarProvResult?.success && buscarProvResult.proveedorAsignado) {
                result = {
                  success: true,
                  productoEncontrado: buscarProvResult.productoEncontrado,
                  proveedorAsignado: buscarProvResult.proveedorAsignado,
                  message: buscarProvResult.message
                }
              } else if (buscarProvResult?.success && !buscarProvResult.proveedorAsignado) {
                result = {
                  success: true,
                  productoEncontrado: buscarProvResult.productoEncontrado,
                  proveedorAsignado: null,
                  message: buscarProvResult.message,
                  proveedoresDisponibles: buscarProvResult.proveedoresDisponibles
                }
              } else {
                result = buscarProvResult || { success: false, message: 'No pude encontrar información del proveedor' }
              }
            } catch (err) {
              console.error('Error en buscarProveedorDeProducto:', err)
              result = { success: false, message: 'Error al buscar proveedor del producto' }
            }
            break
          
          case 'editarProductoPorVoz':
            try {
              const nombreProducto = fc.args?.nombreProducto // Puede ser undefined si hay 1 producto filtrado
              const campo = fc.args?.campo
              const nuevoValor = fc.args?.nuevoValor
              
              // Solo campo y nuevoValor son requeridos, nombreProducto es opcional
              if (!campo || nuevoValor === undefined) {
                result = { success: false, message: 'Necesito saber qué campo cambiar y el nuevo valor' }
                break
              }
              
              const uiContext = useUIContextStore()
              const moduloActual = uiContext.currentModule
              
              // 🧠 LÓGICA INTELIGENTE: 
              // - Si es cambio de STOCK y estamos en inventory → quedarse aquí
              // - Si es cambio de otro campo (precio, nombre, etc) → ir a products
              // - Si estamos en products → quedarse aquí para todo
              const esCambioStock = campo.toLowerCase().includes('stock') || campo.toLowerCase().includes('cantidad')
              const modulosSoportanStock = ['products', 'inventory', 'stock']
              
              if (esCambioStock && modulosSoportanStock.includes(moduloActual)) {
                // Quedarse en el módulo actual, ambos soportan cambio de stock
                console.log(`🎙️ [editarProductoPorVoz] Editando stock en módulo actual: ${moduloActual}`)
              } else if (moduloActual !== 'products') {
                // Para otros campos, ir a productos
                navigateToModule('products')
                await new Promise(resolve => setTimeout(resolve, 800))
              }
              
              // 🔧 USAR LA NUEVA ACCIÓN DIRECTA que edita y guarda automáticamente
              console.log('🎙️ [editarProductoPorVoz] Ejecutando editarCampoProducto...')
              const editResult = await uiContext.executeAction('editarCampoProducto', {
                nombreProducto, // Puede ser undefined, la acción lo manejará
                campo,
                nuevoValor
              })
              
              result = editResult
              
            } catch (err) {
              console.error('Error en editarProductoPorVoz:', err)
              result = { success: false, message: 'Error al intentar editar el producto' }
            }
            break
          
          case 'crearProductoConversacional':
            try {
              const accion = fc.args?.accion
              const campo = fc.args?.campo
              const valor = fc.args?.valor
              
              const uiContext = useUIContextStore()
              
              if (accion === 'iniciar') {
                // 🔒 VALIDACIÓN ROBUSTA: Navegar a productos con reintentos
                let intentos = 0
                const maxIntentos = 3
                
                while (uiContext.currentModule !== 'products' && intentos < maxIntentos) {
                  console.log(`🔄 [crearProducto] Navegando a productos (intento ${intentos + 1}/${maxIntentos})`)
                  navigateToModule('products')
                  await new Promise(resolve => setTimeout(resolve, 800))
                  intentos++
                }
                
                if (uiContext.currentModule !== 'products') {
                  result = { 
                    success: false, 
                    message: 'No pude navegar al módulo de productos. Por favor ve manualmente a Productos y vuelve a intentarlo.' 
                  }
                  break
                }
                
                // Esperar a que el módulo esté listo
                await new Promise(resolve => setTimeout(resolve, 300))
                
                // 🔒 Intentar abrir el modal con reintentos
                let modalAbierto = false
                let openResult = null
                
                for (let i = 0; i < 3 && !modalAbierto; i++) {
                  console.log(`🔄 [crearProducto] Abriendo modal (intento ${i + 1}/3)`)
                  openResult = await uiContext.executeAction('abrirCrearProducto')
                  
                  // Esperar y verificar que el modal se abrió
                  await new Promise(resolve => setTimeout(resolve, 400))
                  
                  const contextoActual = uiContext.getContextForAI()
                  modalAbierto = contextoActual?.screenData?.modalAbierto === 'crear' || 
                                 contextoActual?.screenData?.formularioProducto !== null
                  
                  if (!modalAbierto && i < 2) {
                    console.log('⚠️ [crearProducto] Modal no detectado, reintentando...')
                  }
                }
                
                if (!modalAbierto && !openResult?.success) {
                  result = { 
                    success: false, 
                    message: 'No pude abrir el formulario de crear producto. Intenta hacer clic en el botón "Nuevo Producto" manualmente.' 
                  }
                  break
                }
                
                // Obtener contexto actualizado
                const contexto = uiContext.getContextForAI()
                const tipoTienda = contexto.screenData?.tipoTienda || 'general'
                const categorias = contexto.screenData?.categoriasDisponibles || []
                
                let mensaje = '¡Listo! El formulario está abierto. '
                mensaje += 'Necesito: nombre, categoría, precio de costo y precio de venta. '
                
                if (categorias.length > 0) {
                  mensaje += `Tienes ${categorias.length} categorías. `
                }
                
                mensaje += '¿Cómo se llama el producto?'
                
                result = { success: true, message: mensaje, tipoTienda, categorias: categorias.slice(0, 10) }
              } else if (accion === 'asignar' && campo && valor) {
                // 🔒 Verificar que el modal está abierto antes de asignar
                const contextoActual = uiContext.getContextForAI()
                if (!contextoActual?.screenData?.formularioProducto) {
                  result = { 
                    success: false, 
                    message: 'El formulario no está abierto. Primero usa iniciar para abrir el modal de crear producto.' 
                  }
                  break
                }
                
                // Llenar el campo visualmente
                const llenarResult = await uiContext.executeAction('llenarCampoProducto', { campo, valor })
                
                if (llenarResult?.success) {
                  // Verificar qué campos faltan
                  const formulario = uiContext.getContextForAI()?.screenData?.formularioProducto
                  const faltantes = formulario?.camposFaltantes || []
                  
                  let mensaje = llenarResult.message
                  if (faltantes.length > 0) {
                    mensaje += ` Aún faltan: ${faltantes.join(', ')}.`
                  } else {
                    mensaje += ' ¡Ya tenemos todos los datos obligatorios! ¿Quieres que guarde el producto?'
                  }
                  
                  result = { success: true, message: mensaje, camposFaltantes: faltantes }
                } else {
                  result = llenarResult
                }
              } else if (accion === 'confirmar') {
                // 🔒 Verificar que hay un formulario para guardar
                const contextoActual = uiContext.getContextForAI()
                if (!contextoActual?.screenData?.formularioProducto) {
                  result = { 
                    success: false, 
                    message: 'No hay un producto para guardar. Primero abre el formulario y llena los datos.' 
                  }
                  break
                }
                
                // Guardar el producto
                const guardarResult = await uiContext.executeAction('guardarProducto')
                result = guardarResult || { success: true, message: 'Producto guardado' }
              } else if (accion === 'cancelar') {
                await uiContext.executeAction('cerrarModalProducto')
                result = { success: true, message: 'Creación cancelada. ¿En qué más te ayudo?' }
              } else {
                result = { 
                  success: false, 
                  message: 'Acción no reconocida. Usa: iniciar, asignar (con campo y valor), confirmar, o cancelar'
                }
              }
            } catch (err) {
              console.error('Error en crearProductoConversacional:', err)
              result = { success: false, message: 'Error al intentar crear producto' }
            }
            break
          
          // 📂 CATEGORÍAS
          case 'buscarCategoriaEnVivo':
            try {
              const texto = fc.args?.texto
              if (!texto) {
                result = { success: false, message: 'Dime qué categoría buscar' }
                break
              }
              
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en categorías
              if (uiContext.currentModule !== 'categories') {
                navigateToModule('categories')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Ejecutar búsqueda
              const searchResult = await uiContext.executeAction('buscarCategoria', { texto })
              
              if (searchResult) {
                result = {
                  success: true,
                  message: searchResult.resultados > 0 
                    ? `Encontré ${searchResult.resultados} categoría${searchResult.resultados !== 1 ? 's' : ''}`
                    : `No encontré categorías con "${texto}"`
                }
              } else {
                navigateToModule('categories', { search: texto })
                result = { success: true, message: `Buscando "${texto}" en categorías` }
              }
            } catch (err) {
              console.error('Error en buscarCategoriaEnVivo:', err)
              result = { success: false, message: 'Error al buscar categoría' }
            }
            break
          
          case 'verProductosCategoria':
            try {
              const nombre = fc.args?.nombre
              if (!nombre) {
                result = { success: false, message: 'Dime el nombre de la categoría' }
                break
              }
              
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en categorías
              if (uiContext.currentModule !== 'categories') {
                navigateToModule('categories')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Ejecutar acción
              const viewResult = await uiContext.executeAction('verProductosCategoria', { nombre })
              result = viewResult || { success: false, message: 'No pude mostrar los productos' }
            } catch (err) {
              console.error('Error en verProductosCategoria:', err)
              result = { success: false, message: 'Error al mostrar productos de la categoría' }
            }
            break
          
          case 'crearCategoria':
            try {
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en categorías
              if (uiContext.currentModule !== 'categories') {
                navigateToModule('categories')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Abrir modal de crear
              const createResult = await uiContext.executeAction('abrirCrearCategoria')
              result = createResult || { success: true, message: 'Modal de crear categoría abierto. Escribe el nombre.' }
            } catch (err) {
              console.error('Error en crearCategoria:', err)
              result = { success: false, message: 'Error al abrir el formulario de crear categoría' }
            }
            break
          
          case 'crearCategoriaRapida':
            try {
              const nombre = fc.args?.nombre
              if (!nombre) {
                result = { success: false, message: 'Dime el nombre de la categoría a crear' }
                break
              }
              
              const uiContext = useUIContextStore()
              
              // Crear categoría rápida (funciona desde productos)
              const crearResult = await uiContext.executeAction('crearCategoriaRapida', { nombre })
              result = crearResult || { success: true, message: `Categoría "${nombre}" creada` }
            } catch (err) {
              console.error('Error en crearCategoriaRapida:', err)
              result = { success: false, message: 'Error al crear la categoría' }
            }
            break
          
          // 📦 INVENTARIO
          case 'cambiarTabInventario':
            try {
              const tab = fc.args?.tab?.toLowerCase()
              if (!tab) {
                result = { success: false, message: 'Dime a qué pestaña ir: stock, movimientos o alertas' }
                break
              }
              
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en inventario (el módulo se llama 'stock')
              if (uiContext.currentModule !== 'stock') {
                navigateToModule('stock')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Mapear nombres a tabs
              let tabName = 'stock'
              if (tab.includes('movimiento')) tabName = 'movimientos'
              else if (tab.includes('alerta') || tab.includes('bajo')) tabName = 'alertas'
              else if (tab.includes('stock') || tab.includes('actual')) tabName = 'stock'
              
              // Ejecutar cambio de pestaña
              const tabResult = await uiContext.executeAction('cambiarTabInventario', { tab: tabName })
              
              if (tabResult?.success) {
                const contexto = uiContext.getContextForAI()
                const kpis = contexto.screenData?.kpis || {}
                
                let mensaje = tabResult.message || `Cambiado a ${tabName}`
                
                // Agregar info relevante según la pestaña
                if (tabName === 'alertas' && kpis.stockBajo !== undefined) {
                  mensaje += `. Hay ${kpis.stockBajo} productos con stock bajo.`
                } else if (tabName === 'movimientos' && kpis.movimientosHoy !== undefined) {
                  mensaje += `. Hoy se han registrado ${kpis.movimientosHoy} movimientos.`
                } else if (tabName === 'stock' && kpis.totalProductos !== undefined) {
                  mensaje += `. Hay ${kpis.totalProductos} productos en inventario.`
                }
                
                result = { success: true, message: mensaje }
              } else {
                result = tabResult || { success: false, message: 'No pude cambiar de pestaña' }
              }
            } catch (err) {
              console.error('Error en cambiarTabInventario:', err)
              result = { success: false, message: 'Error al cambiar de pestaña' }
            }
            break
          
          case 'buscarInventario':
            try {
              const texto = fc.args?.texto
              if (!texto) {
                result = { success: false, message: 'Dime qué producto buscar en el inventario' }
                break
              }
              
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en inventario (el módulo se llama 'stock')
              if (uiContext.currentModule !== 'stock') {
                navigateToModule('stock')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Ejecutar búsqueda
              const searchResult = await uiContext.executeAction('buscarInventario', { texto })
              
              if (searchResult) {
                result = {
                  success: true,
                  message: searchResult.resultados > 0 
                    ? `Encontré ${searchResult.resultados} producto${searchResult.resultados !== 1 ? 's' : ''} en el inventario`
                    : `No encontré productos con "${texto}" en el inventario`
                }
              } else {
                result = { success: true, message: `Buscando "${texto}" en el inventario` }
              }
            } catch (err) {
              console.error('Error en buscarInventario:', err)
              result = { success: false, message: 'Error al buscar en el inventario' }
            }
            break
          
          case 'filtrarInventarioPorStock':
            try {
              const nivel = fc.args?.nivel?.toLowerCase()
              if (!nivel) {
                result = { success: false, message: 'Dime el nivel de stock: bajo, normal, alto o todos' }
                break
              }
              
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en inventario (el módulo se llama 'stock')
              if (uiContext.currentModule !== 'stock') {
                navigateToModule('stock')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Ejecutar filtro
              const filterResult = await uiContext.executeAction('filtrarInventarioPorStock', { nivel })
              
              if (filterResult?.success) {
                result = {
                  success: true,
                  message: filterResult.message || `Mostrando productos con stock ${nivel}`
                }
              } else {
                result = filterResult || { success: false, message: 'No pude aplicar el filtro' }
              }
            } catch (err) {
              console.error('Error en filtrarInventarioPorStock:', err)
              result = { success: false, message: 'Error al filtrar por stock' }
            }
            break
          
          case 'verAlertasInventario':
            try {
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en inventario (el módulo se llama 'stock')
              if (uiContext.currentModule !== 'stock') {
                navigateToModule('stock')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Cambiar a pestaña de alertas
              const alertResult = await uiContext.executeAction('verAlertasInventario')
              
              if (alertResult?.success) {
                const contexto = uiContext.getContextForAI()
                const alertas = contexto.screenData?.alertasStock || []
                
                let mensaje = alertResult.message || 'Mostrando alertas de inventario'
                if (alertas.length > 0) {
                  mensaje += `. Hay ${alertas.length} productos con stock bajo.`
                  // Mencionar los primeros 3
                  const primeros = alertas.slice(0, 3).map(p => p.nombre).join(', ')
                  if (alertas.length <= 3) {
                    mensaje += ` Son: ${primeros}.`
                  } else {
                    mensaje += ` Los primeros son: ${primeros}.`
                  }
                } else {
                  mensaje = 'No hay productos con stock bajo. ¡Todo está bien!'
                }
                
                result = { success: true, message: mensaje }
              } else {
                result = alertResult || { success: false, message: 'No pude mostrar las alertas' }
              }
            } catch (err) {
              console.error('Error en verAlertasInventario:', err)
              result = { success: false, message: 'Error al mostrar alertas de inventario' }
            }
            break
          
          // 📊 INVENTARIO INTELIGENTE
          case 'cambiarSeccionInventarioInteligente':
            try {
              const seccion = fc.args?.seccion
              if (!seccion) {
                result = { success: false, message: 'Dime a qué sección ir: general, productos, movimientos, clientes, proveedores, alertas o predicciones' }
                break
              }
              
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en inventario inteligente
              if (uiContext.currentModule !== 'intelligent_inventory') {
                navigateToModule('intelligent_inventory')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Ejecutar cambio de sección
              const secResult = await uiContext.executeAction('cambiarSeccionInventarioInteligente', { seccion })
              
              if (secResult?.success) {
                result = secResult
              } else {
                result = secResult || { success: false, message: 'No pude cambiar de sección' }
              }
            } catch (err) {
              console.error('Error en cambiarSeccionInventarioInteligente:', err)
              result = { success: false, message: 'Error al cambiar de sección' }
            }
            break
          
          case 'buscarProductoInventarioInteligente':
            try {
              const texto = fc.args?.texto
              if (!texto) {
                result = { success: false, message: 'Dime qué producto buscar' }
                break
              }
              
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en inventario inteligente
              if (uiContext.currentModule !== 'intelligent_inventory') {
                navigateToModule('intelligent_inventory')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Ejecutar búsqueda
              const searchResult = await uiContext.executeAction('buscarProductoInventarioInteligente', { texto })
              result = searchResult || { success: true, message: `Buscando "${texto}"` }
            } catch (err) {
              console.error('Error en buscarProductoInventarioInteligente:', err)
              result = { success: false, message: 'Error al buscar producto' }
            }
            break
          
          case 'cambiarPeriodoInventarioInteligente':
            try {
              const periodo = fc.args?.periodo
              if (!periodo) {
                result = { success: false, message: 'Dime el período: hoy, semana, mes o año' }
                break
              }
              
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en inventario inteligente
              if (uiContext.currentModule !== 'intelligent_inventory') {
                navigateToModule('intelligent_inventory')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Ejecutar cambio de período
              const periodoResult = await uiContext.executeAction('cambiarPeriodoInventarioInteligente', { periodo })
              result = periodoResult || { success: true, message: `Período cambiado a ${periodo}` }
            } catch (err) {
              console.error('Error en cambiarPeriodoInventarioInteligente:', err)
              result = { success: false, message: 'Error al cambiar período' }
            }
            break
          
          case 'verAlertasInventarioInteligente':
            try {
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en inventario inteligente
              if (uiContext.currentModule !== 'intelligent_inventory') {
                navigateToModule('intelligent_inventory')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Ir a alertas
              const alertResult = await uiContext.executeAction('verAlertasInventarioInteligente')
              result = alertResult || { success: true, message: 'Mostrando alertas' }
            } catch (err) {
              console.error('Error en verAlertasInventarioInteligente:', err)
              result = { success: false, message: 'Error al mostrar alertas' }
            }
            break
          
          case 'verPrediccionesInventarioInteligente':
            try {
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en inventario inteligente
              if (uiContext.currentModule !== 'intelligent_inventory') {
                navigateToModule('intelligent_inventory')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Ir a predicciones
              const predResult = await uiContext.executeAction('verPrediccionesInventarioInteligente')
              result = predResult || { success: true, message: 'Mostrando predicciones' }
            } catch (err) {
              console.error('Error en verPrediccionesInventarioInteligente:', err)
              result = { success: false, message: 'Error al mostrar predicciones' }
            }
            break
          
          case 'buscarClienteInventarioInteligente':
            try {
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en inventario inteligente
              if (uiContext.currentModule !== 'intelligent_inventory') {
                navigateToModule('intelligent_inventory')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Ir a clientes
              const clientResult = await uiContext.executeAction('buscarClienteInventarioInteligente', { texto: fc.args?.texto || '' })
              result = clientResult || { success: true, message: 'Mostrando clientes en Inventario Inteligente' }
            } catch (err) {
              console.error('Error en buscarClienteInventarioInteligente:', err)
              result = { success: false, message: 'Error al mostrar clientes' }
            }
            break
          
          case 'buscarProveedorInventarioInteligente':
            try {
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en inventario inteligente
              if (uiContext.currentModule !== 'intelligent_inventory') {
                navigateToModule('intelligent_inventory')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Ir a proveedores
              const suppResult = await uiContext.executeAction('buscarProveedorInventarioInteligente', { texto: fc.args?.texto || '' })
              result = suppResult || { success: true, message: 'Mostrando proveedores en Inventario Inteligente' }
            } catch (err) {
              console.error('Error en buscarProveedorInventarioInteligente:', err)
              result = { success: false, message: 'Error al mostrar proveedores' }
            }
            break
          
          // ========== HANDLERS DE CLIENTES ==========
          case 'buscarClienteModulo':
            try {
              const uiContext = useUIContextStore()
              const nombreBuscar = fc.args?.nombre || ''
              
              // Verificar que estamos en clientes
              if (uiContext.currentModule !== 'customers') {
                navigateToModule('customers')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Buscar cliente
              const buscarResult = await uiContext.executeAction('buscarCliente', { texto: nombreBuscar })
              result = buscarResult || { success: true, message: `Buscando clientes con: "${nombreBuscar}"` }
            } catch (err) {
              console.error('Error en buscarClienteModulo:', err)
              result = { success: false, message: 'Error al buscar cliente' }
            }
            break
          
          case 'seleccionarCliente':
            try {
              const uiContext = useUIContextStore()
              const nombreCliente = fc.args?.nombre || ''
              
              // Verificar que estamos en clientes
              if (uiContext.currentModule !== 'customers') {
                navigateToModule('customers')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Seleccionar cliente por nombre
              const selResult = await uiContext.executeAction('seleccionarClientePorNombre', { nombre: nombreCliente })
              result = selResult || { success: true, message: `Buscando y seleccionando cliente: "${nombreCliente}"` }
            } catch (err) {
              console.error('Error en seleccionarCliente:', err)
              result = { success: false, message: 'Error al seleccionar cliente' }
            }
            break
          
          case 'verHistorialCliente':
            try {
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en clientes
              if (uiContext.currentModule !== 'customers') {
                navigateToModule('customers')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Cambiar a pestaña de historial
              const histResult = await uiContext.executeAction('cambiarPestanaCliente', { pestana: 'historial' })
              result = histResult || { success: true, message: 'Mostrando historial de compras del cliente' }
            } catch (err) {
              console.error('Error en verHistorialCliente:', err)
              result = { success: false, message: 'Error al mostrar historial' }
            }
            break
          
          case 'editarClienteActual':
            try {
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en clientes
              if (uiContext.currentModule !== 'customers') {
                navigateToModule('customers')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Editar cliente seleccionado
              const editResult = await uiContext.executeAction('editarClienteSeleccionado')
              result = editResult || { success: true, message: 'Abriendo editor de cliente' }
            } catch (err) {
              console.error('Error en editarClienteActual:', err)
              result = { success: false, message: 'Error al editar cliente' }
            }
            break
          
          case 'crearNuevoClienteVoz':
            try {
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en clientes
              if (uiContext.currentModule !== 'customers') {
                navigateToModule('customers')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Abrir modal de crear cliente
              const crearResult = await uiContext.executeAction('crearNuevoCliente')
              result = crearResult || { success: true, message: 'Abriendo formulario para nuevo cliente' }
            } catch (err) {
              console.error('Error en crearNuevoClienteVoz:', err)
              result = { success: false, message: 'Error al abrir formulario de cliente' }
            }
            break
          
          case 'filtrarClientesPorEstado':
            try {
              const uiContext = useUIContextStore()
              const estado = fc.args?.estado || 'todos'
              
              // Verificar que estamos en clientes
              if (uiContext.currentModule !== 'customers') {
                navigateToModule('customers')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Filtrar por estado
              const filtroResult = await uiContext.executeAction('filtrarClientesPorEstado', { estado })
              result = filtroResult || { success: true, message: `Filtrando clientes: ${estado}` }
            } catch (err) {
              console.error('Error en filtrarClientesPorEstado:', err)
              result = { success: false, message: 'Error al filtrar clientes' }
            }
            break
          
          case 'llenarCampoCliente':
            try {
              const uiContext = useUIContextStore()
              const campo = fc.args?.campo
              const valor = fc.args?.valor
              
              if (!campo || !valor) {
                result = { success: false, message: 'Debes especificar campo y valor' }
                break
              }
              
              // Verificar que estamos en clientes
              if (uiContext.currentModule !== 'customers') {
                navigateToModule('customers')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Llenar el campo del formulario
              const llenarResult = await uiContext.executeAction('llenarCampoCliente', { campo, valor })
              result = llenarResult || { success: true, message: `Campo "${campo}" actualizado a "${valor}"` }
            } catch (err) {
              console.error('Error en llenarCampoCliente:', err)
              result = { success: false, message: 'Error al llenar campo' }
            }
            break
          
          case 'guardarCliente':
            try {
              const uiContext = useUIContextStore()
              
              // Verificar que estamos en clientes
              if (uiContext.currentModule !== 'customers') {
                navigateToModule('customers')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              // Guardar el cliente
              const guardarResult = await uiContext.executeAction('guardarCliente')
              result = guardarResult || { success: true, message: 'Cliente guardado exitosamente' }
            } catch (err) {
              console.error('Error en guardarCliente:', err)
              result = { success: false, message: 'Error al guardar cliente' }
            }
            break
          
          case 'cerrarModalCliente':
            try {
              const uiContext = useUIContextStore()
              
              // Cerrar el modal
              const cerrarResult = await uiContext.executeAction('cerrarModalCliente')
              result = cerrarResult || { success: true, message: 'Modal cerrado' }
            } catch (err) {
              console.error('Error en cerrarModalCliente:', err)
              result = { success: false, message: 'Error al cerrar modal' }
            }
            break
          
          // === CREDITIENDA - GESTIÓN DE CRÉDITOS ===
          case 'crearNuevoCredito':
            try {
              const uiContext = useUIContextStore()
              
              // Navegar a CrediTienda si no estamos ahí
              if (uiContext.currentModule !== 'accounts-receivable') {
                navigateToModule('accounts-receivable')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const crearCreditoResult = await uiContext.executeAction('crearNuevoCredito')
              result = crearCreditoResult || { success: true, message: 'Modal abierto. Proporciona la cédula del cliente.' }
            } catch (err) {
              console.error('Error en crearNuevoCredito:', err)
              result = { success: false, message: 'Error al abrir formulario de crédito' }
            }
            break
          
          case 'buscarClientePorDocumento':
            try {
              const uiContext = useUIContextStore()
              const documento = fc.args?.documento
              
              if (!documento) {
                result = { success: false, message: 'Debes proporcionar el número de documento' }
                break
              }
              
              // Navegar a CrediTienda si no estamos ahí
              if (uiContext.currentModule !== 'accounts-receivable') {
                navigateToModule('accounts-receivable')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const buscarResult = await uiContext.executeAction('buscarClientePorDocumento', { documento })
              result = buscarResult || { success: true, message: `Buscando cliente con documento ${documento}` }
            } catch (err) {
              console.error('Error en buscarClientePorDocumento:', err)
              result = { success: false, message: 'Error al buscar cliente' }
            }
            break
          
          case 'llenarCampoCredito':
            try {
              const uiContext = useUIContextStore()
              const campo = fc.args?.campo
              const valor = fc.args?.valor
              
              if (!campo || !valor) {
                result = { success: false, message: 'Debes especificar campo y valor' }
                break
              }
              
              const llenarResult = await uiContext.executeAction('llenarCampoCredito', { campo, valor })
              result = llenarResult || { success: true, message: `Campo "${campo}" actualizado` }
            } catch (err) {
              console.error('Error en llenarCampoCredito:', err)
              result = { success: false, message: 'Error al llenar campo' }
            }
            break
          
          case 'guardarCredito':
            try {
              const uiContext = useUIContextStore()
              const guardarCreditoResult = await uiContext.executeAction('guardarCredito')
              result = guardarCreditoResult || { success: true, message: 'Crédito guardado exitosamente' }
            } catch (err) {
              console.error('Error en guardarCredito:', err)
              result = { success: false, message: 'Error al guardar crédito' }
            }
            break
          
          case 'seleccionarClienteCredito':
            try {
              const uiContext = useUIContextStore()
              const nombreCredito = fc.args?.nombre
              
              if (!nombreCredito) {
                result = { success: false, message: 'Debes proporcionar el nombre del cliente' }
                break
              }
              
              // Navegar a CrediTienda si no estamos ahí
              if (uiContext.currentModule !== 'accounts-receivable') {
                navigateToModule('accounts-receivable')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const selCreditoResult = await uiContext.executeAction('seleccionarClienteCredito', { nombre: nombreCredito })
              result = selCreditoResult || { success: true, message: `Seleccionando cliente: ${nombreCredito}` }
            } catch (err) {
              console.error('Error en seleccionarClienteCredito:', err)
              result = { success: false, message: 'Error al seleccionar cliente' }
            }
            break
          
          case 'registrarAbono':
            try {
              const uiContext = useUIContextStore()
              const monto = fc.args?.monto
              const metodo = fc.args?.metodo || 'cash'
              
              if (!monto || monto <= 0) {
                result = { success: false, message: 'Debes proporcionar un monto válido' }
                break
              }
              
              const abonoResult = await uiContext.executeAction('registrarAbono', { monto, metodo })
              result = abonoResult || { success: true, message: `Preparando abono de $${monto}` }
            } catch (err) {
              console.error('Error en registrarAbono:', err)
              result = { success: false, message: 'Error al registrar abono' }
            }
            break
          
          case 'confirmarAbono':
            try {
              const uiContext = useUIContextStore()
              const confirmarResult = await uiContext.executeAction('confirmarAbono')
              result = confirmarResult || { success: true, message: 'Abono confirmado' }
            } catch (err) {
              console.error('Error en confirmarAbono:', err)
              result = { success: false, message: 'Error al confirmar abono' }
            }
            break
          
          case 'cerrarModalCredito':
            try {
              const uiContext = useUIContextStore()
              const cerrarCreditoResult = await uiContext.executeAction('cerrarModalCredito')
              result = cerrarCreditoResult || { success: true, message: 'Modal cerrado' }
            } catch (err) {
              console.error('Error en cerrarModalCredito:', err)
              result = { success: false, message: 'Error al cerrar modal' }
            }
            break
          
          // === PROVEEDORES Y COMPRAS ===
          case 'cambiarPestanaCompras':
            try {
              const uiContext = useUIContextStore()
              const pestana = fc.args?.pestana
              
              // Navegar a compras si no estamos ahí
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('proveedores')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const cambiarTabResult = await uiContext.executeAction('cambiarPestanaCompras', { pestana })
              result = cambiarTabResult || { success: true, message: `Cambiado a pestaña ${pestana}` }
            } catch (err) {
              console.error('Error en cambiarPestanaCompras:', err)
              result = { success: false, message: 'Error al cambiar pestaña' }
            }
            break
          
          case 'crearNuevoProveedor':
            try {
              const uiContext = useUIContextStore()
              
              // SIEMPRE navegar a purchase-orders para asegurarnos
              navigateToModule('purchase-orders')
              await new Promise(resolve => setTimeout(resolve, 800))
              
              // Esperar a que las acciones estén registradas (el componente debe montar)
              let intentos = 0
              while (intentos < 5) {
                const testResult = await uiContext.executeAction('crearNuevoProveedor')
                if (testResult && testResult.success !== false) {
                  result = testResult
                  break
                }
                // Si la acción no está disponible, esperar más
                if (testResult?.message?.includes('no disponible')) {
                  await new Promise(resolve => setTimeout(resolve, 400))
                  intentos++
                } else {
                  // La acción se ejecutó (aunque haya fallado por otra razón)
                  result = testResult
                  break
                }
              }
              
              if (intentos >= 5) {
                result = { success: false, message: 'El módulo de proveedores aún está cargando. Intenta de nuevo.' }
              }
              
              if (!result) {
                result = { success: true, message: 'Formulario de proveedor abierto. Dame el nombre del proveedor.' }
              }
            } catch (err) {
              console.error('Error en crearNuevoProveedor:', err)
              result = { success: false, message: 'Error al abrir formulario' }
            }
            break
          
          case 'llenarCampoProveedor':
            try {
              const uiContext = useUIContextStore()
              const campo = fc.args?.campo
              const valor = fc.args?.valor
              
              if (!campo || !valor) {
                result = { success: false, message: 'Debes especificar campo y valor' }
                break
              }
              
              const llenarProvResult = await uiContext.executeAction('llenarCampoProveedor', { campo, valor })
              result = llenarProvResult || { success: true, message: `Campo "${campo}" actualizado` }
            } catch (err) {
              console.error('Error en llenarCampoProveedor:', err)
              result = { success: false, message: 'Error al llenar campo' }
            }
            break
          
          case 'guardarProveedor':
            try {
              const uiContext = useUIContextStore()
              const guardarProvResult = await uiContext.executeAction('guardarProveedor')
              result = guardarProvResult || { success: true, message: 'Proveedor guardado' }
            } catch (err) {
              console.error('Error en guardarProveedor:', err)
              result = { success: false, message: 'Error al guardar proveedor' }
            }
            break
          
          case 'verificarFormularioProveedor':
            try {
              const uiContext = useUIContextStore()
              const verificarResult = await uiContext.executeAction('verificarFormularioProveedor')
              result = verificarResult || { success: false, message: 'No hay formulario visible' }
            } catch (err) {
              console.error('Error en verificarFormularioProveedor:', err)
              result = { success: false, message: 'Error al verificar formulario' }
            }
            break
          
          case 'buscarProveedor':
            try {
              const uiContext = useUIContextStore()
              const texto = fc.args?.texto
              
              if (!texto) {
                result = { success: false, message: 'Debes proporcionar texto de búsqueda' }
                break
              }
              
              // Navegar a compras si no estamos ahí
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('proveedores')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const buscarProvResult = await uiContext.executeAction('buscarProveedor', { texto })
              result = buscarProvResult || { success: true, message: `Buscando: ${texto}` }
            } catch (err) {
              console.error('Error en buscarProveedor:', err)
              result = { success: false, message: 'Error al buscar proveedor' }
            }
            break
          
          case 'listarProveedores':
            try {
              const uiContext = useUIContextStore()
              
              // Navegar a compras si no estamos ahí
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('proveedores')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const listarResult = await uiContext.executeAction('listarProveedores')
              result = listarResult || { success: false, message: 'No se pudieron listar los proveedores' }
            } catch (err) {
              console.error('Error en listarProveedores:', err)
              result = { success: false, message: 'Error al listar proveedores' }
            }
            break
          
          case 'cerrarFormularioProveedor':
            try {
              const uiContext = useUIContextStore()
              const cerrarProvResult = await uiContext.executeAction('cerrarFormularioProveedor')
              result = cerrarProvResult || { success: true, message: 'Formulario cerrado' }
            } catch (err) {
              console.error('Error en cerrarFormularioProveedor:', err)
              result = { success: false, message: 'Error al cerrar formulario' }
            }
            break
          
          case 'seleccionarProveedor':
            try {
              const uiContext = useUIContextStore()
              const nombre = fc.args?.nombre
              
              if (!nombre) {
                result = { success: false, message: 'Debes indicar el nombre del proveedor' }
                break
              }
              
              // Navegar a compras si no estamos ahí
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('proveedores')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const seleccionarResult = await uiContext.executeAction('seleccionarProveedor', { nombre })
              result = seleccionarResult || { success: false, message: `No encontré proveedor "${nombre}"` }
            } catch (err) {
              console.error('Error en seleccionarProveedor:', err)
              result = { success: false, message: 'Error al seleccionar proveedor' }
            }
            break
          
          case 'crearNuevaOrdenCompra':
            try {
              const uiContext = useUIContextStore()
              const nombreProveedor = fc.args?.nombreProveedor
              
              console.log('📦 [crearNuevaOrdenCompra] Proveedor solicitado:', nombreProveedor || 'ninguno')
              
              // SIEMPRE navegar a purchase-orders para que el usuario VEA el proceso
              console.log('🚀 [crearNuevaOrdenCompra] Navegando a purchase-orders...')
              navigateToModule('purchase-orders')
              await new Promise(resolve => setTimeout(resolve, 1000)) // Más tiempo para que cargue
              
              // Esperar a que las acciones estén registradas
              let intentosOrden = 0
              while (intentosOrden < 5) {
                const testResultOrden = await uiContext.executeAction('crearNuevaOrdenCompra', { nombreProveedor })
                if (testResultOrden && testResultOrden.success !== false) {
                  result = testResultOrden
                  break
                }
                if (testResultOrden?.message?.includes('no disponible')) {
                  await new Promise(resolve => setTimeout(resolve, 400))
                  intentosOrden++
                } else {
                  result = testResultOrden
                  break
                }
              }
              
              if (intentosOrden >= 5) {
                result = { success: false, message: 'El módulo de compras aún está cargando. Intenta de nuevo.' }
              }
              
              if (!result) {
                result = { success: true, message: nombreProveedor ? `Formulario abierto con proveedor ${nombreProveedor}` : 'Formulario de orden abierto. ¿A qué proveedor?' }
              }
            } catch (err) {
              console.error('Error en crearNuevaOrdenCompra:', err)
              result = { success: false, message: 'Error al abrir formulario de orden' }
            }
            break
          
          case 'seleccionarProveedorOrden':
            try {
              const uiContext = useUIContextStore()
              const nombre = fc.args?.nombre
              
              if (!nombre) {
                result = { success: false, message: 'Debes indicar el nombre del proveedor' }
                break
              }
              
              const selProvResult = await uiContext.executeAction('seleccionarProveedorOrden', { nombre })
              result = selProvResult || { success: true, message: `Proveedor ${nombre} seleccionado` }
            } catch (err) {
              console.error('Error en seleccionarProveedorOrden:', err)
              result = { success: false, message: 'Error al seleccionar proveedor' }
            }
            break
          
          case 'seleccionarBodegaOrden':
            try {
              const uiContext = useUIContextStore()
              const nombre = fc.args?.nombre
              
              if (!nombre) {
                result = { success: false, message: 'Debes indicar el nombre de la bodega' }
                break
              }
              
              const selBodegaResult = await uiContext.executeAction('seleccionarBodegaOrden', { nombre })
              result = selBodegaResult || { success: true, message: `Bodega ${nombre} seleccionada` }
            } catch (err) {
              console.error('Error en seleccionarBodegaOrden:', err)
              result = { success: false, message: 'Error al seleccionar bodega' }
            }
            break
          
          case 'agregarProductoOrden':
            try {
              const uiContext = useUIContextStore()
              const nombre = fc.args?.nombre
              const cantidad = fc.args?.cantidad || 1
              const costo = fc.args?.costo
              
              if (!nombre) {
                result = { success: false, message: 'Debes indicar el nombre del producto' }
                break
              }
              
              const agregarProdResult = await uiContext.executeAction('agregarProductoOrden', { nombre, cantidad, costo })
              result = agregarProdResult || { success: true, message: `Producto agregado` }
            } catch (err) {
              console.error('Error en agregarProductoOrden:', err)
              result = { success: false, message: 'Error al agregar producto' }
            }
            break
          
          case 'buscarProductoOrden':
            try {
              const uiContext = useUIContextStore()
              const texto = fc.args?.texto
              
              if (!texto) {
                result = { success: false, message: 'Debes indicar qué producto buscar' }
                break
              }
              
              const buscarProdResult = await uiContext.executeAction('buscarProductoOrden', { texto })
              result = buscarProdResult || { success: true, message: `Buscando: ${texto}` }
            } catch (err) {
              console.error('Error en buscarProductoOrden:', err)
              result = { success: false, message: 'Error al buscar producto' }
            }
            break
          
          case 'abrirSelectorProductos':
            try {
              const uiContext = useUIContextStore()
              
              // Asegurar que estamos en el módulo de compras
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('purchase-orders')
                await new Promise(resolve => setTimeout(resolve, 800))
              }
              
              const abrirModalResult = await uiContext.executeAction('abrirSelectorProductos', {})
              result = abrirModalResult || { success: true, message: 'Modal de productos abierto' }
            } catch (err) {
              console.error('Error en abrirSelectorProductos:', err)
              result = { success: false, message: 'Error al abrir selector de productos' }
            }
            break
          
          case 'llenarCampoOrden':
            try {
              const uiContext = useUIContextStore()
              const campo = fc.args?.campo
              const valor = fc.args?.valor
              
              if (!campo || !valor) {
                result = { success: false, message: 'Debes especificar campo y valor' }
                break
              }
              
              const llenarOrdenResult = await uiContext.executeAction('llenarCampoOrden', { campo, valor })
              result = llenarOrdenResult || { success: true, message: `Campo "${campo}" actualizado` }
            } catch (err) {
              console.error('Error en llenarCampoOrden:', err)
              result = { success: false, message: 'Error al llenar campo' }
            }
            break
          
          case 'guardarOrdenCompra':
            try {
              const uiContext = useUIContextStore()
              const comoBorrador = fc.args?.comoBorrador || false
              
              // Asegurar que estamos en el módulo de compras
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('purchase-orders')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const guardarOrdenResult = await uiContext.executeAction('guardarOrdenCompra', { comoBorrador })
              
              // Si el resultado es exitoso, dar mensaje claro
              if (guardarOrdenResult && guardarOrdenResult.success) {
                result = {
                  success: true,
                  message: guardarOrdenResult.message || '¡Orden de compra creada exitosamente!'
                }
              } else if (guardarOrdenResult) {
                result = guardarOrdenResult
              } else {
                // Si no hay resultado pero tampoco hubo error, asumir éxito
                result = { success: true, message: '¡Orden de compra guardada correctamente!' }
              }
            } catch (err) {
              console.error('Error en guardarOrdenCompra:', err)
              result = { success: false, message: 'Error al guardar la orden. ¿Quieres intentar de nuevo?' }
            }
            break
          
          case 'cerrarFormularioOrden':
            try {
              const uiContext = useUIContextStore()
              const cerrarOrdenResult = await uiContext.executeAction('cerrarFormularioOrden')
              result = cerrarOrdenResult || { success: true, message: 'Formulario cerrado' }
            } catch (err) {
              console.error('Error en cerrarFormularioOrden:', err)
              result = { success: false, message: 'Error al cerrar formulario' }
            }
            break
          
          case 'seleccionarOrdenCompra':
            try {
              const uiContext = useUIContextStore()
              const numero = fc.args?.numero
              
              if (!numero) {
                result = { success: false, message: 'Debes indicar el número de orden' }
                break
              }
              
              // Navegar a compras si no estamos ahí
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('proveedores')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const selOrdenResult = await uiContext.executeAction('seleccionarOrdenCompra', { numero })
              result = selOrdenResult || { success: true, message: `Orden ${numero} seleccionada` }
            } catch (err) {
              console.error('Error en seleccionarOrdenCompra:', err)
              result = { success: false, message: 'Error al seleccionar orden' }
            }
            break
          
          case 'filtrarOrdenesCompra':
            try {
              const uiContext = useUIContextStore()
              const estado = fc.args?.estado
              
              if (!estado) {
                result = { success: false, message: 'Debes indicar el estado: todas, pendientes, parciales, recibidas' }
                break
              }
              
              // Navegar a compras si no estamos ahí
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('proveedores')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const filtrarResult = await uiContext.executeAction('filtrarOrdenesCompra', { estado })
              result = filtrarResult || { success: true, message: `Filtrando por: ${estado}` }
            } catch (err) {
              console.error('Error en filtrarOrdenesCompra:', err)
              result = { success: false, message: 'Error al filtrar órdenes' }
            }
            break
          
          // === HANDLERS PARA ACCIONES DE ORDEN SELECCIONADA ===
          
          case 'descargarOrdenPDF':
            try {
              const uiContext = useUIContextStore()
              
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('purchase-orders')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const descargarResult = await uiContext.executeAction('descargarOrdenPDF', {})
              result = descargarResult || { success: true, message: 'PDF descargado' }
            } catch (err) {
              console.error('Error en descargarOrdenPDF:', err)
              result = { success: false, message: 'Error al descargar PDF' }
            }
            break
          
          case 'enviarOrdenEmail':
            try {
              const uiContext = useUIContextStore()
              
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('purchase-orders')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const enviarEmailResult = await uiContext.executeAction('enviarOrdenEmail', {})
              result = enviarEmailResult || { success: true, message: 'Email enviado' }
            } catch (err) {
              console.error('Error en enviarOrdenEmail:', err)
              result = { success: false, message: 'Error al enviar email' }
            }
            break
          
          case 'enviarOrdenWhatsApp':
            try {
              const uiContext = useUIContextStore()
              
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('purchase-orders')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const enviarWAResult = await uiContext.executeAction('enviarOrdenWhatsApp', {})
              result = enviarWAResult || { success: true, message: 'Abriendo WhatsApp' }
            } catch (err) {
              console.error('Error en enviarOrdenWhatsApp:', err)
              result = { success: false, message: 'Error al enviar WhatsApp' }
            }
            break
          
          case 'abrirModalIngresarStock':
            try {
              const uiContext = useUIContextStore()
              
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('purchase-orders')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const abrirModalResult = await uiContext.executeAction('abrirModalIngresarStock', {})
              result = abrirModalResult || { success: true, message: 'Modal de ingreso abierto' }
            } catch (err) {
              console.error('Error en abrirModalIngresarStock:', err)
              result = { success: false, message: 'Error al abrir modal de ingreso' }
            }
            break
          
          case 'confirmarIngresoStock':
            try {
              const uiContext = useUIContextStore()
              
              const confirmarResult = await uiContext.executeAction('confirmarIngresoStock', {})
              result = confirmarResult || { success: true, message: 'Productos ingresados al stock' }
            } catch (err) {
              console.error('Error en confirmarIngresoStock:', err)
              result = { success: false, message: 'Error al confirmar ingreso' }
            }
            break
          
          case 'marcarCantidadRecibida':
            try {
              const uiContext = useUIContextStore()
              const producto = fc.args?.producto
              const cantidad = fc.args?.cantidad
              const recibirTodo = fc.args?.recibirTodo
              
              if (!producto) {
                result = { success: false, message: 'Debes indicar el nombre del producto' }
                break
              }
              
              const marcarResult = await uiContext.executeAction('marcarCantidadRecibida', { producto, cantidad, recibirTodo })
              result = marcarResult || { success: true, message: 'Cantidad marcada' }
            } catch (err) {
              console.error('Error en marcarCantidadRecibida:', err)
              result = { success: false, message: 'Error al marcar cantidad' }
            }
            break
          
          case 'recibirTodosProductos':
            try {
              const uiContext = useUIContextStore()
              
              const recibirTodosResult = await uiContext.executeAction('recibirTodosProductos', {})
              result = recibirTodosResult || { success: true, message: 'Todos los productos marcados' }
            } catch (err) {
              console.error('Error en recibirTodosProductos:', err)
              result = { success: false, message: 'Error al marcar productos' }
            }
            break
          
          case 'marcarOrdenPagada':
            try {
              const uiContext = useUIContextStore()
              
              if (uiContext.currentModule !== 'purchase-orders') {
                navigateToModule('purchase-orders')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const marcarResult = await uiContext.executeAction('marcarOrdenPagada', {})
              result = marcarResult || { success: true, message: 'Abriendo modal para marcar como pagada' }
            } catch (err) {
              console.error('Error en marcarOrdenPagada:', err)
              result = { success: false, message: 'Error al marcar como pagada' }
            }
            break
          
          // === HANDLERS DE USUARIOS Y ROLES ===
          
          case 'listarUsuarios':
            try {
              const uiContext = useUIContextStore()
              
              if (uiContext.currentModule !== 'users-management' && uiContext.currentModule !== 'users') {
                navigateToModule('users-management')
                await new Promise(resolve => setTimeout(resolve, 600))
                
                // 🔒 Verificar si la navegación falló por permisos
                const navError = uiContext.getAndClearNavigationError()
                if (navError) {
                  result = { success: false, message: navError.message }
                  break
                }
              }
              
              const listarResult = await uiContext.executeAction('listarUsuarios', {})
              result = listarResult || { success: true, message: 'Listando usuarios' }
            } catch (err) {
              console.error('Error en listarUsuarios:', err)
              result = { success: false, message: 'Error al listar usuarios' }
            }
            break
          
          case 'listarRoles':
            try {
              const uiContext = useUIContextStore()
              
              if (uiContext.currentModule !== 'users-management' && uiContext.currentModule !== 'users') {
                navigateToModule('users-management')
                await new Promise(resolve => setTimeout(resolve, 600))
                
                // 🔒 Verificar si la navegación falló por permisos
                const navError = uiContext.getAndClearNavigationError()
                if (navError) {
                  result = { success: false, message: navError.message }
                  break
                }
              }
              
              const listarRolesResult = await uiContext.executeAction('listarRoles', {})
              result = listarRolesResult || { success: true, message: 'Listando roles' }
            } catch (err) {
              console.error('Error en listarRoles:', err)
              result = { success: false, message: 'Error al listar roles' }
            }
            break
          
          case 'abrirCrearUsuario':
            try {
              const uiContext = useUIContextStore()
              
              if (uiContext.currentModule !== 'users-management' && uiContext.currentModule !== 'users') {
                navigateToModule('users-management')
                await new Promise(resolve => setTimeout(resolve, 600))
                
                // 🔒 Verificar si la navegación falló por permisos
                const navError = uiContext.getAndClearNavigationError()
                if (navError) {
                  result = { success: false, message: navError.message }
                  break
                }
              }
              
              const crearUserResult = await uiContext.executeAction('abrirCrearUsuario', {})
              result = crearUserResult || { success: true, message: 'Modal de nuevo usuario abierto' }
            } catch (err) {
              console.error('Error en abrirCrearUsuario:', err)
              result = { success: false, message: 'Error al abrir formulario de usuario' }
            }
            break
          
          case 'llenarCampoUsuario':
            try {
              const uiContext = useUIContextStore()
              const campo = fc.args?.campo
              const valor = fc.args?.valor
              
              if (!campo || !valor) {
                result = { success: false, message: 'Debes especificar campo y valor' }
                break
              }
              
              // Asegurar que estamos en usuarios y el modal está abierto
              if (uiContext.currentModule !== 'users-management' && uiContext.currentModule !== 'users') {
                navigateToModule('users-management')
                await new Promise(resolve => setTimeout(resolve, 600))
                
                const navError = uiContext.getAndClearNavigationError()
                if (navError) {
                  result = { success: false, message: navError.message }
                  break
                }
              }
              
              const llenarResult = await uiContext.executeAction('llenarCampoUsuario', { campo, valor })
              result = llenarResult || { success: true, message: `Campo ${campo} llenado con: ${valor}` }
            } catch (err) {
              console.error('Error en llenarCampoUsuario:', err)
              result = { success: false, message: 'Error al llenar campo de usuario' }
            }
            break
          
          case 'guardarUsuario':
            try {
              const uiContext = useUIContextStore()
              
              const guardarResult = await uiContext.executeAction('guardarUsuario', {})
              result = guardarResult || { success: true, message: 'Usuario guardado correctamente' }
            } catch (err) {
              console.error('Error en guardarUsuario:', err)
              result = { success: false, message: 'Error al guardar usuario' }
            }
            break
          
          case 'editarUsuario':
            try {
              const uiContext = useUIContextStore()
              const busqueda = fc.args?.busqueda
              
              if (!busqueda) {
                result = { success: false, message: 'Debes indicar qué usuario editar (nombre o email)' }
                break
              }
              
              if (uiContext.currentModule !== 'users-management' && uiContext.currentModule !== 'users') {
                navigateToModule('users-management')
                await new Promise(resolve => setTimeout(resolve, 600))
                
                const navError = uiContext.getAndClearNavigationError()
                if (navError) {
                  result = { success: false, message: navError.message }
                  break
                }
              }
              
              const editResult = await uiContext.executeAction('editarUsuario', { busqueda })
              result = editResult || { success: true, message: `Buscando usuario: ${busqueda}` }
            } catch (err) {
              console.error('Error en editarUsuario:', err)
              result = { success: false, message: 'Error al editar usuario' }
            }
            break
          
          case 'abrirCrearRol':
            try {
              const uiContext = useUIContextStore()
              
              if (uiContext.currentModule !== 'users-management' && uiContext.currentModule !== 'users') {
                navigateToModule('users-management')
                await new Promise(resolve => setTimeout(resolve, 600))
                
                // 🔒 Verificar si la navegación falló por permisos
                const navError = uiContext.getAndClearNavigationError()
                if (navError) {
                  result = { success: false, message: navError.message }
                  break
                }
              }
              
              const crearRolResult = await uiContext.executeAction('abrirCrearRol', {})
              result = crearRolResult || { success: true, message: 'Modal de nuevo rol abierto' }
            } catch (err) {
              console.error('Error en abrirCrearRol:', err)
              result = { success: false, message: 'Error al abrir formulario de rol' }
            }
            break
          
          case 'buscarUsuario':
            try {
              const uiContext = useUIContextStore()
              const texto = fc.args?.texto
              
              if (!texto) {
                result = { success: false, message: 'Debes indicar qué usuario buscar' }
                break
              }
              
              if (uiContext.currentModule !== 'users-management' && uiContext.currentModule !== 'users') {
                navigateToModule('users-management')
                await new Promise(resolve => setTimeout(resolve, 600))
                
                // 🔒 Verificar si la navegación falló por permisos
                const navError = uiContext.getAndClearNavigationError()
                if (navError) {
                  result = { success: false, message: navError.message }
                  break
                }
              }
              
              const buscarUserResult = await uiContext.executeAction('buscarUsuario', { texto })
              result = buscarUserResult || { success: true, message: `Buscando usuario: ${texto}` }
            } catch (err) {
              console.error('Error en buscarUsuario:', err)
              result = { success: false, message: 'Error al buscar usuario' }
            }
            break
          
          case 'verPermisosDisponibles':
            try {
              const uiContext = useUIContextStore()
              
              if (uiContext.currentModule !== 'users-management' && uiContext.currentModule !== 'users') {
                navigateToModule('users-management')
                await new Promise(resolve => setTimeout(resolve, 600))
                
                // 🔒 Verificar si la navegación falló por permisos
                const navError = uiContext.getAndClearNavigationError()
                if (navError) {
                  result = { success: false, message: navError.message }
                  break
                }
              }
              
              const permisosResult = await uiContext.executeAction('verPermisosDisponibles', {})
              result = permisosResult || { success: true, message: 'Mostrando permisos disponibles' }
            } catch (err) {
              console.error('Error en verPermisosDisponibles:', err)
              result = { success: false, message: 'Error al obtener permisos' }
            }
            break
          
          case 'cambiarPestanaUsuarios':
            try {
              const uiContext = useUIContextStore()
              const pestana = fc.args?.pestana
              
              if (uiContext.currentModule !== 'users-management' && uiContext.currentModule !== 'users') {
                navigateToModule('users-management')
                await new Promise(resolve => setTimeout(resolve, 600))
                
                // 🔒 Verificar si la navegación falló por permisos
                const navError = uiContext.getAndClearNavigationError()
                if (navError) {
                  result = { success: false, message: navError.message }
                  break
                }
              }
              
              const cambiarTabResult = await uiContext.executeAction('cambiarPestanaUsuarios', { pestana })
              result = cambiarTabResult || { success: true, message: `Cambiando a pestaña ${pestana}` }
            } catch (err) {
              console.error('Error en cambiarPestanaUsuarios:', err)
              result = { success: false, message: 'Error al cambiar pestaña' }
            }
            break
          
          // ========================================
          // 💼 CONTROL DE CAJAS - Handlers GLOBALES
          // ========================================
          
          case 'consultarRendimientoEmpleado':
            try {
              const uiContext = useUIContextStore()
              const busqueda = fc.args?.busqueda
              
              if (!busqueda) {
                result = { success: false, message: 'Dime el nombre del empleado que quieres consultar' }
                break
              }
              
              // Esta herramienta es GLOBAL - funciona desde cualquier módulo
              // Primero intentamos obtener datos del contexto actual
              let rendimientoResult = await uiContext.executeAction('consultarRendimientoEmpleado', { busqueda })
              
              // Si no hay acción registrada (aún no cargó cash-admin), navegar y reintentar UNA vez
              if (!rendimientoResult) {
                navigateToModule('cash-admin')
                await new Promise(resolve => setTimeout(resolve, 1000))
                rendimientoResult = await uiContext.executeAction('consultarRendimientoEmpleado', { busqueda })
              }
              
              // Manejar resultado
              if (rendimientoResult?.success && rendimientoResult?.datos) {
                const r = rendimientoResult.datos
                let mensaje = `📊 Rendimiento de ${r.nombre}:\n`
                
                if (r.sesionActiva) {
                  mensaje += `✅ Sesión activa: ${r.sesionActiva.duracion}, ventas: $${r.sesionActiva.ventas.toLocaleString()}\n`
                } else {
                  mensaje += `❌ Sin sesión activa en este momento\n`
                }
                
                mensaje += `📅 Hoy: ${r.resumenHoy.sesiones} sesiones, $${r.resumenHoy.ventas.toLocaleString()} en ventas, ${r.resumenHoy.horasTrabajadas}h trabajadas`
                
                // Agregar información de gastos si los hay
                if (r.resumenHoy.cantidadGastos > 0) {
                  mensaje += `\n💸 Gastos: ${r.resumenHoy.cantidadGastos} gastos por $${r.resumenHoy.gastos.toLocaleString()}`
                }
                
                // Agregar información de devoluciones si las hay
                if (r.resumenHoy.cantidadDevoluciones > 0) {
                  mensaje += `\n↩️ Devoluciones: ${r.resumenHoy.cantidadDevoluciones} devoluciones por $${r.resumenHoy.devoluciones.toLocaleString()}`
                }
                
                result = { 
                  success: true, 
                  message: mensaje,
                  datos: r
                }
              } else if (rendimientoResult?.sugerencias?.length > 0) {
                // Tiene sugerencias de nombres similares
                result = { 
                  success: false, 
                  message: rendimientoResult.message || `No encontré "${busqueda}". ¿Quisiste decir: ${rendimientoResult.sugerencias.join(', ')}?`
                }
              } else {
                // No encontrado y sin sugerencias
                result = { 
                  success: false, 
                  message: rendimientoResult?.message || `No encontré sesiones de caja para "${busqueda}". Este empleado puede que no haya abierto caja recientemente.`
                }
              }
            } catch (err) {
              console.error('Error en consultarRendimientoEmpleado:', err)
              result = { success: false, message: 'Error al consultar rendimiento del empleado' }
            }
            break
          
          case 'obtenerResumenCajas':
            try {
              const uiContext = useUIContextStore()
              
              // Intentar obtener del contexto actual
              const resumenResult = await uiContext.executeAction('obtenerResumenCajas', {})
              
              if (resumenResult?.success && resumenResult?.datos) {
                const d = resumenResult.datos
                let mensaje = `💼 Resumen de Cajas:\n`
                mensaje += `• ${d.sesionesActivas} sesiones activas\n`
                mensaje += `• Empleados: ${d.empleadosActivos?.join(', ') || 'Ninguno'}\n`
                mensaje += `• Total en cajas: $${d.totalEnCajas?.toLocaleString() || 0}\n`
                mensaje += `• Ventas hoy: $${d.ventasHoy?.toLocaleString() || 0}`
                
                if (d.alertas && d.alertas.length > 0) {
                  mensaje += `\n⚠️ ${d.alertas.length} alerta(s) activas`
                }
                
                result = { success: true, message: mensaje, datos: d }
              } else {
                // Navegar al módulo si no hay datos
                navigateToModule('cash-admin')
                await new Promise(resolve => setTimeout(resolve, 800))
                
                const retryResult = await uiContext.executeAction('obtenerResumenCajas', {})
                if (retryResult?.success) {
                  result = retryResult
                } else {
                  result = { success: false, message: 'No pude obtener el resumen de cajas. ¿Quieres que te lleve al módulo?' }
                }
              }
            } catch (err) {
              console.error('Error en obtenerResumenCajas:', err)
              result = { success: false, message: 'Error al obtener resumen de cajas' }
            }
            break
          
          case 'obtenerAlertasEmpleados':
            try {
              const uiContext = useUIContextStore()
              
              const alertasResult = await uiContext.executeAction('obtenerAlertasEmpleados', {})
              
              if (alertasResult?.success) {
                if (alertasResult.alertas && alertasResult.alertas.length > 0) {
                  let mensaje = `⚠️ Alertas de empleados:\n`
                  alertasResult.alertas.forEach(a => {
                    mensaje += `• ${a.mensaje}\n`
                  })
                  result = { success: true, message: mensaje, alertas: alertasResult.alertas }
                } else {
                  result = { success: true, message: '✅ Todo en orden. No hay alertas con los empleados.', alertas: [] }
                }
              } else {
                // Navegar al módulo si no hay datos
                navigateToModule('cash-admin')
                await new Promise(resolve => setTimeout(resolve, 800))
                
                const retryResult = await uiContext.executeAction('obtenerAlertasEmpleados', {})
                if (retryResult?.success) {
                  result = retryResult
                } else {
                  result = { success: true, message: '✅ No pude verificar alertas, pero parece que todo está bien.', alertas: [] }
                }
              }
            } catch (err) {
              console.error('Error en obtenerAlertasEmpleados:', err)
              result = { success: false, message: 'Error al obtener alertas' }
            }
            break
          
          case 'verDetalleSesionCaja':
            try {
              const uiContext = useUIContextStore()
              const idSesion = fc.args?.idSesion
              const busqueda = fc.args?.busqueda
              
              // Verificar que estamos en control de cajas
              if (uiContext.currentModule !== 'cash-admin') {
                navigateToModule('cash-admin')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const detalleResult = await uiContext.executeAction('verDetalleSesion', { idSesion, busqueda })
              result = detalleResult || { success: true, message: 'Mostrando detalles de la sesión' }
            } catch (err) {
              console.error('Error en verDetalleSesionCaja:', err)
              result = { success: false, message: 'Error al ver detalles de sesión' }
            }
            break
          
          case 'filtrarSesionesCaja':
            try {
              const uiContext = useUIContextStore()
              const estado = fc.args?.estado
              
              if (uiContext.currentModule !== 'cash-admin') {
                navigateToModule('cash-admin')
                await new Promise(resolve => setTimeout(resolve, 600))
              }
              
              const filtrarResult = await uiContext.executeAction('filtrarSesionesPorEstado', { estado })
              result = filtrarResult || { success: true, message: `Filtrando sesiones ${estado}` }
            } catch (err) {
              console.error('Error en filtrarSesionesCaja:', err)
              result = { success: false, message: 'Error al filtrar sesiones' }
            }
            break
          
          case 'obtenerMiCajaVsEmpleados':
            try {
              const uiContext = useUIContextStore()
              
              // Intentar obtener del contexto actual
              const miCajaResult = await uiContext.executeAction('obtenerMiCajaVsEmpleados', {})
              
              if (miCajaResult?.success) {
                result = miCajaResult
              } else {
                // Navegar al módulo si no hay datos
                navigateToModule('cash-admin')
                await new Promise(resolve => setTimeout(resolve, 800))
                
                const retryResult = await uiContext.executeAction('obtenerMiCajaVsEmpleados', {})
                if (retryResult?.success) {
                  result = retryResult
                } else {
                  result = { success: false, message: 'No pude obtener la información de cajas. ¿Quieres que te lleve al módulo?' }
                }
              }
            } catch (err) {
              console.error('Error en obtenerMiCajaVsEmpleados:', err)
              result = { success: false, message: 'Error al comparar cajas' }
            }
            break
          
          // ========================================
          // 💸 GASTOS OPERATIVOS - Handlers GLOBALES
          // ========================================
          
          case 'registrarGastoVoz':
            try {
              const uiContext = useUIContextStore()
              const { descripcion, monto, categoria, fuente, proveedor, metodo_pago } = fc.args || {}
              
              if (!descripcion) {
                result = { 
                  success: false, 
                  message: 'Por favor, dime qué compraste o pagaste. Por ejemplo: "compré papelería" o "pagué la luz".'
                }
                break
              }
              
              // Mapear categoría amigable a ID
              const categoriaMap = {
                'servicios_publicos': 'Servicios Públicos',
                'nomina': 'Nómina y Salarios',
                'mantenimiento': 'Mantenimiento',
                'suministros': 'Suministros y Materiales',
                'arriendo': 'Arriendo',
                'transporte': 'Transporte',
                'otros': 'Otros Gastos'
              }
              
              // Intentar obtener datos del contexto de gastos
              let gastoResult = await uiContext.executeAction('registrarGastoVoz', {
                descripcion,
                monto,
                categoria: categoria ? categoriaMap[categoria] : null,
                fuente: fuente || 'general',
                proveedor,
                metodo_pago: metodo_pago || 'efectivo'
              })
              
              // Si no hay acción registrada (no está en expenses), navegar primero
              if (!gastoResult) {
                navigateToModule('expenses')
                await new Promise(resolve => setTimeout(resolve, 1000))
                
                gastoResult = await uiContext.executeAction('registrarGastoVoz', {
                  descripcion,
                  monto,
                  categoria: categoria ? categoriaMap[categoria] : null,
                  fuente: fuente || 'general',
                  proveedor,
                  metodo_pago: metodo_pago || 'efectivo'
                })
              }
              
              if (gastoResult?.success) {
                result = gastoResult
              } else if (gastoResult?.necesitaDatos) {
                // El sistema necesita más datos
                result = {
                  success: false,
                  necesitaDatos: true,
                  message: gastoResult.message,
                  datosActuales: gastoResult.datosActuales
                }
              } else {
                result = { 
                  success: false, 
                  message: gastoResult?.message || 'No pude registrar el gasto. ¿Quieres que te lleve al módulo de gastos?'
                }
              }
            } catch (err) {
              console.error('Error en registrarGastoVoz:', err)
              result = { success: false, message: 'Error al registrar el gasto' }
            }
            break
          
          case 'consultarGastos':
            try {
              const uiContext = useUIContextStore()
              const consulta = fc.args?.consulta || 'resumen'
              const periodo = fc.args?.periodo || 'mes'
              
              // Intentar obtener del contexto actual
              let gastosResult = await uiContext.executeAction('consultarGastos', { consulta, periodo })
              
              // Si no hay datos, navegar a expenses
              if (!gastosResult) {
                navigateToModule('expenses')
                await new Promise(resolve => setTimeout(resolve, 800))
                gastosResult = await uiContext.executeAction('consultarGastos', { consulta, periodo })
              }
              
              if (gastosResult?.success) {
                result = gastosResult
              } else {
                // Dar respuesta genérica si no hay datos
                result = { 
                  success: false, 
                  message: 'No pude obtener la información de gastos. ¿Quieres que te lleve al módulo de gastos operativos?'
                }
              }
            } catch (err) {
              console.error('Error en consultarGastos:', err)
              result = { success: false, message: 'Error al consultar gastos' }
            }
            break
          
          case 'verCategoriasGastos':
            try {
              const uiContext = useUIContextStore()
              
              let categoriasResult = await uiContext.executeAction('verCategoriasGastos', {})
              
              if (!categoriasResult) {
                navigateToModule('expenses')
                await new Promise(resolve => setTimeout(resolve, 600))
                categoriasResult = await uiContext.executeAction('verCategoriasGastos', {})
              }
              
              if (categoriasResult?.success) {
                result = categoriasResult
              } else {
                // Categorías por defecto
                result = {
                  success: true,
                  message: 'Las categorías de gastos disponibles son: Servicios Públicos (luz, agua, internet), Nómina y Salarios, Mantenimiento, Suministros y Materiales (papelería, limpieza), Arriendo, Transporte, y Otros Gastos.'
                }
              }
            } catch (err) {
              console.error('Error en verCategoriasGastos:', err)
              result = { 
                success: true, 
                message: 'Categorías disponibles: Servicios Públicos, Nómina, Mantenimiento, Suministros, Arriendo, Transporte, Otros Gastos.'
              }
            }
            break
          
          // ========================================
          // 📊 REPORTES - Handlers GLOBALES
          // ========================================
          
          case 'consultarReportesGenerales':
            try {
              const uiContext = useUIContextStore()
              const periodo = fc.args?.periodo || 'hoy'
              const tipoConsulta = fc.args?.tipoConsulta || 'resumen'
              
              // Mapear período a formato del API
              const periodoMap = { 'hoy': 'today', 'semana': 'week', 'mes': 'month', 'año': 'year' }
              const periodoApi = periodoMap[periodo] || 'today'
              
              // Intentar obtener del contexto actual
              let reportesResult = await uiContext.executeAction('consultarReportesGenerales', { periodo: periodoApi, tipoConsulta })
              
              // Si no hay datos, obtener directamente de la API
              if (!reportesResult) {
                try {
                  const { reportsService } = await import('@/services/reportsService.js')
                  const salesData = await reportsService.getSalesData(periodoApi)
                  
                  if (salesData.success || salesData.data) {
                    const data = salesData.data || salesData
                    const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
                    
                    let mensaje = ''
                    switch (tipoConsulta) {
                      case 'ventas':
                        mensaje = `💰 Ventas de ${periodo}: ${formatMoney(data.totalSales || 0)} en ${data.totalTransactions || 0} transacciones`
                        break
                      case 'productos':
                        const topProd = data.topProducts?.slice(0, 5) || []
                        mensaje = `🏆 Top productos de ${periodo}:\n` + topProd.map((p, i) => `${i+1}. ${p.name}: ${p.sold || 0} vendidos (${formatMoney(p.revenue || 0)})`).join('\n')
                        break
                      case 'categorias':
                        const cats = data.salesByCategory?.slice(0, 5) || []
                        mensaje = `📊 Ventas por categoría de ${periodo}:\n` + cats.map(c => `• ${c.name}: ${formatMoney(c.sales || 0)}`).join('\n')
                        break
                      case 'tendencia':
                        const trend = data.dailySales || []
                        const promedio = trend.length > 0 ? trend.reduce((a,b) => a+b, 0) / trend.length : 0
                        mensaje = `📈 Tendencia de ${periodo}: ${trend.length} días registrados, promedio diario de ${formatMoney(promedio)}`
                        break
                      default:
                        mensaje = `📊 RESUMEN DE ${periodo.toUpperCase()}:
• Ventas totales: ${formatMoney(data.totalSales || 0)}
• Transacciones: ${data.totalTransactions || 0}
• Ticket promedio: ${formatMoney(data.averageTicket || 0)}
• Margen bruto: ${(data.grossMargin || 0).toFixed(1)}%`
                    }
                    
                    reportesResult = { success: true, message: mensaje }
                  } else {
                    reportesResult = { success: false, message: 'No hay datos de ventas para este período' }
                  }
                } catch (apiErr) {
                  console.error('Error llamando API de reportes:', apiErr)
                  reportesResult = { success: false, message: 'Error al consultar reportes' }
                }
              }
              
              result = reportesResult || { success: false, message: 'No pude obtener los reportes. ¿Quieres ir al módulo de reportes?' }
            } catch (err) {
              console.error('Error en consultarReportesGenerales:', err)
              result = { success: false, message: 'Error al consultar reportes generales' }
            }
            break
          
          case 'consultarReportesCaja':
            try {
              const uiContext = useUIContextStore()
              const periodo = fc.args?.periodo || 'hoy'
              const tipoConsulta = fc.args?.tipoConsulta || 'resumen'
              
              // Mapear período a formato del API
              const periodoMapCaja = { 'hoy': 'today', 'semana': 'week', 'mes': 'month', 'año': 'year' }
              const periodoApiCaja = periodoMapCaja[periodo] || 'today'
              
              // Intentar obtener del contexto actual
              let cajaResult = await uiContext.executeAction('consultarReportesCaja', { periodo: periodoApiCaja, tipoConsulta })
              
              // Si no hay datos, obtener directamente de la API
              if (!cajaResult) {
                try {
                  const { cashReportsService } = await import('@/services/cashReportsService.js')
                  const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
                  
                  let mensaje = ''
                  
                  switch (tipoConsulta) {
                    case 'mejor_cajero':
                      const cashierData = await cashReportsService.getCashierComparison(periodoApiCaja)
                      if (cashierData.success && cashierData.data?.length > 0) {
                        const mejor = cashierData.data[0]
                        mensaje = `🏆 Mejor cajero de ${periodo}: ${mejor.name} con ${formatMoney(mejor.total_sales || 0)} en ${mejor.transactions || 0} transacciones`
                      } else {
                        mensaje = 'No hay datos de cajeros para este período'
                      }
                      break
                      
                    case 'comparativa_cajeros':
                      const compData = await cashReportsService.getCashierComparison(periodoApiCaja)
                      if (compData.success && compData.data?.length > 0) {
                        mensaje = `👥 Comparativa de cajeros (${periodo}):\n` + 
                          compData.data.slice(0, 5).map((c, i) => 
                            `${i+1}. ${c.name}: ${formatMoney(c.total_sales || 0)} (${c.transactions || 0} trans.)`
                          ).join('\n')
                      } else {
                        mensaje = 'No hay datos de comparativa de cajeros'
                      }
                      break
                      
                    case 'top_sesiones':
                      const topData = await cashReportsService.getTopSessions(periodoApiCaja, null, null, 5)
                      if (topData.success && topData.data?.length > 0) {
                        mensaje = `🌟 Mejores sesiones de ${periodo}:\n` + 
                          topData.data.slice(0, 5).map((s, i) => 
                            `${i+1}. ${s.cashier_name}: ${formatMoney(s.total_sales || 0)} (${s.date || 'hoy'})`
                          ).join('\n')
                      } else {
                        mensaje = 'No hay datos de sesiones para este período'
                      }
                      break
                      
                    case 'eficiencia_hora':
                      const hourlyData = await cashReportsService.getHourlyEfficiency(periodoApiCaja)
                      if (hourlyData.success && hourlyData.data?.length > 0) {
                        const mejorHora = hourlyData.data.reduce((a, b) => (a.sales || 0) > (b.sales || 0) ? a : b)
                        mensaje = `⏰ Mejor hora de ventas: ${mejorHora.hour || 'N/A'} con ${formatMoney(mejorHora.sales || 0)}`
                      } else {
                        mensaje = 'No hay datos de eficiencia por hora'
                      }
                      break
                      
                    default:
                      const metricsData = await cashReportsService.getCashMetrics(periodoApiCaja)
                      const cashierComp = await cashReportsService.getCashierComparison(periodoApiCaja)
                      if (metricsData.success || cashierComp.success) {
                        const metrics = metricsData.data || {}
                        const cashiers = cashierComp.data || []
                        const best = cashiers[0] || { name: 'N/A', total_sales: 0 }
                        mensaje = `📊 REPORTE DE CAJAS (${periodo}):
• Sesiones activas: ${metrics.active_sessions || cashiers.length || 0}
• Total ventas: ${formatMoney(metrics.total_sales || cashiers.reduce((a,c) => a + (parseFloat(c.total_sales)||0), 0))}
• Transacciones: ${metrics.total_transactions || cashiers.reduce((a,c) => a + (parseInt(c.transactions)||0), 0)}
• Mejor cajero: ${best.name} (${formatMoney(best.total_sales || 0)})`
                      } else {
                        mensaje = 'No hay datos de cajas para este período'
                      }
                  }
                  
                  cajaResult = { success: true, message: mensaje }
                } catch (apiErr) {
                  console.error('Error llamando API de reportes de caja:', apiErr)
                  cajaResult = { success: false, message: 'Error al consultar reportes de caja' }
                }
              }
              
              result = cajaResult || { success: false, message: 'No pude obtener los reportes de caja. ¿Quieres ir al módulo?' }
            } catch (err) {
              console.error('Error en consultarReportesCaja:', err)
              result = { success: false, message: 'Error al consultar reportes de caja' }
            }
            break
          
          case 'obtenerMejorCajero':
            try {
              const periodo = fc.args?.periodo || 'hoy'
              const periodoMapMejor = { 'hoy': 'today', 'semana': 'week', 'mes': 'month' }
              const periodoApiMejor = periodoMapMejor[periodo] || 'today'
              
              const { cashReportsService } = await import('@/services/cashReportsService.js')
              const cashierData = await cashReportsService.getCashierComparison(periodoApiMejor)
              
              if (cashierData.success && cashierData.data?.length > 0) {
                const mejor = cashierData.data[0]
                const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
                result = {
                  success: true,
                  message: `🏆 El mejor cajero de ${periodo} es ${mejor.name} con ${formatMoney(mejor.total_sales || 0)} en ventas y ${mejor.transactions || 0} transacciones. ¡Excelente trabajo!`
                }
              } else {
                result = { success: false, message: `No hay datos de cajeros para ${periodo}` }
              }
            } catch (err) {
              console.error('Error en obtenerMejorCajero:', err)
              result = { success: false, message: 'Error al obtener el mejor cajero' }
            }
            break
          
          case 'obtenerTopSesiones':
            try {
              const periodo = fc.args?.periodo || 'semana'
              const limite = Math.min(fc.args?.limite || 5, 10)
              const periodoMapTop = { 'hoy': 'today', 'semana': 'week', 'mes': 'month' }
              const periodoApiTop = periodoMapTop[periodo] || 'week'
              
              const { cashReportsService } = await import('@/services/cashReportsService.js')
              const topData = await cashReportsService.getTopSessions(periodoApiTop, null, null, limite)
              
              if (topData.success && topData.data?.length > 0) {
                const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
                const lista = topData.data.slice(0, limite).map((s, i) => 
                  `${i+1}. ${s.cashier_name}: ${formatMoney(s.total_sales || 0)}`
                ).join('\n')
                
                result = {
                  success: true,
                  message: `🌟 Top ${limite} sesiones de ${periodo}:\n${lista}`
                }
              } else {
                result = { success: false, message: `No hay datos de sesiones para ${periodo}` }
              }
            } catch (err) {
              console.error('Error en obtenerTopSesiones:', err)
              result = { success: false, message: 'Error al obtener top sesiones' }
            }
            break
          
          case 'navegarAReportes':
            try {
              const tipoReporte = fc.args?.tipoReporte || 'general'
              
              if (tipoReporte === 'caja') {
                navigateToModule('reports')
                await new Promise(resolve => setTimeout(resolve, 500))
                // El módulo de reportes tiene tabs, intentar activar la de caja
                const uiContext = useUIContextStore()
                await uiContext.executeAction('cambiarAReporteCaja')
                result = { success: true, message: 'Te llevé a los reportes de caja' }
              } else {
                navigateToModule('reports')
                result = { success: true, message: 'Te llevé a los reportes generales' }
              }
            } catch (err) {
              console.error('Error en navegarAReportes:', err)
              result = { success: false, message: 'Error al navegar a reportes' }
            }
            break
          
          // ========================================
          // 🧾 VENTAS/FACTURAS - Handlers GLOBALES (⭐ CORE DEL POS)
          // ========================================
          
          case 'consultarVentasFecha':
            try {
              const fechaParam = fc.args?.fecha || 'hoy'
              const fechaFin = fc.args?.fechaFin
              const incluirDetalle = fc.args?.incluirDetalle || false
              
              const { invoicesService } = await import('@/services/invoicesService.js')
              const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
              
              // Calcular fecha objetivo
              let targetDate = new Date()
              const today = new Date()
              today.setHours(0, 0, 0, 0)
              
              if (fechaParam === 'hoy') {
                targetDate = new Date()
              } else if (fechaParam === 'ayer') {
                targetDate = new Date(Date.now() - 86400000)
              } else if (fechaParam === 'anteayer') {
                targetDate = new Date(Date.now() - 86400000 * 2)
              } else if (/^\d{4}-\d{2}-\d{2}$/.test(fechaParam)) {
                // Fecha ISO
                targetDate = new Date(fechaParam + 'T12:00:00')
              } else {
                // Intentar parsear día de la semana
                const diasSemana = { 'lunes': 1, 'martes': 2, 'miercoles': 3, 'miércoles': 3, 'jueves': 4, 'viernes': 5, 'sabado': 6, 'sábado': 6, 'domingo': 0 }
                const diaTarget = diasSemana[fechaParam.toLowerCase()]
                if (diaTarget !== undefined) {
                  const hoy = new Date()
                  const diaHoy = hoy.getDay()
                  let diff = diaHoy - diaTarget
                  if (diff <= 0) diff += 7 // Si es hoy o futuro, ir a la semana pasada
                  targetDate = new Date(Date.now() - diff * 86400000)
                }
              }
              
              targetDate.setHours(0, 0, 0, 0)
              const targetDateStr = targetDate.toISOString().split('T')[0]
              const targetEndStr = fechaFin || targetDateStr
              
              // Obtener todas las facturas y filtrar
              const invoicesResponse = await invoicesService.getInvoices()
              
              if (invoicesResponse.success && invoicesResponse.data) {
                const allInvoices = invoicesResponse.data
                
                // Filtrar facturas PAGADAS de la fecha objetivo
                const facturasDelDia = allInvoices.filter(inv => {
                  const invDate = inv.date || inv.created_at
                  if (!invDate) return false
                  const invDateStr = invDate.split('T')[0]
                  const isPaid = inv.status === 'paid' || inv.status === 'Pagada' || inv.status === 'pagada'
                  const isInvoice = inv.type !== 'quote' && inv.type !== 'Cotización'
                  return invDateStr >= targetDateStr && invDateStr <= targetEndStr && isPaid && isInvoice
                })
                
                // Calcular totales
                const totalVentas = facturasDelDia.reduce((sum, inv) => sum + parseFloat(inv.total || 0), 0)
                const numFacturas = facturasDelDia.length
                const ticketPromedio = numFacturas > 0 ? totalVentas / numFacturas : 0
                
                // Formatear fecha amigable
                const fechaAmigable = targetDate.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long' })
                
                let mensaje = ''
                
                // Si no hubo ventas
                if (numFacturas === 0) {
                  mensaje = `No hubo ventas registradas el ${fechaAmigable}. ¿Quieres consultar otro día?`
                } else {
                  // Respuesta concisa sin desglose por vendedor (solo si lo piden)
                  mensaje = `El ${fechaAmigable} vendiste ${formatMoney(totalVentas)} en ${numFacturas} facturas. Ticket promedio: ${formatMoney(ticketPromedio)}.`
                  
                  // Agregar contexto positivo si fue un buen día
                  if (totalVentas > 500000) {
                    mensaje += ` ¡Buen día de ventas!`
                  }
                }
                
                result = { success: true, message: mensaje }
              } else {
                result = { success: false, message: 'No pude obtener las facturas. Intenta de nuevo.' }
              }
            } catch (err) {
              console.error('Error en consultarVentasFecha:', err)
              result = { success: false, message: 'Error al consultar ventas de la fecha' }
            }
            break
          
          case 'ventasPorEmpleado':
            try {
              const empleadoNombre = fc.args?.empleado
              const fechaParam = fc.args?.fecha || fc.args?.periodo || 'hoy'
              
              if (!empleadoNombre) {
                result = { success: false, message: '¿De qué empleado quieres saber las ventas?' }
                break
              }
              
              const { invoicesService } = await import('@/services/invoicesService.js')
              const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
              
              // Calcular rango de fechas
              let startDate = new Date()
              let endDate = new Date()
              startDate.setHours(0, 0, 0, 0)
              endDate.setHours(23, 59, 59, 999)
              
              if (fechaParam === 'ayer') {
                startDate = new Date(Date.now() - 86400000)
                endDate = new Date(Date.now() - 86400000)
                startDate.setHours(0, 0, 0, 0)
                endDate.setHours(23, 59, 59, 999)
              } else if (fechaParam === 'semana' || fechaParam === 'week') {
                startDate = new Date(Date.now() - 7 * 86400000)
              } else if (fechaParam === 'mes' || fechaParam === 'month') {
                startDate = new Date(Date.now() - 30 * 86400000)
              } else if (/^\d{4}-\d{2}-\d{2}$/.test(fechaParam)) {
                startDate = new Date(fechaParam + 'T00:00:00')
                endDate = new Date(fechaParam + 'T23:59:59')
              }
              
              const startStr = startDate.toISOString().split('T')[0]
              const endStr = endDate.toISOString().split('T')[0]
              
              // Obtener facturas
              const invoicesResponse = await invoicesService.getInvoices()
              
              if (invoicesResponse.success && invoicesResponse.data) {
                const allInvoices = invoicesResponse.data
                const nombreBusqueda = empleadoNombre.toLowerCase()
                
                // Filtrar facturas del empleado
                const facturasEmpleado = allInvoices.filter(inv => {
                  const vendedor = (inv.seller_name || '').toLowerCase()
                  const invDate = (inv.date || inv.created_at || '').split('T')[0]
                  const isPaid = inv.status === 'paid' || inv.status === 'Pagada' || inv.status === 'pagada'
                  const isInvoice = inv.type !== 'quote' && inv.type !== 'Cotización'
                  const matchVendedor = vendedor.includes(nombreBusqueda) || nombreBusqueda.includes(vendedor.split(' ')[0])
                  return invDate >= startStr && invDate <= endStr && isPaid && isInvoice && matchVendedor
                })
                
                // Calcular totales
                const totalVentas = facturasEmpleado.reduce((sum, inv) => sum + parseFloat(inv.total || 0), 0)
                const numFacturas = facturasEmpleado.length
                const ticketPromedio = numFacturas > 0 ? totalVentas / numFacturas : 0
                
                // Encontrar nombre completo del vendedor
                const nombreReal = facturasEmpleado[0]?.seller_name || empleadoNombre
                
                // Formatear período
                let periodoText = 'hoy'
                if (fechaParam === 'ayer') periodoText = 'ayer'
                else if (fechaParam === 'semana' || fechaParam === 'week') periodoText = 'esta semana'
                else if (fechaParam === 'mes' || fechaParam === 'month') periodoText = 'este mes'
                else if (/^\d{4}-\d{2}-\d{2}$/.test(fechaParam)) {
                  periodoText = `el ${new Date(fechaParam + 'T12:00:00').toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long' })}`
                }
                
                if (numFacturas > 0) {
                  let mensaje = `💼 VENTAS DE ${nombreReal.toUpperCase()} (${periodoText}):\n`
                  mensaje += `• Total vendido: ${formatMoney(totalVentas)}\n`
                  mensaje += `• Facturas: ${numFacturas}\n`
                  mensaje += `• Ticket promedio: ${formatMoney(ticketPromedio)}`
                  
                  // Evaluación del rendimiento
                  if (totalVentas > 500000) {
                    mensaje += `\n\n🏆 ¡Excelente trabajo de ${nombreReal.split(' ')[0]}!`
                  } else if (totalVentas > 200000) {
                    mensaje += `\n\n✅ Buen rendimiento de ${nombreReal.split(' ')[0]}.`
                  }
                  
                  result = { success: true, message: mensaje }
                } else {
                  // Buscar si el empleado existe
                  const todosVendedores = [...new Set(allInvoices.map(i => i.seller_name).filter(Boolean))]
                  const sugerencias = todosVendedores
                    .filter(v => v.toLowerCase().includes(nombreBusqueda[0]) || nombreBusqueda.includes(v.split(' ')[0].toLowerCase()))
                    .slice(0, 3)
                  
                  let mensaje = `No encontré ventas de "${empleadoNombre}" ${periodoText}.`
                  if (sugerencias.length > 0) {
                    mensaje += ` ¿Quisiste decir: ${sugerencias.join(', ')}?`
                  }
                  result = { success: true, message: mensaje }
                }
              } else {
                result = { success: false, message: 'No pude obtener las facturas' }
              }
            } catch (err) {
              console.error('Error en ventasPorEmpleado:', err)
              result = { success: false, message: 'Error al consultar ventas del empleado' }
            }
            break
          
          case 'buscarFactura':
            try {
              const busqueda = fc.args?.busqueda
              const tipo = fc.args?.tipo || 'todos'
              const estado = fc.args?.estado || 'todos'
              
              if (!busqueda) {
                result = { success: false, message: '¿Qué factura quieres buscar? Dame el número o nombre del cliente.' }
                break
              }
              
              const { invoicesService } = await import('@/services/invoicesService.js')
              const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
              
              const invoicesResponse = await invoicesService.getInvoices()
              
              if (invoicesResponse.success && invoicesResponse.data) {
                const allInvoices = invoicesResponse.data
                const busquedaLower = busqueda.toLowerCase()
                
                // Buscar coincidencias
                const coincidencias = allInvoices.filter(inv => {
                  const numero = (inv.invoice_number || inv.number || `FV-${inv.id}`).toLowerCase()
                  const cliente = (inv.customer_name || '').toLowerCase()
                  const vendedor = (inv.seller_name || '').toLowerCase()
                  
                  const matchBusqueda = numero.includes(busquedaLower) || 
                                        cliente.includes(busquedaLower) || 
                                        busquedaLower.includes(numero.replace('fv-', ''))
                  
                  // Filtros adicionales
                  let matchTipo = true
                  if (tipo === 'factura') matchTipo = inv.type !== 'quote' && inv.type !== 'Cotización'
                  else if (tipo === 'cotizacion') matchTipo = inv.type === 'quote' || inv.type === 'Cotización'
                  
                  let matchEstado = true
                  if (estado === 'pagada') matchEstado = inv.status === 'paid' || inv.status === 'Pagada'
                  else if (estado === 'pendiente') matchEstado = inv.status === 'pending' || inv.status === 'Pendiente'
                  else if (estado === 'anulada') matchEstado = inv.status === 'cancelled' || inv.status === 'Anulada'
                  
                  return matchBusqueda && matchTipo && matchEstado
                })
                
                if (coincidencias.length === 0) {
                  result = { success: true, message: `No encontré facturas que coincidan con "${busqueda}".` }
                } else if (coincidencias.length === 1) {
                  const fac = coincidencias[0]
                  const fecha = new Date(fac.date).toLocaleDateString('es-CO', { day: 'numeric', month: 'short' })
                  result = { 
                    success: true, 
                    message: `📄 Encontré la factura ${fac.invoice_number || fac.number || `FV-${fac.id}`}:\n• Cliente: ${fac.customer_name}\n• Total: ${formatMoney(fac.total)}\n• Fecha: ${fecha}\n• Estado: ${fac.status}\n• Vendedor: ${fac.seller_name || 'N/A'}`,
                    data: { facturaId: fac.id }
                  }
                } else {
                  const lista = coincidencias.slice(0, 5).map(fac => {
                    const numero = fac.invoice_number || fac.number || `FV-${fac.id}`
                    return `• ${numero}: ${fac.customer_name} - ${formatMoney(fac.total)} (${fac.status})`
                  }).join('\n')
                  result = { 
                    success: true, 
                    message: `📋 Encontré ${coincidencias.length} facturas:\n${lista}\n\n¿Cuál necesitas?` 
                  }
                }
              } else {
                result = { success: false, message: 'No pude obtener las facturas' }
              }
            } catch (err) {
              console.error('Error en buscarFactura:', err)
              result = { success: false, message: 'Error al buscar factura' }
            }
            break
          
          case 'detalleFactura':
            try {
              const identificador = fc.args?.identificador
              
              if (!identificador) {
                result = { success: false, message: '¿De qué factura quieres el detalle? Dame el número.' }
                break
              }
              
              const { invoicesService } = await import('@/services/invoicesService.js')
              const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
              
              const invoicesResponse = await invoicesService.getInvoices()
              
              if (invoicesResponse.success && invoicesResponse.data) {
                const idLower = identificador.toLowerCase().replace('fv-', '').replace('#', '')
                
                // Buscar factura
                const factura = invoicesResponse.data.find(inv => {
                  const numero = (inv.invoice_number || inv.number || '').toLowerCase().replace('fv-', '')
                  return numero === idLower || inv.id.toString() === idLower
                })
                
                if (factura) {
                  const fecha = new Date(factura.date).toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long' })
                  const items = factura.items || []
                  
                  let mensaje = `📄 DETALLE DE FACTURA ${factura.invoice_number || factura.number || `FV-${factura.id}`}\n\n`
                  mensaje += `👤 Cliente: ${factura.customer_name}\n`
                  mensaje += `📅 Fecha: ${fecha}\n`
                  mensaje += `💳 Método de pago: ${factura.payment_method || 'Efectivo'}\n`
                  mensaje += `👔 Vendedor: ${factura.seller_name || 'N/A'}\n`
                  mensaje += `📋 Estado: ${factura.status}\n\n`
                  
                  if (items.length > 0) {
                    mensaje += `🛒 PRODUCTOS (${items.length}):\n`
                    items.slice(0, 5).forEach(item => {
                      const nombre = item.product_name || item.name || 'Producto'
                      const cant = item.quantity || 1
                      const precio = parseFloat(item.unit_price || item.price || 0)
                      const subtotal = cant * precio
                      mensaje += `• ${nombre} x${cant} = ${formatMoney(subtotal)}\n`
                    })
                    if (items.length > 5) {
                      mensaje += `... y ${items.length - 5} productos más\n`
                    }
                  }
                  
                  mensaje += `\n💰 TOTAL: ${formatMoney(factura.total)}`
                  
                  if (factura.discount_amount > 0) {
                    mensaje += `\n🏷️ Descuento aplicado: ${formatMoney(factura.discount_amount)}`
                  }
                  
                  result = { success: true, message: mensaje, data: { facturaId: factura.id } }
                } else {
                  result = { success: true, message: `No encontré la factura "${identificador}". Verifica el número.` }
                }
              } else {
                result = { success: false, message: 'No pude obtener la información' }
              }
            } catch (err) {
              console.error('Error en detalleFactura:', err)
              result = { success: false, message: 'Error al obtener detalle de factura' }
            }
            break
          
          case 'resumenVentasHoy':
            try {
              const { invoicesService } = await import('@/services/invoicesService.js')
              const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
              
              const invoicesResponse = await invoicesService.getInvoices()
              
              if (invoicesResponse.success && invoicesResponse.data) {
                const hoy = new Date().toISOString().split('T')[0]
                
                const facturasHoy = invoicesResponse.data.filter(inv => {
                  const invDate = (inv.date || inv.created_at || '').split('T')[0]
                  const isPaid = inv.status === 'paid' || inv.status === 'Pagada' || inv.status === 'pagada'
                  const isInvoice = inv.type !== 'quote' && inv.type !== 'Cotización'
                  return invDate === hoy && isPaid && isInvoice
                })
                
                const totalVentas = facturasHoy.reduce((sum, inv) => sum + parseFloat(inv.total || 0), 0)
                const numFacturas = facturasHoy.length
                const ticketPromedio = numFacturas > 0 ? totalVentas / numFacturas : 0
                
                // Última venta
                const ultimaVenta = facturasHoy.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0]
                
                let mensaje = `📊 HOY llevas:\n• ${formatMoney(totalVentas)} en ventas\n• ${numFacturas} facturas\n• Ticket promedio: ${formatMoney(ticketPromedio)}`
                
                if (ultimaVenta) {
                  mensaje += `\n\n🕐 Última venta: ${formatMoney(ultimaVenta.total)} a ${ultimaVenta.customer_name}`
                }
                
                if (numFacturas === 0) {
                  mensaje = `📊 Aún no hay ventas registradas hoy. ¡A vender!`
                }
                
                result = { success: true, message: mensaje }
              } else {
                result = { success: false, message: 'No pude obtener las ventas de hoy' }
              }
            } catch (err) {
              console.error('Error en resumenVentasHoy:', err)
              result = { success: false, message: 'Error al obtener resumen de hoy' }
            }
            break
          
          case 'navegarAFacturas':
            try {
              const facturaId = fc.args?.facturaId
              const busqueda = fc.args?.busqueda
              
              navigateToModule('invoices')
              
              if (facturaId || busqueda) {
                await new Promise(resolve => setTimeout(resolve, 500))
                const params = {}
                if (facturaId) params.selectId = facturaId
                if (busqueda) params.search = busqueda
                
                // Actualizar route query
                if (typeof window !== 'undefined' && window.__ROUTER__) {
                  window.__ROUTER__.push({ path: '/invoices', query: params })
                }
              }
              
              result = { success: true, message: 'Te llevé a facturas' }
            } catch (err) {
              console.error('Error en navegarAFacturas:', err)
              result = { success: false, message: 'Error al navegar a facturas' }
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
  // Función para establecer el límite máximo de duración
  const setMaxDuration = (seconds) => {
    maxDurationSeconds.value = seconds > 0 ? seconds : 0
  }

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
    
    // Control de límites
    maxDurationSeconds,
    wasAutoTerminated,
    setMaxDuration,
    
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
