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
        Schema::create('external_broadcast_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->string('name');
            $table->string('phone', 20);
            $table->string('phone_normalized', 20);
            $table->text('notes')->nullable();
            $table->boolean('is_duplicate_spmb')->default(false);
            $table->unsignedBigInteger('matched_pendaftar_id')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('phone_normalized');
            $table->index('batch_id');
            $table->index('is_duplicate_spmb');
            
            // Foreign keys
            $table->foreign('batch_id')->references('id')->on('external_broadcast_batches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_broadcast_recipients');
    }
};
