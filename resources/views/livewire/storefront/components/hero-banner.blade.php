<div 
    x-data="{ 
        activeSlide: 0, 
        slidesCount: {{ count($banners) }},
        init() {
            setInterval(() => {
                this.activeSlide = (this.activeSlide + 1) % this.slidesCount;
            }, 6000);
        }
    }" 
    class="hero-slider-container relative w-full h-[300px] md:h-[450px] bg-secondary overflow-hidden group"
>
    @if(count($banners) > 0)
        <!-- Slides -->
        <div class="relative w-full h-full">
            @foreach($banners as $index => $banner)
                <div 
                    x-show="activeSlide === {{ $index }}" 
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 scale-102"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0 w-full h-full"
                >
                    <img src="{{ asset('storage/' . $banner['image']) }}" class="w-full h-full object-cover" alt="{{ $banner['title'] ?? 'Promo Banner' }}">
                    
                    @if(!empty($banner['title']))
                        <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent flex items-center">
                            <div class="max-w-xl ml-8 md:ml-16 text-white p-6 space-y-4">
                                <h2 class="text-3xl md:text-5xl font-extrabold tracking-tight leading-none">
                                    {{ $banner['title'] }}
                                </h2>
                                @if(!empty($banner['link']))
                                    <a href="{{ $banner['link'] }}" wire:navigate class="btn-primary">
                                        Shop Now <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @elseif(!empty($banner['link']))
                        <a href="{{ $banner['link'] }}" wire:navigate class="absolute inset-0 w-full h-full z-10"></a>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Navigation Arrows -->
        @if(count($banners) > 1)
            <button 
                x-on:click="activeSlide = activeSlide === 0 ? slidesCount - 1 : activeSlide - 1" 
                class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/30 hover:bg-black/60 text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100"
            >
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button 
                x-on:click="activeSlide = (activeSlide + 1) % slidesCount" 
                class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/30 hover:bg-black/60 text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100"
            >
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <!-- Dots Indicator -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                @foreach($banners as $index => $banner)
                    <button 
                        x-on:click="activeSlide = {{ $index }}"
                        class="w-2.5 h-2.5 rounded-full transition-all"
                        x-bind:class="activeSlide === {{ $index }} ? 'bg-primary w-6' : 'bg-white/60 hover:bg-white'"
                    ></button>
                @endforeach
            </div>
        @endif
    @else
        <!-- Fallback Placeholder Banner -->
        <div class="w-full h-full flex items-center justify-center flex-col bg-secondary text-theme-muted p-8 text-center">
            <i class="fa-solid fa-images text-5xl mb-4 text-primary"></i>
            <h2 class="text-2xl font-bold mb-2 text-theme-text">Welcome to Our Premium Store</h2>
            <p class="max-w-md text-sm">Create active banners in the admin panel under Storefront > Banners to populate this slider.</p>
        </div>
    @endif
</div>
