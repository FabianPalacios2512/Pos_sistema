/**
 * Generador Centralizado de PDFs
 * Servicio único para generar PDFs de forma consistente
 * Usa SOLO jsPDF (NO html2canvas) para calidad vectorial perfecta
 */

import { createInvoiceTemplate } from './invoiceTemplate'
import { createQuotationTemplate } from './quotationTemplate'
import { createPurchaseOrderTemplate } from './purchaseOrderTemplate'
import { createReturnTemplate } from './returnTemplate'

/**
 * Generar PDF de factura
 * @param {Object} invoiceData - Datos de la factura
 * @param {Object} systemSettings - Configuración del sistema
 * @returns {Promise<jsPDF>} PDF generado
 */
export const generateInvoicePDF = async (invoiceData, systemSettings = {}) => {
  try {
    return await createInvoiceTemplate(invoiceData, systemSettings)
  } catch (error) {
    console.error('Error en generateInvoicePDF:', error)
    throw error
  }
}

/**
 * Generar PDF de cotización
 * @param {Object} quotationData - Datos de la cotización
 * @param {Object} systemSettings - Configuración del sistema
 * @returns {Promise<jsPDF>} PDF generado
 */
export const generateQuotationPDF = async (quotationData, systemSettings = {}) => {
  try {
    return await createQuotationTemplate(quotationData, systemSettings)
  } catch (error) {
    console.error('Error en generateQuotationPDF:', error)
    throw error
  }
}

/**
 * Generar PDF de orden de compra
 * @param {Object} orderData - Datos de la orden de compra
 * @param {Object} systemSettings - Configuración del sistema
 * @returns {jsPDF} PDF generado
 */
export const generatePurchaseOrderPDF = (orderData, systemSettings = {}) => {
  try {
    return createPurchaseOrderTemplate(orderData, systemSettings)
  } catch (error) {
    console.error('Error en generatePurchaseOrderPDF:', error)
    throw error
  }
}

/**
 * Generar PDF de devolución
 * @param {Object} returnData - Datos de la devolución
 * @param {Object} systemSettings - Configuración del sistema
 * @returns {Promise<jsPDF>} PDF generado
 */
export const generateReturnPDF = async (returnData, systemSettings = {}) => {
  try {
    return await createReturnTemplate(returnData, systemSettings)
  } catch (error) {
    console.error('Error en generateReturnPDF:', error)
    throw error
  }
}

/**
 * Descargar PDF directamente
 * @param {jsPDF} pdf - Objeto PDF generado
 * @param {string} filename - Nombre del archivo
 */
export const downloadPDF = (pdf, filename) => {
  try {
    pdf.save(filename)
  } catch (error) {
    console.error('Error descargando PDF:', error)
    throw new Error('Error al descargar el PDF')
  }
}

/**
 * Obtener PDF como Blob para enviar por WhatsApp
 * @param {jsPDF} pdf - Objeto PDF generado
 * @returns {Blob} PDF en formato Blob
 */
export const getPDFBlob = (pdf) => {
  try {
    return pdf.output('blob')
  } catch (error) {
    console.error('Error obteniendo Blob del PDF:', error)
    throw new Error('Error al generar Blob del PDF')
  }
}

/**
 * Obtener PDF como Data URL para preview
 * @param {jsPDF} pdf - Objeto PDF generado
 * @returns {string} PDF en formato Data URL
 */
export const getPDFDataURL = (pdf) => {
  try {
    return pdf.output('dataurlstring')
  } catch (error) {
    console.error('Error obteniendo DataURL del PDF:', error)
    throw new Error('Error al generar DataURL del PDF')
  }
}

export default {
  generateInvoicePDF,
  generateQuotationPDF,
  generatePurchaseOrderPDF,
  generateReturnPDF,
  downloadPDF,
  getPDFBlob,
  getPDFDataURL
}
