import { reactive, computed } from 'vue'
import authService from '../services/authService.js'
import { appStore } from './appStore.js'

// Estado global de autenticación
const state = reactive({
  user: null,
  token: null,
  isAuthenticated: false,
  loading: false,
  error: null
})

// Getters computados
const getters = {
  isAuthenticated: computed(() => state.isAuthenticated),
  user: computed(() => state.user),
  token: computed(() => state.token),
  loading: computed(() => state.loading),
  error: computed(() => state.error),
  userRole: computed(() => state.user?.role?.name || state.user?.role),
  userName: computed(() => state.user?.name),
  userCC: computed(() => state.user?.cc)
}

// Acciones
const actions = {
  // Inicializar autenticación
  async initialize() {
    state.loading = true
    state.error = null
    
    try {
      const token = authService.getToken()
      const user = authService.getUser()
      
      if (token && user) {
        state.token = token
        state.user = user
        state.isAuthenticated = true
        
        // Verificar que el token sigue siendo válido
        try {
          const currentUser = await authService.getCurrentUser()
          state.user = currentUser
        } catch (error) {
          // Token inválido, limpiar estado
          await this.logout()
        }
      }
    } catch (error) {
      console.error('Error al inicializar autenticación:', error)
      await this.logout()
    } finally {
      state.loading = false
    }
  },

  // Login
  async login(credentials) {
    state.loading = true
    state.error = null
    
    try {
      const response = await authService.login(credentials)
      
      state.token = response.token
      state.user = response.user
      state.isAuthenticated = true
      
      // ✅ INICIALIZAR APPSTORE DESPUÉS DEL LOGIN EXITOSO (incluye sesión de caja)
      console.log('🚀 Login exitoso, inicializando appStore con sesión de caja...')
      await appStore.initialize()
      
      return response
    } catch (error) {
      state.error = error.message || 'Error en el login'
      throw error
    } finally {
      state.loading = false
    }
  },

  // Logout
  async logout() {
    state.loading = true
    
    try {
      await authService.logout()
    } catch (error) {
      console.error('Error en logout:', error)
    } finally {
      state.user = null
      state.token = null
      state.isAuthenticated = false
      state.error = null
      state.loading = false
      
      // ✅ LIMPIAR APPSTORE AL HACER LOGOUT
      appStore.initialized = false
      appStore.cashSession.initialized = false
      console.log('🧹 AppStore limpiado después del logout')
    }
  },

  // Actualizar usuario
  async updateUser() {
    if (!state.isAuthenticated) return
    
    try {
      const user = await authService.getCurrentUser()
      state.user = user
      localStorage.setItem('user', JSON.stringify(user))
    } catch (error) {
      console.error('Error al actualizar usuario:', error)
      await this.logout()
    }
  },

  // Cambiar contraseña
  async changePassword(passwords) {
    state.loading = true
    state.error = null
    
    try {
      const response = await authService.changePassword(passwords)
      return response
    } catch (error) {
      state.error = error.message || 'Error al cambiar contraseña'
      throw error
    } finally {
      state.loading = false
    }
  },

  // Limpiar errores
  clearError() {
    state.error = null
  },

  // Verificar permisos
  hasRole(role) {
    const userRole = state.user?.role?.name || state.user?.role
    return userRole === role
  },

  hasAnyRole(roles) {
    const userRole = state.user?.role?.name || state.user?.role
    return roles.includes(userRole)
  },

  // Validar credenciales de administrador
  async validateAdminCredentials(email, password) {
    try {
      const response = await authService.validateAdminCredentials(email, password)
      return response.valid
    } catch (error) {
      console.error('Error validando credenciales de admin:', error)
      return false
    }
  },

  // Verificar si el token ha expirado
  async checkTokenExpiration() {
    if (!state.isAuthenticated || !state.token) {
      return false
    }

    try {
      // Intentar hacer una request simple para verificar el token
      await authService.getCurrentUser()
      return true // Token válido
    } catch (error) {
      if (error.response?.status === 401) {
        // Token expirado, limpiar sesión
        console.log('🔒 Token expirado, limpiando sesión automáticamente')
        await this.logout()
        return false
      }
      // Otro error, mantener sesión
      return true
    }
  },

  // Forzar limpieza de sesión (para timeout manual)
  forceLogout() {
    state.user = null
    state.token = null
    state.isAuthenticated = false
    state.error = null
    state.loading = false
    
    // Limpiar localStorage
    localStorage.removeItem('authToken')
    localStorage.removeItem('user')
    
    // ✅ LIMPIAR APPSTORE AL FORZAR LOGOUT
    appStore.initialized = false
    appStore.cashSession.initialized = false
    
    console.log('🚪 Sesión forzada a cerrar')
  }
}

// Inicializar servicio de autenticación
authService.initializeAuth()

// Hook composable para usar la autenticación
export function useAuth() {
  return {
    ...getters,
    ...actions
  }
}

export default {
  state,
  getters,
  actions
}