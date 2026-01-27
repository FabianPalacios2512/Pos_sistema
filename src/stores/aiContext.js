/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🧠 AI Context Store - Sistema de Conciencia de Pantalla
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * Este store mantiene el contexto de lo que el usuario está viendo actualmente.
 * Permite que la IA tenga "conciencia" de la pantalla activa y sus datos.
 * 
 * ARQUITECTURA:
 * - Cada vista (Dashboard, Inventario, POS, etc.) actualiza este contexto
 * - El componente AI105Chat lee este contexto antes de enviar mensajes
 * - Se genera un "system prompt" invisible para la IA con los datos relevantes
 * 
 * USO:
 * - Vistas: Usan el composable useScreenContext() para actualizar datos
 * - Chat: Usa aiContextStore.getSystemPrompt() para obtener el contexto
 */

import { defineStore } from 'pinia'

export const useAIContextStore = defineStore('aiContext', {
  state: () => ({
    // Identificador de la pantalla actual
    currentScreen: 'Unknown',
    
    // Descripción breve de la pantalla (para contexto)
    screenDescription: '',
    
    // Datos estructurados de la pantalla actual
    screenData: {},
    
    // Timestamp de la última actualización
    lastUpdated: null,
    
    // Historial de pantallas visitadas (últimas 5)
    screenHistory: []
  }),

  getters: {
    /**
     * Devuelve un resumen legible del contexto actual
     */
    contextSummary: (state) => {
      if (state.currentScreen === 'Unknown') {
        return 'Sin contexto de pantalla disponible'
      }
      
      return `Pantalla: ${state.currentScreen}. ${state.screenDescription}`
    },

    /**
     * Indica si hay contexto válido disponible
     */
    hasContext: (state) => {
      return state.currentScreen !== 'Unknown' && Object.keys(state.screenData).length > 0
    },

    /**
     * Devuelve los datos formateados para la IA
     */
    formattedData: (state) => {
      try {
        return JSON.stringify(state.screenData, null, 2)
      } catch (e) {
        return '{}'
      }
    }
  },

  actions: {
    /**
     * Actualiza el contexto de pantalla completo
     * @param {Object} params - Parámetros del contexto
     * @param {string} params.screen - Nombre de la pantalla
     * @param {string} params.description - Descripción breve
     * @param {Object} params.data - Datos estructurados de la pantalla
     */
    setScreenContext({ screen, description = '', data = {} }) {
      // Guardar pantalla anterior en historial
      if (this.currentScreen !== 'Unknown' && this.currentScreen !== screen) {
        this.screenHistory.unshift({
          screen: this.currentScreen,
          timestamp: this.lastUpdated
        })
        // Mantener solo las últimas 5
        if (this.screenHistory.length > 5) {
          this.screenHistory = this.screenHistory.slice(0, 5)
        }
      }

      this.currentScreen = screen
      this.screenDescription = description
      this.screenData = data
      this.lastUpdated = new Date().toISOString()
    },

    /**
     * Actualiza solo los datos de la pantalla actual (sin cambiar la pantalla)
     * Útil para actualizaciones en tiempo real (ej: nueva venta)
     * @param {Object} data - Datos actualizados
     */
    updateScreenData(data) {
      this.screenData = {
        ...this.screenData,
        ...data
      }
      this.lastUpdated = new Date().toISOString()
    },

    /**
     * Limpia el contexto (cuando el usuario sale de una vista importante)
     */
    clearContext() {
      this.currentScreen = 'Unknown'
      this.screenDescription = ''
      this.screenData = {}
      this.lastUpdated = null
    },

    /**
     * Genera el system prompt para enviar a la IA
     * Este es el método clave que usa el Chat
     * @returns {string} System prompt con el contexto
     */
    getSystemPrompt() {
      if (!this.hasContext) {
        return ''
      }

      // Construir prompt estructurado pero conciso
      const prompt = `
[CONTEXTO DE PANTALLA - INFORMACIÓN INVISIBLE PARA EL USUARIO]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📍 UBICACIÓN ACTUAL: ${this.currentScreen}
📝 DESCRIPCIÓN: ${this.screenDescription}

📊 DATOS VISIBLES EN PANTALLA:
${this.formattedData}

⏰ Última actualización: ${this.lastUpdated ? new Date(this.lastUpdated).toLocaleTimeString('es-CO') : 'N/A'}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

INSTRUCCIONES PARA LA IA:
- El usuario está viendo la pantalla "${this.currentScreen}"
- Puedes hacer referencia a los datos mostrados arriba si son relevantes para la pregunta
- Si el usuario pregunta "¿cuánto vendí hoy?" y estás en el Dashboard, usa los datos de ventas
- Si pregunta sobre productos y hay datos de productos, úsalos
- NO menciones que tienes "contexto de pantalla" - actúa naturalmente
- Responde como si pudieras "ver" lo mismo que el usuario
`.trim()

      return prompt
    },

    /**
     * Obtiene un contexto resumido (para logs o debugging)
     */
    getContextDebugInfo() {
      return {
        screen: this.currentScreen,
        description: this.screenDescription,
        dataKeys: Object.keys(this.screenData),
        lastUpdated: this.lastUpdated,
        historyCount: this.screenHistory.length
      }
    }
  }
})
