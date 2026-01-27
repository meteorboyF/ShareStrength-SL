<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        $user = $request->user();
        if ($user instanceof \App\Models\Admin) {
            return $next($request);
        }

        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);

    }
}
