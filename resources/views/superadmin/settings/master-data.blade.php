<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center mb-8">
                <a href="{{ route('superadmin.settings') }}"
                    class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Master Data</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Role Management -->
                <a href="{{ route('superadmin.settings.roles') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-pink-100 dark:bg-pink-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Role Management</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola peran user dan hak akses
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Approval Role -->
                <a href="{{ route('superadmin.settings.approval-roles') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Approval Role</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola approver untuk setiap level per
                                departemen
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Department Management -->
                <a href="{{ route('superadmin.settings.departments') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-cyan-100 dark:bg-cyan-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Department Management</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage departments and user assignments
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Job Title -->
                <a href="{{ route('superadmin.settings.job-titles') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-amber-100 dark:bg-amber-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Job Title</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola jabatan yang bisa di-mapping ke
                                user
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Request Types -->
                <a href="{{ route('superadmin.settings.request-types') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Request Types</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola jenis request di form pengajuan
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Replacement Reasons -->
                <a href="{{ route('superadmin.settings.replacement-reasons') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-red-100 dark:bg-red-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Replacement Reasons</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola alasan penggantian di form
                                request
                            </p>
                        </div>
                    </div>
                </a>

                <!-- SLA Configuration -->
                <a href="{{ route('superadmin.settings.sla-configs') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">SLA Configuration</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola target SLA approval & fulfillment
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-sidebar-layout>