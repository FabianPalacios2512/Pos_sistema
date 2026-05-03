/**
 * Plantilla Profesional de Nota de Devolución para PDF
 * Diseño térmico (80mm) clásico, alineado con factura POS
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
    // Buscar factura
    const invoiceRef = invoice_number || 
      returnData.original_invoice?.number || 
      returnData.original_invoice?.invoice_number || 
      returnData.invoice?.number ||
      returnData.invoice_number ||
      'N/A'
    
    // Mapeo de métodos de reembolso
    const refundMethodLabels = {
      'cash': 'Efectivo', 'CASH': 'Efectivo', 'efectivo': 'Efectivo',
      'card': 'Tarjeta', 'CARD': 'Tarjeta', 'tarjeta': 'Tarjeta',
      'transfer': 'Transferencia', 'TRANSFER': 'Transferencia', 'transferencia': 'Transferencia',
      'store_credit': 'Crédito Tienda', 'STORE_CREDIT': 'Crédito Tienda',
      'credit': 'Crédito', 'CREDIT': 'Crédito'
    }
    
    // Cliente
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
    
    // Vendedor
    let cashierName = ''
    if (cashier && typeof cashier === 'string' && cashier.trim()) {
      cashierName = cashier
    } else if (user && typeof user === 'object' && user.name) {
      cashierName = user.name
    } else if (returnData.user?.name) {
      cashierName = returnData.user.name
    }
    
    // Items
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
    
    if (!Array.isArray(itemsList)) itemsList = []

    // ==================== CONFIGURACIÓN EMPRESA ====================
    const companyName = systemSettings.company_name || systemSettings.business_name || systemSettings.store_name || 'MI EMPRESA'
    const companyAddress = systemSettings.company_address || systemSettings.address || ''
    const companyPhone = systemSettings.company_phone || systemSettings.phone || ''
    const companyEmail = systemSettings.company_email || systemSettings.email || ''
    const companyDocument = systemSettings.company_document || systemSettings.nit || systemSettings.tax_id || ''
    const companyLogo = systemSettings.company_logo || systemSettings.logo || null
    const taxLabel = systemSettings.iva_display_name || 'IVA'

    // ==================== GENERAR QR ====================
    const qrDataURL = await QRCode.toDataURL(`DEV:${returnNumber}`, {
      width: 100,
      margin: 1,
      color: { dark: '#000000', light: '#FFFFFF' }
    })

    // ==================== CALCULAR ALTURA DINÁMICA ====================
    const headerHeight = companyLogo ? 45 : 30
    const bannerHeight = 15
    const infoHeight = 24 + (cashierName ? 4 : 0) + ((reason && reason.trim()) ? 8 : 0)
    const tableHeaderHeight = 8
    const itemHeight = 6
    const itemsHeight = Math.max(itemsList.length * itemHeight, 10)
    const totalsHeight = 24
    const refundMethodHeight = 12
    const notesHeight = (notes && typeof notes === 'string' && notes.trim()) ? 10 : 0
    const footerHeight = 40
    const marginBottom = 5
    
    const dynamicHeight = headerHeight + bannerHeight + infoHeight + tableHeaderHeight + 
                         itemsHeight + totalsHeight + refundMethodHeight + notesHeight + 
                         footerHeight + marginBottom

    // ==================== CREAR PDF ====================
    const pdf = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: [80, dynamicHeight]
    })

    let yPos = 8
    const pageWidth = 80
    const leftMargin = 4
    const rightMargin = pageWidth - 4
    const centerX = pageWidth / 2
    const contentWidth = rightMargin - leftMargin

    // ==================== HEADER EMPRESA ====================
    if (companyLogo) {
      try {
        await new Promise((resolve, reject) => {
          const img = new Image()
          img.onload = () => {
            try {
              const imgAspectRatio = img.width / img.height
              let logoWidth = 14
              let logoHeight = logoWidth / imgAspectRatio
              if (logoHeight > 10) {
                logoHeight = 10
                logoWidth = logoHeight * imgAspectRatio
              }
              pdf.addImage(companyLogo, 'PNG', centerX - (logoWidth / 2), yPos, logoWidth, logoHeight, '', 'FAST')
              yPos += logoHeight + 3
              resolve()
            } catch (err) { reject(err) }
          }
          img.onerror = reject
          img.src = companyLogo
        })
      } catch (err) {}
    }

    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(14)
    pdf.setTextColor(0, 0, 0)
    pdf.text(companyName.toUpperCase(), centerX, yPos, { align: 'center' })
    yPos += 5

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(8)
    pdf.setTextColor(85, 85, 85)

    if (companyDocument) {
      pdf.text(`NIT: ${companyDocument}`, centerX, yPos, { align: 'center' })
      yPos += 3
    }
    if (companyAddress) {
      pdf.text(companyAddress, centerX, yPos, { align: 'center', maxWidth: 72 })
      yPos += 3
    }
    if (companyPhone || companyEmail) {
      const contact = [companyPhone, companyEmail].filter(Boolean).join(' • ')
      pdf.text(contact, centerX, yPos, { align: 'center' })
      yPos += 4
    } else {
      yPos += 2
    }

    // Doble línea
    pdf.setLineWidth(0.3)
    pdf.setDrawColor(0, 0, 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 1
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 6

    // ==================== TÍTULO DEVOLUCIÓN ====================
    pdf.setTextColor(0, 0, 0)
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(10)
    pdf.text('NOTA DE DEVOLUCIÓN', centerX, yPos, { align: 'center' })
    yPos += 5
    
    pdf.setFont('courier', 'bold')
    pdf.setFontSize(12)
    pdf.text(`No. ${returnNumber}`, centerX, yPos, { align: 'center' })
    yPos += 8

    // ==================== INFO DEVOLUCIÓN ====================
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)
    
    const returnDate = new Date(created_at || date)
    const dateStr = returnDate.toLocaleDateString('es-CO', { day: '2-digit', month: 'long', year: 'numeric' })
    const timeStr = returnDate.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: true })
    
    pdf.text(`Fecha: ${dateStr}`, leftMargin, yPos)
    yPos += 3
    pdf.text(`Hora: ${timeStr}`, leftMargin, yPos)
    yPos += 4

    pdf.setFont('helvetica', 'bold')
    pdf.text('Factura Ref:', leftMargin, yPos)
    pdf.setFont('courier', 'bold')
    pdf.text(invoiceRef, leftMargin + 18, yPos)
    pdf.setFont('helvetica', 'normal')
    yPos += 4

    pdf.text(`Cliente: ${customerName}`, leftMargin, yPos, { maxWidth: 72 })
    yPos += 4

    if (cashierName) {
      pdf.text(`Atendido por: ${cashierName}`, leftMargin, yPos)
      yPos += 4
    }

    if (reason && typeof reason === 'string' && reason.trim()) {
      pdf.text('Motivo:', leftMargin, yPos)
      const reasonLines = pdf.splitTextToSize(reason, 55)
      pdf.text(reasonLines, leftMargin + 10, yPos)
      yPos += reasonLines.length * 3 + 2
    }

    // Línea separadora
    yPos += 2
    pdf.setLineWidth(0.3)
    pdf.setDrawColor(0, 0, 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 4

    // ==================== TABLA DE PRODUCTOS ====================
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(7)
    pdf.text('DESCRIPCIÓN', leftMargin + 1, yPos)
    pdf.text('CANT.', leftMargin + 38, yPos, { align: 'center' })
    pdf.text('PRECIO', leftMargin + 53, yPos, { align: 'right' })
    pdf.text('TOTAL', rightMargin - 1, yPos, { align: 'right' })
    
    yPos += 3
    pdf.setLineWidth(0.15)
    pdf.setDrawColor(204, 204, 204)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 3

    if (itemsList.length > 0) {
      itemsList.forEach((item, index) => {
        let productName = 'Producto'
        if (item.product_name && typeof item.product_name === 'string') productName = item.product_name
        else if (item.name && typeof item.name === 'string') productName = item.name
        else if (item.product && typeof item.product === 'object' && item.product.name) productName = item.product.name
        
        const quantity = parseInt(item.quantity || item.qty || 1)
        const unitPrice = parseFloat(item.unit_price || item.price || 0)
        const totalPrice = quantity * unitPrice

        const nameLines = pdf.splitTextToSize(productName, 32)
        
        pdf.setFont('helvetica', 'normal')
        pdf.setFontSize(7)
        pdf.text(nameLines[0], leftMargin + 1, yPos)
        
        pdf.setFont('courier', 'normal')
        pdf.text(quantity.toString(), leftMargin + 38, yPos, { align: 'center' })
        
        pdf.setTextColor(102, 102, 102)
        pdf.setFontSize(6)
        pdf.text(`$${formatNumber(unitPrice)}`, leftMargin + 53, yPos, { align: 'right' })
        
        pdf.setFont('courier', 'bold')
        pdf.setFontSize(7)
        pdf.setTextColor(0, 0, 0)
        pdf.text(`$${formatNumber(totalPrice)}`, rightMargin - 1, yPos, { align: 'right' })
        
        yPos += 4

        if (index < itemsList.length - 1) {
          pdf.setLineWidth(0.1)
          pdf.setDrawColor(238, 238, 238)
          pdf.line(leftMargin, yPos, rightMargin, yPos)
          yPos += 2
        }
      })
    } else {
      pdf.setFont('helvetica', 'normal')
      pdf.setTextColor(102, 102, 102)
      pdf.text('Sin productos registrados', centerX, yPos, { align: 'center' })
      yPos += 4
      pdf.setTextColor(0, 0, 0)
    }

    // ==================== TOTALES ====================
    yPos += 2
    pdf.setLineWidth(0.3)
    pdf.setDrawColor(0, 0, 0)
    pdf.setLineDashPattern([2, 2], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 5

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(9)

    pdf.text('Subtotal:', rightMargin - 25, yPos, { align: 'right' })
    pdf.text(`$${formatNumber(subtotal || 0)}`, rightMargin - 1, yPos, { align: 'right' })
    yPos += 4

    const taxAmount = parseFloat(tax_amount || tax || 0)
    if (taxAmount > 0) {
      pdf.text(`${taxLabel}:`, rightMargin - 25, yPos, { align: 'right' })
      pdf.text(`$${formatNumber(taxAmount)}`, rightMargin - 1, yPos, { align: 'right' })
      yPos += 4
    }

    // Línea doble antes del total
    yPos += 1
    pdf.setLineWidth(0.3)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 1
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 5

    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(10)
    pdf.text('TOTAL DEVOLUCIÓN', leftMargin + 1, yPos + 3)
    
    pdf.setFont('courier', 'bold')
    pdf.setFontSize(18)
    pdf.text(`$${formatNumber(total || 0)}`, rightMargin - 1, yPos + 3, { align: 'right' })
    yPos += 10

    // ==================== MÉTODO DE REEMBOLSO ====================
    yPos += 2
    pdf.setLineWidth(0.2)
    pdf.setDrawColor(0, 0, 0)
    pdf.setLineDashPattern([2, 2], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 5

    const rawMethod = (typeof refund_method === 'string' ? refund_method : 'cash') || 'cash'
    const refundMethodText = refundMethodLabels[rawMethod] || refundMethodLabels[rawMethod.toLowerCase()] || rawMethod || 'Efectivo'
    
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(7)
    pdf.text('FORMA DE REEMBOLSO', leftMargin, yPos)
    yPos += 4

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(8)
    pdf.text(`• ${refundMethodText}`, leftMargin, yPos)
    pdf.text(`$${formatNumber(total || 0)}`, rightMargin - 1, yPos, { align: 'right' })
    yPos += 5

    // ==================== NOTAS ====================
    if (notes && typeof notes === 'string' && notes.trim()) {
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(8)
      pdf.text('OBSERVACIONES:', leftMargin, yPos)
      yPos += 4
      
      pdf.setFont('helvetica', 'normal')
      pdf.setFontSize(7)
      const notesLines = pdf.splitTextToSize(notes, contentWidth - 4)
      notesLines.forEach(line => {
        pdf.text(line, leftMargin, yPos)
        yPos += 3
      })
      yPos += 2
    }

    // ==================== QR Y PIE DE PÁGINA ====================
    yPos += 4
    pdf.setLineWidth(0.2)
    pdf.setDrawColor(0, 0, 0)
    pdf.setLineDashPattern([2, 2], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 5

    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(8)
    pdf.text('COMPROBANTE DE DEVOLUCIÓN', centerX, yPos, { align: 'center' })
    yPos += 4

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)
    pdf.text('Conserve este documento', centerX, yPos, { align: 'center' })
    yPos += 6

    const qrSize = 22
    pdf.addImage(qrDataURL, 'PNG', centerX - qrSize/2, yPos, qrSize, qrSize, '', 'FAST')
    yPos += qrSize + 2

    pdf.setFontSize(6)
    pdf.text(returnNumber, centerX, yPos, { align: 'center' })
    yPos += 6

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(5)
    pdf.setTextColor(150, 150, 150)
    pdf.text('Powered by 105POS', centerX, yPos, { align: 'center' })

    return pdf
  } catch (error) {
    console.error('Error generando PDF de devolución:', error)
    throw error
  }
}

function formatNumber(value) {
  const num = parseFloat(value) || 0
  return num.toLocaleString('es-CO', { 
    minimumFractionDigits: 0, 
    maximumFractionDigits: 0 
  })
}
