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
                    <form method="GET" action="{{ route('requests.index') }}" class="relative w-full md:w-80">
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
                            <a href="{{ route('requests.index', array_merge(request()->except('page'), ['status' => $key])) }}"
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
                                
                                $headers = [
                                    'ticket_no' => 'REQ ID',
                                    'items' => 'Item Name', // Sorting by item name is tricky with relation, let's keep it unsortable or handle in controller
                                    'product.category' => 'Category',
                                    'priority' => 'Priority',
                                    'status' => 'Status',
                                    'action' => 'Action'
                                ];
                            @endphp
                            
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('requests.index', array_merge(request()->query(), ['sort' => 'ticket_no', 'direction' => $sortCol === 'ticket_no' ? $nextDir : 'asc'])) }}" class="group inline-flex items-center gap-1">
                                    REQ ID
                                    @if($sortCol === 'ticket_no')
                                        <span class="text-indigo-500">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Item Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('requests.index', array_merge(request()->query(), ['sort' => 'priority', 'direction' => $sortCol === 'priority' ? $nextDir : 'asc'])) }}" class="group inline-flex items-center gap-1">
                                    Priority
                                    @if($sortCol === 'priority')
                                        <span class="text-indigo-500">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('requests.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => $sortCol === 'status' ? $nextDir : 'asc'])) }}" class="group inline-flex items-center gap-1">
                                    Status
                                    @if($sortCol === 'status')
                                        <span class="text-indigo-500">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('requests.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => $sortCol === 'created_at' ? $nextDir : 'asc'])) }}" class="group inline-flex items-center gap-1">
                                    Date
                                    @if($sortCol === 'created_at')
                                        <span class="text-indigo-500">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($requests as $request)
                            @php
                                // Status styling
                                $statusStyles = [
                                    'SUBMITTED' => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300', 'Pending L1'],
                                    'APPR_1' => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300', 'Waiting L2'],
                                    'APPR_2' => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300', 'Waiting L3'],
                                    'APPR_3' => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300', 'Waiting L4'],
                                    'APPR_4' => ['bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300', 'Approved'],
                                    'PO_ISSUED' => ['bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300', 'PO Issued'],
                                    'ON_DELIVERY' => ['bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300', 'On Delivery'],
                                    'COMPLETED' => ['bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300', 'Completed'],
                                    'SYNCED' => ['bg-teal-100 text-teal-800 dark:bg-teal-900/50 dark:text-teal-300', 'Synced'],
                                    'REJECTED' => ['bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300', 'Rejected'],
                                ];
                                
                                    $statusClass = $statusStyles[$request->status][0] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                    $statusLabel = $statusStyles[$request->status][1] ?? $request->status;

                                // Get first item info
                                $firstItem = $request->items->first();
                                $itemName = $firstItem ? ($firstItem->item_name ?? $firstItem->product->name ?? 'N/A') : 'No items';
                                $itemCategory = $firstItem && $firstItem->product ? $firstItem->product->category : ($firstItem->category ?? 'Hardware');
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-xs font-semibold text-gray-900 dark:text-white">{{ $request->ticket_no }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $itemName }}</div>
                                    @if($request->items->count() > 1)
                                        <div class="text-xs text-gray-400">+{{ $request->items->count() - 1 }} more items</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $itemCategory }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $priorityStyles = [
                                            'low' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                            'normal' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                            'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                                            'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                        ];
                                        $priorityClass = $priorityStyles[$request->priority ?? 'normal'] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $priorityClass }}">
                                        {{ ucfirst($request->priority ?? 'Normal') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    {{ $request->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('requests.show', $request->id) }}"
                                        class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <p>No requests found matching your filters.</p>
                                        @if(request('status') != 'all' || request('search'))
                                            <a href="{{ route('requests.index') }}" class="mt-2 text-indigo-600 dark:text-indigo-400 hover:underline">Clear Filters</a>
                                        @else
                                            <a href="{{ route('requests.checkout') }}" class="mt-4 text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">+ New Request</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>

                <!-- Footer: Pagination & Per Page -->
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex flex-col md:flex-row justify-between items-center gap-4">
                    
                    <!-- Per Page Dropdown -->
                    <form method="GET" action="{{ route('requests.index') }}" class="flex items-center gap-2">
                         @foreach(request()->except(['per_page', 'page']) as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <label for="per_page" class="text-xs text-gray-500 dark:text-gray-400 font-medium">Show</label>
                        <select name="per_page" id="per_page" onchange="this.form.submit()"
                            class="text-xs border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-2">
                            @foreach([20, 50, 100, 200, 500] as $n)
                                <option value="{{ $n }}" {{ request('per_page', 20) == $n ? 'selected' : '' }}>{{ $n }}</option>
                            @endforeach
                        </select>
                        <span class="text-xs text-gray-500 dark:text-gray-400">rows</span>
                    </form>

                    <!-- Pagination Links -->
                    <div class="flex-1 flex justify-end">
                        {{ $requests->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
</x-sidebar-layout>