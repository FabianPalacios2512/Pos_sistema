/**
 * Plantilla Profesional de Nota de Devolución para PDF
 * Diseño moderno tipo ticket térmico (80mm)
 * Genera PDF vectorial con jsPDF
 */
import jsPDF from 'jspdf'
import QRCode from 'qrcode'

/**
 * Crear PDF de devolución con diseño profesional
 * @param {Object} returnData - Datos de la devolución
 * @param {Object} systemSettings - Configuración del sistema
 * @returns {Promise<jsPDF>} Objeto PDF listo para descargar o enviar
 */
export const createReturnTemplate = async (returnData, systemSettings = {}) => {
  try {
    // ==================== EXTRAER DATOS ====================
    const {
      number = '',
      invoice_number = '',
      date = new Date(),
      created_at = date,
      customer = {},
      customer_name = '',
      cashier = '',
      user = null,
      items = [],
      return_items = [],
      subtotal = 0,
      tax = 0,
      tax_amount = tax,
      total = 0,
      refund_method = 'Efectivo',
      reason = '',
      notes = ''
    } = returnData

    // Número de devolución
    const returnNumber = number || 'SIN-NUMERO'
    // Buscar factura en múltiples lugares (puede venir como 'number' o 'invoice_number')
    const invoiceRef = invoice_number || 
      returnData.original_invoice?.number || 
      returnData.original_invoice?.invoice_number || 
      returnData.invoice?.number ||
      returnData.invoice_number ||
      'N/A'
    
    // Mapeo de métodos de reembolso a español
    const refundMethodLabels = {
      'cash': 'Efectivo',
      'CASH': 'Efectivo',
      'efectivo': 'Efectivo',
      'card': 'Tarjeta',
      'CARD': 'Tarjeta',
      'tarjeta': 'Tarjeta',
      'transfer': 'Transferencia',
      'TRANSFER': 'Transferencia',
      'transferencia': 'Transferencia',
      'store_credit': 'Crédito Tienda',
      'STORE_CREDIT': 'Crédito Tienda',
      'credit': 'Crédito',
      'CREDIT': 'Crédito'
    }
    
    // Extraer nombre del cliente (puede venir como objeto o string)
    let customerName = 'Consumidor Final'
    if (customer_name && typeof customer_name === 'string') {
      customerName = customer_name
    } else if (customer && typeof customer === 'object' && customer.name) {
      customerName = customer.name
    } else if (customer && typeof customer === 'string') {
      customerName = customer
    } else if (returnData.original_invoice?.customer_name) {
      customerName = returnData.original_invoice.customer_name
    }
    
    // Extraer cajero/vendedor
    let cashierName = ''
    if (cashier && typeof cashier === 'string' && cashier.trim()) {
      cashierName = cashier
    } else if (user && typeof user === 'object' && user.name) {
      cashierName = user.name
    } else if (returnData.user?.name) {
      cashierName = returnData.user.name
    }
    
    // Asegurar que items sea un array válido
    let itemsList = []
    if (Array.isArray(items) && items.length > 0) {
      itemsList = items
    } else if (Array.isArray(return_items) && return_items.length > 0) {
      itemsList = return_items
    } else if (returnData.return_items && Array.isArray(returnData.return_items)) {
      itemsList = returnData.return_items
    } else if (returnData.items && Array.isArray(returnData.items)) {
      itemsList = returnData.items
    }
    
    if (!Array.isArray(itemsList)) {
      itemsList = []
    }

    // ==================== CONFIGURACIÓN EMPRESA ====================
    // company_name puede estar en múltiples lugares
    const companyName = systemSettings.company_name || 
                        systemSettings.business_name || 
                        systemSettings.store_name ||
                        'MI EMPRESA'  // Este es el último fallback, raramente se usará
    const companyAddress = systemSettings.company_address || systemSettings.address || ''
    const companyPhone = systemSettings.company_phone || systemSettings.phone || ''
    const companyEmail = systemSettings.company_email || systemSettings.email || ''
    const companyDocument = systemSettings.company_document || systemSettings.nit || systemSettings.tax_id || ''
    const companyLogo = systemSettings.company_logo || systemSettings.logo || null
    const taxLabel = systemSettings.iva_display_name || 'IVA'

    // ==================== GENERAR QR ====================
    const qrDataURL = await QRCode.toDataURL(`DEV:${returnNumber}`, {
      width: 100,
      margin: 0,
      color: { dark: '#374151', light: '#FFFFFF' }  // Gris oscuro para QR
    })

    // ==================== CALCULAR ALTURA DINÁMICA ====================
    // Cálculo preciso basado en cada sección del PDF - Optimizado para evitar desperdicio
    const headerHeight = companyLogo ? 45 : 30  // Logo o solo nombre + info (reducido)
    const bannerHeight = 16  // Banner + número (compacto)
    const infoHeight = 24 + (cashierName ? 4 : 0) + ((reason && reason.trim()) ? 8 : 0) // Info compacta
    const tableHeaderHeight = 8  // Encabezado de tabla (reducido)
    const itemHeight = 6  // Por cada producto (más compacto)
    const itemsHeight = Math.max(itemsList.length * itemHeight, 10)
    const totalsHeight = 24  // Subtotal, IVA, Total banner (ajustado)
    const refundMethodHeight = 12  // Método de reembolso box (ajustado)
    const notesHeight = (notes && typeof notes === 'string' && notes.trim()) ? 10 : 0
    const footerHeight = 40  // Mensaje + QR + número + línea + Powered by (compacto)
    const marginBottom = 2  // Margen mínimo al final - PDF termina justo después de Powered by
    
    const dynamicHeight = headerHeight + bannerHeight + infoHeight + tableHeaderHeight + 
                         itemsHeight + totalsHeight + refundMethodHeight + notesHeight + 
                         footerHeight + marginBottom

    // ==================== CREAR PDF ====================
    const pdf = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: [80, dynamicHeight]
    })

    // Configuración base
    let yPos = 6
    const pageWidth = 80
    const leftMargin = 4
    const rightMargin = pageWidth - 4
    const centerX = pageWidth / 2
    const contentWidth = rightMargin - leftMargin

    // ==================== COLORES ====================
    // Diseño profesional con tonos sobrios - menos rojo, más elegante
    const colors = {
      primary: [55, 65, 81],       // Gris oscuro profesional (reemplaza rojo)
      accent: [99, 102, 241],      // Indigo para acentos
      dark: [17, 24, 39],          // Gris muy oscuro para títulos
      medium: [75, 85, 99],        // Gris medio para labels
      light: [156, 163, 175],      // Gris claro
      success: [16, 185, 129],     // Verde para confirmaciones
      warning: [234, 88, 12],      // Naranja para alertas
      background: [249, 250, 251], // Fondo
      returnBadge: [220, 38, 38]   // Rojo solo para badge "DEVOLUCIÓN"
    }

    // ==================== HEADER EMPRESA ====================
    
    // Logo (si existe)
    if (companyLogo) {
      try {
        pdf.addImage(companyLogo, 'PNG', centerX - 10, yPos, 20, 12, '', 'FAST')
        yPos += 15
      } catch (err) {
        console.log('No se pudo cargar el logo')
      }
    }

    // Nombre de empresa
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(14)
    pdf.setTextColor(...colors.dark)
    pdf.text(companyName.toUpperCase(), centerX, yPos, { align: 'center' })
    yPos += 5

    // Info empresa (pequeño)
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)
    pdf.setTextColor(...colors.medium)

    if (companyDocument) {
      pdf.text(`NIT: ${companyDocument}`, centerX, yPos, { align: 'center' })
      yPos += 3
    }

    if (companyAddress) {
      const addressLines = pdf.splitTextToSize(companyAddress, 70)
      pdf.text(addressLines, centerX, yPos, { align: 'center' })
      yPos += addressLines.length * 3
    }

    if (companyPhone || companyEmail) {
      const contact = [companyPhone, companyEmail].filter(Boolean).join(' • ')
      pdf.text(contact, centerX, yPos, { align: 'center', maxWidth: 70 })
      yPos += 4
    }

    // ==================== BANNER DEVOLUCIÓN ====================
    yPos += 2
    
    // Banner elegante gris oscuro con borde sutil
    pdf.setFillColor(...colors.primary)
    pdf.roundedRect(leftMargin, yPos, contentWidth, 10, 2, 2, 'F')
    
    // Texto del banner
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(11)
    pdf.setTextColor(255, 255, 255)
    pdf.text('NOTA DE DEVOLUCIÓN', centerX, yPos + 6.5, { align: 'center' })
    yPos += 14

    // ==================== INFO DEVOLUCIÓN ====================
    
    // Número de devolución (destacado)
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(10)
    pdf.setTextColor(...colors.dark)
    pdf.text(`N° ${returnNumber}`, centerX, yPos, { align: 'center' })
    yPos += 5

    // Línea divisora decorativa
    pdf.setDrawColor(...colors.light)
    pdf.setLineWidth(0.3)
    pdf.line(leftMargin + 15, yPos, rightMargin - 15, yPos)
    yPos += 4

    // Info en dos columnas
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)
    
    // Fecha
    const returnDate = new Date(created_at || date)
    const dateStr = returnDate.toLocaleDateString('es-CO', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    })
    const timeStr = returnDate.toLocaleTimeString('es-CO', {
      hour: '2-digit',
      minute: '2-digit'
    })
    
    pdf.setTextColor(...colors.medium)
    pdf.text('Fecha:', leftMargin, yPos)
    pdf.setTextColor(...colors.dark)
    pdf.text(`${dateStr} ${timeStr}`, leftMargin + 12, yPos)
    yPos += 4

    // Factura referencia
    pdf.setTextColor(...colors.medium)
    pdf.text('Factura Ref:', leftMargin, yPos)
    pdf.setTextColor(...colors.dark)
    pdf.setFont('helvetica', 'bold')
    pdf.text(invoiceRef, leftMargin + 18, yPos)
    pdf.setFont('helvetica', 'normal')
    yPos += 4

    // Cliente
    pdf.setTextColor(...colors.medium)
    pdf.text('Cliente:', leftMargin, yPos)
    pdf.setTextColor(...colors.dark)
    pdf.text(customerName, leftMargin + 14, yPos)
    yPos += 4

    // Atendió (si existe)
    if (cashierName) {
      pdf.setTextColor(...colors.medium)
      pdf.text('Atendió:', leftMargin, yPos)
      pdf.setTextColor(...colors.dark)
      pdf.text(cashierName, leftMargin + 14, yPos)
      yPos += 4
    }

    // Motivo (si existe)
    if (reason && typeof reason === 'string' && reason.trim()) {
      pdf.setTextColor(...colors.medium)
      pdf.text('Motivo:', leftMargin, yPos)
      pdf.setTextColor(...colors.dark)
      const reasonLines = pdf.splitTextToSize(reason, 55)
      pdf.text(reasonLines, leftMargin + 14, yPos)
      yPos += reasonLines.length * 3 + 2
    }

    yPos += 2

    // ==================== TABLA DE PRODUCTOS ====================
    
    // Header de tabla con fondo
    pdf.setFillColor(...colors.background)
    pdf.rect(leftMargin, yPos - 1, contentWidth, 6, 'F')
    
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(6)
    pdf.setTextColor(...colors.medium)
    pdf.text('DESCRIPCIÓN', leftMargin + 1, yPos + 3)
    pdf.text('CANT', 50, yPos + 3, { align: 'center' })
    pdf.text('PRECIO', 62, yPos + 3, { align: 'right' })
    pdf.text('TOTAL', rightMargin - 1, yPos + 3, { align: 'right' })
    yPos += 7

    // Línea bajo header
    pdf.setDrawColor(...colors.light)
    pdf.setLineWidth(0.2)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 3

    // Items
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)
    pdf.setTextColor(...colors.dark)

    if (itemsList.length > 0) {
      itemsList.forEach((item, index) => {
        // Extraer datos del item de forma segura
        let productName = 'Producto'
        if (item.product_name && typeof item.product_name === 'string') {
          productName = item.product_name
        } else if (item.name && typeof item.name === 'string') {
          productName = item.name
        } else if (item.product && typeof item.product === 'object' && item.product.name) {
          productName = item.product.name
        }
        
        const quantity = parseInt(item.quantity || item.qty || 1)
        const unitPrice = parseFloat(item.unit_price || item.price || 0)
        const totalPrice = quantity * unitPrice

        // Nombre (truncar si es necesario)
        const truncatedName = productName.length > 28 ? productName.substring(0, 26) + '...' : productName
        pdf.text(truncatedName, leftMargin + 1, yPos)
        
        // Cantidad
        pdf.text(quantity.toString(), 50, yPos, { align: 'center' })
        
        // Precio unitario
        pdf.text(`$${formatNumber(unitPrice)}`, 62, yPos, { align: 'right' })
        
        // Total
        pdf.setFont('helvetica', 'bold')
        pdf.text(`$${formatNumber(totalPrice)}`, rightMargin - 1, yPos, { align: 'right' })
        pdf.setFont('helvetica', 'normal')

        yPos += 5

        // Línea punteada entre items (excepto el último)
        if (index < itemsList.length - 1) {
          pdf.setDrawColor(220, 220, 220)
          pdf.setLineDashPattern([1, 1], 0)
          pdf.line(leftMargin, yPos - 1, rightMargin, yPos - 1)
          pdf.setLineDashPattern([], 0)
        }
      })
    } else {
      pdf.setTextColor(...colors.medium)
      pdf.text('Sin productos registrados', centerX, yPos, { align: 'center' })
      yPos += 5
    }

    yPos += 3

    // ==================== TOTALES ====================
    
    // Línea doble
    pdf.setDrawColor(...colors.dark)
    pdf.setLineWidth(0.4)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 1
    pdf.setLineWidth(0.2)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 4

    // Subtotal
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(8)
    pdf.setTextColor(...colors.medium)
    pdf.text('Subtotal:', leftMargin, yPos)
    pdf.setTextColor(...colors.dark)
    pdf.text(`$${formatNumber(subtotal || 0)}`, rightMargin, yPos, { align: 'right' })
    yPos += 4

    // IVA
    const taxAmount = parseFloat(tax_amount || tax || 0)
    pdf.setTextColor(...colors.medium)
    pdf.text(`${taxLabel}:`, leftMargin, yPos)
    pdf.setTextColor(...colors.dark)
    pdf.text(`$${formatNumber(taxAmount)}`, rightMargin, yPos, { align: 'right' })
    yPos += 5

    // Total a reembolsar (destacado) - Diseño profesional gris oscuro
    pdf.setFillColor(...colors.dark)
    pdf.roundedRect(leftMargin, yPos - 1, contentWidth, 10, 1.5, 1.5, 'F')
    
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(9)
    pdf.setTextColor(255, 255, 255)
    pdf.text('TOTAL A REEMBOLSAR:', leftMargin + 2, yPos + 5)
    pdf.setFontSize(11)
    pdf.text(`$${formatNumber(total || 0)}`, rightMargin - 2, yPos + 5.5, { align: 'right' })
    yPos += 14

    // ==================== MÉTODO DE REEMBOLSO ====================
    
    // Ícono y método - traducido a español
    const rawMethod = (typeof refund_method === 'string' ? refund_method : 'cash') || 'cash'
    const refundMethodText = refundMethodLabels[rawMethod] || refundMethodLabels[rawMethod.toLowerCase()] || rawMethod || 'Efectivo'
    
    pdf.setFillColor(243, 244, 246)
    pdf.roundedRect(leftMargin, yPos - 1, contentWidth, 8, 1, 1, 'F')
    
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)
    pdf.setTextColor(...colors.medium)
    pdf.text('Método de reembolso:', leftMargin + 2, yPos + 4)
    
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(8)
    pdf.setTextColor(...colors.success)
    pdf.text(refundMethodText.toUpperCase(), rightMargin - 2, yPos + 4, { align: 'right' })
    yPos += 10

    // ==================== NOTAS (si existen) ====================
    if (notes && typeof notes === 'string' && notes.trim()) {
      pdf.setFont('helvetica', 'italic')
      pdf.setFontSize(6)
      pdf.setTextColor(...colors.light)
      const notesLines = pdf.splitTextToSize(`Nota: ${notes}`, contentWidth - 4)
      pdf.text(notesLines, centerX, yPos, { align: 'center' })
      yPos += notesLines.length * 3 + 2
    }

    // ==================== QR Y PIE DE PÁGINA ====================
    
    // Mensaje compacto
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(6)
    pdf.setTextColor(...colors.medium)
    pdf.text('Conserve este documento como', centerX, yPos, { align: 'center' })
    yPos += 2.5
    pdf.text('comprobante de su devolución', centerX, yPos, { align: 'center' })
    yPos += 4

    // QR Code centrado - tamaño optimizado
    const qrSize = 20
    pdf.addImage(qrDataURL, 'PNG', centerX - qrSize/2, yPos, qrSize, qrSize, '', 'FAST')
    yPos += qrSize + 2

    // Número bajo QR
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(6)
    pdf.setTextColor(...colors.dark)
    pdf.text(returnNumber, centerX, yPos, { align: 'center' })
    yPos += 4

    // Línea final sutil
    pdf.setDrawColor(...colors.light)
    pdf.setLineWidth(0.1)
    pdf.line(leftMargin + 15, yPos, rightMargin - 15, yPos)
    yPos += 3

    // Footer - Powered by (compacto)
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(5)
    pdf.setTextColor(...colors.light)
    pdf.text('Powered by 105POS', centerX, yPos, { align: 'center' })
    // PDF termina exactamente aquí - altura dinámica calculada para ajustar perfectamente

    return pdf

  } catch (error) {
    console.error('Error generando PDF de devolución:', error)
    throw error
  }
}

/**
 * Formatear número con separador de miles
 */
function formatNumber(value) {
  const num = parseFloat(value) || 0
  return num.toLocaleString('es-CO', { 
    minimumFractionDigits: 0, 
    maximumFractionDigits: 0 
  })
}
