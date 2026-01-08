<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('superadmin.settings.roles') }}"
                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Role Management
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Create New Role</h2>

                <form method="POST" action="{{ route('superadmin.settings.roles.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Role <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="e.g. Finance Approver, IT Support"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="slug" value="{{ old('slug') }}" required
                            placeholder="e.g. finance-approver, it-support"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Slug unik untuk identifikasi role (lowercase, tanpa spasi).</p>
                        @error('slug')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3"
                            placeholder="Deskripsi role ini..."
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Approval Level</label>
                        <select name="approval_level"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Bukan Approver --</option>
                            <option value="1" {{ old('approval_level') == '1' ? 'selected' : '' }}>Level 1</option>
                            <option value="2" {{ old('approval_level') == '2' ? 'selected' : '' }}>Level 2</option>
                            <option value="3" {{ old('approval_level') == '3' ? 'selected' : '' }}>Level 3</option>
                            <option value="4" {{ old('approval_level') == '4' ? 'selected' : '' }}>Level 4</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pilih level jika role ini adalah approver.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_approver" value="0">
                        <input type="checkbox" name="is_approver" value="1" id="is_approver"
                            {{ old('is_approver') ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="is_approver" class="text-sm text-gray-700 dark:text-gray-300">Adalah Approver</label>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                            Create Role
                        </button>
                        <a href="{{ route('superadmin.settings.roles') }}"
                            class="px-6 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-sidebar-layout>
