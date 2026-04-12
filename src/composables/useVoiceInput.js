/**
 * useVoiceInput - Composable para entrada de voz con Web Speech API
 * 
 * Captura audio del micrófono y lo convierte a texto usando la API nativa del navegador.
 * Optimizado para velocidad en la interacción inicial.
 */

import { ref, computed, onUnmounted } from 'vue'

export function useVoiceInput() {
  // Estado
  const isListening = ref(false)
  const transcript = ref('')
  const interimTranscript = ref('')
  const error = ref(null)
  const isSupported = ref(false)
  
  // Instancia de reconocimiento
  let recognition = null
  
  // Verificar soporte del navegador
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
  isSupported.value = !!SpeechRecognition
  
  // Inicializar reconocimiento de voz
  const initRecognition = () => {
    if (!isSupported.value) {
      error.value = 'Tu navegador no soporta reconocimiento de voz'
      return null
    }
    
    if (recognition) return recognition
    
    recognition = new SpeechRecognition()
    
    // Configuración optimizada para español
    recognition.lang = 'es-ES'
    recognition.continuous = false // Solo una frase
    recognition.interimResults = true // Mostrar resultados parciales
    recognition.maxAlternatives = 1
    
    // Eventos
    recognition.onstart = () => {
      isListening.value = true
      error.value = null
      transcript.value = ''
      interimTranscript.value = ''
    }
    
    recognition.onresult = (event) => {
      let finalTranscript = ''
      let interim = ''
      
      for (let i = event.resultIndex; i < event.results.length; i++) {
        const result = event.results[i]
        if (result.isFinal) {
          finalTranscript += result[0].transcript
        } else {
          interim += result[0].transcript
        }
      }
      
      if (finalTranscript) {
        transcript.value = finalTranscript.trim()
      }
      interimTranscript.value = interim
    }
    
    recognition.onerror = (event) => {
      isListening.value = false
      
      switch (event.error) {
        case 'no-speech':
          error.value = 'No se detectó voz. Intenta de nuevo.'
          break
        case 'audio-capture':
          error.value = 'No se encontró micrófono.'
          break
        case 'not-allowed':
          error.value = 'Permiso de micrófono denegado.'
          break
        case 'network':
          error.value = 'Error de red. Verifica tu conexión.'
          break
        default:
          error.value = `Error: ${event.error}`
      }
    }
    
    recognition.onend = () => {
      isListening.value = false
    }
    
    return recognition
  }
  
  // Iniciar escucha
  const startListening = () => {
    return new Promise((resolve, reject) => {
      const rec = initRecognition()
      
      if (!rec) {
        reject(new Error(error.value || 'No soportado'))
        return
      }
      
      // Limpiar estado anterior
      transcript.value = ''
      interimTranscript.value = ''
      error.value = null
      
      // Resolver cuando tengamos resultado final
      const originalOnResult = rec.onresult
      rec.onresult = (event) => {
        originalOnResult(event)
        
        // Si hay resultado final, resolver
        for (let i = event.resultIndex; i < event.results.length; i++) {
          if (event.results[i].isFinal) {
            setTimeout(() => {
              if (transcript.value) {
                resolve(transcript.value)
              }
            }, 100)
          }
        }
      }
      
      // Rechazar en error
      const originalOnError = rec.onerror
      rec.onerror = (event) => {
        originalOnError(event)
        reject(new Error(error.value))
      }
      
      // Rechazar si termina sin resultado
      const originalOnEnd = rec.onend
      rec.onend = () => {
        originalOnEnd()
        if (!transcript.value) {
          reject(new Error('No se capturó ningún texto'))
        }
      }
      
      try {
        rec.start()
      } catch (e) {
        // Ya está escuchando
        if (e.name === 'InvalidStateError') {
          rec.stop()
          setTimeout(() => rec.start(), 100)
        } else {
          reject(e)
        }
      }
    })
  }
  
  // Detener escucha
  const stopListening = () => {
    if (recognition && isListening.value) {
      recognition.stop()
    }
  }
  
  // Toggle escucha
  const toggleListening = async () => {
    if (isListening.value) {
      stopListening()
      return null
    } else {
      return startListening()
    }
  }
  
  // Limpiar al desmontar
  onUnmounted(() => {
    if (recognition) {
      recognition.stop()
      recognition = null
    }
  })
  
  // Estado visual combinado
  const displayText = computed(() => {
    return interimTranscript.value || transcript.value || ''
  })
  
  return {
    // Estado
    isListening,
    transcript,
    interimTranscript,
    displayText,
    error,
    isSupported,
    
    // Métodos
    startListening,
    stopListening,
    toggleListening
  }
}
