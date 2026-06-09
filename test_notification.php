<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Admin;
use App\Notifications\WhatsAppStatusChanged;
use Illuminate\Support\Facades\Http;

echo "=== TESTING TELEGRAM NOTIFICATION ===\n\n";

// 1. Check config
echo "1. Checking Telegram Config:\n";
$token = config('services.telegram.bot_token');
$chatId = config('services.telegram.chat_id');

echo "   Bot Token: " . ($token ? substr($token, 0, 10) . '...' : '❌ NOT SET') . "\n";
echo "   Chat ID: " . ($chatId ? $chatId : '❌ NOT SET') . "\n\n";

if (!$token || !$chatId) {
    echo "❌ Telegram not configured properly!\n";
    exit(1);
}

// 2. Test direct API call
echo "2. Testing Direct Telegram API Call:\n";
try {
    $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
        'chat_id' => $chatId,
        'text' => "🧪 *Direct API Test*\n\nThis is a direct API call test from PHP script.\n\nTime: " . date('Y-m-d H:i:s'),
        'parse_mode' => 'Markdown',
    ]);
    
    if ($response->successful()) {
        echo "   ✅ Direct API call SUCCESS!\n";
        echo "   Response: " . json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
    } else {
        echo "   ❌ Direct API call FAILED!\n";
        echo "   Status: " . $response->status() . "\n";
        echo "   Response: " . json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 3. Test with Admin notification
echo "3. Testing Admin Notification:\n";
$admin = Admin::first();

if (!$admin) {
    echo "   ❌ No admin found in database!\n";
    exit(1);
}

echo "   Admin: {$admin->name} ({$admin->email})\n";

try {
    $notification = new WhatsAppStatusChanged('connected', 'disconnected');
    $admin->notify($notification);
    
    echo "   ✅ Notification method called!\n\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n\n";
}

echo "=== TEST COMPLETE ===\n";
echo "📱 Check Telegram group: {$chatId}\n";
