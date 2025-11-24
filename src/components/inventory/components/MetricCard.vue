<template>
  <div 
    class="metric-card bg-white rounded-lg shadow p-6 transition-all duration-200 hover:shadow-lg"
    :class="{ 'ring-2 ring-offset-2': alert, [alertRingColor]: alert }"
  >
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-600">{{ title }}</p>
        <p 
          class="text-2xl font-bold mt-1"
          :class="valueClass"
        >
          {{ displayValue }}
        </p>
      </div>
      <div 
        class="flex-shrink-0 p-3 rounded-full"
        :class="iconBgClass"
      >
        <i 
          :class="[icon, iconColorClass]"
          class="text-xl"
        ></i>
      </div>
    </div>
    
    <!-- Indicador de alerta -->
    <div v-if="alert" class="mt-3 flex items-center text-sm">
      <i class="fas fa-exclamation-triangle mr-2" :class="alertTextColor"></i>
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
        iconBg: 'bg-blue-100',
        iconColor: 'text-blue-600',
        value: 'text-gray-900',
        alertRing: 'ring-blue-500',
        alertText: 'text-blue-600'
      },
      green: {
        iconBg: 'bg-green-100',
        iconColor: 'text-green-600',
        value: 'text-gray-900',
        alertRing: 'ring-green-500',
        alertText: 'text-green-600'
      },
      yellow: {
        iconBg: 'bg-yellow-100',
        iconColor: 'text-yellow-600',
        value: 'text-gray-900',
        alertRing: 'ring-yellow-500',
        alertText: 'text-yellow-600'
      },
      red: {
        iconBg: 'bg-red-100',
        iconColor: 'text-red-600',
        value: 'text-gray-900',
        alertRing: 'ring-red-500',
        alertText: 'text-red-600'
      },
      purple: {
        iconBg: 'bg-purple-100',
        iconColor: 'text-purple-600',
        value: 'text-gray-900',
        alertRing: 'ring-purple-500',
        alertText: 'text-purple-600'
      },
      indigo: {
        iconBg: 'bg-indigo-100',
        iconColor: 'text-indigo-600',
        value: 'text-gray-900',
        alertRing: 'ring-indigo-500',
        alertText: 'text-indigo-600'
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