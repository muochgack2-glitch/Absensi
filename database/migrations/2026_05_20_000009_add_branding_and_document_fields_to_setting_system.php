<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setting_system', function (Blueprint $table) {
            // Branding
            $table->string('school_website', 255)->nullable()->after('school_email');
            $table->string('instagram_url', 255)->nullable()->after('school_website');

            // Document settings
            $table->string('document_header_text', 255)->nullable()->after('print_footer_text');
            $table->string('document_city', 120)->nullable()->after('document_header_text');
            $table->string('document_sign_name', 255)->nullable()->after('document_city');
            $table->string('document_sign_title', 255)->nullable()->after('document_sign_name');
        });
    }

    public function down(): void
    {
        Schema::table('setting_system', function (Blueprint $table) {
            $table->dropColumn([
                'school_website',
                'instagram_url',
                'document_header_text',
                'document_city',
                'document_sign_name',
                'document_sign_title',
            ]);
        });
    }
};
