<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 transition-colors duration-300">
    <!-- Breadcrumbs -->
    <nav class="flex text-xs text-theme-muted mb-8 gap-2">
        <a href="/" wire:navigate class="hover:text-primary transition-colors">Home</a>
        <span>/</span>
        <a href="/cart" wire:navigate class="hover:text-primary transition-colors">Cart</a>
        <span>/</span>
        <span class="text-theme-text font-semibold">Checkout</span>
    </nav>

    @if($orderPlaced)
        <!-- Success State Overlay Screen -->
        <div class="bg-theme-card border border-theme-border rounded-theme p-8 md:p-12 text-center max-w-2xl mx-auto shadow-2xl space-y-6">
            <div class="w-16 h-16 bg-green-500/10 border border-green-500 text-green-500 rounded-full flex items-center justify-center text-3xl mx-auto animate-bounce">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            
            <div class="space-y-2">
                <h1 class="text-3xl font-extrabold text-theme-text">Order Placed Successfully!</h1>
                <p class="text-sm text-theme-muted">Thank you for shopping with us. Your order ID is <strong class="text-primary">#{{ $placedOrderId }}</strong>.</p>
            </div>

            @if(in_array($paymentMethod, ['bkash', 'nagad']))
                <div class="bg-primary/5 border border-primary/20 rounded-theme p-6 text-left space-y-3">
                    <h3 class="text-sm font-bold text-theme-text uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-mobile-screen-button text-primary"></i> 
                        Mobile Payment Instructions
                    </h3>
                    <p class="text-xs text-theme-muted">
                        Please send <strong>৳{{ number_format($total, 2) }}</strong> to our official account and call support.
                    </p>
                    <div class="divide-y divide-theme-border text-xs">
                        @if($paymentMethod === 'bkash')
                            <div class="py-2 flex justify-between">
                                <span>bKash Number:</span>
                                <strong class="text-primary">{{ $settings['bkash_number'] ?? 'Not Configured' }}</strong>
                            </div>
                        @elseif($paymentMethod === 'nagad')
                            <div class="py-2 flex justify-between">
                                <span>Nagad Number:</span>
                                <strong class="text-primary">{{ $settings['nagad_number'] ?? 'Not Configured' }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="pt-6">
                <a href="/shop" wire:navigate class="btn-primary py-3 px-8 font-bold text-sm tracking-wider">
                    Continue Shopping
                </a>
            </div>
        </div>
    @else
        <!-- Checkout Form Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Panel: Delivery Information -->
            <div class="lg:col-span-2 bg-theme-card border border-theme-border rounded-theme p-6 md:p-8 shadow-sm space-y-6">
                <h2 class="text-xl font-bold text-theme-text border-b border-theme-border pb-4">
                    Delivery Details
                </h2>

                <form wire:submit.prevent="placeOrder" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-theme-text">Full Name *</label>
                            <input 
                                type="text" 
                                wire:model="customerName" 
                                class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                                placeholder="John Doe"
                            />
                            @error('customerName') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-theme-text">Phone Number *</label>
                            <input 
                                type="text" 
                                wire:model="customerPhone" 
                                class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                                placeholder="017xxxxxxxx"
                            />
                            @error('customerPhone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-theme-text">Email Address (Optional)</label>
                        <input 
                            type="email" 
                            wire:model="customerEmail" 
                            class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                            placeholder="email@example.com"
                        />
                        @error('customerEmail') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-theme-text">Shipping Address *</label>
                        <textarea 
                            wire:model="deliveryAddress" 
                            rows="3"
                            class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                            placeholder="Detailed shipping address details..."
                        ></textarea>
                        @error('deliveryAddress') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    @if($enableShippingZones)
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-theme-text">Shipping Zone *</label>
                            <select 
                                wire:model.live="shippingZoneId"
                                class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                            >
                                <option value="">Select Shipping Zone</option>
                                @foreach($shippingZones as $zone)
                                    <option value="{{ $zone['id'] }}">{{ $zone['name'] }} (৳{{ number_format($zone['cost'], 2) }})</option>
                                @endforeach
                            </select>
                            @error('shippingZoneId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    @if($enableLocationShipping)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-theme-text">Country *</label>
                                <select 
                                    wire:model.live="country"
                                    class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                                >
                                    <option value="">Select Country</option>
                                    @foreach($countriesList as $cName)
                                        <option value="{{ $cName }}">{{ $cName }}</option>
                                    @endforeach
                                </select>
                                @error('country') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-theme-text">District *</label>
                                <select 
                                    wire:model.live="district"
                                    class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                                >
                                    <option value="">Select District</option>
                                    @foreach($districtsList as $dist)
                                        <option value="{{ $dist }}">{{ $dist }}</option>
                                    @endforeach
                                </select>
                                @error('district') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-theme-text">Area / Police Station *</label>
                                <select 
                                    wire:model.live="area"
                                    class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                                >
                                    <option value="">Select Area</option>
                                    @foreach($availableAreas as $ar)
                                        <option value="{{ $ar }}">{{ $ar }}</option>
                                    @endforeach
                                </select>
                                @error('area') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    @if(!$enableShippingZones && !$enableLocationShipping)
                        <div class="bg-secondary p-4 rounded-theme border border-theme-border text-xs text-theme-muted">
                            <i class="fa-solid fa-circle-info text-primary mr-1"></i> Dynamic shipping configurations disabled. Fallback shipping charge of <strong>৳{{ number_format($defaultShippingCost, 2) }}</strong> applies.
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-theme-text">Order Notes (Optional)</label>
                        <textarea 
                            wire:model="notes" 
                            rows="2"
                            class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                            placeholder="Additional instructions..."
                        ></textarea>
                    </div>

                    <!-- Payment Gateway Selection -->
                    <div class="space-y-4 pt-6 border-t border-theme-border">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-theme-text">Select Payment Method</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <!-- COD Option -->
                            <label class="border-2 rounded-theme p-4 flex flex-col items-center justify-center gap-2 cursor-pointer transition-all duration-200 {{ $paymentMethod === 'cod' ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-theme-border text-theme-text' }}">
                                <input type="radio" wire:model="paymentMethod" value="cod" class="hidden" />
                                <i class="fa-solid fa-truck-ramp-box text-xl"></i>
                                <span class="text-xs">Cash on Delivery</span>
                            </label>

                            <!-- bKash Option -->
                            @if(!empty($settings['bkash_enabled']) && $settings['bkash_enabled'])
                                <label class="border-2 rounded-theme p-4 flex flex-col items-center justify-center gap-2 cursor-pointer transition-all duration-200 {{ $paymentMethod === 'bkash' ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-theme-border text-theme-text' }}">
                                    <input type="radio" wire:model="paymentMethod" value="bkash" class="hidden" />
                                    <i class="fa-solid fa-mobile-screen text-xl"></i>
                                    <span class="text-xs">bKash</span>
                                </label>
                            @endif

                            <!-- Nagad Option -->
                            @if(!empty($settings['nagad_enabled']) && $settings['nagad_enabled'])
                                <label class="border-2 rounded-theme p-4 flex flex-col items-center justify-center gap-2 cursor-pointer transition-all duration-200 {{ $paymentMethod === 'nagad' ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-theme-border text-theme-text' }}">
                                    <input type="radio" wire:model="paymentMethod" value="nagad" class="hidden" />
                                    <i class="fa-solid fa-mobile text-xl"></i>
                                    <span class="text-xs">Nagad</span>
                                </label>
                            @endif
                        </div>
                        @error('paymentMethod') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="btn-primary w-full text-center py-4 text-sm font-bold tracking-wider mt-6"
                    >
                        <span wire:loading.remove>
                            Place Order (৳{{ number_format($total, 2) }})
                        </span>
                        <span wire:loading class="flex items-center justify-center gap-2">
                            <i class="fa-solid fa-spinner animate-spin"></i> Processing Order...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Right Panel: Order Summary overview -->
            <div class="space-y-6 lg:col-span-1">
                <div class="bg-theme-card border border-theme-border rounded-theme p-6 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-theme-text border-b border-theme-border pb-3">Items Overview</h3>
                    
                    <div class="max-h-60 overflow-y-auto divide-y divide-theme-border">
                        @foreach($cartItems as $item)
                            <div class="py-3.5 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="bg-secondary px-2 py-0.5 rounded-full text-theme-muted font-bold">{{ $item['qty'] }}x</span>
                                    <div>
                                        <p class="font-semibold text-theme-text line-clamp-1">{{ $item['name'] }}</p>
                                        @if($item['variant_name'])
                                            <span class="text-[9px] text-primary">{{ $item['variant_name'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="font-bold text-theme-text">৳{{ number_format($item['price'] * $item['qty'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-theme-border pt-4 space-y-2.5">
                        <div class="flex justify-between text-xs text-theme-muted">
                            <span>Subtotal</span>
                            <span class="font-semibold text-theme-text">৳{{ number_format($subtotal, 2) }}</span>
                        </div>
                        @if($tax > 0)
                            <div class="flex justify-between text-xs text-theme-muted">
                                <span>Taxes</span>
                                <span class="font-semibold text-theme-text">৳{{ number_format($tax, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-xs text-theme-muted">
                            <span>Shipping Charge</span>
                            <span class="font-semibold text-theme-text">
                                @if($shippingCharge > 0)
                                    ৳{{ number_format($shippingCharge, 2) }}
                                @elseif(session()->has('coupon') && session('coupon')['type'] === 'free_shipping')
                                    <span class="text-green-600 font-bold">Free (Coupon Applied)</span>
                                @else
                                    Select Shipping Area
                                @endif
                            </span>
                        </div>
                        @if($couponDiscount > 0)
                            <div class="flex justify-between text-xs text-green-600 font-semibold">
                                <span>Discount</span>
                                <span>-৳{{ number_format($couponDiscount, 2) }}</span>
                            </div>
                        @endif
                        
                        <div class="flex justify-between text-sm font-black text-theme-text border-t border-theme-border pt-3">
                            <span>Grand Total</span>
                            <span class="text-primary text-base">৳{{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
