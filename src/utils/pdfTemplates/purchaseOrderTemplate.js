/**
 * Plantilla de Orden de Compra para PDF
 * Genera PDF profesional en formato A4
 */
import jsPDF from 'jspdf'
import 'jspdf-autotable'

/**
 * Crear PDF de orden de compra
 * @param {Object} orderData - Datos de la orden
 * @param {Object} systemSettings - Configuración del sistema
 * @returns {jsPDF} Objeto PDF listo para descargar
 */
export const createPurchaseOrderTemplate = (orderData, systemSettings = {}) => {
  try {
    const {
      order_number = '',
      order_date = new Date(),
      expected_date = null,
      supplier_name = 'Sin proveedor',
      supplier_email = '',
      supplier_phone = '',
      warehouse_name = '',
      items = [],
      subtotal = 0,
      tax = 0,
      total = 0,
      notes = '',
      status = 'draft'
    } = orderData

    // Configuración de la empresa
    const companyName = systemSettings.company_name || 'MI EMPRESA'
    const companyAddress = systemSettings.company_address || ''
    const companyPhone = systemSettings.company_phone || ''
    const companyEmail = systemSettings.company_email || ''
    const companyDocument = systemSettings.company_document || ''

    // Crear PDF en formato A4
    const pdf = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: 'a4'
    })

    let yPos = 20
    const pageWidth = 210
    const leftMargin = 15
    const rightMargin = pageWidth - 15

    // ==================== ENCABEZADO ====================
    // Nombre de la empresa
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(20)
    pdf.setTextColor(17, 24, 39)
    pdf.text(companyName.toUpperCase(), leftMargin, yPos)
    yPos += 7

    // Información de la empresa
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(9)
    pdf.setTextColor(107, 114, 128)
    
    if (companyDocument) {
      pdf.text(`NIT: ${companyDocument}`, leftMargin, yPos)
      yPos += 4
    }
    if (companyAddress) {
      pdf.text(companyAddress, leftMargin, yPos)
      yPos += 4
    }
    if (companyPhone || companyEmail) {
      const contactLine = [companyPhone, companyEmail].filter(Boolean).join(' | ')
      pdf.text(contactLine, leftMargin, yPos)
    }

    // TÍTULO Y NÚMERO DE ORDEN (lado derecho)
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(24)
    pdf.setTextColor(30, 58, 138) // Blue-900
    pdf.text('ORDEN DE COMPRA', rightMargin, 20, { align: 'right' })
    
    pdf.setFontSize(14)
    pdf.setTextColor(17, 24, 39)
    pdf.text(order_number, rightMargin, 28, { align: 'right' })

    // Estado de la orden
    const statusText = status === 'draft' ? 'BORRADOR' : 
                      status === 'pending' ? 'PENDIENTE' : 
                      status === 'partial' ? 'PARCIAL' : 
                      status === 'received' ? 'RECIBIDA' : 
                      status === 'cancelled' ? 'CANCELADA' : status.toUpperCase()
    
    const statusColor = status === 'draft' ? [156, 163, 175] : // Gray
                       status === 'pending' ? [251, 191, 36] : // Amber
                       status === 'partial' ? [59, 130, 246] : // Blue
                       status === 'received' ? [34, 197, 94] : // Green
                       status === 'cancelled' ? [239, 68, 68] : [107, 114, 128] // Red or gray

    pdf.setFillColor(...statusColor)
    pdf.roundedRect(rightMargin - 35, 30, 35, 7, 2, 2, 'F')
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(8)
    pdf.setTextColor(255, 255, 255)
    pdf.text(statusText, rightMargin - 17.5, 34.5, { align: 'center' })

    yPos = 50

    // ==================== INFORMACIÓN DE PROVEEDOR Y FECHAS ====================
    // Línea divisoria
    pdf.setDrawColor(229, 231, 235)
    pdf.setLineWidth(0.5)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 8

    // Información del proveedor (izquierda)
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(10)
    pdf.setTextColor(17, 24, 39)
    pdf.text('PROVEEDOR:', leftMargin, yPos)
    yPos += 5

    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(9)
    pdf.setTextColor(55, 65, 81)
    pdf.text(supplier_name, leftMargin, yPos)
    yPos += 4
    
    if (supplier_email) {
      pdf.text(`Email: ${supplier_email}`, leftMargin, yPos)
      yPos += 4
    }
    if (supplier_phone) {
      pdf.text(`Teléfono: ${supplier_phone}`, leftMargin, yPos)
    }

    // Información de fechas y bodega (derecha)
    let rightYPos = 63
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(9)
    pdf.setTextColor(107, 114, 128)
    
    pdf.text('Fecha Orden:', rightMargin - 60, rightYPos, { align: 'left' })
    pdf.setFont('helvetica', 'normal')
    pdf.text(formatDate(order_date), rightMargin, rightYPos, { align: 'right' })
    rightYPos += 5

    if (expected_date) {
      pdf.setFont('helvetica', 'bold')
      pdf.text('Fecha Esperada:', rightMargin - 60, rightYPos, { align: 'left' })
      pdf.setFont('helvetica', 'normal')
      pdf.text(formatDate(expected_date), rightMargin, rightYPos, { align: 'right' })
      rightYPos += 5
    }

    // ✅ Bodega Destino removida - información interna del sistema

    yPos = Math.max(yPos, rightYPos) + 10

    // ==================== TABLA DE PRODUCTOS ====================
    // ✅ Solo producto y cantidad - NO mostrar precios (información interna)
    const tableHeaders = [['PRODUCTO', 'CANTIDAD']]
    const tableData = items.map(item => [
      item.product_name || item.name || '',
      `${item.quantity || 0} ${item.unit || 'und'}`
    ])

    pdf.autoTable({
      startY: yPos,
      head: tableHeaders,
      body: tableData,
      theme: 'striped',
      headStyles: {
        fillColor: [30, 58, 138], // Blue-900
        textColor: [255, 255, 255],
        fontSize: 10,
        fontStyle: 'bold',
        halign: 'center'
      },
      bodyStyles: {
        fontSize: 10,
        textColor: [55, 65, 81]
      },
      alternateRowStyles: {
        fillColor: [249, 250, 251]
      },
      columnStyles: {
        0: { cellWidth: 130 }, // Producto (más ancho)
        1: { cellWidth: 40, halign: 'center' } // Cantidad
      },
      margin: { left: leftMargin, right: leftMargin }
    })

    yPos = pdf.lastAutoTable.finalY + 10

    // ==================== SIN TOTALES ====================
    // ✅ Precios y totales removidos - son información interna del sistema
    // El proveedor solo necesita ver qué productos y cantidades solicitar

    // ==================== NOTAS ====================
    if (notes) {
      pdf.setFont('helvetica', 'bold')
      pdf.setFontSize(9)
      pdf.setTextColor(107, 114, 128)
      pdf.text('NOTAS:', leftMargin, yPos)
      yPos += 5

      pdf.setFont('helvetica', 'normal')
      pdf.setTextColor(55, 65, 81)
      const notesLines = pdf.splitTextToSize(notes, rightMargin - leftMargin)
      pdf.text(notesLines, leftMargin, yPos)
      yPos += notesLines.length * 4 + 10
    }

    // ==================== PIE DE PÁGINA ====================
    const footerY = 280 // Posición fija en la parte inferior
    
    pdf.setDrawColor(229, 231, 235)
    pdf.line(leftMargin, footerY - 5, rightMargin, footerY - 5)
    
    pdf.setFont('helvetica', 'italic')
    pdf.setFontSize(8)
    pdf.setTextColor(156, 163, 175)
    pdf.text('Este es un documento generado automáticamente. No requiere firma para su validez.', pageWidth / 2, footerY, { align: 'center' })
    
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)
    pdf.text(`Generado el ${formatDate(new Date())}`, pageWidth / 2, footerY + 4, { align: 'center' })

    return pdf
  } catch (error) {
    console.error('Error generando PDF de orden de compra:', error)
    throw error
  }
}

/**
 * Formatear fecha
 */
function formatDate(date) {
  if (!date) return ''
  const d = new Date(date)
  return d.toLocaleDateString('es-ES', { 
    year: 'numeric', 
    month: '2-digit', 
    day: '2-digit' 
  })
}

/**
 * Formatear número con separadores de miles
 */
function formatNumber(num) {
  return Number(num || 0).toLocaleString('es-ES', { 
    minimumFractionDigits: 2, 
    maximumFractionDigits: 2 
  })
}

export default createPurchaseOrderTemplate
