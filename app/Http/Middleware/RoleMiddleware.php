<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Attribute;

#[Attribute]
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            abort(403, 'Accès non autorisé');
        }

        return $next($request);
    }
}
