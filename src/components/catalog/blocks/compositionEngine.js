import { blocks } from './blockRegistry.js'

/**
 * Check if a hex color code represents a dark color (using WCAG formula)
 */
export function isDarkColor(hex) {
  if (!hex) return false
  const cleanHex = hex.replace('#', '')
  if (cleanHex.length !== 6) return false
  const r = parseInt(cleanHex.substring(0, 2), 16)
  const g = parseInt(cleanHex.substring(2, 4), 16)
  const b = parseInt(cleanHex.substring(4, 6), 16)
  const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255
  return luminance < 0.5
}

/**
 * Validates the layout composition against block registry and color palette.
 * Returns an array of warning objects describing conflicts.
 */
export function validateComposition(layoutConfig, palette = {}) {
  const warnings = []
  const bgIsDark = isDarkColor(palette.background || '#ffffff')

  // Header Validation
  const headerKey = layoutConfig.header_style
  const headerMeta = blocks.headers[headerKey]
  if (headerMeta) {
    if (bgIsDark && headerMeta.bgCompatibility === 'light') {
      warnings.push({
        section: 'header',
        code: 'HEADER_LIGHT_ONLY',
        message: `El header "${headerMeta.name}" no está diseñado para fondos oscuros.`
      })
    } else if (!bgIsDark && headerMeta.requiresDark) {
      warnings.push({
        section: 'header',
        code: 'HEADER_DARK_ONLY',
        message: `El header "${headerMeta.name}" requiere un fondo oscuro para verse correctamente.`
      })
    }
  }

  // Hero Validation
  const heroKey = layoutConfig.hero_style
  const heroMeta = blocks.heroes[heroKey]
  if (heroMeta) {
    if (bgIsDark && heroMeta.bgCompatibility === 'light') {
      warnings.push({
        section: 'hero',
        code: 'HERO_LIGHT_ONLY',
        message: `El hero "${heroMeta.name}" no está diseñado para fondos oscuros.`
      })
    } else if (!bgIsDark && heroMeta.requiresDark) {
      warnings.push({
        section: 'hero',
        code: 'HERO_DARK_ONLY',
        message: `El hero "${heroMeta.name}" requiere un fondo oscuro para verse correctamente.`
      })
    }
  }

  // Hook Validation
  const hookKey = layoutConfig.hook_style
  const hookMeta = blocks.hooks[hookKey]
  if (hookMeta) {
    if (bgIsDark && hookMeta.bgCompatibility === 'light') {
      warnings.push({
        section: 'hook',
        code: 'HOOK_LIGHT_ONLY',
        message: `El hook "${hookMeta.name}" no está diseñado para fondos oscuros.`
      })
    } else if (!bgIsDark && hookMeta.requiresDark) {
      warnings.push({
        section: 'hook',
        code: 'HOOK_DARK_ONLY',
        message: `El hook "${hookMeta.name}" requiere un fondo oscuro para verse correctamente.`
      })
    }
  }

  return warnings
}

/**
 * Returns alternative block options for a given section, filtered by background compatibility
 */
export function getCompatibleBlocks(section, bgIsDark) {
  const sectionKey = section === 'trust_strip' ? 'trustStrips' : `${section}s`
  const pool = blocks[sectionKey] || {}
  
  return Object.entries(pool)
    .filter(([_, meta]) => {
      if (bgIsDark) {
        return meta.bgCompatibility !== 'light'
      } else {
        return !meta.requiresDark
      }
    })
    .map(([key, meta]) => ({ key, ...meta }))
}

/**
 * Autocorrects layoutConfig issues based on background luminance.
 * Returns a new compatible layoutConfig.
 */
export function normalizeComposition(layoutConfig, palette = {}) {
  const normalized = { ...layoutConfig }
  const bgIsDark = isDarkColor(palette.background || '#ffffff')

  // Normalizar Header
  const headerMeta = blocks.headers[normalized.header_style]
  if (!headerMeta || (bgIsDark && headerMeta.bgCompatibility === 'light') || (!bgIsDark && headerMeta.requiresDark)) {
    // Escoger una alternativa válida
    const alternatives = getCompatibleBlocks('header', bgIsDark)
    if (alternatives.length > 0) {
      normalized.header_style = alternatives[0].key
    }
  }

  // Normalizar Hero
  const heroMeta = blocks.heroes[normalized.hero_style]
  if (!heroMeta || (bgIsDark && heroMeta.bgCompatibility === 'light') || (!bgIsDark && heroMeta.requiresDark)) {
    const alternatives = getCompatibleBlocks('hero', bgIsDark)
    if (alternatives.length > 0) {
      normalized.hero_style = alternatives[0].key
    }
  }

  // Normalizar Hook
  const hookMeta = blocks.hooks[normalized.hook_style]
  if (!hookMeta || (bgIsDark && hookMeta.bgCompatibility === 'light') || (!bgIsDark && hookMeta.requiresDark)) {
    const alternatives = getCompatibleBlocks('hook', bgIsDark)
    if (alternatives.length > 0) {
      normalized.hook_style = alternatives[0].key
    }
  }

  // Normalizar Trust Strip
  const trustMeta = blocks.trustStrips[normalized.trust_strip_style]
  if (!trustMeta) {
    const alternatives = getCompatibleBlocks('trust_strip', bgIsDark)
    if (alternatives.length > 0) {
      normalized.trust_strip_style = alternatives[0].key
    }
  }

  return normalized
}
