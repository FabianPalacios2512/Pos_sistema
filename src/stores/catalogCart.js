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
    return state.items.reduce((total, item) => total + (parseFloat(item.price || 0) * (item.quantity || 1)), 0)
  })

  const cartCount = computed(() => {
    return state.items.reduce((total, item) => total + (item.quantity || 1), 0)
  })

  const addItem = (product) => {
    const existingIndex = state.items.findIndex(item => item.id === product.id)
    if (existingIndex > -1) {
      state.items[existingIndex].quantity = (state.items[existingIndex].quantity || 1) + 1
    } else {
      state.items.push({ ...product, quantity: 1 })
    }
    showToast(`${product.name} agregado a tu bolsa`, product)
  }

  const updateItemQuantity = (productId, delta) => {
    const index = state.items.findIndex(item => item.id === productId)
    if (index > -1) {
      const newQuantity = (state.items[index].quantity || 1) + delta
      if (newQuantity <= 0) {
        state.items.splice(index, 1)
      } else {
        state.items[index].quantity = newQuantity
      }
    }
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
    updateItemQuantity,
    removeItem,
    clearCart,
    storeConfig: computed(() => state.storeConfig),
    setStoreConfig,
    toast: computed(() => state.toast),
    showToast,
    hideToast
  }
}
