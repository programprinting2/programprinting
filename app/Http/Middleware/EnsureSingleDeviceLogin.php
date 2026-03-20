<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleDeviceLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        $currentSessionId = (string) $request->session()->getId();
        $storedSessionId = (string) ($user->current_session_id ?? '');

        if ($storedSessionId === '') {
            return $next($request);
        }

        if (! hash_equals($storedSessionId, $currentSessionId)) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akun ini sedang digunakan di perangkat lain.',
                ], 403);
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Akun ini sedang digunakan di perangkat lain. Silakan login ulang.',
                ]);
        }

        return $next($request);
    }
}