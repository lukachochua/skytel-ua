<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->cookie('accessToken');

            if (!$token) {
                Log::info('No access token found, redirecting to login');
                return redirect()->route('login');
            }

            $tokenParts = explode('.', $token);
            if (count($tokenParts) !== 3) {
                Log::error('Malformed token structure');
                return redirect()->route('login')
                    ->withCookie(cookie()->forget('accessToken'))
                    ->withCookie(cookie()->forget('refreshToken'));
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Token validation failed', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('login')
                ->withCookie(cookie()->forget('accessToken'))
                ->withCookie(cookie()->forget('refreshToken'));
        }
    }
}
