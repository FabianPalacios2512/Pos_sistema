<template>
  <div class="min-h-screen font-sans bg-[#EEF2F6] pb-12">

    <div class="relative z-10 container mx-auto px-4 py-8 max-w-7xl">
      
      <!-- Header con tu estilo -->
      <div class="bg-white/50 backdrop-blur-sm rounded-[24px] border border-white shadow-sm p-6 mb-8">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                Configuración de {{ config.storeName || 'Tu Tienda' }}
              </h1>
              <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mt-1">
                Personaliza tu sistema en 3 pasos sencillos
              </p>
            </div>
          </div>

          <!-- Stepper horizontal -->
          <div class="hidden md:flex items-center space-x-4">
            <div class="flex items-center">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all duration-300"
                   :class="currentStep >= 1 ? 'bg-slate-900 text-white shadow-lg' : 'bg-white border border-slate-200 text-slate-400'">
                <span v-if="currentStep > 1">✓</span>
                <span v-else>1</span>
              </div>
              <span class="ml-2 text-xs font-bold uppercase tracking-wide" :class="currentStep >= 1 ? 'text-slate-900' : 'text-slate-400'">Diseño</span>
            </div>
            <div class="w-12 h-0.5 rounded-full transition-all duration-300" :class="currentStep >= 2 ? 'bg-slate-900' : 'bg-slate-200'"></div>
            
            <div class="flex items-center">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all duration-300"
                   :class="currentStep >= 2 ? 'bg-slate-900 text-white shadow-lg' : 'bg-white border border-slate-200 text-slate-400'">
                <span v-if="currentStep > 2">✓</span>
                <span v-else>2</span>
              </div>
              <span class="ml-2 text-xs font-bold uppercase tracking-wide" :class="currentStep >= 2 ? 'text-slate-900' : 'text-slate-400'">Datos</span>
            </div>
            <div class="w-12 h-0.5 rounded-full transition-all duration-300" :class="currentStep >= 3 ? 'bg-slate-900' : 'bg-slate-200'"></div>

            <div class="flex items-center">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all duration-300"
                   :class="currentStep >= 3 ? 'bg-slate-900 text-white shadow-lg' : 'bg-white border border-slate-200 text-slate-400'">
                3
              </div>
              <span class="ml-2 text-xs font-bold uppercase tracking-wide" :class="currentStep >= 3 ? 'text-slate-900' : 'text-slate-400'">WhatsApp</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Transiciones suaves entre pasos -->
      <Transition name="fade-slide" mode="out-in">
        
        <div v-if="currentStep === 1" key="step1" class="space-y-6">
          <div class="bg-white/50 backdrop-blur-sm rounded-[24px] border border-white shadow-sm p-6 mb-6">
            <h2 class="text-xl font-black text-slate-900 tracking-tight mb-2">Elige tu Plantilla de Facturación</h2>
            <p class="text-sm font-semibold text-slate-600">
              Selecciona el diseño que mejor represente tu marca. Podrás cambiarlo después en configuración.
            </p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
            
            <div @click="selectedTemplate = 'classic'" 
                 class="group relative bg-white rounded-[24px] overflow-hidden cursor-pointer transition-all duration-300 hover:shadow-xl"
                 :class="selectedTemplate === 'classic' ? 'ring-4 ring-slate-900 ring-offset-4 ring-offset-[#EEF2F6] shadow-xl' : 'border border-white shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)]'">
              
              <div v-if="selectedTemplate === 'classic'" class="absolute top-4 right-4 z-20 animate-scale-in">
                <div class="w-9 h-9 rounded-xl bg-slate-900 flex items-center justify-center shadow-lg">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
              </div>
              
              <div class="invoice-preview-wrapper bg-slate-50 border-b border-slate-100">
                <div class="invoice-preview-scaler">
                  <ThermalClassicPreview :data="previewDataStep1" :items="dummyItems" />
                </div>
              </div>
              
              <div class="p-5 text-center bg-white relative z-10">
                <h3 class="font-black text-slate-900 mb-1 tracking-tight">Clásico Profesional</h3>
                <p class="text-xs text-slate-500 font-bold">Ideal para régimen común y empresas</p>
              </div>
            </div>

            <div @click="selectedTemplate = 'modern'" 
                 class="group relative bg-white rounded-[24px] overflow-hidden cursor-pointer transition-all duration-300 hover:shadow-xl"
                 :class="selectedTemplate === 'modern' ? 'ring-4 ring-slate-900 ring-offset-4 ring-offset-[#EEF2F6] shadow-xl' : 'border border-white shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)]'">
              
              <div v-if="selectedTemplate === 'modern'" class="absolute top-4 right-4 z-20 animate-scale-in">
                <div class="w-9 h-9 rounded-xl bg-slate-900 flex items-center justify-center shadow-lg">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
              </div>
              
              <div class="invoice-preview-wrapper bg-slate-50 border-b border-slate-100">
                <div class="invoice-preview-scaler">
                  <ThermalModernPreview :data="previewDataStep1" :items="dummyItems" />
                </div>
              </div>
              
              <div class="p-5 text-center bg-white relative z-10">
                <h3 class="font-black text-slate-900 mb-1 tracking-tight">Moderno SaaS</h3>
                <p class="text-xs text-slate-500 font-bold">Estilo digital con encabezado de color</p>
              </div>
            </div>

            <div @click="selectedTemplate = 'minimal'" 
                 class="group relative bg-white rounded-[24px] overflow-hidden cursor-pointer transition-all duration-300 hover:shadow-xl"
                 :class="selectedTemplate === 'minimal' ? 'ring-4 ring-slate-900 ring-offset-4 ring-offset-[#EEF2F6] shadow-xl' : 'border border-white shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)]'">
              
              <div v-if="selectedTemplate === 'minimal'" class="absolute top-4 right-4 z-20 animate-scale-in">
                <div class="w-9 h-9 rounded-xl bg-slate-900 flex items-center justify-center shadow-lg">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
              </div>
              
              <div class="invoice-preview-wrapper bg-slate-50 border-b border-slate-100">
                <div class="invoice-preview-scaler">
                  <ThermalMinimalPreview :data="previewDataStep1" :items="dummyItems" />
                </div>
              </div>
              
              <div class="p-5 text-center bg-white relative z-10">
                <h3 class="font-black text-slate-900 mb-1 tracking-tight">Ticket Minimalista</h3>
                <p class="text-xs text-slate-500 font-bold">Ahorra papel y tinta. Alto contraste.</p>
              </div>
            </div>
          </div>
          
          <div class="mt-8 text-center">
            <button @click="nextStep" class="px-10 py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 active:scale-95 flex items-center space-x-2 mx-auto">
              <span>Continuar</span>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- STEP 2: CONFIGURATION -->
        <div v-else-if="currentStep === 2" key="step2" class="space-y-6">
          <div class="bg-white/50 backdrop-blur-sm rounded-[24px] border border-white shadow-sm p-6 mb-6">
            <h2 class="text-xl font-black text-slate-900 tracking-tight mb-2">Información de tu Negocio</h2>
            <p class="text-sm font-semibold text-slate-600">
              Estos datos aparecerán en todas tus facturas. Asegúrate de que sean correctos.
            </p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
            <!-- Formulario - 2 columnas -->
            <div class="lg:col-span-2">
              <div class="bg-white rounded-[24px] border border-white shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] p-6 space-y-5">
                
                <!-- Logo Upload - PRIMERO (como en la factura) -->
                <div>
                  <label class="block text-sm font-bold text-slate-900 mb-3">
                    Logo del Negocio <span class="text-xs text-slate-500 font-normal">(Opcional)</span>
                  </label>
                  <div class="flex items-center space-x-4">
                    <div v-if="config.logo" class="w-20 h-20 rounded-xl border-2 border-slate-200 overflow-hidden flex items-center justify-center bg-slate-50 shadow-sm">
                      <img :src="config.logo" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div v-else class="w-20 h-20 rounded-xl border-2 border-dashed border-slate-300 flex items-center justify-center bg-slate-50">
                      <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <label class="cursor-pointer inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl border border-slate-200 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <span>Subir Logo</span>
                        <input type="file" @change="handleLogoUpload" accept="image/*" class="hidden">
                      </label>
                      <p class="text-xs font-semibold text-slate-500 mt-1.5">PNG, JPG, SVG • Máx. 2MB</p>
                    </div>
                  </div>
                </div>

                <!-- Nombre y NIT - Grid 2 columnas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div>
                    <label class="block text-sm font-bold text-slate-900 mb-2">
                      Nombre del Negocio <span class="text-red-500">*</span>
                    </label>
                    <input 
                      v-model="config.storeName" 
                      type="text" 
                      placeholder="Ej: Mi Tienda S.A.S"
                      class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-900 focus:border-slate-900 outline-none transition-all text-slate-900 font-semibold"
                      required
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-bold text-slate-900 mb-2">
                      NIT / Documento
                    </label>
                    <input 
                      v-model="config.nit" 
                      type="text" 
                      placeholder="Ej: 900.123.456-7"
                      class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-900 focus:border-slate-900 outline-none transition-all text-slate-900 font-semibold"
                    >
                  </div>
                </div>

                <!-- Dirección - Completa -->
                <div>
                  <label class="block text-sm font-bold text-slate-900 mb-2">
                    Dirección
                  </label>
                  <input 
                    v-model="config.address" 
                    type="text" 
                    placeholder="Calle 123 #45-67, Bogotá"
                    class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-900 focus:border-slate-900 outline-none transition-all text-slate-900 font-semibold"
                  >
                </div>

                <!-- Teléfono y Email - Grid 2 columnas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div>
                    <label class="block text-sm font-bold text-slate-900 mb-2">
                      Teléfono <span class="text-red-500">*</span>
                    </label>
                    <input 
                      v-model="config.phone" 
                      type="tel" 
                      placeholder="+57 300 123 4567"
                      class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-900 focus:border-slate-900 outline-none transition-all text-slate-900 font-semibold"
                      required
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-bold text-slate-900 mb-2">
                      Email
                    </label>
                    <input 
                      v-model="config.email" 
                      type="email" 
                      placeholder="contacto@mitienda.com"
                      class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-900 focus:border-slate-900 outline-none transition-all text-slate-900 font-semibold"
                    >
                  </div>
                </div>

                <!-- Mensaje de Agradecimiento -->
                <div>
                  <label class="block text-sm font-bold text-slate-900 mb-2">
                    Mensaje de Agradecimiento
                  </label>
                  <textarea 
                    v-model="config.thankYouMessage" 
                    rows="3"
                    placeholder="Gracias por su compra. Esperamos verlo pronto."
                    class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-900 focus:border-slate-900 outline-none transition-all text-slate-900 font-semibold resize-none"
                  ></textarea>
                  <p class="text-xs font-semibold text-slate-500 mt-1.5">Este mensaje aparecerá al final de cada factura</p>
                </div>

              </div>
            </div>

            <!-- Preview en tiempo real - 1 columna -->
            <div class="lg:sticky lg:top-8 h-fit">
              <div class="bg-white rounded-[24px] border border-white shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-100 px-4 py-3">
                  <h3 class="text-sm font-black text-slate-900 tracking-tight">Vista Previa</h3>
                  <p class="text-xs font-bold text-slate-500">Así se verá tu factura</p>
                </div>
                <div class="overflow-y-auto p-4 bg-slate-50" style="max-height: 600px;">
                  <div class="bg-white shadow-2xl rounded-lg">
                    <ThermalModernPreview v-if="selectedTemplate === 'modern'" :data="previewConfigData" :items="dummyItems" />
                    <ThermalClassicPreview v-else-if="selectedTemplate === 'classic'" :data="previewConfigData" :items="dummyItems" />
                    <ThermalMinimalPreview v-else :data="previewConfigData" :items="dummyItems" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Botones de navegación -->
          <div class="flex justify-between items-center max-w-7xl mx-auto mt-8">
            <button 
              @click="currentStep = 1" 
              class="px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 font-bold text-base border border-slate-200 rounded-xl shadow-sm hover:shadow-md transition-all flex items-center space-x-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
              </svg>
              <span>Atrás</span>
            </button>
            <button 
              @click="nextStep" 
              :disabled="!config.storeName || !config.phone"
              class="px-10 py-3 bg-slate-900 hover:bg-black disabled:bg-slate-300 disabled:cursor-not-allowed text-white rounded-xl font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 active:scale-95 flex items-center space-x-2">
              <span>Continuar</span>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>

        <div v-else-if="currentStep === 3" key="step3" class="space-y-6">
          <div class="bg-white/50 backdrop-blur-sm rounded-[24px] border border-white shadow-sm p-6 mb-6">
            <h2 class="text-xl font-black text-slate-900 tracking-tight mb-2">Conecta WhatsApp Business</h2>
            <p class="text-sm font-semibold text-slate-600">
              Envía facturas automáticamente, recupera carritos abandonados y automatiza tu comunicación con clientes.
            </p>
          </div>
          
          <div class="max-w-2xl mx-auto">
            <div class="bg-white p-6 rounded-[24px] border border-white shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)]">
              
              <div class="space-y-5">
                <!-- Estado de Conexión -->
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center flex-shrink-0">
                      <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <p class="text-xs font-bold text-emerald-700 uppercase tracking-wide mb-0.5">Estado</p>
                      <p class="text-sm font-bold text-slate-900">
                        {{ whatsappStatus.connected ? '✅ Conectado' : '⏳ Esperando conexión' }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- QR Code y Instrucciones -->
                <div v-if="!whatsappStatus.connected" class="border border-gray-200 rounded-xl p-5">
                  <!-- QR Code -->
                  <div v-if="qrCode" class="flex flex-col items-center space-y-4">
                    <h3 class="text-base font-bold text-slate-900">Escanea el código QR</h3>
                    <div class="bg-white p-4 rounded-xl border-2 border-emerald-500 shadow-lg">
                      <div id="qr-code-onboarding" class="flex items-center justify-center" style="min-height: 200px; min-width: 200px;">
                        <!-- El QR se generará aquí -->
                      </div>
                    </div>
                    
                    <!-- Instrucciones -->
                    <div class="bg-slate-50 rounded-lg p-4 text-left w-full max-w-sm">
                      <p class="text-xs font-bold text-slate-900 mb-2">Cómo conectar:</p>
                      <ol class="text-xs text-slate-600 space-y-1.5 list-decimal list-inside">
                        <li>Abre WhatsApp en tu teléfono</li>
                        <li>Ve a <strong>Configuración → Dispositivos vinculados</strong></li>
                        <li>Toca <strong>"Vincular un dispositivo"</strong></li>
                        <li>Escanea este código QR</li>
                      </ol>
                    </div>

                    <button 
                      @click="refreshQR" 
                      :disabled="refreshingQR"
                      class="text-xs font-bold text-emerald-600 hover:text-emerald-700 underline disabled:opacity-50"
                    >
                      {{ refreshingQR ? 'Actualizando...' : 'Actualizar código QR' }}
                    </button>
                  </div>

                  <!-- Sin QR aún -->
                  <div v-else class="flex flex-col items-center space-y-4 py-8">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                      <svg class="w-8 h-8 text-slate-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                      </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-600">Generando código QR...</p>
                    <button 
                      @click="initializeWhatsApp"
                      class="text-xs font-bold text-emerald-600 hover:text-emerald-700 underline"
                    >
                      Reintentar
                    </button>
                  </div>
                </div>

                <!-- Mensaje de éxito -->
                <div v-else class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl p-6 text-center">
                  <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-bold text-white mb-1">¡WhatsApp Conectado!</h3>
                  <p class="text-sm text-emerald-50">Ya puedes enviar facturas automáticamente</p>
                </div>

                <!-- Número de WhatsApp (opcional) -->
                <div>
                  <label class="block text-xs font-semibold text-gray-900 mb-1.5">
                    Número de WhatsApp Business (opcional)
                  </label>
                  <input 
                    v-model="config.whatsappNumber" 
                    type="text" 
                    placeholder="+57 300 123 4567" 
                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all text-gray-900"
                  >
                  <p class="text-xs text-gray-500 mt-1">Este número aparecerá en tus facturas. Puedes cambiarlo después.</p>
                </div>
                
                <!-- Botones finales -->
                <div class="flex flex-col space-y-3 pt-4 border-t border-gray-200">
                  <button 
                    @click="finishOnboarding" 
                    class="w-full py-3.5 bg-slate-900 hover:bg-black text-white rounded-xl font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 active:scale-95 flex items-center justify-center space-x-2"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ whatsappStatus.connected ? 'Finalizar y Empezar a Vender' : 'Conectar Después y Finalizar' }}</span>
                  </button>
                  
                  <button 
                    @click="currentStep = 2"
                    class="text-slate-500 hover:text-slate-900 text-sm font-bold py-2.5 transition-colors"
                  >
                    Volver al paso anterior
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '@/services/apiClient.js'
import QRCode from 'qrcode'
import { whatsappService } from '@/services/whatsappService.js'
// ✅ IMPORTAR LOS TICKETS TÉRMICOS REALES (los que genera invoiceTemplate.js)
import ThermalClassicPreview from '@/components/invoiceTemplates/ThermalClassicPreview.vue'
import ThermalModernPreview from '@/components/invoiceTemplates/ThermalModernPreview.vue'
import ThermalMinimalPreview from '@/components/invoiceTemplates/ThermalMinimalPreview.vue'

const router = useRouter()
const currentStep = ref(1)
const selectedTemplate = ref('modern')
const isLoading = ref(true)

// Estados de WhatsApp
const whatsappStatus = ref({ connected: false })
const qrCode = ref('')
const refreshingQR = ref(false)
let whatsappCheckInterval = null

const config = reactive({
  storeName: '',
  nit: '',
  phone: '',
  email: '',
  address: '',
  logo: null,
  thankYouMessage: '',
  whatsappNumber: ''
})

// Datos para el preview del Step 1 (estáticos para mostrar ejemplo)
// ✅ ESTOS SON LOS DATOS QUE USAN LOS TEMPLATES REALES DEL SISTEMA
const previewDataStep1 = {
  storeName: 'MI EMPRESA',
  nit: 'N/A',
  address: 'Calle 123 #45-67, Bogotá',
  phone: '+57 300 123 4567',
  logo: null,
  thankYouMessage: '¡Gracias por su compra!'
}

// Items de ejemplo para todas las previsualizaciones
// ✅ MISMO FORMATO QUE USA EL POS REAL
const dummyItems = [
  { name: 'Producto A', quantity: 2, price: 25000, total: 50000 },
  { name: 'Producto B', quantity: 1, price: 100000, total: 100000 }
]

// Preview data reactivo para Step 2 - Datos que cambian en tiempo real
const previewConfigData = computed(() => ({
  storeName: config.storeName || 'MI EMPRESA',
  address: config.address || 'Calle 123 #45-67, Bogotá',
  phone: config.phone || '+57 300 123 4567',
  email: config.email || '',
  nit: config.nit || '',
  logo: config.logo,
  thankYouMessage: config.thankYouMessage || '¡Gracias por su compra!'
}))

const handleLogoUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 2 * 1024 * 1024) { // 2MB máximo
      alert('La imagen es demasiado grande. Máximo 2MB.')
      return
    }
    const reader = new FileReader()
    reader.onload = (e) => {
      config.logo = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

// ===== FUNCIONES DE WHATSAPP =====

const initializeWhatsApp = async () => {
  try {
    console.log('🔄 Inicializando WhatsApp...')
    await whatsappService.initialize()
    
    // Esperar y verificar QR
    setTimeout(async () => {
      await getQRCode()
      startWhatsAppStatusCheck()
    }, 2000)
  } catch (error) {
    console.error('Error iniciando WhatsApp:', error)
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
    // Propagar error para que startWhatsAppStatusCheck lo maneje
    throw error
  }
}

const generateQRImage = async (qrString) => {
  try {
    const qrContainer = document.getElementById('qr-code-onboarding')
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

const refreshQR = async () => {
  refreshingQR.value = true
  try {
    await getQRCode()
  } finally {
    refreshingQR.value = false
  }
}

const checkWhatsAppStatus = async () => {
  try {
    const result = await whatsappService.getStatus()
    if (result.success && result.status) {
      whatsappStatus.value = result.status
      
      // Si se conectó, limpiar QR
      if (result.status.connected) {
        qrCode.value = ''
        stopWhatsAppStatusCheck()
      }
    }
  } catch (error) {
    // Propagar error para que startWhatsAppStatusCheck lo maneje
    throw error
  }
}

const startWhatsAppStatusCheck = () => {
  if (whatsappCheckInterval) return
  
  let errorCount = 0
  const maxErrors = 3
  
  // Verificar cada 3 segundos
  whatsappCheckInterval = setInterval(async () => {
    try {
      await checkWhatsAppStatus()
      errorCount = 0 // Reset contador si tiene éxito
      
      // Si no está conectado, intentar obtener QR
      if (!whatsappStatus.value.connected && !qrCode.value) {
        await getQRCode()
      }
    } catch (error) {
      errorCount++
      console.log(`Error ${errorCount}/${maxErrors} verificando WhatsApp`)
      
      // Si hay muchos errores consecutivos, detener el intervalo
      if (errorCount >= maxErrors) {
        console.log('⚠️ Demasiados errores, deteniendo verificación de WhatsApp')
        stopWhatsAppStatusCheck()
      }
    }
  }, 3000)
}

const stopWhatsAppStatusCheck = () => {
  if (whatsappCheckInterval) {
    clearInterval(whatsappCheckInterval)
    whatsappCheckInterval = null
  }
}

// Watch para cuando cambien de paso
watch(currentStep, (newStep) => {
  if (newStep === 3) {
    // Entró al paso 3 de WhatsApp
    initializeWhatsApp()
  } else {
    // Salió del paso 3
    stopWhatsAppStatusCheck()
  }
})

// Cleanup al desmontar componente
onUnmounted(() => {
  stopWhatsAppStatusCheck()
})

// ===== FIN FUNCIONES DE WHATSAPP =====

const nextStep = async () => {
  if (currentStep.value === 1) {
    // Guardar template seleccionado al pasar a Step 2
    try {
      await apiClient.put('/tenant/system-settings', {
        invoice_template: selectedTemplate.value
      })
    } catch (error) {
      // Silenciar errores 401/404 - es normal en onboarding inicial
      if (error.response?.status !== 401 && error.response?.status !== 404) {
        console.warn('Error guardando template:', error.message)
      }
    }
  }
  
  if (currentStep.value === 2) {
    // Guardar datos de la empresa al pasar a Step 3
    try {
      await saveConfig()
    } catch (error) {
      // Silenciar errores 401/404 - normal en onboarding inicial
      if (error.response && (error.response.status === 404 || error.response.status === 401)) {
        // Es normal que no esté autenticado durante el onboarding inicial
      } else {
        alert('Por favor verifica los datos ingresados.')
        return
      }
    }
  }
  
  if (currentStep.value < 3) {
    currentStep.value++
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

// Cargar configuración existente al montar el componente
onMounted(async () => {
  // 🎯 PRIMERO: Pre-llenar con datos del registro (localStorage)
  const registrationData = JSON.parse(localStorage.getItem('registration_data') || '{}')
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  
  // Pre-llenar nombre de tienda y teléfono desde el registro
  if (registrationData.company_name || registrationData.storeName) {
    config.storeName = registrationData.company_name || registrationData.storeName || ''
  }
  if (registrationData.phone || user.phone) {
    config.phone = registrationData.phone || user.phone || ''
  }
  if (user.email) {
    config.email = user.email || ''
  }

  // SEGUNDO: Intentar cargar datos guardados del backend
  try {
    const response = await apiClient.get('/tenant/system-settings')
    const settings = response.data.data || response.data
    
    // Cargar datos existentes si están disponibles (sobrescribir pre-llenado si existe)
    if (settings && typeof settings === 'object') {
      config.storeName = settings.company_name || config.storeName
      config.nit = settings.company_document || ''
      config.phone = settings.company_phone || config.phone
      config.email = settings.company_email || config.email
      config.address = settings.company_address || ''
      config.logo = settings.company_logo || null
      config.thankYouMessage = settings.invoice_footer_message || ''
      config.whatsappNumber = settings.whatsapp_business_number || ''
      
      // Cargar template seleccionado
      if (settings.invoice_template) {
        selectedTemplate.value = settings.invoice_template
      }
    }
    
    isLoading.value = false
  } catch (error) {
    // Silenciar errores 401/404 - es normal en onboarding inicial sin autenticación
    if (error.response?.status !== 401 && error.response?.status !== 404) {
      console.warn('Error cargando configuración:', error.message)
    }
    // Usar los datos pre-llenados del registro
    isLoading.value = false
  }
})

const saveConfig = async () => {
  try {
    const response = await apiClient.put('/tenant/system-settings', {
      company_name: config.storeName,
      company_document: config.nit,
      company_phone: config.phone,
      company_email: config.email,
      company_address: config.address,
      company_logo: config.logo,
      invoice_footer_message: config.thankYouMessage,
      invoice_template: selectedTemplate.value
    })
    return response
  } catch (error) {
    // Si hay error 404 o 401, es porque aún no estamos autenticados en el tenant
    // Esto es normal en el onboarding inicial, simplemente lo ignoramos
    if (error.response && (error.response.status === 404 || error.response.status === 401)) {
      console.log('Configuración se guardará después del primer login')
      return { success: true } // Retornamos éxito para continuar el flujo
    }
    console.error('Error guardando configuración:', error)
    throw error
  }
}

const finishOnboarding = async () => {
  try {
    // Intentar guardar toda la configuración en una sola llamada
    try {
      await apiClient.put('/tenant/system-settings', {
        company_name: config.storeName,
        company_document: config.nit,
        company_phone: config.phone,
        company_email: config.email,
        company_address: config.address,
        company_logo: config.logo,
        invoice_footer_message: config.thankYouMessage,
        invoice_template: selectedTemplate.value,
        whatsapp_business_number: config.whatsappNumber,
        onboarding_completed: true
      })
    } catch (error) {
      // Si hay error 404 o 401, es porque aún no estamos autenticados
      // Guardamos en localStorage y continuamos de todos modos
      if (error.response && (error.response.status === 404 || error.response.status === 401)) {
        console.log('Configuración se guardará en el primer login')
        // Guardar en localStorage para aplicar después del login
        localStorage.setItem('pending_onboarding_config', JSON.stringify({
          company_name: config.storeName,
          company_document: config.nit,
          company_phone: config.phone,
          company_email: config.email,
          company_address: config.address,
          company_logo: config.logo,
          invoice_footer_message: config.thankYouMessage,
          invoice_template: selectedTemplate.value,
          whatsapp_business_number: config.whatsappNumber
        }))
      } else {
        throw error
      }
    }
    
    localStorage.setItem('onboarding_completed', 'true')
    
    // Redirigir al login para que se autentique en el tenant
    router.push('/login')
  } catch (error) {
    console.error('Error finalizando onboarding:', error)
    alert('Error al guardar la configuración. Por favor intenta de nuevo.')
  }
}
</script>

<style scoped>
/* Transiciones suaves entre pasos */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(30px) scale(0.98);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-30px) scale(0.98);
}

.fade-slide-enter-to,
.fade-slide-leave-from {
  opacity: 1;
  transform: translateX(0) scale(1);
}

@keyframes scaleIn {
  from { transform: scale(0); }
  to { transform: scale(1); }
}
.animate-scale-in { animation: scaleIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }

/* === EL TRUCO DEL AJUSTE PERFECTO === */
.invoice-preview-wrapper {
  width: 100%;
  height: 340px; /* Altura fija de la ventana de vista previa */
  overflow: hidden;
  position: relative;
  display: flex;
  justify-content: center; /* Centrar horizontalmente */
  align-items: flex-start; /* Pegar arriba */
  background-color: #f8fafc;
}

.invoice-preview-scaler {
  /* Ancho base del TICKET TÉRMICO REAL (80mm = ~300px) */
  width: 300px; 
  /* Escala para que se vea bien en el preview */
  transform: scale(1); 
  transform-origin: top center; /* Escalar desde arriba centro */
  margin-top: 10px; /* Margen superior visual */
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); /* Sombra para que flote */
}
</style>