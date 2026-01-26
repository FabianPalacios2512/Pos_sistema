<template>
  <!-- Layout Principal: Sidebar + Contenido -->
  <div class="bg-gray-50 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] font-sans flex" style="height: 100%;">
    
    <!-- Sidebar de Navegación (Columna Izquierda) -->
    <aside class="w-64 bg-white dark:bg-zinc-900 border-r border-gray-200 dark:border-zinc-800 flex-shrink-0 overflow-y-auto" style="height: 100%; display: flex; flex-direction: column;">
      <div class="p-6 border-b border-gray-200 dark:border-zinc-800">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-slate-900 to-slate-700 dark:from-slate-700 dark:to-slate-600 rounded-lg flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <div>
            <h1 class="text-sm font-semibold text-gray-900 dark:text-white tracking-tight">Configuración</h1>
            <p class="text-xs text-gray-500 dark:text-zinc-500">Sistema</p>
          </div>
        </div>
      </div>
      
      <!-- Menú de Navegación -->
      <nav class="p-3">
        <ul class="space-y-1">
          <li>
            <button @click="activeSection = 'general'"
                    :class="[
                      'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200',
                      activeSection === 'general'
                        ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-l-4 border-emerald-600 dark:border-emerald-500'
                        : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800/50 hover:text-gray-900 dark:hover:text-zinc-200'
                    ]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
              <span>General</span>
            </button>
          </li>
          
          <li>
            <button @click="activeSection = 'invoicing'"
                    :class="[
                      'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200',
                      activeSection === 'invoicing'
                        ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-l-4 border-emerald-600 dark:border-emerald-500'
                        : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800/50 hover:text-gray-900 dark:hover:text-zinc-200'
                    ]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <span>Facturación</span>
            </button>
          </li>
          
          <li>
            <button @click="activeSection = 'taxes'"
                    :class="[
                      'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200',
                      activeSection === 'taxes'
                        ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-l-4 border-emerald-600 dark:border-emerald-500'
                        : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800/50 hover:text-gray-900 dark:hover:text-zinc-200'
                    ]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
              </svg>
              <span>Impuestos</span>
            </button>
          </li>
          
          <li>
            <button @click="activeSection = 'plan'"
                    :class="[
                      'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200',
                      activeSection === 'plan'
                        ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-l-4 border-emerald-600 dark:border-emerald-500'
                        : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800/50 hover:text-gray-900 dark:hover:text-zinc-200'
                    ]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
              </svg>
              <span>Mi Plan</span>
            </button>
          </li>
          
          <li>
            <button @click="activeSection = 'pos'"
                    :class="[
                      'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200',
                      activeSection === 'pos'
                        ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-l-4 border-emerald-600 dark:border-emerald-500'
                        : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800/50 hover:text-gray-900 dark:hover:text-zinc-200'
                    ]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              <span>POS</span>
            </button>
          </li>
          
          <li v-if="['premium', 'enterprise'].includes(appStore.tenantPlan)">
            <button @click="activeSection = 'premium'"
                    :class="[
                      'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200',
                      activeSection === 'premium'
                        ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-l-4 border-emerald-600 dark:border-emerald-500'
                        : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800/50 hover:text-gray-900 dark:hover:text-zinc-200'
                    ]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
              </svg>
              <span>Funciones Premium</span>
            </button>
          </li>
          
          <li>
            <button @click="activeSection = 'discounts'"
                    :class="[
                      'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200',
                      activeSection === 'discounts'
                        ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-l-4 border-emerald-600 dark:border-emerald-500'
                        : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800/50 hover:text-gray-900 dark:hover:text-zinc-200'
                    ]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
              </svg>
              <span>Descuentos</span>
            </button>
          </li>
          
          <li>
            <button @click="activeSection = 'expenses'"
                    :class="[
                      'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200',
                      activeSection === 'expenses'
                        ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-l-4 border-emerald-600 dark:border-emerald-500'
                        : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800/50 hover:text-gray-900 dark:hover:text-zinc-200'
                    ]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Categorías de Gastos</span>
            </button>
          </li>
        </ul>
      </nav>
    </aside>
    
    <!-- Panel de Contenido (Columna Derecha) -->
    <div class="flex-1 overflow-hidden flex flex-col" style="min-height: 0;">
      <!-- Header Sticky con Botones de Acción -->
      <div class="sticky top-0 z-10 bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between shadow-sm">
        <div>
          <h2 class="text-lg font-bold text-gray-900 dark:text-white">
            {{ getSectionTitle() }}
          </h2>
          <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
            {{ getSectionDescription() }}
          </p>
        </div>
        
        <div class="flex items-center gap-3">
          <button @click="resetToDefaults" 
                  class="px-4 py-2 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-semibold rounded-lg border border-slate-200 dark:border-zinc-700 transition-all duration-200">
            Restaurar
          </button>
          <button @click="saveAllSettings" :disabled="loading"
                  class="px-5 py-2 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-lg shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300">
            <span v-if="!loading">Guardar Todo</span>
            <span v-else class="flex items-center gap-2">
              <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              Guardando...
            </span>
          </button>
        </div>
      </div>
      
      <!-- Área de Contenido con Scroll -->
      <div class="flex-1 overflow-y-auto p-6">
      
      <!-- Sección: Mi Plan y Suscripción -->
      <div v-if="activeSection === 'plan'" class="space-y-5">
        
        <!-- Tarjeta de Plan Actual -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Plan Actual</h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">
                Vence el: <span class="font-medium text-gray-700 dark:text-zinc-300">{{ subscriptionEndDate }}</span>
              </p>
            </div>
            <div class="flex items-center gap-3">
              <span :class="[
                'px-3 py-1.5 rounded-full text-xs font-semibold',
                isTrialOrPending 
                  ? 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' 
                  : 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800'
              ]">
                {{ isTrialOrPending ? 'Prueba' : 'Activo' }}
              </span>
            </div>
          </div>

          <div class="px-6 py-6">
            <div class="mb-6">
              <p class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ planDisplayName }}</p>
              <p class="text-sm text-gray-500 dark:text-zinc-400">{{ timeRemainingFormatted }} restantes</p>
            </div>

            <div class="mb-6">
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-600 dark:text-zinc-400">Tiempo transcurrido</span>
                <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ progressPercentage }}%</span>
              </div>
              <div class="w-full bg-gray-100 dark:bg-zinc-800 rounded-full h-2 overflow-hidden">
                <div 
                  class="h-full rounded-full transition-all duration-500"
                  :class="[
                    daysRemaining > 30 ? 'bg-emerald-500' : daysRemaining > 7 ? 'bg-amber-500' : 'bg-red-500'
                  ]"
                  :style="{ width: progressPercentage + '%' }"
                ></div>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <div class="flex items-start gap-3">
                <div class="w-11 h-11 bg-gray-50 dark:bg-zinc-800 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Usuarios</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ planLimits?.users || 'Ilimitados' }}</p>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-11 h-11 bg-gray-50 dark:bg-zinc-800 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Productos</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Ilimitados</p>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-11 h-11 bg-gray-50 dark:bg-zinc-800 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Facturas</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Ilimitadas</p>
                </div>
              </div>
            </div>
          </div>

          <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-800/50 border-t border-gray-200 dark:border-zinc-800 flex items-center justify-end gap-3">
            <button 
              @click="showUpgradeModal = false" 
              class="px-4 py-2 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 rounded-lg text-sm font-medium hover:bg-white dark:hover:bg-zinc-800 transition-colors"
            >
              Renovar Ahora
            </button>
            <button 
              @click="showUpgradeModal = true" 
              class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition-colors"
            >
              Mejorar Plan
            </button>
          </div>
        </div>
        
        <!-- Historial de Pagos - Componente Dinámico -->
        <PaymentHistoryPanel />
      </div>
      
      <!-- Configuración General -->
      <div v-if="activeSection === 'general'" class="space-y-5">
        
        <!-- Información de la Empresa -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Información Empresarial</h3>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Datos corporativos y contacto</p>
          </div>
          <div class="p-5">
            <!-- Logo de la Empresa - Compacto en línea -->
            <div class="flex items-center gap-4 mb-5 pb-5 border-b border-gray-100 dark:border-zinc-800">
              <!-- Preview del Logo -->
              <div class="flex-shrink-0">
                <div v-if="systemSettings.company_logo" 
                     class="w-16 h-16 rounded-xl border-2 border-gray-200 dark:border-zinc-700 overflow-hidden bg-white dark:bg-zinc-800 flex items-center justify-center shadow-sm">
                  <img :src="systemSettings.company_logo" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div v-else class="w-16 h-16 rounded-xl border-2 border-dashed border-gray-300 dark:border-zinc-700 flex items-center justify-center bg-gray-50 dark:bg-zinc-800/50">
                  <svg class="w-7 h-7 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
              </div>
              
              <!-- Botones y Texto -->
              <div class="flex-1 min-w-0">
                <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">Logo de la Empresa</label>
                <div class="flex items-center gap-2">
                  <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-xs font-semibold rounded-lg border border-gray-200 dark:border-zinc-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span>{{ systemSettings.company_logo ? 'Cambiar' : 'Subir' }}</span>
                    <input type="file" @change="handleCompanyLogoUpload" accept="image/*" class="hidden">
                  </label>
                  <button v-if="systemSettings.company_logo" 
                          @click="removeCompanyLogo"
                          class="px-3 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                    Eliminar
                  </button>
                </div>
                <p class="text-[10px] text-gray-500 dark:text-zinc-500 mt-1">PNG, JPG o SVG • Máx. 2MB • Se usa en facturas</p>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">Nombre de la Empresa</label>
                <input v-model="systemSettings.company_name" 
                       type="text" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">Tipo de Tienda</label>
                <select v-model="systemSettings.store_type" 
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                  <option value="general">General (Mini Market, Ferretería, Papelería, etc.)</option>
                  <option value="fashion">Moda (Ropa, Zapatos, Accesorios)</option>
                </select>
                <p class="text-[10px] text-gray-500 dark:text-zinc-400 mt-1">Define el diseño del POS y comportamiento predeterminado al crear productos.</p>
              </div>
              
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">NIT/Documento</label>
                <input v-model="systemSettings.company_document" 
                       type="text" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
              </div>
              
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">Email</label>
                <input v-model="systemSettings.company_email" 
                       type="email" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
              </div>
              
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">Teléfono</label>
                <input v-model="systemSettings.company_phone" 
                       type="text" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
              </div>
              
              <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">Dirección</label>
                <input v-model="systemSettings.company_address" 
                       type="text" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Configuración de Facturación -->
      <div v-if="activeSection === 'invoicing'" class="space-y-5">
        
        <!-- Selección de Plantilla de Factura con Vista Previa Integrada -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Plantilla de Factura</h3>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Selecciona el diseño y visualiza la vista previa en tiempo real</p>
          </div>
          <div class="p-5">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
              <!-- Classic Template -->
              <button @click="systemSettings.invoice_template = 'classic'"
                      class="group relative overflow-hidden rounded-lg border-2 transition-all duration-200 text-left"
                      :class="systemSettings.invoice_template === 'classic' 
                        ? 'border-gray-900 dark:border-zinc-600 shadow-md bg-gray-50 dark:bg-zinc-800' 
                        : 'border-gray-200 dark:border-zinc-700 hover:border-gray-400 dark:hover:border-zinc-600'">
                <div class="p-4">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">Clásica</h4>
                    <div v-if="systemSettings.invoice_template === 'classic'"
                         class="w-5 h-5 bg-gray-900 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                      <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                      </svg>
                    </div>
                  </div>
                  <p class="text-[11px] text-gray-600 dark:text-zinc-400">Diseño profesional con bordes suaves</p>
                </div>
              </button>

              <!-- Modern Template -->
              <button @click="systemSettings.invoice_template = 'modern'"
                      class="group relative overflow-hidden rounded-lg border-2 transition-all duration-200 text-left"
                      :class="systemSettings.invoice_template === 'modern' 
                        ? 'border-indigo-600 dark:border-indigo-500 shadow-md bg-indigo-50 dark:bg-indigo-950' 
                        : 'border-gray-200 dark:border-zinc-700 hover:border-indigo-300 dark:hover:border-indigo-800'">
                <div class="p-4">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-bold text-indigo-900 dark:text-indigo-400">Moderna</h4>
                    <div v-if="systemSettings.invoice_template === 'modern'"
                         class="w-5 h-5 bg-indigo-600 dark:bg-indigo-500 rounded-full flex items-center justify-center">
                      <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                      </svg>
                    </div>
                  </div>
                  <p class="text-[11px] text-gray-600 dark:text-zinc-400">Diseño vibrante con gradientes</p>
                </div>
              </button>

              <!-- Minimal Template -->
              <button @click="systemSettings.invoice_template = 'minimal'"
                      class="group relative overflow-hidden rounded-lg border-2 transition-all duration-200 text-left"
                      :class="systemSettings.invoice_template === 'minimal' 
                        ? 'border-black dark:border-zinc-600 shadow-md bg-gray-50 dark:bg-zinc-800' 
                        : 'border-gray-200 dark:border-zinc-700 hover:border-gray-600 dark:hover:border-zinc-600'">
                <div class="p-4">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-bold text-black dark:text-white">Minimalista</h4>
                    <div v-if="systemSettings.invoice_template === 'minimal'"
                         class="w-5 h-5 bg-black dark:bg-zinc-700 rounded-full flex items-center justify-center">
                      <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                      </svg>
                    </div>
                  </div>
                  <p class="text-[11px] text-gray-600 dark:text-zinc-400">Diseño ultra limpio blanco y negro</p>
                </div>
              </button>
            </div>
            
            <!-- Vista Previa en Tiempo Real (Integrada) -->
            <div class="mt-6 p-6 bg-gray-50 dark:bg-zinc-800/50 rounded-lg border border-gray-200 dark:border-zinc-700">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h4 class="text-sm font-bold text-gray-900 dark:text-white">Vista Previa</h4>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">Template: <span class="font-semibold capitalize">{{ systemSettings.invoice_template || 'classic' }}</span></p>
                </div>
              </div>
              
              <!-- Preview Factura -->
              <div class="max-w-md mx-auto bg-white border-2 rounded-lg p-5 shadow-lg"
                   :class="{
                     'border-gray-300': systemSettings.invoice_template === 'classic',
                     'border-indigo-400': systemSettings.invoice_template === 'modern',
                     'border-black border-4': systemSettings.invoice_template === 'minimal'
                   }">
                
                <!-- Header -->
                <div class="pb-3 mb-3"
                     :class="{
                       'border-b border-gray-300': systemSettings.invoice_template === 'classic',
                       'bg-gradient-to-r from-indigo-500 to-purple-500 text-white -m-5 p-5 mb-3 rounded-t': systemSettings.invoice_template === 'modern',
                       'border-b-4 border-black': systemSettings.invoice_template === 'minimal'
                     }">
                  <h3 class="text-base font-bold text-center" 
                      :class="{
                        'text-gray-900': systemSettings.invoice_template !== 'modern',
                        'text-white': systemSettings.invoice_template === 'modern'
                      }">
                    {{ systemSettings.company_name || 'Mi Empresa' }}
                  </h3>
                  <p class="text-xs text-center mt-1"
                     :class="{
                       'text-gray-600': systemSettings.invoice_template !== 'modern',
                       'text-white/90': systemSettings.invoice_template === 'modern'
                     }">
                    {{ systemSettings.company_address || 'Calle 123 #45-67' }}
                  </p>
                  <p class="text-xs text-center"
                     :class="{
                       'text-gray-600': systemSettings.invoice_template !== 'modern',
                       'text-white/90': systemSettings.invoice_template === 'modern'
                     }">
                    {{ systemSettings.company_phone || '3001234567' }}
                  </p>
                  <p v-if="systemSettings.company_document" class="text-xs text-center"
                     :class="{
                       'text-gray-600': systemSettings.invoice_template !== 'modern',
                       'text-white/90': systemSettings.invoice_template === 'modern'
                     }">
                    NIT: {{ systemSettings.company_document }}
                  </p>
                </div>
                
                <!-- Número -->
                <div class="text-sm mb-3 p-2 rounded"
                     :class="{
                       'bg-gray-100': systemSettings.invoice_template === 'classic',
                       'bg-indigo-100 border border-indigo-200': systemSettings.invoice_template === 'modern',
                       'border-2 border-black': systemSettings.invoice_template === 'minimal'
                     }">
                  <div class="flex justify-between">
                    <span class="font-semibold">Factura #:</span>
                    <span class="font-bold"
                          :class="{
                            'text-gray-900': systemSettings.invoice_template !== 'modern',
                            'text-indigo-700': systemSettings.invoice_template === 'modern'
                          }">
                      {{ systemSettings.invoice_prefix }}000123
                    </span>
                  </div>
                </div>
                
                <div class="text-sm mb-3 space-y-1">
                  <div class="flex justify-between">
                    <span>Fecha:</span>
                    <span>{{ new Date().toLocaleDateString('es-CO') }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Cliente:</span>
                    <span>Cliente Demo</span>
                  </div>
                </div>
                
                <!-- Productos -->
                <div class="pt-3 mb-3"
                     :class="{
                       'border-t border-gray-300': systemSettings.invoice_template === 'classic',
                       'border-t-2 border-indigo-200': systemSettings.invoice_template === 'modern',
                       'border-t-2 border-black': systemSettings.invoice_template === 'minimal'
                     }">
                  <div class="text-sm font-semibold mb-2">PRODUCTOS</div>
                  <div class="text-sm">Producto ejemplo - $10,000</div>
                </div>
                
                <!-- Total -->
                <div class="pt-3 p-2 rounded"
                     :class="{
                       'border-t border-gray-300 bg-gray-50': systemSettings.invoice_template === 'classic',
                       'border-t-2 border-indigo-200 bg-indigo-50': systemSettings.invoice_template === 'modern',
                       'border-t-4 border-black bg-black text-white': systemSettings.invoice_template === 'minimal'
                     }">
                  <div class="flex justify-between text-sm font-bold">
                    <span>TOTAL:</span>
                    <span :class="{
                      'text-gray-900': systemSettings.invoice_template !== 'minimal',
                      'text-white': systemSettings.invoice_template === 'minimal'
                    }">
                      $10,000
                    </span>
                  </div>
                </div>
                
                <!-- Footer -->
                <div class="pt-3 mt-3"
                     :class="{
                       'border-t border-dashed border-gray-300': systemSettings.invoice_template === 'classic',
                       'border-t-2 border-dashed border-indigo-300': systemSettings.invoice_template === 'modern',
                       'border-t-2 border-dashed border-black': systemSettings.invoice_template === 'minimal'
                     }">
                  <p class="text-xs font-semibold text-center">{{ systemSettings.invoice_footer_message || '¡GRACIAS POR SU COMPRA!' }}</p>
                  <p class="text-xs text-gray-500 text-center mt-2 pt-2 border-t border-gray-200">Powered by 105 POS</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Numeración y formato removido según solicitud del usuario -->
      </div>
      
      <!-- Configuración de Impuestos -->
      <div v-if="activeSection === 'taxes'" class="space-y-5">
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Configuración de IVA</h3>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Gestión de impuestos aplicables</p>
          </div>
          <div class="p-5">
            <div class="space-y-4">
              <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-950 rounded-lg border border-blue-200 dark:border-blue-900">
                <div>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">Aplicar IVA en ventas</p>
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Incluir IVA automáticamente</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.iva_enabled" type="checkbox" class="sr-only peer">
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
              </div>
              
              <div v-if="systemSettings.iva_enabled" class="grid grid-cols-1 md:grid-cols-2 gap-4 animate-fade-in">
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">Porcentaje de IVA (%)</label>
                  <input v-model.number="systemSettings.iva_percentage" 
                         type="number" 
                         min="0" 
                         max="100" 
                         step="0.1"
                         class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">Nombre para mostrar</label>
                  <input v-model="systemSettings.iva_display_name" 
                         type="text" 
                         placeholder="IVA, IGV, Tax..."
                         class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Funciones Premium (Solo Premium y Enterprise) -->
      <div v-if="activeSection === 'premium'" class="space-y-4">
        
        <!-- Header de sección -->
        <div class="flex items-center justify-between mb-2">
          <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Funciones Premium</h2>
            <p class="text-xs text-gray-500 dark:text-zinc-400">Herramientas avanzadas para potenciar tu negocio</p>
          </div>
        </div>
        
        <!-- Grid de funciones premium -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
          
          <!-- Creditienda -->
          <div class="border-b border-gray-100 dark:border-zinc-800">
            <div class="px-5 py-4 flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Sistema de Créditos</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">Ventas a crédito con recargos configurables</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <span :class="systemSettings.creditienda_enabled ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400' : 'bg-gray-100 dark:bg-zinc-800 text-gray-500 dark:text-zinc-500'" 
                      class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">
                  {{ systemSettings.creditienda_enabled ? 'Activo' : 'Inactivo' }}
                </span>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.creditienda_enabled" type="checkbox" class="sr-only peer">
                  <div class="w-9 h-5 bg-gray-200 dark:bg-zinc-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
              </div>
            </div>
            
            <!-- Configuración expandible -->
            <div v-if="systemSettings.creditienda_enabled" class="px-5 pb-4 pt-0 animate-fade-in">
              <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-4 border border-gray-100 dark:border-zinc-700/50">
                <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1.5">Porcentaje de recargo</label>
                <div class="flex items-center gap-2">
                  <input v-model.number="systemSettings.credit_surcharge_percentage" 
                         type="number" min="0" max="100" step="0.1"
                         class="w-24 px-3 py-2 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                  <span class="text-sm text-gray-500 dark:text-zinc-400">%</span>
                  <span class="text-xs text-gray-400 dark:text-zinc-500 ml-2">Se aplica automáticamente a ventas a crédito</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Fidelización -->
          <div>
            <div class="px-5 py-4 flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950 flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Programa de Fidelización</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">Acumulación y canje de puntos por compras</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <span :class="systemSettings.enable_loyalty_system ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400' : 'bg-gray-100 dark:bg-zinc-800 text-gray-500 dark:text-zinc-500'" 
                      class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">
                  {{ systemSettings.enable_loyalty_system ? 'Activo' : 'Inactivo' }}
                </span>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.enable_loyalty_system" type="checkbox" class="sr-only peer">
                  <div class="w-9 h-5 bg-gray-200 dark:bg-zinc-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-600"></div>
                </label>
              </div>
            </div>
            
            <!-- Configuración expandible -->
            <div v-if="systemSettings.enable_loyalty_system" class="px-5 pb-4 pt-0 animate-fade-in">
              <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-4 border border-gray-100 dark:border-zinc-700/50 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1.5">Puntos por peso</label>
                    <input v-model.number="systemSettings.loyalty_points_per_currency" 
                           type="number" min="0" step="0.0001"
                           class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-1">Ej: 0.001 = 1 pto/$1,000</p>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1.5">Valor del punto</label>
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 text-sm">$</span>
                      <input v-model.number="systemSettings.loyalty_point_value" 
                             type="number" min="0" step="1"
                             class="w-full pl-7 pr-3 py-2 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-1">Valor al redimir</p>
                  </div>
                </div>
                
                <!-- Ejemplo calculado -->
                <div class="flex items-center justify-between py-2 px-3 bg-amber-50 dark:bg-amber-950/30 rounded-lg border border-amber-100 dark:border-amber-900/50">
                  <span class="text-xs text-amber-700 dark:text-amber-400">Compra de $100,000</span>
                  <div class="flex items-center gap-3 text-xs">
                    <span class="text-gray-600 dark:text-zinc-400">→ <strong class="text-amber-700 dark:text-amber-400">{{ Math.floor(100000 * (systemSettings.loyalty_points_per_currency || 0.001)) }} pts</strong></span>
                    <span class="text-gray-600 dark:text-zinc-400">= <strong class="text-emerald-600 dark:text-emerald-400">${{ (Math.floor(100000 * (systemSettings.loyalty_points_per_currency || 0.001)) * (systemSettings.loyalty_point_value || 10)).toLocaleString() }}</strong></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
        </div>
      </div>
      
      <!-- Configuración POS -->
      <div v-if="activeSection === 'pos'" class="space-y-5">
        
        <!-- Configuración de Ventas -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 overflow-hidden">
          <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900 dark:text-white">Configuración de Ventas POS</h2>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Comportamiento y configuraciones del punto de venta</p>
            </div>
          </div>
          <div class="p-5">
            <div class="space-y-3">
              <div class="flex items-center justify-between p-4 bg-white dark:bg-zinc-800/50 rounded-xl border border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 transition-all">
                <div>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">Requerir cliente en ventas</p>
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Obligar a seleccionar un cliente específico (no "Cliente Final") para procesar ventas</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.require_customer" type="checkbox" class="sr-only peer">
                  <div class="w-11 h-6 bg-gray-200 dark:bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
              </div>

              <div class="flex items-center justify-between p-4 bg-white dark:bg-zinc-800/50 rounded-xl border border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 transition-all">
                <div>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">Requerir cliente en cotizaciones</p>
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Obligar a seleccionar cliente específico (no "Cliente Final") para crear cotizaciones</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.require_customer_quotations" type="checkbox" class="sr-only peer">
                  <div class="w-11 h-6 bg-gray-200 dark:bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
              </div>

              <div class="flex items-center justify-between p-4 bg-white dark:bg-zinc-800/50 rounded-xl border border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 transition-all">
                <div>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">Alertas de stock bajo</p>
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Notificar cuando productos tengan poco inventario</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.low_stock_alerts" type="checkbox" class="sr-only peer">
                  <div class="w-11 h-6 bg-gray-200 dark:bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Nueva Sección: Descuentos y Promociones -->
      <div v-if="activeSection === 'discounts'" class="space-y-6">
        
        <!-- Header con botón de agregar -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 overflow-hidden">
          <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900 dark:text-white">Descuentos y Promociones</h2>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Gestión de códigos promocionales y descuentos automáticos</p>
            </div>
            <button @click="showAddDiscountModal = true" 
                    class="px-4 py-2 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white rounded-xl text-sm font-semibold shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center space-x-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <span>Nuevo Descuento</span>
            </button>
          </div>
          <div class="p-5">
            <!-- Configuración global de descuentos -->
            <div class="space-y-4 mb-6">
              <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-950 rounded-lg border border-green-200 dark:border-green-900">
                <div>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">Habilitar descuentos</p>
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Permitir aplicar descuentos en el POS</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.discounts_enabled" type="checkbox" class="sr-only peer">
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                </label>
              </div>
            </div>

            <!-- Lista de descuentos existentes -->
            <div v-if="discounts.length > 0" class="space-y-3">
              <h4 class="text-sm font-bold text-gray-700 dark:text-zinc-300 mb-3">Descuentos Activos</h4>
              <div v-for="discount in discounts" :key="discount.id" 
                   class="flex items-center justify-between p-4 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 rounded-xl hover:border-gray-300 dark:hover:border-zinc-600 transition-all">
                <div class="flex-1">
                  <div class="flex items-center space-x-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full"
                          :class="discount.active ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400' : 'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-400'">
                      {{ discount.active ? 'Activo' : 'Inactivo' }}
                    </span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ discount.name }}</span>
                    <span class="px-2 py-1 text-xs bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-full font-mono border border-slate-200 dark:border-slate-700">{{ discount.code }}</span>
                  </div>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">{{ discount.description }}</p>
                  <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500 dark:text-zinc-500">
                    <span>{{ discount.type === 'percentage' ? discount.value + '%' : '$' + Number(discount.value).toLocaleString() }}</span>
                    <span v-if="discount.expires_at">Exp: {{ formatDate(discount.expires_at) }}</span>
                    <span v-if="discount.usage_limit">{{ discount.used_count }}/{{ discount.usage_limit }} usos</span>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <button @click="editDiscount(discount)" 
                          class="p-2 text-slate-400 dark:text-zinc-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <button @click="deleteDiscount(discount.id)" 
                          class="p-2 text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-8 text-gray-500 dark:text-zinc-500">
              <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/>
              </svg>
              <p class="text-lg font-medium dark:text-zinc-400">No hay descuentos configurados</p>
              <p class="text-sm">Crea el primer descuento haciendo clic en "Nuevo Descuento"</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Sección de Gastos -->
      <div v-if="activeSection === 'expenses'" class="space-y-6">
        
        <!-- Categorías de Gastos -->
        <div class="bg-white rounded-2xl border border-gray-300 overflow-hidden">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Categorías de Gastos</h2>
              <p class="text-xs text-gray-500 mt-0.5">{{ expenseCategories.total || 0 }} categorías registradas</p>
            </div>
            <button @click="openCreateCategoryModal" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium flex items-center space-x-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              <span>Nueva Categoría</span>
            </button>
          </div>

          <!-- Métricas -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-5 bg-gray-50 border-b border-gray-200">
            <div class="bg-white rounded-lg p-3 border border-gray-200">
              <p class="text-xs font-medium text-gray-500">Total</p>
              <p class="text-xl font-bold text-purple-600 mt-1">{{ expenseCategories.total || 0 }}</p>
            </div>
            <div class="bg-white rounded-lg p-3 border border-gray-200">
              <p class="text-xs font-medium text-gray-500">Activas</p>
              <p class="text-xl font-bold text-green-600 mt-1">{{ expenseCategoriesStats.active_count || 0 }}</p>
            </div>
            <div class="bg-white rounded-lg p-3 border border-gray-200">
              <p class="text-xs font-medium text-gray-500">Con Gastos</p>
              <p class="text-xl font-bold text-blue-600 mt-1">{{ expenseCategoriesStats.with_expenses_count || 0 }}</p>
            </div>
            <div class="bg-white rounded-lg p-3 border border-gray-200">
              <p class="text-xs font-medium text-gray-500">Inactivas</p>
              <p class="text-xl font-bold text-gray-600 mt-1">{{ (expenseCategories.total || 0) - (expenseCategoriesStats.active_count || 0) }}</p>
            </div>
          </div>

          <!-- Filtros -->
          <div class="p-5 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <input v-model="expenseCategoryFilters.search" @input="loadExpenseCategories" type="text" placeholder="Buscar categoría..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
              <select v-model="expenseCategoryFilters.is_active" @change="loadExpenseCategories" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Todos los estados</option>
                <option value="1">Activas</option>
                <option value="0">Inactivas</option>
              </select>
              <button @click="loadExpenseCategories" :disabled="loadingCategories" class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-lg text-sm font-medium">
                <span v-if="!loadingCategories">Actualizar</span>
                <span v-else>Cargando...</span>
              </button>
            </div>
          </div>

          <!-- Tabla -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Color</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Nombre</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Descripción</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Gastos</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Total</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Estado</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="loadingCategories">
                  <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                    <div class="flex items-center justify-center space-x-2">
                      <div class="w-5 h-5 border-2 border-purple-600 border-t-transparent rounded-full animate-spin"></div>
                      <span>Cargando categorías...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="!expenseCategories.data || expenseCategories.data.length === 0">
                  <td colspan="7" class="px-4 py-8 text-center text-gray-500">No se encontraron categorías</td>
                </tr>
                <tr v-else v-for="category in (Array.isArray(expenseCategories.data) ? expenseCategories.data : []).filter(c => c !== null && c !== undefined)" :key="category.id" class="hover:bg-gray-50">
                  <td class="px-4 py-3">
                    <div class="w-6 h-6 rounded-full border-2 border-gray-300" :style="{ backgroundColor: category.color }"></div>
                  </td>
                  <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <div class="text-sm text-gray-500 max-w-xs truncate">{{ category.description || '-' }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <div class="text-sm text-gray-900">{{ category.expenses_count || 0 }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900">${{ Number(category.total_amount || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 }) }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <span :class="category.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 text-xs font-semibold rounded-full">
                      {{ category.is_active ? 'Activa' : 'Inactiva' }}
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex items-center space-x-2">
                      <button @click="editExpenseCategory(category)" class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </button>
                      <button @click="deleteExpenseCategory(category.id)" class="p-1 text-red-600 hover:bg-red-50 rounded">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
      
      </div>
      <!-- Fin Área de Contenido con Scroll -->
    </div>
    <!-- Fin Panel de Contenido -->
  </div>
  <!-- Fin Layout Principal -->

  <!-- Modal: Agregar/Editar Descuento -->
  <div v-if="showAddDiscountModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[85vh] overflow-hidden">
      <!-- Header Profesional Simple -->
      <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900">
              {{ editingDiscount ? 'Editar Descuento' : 'Nuevo Descuento' }}
            </h3>
            <p class="text-sm text-gray-500">Configuración de promociones y descuentos</p>
          </div>
        </div>
        <button @click="closeDiscountModal" class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Contenido en Grid Horizontal -->
      <div class="p-6">
        <form @submit.prevent="saveDiscount" class="space-y-6">
          
          <!-- Fila 1: Información Básica -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del descuento *</label>
              <input v-model="discountForm.name" type="text" required
                     placeholder="Descuento de Bienvenida"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Código promocional</label>
              <div class="relative">
                <input v-model="discountForm.code" type="text"
                       placeholder="AUTO (se genera automático)"
                       class="w-full pr-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="button" @click="generateCode"
                        class="absolute right-1 top-1/2 -translate-y-1/2 px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-md transition-colors"
                        title="Generar código">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                </button>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
              <input v-model="discountForm.description" type="text"
                     placeholder="Descripción opcional..."
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>

          <!-- Fila 2: Configuración del Descuento -->
          <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
              <select v-model="discountForm.type" required
                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                <option value="percentage">Porcentaje (%)</option>
                <option value="fixed_amount">Monto Fijo ($)</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Valor {{ discountForm.type === 'percentage' ? '(%)' : '($)' }} *
              </label>
              <input v-model.number="discountForm.value" type="number" required min="0" step="0.01"
                     :max="discountForm.type === 'percentage' ? 100 : undefined"
                     :placeholder="discountForm.type === 'percentage' ? '10' : '5000'"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Monto mínimo ($)</label>
              <input v-model.number="discountForm.minimum_amount" type="number" min="0" step="0.01"
                     placeholder="0"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Descuento máximo ($)</label>
              <input v-model.number="discountForm.maximum_discount" type="number" min="0" step="0.01"
                     placeholder="Ilimitado"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Límite de usos</label>
              <input v-model.number="discountForm.usage_limit" type="number" min="1"
                     placeholder="Ilimitado"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>

          <!-- Fila 3: Vigencia -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de inicio</label>
              <input v-model="discountForm.starts_at" type="date"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <p class="text-xs text-gray-500 mt-1">La promoción inicia a las 00:00 de este día</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de expiración</label>
              <input v-model="discountForm.expires_at" type="date"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <p class="text-xs text-gray-500 mt-1">La promoción termina a las 23:59 de este día</p>
            </div>
          </div>

          <!-- Fila 4: Opciones -->
          <div class="border-t pt-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Opciones Básicas -->
              <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Configuración</h4>
                <div class="space-y-2">
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input v-model="discountForm.active" type="checkbox" 
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Descuento activo</span>
                  </label>
                  
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input v-model="discountForm.stackable" type="checkbox" 
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Acumulable con otros descuentos</span>
                  </label>
                </div>
              </div>
              
              <!-- Restricción de Usuario -->
              <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Restricciones por Cliente</h4>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                  <label class="flex items-start space-x-3 cursor-pointer">
                    <input v-model="discountForm.allow_multiple_uses_per_user" type="checkbox" 
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-0.5">
                    <div>
                      <span class="text-sm font-medium text-gray-900">Permitir múltiples usos por cliente</span>
                      <p class="text-xs text-gray-600 mt-1">
                        Si no está marcado, cada cliente solo podrá usar este descuento una vez
                      </p>
                    </div>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Footer -->
      <div class="bg-gray-50 border-t px-6 py-3 flex justify-between items-center">
        <span class="text-xs text-gray-500">* Campos obligatorios</span>
        
        <div class="flex space-x-3">
          <button type="button" @click="closeDiscountModal" 
                  class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
            Cancelar
          </button>
          <button type="submit" @click="saveDiscount" :disabled="loading"
                  class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2">
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loading ? 'Guardando...' : (editingDiscount ? 'Actualizar' : 'Crear') }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Crear/Editar Categoría de Gasto -->
  <div v-if="showCategoryModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[60] p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-900">{{ editingCategory ? 'Editar Categoría' : 'Nueva Categoría' }}</h3>
      </div>
      <form @submit.prevent="saveExpenseCategory" class="p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
            <input v-model="categoryForm.name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Color *</label>
            <input v-model="categoryForm.color" type="color" required class="w-full h-10 px-1 py-1 border border-gray-300 rounded-lg">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
          <textarea v-model="categoryForm.description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
        </div>
        <div>
          <label class="flex items-center space-x-2">
            <input v-model="categoryForm.is_active" type="checkbox" class="w-4 h-4 text-purple-600 border-gray-300 rounded">
            <span class="text-sm font-medium text-gray-700">Categoría activa</span>
          </label>
        </div>
        <div class="flex justify-end space-x-3 pt-4 border-t">
          <button type="button" @click="closeCategoryModal" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
            Cancelar
          </button>
          <button type="submit" :disabled="savingCategory" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium">
            {{ savingCategory ? 'Guardando...' : 'Guardar' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal de Éxito -->
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="showSuccessModal" class="fixed inset-0 bg-black/70 dark:bg-black/85 flex items-center justify-center z-[9999] p-4">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-sm w-full border border-gray-300 dark:border-zinc-800 transform scale-100 transition-all duration-300">
          <!-- Icono de éxito -->
          <div class="p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-50 dark:bg-emerald-950/50 mb-4 border border-emerald-100 dark:border-emerald-900/50">
              <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ successMessage }}</h3>
            <p class="text-sm text-gray-600 dark:text-zinc-400">Los cambios se han aplicado correctamente</p>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Toast de Error -->
    <Transition name="slide-down">
      <div v-if="showErrorToast" class="fixed top-4 right-4 z-[9999] max-w-md">
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-2xl border border-rose-200 dark:border-rose-900/50 overflow-hidden">
          <div class="p-4 flex items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-rose-50 dark:bg-rose-950/50 flex items-center justify-center border border-rose-100 dark:border-rose-900/50">
              <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Error</h4>
              <p class="text-sm text-gray-600 dark:text-zinc-400">{{ errorMessage }}</p>
            </div>
            <button @click="showErrorToast = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <!-- Barra de progreso -->
          <div class="h-1 bg-rose-50 dark:bg-rose-950">
            <div class="h-full bg-rose-500 dark:bg-rose-400 animate-shrink-width" style="animation-duration: 3s;"></div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Modal de Upgrade de Plan -->
  <UpgradePlanModal :isOpen="showUpgradeModal" @close="showUpgradeModal = false" @success="handleUpgradeSuccess" />
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axiosInstance from '../services/apiClient'
import { appStore } from '../store/appStore.js'
import UpgradePlanModal from './UpgradePlanModal.vue'
import PaymentHistoryPanel from './PaymentHistoryPanel.vue'

// Props para evitar warnings de Vue
const props = defineProps({
  moduleName: String,
  companyInfo: Object,
  systemSettings: Object,
  queryParams: Object
})

// Emits para eventos
const emit = defineEmits(['navigate', 'changeModule', 'change-module', 'openQuotationInPos', 'openReturnInPos', 'refresh'])

// Estado del modal de upgrade de plan
const showUpgradeModal = ref(false)

// Estados para modal de categorías de gastos
const showCategoryModal = ref(false)
const loadingCategories = ref(false)
const savingCategory = ref(false)
const editingCategory = ref(null)
const expenseCategories = ref({ data: [], total: 0 })
const expenseCategoriesStats = ref({})
const expenseCategoryFilters = ref({ search: '', is_active: '' })
const categoryForm = ref({
  name: '',
  description: '',
  color: '#3B82F6',
  is_active: true
})

// Sistema de notificaciones simple sin Vuex
const errorMessage = ref('')
const showErrorToast = ref(false)

const showNotification = (message, type = 'success') => {
  console.log(`[${type.toUpperCase()}] ${message}`)
  if (type === 'success') {
    successMessage.value = message
    showSuccessModal.value = true
    // Auto-cerrar después de 2 segundos
    setTimeout(() => {
      showSuccessModal.value = false
    }, 2000)
  } else if (type === 'error') {
    errorMessage.value = message
    showErrorToast.value = true
    // Auto-cerrar después de 3 segundos
    setTimeout(() => {
      showErrorToast.value = false
    }, 3000)
  }
}

// Estado reactivo
const activeSection = ref('general')
const loading = ref(false)

// Modales
const showAddDiscountModal = ref(false)
const showAddPaymentMethodModal = ref(false)
const showSuccessModal = ref(false)
const successMessage = ref('')

// Estados de edición
const editingDiscount = ref(null)
const editingPaymentMethod = ref(null)

// Secciones de configuración
const sections = ref([
  { id: 'plan', name: 'Mi Plan', icon: '⭐' },
  { id: 'general', name: 'General', icon: '🏢' },
  { id: 'pos', name: 'POS', icon: '🛒' },
  { id: 'discounts', name: 'Descuentos', icon: '🎯' },
  { id: 'payments', name: 'Pagos', icon: '💳' },
  { id: 'expenses', name: 'Gastos', icon: '💰' }
])

// =====================================================
// COMPUTED PROPERTIES PARA MI PLAN
// =====================================================

// Datos del tenant desde appStore
const tenantPlan = computed(() => {
  return appStore.tenant || {}
})

// Fecha de renovación/fin de suscripción
const subscriptionEndDate = computed(() => {
  const endDate = tenantPlan.value.subscription_end_date
  if (!endDate) return 'No disponible'
  return new Date(endDate).toLocaleDateString('es-ES', { 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric' 
  })
})

// ¿Es prueba o pendiente?
const isTrialOrPending = computed(() => {
  const status = tenantPlan.value.subscription_status
  return status === 'trial' || status === 'pending'
})

// Nombre del plan para mostrar
const planDisplayName = computed(() => {
  const planType = tenantPlan.value.plan_type || 'free_trial'
  const planNames = {
    free: 'Plan Gratuito',
    free_trial: 'Plan Gratuito',
    basic: 'Plan Básico',
    pro: 'Plan Profesional',
    premium: 'Plan Profesional',
    enterprise: 'Plan Empresarial',
    trial: 'Período de Prueba'
  }
  return planNames[planType] || 'Plan Desconocido'
})

// Días restantes (número)
const daysRemaining = computed(() => {
  const endDate = tenantPlan.value.subscription_end_date
  if (!endDate) return 0
  
  const end = new Date(endDate)
  const now = new Date()
  
  // Normalizar a medianoche para comparación precisa
  end.setHours(0, 0, 0, 0)
  now.setHours(0, 0, 0, 0)
  
  const diffTime = end - now
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  return Math.max(0, diffDays)
})

// Tiempo restante formateado
const timeRemainingFormatted = computed(() => {
  const days = daysRemaining.value
  
  if (days < 0) return 'Expirado'
  if (days === 0) return 'Hoy (último día)'
  if (days === 1) return '1 día'
  return `${days} días`
})

// Porcentaje de progreso del plan
const progressPercentage = computed(() => {
  const startDate = tenantPlan.value.subscription_start_date
  const endDate = tenantPlan.value.subscription_end_date
  
  if (!startDate || !endDate) return 0
  
  const start = new Date(startDate)
  const end = new Date(endDate)
  const now = new Date()
  
  // Normalizar a medianoche
  start.setHours(0, 0, 0, 0)
  end.setHours(0, 0, 0, 0)
  now.setHours(0, 0, 0, 0)
  
  const totalTime = end - start
  const elapsed = now - start
  
  if (elapsed <= 0) return 0
  if (elapsed >= totalTime) return 100
  
  return Math.floor((elapsed / totalTime) * 100)
})

// Límites del plan
const planLimits = computed(() => {
  return {
    users: tenantPlan.value.max_users || 'Ilimitados',
    products: tenantPlan.value.max_products || 'Ilimitados',
    invoices: tenantPlan.value.max_invoices || 'Ilimitadas'
  }
})

// Configuraciones del sistema desde API
const systemSettings = ref({
  company_name: 'Mi Empresa',
  store_type: 'general', // 🏪 Default
  company_document: '',
  company_phone: '',
  company_email: '',
  company_address: '',
  company_logo: null,
  iva_enabled: true,
  iva_percentage: 0,
  iva_display_name: 'IVA',
  invoice_prefix: 'FACT-',
  invoice_number_start: 1,
  invoice_current_number: 1,
  invoice_footer_message: '',
  invoice_template: 'modern',
  require_customer: false,
  require_customer_quotations: false,
  discounts_enabled: true,
  customer_discounts_enabled: true,
  promo_codes_enabled: true,
  auto_apply_discounts: true,
  show_product_images: true,
  products_per_page: 12,
  low_stock_alerts: true,
  low_stock_threshold: 5
})

// Descuentos y métodos de pago
const discounts = ref([])
const paymentMethods = ref([])

// Formularios
const discountForm = ref({
  name: '',
  description: '',
  code: '',
  type: 'percentage',
  value: 0,
  applies_to: 'all_products',
  applicable_items: null,
  minimum_amount: null,
  maximum_discount: null,
  usage_limit: null,
  starts_at: '',
  expires_at: '',
  active: true,
  stackable: false,
  allow_multiple_uses_per_user: false
})

// 🖼️ Funciones para manejar el logo de la empresa
const handleCompanyLogoUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validar tamaño máximo 2MB
    if (file.size > 2 * 1024 * 1024) {
      showNotification('La imagen es demasiado grande. Máximo 2MB.', 'error')
      return
    }
    
    // Validar tipo de archivo
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp']
    if (!validTypes.includes(file.type)) {
      showNotification('Formato no soportado. Usa PNG, JPG, GIF, SVG o WebP.', 'error')
      return
    }
    
    // Leer archivo como base64
    const reader = new FileReader()
    reader.onload = (e) => {
      systemSettings.value.company_logo = e.target.result
      showNotification('Logo cargado correctamente. Recuerda guardar los cambios.', 'success')
    }
    reader.onerror = () => {
      showNotification('Error al leer el archivo.', 'error')
    }
    reader.readAsDataURL(file)
  }
  // Limpiar input para permitir subir el mismo archivo de nuevo
  event.target.value = ''
}

const removeCompanyLogo = () => {
  systemSettings.value.company_logo = null
  showNotification('Logo eliminado. Recuerda guardar los cambios.', 'success')
}

// Métodos
const loadSystemSettings = async () => {
  try {
    const response = await axiosInstance.get('/system-settings')
    if (response.data.success) {
      const settings = response.data.data
      
      // Convertir valores de base de datos (1/0) a booleanos (true/false)
      const booleanFields = [
        'creditienda_enabled',
        'enable_loyalty_system',
        'require_customer',
        'require_customer_quotations',
        'iva_enabled',
        'show_product_images',
        'low_stock_alerts',
        'discounts_enabled',
        'customer_discounts_enabled',
        'promo_codes_enabled',
        'auto_apply_discounts',
        'enable_credit_system'
      ]
      
      booleanFields.forEach(field => {
        if (settings[field] !== undefined && settings[field] !== null) {
          // Convertir a booleano: 1/"1"/true → true, 0/"0"/false/null → false
          settings[field] = Boolean(Number(settings[field]))
        }
      })
      
      Object.assign(systemSettings.value, settings)
      
      // 🆕 Actualizar datos del tenant en el store (para refrescar info del plan)
      if (response.data.tenant) {
        appStore.tenant = response.data.tenant
      }
    }
  } catch (error) {
    console.error('Error al cargar configuración:', error)
  }
}

const loadDiscounts = async () => {
  try {
    const response = await axiosInstance.get('/discounts')
    if (response.data.success) {
      discounts.value = response.data.data.data || response.data.data
    }
  } catch (error) {
    console.error('Error al cargar descuentos:', error)
  }
}

const loadPaymentMethods = async () => {
  try {
    const response = await axiosInstance.get('/payment-methods')
    if (response.data.success) {
      paymentMethods.value = response.data.data
    }
  } catch (error) {
    console.error('Error al cargar métodos de pago:', error)
  }
}

const saveAllSettings = async () => {
  loading.value = true
  try {
    // Convertir booleanos a enteros para MySQL
    const settingsToSave = {
      ...systemSettings.value,
      creditienda_enabled: systemSettings.value.creditienda_enabled ? 1 : 0,
      enable_loyalty_system: systemSettings.value.enable_loyalty_system ? 1 : 0,
      require_customer: systemSettings.value.require_customer ? 1 : 0,
      require_customer_quotations: systemSettings.value.require_customer_quotations ? 1 : 0,
      iva_enabled: systemSettings.value.iva_enabled ? 1 : 0,
      show_product_images: systemSettings.value.show_product_images ? 1 : 0,
      low_stock_alerts: systemSettings.value.low_stock_alerts ? 1 : 0,
      discounts_enabled: systemSettings.value.discounts_enabled ? 1 : 0,
      customer_discounts_enabled: systemSettings.value.customer_discounts_enabled ? 1 : 0,
      promo_codes_enabled: systemSettings.value.promo_codes_enabled ? 1 : 0,
      auto_apply_discounts: systemSettings.value.auto_apply_discounts ? 1 : 0,
    }
    
    console.log('🏪 [Settings] Guardando configuraciones:', { store_type: settingsToSave.store_type })
    
    const response = await axiosInstance.put('/system-settings', settingsToSave)
    
    console.log('✅ [Settings] Respuesta del servidor:', response.data)
    
    if (response.data.success) {
      showNotification('Configuraciones guardadas exitosamente', 'success')
      // Recargar settings para confirmar que se guardaron
      await loadSystemSettings()
      // 🔄 IMPORTANTE: Recargar appStore para que POS vea los cambios (force=true)
      await appStore.loadSystemSettings(true)
    }
  } catch (error) {
    console.error('❌ [Settings] Error al guardar configuración:', error)
    console.error('❌ [Settings] Response:', error.response?.data)
    showNotification('Error al guardar configuración', 'error')
  } finally {
    loading.value = false
  }
}

const resetToDefaults = async () => {
  if (confirm('¿Estás seguro de restaurar las configuraciones por defecto?')) {
    try {
      const response = await axiosInstance.post('/system-settings/reset')
      if (response.data.success) {
        await loadSystemSettings()
        showNotification('🔄 Configuraciones restauradas', 'success')
      }
    } catch (error) {
      console.error('Error al restaurar configuración:', error)
    }
  }
}

// Funciones auxiliares para títulos dinámicos del header
const getSectionTitle = () => {
  const titles = {
    general: 'Información General',
    invoicing: 'Configuración de Facturación',
    taxes: 'Impuestos y Tasas',
    plan: 'Mi Plan y Suscripción',
    pos: 'Configuración del POS',
    discounts: 'Descuentos y Promociones',
    payments: 'Métodos de Pago',
    expenses: 'Categorías de Gastos'
  }
  return titles[activeSection.value] || 'Configuración'
}

const getSectionDescription = () => {
  const descriptions = {
    general: 'Datos de la empresa y configuración básica',
    invoicing: 'Plantillas, numeración y formato de facturas',
    taxes: 'Configuración de IVA e impuestos aplicables',
    plan: 'Detalles de tu suscripción y límites del plan',
    pos: 'Opciones avanzadas del punto de venta',
    discounts: 'Gestión de descuentos y códigos promocionales',
    payments: 'Configuración de formas de pago aceptadas',
    expenses: 'Categorización de gastos operativos'
  }
  return descriptions[activeSection.value] || ''
}

// Gestión de descuentos
const generateCode = async () => {
  try {
    const response = await axiosInstance.get('/discounts-generate-code')
    if (response.data.success) {
      discountForm.value.code = response.data.data.code
    }
  } catch (error) {
    console.error('Error al generar código:', error)
  }
}

const saveDiscount = async () => {
  loading.value = true
  try {
    // Preparar datos con fechas convertidas a datetime
    const dataToSend = { ...discountForm.value }
    
    // Si hay fecha de inicio, agregar hora 00:00:00
    if (dataToSend.starts_at && !dataToSend.starts_at.includes('T')) {
      dataToSend.starts_at = `${dataToSend.starts_at}T00:00:00`
    }
    
    // Si hay fecha de expiración, agregar hora 23:59:59
    if (dataToSend.expires_at && !dataToSend.expires_at.includes('T')) {
      dataToSend.expires_at = `${dataToSend.expires_at}T23:59:59`
    }
    
    let response
    if (editingDiscount.value) {
      response = await axiosInstance.put(`/discounts/${editingDiscount.value.id}`, dataToSend)
    } else {
      response = await axiosInstance.post('/discounts', dataToSend)
    }
    
    if (response.data.success) {
      await loadDiscounts()
      closeDiscountModal()
      showNotification(editingDiscount.value ? '✅ Descuento actualizado' : '✅ Descuento creado', 'success')
    }
  } catch (error) {
    console.error('Error al guardar descuento:', error)
    showNotification('❌ Error al guardar descuento', 'error')
  } finally {
    loading.value = false
  }
}

const editDiscount = (discount) => {
  editingDiscount.value = discount
  
  // Copiar datos del descuento
  const discountCopy = { ...discount }
  
  // Convertir fechas datetime a date para el input type="date"
  if (discountCopy.starts_at) {
    discountCopy.starts_at = discountCopy.starts_at.split('T')[0]
  }
  if (discountCopy.expires_at) {
    discountCopy.expires_at = discountCopy.expires_at.split('T')[0]
  }
  
  Object.assign(discountForm.value, discountCopy)
  showAddDiscountModal.value = true
}

const deleteDiscount = async (id) => {
  if (confirm('¿Estás seguro de eliminar este descuento?')) {
    try {
      const response = await axiosInstance.delete(`/discounts/${id}`)
      if (response.data.success) {
        await loadDiscounts()
        showNotification('✅ Descuento eliminado', 'success')
      }
    } catch (error) {
      console.error('Error al eliminar descuento:', error)
    }
  }
}

const closeDiscountModal = () => {
  showAddDiscountModal.value = false
  editingDiscount.value = null
  discountForm.value = {
    name: '',
    description: '',
    code: '',
    type: 'percentage',
    value: 0,
    applies_to: 'all_products',
    applicable_items: null,
    minimum_amount: null,
    maximum_discount: null,
    usage_limit: null,
    starts_at: '',
    expires_at: '',
    active: true,
    stackable: false,
    allow_multiple_uses_per_user: false
  }
}

// Gestión de métodos de pago
const togglePaymentMethodStatus = async (method) => {
  try {
    const response = await axiosInstance.patch(`/payment-methods/${method.id}/toggle-status`)
    if (response.data.success) {
      await loadPaymentMethods()
      showNotification('✅ Estado actualizado', 'success')
    }
  } catch (error) {
    console.error('Error al cambiar estado:', error)
  }
}

const editPaymentMethod = (method) => {
  editingPaymentMethod.value = method
  showAddPaymentMethodModal.value = true
}

const deletePaymentMethod = async (id) => {
  if (confirm('¿Estás seguro de eliminar este método de pago?')) {
    try {
      const response = await axiosInstance.delete(`/payment-methods/${id}`)
      if (response.data.success) {
        await loadPaymentMethods()
        showNotification('✅ Método de pago eliminado', 'success')
      }
    } catch (error) {
      console.error('Error al eliminar método de pago:', error)
    }
  }
}

// Utilidades
const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('es-ES')
}

// Funciones para categorías de gastos
const loadExpenseCategories = async () => {
  loadingCategories.value = true
  try {
    const params = {}
    if (expenseCategoryFilters.value.search) params.search = expenseCategoryFilters.value.search
    if (expenseCategoryFilters.value.is_active !== '') params.is_active = expenseCategoryFilters.value.is_active
    
    const response = await axiosInstance.get('/expense-categories', { params })
    console.log('Response de categorías:', response.data)
    
    // Asegurar estructura correcta
    if (response.data) {
      // Si la respuesta tiene success y data, es el formato del backend
      if (response.data.success && response.data.data) {
        expenseCategories.value = response.data.data
      } else {
        expenseCategories.value = response.data
      }
    } else {
      expenseCategories.value = { data: [], total: 0 }
    }
    
    console.log('expenseCategories después de asignar:', expenseCategories.value)
    
    // Cargar estadísticas
    const statsResponse = await axiosInstance.get('/expense-categories/statistics')
    console.log('Stats response:', statsResponse.data)
    if (statsResponse.data && statsResponse.data.success) {
      expenseCategoriesStats.value = statsResponse.data.data || {}
    } else {
      expenseCategoriesStats.value = statsResponse.data || {}
    }
  } catch (error) {
    console.error('Error al cargar categorías:', error)
    expenseCategories.value = { data: [], total: 0 }
    expenseCategoriesStats.value = {}
  } finally {
    loadingCategories.value = false
  }
}

const openCreateCategoryModal = () => {
  editingCategory.value = null
  categoryForm.value = { name: '', description: '', color: '#3B82F6', is_active: true }
  showCategoryModal.value = true
}

const editExpenseCategory = (category) => {
  editingCategory.value = category
  categoryForm.value = {
    name: category.name,
    description: category.description || '',
    color: category.color,
    is_active: category.is_active === 1 || category.is_active === true
  }
  showCategoryModal.value = true
}

const saveExpenseCategory = async () => {
  savingCategory.value = true
  try {
    const data = {
      ...categoryForm.value,
      is_active: categoryForm.value.is_active ? 1 : 0
    }
    
    if (editingCategory.value) {
      await axiosInstance.put(`/expense-categories/${editingCategory.value.id}`, data)
    } else {
      await axiosInstance.post('/expense-categories', data)
    }
    
    await loadExpenseCategories()
    closeCategoryModal()
    showNotification(editingCategory.value ? '✅ Categoría actualizada' : '✅ Categoría creada', 'success')
  } catch (error) {
    console.error('Error al guardar categoría:', error)
    showNotification('❌ Error al guardar categoría', 'error')
  } finally {
    savingCategory.value = false
  }
}

const deleteExpenseCategory = async (id) => {
  if (confirm('¿Estás seguro de eliminar esta categoría?')) {
    try {
      await axiosInstance.delete(`/expense-categories/${id}`)
      await loadExpenseCategories()
      showNotification('✅ Categoría eliminada', 'success')
    } catch (error) {
      console.error('Error al eliminar categoría:', error)
      showNotification('❌ Error al eliminar categoría', 'error')
    }
  }
}

const closeCategoryModal = () => {
  showCategoryModal.value = false
  editingCategory.value = null
  categoryForm.value = { name: '', description: '', color: '#3B82F6', is_active: true }
}

// Watch para cargar categorías cuando se selecciona la sección de gastos
watch(activeSection, (newVal) => {
  if (newVal === 'expenses') {
    loadExpenseCategories()
  }
})

// Watch para cambiar sección desde query params
watch(() => props.queryParams, (newParams) => {
  if (newParams && newParams.section) {
    console.log('🔄 Cambiando sección desde query params:', newParams.section)
    activeSection.value = newParams.section
  }
}, { immediate: true, deep: true })

// Manejo de upgrade exitoso
const handleUpgradeSuccess = async (upgradeData) => {
  // El plan fue actualizado exitosamente
  // Recargar configuraciones y cerrar modal
  showUpgradeModal.value = false
  
  // Recargar datos del tenant y system settings
  await loadSystemSettings()
  
  // Mostrar mensaje de éxito
  showNotification('✅ Plan actualizado exitosamente. Redirigiéndote al POS...', 'success')
  
  // Redirigir al POS después de 2 segundos
  setTimeout(() => {
    window.location.href = '/pos'
  }, 2000)
}

// Inicialización
onMounted(async () => {
  await loadSystemSettings()
  await loadDiscounts()
  await loadPaymentMethods()
  
  // 🔥 Verificar si viene un error de pago en la URL (desde PaymentSuccess)
  const urlParams = new URLSearchParams(window.location.search)
  const paymentError = urlParams.get('payment_error')
  if (paymentError) {
    // Mostrar error de pago
    activeSection.value = 'plan'
    setTimeout(() => {
      showNotification(decodeURIComponent(paymentError), 'error')
      // Limpiar el parámetro de la URL sin recargar
      window.history.replaceState({}, document.title, window.location.pathname)
    }, 500)
  }
  
  // Si viene con query param section=plans, ir directo a Mi Plan y abrir modal
  if (props.queryParams?.section === 'plans') {
    activeSection.value = 'plan'
    // Pequeño delay para que se renderice la sección primero
    setTimeout(() => {
      showUpgradeModal.value = true
    }, 300)
  }
})

// Watch para cuando cambian los queryParams (navegación desde otros módulos)
watch(() => props.queryParams, (newParams) => {
  if (newParams?.section === 'plans') {
    activeSection.value = 'plan'
    setTimeout(() => {
      showUpgradeModal.value = true
    }, 300)
  }
}, { deep: true })
</script>

<style scoped>
/* Animaciones para modales */
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
  transform: scale(0.9);
}

/* Animación para toast de error (slide down from top) */
.slide-down-enter-active {
  transition: all 0.3s ease-out;
}

.slide-down-leave-active {
  transition: all 0.2s ease-in;
}

.slide-down-enter-from {
  transform: translateY(-100%);
  opacity: 0;
}

.slide-down-leave-to {
  transform: translateY(-20px);
  opacity: 0;
}

/* Animación de barra de progreso */
@keyframes shrink-width {
  from {
    width: 100%;
  }
  to {
    width: 0%;
  }
}

.animate-shrink-width {
  animation: shrink-width linear;
}
</style>