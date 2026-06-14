<?php

namespace App\Livewire\Storefront\Components;

use Livewire\Component;
use App\Models\Menu;
use App\Models\Setting;
use App\Helpers\Cart;
use Livewire\Attributes\On;

class Navbar extends Component
{
    public $settings = [];
    public $menuItems = [];
    public $cartCount = 0;

    public function mount()
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
        $this->cartCount = Cart::count();
        
        $menu = Menu::where('location', 'header')->with(['items.category', 'items.brand'])->first();
        if ($menu) {
            $this->menuItems = $menu->items;
        }
    }

    #[On('cart-updated')]
    public function updateCartCount()
    {
        $this->cartCount = Cart::count();
    }

    public function render()
    {
        return view('livewire.storefront.components.navbar');
    }
}
