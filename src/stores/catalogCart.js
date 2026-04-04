/**
 * Shared Cart Store for Public Catalog
 * Persists cart state across route navigations using reactive module-level state
 */
import { reactive, computed, toRefs } from 'vue'

const state = reactive({
  items: [],
  storeConfig: null,
  toast: { show: false, message: '', product: null }
})

let toastTimeout = null

export function useCatalogCart() {
  const cartItems = computed({
    get: () => state.items,
    set: (val) => { state.items = val }
  })

  const cartTotal = computed(() => {
    return state.items.reduce((total, item) => total + parseFloat(item.price || 0), 0)
  })

  const cartCount = computed(() => state.items.length)

  const addItem = (product) => {
    state.items.push(product)
    showToast(`${product.name} agregado a tu bolsa`, product)
  }

  const removeItem = (productId) => {
    const index = state.items.findIndex(item => item.id === productId)
    if (index > -1) state.items.splice(index, 1)
  }

  const clearCart = () => {
    state.items = []
  }

  const setStoreConfig = (config) => {
    state.storeConfig = config
  }

  const showToast = (message, product = null) => {
    if (toastTimeout) clearTimeout(toastTimeout)
    state.toast = { show: true, message, product }
    toastTimeout = setTimeout(() => {
      state.toast.show = false
    }, 2500)
  }

  const hideToast = () => {
    state.toast.show = false
    if (toastTimeout) clearTimeout(toastTimeout)
  }

  return {
    cartItems,
    cartTotal,
    cartCount,
    addItem,
    removeItem,
    clearCart,
    storeConfig: computed(() => state.storeConfig),
    setStoreConfig,
    toast: computed(() => state.toast),
    showToast,
    hideToast
  }
}
