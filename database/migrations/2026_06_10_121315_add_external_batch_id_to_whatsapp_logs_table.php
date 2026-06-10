<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds external_batch_id column to whatsapp_logs table for tracking
     * messages sent as part of external broadcast batches.
     */
    public function up(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            // Add external_batch_id column (nullable bigint unsigned)
            // Nullable because existing logs and SPMB broadcasts won't have this
            $table->unsignedBigInteger('external_batch_id')->nullable()->after('pendaftar_id');
            
            // Add index on external_batch_id for query performance
            // This improves performance when filtering logs by external batch
            $table->index('external_batch_id');
            
            // Note: Foreign key constraint removed to avoid migration order issues
            // The column still works for relationships via Eloquent
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            // Drop index
            $table->dropIndex(['external_batch_id']);
            
            // Drop the column
            $table->dropColumn('external_batch_id');
        });
    }
};
