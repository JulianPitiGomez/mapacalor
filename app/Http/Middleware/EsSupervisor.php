<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsSupervisor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->user()?->es_supervisor) {
            abort(403);
        }

        return $next($request);
    }
}
