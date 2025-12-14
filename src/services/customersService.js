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

  async findByPhone(phone) {
    try {
      const response = await this.getAll()
      // La respuesta puede ser un array directamente o un objeto con propiedad data/customers
      const customersList = Array.isArray(response) ? response : (response.data || response.customers || [])
      
      // Normalizar teléfonos (remover espacios, guiones, etc.)
      const normalizedPhone = phone.replace(/[\s\-()]/g, '')
      return customersList.find(c => {
        const customerPhone = (c.phone || '').replace(/[\s\-()]/g, '')
        return customerPhone === normalizedPhone
      })
    } catch (error) {
      console.error('Error finding customer by phone:', error)
      return null
    }
  },

  async findByDocument(document) {
    try {
      const response = await this.getAll()
      // La respuesta puede ser un array directamente o un objeto con propiedad data/customers
      const customersList = Array.isArray(response) ? response : (response.data || response.customers || [])
      
      // Normalizar documento (remover espacios, guiones, puntos)
      const normalizedDocument = document.replace(/[\s\-\.]/g, '').toUpperCase()
      return customersList.find(c => {
        const customerDocument = (c.document_number || '').replace(/[\s\-\.]/g, '').toUpperCase()
        return customerDocument === normalizedDocument
      })
    } catch (error) {
      console.error('Error finding customer by document:', error)
      return null
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