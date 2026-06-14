@php
    $storeTheme = $settings['store_theme'] ?? 'grocery';
    
    // Theme dynamic grids
    $gridClass = 'grid grid-cols-2 gap-4 ';
    if ($storeTheme === 'grocery') {
        $gridClass .= 'sm:grid-cols-2 md:grid-cols-4 lg:gap-6';
    } else {
        $gridClass .= 'sm:grid-cols-2 md:grid-cols-4 lg:gap-8';
    }
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 transition-colors duration-300">
    <!-- Breadcrumbs -->
    <nav class="flex text-xs text-theme-muted mb-8 gap-2">
        <a href="/" wire:navigate class="hover:text-primary transition-colors">Home</a>
        <span>/</span>
        <a href="/shop" wire:navigate class="hover:text-primary transition-colors">Shop</a>
        <span>/</span>
        <span class="text-theme-text font-semibold">{{ $category->name }}</span>
    </nav>

    @if(session()->has('message'))
        <!-- Toast Notification -->
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-5 right-5 bg-primary text-white py-3 px-6 rounded-theme shadow-2xl z-50 flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Category Banner/Header Info -->
    <div class="bg-theme-card border border-theme-border rounded-theme p-8 md:p-12 mb-12 flex flex-col md:flex-row items-center gap-8 shadow-sm">
        @if(!empty($category->image))
            <img src="{{ asset('storage/' . $category->image) }}" class="w-24 h-24 md:w-32 md:h-32 object-cover rounded-full border border-theme-border" alt="{{ $category->name }}">
        @endif
        <div class="space-y-3 text-center md:text-left flex-grow">
            <h1 class="text-3xl font-extrabold text-theme-text">{{ $category->name }}</h1>
            @if(!empty($category->description))
                <p class="text-sm text-theme-muted max-w-2xl">{{ $category->description }}</p>
            @endif
        </div>
    </div>

    <!-- Product Grid Area -->
    <div class="relative min-h-[300px]">
        @if(count($products) > 0)
            <div class="{{ $gridClass }}">
                @foreach($products as $product)
                    <livewire:storefront.components.product-card :product="$product" :key="'cat-prod-'.$product['id']" />
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-12 pt-6 border-t border-theme-border flex items-center justify-center">
                {{ $products->links() }}
            </div>
        @else
            <!-- No products in category -->
            <div class="bg-theme-card border border-theme-border rounded-theme p-12 text-center text-theme-muted max-w-md mx-auto mt-12">
                <i class="fa-solid fa-folder-open text-4xl mb-4 text-primary"></i>
                <h3 class="text-lg font-bold text-theme-text mb-2">No Products in Category</h3>
                <p class="text-sm">We are currently updating products in this category. Please check back later!</p>
            </div>
        @endif
    </div>
</div>
