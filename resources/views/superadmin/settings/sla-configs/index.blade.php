<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('superadmin.settings') }}" class="hover:text-gray-700">Settings</a>
            <span class="mx-2">/</span>
            <a href="{{ route('superadmin.settings.master-data') }}" class="hover:text-gray-700">Master Data</a>
            <span class="mx-2">/</span>
            <span class="text-indigo-600 font-semibold">SLA Configuration</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('superadmin.settings.master-data') }}"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">SLA Configuration</h1>
                    </div>
                    <p class="text-gray-500">Kelola target SLA untuk Approval dan Fulfillment berdasarkan Priority.</p>
                </div>
            </div>

            <!-- Session Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/50 border-l-4 border-green-500 rounded-r-lg">
                    <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/50 border-l-4 border-red-500 rounded-r-lg">
                    <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Tabs -->
            <div x-data="{ activeTab: 'approval' }" class="space-y-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <button @click="activeTab = 'approval'"
                            :class="activeTab === 'approval' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                            SLA Approval (Per Level)
                        </button>
                        <button @click="activeTab = 'fulfillment'"
                            :class="activeTab === 'fulfillment' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                            SLA Fulfillment (Per Priority)
                        </button>
                    </nav>
                </div>

                <!-- Tab: SLA Approval -->
                <div x-show="activeTab === 'approval'" x-transition>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SLA Approval per Level</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Target waktu approval untuk setiap level. SLA approval terpisah dari SLA fulfillment.
                            </p>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                        Level</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                        Target (Jam)</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                        Warning</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                        Escalation</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($approvalConfigs as $config)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4">
                                            <span
                                                class="font-medium text-gray-900 dark:text-white">{{ $config->level_label }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                            <span class="font-semibold text-lg">{{ $config->target_hours }}</span> jam
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                            {{ $config->warning_percent }}%
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                            {{ $config->escalation_percent }}%
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full {{ $config->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300' }}">
                                                {{ $config->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button type="button"
                                                onclick="openApprovalModal({{ $config->id }}, {{ $config->approval_level }}, {{ $config->target_hours }}, {{ $config->warning_percent }}, {{ $config->escalation_percent }}, {{ $config->is_active ? 'true' : 'false' }})"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 text-sm font-medium">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: SLA Fulfillment -->
                <div x-show="activeTab === 'fulfillment'" x-transition>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SLA Fulfillment per Priority
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Target waktu penyelesaian IT setelah approval selesai. SLA mulai saat approval terakhir
                                disetujui.
                            </p>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                        Priority</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                        Response (Jam)</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                        Fulfillment (Jam)</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                        Warning</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($fulfillmentConfigs as $config)
                                    @php
                                        $priorityColors = [
                                            'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                                            'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                            'low' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        ];
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1 text-sm font-medium rounded-full {{ $priorityColors[$config->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $config->priority_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                            <span class="font-semibold">{{ $config->response_hours }}</span> jam
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                            <span class="font-semibold text-lg">{{ $config->fulfillment_hours }}</span> jam
                                            <span
                                                class="text-xs text-gray-400">({{ round($config->fulfillment_hours / 8, 1) }}
                                                hari kerja)</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                            {{ $config->warning_percent }}%
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full {{ $config->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300' }}">
                                                {{ $config->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button type="button"
                                                onclick="openFulfillmentModal({{ $config->id }}, '{{ $config->priority_label }}', {{ $config->response_hours }}, {{ $config->fulfillment_hours }}, {{ $config->warning_percent }}, {{ $config->is_active ? 'true' : 'false' }})"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 text-sm font-medium">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Info Box -->
                    <div
                        class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-blue-800 dark:text-blue-200">
                                <p class="font-semibold mb-1">Catatan:</p>
                                <ul class="list-disc list-inside space-y-1 text-blue-700 dark:text-blue-300">
                                    <li>SLA Fulfillment dimulai <strong>setelah approval level terakhir</strong>
                                        disetujui</li>
                                    <li>SLA <strong>tidak berjalan</strong> saat status Waiting for Approval</li>
                                    <li>Jam kerja dihitung 8 jam per hari</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Approval SLA -->
    <div id="approvalModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit SLA Approval</h3>
            <form id="approvalForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Level</label>
                    <input type="text" id="approvalLevel" disabled
                        class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Target (Jam Kerja)
                        *</label>
                    <input type="number" name="target_hours" id="approvalTargetHours" required min="1" max="720"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Maksimal waktu untuk menyelesaikan approval di level ini</p>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Warning
                            (%)</label>
                        <input type="number" name="warning_percent" id="approvalWarning" required min="10" max="90"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Escalation
                            (%)</label>
                        <input type="number" name="escalation_percent" id="approvalEscalation" required min="50"
                            max="99"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="approvalActive" value="1"
                            class="rounded text-indigo-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('approvalModal').classList.add('hidden')"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-400">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Fulfillment SLA -->
    <div id="fulfillmentModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit SLA Fulfillment</h3>
            <form id="fulfillmentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                    <input type="text" id="fulfillmentPriority" disabled
                        class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Response Time (Jam)
                        *</label>
                    <input type="number" name="response_hours" id="fulfillmentResponse" required min="1" max="720"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Target waktu IT merespon setelah approval</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fulfillment Time
                        (Jam) *</label>
                    <input type="number" name="fulfillment_hours" id="fulfillmentHours" required min="1" max="720"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Target waktu penyelesaian sampai delivery</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Warning (%)</label>
                    <input type="number" name="warning_percent" id="fulfillmentWarning" required min="10" max="90"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg">
                </div>
                <div class="mb-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="fulfillmentActive" value="1"
                            class="rounded text-indigo-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('fulfillmentModal').classList.add('hidden')"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-400">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const levelLabels = {
            1: 'Level 1 - SPV',
            2: 'Level 2 - Manager',
            3: 'Level 3 - Head',
            4: 'Level 4 - Director'
        };

        function openApprovalModal(id, level, targetHours, warningPercent, escalationPercent, isActive) {
            document.getElementById('approvalForm').action = '/superadmin/settings/sla-approval/' + id;
            document.getElementById('approvalLevel').value = levelLabels[level] || 'Level ' + level;
            document.getElementById('approvalTargetHours').value = targetHours;
            document.getElementById('approvalWarning').value = warningPercent;
            document.getElementById('approvalEscalation').value = escalationPercent;
            document.getElementById('approvalActive').checked = isActive;
            document.getElementById('approvalModal').classList.remove('hidden');
        }

        function openFulfillmentModal(id, priorityLabel, responseHours, fulfillmentHours, warningPercent, isActive) {
            document.getElementById('fulfillmentForm').action = '/superadmin/settings/sla-fulfillment/' + id;
            document.getElementById('fulfillmentPriority').value = priorityLabel;
            document.getElementById('fulfillmentResponse').value = responseHours;
            document.getElementById('fulfillmentHours').value = fulfillmentHours;
            document.getElementById('fulfillmentWarning').value = warningPercent;
            document.getElementById('fulfillmentActive').checked = isActive;
            document.getElementById('fulfillmentModal').classList.remove('hidden');
        }
    </script>
</x-sidebar-layout>