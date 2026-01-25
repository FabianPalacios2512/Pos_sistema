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
      customer = 'Cliente Final',
      customer_name = customer,
      cashier = 'Vendedor',
      items = [],
      subtotal = 0,
      discount = 0,
      tax = 0,
      tax_amount = tax,
      surcharge_amount = 0, // 🎯 Recargo por crédito
      total = 0,
      payments = [],
      change = 0,
      notes = '',
      payment_method = '' // Para saber si es crédito
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
    const headerHeight = 60 // Header empresa + factura (aumentado de 50 a 60 para el logo)
    const customerHeight = 10 // Info cliente
    const tableHeaderHeight = 10 // Header tabla
    const itemHeight = 5 // Espacio por producto
    const itemCount = items.length
    const surchargeLineHeight = surcharge_amount > 0 ? 4 : 0 // 🎯 Espacio para recargo si aplica
    const totalsHeight = 35 + surchargeLineHeight // Totales + forma de pago
    const messageHeight = 15 // Mensaje agradecimiento
    const qrHeight = 35 // QR + código
    const legalHeight = 18 // Info legal (4 líneas)
    const footerHeight = 10 // Powered by + línea divisoria + margen inferior

    const dynamicHeight = headerHeight + customerHeight + tableHeaderHeight +
      (itemCount * itemHeight) + totalsHeight + messageHeight +
      qrHeight + legalHeight + footerHeight + 20 // 20mm padding extra para asegurar espacio

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
    // Modern: Minimalista con color de acento (sin fondo sólido)
    // Classic: Negro puro para máximo contraste
    if (style.name === 'modern') {
      // Modern: Color de acento azul oscuro profesional (sin fondo)
      pdf.setTextColor(0, 86, 179) // #0056b3 - Azul oscuro profesional
    } else {
      pdf.setTextColor(0, 0, 0) // Negro puro para Classic (mejor contraste térmico)
    }

    // Logo (si existe) - Cargar de manera asíncrona
    if (companyLogo) {
      try {
        await new Promise((resolve, reject) => {
          const img = new Image()
          img.onload = () => {
            try {
              const imgAspectRatio = img.width / img.height
              
              // Ancho máximo 14mm, altura máxima 10mm (más compacto)
              let logoWidth = 14
              let logoHeight = logoWidth / imgAspectRatio
              
              // Si la altura calculada excede el máximo, ajustar por altura
              if (logoHeight > 10) {
                logoHeight = 10
                logoWidth = logoHeight * imgAspectRatio
              }
              
              pdf.addImage(companyLogo, 'PNG', centerX - (logoWidth / 2), yPos, logoWidth, logoHeight, '', 'FAST')
              yPos += logoHeight + 3  // Espacio reducido entre logo y nombre
              resolve()
            } catch (err) {
              reject(err)
            }
          }
          img.onerror = reject
          img.src = companyLogo
        })
      } catch (err) {
        console.log('No se pudo cargar el logo', err)
      }
    }

    // Nombre de la empresa - DESTACADO
    pdf.setFont('helvetica', 'bold')
    if (style.name === 'modern') {
      pdf.setFontSize(15) // Ligeramente más grande para Modern
      pdf.setTextColor(0, 86, 179) // Color de acento
    } else {
      pdf.setFontSize(14)
      pdf.setTextColor(0, 0, 0)
    }
    pdf.text(companyName.toUpperCase(), centerX, yPos, { align: 'center' })
    yPos += 5

    // Información de empresa - Compacta
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(8)
    pdf.setTextColor(85, 85, 85) // Gris oscuro elegante para info de contacto

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

    // Resetear color del texto a negro después del header
    pdf.setTextColor(0, 0, 0)

    // Línea separadora según template
    if (style.name === 'classic') {
      // Classic: Línea doble negra
      pdf.setLineWidth(0.3)
      pdf.setDrawColor(0, 0, 0)
      pdf.line(leftMargin, yPos, rightMargin, yPos)
      yPos += 1
      pdf.line(leftMargin, yPos, rightMargin, yPos)
    } else if (style.name === 'modern') {
      // Modern: Línea gruesa con color de acento
      pdf.setLineWidth(0.5)
      pdf.setDrawColor(0, 86, 179) // #0056b3
      pdf.line(leftMargin, yPos, rightMargin, yPos)
    } else {
      // Minimal: Línea fina y sutil gris
      pdf.setLineWidth(0.15)
      pdf.setDrawColor(204, 204, 204) // #cccccc
      pdf.line(leftMargin, yPos, rightMargin, yPos)
    }
    yPos += 5

    // ==================== INFORMACIÓN DE FACTURA CON TEMPLATE ====================
    if (style.name === 'classic') {
      // Classic: Sin fondo, solo texto centrado
      pdf.setTextColor(0, 0, 0)
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(9)
      pdf.text('FACTURA DE VENTA', centerX, yPos + 3, { align: 'center' })
      
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(10)
      pdf.text(`No. ${invoiceCode}`, centerX, yPos + 8, { align: 'center' })
      yPos += 12
    } else if (style.name === 'modern') {
      // Modern: Sin fondo, tipografía elegante con color de acento
      pdf.setTextColor(0, 86, 179) // Color de acento
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(8)
      pdf.text('FACTURA DE VENTA', centerX, yPos + 3, { align: 'center' })
      
      pdf.setTextColor(26, 26, 26) // Casi negro
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(11)
      pdf.text(`No. ${invoiceCode}`, centerX, yPos + 9, { align: 'center' })
      
      pdf.setTextColor(0, 0, 0)
      yPos += 13
    } else {
      // Minimal: Sin fondo, solo tipografía pura y espacio en blanco
      pdf.setTextColor(0, 0, 0)
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(8)
      pdf.text('FACTURA DE VENTA', centerX, yPos + 3, { align: 'center' })
      
      pdf.setFont('courier', 'bold') // Monospace para número
      pdf.setFontSize(12)
      pdf.text(`No. ${invoiceCode}`, centerX, yPos + 9, { align: 'center' })
      
      pdf.setTextColor(0, 0, 0) // Reset color
      yPos += 14
    }

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
    const customerText = customer_name || customer || 'Cliente Final'

    if (customerText && customerText !== 'Cliente Final') {
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

    // Línea antes de productos
    if (style.name === 'modern') {
      pdf.setLineWidth(0.5)
      pdf.setDrawColor(0, 86, 179) // Color de acento
    } else {
      pdf.setLineWidth(0.3)
      pdf.setDrawColor(0, 0, 0)
    }
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    if (style.name === 'classic') {
      yPos += 0.5
      pdf.line(leftMargin, yPos, rightMargin, yPos)
    }
    yPos += 5

    // ==================== TABLA DE PRODUCTOS MEJORADA ====================
    if (style.name === 'classic') {
      // CLASSIC: Header simple DESCRIPCIÓN | TOTAL
      pdf.setTextColor(0, 0, 0)
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(8)
      pdf.text('DESCRIPCIÓN', leftMargin + 1, yPos + 2)
      pdf.text('TOTAL', rightMargin - 1, yPos + 2, { align: 'right' })
      
      yPos += 4
      pdf.setLineWidth(0.3)
      pdf.setDrawColor(0, 0, 0)
      pdf.line(leftMargin, yPos, rightMargin, yPos)
      yPos += 4
    } else if (style.name === 'modern') {
      // MODERN: 4 columnas con color de acento, sin fondo
      pdf.setTextColor(0, 86, 179) // Color de acento azul
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(7)
      
      // Headers: CANT. | DESCRIPCIÓN | P. UNIT. | TOTAL
      pdf.text('CANT.', leftMargin + 6, yPos + 2, { align: 'center' })
      pdf.text('DESCRIPCIÓN', leftMargin + 15, yPos + 2)
      pdf.text('P. UNIT.', leftMargin + 48, yPos + 2, { align: 'right' })
      pdf.text('TOTAL', rightMargin - 1, yPos + 2, { align: 'right' })

      pdf.setTextColor(51, 51, 51) // Gris oscuro
      yPos += 5

      pdf.setLineWidth(0.2)
      pdf.setDrawColor(224, 224, 224) // Gris claro
      pdf.line(leftMargin, yPos, rightMargin, yPos)
      yPos += 3
    } else {
      // MINIMAL: Headers pequeños con letra espaciada, línea fina gris
      pdf.setTextColor(0, 0, 0)
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(6)
      pdf.text('DESCRIPCIÓN', leftMargin + 1, yPos + 2)
      pdf.text('CANT.', leftMargin + 38, yPos + 2, { align: 'center' })
      pdf.text('PRECIO', leftMargin + 53, yPos + 2, { align: 'right' })
      pdf.text('TOTAL', rightMargin - 1, yPos + 2, { align: 'right' })

      yPos += 5
      pdf.setLineWidth(0.15)
      pdf.setDrawColor(204, 204, 204) // Gris claro
      pdf.line(leftMargin, yPos, rightMargin, yPos)
      yPos += 3
    }

    // Items de la factura con formato según template
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)

    items.forEach((item, index) => {
      const itemName = item.name || item.product_name || 'Producto'
      const quantity = item.quantity || 0
      const price = item.price || item.unit_price || 0
      const itemTotal = quantity * price

      if (style.name === 'classic') {
        // CLASSIC: Nombre + total en línea 1, cantidad x precio en línea 2
        const nameLines = pdf.splitTextToSize(itemName, 50)
        pdf.setFont('helvetica', 'bold')
        pdf.setFontSize(8)
        pdf.text(nameLines[0], leftMargin + 1, yPos)
        
        pdf.setFont('helvetica', 'bold')
        pdf.text(`$${itemTotal.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
        yPos += 3
        
        pdf.setFont('courier', 'normal')
        pdf.setFontSize(7)
        pdf.text(`${quantity} x $${price.toLocaleString('es-CO')}`, leftMargin + 1, yPos)
        
        yPos += 3
        if (index < items.length - 1) {
          pdf.setLineWidth(0.1)
          pdf.setDrawColor(200, 200, 200)
          pdf.setLineDashPattern([1, 1], 0)
          pdf.line(leftMargin, yPos, rightMargin, yPos)
          pdf.setLineDashPattern([], 0)
          yPos += 2
        }
      } else if (style.name === 'modern') {
        // MODERN: 4 columnas limpias, sin fondos alternados
        // Columnas: CANT. | DESCRIPCIÓN | P. UNIT. | TOTAL
        const nameLines = pdf.splitTextToSize(itemName, 28)
        
        // Cantidad (monospace, centrado)
        pdf.setFont('courier', 'bold')
        pdf.setFontSize(8)
        pdf.text(quantity.toString(), leftMargin + 6, yPos, { align: 'center' })
        
        // Nombre del producto
        pdf.setFont('helvetica', 'normal')
        pdf.setFontSize(7)
        pdf.setTextColor(51, 51, 51)
        pdf.text(nameLines[0], leftMargin + 15, yPos)
        
        // Precio unitario (monospace, gris)
        pdf.setFont('courier', 'normal')
        pdf.setFontSize(7)
        pdf.setTextColor(102, 102, 102)
        pdf.text(`$${price.toLocaleString('es-CO')}`, leftMargin + 48, yPos, { align: 'right' })
        
        // Total (monospace, bold, oscuro)
        pdf.setFont('courier', 'bold')
        pdf.setFontSize(8)
        pdf.setTextColor(26, 26, 26)
        pdf.text(`$${itemTotal.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
        
        pdf.setTextColor(0, 0, 0)
        yPos += 4
        
        // Línea separadora fina entre productos
        if (index < items.length - 1) {
          pdf.setLineWidth(0.1)
          pdf.setDrawColor(240, 240, 240)
          pdf.line(leftMargin, yPos, rightMargin, yPos)
          yPos += 2
        }
      } else {
        // MINIMAL: Layout limpio con 4 columnas, monospace para números
        const nameLines = pdf.splitTextToSize(itemName, 32)
        
        // Nombre producto (sans-serif)
        pdf.setFont('helvetica', 'normal')
        pdf.setFontSize(7)
        pdf.setTextColor(0, 0, 0)
        pdf.text(nameLines[0], leftMargin + 1, yPos)

        // Cantidad (monospace)
        pdf.setFont('courier', 'normal')
        pdf.text(quantity.toString(), leftMargin + 38, yPos, { align: 'center' })
        
        // Precio unitario (monospace, gris)
        pdf.setTextColor(102, 102, 102)
        pdf.setFontSize(6)
        pdf.text(`$${price.toLocaleString('es-CO')}`, leftMargin + 53, yPos, { align: 'right' })
        
        // Total (monospace, bold, negro)
        pdf.setFont('courier', 'bold')
        pdf.setFontSize(7)
        pdf.setTextColor(0, 0, 0)
        pdf.text(`$${itemTotal.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
        
        pdf.setFont('helvetica', 'normal')
        yPos += 4
        
        // Línea separadora muy fina entre productos
        if (index < items.length - 1) {
          pdf.setLineWidth(0.1)
          pdf.setDrawColor(238, 238, 238) // Gris muy claro
          pdf.line(leftMargin, yPos, rightMargin, yPos)
          yPos += 2
        }
      }
    })

    // Línea separadora antes de totales
    yPos += 2
    if (style.name === 'modern') {
      pdf.setLineWidth(0.2)
      pdf.setDrawColor(224, 224, 224) // Gris claro
    } else {
      pdf.setLineWidth(0.2)
      pdf.setDrawColor(0, 0, 0)
      pdf.setLineDashPattern([2, 2], 0)
    }
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 4

    // ==================== TOTALES MEJORADOS ====================
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(9)

    // Subtotal - Alineado a la derecha
    if (style.name === 'modern') {
      pdf.setTextColor(102, 102, 102) // Gris medio
    }
    pdf.text('Subtotal:', rightMargin - 25, yPos, { align: 'right' })
    if (style.name === 'modern') {
      pdf.setTextColor(51, 51, 51) // Gris oscuro
    }
    pdf.text(`$${subtotal.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
    pdf.setTextColor(0, 0, 0)
    yPos += 4

    // Descuento (si aplica) - destacado en rojo
    if (discount > 0) {
      pdf.setTextColor(220, 38, 38) // Rojo
      pdf.text('Descuento:', rightMargin - 25, yPos, { align: 'right' })
      pdf.text(`-$${discount.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
      pdf.setTextColor(0, 0, 0) // Volver a negro
      yPos += 4
    }

    // IVA - Calcular porcentaje real basado en los valores guardados
    const taxAmount = tax_amount || tax || 0
    let actualTaxRate = taxRate // Default del sistema
    
    // Si hay IVA y subtotal, calcular el porcentaje real usado en la factura
    if (taxAmount > 0 && subtotal > 0) {
      actualTaxRate = Math.round((taxAmount / subtotal) * 100)
    }
    
    if (taxAmount > 0) {
      pdf.text(`${taxLabel} (${actualTaxRate}%):`, rightMargin - 25, yPos, { align: 'right' })
      pdf.text(`$${taxAmount.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
      yPos += 4
    }

    // 🎯 RECARGO POR CRÉDITO (si aplica) - Destacado en ámbar
    if (surcharge_amount > 0) {
      // Calcular porcentaje de recargo
      const surchargePercent = subtotal > 0 ? Math.round((surcharge_amount / subtotal) * 100) : 13
      pdf.setTextColor(217, 119, 6) // Amber-600
      pdf.text(`Recargo (${surchargePercent}%):`, rightMargin - 25, yPos, { align: 'right' })
      pdf.text(`+$${surcharge_amount.toLocaleString('es-CO')}`, rightMargin - 1, yPos, { align: 'right' })
      pdf.setTextColor(0, 0, 0) // Volver a negro
      yPos += 4
    }

    // Línea antes del total
    yPos += 1
    if (style.name === 'modern') {
      pdf.setLineWidth(0.5)
      pdf.setDrawColor(0, 86, 179) // Color de acento
      pdf.line(leftMargin, yPos, rightMargin, yPos)
    } else {
      pdf.setLineWidth(0.3)
      pdf.setDrawColor(0, 0, 0)
      pdf.line(leftMargin, yPos, rightMargin, yPos)
      yPos += 1
      pdf.line(leftMargin, yPos, rightMargin, yPos)
    }
    yPos += 4

    // TOTAL FINAL - Grande y destacado
    if (style.name === 'classic') {
      // Classic: Sin fondo, solo texto grande negro
      pdf.setTextColor(0, 0, 0)
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(11)
      pdf.text('TOTAL A PAGAR:', leftMargin + 1, yPos + 3)
      pdf.setFontSize(16)
      pdf.text(`$${total.toLocaleString('es-CO')}`, rightMargin - 1, yPos + 3, { align: 'right' })
      yPos += 10
    } else if (style.name === 'modern') {
      // Modern: Sin fondo, total con color de acento
      pdf.setTextColor(26, 26, 26) // Casi negro
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(10)
      pdf.text('TOTAL A PAGAR:', leftMargin + 1, yPos + 3)
      
      pdf.setTextColor(0, 86, 179) // Color de acento azul
      pdf.setFontSize(18) // Grande y destacado
      pdf.text(`$${total.toLocaleString('es-CO')}`, rightMargin - 1, yPos + 3, { align: 'right' })
      
      pdf.setTextColor(0, 0, 0)
      yPos += 10
    } else {
      // Minimal: Sin fondo, tipografía pura - Grande y limpio
      pdf.setTextColor(0, 0, 0)
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(10)
      pdf.text('TOTAL A PAGAR', leftMargin + 1, yPos + 3)
      
      // Total con fuente grande monospace
      pdf.setFont('courier', 'bold')
      pdf.setFontSize(18)
      pdf.text(`$${total.toLocaleString('es-CO')}`, rightMargin - 1, yPos + 3, { align: 'right' })
      
      pdf.setTextColor(0, 0, 0) // Reset color
      yPos += 10
    }

    // ==================== INFORMACIÓN DE PAGO MEJORADA ====================
    // Línea antes de forma de pago
    if (style.name === 'modern') {
      pdf.setLineWidth(0.2)
      pdf.setDrawColor(224, 224, 224)
    } else {
      pdf.setLineWidth(0.2)
      pdf.setDrawColor(0, 0, 0)
      pdf.setLineDashPattern([2, 2], 0)
    }
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 4

    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(7)
    if (style.name === 'modern') {
      pdf.setTextColor(0, 86, 179) // Color de acento
    }
    pdf.text('FORMA DE PAGO', leftMargin, yPos)
    pdf.setTextColor(0, 0, 0)
    yPos += 3

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(8)

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
    if (style.name === 'modern') {
      pdf.setLineWidth(0.2)
      pdf.setDrawColor(224, 224, 224)
    } else {
      pdf.setLineWidth(0.2)
      pdf.setLineDashPattern([2, 2], 0)
    }
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 5

    // ==================== MENSAJE PERSONALIZADO ====================
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(10)
    
    // Color del mensaje según template
    if (style.name === 'modern') {
      pdf.setTextColor(0, 86, 179) // Color de acento
    } else {
      pdf.setTextColor(0, 0, 0)
    }

    // Usar el mensaje personalizado del onboarding
    const messageLines = pdf.splitTextToSize(thankYouMessage, 65)
    messageLines.forEach(line => {
      pdf.text(line, centerX, yPos, { align: 'center' })
      yPos += 4
    })
    pdf.setTextColor(0, 0, 0)
    yPos += 4

    // ==================== QR CODE CON ESTILO SELECCIONADO ====================
    const qrSize = 28
    const qrX = (pageWidth - qrSize) / 2

    // Marco alrededor del QR según estilo seleccionado
    pdf.setLineWidth(0.4)

    if (style.name === 'modern') {
      pdf.setDrawColor(0, 86, 179) // Color de acento azul
    } else if (style.name === 'minimal') {
      pdf.setDrawColor(204, 204, 204) // Gris claro para Minimal
      pdf.setLineWidth(0.2) // Línea más fina
    } else {
      pdf.setDrawColor(0, 0, 0) // Negro para Classic
    }

    // Aplicar estilo de QR seleccionado en onboarding
    if (qrStyle === 'circle') {
      pdf.circle(qrX + qrSize / 2, yPos + qrSize / 2, qrSize / 2 + 1, 'S')
    } else if (qrStyle === 'square') {
      pdf.rect(qrX - 1, yPos - 1, qrSize + 2, qrSize + 2, 'S')
    } else {
      // QR redondeado (default)
      if (style.name === 'modern') {
        pdf.roundedRect(qrX - 1, yPos - 1, qrSize + 2, qrSize + 2, 3, 3, 'S')
      } else {
        pdf.roundedRect(qrX - 1, yPos - 1, qrSize + 2, qrSize + 2, 2, 2, 'S')
      }
    }

    pdf.addImage(qrDataURL, 'PNG', qrX, yPos, qrSize, qrSize)
    yPos += qrSize + 4

    pdf.setFontSize(7)
    pdf.setFont('helvetica', 'bold')
    if (style.name === 'modern') {
      pdf.setTextColor(85, 85, 85) // Gris medio
    }
    pdf.text(invoiceCode, centerX, yPos, { align: 'center' })
    pdf.setTextColor(0, 0, 0)
    yPos += 6

    // ==================== INFORMACIÓN LEGAL PROFESIONAL ====================
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(6) // Más pequeño para ahorrar papel
    pdf.setTextColor(0, 0, 0) // Negro puro para impresión térmica

    pdf.text('Régimen Común - No responsable de IVA', centerX, yPos, { align: 'center' })
    yPos += 3
    pdf.text('Factura de venta Art. 617 del E.T.', centerX, yPos, { align: 'center' })
    yPos += 3
    pdf.text('Resolución DIAN 18764069871234', centerX, yPos, { align: 'center' })
    yPos += 3
    pdf.text('Vigencia: 01/01/2024 al 31/12/2024', centerX, yPos, { align: 'center' })
    yPos += 4

    // ==================== FOOTER POWERED BY CENTRADO ====================
    // Línea separadora final
    pdf.setLineWidth(0.1)
    pdf.setDrawColor(180, 180, 180)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 3

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(6)
    pdf.setTextColor(140, 140, 140) // Gris sutil

    // Texto completamente centrado
    const poweredText = 'Powered by 105 POS'
    pdf.text(poweredText, centerX, yPos, { align: 'center' })
    yPos += 3 // Margen inferior después del powered by

    pdf.setTextColor(0, 0, 0) // Reset color

    return pdf

  } catch (error) {
    console.error('Error creando PDF de factura:', error)
    throw new Error(`Error generando PDF: ${error.message}`)
  }
}
