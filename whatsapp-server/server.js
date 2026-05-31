require('dotenv').config();
const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const { default: makeWASocket, DisconnectReason, useMultiFileAuthState, fetchLatestBaileysVersion } = require('@whiskeysockets/baileys');
const pino = require('pino');
const QRCode = require('qrcode');

const app = express();
const PORT = process.env.PORT || 3000;
const HOST = process.env.HOST || 'localhost';

// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static('public')); // Serve static files from public folder

// Global variables
let sock = null;
let qrCodeData = null;
let connectionState = 'disconnected';
let reconnectAttempts = 0;
const MAX_RECONNECT_ATTEMPTS = parseInt(process.env.MAX_RECONNECT_ATTEMPTS) || 5;
const RECONNECT_INTERVAL = parseInt(process.env.RECONNECT_INTERVAL) || 5000;

// Logger
const logger = pino({ 
    level: process.env.LOG_LEVEL || 'info',
    transport: {
        target: 'pino-pretty',
        options: {
            colorize: true,
            translateTime: 'SYS:standard',
            ignore: 'pid,hostname'
        }
    }
});

// Initialize WhatsApp Connection
async function connectToWhatsApp() {
    try {
        const { state, saveCreds } = await useMultiFileAuthState(process.env.SESSION_NAME || 'spmb-wa-session');
        const { version } = await fetchLatestBaileysVersion();

        sock = makeWASocket({
            version,
            logger: pino({ level: 'silent' }),
            printQRInTerminal: true,
            auth: state,
            browser: ['SPMB Gateway', 'Chrome', '1.0.0'],
            defaultQueryTimeoutMs: undefined,
        });

        // Connection update handler
        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                qrCodeData = qr;
                connectionState = 'qr';
                logger.info('QR Code generated, scan with WhatsApp');
                
                // Generate QR code as data URL
                try {
                    const qrDataURL = await QRCode.toDataURL(qr);
                    qrCodeData = qrDataURL;
                } catch (err) {
                    logger.error('Failed to generate QR code:', err);
                }
            }

            if (connection === 'close') {
                const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
                connectionState = 'disconnected';
                logger.warn('Connection closed. Reconnect:', shouldReconnect);

                if (shouldReconnect && reconnectAttempts < MAX_RECONNECT_ATTEMPTS) {
                    reconnectAttempts++;
                    logger.info(`Reconnecting... Attempt ${reconnectAttempts}/${MAX_RECONNECT_ATTEMPTS}`);
                    setTimeout(connectToWhatsApp, RECONNECT_INTERVAL);
                } else if (reconnectAttempts >= MAX_RECONNECT_ATTEMPTS) {
                    logger.error('Max reconnect attempts reached. Please restart the server.');
                } else {
                    logger.info('Logged out. Please scan QR code again.');
                }
            } else if (connection === 'open') {
                connectionState = 'connected';
                reconnectAttempts = 0;
                qrCodeData = null;
                logger.info('WhatsApp connection established successfully!');
            }
        });

        // Credentials update handler
        sock.ev.on('creds.update', saveCreds);

        // Messages handler (optional - for receiving messages)
        sock.ev.on('messages.upsert', async ({ messages }) => {
            const msg = messages[0];
            if (!msg.key.fromMe && msg.message) {
                logger.info('Received message:', msg.message);
                // You can add auto-reply logic here if needed
            }
        });

    } catch (error) {
        logger.error('Failed to connect to WhatsApp:', error);
        connectionState = 'error';
        
        if (reconnectAttempts < MAX_RECONNECT_ATTEMPTS) {
            reconnectAttempts++;
            logger.info(`Retrying connection... Attempt ${reconnectAttempts}/${MAX_RECONNECT_ATTEMPTS}`);
            setTimeout(connectToWhatsApp, RECONNECT_INTERVAL);
        }
    }
}

// Helper function to format phone number
function formatPhoneNumber(phone) {
    // Remove all non-numeric characters
    let cleaned = phone.replace(/\D/g, '');
    
    // If starts with 0, replace with 62
    if (cleaned.startsWith('0')) {
        cleaned = '62' + cleaned.substring(1);
    }
    
    // If doesn't start with 62, add it
    if (!cleaned.startsWith('62')) {
        cleaned = '62' + cleaned;
    }
    
    return cleaned + '@s.whatsapp.net';
}

// API Routes

// Health check
app.get('/', (req, res) => {
    res.json({
        success: true,
        message: 'SPMB WhatsApp Gateway Server',
        version: '1.0.0',
        status: connectionState,
        timestamp: new Date().toISOString()
    });
});

// Get connection status
app.get('/status', (req, res) => {
    res.json({
        success: true,
        status: connectionState,
        qrAvailable: qrCodeData !== null,
        reconnectAttempts: reconnectAttempts,
        timestamp: new Date().toISOString()
    });
});

// Get QR code
app.get('/qr', (req, res) => {
    if (qrCodeData) {
        res.json({
            success: true,
            qr: qrCodeData,
            message: 'Scan this QR code with WhatsApp'
        });
    } else if (connectionState === 'connected') {
        res.json({
            success: false,
            message: 'Already connected to WhatsApp'
        });
    } else {
        res.json({
            success: false,
            message: 'QR code not available yet. Please wait...'
        });
    }
});

// Send single message
app.post('/send', async (req, res) => {
    try {
        const { phone, message } = req.body;

        if (!phone || !message) {
            return res.status(400).json({
                success: false,
                message: 'Phone and message are required'
            });
        }

        if (connectionState !== 'connected') {
            return res.status(503).json({
                success: false,
                message: 'WhatsApp not connected',
                status: connectionState
            });
        }

        const formattedPhone = formatPhoneNumber(phone);
        
        await sock.sendMessage(formattedPhone, { text: message });
        
        logger.info(`Message sent to ${phone}`);
        
        res.json({
            success: true,
            message: 'Message sent successfully',
            to: phone,
            timestamp: new Date().toISOString()
        });

    } catch (error) {
        logger.error('Failed to send message:', error);
        res.status(500).json({
            success: false,
            message: 'Failed to send message',
            error: error.message
        });
    }
});

// Send bulk messages
app.post('/send-bulk', async (req, res) => {
    try {
        const { messages } = req.body;

        if (!messages || !Array.isArray(messages)) {
            return res.status(400).json({
                success: false,
                message: 'Messages array is required'
            });
        }

        if (connectionState !== 'connected') {
            return res.status(503).json({
                success: false,
                message: 'WhatsApp not connected',
                status: connectionState
            });
        }

        const results = [];

        for (const item of messages) {
            try {
                const { phone, message } = item;
                
                if (!phone || !message) {
                    results.push({
                        phone,
                        success: false,
                        error: 'Phone and message are required'
                    });
                    continue;
                }

                const formattedPhone = formatPhoneNumber(phone);
                await sock.sendMessage(formattedPhone, { text: message });
                
                results.push({
                    phone,
                    success: true,
                    timestamp: new Date().toISOString()
                });

                logger.info(`Bulk message sent to ${phone}`);

                // Delay between messages to avoid spam detection
                await new Promise(resolve => setTimeout(resolve, 1000));

            } catch (error) {
                results.push({
                    phone: item.phone,
                    success: false,
                    error: error.message
                });
                logger.error(`Failed to send bulk message to ${item.phone}:`, error);
            }
        }

        const successCount = results.filter(r => r.success).length;
        const failedCount = results.length - successCount;

        res.json({
            success: true,
            message: `Sent ${successCount} messages, ${failedCount} failed`,
            total: results.length,
            successCount,
            failedCount,
            results
        });

    } catch (error) {
        logger.error('Failed to send bulk messages:', error);
        res.status(500).json({
            success: false,
            message: 'Failed to send bulk messages',
            error: error.message
        });
    }
});

// Logout/disconnect
app.post('/logout', async (req, res) => {
    try {
        if (sock) {
            await sock.logout();
            connectionState = 'disconnected';
            qrCodeData = null;
            reconnectAttempts = 0; // Reset reconnect attempts
            logger.info('Logged out from WhatsApp');
            
            // Auto-reconnect after logout to generate new QR code
            logger.info('Auto-reconnecting to generate new QR code...');
            setTimeout(connectToWhatsApp, 2000); // Wait 2 seconds before reconnecting
            
            res.json({
                success: true,
                message: 'Logged out successfully. Reconnecting to generate new QR code...'
            });
        } else {
            res.json({
                success: false,
                message: 'Not connected'
            });
        }
    } catch (error) {
        logger.error('Failed to logout:', error);
        res.status(500).json({
            success: false,
            message: 'Failed to logout',
            error: error.message
        });
    }
});

// Start server
app.listen(PORT, HOST, () => {
    logger.info(`WhatsApp Gateway Server running on http://${HOST}:${PORT}`);
    logger.info('Connecting to WhatsApp...');
    connectToWhatsApp();
});

// Graceful shutdown
process.on('SIGINT', async () => {
    logger.info('Shutting down gracefully...');
    if (sock) {
        await sock.end();
    }
    process.exit(0);
});
