import api from './api';

export const stockTransferService = {
  /**
   * Obtener todos los traslados
   */
  async getAll(filters = {}) {
    return await api.get('/stock-transfers', { params: filters });
  },

  /**
   * Obtener un traslado específico
   */
  async getById(id) {
    return await api.get(`/stock-transfers/${id}`);
  },

  /**
   * Crear un nuevo traslado
   */
  async create(data) {
    return await api.post('/stock-transfers', data);
  },

  /**
   * Completar un traslado
   */
  async complete(id) {
    return await api.post(`/stock-transfers/${id}/complete`);
  },

  /**
   * Cancelar un traslado
   */
  async cancel(id) {
    return await api.post(`/stock-transfers/${id}/cancel`);
  },

  /**
   * Eliminar un traslado
   */
  async delete(id) {
    return await api.delete(`/stock-transfers/${id}`);
  }
};
