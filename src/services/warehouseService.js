import api from './api';

export const warehouseService = {
  /**
   * Obtener todas las bodegas
   */
  async getAll() {
    return await api.get('/warehouses');
  },

  /**
   * Obtener una bodega específica
   */
  async getById(id) {
    return await api.get(`/warehouses/${id}`);
  },

  /**
   * Obtener la bodega por defecto
   */
  async getDefault() {
    return await api.get('/warehouses/default');
  },

  /**
   * Crear una nueva bodega
   */
  async create(data) {
    return await api.post('/warehouses', data);
  },

  /**
   * Actualizar una bodega
   */
  async update(id, data) {
    return await api.put(`/warehouses/${id}`, data);
  },

  /**
   * Eliminar una bodega
   */
  async delete(id) {
    return await api.delete(`/warehouses/${id}`);
  },

  /**
   * Obtener inventario de una bodega
   */
  async getInventory(id) {
    return await api.get(`/warehouses/${id}/inventory`);
  },

  /**
   * Actualizar stock de un producto en una bodega
   */
  async updateStock(warehouseId, productId, quantity, type, notes = null) {
    return await api.post(`/warehouses/${warehouseId}/update-stock`, {
      product_id: productId,
      quantity,
      type, // 'add', 'subtract', 'adjust'
      notes
    });
  }
};
