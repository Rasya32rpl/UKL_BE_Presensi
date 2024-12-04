<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles) // Menggunakan spread operator untuk menerima banyak role
    {
        // Mengecek apakah user sudah login dan apakah role user ada dalam daftar role yang diperbolehkan
        $user = Auth::user();

        if (!$user || !in_array($user->role, $roles)) {
            return response()->json(['message' => 'Akses ditolak. Anda tidak memiliki hak akses.'], 403);
        }

        return $next($request);
    }
}
