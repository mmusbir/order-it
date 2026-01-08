<x-sidebar-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Monitor Requests</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                        Pantau semua request dan status approval dalam sistem.
                    </p>
                </div>
                <a href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Dashboard
                </a>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                @php
                    $quickStats = [
                        ['label' => 'Total', 'count' => $requests->total(), 'color' => 'gray', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['label' => 'Pending', 'count' => $statusCounts['pending'] ?? 0, 'color' => 'yellow', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Processing', 'count' => $statusCounts['processing'] ?? 0, 'color' => 'blue', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                        ['label' => 'Completed', 'count' => $statusCounts['completed'] ?? 0, 'color' => 'green', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Rejected', 'count' => $statusCounts['rejected'] ?? 0, 'color' => 'red', 'icon' => 'M6 18L18 6M6 6l12 12'],
                    ];
                @endphp
                @foreach($quickStats as $stat)
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                    {{ $stat['label'] }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stat['count'] }}</p>
                            </div>
                            <div
                                class="w-10 h-10 bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $stat['icon'] }}"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 mb-6">
                <form method="GET" action="{{ route('admin.requests.monitor') }}"
                    class="flex flex-wrap items-end gap-4">

                    <!-- Search -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="search"
                            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Request ID atau Requester..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Department -->
                    <div class="min-w-[150px]">
                        <label for="department"
                            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Department</label>
                        <select name="department" id="department"
                            class="w-full py-2 px-3 pr-8 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-xs focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-no-repeat bg-right"
                            style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27 stroke-width=%272%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-size: 1.25rem; background-position: right 0.5rem center;">
                            <option value="">-</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="min-w-[150px]">
                        <label for="status"
                            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                        <select name="status" id="status"
                            class="w-full py-2 px-3 pr-8 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-xs focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-no-repeat bg-right"
                            style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27 stroke-width=%272%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-size: 1.25rem; background-position: right 0.5rem center;">
                            <option value="">-</option>
                            <option value="SUBMITTED" {{ request('status') == 'SUBMITTED' ? 'selected' : '' }}>Submitted
                            </option>
                            <option value="APPR_1" {{ request('status') == 'APPR_1' ? 'selected' : '' }}>Approved L1
                            </option>
                            <option value="APPR_2" {{ request('status') == 'APPR_2' ? 'selected' : '' }}>Approved L2
                            </option>
                            <option value="APPR_3" {{ request('status') == 'APPR_3' ? 'selected' : '' }}>Approved L3
                            </option>
                            <option value="APPR_4" {{ request('status') == 'APPR_4' ? 'selected' : '' }}>Ready for PO
                            </option>
                            <option value="PO_ISSUED" {{ request('status') == 'PO_ISSUED' ? 'selected' : '' }}>PO Issued
                            </option>
                            <option value="ON_DELIVERY" {{ request('status') == 'ON_DELIVERY' ? 'selected' : '' }}>On Delivery</option>
                            <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    </div>

                    <!-- Priority -->
                    <div class="min-w-[120px]">
                        <label for="priority"
                            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Priority</label>
                        <select name="priority" id="priority"
                            class="w-full py-2 px-3 pr-8 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-xs focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-no-repeat bg-right"
                            style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27 stroke-width=%272%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-size: 1.25rem; background-position: right 0.5rem center;">
                            <option value="">-</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <!-- Date -->
                    <div class="min-w-[150px]">
                        <label for="date"
                            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Date</label>
                        <input type="date" name="date" id="date" value="{{ request('date') }}"
                            class="w-full py-2 px-3 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'department', 'status', 'priority', 'date']))
                            <a href="{{ route('admin.requests.monitor') }}"
                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Requests Table -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Ticket Info
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Requester
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Items
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Priority
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($requests as $request)
                                @php
                                    $statusConfig = [
                                        'SUBMITTED' => ['label' => 'Submitted', 'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
                                        'APPR_1' => ['label' => 'Waiting L2', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300'],
                                        'APPR_2' => ['label' => 'Waiting L3', 'class' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-300'],
                                        'APPR_3' => ['label' => 'Waiting L4', 'class' => 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900 dark:text-fuchsia-300'],
                                        'APPR_4' => ['label' => 'Ready for PO', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300'],
                                        'PO_ISSUED' => ['label' => 'PO Issued', 'class' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300'],
                                        'ON_DELIVERY' => ['label' => 'On Delivery', 'class' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300'],
                                        'COMPLETED' => ['label' => 'Completed', 'class' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'],
                                        'SYNCED' => ['label' => 'Synced', 'class' => 'bg-teal-100 text-teal-700 dark:bg-teal-900 dark:text-teal-300'],
                                        'REJECTED' => ['label' => 'Rejected', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'],
                                    ];
                                    $status = $statusConfig[$request->status] ?? ['label' => $request->status, 'class' => 'bg-gray-100 text-gray-700'];
                                    $daysAgo = $request->created_at->diffInDays(now());
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.requests.show', $request->id) }}"
                                            class="text-sm font-bold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            {{ $request->ticket_no }}
                                        </a>
                                        <p class="text-xs text-gray-500">{{ $request->request_type }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $request->requester->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $request->requester->department ?? '-' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            @foreach($request->items->take(2) as $item)
                                                <p>• {{ $item->product ? $item->product->name : $item->custom_product_name }}
                                                    (x{{ $item->qty }})</p>
                                            @endforeach
                                            @if($request->items->count() > 2)
                                                <p class="text-xs text-gray-400">+{{ $request->items->count() - 2 }} more items
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $priorityColors = [
                                                'low' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                                'medium' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                                'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
                                                'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $priorityColors[$request->priority] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($request->priority ?? 'medium') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $status['class'] }}">
                                            {{ $status['label'] }}
                                        </span>
                                        @if($daysAgo > 7 && !in_array($request->status, ['COMPLETED', 'REJECTED', 'SYNCED']))
                                            <p class="text-xs text-red-500 mt-1">⚠️ {{ $daysAgo }} days</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            {{ $request->created_at->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $request->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.requests.show', $request->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-semibold transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">No requests found</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Try adjusting your filter
                                            criteria.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($requests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        {{ $requests->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-sidebar-layout>