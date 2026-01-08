<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('superadmin.settings.integration') }}"
                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Back to Integration Settings
                </a>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div
                    class="mb-6 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div
                    class="mb-6 p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.settings.ldap') }}" class="space-y-6">
                @csrf

                <!-- Section 1: Basic Settings -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">LDAP / Active Directory
                                Settings</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Configure connection to LDAP or Active
                                Directory for user sync and authentication.</p>
                        </div>
                    </div>

                    <!-- Enable Toggle -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg mb-5">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Enable LDAP Integration</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Allow syncing and authenticating users
                                from LDAP/AD</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="ldap_enabled" value="1" {{ ($settings['ldap_enabled'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-indigo-600">
                            </div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">LDAP
                                Server</label>
                            <input type="text" name="ldap_server" value="{{ $settings['ldap_server'] ?? '' }}"
                                placeholder="ldap://ldap.company.com atau ldaps://ldap.company.com"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-400 mt-1">Include ldap:// or ldaps:// prefix</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Port</label>
                            <input type="number" name="ldap_port" value="{{ $settings['ldap_port'] ?? 389 }}"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Base
                                DN</label>
                            <input type="text" name="ldap_base_dn" value="{{ $settings['ldap_base_dn'] ?? '' }}"
                                placeholder="DC=company,DC=com"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- AD Options -->
                    <div
                        class="mt-5 p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <h4 class="font-medium text-blue-800 dark:text-blue-300 mb-3">Active Directory Options</h4>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" name="ldap_is_ad" value="1" id="ldap_is_ad" {{ ($settings['ldap_is_ad'] ?? false) ? 'checked' : '' }}
                                    class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                                <label for="ldap_is_ad" class="ms-2 text-sm text-gray-700 dark:text-gray-300">This is an
                                    Active Directory server</label>
                            </div>
                            <div id="ad_domain_wrapper" class="ml-6"
                                style="{{ ($settings['ldap_is_ad'] ?? false) ? '' : 'display:none' }}">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">AD
                                    Domain</label>
                                <input type="text" name="ldap_ad_domain" value="{{ $settings['ldap_ad_domain'] ?? '' }}"
                                    placeholder="ad.company.com"
                                    class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="text-xs text-gray-400 mt-1">Used for User Principal Name authentication
                                    (user@domain)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Security Options -->
                    <div
                        class="mt-5 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <h4 class="font-medium text-yellow-800 dark:text-yellow-300 mb-3">Security Options</h4>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" name="ldap_use_tls" value="1" id="ldap_use_tls" {{ ($settings['ldap_use_tls'] ?? false) ? 'checked' : '' }}
                                    class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="ldap_use_tls" class="ms-2 text-sm text-gray-700 dark:text-gray-300">Use
                                    STARTTLS</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="ldap_ssl_skip_verify" value="1" id="ldap_ssl_skip_verify"
                                    {{ ($settings['ldap_ssl_skip_verify'] ?? false) ? 'checked' : '' }}
                                    class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="ldap_ssl_skip_verify"
                                    class="ms-2 text-sm text-gray-700 dark:text-gray-300">Allow invalid SSL certificate
                                    (for self-signed certs)</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="ldap_password_sync" value="1" id="ldap_password_sync" {{ ($settings['ldap_password_sync'] ?? false) ? 'checked' : '' }}
                                    class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="ldap_password_sync"
                                    class="ms-2 text-sm text-gray-700 dark:text-gray-300">LDAP Password Sync (fallback
                                    to local password if LDAP unavailable)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Bind Settings -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                            </path>
                        </svg>
                        Bind Credentials
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bind
                                Username</label>
                            <input type="text" name="ldap_username" value="{{ $settings['ldap_username'] ?? '' }}"
                                placeholder="CN=Admin,OU=Users,DC=company,DC=com"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bind
                                Password</label>
                            <input type="password" name="ldap_password" value="{{ $settings['ldap_password'] ?? '' }}"
                                placeholder="••••••••"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">LDAP Filter
                                (for sync)</label>
                            <input type="text" name="ldap_filter"
                                value="{{ $settings['ldap_filter'] ?? '(&(objectClass=user)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))' }}"
                                placeholder="(&(objectClass=user))"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-400 mt-1">Filter to find users during sync</p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Authentication
                                Query</label>
                            <input type="text" name="ldap_auth_filter"
                                value="{{ $settings['ldap_auth_filter'] ?? 'sAMAccountName=' }}"
                                placeholder="sAMAccountName= atau uid="
                                class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-400 mt-1">Used to find user when authenticating (AD:
                                sAMAccountName=, LDAP: uid=)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Active Flag
                                Field</label>
                            <input type="text" name="ldap_active_flag" value="{{ $settings['ldap_active_flag'] ?? '' }}"
                                placeholder="active"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-400 mt-1">Optional: LDAP field indicating if user is active</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Field Mapping -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Field Mapping for Syncing
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Map LDAP attributes to user fields. All
                        values should be lowercase.</p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Field
                                    </th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">LDAP
                                        Attribute</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Common
                                        Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                <tr>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Username</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="ldap_username_field"
                                            value="{{ $settings['ldap_username_field'] ?? 'samaccountname' }}"
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">samaccountname / uid</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Email</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="ldap_email_field"
                                            value="{{ $settings['ldap_email_field'] ?? 'mail' }}"
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">mail</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">First Name</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="ldap_fname_field"
                                            value="{{ $settings['ldap_fname_field'] ?? 'givenname' }}"
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">givenname / cn</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Last Name</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="ldap_lname_field"
                                            value="{{ $settings['ldap_lname_field'] ?? 'sn' }}"
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">sn</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Employee Number</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="ldap_emp_num_field"
                                            value="{{ $settings['ldap_emp_num_field'] ?? 'employeenumber' }}"
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">employeenumber</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Department</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="ldap_dept_field"
                                            value="{{ $settings['ldap_dept_field'] ?? 'department' }}"
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">department</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Manager</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="ldap_manager_field"
                                            value="{{ $settings['ldap_manager_field'] ?? 'manager' }}"
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">manager</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Phone</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="ldap_phone_field"
                                            value="{{ $settings['ldap_phone_field'] ?? 'telephonenumber' }}"
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">telephonenumber</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Job Title</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="ldap_jobtitle_field"
                                            value="{{ $settings['ldap_jobtitle_field'] ?? 'title' }}"
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">title</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Location</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="ldap_location_field"
                                            value="{{ $settings['ldap_location_field'] ?? 'physicaldeliveryofficename' }}"
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">physicaldeliveryofficename</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                        Save Settings
                    </button>
                </div>
            </form>

            <!-- Section 4: Test & Sync -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mt-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Test LDAP Settings
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Test Binding -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">Test LDAP Binding</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Test bind credentials and Base DN
                            search</p>
                        <button type="button" id="testBindingBtn"
                            class="w-full py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Test Binding
                        </button>
                        <div id="bindingResult" class="mt-3 hidden"></div>
                    </div>

                    <!-- Test Authentication -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">Test LDAP Login</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Test user authentication</p>
                        <div class="space-y-2 mb-3">
                            <input type="text" id="test_username" placeholder="Username"
                                class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-lg">
                            <input type="password" id="test_password" placeholder="Password"
                                class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-lg">
                        </div>
                        <button type="button" id="testAuthBtn"
                            class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                </path>
                            </svg>
                            Test Login
                        </button>
                        <div id="authResult" class="mt-3 hidden"></div>
                    </div>
                </div>

                <!-- Sync Button -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('superadmin.settings.ldap.sync') }}">
                        @csrf
                        <button type="submit"
                            class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Sync Users from LDAP/Active Directory
                        </button>
                    </form>
                </div>
            </div>

            <!-- Section 5: LDAP Logs -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mt-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">LDAP Integration Logs</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Recent LDAP activity logs for debugging
                            </p>
                        </div>
                    </div>
                    <button type="button" id="refreshLogsBtn"
                        class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">
                        Refresh
                    </button>
                </div>

                <div id="logContainer"
                    class="bg-gray-900 rounded-lg p-4 max-h-96 overflow-y-auto font-mono text-xs text-green-400">
                    <p class="text-gray-500">Loading logs...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle AD Domain field
        document.getElementById('ldap_is_ad').addEventListener('change', function () {
            document.getElementById('ad_domain_wrapper').style.display = this.checked ? 'block' : 'none';
        });

        // Test LDAP Binding
        document.getElementById('testBindingBtn').addEventListener('click', async function () {
            const btn = this;
            const resultDiv = document.getElementById('bindingResult');

            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Testing...';

            try {
                const response = await fetch('{{ route("superadmin.settings.ldap.test-binding") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();

                resultDiv.classList.remove('hidden');
                resultDiv.className = data.success
                    ? 'mt-3 p-3 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700 rounded-lg text-green-700 dark:text-green-300 text-sm'
                    : 'mt-3 p-3 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300 text-sm';
                resultDiv.textContent = data.message;
            } catch (error) {
                resultDiv.classList.remove('hidden');
                resultDiv.className = 'mt-3 p-3 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300 text-sm';
                resultDiv.textContent = 'Connection test failed: ' + error.message;
            }

            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Test Binding</span>';
        });

        // Test LDAP Authentication
        document.getElementById('testAuthBtn').addEventListener('click', async function () {
            const btn = this;
            const resultDiv = document.getElementById('authResult');
            const username = document.getElementById('test_username').value;
            const password = document.getElementById('test_password').value;

            if (!username || !password) {
                resultDiv.classList.remove('hidden');
                resultDiv.className = 'mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-700 rounded-lg text-yellow-700 dark:text-yellow-300 text-sm';
                resultDiv.textContent = 'Please enter username and password.';
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Testing...';

            try {
                const response = await fetch('{{ route("superadmin.settings.ldap.test-auth") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        test_username: username,
                        test_password: password
                    })
                });
                const data = await response.json();

                resultDiv.classList.remove('hidden');
                resultDiv.className = data.success
                    ? 'mt-3 p-3 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700 rounded-lg text-green-700 dark:text-green-300 text-sm'
                    : 'mt-3 p-3 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300 text-sm';
                resultDiv.textContent = data.message;
            } catch (error) {
                resultDiv.classList.remove('hidden');
                resultDiv.className = 'mt-3 p-3 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300 text-sm';
                resultDiv.textContent = 'Authentication test failed: ' + error.message;
            }

            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg><span>Test Login</span>';
        });

        // Load LDAP logs
        async function loadLdapLogs() {
            const container = document.getElementById('logContainer');
            try {
                const response = await fetch('{{ route("superadmin.settings.ldap.logs") }}');
                const data = await response.json();

                if (data.logs && data.logs.length > 0) {
                    container.innerHTML = data.logs.map(log => {
                        let colorClass = 'text-green-400';
                        if (log.includes('[ERROR]') || log.includes('error')) colorClass = 'text-red-400';
                        else if (log.includes('[WARNING]') || log.includes('warning')) colorClass = 'text-yellow-400';
                        else if (log.includes('[INFO]') || log.includes('info')) colorClass = 'text-blue-400';

                        return `<div class="${colorClass} mb-1 border-b border-gray-800 pb-1">${log}</div>`;
                    }).join('');
                } else {
                    container.innerHTML = '<p class="text-gray-500">No LDAP related logs found.</p>';
                }
            } catch (error) {
                container.innerHTML = `<p class="text-red-400">Error loading logs: ${error.message}</p>`;
            }
        }

        document.getElementById('refreshLogsBtn').addEventListener('click', loadLdapLogs);

        // Load logs on page load
        loadLdapLogs();
    </script>
</x-sidebar-layout>