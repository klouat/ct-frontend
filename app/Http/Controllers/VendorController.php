<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VendorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $auth = $request->session()->get('svs_auth');
        $token = data_get($auth, 'access_token');
        $userRole = data_get($auth, 'user.role');
        $userVendorId = data_get($auth, 'user.vendor_id');

        if (! is_string($token) || $token === '') {
            return response()->json([
                'message' => 'Your session has expired. Please log in again.',
            ], 401);
        }

        if ($userRole === 'VENDOR') {
            $vendorName = $this->resolveVendorNameForVendorUser($token, is_numeric($userVendorId) ? (int) $userVendorId : null);

            return response()->json([
                'message' => 'Vendor options loaded successfully',
                'data' => [
                    [
                        'vendor_id' => $userVendorId,
                        'vendor_name' => $vendorName,
                    ],
                ],
            ]);
        }

        try {
            $response = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(20)
                ->withToken($token)
                ->get($this->apiUrl('/api/vendors'), [
                    'per_page' => 100,
                ]);
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
                'message' => $this->extractMessage($body, 'Failed to load vendors'),
                'errors' => $body['errors'] ?? null,
            ], $response->status());
        }

        return response()->json([
            'message' => $this->extractMessage($body, 'Vendor options loaded successfully'),
            'data' => data_get($body, 'data.items', []),
        ]);
    }

    private function apiUrl(string $path): string
    {
        return rtrim((string) config('services.svs.base_url'), '/').$path;
    }

    private function resolveVendorNameForVendorUser(string $token, ?int $vendorId): string
    {
        if ($vendorId === null) {
            return 'Assigned Vendor';
        }

        try {
            $response = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(20)
                ->withToken($token)
                ->get($this->apiUrl('/api/boxes'), [
                    'vendor_id' => $vendorId,
                    'per_page' => 1,
                ]);

            if (! $response->successful()) {
                return 'Vendor ID '.$vendorId;
            }

            $vendorName = data_get($response->json(), 'data.items.0.vendor.vendor_name');

            return is_string($vendorName) && $vendorName !== ''
                ? $vendorName
                : 'Vendor ID '.$vendorId;
        } catch (ConnectionException) {
            return 'Vendor ID '.$vendorId;
        }
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
