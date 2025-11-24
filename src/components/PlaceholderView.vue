<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-purple-50 to-indigo-100 p-8">
    <div class="max-w-4xl mx-auto">
      <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 p-8 backdrop-blur-lg bg-opacity-95">
      
        <!-- Icono y título principal -->
        <div class="text-center mb-8">
          <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
          </div>
          <h1 class="text-4xl font-extrabold bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-600 bg-clip-text text-transparent mb-4">{{ moduleTitle }}</h1>
          <p class="text-xl text-gray-600 font-medium">{{ moduleDescription }}</p>
          <div class="flex items-center justify-center mt-4 text-sm text-gray-500">
            <div class="w-2 h-2 bg-indigo-500 rounded-full mr-2 animate-pulse"></div>
            Funcionalidad en desarrollo
          </div>
        </div>

      <!-- Estado de desarrollo -->
      <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-8">
        <div class="flex items-start space-x-3">
          <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-1">Módulo en Desarrollo</h3>
            <p class="text-blue-700 dark:text-blue-300">
              Esta funcionalidad está siendo desarrollada y estará disponible próximamente.
              El sistema POS principal ya incluye las funciones básicas operativas.
            </p>
          </div>
        </div>
      </div>

      <!-- Funcionalidades planificadas -->
      <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Funcionalidades Planificadas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div v-for="feature in plannedFeatures" :key="feature.id" 
               class="flex items-center space-x-3 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <span class="text-gray-700 dark:text-gray-300">{{ feature.name }}</span>
          </div>
        </div>
      </div>

      <!-- Progreso del desarrollo -->
      <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Progreso del Desarrollo</h2>
        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-2">
          <div class="bg-gradient-to-r from-primary-600 to-accent-600 h-3 rounded-full transition-all duration-500" 
               :style="{ width: developmentProgress + '%' }"></div>
        </div>
        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
          <span>{{ developmentProgress }}% completado</span>
          <span>Estimado: {{ estimatedCompletion }}</span>
        </div>
      </div>

      <!-- Acciones disponibles -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button 
          @click="goToMainPOS"
          class="flex items-center justify-center space-x-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
          </svg>
          <span>Ir al POS Principal</span>
        </button>
        
        <button 
          @click="goToDashboard"
          class="flex items-center justify-center space-x-2 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span>Ver Dashboard</span>
        </button>
      </div>

      <!-- Información adicional -->
      <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
          <p>Para funcionalidades inmediatas, utiliza el módulo de <strong>Dashboard</strong> o <strong>POS</strong></p>
          <p class="mt-1">Sistema POS 105 - Versión empresarial completa</p>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props para recibir información del módulo
const props = defineProps({
  moduleName: {
    type: String,
    default: 'Módulo'
  }
})

// Emits para comunicación con el componente padre
const emit = defineEmits(['navigate'])

// Configuración de módulos
const moduleConfig = {
  categories: {
    title: 'Gestión de Categorías',
    description: 'Organización y administración de categorías de productos',
    progress: 25,
    completion: 'Próxima versión',
    features: [
      { id: 1, name: 'Crear y editar categorías' },
      { id: 2, name: 'Jerarquía de categorías' },
      { id: 3, name: 'Colores y códigos personalizados' },
      { id: 4, name: 'Reportes por categoría' }
    ]
  },
  stock: {
    title: 'Control de Inventario',
    description: 'Gestión avanzada de stock y movimientos de inventario',
    progress: 40,
    completion: '2 semanas',
    features: [
      { id: 1, name: 'Movimientos de entrada y salida' },
      { id: 2, name: 'Ajustes de inventario' },
      { id: 3, name: 'Alertas de stock mínimo' },
      { id: 4, name: 'Valorización de inventario' },
      { id: 5, name: 'Reportes de rotación' },
      { id: 6, name: 'Códigos de ubicación' }
    ]
  },
  suppliers: {
    title: 'Gestión de Proveedores',
    description: 'Administración completa de proveedores y relaciones comerciales',
    progress: 15,
    completion: '3 semanas',
    features: [
      { id: 1, name: 'Base de datos de proveedores' },
      { id: 2, name: 'Órdenes de compra' },
      { id: 3, name: 'Historial de transacciones' },
      { id: 4, name: 'Evaluación de proveedores' }
    ]
  },
  customers: {
    title: 'Base de Datos de Clientes',
    description: 'Gestión integral de clientes y programas de fidelización',
    progress: 30,
    completion: '2 semanas',
    features: [
      { id: 1, name: 'Perfiles de cliente completos' },
      { id: 2, name: 'Historial de compras' },
      { id: 3, name: 'Sistema de puntos' },
      { id: 4, name: 'Segmentación de clientes' },
      { id: 5, name: 'Comunicación personalizada' }
    ]
  },
  invoices: {
    title: 'Sistema de Facturación',
    description: 'Generación y gestión de documentos fiscales',
    progress: 20,
    completion: '4 semanas',
    features: [
      { id: 1, name: 'Facturas electrónicas' },
      { id: 2, name: 'Cotizaciones' },
      { id: 3, name: 'Notas de crédito/débito' },
      { id: 4, name: 'Numeración automática' }
    ]
  },
  users: {
    title: 'Gestión de Usuarios',
    description: 'Control de acceso y permisos del sistema',
    progress: 10,
    completion: '5 semanas',
    features: [
      { id: 1, name: 'Roles y permisos' },
      { id: 2, name: 'Auditoría de acciones' },
      { id: 3, name: 'Sesiones múltiples' },
      { id: 4, name: 'Configuración de seguridad' }
    ]
  },
  reports: {
    title: 'Reportes Avanzados',
    description: 'Análisis de datos y reportes ejecutivos',
    progress: 35,
    completion: '2 semanas',
    features: [
      { id: 1, name: 'Reportes de ventas' },
      { id: 2, name: 'Análisis de inventario' },
      { id: 3, name: 'Métricas de clientes' },
      { id: 4, name: 'Reportes financieros' },
      { id: 5, name: 'Gráficos interactivos' },
      { id: 6, name: 'Exportación múltiple' }
    ]
  },
  settings: {
    title: 'Configuración del Sistema',
    description: 'Parametrización y ajustes generales',
    progress: 50,
    completion: '1 semana',
    features: [
      { id: 1, name: 'Información de la empresa' },
      { id: 2, name: 'Configuración de impuestos' },
      { id: 3, name: 'Métodos de pago' },
      { id: 4, name: 'Preferencias de interfaz' }
    ]
  }
}

// Computed properties
const currentConfig = computed(() => {
  return moduleConfig[props.moduleName] || {
    title: 'Módulo del Sistema',
    description: 'Funcionalidad en desarrollo',
    progress: 0,
    completion: 'Por definir',
    features: []
  }
})

const moduleTitle = computed(() => currentConfig.value.title)
const moduleDescription = computed(() => currentConfig.value.description)
const developmentProgress = computed(() => currentConfig.value.progress)
const estimatedCompletion = computed(() => currentConfig.value.completion)
const plannedFeatures = computed(() => currentConfig.value.features)

// Métodos
const goToMainPOS = () => {
  emit('navigate', 'pos')
}

const goToDashboard = () => {
  emit('navigate', 'dashboard')
}
</script>