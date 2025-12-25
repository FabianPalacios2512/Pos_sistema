import { defineStore } from 'pinia'

export const useAppStore = defineStore('app', {
  state: () => ({
    loading: false,
    darkMode: false
  }),
  
  actions: {
    setLoading(value) {
      this.loading = value
    },
    
    toggleDarkMode() {
      this.darkMode = !this.darkMode
    }
  }
})
