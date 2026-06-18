<?php

use Illuminate\Support\Facades\Route;

// Customer Storefront Routes
Route::get('/', \App\Livewire\Storefront\HomePage::class)->name('storefront.home');
Route::get('/shop', \App\Livewire\Storefront\ShopPage::class)->name('storefront.shop');
Route::get('/product/{slug}', \App\Livewire\Storefront\ProductPage::class)->name('storefront.product');
Route::get('/category/{slug}', \App\Livewire\Storefront\CategoryPage::class)->name('storefront.category');
Route::get('/cart', \App\Livewire\Storefront\CartPage::class)->name('storefront.cart');
Route::get('/checkout', \App\Livewire\Storefront\CheckoutPage::class)->name('storefront.checkout');
Route::get('/page/{slug}', \App\Livewire\Storefront\CmsPage::class)->name('storefront.page');

// XML Product Feed for Facebook Catalogue
Route::get('/feed/facebook-catalog.xml', [\App\Http\Controllers\FeedController::class, 'facebookCatalog'])->name('feed.facebook');
Route::get('/sitemap.xml', [\App\Http\Controllers\FeedController::class, 'sitemap'])->name('feed.sitemap');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('profile', \App\Livewire\Storefront\ProfilePage::class)
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
