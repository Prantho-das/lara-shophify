<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
    @php 
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $storeTheme = $settings['store_theme'] ?? 'grocery';
        $storeFont = $settings['store_font'] ?? 'Outfit';
    @endphp
    <title>{{ $title ?? ($settings['seo_meta_title'] ?? ($settings['store_name'] ?? 'E-Commerce Store')) }}</title>
    <meta name="description" content="{{ $meta_description ?? ($settings['seo_meta_description'] ?? '') }}">
    
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
            @else
                --primary-color: #22c55e;
            @endif
            @if(!empty($settings['secondary_color']))
                --secondary-color: {{ $settings['secondary_color'] }};
            @else
                --secondary-color: #f0fdf4;
            @endif
            
            @if($storeTheme === 'electronics')
                --bg-color: #080c14;
                --card-bg: #0f172a;
                --text-color: #f8fafc;
                --text-muted: #94a3b8;
                --border-color: #1e293b;
                --border-radius: 12px;
                --accent-color: #06b6d4;
            @elseif($storeTheme === 'fashion')
                --bg-color: #ffffff;
                --card-bg: #ffffff;
                --text-color: #0f172a;
                --text-muted: #64748b;
                --border-color: #f1f5f9;
                --border-radius: 0px;
                --accent-color: #d4af37;
            @else
                --bg-color: #f8fafc;
                --card-bg: #ffffff;
                --text-color: #0f172a;
                --text-muted: #64748b;
                --border-color: #e2e8f0;
                --border-radius: 18px;
                --accent-color: #f59e0b;
            @endif
        }
    </style>

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

    @livewireScripts
</body>
</html>
