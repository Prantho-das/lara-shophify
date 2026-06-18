<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\OrdersChart;
use App\Filament\Widgets\QuickActions;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->spa()
            ->login()
            ->brandName(fn () => \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'LaraShophify')
            ->brandLogo(fn () => \App\Models\Setting::where('key', 'store_logo')->value('value') 
                ? asset('storage/' . \App\Models\Setting::where('key', 'store_logo')->value('value')) 
                : null)
            ->favicon(fn () => \App\Models\Setting::where('key', 'store_favicon')->value('value') 
                ? asset('storage/' . \App\Models\Setting::where('key', 'store_favicon')->value('value')) 
                : null)
            ->colors([
                'primary' => Color::Amber,
            ])
             ->globalSearch(false)
            ->maxContentWidth(Width::Full)
            ->sidebarWidth('16rem')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
               QuickActions::class,
               StatsOverView::class,
               OrdersChart::class
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('admin-custom', public_path('css/admin-custom.css')),
        ]);
    }
}
