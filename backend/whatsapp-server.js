#!/usr/bin/env node
import { makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } from '@whiskeysockets/baileys';
import pino from 'pino';
import qrcodeTerminal from 'qrcode-terminal';
import fs from 'fs';
import http from 'http';
import url from 'url';

console.log('🚀 Iniciando servidor WhatsApp...');

let sock = null;
let isConnected = false;

// Configuración del logger
const logger = pino({ level: 'info' }, pino.destination('./whatsapp.log'));

// Función para limpiar sesión
async function cleanSession() {
    try {
        console.log('🧹 Limpiando archivos de sesión...');

        // Limpiar archivos de sesión
        if (fs.existsSync('./whatsapp_session')) {
            fs.rmSync('./whatsapp_session', { recursive: true, force: true });
            console.log('✅ Carpeta de sesión eliminada');
        }

        // Limpiar QR
        if (fs.existsSync('./whatsapp_qr.txt')) {
            fs.unlinkSync('./whatsapp_qr.txt');
            console.log('✅ Archivo QR eliminado');
        }

        // Reset variables
        sock = null;
        isConnected = false;

        console.log('✅ Sesión limpiada completamente');

    } catch (error) {
        console.error('❌ Error limpiando sesión:', error);
    }
}

async function connectToWhatsApp() {
    try {
        console.log('📱 Conectando a WhatsApp...');

        const { state, saveCreds } = await useMultiFileAuthState('./whatsapp_session');
        const { version } = await fetchLatestBaileysVersion();

        sock = makeWASocket({
            version,
            logger,
            printQRInTerminal: false, // Deshabilitamos QR en terminal
            auth: state,
            browser: ["Ubuntu", "Chrome", "20.0.04"],
            markOnlineOnConnect: false,
        });

        // Manejar actualizaciones de conexión
        sock.ev.on('connection.update', (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                console.log('📱 QR Code generado - Escanea con WhatsApp');
                // Mostrar QR en la terminal (usa qrcode-terminal)
                try {
                    qrcodeTerminal.generate(qr, { small: true });
                } catch (e) {
                    // Si no está instalado, mostrar la cadena QR para copiar manualmente
                    console.log('QR string (pegar en https://web.whatsapp.com/ or usar app):', qr);
                }
                // Además guardamos el QR en texto para poder abrirlo desde otro sitio si hace falta
                try {
                    fs.writeFileSync('./whatsapp_qr.txt', qr);
                } catch (e) {
                    // ignore
                }
            }

            if (connection === 'close') {
                const statusCode = (lastDisconnect?.error)?.output?.statusCode;
                const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

                console.log('❌ Conexión cerrada. Código:', statusCode, 'Reconectando:', shouldReconnect);
                isConnected = false;

                // Limpiar sesión en casos específicos de error
                if (statusCode === DisconnectReason.badSession ||
                    statusCode === DisconnectReason.forbidden ||
                    statusCode === DisconnectReason.unauthorized ||
                    statusCode === DisconnectReason.multideviceMismatch) {

                    console.log('🧹 Limpiando sesión por error de vinculación...');
                    cleanSession().catch(console.error);
                }

                if (shouldReconnect) {
                    setTimeout(connectToWhatsApp, 3000);
                }
            } else if (connection === 'open') {
                console.log('✅ WhatsApp conectado exitosamente!');
                isConnected = true;
            }
        });

        // Guardar credenciales cuando cambien
        sock.ev.on('creds.update', saveCreds);

    } catch (error) {
        console.error('❌ Error conectando a WhatsApp:', error);
        setTimeout(connectToWhatsApp, 5000);
    }
}

// Función para enviar mensaje
async function sendMessage(phone, message, pdfPath = null) {
    try {
        if (!isConnected || !sock) {
            throw new Error('WhatsApp no está conectado');
        }

        // Formatear número de teléfono
        const jid = phone.includes('@') ? phone : `${phone.replace(/[^\d]/g, '')}@s.whatsapp.net`;

        console.log(`📤 Enviando mensaje a ${jid}`);

        // ✅ NO USAR AWAIT - Enviar en background
        const sendPromise = (async () => {
            try {
                if (pdfPath && fs.existsSync(pdfPath)) {
                    // Enviar PDF
                    const mediaMessage = {
                        document: fs.readFileSync(pdfPath),
                        mimetype: 'application/pdf',
                        fileName: 'factura.pdf',
                        caption: message
                    };

                    await sock.sendMessage(jid, mediaMessage);
                    console.log('✅ PDF enviado exitosamente a', phone);
                } else {
                    // Enviar solo texto
                    await sock.sendMessage(jid, { text: message });
                    console.log('✅ Mensaje enviado exitosamente a', phone);
                }
            } catch (error) {
                console.error('❌ Error enviando mensaje a', phone, ':', error.message);
            }
        })();

        // ✅ RESPONDER INMEDIATAMENTE sin esperar
        return { success: true, message: 'Mensaje en cola para envío' };

    } catch (error) {
        console.error('❌ Error preparando mensaje:', error);
        return { success: false, error: error.message };
    }
}

// Crear servidor HTTP para recibir comandos
const server = http.createServer(async (req, res) => {
    // Configurar CORS
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    if (req.method === 'OPTIONS') {
        res.writeHead(200);
        res.end();
        return;
    }

    const parsedUrl = url.parse(req.url, true);

    if (req.method === 'GET' && parsedUrl.pathname === '/status') {
        // Endpoint de status
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            connected: isConnected,
            timestamp: new Date().toISOString()
        }));

    } else if (req.method === 'GET' && parsedUrl.pathname === '/qr') {
        // Endpoint para obtener código QR
        try {
            const qrContent = fs.readFileSync('./whatsapp_qr.txt', 'utf8');
            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({
                success: true,
                qr_code: qrContent,
                connected: isConnected
            }));
        } catch (error) {
            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({
                success: false,
                error: 'QR not available',
                connected: isConnected
            }));
        }

    } else if (req.method === 'POST' && parsedUrl.pathname === '/initialize') {
        // Endpoint para inicializar conexión
        res.writeHead(200, { 'Content-Type': 'application/json' });

        if (isConnected) {
            res.end(JSON.stringify({
                success: true,
                message: 'WhatsApp already connected',
                connected: true
            }));
        } else {
            // Reintentar conexión si no está conectado
            connectToWhatsApp();
            res.end(JSON.stringify({
                success: true,
                message: 'WhatsApp initialization started',
                connected: false
            }));
        }

    } else if (req.method === 'POST' && parsedUrl.pathname === '/disconnect') {
        // Endpoint para desconectar
        try {
            if (sock) {
                await sock.logout();
                sock.end();
            }

            // Usar la función de limpieza centralizada
            await cleanSession();

            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({
                success: true,
                message: 'WhatsApp disconnected successfully',
                connected: false
            }));
        } catch (error) {
            res.writeHead(500, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({
                success: false,
                error: error.message
            }));
        }

    } else if (req.method === 'POST' && parsedUrl.pathname === '/clean-session') {
        // Endpoint para limpiar sesión manualmente
        try {
            await cleanSession();

            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({
                success: true,
                message: 'Session cleaned successfully',
                connected: false
            }));
        } catch (error) {
            res.writeHead(500, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({
                success: false,
                error: error.message
            }));
        }

    } else if (req.method === 'POST' && parsedUrl.pathname === '/send') {
        // Endpoint para enviar mensajes
        let body = '';
        req.on('data', chunk => {
            body += chunk.toString();
        });

        req.on('end', async () => {
            try {
                const { phone, message, pdfPath } = JSON.parse(body);

                if (!phone || !message) {
                    res.writeHead(400, { 'Content-Type': 'application/json' });
                    res.end(JSON.stringify({ error: 'Phone and message are required' }));
                    return;
                }

                const result = await sendMessage(phone, message, pdfPath);

                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify(result));

            } catch (error) {
                res.writeHead(500, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ error: error.message }));
            }
        });

    } else {
        res.writeHead(404, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ error: 'Not found' }));
    }
});

// Iniciar servidor HTTP (permitir puerto configurable por entorno)
const PORT = parseInt(process.env.WHATSAPP_PORT || process.env.PORT || '3002', 10);
server.listen(PORT, () => {
    console.log(`🌐 Servidor WhatsApp ejecutándose en http://localhost:${PORT}`);
    console.log('📡 Endpoints disponibles:');
    console.log('  GET  /status      - Verificar estado de conexión');
    console.log('  GET  /qr          - Obtener código QR');
    console.log('  POST /initialize  - Inicializar conexión');
    console.log('  POST /disconnect  - Desconectar WhatsApp');
    console.log('  POST /send        - Enviar mensaje');
    console.log('');
});

// Iniciar conexión WhatsApp
connectToWhatsApp();

// Manejar cierre graceful
process.on('SIGINT', () => {
    console.log('\n🛑 Cerrando servidor WhatsApp...');
    if (sock) {
        sock.end();
    }
    server.close(() => {
        process.exit(0);
    });
});
