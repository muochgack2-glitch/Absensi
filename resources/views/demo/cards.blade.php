@extends('layouts.admin')

@section('title', 'Card Components Demo')

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
    
    .code-block {
        background: var(--gray-900);
        color: var(--gray-100);
        padding: 16px;
        border-radius: var(--radius-lg);
        font-family: 'Courier New', monospace;
        font-size: 13px;
        overflow-x: auto;
        margin-top: 16px;
    }
</style>
@endpush

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="mb-5">
        <h2 class="mb-2">Card Components Demo</h2>
        <p class="text-muted">Koleksi komponen card modern inspired by eRapor8</p>
    </div>

    <!-- 1. Base Card -->
    <div class="demo-section">
        <h3 class="demo-section-title">1. Base Card</h3>
        <p class="demo-section-description">Komponen card dasar yang fleksibel dengan berbagai variasi.</p>
        
        <div class="row g-4">
            <div class="col-md-4">
                <x-card>
                    <h5>Default Card</h5>
                    <p class="mb-0">Basic card with default styling.</p>
                </x-card>
            </div>
            <div class="col-md-4">
                <x-card hover="true" shadow="lg">
                    <h5>Hover Card</h5>
                    <p class="mb-0">Card with hover effect and large shadow.</p>
                </x-card>
            </div>
            <div class="col-md-4">
                <x-card border="true" borderColor="#3b82f6" padding="lg">
                    <h5>Border Card</h5>
                    <p class="mb-0">Card with colored left border.</p>
                </x-card>
            </div>
        </div>
    </div>

    <!-- 2. Stat Cards -->
    <div class="demo-section">
        <h3 class="demo-section-title">2. Stat Cards</h3>
        <p class="demo-section-description">Card untuk menampilkan statistik dengan icon dan trend indicator.</p>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <x-stat-card 
                    icon="fas fa-users" 
                    label="Total Users" 
                    value="1,234" 
                    color="blue"
                    description="All time"
                    trend="+12%"
                    :trendUp="true"
                />
            </div>
            <div class="col-md-6 col-lg-3">
                <x-stat-card 
                    icon="fas fa-shopping-cart" 
                    label="Total Orders" 
                    value="856" 
                    color="green"
                    description="This month"
                    trend="+8%"
                    :trendUp="true"
                />
            </div>
            <div class="col-md-6 col-lg-3">
                <x-stat-card 
                    icon="fas fa-clock" 
                    label="Pending" 
                    value="23" 
                    color="yellow"
                    description="Awaiting review"
                    trend="-5%"
                    :trendUp="false"
                />
            </div>
            <div class="col-md-6 col-lg-3">
                <x-stat-card 
                    icon="fas fa-exclamation-triangle" 
                    label="Issues" 
                    value="7" 
                    color="red"
                    description="Needs attention"
                />
            </div>
        </div>
    </div>

    <!-- 3. Info Cards -->
    <div class="demo-section">
        <h3 class="demo-section-title">3. Info Cards</h3>
        <p class="demo-section-description">Card untuk menampilkan informasi, peringatan, atau notifikasi.</p>
        
        <div class="row g-4">
            <div class="col-md-6">
                <x-info-card type="info" title="Information">
                    This is an informational message. You can use this to display helpful tips or general information.
                </x-info-card>
            </div>
            <div class="col-md-6">
                <x-info-card type="success" title="Success!" dismissible="true">
                    Operation completed successfully. Your changes have been saved.
                </x-info-card>
            </div>
            <div class="col-md-6">
                <x-info-card type="warning" title="Warning">
                    Please review this information carefully before proceeding.
                </x-info-card>
            </div>
            <div class="col-md-6">
                <x-info-card type="danger" title="Error">
                    An error occurred while processing your request. Please try again.
                </x-info-card>
            </div>
        </div>
    </div>

    <!-- 4. Section Cards -->
    <div class="demo-section">
        <h3 class="demo-section-title">4. Section Cards</h3>
        <p class="demo-section-description">Card dengan header section untuk mengorganisir konten.</p>
        
        <div class="row g-4">
            <div class="col-lg-6">
                <x-section-card title="Recent Activity" icon="fas fa-list">
                    <x-slot:actions>
                        <button class="btn btn-sm btn-outline-primary">View All</button>
                    </x-slot:actions>
                    
                    <ul class="list-unstyled mb-0">
                        <li class="py-2 border-bottom">User John Doe registered</li>
                        <li class="py-2 border-bottom">New order #1234 created</li>
                        <li class="py-2">Payment received for order #1233</li>
                    </ul>
                </x-section-card>
            </div>
            <div class="col-lg-6">
                <x-section-card title="Live Data" icon="fas fa-chart-line" badge="LIVE">
                    <div class="text-center py-4">
                        <h2 class="mb-0">Real-time Statistics</h2>
                        <p class="text-muted">Updates every 30 seconds</p>
                    </div>
                </x-section-card>
            </div>
        </div>
    </div>

    <!-- 5. Empty States -->
    <div class="demo-section">
        <h3 class="demo-section-title">5. Empty States</h3>
        <p class="demo-section-description">Komponen untuk menampilkan state kosong dengan berbagai ukuran.</p>
        
        <div class="row g-4">
            <div class="col-md-4">
                <x-card>
                    <x-empty-state 
                        icon="fas fa-inbox" 
                        message="No data" 
                        size="sm"
                    />
                </x-card>
            </div>
            <div class="col-md-4">
                <x-card>
                    <x-empty-state 
                        icon="fas fa-search" 
                        message="No results found"
                        description="Try adjusting your search"
                        size="md"
                    />
                </x-card>
            </div>
            <div class="col-md-4">
                <x-card>
                    <x-empty-state 
                        icon="fas fa-folder-open" 
                        message="No files"
                        description="Upload your first file"
                        size="md"
                    >
                        <x-slot:action>
                            <button class="btn btn-primary btn-sm">Upload File</button>
                        </x-slot:action>
                    </x-empty-state>
                </x-card>
            </div>
        </div>
    </div>

    <!-- 6. Action Cards -->
    <div class="demo-section">
        <h3 class="demo-section-title">6. Action Cards</h3>
        <p class="demo-section-description">Card interaktif dengan hover effect untuk navigasi atau aksi cepat.</p>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <x-action-card 
                    icon="fas fa-user-plus" 
                    title="Add New User" 
                    description="Register a new user account"
                    href="#"
                    color="blue"
                />
            </div>
            <div class="col-md-6 col-lg-4">
                <x-action-card 
                    icon="fas fa-file-export" 
                    title="Export Data" 
                    description="Download reports and analytics"
                    href="#"
                    color="green"
                />
            </div>
            <div class="col-md-6 col-lg-4">
                <x-action-card 
                    icon="fas fa-cog" 
                    title="Settings" 
                    description="Configure system preferences"
                    href="#"
                    color="purple"
                />
            </div>
            <div class="col-md-6 col-lg-4">
                <x-action-card 
                    icon="fas fa-bell" 
                    title="Notifications" 
                    description="Manage your notifications"
                    href="#"
                    color="orange"
                />
            </div>
            <div class="col-md-6 col-lg-4">
                <x-action-card 
                    icon="fas fa-trash" 
                    title="Delete Items" 
                    description="Remove unwanted data"
                    href="#"
                    color="red"
                />
            </div>
            <div class="col-md-6 col-lg-4">
                <x-action-card 
                    icon="fas fa-chart-bar" 
                    title="View Reports" 
                    description="Access detailed analytics"
                    href="#"
                    color="blue"
                />
            </div>
        </div>
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
