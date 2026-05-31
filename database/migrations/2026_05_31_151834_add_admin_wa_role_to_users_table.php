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
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            // MySQL: Modify ENUM to add 'admin_wa'
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrator', 'panitia', 'admin_wa') NOT NULL DEFAULT 'panitia'");
        } elseif ($driver === 'sqlite') {
            // SQLite: Need to recreate table to modify CHECK constraint
            // Step 1: Create temporary table with new schema
            Schema::create('users_temp', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->enum('role', ['administrator', 'panitia', 'admin_wa'])->default('panitia');
                $table->enum('status', ['aktif', 'nonaktif', 'suspended'])->default('aktif');
                $table->timestamp('terakhir_login')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
            
            // Step 2: Copy data from old table to temp table
            DB::statement('INSERT INTO users_temp SELECT * FROM users');
            
            // Step 3: Drop old table
            Schema::drop('users');
            
            // Step 4: Rename temp table to users
            Schema::rename('users_temp', 'users');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            // Kembalikan ke ENUM sebelumnya (tanpa admin_wa)
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrator', 'panitia') NOT NULL DEFAULT 'panitia'");
        } elseif ($driver === 'sqlite') {
            // SQLite: Recreate table with old schema
            Schema::create('users_temp', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->enum('role', ['administrator', 'panitia'])->default('panitia');
                $table->enum('status', ['aktif', 'nonaktif', 'suspended'])->default('aktif');
                $table->timestamp('terakhir_login')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
            
            // Copy data (excluding admin_wa users)
            DB::statement("INSERT INTO users_temp SELECT * FROM users WHERE role != 'admin_wa'");
            
            Schema::drop('users');
            Schema::rename('users_temp', 'users');
        }
    }
};
