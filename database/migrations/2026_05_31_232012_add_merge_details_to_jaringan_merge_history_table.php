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
        Schema::table('jaringan_merge_history', function (Blueprint $table) {
            $table->enum('merge_type', ['full', 'selective'])->default('full')->after('id');
            $table->json('pendaftar_ids')->nullable()->after('affected_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jaringan_merge_history', function (Blueprint $table) {
            $table->dropColumn(['merge_type', 'pendaftar_ids']);
        });
    }
};
