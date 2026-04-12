import { ref, computed } from 'vue'
import apiClient from '../services/apiClient'

// Estado global para notificaciones
const notifications = ref([])
const notificationCounts = ref({
  movements_count: 0,
  alerts_count: 0,
  last_movements_view: null,
  last_alerts_view: null
})

// Variable para el polling interval
let pollingInterval = null

export const notificationStore = {
  // Estado
  notifications,
  notificationCounts,

  // Computed para contar notificaciones no vistas
  unreadMovementsCount: computed(() => {
    return notificationCounts.value.movements_count || 0
  }),

  unreadAlertsCount: computed(() => {
    return notificationCounts.value.alerts_count || 0
  }),

  // Métodos
  async loadNotifications() {
    try {
      // Cargar contadores desde la API
      const countsResponse = await apiClient.get('/notifications/test-counts') // Usar endpoint de prueba
      if (countsResponse.data.success) {
        notificationCounts.value = countsResponse.data.data
      }
      
      // Cargar movimientos recientes para mostrar en el listado
      const movementsResponse = await apiClient.get('/inventory/movements?limit=50&period=day')
      const movements = movementsResponse.data.data.movements || []
      
      // Cargar alertas recientes
      const alertsResponse = await apiClient.get('/inventory/test/alerts')
      const alerts = alertsResponse.data.data.alerts || []
      
      // Convertir a formato de notificación
      const movementNotifications = movements.map(movement => ({
        id: `movement_${movement.movement_id}`,
        type: 'movement',
        title: `Movimiento: ${movement.product_name}`,
        message: `${movement.movement_reason} - ${Math.abs(movement.quantity)} unidades`,
        created_at: movement.movement_date,
        data: movement
      }))
      
      const alertNotifications = alerts.map(alert => ({
        id: alert.id,
        type: 'alert',
        title: alert.title,
        message: alert.message,
        created_at: alert.created_at,
        severity: alert.severity,
        data: alert
      }))
      
      notifications.value = [
        ...movementNotifications,
        ...alertNotifications
      ].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
      
    } catch (error) {
      console.error('Error loading notifications:', error)
    }
  },

  // Método separado para cargar solo contadores (más eficiente)
  async loadCounts() {
    try {
      const response = await apiClient.get('/notifications/test-counts') // Usar endpoint de prueba
      if (response.data.success) {
        notificationCounts.value = response.data.data
      }
    } catch (error) {
      console.error('Error loading notification counts:', error)
    }
  },

  async markMovementsAsViewed() {
    try {
      await apiClient.post('/notifications/test-mark-movements') // Usar endpoint de prueba
      // Recargar contadores después de marcar como visto
      await this.loadCounts()
    } catch (error) {
      console.error('Error marking movements as viewed:', error)
    }
  },

  async markAlertsAsViewed() {
    try {
      await apiClient.post('/notifications/test-mark-alerts') // Usar endpoint de prueba
      // Recargar contadores después de marcar como visto
      await this.loadCounts()
    } catch (error) {
      console.error('Error marking alerts as viewed:', error)
    }
  },

  // Método para inicializar (ya no necesita localStorage)
  async initializeLastVisited() {
    await this.loadCounts()
  },

  // NUEVO: Polling automático cada 30 segundos para detectar cambios
  startPolling(intervalMs = 30000) {
    this.stopPolling() // Detener cualquier polling anterior
    
    pollingInterval = setInterval(async () => {
      try {
        await this.loadCounts()
      } catch (error) {
        console.error('Error en polling de notificaciones:', error)
      }
    }, intervalMs)
    
  },

  stopPolling() {
    if (pollingInterval) {
      clearInterval(pollingInterval)
      pollingInterval = null
    }
  }
}