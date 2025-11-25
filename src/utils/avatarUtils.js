/**
 * Utilidades para generar avatares con iniciales
 * Reemplaza los círculos lila pálidos vacíos con avatares profesionales
 */

/**
 * Obtener las iniciales de un nombre
 * @param {string} name - Nombre completo
 * @returns {string} - Iniciales (máximo 2 caracteres)
 */
export const getInitials = (name) => {
  if (!name) return '??'
  
  const words = name.trim().split(' ').filter(word => word.length > 0)
  
  if (words.length === 0) return '??'
  if (words.length === 1) return words[0].substring(0, 2).toUpperCase()
  
  // Primera letra del primer nombre + primera letra del apellido
  return (words[0][0] + words[words.length - 1][0]).toUpperCase()
}

/**
 * Generar color consistente basado en el nombre
 * @param {string} name - Nombre completo
 * @returns {object} - Objeto con colores de fondo y texto
 */
export const getAvatarColors = (name) => {
  if (!name) return { bg: '#F1F5F9', text: '#475569' } // slate-100 / slate-600
  
  // Generar hash simple del nombre
  let hash = 0
  for (let i = 0; i < name.length; i++) {
    hash = name.charCodeAt(i) + ((hash << 5) - hash)
  }
  
  // Paleta de colores corporativos sutiles
  const colors = [
    { bg: '#F1F5F9', text: '#475569' }, // slate
    { bg: '#EFF6FF', text: '#1E40AF' }, // blue
    { bg: '#F0FDF4', text: '#166534' }, // green
    { bg: '#FEF3C7', text: '#92400E' }, // amber
    { bg: '#FCE7F3', text: '#9F1239' }, // pink
    { bg: '#E0E7FF', text: '#3730A3' }, // indigo
    { bg: '#DBEAFE', text: '#1E3A8A' }, // lightblue
  ]
  
  return colors[Math.abs(hash) % colors.length]
}

/**
 * Componente Vue para avatar con iniciales (para uso en templates)
 * Uso: <CustomerAvatar :name="customer.name" :size="'md'" />
 */
export const CustomerAvatar = {
  name: 'CustomerAvatar',
  props: {
    name: {
      type: String,
      required: true
    },
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    }
  },
  computed: {
    initials() {
      return getInitials(this.name)
    },
    colors() {
      return getAvatarColors(this.name)
    },
    sizeClasses() {
      const sizes = {
        sm: 'w-8 h-8 text-xs',
        md: 'w-10 h-10 text-sm',
        lg: 'w-12 h-12 text-base',
        xl: 'w-16 h-16 text-lg'
      }
      return sizes[this.size]
    }
  },
  template: `
    <div 
      :class="sizeClasses"
      class="rounded-full flex items-center justify-center font-bold transition-all duration-200"
      :style="{ backgroundColor: colors.bg, color: colors.text }"
      :title="name"
    >
      {{ initials }}
    </div>
  `
}

/**
 * Íconos de flecha para movimientos de inventario
 * Reemplaza los cuadrados sólidos rojos/azules por flechas simples
 */
export const MovementIcon = {
  name: 'MovementIcon',
  props: {
    type: {
      type: String,
      required: true,
      validator: (value) => ['entrada', 'salida', 'in', 'out', 'entry', 'exit'].includes(value.toLowerCase())
    },
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg'].includes(value)
    }
  },
  computed: {
    isEntry() {
      return ['entrada', 'in', 'entry'].includes(this.type.toLowerCase())
    },
    sizeClass() {
      const sizes = {
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-6 h-6'
      }
      return sizes[this.size]
    },
    colorClass() {
      return this.isEntry ? 'text-green-600' : 'text-red-600'
    }
  },
  template: `
    <svg 
      :class="[sizeClass, colorClass]" 
      fill="none" 
      stroke="currentColor" 
      viewBox="0 0 24 24"
      :title="isEntry ? 'Entrada' : 'Salida'"
    >
      <!-- Flecha hacia abajo (Entrada) -->
      <path 
        v-if="isEntry"
        stroke-linecap="round" 
        stroke-linejoin="round" 
        stroke-width="2.5" 
        d="M19 14l-7 7m0 0l-7-7m7 7V3"
      />
      <!-- Flecha hacia arriba (Salida) -->
      <path 
        v-else
        stroke-linecap="round" 
        stroke-linejoin="round" 
        stroke-width="2.5" 
        d="M5 10l7-7m0 0l7 7m-7-7v18"
      />
    </svg>
  `
}

export default {
  getInitials,
  getAvatarColors,
  CustomerAvatar,
  MovementIcon
}
