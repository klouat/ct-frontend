<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class RestoreFrontendRememberedAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $sessionToken = data_get($request->session()->get('svs_auth'), 'access_token');

        if (is_string($sessionToken) && $sessionToken !== '') {
            return $next($request);
        }

        $rememberToken = $request->cookie(AuthCookieManager::COOKIE_NAME);

        if (! is_string($rememberToken) || $rememberToken === '') {
            return $next($request);
        }

        try {
            $response = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(15)
                ->withToken($rememberToken)
                ->post($this->apiUrl('/api/auth/refresh'));
        } catch (\Throwable) {
            Cookie::queue(Cookie::forget(AuthCookieManager::COOKIE_NAME));

            return $next($request);
        }

        $payload = $response->json();

        if (! $response->successful()) {
            Cookie::queue(Cookie::forget(AuthCookieManager::COOKIE_NAME));

            return $next($request);
        }

        $request->session()->put('svs_auth', [
            'access_token' => data_get($payload, 'data.access_token'),
            'token_type' => data_get($payload, 'data.token_type', 'bearer'),
            'user' => data_get($payload, 'data.user'),
        ]);

        AuthCookieManager::queueRememberCookie($request, (string) data_get($payload, 'data.access_token'));

        return $next($request);
    }

    private function apiUrl(string $path): string
    {
        return rtrim((string) config('services.svs.base_url'), '/').$path;
    }
}
