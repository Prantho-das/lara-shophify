<?php

namespace App\Livewire\Storefront\Components;

use Livewire\Component;
use App\Models\Product;
use App\Helpers\Cart;

class ProductCard extends Component
{
    public $product;

    public function mount($product)
    {
        $this->product = $product;
    }

    public function addToCart()
    {
        // Add default product to cart
        Cart::add($this->product['id'], 1);
        
        $this->dispatch('cart-updated');

        session()->flash('message', 'Added to cart successfully!');
    }

    public function render()
    {
        return view('livewire.storefront.components.product-card');
    }
}
