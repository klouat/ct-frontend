<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFrontendAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $auth = $request->session()->get('svs_auth');
        $token = data_get($auth, 'access_token');
        $role = data_get($auth, 'user.role');

        if (! is_string($token) || $token === '') {
            return redirect('/login');
        }

        if ($roles !== [] && (! is_string($role) || ! in_array($role, $roles, true))) {
            abort(403, 'You are not allowed to access this page.');
        }

        return $next($request);
    }
}
