/**
 * clientErrorReporter.js
 *
 * Captura errores del navegador que el backend no puede ver por sí solo:
 *   - Imágenes 404 (productos con image_url rota)
 *   - Errores de JavaScript en tiempo de ejecución
 *   - Errores de red de axios (servicios caídos como WhatsApp)
 *
 * Solo actúa cuando el usuario tiene sesión activa (token en localStorage).
 * Usa fetch nativo para evitar pasar por el interceptor de axios que hace logout en 401.
 */

const _reportedKeys = new Set()
const DEBOUNCE_MS = 5 * 60 * 1000
const _lastReport = new Map()

function shouldReport(key) {
  const now = Date.now()
  const last = _lastReport.get(key) || 0
  if (now - last < DEBOUNCE_MS) return false
  _lastReport.set(key, now)
  return true
}

function getToken() {
  return localStorage.getItem('authToken')
}

async function sendReport({ type, message, severity = 'warning', url = null, context = null }) {
  const token = getToken()
  if (!token) return

  try {
    await fetch('/api/errors/report', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({ type, message, severity, url, context }),
    })
  } catch {
    // Nunca romper la app
  }
}

/** Captura imágenes 404 de productos/storage */
function listenForImageErrors() {
  document.addEventListener(
    'error',
    (event) => {
      const el = event.target
      if (!el || el.tagName !== 'IMG') return
      const src = el.src || ''
      if (!src || !src.includes('/storage/')) return

      if (_reportedKeys.has(src)) return
      _reportedKeys.add(src)

      const key = `img-404:${src}`
      if (!shouldReport(key)) return

      sendReport({
        type: 'BrokenProductImage',
        severity: 'warning',
        message: `Imagen de producto no encontrada: ${src}`,
        url: window.location.href,
        context: { image_src: src, page: window.location.pathname },
      })
    },
    true
  )
}

/** Captura errores de JavaScript no manejados */
function listenForJsErrors() {
  window.addEventListener('error', (event) => {
    // Ignorar errores de recursos (imágenes, scripts externos) — ya manejados arriba
    if (event.target && event.target !== window) return

    const msg = event.message || 'Unknown JS error'
    const src = event.filename || ''
    // Ignorar errores de extensiones del navegador
    if (src.startsWith('chrome-extension://') || src.startsWith('moz-extension://')) return

    const key = `js-error:${msg}:${src}:${event.lineno}`
    if (_reportedKeys.has(key)) return
    _reportedKeys.add(key)
    if (!shouldReport(key)) return

    sendReport({
      type: 'JavaScriptError',
      severity: 'error',
      message: `${msg} (${src}:${event.lineno})`,
      url: window.location.href,
      context: {
        filename: src,
        lineno: event.lineno,
        colno: event.colno,
      },
    })
  })

  window.addEventListener('unhandledrejection', (event) => {
    const reason = event.reason
    const msg = reason instanceof Error
      ? reason.message
      : (typeof reason === 'string' ? reason : JSON.stringify(reason))

    if (!msg) return

    // Ignorar errores de red esperados (WhatsApp server no corriendo, etc.)
    const ignoredPatterns = [
      'ERR_CONNECTION_REFUSED',
      'Network Error',
      'net::ERR_',
    ]
    if (ignoredPatterns.some(p => msg.includes(p))) return

    const key = `unhandled-rejection:${msg}`
    if (_reportedKeys.has(key)) return
    _reportedKeys.add(key)
    if (!shouldReport(key)) return

    sendReport({
      type: 'UnhandledPromiseRejection',
      severity: 'error',
      message: msg,
      url: window.location.href,
      context: { type: reason?.constructor?.name },
    })
  })
}

/**
 * Inicia el reporter. Llamar una sola vez desde main.js.
 */
export function initClientErrorReporter() {
  listenForImageErrors()
  listenForJsErrors()
}
