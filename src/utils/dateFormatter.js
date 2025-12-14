/**
 * Utilidades para formateo de fechas con timezone de Colombia
 */

// Configuración para Colombia (GMT-5)
const COLOMBIA_TIMEZONE = 'America/Bogota'
const LOCALE_ES_CO = 'es-CO'

/**
 * Formatea una fecha con timezone de Colombia
 * @param {string|Date} dateInput - Fecha a formatear
 * @param {boolean} includeTime - Si incluir hora (defecto: true)
 * @returns {string} Fecha formateada
 */
export const formatColombianDate = (dateInput, includeTime = true) => {
  if (!dateInput) return ''
  
  try {
    let date
    
    // Si es una string de MySQL (YYYY-MM-DD HH:mm:ss), la tratamos como local de Colombia
    if (typeof dateInput === 'string') {
      // Si no tiene hora, asumimos 00:00:00
      const dateStr = dateInput.includes(' ') ? dateInput : `${dateInput} 00:00:00`
      
      // Creamos la fecha interpretándola como hora de Colombia
      // Agregamos el offset de Colombia (-05:00) para que se interprete correctamente
      date = new Date(dateStr + ' -05:00')
    } else {
      date = new Date(dateInput)
    }

    if (isNaN(date.getTime())) {
      console.warn('Fecha inválida:', dateInput)
      return ''
    }

    if (includeTime) {
      return date.toLocaleString(LOCALE_ES_CO, {
        timeZone: COLOMBIA_TIMEZONE,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false // Formato 24 horas
      })
    } else {
      return date.toLocaleDateString(LOCALE_ES_CO, {
        timeZone: COLOMBIA_TIMEZONE,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      })
    }
  } catch (error) {
    console.error('Error al formatear fecha:', error, dateInput)
    return ''
  }
}

/**
 * Formatea fecha solo con hora (formato colombiano de 12 horas)
 * @param {string|Date} dateInput - Fecha a formatear
 * @returns {string} Hora formateada (ej: "5:39 PM")
 */
export const formatColombianTime = (dateInput) => {
  if (!dateInput) return ''
  
  try {
    let date
    
    if (typeof dateInput === 'string') {
      const dateStr = dateInput.includes(' ') ? dateInput : `${dateInput} 00:00:00`
      date = new Date(dateStr + ' -05:00')
    } else {
      date = new Date(dateInput)
    }

    if (isNaN(date.getTime())) {
      return ''
    }

    return date.toLocaleTimeString(LOCALE_ES_CO, {
      timeZone: COLOMBIA_TIMEZONE,
      hour: 'numeric',
      minute: '2-digit',
      hour12: true // Formato 12 horas (AM/PM)
    })
  } catch (error) {
    console.error('Error al formatear hora:', error, dateInput)
    return ''
  }
}

/**
 * Formatea fecha compacta para facturas (dd/mm/yyyy, h:mm AM/PM)
 * @param {string|Date} dateInput - Fecha a formatear
 * @returns {string} Fecha y hora formateada
 */
export const formatInvoiceDate = (dateInput) => {
  if (!dateInput) return ''
  
  try {
    let date
    
    if (typeof dateInput === 'string') {
      // Si ya es formato ISO completo, usar directamente
      if (dateInput.includes('T') || dateInput.includes('Z')) {
        date = new Date(dateInput)
      } 
      // Si incluye espacio, es formato datetime
      else if (dateInput.includes(' ')) {
        date = new Date(dateInput + ' -05:00')
      } 
      // Si es solo fecha, agregar hora
      else {
        date = new Date(`${dateInput} 00:00:00 -05:00`)
      }
    } else {
      date = new Date(dateInput)
    }

    if (isNaN(date.getTime())) {
      console.error('formatInvoiceDate: Fecha inválida:', dateInput)
      return ''
    }

    const datePart = date.toLocaleDateString(LOCALE_ES_CO, {
      timeZone: COLOMBIA_TIMEZONE,
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    })
    
    const timePart = date.toLocaleTimeString(LOCALE_ES_CO, {
      timeZone: COLOMBIA_TIMEZONE,
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    })
    
    return `${datePart}, ${timePart}`
  } catch (error) {
    console.error('Error al formatear fecha de factura:', error, dateInput)
    return ''
  }
}

/**
 * Formatea fecha corta para reportes (dd/mm/aa)
 * @param {string|Date} dateInput - Fecha a formatear
 * @returns {string} Fecha formateada
 */
export const formatShortDate = (dateInput) => {
  if (!dateInput) return ''
  
  try {
    let date
    
    if (typeof dateInput === 'string') {
      const dateStr = dateInput.includes(' ') ? dateInput : `${dateInput} 00:00:00`
      date = new Date(dateStr + ' -05:00')
    } else {
      date = new Date(dateInput)
    }

    if (isNaN(date.getTime())) {
      return ''
    }

    return date.toLocaleDateString(LOCALE_ES_CO, {
      timeZone: COLOMBIA_TIMEZONE,
      day: '2-digit',
      month: '2-digit',
      year: '2-digit'
    })
  } catch (error) {
    console.error('Error al formatear fecha corta:', error, dateInput)
    return ''
  }
}

/**
 * Obtiene la fecha/hora actual de Colombia
 * @returns {Date} Fecha actual en timezone de Colombia
 */
export const nowColombian = () => {
  return new Date(new Date().toLocaleString("en-US", {timeZone: COLOMBIA_TIMEZONE}))
}

/**
 * Convierte una fecha a formato MySQL considerando timezone de Colombia
 * @param {Date} date - Fecha a convertir
 * @returns {string} Fecha en formato MySQL (YYYY-MM-DD HH:mm:ss)
 */
export const toMySQLDateTime = (date = new Date()) => {
  // Convertir a zona horaria de Colombia
  const colombianDate = new Date(date.toLocaleString("en-US", {timeZone: COLOMBIA_TIMEZONE}))
  
  const year = colombianDate.getFullYear()
  const month = String(colombianDate.getMonth() + 1).padStart(2, '0')
  const day = String(colombianDate.getDate()).padStart(2, '0')
  const hours = String(colombianDate.getHours()).padStart(2, '0')
  const minutes = String(colombianDate.getMinutes()).padStart(2, '0')
  const seconds = String(colombianDate.getSeconds()).padStart(2, '0')
  
  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`
}