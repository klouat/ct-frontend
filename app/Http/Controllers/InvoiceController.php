<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InvoiceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vendor_name' => ['required', 'string', 'max:100'],
            'product_name' => ['required', 'string', 'max:150'],
            'box_count' => ['required', 'integer', 'min:1'],
            'arrival_date' => ['nullable', 'date'],
        ]);

        $auth = $request->session()->get('svs_auth');
        $token = data_get($auth, 'access_token');

        if (! is_string($token) || $token === '') {
            return response()->json([
                'message' => 'Your session has expired. Please log in again.',
            ], 401);
        }

        $invoiceId = 'INV-' . strtoupper(uniqid());
        $productId = 'PD-' . strtoupper(uniqid());

        $payload = [
            'invoice_code' => $invoiceId,
            'po_number' => $invoiceId,
            'vendor_name' => $validated['vendor_name'],
            'product_name' => $validated['product_name'],
            'product_id' => $productId,
            'target_box_count' => $validated['box_count'],
            'estimated_arrival_date' => $validated['arrival_date'] ?? null,
        ];

        try {
            $response = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(20)
                ->withToken($token)
                ->post($this->apiUrl('/api/invoices'), $payload);
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
                'message' => $this->extractMessage($body, 'Failed to create invoice'),
                'errors' => $body['errors'] ?? null,
            ], $response->status());
        }

        return response()->json([
            'message' => $this->extractMessage($body, 'Invoice created successfully'),
            'data' => data_get($body, 'data'),
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
