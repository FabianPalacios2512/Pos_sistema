// Configuración para silenciar logs de Workbox
if (typeof workbox !== 'undefined') {
  // Establecer nivel de log en silencio (solo errores críticos)
  workbox.setConfig({
    debug: false
  });
  
  // Sobrescribir console.log del contexto de workbox
  const originalLog = console.log;
  console.log = function(...args) {
    // Filtrar mensajes de workbox
    if (args[0] && typeof args[0] === 'string' && args[0].includes('workbox')) {
      return; // Silenciar
    }
    originalLog.apply(console, args);
  };
}
