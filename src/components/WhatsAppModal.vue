<template>
  <!-- Modal de WhatsApp -->
  <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.515"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">WhatsApp Business</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                <span :class="statusColor" class="inline-flex items-center">
                  <span class="w-2 h-2 rounded-full mr-2" :class="statusDotColor"></span>
                  {{ statusText }}
                </span>
              </p>
            </div>
          </div>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6">
        <!-- Estado Conectado -->
        <div v-if="whatsappStatus.connected" class="text-center">
          <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">¡WhatsApp Conectado!</h4>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            Ya puedes enviar facturas por WhatsApp a tus clientes
          </p>
          <button 
            @click="handleDisconnect"
            :disabled="loading"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors disabled:opacity-50"
          >
            {{ loading ? 'Desconectando...' : 'Desconectar WhatsApp' }}
          </button>
        </div>

        <!-- Estado No Conectado -->
        <div v-else-if="!whatsappStatus.connected" class="text-center">
          <!-- QR Code disponible -->
          <div v-if="qrCode && !whatsappStatus.connected">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Escanea el código QR</h4>
            <div class="bg-white p-4 rounded-lg inline-block mb-4">
              <div id="qr-code" class="flex items-center justify-center min-h-[200px]">
                <!-- El QR se generará aquí -->
              </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
              1. Abre WhatsApp en tu teléfono<br>
              2. Ve a Configuración > Dispositivos vinculados<br>
              3. Toca "Vincular un dispositivo"<br>
              4. Escanea este código QR
            </p>
            <button 
              @click="refreshQR"
              :disabled="loading"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors disabled:opacity-50 mb-2"
            >
              {{ loading ? 'Actualizando...' : 'Actualizar QR' }}
            </button>
          </div>

          <!-- Sin QR Code -->
          <div v-else>
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">WhatsApp No Conectado</h4>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
              Inicia el servicio de WhatsApp para poder enviar facturas
            </p>
            <div class="space-y-3">
              <button 
                @click="handleInitialize"
                :disabled="loading"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors disabled:opacity-50"
              >
                {{ loading ? 'Iniciando...' : 'Iniciar WhatsApp' }}
              </button>
              
              <button 
                @click="handleInitialize(true)"
                :disabled="loading"
                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors disabled:opacity-50 text-sm"
                title="Usar si aparece 'no se puede vincular dispositivo'"
              >
                {{ loading ? 'Limpiando...' : 'Limpiar Sesión e Iniciar' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center mt-4">
          <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-blue-500 hover:bg-blue-400 transition ease-in-out duration-150">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Procesando...
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { whatsappService } from '../services/whatsappService.js'
import QRCode from 'qrcode'

// Props
const props = defineProps({
  showModal: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['close', 'statusChange'])

// Estado reactivo
const loading = ref(false)
const whatsappStatus = ref({ connected: false })
const qrCode = ref('')
const statusCheckInterval = ref(null)

// Computed
const statusText = computed(() => {
  if (whatsappStatus.value.connected) return 'Conectado'
  if (qrCode.value) return 'Esperando conexión'
  return 'No conectado'
})

const statusColor = computed(() => {
  if (whatsappStatus.value.connected) return 'text-green-600 dark:text-green-400'
  if (qrCode.value) return 'text-yellow-600 dark:text-yellow-400'
  return 'text-red-600 dark:text-red-400'
})

const statusDotColor = computed(() => {
  if (whatsappStatus.value.connected) return 'bg-green-500'
  if (qrCode.value) return 'bg-yellow-500'
  return 'bg-red-500'
})

// Métodos
const checkStatus = async () => {
  try {
    const result = await whatsappService.getStatus()
    if (result.success && result.status) {
      whatsappStatus.value = result.status
      emit('statusChange', result.status)
      
      // Si está conectado, limpiar QR
      if (result.status.connected) {
        qrCode.value = ''
      }
    }
  } catch (error) {
    console.error('Error verificando estado:', error)
  }
}

const getQRCode = async () => {
  try {
    const result = await whatsappService.getQRCode()
    if (result.success && result.qr_code) {
      qrCode.value = result.qr_code
      await nextTick()
      await generateQRImage(result.qr_code)
    }
  } catch (error) {
    console.error('Error obteniendo QR:', error)
  }
}

const generateQRImage = async (qrString) => {
  try {
    const qrContainer = document.getElementById('qr-code')
    if (qrContainer) {
      qrContainer.innerHTML = ''
      const canvas = await QRCode.toCanvas(qrString, {
        width: 200,
        margin: 2,
        color: {
          dark: '#000000',
          light: '#FFFFFF'
        }
      })
      qrContainer.appendChild(canvas)
    }
  } catch (error) {
    console.error('Error generando imagen QR:', error)
  }
}

// Función para limpiar sesión automáticamente
const handleCleanSession = async () => {
  try {
    console.log('🧹 Limpiando sesión de WhatsApp...')
    const result = await whatsappService.cleanSession()
    if (result.success) {
      console.log('✅ Sesión limpiada exitosamente')
      // Reiniciar estado
      whatsappStatus.value = { connected: false }
      qrCode.value = ''
    }
    return result.success
  } catch (error) {
    console.error('Error limpiando sesión:', error)
    return false
  }
}

const handleInitialize = async (cleanFirst = false) => {
  loading.value = true
  try {
    // Si se solicita, limpiar sesión primero
    if (cleanFirst) {
      await handleCleanSession()
      // Esperar un momento después de limpiar
      await new Promise(resolve => setTimeout(resolve, 1000))
    }
    
    const result = await whatsappService.initialize()
    if (result.success) {
      // Esperar un momento y luego verificar QR
      setTimeout(async () => {
        await getQRCode()
        startStatusCheck()
      }, 2000)
    }
  } catch (error) {
    console.error('Error iniciando WhatsApp:', error)
  } finally {
    loading.value = false
  }
}

const handleDisconnect = async () => {
  loading.value = true
  try {
    const result = await whatsappService.disconnect()
    if (result.success) {
      whatsappStatus.value = { connected: false }
      qrCode.value = ''
      emit('statusChange', { connected: false })
      stopStatusCheck()
    }
  } catch (error) {
    console.error('Error desconectando WhatsApp:', error)
  } finally {
    loading.value = false
  }
}

const refreshQR = async () => {
  loading.value = true
  try {
    await getQRCode()
  } finally {
    loading.value = false
  }
}

const startStatusCheck = () => {
  if (statusCheckInterval.value) return
  
  statusCheckInterval.value = setInterval(async () => {
    await checkStatus()
    
    // Si no está conectado, intentar obtener QR
    if (!whatsappStatus.value.connected && !qrCode.value) {
      await getQRCode()
    }
  }, 3000)
}

const stopStatusCheck = () => {
  if (statusCheckInterval.value) {
    clearInterval(statusCheckInterval.value)
    statusCheckInterval.value = null
  }
}

// Lifecycle
onMounted(async () => {
  if (props.showModal) {
    await checkStatus()
    if (!whatsappStatus.value.connected) {
      await getQRCode()
    }
    startStatusCheck()
  }
})

onUnmounted(() => {
  stopStatusCheck()
})

// Watchers
import { watch } from 'vue'

watch(() => props.showModal, (newValue) => {
  if (newValue) {
    checkStatus()
    if (!whatsappStatus.value.connected) {
      getQRCode()
    }
    startStatusCheck()
  } else {
    stopStatusCheck()
  }
})
</script>