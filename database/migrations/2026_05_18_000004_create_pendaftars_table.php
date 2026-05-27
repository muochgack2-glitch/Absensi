<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendaftar', function (Blueprint $table) {
            $table->id('id_pendaftar');
            $table->string('no_registrasi')->unique();
            $table->string('nisn')->unique();
            $table->string('nama_lengkap');
            $table->string('asal_sekolah');
            $table->text('alamat');
            $table->enum('jurusan', ['MPLB', 'AKL', 'BUSANA']);
            $table->string('nama_jaringan')->nullable();
            $table->integer('gelombang')->default(1);
            $table->timestamp('tgl_daftar')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftar');
    }
};
