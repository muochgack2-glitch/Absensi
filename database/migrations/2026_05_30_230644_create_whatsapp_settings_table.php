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
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique(); // Setting key (e.g., wa_server_url, auto_send_enabled)
            $table->text('value')->nullable(); // Setting value
            $table->string('type', 50)->default('string'); // Type: string, boolean, integer, json
            $table->string('group', 50)->default('general'); // Group: general, connection, notification, advanced
            $table->string('label', 200); // Label untuk admin UI
            $table->text('description')->nullable(); // Deskripsi setting
            $table->boolean('is_public')->default(false); // Apakah bisa diakses tanpa auth
            $table->timestamps();
            
            // Index
            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_settings');
    }
};
