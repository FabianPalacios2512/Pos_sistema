/**
 * Utility to generate invoice preview images for onboarding
 * Uses the real PDF generation code to ensure 100% accuracy
 */
import { createInvoiceTemplate } from './pdfTemplates/invoiceTemplate.js'

/**
 * Generate a preview image of an invoice template
 * @param {string} templateName - 'classic', 'modern', or 'minimal'
 * @param {Object} businessData - Business information
 * @returns {Promise<string>} Data URL of the invoice image
 */
export const generateInvoicePreview = async (templateName, businessData = {}) => {
    // Sample invoice data for preview
    const sampleInvoiceData = {
        invoice_number: 'PRE-001',
        date: new Date(),
        customer_name: 'Cliente General',
        cashier: 'Admin',
        items: [
            { name: 'Producto Ejemplo A', quantity: 1, price: 25000 },
            { name: 'Producto Ejemplo B', quantity: 2, price: 25000 },
            { name: 'Servicio Especial', quantity: 1, price: 100000 }
        ],
        subtotal: 175000,
        tax: 33250,
        total: 208250,
        payments: [{ method: 'Efectivo', amount: 208250 }]
    }

    // System settings with the selected template
    const systemSettings = {
        company_name: businessData.storeName || 'MI EMPRESA',
        company_address: businessData.address || 'Calle 123 #45-67, Bogotá',
        company_phone: businessData.phone || '+57 300 123 4567',
        company_email: businessData.email || '',
        company_logo: businessData.logo || null,
        invoice_template: templateName,
        invoice_footer_message: businessData.thankYouMessage || '¡Gracias por su compra!',
        iva_percentage: 19,
        iva_display_name: 'IVA'
    }

    try {
        // Generate the actual PDF using the real code
        const pdf = await createInvoiceTemplate(sampleInvoiceData, systemSettings)

        // Convert PDF to image data URL
        const canvas = document.createElement('canvas')
        const imgData = pdf.output('dataurlstring')

        // For better quality, we'll use the PDF's internal canvas
        // jsPDF can output as canvas which we can then convert to image
        const pdfCanvas = await pdf.html(pdf.internal.pages[1], {
            callback: function (doc) {
                return doc.output('canvas')
            }
        })

        return imgData
    } catch (error) {
        console.error('Error generating invoice preview:', error)
        return null
    }
}

/**
 * Generate preview images for all three templates
 * @param {Object} businessData - Business information
 * @returns {Promise<Object>} Object with preview images for each template
 */
export const generateAllPreviews = async (businessData = {}) => {
    const templates = ['classic', 'modern', 'minimal']
    const previews = {}

    for (const template of templates) {
        previews[template] = await generateInvoicePreview(template, businessData)
    }

    return previews
}
