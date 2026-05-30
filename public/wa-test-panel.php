<?php
/**
 * WhatsApp Gateway Testing Panel
 * PHP-based panel that works via Cloudflare Tunnel
 * Communicates with WhatsApp server from server-side (not browser)
 */

$waServerUrl = 'http://localhost:3000';
$pageTitle = 'WhatsApp Gateway - Testing Panel';

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'status':
            echo fetchStatus();
            break;
        case 'qr':
            echo fetchQR();
            break;
        case 'send':
            echo sendMessage();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

function fetchStatus() {
    global $waServerUrl;
    
    $ch = curl_init("$waServerUrl/status");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200 || !$response) {
        return json_encode([
            'success' => false,
            'message' => 'Cannot connect to WhatsApp server',
            'status' => 'error'
        ]);
    }
    
    return $response;
}

function fetchQR() {
    global $waServerUrl;
    
    $ch = curl_init("$waServerUrl/qr");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200 || !$response) {
        return json_encode([
            'success' => false,
            'message' => 'Cannot fetch QR code'
        ]);
    }
    
    return $response;
}

function sendMessage() {
    global $waServerUrl;
    
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (empty($phone) || empty($message)) {
        return json_encode([
            'success' => false,
            'message' => 'Phone and message are required'
        ]);
    }
    
    $data = json_encode([
        'phone' => $phone,
        'message' => $message
    ]);
    
    $ch = curl_init("$waServerUrl/send");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200 || !$response) {
        return json_encode([
            'success' => false,
            'message' => 'Failed to send message'
        ]);
    }
    
    return $response;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .card h2 {
            color: #333;
            margin-bottom: 16px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: auto;
        }
        
        .status-connected {
            background: #d4edda;
            color: #155724;
        }
        
        .status-disconnected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-qr {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-error {
            background: #f8d7da;
            color: #721c24;
        }
        
        #qrCode {
            text-align: center;
            padding: 20px;
        }
        
        #qrCode img {
            max-width: 100%;
            height: auto;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        label {
            display: block;
            margin-bottom: 6px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }
        
        input, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: inherit;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-top: 16px;
            font-size: 14px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #666;
            font-size: 14px;
        }
        
        .info-value {
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .instructions {
            background: #e3f2fd;
            padding: 16px;
            border-radius: 8px;
            margin-top: 16px;
        }
        
        .instructions h3 {
            color: #1976d2;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .instructions ol {
            margin-left: 20px;
            color: #555;
            line-height: 1.6;
            font-size: 13px;
        }
        
        .instructions li {
            margin-bottom: 6px;
        }
        
        small {
            display: block;
            color: #666;
            font-size: 12px;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📱 SPMB WhatsApp Gateway</h1>
            <p>Testing Panel - Scan QR & Send Messages</p>
        </div>
        
        <div class="grid">
            <!-- Status Card -->
            <div class="card">
                <h2>
                    🔌 Connection Status
                    <span id="statusBadge" class="status-badge status-disconnected">Checking...</span>
                </h2>
                <div id="statusInfo">
                    <div class="loading">
                        <div class="spinner"></div>
                        <p>Loading status...</p>
                    </div>
                </div>
            </div>
            
            <!-- QR Code Card -->
            <div class="card">
                <h2>📷 QR Code Scanner</h2>
                <div id="qrCode">
                    <div class="loading">
                        <div class="spinner"></div>
                        <p>Loading QR code...</p>
                    </div>
                </div>
                <button onclick="refreshQR()" style="margin-top: 16px;">🔄 Refresh QR Code</button>
                
                <div class="instructions">
                    <h3>📋 Cara Scan:</h3>
                    <ol>
                        <li>Buka <strong>WhatsApp</strong> di HP</li>
                        <li>Tap <strong>menu</strong> (⋮) di kanan atas</li>
                        <li>Pilih <strong>"Perangkat Tertaut"</strong></li>
                        <li>Tap <strong>"Tautkan Perangkat"</strong></li>
                        <li><strong>Scan QR code</strong> di atas</li>
                    </ol>
                </div>
            </div>
            
            <!-- Send Message Card -->
            <div class="card">
                <h2>💬 Send Test Message</h2>
                <form id="sendForm" onsubmit="sendMessage(event)">
                    <div class="form-group">
                        <label for="phone">📞 Phone Number</label>
                        <input type="text" id="phone" name="phone" placeholder="081234567890" required>
                        <small>Format: 08xxx, 628xxx, atau +628xxx</small>
                    </div>
                    <div class="form-group">
                        <label for="message">💬 Message</label>
                        <textarea id="message" name="message" placeholder="Ketik pesan Anda di sini..." required></textarea>
                        <small>Pesan akan dikirim via WhatsApp Gateway</small>
                    </div>
                    <button type="submit" id="sendBtn">📤 Send Message</button>
                </form>
                <div id="sendResult"></div>
            </div>
        </div>
    </div>
    
    <script>
        // Load status on page load
        loadStatus();
        loadQR();
        
        // Auto-refresh status every 5 seconds
        setInterval(loadStatus, 5000);
        
        async function loadStatus() {
            try {
                const response = await fetch('?action=status');
                const data = await response.json();
                
                const statusInfo = document.getElementById('statusInfo');
                const statusBadge = document.getElementById('statusBadge');
                
                if (!data.success && data.status === 'error') {
                    statusBadge.className = 'status-badge status-error';
                    statusBadge.textContent = 'Server Error';
                    statusInfo.innerHTML = `
                        <div class="alert alert-error">
                            ⚠️ ${data.message}<br>
                            <small>Pastikan WhatsApp server berjalan di port 3000</small>
                        </div>
                    `;
                    return;
                }
                
                let badgeClass = 'status-disconnected';
                let badgeText = 'Disconnected';
                
                if (data.status === 'connected') {
                    badgeClass = 'status-connected';
                    badgeText = '✓ Connected';
                } else if (data.status === 'qr') {
                    badgeClass = 'status-qr';
                    badgeText = '⏳ Waiting QR Scan';
                }
                
                statusBadge.className = `status-badge ${badgeClass}`;
                statusBadge.textContent = badgeText;
                
                statusInfo.innerHTML = `
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value">${data.status}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">QR Available:</span>
                        <span class="info-value">${data.qrAvailable ? 'Yes' : 'No'}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Reconnect Attempts:</span>
                        <span class="info-value">${data.reconnectAttempts || 0}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Last Update:</span>
                        <span class="info-value">${new Date(data.timestamp).toLocaleTimeString('id-ID')}</span>
                    </div>
                `;
                
                // Auto-refresh QR if status is 'qr'
                if (data.status === 'qr' && data.qrAvailable) {
                    loadQR();
                }
                
            } catch (error) {
                console.error('Failed to load status:', error);
                document.getElementById('statusBadge').className = 'status-badge status-error';
                document.getElementById('statusBadge').textContent = 'Error';
                document.getElementById('statusInfo').innerHTML = `
                    <div class="alert alert-error">
                        ⚠️ Failed to load status
                    </div>
                `;
            }
        }
        
        async function loadQR() {
            try {
                const response = await fetch('?action=qr');
                const data = await response.json();
                
                const qrCode = document.getElementById('qrCode');
                
                if (data.success && data.qr) {
                    qrCode.innerHTML = `
                        <img src="${data.qr}" alt="QR Code">
                        <p style="margin-top: 12px; color: #666; font-size: 14px;">
                            ${data.message}
                        </p>
                        <p style="margin-top: 8px; color: #999; font-size: 12px;">
                            QR code akan expired dalam 20 detik
                        </p>
                    `;
                } else {
                    qrCode.innerHTML = `
                        <div class="alert alert-info">
                            ℹ️ ${data.message || 'QR code not available'}
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Failed to load QR:', error);
                document.getElementById('qrCode').innerHTML = `
                    <div class="alert alert-error">
                        ⚠️ Failed to load QR code
                    </div>
                `;
            }
        }
        
        function refreshQR() {
            document.getElementById('qrCode').innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading QR code...</p>
                </div>
            `;
            loadQR();
        }
        
        async function sendMessage(event) {
            event.preventDefault();
            
            const form = document.getElementById('sendForm');
            const formData = new FormData(form);
            const sendBtn = document.getElementById('sendBtn');
            const sendResult = document.getElementById('sendResult');
            
            sendBtn.disabled = true;
            sendBtn.textContent = '⏳ Sending...';
            sendResult.innerHTML = '';
            
            try {
                const response = await fetch('?action=send', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    sendResult.innerHTML = `
                        <div class="alert alert-success">
                            ✓ Message sent successfully to ${data.to}
                            <br><small>Sent at: ${new Date(data.timestamp).toLocaleString('id-ID')}</small>
                        </div>
                    `;
                    form.reset();
                } else {
                    sendResult.innerHTML = `
                        <div class="alert alert-error">
                            ✗ ${data.message}
                        </div>
                    `;
                }
            } catch (error) {
                sendResult.innerHTML = `
                    <div class="alert alert-error">
                        ✗ Failed to send message: ${error.message}
                    </div>
                `;
            } finally {
                sendBtn.disabled = false;
                sendBtn.textContent = '📤 Send Message';
            }
        }
    </script>
</body>
</html>
