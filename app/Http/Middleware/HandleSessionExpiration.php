<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class HandleSessionExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Si la sesión ha expirado y es una solicitud AJAX/Livewire
        if ($response->getStatusCode() === 419) {
            if ($request->expectsJson() || $request->header('X-Livewire')) {
                return response()->json([
                    'message' => 'Tu sesión ha expirado. Por favor, recarga la página.',
                    'redirect' => route('login')
                ], 419);
            }
        }

        return $response;
    }
}
