import whatsappService from './whatsapp-service.js'
import fs from 'fs'

async function sendWhatsApp() {
    try {
        // Obtener argumentos de la línea de comandos
        const [, , phone, message, pdfPath] = process.argv

        if (!phone || !message) {
            console.log('ERROR: Faltan argumentos (phone, message)')
            process.exit(1)
        }

        console.log('📱 Iniciando envío de WhatsApp...')
        console.log('Teléfono:', phone)
        console.log('Mensaje:', message.substring(0, 50) + '...')

        // Esperar a que WhatsApp esté conectado
        let attempts = 0
        const maxAttempts = 30 // 30 segundos máximo

        while (!whatsappService.getStatus().connected && attempts < maxAttempts) {
            console.log(`⏳ Esperando conexión WhatsApp... (${attempts + 1}/${maxAttempts})`)
            await new Promise(resolve => setTimeout(resolve, 1000))
            attempts++
        }

        if (!whatsappService.getStatus().connected) {
            const status = whatsappService.getStatus()
            if (status.needsQR) {
                console.log('ERROR: WhatsApp no está conectado. Necesitas escanear el código QR.')
                console.log('ERROR: Ejecuta "node whatsapp-service.js" y escanea el código QR primero.')
            } else {
                console.log('ERROR: WhatsApp no pudo conectarse después de 30 segundos.')
            }
            process.exit(1)
        }

        // Leer PDF si existe
        let pdfBuffer = null
        if (pdfPath && fs.existsSync(pdfPath)) {
            pdfBuffer = fs.readFileSync(pdfPath)
            console.log('📄 PDF encontrado, tamaño:', pdfBuffer.length, 'bytes')
        }

        // Enviar mensaje
        const result = await whatsappService.sendMessage(phone, message, pdfBuffer)

        console.log('SUCCESS: Mensaje enviado correctamente')
        console.log('Resultado:', JSON.stringify(result))

        process.exit(0)

    } catch (error) {
        console.log('ERROR:', error.message)
        process.exit(1)
    }
}

// Ejecutar si es llamado directamente
if (import.meta.url === `file://${process.argv[1]}`) {
    sendWhatsApp()
}
