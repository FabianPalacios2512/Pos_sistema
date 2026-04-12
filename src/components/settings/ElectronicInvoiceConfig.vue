<template>
  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <!-- CONFIGURACIÓN DE FACTURACIÓN ELECTRÓNICA DIAN                   -->
  <!-- Permite a cada comercio registrarse con sus datos fiscales          -->
  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Facturación Electrónica</h2>
        <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
          Configura tus datos fiscales para emitir facturas electrónicas validadas por la DIAN
        </p>
      </div>
      
      <!-- Badge de estado -->
      <div v-if="status">
        <span 
          :class="[
            'px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide',
            status.alanube.status === 'active' 
              ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800'
              : status.alanube.status === 'testing'
              ? 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-800'
              : 'bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border border-gray-200 dark:border-zinc-700'
          ]"
        >
          {{ 
            status.alanube.status === 'active' ? 'Habilitado DIAN' 
            : status.alanube.status === 'testing' ? 'En Proceso'
            : '⏸️ Sin Configurar'
          }}
        </span>
      </div>
    </div>

    <!-- Stepper de progreso -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4">
      <div class="flex items-center justify-between">
        <div 
          v-for="(step, index) in steps" 
          :key="index"
          class="flex items-center"
          :class="index < steps.length - 1 ? 'flex-1' : ''"
        >
          <div class="flex items-center">
            <div 
              :class="[
                'w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300',
                currentStep > index 
                  ? 'bg-emerald-500 text-white' 
                  : currentStep === index 
                  ? 'bg-blue-500 text-white ring-4 ring-blue-100 dark:ring-blue-900'
                  : 'bg-gray-200 dark:bg-zinc-800 text-gray-500 dark:text-zinc-500'
              ]"
            >
              <svg v-if="currentStep > index" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span v-else>{{ index + 1 }}</span>
            </div>
            <span 
              class="ml-3 text-sm font-medium hidden sm:inline"
              :class="currentStep >= index ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-zinc-500'"
            >
              {{ step.title }}
            </span>
          </div>
          
          <!-- Línea conectora -->
          <div 
            v-if="index < steps.length - 1" 
            class="flex-1 h-0.5 mx-4"
            :class="currentStep > index ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-zinc-700'"
          />
        </div>
      </div>
    </div>

    <!-- Contenido del paso actual -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
      
      <!-- ═══════════════════════════════════════════════════════════════ -->
      <!-- PASO 1: DATOS FISCALES                                         -->
      <!-- ═══════════════════════════════════════════════════════════════ -->
      <div v-if="currentStep === 0" class="p-6 space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-zinc-800">
          <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950 flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Datos Fiscales de tu Empresa</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400">Información requerida por la DIAN para facturación electrónica</p>
          </div>
        </div>

        <form @submit.prevent="saveFiscalData" class="space-y-5">
          <!-- Razón Social -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
              Razón Social / Nombre del Negocio *
            </label>
            <input 
              v-model="fiscalForm.company_name"
              type="text"
              required
              placeholder="Ej: Mi Tienda S.A.S"
              class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            />
          </div>

          <!-- NIT y DV -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                NIT (sin dígito de verificación) *
              </label>
              <input 
                v-model="fiscalForm.company_document"
                type="text"
                required
                placeholder="Ej: 900559088"
                @input="onNITChange"
                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                Dígito Verificación
              </label>
              <input 
                v-model="fiscalForm.company_dv"
                type="text"
                readonly
                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-900 text-gray-900 dark:text-zinc-200 text-center font-bold text-lg cursor-not-allowed"
              />
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Calculado automáticamente</p>
            </div>
          </div>

          <!-- NIT Formateado (preview) -->
          <div v-if="fiscalForm.company_document && fiscalForm.company_dv" class="bg-emerald-50 dark:bg-emerald-950 rounded-lg p-3 border border-emerald-100 dark:border-emerald-800">
            <p class="text-sm text-emerald-700 dark:text-emerald-400">
              <span class="font-medium">NIT Completo:</span> 
              {{ formatNIT(fiscalForm.company_document, fiscalForm.company_dv) }}
            </p>
          </div>

          <!-- Dirección -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
              Dirección *
            </label>
            <input 
              v-model="fiscalForm.company_address"
              type="text"
              required
              placeholder="Ej: Calle 123 #45-67, Local 101"
              class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            />
          </div>

          <!-- Ciudad -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                Ciudad *
              </label>
              <select 
                v-model="selectedCity"
                @change="onCityChange"
                required
                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              >
                <option value="">Seleccionar ciudad...</option>
                <option v-for="city in cities" :key="city.code" :value="city.code">
                  {{ city.name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                Régimen Tributario
              </label>
              <select 
                v-model="fiscalForm.tax_regime"
                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              >
                <option value="R-99-PN">Persona Natural - No Responsable IVA</option>
                <option value="R-49-PN">Persona Natural - Responsable IVA</option>
                <option value="R-49-RS">Régimen Simple</option>
                <option value="R-49-RG">Régimen Común</option>
              </select>
            </div>
          </div>

          <!-- Email y Teléfono -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                Email de Facturación *
              </label>
              <input 
                v-model="fiscalForm.company_email"
                type="email"
                required
                placeholder="facturacion@miempresa.com"
                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                Teléfono
              </label>
              <input 
                v-model="fiscalForm.company_phone"
                type="tel"
                placeholder="Ej: 3001234567"
                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              />
            </div>
          </div>

          <!-- Botón Guardar -->
          <div class="flex justify-end pt-4">
            <button 
              type="submit"
              :disabled="savingFiscal"
              class="px-6 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <svg v-if="savingFiscal" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ savingFiscal ? 'Guardando...' : 'Guardar y Continuar' }}</span>
              <svg v-if="!savingFiscal" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </button>
          </div>
        </form>
      </div>

      <!-- ═══════════════════════════════════════════════════════════════ -->
      <!-- PASO 2: REGISTRO EN ALANUBE                                    -->
      <!-- ═══════════════════════════════════════════════════════════════ -->
      <div v-if="currentStep === 1" class="p-6 space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-zinc-800">
          <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950 flex items-center justify-center">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Registro en Alanube</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400">Vincular tu empresa con el servicio de facturación electrónica</p>
          </div>
        </div>

        <div v-if="!status?.alanube.company_id" class="text-center py-8">
          <div class="w-20 h-20 mx-auto bg-purple-50 dark:bg-purple-950 rounded-2xl flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
            </svg>
          </div>
          <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Conectar con Alanube</h4>
          <p class="text-gray-600 dark:text-zinc-400 max-w-md mx-auto mb-6">
            Alanube es nuestro proveedor de facturación electrónica. Procesa tus facturas en menos de 3 segundos y valida automáticamente con la DIAN.
          </p>
          <button 
            @click="registerCompany"
            :disabled="registering"
            class="px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 mx-auto"
          >
            <svg v-if="registering" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ registering ? 'Registrando empresa...' : 'Registrar mi Empresa' }}</span>
          </button>
        </div>

        <div v-else class="text-center py-8">
          <div class="w-20 h-20 mx-auto bg-emerald-50 dark:bg-emerald-950 rounded-2xl flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">¡Empresa Registrada!</h4>
          <p class="text-gray-600 dark:text-zinc-400 mb-2">
            Tu empresa ya está conectada con Alanube
          </p>
          <p class="text-xs text-gray-500 dark:text-zinc-500 font-mono mb-6">
            ID: {{ status.alanube.company_id }}
          </p>
          <button 
            @click="currentStep = 2"
            class="px-6 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 flex items-center gap-2 mx-auto"
          >
            <span>Continuar al siguiente paso</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </button>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════════════════ -->
      <!-- PASO 3: HABILITACIÓN DIAN (TEST SET)                           -->
      <!-- ═══════════════════════════════════════════════════════════════ -->
      <div v-if="currentStep === 2" class="p-6 space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-zinc-800">
          <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950 flex items-center justify-center">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Habilitación DIAN</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400">Ejecutar set de pruebas para validar con la DIAN</p>
          </div>
        </div>

        <div v-if="status?.alanube.status !== 'active'" class="text-center py-8">
          <div class="w-20 h-20 mx-auto bg-amber-50 dark:bg-amber-950 rounded-2xl flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Ejecutar Set de Pruebas</h4>
          <p class="text-gray-600 dark:text-zinc-400 max-w-md mx-auto mb-6">
            La DIAN requiere que ejecutemos un set de pruebas para habilitar tu empresa como facturador electrónico. Este proceso es automático y toma unos segundos.
          </p>
          <button 
            @click="runTestSet"
            :disabled="runningTest"
            class="px-8 py-3 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 mx-auto"
          >
            <svg v-if="runningTest" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ runningTest ? 'Ejecutando pruebas...' : 'Iniciar Set de Pruebas' }}</span>
          </button>
        </div>

        <div v-else class="text-center py-8">
          <div class="w-20 h-20 mx-auto bg-emerald-50 dark:bg-emerald-950 rounded-2xl flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <h4 class="text-lg font-semibold text-emerald-600 dark:text-emerald-400 mb-2">¡Habilitado por la DIAN!</h4>
          <p class="text-gray-600 dark:text-zinc-400 mb-6">
            Tu empresa ya puede emitir facturas electrónicas válidas ante la DIAN
          </p>
          <button 
            @click="currentStep = 3"
            class="px-6 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 flex items-center gap-2 mx-auto"
          >
            <span>Activar Facturación Electrónica</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </button>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════════════════ -->
      <!-- PASO 4: ACTIVAR PROVEEDOR                                      -->
      <!-- ═══════════════════════════════════════════════════════════════ -->
      <div v-if="currentStep === 3" class="p-6 space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-zinc-800">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950 flex items-center justify-center">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">¡Listo para Facturar!</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400">Activa la facturación electrónica en tu POS</p>
          </div>
        </div>

        <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-6">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="font-semibold text-gray-900 dark:text-white">Facturación Electrónica DIAN</h4>
              <p class="text-sm text-gray-600 dark:text-zinc-400">Cada venta generará una factura validada por la DIAN con CUFE</p>
            </div>
            <div class="flex items-center gap-3">
              <span 
                :class="[
                  'text-sm font-medium',
                  status?.provider === 'alanube' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-zinc-500'
                ]"
              >
                {{ status?.provider === 'alanube' ? 'Activo' : 'Desactivado' }}
              </span>
              <button
                @click="toggleProvider"
                :disabled="togglingProvider"
                :class="[
                  'relative w-14 h-7 rounded-full transition-colors duration-300',
                  status?.provider === 'alanube' 
                    ? 'bg-emerald-500' 
                    : 'bg-gray-300 dark:bg-zinc-700'
                ]"
              >
                <span 
                  :class="[
                    'absolute top-0.5 w-6 h-6 bg-white rounded-full shadow transition-transform duration-300',
                    status?.provider === 'alanube' ? 'translate-x-7' : 'translate-x-0.5'
                  ]"
                />
              </button>
            </div>
          </div>
        </div>

        <!-- Resumen -->
        <div v-if="status?.provider === 'alanube'" class="bg-emerald-50 dark:bg-emerald-950/50 rounded-xl p-6 border border-emerald-100 dark:border-emerald-900">
          <h4 class="font-semibold text-emerald-700 dark:text-emerald-400 mb-3">Configuración Completa</h4>
          <ul class="space-y-2 text-sm text-emerald-600 dark:text-emerald-400">
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span><strong>Empresa:</strong> {{ status.company.name || 'Sin configurar' }}</span>
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span><strong>NIT:</strong> {{ formatNIT(status.company.nit || '', status.company.dv || '') }}</span>
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span><strong>Proveedor:</strong> Alanube (Validación ~2 segundos)</span>
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span><strong>Estado DIAN:</strong> Habilitado</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Navegación entre pasos -->
    <div v-if="currentStep > 0" class="flex justify-between">
      <button 
        @click="currentStep--"
        class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
        </svg>
        <span>Paso Anterior</span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import alanubeService, { type AlanubeStatus, type City } from '../../services/alanubeService';
import { useToast } from '../../composables/useToast.js';

const { showSuccess, showError, showWarning } = useToast();

// Estado
const status = ref<AlanubeStatus | null>(null);
const cities = ref<City[]>([]);
const loading = ref(true);
const savingFiscal = ref(false);
const registering = ref(false);
const runningTest = ref(false);
const togglingProvider = ref(false);
const selectedCity = ref('');

// Formulario fiscal
const fiscalForm = ref({
  company_name: '',
  company_document: '',
  company_dv: '',
  company_address: '',
  company_city_code: '',
  company_department_code: '',
  company_email: '',
  company_phone: '',
  tax_regime: 'R-99-PN'
});

// Pasos del wizard
const steps = [
  { title: 'Datos Fiscales' },
  { title: 'Registro Alanube' },
  { title: 'Habilitación DIAN' },
  { title: 'Activar' }
];

// Calcular paso actual basado en el estado
const currentStep = ref(0);

// Cargar estado inicial
onMounted(async () => {
  try {
    loading.value = true;
    
    // Cargar ciudades
    const citiesResponse = await alanubeService.getCities();
    if (citiesResponse.success) {
      cities.value = citiesResponse.data;
    }
    
    // Cargar estado de facturación
    const statusResponse = await alanubeService.getStatus();
    if (statusResponse.success) {
      status.value = statusResponse.data;
      
      // Llenar formulario con datos existentes
      if (status.value.company) {
        fiscalForm.value = {
          company_name: status.value.company.name || '',
          company_document: status.value.company.nit || '',
          company_dv: status.value.company.dv || '',
          company_address: status.value.company.address || '',
          company_city_code: status.value.company.city_code || '',
          company_department_code: status.value.company.department_code || '',
          company_email: status.value.company.email || '',
          company_phone: status.value.company.phone || '',
          tax_regime: status.value.company.tax_regime || 'R-99-PN'
        };
        
        // Seleccionar ciudad
        if (status.value.company.city_code) {
          selectedCity.value = status.value.company.city_code;
        }
      }
      
      // Determinar paso actual
      if (status.value.alanube.status === 'active') {
        currentStep.value = 3;
      } else if (status.value.alanube.company_id) {
        currentStep.value = 2;
      } else if (status.value.company.nit) {
        currentStep.value = 1;
      } else {
        currentStep.value = 0;
      }
    }
  } catch (error) {
    console.error('Error cargando configuración:', error);
    showError('Error al cargar configuración de facturación');
  } finally {
    loading.value = false;
  }
});

// Calcular DV cuando cambia el NIT
const onNITChange = () => {
  const nit = fiscalForm.value.company_document.replace(/[^0-9]/g, '');
  fiscalForm.value.company_document = nit;
  
  if (nit.length >= 8) {
    fiscalForm.value.company_dv = alanubeService.calculateDV(nit);
  } else {
    fiscalForm.value.company_dv = '';
  }
};

// Cuando cambia la ciudad
const onCityChange = () => {
  const city = cities.value.find((c: City) => c.code === selectedCity.value);
  if (city) {
    fiscalForm.value.company_city_code = city.code;
    fiscalForm.value.company_department_code = city.department;
  }
};

// Formatear NIT
const formatNIT = (nit: string, dv: string) => {
  if (!nit) return '';
  return alanubeService.formatNIT(nit, dv);
};

// Guardar datos fiscales
const saveFiscalData = async () => {
  try {
    savingFiscal.value = true;
    
    const response = await alanubeService.saveFiscalData(fiscalForm.value);
    
    if (response.success) {
      showSuccess('Datos fiscales guardados correctamente');
      currentStep.value = 1;
      
      // Actualizar estado
      const statusResponse = await alanubeService.getStatus();
      if (statusResponse.success) {
        status.value = statusResponse.data;
      }
    } else {
      showError(response.message || 'Error al guardar datos');
    }
  } catch (error: any) {
    console.error('Error guardando datos fiscales:', error);
    showError(error.response?.data?.message || 'Error al guardar datos fiscales');
  } finally {
    savingFiscal.value = false;
  }
};

// Registrar empresa en Alanube
const registerCompany = async () => {
  try {
    registering.value = true;
    
    const response = await alanubeService.registerCompany();
    
    if (response.success) {
      showSuccess('¡Empresa registrada en Alanube exitosamente!');
      
      // Actualizar estado
      const statusResponse = await alanubeService.getStatus();
      if (statusResponse.success) {
        status.value = statusResponse.data;
        currentStep.value = 2;
      }
    } else {
      showError(response.message || 'Error al registrar empresa');
    }
  } catch (error: any) {
    console.error('Error registrando empresa:', error);
    showError(error.response?.data?.message || 'Error al registrar empresa');
  } finally {
    registering.value = false;
  }
};

// Ejecutar set de pruebas DIAN
const runTestSet = async () => {
  try {
    runningTest.value = true;
    
    const response = await alanubeService.runTestSet();
    
    if (response.success && response.data?.status === 'ACCEPTED') {
      showSuccess('¡Empresa habilitada por la DIAN!');
      
      // Actualizar estado
      const statusResponse = await alanubeService.getStatus();
      if (statusResponse.success) {
        status.value = statusResponse.data;
        currentStep.value = 3;
      }
    } else if (response.success) {
      showWarning('Set de pruebas en proceso. Por favor espera unos minutos.');
    } else {
      showError(response.message || 'Error en set de pruebas');
    }
  } catch (error: any) {
    console.error('Error en set de pruebas:', error);
    showError(error.response?.data?.message || 'Error al ejecutar set de pruebas');
  } finally {
    runningTest.value = false;
  }
};

// Toggle proveedor
const toggleProvider = async () => {
  try {
    togglingProvider.value = true;
    
    const newProvider = status.value?.provider === 'alanube' ? 'none' : 'alanube';
    const response = await alanubeService.setProvider(newProvider);
    
    if (response.success) {
      showSuccess(response.message);
      
      // Actualizar estado
      const statusResponse = await alanubeService.getStatus();
      if (statusResponse.success) {
        status.value = statusResponse.data;
      }
    } else {
      showError(response.message || 'Error al cambiar proveedor');
    }
  } catch (error: any) {
    console.error('Error cambiando proveedor:', error);
    showError(error.response?.data?.message || 'Error al cambiar proveedor');
  } finally {
    togglingProvider.value = false;
  }
};
</script>
