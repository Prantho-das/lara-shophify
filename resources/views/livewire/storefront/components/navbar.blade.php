<header class="store-header {{ ($settings['header_style'] ?? 'normal') === 'sticky' ? 'sticky top-0 z-50 backdrop-blur-md bg-theme-bg/95' : 'relative bg-theme-bg' }} shadow-sm transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo Section -->
            <div class="flex-shrink-0 flex items-center">
                <a href="/" wire:navigate class="flex items-center gap-2">
                    @if(!empty($settings['store_logo']))
                        <img class="h-10 w-auto object-contain" src="{{ asset('storage/' . $settings['store_logo']) }}" alt="{{ $settings['store_name'] ?? 'Store' }}">
                    @else
                        <span class="text-2xl font-bold tracking-tight text-primary">{{ $settings['store_name'] ?? 'Antigravity Store' }}</span>
                    @endif
                </a>
            </div>

            <!-- Middle Navigation Links (Desktop) -->
            <nav class="hidden md:flex space-x-8">
                <a href="/" wire:navigate class="text-sm font-medium hover:text-primary transition-colors py-2">Home</a>
                <a href="/shop" wire:navigate class="text-sm font-medium hover:text-primary transition-colors py-2">Shop</a>
                @foreach($menuItems as $item)
                    @php
                        $resolvedUrl = $item->url;
                        if (($item->type ?? 'custom') === 'category' && $item->category) {
                            $resolvedUrl = '/category/' . $item->category->slug;
                        } elseif (($item->type ?? 'custom') === 'brand' && $item->brand) {
                            $resolvedUrl = '/shop?brand=' . $item->brand->slug;
                        }
                    @endphp
                    <a href="{{ $resolvedUrl }}" wire:navigate class="text-sm font-medium hover:text-primary transition-colors py-2">{{ $item->title }}</a>
                @endforeach
            </nav>

            <!-- Search and Action Icons -->
            <div class="flex items-center gap-4">
                <!-- Search Bar Integration -->
                <div class="hidden sm:block w-64">
                    <livewire:storefront.components.search-bar />
                </div>

                <!-- Dark Mode Toggle Button -->
                <button 
                    id="theme-toggle" 
                    type="button" 
                    class="p-2 text-theme-text hover:text-primary transition-colors focus:outline-none"
                    onclick="toggleDarkMode()"
                    title="Toggle Light/Dark Theme"
                >
                    <i id="theme-toggle-dark-icon" class="fa-solid fa-moon text-lg hidden"></i>
                    <i id="theme-toggle-light-icon" class="fa-solid fa-sun text-lg hidden"></i>
                </button>

                <!-- Shopping Cart Icon -->
                <a href="/cart" wire:navigate class="relative p-2 text-theme-text hover:text-primary transition-colors">
                    <i class="fa-solid fa-bag-shopping text-xl"></i>
                    @if($cartCount > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-primary rounded-full min-w-[20px] h-5">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                <!-- Customer Auth Actions (Desktop) -->
                <div class="hidden sm:block">
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-1.5 text-sm font-semibold hover:text-primary transition-colors">
                                <i class="fa-regular fa-user text-lg"></i>
                                <span class="hidden md:inline">{{ auth()->user()->name }}</span>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-theme-card border border-theme-border rounded-theme shadow-2xl z-50 py-1 text-xs">
                                <a href="/profile" wire:navigate class="block px-4 py-2 hover:bg-secondary text-theme-text">My Profile</a>
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-secondary text-red-500 font-semibold">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="/login" wire:navigate class="text-sm font-semibold hover:text-primary transition-colors flex items-center gap-1">
                            <i class="fa-regular fa-user text-lg"></i>
                            <span class="hidden md:inline">Login</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden">
                    <button type="button" class="text-theme-text hover:text-primary focus:outline-none p-2" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div class="hidden md:hidden border-t border-theme-border bg-theme-bg" id="mobile-menu">
        <div class="px-2 pt-2 pb-4 space-y-1 sm:px-3">
            <div class="px-3 py-2 sm:hidden">
                <livewire:storefront.components.search-bar />
            </div>
            <a href="/" wire:navigate class="block px-3 py-2 rounded-theme text-base font-medium hover:bg-secondary hover:text-primary">Home</a>
            <a href="/shop" wire:navigate class="block px-3 py-2 rounded-theme text-base font-medium hover:bg-secondary hover:text-primary">Shop</a>
            @foreach($menuItems as $item)
                @php
                    $resolvedUrl = $item->url;
                    if (($item->type ?? 'custom') === 'category' && $item->category) {
                        $resolvedUrl = '/category/' . $item->category->slug;
                    } elseif (($item->type ?? 'custom') === 'brand' && $item->brand) {
                        $resolvedUrl = '/shop?brand=' . $item->brand->slug;
                    }
                @endphp
                <a href="{{ $resolvedUrl }}" wire:navigate class="block px-3 py-2 rounded-theme text-base font-medium hover:bg-secondary hover:text-primary">{{ $item->title }}</a>
            @endforeach
            
            <div class="border-t border-theme-border mt-3 pt-3">
                @auth
                    <a href="/profile" wire:navigate class="block px-3 py-2 rounded-theme text-base font-medium hover:bg-secondary hover:text-primary">My Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 rounded-theme text-base font-medium text-red-500 hover:bg-secondary">Logout</button>
                    </form>
                @else
                    <a href="/login" wire:navigate class="block px-3 py-2 rounded-theme text-base font-medium hover:bg-secondary hover:text-primary">Login / Register</a>
                @endauth
            </div>
        </div>
    </div>
</header>
