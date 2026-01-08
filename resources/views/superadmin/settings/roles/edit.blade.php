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
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Role</h2>
                    @if($role->is_system)
                        <span class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">System Role</span>
                    @endif
                </div>

                <form method="POST" action="{{ route('superadmin.settings.roles.update', $role) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Role <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="slug" value="{{ old('slug', $role->slug) }}" required
                            {{ $role->is_system ? 'readonly' : '' }}
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500 {{ $role->is_system ? 'bg-gray-100 dark:bg-gray-600 cursor-not-allowed' : '' }}">
                        @if($role->is_system)
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Slug system role tidak dapat diubah.</p>
                        @endif
                        @error('slug')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Approval Level</label>
                        <select name="approval_level"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Bukan Approver --</option>
                            <option value="1" {{ old('approval_level', $role->approval_level) == '1' ? 'selected' : '' }}>Level 1</option>
                            <option value="2" {{ old('approval_level', $role->approval_level) == '2' ? 'selected' : '' }}>Level 2</option>
                            <option value="3" {{ old('approval_level', $role->approval_level) == '3' ? 'selected' : '' }}>Level 3</option>
                            <option value="4" {{ old('approval_level', $role->approval_level) == '4' ? 'selected' : '' }}>Level 4</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_approver" value="0">
                        <input type="checkbox" name="is_approver" value="1" id="is_approver"
                            {{ old('is_approver', $role->is_approver) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="is_approver" class="text-sm text-gray-700 dark:text-gray-300">Adalah Approver</label>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="is_active"
                            {{ old('is_active', $role->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Active</label>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                            Update Role
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
