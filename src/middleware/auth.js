import authService from '../services/authService.js'

// Middleware para verificar autenticación
export const requireAuth = (to, from, next) => {
  if (authService.isAuthenticated()) {
    next()
  } else {
    console.log('❌ Usuario no autenticado, redirigiendo a login')
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

    const roleName = user.role?.name || user.role
    
    // Mapear roles del backend a roles esperados
    const roleMapping = {
      'Administrador': 'admin',
      'Cajero': 'cajero',
      'Vendedor': 'vendedor',
      'Gerente': 'admin'
    }
    
    const mappedRole = roleMapping[roleName] || roleName

    if (allowedRoles.includes(mappedRole)) {
      next()
    } else {
      // Redireccionar según el rol del usuario
      if (mappedRole === 'cajero' || mappedRole === 'vendedor') {
        next('/pos')
      } else {
        next('/dashboard')
      }
    }
  }
}

// Middleware para admins únicamente
export const requireAdmin = requireRole(['admin'])

// Middleware para cajeros y admins
export const requireCashier = requireRole(['admin', 'cajero'])

// Middleware para vendedores, cajeros y admins
export const requireSeller = requireRole(['admin', 'cajero', 'vendedor'])

// Middleware para redirigir si ya está autenticado
export const redirectIfAuth = (to, from, next) => {
  if (authService.isAuthenticated()) {
    const user = authService.getUser()
    const roleName = user.role?.name || user.role
    
    if (roleName === 'Administrador' || roleName === 'admin') {
      console.log('👑 Redirigiendo administrador a dashboard')
      next('/dashboard')
    } else {
      console.log('👤 Redirigiendo usuario a POS')
      next('/pos')
    }
  } else {
    console.log('❌ Usuario no autenticado, continuando al login')
    next()
  }
}

// Roles y permisos del sistema
export const ROLES = {
  ADMIN: 'admin',
  CAJERO: 'cajero',
  VENDEDOR: 'vendedor'
}

export const PERMISSIONS = {
  // Administración general
  ADMIN_PANEL: ['admin'],
  
  // Punto de Venta
  POS_ACCESS: ['admin', 'cajero', 'vendedor'],
  POS_DISCOUNTS: ['admin', 'cajero'],
  POS_RETURNS: ['admin', 'cajero'],
  
  // Productos
  PRODUCTS_VIEW: ['admin', 'cajero', 'vendedor'],
  PRODUCTS_CREATE: ['admin'],
  PRODUCTS_EDIT: ['admin'],
  PRODUCTS_DELETE: ['admin'],
  
  // Categorías
  CATEGORIES_VIEW: ['admin', 'cajero'],
  CATEGORIES_MANAGE: ['admin'],
  
  // Clientes
  CUSTOMERS_VIEW: ['admin', 'cajero', 'vendedor'],
  CUSTOMERS_CREATE: ['admin', 'cajero', 'vendedor'],
  CUSTOMERS_EDIT: ['admin', 'cajero'],
  CUSTOMERS_DELETE: ['admin'],
  
  // Proveedores
  SUPPLIERS_VIEW: ['admin'],
  SUPPLIERS_MANAGE: ['admin'],
  
  // Inventario
  INVENTORY_VIEW: ['admin', 'cajero'],
  INVENTORY_MANAGE: ['admin'],
  
  // Reportes
  REPORTS_BASIC: ['admin', 'cajero'],
  REPORTS_ADVANCED: ['admin'],
  REPORTS_FINANCIAL: ['admin'],
  
  // Configuración
  SETTINGS_VIEW: ['admin'],
  SETTINGS_MANAGE: ['admin'],
  
  // Usuarios y Roles
  USERS_VIEW: ['admin'],
  USERS_MANAGE: ['admin'],
  ROLES_VIEW: ['admin'],
  ROLES_MANAGE: ['admin'],
  
  // Dashboard
  DASHBOARD_ACCESS: ['admin', 'cajero', 'vendedor'],
  
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