<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Priority: session > cookie > default app locale
        $locale = $request->session()->get('app_locale') 
                  ?? $request->cookie('app_locale') 
                  ?? config('app.locale');

        App::setLocale($locale);

        return $next($request);
    }
}
