<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Settings</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- General Settings -->
                <a href="{{ route('superadmin.settings.general') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">General</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Logo & branding settings</p>
                        </div>
                    </div>
                </a>

                <!-- Integration Settings (LDAP & Snipe-IT) -->
                <a href="{{ route('superadmin.settings.integration') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Integration</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">LDAP / Active Directory & Snipe-IT</p>
                        </div>
                    </div>
                </a>

                <!-- Branch List -->
                <a href="{{ route('superadmin.settings.branches') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Branch List</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage branch offices and locations</p>
                        </div>
                    </div>
                </a>

                <!-- Master Data -->
                <a href="{{ route('superadmin.settings.master-data') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-orange-100 dark:bg-orange-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Master Data</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Roles, Departments, Job Titles, Request
                                Types...
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Categories -->
                <a href="{{ route('superadmin.settings.categories') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-teal-100 dark:bg-teal-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Categories</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage categories and sync from Snipe-IT
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Asset Models -->
                <a href="{{ route('superadmin.settings.asset-models') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-rose-100 dark:bg-rose-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Asset Models</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage asset models and sync from
                                Snipe-IT
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Backup & Restore -->
                <a href="{{ route('superadmin.settings.backup') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Backup & Restore</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Backup database and restore from file
                            </p>
                        </div>
                    </div>
                </a>

            </div>
        </div>
</x-sidebar-layout>