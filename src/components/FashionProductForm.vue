<template>
  <!-- Contenido del formulario sin header ni footer -->
  <div class="space-y-6">
    
    <!-- Grid 2 Columnas: Izquierda (Info) | Derecha (Multimedia) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
      <!-- COLUMNA IZQUIERDA: Información del Producto (2/3) -->
      <div class="lg:col-span-2 space-y-6">
        
        <!-- Información Básica -->
        <div class="space-y-4">
            <!-- Nombre -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1.5">
                Nombre del Producto <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.name"
                type="text" 
                required
                placeholder="Ej: Camiseta Polo Premium"
                class="w-full px-3 py-2 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-md text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-1 focus:ring-gray-400 dark:focus:ring-zinc-600 focus:border-gray-400 dark:focus:border-zinc-600 transition-all"
              >
            </div>

            <!-- Categoría y SKU en fila -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1.5">Categoría *</label>
                <select 
                  v-model="form.category_id"
                  @change="handleCategoryChange"
                  class="w-full px-3 py-2 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-md text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 dark:focus:ring-zinc-600"
                >
                  <option value="" disabled>Seleccionar...</option>
                  <option value="__new__" class="font-medium">+ Nueva Categoría</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1.5">SKU (Opcional)</label>
                <input 
                  v-model="form.sku"
                  type="text" 
                  placeholder="Auto"
                  class="w-full px-3 py-2 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-md text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-1 focus:ring-gray-400 dark:focus:ring-zinc-600"
                >
              </div>
            </div>

            <!-- Proveedor -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1.5">Proveedor (Opcional)</label>
              <select 
                v-model="form.supplier_id"
                @change="handleSupplierChange"
                class="w-full px-3 py-2 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-md text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 dark:focus:ring-zinc-600"
              >
                <option :value="null">Sin proveedor</option>
                <option value="__new__" class="font-medium">+ Nuevo Proveedor</option>
                <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">{{ sup.name }}</option>
              </select>
            </div>

            <!-- Descripción -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1.5">Descripción</label>
              <textarea 
                v-model="form.description"
                rows="3"
                placeholder="Detalles del material, ajuste, etc."
                class="w-full px-3 py-2 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-md text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 resize-none focus:outline-none focus:ring-1 focus:ring-gray-400 dark:focus:ring-zinc-600"
              ></textarea>
            </div>
          </div>
        </div>
        
        <!-- COLUMNA DERECHA: Multimedia (1/3) -->
        <div class="lg:col-span-1">
          <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-2">Imágenes</label>
          
          <!-- Dropzone Minimalista -->
          <div 
            @click="triggerFileInput"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            :class="[
              'border border-dashed rounded-md p-6 text-center transition-all cursor-pointer h-[200px] flex flex-col items-center justify-center gap-2',
              isDragging 
                ? 'border-gray-400 bg-gray-50 dark:bg-zinc-800' 
                : 'border-gray-300 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800/50'
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
            
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            
            <div>
              <p class="text-xs text-gray-600 dark:text-zinc-400">Arrastra o haz click</p>
              <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-0.5">JPG, PNG • Máx 5</p>
            </div>
          </div>

          <!-- Grid de Imágenes -->
          <div v-if="form.images.length > 0" class="grid grid-cols-3 gap-2 mt-3">
            <div v-for="(img, index) in form.images" :key="index" class="relative group aspect-square bg-gray-100 dark:bg-zinc-800 rounded overflow-hidden border border-gray-200 dark:border-zinc-700">
              <img :src="img.preview" class="w-full h-full object-cover">
              <button 
                type="button"
                @click.stop="removeImage(index)"
                class="absolute top-1 right-1 w-5 h-5 bg-black/60 hover:bg-black/80 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
              >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
          </div>
        </div>
    </div>
    <!-- FIN Grid 2 Columnas -->

    <!-- Variantes (Fuera del Grid) -->
    <div class="space-y-4">
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <h3 class="text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Atributos del Producto</h3>
            <button 
              type="button"
              @click="addOption"
              class="text-xs font-medium text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white flex items-center gap-1"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
              Añadir Atributo
            </button>
          </div>

          <!-- Lista de Atributos -->
          <div v-if="form.options.length === 0" class="text-center py-8 text-gray-400 dark:text-zinc-500 text-xs flex flex-col items-center gap-2 bg-gray-50 dark:bg-zinc-900/50 rounded-lg border border-dashed border-gray-200 dark:border-zinc-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <span>No hay atributos. Agrega "Talla" o "Color"</span>
          </div>

          <div v-for="(option, index) in form.options" :key="index" class="bg-white dark:bg-zinc-900 p-4 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm">
            <div class="flex items-start gap-3">
              <!-- Nombre del Atributo - MÁS GRANDE -->
              <div class="w-36 flex-shrink-0">
                <input 
                  v-model="option.name"
                  type="text" 
                  placeholder="Talla / Color"
                  class="w-full px-3 py-3 bg-gray-50 dark:bg-zinc-800 border-2 border-gray-200 dark:border-zinc-700 rounded-lg text-sm font-semibold text-gray-900 dark:text-white focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:border-slate-400 dark:focus:border-slate-600 outline-none transition-all"
                >
              </div>

              <!-- Valores - Color Picker o Input Normal -->
              <div class="flex-1">
                <!-- Si es "Color" mostrar picker directo + círculos -->
                <div v-if="isColorOption(option.name)" class="space-y-2">
                  <!-- Input oculto + botón visual -->
                  <div class="relative">
                    <input 
                      :ref="el => colorInputRefs[index] = el"
                      type="color"
                      @change="addColorFromPicker(index, $event)"
                      class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    >
                    <button
                      type="button"
                      @click="triggerColorPicker(index)"
                      class="w-full px-3 py-2 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 border border-gray-200 dark:border-zinc-700 hover:border-gray-400 dark:hover:border-zinc-600 rounded-md flex items-center justify-center gap-2 transition-all group"
                    >
                      <div class="w-7 h-7 rounded-md bg-gray-900 dark:bg-gray-700 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                      </div>
                      <span class="text-xs font-medium text-gray-600 dark:text-zinc-300 group-hover:text-gray-900 dark:group-hover:text-white">Seleccionar Color</span>
                    </button>
                  </div>
                  
                  <!-- Colores seleccionados - Solo círculos -->
                  <div v-if="option.values.length > 0" class="flex flex-wrap gap-1.5">
                    <button
                      type="button"
                      v-for="val in option.values" 
                      :key="val"
                      @click="removeColorValue(index, val)"
                      :style="{ backgroundColor: val }"
                      class="w-9 h-9 rounded-md border border-gray-200 dark:border-zinc-700 shadow-sm hover:shadow-md hover:scale-110 transition-all group relative"
                      :title="val"
                    >
                      <!-- X para eliminar al hover -->
                      <div class="absolute inset-0 bg-black/60 rounded-md opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Si NO es "Color" mostrar input tradicional - MÁS GRANDE -->
                <div v-else class="flex flex-wrap gap-2 p-3 bg-gray-50 dark:bg-zinc-800 border-2 border-gray-200 dark:border-zinc-700 rounded-lg min-h-[48px]">
                  <span 
                    v-for="(val, vIndex) in option.values" 
                    :key="vIndex"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-200 border-2 border-gray-300 dark:border-zinc-600 shadow-sm"
                  >
                    {{ val }}
                    <button type="button" @click="removeValue(index, vIndex)" class="ml-2 text-gray-500 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 text-lg font-bold">×</button>
                  </span>
                  <input 
                    type="text"
                    v-model="option.tempValue"
                    @keydown.enter.prevent="addOptionValue(index)"
                    @keydown.backspace="handleBackspace(index)"
                    placeholder="Escribe y Enter..."
                    class="flex-1 bg-transparent border-none focus:ring-0 text-sm font-medium text-gray-900 dark:text-white min-w-[120px] p-1"
                  >
                </div>
              </div>

              <!-- Botón Eliminar -->
              <button 
                type="button" 
                @click="removeOption(index)"
                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-all"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Botón Generar Variantes - MÁS GRANDE -->
        <button 
          type="button"
          ref="generateButton"
          @click="generateVariantsWithScroll"
          :disabled="form.options.length === 0"
          class="w-full py-4 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-base font-black rounded-xl shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3 transform hover:scale-[1.02] active:scale-[0.98]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
          <span class="uppercase tracking-wide">Generar Variantes</span>
        </button>
      </div>

      <!-- Matriz de Variantes Generadas -->
      <section v-if="form.variants.length > 0" ref="variantsSection" class="px-6 pb-6 space-y-3 animate-fade-in">
        <h3 class="text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wide">
          Variantes Generadas <span class="text-gray-400">({{ form.variants.length }})</span>
        </h3>

        <!-- Tabla Limpia -->
        <div class="overflow-x-auto border border-gray-200 dark:border-zinc-800 rounded-md">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800">
            <thead class="bg-gray-50 dark:bg-zinc-900">
              <tr>
                <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold text-gray-600 dark:text-zinc-400 uppercase">Variante</th>
                <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold text-gray-600 dark:text-zinc-400 uppercase w-32">SKU</th>
                <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold text-gray-600 dark:text-zinc-400 uppercase w-24">Costo</th>
                <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold text-gray-600 dark:text-zinc-400 uppercase w-24">Precio</th>
                <th scope="col" class="px-3 py-2 text-left text-[10px] font-semibold text-gray-600 dark:text-zinc-400 uppercase w-20">Stock</th>
                <th scope="col" class="px-3 py-2 text-center text-[10px] font-semibold text-gray-600 dark:text-zinc-400 uppercase w-12"></th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
              <tr v-for="(variant, index) in form.variants" :key="index" class="hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors">
                <td class="px-3 py-2">
                  <span class="text-xs font-medium text-gray-900 dark:text-white">
                    {{ getVariantName(variant) }}
                  </span>
                </td>
                <td class="px-3 py-1.5">
                  <input 
                    v-model="variant.sku"
                    type="text" 
                    class="w-full px-2 py-1 text-xs border border-gray-200 dark:border-zinc-700 rounded bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400"
                  >
                </td>
                <td class="px-3 py-1.5">
                  <div class="relative">
                    <span class="absolute left-2 top-1 text-[10px] text-gray-400">$</span>
                    <input 
                      v-model.number="variant.cost"
                      type="number"
                      step="0.01"
                      min="0"
                      placeholder="0.00"
                      class="w-full pl-5 pr-2 py-1 text-xs border border-gray-200 dark:border-zinc-700 rounded bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400"
                    >
                  </div>
                </td>
                <td class="px-3 py-1.5">
                  <div class="relative">
                    <span class="absolute left-2 top-1 text-[10px] text-gray-400">$</span>
                    <input 
                      v-model.number="variant.price"
                      type="number" 
                      class="w-full pl-5 pr-2 py-1 text-xs border border-gray-200 dark:border-zinc-700 rounded bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400"
                    >
                  </div>
                </td>
                <td class="px-3 py-1.5">
                  <input 
                    v-model.number="variant.stock"
                    type="number" 
                    class="w-full px-2 py-1 text-xs border border-gray-200 dark:border-zinc-700 rounded bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400"
                  >
                </td>
                <td class="px-3 py-2 text-center">
                  <button 
                    type="button" 
                    @click="removeVariant(index)"
                    class="text-gray-400 hover:text-red-500 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-2.002-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
import { reactive, computed, ref } from 'vue'

const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  },
  suppliers: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['save', 'cancel', 'create-category', 'create-supplier'])

// Refs para scroll y color pickers
const generateButton = ref(null)
const variantsSection = ref(null)
const colorInputRefs = ref({})

// Estado Reactivo del Formulario
const form = reactive({
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

const generateVariants = () => {
  // Filtrar opciones que tengan valores
  const validOptions = form.options.filter(opt => opt.values.length > 0 && opt.name.trim() !== '')
  
  if (validOptions.length === 0) {
    alert('Debes agregar al menos una opción con valores (ej: Talla: S, M)')
    return
  }

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
      cost: 0,
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

const removeVariant = (index) => {
  form.variants.splice(index, 1)
}

const getVariantName = (variant) => {
  return variant.options.map(o => o.value).join(' / ')
}

// --- Lógica de Imágenes ---
const fileInput = ref(null)
const isDragging = ref(false)

const triggerFileInput = () => {
  fileInput.value.click()
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

const removeImage = (index) => {
  form.images.splice(index, 1)
}

// --- Submit ---

const handleSubmit = () => {
  // Validaciones básicas
  if (!form.name) {
    alert('El nombre es requerido')
    return
  }
  
  if (!form.category_id) {
    alert('Debes seleccionar una categoría')
    return
  }
  
  // ✅ PERMITIR productos sin variantes (productos simples en modo fashion)
  // Si no hay variantes, crear una variante por defecto con el nombre del producto
  if (form.variants.length === 0) {
    form.variants = [{
      sku: form.sku || `SKU-${Date.now()}`,
      price: 0,
      cost: 0,
      stock: 0,
      active: true,
      options: []
    }]
  }

  // Preparar payload final
  const payload = {
    ...form,
    type: 'variable', // CRÍTICO para el backend
    // Limpiar campos temporales y solo enviar opciones que tengan valores
    options: form.options
      .filter(o => o.values && o.values.length > 0) // Solo opciones con valores
      .map(o => ({
        name: o.name,
        values: o.values
      }))
  }

  console.log('📤 Enviando producto variable:', payload)
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