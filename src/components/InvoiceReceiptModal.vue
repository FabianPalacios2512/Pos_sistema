<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="close">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto animate-scale-in" style="width: 400px;" @click.stop>
      
      <!-- Header Simple -->
      <div class="p-4 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Factura POS
          </h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- FACTURA PARA VISUALIZACIÓN -->
      <div class="p-4 dark:bg-gray-800" id="receipt-content">
        <!-- Información del Negocio (Formato POS exacto) -->
        <div class="text-center border-b border-gray-300 dark:border-gray-600 pb-3 mb-3">
          <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ companyInfo.name }}</h2>
          <p v-if="companyInfo.address" class="text-xs text-gray-600 dark:text-gray-300">{{ companyInfo.address }}</p>
          <p v-if="companyInfo.phone" class="text-xs text-gray-600 dark:text-gray-300">Tel: {{ companyInfo.phone }}</p>
          <p v-if="companyInfo.taxId" class="text-xs text-gray-600 dark:text-gray-300">{{ companyInfo.taxLabel }}: {{ companyInfo.taxId }}</p>
        </div>

        <!-- Información de la Venta (Formato POS exacto) -->
        <div class="text-xs mb-3 space-y-1">
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Factura #:</span>
            <span class="font-semibold text-gray-900 dark:text-white">{{ posCompatibleSale.invoiceNumber }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Fecha:</span>
            <span class="text-gray-900 dark:text-white">{{ formatDate(posCompatibleSale.date) }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Vendedor:</span>
            <span class="text-gray-900 dark:text-white">{{ posCompatibleSale.cashier }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Cliente:</span>
            <span class="text-gray-900 dark:text-white">{{ posCompatibleSale.customer }}</span>
          </div>
        </div>

        <!-- Items de la Venta (Formato POS exacto) -->
        <div class="border-t-2 border-gray-300 dark:border-gray-600 pt-3 mb-4">
          <div class="text-xs font-bold text-gray-800 dark:text-gray-200 mb-3 border-b border-dashed border-gray-400 dark:border-gray-600 pb-2">
            PRODUCTOS
          </div>
          
          <div v-for="item in posCompatibleSale.items" :key="item.id" class="mb-3 bg-gray-50 dark:bg-gray-800 p-2 rounded">
            <div class="text-xs">
              <div class="font-medium text-gray-900 dark:text-white mb-1">{{ item.name }}</div>
              <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-gray-400">{{ formatQuantity(item.quantity) }} x ${{ item.price.toLocaleString() }}</span>
                <span class="font-bold text-gray-900 dark:text-white">${{ (item.quantity * item.price).toLocaleString() }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Descuentos aplicados (Formato POS exacto) -->
        <div v-if="posCompatibleSale.appliedDiscount" class="border-t-2 border-dashed border-gray-400 dark:border-gray-600 pt-3 mb-4">
          <div class="text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">DESCUENTO APLICADO</div>
          <div class="flex justify-between text-xs bg-green-50 dark:bg-green-900 p-2 rounded">
            <span class="text-gray-600 dark:text-gray-400">{{ posCompatibleSale.appliedDiscount.name }}</span>
            <span class="text-green-600 dark:text-green-400 font-bold">-${{ posCompatibleSale.discount.toLocaleString() }}</span>
          </div>
        </div>

        <!-- Totales (Formato POS exacto) -->
        <div class="border-t-2 border-dashed border-gray-400 dark:border-gray-600 pt-3 mb-4">
          <div class="space-y-2 text-xs">
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
              <span class="text-gray-900 dark:text-white">${{ posCompatibleSale.subtotal.toLocaleString() }}</span>
            </div>
            <div v-if="posCompatibleSale.discount > 0" class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Descuento:</span>
              <span class="text-red-600 dark:text-red-400">-${{ posCompatibleSale.discount.toLocaleString() }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">IVA ({{ posCompatibleSale.taxRate }}%):</span>
              <span class="text-gray-900 dark:text-white">${{ posCompatibleSale.tax.toLocaleString() }}</span>
            </div>
            <div class="flex justify-between text-sm font-bold border-t-2 border-gray-400 dark:border-gray-600 pt-2 mt-3 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
              <span class="text-gray-900 dark:text-white">TOTAL:</span>
              <span class="text-gray-900 dark:text-white">${{ posCompatibleSale.total.toLocaleString() }}</span>
            </div>
          </div>
        </div>

        <!-- Información de Pago (Formato POS exacto) -->
        <div class="border-t-2 border-dashed border-gray-400 dark:border-gray-600 pt-3 mb-4">
          <div class="text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">MÉTODO DE PAGO</div>
          <div v-for="payment in posCompatibleSale.payments" :key="payment.method" class="flex justify-between text-xs mb-2 bg-gray-50 dark:bg-gray-800 p-2 rounded">
            <span class="text-gray-600 dark:text-gray-400">{{ payment.methodName }}:</span>
            <span class="text-gray-900 dark:text-white font-medium">${{ payment.amount.toLocaleString() }}</span>
          </div>
          <div v-if="posCompatibleSale.change > 0" class="flex justify-between text-xs font-bold border-t border-gray-300 dark:border-gray-600 pt-2 mt-2 bg-green-50 dark:bg-green-900 p-2 rounded">
            <span class="text-gray-900 dark:text-white">Cambio:</span>
            <span class="text-green-600 dark:text-green-400">${{ posCompatibleSale.change.toLocaleString() }}</span>
          </div>
        </div>

        <!-- Mensaje de Agradecimiento (Formato POS exacto) -->
        <div class="text-center border-t-2 border-dashed border-gray-400 dark:border-gray-600 pt-4">
          <p class="text-sm font-bold text-gray-900 dark:text-white mb-2">¡GRACIAS POR SU COMPRA!</p>
          <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Conserve este recibo</p>
          <p v-if="companyInfo.returnPolicy" class="text-xs text-gray-600 dark:text-gray-400 mb-4">{{ companyInfo.returnPolicy }}</p>
          
          <!-- Información Legal -->
          <div class="text-xs text-gray-500 dark:text-gray-400 mt-4 space-y-1 border-t border-gray-200 dark:border-gray-700 pt-3">
            <p class="font-medium">{{ companyInfo.name || '105 POS' }}</p>
            <p>Sistema de facturación</p>
          </div>
        </div>
          <div class="flex flex-col items-center mt-4 mb-4">
            <div class="w-20 h-20 bg-white border-2 border-gray-300 p-2 rounded shadow-sm">
              <img :src="generateQRCode(invoice.invoice_number)" alt="QR Factura" class="w-full h-full">
            </div>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 font-medium">{{ invoice.invoice_number }}</p>
        </div>
      </div>

      <!-- Footer con Acciones - Imprimir, WhatsApp y Nueva Venta -->
      <div class="p-4 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
        <div class="grid grid-cols-2 gap-3 mb-3">
          
          <!-- Botón Imprimir -->
          <button @click="printReceipt" class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span class="text-sm">Imprimir</span>
          </button>

          <!-- Botón WhatsApp -->
          <button @click="sendWhatsApp" :disabled="isWhatsAppLoading" class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
            <svg v-if="!isWhatsAppLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21l4-4 4 4M3 20l6-6M15 3l6 6m-6-6v12.5a.5.5 0 01-.5.5H9a.5.5 0 01-.5-.5V7.5a.5.5 0 01.5-.5h5a.5.5 0 01.5.5V9"></path>
            </svg>
            <div v-else class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm">{{ isWhatsAppLoading ? 'Enviando...' : 'WhatsApp' }}</span>
          </button>
        </div>

        <!-- Botón Nueva Venta -->
        <button @click="newSale" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
          Nueva Venta
        </button>
      </div>
    </div>

    <!-- Modal de Teléfono para WhatsApp -->
    <PhoneInputModal 
      :isOpen="showPhoneModal"
      :customer-phone="invoice.customer_phone"
      @close="showPhoneModal = false"
      @confirm="confirmWhatsAppSend"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import html2canvas from 'html2canvas'
import jsPDF from 'jspdf'
import QRCode from 'qrcode'
import { whatsappService } from '../services/whatsappService'
import PhoneInputModal from './PhoneInputModal.vue'
import { formatInvoiceDate, formatShortDate, formatColombianTime } from '@/utils/dateFormatter.js'

export default {
  name: 'InvoiceReceiptModal',
  components: {
    PhoneInputModal
  },
  props: {
    isOpen: {
      type: Boolean,
      default: false
    },
    invoice: {
      type: Object,
      default: () => ({})
    }
  },
  emits: ['close', 'newSale'],
  setup(props, { emit }) {
    const showPhoneModal = ref(false)
    const isWhatsAppLoading = ref(false)
    
    // Configuración de la tienda desde localStorage
    const storeConfig = ref({
      storeName: localStorage.getItem('storeName') || 'Mi Tienda POS',
      storeAddress: localStorage.getItem('storeAddress') || '',
      storePhone: localStorage.getItem('storePhone') || '',
      storeEmail: localStorage.getItem('storeEmail') || ''
    })

    // 🎯 TRANSFORMAR DATOS AL FORMATO POS EXACTO
    const posCompatibleSale = computed(() => {
      if (!props.invoice) return {}
      
      // Transformar items al formato POS
      const items = parsedItems.value.map(item => ({
        id: item.id,
        name: item.name,
        quantity: item.quantity,
        price: parseFloat(item.price),
        subtotal: parseFloat(item.subtotal)
      }))

      // Crear estructura compatible con POS
      return {
        invoiceNumber: props.invoice.invoice_number,
        date: props.invoice.created_at,
        cashier: props.invoice.seller_name || 'Vendedor',
        customer: props.invoice.customer_name || 'Cliente General',
        items: items,
        subtotal: parseFloat(props.invoice.subtotal || 0),
        discount: parseFloat(props.invoice.discount_amount || 0),
        tax: parseFloat(props.invoice.tax_amount || 0),
        taxRate: 19, // IVA estándar
        total: parseFloat(props.invoice.total || 0),
        totalWithFees: parseFloat(props.invoice.total || 0),
        change: parseFloat(props.invoice.change_given || 0),
        payments: [{
          method: props.invoice.payment_method,
          methodName: getPaymentMethodName(props.invoice.payment_method),
          amount: parseFloat(props.invoice.total || 0)
        }],
        appliedDiscount: props.invoice.discount_amount > 0 ? {
          name: `Descuento ${props.invoice.discount_percentage || 0}%`
        } : null
      }
    })

    // Información de la empresa (formato POS)
    const companyInfo = computed(() => ({
      name: storeConfig.value.storeName || 'Mi Tienda POS',
      address: storeConfig.value.storeAddress,
      phone: storeConfig.value.storePhone,
      taxId: '900123456-7',
      taxLabel: 'NIT',
      returnPolicy: 'Devoluciones hasta 30 días'
    }))

    // Parsear items de la factura
    const parsedItems = computed(() => {
      if (!props.invoice.items) return []
      
      try {
        return typeof props.invoice.items === 'string' 
          ? JSON.parse(props.invoice.items)
          : props.invoice.items
      } catch (error) {
        console.error('Error parsing invoice items:', error)
        return []
      }
    })

    // Formatear números
    const formatNumber = (number) => {
      return new Intl.NumberFormat('es-CO').format(number || 0)
    }

    // Formatear cantidades: mostrar decimales solo si los tiene
    const formatQuantity = (quantity) => {
      if (!quantity) return '0'
      const num = parseFloat(quantity)
      
      // Si tiene decimales, mostrar hasta 2 decimales
      if (num % 1 !== 0) {
        return num.toLocaleString('es-CO', { minimumFractionDigits: 1, maximumFractionDigits: 2 })
      }
      
      // Si es entero, no mostrar decimales
      return num.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
    }

    // Formatear fecha - Ahora usando formatters de Colombia
    const formatDate = (dateString) => {
      return formatInvoiceDate(dateString)
    }

    // Formatear fecha corta para impresión
    const formatDateShort = (dateString) => {
      const shortDate = formatShortDate(dateString)
      const time = formatColombianTime(dateString)
      return `${shortDate} ${time}`
    }

    // Obtener nombre del método de pago
    const getPaymentMethodName = (method) => {
      const methods = {
        'efectivo': 'Efectivo',
        'tarjeta': 'Tarjeta',
        'transferencia': 'Transferencia',
        'credito': 'Crédito'
      }
      return methods[method] || 'Efectivo'
    }

    // Generar QR Code
    const generateQRCode = async (text) => {
      try {
        return await QRCode.toDataURL(text, {
          width: 128,
          margin: 1,
          color: {
            dark: '#000000',
            light: '#FFFFFF'
          }
        })
      } catch (error) {
        console.error('Error generating QR code:', error)
        return ''
      }
    }

    // Cerrar modal
    const close = () => {
      emit('close')
    }

    // Nueva venta
    const newSale = () => {
      emit('newSale')
      close()
    }

    // Imprimir recibo - MEJORADO
    const printReceipt = () => {
      try {
        console.log('🖨️ Intentando imprimir factura...')
        
        const printContent = document.getElementById('receipt-content')
        if (!printContent) {
          console.error('❌ No se encontró el elemento receipt-content')
          alert('❌ Error: No se pudo encontrar el contenido de la factura')
          return
        }
        
        console.log('✅ Elemento receipt-content encontrado')
        
        const printWindow = window.open('', '_blank', 'width=400,height=600')
        if (!printWindow) {
          alert('❌ Error: No se pudo abrir la ventana de impresión. Verifique si tiene bloqueador de pop-ups.')
          return
        }
        
        printWindow.document.write(`
          <!DOCTYPE html>
          <html>
            <head>
              <title>Factura ${props.invoice.invoice_number || 'POS'}</title>
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
              ${printContent.innerHTML}
            </body>
          </html>
        `)
        
        printWindow.document.close()
        
        // Esperar a que el contenido se cargue y luego imprimir automáticamente
        printWindow.onload = () => {
          console.log('✅ Ventana de impresión cargada, iniciando impresión...')
          setTimeout(() => {
            printWindow.print()
            printWindow.close()
            console.log('✅ Impresión enviada correctamente')
          }, 300)
        }
        
        // Fallback si onload no se ejecuta
        setTimeout(() => {
          if (printWindow && !printWindow.closed) {
            printWindow.print()
            printWindow.close()
          }
        }, 1000)
        
      } catch (error) {
        console.error('❌ Error al imprimir:', error)
        alert(`❌ Error al imprimir la factura: ${error.message}`)
      }
    }

    // Enviar por email
    const sendEmail = () => {
      // Implementar funcionalidad de email
      console.log('Enviar por email:', props.invoice)
    }

    // Enviar por WhatsApp - MEJORADO
    const sendWhatsApp = () => {
      console.log('📱 Iniciando envío por WhatsApp...')
      console.log('🔍 Teléfono del cliente:', props.invoice.customer_phone)
      
      // Si la factura ya tiene teléfono, enviar directamente
      if (props.invoice.customer_phone && props.invoice.customer_phone.trim()) {
        console.log('✅ Teléfono encontrado, enviando directamente...')
        confirmWhatsAppSend(props.invoice.customer_phone)
      } else {
        // Si no tiene teléfono, abrir modal para pedirlo
        console.log('⚠️ No hay teléfono, abriendo modal para solicitarlo...')
        showPhoneModal.value = true
      }
    }

    // Confirmar envío por WhatsApp
    const confirmWhatsAppSend = async (phone) => {
      try {
        isWhatsAppLoading.value = true
        showPhoneModal.value = false

        // Validar y formatear el número de teléfono
        let formattedPhone = phone
        
        // Limpiar el número (quitar espacios, guiones, etc.)
        formattedPhone = formattedPhone.replace(/[\s\-\(\)]/g, '')
        
        // Agregar prefijo +57 si no lo tiene
        if (!formattedPhone.startsWith('+57')) {
          if (formattedPhone.startsWith('57')) {
            formattedPhone = '+' + formattedPhone
          } else if (formattedPhone.startsWith('3')) {
            formattedPhone = '+57' + formattedPhone
          } else {
            throw new Error('Formato de número inválido. Debe ser un número colombiano que inicie con 3')
          }
        }
        
        // Validar formato final
        if (!/^\+57[3][0-9]{9}$/.test(formattedPhone)) {
          throw new Error('Formato de número inválido. Use: +573001234567')
        }

        // Usar el método sendInvoiceWithPDF para generar PDF bonito como el POS

        
        // 🎯 USAR EXACTAMENTE EL MISMO MÉTODO DEL POS
        console.log('🔧 Usando método POS original para generar PDF...')
        
        // Hacer visible temporalmente el modal de recibo para captura
        const wasVisible = isWhatsAppLoading.value
        if (!document.getElementById('receipt-content')) {
          throw new Error('Elemento receipt-content no encontrado')
        }

        // Crear canvas del recibo usando html2canvas (MÉTODO POS EXACTO)
        const receiptElement = document.getElementById('receipt-content')
        const canvas = await html2canvas(receiptElement, {
          scale: 2,
          useCORS: true,
          backgroundColor: '#ffffff',
          width: receiptElement.scrollWidth,
          height: receiptElement.scrollHeight,
          logging: false
        })

        // Crear PDF con jsPDF (MÉTODO POS EXACTO)
        const imgWidth = 80 // Ancho del ticket en mm
        const imgHeight = (canvas.height * imgWidth) / canvas.width

        const pdf = new jsPDF({
          orientation: 'portrait',
          unit: 'mm',
          format: [imgWidth, Math.max(imgHeight, 100)]
        })

        const imgData = canvas.toDataURL('image/png', 1.0)
        pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight, '', 'FAST')

        console.log(`📄 PDF generado con método POS: ${imgWidth}x${imgHeight}mm`)

        // Convertir PDF a blob
        const pdfBlob = pdf.output('blob')
        
        // Generar nombre del archivo y mensaje
        const fileName = `factura-${props.invoice.invoice_number}.pdf`
        const customerName = props.invoice.customer_name || 'Cliente'
        const message = `🧾 *FACTURA DE VENTA*\n\nHola ${customerName}, te enviamos tu factura ${props.invoice.invoice_number} por un total de $${formatNumber(props.invoice.total)}.\n\n¡Gracias por tu compra! 😊`

        // Usar el método sendInvoiceWithPDF para enviar PDF bonito
        const result = await whatsappService.sendInvoiceWithPDF(
          formattedPhone,
          pdfBlob,
          message,
          fileName,
          customerName
        )
        
        if (result.success) {
          console.log('✅ Factura enviada por WhatsApp exitosamente')
          
          // Toast personalizado de éxito
          if (window.showToast) {
            window.showToast(
              `✅ Factura ${props.invoice.invoice_number} enviada exitosamente a ${customerName}`,
              'success',
              5000 // 5 segundos
            )
          } else {
            // Fallback si no hay toast
            alert(`✅ Factura ${props.invoice.invoice_number} enviada exitosamente por WhatsApp`)
          }
          
          // Cerrar modal después del envío exitoso
          setTimeout(() => {
            close()
          }, 1500)
        } else {
          throw new Error(result.message || 'Error al enviar mensaje')
        }
        
      } catch (error) {
        console.error('Error al enviar WhatsApp:', error)
        
        const errorMessage = error.response?.data?.message || error.message || 'Error desconocido'
        
        // Toast personalizado de error
        if (window.showToast) {
          window.showToast(
            `❌ Error al enviar factura por WhatsApp: ${errorMessage}`, 
            'error',
            6000 // 6 segundos para errores
          )
        } else {
          // Fallback si no hay toast
          alert(`❌ Error al enviar por WhatsApp: ${errorMessage}`)
        }
      } finally {
        isWhatsAppLoading.value = false
      }
    }

    return {
      storeConfig,
      parsedItems,
      posCompatibleSale,
      companyInfo,
      showPhoneModal,
      isWhatsAppLoading,
      formatNumber,
      formatDate,
      formatDateShort,
      getPaymentMethodName,
      generateQRCode,
      close,
      newSale,
      printReceipt,
      sendEmail,
      sendWhatsApp,
      confirmWhatsAppSend
    }
  }
}
</script>

<style scoped>
@keyframes scale-in {
  0% {
    opacity: 0;
    transform: scale(0.95);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-scale-in {
  animation: scale-in 0.2s ease-out;
}

/* Estilos para el scroll */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}
</style>