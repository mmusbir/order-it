<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome back, Superadmin</h1>
                    <p class="text-gray-500 mt-1">Here is what's happening in the system today.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('superadmin.users.create') }}"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        New User
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <!-- Pending Approvals -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div
                            class="w-12 h-12 bg-orange-100 dark:bg-orange-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        @if($stats['pending_today'] > 0)
                            <span
                                class="text-xs font-medium text-orange-500 bg-orange-50 dark:bg-orange-900/50 px-2 py-1 rounded-full">
                                +{{ $stats['pending_today'] }} today
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Pending Approvals</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $stats['pending_approvals'] }}
                    </p>
                </div>

                <!-- Active Requests -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        @if($stats['active_today'] > 0)
                            <span
                                class="text-xs font-medium text-blue-500 bg-blue-50 dark:bg-blue-900/50 px-2 py-1 rounded-full">
                                +{{ $stats['active_today'] }} today
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Active Requests</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['active_requests'] }}</p>
                </div>

                <!-- Completed Requests -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div
                            class="w-12 h-12 bg-green-100 dark:bg-green-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        @if($stats['completed_today'] > 0)
                            <span
                                class="text-xs font-medium text-green-500 bg-green-50 dark:bg-green-900/50 px-2 py-1 rounded-full">
                                +{{ $stats['completed_today'] }} today
                            </span>
                        @else
                            <span
                                class="text-xs font-medium text-gray-400 bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded-full">
                                all time
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Completed Requests</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_completed'] }}</p>
                </div>

                <!-- Total Users -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div
                            class="w-12 h-12 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        @if($stats['users_today'] > 0)
                            <span
                                class="text-xs font-medium text-purple-500 bg-purple-50 dark:bg-purple-900/50 px-2 py-1 rounded-full">
                                +{{ $stats['users_today'] }} today
                            </span>
                        @else
                            <span
                                class="text-xs font-medium text-gray-400 bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded-full">
                                total
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_users'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Global Integrations Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Global Integrations Status</h2>
                            <a href="{{ route('superadmin.settings') }}"
                                class="text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline">Manage
                                All</a>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- LDAP -->
                            <div
                                class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 border border-transparent dark:border-gray-600">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-12 h-12 bg-violet-100 dark:bg-violet-900 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-violet-600 dark:text-violet-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-semibold text-gray-900 dark:text-white">LDAP Directory</h3>
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-full {{ $stats['ldap_enabled'] ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-600 dark:text-gray-300' }}">
                                                {{ $stats['ldap_enabled'] ? '● Enabled' : '○ Disabled' }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Active Directory Sync</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            {{ $stats['ldap_enabled'] ? 'Manual sync available' : 'Enable in settings to sync users' }}
                                        </p>
                                        <a href="{{ route('superadmin.settings.ldap') }}"
                                            class="text-sm text-indigo-600 dark:text-indigo-400 font-medium mt-1 inline-block">
                                            {{ $stats['ldap_enabled'] ? 'Sync Now' : 'Configure' }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Snipe-IT -->
                            <div
                                class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 border border-transparent dark:border-gray-600">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-semibold text-gray-900 dark:text-white">Snipe-IT Assets</h3>
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-full {{ $stats['snipeit_enabled'] ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-600 dark:text-gray-300' }}">
                                                {{ $stats['snipeit_enabled'] ? '● Connected' : '○ Not Connected' }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Asset Management</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            {{ $stats['snipeit_enabled'] ? 'Syncs on request completion' : 'Enable in settings to connect' }}
                                        </p>
                                        <a href="{{ route('superadmin.settings.snipeit') }}"
                                            class="text-sm text-indigo-600 dark:text-indigo-400 font-medium mt-1 inline-block">
                                            {{ $stats['snipeit_enabled'] ? 'View Logs' : 'Configure' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Module Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Module Quick Actions</h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Manage Users -->
                            <a href="{{ route('superadmin.users') }}"
                                class="group bg-gray-50 dark:bg-gray-700 rounded-xl p-4 hover:bg-indigo-50 dark:hover:bg-indigo-900/50 transition border border-transparent dark:border-gray-600 hover:border-indigo-200 dark:hover:border-indigo-700">
                                <div class="flex justify-between items-start">
                                    <div
                                        class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-300 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mt-3">Manage Users</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Create, edit & manage user accounts
                                </p>
                            </a>

                            <!-- Approval Inbox -->
                            <a href="{{ route('superadmin.requests') }}"
                                class="group bg-gray-50 dark:bg-gray-700 rounded-xl p-4 hover:bg-indigo-50 dark:hover:bg-indigo-900/50 transition border border-transparent dark:border-gray-600 hover:border-indigo-200 dark:hover:border-indigo-700">
                                <div class="flex justify-between items-start">
                                    <div
                                        class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    @if($stats['pending_approvals'] > 0)
                                        <span
                                            class="text-xs font-medium text-orange-500 bg-orange-50 dark:bg-orange-900/50 px-2 py-1 rounded-full">
                                            {{ $stats['pending_approvals'] }} pending
                                        </span>
                                    @else
                                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-300 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mt-3">Approval Inbox</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Review & approve pending requests
                                </p>
                            </a>

                            <!-- Settings & Integration -->
                            <a href="{{ route('superadmin.settings') }}"
                                class="group bg-gray-50 dark:bg-gray-700 rounded-xl p-4 hover:bg-indigo-50 dark:hover:bg-indigo-900/50 transition border border-transparent dark:border-gray-600 hover:border-indigo-200 dark:hover:border-indigo-700">
                                <div class="flex justify-between items-start">
                                    <div
                                        class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-300 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mt-3">Settings & Integration</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Configure LDAP, Snipe-IT & more</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Recent Activity -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700 h-fit">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Recent Activity</h2>
                        <a href="{{ route('superadmin.audit-logs') }}"
                            class="text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline">View
                            All</a>
                    </div>

                    <div class="space-y-3">
                        @forelse($activities->take(10) as $activity)
                            @php
                                $colorClass = 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400';
                                $iconPath = 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                                $action = strtoupper($activity->action);

                                if (str_contains($action, 'CREATE') || str_contains($action, 'ADD')) {
                                    $colorClass = 'bg-purple-100 text-purple-600 dark:bg-purple-900/50 dark:text-purple-400';
                                    $iconPath = 'M12 4v16m8-8H4';
                                } elseif (str_contains($action, 'UPDATE') || str_contains($action, 'EDIT')) {
                                    $colorClass = 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400';
                                    $iconPath = 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z';
                                } elseif (str_contains($action, 'DELETE') || str_contains($action, 'DESTROY')) {
                                    $colorClass = 'bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400';
                                    $iconPath = 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16';
                                } elseif (str_contains($action, 'APPROVE')) {
                                    $colorClass = 'bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400';
                                    $iconPath = 'M5 13l4 4L19 7';
                                } elseif (str_contains($action, 'REJECT')) {
                                    $colorClass = 'bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400';
                                    $iconPath = 'M6 18L18 6M6 6l12 12';
                                } elseif (str_contains($action, 'LOGIN')) {
                                    $colorClass = 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400';
                                    $iconPath = 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1';
                                }
                            @endphp

                            <div class="flex gap-2">
                                <div
                                    class="w-6 h-6 {{ $colorClass }} rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $iconPath }}"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-900 dark:text-white">
                                        <span class="font-medium">{{ $activity->user->name ?? 'System' }}</span>
                                        {{ Str::limit(strtolower($activity->description), 35) }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500">
                                <p>No recent activity recorded.</p>
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('superadmin.audit-logs') }}"
                        class="block w-full mt-6 py-3 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-gray-600 transition text-center">
                        View Audit Logs
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-sidebar-layout>