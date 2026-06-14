<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Helpers\Cart;
use App\Models\Setting;

class ProductPage extends Component
{
    public $product;
    public $selectedVariant = null;
    public $qty = 1;
    public $currentImage = '';
    public $settings = [];

    public function mount($slug)
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
        $this->product = Product::where('slug', $slug)->with(['variants', 'images', 'brand'])->firstOrFail();
        
        $this->currentImage = $this->product->images->first()?->path ?? '';

        // Select first variant as default if exists
        if ($this->product->has_variants && count($this->product->variants) > 0) {
            $this->selectedVariant = $this->product->variants->first()->id;
        }
    }

    public function selectVariant($variantId)
    {
        $this->selectedVariant = $variantId;
    }

    public function addToCart()
    {
        if ($this->qty <= 0) return;

        Cart::add($this->product->id, $this->qty, $this->selectedVariant);
        
        $this->dispatch('cart-updated');

        session()->flash('message', 'Product added to cart successfully!');
    }

    public function getSelectedPriceProperty()
    {
        if ($this->selectedVariant) {
            $variant = ProductVariant::find($this->selectedVariant);
            if ($variant) {
                return (float)$variant->selling_price;
            }
        }
        return (float)$this->product->selling_price;
    }

    public function getSelectedBarcodeProperty()
    {
        if ($this->selectedVariant) {
            $variant = ProductVariant::find($this->selectedVariant);
            if ($variant) {
                return $variant->barcode ?: $this->product->barcode;
            }
        }
        return $this->product->barcode;
    }

    public function getRelatedProductsProperty()
    {
        return Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->limit(4)
            ->get();
    }

    public function render()
    {
        return view('livewire.storefront.product-page', [
            'price' => $this->selectedPrice,
            'barcode' => $this->selectedBarcode,
            'relatedProducts' => $this->relatedProducts
        ])->layout('storefront.layouts.app');
    }
}
