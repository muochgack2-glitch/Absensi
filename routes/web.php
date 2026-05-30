<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PendaftarController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [LandingController::class, 'index'])->name('home');

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
            ->selectRaw("COALESCE(NULLIF(nama_jaringan, ''), '(Langsung)') as nama_jaringan, COUNT(*) as total")
            ->groupBy('nama_jaringan')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        return view('dashboard.index', compact('recentPendaftars', 'perJaringanDashboard'));
    })->name('dashboard');

    // Pendaftar routes
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

    // Reports & Exports
    Route::get('/laporan', [ReportController::class, 'index'])->name('report.index');
    Route::get('/laporan/export/excel', [ReportController::class, 'exportExcel'])->name('report.export.excel');
    Route::get('/laporan/export/jaringan', [ReportController::class, 'exportJaringanExcel'])->name('report.export.jaringan');
    Route::get('/laporan/export/pdf', [ReportController::class, 'exportPdf'])->name('report.export.pdf');
    // Real‑time stats endpoint
    Route::get('/laporan/stats', [ReportController::class, 'stats'])->name('report.stats');

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

        // WhatsApp Gateway - Only for Administrator
        Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
            Route::get('/', [\App\Http\Controllers\WhatsAppController::class, 'index'])->name('index');
            Route::get('/status', [\App\Http\Controllers\WhatsAppController::class, 'status'])->name('status');
            Route::get('/qr', [\App\Http\Controllers\WhatsAppController::class, 'qrCode'])->name('qr');
            
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
            
            // Logout
            Route::post('/logout', [\App\Http\Controllers\WhatsAppController::class, 'logout'])->name('logout');
        });
    });
});

