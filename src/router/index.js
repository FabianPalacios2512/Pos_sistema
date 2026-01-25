import { createRouter, createWebHistory } from 'vue-router'
import { requireAuth, redirectIfAuth, requireRole } from '../middleware/auth.js'
import authService from '../services/authService.js'
import { appStore } from '../store/appStore.js'
import apiClient from '../services/apiClient.js'
import axios from 'axios'

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

  // 🎯 Portal Público de Créditos (SIN autenticación)
  {
    path: '/mi-credito',
    name: 'CreditPortal',
    component: () => import('../views/CreditPortalPublic.vue'),
    meta: {
      title: 'Mi Crédito - Portal de Cliente',
      requiresAuth: false,
      public: true
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
  // Alias para /admin/god-mode/login (redirect automático)
  {
    path: '/admin/god-mode/login',
    redirect: '/admin/god-mode'
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

// 🔑 GUARD PRIORITARIO: Capturar token de URL (para cross-domain auth)
router.beforeEach((to, from, next) => {
  const authToken = to.query.auth_token
  const userData = to.query.user_data
  
  if (authToken) {
    console.log('🔑 Token detectado en URL - guardando en localStorage')
    
    // Decodificar y guardar token
    const decodedToken = decodeURIComponent(authToken)
    localStorage.setItem('authToken', decodedToken)
    localStorage.setItem('loginTimestamp', Date.now().toString())
    
    // Guardar usuario si viene
    if (userData) {
      try {
        const user = JSON.parse(decodeURIComponent(userData))
        localStorage.setItem('user', JSON.stringify(user))
        console.log('👤 Usuario guardado:', user.name || user.email)
      } catch (e) {
        console.warn('⚠️ No se pudo parsear user_data')
      }
    }
    
    // Configurar axios con el token (axios ya está importado arriba)
    axios.defaults.headers.common['Authorization'] = `Bearer ${decodedToken}`
    
    // Limpiar URL quitando los params de auth (mantener otros params si existen)
    const cleanQuery = { ...to.query }
    delete cleanQuery.auth_token
    delete cleanQuery.user_data
    
    // Redirigir a la misma ruta pero sin los params de auth
    next({ path: to.path, query: cleanQuery, replace: true })
    return
  }
  
  next()
})

// Guard global para títulos
router.beforeEach((to, from, next) => {
  // Actualizar título de la página
  if (to.meta.title) {
    document.title = to.meta.title
  }

  next()
})

// 🔥 GUARD CRÍTICO: Verificar si el tenant tiene plan válido ANTES de permitir acceso al POS
router.beforeEach(async (to, from, next) => {
  // Solo verificar en rutas protegidas (no públicas)
  const publicRoutes = [
    '/login', 
    '/register', 
    '/catalog', 
    '/select-plan',
    '/payment/success', 
    '/payment/failure', 
    '/payment/pending',
    '/payment/verify',
    '/terminos-condiciones',
    '/politica-privacidad',
    '/forgot-password',
    '/reset-password',
    '/welcome',
    '/onboarding'
  ]
  
  // Si es ruta pública, no verificar plan
  if (publicRoutes.includes(to.path) || to.path.startsWith('/payment/')) {
    next()
    return
  }
  
  // Si no está autenticado, dejar que otros guards manejen
  if (!authService.isAuthenticated()) {
    next()
    return
  }
  
  // 🛡️ Solo verificar plan si estamos en un subdominio de tenant (no en dominio central)
  const hostname = window.location.hostname
  const isCentralDomain = hostname === '105pos.pro' || hostname === 'www.105pos.pro' || hostname === 'localhost' || hostname === '127.0.0.1'
  
  if (isCentralDomain) {
    // En dominio central, permitir navegación normal
    next()
    return
  }
  
  // 🔥 Estamos en un subdominio de tenant - VERIFICAR PLAN
  try {
    const response = await apiClient.get('/tenant/info')
    const tenant = response.data?.tenant || response.data
    
    const validPlans = ['basic', 'premium', 'enterprise', 'free_trial']
    const planType = tenant?.plan_type || tenant?.plan || 'pending'
    const subscriptionStatus = tenant?.subscription_status || 'pending'
    
    // Si el plan NO es válido o la suscripción está pendiente
    if (!validPlans.includes(planType) || subscriptionStatus === 'pending' || planType === 'pending') {
      console.log('🚨 [Router Guard] Tenant sin plan válido, redirigiendo a select-plan')
      
      const subdomain = tenant?.id || tenant?.subdomain || ''
      const companyName = tenant?.company_name || tenant?.business_name || tenant?.name || ''
      
      const isLocalhost = window.location.hostname.includes('localhost') || window.location.hostname === '127.0.0.1'
      const baseUrl = isLocalhost ? `http://localhost:${window.location.port || 3000}` : 'https://105pos.pro'
      const params = new URLSearchParams()
      if (subdomain) params.append('tenant_id', subdomain)
      if (subdomain) params.append('subdomain', subdomain)
      if (companyName) params.append('company', companyName)
      
      // 🔑 CRÍTICO: Pasar el token para que PlanSelection pueda usarlo después
      const currentToken = localStorage.getItem('authToken')
      if (currentToken) {
        params.append('auth_token', encodeURIComponent(currentToken))
      }
      
      // Redirigir al dominio central para seleccionar plan
      window.location.href = `${baseUrl}/select-plan?${params.toString()}`
      return // No llamar next() - estamos redirigiendo con window.location
    }
  } catch (error) {
    // 🛑 Si hay error 401, el token es inválido - limpiar y redirigir a login
    if (error.response?.status === 401) {
      console.warn('⚠️ [Router Guard] Token inválido, limpiando y redirigiendo a login')
      localStorage.removeItem('authToken')
      localStorage.removeItem('user')
      localStorage.removeItem('sanctum_token')
      next('/login')
      return
    }
    // Para otros errores, permitir navegación (puede ser error de red temporal)
    console.warn('⚠️ [Router Guard] Error verificando plan de tenant:', error)
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
    if (user?.role === 'superadmin' || user?.is_super_admin) {
      // console.log('👑 Super Admin detectado - omitiendo validación de onboarding y systemSettings')
      next()
      return
    }
    
    // ⛔ NUEVO FLUJO: Ya NO bloquear rutas cuando la suscripción expira
    // El modal aparecerá automáticamente en el POS y bloqueará el acceso
    
    // 🔧 FIX: SIEMPRE recargar systemSettings para asegurar datos frescos
    // Esto evita que el router use datos stale o undefined
    try {
      await appStore.loadSystemSettings(true) // force = true
    } catch (error) {
      // Continuar navegación aunque falle (puede ser superadmin o admin central)
      // console.error('⚠️ [Router] Error cargando systemSettings:', error)
    }
    
    // 🔥 PRIORIDAD MÁXIMA: Si suscripción expirada, ir directo al POS
    // El modal se encargará de bloquear todo
    if (appStore.isSubscriptionExpired) {
      if (to.path !== '/pos' && !to.path.startsWith('/payment/')) {
        next('/pos')
        return
      }
      next()
      return
    }
    
    // Permitir navegación normal - el modal se encargará de bloquear si es necesario

    // 🔥 PRIORIDAD: Verificar localStorage primero (más rápido y evita race conditions)
    const localOnboardingCompleted = localStorage.getItem('onboarding_completed') === 'true'
    
    // 🔧 Verificar onboarding_completed del backend (puede ser boolean o int 0/1)
    const backendOnboardingCompleted = appStore.systemSettings?.onboarding_completed === true || 
                                       appStore.systemSettings?.onboarding_completed === 1 ||
                                       appStore.systemSettings?.onboarding_completed === '1'
    
    const onboardingCompleted = localOnboardingCompleted || backendOnboardingCompleted

    // 🎯 REGLA PRINCIPAL: Si ya completó onboarding (en BD o localStorage), permitir acceso normal
    if (onboardingCompleted) {
      // 🔒 Sincronizar localStorage con backend si no estaba en sync
      if (!localOnboardingCompleted && backendOnboardingCompleted) {
        localStorage.setItem('onboarding_completed', 'true')
      }
      
      // 🛡️ PROTECCIÓN: Sincronizar backend si localStorage dice que completó pero backend no
      if (localOnboardingCompleted && !backendOnboardingCompleted) {
        // Forzar actualización en backend para prevenir loops
        try {
          await axios.post('/api/system-settings', { onboarding_completed: true })
          console.log('✅ Onboarding sincronizado con backend')
        } catch (err) {
          console.warn('⚠️ No se pudo sincronizar onboarding con backend:', err)
        }
      }
      
      // Si completó onboarding pero está intentando acceder a /welcome o /onboarding, redirigir a /pos
      if (to.path === '/welcome' || to.path === '/onboarding') {
        next('/pos')
        return
      }
      // Permitir acceso a cualquier otra ruta protegida
      next()
      return
    }

    // 🚨 CRÍTICO: Si NO ha completado onboarding, FORZAR completarlo
    
    // Permitir acceso SOLO a /welcome y /onboarding
    const allowedRoutes = ['/welcome', '/onboarding']
    if (!allowedRoutes.includes(to.path)) {
      // Si no ha visto welcome, mandarlo ahí primero
      const welcomeSeen = localStorage.getItem('welcome_seen')
      if (!welcomeSeen) {
        next('/welcome')
        return
      }
      
      // Si ya vio welcome, mandarlo a onboarding
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
      // Redirigir según el rol del usuario
      if (normalizedUserRole === 'admin' || normalizedUserRole === 'administrador') {
        next('/dashboard')
      } else {
        next('/pos')
      }
      return
    }
  }

  next()
})

export default router