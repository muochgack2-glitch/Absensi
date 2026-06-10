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
        Schema::create('external_broadcast_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name');
            $table->text('description')->nullable();
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('total_sent')->default(0);
            $table->unsignedInteger('total_failed')->default(0);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->enum('source_type', ['csv', 'manual']);
            $table->string('source_file')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->timestamp('completed_at')->nullable();
            
            // Indexes
            $table->index('created_by');
            $table->index('status');
            $table->index('created_at');
            
            // Foreign key
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_broadcast_batches');
    }
};
