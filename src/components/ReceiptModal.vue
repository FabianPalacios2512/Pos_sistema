<template>
  <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <!-- Contenedor optimizado para factura POS 58mm -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-sm w-full max-h-[90vh] overflow-y-auto animate-scale-in" style="width: 350px;">
      
      <!-- Header Simple -->
      <div class="p-4 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Factura POS
          </h3>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- FACTURA PARA VISUALIZACIÓN -->
      <div class="p-4 dark:bg-gray-800" id="receipt-content">
        
        <!-- Información del Negocio (Formato POS 58mm) -->
        <div class="text-center border-b border-gray-300 dark:border-gray-600 pb-3 mb-3">
          <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ companyInfo.name }}</h2>
          <p v-if="companyInfo.address" class="text-xs text-gray-600 dark:text-gray-300">{{ companyInfo.address }}</p>
          <p v-if="companyInfo.phone" class="text-xs text-gray-600 dark:text-gray-300">Tel: {{ companyInfo.phone }}</p>
          <p v-if="companyInfo.taxId" class="text-xs text-gray-600 dark:text-gray-300">{{ companyInfo.taxLabel }}: {{ companyInfo.taxId }}</p>
        </div>

        <!-- Información de la Venta (Formato POS Compacto) -->
        <div class="text-xs mb-3 space-y-1">
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Factura #:</span>
            <span class="font-semibold text-gray-900 dark:text-white">{{ sale.invoiceNumber }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Fecha:</span>
            <span class="text-gray-900 dark:text-white">{{ formatDate(sale.date) }}</span>
          </div>
          <div v-if="sale.due_date" class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Vence:</span>
            <span class="text-gray-900 dark:text-white">{{ formatDate(sale.due_date) }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Vendedor:</span>
            <span class="text-gray-900 dark:text-white">{{ sale.cashier || 'Vendedor' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Cliente:</span>
            <span class="text-gray-900 dark:text-white">{{ sale.customer || 'Cliente General' }}</span>
          </div>
        </div>

        <!-- Items de la Venta (Formato POS) -->
        <div class="border-t-2 border-gray-300 dark:border-gray-600 pt-3 mb-4">
          <div class="text-xs font-bold text-gray-800 dark:text-gray-200 mb-3 border-b border-dashed border-gray-400 dark:border-gray-600 pb-2">
            PRODUCTOS
          </div>
          
          <div v-for="item in sale.items" :key="item.id" class="mb-3 bg-gray-50 dark:bg-gray-800 p-2 rounded">
            <div class="text-xs">
              <div class="font-medium text-gray-900 dark:text-white mb-1">{{ item.name }}</div>
              <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-gray-400">{{ item.quantity }} x ${{ item.price.toLocaleString() }}</span>
                <span class="font-bold text-gray-900 dark:text-white">${{ (item.quantity * item.price).toLocaleString() }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Descuentos aplicados -->
        <div v-if="sale.appliedDiscount" class="border-t-2 border-dashed border-gray-400 dark:border-gray-600 pt-3 mb-4">
          <div class="text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">DESCUENTO APLICADO</div>
          <div class="flex justify-between text-xs bg-green-50 dark:bg-green-900 p-2 rounded">
            <span class="text-gray-600 dark:text-gray-400">{{ sale.appliedDiscount.name }}</span>
            <span class="text-green-600 dark:text-green-400 font-bold">-${{ sale.discount.toLocaleString() }}</span>
          </div>
        </div>

        <!-- Totales (Formato POS) -->
        <div class="border-t-2 border-dashed border-gray-400 dark:border-gray-600 pt-3 mb-4">
          <div class="space-y-2 text-xs">
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
              <span class="text-gray-900 dark:text-white">${{ sale.subtotal.toLocaleString() }}</span>
            </div>
            <div v-if="sale.discount > 0" class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Descuento:</span>
              <span class="text-red-600 dark:text-red-400">-${{ sale.discount.toLocaleString() }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">IVA ({{ sale.taxRate }}%):</span>
              <span class="text-gray-900 dark:text-white">${{ sale.tax.toLocaleString() }}</span>
            </div>
            <!-- Mostrar comisión si aplica -->
            <div v-if="hasPaymentFees" class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Comisión:</span>
              <span class="text-orange-600 dark:text-orange-400">+${{ paymentFeesTotal.toLocaleString() }}</span>
            </div>
            <div class="flex justify-between text-sm font-bold border-t-2 border-gray-400 dark:border-gray-600 pt-2 mt-3 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
              <span class="text-gray-900 dark:text-white">TOTAL:</span>
              <span class="text-gray-900 dark:text-white">${{ (sale.totalWithFees || sale.total).toLocaleString() }}</span>
            </div>
          </div>
        </div>

        <!-- Información de Pago (Formato POS) -->
        <div class="border-t-2 border-dashed border-gray-400 dark:border-gray-600 pt-3 mb-4">
          <div class="text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">MÉTODO DE PAGO</div>
          <div v-for="payment in sale.payments" :key="payment.method" class="flex justify-between text-xs mb-2 bg-gray-50 dark:bg-gray-800 p-2 rounded">
            <span class="text-gray-600 dark:text-gray-400">{{ payment.methodName || getPaymentMethodName(payment.method) }}:</span>
            <span class="text-gray-900 dark:text-white font-medium">${{ payment.amount.toLocaleString() }}</span>
          </div>
          <div v-if="sale.change > 0" class="flex justify-between text-xs font-bold border-t border-gray-300 dark:border-gray-600 pt-2 mt-2 bg-green-50 dark:bg-green-900 p-2 rounded">
            <span class="text-gray-900 dark:text-white">Cambio:</span>
            <span class="text-green-600 dark:text-green-400">${{ sale.change.toLocaleString() }}</span>
          </div>
        </div>

        <!-- Mensaje de Agradecimiento (Formato POS) -->
        <div class="text-center border-t-2 border-dashed border-gray-400 dark:border-gray-600 pt-4">
          <p class="text-sm font-bold text-gray-900 dark:text-white mb-2">¡GRACIAS POR SU COMPRA!</p>
          <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Conserve este recibo</p>
          <p v-if="companyInfo.returnPolicy" class="text-xs text-gray-600 dark:text-gray-400 mb-4">{{ companyInfo.returnPolicy }}</p>
          
          <!-- QR Code con número de factura -->
          <div class="flex flex-col items-center mt-4 mb-4">
            <div class="w-20 h-20 bg-white border-2 border-gray-300 p-2 rounded shadow-sm">
              <img v-if="qrCodeDataURL" :src="qrCodeDataURL" alt="QR Factura" class="w-full h-full">
            </div>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 font-medium">{{ sale.invoiceNumber }}</p>
          </div>
          
          <!-- Información Legal - DESPUÉS del QR -->
          <div class="text-xs text-gray-500 dark:text-gray-400 mt-4 space-y-1 border-t border-gray-200 dark:border-gray-700 pt-3">
            <p class="font-medium">Información Legal</p>
            <p>Régimen Común - No responsable de IVA</p>
            <p>Factura de venta Art. 617 del E.T.</p>
            <p>Resolución DIAN 18764069871234</p>
            <p>Vigencia: 01/01/2024 al 31/12/2024</p>
            <p>Numeración: FV-1 al FV-50000</p>
          </div>
          
          <!-- Nombre de la empresa/software -->
          <div class="text-xs text-gray-500 dark:text-gray-400 mt-4 pt-3 border-t border-gray-300 dark:border-gray-600">
            <p class="font-medium">{{ companyInfo.name || '105 POS' }}</p>
            <p>Sistema de facturación</p>
          </div>
        </div>

        <!-- FACTURA OCULTA PARA IMPRESIÓN 58MM -->
        <div id="factura-print-58mm" style="display:none">
          <div style="width: 58mm; font-family: monospace; font-size: 10px; line-height: 1.2;">
            <div style="text-align: center; border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 3px;">
              <div style="font-weight: bold; font-size: 12px;">{{ companyInfo.name.toUpperCase() }}</div>
              <div v-if="companyInfo.address">{{ companyInfo.address }}</div>
              <div v-if="companyInfo.phone">Tel: {{ companyInfo.phone }}</div>
              <div v-if="companyInfo.taxId">{{ companyInfo.taxLabel }}: {{ companyInfo.taxId }}</div>
            </div>
            
            <div style="margin-bottom: 3px;">
              <div>Factura: {{ sale.invoiceNumber }}</div>
              <div>Fecha: {{ formatDateShort(sale.date) }}</div>
              <div v-if="sale.due_date">Vence: {{ formatDateShort(sale.due_date) }}</div>
              <div>Vendedor: {{ sale.cashier || 'Vendedor' }}</div>
              <div>Cliente: {{ sale.customer || 'General' }}</div>
            </div>
            
            <div style="border-top: 1px dashed #000; padding-top: 2px; margin-bottom: 3px;">
              <div style="font-weight: bold;">PRODUCTOS</div>
              <div v-for="item in sale.items" :key="item.id" style="margin-bottom: 2px;">
                <div>{{ item.name }}</div>
                <div style="display: flex; justify-content: space-between;">
                  <span>{{ item.quantity }} x ${{ item.price.toFixed(2) }}</span>
                  <span>${{ (item.quantity * item.price).toFixed(2) }}</span>
                </div>
              </div>
            </div>
            
            <div v-if="sale.appliedDiscount" style="border-top: 1px dashed #000; padding-top: 2px; margin-bottom: 3px;">
              <div>Descuento: {{ sale.appliedDiscount.name }}</div>
              <div style="display: flex; justify-content: space-between;">
                <span>{{ sale.appliedDiscount.name }}:</span>
                <span>-${{ sale.discount.toFixed(2) }}</span>
              </div>
            </div>
            
            <div style="border-top: 1px dashed #000; padding-top: 2px; margin-bottom: 3px;">
              <div style="display: flex; justify-content: space-between;"><span>Subtotal:</span><span>${{ sale.subtotal.toFixed(2) }}</span></div>
              <div v-if="sale.discount > 0" style="display: flex; justify-content: space-between;"><span>Descuento:</span><span>-${{ sale.discount.toFixed(2) }}</span></div>
              <div style="display: flex; justify-content: space-between;"><span>IVA:</span><span>${{ sale.tax.toFixed(2) }}</span></div>
              <div v-if="hasPaymentFees" style="display: flex; justify-content: space-between;"><span>Comision:</span><span>+${{ paymentFeesTotal.toFixed(2) }}</span></div>
              <div style="display: flex; justify-content: space-between; font-weight: bold; border-top: 1px solid #000; padding-top: 2px;">
                <span>TOTAL:</span><span>${{ (sale.totalWithFees || sale.total).toFixed(2) }}</span>
              </div>
            </div>
            
            <div style="border-top: 1px dashed #000; padding-top: 2px; margin-bottom: 3px;">
              <div v-for="payment in sale.payments" :key="payment.method" style="display: flex; justify-content: space-between;">
                <span>{{ payment.methodName || getPaymentMethodName(payment.method) }}:</span><span>${{ payment.amount.toFixed(2) }}</span>
              </div>
              <div v-if="sale.change > 0" style="display: flex; justify-content: space-between; border-top: 1px solid #000; padding-top: 1px;">
                <span>Cambio:</span><span>${{ sale.change.toFixed(2) }}</span>
              </div>
            </div>
            
            <div style="text-align: center; border-top: 1px dashed #000; padding-top: 2px;">
              <div style="font-weight: bold;">¡GRACIAS POR SU COMPRA!</div>
              <div>Conserve este recibo</div>
              <div v-if="companyInfo.returnPolicy">{{ companyInfo.returnPolicy }}</div>
              <div style="margin-top: 3px; font-size: 8px;">{{ sale.invoiceNumber }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer con Acciones - 4 opciones: Imprimir, Descargar PDF, WhatsApp y Nueva Venta -->
      <div class="p-4 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
        <div class="grid grid-cols-3 gap-3 mb-3">
          <button
            @click="printReceipt"
            class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span class="text-sm">Imprimir</span>
          </button>
          <button
            @click="downloadPDF"
            class="flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="text-sm">Descargar PDF</span>
          </button>
          <button
            @click="sendWhatsApp"
            class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21l4-4 4 4M3 20l6-6M15 3l6 6m-6-6v12.5a.5.5 0 01-.5.5H9a.5.5 0 01-.5-.5V7.5a.5.5 0 01.5-.5h5a.5.5 0 01.5.5V9"></path>
            </svg>
            <span class="text-sm">WhatsApp</span>
          </button>
        </div>
        
        <button
          @click="$emit('new-sale')"
          class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
        >
          Nueva Venta
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits, onMounted, watch } from 'vue'
import qrcode from 'qrcode-generator'
import { formatInvoiceDate, formatShortDate, formatColombianTime } from '@/utils/dateFormatter.js'
import { generateInvoicePDF, downloadPDF as downloadPDFHelper } from '../utils/pdfTemplates/pdfGenerator.js'
import { appStore } from '../store/appStore.js'
import { useToast } from '../composables/useToast.js'

// Props
const props = defineProps({
  sale: {
    type: Object,
    required: true
  },
  systemSettings: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['close', 'new-sale', 'send-whatsapp'])

// Métodos de formateo
const formatDate = (date) => {
  return formatInvoiceDate(date)
}

// QR Code
const qrCodeDataURL = ref('')

// Generar código QR con número de factura
const generateQRCode = () => {
  try {
    const qr = qrcode(4, 'M') // Type 4, Error correction M
    qr.addData(props.sale.invoiceNumber || 'FACTURA-SIN-NUMERO')
    qr.make()
    
    // Generar el SVG del QR
    const modules = qr.getModuleCount()
    const cellSize = 2
    const margin = 0
    const size = modules * cellSize + 2 * margin
    
    let svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 ${size} ${size}">`
    svg += `<rect width="${size}" height="${size}" fill="white"/>`
    
    for (let row = 0; row < modules; row++) {
      for (let col = 0; col < modules; col++) {
        if (qr.isDark(row, col)) {
          const x = col * cellSize + margin
          const y = row * cellSize + margin
          svg += `<rect x="${x}" y="${y}" width="${cellSize}" height="${cellSize}" fill="black"/>`
        }
      }
    }
    svg += '</svg>'
    
    // Convertir SVG a data URL
    qrCodeDataURL.value = 'data:image/svg+xml;base64,' + btoa(svg)
  } catch (error) {
    console.error('Error generando QR:', error)
  }
}

// Generar QR al montar el componente
onMounted(() => {
  generateQRCode()
})

// Computed - Información de la empresa
const companyInfo = computed(() => {
  const settings = props.systemSettings
  return {
    name: settings?.company_name || 'Mi Tienda POS',
    address: settings?.company_address || null,
    phone: settings?.company_phone || null,
    taxId: settings?.company_tax_id || null,
    taxLabel: settings?.tax_id_label || 'RUC',
    returnPolicy: settings?.return_policy || 'Devoluciones: 30 días'
  }
})

// Computed - Comisiones de métodos de pago
const paymentFeesTotal = computed(() => {
  return props.sale.payments?.reduce((total, payment) => {
    return total + (payment.fee || 0)
  }, 0) || 0
})

const hasPaymentFees = computed(() => {
  return paymentFeesTotal.value > 0
})

// Métodos auxiliares

const formatDateShort = (date) => {
  const shortDate = formatShortDate(date)
  const time = formatColombianTime(date)
  return `${shortDate} ${time}`
}

const getPaymentMethodName = (method) => {
  const methods = {
    'cash': 'Efectivo',
    'card': 'Tarjeta',
    'transfer': 'Transferencia',
    'check': 'Cheque',
    'qr': 'Código QR',
    'digital': 'Pago Digital'
  }
  return methods[method] || method
}

const { showToast } = useToast()

const downloadPDF = async () => {
  try {
    showToast('Generando PDF...', 'info')

    // Preparar datos de la factura
    const invoiceData = {
      invoice_number: props.sale.invoiceNumber || 'SIN-NUMERO',
      date: props.sale.date || new Date(),
      customer_name: props.sale.customer || 'Cliente General',
      cashier: props.sale.cashier || 'Vendedor',
      items: props.sale.items || [],
      subtotal: parseFloat(props.sale.subtotal || 0),
      discount: parseFloat(props.sale.discount || 0),
      tax: parseFloat(props.sale.tax || 0),
      total: parseFloat(props.sale.total || 0),
      payments: props.sale.payments || [],
      change: parseFloat(props.sale.change || 0),
      notes: props.sale.notes || ''
    }

    // Generar PDF usando plantilla centralizada
    const pdf = await generateInvoicePDF(invoiceData, props.systemSettings || appStore.systemSettings)
    
    // Descargar
    const filename = `factura-${invoiceData.invoice_number}.pdf`
    downloadPDFHelper(pdf, filename)
    
    showToast('PDF descargado correctamente', 'success')
  } catch (error) {
    console.error('Error descargando PDF:', error)
    showToast('Error al descargar el PDF', 'error')
  }
}

const printReceipt = () => {
  try {
    console.log('🖨️ ReceiptModal: Iniciando impresión...')
    
    // Usar el contenido visible del modal
    const receiptContent = document.getElementById('receipt-content')
    if (!receiptContent) {
      console.error('❌ No se encontró receipt-content')
      alert('❌ Error: No se pudo encontrar el contenido de la factura')
      return
    }
    
    console.log('✅ ReceiptModal: Elemento receipt-content encontrado')
    
    const printWindow = window.open('', '_blank', 'width=400,height=600')
    if (!printWindow) {
      alert('❌ Error: No se pudo abrir la ventana de impresión. Verifique el bloqueador de pop-ups.')
      return
    }
    
    printWindow.document.write(`
      <!DOCTYPE html>
      <html>
        <head>
          <title>Factura ${props.sale.invoiceNumber}</title>
          <meta charset="utf-8">
          <style>
            * { box-sizing: border-box; }
            body { 
              margin: 0; 
              padding: 15px; 
              font-family: monospace, 'Courier New'; 
              font-size: 12px;
              line-height: 1.4;
              color: #000;
              background: #fff;
            }
            @media print {
              body { margin: 0; padding: 8px; }
              @page { size: 58mm auto; margin: 0; }
            }
            .border-b { border-bottom: 1px solid #333; }
            .text-center { text-align: center; }
            .font-bold { font-weight: bold; }
            .mb-3 { margin-bottom: 12px; }
            .pb-3 { padding-bottom: 12px; }
            .pt-3 { padding-top: 12px; }
            .text-xs { font-size: 10px; }
            .text-sm { font-size: 11px; }
            img { max-width: 100%; height: auto; }
          </style>
        </head>
        <body>
          ${receiptContent.innerHTML}
        </body>
      </html>
    `)
    
    printWindow.document.close()
    
    printWindow.onload = () => {
      console.log('✅ ReceiptModal: Ventana cargada, imprimiendo...')
      setTimeout(() => {
        printWindow.print()
        printWindow.close()
      }, 300)
    }
    
    // Fallback
    setTimeout(() => {
      if (printWindow && !printWindow.closed) {
        printWindow.print()
        printWindow.close()
      }
    }, 1000)
    
  } catch (error) {
    console.error('❌ ReceiptModal: Error al imprimir:', error)
    alert(`❌ Error al imprimir: ${error.message}`)
  }
}

const sendEmail = () => {
  const companyName = companyInfo.value.name
  const subject = `Factura ${props.sale.invoiceNumber} - ${companyName}`
  const body = `Estimado cliente,\n\nAdjunto encontrará su factura de compra por un total de $${(props.sale.totalWithFees || props.sale.total).toLocaleString()}\n\nGracias por su preferencia.\n\n${companyName}`
  
  const emailUrl = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`
  window.open(emailUrl)
}

const sendWhatsApp = () => {
  console.log('� ReceiptModal sendWhatsApp ejecutado - Emitiendo evento al padre')
  emit('send-whatsapp')
}
</script>

<style scoped>
@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-scale-in {
  animation: scaleIn 0.2s ease-out;
}
</style>