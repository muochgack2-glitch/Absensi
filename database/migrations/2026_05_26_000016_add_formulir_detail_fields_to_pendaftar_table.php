<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->string('no_kip', 40)->nullable()->after('nik');

            $table->string('alamat_jalan', 255)->nullable()->after('alamat');
            $table->string('alamat_dukuh', 120)->nullable()->after('alamat_jalan');
            $table->string('alamat_rt', 10)->nullable()->after('alamat_dukuh');
            $table->string('alamat_rw', 10)->nullable()->after('alamat_rt');
            $table->string('alamat_kelurahan', 120)->nullable()->after('alamat_rw');
            $table->string('alamat_kecamatan', 120)->nullable()->after('alamat_kelurahan');
            $table->string('alamat_kabupaten', 120)->nullable()->after('alamat_kecamatan');
            $table->string('alamat_provinsi', 120)->nullable()->after('alamat_kabupaten');

            $table->string('alamat_ayah', 255)->nullable()->after('pekerjaan_ayah');
            $table->string('alamat_ibu', 255)->nullable()->after('pekerjaan_ibu');

            $table->string('nama_wali', 100)->nullable()->after('no_hp_ortu');
            $table->string('pekerjaan_wali', 100)->nullable()->after('nama_wali');
            $table->string('alamat_wali', 255)->nullable()->after('pekerjaan_wali');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropColumn([
                'no_kip',
                'alamat_jalan',
                'alamat_dukuh',
                'alamat_rt',
                'alamat_rw',
                'alamat_kelurahan',
                'alamat_kecamatan',
                'alamat_kabupaten',
                'alamat_provinsi',
                'alamat_ayah',
                'alamat_ibu',
                'nama_wali',
                'pekerjaan_wali',
                'alamat_wali',
            ]);
        });
    }
};
