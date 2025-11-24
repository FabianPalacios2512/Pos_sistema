import { computed } from 'vue'
import authService from '../services/authService.js'

/**
 * Composable para verificar permisos del usuario actual
 */
export function usePermissions() {
  
  // Obtener usuario actual con su rol y permisos
  const currentUser = computed(() => {
    return authService.getUser()
  })
  
  // Obtener permisos del rol del usuario
  const userPermissions = computed(() => {
    if (!currentUser.value || !currentUser.value.role) {
      return []
    }
    return currentUser.value.role.permissions || []
  })
  
  /**
   * Verifica si el usuario tiene un permiso específico
   * @param {string} permission - El permiso a verificar (ej: 'products.view')
   * @returns {boolean}
   */
  const hasPermission = (permission) => {
    if (!currentUser.value || !currentUser.value.role) {
      return false
    }
    
    // Si el usuario tiene el permiso especial 'ALL' o 'admin', tiene todos los permisos
    if (userPermissions.value.includes('ALL') || userPermissions.value.includes('admin')) {
      return true
    }
    
    // Verificar si tiene el permiso específico
    return userPermissions.value.includes(permission)
  }
  
  /**
   * Verifica si el usuario tiene acceso a un módulo completo
   * @param {string} module - El módulo a verificar (ej: 'products', 'customers')
   * @returns {boolean}
   */
  const hasModuleAccess = (module) => {
    if (!currentUser.value || !currentUser.value.role) {
      return false
    }
    
    // Si tiene permiso ALL o admin
    if (userPermissions.value.includes('ALL') || userPermissions.value.includes('admin')) {
      return true
    }
    
    // Verificar si tiene al menos un permiso del módulo (ej: 'products.view')
    const hasAccess = userPermissions.value.some(permission => permission.startsWith(`${module}.`))
    
    // Log solo para módulos críticos
    if (module === 'pos' || module === 'dashboard') {
      console.log(`🔍 [hasModuleAccess] ${module}: ${hasAccess} (permisos: ${userPermissions.value.length})`)
    }
    
    return hasAccess
  }
  
  /**
   * Verifica si el usuario tiene TODOS los permisos especificados
   * @param {string[]} permissions - Array de permisos a verificar
   * @returns {boolean}
   */
  const hasAllPermissions = (permissions) => {
    return permissions.every(permission => hasPermission(permission))
  }
  
  /**
   * Verifica si el usuario tiene AL MENOS UNO de los permisos especificados
   * @param {string[]} permissions - Array de permisos a verificar
   * @returns {boolean}
   */
  const hasAnyPermission = (permissions) => {
    return permissions.some(permission => hasPermission(permission))
  }
  
  /**
   * Obtiene los módulos a los que el usuario tiene acceso
   * @returns {string[]} - Array con los nombres de los módulos accesibles
   */
  const getAccessibleModules = () => {
    if (!currentUser.value || !currentUser.value.role) {
      return []
    }
    
    // Si tiene permiso ALL o admin, tiene acceso a todos
    if (userPermissions.value.includes('ALL') || userPermissions.value.includes('admin')) {
      return [
        'dashboard', 'pos', 'invoices', 'returns', 
        'products', 'categories', 'stock', 'intelligent_inventory',
        'customers', 'suppliers', 
        'users', 'cash_register', 'reports', 'settings'
      ]
    }
    
    // Extraer módulos únicos de los permisos
    const modules = new Set()
    userPermissions.value.forEach(permission => {
      const module = permission.split('.')[0]
      modules.add(module)
    })
    
    return Array.from(modules)
  }
  
  /**
   * Verifica si el usuario es administrador
   * @returns {boolean}
   */
  const isAdmin = () => {
    if (!currentUser.value || !currentUser.value.role) {
      return false
    }
    
    const roleName = currentUser.value.role.name?.toLowerCase() || ''
    return roleName.includes('admin') || 
           userPermissions.value.includes('ALL') || 
           userPermissions.value.includes('admin')
  }
  
  return {
    currentUser,
    userPermissions,
    hasPermission,
    hasModuleAccess,
    hasAllPermissions,
    hasAnyPermission,
    getAccessibleModules,
    isAdmin
  }
}
