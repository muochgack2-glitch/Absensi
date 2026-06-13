<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JaringanController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PendaftarController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [LandingController::class, 'index'])->name('home');

// Telegram webhook (no auth, no CSRF)
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])->name('telegram.webhook');

// Public Registration routes (no auth required)
Route::get('/daftar', [RegistrationController::class, 'showForm'])->name('registration.form');
Route::post('/daftar', [RegistrationController::class, 'register'])->name('registration.submit');
Route::get('/daftar/bukti', [RegistrationController::class, 'showReceipt'])->name('registration.receipt');
Route::get('/daftar/cetak', [RegistrationController::class, 'printReceipt'])->name('registration.print');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Protected admin routes
Route::middleware('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Demo routes for UI components
    Route::get('/demo/modals', function () {
        return view('demo.modals');
    })->name('demo.modals');
    
    Route::get('/demo/modals-preview', function () {
        return view('demo.modals-preview');
    })->name('demo.modals-preview');
    
    Route::get('/dashboard', function () {
        $recentPendaftars = \App\Models\Pendaftar::with('logistik')
            ->latest('id_pendaftar')
            ->take(8)
            ->get();

        $perJaringanDashboard = \App\Models\Pendaftar::query()
            ->selectRaw("UPPER(TRIM(COALESCE(nama_jaringan, ''))) as nama_jaringan_normalized, COUNT(*) as total")
            ->groupByRaw("UPPER(TRIM(COALESCE(nama_jaringan, '')))")
            ->orderByDesc('total')
            ->take(8)
            ->get()
            ->map(function($item) {
                $item->nama_jaringan_normalized = $item->nama_jaringan_normalized ?: '(Langsung)';
                return $item;
            });

        return view('dashboard.index', compact('recentPendaftars', 'perJaringanDashboard'));
    })->name('dashboard');

    // Dashboard stats endpoint - accessible by all authenticated users
    Route::get('/dashboard/stats', [ReportController::class, 'stats'])->name('dashboard.stats');

    // Profile routes - accessible by all authenticated users
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfileController::class, 'index'])->name('index');
        Route::put('/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('update');
        Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password');
        Route::post('/update-theme', [\App\Http\Controllers\ProfileController::class, 'updateTheme'])->name('update-theme');
    });

    // Pendaftar routes - Only for Administrator and Panitia
    Route::middleware(['checkRole:administrator,panitia'])->group(function () {
        Route::resource('pendaftar', PendaftarController::class);
        Route::post('/pendaftar/bulk-delete', [PendaftarController::class, 'bulkDelete'])->name('pendaftar.bulk-delete');
        Route::get('/pendaftar-export/excel', [PendaftarController::class, 'exportExcel'])->name('pendaftar.export.excel');
        Route::get('/pendaftar-export/pdf', [PendaftarController::class, 'exportPdf'])->name('pendaftar.export.pdf');
        Route::get('/verifikasi-daftar-ulang', [PendaftarController::class, 'verificationIndex'])->name('pendaftar.verification-index');
        Route::get('/pendaftar/{id}/daftar-ulang-verification', [PendaftarController::class, 'showDaftarUlangVerification'])->name('pendaftar.daftar-ulang');
        Route::post('/pendaftar/{id}/process-daftar-ulang', [PendaftarController::class, 'processDaftarUlang'])->name('pendaftar.process-daftar-ulang');
        Route::post('/pendaftar/{id}/cancel-daftar-ulang', [PendaftarController::class, 'cancelDaftarUlang'])->name('pendaftar.cancel-daftar-ulang');

        // Print Templates
        Route::get('/pendaftar/{id}/print/registrasi', [PendaftarController::class, 'printRegistrasi'])->name('pendaftar.print.registrasi');
        Route::get('/pendaftar/{id}/print/formulir', [PendaftarController::class, 'printFormulir'])->name('pendaftar.print.formulir');
        Route::get('/pendaftar/{id}/print/ambil-barang', [PendaftarController::class, 'printAmbilBarang'])->name('pendaftar.print.ambil-barang');
    });

    // Soft Delete routes (Administrator only)
    Route::middleware(['checkRole:administrator'])->group(function () {
        Route::get('/pendaftar-trashed', [PendaftarController::class, 'trashed'])->name('pendaftar.trashed');
        Route::post('/pendaftar/{id}/restore', [PendaftarController::class, 'restore'])->name('pendaftar.restore');
    });
    // Reports & Exports - Only for Administrator and Panitia
    Route::middleware(['checkRole:administrator,panitia'])->group(function () {
        Route::get('/laporan', [ReportController::class, 'index'])->name('report.index');
        Route::get('/laporan/export/excel', [ReportController::class, 'exportExcel'])->name('report.export.excel');
        Route::get('/laporan/export/jaringan', [ReportController::class, 'exportJaringanExcel'])->name('report.export.jaringan');
        Route::get('/laporan/export/pdf', [ReportController::class, 'exportPdf'])->name('report.export.pdf');
        // Real‑time stats endpoint
        Route::get('/laporan/stats', [ReportController::class, 'stats'])->name('report.stats');
    });

    // Kelola Jaringan - Only for Administrator and Panitia
    Route::middleware(['checkRole:administrator,panitia'])->prefix('admin/jaringan')->name('jaringan.')->group(function () {
        // Stats endpoint
        Route::get('/stats', [JaringanController::class, 'stats'])->name('stats');
        
        // Full Mode
        Route::get('/merge', [JaringanController::class, 'merge'])->name('merge');
        Route::post('/preview', [JaringanController::class, 'preview'])->name('preview');
        Route::post('/process-merge', [JaringanController::class, 'processMerge'])->name('process-merge');
        
        // Selective Mode
        Route::get('/merge-selective', [JaringanController::class, 'mergeSelective'])->name('merge-selective');
        Route::post('/preview-selective', [JaringanController::class, 'previewSelective'])->name('preview-selective');
        Route::post('/process-merge-selective', [JaringanController::class, 'processMergeSelective'])->name('process-merge-selective');
        
        // History & Undo
        Route::get('/history', [JaringanController::class, 'history'])->name('history');
        Route::post('/undo/{id}', [JaringanController::class, 'undo'])->name('undo');
    });

    // Settings - Only for Administrator
    Route::middleware('ensureAdmin')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/tahun-ajaran', [SettingsController::class, 'updateTahunAjaran'])->name('settings.update-tahun-ajaran');
        Route::post('/settings/jurusan', [SettingsController::class, 'storeJurusan'])->name('settings.jurusan.store');
        Route::put('/settings/jurusan/{jurusan}', [SettingsController::class, 'updateJurusan'])->name('settings.jurusan.update');
        Route::delete('/settings/jurusan/{jurusan}', [SettingsController::class, 'destroyJurusan'])->name('settings.jurusan.destroy');

        // User Management - Only for Administrator
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::get('/create', [UserManagementController::class, 'create'])->name('create');
            Route::post('/', [UserManagementController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
            Route::get('/{user}/activity', [UserManagementController::class, 'activityLog'])->name('activity-log');
            Route::post('/{user}/reactivate', [UserManagementController::class, 'reactivate'])->name('reactivate');
        });
    });

    // WhatsApp Gateway - For Administrator and Admin WA
    Route::middleware(['checkRole:administrator,admin_wa'])->prefix('whatsapp')->name('whatsapp.')->group(function () {
        Route::get('/', [\App\Http\Controllers\WhatsAppController::class, 'index'])->name('index');
        Route::get('/status', [\App\Http\Controllers\WhatsAppController::class, 'status'])->name('status');
        Route::get('/health', [\App\Http\Controllers\WhatsAppController::class, 'health'])->name('health');
        Route::get('/qr', [\App\Http\Controllers\WhatsAppController::class, 'qrCode'])->name('qr');
        
        // Diagnostics & Auto-Fix
        Route::get('/diagnostics', [\App\Http\Controllers\WhatsAppController::class, 'diagnostics'])->name('diagnostics');
        Route::post('/auto-fix', [\App\Http\Controllers\WhatsAppController::class, 'autoFix'])->name('auto-fix')->middleware('throttle:3,60'); // Max 3x per hour
        Route::get('/error-logs', [\App\Http\Controllers\WhatsAppController::class, 'getErrorLogs'])->name('error-logs');
        
        // Send message
        Route::get('/send', [\App\Http\Controllers\WhatsAppController::class, 'sendPage'])->name('send');
        Route::post('/send', [\App\Http\Controllers\WhatsAppController::class, 'send'])->name('send.submit');
        Route::post('/send-template', [\App\Http\Controllers\WhatsAppController::class, 'sendWithTemplate'])->name('send.template');
        
        // Logs
        Route::get('/logs', [\App\Http\Controllers\WhatsAppController::class, 'logs'])->name('logs');
        
        // Templates
        Route::get('/templates', [\App\Http\Controllers\WhatsAppController::class, 'templates'])->name('templates');
        Route::get('/templates/create', [\App\Http\Controllers\WhatsAppController::class, 'createTemplate'])->name('templates.create');
        Route::post('/templates', [\App\Http\Controllers\WhatsAppController::class, 'storeTemplate'])->name('templates.store');
        Route::get('/templates/{id}/edit', [\App\Http\Controllers\WhatsAppController::class, 'editTemplate'])->name('templates.edit');
        Route::put('/templates/{id}', [\App\Http\Controllers\WhatsAppController::class, 'updateTemplate'])->name('templates.update');
        Route::delete('/templates/{id}', [\App\Http\Controllers\WhatsAppController::class, 'deleteTemplate'])->name('templates.delete');
        Route::get('/templates/{id}/preview', [\App\Http\Controllers\WhatsAppController::class, 'previewTemplate'])->name('templates.preview');
        
        // Settings
        Route::get('/settings', [\App\Http\Controllers\WhatsAppController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\WhatsAppController::class, 'updateSettings'])->name('settings.update');
        
        // Broadcast
        Route::get('/broadcast', [\App\Http\Controllers\WhatsAppController::class, 'broadcastPage'])->name('broadcast');
        Route::post('/broadcast', [\App\Http\Controllers\WhatsAppController::class, 'sendBroadcast'])->name('broadcast.send');
        
        // External Broadcast (Tasks 6.1-6.4)
        Route::get('/broadcast/external', [\App\Http\Controllers\WhatsAppController::class, 'externalBroadcastPage'])->name('broadcast.external');
        Route::post('/broadcast/external/parse', [\App\Http\Controllers\WhatsAppController::class, 'parseExternalRecipients'])->name('broadcast.external.parse');
        Route::post('/broadcast/external/send', [\App\Http\Controllers\WhatsAppController::class, 'sendExternalBroadcast'])->name('broadcast.external.send');
        Route::get('/external/{id}/messages', [\App\Http\Controllers\WhatsAppController::class, 'getExternalMessages'])->name('external.messages');
        
        // Phone List & Bulk Broadcast
        Route::get('/phone-list', [\App\Http\Controllers\WhatsAppController::class, 'phoneList'])->name('phone-list');
        Route::post('/broadcast/send-bulk', [\App\Http\Controllers\WhatsAppController::class, 'sendBulkBroadcast'])->name('broadcast.send-bulk');
        Route::get('/pendaftar/{id}/messages', [\App\Http\Controllers\WhatsAppController::class, 'getPendaftarMessages'])->name('pendaftar.messages');
        
        // Logout & Restart
        Route::post('/logout', [\App\Http\Controllers\WhatsAppController::class, 'logout'])->name('logout');
        Route::post('/restart', [\App\Http\Controllers\WhatsAppController::class, 'restart'])->name('restart')->middleware('throttle:2,60'); // Max 2x per hour
        Route::post('/reset', [\App\Http\Controllers\WhatsAppController::class, 'reset'])->name('reset')->middleware('throttle:2,60'); // Max 2x per hour
    });

    // Gateway Management - For Administrator and Admin WA
    Route::middleware(['checkRole:administrator,admin_wa'])->prefix('admin/gateway')->name('gateway.')->group(function () {
        Route::get('/', [\App\Http\Controllers\WhatsAppGatewayController::class, 'index'])->name('index');
        Route::get('/{gateway}/qr', [\App\Http\Controllers\WhatsAppGatewayController::class, 'getQRCode'])->name('qr');
        Route::post('/{gateway}/restart', [\App\Http\Controllers\WhatsAppGatewayController::class, 'restart'])->name('restart');
        Route::post('/{gateway}/logout', [\App\Http\Controllers\WhatsAppGatewayController::class, 'logout'])->name('logout');
        Route::post('/{gateway}/reset', [\App\Http\Controllers\WhatsAppGatewayController::class, 'resetGateway'])->name('reset');
        Route::get('/{gateway}/logs', [\App\Http\Controllers\WhatsAppGatewayController::class, 'getLogs'])->name('logs');
        Route::post('/toggle-failover', [\App\Http\Controllers\WhatsAppGatewayController::class, 'toggleFailover'])->name('toggle-failover');
    });
});

