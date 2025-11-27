import { createRouter, createWebHistory } from 'vue-router'
import { requireAuth, redirectIfAuth, requireRole } from '../middleware/auth.js'
import authService from '../services/authService.js'

// Componentes
const LoginView = () => import('../components/LoginView.vue')
const SaasRegister = () => import('../views/SaasRegister.vue') // Importar el nuevo componente
const PosCompleto = () => import('../views/PosCompleto.vue')
const AdminDashboardView = () => import('../views/AdminDashboardView.vue')

const routes = [
  // Ruta de Registro SaaS
  {
    path: '/register',
    name: 'Register',
    component: SaasRegister,
    meta: {
      title: 'Crear Cuenta - 105 POS',
      requiresAuth: false
    }
  },
  // Ruta raíz - redirige según autenticación - TODOS van al POS
  {
    path: '/',
    redirect: (to) => {
      if (authService.isAuthenticated()) {
        return '/pos' // TODOS van al mismo POS
      }
      return '/login'
    }
  },

  // Login
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    beforeEnter: redirectIfAuth,
    meta: {
      title: 'Iniciar Sesión - 105 POS',
      requiresAuth: false
    }
  },

  // POS - Sistema completo para TODOS los roles
  {
    path: '/pos',
    name: 'POS',
    component: PosCompleto,
    beforeEnter: requireAuth, // Solo verificar autenticación, no roles específicos
    meta: {
      title: 'Sistema POS - 105 POS',
      requiresAuth: true
      // Removido roles array - todos los roles autenticados pueden acceder
    }
  },

  // Dashboard - Para administradores
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: PosCompleto,
    beforeEnter: requireAuth, // Solo verificar autenticación
    meta: {
      title: 'Dashboard - 105 POS',
      requiresAuth: true
      // Todos pueden acceder al dashboard, pero la UI se mostrará según permisos
    }
  },

  // Panel de Administrador - Monitoreo de IA
  {
    path: '/admin/ai-monitoring',
    name: 'AdminAIMonitoring',
    component: AdminDashboardView,
    beforeEnter: requireAuth,
    meta: {
      title: 'Monitoreo de IA - Admin',
      requiresAuth: true,
      roles: ['admin', 'Administrador'] // Soportar tanto inglés como español
    }
  },

  // Rutas específicas del POS (para navegación directa)
  {
    path: '/pos/:module',
    name: 'POSModule',
    component: PosCompleto,
    beforeEnter: requireAuth,
    meta: {
      title: 'Sistema POS - 105 POS',
      requiresAuth: true,
      roles: ['admin', 'cajero', 'vendedor']
    }
  },

  // Ruta 404
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('../components/NotFound.vue'),
    meta: {
      title: 'Página no encontrada - 105 POS'
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Guard global para títulos
router.beforeEach((to, from, next) => {
  // Actualizar título de la página
  if (to.meta.title) {
    document.title = to.meta.title
  }
  
  next()
})

// Guard para manejar errores de autenticación
router.beforeEach((to, from, next) => {
  // Si la ruta requiere autenticación y no está autenticado
  if (to.meta.requiresAuth && !authService.isAuthenticated()) {
    next('/login')
    return
  }
  
  // Si está autenticado pero no tiene el rol adecuado
  if (to.meta.roles && authService.isAuthenticated()) {
    const user = authService.getUser()
    const userRole = user.role?.name || user.role // Soportar tanto objeto como string
    
    console.log('🔍 [Router Guard] Verificando acceso a:', to.path)
    console.log('👤 [Router Guard] Usuario:', user.name, '| Rol:', userRole)
    console.log('🎯 [Router Guard] Roles permitidos:', to.meta.roles)
    
    if (!to.meta.roles.includes(userRole)) {
      console.log('❌ [Router Guard] Acceso denegado - redirigiendo...')
      // Redirigir según el rol del usuario
      if (userRole === 'admin') {
        next('/dashboard')
      } else {
        next('/pos')
      }
      return
    }
    
    console.log('✅ [Router Guard] Acceso permitido')
  }
  
  next()
})

export default router