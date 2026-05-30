@extends('layouts.admin')

@section('title', 'Manajemen Pengguna - Admin SPMB')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">
                <i class="fas fa-users me-2"></i>Manajemen Pengguna
            </h2>
            <p class="text-muted small">Kelola akun pengguna administrator dan panitia</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Pengguna
            </a>
        </div>
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

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">- Semua Role -</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">- Semua Status -</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search me-1"></i>Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Terakhir Login</th>
                        <th>Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="fw-500">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'administrator')
                                    <span class="badge bg-danger">Administrator</span>
                                @else
                                    <span class="badge bg-info">Panitia</span>
                                @endif
                            </td>
                            <td>
                                @if($user->status === 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($user->status === 'nonaktif')
                                    <span class="badge bg-secondary">Non Aktif</span>
                                @else
                                    <span class="badge bg-danger">Suspended</span>
                                @endif
                            </td>
                            <td>
                                @if($user->terakhir_login)
                                    <small class="text-muted">{{ $user->terakhir_login->format('d M Y H:i') }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-secondary" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('users.activity-log', $user) }}" class="btn btn-outline-info" 
                                       title="Activity Log">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @if(auth()->user()->id !== $user->id)
                                        @if($user->status === 'nonaktif')
                                            <form id="reactivate-form-{{ $user->id }}" action="{{ route('users.reactivate', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                            </form>
                                            <button type="button" class="btn btn-outline-success" title="Aktifkan"
                                                    onclick="Modal.confirm('Aktifkan pengguna <strong>{{ $user->name }}</strong>?', function() {
                                                        document.getElementById('reactivate-form-{{ $user->id }}').submit();
                                                    }, { type: 'success', title: 'Aktifkan Pengguna', confirmText: 'Ya, Aktifkan', cancelText: 'Batal' })">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @else
                                            <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button" class="btn btn-outline-danger" title="Deaktifkan"
                                                    onclick="Modal.confirm('Deaktifkan pengguna <strong>{{ $user->name }}</strong>?<br><small class=\'text-muted\'>Pengguna tidak akan bisa login setelah dinonaktifkan.</small>', function() {
                                                        document.getElementById('delete-form-{{ $user->id }}').submit();
                                                    }, { type: 'danger', title: 'Deaktifkan Pengguna', confirmText: 'Ya, Deaktifkan', cancelText: 'Batal' })">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    @else
                                        <button type="button" class="btn btn-outline-secondary disabled" 
                                                title="Tidak bisa edit akun sendiri">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox me-2"></i>Tidak ada pengguna
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="card-footer">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
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
