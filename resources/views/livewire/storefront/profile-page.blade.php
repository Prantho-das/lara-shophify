<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 transition-colors duration-300">
    @if(session()->has('message'))
        <!-- Toast Notification -->
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-5 right-5 bg-primary text-white py-3 px-6 rounded-theme shadow-2xl z-50 flex items-center gap-2 transition-all">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-theme-card border border-theme-border rounded-theme p-6 shadow-sm text-center">
                <div class="w-20 h-20 bg-secondary rounded-full flex items-center justify-center mx-auto text-primary font-bold text-3xl mb-4 border border-primary/10">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <h3 class="text-lg font-black text-theme-text">{{ auth()->user()->name }}</h3>
                <p class="text-xs text-theme-muted mt-1">{{ auth()->user()->email }}</p>
                <div class="text-[10px] uppercase font-bold tracking-widest text-primary bg-secondary px-3 py-1 rounded-full w-fit mx-auto mt-3 border border-primary/5">
                    Customer Account
                </div>
            </div>

            <!-- Tab Menu -->
            <nav class="bg-theme-card border border-theme-border rounded-theme overflow-hidden shadow-sm flex flex-row lg:flex-col justify-around lg:justify-start">
                <button wire:click="selectTab('dashboard')" class="flex items-center justify-center lg:justify-start gap-3 px-5 py-4 text-sm font-bold border-b-2 lg:border-b-0 lg:border-l-4 transition-all w-full text-center lg:text-left {{ $activeTab === 'dashboard' ? 'border-primary bg-secondary/40 text-primary' : 'border-transparent text-theme-muted hover:bg-secondary/20 hover:text-theme-text' }}">
                    <i class="fa-solid fa-chart-line text-base"></i>
                    <span class="hidden sm:inline">Dashboard</span>
                </button>
                <button wire:click="selectTab('orders')" class="flex items-center justify-center lg:justify-start gap-3 px-5 py-4 text-sm font-bold border-b-2 lg:border-b-0 lg:border-l-4 transition-all w-full text-center lg:text-left {{ $activeTab === 'orders' ? 'border-primary bg-secondary/40 text-primary' : 'border-transparent text-theme-muted hover:bg-secondary/20 hover:text-theme-text' }}">
                    <i class="fa-solid fa-box text-base"></i>
                    <span class="hidden sm:inline">My Orders</span>
                </button>
                <button wire:click="selectTab('profile')" class="flex items-center justify-center lg:justify-start gap-3 px-5 py-4 text-sm font-bold border-b-2 lg:border-b-0 lg:border-l-4 transition-all w-full text-center lg:text-left {{ $activeTab === 'profile' ? 'border-primary bg-secondary/40 text-primary' : 'border-transparent text-theme-muted hover:bg-secondary/20 hover:text-theme-text' }}">
                    <i class="fa-regular fa-id-card text-base"></i>
                    <span class="hidden sm:inline">Edit Details</span>
                </button>
                <button wire:click="selectTab('security')" class="flex items-center justify-center lg:justify-start gap-3 px-5 py-4 text-sm font-bold border-b-2 lg:border-b-0 lg:border-l-4 transition-all w-full text-center lg:text-left {{ $activeTab === 'security' ? 'border-primary bg-secondary/40 text-primary' : 'border-transparent text-theme-muted hover:bg-secondary/20 hover:text-theme-text' }}">
                    <i class="fa-solid fa-lock text-base"></i>
                    <span class="hidden sm:inline">Security</span>
                </button>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-3">
            @if($activeTab === 'dashboard')
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-theme-card border border-theme-border rounded-theme p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
                        <span class="text-xs font-bold text-theme-muted uppercase tracking-wider">Total Spent</span>
                        <span class="text-2xl font-black text-primary mt-2">৳{{ number_format($stats['total_spent'], 2) }}</span>
                    </div>
                    <div class="bg-theme-card border border-theme-border rounded-theme p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
                        <span class="text-xs font-bold text-theme-muted uppercase tracking-wider">Orders Placed</span>
                        <span class="text-2xl font-black text-theme-text mt-2">{{ $stats['total_orders'] }}</span>
                    </div>
                    <div class="bg-theme-card border border-theme-border rounded-theme p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
                        <span class="text-xs font-bold text-theme-muted uppercase tracking-wider">Active Pending</span>
                        <span class="text-2xl font-black text-amber-500 mt-2">{{ $stats['pending_orders'] }}</span>
                    </div>
                    <div class="bg-theme-card border border-theme-border rounded-theme p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
                        <span class="text-xs font-bold text-theme-muted uppercase tracking-wider">Delivered</span>
                        <span class="text-2xl font-black text-emerald-500 mt-2">{{ $stats['delivered_orders'] }}</span>
                    </div>
                </div>

                <!-- Recent Orders Summary -->
                <div class="bg-theme-card border border-theme-border rounded-theme shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-theme-border flex items-center justify-between bg-secondary/15">
                        <h4 class="text-base font-black text-theme-text">Recent Orders</h4>
                        <button wire:click="selectTab('orders')" class="text-xs font-bold text-primary hover:underline">View All</button>
                    </div>
                    @if(count($orders) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="bg-secondary/10 text-theme-muted uppercase text-[10px] font-bold tracking-wider">
                                        <th class="px-6 py-4">Order ID</th>
                                        <th class="px-6 py-4">Date</th>
                                        <th class="px-6 py-4">Amount</th>
                                        <th class="px-6 py-4">Status</th>
                                        <th class="px-6 py-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-theme-border/60">
                                    @foreach($orders->take(3) as $order)
                                        <tr class="hover:bg-secondary/5 transition-colors">
                                            <td class="px-6 py-4 font-bold text-theme-text">#{{ $order->id }}</td>
                                            <td class="px-6 py-4 text-theme-muted">{{ $order->created_at->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 font-extrabold text-theme-text">৳{{ number_format($order->total_amount, 2) }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800 border border-blue-200
                                                    @elseif($order->status === 'shipped') bg-indigo-100 text-indigo-800 border border-indigo-200
                                                    @elseif($order->status === 'delivered') bg-emerald-100 text-emerald-800 border border-emerald-200
                                                    @else bg-red-100 text-red-800 border border-red-200 @endif
                                                ">
                                                    {{ $order->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <button wire:click="selectTab('orders')" class="text-xs font-extrabold text-primary bg-secondary px-3 py-1.5 rounded-theme border border-primary/10 hover:bg-primary hover:text-white transition-all">Details</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-8 text-center text-theme-muted text-sm">
                            <i class="fa-solid fa-basket-shopping text-3xl mb-3 block text-primary/30"></i>
                            No orders found.
                        </div>
                    @endif
                </div>

            @elseif($activeTab === 'orders')
                <!-- Dynamic Expandable Orders Panel -->
                <div class="space-y-6">
                    <h4 class="text-xl font-black text-theme-text border-b border-theme-border pb-4">My Purchase History</h4>
                    
                    @if(count($orders) > 0)
                        @foreach($orders as $order)
                            <div x-data="{ open: false }" class="bg-theme-card border border-theme-border rounded-theme shadow-sm overflow-hidden hover:border-primary/20 transition-all duration-300">
                                <!-- Order Header Clickable Accordion -->
                                <div @click="open = !open" class="px-6 py-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 cursor-pointer hover:bg-secondary/10 select-none transition-colors">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-3">
                                            <span class="text-base font-black text-theme-text">Order #{{ $order->id }}</span>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                                @elseif($order->status === 'processing') bg-blue-100 text-blue-800 border border-blue-200
                                                @elseif($order->status === 'shipped') bg-indigo-100 text-indigo-800 border border-indigo-200
                                                @elseif($order->status === 'delivered') bg-emerald-100 text-emerald-800 border border-emerald-200
                                                @else bg-red-100 text-red-800 border border-red-200 @endif
                                            ">
                                                {{ $order->status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-theme-muted">Placed on {{ $order->created_at->format('F d, Y - h:i A') }}</p>
                                    </div>
                                    <div class="flex items-center gap-6">
                                        <div class="text-left sm:text-right">
                                            <span class="block text-[10px] font-bold text-theme-muted uppercase tracking-wider">Total Value</span>
                                            <span class="text-base font-black text-primary">৳{{ number_format($order->total_amount, 2) }}</span>
                                        </div>
                                        <div class="text-theme-muted transform transition-transform duration-300" :class="open ? 'rotate-180 text-primary' : ''">
                                            <i class="fa-solid fa-chevron-down text-sm"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Accordion Content (Items + Progress Stepper) -->
                                <div x-show="open" x-collapse x-cloak class="border-t border-theme-border bg-secondary/5 px-6 py-6 space-y-8">
                                    <!-- Visual Progress Stepper -->
                                    <div class="py-4 max-w-xl mx-auto">
                                        <div class="flex items-center justify-between relative">
                                            <!-- Line Connectors -->
                                            <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-1 bg-theme-border z-0"></div>
                                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-primary z-0 transition-all duration-500" 
                                                 style="width: @if($order->status === 'pending') 0% @elseif($order->status === 'processing') 33.3% @elseif($order->status === 'shipped') 66.6% @elseif($order->status === 'delivered') 100% @else 0% @endif"></div>

                                            <!-- Step 1: Pending -->
                                            <div class="relative z-10 flex flex-col items-center">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-sm transition-all duration-300
                                                    @if($order->status === 'cancelled') bg-red-500 text-white
                                                    @else bg-primary text-white @endif
                                                ">
                                                    @if($order->status === 'cancelled') <i class="fa-solid fa-xmark"></i> @else <i class="fa-solid fa-clock"></i> @endif
                                                </div>
                                                <span class="text-[10px] font-bold mt-2 uppercase tracking-wider @if($order->status === 'cancelled') text-red-500 @else text-primary @endif">
                                                    @if($order->status === 'cancelled') Cancelled @else Pending @endif
                                                </span>
                                            </div>

                                            <!-- Step 2: Processing -->
                                            <div class="relative z-10 flex flex-col items-center">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-sm transition-all duration-300
                                                    @if(in_array($order->status, ['processing', 'shipped', 'delivered'])) bg-primary text-white @else bg-theme-card border border-theme-border text-theme-muted @endif
                                                ">
                                                    <i class="fa-solid fa-gear"></i>
                                                </div>
                                                <span class="text-[10px] font-bold mt-2 uppercase tracking-wider @if(in_array($order->status, ['processing', 'shipped', 'delivered'])) text-primary @else text-theme-muted @endif">Processing</span>
                                            </div>

                                            <!-- Step 3: Shipped -->
                                            <div class="relative z-10 flex flex-col items-center">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-sm transition-all duration-300
                                                    @if(in_array($order->status, ['shipped', 'delivered'])) bg-primary text-white @else bg-theme-card border border-theme-border text-theme-muted @endif
                                                ">
                                                    <i class="fa-solid fa-truck-fast"></i>
                                                </div>
                                                <span class="text-[10px] font-bold mt-2 uppercase tracking-wider @if(in_array($order->status, ['shipped', 'delivered'])) text-primary @else text-theme-muted @endif">Shipped</span>
                                            </div>

                                            <!-- Step 4: Delivered -->
                                            <div class="relative z-10 flex flex-col items-center">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-sm transition-all duration-300
                                                    @if($order->status === 'delivered') bg-primary text-white @else bg-theme-card border border-theme-border text-theme-muted @endif
                                                ">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                </div>
                                                <span class="text-[10px] font-bold mt-2 uppercase tracking-wider @if($order->status === 'delivered') text-primary @else text-theme-muted @endif">Delivered</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Order Items -->
                                    <div class="bg-theme-card border border-theme-border rounded-theme overflow-hidden shadow-inner">
                                        <table class="w-full text-left text-sm">
                                            <thead>
                                                <tr class="bg-secondary/15 text-theme-muted uppercase text-[9px] font-bold tracking-wider border-b border-theme-border">
                                                    <th class="px-5 py-3">Product</th>
                                                    <th class="px-5 py-3 text-center">Price</th>
                                                    <th class="px-5 py-3 text-center">Qty</th>
                                                    <th class="px-5 py-3 text-right">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-theme-border/50">
                                                @foreach($order->orderItems as $item)
                                                    <tr>
                                                        <td class="px-5 py-4">
                                                            <div class="flex items-center gap-3">
                                                                @if($item->product->images->first())
                                                                    <img src="{{ asset('storage/' . $item->product->images->first()->path) }}" class="w-10 h-10 rounded-theme object-cover border border-theme-border shrink-0" alt="{{ $item->product->name }}">
                                                                @else
                                                                    <div class="w-10 h-10 rounded-theme bg-secondary flex items-center justify-center text-primary shrink-0">
                                                                        <i class="fa-solid fa-image text-xs"></i>
                                                                    </div>
                                                                @endif
                                                                <div>
                                                                    <span class="block font-bold text-theme-text line-clamp-1 hover:text-primary transition-colors">{{ $item->product->name }}</span>
                                                                    <!-- Variant Detail Rendering -->
                                                                    @if($item->variant_id && $item->variant)
                                                                        @php
                                                                            $varAttrs = is_array($item->variant->attribute_values) ? $item->variant->attribute_values : json_decode($item->variant->attribute_values ?? '[]', true);
                                                                            $varString = implode(', ', array_map(function($k, $v) {
                                                                                return ucfirst($k) . ': ' . $v;
                                                                            }, array_keys($varAttrs), $varAttrs));
                                                                        @endphp
                                                                        <span class="inline-block bg-secondary text-primary font-extrabold text-[10px] rounded px-2 py-0.5 mt-0.5 uppercase tracking-wide border border-primary/10">
                                                                            {{ $varString }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-5 py-4 text-center font-medium text-theme-text">৳{{ number_format($item->unit_price, 2) }}</td>
                                                        <td class="px-5 py-4 text-center font-bold text-theme-text">x{{ $item->quantity }}</td>
                                                        <td class="px-5 py-4 text-right font-extrabold text-theme-text">৳{{ number_format($item->total, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <!-- Footer Totals -->
                                        <div class="bg-secondary/10 px-5 py-4 border-t border-theme-border space-y-1.5 text-xs text-right">
                                            <div class="flex justify-between items-center max-w-xs ml-auto">
                                                <span class="text-theme-muted">Shipping Charge:</span>
                                                <span class="font-bold text-theme-text">৳{{ number_format($order->shipping_charge, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between items-center max-w-xs ml-auto text-sm font-black border-t border-theme-border/60 pt-1.5 mt-1">
                                                <span class="text-theme-text">Grand Total:</span>
                                                <span class="text-primary text-base">৳{{ number_format($order->total_amount, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Delivery Info -->
                                    <div class="bg-theme-card border border-theme-border rounded-theme p-5 text-xs text-theme-muted space-y-1">
                                        <h5 class="font-extrabold text-theme-text uppercase tracking-wider mb-2">Shipping Details & Address</h5>
                                        <p class="whitespace-pre-line leading-relaxed">{{ $order->shipping_address }}</p>
                                        <p class="pt-2">Payment Method: <span class="font-bold uppercase text-theme-text">{{ $order->payment_method }}</span> | Status: <span class="font-bold uppercase text-theme-text">{{ $order->payment_status }}</span></p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="bg-theme-card border border-theme-border rounded-theme p-12 text-center text-theme-muted shadow-sm">
                            <i class="fa-solid fa-box-open text-4xl text-primary/30 mb-4 block animate-bounce"></i>
                            You have not placed any orders yet. 
                            <a href="/shop" wire:navigate class="text-primary font-bold hover:underline ml-1">Go Shopping <i class="fa-solid fa-arrow-right text-[10px]"></i></a>
                        </div>
                    @endif
                </div>

            @elseif($activeTab === 'profile')
                <!-- Profile Edit Form -->
                <div class="bg-theme-card border border-theme-border rounded-theme p-8 shadow-sm space-y-6">
                    <h4 class="text-xl font-black text-theme-text border-b border-theme-border pb-4">Update Profile details</h4>
                    
                    <form wire:submit="updateProfile" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-theme-text" for="profile-name">Full Name</label>
                                <input wire:model="name" id="profile-name" type="text" required class="w-full bg-secondary border border-theme-border rounded-theme px-4 py-3 text-sm text-theme-text focus:outline-none focus:border-primary shadow-inner">
                                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-theme-text" for="profile-email">Email Address</label>
                                <input wire:model="email" id="profile-email" type="email" required class="w-full bg-secondary border border-theme-border rounded-theme px-4 py-3 text-sm text-theme-text focus:outline-none focus:border-primary shadow-inner">
                                @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <button type="submit" wire:loading.attr="disabled" class="btn-primary px-8 py-3 text-sm font-bold tracking-wider">
                            <span wire:loading.remove>Save Profile</span>
                            <span wire:loading><i class="fa-solid fa-spinner animate-spin"></i> Saving...</span>
                        </button>
                    </form>
                </div>

            @elseif($activeTab === 'security')
                <!-- Security Change Password Form -->
                <div class="bg-theme-card border border-theme-border rounded-theme p-8 shadow-sm space-y-6">
                    <h4 class="text-xl font-black text-theme-text border-b border-theme-border pb-4">Update Password</h4>
                    
                    <form wire:submit="updatePassword" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-theme-text" for="current-password">Current Password</label>
                                <input wire:model="current_password" id="current-password" type="password" required class="w-full bg-secondary border border-theme-border rounded-theme px-4 py-3 text-sm text-theme-text focus:outline-none focus:border-primary shadow-inner">
                                @error('current_password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-theme-text" for="new-password">New Password</label>
                                <input wire:model="password" id="new-password" type="password" required class="w-full bg-secondary border border-theme-border rounded-theme px-4 py-3 text-sm text-theme-text focus:outline-none focus:border-primary shadow-inner">
                                @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-theme-text" for="confirm-password">Confirm New Password</label>
                                <input wire:model="password_confirmation" id="confirm-password" type="password" required class="w-full bg-secondary border border-theme-border rounded-theme px-4 py-3 text-sm text-theme-text focus:outline-none focus:border-primary shadow-inner">
                                @error('password_confirmation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <button type="submit" wire:loading.attr="disabled" class="btn-primary px-8 py-3 text-sm font-bold tracking-wider">
                            <span wire:loading.remove>Update Password</span>
                            <span wire:loading><i class="fa-solid fa-spinner animate-spin"></i> Processing...</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
