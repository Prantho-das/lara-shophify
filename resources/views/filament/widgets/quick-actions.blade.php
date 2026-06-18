<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <x-filament::icon
                    icon="heroicon-m-bolt"
                    class="h-5 w-5 text-amber-500 animate-pulse"
                    style="color: rgb(245, 158, 11);"
                />
                <span style="font-weight: 700; font-size: 1.125rem;">Quick Actions / Shortcuts</span>
            </div>
        </x-slot>

        <style>
            .quick-actions-container {
                display: grid;
                grid-template-columns: repeat(1, minmax(0, 1fr));
                gap: 1rem;
                margin-top: 0.75rem;
            }
            @media (min-width: 640px) {
                .quick-actions-container {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }
            @media (min-width: 1024px) {
                .quick-actions-container {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                }
            }
            .quick-action-btn {
                position: relative;
                overflow: hidden;
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 1.25rem 1rem;
                border-radius: 0.75rem;
                border: 1px solid rgba(229, 231, 235, 1);
                background-color: rgba(249, 250, 251, 1);
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                text-decoration: none;
                cursor: pointer;
            }
            .dark .quick-action-btn {
                background-color: rgba(30, 41, 59, 0.4);
                border-color: rgba(71, 85, 105, 0.3);
            }
            .quick-action-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            }
            .btn-indigo:hover { border-color: rgb(99, 102, 241); }
            .btn-emerald:hover { border-color: rgb(16, 185, 129); }
            .btn-amber:hover { border-color: rgb(245, 158, 11); }
            .btn-sky:hover { border-color: rgb(14, 165, 233); }

            .icon-box {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0.75rem;
                border-radius: 0.5rem;
                color: white;
                transition: transform 0.3s ease;
            }
            .quick-action-btn:hover .icon-box {
                transform: scale(1.1);
            }
            .bg-indigo { background-color: rgb(99, 102, 241); }
            .bg-emerald { background-color: rgb(16, 185, 129); }
            .bg-amber { background-color: rgb(245, 158, 11); }
            .bg-sky { background-color: rgb(14, 165, 233); }

            .title-text {
                font-weight: 700;
                font-size: 0.875rem;
                color: rgb(31, 41, 55);
                margin: 0;
            }
            .dark .title-text {
                color: rgb(243, 244, 246);
            }
            .desc-text {
                font-size: 0.75rem;
                color: rgb(107, 114, 128);
                margin-top: 0.125rem;
            }
            .dark .desc-text {
                color: rgb(156, 163, 175);
            }
            .bg-watermark {
                position: absolute;
                right: 0.75rem;
                opacity: 0.05;
                transition: opacity 0.3s ease;
                pointer-events: none;
            }
            .quick-action-btn:hover .bg-watermark {
                opacity: 0.15;
            }
        </style>

        <div class="quick-actions-container">
            <!-- Add Product -->
            <a href="{{ url('admin/products/create') }}" class="quick-action-btn btn-indigo">
                <div class="icon-box bg-indigo">
                    <x-filament::icon icon="heroicon-o-plus-circle" class="h-6 w-6" />
                </div>
                <div>
                    <h4 class="title-text">Add Product</h4>
                    <p class="desc-text">Upload new items</p>
                </div>
                <div class="bg-watermark">
                    <x-filament::icon icon="heroicon-o-plus-circle" class="h-16 w-16" style="color: rgb(99, 102, 241);" />
                </div>
            </a>

            <!-- Create Order -->
            <a href="{{ url('admin/orders/create') }}" class="quick-action-btn btn-emerald">
                <div class="icon-box bg-emerald">
                    <x-filament::icon icon="heroicon-o-shopping-bag" class="h-6 w-6" />
                </div>
                <div>
                    <h4 class="title-text">Create Order</h4>
                    <p class="desc-text">Place manual order</p>
                </div>
                <div class="bg-watermark">
                    <x-filament::icon icon="heroicon-o-shopping-bag" class="h-16 w-16" style="color: rgb(16, 185, 129);" />
                </div>
            </a>

            <!-- Store Settings -->
            <a href="{{ url('admin/store-settings') }}" class="quick-action-btn btn-amber">
                <div class="icon-box bg-amber">
                    <x-filament::icon icon="heroicon-o-cog-6-tooth" class="h-6 w-6" />
                </div>
                <div>
                    <h4 class="title-text">Store Settings</h4>
                    <p class="desc-text">Customize storefront</p>
                </div>
                <div class="bg-watermark">
                    <x-filament::icon icon="heroicon-o-cog-6-tooth" class="h-16 w-16" style="color: rgb(245, 158, 11);" />
                </div>
            </a>

            <!-- View Storefront -->
            <a href="{{ url('/') }}" target="_blank" class="quick-action-btn btn-sky">
                <div class="icon-box bg-sky">
                    <x-filament::icon icon="heroicon-o-globe-alt" class="h-6 w-6" />
                </div>
                <div>
                    <h4 class="title-text">View Storefront</h4>
                    <p class="desc-text">Open site in new tab</p>
                </div>
                <div class="bg-watermark">
                    <x-filament::icon icon="heroicon-o-globe-alt" class="h-16 w-16" style="color: rgb(14, 165, 233);" />
                </div>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
