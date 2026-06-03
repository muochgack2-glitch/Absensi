<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use App\Models\JaringanMergeHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JaringanController extends Controller
{
    /**
     * Display merge page (Mode selection)
     */
    public function merge(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'name_asc');
        
        // Get all unique jaringan with count
        $query = Pendaftar::select('nama_jaringan', DB::raw('count(*) as total'))
            ->whereNotNull('nama_jaringan')
            ->where('nama_jaringan', '!=', '')
            ->groupBy('nama_jaringan');
        
        // Search filter
        if ($search) {
            $query->having('nama_jaringan', 'like', '%' . $search . '%');
        }
        
        // Sorting
        switch ($sort) {
            case 'name_desc':
                $query->orderBy('nama_jaringan', 'desc');
                break;
            case 'count_asc':
                $query->orderBy('total', 'asc');
                break;
            case 'count_desc':
                $query->orderBy('total', 'desc');
                break;
            default: // name_asc
                $query->orderBy('nama_jaringan', 'asc');
        }
        
        $jaringans = $query->get();
        
        // Detect potential duplicates
        $suggestions = $this->detectDuplicates($jaringans);
        
        return view('jaringan.merge', compact('jaringans', 'suggestions', 'search', 'sort'));
    }
    
    /**
     * Display selective merge page
     */
    public function mergeSelective(Request $request)
    {
        $search = $request->get('search');
        $jaringanFilter = $request->get('jaringan');
        $sort = $request->get('sort', 'no_asc');
        
        // Get all pendaftar
        $query = Pendaftar::query();
        
        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_registrasi', 'like', '%' . $search . '%')
                  ->orWhere('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('nama_jaringan', 'like', '%' . $search . '%');
            });
        }
        
        // Jaringan filter
        if ($jaringanFilter) {
            $query->where('nama_jaringan', $jaringanFilter);
        }
        
        // Sorting
        switch ($sort) {
            case 'no_desc':
                $query->orderBy('no_registrasi', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('nama_lengkap', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('nama_lengkap', 'desc');
                break;
            case 'jaringan_asc':
                $query->orderBy('nama_jaringan', 'asc');
                break;
            case 'jaringan_desc':
                $query->orderBy('nama_jaringan', 'desc');
                break;
            default: // no_asc
                $query->orderBy('no_registrasi', 'asc');
        }
        
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        
        $pendaftars = $query->paginate($perPage)->appends([
            'search' => $search,
            'jaringan' => $jaringanFilter,
            'sort' => $sort,
            'per_page' => $perPage
        ]);
        
        // Get all jaringan for filter
        $jaringans = Pendaftar::select('nama_jaringan')
            ->whereNotNull('nama_jaringan')
            ->where('nama_jaringan', '!=', '')
            ->groupBy('nama_jaringan')
            ->orderBy('nama_jaringan')
            ->pluck('nama_jaringan');
        
        return view('jaringan.merge-selective', compact('pendaftars', 'jaringans', 'search', 'jaringanFilter', 'sort'));
    }
    
    /**
     * Preview merge (Full mode)
     */
    public function preview(Request $request)
    {
        $fromJaringan = $request->input('from_jaringan');
        $toJaringan = $request->input('to_jaringan');
        
        if (empty($fromJaringan) || empty($toJaringan)) {
            return response()->json(['error' => 'Data tidak lengkap'], 400);
        }
        
        // Validation: cannot merge to itself
        if ($fromJaringan === $toJaringan) {
            return response()->json(['error' => 'Tidak bisa menggabungkan jaringan ke dirinya sendiri!'], 400);
        }
        
        // Count affected pendaftar
        $count = Pendaftar::where('nama_jaringan', $fromJaringan)->count();
        
        // Get sample pendaftar (5 data)
        $samples = Pendaftar::where('nama_jaringan', $fromJaringan)
            ->select('no_registrasi', 'nama_lengkap', 'nama_jaringan')
            ->limit(5)
            ->get();
        
        return response()->json([
            'from_jaringan' => $fromJaringan,
            'to_jaringan' => $toJaringan,
            'affected_count' => $count,
            'samples' => $samples
        ]);
    }
    
    /**
     * Preview merge (Selective mode)
     */
    public function previewSelective(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);
        $toJaringan = $request->input('to_jaringan');
        
        if (empty($selectedIds) || empty($toJaringan)) {
            return response()->json(['error' => 'Data tidak lengkap'], 400);
        }
        
        // Get selected pendaftar
        $pendaftars = Pendaftar::whereIn('id_pendaftar', $selectedIds)
            ->select('id_pendaftar', 'no_registrasi', 'nama_lengkap', 'nama_jaringan')
            ->get();
        
        // Group by jaringan
        $groupedByJaringan = $pendaftars->groupBy('nama_jaringan')->map(function($group) {
            return [
                'jaringan' => $group->first()->nama_jaringan,
                'count' => $group->count(),
                'samples' => $group->take(3)->values()
            ];
        })->values();
        
        return response()->json([
            'to_jaringan' => $toJaringan,
            'total_count' => $pendaftars->count(),
            'grouped' => $groupedByJaringan,
            'all_samples' => $pendaftars->take(10)
        ]);
    }
    
    /**
     * Process merge (Full mode)
     */
    public function processMerge(Request $request)
    {
        $request->validate([
            'from_jaringan' => 'required|string|max:255',
            'to_jaringan' => 'required|string|max:255',
        ]);
        
        $fromJaringan = trim($request->input('from_jaringan'));
        $toJaringan = trim($request->input('to_jaringan'));
        
        // Validation: cannot merge to itself
        if ($fromJaringan === $toJaringan) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menggabungkan jaringan ke dirinya sendiri!'
            ], 400);
        }
        
        DB::beginTransaction();
        
        try {
            // Get all pendaftar IDs
            $pendaftarIds = Pendaftar::where('nama_jaringan', $fromJaringan)
                ->pluck('id_pendaftar')
                ->toArray();
            
            if (empty($pendaftarIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada pendaftar dengan jaringan tersebut!'
                ], 400);
            }
            
            // Update pendaftar
            Pendaftar::whereIn('id_pendaftar', $pendaftarIds)
                     ->update(['nama_jaringan' => $toJaringan]);
            
            // Save to history
            JaringanMergeHistory::create([
                'merge_type' => 'full',
                'from_jaringan' => $fromJaringan,
                'to_jaringan' => $toJaringan,
                'affected_count' => count($pendaftarIds),
                'pendaftar_ids' => $pendaftarIds,
                'merged_by' => auth()->id(),
                'merged_by_name' => auth()->user()->name,
                'merged_by_role' => auth()->user()->role,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menggabungkan " . count($pendaftarIds) . " pendaftar dari '{$fromJaringan}' ke '{$toJaringan}'.",
                'total' => count($pendaftarIds)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menggabungkan jaringan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Process merge (Selective mode)
     */
    public function processMergeSelective(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array|min:1',
            'to_jaringan' => 'required|string|max:255',
        ]);
        
        $selectedIds = $request->input('selected_ids');
        $toJaringan = trim($request->input('to_jaringan'));
        
        DB::beginTransaction();
        
        try {
            // Get pendaftar details for history
            $pendaftars = Pendaftar::whereIn('id_pendaftar', $selectedIds)->get();
            
            // Get unique jaringan sources
            $fromJaringans = $pendaftars->pluck('nama_jaringan')->unique()->values()->toArray();
            $fromJaringanStr = implode(', ', $fromJaringans);
            
            // Update pendaftar
            Pendaftar::whereIn('id_pendaftar', $selectedIds)
                     ->update(['nama_jaringan' => $toJaringan]);
            
            // Save to history
            JaringanMergeHistory::create([
                'merge_type' => 'selective',
                'from_jaringan' => $fromJaringanStr,
                'to_jaringan' => $toJaringan,
                'affected_count' => count($selectedIds),
                'pendaftar_ids' => $selectedIds,
                'merged_by' => auth()->id(),
                'merged_by_name' => auth()->user()->name,
                'merged_by_role' => auth()->user()->role,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menggabungkan " . count($selectedIds) . " pendaftar terpilih ke '{$toJaringan}'.",
                'total' => count($selectedIds)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menggabungkan pendaftar: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display history page
     */
    public function history(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $typeFilter = $request->get('type', 'all');
        $search = $request->get('search', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        
        $query = JaringanMergeHistory::orderBy('created_at', 'desc');
        
        // Filter by status
        if ($filter === 'active') {
            $query->where('is_undone', false);
        } elseif ($filter === 'undone') {
            $query->where('is_undone', true);
        }
        
        // Filter by type
        if ($typeFilter === 'full') {
            $query->where('merge_type', 'full');
        } elseif ($typeFilter === 'selective') {
            $query->where('merge_type', 'selective');
        }
        
        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('from_jaringan', 'like', '%' . $search . '%')
                  ->orWhere('to_jaringan', 'like', '%' . $search . '%');
            });
        }
        
        // Date range filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        
        $histories = $query->paginate($perPage)->appends([
            'filter' => $filter,
            'type' => $typeFilter,
            'search' => $search,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'per_page' => $perPage
        ]);
        
        return view('jaringan.history', compact('histories', 'filter', 'typeFilter', 'search', 'dateFrom', 'dateTo'));
    }
    
    /**
     * Undo merge - Kembalikan data ke kondisi sebelum merge
     */
    public function undo($id)
    {
        $history = JaringanMergeHistory::findOrFail($id);
        
        // Check if already undone
        if ($history->is_undone) {
            return redirect()->back()->with('error', 'Merge ini sudah pernah di-undo sebelumnya!');
        }
        
        DB::beginTransaction();
        
        try {
            $pendaftarIds = $history->pendaftar_ids;
            
            if (empty($pendaftarIds)) {
                return redirect()->back()->with('error', 'Data pendaftar tidak ditemukan!');
            }
            
            // Kembalikan pendaftar berdasarkan ID yang tersimpan
            if ($history->merge_type === 'full') {
                // Full mode: kembalikan ke 1 jaringan
                Pendaftar::whereIn('id_pendaftar', $pendaftarIds)
                    ->update(['nama_jaringan' => $history->from_jaringan]);
            } else {
                // Selective mode: kembalikan ke jaringan masing-masing
                // Kita perlu data original jaringan, untuk sekarang kembalikan ke from_jaringan pertama
                // TODO: Bisa disempurnakan dengan simpan original jaringan per siswa
                $fromJaringans = explode(', ', $history->from_jaringan);
                Pendaftar::whereIn('id_pendaftar', $pendaftarIds)
                    ->update(['nama_jaringan' => $fromJaringans[0]]);
            }
            
            // Update history status
            $history->update([
                'is_undone' => true,
                'undone_at' => now(),
                'undone_by' => auth()->id(),
                'undone_by_name' => auth()->user()->name,
                'undone_by_role' => auth()->user()->role,
            ]);
            
            DB::commit();
            
            $updated = count($pendaftarIds);
            return redirect()->back()->with('success', "Berhasil membatalkan merge! {$updated} pendaftar dikembalikan.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->with('error', 'Gagal membatalkan merge: ' . $e->getMessage());
        }
    }
    
    /**
     * Get jaringan statistics for dashboard
     */
    public function stats()
    {
        try {
            // Count unique jaringan
            $uniqueCount = Pendaftar::select('nama_jaringan')
                ->whereNotNull('nama_jaringan')
                ->where('nama_jaringan', '!=', '')
                ->groupBy('nama_jaringan')
                ->get()
                ->count();
            
            // Get all jaringan for duplicate detection
            $jaringans = Pendaftar::select('nama_jaringan', DB::raw('count(*) as total'))
                ->whereNotNull('nama_jaringan')
                ->where('nama_jaringan', '!=', '')
                ->groupBy('nama_jaringan')
                ->get();
            
            // Detect duplicates
            $suggestions = $this->detectDuplicates($jaringans);
            $duplicateCount = count($suggestions);
            
            // Count cleaned this month
            $cleanedThisMonth = JaringanMergeHistory::where('is_undone', false)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('affected_count');
            
            return response()->json([
                'uniqueCount' => $uniqueCount,
                'duplicateCount' => $duplicateCount,
                'cleanedThisMonth' => $cleanedThisMonth,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load jaringan stats',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Detect potential duplicates
     */
    private function detectDuplicates($jaringans)
    {
        $suggestions = [];
        $processed = [];
        
        foreach ($jaringans as $j1) {
            if (in_array($j1->nama_jaringan, $processed)) {
                continue;
            }
            
            $group = [$j1];
            
            foreach ($jaringans as $j2) {
                if ($j1->nama_jaringan === $j2->nama_jaringan) {
                    continue;
                }
                
                if (in_array($j2->nama_jaringan, $processed)) {
                    continue;
                }
                
                // Check similarity
                if ($this->isSimilar($j1->nama_jaringan, $j2->nama_jaringan)) {
                    $group[] = $j2;
                    $processed[] = $j2->nama_jaringan;
                }
            }
            
            if (count($group) > 1) {
                $suggestions[] = $group;
            }
            
            $processed[] = $j1->nama_jaringan;
        }
        
        return $suggestions;
    }
    
    /**
     * Check if two names are similar
     */
    private function isSimilar($name1, $name2)
    {
        // Normalize
        $n1 = $this->normalize($name1);
        $n2 = $this->normalize($name2);
        
        // Exact match after normalization
        if ($n1 === $n2) {
            return true;
        }
        
        // Check similarity percentage
        similar_text($n1, $n2, $percent);
        
        return $percent >= 80;
    }
    
    /**
     * Normalize name for comparison
     */
    private function normalize($name)
    {
        $name = strtoupper(trim($name));
        $name = preg_replace('/\s+/', ' ', $name); // Remove double spaces
        $name = str_replace(['.', ',', '-', '_'], '', $name); // Remove special chars
        
        return $name;
    }
}
