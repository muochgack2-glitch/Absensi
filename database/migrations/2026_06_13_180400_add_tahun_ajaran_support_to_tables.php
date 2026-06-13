<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan kolom tahun_ajaran ke tabel-tabel yang memerlukan
     * untuk mendukung multi-year data management.
     * 
     * SAFETY: Migration ini hanya ADD column (non-destructive)
     * - Default value untuk existing data: '2026/2027'
     * - Index untuk performance
     * - Rollback: DROP column
     */
    public function up(): void
    {
        // Add to pendaftar table
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->string('tahun_ajaran', 9)
                ->default('2026/2027')
                ->after('status_siswa')
                ->comment('Format: YYYY/YYYY, contoh: 2027/2028');
            
            $table->index('tahun_ajaran');
        });

        // Add to logistik_bayar table
        Schema::table('logistik_bayar', function (Blueprint $table) {
            $table->string('tahun_ajaran', 9)
                ->default('2026/2027')
                ->after('status_bayar')
                ->comment('Format: YYYY/YYYY, contoh: 2027/2028');
            
            $table->index('tahun_ajaran');
        });

        // Add to whatsapp_logs table (if exists)
        if (Schema::hasTable('whatsapp_logs')) {
            Schema::table('whatsapp_logs', function (Blueprint $table) {
                $table->string('tahun_ajaran', 9)
                    ->default('2026/2027')
                    ->after('status')
                    ->comment('Format: YYYY/YYYY, contoh: 2027/2028');
                
                $table->index('tahun_ajaran');
            });
        }
    }

    /**
     * Reverse the migrations.
     * 
     * ROLLBACK PLAN: Hapus kolom tahun_ajaran yang baru ditambahkan
     */
    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropIndex(['tahun_ajaran']);
            $table->dropColumn('tahun_ajaran');
        });

        Schema::table('logistik_bayar', function (Blueprint $table) {
            $table->dropIndex(['tahun_ajaran']);
            $table->dropColumn('tahun_ajaran');
        });

        if (Schema::hasTable('whatsapp_logs')) {
            Schema::table('whatsapp_logs', function (Blueprint $table) {
                $table->dropIndex(['tahun_ajaran']);
                $table->dropColumn('tahun_ajaran');
            });
        }
    }
};
