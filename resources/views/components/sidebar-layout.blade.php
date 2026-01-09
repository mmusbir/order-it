<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarExpanded: true }"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $appTitle = \App\Models\AppSetting::getValue('app_title', 'Order IT');
        $favicon = \App\Models\AppSetting::getValue('favicon');
    @endphp
    <title>{{ $appTitle }}</title>
    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-200"
    x-init="$watch('darkMode', val => { localStorage.setItem('darkMode', val); document.documentElement.classList.toggle('dark', val) })"
    @toggle-dark-mode.window="darkMode = !darkMode">

    <div class="min-h-screen">
        <!-- Sidebar -->
        <aside
            class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col fixed left-0 top-0 h-full z-30 transition-all duration-300 print:hidden"
            :class="sidebarExpanded ? 'w-64' : 'w-20'">

            <!-- Logo -->
            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                @php
                    $logoRole = Auth::user()->role ?? 'requester';
                    $logoRoute = $logoRole === 'superadmin' ? route('superadmin.dashboard') : ($logoRole === 'admin' ? route('admin.dashboard') : route('dashboard'));
                @endphp
                <a href="{{ $logoRoute }}" class="flex items-center gap-3">
                    @php
                        $sidebarLogoLight = \App\Models\AppSetting::getValue('sidebar_logo_light');
                        $sidebarLogoDark = \App\Models\AppSetting::getValue('sidebar_logo_dark');
                        $hasLightLogo = !empty($sidebarLogoLight);
                        $hasDarkLogo = !empty($sidebarLogoDark);
                    @endphp
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0">
                        {{-- Light mode logo --}}
                        @if($hasLightLogo)
                            <img x-show="!darkMode" src="{{ asset('storage/' . $sidebarLogoLight) }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl">
                        @else
                            <div x-show="!darkMode" class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                </svg>
                            </div>
                        @endif
                        {{-- Dark mode logo --}}
                        @if($hasDarkLogo)
                            <img x-show="darkMode" src="{{ asset('storage/' . $sidebarLogoDark) }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl">
                        @else
                            <div x-show="darkMode" class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div x-show="sidebarExpanded" x-transition class="overflow-hidden">
                        <span class="font-bold text-gray-900 dark:text-white text-lg whitespace-nowrap">{{ \App\Models\AppSetting::getValue('app_title', 'Order IT') }}</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap">
                            @if(Auth::user()->role === 'superadmin') Superadmin Console
                            @elseif(Auth::user()->role === 'admin') Admin Panel
                            @elseif(Auth::user()->role === 'approver' || \App\Models\ApprovalRoleLevel::where('user_id', Auth::id())->where('is_active', true)->exists()) Approver Panel
                            @else Requester Panel @endif
                        </p>
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                @php 
                    $role = Auth::user()->role;
                    // Check if user is mapped as approver at any level OR has explicit approver role
                    $isMappedApprover = Auth::user()->role === 'approver' || \App\Models\ApprovalRoleLevel::where('user_id', Auth::id())
                        ->where('is_active', true)
                        ->exists();
                    
                    // Count pending approvals for notification badge
                    $pendingApprovalsCount = 0;
                    if ($isMappedApprover) {
                        $userId = Auth::id();
                        $levelToStatus = [
                            1 => 'SUBMITTED',
                            2 => 'APPR_1', 
                            3 => 'APPR_2',
                            4 => 'APPR_3',
                        ];
                        $pendingApprovalsCount = \App\Models\Request::whereHas('approvers', function ($q) use ($userId) {
                            $q->where('user_id', $userId)->where('status', 'pending');
                        })->where(function ($q) use ($userId, $levelToStatus) {
                            foreach ($levelToStatus as $level => $requiredStatus) {
                                $q->orWhere(function ($subQ) use ($userId, $level, $requiredStatus) {
                                    $subQ->whereHas('approvers', function ($approverQ) use ($userId, $level) {
                                        $approverQ->where('user_id', $userId)
                                            ->where('level', $level)
                                            ->where('status', 'pending');
                                    })->where('status', $requiredStatus);
                                });
                            }
                        })->count();
                    }
                @endphp

                <!-- Dashboard -->
                <a href="{{ $role === 'superadmin' ? route('superadmin.dashboard') : ($role === 'admin' ? route('admin.dashboard') : route('dashboard')) }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                        {{ request()->routeIs('superadmin.dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    :class="{ 'justify-center': !sidebarExpanded }">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Dashboard</span>
                </a>

                {{-- Approval Inbox (Priority for Approvers) --}}
                @if($isMappedApprover)
                    <div class="px-4 mb-2 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:block" :class="{ 'hidden': !sidebarExpanded }">
                        Approvals
                    </div>
                    <a href="{{ route('requests.approvals') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition relative
                            {{ request()->routeIs('requests.approvals') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <div class="relative">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @if($pendingApprovalsCount > 0)
                                <span class="absolute -top-2 -right-2 text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
                                    style="background: #22c55e; color: white;"
                                    x-show="!sidebarExpanded">{{ $pendingApprovalsCount > 9 ? '9+' : $pendingApprovalsCount }}</span>
                            @endif
                        </div>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap flex items-center gap-2">
                            Need Approval
                            @if($pendingApprovalsCount > 0)
                                <span class="text-xs font-bold rounded-full px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">{{ $pendingApprovalsCount > 99 ? '99+' : $pendingApprovalsCount }}</span>
                            @endif
                        </span>
                    </a>
                @endif

                {{-- Request Menu (For Everyone except Superadmin/Admin who have their own panels usually, but we can allow them to see My Requests) --}}
                @if(!in_array($role, ['superadmin', 'admin']))
                    <div class="px-4 mb-2 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:block" :class="{ 'hidden': !sidebarExpanded }">
                        Requests
                    </div>
                    
                    {{-- Catalog --}}
                    <a href="{{ route('products.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('products.*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Catalog</span>
                    </a>
                @endif

                {{-- My Requests --}}
                @php
                    // Define route based on role to maintain legacy logic
                    $myRequestsRoute = ($role === 'approver' || $role === 'admin' || $role === 'superadmin' || $isMappedApprover) 
                        ? route('requests.my-requests') 
                        : route('requests.index');
                    $isMyRequestsActive = request()->routeIs('requests.my-requests') || request()->routeIs('requests.index');
                @endphp
                
                {{-- Only show My Requests if not Superadmin/Admin OR if they explicitly want to see their requests (which they usually do, but let's stick to standard user flow) --}}
                {{-- Actually, admin/superadmin see My Requests in the original code, so let's keep it available for them too but maybe grouped differently? --}}
                {{-- Let's put My Requests here for EVERYONE --}}
                
                <a href="{{ $myRequestsRoute }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                        {{ $isMyRequestsActive ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    :class="{ 'justify-center': !sidebarExpanded }">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">My Requests</span>
                </a>

                {{-- Asset Resign (Specific Access) --}}
                @if(Auth::user()->canAccessAssetResign() && $role !== 'superadmin')
                    <a href="{{ route('asset-resign.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('asset-resign*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Asset Resign</span>
                    </a>
                @endif


                {{-- Admin / Superadmin Sections --}}
                @if($role === 'superadmin')
                    <p x-show="sidebarExpanded"
                        class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Request Management</p>

                    <a href="{{ route('superadmin.approvals') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('superadmin.approvals*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap flex items-center gap-2">
                            Approval Inbox
                            @php $pendingCount = \App\Models\Request::whereIn('status', ['SUBMITTED', 'APPR_1', 'APPR_2', 'APPR_3'])->count(); @endphp
                            @if($pendingCount > 0)
                                <span class="text-xs bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 px-2 py-0.5 rounded-full font-bold">{{ $pendingCount > 99 ? '99+' : $pendingCount }}</span>
                            @endif
                        </span>
                    </a>

                    <a href="{{ route('superadmin.requests') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('superadmin.requests*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">All Requests</span>
                    </a>

                    <p x-show="sidebarExpanded"
                        class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Inventory & Assets</p>

                    <a href="{{ route('superadmin.products.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('superadmin.products*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Catalog</span>
                    </a>

                    <a href="{{ route('superadmin.consumables') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('superadmin.consumables*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Consumables</span>
                    </a>

                    <a href="{{ route('superadmin.resigned-assets') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('superadmin.resigned-assets*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Asset Resign</span>
                    </a>

                    <p x-show="sidebarExpanded"
                        class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration
                    </p>

                    <a href="{{ route('superadmin.users') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('superadmin.users*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">User Management</span>
                    </a>

                    <a href="{{ route('superadmin.settings') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('superadmin.settings*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Settings</span>
                    </a>

                    <a href="{{ route('superadmin.audit-logs') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('superadmin.audit-logs*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Audit Log</span>
                    </a>

                    <a href="{{ route('superadmin.sla-report') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('superadmin.sla-report*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">SLA Report</span>
                    </a>
                @endif

                @if($role === 'admin')
                    <div class="px-4 mb-2 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:block" :class="{ 'hidden': !sidebarExpanded }">
                        Request Management
                    </div>

                    <a href="{{ route('admin.requests.fulfillment') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.requests.fulfillment') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Order Processing</span>
                    </a>

                    <a href="{{ route('admin.requests.monitor') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.requests.monitor') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Monitor Requests</span>
                    </a>

                    <div class="px-4 mb-2 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:block" :class="{ 'hidden': !sidebarExpanded }">
                        Inventory
                    </div>

                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('admin.products*') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Manage Catalog</span>
                    </a>

                    <a href="{{ route('admin.consumables') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('admin.consumables*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Consumables</span>
                    </a>

                    <a href="{{ route('superadmin.resigned-assets') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('superadmin.resigned-assets*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">Asset Resign</span>
                    </a>

                    <div class="px-4 mb-2 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:block" :class="{ 'hidden': !sidebarExpanded }">
                        Administration
                    </div>

                    <a href="{{ route('admin.users') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg font-medium transition
                            {{ request()->routeIs('admin.users*') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        :class="{ 'justify-center': !sidebarExpanded }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span x-show="sidebarExpanded" x-transition class="whitespace-nowrap">User Management</span>
                    </a>

                @endif
            </nav>

            <!-- User Info -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3" :class="{ 'justify-center': !sidebarExpanded }">
                    <a href="{{ route('profile.edit') }}"
                        class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center flex-shrink-0 hover:ring-2 hover:ring-indigo-500 transition overflow-hidden">
                        @if(Auth::user()->profile_photo)
                            <img src="{{ asset('storage/profile-photos/' . Auth::user()->profile_photo) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            <span class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                        @endif
                    </a>
                    <a x-show="sidebarExpanded" x-transition href="{{ route('profile.edit') }}" class="flex-1 min-w-0 overflow-hidden hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg px-2 py-1 -mx-2 transition">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    </a>
                    <a x-show="sidebarExpanded" href="{{ route('logout.get') }}" class="text-gray-400 hover:text-red-500 transition" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="min-h-screen transition-all duration-300 print:ml-0"
            :style="sidebarExpanded ? 'margin-left: 256px' : 'margin-left: 80px'">
            <!-- Top Bar -->
            <header
                class="bg-transparent px-6 py-4 flex items-center justify-between sticky top-0 z-20 print:hidden">
                <div class="flex items-center gap-4">
                    <button @click="sidebarExpanded = !sidebarExpanded"
                        class="ml-4 p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                    </button>
                    <nav class="text-sm text-gray-500 dark:text-gray-400">
                        <span>Home</span>
                        <span class="mx-2">›</span>
                        <span class="text-gray-900 dark:text-white font-medium">
                            @if(request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('superadmin.dashboard'))
                                Dashboard Overview
                            @elseif(request()->routeIs('requests.checkout'))
                                New Request
                            @elseif(request()->routeIs('requests.show'))
                                Request Details
                            @elseif(request()->routeIs('products.*') || request()->routeIs('admin.products.*') || request()->routeIs('superadmin.products.*'))
                                CMS Catalog
                            @elseif(request()->routeIs('requests.index') || request()->routeIs('requests.my-requests'))
                                My Requests
                            @elseif(request()->routeIs('requests.approvals') || request()->routeIs('superadmin.approvals'))
                                Approval Inbox
                            @elseif(request()->routeIs('superadmin.requests*') || request()->routeIs('admin.requests.monitor'))
                                All Requests
                            @elseif(request()->routeIs('admin.requests.fulfillment'))
                                Order Processing
                            @elseif(request()->routeIs('superadmin.users') || request()->routeIs('admin.users'))
                                User Management
                            @elseif(request()->routeIs('superadmin.consumables') || request()->routeIs('admin.consumables'))
                                Consumables
                            @elseif(request()->routeIs('asset-resign.*') || request()->routeIs('superadmin.resigned-assets'))
                                Asset Resign
                            
                            {{-- Settings & Master Data --}}
                            @elseif(request()->routeIs('superadmin.settings.master-data'))
                                Settings › Master Data
                            @elseif(request()->routeIs('superadmin.settings.roles*'))
                                Settings › Master Data › Roles
                            @elseif(request()->routeIs('superadmin.settings.departments*'))
                                Settings › Master Data › Departments
                            @elseif(request()->routeIs('superadmin.settings.approval-roles*'))
                                Settings › Master Data › Approval Roles
                            @elseif(request()->routeIs('superadmin.settings.job-titles*'))
                                Settings › Master Data › Job Titles
                            @elseif(request()->routeIs('superadmin.settings.categories*'))
                                Settings › Master Data › Categories
                            @elseif(request()->routeIs('superadmin.settings.asset-models*'))
                                Settings › Master Data › Asset Models
                            @elseif(request()->routeIs('superadmin.settings.request-types*'))
                                Settings › Master Data › Request Types
                            @elseif(request()->routeIs('superadmin.settings.replacement-reasons*'))
                                Settings › Master Data › Replacement Reasons
                            @elseif(request()->routeIs('superadmin.settings.branches*'))
                                Settings › Master Data › Branches
                            @elseif(request()->routeIs('superadmin.settings.general*'))
                                Settings › General
                            @elseif(request()->routeIs('superadmin.settings.integration*') || request()->routeIs('superadmin.settings.ldap*') || request()->routeIs('superadmin.settings.snipeit*'))
                                Settings › Integrations
                            @elseif(request()->routeIs('superadmin.settings*'))
                                Settings
                                
                            @elseif(request()->routeIs('superadmin.audit-logs'))
                                Audit Logs
                            @elseif(request()->routeIs('profile.edit'))
                                Profile
                            @else
                                Dashboard
                            @endif
                        </span>
                    </nav>
                </div>

                <div class="flex items-center gap-4">
                    <!-- New Request Button (Static) -->
                    @if(!in_array(Auth::user()->role, ['superadmin', 'admin', 'user']))
                    <a href="{{ route('requests.checkout') }}"
                        class="hidden md:flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Request
                    </a>
                    @endif

                    {{-- Search - Disabled for now
                    <div class="relative hidden md:block">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" placeholder="Search requests, assets, or users..."
                            class="pl-10 pr-4 py-2 w-72 bg-gray-100 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    --}}

                    <!-- Dark Mode Toggle -->
                    <button @click="$dispatch('toggle-dark-mode')"
                        class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                            </path>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </button>

                    <!-- Notifications -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            @php
                                $unreadCount = Auth::user()->unreadNotifications->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span
                                    class="absolute top-1 right-1 w-4 h-4 text-[10px] font-bold rounded-full flex items-center justify-center"
                                    style="background: #22c55e; color: white;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="open" @click.away="open = false" x-cloak
                            class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                                @if($unreadCount > 0)
                                    <form action="{{ route('notifications.read-all') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">Mark all read</button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                @forelse(Auth::user()->unreadNotifications as $notification)
                                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <div class="flex items-start gap-3">
                                            @if($notification->type === 'App\Notifications\RequestActivityNotification')
                                                <div class="bg-blue-100 dark:bg-blue-900/30 p-1.5 rounded-full shrink-0">
                                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                            @elseif($notification->type === 'App\Notifications\ResignedAssetsDetectedNotification')
                                                <div class="bg-yellow-100 dark:bg-yellow-900/30 p-1.5 rounded-full shrink-0">
                                                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            
                                            <div class="flex-1">
                                                <p class="text-sm text-gray-800 dark:text-gray-200">
                                                    {{ $notification->data['message'] ?? 'New notification' }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                                
                                                <div class="mt-2 flex gap-2">
                                                    @if(isset($notification->data['link']))
                                                        <a href="{{ $notification->data['link'] }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">View</a>
                                                    @endif
                                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400">Mark read</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                        No unread notifications
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer
                class="px-6 py-4 bg-transparent text-sm text-gray-500 dark:text-gray-400 flex flex-col items-center justify-center gap-2 print:hidden">
                <span>© {{ date('Y') }} Order IT System. All rights reserved.</span>

            </footer>
        </div>
    </div>
</body>

</html>