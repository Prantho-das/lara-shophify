<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 transition-colors duration-300">
    <!-- Breadcrumbs -->
    <nav class="flex text-xs text-theme-muted mb-8 gap-2">
        <a href="/" wire:navigate class="hover:text-primary transition-colors">Home</a>
        <span>/</span>
        <span class="text-theme-text font-semibold">{{ $title }}</span>
    </nav>

    <!-- Content Card -->
    <article class="bg-theme-card border border-theme-border rounded-theme p-8 md:p-12 shadow-sm space-y-6">
        <h1 class="text-3xl font-extrabold text-theme-text border-b border-theme-border pb-4">
            {{ $title }}
        </h1>
        <div class="prose dark:prose-invert max-w-none text-theme-muted line-height-relaxed">
            {!! $content !!}
        </div>
    </article>
</div>
