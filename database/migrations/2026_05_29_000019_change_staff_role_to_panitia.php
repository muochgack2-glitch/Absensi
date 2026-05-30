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
        // SQLite doesn't support MODIFY COLUMN or ENUM
        // We just need to update existing 'staff' role to 'panitia'
        DB::table('users')
            ->where('role', 'staff')
            ->update(['role' => 'panitia']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: Update 'panitia' back to 'staff'
        DB::table('users')
            ->where('role', 'panitia')
            ->update(['role' => 'staff']);
    }
};
