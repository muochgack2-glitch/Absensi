<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Services\TahunAjaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TahunAjaranController extends Controller
{
    protected $tahunAjaranService;
    
    public function __construct(TahunAjaranService $tahunAjaranService)
    {
        $this->tahunAjaranService = $tahunAjaranService;
    }
    
    /**
     * Display list of tahun ajaran
     */
    public function index()
    {
        $tahunAjarans = TahunAjaran::with('creator')
            ->orderBy('tahun', 'desc')
            ->get();
        
        $activeTA = $this->tahunAjaranService->getActiveTahunAjaran();
        
        return view('admin.tahun-ajaran.index', compact('tahunAjarans', 'activeTA'));
    }
    
    /**
     * Show form untuk create new tahun ajaran
     */
    public function create()
    {
        $activeTA = $this->tahunAjaranService->getActiveTahunAjaran();
        $suggestedYear = $this->suggestNextYear($activeTA);
        
        return view('admin.tahun-ajaran.create', compact('activeTA', 'suggestedYear'));
    }
    
    /**
     * Store new tahun ajaran
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string|regex:/^\d{4}\/\d{4}$/|unique:tahun_ajaran,tahun',
            'started_at' => 'nullable|date',
            'closed_at' => 'nullable|date|after_or_equal:started_at',
            'archive_current' => 'nullable|boolean',
        ], [
            'tahun.required' => 'Tahun ajaran wajib diisi',
            'tahun.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2027/2028)',
            'tahun.unique' => 'Tahun ajaran sudah ada',
            'closed_at.after_or_equal' => 'Periode selesai harus setelah atau sama dengan periode mulai',
        ]);
        
        $options = [
            'archive_current' => $request->boolean('archive_current', true),
            'clone_config' => true,
            'auto_backup' => true,
            'started_at' => $request->started_at ?: now(),
            'closed_at' => $request->closed_at,
        ];
        
        $result = $this->tahunAjaranService->createNewYear($request->tahun, $options);
        
        if ($result['success']) {
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('success', $result['message'])
                ->with('backup_path', $result['backup_path']);
        }
        
        return redirect()->back()
            ->withInput()
            ->with('error', $result['message']);
    }
    
    /**
     * Activate specific tahun ajaran
     */
    public function activate($id)
    {
        try {
            $ta = TahunAjaran::findOrFail($id);
            $ta->activate();
            
            Log::info('Academic year activated', [
                'tahun' => $ta->tahun,
                'user' => auth()->user()->name,
            ]);
            
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('success', "Tahun ajaran {$ta->tahun} berhasil diaktifkan");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengaktifkan tahun ajaran: ' . $e->getMessage());
        }
    }
    
    /**
     * Archive specific tahun ajaran
     */
    public function archive($id)
    {
        try {
            $ta = TahunAjaran::findOrFail($id);
            
            if ($ta->isActive()) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat mengarsipkan tahun ajaran yang sedang aktif. Aktifkan tahun ajaran lain terlebih dahulu.');
            }
            
            $ta->archive();
            
            Log::info('Academic year archived', [
                'tahun' => $ta->tahun,
                'user' => auth()->user()->name,
            ]);
            
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('success', "Tahun ajaran {$ta->tahun} berhasil diarsipkan");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengarsipkan tahun ajaran: ' . $e->getMessage());
        }
    }
    
    /**
     * Update statistics for specific tahun ajaran
     */
    public function updateStatistics($id)
    {
        try {
            $ta = TahunAjaran::findOrFail($id);
            $ta->updateStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Statistik berhasil diperbarui',
                'data' => [
                    'total_pendaftar' => $ta->total_pendaftar,
                    'total_diterima' => $ta->total_diterima,
                    'total_ditolak' => $ta->total_ditolak,
                ],
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui statistik: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Sync all statistics
     */
    public function syncAllStatistics()
    {
        try {
            $updated = $this->tahunAjaranService->syncAllStatistics();
            
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('success', "Berhasil memperbarui statistik untuk {$updated} tahun ajaran");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal sinkronisasi statistik: ' . $e->getMessage());
        }
    }
    
    /**
     * Get statistics for specific year (AJAX)
     */
    public function getStatistics(Request $request)
    {
        $tahun = $request->get('tahun', 'all');
        $stats = $this->tahunAjaranService->getStatistics($tahun);
        
        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
    
    /**
     * Suggest next year based on current active year
     */
    private function suggestNextYear($currentTA)
    {
        if (!$currentTA) {
            return now()->year . '/' . (now()->year + 1);
        }
        
        $parts = explode('/', $currentTA->tahun);
        $nextYear1 = (int)$parts[1];
        $nextYear2 = $nextYear1 + 1;
        
        return "{$nextYear1}/{$nextYear2}";
    }
    
    /**
     * Show detail page for specific tahun ajaran
     */
    public function show($id)
    {
        $ta = TahunAjaran::with(['creator', 'pendaftars'])
            ->findOrFail($id);
        
        $stats = $this->tahunAjaranService->getStatistics($ta->tahun);
        
        // Get pendaftar per bulan untuk chart (database agnostic)
        $driver = config('database.default');
        $monthExpression = $driver === 'sqlite' 
            ? "CAST(strftime('%m', created_at) AS INTEGER)"
            : "MONTH(created_at)";
        
        $pendaftarPerBulan = $ta->pendaftars()
            ->selectRaw("{$monthExpression} as bulan, COUNT(*) as total")
            ->whereNull('deleted_at')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
        
        return view('admin.tahun-ajaran.show', compact('ta', 'stats', 'pendaftarPerBulan'));
    }
    
    /**
     * Delete tahun ajaran permanently
     */
    public function destroy($id)
    {
        try {
            $ta = TahunAjaran::findOrFail($id);
            
            // Prevent deleting active year
            if ($ta->isActive()) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif. Aktifkan tahun ajaran lain terlebih dahulu.');
            }
            
            $tahun = $ta->tahun;
            $totalPendaftar = $ta->total_pendaftar;
            
            // Delete all related pendaftar (including soft deleted and orphans)
            $deletedCount = \App\Models\Pendaftar::where('tahun_ajaran', $tahun)
                ->withTrashed()
                ->forceDelete();
            
            // Also delete related logistik_bayar records
            \DB::table('logistik_bayar')
                ->where('tahun_ajaran', $tahun)
                ->delete();
            
            Log::info("Deleted {$deletedCount} pendaftar (including soft deleted) for tahun {$tahun}");
            
            // Delete tahun ajaran
            $ta->delete();
            
            Log::warning('Academic year permanently deleted', [
                'tahun' => $tahun,
                'total_pendaftar_deleted' => $deletedCount,
                'user' => auth()->user()->name,
            ]);
            
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('success', "Tahun ajaran {$tahun} beserta {$deletedCount} pendaftar berhasil dihapus permanen");
                
        } catch (\Exception $e) {
            Log::error('Failed to delete tahun ajaran', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus tahun ajaran: ' . $e->getMessage());
        }
    }
}
