@extends('layouts.admin')

@section('title', 'Tambah Pengguna - Admin SPMB')

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
                    <i class="fas fa-user-plus me-2"></i>Tambah Pengguna Baru
                </h2>
            </div>

            <!-- Form Card -->
            <x-section-card title="Informasi Pengguna" icon="fas fa-user">
                <form id="user-create-form" action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-12">
                            <x-form-group label="Nama Lengkap" name="name" required="true">
                                <x-input type="text" name="name" value="{{ old('name') }}" 
                                       icon="fas fa-user" placeholder="Masukkan nama lengkap" required />
                            </x-form-group>
                        </div>

                        <!-- Email -->
                        <div class="col-md-12">
                            <x-form-group label="Email" name="email" required="true" help="Email akan digunakan untuk login">
                                <x-input type="email" name="email" value="{{ old('email') }}" 
                                       icon="fas fa-envelope" placeholder="contoh@email.com" required />
                            </x-form-group>
                        </div>

                        <!-- Password -->
                        <div class="col-md-6">
                            <x-form-group label="Password" name="password" required="true" help="Minimal 8 karakter">
                                <x-input type="password" name="password" id="password"
                                       icon="fas fa-lock" placeholder="Masukkan password" required />
                            </x-form-group>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6">
                            <x-form-group label="Konfirmasi Password" name="password_confirmation" required="true">
                                <x-input type="password" name="password_confirmation" id="password_confirmation"
                                       icon="fas fa-lock" placeholder="Ulangi password" required />
                            </x-form-group>
                        </div>

                        <!-- Role -->
                        <div class="col-md-6">
                            <x-form-group label="Role" name="role" required="true" help="Tentukan hak akses pengguna">
                                <x-select name="role" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                            @if($role === 'administrator')
                                                🛡️ Administrator - Akses penuh
                                            @else
                                                👔 Panitia - Akses terbatas
                                            @endif
                                        </option>
                                    @endforeach
                                </x-select>
                            </x-form-group>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <x-form-group label="Status" name="status" required="true">
                                <x-select name="status" required>
                                    <option value="">-- Pilih Status --</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ old('status', 'aktif') === $status ? 'selected' : '' }}>
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
                            Simpan Pengguna
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
    document.getElementById('user-create-form').addEventListener('submit', function() {
        var btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        
        // Re-enable after 10 seconds (safety)
        setTimeout(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan Pengguna';
        }, 10000);
    });

    // Password confirmation validation
    var password = document.getElementById('password');
    var passwordConfirmation = document.getElementById('password_confirmation');

    if (password && passwordConfirmation) {
        passwordConfirmation.addEventListener('input', function() {
            if (password.value !== passwordConfirmation.value) {
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
