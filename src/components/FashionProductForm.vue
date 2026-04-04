<template>
  <!-- Contenido del formulario - Diseño Fashion SaaS -->
  <div class="space-y-6">
    
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 1: INFORMACIÓN BÁSICA -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="space-y-5">
      
      <!-- Nombre del Producto - Protagonista -->
      <div>
        <label class="block text-xs font-bold text-gray-800 dark:text-zinc-200 mb-2 uppercase tracking-wider">
          Nombre del Producto <span class="text-rose-500">*</span>
        </label>
        <input 
          v-model="form.name"
          type="text" 
          required
          placeholder="Ej: Camiseta Polo Premium"
          class="w-full px-5 py-4 bg-white dark:bg-zinc-800 border-2 border-gray-300 dark:border-zinc-600 rounded-xl text-xl font-bold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 transition-all"
        >
      </div>

      <!-- Fila: Categoría + SKU + Proveedor -->
      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold text-gray-800 dark:text-zinc-200 mb-1.5 uppercase tracking-wider">Categoría <span class="text-rose-500">*</span></label>
          <select 
            v-model="form.category_id"
            @change="handleCategoryChange"
            class="w-full px-4 py-3 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-lg text-[15px] font-medium text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 transition-all"
          >
            <option value="" disabled>Seleccionar</option>
            <option value="__new__" class="font-medium text-blue-600">＋ Nueva</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-800 dark:text-zinc-200 mb-1.5 uppercase tracking-wider">SKU</label>
          <input 
            v-model="form.sku"
            type="text" 
            placeholder="Auto-generado"
            class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800/60 border border-gray-300 dark:border-zinc-600 rounded-lg text-[15px] text-gray-700 dark:text-zinc-300 font-mono placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 transition-all"
          >
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-800 dark:text-zinc-200 mb-1.5 uppercase tracking-wider">Proveedor</label>
          <select 
            v-model="form.supplier_id"
            @change="handleSupplierChange"
            class="w-full px-4 py-3 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-lg text-[15px] font-medium text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 transition-all"
          >
            <option :value="null">Sin proveedor</option>
            <option value="__new__" class="font-medium text-blue-600">＋ Nuevo</option>
            <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">{{ sup.name }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 2: MULTIMEDIA + DESCRIPCIÓN -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
      
      <!-- Área de Imágenes - 3 columnas (Protagonista) -->
      <div class="lg:col-span-3">
        <label class="block text-xs font-bold text-gray-800 dark:text-zinc-200 mb-2 uppercase tracking-wider">Galería de Imágenes</label>
        
        <!-- Dropzone Grande -->
        <div 
          @click="triggerFileInput"
          @dragover.prevent="isDragging = true"
          @dragleave.prevent="isDragging = false"
          @drop.prevent="handleDrop"
          :class="[
            'relative border-2 border-dashed rounded-2xl transition-all cursor-pointer',
            isDragging 
              ? 'border-slate-400 dark:border-zinc-500 bg-slate-50 dark:bg-zinc-800/80' 
              : 'border-gray-200 dark:border-zinc-700/80 hover:border-gray-300 dark:hover:border-zinc-600 hover:bg-gray-50/50 dark:hover:bg-zinc-800/20'
          ]"
        >
          <input 
            type="file" 
            ref="fileInput" 
            class="hidden" 
            multiple 
            accept="image/*"
            @change="handleFileChange"
          >
          
          <!-- Sin imágenes - Estado vacío grande -->
          <div v-if="form.images.length === 0" class="py-10 px-6">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 dark:bg-zinc-800 flex items-center justify-center mb-3 border border-gray-200/50 dark:border-zinc-700/50">
              <svg class="w-8 h-8 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-500 dark:text-zinc-400 text-center">Arrastra imágenes aquí</p>
            <p class="text-xs text-gray-400 dark:text-zinc-600 mt-1 text-center">o haz clic para seleccionar · PNG, JPG · Máx 5 fotos</p>
          </div>

          <!-- Con imágenes - Grid de miniaturas -->
          <div v-else class="p-3">
            <div class="grid grid-cols-5 gap-2.5">
              <div v-for="(img, index) in form.images" :key="img.preview" 
                   draggable="true"
                   @dragstart="onImageDragStart(index, $event)"
                   @dragover.prevent="onImageDragOver(index, $event)"
                   @dragleave="onImageDragLeave($event)"
                   @drop.prevent="onImageDrop(index, $event)"
                   @dragend="onImageDragEnd"
                   :class="[
                     'relative group aspect-square bg-gray-100 dark:bg-zinc-800 rounded-xl overflow-hidden border shadow-sm cursor-grab active:cursor-grabbing transition-all duration-200',
                     dragOverIndex === index ? 'border-blue-400 dark:border-blue-500 ring-2 ring-blue-400/30 scale-105' : 'border-gray-200 dark:border-zinc-700',
                     draggingIndex === index ? 'opacity-40 scale-95' : ''
                   ]">
                <img :src="img.preview" class="w-full h-full object-cover pointer-events-none" :class="{ 'opacity-40': deletingImageIndex === index }">
                
                <!-- Overlay de eliminación en progreso -->
                <div v-if="deletingImageIndex === index" 
                     class="absolute inset-0 bg-black/60 flex items-center justify-center">
                  <svg class="w-6 h-6 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </div>
                
                <!-- Botón eliminar -->
                <button 
                  v-if="deletingImageIndex !== index"
                  type="button"
                  @click.stop="removeImage(index)"
                  class="absolute top-1.5 right-1.5 w-6 h-6 bg-black/60 hover:bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all"
                  :disabled="deletingImageIndex !== null"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                
                <!-- Indicador de posición (drag hint) -->
                <div class="absolute top-1.5 left-1.5 w-6 h-6 bg-black/50 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all text-[10px] font-bold">
                  {{ index + 1 }}
                </div>
                
                <span v-if="index === 0" class="absolute bottom-0 left-0 right-0 bg-blue-600/90 text-white text-[9px] font-bold text-center py-0.5 uppercase tracking-wider">★ Principal</span>
              </div>
              <!-- Slot para agregar más -->
              <div v-if="form.images.length < 5" 
                   class="aspect-square rounded-xl border-2 border-dashed border-gray-200 dark:border-zinc-700 flex items-center justify-center text-gray-300 dark:text-zinc-600 hover:border-gray-300 dark:hover:border-zinc-600 hover:text-gray-400 dark:hover:text-zinc-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
              </div>
            </div>
            <p v-if="form.images.length > 1" class="text-[11px] text-gray-400 dark:text-zinc-500 mt-2 text-center">Arrastra para reordenar · La primera imagen será la principal</p>
          </div>
        </div>
      </div>

      <!-- Descripción - 2 columnas -->
      <div class="lg:col-span-2">
        <label class="block text-xs font-bold text-gray-800 dark:text-zinc-200 mb-2 uppercase tracking-wider">Descripción</label>
        <textarea 
          v-model="form.description"
          rows="5"
          placeholder="Describe el material, ajuste, instrucciones de cuidado..."
          class="w-full px-4 py-3.5 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-xl text-[15px] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 transition-all"
          style="height: 100%; min-height: 160px;"
        ></textarea>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 3: VARIANTES (Atributos + Generación) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
      
      <!-- Header de Atributos -->
      <div class="flex items-center justify-between mb-5">
        <div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">Atributos y Variantes</h3>
          <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">Define tallas, colores u otras opciones para este producto</p>
        </div>
        <button 
          type="button"
          @click="addOption"
          class="px-4 py-2.5 text-sm font-bold text-gray-700 dark:text-zinc-200 hover:text-gray-900 dark:hover:text-white bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 rounded-lg flex items-center gap-2 transition-all border border-gray-300 dark:border-zinc-600 shadow-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Añadir Atributo
        </button>
      </div>

      <!-- Estado Vacío -->
      <div v-if="form.options.length === 0" 
           class="text-center py-10 bg-gray-50/50 dark:bg-zinc-900/30 rounded-2xl border-2 border-dashed border-gray-300 dark:border-zinc-700">
        <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-zinc-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        <p class="text-sm font-semibold text-gray-500 dark:text-zinc-400">Sin atributos configurados</p>
        <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">El producto se guardará como simple</p>
      </div>

      <!-- Lista de Atributos -->
      <div v-else class="space-y-3">
        <div v-for="(option, index) in form.options" :key="index" 
             class="flex items-center gap-3 p-4 bg-white dark:bg-zinc-800/40 rounded-xl border border-gray-200 dark:border-zinc-700">
          
          <!-- Nombre del Atributo -->
          <input 
            v-model="option.name"
            type="text" 
            placeholder="Nombre"
            class="w-28 px-3 py-2.5 bg-gray-50 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-600 rounded-lg text-[15px] font-bold text-gray-900 dark:text-white text-center focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 outline-none transition-all flex-shrink-0"
          >

          <!-- Valores - Color Picker o Input Tags -->
          <div class="flex-1 min-w-0">
            <!-- Si es "Color" - Picker Inline -->
            <div v-if="isColorOption(option.name)" class="flex items-center gap-2.5 flex-wrap min-h-[48px] px-4 py-2.5 bg-gray-50 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-600 rounded-lg">
              <!-- Colores seleccionados -->
              <button
                type="button"
                v-for="val in option.values" 
                :key="val"
                @click="removeColorValue(index, val)"
                :style="{ backgroundColor: val }"
                class="w-9 h-9 rounded-lg border-2 border-white dark:border-zinc-700 shadow-md hover:scale-110 transition-transform relative group ring-1 ring-black/10 dark:ring-white/10 flex-shrink-0"
                :title="`Quitar ${val}`"
              >
                <div class="absolute inset-0 bg-black/50 rounded-md opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
              </button>
              
              <!-- Botón Agregar Color -->
              <label class="w-9 h-9 rounded-lg bg-gradient-to-br from-red-400 via-green-400 to-blue-400 flex items-center justify-center cursor-pointer hover:scale-110 transition-transform shadow-md border-2 border-white dark:border-zinc-700 flex-shrink-0">
                <input 
                  type="color"
                  @change="addColorFromPicker(index, $event)"
                  class="sr-only"
                >
                <svg class="w-4 h-4 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
              </label>
            </div>

            <!-- Si NO es "Color" - Input con Tags (chips) -->
            <div v-else class="flex flex-wrap items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-600 rounded-lg min-h-[48px]">
              <span 
                v-for="(val, vIndex) in option.values" 
                :key="vIndex"
                class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 border border-gray-300 dark:border-zinc-500 shadow-sm flex-shrink-0"
              >
                {{ val }}
                <button type="button" @click="removeValue(index, vIndex)" class="ml-2 text-gray-400 hover:text-rose-500 transition-colors text-base leading-none">×</button>
              </span>
              <input 
                type="text"
                v-model="option.tempValue"
                @keydown.enter.prevent="addOptionValue(index)"
                @keydown.backspace="handleBackspace(index)"
                placeholder="Escribir y Enter ↵"
                class="flex-1 min-w-[140px] bg-transparent border-none focus:ring-0 text-[15px] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 py-1"
              >
            </div>
          </div>

          <!-- Botón Eliminar Atributo -->
          <button 
            type="button" 
            @click="removeOption(index)"
            class="p-2.5 text-gray-400 dark:text-zinc-500 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all flex-shrink-0"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div>
      </div>

      <!-- Botón Generar / Continuar -->
      <div class="mt-6">
        <button 
          type="button"
          ref="generateButton"
          @click="handleContinueOrGenerate"
          :class="[
            'w-full py-4 rounded-xl text-[15px] font-bold transition-all flex items-center justify-center gap-2.5',
            hasValidOptions 
              ? 'bg-[#0f172a] dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white shadow-lg shadow-slate-400/30 dark:shadow-slate-900/50' 
              : 'bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-300 dark:border-zinc-600'
          ]"
        >
          <svg v-if="!hasValidOptions" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          <span>{{ buttonText }}</span>
        </button>
        <p class="text-center text-xs text-gray-500 dark:text-zinc-500 mt-2.5">
          {{ !hasValidOptions ? 'Sin atributos = Producto simple' : 'Genera la matriz para asignar precios y stock' }}
        </p>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 4: FORMULARIO SIMPLE (Sin Variantes) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <section v-if="showSimpleForm" ref="simpleFormSection" class="animate-fade-in">
      <div class="bg-blue-50/60 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/30 rounded-xl p-4 mb-5">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-bold text-blue-900 dark:text-blue-300">Producto Simple</h3>
            <p class="text-xs text-blue-700/80 dark:text-blue-400/60">Sin variantes de talla o color</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold text-gray-800 dark:text-zinc-200 mb-2 uppercase tracking-wider">
            Costo <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[15px] text-gray-500 dark:text-zinc-400 font-semibold">$</span>
            <input 
              v-model.number="simpleProduct.cost"
              type="number"
              step="0.01"
              min="0"
              required
              class="w-full pl-9 pr-4 py-3 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-xl text-[15px] font-semibold text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 tabular-nums transition-all"
              placeholder="0"
            >
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-800 dark:text-zinc-200 mb-2 uppercase tracking-wider">
            Precio Venta <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[15px] text-emerald-700 dark:text-emerald-400 font-semibold">$</span>
            <input 
              v-model.number="simpleProduct.price"
              type="number"
              step="0.01"
              min="0"
              required
              class="w-full pl-9 pr-4 py-3 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-xl text-[15px] font-bold text-emerald-700 dark:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 tabular-nums transition-all"
              placeholder="0"
            >
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-800 dark:text-zinc-200 mb-2 uppercase tracking-wider">
            Stock <span class="text-rose-500">*</span>
          </label>
          <input 
            v-model.number="simpleProduct.stock"
            type="number"
            min="0"
            required
            class="w-full px-4 py-3 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-xl text-[15px] font-bold text-gray-900 dark:text-white text-center focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 tabular-nums transition-all"
            placeholder="0"
          >
        </div>
      </div>

      <!-- Indicador de Margen -->
      <div v-if="simpleProduct.cost > 0 && simpleProduct.price > 0" 
           class="mt-4 flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-zinc-800/40 rounded-xl border border-gray-200 dark:border-zinc-700">
        <span class="text-sm font-semibold text-gray-600 dark:text-zinc-400">Margen de Ganancia</span>
        <span class="text-base font-bold tabular-nums" :class="profitMargin >= 20 ? 'text-emerald-700 dark:text-emerald-400' : profitMargin >= 10 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400'">
          {{ profitMargin }}%
        </span>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 5: TABLA DE VARIANTES (Generada) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <section v-if="form.variants.length > 0 && !showSimpleForm" ref="variantsSection" class="animate-fade-in">
      <div class="flex items-center justify-between mb-5">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
          <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          Variantes
          <span class="px-2.5 py-0.5 text-xs font-bold text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-950 rounded-md border border-purple-200 dark:border-purple-800">{{ form.variants.length }}</span>
        </h3>
      </div>

      <!-- Tabla -->
      <div class="overflow-hidden border border-gray-300 dark:border-zinc-700 rounded-2xl shadow-md">
        <table class="min-w-full divide-y divide-gray-300 dark:divide-zinc-700">
          <thead class="bg-gray-100 dark:bg-zinc-900">
            <tr>
              <th class="px-5 py-4 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Variante</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider w-44">SKU</th>
              <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider w-40">Costo</th>
              <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider w-40">Precio</th>
              <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider w-32">Stock</th>
              <th class="px-3 py-4 w-14"></th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-zinc-900/50 divide-y divide-gray-200 dark:divide-zinc-800">
            <tr v-for="(variant, index) in form.variants" :key="index" class="hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors">
              <td class="px-5 py-4">
                <div class="flex items-center gap-2">
                  <template v-for="(part, pIndex) in getVariantParts(variant)" :key="pIndex">
                    <span v-if="pIndex > 0" class="text-gray-300 dark:text-zinc-600">/</span>
                    <span v-if="part.isColor" 
                          class="w-6 h-6 rounded-full border-2 border-white dark:border-zinc-700 shadow-sm ring-1 ring-black/10 dark:ring-white/10 flex-shrink-0"
                          :style="{ backgroundColor: part.value }"
                          :title="part.value">
                    </span>
                    <span v-else class="text-[15px] font-bold text-gray-900 dark:text-white">{{ part.value }}</span>
                  </template>
                </div>
              </td>
              <td class="px-4 py-3.5">
                <input 
                  v-model="variant.sku"
                  type="text" 
                  class="w-full px-3.5 py-2.5 text-sm font-mono border border-gray-300 dark:border-zinc-600 rounded-lg bg-gray-50 dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 transition-all"
                >
              </td>
              <td class="px-4 py-3.5">
                <div class="relative">
                  <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[15px] text-gray-500 dark:text-zinc-400 font-medium">$</span>
                  <input 
                    v-model.number="variant.cost_price"
                    type="number"
                    step="1"
                    min="0"
                    class="w-full pl-8 pr-3 py-2.5 text-[15px] font-semibold text-center border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 tabular-nums transition-all"
                  >
                </div>
              </td>
              <td class="px-4 py-3.5">
                <div class="relative">
                  <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[15px] text-emerald-700 dark:text-emerald-400 font-semibold">$</span>
                  <input 
                    v-model.number="variant.price"
                    type="number" 
                    step="1"
                    min="0"
                    class="w-full pl-8 pr-3 py-2.5 text-[15px] font-bold text-center border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-emerald-700 dark:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 tabular-nums transition-all"
                  >
                </div>
              </td>
              <td class="px-4 py-3.5">
                <input 
                  v-model.number="variant.stock"
                  type="number" 
                  min="0"
                  class="w-full px-3.5 py-2.5 text-[15px] font-bold border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-center focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 focus:border-blue-500 dark:focus:border-blue-400 tabular-nums transition-all"
                >
              </td>
              <td class="px-3 py-3.5 text-center">
                <button 
                  type="button" 
                  @click="removeVariant(index)"
                  class="p-2.5 text-gray-400 dark:text-zinc-500 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
  <!-- FIN contenedor principal -->
</template>

<script setup>
import { reactive, computed, ref, watch } from 'vue'
import { useToast } from '../composables/useToast'
import api from '../services/api'

const { showWarning, showSuccess, showError } = useToast()

const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  },
  suppliers: {
    type: Array,
    default: () => []
  },
  editingProduct: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['save', 'cancel', 'create-category', 'create-supplier'])

// Refs para scroll y color pickers
const generateButton = ref(null)
const variantsSection = ref(null)
const simpleFormSection = ref(null)
const colorInputRefs = ref({})

// Estado para producto simple (sin variantes)
const showSimpleForm = ref(false)
const simpleProduct = reactive({
  cost: 0,
  price: 0,
  stock: 0
})

// Computed: Calcular margen de ganancia para producto simple
const profitMargin = computed(() => {
  if (simpleProduct.cost <= 0 || simpleProduct.price <= 0) return 0
  const margin = ((simpleProduct.price - simpleProduct.cost) / simpleProduct.price) * 100
  return Math.round(margin * 100) / 100
})

// Estado Reactivo del Formulario
const form = reactive({
  id: null, // ID del producto (para edición)
  name: '',
  sku: '',
  description: '',
  category_id: '',
  supplier_id: null,
  options: [
    { name: 'Talla', values: [], tempValue: '' },
    { name: 'Color', values: [], tempValue: '' }
  ],
  variants: [],
  images: []
})

// 🔄 Cargar datos cuando se edita un producto
watch(() => props.editingProduct, (product) => {
  if (product) {
    form.id = product.id || null
    form.name = product.name || ''
    form.sku = product.sku || ''
    form.description = product.description || ''
    form.category_id = product.category_id || ''
    form.supplier_id = product.supplier_id || null
    
    // Cargar opciones
    if (product.options && Array.isArray(product.options)) {
      form.options = product.options.map(opt => {
        // 🛡️ Asegurar que values sea un array de strings, no objetos
        let cleanValues = []
        if (opt.values && Array.isArray(opt.values)) {
          cleanValues = opt.values
            .map(v => {
              // Si v es string, usarlo directamente
              if (typeof v === 'string') return v
              // Si v es objeto con .value string, usar eso
              if (v && typeof v.value === 'string') return v.value
              // Si v es objeto, intentar JSON.stringify pero evitarlo
              if (typeof v === 'object') return ''
              return String(v)
            })
            .filter(v => v) // Filtrar vacíos
        }
        
        return {
          name: opt.name || '',
          values: cleanValues,
          tempValue: ''
        }
      })
    } else {
      // Reset a opciones por defecto si no hay
      form.options = [
        { name: 'Talla', values: [], tempValue: '' },
        { name: 'Color', values: [], tempValue: '' }
      ]
    }
    
    // Cargar variantes
    if (product.variants && Array.isArray(product.variants)) {
      form.variants = product.variants.map(variant => {
        // 🔧 Procesar options correctamente
        let processedOptions = []
        
        if (variant.options) {
          // Si options es un array de objetos con estructura {name, value}
          if (Array.isArray(variant.options)) {
            processedOptions = variant.options.map(opt => {
              // Si el objeto tiene propiedades 'name' y 'value' como strings, úsalas
              if (opt && typeof opt.name === 'string' && typeof opt.value === 'string') {
                return { name: opt.name, value: opt.value }
              }
              // Si tiene value como string
              if (opt && typeof opt.value === 'string') {
                return { name: opt.name || '', value: opt.value }
              }
              // 🛡️ FIX: Si value es un objeto (viene del backend mal parseado), extraer solo el value interno
              if (opt && typeof opt.value === 'object' && opt.value !== null) {
                const innerValue = opt.value.value || ''
                return { name: opt.name || '', value: typeof innerValue === 'string' ? innerValue : '' }
              }
              // Si el opt mismo es un string
              if (typeof opt === 'string') {
                return { name: '', value: opt }
              }
              // Fallback: devolver vacío para evitar [object Object]
              return { name: '', value: '' }
            }).filter(o => o.value) // Filtrar opciones vacías
          }
        }
        
        // Si options_summary existe y es string JSON, parsearlo
        if (variant.options_summary) {
          try {
            const summary = typeof variant.options_summary === 'string' 
              ? JSON.parse(variant.options_summary) 
              : variant.options_summary
              
            if (Array.isArray(summary)) {
              processedOptions = summary.map(s => ({ name: s.name, value: s.value }))
            }
          } catch (e) {
            console.warn('Error parsing options_summary:', e)
          }
        }
        
        return {
          sku: variant.sku,
          price: parseFloat(variant.price || 0),
          cost_price: parseFloat(variant.cost_price || variant.cost || 0),
          stock: parseInt(variant.stock || 0),
          active: variant.active !== false,
          options: processedOptions
        }
      })
    } else {
      form.variants = []
    }
    
    // Detectar si es producto simple (1 variante sin opciones O sin variantes)
    const hasNoVariants = form.variants.length === 0
    const isSimpleProduct = form.variants.length === 1 && 
                           (!form.variants[0].options || form.variants[0].options.length === 0)
    
    if (isSimpleProduct) {
      // Es un producto simple - cargar datos en el formulario simple
      const variant = form.variants[0]
      
      simpleProduct.cost = variant.cost_price || variant.cost || 0
      simpleProduct.price = variant.price
      simpleProduct.stock = variant.stock
      showSimpleForm.value = true
      
      // Limpiar las opciones (no tiene atributos)
      form.options = [
        { name: 'Talla', values: [], tempValue: '' },
        { name: 'Color', values: [], tempValue: '' }
      ]
    } else if (hasNoVariants) {
      // No tiene variantes - intentar cargar desde product padre
      simpleProduct.cost = parseFloat(product.cost_price || 0)
      simpleProduct.price = parseFloat(product.sale_price || product.price || 0)
      simpleProduct.stock = parseInt(product.current_stock || product.stock || 0)
      showSimpleForm.value = true
      
      // Limpiar opciones
      form.options = [
        { name: 'Talla', values: [], tempValue: '' },
        { name: 'Color', values: [], tempValue: '' }
      ]
    } else if (form.variants.length > 1) {
      // Producto con múltiples variantes - ocultar formulario simple
      showSimpleForm.value = false
    } else {
      // No hay variantes
      showSimpleForm.value = false
    }
    
    // Cargar imágenes - Primero las de la galería, luego la principal
    form.images = []
    
    // Helper para construir URL de imagen correctamente
    const buildImageUrl = (url) => {
      if (!url) return null
      if (url.startsWith('data:image')) return url
      if (url.startsWith('http://') || url.startsWith('https://')) return url
      // URLs relativas - agregar origen del navegador
      if (url.startsWith('/storage')) {
        return `${window.location.origin}${url}`
      }
      return `${window.location.origin}/storage/${url}`
    }
    
    // 1. Cargar imágenes de la galería (product.images)
    if (product.images && Array.isArray(product.images) && product.images.length > 0) {
      product.images.forEach(img => {
        const imageUrl = img.url || img.image_url
        if (imageUrl) {
          form.images.push({
            preview: buildImageUrl(imageUrl),
            file: null,
            id: img.id || null
          })
        }
      })
    }
    
    // 2. Si no hay imágenes en galería pero hay image_url principal, usarla
    if (form.images.length === 0 && product.image_url) {
      form.images.push({
        preview: buildImageUrl(product.image_url),
        file: null
      })
    }
  } else {
    // Limpiar formulario cuando no hay producto (modo crear)
    form.id = null
    form.name = ''
    form.sku = ''
    form.description = ''
    form.category_id = ''
    form.supplier_id = null
    form.options = [
      { name: 'Talla', values: [], tempValue: '' },
      { name: 'Color', values: [], tempValue: '' }
    ]
    form.variants = []
    form.images = []
    showSimpleForm.value = false
    simpleProduct.cost = 0
    simpleProduct.price = 0
    simpleProduct.stock = 0
  }
}, { immediate: true })

const handleCategoryChange = (event) => {
  if (event.target.value === '__new__') {
    form.category_id = '' // Reset selection
    emit('create-category')
  }
}

const setCategory = (id) => {
  form.category_id = id
}

const handleSupplierChange = (event) => {
  if (event.target.value === '__new__') {
    form.supplier_id = null // Reset selection
    emit('create-supplier')
  }
}

// --- Lógica de Opciones ---

const addOption = () => {
  form.options.push({
    name: '',
    values: [],
    tempValue: ''
  })
}

const removeOption = (index) => {
  form.options.splice(index, 1)
}

const addOptionValue = (optionIndex) => {
  const option = form.options[optionIndex]
  const val = option.tempValue.trim()
  
  if (val && !option.values.includes(val)) {
    option.values.push(val)
    option.tempValue = '' // Limpiar input
  }
}

const removeValue = (optionIndex, valueIndex) => {
  form.options[optionIndex].values.splice(valueIndex, 1)
}

// 🎨 Funciones para colores personalizados (solo hex)
const isColorOption = (optionName) => {
  return optionName && (
    optionName.toLowerCase().includes('color') || 
    optionName.toLowerCase().includes('colour')
  )
}

// Trigger color picker nativo
const triggerColorPicker = (index) => {
  if (colorInputRefs.value[index]) {
    colorInputRefs.value[index].click()
  }
}

// Agregar color desde picker (se guarda el HEX directamente)
const addColorFromPicker = (optionIndex, event) => {
  const hexColor = event.target.value.toUpperCase()
  const option = form.options[optionIndex]
  
  // Agregar el hex si no existe ya
  if (!option.values.includes(hexColor)) {
    option.values.push(hexColor)
  }
}

const removeColorValue = (optionIndex, colorHex) => {
  const option = form.options[optionIndex]
  const index = option.values.indexOf(colorHex)
  if (index > -1) {
    option.values.splice(index, 1)
  }
}

const handleBackspace = (optionIndex) => {
  const option = form.options[optionIndex]
  if (option.tempValue === '' && option.values.length > 0) {
    option.values.pop()
  }
}

// --- Lógica Cartesiana (Generación de Variantes) ---

// Computed: Determinar si hay atributos con valores
const hasValidOptions = computed(() => {
  return form.options.some(opt => opt.values.length > 0 && opt.name.trim() !== '')
})

// Computed: Texto dinámico del botón
const buttonText = computed(() => {
  if (!hasValidOptions.value) {
    return 'Continuar como Producto Simple'
  }
  return form.variants.length > 0 ? 'Regenerar Tabla de Variantes' : 'Generar Tabla de Variantes'
})

const generateVariants = () => {
  // Filtrar opciones que tengan valores
  const validOptions = form.options.filter(opt => opt.values.length > 0 && opt.name.trim() !== '')
  
  if (validOptions.length === 0) {
    showWarning('⚠️ Debes agregar al menos un atributo con valores (ej: Talla: S, M, L)', 4000)
    return
  }

  // Ocultar formulario simple cuando se generan variantes
  showSimpleForm.value = false

  // Algoritmo de Producto Cartesiano
  const cartesian = (args) => {
    const r = [], max = args.length - 1
    function helper(arr, i) {
      for (let j = 0, l = args[i].values.length; j < l; j++) {
        const a = arr.slice(0) // clonar
        a.push({ 
          name: args[i].name, 
          value: args[i].values[j] 
        })
        if (i === max) r.push(a)
        else helper(a, i + 1)
      }
    }
    helper([], 0)
    return r
  }

  const combinations = cartesian(validOptions)

  // Mapear combinaciones a estructura de variante
  form.variants = combinations.map(combo => {
    // Generar nombre legible: "S / Rojo"
    const variantName = combo.map(c => c.value).join(' / ')
    
    // Generar SKU sugerido: "NOM-S-ROJ"
    const skuSuffix = combo.map(c => c.value.substring(0, 3).toUpperCase()).join('-')
    // Usar SKU del formulario si existe, sino usar las primeras 3 letras del nombre
    const baseSku = form.sku ? form.sku : (form.name ? form.name.substring(0, 3).toUpperCase() : 'PROD')
    
    return {
      options: combo, // Guardar la combinación exacta
      sku: `${baseSku}-${skuSuffix}`,
      cost_price: 0,
      price: 0,
      stock: 0,
      active: true
    }
  })
}

// 🚀 Generar variantes CON scroll automático
const generateVariantsWithScroll = () => {
  generateVariants()
  
  // Scroll suave a la sección de variantes después de un pequeño delay
  setTimeout(() => {
    if (variantsSection.value) {
      variantsSection.value.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
      })
    }
  }, 100)
}

// 🎯 Manejar click en el botón dinámico
const handleContinueOrGenerate = () => {
  if (!hasValidOptions.value) {
    // No hay atributos, mostrar formulario simple
    showSimpleForm.value = true
    
    // Scroll suave al formulario simple
    setTimeout(() => {
      if (simpleFormSection.value) {
        simpleFormSection.value.scrollIntoView({ 
          behavior: 'smooth', 
          block: 'start' 
        })
      }
    }, 100)
  } else {
    // Hay atributos, generar variantes
    showSimpleForm.value = false
    generateVariantsWithScroll()
  }
}

const removeVariant = (index) => {
  form.variants.splice(index, 1)
}

const getVariantName = (variant) => {
  if (!variant.options || !Array.isArray(variant.options)) {
    return variant.sku || 'Variante'
  }
  
  return variant.options
    .filter(o => o && o.value && typeof o.value === 'string')
    .map(o => o.value)
    .join(' / ') || variant.sku || 'Variante'
}

const isHexColor = (val) => /^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/.test(val)

const getVariantParts = (variant) => {
  if (!variant.options || !Array.isArray(variant.options)) {
    return [{ value: variant.sku || 'Variante', isColor: false }]
  }
  return variant.options
    .filter(o => o && o.value && typeof o.value === 'string')
    .map(o => ({ value: o.value, isColor: isHexColor(o.value) }))
}

// --- Lógica de Imágenes ---
const fileInput = ref(null)
const isDragging = ref(false)
const draggingIndex = ref(null)
const dragOverIndex = ref(null)

const triggerFileInput = () => {
  fileInput.value.click()
}

// Drag-and-drop reorder handlers
const onImageDragStart = (index, event) => {
  draggingIndex.value = index
  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('text/plain', index.toString())
}

const onImageDragOver = (index, event) => {
  if (draggingIndex.value === null || draggingIndex.value === index) return
  event.dataTransfer.dropEffect = 'move'
  dragOverIndex.value = index
}

const onImageDragLeave = (event) => {
  // Only clear if we're actually leaving the element
  if (!event.currentTarget.contains(event.relatedTarget)) {
    dragOverIndex.value = null
  }
}

const onImageDrop = (targetIndex, event) => {
  const sourceIndex = draggingIndex.value
  if (sourceIndex === null || sourceIndex === targetIndex) {
    dragOverIndex.value = null
    return
  }
  const moved = form.images.splice(sourceIndex, 1)[0]
  form.images.splice(targetIndex, 0, moved)
  draggingIndex.value = null
  dragOverIndex.value = null
}

const onImageDragEnd = () => {
  draggingIndex.value = null
  dragOverIndex.value = null
}

const handleFileChange = (event) => {
  const files = Array.from(event.target.files)
  processFiles(files)
}

const handleDrop = (event) => {
  isDragging.value = false
  const files = Array.from(event.dataTransfer.files)
  processFiles(files)
}

const processFiles = (files) => {
  files.forEach(file => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader()
      reader.onload = (e) => {
        form.images.push({
          file: file,
          preview: e.target.result
        })
      }
      reader.readAsDataURL(file)
    }
  })
}

// Estado para tracking de eliminación
const deletingImageIndex = ref(null)

const removeImage = async (index) => {
  const image = form.images[index]
  
  // Si la imagen tiene ID (existe en el servidor), eliminarla del backend
  if (image && image.id) {
    deletingImageIndex.value = index
    
    try {
      const data = await api.delete(`/products/images/${image.id}`)
      
      if (data.success) {
        // Eliminar del array local
        form.images.splice(index, 1)
        showSuccess('✅ Imagen eliminada correctamente')
      } else {
        showError(data.message || 'Error al eliminar la imagen')
      }
    } catch (error) {
      console.error('Error eliminando imagen:', error)
      showError(error.message || 'Error al eliminar la imagen del servidor')
    } finally {
      deletingImageIndex.value = null
    }
  } else {
    // Si es una imagen nueva (solo local), simplemente eliminarla del array
    form.images.splice(index, 1)
  }
}

// --- Submit ---

const handleSubmit = () => {
  // Validaciones básicas
  if (!form.name) {
    showWarning('⚠️ El nombre del producto es requerido', 3000)
    return
  }
  
  if (!form.category_id) {
    showWarning('⚠️ Debes seleccionar una categoría', 3000)
    return
  }
  
  // ✅ Manejar productos simples (sin variantes o con 1 variante sin opciones)
  if (showSimpleForm.value) {
    // Validar campos del producto simple
    if (simpleProduct.price <= 0) {
      showWarning('⚠️ El precio de venta debe ser mayor a 0', 3000)
      return
    }
    
    if (simpleProduct.stock < 0) {
      showWarning('⚠️ El stock no puede ser negativo', 3000)
      return
    }
    
    // Actualizar o crear variante simple con los datos del formulario
    form.variants = [{
      sku: form.sku || `SKU-${Date.now()}`,
      price: simpleProduct.price,
      cost_price: simpleProduct.cost,
      stock: simpleProduct.stock,
      active: true,
      options: []
    }]
  }
  
  // Si no hay variantes ni formulario simple mostrado, crear variante por defecto
  if (form.variants.length === 0) {
    form.variants = [{
      sku: form.sku || `SKU-${Date.now()}`,
      price: 0,
      cost_price: 0,
      stock: 0,
      active: true,
      options: []
    }]
  }

  // Preparar payload final
  // Detectar si es producto simple o con variantes
  const isSimpleProduct = form.variants.length === 1 && 
                         (!form.variants[0].options || form.variants[0].options.length === 0)
  
  const payload = {
    ...form,
    type: isSimpleProduct ? 'simple' : 'variable', // CRÍTICO: distinguir tipo
    store_type: 'fashion', // ✅ SIEMPRE marcar como producto de moda
    // Limpiar campos temporales y solo enviar opciones que tengan valores
    options: form.options
      .filter(o => o.values && o.values.length > 0) // Solo opciones con valores
      .map(o => ({
        name: o.name,
        values: o.values
      }))
  }

  emit('save', payload)
}

// Exponer métodos y datos al componente padre
defineExpose({
  setCategory,
  handleSubmit,
  form
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>