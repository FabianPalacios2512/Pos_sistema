// API Configuration
export const API_CONFIG = {
  BASE_URL: 'http://localhost:8000/api',
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

export const getAuthHeaders = () => {
  const token = getAuthToken()
  
  if (token) {
    return {
      ...API_CONFIG.HEADERS,
      'Authorization': `Bearer ${token}`
    }
  } else {
    return API_CONFIG.HEADERS
  }
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
    
    throw new Error(data.message || `HTTP error! status: ${response.status}`)
  }
  
  return data
}

// Base API function
export const apiCall = async (endpoint, options = {}) => {
  const url = `${API_CONFIG.BASE_URL}${endpoint}`
  
  const headers = getAuthHeaders()
  const config = {
    headers,
    ...options,
  }

  try {
    const response = await fetch(url, config)
    return await handleApiResponse(response)
  } catch (error) {
    console.error('❌ API Call Error:', error)
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