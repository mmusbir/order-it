<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Product Catalog') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="cartManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Page Header with Action -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">IT Asset Catalog</h1>
                    <p class="text-gray-500 mt-1">Browse and add products to your cart, then proceed to request.</p>
                </div>
                <!-- Cart Button in Header -->
                <button x-show="cart.length > 0" @click="showCart = true"
                    class="mt-4 md:mt-0 relative bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg shadow flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <span class="font-semibold">View Cart</span>
                    <span
                        class="bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center ml-1"
                        x-text="cart.length"></span>
                </button>
            </div>

            <!-- Search & Filter -->
            <div class="mb-6 flex gap-4">
                <form method="GET" action="{{ route('products.index') }}" class="flex-1 flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search laptop, PC..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-lg font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition">
                        Search
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <!-- Products Grid -->
                @forelse($products as $product)
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg flex flex-col h-full transform transition hover:scale-105 duration-200">
                        <div
                            class="h-48 bg-gray-200 dark:bg-gray-700 w-full object-cover flex items-center justify-center text-gray-400">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    class="h-full w-full object-cover">
                            @else
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            @endif
                        </div>
                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $product->specs }}</p>
                            <div class="mt-auto pt-4 flex items-center justify-between">
                                <span
                                    class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $product->category->name ?? 'IT Asset' }}</span>
                                <button
                                    @click="addToCart(@js(['id' => $product->id, 'name' => $product->name, 'specs' => Str::limit($product->specs ?? '', 50), 'image' => $product->image ? asset('storage/' . $product->image) : '']))"
                                    :class="isInCart({{ $product->id }}) ? 'bg-green-600 hover:bg-green-700' : 'bg-indigo-600 hover:bg-indigo-700'"
                                    class="px-3 py-1.5 text-white text-sm font-medium rounded-lg transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    <span x-text="isInCart({{ $product->id }}) ? 'In Cart' : 'Add'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-12">
                        No products found.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>



        <!-- Cart Sidebar Modal -->
        <div x-show="showCart" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 z-50 flex justify-end"
            @click.self="showCart = false">

            <div x-show="showCart" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-full max-w-md bg-white dark:bg-gray-800 h-full shadow-xl flex flex-col">

                <!-- Cart Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Your Cart</h2>
                    <button @click="showCart = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-6">
                    <template x-if="cart.length === 0">
                        <div class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <p>Your cart is empty</p>
                        </div>
                    </template>

                    <div class="space-y-4">
                        <template x-for="(item, index) in cart" :key="item.id">
                            <div class="flex gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div
                                    class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <template x-if="item.image">
                                        <img :src="item.image" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!item.image">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white" x-text="item.name"></h4>
                                    <p class="text-sm text-gray-500" x-text="item.specs"></p>
                                    <div class="mt-2 flex items-center gap-2">
                                        <label class="text-xs text-gray-500">Qty:</label>
                                        <input type="number" min="1" x-model.number="item.qty" @change="saveCart()"
                                            class="w-16 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded">
                                    </div>
                                </div>
                                <button @click="removeFromCart(index)"
                                    class="text-red-500 hover:text-red-700 self-start">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Cart Footer -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total Items</span>
                        <span class="font-semibold text-gray-900 dark:text-white" x-text="getTotalItems()"></span>
                    </div>
                    <button @click="clearCart()"
                        class="w-full py-2 text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition text-sm">
                        Clear Cart
                    </button>
                    <form method="GET" action="{{ route('requests.checkout') }}" @submit="submitCart($event)">
                        <input type="hidden" name="cart_items" x-model="cartJson">
                        <button type="submit" :disabled="cart.length === 0"
                            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                            Lanjutkan ke Detail Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cartManager() {
            return {
                cart: JSON.parse(localStorage.getItem('requestCart') || '[]'),
                showCart: false,
                get cartJson() {
                    return JSON.stringify(this.cart);
                },

                addToCart(product) {
                    const existing = this.cart.find(item => item.id === product.id);
                    if (!existing) {
                        this.cart.push({ ...product, qty: 1 });
                        this.saveCart();
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.saveCart();
                },

                isInCart(productId) {
                    return this.cart.some(item => item.id === productId);
                },

                clearCart() {
                    this.cart = [];
                    this.saveCart();
                },

                getTotalItems() {
                    return this.cart.reduce((sum, item) => sum + (item.qty || 1), 0);
                },

                saveCart() {
                    localStorage.setItem('requestCart', JSON.stringify(this.cart));
                },

                submitCart(e) {
                    // Cart data is already in the hidden input
                }
            }
        }
    </script>
</x-sidebar-layout>