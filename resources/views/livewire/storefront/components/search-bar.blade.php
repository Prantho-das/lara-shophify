<div class="relative w-full">
    <form wire:submit.prevent="submitSearch" class="relative">
        <input 
            type="text" 
            wire:model.live.debounce.250ms="query"
            placeholder="Search products..." 
            class="search-input w-full bg-secondary text-theme-text placeholder-theme-muted pr-10"
        />
        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-theme-muted hover:text-primary transition-colors">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>

    <!-- Search Results Dropdown -->
    @if(!empty($results))
        <div class="absolute right-0 left-0 mt-2 bg-theme-card border border-theme-border rounded-theme shadow-2xl z-50 overflow-hidden max-h-80 overflow-y-auto">
            <div class="p-2 border-b border-theme-border text-xs font-semibold text-theme-muted">
                Search Results
            </div>
            <div class="divide-y divide-theme-border">
                @foreach($results as $product)
                    @php
                        $image = !empty($product['image_path']) ? $product['image_path'] : '';
                    @endphp
                    <a href="/product/{{ $product['slug'] ?? '' }}" wire:navigate class="flex items-center gap-3 p-3 hover:bg-secondary transition-colors">
                        @if($image)
                            <img src="{{ asset('storage/' . $image) }}" class="w-10 h-10 object-cover rounded-theme border border-theme-border" alt="{{ $product['name'] }}">
                        @else
                            <div class="w-10 h-10 bg-secondary flex items-center justify-center rounded-theme text-theme-muted">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        @endif
                        <div class="flex-grow">
                            <h4 class="text-sm font-medium text-theme-text line-clamp-1">{{ $product['name'] }}</h4>
                            <p class="text-xs text-primary font-bold">
                                ৳{{ number_format($product['price'], 2) }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="p-2 bg-secondary text-center">
                <a href="/shop?search={{ urlencode($query) }}" wire:navigate class="text-xs text-primary font-semibold hover:underline">
                    View all results
                </a>
            </div>
        </div>
    @endif
</div>
