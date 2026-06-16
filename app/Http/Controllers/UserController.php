<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function indexPage()
    {
        return view('dashboard.users');
    }

    public function createPage()
    {
        return view('dashboard.user-form', [
            'mode' => 'create',
            'userId' => null,
        ]);
    }

    public function editPage(int $id)
    {
        return view('dashboard.user-form', [
            'mode' => 'edit',
            'userId' => $id,
        ]);
    }

    public function showPage(int $id)
    {
        return view('dashboard.user-show', [
            'userId' => $id,
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        return $this->forwardGet($request, '/api/users', [
            'per_page' => $request->integer('per_page', 20),
            'page' => $request->integer('page', 1),
            'search' => $request->string('search')->trim()->value(),
            'role' => $request->string('role')->trim()->value(),
        ]);
    }

    public function details(Request $request, int $id): JsonResponse
    {
        return $this->forwardGet($request, '/api/users/'.$id);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:ADMIN,SUPERVISOR,PETUGAS_GUDANG,VENDOR'],
            'vendor_id' => ['required_if:role,VENDOR', 'nullable', 'integer'],
        ]);

        return $this->forwardRequest($request, 'POST', '/api/users', [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'vendor_id' => $validated['role'] === 'VENDOR' ? $validated['vendor_id'] : null,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:ADMIN,SUPERVISOR,PETUGAS_GUDANG,VENDOR'],
            'vendor_id' => ['required_if:role,VENDOR', 'nullable', 'integer'],
        ]);

        return $this->forwardRequest($request, 'PUT', '/api/users/'.$id, [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'] ?? null,
            'role' => $validated['role'],
            'vendor_id' => $validated['role'] === 'VENDOR' ? $validated['vendor_id'] : null,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        return $this->forwardRequest($request, 'DELETE', '/api/users/'.$id);
    }

    private function forwardGet(Request $request, string $path, array $query = []): JsonResponse
    {
        $token = $this->resolveToken($request);

        if ($token === null) {
            return response()->json(['message' => 'Your session has expired. Please log in again.'], 401);
        }

        try {
            $response = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(20)
                ->withToken($token)
                ->get($this->apiUrl($path), array_filter($query, fn ($value) => $value !== '' && $value !== null));
        } catch (ConnectionException) {
            return response()->json(['message' => 'Could not reach the SVS API at port 8000.'], 503);
        }

        $body = $response->json();
        $this->handleUnauthorized($request, $response->status());

        return response()->json([
            'message' => $this->extractMessage($body, $response->successful() ? 'Request successful' : 'Request failed'),
            'data' => data_get($body, 'data'),
            'errors' => $body['errors'] ?? null,
        ], $response->status());
    }

    private function forwardRequest(Request $request, string $method, string $path, array $payload = []): JsonResponse
    {
        $token = $this->resolveToken($request);

        if ($token === null) {
            return response()->json(['message' => 'Your session has expired. Please log in again.'], 401);
        }

        try {
            $response = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(20)
                ->withToken($token)
                ->{strtolower($method)}($this->apiUrl($path), $payload);
        } catch (ConnectionException) {
            return response()->json(['message' => 'Could not reach the SVS API at port 8000.'], 503);
        }

        $body = $response->json();
        $this->handleUnauthorized($request, $response->status());

        return response()->json([
            'message' => $this->extractMessage($body, $response->successful() ? 'Request successful' : 'Request failed'),
            'data' => data_get($body, 'data'),
            'errors' => $body['errors'] ?? null,
        ], $response->status());
    }

    private function resolveToken(Request $request): ?string
    {
        $token = data_get($request->session()->get('svs_auth'), 'access_token');

        return is_string($token) && $token !== '' ? $token : null;
    }

    private function handleUnauthorized(Request $request, int $status): void
    {
        if ($status === 401) {
            $request->session()->forget('svs_auth');
        }
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
