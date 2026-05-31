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
            <x-button variant="primary" href="{{ route('users.create') }}" icon="fas fa-plus">
                Tambah Pengguna
            </x-button>
        </div>
    </div>

    <!-- Filter Card -->
    <x-section-card title="Filter Pencarian" icon="fas fa-filter">
        <form method="GET" action="{{ route('users.index') }}" class="row g-3">
            <div class="col-md-4">
                <x-form-group label="Cari Pengguna" name="search">
                    <x-input type="text" name="search" placeholder="Cari nama atau email..." 
                           value="{{ request('search') }}" icon="fas fa-search" />
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Role" name="role">
                    <x-select name="role">
                        <option value="">- Semua Role -</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </x-select>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Status" name="status">
                    <x-select name="status">
                        <option value="">- Semua Status -</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </x-select>
                </x-form-group>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <x-button type="submit" variant="primary" icon="fas fa-search" block="true">
                    Cari
                </x-button>
            </div>
        </form>
    </x-section-card>

    <!-- Users Table -->
    <x-section-card title="Daftar Pengguna" icon="fas fa-list">
        <x-table striped="true" hover="true">
            <x-slot:header>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Terakhir Login</th>
                    <th>Dibuat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </x-slot:header>

            @forelse($users as $user)
                <tr>
                    <td class="fw-500">
                        <i class="fas fa-user-circle me-2 text-muted"></i>{{ $user->name }}
                    </td>
                    <td>
                        <i class="fas fa-envelope me-2 text-muted"></i>{{ $user->email }}
                    </td>
                    <td>
                        @if($user->role === 'administrator')
                            <span class="badge bg-danger">
                                <i class="fas fa-user-shield me-1"></i>Administrator
                            </span>
                        @elseif($user->role === 'admin_wa')
                            <span class="badge bg-success">
                                <i class="fab fa-whatsapp me-1"></i>Admin WA
                            </span>
                        @else
                            <span class="badge bg-info">
                                <i class="fas fa-user-tie me-1"></i>Panitia
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($user->status === 'aktif')
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>Aktif
                            </span>
                        @elseif($user->status === 'nonaktif')
                            <span class="badge bg-secondary">
                                <i class="fas fa-times-circle me-1"></i>Non Aktif
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="fas fa-ban me-1"></i>Suspended
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($user->terakhir_login)
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>{{ $user->terakhir_login->format('d M Y H:i') }}
                            </small>
                        @else
                            <small class="text-muted">-</small>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>{{ $user->created_at->format('d M Y') }}
                        </small>
                    </td>
                    <td>
                        <x-table-actions align="center">
                            <x-icon-button icon="fas fa-edit" variant="primary" size="sm" 
                                         href="{{ route('users.edit', $user) }}" tooltip="Edit" />
                            <x-icon-button icon="fas fa-history" variant="info" size="sm" 
                                         href="{{ route('users.activity-log', $user) }}" tooltip="Activity Log" />
                            
                            @if(auth()->user()->id !== $user->id)
                                @if($user->status === 'nonaktif')
                                    <form id="reactivate-form-{{ $user->id }}" action="{{ route('users.reactivate', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-success" title="Aktifkan"
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
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Deaktifkan"
                                            onclick="Modal.confirm('Deaktifkan pengguna <strong>{{ $user->name }}</strong>?<br><small class=\'text-muted\'>Pengguna tidak akan bisa login setelah dinonaktifkan.</small>', function() {
                                                document.getElementById('delete-form-{{ $user->id }}').submit();
                                            }, { type: 'danger', title: 'Deaktifkan Pengguna', confirmText: 'Ya, Deaktifkan', cancelText: 'Batal' })">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            @else
                                <x-icon-button icon="fas fa-lock" variant="secondary" size="sm" 
                                             disabled="true" tooltip="Tidak bisa edit akun sendiri" />
                            @endif
                        </x-table-actions>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <x-empty-state icon="fas fa-users" message="Tidak ada pengguna" 
                                     description="Belum ada pengguna yang terdaftar" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="mt-3">
                <x-pagination :paginator="$users" />
            </div>
        @endif
    </x-section-card>
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
