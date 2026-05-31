<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppLog;
use App\Models\WhatsAppTemplate;
use App\Models\WhatsAppSetting;
use App\Models\Pendaftar;
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

        $logs = $query->paginate(20);

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
        $pendaftars = Pendaftar::whereNotNull('no_hp_wali')->get();
        
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
        foreach ($request->recipients as $phone) {
            $messages[] = [
                'phone' => $phone,
                'message' => $request->message,
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
        
        return redirect()->route('whatsapp.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
