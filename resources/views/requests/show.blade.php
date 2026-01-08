<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Request #{{ $requestData->ticket_no }}
            </h2>
            <div class="flex gap-2">
                @if($requestData->request_type === 'NEW_HIRE' || $requestData->request_type === 'NEW_BRANCH')
                    <span class="px-3 py-1 text-sm font-bold bg-green-100 text-green-700 rounded-full">
                        New Hire / Branch
                    </span>
                @elseif($requestData->request_type === 'REPLACEMENT')
                    @if($requestData->replacement_reason === 'AGING')
                        <span class="px-3 py-1 text-sm font-bold bg-yellow-100 text-yellow-700 rounded-full">
                            Replacement: Aging
                        </span>
                    @else
                        <span class="px-3 py-1 text-sm font-bold bg-red-100 text-red-700 rounded-full">
                            Replacement: {{ ucfirst(strtolower($requestData->replacement_reason)) }}
                        </span>
                    @endif
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Print-Only Header (hidden on screen, visible on print) -->
            <div class="print-header hidden print:block mb-6 pb-4 border-b-2 border-gray-300">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold">Request #{{ $requestData->ticket_no }}</h1>
                    <div>
                        @if($requestData->request_type === 'NEW_HIRE' || $requestData->request_type === 'NEW_BRANCH')
                            <span class="px-3 py-1 text-sm font-bold border-2 border-green-600 rounded-full">
                                New Hire / Branch
                            </span>
                        @elseif($requestData->request_type === 'REPLACEMENT')
                            @if($requestData->replacement_reason === 'AGING')
                                <span class="px-3 py-1 text-sm font-bold border-2 border-yellow-600 rounded-full">
                                    Replacement: Aging
                                </span>
                            @else
                                <span class="px-3 py-1 text-sm font-bold border-2 border-red-600 rounded-full">
                                    Replacement: {{ ucfirst(strtolower($requestData->replacement_reason)) }}
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    Printed on: {{ now()->format('d M Y, H:i') }} |
                    Status: {{ $requestData->status }}
                </p>
            </div>

            <!-- Progress Stepper -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 no-print">
                <div class="flex items-center justify-between overflow-x-auto">
                    @php
                        // Build dynamic steps based on assigned approvers
                        $approvers = $requestData->approvers->keyBy('level');
                        $levelLabels = [1 => 'Level 1', 2 => 'Level 2', 3 => 'Level 3', 4 => 'Level 4'];

                        // Status to level mapping
                        $statusToLevel = [
                            'SUBMITTED' => 0,
                            'APPR_1' => 1,
                            'APPR_2' => 2,
                            'APPR_3' => 3,
                            'APPR_4' => 4, // Final Approval (Director Level)
                            'PO_ISSUED' => 5,
                            'ON_DELIVERY' => 6,
                            'COMPLETED' => 7,
                            'SYNCED' => 7,
                        ];

                        $currentLevel = $statusToLevel[$requestData->status] ?? 0;

                        // Build steps array
                        $steps = [];

                        // Step 1: Submitted
                        $steps[] = [
                            'label' => 'Submitted',
                            'sublabel' => $requestData->requester->name ?? '',
                            'completed' => $currentLevel >= 0,
                            'active' => $currentLevel == 0,
                        ];

                        // Approval levels 1-4
                        foreach ([1, 2, 3, 4] as $level) {
                            $approver = $approvers->get($level);
                            $levelName = $levelLabels[$level];
                            $approverName = $approver && $approver->user ? $approver->user->name : '-';
                            $isApproved = $currentLevel >= $level;
                            $isActive = $currentLevel == $level - 1; // Waiting for this level

                            // Custom logic: If status is exactly this level's APPR_X, it means it's approved AT this level
                            // But usually APPR_1 means Approved BY Level 1, waiting for Level 2.

                            $steps[] = [
                                'label' => "{$levelName}",
                                'sublabel' => $approverName,
                                'completed' => $isApproved,
                                'active' => $isActive,
                                'status' => $approver ? $approver->status : 'pending',
                            ];
                        }

                        // Post-approval steps
                        $steps[] = [
                            'label' => 'PO Issued',
                            'sublabel' => $requestData->po_number ?? '-',
                            'completed' => $currentLevel >= 5,
                            'active' => $currentLevel == 4, // APPR_4 status (Approved) triggers PO step activation
                        ];

                        $steps[] = [
                            'label' => 'On Delivery',
                            'sublabel' => $requestData->courier ?? '-',
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

                    @if($requestData->status === 'REJECTED')
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
                                    @elseif(str_contains($step['label'], 'Level'))
                                        @php
                                            $approver = $approvers->get(intval(str_replace('Level ', '', $step['label'])));
                                        @endphp
                                        @if($approver && $approver->status !== 'pending')
                                            <div
                                                class="w-8 h-8 rounded-full border-2 {{ $approver->status == 'approved' ? 'bg-green-50 border-green-500 text-green-600' : 'bg-red-50 border-red-500 text-red-600' }} flex items-center justify-center">
                                                @if($approver->status == 'approved')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @endif
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
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <div
                                    class="mt-2 text-xs font-semibold text-center {{ $step['completed'] ? 'text-indigo-600' : ($step['active'] ? 'text-indigo-600' : 'text-gray-400') }}">
                                    {{ $step['label'] }}
                                </div>
                                @if($step['sublabel'])
                                    <div class="text-[10px] text-gray-500 text-center truncate max-w-[100px]"
                                        title="{{ $step['sublabel'] }}">
                                        {{ $step['sublabel'] }}
                                    </div>
                                @endif
                            </div>
                            @if(!$loop->last)
                                <div class="flex-1 h-1 {{ $step['completed'] ? 'bg-indigo-600' : 'bg-gray-200' }} -mx-2 mb-8 z-0">
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left: Order Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Items Requested</h3>
                            <span
                                class="text-sm font-bold text-indigo-600 dark:text-indigo-400">#{{ $requestData->ticket_no }}</span>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="text-center text-xs font-medium text-gray-500 uppercase">Documents</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($requestData->items as $item)
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item->item_name ?? $item->product->name ?? 'N/A' }}
                                            @if($item->item_specs)
                                                <div class="text-xs text-gray-400">{{ $item->item_specs }}</div>
                                            @endif
                                            @if($item->item_link)
                                                <a href="{{ $item->item_link }}" target="_blank"
                                                    class="text-xs text-indigo-500 hover:underline">View Link</a>
                                            @endif
                                            @if($item->serial_number)
                                                <div class="text-xs text-green-600">SN: {{ $item->serial_number }}</div>
                                            @endif
                                            @if($item->asset_name)
                                                <div class="text-xs text-purple-600">Name: {{ $item->asset_name }}</div>
                                            @endif
                                            @if($item->asset_tag)
                                                <div class="text-xs text-blue-600">Tag: {{ $item->asset_tag }}</div>
                                            @endif
                                            @if($item->is_synced && $item->synced_at)
                                                <div class="text-xs text-emerald-600 font-medium">
                                                    ✓ Synced: {{ $item->synced_at->format('d M Y H:i') }}
                                                </div>
                                            @endif

                                            {{-- Admin: Different forms for Consumable vs Asset --}}
                                            @if(auth()->user()->role == 'admin' && $requestData->po_number && !$item->is_synced)
                                                @php
                                                    $isConsumable = str_contains(strtoupper($requestData->request_type ?? ''), 'CONSUMABLE');
                                                @endphp

                                                @if($isConsumable)
                                                    {{-- Consumable Checkout Form --}}
                                                    <div
                                                        class="mt-2 p-3 bg-orange-50 dark:bg-orange-900/30 rounded border border-orange-200 dark:border-orange-700 space-y-2 print:hidden">
                                                        <p class="text-xs font-bold text-orange-700 dark:text-orange-300 mb-2">
                                                            Checkout Consumable</p>
                                                        <form method="POST"
                                                            action="{{ route('admin.item.checkout-consumable', $item->id) }}"
                                                            class="space-y-2" id="consumable-form-{{ $item->id }}">
                                                            @csrf
                                                            {{-- Consumable Search --}}
                                                            <div>
                                                                <label
                                                                    class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Consumable
                                                                    (Snipe-IT)</label>
                                                                <div class="relative">
                                                                    <input type="text" id="consumable-search-{{ $item->id }}"
                                                                        placeholder="Cari consumable..."
                                                                        class="w-full text-xs px-2 py-1.5 border rounded"
                                                                        autocomplete="off">
                                                                    <input type="hidden" name="consumable_id"
                                                                        id="consumable-id-{{ $item->id }}" required>
                                                                    <div id="consumable-results-{{ $item->id }}"
                                                                        class="absolute z-10 w-full bg-white dark:bg-gray-800 border rounded shadow-lg hidden max-h-40 overflow-y-auto">
                                                                    </div>
                                                                </div>
                                                                <p class="text-xs text-gray-400 mt-1"
                                                                    id="consumable-selected-{{ $item->id }}"></p>
                                                            </div>

                                                            {{-- User Search --}}
                                                            <div>
                                                                <label
                                                                    class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Checkout
                                                                    ke User</label>
                                                                <div class="relative">
                                                                    <input type="text" id="user-search-{{ $item->id }}"
                                                                        placeholder="Cari user..."
                                                                        class="w-full text-xs px-2 py-1.5 border rounded"
                                                                        autocomplete="off">
                                                                    <input type="hidden" name="assigned_to"
                                                                        id="user-id-{{ $item->id }}" required>
                                                                    <div id="user-results-{{ $item->id }}"
                                                                        class="absolute z-10 w-full bg-white dark:bg-gray-800 border rounded shadow-lg hidden max-h-40 overflow-y-auto">
                                                                    </div>
                                                                </div>
                                                                <p class="text-xs text-gray-400 mt-1"
                                                                    id="user-selected-{{ $item->id }}"></p>
                                                            </div>

                                                            {{-- Quantity --}}
                                                            <div>
                                                                <label
                                                                    class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Quantity</label>
                                                                <input type="number" name="checkout_qty" value="1" min="1"
                                                                    class="w-full text-xs px-2 py-1.5 border rounded">
                                                            </div>

                                                            {{-- Note --}}
                                                            <div>
                                                                <label
                                                                    class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Note
                                                                    (opsional)</label>
                                                                <input type="text" name="note" placeholder="Catatan checkout..."
                                                                    class="w-full text-xs px-2 py-1.5 border rounded">
                                                            </div>

                                                            <button type="submit"
                                                                class="w-full text-xs bg-orange-600 text-white py-2 px-4 rounded hover:bg-orange-700 font-bold shadow-md border-0">
                                                                ✓ Checkout to User
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    {{-- Regular Asset Sync Form --}}
                                                    <div
                                                        class="mt-2 p-2 bg-gray-50 dark:bg-gray-700 rounded border space-y-2 print:hidden">
                                                        <form method="POST" action="{{ route('admin.item.serial-tag', $item->id) }}"
                                                            class="space-y-1">
                                                            @csrf
                                                            <input type="text" name="asset_name" value="{{ $item->asset_name }}"
                                                                placeholder="Asset Name" required
                                                                class="w-full text-xs px-2 py-1 border rounded">
                                                            <input type="text" name="serial_number"
                                                                value="{{ $item->serial_number }}" placeholder="Serial Number"
                                                                required class="w-full text-xs px-2 py-1 border rounded">
                                                            <input type="text" name="asset_tag" value="{{ $item->asset_tag }}"
                                                                placeholder="Asset Tag" required
                                                                class="w-full text-xs px-2 py-1 border rounded">
                                                            <button type="submit"
                                                                class="w-full text-xs bg-green-600 text-white py-1 rounded hover:bg-green-700">
                                                                Save Asset Info
                                                            </button>
                                                        </form>
                                                        @if($item->serial_number && $item->asset_tag && $item->asset_name)
                                                            <form method="POST" action="{{ route('admin.item.sync', $item->id) }}"
                                                                class="mt-2">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="w-full text-xs text-blue-600 hover:text-blue-800 flex items-center justify-center gap-1 py-1 hover:underline">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                    Sync Snipe-IT
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif

                                            {{-- Show checkout info after sync for consumables --}}
                                            @if($item->is_synced)
                                                @php
                                                    $isConsumable = str_contains(strtoupper($requestData->request_type ?? ''), 'CONSUMABLE');
                                                @endphp
                                                @if($isConsumable)
                                                    <div
                                                        class="mt-2 p-3 bg-green-50 dark:bg-green-900/30 rounded border border-green-200 dark:border-green-700">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span
                                                                class="text-sm font-bold text-green-700 dark:text-green-300">Checked
                                                                Out</span>
                                                        </div>
                                                        <div class="text-xs text-green-600 dark:text-green-400 space-y-1">
                                                            <p><span class="font-medium">ID:</span> {{ $item->snipeit_asset_id }}
                                                            </p>
                                                            <p><span class="font-medium">Synced:</span>
                                                                {{ $item->synced_at ? \Carbon\Carbon::parse($item->synced_at)->format('d M Y H:i') : '-' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div
                                                        class="mt-2 p-2 bg-green-50 dark:bg-green-900/30 rounded border border-green-200 dark:border-green-700">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            <span
                                                                class="text-xs font-medium text-green-700 dark:text-green-300">Synced
                                                                to Snipe-IT</span>
                                                        </div>
                                                        @if($item->snipeit_asset_id)
                                                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">Asset ID:
                                                                {{ $item->snipeit_asset_id }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif
                                            @if($requestData->status == 'COMPLETED')
                                                <div
                                                    class="bg-green-50 dark:bg-green-900/30 p-4 rounded shadow text-sm print:block">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span class="font-bold text-green-800 dark:text-green-200">✓ Request
                                                            Completed</span>
                                                    </div>
                                                    <p class="text-xs text-green-700 dark:text-green-300 mt-1">This request has
                                                        been fully
                                                        processed.</p>
                                                </div>

                                                {{-- Asset Handover Information --}}
                                                @if($requestData->asset_photo_path && $requestData->e_form_confirmed_at)
                                                    <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded shadow mt-4 print:block">
                                                        <h4
                                                            class="font-bold text-blue-900 dark:text-blue-100 mb-3 flex items-center gap-2">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
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
                                                                <a href="{{ asset('storage/' . $requestData->asset_photo_path) }}"
                                                                    target="_blank" class="block">
                                                                    <img src="{{ asset('storage/' . $requestData->asset_photo_path) }}"
                                                                        alt="Asset Photo"
                                                                        class="w-full max-w-sm rounded-lg border-2 border-blue-200 dark:border-blue-700 hover:border-blue-500 transition cursor-pointer print:max-w-xs">
                                                                </a>
                                                                <p class="text-xs text-gray-500 mt-1 print:hidden">Klik gambar untuk
                                                                    memperbesar</p>
                                                            </div>

                                                            {{-- Handover Details --}}
                                                            <div class="space-y-2">
                                                                <div>
                                                                    <label
                                                                        class="block text-xs font-semibold text-blue-800 dark:text-blue-200">Dikonfirmasi
                                                                        Oleh:</label>
                                                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                                                        {{ $requestData->requester->name }}
                                                                    </p>
                                                                </div>
                                                                <div>
                                                                    <label
                                                                        class="block text-xs font-semibold text-blue-800 dark:text-blue-200">Waktu
                                                                        Konfirmasi:</label>
                                                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                                                        {{ \Carbon\Carbon::parse($requestData->e_form_confirmed_at)->format('d M Y, H:i') }}
                                                                        WIB
                                                                    </p>
                                                                </div>
                                                                <div
                                                                    class="bg-white dark:bg-blue-800 p-3 rounded border border-blue-200 dark:border-blue-600 mt-3">
                                                                    <p class="text-xs text-blue-900 dark:text-blue-100">
                                                                        <svg class="w-4 h-4 inline mr-1" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                            </path>
                                                                        </svg>
                                                                        <strong>E-Form Confirmed:</strong> Requester telah
                                                                        menyatakan bahwa asset diterima dalam kondisi baik dan
                                                                        sesuai spesifikasi.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="py-3 text-sm text-center">
                                            @if($item->disposal_doc_path && !empty($item->disposal_doc_path))
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ asset('storage/' . $item->disposal_doc_path) }}" target="_blank"
                                                        class="inline-flex items-center gap-1 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-2 py-1 rounded transition"
                                                        title="View">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                            </path>
                                                        </svg>
                                                        View
                                                    </a>
                                                    <a href="{{ asset('storage/' . $item->disposal_doc_path) }}" download
                                                        class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded transition"
                                                        title="Download">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                            </path>
                                                        </svg>
                                                        Download
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Admin: Delivery Info Form - shows after all items synced --}}
                        @if(auth()->user()->role == 'admin' && $requestData->po_number)
                            @php
                                $allSynced = $requestData->items->every(fn($item) => $item->is_synced);
                            @endphp
                            @if($allSynced && $requestData->status == 'PO_ISSUED')
                                <div
                                    class="mt-6 p-4 bg-green-50 dark:bg-green-900 rounded-lg border border-green-200 dark:border-green-700">
                                    <h4 class="text-sm font-bold text-green-800 dark:text-green-200 mb-3">
                                        ✓ Semua item sudah di-sync ke Snipe-IT
                                    </h4>
                                    <form method="POST" action="{{ route('admin.delivery-info', $requestData->id) }}"
                                        class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nama
                                                Ekspedisi</label>
                                            <input type="text" name="courier" placeholder="Contoh: JNE, J&T, GoSend" required
                                                class="w-full px-3 py-2 border rounded-lg text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">No.
                                                Resi</label>
                                            <input type="text" name="tracking_no" placeholder="Masukkan nomor resi" required
                                                class="w-full px-3 py-2 border rounded-lg text-sm">
                                        </div>
                                        <button type="submit"
                                            class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm">
                                            Update Status Pengiriman
                                        </button>
                                    </form>
                                </div>
                            @elseif(!$allSynced)
                                <div
                                    class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900 rounded border border-yellow-200 dark:border-yellow-700">
                                    <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                        ⚠ Lengkapi Serial/Tag dan sync semua item ke Snipe-IT untuk melanjutkan ke pengiriman.
                                    </p>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Approval History -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Approval History</h3>
                        <div class="space-y-6">
                            @foreach($requestData->approvalLogs as $log)
                                <div class="flex gap-4">
                                    <!-- Timeline Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 @if($log->action === 'SUBMIT') bg-blue-50 border-blue-500 text-blue-600
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
                </div>

                <!-- Right: Actions & Info -->
                <div class="space-y-6">

                    <!-- Action Box -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Actions</h3>

                        <!-- Print and Share Buttons -->
                        <div class="flex gap-2 mb-4 print:hidden">
                            <button onclick="window.print()"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                Print
                            </button>
                            <button onclick="copyShareLink()" id="shareBtn"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded-lg font-medium hover:bg-indigo-200 dark:hover:bg-indigo-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                                    </path>
                                </svg>
                                <span id="shareBtnText">Share Link</span>
                            </button>
                        </div>

                        <!-- Approvers -->
                        @php
                            $userId = auth()->id();
                            $canApprove = false;
                            $currentLevel = null;

                            // Status to level mapping
                            $statusToLevel = [
                                'SUBMITTED' => 1,
                                'APPR_1' => 2,
                                'APPR_2' => 3,
                                'APPR_3' => 4,
                            ];

                            $requiredLevel = $statusToLevel[$requestData->status] ?? null;

                            if ($requiredLevel) {
                                // Check if current user is assigned as approver for this level
                                $isAssignedApprover = $requestData->approvers
                                    ->where('level', $requiredLevel)
                                    ->where('user_id', $userId)
                                    ->where('status', 'pending')
                                    ->isNotEmpty();

                                if ($isAssignedApprover) {
                                    $canApprove = true;
                                    $currentLevel = $requiredLevel;
                                }
                            }
                        @endphp

                        @if($canApprove)
                            <form method="POST" action="{{ route('requests.status', $requestData->id) }}"
                                class="space-y-3 print:hidden">
                                @csrf
                                <textarea name="note" placeholder="Optional note..."
                                    class="w-full rounded border-gray-300 text-sm"></textarea>
                                <div class="grid grid-cols-2 gap-3">
                                    <button type="submit" name="action" value="approve"
                                        class="w-full py-2 bg-green-600 text-white rounded hover:bg-green-700">Approve</button>
                                    <button type="submit" name="action" value="reject"
                                        class="w-full py-2 bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
                                </div>
                            </form>
                        @endif

                        <!-- Admin: Submit/Print PO -->
                        @if(auth()->user()->role == 'admin' && $requestData->status == 'APPR_4' && !$requestData->po_number)
                            <div class="mb-3">
                                <h4 class="text-sm font-bold mb-2">Submit PO Number</h4>
                                <form method="POST" action="{{ route('admin.po.submit', $requestData->id) }}"
                                    class="space-y-2 print:hidden">
                                    @csrf
                                    <input type="text" name="po_number" placeholder="Masukkan PO Number" required
                                        class="w-full rounded border-gray-300 text-sm px-3 py-2">
                                    @error('po_number') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                    <button type="submit"
                                        class="w-full py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm font-medium">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Submit PO
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if(auth()->user()->role == 'admin' && $requestData->po_number)
                            <a href="{{ route('admin.po', $requestData->id) }}?download=1"
                                class="block w-full text-center py-2 px-4 mb-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 print:hidden">
                                Print Purchase Order
                            </a>
                        @endif

                        {{-- Download Request Detail PDF --}}
                        <a href="{{ route('requests.pdf', $requestData->id) }}"
                            class="block w-full text-center py-2 px-4 mb-2 border border-indigo-600 rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 print:hidden">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Download Request PDF
                        </a>

                        <!-- Digital Handover (ONLY for Requester) -->
                        @if($requestData->status == 'ON_DELIVERY' && Auth::id() === $requestData->requester_id)
                            <div
                                class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded text-sm text-blue-800 dark:text-blue-200 print:hidden mb-4">
                                <h4 class="font-bold mb-2">Konfirmasi Penerimaan Asset</h4>
                                <p class="mb-3 text-xs">Silakan upload foto asset yang diterima dan centang konfirmasi di
                                    bawah ini.</p>

                                <form action="{{ route('requests.bast', $requestData->id) }}" method="POST"
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
                                            Saya menyatakan telah menerima asset tersebut dalam kondisi baik dan lengkap
                                            sesuai dengan spesifikasi yang tertera.
                                        </label>
                                    </div>

                                    <button type="submit"
                                        class="w-full py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs font-bold transition">
                                        Konfirmasi Terima Barang
                                    </button>
                                </form>
                            </div>
                        @elseif($requestData->status == 'ON_DELIVERY' && Auth::id() !== $requestData->requester_id)
                            <div
                                class="bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded text-sm text-yellow-800 dark:text-yellow-200 print:hidden mb-4">
                                <p class="font-semibold">⏳ Menunggu Konfirmasi Penerimaan</p>
                                <p class="text-xs mt-1">Hanya requester yang dapat mengkonfirmasi penerimaan asset.</p>
                            </div>
                        @endif

                        @if($requestData->status == 'COMPLETED')
                            <div class="text-green-600 font-bold text-center">Request Completed</div>
                            @if(auth()->user()->role == 'admin')
                                <form method="POST" action="{{ route('admin.sync', $requestData->id) }}" class="mt-4">
                                    @csrf
                                    <button class="w-full py-2 bg-gray-800 text-white rounded hover:bg-gray-700">Sync to
                                        Snipe-IT</button>
                                </form>
                            @endif
                        @endif

                    </div>

                    <!-- Info Box -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded shadow">
                        <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-2">Requester Info</h4>
                        <p class="text-sm text-gray-500">Requester: <span
                                class="font-bold">{{ $requestData->requester->name }}</span></p>
                        <p class="text-sm text-gray-500">Email: {{ $requestData->requester->email }}</p>

                        <hr class="my-3 border-gray-200">
                        <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-2">Request Context</h4>
                        <p class="text-sm text-gray-500">Request ID: <span
                                class="font-bold text-indigo-600">#{{ $requestData->ticket_no }}</span></p>
                        <p class="text-sm text-gray-500">Type: {{ str_replace('_', ' ', $requestData->request_type) }}
                        </p>
                        <p class="text-sm text-gray-500">Beneficiary: {{ $requestData->beneficiary_name }}
                            ({{ $requestData->beneficiary_type }})</p>

                        {{-- Priority Badge --}}
                        <div class="mt-2">
                            <span class="text-sm text-gray-500">Priority: </span>
                            @php
                                $priorityColors = [
                                    'low' => 'bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-200',
                                    'medium' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
                                    'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300',
                                    'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
                                ];
                                $priorityColor = $priorityColors[$requestData->priority ?? 'low'] ?? $priorityColors['low'];
                            @endphp
                            <span
                                class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $priorityColor }}">
                                {{ ucfirst($requestData->priority ?? 'low') }}
                            </span>
                        </div>



                        @if($requestData->disposal_doc_path && !empty($requestData->disposal_doc_path))
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $requestData->disposal_doc_path) }}" target="_blank"
                                    class="text-sm text-red-600 underline font-bold flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    View Disposal Form
                                </a>
                            </div>
                        @endif

                        @if($requestData->shipping_address)
                            <hr class="my-3 border-gray-200">
                            <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-2">Shipping to</h4>
                            <p class="text-xs text-gray-500">{{ $requestData->shipping_pic_name }}
                                ({{ $requestData->shipping_pic_phone }})</p>
                            <p class="text-xs text-gray-500 italic mt-1">{{ $requestData->shipping_address }}</p>
                        @endif

                        <hr class="my-3 border-gray-200">
                        @if($requestData->po_number)
                            <p class="text-sm text-gray-500 mt-2">PO: {{ $requestData->po_number }}</p>
                        @endif
                        @if($requestData->tracking_no)
                            <p class="text-sm text-gray-500 mt-2">Tracking: {{ $requestData->tracking_no }}
                                ({{ $requestData->courier }})</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {

            /* Hide navigation elements */
            nav,
            header,
            aside,
            .no-print,
            button,
            form,
            .sticky {
                display: none !important;
            }

            /* Show print header */
            .print-header {
                display: block !important;
            }

            .print-header span {
                border-color: #333 !important;
            }

            /* Reset body and layout for print */
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Ensure content takes full width */
            .print-area {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 20px !important;
            }

            /* Show only main content */
            .py-12 {
                padding: 0 !important;
            }

            /* Make sure text is readable */
            * {
                color: black !important;
                background: white !important;
            }

            /* Hide sidebar layout elements */
            [class*="sidebar"],
            [class*="dark:bg-gray"] {
                background: white !important;
            }

            /* Ensure grid layouts print properly */
            .grid {
                display: block !important;
            }

            .lg\:col-span-2,
            .lg\:col-span-1 {
                width: 100% !important;
            }
        }
    </style>

    <script>
        function copyShareLink() {
            const url = window.location.href;

            // Try modern clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(() => {
                    showCopySuccess();
                }).catch(err => {
                    fallbackCopy(url);
                });
            } else {
                fallbackCopy(url);
            }
        }

        function fallbackCopy(text) {
            // Fallback for older browsers or non-secure contexts
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-9999px';
            textArea.style.top = '-9999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand('copy');
                showCopySuccess();
            } catch (err) {
                alert('Link: ' + text);
            }

            document.body.removeChild(textArea);
        }

        function showCopySuccess() {
            const btnText = document.getElementById('shareBtnText');
            const originalText = btnText.textContent;
            btnText.textContent = 'Copied!';

            // Change button color temporarily
            const btn = document.getElementById('shareBtn');
            btn.classList.remove('bg-indigo-100', 'dark:bg-indigo-900/50');
            btn.classList.add('bg-green-100', 'dark:bg-green-900/50');

            setTimeout(() => {
                btnText.textContent = originalText;
                btn.classList.remove('bg-green-100', 'dark:bg-green-900/50');
                btn.classList.add('bg-indigo-100', 'dark:bg-indigo-900/50');
            }, 2000);
        }
        // ==================== CONSUMABLE CHECKOUT SEARCH ====================
        document.addEventListener('DOMContentLoaded', function () {
            // Setup consumable search for each item
            document.querySelectorAll('[id^="consumable-search-"]').forEach(function (input) {
                const itemId = input.id.replace('consumable-search-', '');
                let debounceTimer;

                input.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    const query = this.value.trim();
                    const resultsDiv = document.getElementById('consumable-results-' + itemId);

                    if (query.length < 2) {
                        resultsDiv.classList.add('hidden');
                        return;
                    }

                    debounceTimer = setTimeout(function () {
                        fetch('{{ route("admin.snipeit.consumables") }}?q=' + encodeURIComponent(query))
                            .then(response => response.json())
                            .then(data => {
                                resultsDiv.innerHTML = '';
                                if (data.error) {
                                    resultsDiv.innerHTML = '<div class="p-2 text-xs text-red-500">' + data.error + '</div>';
                                } else if (data.length === 0) {
                                    resultsDiv.innerHTML = '<div class="p-2 text-xs text-gray-400">Tidak ditemukan</div>';
                                } else {
                                    data.forEach(function (item) {
                                        const div = document.createElement('div');
                                        div.className = 'p-2 text-xs hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b';
                                        div.innerHTML = '<div class="font-medium">' + item.name + '</div><div class="text-gray-400">Stock: ' + item.remaining + '/' + item.qty + ' | ' + item.category + '</div>';
                                        div.addEventListener('click', function () {
                                            document.getElementById('consumable-id-' + itemId).value = item.id;
                                            document.getElementById('consumable-search-' + itemId).value = item.name;
                                            document.getElementById('consumable-selected-' + itemId).textContent = '✓ ' + item.name + ' (ID: ' + item.id + ')';
                                            resultsDiv.classList.add('hidden');
                                        });
                                        resultsDiv.appendChild(div);
                                    });
                                }
                                resultsDiv.classList.remove('hidden');
                            })
                            .catch(err => {
                                resultsDiv.innerHTML = '<div class="p-2 text-xs text-red-500">Error: ' + err.message + '</div>';
                                resultsDiv.classList.remove('hidden');
                            });
                    }, 300);
                });

                // Hide results when clicking outside
                document.addEventListener('click', function (e) {
                    if (!input.contains(e.target)) {
                        document.getElementById('consumable-results-' + itemId).classList.add('hidden');
                    }
                });
            });

            // Setup user search for each item
            document.querySelectorAll('[id^="user-search-"]').forEach(function (input) {
                const itemId = input.id.replace('user-search-', '');
                let debounceTimer;

                input.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    const query = this.value.trim();
                    const resultsDiv = document.getElementById('user-results-' + itemId);

                    if (query.length < 2) {
                        resultsDiv.classList.add('hidden');
                        return;
                    }

                    debounceTimer = setTimeout(function () {
                        fetch('{{ route("admin.snipeit.users") }}?q=' + encodeURIComponent(query))
                            .then(response => response.json())
                            .then(data => {
                                resultsDiv.innerHTML = '';
                                if (data.error) {
                                    resultsDiv.innerHTML = '<div class="p-2 text-xs text-red-500">' + data.error + '</div>';
                                } else if (data.length === 0) {
                                    resultsDiv.innerHTML = '<div class="p-2 text-xs text-gray-400">Tidak ditemukan</div>';
                                } else {
                                    data.forEach(function (user) {
                                        const div = document.createElement('div');
                                        div.className = 'p-2 text-xs hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b';
                                        div.innerHTML = '<div class="font-medium">' + user.name + '</div><div class="text-gray-400">' + (user.email || user.username || '') + '</div>';
                                        div.addEventListener('click', function () {
                                            document.getElementById('user-id-' + itemId).value = user.id;
                                            document.getElementById('user-search-' + itemId).value = user.name;
                                            document.getElementById('user-selected-' + itemId).textContent = '✓ ' + user.name + ' (ID: ' + user.id + ')';
                                            resultsDiv.classList.add('hidden');
                                        });
                                        resultsDiv.appendChild(div);
                                    });
                                }
                                resultsDiv.classList.remove('hidden');
                            })
                            .catch(err => {
                                resultsDiv.innerHTML = '<div class="p-2 text-xs text-red-500">Error: ' + err.message + '</div>';
                                resultsDiv.classList.remove('hidden');
                            });
                    }, 300);
                });

                // Hide results when clicking outside
                document.addEventListener('click', function (e) {
                    if (!input.contains(e.target)) {
                        document.getElementById('user-results-' + itemId).classList.add('hidden');
                    }
                });
            });
        });
    </script>
</x-sidebar-layout>