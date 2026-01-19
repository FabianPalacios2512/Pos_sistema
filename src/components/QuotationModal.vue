<template>
  <div v-if="show" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] flex flex-col border border-gray-200 dark:border-gray-700">
      <!-- Header -->
      <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-t-lg border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Cotización</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">Resumen de la cotización generada</p>
            </div>
          </div>
          <button @click="closeModal" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all min-w-[40px] min-h-[40px] flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Contenido con scroll -->
      <div class="p-4 space-y-4 overflow-y-auto flex-1 min-h-0">
        
        <div v-if="type === 'availability_issues'" class="space-y-3">
          
          <div class="text-center bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-center gap-2 mb-1">
              <span class="text-xs font-medium text-blue-600 dark:text-blue-400">Cotización</span>
              <span class="font-mono text-sm font-bold text-blue-900 dark:text-blue-300">{{ quotationCode }}</span>
            </div>
            <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">{{ message }}</p>
          </div>
          
          <div v-if="insufficientStockItems.length > 0" class="space-y-2">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path></svg>
              Stock Insuficiente
            </h4>
            <div v-for="item in insufficientStockItems" :key="item.name" class="bg-white dark:bg-gray-700 rounded-lg p-3 border-l-4 border-orange-400">
              <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ item.name }}</h4>
              <p class="text-xs text-orange-600 dark:text-orange-400 font-medium">{{ item.message }}</p>
              <div class="flex gap-3 text-xs text-gray-600 dark:text-gray-400 mt-1">
                <span>Solicitado: <strong>{{ item.quantity }}</strong></span>
                <span v-if="item.availableQuantity !== undefined">
                  Disponible: <strong class="text-green-600 dark:text-green-400">{{ item.availableQuantity }}</strong>
                </span>
              </div>
            </div>
          </div>
          
          <div v-if="notFoundItems.length > 0" class="space-y-2">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              Productos No Encontrados
            </h4>
            <div v-for="item in notFoundItems" :key="item.name" class="bg-white dark:bg-gray-700 rounded-lg p-3 border-l-4 border-red-500">
              <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ item.name }}</h4>
              <p class="text-xs text-red-600 dark:text-red-400 font-medium">{{ item.message }}</p>
              <div class="flex gap-3 text-xs text-gray-600 dark:text-gray-400 mt-1">
                <span>Solicitado: <strong>{{ item.quantity }}</strong></span>
              </div>
            </div>
          </div>

          <div v-if="hasValidItems" class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 border border-green-200 dark:border-green-800">
            <div class="flex items-center gap-2 mb-1">
              <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <h4 class="text-sm font-semibold text-green-900 dark:text-green-200">Productos Disponibles</h4>
            </div>
            <p class="text-xs text-green-700 dark:text-green-400">
              Se cargarán {{ validItems.length }} producto(s) con las cantidades disponibles.
            </p>
          </div>
        </div>
        <div v-else>
          <!-- ADVERTENCIA: Productos no cargados o con stock insuficiente -->
          <div v-if="unavailableItems && unavailableItems.length > 0" class="mb-3">
            <div class="bg-orange-50 dark:bg-orange-900/30 border-l-4 border-orange-500 rounded-lg p-3 flex items-start gap-2">
              <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
              <div>
                <div class="text-sm font-bold text-orange-700 dark:text-orange-300">Algunos productos tienen problemas de stock</div>
                <div class="text-xs text-orange-600 dark:text-orange-400">Solo se cargarán los productos con stock suficiente.</div>
              </div>
            </div>
            <ul class="space-y-2 mt-2">
              <li v-for="item in unavailableItems" :key="item.name" class="bg-white dark:bg-gray-700 rounded-lg p-2 border border-orange-200 dark:border-orange-800 flex justify-between items-center">
                <div class="flex flex-col gap-0.5">
                  <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.name }}</span>
                  <span v-if="item.issue === 'insufficient_stock'" class="text-xs text-orange-600 dark:text-orange-400">
                    Stock insuficiente: solicitado {{ item.quantity }}, disponible {{ item.availableQuantity }}.
                  </span>
                  <span v-else class="text-xs text-red-600 dark:text-red-400">Producto no encontrado en inventario</span>
                </div>
                
                <!-- Botón para agregar con stock disponible -->
                <button v-if="item.issue === 'insufficient_stock' && item.availableQuantity > 0"
                        @click="addPartialStock(item)"
                        class="px-2 py-1 bg-orange-500 text-white text-xs rounded hover:bg-orange-600 transition-colors"
                        :title="`Agregar ${item.availableQuantity} unidad(es) disponible(s)`">
                  Agregar {{ item.availableQuantity }}
                </button>
              </li>
            </ul>
          </div>
          <!-- Resumen principal -->
          <div class="rounded-lg p-4 border border-blue-200 dark:border-blue-800" style="background-color: #EBF2FF;">
            <div class="grid grid-cols-3 gap-3 mb-3">
              <div>
                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Código de Cotización</span>
                <div class="flex items-center gap-1 mt-0.5">
                  <span class="font-mono text-sm font-bold text-blue-900 dark:text-blue-200">{{ quotationCode }}</span>
                  <button @click="copyCode" class="p-0.5 text-blue-600 hover:text-blue-800 transition-colors" title="Copiar código">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                  </button>
                </div>
              </div>
              
              <!-- QR Code -->
              <div class="flex flex-col items-center">
                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium mb-1">Escanear para Cargar</span>
                <div class="bg-white p-1.5 rounded border">
                  <canvas ref="qrCanvas" class="w-12 h-12"></canvas>
                </div>
              </div>
              
              <div class="text-right">
                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Cliente</span>
                <p class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">{{ getCustomerName }}</p>
              </div>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-blue-200 dark:border-blue-700">
              <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Total</span>
              <span class="text-xl font-bold text-green-600 dark:text-green-400">${{ formatCurrency((quotationData && quotationData.total) ? quotationData.total : 0) }}</span>
            </div>
            
            <!-- Instrucciones del QR -->
            <div class="text-center mt-2">
              <p class="text-xs text-gray-600 dark:text-gray-400">
                📱 Escanea el código QR para cargar automáticamente esta cotización en el POS
              </p>
            </div>
          </div>

          <!-- Mensaje -->
          <div v-if="message" class="text-center mt-3">
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ message }}</p>
          </div>

          <!-- Lista de productos -->
          <div v-if="quotationData && quotationData.items && quotationData.items.length > 0" class="mt-3">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Productos Cotizados</h4>
            <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-700">
              <table class="min-w-full text-xs">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                  <tr>
                    <th class="px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Producto</th>
                    <th class="px-3 py-2 text-center font-medium text-gray-700 dark:text-gray-300">Cantidad</th>
                    <th class="px-3 py-2 text-center font-medium text-gray-700 dark:text-gray-300">Precio</th>
                    <th class="px-3 py-2 text-right font-medium text-gray-700 dark:text-gray-300">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in quotationData.items" :key="item.product_name || item.name" class="border-t border-gray-100 dark:border-gray-600">
                    <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ item.product_name || item.name }}</td>
                    <td class="px-3 py-2 text-center text-gray-700 dark:text-gray-300">{{ item.quantity }}</td>
                    <td class="px-3 py-2 text-center text-gray-700 dark:text-gray-300">${{ formatCurrency(item.price) }}</td>
                    <td class="px-3 py-2 text-right text-green-700 dark:text-green-400 font-semibold">${{ formatCurrency(item.subtotal) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        </div>

        <!-- Botones fijos en el footer -->
        <div class="flex gap-2 p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-b-lg flex-shrink-0">
          
          <button v-if="type === 'found'" 
            @click="loadQuotation" 
            class="flex-1 bg-green-600 text-white px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-green-700 transition-colors">
            <div class="flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path></svg>
              <span>Cargar al Carrito</span>
            </div>
          </button>

          <button v-if="type === 'loaded'" 
            @click="printQuotation" 
            class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors">
            <div class="flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
              <span>Imprimir</span>
            </div>
          </button>
          
          <button v-if="type === 'success'" 
            @click="printQuotation" 
            class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors">
            <div class="flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
              <span>Imprimir</span>
            </div>
          </button>

          <button v-if="type === 'success'" 
            @click="sendWhatsApp" 
            class="flex-1 bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2.5 rounded-lg text-sm font-bold hover:from-green-600 hover:to-green-700 transition-colors duration-200">
            <div class="flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.594z"/>
              </svg>
              <span>WhatsApp</span>
            </div>
          </button>

          <button v-if="type === 'success'" 
            @click="sendEmail" 
            class="flex-1 bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-bold hover:from-purple-600 hover:to-indigo-700 transition-colors duration-200">
            <div class="flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              <span>Email</span>
            </div>
          </button>

          <button v-if="type === 'availability_issues'" 
                  @click="loadAvailableOnly" 
                  :disabled="!hasValidItems"
                  :class="[
                    'flex-1 px-4 py-2.5 rounded-lg text-sm font-bold transition-colors',
                    hasValidItems 
                      ? 'bg-green-600 text-white hover:bg-green-700'
                      : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                  ]">
            <div class="flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path></svg>
              <span>{{ hasValidItems ? 'Cargar disponibles' : 'Sin Stock' }}</span>
            </div>
          </button>
          
          <button @click="closeModal" 
            class="px-4 py-2.5 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-bold hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
            Cancelar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Premium -->
    <Teleport to="body">
      <div v-if="showPremiumModal" 
           class="fixed inset-0 bg-black/60  flex items-center justify-center z-[9999] p-4 animate-fade-in"
           @click.self="showPremiumModal = false">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-slate-800 dark:to-slate-900 rounded-2xl shadow-2xl max-w-md w-full p-8 animate-scale-in border-2 border-blue-200 dark:border-indigo-700">
          
          <!-- Icono estrella premium -->
          <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
              <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
              </svg>
            </div>
          </div>

          <!-- Mensaje -->
          <h3 class="text-2xl font-bold text-center mb-3 text-gray-900 dark:text-white">
            ¡Mejora tu Plan!
          </h3>
          
          <p class="text-center text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
            La función <span class="font-bold text-blue-600 dark:text-blue-400">{{ premiumFeatureName }}</span> está disponible en nuestros planes <span class="font-bold">Premium</span> y <span class="font-bold">Empresarial</span>.
          </p>

          <div class="bg-white/50 dark:bg-slate-950/50 rounded-xl p-4 mb-6 border border-blue-200 dark:border-indigo-800">
            <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
              💡 Desbloquea todas las funciones premium para potenciar tu negocio
            </p>
          </div>

          <!-- Botones -->
          <div class="flex gap-3">
            <button @click="showPremiumModal = false" 
                    class="flex-1 px-4 py-3 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors duration-200 border border-gray-300 dark:border-slate-600">
              Cerrar
            </button>
            <button @click="goToPlans" 
                    class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-bold hover:from-blue-600 hover:to-indigo-700 transition-colors duration-200 shadow-lg">
              Ver Planes
            </button>
          </div>
        </div>
      </div>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import { useModuleNavigation } from '../composables/useModuleNavigation.js'
import QRCode from 'qrcode'

const { navigateToModule } = useModuleNavigation()

// Referencias
const qrCanvas = ref(null)

// Props (SIN CAMBIOS)
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  type: {
    type: String, // 'success' | 'found' | 'loaded' | 'availability_issues'
    default: 'success'
  },
  quotationCode: {
    type: String,
    default: ''
  },
  quotationData: {
    type: Object,
    default: null
  },
  message: {
    type: String,
    default: ''
  },
  unavailableItems: {
    type: Array,
    default: () => []
  },
  validItems: {
    type: Array,
    default: () => []
  },
  userPlan: {
    type: String,
    default: 'free_trial'
  }
})

// Emits
const emit = defineEmits(['close', 'load', 'print', 'load-available', 'add-partial-stock', 'send-whatsapp', 'send-email'])

// Estado local para mostrar confirmación de carga
const productsLoaded = ref(false)
const showPremiumModal = ref(false)
const premiumFeatureName = ref('')

// Función para verificar si el usuario tiene un plan básico
const isBasicPlan = () => {
  const plan = props.userPlan.toLowerCase()
  return plan === 'free_trial' || plan === 'free' || plan === 'basic'
}

// Computed Properties
const hasValidItems = computed(() => {
  return props.validItems && props.validItems.length > 0
})

// Computed para obtener el nombre del cliente de manera segura
const getCustomerName = computed(() => {
  if (!props.quotationData) return 'Cliente General'
  
  const customer = props.quotationData.customer
  
  // Si customer es un string, devolverlo directamente
  if (typeof customer === 'string') {
    return customer
  }
  
  // Si customer es un objeto, extraer el nombre
  if (typeof customer === 'object' && customer !== null) {
    return customer.name || customer.customer_name || 'Cliente General'
  } 
  
  // Si no hay customer o es nulo
  return 'Cliente General'
})

// ==============================================================
// INICIO: Propiedades Computadas AÑADIDAS
// ==============================================================
// Estas dos propiedades nos ayudan a separar la lista de 'unavailableItems'
// en dos listas visuales, lo cual es mucho más claro para el usuario.
// Asumo que los 'issue' types son 'insufficient_stock' y 'not_found'
// basado en tu código original.

/** Filtra solo los items que tienen stock insuficiente. */
const insufficientStockItems = computed(() => {
  return props.unavailableItems.filter(item => item.issue === 'insufficient_stock')
})

/** Filtra solo los items que no se encontraron en la base de datos. */
const notFoundItems = computed(() => {
  return props.unavailableItems.filter(item => item.issue === 'not_found')
})
// ==============================================================
// FIN: Propiedades Computadas AÑADIDAS
// ==============================================================


const getModalTitle = computed(() => {
  switch (props.type) {
    case 'availability_issues':
      return 'Problemas al Cargar Cotización' // Título mejorado
    case 'create':
      return 'Crear Cotización'
    case 'confirm':
      return 'Confirmar Acción'
    default:
      return 'Cotización'
  }
})

// Se elimina getModalSubtitle ya que era redundante con el título.
// const getModalSubtitle = computed(() => { ... })

// Methods (SIN CAMBIOS)
const closeModal = () => {
  emit('close')
}

const loadQuotation = () => {
  emit('load')
}

const printQuotation = () => {
  emit('print')
}

const sendWhatsApp = () => {
  if (isBasicPlan()) {
    premiumFeatureName.value = 'Envío por WhatsApp'
    showPremiumModal.value = true
    return
  }
  emit('send-whatsapp')
}

const sendEmail = () => {
  if (isBasicPlan()) {
    premiumFeatureName.value = 'Envío por Email'
    showPremiumModal.value = true
    return
  }
  emit('send-email')
}

const goToPlans = () => {
  showPremiumModal.value = false
  emit('close') // Cerrar el modal actual
  navigateToModule('settings', { section: 'plans' })
}

const loadAvailableOnly = () => {
  productsLoaded.value = true
  emit('load-available')
}

const addPartialStock = (item) => {
  emit('add-partial-stock', item)
}

const copyCode = async () => {
  try {
    await navigator.clipboard.writeText(props.quotationCode)
    // Aquí podrías agregar una notificación toast
  } catch (error) {
    console.error('Error copiando código:', error)
  }
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-CO').format(amount)
}

// Función para generar QR Code
const generateQRCode = async () => {
  if (!qrCanvas.value || !props.quotationCode) return
  
  try {
    // El QR contendrá el código de cotización para escaneo directo
    await QRCode.toCanvas(qrCanvas.value, props.quotationCode, {
      width: 64,
      height: 64,
      margin: 1,
      color: {
        dark: '#1e3a8a',  // Azul oscuro
        light: '#ffffff'  // Blanco
      }
    })
  } catch (error) {
    console.error('Error generando QR:', error)
  }
}

// Watch para regenerar QR cuando cambie el código
watch(() => props.quotationCode, () => {
  nextTick(generateQRCode)
})

// Watch para regenerar QR cuando el modal se muestre
watch(() => props.show, (newShow) => {
  if (newShow) {
    nextTick(generateQRCode)
  }
})

// Generar QR al montar
onMounted(() => {
  if (props.show && props.quotationCode) {
    nextTick(generateQRCode)
  }
})
</script>