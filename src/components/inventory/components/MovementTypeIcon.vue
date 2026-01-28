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
        bgClass: 'bg-[#e6f4ea] dark:bg-[#1e8e3e]/20',
        colorClass: 'text-[#1e8e3e] dark:text-[#81c995]',
        label: 'Compra'
      },
      sale: {
        icon: 'fas fa-arrow-up',
        bgClass: 'bg-[#fce8e6] dark:bg-[#d93025]/20',
        colorClass: 'text-[#d93025] dark:text-[#f28b82]',
        label: 'Venta'
      },
      adjustment: {
        icon: 'fas fa-edit',
        bgClass: 'bg-[#e8f0fe] dark:bg-[#1a73e8]/20',
        colorClass: 'text-[#1a73e8] dark:text-[#8ab4f8]',
        label: 'Ajuste'
      },
      return: {
        icon: 'fas fa-undo',
        bgClass: 'bg-[#fef7e0] dark:bg-[#f9ab00]/20',
        colorClass: 'text-[#f9ab00] dark:text-[#fdd663]',
        label: 'Devolución'
      },
      transfer: {
        icon: 'fas fa-exchange-alt',
        bgClass: 'bg-[#f3e8ff] dark:bg-[#7c3aed]/20',
        colorClass: 'text-[#7c3aed] dark:text-[#a78bfa]',
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