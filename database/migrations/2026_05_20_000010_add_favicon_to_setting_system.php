<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setting_system', function (Blueprint $table) {
            $table->string('favicon', 500)->nullable()->after('school_logo');
        });
    }

    public function down(): void
    {
        Schema::table('setting_system', function (Blueprint $table) {
            $table->dropColumn('favicon');
        });
    }
};
