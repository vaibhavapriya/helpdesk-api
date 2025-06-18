<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestApi
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Use the 'api' guard (default for Passport)
        if (Auth::guard('api')->check()) {
            return response()->json([
                'message' => 'Already authenticated.'
            ], 403); // or 401 if you prefer
        }

        return $next($request);
    }
}

