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
        // Add backup gateway settings
        DB::table('whatsapp_settings')->insert([
            [
                'key' => 'wa_server_url_backup',
                'value' => 'http://localhost:3001',
                'type' => 'string',
                'group' => 'connection',
                'label' => 'Backup Server URL',
                'description' => 'WhatsApp Gateway Backup Server (Future: Absensi)',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wa_failover_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'connection',
                'label' => 'Enable Auto Failover',
                'description' => 'Automatically switch to backup if primary fails',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wa_failover_timeout',
                'value' => '5',
                'type' => 'integer',
                'group' => 'connection',
                'label' => 'Failover Check Timeout',
                'description' => 'Timeout in seconds for health check before failover',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('whatsapp_settings')->whereIn('key', [
            'wa_server_url_backup',
            'wa_failover_enabled',
            'wa_failover_timeout',
        ])->delete();
    }
};
