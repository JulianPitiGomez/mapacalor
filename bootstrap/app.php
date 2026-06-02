<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'es_supervisor' => \App\Http\Middleware\EsSupervisor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Manejo de errores 419 (Token CSRF expirado / Sesión expirada)
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $exception, \Illuminate\Http\Request $request) {
            if ($response->getStatusCode() === 419) {
                // Si es una petición AJAX o Livewire
                if ($request->expectsJson() || $request->header('X-Livewire')) {
                    return response()->json([
                        'message' => 'Tu sesión ha expirado. Por favor, recarga la página.',
                        'redirect' => route('login')
                    ], 419);
                }

                // Redirigir a la página de sesión expirada
                return response()->view('errors.419', [], 419);
            }

            return $response;
        });
    })->create();
