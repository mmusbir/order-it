<x-sidebar-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header with Greeting -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Selamat {{ now()->hour < 12 ? 'Pagi' : (now()->hour < 17 ? 'Siang' : 'Malam') }},
                        {{ Auth::user()->name }}! 👋
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                        Kelola procurement dan pantau status request.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span
                        class="px-3 py-1.5 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg text-xs font-medium flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        System Online
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ now()->format('l, d M Y') }}
                    </span>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <!-- Ready for PO -->
                <a href="{{ route('admin.requests.fulfillment', ['tab' => 'po']) }}"
                    class="group rounded-xl p-5 text-white shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all"
                    style="background: linear-gradient(to bottom right, #3b82f6, #4f46e5);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium" style="color: rgba(255,255,255,0.85);">Ready for PO</p>
                            <p class="text-3xl font-bold mt-1 text-white">{{ $stats['ready_for_po'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                            style="background: rgba(255,255,255,0.2);">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs mt-3 flex items-center gap-1 group-hover:underline"
                        style="color: rgba(255,255,255,0.85);">
                        Action required →
                    </p>
                </a>

                <!-- On Delivery -->
                <a href="{{ route('admin.requests.monitor', ['status' => 'ON_DELIVERY']) }}"
                    class="group rounded-xl p-5 text-white shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all"
                    style="background: linear-gradient(to bottom right, #f97316, #d97706);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium" style="color: rgba(255,255,255,0.85);">On Delivery</p>
                            <p class="text-3xl font-bold mt-1 text-white">{{ $stats['on_delivery'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                            style="background: rgba(255,255,255,0.2);">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs mt-3 flex items-center gap-1 group-hover:underline"
                        style="color: rgba(255,255,255,0.85);">
                        In transit →
                    </p>
                </a>

                <!-- Pending Approval -->
                <a href="{{ route('admin.requests.monitor') }}"
                    class="group rounded-xl p-5 text-white shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all"
                    style="background: linear-gradient(to bottom right, #eab308, #f59e0b);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium" style="color: rgba(255,255,255,0.85);">Pending Approval</p>
                            <p class="text-3xl font-bold mt-1 text-white">{{ $stats['pending_approval'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                            style="background: rgba(255,255,255,0.2);">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs mt-3 flex items-center gap-1 group-hover:underline"
                        style="color: rgba(255,255,255,0.85);">
                        Awaiting sign-off →
                    </p>
                </a>

                <!-- Completed This Month -->
                <a href="{{ route('admin.requests.monitor', ['status' => 'COMPLETED']) }}"
                    class="group rounded-xl p-5 text-white shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all"
                    style="background: linear-gradient(to bottom right, #10b981, #16a34a);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium" style="color: rgba(255,255,255,0.85);">Completed</p>
                            <p class="text-3xl font-bold mt-1 text-white">{{ $stats['total_this_month'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                            style="background: rgba(255,255,255,0.2);">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs mt-3 flex items-center gap-1" style="color: rgba(255,255,255,0.85);">
                        @if($stats['growth_percentage'] > 0)
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            +{{ $stats['growth_percentage'] }}% this month
                        @else
                            This month →
                        @endif
                    </p>
                </a>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <a href="{{ route('admin.requests.fulfillment') }}"
                    class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-700 transition group">
                    <div
                        class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/40 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800 transition">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white text-sm">Order Processing</p>
                        <p class="text-xs text-gray-500">Process & fulfill</p>
                    </div>
                </a>

                <a href="{{ route('admin.requests.monitor') }}"
                    class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-blue-200 dark:hover:border-blue-700 transition group">
                    <div
                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800 transition">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white text-sm">Monitor Requests</p>
                        <p class="text-xs text-gray-500">View all requests</p>
                    </div>
                </a>

                <a href="{{ route('admin.products.index') }}"
                    class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-purple-200 dark:hover:border-purple-700 transition group">
                    <div
                        class="w-10 h-10 bg-purple-100 dark:bg-purple-900/40 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-800 transition">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white text-sm">Manage Catalog</p>
                        <p class="text-xs text-gray-500">Products & items</p>
                    </div>
                </a>

                <a href="{{ route('admin.users') }}"
                    class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-cyan-200 dark:hover:border-cyan-700 transition group">
                    <div
                        class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/40 rounded-lg flex items-center justify-center group-hover:bg-cyan-200 dark:group-hover:bg-cyan-800 transition">
                        <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white text-sm">User Management</p>
                        <p class="text-xs text-gray-500">Manage users</p>
                    </div>
                </a>
            </div>

            <!-- Recent Requests Table -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-wrap justify-between items-center gap-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Recent Requests</h2>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('admin.dashboard') }}" method="GET" id="perPageForm"
                            class="flex items-center gap-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Show:</span>
                            <select name="per_page" onchange="document.getElementById('perPageForm').submit()"
                                class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-1.5">
                                @foreach([20, 50, 100, 200] as $size)
                                    <option value="{{ $size }}" {{ request('per_page', 20) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        <a href="{{ route('admin.requests.monitor') }}"
                            class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                            View All →
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'ticket_no', 'sort_dir' => ($sortBy == 'ticket_no' && $sortDir == 'asc') ? 'desc' : 'asc']) }}"
                                        class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 transition">
                                        Ticket
                                        @if($sortBy == 'ticket_no')
                                            <svg class="w-3 h-3 {{ $sortDir == 'asc' ? 'rotate-180' : '' }}" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Items</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Requester</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'priority', 'sort_dir' => ($sortBy == 'priority' && $sortDir == 'asc') ? 'desc' : 'asc']) }}"
                                        class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 transition">
                                        Priority
                                        @if($sortBy == 'priority')
                                            <svg class="w-3 h-3 {{ $sortDir == 'asc' ? 'rotate-180' : '' }}" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_dir' => ($sortBy == 'status' && $sortDir == 'asc') ? 'desc' : 'asc']) }}"
                                        class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 transition">
                                        Status
                                        @if($sortBy == 'status')
                                            <svg class="w-3 h-3 {{ $sortDir == 'asc' ? 'rotate-180' : '' }}" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_dir' => ($sortBy == 'created_at' && $sortDir == 'asc') ? 'desc' : 'asc']) }}"
                                        class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 transition">
                                        Date
                                        @if($sortBy == 'created_at')
                                            <svg class="w-3 h-3 {{ $sortDir == 'asc' ? 'rotate-180' : '' }}" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($requests as $request)
                                @php
                                    $firstItem = $request->items->first();
                                    $itemName = $firstItem ? ($firstItem->item_name ?? $firstItem->product->name ?? 'N/A') : 'No items';

                                    $statusConfig = [
                                        'SUBMITTED' => ['label' => 'Submitted', 'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
                                        'APPR_1' => ['label' => 'Waiting L2', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300'],
                                        'APPR_2' => ['label' => 'Waiting L3', 'class' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-300'],
                                        'APPR_3' => ['label' => 'Waiting L4', 'class' => 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900 dark:text-fuchsia-300'],
                                        'APPR_4' => ['label' => 'Ready PO', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300'],
                                        'PO_ISSUED' => ['label' => 'PO Issued', 'class' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300'],
                                        'ON_DELIVERY' => ['label' => 'Delivery', 'class' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300'],
                                        'COMPLETED' => ['label' => 'Completed', 'class' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'],
                                        'SYNCED' => ['label' => 'Synced', 'class' => 'bg-teal-100 text-teal-700 dark:bg-teal-900 dark:text-teal-300'],
                                        'REJECTED' => ['label' => 'Rejected', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'],
                                    ];
                                    $status = $statusConfig[$request->status] ?? ['label' => $request->status, 'class' => 'bg-gray-100 text-gray-700'];

                                    $priorityColors = [
                                        'low' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                        'medium' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                        'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
                                        'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                    ];
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.requests.show', $request->id) }}"
                                            class="font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline">
                                            {{ $request->ticket_no }}
                                        </a>
                                        <p class="text-xs text-gray-500">{{ $request->request_type }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ Str::limit($itemName, 25) }}</p>
                                        @if($request->items->count() > 1)
                                            <p class="text-xs text-gray-400">+{{ $request->items->count() - 1 }} more</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                {{ $request->requester->name ?? 'Unknown' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $request->requester->department ?? '-' }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full {{ $priorityColors[$request->priority] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($request->priority ?? 'medium') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full {{ $status['class'] }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            {{ $request->created_at->format('d M') }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $request->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.requests.show', $request->id) }}"
                                            class="inline-flex items-center px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-semibold transition">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-900 dark:text-white">No requests found</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">There are no recent requests to
                                            display.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($requests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        {{ $requests->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-sidebar-layout>