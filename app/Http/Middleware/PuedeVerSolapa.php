<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Deja pasar solo si el usuario tiene habilitada la solapa indicada.
 * Uso en rutas: ->middleware('solapa:operativos')
 */
class PuedeVerSolapa
{
    public function handle(Request $request, Closure $next, string $solapa): Response
    {
        if (! auth()->user()?->puedeVer($solapa)) {
            abort(403);
        }

        return $next($request);
    }
}
