@extends('layouts.admin')

@section('title', 'Tambah Pendaftar - SPMB')

@push('styles')
<style>
    .dashboard-content {
        animation: zoomFadeIn 0.35s ease-out;
    }

    @keyframes zoomFadeIn {
        from {
            opacity: 0;
            transform: scale(0.97);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>
@endpush

@section('content')
    @include('pendaftar._form-create')
@endsection
