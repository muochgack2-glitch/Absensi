@extends('layouts.admin')

@section('title', 'Verifikasi Daftar Ulang - SPMB (Sistem Penerimaan Murid Baru)')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
        body { font-family: 'Inter', sans-serif !important; }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .info-box {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .info-box label {
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            opacity: 0.9;
            display: block;
            margin-bottom: 5px;
        }
        .info-box .value {
            font-size: 20px;
            font-weight: 700;
        }
        .size-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            margin: 20px 0;
        }
        .size-btn {
            padding: 20px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100px;
            position: relative;
        }
        .size-btn > div:first-of-type {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 4px;
            color: #1f2937;
        }
        .size-btn small {
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
        }
        .size-btn:hover {
            border-color: var(--primary);
            background: #f0f4ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .size-btn.selected {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 6px 20px rgba(var(--primary-rgb), 0.3);
        }
        .size-btn.selected > div:first-of-type {
            color: white;
        }
        .size-btn.selected small {
            color: rgba(255, 255, 255, 0.9);
        }
        .size-btn.selected::before {
            content: '✓';
            position: absolute;
            top: 8px;
            right: 8px;
            width: 24px;
            height: 24px;
            background: white;
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }
        .rollback-card {
            border: 1px solid rgba(239, 68, 68, 0.2);
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.98), rgba(254, 242, 242, 0.95));
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 10px 24px rgba(239, 68, 68, 0.1);
        }
        .rollback-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #b91c1c;
            margin-bottom: 8px;
        }
        .rollback-desc {
            margin-bottom: 14px;
            color: #7f1d1d;
            font-size: 14px;
        }
        .btn-rollback {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 600;
            transition: all 0.25s ease;
        }
        .btn-rollback:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(220, 38, 38, 0.28);
            color: #fff;
        }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Verifikasi Daftar Ulang</h2>
                    <p class="text-muted">Proses verifikasi dan pilih ukuran kaos</p>
                </div>
                <a href="{{ route('pendaftar.verification-index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-circle-check"></i> {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <!-- Data Pendaftar -->
                    <div class="card p-4 mb-4">
                        <h5 class="mb-4">
                            <i class="fas fa-user-circle"></i> Data Pendaftar
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <label>No. Registrasi</label>
                                    <div class="value">{{ $pendaftar->no_registrasi }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <label>Status Daftar Ulang</label>
                                    <div class="value">
                                        @if ($logistik->status_bayar === 'Belum')
                                            <span style="font-size: 16px;">🔴 Belum Daftar Ulang</span>
                                        @else
                                            <span style="font-size: 16px;">✅ Pendaftaran Selesai</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Nama:</strong> {{ $pendaftar->nama_lengkap }}</p>
                                <p><strong>NISN:</strong> {{ $pendaftar->nisn }}</p>
                                <p><strong>Jurusan:</strong> {{ $pendaftar->jurusan }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Asal Sekolah:</strong> {{ $pendaftar->asal_sekolah }}</p>
                                <p><strong>Alamat:</strong> {{ substr($pendaftar->alamat, 0, 50) }}...</p>
                                <p><strong>Gelombang:</strong> {{ $pendaftar->gelombang }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pilih Ukuran Kaos -->
                    @if ($logistik->status_bayar === 'Belum')
                        <div class="card p-4">
                            <h5 class="mb-4">
                                <i class="fas fa-shirt"></i> Pilih Ukuran Kaos
                            </h5>

                            <form id="formPayment" method="POST" action="{{ route('pendaftar.process-daftar-ulang', $pendaftar->id_pendaftar) }}">
                                @csrf

                                <p class="text-muted mb-3">Sebelum menyelesaikan daftar ulang, pilih ukuran kaos yang diinginkan:</p>

                                <div class="size-selector">
                                    <div>
                                        <input type="radio" id="size_s" name="ukuran_kaos" value="S" required style="display: none;">
                                        <label for="size_s" class="size-btn">
                                            <div>S</div>
                                            <small>Kecil</small>
                                        </label>
                                    </div>
                                    <div>
                                        <input type="radio" id="size_m" name="ukuran_kaos" value="M" required style="display: none;">
                                        <label for="size_m" class="size-btn">
                                            <div>M</div>
                                            <small>Sedang</small>
                                        </label>
                                    </div>
                                    <div>
                                        <input type="radio" id="size_l" name="ukuran_kaos" value="L" required style="display: none;">
                                        <label for="size_l" class="size-btn">
                                            <div>L</div>
                                            <small>Besar</small>
                                        </label>
                                    </div>
                                    <div>
                                        <input type="radio" id="size_xl" name="ukuran_kaos" value="XL" required style="display: none;">
                                        <label for="size_xl" class="size-btn">
                                            <div>XL</div>
                                            <small>Sangat Besar</small>
                                        </label>
                                    </div>
                                    <div>
                                        <input type="radio" id="size_xxl" name="ukuran_kaos" value="XXL" required style="display: none;">
                                        <label for="size_xxl" class="size-btn">
                                            <div>XXL</div>
                                            <small>Extra Besar</small>
                                        </label>
                                    </div>
                                    <div>
                                        <input type="radio" id="size_jumbo" name="ukuran_kaos" value="JUMBO" required style="display: none;">
                                        <label for="size_jumbo" class="size-btn">
                                            <div>JUMBO</div>
                                            <small>Jumbo</small>
                                        </label>
                                    </div>
                                </div>

                                <input type="hidden" name="selected_size" id="selected_size">

                                <div class="d-flex gap-2 mt-4 flex-wrap">
                                    <button type="button" id="btnVerifyPayment" class="btn btn-success btn-lg" disabled>
                                        <i class="fas fa-check-circle"></i> <span class="btn-text">Verifikasi Daftar Ulang</span>
                                    </button>
                                    <a href="{{ route('pendaftar.print.ambil-barang', $pendaftar->id_pendaftar) }}" target="_blank" class="btn btn-warning btn-lg">
                                        <i class="fas fa-print"></i> Cetak Bukti Ambil Barang
                                    </a>
                                    <a href="{{ route('pendaftar.verification-index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i> Pendaftaran sudah selesai. <strong>Kaos dipesankan</strong>.
                        </div>

                        <div class="rollback-card mb-3">
                            <div class="rollback-title">
                                <i class="fas fa-triangle-exclamation"></i>
                                Rollback Verifikasi Daftar Ulang
                            </div>
                            <p class="rollback-desc">
                                Aksi ini akan mengembalikan status menjadi <strong>Belum Daftar Ulang</strong> dan mereset ukuran kaos/logistik.
                            </p>
                            <form id="rollbackPaymentForm" method="POST" action="{{ route('pendaftar.cancel-daftar-ulang', $pendaftar->id_pendaftar) }}">
                                @csrf
                                <button type="submit" class="btn-rollback" id="btnRollbackPayment">
                                    <i class="fas fa-rotate-left"></i> <span class="btn-text">Ya, Kembalikan ke Belum Daftar Ulang</span>
                                </button>
                            </form>
                        </div>

                        <a href="{{ route('pendaftar.print.ambil-barang', $pendaftar->id_pendaftar) }}" target="_blank" class="btn btn-warning">
                            <i class="fas fa-print"></i> Cetak Bukti Ambil Barang
                        </a>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="card p-4">
                        <h5 class="mb-3">
                            <i class="fas fa-list-check"></i> Checklist Proses
                        </h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check-circle" style="color: {{ $logistik->status_bayar === 'Lunas' ? '#27ae60' : '#ccc' }};"></i>
                                Verifikasi Daftar Ulang
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle" style="color: {{ $logistik->status_kain === 'Sudah' ? '#27ae60' : '#ccc' }};"></i>
                                Kain Disiapkan
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle" style="color: {{ $logistik->status_kaos === 'Proses' || $logistik->status_kaos === 'Sudah' ? '#27ae60' : '#ccc' }};"></i>
                                Kaos Dipesankan
                            </li>
                        </ul>
                        <div class="mt-4 pt-3 border-top">
                            <h6>Ukuran Kaos Dipilih:</h6>
                            @if ($logistik->ukuran_kaos)
                                <p style="font-size: 24px; font-weight: 700; color: var(--primary);">{{ $logistik->ukuran_kaos }}</p>
                            @else
                                <p class="text-muted">Belum dipilih</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.all.min.js"></script>
<script>
        // Size selector UI
        const sizeInputs = document.querySelectorAll('input[name="ukuran_kaos"]');
        const btnVerify = document.getElementById('btnVerifyPayment');
        const formPayment = document.getElementById('formPayment');
        const sizeLabels = document.querySelectorAll('.size-btn');

        sizeLabels.forEach((label, index) => {
            label.addEventListener('click', (e) => {
                e.preventDefault();
                sizeInputs[index].checked = true;

                // Update UI
                sizeLabels.forEach(l => l.classList.remove('selected'));
                label.classList.add('selected');

                // Enable button
                btnVerify.disabled = false;
            });
        });

        // Form submission
        btnVerify.addEventListener('click', () => {
            const selected = document.querySelector('input[name="ukuran_kaos"]:checked');
            if (!selected) return;
            const selectedSize = selected.value;

            Swal.fire({
                title: 'Konfirmasi Daftar Ulang',
                html: `<p>Verifikasi daftar ulang dan ukuran kaos <strong>${selectedSize}</strong>?</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Verifikasi',
                cancelButtonText: 'Batal',
                confirmButtonColor: 'var(--theme-primary)'
            }).then((result) => {
                if (result.isConfirmed) {
                    btnVerify.disabled = true;
                    btnVerify.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    formPayment.submit();
                }
            });
        });

        // Rollback confirmation (modern)
        const rollbackForm = document.getElementById('rollbackPaymentForm');
        const btnRollback = document.getElementById('btnRollbackPayment');
        if (rollbackForm) {
            console.log("Rollback form ditemukan!");
            rollbackForm.addEventListener('submit', (e) => {
                e.preventDefault();

                Swal.fire({
                    title: 'Batalkan Verifikasi?',
                    html: '<p class="mb-1">Status daftar ulang akan kembali ke <strong>Belum Daftar Ulang</strong>.</p><small class="text-muted">Ukuran kaos dan progres logistik akan direset.</small>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Rollback',
                    cancelButtonText: 'Tetap Pendaftaran Selesai',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (btnRollback) {
                            btnRollback.disabled = true;
                            btnRollback.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                        }
                        rollbackForm.submit();
                    }
                });
            });
        }
    </script>
@endpush
