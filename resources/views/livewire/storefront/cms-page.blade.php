<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 transition-colors duration-300">
    <!-- Breadcrumbs -->
    <nav class="flex text-xs text-theme-muted mb-10 gap-2">
        <a href="/" wire:navigate class="hover:text-primary transition-colors">Home</a>
        <span>/</span>
        <span class="text-theme-text font-semibold">{{ $title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
        <!-- Sidebar Navigation (Only for policy pages) -->
        @if(in_array(request()->route('slug'), ['privacy-policy', 'terms-of-service', 'refund-policy']))
            <aside class="lg:col-span-1 space-y-6">
                <div class="bg-theme-card border border-theme-border rounded-theme p-6 shadow-sm sticky top-24">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-theme-text border-b border-theme-border pb-3 mb-4">Store Policies</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="/page/privacy-policy" wire:navigate class="block text-sm font-bold py-1.5 transition-colors {{ request()->is('page/privacy-policy') ? 'text-primary' : 'text-theme-muted hover:text-primary' }}">
                                <i class="fa-solid fa-shield-halved text-xs mr-2"></i> Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a href="/page/terms-of-service" wire:navigate class="block text-sm font-bold py-1.5 transition-colors {{ request()->is('page/terms-of-service') ? 'text-primary' : 'text-theme-muted hover:text-primary' }}">
                                <i class="fa-solid fa-file-contract text-xs mr-2"></i> Terms of Service
                            </a>
                        </li>
                        <li>
                            <a href="/page/refund-policy" wire:navigate class="block text-sm font-bold py-1.5 transition-colors {{ request()->is('page/refund-policy') ? 'text-primary' : 'text-theme-muted hover:text-primary' }}">
                                <i class="fa-solid fa-rotate-left text-xs mr-2"></i> Refund Policy
                            </a>
                        </li>
                    </ul>
                </div>
            </aside>
            <!-- Content -->
            <article class="lg:col-span-3 bg-theme-card border border-theme-border rounded-theme p-8 md:p-12 shadow-sm space-y-6">
                <h1 class="text-3xl font-extrabold text-theme-text border-b border-theme-border pb-4">
                    {{ $title }}
                </h1>
                <div class="prose dark:prose-invert max-w-none text-theme-muted text-sm leading-relaxed">
                    {!! $content !!}
                </div>
            </article>
        @else
            <!-- Non-policy dynamic pages layout -->
            <article class="lg:col-span-4 bg-theme-card border border-theme-border rounded-theme p-8 md:p-12 shadow-sm space-y-6">
                <h1 class="text-3xl font-extrabold text-theme-text border-b border-theme-border pb-4">
                    {{ $title }}
                </h1>
                <div class="prose dark:prose-invert max-w-none text-theme-muted text-sm leading-relaxed">
                    {!! $content !!}
                </div>
            </article>
        @endif
    </div>
</div>
