<?php

namespace App\Console\Commands;

use App\Models\Pendaftar;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DebugTodayRegistrations extends Command
{
    protected $signature = 'debug:today-registrations';
    protected $description = 'Debug registrations from today';

    public function handle()
    {
        $this->line('=== Debugging Today Registrations ===');
        $this->line('Current time: ' . Carbon::now()->toDateTimeString());
        $this->line('');

        // Check total count
        $total = Pendaftar::count();
        $this->line("Total Pendaftar: {$total}");

        // Check registrations using different methods
        $today = Carbon::now()->startOfDay();
        $tomorrow = Carbon::now()->addDay()->startOfDay();
        
        $this->line('');
        $this->line('--- Using tgl_daftar ---');
        $byTglDaftar = Pendaftar::whereBetween('tgl_daftar', [$today, $tomorrow])->count();
        $this->line("Registrations by tgl_daftar: {$byTglDaftar}");

        $this->line('');
        $this->line('--- Using created_at ---');
        $byCreatedAt = Pendaftar::whereBetween('created_at', [$today, $tomorrow])->count();
        $this->line("Registrations by created_at: {$byCreatedAt}");

        // Show sample data
        $this->line('');
        $this->line('--- Last 5 Registrations ---');
        Pendaftar::latest('id_pendaftar')
            ->take(5)
            ->get()
            ->each(function ($p, $i) use ($today, $tomorrow) {
                $tglDaftar = $p->tgl_daftar?->toDateTimeString() ?? 'NULL';
                $createdAt = $p->created_at?->toDateTimeString() ?? 'NULL';
                $isToday = ($p->tgl_daftar && $p->tgl_daftar->gte($today) && $p->tgl_daftar->lt($tomorrow)) ? '✓' : '-';
                
                $this->line("  [{$i}] #{$p->id_pendaftar} | tgl_daftar: {$tglDaftar} {$isToday} | created_at: {$createdAt}");
            });

        // Check for NULL values
        $this->line('');
        $this->line('--- Data Quality Check ---');
        $nullTglDaftar = Pendaftar::whereNull('tgl_daftar')->count();
        $this->line("Records with NULL tgl_daftar: {$nullTglDaftar}");

        $this->info('Debug complete!');
    }
}
