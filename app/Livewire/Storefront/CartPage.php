<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Helpers\Cart;
use App\Models\Coupon;
use App\Models\Setting;

class CartPage extends Component
{
    public $cartItems = [];
    public $couponCode = '';
    public $couponError = '';
    public $couponDiscount = 0;
    public $settings = [];

    public function mount()
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
        $this->loadCart();
        $this->calculateDiscount();
    }

    public function loadCart()
    {
        $this->cartItems = Cart::get();
    }

    public function increment($key)
    {
        if (isset($this->cartItems[$key])) {
            $qty = $this->cartItems[$key]['qty'] + 1;
            Cart::update($key, $qty);
            $this->loadCart();
            $this->calculateDiscount();
            $this->dispatch('cart-updated');
        }
    }

    public function decrement($key)
    {
        if (isset($this->cartItems[$key])) {
            $qty = $this->cartItems[$key]['qty'] - 1;
            Cart::update($key, $qty);
            $this->loadCart();
            $this->calculateDiscount();
            $this->dispatch('cart-updated');
        }
    }

    public function remove($key)
    {
        Cart::remove($key);
        $this->loadCart();
        $this->calculateDiscount();
        $this->dispatch('cart-updated');
        session()->flash('message', 'Item removed from cart.');
    }

    public function applyCoupon()
    {
        $this->couponError = '';
        
        if (empty($this->couponCode)) {
            $this->couponError = 'Please enter a coupon code.';
            return;
        }

        $coupon = Coupon::where('code', $this->couponCode)
            ->where('status', 'active')
            ->first();

        if (!$coupon) {
            $this->couponError = 'Invalid coupon code or expired.';
            return;
        }

        // Expiry validation
        if ($coupon->expiry_date && now()->gt($coupon->expiry_date)) {
            $this->couponError = 'This coupon has expired.';
            return;
        }

        // Min spend validation
        $subtotal = Cart::subtotal();
        if ($coupon->min_spend && $subtotal < $coupon->min_spend) {
            $this->couponError = 'Minimum spend of ৳' . number_format($coupon->min_spend) . ' is required.';
            return;
        }

        // Apply coupon to session
        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'discount_value' => (float)$coupon->discount_value
        ]);

        $this->calculateDiscount();
        session()->flash('message', 'Coupon applied successfully!');
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        $this->couponDiscount = 0;
        $this->couponCode = '';
        $this->calculateDiscount();
        session()->flash('message', 'Coupon removed.');
    }

    public function calculateDiscount()
    {
        $subtotal = Cart::subtotal();
        $coupon = session()->get('coupon');

        if ($coupon) {
            if ($coupon['type'] === 'percent') {
                $this->couponDiscount = ($subtotal * $coupon['discount_value']) / 100;
            } elseif ($coupon['type'] === 'free_shipping') {
                $this->couponDiscount = 0;
            } else {
                $this->couponDiscount = min($coupon['discount_value'], $subtotal);
            }
        } else {
            $this->couponDiscount = 0;
        }
    }

    public function render()
    {
        $subtotal = Cart::subtotal();
        $tax = Cart::taxTotal();
        $total = max(0, ($subtotal + $tax) - $this->couponDiscount);

        return view('livewire.storefront.cart-page', [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'appliedCoupon' => session()->get('coupon')
        ])->layout('storefront.layouts.app');
    }
}
