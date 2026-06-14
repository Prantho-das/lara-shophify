<footer class="bg-secondary text-theme-text border-t border-theme-border mt-auto pt-16 pb-8 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-{{ count($footerSections) > 0 ? count($footerSections) : 4 }} gap-10 mb-12">
            @if(count($footerSections) > 0)
                <!-- Dynamic Shopify/WordPress style Footer Columns -->
                @foreach($footerSections as $section)
                    @if(($section['type'] ?? '') === 'text_block')
                        <!-- Text Block Widget -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-theme-text">{{ $section['data']['title'] ?? 'About Us' }}</h4>
                            <div class="text-sm text-theme-muted prose prose-sm max-w-none">
                                {!! $section['data']['content'] ?? '' !!}
                            </div>
                            @if(!empty($section['data']['show_social_links']))
                                @php
                                    $socialLinks = json_decode($settings['social_links'] ?? '[]', true);
                                @endphp
                                @if(count($socialLinks) > 0)
                                    <div class="flex gap-4 pt-2 flex-wrap">
                                        @foreach($socialLinks as $link)
                                            @php
                                                $platform = $link['platform'] ?? 'custom';
                                                $iconClass = $link['custom_icon'] ?? '';
                                                if (empty($iconClass) || $platform !== 'custom') {
                                                    if ($platform === 'facebook') $iconClass = 'fa-brands fa-facebook-f';
                                                    elseif ($platform === 'instagram') $iconClass = 'fa-brands fa-instagram';
                                                    elseif ($platform === 'youtube') $iconClass = 'fa-brands fa-youtube';
                                                    elseif ($platform === 'twitter') $iconClass = 'fa-brands fa-x-twitter';
                                                    elseif ($platform === 'tiktok') $iconClass = 'fa-brands fa-tiktok';
                                                    elseif ($platform === 'linkedin') $iconClass = 'fa-brands fa-linkedin-in';
                                                    elseif ($platform === 'pinterest') $iconClass = 'fa-brands fa-pinterest-p';
                                                    elseif ($platform === 'whatsapp') $iconClass = 'fa-brands fa-whatsapp';
                                                    else $iconClass = 'fa-solid fa-link';
                                                }
                                            @endphp
                                            <a href="{{ $link['url'] }}" target="_blank" class="w-8 h-8 rounded-full border border-theme-border flex items-center justify-center text-theme-muted hover:text-primary hover:border-primary transition-all" title="{{ ucfirst($platform) }}">
                                                <i class="{{ $iconClass }}"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>
                    @elseif(($section['type'] ?? '') === 'menu_block')
                        <!-- Menu Block Widget -->
                        @php
                            $menu = \App\Models\Menu::with(['items.category', 'items.brand'])->find($section['data']['menu_id'] ?? null);
                        @endphp
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-theme-text">{{ $section['data']['title'] ?? 'Navigation' }}</h4>
                            @if($menu && count($menu->items) > 0)
                                <ul class="space-y-2.5">
                                    @foreach($menu->items as $item)
                                        @php
                                            $resolvedUrl = $item->url;
                                            if (($item->type ?? 'custom') === 'category' && $item->category) {
                                                $resolvedUrl = '/category/' . $item->category->slug;
                                            } elseif (($item->type ?? 'custom') === 'brand' && $item->brand) {
                                                $resolvedUrl = '/shop?brand=' . $item->brand->slug;
                                            }
                                        @endphp
                                        <li>
                                            <a href="{{ $resolvedUrl }}" wire:navigate class="text-sm text-theme-muted hover:text-primary transition-colors">
                                                {{ $item->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-xs text-theme-muted italic">No menu items configured.</p>
                            @endif
                        </div>
                    @elseif(($section['type'] ?? '') === 'contact_block')
                        <!-- Contact Block Widget -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-theme-text">{{ $section['data']['title'] ?? 'Get In Touch' }}</h4>
                            <ul class="space-y-2.5 text-sm text-theme-muted">
                                @if(!empty($section['data']['phone']))
                                    <li class="flex items-center gap-2">
                                        <i class="fa-solid fa-phone text-xs text-primary"></i>
                                        {{ $section['data']['phone'] }}
                                    </li>
                                @endif
                                @if(!empty($section['data']['email']))
                                    <li class="flex items-center gap-2">
                                        <i class="fa-solid fa-envelope text-xs text-primary"></i>
                                        {{ $section['data']['email'] }}
                                    </li>
                                @endif
                                @if(!empty($section['data']['address']))
                                    <li class="flex items-start gap-2">
                                        <i class="fa-solid fa-location-dot text-xs text-primary mt-1"></i>
                                        <span>{{ $section['data']['address'] }}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                @endforeach
            @else
                <!-- Fallback Default Footer Columns if no widgets saved in settings -->
                <div class="space-y-4">
                    <a href="/" wire:navigate class="flex items-center gap-2">
                        @if(!empty($settings['store_logo']))
                            <img class="h-8 w-auto object-contain" src="{{ asset('storage/' . $settings['store_logo']) }}" alt="Store">
                        @else
                            <span class="text-xl font-bold tracking-tight text-primary">{{ $settings['store_name'] ?? 'Store' }}</span>
                        @endif
                    </a>
                    <p class="text-sm text-theme-muted">
                        Welcome to the next generation Shopify-clone storefront. Designed for premium experience.
                    </p>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-theme-text">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="/shop" wire:navigate class="text-sm text-theme-muted hover:text-primary transition-colors">All Products</a></li>
                        <li><a href="/page/privacy-policy" wire:navigate class="text-sm text-theme-muted hover:text-primary transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-theme-text">Policies</h4>
                    <ul class="space-y-2">
                        <li><a href="/page/terms-of-service" wire:navigate class="text-sm text-theme-muted hover:text-primary transition-colors">Terms of Service</a></li>
                        <li><a href="/page/refund-policy" wire:navigate class="text-sm text-theme-muted hover:text-primary transition-colors">Refund Policy</a></li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-theme-text">Support</h4>
                    <ul class="space-y-2 text-sm text-theme-muted">
                        @if(!empty($settings['support_phone']))
                            <li class="flex items-center gap-2"><i class="fa-solid fa-phone text-xs text-primary"></i> {{ $settings['support_phone'] }}</li>
                        @endif
                        @if(!empty($settings['support_email']))
                            <li class="flex items-center gap-2"><i class="fa-solid fa-envelope text-xs text-primary"></i> {{ $settings['support_email'] }}</li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>

        <div class="border-t border-theme-border pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-theme-muted">
            <p>&copy; {{ date('Y') }} {{ $settings['store_name'] ?? 'Jarvis Store' }}. All rights reserved.</p>
            <div class="flex gap-4">
                <i class="fa-brands fa-cc-visa text-xl"></i>
                <i class="fa-brands fa-cc-mastercard text-xl"></i>
                <span class="font-bold border border-theme-border px-1.5 rounded-sm">bKash</span>
                <span class="font-bold border border-theme-border px-1.5 rounded-sm">Nagad</span>
            </div>
        </div>
    </div>
</footer>
