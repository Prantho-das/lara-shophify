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

                @if(filter_var($settings['enable_buy_now'] ?? true, FILTER_VALIDATE_BOOLEAN))
                    <button 
                        wire:click="buyNow" 
                        wire:loading.attr="disabled"
                        class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-3.5 px-6 rounded-theme flex-grow text-center text-sm transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-amber-500/20"
                    >
                        <span wire:loading.remove wire:target="buyNow">
                            <i class="fa-solid fa-bolt mr-1.5 animate-pulse"></i> Buy Now
                        </span>
                        <span wire:loading wire:target="buyNow" class="flex items-center justify-center gap-2">
                            <i class="fa-solid fa-spinner animate-spin"></i> Processing...
                        </span>
                    </button>
                @endif
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

    @if(filter_var($settings['enable_product_checkout'] ?? true, FILTER_VALIDATE_BOOLEAN))
        <!-- Direct Checkout Form Section -->
        <div class="mt-12 bg-theme-card border-2 border-primary/20 rounded-theme p-6 md:p-8 shadow-md">
            <div class="flex items-center gap-3 border-b border-theme-border pb-4 mb-6">
                <div class="p-2.5 bg-primary/10 text-primary rounded-lg">
                    <i class="fa-solid fa-truck-fast text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-theme-text">সরাসরি অর্ডার করতে ফর্মটি পূরণ করুন</h2>
                    <p class="text-xs text-theme-muted">Fill out the form below to order directly</p>
                </div>
            </div>

            <form wire:submit.prevent="placeDirectOrder" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Delivery Info -->
                <div class="space-y-4">
                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-primary border-l-4 border-primary pl-2 mb-4">Delivery Information</h3>
                    
                    <div>
                        <label class="block text-xs font-bold text-theme-text uppercase tracking-wider mb-2">আপনার নাম (Full Name) *</label>
                        <input type="text" wire:model.defer="customerName" placeholder="আপনার নাম লিখুন" class="w-full px-4 py-3 bg-secondary/35 border border-theme-border rounded-theme focus:ring-2 focus:ring-primary focus:border-transparent text-sm text-theme-text">
                        @error('customerName') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-theme-text uppercase tracking-wider mb-2">মোবাইল নম্বর (Phone Number) *</label>
                        <input type="text" wire:model.defer="customerPhone" placeholder="১১ ডিজিটের মোবাইল নম্বর লিখুন" class="w-full px-4 py-3 bg-secondary/35 border border-theme-border rounded-theme focus:ring-2 focus:ring-primary focus:border-transparent text-sm text-theme-text">
                        @error('customerPhone') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if($enableLocationShipping)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-2">
                            <div>
                                <label class="block text-xs font-bold text-theme-text uppercase tracking-wider mb-2">দেশ (Country) *</label>
                                <select wire:model.live="country" class="w-full px-3 py-3 bg-secondary/35 border border-theme-border rounded-theme focus:ring-2 focus:ring-primary focus:border-transparent text-xs text-theme-text">
                                    <option value="">Select Country</option>
                                    @foreach($countriesList as $name => $lbl)
                                        <option value="{{ $name }}">{{ $lbl }}</option>
                                    @endforeach
                                </select>
                                @error('country') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-theme-text uppercase tracking-wider mb-2">জেলা (District) *</label>
                                <select wire:model.live="district" class="w-full px-3 py-3 bg-secondary/35 border border-theme-border rounded-theme focus:ring-2 focus:ring-primary focus:border-transparent text-xs text-theme-text">
                                    <option value="">Select District</option>
                                    @foreach($districtsList as $name => $lbl)
                                        <option value="{{ $name }}">{{ $lbl }}</option>
                                    @endforeach
                                </select>
                                @error('district') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-theme-text uppercase tracking-wider mb-2">থানা/এলাকা (Area) *</label>
                                <select wire:model.live="area" class="w-full px-3 py-3 bg-secondary/35 border border-theme-border rounded-theme focus:ring-2 focus:ring-primary focus:border-transparent text-xs text-theme-text">
                                    <option value="">Select Area</option>
                                    @foreach($availableAreas as $ar)
                                        <option value="{{ $ar }}">{{ $ar }}</option>
                                    @endforeach
                                </select>
                                @error('area') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-theme-text uppercase tracking-wider mb-2">পূর্ণ ঠিকানা (Full Address) *</label>
                        <textarea rows="3" wire:model.defer="deliveryAddress" placeholder="গ্রাম, থানা, জেলা এবং রোড নং লিখুন" class="w-full px-4 py-3 bg-secondary/35 border border-theme-border rounded-theme focus:ring-2 focus:ring-primary focus:border-transparent text-sm text-theme-text"></textarea>
                        @error('deliveryAddress') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Order Calculation & Payment -->
                <div class="space-y-4 bg-secondary/30 p-6 rounded-theme border border-theme-border flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold uppercase tracking-wider text-primary border-l-4 border-primary pl-2 mb-4">Order Summary</h3>
                        
                        @if($enableShippingZones && count($shippingZones) > 0)
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-theme-text uppercase tracking-wider mb-2">ডেলিভারি এলাকা (Delivery Zone) *</label>
                                <select wire:model.live="shippingZoneId" class="w-full px-4 py-3 bg-theme-card border border-theme-border rounded-theme focus:ring-2 focus:ring-primary focus:border-transparent text-sm text-theme-text">
                                    @foreach($shippingZones as $zone)
                                        <option value="{{ $zone['id'] }}">{{ $zone['name'] }} (৳{{ number_format($zone['cost'], 2) }})</option>
                                    @endforeach
                                </select>
                                @error('shippingZoneId') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between text-theme-muted">
                                <span>Product Price:</span>
                                <span>৳{{ number_format($price, 2) }} x {{ $qty }}</span>
                            </div>
                            <div class="flex justify-between text-theme-muted">
                                <span>Delivery Charge:</span>
                                <span>৳{{ number_format($shippingCharge, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-base font-black text-theme-text border-t border-theme-border pt-2.5">
                                <span>Total Amount:</span>
                                <span class="text-primary">৳{{ number_format(($price * $qty) + $shippingCharge, 2) }}</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-xs font-bold text-theme-text uppercase tracking-wider mb-2.5">পেমেন্ট পদ্ধতি (Payment Method)</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label class="border-2 rounded-theme p-3 flex flex-col items-center justify-center gap-1.5 cursor-pointer transition-all duration-200 {{ $paymentMethod === 'cod' ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-theme-border text-theme-text' }}">
                                    <input type="radio" wire:model="paymentMethod" value="cod" class="hidden">
                                    <i class="fa-solid fa-truck-ramp-box text-lg"></i>
                                    <span class="text-xs">Cash on Delivery</span>
                                </label>

                                @if(!empty($settings['bkash_enabled']) && $settings['bkash_enabled'])
                                    <label class="border-2 rounded-theme p-3 flex flex-col items-center justify-center gap-1.5 cursor-pointer transition-all duration-200 {{ $paymentMethod === 'bkash' ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-theme-border text-theme-text' }}">
                                        <input type="radio" wire:model="paymentMethod" value="bkash" class="hidden">
                                        <i class="fa-solid fa-mobile-screen text-lg"></i>
                                        <span class="text-xs font-bold">bKash</span>
                                    </label>
                                @endif

                                @if(!empty($settings['nagad_enabled']) && $settings['nagad_enabled'])
                                    <label class="border-2 rounded-theme p-3 flex flex-col items-center justify-center gap-1.5 cursor-pointer transition-all duration-200 {{ $paymentMethod === 'nagad' ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-theme-border text-theme-text' }}">
                                        <input type="radio" wire:model="paymentMethod" value="nagad" class="hidden">
                                        <i class="fa-solid fa-mobile text-lg"></i>
                                        <span class="text-xs font-bold">Nagad</span>
                                    </label>
                                @endif
                            </div>
                            @error('paymentMethod') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 py-4 bg-primary text-white text-base font-black rounded-theme hover:bg-primary/95 transition-all duration-300 shadow-xl hover:shadow-primary/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-check animate-bounce"></i>
                        <span>কনফার্ম অর্ডার (Confirm Order)</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

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
