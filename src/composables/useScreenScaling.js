/**
 * useScreenScaling v5 - ULTRA LIGERO
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
// Zoom base de compensación (sin zoom del usuario)
const baseZoom = ref(1)

// Flag para evitar múltiples inicializaciones
let isInitialized = false

/**
 * Detectar si estamos en un dispositivo móvil
 */
function isMobileDevice() {
  // Verificar por user agent
  const userAgent = navigator.userAgent || navigator.vendor || window.opera
  const mobileRegex = /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini|mobile|tablet/i
  
  // Verificar por touch y tamaño de pantalla
  const hasTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0
  const isSmallScreen = window.innerWidth <= 1024
  
  return mobileRegex.test(userAgent.toLowerCase()) || (hasTouch && isSmallScreen)
}

/**
 * Detectar si realmente necesitamos compensar
 * Solo para Windows con 125% o más (devicePixelRatio >= 1.2)
 * NUNCA en dispositivos móviles
 */
function needsCompensation() {
  // NUNCA aplicar en móviles - tienen su propio manejo de DPI
  if (isMobileDevice()) {
    return false
  }
  
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
    baseZoom.value = 1
    isCompensating.value = false
    
    // Limpiar cualquier estilo residual
    html.style.zoom = ''
    html.style.removeProperty('--inv-scale')
    html.style.removeProperty('--base-zoom')
    body.classList.remove('screen-compensated')
    return
  }
  
  // Sí necesita compensación (Windows 125%+)
  const zoom = getZoomValue(ratio)
  baseZoom.value = zoom
  appliedZoom.value = zoom
  isCompensating.value = true
  
  // Aplicar solo el zoom CSS - el navegador maneja el resto
  html.style.zoom = String(zoom)
  html.style.setProperty('--inv-scale', String(1 / zoom))
  html.style.setProperty('--base-zoom', String(zoom))
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
    baseZoom,
    isCompensating,
    isScaled
  }
}

/**
 * Aplicar zoom del usuario SOBRE el zoom base de compensación.
 * @param {number} userZoomPercent - Zoom del usuario en porcentaje (e.g. 100, 90, 110)
 */
export function applyUserZoom(userZoomPercent) {
  const userFactor = userZoomPercent / 100
  const finalZoom = baseZoom.value * userFactor
  
  appliedZoom.value = finalZoom
  
  const html = document.documentElement
  html.style.zoom = String(finalZoom)
  html.style.setProperty('--inv-scale', String(1 / finalZoom))
}

/**
 * Obtener el zoom base de compensación actual (para lectura externa)
 */
export function getBaseZoom() {
  return baseZoom.value
}

/**
 * Inicialización global - llamar UNA vez desde main.js
 */
export function initScreenScaling() {
  if (isInitialized) return
  
  // Aplicar compensación base inmediatamente
  applyCompensation()
  isInitialized = true
  
  // Aplicar zoom del usuario guardado sobre la compensación base
  const savedZoom = parseFloat(localStorage.getItem('pos_zoom') || '100')
  if (savedZoom !== 100) {
    applyUserZoom(savedZoom)
  }
  
  // Solo re-aplicar si el documento aún está cargando
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      applyCompensation()
      if (savedZoom !== 100) {
        applyUserZoom(savedZoom)
      }
    }, { once: true })
  }
}

export default useScreenScaling
