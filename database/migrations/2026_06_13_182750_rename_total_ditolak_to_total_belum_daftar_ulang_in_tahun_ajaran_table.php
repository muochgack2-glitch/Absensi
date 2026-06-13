<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support column rename directly via Laravel Schema
        // We need to use raw SQL
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite: Rename using ALTER TABLE
            DB::statement('ALTER TABLE tahun_ajaran RENAME COLUMN total_ditolak TO total_belum_daftar_ulang');
        } else {
            // MySQL/PostgreSQL: Use Schema builder
            Schema::table('tahun_ajaran', function (Blueprint $table) {
                $table->renameColumn('total_ditolak', 'total_belum_daftar_ulang');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            DB::statement('ALTER TABLE tahun_ajaran RENAME COLUMN total_belum_daftar_ulang TO total_ditolak');
        } else {
            Schema::table('tahun_ajaran', function (Blueprint $table) {
                $table->renameColumn('total_belum_daftar_ulang', 'total_ditolak');
            });
        }
    }
};
