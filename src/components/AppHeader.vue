<template>
  <!-- Header Empresarial Profesional - Diseño Limpio y Minimalista -->
  <header class="sticky top-0 z-50 bg-white border-b border-gray-200">
    <div class="h-16 px-4 lg:px-6">
      <div class="flex items-center justify-between h-full">
        
        <!-- Sección Izquierda: Marca Minimalista -->
        <div class="flex items-center space-x-4">
          <div>
            <h1 class="text-lg font-bold text-gray-900">105 POS Pro</h1>
            <p class="text-xs text-gray-500 font-medium">Sistema de Gestión Empresarial</p>
          </div>
        </div>
        
        <!-- Sección Derecha: Controles y Usuario -->
        <div class="flex items-center space-x-2">
          
          <!-- Notificaciones Compactas -->
          <div class="relative">
            <button
              @click="toggleNotifications"
              class="relative p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors duration-200"
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
                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-100 py-2 z-50"
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
          
          <!-- Video Tutorial -->
          <button
            @click="showVideoTutorial"
            class="hidden md:flex p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
            title="Video tutorial"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </button>
          
          <!-- Ayuda -->
          <button
            @click="showHelp"
            class="hidden md:flex p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors duration-200"
            title="Ayuda"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </button>
          
          <!-- Botón 105 IA -->
          <button
            @click="toggleAIChat"
            class="hidden md:flex items-center space-x-2 px-3 py-2 bg-gradient-to-r from-violet-500 via-purple-500 to-indigo-600 hover:from-violet-600 hover:via-purple-600 hover:to-indigo-700 text-white rounded-lg transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105"
            title="Asistente IA 105"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <span class="text-sm font-bold">105 IA</span>
          </button>
          
          <!-- Separador -->
          <div class="hidden md:block h-5 w-px bg-gray-200"></div>
          
          <!-- Perfil de Usuario -->
          <div class="relative">
            <button
              @click="toggleUserDropdown"
              class="flex items-center space-x-2.5 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors duration-200 border border-gray-200"
            >
              <!-- Avatar -->
              <div class="w-9 h-9 bg-black rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">{{ currentUser.initials }}</span>
              </div>
              
              <!-- Info Usuario (Solo Desktop) -->
              <div class="hidden lg:block text-left">
                <div class="text-sm font-semibold text-gray-900">{{ currentUser.name }}</div>
                <div class="text-xs text-gray-500 font-medium">{{ currentUser.role?.name || 'User' }}</div>
              </div>
              
              <!-- Chevron -->
              <svg class="hidden lg:block w-4 h-4 text-gray-400 transition-transform duration-200" 
                   :class="{ 'rotate-180': userDropdownOpen }" 
                   fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            
            <!-- Dropdown Premium -->
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
                class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-100 py-2 z-50 backdrop-blur-sm"
                style="box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);"
                @click.stop
              >
                <!-- Header del Usuario -->
                <div class="px-4 py-3 border-b border-gray-100">
                  <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 bg-black rounded-lg flex items-center justify-center">
                      <span class="text-white font-bold text-sm">{{ currentUser.initials }}</span>
                    </div>
                    <div>
                      <div class="font-semibold text-sm text-gray-900">{{ currentUser.name }}</div>
                      <div class="text-xs text-gray-500 font-medium">{{ currentUser.role?.name || 'User' }}</div>
                    </div>
                  </div>
                </div>
                
                <!-- Opciones del Menu -->
                <div class="py-1">
                  <!-- Configuración -->
                  <button
                    v-if="shouldShowSettings"
                    @click="handleSettingsClick"
                    class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors font-medium"
                  >
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Configuración
                  </button>
                  
                  <!-- Separador -->
                  <div class="border-t border-gray-100 my-1"></div>
                  
                  <!-- Cerrar Sesión -->
                  <button
                    @click="handleLogout"
                    class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors font-semibold"
                  >
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Cerrar Sesión
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
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
        @click="closeVideoModal"
      >
        <div 
          class="bg-white rounded-xl shadow-2xl max-w-6xl w-full mx-4 overflow-hidden"
          @click.stop
        >
          <!-- Header del Modal -->
          <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">Tutorial: {{ currentModuleTitle }}</h3>
              <p class="text-sm text-gray-500">Video guía para esta sección</p>
            </div>
            <button
              @click="closeVideoModal"
              class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors"
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
            <div v-else class="absolute inset-0 flex items-center justify-center bg-gray-100">
              <div class="text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 002 2v8a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-500">No hay video tutorial disponible para esta sección</p>
                <p class="text-sm text-gray-400 mt-1">Pronto estará disponible</p>
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

const router = useRouter()

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
  'toggleSidebarCollapsed'
])

// Estados reactivos
const userDropdownOpen = ref(false)
const notificationsOpen = ref(false)
const videoModalOpen = ref(false)
const notificationCount = ref(3) // Ejemplo: 3 notificaciones
const notificationsSilent = ref(false) // Estado del silenciador
const aiChatOpen = ref(false) // Estado del chat IA

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

// Mostrar ayuda
const showHelp = () => {
  emit('show-help')
}

// Manejar clic en configuraciones
const handleSettingsClick = () => {
  emit('navigate-to-settings')
  userDropdownOpen.value = false
}

// Manejar logout
const handleLogout = () => {
  emit('logout')
  userDropdownOpen.value = false
}

// Cerrar dropdown al hacer clic fuera
const handleClickOutside = (event) => {
  // Cerrar dropdown de usuario si se hace clic fuera
  if (userDropdownOpen.value && !event.target.closest('.relative')) {
    userDropdownOpen.value = false
  }
  // Cerrar dropdown de notificaciones si se hace clic fuera
  if (notificationsOpen.value && !event.target.closest('.relative')) {
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
  // Event listeners
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEscape)
  
  // Cleanup al desmontar
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    document.removeEventListener('keydown', handleEscape)
  })
})
</script>