<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Brand;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class CategoryPage extends Component
{
    use WithPagination;

    public $category;
    public $settings = [];

    #[Url(history: true, keep: false)]
    public $sort = 'default';

    #[Url(history: true, keep: false)]
    public $brand = '';

    public $priceMin = 0;
    public $priceMax = 50000;

    public function mount($slug)
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
        $this->category = Category::where('slug', $slug)->firstOrFail();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function updatingBrand()
    {
        $this->resetPage();
    }

    public function updatingPriceMin()
    {
        $this->resetPage();
    }

    public function updatingPriceMax()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Support parent-child categories hierarchy
        $categoryIds = Category::where('parent_id', $this->category->id)
            ->pluck('id')
            ->push($this->category->id);

        // Fetch brands that have products in this category hierarchy
        $availableBrands = Brand::whereHas('products', function($q) use ($categoryIds) {
            $q->whereIn('category_id', $categoryIds);
        })->get();

        $query = Product::whereIn('category_id', $categoryIds)
            ->with(['brand', 'images']);

        // Apply Brand Filter
        if (!empty($this->brand)) {
            $query->whereHas('brand', function($q) {
                $q->where('slug', $this->brand);
            });
        }

        // Apply Price Filter based on base_price (just like Shop page)
        $query->whereBetween('base_price', [$this->priceMin, $this->priceMax]);

        // Apply Sorting
        switch ($this->sort) {
            case 'price_low_high':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('base_price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $products = $query->paginate(12);

        return view('livewire.storefront.category-page', [
            'products' => $products,
            'availableBrands' => $availableBrands
        ])->layout('storefront.layouts.app');
    }
}
