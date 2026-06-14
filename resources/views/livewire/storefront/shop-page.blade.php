@php
    $storeTheme = $settings['store_theme'] ?? 'grocery';
    
    // Theme dynamic grids
    $gridClass = 'grid grid-cols-2 gap-4 ';
    if ($storeTheme === 'grocery') {
        $gridClass .= 'sm:grid-cols-2 md:grid-cols-4 lg:gap-6';
    } else {
        $gridClass .= 'sm:grid-cols-2 md:grid-cols-3 lg:gap-8';
    }
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 transition-all duration-300">
    <!-- Breadcrumbs -->
    <nav class="flex text-xs text-theme-muted mb-8 gap-2">
        <a href="/" wire:navigate class="hover:text-primary transition-colors">Home</a>
        <span>/</span>
        <span class="text-theme-text font-semibold">Shop</span>
    </nav>

    @if(session()->has('message'))
        <!-- Toast Notification -->
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-5 right-5 bg-primary text-white py-3 px-6 rounded-theme shadow-2xl z-50 flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filters -->
        <aside class="space-y-8 lg:col-span-1">
            <!-- Search Inside Shop -->
            <div class="bg-theme-card border border-theme-border rounded-theme p-6 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-theme-text">Search</h3>
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.400ms="search" 
                        placeholder="Search products..." 
                        class="w-full bg-secondary border border-theme-border rounded-theme p-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                    />
                    @if($search)
                        <button wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-theme-muted hover:text-primary">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Categories Filter -->
            <div class="bg-theme-card border border-theme-border rounded-theme p-6 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-theme-text">Categories</h3>
                <ul class="space-y-2.5">
                    <li>
                        <button 
                            wire:click="$set('category', '')" 
                            class="w-full text-left text-sm flex items-center justify-between transition-colors {{ empty($category) ? 'text-primary font-bold' : 'text-theme-muted hover:text-theme-text' }}"
                        >
                            <span>All Categories</span>
                        </button>
                    </li>
                    @foreach($categories as $cat)
                        <li>
                            <button 
                                wire:click="$set('category', '{{ $cat->slug }}')" 
                                class="w-full text-left text-sm flex items-center justify-between transition-colors {{ $category === $cat->slug ? 'text-primary font-bold' : 'text-theme-muted hover:text-theme-text' }}"
                            >
                                <span>{{ $cat->name }}</span>
                                <span class="text-xs bg-secondary px-2 py-0.5 rounded-full text-theme-muted">{{ count($cat->products) }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Price Range Filter -->
            <div class="bg-theme-card border border-theme-border rounded-theme p-6 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-theme-text">Price Range</h3>
                <div class="space-y-4" x-data="{ min: @entangle('priceMin'), max: @entangle('priceMax') }">
                    <div class="flex items-center justify-between text-xs text-theme-muted">
                        <span>Min: ৳<span x-text="min"></span></span>
                        <span>Max: ৳<span x-text="max"></span></span>
                    </div>
                    <input 
                        type="range" 
                        min="0" 
                        max="50000" 
                        step="100"
                        x-model.debounce.500ms="max" 
                        class="w-full accent-primary bg-secondary h-2 rounded-lg cursor-pointer"
                    />
                </div>
            </div>
        </aside>

        <!-- Product Grid Area -->
        <section class="lg:col-span-3 space-y-6">
            <!-- Filter summary and sort bar -->
            <div class="bg-theme-card border border-theme-border rounded-theme p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-theme-muted">
                    Showing <span class="font-bold text-theme-text">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</span> of <span class="font-bold text-theme-text">{{ $products->total() }}</span> products
                </p>

                <!-- Sort option dropdown -->
                <div class="flex items-center gap-2">
                    <label class="text-xs text-theme-muted whitespace-nowrap">Sort By:</label>
                    <select 
                        wire:model.live="sort" 
                        class="bg-secondary border border-theme-border rounded-theme py-1.5 px-3 text-xs text-theme-text focus:outline-none focus:border-primary"
                    >
                        <option value="default">New Arrivals</option>
                        <option value="price_low_high">Price: Low to High</option>
                        <option value="price_high_low">Price: High to Low</option>
                        <option value="newest">Latest Added</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid with wire:loading style overlays -->
            <div class="relative min-h-[400px]">
                <div wire:loading.delay.long class="absolute inset-0 bg-theme-bg/60 backdrop-blur-sm z-10 flex items-center justify-center rounded-theme">
                    <div class="flex flex-col items-center gap-3">
                        <i class="fa-solid fa-spinner animate-spin text-4xl text-primary"></i>
                        <span class="text-xs font-semibold text-theme-muted">Updating grid...</span>
                    </div>
                </div>

                @if(count($products) > 0)
                    <div class="{{ $gridClass }}">
                        @foreach($products as $product)
                            <livewire:storefront.components.product-card :product="$product" :key="'shop-'.$product['id']" />
                        @endforeach
                    </div>
                    
                    <!-- Pagination Links -->
                    <div class="mt-12 pt-6 border-t border-theme-border flex items-center justify-center">
                        {{ $products->links() }}
                    </div>
                @else
                    <!-- No products container -->
                    <div class="bg-theme-card border border-theme-border rounded-theme p-12 text-center text-theme-muted max-w-md mx-auto mt-12">
                        <i class="fa-solid fa-circle-info text-4xl mb-4 text-primary"></i>
                        <h3 class="text-lg font-bold text-theme-text mb-2">No Products Found</h3>
                        <p class="text-sm">Try broadening your search or modifying the filters inside the sidebar.</p>
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>
