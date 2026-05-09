import axios from 'axios'

// Configuración base del cliente API
const apiClient = axios.create({
  baseURL: '/api',
  timeout: 15000, // 15 segundos para operaciones normales (AI puede tardar)
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Client-Version': 'v1.1.0'
  }
})

// Interceptor para requests - agregar token automáticamente
apiClient.interceptors.request.use(
  (config) => {
    // Obtener token del localStorage
    const token = localStorage.getItem('authToken')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    
    // FIX: Si es FormData, ELIMINAR Content-Type para que el navegador lo establezca con boundary
    if (config.data instanceof FormData) {
      delete config.headers['Content-Type']
    }
    
    // Log para debugging (solo en desarrollo) - DESACTIVADO
    /*
    if (process.env.NODE_ENV === 'development') {
        hasToken: !!token,
        tokenSent: config.headers.Authorization ? 'YES' : 'NO',
        tokenPreview: token ? token.substring(0, 10) + '...' : 'NONE'
      })
    }
    */
    
    return config
  },
  (error) => {
  // console.error('Request error:', error)
    return Promise.reject(error)
  }
)

// Interceptor para responses - manejo global de errores
apiClient.interceptors.response.use(
  (response) => {
    // Log para debugging (solo en desarrollo)
    if (process.env.NODE_ENV === 'development') {
  /*
  */
    }
    
    // La verificación de suscripción se hace SOLO desde SubscriptionExpiredModal
    // consultando directamente al backend - NO detectar automáticamente aquí
    
    return response
  },
  (error) => {
    // Log del error
    /*
    console.error('API Response Error:', {
      status: error.response?.status,
      url: error.config?.url,
      message: error.message,
      data: error.response?.data
    })
    */

    // Manejo específico de errores HTTP
    if (error.response) {
      const { status, data } = error.response
      
      switch (status) {
        case 401:
          // Token expirado o inválido
          const currentPath = window.location.pathname
          
          // CRÍTICO: NO hacer logout si estamos en /login (proceso de login en curso)
          if (currentPath === '/login') {
            break
          }

          // CHECK PRIORITARIO: Si el backend dice explícitamente "Unauthenticated", el token es inválido.
          // Debemos limpiar y redirigir, SIN IMPORTAR en qué ruta estemos.
          if (data?.message === 'Unauthenticated.' || data?.message?.includes('Unauthenticated') || data?.message?.includes('Token')) {
            // PROTECCIÓN ANTI-BUCLE: Si acabamos de loguearnos (< 10 segundos), NO hacer logout
            const loginTimestamp = localStorage.getItem('loginTimestamp')
            const now = Date.now()
            
            if (loginTimestamp && (now - parseInt(loginTimestamp)) < 10000) {
                console.warn('401 recibido inmediatamente después de login (<10s) - Ignorando logout forzado para evitar bucle.')
                console.warn('Posible problema de configuración backend o latencia de replicación DB.')
                break
            }

            localStorage.removeItem('authToken')
            localStorage.removeItem('user')
            window.location.href = '/login?reason=expired&message=Tu sesión ha expirado'
            return Promise.reject(error)
          }
          
          // NO hacer logout si estamos en rutas especiales de pago
          const allowedExpiredRoutes = [
            '/subscription-expired', 
            '/select-plan', 
            '/payment/success', 
            '/payment/failure', 
            '/payment/pending',
            '/admin/god-mode',
            '/welcome', // Onboarding
            '/onboarding' // Configuración inicial
            // REMOVIDO: '/pos', '/dashboard' - Si el token es inválido, DEBE redirigir a login
          ]
          
          if (allowedExpiredRoutes.includes(currentPath) || currentPath.startsWith('/payment/')) {
            break
          }
          break
          
        case 403:
          // Sin permisos
          // console.warn('Acceso denegado:', data.message)
          
          // Detectar si es por suscripción expirada
          if (data?.subscription_expired === true || (data?.message && (
              data.message.includes('suscripción ha finalizado') ||
              data.message.includes('suscripción ha expirado') || 
              data.message.includes('plan ha expirado') ||
              data.message.includes('renueva tu plan')
          ))) {
            // YA NO REDIRIGIR - El modal aparecerá automáticamente en el POS
            // Solo registrar el evento
          }
          break
          
        case 404:
          // Recurso no encontrado
          // console.warn('Recurso no encontrado:', error.config?.url)
          break
          
        case 422:
          // Errores de validación
          // console.warn('Errores de validación:', data.errors)
          break
        
        case 404:
          // Recurso no encontrado
          // EXCEPCIÓN: NO procesar errores de operaciones CRUD normales
          const isCrudOperation = error.config?.url?.includes('/customers/') && error.config?.method === 'delete'
          
          // Detectar si es error de tenant/tienda no encontrada (SOLO en recursos críticos)
          if (!isCrudOperation && data?.message && (
            data.message.toLowerCase().includes('tenant') ||
            data.message.toLowerCase().includes('tienda') ||
            data.message.toLowerCase().includes('warehouse')
          )) {
            console.error('Error: Recurso del tenant no encontrado')
            console.error('Detalles:', data.message)
            
            // Si es el health-check o recursos críticos, cerrar sesión
            if (error.config?.url?.includes('health-check') || 
                error.config?.url?.includes('warehouses')) {
              // Preservar configuraciones de UI antes de limpiar
              const tourCompleted = localStorage.getItem('pos_tour_completed')
              const tourSkipped = localStorage.getItem('pos_tour_skipped')
              
              localStorage.clear()
              
              // Restaurar configuraciones de UI
              if (tourCompleted) localStorage.setItem('pos_tour_completed', tourCompleted)
              if (tourSkipped) localStorage.setItem('pos_tour_skipped', tourSkipped)
              
              window.location.href = '/login?reason=tenant-error&message=Tu cuenta no está disponible. Por favor, contacta al soporte.'
              return Promise.reject(error)
            }
          }
          break
          
        case 500:
          // Error del servidor
          // Detectar si es error de base de datos/tenant inexistente
          // Detectar SOLO si es error de base de datos del tenant inexistente (no errores SQL normales)
          if (data?.message && (
            data.message.includes('Unknown database') ||
            data.message.includes('Connection refused') ||
            (data.message.toLowerCase().includes('tenant') && data.message.toLowerCase().includes('not found'))
          )) {
            console.error('Error crítico: Base de datos o tenant no existe')
            console.error('Detalles:', data.message)
            
            // Preservar configuraciones de UI antes de limpiar
            const tourCompleted = localStorage.getItem('pos_tour_completed')
            const tourSkipped = localStorage.getItem('pos_tour_skipped')
            
            // Limpiar sesión y redirigir al login
            localStorage.removeItem('authToken')
            localStorage.removeItem('user')
            localStorage.removeItem('loginTimestamp')
            localStorage.clear()
            
            // Restaurar configuraciones de UI
            if (tourCompleted) localStorage.setItem('pos_tour_completed', tourCompleted)
            if (tourSkipped) localStorage.setItem('pos_tour_skipped', tourSkipped)
            
            // Redirigir al login con mensaje
            window.location.href = '/login?reason=tenant-error&message=Tu cuenta no está disponible. Por favor, contacta al soporte.'
            return Promise.reject(error)
          }
          // console.error('Error interno del servidor')
          break
          
        default:
          // console.error('Error HTTP no manejado:', status, data)
      }
    } else if (error.request) {
      // Error de red - no hay respuesta del servidor
  // console.error('Error de conexión con el servidor')
    } else {
      // Error en la configuración de la request
  // console.error('Error en la configuración de la request:', error.message)
    }
    
    return Promise.reject(error)
  }
)

// Métodos helper para requests comunes
export const api = {
  // GET request
  get: (url, config = {}) => apiClient.get(url, config),
  
  // POST request
  post: (url, data = {}, config = {}) => apiClient.post(url, data, config),
  
  // PUT request
  put: (url, data = {}, config = {}) => apiClient.put(url, data, config),
  
  // PATCH request
  patch: (url, data = {}, config = {}) => apiClient.patch(url, data, config),
  
  // DELETE request
  delete: (url, config = {}) => apiClient.delete(url, config),
  
  // Upload file
  upload: (url, formData, config = {}) => {
    return apiClient.post(url, formData, {
      ...config,
      headers: {
        ...config.headers,
        'Content-Type': 'multipart/form-data'
      }
    })
  }
}

// Funciones de utilidad
export const setAuthToken = (token) => {
  if (token) {
    apiClient.defaults.headers.common['Authorization'] = `Bearer ${token}`
    localStorage.setItem('authToken', token)
  } else {
    delete apiClient.defaults.headers.common['Authorization']
    localStorage.removeItem('authToken')
  }
}

export const clearAuth = () => {
  delete apiClient.defaults.headers.common['Authorization']
  localStorage.removeItem('authToken')
  localStorage.removeItem('user')
}

// Configurar token al cargar el módulo
const savedToken = localStorage.getItem('authToken')
if (savedToken) {
  setAuthToken(savedToken)
}

export default apiClient