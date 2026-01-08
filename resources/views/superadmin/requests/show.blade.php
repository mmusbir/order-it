<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('superadmin.requests') }}"
                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Back to Requests
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <!-- Header -->
                <div class="flex justify-between items-start mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $request->ticket_no }}</h2>
                        <p class="text-gray-500 dark:text-gray-400">Created
                            {{ $request->created_at->format('M d, Y H:i') }}
                        </p>
                        <!-- Print and Share Buttons -->
                        <div class="flex gap-2 mt-3">
                            <button onclick="window.print()"
                                class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                Print
                            </button>
                            <button onclick="copyShareLink()" id="shareBtn"
                                class="flex items-center gap-2 px-3 py-1.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded-lg text-sm font-medium hover:bg-indigo-200 dark:hover:bg-indigo-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                                    </path>
                                </svg>
                                <span id="shareBtnText">Share Link</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($request->request_type === 'NEW_HIRE' || $request->request_type === 'NEW_BRANCH')
                            <span class="px-3 py-1 text-sm font-bold bg-green-100 text-green-700 rounded-full">
                                New Hire / Branch
                            </span>
                        @elseif($request->request_type === 'REPLACEMENT')
                            @if($request->replacement_reason === 'AGING')
                                <span class="px-3 py-1 text-sm font-bold bg-yellow-100 text-yellow-700 rounded-full">
                                    Replacement: Aging
                                </span>
                            @else
                                <span class="px-3 py-1 text-sm font-bold bg-red-100 text-red-700 rounded-full">
                                    Replacement: {{ ucfirst(strtolower($request->replacement_reason ?? 'Other')) }}
                                </span>
                            @endif
                        @endif
                        @php
                            $statusColors = [
                                'SUBMITTED' => 'bg-yellow-100 text-yellow-700',
                                'APPR_MGR' => 'bg-blue-100 text-blue-700',
                                'APPR_HEAD' => 'bg-blue-100 text-blue-700',
                                'APPR_DIR' => 'bg-indigo-100 text-indigo-700',
                                'PO_ISSUED' => 'bg-purple-100 text-purple-700',
                                'ON_DELIVERY' => 'bg-cyan-100 text-cyan-700',
                                'COMPLETED' => 'bg-green-100 text-green-700',
                                'SYNCED' => 'bg-green-100 text-green-700',
                                'REJECTED' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span
                            class="px-4 py-2 rounded-full text-sm font-semibold {{ $statusColors[$request->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $request->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Content (2/3) -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Requester Info -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Requester Information
                            </h3>
                            <div class="grid grid-cols-3 gap-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $request->requester->name ?? 'Unknown' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Department</p>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $request->requester->department ?? '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $request->requester->email ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Request Items with Full Details -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Requested Items</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200 dark:border-gray-600">
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                                Item</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-16">
                                                Qty</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                                Documents</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach($request->items as $item)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="font-medium text-gray-900 dark:text-white">
                                                        {{ $item->item_name ?? $item->product?->name ?? 'Unknown' }}
                                                    </div>
                                                    @if($item->item_specs)
                                                        <div class="text-xs text-gray-400 mt-1">{{ $item->item_specs }}</div>
                                                    @endif
                                                    @if($item->item_link)
                                                        <a href="{{ $item->item_link }}" target="_blank"
                                                            class="text-xs text-indigo-500 hover:underline">View Reference
                                                            Link</a>
                                                    @endif
                                                    @if($item->serial_number)
                                                        <div class="text-xs text-green-600 font-semibold mt-1">SN:
                                                            {{ $item->serial_number }}
                                                        </div>
                                                    @endif
                                                    @if($item->notes)
                                                        <div class="text-xs text-gray-500 mt-1 italic">Note: {{ $item->notes }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                                    {{ $item->qty }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($item->disposal_doc_path)
                                                        <div class="flex items-center justify-center gap-2">
                                                            <a href="{{ asset('storage/' . $item->disposal_doc_path) }}"
                                                                target="_blank"
                                                                class="inline-flex items-center gap-1 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-2 py-1 rounded transition"
                                                                title="View Disposal">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                                    </path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                                    </path>
                                                                </svg>
                                                                View
                                                            </a>
                                                            <a href="{{ asset('storage/' . $item->disposal_doc_path) }}"
                                                                download
                                                                class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded transition"
                                                                title="Download Disposal">
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
                            </div>
                        </div>

                        <!-- Justification -->
                        @if($request->justification)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Justification</h3>
                                <p class="text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    {{ $request->justification }}
                                </p>
                            </div>
                        @endif

                        <!-- Approval Logs -->
                        @if($request->approvalLogs && $request->approvalLogs->count() > 0)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Approval History</h3>
                                <div class="space-y-6">
                                    @foreach($request->approvalLogs as $log)
                                        <div class="flex gap-4">
                                            <!-- Timeline Icon -->
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 
                                                    @if($log->action == 'SUBMIT') bg-blue-50 border-blue-500 text-blue-600
                                                    @elseif($log->action == 'APPROVE') bg-green-50 border-green-500 text-green-600
                                                    @else bg-red-50 border-red-500 text-red-600
                                                    @endif">
                                                    @if($log->action == 'SUBMIT')
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                        </svg>
                                                    @elseif($log->action == 'APPROVE')
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Content -->
                                            <div class="flex-1 pt-1">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $log->approver->name ?? 'System' }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ $log->created_at->format('d M Y, H:i') }} • {{ ucfirst(strtolower($log->action)) }}
                                                </p>
                                                @if($log->comment)
                                                    <p class="mt-2 text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg italic border border-gray-200 dark:border-gray-600">
                                                        {{ $log->comment }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Right Sidebar (1/3) -->
                    <div class="space-y-6">
                        <!-- Request Context -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-3">Request Context</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Type:</span>
                                    <span
                                        class="font-medium text-gray-900 dark:text-white">{{ str_replace('_', ' ', $request->request_type ?? 'N/A') }}</span>
                                </div>
                                @if($request->beneficiary_name)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Beneficiary:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $request->beneficiary_name }}</span>
                                    </div>
                                @endif
                                @if($request->beneficiary_type)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Type:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $request->beneficiary_type }}</span>
                                    </div>
                                @endif
                                @if($request->po_number)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">PO Number:</span>
                                        <span class="font-medium text-indigo-600">{{ $request->po_number }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Shipping Information -->
                        @if($request->shipping_address)
                            <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                                <h4 class="font-bold text-blue-700 dark:text-blue-300 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Shipping Information
                                </h4>
                                <div class="space-y-2 text-sm">
                                    @if($request->shipping_pic_name)
                                        <div>
                                            <span class="text-gray-500">PIC:</span>
                                            <span
                                                class="font-medium text-gray-900 dark:text-white ml-1">{{ $request->shipping_pic_name }}</span>
                                        </div>
                                    @endif
                                    @if($request->shipping_pic_phone)
                                        <div>
                                            <span class="text-gray-500">Phone:</span>
                                            <span
                                                class="font-medium text-gray-900 dark:text-white ml-1">{{ $request->shipping_pic_phone }}</span>
                                        </div>
                                    @endif
                                    <div class="pt-2 border-t border-blue-200 dark:border-blue-700">
                                        <p class="text-gray-700 dark:text-gray-300 italic">{{ $request->shipping_address }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Tracking Information -->
                        @if($request->tracking_no)
                            <div class="bg-cyan-50 dark:bg-cyan-900/30 p-4 rounded-lg">
                                <h4 class="font-bold text-cyan-700 dark:text-cyan-300 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                    Delivery Tracking
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <span class="text-gray-500">Courier:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white ml-1">{{ $request->courier ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Tracking No:</span>
                                        <span class="font-medium text-cyan-600 ml-1">{{ $request->tracking_no }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Global Disposal Document (if exists at request level) -->
                        @if($request->disposal_doc_path)
                            <div class="bg-red-50 dark:bg-red-900/30 p-4 rounded-lg">
                                <h4 class="font-bold text-red-700 dark:text-red-300 mb-3">Disposal Form</h4>
                                <a href="{{ asset('storage/' . $request->disposal_doc_path) }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-sm text-red-600 hover:text-red-800 font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    View Disposal Form
                                </a>
                            </div>
                        @endif

                        <!-- BAST Document (Removed) -->

                        {{-- Asset Handover Information --}}
                        @if($request->status == 'COMPLETED' && $request->asset_photo_path && $request->e_form_confirmed_at)
                            <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded shadow mb-4">
                                <h4 class="font-bold text-blue-900 dark:text-blue-100 mb-3 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Informasi Serah Terima Asset
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- Asset Photo --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-blue-800 dark:text-blue-200 mb-2">Foto Asset Diterima:</label>
                                        <a href="{{ asset('storage/' . $request->asset_photo_path) }}" target="_blank" class="block">
                                            <img src="{{ asset('storage/' . $request->asset_photo_path) }}" 
                                                 alt="Asset Photo" 
                                                 class="w-full max-w-sm rounded-lg border-2 border-blue-200 dark:border-blue-700 hover:border-blue-500 transition cursor-pointer print:max-w-xs">
                                        </a>
                                        <p class="text-xs text-gray-500 mt-1 print:hidden">Klik gambar untuk memperbesar</p>
                                    </div>
                                    
                                    {{-- Handover Details --}}
                                    <div class="space-y-2">
                                        <div>
                                            <label class="block text-xs font-semibold text-blue-800 dark:text-blue-200">Dikonfirmasi Oleh:</label>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $request->requester->name }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-blue-800 dark:text-blue-200">Waktu Konfirmasi:</label>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($request->e_form_confirmed_at)->format('d M Y, H:i') }} WIB</p>
                                        </div>
                                        <div class="bg-white dark:bg-blue-800 p-3 rounded border border-blue-200 dark:border-blue-600 mt-3">
                                            <p class="text-xs text-blue-900 dark:text-blue-100">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <strong>E-Form Confirmed:</strong> Requester telah menyatakan bahwa asset diterima dalam kondisi baik dan sesuai spesifikasi.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Download Request PDF --}}
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('requests.pdf', $request->id) }}"
                                class="block w-full text-center py-2 px-4 border border-indigo-600 rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 print:hidden">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Request PDF
                            </a>
                        </div>

                    </div>
                </div>

                <!-- Superadmin Approval Actions -->
                @if(in_array($request->status, ['SUBMITTED', 'APPR_MGR', 'APPR_HEAD']))
                    <div
                        class="bg-indigo-50 dark:bg-indigo-900/30 rounded-lg p-6 mt-6 border border-indigo-200 dark:border-indigo-800">
                        <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-300 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                            Approval Action (Superadmin Override)
                        </h3>
                        <p class="text-sm text-indigo-700 dark:text-indigo-400 mb-4">
                            Current Status: <strong>{{ $request->status }}</strong> - As superadmin, you can approve or
                            reject this request at any stage.
                        </p>
                        <form method="POST" action="{{ route('superadmin.approvals.approve', $request) }}" class="space-y-4"
                            id="approvalForm">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Note
                                    (optional)</label>
                                <textarea name="note" rows="2" placeholder="Add a note for this action..."
                                    class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" formaction="{{ route('superadmin.approvals.approve', $request) }}"
                                    class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Approve Request
                                </button>
                                <button type="submit" formaction="{{ route('superadmin.approvals.reject', $request) }}"
                                    onclick="return confirm('Are you sure you want to reject this request?')"
                                    class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Reject Request
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Digital Handover (Available to all roles) -->
                @if($request->status == 'ON_DELIVERY')
                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-6 mt-6 border border-blue-200 dark:border-blue-800 print:hidden">
                        <h3 class="text-lg font-bold text-blue-900 dark:text-blue-300 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Konfirmasi Penerimaan Asset (Manual Override)
                        </h3>
                        <p class="mb-3 text-sm text-blue-800 dark:text-blue-200">Silakan upload foto asset yang diterima dan centang konfirmasi di bawah ini.</p>
                        
                        <form action="{{ route('requests.bast', $request->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            
                            <!-- Asset Photo Upload -->
                            <div>
                                <label class="block text-xs font-bold mb-1 text-blue-900 dark:text-blue-100">Foto Asset (Wajib)</label>
                                <input type="file" name="asset_photo" accept="image/*" capture="environment"
                                    class="block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none"
                                    required>
                                <p class="text-[10px] text-gray-500 mt-1">Format: JPG, PNG. Max: 2MB.</p>
                            </div>

                            <!-- E-Form Confirmation Checkbox -->
                            <div class="flex items-start gap-2 bg-white dark:bg-gray-800 p-2 rounded border border-blue-200 dark:border-blue-800">
                                <input type="checkbox" name="e_form_confirm" id="e_form_confirm" required
                                    class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <label for="e_form_confirm" class="text-xs leading-tight cursor-pointer text-gray-700 dark:text-gray-300">
                                    Saya menyatakan telah menerima asset tersebut dalam kondisi baik dan lengkap sesuai dengan spesifikasi yang tertera.
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
                <div class="flex gap-4 pt-6 mt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('superadmin.requests.edit', $request) }}"
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                        Edit Request
                    </a>
                    <form method="POST" action="{{ route('superadmin.requests.destroy', $request) }}"
                        onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyShareLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                const btnText = document.getElementById('shareBtnText');
                const originalText = btnText.textContent;
                btnText.textContent = 'Copied!';
                setTimeout(() => {
                    btnText.textContent = originalText;
                }, 2000);
            }).catch(err => {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = url;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                const btnText = document.getElementById('shareBtnText');
                btnText.textContent = 'Copied!';
                setTimeout(() => {
                    btnText.textContent = 'Share Link';
                }, 2000);
            });
        }
    </script>
</x-sidebar-layout>