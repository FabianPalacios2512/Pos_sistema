import apiClient from './apiClient.js'

const BIOMETRIC_ENDPOINT = '/biometric'

class BiometricService {
  /**
   * Enrolar perfil biométrico (imagen base + descriptor facial)
   */
  async enrollProfile(userId, imageBase64, descriptors) {
    try {
      const response = await apiClient.post(`${BIOMETRIC_ENDPOINT}/enroll`, {
        user_id: userId,
        image: imageBase64,
        descriptors: Array.from(descriptors),
      })
      return response.data
    } catch (error) {
      console.error('Error enrolling biometric profile:', error)
      throw error
    }
  }

  /**
   * Obtener descriptor facial activo de un usuario
   */
  async getDescriptor(userId) {
    try {
      const response = await apiClient.get(`${BIOMETRIC_ENDPOINT}/${userId}/descriptor`)
      return response.data
    } catch (error) {
      console.error('Error fetching descriptor:', error)
      throw error
    }
  }

  /**
   * Verificar si un usuario tiene perfil biométrico
   */
  async checkEnrollment(userId) {
    try {
      const response = await apiClient.get(`${BIOMETRIC_ENDPOINT}/${userId}/check`)
      return response.data
    } catch (error) {
      console.error('Error checking enrollment:', error)
      throw error
    }
  }

  /**
   * Registrar punteo de asistencia
   */
  async recordAttendance(userId, eventType, verificationScore, imageBase64 = null) {
    try {
      const response = await apiClient.post(`${BIOMETRIC_ENDPOINT}/attendance`, {
        user_id: userId,
        event_type: eventType,
        verification_score: verificationScore,
        image: imageBase64,
      })
      return response.data
    } catch (error) {
      console.error('Error recording attendance:', error)
      throw error
    }
  }

  /**
   * Obtener historial de asistencia
   */
  async getAttendanceHistory(params = {}) {
    try {
      const response = await apiClient.get(`${BIOMETRIC_ENDPOINT}/attendance/history`, { params })
      return response.data
    } catch (error) {
      console.error('Error fetching attendance history:', error)
      throw error
    }
  }

  /**
   * Obtener resumen del día actual
   */
  async getTodaySummary() {
    try {
      const response = await apiClient.get(`${BIOMETRIC_ENDPOINT}/attendance/today`)
      return response.data
    } catch (error) {
      console.error('Error fetching today summary:', error)
      throw error
    }
  }

  /**
   * Eliminar perfil biométrico de un usuario
   */
  async deleteProfile(userId) {
    try {
      const response = await apiClient.delete(`${BIOMETRIC_ENDPOINT}/${userId}/profile`)
      return response.data
    } catch (error) {
      console.error('Error deleting biometric profile:', error)
      throw error
    }
  }
}

export default new BiometricService()
