<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only administrators may open the /admin area (rule R-24).
 *
 * Registered under the short name "admin" in bootstrap/app.php
 * and used by the route group in routes/admin.php.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Administrators only.');
        }

        return $next($request);
    }
}
