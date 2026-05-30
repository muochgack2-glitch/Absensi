@extends('layouts.admin')

@section('title', 'Preview Modal System')

@push('styles')
<style>
    .preview-section {
        margin-bottom: 40px;
    }
    
    .preview-title {
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .preview-description {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 20px;
        line-height: 1.6;
    }
    
    .button-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    
    .demo-button {
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .demo-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    .demo-button.btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }
    
    .demo-button.btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    
    .demo-button.btn-info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }
    
    .demo-button.btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .code-block {
        background: #1e293b;
        color: #e2e8f0;
        padding: 16px;
        border-radius: 10px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        overflow-x: auto;
        margin-top: 12px;
    }
    
    .code-block code {
        color: #e2e8f0;
    }
    
    .code-keyword {
        color: #c084fc;
    }
    
    .code-string {
        color: #34d399;
    }
    
    .code-function {
        color: #60a5fa;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="admin-page-title">🎭 Preview Modal System</h1>
        <p class="text-muted mb-0">Lihat semua jenis modal yang tersedia dan cara penggunaannya</p>
    </div>
</div>

<!-- Modal Confirm -->
<x-section-card title="Modal Confirm" icon="fas fa-question-circle">
    <div class="preview-section">
        <p class="preview-description">
            Modal konfirmasi digunakan untuk meminta persetujuan user sebelum melakukan aksi penting seperti hapus data, submit form, atau perubahan yang tidak bisa di-undo.
        </p>
        
        <div class="button-grid">
            <button class="demo-button btn-warning" onclick="demoConfirmWarning()">
                <i class="fas fa-exclamation-triangle"></i>
                Warning Confirm
            </button>
            
            <button class="demo-button btn-danger" onclick="demoConfirmDanger()">
                <i class="fas fa-exclamation-circle"></i>
                Danger Confirm
            </button>
            
            <button class="demo-button btn-info" onclick="demoConfirmInfo()">
                <i class="fas fa-info-circle"></i>
                Info Confirm
            </button>
            
            <button class="demo-button btn-success" onclick="demoConfirmSuccess()">
                <i class="fas fa-check-circle"></i>
                Success Confirm
            </button>
        </div>
        
        <div class="code-block">
            <code>
<span class="code-keyword">Modal</span>.<span class="code-function">confirm</span>(<br>
&nbsp;&nbsp;<span class="code-string">'Apakah Anda yakin ingin menghapus data ini?'</span>,<br>
&nbsp;&nbsp;<span class="code-keyword">function</span>() {<br>
&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-comment">// Callback ketika user klik "Ya"</span><br>
&nbsp;&nbsp;&nbsp;&nbsp;console.log(<span class="code-string">'User confirmed!'</span>);<br>
&nbsp;&nbsp;},<br>
&nbsp;&nbsp;{<br>
&nbsp;&nbsp;&nbsp;&nbsp;title: <span class="code-string">'Konfirmasi Hapus'</span>,<br>
&nbsp;&nbsp;&nbsp;&nbsp;confirmText: <span class="code-string">'Ya, Hapus'</span>,<br>
&nbsp;&nbsp;&nbsp;&nbsp;cancelText: <span class="code-string">'Batal'</span>,<br>
&nbsp;&nbsp;&nbsp;&nbsp;type: <span class="code-string">'danger'</span> <span class="code-comment">// warning, danger, info, success</span><br>
&nbsp;&nbsp;}<br>
);
            </code>
        </div>
    </div>
</x-section-card>

<!-- Modal Alert -->
<x-section-card title="Modal Alert" icon="fas fa-bell">
    <div class="preview-section">
        <p class="preview-description">
            Modal alert digunakan untuk menampilkan informasi, notifikasi, atau pesan kepada user. Hanya ada 1 tombol "OK" untuk menutup modal.
        </p>
        
        <div class="button-grid">
            <button class="demo-button btn-warning" onclick="demoAlertWarning()">
                <i class="fas fa-exclamation-triangle"></i>
                Warning Alert
            </button>
            
            <button class="demo-button btn-danger" onclick="demoAlertDanger()">
                <i class="fas fa-times-circle"></i>
                Error Alert
            </button>
            
            <button class="demo-button btn-info" onclick="demoAlertInfo()">
                <i class="fas fa-info-circle"></i>
                Info Alert
            </button>
            
            <button class="demo-button btn-success" onclick="demoAlertSuccess()">
                <i class="fas fa-check-circle"></i>
                Success Alert
            </button>
        </div>
        
        <div class="code-block">
            <code>
<span class="code-keyword">Modal</span>.<span class="code-function">alert</span>(<br>
&nbsp;&nbsp;<span class="code-string">'Data berhasil disimpan!'</span>,<br>
&nbsp;&nbsp;<span class="code-string">'Berhasil'</span>, <span class="code-comment">// title</span><br>
&nbsp;&nbsp;<span class="code-string">'success'</span> <span class="code-comment">// type: warning, danger, info, success</span><br>
);
            </code>
        </div>
    </div>
</x-section-card>

<!-- Modal dengan HTML -->
<x-section-card title="Modal dengan HTML Content" icon="fas fa-code">
    <div class="preview-section">
        <p class="preview-description">
            Modal juga mendukung HTML content untuk menampilkan informasi yang lebih kompleks seperti list, tabel, atau formatting khusus.
        </p>
        
        <div class="button-grid">
            <button class="demo-button btn-info" onclick="demoHtmlContent()">
                <i class="fas fa-list"></i>
                Modal dengan List
            </button>
            
            <button class="demo-button btn-warning" onclick="demoHtmlTable()">
                <i class="fas fa-table"></i>
                Modal dengan Table
            </button>
        </div>
        
        <div class="code-block">
            <code>
<span class="code-keyword">Modal</span>.<span class="code-function">confirm</span>(<br>
&nbsp;&nbsp;<span class="code-string">'&lt;ul&gt;&lt;li&gt;Item 1&lt;/li&gt;&lt;li&gt;Item 2&lt;/li&gt;&lt;/ul&gt;'</span>,<br>
&nbsp;&nbsp;<span class="code-keyword">function</span>() { },<br>
&nbsp;&nbsp;{ title: <span class="code-string">'HTML Content'</span>, type: <span class="code-string">'info'</span> }<br>
);
            </code>
        </div>
    </div>
</x-section-card>

<!-- Real World Examples -->
<x-section-card title="Contoh Penggunaan Real World" icon="fas fa-rocket">
    <div class="preview-section">
        <p class="preview-description">
            Berikut adalah contoh penggunaan modal dalam skenario nyata di aplikasi SPMB.
        </p>
        
        <div class="button-grid">
            <button class="demo-button btn-danger" onclick="demoDeletePendaftar()">
                <i class="fas fa-trash"></i>
                Hapus Pendaftar
            </button>
            
            <button class="demo-button btn-success" onclick="demoVerifikasiDaftarUlang()">
                <i class="fas fa-check-double"></i>
                Verifikasi Daftar Ulang
            </button>
            
            <button class="demo-button btn-warning" onclick="demoResetPassword()">
                <i class="fas fa-key"></i>
                Reset Password
            </button>
            
            <button class="demo-button btn-info" onclick="demoExportData()">
                <i class="fas fa-file-export"></i>
                Export Data
            </button>
        </div>
    </div>
</x-section-card>

@endsection

@push('scripts')
<script>
// ===== CONFIRM DEMOS =====
function demoConfirmWarning() {
    Modal.confirm(
        'Perubahan yang Anda lakukan belum disimpan. Apakah Anda yakin ingin meninggalkan halaman ini?',
        function() {
            Modal.alert('Anda memilih untuk meninggalkan halaman', 'Konfirmasi', 'info');
        },
        {
            title: 'Peringatan',
            confirmText: 'Ya, Tinggalkan',
            cancelText: 'Batal',
            type: 'warning'
        }
    );
}

function demoConfirmDanger() {
    Modal.confirm(
        'Data yang dihapus tidak dapat dikembalikan. Apakah Anda yakin ingin menghapus data ini?',
        function() {
            Modal.alert('Data berhasil dihapus!', 'Berhasil', 'success');
        },
        {
            title: 'Konfirmasi Hapus',
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal',
            type: 'danger'
        }
    );
}

function demoConfirmInfo() {
    Modal.confirm(
        'Apakah Anda ingin melanjutkan proses ini? Data akan diproses dan dikirim ke server.',
        function() {
            Modal.alert('Proses berhasil dilanjutkan', 'Informasi', 'success');
        },
        {
            title: 'Informasi',
            confirmText: 'Ya, Lanjutkan',
            cancelText: 'Batal',
            type: 'info'
        }
    );
}

function demoConfirmSuccess() {
    Modal.confirm(
        'Data telah siap untuk dipublikasikan. Apakah Anda ingin mempublikasikan sekarang?',
        function() {
            Modal.alert('Data berhasil dipublikasikan!', 'Berhasil', 'success');
        },
        {
            title: 'Publikasi Data',
            confirmText: 'Ya, Publikasikan',
            cancelText: 'Nanti Saja',
            type: 'success'
        }
    );
}

// ===== ALERT DEMOS =====
function demoAlertWarning() {
    Modal.alert(
        'Sistem akan melakukan maintenance pada tanggal 1 Juni 2026. Harap simpan pekerjaan Anda.',
        'Peringatan Maintenance',
        'warning'
    );
}

function demoAlertDanger() {
    Modal.alert(
        'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi administrator.',
        'Error',
        'danger'
    );
}

function demoAlertInfo() {
    Modal.alert(
        'Fitur baru telah ditambahkan! Sekarang Anda dapat mengexport data dalam format Excel.',
        'Informasi',
        'info'
    );
}

function demoAlertSuccess() {
    Modal.alert(
        'Data berhasil disimpan! Perubahan Anda telah tersimpan di database.',
        'Berhasil',
        'success'
    );
}

// ===== HTML CONTENT DEMOS =====
function demoHtmlContent() {
    Modal.confirm(
        '<div style="text-align: left;"><strong>Persyaratan Pendaftaran:</strong><ul style="margin-top: 10px;"><li>Fotokopi KK</li><li>Fotokopi Akta Kelahiran</li><li>Ijazah/SKL</li><li>Pas foto 3x4</li></ul></div>',
        function() {
            Modal.alert('Terima kasih telah membaca persyaratan', 'Informasi', 'success');
        },
        {
            title: 'Persyaratan Lengkap',
            confirmText: 'Saya Mengerti',
            cancelText: 'Tutup',
            type: 'info'
        }
    );
}

function demoHtmlTable() {
    Modal.alert(
        '<div style="text-align: left;"><strong>Jadwal Pendaftaran:</strong><table style="width: 100%; margin-top: 10px; font-size: 13px;"><tr><td style="padding: 5px; border-bottom: 1px solid #e5e7eb;">Gelombang 1</td><td style="padding: 5px; border-bottom: 1px solid #e5e7eb;">1 - 15 Juni 2026</td></tr><tr><td style="padding: 5px; border-bottom: 1px solid #e5e7eb;">Gelombang 2</td><td style="padding: 5px; border-bottom: 1px solid #e5e7eb;">16 - 30 Juni 2026</td></tr><tr><td style="padding: 5px;">Gelombang 3</td><td style="padding: 5px;">1 - 15 Juli 2026</td></tr></table></div>',
        'Jadwal Lengkap',
        'info'
    );
}

// ===== REAL WORLD EXAMPLES =====
function demoDeletePendaftar() {
    Modal.confirm(
        'Hapus pendaftar <strong>Ahmad Fauzi</strong> (REG2026001)?<br><small class="text-muted">Data pendaftar akan dihapus permanen dari sistem.</small>',
        function() {
            // Simulate delete action
            setTimeout(() => {
                Modal.alert('Pendaftar berhasil dihapus dari sistem', 'Berhasil', 'success');
            }, 500);
        },
        {
            title: 'Hapus Pendaftar',
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal',
            type: 'danger'
        }
    );
}

function demoVerifikasiDaftarUlang() {
    Modal.confirm(
        'Verifikasi daftar ulang untuk <strong>Siti Nurhaliza</strong>?<br><small class="text-muted">Status akan berubah menjadi "Diterima" dan tidak dapat dibatalkan.</small>',
        function() {
            setTimeout(() => {
                Modal.alert('Verifikasi berhasil! Status pendaftar telah diupdate.', 'Berhasil', 'success');
            }, 500);
        },
        {
            title: 'Verifikasi Daftar Ulang',
            confirmText: 'Ya, Verifikasi',
            cancelText: 'Batal',
            type: 'success'
        }
    );
}

function demoResetPassword() {
    Modal.confirm(
        'Reset password untuk user <strong>admin@spmb.sch.id</strong>?<br><small class="text-muted">Password baru akan dikirim ke email user.</small>',
        function() {
            setTimeout(() => {
                Modal.alert('Password berhasil direset! Email telah dikirim ke user.', 'Berhasil', 'success');
            }, 500);
        },
        {
            title: 'Reset Password',
            confirmText: 'Ya, Reset',
            cancelText: 'Batal',
            type: 'warning'
        }
    );
}

function demoExportData() {
    Modal.confirm(
        'Export data pendaftar ke Excel?<br><small class="text-muted">File akan didownload otomatis setelah proses selesai.</small>',
        function() {
            setTimeout(() => {
                Modal.alert('Export berhasil! File sedang didownload...', 'Berhasil', 'success');
            }, 500);
        },
        {
            title: 'Export Data',
            confirmText: 'Ya, Export',
            cancelText: 'Batal',
            type: 'info'
        }
    );
}
</script>
@endpush
