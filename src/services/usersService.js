import apiClient from './apiClient.js'

const USERS_ENDPOINT = '/users'

class UsersService {
  // Obtener todos los usuarios
  async getAllUsers() {
    try {
      const response = await apiClient.get(USERS_ENDPOINT)
      return response.data
    } catch (error) {
      console.error('Error fetching users:', error)
      throw error
    }
  }

  // Obtener un usuario por ID
  async getUserById(id) {
    try {
      const response = await apiClient.get(`${USERS_ENDPOINT}/${id}`)
      return response.data
    } catch (error) {
      console.error('Error fetching user:', error)
      throw error
    }
  }

  // Crear un nuevo usuario
  async createUser(userData) {
    try {
      const payload = {
        name: userData.name,
        email: userData.email,
        cc: userData.cc, // Cédula - corregido para usar 'cc'
        phone: userData.phone,
        password: userData.password,
        role_id: userData.role_id,
        warehouse_id: userData.warehouse_id || null,
        active: userData.active !== undefined ? userData.active : true
      }
      
      const response = await apiClient.post(USERS_ENDPOINT, payload)
      return response.data
    } catch (error) {
      console.error('Error creating user:', error)
      throw error
    }
  }

  // Actualizar un usuario
  async updateUser(id, userData) {
    try {
      const payload = {
        name: userData.name,
        email: userData.email,
        cc: userData.cc, // Corregido para usar 'cc'
        phone: userData.phone,
        role_id: userData.role_id,
        warehouse_id: userData.warehouse_id || null,
        active: userData.active
      }

      // Solo incluir contraseña si se está actualizando
      if (userData.password && userData.password.trim() !== '') {
        payload.password = userData.password
      }

      const response = await apiClient.put(`${USERS_ENDPOINT}/${id}`, payload)
      return response.data
    } catch (error) {
      console.error('Error updating user:', error)
      throw error
    }
  }

  // Eliminar un usuario
  async deleteUser(id) {
    try {
      const response = await apiClient.delete(`${USERS_ENDPOINT}/${id}`)
      return response.data
    } catch (error) {
      console.error('Error deleting user:', error)
      throw error
    }
  }

  // Cambiar estado de un usuario (activar/desactivar) - NUEVO ENDPOINT
  async toggleUserActive(id) {
    try {
      const response = await apiClient.patch(`${USERS_ENDPOINT}/${id}/toggle-active`)
      return response.data
    } catch (error) {
      console.error('Error toggling user active status:', error)
      throw error
    }
  }

  // Cambiar contraseña de un usuario - NUEVO ENDPOINT
  async changePassword(id, passwordData) {
    try {
      const response = await apiClient.post(`${USERS_ENDPOINT}/${id}/change-password`, {
        password: passwordData.password,
        password_confirmation: passwordData.password_confirmation
      })
      return response.data
    } catch (error) {
      console.error('Error changing password:', error)
      throw error
    }
  }

  // DEPRECATED: Métodos antiguos - mantener por compatibilidad
  async toggleUserStatus(id, active) {
    try {
      const response = await apiClient.patch(`${USERS_ENDPOINT}/${id}/status`, {
        active: active
      })
      return response.data
    } catch (error) {
      console.error('Error toggling user status:', error)
      throw error
    }
  }

  async resetPassword(id, newPassword) {
    try {
      const response = await apiClient.patch(`${USERS_ENDPOINT}/${id}/password`, {
        password: newPassword
      })
      return response.data
    } catch (error) {
      console.error('Error resetting password:', error)
      throw error
    }
  }

  // Verificar si una cédula ya existe
  async checkDocumentExists(documentNumber, excludeId = null) {
    try {
      const params = { document_number: documentNumber }
      if (excludeId) {
        params.exclude_id = excludeId
      }
      
      const response = await apiClient.get(`${USERS_ENDPOINT}/check-document`, { params })
      return response.data.exists
    } catch (error) {
      console.error('Error checking document:', error)
      return false
    }
  }

  // Verificar si un email ya existe
  async checkEmailExists(email, excludeId = null) {
    try {
      const params = { email }
      if (excludeId) {
        params.exclude_id = excludeId
      }
      
      const response = await apiClient.get(`${USERS_ENDPOINT}/check-email`, { params })
      return response.data.exists
    } catch (error) {
      console.error('Error checking email:', error)
      return false
    }
  }

  // Obtener usuarios por rol
  async getUsersByRole(roleId) {
    try {
      const response = await apiClient.get(`${USERS_ENDPOINT}/by-role/${roleId}`)
      return response.data
    } catch (error) {
      console.error('Error fetching users by role:', error)
      throw error
    }
  }

  // Obtener estadísticas de usuarios
  async getUserStats() {
    try {
      const response = await apiClient.get(`${USERS_ENDPOINT}/stats`)
      return response.data
    } catch (error) {
      console.error('Error fetching user stats:', error)
      return {
        total: 0,
        active: 0,
        inactive: 0,
        by_role: {}
      }
    }
  }

  // ═══════════════════════════════════════════
  // Dashboard de Rendimiento & Auditoría
  // ═══════════════════════════════════════════

  async getDashboardKpis(params = {}) {
    const response = await apiClient.get('/users-dashboard/kpis', { params })
    return response.data
  }

  async getUsersWithPerformance(params = {}) {
    const response = await apiClient.get('/users-dashboard/with-performance', { params })
    return response.data
  }

  async getUserProfile(id) {
    const response = await apiClient.get(`/users-dashboard/${id}/profile`)
    return response.data
  }

  async getUserTimeline(id, date = null) {
    const params = date ? { date } : {}
    const response = await apiClient.get(`/users-dashboard/${id}/timeline`, { params })
    return response.data
  }

  async assignUserWarehouse(id, warehouseId) {
    const response = await apiClient.patch(`/users-dashboard/${id}/assign-warehouse`, {
      warehouse_id: warehouseId
    })
    return response.data
  }

  async getPlanInfo() {
    const response = await apiClient.get('/users-dashboard/plan-info')
    return response.data
  }
}

export default new UsersService()