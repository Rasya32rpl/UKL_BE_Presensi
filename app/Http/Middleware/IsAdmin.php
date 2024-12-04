<?php

// app/Http/Middleware/IsAdmin.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Mengecek apakah pengguna adalah admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Admins only.'], 403);
        }

        return $next($request);
    }
}

