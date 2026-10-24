<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (function_exists('header_remove')) {
            @header_remove('X-Powered-By');
        }

        /** @var Response $response */
        $response = $next($request);

        if ($response->headers->has('X-Polr-Stateless-Redirect')) {
            $response->headers->remove('X-Polr-Stateless-Redirect');
            $response->headers->remove('Set-Cookie');
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Referrer-Policy', 'no-referrer');
        }

        $response->headers->remove('X-Powered-By');
        $response->headers->set('X-Frame-Options', 'DENY', false);
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', false);
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()', false);
        $response->headers->set(
            'Content-Security-Policy',
            "frame-ancestors 'none'; base-uri 'self'; object-src 'none'",
            false
        );

        $isHttps = $request->isSecure() || str_starts_with((string) env('APP_PROTOCOL', ''), 'https');
        if ($isHttps) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000', false);
        }

        return $response;
    }
}
