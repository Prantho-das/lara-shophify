<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Helpers\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;

class CheckoutPage extends Component
{
    public $cartItems = [];
    public $customerName = '';
    public $customerEmail = '';
    public $customerPhone = '';
    public $deliveryAddress = '';
    public $notes = '';
    public $paymentMethod = 'cod';
    public $settings = [];
    
    public $enableShippingZones = true;
    public $enableLocationShipping = false;
    public $defaultShippingCost = 60;

    public $country = '';
    public $district = '';
    public $area = '';

    public $countriesList = [];
    public $districtsList = [];
    public $availableAreas = [];

    public $shippingZones = [];
    public $shippingZoneId = null;
    public $shippingCharge = 0;

    public $subtotal = 0;
    public $tax = 0;
    public $couponDiscount = 0;
    public $total = 0;
    
    public $orderPlaced = false;
    public $placedOrderId = null;

    public function mount()
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
        $this->cartItems = Cart::get();

        if (count($this->cartItems) === 0) {
            return $this->redirect('/cart', navigate: true);
        }

        $allowGuest = filter_var($this->settings['allow_guest_checkout'] ?? true, FILTER_VALIDATE_BOOLEAN);
        if (!$allowGuest && !auth()->check()) {
            session()->put('url.intended', '/checkout');
            return $this->redirect('/login', navigate: true);
        }

        // Load dynamic shipping config
        $this->enableShippingZones = filter_var($this->settings['enable_shipping_zones'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->enableLocationShipping = filter_var($this->settings['enable_location_shipping'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->defaultShippingCost = (float)($this->settings['default_shipping_cost'] ?? 60);

        // Set default shipping charge fallback
        $this->shippingCharge = $this->defaultShippingCost;

        if ($this->enableShippingZones) {
            $this->shippingZones = \App\Models\ShippingZone::where('status', 'active')->get()->toArray();
            if (count($this->shippingZones) > 0) {
                $this->shippingZoneId = $this->shippingZones[0]['id'];
                $this->shippingCharge = (float)$this->shippingZones[0]['cost'];
            }
        }

        // Load active countries and default
        $this->countriesList = \App\Models\Country::where('is_active', true)->pluck('name', 'name')->toArray();
        if (isset($this->countriesList['Bangladesh'])) {
            $this->country = 'Bangladesh';
        } else {
            $this->country = array_key_first($this->countriesList) ?? '';
        }

        if ($this->country) {
            $countryObj = \App\Models\Country::where('name', $this->country)->first();
            if ($countryObj) {
                $this->districtsList = \App\Models\District::where('country_id', $countryObj->id)->pluck('name', 'name')->toArray();
            }
        }

        $this->calculateTotals();

        // Trigger Facebook InitiateCheckout CAPI & Pixel
        $eventId = 'ic_' . time();
        $contentIds = [];
        $contents = [];
        foreach ($this->cartItems as $item) {
            $contentIds[] = (string)$item['id'];
            $contents[] = [
                'id' => (string)$item['id'],
                'quantity' => (int)$item['qty']
            ];
        }
        $customData = [
            'content_ids' => $contentIds,
            'contents' => $contents,
            'content_type' => 'product',
            'value' => (float)$this->total,
            'currency' => 'BDT',
        ];
        \App\Services\FacebookCapiService::sendEvent('InitiateCheckout', $eventId, $customData);
        $this->dispatch('fb-event', name: 'InitiateCheckout', data: $customData, eventId: $eventId);
    }

    public function updatedShippingZoneId($value)
    {
        if ($this->enableShippingZones) {
            $zone = \App\Models\ShippingZone::find($value);
            if ($zone) {
                $this->shippingCharge = (float)$zone->cost;
            } else {
                $this->shippingCharge = $this->defaultShippingCost;
            }
        }
        $this->calculateTotals();
    }

    public function updatedCountry($value)
    {
        $this->district = '';
        $this->area = '';
        $this->availableAreas = [];
        $this->shippingCharge = $this->defaultShippingCost;

        $countryObj = \App\Models\Country::where('name', $value)->first();
        if ($countryObj) {
            $this->districtsList = \App\Models\District::where('country_id', $countryObj->id)->pluck('name', 'name')->toArray();
        } else {
            $this->districtsList = [];
        }
        $this->calculateTotals();
    }

    public function updatedDistrict($value)
    {
        $this->area = '';
        $this->availableAreas = [];
        $this->shippingCharge = $this->defaultShippingCost;

        $countryObj = \App\Models\Country::where('name', $this->country)->first();
        if ($countryObj && !empty($value)) {
            $districtObj = \App\Models\District::where('name', $value)->where('country_id', $countryObj->id)->first();
            if ($districtObj) {
                $this->availableAreas = \App\Models\LocationRate::where('district_id', $districtObj->id)
                    ->where('status', 'active')
                    ->pluck('area')
                    ->toArray();

                // Default shipping charge to first area's charge if exists
                $defaultRate = \App\Models\LocationRate::where('district_id', $districtObj->id)
                    ->where('status', 'active')
                    ->first();
                if ($defaultRate) {
                    $this->shippingCharge = (float)$defaultRate->charge;
                }
            }
        }
        $this->calculateTotals();
    }

    public function updatedArea($value)
    {
        if ($this->enableLocationShipping && !empty($this->district) && !empty($value)) {
            $countryObj = \App\Models\Country::where('name', $this->country)->first();
            if ($countryObj) {
                $districtObj = \App\Models\District::where('name', $this->district)->where('country_id', $countryObj->id)->first();
                if ($districtObj) {
                    $rate = \App\Models\LocationRate::where('district_id', $districtObj->id)
                        ->where('area', $value)
                        ->where('status', 'active')
                        ->first();
                    if ($rate) {
                        $this->shippingCharge = (float)$rate->charge;
                    } else {
                        $this->shippingCharge = $this->defaultShippingCost;
                    }
                }
            }
        }
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = Cart::subtotal();
        $this->tax = Cart::taxTotal();
        
        $coupon = session()->get('coupon');
        if ($coupon) {
            if ($coupon['type'] === 'percent') {
                $this->couponDiscount = ($this->subtotal * $coupon['discount_value']) / 100;
            } elseif ($coupon['type'] === 'free_shipping') {
                $this->couponDiscount = 0;
                $this->shippingCharge = 0;
            } else {
                $this->couponDiscount = min($coupon['discount_value'], $this->subtotal);
            }
        } else {
            $this->couponDiscount = 0;
        }

        $this->total = max(0, ($this->subtotal + $this->tax + $this->shippingCharge) - $this->couponDiscount);
    }

    public function placeOrder()
    {
        $rules = [
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'required|string|max:20',
            'deliveryAddress' => 'required|string',
            'paymentMethod' => 'required|string|in:cod,bkash,nagad',
        ];

        if ($this->enableShippingZones) {
            $rules['shippingZoneId'] = 'required|exists:shipping_zones,id';
        }

        if ($this->enableLocationShipping) {
            $rules['country'] = 'required|string';
            $rules['district'] = 'required|string';
            $rules['area'] = 'required|string';
        }

        $this->validate($rules);

        $order = null;

        \DB::transaction(function () use (&$order) {
            $shippingDetails = "Name: {$this->customerName}\n" .
                               "Phone: {$this->customerPhone}\n" .
                               "Email: {$this->customerEmail}\n";
                               
            if ($this->enableLocationShipping) {
                $shippingDetails .= "Country: {$this->country}\n" .
                                   "District: {$this->district}\n" .
                                   "Area: {$this->area}\n";
            }
            
            $shippingDetails .= "Address: {$this->deliveryAddress}\n" .
                               "Notes: {$this->notes}";

            $order = Order::create([
                'user_id' => auth()->id(),
                'status' => 'pending',
                'total_amount' => $this->total,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'pending',
                'shipping_address' => $shippingDetails,
                'shipping_charge' => $this->shippingCharge
            ]);

            foreach ($this->cartItems as $key => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'variant_id' => $item['variant_id'] ? (string)$item['variant_id'] : null,
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'total' => $item['price'] * $item['qty']
                ]);
            }
        });

        if ($order) {
            $this->placedOrderId = $order->id;
            $this->orderPlaced = true;

            // Trigger Facebook Purchase CAPI & Pixel
            $eventId = 'pur_' . $order->id . '_' . time();
            $contentIds = [];
            $contents = [];
            foreach ($this->cartItems as $item) {
                $contentIds[] = (string)$item['id'];
                $contents[] = [
                    'id' => (string)$item['id'],
                    'quantity' => (int)$item['qty']
                ];
            }
            $customData = [
                'content_ids' => $contentIds,
                'contents' => $contents,
                'content_type' => 'product',
                'value' => (float)$order->total_amount,
                'currency' => 'BDT',
            ];
            $userData = [
                'em' => $this->customerEmail ?: null,
                'ph' => $this->customerPhone,
                'fn' => $this->customerName,
                'ct' => $this->district ?: null,
                'country' => $this->country ?: 'Bangladesh',
            ];
            
            \App\Services\FacebookCapiService::sendEvent('Purchase', $eventId, $customData, $userData);
            $this->dispatch('fb-event', name: 'Purchase', data: $customData, eventId: $eventId);
            
            Cart::clear();
            session()->forget('coupon');
            $this->dispatch('cart-updated');
        }
    }

    public function render()
    {
        return view('livewire.storefront.checkout-page')
            ->layout('storefront.layouts.app');
    }
}
