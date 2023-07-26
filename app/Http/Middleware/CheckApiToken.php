<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasHeader('token')) {
            return response()->json([], 404);
        }

        if ($request->header('token') != 111) {
            return response()->json([
                "message" => "token invalid"
            ]);
        }
        return $next($request);
    }
}
