import apiClient from './apiClient.js'

const ROLES_ENDPOINT = '/roles'

class RolesService {
  // Obtener todos los roles
  async getAllRoles() {
    try {
      const response = await apiClient.get(ROLES_ENDPOINT)
      return response.data
    } catch (error) {
      console.error('Error fetching roles:', error)
      throw error
    }
  }

  // Obtener un rol por ID
  async getRoleById(id) {
    try {
      const response = await apiClient.get(`${ROLES_ENDPOINT}/${id}`)
      return response.data
    } catch (error) {
      console.error('Error fetching role:', error)
      throw error
    }
  }

  // Crear un nuevo rol
  async createRole(roleData) {
    try {
      const response = await apiClient.post(ROLES_ENDPOINT, {
        name: roleData.name,
        description: roleData.description,
        permissions: roleData.permissions,
        active: roleData.active
      })
      return response.data
    } catch (error) {
      console.error('Error creating role:', error)
      throw error
    }
  }

  // Actualizar un rol
  async updateRole(id, roleData) {
    try {
      const response = await apiClient.put(`${ROLES_ENDPOINT}/${id}`, {
        name: roleData.name,
        description: roleData.description,
        permissions: roleData.permissions,
        active: roleData.active
      })
      return response.data
    } catch (error) {
      console.error('Error updating role:', error)
      throw error
    }
  }

  // Eliminar un rol
  async deleteRole(id) {
    try {
      const response = await apiClient.delete(`${ROLES_ENDPOINT}/${id}`)
      return response.data
    } catch (error) {
      console.error('Error deleting role:', error)
      throw error
    }
  }

  // Obtener permisos disponibles
  async getAvailablePermissions() {
    try {
      const response = await apiClient.get('/permissions')
      return response.data
    } catch (error) {
      console.error('Error fetching permissions:', error)
      // Fallback con permisos por defecto si no hay endpoint
      return [
        { id: 'dashboard', name: 'Dashboard', category: 'Principal' },
        { id: 'pos', name: 'Punto de Venta', category: 'Ventas' },
        { id: 'sales', name: 'Ventas', category: 'Ventas' },
        { id: 'products', name: 'Productos', category: 'Inventario' },
        { id: 'categories', name: 'Categorías', category: 'Inventario' },
        { id: 'stock', name: 'Control de Stock', category: 'Inventario' },
        { id: 'customers', name: 'Clientes', category: 'Relaciones' },
        { id: 'suppliers', name: 'Proveedores', category: 'Relaciones' },
        { id: 'users', name: 'Usuarios', category: 'Administración' },
        { id: 'roles', name: 'Roles y Permisos', category: 'Administración' },
        { id: 'reports', name: 'Reportes', category: 'Administración' },
        { id: 'informes', name: 'Informes Avanzados', category: 'Administración' },
        { id: 'settings', name: 'Configuración', category: 'Administración' }
      ]
    }
  }

  // Verificar si un rol puede ser eliminado (no tiene usuarios asignados)
  async canDeleteRole(id) {
    try {
      const response = await apiClient.get(`${ROLES_ENDPOINT}/${id}/can-delete`)
      return response.data.canDelete
    } catch (error) {
      console.error('Error checking if role can be deleted:', error)
      return false
    }
  }

  // Obtener usuarios por rol - NUEVO ENDPOINT
  async getUsersByRole(id) {
    try {
      const response = await apiClient.get(`${ROLES_ENDPOINT}/${id}/users`)
      return response.data
    } catch (error) {
      console.error('Error fetching users by role:', error)
      throw error
    }
  }
}

export default new RolesService()