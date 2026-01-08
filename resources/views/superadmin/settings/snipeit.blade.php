<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('superadmin.dashboard') }}"
                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div
                    class="mb-6 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div
                    class="mb-6 p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Snipe-IT API Settings</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Connect to Snipe-IT for asset management
                            sync.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('superadmin.settings.snipeit') }}" class="space-y-5">
                    @csrf

                    <!-- Enable Toggle -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Enable Snipe-IT Sync</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Allow syncing assets to Snipe-IT</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="snipeit_enabled" value="1" {{ ($settings['snipeit_enabled'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-indigo-600">
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Snipe-IT
                            URL</label>
                        <input type="url" name="snipeit_url" value="{{ $settings['snipeit_url'] ?? '' }}"
                            placeholder="https://snipeit.company.com"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">API Token</label>
                        <input type="password" name="snipeit_token" value="{{ $settings['snipeit_token'] ?? '' }}"
                            placeholder="Your Personal Access Token"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-400 mt-1">Generate this from Snipe-IT Admin → User → API Keys</p>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                            class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            Save Settings
                        </button>
                    </div>
                </form>

                <!-- Test Connection Button -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="testConnectionBtn"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Test Connection
                    </button>
                    <div id="testResult" class="mt-4 hidden"></div>
                </div>
            </div>

            <!-- Snipe-IT Log Viewer -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mt-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Snipe-IT Integration Logs</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Recent API activity logs for debugging
                            </p>
                        </div>
                    </div>
                    <button type="button" id="refreshLogsBtn"
                        class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">
                        Refresh
                    </button>
                </div>

                <div id="logContainer"
                    class="bg-gray-900 rounded-lg p-4 max-h-96 overflow-y-auto font-mono text-xs text-green-400">
                    <p class="text-gray-500">Loading logs...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('testConnectionBtn').addEventListener('click', async function () {
            const btn = this;
            const resultDiv = document.getElementById('testResult');

            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Testing...';

            try {
                const response = await fetch('{{ route("superadmin.settings.snipeit.test") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();

                resultDiv.classList.remove('hidden');
                resultDiv.className = data.success
                    ? 'mt-4 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg text-green-700 dark:text-green-300'
                    : 'mt-4 p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300';
                resultDiv.textContent = data.message;
            } catch (error) {
                resultDiv.classList.remove('hidden');
                resultDiv.className = 'mt-4 p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300';
                resultDiv.textContent = 'Connection test failed: ' + error.message;
            }

            btn.disabled = false;
            btn.innerHTML = '<svg class="w-5 h-5 text-gray-700 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span>Test Connection</span>';
        });

        // Load Snipe-IT logs
        async function loadSnipeitLogs() {
            const container = document.getElementById('logContainer');
            try {
                const response = await fetch('{{ route("superadmin.settings.snipeit.logs") }}');
                const data = await response.json();

                if (data.logs && data.logs.length > 0) {
                    container.innerHTML = data.logs.map(log => {
                        let colorClass = 'text-green-400';
                        if (log.includes('[ERROR]') || log.includes('error')) colorClass = 'text-red-400';
                        else if (log.includes('[WARNING]') || log.includes('warning')) colorClass = 'text-yellow-400';
                        else if (log.includes('[INFO]') || log.includes('info')) colorClass = 'text-blue-400';

                        return `<div class="${colorClass} mb-1 border-b border-gray-800 pb-1">${log}</div>`;
                    }).join('');
                } else {
                    container.innerHTML = '<p class="text-gray-500">No Snipe-IT related logs found.</p>';
                }
            } catch (error) {
                container.innerHTML = `<p class="text-red-400">Error loading logs: ${error.message}</p>`;
            }
        }

        document.getElementById('refreshLogsBtn').addEventListener('click', loadSnipeitLogs);

        // Load logs on page load
        loadSnipeitLogs();
    </script>
</x-sidebar-layout>