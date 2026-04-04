<template>
  <div class="flex-1 w-full h-full overflow-y-auto font-sans bg-[#EEF2F6] pb-24">

    <div class="relative w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10 flex flex-col flex-1">
      
      <!-- Encabezado y Progreso Minimalista -->
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 mt-2 shrink-0">
        <div>
          <h1 class="text-2xl md:text-[28px] font-black text-slate-900 tracking-tight">
            Configuración de <span class="text-[#009F7A]">{{ config.storeName || 'tu Tienda' }}</span>
          </h1>
          <p class="text-sm font-semibold text-slate-500 mt-1">
            Personaliza tu sistema en {{ isPremiumPlan ? '3' : '2' }} pasos
          </p>
        </div>

        <!-- Stepper Sutil -->
        <div class="flex items-center gap-2 md:gap-3">
          <!-- Paso 1 -->
          <div class="flex items-center gap-1.5 md:gap-2">
            <div class="w-6 h-6 rounded-full border flex items-center justify-center text-[10px] font-bold transition-all"
                 :class="currentStep >= 1 ? 'border-[#009F7A] bg-[#009F7A]/10 text-[#009F7A]' : 'border-slate-300 text-slate-400'">
              <span v-if="currentStep > 1">✓</span><span v-else>1</span>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest hidden sm:inline" :class="currentStep >= 1 ? 'text-slate-900' : 'text-slate-400'">Diseño</span>
          </div>
          
          <div class="w-4 sm:w-8 h-[2px] rounded-full transition-all" :class="currentStep >= 2 ? 'bg-[#009F7A]' : 'bg-slate-200'"></div>
          
          <!-- Paso 2 -->
          <div class="flex items-center gap-1.5 md:gap-2">
            <div class="w-6 h-6 rounded-full border flex items-center justify-center text-[10px] font-bold transition-all"
                 :class="currentStep >= 2 ? 'border-[#009F7A] bg-[#009F7A]/10 text-[#009F7A]' : 'border-slate-300 text-slate-400'">
              <span v-if="currentStep > 2 || (!isPremiumPlan && currentStep === 2)">✓</span><span v-else>2</span>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest hidden sm:inline" :class="currentStep >= 2 ? 'text-slate-900' : 'text-slate-400'">Datos</span>
          </div>
          
          <!-- Paso 3 (Premium) -->
          <template v-if="isPremiumPlan">
            <div class="w-4 sm:w-8 h-[2px] rounded-full transition-all" :class="currentStep >= 3 ? 'bg-[#009F7A]' : 'bg-slate-200'"></div>
            <div class="flex items-center gap-1.5 md:gap-2">
              <div class="w-6 h-6 rounded-full border flex items-center justify-center text-[10px] font-bold transition-all"
                   :class="currentStep >= 3 ? 'border-[#009F7A] bg-[#009F7A]/10 text-[#009F7A]' : 'border-slate-300 text-slate-400'">
                3
              </div>
              <span class="text-[10px] font-bold uppercase tracking-widest hidden sm:inline" :class="currentStep >= 3 ? 'text-slate-900' : 'text-slate-400'">WhatsApp</span>
            </div>
          </template>
        </div>
      </div>

      <!-- Transiciones suaves entre pasos -->
      <Transition name="fade-slide" mode="out-in">
        
        <!-- ==============================================
             PASO 1: ELIGE TU PLANTILLA
             ============================================== -->
        <div v-if="currentStep === 1" key="step1" class="space-y-6 flex-1">
          
          <div class="mb-4">
            <h2 class="text-lg md:text-xl font-black text-slate-900 tracking-tight">Elige tu Plantilla de Facturación</h2>
            <p class="text-xs md:text-sm font-semibold text-slate-500 mt-1">Selecciona el diseño que mejor represente tu marca. Podrás cambiarlo después.</p>
          </div>

          <!-- Grid de Radio Cards Minimalistas -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            
            <!-- Plantilla Clásica -->
            <label class="group relative flex flex-col bg-white border rounded-xl overflow-hidden cursor-pointer transition-all hover:bg-slate-50 outline-none"
                   :class="selectedTemplate === 'classic' ? 'border-[#009F7A] bg-[#009F7A]/5 ring-2 ring-[#009F7A]/20' : 'border-slate-200 hover:border-[#009F7A]/40'">
              <input type="radio" v-model="selectedTemplate" value="classic" class="sr-only">
              
              <!-- Checkmark Elegante -->
              <div v-if="selectedTemplate === 'classic'" class="absolute top-3 right-3 text-[#009F7A] z-10 bg-white/90 rounded-full p-0.5 backdrop-blur shadow-sm">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                </svg>
              </div>

              <!-- Preview Escala Reducida -->
              <div class="w-full flex justify-center pt-6 pb-2 bg-[#F8FAFC] border-b border-slate-100 overflow-hidden relative" style="height: 380px;">
                <div class="invoice-preview-scaler transition-transform group-hover:scale-[0.88]" style="transform: scale(0.85); transform-origin: top center; margin-top: 0;">
                  <ThermalClassicPreview :data="previewDataStep1" :items="dummyItems" />
                </div>
              </div>

              <div class="p-4 flex-1 flex flex-col justify-center">
                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">Clásico Profesional</h3>
                <p class="text-xs font-semibold text-slate-500 mt-1">Régimen común y empresas</p>
              </div>
            </label>

            <!-- Plantilla Moderna -->
            <label class="group relative flex flex-col bg-white border rounded-xl overflow-hidden cursor-pointer transition-all hover:bg-slate-50 outline-none"
                   :class="selectedTemplate === 'modern' ? 'border-[#009F7A] bg-[#009F7A]/5 ring-2 ring-[#009F7A]/20' : 'border-slate-200 hover:border-[#009F7A]/40'">
              <input type="radio" v-model="selectedTemplate" value="modern" class="sr-only">
              
              <div v-if="selectedTemplate === 'modern'" class="absolute top-3 right-3 text-[#009F7A] z-10 bg-white/90 rounded-full p-0.5 backdrop-blur shadow-sm">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                </svg>
              </div>

              <div class="w-full flex justify-center pt-6 pb-2 bg-transparent border-b border-slate-100 overflow-hidden relative" style="height: 380px;">
                <div class="invoice-preview-scaler transition-transform group-hover:scale-[0.88]" style="transform: scale(0.85); transform-origin: top center; margin-top: 0;">
                  <ThermalModernPreview :data="previewDataStep1" :items="dummyItems" />
                </div>
              </div>

              <div class="p-4 flex-1 flex flex-col justify-center">
                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">Moderno SaaS</h3>
                <p class="text-xs font-semibold text-slate-500 mt-1">Estilo digital corporativo</p>
              </div>
            </label>

            <!-- Plantilla Ticket Minimalista -->
            <label class="group relative flex flex-col bg-white border rounded-xl overflow-hidden cursor-pointer transition-all hover:bg-slate-50 outline-none"
                   :class="selectedTemplate === 'minimal' ? 'border-[#009F7A] bg-[#009F7A]/5 ring-2 ring-[#009F7A]/20' : 'border-slate-200 hover:border-[#009F7A]/40'">
              <input type="radio" v-model="selectedTemplate" value="minimal" class="sr-only">
              
              <div v-if="selectedTemplate === 'minimal'" class="absolute top-3 right-3 text-[#009F7A] z-10 bg-white/90 rounded-full p-0.5 backdrop-blur shadow-sm">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                </svg>
              </div>

              <div class="w-full flex justify-center pt-6 pb-2 bg-[#F8FAFC] border-b border-slate-100 overflow-hidden relative" style="height: 380px;">
                <div class="invoice-preview-scaler transition-transform group-hover:scale-[0.88]" style="transform: scale(0.85); transform-origin: top center; margin-top: 0;">
                  <ThermalMinimalPreview :data="previewDataStep1" :items="dummyItems" />
                </div>
              </div>

              <div class="p-4 flex-1 flex flex-col justify-center">
                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">Ticket Minimalista</h3>
                <p class="text-xs font-semibold text-slate-500 mt-1">Ahorro de papel. Alto contraste.</p>
              </div>
            </label>
          </div>
          
          <div class="mt-8 flex justify-end shrink-0">
            <button @click="nextStep" class="w-full sm:w-auto px-8 py-3 bg-slate-900 hover:bg-black text-white rounded-lg font-bold text-sm tracking-wide transition-all shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
              <span>Continuar Configuración</span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- ==============================================
             PASO 2: DATOS DEL NEGOCIO (60/40 Asimétrico y Sticky)
             ============================================== -->
        <div v-else-if="currentStep === 2" key="step2" class="space-y-6 flex-1">
          
          <div class="mb-4">
            <h2 class="text-lg md:text-xl font-black text-slate-900 tracking-tight">Información del Negocio</h2>
            <p class="text-xs md:text-sm font-semibold text-slate-500 mt-1">Los datos que aparecerán impresos en tus facturas.</p>
          </div>

          <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start relative pb-8">
            
            <!-- Columna Izquierda: Formulario (60%) -->
            <div class="w-full lg:w-[65%] xl:w-[70%] flex flex-col space-y-6">
              
              <div class="bg-white border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                <!-- Zona Drag & Drop Minimalista de Logo -->
                <div class="mb-6 flex items-center gap-4 p-4 border border-dashed border-slate-300 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                  <div v-if="config.logo" class="w-14 h-14 bg-white border border-slate-200 shadow-sm rounded-lg overflow-hidden flex items-center justify-center shrink-0">
                    <img :src="config.logo" alt="Logo" class="w-full h-full object-contain p-1">
                  </div>
                  <div v-else class="w-14 h-14 bg-white border border-slate-200 shadow-sm rounded-lg text-slate-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  </div>
                  <div>
                    <label class="cursor-pointer text-base font-bold text-slate-900 hover:text-[#009F7A] transition-colors inline-block">
                      Subir Logo de la Empresa
                      <input type="file" @change="handleLogoUpload" accept="image/*" class="hidden">
                    </label>
                    <p class="text-[13px] font-semibold text-slate-500 mt-1">PNG o JPG (Recomendado fondo transparente)</p>
                  </div>
                </div>

                <!-- Grid de Inputs Floating Labels -->
                <div class="space-y-4">
                  
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tienda -->
                    <div class="relative group">
                      <input v-model="config.storeName" type="text" id="storeName" placeholder=" " class="peer w-full px-4 pt-6 pb-2.5 border-b-2 border-x-0 border-t-0 border-slate-200 bg-slate-50 hover:bg-slate-100 focus:bg-white focus:outline-none focus:border-[#009F7A] text-base font-semibold text-slate-900 rounded-t-lg transition-all" required>
                      <label for="storeName" class="absolute text-sm text-slate-500 font-semibold duration-300 transform -translate-y-2.5 scale-75 top-4 left-4 z-10 origin-[0] peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-2.5 peer-focus:text-[#009F7A] peer-focus:font-bold">Nombre del Negocio <span class="text-red-500">*</span></label>
                    </div>
                    <!-- NIT -->
                    <div class="relative group">
                      <input v-model="config.nit" type="text" id="nit" placeholder=" " class="peer w-full px-4 pt-6 pb-2.5 border-b-2 border-x-0 border-t-0 border-slate-200 bg-slate-50 hover:bg-slate-100 focus:bg-white focus:outline-none focus:border-[#009F7A] text-base font-semibold text-slate-900 rounded-t-lg transition-all">
                      <label for="nit" class="absolute text-sm text-slate-500 font-semibold duration-300 transform -translate-y-2.5 scale-75 top-4 left-4 z-10 origin-[0] peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-2.5 peer-focus:text-[#009F7A] peer-focus:font-bold">NIT / Documento</label>
                    </div>
                  </div>

                  <!-- Dirección Completa -->
                  <div class="relative group">
                    <input v-model="config.address" type="text" id="address" placeholder=" " class="peer w-full px-4 pt-6 pb-2.5 border-b-2 border-x-0 border-t-0 border-slate-200 bg-slate-50 hover:bg-slate-100 focus:bg-white focus:outline-none focus:border-[#009F7A] text-base font-semibold text-slate-900 rounded-t-lg transition-all">
                    <label for="address" class="absolute text-sm text-slate-500 font-semibold duration-300 transform -translate-y-2.5 scale-75 top-4 left-4 z-10 origin-[0] peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-2.5 peer-focus:text-[#009F7A] peer-focus:font-bold">Dirección Completa</label>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Teléfono -->
                    <div class="relative group">
                      <input v-model="config.phone" type="tel" id="phone" placeholder=" " class="peer w-full px-4 pt-6 pb-2.5 border-b-2 border-x-0 border-t-0 border-slate-200 bg-slate-50 hover:bg-slate-100 focus:bg-white focus:outline-none focus:border-[#009F7A] text-base font-semibold text-slate-900 rounded-t-lg transition-all" required>
                      <label for="phone" class="absolute text-sm text-slate-500 font-semibold duration-300 transform -translate-y-2.5 scale-75 top-4 left-4 z-10 origin-[0] peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-2.5 peer-focus:text-[#009F7A] peer-focus:font-bold">Teléfono / WhatsApp <span class="text-red-500">*</span></label>
                    </div>
                    <!-- Email -->
                    <div class="relative group">
                      <input v-model="config.email" type="email" id="email" placeholder=" " class="peer w-full px-4 pt-6 pb-2.5 border-b-2 border-x-0 border-t-0 border-slate-200 bg-slate-50 hover:bg-slate-100 focus:bg-white focus:outline-none focus:border-[#009F7A] text-base font-semibold text-slate-900 rounded-t-lg transition-all">
                      <label for="email" class="absolute text-sm text-slate-500 font-semibold duration-300 transform -translate-y-2.5 scale-75 top-4 left-4 z-10 origin-[0] peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-2.5 peer-focus:text-[#009F7A] peer-focus:font-bold">Correo (Opcional)</label>
                    </div>
                  </div>

                  <!-- Footer Text -->
                  <div class="relative group pt-2">
                    <textarea v-model="config.thankYouMessage" id="msg" rows="2" placeholder=" " class="peer w-full px-4 pt-7 pb-3 border-b-2 border-x-0 border-t-0 border-slate-200 bg-slate-50 hover:bg-slate-100 focus:bg-white focus:outline-none focus:border-[#009F7A] text-base font-semibold text-slate-900 rounded-t-lg transition-all resize-none"></textarea>
                    <label for="msg" class="absolute text-xs text-slate-500 font-semibold duration-300 transform -translate-y-3.5 scale-75 top-5 left-4 z-10 origin-[0] peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-3.5 peer-focus:text-[#009F7A] peer-focus:font-bold">Mensaje de Despedida (Ticket)</label>
                  </div>
                </div>
              </div>

              
              <!-- Sección opcional: Importar Productos desde Excel (NO para tiendas de MODA) -->
              <div v-if="!isFashionStore" class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl border border-indigo-100 shadow-sm p-5 mt-2">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                  <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                      <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-base font-black text-slate-900 tracking-tight">¿Tienes tu inventario en Excel?</h3>
                      <p class="text-[13px] font-semibold text-slate-600 mt-1">
                        Importa tus productos automáticamente. Nuestra IA detectará las columnas.
                      </p>
                      <div v-if="importedProductsCount > 0" class="mt-2 inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-bold rounded-full">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ importedProductsCount }} productos importados
                      </div>
                    </div>
                  </div>
                  <button
                    @click="showExcelImportModal = true"
                    class="shrink-0 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center justify-center space-x-2 w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span>Importar Excel</span>
                  </button>
                </div>
              </div>

              <!-- Botones de Navegación Paso 2 -->
              <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-xl border border-slate-200 gap-4 mt-2 shadow-sm">
                <button @click="currentStep = 1" class="w-full sm:w-auto px-6 py-3 text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-bold text-base rounded-lg transition-all flex items-center justify-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                  <span>Atrás</span>
                </button>
                
                <button v-if="!isPremiumPlan" @click="finishOnboarding" :disabled="!config.storeName || !config.phone"
                  class="w-full sm:w-auto px-8 py-3.5 bg-slate-900 hover:bg-black disabled:bg-slate-300 disabled:text-slate-500 text-white rounded-lg font-bold text-base tracking-wide transition-all shadow-lg flex items-center justify-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  <span>Finalizar y Entrar</span>
                </button>
                
                <button v-else @click="nextStep" :disabled="!config.storeName || !config.phone"
                  class="w-full sm:w-auto px-8 py-3.5 bg-slate-900 hover:bg-black disabled:bg-slate-300 disabled:text-slate-500 text-white rounded-lg font-bold text-base tracking-wide transition-all shadow-lg flex items-center justify-center gap-2">
                  <span>Continuar a WhatsApp</span>
                  <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>
              </div>
            </div>

            <!-- Columna Derecha: Sticky Preview (45%) -->
            <div class="w-full lg:w-[35%] xl:w-[30%] lg:sticky lg:top-8 mt-2 lg:mt-0 max-w-[400px] mx-auto lg:mx-0">
              <div class="flex items-center gap-2 mb-2 ml-1">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <h3 class="text-xs font-bold uppercase text-slate-500 tracking-widest">Vista Previa en Tiempo Real</h3>
              </div>
              
              <div class="w-full bg-white shadow-xl rounded-2xl overflow-hidden border border-slate-100 transition-all duration-300">
                <div class="invoice-preview-wrapper bg-[#F8FAFC] overflow-hidden relative border-t-4"
                     :class="{
                       'border-slate-800': selectedTemplate === 'classic',
                       'border-blue-500': selectedTemplate === 'modern',
                       'border-black': selectedTemplate === 'minimal'
                     }"
                     style="height: 480px;">
                  <div class="invoice-preview-scaler w-full flex justify-center origin-top pt-6" style="transform: scale(0.95); transform-origin: top center;">
                    <ThermalModernPreview v-if="selectedTemplate === 'modern'" :data="previewConfigData" :items="dummyItems" />
                    <ThermalClassicPreview v-else-if="selectedTemplate === 'classic'" :data="previewConfigData" :items="dummyItems" />
                    <ThermalMinimalPreview v-else :data="previewConfigData" :items="dummyItems" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- <!-- STEP 3: WHATSAPP - Solo para planes Premium/Enterprise -->
        <div v-else-if="currentStep === 3 && isPremiumPlan" key="step3" class="space-y-6">
          <div class="bg-white/50  rounded-[24px] border border-white shadow-sm p-6 mb-6">
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

  <!-- Excel Import Modal -->
  <ExcelImportModal 
    :is-open="showExcelImportModal" 
    @close="showExcelImportModal = false"
    @imported="handleExcelImported"
  />
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '@/services/apiClient.js'
import QRCode from 'qrcode'
import { whatsappService } from '@/services/whatsappService.js'
import { appStore } from '@/store/appStore.js'
// ✅ IMPORTAR LOS TICKETS TÉRMICOS REALES (los que genera invoiceTemplate.js)
import ThermalClassicPreview from '@/components/invoiceTemplates/ThermalClassicPreview.vue'
import ThermalModernPreview from '@/components/invoiceTemplates/ThermalModernPreview.vue'
import ThermalMinimalPreview from '@/components/invoiceTemplates/ThermalMinimalPreview.vue'
// 📊 Modal de importación de Excel
import ExcelImportModal from '@/components/ExcelImportModal.vue'

const router = useRouter()
const currentStep = ref(1)
const selectedTemplate = ref('classic')
const isLoading = ref(true)

// 🔐 Verificar si el plan permite WhatsApp (solo premium y enterprise)
const isPremiumPlan = computed(() => {
  const plan = appStore.tenantPlan || 'free_trial'
  return ['premium', 'enterprise'].includes(plan)
})

// 👗 Verificar si es tienda de moda (no permite importar Excel porque usa variantes)
const isFashionStore = computed(() => {
  return config.store_type === 'fashion'
})

// Número total de pasos según el plan
const totalSteps = computed(() => isPremiumPlan.value ? 3 : 2)

// Excel Import
const showExcelImportModal = ref(false)
const importedProductsCount = ref(0)

const handleExcelImported = (result) => {
  showExcelImportModal.value = false
  if (result.success && result.stats?.imported > 0) {
    importedProductsCount.value = result.stats.imported
  }
}

// Estados de WhatsApp
const whatsappStatus = ref({ connected: false })
const qrCode = ref('')
const refreshingQR = ref(false)
let whatsappCheckInterval = null
let qrAutoRefreshInterval = null // Auto-refresh del QR cada 45 segundos

// 🏪 Leer tipo de tienda INMEDIATAMENTE del localStorage (antes del primer render)
const pendingStoreTypeInitial = localStorage.getItem('pending_store_type')

const config = reactive({
  storeName: '',
  store_type: pendingStoreTypeInitial || 'general', // 🏪 Cargar inmediatamente si existe
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
  address: config.address || '',
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
    await whatsappService.initialize()
    
    // Esperar y verificar QR
    setTimeout(async () => {
      await getQRCode()
      startWhatsAppStatusCheck()
      startQRAutoRefresh() // Iniciar auto-refresh del QR
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
      
      // Si se conectó, limpiar QR y detener intervals
      if (result.status.connected) {
        qrCode.value = ''
        stopWhatsAppStatusCheck()
        stopQRAutoRefresh()
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
      
      // Si hay muchos errores consecutivos, detener el intervalo
      if (errorCount >= maxErrors) {
        stopWhatsAppStatusCheck()
        stopQRAutoRefresh()
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

// Auto-refresh del QR cada 45 segundos (el QR de WhatsApp vence cada ~60 segundos)
const startQRAutoRefresh = () => {
  if (qrAutoRefreshInterval) return
  
  qrAutoRefreshInterval = setInterval(async () => {
    // Solo refrescar si no está conectado y hay un QR visible
    if (!whatsappStatus.value.connected && qrCode.value) {
      try {
        await getQRCode()
      } catch (error) {
        // Silencioso - el status check manejará los errores
      }
    }
  }, 45000) // 45 segundos
}

const stopQRAutoRefresh = () => {
  if (qrAutoRefreshInterval) {
    clearInterval(qrAutoRefreshInterval)
    qrAutoRefreshInterval = null
  }
}

// Watch para cuando cambien de paso
watch(currentStep, (newStep) => {
  if (newStep === 3) {
    // Entró al paso 3 de WhatsApp
    initializeWhatsApp()
  } else {
    // Salió del paso 3 - detener todos los intervals
    stopWhatsAppStatusCheck()
    stopQRAutoRefresh()
  }
})

// Cleanup al desmontar componente
onUnmounted(() => {
  stopWhatsAppStatusCheck()
  stopQRAutoRefresh()
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
  
  // Permitir avanzar solo si NO estamos en el último paso (3)
  if (currentStep.value < 3) {
    currentStep.value++
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

// Cargar configuración existente al montar el componente
onMounted(async () => {
  // 🏪 PRIMERO: Leer tipo de tienda del Welcome si existe
  const pendingStoreType = localStorage.getItem('pending_store_type')
  if (pendingStoreType) {
    config.store_type = pendingStoreType
    console.log('✅ Tipo de tienda cargado desde Welcome:', pendingStoreType)
  }

  // 🎯 SEGUNDO: Pre-llenar con datos del registro (localStorage)
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

  // TERCERO: Intentar cargar datos guardados del backend
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
      
      // 🏪 Solo sobrescribir store_type si NO viene de pending_store_type
      if (!pendingStoreType && settings.store_type) {
        config.store_type = settings.store_type
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
      store_type: config.store_type, // 🏪 Guardar tipo de tienda
      company_document: config.nit,
      company_phone: config.phone,
      company_email: config.email,
      company_address: config.address,
      company_logo: config.logo,
      invoice_footer_message: config.thankYouMessage,
      invoice_template: selectedTemplate.value
    })
    
    // 🧹 Limpiar pending_store_type después de guardarlo
    localStorage.removeItem('pending_store_type')
    console.log('✅ Tipo de tienda guardado en BD y limpiado de localStorage')
    
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
    // 🔥 CRÍTICO: Marcar en localStorage INMEDIATAMENTE como respaldo
    // Esto evita el bucle si el router guard se ejecuta antes de que BD responda
    localStorage.setItem('onboarding_completed', 'true')
    
    // Intentar guardar toda la configuración en una sola llamada
    try {
      await apiClient.put('/tenant/system-settings', {
        company_name: config.storeName,
        store_type: config.store_type, // 🏪 Guardar tipo de tienda
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
      
      // ✅ CRÍTICO: Recargar systemSettings para actualizar appStore
      const { appStore } = await import('@/store/appStore.js')
      await appStore.loadSystemSettings(true) // force = true para recargar
      
      // ✅ SOLUCIÓN: Recargar página para que el router guard lea el valor fresco de BD
      // El navigation guard se ejecuta antes de que Vue procese la reactividad
      window.location.href = '/pos'
      
    } catch (error) {
      // Si hay error 404 o 401, es porque aún no estamos autenticados
      // Guardamos en localStorage y continuamos de todos modos
      if (error.response && (error.response.status === 404 || error.response.status === 401)) {
        // Guardar en localStorage para aplicar después del login
        localStorage.setItem('pending_onboarding_config', JSON.stringify({
          company_name: config.storeName,
          store_type: config.store_type, // 🏪 Guardar tipo de tienda
          company_document: config.nit,
          company_phone: config.phone,
          company_email: config.email,
          company_address: config.address,
          company_logo: config.logo,
          invoice_footer_message: config.thankYouMessage,
          invoice_template: selectedTemplate.value,
          whatsapp_business_number: config.whatsappNumber
        }))
        
        // El flag de localStorage ya está puesto, redirigir al POS
        window.location.href = '/pos'
      } else {
        throw error
      }
    }
  } catch (error) {
    console.error('Error finalizando onboarding:', error)
    // 🔥 Revertir el flag si hay error real
    localStorage.removeItem('onboarding_completed')
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