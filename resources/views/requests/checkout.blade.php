<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('dashboard') }}" class="hover:text-gray-700">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('requests.index') }}" class="hover:text-gray-700">Requests</a>
            <span class="mx-2">/</span>
            <span class="text-indigo-600 font-semibold">New</span>
        </div>
    </x-slot>

    <div class="py-8" x-data="requestForm()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <!-- Page Title -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Form New Request</h1>
                <p class="text-gray-500 mt-1">Fill in the details for your new asset procurement request.</p>
            </div>

            @if(!$hasApprovalRole || $approverCount === 0)
                <!-- Approval Role Not Set Warning -->
                <div class="mb-6 p-6 bg-amber-50 dark:bg-amber-900/30 border-l-4 border-amber-500 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-amber-800 dark:text-amber-200">
                                Approval Role Belum Di-Set
                            </h3>
                            <p class="mt-2 text-amber-700 dark:text-amber-300">
                                @if(!$hasApprovalRole)
                                    Anda belum memiliki <strong>Approval Role</strong> yang ter-assign ke akun Anda.
                                @else
                                    <strong>Approval Role</strong> Anda belum memiliki approver yang ter-assign.
                                @endif
                                <br>Silakan hubungi <strong>IT Administrator</strong> untuk mengatur approval role Anda
                                sebelum dapat mengajukan request.
                            </p>
                            <div class="mt-4">
                                <a href="mailto:it-support@company.com"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Hubungi IT Administrator
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('requests.store') }}" method="POST" enctype="multipart/form-data"
                @if(!$hasApprovalRole || $approverCount === 0)
                    onsubmit="alert('Anda belum memiliki Approval Role yang ter-assign. Silakan hubungi IT Administrator.'); return false;"
                @endif>
                @csrf

                <!-- Session Status -->
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Error:</h3>
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Section 1: Request Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Request Information</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Requester</label>
                            <input type="text" name="requester_name" value="{{ Auth::user()->name }}"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Request Date</label>
                            <input type="date" name="request_date" value="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Priority
                                Level</label>
                            <select name="priority"
                                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>

                    <!-- Request Context -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Request Type</label>
                            <select name="request_type" x-model="requestType"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-sm" required>
                                @foreach($requestTypes as $type)
                                    <option value="{{ $type->slug }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="requestType === 'REPLACEMENT'" style="display: none;">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Replacement
                                Reason</label>
                            <select name="replacement_reason"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-sm">
                                @foreach($replacementReasons as $reason)
                                    <option value="{{ $reason->slug }}">{{ $reason->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Asset Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Asset Details</h2>
                        </div>
                        <span class="text-sm text-gray-400" x-text="items.length + ' Items added'"></span>
                    </div>

                    <!-- Item Rows -->
                    <template x-for="(item, index) in items" :key="index">
                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <!-- Search Input -->
                            <div class="relative mb-3" x-data="{ open: false, search: '' }">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </span>
                                    <input type="text" x-model="search" @focus="open = true" @click="open = true"
                                        :placeholder="item.product_id ? item.sku + ' - ' + item.name : 'Search by SKU or Product Name...'"
                                        class="w-full pl-10 pr-10 py-3 border border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <button type="button" x-show="item.product_id"
                                        @click="clearProduct(index); search = ''"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Dropdown Results (Filtered by Request Type) -->
                                <div x-show="open && search.length > 0" @click.away="open = false" x-transition
                                    class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl max-h-80 overflow-y-auto"
                                    style="background: #fff;">
                                    <template x-for="product in getFilteredProducts(search)" :key="product.id">
                                        <div @click="selectProductData(index, product.id, product.sku, product.name, product.specs, product.image_url, product.category, product.model_name); open = false; search = ''"
                                            class="p-3 hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0">
                                            <div class="flex items-center gap-3">
                                                <template x-if="product.image_url">
                                                    <img :src="product.image_url"
                                                        class="w-12 h-12 object-cover rounded-lg flex-shrink-0">
                                                </template>
                                                <template x-if="!product.image_url">
                                                    <div
                                                        class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                </template>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="text-xs font-mono bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded"
                                                            x-text="product.sku"></span>
                                                        <span
                                                            class="font-semibold text-gray-900 dark:text-white truncate"
                                                            x-text="product.name"></span>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <span class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded"
                                                            x-text="product.category || '-'"></span>
                                                        <span class="ml-1" x-text="product.model_name || ''"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <div x-show="getFilteredProducts(search).length === 0"
                                        class="p-4 text-center text-gray-500">
                                        <span
                                            x-text="search.length > 0 ? 'No products found for this request type' : 'Type to search...'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Product Card -->
                            <div x-show="item.product_id"
                                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                <div class="flex items-start gap-4">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        <template x-if="item.image">
                                            <img :src="item.image" class="w-20 h-20 object-cover rounded-lg">
                                        </template>
                                        <template x-if="!item.image">
                                            <div
                                                class="w-20 h-20 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                    <!-- Product Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span
                                                class="text-xs font-mono bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded"
                                                x-text="item.sku"></span>
                                        </div>
                                        <h4 class="font-bold text-gray-900 dark:text-white" x-text="item.name"></h4>
                                        <div class="flex flex-wrap gap-2 mt-2 text-xs">
                                            <span
                                                class="bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 px-2 py-1 rounded"
                                                x-text="item.category || '-'"></span>
                                            <span
                                                class="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-1 rounded"
                                                x-text="item.model || '-'"></span>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 line-clamp-2"
                                            x-text="item.specs"></p>

                                        <!-- Disposal Doc Upload (Per Item) -->
                                        <div x-show="requestType === 'REPLACEMENT'" class="mt-3">
                                            <label
                                                class="block text-xs font-semibold text-red-500 uppercase mb-1">Upload
                                                Disposal (Required)</label>
                                            <input type="file" :name="'items['+index+'][disposal_doc]'" accept=".pdf"
                                                class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100"
                                                :required="requestType === 'REPLACEMENT'">
                                        </div>
                                    </div>

                                    <!-- Quantity Input (shown if allow_quantity is enabled) -->
                                    <div x-show="allowQuantity" class="flex-shrink-0 w-20">
                                        <label
                                            class="block text-xs font-semibold text-gray-500 uppercase mb-1">Qty</label>
                                        <input type="number" :name="'items['+index+'][qty]'" x-model="item.qty" min="1"
                                            max="100"
                                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-center text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <!-- Hidden Qty (when quantity not allowed, fixed to 1) -->
                                    <input x-show="!allowQuantity" type="hidden" :name="'items['+index+'][qty]'"
                                        value="1">
                                    <!-- Delete Button -->
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                        class="text-red-400 hover:text-red-600 transition p-2 flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                                <!-- Hidden Fields -->
                                <input type="hidden" :name="'items['+index+'][product_id]'" x-model="item.product_id">
                                <input type="hidden" :name="'items['+index+'][name]'" x-model="item.name">
                                <input type="hidden" :name="'items['+index+'][specs]'" x-model="item.specs">
                            </div>

                            <!-- Empty State -->
                            <div x-show="!item.product_id" class="text-center py-4 text-gray-400 text-sm">
                                Search and select a product above
                            </div>
                        </div>
                    </template>

                    <!-- Add Item Button -->
                    <button type="button" @click="addItem()"
                        class="flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium text-sm mt-4 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Another Item
                    </button>
                </div>

                <!-- Section 3: Beneficiary & Shipping -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Shipping</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Asset For</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="beneficiary_type" value="BRANCH" x-model="beneficiaryType"
                                        class="text-indigo-600">
                                    <span class="text-sm">Branch / Office</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="beneficiary_type" value="USER" x-model="beneficiaryType"
                                        class="text-indigo-600">
                                    <span class="text-sm">Personal User</span>
                                </label>
                            </div>
                        </div>
                        <div x-show="beneficiaryType === 'BRANCH'" x-data="{ branchOpen: false, branchSearch: '' }">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Branch</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </span>
                                <input type="text" x-model="branchSearch" @focus="branchOpen = true"
                                    @click="branchOpen = true"
                                    :placeholder="selectedBranchId ? selectedBranchId + ' - ' + (branches.find(b => b.branch_code === selectedBranchId)?.name || '') : 'Search by code or branch name...'"
                                    class="w-full pl-10 pr-10 py-3 border border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                    :disabled="beneficiaryType !== 'BRANCH'">
                                <button type="button" x-show="selectedBranchId"
                                    @click="selectedBranchId = ''; branchSearch = ''; shippingPicName = ''; shippingPicPhone = ''; shippingAddress = ''"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <input type="hidden" name="beneficiary_id" x-model="selectedBranchId">
                            </div>
                            <!-- Branch Dropdown Results -->
                            <div x-show="branchOpen && branchSearch.length > 0" @click.away="branchOpen = false"
                                x-transition
                                class="absolute z-50 w-full max-w-md mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                                @foreach($branches as $branch)
                                    <div x-show="'{{ strtolower($branch->branch_code . ' ' . $branch->name) }}'.includes(branchSearch.toLowerCase())"
                                        @click="selectedBranchId = '{{ $branch->branch_code }}'; fillBranchData(); branchOpen = false; branchSearch = ''"
                                        class="p-3 hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span
                                                    class="font-semibold text-gray-900 dark:text-white">{{ $branch->branch_code }}</span>
                                                <span class="text-gray-500 dark:text-gray-400"> - {{ $branch->name }}</span>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">{{ Str::limit($branch->address, 50) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div x-show="beneficiaryType === 'USER'" class="md:col-span-2 grid grid-cols-2 gap-4"
                            style="display: none;">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Employee
                                    NIK</label>
                                <input type="text" name="beneficiary_id" placeholder="123456"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm"
                                    :disabled="beneficiaryType !== 'USER'">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Employee
                                    Name</label>
                                <input type="text" name="beneficiary_name" placeholder="John Doe"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm"
                                    :disabled="beneficiaryType !== 'USER'">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 pt-6 border-t">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">PIC Name</label>
                            <input type="text" name="shipping_pic_name" x-model="shippingPicName"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">PIC Phone</label>
                            <input type="text" name="shipping_pic_phone" x-model="shippingPicPhone"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm" required>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Shipping
                                Address</label>
                            <textarea name="shipping_address" rows="2" x-model="shippingAddress"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Total & Disclaimer -->
                <div
                    class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-6 mb-6 border border-indigo-100 dark:border-indigo-800">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-indigo-100 dark:bg-indigo-800 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Items Requested
                                </p>
                                <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-300"
                                    x-text="totalQty + ' Items'"></p>
                            </div>
                        </div>
                        <div class="text-center md:text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic max-w-md">
                                "Price will be determined by Procurement team after approval process is complete."
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('requests.index') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition">Cancel</a>
                    <button type="submit"
                        class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-lg">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('requestForm', () => ({
                requestType: '{{ $requestTypes->first()?->slug ?? "NEW_HIRE" }}',
                beneficiaryType: 'BRANCH',
                selectedBranchId: '',
                branches: @json($branches),

                // Products with request type data for filtering (pre-formatted in controller)
                allProducts: @json($productsJson),

                // Request types with slugs mapping to IDs
                requestTypesData: @json($requestTypesMap),

                // Request types config with allow_quantity
                requestTypesConfig: @json($requestTypesConfig),

                // Check if current request type allows quantity input
                get allowQuantity() {
                    const config = this.requestTypesConfig[this.requestType];
                    return config ? config.allow_quantity : false;
                },

                // Bind shipping fields to Alpine models to allow auto-fill
                shippingPicName: '',
                shippingPicPhone: '',
                shippingAddress: '',

                // Get filtered products based on selected request type and search
                getFilteredProducts(search) {
                    if (!search) return [];

                    const searchLower = search.toLowerCase();
                    const selectedTypeId = this.requestTypesData[this.requestType];

                    return this.allProducts.filter(product => {
                        // Check if product matches search
                        const searchText = (product.sku + ' ' + product.name + ' ' + (product.category || '') + ' ' + (product.model_name || '')).toLowerCase();
                        if (!searchText.includes(searchLower)) return false;

                        // Check if product is available for selected request type
                        // If product has no request_type_ids (empty array), it's available for all
                        if (product.request_type_ids.length === 0) return true;

                        // Otherwise, check if selected request type is in the list
                        return product.request_type_ids.includes(selectedTypeId);
                    });
                },

                init() {
                    this.$watch('requestType', (value) => {
                        if (value === 'REPLACEMENT') {
                            this.items.forEach(item => item.qty = 1);
                        }
                        // Clear selected products when request type changes
                        this.items = [{ product_id: '', sku: '', name: '', specs: '', image: '', category: '', model: '', qty: 1 }];
                    });
                },

                fillBranchData() {
                    if (!this.selectedBranchId) return;

                    const branch = this.branches.find(b => b.branch_code === this.selectedBranchId);
                    if (branch) {
                        this.shippingPicName = branch.pic_name || '';
                        this.shippingPicPhone = branch.phone || '';
                        this.shippingAddress = branch.address || '';
                    }
                },

                items: [
                    { product_id: '', sku: '', name: '', specs: '', image: '', category: '', model: '', qty: 1 }
                ],

                init() {
                    // Load cart items from localStorage
                    const cartData = localStorage.getItem('requestCart');
                    if (cartData) {
                        try {
                            const cartItems = JSON.parse(cartData);
                            if (cartItems && cartItems.length > 0) {
                                // Find matching products from database
                                const products = @json($products);
                                let expandedItems = [];

                                cartItems.forEach(cartItem => {
                                    const product = products.find(p => p.id === cartItem.id);
                                    const qty = parseInt(cartItem.qty) || 1;

                                    // Create separate item for each quantity
                                    for (let i = 0; i < qty; i++) {
                                        if (product) {
                                            expandedItems.push({
                                                product_id: product.id,
                                                sku: product.sku || '',
                                                name: product.name || cartItem.name,
                                                specs: product.specs || cartItem.specs || '',
                                                image: product.image ? '/storage/' + product.image : '',
                                                category: product.category?.name || '',
                                                model: product.model || '',
                                                qty: 1
                                            });
                                        } else {
                                            // If product not found in database, use cart data
                                            expandedItems.push({
                                                product_id: cartItem.id,
                                                sku: '',
                                                name: cartItem.name || '',
                                                specs: cartItem.specs || '',
                                                image: cartItem.image || '',
                                                category: '',
                                                model: '',
                                                qty: 1
                                            });
                                        }
                                    }
                                });

                                if (expandedItems.length > 0) {
                                    this.items = expandedItems;
                                }
                                // Clear cart after loading
                                localStorage.removeItem('requestCart');
                            }
                        } catch (e) {
                            console.error('Failed to parse cart data:', e);
                        }
                    }
                },

                addItem() {
                    this.items.push({ product_id: '', sku: '', name: '', specs: '', image: '', category: '', model: '', qty: 1 });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                selectProductData(index, productId, sku, name, specs, image, category, model) {
                    this.items[index].product_id = productId;
                    this.items[index].sku = sku;
                    this.items[index].name = name;
                    this.items[index].specs = specs;
                    this.items[index].image = image;
                    this.items[index].category = category;
                    this.items[index].model = model;
                },

                clearProduct(index) {
                    this.items[index].product_id = '';
                    this.items[index].sku = '';
                    this.items[index].name = '';
                    this.items[index].specs = '';
                    this.items[index].image = '';
                    this.items[index].category = '';
                    this.items[index].model = '';
                },

                get totalQty() {
                    return this.items.reduce((sum, item) => {
                        return item.product_id ? sum + (parseInt(item.qty) || 0) : sum;
                    }, 0);
                }
            }));
        });
    </script>
</x-sidebar-layout>