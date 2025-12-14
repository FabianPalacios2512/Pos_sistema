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
    
    throw new Error(data.message || `HTTP error! status: ${response.status}`)
  }
  
  return data
}

// Base API function
export const apiCall = async (endpoint, options = {}) => {
  const url = `${API_CONFIG.BASE_URL}${endpoint}`
  
  const headers = getAuthHeaders()
  const { silent, ...fetchOptions } = options // Extraer opción silent
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
      console.error('❌ API Call Error:', error)
    }
    throw error
  }
}

// Default API object
const api = {
  get: (endpoint, options = {}) => {
    return apiCall(endpoint, { method: 'GET', ...options })
  },
  
  post: (endpoint, data = {}, options = {}) => {
    return apiCall(endpoint, {
      method: 'POST',
      body: JSON.stringify(data),
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