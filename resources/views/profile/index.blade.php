@extends('layouts.admin')

@section('title', 'Profile Saya - Admin SPMB')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4 mb-4">
            <x-section-card title="Informasi Akun" icon="fas fa-user-circle">
                <div class="text-center mb-4">
                    @php
                        $initials = collect(explode(' ', $user->name))
                            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                            ->take(2)
                            ->join('');
                        
                        $roleDisplay = match($user->role) {
                            'administrator' => ['label' => 'Administrator', 'icon' => 'fas fa-user-shield', 'color' => '#ef4444'],
                            'admin_wa' => ['label' => 'Admin WhatsApp', 'icon' => 'fab fa-whatsapp', 'color' => '#10b981'],
                            'panitia' => ['label' => 'Panitia', 'icon' => 'fas fa-user-tie', 'color' => '#3b82f6'],
                            default => ['label' => 'User', 'icon' => 'fas fa-user', 'color' => '#64748b'],
                        };
                    @endphp
                    
                    <div class="profile-avatar-large mb-3">
                        <span class="profile-initials">{{ $initials }}</span>
                        <span class="profile-status-dot"></span>
                    </div>
                    
                    <h5 class="mb-2">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    <div class="d-inline-flex align-items-center px-3 py-2 rounded-pill" 
                         style="background: {{ $roleDisplay['color'] }}20; color: {{ $roleDisplay['color'] }};">
                        <i class="{{ $roleDisplay['icon'] }} me-2"></i>
                        <strong>{{ $roleDisplay['label'] }}</strong>
                    </div>
                </div>

                <hr>

                <div class="profile-info-list">
                    <div class="profile-info-item">
                        <i class="fas fa-calendar-alt text-muted"></i>
                        <div>
                            <small class="text-muted">Bergabung</small>
                            <div>{{ $user->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="profile-info-item">
                        <i class="fas fa-clock text-muted"></i>
                        <div>
                            <small class="text-muted">Login Terakhir</small>
                            <div>{{ $user->terakhir_login ? $user->terakhir_login->format('d M Y H:i') : 'Belum pernah' }}</div>
                        </div>
                    </div>
                    
                    <div class="profile-info-item">
                        <i class="fas fa-shield-alt text-muted"></i>
                        <div>
                            <small class="text-muted">Status</small>
                            <div>
                                @if($user->status === 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Non Aktif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </x-section-card>
        </div>

        <!-- Edit Profile & Change Password -->
        <div class="col-lg-8">
            <!-- Edit Profile Form -->
            <x-section-card title="Edit Profile" icon="fas fa-edit" class="mb-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <x-form-group label="Nama Lengkap" name="name" required="true">
                                <x-input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                       icon="fas fa-user" placeholder="Masukkan nama lengkap" required />
                            </x-form-group>
                        </div>

                        <div class="col-md-12">
                            <x-form-group label="Email" name="email" required="true">
                                <x-input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                       icon="fas fa-envelope" placeholder="contoh@email.com" required />
                            </x-form-group>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                        <x-button type="submit" variant="primary" icon="fas fa-save">
                            Simpan Perubahan
                        </x-button>
                    </div>
                </form>
            </x-section-card>

            <!-- Change Password Form -->
            <x-section-card title="Ubah Password" icon="fas fa-lock">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <x-form-group label="Password Lama" name="current_password" required="true">
                                <x-input type="password" name="current_password" 
                                       icon="fas fa-lock" placeholder="Masukkan password lama" required />
                            </x-form-group>
                        </div>

                        <div class="col-md-6">
                            <x-form-group label="Password Baru" name="password" required="true" help="Minimal 8 karakter">
                                <x-input type="password" name="password" 
                                       icon="fas fa-key" placeholder="Masukkan password baru" required />
                            </x-form-group>
                        </div>

                        <div class="col-md-6">
                            <x-form-group label="Konfirmasi Password" name="password_confirmation" required="true">
                                <x-input type="password" name="password_confirmation" 
                                       icon="fas fa-key" placeholder="Ulangi password baru" required />
                            </x-form-group>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Tips Keamanan:</strong> Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol untuk password yang kuat.
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                        <x-button type="submit" variant="warning" icon="fas fa-key">
                            Ubah Password
                        </x-button>
                    </div>
                </form>
            </x-section-card>
        </div>
    </div>
</div>

<style>
    .profile-avatar-large {
        position: relative;
        display: inline-block;
    }

    .profile-initials {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 48px;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        border: 4px solid var(--bg-primary);
    }

    .profile-status-dot {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        background: #10b981;
        border: 4px solid var(--bg-primary);
        border-radius: 50%;
        animation: pulse-status 2s ease-in-out infinite;
    }

    .profile-info-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .profile-info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .profile-info-item i {
        width: 20px;
        font-size: 18px;
        margin-top: 2px;
    }

    .profile-info-item > div {
        flex: 1;
    }

    .profile-info-item small {
        display: block;
        font-size: 12px;
        margin-bottom: 2px;
    }
</style>
@endsection

@push('scripts')
<script>
    // Show success/error modal if session exists
    @if (session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            Modal.alert('{{ addslashes(session('success')) }}', 'Berhasil!', 'success');
        });
    @endif

    @if (session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            Modal.alert('{{ addslashes(session('error')) }}', 'Gagal!', 'danger');
        });
    @endif
</script>
@endpush
