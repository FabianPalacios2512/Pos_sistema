<template>
  <div class="min-h-screen bg-white relative">
    
    <!-- HEADER -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm" style="box-shadow: 0 1px 0 rgba(0,0,0,0.06);">
      <div class="flex items-center justify-between px-4 h-12">
        <button 
          @click="goBack"
          class="flex items-center gap-1 text-gray-700 active:text-gray-900 transition-colors -ml-1"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
          </svg>
        </button>
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">
          {{ showCheckoutForm ? 'Finalizar Compra' : 'Tu Bolsa' }}
        </h2>
        <div class="w-5"></div>
      </div>
    </header>

    <!-- ========== CART VIEW ========== -->
    <template v-if="!showCheckoutForm">
      <!-- Empty State -->
      <div v-if="cartItems.length === 0" class="flex flex-col items-center justify-center text-center py-20 px-6">
        <div class="w-16 h-16 border border-gray-200 rounded-full flex items-center justify-center mb-4 bg-gray-50">
          <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
          </svg>
        </div>
        <h4 class="text-lg font-medium text-gray-900">Tu bolsa está vacía</h4>
        <p class="text-gray-500 text-sm mt-2 max-w-[250px] mx-auto">Parece que aún no has agregado nada. ¡Descubre nuestros productos!</p>
        <button @click="$router.push('/catalog')" class="mt-8 px-6 py-3 bg-gray-900 text-white text-xs font-bold uppercase tracking-widest rounded-sm hover:bg-black transition-colors active:scale-[0.98]">
          Explorar Catálogo
        </button>
      </div>

      <!-- Cart Items -->
      <div v-else class="pb-36">
        <div class="px-4 py-3 space-y-3">
          <div v-for="item in cartItems" :key="item.id" class="flex gap-4 p-3 bg-white border border-gray-200 rounded-sm relative">
            <!-- Product thumbnail 3:4 -->
            <div class="w-20 aspect-[3/4] flex-shrink-0 bg-gray-50 overflow-hidden rounded-sm border border-gray-100">
              <img 
                v-if="item.image_url" 
                :src="item.image_url" 
                class="w-full h-full object-cover"
                @error="(e) => e.target.style.display = 'none'"
              />
              <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
              </div>
            </div>
            <!-- Info -->
            <div class="flex-1 min-w-0 flex flex-col justify-between py-0.5">
              <div>
                <h4 class="font-semibold text-gray-900 text-sm leading-snug pr-6">{{ item.name }}</h4>
                <p class="text-xs text-gray-500 mt-0.5 uppercase tracking-wide">{{ item.variant_name || 'Unidad' }}</p>
                <p class="font-bold text-gray-900 text-sm mt-1.5">{{ currencySymbol }}{{ formatPrice(item.price) }}</p>
              </div>
              
              <!-- Quantity Controls -->
              <div class="flex items-center justify-between mt-2">
                <div class="flex items-center border border-gray-300 rounded-sm">
                  <button @click="updateItemQuantity(item.id, -1)" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" /></svg>
                  </button>
                  <span class="w-8 text-center text-[13px] font-bold text-gray-900">{{ item.quantity || 1 }}</span>
                  <button @click="updateItemQuantity(item.id, 1)" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Remove absolutely positioned -->
            <button @click="removeItem(item.id)" class="absolute top-2 right-2 p-1.5 text-gray-400 hover:text-red-500 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
            </button>
          </div>
        </div>

        <!-- Sticky Footer -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 pt-3 pb-4 shadow-[0_-2px_10px_rgba(0,0,0,0.03)] z-10">
          <div class="flex justify-between items-center mb-3">
            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Total Estimado</span>
            <span class="text-xl font-bold text-gray-900 tracking-tight">{{ currencySymbol }}{{ formatPrice(cartTotal) }}</span>
          </div>

          <!-- Min order warning -->
          <div v-if="storeConfig && cartTotal < storeConfig.min_order_value" class="mb-3 bg-amber-50 border border-amber-200 rounded-sm p-2.5 flex items-start gap-2">
            <svg class="h-4 w-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <div class="text-[11px] text-amber-800 leading-snug">
              <p class="font-bold mb-0.5 uppercase tracking-wide">Pedido mínimo: {{ currencySymbol }}{{ formatPrice(storeConfig.min_order_value) }}</p>
              <p class="font-medium">Te faltan {{ currencySymbol }}{{ formatPrice(storeConfig.min_order_value - cartTotal) }}</p>
            </div>
          </div>

          <button 
            @click="showCheckoutForm = true"
            :disabled="storeConfig && cartTotal < storeConfig.min_order_value"
            class="w-full bg-gray-900 hover:bg-black disabled:bg-gray-200 disabled:text-gray-400 text-white h-[48px] text-[12px] font-bold uppercase tracking-widest transition-all disabled:cursor-not-allowed rounded-sm flex items-center justify-center gap-2 active:scale-[0.98]"
          >
            INICIAR CHECKOUT
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" /></svg>
          </button>
        </div>
      </div>
    </template>

    <!-- ========== CHECKOUT FORM - Premium Cards ========== -->
    <template v-else>
      <div class="bg-gray-50 min-h-[calc(100vh-48px)] pb-40">

        <!-- Back link -->
        <div class="px-4 py-3">
          <button 
            @click="showCheckoutForm = false" 
            class="flex items-center gap-1 text-gray-500 hover:text-gray-900 text-[13px] font-bold uppercase tracking-wide transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Volver a la bolsa
          </button>
        </div>

        <div class="px-4 space-y-4">
          
          <!-- Card: Método de Entrega -->
          <div class="bg-white rounded-sm border border-gray-200 p-4 space-y-3">
            <div class="flex items-center gap-2 mb-1">
              <div class="w-5 h-5 rounded-sm bg-gray-900 text-white flex items-center justify-center text-[10px] font-bold">1</div>
              <h3 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Entrega</h3>
            </div>

            <!-- Segmented Control Premium -->
            <div class="grid grid-cols-2 gap-2">
              <button 
                @click="formData.delivery_type = 'delivery'"
                type="button"
                class="relative flex flex-col items-center justify-center gap-1.5 p-3 rounded-sm border-2 transition-all duration-200"
                :class="formData.delivery_type === 'delivery' ? 'border-gray-900 bg-gray-50' : 'border-gray-200 hover:border-gray-300'"
              >
                <div v-if="formData.delivery_type === 'delivery'" class="absolute top-1.5 right-1.5 w-3 h-3 bg-gray-900 rounded-sm flex items-center justify-center">
                  <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </div>
                <svg class="w-6 h-6" :class="formData.delivery_type === 'delivery' ? 'text-gray-900' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                <span class="text-xs font-bold" :class="formData.delivery_type === 'delivery' ? 'text-gray-900' : 'text-gray-500'">A Domicilio</span>
              </button>
              <button 
                @click="formData.delivery_type = 'pickup'"
                type="button"
                class="relative flex flex-col items-center justify-center gap-1.5 p-3 rounded-sm border-2 transition-all duration-200"
                :class="formData.delivery_type === 'pickup' ? 'border-gray-900 bg-gray-50' : 'border-gray-200 hover:border-gray-300'"
              >
                <div v-if="formData.delivery_type === 'pickup'" class="absolute top-1.5 right-1.5 w-3 h-3 bg-gray-900 rounded-sm flex items-center justify-center">
                  <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </div>
                <svg class="w-6 h-6" :class="formData.delivery_type === 'pickup' ? 'text-gray-900' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" /></svg>
                <span class="text-xs font-bold" :class="formData.delivery_type === 'pickup' ? 'text-gray-900' : 'text-gray-500'">Recoger Aquí</span>
              </button>
            </div>
            
            <div v-if="formData.delivery_type === 'delivery'" class="pt-1">
              <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Dirección de Entrega <span class="text-red-500">*</span></label>
              <textarea 
                v-model="formData.customer_address"
                required
                rows="2"
                placeholder="Calle, número, barrio, ciudad..."
                class="w-full py-2.5 px-3 bg-white border border-gray-300 rounded-sm text-gray-900 placeholder-gray-400 focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-all resize-none text-sm"
              ></textarea>
            </div>
          </div>

          <!-- Card: Datos de Contacto -->
          <div class="bg-white rounded-sm border border-gray-200 p-4 space-y-3">
            <div class="flex items-center gap-2 mb-1">
              <div class="w-5 h-5 rounded-sm bg-gray-900 text-white flex items-center justify-center text-[10px] font-bold">2</div>
              <h3 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Tus Datos</h3>
            </div>

            <div class="space-y-3">
              <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Cédula / Documento <span class="text-red-500">*</span></label>
                <div class="relative">
                  <input 
                    v-model="formData.customer_document"
                    @blur="searchCustomerByDocument"
                    type="text"
                    required
                    minlength="6"
                    :disabled="searchingCustomer"
                    placeholder="1234567890"
                    class="w-full h-[40px] px-3 bg-white border border-gray-300 rounded-sm text-gray-900 placeholder-gray-400 focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-all disabled:opacity-50 text-sm"
                  />
                  <div v-if="searchingCustomer" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                  </div>
                </div>
              </div>

              <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Nombre Completo <span class="text-red-500">*</span></label>
                <input 
                  v-model="formData.customer_name"
                  type="text"
                  required
                  placeholder="Tu nombre completo"
                  class="w-full h-[40px] px-3 bg-white border border-gray-300 rounded-sm text-gray-900 placeholder-gray-400 focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-all text-sm"
                />
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Teléfono <span class="text-red-500">*</span></label>
                  <input 
                    v-model="formData.customer_phone"
                    type="tel"
                    required
                    placeholder="300 123 4567"
                    class="w-full h-[40px] px-3 bg-white border border-gray-300 rounded-sm text-gray-900 placeholder-gray-400 focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-all text-sm"
                  />
                </div>
                <div>
                  <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Email <span class="text-gray-400 opacity-80">(Opc.)</span></label>
                  <input 
                    v-model="formData.customer_email"
                    type="email"
                    placeholder="correo@email.com"
                    class="w-full h-[40px] px-3 bg-white border border-gray-300 rounded-sm text-gray-900 placeholder-gray-400 focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-all text-sm"
                  />
                </div>
              </div>
              
              <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Notas <span class="text-gray-400 opacity-80">(Opcional)</span></label>
                <input 
                  v-model="formData.note"
                  type="text"
                  placeholder="Indicación especial..."
                  class="w-full h-[40px] px-3 bg-white border border-gray-300 rounded-sm text-gray-900 placeholder-gray-400 focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-all text-sm"
                />
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Sticky Footer - Premium Retail Flat -->
      <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-20">
        <div class="px-4 pt-3 pb-3">
          <div class="flex items-center justify-between mb-3">
            <div>
              <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest leading-none mb-1">Total a Pagar</p>
              <p class="text-[11px] font-semibold text-gray-500">{{ cartCount }} {{ cartCount !== 1 ? 'productos' : 'producto' }} <span v-if="formData.delivery_type === 'delivery'">+ Envío</span></p>
            </div>
            <span class="text-2xl font-bold text-gray-900 tracking-tight">
              {{ currencySymbol }}{{ formatPrice(cartTotal + (formData.delivery_type === 'delivery' ? (storeConfig?.delivery_cost || 0) : 0)) }}
            </span>
          </div>
        </div>
        <div class="px-4 pb-5">
          <button 
            @click="handleCheckoutSubmit"
            :disabled="submittingOrder || !formData.customer_name || !formData.customer_phone || !formData.customer_document || formData.customer_document.length < 6 || (formData.delivery_type === 'delivery' && !formData.customer_address)"
            class="w-full bg-[#1da851] hover:bg-[#199146] disabled:bg-gray-200 disabled:text-gray-400 text-white h-[50px] text-[13px] font-bold uppercase tracking-widest transition-all disabled:cursor-not-allowed flex items-center justify-center gap-2.5 rounded-sm active:scale-[0.98]"
          >
            <svg v-if="!submittingOrder" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
            <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ submittingOrder ? 'PROCESANDO...' : 'CONFIRMAR PEDIDO' }}</span>
          </button>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '../../services/apiClient.js'
import { useCatalogCart } from '../../stores/catalogCart.js'
import { useToast } from '../../composables/useToast.js'

const router = useRouter()
const { cartItems, cartTotal, cartCount, removeItem, updateItemQuantity, clearCart } = useCatalogCart()
const { showError } = useToast()

const storeConfig = ref(null)
const showCheckoutForm = ref(false)
const submittingOrder = ref(false)
const searchingCustomer = ref(false)

const formData = ref({
  customer_name: '',
  customer_phone: '',
  customer_document: '',
  customer_email: '',
  delivery_type: 'delivery',
  customer_address: '',
  note: ''
})

const currencySymbol = computed(() => storeConfig.value?.currency_symbol || '$')

const formatPrice = (price) => {
  return Number(price).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const goBack = () => {
  if (showCheckoutForm.value) {
    showCheckoutForm.value = false
  } else if (window.history.length > 1) {
    router.back()
  } else {
    router.push('/catalog')
  }
}

const loadConfig = async () => {
  try {
    const response = await apiClient.get('/public/catalog/config')
    if (response.data.success && response.data.data) {
      const d = response.data.data
      storeConfig.value = {
        currency_symbol: '$',
        delivery_cost: parseFloat(d.delivery_cost || 0),
        min_order_value: parseFloat(d.minimum_order || 0),
        whatsapp_number: d.whatsapp_number || '',
        store_name: d.store_name || 'Mi Tienda',
        custom_message: d.custom_message || 'Hola, quiero hacer el siguiente pedido:'
      }
    }
  } catch (e) {
    // Use defaults
  }
}

const searchCustomerByDocument = async () => {
  if (!formData.value.customer_document || formData.value.customer_document.length < 6) return
  try {
    searchingCustomer.value = true
    const response = await apiClient.post('/public/customers/find-by-document', {
      document: formData.value.customer_document
    })
    if (response.data.success && response.data.found) {
      formData.value.customer_name = response.data.customer.name
      formData.value.customer_phone = response.data.customer.phone
      formData.value.customer_email = response.data.customer.email || ''
      formData.value.customer_address = response.data.customer.address || ''
    }
  } catch (_) {
    // Manual fill allowed
  } finally {
    searchingCustomer.value = false
  }
}

const handleCheckoutSubmit = async () => {
  if (!storeConfig.value) return
  if (cartTotal.value < storeConfig.value.min_order_value) return

  try {
    submittingOrder.value = true

    const items = cartItems.value.map(item => ({
      product_id: item.id,
      quantity: item.quantity || 1,
      special_instructions: item.special_instructions || null
    }))

    const response = await apiClient.post('/public/orders', {
      ...formData.value,
      items
    })

    if (response.data.success) {
      const order = response.data.order
      const customerData = { ...formData.value }
      const orderItems = [...cartItems.value]

      // Clear cart
      clearCart()

      // Reset form
      formData.value = {
        customer_name: '',
        customer_phone: '',
        customer_document: '',
        customer_email: '',
        delivery_type: 'delivery',
        customer_address: '',
        note: ''
      }
      showCheckoutForm.value = false

      // Build WhatsApp message
      const greeting = storeConfig.value.custom_message || 'Hola, quiero hacer el siguiente pedido:'
      let message = `${greeting}\n\n`
      message += `*Código: ${order.order_number}*\n\n`
      message += `${customerData.customer_name}\n`
      message += `${customerData.customer_phone}\n\n`

      if (customerData.delivery_type === 'delivery') {
        message += `Envío a: ${customerData.customer_address}\n\n`
      } else {
        message += `Recoger en tienda\n\n`
      }

      message += `*Productos:*\n`
      orderItems.forEach((item, index) => {
        message += `${index + 1}. ${item.name} x${item.quantity || 1}\n`
      })

      const deliveryCost = customerData.delivery_type === 'delivery' ? parseFloat(storeConfig.value.delivery_cost || 0) : 0
      const finalTotal = parseFloat(order.total) + deliveryCost
      message += `\nTotal: ${storeConfig.value.currency_symbol}${formatPrice(finalTotal)}`

      if (customerData.note) {
        message += `\n\n${customerData.note}`
      }

      const whatsappUrl = `https://wa.me/${storeConfig.value.whatsapp_number}?text=${encodeURIComponent(message)}`
      window.open(whatsappUrl, '_blank')

      // Navigate back to catalog
      router.push('/catalog')
    }
  } catch (e) {
    showError('Error al crear el pedido. Por favor intenta nuevamente.')
  } finally {
    submittingOrder.value = false
  }
}

onMounted(() => {
  loadConfig()
})
</script>
