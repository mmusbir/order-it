<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('superadmin.settings') }}" class="hover:text-gray-700">Settings</a>
            <span class="mx-2">/</span>
            <span class="text-indigo-600 font-semibold">Integration</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Integration Settings</h1>
                <p class="text-gray-500 mt-1">Configure external system integrations</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- LDAP / Active Directory -->
                <a href="{{ route('superadmin.settings.ldap') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">LDAP / Active Directory</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sync users from Active Directory</p>
                            @php
                                $ldapEnabled = \App\Models\AppSetting::getValue('ldap_enabled', false);
                            @endphp
                            <span
                                class="inline-flex items-center mt-2 px-2 py-0.5 rounded text-xs font-medium {{ $ldapEnabled ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $ldapEnabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                </a>

                <!-- Snipe-IT -->
                <a href="{{ route('superadmin.settings.snipeit') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-orange-100 dark:bg-orange-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Snipe-IT</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Connect to Snipe-IT asset management</p>
                            @php
                                $snipeitEnabled = \App\Models\AppSetting::getValue('snipeit_enabled', false);
                            @endphp
                            <span
                                class="inline-flex items-center mt-2 px-2 py-0.5 rounded text-xs font-medium {{ $snipeitEnabled ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $snipeitEnabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-sidebar-layout>