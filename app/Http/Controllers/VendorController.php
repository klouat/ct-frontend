<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VendorController extends Controller
{
    public function list(Request $request): JsonResponse
    {
        return $this->forwardGet($request, '/api/vendors', [
            'per_page'    => $request->integer('per_page', 20),
            'vendor_name' => $request->string('vendor_name')->trim()->value(),
            'page'        => $request->integer('page', 1),
        ]);
    }

    public function index(Request $request): JsonResponse
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
                ->get($this->apiUrl('/api/vendors'), ['per_page' => 100]);
        } catch (ConnectionException) {
            return response()->json(['message' => 'Could not reach the SVS API at port 8000.'], 503);
        }

        $body = $response->json();
        $this->handleUnauthorized($request, $response->status());

        if (!$response->successful()) {
            return response()->json([
                'message' => $this->extractMessage($body, 'Failed to load vendors'),
                'errors'  => $body['errors'] ?? null,
            ], $response->status());
        }

        // Return a flat array so dashboard/invoice dropdowns work with Array.isArray(payload.data)
        return response()->json([
            'message' => $this->extractMessage($body, 'Vendor options loaded successfully'),
            'data'    => data_get($body, 'data.items', data_get($body, 'data', [])),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vendor_name' => ['required', 'string', 'max:150'],
        ]);

        return $this->forwardRequest($request, 'POST', '/api/vendors', [
            'vendor_name' => $validated['vendor_name'],
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'vendor_name' => ['required', 'string', 'max:150'],
        ]);

        return $this->forwardRequest($request, 'PUT', '/api/vendors/'.$id, [
            'vendor_name' => $validated['vendor_name'],
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        return $this->forwardRequest($request, 'DELETE', '/api/vendors/'.$id);
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
                ->get($this->apiUrl($path), array_filter($query, fn ($v) => $v !== '' && $v !== null));
        } catch (ConnectionException) {
            return response()->json(['message' => 'Could not reach the SVS API at port 8000.'], 503);
        }

        $body = $response->json();
        $this->handleUnauthorized($request, $response->status());

        return response()->json([
            'message' => $this->extractMessage($body, $response->successful() ? 'Request successful' : 'Request failed'),
            'data'    => data_get($body, 'data'),
            'errors'  => $body['errors'] ?? null,
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
            'data'    => data_get($body, 'data'),
            'errors'  => $body['errors'] ?? null,
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
