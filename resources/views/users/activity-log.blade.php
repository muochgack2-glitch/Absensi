@extends('layouts.admin')

@section('title', 'Activity Log - Admin SPMB')

@section('content')
<div class="container-fluid py-4">
    <!-- Header with Back Button -->
    <div class="mb-4">
        <x-button 
            variant="secondary" 
            outline="true" 
            icon="fas fa-arrow-left" 
            href="{{ route('users.index') }}"
            size="sm"
        >
            Kembali
        </x-button>
        
        <h2 class="mt-3 mb-1">
            <i class="fas fa-history me-2 text-primary"></i>Activity Log
        </h2>
        <p class="text-muted">Riwayat aktivitas pengguna</p>
    </div>

    <!-- User Info Card -->
    <x-section-card title="Informasi Pengguna" icon="fas fa-user" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-circle bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Nama Lengkap</div>
                        <div class="fw-semibold">{{ $user->name }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-circle bg-info bg-opacity-10 text-info">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Email</div>
                        <div class="fw-semibold">{{ $user->email }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-circle bg-success bg-opacity-10 text-success">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Role</div>
                        <div class="fw-semibold">
                            @if($user->role === 'administrator')
                                <i class="fas fa-shield-alt text-danger me-1"></i>Administrator
                            @else
                                <i class="fas fa-user-tie text-primary me-1"></i>Panitia
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-section-card>

    <!-- Activity Logs Table -->
    <x-section-card title="Riwayat Aktivitas" icon="fas fa-list-ul">
        <x-slot:actions>
            <span class="badge bg-primary bg-opacity-10 text-primary">
                <i class="fas fa-database me-1"></i>{{ $logs->total() }} Log
            </span>
        </x-slot:actions>

        <x-table>
            <x-slot:header>
                <tr>
                    <th style="width: 180px;">
                        <i class="fas fa-clock me-1 text-muted"></i>Waktu
                    </th>
                    <th style="width: 140px;">
                        <i class="fas fa-bolt me-1 text-muted"></i>Action
                    </th>
                    <th style="width: 150px;">
                        <i class="fas fa-cube me-1 text-muted"></i>Model
                    </th>
                    <th>
                        <i class="fas fa-comment-dots me-1 text-muted"></i>Deskripsi
                    </th>
                    <th style="width: 140px;">
                        <i class="fas fa-network-wired me-1 text-muted"></i>IP Address
                    </th>
                </tr>
            </x-slot:header>
            
            @forelse($logs as $log)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt text-muted me-2"></i>
                            <div>
                                <div class="fw-medium">{{ $log->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($log->action === 'login')
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </span>
                        @elseif($log->action === 'logout')
                            <span class="badge bg-info bg-opacity-10 text-info">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </span>
                        @elseif($log->action === 'create')
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-plus-circle me-1"></i>Create
                            </span>
                        @elseif($log->action === 'update')
                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-edit me-1"></i>Update
                            </span>
                        @elseif($log->action === 'delete')
                            <span class="badge bg-danger bg-opacity-10 text-danger">
                                <i class="fas fa-trash-alt me-1"></i>Delete
                            </span>
                        @elseif($log->action === 'reactivate')
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="fas fa-redo me-1"></i>Reactivate
                            </span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                <i class="fas fa-circle me-1"></i>{{ ucfirst($log->action) }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($log->model)
                            <span class="badge bg-light border" style="color: var(--text-primary);">
                                <i class="fas fa-cube me-1"></i>{{ $log->model }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($log->description)
                            <div class="text-truncate" style="max-width: 400px;" title="{{ $log->description }}">
                                {{ $log->description }}
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($log->ip_address)
                            <span class="badge bg-light border font-monospace" style="color: var(--text-primary);">
                                <i class="fas fa-network-wired me-1"></i>{{ $log->ip_address }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-0">
                        <x-empty-state 
                            icon="fas fa-history" 
                            message="Tidak ada activity log"
                            description="Belum ada aktivitas yang tercatat untuk pengguna ini"
                        />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="mt-4">
                <x-pagination :paginator="$logs" />
            </div>
        @endif
    </x-section-card>
</div>

@push('styles')
<style>
.avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.font-monospace {
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
}
</style>
@endpush
@endsection
