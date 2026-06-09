<?php
// Test PM2 accessibility from PHP
// Access: https://spmb.smkpgriblora.sch.id/test_pm2_from_php.php

header('Content-Type: text/plain; charset=utf-8');

echo "==============================================\n";
echo "  PM2 ACCESSIBILITY TEST FROM PHP\n";
echo "==============================================\n\n";

// Test 1: Check if shell_exec enabled
echo "1. Testing shell_exec():\n";
if (function_exists('shell_exec')) {
    echo "   ✅ shell_exec is ENABLED\n";
} else {
    echo "   ❌ shell_exec is DISABLED (check php.ini disable_functions)\n";
    exit(1);
}

// Test 2: Check disabled functions
echo "\n2. Checking disabled functions:\n";
$disabled = ini_get('disable_functions');
if (empty($disabled)) {
    echo "   ✅ No functions disabled\n";
} else {
    echo "   ⚠️  Disabled: {$disabled}\n";
    if (str_contains($disabled, 'shell_exec')) {
        echo "   ❌ shell_exec is DISABLED!\n";
        exit(1);
    }
}

// Test 3: Check current user
echo "\n3. Current PHP user:\n";
$user = shell_exec('whoami 2>&1');
echo "   User: {$user}";
$uid = shell_exec('id -u 2>&1');
echo "   UID: {$uid}";

// Test 4: Check which pm2
echo "\n4. Finding PM2 path:\n";
$pm2Path = shell_exec('which pm2 2>&1');
if (empty(trim($pm2Path))) {
    echo "   ❌ PM2 not found in PATH\n";
    echo "   Trying common paths...\n";
    
    $commonPaths = [
        '/usr/bin/pm2',
        '/usr/local/bin/pm2',
        '/root/.nvm/versions/node/v18.0.0/bin/pm2',
        '/root/.nvm/versions/node/v20.0.0/bin/pm2',
        '/home/www/.nvm/versions/node/v18.0.0/bin/pm2',
    ];
    
    $found = false;
    foreach ($commonPaths as $path) {
        if (file_exists($path)) {
            echo "   ✅ Found at: {$path}\n";
            $pm2Path = $path;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "   ❌ PM2 not found in common paths\n";
        echo "\n❌ SOLUTION: Install PM2 or add to PATH\n";
        exit(1);
    }
} else {
    echo "   ✅ Found at: {$pm2Path}";
}

// Test 5: Try pm2 list
echo "\n5. Testing 'pm2 list':\n";
$pm2Cmd = trim($pm2Path);
$output = shell_exec("{$pm2Cmd} list 2>&1");
if (empty($output)) {
    echo "   ❌ No output (permission issue?)\n";
} else {
    echo "   Output:\n";
    echo str_replace("\n", "\n   ", rtrim($output)) . "\n";
}

// Test 6: Try pm2 jlist (JSON format)
echo "\n6. Testing 'pm2 jlist':\n";
$output = shell_exec("{$pm2Cmd} jlist 2>&1");
if (empty($output)) {
    echo "   ❌ No output\n";
} else {
    $json = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "   ✅ Valid JSON response\n";
        echo "   Process count: " . count($json) . "\n";
        if (count($json) > 0) {
            foreach ($json as $proc) {
                $name = $proc['name'] ?? 'unknown';
                $status = $proc['pm2_env']['status'] ?? 'unknown';
                echo "   - {$name}: {$status}\n";
            }
        }
    } else {
        echo "   ❌ Invalid JSON: " . json_last_error_msg() . "\n";
        echo "   Raw output:\n";
        echo str_replace("\n", "\n   ", rtrim($output)) . "\n";
    }
}

// Test 7: Check wa-server process specifically
echo "\n7. Checking 'wa-server' process:\n";
$output = shell_exec("{$pm2Cmd} jlist 2>&1");
$json = json_decode($output, true);
if (is_array($json)) {
    $found = false;
    foreach ($json as $proc) {
        if (isset($proc['name']) && $proc['name'] === 'wa-server') {
            $found = true;
            $status = $proc['pm2_env']['status'] ?? 'unknown';
            $memory = $proc['monit']['memory'] ?? 0;
            $memoryMB = round($memory / 1024 / 1024, 2);
            $restarts = $proc['pm2_env']['restart_time'] ?? 0;
            
            echo "   ✅ Found wa-server\n";
            echo "   Status: {$status}\n";
            echo "   Memory: {$memoryMB} MB\n";
            echo "   Restarts: {$restarts}\n";
            break;
        }
    }
    if (!$found) {
        echo "   ⚠️  wa-server process not found\n";
    }
} else {
    echo "   ❌ Cannot parse PM2 list\n";
}

// Test 8: Check whatsapp-server directory
echo "\n8. Checking whatsapp-server directory:\n";
$baseDir = dirname(__FILE__);
$waServerDir = $baseDir . '/../whatsapp-server';
if (is_dir($waServerDir)) {
    echo "   ✅ Directory exists: {$waServerDir}\n";
    if (file_exists($waServerDir . '/server.js')) {
        echo "   ✅ server.js found\n";
    } else {
        echo "   ❌ server.js NOT found\n";
    }
} else {
    echo "   ❌ Directory NOT found: {$waServerDir}\n";
}

// Final recommendation
echo "\n==============================================\n";
echo "  RECOMMENDATION\n";
echo "==============================================\n";

if (!empty(trim($pm2Path)) && file_exists(trim($pm2Path))) {
    echo "\n✅ PM2 is accessible!\n";
    echo "\n📝 UPDATE WhatsAppController.php:\n";
    echo "   Replace all 'pm2' commands with:\n";
    echo "   '{$pm2Path}'\n";
    echo "\n   Example:\n";
    echo "   // Before:\n";
    echo "   shell_exec('pm2 jlist 2>&1');\n";
    echo "   \n";
    echo "   // After:\n";
    echo "   shell_exec('{$pm2Path} jlist 2>&1');\n";
} else {
    echo "\n❌ PM2 is NOT accessible\n";
    echo "\n📝 SOLUTIONS:\n";
    echo "   1. Install PM2 globally:\n";
    echo "      npm install -g pm2\n";
    echo "   \n";
    echo "   2. Add PM2 to PATH for web server user\n";
    echo "   \n";
    echo "   3. Use symlink:\n";
    echo "      ln -s /path/to/pm2 /usr/bin/pm2\n";
}

echo "\n==============================================\n";
echo "Done!\n";
