import * as XLSX from 'xlsx'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

export const exportService = {
  
  // Exportar datos a Excel
  exportToExcel(data, filename, sheetName = 'Datos') {
    try {
      const worksheet = XLSX.utils.json_to_sheet(data)
      const workbook = XLSX.utils.book_new()
      XLSX.utils.book_append_sheet(workbook, worksheet, sheetName)
      
      // Aplicar estilos básicos al header
      const range = XLSX.utils.decode_range(worksheet['!ref'])
      for (let C = range.s.c; C <= range.e.c; C++) {
        const address = XLSX.utils.encode_cell({ r: 0, c: C })
        if (!worksheet[address]) continue
        worksheet[address].s = {
          font: { bold: true },
          fill: { fgColor: { rgb: "366092" } },
          alignment: { horizontal: "center" }
        }
      }
      
      XLSX.writeFile(workbook, `${filename}.xlsx`)
      return { success: true, message: 'Archivo Excel exportado exitosamente' }
    } catch (error) {
      console.error('Error exportando a Excel:', error)
      return { success: false, message: 'Error al exportar archivo Excel' }
    }
  },

  // Exportar reporte de caja completo
  exportCashierReport(cashierData, period) {
    const reportData = cashierData.map(cashier => ({
      'Cajero': cashier.name,
      'Rol': cashier.role,
      'Sesiones': cashier.sessions,
      'Total Ventas': `$${cashier.totalSales.toLocaleString()}`,
      'Transacciones': cashier.transactions,
      'Promedio por Venta': `$${(cashier.totalSales / cashier.transactions).toFixed(2)}`,
      'Eficiencia (%)': `${cashier.efficiency}%`,
      'Horas Trabajadas': `${cashier.hoursWorked}h`,
      'Ventas por Hora': `$${(cashier.totalSales / cashier.hoursWorked).toFixed(0)}`,
      'Período': period
    }))
    
    const filename = `reporte_cajeros_${period}_${new Date().toISOString().split('T')[0]}`
    return this.exportToExcel(reportData, filename, 'Reporte Cajeros')
  },

  // Exportar comparativa de cajeros
  exportCashierComparison(cashierData, topSessions, alerts) {
    const workbook = XLSX.utils.book_new()
    
    // Hoja 1: Comparativa de cajeros
    const cashierSheet = XLSX.utils.json_to_sheet(cashierData.map(cashier => ({
      'Cajero': cashier.name,
      'Rol': cashier.role,
      'Total Ventas': cashier.totalSales,
      'Transacciones': cashier.transactions,
      'Eficiencia': cashier.efficiency,
      'Horas Trabajadas': cashier.hoursWorked
    })))
    XLSX.utils.book_append_sheet(workbook, cashierSheet, 'Comparativa Cajeros')
    
    // Hoja 2: Top sesiones
    const sessionsSheet = XLSX.utils.json_to_sheet(topSessions.map(session => ({
      'Cajero': session.cashier,
      'Fecha': session.date,
      'Ventas': session.sales,
      'Duración (horas)': session.duration
    })))
    XLSX.utils.book_append_sheet(workbook, sessionsSheet, 'Top Sesiones')
    
    // Hoja 3: Alertas
    const alertsSheet = XLSX.utils.json_to_sheet(alerts.map(alert => ({
      'Tipo': alert.type,
      'Título': alert.title,
      'Mensaje': alert.message
    })))
    XLSX.utils.book_append_sheet(workbook, alertsSheet, 'Alertas')
    
    const filename = `comparativa_cajeros_${new Date().toISOString().split('T')[0]}`
    XLSX.writeFile(workbook, `${filename}.xlsx`)
    
    return { success: true, message: 'Comparativa exportada exitosamente' }
  },

  // Exportar a PDF
  exportToPDF(data, title, filename) {
    try {
      const doc = new jsPDF()
      
      // Título del documento
      doc.setFontSize(20)
      doc.setTextColor(40, 40, 40)
      doc.text(title, 20, 20)
      
      // Información de generación
      doc.setFontSize(10)
      doc.setTextColor(100, 100, 100)
      doc.text(`Generado el: ${new Date().toLocaleString('es-ES')}`, 20, 30)
      
      // Preparar datos para la tabla
      const headers = Object.keys(data[0] || {})
      const rows = data.map(item => headers.map(header => item[header]))
      
      // Generar tabla
      doc.autoTable({
        head: [headers],
        body: rows,
        startY: 40,
        styles: {
          fontSize: 8,
          cellPadding: 3
        },
        headStyles: {
          fillColor: [54, 96, 146],
          textColor: 255,
          fontStyle: 'bold'
        },
        alternateRowStyles: {
          fillColor: [245, 245, 245]
        }
      })
      
      doc.save(`${filename}.pdf`)
      return { success: true, message: 'Archivo PDF exportado exitosamente' }
    } catch (error) {
      console.error('Error exportando a PDF:', error)
      return { success: false, message: 'Error al exportar archivo PDF' }
    }
  },

  // Exportar CSV simple
  exportToCSV(data, filename) {
    try {
      const headers = Object.keys(data[0] || {})
      const csvContent = [
        headers.join(','),
        ...data.map(row => headers.map(header => `"${row[header]}"`).join(','))
      ].join('\n')
      
      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
      const link = document.createElement('a')
      
      if (link.download !== undefined) {
        const url = URL.createObjectURL(blob)
        link.setAttribute('href', url)
        link.setAttribute('download', `${filename}.csv`)
        link.style.visibility = 'hidden'
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
      }
      
      return { success: true, message: 'Archivo CSV exportado exitosamente' }
    } catch (error) {
      console.error('Error exportando a CSV:', error)
      return { success: false, message: 'Error al exportar archivo CSV' }
    }
  },

  // Exportar métricas de rendimiento
  exportPerformanceMetrics(metrics, period) {
    const performanceData = [{
      'Período': period,
      'Sesiones Activas': metrics.activeSessions,
      'Mejor Cajero': metrics.bestCashier.name,
      'Ventas Mejor Cajero': `$${metrics.bestCashier.sales.toLocaleString()}`,
      'Promedio Ventas/Hora': `$${metrics.averageSalesPerHour.toLocaleString()}`,
      'Total Transacciones': metrics.totalTransactions,
      'Fecha Generación': new Date().toLocaleString('es-ES')
    }]
    
    const filename = `metricas_rendimiento_${period}_${new Date().toISOString().split('T')[0]}`
    return this.exportToExcel(performanceData, filename, 'Métricas Rendimiento')
  }
}

// Funciones de utilidad adicionales
export const reportUtils = {
  
  // Formatear datos para exportación
  formatDataForExport(rawData, formatConfig = {}) {
    return rawData.map(item => {
      const formatted = {}
      Object.keys(item).forEach(key => {
        const config = formatConfig[key] || {}
        let value = item[key]
        
        // Aplicar formatos específicos
        if (config.type === 'currency') {
          value = `$${Number(value).toLocaleString()}`
        } else if (config.type === 'percentage') {
          value = `${value}%`
        } else if (config.type === 'date') {
          value = new Date(value).toLocaleDateString('es-ES')
        }
        
        formatted[config.label || key] = value
      })
      return formatted
    })
  },

  // Generar resumen estadístico
  generateSummaryStats(data, fields) {
    const stats = {}
    
    fields.forEach(field => {
      const values = data.map(item => Number(item[field])).filter(v => !isNaN(v))
      if (values.length > 0) {
        stats[field] = {
          total: values.reduce((sum, val) => sum + val, 0),
          average: values.reduce((sum, val) => sum + val, 0) / values.length,
          min: Math.min(...values),
          max: Math.max(...values),
          count: values.length
        }
      }
    })
    
    return stats
  },

  // Validar datos antes de exportar
  validateExportData(data) {
    if (!Array.isArray(data) || data.length === 0) {
      return { valid: false, message: 'No hay datos para exportar' }
    }
    
    const firstItem = data[0]
    if (!firstItem || typeof firstItem !== 'object') {
      return { valid: false, message: 'Formato de datos inválido' }
    }
    
    return { valid: true, message: 'Datos válidos para exportación' }
  }
}