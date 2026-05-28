<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setting_system', function (Blueprint $table) {
            if (! Schema::hasColumn('setting_system', 'school_youtube')) {
                $table->string('school_youtube', 255)->nullable()->after('instagram_url');
            }
            if (! Schema::hasColumn('setting_system', 'tiktok_url')) {
                $table->string('tiktok_url', 255)->nullable()->after('school_youtube');
            }
        });
    }

    public function down(): void
    {
        Schema::table('setting_system', function (Blueprint $table) {
            $table->dropColumn(['school_youtube', 'tiktok_url']);
        });
    }
};
