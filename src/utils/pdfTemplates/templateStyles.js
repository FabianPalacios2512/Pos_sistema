/**
 * Estilos y configuraciones para los 3 templates de factura
 * Classic, Modern, Minimal
 */

/**
 * Template Clásico Profesional
 * Diseño limpio, minimalista y corporativo (Estilo Big 4 / Legal)
 */
export const classicStyle = {
  name: 'classic',
  colors: {
    primary: '#111827', // gray-900
    secondary: '#6B7280', // gray-500
    border: '#E5E7EB', // gray-200 (muy sutil)
    headerBg: '#FFFFFF', // Fondo blanco limpio
    totalBg: '#FFFFFF', // Fondo blanco
    accent: '#111827' // Negro para detalles
  },
  fonts: {
    company: { size: 16, style: 'bold' },
    title: { size: 10, style: 'bold' }, // Más discreto
    header: { size: 8, style: 'bold' },
    body: { size: 8, style: 'normal' },
    total: { size: 11, style: 'bold' }
  },
  layout: {
    borderWidth: 0.1, // Líneas muy finas
    borderRadius: 0,
    padding: 0,
    lineSpacing: 3.5,
    simpleHeader: true // Nuevo flag para header sin caja
  }
}

/**
 * Template Moderno Premium
 * Diseño SaaS vibrante con Indigo/Violeta
 */
export const modernStyle = {
  name: 'modern',
  colors: {
    primary: '#4F46E5', // Indigo-600 (SaaS moderno)
    secondary: '#4338CA', // Indigo-700
    headerBg: '#4F46E5', // Indigo-600
    headerText: '#FFFFFF',
    border: '#E0E7FF', // Indigo-100
    accentBg: '#EEF2FF', // Indigo-50
    totalBg: '#F5F3FF', // Violet-50
    totalText: '#4F46E5', // Indigo-600
    text: '#1E1B4B' // Indigo-950
  },
  fonts: {
    company: { size: 16, style: 'bold' },
    title: { size: 12, style: 'bold' },
    header: { size: 8, style: 'bold' },
    body: { size: 8, style: 'normal' },
    total: { size: 14, style: 'bold' }
  },
  layout: {
    borderWidth: 0.5,
    borderRadius: 4, // Bordes más redondeados
    padding: 6,
    lineSpacing: 4.5,
    useGradient: false, // Color sólido flat design
    headerPadding: 8
  }
}

/**
 * Template Minimalista Premium
 * Diseño elegante blanco y negro con bordes bold
 */
export const minimalStyle = {
  name: 'minimal',
  colors: {
    primary: '#000000',
    secondary: '#374151', // gray-700
    border: '#000000',
    headerBg: '#FFFFFF',
    totalBg: '#FFFFFF',
    divider: '#6B7280' // gray-500
  },
  fonts: {
    company: { size: 15, style: 'bold' },
    title: { size: 12, style: 'bold' },
    header: { size: 7, style: 'normal' },
    body: { size: 7, style: 'normal' },
    total: { size: 12, style: 'bold' }
  },
  layout: {
    borderWidth: 0.6, // Bordes más gruesos
    borderRadius: 0, // Sin redondeo
    padding: 5,
    lineSpacing: 3.5,
    boldDividers: true
  }
}

/**
 * Obtener estilo según el template seleccionado
 */
export const getTemplateStyle = (templateName) => {
  switch (templateName?.toLowerCase()) {
    case 'modern':
      return modernStyle
    case 'minimal':
      return minimalStyle
    case 'classic':
    default:
      return classicStyle
  }
}

/**
 * Aplicar color de header según template
 */
export const applyHeaderStyle = (pdf, style, x, y, width, height) => {
  if (style.name === 'modern') {
    // Header Indigo Moderno
    pdf.setFillColor(79, 70, 229) // Indigo-600
    pdf.roundedRect(x, y, width, height, style.layout.borderRadius, style.layout.borderRadius, 'F')
  } else if (style.name === 'classic') {
    // Classic: Sin fondo, solo línea inferior sutil
    // No dibujamos rectángulo de fondo
    pdf.setDrawColor(229, 231, 235) // gray-200
    pdf.setLineWidth(0.1)
    // pdf.line(x, y + height, x + width, y + height) // La línea se maneja en el template principal
  } else {
    // Minimal: sin fondo
    pdf.setLineWidth(style.layout.borderWidth)
    pdf.setDrawColor(0, 0, 0)
    pdf.rect(x, y, width, height)
  }
}

/**
 * Aplicar estilo de borde según template
 */
export const applyBorderStyle = (pdf, style) => {
  pdf.setLineWidth(style.layout.borderWidth)

  if (style.name === 'modern') {
    pdf.setDrawColor(224, 231, 255) // Indigo-100
  } else if (style.name === 'classic') {
    pdf.setDrawColor(229, 231, 235) // gray-200 (muy sutil)
  } else {
    pdf.setDrawColor(0, 0, 0) // Negro para minimal
  }
}

/**
 * Aplicar estilo de total según template
 */
export const applyTotalStyle = (pdf, style, x, y, width, height) => {
  if (style.name === 'modern') {
    pdf.setFillColor(238, 242, 255) // Indigo-50
    pdf.roundedRect(x, y, width, height, style.layout.borderRadius, style.layout.borderRadius, 'F')
  } else if (style.name === 'classic') {
    // Classic: Sin fondo, borde superior e inferior
    // Se maneja con líneas en el template principal, aquí no rellenamos
  } else {
    // Minimal: borde bold sin fondo
    pdf.setLineWidth(style.layout.borderWidth * 2)
    pdf.setDrawColor(0, 0, 0)
    pdf.rect(x, y, width, height)
  }
}
