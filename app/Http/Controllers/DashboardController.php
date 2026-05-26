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
            $records = $this->fetchHistoryRecords($token, $selectedVendorId);
            $this->logActivity($token, [
                'action' => 'VIEW_DASHBOARD_DATA',
                'table_name' => 'dashboard',
                'description' => sprintf(
                    'Viewed dashboard data for date %s%s',
                    $selectedDate,
                    $selectedVendorId ? ' and vendor '.$selectedVendorId : ''
                ),
            ]);
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
        ];

        $vendorBreakdown = [];
        $totalBoxes = 0;
        $targetBoxes = 0;
        $invoiceCount = 0;
        $selectedVendorName = null;
        
        foreach ($records as $record) {
            $activityAt = (string) ($record['last_scanned_at'] ?? $record['created_at'] ?? $record['recorded_at'] ?? '');
            $boxDate = $activityAt !== ''
                ? Carbon::parse($activityAt)->toDateString()
                : null;

            if ($boxDate !== $selectedDate) {
                continue;
            }

            $invoiceId = isset($record['invoice_id']) ? (int) $record['invoice_id'] : null;

            if ($invoiceId === null) {
                continue;
            }

            $vendorName = (string) ($record['vendor_name'] ?? 'Unknown Vendor');
            $boxQuantity = (int) ($record['box_quantity'] ?? $record['quantity'] ?? 0);

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

            $recordCounts = $this->extractStatusCounts($record);

            $statusCounts['match'] += $recordCounts['match'];
            $statusCounts['less'] += $recordCounts['less'];
            $statusCounts['mismatch'] += $recordCounts['mismatch'];
            $statusCounts['pending'] += $recordCounts['pending'];
            $statusCounts['over'] += $recordCounts['over'];
            $vendorBreakdown[$vendorName]['total_boxes'] += $boxQuantity;
            $totalBoxes += $boxQuantity;
            $targetBoxes += $boxQuantity;
            $invoiceCount++;

            $vendorBreakdown[$vendorName]['match'] += $recordCounts['match'];
            $vendorBreakdown[$vendorName]['less'] += $recordCounts['less'];
            $vendorBreakdown[$vendorName]['mismatch'] += $recordCounts['mismatch'];
            $vendorBreakdown[$vendorName]['pending'] += $recordCounts['pending'];
            $vendorBreakdown[$vendorName]['over'] += $recordCounts['over'];
        }

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
    private function fetchHistoryRecords(string $token, ?int $vendorId): array
    {
        $items = [];
        $page = 1;
        $lastPage = 1;

        do {
            $response = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(20)
                ->withToken($token)
                ->get($this->apiUrl('/api/history'), array_filter([
                    'per_page' => 100,
                    'page' => $page,
                    'vendor_id' => $vendorId,
                ], fn (mixed $value): bool => $value !== null));

            if ($response->status() === 401) {
                throw new RuntimeException('UNAUTHORIZED');
            }

            if (! $response->successful()) {
                throw new RuntimeException('FAILED_TO_LOAD_HISTORY');
            }

            $body = $response->json();
            $pageItems = data_get($body, 'data.items', []);
            $items = array_merge($items, is_array($pageItems) ? $pageItems : []);
            $lastPage = (int) data_get($body, 'data.pagination.last_page', 1);
            $page++;
        } while ($page <= $lastPage && $page <= 10);

        return $items;
    }

    private function extractStatusCounts(array $record): array
    {
        $counts = [
            'match' => max((int) ($record['match_box_count'] ?? 0), 0),
            'less' => max((int) ($record['less_box_count'] ?? 0), 0),
            'mismatch' => max((int) ($record['mismatch_box_count'] ?? 0), 0),
            'pending' => max((int) ($record['pending_box_count'] ?? 0), 0),
            'over' => max((int) ($record['over_box_count'] ?? 0), 0),
        ];

        if (array_sum($counts) > 0) {
            return $counts;
        }

        $boxQuantity = max((int) ($record['box_quantity'] ?? $record['quantity'] ?? 0), 0);
        $status = strtolower(trim((string) ($record['status'] ?? '')));

        return match ($status) {
            'less' => ['match' => 0, 'less' => $boxQuantity, 'mismatch' => 0, 'pending' => 0, 'over' => 0],
            'match', 'done', 'terverifikasi' => ['match' => $boxQuantity, 'less' => 0, 'mismatch' => 0, 'pending' => 0, 'over' => 0],
            'mismatch' => ['match' => 0, 'less' => 0, 'mismatch' => $boxQuantity, 'pending' => 0, 'over' => 0],
            'over' => ['match' => 0, 'less' => 0, 'mismatch' => 0, 'pending' => 0, 'over' => $boxQuantity],
            'pending', 'not_scanned', 'on_progress' => ['match' => 0, 'less' => 0, 'mismatch' => 0, 'pending' => $boxQuantity, 'over' => 0],
            default => ['match' => 0, 'less' => 0, 'mismatch' => 0, 'pending' => 0, 'over' => 0],
        };
    }

    private function apiUrl(string $path): string
    {
        return rtrim((string) config('services.svs.base_url'), '/').$path;
    }

    private function logActivity(string $token, array $payload): void
    {
        try {
            Http::acceptJson()
                ->withoutVerifying()
                ->timeout(10)
                ->withToken($token)
                ->post($this->apiUrl('/api/audit-logs/activity'), $payload);
        } catch (\Throwable) {
            // Ignore audit logging failures for read operations.
        }
    }
}
