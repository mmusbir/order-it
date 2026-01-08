<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('superadmin.settings.approval-roles') }}"
                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Approval Roles
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Approval Role</h2>

                <form method="POST" action="{{ route('superadmin.settings.approval-roles.update', $approvalRole) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Approval Role <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $approvalRole->name) }}" required
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code</label>
                        <input type="text" name="code" value="{{ old('code', $approvalRole->code) }}"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @error('code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $approvalRole->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="is_active"
                            {{ old('is_active', $approvalRole->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Active</label>
                    </div>

                    <!-- Approval Levels Mapping -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-6 mt-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Approval Levels</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tentukan siapa yang akan menjadi approver untuk tiap level.</p>
                            </div>
                        </div>
                        
                        @php
                            $maxLevels = 4; // Best practice: 2-4 approval levels
                            $levelLabels = [
                                1 => 'Level 1',
                                2 => 'Level 2',
                                3 => 'Level 3',
                                4 => 'Level 4',
                            ];
                        @endphp

                        <div id="approval-levels-container" class="space-y-4">
                            @for($level = 1; $level <= $maxLevels; $level++)
                                @php
                                    $levelUsers = $usersByLevel[$level] ?? collect();
                                    $roleName = $levelRoleMap[$level] ?? '';
                                @endphp
                                <div class="approval-level-item bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4" data-level="{{ $level }}">
                                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                                        <div class="w-24">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Level {{ $level }}
                                            </span>
                                        </div>
                                        <div class="flex-1 relative">
                                            <div class="searchable-select-wrapper" data-level="{{ $level }}">
                                                <input type="text" 
                                                    class="searchable-select-input w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                                    placeholder="Ketik untuk mencari approver..."
                                                    data-level="{{ $level }}"
                                                    autocomplete="off">
                                                <input type="hidden" name="levels[{{ $level }}][user_id]" 
                                                    id="level_user_{{ $level }}"
                                                    value="{{ isset($currentLevels[$level]) ? $currentLevels[$level]->user_id : '' }}">
                                                <div class="searchable-select-dropdown hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                                    <div class="searchable-select-option px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer text-gray-500 dark:text-gray-400" data-value="">
                                                        -- Pilih Approver --
                                                    </div>
                                                    @forelse($levelUsers as $user)
                                                        <div class="searchable-select-option px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer text-gray-900 dark:text-white" 
                                                            data-value="{{ $user->id }}" 
                                                            data-name="{{ $user->name }}"
                                                            data-email="{{ $user->email }}">
                                                            <span class="font-medium">{{ $user->name }}</span>
                                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ $user->email }}</span>
                                                        </div>
                                                    @empty
                                                        <div class="px-4 py-2 text-gray-500 dark:text-gray-400 text-sm italic">
                                                            Tidak ada user dengan role Approver
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="hidden" name="levels[{{ $level }}][is_active]" value="0">
                                            <input type="checkbox" name="levels[{{ $level }}][is_active]" value="1"
                                                id="level_active_{{ $level }}"
                                                {{ !isset($currentLevels[$level]) || $currentLevels[$level]->is_active ? 'checked' : '' }}
                                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            <label for="level_active_{{ $level }}" class="text-sm text-gray-600 dark:text-gray-400">Aktif</label>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400 italic">
                            <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Best practice: 2-4 tahap approval untuk keseimbangan antara kontrol dan efisiensi.
                        </p>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Set initial values for searchable inputs
                            @foreach(range(1, 4) as $level)
                                @if(isset($currentLevels[$level]) && $currentLevels[$level]->user_id)
                                    @php
                                        $selectedUser = ($usersByLevel[$level] ?? collect())->firstWhere('id', $currentLevels[$level]->user_id);
                                    @endphp
                                    @if($selectedUser)
                                        document.querySelector('input.searchable-select-input[data-level="{{ $level }}"]').value = "{{ $selectedUser->name }} ({{ $selectedUser->email }})";
                                    @endif
                                @endif
                            @endforeach

                            // Searchable select functionality
                            document.querySelectorAll('.searchable-select-wrapper').forEach(wrapper => {
                                const input = wrapper.querySelector('.searchable-select-input');
                                const dropdown = wrapper.querySelector('.searchable-select-dropdown');
                                const hiddenInput = wrapper.querySelector('input[type="hidden"]');
                                const options = wrapper.querySelectorAll('.searchable-select-option');
                                const level = wrapper.dataset.level;

                                input.addEventListener('focus', () => {
                                    dropdown.classList.remove('hidden');
                                });

                                input.addEventListener('blur', (e) => {
                                    setTimeout(() => dropdown.classList.add('hidden'), 200);
                                });

                                input.addEventListener('input', (e) => {
                                    const search = e.target.value.toLowerCase();
                                    options.forEach(opt => {
                                        const name = (opt.dataset.name || '').toLowerCase();
                                        const email = (opt.dataset.email || '').toLowerCase();
                                        const match = name.includes(search) || email.includes(search) || opt.dataset.value === '';
                                        opt.classList.toggle('hidden', !match);
                                    });
                                    dropdown.classList.remove('hidden');
                                });

                                options.forEach(opt => {
                                    opt.addEventListener('click', () => {
                                        const value = opt.dataset.value;
                                        const name = opt.dataset.name || '';
                                        const email = opt.dataset.email || '';
                                        
                                        hiddenInput.value = value;
                                        input.value = value ? `${name} (${email})` : '';
                                        dropdown.classList.add('hidden');
                                    });
                                });
                            });
                        });
                    </script>

                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                            Update Approval Role
                        </button>
                        <a href="{{ route('superadmin.settings.approval-roles') }}"
                            class="px-6 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-sidebar-layout>
