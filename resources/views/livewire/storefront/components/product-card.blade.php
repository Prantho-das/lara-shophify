<div class="product-card group relative bg-theme-card border border-theme-border rounded-theme overflow-hidden transition-all duration-500 hover:shadow-xl hover:shadow-primary/5 hover:border-primary/20 flex flex-col h-full">
    <!-- Image Gallery Section -->
    <div class="product-image-container relative bg-secondary aspect-square overflow-hidden flex items-center justify-center p-6 border-b border-theme-border">
        <a href="/product/{{ $product['slug'] }}" wire:navigate class="block w-full h-full">
            @if(!empty($product['images']) && count($product['images']) > 0)
                <img src="{{ asset('storage/' . $product['images'][0]['path']) }}" loading="lazy" class="product-image w-full h-full object-contain transition-transform duration-700 group-hover:scale-105" alt="{{ $product['name'] }}">
            @else
                <div class="w-full h-full flex items-center justify-center text-theme-muted">
                    <i class="fa-solid fa-image text-4xl opacity-50"></i>
                </div>
            @endif
        </a>

        <!-- Status Badges -->
        @if(($product['compare_price_display'] ?? 0) > ($product['selling_price'] ?? 0))
            @php
                $savings = (($product['compare_price_display'] - $product['selling_price']) / $product['compare_price_display']) * 100;
            @endphp
            <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full tracking-wider shadow-sm">
                Save {{ round($savings) }}%
            </span>
        @endif
    </div>

    <!-- Product Text Details -->
    <div class="p-5 flex flex-col flex-grow space-y-3">
        <!-- Brand -->
        @if(!empty($product['brand']))
            <span class="text-[10px] uppercase font-bold tracking-widest text-primary">{{ $product['brand']['name'] }}</span>
        @endif
        
        <!-- Title -->
        <h3 class="text-sm font-bold text-theme-text line-clamp-2 min-h-[40px] leading-snug">
            <a href="/product/{{ $product['slug'] }}" wire:navigate class="hover:text-primary transition-colors duration-300">
                {{ $product['name'] }}
            </a>
        </h3>

        <!-- Rating Section -->
        <div class="flex items-center gap-1.5 text-amber-500 text-xs">
            <div class="flex gap-0.5">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star-half-stroke"></i>
            </div>
            <span class="text-theme-muted text-[10px] font-bold">(4.5)</span>
        </div>

        <div class="mt-auto pt-4 border-t border-theme-border flex items-center justify-between gap-3">
            <!-- Pricing -->
            <div class="space-y-0.5">
                @if(!empty($product['compare_price_display']) && $product['compare_price_display'] > $product['selling_price'])
                    <p class="text-xs text-theme-muted line-through">
                        ৳{{ number_format($product['compare_price_display'], 2) }}
                    </p>
                @endif
                <p class="text-base font-extrabold text-primary tracking-tight">
                    ৳{{ number_format($product['selling_price'], 2) }}
                </p>
            </div>

            <!-- Cart button / Dynamic -->
            <button 
                wire:click="addToCart" 
                wire:loading.attr="disabled"
                class="btn-primary p-3 rounded-full hover:shadow-lg hover:shadow-primary/30 transition-all duration-300 flex items-center justify-center"
                title="Add to Cart"
            >
                <i wire:loading.remove wire:target="addToCart" class="fa-solid fa-cart-plus text-base"></i>
                <i wire:loading wire:target="addToCart" class="fa-solid fa-spinner animate-spin text-sm"></i>
            </button>
        </div>
    </div>
</div>
