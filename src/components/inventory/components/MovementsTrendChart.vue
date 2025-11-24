<template>
  <div class="movements-trend-chart">
    <div v-if="!hasData" class="flex items-center justify-center h-64 text-gray-500">
      <div class="text-center">
        <i class="fas fa-chart-line text-4xl mb-3 text-gray-300"></i>
        <p class="text-lg">No hay datos suficientes para mostrar el gráfico</p>
        <p class="text-sm">Los movimientos aparecerán aquí cuando haya actividad</p>
      </div>
    </div>
    
    <canvas 
      v-else
      ref="chartCanvas"
      :height="height"
      class="w-full"
    ></canvas>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue'
import Chart from 'chart.js/auto'

export default {
  name: 'MovementsTrendChart',
  props: {
    data: {
      type: Object,
      default: () => ({})
    },
    height: {
      type: Number,
      default: 300
    }
  },
  setup(props) {
    const chartCanvas = ref(null)
    const chartInstance = ref(null)

    const hasData = computed(() => {
      return props.data && Object.keys(props.data).length > 0
    })

    const formatChartData = () => {
      if (!hasData.value) return { labels: [], datasets: [] }

      const dates = Object.keys(props.data).sort()
      const entryData = []
      const exitData = []
      
      dates.forEach(date => {
        const dayData = props.data[date] || []
        const purchases = dayData.find(d => d.type === 'purchase')?.total || 0
        const adjustments = dayData.find(d => d.type === 'adjustment')?.total || 0
        const returns = dayData.find(d => d.type === 'return')?.total || 0
        const sales = dayData.find(d => d.type === 'sale')?.total || 0
        
        entryData.push(purchases + adjustments + returns)
        exitData.push(sales)
      })
      
      return {
        labels: dates.map(date => {
          const dateObj = new Date(date)
          return dateObj.toLocaleDateString('es-ES', { 
            month: 'short', 
            day: 'numeric'
          })
        }),
        datasets: [
          {
            label: 'Entradas',
            data: entryData,
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: 'rgb(34, 197, 94)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
          },
          {
            label: 'Salidas',
            data: exitData,
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: 'rgb(239, 68, 68)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
          }
        ]
      }
    }

    const createChart = () => {
      if (!chartCanvas.value || !hasData.value) return

      const ctx = chartCanvas.value.getContext('2d')
      
      if (chartInstance.value) {
        chartInstance.value.destroy()
      }

      chartInstance.value = new Chart(ctx, {
        type: 'line',
        data: formatChartData(),
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: {
            intersect: false,
            mode: 'index',
          },
          plugins: {
            legend: {
              position: 'top',
              labels: {
                usePointStyle: true,
                padding: 20,
                font: {
                  size: 12,
                  family: 'Inter, sans-serif'
                }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleColor: '#fff',
              bodyColor: '#fff',
              borderColor: 'rgba(255, 255, 255, 0.1)',
              borderWidth: 1,
              cornerRadius: 8,
              displayColors: true,
              callbacks: {
                label: (context) => {
                  const label = context.dataset.label || ''
                  const value = context.parsed.y
                  return `${label}: ${value} unidades`
                }
              }
            }
          },
          scales: {
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  size: 11,
                  family: 'Inter, sans-serif'
                }
              }
            },
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              },
              ticks: {
                font: {
                  size: 11,
                  family: 'Inter, sans-serif'
                },
                callback: (value) => {
                  return Math.floor(value).toString()
                }
              }
            }
          },
          elements: {
            line: {
              borderWidth: 2
            }
          }
        }
      })
    }

    const updateChart = () => {
      if (chartInstance.value && hasData.value) {
        const newData = formatChartData()
        chartInstance.value.data = newData
        chartInstance.value.update('active')
      } else if (hasData.value) {
        createChart()
      }
    }

    // Watchers
    watch(() => props.data, () => {
      updateChart()
    }, { deep: true })

    // Lifecycle
    onMounted(() => {
      if (hasData.value) {
        createChart()
      }
    })

    onUnmounted(() => {
      if (chartInstance.value) {
        chartInstance.value.destroy()
      }
    })

    return {
      chartCanvas,
      hasData
    }
  }
}
</script>

<style scoped>
.movements-trend-chart {
  position: relative;
  width: 100%;
}

/* Asegurar que el canvas sea responsive */
canvas {
  max-width: 100%;
  height: auto !important;
}

/* Loading animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.movements-trend-chart {
  animation: fadeIn 0.5s ease-out;
}
</style>