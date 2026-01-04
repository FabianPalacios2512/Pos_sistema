import { createRouter, createWebHistory } from 'vue-router'
import { requireAuth, redirectIfAuth, requireRole } from '../middleware/auth.js'
import authService from '../services/authService.js'
import { appStore } from '../store/appStore.js'

// Componentes
const LoginView = () => import('../components/LoginView.vue')
const ForgotPasswordView = () => import('../components/ForgotPasswordView.vue')
const ResetPasswordView = () => import('../components/ResetPasswordView.vue')
const SaasRegister = () => import('../views/SaasRegister.vue')
const TermsConditions = () => import('../views/TermsConditions.vue')
const PrivacyPolicy = () => import('../views/PrivacyPolicy.vue')
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
    beforeEnter: (to, from, next) => {
      // 🔒 PROTECCIÓN: Solo permitir /register en app central
      const hostname = window.location.hostname
      const parts = hostname.split('.')
      
      // Dominios principales permitidos
      const mainDomains = ['localhost', '127.0.0.1', '105pos.pro', 'www.105pos.pro']
      const isMainDomain = mainDomains.includes(hostname)
      
      // Si es un subdominio (no dominio principal), bloquear
      if (!isMainDomain && parts.length > 2) {
        console.warn('⚠️ Acceso a /register bloqueado desde subdominio')
        next('/login')
        return
      }
      
      next()
    },
    meta: {
      title: 'Crear Cuenta - 105 POS',
      requiresAuth: false
    }
  },
  // Ruta de Términos y Condiciones
  {
    path: '/terminos-condiciones',
    name: 'TermsConditions',
    component: TermsConditions,
    meta: {
      title: 'Términos y Condiciones - 105 POS',
      requiresAuth: false
    }
  },
  // Ruta de Política de Privacidad
  {
    path: '/politica-privacidad',
    name: 'PrivacyPolicy',
    component: PrivacyPolicy,
    meta: {
      title: 'Política de Privacidad - 105 POS',
      requiresAuth: false
    }
  },
  // 🔐 Recuperación de Contraseña
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: ForgotPasswordView,
    meta: {
      title: 'Recuperar Contraseña - 105 POS',
      requiresAuth: false
    }
  },
  {
    path: '/reset-password',
    name: 'ResetPassword',
    component: ResetPasswordView,
    meta: {
      title: 'Nueva Contraseña - 105 POS',
      requiresAuth: false
    }
  },
  // Ruta de Selección de Plan
  {
    path: '/select-plan',
    name: 'SelectPlan',
    component: () => import('../views/PlanSelection.vue'),
    meta: {
      title: 'Selecciona Tu Plan - 105 POS',
      requiresAuth: false
    }
  },
  // Rutas de resultado de pago
  // 🔍 Ruta de verificación intermedia (ePayco redirige aquí SIEMPRE)
  {
    path: '/payment/verify',
    name: 'PaymentVerification',
    component: () => import('../views/PaymentVerification.vue'),
    meta: {
      title: 'Verificando Pago - 105 POS',
      requiresAuth: false
    }
  },
  {
    path: '/payment/success',
    name: 'PaymentSuccess',
    component: () => import('../views/PaymentSuccess.vue'),
    meta: {
      title: 'Pago Exitoso - 105 POS',
      requiresAuth: false
    }
  },
  {
    path: '/payment/failure',
    name: 'PaymentFailure',
    component: () => import('../views/PaymentFailure.vue'),
    meta: {
      title: 'Pago Rechazado - 105 POS',
      requiresAuth: false
    }
  },
  {
    path: '/payment/pending',
    name: 'PaymentPending',
    component: () => import('../views/PaymentSuccess.vue'), // Usa la misma vista de éxito
    meta: {
      title: 'Pago Pendiente - 105 POS',
      requiresAuth: false
    }
  },
  // 🔍 TEMPORAL - DEBUG para detectar parámetros de Wompi
  {
    path: '/payment/debug',
    name: 'PaymentDebug',
    component: () => import('../views/PaymentDebug.vue'),
    meta: {
      title: 'Debug - Parámetros de Pago',
      requiresAuth: false
    }
  },
  // Ruta raíz - Landing page SaaS (Registro)
  {
    path: '/',
    redirect: (to) => {
      if (authService.isAuthenticated()) {
        return '/pos' // Si ya está autenticado, va al POS
      }
      return '/register' // Landing page = Registro
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

  // Dashboard - Redirige al POS (vista principal del sistema)
  {
    path: '/dashboard',
    name: 'Dashboard',
    redirect: '/pos', // Dashboard ahora redirige al POS
    meta: {
      title: 'Dashboard - 105 POS',
      requiresAuth: true
    }
  },

  // Admin Dashboard Real - Para visualización de métricas
  {
    path: '/admin-dashboard',
    name: 'AdminDashboard',
    component: AdminDashboardView,
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

  // Ruta de Upgrade (accesible siempre, incluso con trial expirado)
  {
    path: '/upgrade',
    name: 'Upgrade',
    component: () => import('../views/UpgradeView.vue'),
    meta: {
      title: 'Actualizar Plan - 105 POS',
      requiresAuth: false, // Accesible sin auth para mostrar planes
      allowExpiredTrial: true, // Flag especial para bypass del middleware de trial
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
router.beforeEach(async (to, from, next) => {
  
  // Excluir SOLO rutas públicas reales (login, register, catalog, select-plan, payment/*, términos, etc.)
  const publicRoutes = [
    '/login', 
    '/register', 
    '/catalog', 
    '/select-plan',  // ✅ CRÍTICO: Permitir acceso a selección de planes para renovación
    '/payment/success', 
    '/payment/failure', 
    '/payment/pending',
    '/terminos-condiciones',
    '/politica-privacidad',
    '/forgot-password',
    '/reset-password'
  ]
  
  // Si es una ruta pública real, permitir acceso sin verificar onboarding
  if (publicRoutes.includes(to.path)) {
    next()
    return
  }

  // Si está autenticado, verificar el flujo welcome → onboarding
  if (authService.isAuthenticated()) {
    // ✅ EXCEPCIÓN: Super admins NO pasan por onboarding (no tienen tenant)
    const user = authService.getUser()
    if (user?.role === 'superadmin') {
      console.log('👑 Super Admin detectado - omitiendo validación de onboarding')
      next()
      return
    }
    
    console.log('🔍 Router Guard - Navegando de', from.path, 'a', to.path)
    console.log('🔍 Router Guard - isSubscriptionExpired:', appStore.isSubscriptionExpired)
    
    // ⛔ NUEVO FLUJO: Ya NO bloquear rutas cuando la suscripción expira
    // El modal aparecerá automáticamente en el POS y bloqueará el acceso
    // Solo cargar datos si no están cargados
    if (!appStore.systemSettings || Object.keys(appStore.systemSettings).length === 0) {
      await appStore.loadSystemSettings()
      console.log('✅ SystemSettings cargados, isSubscriptionExpired:', appStore.isSubscriptionExpired)
    }
    
    // 🔥 PRIORIDAD MÁXIMA: Si suscripción expirada, ir directo al POS
    // El modal se encargará de bloquear todo
    if (appStore.isSubscriptionExpired) {
      console.log('🔥 Suscripción expirada - Forzando acceso a /pos para mostrar modal')
      if (to.path !== '/pos' && !to.path.startsWith('/payment/')) {
        next('/pos')
        return
      }
      next()
      return
    }
    
    // Permitir navegación normal - el modal se encargará de bloquear si es necesario

    const onboardingCompleted = appStore.systemSettings.onboarding_completed || false

    // 🎯 REGLA PRINCIPAL: Si ya completó onboarding en BD, permitir acceso normal
    if (onboardingCompleted) {
      // Si completó onboarding pero está intentando acceder a /welcome o /onboarding, redirigir a /pos
      if (to.path === '/welcome' || to.path === '/onboarding') {
        console.log('✅ Onboarding ya completado - redirigiendo a /pos')
        next('/pos')
        return
      }
      // Permitir acceso a cualquier otra ruta protegida
      next()
      return
    }

    // 🚨 CRÍTICO: Si NO ha completado onboarding, FORZAR completarlo
    // No importa si ya vio welcome o no - DEBE completar configuración
    console.log('⚠️ Onboarding incompleto - usuario DEBE configurar el sistema')
    
    // Permitir acceso SOLO a /welcome y /onboarding
    const allowedRoutes = ['/welcome', '/onboarding']
    if (!allowedRoutes.includes(to.path)) {
      // Si no ha visto welcome, mandarlo ahí primero
      const welcomeSeen = localStorage.getItem('welcome_seen')
      if (!welcomeSeen) {
        console.log('🎯 Primera vez - redirigiendo a welcome')
        next('/welcome')
        return
      }
      
      // Si ya vio welcome, mandarlo a onboarding
      console.log('🎯 FORZANDO configuración - redirigiendo a onboarding')
      next('/onboarding')
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

    // Normalizar roles para comparación flexible
    const normalizedUserRole = userRole?.toLowerCase()
    const normalizedAllowedRoles = to.meta.roles.map(r => r.toLowerCase())
    
    // Mapeo de roles equivalentes
    const roleEquivalents = {
      'administrador': ['admin', 'administrador'],
      'admin': ['admin', 'administrador'],
      'superadmin': ['superadmin', 'super admin', 'admin', 'administrador']
    }
    
    // Verificar si el rol del usuario tiene acceso
    let hasAccess = normalizedAllowedRoles.includes(normalizedUserRole)
    
    // Si no tiene acceso directo, verificar equivalencias
    if (!hasAccess && roleEquivalents[normalizedUserRole]) {
      hasAccess = roleEquivalents[normalizedUserRole].some(equiv => 
        normalizedAllowedRoles.includes(equiv)
      )
    }

    if (!hasAccess) {
      console.log('❌ [Router Guard] Acceso denegado - redirigiendo...')
      // Redirigir según el rol del usuario
      if (normalizedUserRole === 'admin' || normalizedUserRole === 'administrador') {
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