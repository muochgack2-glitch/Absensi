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
        Schema::create('logistik_bayar', function (Blueprint $table) {
            $table->id('id_logistik');
            $table->unsignedBigInteger('id_pendaftar');
            $table->enum('status_bayar', ['Belum', 'Lunas'])->default('Belum');
            $table->string('ukuran_kaos')->nullable();
            $table->enum('status_kain', ['Belum', 'Sudah'])->default('Belum');
            $table->enum('status_kaos', ['Belum', 'Proses', 'Sudah'])->default('Belum');
            $table->timestamps();

            // Foreign key
            $table->foreign('id_pendaftar')
                ->references('id_pendaftar')
                ->on('pendaftar')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistik_bayar');
    }
};
