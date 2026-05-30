@extends('layouts.admin')

@section('title', 'Activity Log - Admin SPMB')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('users.index') }}" class="text-decoration-none">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <h2 class="mt-3 mb-0">
                <i class="fas fa-history me-2"></i>Activity Log: {{ $user->name }}
            </h2>
            <p class="text-muted small">{{ $user->email }}</p>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>Action</th>
                        <th>Model</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <small class="text-muted">{{ $log->created_at->format('d M Y H:i:s') }}</small>
                            </td>
                            <td>
                                @if($log->action === 'login')
                                    <span class="badge bg-success">Login</span>
                                @elseif($log->action === 'logout')
                                    <span class="badge bg-info">Logout</span>
                                @elseif($log->action === 'create')
                                    <span class="badge bg-primary">Create</span>
                                @elseif($log->action === 'update')
                                    <span class="badge bg-warning">Update</span>
                                @elseif($log->action === 'delete' || $log->action === 'reactivate')
                                    <span class="badge bg-danger">{{ ucfirst($log->action) }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($log->model)
                                    <small>{{ $log->model }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                @if($log->description)
                                    <small>{{ $log->description }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                @if($log->ip_address)
                                    <small class="text-muted">{{ $log->ip_address }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox me-2"></i>Tidak ada activity log
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="card-footer">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
