<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdministrator
{
    /**
     * Handle an incoming request - only allows Administrator role
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== 'administrator') {
            abort(403, 'Hanya Administrator yang dapat mengakses halaman ini.');
        }

        // Log activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'access_settings',
            'description' => 'Akses halaman settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $next($request);
    }
}
