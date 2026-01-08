<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Consumable Management
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif
            @if($error)
                <div class="mb-4 p-4 bg-yellow-100 border border-yellow-200 text-yellow-700 rounded-lg">
                    {{ $error }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg" x-data="consumableManager()">
                <!-- Search and Bulk Actions -->
                <div
                    class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap gap-4 items-center justify-between">
                    <div class="flex gap-2 items-center">
                        <form method="GET" class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari consumable..." class="px-3 py-2 border rounded-lg text-sm w-64">
                            <button type="submit"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm hover:bg-gray-700">
                                Cari
                            </button>
                        </form>

                        <!-- Sync Button -->
                        <form method="POST" action="{{ route('superadmin.consumables.sync') }}">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Sync
                            </button>
                        </form>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="flex gap-2 items-center">
                        <span x-show="selectedIds.length > 0" class="text-sm text-gray-600 dark:text-gray-400">
                            <span x-text="selectedIds.length"></span> item dipilih
                        </span>
                        <button type="button" x-show="selectedIds.length > 0" @click="bulkDelete()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            Hapus Terpilih
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" @change="toggleAll($event)" x-ref="selectAllCheckbox"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    ID</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Nama</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Kategori</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Qty Total</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tersedia</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($consumables as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" value="{{ $item['id'] }}"
                                            @change="toggleItem({{ $item['id'] }})"
                                            :checked="selectedIds.includes({{ $item['id'] }})"
                                            class="item-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $item['id'] }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['name'] }}
                                        </div>
                                        @if(isset($item['manufacturer']['name']))
                                            <div class="text-xs text-gray-400">{{ $item['manufacturer']['name'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $item['category']['name'] ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-white">
                                        {{ $item['qty'] ?? 0 }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @php $remaining = $item['remaining'] ?? 0; @endphp
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                        {{ $remaining > 10 ? 'bg-green-100 text-green-800' : ($remaining > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $remaining }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button"
                                            @click="confirmDelete({{ $item['id'] }}, '{{ addslashes($item['name']) }}')"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        @if($error)
                                            Tidak dapat memuat data consumables.
                                        @else
                                            Tidak ada data consumables.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700 text-sm text-gray-500">
                    Total: {{ count($consumables) }} consumables
                </div>

                <!-- Delete Confirmation Modal -->
                <div x-show="showDeleteModal" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Konfirmasi Hapus</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Apakah Anda yakin ingin menghapus <span class="font-semibold"
                                x-text="deleteItemName"></span>?
                            Item akan dihapus dari Snipe-IT.
                        </p>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showDeleteModal = false"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                Batal
                            </button>
                            <form :action="deleteUrl" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Bulk Delete Form (hidden) -->
                <form id="bulkDeleteForm" method="POST" action="{{ route('superadmin.consumables.bulk-delete') }}"
                    class="hidden">
                    @csrf
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                </form>
            </div>
        </div>
    </div>

    <script>
        function consumableManager() {
            return {
                selectedIds: [],
                showDeleteModal: false,
                deleteItemId: null,
                deleteItemName: '',
                deleteUrl: '',

                toggleAll(event) {
                    if (event.target.checked) {
                        this.selectedIds = [...document.querySelectorAll('.item-checkbox')].map(cb => parseInt(cb.value));
                    } else {
                        this.selectedIds = [];
                    }
                },

                toggleItem(id) {
                    if (this.selectedIds.includes(id)) {
                        this.selectedIds = this.selectedIds.filter(i => i !== id);
                    } else {
                        this.selectedIds.push(id);
                    }
                },

                confirmDelete(id, name) {
                    this.deleteItemId = id;
                    this.deleteItemName = name;
                    this.deleteUrl = '{{ url("superadmin/consumables") }}/' + id;
                    this.showDeleteModal = true;
                },

                bulkDelete() {
                    if (this.selectedIds.length === 0) return;
                    if (confirm('Apakah Anda yakin ingin menghapus ' + this.selectedIds.length + ' item terpilih?')) {
                        document.getElementById('bulkDeleteForm').submit();
                    }
                }
            }
        }
    </script>
</x-sidebar-layout>