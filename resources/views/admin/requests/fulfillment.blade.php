<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Order Processing Setup
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex w-full" aria-label="Tabs">
                    <a href="{{ route('admin.requests.fulfillment', ['tab' => 'po']) }}"
                        class="{{ $tab === 'po' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }} 
                              whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center flex-1 gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Generated PO
                        <span
                            class="{{ $tab === 'po' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900' }} py-0.5 px-3 rounded-full text-xs font-medium md:inline-block">
                            {{ $counts['po'] }}
                        </span>
                    </a>

                    <a href="{{ route('admin.requests.fulfillment', ['tab' => 'sync']) }}"
                        class="{{ $tab === 'sync' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }} 
                              whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center flex-1 gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Ready to Sync
                        <span
                            class="{{ $tab === 'sync' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900' }} py-0.5 px-3 rounded-full text-xs font-medium md:inline-block">
                            {{ $counts['sync'] }}
                        </span>
                    </a>

                    <a href="{{ route('admin.requests.fulfillment', ['tab' => 'delivery']) }}"
                        class="{{ $tab === 'delivery' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }} 
                              whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center flex-1 gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0">
                            </path>
                        </svg>
                        On Delivery
                        <span
                            class="{{ $tab === 'delivery' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900' }} py-0.5 px-3 rounded-full text-xs font-medium md:inline-block">
                            {{ $counts['delivery'] }}
                        </span>
                    </a>

                    <a href="{{ route('admin.requests.fulfillment', ['tab' => 'completed']) }}"
                        class="{{ $tab === 'completed' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }} 
                              whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center flex-1 gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Completed
                        <span
                            class="{{ $tab === 'completed' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900' }} py-0.5 px-3 rounded-full text-xs font-medium md:inline-block">
                            {{ $counts['completed'] }}
                        </span>
                    </a>
                </nav>
            </div>

            <!-- Action Description -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            @if($tab === 'po')
                                These orders have been fully approved. Please generate a Purchase Order (PO) to proceed.
                            @elseif($tab === 'sync')
                                PO has been generated. Please ensure serial numbers are assigned and sync items to Snipe-IT.
                            @elseif($tab === 'delivery')
                                Items are ready for delivery or in transit. Update tracking info once shipped.
                            @elseif($tab === 'completed')
                                Requester has confirmed receipt of these items.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Requests Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Ticket Info
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Items
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Requester
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Priority
                                    </th>
                                    @if($tab === 'delivery')
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Delivery Info
                                        </th>
                                    @endif
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <div class="flex items-center justify-end space-x-2">
                                            <span>Show:</span>
                                            <select onchange="window.location.href = this.value"
                                                class="text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1">
                                                @foreach([20, 25, 50, 100, 200, 500] as $val)
                                                    <option value="{{ request()->fullUrlWithQuery(['per_page' => $val]) }}"
                                                        {{ $perPage == $val ? 'selected' : '' }}>
                                                        {{ $val }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($requests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                                {{ $request->ticket_no }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $request->created_at->format('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                @foreach($request->items as $item)
                                                    <div>•
                                                        {{ $item->product ? $item->product->name : $item->custom_product_name }}
                                                        ({{ $item->qty }}x)
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $request->requester->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $request->requester->department }}
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
                                        @if($tab === 'delivery')
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($request->courier || $request->tracking_no)
                                                    <div class="flex flex-col gap-0.5">
                                                        <span
                                                            class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded-full inline-block w-fit uppercase border border-blue-200">
                                                            {{ $request->courier }}
                                                        </span>
                                                        <span class="text-[10px] text-gray-600 font-mono tracking-tight">
                                                            Resi: {{ $request->tracking_no }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="flex items-center gap-1.5 text-orange-500">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span class="text-[10px] font-bold uppercase tracking-wider">Pending
                                                            Shipping</span>
                                                    </div>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.requests.show', $request->id) }}"
                                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                @if($tab === 'po')
                                                    Generate PO
                                                @elseif($tab === 'sync')
                                                    Sync to Snipe-IT
                                                @elseif($tab === 'delivery')
                                                    Update Delivery
                                                @elseif($tab === 'completed')
                                                    View Details
                                                @endif
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $tab === 'delivery' ? 6 : 5 }}"
                                            class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                                    </path>
                                                </svg>
                                                <p>No requests needing action in this category.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-sidebar-layout>