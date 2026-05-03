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
 * @returns {Promise<jsPDF>} Objeto PDF listo para descargar o enviar
 */
export const createQuotationTemplate = async (quotationData, systemSettings = {}) => {
  try {
    // Extraer datos
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

    const quotationCode = quotation_number || quotationNumber || 'SIN-NUMERO'

    // Configuración empresa
    const companyName = systemSettings.company_name || 'MI EMPRESA'
    const companyAddress = systemSettings.company_address || ''
    const companyPhone = systemSettings.company_phone || ''
    const companyEmail = systemSettings.company_email || ''
    const companyDocument = systemSettings.company_document || ''
    const companyLogo = systemSettings.company_logo || systemSettings.logo || null
    const taxLabel = systemSettings.iva_display_name || 'IVA'
    const taxRate = systemSettings.iva_percentage || 19

    // Generar QR Code
    const qrDataURL = await QRCode.toDataURL(quotationCode, {
      width: 100,
      margin: 1,
      color: { dark: '#000000', light: '#FFFFFF' }
    })

    // Calcular altura dinámica
    const headerHeight = companyLogo ? 45 : 30
    const infoHeight = 35
    const itemHeight = 6
    const itemCount = items.length || 1
    const totalsHeight = 35
    const notesHeight = notes && notes.trim() ? 20 : 0
    const messageHeight = 20
    const qrSectionHeight = 40
    
    const dynamicHeight = headerHeight + infoHeight + (itemCount * itemHeight) + totalsHeight + notesHeight + messageHeight + qrSectionHeight

    // Crear PDF
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
      const contactLine = [companyPhone, companyEmail].filter(Boolean).join(' • ')
      pdf.text(contactLine, centerX, yPos, { align: 'center' })
      yPos += 4
    } else {
      yPos += 2
    }

    // Línea doble
    pdf.setLineWidth(0.3)
    pdf.setDrawColor(0, 0, 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 1
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 6

    // ==================== INFORMACIÓN DE COTIZACIÓN ====================
    pdf.setTextColor(0, 0, 0)
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(10)
    pdf.text('COTIZACIÓN', centerX, yPos, { align: 'center' })
    yPos += 5

    pdf.setFont('courier', 'bold')
    pdf.setFontSize(12)
    pdf.text(`No. ${quotationCode}`, centerX, yPos, { align: 'center' })
    yPos += 8

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)
    
    const quotationDate = new Date(created_at || date)
    const dateStr = quotationDate.toLocaleDateString('es-CO', { day: '2-digit', month: 'long', year: 'numeric' })
    pdf.text(`Fecha: ${dateStr}`, leftMargin, yPos)
    yPos += 4

    const expiryDate = new Date(quotationDate)
    expiryDate.setDate(expiryDate.getDate() + (validity_days || 15))
    const expDateStr = expiryDate.toLocaleDateString('es-CO', { day: '2-digit', month: 'long', year: 'numeric' })
    pdf.text(`Válida hasta: ${expDateStr}`, leftMargin, yPos)
    yPos += 4

    pdf.text(`Atendido por: ${cashier}`, leftMargin, yPos)
    yPos += 5

    // Separador
    pdf.setLineWidth(0.2)
    pdf.setLineDashPattern([2, 2], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 4

    // ==================== INFORMACIÓN DEL CLIENTE ====================
    const customerText = customer_name || customer || 'Cliente General'
    pdf.setFont('helvetica', 'bold')
    pdf.text('CLIENTE:', leftMargin, yPos)
    pdf.setFont('helvetica', 'normal')
    pdf.text(customerText, leftMargin + 14, yPos, { maxWidth: 60 })
    yPos += 5

    // Línea continua
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

    // Items
    if (items.length > 0) {
      items.forEach((item, index) => {
        const itemName = item.name || item.product_name || 'Producto'
        const quantity = item.quantity || 0
        const price = item.price || item.unit_price || 0
        const itemTotal = quantity * price

        const nameLines = pdf.splitTextToSize(itemName, 32)
        
        pdf.setFont('helvetica', 'normal')
        pdf.setFontSize(7)
        pdf.setTextColor(0, 0, 0)
        pdf.text(nameLines[0], leftMargin + 1, yPos)
        
        pdf.setFont('courier', 'normal')
        pdf.text(quantity.toString(), leftMargin + 38, yPos, { align: 'center' })
        
        pdf.setTextColor(102, 102, 102)
        pdf.setFontSize(6)
        pdf.text(`$${price.toLocaleString('es-CO')}`, leftMargin + 53, yPos, { align: 'right' })
        
        pdf.setFont('courier', 'bold')
        pdf.setFontSize(7)
        pdf.setTextColor(0, 0, 0)
        pdf.text(`$${itemTotal.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
        
        yPos += 4

        if (index < items.length - 1) {
          pdf.setLineWidth(0.1)
          pdf.setDrawColor(238, 238, 238)
          pdf.line(leftMargin, yPos, rightMargin, yPos)
          yPos += 2
        }
      })
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
    pdf.text(`$${subtotal.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
    yPos += 4

    if (discount > 0) {
      pdf.text('Descuento:', rightMargin - 25, yPos, { align: 'right' })
      pdf.text(`-$${discount.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
      yPos += 4
    }

    const taxAmount = tax_amount || tax || 0
    if (taxAmount > 0) {
      pdf.text(`${taxLabel} (${taxRate}%):`, rightMargin - 25, yPos, { align: 'right' })
      pdf.text(`$${taxAmount.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
      yPos += 4
    }

    // Línea doble antes de total
    yPos += 1
    pdf.setLineWidth(0.3)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 1
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 5

    // TOTAL FINAL
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(10)
    pdf.text('TOTAL COTIZACIÓN', leftMargin + 1, yPos + 3)
    
    pdf.setFont('courier', 'bold')
    pdf.setFontSize(18)
    pdf.text(`$${total.toLocaleString('es-CO')}`, rightMargin - 1, yPos + 3, { align: 'right' })
    yPos += 10

    // ==================== NOTAS ====================
    if (notes && notes.trim()) {
      yPos += 2
      pdf.setLineWidth(0.2)
      pdf.setLineDashPattern([2, 2], 0)
      pdf.line(leftMargin, yPos, rightMargin, yPos)
      pdf.setLineDashPattern([], 0)
      yPos += 5

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
      yPos += 2
    }

    // ==================== MENSAJE ====================
    yPos += 4
    pdf.setLineWidth(0.2)
    pdf.setDrawColor(0, 0, 0)
    pdf.setLineDashPattern([2, 2], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 6

    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(8)
    pdf.text('DOCUMENTO NO VÁLIDO COMO FACTURA', centerX, yPos, { align: 'center' })
    yPos += 4

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)
    pdf.text('Cotización informativa sujeta a cambios', centerX, yPos, { align: 'center' })
    yPos += 3
    pdf.text('Precios pueden variar sin previo aviso', centerX, yPos, { align: 'center' })
    yPos += 6

    // ==================== QR CODE ====================
    const qrSize = 22
    pdf.addImage(qrDataURL, 'PNG', centerX - qrSize/2, yPos, qrSize, qrSize, '', 'FAST')
    yPos += qrSize + 2

    pdf.setFontSize(6)
    pdf.text(quotationCode, centerX, yPos, { align: 'center' })
    yPos += 6

    // ==================== PIE DE PÁGINA ====================
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(5)
    pdf.setTextColor(150, 150, 150)
    pdf.text('Powered by 105POS', centerX, yPos, { align: 'center' })

    return pdf

  } catch (error) {
    console.error('Error creando PDF de cotización:', error)
    throw new Error(`Error generando PDF: ${error.message}`)
  }
}
