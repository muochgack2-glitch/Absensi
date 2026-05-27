<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setting_system', function (Blueprint $table) {
            $table->string('theme_preset', 50)->default('purple')->after('favicon');
            $table->string('theme_primary', 20)->nullable()->after('theme_preset');
            $table->string('theme_secondary', 20)->nullable()->after('theme_primary');
        });
    }

    public function down(): void
    {
        Schema::table('setting_system', function (Blueprint $table) {
            $table->dropColumn(['theme_preset', 'theme_primary', 'theme_secondary']);
        });
    }
};
