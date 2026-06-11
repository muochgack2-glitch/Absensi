<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DUPLICATE DETECTION ===\n\n";

// Test normalization
$service = new App\Services\ExternalBroadcastService();

echo "1. Test Phone Normalization:\n";
$testPhones = [
    '081234567890',
    '0812-3456-7890',
    '62812-3456-7890',
    '+62812-3456-7890',
    '812345678',
];

foreach ($testPhones as $phone) {
    $normalized = $service->normalizePhone($phone);
    echo "   {$phone} -> {$normalized}\n";
}

echo "\n2. Check SPMB Database:\n";
$pendaftars = DB::table('pendaftar')
    ->whereNotNull('no_hp_wali')
    ->orWhereNotNull('no_hp_ortu')
    ->orWhereNotNull('no_telepon')
    ->limit(5)
    ->get(['id_pendaftar', 'nama_lengkap', 'no_hp_wali', 'no_hp_ortu', 'no_telepon']);

foreach ($pendaftars as $p) {
    echo "   ID: {$p->id_pendaftar} | {$p->nama_lengkap}\n";
    if ($p->no_hp_wali) {
        $norm = $service->normalizePhone($p->no_hp_wali);
        echo "      Wali: {$p->no_hp_wali} -> {$norm}\n";
    }
    if ($p->no_hp_ortu) {
        $norm = $service->normalizePhone($p->no_hp_ortu);
        echo "      Ortu: {$p->no_hp_ortu} -> {$norm}\n";
    }
    if ($p->no_telepon) {
        $norm = $service->normalizePhone($p->no_telepon);
        echo "      Siswa: {$p->no_telepon} -> {$norm}\n";
    }
}

echo "\n3. Test Duplicate Detection:\n";
// Get first phone from database
$firstPendaftar = DB::table('pendaftar')
    ->where(function($query) {
        $query->whereNotNull('no_hp_wali')
              ->orWhereNotNull('no_hp_ortu')
              ->orWhereNotNull('no_telepon');
    })
    ->first();

if ($firstPendaftar) {
    $phone = $firstPendaftar->no_hp_wali ?? $firstPendaftar->no_hp_ortu ?? $firstPendaftar->no_telepon;
    echo "   Using phone from DB: {$phone}\n";
    
    $testRecipients = [
        [
            'name' => 'Test Duplikat',
            'phone' => $phone,
            'phone_normalized' => $service->normalizePhone($phone),
            'notes' => 'Should be duplicate'
        ],
        [
            'name' => 'Test Unik',
            'phone' => '089999888777',
            'phone_normalized' => $service->normalizePhone('089999888777'),
            'notes' => 'Should be unique'
        ]
    ];
    
    $result = $service->detectDuplicates($testRecipients);
    
    echo "   Debug: Checking detection logic...\n";
    echo "   Looking for: 62883139147095\n";
    
    // Manual check
    $allPendaftars = DB::table('pendaftar')->limit(10)->get();
    foreach ($allPendaftars as $p) {
        if ($p->no_telepon) {
            $norm = $service->normalizePhone($p->no_telepon);
            if ($norm === '62883139147095') {
                echo "   FOUND MATCH! ID: {$p->id_pendaftar}, Phone: {$p->no_telepon}, Normalized: {$norm}\n";
            }
        }
    }
    
    foreach ($result as $r) {
        $status = ($r['is_duplicate_spmb'] ?? false) ? '🔄 DUPLICATE' : '✓ UNIQUE';
        echo "   {$r['name']} ({$r['phone_normalized']}) -> {$status}\n";
        if ($r['is_duplicate_spmb'] ?? false) {
            echo "      Matched with pendaftar ID: {$r['matched_pendaftar_id']}\n";
        }
    }
} else {
    echo "   No pendaftar found with phone number\n";
}

echo "\n=== DONE ===\n";
