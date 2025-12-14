<template>
  <!-- Modal Overlay -->
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen" class="fixed inset-0 z-[60] overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="handleClose"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-6xl bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden transform transition-all">
            
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900/80">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div>
                  <h2 class="text-lg font-bold text-gray-900 dark:text-white">Importar Productos desde Excel</h2>
                  <p class="text-sm text-gray-500 dark:text-zinc-400">{{ stepDescription }}</p>
                </div>
              </div>
              
              <!-- Close Button -->
              <button @click="handleClose" 
                      class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <!-- Progress Steps -->
            <div class="px-6 py-3 bg-gray-50/50 dark:bg-zinc-900/50 border-b border-gray-100 dark:border-zinc-800/50">
              <div class="flex items-center justify-center gap-2">
                <div v-for="(step, index) in steps" :key="index" class="flex items-center">
                  <div class="flex items-center gap-2">
                    <div :class="[
                      'w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold transition-all',
                      currentStep > index + 1 ? 'bg-emerald-500 text-white' :
                      currentStep === index + 1 ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 dark:ring-indigo-900/50' :
                      'bg-gray-200 dark:bg-zinc-700 text-gray-500 dark:text-zinc-400'
                    ]">
                      <svg v-if="currentStep > index + 1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                      <span v-else>{{ index + 1 }}</span>
                    </div>
                    <span :class="[
                      'text-sm font-medium hidden sm:inline',
                      currentStep >= index + 1 ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-500'
                    ]">{{ step }}</span>
                  </div>
                  <div v-if="index < steps.length - 1" :class="[
                    'w-12 h-0.5 mx-2',
                    currentStep > index + 1 ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-zinc-700'
                  ]"></div>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="p-6 max-h-[60vh] overflow-y-auto">
              
              <!-- Step 1: Upload File -->
              <div v-if="currentStep === 1" class="space-y-6">
                <!-- Dropzone -->
                <div 
                  @dragover.prevent="isDragging = true"
                  @dragleave="isDragging = false"
                  @drop.prevent="handleDrop"
                  :class="[
                    'border-2 border-dashed rounded-2xl p-12 text-center transition-all cursor-pointer',
                    isDragging ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 
                    'border-gray-300 dark:border-zinc-700 hover:border-indigo-400 dark:hover:border-indigo-500 hover:bg-gray-50 dark:hover:bg-zinc-800/50'
                  ]"
                  @click="triggerFileInput"
                >
                  <input type="file" ref="fileInput" @change="handleFileSelect" accept=".xlsx,.xls,.csv" class="hidden" />
                  
                  <div class="flex flex-col items-center gap-4">
                    <div :class="[
                      'w-16 h-16 rounded-2xl flex items-center justify-center transition-colors',
                      isDragging ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-gray-100 dark:bg-zinc-800'
                    ]">
                      <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                      </svg>
                    </div>
                    
                    <div>
                      <p class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ isDragging ? '¡Suelta el archivo aquí!' : 'Arrastra tu archivo Excel aquí' }}
                      </p>
                      <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">
                        o haz clic para seleccionar un archivo
                      </p>
                    </div>
                    
                    <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-zinc-500">
                      <span class="px-2 py-1 bg-gray-100 dark:bg-zinc-800 rounded">.xlsx</span>
                      <span class="px-2 py-1 bg-gray-100 dark:bg-zinc-800 rounded">.xls</span>
                      <span class="px-2 py-1 bg-gray-100 dark:bg-zinc-800 rounded">.csv</span>
                      <span class="text-gray-300 dark:text-zinc-600">|</span>
                      <span>Máx 10MB</span>
                    </div>
                  </div>
                </div>

                <!-- Download Template -->
                <div class="flex items-center justify-center">
                  <button @click="downloadTemplate" class="flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Descargar plantilla de ejemplo</span>
                  </button>
                </div>

                <!-- Uploaded File Info -->
                <div v-if="uploadedFile" class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/40 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </div>
                      <div>
                        <p class="font-medium text-emerald-900 dark:text-emerald-100">{{ uploadedFile.name }}</p>
                        <p class="text-sm text-emerald-600 dark:text-emerald-400">{{ formatFileSize(uploadedFile.size) }} • {{ totalRows }} productos detectados</p>
                      </div>
                    </div>
                    <button @click="clearFile" class="p-2 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 rounded-lg transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Step 2: Column Mapping -->
              <div v-else-if="currentStep === 2" class="space-y-6">
                <!-- AI Analysis Badge -->
                <div v-if="aiAnalysis" class="flex items-center justify-between p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/40 rounded-lg flex items-center justify-center">
                      <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium text-indigo-900 dark:text-indigo-100">
                        IA detectó las columnas automáticamente
                      </p>
                      <p class="text-sm text-indigo-600 dark:text-indigo-400">
                        Confianza: {{ aiAnalysis.confidence }}% • Método: {{ aiAnalysis.method === 'groq_ai' ? 'Groq AI' : 'Detección local' }}
                      </p>
                    </div>
                  </div>
                  <span :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    aiAnalysis.confidence >= 80 ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300' :
                    aiAnalysis.confidence >= 50 ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300' :
                    'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300'
                  ]">
                    {{ aiAnalysis.confidence >= 80 ? 'Alta' : aiAnalysis.confidence >= 50 ? 'Media' : 'Baja' }}
                  </span>
                </div>

                <!-- Warnings -->
                <div v-if="aiAnalysis?.warnings?.length" class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                  <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                      <p class="font-medium text-amber-900 dark:text-amber-100">Advertencias</p>
                      <ul class="mt-1 text-sm text-amber-700 dark:text-amber-300 space-y-1">
                        <li v-for="warning in aiAnalysis.warnings" :key="warning">• {{ warning }}</li>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Column Mapping Table -->
                <div class="bg-white dark:bg-zinc-800/50 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden">
                  <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase">Columna en Excel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase">Ejemplo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase">Mapear a</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                      <tr v-for="header in headers" :key="header" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-4 py-3">
                          <span class="font-medium text-gray-900 dark:text-white">{{ header }}</span>
                        </td>
                        <td class="px-4 py-3">
                          <span class="text-sm text-gray-500 dark:text-zinc-400 font-mono">{{ getSampleValue(header) }}</span>
                        </td>
                        <td class="px-4 py-3">
                          <select v-model="columnMapping[header]" 
                                  class="w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                            <option value="ignore">-- Ignorar --</option>
                            <option v-for="field in systemFields" :key="field.key" :value="field.key">
                              {{ field.label }} {{ field.required ? '*' : '' }}
                            </option>
                          </select>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Step 3: Preview & Edit -->
              <div v-else-if="currentStep === 3" class="space-y-4">
                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                  <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ previewStats.total }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Total</p>
                  </div>
                  <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ previewStats.valid }}</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400">Válidos</p>
                  </div>
                  <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ previewStats.with_warnings }}</p>
                    <p class="text-xs text-amber-600 dark:text-amber-400">Advertencias</p>
                  </div>
                  <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ previewStats.invalid }}</p>
                    <p class="text-xs text-red-600 dark:text-red-400">Con errores</p>
                  </div>
                </div>

                <!-- Preview Table -->
                <div class="bg-white dark:bg-zinc-800/50 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden">
                  <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                    <table class="w-full">
                      <thead class="bg-gray-50 dark:bg-zinc-800 sticky top-0">
                        <tr>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400 w-10">#</th>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400">Estado</th>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400">Imagen</th>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400">Nombre *</th>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400">Precio Venta *</th>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400">Costo</th>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400">Stock</th>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400">SKU</th>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400">Categoría</th>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400 w-10"></th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                        <tr v-for="(product, index) in previewData" :key="index" 
                            :class="[
                              'transition-colors',
                              !product.validation.is_valid ? 'bg-red-50/50 dark:bg-red-900/10' :
                              product.validation.warnings.length ? 'bg-amber-50/50 dark:bg-amber-900/10' :
                              'hover:bg-gray-50 dark:hover:bg-zinc-800/50'
                            ]">
                          <td class="px-3 py-2 text-xs text-gray-500 dark:text-zinc-500">{{ product.row_number }}</td>
                          <td class="px-3 py-2">
                            <div class="flex items-center gap-1">
                              <span v-if="!product.validation.is_valid" class="w-5 h-5 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center" title="Con errores">
                                <svg class="w-3 h-3 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                              </span>
                              <span v-else-if="product.validation.warnings.length" class="w-5 h-5 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center" title="Advertencias">
                                <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"></path>
                                </svg>
                              </span>
                              <span v-else class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                                <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                              </span>
                            </div>
                          </td>
                          <!-- Columna Imagen -->
                          <td class="px-3 py-2">
                            <div class="flex items-center gap-1">
                              <div v-if="product.mapped_data.image_url" 
                                   class="relative group w-10 h-10 rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-700">
                                <img :src="product.mapped_data.image_url" 
                                     alt="Imagen producto"
                                     class="w-full h-full object-cover"
                                     @error="(e) => e.target.style.display='none'" />
                                <button @click="removeImageUrl(index)" 
                                        class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                  </svg>
                                </button>
                              </div>
                              <button v-else
                                      @click="openImageModal(index)"
                                      class="w-10 h-10 rounded-lg border-2 border-dashed border-gray-300 dark:border-zinc-600 hover:border-indigo-400 dark:hover:border-indigo-500 flex items-center justify-center text-gray-400 dark:text-zinc-500 hover:text-indigo-500 transition-colors"
                                      title="Añadir imagen">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                              </button>
                            </div>
                          </td>
                          <td class="px-3 py-2">
                            <input v-model="product.mapped_data.name" type="text" 
                                   class="w-full px-2 py-1 text-sm bg-transparent border border-transparent hover:border-gray-300 dark:hover:border-zinc-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded text-gray-900 dark:text-white" />
                          </td>
                          <td class="px-3 py-2">
                            <input v-model.number="product.mapped_data.sale_price" type="number" step="0.01"
                                   class="w-24 px-2 py-1 text-sm bg-transparent border border-transparent hover:border-gray-300 dark:hover:border-zinc-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded text-gray-900 dark:text-white text-right" />
                          </td>
                          <td class="px-3 py-2">
                            <input v-model.number="product.mapped_data.cost_price" type="number" step="0.01"
                                   class="w-24 px-2 py-1 text-sm bg-transparent border border-transparent hover:border-gray-300 dark:hover:border-zinc-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded text-gray-900 dark:text-white text-right" />
                          </td>
                          <td class="px-3 py-2">
                            <input v-model.number="product.mapped_data.current_stock" type="number"
                                   class="w-20 px-2 py-1 text-sm bg-transparent border border-transparent hover:border-gray-300 dark:hover:border-zinc-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded text-gray-900 dark:text-white text-right" />
                          </td>
                          <td class="px-3 py-2">
                            <input v-model="product.mapped_data.sku" type="text"
                                   class="w-24 px-2 py-1 text-sm bg-transparent border border-transparent hover:border-gray-300 dark:hover:border-zinc-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded text-gray-900 dark:text-white" />
                          </td>
                          <td class="px-3 py-2">
                            <input v-model="product.mapped_data.category" type="text"
                                   class="w-28 px-2 py-1 text-sm bg-transparent border border-transparent hover:border-gray-300 dark:hover:border-zinc-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded text-gray-900 dark:text-white" />
                          </td>
                          <td class="px-3 py-2">
                            <button @click="removeProduct(index)" class="p-1 text-gray-400 hover:text-red-500 dark:hover:text-red-400 rounded transition-colors">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                              </svg>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- Step 4: Importing -->
              <div v-else-if="currentStep === 4" class="flex flex-col items-center justify-center py-12">
                <div v-if="isImporting" class="text-center">
                  <div class="w-16 h-16 border-4 border-indigo-200 dark:border-indigo-800 border-t-indigo-600 dark:border-t-indigo-400 rounded-full animate-spin mx-auto"></div>
                  <p class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Importando productos...</p>
                  <p class="text-sm text-gray-500 dark:text-zinc-400">Esto puede tomar unos segundos</p>
                </div>
                
                <div v-else-if="importResult" class="text-center space-y-4 w-full max-w-md">
                  <div :class="[
                    'w-20 h-20 rounded-full flex items-center justify-center mx-auto',
                    importResult.success ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-red-100 dark:bg-red-900/40'
                  ]">
                    <svg v-if="importResult.success" class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg v-else class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </div>
                  
                  <div>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                      {{ importResult.success ? '¡Importación completada!' : 'Error en la importación' }}
                    </p>
                    <p class="text-gray-500 dark:text-zinc-400">{{ importResult.message }}</p>
                  </div>

                  <div v-if="importResult.stats" class="grid grid-cols-3 gap-3 mt-6">
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-3">
                      <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ importResult.stats.imported }}</p>
                      <p class="text-xs text-emerald-600 dark:text-emerald-400">Importados</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-3">
                      <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ importResult.stats.skipped }}</p>
                      <p class="text-xs text-amber-600 dark:text-amber-400">Omitidos</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-3">
                      <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ importResult.stats.errors }}</p>
                      <p class="text-xs text-red-600 dark:text-red-400">Errores</p>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900/80">
              <button v-if="currentStep > 1 && currentStep < 4"
                      @click="previousStep"
                      class="px-4 py-2 text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                ← Anterior
              </button>
              <div v-else></div>

              <div class="flex items-center gap-3">
                <button @click="handleClose"
                        class="px-4 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-medium rounded-xl transition-colors">
                  {{ currentStep === 4 && importResult ? 'Cerrar' : 'Cancelar' }}
                </button>
                
                <button v-if="currentStep < 4"
                        @click="nextStep"
                        :disabled="!canProceed || isLoading"
                        :class="[
                          'px-6 py-2.5 font-semibold rounded-xl shadow-lg transition-all flex items-center gap-2',
                          canProceed && !isLoading ? 
                            'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-400/40 dark:shadow-indigo-900/50' : 
                            'bg-gray-300 dark:bg-zinc-700 text-gray-500 dark:text-zinc-500 cursor-not-allowed'
                        ]">
                  <svg v-if="isLoading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                  <span>{{ nextButtonText }}</span>
                  <svg v-if="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
    
    <!-- Modal de Imagen -->
    <Transition name="modal">
      <div v-if="showImageModal" class="fixed inset-0 z-[70] overflow-y-auto">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeImageModal"></div>
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-md bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden">
            <!-- Header -->
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 dark:border-zinc-800">
              <h3 class="font-semibold text-gray-900 dark:text-white">Añadir imagen del producto</h3>
              <button @click="closeImageModal" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            
            <!-- Content -->
            <div class="p-5 space-y-4">
              <p class="text-sm text-gray-500 dark:text-zinc-400">
                Pega o escribe la URL de la imagen del producto (formato: https://...)
              </p>
              
              <div class="space-y-2">
                <label class="text-sm font-medium text-gray-700 dark:text-zinc-300">URL de imagen</label>
                <input 
                  v-model="tempImageUrl"
                  type="url"
                  @paste="handleImagePaste"
                  placeholder="https://ejemplo.com/imagen.jpg"
                  class="w-full px-3 py-2.5 text-sm bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500"
                />
              </div>
              
              <!-- Preview -->
              <div v-if="tempImageUrl" class="mt-4">
                <p class="text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Vista previa</p>
                <div class="flex justify-center p-4 bg-gray-50 dark:bg-zinc-800/50 rounded-lg border border-gray-200 dark:border-zinc-700">
                  <img :src="tempImageUrl" 
                       alt="Vista previa"
                       class="max-w-full max-h-40 rounded-lg object-contain"
                       @error="(e) => e.target.parentElement.innerHTML = '<span class=\'text-sm text-red-500\'>Error al cargar imagen</span>'" />
                </div>
              </div>
            </div>
            
            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 px-5 py-3 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900/80">
              <button @click="closeImageModal" class="px-4 py-2 text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                Cancelar
              </button>
              <button @click="saveImageUrl" 
                      :disabled="!tempImageUrl.trim()"
                      :class="[
                        'px-4 py-2 font-semibold rounded-lg transition-colors',
                        tempImageUrl.trim() ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gray-200 dark:bg-zinc-700 text-gray-400 dark:text-zinc-500 cursor-not-allowed'
                      ]">
                Guardar
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import api from '@/services/api'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'imported'])

// State
const currentStep = ref(1)
const steps = ['Subir archivo', 'Mapear columnas', 'Revisar datos', 'Importar']

const isDragging = ref(false)
const isLoading = ref(false)
const isImporting = ref(false)

const fileInput = ref(null)
const uploadedFile = ref(null)
const importId = ref(null)
const totalRows = ref(0)

const headers = ref([])
const sampleData = ref([])
const columnMapping = ref({})
const aiAnalysis = ref(null)

const previewData = ref([])
const previewStats = ref({ total: 0, valid: 0, with_warnings: 0, invalid: 0 })

const importResult = ref(null)

// System fields for mapping
const systemFields = [
  { key: 'name', label: 'Nombre del producto', required: true },
  { key: 'sale_price', label: 'Precio de venta', required: true },
  { key: 'cost_price', label: 'Precio de costo', required: false },
  { key: 'current_stock', label: 'Stock actual', required: false },
  { key: 'sku', label: 'Código SKU', required: false },
  { key: 'barcode', label: 'Código de barras', required: false },
  { key: 'category', label: 'Categoría', required: false },
  { key: 'description', label: 'Descripción', required: false },
  { key: 'min_stock', label: 'Stock mínimo', required: false },
  { key: 'wholesale_price', label: 'Precio mayorista', required: false },
  { key: 'image_url', label: 'URL de imagen', required: false },
]

// Estado para modal de imagen
const showImageModal = ref(false)
const editingImageIndex = ref(null)
const tempImageUrl = ref('')

// Métodos para manejo de imagen
const openImageModal = (index) => {
  editingImageIndex.value = index
  tempImageUrl.value = previewData.value[index]?.mapped_data?.image_url || ''
  showImageModal.value = true
}

const closeImageModal = () => {
  showImageModal.value = false
  editingImageIndex.value = null
  tempImageUrl.value = ''
}

const saveImageUrl = () => {
  if (editingImageIndex.value !== null && tempImageUrl.value.trim()) {
    previewData.value[editingImageIndex.value].mapped_data.image_url = tempImageUrl.value.trim()
  }
  closeImageModal()
}

const handleImagePaste = (event) => {
  const clipboardData = event.clipboardData || window.clipboardData
  const pastedText = clipboardData.getData('text')
  
  // Verificar si es una URL de imagen
  if (pastedText && (pastedText.startsWith('http://') || pastedText.startsWith('https://'))) {
    tempImageUrl.value = pastedText
  }
}

const removeImageUrl = (index) => {
  if (previewData.value[index]) {
    previewData.value[index].mapped_data.image_url = null
  }
}

// Computed
const stepDescription = computed(() => {
  switch (currentStep.value) {
    case 1: return 'Sube tu archivo Excel o CSV con tus productos'
    case 2: return 'Verifica que las columnas se mapearon correctamente'
    case 3: return 'Revisa y edita los productos antes de importar'
    case 4: return isImporting.value ? 'Importando productos...' : 'Proceso completado'
    default: return ''
  }
})

const canProceed = computed(() => {
  switch (currentStep.value) {
    case 1: return uploadedFile.value !== null
    case 2: {
      const hasName = Object.values(columnMapping.value).includes('name')
      const hasPrice = Object.values(columnMapping.value).includes('sale_price')
      return hasName && hasPrice
    }
    case 3: return previewData.value.some(p => p.validation.is_valid)
    default: return false
  }
})

const nextButtonText = computed(() => {
  switch (currentStep.value) {
    case 1: return isLoading.value ? 'Analizando...' : 'Analizar archivo'
    case 2: return isLoading.value ? 'Procesando...' : 'Ver preview'
    case 3: return `Importar ${previewStats.value.valid + previewStats.value.with_warnings} productos`
    default: return 'Siguiente'
  }
})

// Methods
const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    processFile(file)
  }
}

const handleDrop = (event) => {
  isDragging.value = false
  const file = event.dataTransfer.files[0]
  if (file) {
    processFile(file)
  }
}

const processFile = async (file) => {
  // Validate file type
  const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv']
  const validExtensions = ['.xlsx', '.xls', '.csv']
  
  const extension = file.name.substring(file.name.lastIndexOf('.')).toLowerCase()
  if (!validExtensions.includes(extension)) {
    alert('Por favor sube un archivo Excel (.xlsx, .xls) o CSV (.csv)')
    return
  }

  uploadedFile.value = file
  isLoading.value = true

  try {
    const formData = new FormData()
    formData.append('file', file)

    console.log('Enviando archivo al servidor...')
    
    // Usar fetch directamente para FormData (api.post convierte a JSON)
    const token = localStorage.getItem('authToken')
    const fetchHeaders = {
      'Accept': 'application/json'
    }
    if (token) {
      fetchHeaders['Authorization'] = `Bearer ${token}`
    }
    
    const fetchResponse = await fetch('/api/excel-import/upload', {
      method: 'POST',
      headers: fetchHeaders,
      body: formData  // FormData se envía sin Content-Type para que el browser lo establezca
    })
    
    const response = { data: await fetchResponse.json() }

    console.log('Respuesta del servidor:', response)
    console.log('Respuesta data:', response.data)

    // Verificar que la respuesta tenga la estructura esperada
    if (!response.data) {
      throw new Error('El servidor no devolvió datos')
    }

    if (response.data.success) {
      importId.value = response.data.import_id
      totalRows.value = response.data.total_rows
      headers.value = response.data.headers || []
      sampleData.value = response.data.sample_data || []
      
      // Verificar que ai_analysis exista
      if (response.data.ai_analysis) {
        columnMapping.value = response.data.ai_analysis.column_mapping || {}
        aiAnalysis.value = response.data.ai_analysis
      } else {
        // Si no hay AI analysis, crear uno básico
        columnMapping.value = {}
        aiAnalysis.value = { method: 'none', confidence: 0 }
      }
    } else {
      throw new Error(response.data.message || 'Error al procesar el archivo')
    }
  } catch (error) {
    console.error('Error completo:', error)
    console.error('Error response:', error.response)
    console.error('Error data:', error.response?.data)
    
    let errorMessage = 'Error desconocido'
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    } else if (error.response?.data?.error) {
      errorMessage = error.response.data.error
    } else if (error.message) {
      errorMessage = error.message
    }
    
    alert('Error al procesar el archivo: ' + errorMessage)
    clearFile()
  } finally {
    isLoading.value = false
  }
}

const clearFile = () => {
  uploadedFile.value = null
  importId.value = null
  totalRows.value = 0
  headers.value = []
  sampleData.value = []
  columnMapping.value = {}
  aiAnalysis.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const getSampleValue = (header) => {
  const headerIndex = headers.value.indexOf(header)
  if (headerIndex >= 0 && sampleData.value.length > 0) {
    return sampleData.value[0][headerIndex] || '-'
  }
  return '-'
}

const downloadTemplate = async () => {
  try {
    const response = await fetch('/api/excel-import/template')
    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'plantilla_productos_105pos.csv')
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Error downloading template:', error)
  }
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const nextStep = async () => {
  if (currentStep.value === 1) {
    currentStep.value = 2
  } else if (currentStep.value === 2) {
    await generatePreview()
  } else if (currentStep.value === 3) {
    await importProducts()
  }
}

const previousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

const generatePreview = async () => {
  isLoading.value = true

  try {
    // Usar fetch directamente para consistencia
    const token = localStorage.getItem('authToken')
    const fetchHeaders = {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
    if (token) {
      fetchHeaders['Authorization'] = `Bearer ${token}`
    }
    
    const fetchResponse = await fetch('/api/excel-import/preview', {
      method: 'POST',
      headers: fetchHeaders,
      body: JSON.stringify({
        import_id: importId.value,
        column_mapping: columnMapping.value
      })
    })
    
    const response = await fetchResponse.json()
    console.log('Preview response:', response)

    if (response.success) {
      previewData.value = response.preview_data
      previewStats.value = response.stats
      currentStep.value = 3
    } else {
      throw new Error(response.message || 'Error al generar preview')
    }
  } catch (error) {
    console.error('Error generating preview:', error)
    alert('Error al generar preview: ' + (error.message || 'Error desconocido'))
  } finally {
    isLoading.value = false
  }
}

const removeProduct = (index) => {
  previewData.value.splice(index, 1)
  recalculateStats()
}

const recalculateStats = () => {
  let valid = 0, withWarnings = 0, invalid = 0

  previewData.value.forEach(product => {
    if (product.validation.is_valid) {
      if (product.validation.warnings.length > 0) {
        withWarnings++
      } else {
        valid++
      }
    } else {
      invalid++
    }
  })

  previewStats.value = {
    total: previewData.value.length,
    valid,
    with_warnings: withWarnings,
    invalid
  }
}

const importProducts = async () => {
  currentStep.value = 4
  isImporting.value = true

  try {
    // Filter only valid products
    const productsToImport = previewData.value
      .filter(p => p.validation.is_valid)
      .map(p => p.mapped_data)

    // Usar fetch directamente para consistencia
    const token = localStorage.getItem('authToken')
    const fetchHeaders = {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
    if (token) {
      fetchHeaders['Authorization'] = `Bearer ${token}`
    }
    
    const fetchResponse = await fetch('/api/excel-import/import', {
      method: 'POST',
      headers: fetchHeaders,
      body: JSON.stringify({
        import_id: importId.value,
        products: productsToImport
      })
    })
    
    const response = await fetchResponse.json()
    console.log('Import response:', response)

    importResult.value = response
    emit('imported', response)

  } catch (error) {
    console.error('Error importing products:', error)
    importResult.value = {
      success: false,
      message: error.response?.data?.message || 'Error durante la importación'
    }
  } finally {
    isImporting.value = false
  }
}

const handleClose = () => {
  // If in progress, cancel
  if (importId.value && currentStep.value < 4) {
    fetch('/api/excel-import/cancel', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ import_id: importId.value })
    }).catch(() => {})
  }
  
  resetState()
  emit('close')
}

const resetState = () => {
  currentStep.value = 1
  uploadedFile.value = null
  importId.value = null
  totalRows.value = 0
  headers.value = []
  sampleData.value = []
  columnMapping.value = {}
  aiAnalysis.value = null
  previewData.value = []
  previewStats.value = { total: 0, valid: 0, with_warnings: 0, invalid: 0 }
  importResult.value = null
  isLoading.value = false
  isImporting.value = false
}

// Watch for modal open/close
watch(() => props.isOpen, (newVal) => {
  if (!newVal) {
    resetState()
  }
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from > div:last-child,
.modal-leave-to > div:last-child {
  transform: scale(0.95);
}
</style>
