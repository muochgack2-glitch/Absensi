<?php
// Test shell_exec availability
echo "<h2>PHP Shell Exec Test</h2>";

// Check if function exists
if (function_exists('shell_exec')) {
    echo "<p>✅ shell_exec function EXISTS</p>";
} else {
    echo "<p>❌ shell_exec function NOT FOUND</p>";
    exit;
}

// Check disabled functions
$disabled = ini_get('disable_functions');
echo "<p><strong>Disabled functions:</strong> " . ($disabled ? $disabled : "(none)") . "</p>";

// Test basic command
$whoami = shell_exec('whoami 2>&1');
echo "<p><strong>whoami:</strong> " . ($whoami ? htmlspecialchars($whoami) : "(empty)") . "</p>";

// Test PM2 command
$pm2Version = shell_exec('/usr/bin/pm2 -v 2>&1');
echo "<p><strong>PM2 version:</strong> " . ($pm2Version ? htmlspecialchars($pm2Version) : "(empty)") . "</p>";

// Test PM2 jlist
$pm2List = shell_exec('/usr/bin/pm2 jlist 2>&1');
echo "<p><strong>PM2 jlist:</strong> " . ($pm2List ? '<pre>' . htmlspecialchars(substr($pm2List, 0, 500)) . '</pre>' : "(empty)") . "</p>";

// PHP version and SAPI
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>PHP SAPI:</strong> " . php_sapi_name() . "</p>";

// Check for safe_mode (deprecated but sometimes still exists)
echo "<p><strong>Safe mode:</strong> " . (ini_get('safe_mode') ? 'ON' : 'OFF') . "</p>";

// Test with exec()
echo "<h3>Testing with exec():</h3>";
$output = [];
$returnVar = 0;
exec('/usr/bin/pm2 -v 2>&1', $output, $returnVar);
echo "<p><strong>exec() result:</strong> " . implode("\n", $output) . " (return code: {$returnVar})</p>";
