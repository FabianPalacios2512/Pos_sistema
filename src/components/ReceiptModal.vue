<template>
  <div class="fixed inset-0 bg-black/50  flex items-center justify-center z-50 p-4">
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
          
          <div v-for="item in normalizedSale.items" :key="item.id" class="mb-3 bg-gray-50 dark:bg-gray-800 p-2 rounded">
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
            <span class="text-green-600 dark:text-green-400 font-bold">-${{ normalizedSale.discount.toLocaleString() }}</span>
          </div>
        </div>

        <!-- Totales (Formato POS) -->
        <div class="border-t-2 border-dashed border-gray-400 dark:border-gray-600 pt-3 mb-4">
          <div class="space-y-2 text-xs">
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
              <span class="text-gray-900 dark:text-white">${{ normalizedSale.subtotal.toLocaleString() }}</span>
            </div>
            <div v-if="normalizedSale.discount > 0" class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Descuento:</span>
              <span class="text-red-600 dark:text-red-400">-${{ normalizedSale.discount.toLocaleString() }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">IVA ({{ sale.taxRate }}%):</span>
              <span class="text-gray-900 dark:text-white">${{ normalizedSale.tax.toLocaleString() }}</span>
            </div>
            <!-- Mostrar comisión si aplica -->
            <div v-if="hasPaymentFees" class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Comisión:</span>
              <span class="text-orange-600 dark:text-orange-400">+${{ paymentFeesTotal.toLocaleString() }}</span>
            </div>
            <div class="flex justify-between text-sm font-bold border-t-2 border-gray-400 dark:border-gray-600 pt-2 mt-3 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
              <span class="text-gray-900 dark:text-white">TOTAL:</span>
              <span class="text-gray-900 dark:text-white">${{ (normalizedSale.totalWithFees || normalizedSale.total).toLocaleString() }}</span>
            </div>
          </div>
        </div>

        <!-- Información de Pago (Formato POS) -->
        <div class="border-t-2 border-dashed border-gray-400 dark:border-gray-600 pt-3 mb-4">
          <div class="text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">MÉTODO DE PAGO</div>
          <div v-for="payment in normalizedSale.payments" :key="payment.method" class="flex justify-between text-xs mb-2 bg-gray-50 dark:bg-gray-800 p-2 rounded">
            <span class="text-gray-600 dark:text-gray-400">{{ payment.methodName || getPaymentMethodName(payment.method) }}:</span>
            <span class="text-gray-900 dark:text-white font-medium">${{ payment.amount.toLocaleString() }}</span>
          </div>
          <div v-if="normalizedSale.change > 0" class="flex justify-between text-xs font-bold border-t border-gray-300 dark:border-gray-600 pt-2 mt-2 bg-green-50 dark:bg-green-900 p-2 rounded">
            <span class="text-gray-900 dark:text-white">Cambio:</span>
            <span class="text-green-600 dark:text-green-400">${{ normalizedSale.change.toLocaleString() }}</span>
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
              <div v-for="item in normalizedSale.items" :key="item.id" style="margin-bottom: 2px;">
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
                <span>-${{ normalizedSale.discount.toFixed(2) }}</span>
              </div>
            </div>
            
            <div style="border-top: 1px dashed #000; padding-top: 2px; margin-bottom: 3px;">
              <div style="display: flex; justify-content: space-between;"><span>Subtotal:</span><span>${{ normalizedSale.subtotal.toFixed(2) }}</span></div>
              <div v-if="normalizedSale.discount > 0" style="display: flex; justify-content: space-between;"><span>Descuento:</span><span>-${{ normalizedSale.discount.toFixed(2) }}</span></div>
              <div style="display: flex; justify-content: space-between;"><span>IVA:</span><span>${{ normalizedSale.tax.toFixed(2) }}</span></div>
              <div v-if="hasPaymentFees" style="display: flex; justify-content: space-between;"><span>Comision:</span><span>+${{ paymentFeesTotal.toFixed(2) }}</span></div>
              <div style="display: flex; justify-content: space-between; font-weight: bold; border-top: 1px solid #000; padding-top: 2px;">
                <span>TOTAL:</span><span>${{ (normalizedSale.totalWithFees || normalizedSale.total).toFixed(2) }}</span>
              </div>
            </div>
            
            <div style="border-top: 1px dashed #000; padding-top: 2px; margin-bottom: 3px;">
              <div v-for="payment in normalizedSale.payments" :key="payment.method" style="display: flex; justify-content: space-between;">
                <span>{{ payment.methodName || getPaymentMethodName(payment.method) }}:</span><span>${{ payment.amount.toFixed(2) }}</span>
              </div>
              <div v-if="normalizedSale.change > 0" style="display: flex; justify-content: space-between; border-top: 1px solid #000; padding-top: 1px;">
                <span>Cambio:</span><span>${{ normalizedSale.change.toFixed(2) }}</span>
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

      <!-- Footer con Acciones - 4 opciones: Imprimir, Descargar PDF, WhatsApp y Email -->
      <div class="p-4 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
        <div class="grid grid-cols-2 gap-3 mb-3">
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
            <span class="text-sm">Descargar</span>
          </button>
          <button
            @click="sendWhatsApp"
            class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
            <span class="text-sm">WhatsApp</span>
          </button>
          <button
            @click="sendEmail"
            class="flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <span class="text-sm">Email</span>
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
    
    <!-- Modal de Función Premium -->
    <Teleport to="body">
      <div v-if="showPremiumModal" class="fixed inset-0 bg-black/70  flex items-center justify-center z-[60] p-4 animate-fade-in">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-zinc-800 animate-scale-in">
          
          <!-- Contenido -->
          <div class="p-8 text-center">
            <!-- Icono Premium -->
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
              <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
              </svg>
            </div>

            <!-- Título -->
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">¡Mejora tu Plan!</h3>
            
            <!-- Mensaje -->
            <p class="text-base text-gray-600 dark:text-zinc-400 mb-6 leading-relaxed">
              <span class="font-semibold text-blue-600 dark:text-blue-400">{{ premiumFeatureName }}</span> está disponible en nuestros planes premium.
            </p>
            
            <p class="text-sm text-gray-500 dark:text-zinc-500 mb-8">
              💡 Desbloquea todas las funciones premium para potenciar tu negocio
            </p>

            <!-- Botones -->
            <div class="flex gap-3">
              <button
                @click="closePremiumModal"
                class="flex-1 py-3 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-base font-semibold rounded-xl border border-gray-300 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors"
              >
                Cerrar
              </button>
              <button
                @click="goToPlans"
                class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-base font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl"
              >
                Ver Planes
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits, onMounted, watch } from 'vue'
import { useModuleNavigation } from '../composables/useModuleNavigation.js'
import qrcode from 'qrcode-generator'
import { formatInvoiceDate, formatShortDate, formatColombianTime } from '@/utils/dateFormatter.js'
import { generateInvoicePDF, downloadPDF as downloadPDFHelper } from '../utils/pdfTemplates/pdfGenerator.js'
import { appStore } from '../store/appStore.js'
import { useToast } from '../composables/useToast.js'

const { navigateToModule } = useModuleNavigation()

// Props
const props = defineProps({
  sale: {
    type: Object,
    required: true
  },
  systemSettings: {
    type: Object,
    default: null
  },
  userPlan: {
    type: String,
    default: 'free_trial'
  }
})

// Emits
const emit = defineEmits(['close', 'new-sale', 'send-whatsapp', 'send-email'])

// Estado
const showPremiumModal = ref(false)
const premiumFeatureName = ref('')

// Verificar si el usuario tiene plan básico (free_trial, free, basic)
const isBasicPlan = () => {
  const plan = props.userPlan.toLowerCase()
  return plan === 'free_trial' || plan === 'free' || plan === 'basic'
}

// Mostrar modal premium
const showPremiumFeature = (featureName) => {
  premiumFeatureName.value = featureName
  showPremiumModal.value = true
}

// Cerrar modal premium
const closePremiumModal = () => {
  showPremiumModal.value = false
}

const goToPlans = () => {
  showPremiumModal.value = false
  emit('close') // Cerrar el modal actual
  navigateToModule('settings', { section: 'plans' })
}

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

// Computed - Normalizar items (convertir strings a números)
const normalizedSale = computed(() => {
  return {
    ...props.sale,
    items: (props.sale.items || []).map(item => ({
      ...item,
      price: parseFloat(item.price) || 0,
      quantity: parseFloat(item.quantity) || 0
    })),
    payments: (props.sale.payments || []).map(payment => ({
      ...payment,
      amount: parseFloat(payment.amount) || 0,
      fee: parseFloat(payment.fee) || 0
    })),
    subtotal: parseFloat(props.sale.subtotal) || 0,
    discount: parseFloat(props.sale.discount) || 0,
    tax: parseFloat(props.sale.tax) || 0,
    total: parseFloat(props.sale.total) || 0,
    totalWithFees: parseFloat(props.sale.totalWithFees) || 0,
    change: parseFloat(props.sale.change) || 0
  }
})

// Computed - Comisiones de métodos de pago
const paymentFeesTotal = computed(() => {
  return normalizedSale.value.payments?.reduce((total, payment) => {
    return total + (parseFloat(payment.fee) || 0)
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

const printReceipt = async () => {
  try {
    console.log('🖨️ ReceiptModal: Iniciando impresión...')
    
    // Preparar datos de la factura (igual que downloadPDF)
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
    const pdfBlob = pdf.output('blob')
    const pdfUrl = URL.createObjectURL(pdfBlob)
    
    // Abrir ventana nueva y activar impresión rápida
    const printWindow = window.open(pdfUrl, '_blank')
    if (printWindow) {
      printWindow.onload = () => {
        setTimeout(() => {
          printWindow.print()
        }, 250)
      }
    }
    
    console.log('✅ ReceiptModal: PDF generado y ventana abierta')
    
  } catch (error) {
    console.error('❌ Error al imprimir factura:', error)
    alert('❌ Error al imprimir la factura')
  }
}

const sendEmail = () => {
  if (isBasicPlan()) {
    showPremiumFeature('Envío por Email')
    return
  }
  
  // Emitir evento al padre para que maneje el envío
  emit('send-email')
}

const sendWhatsApp = () => {
  if (isBasicPlan()) {
    showPremiumFeature('Envío por WhatsApp')
    return
  }
  
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