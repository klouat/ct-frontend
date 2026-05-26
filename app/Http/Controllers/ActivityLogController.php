<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ActivityLogController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $auth = $request->session()->get('svs_auth');
        $token = data_get($auth, 'access_token');

        if (! is_string($token) || $token === '') {
            return response()->json([
                'message' => 'Your session has expired. Please log in again.',
            ], 401);
        }

        $validated = $request->validate([
            'action' => ['required', 'string', 'max:100'],
            'table_name' => ['required', 'string', 'max:100'],
            'record_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $response = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(15)
                ->withToken($token)
                ->post($this->apiUrl('/api/audit-logs/activity'), $validated);
        } catch (ConnectionException) {
            return response()->json([
                'message' => 'Could not reach the SVS API at port 8000.',
            ], 503);
        }

        $body = $response->json();

        if ($response->status() === 401) {
            $request->session()->forget('svs_auth');
        }

        return response()->json([
            'message' => $this->extractMessage($body, $response->successful() ? 'Activity logged successfully' : 'Failed to log activity'),
            'data' => data_get($body, 'data'),
            'errors' => $body['errors'] ?? null,
        ], $response->status());
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
