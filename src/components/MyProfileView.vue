<template>
  <!-- 🎨 Mi Perfil - Diseño Ejecutivo SaaS Dashboard -->
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-4 md:px-8">
    <div class="p-4 lg:p-6 max-w-6xl mx-auto space-y-5 pb-8 animate-fade-in">
      
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Configuración de cuenta</h1>
          <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">Administra tu información personal y preferencias de seguridad</p>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="w-8 h-8 border-2 border-slate-200 dark:border-zinc-700 border-t-slate-600 dark:border-t-slate-400 rounded-full animate-spin"></div>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- COLUMNA IZQUIERDA: Cuenta + Seguridad -->
        <div class="lg:col-span-1 space-y-5">
          
          <!-- Tarjeta: Cuenta -->
          <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm p-5">
            <div class="flex flex-col items-center text-center">
              <!-- Avatar -->
              <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center border-2 border-gray-200 dark:border-zinc-700 mb-4">
                <span class="text-slate-600 dark:text-zinc-300 font-semibold text-2xl">{{ userInitials }}</span>
              </div>
              
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ currentUser?.name || 'Usuario' }}</h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">{{ currentUser?.email || '' }}</p>
              
              <div class="flex items-center justify-center gap-2 mt-3 flex-wrap">
                <span class="px-2.5 py-1 text-xs font-medium bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 rounded-full">
                  {{ currentUser?.role?.name || 'Usuario' }}
                </span>
                <span class="px-2.5 py-1 text-xs font-medium bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 rounded-full capitalize">
                  {{ planDisplayName }}
                </span>
                <span 
                  class="px-2.5 py-1 text-xs font-medium rounded-full border"
                  :class="userIsActive 
                    ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' 
                    : 'bg-gray-50 dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 border-gray-200 dark:border-zinc-700'"
                >
                  {{ userIsActive ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Tarjeta: Seguridad -->
          <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide mb-4">Seguridad</h2>
            
            <!-- Usuario de Google -->
            <div v-if="isGoogleUser" class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 dark:border-zinc-700 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                  <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                  <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                  <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                  <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
              </div>
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                  <span class="text-sm font-medium text-gray-900 dark:text-white">Google</span>
                  <span class="px-1.5 py-0.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[10px] font-medium rounded border border-blue-200 dark:border-blue-800">
                    Conectado
                  </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-zinc-400">Tu contraseña es administrada por Google</p>
                <a href="https://myaccount.google.com/security" target="_blank" class="inline-flex items-center gap-1 text-xs text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white mt-2 transition-colors">
                  Administrar seguridad
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
              </div>
            </div>

            <!-- Usuario de Email/Contraseña -->
            <div v-else>
              <form @submit.prevent="changePassword" class="space-y-3">
                <div>
                  <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Contraseña actual</label>
                  <div class="relative">
                    <input v-model="passwordData.current_password" :type="showCurrentPassword ? 'text' : 'password'" required class="w-full px-3 py-2 pr-9 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-slate-500" placeholder="••••••••"/>
                    <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Nueva contraseña</label>
                  <div class="relative">
                    <input v-model="passwordData.new_password" :type="showNewPassword ? 'text' : 'password'" required minlength="8" class="w-full px-3 py-2 pr-9 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-slate-500" placeholder="Mínimo 8 caracteres"/>
                    <button type="button" @click="showNewPassword = !showNewPassword" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Confirmar contraseña</label>
                  <div class="relative">
                    <input v-model="passwordData.confirm_password" :type="showConfirmPassword ? 'text' : 'password'" required minlength="8" class="w-full px-3 py-2 pr-9 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-slate-500" placeholder="Repite la contraseña"/>
                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </div>
                
                <!-- Indicador de fortaleza -->
                <div v-if="passwordData.new_password" class="flex items-center gap-2">
                  <div class="flex gap-1 flex-1">
                    <div class="h-1 flex-1 rounded-full transition-all" :class="passwordStrength >= 33 ? passwordStrengthClass : 'bg-gray-200 dark:bg-zinc-700'"></div>
                    <div class="h-1 flex-1 rounded-full transition-all" :class="passwordStrength >= 66 ? passwordStrengthClass : 'bg-gray-200 dark:bg-zinc-700'"></div>
                    <div class="h-1 flex-1 rounded-full transition-all" :class="passwordStrength >= 100 ? passwordStrengthClass : 'bg-gray-200 dark:bg-zinc-700'"></div>
                  </div>
                  <span class="text-[10px] font-medium" :class="passwordStrengthTextClass">{{ passwordStrengthText }}</span>
                </div>
                
                <p v-if="passwordData.confirm_password && passwordData.new_password !== passwordData.confirm_password" class="text-xs text-rose-600 dark:text-rose-400">Las contraseñas no coinciden</p>
                
                <button type="submit" :disabled="savingPassword || !canChangePassword" class="w-full px-4 py-2 bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 dark:hover:bg-slate-600 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-all flex items-center justify-center gap-2">
                  <svg v-if="savingPassword" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                  {{ savingPassword ? 'Actualizando...' : 'Cambiar contraseña' }}
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- COLUMNA DERECHA: Información Personal (más ancha) -->
        <div class="lg:col-span-2">
          <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800">
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Información personal</h2>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Datos de contacto y facturación</p>
            </div>
            
            <form @submit.prevent="saveProfile" class="p-5">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1.5">Nombre completo</label>
                  <input v-model="formData.name" type="text" required class="w-full px-3 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-slate-500" placeholder="Ingresa tu nombre"/>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1.5">Correo electrónico</label>
                  <input v-model="formData.email" type="email" disabled class="w-full px-3 py-2.5 text-sm border border-gray-100 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-800/50 text-gray-400 dark:text-zinc-500 rounded-lg cursor-not-allowed"/>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1.5">Documento de identidad</label>
                  <input v-model="formData.cc" type="text" class="w-full px-3 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-slate-500" placeholder="Número de cédula o NIT"/>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1.5">Teléfono de contacto</label>
                  <input v-model="formData.phone" type="tel" class="w-full px-3 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-slate-500" placeholder="+57 300 000 0000"/>
                </div>
              </div>
              
              <div class="flex justify-end pt-4 mt-4 border-t border-gray-100 dark:border-zinc-800">
                <button type="submit" :disabled="savingProfile" class="px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 dark:hover:bg-slate-600 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-all flex items-center gap-2">
                  <svg v-if="savingProfile" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                  {{ savingProfile ? 'Guardando...' : 'Guardar cambios' }}
                </button>
              </div>
            </form>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAuth } from '../store/auth.js'
import { appStore } from '../store/appStore.js'
import usersService from '../services/usersService.js'

// Usar el composable de autenticación
const auth = useAuth()

// Estado
const loading = ref(true)
const savingProfile = ref(false)
const savingPassword = ref(false)
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

// Usuario actual
const currentUser = computed(() => auth.user.value)
const tenantPlan = computed(() => appStore.tenantPlan || 'free_trial')

// Detectar si el usuario inició sesión con Google
// Chequeamos múltiples fuentes: google_id del usuario, o flag en localStorage
const isGoogleUser = computed(() => {
  // 1. Verificar google_id en el objeto de usuario
  if (currentUser.value?.google_id) return true
  
  // 2. Verificar flag en localStorage (guardado al hacer login con Google)
  if (localStorage.getItem('google_login') === 'true') return true
  
  return false
})

// El usuario siempre está activo si está logueado
const userIsActive = computed(() => {
  // Si el usuario puede iniciar sesión, está activo
  return currentUser.value?.active === true || currentUser.value?.active === 1 || true
})

// Nombre del plan para mostrar
const planDisplayName = computed(() => {
  const names = {
    'free_trial': 'Prueba Gratuita',
    'free': 'Gratuito',
    'basic': 'Básico',
    'pro': 'Premium',
    'premium': 'Premium',
    'enterprise': 'Enterprise'
  }
  return names[tenantPlan.value] || tenantPlan.value
})

// Formulario de perfil
const formData = reactive({
  name: '',
  email: '',
  cc: '',
  phone: ''
})

// Formulario de contraseña
const passwordData = reactive({
  current_password: '',
  new_password: '',
  confirm_password: ''
})

// Computados
const userInitials = computed(() => {
  if (!currentUser.value?.name) return 'U'
  return currentUser.value.name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .substring(0, 2)
})

const passwordStrength = computed(() => {
  const pwd = passwordData.new_password
  if (!pwd) return 0
  let strength = 0
  if (pwd.length >= 8) strength += 33
  if (/[A-Z]/.test(pwd)) strength += 33
  if (/[0-9]/.test(pwd)) strength += 34
  return strength
})

const passwordStrengthClass = computed(() => {
  if (passwordStrength.value < 33) return 'bg-rose-500'
  if (passwordStrength.value < 66) return 'bg-amber-500'
  return 'bg-emerald-500'
})

const passwordStrengthText = computed(() => {
  if (passwordStrength.value < 33) return 'Débil'
  if (passwordStrength.value < 66) return 'Media'
  return 'Fuerte'
})

const passwordStrengthTextClass = computed(() => {
  if (passwordStrength.value < 33) return 'text-rose-600 dark:text-rose-400'
  if (passwordStrength.value < 66) return 'text-amber-600 dark:text-amber-400'
  return 'text-emerald-600 dark:text-emerald-400'
})

const canChangePassword = computed(() => {
  return passwordData.current_password &&
         passwordData.new_password &&
         passwordData.confirm_password &&
         passwordData.new_password === passwordData.confirm_password &&
         passwordData.new_password.length >= 8
})

// Métodos
const loadProfile = () => {
  loading.value = true
  if (currentUser.value) {
    formData.name = currentUser.value.name || ''
    formData.email = currentUser.value.email || ''
    formData.cc = currentUser.value.cc || ''
    formData.phone = currentUser.value.phone || ''
  }
  loading.value = false
}

const saveProfile = async () => {
  try {
    savingProfile.value = true
    
    await usersService.updateUser(currentUser.value.id, {
      name: formData.name,
      email: formData.email,
      cc: formData.cc,
      phone: formData.phone,
      role_id: currentUser.value.role_id,
      active: currentUser.value.active
    })
    
    await auth.updateUser()
    alert('✅ Perfil actualizado correctamente')
  } catch (error) {
    console.error('Error guardando perfil:', error)
    alert('❌ Error al guardar el perfil')
  } finally {
    savingProfile.value = false
  }
}

const changePassword = async () => {
  if (!canChangePassword.value) return
  
  try {
    savingPassword.value = true
    
    await usersService.changePassword(currentUser.value.id, {
      current_password: passwordData.current_password,
      password: passwordData.new_password,
      password_confirmation: passwordData.confirm_password
    })
    
    passwordData.current_password = ''
    passwordData.new_password = ''
    passwordData.confirm_password = ''
    
    alert('✅ Contraseña actualizada correctamente')
  } catch (error) {
    console.error('Error cambiando contraseña:', error)
    if (error.response?.status === 401) {
      alert('❌ La contraseña actual es incorrecta')
    } else {
      alert('❌ Error al cambiar la contraseña')
    }
  } finally {
    savingPassword.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadProfile()
})
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fade-in 0.6s ease-out;
}
</style>
