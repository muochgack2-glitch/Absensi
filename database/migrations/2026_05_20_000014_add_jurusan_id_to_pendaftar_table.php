<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->unsignedBigInteger('jurusan_id')->nullable()->after('jurusan');
            $table->index('jurusan_id');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropIndex(['jurusan_id']);
            $table->dropColumn('jurusan_id');
        });
    }
};
