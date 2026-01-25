/**
 * 🖥️ useScreenScaling v5 - ULTRA LIGERO
 * 
 * Solo actúa cuando Windows tiene escalado 125%+ (devicePixelRatio > 1.2)
 * Completamente transparente cuando no hay escalado.
 * 
 * OPTIMIZADO PARA RENDIMIENTO:
 * - Sin listeners de resize innecesarios
 * - Sin manipulación de DOM excesiva
 * - Solo aplica zoom CSS cuando es necesario
 */

import { ref, onMounted, computed } from 'vue'

// Estado global - solo lectura
const deviceScale = ref(1)
const appliedZoom = ref(1)
const isCompensating = ref(false)

// Flag para evitar múltiples inicializaciones
let isInitialized = false

/**
 * Detectar si realmente necesitamos compensar
 * Solo para Windows con 125% o más (devicePixelRatio >= 1.2)
 */
function needsCompensation() {
  const ratio = window.devicePixelRatio || 1
  // Solo compensar si ratio es 1.2 o mayor (Windows 125%+)
  return ratio >= 1.2
}

/**
 * Calcular zoom de compensación
 */
function getZoomValue(ratio) {
  // Windows 125% (1.25) → zoom 0.8
  // Windows 150% (1.5) → zoom 0.667
  return Math.max(0.65, 1 / ratio)
}

/**
 * Aplicar compensación SOLO si es necesario
 */
function applyCompensation() {
  const ratio = window.devicePixelRatio || 1
  deviceScale.value = ratio
  
  const html = document.documentElement
  const body = document.body
  
  // Si NO necesita compensación, asegurar que todo esté limpio y salir
  if (!needsCompensation()) {
    appliedZoom.value = 1
    isCompensating.value = false
    
    // Limpiar cualquier estilo residual
    html.style.zoom = ''
    html.style.removeProperty('--inv-scale')
    body.classList.remove('screen-compensated')
    return
  }
  
  // Sí necesita compensación (Windows 125%+)
  const zoom = getZoomValue(ratio)
  appliedZoom.value = zoom
  isCompensating.value = true
  
  // Aplicar solo el zoom CSS - el navegador maneja el resto
  html.style.zoom = String(zoom)
  html.style.setProperty('--inv-scale', String(1 / zoom))
  body.classList.add('screen-compensated')
}

/**
 * Composable para componentes Vue
 */
export function useScreenScaling() {
  const isScaled = computed(() => deviceScale.value >= 1.2)
  
  onMounted(() => {
    if (!isInitialized) {
      applyCompensation()
      isInitialized = true
    }
  })
  
  return {
    deviceScale,
    appliedZoom,
    isCompensating,
    isScaled
  }
}

/**
 * Inicialización global - llamar UNA vez desde main.js
 */
export function initScreenScaling() {
  if (isInitialized) return
  
  // Aplicar inmediatamente
  applyCompensation()
  isInitialized = true
  
  // Solo re-aplicar si el documento aún está cargando
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applyCompensation, { once: true })
  }
}

export default useScreenScaling
