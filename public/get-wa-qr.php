<?php
// Simple script to fetch WhatsApp QR code and display as image
// Access: https://spmb.smkpgriblora.sch.id/get-wa-qr.php

header('Content-Type: text/html; charset=utf-8');

$waServerUrl = 'http://localhost:3000/qr';

// Fetch QR code from WhatsApp server
$ch = curl_init($waServerUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    die('Error: Cannot connect to WhatsApp server. Make sure server is running on port 3000.');
}

$data = json_decode($response, true);

if (!isset($data['success']) || !$data['success']) {
    die('Error: ' . ($data['message'] ?? 'QR code not available'));
}

$qrDataUrl = $data['qr'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp QR Code - SPMB</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .qr-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        img {
            max-width: 100%;
            height: auto;
            border: 3px solid #ddd;
            border-radius: 10px;
        }
        .instructions {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            text-align: left;
            margin-top: 20px;
        }
        .instructions h3 {
            color: #1976d2;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .instructions ol {
            margin-left: 20px;
            color: #555;
            line-height: 1.8;
        }
        .instructions li {
            margin-bottom: 8px;
        }
        .refresh-btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.2s;
        }
        .refresh-btn:hover {
            transform: translateY(-2px);
        }
        .status {
            display: inline-block;
            padding: 8px 16px;
            background: #4caf50;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <span class="status">🟢 Server Online</span>
        <h1>📱 WhatsApp QR Code</h1>
        <p>Scan QR code ini dengan WhatsApp Anda</p>
        
        <div class="qr-container">
            <img src="<?php echo htmlspecialchars($qrDataUrl); ?>" alt="WhatsApp QR Code">
        </div>
        
        <div class="instructions">
            <h3>📋 Cara Scan:</h3>
            <ol>
                <li>Buka <strong>WhatsApp</strong> di HP Anda</li>
                <li>Tap <strong>menu</strong> (3 titik di kanan atas)</li>
                <li>Pilih <strong>"Perangkat Tertaut"</strong></li>
                <li>Tap <strong>"Tautkan Perangkat"</strong></li>
                <li><strong>Scan QR code</strong> di atas</li>
            </ol>
        </div>
        
        <a href="?" class="refresh-btn">🔄 Refresh QR Code</a>
        
        <p style="margin-top: 20px; font-size: 12px; color: #999;">
            QR code akan expired dalam 20 detik. Refresh jika sudah expired.
        </p>
    </div>
    
    <script>
        // Auto refresh every 15 seconds
        setTimeout(function() {
            location.reload();
        }, 15000);
    </script>
</body>
</html>
