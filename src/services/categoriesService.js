import { apiCall } from './api.js'

export const categoriesService = {
  async getAll() {
    try {
  // console.log('categoriesService.getAll - Haciendo petición...')
      const response = await apiCall('/categories')
  // console.log('categoriesService.getAll - Respuesta recibida:', response)
      return response // Devolver la respuesta completa que ya tiene { success, data, message }
    } catch (error) {
  // console.error('Error getting categories:', error)
      throw error
    }
  },

  // Obtener categorías optimizadas para POS
  async getForPos() {
    try {
      const response = await apiCall('/categories-pos')
      return response
    } catch (error) {
  // console.error('Error getting categories for POS:', error)
      throw error
    }
  },

  async getById(id) {
    try {
      const response = await apiCall(`/categories/${id}`)
      return response.data
    } catch (error) {
  // console.error('Error getting category:', error)
      throw error
    }
  },

  async create(categoryData) {
    try {
  // console.log('categoriesService.create - Datos enviados:', categoryData)
      const response = await apiCall('/categories', {
        method: 'POST',
        body: JSON.stringify(categoryData)
      })
  // console.log('categoriesService.create - Respuesta recibida:', response)
      return response // Devolver la respuesta completa que ya tiene { success, data, message }
    } catch (error) {
  // console.error('Error creating category:', error)
      throw error
    }
  },

  async update(id, categoryData) {
    try {
      const response = await apiCall(`/categories/${id}`, {
        method: 'PUT',
        body: JSON.stringify(categoryData)
      })
      return response // Devolver la respuesta completa que ya tiene { success, data, message }
    } catch (error) {
  // console.error('Error updating category:', error)
      throw error
    }
  },

  async delete(id) {
    try {
      const response = await apiCall(`/categories/${id}`, {
        method: 'DELETE'
      })
      return response.data
    } catch (error) {
  // console.error('Error deleting category:', error)
      throw error
    }
  }
}

export default categoriesService