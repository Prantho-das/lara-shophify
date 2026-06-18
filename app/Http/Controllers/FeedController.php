<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    /**
     * Generates a compliant XML product catalog feed for Facebook Catalog.
     *
     * @return Response
     */
    public function facebookCatalog()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $storeName = $settings['store_name'] ?? 'E-Commerce Store';
        $storeUrl = url('/');

        // Fetch active products with relations
        $products = Product::with(['brand', 'images', 'category'])
            ->where('status', 'active')
            ->get();

        $content = view('feed.facebook', compact('products', 'storeName', 'storeUrl'));

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8'
        ]);
    }

    /**
     * Generates a standard XML sitemap for SEO indexers.
     *
     * @return Response
     */
    public function sitemap()
    {
        $urls = [];

        // Core Pages
        $urls[] = ['loc' => url('/'), 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '1.0'];
        $urls[] = ['loc' => url('/shop'), 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '0.8'];
        $urls[] = ['loc' => url('/cart'), 'lastmod' => date('Y-m-d'), 'changefreq' => 'weekly', 'priority' => '0.5'];
        $urls[] = ['loc' => url('/checkout'), 'lastmod' => date('Y-m-d'), 'changefreq' => 'weekly', 'priority' => '0.5'];

        // Store Policies
        $urls[] = ['loc' => url('/page/privacy-policy'), 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.3'];
        $urls[] = ['loc' => url('/page/terms-of-service'), 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.3'];
        $urls[] = ['loc' => url('/page/refund-policy'), 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.3'];

        // Dynamic Active Products
        $products = \App\Models\Product::where('status', 'active')->get();
        foreach ($products as $product) {
            $urls[] = [
                'loc' => route('storefront.product', $product->slug),
                'lastmod' => $product->updated_at ? $product->updated_at->format('Y-m-d') : date('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
        }

        // Active Categories
        $categories = \App\Models\Category::where('is_active', true)->get();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => url('/category/' . $category->slug),
                'lastmod' => $category->updated_at ? $category->updated_at->format('Y-m-d') : date('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.6'
            ];
        }

        $content = view('feed.sitemap', compact('urls'));

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8'
        ]);
    }
}
