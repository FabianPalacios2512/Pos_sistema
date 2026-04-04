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
  
  // 🔒 Último error de navegación (para notificar a la IA cuando no tiene permisos)
  const lastNavigationError = ref(null)
  // Estructura: { module: 'users', moduleName: 'Usuarios', roleName: 'Vendedor', message: '...' }
  
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
    },
    
    // 🔄 Devoluciones
    devoluciones: {
      devolucionesHoy: 0,
      devolucionesMes: 0,
      montoHoy: 0,
      montoMes: 0
    },
    
    // 🧾 Última Factura (para consultas rápidas de la IA)
    ultimaFactura: {
      numero: null,
      cliente: null,
      total: 0,
      fecha: null,
      productos: 0
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
      'reportes': 'Reportes',
      'reports-general': 'Reportes Generales',
      'reportes-generales': 'Reportes Generales',
      'reports-caja': 'Reportes de Caja',
      'reportes-caja': 'Reportes de Caja',
      'reports-inventario': 'Reportes de Inventario',
      'reportes-inventario': 'Reportes de Inventario',
      'settings': 'Configuración',
      'warehouses': 'Bodegas',
      'intelligent_inventory': 'Inventario Inteligente',
      'operational-expenses': 'Gastos Operativos',
      'expenses': 'Gastos Operativos',
      'gastos': 'Gastos Operativos',
      'cash-register': 'Control de Caja',
      'cash-admin': 'Control de Cajas',
      'users-management': 'Usuarios y Roles',
      'users': 'Usuarios y Roles'
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
      
      // ═══════════════════════════════════════════════════════════
      // 💰 DATOS DE CONTROL DE CAJAS (módulo cash-admin)
      // ═══════════════════════════════════════════════════════════
      if (screenData.value.tipoReporte === 'cash-admin') {
        summary += `\n\n💰 CONTROL DE CAJAS:`
        
        if (screenData.value.kpis) {
          const kpis = screenData.value.kpis
          summary += `\n\n📊 KPIs DE CAJAS:`
          summary += `\n• Sesiones activas: ${kpis.sesionesActivas || 0}`
          summary += `\n• Total en cajas: ${kpis.totalEnCajas || '$0'}`
          summary += `\n• Ventas hoy: ${kpis.ventasHoy || '$0'}`
          if (kpis.empleadosConCajaAbierta?.length > 0) {
            summary += `\n• Empleados con caja abierta: ${kpis.empleadosConCajaAbierta.join(', ')}`
          }
        }
        
        if (screenData.value.sesiones?.lista?.length > 0) {
          summary += `\n\n👥 SESIONES DE CAJA (${screenData.value.sesiones.totalRegistros}):`
          screenData.value.sesiones.lista.slice(0, 5).forEach((s, i) => {
            summary += `\n${i+1}. ${s.usuario}: ${s.ventas} - ${s.estado} (${s.duracion})`
          })
          if (screenData.value.sesiones.lista.length > 5) {
            summary += `\n... y ${screenData.value.sesiones.totalRegistros - 5} más`
          }
        }
        
        if (screenData.value.alertasEmpleados?.length > 0) {
          summary += `\n\n⚠️ ALERTAS DE EMPLEADOS (${screenData.value.alertasEmpleados.length}):`
          screenData.value.alertasEmpleados.forEach(a => {
            summary += `\n• [${a.tipo}] ${a.titulo}: ${a.descripcion}`
          })
        }
        
        // Info de modales abiertos
        if (screenData.value.modales) {
          if (screenData.value.modales.auditoriaAbierta) {
            summary += `\n\n📋 MODAL DE AUDITORÍA ABIERTO - Puedes consultar movimientos detallados`
          }
          if (screenData.value.modales.detalleAbierto) {
            summary += `\n\n📋 MODAL DE DETALLES ABIERTO`
          }
        }
        
        // Datos de auditoría si están disponibles
        if (screenData.value.auditoriaActual) {
          const audit = screenData.value.auditoriaActual
          summary += `\n\n🔍 AUDITORÍA DE SESIÓN (${audit.usuario}):`
          if (audit.estadisticas) {
            summary += `\n• Ventas: ${audit.estadisticas.totalTransacciones} (${audit.estadisticas.totalVentas})`
            summary += `\n• Venta promedio: ${audit.estadisticas.ventaPromedio}`
            summary += `\n• Venta mayor: ${audit.estadisticas.ventaMayor}`
            summary += `\n• Devoluciones: ${audit.estadisticas.devoluciones || 0} (${audit.estadisticas.montoDevuelto || '$0'})`
            summary += `\n• Gastos: ${audit.estadisticas.gastos || 0} (${audit.estadisticas.montoGastos || '$0'})`
            summary += `\n• Duración: ${audit.estadisticas.duracion || 'N/A'}`
          }
          if (audit.timeline?.length > 0) {
            summary += `\n• Timeline con ${audit.timeline.length} eventos registrados`
          }
        }
        
        summary += `\n\n💡 ACCIONES DISPONIBLES:`
        summary += `\n• verAuditoriaSesion: Ver todos los movimientos (ventas, devoluciones, gastos) de una sesión`
        summary += `\n• verDetalleSesion: Ver resumen de una sesión`
        summary += `\n• consultarRendimientoEmpleado: Ver estadísticas de un empleado`
      }
      
      // ═══════════════════════════════════════════════════════════
      // 🧾 DATOS DE FACTURAS (módulo invoices)
      // ═══════════════════════════════════════════════════════════
      if (screenData.value.tipoReporte === 'invoices') {
        summary += `\n\n🧾 MÓDULO DE FACTURAS:`
        
        if (screenData.value.resumenFacturas) {
          const resumen = screenData.value.resumenFacturas
          summary += `\n\n📊 RESUMEN DE FACTURAS:`
          summary += `\n• Total de facturas: ${resumen.total}`
          summary += `\n• Facturas del mes: ${resumen.facturasDelMes}`
          summary += `\n• Total facturado: ${resumen.totalFacturado}`
          summary += `\n• Pagadas: ${resumen.porEstado?.pagadas || 0}`
          summary += `\n• Pendientes: ${resumen.porEstado?.pendientes || 0}`
          summary += `\n• Anuladas: ${resumen.porEstado?.anuladas || 0}`
          summary += `\n• Devueltas: ${resumen.porEstado?.devueltas || 0}`
          summary += `\n• Cotizaciones activas: ${resumen.cotizaciones || 0}`
        }
        
        // Facturas de hoy
        if (screenData.value.facturasHoy) {
          const hoy = screenData.value.facturasHoy
          summary += `\n\n📅 FACTURAS DE HOY:`
          summary += `\n• Cantidad: ${hoy.cantidad}`
          summary += `\n• Total: ${hoy.total}`
          if (hoy.lista?.length > 0) {
            hoy.lista.forEach((f, i) => {
              summary += `\n  ${i+1}. ${f.numero}: ${f.cliente} - ${f.total}`
            })
          }
        }
        
        // Últimas facturas
        if (screenData.value.ultimasFacturas?.length > 0) {
          summary += `\n\n📋 ÚLTIMAS ${screenData.value.ultimasFacturas.length} FACTURAS:`
          screenData.value.ultimasFacturas.forEach((f, i) => {
            summary += `\n${i+1}. ${f.numero}: ${f.cliente} - ${f.total} (${f.fecha}) [${f.estado}]`
          })
        }
        
        // Primera y última del período
        if (screenData.value.primeraFacturaPeriodo) {
          const p = screenData.value.primeraFacturaPeriodo
          summary += `\n\n📌 PRIMERA FACTURA DEL PERÍODO:`
          summary += `\n• ${p.numero}: ${p.cliente} - ${p.total} (${p.fecha})`
        }
        if (screenData.value.ultimaFacturaPeriodo) {
          const u = screenData.value.ultimaFacturaPeriodo
          summary += `\n\n📌 ÚLTIMA FACTURA (MÁS RECIENTE):`
          summary += `\n• ${u.numero}: ${u.cliente} - ${u.total} (${u.fecha})`
        }
        
        summary += `\n\n💡 ACCIONES DISPONIBLES:`
        summary += `\n• seleccionarFactura: Seleccionar una factura por número o posición`
        summary += `\n• consultarFacturasHoy: Ver todas las facturas de hoy`
        summary += `\n• consultarFacturaEspecial: Ver primera/última factura del día/semana/mes`
        summary += `\n• buscarFacturasPorCliente: Buscar facturas de un cliente específico`
        summary += `\n• sendEmail, sendWhatsApp, downloadPDF, printInvoice: Acciones sobre factura seleccionada`
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
      
      // ═══════════════════════════════════════════════════════════
      // 🧠 INVENTARIO INTELIGENTE (módulo intelligent_inventory)
      // ═══════════════════════════════════════════════════════════
      if (currentModule.value === 'intelligent_inventory') {
        summary += `\n\n🧠 INVENTARIO INTELIGENTE - ANÁLISIS AVANZADO:`
        
        // Sección activa
        const seccionActiva = screenData.value.seccionActiva || 'overview'
        const nombresSeccion = {
          'overview': 'Vista General',
          'products': 'Productos',
          'movements': 'Movimientos',
          'customers': 'Clientes',
          'suppliers': 'Proveedores',
          'alerts': 'Alertas',
          'predictions': 'Predicciones'
        }
        summary += `\n• Sección actual: ${nombresSeccion[seccionActiva] || seccionActiva}`
        summary += `\n• Secciones disponibles: Vista General, Productos, Movimientos, Clientes, Proveedores, Alertas, Predicciones`
        
        // KPIs si están disponibles
        if (screenData.value.metrics) {
          const m = screenData.value.metrics
          summary += `\n\n📊 KPIs INVENTARIO INTELIGENTE:`
          summary += `\n• Productos activos: ${m.activeProducts || 0} de ${m.totalProducts || 0}`
          summary += `\n• Valor invertido (costo): $${(m.totalInventoryValue || 0).toLocaleString('es-CO')}`
          summary += `\n• Valor potencial (venta): $${(m.totalSaleValue || 0).toLocaleString('es-CO')}`
          summary += `\n• Ganancia estimada: $${((m.totalSaleValue || 0) - (m.totalInventoryValue || 0)).toLocaleString('es-CO')}`
          summary += `\n• Productos stock bajo: ${m.lowStockProducts || 0}`
          summary += `\n• Productos sin stock: ${m.outOfStockProducts || 0}`
        }
        
        // Datos de rotación si están disponibles
        if (screenData.value.rotacion || screenData.value.topProductosVendidos) {
          summary += `\n\n📈 DATOS DE ROTACIÓN Y VENTAS:`
          if (screenData.value.topProductosVendidos?.length > 0) {
            summary += `\n\n🏆 TOP PRODUCTOS MÁS VENDIDOS:`
            screenData.value.topProductosVendidos.slice(0, 5).forEach((p, i) => {
              summary += `\n${i+1}. ${p.nombre}: ${p.vendidos || p.cantidad || 0} unidades ($${(p.ingresos || p.total || 0).toLocaleString('es-CO')})`
            })
          }
        }
        
        // Productos con stock bajo
        if (screenData.value.productosStockBajo?.length > 0) {
          summary += `\n\n⚠️ PRODUCTOS CON STOCK BAJO (${screenData.value.productosStockBajo.length}):`
          screenData.value.productosStockBajo.slice(0, 5).forEach(p => {
            summary += `\n• ${p.nombre}: ${p.stockActual || p.stock || 0}/${p.stockMinimo || p.min_stock || 0} unidades`
          })
        }
        
        // Predicciones si están disponibles
        if (screenData.value.predicciones) {
          summary += `\n\n🔮 PREDICCIONES DE INVENTARIO:`
          if (screenData.value.predicciones.proximosAgotarse?.length > 0) {
            summary += `\n• Próximos a agotarse: ${screenData.value.predicciones.proximosAgotarse.map(p => p.nombre).join(', ')}`
          }
          if (screenData.value.predicciones.tendenciaVentas) {
            summary += `\n• Tendencia ventas: ${screenData.value.predicciones.tendenciaVentas}`
          }
        }
        
        // Instrucciones específicas
        if (screenData.value.instrucciones) {
          summary += `\n\n📋 INFORMACIÓN DE NAVEGACIÓN:`
          summary += `\n${screenData.value.instrucciones.secciones || ''}`
        }
        
        summary += `\n\n💡 ACCIONES DISPONIBLES EN INVENTARIO INTELIGENTE:`
        summary += `\n• cambiarSeccionInventarioInteligente: Cambiar entre Vista General, Productos, Movimientos, etc.`
        summary += `\n• buscarProductoInventarioInteligente: Buscar productos`
        summary += `\n• cambiarPeriodoInventarioInteligente: Cambiar período (hoy, semana, mes, año)`
        summary += `\n• verAlertasInventarioInteligente: Ver productos con stock bajo`
        summary += `\n• verPrediccionesInventarioInteligente: Ver predicciones ML`
      }
      
      // ═══════════════════════════════════════════════════════════
      // 📊 DATOS DE REPORTES
      // ═══════════════════════════════════════════════════════════
      

      // �📈 REPORTES DE CAJA
      if (screenData.value.tipoReporte === 'reports-caja') {
        summary += `\n\n📊 REPORTE DE CAJAS ACTIVO:`
        summary += `\n• Descripción: ${screenData.value.descripcion || 'Análisis de cajeros'}`
        summary += `\n• Período: ${screenData.value.periodoActual || 'No definido'}`
        
        if (screenData.value.kpis) {
          const kpis = screenData.value.kpis
          summary += `\n\n💰 KPIs DE CAJA:`
          summary += `\n• Sesiones activas: ${kpis.sesionesActivas || 0}`
          summary += `\n• Total ventas: ${kpis.totalVentas || '$0'}`
          summary += `\n• Transacciones: ${kpis.totalTransacciones || 0}`
          summary += `\n• Mejor cajero: ${kpis.mejorCajero || 'N/A'} (${kpis.mejorCajeroVentas || '$0'})`
          summary += `\n• Promedio por hora: ${kpis.promedioHora || '$0'}`
        }
        
        if (screenData.value.cajeros && screenData.value.cajeros.length > 0) {
          summary += `\n\n👥 COMPARATIVA DE CAJEROS (${screenData.value.cajeros.length}):`
          screenData.value.cajeros.forEach((c, i) => {
            summary += `\n${i+1}. ${c.nombre}: ${c.ventas} (${c.transacciones} trans.) - Ticket: ${c.ticketPromedio}`
          })
        }
        
        if (screenData.value.topSesiones && screenData.value.topSesiones.length > 0) {
          summary += `\n\n🏆 TOP SESIONES:`
          screenData.value.topSesiones.forEach((s, i) => {
            summary += `\n${i+1}. ${s.cajero}: ${s.ventas} (${s.fecha})`
          })
        }
        
        if (screenData.value.alertas && screenData.value.alertas.length > 0) {
          summary += `\n\n⚠️ ALERTAS DE CAJA (${screenData.value.alertas.length}):`
          screenData.value.alertas.forEach(a => {
            summary += `\n• [${a.tipo}] ${a.titulo}: ${a.mensaje}`
          })
        }
      }
      
      // 📦 REPORTES DE INVENTARIO
      if (screenData.value.tipoReporte === 'reports-inventario') {
        summary += `\n\n📦 REPORTE DE INVENTARIO ACTIVO:`
        summary += `\n• Descripción: ${screenData.value.descripcion || 'Análisis de inventario'}`
        
        if (screenData.value.kpis) {
          const kpis = screenData.value.kpis
          summary += `\n\n📊 KPIs DE INVENTARIO:`
          summary += `\n• Total productos: ${kpis.totalProductos || 0} (${kpis.productosActivos || 0} activos)`
          summary += `\n• Valor inventario (costo): ${kpis.valorInventario || '$0'}`
          summary += `\n• Valor venta potencial: ${kpis.valorVentaPotencial || '$0'}`
          summary += `\n• Margen potencial: ${kpis.margenPotencial || '$0'} (${kpis.porcentajeMargen || '0%'})`
          summary += `\n• Productos stock bajo: ${kpis.productosBajoStock || 0}`
          summary += `\n• Productos sin stock: ${kpis.productosSinStock || 0}`
        }
        
        if (screenData.value.analisisABC) {
          const abc = screenData.value.analisisABC
          summary += `\n\n📈 ANÁLISIS ABC:`
          summary += `\n• Clase A (alta rotación): ${abc.claseA || 0} productos`
          summary += `\n• Clase B (media rotación): ${abc.claseB || 0} productos`
          summary += `\n• Clase C (baja rotación): ${abc.claseC || 0} productos`
        }
        
        if (screenData.value.categorias && screenData.value.categorias.length > 0) {
          summary += `\n\n📂 DISTRIBUCIÓN POR CATEGORÍA:`
          screenData.value.categorias.forEach(c => {
            summary += `\n• ${c.nombre}: ${c.valor}`
          })
        }
        
        if (screenData.value.topVendidos && screenData.value.topVendidos.length > 0) {
          summary += `\n\n🏆 TOP PRODUCTOS MÁS VENDIDOS:`
          screenData.value.topVendidos.forEach((p, i) => {
            summary += `\n${i+1}. ${p.nombre}: ${p.vendidos} unidades (${p.ingresos})`
          })
        }
        
        if (screenData.value.stockBajo && screenData.value.stockBajo.length > 0) {
          summary += `\n\n⚠️ PRODUCTOS CON STOCK BAJO (${screenData.value.stockBajo.length}):`
          screenData.value.stockBajo.forEach(p => {
            summary += `\n• ${p.nombre}: ${p.stockActual}/${p.stockMinimo} unidades`
          })
        }
        
        if (screenData.value.sinMovimiento && screenData.value.sinMovimiento.length > 0) {
          summary += `\n\n📦 PRODUCTOS SIN MOVIMIENTO (Capital Inmovilizado):`
          screenData.value.sinMovimiento.forEach(p => {
            summary += `\n• ${p.nombre}: ${p.stock} unidades (${p.valorInmovilizado})`
          })
        }
      }
      
      // � REPORTES GENERALES
      if (screenData.value.tipoReporte === 'reports-general') {
        summary += `\n\n📊 REPORTE GENERAL ACTIVO:`
        summary += `\n• Descripción: ${screenData.value.descripcion || 'Dashboard ejecutivo'}`
        summary += `\n• Período: ${screenData.value.periodoActual || 'No definido'}`
        
        if (screenData.value.kpis) {
          const kpis = screenData.value.kpis
          summary += `\n\n💰 KPIs DE VENTAS:`
          summary += `\n• Ventas totales: ${kpis.ventasTotales || '$0'}`
          summary += `\n• Transacciones: ${kpis.transacciones || 0}`
          summary += `\n• Ticket promedio: ${kpis.ticketPromedio || '$0'}`
          summary += `\n• Margen bruto: ${kpis.margenBruto || '0%'}`
        }
        
        if (screenData.value.topProductos && screenData.value.topProductos.length > 0) {
          summary += `\n\n🏆 TOP PRODUCTOS MÁS VENDIDOS:`
          screenData.value.topProductos.forEach((p, i) => {
            summary += `\n${i+1}. ${p.nombre}: ${p.vendidos} vendidos (${p.ingresos})`
          })
        }
        
        if (screenData.value.ventasPorCategoria && screenData.value.ventasPorCategoria.length > 0) {
          summary += `\n\n📂 VENTAS POR CATEGORÍA:`
          screenData.value.ventasPorCategoria.forEach(c => {
            summary += `\n• ${c.nombre}: ${c.ventas}`
          })
        }
        
        if (screenData.value.productosStockBajo && screenData.value.productosStockBajo.length > 0) {
          summary += `\n\n⚠️ ALERTAS DE STOCK BAJO (${screenData.value.productosStockBajo.length}):`
          screenData.value.productosStockBajo.forEach(p => {
            summary += `\n• ${p.nombre}: ${p.stock} unidades`
          })
        }
      }
      
      // �📋 MENÚ DE REPORTES
      if (screenData.value.tipoReporte === 'reports-menu') {
        summary += `\n\n📊 MENÚ DE REPORTES:`
        summary += `\n• Reporte activo: ${screenData.value.reporteActivoNombre || 'Ninguno'}`
        summary += `\n• Reportes disponibles: ${(screenData.value.reportesDisponibles || []).join(', ')}`
        summary += `\n• Puedo cambiar entre: Reportes Generales, Reportes de Caja, Reportes de Inventario`
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
  
  // 🔒 Establecer error de navegación (para la IA)
  const setLastNavigationError = (error) => {
    lastNavigationError.value = error
  }
  
  // 🔒 Obtener y limpiar error de navegación (para la IA)
  const getAndClearNavigationError = () => {
    const error = lastNavigationError.value
    lastNavigationError.value = null
    return error
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
    lastNavigationError, // 🔒 Error de navegación por permisos
    
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
    setLastNavigationError, // 🔒 Error de navegación
    getAndClearNavigationError, // 🔒 Obtener y limpiar error
    
    // 🌐 Datos Globales del Negocio
    setGlobalBusinessData,
    updateGlobalBusinessSection,
    getGlobalBusinessSummary
  }
})
