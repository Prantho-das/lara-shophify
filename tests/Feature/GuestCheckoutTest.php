<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Setting;
use App\Models\Product;
use App\Helpers\Cart;
use Livewire\Livewire;
use App\Livewire\Storefront\CheckoutPage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed a sample product for cart
        $this->product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'base_price' => 100,
            'status' => 'active',
        ]);
    }

    public function test_guest_cannot_checkout_when_guest_checkout_is_disabled(): void
    {
        // 1. Disable guest checkout setting
        Setting::updateOrCreate(['key' => 'allow_guest_checkout'], ['value' => '0']);

        // 2. Put item in cart
        Cart::add($this->product->id, 1);

        // 3. Test checkout page redirects guest to login
        Livewire::test(CheckoutPage::class)
            ->assertRedirect('/login');
    }

    public function test_guest_can_checkout_when_guest_checkout_is_enabled(): void
    {
        // 1. Enable guest checkout setting
        Setting::updateOrCreate(['key' => 'allow_guest_checkout'], ['value' => '1']);

        // 2. Put item in cart
        Cart::add($this->product->id, 1);

        // 3. Test checkout page does NOT redirect guest
        Livewire::test(CheckoutPage::class)
            ->assertOk();
    }

    public function test_guest_can_place_order_successfully(): void
    {
        // 1. Enable guest checkout setting
        Setting::updateOrCreate(['key' => 'allow_guest_checkout'], ['value' => '1']);
        
        // Disable shipping zones and location shipping to simplify rules
        Setting::updateOrCreate(['key' => 'enable_shipping_zones'], ['value' => '0']);
        Setting::updateOrCreate(['key' => 'enable_location_shipping'], ['value' => '0']);

        // 2. Put item in cart
        Cart::add($this->product->id, 1);

        // 3. Place order via Livewire
        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('customerPhone', '01700000000')
            ->set('customerEmail', 'john@example.com')
            ->set('deliveryAddress', 'Dhaka, Bangladesh')
            ->set('paymentMethod', 'cod')
            ->call('placeOrder')
            ->assertHasNoErrors();

        // 4. Assert order is created with user_id = null
        $this->assertDatabaseHas('orders', [
            'user_id' => null,
            'total_amount' => 160,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
        ]);
    }
}
