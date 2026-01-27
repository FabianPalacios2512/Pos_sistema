/**
 * 🎯 uiContextStore - Store global de contexto de UI para IA de voz
 * 
 * Este store rastrea:
 * - Módulo actual en el que está el usuario
 * - Elemento seleccionado (factura, producto, cliente, proveedor, etc.)
 * - Modales abiertos
 * - Acciones disponibles en el contexto actual
 * 
 * La IA de voz usa este store para saber qué está viendo el usuario
 * y ejecutar acciones contextualmente (enviar email, WhatsApp, PDF, etc.)
 */

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useUIContextStore = defineStore('uiContext', () => {
  // ═══════════════════════════════════════════════════════════════
  // ESTADO
  // ═══════════════════════════════════════════════════════════════
  
  // Módulo actual
  const currentModule = ref('dashboard')
  
  // Elemento seleccionado en el módulo actual
  const selectedElement = ref(null)
  // Tipo: { type: 'invoice'|'product'|'customer'|'supplier'|'category'|'return', data: {...} }
  
  // Modal abierto actualmente
  const activeModal = ref(null)
  // Tipo: { name: 'productDetail'|'invoicePreview'|'customerEdit'|etc, data: {...} }
  
  // Acciones disponibles en el contexto actual
  const availableActions = ref([])
  // Array de: { id: 'sendEmail', label: 'Enviar por Email', icon: 'email' }
  
  // Callbacks para ejecutar acciones (registrados por los componentes)
  const actionCallbacks = ref({})
  
  // 🧠 Datos de pantalla para la IA (estado de caja, ventas, KPIs, etc.)
  const screenData = ref({})
  // Estructura libre según el módulo. Ej para Dashboard:
  // { estadoCaja: {...}, ventasHoy: {...}, topProductos: [...], alertasStock: {...} }
  
  // ═══════════════════════════════════════════════════════════════
  // 🌐 DATOS GLOBALES DEL NEGOCIO (SIEMPRE DISPONIBLES PARA LA IA)
  // ═══════════════════════════════════════════════════════════════
  // Estos datos NUNCA se limpian al cambiar de módulo.
  // La IA siempre tiene acceso a esta información "de primera mano"
  // para responder preguntas sobre el negocio desde cualquier vista.
  const globalBusinessData = ref({
    // Última actualización
    ultimaActualizacion: null,
    
    // 📦 Inventario
    inventario: {
      productosActivos: 0,
      productosTotal: 0,
      valorInvertido: 0,           // Costo total del inventario
      valorPotencial: 0,           // Precio venta total
      gananciaEstimada: 0,         // Diferencia
      stockBajo: 0,
      sinStock: 0
    },
    
    // 💰 Ventas
    ventas: {
      ventasHoy: 0,
      ventasMes: 0,
      transaccionesHoy: 0,
      transaccionesMes: 0,
      ticketPromedio: 0
    },
    
    // 💸 Gastos
    gastos: {
      gastosMes: 0,
      gastosHoy: 0
    },
    
    // 📈 Ganancias
    ganancias: {
      gananciaBrutaMes: 0,        // Ventas - Costo productos
      gananciaNeta: 0,            // Bruta - Gastos
      margenPromedio: 0
    },
    
    // 📊 Estado de Caja
    caja: {
      estado: 'cerrada',          // 'abierta' | 'cerrada'
      montoActual: 0
    },
    
    // ⚠️ Alertas
    alertas: {
      productosStockBajo: [],
      productosSinStock: []
    },
    
    // 🏆 Rankings
    rankings: {
      topProductosHoy: [],
      topProductosMes: []
    }
  })
  
  // ═══════════════════════════════════════════════════════════════
  // GETTERS / COMPUTED
  // ═══════════════════════════════════════════════════════════════
  
  // Resumen del contexto actual para la IA
  const contextSummary = computed(() => {
    const moduleNames = {
      'dashboard': 'Panel Principal',
      'pos': 'Punto de Venta',
      'invoices': 'Facturas',
      'products': 'Productos',
      'customers': 'Clientes',
      'suppliers': 'Proveedores',
      'categories': 'Categorías',
      'stock': 'Control de Stock',
      'returns-management': 'Devoluciones',
      'reports': 'Reportes',
      'settings': 'Configuración',
      'warehouses': 'Bodegas',
      'intelligent_inventory': 'Inventario Inteligente',
      'operational-expenses': 'Gastos Operativos',
      'cash-register': 'Control de Caja'
    }
    
    let summary = `Estás en: ${moduleNames[currentModule.value] || currentModule.value}`
    
    // Agregar info del elemento seleccionado
    if (selectedElement.value) {
      const el = selectedElement.value
      switch (el.type) {
        case 'invoice':
          summary += `\nFactura seleccionada: ${el.data.invoice_number || el.data.number || 'N/A'}`
          summary += ` | Total: $${parseFloat(el.data.total || 0).toLocaleString()}`
          summary += ` | Cliente: ${el.data.customer_name || el.data.customer || 'Consumidor Final'}`
          summary += ` | Estado: ${el.data.status === 'completed' ? 'Pagada' : el.data.status}`
          break
        case 'product':
          summary += `\nProducto seleccionado: ${el.data.name || 'N/A'}`
          summary += ` | Precio: $${parseFloat(el.data.sale_price || el.data.price || 0).toLocaleString()}`
          summary += ` | Stock: ${el.data.stock || 0} unidades`
          if (el.data.category_name) summary += ` | Categoría: ${el.data.category_name}`
          break
        case 'customer':
          summary += `\nCliente seleccionado: ${el.data.name || el.data.full_name || 'N/A'}`
          if (el.data.phone) summary += ` | Tel: ${el.data.phone}`
          if (el.data.email) summary += ` | Email: ${el.data.email}`
          break
        case 'supplier':
          summary += `\nProveedor seleccionado: ${el.data.name || 'N/A'}`
          if (el.data.phone) summary += ` | Tel: ${el.data.phone}`
          if (el.data.contact_name) summary += ` | Contacto: ${el.data.contact_name}`
          break
        case 'return':
          summary += `\nDevolución seleccionada: ${el.data.return_number || el.data.id || 'N/A'}`
          summary += ` | Factura original: ${el.data.invoice_number || 'N/A'}`
          summary += ` | Total: $${parseFloat(el.data.total || 0).toLocaleString()}`
          break
        case 'expense':
          summary += `\nGasto seleccionado: ${el.data.description || el.data.concept || 'N/A'}`
          summary += ` | Monto: $${parseFloat(el.data.amount || 0).toLocaleString()}`
          break
      }
    }
    
    // Agregar info del modal abierto
    if (activeModal.value) {
      const modalNames = {
        'productDetail': 'Detalle de producto',
        'productEdit': 'Editando producto',
        'customerEdit': 'Editando cliente',
        'invoicePreview': 'Vista previa de factura',
        'phoneModal': 'Ingreso de teléfono para WhatsApp',
        'returnModal': 'Procesando devolución',
        'paymentModal': 'Modal de pago'
      }
      summary += `\nModal abierto: ${modalNames[activeModal.value.name] || activeModal.value.name}`
    }
    
    // Agregar acciones disponibles
    if (availableActions.value.length > 0) {
      summary += `\nAcciones disponibles: ${availableActions.value.map(a => a.label).join(', ')}`
    }
    
    // 🧠 Agregar datos de pantalla (Dashboard KPIs, etc.)
    if (screenData.value && Object.keys(screenData.value).length > 0) {
      summary += `\n\n📊 DATOS EN PANTALLA:`
      
      // Estado de caja
      if (screenData.value.estadoCaja) {
        const caja = screenData.value.estadoCaja
        summary += `\n• Estado de Caja: ${caja.estado} - ${caja.monto}`
      }
      
      // Ventas del día
      if (screenData.value.ventasHoy) {
        const ventas = screenData.value.ventasHoy
        summary += `\n• Ventas Hoy: ${ventas.total} (${ventas.transacciones} transacciones)`
        if (ventas.ticketPromedio) summary += ` - Ticket promedio: ${ventas.ticketPromedio}`
      }
      
      // Alertas de stock
      if (screenData.value.alertasStock) {
        const alertas = screenData.value.alertasStock
        summary += `\n• Alertas de Stock: ${alertas.estado}`
      }
      
      // Top productos
      if (screenData.value.topProductos && screenData.value.topProductos.length > 0) {
        summary += `\n• Top Productos:`
        screenData.value.topProductos.forEach(p => {
          summary += `\n  ${p.posicion}. ${p.nombre} - ${p.ingresos}`
        })
      }
      
      // Tendencia
      if (screenData.value.tendencia) {
        summary += `\n• Tendencia: ${screenData.value.tendencia}`
      }
      
      // Período del gráfico (Dashboard)
      if (screenData.value.periodoGrafico) {
        const periodo = screenData.value.periodoGrafico
        summary += `\n• Gráfico mostrando: ${periodo.descripcion} (${periodo.valor})`
        summary += `\n• Puedes cambiar a: ${periodo.opcionesDisponibles.join(', ')}`
      }
      
      // 📄 DATOS DE FACTURAS (módulo invoices)
      if (screenData.value.resumenFacturas) {
        const facturas = screenData.value.resumenFacturas
        summary += `\n• Total Facturas: ${facturas.total}`
        summary += `\n• Facturas del Mes: ${facturas.facturasDelMes} (${facturas.totalFacturado})`
        if (facturas.porEstado) {
          summary += `\n• Por Estado: Pagadas: ${facturas.porEstado.pagadas}, Pendientes: ${facturas.porEstado.pendientes}, Anuladas: ${facturas.porEstado.anuladas}, Devueltas: ${facturas.porEstado.devueltas}`
        }
        summary += `\n• Cotizaciones activas: ${facturas.cotizaciones}`
      }
      
      // 📄 FACTURA SELECCIONADA (para acciones de envío)
      if (screenData.value.facturaSeleccionada) {
        const factura = screenData.value.facturaSeleccionada
        summary += `\n\n🎯 FACTURA SELECCIONADA:`
        summary += `\n• Número: ${factura.numero} (${factura.tipo})`
        summary += `\n• Estado: ${factura.estado}`
        summary += `\n• Cliente: ${factura.cliente}`
        summary += `\n• Total: ${factura.total}`
        summary += `\n• Fecha: ${factura.fecha}`
        summary += `\n• Tiene Email: ${factura.tieneEmail ? 'SÍ (' + factura.email + ')' : 'NO'}`
        summary += `\n• Tiene Teléfono: ${factura.tieneTelefono ? 'SÍ (' + factura.telefono + ')' : 'NO'}`
      }
      
      // 📋 INSTRUCCIONES PARA LA IA (sobre envíos)
      if (screenData.value.instrucciones) {
        summary += `\n\n⚠️ INSTRUCCIONES IMPORTANTES:`
        summary += `\n• WhatsApp: ${screenData.value.instrucciones.enviarWhatsApp}`
        summary += `\n• Email: ${screenData.value.instrucciones.enviarEmail}`
      }
      
      // 🔄 DATOS DE DEVOLUCIONES (módulo returns-management)
      if (screenData.value.resumenDevoluciones) {
        const devs = screenData.value.resumenDevoluciones
        summary += `\n• Total Devoluciones: ${devs.total}`
        summary += `\n• Total Devuelto: ${devs.totalDevuelto}`
        summary += `\n• Completadas: ${devs.completadas} (${devs.montoCompletado})`
        summary += `\n• Pendientes: ${devs.pendientes} (${devs.montoPendiente})`
        summary += `\n• Productos Devueltos: ${devs.productosDevueltos} unidades`
      }
      
      // 🔄 DEVOLUCIÓN SELECCIONADA (para acciones de envío)
      if (screenData.value.devolucionSeleccionada) {
        const dev = screenData.value.devolucionSeleccionada
        summary += `\n\n🎯 DEVOLUCIÓN SELECCIONADA:`
        summary += `\n• Número: ${dev.numero}`
        summary += `\n• Estado: ${dev.estado}`
        summary += `\n• Factura Original: ${dev.facturaOriginal}`
        summary += `\n• Cliente: ${dev.cliente}`
        summary += `\n• Total: ${dev.total}`
        summary += `\n• Fecha: ${dev.fecha}`
        summary += `\n• Motivo: ${dev.motivo}`
        summary += `\n• Método Reembolso: ${dev.metodoReembolso}`
        summary += `\n• Items devueltos: ${dev.items}`
        summary += `\n• Tiene Email: ${dev.tieneEmail ? 'SÍ (' + dev.email + ')' : 'NO'}`
        summary += `\n• Tiene Teléfono: ${dev.tieneTelefono ? 'SÍ (' + dev.telefono + ')' : 'NO'}`
      }
      
      // 📦 DATOS DE PRODUCTOS (módulo products)
      if (screenData.value.resumenProductos) {
        const prods = screenData.value.resumenProductos
        summary += `\n\n📦 PRODUCTOS EN PANTALLA:`
        summary += `\n• Total: ${prods.total}`
        summary += `\n• Activos: ${prods.activos}`
        summary += `\n• Inactivos: ${prods.inactivos}`
        summary += `\n• Stock Bajo: ${prods.stockBajo}`
        summary += `\n• Valor Inventario: $${prods.valorInventario?.toLocaleString('es-CO') || 0}`
        summary += `\n• Categorías: ${prods.categorias}`
        summary += `\n• Tipo Tienda: ${screenData.value.tipoTienda || 'general'}`
      }
      
      // 📦 FILTROS ACTIVOS EN PRODUCTOS
      if (screenData.value.filtrosActivos) {
        const filtros = screenData.value.filtrosActivos
        if (filtros.busqueda || filtros.categoria || filtros.estado) {
          summary += `\n\n🔍 FILTROS ACTIVOS:`
          if (filtros.busqueda) summary += `\n• Búsqueda: "${filtros.busqueda}"`
          if (filtros.categoria) summary += `\n• Categoría: ${filtros.categoria}`
          if (filtros.estado) summary += `\n• Estado: ${filtros.estado}`
        }
      }
      
      // 📦 ALERTAS DE STOCK BAJO
      if (screenData.value.alertasStockBajo && screenData.value.alertasStockBajo.length > 0) {
        summary += `\n\n⚠️ PRODUCTOS CON STOCK BAJO (${screenData.value.alertasStockBajo.length}):`
        screenData.value.alertasStockBajo.slice(0, 5).forEach(p => {
          summary += `\n• ${p.nombre}: ${p.stockActual}/${p.stockMinimo} (${p.categoria})`
        })
        if (screenData.value.alertasStockBajo.length > 5) {
          summary += `\n• ... y ${screenData.value.alertasStockBajo.length - 5} más`
        }
      }
      
      // 📦 PRODUCTO SELECCIONADO
      if (screenData.value.productoSeleccionado) {
        const prod = screenData.value.productoSeleccionado
        summary += `\n\n🎯 PRODUCTO SELECCIONADO:`
        summary += `\n• ID: ${prod.id}`
        summary += `\n• Nombre: ${prod.nombre}`
        summary += `\n• SKU: ${prod.sku || 'Sin SKU'}`
        summary += `\n• Precio: $${prod.precio?.toLocaleString('es-CO') || 0}`
        summary += `\n• Costo: $${prod.costo?.toLocaleString('es-CO') || 0}`
        summary += `\n• Stock: ${prod.stock || 0}`
        summary += `\n• Categoría: ${prod.categoria || 'Sin categoría'}`
        summary += `\n• Activo: ${prod.activo ? 'Sí' : 'No'}`
      }
      
      // 📦 SEDES DISPONIBLES PARA PRODUCTOS
      if (screenData.value.sedesDisponibles && screenData.value.sedesDisponibles.length > 1) {
        summary += `\n\n🏢 SEDES DISPONIBLES (${screenData.value.sedesDisponibles.length}):`
        screenData.value.sedesDisponibles.forEach(s => {
          summary += `\n• ${s.nombre} (ID: ${s.id})`
        })
      }
      
      // 📦 MODAL DE PRODUCTO ABIERTO
      if (screenData.value.modalAbierto) {
        summary += `\n\n📝 MODAL ABIERTO: ${screenData.value.modalAbierto === 'crear' ? 'Nuevo Producto' : 'Editar Producto'}`
      }
    }
    
    return summary
  })
  
  // ═══════════════════════════════════════════════════════════════
  // ACCIONES
  // ═══════════════════════════════════════════════════════════════
  
  // Actualizar módulo actual
  const setCurrentModule = (moduleName) => {
    currentModule.value = moduleName
    // Limpiar selección al cambiar de módulo
    selectedElement.value = null
    activeModal.value = null
    availableActions.value = []
    // También limpiar datos de pantalla al cambiar de módulo
    screenData.value = {}
  }
  
  // 🧠 Actualizar datos de pantalla (KPIs, métricas visibles)
  const setScreenData = (data) => {
    screenData.value = data
  }
  
  // 🧠 Actualizar parcialmente datos de pantalla
  const updateScreenData = (partialData) => {
    screenData.value = { ...screenData.value, ...partialData }
  }
  
  // ═══════════════════════════════════════════════════════════════
  // 🌐 FUNCIONES PARA DATOS GLOBALES DEL NEGOCIO
  // ═══════════════════════════════════════════════════════════════
  
  // Actualizar datos globales completos
  const setGlobalBusinessData = (data) => {
    globalBusinessData.value = {
      ...globalBusinessData.value,
      ...data,
      ultimaActualizacion: new Date().toISOString()
    }
  }
  
  // Actualizar una sección específica de datos globales
  const updateGlobalBusinessSection = (section, data) => {
    if (globalBusinessData.value[section]) {
      globalBusinessData.value[section] = {
        ...globalBusinessData.value[section],
        ...data
      }
      globalBusinessData.value.ultimaActualizacion = new Date().toISOString()
    }
  }
  
  // Obtener resumen global para la IA (siempre disponible)
  const getGlobalBusinessSummary = () => {
    const g = globalBusinessData.value
    const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
    
    let summary = '\n\n🌐 RESUMEN GLOBAL DEL NEGOCIO (Datos de Primera Mano):'
    
    // Inventario
    summary += `\n📦 INVENTARIO:`
    summary += `\n   • Productos activos: ${g.inventario.productosActivos} de ${g.inventario.productosTotal}`
    summary += `\n   • Valor invertido: ${formatMoney(g.inventario.valorInvertido)}`
    summary += `\n   • Valor potencial (ventas): ${formatMoney(g.inventario.valorPotencial)}`
    summary += `\n   • Ganancia estimada: ${formatMoney(g.inventario.gananciaEstimada)}`
    summary += `\n   • Stock bajo: ${g.inventario.stockBajo} productos`
    summary += `\n   • Sin stock: ${g.inventario.sinStock} productos`
    
    // Ventas
    summary += `\n💰 VENTAS:`
    summary += `\n   • Ventas hoy: ${formatMoney(g.ventas.ventasHoy)} (${g.ventas.transaccionesHoy} transacciones)`
    summary += `\n   • Ventas del mes: ${formatMoney(g.ventas.ventasMes)} (${g.ventas.transaccionesMes} transacciones)`
    summary += `\n   • Ticket promedio: ${formatMoney(g.ventas.ticketPromedio)}`
    
    // Gastos
    summary += `\n💸 GASTOS:`
    summary += `\n   • Gastos del mes: ${formatMoney(g.gastos.gastosMes)}`
    summary += `\n   • Gastos hoy: ${formatMoney(g.gastos.gastosHoy)}`
    
    // Ganancias
    summary += `\n📈 GANANCIAS:`
    summary += `\n   • Ganancia bruta del mes: ${formatMoney(g.ganancias.gananciaBrutaMes)}`
    summary += `\n   • Ganancia neta: ${formatMoney(g.ganancias.gananciaNeta)}`
    summary += `\n   • Margen promedio: ${g.ganancias.margenPromedio}%`
    
    // Caja
    summary += `\n🏦 CAJA:`
    summary += `\n   • Estado: ${g.caja.estado === 'abierta' ? 'ABIERTA' : 'CERRADA'}`
    summary += `\n   • Monto actual: ${formatMoney(g.caja.montoActual)}`
    
    // Alertas
    if (g.alertas.productosStockBajo.length > 0) {
      summary += `\n⚠️ ALERTAS (${g.alertas.productosStockBajo.length} productos con stock bajo):`
      g.alertas.productosStockBajo.slice(0, 3).forEach(p => {
        summary += `\n   • ${p.nombre}: ${p.stock} unidades`
      })
      if (g.alertas.productosStockBajo.length > 3) {
        summary += `\n   • ...y ${g.alertas.productosStockBajo.length - 3} más`
      }
    }
    
    // Top productos
    if (g.rankings.topProductosHoy.length > 0) {
      summary += `\n🏆 TOP PRODUCTOS HOY:`
      g.rankings.topProductosHoy.slice(0, 3).forEach((p, i) => {
        summary += `\n   ${i + 1}. ${p.nombre}: ${formatMoney(p.ingresos)}`
      })
    }
    
    if (g.ultimaActualizacion) {
      const hace = Math.round((Date.now() - new Date(g.ultimaActualizacion).getTime()) / 60000)
      summary += `\n\n⏱️ Datos actualizados hace ${hace} minutos`
    }
    
    return summary
  }
  
  // Seleccionar un elemento
  const setSelectedElement = (type, data, actions = []) => {
    selectedElement.value = { type, data }
    availableActions.value = actions
    console.log(`🎯 [UIContext] Elemento seleccionado: ${type}`, data?.name || data?.invoice_number || data?.id)
  }
  
  // Limpiar selección
  const clearSelection = () => {
    selectedElement.value = null
    availableActions.value = []
  }
  
  // Registrar modal abierto
  const setActiveModal = (modalName, data = null) => {
    activeModal.value = { name: modalName, data }
    console.log(`🎯 [UIContext] Modal abierto: ${modalName}`)
  }
  
  // Cerrar modal
  const clearModal = () => {
    activeModal.value = null
  }
  
  // Registrar callback de acción (los componentes registran sus funciones)
  const registerAction = (actionId, callback) => {
    actionCallbacks.value[actionId] = callback
    // Log reducido - comentado para evitar spam en consola
    // console.log(`🎯 [UIContext] Acción registrada: ${actionId}`)
  }
  
  // Ejecutar una acción (ahora acepta parámetros)
  const executeAction = async (actionId, params = {}) => {
    console.log(`🎯 [UIContext] Ejecutando: ${actionId}`, params)
    // Log de acciones registradas removido - era muy verboso
    // console.log(`🎯 [UIContext] Acciones registradas:`, Object.keys(actionCallbacks.value))
    
    const callback = actionCallbacks.value[actionId]
    if (callback) {
      try {
        // Pasar parámetros al callback
        const result = await callback(params)
        // Si el callback devuelve algo, usarlo; si no, crear respuesta genérica
        if (result && typeof result === 'object') {
          return result
        }
        return { success: true, message: `Acción "${actionId}" ejecutada` }
      } catch (err) {
        console.error(`Error ejecutando acción ${actionId}:`, err)
        return { success: false, message: `Error: ${err.message}` }
      }
    } else {
      return { success: false, message: `Acción "${actionId}" no disponible` }
    }
  }
  
  // Obtener contexto para la IA (incluye datos estructurados)
  const getContextForAI = () => {
    return {
      module: currentModule.value,
      selectedElement: selectedElement.value,
      activeModal: activeModal.value?.name || null,
      availableActions: availableActions.value.map(a => a.id),
      screenData: screenData.value, // 🧠 Incluir datos de pantalla
      globalBusinessData: globalBusinessData.value, // 🌐 Siempre incluir datos globales
      summary: contextSummary.value + getGlobalBusinessSummary() // 🌐 Añadir resumen global
    }
  }
  
  return {
    // Estado
    currentModule,
    selectedElement,
    activeModal,
    availableActions,
    screenData, // 🧠 Exponer datos de pantalla
    globalBusinessData, // 🌐 Exponer datos globales
    
    // Computed
    contextSummary,
    
    // Acciones
    setCurrentModule,
    setSelectedElement,
    clearSelection,
    setActiveModal,
    clearModal,
    registerAction,
    executeAction,
    getContextForAI,
    setScreenData,     // 🧠 Nueva función
    updateScreenData,  // 🧠 Nueva función
    
    // 🌐 Datos Globales del Negocio
    setGlobalBusinessData,
    updateGlobalBusinessSection,
    getGlobalBusinessSummary
  }
})
