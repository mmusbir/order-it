<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                    <p class="text-gray-500 mt-1">Welcome back, {{ Auth::user()->name }}. Here's an overview of your IT
                        requests.</p>
                </div>
                <a href="{{ route('requests.checkout') }}"
                    class="flex items-center px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-full shadow transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Request
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <!-- Total Requests -->
                <a href="{{ route('requests.index', ['status' => 'all']) }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-200 transition cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Requests</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        </div>
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Click to view all →</p>
                </a>

                <!-- Pending Approval -->
                <a href="{{ route('requests.index', ['status' => 'waiting']) }}"
                    class="bg-gradient-to-br from-amber-50 to-yellow-50 dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-amber-100 hover:shadow-md hover:border-amber-200 transition cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Pending Approval</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['pending'] ?? 0 }}</p>
                            <p class="text-xs text-amber-600 mt-1">Waiting for approval</p>
                        </div>
                        <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Click to view →</p>
                </a>

                <!-- Approved -->
                <a href="{{ route('requests.index', ['status' => 'approved']) }}"
                    class="bg-gradient-to-br from-cyan-50 to-blue-50 dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-cyan-100 hover:shadow-md hover:border-cyan-200 transition cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Approved</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['approved'] ?? 0 }}
                            </p>
                            <p class="text-xs text-cyan-600 mt-1">Processing PO</p>
                        </div>
                        <div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Click to view →</p>
                </a>



                <!-- Completed -->
                <a href="{{ route('requests.index', ['status' => 'completed']) }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-200 transition cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Completed</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['completed'] }}</p>
                        </div>
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Click to view →</p>
                </a>
            </div>

            <!-- Recent Requests -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Recent Requests</h2>
                    <a href="{{ route('requests.index') }}"
                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                        View All
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>

                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Request ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Item Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date Requested
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($requests as $request)
                            @php
                                $statusStyles = [
                                    'SUBMITTED' => ['bg-yellow-100 text-yellow-800', 'Pending L1'],
                                    'APPR_1' => ['bg-yellow-100 text-yellow-800', 'Waiting L2'],
                                    'APPR_2' => ['bg-yellow-100 text-yellow-800', 'Waiting L3'],
                                    'APPR_3' => ['bg-yellow-100 text-yellow-800', 'Waiting L4'],
                                    'APPR_4' => ['bg-blue-100 text-blue-800', 'Approved'],
                                    'PO_ISSUED' => ['bg-purple-100 text-purple-800', 'PO Issued'],
                                    'ON_DELIVERY' => ['bg-indigo-100 text-indigo-800', 'On Delivery'],
                                    'COMPLETED' => ['bg-green-100 text-green-800', 'Completed'],
                                    'SYNCED' => ['bg-teal-100 text-teal-800', 'Synced'],
                                    'REJECTED' => ['bg-red-100 text-red-800', 'Rejected'],
                                ];

                                $statusClass = $statusStyles[$request->status][0] ?? 'bg-gray-100 text-gray-800';
                                $statusLabel = $statusStyles[$request->status][1] ?? $request->status;

                                $firstItem = $request->items->first();
                                $itemName = $firstItem ? ($firstItem->item_name ?? $firstItem->product->name ?? 'N/A') : 'No items';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $request->ticket_no }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <span class="text-gray-900 dark:text-white">{{ $itemName }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $request->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ $statusLabel }}</span>

                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('requests.show', $request->id) }}"
                                        class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <p>No requests yet. Create your first request!</p>
                                    <a href="{{ route('requests.checkout') }}"
                                        class="mt-2 inline-block text-indigo-600 font-semibold">+ New Request</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Simple Pagination Info -->
                @if($requests->count() > 0)
                    <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            Showing 1 to {{ $requests->count() }} of {{ $stats['total'] }} entries
                        </div>
                        <div class="flex gap-2">
                            <span class="w-8 h-8 flex items-center justify-center text-gray-400">&lt;</span>
                            <span class="w-8 h-8 flex items-center justify-center text-gray-400">&gt;</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-sidebar-layout>