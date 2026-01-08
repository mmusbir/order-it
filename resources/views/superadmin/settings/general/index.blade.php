<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('superadmin.settings') }}" class="hover:text-gray-700">Settings</a>
            <span class="mx-2">/</span>
            <span class="text-indigo-600 font-semibold">General</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">General Settings</h1>
                <p class="text-gray-500 mt-1">Kelola logo dan branding aplikasi.</p>
            </div>

            <!-- Session Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('superadmin.settings.general.update') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Logo Settings - Sidebar -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Logo Sidebar</h3>
                    <p class="text-sm text-gray-500 mb-6">Logo yang ditampilkan di sidebar navigasi. Ukuran: 40x40 px,
                        Format: PNG</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Light Mode Logo -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <label class="text-sm font-semibold text-gray-700">Mode Siang (Light)</label>
                            </div>

                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                                    @if($sidebarLogoLight)
                                        <img src="{{ asset('storage/' . $sidebarLogoLight) }}" alt="Sidebar Logo Light"
                                            class="w-10 h-10 object-contain">
                                    @else
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <span class="text-xs {{ $sidebarLogoLight ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $sidebarLogoLight ? 'Aktif' : 'Default' }}
                                </span>
                            </div>

                            <input type="file" name="sidebar_logo_light" accept="image/png"
                                class="block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                            @error('sidebar_logo_light') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dark Mode Logo -->
                        <div class="border border-gray-600 rounded-lg p-4 bg-gray-800">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                                <label class="text-sm font-semibold text-gray-200">Mode Malam (Dark)</label>
                            </div>

                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="w-16 h-16 bg-gray-700 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-500">
                                    @if($sidebarLogoDark)
                                        <img src="{{ asset('storage/' . $sidebarLogoDark) }}" alt="Sidebar Logo Dark"
                                            class="w-10 h-10 object-contain">
                                    @else
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <span class="text-xs {{ $sidebarLogoDark ? 'text-green-400' : 'text-gray-400' }}">
                                    {{ $sidebarLogoDark ? 'Aktif' : 'Default' }}
                                </span>
                            </div>

                            <input type="file" name="sidebar_logo_dark" accept="image/png"
                                class="block w-full text-xs text-gray-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-900 file:text-indigo-300 hover:file:bg-indigo-800">
                            @error('sidebar_logo_dark') <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Logo Settings - Login -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Logo Halaman Login</h3>
                    <p class="text-sm text-gray-500 mb-6">Logo yang ditampilkan di halaman login dan register. Ukuran:
                        80x80 px, Format: PNG</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Light Mode Logo -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <label class="text-sm font-semibold text-gray-700">Mode Siang (Light)</label>
                            </div>

                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                                    @if($loginLogoLight)
                                        <img src="{{ asset('storage/' . $loginLogoLight) }}" alt="Login Logo Light"
                                            class="w-20 h-20 object-contain">
                                    @else
                                        <x-application-logo class="w-16 h-16 fill-current text-gray-500" />
                                    @endif
                                </div>
                                <span class="text-xs {{ $loginLogoLight ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $loginLogoLight ? 'Aktif' : 'Default' }}
                                </span>
                            </div>

                            <input type="file" name="login_logo_light" accept="image/png"
                                class="block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                            @error('login_logo_light') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Dark Mode Logo -->
                        <div class="border border-gray-600 rounded-lg p-4 bg-gray-800">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                                <label class="text-sm font-semibold text-gray-200">Mode Malam (Dark)</label>
                            </div>

                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="w-24 h-24 bg-gray-700 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-500">
                                    @if($loginLogoDark)
                                        <img src="{{ asset('storage/' . $loginLogoDark) }}" alt="Login Logo Dark"
                                            class="w-20 h-20 object-contain">
                                    @else
                                        <x-application-logo class="w-16 h-16 fill-current text-gray-400" />
                                    @endif
                                </div>
                                <span class="text-xs {{ $loginLogoDark ? 'text-green-400' : 'text-gray-400' }}">
                                    {{ $loginLogoDark ? 'Aktif' : 'Default' }}
                                </span>
                            </div>

                            <input type="file" name="login_logo_dark" accept="image/png"
                                class="block w-full text-xs text-gray-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-900 file:text-indigo-300 hover:file:bg-indigo-800">
                            @error('login_logo_dark') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Branding Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Branding Settings</h3>
                    <p class="text-sm text-gray-500 mb-6">Atur nama aplikasi dan favicon untuk browser tab.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- App Title -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                Nama Aplikasi (Tab Title)
                            </label>
                            <p class="text-xs text-gray-500 mb-3">Default: "Order IT"</p>

                            <input type="text" name="app_title" value="{{ $appTitle ?? 'Order IT' }}"
                                placeholder="Order IT"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @error('app_title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Favicon -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                Favicon (Icon Tab)
                            </label>
                            <p class="text-xs text-gray-500 mb-3">Format: ICO, 16x16 atau 32x32 px</p>

                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600">
                                    @if($favicon)
                                        <img src="{{ asset('storage/' . $favicon) }}" alt="Favicon"
                                            class="w-8 h-8 object-contain">
                                    @else
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    @endif
                                </div>
                                <span class="text-sm {{ $favicon ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $favicon ? 'Aktif' : 'Default' }}
                                </span>
                            </div>

                            <input type="file" name="favicon" accept=".ico,image/x-icon,image/vnd.microsoft.icon"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('favicon') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Reset Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Reset ke Default</h4>
                            <p class="text-xs text-gray-500">Hapus semua logo, favicon custom dan reset nama ke default.
                            </p>
                        </div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="reset_all" value="1"
                                class="rounded text-red-600 focus:ring-red-500">
                            <span class="text-sm text-red-600">Reset All</span>
                        </label>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('superadmin.settings') }}"
                        class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-sidebar-layout>