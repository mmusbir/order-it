<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Requests</h1>
                        <p class="text-gray-500 mt-1">Track and manage your IT asset procurement requests, approvals,
                            and deliveries.</p>
                    </div>
                    <a href="{{ route('requests.checkout') }}"
                        class="flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Request
                    </a>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <!-- Search -->
                    <form method="GET" action="{{ route('requests.my-requests') }}" class="relative w-full md:w-80">
                        @foreach(request()->except(['search', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Search by Request ID or Item Name"
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </form>

                    <!-- Filter Tabs -->
                    <div class="flex gap-2 bg-gray-100 p-1 rounded-lg dark:bg-gray-700">
                        @foreach([
                            'all' => 'All Requests',
                            'waiting' => 'Waiting',
                            'approved' => 'Approved',
                            'completed' => 'Completed',
                            'rejected' => 'Rejected'
                        ] as $key => $label)
                            <a href="{{ route('requests.my-requests', array_merge(request()->except('page'), ['status' => $key])) }}"
                               class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ request('status', 'all') === $key ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-600 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Requests Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            @php
                                $sortCol = request('sort', 'created_at');
                                $sortDir = request('direction', 'desc');
                                $nextDir = $sortDir === 'asc' ? 'desc' : 'asc';
                            @endphp
                            
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('requests.my-requests', array_merge(request()->query(), ['sort' => 'ticket_no', 'direction' => $sortCol === 'ticket_no' ? $nextDir : 'asc'])) }}" class="group inline-flex items-center gap-1">
                                    REQ ID
                                    @if($sortCol === 'ticket_no')
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('requests.my-requests', array_merge(request()->query(), ['sort' => 'priority', 'direction' => $sortCol === 'priority' ? $nextDir : 'asc'])) }}" class="group inline-flex items-center gap-1">
                                    Priority
                                    @if($sortCol === 'priority')
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('requests.my-requests', array_merge(request()->query(), ['sort' => 'status', 'direction' => $sortCol === 'status' ? $nextDir : 'asc'])) }}" class="group inline-flex items-center gap-1">
                                    Status
                                    @if($sortCol === 'status')
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($requests as $req)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('requests.show', $req->id) }}" class="font-semibold text-indigo-600 hover:text-indigo-800">
                                        #{{ $req->ticket_no }}
                                    </a>
                                    <p class="text-xs text-gray-400 mt-1">{{ $req->created_at->format('d M Y') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($req->items->count() > 0)
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $req->items->first()->product->name ?? '-' }}</p>
                                        @if($req->items->count() > 1)
                                            <p class="text-xs text-gray-400">+{{ $req->items->count() - 1 }} more items</p>
                                        @endif
                                    @else
                                        <p class="text-sm text-gray-400">-</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $req->items->first()->product->category ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $priorityColors = [
                                            'low' => 'bg-gray-100 text-gray-600',
                                            'medium' => 'bg-yellow-100 text-yellow-700',
                                            'high' => 'bg-orange-100 text-orange-700',
                                            'urgent' => 'bg-red-100 text-red-700',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $priorityColors[$req->priority] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($req->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'SUBMITTED' => 'bg-blue-100 text-blue-700',
                                            'APPR_1' => 'bg-cyan-100 text-cyan-700',
                                            'APPR_2' => 'bg-teal-100 text-teal-700',
                                            'APPR_3' => 'bg-green-100 text-green-700',
                                            'APPR_4' => 'bg-emerald-100 text-emerald-700',
                                            'PO_ISSUED' => 'bg-purple-100 text-purple-700',
                                            'ON_DELIVERY' => 'bg-indigo-100 text-indigo-700',
                                            'COMPLETED' => 'bg-green-100 text-green-700',
                                            'REJECTED' => 'bg-red-100 text-red-700',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $statusColors[$req->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ str_replace('_', ' ', $req->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('requests.show', $req->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        View Details →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No requests found</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new request.</p>
                                    <a href="{{ route('requests.checkout') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        New Request
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>

                <!-- Pagination -->
                @if($requests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-sidebar-layout>
