<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('superadmin.settings') }}"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Role Management</h1>
                    </div>
                    <p class="text-gray-500">Lihat daftar role yang tersedia di sistem.</p>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div
                    class="mb-6 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Role Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                @php
                    $roleColors = [
                        'requester' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                        'approver' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                        'admin' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
                        'superadmin' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                    ];
                @endphp
                @foreach($roleModels as $role)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-100 dark:border-gray-700 text-center">
                        <span
                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $roleColors[$role->slug] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300' }}">
                            {{ ucfirst($role->slug) }}
                        </span>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $roleCounts[$role->slug] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">users</p>
                    </div>
                @endforeach
            </div>

            <!-- Roles Table -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Role</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Slug</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Description</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Users</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($roleModels as $role)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $roleColors[$role->slug] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300' }}">
                                            {{ $role->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $role->slug }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ Str::limit($role->description, 50) }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $roleCounts[$role->slug] ?? 0 }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('superadmin.users', ['role' => $role->slug]) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        View Users
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Info Box -->
            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Untuk assign role ke user, gunakan menu <a href="{{ route('superadmin.users') }}"
                        class="font-semibold underline">User Management</a>.
                </p>
            </div>
        </div>
    </div>
</x-sidebar-layout>