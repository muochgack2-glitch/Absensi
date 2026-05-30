@extends('layouts.admin')

@section('title', 'Notification Components Demo')

@push('styles')
<style>
    .demo-section {
        margin-bottom: 48px;
    }
    
    .demo-section-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }
    
    .demo-section-description {
        font-size: 14px;
        color: var(--text-secondary);
        margin-bottom: 24px;
    }
    
    .demo-button-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--space-3);
    }
</style>
@endpush

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="mb-5">
        <h2 class="mb-2">Notification Components Demo</h2>
        <p class="text-muted">Koleksi komponen notifikasi modern inspired by eRapor8</p>
    </div>

    <!-- 1. Alert Components -->
    <div class="demo-section">
        <h3 class="demo-section-title">1. Alert Components</h3>
        <p class="demo-section-description">Alert boxes untuk menampilkan pesan penting.</p>
        
        <x-section-card title="Alert Examples" icon="fas fa-bell">
            <x-alert type="info" title="Information">
                This is an informational alert. You can use this to display helpful tips or general information to users.
            </x-alert>
            
            <x-alert type="success" title="Success!">
                Your operation has been completed successfully. All changes have been saved.
            </x-alert>
            
            <x-alert type="warning" title="Warning">
                Please review this information carefully before proceeding with the action.
            </x-alert>
            
            <x-alert type="danger" title="Error">
                An error occurred while processing your request. Please try again or contact support.
            </x-alert>
        </x-section-card>
    </div>

    <!-- 2. Dismissible Alerts -->
    <div class="demo-section">
        <h3 class="demo-section-title">2. Dismissible Alerts</h3>
        <p class="demo-section-description">Alert yang bisa ditutup oleh user.</p>
        
        <x-section-card title="Dismissible Alert Examples" icon="fas fa-times-circle">
            <x-alert type="info" dismissible="true">
                This alert can be dismissed by clicking the close button.
            </x-alert>
            
            <x-alert type="success" title="Success!" dismissible="true">
                Operation completed successfully. You can close this message.
            </x-alert>
            
            <x-alert type="warning" dismissible="true">
                This is a dismissible warning message.
            </x-alert>
        </x-section-card>
    </div>

    <!-- 3. Toast Notifications -->
    <div class="demo-section">
        <h3 class="demo-section-title">3. Toast Notifications</h3>
        <p class="demo-section-description">Toast notifications yang muncul di pojok layar.</p>
        
        <x-section-card title="Toast Examples" icon="fas fa-comment-dots">
            <p class="mb-4">Click the buttons below to trigger toast notifications:</p>
            
            <div class="demo-button-grid">
                <x-button variant="success" icon="fas fa-check" onclick="Toast.success('Data berhasil disimpan!')">
                    Success Toast
                </x-button>
                
                <x-button variant="danger" icon="fas fa-times" onclick="Toast.error('Terjadi kesalahan!')">
                    Error Toast
                </x-button>
                
                <x-button variant="warning" icon="fas fa-exclamation-triangle" onclick="Toast.warning('Perhatian! Data akan dihapus.')">
                    Warning Toast
                </x-button>
                
                <x-button variant="info" icon="fas fa-info-circle" onclick="Toast.info('Informasi penting untuk Anda.')">
                    Info Toast
                </x-button>
                
                <x-button variant="primary" icon="fas fa-bell" onclick="Toast.success('Operasi berhasil!', 'Berhasil!', 3000)">
                    Custom Duration (3s)
                </x-button>
                
                <x-button variant="secondary" icon="fas fa-infinity" onclick="Toast.info('Toast ini tidak akan hilang otomatis.', 'Persistent', 0)">
                    No Auto-dismiss
                </x-button>
            </div>
            
            <x-info-card type="info" title="💡 How to Use" class="mt-4">
                <p class="mb-2">Use the global <code>Toast</code> object to show notifications:</p>
                <pre class="bg-dark text-light p-3 rounded"><code>// Success
Toast.success('Message', 'Title', duration);

// Error
Toast.error('Message', 'Title', duration);

// Warning
Toast.warning('Message', 'Title', duration);

// Info
Toast.info('Message', 'Title', duration);</code></pre>
            </x-info-card>
        </x-section-card>
    </div>

    <!-- 4. Notification Badges -->
    <div class="demo-section">
        <h3 class="demo-section-title">4. Notification Badges</h3>
        <p class="demo-section-description">Badge untuk menampilkan jumlah notifikasi.</p>
        
        <x-section-card title="Badge Examples" icon="fas fa-certificate">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="p-4 bg-secondary rounded text-center">
                        <div class="position-relative d-inline-block">
                            <i class="fas fa-bell fa-3x text-primary"></i>
                            <x-notification-badge count="5" class="position-absolute top-0 start-100 translate-middle" />
                        </div>
                        <p class="mt-3 mb-0 small">Badge with Count</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="p-4 bg-secondary rounded text-center">
                        <div class="position-relative d-inline-block">
                            <i class="fas fa-envelope fa-3x text-primary"></i>
                            <x-notification-badge count="150" max="99" class="position-absolute top-0 start-100 translate-middle" />
                        </div>
                        <p class="mt-3 mb-0 small">Max Count (99+)</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="p-4 bg-secondary rounded text-center">
                        <div class="position-relative d-inline-block">
                            <i class="fas fa-comment fa-3x text-primary"></i>
                            <x-notification-badge dot="true" class="position-absolute top-0 start-100 translate-middle" />
                        </div>
                        <p class="mt-3 mb-0 small">Dot Badge</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="p-4 bg-secondary rounded text-center">
                        <div class="position-relative d-inline-block">
                            <i class="fas fa-shopping-cart fa-3x text-primary"></i>
                            <x-notification-badge count="3" type="success" class="position-absolute top-0 start-100 translate-middle" />
                        </div>
                        <p class="mt-3 mb-0 small">Success Badge</p>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mt-2">
                <div class="col-md-3">
                    <div class="p-4 bg-secondary rounded text-center">
                        <div class="position-relative d-inline-block">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                            <x-notification-badge count="12" type="warning" class="position-absolute top-0 start-100 translate-middle" />
                        </div>
                        <p class="mt-3 mb-0 small">Warning Badge</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="p-4 bg-secondary rounded text-center">
                        <div class="position-relative d-inline-block">
                            <i class="fas fa-info-circle fa-3x text-info"></i>
                            <x-notification-badge count="8" type="info" class="position-absolute top-0 start-100 translate-middle" />
                        </div>
                        <p class="mt-3 mb-0 small">Info Badge</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="p-4 bg-secondary rounded text-center">
                        <div class="position-relative d-inline-block">
                            <i class="fas fa-star fa-3x text-warning"></i>
                            <x-notification-badge count="25" type="primary" class="position-absolute top-0 start-100 translate-middle" />
                        </div>
                        <p class="mt-3 mb-0 small">Primary Badge</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="p-4 bg-secondary rounded text-center">
                        <div class="position-relative d-inline-block">
                            <i class="fas fa-heart fa-3x text-danger"></i>
                            <x-notification-badge count="0" class="position-absolute top-0 start-100 translate-middle" />
                        </div>
                        <p class="mt-3 mb-0 small">Zero Count (Hidden)</p>
                    </div>
                </div>
            </div>
        </x-section-card>
    </div>

    <!-- 5. Real World Examples -->
    <div class="demo-section">
        <h3 class="demo-section-title">5. Real World Examples</h3>
        <p class="demo-section-description">Contoh penggunaan dalam aplikasi nyata.</p>
        
        <div class="row g-4">
            <div class="col-lg-6">
                <x-section-card title="Form Submission" icon="fas fa-paper-plane">
                    <form onsubmit="event.preventDefault(); Toast.success('Form berhasil dikirim!', 'Berhasil!');">
                        <x-form-group label="Name" name="name">
                            <x-input name="name" placeholder="Enter name" />
                        </x-form-group>
                        
                        <x-form-group label="Email" name="email">
                            <x-input name="email" type="email" placeholder="Enter email" />
                        </x-form-group>
                        
                        <x-button variant="primary" type="submit" icon="fas fa-paper-plane">
                            Submit Form
                        </x-button>
                    </form>
                </x-section-card>
            </div>
            
            <div class="col-lg-6">
                <x-section-card title="Action Buttons" icon="fas fa-bolt">
                    <div class="d-flex flex-column gap-3">
                        <x-button variant="success" icon="fas fa-save" block="true" 
                                  onclick="Toast.success('Data berhasil disimpan ke database.', 'Tersimpan!')">
                            Save Data
                        </x-button>
                        
                        <x-button variant="danger" icon="fas fa-trash" block="true"
                                  onclick="Toast.error('Data tidak dapat dihapus karena masih digunakan.', 'Gagal!')">
                            Delete Data
                        </x-button>
                        
                        <x-button variant="warning" icon="fas fa-download" block="true"
                                  onclick="Toast.warning('File sedang diunduh. Mohon tunggu...', 'Mengunduh')">
                            Download File
                        </x-button>
                        
                        <x-button variant="info" icon="fas fa-sync" block="true"
                                  onclick="Toast.info('Data sedang disinkronkan dengan server.', 'Sinkronisasi')">
                            Sync Data
                        </x-button>
                    </div>
                </x-section-card>
            </div>
            
            <div class="col-lg-12">
                <x-section-card title="Notification Center" icon="fas fa-bell">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="mb-0">Recent Notifications</h6>
                        <div class="position-relative">
                            <x-icon-button icon="fas fa-bell" variant="primary" />
                            <x-notification-badge count="3" class="position-absolute top-0 start-100 translate-middle" />
                        </div>
                    </div>
                    
                    <x-alert type="info" dismissible="true">
                        <strong>New message</strong> from John Doe: "Meeting rescheduled to 3 PM"
                    </x-alert>
                    
                    <x-alert type="success" dismissible="true">
                        <strong>Payment received</strong> - Invoice #12345 has been paid
                    </x-alert>
                    
                    <x-alert type="warning" dismissible="true">
                        <strong>Reminder</strong> - Your subscription expires in 7 days
                    </x-alert>
                </x-section-card>
            </div>
        </div>
    </div>

    <!-- 6. Laravel Flash Messages -->
    <div class="demo-section">
        <h3 class="demo-section-title">6. Laravel Flash Messages</h3>
        <p class="demo-section-description">Integrasi dengan Laravel session flash messages.</p>
        
        <x-section-card title="Flash Message Integration" icon="fas fa-code">
            <x-info-card type="info" title="💡 How It Works">
                <p class="mb-2">Toast container automatically displays Laravel flash messages:</p>
                <pre class="bg-dark text-light p-3 rounded mb-0"><code>// In Controller
return redirect()->back()->with('success', 'Data saved!');
return redirect()->back()->with('error', 'Failed to save!');
return redirect()->back()->with('warning', 'Please check!');
return redirect()->back()->with('info', 'Information here');</code></pre>
            </x-info-card>
            
            <div class="mt-4">
                <p class="mb-3"><strong>Test Flash Messages:</strong></p>
                <div class="demo-button-grid">
                    <form action="#" method="GET">
                        <x-button variant="success" type="button" 
                                  onclick="Toast.success('This simulates a Laravel flash message', 'Success!')">
                            Simulate Success
                        </x-button>
                    </form>
                    
                    <form action="#" method="GET">
                        <x-button variant="danger" type="button"
                                  onclick="Toast.error('This simulates a Laravel flash message', 'Error!')">
                            Simulate Error
                        </x-button>
                    </form>
                </div>
            </div>
        </x-section-card>
    </div>

    <!-- Documentation Link -->
    <div class="demo-section">
        <x-info-card type="info" title="📚 Documentation">
            <p class="mb-2">Untuk dokumentasi lengkap dan contoh kode, lihat file:</p>
            <code>resources/views/components/README.md</code>
        </x-info-card>
    </div>
</div>
@endsection
