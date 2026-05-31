<?php
/**
 * Test WhatsApp Send Message
 * 
 * Script untuk test langsung kirim pesan ke WhatsApp server
 * Usage: php test-wa-send.php
 */

// Configuration
$serverUrl = 'http://localhost:3000';
$testPhone = '6281234567890'; // GANTI dengan nomor HP test Anda
$testMessage = 'Test pesan dari SPMB - ' . date('Y-m-d H:i:s');

echo "========================================\n";
echo "WhatsApp Send Message Test\n";
echo "========================================\n\n";

// Step 1: Check server status
echo "Step 1: Checking server status...\n";
$statusUrl = "$serverUrl/status";
$statusResponse = file_get_contents($statusUrl);
$status = json_decode($statusResponse, true);

echo "Status Response:\n";
echo json_encode($status, JSON_PRETTY_PRINT) . "\n\n";

if (!isset($status['status']) || $status['status'] !== 'connected') {
    echo "❌ ERROR: WhatsApp not connected!\n";
    echo "Status: " . ($status['status'] ?? 'unknown') . "\n";
    echo "\nPlease:\n";
    echo "1. Check if PM2 is running: pm2 status\n";
    echo "2. Check PM2 logs: pm2 logs spmb-wa-gateway\n";
    echo "3. Scan QR code if status is 'qr'\n";
    exit(1);
}

echo "✓ WhatsApp is connected\n\n";

// Step 2: Send test message
echo "Step 2: Sending test message...\n";
echo "To: $testPhone\n";
echo "Message: $testMessage\n\n";

$sendUrl = "$serverUrl/send";
$postData = json_encode([
    'phone' => $testPhone,
    'message' => $testMessage,
]);

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => $postData,
    ],
];

$context = stream_context_create($options);
$sendResponse = file_get_contents($sendUrl, false, $context);
$result = json_decode($sendResponse, true);

echo "Send Response:\n";
echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

// Step 3: Analyze result
echo "Step 3: Analyzing result...\n";

if (isset($result['success']) && $result['success'] === true) {
    echo "✓ Server returned success=true\n";
    echo "✓ Message sent to WhatsApp server\n\n";
    
    echo "========================================\n";
    echo "✅ TEST PASSED!\n";
    echo "========================================\n\n";
    
    echo "Next steps:\n";
    echo "1. Check your WhatsApp on phone number: $testPhone\n";
    echo "2. You should receive the test message\n";
    echo "3. If message not received, check:\n";
    echo "   - Phone number is correct and has WhatsApp\n";
    echo "   - Phone number format (should be 62xxx)\n";
    echo "   - WhatsApp server logs: pm2 logs spmb-wa-gateway\n";
    
} else {
    echo "❌ Server returned success=false\n";
    echo "Error: " . ($result['message'] ?? 'Unknown error') . "\n\n";
    
    echo "========================================\n";
    echo "❌ TEST FAILED!\n";
    echo "========================================\n\n";
    
    echo "Possible issues:\n";
    echo "1. WhatsApp not connected (check status)\n";
    echo "2. Phone number format invalid\n";
    echo "3. Rate limiting (too many messages)\n";
    echo "4. WhatsApp server error\n\n";
    
    echo "Check PM2 logs:\n";
    echo "pm2 logs spmb-wa-gateway --lines 50\n";
}

echo "\n";
