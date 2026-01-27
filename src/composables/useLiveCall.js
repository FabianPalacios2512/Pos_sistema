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

REGLA IMPORTANTE - DATOS EN TIEMPO REAL:
- Para cualquier dato numérico (productos, ventas, facturas), SIEMPRE usa las herramientas
- Nunca inventes números, consulta siempre

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
                  description: 'SIEMPRE usa esta herramienta cuando el usuario diga "llévame a", "ir a", "abre", "muéstrame el módulo de", "quiero ver". También úsala para preguntas sobre ganancias, inventario, gastos, sedes, traslados - navega primero, luego consulta. Módulos disponibles: dashboard, pos, productos, clientes, facturas, devoluciones, reportes, configuracion, proveedores, categorias, stock, inventario_inteligente, sedes.',
                  parameters: {
                    type: 'OBJECT',
                    properties: {
                      modulo: { 
                        type: 'STRING', 
                        description: 'El módulo al que navegar. Usa: dashboard, pos, productos, clientes, facturas, devoluciones, reportes, configuracion, proveedores, categorias, stock, inventario_inteligente (para ganancias, valor inventario, gastos), sedes (para gestión de tiendas, bodegas y traslados)',
                        enum: ['dashboard', 'pos', 'productos', 'clientes', 'facturas', 'devoluciones', 'reportes', 'configuracion', 'proveedores', 'categorias', 'stock', 'inventario_inteligente', 'sedes', 'traslados']
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
                  description: 'Abre el formulario para crear un nuevo cliente. Usa cuando digan "crear cliente", "nuevo cliente", "agregar cliente".',
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
              'devoluciones': 'returns-management',
              'reportes': 'reports',
              'configuracion': 'settings',
              'proveedores': 'suppliers',
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
              'cartera': 'accounts-receivable'
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
