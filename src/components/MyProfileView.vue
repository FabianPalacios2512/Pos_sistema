<template>
  <!-- 🎨 Mi Perfil - Diseño Panel de Configuración SaaS -->
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header -->
      <div class="flex items-center justify-between pb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mi Perfil</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Administra tu información personal y seguridad</p>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <div v-else class="space-y-6">
        
        <!-- Layout Grid: 2 columnas en desktop -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Columna Izquierda: Info del Usuario (1 col) -->
          <div class="lg:col-span-1 space-y-6">
            
            <!-- Tarjeta de Perfil -->
            <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
              <!-- Avatar y nombre -->
              <div class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 dark:from-slate-700 dark:via-slate-600 dark:to-slate-700 px-6 py-6 text-center">
                <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-xl shadow-blue-500/30">
                  <span class="text-white font-bold text-2xl">{{ userInitials }}</span>
                </div>
                <h2 class="text-lg font-bold text-white mt-3">{{ currentUser?.name || 'Usuario' }}</h2>
                <p class="text-slate-300 text-sm">{{ currentUser?.email || '' }}</p>
              </div>
              
              <!-- Info rápida -->
              <div class="p-4 space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-zinc-800">
                  <span class="text-sm text-gray-500 dark:text-zinc-400">Rol</span>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">{{ currentUser?.role?.name || 'Usuario' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-zinc-800">
                  <span class="text-sm text-gray-500 dark:text-zinc-400">Plan</span>
                  <span class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ planDisplayName }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-zinc-800">
                  <span class="text-sm text-gray-500 dark:text-zinc-400">ID</span>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">#{{ currentUser?.id || '-' }}</span>
                </div>
                <div class="flex items-center justify-between py-2">
                  <span class="text-sm text-gray-500 dark:text-zinc-400">Estado</span>
                  <span 
                    :class="currentUser?.active 
                      ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' 
                      : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800'"
                    class="px-2.5 py-1 rounded-full text-xs font-bold border"
                  >
                    {{ currentUser?.active ? 'Activo' : 'Inactivo' }}
                  </span>
                </div>
              </div>
            </div>
            
          </div>
          
          <!-- Columna Derecha: Formularios (2 cols) -->
          <div class="lg:col-span-2 space-y-6">
            
            <!-- Información Personal -->
            <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 p-6">
              <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">Información Personal</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">Actualiza tus datos de contacto</p>
                </div>
              </div>
              
              <form @submit.prevent="saveProfile" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Nombre -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Nombre Completo</label>
                    <input
                      v-model="formData.name"
                      type="text"
                      required
                      class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                      placeholder="Tu nombre"
                    />
                  </div>
                  
                  <!-- Email (solo lectura) -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Correo Electrónico</label>
                    <input
                      v-model="formData.email"
                      type="email"
                      disabled
                      class="w-full px-3 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800/50 text-gray-500 dark:text-zinc-400 rounded-lg cursor-not-allowed"
                    />
                  </div>
                  
                  <!-- Cédula -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Cédula / Documento</label>
                    <input
                      v-model="formData.cc"
                      type="text"
                      class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                      placeholder="Número de documento"
                    />
                  </div>
                  
                  <!-- Teléfono -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Teléfono</label>
                    <input
                      v-model="formData.phone"
                      type="tel"
                      class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                      placeholder="+57 300 123 4567"
                    />
                  </div>
                </div>
                
                <!-- Botón Guardar -->
                <div class="flex justify-end pt-2">
                  <button
                    type="submit"
                    :disabled="savingProfile"
                    class="px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:opacity-50 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center gap-2"
                  >
                    <svg v-if="savingProfile" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ savingProfile ? 'Guardando...' : 'Guardar Cambios' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Seguridad - Cambio de Contraseña -->
            <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 p-6">
              <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">Seguridad</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">Cambia tu contraseña de acceso</p>
                </div>
              </div>
              
              <form @submit.prevent="changePassword" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <!-- Contraseña Actual -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Contraseña Actual</label>
                    <div class="relative">
                      <input
                        v-model="passwordData.current_password"
                        :type="showCurrentPassword ? 'text' : 'password'"
                        required
                        class="w-full px-3 py-2.5 pr-10 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        placeholder="••••••••"
                      />
                      <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg v-if="showCurrentPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Nueva Contraseña -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Nueva Contraseña</label>
                    <div class="relative">
                      <input
                        v-model="passwordData.new_password"
                        :type="showNewPassword ? 'text' : 'password'"
                        required
                        minlength="8"
                        class="w-full px-3 py-2.5 pr-10 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        placeholder="Mínimo 8 caracteres"
                      />
                      <button type="button" @click="showNewPassword = !showNewPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg v-if="showNewPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Confirmar Contraseña -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Confirmar Nueva</label>
                    <div class="relative">
                      <input
                        v-model="passwordData.confirm_password"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        required
                        minlength="8"
                        class="w-full px-3 py-2.5 pr-10 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        placeholder="Repite la contraseña"
                      />
                      <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg v-if="showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                      </button>
                    </div>
                  </div>
                </div>
                
                <!-- Indicador de fortaleza -->
                <div v-if="passwordData.new_password" class="flex items-center gap-3">
                  <div class="flex-1 h-1.5 bg-gray-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                    <div 
                      class="h-full transition-all duration-300 rounded-full"
                      :class="passwordStrengthClass"
                      :style="{ width: passwordStrength + '%' }"
                    ></div>
                  </div>
                  <span class="text-xs font-medium min-w-[50px]" :class="passwordStrengthTextClass">{{ passwordStrengthText }}</span>
                </div>
                
                <!-- Error de confirmación -->
                <p v-if="passwordData.confirm_password && passwordData.new_password !== passwordData.confirm_password" class="text-xs text-rose-600 dark:text-rose-400">
                  Las contraseñas no coinciden
                </p>
                
                <!-- Botón Cambiar -->
                <div class="flex justify-end pt-2">
                  <button
                    type="submit"
                    :disabled="savingPassword || !canChangePassword"
                    class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg shadow-amber-400/40 transition-all duration-300 flex items-center gap-2"
                  >
                    <svg v-if="savingPassword" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    {{ savingPassword ? 'Cambiando...' : 'Cambiar Contraseña' }}
                  </button>
                </div>
              </form>
            </div>
            
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
