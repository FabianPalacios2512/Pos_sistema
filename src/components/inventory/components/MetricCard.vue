<template>
  <div 
    class="metric-card bg-white dark:bg-[#1e1f20] rounded-2xl p-5 transition-all duration-200 hover:shadow-md"
    :class="{ 'ring-2 ring-offset-2 dark:ring-offset-[#131314]': alert, [alertRingColor]: alert }"
  >
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">{{ title }}</p>
        <p 
          class="text-2xl font-semibold mt-1"
          :class="valueClass"
        >
          {{ displayValue }}
        </p>
      </div>
      <div 
        class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center"
        :class="iconBgClass"
      >
        <i 
          :class="[icon, iconColorClass]"
          class="text-xl"
        ></i>
      </div>
    </div>
    
    <!-- Indicador de alerta -->
    <div v-if="alert" class="mt-3 flex items-center text-xs">
      <svg class="w-4 h-4 mr-1.5" :class="alertTextColor" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
      </svg>
      <span :class="alertTextColor">Requiere atención</span>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'MetricCard',
  props: {
    title: {
      type: String,
      required: true
    },
    value: {
      type: [String, Number],
      required: true
    },
    icon: {
      type: String,
      required: true
    },
    color: {
      type: String,
      default: 'blue',
      validator: (value) => ['blue', 'green', 'yellow', 'red', 'purple', 'indigo'].includes(value)
    },
    alert: {
      type: Boolean,
      default: false
    },
    isCurrency: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    const colorClasses = {
      blue: {
        iconBg: 'bg-[#e8f0fe] dark:bg-[#1a73e8]/20',
        iconColor: 'text-[#1a73e8] dark:text-[#8ab4f8]',
        value: 'text-[#1e1f20] dark:text-[#e3e3e3]',
        alertRing: 'ring-[#1a73e8]',
        alertText: 'text-[#1a73e8] dark:text-[#8ab4f8]'
      },
      green: {
        iconBg: 'bg-[#e6f4ea] dark:bg-[#1e8e3e]/20',
        iconColor: 'text-[#1e8e3e] dark:text-[#81c995]',
        value: 'text-[#1e1f20] dark:text-[#e3e3e3]',
        alertRing: 'ring-[#1e8e3e]',
        alertText: 'text-[#1e8e3e] dark:text-[#81c995]'
      },
      yellow: {
        iconBg: 'bg-[#fef7e0] dark:bg-[#f9ab00]/20',
        iconColor: 'text-[#f9ab00] dark:text-[#fdd663]',
        value: 'text-[#1e1f20] dark:text-[#e3e3e3]',
        alertRing: 'ring-[#f9ab00]',
        alertText: 'text-[#f9ab00] dark:text-[#fdd663]'
      },
      red: {
        iconBg: 'bg-[#fce8e6] dark:bg-[#d93025]/20',
        iconColor: 'text-[#d93025] dark:text-[#f28b82]',
        value: 'text-[#1e1f20] dark:text-[#e3e3e3]',
        alertRing: 'ring-[#d93025]',
        alertText: 'text-[#d93025] dark:text-[#f28b82]'
      },
      purple: {
        iconBg: 'bg-[#f3e8ff] dark:bg-[#7c3aed]/20',
        iconColor: 'text-[#7c3aed] dark:text-[#a78bfa]',
        value: 'text-[#1e1f20] dark:text-[#e3e3e3]',
        alertRing: 'ring-[#7c3aed]',
        alertText: 'text-[#7c3aed] dark:text-[#a78bfa]'
      },
      indigo: {
        iconBg: 'bg-[#e8f0fe] dark:bg-[#4f46e5]/20',
        iconColor: 'text-[#4f46e5] dark:text-[#818cf8]',
        value: 'text-[#1e1f20] dark:text-[#e3e3e3]',
        alertRing: 'ring-[#4f46e5]',
        alertText: 'text-[#4f46e5] dark:text-[#818cf8]'
      }
    }

    const iconBgClass = computed(() => colorClasses[props.color]?.iconBg || colorClasses.blue.iconBg)
    const iconColorClass = computed(() => colorClasses[props.color]?.iconColor || colorClasses.blue.iconColor)
    const valueClass = computed(() => {
      if (props.alert) {
        return colorClasses[props.color]?.alertText || colorClasses.blue.alertText
      }
      return colorClasses[props.color]?.value || colorClasses.blue.value
    })
    const alertRingColor = computed(() => colorClasses[props.color]?.alertRing || colorClasses.blue.alertRing)
    const alertTextColor = computed(() => colorClasses[props.color]?.alertText || colorClasses.blue.alertText)

    const displayValue = computed(() => {
      if (props.isCurrency && typeof props.value === 'string') {
        return props.value
      }
      
      if (typeof props.value === 'number') {
        return new Intl.NumberFormat('es-ES').format(props.value)
      }
      
      return props.value
    })

    return {
      iconBgClass,
      iconColorClass,
      valueClass,
      alertRingColor,
      alertTextColor,
      displayValue
    }
  }
}
</script>

<style scoped>
.metric-card {
  border: 1px solid rgba(229, 231, 235, 0.5);
}

.metric-card:hover {
  transform: translateY(-1px);
}

/* Animación suave para las alertas */
@keyframes pulse-ring {
  0% {
    box-shadow: 0 0 0 0px rgba(239, 68, 68, 0.4);
  }
  100% {
    box-shadow: 0 0 0 20px rgba(239, 68, 68, 0);
  }
}

.ring-red-500 {
  animation: pulse-ring 2s ease-out infinite;
}

.ring-yellow-500 {
  animation: pulse-ring 2s ease-out infinite;
}

/* Responsive */
@media (max-width: 768px) {
  .metric-card {
    padding: 1rem;
  }
  
  .text-2xl {
    font-size: 1.5rem;
  }
}
</style>