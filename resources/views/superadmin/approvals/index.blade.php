<x-sidebar-layout>
    <div class="py-6" x-data="approvalManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Global Approval Inbox</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                        Oversee and manage all IT procurement requests across all departments.
                    </p>
                </div>
                <a href="{{ route('superadmin.dashboard') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Dashboard
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Total Pending -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Pending</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalPending }}</p>
                        </div>
                        <div
                            class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Urgent Requests -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Urgent (>3 Days)</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $urgentRequests }}</p>
                        </div>
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Stuck >7 Days -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Stuck (>7 Days)</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stuckRequests }}</p>
                        </div>
                        <div
                            class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Processed Today -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Processed Today</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $processedToday }}</p>
                        </div>
                        <div
                            class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div
                    class="mb-4 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div
                    class="mb-4 p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 mb-4">
                <form method="GET" action="{{ route('superadmin.approvals') }}"
                    class="flex flex-wrap items-center gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search Request ID or Requester"
                                class="w-full pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="relative">
                        <select name="department"
                            class="px-4 py-2 pr-10 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm cursor-pointer focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}
                                </option>
                            @endforeach
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="relative">
                        <select name="status"
                            class="px-4 py-2 pr-10 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm cursor-pointer focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="relative">
                        <select name="priority"
                            class="px-4 py-2 pr-10 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm cursor-pointer focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                            <option value="">All Priority</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">Filter</button>
                    <a href="{{ route('superadmin.approvals') }}"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">Reset</a>

                    <!-- Bulk Actions -->
                    <div class="ml-auto flex items-center gap-2" x-show="selectedIds.length > 0" x-cloak>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            <span x-text="selectedIds.length"></span> selected
                        </span>
                        <button type="button" @click="bulkApprove()"
                            class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Approve All
                        </button>
                        <button type="button" @click="bulkReject()"
                            class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Reject All
                        </button>
                    </div>
                </form>
            </div>

            <!-- Requests Table -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" @change="toggleAll($event)" x-ref="selectAllCheckbox"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Request ID
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Requester
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Item Summary
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Status
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Priority
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Created
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($requests as $request)
                            @php
                                $statusConfig = [
                                    'SUBMITTED' => ['label' => 'Waiting L1', 'bg' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-700 dark:text-yellow-300'],
                                    'APPR_1' => ['label' => 'Waiting L2', 'bg' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-700 dark:text-yellow-300'],
                                    'APPR_2' => ['label' => 'Waiting L3', 'bg' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-700 dark:text-yellow-300'],
                                    'APPR_3' => ['label' => 'Waiting L4', 'bg' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-700 dark:text-yellow-300'],
                                    'APPR_4' => ['label' => 'Approved', 'bg' => 'bg-green-100 dark:bg-green-900', 'text' => 'text-green-700 dark:text-green-300'],
                                    'PO_ISSUED' => ['label' => 'PO Issued', 'bg' => 'bg-blue-100 dark:bg-blue-900', 'text' => 'text-blue-700 dark:text-blue-300'],
                                    'ON_DELIVERY' => ['label' => 'On Delivery', 'bg' => 'bg-cyan-100 dark:bg-cyan-900', 'text' => 'text-cyan-700 dark:text-cyan-300'],
                                    'COMPLETED' => ['label' => 'Completed', 'bg' => 'bg-green-100 dark:bg-green-900', 'text' => 'text-green-700 dark:text-green-300'],
                                    'REJECTED' => ['label' => 'Rejected', 'bg' => 'bg-red-100 dark:bg-red-900', 'text' => 'text-red-700 dark:text-red-300'],
                                ];
                                $config = $statusConfig[$request->status] ?? ['label' => $request->status, 'bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
                                $daysAgo = $request->created_at->diffInDays(now());
                                $isUrgent = $daysAgo >= 3;

                                $priorityColors = [
                                    'low' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                    'medium' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300',
                                    'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300',
                                    'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
                                ];
                                $priorityColor = $priorityColors[$request->priority] ?? 'bg-gray-100 text-gray-600';
                                $isUrgent = $daysAgo >= 3;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" value="{{ $request->id }}"
                                        @change="toggleItem({{ $request->id }})"
                                        :checked="selectedIds.includes({{ $request->id }})"
                                        class="item-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('superadmin.requests.show', $request) }}"
                                        class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                        {{ $request->ticket_no }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white text-sm">
                                            {{ $request->requester->name ?? 'Unknown' }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $request->requester->department ?? '-' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900 dark:text-white max-w-xs">
                                        @foreach($request->items->take(2) as $item)
                                            <p class="truncate">• {{ $item->product->name ?? $item->item_name ?? 'Item' }}</p>
                                        @endforeach
                                        @if($request->items->count() > 2)
                                            <p class="text-gray-500 text-xs">+{{ $request->items->count() - 2 }} more</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $config['bg'] }} {{ $config['text'] }}">
                                        {{ $config['label'] }}
                                    </span>
                                    @if($daysAgo > 7)
                                        <p class="text-xs text-red-500 mt-1">> {{ $daysAgo }} Days</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $priorityColor }}">
                                        {{ ucfirst($request->priority ?? 'medium') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $request->created_at->diffForHumans() }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if(in_array($request->status, ['SUBMITTED', 'APPR_1', 'APPR_2', 'APPR_3']))
                                            <form method="POST" action="{{ route('superadmin.approvals.approve', $request) }}"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-semibold">
                                                    Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('superadmin.approvals.reject', $request) }}"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-semibold">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('superadmin.requests.show', $request) }}"
                                            class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-xs font-semibold flex items-center gap-1"
                                            title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 dark:text-white">No pending requests</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">All requests have been processed.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Footer with Pagination & Per Page -->
                <div
                    class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex flex-col md:flex-row justify-between items-center gap-4">
                    <form method="GET" action="{{ route('superadmin.approvals') }}" class="flex items-center gap-2">
                        @foreach(request()->except(['per_page', 'page']) as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <label for="per_page_approvals"
                            class="text-sm text-gray-600 dark:text-gray-400 font-medium">Show</label>
                        <div class="relative">
                            <select name="per_page" id="per_page_approvals" onchange="this.form.submit()"
                                class="text-sm border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-10 cursor-pointer min-w-[80px]"
                                style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                                @foreach([20, 50, 100, 200, 500] as $n)
                                    <option value="{{ $n }}" {{ request('per_page', 20) == $n ? 'selected' : '' }}>{{ $n }}
                                    </option>
                                @endforeach
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">rows per page</span>
                    </form>

                    @if($requests->hasPages())
                        <div class="flex-1 flex justify-end">
                            {{ $requests->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Action Form (hidden) -->
    <form id="bulkApproveForm" method="POST" action="{{ route('superadmin.approvals.bulk') }}" class="hidden">
        @csrf
        <input type="hidden" name="action" value="approve">
    </form>
    <form id="bulkRejectForm" method="POST" action="{{ route('superadmin.approvals.bulk') }}" class="hidden">
        @csrf
        <input type="hidden" name="action" value="reject">
    </form>

    <script>
        function approvalManager() {
            return {
                selectedIds: [],

                toggleAll(event) {
                    if (event.target.checked) {
                        this.selectedIds = [...document.querySelectorAll('.item-checkbox')].map(cb => parseInt(cb.value));
                    } else {
                        this.selectedIds = [];
                    }
                },

                toggleItem(id) {
                    if (this.selectedIds.includes(id)) {
                        this.selectedIds = this.selectedIds.filter(i => i !== id);
                    } else {
                        this.selectedIds.push(id);
                    }
                },

                bulkApprove() {
                    if (this.selectedIds.length === 0) return;
                    if (confirm('Are you sure you want to approve ' + this.selectedIds.length + ' selected request(s)?')) {
                        const form = document.getElementById('bulkApproveForm');
                        this.selectedIds.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });
                        form.submit();
                    }
                },

                bulkReject() {
                    if (this.selectedIds.length === 0) return;
                    if (confirm('Are you sure you want to reject ' + this.selectedIds.length + ' selected request(s)?')) {
                        const form = document.getElementById('bulkRejectForm');
                        this.selectedIds.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });
                        form.submit();
                    }
                }
            }
        }
    </script>
</x-sidebar-layout>