<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Approver Dashboard</h1>
                    <p class="text-gray-500 mt-1">Welcome back, {{ Auth::user()->name }}. Here's your approval overview.</p>
                    @if($approverLevel)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 mt-2">
                            {{ $approverLevel->level_name ?? 'Approver' }}
                        </span>
                    @endif
                </div>
                <a href="{{ route('requests.approvals') }}"
                    class="flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-full shadow transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    View All Approvals
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <!-- Pending Approvals -->
                <a href="{{ route('requests.approvals') }}"
                    class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/30 dark:to-orange-900/30 rounded-xl p-5 shadow-sm border border-amber-200 hover:shadow-md hover:border-amber-300 transition cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Pending Approvals</p>
                            <p class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $pendingApprovals }}</p>
                            <p class="text-xs text-amber-600 mt-1">Needs your action</p>
                        </div>
                        <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Click to view →</p>
                </a>

                <!-- Total Approved -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 rounded-xl p-5 shadow-sm border border-green-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Approved</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $totalApproved }}</p>
                            <p class="text-xs text-green-600 mt-1">All time</p>
                        </div>
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Rejected -->
                <div class="bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/30 dark:to-rose-900/30 rounded-xl p-5 shadow-sm border border-red-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Rejected</p>
                            <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $totalRejected }}</p>
                            <p class="text-xs text-red-600 mt-1">All time</p>
                        </div>
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Approval Rate -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Approval Rate</p>
                            @php
                                $total = $totalApproved + $totalRejected;
                                $rate = $total > 0 ? round(($totalApproved / $total) * 100) : 0;
                            @endphp
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $rate }}%</p>
                            <p class="text-xs text-gray-500 mt-1">Based on {{ $total }} actions</p>
                        </div>
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pending Requests -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Pending for Your Approval</h2>
                        <a href="{{ route('requests.approvals') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                            View All
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    @if($pendingRequests && $pendingRequests->count() > 0)
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($pendingRequests as $request)
                                @if($request)
                                    <a href="{{ route('requests.show', $request->id) }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-semibold text-gray-900 dark:text-white">#{{ $request->ticket_no }}</p>
                                                <p class="text-sm text-gray-500 mt-1">{{ $request->requester->name ?? 'Unknown' }}</p>
                                                @if($request->items->count() > 0)
                                                    <p class="text-xs text-gray-400 mt-1">{{ $request->items->first()->product->name ?? 'No items' }}</p>
                                                @endif
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                Pending
                                            </span>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500 mt-4">No pending approvals</p>
                            <p class="text-sm text-gray-400">All caught up! 🎉</p>
                        </div>
                    @endif
                </div>

                <!-- Recent Activity -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Your Recent Activity</h2>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Show:</span>
                            <select id="activityPerPage" onchange="changeActivityPerPage(this.value)" 
                                class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="10" {{ request('activity_per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('activity_per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('activity_per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('activity_per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>

                    @if($recentActivity && $recentActivity->count() > 0)
                        <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[500px] overflow-y-auto">
                            @foreach($recentActivity as $log)
                                <div class="px-6 py-3 flex items-start gap-3">
                                    @if($log->action == 'APPROVE')
                                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $log->action == 'APPROVE' ? 'Approved' : 'Rejected' }}
                                            <span class="text-indigo-600">#{{ $log->request->ticket_no ?? 'N/A' }}</span>
                                        </p>
                                        @if($log->comments)
                                            <p class="text-xs text-gray-500 truncate">{{ $log->comments }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-1">{{ $log->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- Pagination Info --}}
                        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-sm text-gray-500">
                            <span>Showing {{ $recentActivity->firstItem() ?? 0 }} - {{ $recentActivity->lastItem() ?? 0 }} of {{ $recentActivity->total() }}</span>
                            <div class="flex gap-2">
                                @if($recentActivity->onFirstPage())
                                    <span class="px-3 py-1 text-gray-400 cursor-not-allowed">← Prev</span>
                                @else
                                    <a href="{{ $recentActivity->previousPageUrl() }}" class="px-3 py-1 text-indigo-600 hover:text-indigo-800">← Prev</a>
                                @endif
                                
                                @if($recentActivity->hasMorePages())
                                    <a href="{{ $recentActivity->nextPageUrl() }}" class="px-3 py-1 text-indigo-600 hover:text-indigo-800">Next →</a>
                                @else
                                    <span class="px-3 py-1 text-gray-400 cursor-not-allowed">Next →</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500 mt-4">No activity yet</p>
                            <p class="text-sm text-gray-400">Your approval history will appear here</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    
    <script>
        function changeActivityPerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('activity_per_page', value);
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        }
    </script>
</x-sidebar-layout>

