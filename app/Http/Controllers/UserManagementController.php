<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filter_role = $request->get('role');
        $filter_status = $request->get('status');

        $query = User::query();

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($filter_role) {
            $query->where('role', $filter_role);
        }

        // Status filter
        if ($filter_status) {
            $query->where('status', $filter_status);
        }

        $users = $query->paginate(15);
        $roles = ['administrator', 'panitia', 'admin_wa'];
        $statuses = ['aktif', 'nonaktif', 'suspended'];

        return view('users.index', compact('users', 'search', 'filter_role', 'filter_status', 'roles', 'statuses'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create', [
            'roles' => ['administrator', 'panitia', 'admin_wa'],
            'statuses' => ['aktif', 'nonaktif', 'suspended'],
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:administrator,panitia,admin_wa',
            'status' => 'required|in:aktif,nonaktif,suspended',
        ], [
            'name.required' => 'Nama pengguna harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role harus dipilih',
            'status.required' => 'Status harus dipilih',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
            'email_verified_at' => now(),
        ]);

        // Log activity
        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'model' => 'User',
            'model_id' => $user->id,
            'description' => "Create user: {$user->name} ({$user->email})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Pengguna '{$user->name}' berhasil dibuat");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Prevent editing users with higher privileges
        if ($user->role === 'administrator' && auth()->user()->role !== 'administrator') {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak memiliki izin mengubah pengguna administrator');
        }

        return view('users.edit', [
            'user' => $user,
            'roles' => ['administrator', 'panitia', 'admin_wa'],
            'statuses' => ['aktif', 'nonaktif', 'suspended'],
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Prevent editing self role/status
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa mengubah role atau status akun sendiri');
        }

        // Prevent non-admins from editing admins
        if ($user->role === 'administrator' && auth()->user()->role !== 'administrator') {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak memiliki izin mengubah pengguna administrator');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:administrator,panitia,admin_wa',
            'status' => 'required|in:aktif,nonaktif,suspended',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama pengguna harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role harus dipilih',
            'status.required' => 'Status harus dipilih',
        ]);

        $old_data = $user->only(['name', 'email', 'role', 'status']);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Log activity
        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model' => 'User',
            'model_id' => $user->id,
            'description' => "Update user: {$user->name} ({$user->email})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Pengguna '{$user->name}' berhasil diperbarui");
    }

    /**
     * Remove the specified user from storage (soft delete - deactivate).
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri');
        }

        // Prevent non-admins from deleting admins
        if ($user->role === 'administrator' && auth()->user()->role !== 'administrator') {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak memiliki izin menghapus pengguna administrator');
        }

        $user_name = $user->name;

        // Soft delete - deactivate user
        $user->update(['status' => 'nonaktif']);

        // Log activity
        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'model' => 'User',
            'model_id' => $user->id,
            'description' => "Deactivate user: {$user_name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Pengguna '{$user_name}' berhasil dideaktifkan");
    }

    /**
     * Show activity logs for a user
     */
    public function activityLog(User $user)
    {
        $logs = UserActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('users.activity-log', compact('user', 'logs'));
    }

    /**
     * Reactivate a deactivated user
     */
    public function reactivate(User $user)
    {
        if ($user->status !== 'nonaktif') {
            return back()->with('error', 'User bukan status nonaktif');
        }

        $user->update(['status' => 'aktif']);

        // Log activity
        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'reactivate',
            'model' => 'User',
            'model_id' => $user->id,
            'description' => "Reactivate user: {$user->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Pengguna '{$user->name}' berhasil diaktifkan kembali");
    }
}
