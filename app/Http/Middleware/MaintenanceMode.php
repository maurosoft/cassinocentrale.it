<?php

namespace App\Http\Middleware;

use App\Support\Settings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Modalità manutenzione "morbida":
 *  - i visitatori non loggati vedono una pagina di cortesia;
 *  - lo staff (utenti loggati) continua a navigare il sito e vede un banner.
 * L'area admin e il login restano sempre raggiungibili.
 */
class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Non bloccare mai l'area riservata, il login e il controllo di salute.
        if ($request->is('admin', 'admin/*', 'up')) {
            return $next($request);
        }

        $enabled = (bool) Settings::get('site.maintenance_enabled', false);

        if ($enabled && ! $request->user()) {
            return response()->view('maintenance', [
                'message' => Settings::get('site.maintenance_message'),
            ], 503);
        }

        return $next($request);
    }
}
