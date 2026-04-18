import authService from '../services/authService.js'

// Middleware para verificar autenticación
export const requireAuth = (to, from, next) => {
  if (authService.isAuthenticated()) {
    next()
  } else {
    next('/login')
  }
}

// Middleware para verificar roles específicos
export const requireRole = (allowedRoles) => {
  return (to, from, next) => {
    if (!authService.isAuthenticated()) {
      next('/login')
      return
    }

    const user = authService.getUser()
    if (!user) {
      next('/login')
      return
    }

    // Si el usuario no tiene rol definido, asignar un rol por defecto
    if (!user.role) {
      console.warn('Usuario sin rol definido, asignando rol por defecto')
      // Permitir acceso como cajero por defecto
      const mappedRole = 'cajero'
      if (allowedRoles.includes(mappedRole)) {
        next()
      } else {
        next('/pos')
      }
      return
    }

    const roleName = user.role?.name || user.role
    
    // Mapear roles del backend a roles esperados
    const roleMapping = {
      'Administrador': 'admin',
      'Administrador POS': 'admin_pos',
      'Cajero': 'cajero',
      'Vendedor': 'vendedor',
      'Gerente': 'admin'
    }
    
    const mappedRole = roleMapping[roleName] || roleName

    if (allowedRoles.includes(mappedRole)) {
      next()
    } else {
      // TODOS los usuarios van al POS
      next('/pos')
    }
  }
}

// Middleware para admins únicamente
export const requireAdmin = requireRole(['admin', 'admin_pos'])

// Middleware para cajeros y admins
export const requireCashier = requireRole(['admin', 'admin_pos', 'cajero'])

// Middleware para vendedores, cajeros y admins
export const requireSeller = requireRole(['admin', 'admin_pos', 'cajero', 'vendedor'])

// Middleware para redirigir si ya está autenticado
export const redirectIfAuth = (to, from, next) => {
  // IMPORTANTE: Permitir acceso a login si viene con token pendiente de procesar
  const hasGoogleToken = to.query.google_login_token || to.query.google_token
  const hasCentralToken = to.query.central_login_token
  
  if (hasGoogleToken || hasCentralToken) {
    // Limpiar auth previa para evitar conflictos con el token nuevo
    if (hasCentralToken) {
      localStorage.removeItem('authToken')
      localStorage.removeItem('user')
    }
    next()
    return
  }
  
  if (authService.isAuthenticated()) {
    // Permitir acceso a rutas de onboarding aunque esté autenticado
    const onboardingRoutes = ['/welcome', '/onboarding']
    if (onboardingRoutes.includes(to.path)) {
      next()
      return
    }
    
    next('/pos')
  } else {
    next()
  }
}

// Roles y permisos del sistema
export const ROLES = {
  ADMIN: 'admin',
  ADMIN_POS: 'admin_pos',
  CAJERO: 'cajero',
  VENDEDOR: 'vendedor'
}

export const PERMISSIONS = {
  // Administración general
  ADMIN_PANEL: ['admin', 'admin_pos'],
  
  // Punto de Venta
  POS_ACCESS: ['admin', 'admin_pos', 'cajero', 'vendedor'],
  POS_DISCOUNTS: ['admin', 'admin_pos', 'cajero'],
  POS_RETURNS: ['admin', 'admin_pos', 'cajero'],
  
  // Productos
  PRODUCTS_VIEW: ['admin', 'admin_pos', 'cajero', 'vendedor'],
  PRODUCTS_CREATE: ['admin', 'admin_pos'],
  PRODUCTS_EDIT: ['admin', 'admin_pos'],
  PRODUCTS_DELETE: ['admin', 'admin_pos'],
  
  // Categorías
  CATEGORIES_VIEW: ['admin', 'admin_pos', 'cajero'],
  CATEGORIES_MANAGE: ['admin', 'admin_pos'],
  
  // Clientes
  CUSTOMERS_VIEW: ['admin', 'admin_pos', 'cajero', 'vendedor'],
  CUSTOMERS_CREATE: ['admin', 'admin_pos', 'cajero', 'vendedor'],
  CUSTOMERS_EDIT: ['admin', 'admin_pos', 'cajero'],
  CUSTOMERS_DELETE: ['admin', 'admin_pos'],
  
  // Proveedores
  SUPPLIERS_VIEW: ['admin', 'admin_pos'],
  SUPPLIERS_MANAGE: ['admin', 'admin_pos'],
  
  // Inventario
  INVENTORY_VIEW: ['admin', 'admin_pos', 'cajero'],
  INVENTORY_MANAGE: ['admin', 'admin_pos'],
  
  // Reportes
  REPORTS_BASIC: ['admin', 'admin_pos', 'cajero'],
  REPORTS_ADVANCED: ['admin'],
  REPORTS_FINANCIAL: ['admin'],
  
  // Configuración
  SETTINGS_VIEW: ['admin', 'admin_pos'],
  SETTINGS_MANAGE: ['admin'],
  
  // Usuarios y Roles
  USERS_VIEW: ['admin', 'admin_pos'],
  USERS_MANAGE: ['admin', 'admin_pos'],
  ROLES_VIEW: ['admin'],
  ROLES_MANAGE: ['admin'],
  
  // Dashboard
  DASHBOARD_ACCESS: ['admin', 'admin_pos', 'cajero', 'vendedor'],
  
  // Ventas
  SALES_VIEW: ['admin', 'cajero'],
  SALES_HISTORY: ['admin', 'cajero'],
  
  // Stock
  STOCK_VIEW: ['admin'],
  STOCK_MANAGE: ['admin'],
  
  // Informes ejecutivos
  INFORMES_VIEW: ['admin'],
  INFORMES_GENERATE: ['admin']
}

// Función para verificar permisos
export const hasPermission = (permission) => {
  const user = authService.getUser()
  if (!user) return false
  
  const allowedRoles = PERMISSIONS[permission]
  return allowedRoles ? allowedRoles.includes(user.role) : false
}

// Función para verificar múltiples permisos
export const hasAnyPermission = (permissions) => {
  return permissions.some(permission => hasPermission(permission))
}

// Función para verificar todos los permisos
export const hasAllPermissions = (permissions) => {
  return permissions.every(permission => hasPermission(permission))
}

export default {
  requireAuth,
  requireRole,
  requireAdmin,
  requireCashier,
  requireSeller,
  redirectIfAuth,
  hasPermission,
  hasAnyPermission,
  hasAllPermissions,
  ROLES,
  PERMISSIONS
}