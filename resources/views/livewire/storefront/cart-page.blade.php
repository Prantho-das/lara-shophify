<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 transition-colors duration-300">
    <!-- Breadcrumbs -->
    <nav class="flex text-xs text-theme-muted mb-8 gap-2">
        <a href="/" wire:navigate class="hover:text-primary transition-colors">Home</a>
        <span>/</span>
        <span class="text-theme-text font-semibold">Shopping Cart</span>
    </nav>

    <h1 class="text-3xl font-extrabold text-theme-text mb-8">Shopping Cart</h1>

    @if(session()->has('message'))
        <!-- Toast Notification -->
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-5 right-5 bg-primary text-white py-3 px-6 rounded-theme shadow-2xl z-50 flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if(count($cartItems) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Cart Items List -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-theme-card border border-theme-border rounded-theme overflow-hidden shadow-sm">
                    <div class="divide-y divide-theme-border">
                        @foreach($cartItems as $key => $item)
                            <div class="p-6 flex flex-col sm:flex-row items-center gap-6">
                                <!-- Image -->
                                @if(!empty($item['image']))
                                    <img src="{{ asset('storage/' . $item['image']) }}" class="w-20 h-20 object-contain bg-secondary rounded-theme p-1 border border-theme-border" alt="{{ $item['name'] }}">
                                @else
                                    <div class="w-20 h-20 bg-secondary flex items-center justify-center rounded-theme text-theme-muted">
                                        <i class="fa-solid fa-image text-xl"></i>
                                    </div>
                                @endif

                                <!-- Details -->
                                <div class="flex-grow text-center sm:text-left space-y-1">
                                    <h3 class="text-sm font-bold text-theme-text">{{ $item['name'] }}</h3>
                                    @if(!empty($item['variant_name']))
                                        <span class="inline-block text-[10px] bg-secondary text-primary font-bold px-2 py-0.5 rounded-sm">
                                            {{ $item['variant_name'] }}
                                        </span>
                                    @endif
                                    <p class="text-xs text-theme-muted font-bold">৳{{ number_format($item['price'], 2) }}</p>
                                </div>

                                <!-- Quantity Stepper -->
                                <div class="flex items-center border border-theme-border rounded-theme overflow-hidden bg-secondary">
                                    <button 
                                        wire:click="decrement('{{ $key }}')" 
                                        class="p-2 hover:text-primary transition-colors text-xs font-bold w-8 text-center"
                                    >-</button>
                                    <span class="w-8 text-center text-xs font-bold text-theme-text">{{ $item['qty'] }}</span>
                                    <button 
                                        wire:click="increment('{{ $key }}')" 
                                        class="p-2 hover:text-primary transition-colors text-xs font-bold w-8 text-center"
                                    >+</button>
                                </div>

                                <!-- Subtotal & Delete -->
                                <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-4">
                                    <p class="text-sm font-extrabold text-theme-text">
                                        ৳{{ number_format($item['price'] * $item['qty'], 2) }}
                                    </p>
                                    <button 
                                        wire:click="remove('{{ $key }}')" 
                                        class="text-xs text-red-500 hover:text-red-700 hover:underline flex items-center gap-1"
                                    >
                                        <i class="fa-regular fa-trash-can"></i> Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="space-y-6">
                <!-- Promo Code -->
                <div class="bg-theme-card border border-theme-border rounded-theme p-6 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-theme-text">Promo Code</h3>
                    @if($appliedCoupon)
                        <div class="bg-green-500/10 border border-green-500/30 rounded-theme p-3 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-green-600 font-bold block">Coupon Applied {{ $appliedCoupon['type'] === 'free_shipping' ? '(Free Shipping)' : '' }}</span>
                                <span class="text-sm font-black text-green-700">{{ $appliedCoupon['code'] }}</span>
                            </div>
                            <button wire:click="removeCoupon" class="text-xs text-red-500 hover:underline">Remove</button>
                        </div>
                    @else
                        <div class="flex gap-2">
                            <input 
                                type="text" 
                                wire:model="couponCode" 
                                placeholder="Enter coupon" 
                                class="flex-grow bg-secondary border border-theme-border rounded-theme px-3 py-2 text-sm text-theme-text focus:outline-none focus:border-primary"
                            />
                            <button 
                                wire:click="applyCoupon" 
                                class="btn-primary py-2 px-4 text-xs font-bold whitespace-nowrap"
                            >
                                Apply
                            </button>
                        </div>
                        @if($couponError)
                            <p class="text-[11px] text-red-500 font-semibold mt-1">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $couponError }}
                            </p>
                        @endif
                    @endif
                </div>

                <!-- Totals Summary -->
                <div class="bg-theme-card border border-theme-border rounded-theme p-6 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-theme-text border-b border-theme-border pb-3">Order Summary</h3>
                    
                    <div class="space-y-2.5">
                        <div class="flex justify-between text-sm text-theme-muted">
                            <span>Subtotal</span>
                            <span class="font-semibold text-theme-text">৳{{ number_format($subtotal, 2) }}</span>
                        </div>
                        @if($tax > 0)
                            <div class="flex justify-between text-sm text-theme-muted">
                                <span>Estimated Tax</span>
                                <span class="font-semibold text-theme-text">৳{{ number_format($tax, 2) }}</span>
                            </div>
                        @endif
                        @if($couponDiscount > 0)
                            <div class="flex justify-between text-sm text-green-600 font-semibold">
                                <span>Discount</span>
                                <span>-৳{{ number_format($couponDiscount, 2) }}</span>
                            </div>
                        @endif
                        
                        <div class="flex justify-between text-base font-black text-theme-text border-t border-theme-border pt-3">
                            <span>Total Price</span>
                            <span class="text-primary text-lg">৳{{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <a href="/checkout" wire:navigate class="btn-primary w-full text-center py-3.5 mt-4 text-sm font-bold tracking-wider">
                        Proceed to Checkout <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Cart Empty State -->
        <div class="bg-theme-card border border-theme-border rounded-theme p-12 text-center text-theme-muted max-w-md mx-auto mt-12 shadow-sm">
            <i class="fa-solid fa-basket-shopping text-5xl mb-4 text-primary"></i>
            <h3 class="text-lg font-bold text-theme-text mb-2">Your Cart is Empty</h3>
            <p class="text-sm mb-6">Looks like you haven't added any products to your cart yet.</p>
            <a href="/shop" wire:navigate class="btn-primary text-xs font-semibold py-3 px-6">
                Start Shopping
            </a>
        </div>
    @endif
</div>
