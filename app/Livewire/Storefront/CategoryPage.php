<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Livewire\WithPagination;

class CategoryPage extends Component
{
    use WithPagination;

    public $category;
    public $settings = [];

    public function mount($slug)
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
        $this->category = Category::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        // Support parent-child categories hierarchy
        $categoryIds = Category::where('parent_id', $this->category->id)
            ->pluck('id')
            ->push($this->category->id);

        $products = Product::whereIn('category_id', $categoryIds)
            ->with(['brand', 'images'])
            ->paginate(12);

        return view('livewire.storefront.category-page', [
            'products' => $products
        ])->layout('storefront.layouts.app');
    }
}
