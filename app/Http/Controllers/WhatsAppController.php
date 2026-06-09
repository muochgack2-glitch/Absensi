<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use App\Models\LogistikBayar;
use App\Models\Jurusan;
use App\Models\SettingSystem;
use App\Models\WhatsAppLog;
use App\Models\WhatsAppTemplate;
use App\Models\WhatsAppSetting;
use App\Models\UserActivityLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WhatsAppController extends Controller
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Dashboard WhatsApp Gateway
     */
    public function index()
    {
        $status = $this->whatsappService->getStatus();
        $statistics = $this->whatsappService->getStatistics();
        
        $recentLogs = WhatsAppLog::with(['pendaftar', 'template', 'sender'])
            ->latest()
            ->limit(10)
            ->get();

        return view('whatsapp.index', compact('status', 'statistics', 'recentLogs'));
    }

    /**
     * Get server status (AJAX)
     */
    public function status()
    {
        $status = $this->whatsappService->getStatus();
        return response()->json($status);
    }

    /**
     * Get server health metrics (AJAX)
     */
    public function health()
    {
        $health = $this->whatsappService->getHealth();
        return response()->json($health);
    }

    /**
     * Get QR code (AJAX)
     */
    public function qrCode()
    {
        $qr = $this->whatsappService->getQRCode();
        return response()->json($qr);
    }

    /**
     * Send message page
     */
    public function sendPage()
    {
        $templates = WhatsAppTemplate::active()->get();
        return view('whatsapp.send', compact('templates'));
    }

    /**
     * Send single message
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->whatsappService->send(
            $request->phone,
            $request->message,
            [
                'type' => 'manual',
                'sent_by' => auth()->id(),
            ]
        );

        return response()->json($result);
    }

    /**
     * Send message with template
     */
    public function sendWithTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'template_id' => 'required|exists:whatsapp_templates,id',
            'data' => 'nullable|array', // Changed to nullable - template might not have variables
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $template = WhatsAppTemplate::findOrFail($request->template_id);
        
        $result = $this->whatsappService->sendWithTemplate(
            $request->phone,
            $template->name,
            $request->data,
            [
                'type' => 'manual',
                'sent_by' => auth()->id(),
            ]
        );

        return response()->json($result);
    }

    /**
     * Logs page
     */
    public function logs(Request $request)
    {
        $query = WhatsAppLog::with(['pendaftar', 'template', 'sender'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filter by date
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        // Search by phone
        if ($request->has('search') && $request->search != '') {
            $query->where('phone', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        
        $logs = $query->paginate($perPage)->appends($request->except('page'));

        return view('whatsapp.logs', compact('logs'));
    }

    /**
     * Templates page
     */
    public function templates()
    {
        $templates = WhatsAppTemplate::latest()->get();
        return view('whatsapp.templates', compact('templates'));
    }

    /**
     * Create template page
     */
    public function createTemplate()
    {
        return view('whatsapp.template-form');
    }

    /**
     * Store template
     */
    public function storeTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:whatsapp_templates,name',
            'label' => 'required|string',
            'message' => 'required|string',
            'type' => 'required|in:registration,payment,reminder,notification,custom',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'auto_send' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        WhatsAppTemplate::create($request->all());

        return redirect()->route('whatsapp.templates')
            ->with('success', 'Template created successfully');
    }

    /**
     * Edit template page
     */
    public function editTemplate($id)
    {
        $template = WhatsAppTemplate::findOrFail($id);
        return view('whatsapp.template-form', compact('template'));
    }

    /**
     * Update template
     */
    public function updateTemplate(Request $request, $id)
    {
        $template = WhatsAppTemplate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:whatsapp_templates,name,' . $id,
            'label' => 'required|string',
            'message' => 'required|string',
            'type' => 'required|in:registration,payment,reminder,notification,custom',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'auto_send' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $template->update($request->all());

        return redirect()->route('whatsapp.templates')
            ->with('success', 'Template updated successfully');
    }

    /**
     * Delete template
     */
    public function deleteTemplate($id)
    {
        $template = WhatsAppTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('whatsapp.templates')
            ->with('success', 'Template deleted successfully');
    }

    /**
     * Preview template
     */
    public function previewTemplate($id)
    {
        $template = WhatsAppTemplate::findOrFail($id);
        $preview = $template->getPreview();

        return response()->json([
            'success' => true,
            'preview' => $preview,
        ]);
    }

    /**
     * Settings page
     */
    public function settings()
    {
        $settings = WhatsAppSetting::orderBy('group')->get()->groupBy('group');
        return view('whatsapp.settings', compact('settings'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            WhatsAppSetting::set($key, $value);
        }

        // Clear cache
        WhatsAppSetting::clearCache();

        return redirect()->route('whatsapp.settings')
            ->with('success', 'Settings updated successfully');
    }

    /**
     * Broadcast page
     */
    public function broadcastPage()
    {
        $templates = WhatsAppTemplate::active()->get();
        
        // Get pendaftars with any valid phone number (siswa, ortu, or wali)
        $pendaftars = Pendaftar::where(function($query) {
            $query->where(function($q) {
                $q->whereNotNull('no_telepon')
                  ->where('no_telepon', '!=', '')
                  ->where('no_telepon', '!=', '-');
            })
            ->orWhere(function($q) {
                $q->whereNotNull('no_hp_ortu')
                  ->where('no_hp_ortu', '!=', '')
                  ->where('no_hp_ortu', '!=', '-');
            })
            ->orWhere(function($q) {
                $q->whereNotNull('no_hp_wali')
                  ->where('no_hp_wali', '!=', '')
                  ->where('no_hp_wali', '!=', '-');
            });
        })->get();
        
        // Set primary phone for each pendaftar (priority: wali > ortu > siswa)
        $pendaftars->each(function($p) {
            if (!empty($p->no_hp_wali) && $p->no_hp_wali != '-') {
                $p->primary_phone = $p->no_hp_wali;
                $p->phone_type = 'Wali';
            } elseif (!empty($p->no_hp_ortu) && $p->no_hp_ortu != '-') {
                $p->primary_phone = $p->no_hp_ortu;
                $p->phone_type = 'Orang Tua';
            } elseif (!empty($p->no_telepon) && $p->no_telepon != '-') {
                $p->primary_phone = $p->no_telepon;
                $p->phone_type = 'Siswa';
            }
        });
        
        return view('whatsapp.broadcast', compact('templates', 'pendaftars'));
    }

    /**
     * Send broadcast
     */
    public function sendBroadcast(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipients' => 'required|array',
            'recipients.*' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $messages = [];
        $successCount = 0;
        $failedCount = 0;
        $results = [];

        foreach ($request->recipients as $phone) {
            // Find pendaftar by phone number
            $pendaftar = Pendaftar::where(function($query) use ($phone) {
                $query->where('no_hp_wali', $phone)
                      ->orWhere('no_hp_ortu', $phone)
                      ->orWhere('no_telepon', $phone);
            })->first();

            // Replace variables in message
            $personalizedMessage = $request->message;
            
            if ($pendaftar) {
                $personalizedMessage = $this->replaceMessageVariables($request->message, [
                    'name' => $pendaftar->nama_lengkap,
                    'no_reg' => $pendaftar->no_registrasi,
                    'jurusan' => $pendaftar->jurusan,
                    'nisn' => $pendaftar->nisn,
                    'asal_sekolah' => $pendaftar->asal_sekolah,
                ]);
            }

            $messages[] = [
                'phone' => $phone,
                'message' => $personalizedMessage,
            ];
        }

        $result = $this->whatsappService->sendBulk($messages, [
            'type' => 'broadcast',
            'sent_by' => auth()->id(),
        ]);

        return response()->json($result);
    }

    /**
     * Logout from WhatsApp
     */
    public function logout()
    {
        $result = $this->whatsappService->logout();
        
        // Return JSON for AJAX request
        if (request()->expectsJson()) {
            return response()->json($result);
        }
        
        // Return redirect for regular request
        return redirect()->route('whatsapp.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Restart WhatsApp server
     */
    public function restart()
    {
        // Check last restart time (cooldown 5 minutes)
        $lastRestart = cache()->get('wa_server_last_restart');
        if ($lastRestart && now()->diffInMinutes($lastRestart) < 5) {
            $remainingMinutes = 5 - now()->diffInMinutes($lastRestart);
            return response()->json([
                'success' => false,
                'message' => "Server baru saja restart. Tunggu {$remainingMinutes} menit lagi."
            ], 429);
        }

        // Log activity
        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'wa_server_restart',
            'description' => 'User initiated WA Gateway server restart',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Set cooldown
        cache()->put('wa_server_last_restart', now(), 300); // 5 minutes

        // Restart server
        $result = $this->whatsappService->restart();
        
        return response()->json($result);
    }

    /**
     * Phone list page - Rekap nomor HP pendaftar
     */
    public function phoneList(Request $request)
    {
        $query = Pendaftar::with('masterJurusan');

        // Filter by jurusan
        if ($request->has('jurusan_id') && $request->jurusan_id != '') {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        // Filter by gelombang
        if ($request->has('gelombang') && $request->gelombang != '') {
            $query->where('gelombang', $request->gelombang);
        }

        // Filter by status siswa
        if ($request->has('status_siswa') && $request->status_siswa != '') {
            $query->where('status_siswa', $request->status_siswa);
        }

        // Filter by phone type
        $phoneType = $request->get('phone_type', 'all');
        if ($phoneType != 'all') {
            switch ($phoneType) {
                case 'wali':
                    $query->whereNotNull('no_hp_wali')->where('no_hp_wali', '!=', '');
                    break;
                case 'ortu':
                    $query->whereNotNull('no_hp_ortu')->where('no_hp_ortu', '!=', '');
                    break;
                case 'siswa':
                    $query->whereNotNull('no_telepon')->where('no_telepon', '!=', '');
                    break;
            }
        }

        // Search by name or NISN
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('no_registrasi', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        
        $pendaftars = $query->orderBy('tgl_daftar', 'desc')->paginate($perPage)->appends($request->except('page'));

        // Add phone data to each pendaftar
        $pendaftars->getCollection()->transform(function ($pendaftar) use ($phoneType) {
            $pendaftar->phone_data = $this->getPhoneDataForDisplay($pendaftar, $phoneType);
            return $pendaftar;
        });

        // Get statistics
        $statistics = $this->getPhoneStatistics();

        // Get jurusans for filter
        $jurusans = Jurusan::where('aktif', true)->orderBy('nama')->get();

        // Get unique gelombangs
        $gelombangs = Pendaftar::select('gelombang')
            ->distinct()
            ->whereNotNull('gelombang')
            ->orderBy('gelombang')
            ->pluck('gelombang');

        // Get templates for broadcast
        $templates = WhatsAppTemplate::active()->get();

        return view('whatsapp.phone-list', compact(
            'pendaftars',
            'statistics',
            'jurusans',
            'gelombangs',
            'templates'
        ));
    }

    /**
     * Send bulk broadcast from phone list
     */
    public function sendBulkBroadcast(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phones' => 'required|array|min:1',
            'phones.*.phone' => 'required|string',
            'phones.*.name' => 'required|string',
            'phones.*.no_reg' => 'required|string',
            'phones.*.jurusan' => 'required|string',
            'message' => 'required|string',
            'template_id' => 'nullable|exists:whatsapp_templates,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $phones = $request->phones;
        $message = $request->message;
        $templateId = $request->template_id;

        $results = [];
        $successCount = 0;
        $failedCount = 0;

        foreach ($phones as $phoneData) {
            try {
                // Replace variables in message for each recipient
                $personalizedMessage = $this->replaceMessageVariables($message, $phoneData);

                // Send message
                $result = $this->whatsappService->send(
                    $phoneData['phone'],
                    $personalizedMessage,
                    [
                        'type' => 'broadcast',
                        'sent_by' => auth()->id(),
                        'template_id' => $templateId,
                        'pendaftar_id' => $phoneData['id'] ?? null,
                    ]
                );

                if ($result['success']) {
                    $successCount++;
                } else {
                    $failedCount++;
                }

                $results[] = [
                    'phone' => $phoneData['phone'],
                    'name' => $phoneData['name'],
                    'success' => $result['success'],
                    'message' => $result['message'] ?? null,
                ];

                // Delay between messages
                if (count($phones) > 1) {
                    sleep(1);
                }

            } catch (\Exception $e) {
                $failedCount++;
                $results[] = [
                    'phone' => $phoneData['phone'],
                    'name' => $phoneData['name'],
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Broadcast selesai. Terkirim: {$successCount}, Gagal: {$failedCount}",
            'total' => count($phones),
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'results' => $results,
        ]);
    }

    /**
     * Get phone data for display
     */
    private function getPhoneDataForDisplay(Pendaftar $pendaftar, string $phoneType = 'all'): array
    {
        $phone = null;
        $type = null;

        if ($phoneType == 'all') {
            // Priority: wali > ortu > siswa
            if (!empty($pendaftar->no_hp_wali)) {
                $phone = $pendaftar->no_hp_wali;
                $type = 'Wali';
            } elseif (!empty($pendaftar->no_hp_ortu)) {
                $phone = $pendaftar->no_hp_ortu;
                $type = 'Orang Tua';
            } elseif (!empty($pendaftar->no_telepon)) {
                $phone = $pendaftar->no_telepon;
                $type = 'Siswa';
            }
        } elseif ($phoneType == 'wali' && !empty($pendaftar->no_hp_wali)) {
            $phone = $pendaftar->no_hp_wali;
            $type = 'Wali';
        } elseif ($phoneType == 'ortu' && !empty($pendaftar->no_hp_ortu)) {
            $phone = $pendaftar->no_hp_ortu;
            $type = 'Orang Tua';
        } elseif ($phoneType == 'siswa' && !empty($pendaftar->no_telepon)) {
            $phone = $pendaftar->no_telepon;
            $type = 'Siswa';
        }

        // Format phone number
        $formatted = null;
        if ($phone) {
            $formatted = $this->formatPhoneForDisplay($phone);
        }

        return [
            'phone' => $phone,
            'formatted' => $formatted,
            'type' => $type,
        ];
    }

    /**
     * Format phone number for display
     */
    private function formatPhoneForDisplay(string $phone): string
    {
        // Remove non-numeric
        $cleaned = preg_replace('/\D/', '', $phone);

        // Convert to 62xxx format
        if (substr($cleaned, 0, 1) === '0') {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (!str_starts_with($cleaned, '62')) {
            $cleaned = '62' . $cleaned;
        }

        // Format: 0812-3456-7890
        $original = $phone;
        if (substr($original, 0, 1) === '0') {
            return substr($original, 0, 4) . '-' . substr($original, 4, 4) . '-' . substr($original, 8);
        }

        return $phone;
    }

    /**
     * Get phone statistics
     */
    private function getPhoneStatistics(): array
    {
        $totalPendaftar = Pendaftar::count();
        
        $withPhone = Pendaftar::where(function($query) {
            $query->whereNotNull('no_hp_wali')
                  ->orWhereNotNull('no_hp_ortu')
                  ->orWhereNotNull('no_telepon');
        })->where(function($query) {
            $query->where('no_hp_wali', '!=', '')
                  ->orWhere('no_hp_ortu', '!=', '')
                  ->orWhere('no_telepon', '!=', '');
        })->count();

        $withoutPhone = $totalPendaftar - $withPhone;
        $phonePercentage = $totalPendaftar > 0 ? round(($withPhone / $totalPendaftar) * 100, 1) : 0;

        return [
            'total_pendaftar' => $totalPendaftar,
            'with_phone' => $withPhone,
            'without_phone' => $withoutPhone,
            'phone_percentage' => $phonePercentage,
        ];
    }

    /**
     * Replace message variables with actual data
     */
    private function replaceMessageVariables(string $message, array $data): string
    {
        $settings = SettingSystem::instance()->toSettingsArray();
        
        $replacements = [
            '{nama}' => $data['name'] ?? '',
            '{nama_lengkap}' => $data['name'] ?? '',
            '{no_pendaftaran}' => $data['no_reg'] ?? '',
            '{no_registrasi}' => $data['no_reg'] ?? '',
            '{jurusan}' => $data['jurusan'] ?? '',
            '{nisn}' => $data['nisn'] ?? '',
            '{asal_sekolah}' => $data['asal_sekolah'] ?? '',
            '{portal_url}' => url('/'),
            '{sekolah}' => $settings['school_name'] ?? 'SMK PGRI BLORA',
            '{tanggal}' => now()->format('d-m-Y'),
            '{tahun}' => now()->format('Y'),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    /**
     * Get PM2 diagnostics
     */
    public function diagnostics()
    {
        try {
            // Get PM2 process list (using full path)
            $pm2Output = shell_exec('/usr/bin/pm2 jlist 2>&1');
            $pm2Processes = json_decode($pm2Output, true);
            
            // Find whatsapp-server process
            $process = null;
            if (is_array($pm2Processes)) {
                foreach ($pm2Processes as $p) {
                    if (isset($p['name']) && $p['name'] === 'whatsapp-server') {
                        $process = $p;
                        break;
                    }
                }
            }

            $issues = [];
            $recommendations = [];

            if (!$process) {
                $issues[] = [
                    'type' => 'error',
                    'code' => 'PROCESS_NOT_FOUND',
                    'title' => 'PM2 Process Not Found',
                    'description' => 'WhatsApp server process tidak ditemukan di PM2',
                    'auto_fixable' => true
                ];
            } else {
                $pm2 = $process['pm2_env'] ?? [];
                $monit = $process['monit'] ?? [];
                $restarts = $pm2['restart_time'] ?? 0;
                $status = $pm2['status'] ?? 'unknown';
                $memory = $monit['memory'] ?? 0;
                $memoryMB = round($memory / 1024 / 1024, 2);

                // Check crash loop (restarts > 10)
                if ($restarts > 10) {
                    $issues[] = [
                        'type' => 'warning',
                        'code' => 'CRASH_LOOP',
                        'title' => 'High Restart Count',
                        'description' => "Server telah restart {$restarts} kali. Mungkin ada masalah yang perlu diperbaiki.",
                        'auto_fixable' => true
                    ];
                }

                // Check memory usage (> 500 MB)
                if ($memoryMB > 500) {
                    $issues[] = [
                        'type' => 'warning',
                        'code' => 'HIGH_MEMORY',
                        'title' => 'High Memory Usage',
                        'description' => "Memory usage tinggi: {$memoryMB} MB. Pertimbangkan restart server.",
                        'auto_fixable' => false
                    ];
                }

                // Check if process is stopped
                if ($status === 'stopped' || $status === 'errored') {
                    $issues[] = [
                        'type' => 'error',
                        'code' => 'PROCESS_STOPPED',
                        'title' => 'Process Stopped',
                        'description' => "Server dalam status: {$status}",
                        'auto_fixable' => true
                    ];
                }

                // Check for import path errors in logs
                $errorLog = shell_exec('/usr/bin/pm2 logs whatsapp-server --err --lines 50 --nostream 2>&1');
                if ($errorLog && str_contains($errorLog, 'ERR_UNSUPPORTED_DIR_IMPORT')) {
                    $issues[] = [
                        'type' => 'error',
                        'code' => 'IMPORT_PATH_ERROR',
                        'title' => 'Import Path Error',
                        'description' => 'Ditemukan error import path di logs. Server perlu direstart dengan konfigurasi yang benar.',
                        'auto_fixable' => true
                    ];
                }
            }

            // Get fix history from cache
            $fixHistory = cache()->get('wa_diagnostics_fix_history', []);

            return response()->json([
                'success' => true,
                'data' => [
                    'process' => $process,
                    'issues' => $issues,
                    'recommendations' => $recommendations,
                    'fix_history' => array_slice($fixHistory, -10), // Last 10 fixes
                    'timestamp' => now()->toIso8601String()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get diagnostics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-fix detected issues
     */
    public function autoFix()
    {
        try {
            // Rate limiting: Max 3 auto-fixes per hour
            $fixCount = cache()->get('wa_autofix_count', 0);
            $fixTimestamp = cache()->get('wa_autofix_timestamp');
            
            if ($fixCount >= 3 && $fixTimestamp && now()->diffInHours($fixTimestamp) < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rate limit exceeded. Maximum 3 auto-fixes per hour. Please wait.'
                ], 429);
            }

            // Get diagnostics first
            $diagnosticsResponse = $this->diagnostics();
            $diagnostics = $diagnosticsResponse->getData(true);
            
            if (!$diagnostics['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get diagnostics'
                ], 500);
            }

            $issues = $diagnostics['data']['issues'] ?? [];
            $fixedIssues = [];
            $failedIssues = [];

            foreach ($issues as $issue) {
                if (!$issue['auto_fixable']) {
                    continue;
                }

                $fixed = false;
                $fixMessage = '';

                switch ($issue['code']) {
                    case 'PROCESS_NOT_FOUND':
                        // Start new PM2 process
                        $output = shell_exec('cd ' . base_path('whatsapp-server') . ' && /usr/bin/pm2 start server.js --name whatsapp-server 2>&1');
                        $fixed = true;
                        $fixMessage = 'Started new PM2 process';
                        break;

                    case 'IMPORT_PATH_ERROR':
                        // Delete process and restart with correct path
                        shell_exec('/usr/bin/pm2 delete whatsapp-server 2>&1');
                        sleep(2);
                        $output = shell_exec('cd ' . base_path('whatsapp-server') . ' && /usr/bin/pm2 start server.js --name whatsapp-server 2>&1');
                        $fixed = true;
                        $fixMessage = 'Deleted and restarted process with correct configuration';
                        break;

                    case 'CRASH_LOOP':
                        // Flush logs and restart
                        shell_exec('/usr/bin/pm2 flush whatsapp-server 2>&1');
                        sleep(1);
                        shell_exec('/usr/bin/pm2 restart whatsapp-server 2>&1');
                        $fixed = true;
                        $fixMessage = 'Flushed logs and restarted process';
                        break;

                    case 'PROCESS_STOPPED':
                        // Restart the process
                        shell_exec('/usr/bin/pm2 restart whatsapp-server 2>&1');
                        $fixed = true;
                        $fixMessage = 'Restarted stopped process';
                        break;
                }

                if ($fixed) {
                    $fixedIssues[] = [
                        'code' => $issue['code'],
                        'title' => $issue['title'],
                        'fix' => $fixMessage
                    ];
                } else {
                    $failedIssues[] = [
                        'code' => $issue['code'],
                        'title' => $issue['title'],
                        'reason' => 'No auto-fix available'
                    ];
                }
            }

            // Update fix counter
            if (!empty($fixedIssues)) {
                cache()->put('wa_autofix_count', $fixCount + 1, 3600);
                cache()->put('wa_autofix_timestamp', now(), 3600);

                // Add to fix history
                $fixHistory = cache()->get('wa_diagnostics_fix_history', []);
                $fixHistory[] = [
                    'timestamp' => now()->toIso8601String(),
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name,
                    'fixed_issues' => $fixedIssues,
                    'failed_issues' => $failedIssues
                ];
                cache()->put('wa_diagnostics_fix_history', $fixHistory, 86400 * 7); // 7 days

                // Log activity
                UserActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'wa_auto_fix',
                    'description' => 'Applied auto-fix for ' . count($fixedIssues) . ' issue(s)',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => count($fixedIssues) > 0 
                    ? 'Auto-fix applied successfully. Fixed ' . count($fixedIssues) . ' issue(s).'
                    : 'No fixable issues found.',
                'data' => [
                    'fixed' => $fixedIssues,
                    'failed' => $failedIssues
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Auto-fix failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get error logs from PM2
     */
    public function getErrorLogs()
    {
        try {
            $errorLog = shell_exec('/usr/bin/pm2 logs whatsapp-server --err --lines 100 --nostream 2>&1');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'logs' => $errorLog ?? 'No error logs found',
                    'timestamp' => now()->toIso8601String()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get error logs: ' . $e->getMessage()
            ], 500);
        }
    }
}
