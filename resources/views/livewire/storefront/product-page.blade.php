@php
    $storeTheme = $settings['store_theme'] ?? 'grocery';
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 transition-colors duration-300">
    <!-- Breadcrumbs -->
    <nav class="flex text-xs text-theme-muted mb-8 gap-2">
        <a href="/" wire:navigate class="hover:text-primary transition-colors">Home</a>
        <span>/</span>
        <a href="/shop" wire:navigate class="hover:text-primary transition-colors">Shop</a>
        <span>/</span>
        <span class="text-theme-text font-semibold">{{ $product->name }}</span>
    </nav>

    @if(session()->has('message'))
        <!-- Toast Notification -->
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-5 right-5 bg-primary text-white py-3 px-6 rounded-theme shadow-2xl z-50 flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 bg-theme-card border border-theme-border rounded-theme p-6 md:p-8 shadow-sm">
        <!-- Left Column: Image Gallery (Alpine.js integration with Premium Hover Magnifier Zoom) -->
        <div class="space-y-4" x-data="{ activeImg: '{{ $currentImage ? asset('storage/' . $currentImage) : 'https://placehold.co/600x600/f3f4f6/9ca3af?text=No+Image' }}' }">
            <div 
                class="aspect-square bg-secondary rounded-theme overflow-hidden border border-theme-border flex items-center justify-center p-4 relative cursor-zoom-in group"
                x-data="{ zoom: false, x: 0, y: 0 }"
                @mousemove="
                    zoom = true;
                    const rect = $el.getBoundingClientRect();
                    x = (($event.clientX - rect.left) / rect.width) * 100;
                    y = (($event.clientY - rect.top) / rect.height) * 100;
                "
                @mouseleave="zoom = false"
            >
                <img 
                    :src="activeImg" 
                    class="max-h-[450px] object-contain transition-transform duration-150 ease-out" 
                    :class="zoom ? 'scale-[2.2]' : 'scale-100'"
                    :style="zoom ? `transform-origin: ${x}% ${y}%` : ''"
                    alt="{{ $product->name }}"
                >
                <!-- Subtle Zoom Badge Indicator -->
                <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full pointer-events-none opacity-80 group-hover:opacity-0 transition-opacity">
                    <i class="fa-solid fa-magnifying-glass-plus mr-1"></i> Hover to Zoom
                </div>
            </div>
            
            <!-- Thumbnails -->
            @if(count($product->images) > 1)
                <div class="flex gap-3 overflow-x-auto py-2">
                    @foreach($product->images as $img)
                        <button 
                            @click="activeImg = '{{ asset('storage/' . $img->path) }}'" 
                            class="w-16 h-16 bg-secondary rounded-theme overflow-hidden border-2 transition-all p-1 flex items-center justify-center"
                            :class="activeImg.includes('{{ $img->path }}') ? 'border-primary' : 'border-theme-border'"
                        >
                            <img src="{{ asset('storage/' . $img->path) }}" class="object-cover w-full h-full" alt="Thumbnail">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right Column: Product Info -->
        <div class="flex flex-col space-y-6">
            <div class="space-y-2">
                @if($product->brand)
                    <span class="text-xs uppercase font-extrabold tracking-widest text-primary">{{ $product->brand->name }}</span>
                @endif
                <h1 class="text-2xl md:text-3xl font-extrabold text-theme-text">{{ $product->name }}</h1>
                
                @if($barcode)
                    <div class="flex items-center gap-2 text-xs text-theme-muted">
                        <i class="fa-solid fa-barcode"></i>
                        <span>Barcode: <strong>{{ $barcode }}</strong></span>
                    </div>
                @endif
            </div>

            <!-- Price Block -->
            <div class="bg-secondary p-4 rounded-theme flex items-center justify-between">
                <div>
                    <span class="text-xs text-theme-muted">Selling Price</span>
                    <p class="text-2xl font-black text-primary">৳{{ number_format($price, 2) }}</p>
                </div>
                @if($product->compare_price_display)
                    <div class="text-right">
                        <span class="text-xs text-theme-muted">Compare At</span>
                        <p class="text-lg text-theme-muted line-through">৳{{ number_format($product->compare_price_display, 2) }}</p>
                    </div>
                @endif
            </div>

            <!-- Variants selector -->
            @if($product->has_variants && count($product->variants) > 0)
                <div class="space-y-3">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-theme-text">Available Options:</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->variants as $variant)
                            @php
                                $attribs = is_array($variant->attribute_values) ? $variant->attribute_values : json_decode($variant->attribute_values, true);
                                $varLabel = implode(' / ', $attribs);
                            @endphp
                            <button 
                                wire:click="selectVariant({{ $variant->id }})"
                                class="border-2 rounded-theme py-2.5 px-4 text-xs font-semibold tracking-wide transition-all duration-200 {{ $selectedVariant === $variant->id ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-theme-border hover:border-theme-text text-theme-text' }}"
                            >
                                {{ $varLabel }}
                                <span class="block text-[10px] text-theme-muted">৳{{ number_format($variant->selling_price, 2) }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quantity & Add to cart row -->
            <div class="flex items-center gap-4 pt-4 border-t border-theme-border">
                <div class="flex items-center border border-theme-border rounded-theme overflow-hidden bg-secondary">
                    <button 
                        wire:click="$set('qty', {{ max(1, $qty - 1) }})" 
                        class="p-3 hover:text-primary transition-colors text-sm font-bold w-10 text-center"
                    >-</button>
                    <span class="w-12 text-center text-sm font-bold text-theme-text">{{ $qty }}</span>
                    <button 
                        wire:click="$set('qty', {{ $qty + 1 }})" 
                        class="p-3 hover:text-primary transition-colors text-sm font-bold w-10 text-center"
                    >+</button>
                </div>

                <button 
                    wire:click="addToCart" 
                    wire:loading.attr="disabled"
                    class="btn-primary flex-grow text-center text-sm font-bold py-3.5"
                >
                    <span wire:loading.remove wire:target="addToCart">
                        <i class="fa-solid fa-cart-shopping mr-2"></i> Add To Cart
                    </span>
                    <span wire:loading wire:target="addToCart" class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-spinner animate-spin"></i> Adding...
                    </span>
                </button>
            </div>

            <!-- Tax Rate info -->
            @if($product->tax_rate > 0)
                <p class="text-[11px] text-theme-muted italic">
                    * Price includes {{ $product->tax_rate }}% Tax.
                </p>
            @endif

            <!-- Rich Description -->
            <div class="prose max-w-none pt-4 border-t border-theme-border text-sm text-theme-muted">
                <h3 class="text-sm font-bold uppercase tracking-wider text-theme-text mb-3">Product Description</h3>
                {!! $product->description !!}
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if(count($relatedProducts) > 0)
        <section class="mt-16 space-y-6">
            <h2 class="text-2xl font-bold tracking-tight text-theme-text border-b border-theme-border pb-4">
                You May Also Like
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $rel)
                    <livewire:storefront.components.product-card :product="$rel" :key="'related-'.$rel->id" />
                @endforeach
            </div>
        </section>
    @endif
</div>
