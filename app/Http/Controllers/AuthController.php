<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        if (Session::has('admin_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['login' => 'Username atau password salah']);
        }

        Session::put('admin_id', $admin->id_admin);
        Session::put('admin_name', $admin->nama_petugas);
        Session::put('admin_username', $admin->username);

        return redirect()->route('dashboard')->with('success', 'Selamat datang ' . $admin->nama_petugas);
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        Session::forget(['admin_id', 'admin_name', 'admin_username']);
        return redirect()->route('home')->with('success', 'Anda telah logout');
    }
}
