<template>
  <div class="fixed inset-0 bg-black/50 dark:bg-black/70  flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-5xl border border-gray-200 dark:border-zinc-800 shadow-2xl dark:shadow-black/50 my-8">
      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 dark:border-zinc-800">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-zinc-700 dark:to-zinc-800 flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-zinc-700">
            <span class="text-lg font-bold text-slate-600 dark:text-zinc-300">{{ (tenant.business_name || tenant.name || 'N')[0].toUpperCase() }}</span>
          </div>
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Configuración de Tienda</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">{{ tenant.business_name || tenant.name }} · <span class="font-mono">{{ tenant.primary_domain }}</span></p>
          </div>
        </div>
        <button @click="$emit('close')" class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Tabs -->
      <div class="flex border-b border-gray-200 dark:border-zinc-800 px-6 bg-gray-50 dark:bg-zinc-900/50">
        <button @click="activeTab = 'users'" :class="activeTab === 'users' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300'" class="px-4 py-3 border-b-2 font-semibold text-sm transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
          </svg>
          Usuarios
        </button>
        <button @click="activeTab = 'products'" :class="activeTab === 'products' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300'" class="px-4 py-3 border-b-2 font-semibold text-sm transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
          </svg>
          Productos
        </button>
        <button @click="activeTab = 'subscription'" :class="activeTab === 'subscription' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300'" class="px-4 py-3 border-b-2 font-semibold text-sm transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          Suscripción
        </button>
        <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300'" class="px-4 py-3 border-b-2 font-semibold text-sm transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Información
        </button>
      </div>

      <!-- Content -->
      <div class="p-6 max-h-[500px] overflow-y-auto">
        <!-- Tab: Usuarios -->
        <div v-if="activeTab === 'users'">
          <div class="flex items-center justify-between mb-4">
            <h4 class="text-base font-bold text-gray-900 dark:text-white">Usuarios de la Tienda</h4>
            <button @click="loadUsers" class="p-2 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-600 dark:text-zinc-400 rounded-lg transition-colors">
              <svg class="w-4 h-4" :class="{'animate-spin': loadingUsers}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
            </button>
          </div>

          <div v-if="loadingUsers" class="text-center py-8 text-gray-500 dark:text-zinc-400">
            <svg class="w-8 h-8 animate-spin mx-auto mb-2 text-blue-500" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Cargando usuarios...
          </div>
          
          <div v-else-if="users.length === 0" class="text-center py-12 text-gray-500 dark:text-zinc-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            No hay usuarios registrados
          </div>

          <div v-else class="space-y-3">
            <div v-for="user in users" :key="user.id" class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 flex items-center justify-between border border-gray-200 dark:border-zinc-700/50 hover:border-gray-300 dark:hover:border-zinc-700 transition-colors">
              <div class="flex items-center gap-4">
                <div class="w-11 h-11 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/20">
                  <span class="text-white font-bold">{{ user.name.charAt(0).toUpperCase() }}</span>
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ user.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">{{ user.email }}</p>
                  <span v-if="user.role" class="inline-flex mt-1.5 px-2 py-0.5 bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 text-[10px] font-bold rounded-md border border-blue-100 dark:border-blue-900">
                    {{ user.role.name || user.role }}
                  </span>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button @click="resetPassword(user)" class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold transition-colors flex items-center gap-1.5 shadow-sm">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                  </svg>
                  Resetear Contraseña
                </button>
                <span :class="user.active ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'" class="px-2.5 py-1 text-[10px] font-bold rounded-lg border">
                  {{ user.active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab: Productos -->
        <div v-if="activeTab === 'products'">
          <div class="flex items-center justify-between mb-4">
            <h4 class="text-base font-bold text-gray-900 dark:text-white">Productos de la Tienda</h4>
            <button @click="loadProducts" class="p-2 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-600 dark:text-zinc-400 rounded-lg transition-colors">
              <svg class="w-4 h-4" :class="{'animate-spin': loadingProducts}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
            </button>
          </div>

          <div v-if="loadingProducts" class="text-center py-8 text-gray-500 dark:text-zinc-400">
            <svg class="w-8 h-8 animate-spin mx-auto mb-2 text-blue-500" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Cargando productos...
          </div>
          
          <div v-else-if="products.length === 0" class="text-center py-12 text-gray-500 dark:text-zinc-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            No hay productos registrados
          </div>

          <div v-else>
            <p class="text-sm text-gray-500 dark:text-zinc-400 mb-3">Total: <strong class="text-gray-900 dark:text-white">{{ products.length }}</strong> productos</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
              <div v-for="product in products.slice(0, 20)" :key="product.id" class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-3 border border-gray-200 dark:border-zinc-700/50">
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate mb-1">{{ product.name }}</p>
                <p class="text-base font-bold text-emerald-600 dark:text-emerald-400">${{ parseFloat(product.price).toFixed(0) }}</p>
                <p class="text-[11px] text-gray-500 dark:text-zinc-500 mt-1">Stock: {{ product.stock || 0 }}</p>
              </div>
            </div>
            <p v-if="products.length > 20" class="text-center text-gray-400 dark:text-zinc-500 text-sm mt-4 py-2 bg-gray-50 dark:bg-zinc-800/30 rounded-lg">
              ... y {{ products.length - 20 }} productos más
            </p>
          </div>
        </div>

        <!-- Tab: Suscripción (NUEVO) -->
        <div v-if="activeTab === 'subscription'">
          <div class="space-y-6">
            <!-- Estado Actual -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-200 dark:border-zinc-700/50">
              <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Estado Actual
              </h4>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Plan</p>
                  <span :class="getPlanBadge(tenant.plan)" class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold border">
                    {{ (tenant.plan || 'N/A').replace('_', ' ').toUpperCase() }}
                  </span>
                </div>
                <div>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Estado</p>
                  <div class="flex items-center gap-1.5">
                    <span 
                      class="w-2 h-2 rounded-full"
                      :class="{
                        'bg-emerald-500': tenant.status === 'active',
                        'bg-amber-500': tenant.status === 'paused',
                        'bg-rose-500': tenant.status === 'suspended'
                      }"
                    ></span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                      {{ tenant.status === 'active' ? 'Activo' : tenant.status === 'paused' ? 'Pausado' : 'Suspendido' }}
                    </span>
                  </div>
                </div>
                <div>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Inicio Suscripción</p>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ tenant.subscription_start ? new Date(tenant.subscription_start).toLocaleDateString('es-ES') : 'N/A' }}
                  </p>
                </div>
                <div>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Vencimiento</p>
                  <p class="text-sm font-semibold" :class="isExpired ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-white'">
                    {{ tenant.subscription_end ? new Date(tenant.subscription_end).toLocaleDateString('es-ES') : 'N/A' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Editar Fechas del Plan -->
            <div class="bg-blue-50 dark:bg-blue-950/30 rounded-xl p-5 border border-blue-100 dark:border-blue-900/50">
              <h4 class="text-sm font-bold text-blue-700 dark:text-blue-400 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Modificar Fechas del Plan
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                  <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Fecha de Inicio</label>
                  <input 
                    type="date" 
                    v-model="editSubscription.start"
                    class="w-full px-3 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Fecha de Vencimiento</label>
                  <input 
                    type="date" 
                    v-model="editSubscription.end"
                    class="w-full px-3 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                </div>
              </div>
              <!-- Botones rápidos -->
              <div class="flex flex-wrap gap-2 mb-4">
                <button @click="extendDays(7)" class="px-3 py-1.5 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-lg border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                  +7 días
                </button>
                <button @click="extendDays(15)" class="px-3 py-1.5 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-lg border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                  +15 días
                </button>
                <button @click="extendDays(30)" class="px-3 py-1.5 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-lg border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                  +1 mes
                </button>
                <button @click="extendDays(90)" class="px-3 py-1.5 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-lg border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                  +3 meses
                </button>
                <button @click="extendDays(365)" class="px-3 py-1.5 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-lg border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                  +1 año
                </button>
              </div>
              <button 
                @click="updateSubscriptionDates" 
                :disabled="savingDates"
                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-lg transition-colors disabled:opacity-50 flex items-center justify-center gap-2"
              >
                <svg v-if="savingDates" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ savingDates ? 'Guardando...' : 'Guardar Fechas' }}
              </button>
            </div>

            <!-- Pausar/Activar Tienda -->
            <div :class="tenant.status === 'active' ? 'bg-amber-50 dark:bg-amber-950/30 border-amber-100 dark:border-amber-900/50' : 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-100 dark:border-emerald-900/50'" class="rounded-xl p-5 border">
              <h4 :class="tenant.status === 'active' ? 'text-amber-700 dark:text-amber-400' : 'text-emerald-700 dark:text-emerald-400'" class="text-sm font-bold mb-2 flex items-center gap-2">
                <svg v-if="tenant.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ tenant.status === 'active' ? 'Pausar Tienda' : 'Activar Tienda' }}
              </h4>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mb-4">
                {{ tenant.status === 'active' 
                  ? 'Pausar la tienda impedirá que los usuarios puedan acceder temporalmente. Los datos se conservarán.' 
                  : 'Activar la tienda permitirá que los usuarios vuelvan a acceder normalmente.' 
                }}
              </p>
              <button 
                @click="toggleTenantStatus"
                :disabled="togglingStatus"
                :class="tenant.status === 'active' 
                  ? 'bg-amber-500 hover:bg-amber-600' 
                  : 'bg-emerald-500 hover:bg-emerald-600'"
                class="w-full py-2.5 text-white font-bold text-sm rounded-lg transition-colors disabled:opacity-50 flex items-center justify-center gap-2"
              >
                <svg v-if="togglingStatus" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ togglingStatus ? 'Procesando...' : (tenant.status === 'active' ? 'Pausar Tienda' : 'Activar Tienda') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Tab: Información -->
        <div v-if="activeTab === 'info'">
          <h4 class="text-base font-bold text-gray-900 dark:text-white mb-4">Información de la Tienda</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50">
              <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">ID de Tenant</p>
              <p class="text-sm font-mono text-gray-900 dark:text-white">{{ tenant.id }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50">
              <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Nombre del Negocio</p>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ tenant.business_name || 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50">
              <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Dominio Principal</p>
              <a :href="'https://' + tenant.primary_domain" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-mono">{{ tenant.primary_domain }}</a>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700/50">
              <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1">Fecha de Creación</p>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ new Date(tenant.created_at).toLocaleString('es-ES') }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900/50">
        <button @click="$emit('close')" class="px-5 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-xl font-semibold text-sm transition-colors border border-gray-200 dark:border-zinc-700">
          Cerrar
        </button>
      </div>
    </div>

    <!-- Modal: Resetear Contraseña -->
    <div v-if="showResetPasswordModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 max-w-md w-full border border-gray-200 dark:border-zinc-700 shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-amber-100 dark:bg-amber-950 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
          </div>
          <div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white">Resetear Contraseña</h4>
            <p class="text-sm text-gray-500 dark:text-zinc-400">{{ selectedUser?.name }}</p>
          </div>
        </div>
        <input 
          v-model="newPassword" 
          type="text" 
          placeholder="Nueva contraseña" 
          class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white rounded-xl border border-gray-200 dark:border-zinc-700 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 mb-4"
        >
        <div class="flex gap-3">
          <button @click="showResetPasswordModal = false; selectedUser = null; newPassword = ''" class="flex-1 px-4 py-3 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-xl font-semibold text-sm transition-colors">
            Cancelar
          </button>
          <button @click="confirmResetPassword" class="flex-1 px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold text-sm transition-colors">
            Confirmar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  tenant: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'refresh', 'toggle-status'])

const activeTab = ref('users')
const users = ref([])
const products = ref([])
const loadingUsers = ref(false)
const loadingProducts = ref(false)
const showResetPasswordModal = ref(false)
const selectedUser = ref(null)
const newPassword = ref('')
const savingDates = ref(false)
const togglingStatus = ref(false)

// Fechas de suscripción editables
const editSubscription = ref({
  start: '',
  end: ''
})

// Verificar si está expirado
const isExpired = computed(() => {
  if (!props.tenant.subscription_end) return false
  return new Date(props.tenant.subscription_end) < new Date()
})

// Inicializar fechas
const initDates = () => {
  if (props.tenant.subscription_start) {
    editSubscription.value.start = props.tenant.subscription_start.split('T')[0]
  }
  if (props.tenant.subscription_end) {
    editSubscription.value.end = props.tenant.subscription_end.split('T')[0]
  }
}

// Extender días desde fecha actual de vencimiento
const extendDays = (days) => {
  let baseDate = editSubscription.value.end 
    ? new Date(editSubscription.value.end) 
    : new Date()
  
  // Si ya expiró, partir desde hoy
  if (baseDate < new Date()) {
    baseDate = new Date()
  }
  
  baseDate.setDate(baseDate.getDate() + days)
  editSubscription.value.end = baseDate.toISOString().split('T')[0]
}

// Guardar fechas de suscripción
const updateSubscriptionDates = async () => {
  if (!editSubscription.value.start || !editSubscription.value.end) {
    alert('Por favor selecciona ambas fechas')
    return
  }
  
  savingDates.value = true
  try {
    const res = await axios.put(`/api/admin/tenants/${props.tenant.id}/subscription`, {
      subscription_start: editSubscription.value.start,
      subscription_end: editSubscription.value.end
    })
    
    if (res.data.success) {
      alert('✅ Fechas actualizadas correctamente')
      emit('refresh')
    } else {
      alert('❌ Error: ' + (res.data.message || 'No se pudo actualizar'))
    }
  } catch (error) {
    alert('❌ Error al actualizar fechas: ' + (error.response?.data?.message || error.message))
  }
  savingDates.value = false
}

// Pausar/Activar tienda
const toggleTenantStatus = async () => {
  const newStatus = props.tenant.status === 'active' ? 'paused' : 'active'
  const confirmMsg = props.tenant.status === 'active' 
    ? '¿Seguro que deseas PAUSAR esta tienda? Los usuarios no podrán acceder.'
    : '¿Seguro que deseas ACTIVAR esta tienda?'
  
  if (!confirm(confirmMsg)) return
  
  togglingStatus.value = true
  try {
    const res = await axios.put(`/api/admin/tenants/${props.tenant.id}/status`, {
      status: newStatus
    })
    
    if (res.data.success) {
      alert(`✅ Tienda ${newStatus === 'active' ? 'activada' : 'pausada'} correctamente`)
      emit('refresh')
      emit('close')
    } else {
      alert('❌ Error: ' + (res.data.message || 'No se pudo cambiar el estado'))
    }
  } catch (error) {
    alert('❌ Error: ' + (error.response?.data?.message || error.message))
  }
  togglingStatus.value = false
}

// Cargar usuarios
const loadUsers = async () => {
  loadingUsers.value = true
  try {
    const res = await axios.get(`/admin/api/tenants/${props.tenant.id}/users`)
    if (res.data.success) {
      users.value = res.data.data
    }
  } catch (error) {
    alert('Error al cargar usuarios')
  }
  loadingUsers.value = false
}

// Cargar productos
const loadProducts = async () => {
  loadingProducts.value = true
  try {
    const res = await axios.get(`/admin/api/tenants/${props.tenant.id}/products`)
    if (res.data.success) {
      products.value = res.data.data
    }
  } catch (error) {
    alert('Error al cargar productos')
  }
  loadingProducts.value = false
}

// Resetear contraseña
const resetPassword = (user) => {
  selectedUser.value = user
  newPassword.value = ''
  showResetPasswordModal.value = true
}

const confirmResetPassword = async () => {
  if (!newPassword.value) {
    alert('Por favor ingresa una contraseña')
    return
  }
  
  try {
    const res = await axios.post(`/admin/api/tenants/${props.tenant.id}/users/${selectedUser.value.id}/reset-password`, {
      password: newPassword.value
    })
    
    if (res.data.success) {
      alert(`✅ Contraseña actualizada!\n\nUsuario: ${selectedUser.value.email}\nNueva contraseña: ${newPassword.value}`)
      showResetPasswordModal.value = false
      selectedUser.value = null
      newPassword.value = ''
    }
  } catch (error) {
    alert('❌ Error al resetear contraseña: ' + (error.response?.data?.message || error.message))
  }
}

// Badge de plan
const getPlanBadge = (plan) => {
  const badges = {
    'free_trial': 'bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-700',
    'basic': 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
    'premium': 'bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-800',
    'enterprise': 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
  }
  return badges[plan] || badges.free_trial
}

// Cargar usuarios al montar
onMounted(() => {
  loadUsers()
  initDates()
})
</script>
