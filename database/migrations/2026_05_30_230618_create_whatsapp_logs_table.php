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
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->index(); // Nomor HP tujuan
            $table->text('message'); // Isi pesan
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending'); // Status pengiriman
            $table->string('type', 50)->default('manual'); // Type: manual, auto_registration, broadcast, reminder
            $table->foreignId('pendaftar_id')->nullable()->constrained('pendaftars')->onDelete('set null'); // Relasi ke pendaftar (jika ada)
            $table->foreignId('template_id')->nullable()->constrained('whatsapp_templates')->onDelete('set null'); // Template yang digunakan
            $table->foreignId('sent_by')->nullable()->constrained('users')->onDelete('set null'); // User yang mengirim (untuk manual send)
            $table->text('error_message')->nullable(); // Error message jika gagal
            $table->timestamp('sent_at')->nullable(); // Waktu terkirim
            $table->json('metadata')->nullable(); // Data tambahan (response dari API, dll)
            $table->timestamps();
            
            // Indexes untuk performa query
            $table->index('status');
            $table->index('type');
            $table->index('sent_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
