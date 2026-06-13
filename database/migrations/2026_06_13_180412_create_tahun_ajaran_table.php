<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Membuat tabel tahun_ajaran untuk mengelola tahun pelajaran
     * dengan auto registration number dan statistics tracking
     */
    public function up(): void
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->string('tahun', 9)->unique()->comment('Format: YYYY/YYYY, contoh: 2027/2028');
            $table->enum('status', ['upcoming', 'active', 'archived'])->default('upcoming');
            
            // Registration Number Config
            $table->string('reg_number_pattern', 50)->default('SPMB-{YEAR}-{NUMBER:4}')->comment('Pattern untuk generate nomor registrasi');
            $table->unsignedInteger('reg_number_current')->default(0)->comment('Counter nomor registrasi saat ini');
            
            // Statistics
            $table->unsignedInteger('total_pendaftar')->default(0);
            $table->unsignedInteger('total_diterima')->default(0);
            $table->unsignedInteger('total_ditolak')->default(0);
            
            // Timestamps
            $table->date('started_at')->nullable()->comment('Tanggal mulai pendaftaran');
            $table->date('closed_at')->nullable()->comment('Tanggal tutup pendaftaran');
            
            // Audit (without foreign key for compatibility)
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('tahun');
            $table->index('created_by');
        });

        // Get total pendaftar from database
        $totalPendaftar = DB::table('pendaftar')->count();
        $totalDiterima = DB::table('pendaftar')->where('status_siswa', 'Diterima')->count();
        $totalDitolak = DB::table('pendaftar')->where('status_siswa', 'Ditolak')->count();

        // Insert current year (backfill untuk data yang sudah ada)
        DB::table('tahun_ajaran')->insert([
            'tahun' => '2026/2027',
            'status' => 'active',
            'reg_number_pattern' => 'SPMB-{YEAR}-{NUMBER:4}',
            'reg_number_current' => $totalPendaftar,
            'total_pendaftar' => $totalPendaftar,
            'total_diterima' => $totalDiterima,
            'total_ditolak' => $totalDitolak,
            'started_at' => now(),
            'created_by' => 1, // Admin ID
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add setting untuk active tahun ajaran
        // Check if column 'key' exists in setting_system (for compatibility)
        if (Schema::hasTable('setting_system')) {
            $columns = Schema::getColumnListing('setting_system');
            
            if (in_array('key', $columns)) {
                // New format (with key column)
                $existing = DB::table('setting_system')->where('key', 'active_tahun_ajaran')->first();
                if (!$existing) {
                    DB::table('setting_system')->insert([
                        'key' => 'active_tahun_ajaran',
                        'value' => '2026/2027',
                        'group' => 'system',
                        'label' => 'Tahun Pelajaran Aktif',
                        'description' => 'Tahun pelajaran yang sedang berjalan saat ini',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // Old format (just update academic_year column)
                DB::table('setting_system')->update([
                    'academic_year' => '2026/2027',
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     * 
     * ROLLBACK PLAN: Hapus tabel dan setting
     */
    public function down(): void
    {
        // Remove setting if exists
        if (Schema::hasTable('setting_system')) {
            DB::table('setting_system')->where('key', 'active_tahun_ajaran')->delete();
        }

        Schema::dropIfExists('tahun_ajaran');
    }
};
