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
        // Add theme_preference to admin table (singular)
        Schema::table('admin', function (Blueprint $table) {
            $table->enum('theme_preference', ['light', 'dark'])->default('dark')->after('nama_petugas');
        });
        
        // Add theme_preference to users table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('theme_preference', ['light', 'dark'])->default('dark')->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            $table->dropColumn('theme_preference');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('theme_preference');
        });
    }
};
