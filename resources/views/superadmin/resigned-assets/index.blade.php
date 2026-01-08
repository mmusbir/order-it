<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Asset Resign Management
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div
                    class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg dark:bg-green-900/30 dark:border-green-800 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div
                    class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg dark:bg-red-900/30 dark:border-red-800 dark:text-red-400">
                    {{ session('error') }}
                </div>
            @endif
            @if(isset($snipeitError) && $snipeitError)
                <div
                    class="mb-4 p-4 bg-yellow-100 border border-yellow-200 text-yellow-700 rounded-lg dark:bg-yellow-900/30 dark:border-yellow-800 dark:text-yellow-400 flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    {{ $snipeitError }}
                </div>
            @endif

            @if(!$snipeitEnabled)
                <div
                    class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 text-center">
                    <svg class="w-12 h-12 mx-auto text-yellow-500 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-300 mb-2">Snipe-IT Tidak Aktif</h3>
                    <p class="text-yellow-700 dark:text-yellow-400 mb-4">Silakan konfigurasi dan aktifkan integrasi Snipe-IT
                        terlebih dahulu.</p>
                    <a href="{{ route('superadmin.settings.snipeit') }}"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        Konfigurasi Snipe-IT
                    </a>
                </div>
            @else
                <!-- Dashboard Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <!-- Total Assets Card -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Asset Resign</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Available Assets Card -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Status Available</p>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $stats['available'] ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Checked Out Assets Card -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Status Checked Out</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ $stats['checked_out'] ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg" x-data="resignedAssetManager()">
                    @if($isSuperadmin ?? false)
                        <!-- Step 1: Upload Excel (Superadmin Only) -->
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 text-sm font-bold mr-2">1</span>
                                Upload Daftar User Aktif
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 ml-10">
                                Upload file Excel/CSV yang berisi data karyawan aktif. File harus memiliki kolom <code
                                    class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">employee_number</code>, <code
                                    class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">employee_id</code>, atau <code
                                    class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">nik</code>.
                            </p>

                            <div class="ml-10 flex flex-wrap gap-4 items-end">
                                <form method="POST" action="{{ route('superadmin.resigned-assets.upload') }}"
                                    enctype="multipart/form-data" class="flex gap-3 items-end">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File
                                            Excel/CSV</label>
                                        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required
                                            class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-400">
                                    </div>
                                    <button type="submit"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        Upload
                                    </button>
                                </form>

                                @if(count($activeEmployees) > 0)
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-3 py-2 bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-400 rounded-lg text-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            {{ count($activeEmployees) }} user aktif terdeteksi
                                        </span>
                                        <form method="POST" action="{{ route('superadmin.resigned-assets.clear-active-users') }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm"
                                                title="Hapus data">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <!-- Upload History -->
                            @if(isset($uploadHistory) && count($uploadHistory) > 0)
                                <div class="mt-4 ml-10">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">History Upload:</h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th
                                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                        File</th>
                                                    <th
                                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                        Jumlah</th>
                                                    <th
                                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                        Uploader</th>
                                                    <th
                                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                        Waktu</th>
                                                    <th
                                                        class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                                        Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($uploadHistory as $upload)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                        <td class="px-3 py-2 text-gray-900 dark:text-white">
                                                            {{ $upload->original_filename }}
                                                        </td>
                                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">
                                                            {{ $upload->employee_count }} user
                                                        </td>
                                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">
                                                            {{ $upload->uploader->name ?? 'Unknown' }}
                                                        </td>
                                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">
                                                            {{ $upload->uploaded_at->format('d M Y H:i') }}
                                                        </td>
                                                        <td class="px-3 py-2 text-center">
                                                            <form method="POST"
                                                                action="{{ route('superadmin.resigned-assets.delete-upload-history', $upload->id) }}"
                                                                onsubmit="return confirm('Hapus history ini?')" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-800"
                                                                    title="Hapus">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                        </path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Step 2: Detect Resigned Users -->
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 text-sm font-bold mr-2">2</span>
                                Deteksi Asset dari User Resign
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 ml-10">
                                Klik tombol di bawah untuk mendeteksi asset yang di-assign ke user yang tidak ada dalam daftar
                                user aktif (resign).
                            </p>

                            <div class="ml-10">
                                <form method="POST" action="{{ route('superadmin.resigned-assets.detect') }}"
                                    class="flex gap-3 items-end">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter
                                            Category (opsional)</label>
                                        <select name="category_id"
                                            class="w-64 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                            <option value="">Semua Category</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium flex items-center gap-2"
                                        {{ count($activeEmployees) == 0 ? 'disabled' : '' }}>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Detect Resigned Users
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Step 3: Asset List (Visible to all with access) -->
                    <div class="p-6">
                        <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 text-sm font-bold mr-2">3</span>
                                Daftar Asset dari User Resign
                            </h3>

                            <div class="flex gap-2 items-center">
                                <!-- Export CSV Button -->
                                <a href="{{ ($isSuperadmin ?? false) ? route('superadmin.resigned-assets.export-csv', request()->query()) : route('asset-resign.export-csv', request()->query()) }}"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Export CSV
                                </a>
                            </div>
                        </div>

                        <!-- Search & Filter -->
                        <form method="GET" class="flex gap-2 flex-wrap mb-4 items-end" x-data="locationFilter()">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari asset..."
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white">
                            <select name="status"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white">
                                <option value="">Semua Status</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available
                                </option>
                                <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked
                                    Out</option>
                            </select>
                            <select name="category"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white">
                                <option value="">Semua Category</option>
                                @foreach($categoriesResult ?? [] as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Multi-select Location Dropdown -->
                            <div class="relative">
                                <button type="button" @click="open = !open"
                                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white min-w-[160px] text-left flex justify-between items-center">
                                    <span
                                        x-text="selectedCount > 0 ? selectedCount + ' Location' : 'Semua Location'"></span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false" x-cloak
                                    class="absolute z-50 mt-1 w-64 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg">
                                    <!-- Search -->
                                    <div class="p-2 border-b border-gray-200 dark:border-gray-600">
                                        <input type="text" x-model="searchTerm" placeholder="Cari location..."
                                            class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-600 dark:text-white">
                                    </div>
                                    <!-- Options -->
                                    <div class="max-h-48 overflow-y-auto p-2 space-y-1">
                                        <template x-for="loc in filteredLocations" :key="loc">
                                            <label
                                                class="flex items-center gap-2 px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 rounded cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                                                <input type="checkbox" :value="loc" x-model="selectedLocations"
                                                    class="rounded text-indigo-600 focus:ring-indigo-500">
                                                <span x-text="loc" class="truncate"></span>
                                            </label>
                                        </template>
                                        <div x-show="filteredLocations.length === 0"
                                            class="text-center text-gray-500 text-sm py-2">
                                            Tidak ditemukan
                                        </div>
                                    </div>
                                    <!-- Clear All -->
                                    <div class="p-2 border-t border-gray-200 dark:border-gray-600">
                                        <button type="button" @click="selectedLocations = []"
                                            class="text-xs text-red-600 hover:text-red-800">Clear All</button>
                                    </div>
                                </div>

                                <!-- Hidden inputs for selected locations -->
                                <template x-for="loc in selectedLocations" :key="loc">
                                    <input type="hidden" name="locations[]" :value="loc">
                                </template>
                            </div>

                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search
                            </button>
                            <a href="{{ ($isSuperadmin ?? false) ? route('superadmin.resigned-assets') : route('asset-resign.index') }}"
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm font-medium flex items-center gap-2 shadow-sm border border-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Reset Filter
                            </a>
                        </form>

                        <script>
                            function locationFilter() {
                                return {
                                    open: false,
                                    searchTerm: '',
                                    locations: @json($locations ?? []),
                                    selectedLocations: @json(request('locations', [])),
                                    get filteredLocations() {
                                        if (!this.searchTerm) return this.locations;
                                        return this.locations.filter(loc =>
                                            loc.toLowerCase().includes(this.searchTerm.toLowerCase())
                                        );
                                    },
                                    get selectedCount() {
                                        return this.selectedLocations.length;
                                    }
                                }
                            }
                        </script>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            @php
                                $currentSort = request('sort', 'created_at');
                                $currentDirection = request('direction', 'desc');
                                $sortLink = fn($column) => request()->fullUrlWithQuery([
                                    'sort' => $column,
                                    'direction' => $currentSort === $column && $currentDirection === 'asc' ? 'desc' : 'asc'
                                ]);
                                $sortIcon = fn($column) => $currentSort === $column
                                    ? ($currentDirection === 'asc' ? '↑' : '↓')
                                    : '↕';
                            @endphp
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            <a href="{{ $sortLink('asset_tag') }}"
                                                class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                Asset Tag {{ $sortIcon('asset_tag') }}
                                            </a>
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            <a href="{{ $sortLink('asset_name') }}"
                                                class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                Nama Asset {{ $sortIcon('asset_name') }}
                                            </a>
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            Serial Number
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            <a href="{{ $sortLink('location_name') }}"
                                                class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                Location {{ $sortIcon('location_name') }}
                                            </a>
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            <a href="{{ $sortLink('previous_employee_name') }}"
                                                class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                User Resign {{ $sortIcon('previous_employee_name') }}
                                            </a>
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            <a href="{{ $sortLink('status') }}"
                                                class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                Status {{ $sortIcon('status') }}
                                            </a>
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            Assigned To
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($assets as $asset)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">
                                                {{ $asset->asset_tag }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $asset->asset_name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $asset->model_name }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">
                                                {{ $asset->serial_number ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $asset->location_name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    {{ $asset->previous_employee_name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $asset->previous_employee_number }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($asset->status === 'available')
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-400">
                                                        Available
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-400">
                                                        Checked Out
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                @if($asset->assigned_to_name)
                                                    {{ $asset->assigned_to_name }}
                                                    <div class="text-xs text-gray-500">
                                                        {{ $asset->checked_out_at?->format('d M Y H:i') }}
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex justify-center gap-2">
                                                    @if($asset->status === 'available')
                                                        <!-- Check-in (if still assigned in Snipe-IT) -->
                                                        <form method="POST"
                                                            action="{{ route('superadmin.resigned-assets.checkin', $asset->id) }}"
                                                            class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="px-3 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600"
                                                                title="Check-in di Snipe-IT">
                                                                Check-in
                                                            </button>
                                                        </form>

                                                        <!-- Checkout to new user -->
                                                        <button type="button"
                                                            @click="openCheckoutModal({{ $asset->id }}, '{{ $asset->asset_tag }}')"
                                                            class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                                                            Checkout
                                                        </button>
                                                    @else
                                                        <!-- Check-in to make available again -->
                                                        <form method="POST"
                                                            action="{{ route('superadmin.resigned-assets.checkin', $asset->id) }}"
                                                            class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="px-3 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600">
                                                                Check-in
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <!-- Delete record -->
                                                    <form method="POST"
                                                        action="{{ route('superadmin.resigned-assets.delete', $asset->id) }}"
                                                        onsubmit="return confirm('Hapus record ini?')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700"
                                                            title="Hapus record">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
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
                                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                    </path>
                                                </svg>
                                                Belum ada asset dari user resign. Upload file Excel dan klik "Detect Resigned
                                                Users" untuk memulai.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Show</span>
                                <form method="GET" class="inline-block">
                                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                                        @if(is_array($value))
                                            @foreach($value as $v)
                                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                            @endforeach
                                        @else
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endif
                                    @endforeach
                                    <select name="per_page" onchange="this.form.submit()"
                                        class="text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                        @foreach([20, 50, 100, 200, 500] as $size)
                                            <option value="{{ $size }}" {{ request('per_page') == $size ? 'selected' : '' }}>
                                                {{ $size }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                                <span class="text-sm text-gray-700 dark:text-gray-300">entries</span>
                            </div>

                            <div class="flex-1 flex justify-end">
                                {{ $assets->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>

                    <!-- Checkout Modal -->
                    <div x-show="showCheckoutModal" x-cloak
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                                Checkout Asset <span x-text="checkoutAssetTag"></span>
                            </h3>

                            <form :action="checkoutUrl" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih
                                        User Baru</label>
                                    <div class="relative">
                                        <input type="text" x-model="userSearch" @input="searchUsers()"
                                            placeholder="Ketik nama atau employee number..."
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">

                                        <!-- Search Results -->
                                        <div x-show="userResults.length > 0"
                                            class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                            <template x-for="user in userResults" :key="user.id">
                                                <div @click="selectUser(user)"
                                                    class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-900 dark:text-white">
                                                    <span x-text="user.text"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div x-show="selectedUser"
                                        class="mt-2 p-2 bg-green-100 dark:bg-green-900/50 rounded-lg text-green-800 dark:text-green-400 text-sm">
                                        User dipilih: <span x-text="selectedUser?.name"></span>
                                    </div>

                                    <input type="hidden" name="snipeit_user_id" x-model="selectedUserId">
                                    <input type="hidden" name="user_name" x-model="selectedUserName">
                                </div>

                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="showCheckoutModal = false"
                                        class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500">
                                        Batal
                                    </button>
                                    <button type="submit" :disabled="!selectedUserId"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Checkout
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function resignedAssetManager() {
            return {
                showCheckoutModal: false,
                checkoutAssetId: null,
                checkoutAssetTag: '',
                checkoutUrl: '',
                userSearch: '',
                userResults: [],
                selectedUser: null,
                selectedUserId: '',
                selectedUserName: '',
                searchTimeout: null,

                openCheckoutModal(assetId, assetTag) {
                    this.checkoutAssetId = assetId;
                    this.checkoutAssetTag = assetTag;
                    this.checkoutUrl = '{{ url("superadmin/resigned-assets") }}/' + assetId + '/checkout';
                    this.userSearch = '';
                    this.userResults = [];
                    this.selectedUser = null;
                    this.selectedUserId = '';
                    this.selectedUserName = '';
                    this.showCheckoutModal = true;
                },

                searchUsers() {
                    clearTimeout(this.searchTimeout);
                    if (this.userSearch.length < 2) {
                        this.userResults = [];
                        return;
                    }

                    this.searchTimeout = setTimeout(() => {
                        fetch('{{ route("superadmin.resigned-assets.search-users") }}?q=' + encodeURIComponent(this.userSearch))
                            .then(res => res.json())
                            .then(data => {
                                this.userResults = data.results || [];
                            });
                    }, 300);
                },

                selectUser(user) {
                    this.selectedUser = user;
                    this.selectedUserId = user.id;
                    this.selectedUserName = user.name;
                    this.userSearch = user.text;
                    this.userResults = [];
                }
            }
        }
    </script>
</x-sidebar-layout>