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
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // Nama template (e.g., welcome_message, payment_reminder)
            $table->string('label', 200); // Label untuk admin UI (e.g., "Pesan Selamat Datang")
            $table->text('message'); // Template pesan (support variables: {nama}, {no_pendaftaran}, {jurusan}, dll)
            $table->text('description')->nullable(); // Deskripsi template
            $table->enum('type', ['registration', 'payment', 'reminder', 'notification', 'custom'])->default('custom'); // Kategori template
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif
            $table->boolean('auto_send')->default(false); // Kirim otomatis atau manual
            $table->json('variables')->nullable(); // List variabel yang tersedia (untuk dokumentasi)
            $table->integer('usage_count')->default(0); // Jumlah pemakaian
            $table->timestamp('last_used_at')->nullable(); // Terakhir digunakan
            $table->timestamps();
            
            // Indexes
            $table->index('type');
            $table->index('is_active');
            $table->index('auto_send');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
