<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
    @php 
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $storeTheme = $settings['store_theme'] ?? 'grocery';
        $storeFont = $settings['store_font'] ?? 'Outfit';
    @endphp
    <title>{{ $title ?? ($settings['seo_meta_title'] ?? ($settings['store_name'] ?? 'E-Commerce Store')) }}</title>
    <meta name="description" content="{{ $meta_description ?? ($settings['seo_meta_description'] ?? '') }}">
    @if(!empty($settings['store_favicon']))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $settings['store_favicon']) }}">
    @endif
    
    <!-- Preconnect & Fonts Preloading for Max PageSpeed -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($storeFont) }}:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: 'var(--primary-color)',
                        secondary: 'var(--secondary-color)',
                        accent: 'var(--accent-color)',
                        'theme-bg': 'var(--bg-color)',
                        'theme-text': 'var(--text-color)',
                        'theme-muted': 'var(--text-muted)',
                        'theme-card': 'var(--card-bg)',
                        'theme-border': 'var(--border-color)',
                    },
                    borderRadius: {
                        'theme': 'var(--border-radius)',
                    },
                    fontFamily: {
                        sans: ['{{ $storeFont }}', 'system-ui', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Theme Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/themes/' . $storeTheme . '.css') }}">

    <!-- Dynamic Admin Custom Overrides -->
    <style>
        :root {
            @if(!empty($settings['primary_color']))
                --primary-color: {{ $settings['primary_color'] }};
                --primary-hover: {{ $settings['primary_color'] }}cc;
                --secondary-color: color-mix(in srgb, {{ $settings['primary_color'] }} 8%, white);
            @endif
            @if(!empty($settings['secondary_color']))
                --secondary-color: {{ $settings['secondary_color'] }};
            @endif
        }

        .dark {
            @if(!empty($settings['primary_color']))
                --secondary-color: color-mix(in srgb, {{ $settings['primary_color'] }} 12%, #0b1329);
            @endif
            @if(!empty($settings['secondary_color']))
                --secondary-color: {{ $settings['secondary_color'] }};
            @endif
        }
    </style>

    <!-- Theme Initialization Script -->
    <script>
        function applyTheme() {
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        applyTheme();

        function updateThemeIcons() {
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            if (!darkIcon || !lightIcon) return;
            
            if (document.documentElement.classList.contains('dark')) {
                darkIcon.classList.add('hidden');
                lightIcon.classList.remove('hidden');
            } else {
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
            }
        }

        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            updateThemeIcons();
        }

        document.addEventListener('DOMContentLoaded', () => {
            applyTheme();
            updateThemeIcons();
        });
        document.addEventListener('livewire:navigated', () => {
            applyTheme();
            updateThemeIcons();
        });
    </script>

    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @livewireStyles

    @if(!empty($settings['gtm_id']))
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ $settings['gtm_id'] }}');</script>
    <!-- End Google Tag Manager -->
    @endif

    @if(!empty($settings['facebook_pixel_id']))
    <!-- Facebook Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ $settings['facebook_pixel_id'] }}');
    fbq('track', 'PageView');

    // Livewire custom event listener for CAPI deduplication
    window.addEventListener('fb-event', event => {
        let detail = event.detail;
        if (detail && window.fbq) {
            let name = detail.name;
            let data = detail.data || {};
            let eventId = detail.eventId;
            fbq('track', name, data, eventId ? { eventID: eventId } : {});
        }
    });
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ $settings['facebook_pixel_id'] }}&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->
    @endif

    @if(!empty($settings['tiktok_pixel_id']))
    <!-- TikTok Pixel -->
    <script>
    !function (w, d, t) {
      w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
      ttq.load('{{ $settings['tiktok_pixel_id'] }}');
      ttq.page();
    }(window, document, 'ttq');
    </script>
    <!-- End TikTok Pixel -->
    @endif

    @if(!empty($settings['custom_head_scripts']))
        {!! $settings['custom_head_scripts'] !!}
    @endif
</head>
<body class="bg-theme-bg text-theme-text font-sans antialiased min-h-screen flex flex-col transition-colors duration-500 overflow-x-hidden">
    @if(!empty($settings['gtm_id']))
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $settings['gtm_id'] }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif

    @if(!empty($settings['custom_body_scripts']))
        {!! $settings['custom_body_scripts'] !!}
    @endif
    
    @if(!empty($settings['topbar_text']))
        <!-- Announcement Bar -->
        <div class="bg-gradient-to-r from-primary via-primary/90 to-primary text-white text-center py-2.5 px-4 text-xs font-bold tracking-wider uppercase shadow-sm relative z-50">
            {{ $settings['topbar_text'] }}
        </div>
    @endif

    <!-- Global Header/Navbar -->
    <livewire:storefront.components.navbar />

    <!-- Main Content wrapper for animations -->
    <main class="flex-grow opacity-0 transition-opacity duration-300 ease-in" onload="this.style.opacity='1'" style="opacity: 1;">
        {{ $slot }}
    </main>

    <!-- Global Footer -->
    <livewire:storefront.components.footer />

    @if($settings['popup_enabled'] ?? false)
        @php
            $popupType = $settings['popup_type'] ?? 'newsletter';
            $popupDelay = (int) ($settings['popup_delay'] ?? 3) * 1000;
            $popupCookieLifetime = (int) ($settings['popup_cookie_lifetime'] ?? 1);
            $popupTitle = $settings['popup_title'] ?? '';
            $popupContent = $settings['popup_content'] ?? '';
            $popupImage = $settings['popup_image'] ?? '';
            $popupLink = $settings['popup_link'] ?? '';
        @endphp
        
        <div 
            x-data="{ 
                show: false,
                init() {
                    const hiddenUntil = localStorage.getItem('hide_storefront_popup_until');
                    if (!hiddenUntil || new Date().getTime() > parseInt(hiddenUntil)) {
                        setTimeout(() => {
                            this.show = true;
                        }, {{ $popupDelay }});
                    }
                },
                closePopup() {
                    this.show = false;
                },
                closePermanently() {
                    const days = {{ $popupCookieLifetime }};
                    const expireTime = new Date().getTime() + (days * 24 * 60 * 60 * 1000);
                    localStorage.setItem('hide_storefront_popup_until', expireTime.toString());
                    this.show = false;
                }
            }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div 
                @click.away="closePopup()"
                class="relative w-full max-w-lg bg-theme-card text-theme-text rounded-theme border border-theme-border shadow-2xl overflow-hidden"
            >
                <button @click="closePopup()" class="absolute top-4 right-4 z-10 text-theme-muted hover:text-primary transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>

                @if($popupType === 'newsletter')
                    <div class="p-8 md:p-10 text-center">
                        <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fa-solid fa-envelope-open-text text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-black tracking-tight mb-2">{{ $popupTitle }}</h3>
                        <div class="text-theme-muted mb-6 prose prose-sm max-w-none dark:prose-invert">
                            {!! $popupContent !!}
                        </div>
                        <form onsubmit="event.preventDefault(); alert('Subscribed successfully!');" class="space-y-4">
                            <input type="email" placeholder="Enter your email address" required class="w-full px-4 py-3 bg-secondary/30 border border-theme-border rounded-theme focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm text-theme-text">
                            <button type="submit" class="w-full py-3 bg-primary text-white font-bold rounded-theme hover:bg-primary/90 transition-colors shadow-lg hover:shadow-primary/20">Subscribe Now</button>
                        </form>
                        <div class="mt-6 flex items-center justify-center gap-2">
                            <input type="checkbox" id="dont_show_again" @change="closePermanently()" class="rounded border-theme-border text-primary focus:ring-primary bg-transparent">
                            <label for="dont_show_again" class="text-xs text-theme-muted cursor-pointer hover:text-theme-text transition-colors">Don't show this again</label>
                        </div>
                    </div>

                @elseif($popupType === 'promotion')
                    <div class="relative">
                        @if($popupLink)
                            <a href="{{ $popupLink }}" @click="closePopup()">
                                <img src="{{ asset('storage/' . $popupImage) }}" alt="Promotion" class="w-full h-auto object-cover max-h-[400px]">
                            </a>
                        @else
                            <img src="{{ asset('storage/' . $popupImage) }}" alt="Promotion" class="w-full h-auto object-cover max-h-[400px]">
                        @endif
                        <div class="p-4 text-center bg-theme-card border-t border-theme-border flex items-center justify-center gap-2">
                            <input type="checkbox" id="dont_show_promo" @change="closePermanently()" class="rounded border-theme-border text-primary focus:ring-primary bg-transparent">
                            <label for="dont_show_promo" class="text-xs text-theme-muted cursor-pointer hover:text-theme-text transition-colors">Don't show this again</label>
                        </div>
                    </div>

                @elseif($popupType === 'announcement')
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-amber-500/10 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fa-solid fa-bullhorn text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-black tracking-tight mb-2">{{ $popupTitle }}</h3>
                        <div class="text-theme-muted mb-6 prose prose-sm max-w-none dark:prose-invert">
                            {!! $popupContent !!}
                        </div>
                        @if($popupLink)
                            <a href="{{ $popupLink }}" @click="closePopup()" class="inline-block px-8 py-3 bg-primary text-white font-bold rounded-theme hover:bg-primary/90 transition-colors shadow-lg hover:shadow-primary/20">Learn More</a>
                        @endif
                        <div class="mt-6 flex items-center justify-center gap-2">
                            <input type="checkbox" id="dont_show_announce" @change="closePermanently()" class="rounded border-theme-border text-primary focus:ring-primary bg-transparent">
                            <label for="dont_show_announce" class="text-xs text-theme-muted cursor-pointer hover:text-theme-text transition-colors">Don't show this again</label>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @livewireScripts
</body>
</html>
