<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user()?->loadMissing('roles.permissions');

        abort_if(! $user, 401);
        abort_if(! $user->hasPermission($permission), 403);

        return $next($request);
    }
}
