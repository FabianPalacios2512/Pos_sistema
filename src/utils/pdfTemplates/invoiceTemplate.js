/**
 * Plantilla de Factura/Invoice para PDF
 * Genera PDF vectorial con jsPDF (NO usa html2canvas)
 * Se usa para: Imprimir, Descargar, WhatsApp
 * Soporta 3 templates: Classic, Modern, Minimal
 */
import jsPDF from 'jspdf'
import QRCode from 'qrcode'
import { getTemplateStyle, applyHeaderStyle, applyBorderStyle, applyTotalStyle } from './templateStyles.js'

/**
 * Crear PDF de factura con diseño tipo ticket térmico (80mm)
 * @param {Object} invoiceData - Datos de la factura
 * @param {Object} systemSettings - Configuración del sistema (empresa, IVA, template, etc)
 * @returns {jsPDF} Objeto PDF listo para descargar o enviar
 */
export const createInvoiceTemplate = async (invoiceData, systemSettings = {}) => {
  try {
    // Extraer datos de la factura
    const {
      invoice_number = '',
      invoiceNumber = '',
      date = new Date(),
      created_at = date,
      customer = 'Cliente General',
      customer_name = customer,
      cashier = 'Vendedor',
      items = [],
      subtotal = 0,
      discount = 0,
      tax = 0,
      tax_amount = tax,
      total = 0,
      payments = [],
      change = 0,
      notes = ''
    } = invoiceData

    // Número de factura (compatibilidad con diferentes nombres)
    const invoiceCode = invoice_number || invoiceNumber || 'SIN-NUMERO'

    // Configuración de la empresa
    const companyName = systemSettings.company_name || 'MI EMPRESA'
    const companyAddress = systemSettings.company_address || ''
    const companyPhone = systemSettings.company_phone || ''
    const companyEmail = systemSettings.company_email || ''
    const companyDocument = systemSettings.company_document || ''
    const companyLogo = systemSettings.company_logo || null
    const thankYouMessage = systemSettings.invoice_footer_message || systemSettings.thank_you_message || '¡Gracias por su compra!'
    const qrStyle = systemSettings.qr_style || 'rounded'
    const taxLabel = systemSettings.iva_display_name || 'IVA'
    const taxRate = systemSettings.iva_percentage || 19

    // 🎨 TEMPLATE SELECCIONADO (classic, modern, minimal)
    const selectedTemplate = systemSettings.invoice_template || 'classic'
    const style = getTemplateStyle(selectedTemplate)

    console.log(`📄 Generando factura con template: ${selectedTemplate.toUpperCase()}`)
    console.log('📋 System Settings recibidos:', {
      invoice_template: systemSettings.invoice_template,
      qr_style: systemSettings.qr_style,
      company_logo: systemSettings.company_logo ? 'SI' : 'NO',
      invoice_footer_message: systemSettings.invoice_footer_message
    })

    // Generar QR Code
    const qrDataURL = await QRCode.toDataURL(invoiceCode, {
      width: 80,
      margin: 1,
      color: { dark: '#000000', light: '#FFFFFF' }
    })

    // Calcular altura dinámica exacta según contenido
    const headerHeight = 50 // Header empresa + factura
    const customerHeight = 10 // Info cliente
    const tableHeaderHeight = 10 // Header tabla
    const itemHeight = 5 // Espacio por producto
    const itemCount = items.length
    const totalsHeight = 35 // Totales + forma de pago
    const messageHeight = 15 // Mensaje agradecimiento
    const qrHeight = 35 // QR + código
    const legalHeight = 18 // Info legal (4 líneas)
    const footerHeight = 5 // Powered by

    const dynamicHeight = headerHeight + customerHeight + tableHeaderHeight +
      (itemCount * itemHeight) + totalsHeight + messageHeight +
      qrHeight + legalHeight + footerHeight + 10 // 10mm padding extra

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

    // ==================== HEADER EMPRESA CON TEMPLATE ====================
    // Header colorido para template moderno
    // ==================== HEADER EMPRESA CON TEMPLATE ====================
    // Header colorido para template moderno
    if (style.name === 'modern') {
      pdf.setFillColor(79, 70, 229) // Indigo-600
      pdf.rect(0, 0, pageWidth, 25, 'F')
      pdf.setTextColor(255, 255, 255) // Texto blanco
    } else {
      pdf.setTextColor(17, 24, 39) // Gray-900 para classic
    }

    // Logo (si existe)
    if (companyLogo) {
      try {
        pdf.addImage(companyLogo, 'PNG', centerX - 8, yPos, 16, 10, '', 'FAST')
        yPos += 12
      } catch (err) {
        console.log('No se pudo cargar el logo')
      }
    }

    // Nombre de la empresa
    pdf.setFont('helvetica', style.fonts.company.style)
    pdf.setFontSize(style.fonts.company.size)
    pdf.text(companyName.toUpperCase(), centerX, yPos, { align: 'center' })
    yPos += 6

    // Información de empresa
    pdf.setFont('helvetica', style.fonts.header.style)
    pdf.setFontSize(style.fonts.header.size)

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
      yPos += 5
    } else {
      yPos += 3
    }

    // Resetear color del texto a negro después del header moderno
    pdf.setTextColor(0, 0, 0)

    // Línea separadora según template
    applyBorderStyle(pdf, style)
    if (style.name === 'minimal') {
      pdf.setLineWidth(style.layout.borderWidth * 2)
    }
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    if (style.name === 'classic') {
      yPos += 0.5
      pdf.line(leftMargin, yPos, rightMargin, yPos)
    }
    yPos += 5

    // ==================== INFORMACIÓN DE FACTURA CON TEMPLATE ====================
    // Caja con el número de factura según template
    applyHeaderStyle(pdf, style, leftMargin, yPos, pageWidth - 8, 14)

    // Color del texto según template
    if (style.name === 'modern') {
      pdf.setTextColor(255, 255, 255) // Blanco para modern
    } else if (style.name === 'classic') {
      pdf.setTextColor(17, 24, 39) // Gray-900 fuerte
    } else {
      pdf.setTextColor(0, 0, 0) // Negro para minimal
    }

    pdf.setFont('helvetica', style.fonts.title.style)
    pdf.setFontSize(style.fonts.title.size)
    pdf.text('FACTURA DE VENTA', centerX, yPos + 5, { align: 'center' })

    pdf.setFontSize(style.fonts.title.size - 1)
    pdf.text(`No. ${invoiceCode}`, centerX, yPos + 10, { align: 'center' })

    pdf.setTextColor(0, 0, 0) // Reset color
    yPos += 17

    // Fecha y hora en formato más legible
    const invoiceDate = new Date(created_at || date)
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)

    const fechaFormateada = invoiceDate.toLocaleDateString('es-CO', {
      day: '2-digit',
      month: 'long',
      year: 'numeric'
    })
    const horaFormateada = invoiceDate.toLocaleTimeString('es-CO', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    })

    pdf.text(`Fecha: ${fechaFormateada}`, leftMargin, yPos)
    yPos += 3
    pdf.text(`Hora: ${horaFormateada}`, leftMargin, yPos)
    yPos += 3
    pdf.text(`Atendido por: ${cashier}`, leftMargin, yPos)
    yPos += 5

    // Separador sutil
    pdf.setLineWidth(0.2)
    pdf.setLineDashPattern([2, 2], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 4

    // ==================== INFORMACIÓN DEL CLIENTE MEJORADA ====================
    const customerText = customer_name || customer || 'Cliente General'

    if (customerText && customerText !== 'Cliente General') {
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(8)
      pdf.text('DATOS DEL CLIENTE', leftMargin, yPos)
      yPos += 4

      pdf.setFont('helvetica', 'normal')
      pdf.setFontSize(7)
      pdf.text(`Nombre: ${customerText}`, leftMargin, yPos, { maxWidth: 72 })
      yPos += 4
    } else {
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(7)
      pdf.text('CLIENTE:', leftMargin, yPos)
      pdf.setFont('helvetica', 'normal')
      pdf.text(customerText, leftMargin + 15, yPos)
      yPos += 4
    }

    // Línea doble antes de productos (más profesional)
    pdf.setLineWidth(0.3)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 0.5
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 5

    // ==================== TABLA DE PRODUCTOS MEJORADA ====================
    // Encabezado con fondo según template
    // Encabezado con fondo según template
    if (style.name === 'modern') {
      pdf.setFillColor(238, 242, 255) // Indigo-50
    } else if (style.name === 'classic') {
      pdf.setFillColor(255, 255, 255) // Blanco (sin fondo gris)
    } else {
      pdf.setFillColor(255, 255, 255) // Blanco para minimal
    }

    // Dibujar fondo solo si no es classic (Classic es limpio)
    if (style.name !== 'classic') {
      pdf.rect(leftMargin, yPos - 1, pageWidth - 8, 5, 'F')
    } else {
      // En classic, solo una línea abajo del header
      pdf.setDrawColor(229, 231, 235)
      pdf.setLineWidth(0.1)
      pdf.line(leftMargin, yPos + 4, rightMargin, yPos + 4)
    }

    // Color texto header según template
    // Color texto header según template
    if (style.name === 'modern') {
      pdf.setTextColor(67, 56, 202) // Indigo-700
    } else if (style.name === 'classic') {
      pdf.setTextColor(17, 24, 39) // Gray-900
    } else {
      pdf.setTextColor(0, 0, 0)
    }

    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(style.fonts.header.size)
    pdf.text('DESCRIPCIÓN', leftMargin + 1, yPos + 2)
    pdf.text('CANT', leftMargin + 42, yPos + 2, { align: 'center' })
    pdf.text('PRECIO', leftMargin + 54, yPos + 2, { align: 'right' })
    pdf.text('TOTAL', rightMargin - 1, yPos + 2, { align: 'right' })

    pdf.setTextColor(0, 0, 0) // Reset
    yPos += 5

    pdf.setLineWidth(0.2)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 3

    // Items de la factura con mejor formato
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)

    items.forEach((item, index) => {
      const itemName = item.name || item.product_name || 'Producto'
      const quantity = item.quantity || 0
      const price = item.price || item.unit_price || 0
      const itemTotal = quantity * price

      // Fondo alternado para mejor legibilidad
      if (index % 2 === 0) {
        pdf.setFillColor(252, 252, 252)
        pdf.rect(leftMargin, yPos - 2, pageWidth - 8, 5, 'F')
      }

      // Nombre del producto (con wrapping si es muy largo)
      const nameLines = pdf.splitTextToSize(itemName, 38)
      const firstLine = nameLines[0]
      pdf.text(firstLine, leftMargin + 1, yPos)

      // Cantidad (centrado)
      pdf.text(quantity.toString(), leftMargin + 42, yPos, { align: 'center' })

      // Precio (alineado derecha)
      pdf.text(`$${price.toLocaleString('es-CO')}`, leftMargin + 54, yPos, { align: 'right' })

      // Total (alineado derecha, en negrita si es múltiple)
      if (quantity > 1) {
        pdf.setFont('helvetica', 'bold')
      }
      pdf.text(`$${itemTotal.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
      pdf.setFont('helvetica', 'normal')

      // Líneas adicionales del nombre (si las hay)
      if (nameLines.length > 1) {
        yPos += 3
        for (let i = 1; i < nameLines.length && i < 2; i++) {
          pdf.setFontSize(6)
          pdf.text(nameLines[i], leftMargin + 1, yPos)
          yPos += 2.5
        }
        pdf.setFontSize(7)
      }

      yPos += 4
    })

    // Línea separadora antes de totales
    yPos += 2
    pdf.setLineWidth(0.2)
    pdf.setLineDashPattern([2, 2], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 4

    // ==================== TOTALES MEJORADOS ====================
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(8)

    // Subtotal
    pdf.text('Subtotal:', leftMargin + 35, yPos)
    pdf.text(`$${subtotal.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
    yPos += 4

    // Descuento (si aplica) - destacado en rojo
    if (discount > 0) {
      pdf.setTextColor(220, 38, 38) // Rojo
      pdf.text('Descuento:', leftMargin + 35, yPos)
      pdf.text(`-$${discount.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
      pdf.setTextColor(0, 0, 0) // Volver a negro
      yPos += 4
    }

    // IVA
    const taxAmount = tax_amount || tax || 0
    if (taxAmount > 0) {
      pdf.text(`${taxLabel} (${taxRate}%):`, leftMargin + 35, yPos)
      pdf.text(`$${taxAmount.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
      yPos += 4
    }

    // Línea doble antes del total
    yPos += 1
    pdf.setLineWidth(0.4)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 0.5
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 4

    // TOTAL FINAL - Con estilo del template
    applyTotalStyle(pdf, style, leftMargin, yPos - 2, pageWidth - 8, 9)

    // Color del texto del total según template
    // Color del texto del total según template
    if (style.name === 'modern') {
      pdf.setTextColor(67, 56, 202) // Indigo-700
    } else if (style.name === 'classic') {
      pdf.setTextColor(17, 24, 39) // Gray-900
    } else {
      pdf.setTextColor(0, 0, 0)
    }

    pdf.setFont('helvetica', style.fonts.total.style)
    pdf.setFontSize(style.fonts.total.size)
    pdf.text('TOTAL A PAGAR:', leftMargin + 2, yPos + 3.5)
    pdf.setFontSize(style.fonts.total.size + 2)
    pdf.text(`$${total.toLocaleString('es-CO')}`, rightMargin - 2, yPos + 3.5, { align: 'right' })

    pdf.setTextColor(0, 0, 0) // Reset color
    yPos += 11

    // ==================== INFORMACIÓN DE PAGO MEJORADA ====================
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(7)
    pdf.text('FORMA DE PAGO', leftMargin, yPos)
    yPos += 3

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)

    if (payments && payments.length > 0) {
      payments.forEach(payment => {
        const methodName = payment.methodName || payment.method || 'Efectivo'
        const amount = payment.amount || 0
        pdf.text(`• ${methodName}`, leftMargin, yPos)
        pdf.text(`$${amount.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
        yPos += 3
      })
    } else {
      pdf.text('• Efectivo', leftMargin, yPos)
      pdf.text(`$${total.toLocaleString()}`, rightMargin, yPos, { align: 'right' })
      yPos += 3
    }

    // Cambio (si aplica) - Destacado
    if (change > 0) {
      yPos += 2
      pdf.setFillColor(220, 252, 231) // Verde claro
      pdf.roundedRect(leftMargin, yPos - 2, pageWidth - 8, 6, 1, 1, 'F')

      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(8)
      pdf.text('Su cambio:', leftMargin + 2, yPos + 2)
      pdf.text(`$${change.toLocaleString('es-CO')}`, rightMargin - 2, yPos + 2, { align: 'right' })
      yPos += 7
    } else {
      yPos += 4
    }

    // Línea separadora elegante
    pdf.setLineWidth(0.2)
    pdf.setLineDashPattern([2, 2], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 5

    // ==================== MENSAJE PERSONALIZADO ====================
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(10)

    // Usar el mensaje personalizado del onboarding
    const messageLines = pdf.splitTextToSize(thankYouMessage, 65)
    messageLines.forEach(line => {
      pdf.text(line, centerX, yPos, { align: 'center' })
      yPos += 4
    })
    yPos += 4

    // ==================== QR CODE CON ESTILO SELECCIONADO ====================
    const qrSize = 28
    const qrX = (pageWidth - qrSize) / 2

    // Marco alrededor del QR según estilo seleccionado
    pdf.setLineWidth(0.3)

    if (style.name === 'modern') {
      pdf.setDrawColor(79, 70, 229) // Indigo-600
      pdf.setFillColor(238, 242, 255) // Indigo-50
    } else if (style.name === 'minimal') {
      pdf.setDrawColor(0, 0, 0)
    } else {
      pdf.setDrawColor(200, 200, 200)
    }

    // Aplicar estilo de QR seleccionado en onboarding
    if (qrStyle === 'circle') {
      // QR circular
      pdf.circle(qrX + qrSize / 2, yPos + qrSize / 2, qrSize / 2 + 1, 'S')
    } else if (qrStyle === 'square') {
      // QR cuadrado sin redondeo
      pdf.rect(qrX - 1, yPos - 1, qrSize + 2, qrSize + 2, 'S')
    } else {
      // QR redondeado (default)
      pdf.roundedRect(qrX - 1, yPos - 1, qrSize + 2, qrSize + 2, 2, 2, 'S')
    }

    pdf.addImage(qrDataURL, 'PNG', qrX, yPos, qrSize, qrSize)
    yPos += qrSize + 4

    pdf.setFontSize(7)
    pdf.setFont('helvetica', 'bold')
    pdf.text(invoiceCode, centerX, yPos, { align: 'center' })
    yPos += 6

    // ==================== INFORMACIÓN LEGAL PROFESIONAL ====================
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(6.5)
    pdf.setTextColor(60, 60, 60) // Gris oscuro para mejor legibilidad

    pdf.text('Régimen Común - No responsable de IVA', centerX, yPos, { align: 'center' })
    yPos += 3.5
    pdf.text('Factura de venta Art. 617 del E.T.', centerX, yPos, { align: 'center' })
    yPos += 3.5
    pdf.text('Resolución DIAN 18764069871234', centerX, yPos, { align: 'center' })
    yPos += 3.5
    pdf.text('Vigencia: 01/01/2024 al 31/12/2024', centerX, yPos, { align: 'center' })
    yPos += 5

    // ==================== FOOTER POWERED BY CENTRADO ====================
    pdf.setLineWidth(0.1)
    pdf.setDrawColor(220, 220, 220)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 3

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(6)
    pdf.setTextColor(120, 120, 120) // Gris medio profesional

    // Texto completamente centrado
    const poweredText = 'Powered by 105 POS'
    pdf.text(poweredText, centerX, yPos, { align: 'center' })

    pdf.setTextColor(0, 0, 0) // Reset color

    return pdf

  } catch (error) {
    console.error('Error creando PDF de factura:', error)
    throw new Error(`Error generando PDF: ${error.message}`)
  }
}
