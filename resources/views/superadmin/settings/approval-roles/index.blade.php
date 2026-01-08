<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Approval Role</h1>
                    </div>
                    <p class="text-gray-500">Kelola approval role dan levelnya.</p>
                </div>
                <a href="{{ route('superadmin.settings.approval-roles.create') }}"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Approval Role
                </a>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div
                    class="mb-6 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Approval Roles List -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Approval Roles</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $approvalRoles->count() }} approval roles</p>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($approvalRoles as $role)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $role->name }}</p>
                                    @if($role->code)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Code: {{ $role->code }}</p>
                                    @endif
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $role->users_count }}
                                            users</span>
                                        @php
                                            $levelCount = $role->levels->where('is_active', true)->whereNotNull('user_id')->count();
                                        @endphp
                                        @if($levelCount > 0)
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium rounded bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                                {{ $levelCount }} Levels Active
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium rounded bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">
                                                No Levels Assigned
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('superadmin.settings.approval-roles.edit', $role) }}"
                                        class="p-1.5 text-gray-400 hover:text-indigo-600 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <form method="POST"
                                        action="{{ route('superadmin.settings.approval-roles.destroy', $role) }}"
                                        onsubmit="return confirm('Delete this approval role?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 transition"
                                            title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                            No approval roles yet. Create one to get started.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-sidebar-layout>