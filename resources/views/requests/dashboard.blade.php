<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header with Welcome Message -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Selamat Datang, {{ explode(' ', Auth::user()->name)[0] }}! 👋
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Berikut ringkasan request Anda hari ini.</p>
            </div>

            <!-- Quick Actions - More Prominent -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <a href="{{ route('products.index') }}"
                    class="group relative overflow-hidden bg-gradient-to-br from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
                    <div class="relative flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Browse Catalog</h3>
                            <p class="text-indigo-100 text-sm">Lihat produk tersedia</p>
                        </div>
                        <svg class="w-5 h-5 text-white/70 ml-auto group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('requests.checkout') }}"
                    class="group relative overflow-hidden bg-gradient-to-br from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
                    <div class="relative flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Buat Request</h3>
                            <p class="text-emerald-100 text-sm">Request IT asset baru</p>
                        </div>
                        <svg class="w-5 h-5 text-white/70 ml-auto group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('requests.index') }}"
                    class="group relative overflow-hidden bg-gradient-to-br from-slate-600 to-slate-700 hover:from-slate-700 hover:to-slate-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
                    <div class="relative flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">My Requests</h3>
                            <p class="text-slate-300 text-sm">Lihat semua request</p>
                        </div>
                        <svg class="w-5 h-5 text-white/70 ml-auto group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Stats Cards - Improved Design -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                <!-- Total Requests -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Request</p>
                </div>

                <!-- Pending Approval -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        @if($stats['pending'] > 0)
                            <span class="flex h-3 w-3">
                                <span
                                    class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-yellow-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                            </span>
                        @endif
                    </div>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pending</p>
                </div>

                <!-- PO Issued -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['po_issued'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">PO Issued</p>
                </div>

                <!-- On Delivery -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['delivery'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">On Delivery</p>
                </div>

                <!-- Completed -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['completed'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Completed</p>
                </div>
            </div>

            <!-- Recent Requests - Improved Table -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request Terbaru</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">5 request terakhir Anda</p>
                    </div>
                    <a href="{{ route('requests.index') }}"
                        class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-medium flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Ticket</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Item</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentRequests as $request)
                                @php
                                    $statusStyles = [
                                        'SUBMITTED' => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300', 'Pending L1'],
                                        'APPR_1' => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300', 'Waiting L2'],
                                        'APPR_2' => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300', 'Waiting L3'],
                                        'APPR_3' => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300', 'Waiting L4'],
                                        'APPR_4' => ['bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300', 'Approved'],
                                        'PO_ISSUED' => ['bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300', 'PO Issued'],
                                        'ON_DELIVERY' => ['bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300', 'On Delivery'],
                                        'COMPLETED' => ['bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300', 'Completed'],
                                        'SYNCED' => ['bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300', 'Synced'],
                                        'REJECTED' => ['bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300', 'Rejected'],
                                    ];
                                    $statusClass = $statusStyles[$request->status][0] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                    $statusLabel = $statusStyles[$request->status][1] ?? $request->status;
                                    $firstItem = $request->items->first();
                                    $itemName = $firstItem ? ($firstItem->item_name ?? $firstItem->product->name ?? 'N/A') : 'No items';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span
                                            class="font-semibold text-gray-900 dark:text-white">{{ $request->ticket_no }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-gray-600 dark:text-gray-300">{{ Str::limit($itemName, 30) }}</span>
                                        @if($request->items->count() > 1)
                                            <span class="text-xs text-gray-400 ml-1">+{{ $request->items->count() - 1 }}
                                                lainnya</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $request->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('requests.show', $request) }}"
                                            class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 text-sm font-medium">
                                            Detail →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <p class="text-gray-500 dark:text-gray-400 mb-2">Belum ada request</p>
                                            <a href="{{ route('requests.checkout') }}"
                                                class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-medium">
                                                Buat request pertama Anda →
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-sidebar-layout>