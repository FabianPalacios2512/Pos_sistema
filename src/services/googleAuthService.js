import apiClient from './apiClient'

/**
 * Servicio para autenticación con Google OAuth 2.0
 */
class GoogleAuthService {
  /**
   * Inicia el flujo de autenticación con Google
   * @param {Object} registrationData - Datos de registro del usuario (company_name, etc.)
   * @returns {Promise<string>} - URL de autorización de Google
   */
  async initiateGoogleAuth(registrationData = {}) {
    try {
      const response = await apiClient.post('/auth/google/redirect', {
        state: JSON.stringify(registrationData)
      })
      
      if (response.data.success && response.data.url) {
        return response.data.url
      }
      
      throw new Error(response.data.message || 'Error al generar URL de Google')
    } catch (error) {
      console.error('Error en initiateGoogleAuth:', error)
      throw error
    }
  }

  /**
   * Maneja el callback de Google OAuth después de la autorización
   * (Esta función se llama automáticamente por el backend en la ruta GET callback)
   * Solo se usa si necesitas procesar el callback desde el frontend
   */
  async handleCallback(code, state) {
    try {
      const response = await apiClient.get('/auth/google/callback', {
        params: { code, state }
      })
      
      return response.data
    } catch (error) {
      console.error('Error en handleCallback:', error)
      throw error
    }
  }

  /**
   * Inicia sesión con Google (para usuarios existentes)
   * @returns {Promise<Object>} - Datos del usuario y token
   */
  async loginWithGoogle() {
    try {
      const response = await apiClient.post('/auth/google/login')
      return response.data
    } catch (error) {
      console.error('Error en loginWithGoogle:', error)
      throw error
    }
  }

  /**
   * Abre popup de Google OAuth (alternativa a redirect completo)
   * @param {string} authUrl - URL de autorización de Google
   * @returns {Promise<Object>} - Resultado del OAuth
   */
  openGooglePopup(authUrl) {
    return new Promise((resolve, reject) => {
      const width = 500
      const height = 600
      const left = (window.screen.width / 2) - (width / 2)
      const top = (window.screen.height / 2) - (height / 2)
      
      const popup = window.open(
        authUrl,
        'Google Sign In',
        `width=${width},height=${height},left=${left},top=${top}`
      )
      
      if (!popup) {
        reject(new Error('Popup bloqueado. Permite popups para este sitio.'))
        return
      }

      // Listener para mensaje del popup
      const messageHandler = (event) => {
        // Validar origen del mensaje
        if (event.origin !== window.location.origin) return
        
        if (event.data.type === 'google-auth-success') {
          window.removeEventListener('message', messageHandler)
          popup.close()
          resolve(event.data.payload)
        } else if (event.data.type === 'google-auth-error') {
          window.removeEventListener('message', messageHandler)
          popup.close()
          reject(new Error(event.data.message))
        }
      }

      window.addEventListener('message', messageHandler)

      // Verificar si el popup fue cerrado manualmente
      const checkClosed = setInterval(() => {
        if (popup.closed) {
          clearInterval(checkClosed)
          window.removeEventListener('message', messageHandler)
          reject(new Error('Autenticación cancelada'))
        }
      }, 500)
    })
  }
}

export default new GoogleAuthService()
