<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $auth = $request->session()->get('svs_auth');
        $token = data_get($auth, 'access_token');
        $userRole = (string) data_get($auth, 'user.role', '');
        $userVendorId = data_get($auth, 'user.vendor_id');

        if (! is_string($token) || $token === '') {
            return response()->json([
                'message' => 'Your session has expired. Please log in again.',
            ], 401);
        }

        $selectedDate = $request->filled('date')
            ? Carbon::parse((string) $request->query('date'))->toDateString()
            : now()->toDateString();

        $selectedVendorId = $request->filled('vendor_id')
            ? (int) $request->integer('vendor_id')
            : null;

        if ($userRole === 'VENDOR' && is_numeric($userVendorId)) {
            $selectedVendorId = (int) $userVendorId;
        }

        try {
            $boxes = $this->fetchBoxes($token, $selectedVendorId);
        } catch (RuntimeException $exception) {
            if ($exception->getMessage() === 'UNAUTHORIZED') {
                $request->session()->forget('svs_auth');

                return response()->json([
                    'message' => 'Your session has expired. Please log in again.',
                ], 401);
            }

            return response()->json([
                'message' => 'Failed to load dashboard data.',
            ], 500);
        } catch (ConnectionException) {
            return response()->json([
                'message' => 'Could not reach the SVS API at port 8000.',
            ], 503);
        }

        $statusCounts = [
            'less' => 0,
            'match' => 0,
            'mismatch' => 0,
            'pending' => 0,
            'over' => 0,
            'done' => 0,
            'unknown' => 0,
        ];

        $vendorBreakdown = [];
        $totalBoxes = 0;
        $targetBoxes = 0;
        $invoiceCount = 0;
        $selectedVendorName = null;
        $invoiceIds = [];
        $invoiceTargets = [];

        foreach ($boxes as $box) {
            $createdAt = (string) ($box['created_at'] ?? '');
            $boxDate = $createdAt !== ''
                ? Carbon::parse($createdAt)->toDateString()
                : null;

            if ($boxDate !== $selectedDate) {
                continue;
            }

            $invoiceId = isset($box['invoice_id']) ? (int) $box['invoice_id'] : null;

            if ($invoiceId === null) {
                continue;
            }

            $vendorName = (string) data_get($box, 'vendor.vendor_name', 'Unknown Vendor');

            if ($selectedVendorId !== null) {
                $selectedVendorName = $vendorName;
            }

            if (! isset($vendorBreakdown[$vendorName])) {
                $vendorBreakdown[$vendorName] = [
                    'vendor_name' => $vendorName,
                    'total_boxes' => 0,
                    'match' => 0,
                    'less' => 0,
                    'mismatch' => 0,
                    'pending' => 0,
                    'over' => 0,
                ];
            }

            $status = strtolower((string) ($box['status'] ?? 'unknown'));
            $normalizedStatus = array_key_exists($status, $statusCounts) ? $status : 'unknown';

            $statusCounts[$normalizedStatus]++;
            $vendorBreakdown[$vendorName]['total_boxes']++;
            $totalBoxes++;
            $invoiceIds[$invoiceId] = true;
            $invoiceTargets[$invoiceId] = (int) data_get($box, 'invoice.target_box_count', 0);

            if (isset($vendorBreakdown[$vendorName][$normalizedStatus])) {
                $vendorBreakdown[$vendorName][$normalizedStatus]++;
            }
        }

        $invoiceCount = count($invoiceIds);
        $targetBoxes = array_sum($invoiceTargets);

        return response()->json([
            'message' => 'Dashboard data loaded successfully',
            'data' => [
                'date' => $selectedDate,
                'vendor_id' => $selectedVendorId,
                'vendor_name' => $selectedVendorName,
                'invoice_count' => $invoiceCount,
                'total_boxes' => $totalBoxes,
                'target_boxes' => $targetBoxes,
                'status_counts' => $statusCounts,
                'vendor_breakdown' => array_values($vendorBreakdown),
            ],
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchBoxes(string $token, ?int $vendorId): array
    {
        $items = [];
        $page = 1;
        $lastPage = 1;

        do {
            $response = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(20)
                ->withToken($token)
                ->get($this->apiUrl('/api/boxes'), array_filter([
                    'per_page' => 100,
                    'page' => $page,
                    'vendor_id' => $vendorId,
                ], fn (mixed $value): bool => $value !== null));

            if ($response->status() === 401) {
                throw new RuntimeException('UNAUTHORIZED');
            }

            if (! $response->successful()) {
                throw new RuntimeException('FAILED_TO_LOAD_BOXES');
            }

            $body = $response->json();
            $pageItems = data_get($body, 'data.items', []);
            $items = array_merge($items, is_array($pageItems) ? $pageItems : []);
            $lastPage = (int) data_get($body, 'data.pagination.last_page', 1);
            $page++;
        } while ($page <= $lastPage && $page <= 10);

        return $items;
    }

    private function apiUrl(string $path): string
    {
        return rtrim((string) config('services.svs.base_url'), '/').$path;
    }
}
