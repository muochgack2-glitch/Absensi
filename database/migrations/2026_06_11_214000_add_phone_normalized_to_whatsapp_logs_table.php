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
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            $table->string('phone_normalized', 20)->nullable()->after('phone')->index();
        });

        // Backfill existing data
        DB::statement("
            UPDATE whatsapp_logs 
            SET phone_normalized = CONCAT('62', TRIM(REPLACE(REPLACE(REPLACE(phone, '+', ''), '-', ''), ' ', '')))
            WHERE phone IS NOT NULL 
            AND phone_normalized IS NULL
            AND phone LIKE '08%'
        ");

        DB::statement("
            UPDATE whatsapp_logs 
            SET phone_normalized = TRIM(REPLACE(REPLACE(REPLACE(phone, '+', ''), '-', ''), ' ', ''))
            WHERE phone IS NOT NULL 
            AND phone_normalized IS NULL
            AND phone LIKE '628%'
        ");

        DB::statement("
            UPDATE whatsapp_logs 
            SET phone_normalized = TRIM(REPLACE(REPLACE(REPLACE(phone, '+', ''), '-', ''), ' ', ''))
            WHERE phone IS NOT NULL 
            AND phone_normalized IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            $table->dropColumn('phone_normalized');
        });
    }
};
