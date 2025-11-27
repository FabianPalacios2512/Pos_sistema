import axios from 'axios'

// Configuración base del cliente API
const apiClient = axios.create({
  baseURL: '/api',
  timeout: 15000, // 15 segundos para operaciones normales (AI puede tardar)
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
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
    
    // Log para debugging (solo en desarrollo)
    if (process.env.NODE_ENV === 'development') {
  /*
  console.log('API Request:', {
    method: config.method?.toUpperCase(),
    url: config.url,
    data: config.data,
    headers: config.headers,
    hasToken: !!token,
    tokenPreview: token ? token.substring(0, 20) + '...' : 'NO TOKEN'
  })
  */
    }
    
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
  console.log('API Response:', {
    status: response.status,
    url: response.config.url,
    data: response.data
  })
  */
    }
    
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
          console.log('🔒 Token expirado o inválido detectado')
          localStorage.removeItem('authToken')
          localStorage.removeItem('user')
          
          // Solo redirigir si no estamos ya en login
          if (window.location.pathname !== '/login') {
            // Redirigir con información de expiración
            window.location.href = '/login?reason=expired&message=Tu sesión ha expirado'
          }
          break
          
        case 403:
          // Sin permisos
          // console.warn('Acceso denegado:', data.message)
          break
          
        case 404:
          // Recurso no encontrado
          // console.warn('Recurso no encontrado:', error.config?.url)
          break
          
        case 422:
          // Errores de validación
          // console.warn('Errores de validación:', data.errors)
          break
          
        case 500:
          // Error del servidor
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