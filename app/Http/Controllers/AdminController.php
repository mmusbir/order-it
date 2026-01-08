<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Assuming dompdf wrapper is used

class AdminController extends Controller
{
    /**
     * Monitor Requests (All requests except Drafts)
     */
    public function monitor(Request $request)
    {
        // Get departments for filter
        $departments = \App\Models\User::whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->filter();

        $query = RequestModel::with(['requester', 'items.product'])
            ->where('status', '!=', RequestModel::STATUS_DRAFT);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                    ->orWhereHas('requester', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply department filter
        if ($request->filled('department')) {
            $query->whereHas('requester', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        // Apply date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Apply priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Status counts for quick stats
        $statusCounts = [
            'pending' => RequestModel::whereIn('status', [
                RequestModel::STATUS_SUBMITTED,
                RequestModel::STATUS_APPROVED_1,
                RequestModel::STATUS_APPROVED_2,
                RequestModel::STATUS_APPROVED_3,
            ])->count(),
            'processing' => RequestModel::whereIn('status', [
                RequestModel::STATUS_APPROVED_4,
                RequestModel::STATUS_PO_ISSUED,
                RequestModel::STATUS_ON_DELIVERY,
            ])->count(),
            'completed' => RequestModel::where('status', RequestModel::STATUS_COMPLETED)->count(),
            'rejected' => RequestModel::where('status', RequestModel::STATUS_REJECTED)->count(),
        ];

        return view('admin.requests.monitor', compact('requests', 'departments', 'statusCounts'));
    }

    /**
     * Show Request Details for Admin
     */
    public function showRequest($id)
    {
        $request = RequestModel::with(['requester', 'items.product', 'approvers.user', 'approvalLogs.user'])->findOrFail($id);
        return view('admin.requests.show', compact('request'));
    }

    /**
     * Order Processing (Actionable Items)
     */
    public function fulfillment(Request $request)
    {
        $tab = $request->get('tab', 'po'); // po, sync, delivery, completed

        $query = RequestModel::with(['requester', 'items.product']);

        if ($tab === 'po') {
            $query->where('status', RequestModel::STATUS_APPROVED_4);
        } elseif ($tab === 'sync') {
            $query->where('status', RequestModel::STATUS_PO_ISSUED);
        } elseif ($tab === 'delivery') {
            $query->whereIn('status', [RequestModel::STATUS_SYNCED, RequestModel::STATUS_ON_DELIVERY]);
        } elseif ($tab === 'completed') {
            $query->where('status', RequestModel::STATUS_COMPLETED);
        } else {
            // Fallback (show all actionable/recent states)
            $query->whereIn('status', [
                RequestModel::STATUS_APPROVED_4,
                RequestModel::STATUS_PO_ISSUED,
                RequestModel::STATUS_SYNCED,
                RequestModel::STATUS_ON_DELIVERY,
                RequestModel::STATUS_COMPLETED
            ]);
        }

        $perPage = $request->input('per_page', 20);
        if (!in_array($perPage, [20, 25, 50, 100, 200, 500])) {
            $perPage = 20;
        }

        $requests = $query->orderBy('updated_at', 'desc')->paginate($perPage)->withQueryString();

        // Get counts for tabs
        $counts = [
            'po' => RequestModel::where('status', RequestModel::STATUS_APPROVED_4)->count(),
            'sync' => RequestModel::where('status', RequestModel::STATUS_PO_ISSUED)->count(),
            'delivery' => RequestModel::whereIn('status', [RequestModel::STATUS_SYNCED, RequestModel::STATUS_ON_DELIVERY])->count(),
            'completed' => RequestModel::where('status', RequestModel::STATUS_COMPLETED)->count(),
        ];

        return view('admin.requests.fulfillment', compact('requests', 'tab', 'counts', 'perPage'));
    }

    public function dashboard(Request $request)
    {
        // Stats
        $stats = [
            'ready_for_po' => RequestModel::where('status', RequestModel::STATUS_APPROVED_4)->count(),
            'on_delivery' => RequestModel::where('status', RequestModel::STATUS_ON_DELIVERY)->count(),
            'pending_approval' => RequestModel::whereIn('status', [
                RequestModel::STATUS_SUBMITTED,
                RequestModel::STATUS_APPROVED_1,
                RequestModel::STATUS_APPROVED_2,
                RequestModel::STATUS_APPROVED_3
            ])->count(),
            'total_this_month' => RequestModel::whereIn('status', [RequestModel::STATUS_COMPLETED, RequestModel::STATUS_SYNCED])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'growth_percentage' => 0,
        ];

        // Calculate growth percentage (comparing completed requests)
        $lastMonthTotal = RequestModel::whereIn('status', [RequestModel::STATUS_COMPLETED, RequestModel::STATUS_SYNCED])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        if ($lastMonthTotal > 0) {
            $stats['growth_percentage'] = round((($stats['total_this_month'] - $lastMonthTotal) / $lastMonthTotal) * 100);
        }

        // We can reuse the monitor query logic or just show recent requests with pagination
        $perPage = $request->input('per_page', 20);
        if (!in_array($perPage, [20, 50, 100, 200, 500])) {
            $perPage = 20;
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        // Validate sort parameters
        $allowedSorts = ['status', 'created_at', 'ticket_no'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        $requests = RequestModel::with(['requester', 'items.product'])
            ->where('status', '!=', RequestModel::STATUS_DRAFT)
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);

        return view('admin.dashboard', compact('stats', 'requests', 'sortBy', 'sortDir'));
    }

    public function generatePo(Request $request, $id)
    {
        $requestData = RequestModel::with('items.product')->findOrFail($id);

        // Ensure status is at least approved by director
        if (!in_array($requestData->status, [RequestModel::STATUS_APPROVED_4, RequestModel::STATUS_PO_ISSUED, RequestModel::STATUS_ON_DELIVERY, RequestModel::STATUS_COMPLETED])) {
            return redirect()->back()->with('error', 'Request not ready for PO.');
        }

        // If PO number not exists, require manual input
        if (!$requestData->po_number) {
            $validated = $request->validate([
                'po_number' => 'required|string|max:100|unique:requests,po_number',
            ]);

            $requestData->update([
                'po_number' => $validated['po_number'],
                'status' => RequestModel::STATUS_PO_ISSUED
            ]);

            return redirect()->back()->with('success', 'PO Number berhasil disimpan: ' . $validated['po_number']);
        }

        $pdf = Pdf::loadView('pdf.po', compact('requestData'));
        return $pdf->download('PO-' . $requestData->ticket_no . '.pdf');
    }

    public function updateDelivery(Request $request, $id)
    {
        $requestData = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'courier' => 'required',
            'tracking_no' => 'required',
            'serial_numbers' => 'required|array', // key: item_id, value: serial
        ]);

        foreach ($validated['serial_numbers'] as $itemId => $serial) {
            $item = $requestData->items()->find($itemId);
            if ($item) {
                $item->update(['serial_number' => $serial]);
            }
        }

        $requestData->update([
            'courier' => $validated['courier'],
            'tracking_no' => $validated['tracking_no'],
            'status' => RequestModel::STATUS_ON_DELIVERY
        ]);

        return redirect()->back()->with('success', 'Delivery updated.');
    }

    public function syncToSnipe($id, \App\Services\SnipeITService $snipeService)
    {
        $requestData = RequestModel::with(['items.product', 'requester'])->findOrFail($id);

        if (!in_array($requestData->status, [RequestModel::STATUS_PO_ISSUED, RequestModel::STATUS_ON_DELIVERY, RequestModel::STATUS_COMPLETED])) {
            return redirect()->back()->with('error', 'Request must have at least a PO Issued to sync.');
        }

        if ($requestData->request_type === 'NEW_CONSUMABLE') {
            return redirect()->back()->with('error', 'Consumable requests must be checked out individually.');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($requestData->items as $item) {
            if ($item->is_synced)
                continue;

            // 1. Find or default user
            $assignedUser = $snipeService->findUserByEmail($requestData->requester->email);
            $assignedToId = $assignedUser ? $assignedUser['id'] : null;

            // 2. Prepare Payload
            $payload = [
                'model_id' => $item->product->snipeit_model_id,
                'status_id' => 1, // Ready to Deploy (Default ID usually 1) typically 'Ready to Deploy'
                'asset_tag' => 'TAG-' . uniqid(), // Generate tag or use serial
                'serial' => $item->serial_number ?? 'N/A',
                'purchase_cost' => $item->snap_price,
                'purchase_date' => date('Y-m-d'),
                'order_number' => $requestData->po_number,
                'assigned_to' => $assignedToId,
                'checkout_to_type' => 'user',
            ];

            // 3. Post to Snipe
            $result = $snipeService->createAsset($payload);

            if ($result) {
                $item->update(['is_synced' => true]);
                $successCount++;
            } else {
                $failCount++;
            }
        }

        if ($successCount > 0 && $failCount === 0) {
            $requestData->update(['status' => RequestModel::STATUS_SYNCED]);
        }

        return redirect()->back()->with('success', "Synced: $successCount items. Failed: $failCount.");
    }

    /**
     * Update serial number, asset tag, and asset name for a single item
     */
    public function updateItemSerialTag(Request $request, $itemId)
    {
        $item = \App\Models\RequestItem::findOrFail($itemId);

        $validated = $request->validate([
            'serial_number' => 'required|string|max:255',
            'asset_tag' => 'required|string|max:255',
            'asset_name' => 'required|string|max:255',
        ]);

        $item->update($validated);

        return redirect()->back()->with('success', 'Serial number, asset tag, dan asset name berhasil disimpan.');
    }

    /**
     * Sync a single item to Snipe-IT
     */
    public function syncItemToSnipe($itemId)
    {
        $item = \App\Models\RequestItem::with('product', 'request')->findOrFail($itemId);

        if (!$item->serial_number || !$item->asset_tag || !$item->asset_name) {
            return redirect()->back()->with('error', 'Serial number, asset tag, dan asset name harus diisi terlebih dahulu.');
        }

        $enabled = \App\Models\AppSetting::getValue('snipeit_enabled', '0');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            return redirect()->back()->with('error', 'Snipe-IT integration tidak aktif.');
        }

        $url = \App\Models\AppSetting::getValue('snipeit_url');
        $token = \App\Models\AppSetting::getValue('snipeit_token');
        $statusId = \App\Models\AppSetting::getValue('snipeit_status_id', 2);

        if (!$url || !$token) {
            return redirect()->back()->with('error', 'Snipe-IT belum dikonfigurasi.');
        }

        try {
            // Get product category and model IDs
            $product = $item->product;
            $categoryId = $product->snipeit_category_id ?? null;
            $modelId = $product->snipeit_model_id ?? null;

            // Prepare asset data
            $assetData = [
                'name' => $item->asset_name,
                'serial' => $item->serial_number,
                'asset_tag' => $item->asset_tag,
                'status_id' => $statusId,
            ];

            // Add model if available
            if ($modelId) {
                $assetData['model_id'] = $modelId;
            }

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post(rtrim($url, '/') . '/api/v1/hardware', $assetData);

            if ($response->successful()) {
                $data = $response->json();
                $snipeitId = $data['payload']['id'] ?? null;

                $item->update([
                    'snipeit_asset_id' => $snipeitId,
                    'is_synced' => true,
                    'synced_at' => now(),
                ]);

                // Check if all items in the request are now synced
                $requestData = $item->request;
                $allSynced = $requestData->items()->where('is_synced', false)->count() === 0;

                if ($allSynced) {
                    $requestData->update(['status' => RequestModel::STATUS_SYNCED]);
                }

                return redirect()->back()->with('success', 'Asset berhasil di-sync ke Snipe-IT! ID: ' . $snipeitId);
            } else {
                $responseData = $response->json();
                $statusCode = $response->status();

                // Build detailed error message
                $errorDetails = [];
                $errorDetails[] = "Status Code: {$statusCode}";

                if (isset($responseData['status']) && $responseData['status'] === 'error') {
                    if (isset($responseData['messages'])) {
                        if (is_array($responseData['messages'])) {
                            foreach ($responseData['messages'] as $field => $messages) {
                                if (is_array($messages)) {
                                    $errorDetails[] = ucfirst($field) . ': ' . implode(', ', $messages);
                                } else {
                                    $errorDetails[] = ucfirst($field) . ': ' . $messages;
                                }
                            }
                        } else {
                            $errorDetails[] = $responseData['messages'];
                        }
                    }
                    if (isset($responseData['message'])) {
                        $errorDetails[] = $responseData['message'];
                    }
                } else {
                    $errorDetails[] = 'Response: ' . json_encode($responseData);
                }

                return redirect()->back()->with('error', 'Gagal sync ke Snipe-IT. ' . implode(' | ', $errorDetails));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error koneksi ke Snipe-IT: ' . $e->getMessage());
        }
    }

    /**
     * Update delivery info for the request
     */
    public function updateRequestDelivery(Request $request, $id)
    {
        $requestData = RequestModel::findOrFail($id);

        $validated = $request->validate([
            'courier' => 'required|string|max:255',
            'tracking_no' => 'required|string|max:255',
        ]);

        $requestData->update([
            'courier' => $validated['courier'],
            'tracking_no' => $validated['tracking_no'],
            'status' => RequestModel::STATUS_ON_DELIVERY,
        ]);

        // Notify Requester
        $requestData->requester->notify(new \App\Notifications\RequestActivityNotification(
            $requestData,
            $requestData->status, // effectively old status if we ignore the update in memory object? No, variable is updated? No, Eloquent update doesn't auto-refresh the object instance usually unless refresh() is called
            RequestModel::STATUS_ON_DELIVERY,
            'on_delivery'
        ));

        return redirect()->back()->with('success', 'Info pengiriman berhasil disimpan.');
    }

    // ==================== SNIPE-IT CONSUMABLE CHECKOUT ====================

    /**
     * Search consumables from Snipe-IT
     */
    public function searchSnipeITConsumables(Request $request)
    {
        $search = $request->get('q', '');

        $enabled = \App\Models\AppSetting::getValue('snipeit_enabled', '0');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            return response()->json(['error' => 'Snipe-IT not enabled'], 400);
        }

        $url = \App\Models\AppSetting::getValue('snipeit_url');
        $token = \App\Models\AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return response()->json(['error' => 'Snipe-IT not configured'], 400);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get(rtrim($url, '/') . '/api/v1/consumables', [
                        'search' => $search,
                        'limit' => 20,
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                $consumables = collect($data['rows'] ?? [])
                    ->filter(function ($item) {
                        // Filter out consumables with no remaining stock
                        return ($item['remaining'] ?? 0) > 0;
                    })
                    ->map(function ($item) {
                        return [
                            'id' => $item['id'],
                            'name' => $item['name'],
                            'qty' => $item['qty'] ?? 0,
                            'remaining' => $item['remaining'] ?? 0,
                            'category' => $item['category']['name'] ?? '-',
                        ];
                    })->values();
                return response()->json($consumables);
            }

            return response()->json(['error' => 'Failed to fetch consumables'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Search users from Snipe-IT
     */
    public function searchSnipeITUsers(Request $request)
    {
        $search = $request->get('q', '');

        $enabled = \App\Models\AppSetting::getValue('snipeit_enabled', '0');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            return response()->json(['error' => 'Snipe-IT not enabled'], 400);
        }

        $url = \App\Models\AppSetting::getValue('snipeit_url');
        $token = \App\Models\AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return response()->json(['error' => 'Snipe-IT not configured'], 400);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get(rtrim($url, '/') . '/api/v1/users', [
                        'search' => $search,
                        'limit' => 100, // Get more results to filter
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                $users = collect($data['rows'] ?? [])->filter(function ($item) {
                    // Filter users by department 'All Branch'
                    $department = $item['department']['name'] ?? '';
                    return $department === 'All Branch';
                })->map(function ($item) {
                    return [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'username' => $item['username'] ?? '',
                        'email' => $item['email'] ?? '',
                        'employee_num' => $item['employee_num'] ?? '',
                        'department' => $item['department']['name'] ?? '',
                    ];
                })->take(20)->values();
                return response()->json($users);
            }

            return response()->json(['error' => 'Failed to fetch users'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Search locations from Snipe-IT
     */
    public function searchSnipeITLocations(Request $request)
    {
        $search = $request->get('q', '');

        $enabled = \App\Models\AppSetting::getValue('snipeit_enabled', '0');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            return response()->json(['error' => 'Snipe-IT not enabled'], 400);
        }

        $url = \App\Models\AppSetting::getValue('snipeit_url');
        $token = \App\Models\AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return response()->json(['error' => 'Snipe-IT not configured'], 400);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get(rtrim($url, '/') . '/api/v1/locations', [
                        'search' => $search,
                        'limit' => 20,
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                $locations = collect($data['rows'] ?? [])->map(function ($item) {
                    return [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'city' => $item['city'] ?? '',
                    ];
                });
                return response()->json($locations);
            }

            return response()->json(['error' => 'Failed to fetch locations'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Checkout consumable to user in Snipe-IT
     */
    public function checkoutConsumableToSnipe(Request $request, $itemId)
    {
        $item = \App\Models\RequestItem::with('request')->findOrFail($itemId);

        $validated = $request->validate([
            'consumable_id' => 'required|integer',
            'assigned_to' => 'required|integer',
            'checkout_qty' => 'nullable|integer|min:1',
            'note' => 'nullable|string|max:500',
            'consumable_name' => 'nullable|string',
            'user_name' => 'nullable|string',
        ]);

        $enabled = \App\Models\AppSetting::getValue('snipeit_enabled', '0');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            \Illuminate\Support\Facades\Log::warning('Consumable checkout failed: Snipe-IT integration disabled');
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Snipe-IT integration tidak aktif.'], 400);
            }
            return redirect()->back()->with('error', 'Snipe-IT integration tidak aktif.');
        }

        $url = \App\Models\AppSetting::getValue('snipeit_url');
        $token = \App\Models\AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            \Illuminate\Support\Facades\Log::warning('Consumable checkout failed: Snipe-IT not configured');
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Snipe-IT belum dikonfigurasi.'], 400);
            }
            return redirect()->back()->with('error', 'Snipe-IT belum dikonfigurasi.');
        }

        try {
            // Snipe-IT consumables can only be checked out to users
            $checkoutData = [
                'assigned_to' => $validated['assigned_to'],
            ];

            if (!empty($validated['checkout_qty'])) {
                $checkoutData['qty'] = $validated['checkout_qty'];
            }

            if (!empty($validated['note'])) {
                $checkoutData['note'] = $validated['note'];
            }

            $apiUrl = rtrim($url, '/') . '/api/v1/consumables/' . $validated['consumable_id'] . '/checkout';

            \Illuminate\Support\Facades\Log::info('Consumable checkout attempt', [
                'item_id' => $itemId,
                'api_url' => $apiUrl,
                'checkout_data' => $checkoutData,
            ]);

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post($apiUrl, $checkoutData);

            \Illuminate\Support\Facades\Log::info('Consumable checkout response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $item->update([
                    'is_synced' => true,
                    'synced_at' => now(),
                    'snipeit_asset_id' => 'CONSUMABLE-' . $validated['consumable_id'],
                    'synced_item_name' => $validated['consumable_name'] ?? ('Consumable #' . $validated['consumable_id']),
                    'synced_location_name' => $validated['user_name'] ?? ('User #' . $validated['assigned_to']),
                    'synced_qty' => $validated['checkout_qty'] ?? 1,
                ]);

                // Check if all items in the request are now synced
                $requestData = $item->request;
                $allSynced = $requestData->items()->where('is_synced', false)->count() === 0;

                if ($allSynced) {
                    $requestData->update(['status' => RequestModel::STATUS_SYNCED]);
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Consumable berhasil di-checkout!' . ($allSynced ? ' Request status updated to SYNCED.' : '')
                    ]);
                }
                return redirect()->back()->with('success', 'Consumable berhasil di-checkout ke user di Snipe-IT!');
            } else {
                $responseData = $response->json();
                $errorMsg = $responseData['messages'] ?? $responseData['message'] ?? 'Unknown error';
                if (is_array($errorMsg)) {
                    $errorMsg = json_encode($errorMsg);
                }

                \Illuminate\Support\Facades\Log::error('Consumable checkout failed', [
                    'status' => $response->status(),
                    'error' => $errorMsg,
                ]);

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Gagal checkout: ' . $errorMsg], 400);
                }
                return redirect()->back()->with('error', 'Gagal checkout: ' . $errorMsg);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Consumable checkout exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error koneksi: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error koneksi ke Snipe-IT: ' . $e->getMessage());
        }
    }

    // ==================== CONSUMABLE MANAGEMENT ====================

    /**
     * List consumables from Snipe-IT
     */
    /**
     * List consumables from Snipe-IT
     */
    public function consumables(Request $request)
    {
        $enabled = \App\Models\AppSetting::getValue('snipeit_enabled', '0');
        $url = \App\Models\AppSetting::getValue('snipeit_url');
        $token = \App\Models\AppSetting::getValue('snipeit_token');

        $consumables = [];
        $error = null;
        $cacheKey = 'consumables_data';

        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            $error = 'Snipe-IT integration tidak aktif. Silakan hubungi Administrator.';
            // Try to load cached data
            $consumables = cache()->get($cacheKey, []);
        } elseif (!$url || !$token) {
            $error = 'Snipe-IT belum dikonfigurasi. Silakan hubungi Administrator.';
            $consumables = cache()->get($cacheKey, []);
        } else {
            try {
                $search = $request->get('search', '');
                $response = \Illuminate\Support\Facades\Http::timeout(10)->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ])->get(rtrim($url, '/') . '/api/v1/consumables', [
                            'search' => $search,
                            'limit' => 100,
                        ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $consumables = $data['rows'] ?? [];

                    // Cache the data for 24 hours (only cache when no search filter)
                    if (empty($search)) {
                        cache()->put($cacheKey, $consumables, now()->addHours(24));
                    }
                } else {
                    $error = 'Gagal mengambil data dari Snipe-IT. Status: ' . $response->status();
                    // Load cached data on failure
                    $cachedData = cache()->get($cacheKey, []);
                    if (!empty($cachedData)) {
                        $consumables = $cachedData;
                        $error = 'Snipe-IT tidak terhubung. Menampilkan data cache terakhir.';
                    }
                }
            } catch (\Exception $e) {
                // Load cached data on connection failure
                $cachedData = cache()->get($cacheKey, []);
                if (!empty($cachedData)) {
                    $consumables = $cachedData;
                    $error = 'Snipe-IT tidak terhubung. Menampilkan data cache terakhir.';
                } else {
                    $error = 'Snipe-IT tidak terhubung dan tidak ada data cache tersedia: ' . $e->getMessage();
                }
            }
        }

        return view('admin.consumables.index', compact('consumables', 'error'));
    }

    /**
     * Delete a single consumable from Snipe-IT
     */
    public function deleteConsumable($id)
    {
        $url = \App\Models\AppSetting::getValue('snipeit_url');
        $token = \App\Models\AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return redirect()->back()->with('error', 'Snipe-IT belum dikonfigurasi.');
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->delete(rtrim($url, '/') . '/api/v1/consumables/' . $id);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Consumable berhasil dihapus dari Snipe-IT.');
            } else {
                $data = $response->json();
                $msg = $data['messages'] ?? $data['message'] ?? 'Unknown error';
                return redirect()->back()->with('error', 'Gagal menghapus: ' . (is_array($msg) ? json_encode($msg) : $msg));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ==================== USER MANAGEMENT ====================

    /**
     * List all users
     */
    public function users(Request $request)
    {
        $query = \App\Models\User::with(['jobTitle', 'approvalRole']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Sorting
        $sortBy = $request->get('sort', 'name');
        $sortDir = $request->get('dir', 'asc');
        $allowedSorts = ['name', 'email', 'employee_number', 'role', 'department', 'job_title_id', 'approval_role_id'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('name', 'asc');
        }

        // Per Page
        $perPage = (int) $request->get('per_page', 20);
        $allowedPerPage = [20, 50, 100, 200, 500];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 20;
        }

        $users = $query->paginate($perPage);
        $roles = \App\Models\Role::active()->orderBy('name')->get();

        // Pass 'admin' prefix to reuse or adapt views
        return view('admin.users.index', compact('users', 'roles', 'sortBy', 'sortDir', 'perPage'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        $roles = \App\Models\Role::active()->orderBy('name')->get();
        $departments = \App\Models\Department::where('is_active', true)->orderBy('name')->get();
        $approvalRoles = \App\Models\ApprovalRole::where('is_active', true)->orderBy('name')->get();
        $jobTitles = \App\Models\JobTitle::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'departments', 'approvalRoles', 'jobTitles'));
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        // Get valid role slugs from the roles table
        $validRoles = \App\Models\Role::active()->pluck('slug')->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'employee_number' => 'required|string|max:50',
            'password' => 'required|min:8|confirmed',
            'role' => ['required', \Illuminate\Validation\Rule::in($validRoles)],
            'department' => 'required|string|max:255',
            'approval_role_id' => 'nullable|exists:approval_roles,id',
            'job_title_id' => 'nullable|exists:job_titles,id',
        ]);

        // Prevent creating Superadmin
        if ($validated['role'] === 'superadmin') {
            return back()->with('error', 'You are not authorized to create a Superadmin user.');
        }

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'employee_number' => $validated['employee_number'] ?? null,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => $validated['role'],
            'department' => $validated['department'] ?? null,
            'approval_role_id' => $validated['approval_role_id'] ?? null,
            'job_title_id' => $validated['job_title_id'] ?? null,
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    /**
     * Show edit user form
     */
    public function editUser($id)
    {
        $user = \App\Models\User::findOrFail($id);

        // Security Check: Cannot edit Superadmin
        if ($user->role === 'superadmin') {
            return redirect()->route('admin.users')->with('error', 'You are not authorized to edit a Superadmin user.');
        }

        $roles = \App\Models\Role::active()->orderBy('name')->get();
        $departments = \App\Models\Department::where('is_active', true)->orderBy('name')->get();
        $approvalRoles = \App\Models\ApprovalRole::where('is_active', true)->orderBy('name')->get();
        $jobTitles = \App\Models\JobTitle::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles', 'departments', 'approvalRoles', 'jobTitles'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        // Security Check: Cannot edit Superadmin
        if ($user->role === 'superadmin') {
            return redirect()->route('admin.users')->with('error', 'You are not authorized to edit a Superadmin user.');
        }

        // Get valid role slugs from the roles table
        $validRoles = \App\Models\Role::active()->pluck('slug')->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'employee_number' => 'required|string|max:50',
            'password' => 'nullable|min:8|confirmed',
            'role' => ['required', \Illuminate\Validation\Rule::in($validRoles)],
            'department' => 'required|string|max:255',
            'approval_role_id' => 'nullable|exists:approval_roles,id',
            'job_title_id' => 'nullable|exists:job_titles,id',
        ]);

        // Prevent elevating to Superadmin
        if ($validated['role'] === 'superadmin') {
            return back()->with('error', 'You cannot promote a user to Superadmin.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->employee_number = $validated['employee_number'] ?? null;
        $user->role = $validated['role'];
        $user->department = $validated['department'] ?? null;
        $user->approval_role_id = $validated['approval_role_id'] ?? null;
        $user->job_title_id = $validated['job_title_id'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function destroyUser($id)
    {
        $user = \App\Models\User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        // Security Check: Cannot delete Superadmin
        if ($user->role === 'superadmin') {
            return back()->with('error', 'You are not authorized to delete a Superadmin user.');
        }

        // Check if user has related requests
        $requestCount = RequestModel::where('requester_id', $user->id)->count();
        if ($requestCount > 0) {
            return back()->with('error', "Cannot delete user. User has {$requestCount} related request(s). Please reassign or delete the requests first.");
        }

        // Delete related records
        \App\Models\ApprovalLog::where('user_id', $user->id)->delete();
        \App\Models\DepartmentApprovalLevel::where('user_id', $user->id)->delete();
        \App\Models\ActivityLog::where('user_id', $user->id)->delete();

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }


    /**
     * Bulk delete consumables from Snipe-IT
     */
    public function bulkDeleteConsumables(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih.');
        }

        $url = \App\Models\AppSetting::getValue('snipeit_url');
        $token = \App\Models\AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return redirect()->back()->with('error', 'Snipe-IT belum dikonfigurasi.');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($ids as $id) {
            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ])->delete(rtrim($url, '/') . '/api/v1/consumables/' . $id);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            } catch (\Exception $e) {
                $failCount++;
            }
        }

        return redirect()->back()->with('success', "Berhasil menghapus {$successCount} item. Gagal: {$failCount}.");
    }

    /**
     * Sync/refresh consumables from Snipe-IT
     */
    public function syncConsumables()
    {
        return redirect()->route('admin.consumables')->with('success', 'Data consumables berhasil di-refresh dari Snipe-IT.');
    }
}
