import { createApp } from 'vue'
import App from './App.vue'
import router from './router/index.js'
import './style.css'

// Registrar PWA Service Worker
import { registerSW } from 'virtual:pwa-register'

const updateSW = registerSW({
	immediate: true, // Forzar actualización inmediata
	onNeedRefresh() {
		// Auto-actualizar sin preguntar para forzar limpieza del SW viejo
		updateSW(true)
	},
	onOfflineReady() {
		console.log('✅ Aplicación lista para funcionar sin conexión')
	},
	onRegistered(r) {
		// Forzar verificación de actualización cada 30 segundos
		r && setInterval(() => {
			r.update()
		}, 30000)
	}
})

// Configurar interceptor offline para Axios
import { setupOfflineInterceptor } from './utils/offlineInterceptor.js'
setupOfflineInterceptor()

// Inicializar validador de tiempo offline
import offlineTimeValidator from './utils/offlineTimeValidator.js'
// El validador se inicializa automáticamente al importarse

import { createPinia } from 'pinia'

const app = createApp(App)
const pinia = createPinia()

	// Suppress verbose console.log/debug output application-wide unless explicitly enabled.
	// This is a low-risk way to remove the large amount of debug spam the app produces
	// without having to edit every file. To re-enable logs for debugging, open devtools
	// and run: localStorage.setItem('POS_DEBUG', '1'); location.reload()
	; (function () {
		try {
			// TEMPORALMENTE HABILITADO PARA DEBUG
			const debug = localStorage.getItem('POS_DEBUG') || '1' // Habilitado por defecto
			if (!debug) {
				// keep error/warn/trace intact, only silence console.log and console.debug
				console._originalLog = console.log.bind(console)
				console._originalDebug = console.debug ? console.debug.bind(console) : null
				console.log = function () { }
				console.debug = function () { }
			}
		} catch (e) {
			// ignore any error accessing localStorage in restricted contexts
		}
	})()

app.use(pinia)
app.use(router)
app.mount('#app')