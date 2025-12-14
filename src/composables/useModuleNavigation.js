import { ref } from 'vue'

// Estado global para navegación de módulos
const currentModuleGlobal = ref(null)
const currentQueryGlobal = ref({})
const moduleChangeCallbacks = []

export function useModuleNavigation() {
  
  // Registrar callback para cuando cambie el módulo
  const onModuleChange = (callback) => {
    moduleChangeCallbacks.push(callback)
  }
  
  // Navegar a un módulo con query params opcionales
  const navigateToModule = (moduleName, queryParams = {}) => {
    console.log('🚀 [useModuleNavigation] Navegando a:', moduleName, 'con query:', queryParams)
    currentModuleGlobal.value = moduleName
    currentQueryGlobal.value = queryParams
    
    // Ejecutar todos los callbacks registrados con módulo y query
    moduleChangeCallbacks.forEach(cb => {
      try {
        cb(moduleName, queryParams)
      } catch (error) {
        console.error('Error en callback de navegación:', error)
      }
    })
  }
  
  return {
    currentModuleGlobal,
    currentQueryGlobal,
    navigateToModule,
    onModuleChange
  }
}
