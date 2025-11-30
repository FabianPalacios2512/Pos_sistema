import { createRouter, createWebHistory } from 'vue-router'
import { requireAuth, redirectIfAuth, requireRole } from '../middleware/auth.js'
import authService from '../services/authService.js'

// Componentes
const LoginView = () => import('../components/LoginView.vue')
const SaasRegister = () => import('../views/SaasRegister.vue')
const WelcomeIntro = () => import('../views/WelcomeIntro.vue')
const InitialOnboardingView = () => import('../views/InitialOnboardingView.vue')
const PosCompleto = () => import('../views/PosCompleto.vue')
const AdminDashboardView = () => import('../views/AdminDashboardView.vue')
const GodModeAdminPanel = () => import('../components/admin/GodModeAdminPanel.vue')
const PublicCatalog = () => import('../views/PublicCatalog.vue')

const routes = [
  // Ruta de Pantalla de Bienvenida (Intro)
  {
    path: '/welcome',
    name: 'Welcome',
    component: WelcomeIntro,
    beforeEnter: requireAuth,
    meta: {
      title: '¡Bienvenido a 105 POS!',
      requiresAuth: true
    }
  },
  // Ruta de Onboarding Inicial (Primera configuración)
  {
    path: '/onboarding',
    name: 'Onboarding',
    component: InitialOnboardingView,
    beforeEnter: requireAuth,
    meta: {
      title: 'Configuración Inicial - 105 POS',
      requiresAuth: true
    }
  },
  // Ruta Pública del Catálogo (SIN autenticación)
  {
    path: '/catalog',
    name: 'PublicCatalog',
    component: PublicCatalog,
    meta: {
      title: 'Catálogo Online - Tienda',
      requiresAuth: false,
      public: true // Marca como ruta pública
    }
  },

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

  // Panel Super Admin - Gestión de Tenants (GOD MODE)
  {
    path: '/admin/god-mode',
    name: 'GodModeAdmin',
    component: GodModeAdminPanel,
    beforeEnter: requireAuth,
    meta: {
      title: 'Super Admin - Gestión de Tenants',
      requiresAuth: true,
      roles: ['admin', 'Administrador', 'superadmin'] // Super admins y admins pueden acceder
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

// Guard para redirección a onboarding (primera vez)
router.beforeEach((to, from, next) => {
  // Excluir SOLO rutas públicas reales (login, register, catalog)
  const publicRoutes = ['/login', '/register', '/catalog']
  
  // Si es una ruta pública real, permitir acceso sin verificar onboarding
  if (publicRoutes.includes(to.path)) {
    next()
    return
  }

  // Si está autenticado, verificar el flujo welcome → onboarding
  if (authService.isAuthenticated()) {
    const onboardingCompleted = localStorage.getItem('onboarding_completed')
    const welcomeSeen = localStorage.getItem('welcome_seen')

    // 🎯 REGLA 1: Si no ha visto welcome, SIEMPRE redirigir a /welcome (incluso si viene de /onboarding)
    if (!welcomeSeen && to.path !== '/welcome') {
      console.log('🎯 Primera vez detectada - redirigiendo a welcome')
      next('/welcome')
      return
    }

    // 🎯 REGLA 2: Si ya vio welcome pero no completó onboarding, redirigir a /onboarding
    if (welcomeSeen && !onboardingCompleted && to.path !== '/onboarding') {
      console.log('🎯 Welcome visto, continuando con onboarding')
      next('/onboarding')
      return
    }

    // 🎯 REGLA 3: Si ya completó onboarding, permitir acceso a cualquier ruta protegida
    if (onboardingCompleted) {
      next()
      return
    }
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