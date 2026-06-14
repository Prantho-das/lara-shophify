<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Banner;
use App\Models\ShippingZone;
use App\Models\Coupon;
use App\Models\Country;
use App\Models\District;
use Illuminate\Support\Str;

class StorefrontSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Store Settings
        $settings = [
            'store_name' => 'Jarvis Smart Store',
            'store_theme' => 'grocery', // Default theme
            'currency_symbol' => '৳',
            'support_phone' => '+8801700000000',
            'support_email' => 'support@jarvisstore.com',
            'primary_color' => '#22c55e',
            'secondary_color' => '#f0fdf4',
            'topbar_text' => '🔥 Exclusive Offer: Use code JARVIS10 to get 10% Discount!',
            'header_style' => 'sticky',
            'social_links' => json_encode([
                ['platform' => 'facebook', 'url' => 'https://facebook.com', 'custom_icon' => ''],
                ['platform' => 'instagram', 'url' => 'https://instagram.com', 'custom_icon' => ''],
                ['platform' => 'youtube', 'url' => 'https://youtube.com', 'custom_icon' => ''],
                ['platform' => 'twitter', 'url' => 'https://x.com', 'custom_icon' => '']
            ]),
            'seo_meta_title' => 'Jarvis Premium Store - Grocery, Fashion & Tech',
            'seo_meta_description' => 'The next generation Laravel & Livewire powered dynamic Shopify clone ecommerce storefront.',
            'privacy_policy' => '<h2>Privacy Policy</h2><p>This is the privacy policy of Jarvis Premium Store. We value your data security.</p>',
            'terms_of_service' => '<h2>Terms of Service</h2><p>Welcome to our store. By accessing the site, you agree to our rules and terms.</p>',
            'refund_policy' => '<h2>Refund & Return Policy</h2><p>We provide a 7-day refund policy on all eligible purchases.</p>',
            'homepage_sections' => json_encode([
                ['type' => 'slider', 'data' => []],
                ['type' => 'featured_categories', 'data' => ['title' => 'Browse Our Premium Categories']],
                ['type' => 'trending_products', 'data' => ['title' => 'Hot Deals & Trending Now', 'limit' => 8]],
                ['type' => 'promo_banner', 'data' => ['image' => 'settings/promo_wide.jpg', 'link' => '/shop']],
                ['type' => 'text_block', 'data' => ['title' => 'Organic Freshness & Premium Quality', 'content' => '<p>Welcome to Jarvis Smart Store. We offer fresh organic food, trendiest fashion apparel, and premium electronics at the best rates in Bangladesh. Enjoy cash on delivery, and instant mobile refund payments!</p>']]
            ]),
            'footer_sections' => json_encode([
                [
                    'type' => 'text_block',
                    'data' => [
                        'title' => 'About Jarvis Store',
                        'content' => '<p>The next generation Laravel & Livewire powered dynamic Shopify clone ecommerce storefront. Built for speed, high conversion and responsive designs.</p>',
                        'show_social_links' => true
                    ]
                ],
                [
                    'type' => 'menu_block',
                    'data' => [
                        'title' => 'Quick Links',
                        'menu_id' => 1
                    ]
                ],
                [
                    'type' => 'contact_block',
                    'data' => [
                        'title' => 'Customer Support',
                        'phone' => '+8801700000000',
                        'email' => 'support@jarvisstore.com',
                        'address' => 'Dhaka, Bangladesh'
                    ]
                ]
            ])
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // 2. Seed Banners/Slider
        Banner::truncate();
        Banner::create([
            'title' => 'Fresh Organic Groceries',
            'image' => 'settings/banner1.jpg',
            'link' => '/shop',
            'status' => 'active'
        ]);
        Banner::create([
            'title' => 'Trending Fashion Wear',
            'image' => 'settings/banner2.jpg',
            'link' => '/shop',
            'status' => 'active'
        ]);
        Banner::create([
            'title' => 'Premium Electronics & Accessories',
            'image' => 'settings/banner3.jpg',
            'link' => '/shop',
            'status' => 'active'
        ]);

        // 3. Seed Categories
        Category::truncate();
        $groceryCat = Category::create([
            'name' => 'Organic Grocery',
            'slug' => 'organic-grocery',
            'description' => 'Fresh farm vegetables, dairy items and organic fruits.',
            'is_active' => true,
            'sort_order' => 1
        ]);

        $fashionCat = Category::create([
            'name' => 'Fashion & Lifestyle',
            'slug' => 'fashion-lifestyle',
            'description' => 'Modern clothing, designer shoes, and trendiest fashion gear.',
            'is_active' => true,
            'sort_order' => 2
        ]);

        $electronicsCat = Category::create([
            'name' => 'Electronics & Tech',
            'slug' => 'electronics-tech',
            'description' => 'Smartphones, smartwatches, earphones and accessories.',
            'is_active' => true,
            'sort_order' => 3
        ]);

        // 4. Seed Products
        Product::truncate();
        ProductImage::truncate();
        ProductVariant::truncate();
        
        // Grocery Products
        $p1 = Product::create([
            'name' => 'Organic Fresh Apples (1kg)',
            'slug' => 'organic-fresh-apples',
            'description' => '<p>Crispy, crunchy organic fresh red apples direct from Northern farms.</p>',
            'base_price' => 280.00,
            'compare_price' => 320.00,
            'category_id' => $groceryCat->id,
            'tax_rate' => 5.00,
            'barcode' => 'APPLE1KG',
            'status' => 'active'
        ]);
        $p1->images()->create([
            'path' => 'settings/apples.jpg',
            'is_primary' => true
        ]);

        $p2 = Product::create([
            'name' => 'Farm Fresh Broccoli (500g)',
            'slug' => 'farm-fresh-broccoli',
            'description' => '<p>Rich in fiber, minerals and antioxidants. Organic broccoli.</p>',
            'base_price' => 90.00,
            'compare_price' => 110.00,
            'category_id' => $groceryCat->id,
            'tax_rate' => 0.00,
            'barcode' => 'BROCCOLI500G',
            'status' => 'active'
        ]);
        $p2->images()->create([
            'path' => 'settings/broccoli.jpg',
            'is_primary' => true
        ]);

        // Fashion Products
        $p3 = Product::create([
            'name' => 'Premium Black Cotton Polo Shirt',
            'slug' => 'premium-black-polo',
            'description' => '<p>High-quality 100% breathable organic cotton slim-fit polo shirt.</p>',
            'base_price' => 850.00,
            'compare_price' => 1200.00,
            'category_id' => $fashionCat->id,
            'tax_rate' => 7.50,
            'barcode' => 'POLOSHIRT',
            'status' => 'active',
            'has_variants' => true,
            'attributes' => ['Size' => ['M', 'L', 'XL']]
        ]);
        $p3->images()->create([
            'path' => 'settings/polo.jpg',
            'is_primary' => true
        ]);

        $p3->variants()->create([
            'attribute_values' => ['Size' => 'M'],
            'sku' => 'POLO-M',
            'price' => 850.00,
            'compare_price' => 1200.00,
            'stock_quantity' => 50,
            'is_active' => true,
        ]);
        $p3->variants()->create([
            'attribute_values' => ['Size' => 'L'],
            'sku' => 'POLO-L',
            'price' => 950.00,
            'compare_price' => 1300.00,
            'stock_quantity' => 30,
            'is_active' => true,
        ]);
        $p3->variants()->create([
            'attribute_values' => ['Size' => 'XL'],
            'sku' => 'POLO-XL',
            'price' => 1050.00,
            'compare_price' => 1400.00,
            'stock_quantity' => 15,
            'is_active' => true,
        ]);

        $p4 = Product::create([
            'name' => 'Leather Wallet for Men',
            'slug' => 'leather-wallet-men',
            'description' => '<p>Genuine leather bi-fold slim wallet with multi-card slots.</p>',
            'base_price' => 1500.00,
            'compare_price' => 1800.00,
            'category_id' => $fashionCat->id,
            'tax_rate' => 15.00,
            'barcode' => 'LEATHERWALLET',
            'status' => 'active'
        ]);
        $p4->images()->create([
            'path' => 'settings/wallet.jpg',
            'is_primary' => true
        ]);

        // Electronics Products
        $p5 = Product::create([
            'name' => 'Wireless ANC Earbuds Pro',
            'slug' => 'wireless-anc-earbuds',
            'description' => '<p>Active Noise Cancellation (ANC) with deep bass, smart control, and 30-hour playback.</p>',
            'base_price' => 3500.00,
            'compare_price' => 4500.00,
            'category_id' => $electronicsCat->id,
            'tax_rate' => 15.00,
            'barcode' => 'ANCEARBUDS',
            'status' => 'active'
        ]);
        $p5->images()->create([
            'path' => 'settings/earbuds.jpg',
            'is_primary' => true
        ]);

        $p6 = Product::create([
            'name' => 'Smart Watch Band 8',
            'slug' => 'smart-watch-band-8',
            'description' => '<p>AMOLED screen smartwatch with multi-sports tracking and blood oxygen monitoring.</p>',
            'base_price' => 4200.00,
            'compare_price' => 5000.00,
            'category_id' => $electronicsCat->id,
            'tax_rate' => 15.00,
            'barcode' => 'SMARTWATCH8',
            'status' => 'active'
        ]);
        $p6->images()->create([
            'path' => 'settings/smartwatch.jpg',
            'is_primary' => true
        ]);

        // 5. Seed Menus
        Menu::truncate();
        MenuItem::truncate();

        // Header Menu
        $headerMenu = Menu::create([
            'name' => 'Header Navigation',
            'location' => 'header'
        ]);

        MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'Organic Grocery',
            'url' => '/category/organic-grocery',
            'sort_order' => 1
        ]);

        MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'Fashion apparel',
            'url' => '/category/fashion-lifestyle',
            'sort_order' => 2
        ]);

        MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'Tech gadgets',
            'url' => '/category/electronics-tech',
            'sort_order' => 3
        ]);

        // Footer Menu
        $footerMenu = Menu::create([
            'name' => 'Footer Navigation',
            'location' => 'footer'
        ]);

        MenuItem::create([
            'menu_id' => $footerMenu->id,
            'title' => 'Help Center',
            'url' => '/page/privacy-policy',
            'sort_order' => 1
        ]);

        // 6. Seed Shipping Zones
        ShippingZone::truncate();
        ShippingZone::create([
            'name' => 'Inside Dhaka',
            'cost' => 80.00,
            'status' => 'active'
        ]);
        ShippingZone::create([
            'name' => 'Outside Dhaka',
            'cost' => 150.00,
            'status' => 'active'
        ]);

        // 7. Seed Location Rates
        Country::truncate();
        District::truncate();
        \App\Models\LocationRate::truncate();

        $bd = Country::create(['name' => 'Bangladesh', 'code' => 'BD']);
        $in = Country::create(['name' => 'India', 'code' => 'IN']);

        $dhaka = District::create(['country_id' => $bd->id, 'name' => 'Dhaka']);
        $ctg = District::create(['country_id' => $bd->id, 'name' => 'Chittagong']);
        $sylhet = District::create(['country_id' => $bd->id, 'name' => 'Sylhet']);

        $wb = District::create(['country_id' => $in->id, 'name' => 'West Bengal']);

        \App\Models\LocationRate::create([
            'district_id' => $dhaka->id,
            'area' => 'Dhanmondi',
            'charge' => 80.00,
            'status' => 'active'
        ]);
        \App\Models\LocationRate::create([
            'district_id' => $dhaka->id,
            'area' => 'Gulshan',
            'charge' => 90.00,
            'status' => 'active'
        ]);
        \App\Models\LocationRate::create([
            'district_id' => $ctg->id,
            'area' => 'Agrabad',
            'charge' => 120.00,
            'status' => 'active'
        ]);
        \App\Models\LocationRate::create([
            'district_id' => $sylhet->id,
            'area' => 'Zindabazar',
            'charge' => 130.00,
            'status' => 'active'
        ]);
        \App\Models\LocationRate::create([
            'district_id' => $wb->id,
            'area' => 'Kolkata',
            'charge' => 250.00,
            'status' => 'active'
        ]);

        // 8. Seed Coupons
        Coupon::truncate();
        Coupon::create([
            'code' => 'FREEBILL',
            'type' => 'free_shipping',
            'discount_value' => 0.00,
            'min_spend' => 500.00,
            'status' => 'active'
        ]);
        Coupon::create([
            'code' => 'SAVE100',
            'type' => 'fixed',
            'discount_value' => 100.00,
            'min_spend' => 1000.00,
            'status' => 'active'
        ]);
        Coupon::create([
            'code' => 'DISCOUNT20',
            'type' => 'percent',
            'discount_value' => 20.00,
            'min_spend' => 500.00,
            'status' => 'active'
        ]);
    }
}
