<template>
  <div 
    class="flex items-center justify-center w-10 h-10 rounded-full"
    :class="bgClass"
  >
    <i 
      :class="[iconClass, colorClass]"
      class="text-sm"
    ></i>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'MovementTypeIcon',
  props: {
    type: {
      type: String,
      required: true,
      validator: (value) => ['purchase', 'sale', 'adjustment', 'return', 'transfer'].includes(value)
    },
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg'].includes(value)
    }
  },
  setup(props) {
    const typeConfig = {
      purchase: {
        icon: 'fas fa-arrow-down',
        bgClass: 'bg-green-100',
        colorClass: 'text-green-600',
        label: 'Compra'
      },
      sale: {
        icon: 'fas fa-arrow-up',
        bgClass: 'bg-red-100',
        colorClass: 'text-red-600',
        label: 'Venta'
      },
      adjustment: {
        icon: 'fas fa-edit',
        bgClass: 'bg-blue-100',
        colorClass: 'text-blue-600',
        label: 'Ajuste'
      },
      return: {
        icon: 'fas fa-undo',
        bgClass: 'bg-yellow-100',
        colorClass: 'text-yellow-600',
        label: 'Devolución'
      },
      transfer: {
        icon: 'fas fa-exchange-alt',
        bgClass: 'bg-purple-100',
        colorClass: 'text-purple-600',
        label: 'Transferencia'
      }
    }

    const config = computed(() => typeConfig[props.type] || typeConfig.adjustment)
    const iconClass = computed(() => config.value.icon)
    const bgClass = computed(() => config.value.bgClass)
    const colorClass = computed(() => config.value.colorClass)

    return {
      iconClass,
      bgClass,
      colorClass
    }
  }
}
</script>

<style scoped>
/* Variaciones de tamaño */
.w-10.h-10 {
  width: 2.5rem;
  height: 2.5rem;
}

/* Animación suave */
.movement-icon {
  transition: transform 0.2s ease;
}

.movement-icon:hover {
  transform: scale(1.1);
}
</style>