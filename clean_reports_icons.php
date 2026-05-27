<?php

$files = [
    __DIR__ . '/resources/views/reports/index.blade.php',
    __DIR__ . '/resources/views/reports/pdf.blade.php',
];

$replacements = [
    'â³' => '⏳',
    '🖨️' => '🖨️',
    '←' => '←',
];

foreach ($files as $file) {
    $text = file_get_contents($file);
    if ($text === false) {
        continue;
    }

    $original = $text;
    foreach ($replacements as $from => $to) {
        $text = str_replace($from, $to, $text);
    }

    if ($text !== $original) {
        file_put_contents($file, $text);
        echo 'Updated ' . basename($file) . PHP_EOL;
    }
}
