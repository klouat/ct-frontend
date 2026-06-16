<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthCookieManager
{
    public const COOKIE_NAME = 'svs_remember_token';

    private const REMEMBER_MINUTES = 43200;

    public static function queueRememberCookie(Request $request, string $token): void
    {
        Cookie::queue(cookie(
            self::COOKIE_NAME,
            $token,
            self::REMEMBER_MINUTES,
            '/',
            config('session.domain'),
            self::isSecure($request),
            true,
            false,
            config('session.same_site', 'lax')
        ));
    }

    public static function queueForgetRememberCookie(): void
    {
        Cookie::queue(Cookie::forget(
            self::COOKIE_NAME,
            '/',
            config('session.domain')
        ));
    }

    private static function isSecure(Request $request): bool
    {
        $configured = config('session.secure');

        return is_bool($configured) ? $configured : $request->isSecure();
    }
}
