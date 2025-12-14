<template>
  <div class="fixed bottom-6 right-6 z-50 space-y-3 max-w-md">
    <!-- Contenedor para las notificaciones -->
    <transition-group 
      name="toast" 
      tag="div" 
      class="space-y-3"
      enter-active-class="toast-enter-active"
      leave-active-class="toast-leave-active"
      enter-from-class="toast-enter-from"
      leave-to-class="toast-leave-to"
    >
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="[
          'bg-white rounded-lg shadow-lg border-l-4 p-4 min-w-[350px] max-w-md',
          'transform transition-all duration-300 ease-in-out',
          getNotificationBorderClass(notification.type)
        ]"
        @mouseenter="pauseAutoClose(notification.id)"
        @mouseleave="resumeAutoClose(notification.id)"
      >
        <!-- Header -->
        <div class="flex items-start justify-between mb-2">
          <div class="flex items-center">
            <div :class="[
              'p-2 rounded-full mr-3',
              getNotificationIconBgClass(notification.type)
            ]">
              <i :class="[
                'text-sm',
                getNotificationIconClass(notification.type)
              ]"></i>
            </div>
            <div>
              <h4 class="text-sm font-semibold text-gray-900">{{ notification.title }}</h4>
              <p class="text-xs text-gray-500">{{ translateCategory(notification.category) }}</p>
            </div>
          </div>
          <button 
            @click="dismissNotification(notification.id)"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>

        <!-- Content -->
        <div class="mb-3">
          <p class="text-sm text-gray-700">{{ notification.message }}</p>
          <div v-if="notification.product_name" class="flex items-center mt-2 text-xs text-gray-500">
            <i class="fas fa-box mr-1"></i>
            <span>{{ notification.product_name }}</span>
            <span v-if="notification.current_stock !== undefined" class="ml-2">
              • Stock: {{ notification.current_stock }}
            </span>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
          <div class="flex space-x-2">
            <button 
              v-if="notification.action_text"
              @click="handleActionClick(notification)"
              class="bg-indigo-600 text-white px-3 py-1 text-xs rounded hover:bg-indigo-700 transition-colors"
            >
              {{ notification.action_text }}
            </button>
            <button 
              @click="dismissNotification(notification.id)"
              class="bg-gray-100 text-gray-700 px-3 py-1 text-xs rounded hover:bg-gray-200 transition-colors"
            >
              Entendido
            </button>
          </div>
          <button 
            @click="dismissForever(notification)"
            class="text-xs text-gray-500 hover:text-gray-700 transition-colors underline"
          >
            No volver a mostrar
          </button>
        </div>

        <!-- Progress bar for auto-close - OCULTA para mejor apariencia -->
        <!-- <div 
          v-if="notification.autoClose && !notification.paused"
          class="mt-3 w-full bg-gray-200 rounded-full h-1"
        >
          <div 
            class="bg-indigo-600 h-1 rounded-full transition-all ease-linear"
            :style="{ 
              width: `${notification.progress}%`,
              transitionDuration: `${notification.duration}ms`
            }"
          ></div>
        </div> -->
      </div>
    </transition-group>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'

export default {
  name: 'ToastNotifications',
  emits: ['action-click', 'dismiss-forever'],
  setup(props, { emit }) {
    const notifications = ref([])
    const timers = ref(new Map())

    // Métodos para gestionar notificaciones
    const addNotification = (notification) => {
      const id = notification.id || `toast-${Date.now()}-${Math.random()}`
      const newNotification = {
        id,
        title: notification.title || 'Notificación',
        message: notification.message,
        type: notification.severity || notification.type || 'info',
        category: notification.category || 'Sistema',
        product_name: notification.product_name,
        current_stock: notification.current_stock,
        action_text: notification.action_text,
        action_url: notification.action_url,
        autoClose: notification.autoClose !== false,
        duration: notification.duration || 8000,
        progress: 0,
        paused: false,
        originalNotification: notification
      }

      notifications.value.push(newNotification)

      // Configurar auto-close
      if (newNotification.autoClose) {
        startAutoClose(newNotification)
      }

      return id
    }

    const dismissNotification = (id) => {
      const index = notifications.value.findIndex(n => n.id === id)
      if (index > -1) {
        // Limpiar timer si existe
        if (timers.value.has(id)) {
          clearTimeout(timers.value.get(id))
          timers.value.delete(id)
        }
        notifications.value.splice(index, 1)
      }
    }

    const dismissForever = (notification) => {
      emit('dismiss-forever', notification.originalNotification)
      dismissNotification(notification.id)
    }

    const handleActionClick = (notification) => {
      emit('action-click', notification.originalNotification)
      dismissNotification(notification.id)
    }

    const startAutoClose = (notification) => {
      if (!notification.autoClose || notification.paused) return
      
      const startTime = Date.now()
      
      const updateProgress = () => {
        if (notification.paused) return
        
        const elapsed = Date.now() - startTime
        const progress = Math.min((elapsed / notification.duration) * 100, 100)
        notification.progress = progress

        if (progress >= 100) {
          dismissNotification(notification.id)
        } else {
          const timerId = setTimeout(updateProgress, 100)
          timers.value.set(notification.id, timerId)
        }
      }

      updateProgress()
    }

    const pauseAutoClose = (id) => {
      const notification = notifications.value.find(n => n.id === id)
      if (notification && !notification.paused) {
        notification.paused = true
        if (timers.value.has(id)) {
          clearTimeout(timers.value.get(id))
          timers.value.delete(id)
        }
      }
    }

    const resumeAutoClose = (id) => {
      const notification = notifications.value.find(n => n.id === id)
      if (notification && notification.paused && notification.autoClose) {
        notification.paused = false
        // Reiniciar el temporizador desde donde se pausó
        const remainingTime = notification.duration * (1 - notification.progress / 100)
        
        setTimeout(() => {
          if (!notification.paused) {
            dismissNotification(notification.id)
          }
        }, remainingTime)
      }
    }

    // Métodos de estilo
    const translateCategory = (category) => {
      const translations = {
        'stock': 'Inventario',
        'purchase_recommendation': 'Recomendación de Compra',
        'trend_analysis': 'Análisis de Tendencias',
        'rotation': 'Rotación de Productos',
        'margin': 'Análisis de Márgenes',
        'discount': 'Descuentos',
        'Sistema': 'Sistema',
        'inventory': 'Inventario'
      }
      return translations[category] || category
    }

    const getNotificationBorderClass = (type) => {
      const classes = {
        critical: 'border-red-500',
        warning: 'border-yellow-500',
        info: 'border-blue-500',
        success: 'border-green-500'
      }
      return classes[type] || 'border-gray-300'
    }

    const getNotificationIconBgClass = (type) => {
      const classes = {
        critical: 'bg-red-100 text-red-600',
        warning: 'bg-yellow-100 text-yellow-600', 
        info: 'bg-blue-100 text-blue-600',
        success: 'bg-green-100 text-green-600'
      }
      return classes[type] || 'bg-gray-100 text-gray-600'
    }

    const getNotificationIconClass = (type) => {
      const classes = {
        critical: 'fas fa-exclamation-triangle',
        warning: 'fas fa-exclamation-circle',
        info: 'fas fa-info-circle',
        success: 'fas fa-check-circle'
      }
      return classes[type] || 'fas fa-bell'
    }

    // Limpiar timers al desmontar
    onUnmounted(() => {
      timers.value.forEach(timer => clearTimeout(timer))
      timers.value.clear()
    })

    // Exponer métodos para uso externo
    const show = (notification) => {
      return addNotification(notification)
    }

    const clear = () => {
      notifications.value.forEach(n => dismissNotification(n.id))
    }

    const clearType = (type) => {
      notifications.value
        .filter(n => n.type === type)
        .forEach(n => dismissNotification(n.id))
    }

    return {
      notifications,
      addNotification,
      dismissNotification,
      dismissForever,
      handleActionClick,
      pauseAutoClose,
      resumeAutoClose,
      translateCategory,
      getNotificationBorderClass,
      getNotificationIconBgClass,
      getNotificationIconClass,
      show,
      clear,
      clearType
    }
  }
}
</script>

<style scoped>
/* Animaciones para las notificaciones */
.toast-enter-active {
  transition: all 0.3s ease-out;
}

.toast-leave-active {
  transition: all 0.3s ease-in;
}

.toast-enter-from {
  transform: translateX(100%) translateY(20px);
  opacity: 0;
}

.toast-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

/* Efecto de hover suave */
.toast-notification:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

/* Animación de la barra de progreso */
.progress-bar {
  animation: shrink linear;
}

@keyframes shrink {
  from { width: 100%; }
  to { width: 0%; }
}
</style>