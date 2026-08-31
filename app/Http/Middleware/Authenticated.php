<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticated
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->session()->has('user')) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.',
        ], 401);
    }
}
