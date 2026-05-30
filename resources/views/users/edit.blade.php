@extends('layouts.admin')

@section('title', 'Edit Pengguna - Admin SPMB')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <x-button variant="secondary" href="{{ route('users.index') }}" icon="fas fa-arrow-left" outline="true">
                    Kembali ke Daftar Pengguna
                </x-button>
                <h2 class="mt-3">
                    <i class="fas fa-edit me-2"></i>Edit Pengguna: {{ $user->name }}
                </h2>
            </div>

            <!-- User Info Card -->
            <x-info-card type="info" title="Informasi Akun" icon="fas fa-info-circle">
                <ul class="mb-0 small">
                    <li><i class="fas fa-clock me-2"></i><strong>Terakhir login:</strong> {{ $user->terakhir_login ? $user->terakhir_login->format('d M Y H:i') : 'Belum pernah login' }}</li>
                    <li><i class="fas fa-calendar me-2"></i><strong>Akun dibuat:</strong> {{ $user->created_at->format('d M Y H:i') }}</li>
                    @if($user->updated_at != $user->created_at)
                        <li><i class="fas fa-edit me-2"></i><strong>Terakhir diupdate:</strong> {{ $user->updated_at->format('d M Y H:i') }}</li>
                    @endif
                </ul>
            </x-info-card>

            <!-- Form Card -->
            <x-section-card title="Edit Informasi Pengguna" icon="fas fa-user-edit">
                <form id="user-edit-form" action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-12">
                            <x-form-group label="Nama Lengkap" name="name" required="true">
                                <x-input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                       icon="fas fa-user" placeholder="Masukkan nama lengkap" required />
                            </x-form-group>
                        </div>

                        <!-- Email -->
                        <div class="col-md-12">
                            <x-form-group label="Email" name="email" required="true">
                                <x-input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                       icon="fas fa-envelope" placeholder="contoh@email.com" required />
                            </x-form-group>
                        </div>

                        <!-- Password (Optional) -->
                        <div class="col-md-6">
                            <x-form-group label="Password Baru" name="password" help="Kosongkan jika tidak ingin diubah">
                                <x-input type="password" name="password" id="password"
                                       icon="fas fa-lock" placeholder="Masukkan password baru" />
                            </x-form-group>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6">
                            <x-form-group label="Konfirmasi Password Baru" name="password_confirmation">
                                <x-input type="password" name="password_confirmation" id="password_confirmation"
                                       icon="fas fa-lock" placeholder="Ulangi password baru" />
                            </x-form-group>
                        </div>

                        <!-- Role -->
                        <div class="col-md-6">
                            <x-form-group label="Role" name="role" required="true" 
                                        :help="auth()->user()->id === $user->id ? 'Tidak bisa mengubah role akun sendiri' : 'Tentukan hak akses pengguna'">
                                <x-select name="role" required :disabled="auth()->user()->id === $user->id">
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                            @if($role === 'administrator')
                                                🛡️ Administrator - Akses penuh
                                            @else
                                                👔 Panitia - Akses terbatas
                                            @endif
                                        </option>
                                    @endforeach
                                </x-select>
                                @if(auth()->user()->id === $user->id)
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                @endif
                            </x-form-group>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <x-form-group label="Status" name="status" required="true"
                                        :help="auth()->user()->id === $user->id ? 'Tidak bisa mengubah status akun sendiri' : ''">
                                <x-select name="status" required :disabled="auth()->user()->id === $user->id">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ old('status', $user->status) === $status ? 'selected' : '' }}>
                                            @if($status === 'aktif')
                                                ✅ Aktif
                                            @elseif($status === 'nonaktif')
                                                ❌ Non Aktif
                                            @else
                                                🚫 Suspended
                                            @endif
                                        </option>
                                    @endforeach
                                </x-select>
                                @if(auth()->user()->id === $user->id)
                                    <input type="hidden" name="status" value="{{ $user->status }}">
                                @endif
                            </x-form-group>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                        <x-button type="button" variant="secondary" href="{{ route('users.index') }}" 
                                icon="fas fa-times" outline="true">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary" icon="fas fa-save" id="btn-submit">
                            Simpan Perubahan
                        </x-button>
                    </div>
                </form>
            </x-section-card>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Loading state for submit button
    document.getElementById('user-edit-form').addEventListener('submit', function() {
        var btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        
        // Re-enable after 10 seconds (safety)
        setTimeout(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan Perubahan';
        }, 10000);
    });

    // Password confirmation validation (only if password is filled)
    var password = document.getElementById('password');
    var passwordConfirmation = document.getElementById('password_confirmation');

    if (password && passwordConfirmation) {
        passwordConfirmation.addEventListener('input', function() {
            if (password.value && password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('Password tidak cocok');
            } else {
                passwordConfirmation.setCustomValidity('');
            }
        });

        password.addEventListener('input', function() {
            if (passwordConfirmation.value && password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('Password tidak cocok');
            } else {
                passwordConfirmation.setCustomValidity('');
            }
        });
    }

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
