<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">User Management</h1>
                    <p class="text-gray-500 mt-1">Manage all user accounts and their roles.</p>
                </div>
                <a href="{{ route('admin.users.create') }}"
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
                    <a href="{{ route('admin.users') }}"
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
                                    <a href="{{ sortUrl('employee_number', $currentSort, $currentDir) }}"
                                        class="flex items-center gap-1 hover:text-indigo-600">
                                        Emp ID {!! sortIcon('employee_number', $currentSort, $currentDir) !!}
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
                                    Approval Role
                                </th>
                                <th class="px-3 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-3 py-3">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}
                                        </div>
                                        <div class="text-gray-400 text-[10px]">
                                            {{ $user->approvalRole?->name ?? 'No Approval Role' }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-3 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $user->employee_number ?? '-' }}
                                    </td>
                                    <td class="px-3 py-3">
                                        @php
                                            $roleColors = [
                                                'superadmin' => 'bg-purple-100 text-purple-700',
                                                'admin' => 'bg-blue-100 text-blue-700',
                                                'requester' => 'bg-gray-100 text-gray-700',
                                                'approver' => 'bg-orange-100 text-orange-700',
                                            ];
                                            $color = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $color }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $user->department ?? '-' }}
                                    </td>
                                    <td class="px-3 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $user->approvalRole->name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($user->role !== 'superadmin')
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    Edit
                                                </a>

                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                    class="inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-xs italic">Protected</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        No users found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-sidebar-layout>