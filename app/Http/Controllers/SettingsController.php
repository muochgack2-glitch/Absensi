<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\SettingSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SettingSystem::instance()->toSettingsArray();
        $jurusans = Jurusan::orderByDesc('aktif')->orderBy('kode')->get();

        return view('settings.index', [
            'settings' => $settings,
            'jurusans' => $jurusans,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name'         => 'required|string|max:255',
            'academic_year'       => 'required|string|max:20',
            'registration_status' => 'required|in:open,closed',
            'registration_fee'    => 'required|integer|min:0',
            'active_wave'         => 'required|string|max:100',
            'school_address'      => 'nullable|string|max:500',
            'school_contact'      => 'nullable|string|max:100',
            'principal_name'      => 'nullable|string|max:255',
            'school_logo'         => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'favicon'             => 'nullable|image|mimes:ico,png,jpg,jpeg,webp,svg|max:1024',
            'school_city'         => 'nullable|string|max:120',
            'school_phone'        => 'nullable|string|max:50',
            'school_email'        => 'nullable|email|max:120',
            'school_website'      => 'nullable|url|max:255',
            'instagram_url'       => 'nullable|url|max:255',
            'school_youtube'      => 'nullable|url|max:255',
            'tiktok_url'          => 'nullable|url|max:255',
            'theme_preset'        => 'nullable|in:purple,blue,green,orange,red,slate',
            'theme_primary'       => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_secondary'     => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'print_footer_text'   => 'nullable|string|max:255',
            'document_header_text' => 'nullable|string|max:255',
            'document_city'       => 'nullable|string|max:120',
            'document_sign_name'  => 'nullable|string|max:255',
            'document_sign_title' => 'nullable|string|max:255',
        ]);

        $setting = SettingSystem::instance();

        if ($request->hasFile('school_logo')) {
            if (!empty($setting->school_logo) && Storage::disk('public')->exists($setting->school_logo)) {
                Storage::disk('public')->delete($setting->school_logo);
            }

            $validated['school_logo'] = $request->file('school_logo')->store('settings/logos', 'public');
        } else {
            unset($validated['school_logo']);
        }

        if ($request->hasFile('favicon')) {
            if (!empty($setting->favicon) && Storage::disk('public')->exists($setting->favicon)) {
                Storage::disk('public')->delete($setting->favicon);
            }

            $validated['favicon'] = $request->file('favicon')->store('settings/favicons', 'public');
        } else {
            unset($validated['favicon']);
        }

        $themePresets = [
            'purple' => ['#667eea', '#764ba2'],
            'blue'   => ['#0ea5e9', '#0369a1'],
            'green'  => ['#10b981', '#047857'],
            'orange' => ['#f97316', '#c2410c'],
            'red'    => ['#ef4444', '#991b1b'],
            'slate'  => ['#475569', '#0f172a'],
        ];

        if (!empty($validated['theme_preset']) && isset($themePresets[$validated['theme_preset']])) {
            [$validated['theme_primary'], $validated['theme_secondary']] = $themePresets[$validated['theme_preset']];
        }

        $waveNumber = (int) filter_var($validated['active_wave'], FILTER_SANITIZE_NUMBER_INT);
        if ($waveNumber > 0) {
            $validated['gelombang_aktif'] = $waveNumber;
        }

        $setting->update($validated);

        return redirect()->route('settings.index')->with('success', 'Pengaturan sistem berhasil disimpan.');
    }

    public function storeJurusan(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:30', Rule::unique('jurusan', 'kode')],
            'nama' => 'required|string|max:150',
            'kuota' => 'required|integer|min:0',
            'aktif' => 'nullable|boolean',
        ]);

        Jurusan::create([
            'kode' => strtoupper(trim($validated['kode'])),
            'nama' => trim($validated['nama']),
            'kuota' => (int) $validated['kuota'],
            'aktif' => (bool) ($validated['aktif'] ?? true),
        ]);

        return redirect()->route('settings.index')->with('success', 'Jurusan baru berhasil ditambahkan.');
    }

    public function updateJurusan(Request $request, Jurusan $jurusan)
    {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:30',
                Rule::unique('jurusan', 'kode')->ignore($jurusan->id),
            ],
            'nama' => 'required|string|max:150',
            'kuota' => 'required|integer|min:0',
            'aktif' => 'nullable|boolean',
        ]);

        $jurusan->update([
            'kode' => strtoupper(trim($validated['kode'])),
            'nama' => trim($validated['nama']),
            'kuota' => (int) $validated['kuota'],
            'aktif' => (bool) ($validated['aktif'] ?? false),
        ]);

        return redirect()->route('settings.index')->with('success', 'Data jurusan berhasil diperbarui.');
    }

    public function destroyJurusan(Jurusan $jurusan)
    {
        if ($jurusan->pendaftars()->exists()) {
            return redirect()->route('settings.index')->with('error', 'Jurusan tidak bisa dihapus karena sudah digunakan oleh data pendaftar.');
        }

        $jurusan->delete();

        return redirect()->route('settings.index')->with('success', 'Jurusan berhasil dihapus.');
    }
}
