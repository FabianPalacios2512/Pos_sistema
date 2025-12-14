import whatsappService from './whatsapp-service.js'

console.log('🚀 Iniciando servicio de WhatsApp...')
console.log('📱 Si es la primera vez, aparecerá un código QR para escanear.')
console.log('💡 Usa tu WhatsApp Business para escanear el código.')
console.log('⏳ Mantén este proceso corriendo para recibir mensajes...')
console.log('')
console.log('Para detener: Ctrl+C')
console.log('=' .repeat(50))

// Mostrar estado cada 10 segundos
setInterval(() => {
    const status = whatsappService.getStatus()
    if (status.connected) {
        console.log('✅ WhatsApp conectado y listo')
    } else if (status.needsQR) {
        console.log('📱 Esperando escaneo del código QR...')
    } else {
        console.log('⏳ Conectando...')
    }
}, 10000)

// Mantener el proceso vivo
process.on('SIGINT', async () => {
    console.log('\n🛑 Cerrando conexión WhatsApp...')
    await whatsappService.disconnect()
    process.exit(0)
})
