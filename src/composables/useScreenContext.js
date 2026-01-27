/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🎯 useScreenContext - Composable para Conciencia de Pantalla
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * Hook/Composable que permite a cualquier vista del sistema comunicar
 * su contexto a la IA. Simplifica el uso del store aiContext.
 * 
 * USO EN UNA VISTA:
 * ```javascript
 * import { useScreenContext } from '@/composables/useScreenContext'
 * 
 * // En setup()
 * const { setContext, updateData } = useScreenContext()
 * 
 * // Al montar o cuando cambien los datos
 * setContext({
 *   screen: 'Panel de Control',
 *   description: 'Dashboard principal con métricas del negocio',
 *   data: {
 *     ventasHoy: '$50.000',
 *     transacciones: 5,
 *     estadoCaja: 'Abierta'
 *   }
 * })
 * 
 * // Para actualizaciones parciales (ej: nueva venta en tiempo real)
 * updateData({ ventasHoy: '$75.000', transacciones: 6 })
 * ```
 */

import { onMounted, onUnmounted, watch } from 'vue'
import { useAIContextStore } from '@/stores/aiContext'

/**
 * Composable para gestionar el contexto de pantalla para la IA
 * 
 * @param {Object} options - Opciones de configuración
 * @param {boolean} options.clearOnUnmount - Si limpiar el contexto al desmontar (default: false)
 * @returns {Object} Métodos para gestionar el contexto
 */
export function useScreenContext(options = { clearOnUnmount: false }) {
  const aiContextStore = useAIContextStore()

  /**
   * Establece el contexto completo de la pantalla
   * @param {Object} context
   * @param {string} context.screen - Nombre de la pantalla
   * @param {string} context.description - Descripción breve
   * @param {Object} context.data - Datos estructurados
   */
  const setContext = ({ screen, description = '', data = {} }) => {
    aiContextStore.setScreenContext({
      screen,
      description,
      data
    })
  }

  /**
   * Actualiza solo los datos (sin cambiar pantalla)
   * Ideal para actualizaciones en tiempo real
   * @param {Object} data - Datos a actualizar/agregar
   */
  const updateData = (data) => {
    aiContextStore.updateScreenData(data)
  }

  /**
   * Limpia el contexto actual
   */
  const clearContext = () => {
    aiContextStore.clearContext()
  }

  /**
   * Obtiene el contexto actual (para debugging)
   */
  const getContext = () => {
    return {
      screen: aiContextStore.currentScreen,
      description: aiContextStore.screenDescription,
      data: aiContextStore.screenData,
      lastUpdated: aiContextStore.lastUpdated
    }
  }

  /**
   * Crea un watcher reactivo para actualizar el contexto automáticamente
   * cuando cambien ciertas variables
   * 
   * @param {Function} dataGetter - Función que retorna los datos a observar
   * @param {Object} screenInfo - Información de la pantalla
   * @param {string} screenInfo.screen - Nombre de la pantalla
   * @param {string} screenInfo.description - Descripción
   */
  const watchAndUpdate = (dataGetter, { screen, description }) => {
    // Establecer contexto inicial
    setContext({
      screen,
      description,
      data: dataGetter()
    })

    // Crear watcher para actualizaciones automáticas
    return watch(
      dataGetter,
      (newData) => {
        updateData(newData)
      },
      { deep: true }
    )
  }

  // Limpiar contexto al desmontar si está configurado
  if (options.clearOnUnmount) {
    onUnmounted(() => {
      clearContext()
    })
  }

  return {
    // Métodos principales
    setContext,
    updateData,
    clearContext,
    getContext,
    watchAndUpdate,
    
    // Acceso directo al store (para casos avanzados)
    store: aiContextStore
  }
}

/**
 * Helper para crear el objeto de datos del Dashboard
 * Basado en la estructura real del DashboardView_Executive.vue
 * 
 * @param {Object} params - Parámetros del dashboard
 * @returns {Object} Datos formateados para el contexto
 */
export function formatDashboardContext({
  hasOpenSession,
  cashAmount,
  todaySales,
  transactionsCount,
  averageTicket,
  lowStockCount,
  topProducts = [],
  chartTrend = '',
  recentTransactions = []
}) {
  return {
    // Estado de caja
    estadoCaja: {
      estado: hasOpenSession ? 'Abierta' : 'Cerrada',
      monto: `$${formatNumber(cashAmount)}`,
      descripcion: hasOpenSession 
        ? `Caja abierta con $${formatNumber(cashAmount)} en efectivo`
        : 'No hay caja abierta actualmente'
    },
    
    // Ventas del día
    ventasHoy: {
      total: `$${formatNumber(todaySales)}`,
      transacciones: transactionsCount,
      ticketPromedio: `$${formatNumber(averageTicket)}`,
      resumen: `${transactionsCount} ventas por un total de $${formatNumber(todaySales)}`
    },
    
    // Alertas de inventario
    alertasStock: {
      cantidad: lowStockCount,
      estado: lowStockCount === 0 ? 'Todo en orden' : `${lowStockCount} productos con stock bajo`,
      urgencia: lowStockCount > 5 ? 'alta' : lowStockCount > 0 ? 'media' : 'ninguna'
    },
    
    // Top productos (máximo 5)
    topProductos: topProducts.slice(0, 5).map((p, index) => ({
      posicion: index + 1,
      nombre: p.name || p.nombre,
      ingresos: `$${formatNumber(p.revenue || p.ingresos || 0)}`,
      vendidos: p.sold || p.vendidos || 0
    })),
    
    // Tendencia de ventas
    tendencia: chartTrend || 'Sin datos de tendencia disponibles',
    
    // Últimas transacciones (máximo 3 para no sobrecargar)
    ultimasTransacciones: recentTransactions.slice(0, 3).map(t => ({
      cliente: t.customer?.name || 'Cliente General',
      monto: `$${formatNumber(t.total)}`,
      estado: 'Pagado'
    }))
  }
}

/**
 * Formatea números para mostrar como moneda colombiana
 */
function formatNumber(value) {
  if (!value) return '0'
  return new Intl.NumberFormat('es-CO', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value)
}

export default useScreenContext
