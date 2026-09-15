<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self), payment=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-site');
        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if ($this->isAuthenticated()) {
            $response->headers->set('Cache-Control', 'no-store, private, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }

    private function isAuthenticated(): bool
    {
        return Auth::guard('admin')->check()
            || Auth::guard('staff')->check()
            || Auth::guard('customer')->check();
    }

    private function contentSecurityPolicy(): string
    {
        return implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            "frame-src https://www.google.com/recaptcha/ https://recaptcha.google.com/recaptcha/",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com https://www.google.com https://www.gstatic.com",
            "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://unpkg.com",
            "font-src 'self' data: https://cdnjs.cloudflare.com",
            "img-src 'self' data: blob: https://*.tile.openstreetmap.org https://www.google.com https://www.gstatic.com",
            "connect-src 'self' https://nominatim.openstreetmap.org https://*.tile.openstreetmap.org https://www.google.com",
            "worker-src 'self' blob: https://cdn.jsdelivr.net",
            app()->environment('production') ? 'upgrade-insecure-requests' : '',
        ]);
    }
}
