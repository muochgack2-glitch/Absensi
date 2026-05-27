<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setting_system', function (Blueprint $table) {
            $table->string('school_name', 255)->default('SMK Contoh Indonesia')->after('gelombang_aktif');
            $table->string('academic_year', 20)->default('2026/2027')->after('school_name');
            $table->enum('registration_status', ['open', 'closed'])->default('open')->after('academic_year');
            $table->unsignedInteger('registration_fee')->default(150000)->after('registration_status');
            $table->string('active_wave', 100)->default('Gelombang 1')->after('registration_fee');
            $table->string('principal_name', 255)->nullable()->after('active_wave');
            $table->unsignedInteger('quota_mplb')->default(100)->after('principal_name');
            $table->unsignedInteger('quota_akl')->default(100)->after('quota_mplb');
            $table->unsignedInteger('quota_busana')->default(100)->after('quota_akl');
            $table->string('school_address', 500)->nullable()->after('quota_busana');
            $table->string('school_contact', 100)->nullable()->after('school_address');
            $table->string('school_city', 120)->nullable()->after('school_contact');
            $table->string('school_phone', 50)->nullable()->after('school_city');
            $table->string('school_email', 120)->nullable()->after('school_phone');
            $table->string('school_logo', 500)->nullable()->after('school_email');
            $table->string('print_footer_text', 255)->nullable()->after('school_logo');
        });
    }

    public function down(): void
    {
        Schema::table('setting_system', function (Blueprint $table) {
            $table->dropColumn([
                'school_name', 'academic_year', 'registration_status',
                'registration_fee', 'active_wave', 'principal_name',
                'quota_mplb', 'quota_akl', 'quota_busana',
                'school_address', 'school_contact', 'school_city',
                'school_phone', 'school_email', 'school_logo',
                'print_footer_text',
            ]);
        });
    }
};
