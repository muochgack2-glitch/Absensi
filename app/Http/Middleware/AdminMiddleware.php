<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Supports both old Session-based and new Auth-based authentication
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check new Auth system (Laravel Auth facade)
        if (Auth::check()) {
            return $next($request);
        }

        // Check old Session system (backward compatibility)
        if (Session::has('admin_id')) {
            return $next($request);
        }

        // Not authenticated
        return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu');
    }
}
