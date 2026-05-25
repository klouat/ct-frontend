<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $response = Http::acceptJson()
            ->withoutVerifying()
            ->timeout(15)
            ->post($this->apiUrl('/api/auth/login'), $validated);

        $payload = $response->json();

        if (! $response->successful()) {
            return response()->json([
                'message' => $this->extractMessage($payload, 'Login failed'),
                'errors' => $payload['errors'] ?? null,
            ], $response->status());
        }

        $request->session()->put('svs_auth', [
            'access_token' => data_get($payload, 'data.access_token'),
            'token_type' => data_get($payload, 'data.token_type', 'bearer'),
            'user' => data_get($payload, 'data.user'),
        ]);

        return response()->json([
            'message' => $this->extractMessage($payload, 'Login successful'),
            'redirect' => url('/invoice'),
            'user' => data_get($payload, 'data.user'),
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:ADMIN,OPERATOR,VENDOR,DRIVER'],
            'vendor_id' => ['nullable', 'integer'],
        ]);

        $response = Http::acceptJson()
            ->withoutVerifying()
            ->timeout(15)
            ->post($this->apiUrl('/api/auth/register'), $validated);

        $payload = $response->json();

        if (! $response->successful()) {
            return response()->json([
                'message' => $this->extractMessage($payload, 'Registration failed'),
                'errors' => $payload['errors'] ?? null,
            ], $response->status());
        }

        return response()->json([
            'message' => $this->extractMessage($payload, 'Registration successful'),
            'redirect' => url('/login'),
        ], $response->status());
    }

    public function logout(Request $request): RedirectResponse
    {
        $auth = $request->session()->get('svs_auth');
        $token = data_get($auth, 'access_token');

        if (is_string($token) && $token !== '') {
            Http::acceptJson()
                ->withoutVerifying()
                ->timeout(15)
                ->withToken($token)
                ->post($this->apiUrl('/api/auth/logout'));
        }

        $request->session()->forget('svs_auth');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function apiUrl(string $path): string
    {
        return rtrim((string) config('services.svs.base_url'), '/').$path;
    }

    private function extractMessage(mixed $payload, string $fallback): string
    {
        $message = data_get($payload, 'message')
            ?? data_get($payload, 'error')
            ?? data_get($payload, 'detail.0.msg')
            ?? data_get($payload, 'detail');

        return is_string($message) && $message !== '' ? $message : $fallback;
    }
}
