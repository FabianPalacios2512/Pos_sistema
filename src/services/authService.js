import apiClient from './apiClient.js'
import mockAuthService from './mockAuthService.js'
import { appStore } from '../store/appStore.js'

// Configuración para usar mock o API real
const USE_MOCK = false; // Cambiado a false para usar base de datos real

const authService = {
  // Login con cédula y contraseña
  async login(credentials) {
    try {
      let response;
      
      if (USE_MOCK) {
        response = { data: await mockAuthService.login(credentials) };
      } else {
        response = await apiClient.post('/login', {
          cc: credentials.cc, // Usar cédula como lo diseñamos
          password: credentials.password
        });
      }
      
      if (response.data.success && response.data.data?.token) {
        // Guardar token en localStorage
        localStorage.setItem('authToken', response.data.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.data.user));
        
        // Configurar token en el cliente API
        apiClient.defaults.headers.common['Authorization'] = `Bearer ${response.data.data.token}`;
        
        // 🚀 INICIALIZAR STORE GLOBAL DESPUÉS DEL LOGIN
        console.log('🏪 Inicializando datos globales después del login...')
        appStore.initialize().catch(error => {
          console.warn('⚠️ Error inicializando store:', error)
        })
        
        // Verificar que se guardó correctamente
        const savedToken = localStorage.getItem('authToken');
      }
      
      return response.data.data; // Retornar solo la data interna
    } catch (error) {
      if (USE_MOCK) {
        throw error;
      } else {
        throw error.response?.data || { message: 'Error de conexión' };
      }
    }
  },

  // Logout
  async logout() {
    try {
      if (USE_MOCK) {
        await mockAuthService.logout();
      } else {
        await apiClient.post('/logout');
      }
    } catch (error) {
      console.error('Error al hacer logout:', error);
    } finally {
      // Limpiar datos locales
      localStorage.removeItem('authToken');
      localStorage.removeItem('user');
      delete apiClient.defaults.headers.common['Authorization'];
    }
  },

  // Obtener usuario actual
  async getCurrentUser() {
    try {
      let response;
      
      if (USE_MOCK) {
        const token = this.getToken();
        response = { data: await mockAuthService.getCurrentUser(token) };
      } else {
        response = await apiClient.get('/me');
      }
      
      return response.data;
    } catch (error) {
      // Si falla, limpiar datos locales
      this.logout();
      if (USE_MOCK) {
        throw error;
      } else {
        throw error.response?.data || { message: 'Sesión expirada' };
      }
    }
  },

  // Verificar si está autenticado
  isAuthenticated() {
    const token = localStorage.getItem('authToken');
    return !!token;
  },

  // Obtener token
  getToken() {
    return localStorage.getItem('authToken');
  },

  // Obtener usuario de localStorage
  getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  },

  // Verificar permisos por rol
  hasRole(role) {
    const user = this.getUser();
    return user?.role === role;
  },

  // Verificar múltiples roles
  hasAnyRole(roles) {
    const user = this.getUser();
    return roles.includes(user?.role);
  },

  // Inicializar autenticación al cargar la app
  initializeAuth() {
    const token = this.getToken();
    if (token) {
      apiClient.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }
  },

  // Registrar nuevo usuario (solo admin)
  async register(userData) {
    try {
      let response;
      
      if (USE_MOCK) {
        response = { data: await mockAuthService.register(userData) };
      } else {
        response = await apiClient.post('/auth/register', userData);
      }
      
      return response.data;
    } catch (error) {
      if (USE_MOCK) {
        throw error;
      } else {
        throw error.response?.data || { message: 'Error al registrar usuario' };
      }
    }
  },

  // Cambiar contraseña
  async changePassword(passwords) {
    try {
      let response;
      
      if (USE_MOCK) {
        const token = this.getToken();
        response = { data: await mockAuthService.changePassword(token, passwords) };
      } else {
        response = await apiClient.post('/auth/change-password', passwords);
      }
      
      return response.data;
    } catch (error) {
      if (USE_MOCK) {
        throw error;
      } else {
        throw error.response?.data || { message: 'Error al cambiar contraseña' };
      }
    }
  },

  // Obtener todos los usuarios (solo admin)
  async getUsers() {
    try {
      let response;
      
      if (USE_MOCK) {
        response = { data: await mockAuthService.getUsers() };
      } else {
        response = await apiClient.get('/users');
      }
      
      return response.data;
    } catch (error) {
      if (USE_MOCK) {
        throw error;
      } else {
        throw error.response?.data || { message: 'Error al obtener usuarios' };
      }
    }
  },

  // Validar credenciales de administrador
  async validateAdminCredentials(email, password) {
    try {
      let response;
      if (USE_MOCK) {
        // En modo mock, verificar contra usuarios mock
        const isValid = await mockAuthService.validateAdminCredentials(email, password);
        response = { data: { valid: isValid } };
      } else {
        response = await apiClient.post('/auth/validate-admin', { email, password });
      }
      
      return response.data;
    } catch (error) {
      if (USE_MOCK) {
        return { valid: false };
      } else {
        throw error.response?.data || { message: 'Error validando credenciales' };
      }
    }
  }
};

export default authService;