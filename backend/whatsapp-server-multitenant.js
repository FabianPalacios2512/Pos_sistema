#!/usr/bin/env node
/**
 * 📱 WhatsApp Multi-Tenant Server
 * Sistema que permite múltiples conexiones de WhatsApp por tenant
 * Cada tienda/tenant tiene su propia sesión independiente
 */

import { makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } from '@whiskeysockets/baileys';
import pino from 'pino';
import qrcodeTerminal from 'qrcode-terminal';
import fs from 'fs';
import http from 'http';
import url from 'url';
import path from 'path';

console.log('🚀 Iniciando servidor WhatsApp Multi-Tenant...');

// Mapa para almacenar las conexiones por tenant
const tenantConnections = new Map();
// Formato: { tenantId: { sock, isConnected, qr, lastActivity } }

// Configuración del logger
const logger = pino({ level: 'info' }, pino.destination('./whatsapp-multitenant.log'));

// Función para obtener el path de sesión de un tenant
function getTenantSessionPath(tenantId) {
    const sessionsDir = './whatsapp_sessions';
    if (!fs.existsSync(sessionsDir)) {
        fs.mkdirSync(sessionsDir, { recursive: true });
    }
    return path.join(sessionsDir, tenantId);
}

// Función para obtener el path del QR de un tenant
function getTenantQRPath(tenantId) {
    const qrDir = './whatsapp_qrs';
    if (!fs.existsSync(qrDir)) {
        fs.mkdirSync(qrDir, { recursive: true });
    }
    return path.join(qrDir, `${tenantId}_qr.txt`);
}

// Función para limpiar sesión de un tenant específico
async function cleanTenantSession(tenantId) {
    try {
        console.log(`🧹 Limpiando sesión del tenant ${tenantId}...`);

        const sessionPath = getTenantSessionPath(tenantId);
        const qrPath = getTenantQRPath(tenantId);

        // Limpiar archivos de sesión
        if (fs.existsSync(sessionPath)) {
            fs.rmSync(sessionPath, { recursive: true, force: true });
            console.log(`✅ Carpeta de sesión eliminada para ${tenantId}`);
        }

        // Limpiar QR
        if (fs.existsSync(qrPath)) {
            fs.unlinkSync(qrPath);
            console.log(`✅ Archivo QR eliminado para ${tenantId}`);
        }

        // Limpiar conexión del mapa
        if (tenantConnections.has(tenantId)) {
            const connection = tenantConnections.get(tenantId);
            if (connection.sock) {
                try {
                    await connection.sock.logout();
                    connection.sock.end();
                } catch (e) {
                    // Ignorar errores al cerrar
                }
            }
            tenantConnections.delete(tenantId);
        }

        console.log(`✅ Sesión limpiada completamente para ${tenantId}`);
        return true;

    } catch (error) {
        console.error(`❌ Error limpiando sesión de ${tenantId}:`, error);
        return false;
    }
}

// Función para conectar un tenant a WhatsApp
async function connectTenantToWhatsApp(tenantId) {
    try {
        console.log(`📱 Conectando tenant ${tenantId} a WhatsApp...`);

        const sessionPath = getTenantSessionPath(tenantId);
        const qrPath = getTenantQRPath(tenantId);

        const { state, saveCreds } = await useMultiFileAuthState(sessionPath);
        const { version } = await fetchLatestBaileysVersion();

        const sock = makeWASocket({
            version,
            logger,
            printQRInTerminal: false,
            auth: state,
            browser: [`POS-${tenantId}`, "Chrome", "20.0.04"],
            markOnlineOnConnect: false,
        });

        // Inicializar o actualizar conexión en el mapa
        if (!tenantConnections.has(tenantId)) {
            tenantConnections.set(tenantId, {
                sock: null,
                isConnected: false,
                qr: null,
                lastActivity: Date.now()
            });
        }

        const connection = tenantConnections.get(tenantId);
        connection.sock = sock;

        // Manejar actualizaciones de conexión
        sock.ev.on('connection.update', (update) => {
            const { connection: connStatus, lastDisconnect, qr } = update;

            if (qr) {
                console.log(`📱 QR Code generado para tenant ${tenantId}`);
                connection.qr = qr;

                try {
                    qrcodeTerminal.generate(qr, { small: true });
                    console.log(`\n>>> QR para tenant: ${tenantId} <<<\n`);
                } catch (e) {
                    console.log(`QR string para ${tenantId}:`, qr);
                }

                // Guardar QR en archivo
                try {
                    fs.writeFileSync(qrPath, qr);
                } catch (e) {
                    console.error(`Error guardando QR para ${tenantId}:`, e);
                }
            }

            if (connStatus === 'close') {
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

                console.log(`❌ Conexión cerrada para ${tenantId}. Código:`, statusCode, 'Reconectando:', shouldReconnect);
                connection.isConnected = false;
                connection.qr = null;

                // Limpiar sesión en casos específicos de error
                if (statusCode === DisconnectReason.badSession ||
                    statusCode === DisconnectReason.forbidden ||
                    statusCode === DisconnectReason.unauthorized ||
                    statusCode === DisconnectReason.multideviceMismatch) {

                    console.log(`🧹 Limpiando sesión de ${tenantId} por error de vinculación...`);
                    cleanTenantSession(tenantId).catch(console.error);
                }

                if (shouldReconnect) {
                    setTimeout(() => connectTenantToWhatsApp(tenantId), 3000);
                }
            } else if (connStatus === 'open') {
                console.log(`✅ WhatsApp conectado exitosamente para tenant ${tenantId}!`);
                connection.isConnected = true;
                connection.qr = null;
                connection.lastActivity = Date.now();
            }
        });

        // Guardar credenciales cuando cambien
        sock.ev.on('creds.update', saveCreds);

    } catch (error) {
        console.error(`❌ Error conectando tenant ${tenantId} a WhatsApp:`, error);
        setTimeout(() => connectTenantToWhatsApp(tenantId), 5000);
    }
}

// Función para enviar mensaje (por tenant)
async function sendTenantMessage(tenantId, phone, message, pdfPath = null, pdfBase64 = null, fileName = 'document.pdf') {
    try {
        const connection = tenantConnections.get(tenantId);

        if (!connection || !connection.isConnected || !connection.sock) {
            throw new Error(`WhatsApp no está conectado para el tenant ${tenantId}`);
        }

        // Formatear número de teléfono
        const jid = phone.includes('@') ? phone : `${phone.replace(/[^\d]/g, '')}@s.whatsapp.net`;

        // Opción 1: PDF desde base64 (método nuevo - usado por frontend)
        if (pdfBase64) {
            console.log(`📄 Enviando PDF en base64 a ${phone} desde tenant ${tenantId}`);
            // Convertir base64 a buffer (remover prefijo data:application/pdf;base64, si existe)
            const base64Data = pdfBase64.replace(/^data:application\/pdf;base64,/, '');
            const fileBuffer = Buffer.from(base64Data, 'base64');

            await connection.sock.sendMessage(jid, {
                document: fileBuffer,
                mimetype: 'application/pdf',
                fileName: fileName || 'factura.pdf',
                caption: message
            });
            console.log(`✅ PDF enviado exitosamente (${fileBuffer.length} bytes)`);
        }
        // Opción 2: PDF desde archivo (método antiguo - compatibilidad)
        else if (pdfPath && fs.existsSync(pdfPath)) {
            console.log(`📄 Enviando PDF desde archivo ${pdfPath}`);
            const fileBuffer = fs.readFileSync(pdfPath);
            await connection.sock.sendMessage(jid, {
                document: fileBuffer,
                mimetype: 'application/pdf',
                fileName: path.basename(pdfPath),
                caption: message
            });
            console.log(`✅ PDF enviado exitosamente desde archivo`);
        }
        // Opción 3: Solo texto
        else {
            console.log(`💬 Enviando solo texto a ${phone}`);
            await connection.sock.sendMessage(jid, { text: message });
            console.log(`✅ Mensaje de texto enviado`);
        }

        connection.lastActivity = Date.now();
        console.log(`✅ Mensaje enviado exitosamente a ${phone} desde tenant ${tenantId}`);

        return {
            success: true,
            message: 'Mensaje enviado exitosamente'
        };

    } catch (error) {
        console.error(`❌ Error enviando mensaje desde tenant ${tenantId}:`, error);
        return {
            success: false,
            error: error.message
        };
    }
}

// Función para obtener el estado de un tenant
function getTenantStatus(tenantId) {
    const connection = tenantConnections.get(tenantId);

    if (!connection) {
        return { connected: false, qr: null };
    }

    return {
        connected: connection.isConnected,
        qr: connection.qr,
        lastActivity: connection.lastActivity
    };
}

// Crear servidor HTTP
const server = http.createServer(async (req, res) => {
    const parsedUrl = url.parse(req.url, true);

    // CORS headers
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Tenant-Id');

    if (req.method === 'OPTIONS') {
        res.writeHead(200);
        res.end();
        return;
    }

    // Extraer tenant_id del header
    const tenantId = req.headers['x-tenant-id'] || 'default';
    console.log(`📡 Request: ${req.method} ${parsedUrl.pathname} | Tenant: ${tenantId}`);

    // ===== ENDPOINTS =====

    if (req.method === 'GET' && parsedUrl.pathname === '/status') {
        // Obtener estado de conexión del tenant
        const status = getTenantStatus(tenantId);
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            success: true,
            tenant_id: tenantId,
            status: status
        }));

    } else if (req.method === 'GET' && parsedUrl.pathname === '/qr') {
        // Obtener QR del tenant
        const qrPath = getTenantQRPath(tenantId);

        try {
            if (fs.existsSync(qrPath)) {
                const qrContent = fs.readFileSync(qrPath, 'utf8');
                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({
                    success: true,
                    tenant_id: tenantId,
                    qr_code: qrContent
                }));
            } else {
                const status = getTenantStatus(tenantId);
                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({
                    success: false,
                    tenant_id: tenantId,
                    message: status.connected ? 'Already connected' : 'QR not available yet',
                    connected: status.connected
                }));
            }
        } catch (error) {
            res.writeHead(500, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({
                success: false,
                error: error.message
            }));
        }

    } else if (req.method === 'POST' && parsedUrl.pathname === '/initialize') {
        // Inicializar conexión para el tenant
        const status = getTenantStatus(tenantId);

        if (status.connected) {
            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({
                success: true,
                tenant_id: tenantId,
                message: 'WhatsApp already connected',
                connected: true
            }));
        } else {
            // Iniciar conexión
            connectTenantToWhatsApp(tenantId);
            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({
                success: true,
                tenant_id: tenantId,
                message: 'WhatsApp initialization started',
                connected: false
            }));
        }

    } else if (req.method === 'POST' && parsedUrl.pathname === '/disconnect') {
        // Desconectar tenant
        try {
            const connection = tenantConnections.get(tenantId);

            if (connection && connection.sock) {
                await connection.sock.logout();
                connection.sock.end();
            }

            await cleanTenantSession(tenantId);

            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({
                success: true,
                tenant_id: tenantId,
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
        // Limpiar sesión del tenant
        try {
            await cleanTenantSession(tenantId);

            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({
                success: true,
                tenant_id: tenantId,
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
        // Enviar mensaje desde el tenant
        let body = '';
        req.on('data', chunk => {
            body += chunk.toString();
        });

        req.on('end', async () => {
            try {
                const { phone, message, pdfPath, pdfBase64, fileName } = JSON.parse(body);

                if (!phone || !message) {
                    res.writeHead(400, { 'Content-Type': 'application/json' });
                    res.end(JSON.stringify({ error: 'Phone and message are required' }));
                    return;
                }

                const result = await sendTenantMessage(tenantId, phone, message, pdfPath, pdfBase64, fileName);

                res.writeHead(result.success ? 200 : 500, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ ...result, tenant_id: tenantId }));

            } catch (error) {
                res.writeHead(500, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ error: error.message }));
            }
        });

    } else if (req.method === 'GET' && parsedUrl.pathname === '/tenants') {
        // Listar todos los tenants conectados (endpoint administrativo)
        const tenantsList = [];
        for (const [id, connection] of tenantConnections.entries()) {
            tenantsList.push({
                tenant_id: id,
                connected: connection.isConnected,
                has_qr: !!connection.qr,
                last_activity: connection.lastActivity
            });
        }

        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            success: true,
            total_tenants: tenantsList.length,
            tenants: tenantsList
        }));

    } else {
        res.writeHead(404, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ error: 'Not found' }));
    }
});

// Iniciar servidor HTTP
const PORT = parseInt(process.env.WHATSAPP_PORT || process.env.PORT || '3002', 10);
server.listen(PORT, () => {
    console.log(`🌐 Servidor WhatsApp Multi-Tenant ejecutándose en http://localhost:${PORT}`);
    console.log('📡 Endpoints disponibles:');
    console.log('  [GET]  /status       - Estado de conexión (usa header X-Tenant-Id)');
    console.log('  [GET]  /qr           - Obtener código QR (usa header X-Tenant-Id)');
    console.log('  [POST] /initialize   - Inicializar conexión (usa header X-Tenant-Id)');
    console.log('  [POST] /disconnect   - Desconectar WhatsApp (usa header X-Tenant-Id)');
    console.log('  [POST] /send         - Enviar mensaje (usa header X-Tenant-Id)');
    console.log('  [POST] /clean-session- Limpiar sesión (usa header X-Tenant-Id)');
    console.log('  [GET]  /tenants      - Listar todos los tenants conectados');
    console.log('');
    console.log('💡 Cada tenant debe enviar su ID en el header: X-Tenant-Id');
});

// Limpiar sesiones inactivas cada hora
setInterval(() => {
    const now = Date.now();
    const maxInactivity = 3600000; // 1 hora

    for (const [tenantId, connection] of tenantConnections.entries()) {
        if (!connection.isConnected && (now - connection.lastActivity) > maxInactivity) {
            console.log(`🧹 Limpiando sesión inactiva de tenant ${tenantId}`);
            cleanTenantSession(tenantId).catch(console.error);
        }
    }
}, 3600000); // Cada hora

// Manejar cierre graceful
process.on('SIGINT', async () => {
    console.log('\n🛑 Cerrando servidor WhatsApp Multi-Tenant...');

    // Cerrar todas las conexiones
    for (const [tenantId, connection] of tenantConnections.entries()) {
        if (connection.sock) {
            try {
                connection.sock.end();
            } catch (e) {
                // Ignorar errores
            }
        }
    }

    server.close(() => {
        process.exit(0);
    });
});
