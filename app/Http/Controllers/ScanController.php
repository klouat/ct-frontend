<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScanController extends Controller
{
    public function scan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_text' => ['required', 'string'],
        ]);

        return $this->forwardRequest(
            $request,
            '/api/scan',
            [
                'qr_text' => $validated['qr_text'],
            ]
        );
    }

    public function markPending(Request $request, int $invoiceId): JsonResponse
    {
        return $this->forwardRequest($request, '/api/scan/invoices/'.$invoiceId.'/pending');
    }

    public function complete(Request $request, int $invoiceId): JsonResponse
    {
        return $this->forwardRequest($request, '/api/scan/invoices/'.$invoiceId.'/complete');
    }

    private function forwardRequest(Request $request, string $path, array $payload = []): JsonResponse
    {
        $auth = $request->session()->get('svs_auth');
        $token = data_get($auth, 'access_token');

        if (! is_string($token) || $token === '') {
            return response()->json([
                'message' => 'Your session has expired. Please log in again.',
            ], 401);
        }

        try {
            $response = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(20)
                ->withToken($token)
                ->post($this->apiUrl($path), $payload);
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
            'message' => $this->extractMessage($body, $response->successful() ? 'Request successful' : 'Request failed'),
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
