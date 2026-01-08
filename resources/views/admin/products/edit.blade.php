<x-sidebar-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('admin.products.index') }}"
                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Back to Catalog
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Product</h2>

                <form method="POST" action="{{ route('admin.products.update', $product) }}"
                    enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>


                    <!-- Category and Model Selection (linked) -->
                    @if(count($categories) > 0 && count($models) > 0)
                        <div x-data="{
                                    // Category state
                                    catOpen: false,
                                    catSearch: '',
                                    selectedCategory: '{{ old('category', $product->category) }}',
                                    selectedCategoryId: '{{ old('snipeit_category_id', $product->snipeit_category_id) }}',
                                    categories: {{ Js::from($categories) }},

                                    // Model state
                                    modelOpen: false,
                                    modelSearch: '',
                                    selectedModel: '{{ old('model_name', $product->model_name) }}',
                                    selectedModelId: '{{ old('snipeit_model_id', $product->snipeit_model_id) }}',
                                    allModels: {{ Js::from($models) }},

                                    get filteredCategories() {
                                        if (!this.catSearch) return this.categories;
                                        return this.categories.filter(c => c.name.toLowerCase().includes(this.catSearch.toLowerCase()));
                                    },

                                    get filteredModels() {
                                        let models = this.allModels;
                                        // Filter by selected category
                                        if (this.selectedCategoryId) {
                                            models = models.filter(m => m.category_id == this.selectedCategoryId);
                                        }
                                        // Then filter by search
                                        if (this.modelSearch) {
                                            models = models.filter(m => m.name.toLowerCase().includes(this.modelSearch.toLowerCase()));
                                        }
                                        return models;
                                    },

                                    selectCategory(cat) {
                                        this.selectedCategory = cat.name;
                                        this.selectedCategoryId = cat.id;
                                        this.catSearch = '';
                                        this.catOpen = false;
                                        // Reset model selection when category changes
                                        this.selectedModel = '';
                                        this.selectedModelId = '';
                                    },

                                    clearCategory() {
                                        this.selectedCategory = '';
                                        this.selectedCategoryId = '';
                                        this.catSearch = '';
                                        // Also reset model
                                        this.selectedModel = '';
                                        this.selectedModelId = '';
                                    },

                                    selectModel(model) {
                                        this.selectedModel = model.name;
                                        this.selectedModelId = model.id;
                                        this.modelSearch = '';
                                        this.modelOpen = false;
                                    },

                                    clearModel() {
                                        this.selectedModel = '';
                                        this.selectedModelId = '';
                                        this.modelSearch = '';
                                    }
                                }" class="space-y-5">

                            <!-- Category Dropdown -->
                            <div class="relative">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                <input type="hidden" name="category" :value="selectedCategory">
                                <input type="hidden" name="snipeit_category_id" :value="selectedCategoryId">
                                <div class="relative">
                                    <input type="text" x-model="catSearch" @focus="catOpen = true" @click="catOpen = true"
                                        @keydown.escape="catOpen = false"
                                        :placeholder="selectedCategory ? selectedCategory : 'Search or select category...'"
                                        class="w-full px-4 py-2 pr-20 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                                        <button type="button" x-show="selectedCategory" @click="clearCategory()"
                                            class="text-gray-400 hover:text-red-500 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        <button type="button" @click="catOpen = !catOpen"
                                            class="text-gray-400 hover:text-gray-600 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div x-show="catOpen" @click.away="catOpen = false" x-transition
                                    class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl max-h-60 overflow-y-auto"
                                    style="background: #fff;">
                                    <template x-for="cat in filteredCategories" :key="cat.id">
                                        <div @click="selectCategory(cat)"
                                            class="px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer text-gray-900 dark:text-white"
                                            x-text="cat.name"></div>
                                    </template>
                                    <div x-show="filteredCategories.length === 0" class="px-4 py-2 text-gray-500">No
                                        categories found</div>
                                </div>
                                <p class="text-xs text-green-500 mt-1">✓ {{ count($categories) }} categories available</p>
                                @error('category') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Model Dropdown (filtered by category) -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model
                                    Name</label>
                                <input type="hidden" name="model_name" :value="selectedModel">
                                <input type="hidden" name="snipeit_model_id" :value="selectedModelId">
                                <div class="relative">
                                    <input type="text" x-model="modelSearch" @focus="modelOpen = true"
                                        @click="modelOpen = true" @keydown.escape="modelOpen = false"
                                        :placeholder="selectedModel ? selectedModel : (selectedCategoryId ? 'Search or select model...' : 'Select a category first...')"
                                        :disabled="!selectedCategoryId"
                                        :class="!selectedCategoryId ? 'bg-gray-100 cursor-not-allowed' : ''"
                                        class="w-full px-4 py-2 pr-20 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                                        <button type="button" x-show="selectedModel" @click="clearModel()"
                                            class="text-gray-400 hover:text-red-500 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        <button type="button" @click="modelOpen = !modelOpen"
                                            class="text-gray-400 hover:text-gray-600 p-1" :disabled="!selectedCategoryId">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div x-show="modelOpen && selectedCategoryId" @click.away="modelOpen = false" x-transition
                                    class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl max-h-60 overflow-y-auto"
                                    style="background: #fff;">
                                    <template x-for="model in filteredModels" :key="model.id">
                                        <div @click="selectModel(model)"
                                            class="px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer text-gray-900 dark:text-white">
                                            <span x-text="model.name"></span>
                                            <span x-show="model.model_number" class="text-gray-400 text-sm ml-1"
                                                x-text="'(' + model.model_number + ')'"></span>
                                        </div>
                                    </template>
                                    <div x-show="filteredModels.length === 0" class="px-4 py-2 text-gray-500">No models
                                        found for this category</div>
                                </div>
                                <p class="text-xs mt-1" :class="selectedCategoryId ? 'text-green-500' : 'text-gray-400'">
                                    <span x-show="selectedCategoryId">✓ Showing models for selected category</span>
                                    <span x-show="!selectedCategoryId">Select a category to see available models</span>
                                </p>
                                @error('model_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @elseif(count($categories) > 0)
                        <!-- Only categories available -->
                        <div x-data="{ 
                                    open: false, 
                                    search: '',
                                    selected: '{{ old('category', $product->category) }}',
                                    selectedId: '{{ old('snipeit_category_id', $product->snipeit_category_id) }}',
                                    categories: {{ Js::from($categories) }},
                                    get filtered() {
                                        if (!this.search) return this.categories;
                                        return this.categories.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    select(cat) {
                                        this.selected = cat.name;
                                        this.selectedId = cat.id;
                                        this.search = '';
                                        this.open = false;
                                    }
                                }" class="relative">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                            <input type="hidden" name="category" :value="selected">
                            <input type="hidden" name="snipeit_category_id" :value="selectedId">
                            <div class="relative">
                                <input type="text" x-model="search" @focus="open = true" @click="open = true"
                                    @keydown.escape="open = false"
                                    :placeholder="selected ? selected : 'Search or select category...'"
                                    class="w-full px-4 py-2 pr-20 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                                    <button type="button" x-show="selected"
                                        @click="selected = ''; selectedId = ''; search = ''"
                                        class="text-gray-400 hover:text-red-500 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <button type="button" @click="open = !open"
                                        class="text-gray-400 hover:text-gray-600 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl max-h-60 overflow-y-auto"
                                style="background: #fff;">
                                <template x-for="cat in filtered" :key="cat.id">
                                    <div @click="select(cat)"
                                        class="px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer text-gray-900 dark:text-white"
                                        x-text="cat.name"></div>
                                </template>
                                <div x-show="filtered.length === 0" class="px-4 py-2 text-gray-500">No categories found
                                </div>
                            </div>
                            <p class="text-xs text-green-500 mt-1">✓ {{ count($categories) }} categories available</p>
                            @error('category') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model
                                Name</label>
                            <input type="text" name="model_name" value="{{ old('model_name', $product->model_name) }}"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter model name manually">
                            <p class="text-xs text-gray-400 mt-1">No models synced from Snipe-IT</p>
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                            <input type="text" name="category" value="{{ old('category', $product->category) }}"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter category manually">
                            <p class="text-xs text-gray-400 mt-1">Snipe-IT not connected - enter manually</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model
                                Name</label>
                            <input type="text" name="model_name" value="{{ old('model_name', $product->model_name) }}"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter model name manually">
                            <p class="text-xs text-gray-400 mt-1">Snipe-IT not connected - enter manually</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Specifications
                            <span class="text-red-500">*</span></label>
                        <textarea name="specs" rows="3" required
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('specs', $product->specs) }}</textarea>
                        @error('specs') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product
                            Image</label>
                        @if($product->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    class="w-24 h-24 object-cover rounded-lg">
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                        <p class="text-xs text-gray-400 mt-1">Leave empty to keep current image</p>
                        @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Request Types -->
                    @php
                        $selectedTypeIds = old('request_types', $product->requestTypes->pluck('id')->toArray());
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Visible untuk Request Type</label>
                        <p class="text-xs text-gray-500 mb-3">Pilih request type mana yang dapat melihat produk ini. Jika tidak ada yang dipilih, produk akan tersedia untuk semua request type.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($requestTypes as $type)
                                <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition">
                                    <input type="checkbox" 
                                        name="request_types[]" 
                                        value="{{ $type->id }}"
                                        {{ in_array($type->id, $selectedTypeIds) ? 'checked' : '' }}
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $type->name }}</span>
                                        @if($type->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $type->description }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('request_types') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                            class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            Update Product
                        </button>
                        <a href="{{ route('admin.products.index') }}"
                            class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-sidebar-layout>
