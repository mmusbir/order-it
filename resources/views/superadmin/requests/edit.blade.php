<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('superadmin.requests') }}"
                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Back to Requests
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Edit Request</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-6">{{ $request->ticket_no }}</p>

                <form method="POST" action="{{ route('superadmin.requests.update', $request) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Request Info (Read-only) -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Requester</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $request->requester->name ?? 'Unknown' }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 mb-1">Items</p>
                        <ul class="text-gray-900 dark:text-white">
                            @foreach($request->items as $item)
                                <li>• {{ $item->item_name ?? $item->product?->name ?? 'Unknown' }} (x{{ $item->qty }})</li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status" required
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ $request->status == $key ? 'selected' : '' }}>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                        <textarea name="notes" rows="3"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Add notes about this status change...">{{ old('notes', $request->notes) }}</textarea>
                        @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                            class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            Update Request
                        </button>
                        <a href="{{ route('superadmin.requests') }}"
                            class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-sidebar-layout>