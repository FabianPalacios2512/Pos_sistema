/**
 * Plantilla de Cotización/Quotation para PDF
 * Genera PDF vectorial con jsPDF (NO usa html2canvas)
 * Se usa para: Imprimir, Descargar, WhatsApp
 */
import jsPDF from 'jspdf'
import QRCode from 'qrcode'

/**
 * Crear PDF de cotización con diseño tipo ticket térmico (80mm)
 * @param {Object} quotationData - Datos de la cotización
 * @param {Object} systemSettings - Configuración del sistema (empresa, IVA, etc)
 * @returns {jsPDF} Objeto PDF listo para descargar o enviar
 */
export const createQuotationTemplate = async (quotationData, systemSettings = {}) => {
  try {
    // Extraer datos de la cotización
    const {
      quotation_number = '',
      quotationNumber = '',
      date = new Date(),
      created_at = date,
      customer = 'Cliente',
      customer_name = customer,
      cashier = 'Vendedor',
      items = [],
      subtotal = 0,
      discount = 0,
      tax = 0,
      tax_amount = tax,
      total = 0,
      notes = '',
      validity_days = 15
    } = quotationData

    // Número de cotización
    const quotationCode = quotation_number || quotationNumber || 'SIN-NUMERO'

    // Configuración de la empresa
    const companyName = systemSettings.company_name || 'MI EMPRESA'
    const companyAddress = systemSettings.company_address || ''
    const companyPhone = systemSettings.company_phone || ''
    const companyEmail = systemSettings.company_email || ''
    const companyDocument = systemSettings.company_document || ''
    const taxLabel = systemSettings.iva_display_name || 'IVA'
    const taxRate = systemSettings.iva_percentage || 19

    // Generar QR Code
    const qrDataURL = await QRCode.toDataURL(quotationCode, {
      width: 80,
      margin: 1,
      color: { dark: '#000000', light: '#FFFFFF' }
    })

    // Calcular altura dinámica según items
    const baseHeight = 120
    const itemHeight = 4
    const itemCount = items.length
    const qrSectionHeight = 60
    const dynamicHeight = Math.max(200, baseHeight + (itemCount * itemHeight) + qrSectionHeight)

    // Crear PDF con formato ticket (80mm ancho, altura dinámica)
    const pdf = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: [80, dynamicHeight]
    })

    // Configuración del ticket
    let yPos = 8
    const pageWidth = 80
    const leftMargin = 4
    const rightMargin = pageWidth - 4
    const centerX = pageWidth / 2

    // ==================== HEADER EMPRESA ====================
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(12)
    pdf.text(companyName.toUpperCase(), centerX, yPos, { align: 'center' })
    yPos += 5

    if (companyAddress) {
      pdf.setFontSize(8)
      pdf.setFont('helvetica', 'normal')
      pdf.text(companyAddress, centerX, yPos, { align: 'center', maxWidth: 72 })
      yPos += 4
    }

    if (companyPhone) {
      pdf.setFontSize(8)
      pdf.text(`Tel: ${companyPhone}`, centerX, yPos, { align: 'center' })
      yPos += 4
    }

    if (companyEmail) {
      pdf.setFontSize(8)
      pdf.text(companyEmail, centerX, yPos, { align: 'center' })
      yPos += 4
    }

    if (companyDocument) {
      pdf.setFontSize(8)
      pdf.text(`NIT: ${companyDocument}`, centerX, yPos, { align: 'center' })
      yPos += 6
    } else {
      yPos += 2
    }

    // Línea separadora
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 6

    // ==================== INFORMACIÓN DE COTIZACIÓN ====================
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(10)
    pdf.text('COTIZACIÓN', centerX, yPos, { align: 'center' })
    yPos += 5

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(8)
    pdf.text(`No: ${quotationCode}`, leftMargin, yPos)
    yPos += 4

    const quotationDate = new Date(created_at || date)
    pdf.text(`Fecha: ${quotationDate.toLocaleDateString('es-CO')}`, leftMargin, yPos)
    yPos += 4

    // Validez de la cotización
    const expiryDate = new Date(quotationDate)
    expiryDate.setDate(expiryDate.getDate() + (validity_days || 15))
    pdf.text(`Válida hasta: ${expiryDate.toLocaleDateString('es-CO')}`, leftMargin, yPos)
    yPos += 4

    pdf.text(`Asesor: ${cashier}`, leftMargin, yPos)
    yPos += 6

    // Línea punteada
    pdf.setLineDashPattern([1, 1], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 6

    // ==================== INFORMACIÓN DEL CLIENTE ====================
    pdf.setFont('helvetica', 'bold')
    pdf.text('CLIENTE:', leftMargin, yPos)
    yPos += 4
    pdf.setFont('helvetica', 'normal')
    const customerText = customer_name || customer || 'Cliente'
    pdf.text(customerText, leftMargin, yPos, { maxWidth: 72 })
    yPos += 6

    // Línea punteada
    pdf.setLineDashPattern([1, 1], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 6

    // ==================== TABLA DE PRODUCTOS ====================
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(7)
    pdf.text('Descripción', leftMargin, yPos)
    pdf.text('Cant', leftMargin + 40, yPos)
    pdf.text('Precio', leftMargin + 50, yPos)
    pdf.text('Total', leftMargin + 65, yPos)
    yPos += 3

    pdf.setLineDashPattern([1, 1], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 4

    // Items de la cotización
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)

    items.forEach(item => {
      const itemName = item.name || item.product_name || 'Producto'
      const quantity = item.quantity || 0
      const price = item.price || item.unit_price || 0
      const itemTotal = quantity * price

      // Nombre del producto (con wrapping si es muy largo)
      const nameLines = pdf.splitTextToSize(itemName, 36)
      nameLines.forEach((line, index) => {
        if (index === 0) {
          pdf.text(line, leftMargin, yPos)
        } else {
          yPos += 3
          pdf.text(line, leftMargin, yPos)
        }
      })

      // Cantidad, precio y total en la primera línea
      pdf.text(quantity.toString(), leftMargin + 42, yPos - (nameLines.length - 1) * 3)
      pdf.text(`$${price.toLocaleString()}`, leftMargin + 50, yPos - (nameLines.length - 1) * 3)
      pdf.text(`$${itemTotal.toLocaleString()}`, leftMargin + 65, yPos - (nameLines.length - 1) * 3)

      yPos += 4
    })

    // Línea punteada
    yPos += 2
    pdf.setLineDashPattern([1, 1], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 5

    // ==================== TOTALES ====================
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(8)

    // Subtotal
    pdf.text('Subtotal:', leftMargin, yPos)
    pdf.text(`$${subtotal.toLocaleString()}`, rightMargin, yPos, { align: 'right' })
    yPos += 4

    // Descuento (si aplica)
    if (discount > 0) {
      pdf.text('Descuento:', leftMargin, yPos)
      pdf.text(`-$${discount.toLocaleString()}`, rightMargin, yPos, { align: 'right' })
      yPos += 4
    }

    // IVA
    const taxAmount = tax_amount || tax || 0
    if (taxAmount > 0) {
      pdf.text(`${taxLabel} (${taxRate}%):`, leftMargin, yPos)
      pdf.text(`$${taxAmount.toLocaleString()}`, rightMargin, yPos, { align: 'right' })
      yPos += 4
    }

    // Línea de total
    pdf.setLineWidth(0.5)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 5

    // TOTAL FINAL
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(10)
    pdf.text('TOTAL:', leftMargin, yPos)
    pdf.text(`$${total.toLocaleString()}`, rightMargin, yPos, { align: 'right' })
    yPos += 6

    // Línea punteada
    pdf.setLineDashPattern([1, 1], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 6

    // ==================== NOTAS (si aplica) ====================
    if (notes && notes.trim()) {
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(8)
      pdf.text('OBSERVACIONES:', leftMargin, yPos)
      yPos += 4

      pdf.setFont('helvetica', 'normal')
      pdf.setFontSize(7)
      const notesLines = pdf.splitTextToSize(notes, 72)
      notesLines.forEach(line => {
        pdf.text(line, leftMargin, yPos)
        yPos += 3
      })
      yPos += 3

      // Línea punteada
      pdf.setLineDashPattern([1, 1], 0)
      pdf.line(leftMargin, yPos, rightMargin, yPos)
      pdf.setLineDashPattern([], 0)
      yPos += 6
    }

    // ==================== MENSAJE ====================
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(8)
    pdf.text('COTIZACIÓN NO VÁLIDA COMO FACTURA', centerX, yPos, { align: 'center' })
    yPos += 4

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)
    pdf.text('Esta es una cotización sujeta a cambios', centerX, yPos, { align: 'center' })
    yPos += 3
    pdf.text('Los precios pueden variar sin previo aviso', centerX, yPos, { align: 'center' })
    yPos += 8

    // ==================== QR CODE ====================
    const qrSize = 25
    const qrX = (pageWidth - qrSize) / 2
    pdf.addImage(qrDataURL, 'PNG', qrX, yPos, qrSize, qrSize)
    yPos += qrSize + 3

    pdf.setFontSize(7)
    pdf.text(quotationCode, centerX, yPos, { align: 'center' })
    yPos += 6

    // ==================== INFORMACIÓN DE CONTACTO ====================
    pdf.setFontSize(6)
    pdf.setFont('helvetica', 'normal')
    if (companyPhone) {
      pdf.text(`Contáctenos: ${companyPhone}`, centerX, yPos, { align: 'center' })
      yPos += 3
    }
    if (companyEmail) {
      pdf.text(companyEmail, centerX, yPos, { align: 'center' })
      yPos += 3
    }

    return pdf

  } catch (error) {
    console.error('Error creando PDF de cotización:', error)
    throw new Error(`Error generando PDF: ${error.message}`)
  }
}
