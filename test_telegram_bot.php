#!/usr/bin/env php
<?php
/**
 * Telegram Bot Test Script
 * 
 * Quick test to verify your Telegram bot credentials and connectivity.
 * Run: php test_telegram_bot.php
 */

// Load environment variables
require __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get credentials from .env
$botToken = $_ENV['TELEGRAM_BOT_TOKEN'] ?? null;
$chatId = $_ENV['TELEGRAM_CHAT_ID'] ?? null;

echo "\n";
echo "================================================\n";
echo "  TELEGRAM BOT CONFIGURATION TEST\n";
echo "================================================\n\n";

// Check if credentials exist
if (!$botToken || !$chatId) {
    echo "❌ ERROR: Missing Telegram credentials!\n\n";
    echo "Please add these to your .env file:\n";
    echo "  TELEGRAM_BOT_TOKEN=your_bot_token_here\n";
    echo "  TELEGRAM_CHAT_ID=your_chat_id_here\n\n";
    exit(1);
}

// Display current config
echo "📋 Current Configuration:\n";
echo "  Bot Token: " . substr($botToken, 0, 15) . "..." . substr($botToken, -5) . "\n";
echo "  Chat ID: $chatId\n\n";

// Test 1: Check bot info
echo "🔍 Test 1: Checking bot information...\n";
$url = "https://api.telegram.org/bot{$botToken}/getMe";
$response = @file_get_contents($url);

if ($response === false) {
    echo "❌ FAILED: Cannot connect to Telegram API\n";
    echo "   Check your internet connection and bot token.\n\n";
    exit(1);
}

$data = json_decode($response, true);

if (!$data['ok']) {
    echo "❌ FAILED: Invalid bot token\n";
    echo "   Error: " . ($data['description'] ?? 'Unknown error') . "\n\n";
    exit(1);
}

echo "✅ PASSED: Bot is valid\n";
echo "   Bot name: " . $data['result']['first_name'] . "\n";
echo "   Username: @" . $data['result']['username'] . "\n\n";

// Test 2: Send test message
echo "📨 Test 2: Sending test message...\n";
$message = "🤖 *Test Message*\n\n" .
           "This is a test from your SPMB WhatsApp Gateway Bot.\n\n" .
           "✅ If you see this message, your bot is configured correctly!\n\n" .
           "⏰ Time: " . date('d M Y H:i:s');

$postData = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'Markdown',
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($postData),
    ],
];

$context = stream_context_create($options);
$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "❌ FAILED: Cannot send message\n";
    echo "   Check your internet connection.\n\n";
    exit(1);
}

$data = json_decode($response, true);

if (!$data['ok']) {
    echo "❌ FAILED: Cannot send message to chat\n";
    echo "   Error: " . ($data['description'] ?? 'Unknown error') . "\n\n";
    
    if (strpos($data['description'] ?? '', 'chat not found') !== false) {
        echo "💡 Tip: Make sure your bot is a member of the group/chat.\n";
        echo "   Chat ID should be negative for groups (e.g., -1001234567890)\n\n";
    }
    
    exit(1);
}

echo "✅ PASSED: Message sent successfully\n";
echo "   Message ID: " . $data['result']['message_id'] . "\n";
echo "   Check your Telegram group for the test message.\n\n";

// Test 3: Check webhook status
echo "🔗 Test 3: Checking webhook configuration...\n";
$url = "https://api.telegram.org/bot{$botToken}/getWebhookInfo";
$response = @file_get_contents($url);

if ($response === false) {
    echo "⚠️  WARNING: Cannot check webhook status\n\n";
} else {
    $data = json_decode($response, true);
    
    if ($data['ok']) {
        $webhookUrl = $data['result']['url'] ?? '';
        $pendingCount = $data['result']['pending_update_count'] ?? 0;
        
        if (empty($webhookUrl)) {
            echo "⚠️  WARNING: Webhook not set\n";
            echo "   Inline buttons won't work until webhook is configured.\n";
            echo "   Run this command to set webhook:\n\n";
            echo "   curl -X POST \"https://api.telegram.org/bot{$botToken}/setWebhook\" \\\n";
            echo "     -d \"url=https://yourdomain.com/telegram/webhook\"\n\n";
        } else {
            echo "✅ PASSED: Webhook is configured\n";
            echo "   Webhook URL: $webhookUrl\n";
            
            if ($pendingCount > 0) {
                echo "   ⚠️  Pending updates: $pendingCount\n";
                echo "   (These will be processed automatically)\n";
            }
            
            $lastError = $data['result']['last_error_message'] ?? null;
            if ($lastError) {
                echo "   ⚠️  Last error: $lastError\n";
                echo "   Check if your server is accessible from internet.\n";
            }
            echo "\n";
        }
    }
}

// Test 4: Send message with inline buttons
echo "🔘 Test 4: Sending message with inline buttons...\n";

$keyboard = [
    [
        ['text' => '✅ Test Button 1', 'callback_data' => 'test_1'],
        ['text' => '❌ Test Button 2', 'callback_data' => 'test_2'],
    ],
    [
        ['text' => '📊 View Dashboard', 'url' => 'https://example.com'],
    ],
];

$postData = [
    'chat_id' => $chatId,
    'text' => "🔘 *Button Test*\n\nThis message has inline buttons.\n\n" .
              "⚠️ Callback buttons (Test Button 1 & 2) only work if webhook is set.\n" .
              "✅ URL button (Dashboard) works without webhook.",
    'parse_mode' => 'Markdown',
    'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($postData),
    ],
];

$context = stream_context_create($options);
$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "❌ FAILED: Cannot send message with buttons\n\n";
} else {
    $data = json_decode($response, true);
    
    if ($data['ok']) {
        echo "✅ PASSED: Message with buttons sent\n";
        echo "   Check your Telegram group and try clicking the buttons.\n\n";
    } else {
        echo "❌ FAILED: " . ($data['description'] ?? 'Unknown error') . "\n\n";
    }
}

// Summary
echo "================================================\n";
echo "  TEST SUMMARY\n";
echo "================================================\n\n";
echo "✅ All basic tests passed!\n\n";
echo "Next steps:\n";
echo "1. Check your Telegram group for test messages\n";
echo "2. Try clicking the buttons (if webhook is set)\n";
echo "3. Run: php artisan wa:monitor\n";
echo "4. Check setup guide: TELEGRAM_SETUP_GUIDE.md\n\n";

exit(0);
