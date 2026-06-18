<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Product;

use Livewire\Attributes\Computed;

class HomePage extends Component
{
    public $sections = [];
    public $settings = [];

    public function mount()
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
        $this->sections = json_decode($this->settings['homepage_sections'] ?? '[]', true);
    }

    public function getCategories($categoryIds = [])
    {
        $query = Category::where('is_active', true);
        if (!empty($categoryIds)) {
            $query->whereIn('id', $categoryIds);
        } else {
            $query->whereNull('parent_id');
        }
        return $query->with(['products'])
            ->orderBy('sort_order')
            ->limit(6)
            ->get();
    }

    public function getProducts($productIds = [], $limit = 8)
    {
        $query = Product::with(['brand', 'images'])->where('status', 'active');
        if (!empty($productIds)) {
            $query->whereIn('id', $productIds);
        } else {
            $query->orderBy('id', 'desc')->limit($limit);
        }
        return $query->get();
    }

    public function getBrands($brandIds = [])
    {
        $query = \App\Models\Brand::where('is_active', true);
        if (!empty($brandIds)) {
            $query->whereIn('id', $brandIds);
        }
        return $query->get();
    }

    public function getProduct($productId)
    {
        return Product::with(['brand', 'images'])->where('status', 'active')->find($productId);
    }

    public function render()
    {
        return view('livewire.storefront.home-page')
            ->layout('storefront.layouts.app');
    }
}
