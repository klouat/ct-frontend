<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuthCookieManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $response = Http::acceptJson()
            ->withoutVerifying()
            ->timeout(15)
            ->post($this->apiUrl('/api/auth/login'), $validated);

        $payload = $response->json();

        if (! $response->successful()) {
            return response()->json([
                'message' => $this->errorMessage($payload, 'Login failed'),
                'errors' => $payload['errors'] ?? null,
            ], $response->status());
        }

        $request->session()->put('svs_auth', [
            'access_token' => data_get($payload, 'data.access_token'),
            'token_type' => data_get($payload, 'data.token_type', 'bearer'),
            'user' => data_get($payload, 'data.user'),
        ]);

        if ((bool) ($validated['remember'] ?? false)) {
            AuthCookieManager::queueRememberCookie($request, (string) data_get($payload, 'data.access_token'));
        } else {
            AuthCookieManager::queueForgetRememberCookie();
        }

        return response()->json([
            'message' => $this->extractMessage($payload, 'Login successful'),
            'redirect' => url($this->landingPageForRole((string) data_get($payload, 'data.user.role', ''))),
            'user' => data_get($payload, 'data.user'),
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Public registration is disabled.',
        ], 403);

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:ADMIN,SUPERVISOR,PETUGAS_GUDANG,VENDOR'],
            'vendor_id' => ['required_if:role,VENDOR', 'integer', 'nullable'],
        ]);

        $response = Http::acceptJson()
            ->withoutVerifying()
            ->timeout(15)
            ->post($this->apiUrl('/api/auth/register'), $validated);

        $payload = $response->json();

        if (! $response->successful()) {
            return response()->json([
                'message' => $this->errorMessage($payload, 'Registration failed'),
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
        AuthCookieManager::queueForgetRememberCookie();

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

    private function errorMessage(mixed $payload, string $fallback): string
    {
        $message = $this->extractMessage($payload, $fallback);

        if (in_array($message, ['Validation failed', 'Login failed', 'Registration failed'], true)) {
            $firstError = $this->firstValidationError($payload);

            if ($firstError !== null) {
                return $firstError;
            }
        }

        return $message;
    }

    private function firstValidationError(mixed $payload): ?string
    {
        $errors = data_get($payload, 'errors');

        if (! is_array($errors)) {
            return null;
        }

        foreach ($errors as $messages) {
            if (! is_array($messages)) {
                continue;
            }

            $firstMessage = Arr::first($messages, fn (mixed $value): bool => is_string($value) && trim($value) !== '');

            if (is_string($firstMessage) && $firstMessage !== '') {
                return $firstMessage;
            }
        }

        return null;
    }

    private function landingPageForRole(string $role): string
    {
        return match ($role) {
            'PETUGAS_GUDANG' => '/scan',
            'VENDOR' => '/invoice-barcodes',
            'ADMIN', 'SUPERVISOR' => '/home',
            default => '/login',
        };
    }

    public function publicVendors(): JsonResponse
    {
        try {
            $response = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(15)
                ->get($this->apiUrl('/api/public/vendors'));
        } catch (\Illuminate\Http\Client\ConnectionException) {
            return response()->json([
                'message' => 'Could not reach the SVS API.',
            ], 503);
        }

        if (! $response->successful()) {
            return response()->json(['message' => 'Failed to load vendors'], $response->status());
        }

        return response()->json($response->json());
    }
}
