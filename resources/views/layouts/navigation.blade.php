<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        @php
                            $sidebarLogoLight = \App\Models\AppSetting::getValue('sidebar_logo_light');
                            $sidebarLogoDark = \App\Models\AppSetting::getValue('sidebar_logo_dark');
                        @endphp
                        {{-- Light mode --}}
                        @if($sidebarLogoLight)
                            <img src="{{ asset('storage/' . $sidebarLogoLight) }}" alt="Logo"
                                class="block h-9 w-auto object-contain dark:hidden">
                        @else
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:hidden" />
                        @endif
                        {{-- Dark mode --}}
                        @if($sidebarLogoDark)
                            <img src="{{ asset('storage/' . $sidebarLogoDark) }}" alt="Logo"
                                class="hidden h-9 w-auto object-contain dark:block">
                        @else
                            <x-application-logo class="hidden h-9 w-auto fill-current text-gray-200 dark:block" />
                        @endif
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('superadmin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(Auth::user()->role === 'requester')
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                            {{ __('Catalog') }}
                        </x-nav-link>
                        <x-nav-link :href="route('requests.index')" :active="request()->routeIs('requests.*')">
                            {{ __('My Requests') }}
                        </x-nav-link>
                        @if(Auth::user()->canAccessAssetResign())
                            <x-nav-link :href="route('asset-resign.index')" :active="request()->routeIs('asset-resign*')">
                                {{ __('Asset Resign') }}
                            </x-nav-link>
                        @endif
                    @endif

                    @php
                        $navIsMappedApprover = \App\Models\ApprovalRoleLevel::where('user_id', Auth::id())
                            ->where('is_active', true)
                            ->exists();
                    @endphp
                    @if($navIsMappedApprover)
                        <x-nav-link :href="route('requests.approvals')" :active="request()->routeIs('requests.approvals')">
                            {{ __('Approvals') }}
                        </x-nav-link>
                        <x-nav-link :href="route('requests.my-requests')"
                            :active="request()->routeIs('requests.my-requests')">
                            {{ __('My Requests') }}
                        </x-nav-link>
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                            {{ __('Catalog') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Admin Panel') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                            {{ __('Manage Catalog') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->role === 'superadmin')
                        <x-nav-link :href="route('superadmin.users')" :active="request()->routeIs('superadmin.users*')">
                            {{ __('Users') }}
                        </x-nav-link>
                        <x-nav-link :href="route('superadmin.resigned-assets')"
                            :active="request()->routeIs('superadmin.resigned-assets*')">
                            {{ __('Asset Resign') }}
                        </x-nav-link>
                        <x-nav-link :href="route('superadmin.settings')"
                            :active="request()->routeIs('superadmin.settings*')">
                            {{ __('Settings') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <!-- Notification Bell -->
                <div class="relative" x-data="{ 
                    open: false,
                    markRead(id) {
                        fetch(`{{ url('/notifications') }}/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        });
                    }
                }">
                    <button @click="open = ! open"
                        class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 relative">
                        <span class="sr-only">View notifications</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white"
                                style="background: #22c55e;"></span>
                        @endif
                    </button>

                    <!-- Notifications Dropdown -->
                    <div x-show="open" @click.away="open = false"
                        class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-[100] py-1"
                        style="display: none;">
                        <div
                            class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase">Notifications</h3>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.read-all') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">Mark all
                                        read</button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-60 overflow-y-auto">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <a href="{{ $notification->data['link'] ?? '#' }}"
                                    @click="markRead('{{ $notification->id }}')"
                                    class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            @if($notification->data['action'] == 'approved')
                                                <div class="h-6 w-6 rounded-full bg-green-100 flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            @elseif($notification->data['action'] == 'rejected')
                                                <div class="h-6 w-6 rounded-full bg-red-100 flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3 w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white capitalize">
                                                {{ str_replace('_', ' ', $notification->data['action'] ?? 'Notification') }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ Str::limit($notification->data['message'] ?? '', 50) }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-gray-500 text-sm">
                                    No new notifications
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Dark Mode Toggle -->
                <button @click="$dispatch('toggle-dark-mode')"
                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    <!-- Sun Icon (shown in dark mode) -->
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <!-- Moon Icon (shown in light mode) -->
                    <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                        </path>
                    </svg>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>