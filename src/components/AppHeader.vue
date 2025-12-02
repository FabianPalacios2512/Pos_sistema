<template>
  <!-- Header Empresarial Profesional - Diseño Limpio y Minimalista -->
  <header class="sticky top-0 z-50 bg-white dark:bg-[#18181b] border-b border-gray-200 dark:border-white/10 transition-colors duration-300">
    <div class="h-16 px-4 lg:px-6">
      <div class="flex items-center justify-between h-full">
        
        <!-- Sección Izquierda: Marca Minimalista -->
        <div class="flex items-center space-x-4">
          <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white transition-colors duration-300">105 POS Pro</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium transition-colors duration-300">Sistema de Gestión Empresarial</p>
          </div>
        </div>
        
        <!-- Sección Derecha: Controles y Usuario -->
        <div class="flex items-center space-x-2">
          
          <!-- Notificaciones Compactas -->
          <div class="relative">
            <button
              @click="toggleNotifications"
              class="relative p-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
              title="Notificaciones"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
              </svg>
              <!-- Badge compacto -->
              <span v-if="notificationCount > 0" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
            </button>
            
            <!-- Dropdown Notificaciones -->
            <Transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 scale-95"
              enter-to-class="opacity-100 scale-100"
              leave-active-class="transition ease-in duration-150"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-95"
            >
              <div 
                v-if="notificationsOpen"
                class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 py-2 z-50 transition-colors duration-300"
                @click.stop
              >
                <div class="px-4 py-3 border-b border-gray-50">
                  <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Notificaciones</h3>
                    <!-- Toggle Silenciar -->
                    <button
                      @click="toggleNotificationsSilent"
                      class="flex items-center space-x-1 px-2 py-1 text-xs rounded-md transition-all duration-200"
                      :class="notificationsSilent 
                        ? 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100' 
                        : 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100'"
                      :title="notificationsSilent ? 'Reactivar notificaciones' : 'Silenciar notificaciones'"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="notificationsSilent" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 5.586a2 2 0 002.828 0L12 2l3.586 3.586a2 2 0 002.828 0l1.414 1.414a2 2 0 000 2.828L16.242 12l3.586 3.586a2 2 0 000 2.828L18.414 20l-3.586-3.586a2 2 0 00-2.828 0L12 20l-3.586-3.586a2 2 0 00-2.828 0L4.172 15l3.586-3.586a2 2 0 000-2.828L4.172 9l1.414-1.414z" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                      </svg>
                      <span>{{ notificationsSilent ? 'Silenciadas' : 'Silenciar' }}</span>
                    </button>
                  </div>
                </div>
                <div class="max-h-64 overflow-y-auto">
                  <div v-if="notificationCount === 0" class="px-4 py-6 text-center text-gray-500 text-sm">
                    No hay notificaciones nuevas
                  </div>
                  <div v-else-if="notificationsSilent" class="px-4 py-6 text-center text-gray-500 text-sm">
                    <div class="flex flex-col items-center space-y-2">
                      <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 5.586a2 2 0 002.828 0L12 2l3.586 3.586a2 2 0 002.828 0l1.414 1.414a2 2 0 000 2.828L16.242 12l3.586 3.586a2 2 0 000 2.828L18.414 20l-3.586-3.586a2 2 0 00-2.828 0L12 20l-3.586-3.586a2 2 0 00-2.828 0L4.172 15l3.586-3.586a2 2 0 000-2.828L4.172 9l1.414-1.414z" />
                      </svg>
                      <p class="text-sm">Notificaciones silenciadas</p>
                      <p class="text-xs">Las alertas del dashboard están desactivadas</p>
                    </div>
                  </div>
                  <div v-else class="py-2">
                    <!-- Ejemplo de notificaciones -->
                    <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer">
                      <p class="text-sm text-gray-900">Stock bajo en productos</p>
                      <p class="text-xs text-gray-500 mt-1">5 productos requieren reabastecimiento</p>
                    </div>
                    <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer">
                      <p class="text-sm text-gray-900">Venta completada</p>
                      <p class="text-xs text-gray-500 mt-1">Nueva venta por $150,000</p>
                    </div>
                  </div>
                </div>
              </div>
            </Transition>
          </div>
          
          <!-- Video Tutorial - GHOST BUTTON -->
          <button
            id="tour-video-button"
            @click="showVideoTutorial"
            class="hidden md:flex p-2 text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-lg transition-colors duration-200"
            title="Video tutorial"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </button>
          
          <!-- Ayuda -->
          <button
            id="tour-help-button"
            @click="showHelp"
            class="hidden md:flex p-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
            title="Ayuda"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </button>
          
          <!-- Botón Radio - 100% Ghost -->
          <button
            id="tour-voice-button"
            @click="$emit('toggle-radio')"
            class="hidden md:flex items-center space-x-1.5 px-3 py-2 rounded-lg transition-all duration-200 relative bg-transparent hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-600 dark:text-zinc-400"
            :title="isRadioActive ? `Reproduciendo: ${currentRadioName}` : 'Radio'"
          >
            <!-- Punto indicador (SOLO cuando está reproduciendo) -->
            <span 
              v-if="isRadioActive"
              class="absolute -top-1 -right-1 flex h-2.5 w-2.5"
            >
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>

            <!-- Icono de Radio -->
            <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.651a3.75 3.75 0 010-5.303m5.304 0a3.75 3.75 0 010 5.303m-7.425 2.122a6.75 6.75 0 010-9.546m9.546 0a6.75 6.75 0 010 9.546M5.106 18.894c-3.808-3.808-3.808-9.98 0-13.789m13.788 0c3.808 3.808 3.808 9.981 0 13.79M12 12h.008v.007H12V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            
            <!-- Texto -->
            <span class="text-sm font-medium">Radio</span>
          </button>

          <!-- Botón 105 IA - 100% Ghost (ya corregido previamente) -->
          <button
            id="tour-ia-button"
            @click="toggleAIChat"
            class="hidden md:flex items-center space-x-1.5 px-3 py-2 bg-transparent hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-600 dark:text-zinc-400 rounded-lg transition-all duration-200"
            title="Asistente IA 105"
          >
            <svg class="w-4 h-4 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <span class="text-sm font-medium">105 IA</span>
          </button>
          
          <!-- Separador -->
          <div class="hidden md:block h-5 w-px bg-gray-200 dark:bg-gray-600"></div>
          
          <!-- Contexto de Sede - TEXTO PLANO (no botón) -->
          <div v-if="currentModule === 'pos' && currentWarehouse" 
               class="hidden md:flex items-center gap-1.5 px-2 py-1"
               title="Tienda actual">
            <svg class="w-3.5 h-3.5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="text-sm font-medium text-gray-600 dark:text-zinc-400">{{ currentWarehouse.name }}</span>
          </div>
          
          <!-- Perfil de Usuario - Solo Avatar -->
          <div class="relative" id="user-dropdown-container">
            <button
              id="user-profile-button"
              @click="toggleUserDropdown"
              class="p-1 rounded-full hover:ring-2 hover:ring-gray-300 transition-all duration-200"
              :title="`${currentUser.name} - ${currentUser.role?.name || 'User'}`"
            >
              <!-- Avatar Circular -->
              <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full flex items-center justify-center shadow-md">
                <span class="text-white font-bold text-sm">{{ currentUser.initials }}</span>
              </div>
            </button>
            
            <!-- Dropdown Mejorado con Temas -->
            <Transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 scale-95"
              enter-to-class="opacity-100 scale-100"
              leave-active-class="transition ease-in duration-150"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-95"
            >
              <div 
                v-if="userDropdownOpen"
                id="user-dropdown-menu"
                class="absolute right-0 mt-3 w-72 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50"
                style="box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);"
                @click.stop
              >
                <!-- Header del Usuario -->
                <div class="px-4 py-4 bg-gradient-to-br from-blue-50 to-indigo-50 border-b border-gray-100">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                      <span class="text-white font-bold text-base">{{ currentUser.initials }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="font-semibold text-sm text-gray-900 truncate">{{ currentUser.name }}</div>
                      <div class="text-xs text-gray-600 font-medium">{{ currentUser.role?.name || 'User' }}</div>
                      <div class="text-xs text-gray-500 mt-0.5 truncate">{{ currentUser.email }}</div>
                    </div>
                  </div>
                </div>
                
                <!-- Opciones del Menu -->
                <div class="py-2">
                  <!-- Mi Perfil -->
                  <button
                    @click="handleProfileClick"
                    class="w-full flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 transition-colors font-medium group"
                  >
                    <div class="w-8 h-8 mr-3 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                      <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                    </div>
                    <span>Mi Perfil</span>
                  </button>
                  
                  <!-- Configuración -->
                  <button
                    v-if="shouldShowSettings"
                    @click="handleSettingsClick"
                    class="w-full flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors font-medium group"
                  >
                    <div class="w-8 h-8 mr-3 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                      <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                    </div>
                    <span>Configuración</span>
                  </button>
                  
                  <!-- Separador -->
                  <div class="border-t border-gray-100 my-2"></div>
                  
                  <!-- Selector de Tema -->
                  <div class="px-4 py-2">
                    <div class="flex items-center justify-between mb-2">
                      <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Apariencia</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                      <!-- Modo Claro -->
                      <button
                        @click="setTheme('light')"
                        :class="[
                          'flex flex-col items-center justify-center p-2 rounded-lg border-2 transition-all duration-200',
                          currentTheme === 'light' 
                            ? 'border-blue-500 bg-blue-50' 
                            : 'border-gray-200 bg-white hover:border-gray-300'
                        ]"
                      >
                        <svg class="w-5 h-5 mb-1" :class="currentTheme === 'light' ? 'text-blue-600' : 'text-gray-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="text-xs font-medium" :class="currentTheme === 'light' ? 'text-blue-600' : 'text-gray-600'">Claro</span>
                      </button>
                      
                      <!-- Modo Oscuro -->
                      <button
                        @click="setTheme('dark')"
                        :class="[
                          'flex flex-col items-center justify-center p-2 rounded-lg border-2 transition-all duration-200',
                          currentTheme === 'dark' 
                            ? 'border-blue-500 bg-blue-50' 
                            : 'border-gray-200 bg-white hover:border-gray-300'
                        ]"
                      >
                        <svg class="w-5 h-5 mb-1" :class="currentTheme === 'dark' ? 'text-blue-600' : 'text-gray-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <span class="text-xs font-medium" :class="currentTheme === 'dark' ? 'text-blue-600' : 'text-gray-600'">Oscuro</span>
                      </button>
                      
                      <!-- Modo Sistema -->
                      <button
                        @click="setTheme('system')"
                        :class="[
                          'flex flex-col items-center justify-center p-2 rounded-lg border-2 transition-all duration-200',
                          currentTheme === 'system' 
                            ? 'border-blue-500 bg-blue-50' 
                            : 'border-gray-200 bg-white hover:border-gray-300'
                        ]"
                      >
                        <svg class="w-5 h-5 mb-1" :class="currentTheme === 'system' ? 'text-blue-600' : 'text-gray-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-xs font-medium" :class="currentTheme === 'system' ? 'text-blue-600' : 'text-gray-600'">Sistema</span>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Separador -->
                  <div class="border-t border-gray-100 my-2"></div>
                  
                  <!-- Cerrar Sesión -->
                  <button
                    @click="handleLogout"
                    class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors font-semibold group"
                  >
                    <div class="w-8 h-8 mr-3 bg-red-100 rounded-lg flex items-center justify-center group-hover:bg-red-200 transition-colors">
                      <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                      </svg>
                    </div>
                    <span>Cerrar Sesión</span>
                  </button>
                </div>
              </div>
            </Transition>
          </div>
          
        </div>
      </div>
    </div>
  </header>

  <!-- Modal de Video Tutorial - Fuera del header para pantalla completa -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="videoModalOpen"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50 dark:bg-opacity-70 backdrop-blur-sm"
        @click="closeVideoModal"
      >
        <div 
          class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-6xl w-full mx-4 overflow-hidden"
          @click.stop
        >
          <!-- Header del Modal -->
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tutorial: {{ currentModuleTitle }}</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">Video guía para esta sección</p>
            </div>
            <button
              @click="closeVideoModal"
              class="p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <!-- Video Container -->
          <div class="relative pb-[56.25%] h-0"> <!-- 16:9 aspect ratio -->
            <iframe
              v-if="currentVideoUrl"
              :src="currentVideoUrl"
              class="absolute top-0 left-0 w-full h-full"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            ></iframe>
            <div v-else class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-900">
              <div class="text-center">
                <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 002 2v8a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">No hay video tutorial disponible para esta sección</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Pronto estará disponible</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Componente de Chat IA 105 -->
  <AI105Chat 
    :is-open="aiChatOpen" 
    @close="aiChatOpen = false"
    @navigate="handleAINavigation"
  />
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import AI105Chat from './AI105Chat.vue'
import { useRadioState } from '../composables/useRadioState'

const router = useRouter()

// Composable de estado de radio - Destructurado para reactividad correcta
const { isPlaying, currentRadioName, currentRadioId, isRadioActive, radioStatus } = useRadioState()

// Props
const props = defineProps({
  moduleTitle: {
    type: String,
    required: false,
    default: 'Dashboard'
  },
  moduleDescription: {
    type: String,
    required: false,
    default: 'Panel de control del sistema'
  },
  currentUser: {
    type: Object,
    required: true
  },
  currentModule: {
    type: String,
    required: false,
    default: 'dashboard'
  },
  currentWarehouse: {
    type: Object,
    required: false,
    default: null
  },
  shouldShowSettings: {
    type: Boolean,
    default: false
  },
  autoHideEnabled: {
    type: Boolean,
    default: false
  },
  sidebarCollapsed: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits([
  'navigate-to-settings',
  'logout',
  'show-help',
  'notifications-silenced',
  'show-video-tutorial',
  'toggleSidebar',
  'toggleAutoHide',
  'toggleSidebarCollapsed',
  'toggle-radio'
])

// Estados reactivos
const userDropdownOpen = ref(false)
const notificationsOpen = ref(false)
const videoModalOpen = ref(false)
const notificationCount = ref(3) // Ejemplo: 3 notificaciones
const notificationsSilent = ref(false) // Estado del silenciador
const aiChatOpen = ref(false) // Estado del chat IA
const currentTheme = ref('system') // Estado del tema actual (light, dark, system)

// Videos por módulo/sección (URLs de ejemplo - reemplazar con videos reales)
const videoUrls = {
  dashboard: 'https://www.youtube.com/embed/dQw4w9WgXcQ', // Video ejemplo Dashboard
  pos: 'https://www.youtube.com/embed/dQw4w9WgXcQ', // Video ejemplo Punto de Venta
  invoices: 'https://www.youtube.com/embed/dQw4w9WgXcQ', // Video ejemplo Facturas
  inventory: 'https://www.youtube.com/embed/dQw4w9WgXcQ', // Video ejemplo Inventario
  customers: 'https://www.youtube.com/embed/dQw4w9WgXcQ', // Video ejemplo Clientes
  reports: 'https://www.youtube.com/embed/dQw4w9WgXcQ', // Video ejemplo Reportes
  settings: 'https://www.youtube.com/embed/dQw4w9WgXcQ' // Video ejemplo Configuración
}

// Computed
const currentVideoUrl = computed(() => {
  return videoUrls[props.currentModule] || null
})

const currentModuleTitle = computed(() => {
  const titles = {
    dashboard: 'Dashboard Principal',
    pos: 'Punto de Venta',
    invoices: 'Gestión de Facturas',
    inventory: 'Control de Inventario',
    customers: 'Gestión de Clientes',
    reports: 'Reportes y Análisis',
    settings: 'Configuración del Sistema'
  }
  return titles[props.currentModule] || props.moduleTitle
})

// Manejar toggle del dropdown de usuario
const toggleUserDropdown = () => {
  userDropdownOpen.value = !userDropdownOpen.value
  // Cerrar notificaciones si están abiertas
  if (notificationsOpen.value) {
    notificationsOpen.value = false
  }
}

// Manejar toggle de notificaciones
const toggleNotifications = () => {
  notificationsOpen.value = !notificationsOpen.value
  // Cerrar dropdown de usuario si está abierto
  if (userDropdownOpen.value) {
    userDropdownOpen.value = false
  }
}

// Silenciar/reactivar notificaciones
const toggleNotificationsSilent = () => {
  notificationsSilent.value = !notificationsSilent.value
  emit('notifications-silenced', notificationsSilent.value)
  
  // Mostrar feedback visual
  if (notificationsSilent.value) {
    console.log('🔕 Notificaciones silenciadas - Las alertas del dashboard no se mostrarán')
  } else {
    console.log('🔔 Notificaciones reactivadas - Las alertas del dashboard volverán a mostrarse')
  }
}

// Toggle del chat IA
const toggleAIChat = () => {
  aiChatOpen.value = !aiChatOpen.value
  console.log('🤖 Chat IA 105:', aiChatOpen.value ? 'Abierto' : 'Cerrado')
}

// Manejar navegación desde el chat IA
const handleAINavigation = async (payload) => {
  console.log('🚀 [AppHeader] Navegación solicitada por IA:', payload)
  
  try {
    // La navegación ya se ejecutó en AI105Chat.vue
    // Solo logueamos aquí para tracking
    console.log('✅ [AppHeader] Evento de navegación recibido')
    
    // NO cerrar el chat - dejar que el usuario vea que navegó correctamente
    // El usuario puede cerrar manualmente el chat cuando quiera
  } catch (error) {
    console.error('❌ [AppHeader] Error en navegación:', error)
  }
}

// Mostrar video tutorial
const showVideoTutorial = () => {
  console.log('🎥 Abriendo video tutorial para módulo:', props.currentModule)
  console.log('📹 Título del módulo:', currentModuleTitle.value)
  console.log('🔗 URL del video:', currentVideoUrl.value)
  
  videoModalOpen.value = true
  emit('show-video-tutorial', props.currentModule)
}

// Cerrar modal de video
const closeVideoModal = () => {
  videoModalOpen.value = false
}

// 🎨 Manejo del tema
const setTheme = (theme) => {
  currentTheme.value = theme
  localStorage.setItem('theme-preference', theme)
  applyTheme(theme)
  console.log('🎨 Tema cambiado a:', theme)
}

const applyTheme = (theme) => {
  const root = document.documentElement
  
  if (theme === 'dark') {
    root.classList.add('dark')
  } else if (theme === 'light') {
    root.classList.remove('dark')
  } else if (theme === 'system') {
    // Detectar preferencia del sistema
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    if (prefersDark) {
      root.classList.add('dark')
    } else {
      root.classList.remove('dark')
    }
  }
}

const loadThemePreference = () => {
  const savedTheme = localStorage.getItem('theme-preference') || 'system'
  currentTheme.value = savedTheme
  applyTheme(savedTheme)
}

// 👤 Manejar clic en Mi Perfil
const handleProfileClick = () => {
  console.log('👤 Abrir perfil de usuario')
  userDropdownOpen.value = false
  // Aquí puedes emitir un evento o navegar a la vista de perfil
  // emit('navigate-to-profile')
  // O si tienes un módulo de perfil:
  router.push('/profile') // Descomenta cuando tengas la ruta
}

// ⚙️ Manejar clic en Configuración
const handleSettingsClick = () => {
  console.log('⚙️ Abrir configuración')
  userDropdownOpen.value = false
  emit('navigate-to-settings')
}

// 🚪 Manejar logout
const handleLogout = () => {
  console.log('🚪 Cerrando sesión')
  userDropdownOpen.value = false
  emit('logout')
}

// Mostrar ayuda
const showHelp = () => {
  emit('show-help')
}

// Cerrar dropdown al hacer clic fuera
const handleClickOutside = (event) => {
  // Verificar si el clic fue dentro del dropdown de usuario
  const userContainer = document.getElementById('user-dropdown-container')
  const isInsideUserDropdown = userContainer?.contains(event.target)
  
  // Cerrar dropdown de usuario si se hace clic fuera
  if (userDropdownOpen.value && !isInsideUserDropdown) {
    userDropdownOpen.value = false
  }
  
  // Verificar si el clic fue dentro del dropdown de notificaciones
  const notificationsButton = event.target.closest('button[title="Notificaciones"]')
  const notificationsMenu = event.target.closest('div[class*="w-80"]')
  
  // Cerrar dropdown de notificaciones si se hace clic fuera
  if (notificationsOpen.value && !notificationsButton && !notificationsMenu) {
    notificationsOpen.value = false
  }
}

// Manejar tecla Escape
const handleEscape = (event) => {
  if (event.key === 'Escape') {
    userDropdownOpen.value = false
    notificationsOpen.value = false
    videoModalOpen.value = false
  }
}

// Lifecycle hooks
onMounted(() => {
  // Cargar preferencia de tema al montar
  loadThemePreference()
  
  // Escuchar cambios en el tema del sistema
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  const handleSystemThemeChange = () => {
    if (currentTheme.value === 'system') {
      applyTheme('system')
    }
  }
  mediaQuery.addEventListener('change', handleSystemThemeChange)
  
  // Event listeners
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEscape)
  
  // Cleanup al desmontar
  onUnmounted(() => {
    mediaQuery.removeEventListener('change', handleSystemThemeChange)
    document.removeEventListener('click', handleClickOutside)
    document.removeEventListener('keydown', handleEscape)
  })
})
</script>

<style scoped>
/* Animación de barras de sonido para el botón de radio */
@keyframes sound-bar {
  0%, 100% {
    height: 0.5rem;
  }
  50% {
    height: 0.75rem;
  }
}

.animate-sound-bar {
  animation: sound-bar 0.6s ease-in-out infinite;
}
</style>