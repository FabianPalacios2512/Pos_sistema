import {
    default as makeWASocket,
    DisconnectReason,
    useMultiFileAuthState
} from '@whiskeysockets/baileys'
import qrcode from 'qrcode-terminal'
import fs from 'fs'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

class WhatsAppService {
    constructor() {
        this.sock = null
        this.isConnected = false
        this.qrCode = null
        this.sessionPath = path.join(__dirname, 'whatsapp-session')
    }

    async initialize() {
        try {
            console.log('🚀 Iniciando servicio de WhatsApp...')

            // Crear directorio de sesión si no existe
            if (!fs.existsSync(this.sessionPath)) {
                fs.mkdirSync(this.sessionPath, { recursive: true })
            }

            // Configurar Baileys con configuraciones más estables
            const { state, saveCreds } = await useMultiFileAuthState(this.sessionPath)

            this.sock = makeWASocket({
                auth: state,
                printQRInTerminal: true,
                browser: ['Ubuntu', 'Chrome', '20.0.04'],
                connectTimeoutMs: 60000,
                defaultQueryTimeoutMs: 60000,
                keepAliveIntervalMs: 10000,
                retryRequestDelayMs: 250,
                maxMsgRetryCount: 5,
                syncFullHistory: false,
                markOnlineOnConnect: true
            })

            // Eventos
            this.sock.ev.on('connection.update', (update) => {
                const { connection, lastDisconnect, qr } = update

                if (qr) {
                    console.log('\n📱 Escanea este código QR con WhatsApp:')
                    qrcode.generate(qr, { small: true })
                    this.qrCode = qr
                }

                if (connection === 'close') {
                    const shouldReconnect = (lastDisconnect?.error)?.output?.statusCode !== DisconnectReason.loggedOut
                    console.log('❌ Conexión cerrada. Reconectando:', shouldReconnect)
                    this.isConnected = false

                    if (shouldReconnect && (lastDisconnect?.error)?.output?.statusCode !== DisconnectReason.badSession) {
                        console.log('⏳ Conectando...')
                        setTimeout(() => this.initialize(), 5000)
                    } else if ((lastDisconnect?.error)?.output?.statusCode === DisconnectReason.badSession) {
                        console.log('🔄 Sesión corrupta, limpiando...')
                        if (fs.existsSync(this.sessionPath)) {
                            fs.rmSync(this.sessionPath, { recursive: true, force: true })
                        }
                        setTimeout(() => this.initialize(), 2000)
                    }
                } else if (connection === 'open') {
                    console.log('✅ WhatsApp conectado exitosamente!')
                    this.isConnected = true
                    this.qrCode = null
                } else if (connection === 'connecting') {
                    console.log('⏳ Conectando...')
                }
            })

            this.sock.ev.on('creds.update', saveCreds)

        } catch (error) {
            console.error('❌ Error inicializando WhatsApp:', error)
            throw error
        }
    }

    async sendMessage(phoneNumber, message, pdfBuffer = null, fileName = 'factura.pdf') {
        try {
            if (!this.isConnected || !this.sock) {
                throw new Error('WhatsApp no está conectado')
            }

            // Limpiar y formatear número
            const cleanPhone = phoneNumber.replace(/[^\d]/g, '')
            const formattedPhone = cleanPhone.startsWith('57') ?
                `${cleanPhone}@s.whatsapp.net` :
                `57${cleanPhone}@s.whatsapp.net`

            console.log(`📤 Enviando mensaje a: ${formattedPhone}`)

            // Enviar mensaje de texto
            await this.sock.sendMessage(formattedPhone, { text: message })

            // Si hay PDF, enviarlo como documento
            if (pdfBuffer) {
                await this.sock.sendMessage(formattedPhone, {
                    document: pdfBuffer,
                    fileName: fileName,
                    mimetype: 'application/pdf',
                    caption: 'Factura adjunta 📄'
                })
            }

            console.log('✅ Mensaje enviado exitosamente')
            return { success: true, phone: formattedPhone }

        } catch (error) {
            console.error('❌ Error enviando mensaje:', error)
            throw new Error(`Error enviando WhatsApp: ${error.message}`)
        }
    }

    getStatus() {
        return {
            connected: this.isConnected,
            qrCode: this.qrCode,
            needsQR: !this.isConnected && this.qrCode !== null
        }
    }

    async disconnect() {
        if (this.sock) {
            await this.sock.logout()
            this.isConnected = false
        }
    }
}

// Crear instancia singleton
const whatsappService = new WhatsAppService()

// Inicializar automáticamente
whatsappService.initialize().catch(console.error)

export default whatsappService
