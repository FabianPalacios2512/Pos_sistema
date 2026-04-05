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
 * - 🌐 NUEVO: También incluye datos globales del negocio desde uiContextStore
 * 
 * USO:
 * - Vistas: Usan el composable useScreenContext() para actualizar datos
 * - Chat: Usa aiContextStore.getSystemPrompt() para obtener el contexto
 */

import { defineStore } from 'pinia'
import { useUIContextStore } from '../store/uiContextStore.js'

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
      // 🔒 Obtener rol y permisos del usuario actual
      let roleContext = ''
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}')
        const roleName = user.role?.name || ''
        const permissions = Array.isArray(user.role?.permissions) ? user.role.permissions : []
        const isAllPerms = permissions.includes('*') || permissions.includes('ALL') || permissions.includes('admin')
        const isVendedor = !isAllPerms && (roleName.toLowerCase().includes('vendedor') || roleName.toLowerCase().includes('cajero'))
        
        if (isVendedor) {
          const moduleNameMap = {
            'dashboard': 'Panel Principal', 'pos': 'Punto de Venta', 'invoices': 'Facturas',
            'returns': 'Devoluciones', 'products': 'Productos', 'categories': 'Categorías',
            'stock': 'Gestión de Stock', 'inventory': 'Inventario', 'customers': 'Clientes',
            'sales': 'Ventas', 'accounts-receivable': 'CrediTienda'
          }
          const modulosPermitidos = new Set()
          permissions.forEach(p => {
            const mod = p.split('.')[0]
            if (moduleNameMap[mod]) modulosPermitidos.add(moduleNameMap[mod])
          })
          roleContext = `
🔒 ROL DEL USUARIO: ${roleName} (VENDEDOR/CAJERO - ACCESO LIMITADO)
Módulos permitidos: ${Array.from(modulosPermitidos).join(', ')}
⚠️ NO tiene acceso a: Reportes, Gastos, Gestión de Usuarios, Configuración, Proveedores.
- No le sugieras navegar a módulos restringidos.
- No le muestres datos financieros globales (ganancias netas, márgenes, gastos).
- Si pregunta por algo restringido, dile amablemente que eso lo maneja el administrador.
- Sus ventas/facturas son solo las suyas, no las del negocio completo.
`
        } else {
          roleContext = `
👤 ROL DEL USUARIO: ${roleName || 'Administrador'} (ACCESO COMPLETO)
Tiene acceso a todos los módulos y datos del sistema sin restricción.
`
        }
      } catch {}

      // 🌐 Obtener datos globales del negocio desde uiContextStore
      let globalBusinessContext = ''
      try {
        const uiContextStore = useUIContextStore()
        // Acceder al valor del ref correctamente
        const globalData = uiContextStore.globalBusinessData
        
        if (globalData && globalData.ultimaActualizacion) {
          const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
          
          globalBusinessContext = `
🌐 DATOS GLOBALES DEL NEGOCIO (Información Actualizada):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

💰 VENTAS:
   • Ventas hoy: ${formatMoney(globalData.ventas?.ventasHoy)} (${globalData.ventas?.transaccionesHoy || 0} transacciones)
   • Ventas del mes: ${formatMoney(globalData.ventas?.ventasMes)} (${globalData.ventas?.transaccionesMes || 0} transacciones)
   • Ticket promedio: ${formatMoney(globalData.ventas?.ticketPromedio)}

📦 INVENTARIO:
   • Productos activos: ${globalData.inventario?.productosActivos || 0}
   • Productos con stock bajo: ${globalData.inventario?.stockBajo || 0}
   • Productos sin stock: ${globalData.inventario?.sinStock || 0}
   • Valor del inventario (costo): ${formatMoney(globalData.inventario?.valorInvertido)}
   • Valor potencial (venta): ${formatMoney(globalData.inventario?.valorPotencial)}
   • Ganancia estimada inventario: ${formatMoney(globalData.inventario?.gananciaEstimada)}

💸 GASTOS:
   • Gastos hoy: ${formatMoney(globalData.gastos?.gastosHoy)}
   • Gastos del mes: ${formatMoney(globalData.gastos?.gastosMes)}

📈 GANANCIAS Y RENTABILIDAD:
   • Ganancia bruta del mes: ${formatMoney(globalData.ganancias?.gananciaBrutaMes)} (ventas - costo productos)
   • Ganancia neta del mes: ${formatMoney(globalData.ganancias?.gananciaNeta)} (bruta - gastos)
   • Margen promedio: ${(globalData.ganancias?.margenPromedio || 0).toFixed(1)}%

🔄 DEVOLUCIONES:
   • Devoluciones hoy: ${globalData.devoluciones?.devolucionesHoy || 0} (${formatMoney(globalData.devoluciones?.montoHoy)})
   • Devoluciones del mes: ${globalData.devoluciones?.devolucionesMes || 0} (${formatMoney(globalData.devoluciones?.montoMes)})

🏦 ESTADO DE CAJA:
   • Estado: ${globalData.caja?.estado === 'abierta' ? 'ABIERTA' : 'CERRADA'}
   • Monto actual: ${formatMoney(globalData.caja?.montoActual)}

🧾 ÚLTIMA FACTURA GENERADA:
   • Número: ${globalData.ultimaFactura?.numero || 'N/A'}
   • Cliente: ${globalData.ultimaFactura?.cliente || 'N/A'}
   • Total: ${formatMoney(globalData.ultimaFactura?.total)}
   • Fecha: ${globalData.ultimaFactura?.fecha || 'N/A'}
   • Vendedor: ${globalData.ultimaFactura?.vendedor || 'N/A'}
   • Método de pago: ${globalData.ultimaFactura?.metodoPago || 'N/A'}

⚠️ ALERTAS DE STOCK BAJO:${globalData.alertas?.productosStockBajo?.length > 0 
  ? globalData.alertas.productosStockBajo.slice(0, 5).map(p => `\n   • ${p.nombre}: ${p.stock} unidades`).join('')
  : '\n   • No hay alertas de stock bajo'}

🏆 TOP PRODUCTOS MÁS VENDIDOS HOY:${globalData.rankings?.topProductosHoy?.length > 0
  ? globalData.rankings.topProductosHoy.slice(0, 5).map((p, i) => `\n   ${i+1}. ${p.nombre}: ${p.cantidad} unidades`).join('')
  : '\n   • Sin ventas registradas hoy'}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
`
        }
      } catch (e) {
        console.warn('⚠️ [aiContext] No se pudo obtener datos globales:', e.message)
      }

      // Si no hay contexto de pantalla pero sí hay datos globales, devolver solo los globales
      if (!this.hasContext && globalBusinessContext) {
        return `
[CONTEXTO DE NEGOCIO - INFORMACIÓN PARA RESPONDER PREGUNTAS]
${roleContext}
${globalBusinessContext}
INSTRUCCIONES PARA LA IA:
- Usa estos datos globales para responder preguntas sobre el negocio
- Si preguntan "¿cuánto vendí hoy?", responde con las ventas del día
- Si preguntan "¿cuál fue la última factura?", responde con los datos de la última factura
- Si preguntan por stock bajo, menciona los productos con alertas
- Si preguntan por el estado de la caja, indica si está abierta o cerrada
- Si preguntan por devoluciones, usa los datos de devoluciones
- NO menciones que tienes "contexto" - responde naturalmente
`.trim()
      }

      if (!this.hasContext) {
        return roleContext || ''
      }

      // Construir prompt estructurado pero conciso
      const prompt = `
[CONTEXTO DE PANTALLA - INFORMACIÓN INVISIBLE PARA EL USUARIO]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
${roleContext}
📍 UBICACIÓN ACTUAL: ${this.currentScreen}
📝 DESCRIPCIÓN: ${this.screenDescription}

📊 DATOS VISIBLES EN PANTALLA:
${this.formattedData}

⏰ Última actualización: ${this.lastUpdated ? new Date(this.lastUpdated).toLocaleTimeString('es-CO') : 'N/A'}
${globalBusinessContext}
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
