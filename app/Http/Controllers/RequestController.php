<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
use App\Models\Product;
use App\Models\Branch;
use App\Models\RequestItem;
use App\Models\ApprovalLog;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource (Requester).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $role = $user->role;

        // Check if user is assigned as an approver in any request_approvers
        $isApprover = \App\Models\RequestApprover::where('user_id', $userId)
            ->where('status', 'pending')
            ->exists();

        // Also check if user is mapped as approver in any approval_role_levels
        $isMappedApprover = \App\Models\ApprovalRoleLevel::where('user_id', $userId)
            ->where('is_active', true)
            ->exists();

        // Approver Dashboard - show approver-specific dashboard with stats
        if ($isApprover || $isMappedApprover || $role === 'approver') {
            // Get approver's level info
            $approverLevel = \App\Models\ApprovalRoleLevel::where('user_id', $userId)
                ->where('is_active', true)
                ->first();

            // Level to status mapping (same as approvals method)
            $levelToStatus = [
                1 => RequestModel::STATUS_SUBMITTED,
                2 => RequestModel::STATUS_APPROVED_1,
                3 => RequestModel::STATUS_APPROVED_2,
                4 => RequestModel::STATUS_APPROVED_3,
            ];

            // Pending approvals - requests where user is approver AND request is at their level
            $pendingQuery = RequestModel::whereHas('approvers', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->where('status', 'pending');
            })
                ->where(function ($q) use ($userId, $levelToStatus) {
                    foreach ($levelToStatus as $level => $requiredStatus) {
                        $q->orWhere(function ($subQ) use ($userId, $level, $requiredStatus) {
                            $subQ->whereHas('approvers', function ($approverQ) use ($userId, $level) {
                                $approverQ->where('user_id', $userId)
                                    ->where('level', $level)
                                    ->where('status', 'pending');
                            })->where('status', $requiredStatus);
                        });
                    }
                });

            $pendingApprovals = $pendingQuery->count();

            // Pending requests list
            $pendingRequests = (clone $pendingQuery)
                ->with(['requester', 'items.product'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Total approved by this user
            $totalApproved = ApprovalLog::where('user_id', $userId)
                ->where('action', 'APPROVE')
                ->count();

            // Total rejected by this user
            $totalRejected = ApprovalLog::where('user_id', $userId)
                ->where('action', 'REJECT')
                ->count();

            // Recent approval activity with pagination - only APPROVE and REJECT actions
            $activityPerPage = (int) $request->input('activity_per_page', 10);
            if (!in_array($activityPerPage, [10, 25, 50, 100])) {
                $activityPerPage = 10;
            }

            $recentActivity = ApprovalLog::where('user_id', $userId)
                ->whereIn('action', ['APPROVE', 'REJECT'])
                ->with('request')
                ->orderBy('created_at', 'desc')
                ->paginate($activityPerPage)
                ->withQueryString();

            return view('dashboard-approver', [
                'approverLevel' => $approverLevel,
                'pendingApprovals' => $pendingApprovals,
                'totalApproved' => $totalApproved,
                'totalRejected' => $totalRejected,
                'recentActivity' => $recentActivity,
                'pendingRequests' => $pendingRequests,
            ]);

        }

        // Requester Logic
        $user = Auth::user();
        $userId = $user->id;

        // Stats Logic (Keep as is, but maybe optimize later)
        $stats = [
            'total' => RequestModel::where('requester_id', $userId)->count(),
            'pending' => RequestModel::where('requester_id', $userId)->whereIn('status', [
                RequestModel::STATUS_SUBMITTED,
                RequestModel::STATUS_APPROVED_1,
                RequestModel::STATUS_APPROVED_2,
                RequestModel::STATUS_APPROVED_3,
            ])->count(),
            'approved' => RequestModel::where('requester_id', $userId)->whereIn('status', [
                RequestModel::STATUS_APPROVED_4,
                RequestModel::STATUS_PO_ISSUED,
                RequestModel::STATUS_ON_DELIVERY,
            ])->count(),
            'po_issued' => RequestModel::where('requester_id', $userId)->where('status', RequestModel::STATUS_PO_ISSUED)->count(),
            'delivery' => RequestModel::where('requester_id', $userId)->where('status', RequestModel::STATUS_ON_DELIVERY)->count(),
            'completed' => RequestModel::where('requester_id', $userId)->where('status', RequestModel::STATUS_COMPLETED)->count(),
            'rejected' => RequestModel::where('requester_id', $userId)->where('status', RequestModel::STATUS_REJECTED)->count(),
        ];


        // Table Query
        $query = RequestModel::where('requester_id', $userId);

        // 1. Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($iq) use ($search) {
                        $iq->where('item_name', 'like', "%{$search}%")
                            ->orWhereHas('product', function ($pq) use ($search) {
                                $pq->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // 2. Filter Status
        $filter = $request->input('status', 'all');
        if ($filter === 'waiting') {
            $query->whereIn('status', [
                RequestModel::STATUS_SUBMITTED,
                RequestModel::STATUS_APPROVED_1,
                RequestModel::STATUS_APPROVED_2,
                RequestModel::STATUS_APPROVED_3,
            ]);
        } elseif ($filter === 'approved') { // Processing/Approved
            $query->whereIn('status', [
                RequestModel::STATUS_APPROVED_4,
                RequestModel::STATUS_PO_ISSUED,
                RequestModel::STATUS_ON_DELIVERY,
            ]);
        } elseif ($filter === 'completed') {
            $query->where('status', RequestModel::STATUS_COMPLETED);
        } elseif ($filter === 'awaiting_bast') {
            $query->whereNotNull('courier')
                ->whereNotNull('tracking_no')
                ->whereNull('bast_file')
                ->where('status', '!=', RequestModel::STATUS_COMPLETED);
        } elseif ($filter === 'rejected') {
            $query->where('status', RequestModel::STATUS_REJECTED);
        }

        // 3. Sorting
        $sortColumn = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        // Validate sort column to prevent SQL injection or errors
        $allowedSorts = ['created_at', 'ticket_no', 'status', 'priority'];
        if (!in_array($sortColumn, $allowedSorts)) {
            $sortColumn = 'created_at';
        }
        $query->orderBy($sortColumn, $sortDirection);

        // 4. Pagination
        $perPage = (int) $request->input('per_page', 20);
        $allowedPerPage = [20, 50, 100, 200, 500];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 20;
        }

        $requests = $query->paginate($perPage)->withQueryString();

        return view('requests.index', compact('requests', 'stats'));
    }

    /**
     * Display the user's personal requests (My Requests).
     */
    public function myRequests(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Stats Logic
        $stats = [
            'total' => RequestModel::where('requester_id', $userId)->count(),
            'pending' => RequestModel::where('requester_id', $userId)->whereIn('status', [
                RequestModel::STATUS_SUBMITTED,
                RequestModel::STATUS_APPROVED_1,
                RequestModel::STATUS_APPROVED_2,
                RequestModel::STATUS_APPROVED_3,
            ])->count(),
            'po_issued' => RequestModel::where('requester_id', $userId)->where('status', RequestModel::STATUS_PO_ISSUED)->count(),
            'delivery' => RequestModel::where('requester_id', $userId)->where('status', RequestModel::STATUS_ON_DELIVERY)->count(),
            'completed' => RequestModel::where('requester_id', $userId)->where('status', RequestModel::STATUS_COMPLETED)->count(),
        ];

        // Table Query
        $query = RequestModel::where('requester_id', $userId);

        // 1. Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($iq) use ($search) {
                        $iq->where('item_name', 'like', "%{$search}%")
                            ->orWhereHas('product', function ($pq) use ($search) {
                                $pq->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // 2. Filter Status
        $filter = $request->input('status', 'all');
        if ($filter === 'waiting') {
            $query->whereIn('status', [
                RequestModel::STATUS_SUBMITTED,
                RequestModel::STATUS_APPROVED_1,
                RequestModel::STATUS_APPROVED_2,
                RequestModel::STATUS_APPROVED_3,
            ]);
        } elseif ($filter === 'approved') { // Processing/Approved
            $query->whereIn('status', [
                RequestModel::STATUS_APPROVED_4,
                RequestModel::STATUS_PO_ISSUED,
                RequestModel::STATUS_ON_DELIVERY,
            ]);
        } elseif ($filter === 'completed') {
            $query->where('status', RequestModel::STATUS_COMPLETED);
        } elseif ($filter === 'rejected') {
            $query->where('status', RequestModel::STATUS_REJECTED);
        }

        // 3. Sorting
        $sortColumn = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $allowedSorts = ['created_at', 'ticket_no', 'status', 'priority'];
        if (!in_array($sortColumn, $allowedSorts)) {
            $sortColumn = 'created_at';
        }
        $query->orderBy($sortColumn, $sortDirection);

        // 4. Pagination
        $perPage = (int) $request->input('per_page', 20);
        $allowedPerPage = [20, 50, 100, 200, 500];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 20;
        }

        $requests = $query->paginate($perPage)->withQueryString();

        return view('requests.my-requests', compact('requests', 'stats'));
    }

    /**
     * Display the dashboard for requester role.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $userId = $user->id;

        $stats = [
            'total' => RequestModel::where('requester_id', $userId)->count(),
            'pending' => RequestModel::where('requester_id', $userId)->whereIn('status', [
                RequestModel::STATUS_SUBMITTED,
                RequestModel::STATUS_APPROVED_1,
                RequestModel::STATUS_APPROVED_2,
                RequestModel::STATUS_APPROVED_3,
            ])->count(),
            'approved' => RequestModel::where('requester_id', $userId)->whereIn('status', [
                RequestModel::STATUS_APPROVED_4,
                RequestModel::STATUS_PO_ISSUED,
                RequestModel::STATUS_ON_DELIVERY,
            ])->count(),

            'completed' => RequestModel::where('requester_id', $userId)->where('status', RequestModel::STATUS_COMPLETED)->count(),
        ];

        $recentRequests = RequestModel::where('requester_id', $userId)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('requests.dashboard', compact('stats', 'recentRequests'));
    }

    /**
     * Display history of requests that the approver has acted upon.
     */
    public function history()
    {
        $user = Auth::user();
        $role = $user->role;

        // Only for users mapped as approvers
        $isMappedApprover = \App\Models\ApprovalRoleLevel::where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();

        if (!$isMappedApprover) {
            return redirect()->route('requests.index');
        }

        // Get requests that this user has approved or rejected
        $approvedRequestIds = ApprovalLog::where('user_id', $user->id)
            ->pluck('request_id')
            ->unique();

        // Get user's approval logs keyed by request_id
        $userApprovalLogs = ApprovalLog::where('user_id', $user->id)
            ->get()
            ->keyBy('request_id');

        $requests = RequestModel::whereIn('id', $approvedRequestIds)
            ->with(['requester', 'items', 'logs.user'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        // Stats for history
        $stats = [
            'approved' => ApprovalLog::where('user_id', $user->id)->where('action', 'APPROVE')->count(),
            'rejected' => ApprovalLog::where('user_id', $user->id)->where('action', 'REJECT')->count(),
            'total' => $approvedRequestIds->count(),
            'this_month' => ApprovalLog::where('user_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        return view('requests.history', compact('requests', 'stats', 'role', 'userApprovalLogs'));
    }
    /**
     * Display approval inbox for approvers - requests pending their approval.
     */
    public function approvals(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Only for users mapped as approvers OR with explicit approver role
        $isMappedApprover = \App\Models\ApprovalRoleLevel::where('user_id', $userId)
            ->where('is_active', true)
            ->exists();

        if (!$isMappedApprover && $user->role !== 'approver') {
            return redirect()->route('requests.index');
        }

        // Level to status mapping
        $levelToStatus = [
            1 => RequestModel::STATUS_SUBMITTED,
            2 => RequestModel::STATUS_APPROVED_1,
            3 => RequestModel::STATUS_APPROVED_2,
            4 => RequestModel::STATUS_APPROVED_3,
        ];

        // Check if viewing history tab
        $isHistory = $request->get('tab') === 'history';

        // Sorting & Pagination
        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [20, 50, 100, 200, 500]))
            $perPage = 20;

        $defaultSort = $isHistory ? 'updated_at' : 'created_at';
        $sortCol = $request->input('sort', $defaultSort);
        $sortDir = $request->input('direction', 'desc');

        $allowedSorts = ['created_at', 'updated_at', 'ticket_no', 'status', 'priority'];
        if (!in_array($sortCol, $allowedSorts)) {
            $sortCol = $defaultSort;
        }

        $userApprovalLogs = collect();

        if ($isHistory) {
            // Get requests that this user has approved or rejected
            $approvedRequestIds = ApprovalLog::where('user_id', $userId)
                ->whereIn('action', ['APPROVE', 'REJECT'])
                ->pluck('request_id')
                ->unique();

            // Get user's approval logs keyed by request_id
            $userApprovalLogs = ApprovalLog::where('user_id', $userId)
                ->whereIn('action', ['APPROVE', 'REJECT'])
                ->get()
                ->keyBy('request_id');

            $requestsQuery = RequestModel::whereIn('id', $approvedRequestIds)
                ->with(['requester', 'items', 'logs.user']);

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $requestsQuery->where(function ($q) use ($search) {
                    $q->where('ticket_no', 'like', "%{$search}%")
                        ->orWhereHas('requester', function ($rq) use ($search) {
                            $rq->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Apply status filter
            if ($request->filled('status')) {
                $requestsQuery->where('status', $request->status);
            }

            // Apply priority filter
            if ($request->filled('priority')) {
                $requestsQuery->where('priority', $request->priority);
            }

            $requests = $requestsQuery->orderBy($sortCol, $sortDir)->paginate($perPage)->withQueryString();
        } else {
            // Base query for pending approvals assigned to this user
            $pendingQuery = RequestModel::whereHas('approvers', function ($q) use ($userId, $levelToStatus) {
                $q->where('user_id', $userId)
                    ->where('status', 'pending');
            })
                ->where(function ($q) use ($userId, $levelToStatus) {
                    foreach ($levelToStatus as $level => $requiredStatus) {
                        $q->orWhere(function ($subQ) use ($userId, $level, $requiredStatus) {
                            $subQ->whereHas('approvers', function ($approverQ) use ($userId, $level) {
                                $approverQ->where('user_id', $userId)
                                    ->where('level', $level)
                                    ->where('status', 'pending');
                            })->where('status', $requiredStatus);
                        });
                    }
                })
                ->with(['requester', 'items', 'approvers.user']);

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $pendingQuery->where(function ($q) use ($search) {
                    $q->where('ticket_no', 'like', "%{$search}%")
                        ->orWhereHas('requester', function ($rq) use ($search) {
                            $rq->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Apply status filter
            if ($request->filled('status')) {
                $pendingQuery->where('status', $request->status);
            }

            // Apply priority filter
            if ($request->filled('priority')) {
                $pendingQuery->where('priority', $request->priority);
            }

            $requests = $pendingQuery->orderBy($sortCol, $sortDir)->paginate($perPage)->withQueryString();
        }

        // Stats (always calculate from pending query)
        $pendingStatsQuery = RequestModel::whereHas('approvers', function ($q) use ($userId, $levelToStatus) {
            $q->where('user_id', $userId)
                ->where('status', 'pending');
        })
            ->where(function ($q) use ($userId, $levelToStatus) {
                foreach ($levelToStatus as $level => $requiredStatus) {
                    $q->orWhere(function ($subQ) use ($userId, $level, $requiredStatus) {
                        $subQ->whereHas('approvers', function ($approverQ) use ($userId, $level) {
                            $approverQ->where('user_id', $userId)
                                ->where('level', $level)
                                ->where('status', 'pending');
                        })->where('status', $requiredStatus);
                    });
                }
            });

        $totalPending = $pendingStatsQuery->count();
        $urgentRequests = (clone $pendingStatsQuery)->where('created_at', '<', now()->subDays(3))->count();
        $stuckRequests = (clone $pendingStatsQuery)->where('created_at', '<', now()->subDays(7))->count();
        $processedToday = ApprovalLog::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        $statuses = [
            RequestModel::STATUS_SUBMITTED => 'Waiting Approval (Level 1)',
            RequestModel::STATUS_APPROVED_1 => 'Waiting Approval (Level 2)',
            RequestModel::STATUS_APPROVED_2 => 'Waiting Approval (Level 3)',
            RequestModel::STATUS_APPROVED_3 => 'Waiting Approval (Level 4)',
            RequestModel::STATUS_APPROVED_4 => 'Approved',
            RequestModel::STATUS_REJECTED => 'Rejected',
        ];

        return view('requests.approvals', [
            'requests' => $requests,
            'userApprovalLogs' => $userApprovalLogs,
            'totalPending' => $totalPending,
            'urgentRequests' => $urgentRequests,
            'stuckRequests' => $stuckRequests,
            'processedToday' => $processedToday,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Bulk approve or reject multiple requests.
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:requests,id',
            'action' => 'required|in:approve,reject',
        ]);

        $user = Auth::user();
        $userId = $user->id;
        $action = $validated['action'];
        $successCount = 0;
        $failedCount = 0;

        // Status to level mapping
        $statusToLevel = [
            RequestModel::STATUS_SUBMITTED => 1,
            RequestModel::STATUS_APPROVED_1 => 2,
            RequestModel::STATUS_APPROVED_2 => 3,
            RequestModel::STATUS_APPROVED_3 => 4,
        ];

        // Level to next status mapping
        $levelToNextStatus = [
            1 => RequestModel::STATUS_APPROVED_1,
            2 => RequestModel::STATUS_APPROVED_2,
            3 => RequestModel::STATUS_APPROVED_3,
            4 => RequestModel::STATUS_APPROVED_4,
        ];

        foreach ($validated['ids'] as $requestId) {
            $requestData = RequestModel::find($requestId);
            if (!$requestData)
                continue;

            $currentStatus = $requestData->status;
            $requiredLevel = $statusToLevel[$currentStatus] ?? null;

            if (!$requiredLevel) {
                $failedCount++;
                continue;
            }

            // Check if user is the assigned approver for this request
            $requestApprover = \App\Models\RequestApprover::where('request_id', $requestId)
                ->where('level', $requiredLevel)
                ->where('user_id', $userId)
                ->where('status', 'pending')
                ->first();

            if (!$requestApprover) {
                $failedCount++;
                continue;
            }

            // Process
            if ($action === 'reject') {
                $newStatus = RequestModel::STATUS_REJECTED;
                $requestApprover->update([
                    'status' => 'rejected',
                    'action_at' => now(),
                ]);
            } else {
                // Find next available level
                $newStatus = $levelToNextStatus[$requiredLevel] ?? RequestModel::STATUS_APPROVED_4;

                // Check for next levels with approvers
                for ($checkLevel = $requiredLevel + 1; $checkLevel <= 4; $checkLevel++) {
                    $hasApprover = \App\Models\RequestApprover::where('request_id', $requestId)
                        ->where('level', $checkLevel)
                        ->where('status', 'pending')
                        ->exists();

                    if ($hasApprover) {
                        $newStatus = $levelToNextStatus[$checkLevel - 1] ?? RequestModel::STATUS_APPROVED_4;
                        break;
                    }
                }

                $requestApprover->update([
                    'status' => 'approved',
                    'action_at' => now(),
                ]);
            }

            $requestData->update(['status' => $newStatus]);

            ApprovalLog::create([
                'request_id' => $requestId,
                'user_id' => $userId,
                'role' => "LEVEL_{$requiredLevel}",
                'action' => strtoupper($action),
            ]);

            // Send notification to requester
            $requestData->requester->notify(new \App\Notifications\RequestActivityNotification(
                $requestData,
                $currentStatus,
                $newStatus,
                $action === 'approve' ? 'approved' : 'rejected'
            ));

            $successCount++;
        }

        $actionText = $action === 'approve' ? 'disetujui' : 'ditolak';
        return redirect()->route('requests.approvals')
            ->with('success', "{$successCount} request berhasil {$actionText}." . ($failedCount > 0 ? " {$failedCount} gagal." : ''));
    }

    /**
     * Show the checkout form for creating a new request.
     */
    public function checkout()
    {
        // Load products with their request type IDs for filtering
        $products = Product::with('requestTypes')->orderBy('name')->get();

        // Transform products to include request_type_ids for JS filtering
        $products = $products->map(function ($product) {
            $product->request_type_ids = $product->requestTypes->pluck('id')->toArray();
            return $product;
        });

        // Pre-format products JSON for Alpine.js to avoid closure in @json
        $productsJson = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'specs' => $p->specs,
                'image_url' => $p->image ? asset('storage/' . $p->image) : '',
                'category' => $p->category,
                'model_name' => $p->model_name,
                'request_type_ids' => $p->request_type_ids ?? [],
            ];
        })->values();

        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        // Get request types and replacement reasons from database
        $requestTypes = \App\Models\RequestType::active()->orderBy('name')->get();
        $replacementReasons = \App\Models\ReplacementReason::active()->orderBy('name')->get();

        // Request types slug to ID mapping for JS
        $requestTypesMap = $requestTypes->pluck('id', 'slug');

        // Request types config with allow_quantity for each type
        $requestTypesConfig = $requestTypes->mapWithKeys(function ($type) {
            return [
                $type->slug => [
                    'id' => $type->id,
                    'allow_quantity' => $type->allow_quantity,
                ]
            ];
        });

        // Check if user has approval role
        $user = Auth::user();
        $hasApprovalRole = $user->approval_role_id ? true : false;
        $approverCount = 0;

        if ($hasApprovalRole) {
            $approverCount = \App\Models\ApprovalRoleLevel::where('approval_role_id', $user->approval_role_id)
                ->where('is_active', true)
                ->whereNotNull('user_id')
                ->count();
        }

        return view('requests.checkout', compact('products', 'productsJson', 'branches', 'hasApprovalRole', 'approverCount', 'requestTypes', 'replacementReasons', 'requestTypesMap', 'requestTypesConfig'));
    }

    /**
     * Store a newly created resource in storage (Checkout).
     */
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Request Store Triggered', $request->all());

        // Validation for catalog-based product selection
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',

            'request_type' => 'required|exists:request_types,slug',
            'replacement_reason' => 'nullable|exists:replacement_reasons,slug',
            'items.*.disposal_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            'beneficiary_type' => 'required|in:BRANCH,USER',
            'beneficiary_id' => 'nullable|string|max:100',
            'beneficiary_name' => 'nullable|string|max:255',

            'shipping_address' => 'required|string',
            'shipping_pic_name' => 'required|string|max:255',
            'shipping_pic_phone' => 'required|string|max:50',

            'notes' => 'nullable|string|max:2000',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        // Check if user has approval_role_id assigned
        $requester = Auth::user();
        if (!$requester->approval_role_id) {
            return redirect()->back()
                ->with('error', 'Anda belum memiliki Approval Role yang ter-assign. Silakan hubungi IT Administrator untuk mengatur approval role Anda.')
                ->withInput();
        }

        // Check if approval role has any active approvers
        $approverCount = \App\Models\ApprovalRoleLevel::where('approval_role_id', $requester->approval_role_id)
            ->where('is_active', true)
            ->whereNotNull('user_id')
            ->count();

        if ($approverCount === 0) {
            return redirect()->back()
                ->with('error', 'Approval Role Anda belum memiliki approver yang ter-assign. Silakan hubungi IT Administrator.')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Handle File Upload - DEPRECATED Global Upload
            $disposalPath = null;
            // Removed global disposal doc handling in favor of per-item

            // Resolve Beneficiary Name
            $beneficiaryName = $validated['beneficiary_name'] ?? ('Branch: ' . $validated['beneficiary_id']);

            // Create Request
            $year = date('Y');
            do {
                $ticketNo = 'BAV-' . $year . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
            } while (RequestModel::where('ticket_no', $ticketNo)->exists());
            $newRequest = RequestModel::create([
                'ticket_no' => $ticketNo,
                'requester_id' => Auth::id(),
                'status' => RequestModel::STATUS_SUBMITTED,

                // Context
                'request_type' => $validated['request_type'],
                'replacement_reason' => $request->input('replacement_reason'),
                'disposal_doc_path' => $disposalPath,
                'beneficiary_type' => $validated['beneficiary_type'],
                'beneficiary_id' => $validated['beneficiary_id'],
                'beneficiary_name' => $beneficiaryName,
                'shipping_address' => $validated['shipping_address'],
                'shipping_pic_name' => $validated['shipping_pic_name'],
                'shipping_pic_phone' => $validated['shipping_pic_phone'],
                'priority' => $request->input('priority', 'low'),
            ]);

            // Add Items from catalog selection
            foreach ($validated['items'] as $index => $itemData) {
                // Handle Item Disposal Doc
                $itemDisposalPath = null;
                if ($request->hasFile("items.{$index}.disposal_doc") && $validated['request_type'] === 'REPLACEMENT') {
                    $fileObj = $request->file("items.{$index}.disposal_doc");

                    if (is_array($fileObj)) {
                        $fileObj = $fileObj[0];
                    }

                    // Use move() method instead of store() to bypass FilesystemAdapter issues
                    $extension = $fileObj->getClientOriginalExtension() ?: 'pdf';
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $destinationPath = storage_path('app/public/disposal_docs');

                    // Ensure directory exists
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    $fileObj->move($destinationPath, $filename);
                    $itemDisposalPath = 'disposal_docs/' . $filename;
                }

                $product = Product::find($itemData['product_id']);
                RequestItem::create([
                    'request_id' => $newRequest->id,
                    'disposal_doc_path' => $itemDisposalPath,
                    'product_id' => $product->id,
                    'item_name' => $product->name,
                    'item_specs' => $product->specs,
                    'qty' => $itemData['qty'],
                    'snap_price' => 0,
                ]);
            }

            // Log
            ApprovalLog::create([
                'request_id' => $newRequest->id,
                'user_id' => Auth::id(),
                'role' => 'REQUESTER',
                'action' => 'SUBMITTED',
            ]);

            // Assign Approvers from user's Approval Role mapping
            $requester = Auth::user();
            $approvalLevels = collect();

            if ($requester->approval_role_id) {
                $approvalLevels = \App\Models\ApprovalRoleLevel::where('approval_role_id', $requester->approval_role_id)
                    ->where('is_active', true)
                    ->whereNotNull('user_id')
                    ->orderBy('level')
                    ->get();

                foreach ($approvalLevels as $level) {
                    \App\Models\RequestApprover::create([
                        'request_id' => $newRequest->id,
                        'level' => $level->level,
                        'user_id' => $level->user_id,
                        'status' => 'pending',
                    ]);
                }
            }

            // Determine initial status based on first available approver level
            // Skip empty levels and go directly to the first assigned level
            $levelToStatus = [
                1 => RequestModel::STATUS_SUBMITTED,     // L1 pending
                2 => RequestModel::STATUS_APPROVED_1,  // L2 pending (L1 skipped)
                3 => RequestModel::STATUS_APPROVED_2,  // L3 pending (L1,L2 skipped)
                4 => RequestModel::STATUS_APPROVED_3, // L4 pending (L1,L2,L3 skipped)
            ];

            $firstApproverLevel = $approvalLevels->first();
            if ($firstApproverLevel && $firstApproverLevel->level > 1) {
                // Skip to the appropriate status for the first available level
                $initialStatus = $levelToStatus[$firstApproverLevel->level] ?? RequestModel::STATUS_SUBMITTED;
                $newRequest->update(['status' => $initialStatus]);

                // Log auto-skip for transparency
                foreach (range(1, $firstApproverLevel->level - 1) as $skippedLevel) {
                    ApprovalLog::create([
                        'request_id' => $newRequest->id,
                        'user_id' => Auth::id(),
                        'role' => "LEVEL_{$skippedLevel}",
                        'action' => 'AUTO_SKIPPED',
                    ]);
                }
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'REQUEST_CREATED',
                'description' => "Created request {$newRequest->ticket_no}",
                'ip_address' => request()->ip(),
                'subject_type' => get_class($newRequest),
                'subject_id' => $newRequest->id,
            ]);

            DB::commit();

            return redirect()->route('requests.show', $newRequest->id)->with('success', 'Request submitted successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit request: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource (Dynamic Page).
     */
    public function show($id)
    {
        $requestData = RequestModel::with(['items.product', 'logs.user', 'requester', 'approvers.user'])->findOrFail($id);

        $user = Auth::user();

        // Security check: allow if user is:
        // 1. Admin or Superadmin
        // 2. The requester themselves
        // 3. Assigned as approver for this request
        // 4. Mapped as approver in approval_role_levels
        $canView = false;

        if (in_array($user->role, ['admin', 'superadmin'])) {
            $canView = true;
        } elseif ($requestData->requester_id === $user->id) {
            $canView = true;
        } elseif ($requestData->approvers->where('user_id', $user->id)->isNotEmpty()) {
            $canView = true;
        } else {
            // Check if user is mapped as approver in any approval_role_levels
            $isMappedApprover = \App\Models\ApprovalRoleLevel::where('user_id', $user->id)
                ->where('is_active', true)
                ->exists();
            if ($isMappedApprover) {
                $canView = true;
            }
        }

        if (!$canView) {
            abort(403);
        }

        return view('requests.show', compact('requestData'));
    }

    /**
     * Update the status (Approval Actions).
     */
    public function updateStatus(Request $request, $id)
    {
        $requestData = RequestModel::with('requester')->findOrFail($id);

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string'
        ]);

        $user = Auth::user();
        $currentStatus = $requestData->status;
        $newStatus = $currentStatus;

        // Status to level mapping
        $statusToLevel = [
            RequestModel::STATUS_SUBMITTED => 1,     // Needs L1 approval
            RequestModel::STATUS_APPROVED_1 => 2,  // Needs L2 approval
            RequestModel::STATUS_APPROVED_2 => 3,  // Needs L3 approval
            RequestModel::STATUS_APPROVED_3 => 4, // Needs L4 approval
        ];

        // Level to next status mapping
        $levelToNextStatus = [
            1 => RequestModel::STATUS_APPROVED_1,
            2 => RequestModel::STATUS_APPROVED_2,
            3 => RequestModel::STATUS_APPROVED_3,
            4 => RequestModel::STATUS_APPROVED_4,
        ];

        // Check if current user is the assigned approver for this request at the current level
        $requiredLevel = $statusToLevel[$currentStatus] ?? null;

        if (!$requiredLevel) {
            return redirect()->back()->with('error', 'Request tidak dapat di-approve pada status ini.');
        }

        $requestApprover = \App\Models\RequestApprover::where('request_id', $requestData->id)
            ->where('level', $requiredLevel)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$requestApprover) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk meng-approve request ini.');
        }

        // Process approval/rejection
        if ($validated['action'] === 'reject') {
            $newStatus = RequestModel::STATUS_REJECTED;
            $requestApprover->update([
                'status' => 'rejected',
                'notes' => $validated['note'] ?? null,
                'action_at' => now(),
            ]);
        } else {
            // Find next available level with an approver assigned
            $nextLevel = $requiredLevel + 1;
            $finalStatus = $levelToNextStatus[$requiredLevel]; // Default next status

            // Check if there's an approver for the next levels
            $allLevelStatuses = [
                1 => RequestModel::STATUS_APPROVED_1,
                2 => RequestModel::STATUS_APPROVED_2,
                3 => RequestModel::STATUS_APPROVED_3,
                4 => RequestModel::STATUS_APPROVED_4,
            ];

            // Find the next level that has an approver
            $foundNextApprover = false;
            $skippedLevels = [];

            for ($checkLevel = $nextLevel; $checkLevel <= 4; $checkLevel++) {
                $hasApprover = \App\Models\RequestApprover::where('request_id', $requestData->id)
                    ->where('level', $checkLevel)
                    ->where('status', 'pending')
                    ->exists();

                if ($hasApprover) {
                    // Found the next level with an approver
                    // Set status to the one that makes this level pending
                    $newStatus = $allLevelStatuses[$checkLevel - 1] ?? RequestModel::STATUS_APPROVED_4;
                    $foundNextApprover = true;
                    break;
                } else {
                    // No approver at this level, mark for skipping
                    $skippedLevels[] = $checkLevel;
                }
            }

            // If no more approvers found, set to final approval status
            if (!$foundNextApprover) {
                $newStatus = RequestModel::STATUS_APPROVED_4;
            }

            $requestApprover->update([
                'status' => 'approved',
                'notes' => $validated['note'] ?? null,
                'action_at' => now(),
            ]);

            // Log skipped levels
            foreach ($skippedLevels as $skipLevel) {
                // Only log if we're actually skipping this level (it's before the next approver or final)
                if ($skipLevel < ($foundNextApprover ? $nextLevel + count($skippedLevels) : 5)) {
                    ApprovalLog::create([
                        'request_id' => $requestData->id,
                        'user_id' => $user->id,
                        'role' => "LEVEL_{$skipLevel}",
                        'action' => 'AUTO_SKIPPED',
                    ]);
                }
            }
        }

        if ($newStatus !== $currentStatus) {
            $requestData->update(['status' => $newStatus]);

            ApprovalLog::create([
                'request_id' => $requestData->id,
                'user_id' => $user->id,
                'role' => "LEVEL_{$requiredLevel}",
                'action' => strtoupper($validated['action']),
                'note' => $validated['note'] ?? null,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => ($validated['action'] == 'approve' ? 'REQUEST_APPROVED' : 'REQUEST_REJECTED'),
                'description' => strtoupper($validated['action']) . " request {$requestData->ticket_no} from status {$currentStatus} to {$newStatus}",
                'ip_address' => request()->ip(),
                'subject_type' => get_class($requestData),
                'subject_id' => $requestData->id,
            ]);

            // Notify Requester
            $requestData->requester->notify(new \App\Notifications\RequestActivityNotification(
                $requestData,
                $currentStatus,
                $newStatus,
                $validated['action'] == 'approve' ? 'approved' : 'rejected'
            ));

            // Notify Next Approver if Approved and moving to next pending level
            if ($validated['action'] === 'approve' && $newStatus !== RequestModel::STATUS_APPROVED_4 && $newStatus !== RequestModel::STATUS_REJECTED) {
                $nextLevelMap = [
                    RequestModel::STATUS_SUBMITTED => 1,
                    RequestModel::STATUS_APPROVED_1 => 2,
                    RequestModel::STATUS_APPROVED_2 => 3,
                    RequestModel::STATUS_APPROVED_3 => 4,
                ];

                // We are at $newStatus. The level corresponding to this status has just been PENDING-ed? 
                // No, updateStatus logic sets the status that MAKES the new level "Current".
                // E.g. L1 approves -> Status becomes APPR_1 -> This means L2 is now pending.

                $statusToNextLevel = [
                    RequestModel::STATUS_APPROVED_1 => 2,
                    RequestModel::STATUS_APPROVED_2 => 3,
                    RequestModel::STATUS_APPROVED_3 => 4,
                ];

                $targetLevel = $statusToNextLevel[$newStatus] ?? null;

                if ($targetLevel) {
                    $nextApprover = \App\Models\RequestApprover::where('request_id', $requestData->id)
                        ->where('level', $targetLevel)
                        ->where('status', 'pending')
                        ->with('user')
                        ->first();

                    if ($nextApprover && $nextApprover->user) {
                        $nextApprover->user->notify(new \App\Notifications\RequestActivityNotification(
                            $requestData,
                            $currentStatus,
                            $newStatus,
                            'approval_needed' // distinct action for approver
                        ));
                    }
                }
            }



            // Send Email Notification
            try {
                \Illuminate\Support\Facades\Mail::to($requestData->requester->email)->send(new \App\Mail\RequestStatusUpdated($requestData));
            } catch (\Exception $e) {
                // Log email failure but don't stop process
                \Illuminate\Support\Facades\Log::error('Email failed: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Request updated.');
    }
    public function completeHandover(Request $request, $id) // Renamed from uploadBast
    {
        $requestData = RequestModel::findOrFail($id);

        // Authorization: Only the requester, admin, or superadmin can complete handover
        $user = Auth::user();
        $isRequester = $user->id === $requestData->requester_id;
        $isAdminOrSuperadmin = in_array($user->role, ['admin', 'superadmin']);

        if (!$isRequester && !$isAdminOrSuperadmin) {
            return redirect()->back()->with('error', 'Hanya requester, admin, atau superadmin yang dapat mengkonfirmasi penerimaan asset.');
        }

        if ($requestData->status !== RequestModel::STATUS_ON_DELIVERY) {
            return redirect()->back()->with('error', 'Request status invalid for BAST upload.');
        }

        // Updated validation for E-form and Asset Photo
        $validated = $request->validate([
            'asset_photo' => 'required|image|max:2048', // Max 2MB (changed from 10MB)
            'e_form_confirm' => 'accepted' // Checkbox must be checked
        ]);

        if ($request->hasFile('asset_photo')) { // Changed from bast_file
            $fileObj = $request->file('asset_photo');
            $extension = $fileObj->getClientOriginalExtension() ?: 'jpg';
            $filename = 'handover_' . time() . '_' . uniqid() . '.' . $extension;
            $destinationPath = storage_path('app/public/assets_received'); // Changed directory

            // Ensure directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $fileObj->move($destinationPath, $filename);
            $path = 'assets_received/' . $filename;

            $requestData->update([
                'asset_photo_path' => $path, // Changed column
                'e_form_confirmed_at' => now(), // Add confirmation timestamp
                'status' => RequestModel::STATUS_COMPLETED
            ]);

            // Log
            ApprovalLog::create([
                'request_id' => $requestData->id,
                'user_id' => Auth::id(),
                'role' => auth()->user()->role, // Use actual role, not hardcoded 'REQUESTER'
                'action' => 'HANDOVER_COMPLETED', // Updated action name
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'REQUEST_COMPLETED',
                'description' => "Asset Handover Completed for request {$requestData->ticket_no}. E-form confirmed.",
                'ip_address' => request()->ip(),
                'subject_type' => get_class($requestData),
                'subject_id' => $requestData->id,
            ]);

            // Notify Requester
            $requestData->requester->notify(new \App\Notifications\RequestActivityNotification(
                $requestData,
                RequestModel::STATUS_ON_DELIVERY,
                RequestModel::STATUS_COMPLETED,
                'completed'
            ));

            // Notify Admins
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\RequestActivityNotification(
                    $requestData,
                    RequestModel::STATUS_ON_DELIVERY,
                    RequestModel::STATUS_COMPLETED,
                    'completed'
                ));
            }
        }

        return redirect()->back()->with('success', 'Asset handover berhasil dikonfirmasi. Request completed!');
    }

    /**
     * Export request detail to PDF
     */
    public function exportPdf($id)
    {
        \Log::info('========== PDF EXPORT STARTED ==========');
        \Log::info('Request ID: ' . $id);
        \Log::info('User: ' . Auth::user()->name . ' (ID: ' . Auth::user()->id . ')');

        try {
            $request = RequestModel::with(['requester', 'items.product', 'logs.user'])
                ->findOrFail($id);

            // Authorization: User must be requester, admin, superadmin, or an approver of this request
            $user = Auth::user();
            $isRequester = $user->id === $request->requester_id;
            $isAdminOrSuperadmin = in_array($user->role, ['admin', 'superadmin']);
            $isApprover = ApprovalLog::where('request_id', $id)
                ->where('user_id', $user->id)
                ->exists();

            \Log::info('Auth check - Req:' . ($isRequester ? 'Y' : 'N') . ' Admin:' . ($isAdminOrSuperadmin ? 'Y' : 'N') . ' Appr:' . ($isApprover ? 'Y' : 'N'));

            if (!$isRequester && !$isAdminOrSuperadmin && !$isApprover) {
                \Log::warning('Authorization FAILED');
                return redirect()->back()->with('error', 'Unauthorized access.');
            }

            \Log::info('Authorization PASSED');

            \Log::info('Generating PDF for request: ' . $request->ticket_no);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.request-detail', compact('request'));

            \Log::info('PDF generated successfully for: ' . $request->ticket_no);

            return $pdf->download('Request_Detail_' . $request->ticket_no . '.pdf');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}

