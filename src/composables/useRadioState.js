/**
 * 🎵 Composable para manejar el estado global de la radio
 * Permite compartir el estado de reproducción entre RadioWidget y AppHeader
 */

import { ref, computed } from 'vue'

// Estado global compartido (fuera de la función para persistencia entre componentes)
const isPlaying = ref(false)
const currentRadioName = ref('')
const currentRadioId = ref(null)

export function useRadioState() {
  // Métodos para actualizar el estado
  const setPlaying = (playing) => {
    isPlaying.value = playing
  }

  const setCurrentRadio = (radio) => {
    if (radio) {
      currentRadioId.value = radio.id
      currentRadioName.value = radio.name
    } else {
      currentRadioId.value = null
      currentRadioName.value = ''
    }
  }

  const stopRadio = () => {
    isPlaying.value = false
    currentRadioId.value = null
    currentRadioName.value = ''
  }

  // Estado computado - DEBE ser false si no hay radio o no está playing
  const isRadioActive = computed(() => {
    return isPlaying.value === true && currentRadioId.value !== null
  })
  
  const radioStatus = computed(() => {
    if (!currentRadioId.value) return 'idle'
    return isPlaying.value ? 'playing' : 'paused'
  })

  return {
    // Estado reactivo
    isPlaying,
    currentRadioName,
    currentRadioId,
    isRadioActive,
    radioStatus,
    
    // Métodos
    setPlaying,
    setCurrentRadio,
    stopRadio
  }
}
