import { createApp } from 'vue'
import App from './App.vue'
import router from './router/index.js'
import './style.css'

const app = createApp(App)

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

app.use(router)
app.mount('#app')