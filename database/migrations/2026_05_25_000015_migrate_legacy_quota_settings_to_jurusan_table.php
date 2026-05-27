<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting_system') && Schema::hasTable('jurusan')) {
            $setting = DB::table('setting_system')->first();

            if ($setting) {
                $quotaMap = [
                    'MPLB' => $setting->quota_mplb ?? null,
                    'AKL' => $setting->quota_akl ?? null,
                    'BUSANA' => $setting->quota_busana ?? null,
                ];

                foreach ($quotaMap as $kode => $kuota) {
                    if ($kuota !== null) {
                        DB::table('jurusan')
                            ->where('kode', $kode)
                            ->update(['kuota' => (int) $kuota]);
                    }
                }
            }
        }

        if (Schema::hasTable('setting_system')) {
            Schema::table('setting_system', function (Blueprint $table) {
                if (Schema::hasColumn('setting_system', 'quota_mplb')) {
                    $table->dropColumn('quota_mplb');
                }
                if (Schema::hasColumn('setting_system', 'quota_akl')) {
                    $table->dropColumn('quota_akl');
                }
                if (Schema::hasColumn('setting_system', 'quota_busana')) {
                    $table->dropColumn('quota_busana');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting_system')) {
            Schema::table('setting_system', function (Blueprint $table) {
                if (! Schema::hasColumn('setting_system', 'quota_mplb')) {
                    $table->unsignedInteger('quota_mplb')->default(100)->after('principal_name');
                }
                if (! Schema::hasColumn('setting_system', 'quota_akl')) {
                    $table->unsignedInteger('quota_akl')->default(100)->after('quota_mplb');
                }
                if (! Schema::hasColumn('setting_system', 'quota_busana')) {
                    $table->unsignedInteger('quota_busana')->default(100)->after('quota_akl');
                }
            });
        }

        if (Schema::hasTable('setting_system') && Schema::hasTable('jurusan')) {
            $quotaMap = DB::table('jurusan')
                ->whereIn('kode', ['MPLB', 'AKL', 'BUSANA'])
                ->pluck('kuota', 'kode');

            DB::table('setting_system')->update([
                'quota_mplb' => (int) ($quotaMap['MPLB'] ?? 100),
                'quota_akl' => (int) ($quotaMap['AKL'] ?? 100),
                'quota_busana' => (int) ($quotaMap['BUSANA'] ?? 100),
            ]);
        }
    }
};
