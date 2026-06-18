<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use App\Models\Setting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class StoreSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
        protected static ?int $navigationSort = 130;
    protected static ?string $title = 'Store Settings & Theme Customizer';

    protected string $view = 'filament.pages.store-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        
        $this->form->fill([
            'store_name' => $settings['store_name'] ?? '',
            'store_logo' => $settings['store_logo'] ?? '',
            'store_favicon' => $settings['store_favicon'] ?? '',
            'store_theme' => $settings['store_theme'] ?? 'grocery',
            'allow_guest_checkout' => filter_var($settings['allow_guest_checkout'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'enable_buy_now' => filter_var($settings['enable_buy_now'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'enable_product_checkout' => filter_var($settings['enable_product_checkout'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'support_phone' => $settings['support_phone'] ?? '',
            'support_email' => $settings['support_email'] ?? '',
            'currency_symbol' => $settings['currency_symbol'] ?? '৳',
            'bkash_enabled' => filter_var($settings['bkash_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'bkash_number' => $settings['bkash_number'] ?? '',
            'nagad_enabled' => filter_var($settings['nagad_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'nagad_number' => $settings['nagad_number'] ?? '',
            'enable_shipping_zones' => filter_var($settings['enable_shipping_zones'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'enable_location_shipping' => filter_var($settings['enable_location_shipping'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'default_shipping_cost' => $settings['default_shipping_cost'] ?? 60,
            'primary_color' => $settings['primary_color'] ?? '#000000',
            'secondary_color' => $settings['secondary_color'] ?? '#ffffff',
            'store_font' => $settings['store_font'] ?? 'Outfit',
            'topbar_text' => $settings['topbar_text'] ?? '',
            'header_style' => $settings['header_style'] ?? 'normal',
            'social_links' => json_decode($settings['social_links'] ?? '[]', true),
            'seo_meta_title' => $settings['seo_meta_title'] ?? '',
            'seo_meta_description' => $settings['seo_meta_description'] ?? '',
            'privacy_policy' => $settings['privacy_policy'] ?? '',
            'terms_of_service' => $settings['terms_of_service'] ?? '',
            'refund_policy' => $settings['refund_policy'] ?? '',
            'homepage_sections' => json_decode($settings['homepage_sections'] ?? '[]', true),
            'footer_sections' => json_decode($settings['footer_sections'] ?? '[]', true),
            'gtm_id' => $settings['gtm_id'] ?? '',
            'facebook_pixel_id' => $settings['facebook_pixel_id'] ?? '',
            'facebook_capi_enabled' => filter_var($settings['facebook_capi_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'facebook_capi_token' => $settings['facebook_capi_token'] ?? '',
            'facebook_capi_test_code' => $settings['facebook_capi_test_code'] ?? '',
            'tiktok_pixel_id' => $settings['tiktok_pixel_id'] ?? '',
            'custom_head_scripts' => $settings['custom_head_scripts'] ?? '',
            'custom_body_scripts' => $settings['custom_body_scripts'] ?? '',
            'steadfast_api_key' => $settings['steadfast_api_key'] ?? '',
            'steadfast_secret_key' => $settings['steadfast_secret_key'] ?? '',
            'pathao_client_id' => $settings['pathao_client_id'] ?? '',
            'pathao_client_secret' => $settings['pathao_client_secret'] ?? '',
            'pathao_username' => $settings['pathao_username'] ?? '',
            'pathao_password' => $settings['pathao_password'] ?? '',
            'pathao_store_id' => $settings['pathao_store_id'] ?? '',
            'pathao_sandbox' => filter_var($settings['pathao_sandbox'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'popup_enabled' => filter_var($settings['popup_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'popup_type' => $settings['popup_type'] ?? 'newsletter',
            'popup_title' => $settings['popup_title'] ?? '',
            'popup_content' => $settings['popup_content'] ?? '',
            'popup_image' => $settings['popup_image'] ?? '',
            'popup_link' => $settings['popup_link'] ?? '',
            'popup_delay' => $settings['popup_delay'] ?? 3,
            'popup_cookie_lifetime' => $settings['popup_cookie_lifetime'] ?? 1,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                TextInput::make('store_name')->required(),
                                FileUpload::make('store_logo')->image()
                                    ->disk('public')
                                    ->directory('settings'),
                                FileUpload::make('store_favicon')
                                    ->label('Store Favicon')
                                    ->image()
                                    ->disk('public')
                                    ->directory('settings'),
                                Select::make('store_theme')
                                    ->label('Store Theme Preset')
                                    ->options([
                                        'grocery' => 'Grocery Store',
                                        'fashion' => 'Fashion/Lifestyle',
                                        'electronics' => 'Electronics/Gadgets',
                                    ])
                                    ->default('grocery')
                                    ->required(),
                                Toggle::make('allow_guest_checkout')
                                    ->label('Allow Customers to Checkout as Guest')
                                    ->default(true),
                                Toggle::make('enable_buy_now')
                                    ->label("Enable 'Buy Now' Button (Direct Order)")
                                    ->default(true),
                                Toggle::make('enable_product_checkout')
                                    ->label("Enable Product-wise Direct Checkout Form on Product Details Page")
                                    ->default(true),
                                TextInput::make('support_phone'),
                                TextInput::make('support_email')->email(),
                                TextInput::make('currency_symbol')->default('৳'),
                            ]),
                        Tabs\Tab::make('Payments')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Toggle::make('bkash_enabled')->label('Enable bKash'),
                                TextInput::make('bkash_number')->label('bKash Merchant/Personal Number'),
                                Toggle::make('nagad_enabled')->label('Enable Nagad'),
                                TextInput::make('nagad_number')->label('Nagad Merchant/Personal Number'),
                            ]),
                        Tabs\Tab::make('Shipping & Delivery')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)->schema([
                                    Toggle::make('enable_shipping_zones')
                                        ->label('Enable Fixed Shipping Zones Selector')
                                        ->default(true),
                                    Toggle::make('enable_location_shipping')
                                        ->label('Enable Country / District / Area Based Address Selectors')
                                        ->default(false),
                                    TextInput::make('default_shipping_cost')
                                        ->numeric()
                                        ->default(60)
                                        ->label('Fallback Default Shipping Cost (৳)'),
                                ]),
                                
                                \Filament\Schemas\Components\Fieldset::make('Steadfast Courier API Settings')
                                    ->schema([
                                        TextInput::make('steadfast_api_key')->label('Steadfast API Key'),
                                        TextInput::make('steadfast_secret_key')->label('Steadfast Secret Key'),
                                    ])->columns(2),

                                \Filament\Schemas\Components\Fieldset::make('Pathao Courier API Settings')
                                    ->schema([
                                        TextInput::make('pathao_client_id')->label('Pathao Client ID'),
                                        TextInput::make('pathao_client_secret')->label('Pathao Client Secret'),
                                        TextInput::make('pathao_username')->label('Pathao Username/Email'),
                                        TextInput::make('pathao_password')->label('Pathao Password')->password()->dehydrateStateUsing(fn ($state) => $state),
                                        TextInput::make('pathao_store_id')->label('Pathao Store ID'),
                                        Toggle::make('pathao_sandbox')->label('Enable Pathao Sandbox Mode')->default(true),
                                    ])->columns(3),
                            ]),
                        Tabs\Tab::make('Theme & Appearance')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(2)->schema([
                                    ColorPicker::make('primary_color')
                                        ->label('Theme Primary Color')
                                        ->helperText('Your brand\'s main color used for buttons, active links, and highlights.'),
                                    ColorPicker::make('secondary_color')
                                        ->label('Theme Secondary Color')
                                        ->helperText('Use a very light pastel/soft version of primary color (e.g. #fef2f2). Leave blank to auto-generate from primary.'),
                                ]),
                                Select::make('store_font')
                                    ->label('Store Font')
                                    ->options([
                                        'Outfit' => 'Outfit (Modern / Sleek)',
                                        'Inter' => 'Inter (Clean / Corporate)',
                                        'Roboto' => 'Roboto (Standard)',
                                        'Poppins' => 'Poppins (Playful / Rounded)',
                                        'Montserrat' => 'Montserrat (Bold / Elegant)',
                                        'Playfair Display' => 'Playfair Display (Serif / Luxury)',
                                        'Lora' => 'Lora (Serif / Editorial)',
                                        'Oswald' => 'Oswald (Condense / Sporty)',
                                        'Plus Jakarta Sans' => 'Plus Jakarta Sans (Premium / Tech)',
                                    ])
                                    ->default('Outfit')
                                    ->required(),
                                Select::make('header_style')->options([
                                    'normal' => 'Normal Header',
                                    'sticky' => 'Sticky Header (Stays on top)',
                                ])->default('normal'),
                                TextInput::make('topbar_text')->label('Announcement Bar Text')->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Popups & Modals')
                            ->icon('heroicon-o-chat-bubble-bottom-center-text')
                            ->schema([
                                Toggle::make('popup_enabled')
                                    ->label('Enable Popups on Storefront')
                                    ->default(false)
                                    ->live(),
                                \Filament\Schemas\Components\Grid::make(2)->schema([
                                    Select::make('popup_type')
                                        ->options([
                                            'newsletter' => 'Newsletter Signup (Form)',
                                            'promotion' => 'Promotional Image Banner',
                                            'announcement' => 'Standard Announcement Alert',
                                        ])
                                        ->default('newsletter')
                                        ->required()
                                        ->live(),
                                    TextInput::make('popup_title')
                                        ->label('Popup Heading Title')
                                        ->required(fn($get) => $get('popup_enabled') && $get('popup_type') !== 'promotion'),
                                ])->visible(fn($get) => $get('popup_enabled')),
                                RichEditor::make('popup_content')
                                    ->label('Popup Body / Content Text')
                                    ->visible(fn($get) => $get('popup_enabled') && $get('popup_type') !== 'promotion'),
                                FileUpload::make('popup_image')
                                    ->label('Popup Banner Image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('settings')
                                    ->visible(fn($get) => $get('popup_enabled') && $get('popup_type') === 'promotion'),
                                TextInput::make('popup_link')
                                    ->label('Popup Redirect/Action Link (e.g. /shop)')
                                    ->visible(fn($get) => $get('popup_enabled')),
                                \Filament\Schemas\Components\Grid::make(2)->schema([
                                    TextInput::make('popup_delay')
                                        ->label('Trigger Delay (seconds)')
                                        ->numeric()
                                        ->default(3)
                                        ->required(fn($get) => $get('popup_enabled')),
                                    TextInput::make('popup_cookie_lifetime')
                                        ->label('Cookie Lifetime / Hide Duration (days)')
                                        ->numeric()
                                        ->default(1)
                                        ->required(fn($get) => $get('popup_enabled')),
                                ])->visible(fn($get) => $get('popup_enabled')),
                            ]),
                        Tabs\Tab::make('SEO & Social')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                \Filament\Schemas\Components\Fieldset::make('Search Engine Optimization')->schema([
                                    TextInput::make('seo_meta_title')->label('Global Meta Title'),
                                    \Filament\Forms\Components\Textarea::make('seo_meta_description')->label('Global Meta Description')->columnSpanFull(),
                                ]),
                                \Filament\Forms\Components\Repeater::make('social_links')
                                    ->label('Dynamic Social Media Links')
                                    ->schema([
                                        Select::make('platform')
                                            ->options([
                                                'facebook' => 'Facebook',
                                                'instagram' => 'Instagram',
                                                'youtube' => 'YouTube',
                                                'twitter' => 'Twitter / X',
                                                'tiktok' => 'TikTok',
                                                'linkedin' => 'LinkedIn',
                                                'pinterest' => 'Pinterest',
                                                'whatsapp' => 'WhatsApp',
                                                'custom' => 'Custom Link',
                                            ])
                                            ->required()
                                            ->reactive(),
                                        TextInput::make('url')
                                            ->label('URL')
                                            ->url()
                                            ->required(),
                                        Select::make('custom_icon')
                                            ->label('Select Icon')
                                            ->options([
                                                'fa-solid fa-link' => 'Link / Website Icon',
                                                'fa-solid fa-globe' => 'Globe / Web Icon',
                                                'fa-solid fa-envelope' => 'Envelope / Email Icon',
                                                'fa-solid fa-phone' => 'Phone Icon',
                                                'fa-solid fa-heart' => 'Heart Icon',
                                                'fa-solid fa-star' => 'Star Icon',
                                                'fa-solid fa-share-nodes' => 'Share Icon',
                                                'fa-solid fa-comments' => 'Chat Icon',
                                            ])
                                            ->visible(fn($get) => $get('platform') === 'custom')
                                            ->default('fa-solid fa-link')
                                            ->required(fn($get) => $get('platform') === 'custom'),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->collapsible(),
                            ]),
                        Tabs\Tab::make('Store Policies')
                            ->icon('heroicon-o-scale')
                            ->schema([
                                RichEditor::make('privacy_policy')->label('Privacy Policy')->columnSpanFull(),
                                RichEditor::make('terms_of_service')->label('Terms of Service')->columnSpanFull(),
                                RichEditor::make('refund_policy')->label('Refund & Return Policy')->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Homepage Layout')
                            ->icon('heroicon-o-view-columns')
                            ->schema([
                                Builder::make('homepage_sections')
                                    ->label('Drag and Drop sections to design your homepage')
                                    ->blocks([
                                        Builder\Block::make('slider')
                                            ->label('Main Banners/Slider')
                                            ->icon('heroicon-o-photo')
                                            ->schema([
                                                TextInput::make('title')->label('Section Title (Optional)'),
                                                Select::make('banner_ids')
                                                    ->label('Select Banners to Show')
                                                    ->multiple()
                                                    ->options(fn() => \App\Models\Banner::pluck('title', 'id')->toArray()),
                                            ]),
                                        Builder\Block::make('hero_split')
                                            ->label('Split Hero Banner')
                                            ->icon('heroicon-o-newspaper')
                                            ->schema([
                                                TextInput::make('title')->label('Main Title')->required(),
                                                TextInput::make('subtitle')->label('Subtitle/Description'),
                                                TextInput::make('badge_text')->label('Small Badge Text (e.g. NEW ARRIVAL)'),
                                                TextInput::make('button_text')->label('Button Text')->default('Shop Now'),
                                                TextInput::make('button_url')->label('Button Link')->default('/shop'),
                                                FileUpload::make('image')->image() ->disk('public')->directory('settings')->label('Hero Image')->required(),
                                                Select::make('text_alignment')
                                                    ->options([
                                                        'left' => 'Text on Left, Image on Right',
                                                        'right' => 'Text on Right, Image on Left',
                                                    ])->default('left')->required(),
                                            ]),
                                        Builder\Block::make('category_pills')
                                            ->label('Category Pills (Quick Links)')
                                            ->icon('heroicon-o-bookmark')
                                            ->schema([
                                                TextInput::make('title')->label('Section Title')->default('Explore Categories'),
                                                Select::make('category_ids')
                                                    ->label('Select Categories')
                                                    ->multiple()
                                                    ->options(fn() => \App\Models\Category::pluck('name', 'id')->toArray()),
                                            ]),
                                        Builder\Block::make('tabbed_products')
                                            ->label('Tabbed Products Grid')
                                            ->icon('heroicon-o-squares-plus')
                                            ->schema([
                                                TextInput::make('title')->label('Section Title')->default('Our Collections'),
                                                \Filament\Forms\Components\Repeater::make('tabs')
                                                    ->label('Tabs Setup')
                                                    ->schema([
                                                        TextInput::make('tab_title')->label('Tab Name (e.g. Best Sellers)')->required(),
                                                        Select::make('product_ids')
                                                            ->label('Select Products')
                                                            ->multiple()
                                                            ->options(fn() => \App\Models\Product::pluck('name', 'id')->toArray())
                                                            ->required(),
                                                    ])
                                                    ->columns(2)
                                                    ->collapsible()
                                                    ->default([
                                                        ['tab_title' => 'New Arrivals'],
                                                        ['tab_title' => 'Best Sellers']
                                                    ]),
                                            ]),
                                        Builder\Block::make('deal_of_the_day')
                                            ->label('Deal of the Day (Countdown)')
                                            ->icon('heroicon-o-clock')
                                            ->schema([
                                                TextInput::make('title')->label('Section Title')->default('Deal Of The Day'),
                                                Select::make('product_id')
                                                    ->label('Select Featured Product')
                                                    ->options(fn() => \App\Models\Product::pluck('name', 'id')->toArray())
                                                    ->required(),
                                                \Filament\Forms\Components\DateTimePicker::make('countdown_end')
                                                    ->label('Countdown Ends At')
                                                    ->required(),
                                                TextInput::make('stock_limit')
                                                    ->numeric()
                                                    ->label('Initial Stock Limit (e.g. 50)')
                                                    ->default(50),
                                                TextInput::make('stock_sold')
                                                    ->numeric()
                                                    ->label('Simulated Items Sold (e.g. 35)')
                                                    ->default(35),
                                            ]),
                                        Builder\Block::make('featured_categories')
                                            ->label('Featured Categories Grid')
                                            ->icon('heroicon-o-tag')
                                            ->schema([
                                                TextInput::make('title')->label('Section Title')->default('Shop by Category'),
                                                Select::make('category_ids')
                                                    ->label('Select Categories to Show')
                                                    ->multiple()
                                                    ->options(fn() => \App\Models\Category::pluck('name', 'id')->toArray()),
                                            ]),
                                        Builder\Block::make('trending_products')
                                            ->label('Trending Products Grid')
                                            ->icon('heroicon-o-sparkles')
                                            ->schema([
                                                TextInput::make('title')->label('Section Title')->default('Trending Now'),
                                                Select::make('product_ids')
                                                    ->label('Select Specific Products (Leave empty to show latest)')
                                                    ->multiple()
                                                    ->options(fn() => \App\Models\Product::pluck('name', 'id')->toArray()),
                                                TextInput::make('limit')->numeric()->default(8)->label('Number of Products (If no specific selected)'),
                                            ]),
                                        Builder\Block::make('product_carousel')
                                            ->label('Product Slider / Carousel')
                                            ->icon('heroicon-o-queue-list')
                                            ->schema([
                                                TextInput::make('title')->label('Section Title')->default('Special Offers'),
                                                Select::make('product_ids')
                                                    ->label('Select Specific Products')
                                                    ->multiple()
                                                    ->options(fn() => \App\Models\Product::pluck('name', 'id')->toArray()),
                                                TextInput::make('limit')->numeric()->default(8)->label('Limit (If no products selected)'),
                                            ]),
                                        Builder\Block::make('brand_grid')
                                            ->label('Brands Grid/Slider')
                                            ->icon('heroicon-o-square-3-stack-3d')
                                            ->schema([
                                                TextInput::make('title')->label('Section Title')->default('Shop by Brand'),
                                                Select::make('brand_ids')
                                                    ->label('Select Brands (Leave empty to show all)')
                                                    ->multiple()
                                                    ->options(fn() => \App\Models\Brand::pluck('name', 'id')->toArray()),
                                            ]),
                                        Builder\Block::make('testimonials')
                                            ->label('Customer Testimonials')
                                            ->icon('heroicon-o-chat-bubble-bottom-center-text')
                                            ->schema([
                                                TextInput::make('title')->label('Section Title')->default('What Our Customers Say'),
                                                \Filament\Forms\Components\Repeater::make('items')
                                                    ->label('Testimonials')
                                                    ->schema([
                                                        TextInput::make('name')->required(),
                                                        TextInput::make('role')->label('Role / Designation')->default('Verified Customer'),
                                                        TextInput::make('rating')->numeric()->default(5)->minValue(1)->maxValue(5)->required(),
                                                        \Filament\Forms\Components\Textarea::make('comment')->required(),
                                                        FileUpload::make('avatar') ->disk('public')->image()->directory('settings')->label('Avatar (Optional)'),
                                                    ])
                                                    ->columns(2)
                                                    ->collapsible(),
                                            ]),
                                        Builder\Block::make('trust_badges')
                                            ->label('Trust Badges / Features')
                                            ->icon('heroicon-o-shield-check')
                                            ->schema([
                                                \Filament\Forms\Components\Repeater::make('badges')
                                                    ->label('Badges')
                                                    ->schema([
                                                        Select::make('icon')
                                                            ->options([
                                                                'fa-solid fa-truck-fast' => 'Delivery Truck',
                                                                'fa-solid fa-rotate-left' => 'Refund / Return',
                                                                'fa-solid fa-headset' => 'Headset Support',
                                                                'fa-solid fa-shield-halved' => 'Secure Payment Shield',
                                                                'fa-solid fa-tags' => 'Tags / Discount',
                                                                'fa-solid fa-thumbs-up' => 'Thumbs Up Quality',
                                                            ])
                                                            ->required(),
                                                        TextInput::make('title')->required(),
                                                        TextInput::make('subtitle')->required(),
                                                    ])
                                                    ->columns(3)
                                                    ->collapsible(),
                                            ]),
                                        Builder\Block::make('newsletter_signup')
                                            ->label('Newsletter Signup')
                                            ->icon('heroicon-o-envelope-open')
                                            ->schema([
                                                TextInput::make('title')->label('Title')->default('Subscribe to our Newsletter'),
                                                RichEditor::make('content')->default('<p>Get 10% discount on your first order. No spam, unsubscribe anytime.</p>'),
                                                TextInput::make('button_text')->default('Subscribe'),
                                            ]),
                                        Builder\Block::make('promo_banner')
                                            ->label('Promotional Banner (Image)')
                                            ->icon('heroicon-o-megaphone')
                                            ->schema([
                                                FileUpload::make('image') ->disk('public')->image()->directory('settings'),
                                                TextInput::make('link')->url()->label('Banner Link'),
                                            ]),
                                        Builder\Block::make('text_block')
                                            ->label('Custom Text Block')
                                            ->icon('heroicon-o-document-text')
                                            ->schema([
                                                TextInput::make('title')->label('Title'),
                                                RichEditor::make('content')->required(),
                                            ]),
                                    ])->collapsible(),
                            ]),
                        Tabs\Tab::make('Footer Layout')
                            ->icon('heroicon-o-table-cells')
                            ->schema([
                                Builder::make('footer_sections')
                                    ->label('Customize Footer Columns (Max 4 columns)')
                                    ->maxItems(4)
                                    ->blocks([
                                        Builder\Block::make('text_block')
                                            ->label('Rich Text / About Us')
                                            ->icon('heroicon-o-document-text')
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                RichEditor::make('content')->required(),
                                                Toggle::make('show_social_links')->label('Show Social Media Icons')->default(true),
                                            ]),
                                        Builder\Block::make('menu_block')
                                            ->label('Navigation Menu')
                                            ->icon('heroicon-o-link')
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                Select::make('menu_id')
                                                    ->label('Select Menu')
                                                    ->options(fn() => \App\Models\Menu::pluck('name', 'id')->toArray())
                                                    ->required(),
                                            ]),
                                        Builder\Block::make('contact_block')
                                            ->label('Contact / Support Info')
                                            ->icon('heroicon-o-envelope')
                                            ->schema([
                                                TextInput::make('title')->default('Support Center'),
                                                TextInput::make('phone'),
                                                TextInput::make('email')->email(),
                                                TextInput::make('address'),
                                            ]),
                                    ])->collapsible(),
                            ]),
                        Tabs\Tab::make('Marketing & Pixels')
                            ->icon('heroicon-o-presentation-chart-line')
                            ->schema([
                                \Filament\Schemas\Components\Fieldset::make('Analytics & Pixel IDs')->schema([
                                    TextInput::make('gtm_id')->label('Google Tag Manager ID')->placeholder('GTM-XXXXXX'),
                                    TextInput::make('facebook_pixel_id')->label('Facebook Pixel ID')->placeholder('1234567890'),
                                    TextInput::make('tiktok_pixel_id')->label('TikTok Pixel ID')->placeholder('C1234567890'),
                                ])->columns(3),
                                \Filament\Schemas\Components\Fieldset::make('Facebook Conversion API (CAPI)')->schema([
                                    Toggle::make('facebook_capi_enabled')
                                        ->label('Enable Facebook Conversion API')
                                        ->default(false)
                                        ->reactive(),
                                    TextInput::make('facebook_capi_token')
                                        ->label('CAPI Access Token')
                                        ->password()
                                        ->dehydrateStateUsing(fn ($state) => $state)
                                        ->required(fn($get) => $get('facebook_capi_enabled') === true)
                                        ->columnSpan(2),
                                    TextInput::make('facebook_capi_test_code')
                                        ->label('CAPI Test Event Code (Optional)')
                                        ->placeholder('TEST12345')
                                        ->helperText('Use this to test events inside Events Manager > Test Events tab.'),
                                ])->columns(4),
                                \Filament\Schemas\Components\Fieldset::make('Custom Scripts / Pixels')->schema([
                                    \Filament\Forms\Components\Textarea::make('custom_head_scripts')
                                        ->label('Custom Header Scripts (Inside <head>)')
                                        ->rows(4)
                                        ->columnSpanFull()
                                        ->placeholder('<!-- Global site tag (gtag.js) -->'),
                                    \Filament\Forms\Components\Textarea::make('custom_body_scripts')
                                        ->label('Custom Body Scripts (Inside <body>)')
                                        ->rows(4)
                                        ->columnSpanFull()
                                        ->placeholder('<!-- Facebook Noscript or Custom Widgets -->...'),
                                ]),
                            ]),
                        Tabs\Tab::make('Quick Links & Feeds')
                            ->icon('heroicon-o-link')
                            ->schema([
                                \Filament\Schemas\Components\Fieldset::make('Storefront Policy Pages')->schema([
                                    \Filament\Forms\Components\Placeholder::make('privacy_policy_url')
                                        ->label('Privacy Policy URL')
                                        ->content(fn() => new \Illuminate\Support\HtmlString('<a href="' . url('/page/privacy-policy') . '" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline font-medium flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>' . url('/page/privacy-policy') . '</a>')),
                                    \Filament\Forms\Components\Placeholder::make('terms_url')
                                        ->label('Terms of Service URL')
                                        ->content(fn() => new \Illuminate\Support\HtmlString('<a href="' . url('/page/terms-of-service') . '" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline font-medium flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>' . url('/page/terms-of-service') . '</a>')),
                                    \Filament\Forms\Components\Placeholder::make('refund_url')
                                        ->label('Refund & Return Policy URL')
                                        ->content(fn() => new \Illuminate\Support\HtmlString('<a href="' . url('/page/refund-policy') . '" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline font-medium flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>' . url('/page/refund-policy') . '</a>')),
                                ])->columns(3),
                                \Filament\Schemas\Components\Fieldset::make('Search Engine & Marketing Feeds')->schema([
                                    \Filament\Forms\Components\Placeholder::make('sitemap_url')
                                        ->label('XML Sitemap Feed URL')
                                        ->content(fn() => new \Illuminate\Support\HtmlString('<a href="' . url('/sitemap.xml') . '" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline font-mono flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>' . url('/sitemap.xml') . '</a>')),
                                    \Filament\Forms\Components\Placeholder::make('facebook_catalog_url')
                                        ->label('Facebook Product Catalogue XML Feed')
                                        ->content(fn() => new \Illuminate\Support\HtmlString('<a href="' . url('/feed/facebook-catalog.xml') . '" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline font-mono flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>' . url('/feed/facebook-catalog.xml') . '</a>')),
                                ])->columns(2),
                            ]),
                    ])->columnSpanFull()
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        try {
            $data = $this->form->getState();

            foreach ($data as $key => $value) {
                $val = is_array($value) ? json_encode($value) : $value;
                Setting::updateOrCreate(['key' => $key], ['value' => $val]);
            }

            Notification::make()
                ->title('Settings Saved Successfully!')
                ->success()
                ->send();
        } catch (\Illuminate\Validation\ValidationException $exception) {
            \Log::error('StoreSettings Validation Errors: ' . json_encode($exception->errors()));
            
            $errorMsg = implode('<br>', array_map(function($field, $msgs) {
                $cleanField = $field;
                if (str_starts_with($cleanField, 'data.')) {
                    $cleanField = substr($cleanField, 5);
                }
                
                // Replace builder UUID paths with clean labels
                $cleanField = preg_replace('/homepage_sections\.[a-zA-Z0-9-]+\.data\./i', 'Homepage: ', $cleanField);
                $cleanField = preg_replace('/footer_sections\.[a-zA-Z0-9-]+\.data\./i', 'Footer: ', $cleanField);
                $cleanField = preg_replace('/social_links\.\d+\./i', 'Social Links: ', $cleanField);
                
                // Clean remaining separators and capitalize
                $cleanField = str_replace(['.', '_'], [' ', ' '], $cleanField);
                $cleanField = ucwords(trim($cleanField));
                
                return '<strong>' . $cleanField . '</strong>: ' . implode('; ', $msgs);
            }, array_keys($exception->errors()), $exception->errors()));

            Notification::make()
                ->title('Validation Error')
                ->body($errorMsg)
                ->danger()
                ->send();
            
            throw $exception;
        }
    }
}
