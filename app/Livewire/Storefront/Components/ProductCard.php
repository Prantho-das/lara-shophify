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

        // Trigger Facebook AddToCart CAPI & Pixel
        $eventId = 'atc_' . $this->product['id'] . '_' . time();
        $customData = [
            'content_ids' => [(string)$this->product['id']],
            'content_name' => $this->product['name'],
            'content_type' => 'product',
            'value' => (float)$this->product['selling_price'],
            'currency' => 'BDT',
        ];
        \App\Services\FacebookCapiService::sendEvent('AddToCart', $eventId, $customData);
        $this->dispatch('fb-event', name: 'AddToCart', data: $customData, eventId: $eventId);

        session()->flash('message', 'Added to cart successfully!');
    }

    public function render()
    {
        return view('livewire.storefront.components.product-card');
    }
}
