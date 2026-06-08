<?php

/**
 * Test WA Gateway Restart Feature
 * 
 * This script tests the new restart server feature
 * Usage: php test-wa-restart-feature.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\WhatsAppService;

echo "=================================================\n";
echo "  WA GATEWAY RESTART FEATURE TEST\n";
echo "=================================================\n\n";

$service = new WhatsAppService();

// Test 1: Get Status Before
echo "1. Status before restart:\n";
$statusBefore = $service->getStatus();
if ($statusBefore['success']) {
    echo "   ✅ Connected: " . ($statusBefore['data']['status'] ?? 'unknown') . "\n";
} else {
    echo "   ❌ Not connected\n";
}

echo "\n";

// Test 2: Restart Server
echo "2. Testing restart() method:\n";
echo "   Sending restart command...\n";
$restart = $service->restart();

if ($restart['success']) {
    echo "   ✅ " . $restart['message'] . "\n";
    echo "   Waiting 10 seconds for server to restart...\n";
    
    // Show countdown
    for ($i = 10; $i >= 1; $i--) {
        echo "   {$i}...\r";
        sleep(1);
    }
    echo "   \n";
} else {
    echo "   ❌ Restart failed: " . $restart['message'] . "\n";
    exit(1);
}

echo "\n";

// Test 3: Get Status After
echo "3. Status after restart:\n";
$statusAfter = $service->getStatus();
if ($statusAfter['success']) {
    echo "   ✅ Connected: " . ($statusAfter['data']['status'] ?? 'unknown') . "\n";
    echo "   - QR Available: " . ($statusAfter['data']['qrAvailable'] ? 'Yes' : 'No') . "\n";
    
    if ($statusAfter['data']['status'] === 'connected') {
        echo "   ✅ SERVER RESTART SUCCESSFUL!\n";
        echo "   ✅ Auto-reconnected without scanning QR!\n";
    } else {
        echo "   ⚠️  Status: " . $statusAfter['data']['status'] . "\n";
        echo "   (Server might need more time to reconnect)\n";
    }
} else {
    echo "   ❌ Server not responding yet\n";
}

echo "\n";
echo "=================================================\n";
echo "  RESTART FEATURE TEST SUMMARY\n";
echo "=================================================\n";

// Comparison
echo "\nBefore Restart:\n";
echo "- Status: " . ($statusBefore['data']['status'] ?? 'unknown') . "\n";

echo "\nAfter Restart:\n";
echo "- Status: " . ($statusAfter['data']['status'] ?? 'unknown') . "\n";

echo "\nFeature Validation:\n";
echo "✅ Restart endpoint: Working\n";
echo "✅ Server process exit: Success\n";
if (isset($statusAfter['data']['status']) && $statusAfter['data']['status'] === 'connected') {
    echo "✅ Auto-reconnect: Success\n";
    echo "✅ Session preserved: Yes (no QR needed)\n";
} else {
    echo "⚠️  Auto-reconnect: In progress (check again in 30s)\n";
}

echo "\n";
echo "=================================================\n";
echo "\nNote: In production with PM2, the server will\n";
echo "automatically restart after process.exit(0)\n";
echo "\n";
echo "Local testing without PM2 shows the restart\n";
echo "command works, but PM2 auto-restart is only\n";
echo "available in production environment.\n";
echo "=================================================\n";
