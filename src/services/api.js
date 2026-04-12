// API Configuration
export const API_CONFIG = {
  BASE_URL: '/api',
  TIMEOUT: 10000,
  HEADERS: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
}

// Auth utilities
export const getAuthToken = () => {
  return localStorage.getItem('authToken')
}

export const setAuthToken = (token) => {
  localStorage.setItem('authToken', token)
}

export const removeAuthToken = () => {
  localStorage.removeItem('authToken')
}

// Get tenant ID from localStorage or URL
export const getTenantId = () => {
  // 1. Intentar obtener del usuario autenticado
  const userStr = localStorage.getItem('user')
  if (userStr) {
    try {
      const user = JSON.parse(userStr)
      // Si el usuario tiene un tenant_id, usarlo
      if (user.tenant_id) {
        return user.tenant_id
      }
    } catch (e) {
      console.warn('Error parsing user data:', e)
    }
  }

  // 2. Intentar extraer del subdominio (para desarrollo multi-tenant)
  if (window.location.hostname !== 'localhost' && window.location.hostname.includes('.')) {
    const subdomain = window.location.hostname.split('.')[0]
    return subdomain
  }

  // 3. Extraer de query params (?subdomain=XXX o ?tenant=XXX)
  const urlParams = new URLSearchParams(window.location.search)
  const tenantFromQuery = urlParams.get('subdomain') || urlParams.get('tenant')
  if (tenantFromQuery) {
    return tenantFromQuery
  }

  // 4. Buscar en la URL del onboarding (/onboarding/:tenantId)
  const pathParts = window.location.pathname.split('/')
  const onboardingIndex = pathParts.indexOf('onboarding')
  if (onboardingIndex !== -1 && pathParts[onboardingIndex + 1]) {
    return pathParts[onboardingIndex + 1]
  }

  // 5. Default: usar el primer tenant disponible o un valor por defecto
  return localStorage.getItem('tenant_id') || '105_pos_pro'
}

export const getAuthHeaders = () => {
  const token = getAuthToken()
  const tenantId = getTenantId()
  
  const headers = {
    ...API_CONFIG.HEADERS
  }

  if (token) {
    headers['Authorization'] = `Bearer ${token}`
  }

  if (tenantId) {
    headers['X-Tenant-Id'] = tenantId
  }

  return headers
}

// API response wrapper
export const handleApiResponse = async (response) => {
  const contentType = response.headers.get('content-type')
  
  // Si no es JSON, mostrar el texto de la respuesta
  if (!contentType || !contentType.includes('application/json')) {
    const text = await response.text().catch(() => 'Sin respuesta')
    console.error('Respuesta no-JSON del servidor:', {
      status: response.status,
      statusText: response.statusText,
      contentType,
      body: text.substring(0, 500) // Primeros 500 caracteres
    })
    throw new Error(`Error del servidor (${response.status}): ${response.statusText}`)
  }
  
  const data = await response.json().catch(() => ({ message: 'Error de conexión' }))
  
  if (!response.ok) {
    // Si hay errores de validación (422), mostrar errores específicos
    if (response.status === 422 && data.errors) {
      const errorMessages = Object.values(data.errors).flat().join(', ')
      throw new Error(`Error de validación: ${errorMessages}`)
    }
    
    // Si es error 429 (límite de IA), crear un error especial con los datos completos
    if (response.status === 429) {
      const error = new Error(data.message || 'Límite alcanzado')
      error.response = {
        status: 429,
        data: data
      }
      throw error
    }

    // MANEJO DE ERROR 403 (Suscripción Expirada)
    if (response.status === 403) {
      const error = new Error(data.message || 'Acceso denegado')
      error.response = {
        status: 403,
        data: data
      }
      
      // Detectar si es por suscripción expirada y redirigir
      if (data?.subscription_expired === true || (data?.message && (
          data.message.includes('suscripción ha finalizado') ||
          data.message.includes('suscripción ha expirado') || 
          data.message.includes('plan ha expirado') ||
          data.message.includes('renueva tu plan')
      ))) {
        // NO redirigir si ya estamos en rutas permitidas para usuarios expirados
        const allowedExpiredRoutes = ['/subscription-expired', '/select-plan', '/payment/success', '/payment/failure']
        const currentPath = window.location.pathname
        
        if (!allowedExpiredRoutes.includes(currentPath)) {
          // Evitar redirecciones múltiples con un flag temporal
          if (!window.__redirecting_to_expired) {
            window.__redirecting_to_expired = true
            setTimeout(() => {
              window.location.href = '/subscription-expired'
            }, 100)
          }
        } else {
        }
      }
      
      throw error
    }
    
    // Para otros errores, adjuntar respuesta para compatibilidad con Axios
    const error = new Error(data.message || `HTTP error! status: ${response.status}`)
    error.response = {
      status: response.status,
      data: data
    }
    throw error
  }
  
  return data
}

// Base API function
export const apiCall = async (endpoint, options = {}) => {
  const url = `${API_CONFIG.BASE_URL}${endpoint}`
  
  const headers = getAuthHeaders()
  const { silent, ...fetchOptions } = options // Extraer opción silent

  // Si el body es FormData, eliminar Content-Type para que el navegador lo establezca con el boundary correcto
  if (fetchOptions.body instanceof FormData) {
    delete headers['Content-Type']
  }

  const config = {
    headers,
    ...fetchOptions,
  }

  try {
    const response = await fetch(url, config)
    return await handleApiResponse(response)
  } catch (error) {
    // Solo mostrar error en consola si no es silencioso
    if (!silent) {
      console.error('API Call Error:', error)
    }
    throw error
  }
}

// Default API object
const api = {
  get: (endpoint, options = {}) => {
    // Procesar params y convertirlos a query string (compatibilidad con Axios)
    let url = endpoint
    if (options.params) {
      const queryParams = new URLSearchParams()
      for (const [key, value] of Object.entries(options.params)) {
        if (value !== undefined && value !== null) {
          queryParams.append(key, value)
        }
      }
      const queryString = queryParams.toString()
      if (queryString) {
        url = `${endpoint}${endpoint.includes('?') ? '&' : '?'}${queryString}`
      }
      // Eliminar params del options ya que lo procesamos
      const { params, ...restOptions } = options
      return apiCall(url, { method: 'GET', ...restOptions })
    }
    return apiCall(url, { method: 'GET', ...options })
  },
  
  post: (endpoint, data = {}, options = {}) => {
    // Si data es FormData, NO hacer stringify
    const body = data instanceof FormData ? data : JSON.stringify(data)
    
    return apiCall(endpoint, {
      method: 'POST',
      body,
      ...options
    })
  },
  
  put: (endpoint, data = {}, options = {}) => {
    return apiCall(endpoint, {
      method: 'PUT',
      body: JSON.stringify(data),
      ...options
    })
  },
  
  patch: (endpoint, data = {}, options = {}) => {
    return apiCall(endpoint, {
      method: 'PATCH',
      body: JSON.stringify(data),
      ...options
    })
  },
  
  delete: (endpoint, options = {}) => {
    return apiCall(endpoint, { method: 'DELETE', ...options })
  }
}

export default api