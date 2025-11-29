import { ref, computed } from 'vue'
import { appStore } from '../store/appStore.js'

const showUpgradeModal = ref(false)

export function useCreditienda() {
  // Usar directamente el appStore que ya tiene los settings cargados
  const isCreditiendaEnabled = computed(() => {
    return appStore.systemSettings?.creditienda_enabled === true
  })

  const showCreditiendaUpgradeModal = () => {
    showUpgradeModal.value = true
  }

  const closeUpgradeModal = () => {
    showUpgradeModal.value = false
  }

  return {
    isCreditiendaEnabled,
    showUpgradeModal,
    showCreditiendaUpgradeModal,
    closeUpgradeModal
  }
}
