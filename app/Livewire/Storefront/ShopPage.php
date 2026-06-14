<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ShopPage extends Component
{
    use WithPagination;

    #[Url(history: true, keep: false)]
    public $search = '';

    #[Url(history: true, keep: false)]
    public $category = '';

    #[Url(history: true, keep: false)]
    public $sort = 'default';

    public $priceMin = 0;
    public $priceMax = 50000;
    public $settings = [];

    public function mount()
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function getCategoriesProperty()
    {
        return Category::where('is_active', true)->whereNull('parent_id')->with('products')->get();
    }

    public function render()
    {
        $query = Product::query()->with(['brand', 'images']);

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->category)) {
            $categoryModel = Category::where('slug', $this->category)->first();
            if ($categoryModel) {
                // Support parent/child categories if needed
                $categoryIds = Category::where('parent_id', $categoryModel->id)
                    ->pluck('id')
                    ->push($categoryModel->id);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        $query->whereBetween('base_price', [$this->priceMin, $this->priceMax]);

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

        return view('livewire.storefront.shop-page', [
            'products' => $products,
            'categories' => $this->categories
        ])->layout('storefront.layouts.app');
    }
}
