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
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            // Add foreign key constraints
            $table->foreign('pendaftar_id')
                ->references('id')
                ->on('pendaftars')
                ->onDelete('set null');
            
            $table->foreign('template_id')
                ->references('id')
                ->on('whatsapp_templates')
                ->onDelete('set null');
            
            $table->foreign('sent_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            $table->dropForeign(['pendaftar_id']);
            $table->dropForeign(['template_id']);
            $table->dropForeign(['sent_by']);
        });
    }
};
