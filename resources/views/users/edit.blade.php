@extends('layouts.admin')

@section('title', 'Edit Pengguna - Admin SPMB')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <a href="{{ route('users.index') }}" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Pengguna
                </a>
                <h2 class="mt-3">
                    <i class="fas fa-edit me-2"></i>Edit Pengguna: {{ $user->name }}
                </h2>
            </div>

            <!-- Alert Messages -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> Terjadi kesalahan:
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Form Card -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password (Optional) -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru <span class="text-muted">(Kosongkan jika tidak ingin diubah)</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password">
                            <small class="form-text text-muted">Minimal 8 karakter</small>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation">
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" name="role" required {{ auth()->user()->id === $user->id ? 'disabled' : '' }}>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                        @if($role === 'administrator')
                                            Administrator - Akses penuh ke semua fitur
                                        @else
                                            Panitia - Akses terbatas (tanpa Settings)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if(auth()->user()->id === $user->id)
                                <small class="form-text text-muted">Tidak bisa mengubah role akun sendiri</small>
                                <input type="hidden" name="role" value="{{ $user->role }}">
                            @endif
                            @error('role')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required {{ auth()->user()->id === $user->id ? 'disabled' : '' }}>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ old('status', $user->status) === $status ? 'selected' : '' }}>
                                        @if($status === 'aktif')
                                            Aktif
                                        @elseif($status === 'nonaktif')
                                            Non Aktif
                                        @else
                                            Suspended
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if(auth()->user()->id === $user->id)
                                <small class="form-text text-muted">Tidak bisa mengubah status akun sendiri</small>
                                <input type="hidden" name="status" value="{{ $user->status }}">
                            @endif
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info -->
                        <div class="alert alert-info">
                            <strong>Informasi:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li>Terakhir login: {{ $user->terakhir_login ? $user->terakhir_login->format('d M Y H:i') : 'Belum pernah login' }}</li>
                                <li>Akun dibuat: {{ $user->created_at->format('d M Y H:i') }}</li>
                            </ul>
                        </div>

                        <!-- Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
