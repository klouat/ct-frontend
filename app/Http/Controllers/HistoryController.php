<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HistoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $auth = $request->session()->get('svs_auth');
        $token = data_get($auth, 'access_token');

        if (! is_string($token) || $token === '') {
            return response()->json([
                'message' => 'Your session has expired. Please log in again.',
            ], 401);
        }

        $query = array_filter([
            'search' => trim((string) $request->query('search', '')),
            'per_page' => $request->integer('per_page', 20),
        ], fn (mixed $value): bool => $value !== '' && $value !== null);

        try {
            $response = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(20)
                ->withToken($token)
                ->get($this->apiUrl('/api/history'), $query);
        } catch (ConnectionException) {
            return response()->json([
                'message' => 'Could not reach the SVS API at port 8000.',
            ], 503);
        }

        $body = $response->json();

        if ($response->status() === 401) {
            $request->session()->forget('svs_auth');
        }

        if (! $response->successful()) {
            return response()->json([
                'message' => $this->extractMessage($body, 'Failed to load history'),
                'errors' => $body['errors'] ?? null,
            ], $response->status());
        }

        return response()->json([
            'message' => $this->extractMessage($body, 'History loaded successfully'),
            'data' => data_get($body, 'data'),
        ]);
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
