import api from './api.js'

export const suppliersService = {
  async getAll() {
    try {
      const response = await api.get('/suppliers')
      return response
    } catch (error) {
      console.error('Error getting suppliers:', error)
      throw error
    }
  },

  async getById(id) {
    try {
      const response = await api.get(`/suppliers/${id}`)
      return response.data
    } catch (error) {
      console.error('Error getting supplier:', error)
      throw error
    }
  },

  async create(supplierData) {
    try {
      const response = await api.post('/suppliers', supplierData)
      return response.data
    } catch (error) {
      console.error('Error creating supplier:', error)
      throw error
    }
  },

  async update(id, supplierData) {
    try {
      const response = await api.put(`/suppliers/${id}`, supplierData)
      return response.data
    } catch (error) {
      console.error('Error updating supplier:', error)
      throw error
    }
  },

  async delete(id) {
    try {
      const response = await api.delete(`/suppliers/${id}`)
      return response.data
    } catch (error) {
      console.error('Error deleting supplier:', error)
      throw error
    }
  },

  async toggleStatus(id) {
    try {
      const response = await api.put(`/suppliers/${id}/toggle-status`)
      return response
    } catch (error) {
      console.error('Error toggling supplier status:', error)
      throw error
    }
  },

  async getSupplierInfo(id) {
    try {
      const response = await api.get(`/suppliers/${id}`)
      return response
    } catch (error) {
      console.error('Error getting supplier info:', error)
      throw error
    }
  }
}

export default suppliersService