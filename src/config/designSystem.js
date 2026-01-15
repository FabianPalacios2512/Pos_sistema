/**
 * 🎨 Sistema de Diseño Oficial - POS Empresarial
 * 
 * Basado en: Usuarios y Roles, Gastos Operativos, Control de Cajas
 * Última actualización: 5 de diciembre de 2025
 */

export const designSystem = {
  // ============================================
  // 🌈 GRADIENTES DE FONDO
  // ============================================
  gradients: {
    light: 'bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200',
    dark: 'dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c]',
    full: 'bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c]'
  },

  // ============================================
  // 📦 CONTENEDORES Y PANELES
  // ============================================
  containers: {
    // Contenedor principal de página
    page: 'min-h-screen font-sans transition-colors duration-300 px-8',
    
    // Panel/Tarjeta principal
    panel: 'bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800',
    
    // Panel más pequeño
    card: 'bg-white dark:bg-zinc-900 rounded-xl shadow-sm dark:shadow-black/50 border border-gray-200 dark:border-zinc-800',
  },

  // ============================================
  // 🎯 KPIs CON ESTILO MODERNO
  // ============================================
  kpis: {
    // Card de KPI (sin blur para mejor rendimiento)
    card: 'bg-white dark:bg-zinc-900/95 rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800 hover:border-gray-400 dark:hover:border-zinc-700 transition-colors duration-150 shadow-md dark:shadow-lg dark:shadow-black/50',
    
    // Contenedor de icono
    iconContainer: 'w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5',
    
    // Icono (colores por categoría)
    icon: {
      emerald: 'text-emerald-600 dark:text-emerald-400',
      blue: 'text-blue-600 dark:text-blue-400',
      amber: 'text-amber-600 dark:text-amber-400',
      purple: 'text-purple-600 dark:text-purple-400',
      red: 'text-red-600 dark:text-red-400',
      indigo: 'text-indigo-600 dark:text-indigo-400'
    },
    
    // Textos
    label: 'text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide',
    value: 'text-2xl font-bold text-gray-900 dark:text-white mt-0.5',
    subtext: 'text-xs text-gray-500 dark:text-zinc-400 mt-0.5'
  },

  // ============================================
  // 🔘 BOTONES
  // ============================================
  buttons: {
    // Botón principal
    primary: 'px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300',
    
    // Botón secundario
    secondary: 'px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200',
    
    // Botón de icono en tablas (editar)
    iconEdit: 'p-2 rounded-lg border border-transparent text-slate-400 dark:text-zinc-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:border-amber-100 dark:hover:border-amber-900/30 transition-all duration-200',
    
    // Botón de icono en tablas (ver)
    iconView: 'p-2 rounded-lg border border-transparent text-slate-400 dark:text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-100 dark:hover:border-blue-900/30 transition-all duration-200',
    
    // Botón de icono en tablas (eliminar)
    iconDelete: 'p-2 rounded-lg border border-transparent text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:border-rose-100 dark:hover:border-rose-900/30 transition-all duration-200'
  },

  // ============================================
  // 📋 TABS NAVIGATION
  // ============================================
  tabs: {
    container: 'bg-gray-50 dark:bg-zinc-800 rounded-xl p-1 inline-flex border border-gray-200 dark:border-zinc-700 h-[46px]',
    buttonActive: 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm',
    buttonInactive: 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white',
    buttonBase: 'px-5 py-2.5 text-sm font-bold rounded-lg transition-all duration-200'
  },

  // ============================================
  // 📊 TABLAS
  // ============================================
  tables: {
    // Header de tabla
    header: 'bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-4 py-3',
    
    // Thead
    thead: 'bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800',
    theadText: 'text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider',
    
    // Tbody
    tbody: 'bg-white dark:bg-zinc-900',
    
    // Fila con hover
    row: 'hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-all duration-200 border-b border-gray-100 dark:border-zinc-800',
    
    // Texto en celdas
    cellText: 'text-sm text-gray-700 dark:text-zinc-300',
    cellTextBold: 'text-sm font-semibold text-gray-900 dark:text-white'
  },

  // ============================================
  // 📝 INPUTS Y FORMULARIOS
  // ============================================
  inputs: {
    // Input de búsqueda
    search: 'w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent',
    
    // Select
    select: 'px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400',
    
    // Input de fecha/calendario
    date: 'px-3 py-3 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
    
    // Input de texto en modales
    text: 'px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
    
    // Textarea
    textarea: 'w-full px-3 py-2 text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all resize-none'
  },

  // ============================================
  // 🏷️ BADGES
  // ============================================
  badges: {
    // Éxito / Activo
    success: 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
    
    // Error / Inactivo
    error: 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800',
    
    // Información / Primario
    info: 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
    
    // Advertencia
    warning: 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800',
    
    // Especial / Púrpura
    special: 'bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-800',
    
    // Base para badges pequeños
    baseSmall: 'px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide',
    
    // Base para badges medianos
    baseMedium: 'px-2 py-0.5 rounded-md text-xs font-medium border'
  },

  // ============================================
  // 📄 MODALES
  // ============================================
  modals: {
    overlay: 'fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4',
    container: 'bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 max-w-2xl w-full max-h-[90vh] overflow-y-auto',
    header: 'border-b border-gray-200 dark:border-zinc-800 px-6 py-4 bg-gray-50 dark:bg-zinc-900',
    body: 'px-6 py-4',
    footer: 'bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-3 flex justify-end gap-2.5'
  },

  // ============================================
  // 🎨 COLORES DE TEXTO
  // ============================================
  text: {
    // Títulos
    title: 'text-2xl font-bold text-gray-900 dark:text-white',
    subtitle: 'text-sm text-gray-600 dark:text-zinc-400 mt-1',
    
    // Textos regulares
    primary: 'text-gray-900 dark:text-white',
    secondary: 'text-gray-700 dark:text-zinc-300',
    tertiary: 'text-gray-600 dark:text-zinc-400',
    muted: 'text-gray-500 dark:text-zinc-500'
  },

  // ============================================
  // 📦 ELEMENTOS ESPECIALES
  // ============================================
  special: {
    // Empty state
    emptyIcon: 'w-16 h-16 mx-auto bg-gray-100 dark:bg-zinc-800/50 rounded-full flex items-center justify-center mb-4',
    emptyTitle: 'text-lg font-semibold text-gray-900 dark:text-white mb-2',
    emptyText: 'text-gray-500 dark:text-zinc-400',
    
    // Loading spinner
    spinner: 'animate-spin rounded-full h-10 w-10 border-4 border-gray-200 dark:border-zinc-700 border-t-blue-600 dark:border-t-blue-400',
    
    // Divider
    divider: 'border-b border-gray-200 dark:border-zinc-800'
  }
}

/**
 * Función helper para combinar clases del sistema de diseño
 */
export function combineClasses(...classes) {
  return classes.filter(Boolean).join(' ')
}

/**
 * Función para obtener clases de badge según el tipo
 */
export function getBadgeClasses(type, size = 'small') {
  const baseClass = size === 'small' ? designSystem.badges.baseSmall : designSystem.badges.baseMedium
  const typeClass = designSystem.badges[type] || designSystem.badges.info
  return `${baseClass} ${typeClass}`
}

export default designSystem
