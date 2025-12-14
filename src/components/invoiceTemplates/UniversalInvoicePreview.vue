<template>
  <div class="invoice-preview w-full bg-white font-sans shadow-2xl border border-gray-200" style="font-family: 'Helvetica', Arial, sans-serif; width: 80mm; min-height: 500px;">
    
    <!-- ========== TEMPLATE CLASSIC ========== -->
    <div v-if="template === 'classic'" class="bg-white">
      <!-- Header Empresarial Clean -->
      <div class="text-center px-6 pt-6 pb-4 border-b-2 border-gray-200">
        <h2 class="font-bold text-gray-900 uppercase tracking-wider text-lg mb-2" style="letter-spacing: 0.1em;">
          {{ data.storeName }}
        </h2>
        <p class="text-[9pt] text-gray-600 leading-relaxed">{{ data.address }}</p>
        <p class="text-[9pt] text-gray-600">{{ data.phone }}</p>
      </div>

      <!-- Título Factura Elegante -->
      <div class="text-center py-4 bg-gray-50">
        <h3 class="font-bold text-gray-900 text-sm tracking-wide">FACTURA DE VENTA</h3>
        <p class="font-semibold text-gray-700 text-xs mt-1">No. PRE-001</p>
      </div>

      <!-- Información -->
      <div class="px-6 py-3 text-[8pt] text-gray-700 bg-white space-y-1.5">
        <div class="flex justify-between">
          <span class="text-gray-500">Fecha:</span>
          <span class="font-semibold">29 de noviembre de 2025</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Hora:</span>
          <span class="font-semibold">07:13 p. m.</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Cliente:</span>
          <span class="font-semibold">Cliente General</span>
        </div>
      </div>

      <!-- Tabla de Productos Profesional -->
      <div class="px-6 py-4">
        <table class="w-full text-[8pt]">
          <thead>
            <tr class="border-b border-gray-300">
              <th class="text-left py-2 font-semibold text-gray-700 uppercase text-[7pt]">Producto</th>
              <th class="text-center py-2 font-semibold text-gray-700 uppercase text-[7pt] w-12">Cant</th>
              <th class="text-right py-2 font-semibold text-gray-700 uppercase text-[7pt] w-20">Total</th>
            </tr>
          </thead>
          <tbody class="text-gray-800">
            <tr class="border-b border-gray-100">
              <td class="py-2">Gorra len</td>
              <td class="text-center py-2">1</td>
              <td class="text-right py-2 font-medium">$30.000</td>
            </tr>
            <tr class="border-b border-gray-100">
              <td class="py-2">Producto de prueba 1</td>
              <td class="text-center py-2">1</td>
              <td class="text-right py-2 font-medium">$111.111</td>
            </tr>
            <tr class="border-b border-gray-100">
              <td class="py-2">Producto de prueba 2</td>
              <td class="text-center py-2">5</td>
              <td class="text-right py-2 font-medium">$555.555</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Totales Elegantes -->
      <div class="px-6 pb-6">
        <div class="border-t border-gray-300 pt-3 space-y-2 text-[9pt]">
          <div class="flex justify-between text-gray-700">
            <span>Subtotal:</span>
            <span class="font-semibold">$696.666</span>
          </div>
          <div class="flex justify-between text-gray-700">
            <span>IVA (19%):</span>
            <span class="font-semibold">$132.367</span>
          </div>
          <div class="flex justify-between text-gray-900 text-base font-bold pt-2 border-t-2 border-gray-900">
            <span>TOTAL:</span>
            <span>$829.033</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ========== TEMPLATE MODERN ========== -->
    <div v-else-if="template === 'modern'">
      <!-- Header Premium con Gradiente -->
      <div class="bg-gradient-to-br from-indigo-600 to-purple-600 text-white px-6 py-6 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5"></div>
        <div class="relative z-10">
          <h2 class="font-bold uppercase tracking-wider text-lg mb-2" style="letter-spacing: 0.15em;">
            {{ data.storeName }}
          </h2>
          <p class="text-[9pt] text-indigo-100 leading-relaxed">{{ data.address }}</p>
          <p class="text-[9pt] text-indigo-100">{{ data.phone }}</p>
        </div>
      </div>

      <!-- Badge Factura Premium -->
      <div class="px-6 py-4 bg-white">
        <div class="border-2 border-indigo-600 rounded-xl p-3 text-center bg-gradient-to-br from-indigo-50 to-purple-50">
          <h3 class="font-bold text-indigo-700 text-sm tracking-wide">FACTURA DE VENTA</h3>
          <p class="font-bold text-indigo-600 text-xs mt-1">No. PRE-001</p>
        </div>
      </div>

      <!-- Información Premium -->
      <div class="px-6 py-3 text-[8pt] text-gray-700 bg-white space-y-1.5">
        <div class="flex justify-between">
          <span class="text-indigo-600 font-medium">Fecha:</span>
          <span class="font-semibold">29 de noviembre de 2025</span>
        </div>
        <div class="flex justify-between">
          <span class="text-indigo-600 font-medium">Hora:</span>
          <span class="font-semibold">07:13 p. m.</span>
        </div>
        <div class="flex justify-between">
          <span class="text-indigo-600 font-medium">Cliente:</span>
          <span class="font-semibold">Cliente General</span>
        </div>
      </div>

      <!-- Tabla Premium -->
      <div class="px-6 py-4">
        <table class="w-full text-[8pt]">
          <thead>
            <tr class="bg-indigo-50 border-b-2 border-indigo-200">
              <th class="text-left py-2 px-2 font-bold text-indigo-700 uppercase text-[7pt]">Producto</th>
              <th class="text-center py-2 font-bold text-indigo-700 uppercase text-[7pt] w-12">Cant</th>
              <th class="text-right py-2 px-2 font-bold text-indigo-700 uppercase text-[7pt] w-20">Total</th>
            </tr>
          </thead>
          <tbody class="text-gray-800">
            <tr class="border-b border-indigo-50 hover:bg-indigo-25">
              <td class="py-2 px-2">Gorra len</td>
              <td class="text-center py-2">1</td>
              <td class="text-right py-2 px-2 font-medium">$30.000</td>
            </tr>
            <tr class="border-b border-indigo-50 hover:bg-indigo-25">
              <td class="py-2 px-2">Producto de prueba 1</td>
              <td class="text-center py-2">1</td>
              <td class="text-right py-2 px-2 font-medium">$111.111</td>
            </tr>
            <tr class="border-b border-indigo-50 hover:bg-indigo-25">
              <td class="py-2 px-2">Producto de prueba 2</td>
              <td class="text-center py-2">5</td>
              <td class="text-right py-2 px-2 font-medium">$555.555</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Totales Premium con Gradiente -->
      <div class="px-6 pb-6">
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-4 border border-indigo-200">
          <div class="space-y-2 text-[9pt]">
            <div class="flex justify-between text-gray-700">
              <span>Subtotal:</span>
              <span class="font-semibold">$696.666</span>
            </div>
            <div class="flex justify-between text-gray-700">
              <span>IVA (19%):</span>
              <span class="font-semibold">$132.367</span>
            </div>
            <div class="flex justify-between text-indigo-700 text-base font-bold pt-2 border-t-2 border-indigo-300">
              <span>TOTAL:</span>
              <span>$829.033</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========== TEMPLATE MINIMAL ========== -->
    <div v-else-if="template === 'minimal'">
      <!-- Header Minimal Premium -->
      <div class="text-center px-6 pt-6 pb-4 border-b-4 border-black">
        <h2 class="font-bold text-black uppercase tracking-widest text-lg mb-2" style="letter-spacing: 0.2em;">
          {{ data.storeName }}
        </h2>
        <p class="text-[9pt] text-gray-700 leading-relaxed">{{ data.address }}</p>
        <p class="text-[9pt] text-gray-700">{{ data.phone }}</p>
      </div>

      <!-- Badge Factura Minimal -->
      <div class="px-6 py-4 bg-white">
        <div class="border-3 border-black p-3 text-center">
          <h3 class="font-bold text-black text-sm tracking-wider uppercase">Factura de Venta</h3>
          <p class="font-bold text-black text-xs mt-1">No. PRE-001</p>
        </div>
      </div>

      <!-- Información Minimal -->
      <div class="px-6 py-3 text-[8pt] text-gray-800 bg-white space-y-1.5">
        <div class="flex justify-between">
          <span class="font-medium">Fecha:</span>
          <span class="font-semibold">29 de noviembre de 2025</span>
        </div>
        <div class="flex justify-between">
          <span class="font-medium">Hora:</span>
          <span class="font-semibold">07:13 p. m.</span>
        </div>
        <div class="flex justify-between">
          <span class="font-medium">Cliente:</span>
          <span class="font-semibold">Cliente General</span>
        </div>
      </div>

      <!-- Tabla Minimal -->
      <div class="px-6 py-4">
        <table class="w-full text-[8pt]">
          <thead>
            <tr class="border-b-2 border-black">
              <th class="text-left py-2 font-bold text-black uppercase text-[7pt]">Producto</th>
              <th class="text-center py-2 font-bold text-black uppercase text-[7pt] w-12">Cant</th>
              <th class="text-right py-2 font-bold text-black uppercase text-[7pt] w-20">Total</th>
            </tr>
          </thead>
          <tbody class="text-gray-900">
            <tr class="border-b border-gray-300">
              <td class="py-2">Gorra len</td>
              <td class="text-center py-2">1</td>
              <td class="text-right py-2 font-semibold">$30.000</td>
            </tr>
            <tr class="border-b border-gray-300">
              <td class="py-2">Producto de prueba 1</td>
              <td class="text-center py-2">1</td>
              <td class="text-right py-2 font-semibold">$111.111</td>
            </tr>
            <tr class="border-b border-gray-300">
              <td class="py-2">Producto de prueba 2</td>
              <td class="text-center py-2">5</td>
              <td class="text-right py-2 font-semibold">$555.555</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Totales Minimal -->
      <div class="px-6 pb-6">
        <div class="border-t-2 border-black pt-3 space-y-2 text-[9pt]">
          <div class="flex justify-between text-gray-800">
            <span class="font-medium">Subtotal:</span>
            <span class="font-semibold">$696.666</span>
          </div>
          <div class="flex justify-between text-gray-800">
            <span class="font-medium">IVA (19%):</span>
            <span class="font-semibold">$132.367</span>
          </div>
          <div class="flex justify-between text-black text-base font-bold pt-2 border-t-4 border-black">
            <span>TOTAL:</span>
            <span>$829.033</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { defineProps } from 'vue'

defineProps({
  template: {
    type: String,
    required: true,
    validator: (value) => ['classic', 'modern', 'minimal'].includes(value)
  },
  data: {
    type: Object,
    default: () => ({
      storeName: 'MI EMPRESA',
      address: 'Calle 123 #45-67, Bogotá',
      phone: '+57 300 123 4567',
      logo: null
    })
  }
})
</script>

<style scoped>
/* Asegurar que el preview se vea como ticket real */
.invoice-preview {
  min-height: 400px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
</style>
