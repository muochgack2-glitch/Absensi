<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Alter enum to include both 'staff' and 'panitia' temporarily
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrator', 'staff', 'panitia') NOT NULL DEFAULT 'panitia'");
        
        // Step 2: Update existing 'staff' role to 'panitia'
        DB::table('users')
            ->where('role', 'staff')
            ->update(['role' => 'panitia']);

        // Step 3: Remove 'staff' from enum, keeping only 'administrator' and 'panitia'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrator', 'panitia') NOT NULL DEFAULT 'panitia'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Alter enum to include both 'staff' and 'panitia' temporarily
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrator', 'staff', 'panitia') NOT NULL DEFAULT 'staff'");
        
        // Step 2: Update existing 'panitia' role back to 'staff'
        DB::table('users')
            ->where('role', 'panitia')
            ->update(['role' => 'staff']);

        // Step 3: Remove 'panitia' from enum, keeping only 'administrator' and 'staff'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrator', 'staff') NOT NULL DEFAULT 'staff'");
    }
};
