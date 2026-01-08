<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Approval History</h1>
                    <p class="text-gray-500 mt-1">Requests you have approved or rejected.</p>
                </div>
                <a href="{{ route('requests.index') }}"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <!-- Total Reviewed -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-blue-500 bg-blue-50 dark:bg-blue-900/50 px-2 py-1 rounded-full">all time</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Total Reviewed</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>

                <!-- Approved -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div
                            class="w-12 h-12 bg-green-100 dark:bg-green-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-green-500 bg-green-50 dark:bg-green-900/50 px-2 py-1 rounded-full">approved</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Approved</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['approved'] }}</p>
                </div>

                <!-- Rejected -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div
                            class="w-12 h-12 bg-red-100 dark:bg-red-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-red-500 bg-red-50 dark:bg-red-900/50 px-2 py-1 rounded-full">rejected</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Rejected</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['rejected'] }}</p>
                </div>

                <!-- This Month -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div
                            class="w-12 h-12 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-purple-500 bg-purple-50 dark:bg-purple-900/50 px-2 py-1 rounded-full">{{ date('M') }}</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">This Month</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['this_month'] }}</p>
                </div>
            </div>

            <!-- History Table -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Request History</h2>
                </div>

                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Request ID</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Requester</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Item Details</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Date</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($requests as $request)
                            @php
                                $firstItem = $request->items->first();
                                $itemName = $firstItem ? ($firstItem->item_name ?? 'N/A') : 'No items';
                                $initials = strtoupper(substr($request->requester->name ?? 'U', 0, 2));
                                $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-pink-500', 'bg-yellow-500'];
                                $bgColor = $colors[$request->id % count($colors)];

                                $statusColors = [
                                    'SUBMITTED' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                    'APPR_MGR' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                    'APPR_HEAD' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                    'APPR_DIR' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
                                    'PO_ISSUED' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
                                    'ON_DELIVERY' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-300',
                                    'COMPLETED' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                    'SYNCED' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                    'REJECTED' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                ];
                                $statusColor = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <span
                                        class="font-semibold text-gray-900 dark:text-white">{{ $request->ticket_no }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 {{ $bgColor }} rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">
                                                {{ $request->requester->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $request->requester->department ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    {{ $itemName }}
                                    @if($request->items->count() > 1)
                                        <span class="text-xs text-gray-400">+{{ $request->items->count() - 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    {{ $request->updated_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $approvalLog = $userApprovalLogs->get($request->id);
                                        $actionColor = $approvalLog && $approvalLog->action === 'APPROVE' 
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                                            : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300';
                                        $actionLabel = $approvalLog ? ($approvalLog->action === 'APPROVE' ? 'Approved' : 'Rejected') : 'Processed';
                                    @endphp
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $actionColor }} w-fit">
                                            {{ $actionLabel }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            by {{ auth()->user()->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('requests.show', $request->id) }}"
                                        class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-lg transition">
                                        View Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-900 dark:text-white">No history yet</p>
                                        <p class="text-gray-500 dark:text-gray-400">You haven't approved or rejected any
                                            requests yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($requests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Showing {{ $requests->firstItem() }}-{{ $requests->lastItem() }} of {{ $requests->total() }}
                            requests
                        </p>
                        <div class="flex gap-1">
                            {{ $requests->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-sidebar-layout>