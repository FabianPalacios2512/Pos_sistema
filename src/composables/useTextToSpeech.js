/**
 * 🔊 useTextToSpeech - Composable para síntesis de voz con Gemini TTS
 * 
 * Genera audio de alta calidad usando gemini-2.5-flash-tts
 * Con fallback a Web Speech API nativa si falla.
 */

import { ref, onUnmounted } from 'vue'
import { getAuthToken, getTenantId } from '@/services/api'

export function useTextToSpeech() {
  // Estado
  const isSpeaking = ref(false)
  const isGenerating = ref(false)
  const error = ref(null)
  const currentAudio = ref(null)
  
  // Cola de audio para reproducción secuencial
  const audioQueue = []
  let isProcessingQueue = false
  
  /**
   * Genera audio usando Gemini TTS (Premium)
   * @param {string} text - Texto a convertir en voz
   * @returns {Promise<Blob>} - Audio blob para reproducir
   */
  const generateVoiceResponse = async (text) => {
    if (!text || text.trim().length === 0) {
      throw new Error('Texto vacío')
    }
    
    isGenerating.value = true
    error.value = null
    
    try {
      const token = getAuthToken()
      const tenantId = getTenantId()
      
      const response = await fetch('/api/ai/text-to-speech', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'audio/mpeg',
          'Authorization': `Bearer ${token}`,
          'X-Tenant-ID': tenantId
        },
        body: JSON.stringify({
          text: text.trim(),
          voice: 'Kore', // Voz natural en español
          model: 'gemini-2.5-flash-preview-tts'
        })
      })
      
      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}))
        throw new Error(errorData.message || `Error ${response.status}`)
      }
      
      const audioBlob = await response.blob()
      return audioBlob
      
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      isGenerating.value = false
    }
  }
  
  /**
   * Reproduce un blob de audio
   * @param {Blob} audioBlob - Audio a reproducir
   * @returns {Promise<void>}
   */
  const playAudioBlob = (audioBlob) => {
    return new Promise((resolve, reject) => {
      const audioUrl = URL.createObjectURL(audioBlob)
      const audio = new Audio(audioUrl)
      
      currentAudio.value = audio
      isSpeaking.value = true
      
      audio.onended = () => {
        isSpeaking.value = false
        currentAudio.value = null
        URL.revokeObjectURL(audioUrl)
        resolve()
      }
      
      audio.onerror = (e) => {
        isSpeaking.value = false
        currentAudio.value = null
        URL.revokeObjectURL(audioUrl)
        reject(new Error('Error reproduciendo audio'))
      }
      
      audio.play().catch(reject)
    })
  }
  
  /**
   * Genera y reproduce audio para un texto (flujo completo)
   * @param {string} text - Texto a hablar
   * @param {boolean} useFallback - Usar fallback nativo si falla
   */
  const speak = async (text, useFallback = true) => {
    try {
      // Intentar con Gemini TTS Premium
      const audioBlob = await generateVoiceResponse(text)
      await playAudioBlob(audioBlob)
    } catch (err) {
      console.warn('Gemini TTS falló, usando fallback:', err.message)
      
      if (useFallback) {
        // Fallback a Web Speech API nativa
        await speakNative(text)
      } else {
        throw err
      }
    }
  }
  
  /**
   * Fallback: Síntesis de voz nativa del navegador
   * @param {string} text - Texto a hablar
   */
  const speakNative = (text) => {
    return new Promise((resolve, reject) => {
      if (!('speechSynthesis' in window)) {
        reject(new Error('Síntesis de voz no soportada'))
        return
      }
      
      // Cancelar cualquier síntesis anterior
      window.speechSynthesis.cancel()
      
      const utterance = new SpeechSynthesisUtterance(text)
      utterance.lang = 'es-ES'
      utterance.rate = 1.0
      utterance.pitch = 1.0
      
      // Buscar voz en español
      const voices = window.speechSynthesis.getVoices()
      const spanishVoice = voices.find(v => v.lang.startsWith('es'))
      if (spanishVoice) {
        utterance.voice = spanishVoice
      }
      
      isSpeaking.value = true
      
      utterance.onend = () => {
        isSpeaking.value = false
        resolve()
      }
      
      utterance.onerror = (e) => {
        isSpeaking.value = false
        reject(new Error(e.error))
      }
      
      window.speechSynthesis.speak(utterance)
    })
  }
  
  /**
   * Detener reproducción actual
   */
  const stop = () => {
    // Detener audio HTML5
    if (currentAudio.value) {
      currentAudio.value.pause()
      currentAudio.value = null
    }
    
    // Detener síntesis nativa
    if ('speechSynthesis' in window) {
      window.speechSynthesis.cancel()
    }
    
    isSpeaking.value = false
    isGenerating.value = false
  }
  
  /**
   * Agregar a cola de reproducción
   * @param {string} text - Texto a encolar
   */
  const enqueue = async (text) => {
    audioQueue.push(text)
    
    if (!isProcessingQueue) {
      processQueue()
    }
  }
  
  const processQueue = async () => {
    if (audioQueue.length === 0) {
      isProcessingQueue = false
      return
    }
    
    isProcessingQueue = true
    const text = audioQueue.shift()
    
    try {
      await speak(text)
    } catch (e) {
      console.error('Error en cola de audio:', e)
    }
    
    processQueue()
  }
  
  // Limpiar al desmontar
  onUnmounted(() => {
    stop()
    audioQueue.length = 0
  })
  
  return {
    // Estado
    isSpeaking,
    isGenerating,
    error,
    
    // Métodos principales
    speak,
    speakNative,
    stop,
    
    // Métodos auxiliares
    generateVoiceResponse,
    playAudioBlob,
    enqueue
  }
}
