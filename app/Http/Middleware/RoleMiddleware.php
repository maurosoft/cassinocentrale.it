<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lascia passare solo gli utenti con uno dei ruoli indicati.
 * Uso nelle rotte: ->middleware('role:superadmin,reception')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Il superadmin può fare tutto.
        if ($user && ($user->isSuperadmin() || in_array($user->role, $roles, true))) {
            return $next($request);
        }

        abort(403, 'Non hai i permessi per accedere a questa sezione.');
    }
}
