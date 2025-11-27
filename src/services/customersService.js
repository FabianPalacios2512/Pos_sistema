import { apiCall } from './api.js'

export const customersService = {
  async getAll() {
    try {
      const response = await apiCall('/customers')
      return response
    } catch (error) {
      console.error('Error getting customers:', error)
      throw error
    }
  },

  async getById(id) {
    try {
      const response = await apiCall(`/customers/${id}`)
      return response.data
    } catch (error) {
      console.error('Error getting customer:', error)
    }
  },

  async create(customerData) {
    try {
      const response = await apiCall('/customers', {
        method: 'POST',
        body: JSON.stringify(customerData)
      })
      return response
    } catch (error) {
      console.error('Error creating customer:', error)
      throw error
    }
  },

  async update(id, customerData) {
    try {
      const response = await apiCall(`/customers/${id}`, {
        method: 'PUT',
        body: JSON.stringify(customerData)
      })
      return response
    } catch (error) {
      console.error('Error updating customer:', error)
      throw error
    }
  },

  async delete(id) {
    try {
      const response = await apiCall(`/customers/${id}`, {
        method: 'DELETE'
      })
      return response.data
    } catch (error) {
      console.error('Error deleting customer:', error)
      throw error
    }
  }
}

export default customersService