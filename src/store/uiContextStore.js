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
    console.log(`🎯 [UIContext] Acción registrada: ${actionId}`)
  }
  
  // Ejecutar una acción
  const executeAction = async (actionId) => {
    console.log(`🎯 [UIContext] Intentando ejecutar: ${actionId}`)
    console.log(`🎯 [UIContext] Acciones registradas:`, Object.keys(actionCallbacks.value))
    
    const callback = actionCallbacks.value[actionId]
    if (callback) {
      try {
        await callback()
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
      summary: contextSummary.value
    }
  }
  
  return {
    // Estado
    currentModule,
    selectedElement,
    activeModal,
    availableActions,
    
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
    getContextForAI
  }
})
