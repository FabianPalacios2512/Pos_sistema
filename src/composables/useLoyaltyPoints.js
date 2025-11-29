import { ref, computed } from 'vue'
import apiClient from '@/services/api'
import { appStore } from '@/store/appStore'

export function useLoyaltyPoints() {
  const loyaltySettings = ref({
    enabled: false,
    points_per_currency: 0.001,
    point_value: 10
  })

  const loading = ref(false)
  const error = ref(null)

  /**
   * Cargar configuración del sistema de loyalty
   * OPTIMIZACIÓN SaaS: Solo carga si está habilitado en system_settings
   */
  const loadSettings = async () => {
    try {
      // 🔥 OPTIMIZACIÓN: Verificar primero en appStore si loyalty está habilitado
      const systemSettings = appStore.systemSettings
      if (!systemSettings.enable_loyalty_system) {
        loyaltySettings.value.enabled = false
        return
      }

      loading.value = true
      error.value = null
      const response = await apiClient.get('/loyalty/settings')
      
      // La respuesta ya viene desenvuelta por handleApiResponse
      if (response.success && response.data) {
        loyaltySettings.value = response.data
      } else {
        console.warn('⚠️ [Loyalty] Respuesta sin estructura esperada:', response)
      }
    } catch (err) {
      console.error('❌ [Loyalty] Error loading loyalty settings:', err)
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  /**
   * Calcular cuántos puntos se ganarán por un monto
   */
  const calculatePointsToEarn = (amount) => {
    if (!loyaltySettings.value.enabled) return 0
    return Math.floor(amount * loyaltySettings.value.points_per_currency)
  }

  /**
   * Calcular el valor en dinero de los puntos
   */
  const calculatePointsValue = (points) => {
    if (!loyaltySettings.value.enabled) return 0
    return points * loyaltySettings.value.point_value
  }

  /**
   * Obtener puntos de un cliente
   */
  const getCustomerPoints = async (customerId) => {
    try {
      loading.value = true
      error.value = null
      const response = await apiClient.get(`/loyalty/customer/${customerId}/points`)
      
      if (response.data.success) {
        return response.data.data
      }
      return null
    } catch (err) {
      console.error('Error getting customer points:', err)
      error.value = err.message
      return null
    } finally {
      loading.value = false
    }
  }

  /**
   * Obtener historial de transacciones de puntos
   */
  const getCustomerTransactions = async (customerId) => {
    try {
      loading.value = true
      error.value = null
      const response = await apiClient.get(`/loyalty/customer/${customerId}/transactions`)
      
      if (response.data.success) {
        return response.data.data
      }
      return null
    } catch (err) {
      console.error('Error getting customer transactions:', err)
      error.value = err.message
      return null
    } finally {
      loading.value = false
    }
  }

  /**
   * Validar si se pueden redimir puntos
   */
  const validateRedemption = async (customerId, points) => {
    try {
      loading.value = true
      error.value = null
      const response = await apiClient.post('/loyalty/validate-redemption', {
        customer_id: customerId,
        points: points
      })
      
      return {
        success: response.data.success,
        data: response.data.data,
        message: response.data.message
      }
    } catch (err) {
      console.error('Error validating redemption:', err)
      return {
        success: false,
        message: err.response?.data?.message || err.message,
        data: err.response?.data?.data
      }
    } finally {
      loading.value = false
    }
  }

  /**
   * Formatear puntos como dinero
   */
  const formatPointsAsMoney = (points) => {
    const value = calculatePointsValue(points)
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(value)
  }

  /**
   * Sistema habilitado
   */
  const isEnabled = computed(() => loyaltySettings.value.enabled)

  return {
    // State
    loyaltySettings,
    loading,
    error,
    isEnabled,

    // Methods
    loadSettings,
    calculatePointsToEarn,
    calculatePointsValue,
    getCustomerPoints,
    getCustomerTransactions,
    validateRedemption,
    formatPointsAsMoney
  }
}
