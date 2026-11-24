<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlayer
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'pemain') {
            abort(403, 'Akses hanya untuk pemain/penyewa.');
        }

        return $next($request);
    }
}
