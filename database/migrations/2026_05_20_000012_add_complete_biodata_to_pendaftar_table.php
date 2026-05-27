<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->string('status_data', 20)->default('awal')->after('status_siswa');
            $table->string('email', 100)->nullable()->after('nama_lengkap');
            $table->string('no_telepon', 20)->nullable()->after('email');
            $table->string('nik', 20)->nullable()->after('nisn');
            $table->string('tempat_lahir', 100)->nullable()->after('nik');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
            $table->string('agama', 50)->nullable()->after('jenis_kelamin');
            $table->string('tahun_lulus', 10)->nullable()->after('asal_sekolah');
            $table->string('nama_ayah', 100)->nullable()->after('alamat');
            $table->string('pekerjaan_ayah', 100)->nullable()->after('nama_ayah');
            $table->string('nama_ibu', 100)->nullable()->after('pekerjaan_ayah');
            $table->string('pekerjaan_ibu', 100)->nullable()->after('nama_ibu');
            $table->string('no_hp_ortu', 20)->nullable()->after('pekerjaan_ibu');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropColumn([
                'status_data', 'email', 'no_telepon', 'nik', 'tempat_lahir',
                'tanggal_lahir', 'jenis_kelamin', 'agama', 'tahun_lulus',
                'nama_ayah', 'pekerjaan_ayah', 'nama_ibu', 'pekerjaan_ibu',
                'no_hp_ortu',
            ]);
        });
    }
};
