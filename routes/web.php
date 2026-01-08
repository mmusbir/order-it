<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SuperadminController;
use Illuminate\Support\Facades\Route;

// Public Redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Redirect Logic
    Route::get('/dashboard', function () {
        $role = auth()->user()->role ?? 'requester';

        if ($role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        } elseif ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'approver') {
            return redirect()->route('requests.index');
        }

        // Requester Dashboard - show dashboard with stats
        $userId = auth()->id();
        $requests = \App\Models\Request::where('requester_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'total' => \App\Models\Request::where('requester_id', $userId)->count(),
            'approved' => \App\Models\Request::where('requester_id', $userId)->whereIn('status', ['APPR_MGR', 'APPR_HEAD', 'APPR_DIR', 'PO_ISSUED'])->count(),
            'delivery' => \App\Models\Request::where('requester_id', $userId)->where('status', 'ON_DELIVERY')->count(),
            'completed' => \App\Models\Request::where('requester_id', $userId)->whereIn('status', ['COMPLETED', 'SYNCED'])->count(),
        ];

        return view('dashboard', compact('requests', 'stats'));
    })->name('dashboard');

    // Notifications
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Requester: Catalog & Cart
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Requester/Approver: Request Management
    Route::get('requests/dashboard', [RequestController::class, 'dashboard'])->name('requests.dashboard');
    Route::get('requests/my-requests', [RequestController::class, 'myRequests'])->name('requests.my-requests');
    Route::get('requests/checkout', [RequestController::class, 'checkout'])->name('requests.checkout');
    Route::get('requests/history', [RequestController::class, 'history'])->name('requests.history');
    Route::get('requests/approvals', [RequestController::class, 'approvals'])->name('requests.approvals');

    // Test route
    Route::get('requests/{id}/test', function ($id) {
        return 'TEST ROUTE WORKS! ID: ' . $id;
    })->name('requests.test');

    // PDF Export - MUST be before resource route to avoid conflict
    Route::get('requests/{id}/pdf', [RequestController::class, 'exportPdf'])->name('requests.pdf');

    Route::resource('requests', RequestController::class);
    Route::post('requests/{id}/status', [RequestController::class, 'updateStatus'])->name('requests.status');
    Route::post('requests/{id}/bast', [RequestController::class, 'completeHandover'])->name('requests.bast');
    Route::post('requests/bulk-approve', [RequestController::class, 'bulkApprove'])->name('requests.bulk-approve');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // New Admin Menus
        Route::get('/requests/monitor', [AdminController::class, 'monitor'])->name('requests.monitor');
        Route::get('/requests/fulfillment', [AdminController::class, 'fulfillment'])->name('requests.fulfillment');
        Route::get('/requests/{id}', [AdminController::class, 'showRequest'])->name('requests.show');

        // Admin User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        Route::resource('products', ProductController::class);
        Route::post('requests/{id}/po', [AdminController::class, 'generatePo'])->name('po.submit');
        Route::get('requests/{id}/po', [AdminController::class, 'generatePo'])->name('po');
        Route::post('requests/{id}/delivery', [AdminController::class, 'updateDelivery'])->name('delivery');
        Route::post('sync/{id}', [AdminController::class, 'syncToSnipe'])->name('sync');

        // Per-item operations
        Route::post('item/{itemId}/serial-tag', [AdminController::class, 'updateItemSerialTag'])->name('item.serial-tag');
        Route::post('item/{itemId}/sync', [AdminController::class, 'syncItemToSnipe'])->name('item.sync');
        Route::post('requests/{id}/delivery-info', [AdminController::class, 'updateRequestDelivery'])->name('delivery-info');

        // Snipe-IT Consumable Checkout
        Route::get('snipeit/consumables', [AdminController::class, 'searchSnipeITConsumables'])->name('snipeit.consumables');
        Route::get('snipeit/users', [AdminController::class, 'searchSnipeITUsers'])->name('snipeit.users');
        Route::get('snipeit/locations', [AdminController::class, 'searchSnipeITLocations'])->name('snipeit.locations');
        Route::post('item/{itemId}/checkout-consumable', [AdminController::class, 'checkoutConsumableToSnipe'])->name('item.checkout-consumable');

        // Consumable Management
        Route::get('consumables', [AdminController::class, 'consumables'])->name('consumables');
        Route::delete('consumables/{id}', [AdminController::class, 'deleteConsumable'])->name('consumables.delete');
        Route::post('consumables/bulk-delete', [AdminController::class, 'bulkDeleteConsumables'])->name('consumables.bulk-delete');
        Route::post('consumables/sync', [AdminController::class, 'syncConsumables'])->name('consumables.sync');
    });

    // Shared Admin/Superadmin Asset Resign Routes (Full Access)
    Route::prefix('superadmin')->name('superadmin.')->middleware(['auth'])->group(function () {
        Route::get('resigned-assets', [SuperadminController::class, 'resignedAssets'])->name('resigned-assets');
        Route::post('resigned-assets/upload-active-users', [SuperadminController::class, 'uploadActiveUsers'])->name('resigned-assets.upload');
        Route::post('resigned-assets/detect', [SuperadminController::class, 'detectResignedUsers'])->name('resigned-assets.detect');
        Route::post('resigned-assets/clear-active-users', [SuperadminController::class, 'clearActiveUsers'])->name('resigned-assets.clear-active-users');
        Route::delete('resigned-assets/upload-history/{id}', [SuperadminController::class, 'deleteUploadHistory'])->name('resigned-assets.delete-upload-history');
        Route::get('resigned-assets/export-csv', [SuperadminController::class, 'exportResignedAssetsCsv'])->name('resigned-assets.export-csv');
        Route::get('resigned-assets/search-users', [SuperadminController::class, 'searchSnipeitUsersForCheckout'])->name('resigned-assets.search-users');
        Route::post('resigned-assets/{id}/checkin', [SuperadminController::class, 'checkinResignedAsset'])->name('resigned-assets.checkin');
        Route::post('resigned-assets/{id}/checkout', [SuperadminController::class, 'checkoutResignedAsset'])->name('resigned-assets.checkout');
        Route::delete('resigned-assets/{id}', [SuperadminController::class, 'deleteResignedAsset'])->name('resigned-assets.delete');
    });

    // Superadmin Routes
    Route::prefix('superadmin')->name('superadmin.')->middleware('superadmin')->group(function () {
        Route::get('/', [SuperadminController::class, 'dashboard'])->name('dashboard');

        // SLA Report
        Route::get('sla-report', [SuperadminController::class, 'slaReport'])->name('sla-report');

        // User Management
        Route::get('users', [SuperadminController::class, 'users'])->name('users');
        Route::get('users/create', [SuperadminController::class, 'createUser'])->name('users.create');
        Route::post('users', [SuperadminController::class, 'storeUser'])->name('users.store');
        Route::get('users/{user}/edit', [SuperadminController::class, 'editUser'])->name('users.edit');
        Route::put('users/{user}', [SuperadminController::class, 'updateUser'])->name('users.update');
        Route::delete('users/{user}', [SuperadminController::class, 'deleteUser'])->name('users.destroy');

        // Settings
        Route::get('settings', [SuperadminController::class, 'settings'])->name('settings');
        Route::get('settings/master-data', [SuperadminController::class, 'masterData'])->name('settings.master-data');
        Route::get('settings/integration', [SuperadminController::class, 'integrationSettings'])->name('settings.integration');
        Route::get('settings/ldap', [SuperadminController::class, 'ldapSettings'])->name('settings.ldap');
        Route::post('settings/ldap', [SuperadminController::class, 'saveLdapSettings']);
        Route::post('settings/ldap/sync', [SuperadminController::class, 'syncLdap'])->name('settings.ldap.sync');
        Route::post('settings/ldap/test-binding', [SuperadminController::class, 'testLdapBinding'])->name('settings.ldap.test-binding');
        Route::post('settings/ldap/test-auth', [SuperadminController::class, 'testLdapAuth'])->name('settings.ldap.test-auth');
        Route::get('settings/ldap/logs', [SuperadminController::class, 'getLdapLogs'])->name('settings.ldap.logs');
        Route::get('settings/snipeit', [SuperadminController::class, 'snipeitSettings'])->name('settings.snipeit');
        Route::post('settings/snipeit', [SuperadminController::class, 'saveSnipeitSettings']);
        Route::post('settings/snipeit/test', [SuperadminController::class, 'testSnipeitConnection'])->name('settings.snipeit.test');
        Route::get('settings/snipeit/logs', [SuperadminController::class, 'getSnipeitLogs'])->name('settings.snipeit.logs');

        // Branch Management
        Route::get('settings/branches/template', [SuperadminController::class, 'downloadBranchTemplate'])->name('settings.branches.template');
        Route::get('settings/branches/export', [SuperadminController::class, 'exportBranchesCsv'])->name('settings.branches.export');
        Route::post('settings/branches/import', [SuperadminController::class, 'importBranchesCsv'])->name('settings.branches.import');

        Route::get('settings/branches', [SuperadminController::class, 'branches'])->name('settings.branches');
        Route::get('settings/branches/create', [SuperadminController::class, 'createBranch'])->name('settings.branches.create');
        Route::post('settings/branches', [SuperadminController::class, 'storeBranch'])->name('settings.branches.store');
        Route::get('settings/branches/{branch}/edit', [SuperadminController::class, 'editBranch'])->name('settings.branches.edit');
        Route::put('settings/branches/{branch}', [SuperadminController::class, 'updateBranch'])->name('settings.branches.update');
        Route::delete('settings/branches/{branch}', [SuperadminController::class, 'deleteBranch'])->name('settings.branches.destroy');



        // Role Management
        Route::get('settings/roles', [SuperadminController::class, 'roles'])->name('settings.roles');
        Route::get('settings/roles/create', [SuperadminController::class, 'createRole'])->name('settings.roles.create');
        Route::post('settings/roles', [SuperadminController::class, 'storeRole'])->name('settings.roles.store');
        Route::get('settings/roles/{role}/edit', [SuperadminController::class, 'editRole'])->name('settings.roles.edit');
        Route::put('settings/roles/{role}', [SuperadminController::class, 'updateRole'])->name('settings.roles.update-role');
        Route::delete('settings/roles/{role}', [SuperadminController::class, 'deleteRole'])->name('settings.roles.destroy');
        Route::put('settings/roles/{user}', [SuperadminController::class, 'updateUserRole'])->name('settings.roles.update');

        // Department Management
        Route::get('settings/departments', [SuperadminController::class, 'departments'])->name('settings.departments');
        Route::get('settings/departments/create', [SuperadminController::class, 'createDepartment'])->name('settings.departments.create');
        Route::post('settings/departments', [SuperadminController::class, 'storeDepartment'])->name('settings.departments.store');
        Route::get('settings/departments/{department}/edit', [SuperadminController::class, 'editDepartment'])->name('settings.departments.edit');
        Route::put('settings/departments/{department}', [SuperadminController::class, 'updateDepartment'])->name('settings.departments.update');
        Route::delete('settings/departments/{department}', [SuperadminController::class, 'deleteDepartment'])->name('settings.departments.destroy');

        // Approval Role Management
        Route::get('settings/approval-roles', [SuperadminController::class, 'approvalRoles'])->name('settings.approval-roles');
        Route::get('settings/approval-roles/create', [SuperadminController::class, 'createApprovalRole'])->name('settings.approval-roles.create');
        Route::post('settings/approval-roles', [SuperadminController::class, 'storeApprovalRole'])->name('settings.approval-roles.store');
        Route::get('settings/approval-roles/{approvalRole}/edit', [SuperadminController::class, 'editApprovalRole'])->name('settings.approval-roles.edit');
        Route::put('settings/approval-roles/{approvalRole}', [SuperadminController::class, 'updateApprovalRole'])->name('settings.approval-roles.update');
        Route::delete('settings/approval-roles/{approvalRole}', [SuperadminController::class, 'deleteApprovalRole'])->name('settings.approval-roles.destroy');

        // Job Title Management
        Route::get('settings/job-titles', [SuperadminController::class, 'jobTitles'])->name('settings.job-titles');
        Route::get('settings/job-titles/create', [SuperadminController::class, 'createJobTitle'])->name('settings.job-titles.create');
        Route::post('settings/job-titles', [SuperadminController::class, 'storeJobTitle'])->name('settings.job-titles.store');
        Route::get('settings/job-titles/{jobTitle}/edit', [SuperadminController::class, 'editJobTitle'])->name('settings.job-titles.edit');
        Route::put('settings/job-titles/{jobTitle}', [SuperadminController::class, 'updateJobTitle'])->name('settings.job-titles.update');
        Route::delete('settings/job-titles/{jobTitle}', [SuperadminController::class, 'deleteJobTitle'])->name('settings.job-titles.destroy');

        // Category Management
        Route::get('settings/categories', [SuperadminController::class, 'categories'])->name('settings.categories');
        Route::post('settings/categories', [SuperadminController::class, 'storeCategory'])->name('settings.categories.store');
        Route::put('settings/categories/{category}', [SuperadminController::class, 'updateCategory'])->name('settings.categories.update');
        Route::delete('settings/categories/{category}', [SuperadminController::class, 'deleteCategory'])->name('settings.categories.destroy');
        Route::post('settings/categories/sync', [SuperadminController::class, 'syncCategoriesFromSnipeit'])->name('settings.categories.sync');

        // Asset Model Management
        Route::get('settings/asset-models', [SuperadminController::class, 'assetModels'])->name('settings.asset-models');
        Route::post('settings/asset-models', [SuperadminController::class, 'storeAssetModel'])->name('settings.asset-models.store');
        Route::put('settings/asset-models/{assetModel}', [SuperadminController::class, 'updateAssetModel'])->name('settings.asset-models.update');
        Route::delete('settings/asset-models/{assetModel}', [SuperadminController::class, 'deleteAssetModel'])->name('settings.asset-models.destroy');
        Route::post('settings/asset-models/sync', [SuperadminController::class, 'syncModelsFromSnipeit'])->name('settings.asset-models.sync');

        // General Settings (Logo Management)
        Route::get('settings/general', [SuperadminController::class, 'generalSettings'])->name('settings.general');
        Route::put('settings/general', [SuperadminController::class, 'updateGeneralSettings'])->name('settings.general.update');

        // Request Type Management
        Route::get('settings/request-types', [SuperadminController::class, 'requestTypes'])->name('settings.request-types');
        Route::post('settings/request-types', [SuperadminController::class, 'storeRequestType'])->name('settings.request-types.store');
        Route::put('settings/request-types/{requestType}', [SuperadminController::class, 'updateRequestType'])->name('settings.request-types.update');
        Route::delete('settings/request-types/{requestType}', [SuperadminController::class, 'deleteRequestType'])->name('settings.request-types.destroy');

        // Replacement Reason Management
        Route::get('settings/replacement-reasons', [SuperadminController::class, 'replacementReasons'])->name('settings.replacement-reasons');
        Route::post('settings/replacement-reasons', [SuperadminController::class, 'storeReplacementReason'])->name('settings.replacement-reasons.store');
        Route::put('settings/replacement-reasons/{replacementReason}', [SuperadminController::class, 'updateReplacementReason'])->name('settings.replacement-reasons.update');
        Route::delete('settings/replacement-reasons/{replacementReason}', [SuperadminController::class, 'deleteReplacementReason'])->name('settings.replacement-reasons.destroy');

        // Consumable Management (Snipe-IT)
        Route::get('consumables', [SuperadminController::class, 'consumables'])->name('consumables');
        Route::delete('consumables/{id}', [SuperadminController::class, 'deleteConsumable'])->name('consumables.delete');
        Route::post('consumables/bulk-delete', [SuperadminController::class, 'bulkDeleteConsumables'])->name('consumables.bulk-delete');
        Route::post('consumables/sync', [SuperadminController::class, 'syncConsumables'])->name('consumables.sync');

        // Catalog Management
        Route::resource('products', ProductController::class);

        // Request Management (Global)
        Route::get('requests', [SuperadminController::class, 'requests'])->name('requests');
        Route::get('requests/{request}', [SuperadminController::class, 'showRequest'])->name('requests.show');
        Route::get('requests/{request}/edit', [SuperadminController::class, 'editRequest'])->name('requests.edit');
        Route::put('requests/{request}', [SuperadminController::class, 'updateRequest'])->name('requests.update');
        Route::delete('requests/{request}', [SuperadminController::class, 'deleteRequest'])->name('requests.destroy');

        // Global Approval Inbox
        Route::get('approvals', [SuperadminController::class, 'approvals'])->name('approvals');
        Route::post('approvals/{request}/approve', [SuperadminController::class, 'approveRequest'])->name('approvals.approve');
        Route::post('approvals/{request}/reject', [SuperadminController::class, 'rejectRequest'])->name('approvals.reject');
        Route::post('approvals/bulk', [SuperadminController::class, 'bulkApproval'])->name('approvals.bulk');

        // Audit Log
        Route::get('audit-logs', [SuperadminController::class, 'auditLogs'])->name('audit-logs');

        // SLA Configuration
        Route::get('settings/sla-configs', [SuperadminController::class, 'slaConfigs'])->name('settings.sla-configs');
        Route::put('settings/sla-approval/{id}', [SuperadminController::class, 'updateSlaApprovalConfig'])->name('settings.sla-approval.update');
        Route::put('settings/sla-fulfillment/{id}', [SuperadminController::class, 'updateSlaFulfillmentConfig'])->name('settings.sla-fulfillment.update');

        // SLA Report
        Route::get('sla-report', [SuperadminController::class, 'slaReport'])->name('sla-report');
        Route::get('sla-report/export-csv', [SuperadminController::class, 'exportSlaReportCsv'])->name('sla-report.export-csv');


        // Backup & Restore
        Route::get('settings/backup', [App\Http\Controllers\BackupController::class, 'index'])->name('settings.backup');
        Route::post('settings/backup', [App\Http\Controllers\BackupController::class, 'store'])->name('settings.backup.store');
        Route::get('settings/backup/{filename}/download', [App\Http\Controllers\BackupController::class, 'download'])->name('settings.backup.download');
        Route::delete('settings/backup/{filename}', [App\Http\Controllers\BackupController::class, 'destroy'])->name('settings.backup.destroy');
        Route::post('settings/backup/restore', [App\Http\Controllers\BackupController::class, 'restore'])->name('settings.backup.restore');

    });
});

// Asset Resign Management - accessible by users with department access
Route::middleware(['auth'])->prefix('asset-resign')->name('asset-resign.')->group(function () {
    Route::get('/', [SuperadminController::class, 'resignedAssetsForUsers'])->name('index');
    Route::post('{id}/checkin', [SuperadminController::class, 'checkinResignedAsset'])->name('checkin');
    Route::post('{id}/checkout', [SuperadminController::class, 'checkoutResignedAsset'])->name('checkout');
    Route::delete('{id}', [SuperadminController::class, 'deleteResignedAsset'])->name('delete');
    Route::get('search-users', [SuperadminController::class, 'searchSnipeitUsersForCheckout'])->name('search-users');
    Route::get('export-csv', [SuperadminController::class, 'exportResignedAssetsCsv'])->name('export-csv');
    Route::get('export-csv', [SuperadminController::class, 'exportResignedAssetsCsv'])->name('export-csv');
});

require __DIR__ . '/auth.php';
