<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class SuperadminController extends Controller
{
    /**
     * Superadmin Dashboard
     */
    public function dashboard()
    {
        $today = now()->startOfDay();

        // Main stats
        $totalUsers = User::count();
        $usersToday = User::where('created_at', '>=', $today)->count();

        $pendingApprovals = \App\Models\Request::whereIn('status', ['SUBMITTED', 'APPR_1', 'APPR_2', 'APPR_3'])->count();
        $pendingToday = \App\Models\Request::whereIn('status', ['SUBMITTED', 'APPR_1', 'APPR_2', 'APPR_3'])
            ->where('created_at', '>=', $today)->count();

        $activeRequests = \App\Models\Request::whereNotIn('status', ['COMPLETED', 'SYNCED', 'REJECTED', 'APPR_4'])->count();
        $activeToday = \App\Models\Request::whereNotIn('status', ['COMPLETED', 'SYNCED', 'REJECTED', 'APPR_4'])
            ->where('created_at', '>=', $today)->count();

        $totalCompleted = \App\Models\Request::whereIn('status', ['COMPLETED', 'SYNCED'])->count();
        $completedToday = \App\Models\Request::whereIn('status', ['COMPLETED', 'SYNCED'])
            ->where('updated_at', '>=', $today)->count();

        $stats = [
            'total_users' => $totalUsers,
            'users_today' => $usersToday,
            'pending_approvals' => $pendingApprovals,
            'pending_today' => $pendingToday,
            'active_requests' => $activeRequests,
            'active_today' => $activeToday,
            'total_completed' => $totalCompleted,
            'completed_today' => $completedToday,
            'users_by_role' => User::selectRaw('role, count(*) as count')->groupBy('role')->pluck('count', 'role'),
            'ldap_enabled' => AppSetting::getValue('ldap_enabled', false),
            'snipeit_enabled' => AppSetting::getValue('snipeit_enabled', false),
        ];

        $activities = \App\Models\ActivityLog::with('user')->latest()->take(10)->get();

        return view('superadmin.dashboard', compact('stats', 'activities'));
    }

    // ==================== USER MANAGEMENT ====================

    /**
     * List all users
     */
    public function users(Request $request)
    {
        $query = User::with(['jobTitle', 'approvalRole']);

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

        return view('superadmin.users.index', compact('users', 'roles', 'sortBy', 'sortDir', 'perPage'));
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
        return view('superadmin.users.create', compact('roles', 'departments', 'approvalRoles', 'jobTitles'));
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
            'role' => ['required', Rule::in($validRoles)],
            'department' => 'required|string|max:255',
            'approval_role_id' => 'nullable|exists:approval_roles,id',
            'job_title_id' => 'nullable|exists:job_titles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'employee_number' => $validated['employee_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'department' => $validated['department'] ?? null,
            'approval_role_id' => $validated['approval_role_id'] ?? null,
            'job_title_id' => $validated['job_title_id'] ?? null,
        ]);

        $this->logActivity('USER_CREATED', "Created user {$user->name} ({$user->role})", $user);

        return redirect()->route('superadmin.users')->with('success', 'User created successfully.');
    }

    /**
     * Show edit user form
     */
    public function editUser(User $user)
    {
        $roles = \App\Models\Role::active()->orderBy('name')->get();
        $departments = \App\Models\Department::where('is_active', true)->orderBy('name')->get();
        $approvalRoles = \App\Models\ApprovalRole::where('is_active', true)->orderBy('name')->get();
        $jobTitles = \App\Models\JobTitle::where('is_active', true)->orderBy('name')->get();
        return view('superadmin.users.edit', compact('user', 'roles', 'departments', 'approvalRoles', 'jobTitles'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        // Get valid role slugs from the roles table
        $validRoles = \App\Models\Role::active()->pluck('slug')->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'employee_number' => 'required|string|max:50',
            'password' => 'nullable|min:8|confirmed',
            'role' => ['required', Rule::in($validRoles)],
            'department' => 'required|string|max:255',
            'approval_role_id' => 'nullable|exists:approval_roles,id',
            'job_title_id' => 'nullable|exists:job_titles,id',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->employee_number = $validated['employee_number'] ?? null;
        $user->role = $validated['role'];
        $user->department = $validated['department'] ?? null;
        $user->approval_role_id = $validated['approval_role_id'] ?? null;
        $user->job_title_id = $validated['job_title_id'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $this->logActivity('USER_UPDATED', "Updated user {$user->name}", $user);

        return redirect()->route('superadmin.users')->with('success', 'User updated successfully.');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        // Check if user has related requests
        $requestCount = \App\Models\Request::where('requester_id', $user->id)->count();
        if ($requestCount > 0) {
            return back()->with('error', "Cannot delete user. User has {$requestCount} related request(s). Please reassign or delete the requests first.");
        }

        // Delete related records
        \App\Models\ApprovalLog::where('user_id', $user->id)->delete();
        \App\Models\DepartmentApprovalLevel::where('user_id', $user->id)->delete();
        \App\Models\ActivityLog::where('user_id', $user->id)->delete();

        $userName = $user->name;
        $userId = $user->id;
        $user->delete();

        $this->logActivity('USER_DELETED', "Deleted user {$userName} (ID: {$userId})");
        return redirect()->route('superadmin.users')->with('success', 'User deleted successfully.');
    }

    // ==================== SETTINGS ====================

    /**
     * General settings page
     */
    public function settings()
    {
        return view('superadmin.settings.index');
    }

    /**
     * Master Data settings page
     */
    public function masterData()
    {
        return view('superadmin.settings.master-data');
    }

    /**
     * Integration settings page (LDAP & Snipe-IT)
     */
    public function integrationSettings()
    {
        return view('superadmin.settings.integration');
    }

    // ==================== LDAP SETTINGS ====================

    /**
     * LDAP settings form
     */
    public function ldapSettings()
    {
        $settings = AppSetting::getByGroup('ldap');
        return view('superadmin.settings.ldap', compact('settings'));
    }

    /**
     * Save LDAP settings
     */
    public function saveLdapSettings(Request $request)
    {
        $validated = $request->validate([
            // Basic Settings
            'ldap_enabled' => 'boolean',
            'ldap_server' => 'nullable|string|max:255',
            'ldap_port' => 'nullable|integer',
            'ldap_base_dn' => 'nullable|string|max:500',
            'ldap_username' => 'nullable|string|max:255',
            'ldap_password' => 'nullable|string|max:255',
            'ldap_filter' => 'nullable|string|max:500',
            'ldap_auth_filter' => 'nullable|string|max:255',
            // AD & Security Options
            'ldap_is_ad' => 'boolean',
            'ldap_ad_domain' => 'nullable|string|max:255',
            'ldap_use_tls' => 'boolean',
            'ldap_ssl_skip_verify' => 'boolean',
            'ldap_password_sync' => 'boolean',
            'ldap_active_flag' => 'nullable|string|max:100',
            // Field Mappings
            'ldap_username_field' => 'nullable|string|max:100',
            'ldap_email_field' => 'nullable|string|max:100',
            'ldap_fname_field' => 'nullable|string|max:100',
            'ldap_lname_field' => 'nullable|string|max:100',
            'ldap_emp_num_field' => 'nullable|string|max:100',
            'ldap_dept_field' => 'nullable|string|max:100',
            'ldap_manager_field' => 'nullable|string|max:100',
            'ldap_phone_field' => 'nullable|string|max:100',
            'ldap_jobtitle_field' => 'nullable|string|max:100',
            'ldap_location_field' => 'nullable|string|max:100',
        ]);

        // Handle checkboxes - they won't be present if unchecked
        $checkboxFields = ['ldap_enabled', 'ldap_is_ad', 'ldap_use_tls', 'ldap_ssl_skip_verify', 'ldap_password_sync'];
        foreach ($checkboxFields as $field) {
            $validated[$field] = $request->has($field) ? '1' : '0';
        }

        foreach ($validated as $key => $value) {
            AppSetting::set($key, $value, 'ldap');
        }

        // Log the settings update
        \Log::info('LDAP settings updated', ['user' => auth()->user()->name]);

        return back()->with('success', 'LDAP settings saved successfully.');
    }

    /**
     * Test LDAP Binding (tests connection and bind credentials)
     */
    public function testLdapBinding()
    {
        // Check if LDAP extension is loaded
        if (!extension_loaded('ldap')) {
            return response()->json([
                'success' => false,
                'message' => 'PHP LDAP extension is not installed or enabled. Please enable php_ldap extension in your PHP configuration.'
            ]);
        }

        try {
            $server = AppSetting::getValue('ldap_server');
            $port = AppSetting::getValue('ldap_port', 389);
            $baseDn = AppSetting::getValue('ldap_base_dn');
            $username = AppSetting::getValue('ldap_username');
            $password = AppSetting::getValue('ldap_password');
            $useTls = AppSetting::getValue('ldap_use_tls', false);
            $skipSslVerify = AppSetting::getValue('ldap_ssl_skip_verify', false);

            if (!$server) {
                return response()->json(['success' => false, 'message' => 'LDAP Server URL is required.']);
            }

            \Log::info('LDAP binding test started', ['server' => $server, 'port' => $port]);

            // Connect to LDAP
            $ldapConn = @ldap_connect($server, $port);
            if (!$ldapConn) {
                \Log::error('LDAP connection failed', ['server' => $server]);
                return response()->json(['success' => false, 'message' => 'Could not connect to LDAP server.']);
            }

            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

            if ($skipSslVerify) {
                ldap_set_option($ldapConn, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
            }

            // Start TLS if enabled
            if ($useTls) {
                if (!@ldap_start_tls($ldapConn)) {
                    \Log::error('LDAP TLS failed', ['error' => ldap_error($ldapConn)]);
                    return response()->json(['success' => false, 'message' => 'STARTTLS failed: ' . ldap_error($ldapConn)]);
                }
            }

            // Bind
            $bind = @ldap_bind($ldapConn, $username, $password);
            if (!$bind) {
                \Log::error('LDAP bind failed', ['error' => ldap_error($ldapConn)]);
                return response()->json(['success' => false, 'message' => 'LDAP bind failed: ' . ldap_error($ldapConn)]);
            }

            // Test search in base DN
            $filter = AppSetting::getValue('ldap_filter', '(objectClass=*)');
            $search = @ldap_search($ldapConn, $baseDn, $filter, ['cn'], 0, 1);

            if (!$search) {
                \Log::warning('LDAP search test failed', ['base_dn' => $baseDn, 'error' => ldap_error($ldapConn)]);
                ldap_unbind($ldapConn);
                return response()->json([
                    'success' => true,
                    'message' => 'LDAP bind successful, but could not search Base DN. Please verify your Base DN setting.'
                ]);
            }

            $entries = ldap_get_entries($ldapConn, $search);
            ldap_unbind($ldapConn);

            \Log::info('LDAP binding test successful', ['entries_found' => $entries['count']]);

            return response()->json([
                'success' => true,
                'message' => "LDAP connection and bind successful! Found {$entries['count']} entries in Base DN."
            ]);

        } catch (\Exception $e) {
            \Log::error('LDAP binding test exception', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Test LDAP Authentication (tests user login)
     */
    public function testLdapAuth(Request $request)
    {
        $request->validate([
            'test_username' => 'required|string',
            'test_password' => 'required|string',
        ]);

        $testUser = $request->test_username;
        $testPass = $request->test_password;

        // Check if LDAP extension is loaded
        if (!extension_loaded('ldap')) {
            return response()->json([
                'success' => false,
                'message' => 'PHP LDAP extension is not installed or enabled. Please enable php_ldap extension in your PHP configuration.'
            ]);
        }

        try {
            $server = AppSetting::getValue('ldap_server');
            $port = AppSetting::getValue('ldap_port', 389);
            $baseDn = AppSetting::getValue('ldap_base_dn');
            $isAd = AppSetting::getValue('ldap_is_ad', false);
            $adDomain = AppSetting::getValue('ldap_ad_domain', '');
            $useTls = AppSetting::getValue('ldap_use_tls', false);
            $skipSslVerify = AppSetting::getValue('ldap_ssl_skip_verify', false);
            $authFilter = AppSetting::getValue('ldap_auth_filter', 'uid=');

            if (!$server) {
                return response()->json(['success' => false, 'message' => 'LDAP Server URL is required.']);
            }

            \Log::info('LDAP auth test started', ['user' => $testUser]);

            // Connect to LDAP
            $ldapConn = @ldap_connect($server, $port);
            if (!$ldapConn) {
                return response()->json(['success' => false, 'message' => 'Could not connect to LDAP server.']);
            }

            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

            if ($skipSslVerify) {
                ldap_set_option($ldapConn, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
            }

            if ($useTls) {
                if (!@ldap_start_tls($ldapConn)) {
                    return response()->json(['success' => false, 'message' => 'STARTTLS failed: ' . ldap_error($ldapConn)]);
                }
            }

            // Construct user DN for authentication
            if ($isAd && $adDomain) {
                // Active Directory uses UserPrincipalName
                $userDn = $testUser . '@' . $adDomain;
            } else {
                // Standard LDAP - search for user first
                $bindUser = AppSetting::getValue('ldap_username');
                $bindPass = AppSetting::getValue('ldap_password');

                // First bind with admin to search for user
                $adminBind = @ldap_bind($ldapConn, $bindUser, $bindPass);
                if (!$adminBind) {
                    return response()->json(['success' => false, 'message' => 'Admin bind failed. Cannot search for user.']);
                }

                // Search for the user
                $searchFilter = "({$authFilter}{$testUser})";
                $search = @ldap_search($ldapConn, $baseDn, $searchFilter, ['dn']);
                if (!$search) {
                    return response()->json(['success' => false, 'message' => 'User search failed: ' . ldap_error($ldapConn)]);
                }

                $entries = ldap_get_entries($ldapConn, $search);
                if ($entries['count'] == 0) {
                    return response()->json(['success' => false, 'message' => 'User not found in LDAP directory.']);
                }

                $userDn = $entries[0]['dn'];
            }

            // Now try to bind as the test user
            $userBind = @ldap_bind($ldapConn, $userDn, $testPass);
            ldap_unbind($ldapConn);

            if (!$userBind) {
                \Log::warning('LDAP auth test failed', ['user' => $testUser]);
                return response()->json(['success' => false, 'message' => 'Authentication failed. Invalid username or password.']);
            }

            \Log::info('LDAP auth test successful', ['user' => $testUser]);
            return response()->json(['success' => true, 'message' => "User '{$testUser}' authenticated successfully!"]);

        } catch (\Exception $e) {
            \Log::error('LDAP auth test exception', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get LDAP related logs from Laravel log file
     */
    public function getLdapLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logFile)) {
            $file = new \SplFileObject($logFile, 'r');
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key();

            $startLine = max(0, $totalLines - 500);
            $file->seek($startLine);

            $allLines = [];
            while (!$file->eof()) {
                $line = $file->fgets();
                if (!empty(trim($line))) {
                    $allLines[] = $line;
                }
            }

            // Filter for LDAP related logs
            $keywords = ['ldap', 'LDAP', 'active directory', 'bind', 'sync'];

            foreach ($allLines as $line) {
                $lineLower = strtolower($line);
                foreach ($keywords as $keyword) {
                    if (strpos($lineLower, strtolower($keyword)) !== false) {
                        $cleanLine = htmlspecialchars(trim($line));
                        if (strlen($cleanLine) > 500) {
                            $cleanLine = substr($cleanLine, 0, 500) . '...';
                        }
                        $logs[] = $cleanLine;
                        break;
                    }
                }
            }

            $logs = array_slice($logs, -50);
            $logs = array_reverse($logs);
        }

        return response()->json(['logs' => $logs]);
    }

    /**
     * Sync users from LDAP/Active Directory
     */
    public function syncLdap()
    {
        $enabled = AppSetting::getValue('ldap_enabled', false);
        if (!$enabled) {
            return back()->with('error', 'LDAP sync is not enabled.');
        }

        // Check if LDAP extension is loaded
        if (!extension_loaded('ldap')) {
            return back()->with('error', 'PHP LDAP extension is not installed or enabled. Please enable php_ldap extension in your PHP configuration.');
        }

        try {
            // Get LDAP settings
            $server = AppSetting::getValue('ldap_server');
            $port = AppSetting::getValue('ldap_port', 389);
            $baseDn = AppSetting::getValue('ldap_base_dn');
            $username = AppSetting::getValue('ldap_username');
            $password = AppSetting::getValue('ldap_password');
            $filter = AppSetting::getValue('ldap_filter', '(objectClass=user)');
            $useTls = AppSetting::getValue('ldap_use_tls', false);
            $skipSslVerify = AppSetting::getValue('ldap_ssl_skip_verify', false);

            // Get field mappings with defaults
            $usernameField = AppSetting::getValue('ldap_username_field', 'samaccountname');
            $emailField = AppSetting::getValue('ldap_email_field', 'mail');
            $fnameField = AppSetting::getValue('ldap_fname_field', 'givenname');
            $lnameField = AppSetting::getValue('ldap_lname_field', 'sn');
            $empNumField = AppSetting::getValue('ldap_emp_num_field', 'employeenumber');
            $deptField = AppSetting::getValue('ldap_dept_field', 'department');
            $phoneField = AppSetting::getValue('ldap_phone_field', 'telephonenumber');
            $jobtitleField = AppSetting::getValue('ldap_jobtitle_field', 'title');

            \Log::info('LDAP sync started');

            // Connect to LDAP
            $ldapConn = @ldap_connect($server, $port);
            if (!$ldapConn) {
                throw new \Exception('Could not connect to LDAP server.');
            }

            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

            if ($skipSslVerify) {
                ldap_set_option($ldapConn, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
            }

            if ($useTls) {
                if (!@ldap_start_tls($ldapConn)) {
                    throw new \Exception('STARTTLS failed: ' . ldap_error($ldapConn));
                }
            }

            // Bind
            $bind = @ldap_bind($ldapConn, $username, $password);
            if (!$bind) {
                throw new \Exception('LDAP bind failed: ' . ldap_error($ldapConn));
            }

            // Build attribute list for search
            $attributes = array_filter([
                $usernameField,
                $emailField,
                $fnameField,
                $lnameField,
                $empNumField,
                $deptField,
                $phoneField,
                $jobtitleField,
                'cn', // Always include cn as fallback
            ]);

            // Search users
            $search = @ldap_search($ldapConn, $baseDn, $filter, $attributes);
            if (!$search) {
                throw new \Exception('LDAP search failed: ' . ldap_error($ldapConn));
            }

            $entries = ldap_get_entries($ldapConn, $search);

            $imported = 0;
            $updated = 0;
            $skipped = 0;

            for ($i = 0; $i < $entries['count']; $i++) {
                $entry = $entries[$i];

                // Get email (required field)
                $email = $entry[strtolower($emailField)][0] ?? null;
                if (!$email) {
                    $skipped++;
                    continue;
                }

                // Build name from first name + last name, or use cn as fallback
                $fname = $entry[strtolower($fnameField)][0] ?? '';
                $lname = $entry[strtolower($lnameField)][0] ?? '';
                $name = trim($fname . ' ' . $lname);
                if (empty($name)) {
                    $name = $entry['cn'][0] ?? $entry[strtolower($usernameField)][0] ?? 'Unknown';
                }

                $employeeNumber = $entry[strtolower($empNumField)][0] ?? null;
                $department = $entry[strtolower($deptField)][0] ?? null;
                $phone = $entry[strtolower($phoneField)][0] ?? null;

                $user = User::where('email', $email)->first();
                if ($user) {
                    $user->update([
                        'name' => $name,
                        'department' => $department,
                        'employee_number' => $employeeNumber,
                    ]);
                    $updated++;
                } else {
                    User::create([
                        'name' => $name,
                        'email' => $email,
                        'employee_number' => $employeeNumber,
                        'password' => Hash::make(str()->random(16)),
                        'role' => 'requester',
                        'department' => $department,
                    ]);
                    $imported++;
                }
            }

            ldap_unbind($ldapConn);

            \Log::info('LDAP sync completed', ['imported' => $imported, 'updated' => $updated, 'skipped' => $skipped]);

            return back()->with('success', "LDAP sync completed. Imported: {$imported}, Updated: {$updated}, Skipped: {$skipped}");

        } catch (\Exception $e) {
            \Log::error('LDAP sync failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'LDAP sync failed: ' . $e->getMessage());
        }
    }

    // ==================== SNIPE-IT SETTINGS ====================

    /**
     * Snipe-IT settings form
     */
    public function snipeitSettings()
    {
        $settings = AppSetting::getByGroup('snipeit');
        return view('superadmin.settings.snipeit', compact('settings'));
    }

    /**
     * Save Snipe-IT settings
     */
    public function saveSnipeitSettings(Request $request)
    {
        $validated = $request->validate([
            'snipeit_url' => 'nullable|url|max:255',
            'snipeit_token' => 'nullable|string|max:500',
        ]);

        // Handle checkbox - if not present, it means unchecked
        $enabled = $request->has('snipeit_enabled') ? '1' : '0';
        AppSetting::set('snipeit_enabled', $enabled, 'snipeit');

        // Save URL and token
        AppSetting::set('snipeit_url', $validated['snipeit_url'] ?? '', 'snipeit');
        AppSetting::set('snipeit_token', $validated['snipeit_token'] ?? '', 'snipeit');

        // Clear cache
        \Illuminate\Support\Facades\Cache::forget('setting.snipeit_enabled');
        \Illuminate\Support\Facades\Cache::forget('setting.snipeit_url');
        \Illuminate\Support\Facades\Cache::forget('setting.snipeit_token');

        return back()->with('success', 'Snipe-IT settings saved successfully.');
    }

    /**
     * Test Snipe-IT API connection
     */
    public function testSnipeitConnection()
    {
        $url = AppSetting::getValue('snipeit_url');
        $token = AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return response()->json(['success' => false, 'message' => 'Snipe-IT URL and Token are required.']);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get(rtrim($url, '/') . '/api/v1/hardware', ['limit' => 1]);

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'Connection successful!']);
            } else {
                return response()->json(['success' => false, 'message' => 'API returned: ' . $response->status()]);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $message = $e->getMessage();
            if (str_contains($message, 'Could not resolve host') || str_contains($message, 'cURL error 6')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to resolve hostname. Please check if the URL is correct and accessible.'
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Connection failed: ' . $message]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Get Snipe-IT related logs from Laravel log file
     */
    public function getSnipeitLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logFile)) {
            // Read last 500 lines of log file
            $file = new \SplFileObject($logFile, 'r');
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key();

            $startLine = max(0, $totalLines - 500);
            $file->seek($startLine);

            $allLines = [];
            while (!$file->eof()) {
                $line = $file->fgets();
                if (!empty(trim($line))) {
                    $allLines[] = $line;
                }
            }

            // Filter for Snipe-IT / Consumable related logs
            $keywords = ['snipe', 'consumable', 'checkout', 'Consumable checkout', 'api/v1/consumables', 'api/v1/hardware'];

            foreach ($allLines as $line) {
                $lineLower = strtolower($line);
                foreach ($keywords as $keyword) {
                    if (strpos($lineLower, strtolower($keyword)) !== false) {
                        // Clean up the line for display
                        $cleanLine = htmlspecialchars(trim($line));
                        if (strlen($cleanLine) > 500) {
                            $cleanLine = substr($cleanLine, 0, 500) . '...';
                        }
                        $logs[] = $cleanLine;
                        break;
                    }
                }
            }

            // Keep only the last 50 relevant logs
            $logs = array_slice($logs, -50);

            // Reverse to show newest first
            $logs = array_reverse($logs);
        }

        return response()->json(['logs' => $logs]);
    }

    // ==================== REQUEST MANAGEMENT ====================

    /**
     * Display all requests globally with filters
     */
    public function requests(Request $request)
    {
        $query = \App\Models\Request::with(['requester', 'items']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                    ->orWhereHas('requester', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $perPage = $request->input('per_page', 20);
        $validPerPage = [20, 50, 100, 200, 500];

        if (!in_array($perPage, $validPerPage)) {
            $perPage = 20;
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Get all statuses for filter dropdown
        $statuses = [
            'SUBMITTED' => 'Pending L1',
            'APPR_1' => 'Waiting L2',
            'APPR_2' => 'Waiting L3',
            'APPR_3' => 'Waiting L4',
            'APPR_4' => 'Approved',
            'PO_ISSUED' => 'PO Issued',
            'ON_DELIVERY' => 'On Delivery',
            'COMPLETED' => 'Completed',
            'SYNCED' => 'Synced',
            'REJECTED' => 'Rejected',
        ];

        return view('superadmin.requests.index', compact('requests', 'statuses'));
    }

    /**
     * Show single request detail
     */
    public function showRequest(\App\Models\Request $request)
    {
        $request->load(['requester', 'items.product', 'approvalLogs.approver']);
        return view('superadmin.requests.show', compact('request'));
    }

    /**
     * Edit request form
     */
    public function editRequest(\App\Models\Request $request)
    {
        $request->load(['requester', 'items.product']);
        $statuses = [
            'SUBMITTED' => 'Submitted',
            'APPR_MGR' => 'Approved by Manager',
            'APPR_HEAD' => 'Approved by Head',
            'APPR_DIR' => 'Approved by Director',
            'PO_ISSUED' => 'PO Issued',
            'ON_DELIVERY' => 'On Delivery',
            'COMPLETED' => 'Completed',
            'SYNCED' => 'Synced to Snipe-IT',
            'REJECTED' => 'Rejected',
        ];
        return view('superadmin.requests.edit', compact('request', 'statuses'));
    }

    /**
     * Update request
     */
    public function updateRequest(Request $httpRequest, \App\Models\Request $request)
    {
        $validated = $httpRequest->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $request->status = $validated['status'];
        if (isset($validated['notes'])) {
            $request->notes = $validated['notes'];
        }
        $request->save();

        return redirect()->route('superadmin.requests')->with('success', 'Request updated successfully.');
    }

    /**
     * Delete request
     */
    public function deleteRequest(\App\Models\Request $request)
    {
        // Delete related items first
        $request->items()->delete();
        $request->approvalLogs()->delete();
        $request->delete();

        return redirect()->route('superadmin.requests')->with('success', 'Request deleted successfully.');
    }

    // ==================== APPROVAL INBOX ====================

    /**
     * Global Approval Inbox
     */
    public function approvals(Request $request)
    {
        $query = \App\Models\Request::with(['requester', 'items']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                    ->orWhereHas('requester', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Department filter
        if ($request->filled('department')) {
            $query->whereHas('requester', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: Hide completed/rejected items from Inbox
            $query->whereNotIn('status', ['COMPLETED', 'SYNCED', 'REJECTED']);
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Per page handling
        $perPage = $request->input('per_page', 20);
        $validPerPage = [20, 50, 100, 200, 500];
        if (!in_array((int) $perPage, $validPerPage)) {
            $perPage = 20;
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Stats
        // Pending: total tickets yang belum selesai approvalnya (belum COMPLETED/REJECTED)
        $totalPending = \App\Models\Request::whereNotIn('status', ['COMPLETED', 'SYNCED', 'REJECTED'])->count();

        // Urgent: total request dengan priority = high
        $urgentRequests = \App\Models\Request::where('priority', 'high')
            ->whereNotIn('status', ['COMPLETED', 'SYNCED', 'REJECTED'])->count();

        // Stuck: total request yang sudah lebih 7 hari dan belum selesai
        $stuckRequests = \App\Models\Request::whereNotIn('status', ['COMPLETED', 'SYNCED', 'REJECTED'])
            ->where('created_at', '<', now()->subDays(7))->count();

        // Processed Today: total perubahan status tiket request hari ini
        $processedToday = \App\Models\ApprovalLog::whereDate('created_at', today())->count();

        // Departments for filter
        $departments = User::whereNotNull('department')->distinct()->pluck('department');

        // Statuses for filter
        $statuses = [
            'SUBMITTED' => 'Waiting Approval',
            'APPR_MGR' => 'Approved by Manager',
            'APPR_HEAD' => 'Approved by Head',
            'APPR_DIR' => 'Approved by Director',
            'PO_ISSUED' => 'Processing PO',
            'ON_DELIVERY' => 'On Delivery',
            'COMPLETED' => 'Completed',
            'REJECTED' => 'Rejected',
        ];

        return view('superadmin.approvals.index', compact(
            'requests',
            'totalPending',
            'urgentRequests',
            'stuckRequests',
            'processedToday',
            'departments',
            'statuses'
        ));
    }

    /**
     * Approve a request (superadmin override)
     */
    public function approveRequest(Request $httpRequest, \App\Models\Request $request)
    {
        // Superadmin can approve to any status
        $nextStatus = match ($request->status) {
            'SUBMITTED' => 'APPR_MGR',
            'APPR_MGR' => 'APPR_HEAD',
            'APPR_HEAD' => 'APPR_DIR',
            default => $request->status
        };

        $request->status = $nextStatus;
        $request->save();

        // Log approval
        \App\Models\ApprovalLog::create([
            'request_id' => $request->id,
            'user_id' => auth()->id(),
            'role' => 'superadmin',
            'action' => 'APPROVE',
        ]);

        return back()->with('success', "Request {$request->ticket_no} approved.");
    }

    /**
     * Reject a request
     */
    public function rejectRequest(Request $httpRequest, \App\Models\Request $request)
    {
        $request->status = 'REJECTED';
        $request->save();

        // Log rejection
        \App\Models\ApprovalLog::create([
            'request_id' => $request->id,
            'user_id' => auth()->id(),
            'role' => 'superadmin',
            'action' => 'REJECT',
        ]);

        return back()->with('success', "Request {$request->ticket_no} rejected.");
    }

    /**
     * Bulk approve/reject requests
     */
    public function bulkApproval(Request $httpRequest)
    {
        $validated = $httpRequest->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:requests,id',
            'action' => 'required|in:approve,reject',
        ]);

        $ids = $validated['ids'];
        $action = $validated['action'];
        $count = 0;

        foreach ($ids as $id) {
            $request = \App\Models\Request::find($id);
            if (!$request)
                continue;

            if ($action === 'approve') {
                $nextStatus = match ($request->status) {
                    'SUBMITTED' => 'APPR_1',
                    'APPR_1' => 'APPR_2',
                    'APPR_2' => 'APPR_3',
                    'APPR_3' => 'APPR_4',
                    default => $request->status
                };
                $request->status = $nextStatus;
                $request->save();

                \App\Models\ApprovalLog::create([
                    'request_id' => $request->id,
                    'user_id' => auth()->id(),
                    'role' => 'superadmin',
                    'action' => 'APPROVE',
                ]);
            } else {
                $request->status = 'REJECTED';
                $request->save();

                \App\Models\ApprovalLog::create([
                    'request_id' => $request->id,
                    'user_id' => auth()->id(),
                    'role' => 'superadmin',
                    'action' => 'REJECT',
                ]);
            }
            $count++;
        }

        $actionLabel = $action === 'approve' ? 'approved' : 'rejected';
        return back()->with('success', "{$count} request(s) {$actionLabel} successfully.");
    }

    // ==================== BRANCH MANAGEMENT ====================

    /**
     * List all branches
     */
    public function branches(Request $request)
    {
        $query = \App\Models\Branch::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('branch_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $branches = $query->orderBy('name')->paginate($perPage)->withQueryString();
        return view('superadmin.settings.branches.index', compact('branches'));
    }

    /**
     * Show create branch form
     */
    public function createBranch()
    {
        return view('superadmin.settings.branches.create');
    }

    /**
     * Store new branch
     */
    public function storeBranch(Request $request)
    {
        $validated = $request->validate([
            'branch_code' => 'required|string|max:50|unique:branches,branch_code',
            'name' => 'required|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'google_maps_url' => 'nullable|url|max:500',
        ]);

        $branch = \App\Models\Branch::create($validated);
        $this->logActivity('BRANCH_CREATED', "Created branch {$branch->name}", $branch);

        return redirect()->route('superadmin.settings.branches')->with('success', 'Branch created successfully.');
    }

    /**
     * Show edit branch form
     */
    public function editBranch(\App\Models\Branch $branch)
    {
        return view('superadmin.settings.branches.edit', compact('branch'));
    }

    /**
     * Update branch
     */
    public function updateBranch(Request $request, \App\Models\Branch $branch)
    {
        $validated = $request->validate([
            'branch_code' => 'required|string|max:50|unique:branches,branch_code,' . $branch->id,
            'name' => 'required|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'google_maps_url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $branch->update($validated);
        $this->logActivity('BRANCH_UPDATED', "Updated branch {$branch->name}", $branch);

        return redirect()->route('superadmin.settings.branches')->with('success', 'Branch updated successfully.');
    }

    /**
     * Download Branch CSV Template
     */
    /**
     * Download Branch CSV Template
     */
    public function downloadBranchTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="branches_template.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['branch_code', 'name', 'pic_name', 'phone', 'address', 'google_maps_url', 'is_active']);

            // Get 4 random branches for example
            $details = \App\Models\Branch::inRandomOrder()->limit(4)->get();

            if ($details->count() > 0) {
                foreach ($details as $branch) {
                    fputcsv($handle, [
                        $branch->branch_code,
                        $branch->name,
                        $branch->pic_name,
                        $branch->phone,
                        $branch->address,
                        $branch->google_maps_url,
                        '1'
                    ]);
                }
            } else {
                // Fallback dummy data if no branches exist
                fputcsv($handle, ['BRN-001', 'Contoh Cabang', 'John Doe', '021-12345678', 'Jl. Contoh No. 1', 'https://maps.google.com/...', '1']);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Branches to CSV
     */
    /**
     * Export Branches to CSV
     */
    public function exportBranchesCsv()
    {
        $branches = \App\Models\Branch::orderBy('branch_code')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="branches_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($branches) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['branch_code', 'name', 'pic_name', 'phone', 'address', 'google_maps_url', 'is_active']);

            foreach ($branches as $branch) {
                fputcsv($handle, [
                    $branch->branch_code,
                    $branch->name,
                    $branch->pic_name,
                    $branch->phone,
                    $branch->address,
                    $branch->google_maps_url,
                    $branch->is_active ? '1' : '0'
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import Branches from CSV
     */
    public function importBranchesCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'import_mode' => 'required|in:add,update',
        ]);

        $file = $request->file('csv_file');
        $mode = $request->input('import_mode');

        $filePath = $file->getRealPath();
        if (empty($filePath)) {
            // Fallback: move file to temp location and use that path
            $filePath = $file->getPathname();
            if (empty($filePath)) {
                return back()->with('error', 'Unable to read uploaded file. Please try again.');
            }
        }

        $handle = fopen($filePath, 'r');

        // Read file content and convert encoding if needed
        $content = file_get_contents($filePath);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            // Write converted content to temp file
            $tempFile = tempnam(sys_get_temp_dir(), 'csv_');
            file_put_contents($tempFile, $content);
            fclose($handle);
            $handle = fopen($tempFile, 'r');
        }

        // Skip header
        fgetcsv($handle);

        $imported = 0;
        $updated = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            // Mapping: 0=code, 1=name, 2=pic, 3=phone, 4=address, 5=maps, 6=active
            if (count($row) < 2)
                continue; // Min require code & name

            $code = trim($row[0]);
            if (empty($code))
                continue;

            $branch = \App\Models\Branch::where('branch_code', $code)->first();

            $data = [
                'branch_code' => $code,
                'name' => $row[1],
                'pic_name' => $row[2] ?? null,
                'phone' => $row[3] ?? null,
                'address' => $row[4] ?? null,
                'google_maps_url' => $row[5] ?? null,
                'is_active' => isset($row[6]) ? (bool) $row[6] : true,
            ];

            if ($mode === 'add') {
                if (!$branch) {
                    \App\Models\Branch::create($data);
                    $imported++;
                } else {
                    $skipped++;
                }
            } elseif ($mode === 'update') {
                if ($branch) {
                    $branch->update($data);
                    $updated++;
                } else {
                    \App\Models\Branch::create($data);
                    $imported++;
                }
            }
        }

        fclose($handle);

        return back()->with('success', "Process completed. Mode: " . strtoupper($mode) . ". Imported: {$imported}, Updated: {$updated}, Skipped: {$skipped}.");
    }

    /**
     * Delete branch
     */
    public function deleteBranch(\App\Models\Branch $branch)
    {
        $branch->delete();
        $this->logActivity('BRANCH_DELETED', "Deleted branch {$branch->name} (Code: {$branch->branch_code})");
        return redirect()->route('superadmin.settings.branches')->with('success', 'Branch deleted successfully.');
    }

    // ==================== GENERAL SETTINGS (LOGO MANAGEMENT) ====================

    /**
     * Show general settings page
     */
    public function generalSettings()
    {
        $sidebarLogoLight = AppSetting::getValue('sidebar_logo_light');
        $sidebarLogoDark = AppSetting::getValue('sidebar_logo_dark');
        $loginLogoLight = AppSetting::getValue('login_logo_light');
        $loginLogoDark = AppSetting::getValue('login_logo_dark');
        $favicon = AppSetting::getValue('favicon');
        $appTitle = AppSetting::getValue('app_title', 'Order IT');

        return view('superadmin.settings.general.index', compact(
            'sidebarLogoLight',
            'sidebarLogoDark',
            'loginLogoLight',
            'loginLogoDark',
            'favicon',
            'appTitle'
        ));
    }

    /**
     * Update general settings (logos, favicon, app title)
     */
    public function updateGeneralSettings(Request $request)
    {
        $request->validate([
            'sidebar_logo_light' => 'nullable|image|mimes:png|max:2048',
            'sidebar_logo_dark' => 'nullable|image|mimes:png|max:2048',
            'login_logo_light' => 'nullable|image|mimes:png|max:2048',
            'login_logo_dark' => 'nullable|image|mimes:png|max:2048',
            'favicon' => 'nullable|file|mimes:ico|max:512',
            'app_title' => 'nullable|string|max:100',
        ]);

        // Reset all settings if requested
        if ($request->has('reset_all')) {
            $logoKeys = ['sidebar_logo_light', 'sidebar_logo_dark', 'login_logo_light', 'login_logo_dark', 'favicon'];

            foreach ($logoKeys as $key) {
                $oldFile = AppSetting::getValue($key);
                if (!empty($oldFile) && trim($oldFile) !== '')
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldFile);
                AppSetting::set($key, null);
            }

            AppSetting::set('app_title', 'Order IT');

            $this->logActivity('SETTINGS_RESET', 'Reset all branding settings to default');

            return redirect()->route('superadmin.settings.general')->with('success', 'Semua setting berhasil di-reset ke default.');
        }

        // Handle logo uploads
        $logoFields = [
            'sidebar_logo_light' => 'Sidebar logo (light mode)',
            'sidebar_logo_dark' => 'Sidebar logo (dark mode)',
            'login_logo_light' => 'Login logo (light mode)',
            'login_logo_dark' => 'Login logo (dark mode)',
        ];

        foreach ($logoFields as $field => $label) {
            if ($request->hasFile($field)) {
                $oldLogo = AppSetting::getValue($field);
                if (!empty($oldLogo) && trim($oldLogo) !== '')
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldLogo);

                $path = $request->file($field)->store('logos', 'public');
                AppSetting::set($field, $path);
                $this->logActivity('LOGO_UPDATED', "Updated {$label}");
            }
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $oldFavicon = AppSetting::getValue('favicon');
            if (!empty($oldFavicon) && trim($oldFavicon) !== '')
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldFavicon);

            $path = $request->file('favicon')->store('logos', 'public');
            AppSetting::set('favicon', $path);
            $this->logActivity('FAVICON_UPDATED', 'Updated favicon');
        }

        // Handle app title
        if ($request->filled('app_title')) {
            AppSetting::set('app_title', $request->input('app_title'));
            $this->logActivity('APP_TITLE_UPDATED', 'Updated app title to: ' . $request->input('app_title'));
        }

        return redirect()->route('superadmin.settings.general')->with('success', 'Settings berhasil diperbarui.');
    }

    /**
     * Audit Logs - View all activity logs with filters
     */
    public function auditLogs(Request $request)
    {
        $query = \App\Models\ActivityLog::with('user')->latest();

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(25);

        // Get unique actions for filter dropdown
        $actions = \App\Models\ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        // Get users for filter dropdown
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('superadmin.audit-logs.index', compact('logs', 'actions', 'users'));
    }

    /**
     * Role Management - List all roles
     */
    public function roles(Request $request)
    {
        // Get roles from database
        $roleModels = \App\Models\Role::orderBy('name')->get();

        $roleCounts = User::selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');

        return view('superadmin.settings.roles.index', compact('roleCounts', 'roleModels'));
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, User $user)
    {
        // Get valid role slugs from database
        $validRoles = \App\Models\Role::active()->pluck('slug')->toArray();

        $validated = $request->validate([
            'role' => 'required|in:' . implode(',', $validRoles),
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);

        $this->logActivity('ROLE_UPDATED', "Updated role for {$user->name} from {$oldRole} to {$validated['role']}", $user);

        return redirect()->back()->with('success', "Role for {$user->name} updated to {$validated['role']}.");
    }

    /**
     * Show create role form
     */
    public function createRole()
    {
        return view('superadmin.settings.roles.create');
    }

    /**
     * Store new role
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:roles,slug|regex:/^[a-z0-9\-]+$/',
            'description' => 'nullable|string',
            'approval_level' => 'nullable|integer|min:1|max:4',
            'is_approver' => 'boolean',
        ]);

        $role = \App\Models\Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'approval_level' => $validated['approval_level'] ?? null,
            'is_approver' => $validated['is_approver'] ?? false,
            'is_system' => false,
            'is_active' => true,
        ]);

        $this->logActivity('ROLE_CREATED', "Created new role: {$role->name}", $role);

        return redirect()->route('superadmin.settings.roles')->with('success', "Role {$role->name} berhasil dibuat.");
    }

    /**
     * Show edit role form
     */
    public function editRole(\App\Models\Role $role)
    {
        return view('superadmin.settings.roles.edit', compact('role'));
    }

    /**
     * Update role
     */
    public function updateRole(Request $request, \App\Models\Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|regex:/^[a-z0-9\-]+$/|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'approval_level' => 'nullable|integer|min:1|max:4',
            'is_approver' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Don't allow changing slug for system roles
        if ($role->is_system) {
            unset($validated['slug']);
        }

        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $role->slug,
            'description' => $validated['description'] ?? null,
            'approval_level' => $validated['approval_level'] ?? null,
            'is_approver' => $validated['is_approver'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $this->logActivity('ROLE_UPDATED', "Updated role: {$role->name}", $role);

        return redirect()->route('superadmin.settings.roles')->with('success', "Role {$role->name} berhasil diupdate.");
    }

    /**
     * Delete role
     */
    public function deleteRole(\App\Models\Role $role)
    {
        if ($role->is_system) {
            return redirect()->back()->with('error', 'System role tidak dapat dihapus.');
        }

        // Check if any users have this role
        $usersCount = User::where('role', $role->slug)->count();
        if ($usersCount > 0) {
            return redirect()->back()->with('error', "Role tidak dapat dihapus karena masih digunakan oleh {$usersCount} user.");
        }

        $roleName = $role->name;
        $this->logActivity('ROLE_DELETED', "Deleted role: {$roleName}", $role);
        $role->delete();

        return redirect()->route('superadmin.settings.roles')->with('success', "Role {$roleName} berhasil dihapus.");
    }

    /**
     * Department Management - List all departments
     */
    public function departments(Request $request)
    {
        $departments = \App\Models\Department::with('approvalLevels')->withCount(['users'])->orderBy('name')->get();

        return view('superadmin.settings.departments.index', compact('departments'));
    }

    /**
     * Show create department form
     */
    public function createDepartment()
    {
        return view('superadmin.settings.departments.create');
    }

    /**
     * Store new department
     */
    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'can_access_asset_resign' => 'boolean',
        ]);

        $department = \App\Models\Department::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'can_access_asset_resign' => $validated['can_access_asset_resign'] ?? false,
            'is_active' => true,
        ]);
        $this->logActivity('DEPARTMENT_CREATED', "Created department: {$department->name}", $department);

        return redirect()->route('superadmin.settings.departments')->with('success', "Department {$department->name} created successfully.");
    }

    /**
     * Show edit department form
     */
    public function editDepartment(\App\Models\Department $department)
    {
        return view('superadmin.settings.departments.edit', compact('department'));
    }

    /**
     * Update department
     */
    public function updateDepartment(Request $request, \App\Models\Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'can_access_asset_resign' => 'boolean',
        ]);

        $oldName = $department->name;
        $department->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'can_access_asset_resign' => $validated['can_access_asset_resign'] ?? false,
        ]);

        // Update users if department name changed
        if ($oldName !== $validated['name']) {
            User::where('department', $oldName)->update(['department' => $validated['name']]);
        }

        $this->logActivity('DEPARTMENT_UPDATED', "Updated department: {$department->name}", $department);

        return redirect()->route('superadmin.settings.departments')->with('success', "Department updated successfully.");
    }

    /**
     * Delete department
     */
    public function deleteDepartment(\App\Models\Department $department)
    {
        $name = $department->name;

        // Clear department from users
        User::where('department', $name)->update(['department' => null]);

        $department->delete();
        $this->logActivity('DEPARTMENT_DELETED', "Deleted department: {$name}");

        return redirect()->route('superadmin.settings.departments')->with('success', "Department {$name} deleted.");
    }

    /**
     * Approval Role Management - List all approval roles
     */
    public function approvalRoles(Request $request)
    {
        $approvalRoles = \App\Models\ApprovalRole::with('levels')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        return view('superadmin.settings.approval-roles.index', compact('approvalRoles'));
    }

    /**
     * Show create approval role form
     */
    public function createApprovalRole()
    {
        return view('superadmin.settings.approval-roles.create');
    }

    /**
     * Store new approval role
     */
    public function storeApprovalRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:approval_roles,name',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $approvalRole = \App\Models\ApprovalRole::create($validated);
        $this->logActivity('APPROVAL_ROLE_CREATED', "Created approval role: {$approvalRole->name}", $approvalRole);

        return redirect()->route('superadmin.settings.approval-roles')->with('success', "Approval Role {$approvalRole->name} berhasil dibuat.");
    }

    /**
     * Show edit approval role form
     */
    public function editApprovalRole(\App\Models\ApprovalRole $approvalRole)
    {
        $approvalRole->load('levels');

        // Get all users with 'approver' role for selection in all levels
        $approverUsers = User::where('role', 'approver')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        // All levels use the same pool of approver users
        $usersByLevel = [];
        for ($level = 1; $level <= 4; $level++) {
            $usersByLevel[$level] = $approverUsers;
        }

        // Level labels for display
        $levelRoleMap = [
            1 => 'Level 1',
            2 => 'Level 2',
            3 => 'Level 3',
            4 => 'Level 4',
        ];

        $currentLevels = $approvalRole->levels->keyBy('level');

        return view('superadmin.settings.approval-roles.edit', compact('approvalRole', 'usersByLevel', 'currentLevels', 'levelRoleMap'));
    }

    /**
     * Update approval role
     */
    public function updateApprovalRole(Request $request, \App\Models\ApprovalRole $approvalRole)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:approval_roles,name,' . $approvalRole->id,
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'levels' => 'nullable|array',
            'levels.*.user_id' => 'nullable|exists:users,id',
            'levels.*.is_active' => 'boolean',
        ]);

        $approvalRole->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Handle approval levels
        if (isset($validated['levels'])) {
            foreach ($validated['levels'] as $level => $data) {
                $userId = $data['user_id'] ?? null;
                $isActive = isset($data['is_active']) ? (bool) $data['is_active'] : true;

                \App\Models\ApprovalRoleLevel::updateOrCreate(
                    ['approval_role_id' => $approvalRole->id, 'level' => $level],
                    ['user_id' => $userId, 'is_active' => $isActive]
                );
            }
        }

        $this->logActivity('APPROVAL_ROLE_UPDATED', "Updated approval role: {$approvalRole->name}", $approvalRole);

        return redirect()->route('superadmin.settings.approval-roles')->with('success', "Approval Role berhasil diperbarui.");
    }

    /**
     * Delete approval role
     */
    public function deleteApprovalRole(\App\Models\ApprovalRole $approvalRole)
    {
        $name = $approvalRole->name;

        // Clear user associations
        User::where('approval_role_id', $approvalRole->id)->update(['approval_role_id' => null]);

        $approvalRole->delete();
        $this->logActivity('APPROVAL_ROLE_DELETED', "Deleted approval role: {$name}");

        return redirect()->route('superadmin.settings.approval-roles')->with('success', "Approval Role {$name} berhasil dihapus.");
    }

    /**
     * Job Title Management - List all job titles
     */
    public function jobTitles()
    {
        $jobTitles = \App\Models\JobTitle::withCount('users')->orderBy('name')->get();
        return view('superadmin.settings.job-titles.index', compact('jobTitles'));
    }

    /**
     * Show create job title form
     */
    public function createJobTitle()
    {
        return view('superadmin.settings.job-titles.create');
    }

    /**
     * Store new job title
     */
    public function storeJobTitle(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_titles,name',
            'description' => 'nullable|string',
        ]);

        $jobTitle = \App\Models\JobTitle::create($validated);
        $this->logActivity('JOB_TITLE_CREATED', "Created job title: {$jobTitle->name}", $jobTitle);

        return redirect()->route('superadmin.settings.job-titles')->with('success', "Job Title {$jobTitle->name} berhasil dibuat.");
    }

    /**
     * Show edit job title form
     */
    public function editJobTitle(\App\Models\JobTitle $jobTitle)
    {
        return view('superadmin.settings.job-titles.edit', compact('jobTitle'));
    }

    /**
     * Update job title
     */
    public function updateJobTitle(Request $request, \App\Models\JobTitle $jobTitle)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_titles,name,' . $jobTitle->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $jobTitle->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $this->logActivity('JOB_TITLE_UPDATED', "Updated job title: {$jobTitle->name}", $jobTitle);

        return redirect()->route('superadmin.settings.job-titles')->with('success', "Job Title berhasil diperbarui.");
    }

    /**
     * Delete job title
     */
    public function deleteJobTitle(\App\Models\JobTitle $jobTitle)
    {
        $name = $jobTitle->name;

        // Clear user associations
        User::where('job_title_id', $jobTitle->id)->update(['job_title_id' => null]);

        $jobTitle->delete();
        $this->logActivity('JOB_TITLE_DELETED', "Deleted job title: {$name}");

        return redirect()->route('superadmin.settings.job-titles')->with('success', "Job Title {$name} berhasil dihapus.");
    }

    // ==================== CATEGORY MANAGEMENT ====================

    /**
     * List all categories
     */
    public function categories()
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('superadmin.settings.categories.index', compact('categories'));
    }

    /**
     * Store new category
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = \App\Models\Category::create($validated);

        // Sync to Snipe-IT if enabled
        $this->syncCategoryToSnipeit($category);

        $this->logActivity('CATEGORY_CREATED', "Created category: {$category->name}", $category);
        return redirect()->route('superadmin.settings.categories')->with('success', "Category {$category->name} berhasil ditambahkan.");
    }

    /**
     * Update category
     */
    public function updateCategory(Request $request, \App\Models\Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'is_active' => 'boolean',
        ]);

        $category->update($validated);
        $this->logActivity('CATEGORY_UPDATED', "Updated category: {$category->name}", $category);

        return redirect()->route('superadmin.settings.categories')->with('success', "Category {$category->name} berhasil diupdate.");
    }

    /**
     * Delete category
     */
    public function deleteCategory(\App\Models\Category $category)
    {
        $name = $category->name;
        $category->delete();
        $this->logActivity('CATEGORY_DELETED', "Deleted category: {$name}");

        return redirect()->route('superadmin.settings.categories')->with('success', "Category {$name} berhasil dihapus.");
    }

    /**
     * Sync categories from Snipe-IT
     */
    public function syncCategoriesFromSnipeit()
    {
        $enabled = AppSetting::getValue('snipeit_enabled', '0');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            return redirect()->route('superadmin.settings.categories')->with('error', 'Snipe-IT integration tidak aktif.');
        }

        $url = AppSetting::getValue('snipeit_url');
        $token = AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return redirect()->route('superadmin.settings.categories')->with('error', 'Snipe-IT URL atau Token tidak dikonfigurasi.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get(rtrim($url, '/') . '/api/v1/categories', ['limit' => 500]);

            if ($response->successful()) {
                $categories = $response->json()['rows'] ?? [];
                $synced = 0;

                foreach ($categories as $cat) {
                    $existing = \App\Models\Category::where('snipeit_id', $cat['id'])->first();
                    if (!$existing) {
                        \App\Models\Category::create([
                            'name' => $cat['name'],
                            'snipeit_id' => $cat['id'],
                            'is_active' => true,
                        ]);
                        $synced++;
                    }
                }

                $this->logActivity('CATEGORIES_SYNCED', "Synced {$synced} categories from Snipe-IT");
                return redirect()->route('superadmin.settings.categories')->with('success', "Berhasil sync {$synced} kategori baru dari Snipe-IT.");
            }

            return redirect()->route('superadmin.settings.categories')->with('error', 'Gagal mengambil data dari Snipe-IT.');
        } catch (\Exception $e) {
            return redirect()->route('superadmin.settings.categories')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Sync category to Snipe-IT
     */
    private function syncCategoryToSnipeit(\App\Models\Category $category)
    {
        $enabled = AppSetting::getValue('snipeit_enabled', '0');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            return;
        }

        $url = AppSetting::getValue('snipeit_url');
        $token = AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post(rtrim($url, '/') . '/api/v1/categories', [
                        'name' => $category->name,
                        'category_type' => 'asset',
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['payload']['id'])) {
                    $category->update(['snipeit_id' => $data['payload']['id']]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail
        }
    }

    // ==================== ASSET MODEL MANAGEMENT ====================

    /**
     * List all asset models
     */
    public function assetModels()
    {
        $assetModels = \App\Models\AssetModel::with('category')->orderBy('name')->get();
        $categories = \App\Models\Category::active()->orderBy('name')->get();
        return view('superadmin.settings.asset-models.index', compact('assetModels', 'categories'));
    }

    /**
     * Store new asset model
     */
    public function storeAssetModel(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $assetModel = \App\Models\AssetModel::create($validated);

        // Sync to Snipe-IT if enabled
        $this->syncAssetModelToSnipeit($assetModel);

        $this->logActivity('ASSET_MODEL_CREATED', "Created asset model: {$assetModel->name}", $assetModel);
        return redirect()->route('superadmin.settings.asset-models')->with('success', "Asset Model {$assetModel->name} berhasil ditambahkan.");
    }

    /**
     * Update asset model
     */
    public function updateAssetModel(Request $request, \App\Models\AssetModel $assetModel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $assetModel->update($validated);
        $this->logActivity('ASSET_MODEL_UPDATED', "Updated asset model: {$assetModel->name}", $assetModel);

        return redirect()->route('superadmin.settings.asset-models')->with('success', "Asset Model {$assetModel->name} berhasil diupdate.");
    }

    /**
     * Delete asset model
     */
    public function deleteAssetModel(\App\Models\AssetModel $assetModel)
    {
        $name = $assetModel->name;
        $assetModel->delete();
        $this->logActivity('ASSET_MODEL_DELETED', "Deleted asset model: {$name}");

        return redirect()->route('superadmin.settings.asset-models')->with('success', "Asset Model {$name} berhasil dihapus.");
    }

    /**
     * Sync asset models from Snipe-IT
     */
    public function syncModelsFromSnipeit()
    {
        $enabled = AppSetting::getValue('snipeit_enabled', '0');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            return redirect()->route('superadmin.settings.asset-models')->with('error', 'Snipe-IT integration tidak aktif.');
        }

        $url = AppSetting::getValue('snipeit_url');
        $token = AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return redirect()->route('superadmin.settings.asset-models')->with('error', 'Snipe-IT URL atau Token tidak dikonfigurasi.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get(rtrim($url, '/') . '/api/v1/models', ['limit' => 500]);

            if ($response->successful()) {
                $models = $response->json()['rows'] ?? [];
                $synced = 0;

                foreach ($models as $model) {
                    $existing = \App\Models\AssetModel::where('snipeit_id', $model['id'])->first();
                    if (!$existing) {
                        // Find or create category
                        $categoryId = null;
                        if (isset($model['category']['id'])) {
                            $cat = \App\Models\Category::where('snipeit_id', $model['category']['id'])->first();
                            if ($cat) {
                                $categoryId = $cat->id;
                            }
                        }

                        \App\Models\AssetModel::create([
                            'name' => $model['name'],
                            'category_id' => $categoryId,
                            'snipeit_id' => $model['id'],
                            'is_active' => true,
                        ]);
                        $synced++;
                    }
                }

                $this->logActivity('ASSET_MODELS_SYNCED', "Synced {$synced} asset models from Snipe-IT");
                return redirect()->route('superadmin.settings.asset-models')->with('success', "Berhasil sync {$synced} model baru dari Snipe-IT.");
            }

            return redirect()->route('superadmin.settings.asset-models')->with('error', 'Gagal mengambil data dari Snipe-IT.');
        } catch (\Exception $e) {
            return redirect()->route('superadmin.settings.asset-models')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Sync asset model to Snipe-IT
     */
    private function syncAssetModelToSnipeit(\App\Models\AssetModel $assetModel)
    {
        $enabled = AppSetting::getValue('snipeit_enabled', '0');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            return;
        }

        $url = AppSetting::getValue('snipeit_url');
        $token = AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return;
        }

        try {
            $data = ['name' => $assetModel->name];

            // Add category if exists
            if ($assetModel->category && $assetModel->category->snipeit_id) {
                $data['category_id'] = $assetModel->category->snipeit_id;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post(rtrim($url, '/') . '/api/v1/models', $data);

            if ($response->successful()) {
                $respData = $response->json();
                if (isset($respData['payload']['id'])) {
                    $assetModel->update(['snipeit_id' => $respData['payload']['id']]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail
        }
    }

    // ==================== REQUEST TYPE MANAGEMENT ====================

    /**
     * List all request types
     */
    public function requestTypes()
    {
        $requestTypes = \App\Models\RequestType::orderBy('name')->get();
        return view('superadmin.settings.request-types.index', compact('requestTypes'));
    }

    /**
     * Store new request type
     */
    public function storeRequestType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:request_types,slug|regex:/^[A-Z0-9_]+$/',
            'description' => 'nullable|string',
            'allow_quantity' => 'boolean',
        ]);

        $validated['allow_quantity'] = $request->has('allow_quantity');

        $requestType = \App\Models\RequestType::create($validated);
        $this->logActivity('REQUEST_TYPE_CREATED', "Created request type: {$requestType->name}", $requestType);

        return redirect()->route('superadmin.settings.request-types')->with('success', "Request Type {$requestType->name} berhasil ditambahkan.");
    }

    /**
     * Update request type
     */
    public function updateRequestType(Request $request, \App\Models\RequestType $requestType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|regex:/^[A-Z0-9_]+$/|unique:request_types,slug,' . $requestType->id,
            'description' => 'nullable|string',
            'allow_quantity' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $requestType->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'allow_quantity' => $request->has('allow_quantity'),
            'is_active' => $request->has('is_active'),
        ]);

        $this->logActivity('REQUEST_TYPE_UPDATED', "Updated request type: {$requestType->name}", $requestType);

        return redirect()->route('superadmin.settings.request-types')->with('success', "Request Type berhasil diperbarui.");
    }

    /**
     * Delete request type
     */
    public function deleteRequestType(\App\Models\RequestType $requestType)
    {
        $name = $requestType->name;
        $requestType->delete();
        $this->logActivity('REQUEST_TYPE_DELETED', "Deleted request type: {$name}");

        return redirect()->route('superadmin.settings.request-types')->with('success', "Request Type {$name} berhasil dihapus.");
    }

    // ==================== REPLACEMENT REASON MANAGEMENT ====================

    /**
     * List all replacement reasons
     */
    public function replacementReasons()
    {
        $replacementReasons = \App\Models\ReplacementReason::orderBy('name')->get();
        return view('superadmin.settings.replacement-reasons.index', compact('replacementReasons'));
    }

    /**
     * Store new replacement reason
     */
    public function storeReplacementReason(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:replacement_reasons,slug|regex:/^[A-Z0-9_]+$/',
            'description' => 'nullable|string',
        ]);

        $reason = \App\Models\ReplacementReason::create($validated);
        $this->logActivity('REPLACEMENT_REASON_CREATED', "Created replacement reason: {$reason->name}", $reason);

        return redirect()->route('superadmin.settings.replacement-reasons')->with('success', "Replacement Reason {$reason->name} berhasil ditambahkan.");
    }

    /**
     * Update replacement reason
     */
    public function updateReplacementReason(Request $request, \App\Models\ReplacementReason $replacementReason)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|regex:/^[A-Z0-9_]+$/|unique:replacement_reasons,slug,' . $replacementReason->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $replacementReason->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $this->logActivity('REPLACEMENT_REASON_UPDATED', "Updated replacement reason: {$replacementReason->name}", $replacementReason);

        return redirect()->route('superadmin.settings.replacement-reasons')->with('success', "Replacement Reason berhasil diperbarui.");
    }

    /**
     * Delete replacement reason
     */
    public function deleteReplacementReason(\App\Models\ReplacementReason $replacementReason)
    {
        $name = $replacementReason->name;
        $replacementReason->delete();
        $this->logActivity('REPLACEMENT_REASON_DELETED', "Deleted replacement reason: {$name}");

        return redirect()->route('superadmin.settings.replacement-reasons')->with('success', "Replacement Reason {$name} berhasil dihapus.");
    }

    /**
     * Log an activity
     */
    private function logActivity($action, $description, $subject = null)
    {
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
        ]);
    }

    // ==================== CONSUMABLE MANAGEMENT ====================

    /**
     * List consumables from Snipe-IT
     */
    public function consumables(Request $request)
    {
        $enabled = AppSetting::getValue('snipeit_enabled', '0');
        $url = AppSetting::getValue('snipeit_url');
        $token = AppSetting::getValue('snipeit_token');

        $consumables = [];
        $error = null;
        $cacheKey = 'consumables_data';

        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            $error = 'Snipe-IT integration tidak aktif. Silakan aktifkan di Settings > Integration > Snipe-IT.';
            // Try to load cached data
            $consumables = cache()->get($cacheKey, []);
        } elseif (!$url || !$token) {
            $error = 'Snipe-IT belum dikonfigurasi. Silakan konfigurasi di Settings > Integration > Snipe-IT.';
            $consumables = cache()->get($cacheKey, []);
        } else {
            try {
                $search = $request->get('search', '');
                $response = Http::timeout(10)->withHeaders([
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
                    $error = 'Snipe-IT tidak terhubung dan tidak ada data cache tersedia.';
                }
            }
        }

        return view('superadmin.consumables.index', compact('consumables', 'error'));
    }

    /**
     * Delete a single consumable from Snipe-IT
     */
    public function deleteConsumable($id)
    {
        $url = AppSetting::getValue('snipeit_url');
        $token = AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return redirect()->back()->with('error', 'Snipe-IT belum dikonfigurasi.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->delete(rtrim($url, '/') . '/api/v1/consumables/' . $id);

            if ($response->successful()) {
                $this->logActivity('CONSUMABLE_DELETED', "Deleted consumable ID: {$id}");
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

    /**
     * Bulk delete consumables from Snipe-IT
     */
    public function bulkDeleteConsumables(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih.');
        }

        $url = AppSetting::getValue('snipeit_url');
        $token = AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return redirect()->back()->with('error', 'Snipe-IT belum dikonfigurasi.');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($ids as $id) {
            try {
                $response = Http::withHeaders([
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

        $this->logActivity('CONSUMABLES_BULK_DELETED', "Deleted {$successCount} consumables from Snipe-IT");

        return redirect()->back()->with('success', "Berhasil menghapus {$successCount} item. Gagal: {$failCount}.");
    }

    /**
     * Sync/refresh consumables from Snipe-IT (just redirect back to refresh)
     */
    public function syncConsumables()
    {
        return redirect()->route('superadmin.consumables')->with('success', 'Data consumables berhasil di-refresh dari Snipe-IT.');
    }

    public function resignedAssets(Request $request)
    {
        if (!in_array(auth()->user()->role, ['superadmin', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $snipeService = app(\App\Services\SnipeITService::class);
        $snipeitError = null;
        $categories = [];

        if (!$snipeService->isEnabled()) {
            return view('superadmin.resigned-assets.index', ['assets' => collect(), 'snipeitEnabled' => false, 'categories' => [], 'locations' => [], 'activeEmployees' => session('active_employees', [])]);
        }

        // Get only asset type categories from Snipe-IT with error handling
        try {
            $categoriesData = $snipeService->getCategories('asset');
            $categories = $categoriesData['rows'] ?? [];
        } catch (\Exception $e) {
            $snipeitError = 'Snipe-IT tidak terhubung. Beberapa fitur mungkin tidak tersedia.';
            $categories = [];
        }

        // Get unique locations from existing resigned assets
        $locations = \App\Models\ResignedAsset::whereNotNull('location_name')
            ->distinct()
            ->pluck('location_name')
            ->filter()
            ->values()
            ->toArray();

        // Get unique categories from existing resigned assets
        $categoriesResult = \App\Models\ResignedAsset::whereNotNull('category_name')
            ->distinct()
            ->pluck('category_name')
            ->filter()
            ->values()
            ->toArray();

        $query = \App\Models\ResignedAsset::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                    ->orWhere('asset_name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('previous_employee_name', 'like', "%{$search}%")
                    ->orWhere('location_name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Handle multiple locations array
        if ($request->filled('locations') && is_array($request->locations)) {
            $query->whereIn('location_name', $request->locations);
        } elseif ($request->filled('location')) {
            $query->where('location_name', $request->location);
        }
        if ($request->filled('category')) {
            $query->where('category_name', $request->category);
        }

        // Sorting
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['created_at', 'location_name', 'asset_tag', 'asset_name', 'previous_employee_name', 'status'];
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [20, 50, 100, 200, 500]))
            $perPage = 20;

        $assets = $query->paginate($perPage)->withQueryString();

        // Get upload history
        $uploadHistory = \App\Models\ActiveUserUpload::with('uploader')
            ->orderBy('uploaded_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate stats for dashboard cards
        $stats = [
            'total' => \App\Models\ResignedAsset::count(),
            'available' => \App\Models\ResignedAsset::where('status', 'available')->count(),
            'checked_out' => \App\Models\ResignedAsset::where('status', 'checked_out')->count(),
        ];

        return view('superadmin.resigned-assets.index', [
            'assets' => $assets,
            'snipeitEnabled' => true,
            'snipeitError' => $snipeitError,
            'categories' => $categories,
            'categoriesResult' => $categoriesResult,
            'locations' => $locations,
            'activeEmployees' => session('active_employees', []),
            'uploadHistory' => $uploadHistory,
            'isSuperadmin' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Resigned Assets for department users (not superadmin)
     * This method has access control based on department settings
     */
    public function resignedAssetsForUsers(Request $request)
    {
        // Check access permission
        if (!auth()->user()->canAccessAssetResign()) {
            abort(403, 'You do not have permission to access Asset Resign Management.');
        }

        $snipeService = app(\App\Services\SnipeITService::class);
        if (!$snipeService->isEnabled()) {
            return view('superadmin.resigned-assets.index', [
                'assets' => collect(),
                'snipeitEnabled' => false,
                'categories' => [],
                'categoriesResult' => [],
                'locations' => [],
                'activeEmployees' => [],
                'uploadHistory' => [],
                'isSuperadmin' => false
            ]);
        }

        // Get unique locations from existing resigned assets
        $locations = \App\Models\ResignedAsset::whereNotNull('location_name')
            ->distinct()
            ->pluck('location_name')
            ->filter()
            ->values()
            ->toArray();

        // Get unique categories from existing resigned assets
        $categoriesResult = \App\Models\ResignedAsset::whereNotNull('category_name')
            ->distinct()
            ->pluck('category_name')
            ->filter()
            ->values()
            ->toArray();

        $query = \App\Models\ResignedAsset::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                    ->orWhere('asset_name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('previous_employee_name', 'like', "%{$search}%")
                    ->orWhere('location_name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Handle multiple locations array
        if ($request->filled('locations') && is_array($request->locations)) {
            $query->whereIn('location_name', $request->locations);
        } elseif ($request->filled('location')) {
            $query->where('location_name', $request->location);
        }
        if ($request->filled('category')) {
            $query->where('category_name', $request->category);
        }

        // Sorting
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['created_at', 'location_name', 'asset_tag', 'asset_name', 'previous_employee_name', 'status'];
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [20, 50, 100, 200, 500]))
            $perPage = 20;

        $assets = $query->paginate($perPage)->withQueryString();

        // Calculate stats for dashboard cards
        $stats = [
            'total' => \App\Models\ResignedAsset::count(),
            'available' => \App\Models\ResignedAsset::where('status', 'available')->count(),
            'checked_out' => \App\Models\ResignedAsset::where('status', 'checked_out')->count(),
        ];

        return view('superadmin.resigned-assets.index', [
            'assets' => $assets,
            'snipeitEnabled' => true,
            'categories' => [],
            'categoriesResult' => $categoriesResult,
            'locations' => $locations,
            'activeEmployees' => [],
            'uploadHistory' => [],
            'isSuperadmin' => false,
            'stats' => $stats,
        ]);
    }

    public function uploadActiveUsers(Request $request)
    {
        if (!in_array(auth()->user()->role, ['superadmin', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate(['excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);

        try {
            $file = $request->file('excel_file');

            // Validate file exists and is valid
            if (!$file || !$file->isValid()) {
                return back()->with('error', 'File tidak valid atau gagal diupload. Error: ' . ($file ? $file->getErrorMessage() : 'No file'));
            }

            // Store file using move() to temp directory
            $extension = strtolower($file->getClientOriginalExtension());
            $tempFileName = 'temp_active_users_' . time() . '.' . $extension;
            $tempDir = storage_path('app/temp');

            // Create temp directory if not exists
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $file->move($tempDir, $tempFileName);
            $filePath = $tempDir . '/' . $tempFileName;

            if (!file_exists($filePath)) {
                return back()->with('error', 'Gagal menyimpan file sementara.');
            }

            $activeEmployees = [];

            try {
                if ($extension === 'csv') {
                    $handle = fopen($filePath, 'r');
                    if (!$handle) {
                        throw new \Exception('Gagal membuka file CSV.');
                    }
                    $header = fgetcsv($handle);
                    if (!$header) {
                        fclose($handle);
                        throw new \Exception('File CSV kosong atau format tidak valid.');
                    }
                    $empNumIndex = array_search('employee_number', array_map('strtolower', $header));
                    if ($empNumIndex === false) {
                        $empNumIndex = array_search('employee_id', array_map('strtolower', $header));
                    }
                    if ($empNumIndex === false) {
                        $empNumIndex = array_search('nik', array_map('strtolower', $header));
                    }
                    if ($empNumIndex === false) {
                        fclose($handle);
                        throw new \Exception('Kolom employee_number/employee_id/nik tidak ditemukan di file CSV.');
                    }
                    while (($row = fgetcsv($handle)) !== false) {
                        if (isset($row[$empNumIndex]) && !empty(trim($row[$empNumIndex]))) {
                            $activeEmployees[] = trim($row[$empNumIndex]);
                        }
                    }
                    fclose($handle);
                } else {
                    // Excel file (xlsx, xls)
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load($filePath);
                    $rows = $spreadsheet->getActiveSheet()->toArray();

                    if (empty($rows) || empty($rows[0])) {
                        throw new \Exception('File Excel kosong.');
                    }

                    $header = array_map('strtolower', array_map('strval', $rows[0]));
                    $empNumIndex = array_search('employee_number', $header);
                    if ($empNumIndex === false) {
                        $empNumIndex = array_search('employee_id', $header);
                    }
                    if ($empNumIndex === false) {
                        $empNumIndex = array_search('nik', $header);
                    }
                    if ($empNumIndex === false) {
                        throw new \Exception('Kolom employee_number/employee_id/nik tidak ditemukan di file Excel.');
                    }

                    for ($i = 1; $i < count($rows); $i++) {
                        if (isset($rows[$i][$empNumIndex]) && !empty(trim(strval($rows[$i][$empNumIndex])))) {
                            $activeEmployees[] = trim(strval($rows[$i][$empNumIndex]));
                        }
                    }
                }
            } finally {
                // Clean up temp file
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            if (empty($activeEmployees)) {
                return back()->with('error', 'Tidak ada employee number yang ditemukan dalam file.');
            }

            // Save upload history
            \App\Models\ActiveUserUpload::create([
                'filename' => $tempFileName,
                'original_filename' => $file->getClientOriginalName(),
                'employee_count' => count($activeEmployees),
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now(),
            ]);

            session(['active_employees' => $activeEmployees]);
            $this->logActivity('ACTIVE_USERS_UPLOADED', "Uploaded " . count($activeEmployees) . " employees");
            return back()->with('success', 'File berhasil diupload. Ditemukan ' . count($activeEmployees) . ' employee number aktif.');
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function detectResignedUsers(Request $request)
    {
        $activeEmployees = session('active_employees', []);
        \Log::info('Detect Resigned Users Triggered', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role,
            'active_employees_count' => count($activeEmployees),
            'category_id' => $request->get('category_id')
        ]);
        if (empty($activeEmployees))
            return back()->with('error', 'Upload file Excel user aktif terlebih dahulu.');
        $snipeService = app(\App\Services\SnipeITService::class);
        if (!$snipeService->isEnabled())
            return back()->with('error', 'Snipe-IT tidak aktif.');
        try {
            // Fetch ALL assets with pagination
            $allAssets = [];
            $limit = 500;
            $offset = 0;
            $categoryId = $request->get('category_id');

            do {
                $hardware = $snipeService->getHardware($categoryId, $limit, $offset);
                if (!$hardware || !isset($hardware['rows'])) {
                    if ($offset === 0) {
                        return back()->with('error', 'Gagal mengambil data dari Snipe-IT.');
                    }
                    break;
                }

                $allAssets = array_merge($allAssets, $hardware['rows']);
                $total = $hardware['total'] ?? 0;
                $offset += $limit;
            } while ($offset < $total);

            \Log::info('Snipe-IT Fetch Complete', ['total_assets' => count($allAssets)]);

            $importedCount = 0;
            $updatedCount = 0;
            $removedCount = 0;

            // Get all Snipe-IT Asset IDs currently in our database
            $existingSnipeIds = \App\Models\ResignedAsset::pluck('snipeit_asset_id')->toArray();
            $foundSnipeIds = [];

            foreach ($allAssets as $asset) {
                // Check if assigned
                $isAssigned = isset($asset['assigned_to']) && !empty($asset['assigned_to']);

                // If assigned, check if valid employee
                $employeeNum = $asset['assigned_to']['employee_number'] ?? null;
                $isResignedUser = $isAssigned && !empty($employeeNum) && !in_array($employeeNum, $activeEmployees);

                if ($isResignedUser) {
                    // This IS a resigned asset
                    $foundSnipeIds[] = $asset['id'];

                    // Get location
                    $locationName = null;
                    if (isset($asset['rtd_location']) && !empty($asset['rtd_location']['name'])) {
                        $locationName = $asset['rtd_location']['name'];
                    } elseif (isset($asset['location']) && !empty($asset['location']['name'])) {
                        $locationName = $asset['location']['name'];
                    } elseif (isset($asset['assigned_to']['location']) && !empty($asset['assigned_to']['location']['name'])) {
                        $locationName = $asset['assigned_to']['location']['name'];
                    }

                    // Check/Update Existing
                    $existingAsset = \App\Models\ResignedAsset::where('snipeit_asset_id', $asset['id'])->first();
                    if ($existingAsset) {
                        if (empty($existingAsset->location_name) && !empty($locationName)) {
                            $existingAsset->update(['location_name' => $locationName]);
                            $updatedCount++;
                        }
                        continue;
                    }

                    // Create New
                    \App\Models\ResignedAsset::create([
                        'snipeit_asset_id' => $asset['id'],
                        'asset_tag' => $asset['asset_tag'] ?? 'N/A',
                        'asset_name' => $asset['name'] ?? 'Unknown',
                        'serial_number' => $asset['serial'] ?? null,
                        'model_name' => $asset['model']['name'] ?? null,
                        'category_name' => $asset['category']['name'] ?? 'Handphone',
                        'location_name' => $locationName,
                        'previous_employee_number' => $employeeNum,
                        'previous_employee_name' => $asset['assigned_to']['name'] ?? 'Unknown',
                        'status' => 'available',
                    ]);
                    $importedCount++;
                }
            }

            // Cleanup: Remove assets that are no longer "resigned" (e.g., re-assigned to active user or checked in)
            // We do this by finding IDs that were in DB but NOT found in the current "resigned" list from Snipe-IT
            // Note: We only check against the category filtered query. If category filter is active, we should only cleanup that category.

            $queryToRemove = \App\Models\ResignedAsset::whereNotIn('snipeit_asset_id', $foundSnipeIds);

            // stricter safety: if we filtered by CAtegory ID in the fetch, only remove assets of that category from DB to avoid wiping others
            // However, local DB stores category_name, not ID. Snipe API return category object.
            // Ideally we should sync all. If user filtered, $allAssets implies partial list.
            // If request has category_id, we can't safely do a full cleanup unless we filter local DB by that category too.
            // But local DB category_name might differ or we don't have the map easily.
            // SAFE APPROACH: Only clean up if NO Category Filter is applied (Full Scan).

            if (empty($categoryId)) {
                $removedCount = $queryToRemove->delete();
                \Log::info('Cleanup executed', ['removed_count' => $removedCount]);
            } else {
                // If filtered, we can try to match category name if possible, or skip cleanup to be safe.
                // Let's look at the first asset to get category name? No.
                // Let's skip cleanup on filtered search to avoid deleting other categories' data.
                $removedCount = 0;
                \Log::info('Cleanup skipped due to category filter');
            }

            $this->logActivity('RESIGNED_ASSETS_DETECTED', "Detected {$importedCount}, Updated {$updatedCount}, Removed {$removedCount} (Resolved)");

            // Get total count to confirm records persist
            $totalCount = \App\Models\ResignedAsset::count();

            $message = "Berhasil mendeteksi {$importedCount} asset baru.";
            if ($updatedCount > 0)
                $message .= " {$updatedCount} updated.";
            if ($removedCount > 0)
                $message .= " {$removedCount} asset dihapus dari daftar (sudah fixed/checkout).";

            if (!empty($categoryId) && $removedCount == 0) {
                $message .= " (Cleanup skipped due to category filter).";
            }

            $message .= " Total: {$totalCount}.";

            // Send notification to users who can access Asset Resign
            if ($importedCount > 0) {
                $usersToNotify = User::all()->filter(function ($user) {
                    return $user->canAccessAssetResign();
                });

                \Illuminate\Support\Facades\Notification::send($usersToNotify, new \App\Notifications\ResignedAssetsDetectedNotification($importedCount));
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function checkinResignedAsset($id)
    {
        if (!auth()->user()->canAccessAssetResign()) {
            abort(403);
        }
        $asset = \App\Models\ResignedAsset::findOrFail($id);
        $snipeService = app(\App\Services\SnipeITService::class);
        if (!$snipeService->isEnabled())
            return back()->with('error', 'Snipe-IT tidak aktif.');
        try {
            if ($snipeService->checkinAsset($asset->snipeit_asset_id, 'Checked in from resigned user')) {
                $asset->update(['status' => 'available', 'assigned_to_user_id' => null, 'assigned_to_snipeit_user_id' => null, 'assigned_to_name' => null, 'checked_out_at' => null]);
                $this->logActivity('RESIGNED_ASSET_CHECKIN', "Checked in {$asset->asset_tag}", $asset);
                return back()->with('success', "Asset {$asset->asset_tag} berhasil di check-in.");
            }
            return back()->with('error', 'Gagal check-in di Snipe-IT.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function checkoutResignedAsset(Request $request, $id)
    {
        if (!auth()->user()->canAccessAssetResign()) {
            abort(403);
        }
        $request->validate(['snipeit_user_id' => 'required', 'user_name' => 'required|string']);
        $asset = \App\Models\ResignedAsset::findOrFail($id);
        $snipeService = app(\App\Services\SnipeITService::class);
        if (!$snipeService->isEnabled())
            return back()->with('error', 'Snipe-IT tidak aktif.');
        try {
            // Step 1: Checkin the asset first (required by Snipe-IT before checkout to new user)
            $checkinResult = $snipeService->checkinAsset($asset->snipeit_asset_id, 'Preparing for reassignment');
            if (!$checkinResult) {
                \Log::warning('Checkin may have failed for asset ' . $asset->snipeit_asset_id . ', attempting checkout anyway');
            }

            // Step 2: Checkout to new user
            $checkoutResult = $snipeService->checkoutAsset($asset->snipeit_asset_id, $request->snipeit_user_id, 'Checked out to new user from resigned asset management');
            if ($checkoutResult) {
                $asset->update([
                    'status' => 'checked_out',
                    'assigned_to_snipeit_user_id' => $request->snipeit_user_id,
                    'assigned_to_name' => $request->user_name,
                    'checked_out_at' => now()
                ]);
                $this->logActivity('RESIGNED_ASSET_CHECKOUT', "Checked out {$asset->asset_tag} to {$request->user_name}", $asset);
                return back()->with('success', "Asset {$asset->asset_tag} berhasil di check-out ke {$request->user_name}.");
            }
            return back()->with('error', 'Gagal check-out di Snipe-IT. Pastikan user valid.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function searchSnipeitUsersForCheckout(Request $request)
    {
        if (!auth()->user()->canAccessAssetResign()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $snipeService = app(\App\Services\SnipeITService::class);
        if (!$snipeService->isEnabled())
            return response()->json(['error' => 'Snipe-IT tidak aktif'], 400);
        $users = $snipeService->getAllUsers($request->get('q', ''), 20);
        if (!$users || !isset($users['rows']))
            return response()->json(['results' => []]);
        return response()->json(['results' => collect($users['rows'])->map(fn($u) => ['id' => $u['id'], 'text' => $u['name'] . ' (' . ($u['employee_num'] ?? 'No Emp#') . ')', 'name' => $u['name']])]);
    }

    public function deleteResignedAsset($id)
    {
        if (!auth()->user()->canAccessAssetResign()) {
            abort(403);
        }
        $asset = \App\Models\ResignedAsset::findOrFail($id);
        $tag = $asset->asset_tag;
        $asset->delete();
        $this->logActivity('RESIGNED_ASSET_DELETED', "Deleted {$tag}");
        return back()->with('success', "Record {$tag} berhasil dihapus.");
    }

    public function clearActiveUsers()
    {
        if (!in_array(auth()->user()->role, ['superadmin', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        session()->forget('active_employees');
        return back()->with('success', 'Data user aktif dihapus.');
    }

    public function deleteUploadHistory($id)
    {
        if (!in_array(auth()->user()->role, ['superadmin', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $history = \App\Models\ActiveUserUpload::findOrFail($id);
        $filename = $history->original_filename;
        $history->delete();
        $this->logActivity('UPLOAD_HISTORY_DELETED', "Deleted upload history: {$filename}");
        return back()->with('success', "History upload {$filename} berhasil dihapus.");
    }

    public function exportResignedAssetsCsv(Request $request)
    {
        if (!auth()->user()->canAccessAssetResign()) {
            abort(403);
        }
        $query = \App\Models\ResignedAsset::query();

        // Apply same filters as the index page
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                    ->orWhere('asset_name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('previous_employee_name', 'like', "%{$search}%")
                    ->orWhere('location_name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('location')) {
            $query->where('location_name', $request->location);
        }
        if ($request->filled('category')) {
            $query->where('category_name', $request->category);
        }

        $assets = $query->orderBy('created_at', 'desc')->get();

        $filename = 'resigned_assets_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($assets) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'Asset Tag',
                'Nama Asset',
                'Serial Number',
                'Model',
                'Category',
                'Location',
                'User Resign',
                'Employee Number',
                'Status',
                'Assigned To',
                'Checked Out At',
                'Created At'
            ]);

            // CSV Data
            foreach ($assets as $asset) {
                fputcsv($file, [
                    $asset->asset_tag,
                    $asset->asset_name,
                    $asset->serial_number ?? '',
                    $asset->model_name ?? '',
                    $asset->category_name ?? '',
                    $asset->location_name ?? '',
                    $asset->previous_employee_name ?? '',
                    $asset->previous_employee_number ?? '',
                    $asset->status,
                    $asset->assigned_to_name ?? '',
                    $asset->checked_out_at ? $asset->checked_out_at->format('Y-m-d H:i:s') : '',
                    $asset->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        $this->logActivity('RESIGNED_ASSETS_EXPORTED', 'Exported ' . $assets->count() . ' assets to CSV');

        return response()->stream($callback, 200, $headers);
    }

    // ==================== SLA CONFIGURATION ====================

    /**
     * SLA Configuration page (Approval & Fulfillment)
     */
    public function slaConfigs()
    {
        $approvalConfigs = \App\Models\SlaApprovalConfig::orderBy('approval_level')->get();
        $fulfillmentConfigs = \App\Models\SlaFulfillmentConfig::orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")->get();

        return view('superadmin.settings.sla-configs.index', compact('approvalConfigs', 'fulfillmentConfigs'));
    }

    /**
     * Update SLA Approval Config
     */
    public function updateSlaApprovalConfig(Request $request, $id)
    {
        $config = \App\Models\SlaApprovalConfig::findOrFail($id);

        $validated = $request->validate([
            'target_hours' => 'required|integer|min:1|max:720',
            'warning_percent' => 'required|integer|min:10|max:90',
            'escalation_percent' => 'required|integer|min:50|max:99',
            'is_active' => 'boolean',
        ]);

        $config->update([
            'target_hours' => $validated['target_hours'],
            'warning_percent' => $validated['warning_percent'],
            'escalation_percent' => $validated['escalation_percent'],
            'is_active' => $request->has('is_active'),
        ]);

        $this->logActivity('SLA_APPROVAL_UPDATED', "Updated SLA Approval Level {$config->approval_level}");

        return back()->with('success', "SLA Approval Level {$config->approval_level} updated successfully.");
    }

    /**
     * Update SLA Fulfillment Config
     */
    public function updateSlaFulfillmentConfig(Request $request, $id)
    {
        $config = \App\Models\SlaFulfillmentConfig::findOrFail($id);

        $validated = $request->validate([
            'response_hours' => 'required|integer|min:1|max:720',
            'fulfillment_hours' => 'required|integer|min:1|max:720',
            'warning_percent' => 'required|integer|min:10|max:90',
            'is_active' => 'boolean',
        ]);

        $config->update([
            'response_hours' => $validated['response_hours'],
            'fulfillment_hours' => $validated['fulfillment_hours'],
            'warning_percent' => $validated['warning_percent'],
            'is_active' => $request->has('is_active'),
        ]);

        $this->logActivity('SLA_FULFILLMENT_UPDATED', "Updated SLA Fulfillment for priority {$config->priority}");

        return back()->with('success', "SLA Fulfillment for {$config->priority_label} updated successfully.");
    }

    // ==================== SLA REPORT ====================

    /**
     * SLA Report page
     */
    public function slaReport(Request $request)
    {
        $query = \App\Models\Request::with(['requester', 'approvalLogs'])
            ->whereNotIn('status', ['DRAFT']);

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Department filter
        if ($request->filled('department')) {
            $query->whereHas('requester', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate SLA status for each request
        $slaConfigs = \App\Models\SlaFulfillmentConfig::all()->keyBy('priority');

        $requests->getCollection()->transform(function ($req) use ($slaConfigs) {
            $req->sla_data = $this->calculateSlaStatus($req, $slaConfigs);
            return $req;
        });

        // Filter by SLA status if specified
        if ($request->filled('sla_status')) {
            $slaStatusFilter = $request->sla_status;
            $filteredItems = $requests->getCollection()->filter(function ($req) use ($slaStatusFilter) {
                return $req->sla_data['status'] === $slaStatusFilter;
            });
            $requests->setCollection($filteredItems);
        }

        // Calculate summary stats
        $allRequests = \App\Models\Request::whereNotIn('status', ['DRAFT'])->get();
        $stats = [
            'total' => $allRequests->count(),
            'on_time' => 0,
            'at_risk' => 0,
            'breached' => 0,
        ];

        foreach ($allRequests as $req) {
            $slaData = $this->calculateSlaStatus($req, $slaConfigs);
            $stats[$slaData['status'] === 'on-time' ? 'on_time' : ($slaData['status'] === 'at-risk' ? 'at_risk' : 'breached')]++;
        }

        // Departments for filter
        $departments = \App\Models\User::whereNotNull('department')->distinct()->pluck('department');

        return view('superadmin.sla-report.index', compact('requests', 'stats', 'departments'));
    }

    /**
     * Calculate SLA status for a request
     */
    private function calculateSlaStatus($request, $slaConfigs)
    {
        $config = $slaConfigs[$request->priority] ?? null;

        if (!$config) {
            return [
                'status' => 'unknown',
                'target_hours' => 0,
                'actual_hours' => 0,
                'deadline' => null,
                'sla_start' => null,
            ];
        }

        // SLA starts after final approval (APPR_4)
        $finalApproval = $request->approvalLogs()
            ->where('action', 'APPROVE')
            ->orderBy('created_at', 'desc')
            ->first();

        // If not yet approved, check if still in approval process
        $completedStatuses = ['COMPLETED', 'SYNCED', 'REJECTED'];
        $approvalStatuses = ['SUBMITTED', 'APPR_1', 'APPR_2', 'APPR_3'];

        if (in_array($request->status, $approvalStatuses)) {
            return [
                'status' => 'pending',
                'target_hours' => $config->fulfillment_hours,
                'actual_hours' => 0,
                'deadline' => null,
                'sla_start' => null,
                'message' => 'Waiting for approval',
            ];
        }

        // Determine SLA start time
        $slaStartTime = $finalApproval ? $finalApproval->created_at : $request->created_at;
        $deadline = $slaStartTime->copy()->addHours($config->fulfillment_hours);
        $warningTime = $slaStartTime->copy()->addHours($config->fulfillment_hours * ($config->warning_percent / 100));

        // Determine completion time
        if (in_array($request->status, $completedStatuses)) {
            $completionTime = $request->updated_at;
        } else {
            $completionTime = now();
        }

        $actualHours = $slaStartTime->diffInHours($completionTime);

        // Determine status
        if (in_array($request->status, $completedStatuses) && $request->status !== 'REJECTED') {
            $status = $completionTime <= $deadline ? 'on-time' : 'breached';
        } elseif ($request->status === 'REJECTED') {
            $status = 'rejected';
        } elseif (now() > $deadline) {
            $status = 'breached';
        } elseif (now() > $warningTime) {
            $status = 'at-risk';
        } else {
            $status = 'on-time';
        }

        return [
            'status' => $status,
            'target_hours' => $config->fulfillment_hours,
            'actual_hours' => $actualHours,
            'deadline' => $deadline,
            'sla_start' => $slaStartTime,
        ];
    }

    /**
     * Export SLA Report to CSV
     */
    public function exportSlaReportCsv(Request $request)
    {
        $query = \App\Models\Request::with(['requester', 'approvalLogs'])
            ->whereNotIn('status', ['DRAFT']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('department')) {
            $query->whereHas('requester', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();
        $slaConfigs = \App\Models\SlaFulfillmentConfig::all()->keyBy('priority');

        $filename = 'sla_report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($requests, $slaConfigs) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Ticket No',
                'Requester',
                'Department',
                'Priority',
                'Status',
                'Created At',
                'SLA Start',
                'Deadline',
                'Completed At',
                'Target Hours',
                'Actual Hours',
                'SLA Status',
            ]);

            $priorityLabels = [
                'urgent' => 'P1 - Critical',
                'high' => 'P2 - High',
                'medium' => 'P3 - Medium',
                'low' => 'P4 - Low',
            ];

            foreach ($requests as $req) {
                $slaData = $this->calculateSlaStatus($req, $slaConfigs);
                $completedAt = in_array($req->status, ['COMPLETED', 'SYNCED', 'REJECTED']) ? $req->updated_at->format('Y-m-d H:i:s') : '-';

                fputcsv($file, [
                    $req->ticket_no,
                    $req->requester->name ?? 'N/A',
                    $req->requester->department ?? 'N/A',
                    $priorityLabels[$req->priority] ?? $req->priority,
                    $req->status,
                    $req->created_at->format('Y-m-d H:i:s'),
                    $slaData['sla_start'] ? $slaData['sla_start']->format('Y-m-d H:i:s') : '-',
                    $slaData['deadline'] ? $slaData['deadline']->format('Y-m-d H:i:s') : '-',
                    $completedAt,
                    $slaData['target_hours'],
                    $slaData['actual_hours'],
                    strtoupper($slaData['status']),
                ]);
            }

            fclose($file);
        };

        $this->logActivity('SLA_REPORT_EXPORTED', 'Exported SLA report with ' . $requests->count() . ' records');

        return response()->stream($callback, 200, $headers);
    }
}



