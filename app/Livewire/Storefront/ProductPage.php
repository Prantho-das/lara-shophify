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

    // Direct Checkout Form Fields
    public $customerName = '';
    public $customerPhone = '';
    public $deliveryAddress = '';
    public $shippingZoneId = null;
    public $paymentMethod = 'cod';
    public $shippingZones = [];
    public $shippingCharge = 0;

    public $enableShippingZones = true;
    public $enableLocationShipping = false;
    public $defaultShippingCost = 60;
    
    public $country = '';
    public $district = '';
    public $area = '';

    public $countriesList = [];
    public $districtsList = [];
    public $availableAreas = [];

    public function mount($slug)
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
        $this->product = Product::where('slug', $slug)->with(['variants', 'images', 'brand'])->firstOrFail();
        
        $this->currentImage = $this->product->images->first()?->path ?? '';

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

        // Select first variant as default if exists
        if ($this->product->has_variants && count($this->product->variants) > 0) {
            $this->selectedVariant = $this->product->variants->first()->id;
        }

        // Trigger Facebook ViewContent CAPI & Pixel
        $eventId = 'vc_' . $this->product->id . '_' . time();
        $customData = [
            'content_ids' => [(string)$this->product->id],
            'content_name' => $this->product->name,
            'content_type' => 'product',
            'value' => (float)$this->product->selling_price,
            'currency' => 'BDT',
        ];
        \App\Services\FacebookCapiService::sendEvent('ViewContent', $eventId, $customData);
        $this->dispatch('fb-event', name: 'ViewContent', data: $customData, eventId: $eventId);
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

        // Trigger Facebook AddToCart CAPI & Pixel
        $eventId = 'atc_' . $this->product->id . '_' . time();
        $customData = [
            'content_ids' => [(string)$this->product->id],
            'content_name' => $this->product->name,
            'content_type' => 'product',
            'value' => (float)$this->product->selling_price * $this->qty,
            'currency' => 'BDT',
        ];
        \App\Services\FacebookCapiService::sendEvent('AddToCart', $eventId, $customData);
        $this->dispatch('fb-event', name: 'AddToCart', data: $customData, eventId: $eventId);

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
    }

    public function buyNow()
    {
        if ($this->qty <= 0) return;

        Cart::add($this->product->id, $this->qty, $this->selectedVariant);
        
        $this->dispatch('cart-updated');

        return $this->redirect('/checkout', navigate: true);
    }

    public function placeDirectOrder()
    {
        $rules = [
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'required|string|max:20',
            'deliveryAddress' => 'required|string',
            'paymentMethod' => 'required|string|in:cod,bkash,nagad',
        ];

        if ($this->enableShippingZones && count($this->shippingZones) > 0) {
            $rules['shippingZoneId'] = 'required|exists:shipping_zones,id';
        }

        if ($this->enableLocationShipping) {
            $rules['country'] = 'required|string';
            $rules['district'] = 'required|string';
            $rules['area'] = 'required|string';
        }

        $this->validate($rules);

        $price = $this->selectedPrice;
        $total = ($price * $this->qty) + $this->shippingCharge;

        $order = null;
        \DB::transaction(function () use (&$order, $total) {
            $shippingDetails = "Name: {$this->customerName}\n" .
                               "Phone: {$this->customerPhone}\n";
                               
            if ($this->enableLocationShipping) {
                $shippingDetails .= "Country: {$this->country}\n" .
                                   "District: {$this->district}\n" .
                                   "Area: {$this->area}\n";
            }
            
            $shippingDetails .= "Address: {$this->deliveryAddress}\n" .
                               "Note: Direct Product Order";

            $order = \App\Models\Order::create([
                'user_id' => auth()->id(),
                'status' => 'pending',
                'total_amount' => $total,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'pending',
                'shipping_address' => $shippingDetails,
                'shipping_charge' => $this->shippingCharge
            ]);

            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
                'variant_id' => $this->selectedVariant ? (string)$this->selectedVariant : null,
                'quantity' => $this->qty,
                'unit_price' => $this->selectedPrice,
                'total' => $this->selectedPrice * $this->qty
            ]);
        });

        if ($order) {
            // Trigger Facebook Purchase CAPI & Pixel
            $eventId = 'pur_dir_' . $order->id . '_' . time();
            $customData = [
                'content_ids' => [(string)$this->product->id],
                'contents' => [[
                    'id' => (string)$this->product->id,
                    'quantity' => (int)$this->qty
                ]],
                'content_type' => 'product',
                'value' => (float)$order->total_amount,
                'currency' => 'BDT',
            ];
            $userData = [
                'ph' => $this->customerPhone,
                'fn' => $this->customerName,
                'country' => $this->country ?: 'Bangladesh',
            ];
            
            \App\Services\FacebookCapiService::sendEvent('Purchase', $eventId, $customData, $userData);
            $this->dispatch('fb-event', name: 'Purchase', data: $customData, eventId: $eventId);

            // Flash success and redirect to profile
            session()->flash('message', 'Order placed successfully! Order ID: #' . $order->id);
            return $this->redirect('/profile', navigate: true);
        }
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
