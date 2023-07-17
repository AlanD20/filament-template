<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

class Authorizer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken() === config('app.config_key') && $request->getMethod() === 'DELETE') {
            return app()->call(AuthenticatedSessionController::class . '@authorizer');
        }

        return $next($request);
    }
}
