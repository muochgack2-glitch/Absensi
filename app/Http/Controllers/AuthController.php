<?php

namespace App\Http\Controllers;

use App\Models\SettingSystem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $settings = SettingSystem::instance()->toSettingsArray();
        return view('auth.login', compact('settings'));
    }

    /**
     * Handle login request using User model
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Check user exists and password correct
        if (!$user || !\Illuminate\Support\Facades\Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'Email atau password salah',
            ]);
        }

        // Check user is active
        if ($user->status !== 'aktif') {
            throw ValidationException::withMessages([
                'login' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ]);
        }

        // Check user has valid role
        if (!in_array($user->role, ['administrator', 'panitia', 'admin_wa'])) {
            throw ValidationException::withMessages([
                'login' => 'Role pengguna tidak valid.',
            ]);
        }

        // Update last login
        $user->updateLastLogin();

        // Log activity
        \App\Models\UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'Login berhasil',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Authenticate user
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Selamat datang ' . $user->name . '!');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Log activity
        if (Auth::check()) {
            \App\Models\UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'logout',
                'description' => 'Logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah logout');
    }
}
