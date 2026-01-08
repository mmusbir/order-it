<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches) }"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $appTitle = \App\Models\AppSetting::getValue('app_title', 'Order IT');
        $favicon = \App\Models\AppSetting::getValue('favicon');
        $loginLogoLight = \App\Models\AppSetting::getValue('login_logo_light');
        $loginLogoDark = \App\Models\AppSetting::getValue('login_logo_dark');
    @endphp
    <title>{{ $appTitle }}</title>
    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <a href="/">
                {{-- Light mode logo --}}
                @if($loginLogoLight)
                    <img x-show="!darkMode" src="{{ asset('storage/' . $loginLogoLight) }}" alt="Logo"
                        class="w-20 h-20 object-contain">
                @else
                    <x-application-logo x-show="!darkMode" class="w-20 h-20 fill-current text-gray-500" />
                @endif

                {{-- Dark mode logo --}}
                @if($loginLogoDark)
                    <img x-show="darkMode" src="{{ asset('storage/' . $loginLogoDark) }}" alt="Logo"
                        class="w-20 h-20 object-contain">
                @else
                    <x-application-logo x-show="darkMode" class="w-20 h-20 fill-current text-gray-400" />
                @endif
            </a>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>