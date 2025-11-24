import { ref } from 'vue'

// Estado global de las notificaciones
const toasts = ref([])
let toastIdCounter = 0

export function useToast() {
  const showToast = (message, type = 'info', duration = 3000) => {
    const id = ++toastIdCounter
    const toast = {
      id,
      message,
      type, // 'success', 'error', 'warning', 'info'
      timestamp: Date.now()
    }
    
    toasts.value.push(toast)
    
    // Auto-remove después del duration
    if (duration > 0) {
      setTimeout(() => {
        removeToast(id)
      }, duration)
    }
    
    return id
  }

  const removeToast = (id) => {
    const index = toasts.value.findIndex(toast => toast.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  const clearToasts = () => {
    toasts.value = []
  }

  // Métodos de conveniencia
  const showSuccess = (message, duration = 3000) => {
    return showToast(message, 'success', duration)
  }

  const showError = (message, duration = 5000) => {
    return showToast(message, 'error', duration)
  }

  const showWarning = (message, duration = 4000) => {
    return showToast(message, 'warning', duration)
  }

  const showInfo = (message, duration = 3000) => {
    return showToast(message, 'info', duration)
  }

  return {
    toasts,
    showToast,
    removeToast,
    clearToasts,
    showSuccess,
    showError,
    showWarning,
    showInfo
  }
}