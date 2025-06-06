<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$role): Response
    {
        if(Auth::check() && Auth::user()->role=== $role){
            return $next($request);
        }
        else{
            abort(304,'unauthorize');
        }
    }
}
// Auth::check();
// Auth::user();
// Auth::attempt([...]);
// Auth::logout();

// app/Http/Middleware/RedirectIfAuthenticated.php
//public function handle(Request $request, Closure $next, ...$guards)
// {
//     if (Auth::check()) {
//         return redirect('/dashboard'); // ← This is your current redirect
//     }

//     return $next($request);
// }
// public function handle(Request $request, Closure $next, ...$guards)
// {
//     $guard = $guards[0] ?? null;

//     if (Auth::guard($guard)->check()) {
//         return redirect('/home'); // Redirect wherever you prefer
//     }

//     return $next($request);
// }

// In app/Http/Middleware/EnsureUserIsRequesterOrAdmin.php:
// public function handle($request, Closure $next)
// {
//     $user = auth()->user();
//     $ticket = $request->route('ticket'); // assuming route model binding

//     if ($user->id !== $ticket->requester_id && !$user->is_admin) {
//         abort(403, 'Unauthorized');
//     }

//     return $next($request);
// }
// protected $routeMiddleware = [
//     // ...
//     'requester.or.admin' => \App\Http\Middleware\EnsureUserIsRequesterOrAdmin::class,
// ];app/Http/Kernel.php:
