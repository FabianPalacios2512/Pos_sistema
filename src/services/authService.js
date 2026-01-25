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
        // Detectar si estamos en el dominio central (105pos.pro) o en un subdominio de tenant
        const hostname = window.location.hostname;
        const isCentralDomain = hostname === '105pos.pro' || hostname === 'www.105pos.pro' || hostname === 'localhost';
        
        if (isCentralDomain) {
          // En dominio central: intentar como super admin
          try {
            response = await apiClient.post('/admin/login', {
              email: credentials.email,
              password: credentials.password
            });
            
            if (response.data.success && response.data.data?.token) {
              localStorage.setItem('authToken', response.data.data.token);
              localStorage.setItem('user', JSON.stringify(response.data.data.user));
              apiClient.defaults.headers.common['Authorization'] = `Bearer ${response.data.data.token}`;
              return response.data.data;
            }
          } catch (adminError) {
            // Si falla admin, intentar login normal
            response = await apiClient.post('/login', {
              email: credentials.email,
              password: credentials.password
            });
          }
        } else {
          // En subdominio de tenant: usar login normal directamente
          response = await apiClient.post('/login', {
            email: credentials.email,
            password: credentials.password
          });
        }
      }
      
      if (response.data.success && response.data.data?.token) {
        // Guardar token en localStorage
        localStorage.setItem('authToken', response.data.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.data.user));
        // Guardar timestamp de inicio de sesión para evitar bucles de logout inmediato en errores 401
        localStorage.setItem('loginTimestamp', Date.now());
        
        // Configurar token en el cliente API
        apiClient.defaults.headers.common['Authorization'] = `Bearer ${response.data.data.token}`;
        
        // 🚀 INICIALIZAR STORE GLOBAL DESPUÉS DEL LOGIN
        try {
          await appStore.initialize()
        } catch (error) {
          console.warn('⚠️ Error inicializando store:', error)
        }
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
      const user = this.getUser();
      
      if (USE_MOCK) {
        await mockAuthService.logout();
      } else {
        // Solo llamar logout API si NO es super admin
        if (user?.role !== 'superadmin' && !user?.is_super_admin) {
          await apiClient.post('/logout');
        } else {
          console.log('👑 [AuthService] Superadmin logout - Skip API call')
        }
      }
    } catch (error) {
      console.error('Error al hacer logout:', error);
    } finally {
      // 🔒 PRESERVAR configuraciones de UI que NO deben perderse entre sesiones
      const tourCompleted = localStorage.getItem('pos_tour_completed')
      const tourSkipped = localStorage.getItem('pos_tour_skipped')
      
      // Limpiar datos locales
      localStorage.removeItem('authToken');
      localStorage.removeItem('user');
      localStorage.removeItem('google_login'); // 🔥 Limpiar flag de login con Google
      localStorage.removeItem('onboarding_completed'); // 🔥 Limpiar flag de onboarding
      localStorage.removeItem('welcome_seen'); // 🔥 Limpiar flag de welcome
      delete apiClient.defaults.headers.common['Authorization'];
      
      // 🔒 RESTAURAR configuraciones de UI preservadas
      if (tourCompleted) localStorage.setItem('pos_tour_completed', tourCompleted)
      if (tourSkipped) localStorage.setItem('pos_tour_skipped', tourSkipped)
    }
  },

  // Obtener usuario actual
  async getCurrentUser() {
    try {
      let response;
      
      // 👑 Si es super admin, retornar datos de localStorage sin llamar API
      const localUser = this.getUser()
      if (localUser?.role === 'superadmin' || localUser?.is_super_admin) {
        console.log('👑 [AuthService] Superadmin detectado - NO llamando a /api/me')
        return {
          success: true,
          data: { user: localUser }
        }
      }
      
      if (USE_MOCK) {
        const token = this.getToken();
        response = { data: await mockAuthService.getCurrentUser(token) };
      } else {
        response = await apiClient.get('/me');
      }
      
      return response.data;
    } catch (error) {
      // Si falla, limpiar datos locales
      // ⛔ FIX: No hacer logout si es error 403 (Suscripción expirada) o 401 en rutas de renovación
      if (error.response?.status === 403) {
        console.log('⛔ [AuthService] Error 403 en getCurrentUser - Posible suscripción expirada. NO haciendo logout.')
        throw error
      }
      
      // También proteger contra 401 si estamos en proceso de renovación
      if (error.response?.status === 401) {
        const currentPath = window.location.pathname
        const allowedExpiredRoutes = ['/subscription-expired', '/select-plan', '/payment/success', '/payment/failure', '/admin/god-mode']
        if (allowedExpiredRoutes.includes(currentPath)) {
          console.log('⛔ [AuthService] Error 401 en ruta protegida - NO haciendo logout.')
          throw error
        }
      }
      
      // 👑 PROTECCIÓN: No hacer logout si es superadmin y solo falló una llamada API
      const localUser = this.getUser()
      if (localUser?.role === 'superadmin' || localUser?.is_super_admin) {
        console.warn('⚠️ [AuthService] Error en superadmin - NO haciendo logout')
        throw error
      }

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