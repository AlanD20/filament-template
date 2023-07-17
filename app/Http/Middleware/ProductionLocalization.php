<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ProductionLocalization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('production')) {
            $locale = config('app.production_locale');
            session()->put('locale', $locale);
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
