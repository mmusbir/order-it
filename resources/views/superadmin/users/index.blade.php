<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">User Management</h1>
                    <p class="text-gray-500 mt-1">Manage all user accounts and their roles.</p>
                </div>
                <a href="{{ route('superadmin.users.create') }}"
                    class="flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add User
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

            <!-- Filters -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6 border border-gray-100 dark:border-gray-700">
                <form method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name, email, or employee number..."
                            class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <select name="role"
                        class="px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <select name="per_page"
                        class="px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach([20, 50, 100, 200, 500] as $size)
                            <option value="{{ $size }}" {{ ($perPage ?? 20) == $size ? 'selected' : '' }}>
                                {{ $size }} per page
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="px-4 py-2 text-sm bg-gray-800 dark:bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        Filter
                    </button>
                    <a href="{{ route('superadmin.users') }}"
                        class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Reset
                    </a>
                </form>
            </div>

            @php
                $currentSort = $sortBy ?? 'name';
                $currentDir = $sortDir ?? 'asc';

                function sortUrl($column, $currentSort, $currentDir)
                {
                    $newDir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
                    return request()->fullUrlWithQuery(['sort' => $column, 'dir' => $newDir]);
                }

                function sortIcon($column, $currentSort, $currentDir)
                {
                    if ($currentSort !== $column) {
                        return '<svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>';
                    }
                    return $currentDir === 'asc'
                        ? '<svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>'
                        : '<svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                }
            @endphp

            <!-- Users Table -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-3 py-3 text-left font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                    <a href="{{ sortUrl('employee_number', $currentSort, $currentDir) }}"
                                        class="flex items-center gap-1 hover:text-indigo-600">
                                        Emp No {!! sortIcon('employee_number', $currentSort, $currentDir) !!}
                                    </a>
                                </th>
                                <th
                                    class="px-3 py-3 text-left font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                    <a href="{{ sortUrl('name', $currentSort, $currentDir) }}"
                                        class="flex items-center gap-1 hover:text-indigo-600">
                                        Name {!! sortIcon('name', $currentSort, $currentDir) !!}
                                    </a>
                                </th>
                                <th
                                    class="px-3 py-3 text-left font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                    <a href="{{ sortUrl('email', $currentSort, $currentDir) }}"
                                        class="flex items-center gap-1 hover:text-indigo-600">
                                        Email {!! sortIcon('email', $currentSort, $currentDir) !!}
                                    </a>
                                </th>
                                <th
                                    class="px-3 py-3 text-left font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                    <a href="{{ sortUrl('role', $currentSort, $currentDir) }}"
                                        class="flex items-center gap-1 hover:text-indigo-600">
                                        Role {!! sortIcon('role', $currentSort, $currentDir) !!}
                                    </a>
                                </th>
                                <th
                                    class="px-3 py-3 text-left font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                    <a href="{{ sortUrl('department', $currentSort, $currentDir) }}"
                                        class="flex items-center gap-1 hover:text-indigo-600">
                                        Dept {!! sortIcon('department', $currentSort, $currentDir) !!}
                                    </a>
                                </th>
                                <th
                                    class="px-3 py-3 text-left font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                    <a href="{{ sortUrl('job_title_id', $currentSort, $currentDir) }}"
                                        class="flex items-center gap-1 hover:text-indigo-600">
                                        Job Title {!! sortIcon('job_title_id', $currentSort, $currentDir) !!}
                                    </a>
                                </th>
                                <th
                                    class="px-3 py-3 text-left font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                    <a href="{{ sortUrl('approval_role_id', $currentSort, $currentDir) }}"
                                        class="flex items-center gap-1 hover:text-indigo-600">
                                        Approval {!! sortIcon('approval_role_id', $currentSort, $currentDir) !!}
                                    </a>
                                </th>
                                <th
                                    class="px-3 py-3 text-left font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-3 py-2 font-mono text-gray-600 dark:text-gray-400">
                                        {{ $user->employee_number ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-7 h-7 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span
                                                    class="text-indigo-600 dark:text-indigo-400 font-bold text-[10px]">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                            </div>
                                            <span
                                                class="font-medium text-gray-900 dark:text-white truncate max-w-[120px]">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400 truncate max-w-[150px]">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-3 py-2">
                                        @php
                                            $roleColors = [
                                                'superadmin' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                                'admin' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
                                                'approver' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                                'requester' => 'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300',
                                            ];
                                        @endphp
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400 truncate max-w-[100px]">
                                        {{ $user->department ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400 truncate max-w-[100px]">
                                        {{ $user->jobTitle->name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($user->approvalRole)
                                            <span
                                                class="px-2 py-0.5 text-[10px] font-medium rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                                                {{ $user->approvalRole->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex gap-1">
                                            <a href="{{ route('superadmin.users.edit', $user) }}"
                                                class="p-1 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}"
                                                    onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-1 text-red-600 hover:text-red-800 dark:text-red-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">No users
                                        found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                        {{ $users->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-sidebar-layout>