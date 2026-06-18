@php
    $storeTheme = $settings['store_theme'] ?? 'grocery';
    
    // Theme dynamic grids
    $gridClass = 'grid grid-cols-2 gap-6 ';
    if ($storeTheme === 'grocery') {
        $gridClass .= 'sm:grid-cols-3 md:grid-cols-5 lg:gap-8';
    } else {
        $gridClass .= 'sm:grid-cols-2 md:grid-cols-4 lg:gap-8';
    }
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-20">
    @if(session()->has('message'))
        <!-- Toast Notification -->
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-5 right-5 bg-primary text-white py-3 px-6 rounded-theme shadow-2xl z-50 flex items-center gap-2 transition-all">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if(!empty($sections))
        @foreach($sections as $section)
            @if(($section['type'] ?? '') === 'slider')
                <!-- Slider section -->
                <section class="w-full relative overflow-hidden rounded-theme shadow-xl border border-theme-border">
                    <livewire:storefront.components.hero-banner :banner-ids="$section['data']['banner_ids'] ?? []" />
                </section>

            @elseif(($section['type'] ?? '') === 'hero_split')
                <!-- Split Hero Banner -->
                <section class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center bg-theme-card border border-theme-border rounded-theme p-8 md:p-16 relative overflow-hidden shadow-sm">
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
                    <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
                    
                    <div class="space-y-6 relative z-10 {{ ($section['data']['text_alignment'] ?? 'left') === 'right' ? 'md:order-2' : '' }}">
                        @if(!empty($section['data']['badge_text']))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary border border-primary/20">
                                {{ $section['data']['badge_text'] }}
                            </span>
                        @endif
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-theme-text leading-tight tracking-tight">
                            {{ $section['data']['title'] }}
                        </h1>
                        <p class="text-base md:text-lg text-theme-muted max-w-xl">
                            {{ $section['data']['subtitle'] ?? '' }}
                        </p>
                        <div class="pt-4">
                            <a href="{{ $section['data']['button_url'] ?? '/shop' }}" wire:navigate class="btn-primary py-3 px-8 font-bold text-sm tracking-wider shadow-md">
                                {{ $section['data']['button_text'] ?? 'Shop Now' }} <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="relative z-10 w-full aspect-[4/3] rounded-2xl overflow-hidden shadow-inner group {{ ($section['data']['text_alignment'] ?? 'left') === 'right' ? 'md:order-1' : '' }}">
                        @if(!empty($section['data']['image']))
                            <img src="{{ asset('storage/' . $section['data']['image']) }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-103" alt="{{ $section['data']['title'] }}">
                        @else
                            <div class="w-full h-full bg-secondary flex items-center justify-center text-theme-muted">
                                <i class="fa-solid fa-image text-5xl opacity-40 animate-pulse"></i>
                            </div>
                        @endif
                    </div>
                </section>

            @elseif(($section['type'] ?? '') === 'category_pills')
                <!-- Category Pills (Quick Links) -->
                <section class="space-y-6">
                    @if(!empty($section['data']['title']))
                        <h3 class="text-xl font-bold tracking-tight text-theme-text border-b border-theme-border pb-3">{{ $section['data']['title'] }}</h3>
                    @endif
                    <div class="flex flex-wrap gap-3">
                        @foreach($this->getCategories($section['data']['category_ids'] ?? []) as $cat)
                            <a href="/category/{{ $cat->slug }}" wire:navigate class="flex items-center gap-2.5 px-5 py-3 rounded-full bg-theme-card border border-theme-border text-theme-text hover:border-primary hover:text-primary hover:shadow-lg hover:shadow-primary/5 transition-all duration-300 font-bold text-sm">
                                @if(!empty($cat->image))
                                    <img src="{{ asset('storage/' . $cat->image) }}" class="w-6 h-6 rounded-full object-cover" alt="{{ $cat->name }}">
                                @else
                                    <i class="fa-solid fa-layer-group text-xs text-primary"></i>
                                @endif
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </section>

            @elseif(($section['type'] ?? '') === 'tabbed_products')
                <!-- Tabbed Products Grid -->
                <section class="space-y-8" x-data="{ activeTab: 0 }">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-theme-border pb-5">
                        <h2 class="text-3xl font-extrabold tracking-tight text-theme-text">
                            {{ $section['data']['title'] ?? 'Our Collections' }}
                        </h2>
                        
                        <!-- Tabs Navigation -->
                        <div class="flex border border-theme-border rounded-full p-1 bg-secondary/50">
                            @foreach($section['data']['tabs'] ?? [] as $index => $tab)
                                <button 
                                    @click="activeTab = {{ $index }}" 
                                    :class="activeTab === {{ $index }} ? 'bg-primary text-white shadow-md' : 'text-theme-muted hover:text-theme-text'"
                                    class="px-5 py-2 rounded-full text-xs font-bold transition-all duration-300 uppercase tracking-wider"
                                >
                                    {{ $tab['tab_title'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tabs Content Panels -->
                    @foreach($section['data']['tabs'] ?? [] as $index => $tab)
                        <div x-show="activeTab === {{ $index }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="{{ $gridClass }}">
                            @foreach($this->getProducts($tab['product_ids'] ?? []) as $product)
                                <livewire:storefront.components.product-card :product="$product" :key="'tabbed-'.$index.'-'.$product['id']" />
                            @endforeach
                        </div>
                    @endforeach
                </section>

            @elseif(($section['type'] ?? '') === 'deal_of_the_day')
                <!-- Deal of the Day / Countdown Section -->
                @php
                    $dealProduct = $this->getProduct($section['data']['product_id'] ?? null);
                    $endTime = !empty($section['data']['countdown_end']) ? strtotime($section['data']['countdown_end']) : time() + 86400;
                    $stockLimit = (int)($section['data']['stock_limit'] ?? 50);
                    $stockSold = (int)($section['data']['stock_sold'] ?? 35);
                    $stockRemaining = max(0, $stockLimit - $stockSold);
                    $percentSold = ($stockLimit > 0) ? round(($stockSold / $stockLimit) * 100) : 0;
                @endphp
                @if($dealProduct)
                    <section class="bg-gradient-to-br from-theme-card to-secondary/20 border border-theme-border rounded-theme p-8 md:p-14 shadow-md overflow-hidden relative">
                        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                            <!-- Product Showcase side -->
                            <div class="flex flex-col sm:flex-row gap-6 items-center">
                                <div class="w-full sm:w-1/2 aspect-square rounded-2xl bg-secondary p-4 overflow-hidden border border-theme-border relative group">
                                    <a href="/product/{{ $dealProduct->slug }}" wire:navigate class="block w-full h-full">
                                        @if(count($dealProduct->images) > 0)
                                            <img src="{{ asset('storage/' . $dealProduct->images[0]->path) }}" class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105" alt="{{ $dealProduct->name }}">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-theme-muted"><i class="fa-solid fa-image text-3xl opacity-40"></i></div>
                                        @endif
                                    </a>
                                </div>
                                <div class="w-full sm:w-1/2 space-y-3">
                                    @if($dealProduct->brand)
                                        <span class="text-xs uppercase font-extrabold tracking-widest text-primary">{{ $dealProduct->brand->name }}</span>
                                    @endif
                                    <h3 class="text-xl font-bold text-theme-text leading-snug">
                                        <a href="/product/{{ $dealProduct->slug }}" wire:navigate class="hover:text-primary transition-colors">
                                            {{ $dealProduct->name }}
                                        </a>
                                    </h3>
                                    <div class="flex items-center gap-1.5 text-amber-500 text-xs">
                                        <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star-half-stroke"></i>
                                        <span class="text-theme-muted font-bold text-[10px]">(4.8 / 120 reviews)</span>
                                    </div>
                                    <div class="flex items-baseline gap-2.5">
                                        <span class="text-2xl font-black text-primary">৳{{ number_format($dealProduct->selling_price, 2) }}</span>
                                        @if($dealProduct->compare_price_display > $dealProduct->selling_price)
                                            <span class="text-sm text-theme-muted line-through">৳{{ number_format($dealProduct->compare_price_display, 2) }}</span>
                                        @endif
                                    </div>
                                    <div class="pt-2">
                                        <a href="/product/{{ $dealProduct->slug }}" wire:navigate class="btn-primary text-xs font-extrabold px-6 py-2.5 shadow-sm">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Countdown & Stock Tracker side -->
                            <div class="space-y-6" x-data="{
                                end: {{ $endTime }},
                                days: 0, hours: 0, minutes: 0, seconds: 0,
                                init() {
                                    this.update();
                                    setInterval(() => this.update(), 1000);
                                },
                                update() {
                                    let now = Math.floor(Date.now() / 1000);
                                    let diff = this.end - now;
                                    if (diff <= 0) return;
                                    this.days = Math.floor(diff / 86400);
                                    this.hours = Math.floor((diff % 86400) / 3600);
                                    this.minutes = Math.floor((diff % 3600) / 60);
                                    this.seconds = diff % 60;
                                }
                            }">
                                <div class="space-y-1">
                                    <h4 class="text-xs uppercase font-extrabold text-red-500 tracking-widest flex items-center gap-1.5">
                                        <i class="fa-solid fa-fire animate-pulse text-sm"></i> Hurry Up! Deal Ends In:
                                    </h4>
                                </div>
                                
                                <!-- Countdown layout -->
                                <div class="grid grid-cols-4 gap-3 text-center max-w-sm">
                                    <div class="bg-theme-card border border-theme-border rounded-xl p-3.5 shadow-sm">
                                        <div class="text-2xl md:text-3xl font-black text-theme-text" x-text="days">00</div>
                                        <div class="text-[9px] uppercase tracking-wider text-theme-muted font-bold mt-1">Days</div>
                                    </div>
                                    <div class="bg-theme-card border border-theme-border rounded-xl p-3.5 shadow-sm">
                                        <div class="text-2xl md:text-3xl font-black text-theme-text" x-text="hours">00</div>
                                        <div class="text-[9px] uppercase tracking-wider text-theme-muted font-bold mt-1">Hours</div>
                                    </div>
                                    <div class="bg-theme-card border border-theme-border rounded-xl p-3.5 shadow-sm">
                                        <div class="text-2xl md:text-3xl font-black text-theme-text" x-text="minutes">00</div>
                                        <div class="text-[9px] uppercase tracking-wider text-theme-muted font-bold mt-1">Mins</div>
                                    </div>
                                    <div class="bg-theme-card border border-theme-border rounded-xl p-3.5 shadow-sm">
                                        <div class="text-2xl md:text-3xl font-black text-theme-text" x-text="seconds">00</div>
                                        <div class="text-[9px] uppercase tracking-wider text-theme-muted font-bold mt-1">Secs</div>
                                    </div>
                                </div>
                                
                                <!-- Stock Progress Bar -->
                                <div class="space-y-2">
                                    <div class="flex justify-between text-xs text-theme-muted font-bold">
                                        <span>Only <strong class="text-red-500">{{ $stockRemaining }} items</strong> left in stock!</span>
                                        <span>{{ $percentSold }}% Sold</span>
                                    </div>
                                    <div class="w-full h-2.5 bg-secondary border border-theme-border rounded-full overflow-hidden shadow-inner">
                                        <div class="h-full bg-gradient-to-r from-red-500 to-amber-500 transition-all duration-1000" style="width: {{ $percentSold }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

            @elseif(($section['type'] ?? '') === 'featured_categories')
                <!-- Categories section -->
                <section class="space-y-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-theme-border pb-5">
                        <div class="space-y-1">
                            <h2 class="text-3xl font-extrabold tracking-tight text-theme-text">
                                {{ $section['data']['title'] ?? 'Shop by Category' }}
                            </h2>
                            <p class="text-sm text-theme-muted">Select a category to browse related products.</p>
                        </div>
                        <a href="/shop" wire:navigate class="text-sm font-bold text-primary hover:text-primary/80 transition-colors flex items-center gap-1 group">
                            Browse All <i class="fa-solid fa-chevron-right text-[10px] transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                    
                    <!-- Categories layouts based on themes -->
                    @if($storeTheme === 'grocery')
                        <!-- Grocery design: Beautiful Rounded Pills -->
                        <div class="flex flex-wrap gap-3 items-center justify-center">
                            @foreach($this->getCategories($section['data']['category_ids'] ?? []) as $category)
                                <a href="/category/{{ $category->slug }}" wire:navigate class="bg-secondary text-primary font-bold hover:bg-primary hover:text-white transition-all duration-300 rounded-full px-6 py-3 shadow-sm border border-primary/15 text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-basket-shopping text-xs"></i>
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    @elseif($storeTheme === 'fashion')
                        <!-- Fashion design: Minimal Clean Tall Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                            @foreach($this->getCategories($section['data']['category_ids'] ?? []) as $category)
                                <a href="/category/{{ $category->slug }}" wire:navigate class="group block relative overflow-hidden bg-secondary aspect-[4/5] border border-theme-border">
                                    @if(!empty($category->image))
                                        <img src="{{ asset('storage/' . $category->image) }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" alt="{{ $category->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-secondary text-theme-muted">
                                            <i class="fa-solid fa-image text-3xl"></i>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent flex flex-col justify-end p-6">
                                        <span class="text-white text-lg uppercase tracking-widest font-semibold border-b-2 border-white pb-1 w-fit">
                                            {{ $category->name }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <!-- Electronics layout: Modern Glass-boxes -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6">
                            @foreach($this->getCategories($section['data']['category_ids'] ?? []) as $category)
                                <a href="/category/{{ $category->slug }}" wire:navigate class="bg-theme-card border border-theme-border rounded-theme p-6 text-center hover:border-primary hover:shadow-lg hover:shadow-primary/5 transition-all duration-300 flex flex-col items-center gap-4 group">
                                    @if(!empty($category->image))
                                        <img src="{{ asset('storage/' . $category->image) }}" loading="lazy" class="w-16 h-16 object-contain group-hover:scale-105 transition-transform" alt="{{ $category->name }}">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-secondary flex items-center justify-center text-primary text-2xl group-hover:bg-primary group-hover:text-white transition-all duration-300">
                                            <i class="fa-solid fa-layer-group"></i>
                                        </div>
                                    @endif
                                    <span class="text-sm font-bold text-theme-text line-clamp-1 group-hover:text-primary transition-colors">{{ $category->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>

            @elseif(($section['type'] ?? '') === 'trending_products')
                <!-- Trending products section -->
                <section class="space-y-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-theme-border pb-5">
                        <div class="space-y-1">
                            <h2 class="text-3xl font-extrabold tracking-tight text-theme-text">
                                {{ $section['data']['title'] ?? 'Trending Now' }}
                            </h2>
                            <p class="text-sm text-theme-muted">The best selling products of the season.</p>
                        </div>
                        <a href="/shop" wire:navigate class="text-sm font-bold text-primary hover:text-primary/80 transition-colors flex items-center gap-1 group">
                            View More <i class="fa-solid fa-chevron-right text-[10px] transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <!-- Grid Layout -->
                    <div class="{{ $gridClass }}">
                        @foreach($this->getProducts($section['data']['product_ids'] ?? [], $section['data']['limit'] ?? 8) as $product)
                            <livewire:storefront.components.product-card :product="$product" :key="'trending-'.$product['id']" />
                        @endforeach
                    </div>
                </section>

            @elseif(($section['type'] ?? '') === 'promo_banner')
                <!-- Promo banner section -->
                @if(!empty($section['data']['image']))
                    <section class="w-full relative group overflow-hidden rounded-theme border border-theme-border">
                        <a href="{{ $section['data']['link'] ?? '#' }}" wire:navigate class="block w-full overflow-hidden hover:opacity-95 transition-all">
                            <img src="{{ asset('storage/' . $section['data']['image']) }}" loading="lazy" class="w-full h-auto object-cover max-h-[350px] transition-transform duration-1000 group-hover:scale-102" alt="Promo Banner">
                        </a>
                    </section>
                @endif

            @elseif(($section['type'] ?? '') === 'text_block')
                <!-- Custom text block section -->
                <section class="bg-theme-card border border-theme-border rounded-theme p-10 md:p-16 text-center max-w-4xl mx-auto shadow-md relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
                    <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
                    @if(!empty($section['data']['title']))
                        <h2 class="text-4xl font-extrabold tracking-tight text-theme-text mb-6">
                            {{ $section['data']['title'] }}
                        </h2>
                    @endif
                    <div class="prose dark:prose-invert max-w-none text-theme-muted text-base leading-relaxed">
                        {!! $section['data']['content'] !!}
                    </div>
                </section>

            @elseif(($section['type'] ?? '') === 'product_carousel')
                <!-- Product Slider / Carousel -->
                <section class="space-y-8" x-data="{ scroll: 0 }">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-theme-border pb-5">
                        <div class="space-y-1">
                            <h2 class="text-3xl font-extrabold tracking-tight text-theme-text">
                                {{ $section['data']['title'] ?? 'Featured Offers' }}
                            </h2>
                            <p class="text-sm text-theme-muted">Swipe left/right to view all exclusive deals.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="$refs.carousel.scrollBy({ left: -300, behavior: 'smooth' })" class="p-2.5 rounded-full border border-theme-border hover:bg-primary hover:text-white transition-all text-sm flex items-center justify-center w-10 h-10 shadow-sm bg-theme-card">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <button @click="$refs.carousel.scrollBy({ left: 300, behavior: 'smooth' })" class="p-2.5 rounded-full border border-theme-border hover:bg-primary hover:text-white transition-all text-sm flex items-center justify-center w-10 h-10 shadow-sm bg-theme-card">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div x-ref="carousel" class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth no-scrollbar pb-6">
                        @foreach($this->getProducts($section['data']['product_ids'] ?? [], $section['data']['limit'] ?? 8) as $product)
                            <div class="min-w-[280px] w-[280px] sm:min-w-[310px] sm:w-[310px] snap-start">
                                <livewire:storefront.components.product-card :product="$product" :key="'carousel-'.$product['id']" />
                            </div>
                        @endforeach
                    </div>
                </section>

            @elseif(($section['type'] ?? '') === 'brand_grid')
                <!-- Brands Grid/Slider -->
                <section class="space-y-8">
                    <div class="border-b border-theme-border pb-5">
                        <h2 class="text-3xl font-extrabold tracking-tight text-theme-text">
                            {{ $section['data']['title'] ?? 'Shop by Brand' }}
                        </h2>
                        <p class="text-sm text-theme-muted">Find genuine products from top leading brands.</p>
                    </div>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-6">
                        @foreach($this->getBrands($section['data']['brand_ids'] ?? []) as $brand)
                            <a href="/shop?brand={{ $brand->slug }}" wire:navigate class="bg-theme-card border border-theme-border rounded-theme p-6 flex items-center justify-center aspect-[3/2] hover:border-primary hover:shadow-lg transition-all duration-300 group">
                                @if(!empty($brand->logo))
                                    <img src="{{ asset('storage/' . $brand->logo) }}" loading="lazy" class="max-h-12 w-auto object-contain filter grayscale group-hover:grayscale-0 transition-all duration-300 transform group-hover:scale-105" alt="{{ $brand->name }}">
                                @else
                                    <span class="text-lg font-bold text-theme-muted group-hover:text-primary transition-colors">{{ $brand->name }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>

            @elseif(($section['type'] ?? '') === 'testimonials')
                <!-- Testimonials -->
                <section class="space-y-8">
                    <div class="text-center max-w-xl mx-auto space-y-2">
                        <h2 class="text-3xl font-extrabold tracking-tight text-theme-text">
                            {{ $section['data']['title'] ?? 'What Our Customers Say' }}
                        </h2>
                        <p class="text-sm text-theme-muted">Real stories and reviews from our loyal customers.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @foreach($section['data']['items'] ?? [] as $item)
                            <div class="bg-theme-card border border-theme-border rounded-theme p-8 shadow-sm flex flex-col justify-between hover:shadow-md transition-all duration-300 relative overflow-hidden group">
                                <div class="absolute -right-6 -top-6 text-primary/5 text-8xl font-serif">“</div>
                                <div class="space-y-4 relative z-10">
                                    <div class="flex text-amber-400 gap-1 text-sm">
                                        @for($i = 0; $i < ($item['rating'] ?? 5); $i++)
                                            <i class="fa-solid fa-star"></i>
                                        @endfor
                                    </div>
                                    <p class="text-sm text-theme-muted italic leading-relaxed">
                                        "{{ $item['comment'] ?? '' }}"
                                    </p>
                                </div>
                                <div class="flex items-center gap-4 mt-6 pt-6 border-t border-theme-border/60">
                                    @if(!empty($item['avatar']))
                                        <img src="{{ asset('storage/' . $item['avatar']) }}" class="w-12 h-12 rounded-full object-cover border-2 border-primary/20" alt="{{ $item['name'] }}">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center text-primary font-bold text-lg">
                                            {{ substr($item['name'] ?? 'U', 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="text-sm font-bold text-theme-text">{{ $item['name'] ?? 'Anonymous' }}</h4>
                                        <p class="text-xs text-theme-muted">{{ $item['role'] ?? 'Customer' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

            @elseif(($section['type'] ?? '') === 'trust_badges')
                <!-- Trust Badges -->
                <section class="bg-theme-card border border-theme-border rounded-theme p-8 md:p-12 shadow-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 divide-y sm:divide-y-0 sm:divide-x divide-theme-border/60">
                        @foreach($section['data']['badges'] ?? [] as $badge)
                            <div class="flex items-center gap-4 px-4 py-4 sm:py-0 first:pl-0">
                                <div class="w-12 h-12 rounded-full bg-secondary text-primary flex items-center justify-center text-xl shrink-0">
                                    <i class="{{ $badge['icon'] ?? 'fa-solid fa-truck-fast' }}"></i>
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="text-sm font-extrabold text-theme-text uppercase tracking-wider">{{ $badge['title'] ?? '' }}</h4>
                                    <p class="text-xs text-theme-muted leading-tight">{{ $badge['subtitle'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

            @elseif(($section['type'] ?? '') === 'newsletter_signup')
                <!-- Newsletter Signup -->
                <section class="bg-gradient-to-br from-theme-card to-secondary/30 border border-theme-border rounded-theme p-10 md:p-16 text-center max-w-4xl mx-auto shadow-xl relative overflow-hidden">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
                    <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
                    
                    <div class="max-w-2xl mx-auto space-y-6 relative z-10">
                        <h2 class="text-4xl font-extrabold tracking-tight text-theme-text">
                            {{ $section['data']['title'] ?? 'Subscribe to our Newsletter' }}
                        </h2>
                        <div class="text-theme-muted text-sm leading-relaxed">
                            {!! $section['data']['content'] ?? '' !!}
                        </div>
                        <form onsubmit="event.preventDefault(); alert('Thank you for subscribing!');" class="flex flex-col sm:flex-row gap-3 mt-8 max-w-md mx-auto">
                            <input type="email" placeholder="Enter your email address" required class="flex-grow bg-theme-card border border-theme-border rounded-theme px-4 py-3 text-sm focus:outline-none focus:border-primary text-theme-text shadow-inner">
                            <button type="submit" class="btn-primary px-8 py-3 text-sm font-bold tracking-wider">
                                {{ $section['data']['button_text'] ?? 'Subscribe' }}
                            </button>
                        </form>
                    </div>
                </section>
            @endif
        @endforeach
    @else
        <!-- No dynamic layout. Set defaults -->
        <section class="w-full rounded-theme overflow-hidden border border-theme-border shadow-lg">
            <livewire:storefront.components.hero-banner />
        </section>
        
        <section class="space-y-8">
            <h2 class="text-3xl font-extrabold border-b border-theme-border pb-5">Our Products</h2>
            <div class="{{ $gridClass }}">
                @foreach($this->getProducts([], 10) as $product)
                    <livewire:storefront.components.product-card :product="$product" :key="'default-'.$product['id']" />
                @endforeach
            </div>
        </section>
    @endif
</div>
