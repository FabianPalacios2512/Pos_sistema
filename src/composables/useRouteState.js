/**
 * Composable para Persistencia de Estado y Auto-refresh
 * 
 * Funcionalidades:
 * 1. Guardar y restaurar la ruta/módulo actual al hacer refresh
 * 2. Disparar auto-refresh invisible cuando se carga un módulo
 */

import { ref, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const STORAGE_KEY = 'pos_last_module'
const REFRESH_TIMESTAMP_KEY = 'pos_last_refresh'

export function useRouteState() {
  const router = useRouter()
  
  /**
   * Guarda el módulo actual en localStorage
   * @param {string} moduleName - Nombre del módulo actual
   */
  const saveCurrentModule = (moduleName) => {
    if (!moduleName) return
    
    try {
      const state = {
        module: moduleName,
        timestamp: Date.now(),
        path: router.currentRoute.value.fullPath
      }
      localStorage.setItem(STORAGE_KEY, JSON.stringify(state))
    } catch (error) {
      console.error('Error guardando módulo actual:', error)
    }
  }
  
  /**
   * Restaura el último módulo guardado
   * @returns {string|null} - Nombre del módulo o null si no hay nada guardado
   */
  const restoreLastModule = () => {
    try {
      const saved = localStorage.getItem(STORAGE_KEY)
      if (!saved) return null
      
      const state = JSON.parse(saved)
      const timeSinceLastSave = Date.now() - state.timestamp
      
      // Si han pasado más de 24 horas, no restaurar (sesión expiró)
      if (timeSinceLastSave > 24 * 60 * 60 * 1000) {
        localStorage.removeItem(STORAGE_KEY)
        return null
      }
      
      return state.module
    } catch (error) {
      console.error('Error restaurando módulo:', error)
      return null
    }
  }
  
  /**
   * Limpia el estado guardado
   */
  const clearSavedModule = () => {
    localStorage.removeItem(STORAGE_KEY)
  }
  
  /**
   * Marca que se hizo un refresh
   */
  const markRefresh = () => {
    localStorage.setItem(REFRESH_TIMESTAMP_KEY, Date.now().toString())
  }
  
  /**
   * Verifica si la página fue recién refrescada (menos de 2 segundos)
   * @returns {boolean}
   */
  const wasRecentlyRefreshed = () => {
    try {
      const lastRefresh = localStorage.getItem(REFRESH_TIMESTAMP_KEY)
      if (!lastRefresh) return false
      
      const timeSinceRefresh = Date.now() - parseInt(lastRefresh)
      return timeSinceRefresh < 2000 // 2 segundos
    } catch {
      return false
    }
  }
  
  return {
    saveCurrentModule,
    restoreLastModule,
    clearSavedModule,
    markRefresh,
    wasRecentlyRefreshed
  }
}

/**
 * Hook para Auto-refresh de Datos
 * 
 * Dispara una función de carga cuando:
 * 1. El componente se monta por primera vez
 * 2. El componente se activa después de estar inactivo
 * 
 * @param {Function} loadFunction - Función async que carga los datos
 * @param {Object} options - Opciones de configuración
 * @param {boolean} options.autoLoad - Auto-cargar al montar (default: true)
 * @param {boolean} options.silent - Cargar sin mostrar loading visual (default: true para refresh)
 */
export function useAutoRefresh(loadFunction, options = {}) {
  const { autoLoad = true, silent = true } = options
  
  const isLoading = ref(false)
  const lastRefreshTime = ref(null)
  
  /**
   * Ejecuta la función de carga con manejo de errores
   */
  const refresh = async (forceSilent = silent) => {
    if (isLoading.value) return
    
    try {
      isLoading.value = !forceSilent
      await loadFunction()
      lastRefreshTime.value = Date.now()
    } catch (error) {
      console.error('Error en auto-refresh:', error)
    } finally {
      isLoading.value = false
    }
  }
  
  /**
   * Refresh silencioso (sin indicadores visuales)
   */
  const silentRefresh = () => refresh(true)
  
  /**
   * Refresh con indicador visual
   */
  const visibleRefresh = () => refresh(false)
  
  // Auto-cargar al montar si está habilitado
  if (autoLoad) {
    onMounted(() => {
      silentRefresh()
    })
  }
  
  return {
    isLoading,
    lastRefreshTime,
    refresh,
    silentRefresh,
    visibleRefresh
  }
}
