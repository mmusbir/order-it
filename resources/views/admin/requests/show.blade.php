<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Order #{{ $request->ticket_no }} Details
            </h2>
            <div class="flex space-x-4 print:hidden">
                <button onclick="window.print()"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900 flex items-center bg-gray-100 px-3 py-1 rounded">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Print Ticket
                </button>
                <a href="{{ route('admin.requests.fulfillment') }}"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-900 flex items-center">
                    &larr; Back to Order Processing
                </a>
                <a href="{{ route('admin.requests.monitor') }}"
                    class="text-sm font-medium text-gray-500 hover:text-gray-700 flex items-center">
                    &larr; Back to Monitor
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Progress Stepper (Tracking History) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between overflow-x-auto">
                    @php
                        // Build dynamic steps based on assigned approvers
                        $approvers = $request->approvers->keyBy('level');
                        $levelLabels = [1 => 'Approval 1', 2 => 'Approval 2', 3 => 'Approval 3', 4 => 'Approval 4'];

                        // Status to level mapping
                        $statusToLevel = [
                            'SUBMITTED' => 0,
                            'APPR_1' => 1,
                            'APPR_2' => 2,
                            'APPR_3' => 3,
                            'APPR_4' => 4,
                            'PO_ISSUED' => 5,
                            'ON_DELIVERY' => 6,
                            'COMPLETED' => 7,
                            'SYNCED' => 7,
                        ];

                        $currentLevel = $statusToLevel[$request->status] ?? 0;

                        // Build steps array matches Request Show Logic
                        $steps = [];

                        // Step 1: Submitted
                        $steps[] = [
                            'label' => 'Submitted',
                            'sublabel' => $request->requester->name ?? '',
                            'completed' => $currentLevel >= 0,
                            'active' => $currentLevel == 0,
                        ];

                        // Approval levels 1-4
                        foreach ([1, 2, 3, 4] as $level) {
                            $approver = $approvers->get($level);
                            $levelName = $levelLabels[$level];
                            $approverName = $approver && $approver->user ? $approver->user->name : '-';
                            $isApproved = $currentLevel >= $level;
                            $isActive = $currentLevel == $level - 1;

                            $steps[] = [
                                'label' => "{$levelName}",
                                'sublabel' => $approverName,
                                'completed' => $isApproved,
                                'active' => $isActive,
                            ];
                        }

                        // Post-approval steps
                        $steps[] = [
                            'label' => 'PO Issued',
                            'sublabel' => $request->po_number ?? '-',
                            'completed' => $currentLevel >= 5,
                            'active' => $currentLevel == 4,
                        ];

                        $steps[] = [
                            'label' => 'On Delivery',
                            'sublabel' => $request->courier ?? '-',
                            'completed' => $currentLevel >= 6,
                            'active' => $currentLevel == 5,
                        ];

                        $steps[] = [
                            'label' => 'Completed',
                            'sublabel' => '',
                            'completed' => $currentLevel >= 7,
                            'active' => $currentLevel == 6,
                        ];
                    @endphp

                    @if($request->status === 'REJECTED')
                        <div class="text-red-600 font-bold text-lg w-full text-center bg-red-100 p-4 rounded">
                            REQUEST REJECTED
                        </div>
                    @else
                        @foreach($steps as $index => $step)
                            <div class="flex flex-col items-center min-w-[90px] relative z-10">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center border-2 
                                                                                        {{ $step['completed'] ? 'bg-indigo-600 border-indigo-600 text-white' : ($step['active'] ? 'bg-white border-indigo-600 text-indigo-600' : 'bg-gray-100 border-gray-300 text-gray-400') }}">

                                    @if($step['label'] === 'Submitted')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    @elseif(str_contains($step['label'], 'Approval'))
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($step['label'] === 'PO Issued')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                    @elseif($step['label'] === 'On Delivery')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0">
                                            </path>
                                        </svg>
                                    @elseif($step['label'] === 'Completed')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M5 13l4 4L19 7">
                                            </path>
                                        </svg>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <div
                                    class="mt-2 text-xs font-semibold text-center {{ $step['completed'] ? 'text-indigo-600' : ($step['active'] ? 'text-indigo-600' : 'text-gray-400') }}">
                                    {{ $step['label'] }}
                                </div>
                                <div class="text-[10px] text-gray-400 text-center truncate max-w-[80px]"
                                    title="{{ $step['sublabel'] }}">
                                    {{ $step['sublabel'] }}
                                </div>
                            </div>
                            @if(!$loop->last)
                                <div class="flex-1 h-0.5 {{ $step['completed'] ? 'bg-indigo-600' : 'bg-gray-200' }} -mx-4 mb-8 z-0">
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Request Details -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Order
                                    #{{ $request->ticket_no }}</h3>
                                <p class="text-sm text-gray-500">{{ $request->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                                {{ $request->status }}
                            </span>
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Items</h4>
                            <ul class="space-y-3">
                                @foreach($request->items as $item)
                                    <li class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg space-y-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-medium text-gray-800 dark:text-white">
                                                    {{ $item->item_name ?? $item->product->name }}
                                                </p>
                                                <p class="text-xs text-gray-500">{{ $item->item_specs }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                                    x{{ $item->qty }}</p>
                                                <span
                                                    class="text-[10px] px-2 py-0.5 rounded bg-gray-200 text-gray-600 uppercase">
                                                    {{ $request->request_type === 'NEW_CONSUMABLE' ? 'CONSUMABLE' : 'ASSET' }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Disposal Document --}}
                                        @if($item->disposal_doc_path)
                                            <div
                                                class="flex items-center gap-2 py-2 border-t border-gray-100 dark:border-gray-600">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase">Disposal Doc:</span>
                                                <a href="{{ asset('storage/' . $item->disposal_doc_path) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 text-[10px] font-bold text-red-600 bg-red-50 hover:bg-red-100 px-2 py-1 rounded transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                    View
                                                </a>
                                                <a href="{{ asset('storage/' . $item->disposal_doc_path) }}" download
                                                    class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                        </path>
                                                    </svg>
                                                    Download
                                                </a>
                                            </div>
                                        @endif

                                        @if(in_array($request->status, ['PO_ISSUED', 'ON_DELIVERY', 'SYNCED']))
                                            @if($request->request_type !== 'NEW_CONSUMABLE')
                                                <!-- Asset Sync Form -->
                                                <div class="pt-3 border-t border-gray-200 dark:border-gray-600 print:hidden">
                                                    <form action="{{ route('admin.item.serial-tag', $item->id) }}" method="POST"
                                                        class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                                        @csrf
                                                        <div>
                                                            <label
                                                                class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Asset
                                                                Name</label>
                                                            <input type="text" name="asset_name"
                                                                value="{{ $item->asset_name ?? $item->item_name ?? $item->product->name }}"
                                                                {{ $item->is_synced ? 'disabled' : '' }}
                                                                class="w-full text-xs rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Serial
                                                                Number</label>
                                                            <input type="text" name="serial_number"
                                                                value="{{ $item->serial_number }}" {{ $item->is_synced ? 'disabled' : '' }}
                                                                class="w-full text-xs rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        </div>
                                                        <div class="relative">
                                                            <label
                                                                class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Asset
                                                                Tag</label>
                                                            <div class="flex gap-1">
                                                                <input type="text" name="asset_tag" value="{{ $item->asset_tag }}"
                                                                    {{ $item->is_synced ? 'disabled' : '' }}
                                                                    class="flex-1 text-xs rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                                                @if(!$item->is_synced)
                                                                    <button type="submit"
                                                                        class="bg-indigo-600 text-white px-2 py-1 rounded text-xs hover:bg-indigo-700">Save</button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </form>

                                                    @if($item->serial_number && $item->asset_tag && !$item->is_synced)
                                                        <form action="{{ route('admin.item.sync', $item->id) }}" method="POST"
                                                            class="mt-2">
                                                            @csrf
                                                            <button type="submit"
                                                                class="w-full bg-green-600 text-white py-1 rounded text-xs hover:bg-green-700 font-bold">
                                                                Sync to Snipe-IT Assets
                                                            </button>
                                                        </form>
                                                    @elseif($item->is_synced)
                                                        <div
                                                            class="mt-2 flex items-center justify-center text-[10px] text-green-600 font-bold uppercase">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            Synced to Asset #{{ $item->snipeit_asset_id }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <!-- Consumable Checkout UI -->
                                                <div class="pt-3 border-t border-gray-200 dark:border-gray-600 print:hidden"
                                                    x-data="consumableSync({{ $item->id }}, {{ $item->is_synced ? 'true' : 'false' }})">

                                                    @if($item->is_synced)
                                                        <div
                                                            class="bg-indigo-50 dark:bg-indigo-900/20 p-3 rounded-lg border border-indigo-100 dark:border-indigo-800">
                                                            <div
                                                                class="flex items-center gap-2 text-indigo-700 dark:text-indigo-400 font-bold text-xs mb-1">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                        clip-rule="evenodd"></path>
                                                                </svg>
                                                                SUDAH TERCHECKOUT
                                                            </div>
                                                            <div class="text-[11px] text-gray-600 dark:text-gray-400 space-y-0.5">
                                                                <p><span class="font-bold">Item:</span>
                                                                    {{ $item->synced_item_name ?? '-' }}</p>
                                                                <p><span class="font-bold">Jumlah:</span>
                                                                    {{ $item->synced_qty ?? $item->qty }}</p>
                                                                <p><span class="font-bold">Lokasi:</span>
                                                                    {{ $item->synced_location_name ?? '-' }}</p>
                                                                <p><span class="font-bold text-[10px] uppercase">Snipe-IT ID:</span>
                                                                    {{ $item->snipeit_asset_id }}</p>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <form @submit.prevent="checkout()" class="space-y-3" x-show="!localSynced">
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                <div class="relative">
                                                                    <label
                                                                        class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Snipe-IT
                                                                        Consumable</label>
                                                                    <input type="text" x-model="searchConsumable"
                                                                        @input.debounce.500ms="findConsumables()"
                                                                        placeholder="Cari consumable..." :disabled="localSynced"
                                                                        class="w-full text-xs rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">

                                                                    <div x-show="consumables.length > 0"
                                                                        class="absolute z-50 w-full mt-1 bg-white border rounded shadow-lg max-h-40 overflow-y-auto">
                                                                        <template x-for="c in consumables" :key="c.id">
                                                                            <div @click="selectConsumable(c)"
                                                                                class="p-2 text-xs hover:bg-gray-100 cursor-pointer border-b last:border-0">
                                                                                <span x-text="c.name" class="font-bold"></span>
                                                                                <span class="text-gray-500"
                                                                                    x-text="'(Stock: ' + c.remaining + ')'"></span>
                                                                            </div>
                                                                        </template>
                                                                    </div>
                                                                    <input type="hidden" name="consumable_id"
                                                                        :value="selectedConsumableId">
                                                                </div>

                                                                <div class="relative">
                                                                    <label
                                                                        class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Assign
                                                                        To (Snipe-IT User)</label>
                                                                    <input type="text" x-model="searchUser"
                                                                        @input.debounce.500ms="findUsers()" placeholder="Cari user..."
                                                                        :disabled="localSynced"
                                                                        class="w-full text-xs rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">

                                                                    <div x-show="users.length > 0"
                                                                        class="absolute z-50 w-full mt-1 bg-white border rounded shadow-lg max-h-40 overflow-y-auto">
                                                                        <template x-for="u in users" :key="u.id">
                                                                            <div @click="selectUser(u)"
                                                                                class="p-2 text-xs hover:bg-gray-100 cursor-pointer border-b last:border-0">
                                                                                <span x-text="u.name" class="font-bold"></span>
                                                                                <span class="text-gray-500 text-[10px] block"
                                                                                    x-text="u.department || u.username"></span>
                                                                            </div>
                                                                        </template>
                                                                    </div>
                                                                    <input type="hidden" name="assigned_to" :value="selectedUserId">
                                                                </div>
                                                            </div>

                                                            <div class="flex items-end gap-2">
                                                                <div class="w-24">
                                                                    <label
                                                                        class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Qty</label>
                                                                    <input type="number" x-model="qty" min="1" :disabled="localSynced"
                                                                        class="w-full text-xs rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                                                </div>
                                                                <button type="submit"
                                                                    :disabled="!selectedConsumableId || !selectedUserId || loading || localSynced"
                                                                    class="flex-1 py-1.5 rounded text-xs font-bold text-white transition-colors"
                                                                    :class="selectedConsumableId && selectedUserId ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-400 cursor-not-allowed'">
                                                                    <span x-show="!loading">Checkout Consumable</span>
                                                                    <span x-show="loading">Processing...</span>
                                                                </button>
                                                            </div>
                                                        </form>

                                                        <div x-show="localSynced" x-cloak
                                                            class="bg-green-50 text-green-700 p-3 rounded-lg border border-green-200 text-xs font-bold flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            Checkout Berhasil! Mohon tunggu sebentar...
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Approval History (Vertical Log) -->
                    @if($request->approvalLogs->count() > 0)
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Approval History</h3>
                            <div class="space-y-6">
                                @foreach($request->approvalLogs as $log)
                                    <div class="flex gap-4">
                                        <!-- Timeline Icon -->
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 
                                                                                        @if($log->action === 'SUBMIT') bg-blue-50 border-blue-500 text-blue-600
                                                                                        @elseif($log->action === 'APPROVE') bg-green-50 border-green-500 text-green-600
                                                                                        @else bg-red-50 border-red-500 text-red-600
                                                                                        @endif">
                                                @if($log->action === 'SUBMIT')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                    </svg>
                                                @elseif($log->action === 'APPROVE')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 pt-1">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $log->user->name ?? 'System' }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $log->created_at->format('d M Y, H:i') }} •
                                                {{ ucfirst(strtolower($log->action)) }}
                                            </p>
                                            @if($log->note)
                                                <p
                                                    class="mt-2 text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg italic border border-gray-200 dark:border-gray-600">
                                                    "{{ $log->note }}"
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Digital Handover (Available to all roles) -->
                    @if($request->status == 'ON_DELIVERY')
                        <div
                            class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded text-sm text-blue-800 dark:text-blue-200 print:hidden">
                            <h4 class="font-bold mb-2">Konfirmasi Penerimaan Asset</h4>
                            <p class="mb-3 text-xs">Silakan upload foto asset yang diterima dan centang konfirmasi di bawah
                                ini.</p>

                            <form action="{{ route('requests.bast', $request->id) }}" method="POST"
                                enctype="multipart/form-data" class="space-y-3">
                                @csrf

                                <!-- Asset Photo Upload -->
                                <div>
                                    <label class="block text-xs font-bold mb-1">Foto Asset (Wajib)</label>
                                    <input type="file" name="asset_photo" accept="image/*" capture="environment"
                                        class="block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none"
                                        required>
                                    <p class="text-[10px] text-gray-500 mt-1">Format: JPG, PNG. Max: 2MB.</p>
                                </div>

                                <!-- E-Form Confirmation Checkbox -->
                                <div
                                    class="flex items-start gap-2 bg-white dark:bg-gray-800 p-2 rounded border border-blue-200 dark:border-blue-800">
                                    <input type="checkbox" name="e_form_confirm" id="e_form_confirm" required
                                        class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="e_form_confirm" class="text-xs leading-tight cursor-pointer">
                                        Saya menyatakan telah menerima asset tersebut dalam kondisi baik dan lengkap sesuai
                                        dengan spesifikasi yang tertera.
                                    </label>
                                </div>

                                <button type="submit"
                                    class="w-full py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs font-bold transition">
                                    Konfirmasi Terima Barang
                                </button>
                            </form>
                        </div>
                    @endif
                    <!-- Admin Actions -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 print:hidden">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Admin Actions</h4>
                        <div class="space-y-3">
                            @if($request->status === 'APPR_4')
                                <!-- Generate PO -->
                                <form action="{{ route('admin.po', $request->id) }}" method="GET">
                                    @if(!$request->po_number)
                                        <div class="mb-3">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Enter
                                                PO Number</label>
                                            <input type="text" name="po_number" required placeholder="e.g. PO/2023/001"
                                                class="w-full text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                                        </div>
                                    @endif
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                        {{ $request->po_number ? 'Download PO PDF' : 'Save & Download PO' }}
                                    </button>
                                    @if($request->po_number)
                                        <button type="button" onclick="window.print()"
                                            class="mt-2 w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                            Print Document
                                        </button>
                                    @endif
                                </form>
                            @elseif($request->status === 'PO_ISSUED')
                                <!-- Sync to Snipe-IT (Only for Assets) -->
                                @if($request->request_type !== 'NEW_CONSUMABLE')
                                    <form action="{{ route('admin.sync', $request->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                            Sync to Snipe-IT
                                        </button>
                                    </form>
                                @endif
                            @elseif($request->status === 'ON_DELIVERY' || $request->status === 'SYNCED')
                                <!-- Update Delivery -->
                                <div class="text-xs text-blue-600 mb-2 font-medium">Update Delivery Information</div>
                                <form action="{{ route('admin.delivery-info', $request->id) }}" method="POST"
                                    class="space-y-3">
                                    @csrf
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Expedition
                                            (Courier)</label>
                                        <input type="text" name="courier" value="{{ $request->courier }}" required
                                            placeholder="e.g. JNE, TIKI, Internal"
                                            class="w-full text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Receipt
                                            Number (Resi)</label>
                                        <input type="text" name="tracking_no" value="{{ $request->tracking_no }}" required
                                            placeholder="e.g. 123456789"
                                            class="w-full text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                                    </div>
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                        Save Delivery Info
                                    </button>
                                </form>
                            @endif

                            <!-- Download BAST (Removed) -->

                            {{-- Asset Handover Information --}}
                            @if($request->status == 'COMPLETED' && $request->asset_photo_path && $request->e_form_confirmed_at)
                                <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded shadow mb-4">
                                    <h4 class="font-bold text-blue-900 dark:text-blue-100 mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Informasi Serah Terima Asset
                                    </h4>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Asset Photo --}}
                                        <div>
                                            <label
                                                class="block text-xs font-semibold text-blue-800 dark:text-blue-200 mb-2">Foto
                                                Asset Diterima:</label>
                                            <a href="{{ asset('storage/' . $request->asset_photo_path) }}" target="_blank"
                                                class="block">
                                                <img src="{{ asset('storage/' . $request->asset_photo_path) }}"
                                                    alt="Asset Photo"
                                                    class="w-full max-w-sm rounded-lg border-2 border-blue-200 dark:border-blue-700 hover:border-blue-500 transition cursor-pointer print:max-w-xs">
                                            </a>
                                            <p class="text-xs text-gray-500 mt-1 print:hidden">Klik gambar untuk memperbesar
                                            </p>
                                        </div>

                                        {{-- Handover Details --}}
                                        <div class="space-y-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-semibold text-blue-800 dark:text-blue-200">Dikonfirmasi
                                                    Oleh:</label>
                                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                                    {{ $request->requester->name }}</p>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-semibold text-blue-800 dark:text-blue-200">Waktu
                                                    Konfirmasi:</label>
                                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                                    {{ \Carbon\Carbon::parse($request->e_form_confirmed_at)->format('d M Y, H:i') }}
                                                    WIB</p>
                                            </div>
                                            <div
                                                class="bg-white dark:bg-blue-800 p-3 rounded border border-blue-200 dark:border-blue-600 mt-3">
                                                <p class="text-xs text-blue-900 dark:text-blue-100">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <strong>E-Form Confirmed:</strong> Requester telah menyatakan bahwa
                                                    asset diterima dalam kondisi baik dan sesuai spesifikasi.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Download Request PDF --}}
                            <a href="{{ route('requests.pdf', $request->id) }}"
                                class="block w-full text-center py-2 px-4 mb-3 border border-indigo-600 rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 print:hidden">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Download Request PDF
                            </a>

                            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                                <a href="{{ route('admin.requests.fulfillment') }}"
                                    class="block w-full text-center py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700">
                                    Back to Order Processing
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Request Info & Context -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Request Info</h4>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Requester</span>
                                <span
                                    class="text-gray-900 dark:text-white font-medium">{{ $request->requester->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Email</span>
                                <span
                                    class="text-gray-900 dark:text-white font-medium text-xs">{{ $request->requester->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Department</span>
                                <span
                                    class="text-gray-900 dark:text-white font-medium">{{ $request->requester->department }}</span>
                            </div>
                        </div>

                        <hr class="my-4 border-gray-200 dark:border-gray-700">

                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Request Context</h4>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Request ID</span>
                                <span class="text-indigo-600 font-bold">#{{ $request->ticket_no }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Type</span>
                                <span
                                    class="text-gray-900 dark:text-white font-medium">{{ str_replace('_', ' ', $request->request_type) }}</span>
                            </div>
                            @if($request->beneficiary_name)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Beneficiary</span>
                                    <span class="text-gray-900 dark:text-white font-medium">{{ $request->beneficiary_name }}
                                        ({{ $request->beneficiary_type }})</span>
                                </div>
                            @endif
                        </div>

                        {{-- Global Disposal Form --}}
                        @if($request->disposal_doc_path)
                            <div
                                class="mt-4 p-3 bg-red-50 dark:bg-red-900/30 rounded-lg border border-red-100 dark:border-red-800">
                                <h5 class="text-[10px] font-bold text-red-700 dark:text-red-400 uppercase mb-2">Disposal
                                    Form</h5>
                                <a href="{{ asset('storage/' . $request->disposal_doc_path) }}" target="_blank"
                                    class="flex items-center gap-2 text-sm text-red-600 hover:text-red-800 font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    View Document
                                </a>
                            </div>
                        @endif

                        @if($request->shipping_address)
                            <hr class="my-4 border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Shipping To</h4>
                            <div class="text-sm">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $request->shipping_pic_name }}</p>
                                <p class="text-gray-500">{{ $request->shipping_pic_phone }}</p>
                                <p class="text-gray-500 text-xs mt-1 italic">{{ $request->shipping_address }}</p>
                            </div>
                        @endif

                        @if($request->po_number || $request->tracking_no)
                            <hr class="my-4 border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Delivery Info</h4>
                            <div class="space-y-2 text-sm">
                                @if($request->po_number)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">PO Number</span>
                                        <span class="text-purple-600 font-bold">{{ $request->po_number }}</span>
                                    </div>
                                @endif
                                @if($request->tracking_no)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Tracking</span>
                                        <span class="text-blue-600 font-bold">{{ $request->tracking_no }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Courier</span>
                                        <span class="text-gray-900 dark:text-white font-medium">{{ $request->courier }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        function consumableSync(itemId, initialIsSynced) {
            return {
                itemId: itemId,
                searchConsumable: '',
                searchUser: '',
                consumables: [],
                users: [],
                selectedConsumableId: null,
                selectedUserId: null,
                qty: 1,
                loading: false,
                localSynced: initialIsSynced,

                findConsumables() {
                    if (this.searchConsumable.length < 2) { this.consumables = []; return; }
                    fetch(`{{ route('admin.snipeit.consumables') }}?q=` + encodeURIComponent(this.searchConsumable))
                        .then(res => res.json())
                        .then(data => { this.consumables = data || []; });
                },

                findUsers() {
                    if (this.searchUser.length < 2) { this.users = []; return; }
                    fetch(`{{ route('admin.snipeit.users') }}?q=` + encodeURIComponent(this.searchUser))
                        .then(res => res.json())
                        .then(data => { this.users = data || []; });
                },

                selectConsumable(c) {
                    this.selectedConsumableId = c.id;
                    this.searchConsumable = c.name;
                    this.consumables = [];
                },

                selectUser(u) {
                    this.selectedUserId = u.id;
                    this.searchUser = u.name;
                    this.users = [];
                },

                checkout() {
                    console.log('Checkout called', {
                        itemId: this.itemId,
                        selectedConsumableId: this.selectedConsumableId,
                        selectedUserId: this.selectedUserId,
                        qty: this.qty,
                        localSynced: this.localSynced
                    });

                    if (!this.selectedConsumableId || !this.selectedUserId || this.localSynced) {
                        console.log('Checkout blocked: missing required fields or already synced');
                        return;
                    }
                    this.loading = true;

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('consumable_id', this.selectedConsumableId);
                    formData.append('assigned_to', this.selectedUserId);
                    formData.append('checkout_qty', this.qty);
                    formData.append('consumable_name', this.searchConsumable);
                    formData.append('user_name', this.searchUser);

                    const url = `{{ url('admin/item') }}/${this.itemId}/checkout-consumable`;
                    console.log('Fetching URL:', url);

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                        .then(res => {
                            console.log('Response status:', res.status);
                            return res.json();
                        })
                        .then(data => {
                            console.log('Response data:', data);
                            this.loading = false;
                            if (data.success) {
                                this.localSynced = true;
                                // Trigger dynamic update if needed or just reload to sync global status
                                setTimeout(() => { window.location.reload(); }, 1500);
                            } else {
                                alert(data.message || 'Gagal checkout');
                            }
                        })
                        .catch(e => {
                            this.loading = false;
                            console.error('Fetch error:', e);
                            alert('Terjadi kesalahan saat menghubungi server.');
                        });
                }
            }
        }
    </script>
</x-sidebar-layout>