<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Settings > Branch List</p>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Branch Management</h1>
                    <p class="text-gray-500 mt-1">Manage all branch offices.</p>
                </div>
                <div class="flex flex-col gap-4 items-end">
                    <form action="{{ route('superadmin.settings.branches') }}" method="GET" class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by ID or Name..."
                            class="pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500 w-64 text-sm transition-all shadow-sm">
                    </form>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('superadmin.settings.branches.template') }}"
                            class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Template
                        </a>
                        <a href="{{ route('superadmin.settings.branches.export') }}"
                            class="px-3 py-2 bg-green-100 dark:bg-green-900 hover:bg-green-200 dark:hover:bg-green-800 text-green-700 dark:text-green-300 rounded-lg font-medium transition text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Export
                        </a>
                        <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                            class="px-3 py-2 bg-blue-100 dark:bg-blue-900 hover:bg-blue-200 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-lg font-medium transition text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                </path>
                            </svg>
                            Import
                        </button>
                        <a href="{{ route('superadmin.settings.branches.create') }}"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            Add Branch
                        </a>
                    </div>
                </div>
            </div>

            <!-- Import Modal -->
            <div id="importModal"
                class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
                <div
                    class="relative p-5 border w-96 shadow-lg rounded-xl bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700">
                    <div class="mt-3">
                        <div class="text-center mb-4">
                            <div
                                class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Import Branches CSV
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Upload a CSV file to import
                                branches.
                                Make sure to use the template.</p>
                        </div>

                        <form action="{{ route('superadmin.settings.branches.import') }}" method="POST"
                            enctype="multipart/form-data" class="mt-4">
                            @csrf

                            <div class="mb-4 space-y-2">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Import Mode:</p>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="import_mode" value="add" checked
                                        class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Add New (Skip
                                        existing)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="import_mode" value="update"
                                        class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Update (Match ID &
                                        Name)</span>
                                </label>
                            </div>

                            <input type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                dark:file:bg-indigo-900 dark:file:text-indigo-300
                                hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800
                            " />
                            <div class="flex gap-2 mt-6">
                                <button type="button"
                                    onclick="document.getElementById('importModal').classList.add('hidden')"
                                    class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="flex-1 px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                    Import
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div
                    class="mb-6 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Branches Table -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                
                <!-- Per page selector -->
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Show</span>
                        <select onchange="window.location.href=this.value" 
                            class="text-xs border border-gray-200 dark:border-gray-600 dark:bg-gray-700 rounded px-2 py-1">
                            @foreach([20, 50, 100, 200, 500] as $size)
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => $size]) }}" 
                                    {{ request('per_page', 20) == $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                        <span class="text-xs text-gray-500">entries</span>
                    </div>
                    <span class="text-xs text-gray-500">Total: {{ $branches->total() }} branches</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                    ID Cabang</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                    Nama Cabang</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                    PIC Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                    Phone</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-64">
                                    Alamat</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                    Maps</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($branches as $branch)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-2">
                                        <span
                                            class="text-xs font-mono bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded">{{ $branch->branch_code }}</span>
                                    </td>
                                    <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $branch->name }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ $branch->pic_name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ $branch->phone ?? '-' }}</td>
                                    <td class="px-4 py-2 text-gray-500 dark:text-gray-400 whitespace-normal min-w-[180px]">
                                        {{ Str::limit($branch->address ?? '-', 40) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($branch->google_maps_url)
                                            <a href="{{ $branch->google_maps_url }}" target="_blank"
                                                class="text-indigo-600 hover:text-indigo-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($branch->is_active)
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">Active</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('superadmin.settings.branches.edit', $branch) }}"
                                                class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition"
                                                title="Edit Branch">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <form method="POST"
                                                action="{{ route('superadmin.settings.branches.destroy', $branch) }}"
                                                class="inline"
                                                onsubmit="return confirm('Are you sure you want to delete this branch?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition"
                                                    title="Delete Branch">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <p class="text-gray-500 dark:text-gray-400">No branches found. <a
                                                href="{{ route('superadmin.settings.branches.create') }}"
                                                class="text-indigo-600 hover:underline">Add your first branch</a>.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($branches->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            Showing {{ $branches->firstItem() }} to {{ $branches->lastItem() }} of {{ $branches->total() }} entries
                        </div>
                        <div>{{ $branches->links() }}</div>
                    </div>
                @endif
            </div>

            <!-- Back to Settings -->
            <div class="mt-6">
                <a href="{{ route('superadmin.settings') }}"
                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Back to Settings
                </a>
            </div>
        </div>
    </div>
</x-sidebar-layout>