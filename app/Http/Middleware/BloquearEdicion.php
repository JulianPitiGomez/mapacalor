<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Corta cualquier intento de escritura de un visualizador: los formularios de alta
 * y edición (rutas *.create / *.edit) y todo lo que no sea GET.
 * Las acciones de los componentes Livewire se cubren aparte con el trait
 * App\Livewire\Concerns\RequiereEdicion, porque pasan por /livewire/update.
 */
class BloquearEdicion
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = auth()->user();

        if ($usuario && ! $usuario->puedeEditar()) {
            $nombreRuta = $request->route()?->getName() ?? '';

            if (! $request->isMethodSafe() || str_ends_with($nombreRuta, '.create') || str_ends_with($nombreRuta, '.edit')) {
                abort(403);
            }
        }

        return $next($request);
    }
}
